<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration hardening custom_plan_requests table for 10K concurrent operations:
 * - Adds aggregate_version for Optimistic Concurrency Control (OCC)
 * - Adds index on updated_at to eliminate filesorts during delta polling/sync
 * - Adds composite index on (status, updated_at) for efficient filtering and sort seeks
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('custom_plan_requests')) {
            Schema::table('custom_plan_requests', function (Blueprint $table) {
                if (! Schema::hasColumn('custom_plan_requests', 'aggregate_version')) {
                    $table->unsignedInteger('aggregate_version')->default(1)->after('status');
                }

                if (! Schema::hasIndex('custom_plan_requests', 'idx_cpr_updated_at')) {
                    $table->index('updated_at', 'idx_cpr_updated_at');
                }

                if (! Schema::hasIndex('custom_plan_requests', 'idx_cpr_status_updated_at')) {
                    $table->index(['status', 'updated_at'], 'idx_cpr_status_updated_at');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('custom_plan_requests')) {
            Schema::table('custom_plan_requests', function (Blueprint $table) {
                if (Schema::hasColumn('custom_plan_requests', 'aggregate_version')) {
                    $table->dropColumn('aggregate_version');
                }

                if (Schema::hasIndex('custom_plan_requests', 'idx_cpr_status_updated_at')) {
                    $table->dropIndex('idx_cpr_status_updated_at');
                }

                if (Schema::hasIndex('custom_plan_requests', 'idx_cpr_updated_at')) {
                    $table->dropIndex('idx_cpr_updated_at');
                }
            });
        }
    }
};
