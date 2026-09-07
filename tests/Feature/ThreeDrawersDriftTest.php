<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * V3 Three-Drawer Step 9 — Counter Drift Detection & Healing Tests
 *
 * Covers:
 *  A. Command wiring / class contracts
 *  B. V3 status cache staleness detection
 *  C. V3 status cache healing (--heal flag)
 *  D. Clean-state exits with SUCCESS
 *  E. Job counters table drift detection
 *  F. Job counters healing
 *  G. Version key informational check (does not count as hard drift)
 *  H. Write-path root cause: Cache::forget() order guarantees
 *  I. Cache prefix / feature flag behaviour
 */
class ThreeDrawersDriftTest extends TestCase
{
    // use RefreshDatabase; // Disabled per project rule: never run RefreshDatabase

    protected function setUp(): void
    {
        parent::setUp();
        // Use the array cache driver in tests — no Redis dependency.
        config(['cache.default' => 'array']);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // A — Class / wiring contracts
    // ─────────────────────────────────────────────────────────────────────────

    /** @test */
    public function command_class_exists(): void
    {
        $this->assertTrue(
            class_exists(\App\Console\Commands\VerifyThreeDrawersDrift::class),
            'VerifyThreeDrawersDrift command class must exist'
        );
    }

    /** @test */
    public function command_signature_is_correct(): void
    {
        $cmd = new \App\Console\Commands\VerifyThreeDrawersDrift();
        $this->assertStringContainsString('drawers:verify-drift', $cmd->getName());
    }

    /** @test */
    public function command_has_heal_option(): void
    {
        $cmd = new \App\Console\Commands\VerifyThreeDrawersDrift();
        $this->assertTrue(
            $cmd->getDefinition()->hasOption('heal'),
            'Command must have a --heal option'
        );
    }

    /** @test */
    public function command_has_hours_option(): void
    {
        $cmd = new \App\Console\Commands\VerifyThreeDrawersDrift();
        $this->assertTrue(
            $cmd->getDefinition()->hasOption('hours'),
            'Command must have a --hours option'
        );
    }

    /** @test */
    public function command_is_scheduled_in_kernel(): void
    {
        $kernel   = app(\Illuminate\Contracts\Console\Kernel::class);
        $schedule = app(\Illuminate\Console\Scheduling\Schedule::class);
        // Trigger schedule registration.
        $kernel->call('schedule:list');

        $commands = collect($schedule->events())->map(fn($e) => $e->command ?? '');
        $hasCommand = $commands->contains(fn($c) => str_contains($c, 'drawers:verify-drift'));

        $this->assertTrue($hasCommand, 'drawers:verify-drift must be registered in Kernel::schedule()');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // B — Stale V3 status cache DETECTION
    // ─────────────────────────────────────────────────────────────────────────

    /** @test */
    public function detects_stale_v3_status_cache(): void
    {
        // Insert a job with status='active' into DB.
        $jobId = DB::table('jobs')->insertGetId([
            'company_id'   => 1,
            'title'        => 'Test Job',
            'status'       => 'active',
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        // Plant a stale cache entry reflecting the OLD status (inactive).
        $staleJob         = new \stdClass();
        $staleJob->status = 'inactive';
        $staleJob->id     = $jobId;
        Cache::put("v3:status_job:{$jobId}", $staleJob, 3600);

        // Run command WITHOUT --heal — should exit FAILURE.
        $this->artisan('drawers:verify-drift', ['--hours' => 1])
             ->assertExitCode(1);
    }

    /** @test */
    public function reports_no_drift_when_cache_matches_db(): void
    {
        $jobId = DB::table('jobs')->insertGetId([
            'company_id'   => 1,
            'title'        => 'Clean Job',
            'status'       => 'active',
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        // Cache entry matches DB status.
        $freshJob         = new \stdClass();
        $freshJob->status = 'active';
        $freshJob->id     = $jobId;
        Cache::put("v3:status_job:{$jobId}", $freshJob, 3600);

        $this->artisan('drawers:verify-drift', ['--hours' => 1])
             ->assertExitCode(0);
    }

    /** @test */
    public function ignores_jobs_with_no_cache_entry(): void
    {
        // Job in DB, no corresponding cache key — should be clean.
        DB::table('jobs')->insertGetId([
            'company_id' => 1,
            'title'      => 'Uncached Job',
            'status'     => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->artisan('drawers:verify-drift', ['--hours' => 1])
             ->assertExitCode(0);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // C — Stale V3 status cache HEALING
    // ─────────────────────────────────────────────────────────────────────────

    /** @test */
    public function heals_stale_v3_status_cache_and_exits_success(): void
    {
        $jobId = DB::table('jobs')->insertGetId([
            'company_id'   => 1,
            'title'        => 'Healed Job',
            'status'       => 'active',
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        $staleJob         = new \stdClass();
        $staleJob->status = 'hold';
        Cache::put("v3:status_job:{$jobId}", $staleJob, 3600);
        Cache::put("v3:status_log:{$jobId}", (object)['created_at' => now()->subDay()], 3600);

        $this->artisan('drawers:verify-drift', ['--heal' => true, '--hours' => 1])
             ->assertExitCode(0);

        // Both keys must be cleared after healing.
        $this->assertNull(Cache::get("v3:status_job:{$jobId}"), 'Status cache must be cleared after heal');
        $this->assertNull(Cache::get("v3:status_log:{$jobId}"), 'Log cache must be cleared after heal');
    }

    /** @test */
    public function heal_does_not_clear_fresh_cache_entries(): void
    {
        $jobId = DB::table('jobs')->insertGetId([
            'company_id' => 1,
            'title'      => 'Fresh Job',
            'status'     => 'inactive',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $freshJob         = new \stdClass();
        $freshJob->status = 'inactive'; // matches DB
        Cache::put("v3:status_job:{$jobId}", $freshJob, 3600);

        $this->artisan('drawers:verify-drift', ['--heal' => true, '--hours' => 1])
             ->assertExitCode(0);

        // Fresh entry must NOT be evicted.
        $this->assertNotNull(Cache::get("v3:status_job:{$jobId}"), 'Fresh cache entry must survive heal run');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // D — Clean state exits SUCCESS
    // ─────────────────────────────────────────────────────────────────────────

    /** @test */
    public function exits_success_with_empty_database(): void
    {
        $this->artisan('drawers:verify-drift', ['--hours' => 1])
             ->assertExitCode(0);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // E — job_counters table drift DETECTION
    // ─────────────────────────────────────────────────────────────────────────

    /** @test */
    public function detects_job_counters_drift_when_table_exists(): void
    {
        if (!DB::getSchemaBuilder()->hasTable('job_counters')) {
            $this->markTestSkipped('job_counters table not present in test DB');
        }

        // Insert a live job.
        DB::table('jobs')->insert([
            'company_id' => 99,
            'title'      => 'Counter Test Job',
            'status'     => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Insert a deliberately wrong counter row.
        DB::table('job_counters')->upsert(
            [['company_id' => 99, 'total_jobs' => 0, 'hiring_jobs' => 0, 'on_hold_jobs' => 0, 'closed_jobs' => 0, 'last_event_at' => now()]],
            ['company_id'],
            ['total_jobs', 'hiring_jobs', 'on_hold_jobs', 'closed_jobs', 'last_event_at']
        );

        $this->artisan('drawers:verify-drift', ['--hours' => 1])
             ->assertExitCode(1);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // F — job_counters HEALING
    // ─────────────────────────────────────────────────────────────────────────

    /** @test */
    public function heals_job_counters_drift(): void
    {
        if (!DB::getSchemaBuilder()->hasTable('job_counters')) {
            $this->markTestSkipped('job_counters table not present in test DB');
        }

        DB::table('jobs')->insert([
            'company_id' => 98,
            'title'      => 'Heal Counter Job',
            'status'     => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Wrong counter seeded.
        DB::table('job_counters')->upsert(
            [['company_id' => 98, 'total_jobs' => 0, 'hiring_jobs' => 0, 'on_hold_jobs' => 0, 'closed_jobs' => 0, 'last_event_at' => now()]],
            ['company_id'],
            ['total_jobs', 'hiring_jobs', 'on_hold_jobs', 'closed_jobs', 'last_event_at']
        );

        $this->artisan('drawers:verify-drift', ['--heal' => true, '--hours' => 1])
             ->assertExitCode(0);

        $row = DB::table('job_counters')->where('company_id', 98)->first();
        $this->assertEquals(1, (int) $row->total_jobs, 'total_jobs must be healed to 1');
        $this->assertEquals(1, (int) $row->hiring_jobs, 'hiring_jobs must be healed to 1 (active=hiring)');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // G — Version key check is INFORMATIONAL (does not fail the command)
    // ─────────────────────────────────────────────────────────────────────────

    /** @test */
    public function missing_version_key_does_not_cause_hard_drift(): void
    {
        // Insert a user into job_assign without seeding a version key.
        // This simulates a Redis flush that cleared version keys.
        if (!DB::getSchemaBuilder()->hasTable('job_assign')) {
            $this->markTestSkipped('job_assign table not present in test DB');
        }

        // Ensure the version key is absent.
        Cache::forget('allassignedagency_version_user_999');

        DB::table('job_assign')->insert([
            'job_id'     => 1,
            'user_id'    => 999,
            'company_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // No stale status caches; no counter drift — command must still succeed.
        $this->artisan('drawers:verify-drift', ['--hours' => 1])
             ->assertExitCode(0);
    }

    /** @test */
    public function heal_seeds_missing_version_keys(): void
    {
        if (!DB::getSchemaBuilder()->hasTable('job_assign')) {
            $this->markTestSkipped('job_assign table not present in test DB');
        }

        Cache::forget('allassignedagency_version_user_777');

        DB::table('job_assign')->insert([
            'job_id'     => 2,
            'user_id'    => 777,
            'company_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->artisan('drawers:verify-drift', ['--heal' => true, '--hours' => 1])
             ->assertExitCode(0);

        $this->assertNotNull(
            Cache::get('allassignedagency_version_user_777'),
            'Missing version key must be seeded to 1 after --heal'
        );
        $this->assertEquals(1, (int) Cache::get('allassignedagency_version_user_777'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // H — Write-path root cause: Cache::forget() must happen before
    //     JobStatusLog::create() (prevents drift on exception)
    // ─────────────────────────────────────────────────────────────────────────

    /** @test */
    public function cache_forget_precedes_job_status_log_create_in_source(): void
    {
        // Structural test: verify the order of operations in newUpdateJobstatus().
        // If this ever regresses, the test fails before any drift can accumulate in prod.
        $controllerFile = base_path(
            'app/Http/Controllers/ServiceCompany/ServiceCompanyJobsController.php'
        );
        $this->assertFileExists($controllerFile);

        $source = file_get_contents($controllerFile);

        // Find both positions in the source.
        $forgetPos  = strpos($source, 'Cache::forget("v3:status_job:{$job_id}")');
        $logPos     = strpos($source, 'JobStatusLog::create([');

        $this->assertNotFalse($forgetPos,  'Cache::forget(v3:status_job) must exist in controller');
        $this->assertNotFalse($logPos,     'JobStatusLog::create() must exist in controller');

        $this->assertLessThan(
            $logPos,
            $forgetPos,
            'Cache::forget() must appear BEFORE JobStatusLog::create() to prevent drift on exception'
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // I — Feature flag / isolation
    // ─────────────────────────────────────────────────────────────────────────

    /** @test */
    public function command_runs_regardless_of_feature_flag(): void
    {
        // The drift command itself must not be gated behind use_three_drawers_v3.
        // Even when the flag is OFF, old stale cache entries could exist from when it was ON.
        config(['feature.use_three_drawers_v3' => false]);

        $this->artisan('drawers:verify-drift', ['--hours' => 1])
             ->assertExitCode(0);
    }

    /** @test */
    public function detects_stale_cache_even_when_feature_flag_is_off(): void
    {
        config(['feature.use_three_drawers_v3' => false]);

        $jobId = DB::table('jobs')->insertGetId([
            'company_id' => 1,
            'title'      => 'Flag-off Stale Job',
            'status'     => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $stale         = new \stdClass();
        $stale->status = 'hold';
        Cache::put("v3:status_job:{$jobId}", $stale, 3600);

        // Command must still detect the stale entry even if the flag is off,
        // so it can clean up caches left from a previous flag=ON deployment.
        $this->artisan('drawers:verify-drift', ['--hours' => 1])
             ->assertExitCode(1);
    }
}
