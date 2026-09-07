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
        // 1. Candidate Status: Composite index for tenant pipeline filtering
        if (Schema::hasTable('candidate_status')) {
            Schema::table('candidate_status', function (Blueprint $table) {
                $sm = Schema::getConnection()->getDoctrineSchemaManager();
                $indexes = array_keys($sm->listTableIndexes('candidate_status'));
                if (! in_array('idx_candidate_status_company_parent_priority', $indexes)) {
                    $table->index(['company_id', 'parent', 'priority'], 'idx_candidate_status_company_parent_priority');
                }
            });
        }

        // 2. Offer Point Companies: Composite index for section type & sorting lookups
        if (Schema::hasTable('offer_point_companies')) {
            Schema::table('offer_point_companies', function (Blueprint $table) {
                $sm = Schema::getConnection()->getDoctrineSchemaManager();
                $indexes = array_keys($sm->listTableIndexes('offer_point_companies'));
                if (! in_array('idx_offer_point_comp_type_divided_sorting', $indexes)) {
                    $table->index(['company_id', 'section_type', 'section_divided', 'sorting'], 'idx_offer_point_comp_type_divided_sorting');
                }
            });
        }

        // 3. Boost Keywords: Composite index for tenant & country filtering
        if (Schema::hasTable('boostkeywords')) {
            Schema::table('boostkeywords', function (Blueprint $table) {
                $sm = Schema::getConnection()->getDoctrineSchemaManager();
                $indexes = array_keys($sm->listTableIndexes('boostkeywords'));
                if (! in_array('idx_boostkeywords_company_country_id', $indexes)) {
                    $table->index(['company_id', 'country_id', 'id'], 'idx_boostkeywords_company_country_id');
                }
            });
        }

        // 4. Company Coins: Composite index for tenant coin event lookups
        if (Schema::hasTable('company_coins')) {
            Schema::table('company_coins', function (Blueprint $table) {
                $sm = Schema::getConnection()->getDoctrineSchemaManager();
                $indexes = array_keys($sm->listTableIndexes('company_coins'));
                if (! in_array('idx_company_coins_company_event', $indexes)) {
                    $table->index(['company_id', 'event_type'], 'idx_company_coins_company_event');
                }
            });
        }

        // 5. Client Location: Composite index for client address lookups
        if (Schema::hasTable('client_location')) {
            Schema::table('client_location', function (Blueprint $table) {
                $sm = Schema::getConnection()->getDoctrineSchemaManager();
                $indexes = array_keys($sm->listTableIndexes('client_location'));
                if (! in_array('idx_client_location_client_id_id', $indexes)) {
                    $table->index(['client_id', 'id'], 'idx_client_location_client_id_id');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('candidate_status')) {
            Schema::table('candidate_status', function (Blueprint $table) {
                $table->dropIndex('idx_candidate_status_company_parent_priority');
            });
        }

        if (Schema::hasTable('offer_point_companies')) {
            Schema::table('offer_point_companies', function (Blueprint $table) {
                $table->dropIndex('idx_offer_point_comp_type_divided_sorting');
            });
        }

        if (Schema::hasTable('boostkeywords')) {
            Schema::table('boostkeywords', function (Blueprint $table) {
                $table->dropIndex('idx_boostkeywords_company_country_id');
            });
        }

        if (Schema::hasTable('company_coins')) {
            Schema::table('company_coins', function (Blueprint $table) {
                $table->dropIndex('idx_company_coins_company_event');
            });
        }

        if (Schema::hasTable('client_location')) {
            Schema::table('client_location', function (Blueprint $table) {
                $table->dropIndex('idx_client_location_client_id_id');
            });
        }
    }
};
