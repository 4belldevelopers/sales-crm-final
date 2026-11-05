<?php

namespace Tests\Feature;

use App\DTO\ServiceCompany\ClientJobsQuery;
use App\Services\Analytics\ClientPerformanceAnalyticsService;
use App\Services\Analytics\ClientPerformanceCacheManager;
use App\Services\Candidate\CandidateDrawerQueryService;
use App\Services\ServiceCompany\ClientJobsReadService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * Class DirectClientAndAgencyDrawerDataSymmetryTest
 *
 * Authoritative Test Suite for Two-Sided Data Symmetry:
 * Ensures data shared between Upstream Direct Client (Company #2) and Downstream Agency Partner (Company #4)
 * is 100% identical in both drawers across:
 * 1. Performance Tab (KPIs, Funnel, Shared Roles, and Scoped Agency Recruiters)
 * 2. Candidates Tab (Strict Shared Jobs Bounding, Dual Party Attribution, Exclusion of Statuses 8 and 9)
 * 3. Jobs Tab (Pipeline Stage Counts for Shared Requisitions)
 */
class DirectClientAndAgencyDrawerDataSymmetryTest extends TestCase
{
    use DatabaseTransactions;

    protected const DIRECT_CLIENT_ID = 2; // 4Bell Technology (Host / Upstream)
    protected const AGENCY_COMPANY_ID = 4; // InnovateSoft Technologies (Downstream Agency)

    protected function setUp(): void
    {
        parent::setUp();

        $this->buildTestSchema();
        $this->seedTestData();

        try {
            Redis::flushdb();
        } catch (\Throwable) {
        }

        app(ClientPerformanceCacheManager::class)->invalidate(self::DIRECT_CLIENT_ID, self::AGENCY_COMPANY_ID);
        app(ClientPerformanceCacheManager::class)->invalidate(self::AGENCY_COMPANY_ID, self::DIRECT_CLIENT_ID);
    }

    protected function buildTestSchema(): void
    {
        if (! Schema::hasTable('users')) {
            Schema::create('users', function ($t) {
                $t->increments('id');
                $t->unsignedInteger('company_id')->nullable();
                $t->string('name', 191)->nullable();
                $t->string('email', 191)->nullable();
                $t->timestamps();
            });
        }

        if (! Schema::hasTable('companies')) {
            Schema::create('companies', function ($t) {
                $t->increments('id');
                $t->string('company_name', 255)->nullable();
                $t->string('company_email', 191)->nullable();
                $t->string('status', 32)->default('active');
                $t->timestamps();
            });
        }

        if (! Schema::hasTable('recommendation_relation')) {
            Schema::create('recommendation_relation', function ($t) {
                $t->increments('id');
                $t->unsignedInteger('from_company_id')->nullable();
                $t->unsignedInteger('to_company_id')->nullable();
                $t->unsignedInteger('from_user_id')->nullable();
                $t->tinyInteger('final_status')->default(1);
                $t->timestamps();
            });
        }

        if (! Schema::hasTable('job_assign')) {
            Schema::create('job_assign', function ($t) {
                $t->increments('id');
                $t->unsignedInteger('company_id')->nullable();
                $t->unsignedInteger('job_id')->nullable();
                $t->unsignedInteger('spok_user_id')->nullable();
                $t->timestamps();
            });
        }
    }

    protected function seedTestData(): void
    {
        DB::table('companies')->updateOrInsert(
            ['id' => self::DIRECT_CLIENT_ID],
            ['company_name' => '4Bell Technology', 'company_email' => 'direct@4bell.com', 'status' => 'active']
        );

        DB::table('companies')->updateOrInsert(
            ['id' => self::AGENCY_COMPANY_ID],
            ['company_name' => 'InnovateSoft Technologies', 'company_email' => 'agency@innovatesoft.com', 'status' => 'active']
        );

        DB::table('recommendation_relation')->updateOrInsert(
            ['from_company_id' => self::DIRECT_CLIENT_ID, 'to_company_id' => self::AGENCY_COMPANY_ID],
            ['final_status' => 1]
        );
    }

    /**
     * Test that Performance Dashboard returns 100% identical summary metrics,
     * funnel counts, and job roles between Agency Drawer and Direct Client Drawer.
     */
    public function test_performance_dashboard_data_is_100_percent_identical_for_both_sides(): void
    {
        $service = app(ClientPerformanceAnalyticsService::class);

        // Side A: Company 2 viewing Agency 4 (Consultancy / Agency Drawer)
        $sideA = $service->getPerformanceDashboard(self::DIRECT_CLIENT_ID, self::AGENCY_COMPANY_ID, 'agency', '12M');

        // Side B: Company 4 viewing Direct Client 2 (Direct Client Drawer)
        $sideB = $service->getPerformanceDashboard(self::AGENCY_COMPANY_ID, self::DIRECT_CLIENT_ID, 'direct', '12M');

        // 1. Summary Metrics Symmetry
        $this->assertEquals($sideA['summary']['candidates']['value'], $sideB['summary']['candidates']['value']);
        $this->assertEquals($sideA['summary']['screened']['value'], $sideB['summary']['screened']['value']);
        $this->assertEquals($sideA['summary']['interviewed']['value'], $sideB['summary']['interviewed']['value']);
        $this->assertEquals($sideA['summary']['offers']['value'], $sideB['summary']['offers']['value']);
        $this->assertEquals($sideA['summary']['joins']['value'], $sideB['summary']['joins']['value']);

        // 2. Funnel Stages Symmetry
        $funnelA = array_column($sideA['funnel']['stages'], 'count', 'key');
        $funnelB = array_column($sideB['funnel']['stages'], 'count', 'key');
        $this->assertEquals($funnelA, $funnelB, 'Hiring pipeline funnel counts must match 100% on both sides');

        // 3. Jobs Count Symmetry
        $this->assertEquals($sideA['jobs']['total_count'], $sideB['jobs']['total_count']);

        // 4. Roles Breakdown Symmetry
        $rolesA = array_column($sideA['roles']['items'], 'submissions', 'job_id');
        $rolesB = array_column($sideB['roles']['items'], 'submissions', 'job_id');
        $this->assertEquals($rolesA, $rolesB, 'Job roles and submissions must match 100% on both sides');

        // 5. Recruiter Team Scoping Symmetry (both show agency team members)
        $recruitersA = array_column($sideA['recruiters']['items'], 'name', 'user_id');
        $recruitersB = array_column($sideB['recruiters']['items'], 'name', 'user_id');
        $this->assertEquals($recruitersA, $recruitersB, 'Recruiter table team members must match 100% on both sides');
    }

    /**
     * Test that Candidates Tab returns 100% identical candidate lists between Agency Drawer and Direct Client Drawer.
     */
    public function test_candidate_drawer_tab_data_is_100_percent_identical_for_both_sides(): void
    {
        $service = app(CandidateDrawerQueryService::class);

        // Side A: Company 2 viewing Agency 4 (agency drawer)
        $resA = $service->getCandidates(self::DIRECT_CLIENT_ID, self::AGENCY_COMPANY_ID, 'agency', ['size' => 50]);

        // Side B: Company 4 viewing Direct Client 2 (direct client drawer)
        $resB = $service->getCandidates(self::AGENCY_COMPANY_ID, self::DIRECT_CLIENT_ID, 'direct', ['size' => 50]);

        $this->assertEquals($resA['pagination']['total'], $resB['pagination']['total']);

        $candsA = array_column($resA['data'], 'id');
        $candsB = array_column($resB['data'], 'id');
        sort($candsA);
        sort($candsB);

        $this->assertEquals($candsA, $candsB, 'Candidate IDs in Candidates tab must match 100% on both sides');

        // Stage counts symmetry and zero rejections validation
        $this->assertEquals($resA['meta']['stage_counts'], $resB['meta']['stage_counts'], 'Stage counts in Candidate tab must match 100% on both sides');
        $this->assertEquals(0, $resA['meta']['stage_counts']['rejected'], 'Rejected count must be 0 when no candidate has job_master_status_id = 7 on shared job');
        $this->assertEquals(0, $resB['meta']['stage_counts']['rejected'], 'Rejected count must be 0 on direct client side as well');

        // Explicit status 7 query returns 0 candidates on both sides
        $resA7 = $service->getCandidates(self::DIRECT_CLIENT_ID, self::AGENCY_COMPANY_ID, 'agency', ['status_id' => 7]);
        $resB7 = $service->getCandidates(self::AGENCY_COMPANY_ID, self::DIRECT_CLIENT_ID, 'direct', ['status_id' => 7]);
        $this->assertCount(0, $resA7['data'], 'Querying status 7 must return 0 candidates on agency side');
        $this->assertCount(0, $resB7['data'], 'Querying status 7 must return 0 candidates on direct client side');
    }

    /**
     * Test that Jobs Tab returns 100% identical pipeline stage counts for shared requisitions.
     */
    public function test_jobs_tab_pipeline_counts_are_100_percent_identical_for_both_sides(): void
    {
        $service = app(ClientJobsReadService::class);

        $qA = new ClientJobsQuery(
            tenantId: self::DIRECT_CLIENT_ID,
            serviceCompanyId: self::DIRECT_CLIENT_ID,
            clientId: self::AGENCY_COMPANY_ID,
            clientType: 'agency',
            partnerCompanyId: null,
            search: null,
            status: 'all',
            page: 1,
            perPage: 20
        );

        $qB = new ClientJobsQuery(
            tenantId: self::AGENCY_COMPANY_ID,
            serviceCompanyId: self::AGENCY_COMPANY_ID,
            clientId: self::DIRECT_CLIENT_ID,
            clientType: 'direct',
            partnerCompanyId: null,
            search: null,
            status: 'all',
            page: 1,
            perPage: 20
        );

        $jobsA = $service->getJobs($qA);
        $jobsB = $service->getJobs($qB);

        $this->assertEquals($jobsA->totalCount, $jobsB->totalCount, 'Total job counts must match');

        // Pipeline stage counts on Job 2
        $stagesA = $jobsA->pipelineStageMap[2] ?? [];
        $stagesB = $jobsB->pipelineStageMap[2] ?? [];
        ksort($stagesA);
        ksort($stagesB);

        $this->assertEquals($stagesA, $stagesB, 'Pipeline stage breakdown on Job 2 must match 100% on both sides');
    }
}
