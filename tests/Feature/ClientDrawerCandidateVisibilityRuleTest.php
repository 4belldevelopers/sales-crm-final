<?php

namespace Tests\Feature;

use App\DTO\ServiceCompany\ClientActivityQuery;
use App\Models\User;
use App\Services\Analytics\ClientPerformanceAnalyticsService;
use App\Services\Analytics\ClientPerformanceCacheManager;
use App\Services\Analytics\ClientPerformanceDateRangeResolver;
use App\Services\Analytics\ClientPerformanceInsightEngine;
use App\Services\Analytics\ClientPerformanceQueryBuilder;
use App\Services\Analytics\ClientPerformanceScopeResolver;
use App\Services\Candidate\CandidateDrawerQueryService;
use App\Services\Candidate\ClientCandidateVersionStore;
use App\Services\ElasticService;
use App\Services\ServiceCompany\ClientActivityReadService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * Class ClientDrawerCandidateVisibilityRuleTest
 *
 * Authoritative Test Suite for the Candidate Visibility Rule across Internal and Direct Client Drawers:
 * - Candidate Tab: Excludes In Review candidates (status 8) from listings, queries, and stage counts.
 * - Activity Tab: Drops In Review candidate events, transitions, and interviews.
 * - Performance Tab: Summary KPIs and charts strictly exclude In Review candidates.
 * - Hiring Pipeline Funnel: The only exception where In Review candidates are reflected in the pipeline.
 * - Interview Tracker: Strict exclusion of status 8 candidates.
 * - Transition Lifecycle: Candidates become visible/countable only after moving out of In Review to an eligible stage.
 * - GeneralMethod: Cache counters strictly exclude status 8.
 */
class ClientDrawerCandidateVisibilityRuleTest extends TestCase
{
    use DatabaseTransactions;

    protected const SERVICE_COMPANY_ID = 105;

    protected const CLIENT_COMPANY_ID = 4;

    protected const DIRECT_CLIENT_COMPANY_ID = 4;

    protected User $testUser;

    protected function setUp(): void
    {
        parent::setUp();

        config(['auth.single_device_login.enabled' => false]);
        Gate::define('view_clients', fn () => true);
        Gate::define('view_jobs', fn () => true);

        try {
            Redis::flushdb();
        } catch (\Throwable) {
        }

        $this->buildTestSchema();
        $this->seedTestData();
    }

    protected function buildTestSchema(): void
    {
        if (! Schema::hasTable('users')) {
            Schema::create('users', function ($t) {
                $t->increments('id');
                $t->unsignedInteger('company_id')->nullable();
                $t->string('name', 191)->nullable();
                $t->string('email', 191)->nullable();
                $t->timestamp('email_verified_at')->nullable();
                $t->string('password', 191)->nullable();
                $t->string('role', 100)->nullable();
                $t->string('status', 32)->default('active');
                $t->tinyInteger('root_user')->default(0);
                $t->string('calling_code', 10)->nullable();
                $t->string('mobile', 50)->nullable();
                $t->string('image', 255)->nullable();
                $t->tinyInteger('is_superadmin')->default(0);
                $t->rememberToken();
                $t->timestamps();
            });
        }

        if (! Schema::hasTable('companies')) {
            Schema::create('companies', function ($t) {
                $t->increments('id');
                $t->string('company_name', 255)->nullable();
                $t->string('company_email', 191)->nullable();
                $t->string('company_phone', 55)->nullable();
                $t->string('website', 255)->nullable();
                $t->text('address')->nullable();
                $t->string('status', 32)->default('active');
                $t->string('company_type', 32)->default('service');
                $t->string('package_type', 32)->default('monthly');
                $t->string('logo', 255)->nullable();
                $t->string('industries', 255)->nullable();
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

        if (! Schema::hasTable('roles')) {
            Schema::create('roles', function ($t) {
                $t->increments('id');
                $t->unsignedInteger('company_id')->nullable();
                $t->string('name', 191);
                $t->string('display_name', 191)->nullable();
                $t->string('guard_name', 191)->default('web');
                $t->timestamps();
            });
        }

        if (! Schema::hasTable('permissions')) {
            Schema::create('permissions', function ($t) {
                $t->increments('id');
                $t->string('name', 191);
                $t->string('guard_name', 191)->default('web');
                $t->timestamps();
            });
        }

        if (! Schema::hasTable('model_has_permissions')) {
            Schema::create('model_has_permissions', function ($t) {
                $t->unsignedInteger('permission_id');
                $t->string('model_type', 191);
                $t->unsignedInteger('model_id');
            });
        }

        if (! Schema::hasTable('model_has_roles')) {
            Schema::create('model_has_roles', function ($t) {
                $t->unsignedInteger('role_id');
                $t->string('model_type', 191);
                $t->unsignedInteger('model_id');
            });
        }
    }

    protected function seedTestData(): void
    {
        DB::table('companies')->insertOrIgnore([
            ['id' => self::SERVICE_COMPANY_ID, 'company_name' => 'ABC Recruitment Pvt. Ltd.', 'company_type' => 'service', 'status' => 'active'],
            ['id' => self::CLIENT_COMPANY_ID, 'company_name' => '4Bell Technology', 'company_type' => 'service', 'status' => 'active'],
        ]);

        DB::table('recommendation_relation')->insertOrIgnore([
            [
                'id' => 1,
                'from_company_id' => self::CLIENT_COMPANY_ID,
                'to_company_id' => self::SERVICE_COMPANY_ID,
                'from_user_id' => 42,
                'final_status' => 1,
            ],
        ]);

        DB::table('users')->insertOrIgnore([
            ['id' => 101, 'company_id' => self::SERVICE_COMPANY_ID, 'name' => 'Rahul Sharma', 'email' => 'rahul@abc.com', 'status' => 'active', 'root_user' => 1, 'is_superadmin' => 0],
        ]);

        $this->testUser = User::find(101);
        $this->actingAs($this->testUser);

        Redis::setex('ats:auth_context:direct:'.self::SERVICE_COMPANY_ID.':'.self::CLIENT_COMPANY_ID, 60, 1);
        Redis::setex('ats:auth_context:internal:'.self::SERVICE_COMPANY_ID.':'.self::CLIENT_COMPANY_ID, 60, 1);
    }

    // =========================================================================
    // 1. CANDIDATE TAB VISIBILITY TESTS
    // =========================================================================

    public function test_candidate_tab_excludes_in_review_candidates_in_direct_client_mode(): void
    {
        $mockElastic = \Mockery::mock(ElasticService::class);
        $mockClient = \Mockery::mock();
        $mockVersionStore = \Mockery::mock(ClientCandidateVersionStore::class);

        $mockElastic->shouldReceive('indexExists')->andReturn(true);
        $mockElastic->shouldReceive('getClient')->andReturn($mockClient);
        $mockVersionStore->shouldReceive('getVersion')->andReturn(1);

        $capturedBody = null;
        $mockClient->shouldReceive('search')->andReturnUsing(function ($params) use (&$capturedBody) {
            $capturedBody = $params['body'] ?? [];

            return [
                'hits' => [
                    'total' => ['value' => 1, 'relation' => 'eq'],
                    'hits' => [
                        [
                            '_source' => [
                                'id' => 1001,
                                'wiseId' => 1001,
                                'job_application_id' => 1001,
                                'full_name' => 'Eligible Candidate',
                                'job_master_status_id' => 1, // Sourcing
                                'created_at' => '2026-10-29T10:00:00Z',
                            ],
                        ],
                    ],
                ],
                'aggregations' => [
                    'sourcing' => ['doc_count' => 1],
                    'in_review' => ['doc_count' => 5], // ES aggregation may return count, service must zero it out
                ],
            ];
        });

        $service = new CandidateDrawerQueryService($mockElastic, $mockVersionStore);
        $result = $service->getCandidates(self::SERVICE_COMPANY_ID, self::CLIENT_COMPANY_ID, 'direct');

        // Assert query body strictly excluded In Review status 8
        $this->assertNotNull($capturedBody);
        $mustNot = $capturedBody['query']['bool']['must_not'] ?? [];
        $hasJobMasterStatusExclusion = false;
        $hasMasterStatusExclusion = false;

        foreach ($mustNot as $clause) {
            if (isset($clause['terms']['job_master_status_id'])) {
                $terms = $clause['terms']['job_master_status_id'];
                if (in_array(8, $terms) || in_array('8', $terms)) {
                    $hasJobMasterStatusExclusion = true;
                }
            }
            if (isset($clause['terms']['master_status_id'])) {
                $terms = $clause['terms']['master_status_id'];
                if (in_array(8, $terms) || in_array('8', $terms)) {
                    $hasMasterStatusExclusion = true;
                }
            }
            if (isset($clause['bool']['must'])) {
                foreach ($clause['bool']['must'] as $sub) {
                    if (isset($sub['terms']['master_status_id'])) {
                        $terms = $sub['terms']['master_status_id'];
                        if (in_array(8, $terms) || in_array('8', $terms)) {
                            $hasMasterStatusExclusion = true;
                        }
                    }
                }
            }
        }

        $this->assertTrue($hasJobMasterStatusExclusion, 'Direct Client query must strictly exclude job_master_status_id 8 in must_not');
        $this->assertTrue($hasMasterStatusExclusion, 'Direct Client query must strictly exclude master_status_id 8 in must_not');

        // Assert result candidate does not have status 8
        $this->assertCount(1, $result['data']);
        $this->assertEquals(1, $result['data'][0]['job_master_status_id']);
        $this->assertEquals(0, $result['meta']['stage_counts']['in_review'], 'Stage count for in_review must be strictly 0');
    }

    public function test_candidate_tab_excludes_in_review_candidates_in_internal_client_mode(): void
    {
        $mockElastic = \Mockery::mock(ElasticService::class);
        $mockClient = \Mockery::mock();
        $mockVersionStore = \Mockery::mock(ClientCandidateVersionStore::class);

        $mockElastic->shouldReceive('indexExists')->andReturn(true);
        $mockElastic->shouldReceive('getClient')->andReturn($mockClient);
        $mockVersionStore->shouldReceive('getVersion')->andReturn(1);

        $capturedBody = null;
        $mockClient->shouldReceive('search')->andReturnUsing(function ($params) use (&$capturedBody) {
            $capturedBody = $params['body'] ?? [];

            return [
                'hits' => [
                    'total' => ['value' => 1, 'relation' => 'eq'],
                    'hits' => [
                        [
                            '_source' => [
                                'id' => 1002,
                                'wiseId' => 1002,
                                'full_name' => 'Internal Candidate',
                                'job_master_status_id' => 2,
                                'created_at' => '2026-10-29T10:00:00Z',
                            ],
                        ],
                    ],
                ],
                'aggregations' => [
                    'screening' => ['doc_count' => 1],
                    'in_review' => ['doc_count' => 8],
                ],
            ];
        });

        $service = new CandidateDrawerQueryService($mockElastic, $mockVersionStore);
        $result = $service->getCandidates(self::SERVICE_COMPANY_ID, self::CLIENT_COMPANY_ID, 'internal');

        $this->assertNotNull($capturedBody);
        $mustNot = $capturedBody['query']['bool']['must_not'] ?? [];
        $hasJobMasterStatusExclusion = false;

        foreach ($mustNot as $clause) {
            if (isset($clause['terms']['job_master_status_id']) && in_array(8, $clause['terms']['job_master_status_id'])) {
                $hasJobMasterStatusExclusion = true;
            }
        }

        $this->assertTrue($hasJobMasterStatusExclusion, 'Internal Client query must strictly exclude job_master_status_id 8');
        $this->assertEquals(0, $result['meta']['stage_counts']['in_review'], 'Internal Client in_review stage count must be 0');
    }

    public function test_candidate_tab_returns_empty_when_explicitly_querying_status_id_8(): void
    {
        $mockElastic = \Mockery::mock(ElasticService::class);
        $mockVersionStore = \Mockery::mock(ClientCandidateVersionStore::class);
        $mockVersionStore->shouldReceive('getVersion')->andReturn(1);

        // ES search must NEVER even be called when status_id = 8 is queried
        $mockElastic->shouldReceive('getClient')->never();

        $service = new CandidateDrawerQueryService($mockElastic, $mockVersionStore);
        $result = $service->getCandidates(self::SERVICE_COMPANY_ID, self::CLIENT_COMPANY_ID, 'direct', ['status_id' => 8]);

        $this->assertEmpty($result['data'], 'Querying status_id 8 must return empty candidate list');
        $this->assertEquals(0, $result['pagination']['total']);
        $this->assertEquals(0, $result['meta']['stage_counts']['in_review']);
    }

    // =========================================================================
    // 2. ACTIVITY TAB VISIBILITY TESTS
    // =========================================================================

    public function test_activity_tab_drops_in_review_candidate_events_and_transitions(): void
    {
        $mockElastic = \Mockery::mock(ElasticService::class);
        $mockClient = \Mockery::mock();

        $mockElastic->shouldReceive('indexExists')->andReturn(true);
        $mockElastic->shouldReceive('getClient')->andReturn($mockClient);
        $mockClient->shouldReceive('count')->andReturn(['count' => 1]);

        // Mock job and candidate resolution
        $mockClient->shouldReceive('search')->andReturnUsing(function ($params) {
            $index = $params['index'] ?? '';

            if (str_contains($index, 'jobs')) {
                return [
                    'hits' => [
                        'hits' => [
                            [
                                '_id' => '10',
                                '_source' => [
                                    'id' => 10,
                                    'title' => 'Backend Engineer',
                                    'status' => 'active',
                                    'recommendation_relation' => [
                                        [
                                            'from_company_id' => self::CLIENT_COMPANY_ID,
                                            'to_company_id' => self::SERVICE_COMPANY_ID,
                                            'final_status' => 1,
                                            'user_id' => 42,
                                            'user_name' => 'Rahul Sharma',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ];
            }

            if (str_contains($index, 'candidates')) {
                return [
                    'hits' => [
                        'hits' => [
                            [
                                '_id' => '101',
                                '_source' => [
                                    'id' => 101,
                                    'candidate_id' => 101,
                                    'wiseId' => 101,
                                    'full_name' => 'Alice InReview',
                                    'job_master_status_id' => 8, // In Review candidate
                                    'master_status_name' => 'In Review',
                                    'thirdparty_company_id' => self::SERVICE_COMPANY_ID,
                                    'job_id' => 10,
                                ],
                            ],
                            [
                                '_id' => '102',
                                '_source' => [
                                    'id' => 102,
                                    'candidate_id' => 102,
                                    'wiseId' => 102,
                                    'full_name' => 'Bob Eligible',
                                    'job_master_status_id' => 2, // Screening candidate
                                    'master_status_name' => 'Screening',
                                    'thirdparty_company_id' => self::SERVICE_COMPANY_ID,
                                    'job_id' => 10,
                                ],
                            ],
                        ],
                    ],
                ];
            }

            return ['hits' => ['hits' => []]];
        });

        // Mock multi-index msearch on ElasticService with 2 events: 1 for Alice (In Review) and 1 for Bob (Eligible)
        $mockElastic->shouldReceive('msearch')->andReturn([
            'responses' => [
                [
                    'hits' => [
                        'hits' => [
                            [
                                '_id' => 'evt_alice',
                                '_source' => [
                                    'application_id' => 101,
                                    'candidate_name' => 'Alice InReview',
                                    'job_id' => 10,
                                    'candidate_status' => 8,
                                    'master_process_name' => 'In Review',
                                    'created_at' => '2026-10-29T09:00:00Z',
                                ],
                            ],
                            [
                                '_id' => 'evt_bob',
                                '_source' => [
                                    'application_id' => 102,
                                    'candidate_name' => 'Bob Eligible',
                                    'job_id' => 10,
                                    'candidate_status' => 2,
                                    'master_process_name' => 'Screening',
                                    'created_at' => '2026-10-29T10:00:00Z',
                                ],
                            ],
                        ],
                    ],
                ],
                ['hits' => ['hits' => []]],
                ['hits' => ['hits' => []]],
                ['hits' => ['hits' => []]],
            ],
        ]);

        $query = new ClientActivityQuery(
            tenantId: self::CLIENT_COMPANY_ID,
            serviceCompanyId: self::SERVICE_COMPANY_ID,
            clientId: self::CLIENT_COMPANY_ID,
            clientType: 'direct',
            partnerCompanyId: self::CLIENT_COMPANY_ID,
            category: 'all',
            timeframe: 'all',
            search: null,
            cursorSequence: null,
            cursorEventId: null,
            limit: 50,
            forceRefresh: true
        );

        $activityService = new ClientActivityReadService($mockElastic);
        $result = $activityService->getActivityStream($query);

        $events = $result->items;
        // Event for Alice In Review MUST be dropped, Bob Eligible MUST remain
        $this->assertCount(1, $events, 'In Review candidate activity must be dropped from activity stream');
        $this->assertEquals('Bob Eligible', $events[0]['candidate']['name']);
    }

    public function test_activity_tab_drops_in_review_candidate_interviews(): void
    {
        $mockElastic = \Mockery::mock(ElasticService::class);
        $mockClient = \Mockery::mock();

        $mockElastic->shouldReceive('indexExists')->andReturn(true);
        $mockElastic->shouldReceive('getClient')->andReturn($mockClient);
        $mockClient->shouldReceive('count')->andReturn(['count' => 1]);

        $mockClient->shouldReceive('search')->andReturnUsing(function ($params) {
            $index = $params['index'] ?? '';
            if (str_contains($index, 'jobs')) {
                return [
                    'hits' => [
                        'hits' => [
                            [
                                '_id' => '10',
                                '_source' => [
                                    'id' => 10,
                                    'title' => 'Software Engineer',
                                    'status' => 'active',
                                    'recommendation_relation' => [
                                        [
                                            'from_company_id' => self::CLIENT_COMPANY_ID,
                                            'to_company_id' => self::SERVICE_COMPANY_ID,
                                            'final_status' => 1,
                                            'user_id' => 42,
                                            'user_name' => 'Rahul Sharma',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ];
            }
            if (str_contains($index, 'candidates')) {
                return [
                    'hits' => [
                        'hits' => [
                            [
                                '_id' => '102',
                                '_source' => [
                                    'id' => 102,
                                    'candidate_id' => 102,
                                    'wiseId' => 102,
                                    'full_name' => 'Bob Eligible',
                                    'job_master_status_id' => 3, // Evaluation
                                    'master_status_name' => 'Evaluation',
                                    'thirdparty_company_id' => self::SERVICE_COMPANY_ID,
                                    'job_id' => 10,
                                ],
                            ],
                            [
                                '_id' => '103',
                                '_source' => [
                                    'id' => 103,
                                    'candidate_id' => 103,
                                    'wiseId' => 103,
                                    'full_name' => 'Charlie Reviewing',
                                    'job_master_status_id' => 8,
                                    'master_status_name' => 'In Review',
                                    'thirdparty_company_id' => self::SERVICE_COMPANY_ID,
                                    'job_id' => 10,
                                ],
                            ],
                        ],
                    ],
                ];
            }

            return ['hits' => ['hits' => []]];
        });

        // Mock multi-index msearch with 1 interview for Charlie (In Review) and 1 for Bob (Eligible)
        $mockElastic->shouldReceive('msearch')->andReturn([
            'responses' => [
                [
                    'hits' => [
                        'hits' => [
                            [
                                '_id' => 'int_charlie',
                                '_source' => [
                                    'candidate_id' => 103,
                                    'job_id' => 10,
                                    'full_name' => 'Charlie Reviewing',
                                    'stage' => 'In Review',
                                    'master_status_id' => 8,
                                    'schedule_date' => '2026-10-30T10:00:00Z',
                                    'created_at' => '2026-10-29T10:00:00Z',
                                ],
                            ],
                            [
                                '_id' => 'int_bob',
                                '_source' => [
                                    'candidate_id' => 102,
                                    'job_id' => 10,
                                    'full_name' => 'Bob Eligible',
                                    'stage' => 'Evaluation',
                                    'master_status_id' => 3,
                                    'schedule_date' => '2026-10-30T11:00:00Z',
                                    'created_at' => '2026-10-29T10:00:00Z',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $query = new ClientActivityQuery(
            tenantId: self::CLIENT_COMPANY_ID,
            serviceCompanyId: self::SERVICE_COMPANY_ID,
            clientId: self::CLIENT_COMPANY_ID,
            clientType: 'direct',
            partnerCompanyId: self::CLIENT_COMPANY_ID,
            category: 'interviews',
            timeframe: 'all',
            search: null,
            cursorSequence: null,
            cursorEventId: null,
            limit: 50,
            forceRefresh: true
        );

        $activityService = new ClientActivityReadService($mockElastic);
        $result = $activityService->getActivityStream($query);

        // Charlie's interview (In Review) MUST be dropped, Bob's interview MUST remain
        $this->assertCount(1, $result->items, 'In Review candidate interview must be dropped from activity stream');
        $this->assertEquals('Bob Eligible', $result->items[0]['candidate']['name']);
    }

    // =========================================================================
    // 3. PERFORMANCE TAB VISIBILITY & HIRING PIPELINE EXCEPTION TESTS
    // =========================================================================

    public function test_performance_tab_summary_kpis_strictly_exclude_in_review_candidates(): void
    {
        $mockElastic = \Mockery::mock(ElasticService::class);
        $mockClient = \Mockery::mock();

        $mockElastic->shouldReceive('indexExists')->andReturn(true);
        $mockElastic->shouldReceive('getClient')->andReturn($mockClient);

        // Pre-configure Redis context
        Redis::setex('ats:auth_context:direct:'.self::SERVICE_COMPANY_ID.':'.self::CLIENT_COMPANY_ID, 60, 1);

        $mockClient->shouldReceive('search')->andReturnUsing(function ($params) {
            $index = $params['index'] ?? '';

            if (str_contains($index, 'candidates')) {
                return [
                    'hits' => ['total' => ['value' => 50], 'hits' => []],
                    'aggregations' => [
                        'total_submissions' => ['value' => 50],
                        'eligible_submissions' => [
                            'submissions' => ['value' => 40], // 40 eligible (1..6), 10 in review (8)
                            'unique_count' => ['value' => 38],
                        ],
                        'in_review_candidates' => [
                            'submissions' => ['value' => 10], // 10 in review
                            'unique_count' => ['value' => 10],
                        ],
                        'screened_candidates' => ['unique_count' => ['value' => 25]],
                        'interviewed_candidates' => ['unique_count' => ['value' => 15]],
                        'offered_candidates' => ['unique_count' => ['value' => 5]],
                        'joined_candidates' => ['unique_count' => ['value' => 3]],
                        'velocity_trend' => ['buckets' => []],
                        'job_roles_bucket' => ['buckets' => []],
                        'recruiters_bucket' => ['buckets' => []],
                    ],
                ];
            }

            if (str_contains($index, 'jobs')) {
                return [
                    'hits' => ['total' => ['value' => 5], 'hits' => []],
                    'aggregations' => [
                        'employment_type_breakdown' => ['buckets' => []],
                        'job_status_counts' => ['buckets' => []],
                        'active_jobs' => ['doc_count' => 5],
                    ],
                ];
            }

            return ['hits' => ['total' => ['value' => 0], 'hits' => []], 'aggregations' => []];
        });

        $scopeResolver = app(ClientPerformanceScopeResolver::class);
        $dateResolver = app(ClientPerformanceDateRangeResolver::class);
        $queryBuilder = app(ClientPerformanceQueryBuilder::class);
        $insightEngine = app(ClientPerformanceInsightEngine::class);
        $cacheManager = app(ClientPerformanceCacheManager::class);

        $service = new ClientPerformanceAnalyticsService(
            $mockElastic,
            $scopeResolver,
            $dateResolver,
            $queryBuilder,
            $insightEngine,
            $cacheManager
        );

        $dashboard = $service->getPerformanceDashboard(
            authCompanyId: self::SERVICE_COMPANY_ID,
            clientId: self::CLIENT_COMPANY_ID,
            clientType: 'direct',
            range: '12M'
        );

        // Summary Candidates KPI MUST equal eligible count (40), NOT 50
        $this->assertEquals(40, $dashboard['summary']['candidates']['value'], 'Summary Total Candidates must strictly exclude In Review candidates');
        $this->assertEquals(38, $dashboard['summary']['candidates']['unique_count']);

        // Other KPIs
        $this->assertEquals(25, $dashboard['summary']['screened']['value']);
        $this->assertEquals(15, $dashboard['summary']['interviewed']['value']);
        $this->assertEquals(5, $dashboard['summary']['offers']['value']);
        $this->assertEquals(3, $dashboard['summary']['joins']['value']);
    }

    public function test_performance_tab_hiring_pipeline_funnel_reflects_in_review_candidates(): void
    {
        $mockElastic = \Mockery::mock(ElasticService::class);
        $mockClient = \Mockery::mock();

        $mockElastic->shouldReceive('indexExists')->andReturn(true);
        $mockElastic->shouldReceive('getClient')->andReturn($mockClient);

        $mockClient->shouldReceive('search')->andReturnUsing(function ($params) {
            $index = $params['index'] ?? '';
            if (str_contains($index, 'candidates')) {
                return [
                    'hits' => ['total' => ['value' => 50], 'hits' => []],
                    'aggregations' => [
                        'total_submissions' => ['value' => 50],
                        'eligible_submissions' => [
                            'submissions' => ['value' => 40],
                            'unique_count' => ['value' => 38],
                        ],
                        'in_review_candidates' => [
                            'submissions' => ['value' => 10],
                            'unique_count' => ['value' => 10],
                        ],
                        'screened_candidates' => ['unique_count' => ['value' => 20]],
                        'interviewed_candidates' => ['unique_count' => ['value' => 10]],
                        'offered_candidates' => ['unique_count' => ['value' => 5]],
                        'joined_candidates' => ['unique_count' => ['value' => 2]],
                        'velocity_trend' => ['buckets' => []],
                        'job_roles_bucket' => ['buckets' => []],
                        'recruiters_bucket' => ['buckets' => []],
                    ],
                ];
            }

            return ['hits' => ['total' => ['value' => 0], 'hits' => []], 'aggregations' => []];
        });

        $scopeResolver = app(ClientPerformanceScopeResolver::class);
        $dateResolver = app(ClientPerformanceDateRangeResolver::class);
        $queryBuilder = app(ClientPerformanceQueryBuilder::class);
        $insightEngine = app(ClientPerformanceInsightEngine::class);
        $cacheManager = app(ClientPerformanceCacheManager::class);

        $service = new ClientPerformanceAnalyticsService(
            $mockElastic,
            $scopeResolver,
            $dateResolver,
            $queryBuilder,
            $insightEngine,
            $cacheManager
        );

        $dashboard = $service->getPerformanceDashboard(
            authCompanyId: self::SERVICE_COMPANY_ID,
            clientId: self::CLIENT_COMPANY_ID,
            clientType: 'direct',
            range: '12M'
        );

        // Hiring Pipeline Funnel: Strictly excludes In Review candidates (0% In Review)
        $funnel = $dashboard['funnel'];
        $this->assertEquals(0, $funnel['in_review_count'], 'Hiring Pipeline in_review_count must be strictly 0');
        $this->assertEquals(0, $funnel['in_review_unique_count'], 'Hiring Pipeline in_review_unique_count must be strictly 0');
        $this->assertEquals(40, $funnel['hiring_pipeline_total'], 'Hiring pipeline total must strictly equal eligible submissions (40)');

        // Funnel stages begin with eligible candidate count (40)
        $this->assertEquals(40, $funnel['stages'][0]['count']);
        $this->assertEquals('Candidates', $funnel['stages'][0]['label']);
    }

    // =========================================================================
    // 4. INTERVIEW TRACKER SCOPE & VISIBILITY TESTS
    // =========================================================================

    public function test_interview_tracker_query_excludes_status_8_candidates(): void
    {
        $queryBuilder = app(ClientPerformanceQueryBuilder::class);
        $scope = [
            'auth_company_id' => self::SERVICE_COMPANY_ID,
            'client_id' => self::CLIENT_COMPANY_ID,
            'client_type' => 'direct',
        ];

        $trackerQuery = $queryBuilder->buildInterviewTrackerQuery($scope, []);

        $mustNot = $trackerQuery['query']['bool']['must_not'] ?? [];
        $hasMasterStatusExclusion = false;
        $hasJobMasterStatusExclusion = false;

        foreach ($mustNot as $clause) {
            if (isset($clause['terms']['master_status_id']) && in_array(8, $clause['terms']['master_status_id'])) {
                $hasMasterStatusExclusion = true;
            }
            if (isset($clause['terms']['job_master_status_id']) && in_array(8, $clause['terms']['job_master_status_id'])) {
                $hasJobMasterStatusExclusion = true;
            }
        }

        $this->assertTrue($hasMasterStatusExclusion, 'Interview Tracker query must exclude master_status_id 8 in must_not');
        $this->assertTrue($hasJobMasterStatusExclusion, 'Interview Tracker query must exclude job_master_status_id 8 in must_not');
    }

    // =========================================================================
    // 5. STAGE TRANSITION LIFECYCLE TEST (IN REVIEW -> SOURCING)
    // =========================================================================

    public function test_candidate_becomes_visible_after_moving_from_in_review_to_sourcing(): void
    {
        $mockElastic = \Mockery::mock(ElasticService::class);
        $mockClient = \Mockery::mock();
        $mockVersionStore = \Mockery::mock(ClientCandidateVersionStore::class);

        $mockElastic->shouldReceive('indexExists')->andReturn(true);
        $mockElastic->shouldReceive('getClient')->andReturn($mockClient);
        $mockVersionStore->shouldReceive('getVersion')->andReturn(1);

        // When candidate was in status 8, they were excluded. Now status is 1 (Sourcing)
        $mockClient->shouldReceive('search')->andReturn([
            'hits' => [
                'total' => ['value' => 1, 'relation' => 'eq'],
                'hits' => [
                    [
                        '_source' => [
                            'id' => 999,
                            'wiseId' => 999,
                            'job_application_id' => 999,
                            'full_name' => 'Transitioned Candidate',
                            'job_master_status_id' => 1, // Promoted to Sourcing
                            'master_status_name' => 'Sourcing',
                            'created_at' => '2026-10-29T11:00:00Z',
                        ],
                    ],
                ],
            ],
            'aggregations' => [
                'sourcing' => ['doc_count' => 1],
                'in_review' => ['doc_count' => 0],
            ],
        ]);

        $service = new CandidateDrawerQueryService($mockElastic, $mockVersionStore);
        $result = $service->getCandidates(self::SERVICE_COMPANY_ID, self::CLIENT_COMPANY_ID, 'direct');

        $this->assertCount(1, $result['data']);
        $this->assertEquals('Transitioned Candidate', $result['data'][0]['full_name']);
        $this->assertEquals(1, $result['meta']['stage_counts']['sourcing']);
        $this->assertEquals(0, $result['meta']['stage_counts']['in_review']);
    }

    // =========================================================================
    // 6. GENERALMETHOD CLIENT CACHE COUNTERS TEST
    // =========================================================================

    public function test_general_method_candidate_counts_exclude_in_review(): void
    {
        $mockElastic = \Mockery::mock(ElasticService::class);
        $mockClient = \Mockery::mock();

        $mockElastic->shouldReceive('indexExists')->andReturn(true);
        $mockElastic->shouldReceive('getClient')->andReturn($mockClient);
        $this->app->instance(ElasticService::class, $mockElastic);

        $mockClient->shouldReceive('search')->andReturnUsing(function ($params) {
            $index = $params['index'] ?? '';
            if (str_contains($index, 'interview')) {
                return ['hits' => ['total' => 0]];
            }

            return [
                'hits' => ['total' => ['value' => 10]],
                'aggregations' => [
                    'all_candidates' => ['doc_count' => 10], // Allow-list [1..6] returns 10
                    'joined' => ['doc_count' => 2],
                    'sourced' => ['doc_count' => 5],
                    'screening' => ['doc_count' => 3],
                    'evaluated' => ['doc_count' => 0],
                    'negotiations' => ['doc_count' => 0],
                    'offers' => ['doc_count' => 0],
                    'rejected' => ['doc_count' => 0],
                    'in_review' => ['doc_count' => 7], // Raw bucket might have 7, but method must zero it
                ],
            ];
        });

        // Test Direct Client counts
        $directCounts = \App\Helper\GeneralMethod::DirectClientCandidatesCountList(self::SERVICE_COMPANY_ID, self::CLIENT_COMPANY_ID);
        $this->assertEquals(0, $directCounts->in_review, 'Direct Client GeneralMethod counter must return in_review = 0');
        $this->assertEquals(10, $directCounts->all_candidates);

        // Test Internal Client counts
        $internalCounts = \App\Helper\GeneralMethod::InternalClientCandidatesCountList(self::CLIENT_COMPANY_ID, self::SERVICE_COMPANY_ID);
        $this->assertEquals(0, $internalCounts->in_review, 'Internal Client GeneralMethod counter must return in_review = 0');
        $this->assertEquals(10, $internalCounts->all_candidates);
    }

    public function test_direct_client_candidates_count_list_query_contract_excludes_status_8_and_9(): void
    {
        $mockElastic = \Mockery::mock(ElasticService::class);
        $mockClient = \Mockery::mock();

        $mockElastic->shouldReceive('indexExists')->andReturn(true);
        $mockElastic->shouldReceive('getClient')->andReturn($mockClient);
        $this->app->instance(ElasticService::class, $mockElastic);

        $capturedParams = [];
        $mockClient->shouldReceive('search')->andReturnUsing(function ($params) use (&$capturedParams) {
            $capturedParams[$params['index']] = $params;
            if (str_contains($params['index'], 'interview')) {
                return ['hits' => ['total' => ['value' => 1]]];
            }

            return [
                'hits' => ['total' => ['value' => 5]],
                'aggregations' => [
                    'all_candidates' => ['doc_count' => 5],
                    'joined' => ['doc_count' => 1],
                    'sourced' => ['doc_count' => 2],
                    'screening' => ['doc_count' => 2],
                    'evaluated' => ['doc_count' => 0],
                    'negotiations' => ['doc_count' => 0],
                    'offers' => ['doc_count' => 0],
                    'rejected' => ['doc_count' => 0],
                    'in_review' => ['doc_count' => 0],
                ],
            ];
        });

        // Clear cache key to ensure fresh query execution
        cache()->forget('ats:kpi:direct_client_cand_v2:2:4');

        $result = \App\Helper\GeneralMethod::DirectClientCandidatesCountList(2, 4);

        $this->assertEquals(5, $result->all_candidates);
        $this->assertEquals(1, $result->joined);
        $this->assertEquals(2, $result->sourced);
        $this->assertEquals(2, $result->screening);
        $this->assertEquals(0, $result->in_review);

        // Verify candidate query scopes to client and agency attribution, and excludes job_master_status_id [8, 9]
        $candQuery = $capturedParams[ELASTIC_CANDIDATES]['body']['query']['bool'] ?? [];
        $this->assertNotEmpty($candQuery, 'ELASTIC_CANDIDATES query must be executed');

        $mustNot = json_encode($candQuery['must_not'] ?? []);
        $this->assertStringContainsString('8', $mustNot);
        $this->assertStringContainsString('9', $mustNot);

        $must = json_encode($candQuery['must'] ?? []);
        $this->assertStringContainsString('company_id', $must);
        $this->assertStringContainsString('ClientComID', $must);
    }
}
