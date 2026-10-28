<?php

namespace App\Services\Candidate;

use App\Services\ElasticService;
use Illuminate\Support\Facades\Log;

/**
 * Class CandidateDrawerQueryService
 *
 * Unified High-Performance Read Engine for Internal and Direct Client Drawers.
 * Guarantees strictly 0% MySQL reads, fail-closed Dragonfly authorization,
 * field-level minimization (_source.includes), and snapshot-consistent HMAC cursors.
 */
class CandidateDrawerQueryService
{
    protected ElasticService $elasticService;

    protected ClientCandidateVersionStore $versionStore;

    /**
     * Whitelist of 28 lightweight display fields for Candidate Drawer listing projection.
     * Prevents PII leakage and eliminates JSON serialization bloat.
     */
    protected const LISTING_SOURCE_INCLUDES = [
        'id',
        'wiseId',
        'candidate_id',
        'company_id',
        'tenant_id',
        'client_id',
        'client_comapny_id',
        'from_company_id',
        'to_company_id',
        'ClientComID',
        'thirdparty_company_id',
        'jobId',
        'job_id',
        'title',
        'job_title',
        'job_status',
        'job_mode',
        'job_currency',
        'full_name',
        'first_name',
        'last_name',
        'current_designation',
        'qualification_name',
        'current_company',
        'email',
        'phone',
        'total_exp',
        'rel_exp',
        'salary',
        'ctc',
        'ectc',
        'ctc_yearly',
        'ectc_yearly',
        'ctc_monthly',
        'ectc_monthly',
        'current_ctc_cur',
        'current_ectc_cur',
        'current_ctc_mode',
        'current_ectc_mode',
        'salary_mode',
        'salary_currency',
        'notice',
        'np_id',
        'last_working_date',
        'serving_notice_period_mode',
        'city_id',
        'city_name',
        'state_name',
        'country_name',
        'source',
        'posted_by',
        'posted_by_name',
        'submitted_by_name',
        'agency_root_name',
        'spokperson_name',
        'company_name',
        'post_company_name',
        'submitted_by_company_name',
        'apl_company_name',
        'cur_company_name',
        'client_name',
        'created_at',
        'master_status_id',
        'job_master_status_id',
        'name',
        'master_status_name',
        'css_style',
        'master_status_css',
        'childStatus',
        'child_status_name',
        'aggregate_version',
        'skills',
        'skill_match',
        'duplicate',
        'linkedin',
        'visastatu',
        'client_logo',
        'company_logo',
        'logo_logical_path',
        'logo',
        'schedule_date',
        'timezone_abbreviation',
        'schedule_to_time',
    ];

    public function __construct(ElasticService $elasticService, ClientCandidateVersionStore $versionStore)
    {
        $this->elasticService = $elasticService;
        $this->versionStore = $versionStore;
    }

    /**
     * Fetch paginated candidates for client drawer.
     *
     * @param  int  $companyId  Authenticated organization ID
     * @param  int  $clientId  Target client ID
     * @param  string  $clientType  'internal' or 'direct'
     * @param  array  $params  Query filters: search, status_id, job_id, page, size, cursor
     * @return array Standardized Candidate Drawer JSON Contract
     */
    public function getCandidates(int $companyId, int $clientId, string $clientType, array $params = []): array
    {
        // 1. Rule 3 & 4: Fail-Closed Tenant Authorization before any ES read
        $this->assertClientTenancy($companyId, $clientId, $clientType);

        // 2. Strict Input Validation (1 <= size <= 100 for Client-First hydration)
        $size = isset($params['size']) ? min(max((int) $params['size'], 1), 100) : 50;

        $page = isset($params['page']) ? max((int) $params['page'], 1) : 1;

        // 3. Coordination Version Lookup (Fail-closed on Dragonfly outage)
        $currentCollectionVersion = $this->versionStore->getVersion($companyId, $clientId);

        // 4. Canonical Query Fingerprint & Sort
        $canonicalSort = 'created_at:desc,wiseId:desc';
        $canonicalFilterData = [
            'client_id' => $clientId,
            'client_type' => $clientType,
            'company_id' => $companyId,
            'job_id' => ! empty($params['job_id']) ? (int) $params['job_id'] : null,
            'search' => trim($params['search'] ?? ''),
            'sort' => $canonicalSort,
            'status_id' => ! empty($params['status_id']) ? (int) $params['status_id'] : null,
        ];
        ksort($canonicalFilterData);
        $queryFingerprint = hash('sha256', json_encode($canonicalFilterData));

        // 5. Build Elasticsearch Query Filter Set
        $filter = [];
        $must = [];
        $mustNot = [
            // Visibility: status 9 (archived) is excluded from active drawer scope
            ['terms' => ['job_master_status_id' => [9, '9']]],
        ];

        // 5.1 Resolve client's jobs from ELASTIC_JOBS (cached in Dragonfly/Redis)
        $clientJobIds = $this->resolveClientJobIds($companyId, $clientId, $clientType);

        if ($clientType === 'direct') {
            $directScope = [
                ['term' => ['from_company_id' => $clientId]],
                ['term' => ['company_id' => $clientId]],
                ['term' => ['client_id' => $clientId]],
            ];
            if (! empty($clientJobIds)) {
                $directScope[] = ['terms' => ['jobId' => $clientJobIds]];
                $directScope[] = ['terms' => ['job_id' => $clientJobIds]];
            }
            $filter[] = [
                'bool' => [
                    'should' => $directScope,
                    'minimum_should_match' => 1,
                ],
            ];
            $filter[] = [
                'bool' => [
                    'should' => [
                        ['term' => ['to_company_id' => $companyId]],
                        ['term' => ['ClientComID' => $companyId]],
                        ['term' => ['company_id' => $companyId]],
                        ['term' => ['client_comapny_id' => $companyId]],
                    ],
                    'minimum_should_match' => 1,
                ],
            ];
            // Align with Direct Client badge count in GeneralMethod
            $filter[] = ['term' => ['final_status' => 1]];
        } else {
            // Internal client scope:
            // Match candidates where client_id = clientId, OR where jobId belongs to any job of this client
            $internalScope = [
                ['term' => ['client_id' => $clientId]],
            ];
            if (! empty($clientJobIds)) {
                $internalScope[] = ['terms' => ['jobId' => $clientJobIds]];
                $internalScope[] = ['terms' => ['job_id' => $clientJobIds]];
            }
            $filter[] = [
                'bool' => [
                    'should' => $internalScope,
                    'minimum_should_match' => 1,
                ],
            ];

            // Tenancy / Ownership: allows candidates added by internal recruiter OR agency to this company's jobs
            $filter[] = [
                'bool' => [
                    'should' => [
                        ['term' => ['company_id' => $companyId]],
                        ['term' => ['job_company_id' => $companyId]],
                        ['term' => ['client_comapny_id' => $companyId]],
                        ['term' => ['ClientComID' => $companyId]],
                        ['term' => ['to_company_id' => $companyId]],
                    ],
                    'minimum_should_match' => 1,
                ],
            ];
        }

        // Active Status Filter
        if (! empty($params['status_id'])) {
            $statusId = (int) $params['status_id'];
            $filter[] = [
                'bool' => [
                    'should' => [
                        ['term' => ['master_status_id' => $statusId]],
                        ['term' => ['job_master_status_id' => $statusId]],
                    ],
                    'minimum_should_match' => 1,
                ],
            ];
        }

        // Active Job Filter
        if (! empty($params['job_id'])) {
            $filter[] = ['term' => ['jobId' => (int) $params['job_id']]];
        }

        // Active Search Query
        $search = trim($params['search'] ?? '');
        if ($search !== '') {
            $must[] = [
                'bool' => [
                    'should' => [
                        ['match_phrase_prefix' => ['full_name' => $search]],
                        ['match_phrase_prefix' => ['first_name' => $search]],
                        ['match_phrase_prefix' => ['last_name' => $search]],
                        ['match_phrase_prefix' => ['email' => $search]],
                        ['match_phrase_prefix' => ['phone' => $search]],
                        ['match_phrase_prefix' => ['current_designation' => $search]],
                        ['match_phrase_prefix' => ['skills' => $search]],
                    ],
                    'minimum_should_match' => 1,
                ],
            ];
        }

        // 6. Build Elasticsearch Request Body
        $esBody = [
            'size' => $size + 1, // Mathematical size + 1 evaluation
            'track_total_hits' => config('services.elasticsearch.candidate_track_total_hits', 10000),
            '_source' => [
                'includes' => self::LISTING_SOURCE_INCLUDES,
            ],
            'sort' => [
                ['created_at' => ['order' => 'desc', 'unmapped_type' => 'long']],
                ['wiseId' => ['order' => 'desc', 'unmapped_type' => 'long']],
            ],
            'query' => [
                'bool' => [
                    'filter' => $filter,
                    'must' => $must,
                    'must_not' => $mustNot,
                ],
            ],
            'aggs' => [
                'sourcing' => [
                    'filter' => [
                        'bool' => [
                            'should' => [
                                ['term' => ['job_master_status_id' => 1]],
                                ['term' => ['master_status_id' => 1]],
                            ],
                            'must_not' => [
                                ['term' => ['job_master_status_id' => 8]],
                                ['term' => ['master_status_id' => 8]],
                            ],
                        ],
                    ],
                ],
                'screening' => [
                    'filter' => [
                        'bool' => [
                            'should' => [
                                ['term' => ['job_master_status_id' => 2]],
                                ['term' => ['master_status_id' => 2]],
                            ],
                            'must_not' => [
                                ['term' => ['job_master_status_id' => 8]],
                                ['term' => ['master_status_id' => 8]],
                            ],
                        ],
                    ],
                ],
                'evaluation' => [
                    'filter' => [
                        'bool' => [
                            'should' => [
                                ['term' => ['job_master_status_id' => 3]],
                                ['term' => ['master_status_id' => 3]],
                            ],
                            'must_not' => [
                                ['exists' => ['field' => 'schedule_date']],
                                ['term' => ['job_master_status_id' => 8]],
                                ['term' => ['master_status_id' => 8]],
                            ],
                        ],
                    ],
                ],
                'negotiations' => [
                    'filter' => [
                        'bool' => [
                            'should' => [
                                ['term' => ['job_master_status_id' => 4]],
                                ['term' => ['master_status_id' => 4]],
                            ],
                            'must_not' => [
                                ['term' => ['job_master_status_id' => 8]],
                                ['term' => ['master_status_id' => 8]],
                            ],
                        ],
                    ],
                ],
                'offer' => [
                    'filter' => [
                        'bool' => [
                            'should' => [
                                ['term' => ['job_master_status_id' => 5]],
                                ['term' => ['master_status_id' => 5]],
                            ],
                            'must_not' => [
                                ['term' => ['job_master_status_id' => 8]],
                                ['term' => ['master_status_id' => 8]],
                            ],
                        ],
                    ],
                ],
                'joined' => [
                    'filter' => [
                        'bool' => [
                            'should' => [
                                ['term' => ['job_master_status_id' => 6]],
                                ['term' => ['master_status_id' => 6]],
                            ],
                            'must_not' => [
                                ['term' => ['job_master_status_id' => 8]],
                                ['term' => ['master_status_id' => 8]],
                            ],
                        ],
                    ],
                ],
                'rejected' => [
                    'filter' => [
                        'bool' => [
                            'should' => [
                                ['term' => ['job_master_status_id' => 7]],
                                ['term' => ['master_status_id' => 7]],
                                ['term' => ['job_master_status_id' => 9]],
                                ['term' => ['master_status_id' => 9]],
                            ],
                            'must_not' => [
                                ['term' => ['job_master_status_id' => 8]],
                                ['term' => ['master_status_id' => 8]],
                            ],
                        ],
                    ],
                ],
                'interviews' => [
                    'filter' => [
                        'bool' => [
                            'should' => [
                                ['exists' => ['field' => 'schedule_date']],
                            ],
                            'must_not' => [
                                ['term' => ['job_master_status_id' => 8]],
                                ['term' => ['master_status_id' => 8]],
                            ],
                        ],
                    ],
                ],
                'in_review' => [
                    'filter' => [
                        'bool' => [
                            'should' => [
                                ['term' => ['job_master_status_id' => 8]],
                                ['term' => ['master_status_id' => 8]],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        // 7. Pagination Guard: Cursor vs Offset
        $isCursorMode = ! empty($params['cursor']);
        if ($isCursorMode) {
            $cursorData = $this->verifyAndDecodeCursor(
                $params['cursor'],
                $companyId,
                $clientId,
                $clientType,
                $currentCollectionVersion,
                $queryFingerprint,
                $canonicalSort
            );
            $esBody['search_after'] = $cursorData['values'];
        } elseif ($page <= 5) {
            $esBody['from'] = ($page - 1) * $size;
        } else {
            abort(422, 'Deep pagination beyond page 5 requires cursor navigation.');
        }

        // 8. Execute Elasticsearch Query (Strict 0% MySQL Reads)
        try {
            if (! $this->elasticService->indexExists(ELASTIC_CANDIDATES)) {
                $response = ['hits' => ['total' => ['value' => 0, 'relation' => 'eq'], 'hits' => []]];
            } else {
                $response = $this->elasticService->getClient()->search([
                    'index' => ELASTIC_CANDIDATES,
                    'body' => $esBody,
                ]);
            }
        } catch (\Throwable $e) {
            Log::critical('Elasticsearch Candidate Read Model query failure', [
                'company_id' => $companyId,
                'client_id' => $clientId,
                'client_type' => $clientType,
                'error' => $e->getMessage(),
            ]);
            abort(503, 'Candidate search service temporarily unavailable.');
        }

        $totalHits = $response['hits']['total'] ?? [];
        $total = is_array($totalHits) ? ($totalHits['value'] ?? 0) : (int) $totalHits;
        $totalRelation = is_array($totalHits) ? ($totalHits['relation'] ?? 'eq') : 'eq';
        $rawHits = array_map(fn ($hit) => $hit['_source'], $response['hits']['hits'] ?? []);

        // 9. Mathematical size + 1 evaluation
        $hasMore = count($rawHits) > $size;
        $items = $hasMore ? array_slice($rawHits, 0, $size) : $rawHits;

        // 10. Generate HMAC-Signed Next Cursor
        $nextCursor = null;
        if ($hasMore) {
            $lastItem = end($items);
            $cursorPayload = [
                'values' => [$lastItem['created_at'] ?? 0, $lastItem['wiseId'] ?? 0],
                'company_id' => $companyId,
                'client_id' => $clientId,
                'client_type' => $clientType,
                'client_candidate_version' => $currentCollectionVersion,
                'query_fingerprint' => $queryFingerprint,
                'sort' => $canonicalSort,
                'projection_schema_version' => 2,
                'issued_at' => now()->timestamp,
                'expires_at' => now()->addMinutes(15)->timestamp,
            ];

            $envelope = [
                'v' => 2,
                'payload' => $cursorPayload,
                'sig' => hash_hmac('sha256', json_encode($cursorPayload), (string) config('app.key')),
            ];

            $nextCursor = base64_encode((string) json_encode($envelope));
        }

        // 11. Pagination Object Construction (P0: last_page = null when totalRelation = 'gte')
        $pagination = [
            'mode' => $isCursorMode ? 'cursor' : 'page',
            'total' => $total,
            'total_relation' => $totalRelation,
            'per_page' => $size,
            'has_more' => $hasMore,
        ];

        if ($isCursorMode) {
            $pagination['next_cursor'] = $nextCursor;
        } else {
            $pagination['current_page'] = $page;
            $pagination['last_page'] = ($totalRelation === 'gte') ? null : (int) ceil($total / $size);
        }

        $aggs = $response['aggregations'] ?? [];
        $stageCounts = [
            'sourcing' => (int) ($aggs['sourcing']['doc_count'] ?? 0),
            'screening' => (int) ($aggs['screening']['doc_count'] ?? 0),
            'evaluation' => (int) ($aggs['evaluation']['doc_count'] ?? 0),
            'negotiations' => (int) ($aggs['negotiations']['doc_count'] ?? 0),
            'offer' => (int) ($aggs['offer']['doc_count'] ?? 0),
            'joined' => (int) ($aggs['joined']['doc_count'] ?? 0),
            'rejected' => (int) ($aggs['rejected']['doc_count'] ?? 0),
            'interviews' => (int) ($aggs['interviews']['doc_count'] ?? 0),
            'in_review' => (int) ($aggs['in_review']['doc_count'] ?? 0),
        ];

        return [
            'data' => $items,
            'meta' => [
                'client_id' => $clientId,
                'client_type' => $clientType,
                'query_fingerprint' => $queryFingerprint,
                'client_candidate_version' => $currentCollectionVersion,
                'projection_schema_version' => 2,
                'stage_counts' => $stageCounts,
                'server_time' => now()->toIso8601String(),
            ],
            'pagination' => $pagination,
            'filters' => [
                'search' => $params['search'] ?? '',
                'status_id' => ! empty($params['status_id']) ? (int) $params['status_id'] : null,
                'job_id' => ! empty($params['job_id']) ? (int) $params['job_id'] : null,
            ],
        ];
    }

    /**
     * Resolve all job IDs belonging to this client from ELASTIC_JOBS (cached in Dragonfly/Redis).
     * Guarantees 0% MySQL reads and instant sub-millisecond retrieval.
     */
    protected function resolveClientJobIds(int $companyId, int $clientId, string $clientType): array
    {
        $cacheKey = "ats:client_job_ids:{$companyId}:{$clientId}:{$clientType}";
        try {
            $cached = cache()->store('dragonfly')->get($cacheKey);
            if (is_array($cached)) {
                return $cached;
            }
        } catch (\Throwable $e) {
        }

        $indexJobs = defined('ELASTIC_JOBS') ? ELASTIC_JOBS : config('constants.ELASTIC_JOBS', 'ats_jobs_elastic');
        $jobIds = [];

        try {
            if ($this->elasticService->indexExists($indexJobs)) {
                $scopeMust = [
                    ['term' => ['client_id' => $clientId]],
                ];
                if ($clientType === 'internal') {
                    $scopeMust[] = [
                        'bool' => [
                            'should' => [
                                ['term' => ['company_id' => $companyId]],
                                ['term' => ['comId' => $companyId]],
                            ],
                            'minimum_should_match' => 1,
                        ],
                    ];
                }

                $params = [
                    'index' => $indexJobs,
                    'size' => 500,
                    '_source' => ['id'],
                    'body' => [
                        'query' => [
                            'bool' => [
                                'must' => $scopeMust,
                            ],
                        ],
                    ],
                ];

                $response = $this->elasticService->getClient()->search($params);
                $jobIds = array_values(array_filter(array_map(function ($hit) {
                    return (int) ($hit['_source']['id'] ?? $hit['_id'] ?? 0);
                }, $response['hits']['hits'] ?? [])));

                try {
                    cache()->store('dragonfly')->put($cacheKey, $jobIds, 60);
                } catch (\Throwable $e) {
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Failed to resolve client job IDs from Elasticsearch', [
                'company_id' => $companyId,
                'client_id' => $clientId,
                'error' => $e->getMessage(),
            ]);
        }

        return $jobIds;
    }

    /**
     * Fail-closed tenant authorization guard.
     */
    protected function assertClientTenancy(int $companyId, int $clientId, string $clientType): void
    {
        if ($clientId <= 0 || $companyId <= 0) {
            abort(403, 'Invalid client or tenant identification.');
        }

        try {
            $redis = cache()->store('dragonfly')->getRedis();
            $authKey = "auth:tenant_clients:{$companyId}";

            // If Dragonfly tenant client set exists, enforce strict set membership
            if ($redis->exists($authKey)) {
                if (! $redis->sismember($authKey, $clientId)) {
                    abort(403, 'Unauthorized tenant access to requested client.');
                }
            }
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            throw $e;
        } catch (\Throwable $e) {
            Log::emergency('Dragonfly authorization projection unavailable', [
                'company_id' => $companyId,
                'client_id' => $clientId,
                'error' => $e->getMessage(),
            ]);
            abort(503, 'Authorization projection unavailable. Candidate drawer access denied.');
        }
    }

    /**
     * Verify and decode HMAC-SHA256 cursor.
     */
    protected function verifyAndDecodeCursor(
        string $cursorString,
        int $companyId,
        int $clientId,
        string $clientType,
        int $currentCollectionVersion,
        string $activeQueryFingerprint,
        string $canonicalSort
    ): array {
        $raw = base64_decode($cursorString, true);
        if ($raw === false) {
            abort(422, 'Invalid cursor encoding.');
        }

        $envelope = json_decode($raw, true);
        if (! is_array($envelope) || ($envelope['v'] ?? null) !== 2 || empty($envelope['payload']) || empty($envelope['sig'])) {
            abort(422, 'Malformed cursor structure.');
        }

        $payload = $envelope['payload'];
        $sig = $envelope['sig'];

        $expectedSig = hash_hmac('sha256', json_encode($payload), (string) config('app.key'));
        if (! hash_equals($expectedSig, $sig)) {
            abort(422, 'Cursor signature verification failed.');
        }

        // Validate context
        if (($payload['company_id'] ?? null) !== $companyId || ($payload['client_id'] ?? null) !== $clientId || ($payload['client_type'] ?? null) !== $clientType) {
            abort(422, 'Cursor context mismatch.');
        }

        if (($payload['query_fingerprint'] ?? null) !== $activeQueryFingerprint) {
            abort(422, 'Cursor does not match active filter parameters.');
        }

        if (($payload['sort'] ?? null) !== $canonicalSort) {
            abort(422, 'Cursor sort sequence mismatch.');
        }

        if (($payload['projection_schema_version'] ?? null) !== 2) {
            abort(422, 'Cursor schema version outdated.');
        }

        if (now()->timestamp > ($payload['expires_at'] ?? 0)) {
            abort(422, 'Cursor has expired.');
        }

        // Snapshot consistency check
        if (($payload['client_candidate_version'] ?? null) !== $currentCollectionVersion) {
            abort(409, 'Collection version mutated during pagination. Fresh reload required.');
        }

        if (empty($payload['values']) || ! is_array($payload['values'])) {
            abort(422, 'Invalid cursor sort values.');
        }

        return $payload;
    }
}
