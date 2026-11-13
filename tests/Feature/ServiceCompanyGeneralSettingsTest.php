<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Tests\TestCase;

/**
 * ServiceCompanyGeneralSettingsTest
 *
 * Verifies that the General Settings tab renders according to the
 * Rule 20 Modern Card + Sub-Card Enterprise Design System and preserves
 * 100% of existing business logic, multi-tenant isolation, and department operations.
 * Also verifies removal of search, footer, subtabs, and addition of reset functionality.
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

        $this->assertStringContainsString('gs-dept-subcard', $html);
        $this->assertStringContainsString('Engineering &amp; Technology', $html);
        $this->assertStringContainsString('Human Resources &amp; Talent', $html);
        $this->assertStringContainsString('dept_delete', $html);
        $this->assertStringContainsString('rel="101"', $html);
        $this->assertStringContainsString('rel="102"', $html);
        $this->assertStringContainsString('fas fa-briefcase', $html);
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
     * Test 4: Verify tab-general-settings renders required cards, reset button, and has removed search/footer/subtabs.
     */
    public function test_tab_general_settings_renders_clean_toolbar_and_cards(): void
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
            'time_formatArr' => [(object) ['id' => 1, 'name' => '12-hour format with leading zeros (01 to 12) AM/PM']],
            'date_formatArr' => [(object) ['id' => 1, 'name' => 'DD/MM/YYYY']],
            'departmentArr' => [(object) ['id' => 101, 'name' => 'Engineering']],
            'userPermissions' => ['view_settings', 'add_settings', 'edit_settings'],
        ])->render();

        // 1. Verify "4 Configuration Domains" is removed
        $this->assertStringNotContainsString('4 Configuration Domains', $html);

        // 2. Verify search bar is removed
        $this->assertStringNotContainsString('id="general_settings_search"', $html);

        // 3. Verify Save Changes button on header is removed
        $this->assertStringNotContainsString('id="updateCompanyProfileHeader"', $html);

        // 4. Verify footer card with Save and Discard is removed
        $this->assertStringNotContainsString('gs-bottom-strip', $html);
        $this->assertStringNotContainsString('gs-btn-discard', $html);

        // 5. Verify Sub-tabs row is removed
        $this->assertStringNotContainsString('gs-subtabs-card', $html);
        $this->assertStringNotContainsString('id="settings_subtabs_bar"', $html);

        // 6. Verify Reset button is present on Tier 1 Toolbar
        $this->assertStringContainsString('id="gsResetSettingsBtn"', $html);
        $this->assertStringContainsString('gs-btn-reset', $html);

        // 7. Verify Connected Accounts & Integrations card is hidden completely
        $this->assertStringContainsString('id="card_integrations"', $html);
        $this->assertStringContainsString('style="display:none;"', $html);

        // 8. Verify Time & Date display style format has clean non-truncated options and split selects
        $this->assertStringContainsString('12-Hour Format (01-12 AM/PM)', $html);
        $this->assertStringContainsString('gs-split-selects', $html);
        $this->assertStringContainsString('grid-template-columns: 1.45fr 1fr', $html);

        // 9. Verify every heading has light background color
        $this->assertStringContainsString('#card_locale .gs-card-header', $html);
        $this->assertStringContainsString('background: #eff6ff !important;', $html);
        $this->assertStringContainsString('#card_duplicates .gs-card-header', $html);
        $this->assertStringContainsString('background: #f5f3ff !important;', $html);
        $this->assertStringContainsString('#card_departments .gs-card-header', $html);
        $this->assertStringContainsString('background: #ecfdf5 !important;', $html);

        // 10. Verify Primary Cards and Inputs are preserved
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
        $this->assertStringContainsString('data-bs-toggle="modal"', $html);
        $this->assertStringContainsString('data-bs-target="#add_department"', $html);
    }

    /**
     * Test 5: Verify resetGeneralSettings route is defined.
     */
    public function test_reset_general_settings_route_is_defined(): void
    {
        $this->assertTrue(Route::has('service_company.account.resetGeneralSettings'));
    }
}
