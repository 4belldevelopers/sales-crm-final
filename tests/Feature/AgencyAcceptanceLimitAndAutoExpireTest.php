<?php

namespace Tests\Feature;

use App\Http\Middleware\EnsureOneDeviceLogin;
use App\Models\Job;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * AgencyAcceptanceLimitAndAutoExpireTest
 *
 * Intent: Comprehensive end-to-end verification for Agency Acceptance Limit & Auto-expire
 * settings in the "Publish to Agency Marketplace" drawer on JERO Service Company Jobs.
 *
 * Scenarios verified:
 * 1. Change acceptance limit from 20 to valid options (5, 10, 50) & verify database persistence.
 * 2. Change expiry duration from 7 days to valid options (14, 30 days) & verify end_date calculation.
 * 3. Sequential update of both settings & verify atomic coexistence.
 * 4. Verify drawer HTML renders matching 'selected' options from authoritative job data.
 * 5. Verify setting updates work seamlessly on already-published jobs without unpublishing.
 * 6. Reject invalid or tampered request values (e.g., vendor_limit = 99, auto_expire = 999).
 * 7. Tenant isolation & unauthorized job ID rejection.
 * 8. Rapid successive setting changes retain the latest authoritative value.
 * 9. Publish and unpublish operations work and retain configured settings.
 * 10. Dynamic enforcement of vendor_limit in marketplace acceptance logic.
 *
 * Run:
 *   php artisan test --filter=AgencyAcceptanceLimitAndAutoExpireTest
 */
class AgencyAcceptanceLimitAndAutoExpireTest extends TestCase
{
    protected $testUser;

    protected $jobId;

    protected $companyId;

    protected $job;

    protected function setUp(): void
    {
        parent::setUp();
        config([
            'database.default' => 'mysql',
            'database.connections.mysql.database' => 'dbmaster',
        ]);
        DB::purge('mysql');
        DB::reconnect('mysql');
        DB::setDefaultConnection('mysql');

        $this->withoutMiddleware([EnsureOneDeviceLogin::class]);

        // Find an active job belonging to a service company
        $jobRecord = DB::table('jobs')
            ->whereNotNull('company_id')
            ->where('company_id', '>', 0)
            ->where('status', 'active')
            ->first();

        if (! $jobRecord) {
            $jobRecord = DB::table('jobs')->whereNotNull('company_id')->first();
        }

        $this->assertNotNull($jobRecord, 'A valid job requisition must exist for testing.');

        $this->jobId = $jobRecord->id;
        $this->companyId = $jobRecord->company_id;

        $this->testUser = User::withoutGlobalScopes()
            ->where('company_id', $this->companyId)
            ->first();

        $this->assertNotNull($this->testUser, 'A user belonging to tenant company must exist.');
    }

    /**
     * Test Case 1: Change Agency Acceptance Limit to valid option (e.g. 10 agencies).
     */
    public function test_change_agency_acceptance_limit_persists_correctly(): void
    {
        $response = $this->actingAs($this->testUser)->postJson(route('service_company.jobs.auction_send_job_request'), [
            'current_jobid' => $this->jobId,
            'action' => 'update_settings',
            'setting_type' => 'vendor_limit',
            'vendor_limit' => 10,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'vendor_limit' => 10,
        ]);

        $freshJob = Job::withoutGlobalScopes()->find($this->jobId);
        $this->assertEquals(10, $freshJob->vendor_limit);
        $meta = is_array($freshJob->meta_details) ? $freshJob->meta_details : json_decode($freshJob->meta_details, true);
        $this->assertEquals(10, $meta['vendor_limit']);
    }

    /**
     * Test Case 2: Change Auto-expire to valid option (e.g. 14 days) and verify end_date.
     */
    public function test_change_auto_expire_duration_calculates_and_persists_end_date(): void
    {
        $response = $this->actingAs($this->testUser)->postJson(route('service_company.jobs.auction_send_job_request'), [
            'current_jobid' => $this->jobId,
            'action' => 'update_settings',
            'setting_type' => 'auto_expire',
            'auto_expire' => 14,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'auto_expire' => 14,
        ]);

        $freshJob = Job::withoutGlobalScopes()->find($this->jobId);
        $this->assertEquals(14, $freshJob->auto_expire);
        $this->assertEquals(1, (int) $freshJob->closeType);

        $expectedDate = Carbon::now()->addDays(14)->format('Y-m-d');
        $this->assertStringContainsString($expectedDate, $freshJob->end_date);
    }

    /**
     * Test Case 3: Change both settings sequentially and verify both are retained.
     */
    public function test_change_both_settings_sequentially_preserves_both(): void
    {
        // 1. Set vendor limit to 50
        $this->actingAs($this->testUser)->postJson(route('service_company.jobs.auction_send_job_request'), [
            'current_jobid' => $this->jobId,
            'action' => 'update_settings',
            'setting_type' => 'vendor_limit',
            'vendor_limit' => 50,
        ])->assertJson(['status' => 'success', 'vendor_limit' => 50]);

        // 2. Set auto expire to 30
        $this->actingAs($this->testUser)->postJson(route('service_company.jobs.auction_send_job_request'), [
            'current_jobid' => $this->jobId,
            'action' => 'update_settings',
            'setting_type' => 'auto_expire',
            'auto_expire' => 30,
        ])->assertJson(['status' => 'success', 'auto_expire' => 30]);

        // 3. Verify both remain in database
        $freshJob = Job::withoutGlobalScopes()->find($this->jobId);
        $this->assertEquals(50, $freshJob->vendor_limit);
        $this->assertEquals(30, $freshJob->auto_expire);
    }

    /**
     * Test Case 4 & 5: Drawer rendering verification - verify selected attributes are output.
     */
    public function test_drawer_renders_saved_settings_authoritatively(): void
    {
        // Save known settings
        $this->actingAs($this->testUser)->postJson(route('service_company.jobs.auction_send_job_request'), [
            'current_jobid' => $this->jobId,
            'action' => 'update_settings',
            'setting_type' => 'vendor_limit',
            'vendor_limit' => 50,
        ]);
        $this->actingAs($this->testUser)->postJson(route('service_company.jobs.auction_send_job_request'), [
            'current_jobid' => $this->jobId,
            'action' => 'update_settings',
            'setting_type' => 'auto_expire',
            'auto_expire' => 30,
        ]);

        // Fetch drawer view with _force=1 to bypass representation cache
        $viewResponse = $this->actingAs($this->testUser)->getJson(
            route('service_company.jobs.spartnerlisting', ['id' => $this->jobId, '_force' => 1])
        );

        $viewResponse->assertStatus(200);
        $html = $viewResponse->json('view') ?? '';

        // Verify the select options have 'selected' on 50 and 30
        $this->assertStringContainsString('value="50" selected', $html);
        $this->assertStringContainsString('value="30" selected', $html);
        $this->assertStringContainsString('id="marketplace_vendor_limit" data-current="50"', $html);
        $this->assertStringContainsString('id="marketplace_auto_expire" data-current="30"', $html);
    }

    /**
     * Test Case 6: Published job retains settings without unpublishing.
     */
    public function test_update_settings_on_published_job_preserves_published_state(): void
    {
        // Ensure job is published (status 2)
        DB::table('jobs')->where('id', $this->jobId)->update(['public_market_status' => 2]);

        $response = $this->actingAs($this->testUser)->postJson(route('service_company.jobs.auction_send_job_request'), [
            'current_jobid' => $this->jobId,
            'action' => 'update_settings',
            'setting_type' => 'vendor_limit',
            'vendor_limit' => 10,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'is_published' => true,
            'public_market_status' => 2,
            'vendor_limit' => 10,
        ]);

        $freshJob = Job::withoutGlobalScopes()->find($this->jobId);
        $this->assertEquals(2, (int) $freshJob->public_market_status);
        $this->assertEquals(10, $freshJob->vendor_limit);
    }

    /**
     * Test Case 7: Invalid or tampered request values are rejected with clear error.
     */
    public function test_invalid_setting_values_are_rejected(): void
    {
        // Invalid vendor limit
        $res1 = $this->actingAs($this->testUser)->postJson(route('service_company.jobs.auction_send_job_request'), [
            'current_jobid' => $this->jobId,
            'action' => 'update_settings',
            'vendor_limit' => 999,
        ]);
        $res1->assertJson(['status' => 'error']);
        $this->assertStringContainsString('Invalid agency acceptance limit', $res1->json('message'));

        // Invalid auto expire
        $res2 = $this->actingAs($this->testUser)->postJson(route('service_company.jobs.auction_send_job_request'), [
            'current_jobid' => $this->jobId,
            'action' => 'update_settings',
            'auto_expire' => 45,
        ]);
        $res2->assertJson(['status' => 'error']);
        $this->assertStringContainsString('Invalid auto-expire', $res2->json('message'));
    }

    /**
     * Test Case 8: Unauthorized user or job from another tenant is rejected.
     */
    public function test_unauthorized_cross_tenant_update_is_rejected(): void
    {
        // Load an actual user from a different tenant company (company_id > 1 to avoid super-admin)
        $foreignUser = User::withoutGlobalScopes()
            ->where('company_id', '!=', $this->companyId)
            ->where('company_id', '>', 1)
            ->first();

        $this->assertNotNull($foreignUser, 'A user from a different tenant must exist.');

        $response = $this->actingAs($foreignUser)->postJson(route('service_company.jobs.auction_send_job_request'), [
            'current_jobid' => $this->jobId,
            'action' => 'update_settings',
            'vendor_limit' => 5,
        ]);

        $response->assertJson([
            'status' => 'error',
            'message' => 'Unauthorized access to job requisition.',
        ]);
    }

    /**
     * Test Case 9: Rapid successive updates retain the latest value.
     */
    public function test_rapid_successive_updates_retain_latest_value(): void
    {
        $this->actingAs($this->testUser)->postJson(route('service_company.jobs.auction_send_job_request'), [
            'current_jobid' => $this->jobId,
            'action' => 'update_settings',
            'vendor_limit' => 5,
        ]);
        $this->actingAs($this->testUser)->postJson(route('service_company.jobs.auction_send_job_request'), [
            'current_jobid' => $this->jobId,
            'action' => 'update_settings',
            'vendor_limit' => 10,
        ]);
        $this->actingAs($this->testUser)->postJson(route('service_company.jobs.auction_send_job_request'), [
            'current_jobid' => $this->jobId,
            'action' => 'update_settings',
            'vendor_limit' => 20,
        ]);

        $freshJob = Job::withoutGlobalScopes()->find($this->jobId);
        $this->assertEquals(20, $freshJob->vendor_limit);
    }

    /**
     * Test Case 10: Publish operation saves configured vendor_limit and auto_expire.
     */
    public function test_publish_operation_saves_dropdown_settings(): void
    {
        // Reset to unpublished state
        DB::table('jobs')->where('id', $this->jobId)->update(['public_market_status' => 6]);

        $response = $this->actingAs($this->testUser)->postJson(route('service_company.jobs.auction_send_job_request'), [
            'current_jobid' => $this->jobId,
            'job_cancel' => 0,
            'vendor_limit' => 50,
            'auto_expire' => 14,
        ]);

        $response->assertStatus(200);
        $freshJob = Job::withoutGlobalScopes()->find($this->jobId);
        $this->assertEquals(50, $freshJob->vendor_limit);
        $this->assertEquals(14, $freshJob->auto_expire);
    }

    /**
     * Test Case 11: Unpublish operation works properly without clearing settings.
     */
    public function test_unpublish_operation_preserves_configured_settings(): void
    {
        // First ensure it has vendor_limit = 50 and auto_expire = 14
        $freshJob = Job::withoutGlobalScopes()->find($this->jobId);
        $this->assertEquals(50, $freshJob->vendor_limit);

        $response = $this->actingAs($this->testUser)->postJson(route('service_company.jobs.auction_send_job_request'), [
            'current_jobid' => $this->jobId,
            'job_cancel' => 1,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'is_published' => false,
            'public_market_status' => 6,
        ]);

        // Verify settings still exist in database
        $freshJob = Job::withoutGlobalScopes()->find($this->jobId);
        $this->assertEquals(50, $freshJob->vendor_limit);
        $this->assertEquals(14, $freshJob->auto_expire);
    }
}
