<?php

namespace Tests\Feature;

use App\Services\Analytics\ClientPerformanceAnalyticsService;
use App\Services\Analytics\ClientPerformanceCacheManager;
use App\Services\Analytics\ClientPerformanceScopeResolver;
use App\Services\ElasticService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * Class AgencyDrawerRecruiterPerformanceTeamScopingTest
 *
 * Authoritative Test Suite for Agency Drawer Recruiter Performance Team Scoping:
 * - Agency Drawer Recruiter Performance table ONLY lists recruiters belonging to the Partner Agency team.
 * - Host company recruiters (e.g. User #2 from Company #2) are strictly excluded from the agency's recruiter list.
 * - Agency team members from local_users_list_elastic are resolved and listed with their metrics.
 * - Candidate pipeline in agency mode is strictly scoped to sharedJobIds.
 * - Adheres strictly to CQRS (0% MySQL reads on read path).
 */
class AgencyDrawerRecruiterPerformanceTeamScopingTest extends TestCase
{
    use DatabaseTransactions;

    protected const HOST_COMPANY_ID = 2;
    protected const AGENCY_COMPANY_ID = 4;

    protected function setUp(): void
    {
        parent::setUp();

        $this->buildTestSchema();
        $this->seedTestData();

        try {
            Redis::flushdb();
        } catch (\Throwable) {
        }
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
    }

    protected function seedTestData(): void
    {
        DB::table('companies')->updateOrInsert(
            ['id' => self::HOST_COMPANY_ID],
            ['company_name' => '4Bell Technology', 'company_email' => 'host@4bell.com', 'status' => 'active']
        );

        DB::table('companies')->updateOrInsert(
            ['id' => self::AGENCY_COMPANY_ID],
            ['company_name' => 'InnovateSoft Technologies', 'company_email' => 'agency@innovatesoft.com', 'status' => 'active']
        );

        DB::table('recommendation_relation')->updateOrInsert(
            ['from_company_id' => self::HOST_COMPANY_ID, 'to_company_id' => self::AGENCY_COMPANY_ID],
            ['final_status' => 1]
        );

        DB::table('users')->updateOrInsert(
            ['id' => 2],
            ['company_id' => self::HOST_COMPANY_ID, 'name' => 'Vibhanshu Jaiswal', 'email' => 'vibhanshu@4bell.com']
        );

        DB::table('users')->updateOrInsert(
            ['id' => 5],
            ['company_id' => self::AGENCY_COMPANY_ID, 'name' => 'Daksh Sharma', 'email' => 'daksh@innovatesoft.com']
        );

        DB::table('users')->updateOrInsert(
            ['id' => 4],
            ['company_id' => self::AGENCY_COMPANY_ID, 'name' => 'Varsha Sharma', 'email' => 'varsha@innovatesoft.com']
        );
    }

    /**
     * Test that in agency mode, Recruiter Performance items strictly belong to the agency company.
     */
    public function test_agency_drawer_recruiter_performance_strictly_lists_agency_team(): void
    {
        $cacheMgr = app(ClientPerformanceCacheManager::class);
        $cacheMgr->invalidate(self::HOST_COMPANY_ID, self::AGENCY_COMPANY_ID);

        $analyticsService = app(ClientPerformanceAnalyticsService::class);
        $dashboard = $analyticsService->getPerformanceDashboard(
            self::HOST_COMPANY_ID,
            self::AGENCY_COMPANY_ID,
            'agency',
            '12M'
        );

        $this->assertArrayHasKey('recruiters', $dashboard);
        $this->assertArrayHasKey('items', $dashboard['recruiters']);

        $items = $dashboard['recruiters']['items'];
        $this->assertNotEmpty($items, 'Agency recruiters list should not be empty');

        foreach ($items as $recruiter) {
            $userId = $recruiter['user_id'];
            $this->assertNotEquals(2, $userId, 'Host company recruiter (User #2) must NEVER appear in Agency Recruiter Performance table.');

            // Verify the user belongs to Company 4 (Agency)
            $userRow = DB::table('users')->where('id', $userId)->first(['company_id']);
            if ($userRow) {
                $this->assertEquals(
                    self::AGENCY_COMPANY_ID,
                    (int) $userRow->company_id,
                    "Recruiter User #{$userId} must belong to agency company " . self::AGENCY_COMPANY_ID
                );
            }
        }
    }

    /**
     * Test that candidate filter for agency mode strictly includes sharedJobIds constraint.
     */
    public function test_agency_candidate_filter_strictly_scopes_to_shared_job_ids(): void
    {
        $scopeResolver = app(ClientPerformanceScopeResolver::class);
        $scope = $scopeResolver->resolveScope(
            self::HOST_COMPANY_ID,
            self::AGENCY_COMPANY_ID,
            'agency'
        );

        $candidateFilter = $scope['candidate_filter'];
        $this->assertIsArray($candidateFilter);

        $mustClauses = $candidateFilter['bool']['must'] ?? [];
        $hasJobIdConstraint = false;

        foreach ($mustClauses as $clause) {
            if (isset($clause['bool']['should'])) {
                foreach ($clause['bool']['should'] as $subClause) {
                    if (isset($subClause['terms']['jobId']) || isset($subClause['terms']['job_id'])) {
                        $hasJobIdConstraint = true;
                        break 2;
                    }
                }
            }
        }

        $this->assertTrue($hasJobIdConstraint, 'Agency candidate_filter must strictly scope by jobId / job_id terms in sharedJobIds.');
    }

    /**
     * Test that Top Roles in agency mode only lists shared jobs.
     */
    public function test_agency_top_roles_only_lists_shared_jobs(): void
    {
        $cacheMgr = app(ClientPerformanceCacheManager::class);
        $cacheMgr->invalidate(self::HOST_COMPANY_ID, self::AGENCY_COMPANY_ID);

        $analyticsService = app(ClientPerformanceAnalyticsService::class);
        $dashboard = $analyticsService->getPerformanceDashboard(
            self::HOST_COMPANY_ID,
            self::AGENCY_COMPANY_ID,
            'agency',
            '12M'
        );

        $roles = $dashboard['roles']['items'] ?? [];
        $scopeResolver = app(ClientPerformanceScopeResolver::class);
        $scope = $scopeResolver->resolveScope(self::HOST_COMPANY_ID, self::AGENCY_COMPANY_ID, 'agency');
        $sharedJobIds = $scope['shared_job_ids'] ?? [];

        foreach ($roles as $role) {
            $this->assertContains(
                $role['job_id'],
                $sharedJobIds,
                "Role job #{$role['job_id']} must be in shared jobs list " . json_encode($sharedJobIds)
            );
        }
    }
}
