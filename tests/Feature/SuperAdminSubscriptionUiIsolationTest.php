<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SuperAdminSubscriptionUiIsolationTest extends TestCase
{
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

    /**
     * Verify root container and CSS scoping on super-admin subscription blade.
     */
    public function test_subscription_index_view_contains_isolated_root_and_scoped_css(): void
    {
        $viewContent = file_get_contents(resource_path('views/super-admin/subscription/index.blade.php'));

        // Assert root container exists
        $this->assertStringContainsString('<div class="superadmin-subscription-page">', $viewContent);

        // Assert select-all checkbox is removed from table header
        $this->assertStringNotContainsString('id="checkAllCompanies"', $viewContent);
        $this->assertStringNotContainsString('selectedCountBadge', $viewContent);

        // Assert first header is Company Name with ps-4
        $this->assertStringContainsString('<th class="min-w-220px ps-4">Company Name</th>', $viewContent);

        // Assert scoped CSS contains 48px company-logo and company-avatar rules
        $this->assertStringContainsString('.superadmin-subscription-page .company-logo', $viewContent);
        $this->assertStringContainsString('width: 48px !important;', $viewContent);
        $this->assertStringContainsString('height: 48px !important;', $viewContent);
        $this->assertStringContainsString('object-fit: contain !important;', $viewContent);
        $this->assertStringContainsString('.superadmin-subscription-page .company-avatar', $viewContent);

        // Assert top space management rules exist
        $this->assertStringContainsString('margin-top: -48px;', $viewContent);
        $this->assertStringContainsString('#kt_header {', $viewContent);
        $this->assertStringContainsString('border-bottom: 1px solid #eff2f5 !important;', $viewContent);
        $this->assertStringContainsString('px-4 pt-2 pb-4', $viewContent);

        // Assert JS is scoped to rootSelector and does not listen globally on row-select-checkbox
        $this->assertStringContainsString("const rootSelector = '.superadmin-subscription-page';", $viewContent);
        $this->assertStringNotContainsString("$(document).on('change', '.row-select-checkbox'", $viewContent);
        $this->assertStringNotContainsString("$('#checkAllCompanies').on('change'", $viewContent);
        $this->assertStringContainsString('$row.find(\'.status-cell\')', $viewContent);
    }

    /**
     * Verify company subscription ajax partial does not contain checkboxes and uses standardized logos.
     */
    public function test_subscription_ajax_partial_has_standardized_logos_and_no_checkboxes(): void
    {
        $ajaxViewContent = file_get_contents(resource_path('views/super-admin/ajax_pages/company_subscription_ajax.blade.php'));

        // Assert row checkboxes are removed
        $this->assertStringNotContainsString('row-select-checkbox', $ajaxViewContent);
        $this->assertStringNotContainsString('<input class="form-check-input', $ajaxViewContent);

        // Assert company logo container uses .company-logo and .company-logo-img
        $this->assertStringContainsString('class="company-logo me-3 show-detail cursor-pointer"', $ajaxViewContent);
        $this->assertStringContainsString('class="company-logo-img show-detail cursor-pointer"', $ajaxViewContent);

        // Assert fallback initial avatar uses .company-avatar
        $this->assertStringContainsString('class="company-avatar symbol symbol-circle me-3 show-detail cursor-pointer"', $ajaxViewContent);

        // Assert status column has status-cell class
        $this->assertStringContainsString('<td class="status-cell">', $ajaxViewContent);

        // Assert empty state colspan is adjusted to 10
        $this->assertStringContainsString('<td colspan="10" class="text-center py-10 text-muted fs-6">', $ajaxViewContent);
    }

    /**
     * Verify company subscription ajax view renders without exceptions.
     */
    public function test_subscription_ajax_view_renders_cleanly(): void
    {
        $sampleData = [
            [
                'id' => 99991,
                'company_name' => 'InnovateSoft Technologies',
                'company_type' => 1,
                'logo' => 'https://via.placeholder.com/150',
                'status' => 'active',
                'created_at' => '2026-09-17 10:00:00',
                'last_login' => '2026-09-17 12:00:00',
                'company_coins' => [
                    [
                        'event_type' => 'operational',
                        'package_name' => 'Silver',
                        'coins' => 5087,
                        'end_date' => '2027-03-16',
                    ],
                    [
                        'event_type' => 'sales',
                        'package_name' => 'Free',
                        'coins' => 1569,
                        'end_date' => '2026-10-17',
                    ],
                ],
                'locations_address' => [
                    [
                        'locations_address' => [
                            [
                                'city_name' => 'Noida',
                                'state_name' => 'Uttar Pradesh',
                                'country_name' => 'India',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'id' => 99992,
                'company_name' => 'TechNova Solutions Pvt. Ltd.',
                'company_type' => 1,
                'logo' => '',
                'status' => 'active',
                'created_at' => '2026-09-17 10:00:00',
                'last_login' => '2026-09-17 12:00:00',
                'company_coins' => [
                    [
                        'event_type' => 'operational',
                        'package_name' => 'Free',
                        'coins' => 100,
                        'end_date' => '2026-10-17',
                    ],
                    [
                        'event_type' => 'sales',
                        'package_name' => 'Free',
                        'coins' => 1995,
                        'end_date' => '2026-10-17',
                    ],
                ],
            ],
        ];

        $rendered = view('super-admin.ajax_pages.company_subscription_ajax', [
            'data' => $sampleData,
            'userPermissions' => [],
            'coinEventType' => 'operational',
        ])->render();

        // Must not contain any checkboxes
        $this->assertStringNotContainsString('type="checkbox"', $rendered);
        $this->assertStringNotContainsString('row-select-checkbox', $rendered);

        // Must contain InnovateSoft with company-logo container
        $this->assertStringContainsString('InnovateSoft Technologies', $rendered);
        $this->assertStringContainsString('class="company-logo me-3 show-detail cursor-pointer"', $rendered);

        // Must contain TechNova with initial avatar "T"
        $this->assertStringContainsString('TechNova Solutions Pvt. Ltd.', $rendered);
        $this->assertStringContainsString('class="company-avatar symbol symbol-circle me-3 show-detail cursor-pointer"', $rendered);
        $this->assertStringContainsString('T', $rendered);

        // Must contain status-cell
        $this->assertStringContainsString('class="status-cell"', $rendered);

        // Must NOT contain Edit Wallet option
        $this->assertStringNotContainsString('Edit Wallet', $rendered);
        $this->assertStringNotContainsString('action-edit-wallet', $rendered);

        // Must contain Edit Company link with encrypted company ID (not plaintext ID)
        $this->assertStringContainsString('Edit Company', $rendered);
        $this->assertStringNotContainsString('company/edit/99991', $rendered);
        $this->assertStringNotContainsString('company/edit/99992', $rendered);

        // Verify that the link in $rendered contains a valid decryptable token
        preg_match('/href="[^"]*company\/edit\/([^"]+)"/', $rendered, $matches);
        $this->assertNotEmpty($matches);
        $encryptedToken = $matches[1];
        $decryptedId = \Illuminate\Support\Facades\Crypt::decryptString($encryptedToken);
        $this->assertEquals(99991, $decryptedId);
    }

    /**
     * Verify pagination section is clean without page size dropdown and duplicate info is hidden.
     */
    public function test_subscription_index_view_has_clean_pagination_without_pagesize_dropdown(): void
    {
        $viewContent = file_get_contents(resource_path('views/super-admin/subscription/index.blade.php'));

        // Assert page size dropdown is completely removed
        $this->assertStringNotContainsString('id="pageSizeSelect"', $viewContent);
        $this->assertStringNotContainsString('per page', $viewContent);

        // Assert authoritative single summary exists
        $this->assertStringContainsString('id="subscriptionSummary"', $viewContent);
        $this->assertStringContainsString('id="paginationsubscription"', $viewContent);

        // Assert scoped CSS hides duplicate pagination-info inside #paginationsubscription
        $this->assertStringContainsString('.superadmin-subscription-page #paginationsubscription .pagination-info', $viewContent);
        $this->assertStringContainsString('display: none !important;', $viewContent);

        // Assert fixed pageSize = 10 is maintained in SubscriptionState
        $this->assertStringContainsString('SubscriptionState.pageSize = 10;', $viewContent);
        $this->assertStringNotContainsString('$root.find(\'#pageSizeSelect\').on(\'change\'', $viewContent);
    }
}
