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
        // 1. Bench Assign: Composite index for TRM talent user counts
        if (Schema::hasTable('bench_assign')) {
            Schema::table('bench_assign', function (Blueprint $table) {
                $sm = Schema::getConnection()->getDoctrineSchemaManager();
                $indexes = array_keys($sm->listTableIndexes('bench_assign'));
                if (! in_array('idx_bench_assign_user_company', $indexes)) {
                    $table->index(['user_id', 'company_id'], 'idx_bench_assign_user_company');
                }
            });
        }

        // 2. Job Assign: Composite index for Captain jobs (spok_user_id) distinct count
        if (Schema::hasTable('job_assign')) {
            Schema::table('job_assign', function (Blueprint $table) {
                $sm = Schema::getConnection()->getDoctrineSchemaManager();
                $indexes = array_keys($sm->listTableIndexes('job_assign'));
                if (! in_array('idx_job_assign_company_spok_job', $indexes)) {
                    $table->index(['company_id', 'spok_user_id', 'job_id'], 'idx_job_assign_company_spok_job');
                }
                if (! in_array('idx_job_assign_company_user_job', $indexes)) {
                    $table->index(['company_id', 'user_id', 'job_id'], 'idx_job_assign_company_user_job');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('bench_assign')) {
            Schema::table('bench_assign', function (Blueprint $table) {
                $table->dropIndex('idx_bench_assign_user_company');
            });
        }

        if (Schema::hasTable('job_assign')) {
            Schema::table('job_assign', function (Blueprint $table) {
                $table->dropIndex('idx_job_assign_company_spok_job');
                $table->dropIndex('idx_job_assign_company_user_job');
            });
        }
    }
};
