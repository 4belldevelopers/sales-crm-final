<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Add idx_company_event_type_id to coin_transection_logs
 *
 * Intent: Add composite index (company_id, event_type, id) to eliminate MySQL filesort
 * during transaction log queries ordering by id DESC under 10K+ concurrent recruiter load.
 *
 * Tenancy Note: Incorporates company_id as leading column for strict tenant isolation.
 *
 * Author: Antigravity AI
 * Date: October 09, 2026
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('coin_transection_logs')) {
            Schema::table('coin_transection_logs', function (Blueprint $table) {
                if (! Schema::hasIndex('coin_transection_logs', 'idx_company_event_type_id')) {
                    $table->index(['company_id', 'event_type', 'id'], 'idx_company_event_type_id');
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
                if (Schema::hasIndex('coin_transection_logs', 'idx_company_event_type_id')) {
                    $table->dropIndex('idx_company_event_type_id');
                }
            });
        }
    }
};
