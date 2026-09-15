<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Add Team Listing V2 Performance Indexes
 *
 * Intent: Add targeted composite indexes to accelerate queries on bench_assign
 * and job_assign tables for Team Listing V2 optimization (workload count batching).
 * Tenancy: All composite indexes incorporate tenant scope (company_id) or user_id.
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
            ['bench_assign', 'idx_bench_assign_user_company', ['user_id', 'company_id']],
            ['job_assign', 'idx_job_assign_company_spok_job', ['company_id', 'spok_user_id', 'job_id']],
            ['job_assign', 'idx_job_assign_company_user_job', ['company_id', 'user_id', 'job_id']],
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
            ['bench_assign', 'idx_bench_assign_user_company'],
            ['job_assign', 'idx_job_assign_company_spok_job'],
            ['job_assign', 'idx_job_assign_company_user_job'],
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
