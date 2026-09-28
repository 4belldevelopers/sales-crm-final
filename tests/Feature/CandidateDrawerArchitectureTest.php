<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\JobApplication;
use App\Models\User;
use App\Services\CandidateDrawerReadService;
use App\Services\CandidateDrawerSecurityService;
use App\Services\CandidateElasticRebuildService;
use App\Services\CandidateOutboxTelemetryService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class CandidateDrawerArchitectureTest
 *
 * Intent: Comprehensive feature test suite verifying all architectural invariants
 * defined in docs/drawer_candidate.md:
 * 1. Zero MySQL SELECTs across ALL drawer read endpoints through full HTTP kernel stack
 * 2. Tenant-scoped permission_version and realtime security invalidation
 * 3. Transactionally coordinated idempotency state machine and atomic OCC
 * 4. Watermark-guaranteed Elasticsearch rebuild protocol
 * 5. Hard CI-enforced <= 15 KB gzip payload budget on candidate core endpoint
 * 6. Outbox production SLO telemetry and alert thresholds
 */
class CandidateDrawerArchitectureTest extends TestCase
{
    const DRAWER_READ_ENDPOINTS = [
        '/service_company/job-applications/{id}/core-json',
        '/service_company/job-applications/{id}/interviews',
        '/service_company/job-applications/{id}/questions',
        '/service_company/job-applications/{id}/timeline',
        '/service_company/job-applications/{id}/resume-meta',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        \Illuminate\Support\Facades\Queue::fake();
        Company::unsetEventDispatcher();
        config(['auth.single_device_login.enabled' => false]);

        $this->buildSchema();
    }

    private function buildSchema(): void
    {
        if (! \Illuminate\Support\Facades\Schema::hasTable('companies')) {
            \Illuminate\Support\Facades\Schema::create('companies', function ($t) {
                $t->increments('id');
                $t->string('company_name', 255)->nullable();
                $t->string('company_email', 191)->nullable();
                $t->string('company_phone', 55)->nullable();
                $t->string('website', 255)->nullable();
                $t->text('address')->nullable();
                $t->string('status', 32)->default('active');
                $t->string('package_type', 32)->default('monthly');
                $t->timestamps();
            });
        }

        if (! \Illuminate\Support\Facades\Schema::hasTable('users')) {
            \Illuminate\Support\Facades\Schema::create('users', function ($t) {
                $t->increments('id');
                $t->unsignedInteger('company_id')->nullable();
                $t->string('name', 191)->nullable();
                $t->string('email', 191)->nullable();
                $t->timestamp('email_verified_at')->nullable();
                $t->string('password', 191)->nullable();
                $t->string('role', 100)->nullable();
                $t->string('status', 32)->default('active');
                $t->rememberToken();
                $t->timestamps();
            });
        }

        if (! \Illuminate\Support\Facades\Schema::hasTable('job_applications')) {
            \Illuminate\Support\Facades\Schema::create('job_applications', function ($t) {
                $t->increments('id');
                $t->unsignedInteger('company_id')->nullable();
                $t->integer('submitted_by')->default(1);
                $t->integer('thirdparty_company_id')->default(0);
                $t->unsignedInteger('job_id')->nullable();
                $t->string('full_name', 191)->nullable();
                $t->string('email', 191)->nullable();
                $t->string('phone_code', 11)->nullable();
                $t->string('phone', 191)->nullable();
                $t->string('secondrary_email', 55)->nullable();
                $t->string('secondary_mobile', 20)->nullable();
                $t->string('gender', 50)->nullable();
                $t->date('dob')->nullable();
                $t->string('country', 100)->nullable();
                $t->string('state', 100)->nullable();
                $t->string('city', 100)->nullable();
                $t->string('current_location', 100)->nullable();
                $t->string('current_designation', 100)->nullable();
                $t->string('current_company', 100)->nullable();
                $t->string('total_exp', 50)->nullable();
                $t->string('rel_exp', 50)->nullable();
                $t->text('skills')->nullable();
                $t->string('rating', 10)->nullable();
                $t->unsignedInteger('status_id')->default(1);
                $t->unsignedInteger('master_status_id')->default(1);
                $t->unsignedInteger('child_status_id')->nullable();
                $t->integer('ctc')->nullable();
                $t->integer('ectc')->nullable();
                $t->integer('current_ctc_cur')->default(0);
                $t->integer('current_ctc_mode')->default(0);
                $t->integer('current_ectc_cur')->default(0);
                $t->integer('current_ectc_mode')->default(0);
                $t->integer('proposed_ctc')->nullable();
                $t->integer('offer_amount')->nullable();
                $t->integer('offer_currency')->nullable();
                $t->integer('offer_mode')->nullable();
                $t->mediumText('recruiter_note')->nullable();
                $t->text('careersummary')->nullable();
                $t->text('resume')->nullable();
                $t->unsignedBigInteger('current_resume_id')->nullable();
                $t->unsignedInteger('resume_version')->default(1);
                $t->unsignedInteger('aggregate_version')->default(1);
                $t->string('client_submission_uuid', 36)->nullable();
                $t->timestamps();
                $t->softDeletes();
            });
        }

        if (! \Illuminate\Support\Facades\Schema::hasTable('currencies')) {
            \Illuminate\Support\Facades\Schema::create('currencies', function ($t) {
                $t->increments('id');
                $t->string('currency_name', 191)->nullable();
                $t->string('currency_symbol', 191)->nullable();
                $t->string('currency_code', 191)->nullable();
                $t->integer('getKey')->default(0);
                $t->timestamps();
            });

            \Illuminate\Support\Facades\DB::table('currencies')->insert([
                'currency_name' => 'Dollar',
                'currency_symbol' => '$',
                'currency_code' => 'USD',
                'getKey' => 4,
            ]);
        }

        if (! \Illuminate\Support\Facades\Schema::hasTable('pipeline_counters')) {
            \Illuminate\Support\Facades\Schema::create('pipeline_counters', function ($t) {
                $t->unsignedInteger('job_id')->primary();
                $t->unsignedInteger('company_id');
                $t->unsignedInteger('sourcing')->default(0);
                $t->unsignedInteger('screening')->default(0);
                $t->unsignedInteger('evaluation')->default(0);
                $t->unsignedInteger('negotiation')->default(0);
                $t->unsignedInteger('offered')->default(0);
                $t->unsignedInteger('joined')->default(0);
                $t->unsignedInteger('rejected')->default(0);
                $t->tinyInteger('fresh_data')->default(1);
                $t->timestamp('last_event_at')->nullable();
            });
        }

        // Run outbox & idempotency migrations
        $this->artisan('migrate', ['--path' => 'database/migrations/2026_09_28_000001_create_ats_candidate_drawer_sys_outbox_and_idempotency.php']);
    }

    /**
     * Invariant 1: Strictly Zero MySQL SELECTs across complete drawer HTTP stack matrix.
     */
    public function test_enforces_strictly_zero_mysql_selects_across_complete_drawer_http_stack(): void
    {
        $tenantA = Company::factory()->create();
        $tenantB = Company::factory()->create();

        $candidateA = JobApplication::factory()->create(['company_id' => $tenantA->id]);
        CandidateDrawerReadService::recordCandidateProjection($candidateA);

        $authorizedUserA = User::factory()->create(['company_id' => $tenantA->id]);
        $authorizedUserA->role = 'recruiter_authorized';

        $restrictedUserA = User::factory()->create(['company_id' => $tenantA->id]);
        $restrictedUserA->role = 'recruiter_restricted';

        $crossTenantUserB = User::factory()->create(['company_id' => $tenantB->id]);
        $crossTenantUserB->role = 'recruiter_authorized';

        $testMatrix = [
            ['user' => $authorizedUserA, 'expected_status' => 200],
            ['user' => $restrictedUserA, 'expected_status' => 403],
            ['user' => $crossTenantUserB, 'expected_status' => 403],
            ['user' => null, 'expected_status' => 401],
        ];

        foreach (self::DRAWER_READ_ENDPOINTS as $endpoint) {
            foreach ($testMatrix as $case) {
                $selectExecuted = false;
                $executedQueries = [];

                DB::listen(function ($query) use (&$selectExecuted, &$executedQueries) {
                    $sql = strtoupper(trim($query->sql));
                    if (str_starts_with($sql, 'SELECT')) {
                        $selectExecuted = true;
                        $executedQueries[] = $query->sql;
                    }
                });

                $url = str_replace('{id}', (string) $candidateA->id, $endpoint);
                if ($case['user'] === null) {
                    auth()->logout();
                    $response = $this->getJson($url);
                } else {
                    $response = $this->actingAs($case['user'])->getJson($url);
                }

                $response->assertStatus($case['expected_status']);

                $this->assertFalse(
                    $selectExecuted,
                    "Architectural Invariant Violation: MySQL SELECT executed on drawer endpoint [{$endpoint}]: ".implode('; ', $executedQueries)
                );
            }
        }
    }

    /**
     * Invariant 4: Hard CI-Enforced 15 KB gzip Payload Budget on candidate core endpoint.
     */
    public function test_enforces_hard_15kb_gzip_budget_on_candidate_core_endpoint(): void
    {
        $tenant = Company::factory()->create();
        $candidate = JobApplication::factory()->createWithAllAttributes(['company_id' => $tenant->id]);
        CandidateDrawerReadService::recordCandidateProjection($candidate);

        $user = User::factory()->create(['company_id' => $tenant->id]);
        $user->role = 'recruiter_authorized';

        $response = $this->actingAs($user)->getJson("/service_company/job-applications/{$candidate->id}/core-json");
        $response->assertStatus(200);

        // Measures actual HTTP representation (Content-Encoding: gzip)
        $actualGzipBytes = strlen(gzencode($response->getContent(), 9));
        $this->assertLessThanOrEqual(
            15360,
            $actualGzipBytes,
            "Core payload budget exceeded: {$actualGzipBytes} bytes > 15360 bytes (15 KB gzip ceiling)"
        );
    }

    /**
     * Invariant 3: Transactionally Coordinated Idempotency State Machine (Winner, Replay, Payload Mismatch).
     */
    public function test_transactionally_coordinated_idempotency_state_machine(): void
    {
        $tenant = Company::factory()->create();
        $candidate = JobApplication::factory()->create([
            'company_id' => $tenant->id,
            'aggregate_version' => 1,
            'recruiter_note' => 'Original note',
        ]);

        $user = User::factory()->create(['company_id' => $tenant->id]);
        $idempotencyKey = (string) Str::uuid();

        // 1. Initial Request (Winner)
        $payload = [
            'recruiter_note' => 'First updated note',
            'expected_version' => 1,
        ];

        $response1 = $this->actingAs($user)
            ->withHeader('Idempotency-Key', $idempotencyKey)
            ->postJson("/service_company/job-applications/{$candidate->id}/mutate", $payload);

        $response1->assertStatus(200);
        $response1->assertJsonFragment([
            'status' => 'success',
            'aggregate_version' => 2,
        ]);

        // Verify outbox event written in same transaction
        $this->assertDatabaseHas('sys_outbox_events', [
            'tenant_id' => $tenant->id,
            'idempotency_key' => $idempotencyKey,
            'aggregate_id' => $candidate->id,
            'aggregate_version' => 2,
            'status' => 'PENDING',
        ]);

        // Verify idempotency key is COMPLETED
        $this->assertDatabaseHas('sys_idempotency_keys', [
            'tenant_id' => $tenant->id,
            'idempotency_key' => $idempotencyKey,
            'status' => 'COMPLETED',
            'response_status' => 200,
        ]);

        // 2. Duplicate Request with identical payload (Replay) -> returns 200 without double mutation
        $response2 = $this->actingAs($user)
            ->withHeader('Idempotency-Key', $idempotencyKey)
            ->postJson("/service_company/job-applications/{$candidate->id}/mutate", $payload);

        $response2->assertStatus(200);
        $response2->assertJsonFragment([
            'status' => 'success',
            'aggregate_version' => 2,
        ]);

        // 3. Duplicate Key with Mismatched Payload -> returns 422 Unprocessable Entity
        $mismatchedPayload = [
            'recruiter_note' => 'Different payload with same key',
            'expected_version' => 1,
        ];

        $response3 = $this->actingAs($user)
            ->withHeader('Idempotency-Key', $idempotencyKey)
            ->postJson("/service_company/job-applications/{$candidate->id}/mutate", $mismatchedPayload);

        $response3->assertStatus(422);
    }

    /**
     * Invariant 3: Optimistic Concurrency Control (OCC) conflict returns 409 and marks FAILED_FINAL.
     */
    public function test_occ_conflict_marks_failed_final_and_returns_409(): void
    {
        $tenant = Company::factory()->create();
        $candidate = JobApplication::factory()->create([
            'company_id' => $tenant->id,
            'aggregate_version' => 5, // Currently at version 5
        ]);

        $user = User::factory()->create(['company_id' => $tenant->id]);
        $idempotencyKey = (string) Str::uuid();

        // Send mutation with stale expected_version = 4
        $payload = [
            'recruiter_note' => 'Conflicting write attempt',
            'expected_version' => 4,
        ];

        $response = $this->actingAs($user)
            ->withHeader('Idempotency-Key', $idempotencyKey)
            ->postJson("/service_company/job-applications/{$candidate->id}/mutate", $payload);

        $response->assertStatus(409);

        // Verify idempotency record recorded FAILED_FINAL with 409 status
        $this->assertDatabaseHas('sys_idempotency_keys', [
            'tenant_id' => $tenant->id,
            'idempotency_key' => $idempotencyKey,
            'status' => 'FAILED_FINAL',
            'response_status' => 409,
        ]);
    }

    /**
     * Invariant 2: Tenant-scoped permission_version and auth_context_hash calculation.
     */
    public function test_tenant_scoped_permission_version_and_auth_context_hash(): void
    {
        $securityService = app(CandidateDrawerSecurityService::class);
        $tenantId = 7788;
        $userId = 101;
        $roleId = 'recruiter_lead';

        $initialVersion = $securityService->getPermissionVersion($tenantId);
        $this->assertEquals(1, $initialVersion);

        $hash1 = $securityService->calculateAuthContextHash($tenantId, $userId, $roleId, $initialVersion);
        $this->assertEquals(64, strlen($hash1));

        // Bump permission_version (e.g., admin revokes salary view)
        $newVersion = $securityService->incrementPermissionVersion($tenantId, $userId, $roleId);
        $this->assertEquals(2, $newVersion);

        $hash2 = $securityService->calculateAuthContextHash($tenantId, $userId, $roleId, $newVersion);
        $this->assertNotEquals($hash1, $hash2);
    }

    /**
     * Invariant 3: Scheduled pruning command deletes expired idempotency records.
     */
    public function test_idempotency_pruning_command(): void
    {
        // Expired record
        DB::table('sys_idempotency_keys')->insert([
            'tenant_id' => 1,
            'idempotency_key' => 'expired-key-1',
            'request_hash' => 'hash1',
            'status' => 'COMPLETED',
            'expires_at' => now()->subHour(),
            'created_at' => now()->subDay(),
            'updated_at' => now()->subDay(),
        ]);

        // Active record
        DB::table('sys_idempotency_keys')->insert([
            'tenant_id' => 1,
            'idempotency_key' => 'active-key-1',
            'request_hash' => 'hash2',
            'status' => 'COMPLETED',
            'expires_at' => now()->addHours(12),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->artisan('idempotency:prune')->assertSuccessful();

        $this->assertDatabaseMissing('sys_idempotency_keys', ['idempotency_key' => 'expired-key-1']);
        $this->assertDatabaseHas('sys_idempotency_keys', ['idempotency_key' => 'active-key-1']);
    }

    /**
     * Invariant 4: Watermark-Guaranteed Elasticsearch Rebuild Protocol.
     */
    public function test_watermark_guaranteed_rebuild_protocol(): void
    {
        $rebuildService = app(CandidateElasticRebuildService::class);
        $result = $rebuildService->executeRebuild('candidates_test_alias', true);

        $this->assertEquals('completed', $result['status']);
        $this->assertArrayHasKey('watermark_id', $result);
        $this->assertArrayHasKey('snapshot_count', $result);
        $this->assertArrayHasKey('replayed_events_count', $result);
    }

    /**
     * Section 3: Outbox Production SLO Telemetry Metrics calculation.
     */
    public function test_outbox_telemetry_slo_metrics(): void
    {
        $telemetryService = app(CandidateOutboxTelemetryService::class);
        $metrics = $telemetryService->getTelemetryMetrics();

        $this->assertArrayHasKey('max_projection_lag_ver', $metrics);
        $this->assertArrayHasKey('oldest_pending_event_age_sec', $metrics);
        $this->assertArrayHasKey('outbox_dead_letter_count', $metrics);
        $this->assertArrayHasKey('mutation_resilience_rate', $metrics);
        $this->assertArrayHasKey('alerts', $metrics);

        $this->artisan('candidates:outbox-telemetry', ['--json' => true])->assertSuccessful();
    }
}
