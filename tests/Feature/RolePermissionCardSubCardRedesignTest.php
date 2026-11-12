<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\View;
use Tests\TestCase;

/**
 * RolePermissionCardSubCardRedesignTest
 *
 * Intent: Verifies that the Service Company Roles & Permissions partials adhere strictly
 * to the Rule 20 Modern Card + Sub-Card Enterprise Design System and Client-Tracker-inspired
 * interactive KPI strip, including real user initials, domain coverage sub-cards,
 * debounced search toolbar, and single-active primary blue sub-tabs.
 */
class RolePermissionCardSubCardRedesignTest extends TestCase
{
    public function test_role_stat_cards_render_client_tracker_inspired_six_kpi_grid(): void
    {
        $html = View::make('service_company.role-permission.components.role_stat_cards', [
            'totalRolesCount' => 8,
            'systemRolesCount' => 6,
            'customRolesCount' => 2,
            'highAccessCount' => 3,
            'totalAssignedUsers' => 14,
            'staffedRolesCount' => 5,
            'unassignedRolesCount' => 3,
            'totalPermGroups' => 152,
            'uniquePermGroupsCount' => 19,
            'groupTotals' => array_fill(0, 19, 4),
        ])->render();

        $this->assertStringContainsString('rp-kpi-grid', $html);
        $this->assertStringContainsString('data-kpi-filter="all"', $html);
        $this->assertStringContainsString('data-kpi-filter="system"', $html);
        $this->assertStringContainsString('data-kpi-filter="custom"', $html);
        $this->assertStringContainsString('data-kpi-filter="high_access"', $html);
        $this->assertStringContainsString('data-kpi-filter="staffed"', $html);
        $this->assertStringContainsString('data-kpi-filter="unassigned"', $html);
        $this->assertStringContainsString('Total Roles', $html);
        $this->assertStringContainsString('High Access', $html);
        $this->assertStringContainsString('Permission Groups', $html);
        $this->assertStringContainsString('2 custom', $html);
    }

    public function test_role_filter_bar_renders_rule_20_tier_1_toolbar_and_tier_2_subtabs(): void
    {
        $html = View::make('service_company.role-permission.components.role_filter_bar', [
            'totalRolesCount' => 8,
            'systemRolesCount' => 6,
            'customRolesCount' => 2,
            'highAccessCount' => 3,
            'staffedRolesCount' => 5,
            'unassignedRolesCount' => 3,
        ])->render();

        $this->assertStringContainsString('rp-toolbar-card', $html);
        $this->assertStringContainsString('rp-search-subcard', $html);
        $this->assertStringContainsString('id="roleSearch"', $html);
        $this->assertStringContainsString('id="btnRoleSearchClear"', $html);
        $this->assertStringContainsString('rp-tabs-card', $html);
        $this->assertStringContainsString('js-rp-tab active', $html);
        $this->assertStringContainsString('id="rpVisibleCount"', $html);
        $this->assertStringContainsString('RBAC Active', $html);
    }

    public function test_role_card_item_renders_five_discrete_subcards_audit_strip_and_real_initials(): void
    {
        $role = (object) [
            'id' => 12,
            'name' => 'senior_recruiter',
            'display_name' => 'Senior Recruiter',
            'priority' => 2,
            'manual_add' => 1,
            'created_at' => '2026-08-01 10:00:00',
            'updated_at' => '2026-11-10 15:30:00',
            'grouped_permissions' => collect([
                'Jobs' => collect(['view_jobs', 'add_jobs']),
                'Job Applications' => collect(['view_job_applications']),
                'Offer' => collect(['view_offer']),
                'Joining' => collect(['view_joining']),
                'Clients' => collect(['view_clients']),
            ]),
        ];

        $allRoleStats = [
            12 => [
                'userCount' => 2,
                'users' => collect([
                    (object) ['id' => 1, 'name' => 'Vibhanshu Jaiswal', 'email' => 'vj@example.com'],
                    (object) ['id' => 2, 'name' => 'Aarav Sharma', 'email' => 'as@example.com'],
                ]),
                'grpCount' => 5,
                'totalAssignedPerms' => 6,
                'coverage' => [
                    'Recruitment' => ['assigned' => 5, 'total' => 20, 'pct' => 25, 'color' => '#27ae60'],
                    'Talent Marketplace' => ['assigned' => 0, 'total' => 8, 'pct' => 0, 'color' => '#1a73e8'],
                    'Sales & CRM' => ['assigned' => 1, 'total' => 10, 'pct' => 10, 'color' => '#5e72e4'],
                    'Admin & Security' => ['assigned' => 0, 'total' => 12, 'pct' => 0, 'color' => '#c0392b'],
                ],
                'access' => ['label' => 'High Access', 'class' => 'badge-high'],
                'detailJson' => json_encode(['id' => 12, 'name' => 'Senior Recruiter']),
            ],
        ];

        $html = View::make('service_company.role-permission.components.role_card_item', [
            'rlist' => $role,
            'loop' => (object) ['index' => 0],
            'allRoleStats' => $allRoleStats,
            'totalPermissions' => 50,
            'cardPalette' => [
                ['bg' => '#eff6ff', 'stroke' => '#2563eb', 'icon' => 'shield-check'],
            ],
            'avColors' => ['#2563eb', '#4f46e5'],
        ])->render();

        $this->assertStringContainsString('rp-primary-card is-staffed', $html);
        $this->assertStringContainsString('rp-subcard-identity', $html);
        $this->assertStringContainsString('rp-subcard-coverage', $html);
        $this->assertStringContainsString('rp-subcard-modules', $html);
        $this->assertStringContainsString('rp-subcard-members', $html);
        $this->assertStringContainsString('rp-subcard-governance', $html);
        $this->assertStringContainsString('rp-entity-bottom', $html);
        $this->assertStringContainsString('#CUS-12', $html);
        $this->assertStringContainsString('VJ', $html);
        $this->assertStringContainsString('AS', $html);
        $this->assertStringContainsString('Vibhanshu Jaiswal', $html);
        $this->assertStringContainsString('role_delete', $html);
        $this->assertStringNotContainsString('<table', $html);
    }
}
