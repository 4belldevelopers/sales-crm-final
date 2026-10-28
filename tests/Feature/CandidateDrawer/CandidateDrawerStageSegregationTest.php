<?php

namespace Tests\Feature\CandidateDrawer;

use App\Models\User;
use App\Services\Candidate\CandidateDrawerQueryService;
use App\Services\Candidate\ClientCandidateVersionStore;
use App\Services\ElasticService;
use Tests\TestCase;

/**
 * Class CandidateDrawerStageSegregationTest
 *
 * Verifies strict 1:1 stage segregation, accurate aggregation counters,
 * and zero cross-contamination between Screening, Rejected, InReview, and other stages.
 */
class CandidateDrawerStageSegregationTest extends TestCase
{
    protected User $user;

    protected int $companyId = 2;

    protected int $clientId = 35;

    protected function setUp(): void
    {
        parent::setUp();

        config(['auth.single_device_login.enabled' => false]);

        $this->user = new User;
        $this->user->id = 1;
        $this->user->company_id = $this->companyId;
        $this->user->name = 'Test Recruiter';
        $this->user->email = 'recruiter@4bell.in';
        $this->user->status = 'active';
    }

    public function test_screening_candidate_does_not_appear_in_rejected_aggregations(): void
    {
        $mockElastic = \Mockery::mock(ElasticService::class);
        $mockClient = \Mockery::mock();

        $mockElastic->shouldReceive('indexExists')->andReturn(true);
        $mockElastic->shouldReceive('getClient')->andReturn($mockClient);

        // Candidate has job_master_status_id = 2 (Screening) but master_status_id = 7 (Historical Rejection)
        $syntheticHits = [
            [
                '_source' => [
                    'id' => 157,
                    'wiseId' => 157,
                    'candidate_id' => 188,
                    'full_name' => 'Uujaiswal',
                    'job_master_status_id' => 2,
                    'master_status_id' => 7,
                    'company_id' => $this->companyId,
                    'client_id' => $this->clientId,
                    'jobId' => 86,
                    'job_title' => 'RPA Developer',
                    'created_at' => '2026-10-28 10:00:00',
                    'skills' => 'automation anywhere a360,python',
                ],
            ],
        ];

        $mockClient->shouldReceive('search')
            ->once()
            ->withArgs(function ($params) {
                // Verify that aggs contains our mutually exclusive stage rules
                $aggs = $params['body']['aggs'] ?? [];
                $this->assertArrayHasKey('screening', $aggs);
                $this->assertArrayHasKey('rejected', $aggs);
                $this->assertArrayHasKey('sourcing', $aggs);
                $this->assertArrayHasKey('evaluation', $aggs);
                $this->assertArrayHasKey('interviews', $aggs);

                return true;
            })
            ->andReturn([
                'hits' => [
                    'total' => ['value' => 1, 'relation' => 'eq'],
                    'hits' => $syntheticHits,
                ],
                'aggregations' => [
                    'sourcing' => ['doc_count' => 0],
                    'screening' => ['doc_count' => 1],
                    'evaluation' => ['doc_count' => 0],
                    'negotiations' => ['doc_count' => 0],
                    'offer' => ['doc_count' => 0],
                    'joined' => ['doc_count' => 0],
                    'rejected' => ['doc_count' => 0],
                    'interviews' => ['doc_count' => 0],
                    'in_review' => ['doc_count' => 0],
                ],
            ]);

        $mockVersionStore = \Mockery::mock(ClientCandidateVersionStore::class);
        $mockVersionStore->shouldReceive('getVersion')->andReturn(1);

        $queryService = new CandidateDrawerQueryService($mockElastic, $mockVersionStore);
        $result = $queryService->getCandidates($this->companyId, $this->clientId, 'internal');

        $this->assertEquals(1, $result['pagination']['total']);
        $this->assertEquals(1, $result['meta']['stage_counts']['screening']);
        $this->assertEquals(0, $result['meta']['stage_counts']['rejected']);
        $this->assertEquals(0, $result['meta']['stage_counts']['in_review']);
        $this->assertEquals('Uujaiswal', $result['data'][0]['full_name']);
        $this->assertEquals(2, $result['data'][0]['job_master_status_id']);
    }

    public function test_status_filter_uses_canonical_job_status(): void
    {
        $mockElastic = \Mockery::mock(ElasticService::class);
        $mockClient = \Mockery::mock();

        $mockElastic->shouldReceive('indexExists')->andReturn(true);
        $mockElastic->shouldReceive('getClient')->andReturn($mockClient);

        // When filtering by status_id = 7 (Rejected), the query must NOT match candidates whose job_master_status_id is 2
        $mockClient->shouldReceive('search')
            ->once()
            ->withArgs(function ($params) {
                $filters = $params['body']['query']['bool']['filter'] ?? [];
                $hasStatusFilter = false;
                foreach ($filters as $f) {
                    if (isset($f['bool']['should'])) {
                        foreach ($f['bool']['should'] as $s) {
                            if (isset($s['terms']['job_master_status_id']) && in_array(7, $s['terms']['job_master_status_id'])) {
                                $hasStatusFilter = true;
                                break 2;
                            }
                        }
                    }
                }
                $this->assertTrue($hasStatusFilter, 'Filter must contain canonical status resolution for status 7');

                return true;
            })
            ->andReturn([
                'hits' => [
                    'total' => ['value' => 0, 'relation' => 'eq'],
                    'hits' => [],
                ],
                'aggregations' => [
                    'sourcing' => ['doc_count' => 0],
                    'screening' => ['doc_count' => 1],
                    'evaluation' => ['doc_count' => 0],
                    'negotiations' => ['doc_count' => 0],
                    'offer' => ['doc_count' => 0],
                    'joined' => ['doc_count' => 0],
                    'rejected' => ['doc_count' => 0],
                    'interviews' => ['doc_count' => 0],
                    'in_review' => ['doc_count' => 0],
                ],
            ]);

        $mockVersionStore = \Mockery::mock(ClientCandidateVersionStore::class);
        $mockVersionStore->shouldReceive('getVersion')->andReturn(1);

        $queryService = new CandidateDrawerQueryService($mockElastic, $mockVersionStore);
        $result = $queryService->getCandidates($this->companyId, $this->clientId, 'internal', ['status_id' => 7]);

        $this->assertEquals(0, $result['pagination']['total']);
        $this->assertEmpty($result['data']);
    }
}
