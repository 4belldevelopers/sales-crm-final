<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * CoinsDatabaseIndexTest — V2 Coins Engine Phase 3 Feature Test Suite.
 *
 * Intent: Verifies presence and utilization of composite performance indexes across
 * coin_transection_logs and coin_order_events tables.
 *
 * Tenancy Note: Tenant isolation is enforced via company_id filtering. Does NOT use
 * RefreshDatabase (per Rule #2 and project guidelines).
 *
 * Author: Antigravity AI
 * Date: August 26, 2026
 */
class CoinsDatabaseIndexTest extends TestCase
{
    /**
     * Test presence of newly created composite database indexes.
     *
     * Intent: Confirms schema integrity for coin transaction log and event lookup indexes.
     */
    public function test_coin_engine_v2_indexes_exist_in_database_schema(): void
    {
        $this->assertTrue(
            Schema::hasIndex('coin_transection_logs', 'idx_coin_logs_comp_created'),
            'Index idx_coin_logs_comp_created must exist on coin_transection_logs table'
        );

        $this->assertTrue(
            Schema::hasIndex('coin_transection_logs', 'idx_coin_logs_event_type'),
            'Index idx_coin_logs_event_type must exist on coin_transection_logs table'
        );

        $this->assertTrue(
            Schema::hasIndex('coin_transection_logs', 'idx_coin_logs_order_id'),
            'Index idx_coin_logs_order_id must exist on coin_transection_logs table'
        );

        $this->assertTrue(
            Schema::hasIndex('coin_order_events', 'idx_coin_order_events_lookup'),
            'Index idx_coin_order_events_lookup must exist on coin_order_events table'
        );
    }

    /**
     * Test EXPLAIN query execution plan optimization for coin log queries.
     *
     * Intent: Verifies MySQL query optimizer uses created indexes instead of full table scans (type=ALL).
     */
    public function test_explain_query_plan_utilizes_new_indexes(): void
    {
        $explain = DB::select('EXPLAIN SELECT * FROM coin_transection_logs WHERE company_id = 1 ORDER BY created_at DESC LIMIT 10');

        $this->assertNotEmpty($explain);
        $firstRow = $explain[0];

        fwrite(STDOUT, sprintf(
            "\n[Phase 3 Index Test] Query EXPLAIN -> Possible Keys: %s | Used Key: %s | Key Length: %s\n",
            $firstRow->possible_keys ?? 'none',
            $firstRow->key ?? 'none',
            $firstRow->key_len ?? 'none'
        ));

        $this->assertNotNull($firstRow->key, 'Query must utilize an index for tenant log lookups');
    }
}
