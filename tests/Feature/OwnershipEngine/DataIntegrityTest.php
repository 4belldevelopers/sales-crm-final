<?php

namespace Tests\Feature\OwnershipEngine;

use App\Services\OwnershipEngine\Models\OwnershipCluster;
use App\Services\OwnershipEngine\OwnershipEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Phase 6: directly tests the quality requirements restated this turn — deterministic
 * ownership, idempotency, transaction safety, and event immutability — as distinct
 * properties, not just as a side effect of the Master Tracker scenario tests (Phase 2) and
 * backfill tests (Phase 4). Requires a real MySQL 8 connection (see SchemaTest).
 *
 * NOT covered here (documented limitation, not silently skipped): true multi-connection
 * concurrency (two overlapping transactions racing for the same cluster lock) needs a real
 * concurrent-execution harness — two DB connections/processes — which this environment
 * cannot provide. §8/§23 of docs/OWNERSHIP_ENGINE_DESIGN.md give the structural argument
 * (single-cluster-lock transactions, deadlock-free by construction); this file tests what's
 * actually executable in a single-process PHPUnit run.
 */
class DataIntegrityTest extends TestCase
{
    use RefreshDatabase;

    private function makeCompany(int $appDuplicateMode = 1): int
    {
        return (int) DB::table('companies')->insertGetId([
            'company_name' => 'Integrity Test Co '.uniqid(),
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
            'title' => 'Integrity Test Job',
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

    private function makeRow(int $jobId, int $companyId, int $thirdpartyCompanyId, string $email): int
    {
        $jobApplicationId = (int) DB::table('job_applications')->insertGetId([
            'submitted_by' => 1,
            'email' => $email,
            'phone' => '9990000000',
            'created_at' => now(),
        ]);

        return (int) DB::table('add_job_wise_condidates')->insertGetId([
            'job_id' => $jobId,
            'job_application_id' => $jobApplicationId,
            'company_id' => $companyId,
            'thirdparty_company_id' => $thirdpartyCompanyId,
            'master_status_id' => 8,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function test_idempotency_calling_attemptCompanyReservation_twice_writes_one_event(): void
    {
        $company = $this->makeCompany();
        $job = $this->makeJob($company);
        $row = $this->makeRow($job, $company, 0, 'integrity1@example.test');

        $first = app(OwnershipEngine::class)->attemptCompanyReservation($row);
        $second = app(OwnershipEngine::class)->attemptCompanyReservation($row);

        $this->assertSame(1, $first->state);
        $this->assertSame(1, $second->state); // still reports OWNER, not an error
        $this->assertSame(1, DB::table('ownership_events')->where('subject_row_id', $row)->count());
        $this->assertSame(1, DB::table('ownership_clusters')->count());
    }

    public function test_event_immutability_prior_events_are_never_modified_by_later_activity(): void
    {
        $company = $this->makeCompany();
        $job = $this->makeJob($company);
        $cr = $this->makeRow($job, $company, 0, 'integrity2@example.test');

        app(OwnershipEngine::class)->attemptCompanyReservation($cr);

        $firstEvent = DB::table('ownership_events')->where('subject_row_id', $cr)->first();
        $snapshot = (array) $firstEvent;

        // Cause further activity on the SAME cluster (a losing agency contender) — must not
        // touch the first event's row at all, only append a new one.
        $agency = $this->makeRow($job, $company, 9001, 'integrity2@example.test');
        app(OwnershipEngine::class)->claimForAgencySourcing($agency);

        $afterEvent = (array) DB::table('ownership_events')->where('id', $firstEvent->id)->first();

        $this->assertSame($snapshot, $afterEvent);
        $this->assertSame(2, DB::table('ownership_events')->count()); // appended, not mutated
    }

    public function test_no_partial_write_when_a_guard_exception_is_thrown_before_any_write(): void
    {
        $company = $this->makeCompany();
        $job = $this->makeJob($company);
        // An agency row (thirdparty_company_id != 0) passed to attemptCompanyReservation()
        // must throw before touching any table.
        $agency = $this->makeRow($job, $company, 9001, 'integrity3@example.test');

        try {
            app(OwnershipEngine::class)->attemptCompanyReservation($agency);
            $this->fail('Expected a LogicException.');
        } catch (\LogicException $e) {
            // expected
        }

        $this->assertSame(0, DB::table('ownership_events')->count());
        $this->assertSame(0, DB::table('ownership_clusters')->count());
        $this->assertNull(DB::table('add_job_wise_condidates')->where('id', $agency)->value('ownership_state'));
    }

    public function test_deterministic_ownership_same_inputs_produce_same_outputs_across_two_independent_clusters(): void
    {
        // Two structurally identical scenarios (S9-shape: company reserves, agency sources
        // later) built independently, in different companies, must reach the same verdict —
        // proving the outcome depends only on the rule logic and relative timestamps, not on
        // any incidental process/request state.
        $companyA = $this->makeCompany();
        $jobA = $this->makeJob($companyA);
        $crA = $this->makeRow($jobA, $companyA, 0, 'integrity4a@example.test');
        app(OwnershipEngine::class)->attemptCompanyReservation($crA);
        $agencyA = $this->makeRow($jobA, $companyA, 9001, 'integrity4a@example.test');
        $resultA = app(OwnershipEngine::class)->claimForAgencySourcing($agencyA);

        $companyB = $this->makeCompany();
        $jobB = $this->makeJob($companyB);
        $crB = $this->makeRow($jobB, $companyB, 0, 'integrity4b@example.test');
        app(OwnershipEngine::class)->attemptCompanyReservation($crB);
        $agencyB = $this->makeRow($jobB, $companyB, 9001, 'integrity4b@example.test');
        $resultB = app(OwnershipEngine::class)->claimForAgencySourcing($agencyB);

        $this->assertSame($resultA->state, $resultB->state);
        $this->assertSame($resultA->decisionRule, $resultB->decisionRule);
        $this->assertSame(2, $resultA->state); // agency is the duplicate in both (ER5/ER4)
    }

    public function test_transaction_safety_lock_or_create_cluster_recovers_when_a_matching_cluster_already_exists(): void
    {
        // Simulates the "another transaction already created this cluster" branch
        // (lockOrCreateCluster()'s catch path) without needing true concurrent execution:
        // pre-create the cluster row exactly as a racing transaction would have, then confirm
        // the engine finds and locks it rather than erroring or creating a duplicate.
        $company = $this->makeCompany();
        $job = $this->makeJob($company);
        $row = $this->makeRow($job, $company, 0, 'integrity5@example.test');

        OwnershipCluster::create([
            'scoping_company_id' => $company,
            'scope_mode' => 1,
            'scope_key' => $job,
            'normalized_email' => 'integrity5@example.test',
            'normalized_phone' => null,
        ]);

        $this->assertSame(1, DB::table('ownership_clusters')->count());

        $result = app(OwnershipEngine::class)->attemptCompanyReservation($row);

        $this->assertSame(1, $result->state);
        $this->assertSame(1, DB::table('ownership_clusters')->count()); // reused, not duplicated
    }
}
