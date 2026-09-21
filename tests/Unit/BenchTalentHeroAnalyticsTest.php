<?php

namespace Tests\Unit;

use App\Services\BenchTalentHeroAnalytics;
use App\Services\ElasticService;
use Mockery;
use Tests\TestCase;

/** Verify that the hero reads tenant-scoped ES aggregates and labels missing data honestly. */
class BenchTalentHeroAnalyticsTest extends TestCase
{
    /** The four sources must travel in one tenant-filtered read and return real counts. */
    public function test_hero_uses_tenant_scoped_aggregations(): void
    {
        $elastic = Mockery::mock(ElasticService::class);
        $elastic->shouldReceive('msearch')->once()->with(Mockery::on(function (array $body): bool {
            $this->assertCount(8, $body);
            $this->assertSame(42, $body[1]['query']['bool']['filter'][0]['term']['company_id']);
            $this->assertSame(42, $body[3]['query']['bool']['filter'][0]['term']['source_from_company_id']);
            $this->assertSame(42, $body[5]['query']['bool']['filter'][0]['term']['company_id']);
            $this->assertSame(42, $body[7]['query']['bool']['filter'][0]['term']['source_comapny_id']);
            $this->assertSame(0, $body[1]['size']);

            return true;
        }))->andReturn(['responses' => [
            [
                'hits' => ['total' => ['value' => 14]],
                'aggregations' => [
                    'pool' => ['doc_count' => 7], 'bench' => ['doc_count' => 2],
                    'placed' => ['doc_count' => 4], 'paused' => ['doc_count' => 1],
                    'exited' => ['doc_count' => 0], 'views' => ['value' => 30],
                    'added_current' => ['doc_count' => 3], 'added_previous' => ['doc_count' => 2],
                ],
            ],
            ['aggregations' => [
                'current' => ['doc_count' => 10, 'accepted' => ['doc_count' => 6], 'expired' => ['doc_count' => 2]],
                'previous' => ['doc_count' => 8, 'accepted' => ['doc_count' => 4], 'expired' => ['doc_count' => 1]],
            ]],
            ['aggregations' => ['current' => ['doc_count' => 3], 'previous' => ['doc_count' => 2]]],
            ['aggregations' => ['current' => ['doc_count' => 1], 'previous' => ['doc_count' => 0]]],
        ]]);

        $hero = (new BenchTalentHeroAnalytics($elastic))->forCompany(42, 'this_month');

        $this->assertSame(14, $hero['counts']->all_records);
        $this->assertSame(2, $hero['stages']['bench']['value']);
        $this->assertSame(14.3, $hero['stages']['bench']['share']);
        $this->assertSame(6, $hero['stages']['accepted']['value']);
        $this->assertSame(50.0, $hero['stages']['accepted']['delta']);
        $this->assertSame(30, $hero['stages']['views']['value']);
        $this->assertNull($hero['stages']['views']['delta']);
    }

    /** A missing optional index is not a measured zero. */
    public function test_missing_optional_index_is_unavailable(): void
    {
        $elastic = Mockery::mock(ElasticService::class);
        $elastic->shouldReceive('msearch')->once()->andReturn(['responses' => [
            ['hits' => ['total' => ['value' => 1]], 'aggregations' => ['bench' => ['doc_count' => 1]]],
            ['error' => ['type' => 'index_not_found_exception']],
            ['error' => ['type' => 'index_not_found_exception']],
            ['error' => ['type' => 'index_not_found_exception']],
        ]]);

        $hero = (new BenchTalentHeroAnalytics($elastic))->forCompany(42, 'all_time');

        $this->assertFalse($hero['stages']['sent']['available']);
        $this->assertFalse($hero['stages']['interviews']['available']);
        $this->assertFalse($hero['stages']['placed']['available']);
        $this->assertNull($hero['stages']['sent']['delta']);
    }
}
