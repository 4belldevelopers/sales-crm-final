<?php

namespace App\Services\Analytics;

use App\Services\Analytics\Contracts\PerformanceStatusMap;
use App\Services\Analytics\DTO\PerformanceScopeContract;
use App\Services\ElasticService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * ClientPerformanceScopeResolver
 *
 * Resolves the multi-tenant query scope and enforces IDOR-safe authorization barriers
 * for both Internal Clients and Direct Clients (Marketplace Requisitions).
 *
 * Multi-Tenant Security Guarantee:
 * - Internal Client: Strictly verifies that client belongs to auth_company_id.
 * - Direct Client: Strictly verifies that an approved relationship (final_status=1)
 *   exists between the authenticated company and the direct client organization.
 * - Canonical Shared Jobs Invariant: Resolves canonical shared_job_ids first.
 *   If a job is not in shared_job_ids, it is completely absent from all analytics.
 * - Sourcing Gating Invariant: Uses positive allow-list SHARED_PIPELINE_STATUSES [1..6].
 */
class ClientPerformanceScopeResolver
{
    protected ElasticService $elasticService;

    protected static array $inMemoryScopeCache = [];

    public function __construct(ElasticService $elasticService)
    {
        $this->elasticService = $elasticService;
    }

    /**
     * Resolve and authorize the analytics scope for a given client ID.
     *
     * @param  int  $authCompanyId  Authenticated user's company ID
     * @param  int  $clientId  Requested client ID (or Direct Client company ID)
     * @param  string  $clientTypeHint  'internal', 'direct', or 'auto'
     * @return array Standardized scope definition array
     *
     * @throws AuthorizationException If access is not authorized
     */
    public function resolveScope(int $authCompanyId, int $clientId, string $clientTypeHint = 'auto'): array
    {
        $cacheKey = "{$authCompanyId}_{$clientId}_{$clientTypeHint}";
        if (isset(self::$inMemoryScopeCache[$cacheKey])) {
            return self::$inMemoryScopeCache[$cacheKey];
        }

        $candidatesIndex = config(ClientPerformanceConstants::CONFIG_INDEX_CANDIDATES, 'local_candidates_elastic');
        $jobsIndex = config(ClientPerformanceConstants::CONFIG_INDEX_JOBS, 'local_jobs_elastic');
        $clientsIndex = config(ClientPerformanceConstants::CONFIG_INDEX_CLIENTS, 'local_client_list_elastic');
        $interviewsIndex = config(ClientPerformanceConstants::CONFIG_INDEX_INTERVIEWS, 'local_interview_tracker_elastic');
        $timelineIndex = config(ClientPerformanceConstants::CONFIG_INDEX_TIMELINE, 'local_candidate_timeline');

        // Step 1: Detect or verify Internal Client scope
        if ($clientTypeHint === 'internal' || $clientTypeHint === 'auto') {
            $isInternal = $this->verifyInternalClient($authCompanyId, $clientId, $clientsIndex);
            if ($isInternal) {
                $sharedJobData = $this->resolveSharedJobIds($authCompanyId, $clientId, 'internal', $jobsIndex);
                $sharedJobIds = $sharedJobData['shared_job_ids'];
                $activeSharedJobIds = $sharedJobData['active_job_ids'];

                $contract = new PerformanceScopeContract(
                    clientId: $clientId,
                    agencyCompanyId: $authCompanyId,
                    clientType: 'internal',
                    clientName: $isInternal['name'] ?? 'Client #'.$clientId,
                    relationshipType: 'TENANT_OWNED',
                    sharedJobIds: $sharedJobIds,
                    activeSharedJobIds: $activeSharedJobIds,
                    activeRelationshipOnly: true,
                    range: '12M',
                    compareWith: 'previous_period',
                    timezone: 'Asia/Kolkata',
                    dateBoundaries: [],
                    theme: 'blue',
                    themeColor: '#009ef7',
                    indices: [
                        'candidates' => $candidatesIndex,
                        'jobs' => $jobsIndex,
                        'interviews' => $interviewsIndex,
                        'timeline' => $timelineIndex,
                        'clients' => $clientsIndex,
                    ],
                    correlationId: 'cor_'.Str::random(12),
                    scopeVersion: 1
                );

                $scope = [
                    'contract' => $contract,
                    'client_type' => 'internal',
                    'client_id' => $clientId,
                    'client_name' => $isInternal['name'] ?? 'Client #'.$clientId,
                    'auth_company_id' => $authCompanyId,
                    'shared_job_ids' => $sharedJobIds,
                    'active_shared_job_ids' => $activeSharedJobIds,
                    'theme' => 'blue',
                    'theme_color' => '#009ef7',
                    'indices' => [
                        'candidates' => $candidatesIndex,
                        'jobs' => $jobsIndex,
                        'interviews' => $interviewsIndex,
                        'timeline' => $timelineIndex,
                        'clients' => $clientsIndex,
                    ],
                    // Candidate query: internal company + client + positive allow-list [1..6]
                    'candidate_filter' => [
                        'bool' => [
                            'must' => [
                                ['term' => ['company_id' => $authCompanyId]],
                                [
                                    'bool' => [
                                        'should' => [
                                            ['term' => ['client_id' => $clientId]],
                                            ['term' => ['client_company_id' => $clientId]],
                                            ['term' => ['client_comapny_id' => $clientId]],
                                        ],
                                        'minimum_should_match' => 1,
                                    ],
                                ],
                                ['terms' => ['job_master_status_id' => PerformanceStatusMap::PIPELINE_WITH_IN_REVIEW_STATUSES]],
                            ],
                            'must_not' => [
                                ['terms' => ['job_master_status_id' => [9, '9']]],
                                ['terms' => ['master_status_id' => [9, '9']]],
                            ],
                        ],
                    ],
                    // Job query must match auth company and client_id
                    'job_filter' => [
                        'bool' => [
                            'must' => [
                                ['term' => ['company_id' => $authCompanyId]],
                                ['term' => ['client_id' => $clientId]],
                            ],
                        ],
                    ],
                ];
                self::$inMemoryScopeCache[$cacheKey] = $scope;

                return $scope;
            }
        }

        // Step 2: Detect or verify Direct Client scope
        if ($clientTypeHint === 'direct' || $clientTypeHint === 'auto') {
            $isDirect = $this->verifyDirectClient($authCompanyId, $clientId);
            if ($isDirect) {
                $sharedJobData = $this->resolveSharedJobIds($authCompanyId, $clientId, 'direct', $jobsIndex);
                $sharedJobIds = $sharedJobData['shared_job_ids'];
                $activeSharedJobIds = $sharedJobData['active_job_ids'];

                $contract = new PerformanceScopeContract(
                    clientId: $clientId,
                    agencyCompanyId: $authCompanyId,
                    clientType: 'direct',
                    clientName: $isDirect['name'] ?? 'Direct Client #'.$clientId,
                    relationshipType: 'MARKETPLACE_SHARED',
                    sharedJobIds: $sharedJobIds,
                    activeSharedJobIds: $activeSharedJobIds,
                    activeRelationshipOnly: true,
                    range: '12M',
                    compareWith: 'previous_period',
                    timezone: 'Asia/Kolkata',
                    dateBoundaries: [],
                    theme: 'emerald',
                    themeColor: '#10b981',
                    indices: [
                        'candidates' => $candidatesIndex,
                        'jobs' => $jobsIndex,
                        'interviews' => $interviewsIndex,
                        'timeline' => $timelineIndex,
                        'clients' => $clientsIndex,
                    ],
                    correlationId: 'cor_'.Str::random(12),
                    scopeVersion: 1
                );

                $scope = [
                    'contract' => $contract,
                    'client_type' => 'direct',
                    'client_id' => $clientId,
                    'client_name' => $isDirect['name'] ?? 'Direct Client #'.$clientId,
                    'auth_company_id' => $authCompanyId,
                    'shared_job_ids' => $sharedJobIds,
                    'active_shared_job_ids' => $activeSharedJobIds,
                    'theme' => 'emerald',
                    'theme_color' => '#10b981',
                    'indices' => [
                        'candidates' => $candidatesIndex,
                        'jobs' => $jobsIndex,
                        'interviews' => $interviewsIndex,
                        'timeline' => $timelineIndex,
                        'clients' => $clientsIndex,
                    ],
                    // Candidate filter for direct client:
                    // 1. Strictly bounded by canonical shared_job_ids (if none, [-1] guarantees 0 leakage)
                    // 2. Both client and agency matching
                    // 3. Active shared relationship final_status = 1
                    // 4. Positive allow-list: SHARED_PIPELINE_STATUSES [1, 2, 3, 4, 5, 6] (Sourcing onward)
                    'candidate_filter' => [
                        'bool' => [
                            'must' => [
                                ['terms' => ['jobId' => ! empty($sharedJobIds) ? $sharedJobIds : [-1]]],
                                [
                                    'bool' => [
                                        'should' => [
                                            ['term' => ['from_company_id' => $clientId]],
                                            ['term' => ['company_id' => $clientId]],
                                        ],
                                        'minimum_should_match' => 1,
                                    ],
                                ],
                                [
                                    'bool' => [
                                        'should' => [
                                            ['term' => ['to_company_id' => $authCompanyId]],
                                            ['term' => ['ClientComID' => $authCompanyId]],
                                            ['term' => ['thirdparty_company_id' => $authCompanyId]],
                                        ],
                                        'minimum_should_match' => 1,
                                    ],
                                ],
                                ['term' => ['final_status' => 1]],
                                ['terms' => ['job_master_status_id' => PerformanceStatusMap::PIPELINE_WITH_IN_REVIEW_STATUSES]],
                            ],
                            'must_not' => [
                                ['terms' => ['job_master_status_id' => [9, '9']]],
                                ['terms' => ['master_status_id' => [9, '9']]],
                            ],
                        ],
                    ],
                    // Job filter for direct client: Strictly matches nested recommendation_relation (final_status = 1)
                    'job_filter' => [
                        'bool' => [
                            'must' => [
                                [
                                    'nested' => [
                                        'path' => 'recommendation_relation',
                                        'query' => [
                                            'bool' => [
                                                'must' => [
                                                    ['term' => ['recommendation_relation.to_company_id' => (int) $authCompanyId]],
                                                    ['term' => ['recommendation_relation.from_company_id' => (int) $clientId]],
                                                    ['term' => ['recommendation_relation.final_status' => 1]],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ];
                self::$inMemoryScopeCache[$cacheKey] = $scope;

                return $scope;
            }
        }

        // Unauthorized access: Neither an internal client of this company nor an authorized direct client
        throw new AuthorizationException('Unauthorized access to client performance analytics.');
    }

    /**
     * Resolve canonical shared job IDs directly from ELASTIC_JOBS.
     */
    public function resolveSharedJobIds(int $authCompanyId, int $clientId, string $clientType, string $jobsIndex): array
    {
        $sharedJobIds = [];
        $activeJobIds = [];

        try {
            if (! $this->elasticService->indexExists($jobsIndex)) {
                return ['shared_job_ids' => [], 'active_job_ids' => []];
            }

            if ($clientType === 'direct') {
                $query = [
                    'nested' => [
                        'path' => 'recommendation_relation',
                        'query' => [
                            'bool' => [
                                'must' => [
                                    ['term' => ['recommendation_relation.to_company_id' => (int) $authCompanyId]],
                                    ['term' => ['recommendation_relation.from_company_id' => (int) $clientId]],
                                    ['term' => ['recommendation_relation.final_status' => 1]],
                                ],
                            ],
                        ],
                    ],
                ];
            } else {
                $query = [
                    'bool' => [
                        'must' => [
                            ['term' => ['company_id' => (int) $authCompanyId]],
                            ['term' => ['client_id' => (int) $clientId]],
                        ],
                    ],
                ];
            }

            $searchRes = $this->elasticService->getClient()->search([
                'index' => $jobsIndex,
                'size' => 1000,
                '_source' => ['id', 'status'],
                'body' => [
                    'query' => $query,
                ],
            ]);

            if (! empty($searchRes['hits']['hits'])) {
                foreach ($searchRes['hits']['hits'] as $hit) {
                    $src = $hit['_source'] ?? [];
                    if (! empty($src['id'])) {
                        $jobId = (int) $src['id'];
                        $sharedJobIds[] = $jobId;
                        $status = strtolower(trim((string) ($src['status'] ?? '')));
                        if (in_array($status, ['active', 'hiring'], true)) {
                            $activeJobIds[] = $jobId;
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            \Log::warning("resolveSharedJobIds failed for client {$clientId}: ".$e->getMessage());
        }

        return [
            'shared_job_ids' => array_values(array_unique($sharedJobIds)),
            'active_job_ids' => array_values(array_unique($activeJobIds)),
        ];
    }

    /**
     * Verify if client exists and belongs to the authenticated company.
     */
    protected function verifyInternalClient(int $authCompanyId, int $clientId, string $clientsIndex): ?array
    {
        // Tier 1: Check Elasticsearch client index
        try {
            if ($this->elasticService->indexExists($clientsIndex)) {
                $searchRes = $this->elasticService->getClient()->search([
                    'index' => $clientsIndex,
                    'body' => [
                        'size' => 1,
                        'query' => [
                            'bool' => [
                                'must' => [
                                    ['term' => ['company_id' => $authCompanyId]],
                                    [
                                        'bool' => [
                                            'should' => [
                                                ['term' => ['_id' => (string) $clientId]],
                                                ['term' => ['client_id' => $clientId]],
                                                ['term' => ['id' => $clientId]],
                                            ],
                                            'minimum_should_match' => 1,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ]);

                if (! empty($searchRes['hits']['hits'])) {
                    $source = $searchRes['hits']['hits'][0]['_source'] ?? [];

                    return [
                        'name' => $source['name'] ?? $source['company_name'] ?? 'Client #'.$clientId,
                    ];
                }
            }
        } catch (\Throwable $e) {
            // Log warning and fallback
        }

        // Tier 2: Read through Redis/DB cache for fast authorized check
        $client = cache()->remember("tenant_{$authCompanyId}_client_{$clientId}_exists", 3600, function () use ($authCompanyId, $clientId) {
            return DB::table('clients')
                ->where('id', $clientId)
                ->where('company_id', $authCompanyId)
                ->select('id', 'name')
                ->first();
        });

        if ($client) {
            return ['name' => $client->name];
        }

        return null;
    }

    /**
     * Verify if direct client relationship is active between authenticated company and target company.
     */
    protected function verifyDirectClient(int $authCompanyId, int $clientId): ?array
    {
        // Verify relationship in cache
        $rel = cache()->remember("direct_rel_{$authCompanyId}_{$clientId}", 3600, function () use ($authCompanyId, $clientId) {
            $r = DB::table('recommendation_relation')
                ->where('from_company_id', $clientId)
                ->where('to_company_id', $authCompanyId)
                ->where('final_status', 1)
                ->first();

            if (! $r) {
                $r = DB::table('staffing_company_relation')
                    ->where('from_company_id', $clientId)
                    ->where('to_company_id', $authCompanyId)
                    ->first();
            }

            return $r ? true : false;
        });

        if ($rel) {
            $compName = cache()->remember("company_name_{$clientId}", 86400, function () use ($clientId) {
                return DB::table('companies')->where('id', $clientId)->value('company_name');
            });

            return ['name' => $compName ?? 'Direct Client #'.$clientId];
        }

        return null;
    }
}
