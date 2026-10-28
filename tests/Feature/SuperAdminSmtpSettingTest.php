<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * SuperAdminSmtpSettingTest
 *
 * Automated verification test suite for Super Admin SMTP Settings & Multi-Gateway Mail Hub.
 * Verifies:
 * 1. Authentication and Super Admin authorization boundaries.
 * 2. Complete loading of all 5 dedicated gateways (8, 9, 10, 11, 12).
 * 3. Atomic multi-gateway updates within DB::transaction().
 * 4. Password preservation when empty string/null is submitted.
 * 5. Password update when explicit new password is provided.
 * 6. Protection against gateway ID tampering outside [8, 9, 10, 11, 12].
 * 7. Port bounds and email format validation.
 * 8. Protection of operational dailyCount counter against overwrite.
 * 9. Post-commit multi-tenant cache invalidation.
 * 10. Test-email endpoint validation and rate-limiting.
 */
class SuperAdminSmtpSettingTest extends TestCase
{
    /**
     * @var array Backup of original gateway records to restore after test run.
     */
    protected array $originalGatewayRows = [];

    protected User $superAdminUser;

    protected User $regularUser;

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

        // Backup existing gateway records (IDs 8-12)
        $rows = DB::table('smtp_settings')->whereIn('id', [8, 9, 10, 11, 12])->get();
        foreach ($rows as $row) {
            $this->originalGatewayRows[$row->id] = (array) $row;
        }

        // Retrieve or create a Super Admin user
        $admin = User::where('is_superadmin', 1)->first();
        if (! $admin) {
            $admin = User::factory()->create([
                'is_superadmin' => 1,
                'email' => 'test_superadmin_'.time().'@jero.works',
            ]);
        }
        $this->superAdminUser = $admin;

        // Retrieve or create a regular user
        $regular = User::where('is_superadmin', 0)->first();
        if (! $regular) {
            $regular = User::factory()->create([
                'is_superadmin' => 0,
                'email' => 'test_regular_'.time().'@jero.works',
            ]);
        }
        $this->regularUser = $regular;
        \Illuminate\Support\Facades\RateLimiter::clear('smtp_test_admin_'.$this->superAdminUser->id);
    }

    protected function tearDown(): void
    {
        // Restore original database records
        foreach ($this->originalGatewayRows as $id => $data) {
            DB::table('smtp_settings')->where('id', $id)->update($data);
        }

        Cache::forget('smtp_settings:default');
        if (isset($this->superAdminUser)) {
            \Illuminate\Support\Facades\RateLimiter::clear('smtp_test_admin_'.$this->superAdminUser->id);
        }

        parent::tearDown();
    }

    /**
     * Test 1: Unauthenticated guests are redirected to login.
     */
    public function test_guest_cannot_access_smtp_settings(): void
    {
        $response = $this->get('/super-admin/smtp-settings');
        $response->assertRedirect('/login');
    }

    /**
     * Test 2: Non-superadmin authenticated users cannot access SMTP settings.
     */
    public function test_non_superadmin_user_cannot_access_smtp_settings(): void
    {
        $response = $this->actingAs($this->regularUser)->get('/super-admin/smtp-settings');
        $response->assertRedirect('/login');
    }

    /**
     * Test 3: Super Admin can view the page and see all 5 gateways rendered.
     * Also asserts passwords are NOT leaked in HTML value attributes.
     */
    public function test_superadmin_can_view_smtp_settings_page_with_five_gateways(): void
    {
        $response = $this->actingAs($this->superAdminUser)->get('/super-admin/smtp-settings');

        $response->assertStatus(200);
        $response->assertViewHas('smtpSetting');

        $settings = $response->viewData('smtpSetting');
        $this->assertCount(5, $settings);

        $gatewayIds = $settings->pluck('id')->sort()->values()->all();
        $this->assertEquals([8, 9, 10, 11, 12], $gatewayIds);

        // Security check: Stored password must NOT be leaked in HTML value=""
        foreach ($settings as $setting) {
            if (! empty($setting->mail_password)) {
                $response->assertDontSee('value="'.$setting->mail_password.'"', false);
            }
        }
    }

    /**
     * Test 4: Super Admin can batch update all 5 gateways atomically.
     */
    public function test_superadmin_can_update_all_five_gateways_atomically(): void
    {
        $payload = [
            'mail_driver' => 'smtp',
            'mail_host' => [
                8 => 'smtp.updated8.com',
                9 => 'smtp.updated9.com',
                10 => 'smtp.updated10.com',
                11 => 'smtp.updated11.com',
                12 => 'smtp.updated12.com',
            ],
            'mail_port' => [
                8 => 587,
                9 => 465,
                10 => 587,
                11 => 465,
                12 => 587,
            ],
            'mail_username' => [
                8 => 'user8@domain.com',
                9 => 'user9@domain.com',
                10 => 'user10@domain.com',
                11 => 'user11@domain.com',
                12 => 'user12@domain.com',
            ],
            'mail_encryption' => [
                8 => 'tls',
                9 => 'ssl',
                10 => 'tls',
                11 => 'ssl',
                12 => 'tls',
            ],
            'from_mail' => [
                8 => 'outreach@updated.com',
                9 => 'resumes@updated.com',
                10 => 'interviews@updated.com',
                11 => 'alerts@updated.com',
                12 => 'billing@updated.com',
            ],
            'mail_from_name' => [
                8 => 'JERO Mass Mailer',
                9 => 'JERO Candidate Desk',
                10 => 'JERO Calendar Bot',
                11 => 'JERO Alerts',
                12 => 'JERO Finance',
            ],
            'status' => [
                8 => 0, // Active
                9 => 0,
                10 => 1, // Inactive
                11 => 0,
                12 => 0,
            ],
        ];

        $response = $this->actingAs($this->superAdminUser)
            ->putJson(route('superadmin.smtp-settings.update', 8), $payload);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);

        // Assert database persistence and legacy column synchronization
        $g8 = DB::table('smtp_settings')->where('id', 8)->first();
        $this->assertEquals('smtp.updated8.com', $g8->mail_host);
        $this->assertEquals('smtp.updated8.com', $g8->notification_mail_host);
        $this->assertEquals(587, $g8->mail_port);
        $this->assertEquals('outreach@updated.com', $g8->mail_from_email);
        $this->assertEquals('outreach@updated.com', $g8->email_id);
        $this->assertEquals(0, (int) $g8->status);

        $g10 = DB::table('smtp_settings')->where('id', 10)->first();
        $this->assertEquals(1, (int) $g10->status);
    }

    /**
     * Test 5: Submitting empty password retains original password without wiping.
     */
    public function test_empty_password_retains_existing_password(): void
    {
        $existing = DB::table('smtp_settings')->where('id', 8)->first();
        $this->assertNotEmpty($existing->mail_password);

        $payload = [
            'mail_driver' => 'smtp',
            'mail_host' => [8 => 'smtp.retaintest.com'],
            'mail_port' => [8 => 465],
            'mail_username' => [8 => 'user@retain.com'],
            'mail_password' => [8 => ''], // Blank string: MUST NOT overwrite existing
            'from_mail' => [8 => 'test@retain.com'],
            'status' => [8 => 0],
        ];

        $response = $this->actingAs($this->superAdminUser)
            ->putJson(route('superadmin.smtp-settings.update', 8), $payload);

        $response->assertStatus(200);

        $refreshed = DB::table('smtp_settings')->where('id', 8)->first();
        $this->assertEquals($existing->mail_password, $refreshed->mail_password);
        $this->assertEquals('smtp.retaintest.com', $refreshed->mail_host);
    }

    /**
     * Test 6: Providing an explicit new password updates the database.
     */
    public function test_provided_password_is_updated(): void
    {
        $payload = [
            'mail_driver' => 'smtp',
            'mail_host' => [8 => 'smtp.newpass.com'],
            'mail_port' => [8 => 465],
            'mail_username' => [8 => 'user@newpass.com'],
            'mail_password' => [8 => 'SuperSecretNewKey2026!'],
            'from_mail' => [8 => 'test@newpass.com'],
            'status' => [8 => 0],
        ];

        $response = $this->actingAs($this->superAdminUser)
            ->putJson(route('superadmin.smtp-settings.update', 8), $payload);

        $response->assertStatus(200);

        $refreshed = DB::table('smtp_settings')->where('id', 8)->first();
        $this->assertEquals('SuperSecretNewKey2026!', $refreshed->mail_password);
    }

    /**
     * Test 7: Gateway ID tampering outside [8, 9, 10, 11, 12] is rejected by validation.
     */
    public function test_gateway_id_tampering_is_rejected(): void
    {
        $payload = [
            'mail_driver' => 'smtp',
            'mail_host' => [99 => 'evil.smtp.com'], // Invalid gateway ID
            'mail_port' => [99 => 465],
            'mail_username' => [99 => 'attacker'],
            'from_mail' => [99 => 'hacker@evil.com'],
        ];

        $response = $this->actingAs($this->superAdminUser)
            ->putJson(route('superadmin.smtp-settings.update', 8), $payload);

        $response->assertStatus(422);
    }

    /**
     * Test 8: Out of bounds ports are rejected by validation.
     */
    public function test_invalid_port_bounds_rejected(): void
    {
        $payload = [
            'mail_driver' => 'smtp',
            'mail_host' => [8 => 'smtp.valid.com'],
            'mail_port' => [8 => 75000], // Out of port range (max 65535)
            'mail_username' => [8 => 'valid_user'],
            'from_mail' => [8 => 'valid@valid.com'],
        ];

        $response = $this->actingAs($this->superAdminUser)
            ->putJson(route('superadmin.smtp-settings.update', 8), $payload);

        $response->assertStatus(422);
    }

    /**
     * Test 9: Malformed email format is rejected by validation.
     */
    public function test_invalid_from_email_rejected(): void
    {
        $payload = [
            'mail_driver' => 'smtp',
            'mail_host' => [8 => 'smtp.valid.com'],
            'mail_port' => [8 => 465],
            'mail_username' => [8 => 'valid_user'],
            'from_mail' => [8 => 'this-is-not-an-email'],
        ];

        $response = $this->actingAs($this->superAdminUser)
            ->putJson(route('superadmin.smtp-settings.update', 8), $payload);

        $response->assertStatus(422);
    }

    /**
     * Test 10: Live dailyCount counter is protected and never overwritten during configuration save.
     */
    public function test_daily_counter_is_protected_from_overwrite(): void
    {
        // Set Gateway 8 dailyCount to a known value
        DB::table('smtp_settings')->where('id', 8)->update(['dailyCount' => 88]);

        $payload = [
            'mail_driver' => 'smtp',
            'mail_host' => [8 => 'smtp.countertest.com'],
            'mail_port' => [8 => 465],
            'mail_username' => [8 => 'user@countertest.com'],
            'from_mail' => [8 => 'test@countertest.com'],
            'status' => [8 => 0],
            'daily_used' => [8 => 0], // Untrusted submission attempt
        ];

        $response = $this->actingAs($this->superAdminUser)
            ->putJson(route('superadmin.smtp-settings.update', 8), $payload);

        $response->assertStatus(200);

        $refreshed = DB::table('smtp_settings')->where('id', 8)->first();
        $this->assertEquals(88, (int) $refreshed->dailyCount);
    }

    /**
     * Test 11: Post-commit cache invalidation clears smtp_settings:default.
     */
    public function test_post_commit_cache_invalidation_executes(): void
    {
        Cache::put('smtp_settings:default', 'cached_dummy_setting', 3600);
        $this->assertTrue(Cache::has('smtp_settings:default'));

        $payload = [
            'mail_driver' => 'smtp',
            'mail_host' => [8 => 'smtp.cacheinvalidation.com'],
            'mail_port' => [8 => 465],
            'mail_username' => [8 => 'user@cache.com'],
            'from_mail' => [8 => 'test@cache.com'],
            'status' => [8 => 0],
        ];

        $response = $this->actingAs($this->superAdminUser)
            ->putJson(route('superadmin.smtp-settings.update', 8), $payload);

        $response->assertStatus(200);
        $this->assertFalse(Cache::has('smtp_settings:default'));
    }

    /**
     * Test 12: Test-email endpoint validates gateway and recipient email format.
     */
    public function test_test_email_requires_valid_gateway_and_recipient(): void
    {
        // Invalid email format
        $response = $this->actingAs($this->superAdminUser)
            ->postJson(route('superadmin.smtp-settings.sendTestEmail'), [
                'gateway_id' => 8,
                'test_email' => 'invalid-recipient',
            ]);
        $response->assertStatus(422);

        // Invalid gateway ID
        $response2 = $this->actingAs($this->superAdminUser)
            ->postJson(route('superadmin.smtp-settings.sendTestEmail'), [
                'gateway_id' => 999,
                'test_email' => 'valid@jero.works',
            ]);
        $response2->assertStatus(422);
    }

    /**
     * Test 13: Test-email endpoint is rate limited to 5 attempts per minute.
     */
    public function test_test_email_rate_limiting(): void
    {
        $rateLimitKey = 'smtp_test_admin_'.$this->superAdminUser->id;
        \Illuminate\Support\Facades\RateLimiter::clear($rateLimitKey);

        // Pre-hit rate limiter 5 times
        for ($i = 0; $i < 5; $i++) {
            \Illuminate\Support\Facades\RateLimiter::hit($rateLimitKey, 60);
        }

        $payload = [
            'gateway_id' => 8,
            'test_email' => 'ratelimit@jero.works',
        ];

        // 6th attempt must be throttled with HTTP 429
        $response = $this->actingAs($this->superAdminUser)
            ->postJson(route('superadmin.smtp-settings.sendTestEmail'), $payload);

        $response->assertStatus(429);
        $response->assertJson(['status' => 'fail']);
    }
}
