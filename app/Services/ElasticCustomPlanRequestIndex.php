<?php

namespace App\Services;

use App\Models\CustomPlanRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class ElasticCustomPlanRequestIndex
 *
 * Intent: Elasticsearch Read Authority and dual-store synchronization engine for Custom Plan Requests.
 * Provides CQRS read isolation (0% MySQL reads on search, filtering, pagination, and KPI counts),
 * index lifecycle provisioning, single-document atomic indexing, and bulk reconciliation.
 *
 * Index: local_custom_plan_requests (constant ELASTIC_CUSTOM_PLAN_REQUESTS)
 * Author: Antigravity AI
 * Date: October 30, 2026
 */
class ElasticCustomPlanRequestIndex
{
    public function __construct(
        protected ElasticService $elasticService
    ) {}

    /**
     * Get authoritative index name.
     */
    public function getIndexName(): string
    {
        if (defined('ELASTIC_CUSTOM_PLAN_REQUESTS')) {
            return ELASTIC_CUSTOM_PLAN_REQUESTS;
        }

        return config('constants.ELASTIC_CUSTOM_PLAN_REQUESTS', 'local_custom_plan_requests');
    }

    /**
     * Ensure the Elasticsearch index and mappings exist.
     */
    public function ensureIndexExists(): bool
    {
        $index = $this->getIndexName();

        try {
            if ($this->elasticService->indexExists($index)) {
                return true;
            }

            $mappings = [
                'properties' => [
                    'id' => ['type' => 'integer'],
                    'request_reference' => ['type' => 'keyword'],
                    'company_id' => ['type' => 'integer'],
                    'user_id' => ['type' => 'integer'],
                    'customer_name' => [
                        'type' => 'text',
                        'fields' => ['keyword' => ['type' => 'keyword', 'ignore_above' => 256]],
                    ],
                    'customer_email' => ['type' => 'keyword'],
                    'customer_mobile' => ['type' => 'keyword'],
                    'company_name' => [
                        'type' => 'text',
                        'fields' => ['keyword' => ['type' => 'keyword', 'ignore_above' => 256]],
                    ],
                    'location' => ['type' => 'text'],
                    'request_type' => ['type' => 'keyword'],
                    'package_type' => ['type' => 'keyword'],
                    'package_id' => ['type' => 'integer'],
                    'package_title' => [
                        'type' => 'text',
                        'fields' => ['keyword' => ['type' => 'keyword', 'ignore_above' => 256]],
                    ],
                    'status' => ['type' => 'keyword'],
                    'aggregate_version' => ['type' => 'integer'],
                    'message' => ['type' => 'text'],
                    'admin_notes' => ['type' => 'text'],
                    'assigned_to' => ['type' => 'integer'],
                    'updated_by' => ['type' => 'integer'],
                    'last_status_updated_at' => [
                        'type' => 'date',
                        'format' => 'yyyy-MM-dd HH:mm:ss||yyyy-MM-dd\'T\'HH:mm:ssZZ||epoch_millis||strict_date_optional_time',
                    ],
                    'created_at' => [
                        'type' => 'date',
                        'format' => 'yyyy-MM-dd HH:mm:ss||yyyy-MM-dd\'T\'HH:mm:ssZZ||epoch_millis||strict_date_optional_time',
                    ],
                    'updated_at' => [
                        'type' => 'date',
                        'format' => 'yyyy-MM-dd HH:mm:ss||yyyy-MM-dd\'T\'HH:mm:ssZZ||epoch_millis||strict_date_optional_time',
                    ],
                ],
            ];

            $this->elasticService->createIndex($index, $mappings);

            return true;
        } catch (\Throwable $e) {
            Log::error('ElasticCustomPlanRequestIndex: Failed to create index '.$index.': '.$e->getMessage());

            return false;
        }
    }

    /**
     * Format a model or row object for Elasticsearch storage.
     */
    public function formatDocument($cpr): array
    {
        $pkgTitle = is_object($cpr) ? ($cpr->package_title ?? null) : ($cpr['package_title'] ?? null);
        $pkgType = is_object($cpr) ? ($cpr->package_type ?? 'operational') : ($cpr['package_type'] ?? 'operational');

        if (empty($pkgTitle) || strcasecmp(trim($pkgTitle), 'free') === 0 || strcasecmp(trim($pkgTitle), 'custom package') === 0) {
            $pkgTitle = ucfirst($pkgType ?? 'operational').' Custom Package';
        }

        $createdAt = is_object($cpr) ? ($cpr->created_at ?? null) : ($cpr['created_at'] ?? null);
        if ($createdAt instanceof Carbon) {
            $createdAt = $createdAt->format('Y-m-d H:i:s');
        } elseif (is_string($createdAt) && ! empty($createdAt)) {
            $createdAt = date('Y-m-d H:i:s', strtotime($createdAt));
        }

        $updatedAt = is_object($cpr) ? ($cpr->updated_at ?? null) : ($cpr['updated_at'] ?? null);
        if ($updatedAt instanceof Carbon) {
            $updatedAt = $updatedAt->format('Y-m-d H:i:s');
        } elseif (is_string($updatedAt) && ! empty($updatedAt)) {
            $updatedAt = date('Y-m-d H:i:s', strtotime($updatedAt));
        }

        $statusUpdatedAt = is_object($cpr) ? ($cpr->last_status_updated_at ?? null) : ($cpr['last_status_updated_at'] ?? null);
        if ($statusUpdatedAt instanceof Carbon) {
            $statusUpdatedAt = $statusUpdatedAt->format('Y-m-d H:i:s');
        } elseif (is_string($statusUpdatedAt) && ! empty($statusUpdatedAt)) {
            $statusUpdatedAt = date('Y-m-d H:i:s', strtotime($statusUpdatedAt));
        }

        return [
            'id' => (int) (is_object($cpr) ? $cpr->id : $cpr['id']),
            'request_reference' => (string) (is_object($cpr) ? $cpr->request_reference : $cpr['request_reference']),
            'company_id' => ! empty(is_object($cpr) ? $cpr->company_id : $cpr['company_id']) ? (int) (is_object($cpr) ? $cpr->company_id : $cpr['company_id']) : null,
            'user_id' => ! empty(is_object($cpr) ? $cpr->user_id : $cpr['user_id']) ? (int) (is_object($cpr) ? $cpr->user_id : $cpr['user_id']) : null,
            'customer_name' => (string) (is_object($cpr) ? $cpr->customer_name : $cpr['customer_name']),
            'customer_email' => (string) (is_object($cpr) ? $cpr->customer_email : $cpr['customer_email']),
            'customer_mobile' => (string) (is_object($cpr) ? ($cpr->customer_mobile ?? '') : ($cpr['customer_mobile'] ?? '')),
            'company_name' => (string) (is_object($cpr) ? ($cpr->company_name ?? 'N/A') : ($cpr['company_name'] ?? 'N/A')),
            'location' => (string) (is_object($cpr) ? ($cpr->location ?? '') : ($cpr['location'] ?? '')),
            'request_type' => (string) (is_object($cpr) ? ($cpr->request_type ?? 'Custom Subscription Plan') : ($cpr['request_type'] ?? 'Custom Subscription Plan')),
            'package_type' => (string) ($pkgType ?? 'operational'),
            'package_id' => ! empty(is_object($cpr) ? $cpr->package_id : $cpr['package_id']) ? (int) (is_object($cpr) ? $cpr->package_id : $cpr['package_id']) : null,
            'package_title' => (string) $pkgTitle,
            'status' => (string) (is_object($cpr) ? $cpr->status : $cpr['status']),
            'aggregate_version' => (int) (is_object($cpr) ? ($cpr->aggregate_version ?? 1) : ($cpr['aggregate_version'] ?? 1)),
            'message' => (string) (is_object($cpr) ? ($cpr->message ?? '') : ($cpr['message'] ?? '')),
            'admin_notes' => (string) (is_object($cpr) ? ($cpr->admin_notes ?? '') : ($cpr['admin_notes'] ?? '')),
            'assigned_to' => ! empty(is_object($cpr) ? $cpr->assigned_to : $cpr['assigned_to']) ? (int) (is_object($cpr) ? $cpr->assigned_to : $cpr['assigned_to']) : null,
            'updated_by' => ! empty(is_object($cpr) ? $cpr->updated_by : $cpr['updated_by']) ? (int) (is_object($cpr) ? $cpr->updated_by : $cpr['updated_by']) : null,
            'last_status_updated_at' => $statusUpdatedAt,
            'created_at' => $createdAt,
            'updated_at' => $updatedAt,
        ];
    }

    /**
     * Index or update a single CustomPlanRequest into Elasticsearch.
     */
    public function indexRequest($cpr): bool
    {
        $this->ensureIndexExists();
        $index = $this->getIndexName();
        $id = is_object($cpr) ? $cpr->id : $cpr['id'];
        $doc = $this->formatDocument($cpr);

        try {
            $this->elasticService->getClient()->index([
                'index' => $index,
                'id' => (string) $id,
                'body' => $doc,
            ]);

            return true;
        } catch (\Throwable $e) {
            Log::error("ElasticCustomPlanRequestIndex: Failed to index request {$id}: ".$e->getMessage());

            return false;
        }
    }

    /**
     * Bulk index all CustomPlanRequest records from MySQL into Elasticsearch.
     */
    public function reindexAll(): int
    {
        $this->ensureIndexExists();
        $index = $this->getIndexName();
        $total = 0;

        DB::table('custom_plan_requests')
            ->orderBy('id', 'asc')
            ->chunk(200, function ($rows) use ($index, &$total) {
                $params = ['body' => []];

                foreach ($rows as $row) {
                    $doc = $this->formatDocument($row);
                    $params['body'][] = [
                        'index' => [
                            '_index' => $index,
                            '_id' => (string) $row->id,
                        ],
                    ];
                    $params['body'][] = $doc;
                    $total++;
                }

                if (! empty($params['body'])) {
                    $this->elasticService->bulkIndex($params);
                }
            });

        return $total;
    }

    /**
     * Execute Elasticsearch query for CustomPlanRequests with filtering, search, sorting, and pagination.
     */
    public function searchRequests(array $params = []): array
    {
        $this->ensureIndexExists();
        $index = $this->getIndexName();

        $must = [];
        $filter = [];

        // 1. Text Search Filter
        $searchTerm = trim((string) ($params['search'] ?? ''));
        if (! empty($searchTerm)) {
            $must[] = [
                'bool' => [
                    'should' => [
                        [
                            'multi_match' => [
                                'query' => $searchTerm,
                                'fields' => ['request_reference^3', 'customer_name^2', 'customer_email^2', 'company_name^2', 'package_title'],
                                'type' => 'best_fields',
                            ],
                        ],
                        [
                            'wildcard' => [
                                'request_reference' => [
                                    'value' => '*'.strtolower($searchTerm).'*',
                                    'case_insensitive' => true,
                                ],
                            ],
                        ],
                        [
                            'wildcard' => [
                                'customer_name.keyword' => [
                                    'value' => '*'.$searchTerm.'*',
                                    'case_insensitive' => true,
                                ],
                            ],
                        ],
                        [
                            'wildcard' => [
                                'company_name.keyword' => [
                                    'value' => '*'.$searchTerm.'*',
                                    'case_insensitive' => true,
                                ],
                            ],
                        ],
                        [
                            'wildcard' => [
                                'customer_email' => [
                                    'value' => '*'.strtolower($searchTerm).'*',
                                    'case_insensitive' => true,
                                ],
                            ],
                        ],
                    ],
                    'minimum_should_match' => 1,
                ],
            ];
        }

        // 2. Status Filter
        $status = $params['status'] ?? null;
        if (! empty($status) && $status !== 'all') {
            $filter[] = [
                'term' => [
                    'status' => $status,
                ],
            ];
        }

        // 3. Date Range Filter
        $startDate = $params['startDate'] ?? null;
        $endDate = $params['endDate'] ?? null;
        if (! empty($startDate) && ! empty($endDate)) {
            $filter[] = [
                'range' => [
                    'created_at' => [
                        'gte' => Carbon::parse($startDate)->startOfDay()->format('Y-m-d H:i:s'),
                        'lte' => Carbon::parse($endDate)->endOfDay()->format('Y-m-d H:i:s'),
                    ],
                ],
            ];
        }

        $query = [
            'bool' => [
                'must' => ! empty($must) ? $must : [['match_all' => new \stdClass]],
                'filter' => $filter,
            ],
        ];

        // 4. Sorting
        $sortColumn = $params['order_column'] ?? 'id';
        $sortDir = strtolower($params['order_dir'] ?? 'desc') === 'asc' ? 'asc' : 'desc';

        $sortMapping = [
            'id' => 'id',
            'request_reference' => 'request_reference',
            'customer_name' => 'customer_name.keyword',
            'customer_email' => 'customer_email',
            'company_name' => 'company_name.keyword',
            'request_type' => 'request_type',
            'status' => 'status',
            'created_at' => 'created_at',
            'updated_at' => 'updated_at',
        ];

        $esSortField = $sortMapping[$sortColumn] ?? 'id';
        $sort = [
            [$esSortField => ['order' => $sortDir, 'unmapped_type' => 'keyword']],
        ];
        if ($esSortField !== 'id') {
            $sort[] = ['id' => ['order' => 'desc']];
        }

        $from = (int) ($params['start'] ?? 0);
        $size = (int) ($params['length'] ?? 10);
        if ($size <= 0) {
            $size = 10;
        }

        $searchParams = [
            'index' => $index,
            'body' => [
                'from' => $from,
                'size' => $size,
                'query' => $query,
                'sort' => $sort,
                'aggs' => [
                    'status_counts' => [
                        'terms' => [
                            'field' => 'status',
                            'size' => 10,
                        ],
                    ],
                ],
            ],
        ];

        try {
            $response = $this->elasticService->getClient()->search($searchParams);

            $total = (int) ($response['hits']['total']['value'] ?? 0);
            $items = array_map(fn ($hit) => $hit['_source'], $response['hits']['hits'] ?? []);

            $aggregations = [];
            if (isset($response['aggregations']['status_counts']['buckets'])) {
                foreach ($response['aggregations']['status_counts']['buckets'] as $b) {
                    $aggregations[$b['key']] = (int) $b['doc_count'];
                }
            }

            return [
                'status' => 'success',
                'total' => $total,
                'items' => $items,
                'aggregations' => $aggregations,
            ];
        } catch (\Throwable $e) {
            Log::error('ElasticCustomPlanRequestIndex: Search failed: '.$e->getMessage());

            return [
                'status' => 'error',
                'message' => $e->getMessage(),
                'total' => 0,
                'items' => [],
                'aggregations' => [],
            ];
        }
    }

    /**
     * Compute summary counts across all requests via Elasticsearch aggregations.
     */
    public function getSummaryCountsFromElastic(): ?array
    {
        $this->ensureIndexExists();
        $index = $this->getIndexName();

        $searchParams = [
            'index' => $index,
            'body' => [
                'size' => 0,
                'query' => [
                    'match_all' => new \stdClass,
                ],
                'aggs' => [
                    'status_counts' => [
                        'terms' => [
                            'field' => 'status',
                            'size' => 10,
                        ],
                    ],
                ],
            ],
        ];

        try {
            $response = $this->elasticService->getClient()->search($searchParams);
            $total = (int) ($response['hits']['total']['value'] ?? 0);

            $buckets = [];
            if (isset($response['aggregations']['status_counts']['buckets'])) {
                foreach ($response['aggregations']['status_counts']['buckets'] as $b) {
                    $buckets[$b['key']] = (int) $b['doc_count'];
                }
            }

            return [
                'total' => $total,
                'new' => $buckets[CustomPlanRequest::STATUS_NEW] ?? 0,
                'in_progress' => $buckets[CustomPlanRequest::STATUS_IN_PROGRESS] ?? 0,
                'contacted' => $buckets[CustomPlanRequest::STATUS_CONTACTED] ?? 0,
                'resolved' => $buckets[CustomPlanRequest::STATUS_RESOLVED] ?? 0,
                'closed' => $buckets[CustomPlanRequest::STATUS_CLOSED] ?? 0,
            ];
        } catch (\Throwable $e) {
            Log::error('ElasticCustomPlanRequestIndex: Aggregation failed: '.$e->getMessage());

            return null;
        }
    }
}
