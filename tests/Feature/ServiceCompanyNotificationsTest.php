<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\View;
use Tests\TestCase;

/**
 * ServiceCompanyNotificationsTest
 *
 * Verifies that the Notifications & Alert Routing tab renders according to the
 * Rule 20 Modern Card + Sub-Card Enterprise Design System and preserves 100%
 * of email routing inputs, validation hooks, inline edit/save controls, and triggers.
 */
class ServiceCompanyNotificationsTest extends TestCase
{
    /**
     * Test 1: Verify the notifications blade partial exists cleanly.
     */
    public function test_notifications_blade_component_exists(): void
    {
        $this->assertTrue(View::exists('service_company.account.components.tab-notifications'));
    }

    /**
     * Test 2: Verify tab-notifications renders clean header card, 2-column channel cards with inline edit, and no redundant search/subtabs/footer.
     */
    public function test_tab_notifications_renders_three_tier_cards_and_routing_inputs(): void
    {
        $fakeAccount = (object) [
            'id' => 99,
            'company_email' => 'admin@company.com',
        ];

        $fakeNotification = [
            'Recuitment' => (object) ['email' => 'talent@company.com'],
            'Sales' => (object) ['email' => 'business@company.com'],
        ];

        $html = View::make('service_company.account.components.tab-notifications', [
            'account' => $fakeAccount,
            'notification' => $fakeNotification,
            'userPermissions' => ['view_settings', 'add_settings', 'edit_settings'],
        ])->render();

        // Tier 1 Summary Card
        $this->assertStringContainsString('tier1-toolbar-card', $html);
        $this->assertStringContainsString('Notification Email Routing', $html);

        // Verification: Removed search bar, header save button, count pill, subtabs, and footer strip
        $this->assertStringNotContainsString('notifications_search', $html);
        $this->assertStringNotContainsString('saveNotificationSettingsHeader', $html);
        $this->assertStringNotContainsString('2 Dedicated Inboxes', $html);
        $this->assertStringNotContainsString('tier2-subtabs-card', $html);
        $this->assertStringNotContainsString('notifications_subtabs_bar', $html);
        $this->assertStringNotContainsString('activity-strip-card', $html);
        $this->assertStringNotContainsString('ns-bottom-strip', $html);

        // Tier 3 Card 1: Recruitment Notifications (Email Sub-Card + Inline Edit + 8 Services Underneath)
        $this->assertStringContainsString('id="card_notif_recruitment"', $html);
        $this->assertStringContainsString('ns-inbox-subcard', $html);
        $this->assertStringContainsString('id="recuitment_email"', $html);
        $this->assertStringContainsString('value="talent@company.com"', $html);
        $this->assertStringContainsString('data-admin-fallback="admin@company.com"', $html);
        $this->assertStringContainsString('id="recuitment_email_typo_hint"', $html);
        $this->assertStringContainsString('ns-btn-edit', $html);
        $this->assertStringContainsString('ns-btn-save-inline', $html);
        $this->assertStringContainsString('ns-btn-cancel-inline', $html);
        $this->assertStringContainsString('ns-service-subcard', $html);
        $this->assertStringContainsString('Candidate Pipeline &amp; Stage Moves', $html);
        $this->assertStringContainsString('Job Offers, Approvals &amp; Responses', $html);

        // Tier 3 Card 2: Sales & Commercial Notifications (Email Sub-Card + Inline Edit + 8 Services Underneath)
        $this->assertStringContainsString('id="card_notif_sales"', $html);
        $this->assertStringContainsString('id="sales_email"', $html);
        $this->assertStringContainsString('value="business@company.com"', $html);
        $this->assertStringContainsString('data-admin-fallback="admin@company.com"', $html);
        $this->assertStringContainsString('id="sales_email_typo_hint"', $html);
        $this->assertStringContainsString('Direct Client Job Requisitions', $html);
        $this->assertStringContainsString('Invoices, Payments &amp; Settlement Alerts', $html);

        // Form structure preserved
        $this->assertStringContainsString('id="notification_content"', $html);
    }

    /**
     * Test 3: Verify permission block prevents form rendering when view_settings is absent.
     */
    public function test_tab_notifications_enforces_view_settings_permission(): void
    {
        $html = View::make('service_company.account.components.tab-notifications', [
            'account' => (object) [],
            'notification' => [],
            'userPermissions' => [],
        ])->render();

        $this->assertStringContainsString('You do not have permission to view notification settings', $html);
        $this->assertStringNotContainsString('id="notification_content"', $html);
    }
}
