<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Add Client Details V2 Performance Indexes
 *
 * Intent: Add composite index on jobs(client_id, status) and client_contact_info(client_id)
 * to accelerate active requisition counts and contact queries for client drawer screens.
 * Tenancy: All tenancy-scoped queries incorporate client_id.
 *
 * Author: Antigravity AI
 * Date: September 3, 2026
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('jobs')) {
            Schema::table('jobs', function (Blueprint $table) {
                if (! Schema::hasIndex('jobs', 'idx_jobs_client_status')) {
                    $table->index(['client_id', 'status'], 'idx_jobs_client_status');
                }
            });
        }

        if (Schema::hasTable('client_contact_info')) {
            Schema::table('client_contact_info', function (Blueprint $table) {
                if (! Schema::hasIndex('client_contact_info', 'idx_client_contact_client_id')) {
                    $table->index(['client_id'], 'idx_client_contact_client_id');
                }
            });
        }

        if (Schema::hasTable('client_file')) {
            Schema::table('client_file', function (Blueprint $table) {
                if (! Schema::hasIndex('client_file', 'idx_client_file_client_id')) {
                    $table->index(['client_id'], 'idx_client_file_client_id');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('jobs')) {
            Schema::table('jobs', function (Blueprint $table) {
                $table->dropIndex('idx_jobs_client_status');
            });
        }

        if (Schema::hasTable('client_contact_info')) {
            Schema::table('client_contact_info', function (Blueprint $table) {
                $table->dropIndex('idx_client_contact_client_id');
            });
        }

        if (Schema::hasTable('client_file')) {
            Schema::table('client_file', function (Blueprint $table) {
                $table->dropIndex('idx_client_file_client_id');
            });
        }
    }
};
