<?php

namespace Tests\Feature\OwnershipEngine;

use App\Services\OwnershipEngine\OwnershipEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Phase 8: live processing is unconditional (kill switch aside) and the authoritative flag
 * hierarchy (currently inert, reserved for the future cutover mechanism) per Finding 7 —
 * supersedes Finding 6's original (wrong) design where the hierarchy gated processing itself.
 * Also covers the rebuild/verify/orphan-detection family (Finding 4/6, unaffected by
 * Finding 7). Requires a real MySQL 8 connection (see SchemaTest).
 */
class ActivationReadinessTest extends TestCase
{
    use RefreshDatabase;

    private function makeCompany(int $appDuplicateMode = 1): int
    {
        return (int) DB::table('companies')->insertGetId([
            'company_name' => 'Activation Test Co '.uniqid(),
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
            'title' => 'Activation Test Job',
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

    private function cachedState(int $rowId): ?int
    {
        $state = DB::table('add_job_wise_condidates')->where('id', $rowId)->value('ownership_state');

        return $state === null ? null : (int) $state;
    }

    // ---- Live processing vs. the authoritative flag hierarchy (Finding 7) -----------

    public function test_processing_happens_unconditionally_regardless_of_the_flag_hierarchy(): void
    {
        // Finding 7: the flag hierarchy defaults to "not authoritative" (false), but that
        // must NOT stop live processing — the engine keeps building history regardless.
        $company = $this->makeCompany();
        $job = $this->makeJob($company);
        $row = $this->makeRow($job, $company, 0, 'flag1@example.test');

        $result = app(OwnershipEngine::class)->attemptCompanyReservation($row);

        $this->assertSame(1, $result->state);
        $this->assertSame(1, DB::table('ownership_events')->count());
    }

    public function test_company_and_job_overrides_have_no_effect_on_live_processing(): void
    {
        $company = $this->makeCompany();
        DB::table('companies')->where('id', $company)->update(['ownership_engine_enabled' => false]);
        $job = $this->makeJob($company);
        DB::table('jobs')->where('id', $job)->update(['ownership_engine_enabled' => false]);
        $row = $this->makeRow($job, $company, 0, 'flag2@example.test');

        // Both overrides explicitly "off" — processing must still happen; only the kill
        // switch (tested below) is allowed to stop it.
        $result = app(OwnershipEngine::class)->attemptCompanyReservation($row);

        $this->assertSame(1, $result->state);
    }

    public function test_kill_switch_stops_live_processing(): void
    {
        DB::table('ownership_engine_settings')->where('id', 1)->update([
            'kill_switch_active' => true,
            'kill_switch_reason' => 'test',
        ]);

        $company = $this->makeCompany();
        $job = $this->makeJob($company);
        $row = $this->makeRow($job, $company, 0, 'flag5@example.test');

        $result = app(OwnershipEngine::class)->attemptCompanyReservation($row);

        $this->assertNull($result->state);
        $this->assertSame(0, DB::table('ownership_events')->count());
    }

    // ---- The authoritative flag hierarchy itself (isAuthoritative(), currently inert) --

    public function test_isAuthoritative_precedence_kill_switch_beats_everything(): void
    {
        DB::table('ownership_engine_settings')->where('id', 1)->update([
            'globally_enabled' => true,
            'kill_switch_active' => true,
        ]);
        $company = $this->makeCompany();
        DB::table('companies')->where('id', $company)->update(['ownership_engine_enabled' => true]);

        $flags = app(\App\Services\OwnershipEngine\Support\FeatureFlags::class);
        $this->assertFalse($flags->isAuthoritative($company));
    }

    public function test_isAuthoritative_precedence_job_beats_company_beats_global(): void
    {
        DB::table('ownership_engine_settings')->where('id', 1)->update(['globally_enabled' => false]);
        $company = $this->makeCompany();
        DB::table('companies')->where('id', $company)->update(['ownership_engine_enabled' => false]);
        $job = $this->makeJob($company);
        DB::table('jobs')->where('id', $job)->update(['ownership_engine_enabled' => true]);

        $flags = app(\App\Services\OwnershipEngine\Support\FeatureFlags::class);
        $this->assertTrue($flags->isAuthoritative($company, $job));
        $this->assertFalse($flags->isAuthoritative($company)); // no job context — company override (false) applies
    }

    public function test_isAuthoritative_defaults_to_false(): void
    {
        $company = $this->makeCompany();

        $this->assertFalse(app(\App\Services\OwnershipEngine\Support\FeatureFlags::class)->isAuthoritative($company));
    }

    public function test_kill_switch_stops_backfill_too(): void
    {
        DB::table('ownership_engine_settings')->where('id', 1)->update([
            'kill_switch_active' => true,
            'kill_switch_reason' => 'test',
        ]);

        $company = $this->makeCompany();
        $job = $this->makeJob($company);
        $row = $this->makeRow($job, $company, 0, 'flag6@example.test');

        $results = app(OwnershipEngine::class)->backfillCandidate($row);

        $this->assertCount(0, $results);
    }

    // ---- Rebuild / verify / orphan detection -----------------------------------------

    public function test_rebuildCluster_reconstructs_from_existing_events_without_creating_new_ones(): void
    {
        $company = $this->makeCompany();
        $job = $this->makeJob($company);
        $cr = $this->makeRow($job, $company, 0, 'rebuild1@example.test');
        $engine = app(OwnershipEngine::class);
        $engine->attemptCompanyReservation($cr);

        $eventCountBefore = DB::table('ownership_events')->count();
        $clusterId = DB::table('add_job_wise_condidates')->where('id', $cr)->value('ownership_cluster_id');

        // Simulate a corrupted cache (as if a bug or manual edit broke it).
        DB::table('add_job_wise_condidates')->where('id', $cr)->update(['ownership_state' => null]);
        $this->assertNull($this->cachedState($cr));

        $results = $engine->rebuildCluster((int) $clusterId);

        $this->assertSame(1, $this->cachedState($cr));
        $this->assertCount(1, $results);
        $this->assertSame($eventCountBefore, DB::table('ownership_events')->count()); // no new events
    }

    public function test_verifyClusterConsistency_detects_a_corrupted_cache_then_clean_after_repair(): void
    {
        $company = $this->makeCompany();
        $job = $this->makeJob($company);
        $cr = $this->makeRow($job, $company, 0, 'rebuild2@example.test');
        $engine = app(OwnershipEngine::class);
        $engine->attemptCompanyReservation($cr);

        $clusterId = (int) DB::table('add_job_wise_condidates')->where('id', $cr)->value('ownership_cluster_id');

        $this->assertSame([], $engine->verifyClusterConsistency($clusterId));

        DB::table('add_job_wise_condidates')->where('id', $cr)->update(['ownership_state' => 2]); // corrupt it

        $issues = $engine->verifyClusterConsistency($clusterId);
        $this->assertNotEmpty($issues);

        $engine->rebuildCluster($clusterId);
        $this->assertSame([], $engine->verifyClusterConsistency($clusterId));
    }

    public function test_detectOrphanClusters_finds_a_cluster_with_zero_events(): void
    {
        $company = $this->makeCompany();
        $job = $this->makeJob($company);

        $orphanId = DB::table('ownership_clusters')->insertGetId([
            'scoping_company_id' => $company,
            'scope_mode' => 1,
            'scope_key' => $job,
            'normalized_email' => 'orphan-cluster@example.test',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $orphans = app(OwnershipEngine::class)->detectOrphanClusters();

        $this->assertTrue($orphans->pluck('id')->contains($orphanId));
    }

    public function test_detectOrphanEvents_finds_an_event_pointing_to_a_nonexistent_cluster(): void
    {
        DB::table('ownership_events')->insert([
            'event_uuid' => random_bytes(16),
            'cluster_id' => 999999,
            'scoping_company_id' => 1,
            'scope_mode' => 1,
            'scope_key' => 1,
            'subject_row_id' => 1,
            'event_type' => 'OWNER_ESTABLISHED',
            'decision_rule' => 'ER2',
            'trigger_source' => 'candidate_creation',
            'reason' => 'test orphan event',
            'event_time' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $orphans = app(OwnershipEngine::class)->detectOrphanEvents();

        $this->assertGreaterThanOrEqual(1, $orphans->count());
    }

    public function test_detectCacheInconsistencies_finds_a_corrupted_row(): void
    {
        $company = $this->makeCompany();
        $job = $this->makeJob($company);
        $cr = $this->makeRow($job, $company, 0, 'rebuild3@example.test');
        $engine = app(OwnershipEngine::class);
        $engine->attemptCompanyReservation($cr);

        $this->assertCount(0, $engine->detectCacheInconsistencies());

        DB::table('add_job_wise_condidates')->where('id', $cr)->update(['ownership_state' => 2]); // now wrong

        $inconsistencies = $engine->detectCacheInconsistencies();
        $this->assertTrue($inconsistencies->pluck('id')->contains($cr));
    }
}
