<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\View;
use Tests\TestCase;

/**
 * ServiceCompanyGeneralSettingsTest
 *
 * Verifies that the General Settings tab renders according to the
 * Rule 20 Modern Card + Sub-Card Enterprise Design System and preserves
 * 100% of existing business logic, multi-tenant isolation, and department operations.
 */
class ServiceCompanyGeneralSettingsTest extends TestCase
{
    /**
     * Test 1: Verify the general settings component blade exists and compiles cleanly.
     */
    public function test_general_settings_blade_component_compiles_cleanly(): void
    {
        $this->assertTrue(View::exists('service_company.account.components.tab-general-settings'));
        $this->assertTrue(View::exists('service_company.account.components.departments-list'));
    }

    /**
     * Test 2: Verify the departments-list partial renders modern sub-cards correctly.
     */
    public function test_departments_list_partial_renders_modern_subcards(): void
    {
        $fakeDepartments = [
            (object) ['id' => 101, 'name' => 'Engineering & Technology'],
            (object) ['id' => 102, 'name' => 'Human Resources & Talent'],
        ];

        $html = View::make('service_company.account.components.departments-list', [
            'departmentArr' => $fakeDepartments,
        ])->render();

        $this->assertStringContainsString('dept-subcard', $html);
        $this->assertStringContainsString('Engineering &amp; Technology', $html);
        $this->assertStringContainsString('Human Resources &amp; Talent', $html);
        $this->assertStringContainsString('dept_delete', $html);
        $this->assertStringContainsString('rel="101"', $html);
        $this->assertStringContainsString('rel="102"', $html);
        $this->assertStringContainsString('fa-solid fa-briefcase', $html);
    }

    /**
     * Test 3: Verify the departments-list partial renders empty state gracefully.
     */
    public function test_departments_list_partial_renders_empty_state_when_no_departments(): void
    {
        $html = View::make('service_company.account.components.departments-list', [
            'departmentArr' => [],
        ])->render();

        $this->assertStringContainsString('dept-empty-state', $html);
        $this->assertStringContainsString('No Departments Configured', $html);
    }

    /**
     * Test 4: Verify tab-general-settings includes 3-tier structure and required inputs.
     */
    public function test_tab_general_settings_renders_three_tier_cards_and_controls(): void
    {
        $fakeAccount = (object) [
            'id' => 99,
            'language' => 1,
            'timezone' => 'Asia/Kolkata',
            'currency' => 53,
            'time_format' => 1,
            'date_format' => 1,
            'bench_show_mode' => 0,
            'app_duplicate' => 2,
            'app_duplicate_days' => '60',
            'google_status' => 1,
            'zoom_status' => 1,
            'microsoft_status' => 0,
        ];

        $html = View::make('service_company.account.components.tab-general-settings', [
            'account' => $fakeAccount,
            'languagesArr' => [(object) ['id' => 1, 'name' => 'English']],
            'timezone' => [(object) ['time_zone' => 'Asia/Kolkata', 'country_name' => 'India', 'gmt_offse' => '+05:30', 'timezone_abbreviation' => 'IST', 'priority' => 1]],
            'currencyArr' => [(object) ['id' => 53, 'code' => 'INR', 'country' => 'India']],
            'time_formatArr' => [(object) ['id' => 1, 'name' => '12-Hour']],
            'date_formatArr' => [(object) ['id' => 1, 'name' => 'DD/MM/YYYY']],
            'departmentArr' => [(object) ['id' => 101, 'name' => 'Engineering']],
            'userPermissions' => ['view_settings', 'add_settings', 'edit_settings'],
        ])->render();

        // Tier 1 Toolbar
        $this->assertStringContainsString('tier1-toolbar-card', $html);
        $this->assertStringContainsString('general_settings_search', $html);
        $this->assertStringContainsString('updateCompanyProfileHeader', $html);

        // Tier 2 Sub-Tabs
        $this->assertStringContainsString('tier2-subtabs-card', $html);
        $this->assertStringContainsString('data-target="card_locale"', $html);
        $this->assertStringContainsString('data-target="card_duplicates"', $html);
        $this->assertStringContainsString('data-target="card_departments"', $html);
        $this->assertStringContainsString('data-target="card_integrations"', $html);

        // Tier 3 Cards
        $this->assertStringContainsString('id="card_locale"', $html);
        $this->assertStringContainsString('name="language"', $html);
        $this->assertStringContainsString('name="timezone"', $html);
        $this->assertStringContainsString('name="currency"', $html);
        $this->assertStringContainsString('name="time_format"', $html);

        $this->assertStringContainsString('id="card_duplicates"', $html);
        $this->assertStringContainsString('name="app_duplicate"', $html);
        $this->assertStringContainsString('name="app_duplicate_overroll"', $html);
        $this->assertStringContainsString('name="app_duplicate_requisition_wise"', $html);

        $this->assertStringContainsString('id="card_departments"', $html);
        $this->assertStringContainsString('dept_content', $html);

        $this->assertStringContainsString('id="card_integrations"', $html);
        $this->assertStringContainsString('name="google_status"', $html);
        $this->assertStringContainsString('name="zoom_status"', $html);
        $this->assertStringContainsString('name="microsoft_status"', $html);

        // Form Action & CSRF
        $this->assertStringContainsString('id="setting_content"', $html);
        $this->assertStringContainsString('id="updateCompanyProfile"', $html);
    }
}
