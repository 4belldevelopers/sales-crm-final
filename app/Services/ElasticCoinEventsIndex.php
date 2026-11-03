<?php

namespace App\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class ElasticCoinEventsIndex
 *
 * Intent: Elasticsearch Read Authority and dual-store synchronization engine for Coin Features & Events.
 * Provides CQRS read isolation (0% MySQL reads on search, filtering, pagination, and KPI aggregations),
 * single-document atomic indexing, and bulk reconciliation between MySQL and Elasticsearch.
 *
 * Index: local_coin_events (constant ELASTIC_COIN_EVENTS)
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class ElasticCoinEventsIndex
{
    public function __construct(
        protected ElasticService $elasticService,
        protected ElasticsearchCircuitBreaker $circuitBreaker
    ) {}

    /**
     * Get authoritative index name.
     */
    public function getIndexName(): string
    {
        if (defined('ELASTIC_COIN_EVENTS')) {
            return ELASTIC_COIN_EVENTS;
        }

        return (string) config('constants.ELASTIC_COIN_EVENTS', 'local_coin_events');
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
                    'id' => ['type' => 'long'],
                    'event_type' => ['type' => 'keyword'],
                    'title' => [
                        'type' => 'text',
                        'fields' => ['keyword' => ['type' => 'keyword', 'ignore_above' => 256]],
                    ],
                    'default_rate' => ['type' => 'double'],
                    'description' => ['type' => 'text'],
                    'deduction_type' => ['type' => 'keyword'],
                    'minimum_required' => ['type' => 'double'],
                    'status' => ['type' => 'integer'],
                ],
            ];

            $this->elasticService->createIndex($index, $mappings);

            return true;
        } catch (\Throwable $e) {
            Log::error('ElasticCoinEventsIndex: Failed to create index '.$index.': '.$e->getMessage());

            return false;
        }
    }

    /**
     * Format a model or row object for Elasticsearch storage.
     *
     * @param  object|array  $event
     */
    public function formatDocument($event): array
    {
        $id = is_object($event) ? (int) $event->id : (int) $event['id'];
        $eventType = is_object($event) ? (string) ($event->event_type ?? 'sales') : (string) ($event['event_type'] ?? 'sales');
        $title = is_object($event) ? (string) ($event->title ?? '') : (string) ($event['title'] ?? '');
        $defaultRate = is_object($event) ? (float) ($event->default_rate ?? 0.00) : (float) ($event['default_rate'] ?? 0.00);
        $description = is_object($event) ? (string) ($event->description ?? '') : (string) ($event['description'] ?? '');
        $deductionType = is_object($event) ? (string) ($event->deduction_type ?? 'onetime') : (string) ($event['deduction_type'] ?? 'onetime');
        $minimumRequired = is_object($event) ? (float) ($event->minimum_required ?? 0.00) : (float) ($event['minimum_required'] ?? 0.00);
        $status = is_object($event) ? (int) ($event->status ?? 1) : (int) ($event['status'] ?? 1);

        return [
            'id' => $id,
            'event_type' => $eventType,
            'title' => $title,
            'default_rate' => $defaultRate,
            'description' => $description,
            'deduction_type' => $deductionType,
            'minimum_required' => $minimumRequired,
            'status' => $status,
        ];
    }

    /**
     * Index or update a single Coin Event into Elasticsearch.
     *
     * @param  object|array  $event
     */
    public function indexEvent($event): bool
    {
        $this->ensureIndexExists();
        $index = $this->getIndexName();
        $id = is_object($event) ? $event->id : $event['id'];
        $doc = $this->formatDocument($event);

        try {
            $this->elasticService->getClient()->index([
                'index' => $index,
                'id' => (string) $id,
                'body' => $doc,
            ]);

            return true;
        } catch (\Throwable $e) {
            Log::error("ElasticCoinEventsIndex: Failed to index event {$id}: ".$e->getMessage());

            return false;
        }
    }

    /**
     * Delete an event document from Elasticsearch.
     */
    public function deleteEvent(int $id): bool
    {
        $index = $this->getIndexName();

        try {
            $this->elasticService->getClient()->delete([
                'index' => $index,
                'id' => (string) $id,
            ]);

            return true;
        } catch (\Throwable $e) {
            Log::warning("ElasticCoinEventsIndex: Delete doc {$id} notice: ".$e->getMessage());

            return false;
        }
    }

    /**
     * Bulk index all Coin Events from MySQL into Elasticsearch.
     */
    public function reindexAll(): int
    {
        $this->ensureIndexExists();
        $index = $this->getIndexName();
        $total = 0;

        DB::table('coin_events')
            ->orderBy('id', 'asc')
            ->chunk(100, function ($rows) use ($index, &$total) {
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
     * Fetch KPI Aggregations directly from Elasticsearch.
     */
    public function getAggregations(): array
    {
        $defaultAggs = [
            'total' => 0,
            'active' => 0,
            'inactive' => 0,
            'operational' => 0,
            'sales' => 0,
            'daily' => 0,
            'onetime' => 0,
        ];

        if (! $this->circuitBreaker->isAvailable()) {
            return $defaultAggs;
        }

        try {
            $this->ensureIndexExists();
            $index = $this->getIndexName();

            $params = [
                'index' => $index,
                'body' => [
                    'size' => 0,
                    'aggs' => [
                        'active' => [
                            'filter' => ['term' => ['status' => 1]],
                        ],
                        'inactive' => [
                            'filter' => ['term' => ['status' => 0]],
                        ],
                        'operational' => [
                            'filter' => ['term' => ['event_type' => 'operational']],
                        ],
                        'sales' => [
                            'filter' => ['term' => ['event_type' => 'sales']],
                        ],
                        'daily' => [
                            'filter' => ['term' => ['deduction_type' => 'daily']],
                        ],
                        'onetime' => [
                            'filter' => ['term' => ['deduction_type' => 'onetime']],
                        ],
                    ],
                ],
            ];

            $response = $this->elasticService->getClient()->search($params);
            $this->circuitBreaker->recordSuccess();

            $total = (int) ($response['hits']['total']['value'] ?? 0);
            $aggs = $response['aggregations'] ?? [];

            return [
                'total' => $total,
                'active' => (int) ($aggs['active']['doc_count'] ?? 0),
                'inactive' => (int) ($aggs['inactive']['doc_count'] ?? 0),
                'operational' => (int) ($aggs['operational']['doc_count'] ?? 0),
                'sales' => (int) ($aggs['sales']['doc_count'] ?? 0),
                'daily' => (int) ($aggs['daily']['doc_count'] ?? 0),
                'onetime' => (int) ($aggs['onetime']['doc_count'] ?? 0),
            ];
        } catch (\Throwable $e) {
            $this->circuitBreaker->recordFailure($e);
            Log::warning('ElasticCoinEventsIndex::getAggregations fallback triggered: '.$e->getMessage());

            return $defaultAggs;
        }
    }

    /**
     * Search Coin Events with full-text search, term filtering, and pagination.
     */
    public function searchEvents(array $params = []): array
    {
        $defaultResult = [
            'total' => 0,
            'items' => [],
            'page' => (int) ($params['page'] ?? 1),
            'per_page' => (int) ($params['per_page'] ?? 100),
        ];

        if (! $this->circuitBreaker->isAvailable()) {
            return $defaultResult;
        }

        try {
            $this->ensureIndexExists();
            $index = $this->getIndexName();

            $must = [];
            $filter = [];

            // 1. Text Search Filter
            $searchTerm = trim((string) ($params['search'] ?? ''));
            if (! empty($searchTerm)) {
                $should = [
                    [
                        'multi_match' => [
                            'query' => $searchTerm,
                            'fields' => ['title^3', 'description^2'],
                            'type' => 'best_fields',
                            'fuzziness' => 'AUTO',
                        ],
                    ],
                    [
                        'wildcard' => [
                            'title.keyword' => [
                                'value' => '*'.strtolower($searchTerm).'*',
                                'case_insensitive' => true,
                            ],
                        ],
                    ],
                ];

                if (is_numeric($searchTerm)) {
                    $should[] = ['term' => ['id' => (int) $searchTerm]];
                }

                $must[] = ['bool' => ['should' => $should, 'minimum_should_match' => 1]];
            }

            // 2. Event Type Filter
            if (! empty($params['event_type']) && in_array($params['event_type'], ['operational', 'sales'], true)) {
                $filter[] = ['term' => ['event_type' => $params['event_type']]];
            }

            // 3. Deduction Type Filter
            if (! empty($params['deduction_type']) && in_array($params['deduction_type'], ['daily', 'onetime'], true)) {
                $filter[] = ['term' => ['deduction_type' => $params['deduction_type']]];
            }

            // 4. Status Filter
            if (isset($params['status']) && $params['status'] !== '' && $params['status'] !== null) {
                $filter[] = ['term' => ['status' => (int) $params['status']]];
            }

            $query = ['bool' => []];
            if (! empty($must)) {
                $query['bool']['must'] = $must;
            }
            if (! empty($filter)) {
                $query['bool']['filter'] = $filter;
            }
            if (empty($must) && empty($filter)) {
                $query = ['match_all' => new \stdClass];
            }

            $page = max((int) ($params['page'] ?? 1), 1);
            $perPage = min(max((int) ($params['per_page'] ?? 100), 5), 500);
            $from = ($page - 1) * $perPage;

            $searchBody = [
                'query' => $query,
                'sort' => [['id' => ['order' => 'asc']]],
                'from' => $from,
                'size' => $perPage,
            ];

            $response = $this->elasticService->getClient()->search([
                'index' => $index,
                'body' => $searchBody,
            ]);

            $this->circuitBreaker->recordSuccess();

            $total = (int) ($response['hits']['total']['value'] ?? 0);
            $items = array_map(function ($hit) {
                $src = $hit['_source'];

                return (object) [
                    'id' => (int) ($src['id'] ?? $hit['_id']),
                    'event_type' => (string) ($src['event_type'] ?? 'sales'),
                    'title' => (string) ($src['title'] ?? ''),
                    'default_rate' => (float) ($src['default_rate'] ?? 0.00),
                    'description' => (string) ($src['description'] ?? ''),
                    'deduction_type' => (string) ($src['deduction_type'] ?? 'onetime'),
                    'minimum_required' => (float) ($src['minimum_required'] ?? 0.00),
                    'status' => (int) ($src['status'] ?? 1),
                ];
            }, $response['hits']['hits'] ?? []);

            return [
                'total' => $total,
                'items' => $items,
                'page' => $page,
                'per_page' => $perPage,
            ];
        } catch (\Throwable $e) {
            $this->circuitBreaker->recordFailure($e);
            Log::warning('ElasticCoinEventsIndex::searchEvents fallback triggered: '.$e->getMessage());

            return $defaultResult;
        }
    }

    /**
     * Get single event by ID from Elasticsearch.
     */
    public function getEventById(int $id): ?object
    {
        if (! $this->circuitBreaker->isAvailable()) {
            return null;
        }

        try {
            $index = $this->getIndexName();
            $response = $this->elasticService->getClient()->get([
                'index' => $index,
                'id' => (string) $id,
            ]);

            $this->circuitBreaker->recordSuccess();

            if (isset($response['_source'])) {
                $src = $response['_source'];

                return (object) [
                    'id' => (int) ($src['id'] ?? $id),
                    'event_type' => (string) ($src['event_type'] ?? 'sales'),
                    'title' => (string) ($src['title'] ?? ''),
                    'default_rate' => (float) ($src['default_rate'] ?? 0.00),
                    'description' => (string) ($src['description'] ?? ''),
                    'deduction_type' => (string) ($src['deduction_type'] ?? 'onetime'),
                    'minimum_required' => (float) ($src['minimum_required'] ?? 0.00),
                    'status' => (int) ($src['status'] ?? 1),
                ];
            }

            return null;
        } catch (\Throwable $e) {
            $this->circuitBreaker->recordFailure($e);

            return null;
        }
    }

    /**
     * Wrap items array into a Laravel LengthAwarePaginator.
     */
    public function paginateResults(array $searchResult, string $path = ''): LengthAwarePaginator
    {
        $paginator = new LengthAwarePaginator(
            $searchResult['items'],
            $searchResult['total'],
            $searchResult['per_page'],
            $searchResult['page'],
            ['path' => $path ?: url()->current()]
        );

        return $paginator;
    }
}
