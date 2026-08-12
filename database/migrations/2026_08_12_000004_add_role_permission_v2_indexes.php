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
        // 1. Roles Table: Composite index for tenant role listing & priority sorting
        if (Schema::hasTable('roles')) {
            Schema::table('roles', function (Blueprint $table) {
                $sm = Schema::getConnection()->getDoctrineSchemaManager();
                $indexes = array_keys($sm->listTableIndexes('roles'));
                if (! in_array('idx_roles_company_priority_name', $indexes)) {
                    $table->index(['company_id', 'priority', 'name'], 'idx_roles_company_priority_name');
                }
            });
        }

        // 2. Permissions Table: Composite index for active permission group counts
        if (Schema::hasTable('permissions')) {
            Schema::table('permissions', function (Blueprint $table) {
                $sm = Schema::getConnection()->getDoctrineSchemaManager();
                $indexes = array_keys($sm->listTableIndexes('permissions'));
                if (! in_array('idx_permissions_status_group_id', $indexes)) {
                    $table->index(['status', 'group_name', 'id'], 'idx_permissions_status_group_id');
                }
            });
        }

        // 3. Role User Table: Composite index for role-user join & count aggregation
        if (Schema::hasTable('role_user')) {
            Schema::table('role_user', function (Blueprint $table) {
                $sm = Schema::getConnection()->getDoctrineSchemaManager();
                $indexes = array_keys($sm->listTableIndexes('role_user'));
                if (! in_array('idx_role_user_role_user', $indexes)) {
                    $table->index(['role_id', 'user_id'], 'idx_role_user_role_user');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('roles')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->dropIndex('idx_roles_company_priority_name');
            });
        }

        if (Schema::hasTable('permissions')) {
            Schema::table('permissions', function (Blueprint $table) {
                $table->dropIndex('idx_permissions_status_group_id');
            });
        }

        if (Schema::hasTable('role_user')) {
            Schema::table('role_user', function (Blueprint $table) {
                $table->dropIndex('idx_role_user_role_user');
            });
        }
    }
};
