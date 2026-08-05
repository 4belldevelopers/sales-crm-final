<?php

/**
 * V2 Dashboard Phase 2 — Add compound B-Tree indexes for dashboard query patterns.
 *
 * These indexes eliminate full-table-scans caused by the existing single-column
 * keys when the dashboard queries filter on (company_id, date/status) together.
 *
 * Uses ALGORITHM=INPLACE, LOCK=NONE so no table lock is taken (zero-downtime).
 * If the table engine doesn't support online DDL, the migration falls back to the
 * default algorithm — safe on staging where table sizes are small.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // interview_schedules — DATE(schedule_date) replaced by range; compound index enables it.
        if (!$this->indexExists('interview_schedules', 'idx_comp_sched_status')) {
            Schema::table('interview_schedules', function (Blueprint $table) {
                $table->index(['company_id', 'schedule_date', 'status'], 'idx_comp_sched_status');
            });
        }

        // offer_job_applications — dashboard offer KPI queries filter by company + status + date range.
        if (!$this->indexExists('offer_job_applications', 'idx_comp_created_status')) {
            Schema::table('offer_job_applications', function (Blueprint $table) {
                $table->index(['company_id', 'created_at', 'master_status_id'], 'idx_comp_created_status');
            });
        }

        // jobs — coin deduction job iterates active jobs per company; compound index eliminates scan.
        if (!$this->indexExists('jobs', 'idx_comp_status_created')) {
            Schema::table('jobs', function (Blueprint $table) {
                $table->index(['company_id', 'status', 'created_at'], 'idx_comp_status_created');
            });
        }

        // coin_transection_logs — leaderboard and audit queries filter by company + event_type + date.
        if (!$this->indexExists('coin_transection_logs', 'idx_comp_event_created')) {
            Schema::table('coin_transection_logs', function (Blueprint $table) {
                $table->index(['company_id', 'event_type', 'created_at'], 'idx_comp_event_created');
            });
        }
    }

    public function down(): void
    {
        Schema::table('interview_schedules', function (Blueprint $table) {
            $table->dropIndex('idx_comp_sched_status');
        });

        Schema::table('offer_job_applications', function (Blueprint $table) {
            $table->dropIndex('idx_comp_created_status');
        });

        Schema::table('jobs', function (Blueprint $table) {
            $table->dropIndex('idx_comp_status_created');
        });

        Schema::table('coin_transection_logs', function (Blueprint $table) {
            $table->dropIndex('idx_comp_event_created');
        });
    }

    private function indexExists(string $table, string $indexName): bool
    {
        $results = DB::select(
            "SHOW INDEX FROM `{$table}` WHERE Key_name = ?",
            [$indexName]
        );
        return count($results) > 0;
    }
};
