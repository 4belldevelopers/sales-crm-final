<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Add Coins Engine V2 Scalability & Performance Indexes
 *
 * Intent: Add targeted composite and lookup indexes across coin_transection_logs
 * and coin_order_events tables to eliminate full table scans during historical log searches,
 * DataTables reporting, and coin event deduction rate lookups under 10K+ concurrent recruiter load.
 *
 * Tenancy Note: All tenant-scoped tables incorporate company_id in composite indexes.
 *
 * Author: Antigravity AI
 * Date: August 26, 2026
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Coin Transaction Logs Table: Tenant history and reporting lookup
        if (Schema::hasTable('coin_transection_logs')) {
            Schema::table('coin_transection_logs', function (Blueprint $table) {
                if (! Schema::hasIndex('coin_transection_logs', 'idx_coin_logs_comp_created')) {
                    $table->index(['company_id', 'created_at'], 'idx_coin_logs_comp_created');
                }
                if (! Schema::hasIndex('coin_transection_logs', 'idx_coin_logs_event_type')) {
                    $table->index(['event_type'], 'idx_coin_logs_event_type');
                }
                if (! Schema::hasIndex('coin_transection_logs', 'idx_coin_logs_order_id')) {
                    $table->index(['coin_order_id'], 'idx_coin_logs_order_id');
                }
            });
        }

        // 2. Coin Order Events Table: Deduction rate lookups
        if (Schema::hasTable('coin_order_events')) {
            Schema::table('coin_order_events', function (Blueprint $table) {
                if (! Schema::hasIndex('coin_order_events', 'idx_coin_order_events_lookup')) {
                    $table->index(['company_id', 'coin_order_id', 'coin_event_id'], 'idx_coin_order_events_lookup');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('coin_transection_logs')) {
            Schema::table('coin_transection_logs', function (Blueprint $table) {
                if (Schema::hasIndex('coin_transection_logs', 'idx_coin_logs_comp_created')) {
                    $table->dropIndex('idx_coin_logs_comp_created');
                }
                if (Schema::hasIndex('coin_transection_logs', 'idx_coin_logs_event_type')) {
                    $table->dropIndex('idx_coin_logs_event_type');
                }
                if (Schema::hasIndex('coin_transection_logs', 'idx_coin_logs_order_id')) {
                    $table->dropIndex('idx_coin_logs_order_id');
                }
            });
        }

        if (Schema::hasTable('coin_order_events')) {
            Schema::table('coin_order_events', function (Blueprint $table) {
                if (Schema::hasIndex('coin_order_events', 'idx_coin_order_events_lookup')) {
                    $table->dropIndex('idx_coin_order_events_lookup');
                }
            });
        }
    }
};
