<?php

namespace Tests\Feature;

use App\Http\Controllers\ServiceCompany\ServiceCompanyAccountSettingsController;
use App\Models\User;
use App\Services\AccountSettingsService;
use App\Services\ElasticService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Tests\TestCase;

/**
 * ServiceCompanyInterviewStagesTest
 *
 * Verifies that the Interview Pipeline Builder tab renders according to the
 * Rule 20 Modern Card + Sub-Card Enterprise Design System and satisfies all user requirements:
 * 1. 7 Master Stages card removed
 * 2. Live ATS Pipeline badge removed
 * 3. 13 Sub-Stages Defined text removed
 * 4. Search bar completely removed
 * 5. Add substage directly from the header
 * 6. Instant AJAX Sync bottom notice removed
 * 7. Joined/Rejected and all sub-stages use "Stage X" numbering instead of "Step X"
 * 8. Sub-stages are draggable for moving/reordering
 * 9. "+ Add" / "+ Add Sub-Stage" buttons removed from every stage card
 * 10. Header button has dynamic text "Add Substage"
 * 11. Stage headers use stage background color (var(--stage-light))
 * 12. Reset button present on Tier 1 toolbar and resets pipeline to system defaults
 */
class ServiceCompanyInterviewStagesTest extends TestCase
{
    /**
     * Test 1: Verify the interview stages blade partial exists cleanly.
     */
    public function test_interview_stages_blade_component_exists(): void
    {
        $this->assertTrue(View::exists('service_company.account.components.tab-interview-stages'));
    }

    /**
     * Test 2: Verify all user-requested requirements are met on render, including Reset button.
     */
    public function test_tab_interview_stages_satisfies_all_user_requirements(): void
    {
        $joinedStages = [
            (object) [
                'id' => 103,
                'name' => 'Onboarded',
                'parent' => 6,
                'priority' => 0,
                'delete_status' => 0,
            ],
            (object) [
                'id' => 104,
                'name' => 'Backed Out',
                'parent' => 6,
                'priority' => 1,
                'delete_status' => 0,
            ],
        ];

        $rejectStages = [
            (object) [
                'id' => 105,
                'name' => 'Duplicate',
                'parent' => 7,
                'priority' => 0,
                'delete_status' => 0,
            ],
            (object) [
                'id' => 106,
                'name' => 'Not Interested',
                'parent' => 7,
                'priority' => 1,
                'delete_status' => 0,
            ],
        ];

        $html = View::make('service_company.account.components.tab-interview-stages', [
            'interviewStage_sourcing' => [],
            'interviewStage_screening' => [],
            'interviewStage_evaluation' => [],
            'interviewStage_negotiation' => [],
            'interviewStage_offered' => [],
            'interviewStage_joined' => $joinedStages,
            'interviewStage_reject' => $rejectStages,
            'userPermissions' => ['view_settings', 'add_settings', 'edit_settings'],
        ])->render();

        // Req 1 & 2: 7 Master Stages and Live ATS Pipeline badges removed from header
        $this->assertStringNotContainsString('7 Master Stages', $html);
        $this->assertStringNotContainsString('Live ATS Pipeline', $html);

        // Req 3: "Sub-Stages Defined" text removed from header subtitle
        $this->assertStringNotContainsString('Sub-Stages Defined', $html);

        // Req 4: Search bar completely removed
        $this->assertStringNotContainsString('ipsSearchInput', $html);
        $this->assertStringNotContainsString('ips-search-subcard', $html);
        $this->assertStringNotContainsString('Search stages or sub-stages', $html);

        // Req 5 & 10: Dynamic header button "Add Substage" present
        $this->assertStringContainsString('id="ipsHeaderAddBtn"', $html);
        $this->assertStringContainsString('id="ipsHeaderAddBtnText"', $html);
        $this->assertStringContainsString('Add Substage', $html);

        // Req: Reset button present on Tier 1 toolbar
        $this->assertStringContainsString('id="ipsResetPipelineBtn"', $html);
        $this->assertStringContainsString('ips-btn-reset', $html);
        $this->assertStringContainsString('Reset', $html);

        // Req 6: Instant AJAX Sync bottom notice completely removed
        $this->assertStringNotContainsString('Instant AJAX Sync', $html);
        $this->assertStringNotContainsString('atomic cache invalidation', $html);

        // Req 7: Sub-stage numbering uses "Stage X", never "Step X"
        $this->assertStringContainsString('Stage 1', $html);
        $this->assertStringContainsString('Stage 2', $html);
        $this->assertStringNotContainsString('Step 1', $html);
        $this->assertStringNotContainsString('Step 2', $html);

        // Req 8: Items are draggable for moving/reordering
        $this->assertStringContainsString('draggable="true"', $html);
        $this->assertStringContainsString('ips-drag-handle', $html);

        // Req 9: "+ Add" / "+ Add Sub-Stage" removed from every stage card header and empty state
        $this->assertStringNotContainsString('ips-btn-add-sub', $html);
        $this->assertStringNotContainsString('ips-btn-empty-add', $html);

        // Req 11: Every stage header has background color styled with var(--stage-light)
        $this->assertStringContainsString('var(--stage-light', $html);
        $this->assertStringContainsString('var(--stage-border', $html);

        // Verify the 7 master stages are present
        $this->assertStringContainsString('Sourcing', $html);
        $this->assertStringContainsString('Screening', $html);
        $this->assertStringContainsString('Evaluation', $html);
        $this->assertStringContainsString('Negotiation', $html);
        $this->assertStringContainsString('Offer', $html);
        $this->assertStringContainsString('Joined', $html);
        $this->assertStringContainsString('Rejected', $html);
    }

    /**
     * Test 3: Verify add/reset buttons respect permissions when add_settings/edit_settings are absent.
     */
    public function test_tab_interview_stages_respects_permissions(): void
    {
        $html = View::make('service_company.account.components.tab-interview-stages', [
            'interviewStage_sourcing' => [],
            'interviewStage_screening' => [],
            'interviewStage_evaluation' => [],
            'interviewStage_negotiation' => [],
            'interviewStage_offered' => [],
            'interviewStage_joined' => [],
            'interviewStage_reject' => [],
            'userPermissions' => ['view_settings'], // Neither add_settings nor edit_settings
        ])->render();

        $this->assertStringNotContainsString('ipsHeaderAddBtn', $html);
        $this->assertStringNotContainsString('ipsResetPipelineBtn', $html);
    }

    /**
     * Test 4: Verify resetIntStages deletes custom sub-stages and restores system defaults.
     */
    public function test_reset_int_stages_deletes_custom_and_preserves_system_defaults(): void
    {
        foreach (['global_settings', 'sms_settings', 'theme_settings'] as $tbl) {
            if (! \Illuminate\Support\Facades\Schema::hasTable($tbl)) {
                \Illuminate\Support\Facades\Schema::create($tbl, function ($t) {
                    $t->increments('id');
                    $t->integer('company_id')->nullable();
                    $t->string('company_name')->nullable();
                    $t->timestamps();
                });
            }
        }

        if (! \Illuminate\Support\Facades\Schema::hasTable('candidate_status')) {
            \Illuminate\Support\Facades\Schema::create('candidate_status', function ($table) {
                $table->increments('id');
                $table->integer('company_id');
                $table->integer('parent')->default(0);
                $table->string('name', 55);
                $table->integer('priority')->default(0);
                $table->integer('delete_status')->default(1);
                $table->timestamps();
            });
        }

        if (! \Illuminate\Support\Facades\Schema::hasTable('job_applications')) {
            \Illuminate\Support\Facades\Schema::create('job_applications', function ($table) {
                $table->increments('id');
                $table->integer('company_id');
                $table->integer('child_status_id')->default(0);
                $table->timestamps();
            });
        }

        \Illuminate\Support\Facades\Cache::put('global_settings', (object) ['currency' => (object) ['id' => 1, 'code' => 'USD']], 86400);

        $testCompanyId = 8888;

        // Clean up any test records
        DB::table('candidate_status')->where('company_id', $testCompanyId)->delete();

        // 1. Insert 2 custom sub-stages (delete_status = 1) under Sourcing and Evaluation
        $customStage1Id = DB::table('candidate_status')->insertGetId([
            'company_id' => $testCompanyId,
            'parent' => 1,
            'name' => 'Agency Outreach',
            'priority' => 0,
            'delete_status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $customStage2Id = DB::table('candidate_status')->insertGetId([
            'company_id' => $testCompanyId,
            'parent' => 3,
            'name' => 'Coding Challenge',
            'priority' => 0,
            'delete_status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Insert 1 system sub-stage under Joined (delete_status = 0)
        DB::table('candidate_status')->insert([
            'company_id' => $testCompanyId,
            'parent' => 6,
            'name' => 'Onboarded',
            'priority' => 0,
            'delete_status' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Mock authenticated user with testCompanyId
        $mockUser = new User;
        $mockUser->id = 9999;
        $mockUser->company_id = $testCompanyId;
        Auth::setUser($mockUser);

        // Instantiate controller and mock dependencies
        $mockElastic = $this->createMock(ElasticService::class);
        $mockAccountSettings = $this->createMock(AccountSettingsService::class);
        $mockAccountSettings->expects($this->once())
            ->method('flushAccountCache')
            ->with($testCompanyId);

        $controller = new ServiceCompanyAccountSettingsController($mockElastic, $mockAccountSettings);

        $response = $controller->resetIntStages(new Request);
        $responseData = is_array($response) ? $response : $response->getData(true);

        $this->assertEquals('success', $responseData['status']);
        $this->assertStringContainsString('reset to system defaults', $responseData['message']);
        $this->assertNotEmpty($responseData['html']);

        // Assert that custom sub-stages were deleted
        $remainingCustom = DB::table('candidate_status')
            ->where('company_id', $testCompanyId)
            ->whereIn('id', [$customStage1Id, $customStage2Id])
            ->count();
        $this->assertEquals(0, $remainingCustom);

        // Assert that system sub-stages exist (Joined: 2, Rejected: 11)
        $joinedCount = DB::table('candidate_status')
            ->where('company_id', $testCompanyId)
            ->where('parent', 6)
            ->where('delete_status', 0)
            ->count();
        $this->assertEquals(2, $joinedCount);

        $rejectedCount = DB::table('candidate_status')
            ->where('company_id', $testCompanyId)
            ->where('parent', 7)
            ->where('delete_status', 0)
            ->count();
        $this->assertEquals(11, $rejectedCount);

        // Clean up
        DB::table('candidate_status')->where('company_id', $testCompanyId)->delete();
    }
}
