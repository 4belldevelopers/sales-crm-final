<?php

namespace App\Services;

use Carbon\CarbonImmutable;

/**
 * Supplies the In-House Talent hero from tenant-scoped Elasticsearch read models.
 * Inventory is a current snapshot; engagement events follow the selected period.
 */
class BenchTalentHeroAnalytics
{
    public function __construct(private ElasticService $elasticService)
    {
    }

    /**
     * Build the eight hero stages without reading MySQL. Every query is restricted
     * to the candidate-owning company or the source company of a placement.
     */
    public function forCompany(int $companyId, string $month, string $window): array
    {
        [$month, $window, $current] = $this->periods($month, $window);

        $body = [];
        $this->addSearch($body, ELASTIC_CANDIDATES_BENCH, [
            ['term' => ['company_id' => $companyId]],
            ['term' => ['condidate_type.keyword' => 'bench_condidate']],
        ], [
            'pool' => ['filter' => ['term' => ['status' => 1]]],
            'bench' => ['filter' => ['term' => ['status' => 2]]],
            'placed' => ['filter' => ['term' => ['status' => 3]]],
            'paused' => ['filter' => ['term' => ['status' => 4]]],
            'exited' => ['filter' => ['term' => ['status' => 5]]],
            'views' => ['sum' => ['field' => 'viewCount']],
        ]);

        $this->addSearch($body, ELASTIC_CANDIDATES_REQUEST, [
            ['term' => ['source_from_company_id' => $companyId]],
            ['term' => ['condidate_type.keyword' => 'bench_condidate']],
        ], [
            'current' => $this->dateBucket('created_at', $current, [
                'accepted' => ['filter' => ['term' => ['ac_status' => 1]]],
                'expired' => ['filter' => ['term' => ['ac_status' => 2]]],
            ]),
        ]);

        $this->addSearch($body, ELASTIC_INERTVIEW_TRACKER, [
            ['term' => ['company_id' => $companyId]],
            ['term' => ['candidate_type.keyword' => 'bench_condidate']],
        ], [
            'current' => $this->dateBucket('schedule_date', $current),
        ]);

        $this->addSearch($body, ELASTIC_BENCH_PLACED, [
            ['term' => ['source_comapny_id' => $companyId]],
        ], [
            'current' => $this->dateBucket('created_at', $current),
        ]);

        $responses = $this->elasticService->msearch($body)['responses'] ?? [];
        $bench = $responses[0] ?? [];
        $requests = $responses[1] ?? [];
        $interviews = $responses[2] ?? [];
        $placements = $responses[3] ?? [];
        if (! isset($bench['aggregations'])) {
            throw new \RuntimeException('Bench talent analytics are unavailable.');
        }

        $requestsAvailable = isset($requests['aggregations']);
        $interviewsAvailable = isset($interviews['aggregations']);
        $placementsAvailable = isset($placements['aggregations']);

        $total = (int) ($bench['hits']['total']['value'] ?? 0);
        $benchCount = (int) ($bench['aggregations']['bench']['doc_count'] ?? 0);
        $sent = (int) ($requests['aggregations']['current']['doc_count'] ?? 0);
        $accepted = (int) ($requests['aggregations']['current']['accepted']['doc_count'] ?? 0);
        $expired = (int) ($requests['aggregations']['current']['expired']['doc_count'] ?? 0);
        $views = (int) round((float) ($bench['aggregations']['views']['value'] ?? 0));
        $interviewCount = (int) ($interviews['aggregations']['current']['doc_count'] ?? 0);
        $placed = (int) ($placements['aggregations']['current']['doc_count'] ?? 0);

        $stages = [
            'talent' => $this->stage($total),
            'bench' => $this->stage($benchCount),
            'sent' => $this->stage($sent, $requestsAvailable),
            'accepted' => $this->stage($accepted, $requestsAvailable),
            'expired' => $this->stage($expired, $requestsAvailable),
            'views' => $this->stage($views),
            'interviews' => $this->stage($interviewCount, $interviewsAvailable),
            'placed' => $this->stage($placed, $placementsAvailable),
        ];

        return [
            'month' => $month,
            'window' => $window,
            'counts' => (object) [
                'all_records' => $total,
                'pool' => (int) ($bench['aggregations']['pool']['doc_count'] ?? 0),
                'bench' => $benchCount,
                'placed' => (int) ($bench['aggregations']['placed']['doc_count'] ?? 0),
                'paused' => (int) ($bench['aggregations']['paused']['doc_count'] ?? 0),
                'exited' => (int) ($bench['aggregations']['exited']['doc_count'] ?? 0),
            ],
            'stages' => $stages,
        ];
    }

    /** Append a tenant-filtered aggregation to one Elasticsearch multi-search. */
    private function addSearch(array &$body, string $index, array $filters, array $aggregations): void
    {
        $body[] = ['index' => $index];
        $body[] = [
            'size' => 0,
            'track_total_hits' => true,
            'query' => ['bool' => ['filter' => $filters]],
            'aggs' => $aggregations,
        ];
    }

    /** Bound a date aggregation without changing the underlying status rules. */
    private function dateBucket(string $field, ?array $range, array $aggregations = []): array
    {
        $bucket = ['filter' => $range
            ? ['range' => [$field => ['gte' => $range[0], 'lt' => $range[1]]]]
            : ['match_all' => (object) []]];
        if ($aggregations) {
            $bucket['aggs'] = $aggregations;
        }

        return $bucket;
    }

    /** Anchor a 1/3/6/12-month window to the selected ending month. */
    private function periods(string $month, string $window): array
    {
        $now = CarbonImmutable::now();
        $allowedMonths = array_map(
            fn (int $offset) => $now->subMonthsNoOverflow($offset)->format('Y-m'),
            range(0, 11)
        );
        $month = in_array($month, $allowedMonths, true) ? $month : $allowedMonths[0];
        $monthCounts = ['monthly' => 1, 'quarterly' => 3, 'half_yearly' => 6, 'yearly' => 12];
        $window = array_key_exists($window, $monthCounts) ? $window : 'monthly';
        $months = $monthCounts[$window];

        $anchor = $now->setDate((int) substr($month, 0, 4), (int) substr($month, 5, 2), 1)->startOfDay();
        $currentStart = $anchor->subMonthsNoOverflow($months - 1)->startOfMonth();
        $currentEnd = $month === $allowedMonths[0] ? $now : $anchor->addMonth();
        return [
            $month,
            $window,
            [$currentStart->toIso8601String(), $currentEnd->toIso8601String()],
        ];
    }

    /** Preserve source availability separately from a measured zero. */
    private function stage(int $value, bool $available = true): array
    {
        return [
            'value' => $value,
            'available' => $available,
        ];
    }
}
