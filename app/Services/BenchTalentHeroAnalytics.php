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
    public function forCompany(int $companyId, string $period): array
    {
        $period = in_array($period, ['this_month', 'last_month', 'last_90_days', 'all_time'], true)
            ? $period : 'this_month';
        [$current, $previous] = $this->periods($period);

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
            'added_current' => $this->dateBucket('created_at', $current),
            'added_previous' => $this->dateBucket('created_at', $previous),
        ]);

        $this->addSearch($body, ELASTIC_CANDIDATES_REQUEST, [
            ['term' => ['source_from_company_id' => $companyId]],
            ['term' => ['condidate_type.keyword' => 'bench_condidate']],
        ], [
            'current' => $this->dateBucket('created_at', $current, [
                'accepted' => ['filter' => ['term' => ['ac_status' => 1]]],
                'expired' => ['filter' => ['term' => ['ac_status' => 2]]],
            ]),
            'previous' => $this->dateBucket('created_at', $previous, [
                'accepted' => ['filter' => ['term' => ['ac_status' => 1]]],
                'expired' => ['filter' => ['term' => ['ac_status' => 2]]],
            ]),
        ]);

        $this->addSearch($body, ELASTIC_INERTVIEW_TRACKER, [
            ['term' => ['company_id' => $companyId]],
            ['term' => ['candidate_type.keyword' => 'bench_condidate']],
        ], [
            'current' => $this->dateBucket('schedule_date', $current),
            'previous' => $this->dateBucket('schedule_date', $previous),
        ]);

        $this->addSearch($body, ELASTIC_BENCH_PLACED, [
            ['term' => ['source_comapny_id' => $companyId]],
        ], [
            'current' => $this->dateBucket('created_at', $current),
            'previous' => $this->dateBucket('created_at', $previous),
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
            'talent' => $this->stage($total, $total > 0 ? 100 : 0, $bench['aggregations']['added_current']['doc_count'] ?? 0, $bench['aggregations']['added_previous']['doc_count'] ?? 0, $previous),
            'bench' => $this->stage($benchCount, $this->percent($benchCount, $total), 0, 0, null),
            'sent' => $this->stage($sent, $this->percent($sent, $total), $sent, $requests['aggregations']['previous']['doc_count'] ?? 0, $previous, $requestsAvailable),
            'accepted' => $this->stage($accepted, $this->percent($accepted, $sent), $accepted, $requests['aggregations']['previous']['accepted']['doc_count'] ?? 0, $previous, $requestsAvailable),
            'expired' => $this->stage($expired, $this->percent($expired, $sent), $expired, $requests['aggregations']['previous']['expired']['doc_count'] ?? 0, $previous, $requestsAvailable),
            'views' => $this->stage($views, $this->percent($views, $total), 0, 0, null),
            'interviews' => $this->stage($interviewCount, $this->percent($interviewCount, $sent), $interviewCount, $interviews['aggregations']['previous']['doc_count'] ?? 0, $previous, $interviewsAvailable),
            'placed' => $this->stage($placed, $this->percent($placed, $sent), $placed, $placements['aggregations']['previous']['doc_count'] ?? 0, $previous, $placementsAvailable),
        ];

        return [
            'period' => $period,
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

    /** Pair a selected window with the immediately preceding comparable window. */
    private function periods(string $period): array
    {
        $now = CarbonImmutable::now();

        return match ($period) {
            'last_month' => [
                [$now->subMonthNoOverflow()->startOfMonth()->toIso8601String(), $now->startOfMonth()->toIso8601String()],
                [$now->subMonthsNoOverflow(2)->startOfMonth()->toIso8601String(), $now->subMonthNoOverflow()->startOfMonth()->toIso8601String()],
            ],
            'last_90_days' => [
                [$now->subDays(90)->toIso8601String(), $now->toIso8601String()],
                [$now->subDays(180)->toIso8601String(), $now->subDays(90)->toIso8601String()],
            ],
            'all_time' => [null, null],
            default => [
                [$now->startOfMonth()->toIso8601String(), $now->toIso8601String()],
                [$now->subMonthNoOverflow()->startOfMonth()->toIso8601String(), $now->subMonthNoOverflow()->toIso8601String()],
            ],
        };
    }

    /** Return a displayed count, share and signed comparison percentage. */
    private function stage(int $value, float|int $share, int $current, int $previousCount, ?array $previous, bool $available = true): array
    {
        return [
            'value' => $value,
            'share' => $share,
            'available' => $available,
            'delta' => ! $available || $previous === null ? null : ($previousCount > 0
                ? round((($current - $previousCount) / $previousCount) * 100, 1)
                : ($current > 0 ? 100.0 : 0.0)),
        ];
    }

    /** Express one stage as a share of its meaningful predecessor. */
    private function percent(int $part, int $whole): float
    {
        return $whole > 0 ? round($part / $whole * 100, 1) : 0;
    }
}
