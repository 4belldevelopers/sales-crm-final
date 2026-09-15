<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Add Account Listing V2 Performance Indexes
 *
 * Intent: Add targeted composite indexes to accelerate queries on candidate_status,
 * offer_point_companies, boostkeywords, company_coins, and client_location tables
 * for Account Listing V2 optimization.
 *
 * Author: Antigravity AI
 * Date: August 12, 2026
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $indexes = [
            ['candidate_status', 'idx_candidate_status_company_parent_priority', ['company_id', 'parent', 'priority']],
            ['offer_point_companies', 'idx_offer_point_comp_type_divided_sorting', ['company_id', 'section_type', 'section_divided', 'sorting']],
            ['boostkeywords', 'idx_boostkeywords_company_country_id', ['company_id', 'country_id', 'id']],
            ['company_coins', 'idx_company_coins_company_event', ['company_id', 'event_type']],
            ['client_location', 'idx_client_location_client_id_id', ['client_id', 'id']],
        ];

        foreach ($indexes as [$table, $indexName, $columns]) {
            try {
                if (Schema::hasTable($table) && ! Schema::hasIndex($table, $indexName)) {
                    Schema::table($table, function (Blueprint $t) use ($columns, $indexName) {
                        $t->index($columns, $indexName);
                    });
                }
            } catch (\Throwable $e) {
                report($e);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $indexes = [
            ['candidate_status', 'idx_candidate_status_company_parent_priority'],
            ['offer_point_companies', 'idx_offer_point_comp_type_divided_sorting'],
            ['boostkeywords', 'idx_boostkeywords_company_country_id'],
            ['company_coins', 'idx_company_coins_company_event'],
            ['client_location', 'idx_client_location_client_id_id'],
        ];

        foreach ($indexes as [$table, $indexName]) {
            try {
                if (Schema::hasTable($table) && Schema::hasIndex($table, $indexName)) {
                    Schema::table($table, function (Blueprint $t) use ($indexName) {
                        $t->dropIndex($indexName);
                    });
                }
            } catch (\Throwable $e) {
                report($e);
            }
        }
    }
};
