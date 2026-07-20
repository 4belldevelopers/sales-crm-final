<?php

namespace Tests\Feature\OwnershipEngine;

use App\Services\OwnershipEngine\OwnershipEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Phase 4: backfillCandidate() must reconstruct historical ownership purely from raw data
 * (add_job_wise_condidates.created_at + timeline) for rows that never had a live trigger
 * fire, produce the exact same verdicts the live engine would have, and be safely
 * idempotent/re-runnable. Requires a real MySQL 8 connection (see SchemaTest).
 */
class BackfillTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Reference data the engine depends on (master_status.id = 1 = "Sourcing") — not
        // provided by migrations, only by a seeder that may not run in this environment.
        DB::table('master_status')->insertOrIgnore([
            'id' => 1,
            'name' => 'Sourcing',
            'css_style' => 'x',
            'text_name' => 'Sourcing',
            'divname' => 'x',
            'direct_client_priority' => 1,
            'custom_client' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    private function makeCompany(int $appDuplicateMode = 1): int
    {
        return (int) DB::table('companies')->insertGetId([
            'company_name' => 'Backfill Test Co '.uniqid(),
            'company_email' => uniqid().'@example.test',
            'locale' => 'en',
            'app_duplicate' => $appDuplicateMode,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function makeJob(int $companyId): int
    {
        return (int) DB::table('jobs')->insertGetId([
            'title' => 'Backfill Test Job',
            'company_id' => $companyId,
            'job_description' => 'x',
            'job_requirement' => 'x',
            'total_positions' => 1,
            'start_date' => now(),
            'end_date' => now()->addMonth(),
            'required_columns' => '',
            'meta_details' => '',
            'section_visibility' => '',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /** Creates a historical row with a real created_at in the past — no engine call made. */
    private function makeHistoricalRow(int $jobId, int $companyId, int $thirdpartyCompanyId, string $email, Carbon $createdAt): int
    {
        $jobApplicationId = (int) DB::table('job_applications')->insertGetId([
            'submitted_by' => 1,
            'email' => $email,
            'phone' => '9990000000',
            'created_at' => $createdAt,
        ]);

        return (int) DB::table('add_job_wise_condidates')->insertGetId([
            'job_id' => $jobId,
            'job_application_id' => $jobApplicationId,
            'company_id' => $companyId,
            'thirdparty_company_id' => $thirdpartyCompanyId,
            'master_status_id' => $thirdpartyCompanyId === 0 ? 8 : 1,
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ]);
    }

    /** Historical timeline entry recording when a row first reached Sourcing. */
    private function makeHistoricalSourcingEvent(int $rowId, Carbon $at): void
    {
        $row = DB::table('add_job_wise_condidates')->where('id', $rowId)->first();
        $sourcingLabel = DB::table('master_status')->where('id', 1)->value('name');

        DB::table('timeline')->insert([
            'owner_id' => 1,
            'application_id' => $row->job_application_id,
            'application_email' => 'x@example.test',
            'job_id' => $row->job_id,
            'process_id' => 1,
            'process_child_name' => 'x',
            'note' => 'backfill test fixture',
            'comment' => 'backfill test fixture',
            'master_process_name' => $sourcingLabel,
            'create_at' => $at,
            'update_at' => $at,
        ]);
    }

    private function cachedState(int $rowId): ?int
    {
        $state = DB::table('add_job_wise_condidates')->where('id', $rowId)->value('ownership_state');

        return $state === null ? null : (int) $state;
    }

    public function test_historical_company_row_gets_reserved_on_backfill(): void
    {
        $company = $this->makeCompany();
        $job = $this->makeJob($company);
        $cr = $this->makeHistoricalRow($job, $company, 0, 'bf1@example.test', now()->subDays(30));

        $results = app(OwnershipEngine::class)->backfillCandidate($cr);

        $this->assertCount(1, $results);
        $this->assertSame(1, $this->cachedState($cr));
        $this->assertSame(1, DB::table('ownership_events')->where('subject_row_id', $cr)->where('trigger_source', 'manual_rebuild')->count());
    }

    public function test_historical_agency_row_that_never_sourced_stays_undetermined(): void
    {
        $company = $this->makeCompany();
        $job = $this->makeJob($company);
        $a = $this->makeHistoricalRow($job, $company, 9001, 'bf2@example.test', now()->subDays(30));
        // Never reached Sourcing historically — no timeline entry created.

        $results = app(OwnershipEngine::class)->backfillCandidate($a);

        $this->assertCount(0, $results);
        $this->assertNull($this->cachedState($a));
    }

    public function test_historical_mixed_cluster_reconstructs_correct_chronological_winner(): void
    {
        // Mirrors S15: agency sourced first historically, company row created later —
        // agency must win even though backfillCandidate() is called on the company row.
        $company = $this->makeCompany();
        $job = $this->makeJob($company);

        $a = $this->makeHistoricalRow($job, $company, 9001, 'bf3@example.test', now()->subDays(30));
        $this->makeHistoricalSourcingEvent($a, now()->subDays(29));

        $cr = $this->makeHistoricalRow($job, $company, 0, 'bf3@example.test', now()->subDays(10));

        $results = app(OwnershipEngine::class)->backfillCandidate($cr);

        $this->assertCount(2, $results); // both cluster members got an event
        $this->assertSame(1, $this->cachedState($a));  // agency owns (ER3)
        $this->assertSame(2, $this->cachedState($cr)); // company is duplicate (ER5)
    }

    public function test_row_already_live_processed_is_skipped_by_backfill(): void
    {
        $company = $this->makeCompany();
        $job = $this->makeJob($company);
        $cr = $this->makeHistoricalRow($job, $company, 0, 'bf4@example.test', now()->subDays(30));

        // Simulate Phase 3 having already processed this row live.
        app(OwnershipEngine::class)->attemptCompanyReservation($cr);
        $this->assertSame(1, $this->cachedState($cr));

        $results = app(OwnershipEngine::class)->backfillCandidate($cr);

        $this->assertCount(0, $results); // nothing to do — already covered
        $this->assertSame(1, $this->cachedState($cr)); // unchanged
        $this->assertSame(1, DB::table('ownership_events')->where('subject_row_id', $cr)->count()); // still exactly one event
    }

    public function test_backfill_is_idempotent_when_run_twice(): void
    {
        $company = $this->makeCompany();
        $job = $this->makeJob($company);
        $cr = $this->makeHistoricalRow($job, $company, 0, 'bf5@example.test', now()->subDays(30));

        $first = app(OwnershipEngine::class)->backfillCandidate($cr);
        $second = app(OwnershipEngine::class)->backfillCandidate($cr);

        $this->assertCount(1, $first);
        $this->assertCount(0, $second); // already covered by the first call
        $this->assertSame(1, DB::table('ownership_events')->where('subject_row_id', $cr)->count());
    }

    public function test_app_duplicate_disabled_company_is_never_backfilled(): void
    {
        $company = $this->makeCompany(appDuplicateMode: 0);
        $job = $this->makeJob($company);
        $cr = $this->makeHistoricalRow($job, $company, 0, 'bf6@example.test', now()->subDays(30));

        $results = app(OwnershipEngine::class)->backfillCandidate($cr);

        $this->assertCount(0, $results);
        $this->assertNull($this->cachedState($cr));
        $this->assertSame(0, DB::table('ownership_events')->where('subject_row_id', $cr)->count());
    }
}
