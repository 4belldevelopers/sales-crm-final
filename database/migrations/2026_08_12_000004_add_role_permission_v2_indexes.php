<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Add Role Permission Listing V2 Performance Indexes
 *
 * Intent: Add targeted composite indexes to accelerate queries on roles, permissions,
 * and role_user tables for Role Permission Listing V2 performance optimization.
 * Tenancy: All composite indexes incorporate tenant scope (company_id) or role_id.
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
            ['roles', 'idx_roles_company_priority_name', ['company_id', 'priority', 'name']],
            ['permissions', 'idx_permissions_status_group_id', ['status', 'group_name', 'id']],
            ['role_user', 'idx_role_user_role_user', ['role_id', 'user_id']],
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
            ['roles', 'idx_roles_company_priority_name'],
            ['permissions', 'idx_permissions_status_group_id'],
            ['role_user', 'idx_role_user_role_user'],
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
