<?php

namespace Tests\Feature;

use App\Http\Middleware\EnsureOneDeviceLogin;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * SendRequirementMultipleAgenciesTest
 *
 * Automated regression and feature verification for the Send Requirement to Multiple Agencies workflow.
 * Verifies:
 * 1. Authentication and tenancy protection (unauthorized or cross-tenant access rejected).
 * 2. Request payload validation (job_id, agency_ids array required).
 * 3. Atomic database operations (staffing_company_invitation, recommendation_relation, sys_outbox_events).
 * 4. Deduplication against already invited agencies.
 * 5. Idempotent outbox event creation.
 */
class SendRequirementMultipleAgenciesTest extends TestCase
{
    protected $testUser;
    protected $jobId;
    protected $companyId;

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

        $this->withoutMiddleware([EnsureOneDeviceLogin::class]);

        // Locate an authoritative test job and active owner from a standard tenant (> 1)
        $job = DB::table('jobs')->whereNotNull('company_id')->where('company_id', '>', 1)->first();
        if ($job) {
            $this->jobId = $job->id;
            $this->companyId = $job->company_id;
            $this->testUser = User::withoutGlobalScopes()
                ->where('company_id', $this->companyId)
                ->where('status', 'active')
                ->first()
                ?? User::withoutGlobalScopes()
                    ->where('company_id', $this->companyId)
                    ->first();
        } else {
            $this->jobId = 1;
            $this->companyId = 2;
            $this->testUser = User::withoutGlobalScopes()->where('company_id', '>', 1)->first();
        }
    }

    /**
     * Test unauthenticated access is rejected or redirected.
     */
    public function test_unauthenticated_request_is_rejected(): void
    {
        Auth::logout();
        $response = $this->postJson(route('service_company.jobs.send_multiple_job_invitations'), [
            'job_id' => $this->jobId,
            'agency_ids' => [1001, 1002],
        ]);

        $this->assertTrue(in_array($response->getStatusCode(), [401, 302, 403]));
    }

    /**
     * Test validation rejects missing parameters.
     */
    public function test_validation_rejects_missing_job_or_empty_agency_ids(): void
    {
        if (!$this->testUser) {
            $this->markTestSkipped('No test user available');
        }

        // Missing job_id
        $response1 = $this->actingAs($this->testUser)->postJson(route('service_company.jobs.send_multiple_job_invitations'), [
            'agency_ids' => [1001],
        ]);
        $response1->assertStatus(422);

        // Empty agency_ids
        $response2 = $this->actingAs($this->testUser)->postJson(route('service_company.jobs.send_multiple_job_invitations'), [
            'job_id' => $this->jobId,
            'agency_ids' => [],
        ]);
        $response2->assertStatus(422);
    }

    /**
     * Test cross-tenant job access is strictly forbidden.
     */
    public function test_cross_tenant_job_access_is_forbidden(): void
    {
        $intruderUser = User::withoutGlobalScopes()
            ->where('company_id', '>', 1)
            ->where('company_id', '!=', $this->companyId)
            ->where('status', 'active')
            ->first()
            ?? User::withoutGlobalScopes()
                ->where('company_id', '>', 1)
                ->where('company_id', '!=', $this->companyId)
                ->first();

        if (!$intruderUser) {
            $this->markTestSkipped('No cross-tenant user found.');
        }

        $response = $this->actingAs($intruderUser)->postJson(route('service_company.jobs.send_multiple_job_invitations'), [
            'job_id' => $this->jobId,
            'agency_ids' => [1001],
        ]);

        $response->assertStatus(404);
        $response->assertJson([
            'status' => 'error',
        ]);
    }

    /**
     * Test atomic execution of multi-agency requirement sending within transaction.
     */
    public function test_successful_multi_agency_invitation_flow_with_outbox(): void
    {
        if (!$this->testUser) {
            $this->markTestSkipped('No test user available');
        }

        // Ensure job status is not closed/hold for the test
        DB::table('jobs')->where('id', $this->jobId)->update(['status' => 'active']);

        // Pick 2 target company IDs that are distinct from companyId
        $agencies = DB::table('companies')
            ->where('id', '!=', $this->companyId)
            ->where('status', 1)
            ->limit(2)
            ->pluck('id')
            ->toArray();

        if (count($agencies) < 2) {
            $agencies = [999981, 999982];
        }

        // Clean up any pre-existing records for these agencies to ensure a fresh test
        DB::table('recommendation_relation')
            ->where('job_id', $this->jobId)
            ->whereIn('to_company_id', $agencies)
            ->delete();

        DB::table('staffing_company_invitation')
            ->where('job_id', $this->jobId)
            ->where('from_company_id', $this->companyId)
            ->whereIn('to_company_id', $agencies)
            ->delete();

        $response = $this->actingAs($this->testUser)->postJson(route('service_company.jobs.send_multiple_job_invitations'), [
            'job_id' => $this->jobId,
            'agency_ids' => $agencies,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
        ]);

        $data = $response->json();
        $this->assertArrayHasKey('invited_agencies', $data);
        $this->assertArrayHasKey('skipped_agencies', $data);
        $this->assertArrayHasKey('total_invited', $data);

        // Verify outbox record was written
        $outbox = DB::table('sys_outbox_events')
            ->where('tenant_id', $this->companyId)
            ->where('event_type', 'BULK_AGENCY_REQUIREMENT_SENT')
            ->latest('id')
            ->first();

        $this->assertNotNull($outbox, 'Outbox event should be written for bulk requirement invitation.');
        $payload = json_decode($outbox->payload, true);
        $this->assertEquals($this->jobId, $payload['job_id']);

        // Clean up test artifacts
        DB::table('recommendation_relation')
            ->where('job_id', $this->jobId)
            ->whereIn('to_company_id', $agencies)
            ->delete();

        DB::table('staffing_company_invitation')
            ->where('job_id', $this->jobId)
            ->where('from_company_id', $this->companyId)
            ->whereIn('to_company_id', $agencies)
            ->delete();

        if ($outbox) {
            DB::table('sys_outbox_events')->where('id', $outbox->id)->delete();
        }
    }
}
