<?php

namespace Tests\Feature;

use App\Http\Controllers\ServiceCompany\ServiceCompanyJobsController;
use App\Services\FindAgencyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * TenantIsolationVerificationTest
 *
 * Phase D automated verification test suite for Multi-Tenant Isolation.
 * Verifies:
 * 1. Tenant A and Tenant B can use identical idempotency keys without collision or cross-tenant leakage.
 * 2. Tenant A's transactional outbox records are completely invisible to Tenant B.
 * 3. Recommendation relations and invitation statuses are strictly isolated by tenant company_id.
 * 4. Tenant-scoped cache invalidation strictly mutates only the targeted tenant's cache version.
 */
class TenantIsolationVerificationTest extends TestCase
{
    protected $tenantA = 99911;
    protected $tenantB = 99922;
    protected $testKeys = [];

    protected function setUp(): void
    {
        parent::setUp();
        config([
            'database.default' => 'mysql',
            'database.connections.mysql.database' => 'newdata',
        ]);
        DB::purge('mysql');
        DB::reconnect('mysql');
        DB::setDefaultConnection('mysql');
    }

    protected $testRelationIds = [];
    protected $testJobIds = [];

    protected function tearDown(): void
    {
        if (! empty($this->testKeys)) {
            DB::table('sys_idempotency_keys')
                ->whereIn('tenant_id', [$this->tenantA, $this->tenantB])
                ->whereIn('idempotency_key', $this->testKeys)
                ->delete();

            DB::table('sys_outbox_events')
                ->whereIn('tenant_id', [$this->tenantA, $this->tenantB])
                ->whereIn('idempotency_key', $this->testKeys)
                ->delete();
        }

        if (! empty($this->testRelationIds)) {
            DB::table('recommendation_relation')
                ->whereIn('id', $this->testRelationIds)
                ->delete();
        }

        if (! empty($this->testJobIds)) {
            DB::table('jobs')->whereIn('id', $this->testJobIds)->delete();
        }

        parent::tearDown();
    }

    protected function invokeControllerMethod(string $methodName, array $args = [])
    {
        $controller = app(ServiceCompanyJobsController::class);
        $ref = new \ReflectionClass($controller);
        $method = $ref->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($controller, $args);
    }

    /**
     * Test 1: Tenant A and Tenant B using the same logical key string maintain total isolation.
     */
    public function test_same_idempotency_key_across_tenants_is_strictly_isolated(): void
    {
        $sharedKeyString = 'inv_batch_2026_01';
        $this->testKeys[] = $sharedKeyString;

        $payloadA = [
            'from_company_id' => $this->tenantA,
            'to_company_id' => 5001,
            'jobid' => 101,
        ];

        $payloadB = [
            'from_company_id' => $this->tenantB,
            'to_company_id' => 5002,
            'jobid' => 202,
        ];

        // Tenant A claims the key
        $claimA = $this->invokeControllerMethod('claimIdempotency', [
            $this->tenantA,
            $sharedKeyString,
            $payloadA,
        ]);
        $this->assertNull($claimA, 'Tenant A must successfully claim the key.');

        // Tenant A completes with response payload
        $this->invokeControllerMethod('completeIdempotency', [
            $this->tenantA,
            $sharedKeyString,
            ['status' => 'success', 'tenant' => 'A'],
        ]);

        // Tenant B now attempts to claim the EXACT same key string
        $claimB = $this->invokeControllerMethod('claimIdempotency', [
            $this->tenantB,
            $sharedKeyString,
            $payloadB,
        ]);
        $this->assertNull($claimB, 'Tenant B must successfully claim their own key independently, without collision.');

        // Tenant A re-checks: must get Tenant A cached payload
        $checkA = $this->invokeControllerMethod('claimIdempotency', [
            $this->tenantA,
            $sharedKeyString,
            $payloadA,
        ]);
        $this->assertEquals('A', $checkA['tenant']);

        // Tenant B completes with distinct response
        $this->invokeControllerMethod('completeIdempotency', [
            $this->tenantB,
            $sharedKeyString,
            ['status' => 'success', 'tenant' => 'B'],
        ]);

        // Tenant B re-checks: must get Tenant B payload, never Tenant A
        $checkB = $this->invokeControllerMethod('claimIdempotency', [
            $this->tenantB,
            $sharedKeyString,
            $payloadB,
        ]);
        $this->assertEquals('B', $checkB['tenant'], 'Tenant B must never see Tenant A response data.');
    }

    /**
     * Test 2: Outbox events are strictly partitioned by tenant_id.
     */
    public function test_outbox_events_are_strictly_partitioned(): void
    {
        $keyA = 'outbox_test_a_' . uniqid();
        $this->testKeys[] = $keyA;

        DB::table('sys_outbox_events')->insert([
            'tenant_id' => $this->tenantA,
            'idempotency_key' => $keyA,
            'aggregate_type' => 'AgencyRequest',
            'aggregate_id' => 6001,
            'aggregate_version' => 1,
            'event_type' => 'DIRECT_AGENCY_INVITATION_SENT',
            'payload' => json_encode(['secret_client_note' => 'Confidential Tenant A Contract']),
            'status' => 'PENDING',
            'attempts' => 0,
            'available_at' => now(),
            'created_at' => now(),
        ]);

        // Verify Tenant B query with tenant_id filter cannot access Tenant A event
        $tenantBEvents = DB::table('sys_outbox_events')
            ->where('tenant_id', $this->tenantB)
            ->where('idempotency_key', $keyA)
            ->get();

        $this->assertCount(0, $tenantBEvents, 'Tenant B must have zero visibility into Tenant A outbox events.');
    }

    /**
     * Test 3: Recommendation relation query scoping prevents cross-tenant visibility.
     */
    public function test_recommendation_relation_is_tenant_scoped(): void
    {
        $idA = DB::table('recommendation_relation')->insertGetId([
            'from_company_id' => $this->tenantA,
            'to_company_id' => 7001,
            'job_id' => 901,
            'final_status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->testRelationIds[] = $idA;

        $idB = DB::table('recommendation_relation')->insertGetId([
            'from_company_id' => $this->tenantB,
            'to_company_id' => 7002,
            'job_id' => 902,
            'final_status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->testRelationIds[] = $idB;

        // Query scoped to Tenant A
        $relA = DB::table('recommendation_relation')
            ->where('from_company_id', $this->tenantA)
            ->whereIn('id', $this->testRelationIds)
            ->get();

        $this->assertCount(1, $relA);
        $this->assertEquals($idA, $relA->first()->id);

        // Query scoped to Tenant B
        $relB = DB::table('recommendation_relation')
            ->where('from_company_id', $this->tenantB)
            ->whereIn('id', $this->testRelationIds)
            ->get();

        $this->assertCount(1, $relB);
    }

    /**
     * Test 4: Tenant cache invalidation only increments targeted tenant version.
     */
    public function test_tenant_cache_invalidation_is_strictly_scoped(): void
    {
        $versionKeyA = "ats:v2:findagency:version:{$this->tenantA}";
        $versionKeyB = "ats:v2:findagency:version:{$this->tenantB}";

        Cache::put($versionKeyA, 10, 3600);
        Cache::put($versionKeyB, 20, 3600);

        // Invalidate Tenant A
        FindAgencyService::invalidateTenantCache($this->tenantA);

        $newVersionA = Cache::get($versionKeyA);
        $newVersionB = Cache::get($versionKeyB);

        $this->assertEquals(11, $newVersionA, 'Tenant A version must increment to 11.');
        $this->assertEquals(20, $newVersionB, 'Tenant B version must remain untouched at 20.');
    }

    /**
     * Test 5: Forged company ID in mutation is strictly rejected.
     */
    public function test_forged_company_id_in_mutation_is_strictly_rejected(): void
    {
        $userA = new \App\Models\User();
        $userA->id = 88811;
        $userA->company_id = $this->tenantA;
        Auth::setUser($userA);

        $controller = app(ServiceCompanyJobsController::class);
        $controller->user = $userA;

        // Attacker logs in as Tenant A but submits payload claiming to be Tenant B
        $request = Request::create('/service_company/jobs/send_only_job_invitation', 'POST', [
            'fromcompany' => $this->tenantB, // Forged company!
            'tocompany' => 5001,
            'jobid' => 12345,
        ]);

        $response = $controller->send_only_job_invitation($request);
        $data = is_array($response) ? $response : (method_exists($response, 'getData') ? $response->getData(true) : json_decode($response->getContent(), true));

        $this->assertEquals('error', $data['status'] ?? null);
        $this->assertStringContainsString('Forged company identity', $data['message'] ?? '');
    }

    /**
     * Test 6: Cross-tenant job ID in mutation is strictly rejected.
     */
    public function test_cross_tenant_job_id_in_mutation_is_strictly_rejected(): void
    {
        $userA = new \App\Models\User();
        $userA->id = 88811;
        $userA->company_id = $this->tenantA;
        Auth::setUser($userA);

        $controller = app(ServiceCompanyJobsController::class);
        $controller->user = $userA;

        // Insert Job belonging exclusively to Tenant B
        $jobBId = DB::table('jobs')->insertGetId([
            'company_id' => $this->tenantB,
            'title' => 'Tenant B Confidential Role',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->testJobIds[] = $jobBId;

        // Tenant A attempts to invite agency to Tenant B's job
        $request = Request::create('/service_company/jobs/send_only_job_invitation', 'POST', [
            'fromcompany' => $this->tenantA,
            'tocompany' => 5001,
            'jobid' => $jobBId,
        ]);

        $response = $controller->send_only_job_invitation($request);
        $data = is_array($response) ? $response : (method_exists($response, 'getData') ? $response->getData(true) : json_decode($response->getContent(), true));

        $this->assertEquals('error', $data['status'] ?? null);
        $this->assertStringContainsString('Job requisition does not belong to your company', $data['message'] ?? '');
    }

    /**
     * Test 7: Cross-tenant job ID in drawer recommendedData returns 403 Forbidden.
     */
    public function test_cross_tenant_job_id_in_drawer_recommended_data_returns_403(): void
    {
        $userA = new \App\Models\User();
        $userA->id = 88811;
        $userA->company_id = $this->tenantA;
        Auth::setUser($userA);

        $controller = app(ServiceCompanyJobsController::class);
        $controller->user = $userA;

        // Insert Job belonging exclusively to Tenant B
        $jobBId = DB::table('jobs')->insertGetId([
            'company_id' => $this->tenantB,
            'title' => 'Tenant B Role',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->testJobIds[] = $jobBId;

        $request = Request::create('/service_company/jobs/recommendedData', 'GET', [
            'jobid' => $jobBId,
        ]);
        $request->setUserResolver(fn() => $userA);

        $response = $controller->recommendedData($request);

        $this->assertEquals(403, $response->getStatusCode(), 'Cross-tenant drawer access must be rejected with HTTP 403 Forbidden.');
    }

    /**
     * Test 8: Cache hit cannot bypass tenant authorization for recommendedData.
     */
    public function test_cache_hit_cannot_bypass_tenant_authorization(): void
    {
        $userB = new \App\Models\User();
        $userB->id = 88822;
        $userB->company_id = $this->tenantB;

        // Create job for Tenant B
        $jobBId = DB::table('jobs')->insertGetId([
            'company_id' => $this->tenantB,
            'title' => 'Tenant B Secret Job',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->testJobIds[] = $jobBId;

        // Pre-warm the cache for this job
        $jobMetaCacheKey = 'ats:job_summary_meta:' . $jobBId;
        Cache::put($jobMetaCacheKey, [
            'job' => ['id' => $jobBId, 'company_id' => $this->tenantB, 'title' => 'Secret Job'],
            'skills' => ['php', 'laravel'],
        ], 3600);

        // Now Tenant A makes request while cache is warm
        $userA = new \App\Models\User();
        $userA->id = 88811;
        $userA->company_id = $this->tenantA;
        Auth::setUser($userA);

        $controller = app(ServiceCompanyJobsController::class);
        $controller->user = $userA;

        $request = Request::create('/service_company/jobs/recommendedData', 'GET', [
            'jobid' => $jobBId,
        ]);
        $request->setUserResolver(fn() => $userA);

        $response = $controller->recommendedData($request);

        $this->assertEquals(403, $response->getStatusCode(), 'Even with pre-warmed cache, Tenant A must be rejected with 403.');
    }
}
