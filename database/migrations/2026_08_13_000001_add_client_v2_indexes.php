<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Add Client V2 Performance Indexes
 *
 * Intent: Add targeted composite and foreign key indexes across clients, client_contact_info,
 * client_file, and client_location tables to eliminate unindexed full table scans.
 * Tenancy: All tenancy-scoped tables incorporate company_id or client_id.
 *
 * Author: Antigravity AI
 * Date: August 13, 2026
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Clients Table: Tenant lookup, website domain check, & list sorting
        if (Schema::hasTable('clients')) {
            Schema::table('clients', function (Blueprint $table) {
                if (! Schema::hasIndex('clients', 'idx_clients_company_id')) {
                    $table->index(['company_id'], 'idx_clients_company_id');
                }
                if (! Schema::hasIndex('clients', 'idx_clients_website')) {
                    $table->index(['website'], 'idx_clients_website');
                }
                if (! Schema::hasIndex('clients', 'idx_clients_company_created')) {
                    $table->index(['company_id', 'created_at'], 'idx_clients_company_created');
                }
            });
        }

        // 2. Client Contact Info Table: Client relation & email lookup
        if (Schema::hasTable('client_contact_info')) {
            Schema::table('client_contact_info', function (Blueprint $table) {
                if (! Schema::hasIndex('client_contact_info', 'idx_client_contact_info_client_id')) {
                    $table->index(['client_id'], 'idx_client_contact_info_client_id');
                }
                if (! Schema::hasIndex('client_contact_info', 'idx_client_contact_info_client_id_id')) {
                    $table->index(['client_id', 'id'], 'idx_client_contact_info_client_id_id');
                }
                if (! Schema::hasIndex('client_contact_info', 'idx_client_contact_info_email')) {
                    $table->index(['contact_person_email'], 'idx_client_contact_info_email');
                }
            });
        }

        // 3. Client File Table: Client relation & tenant file lookup
        if (Schema::hasTable('client_file')) {
            Schema::table('client_file', function (Blueprint $table) {
                if (! Schema::hasIndex('client_file', 'idx_client_file_client_id')) {
                    $table->index(['client_id'], 'idx_client_file_client_id');
                }
                if (! Schema::hasIndex('client_file', 'idx_client_file_company_id')) {
                    $table->index(['company_id'], 'idx_client_file_company_id');
                }
            });
        }

        // 4. Client Location Table: Tenant scoping & composite lookup
        if (Schema::hasTable('client_location')) {
            Schema::table('client_location', function (Blueprint $table) {
                if (! Schema::hasIndex('client_location', 'idx_client_location_company_id')) {
                    $table->index(['company_id'], 'idx_client_location_company_id');
                }
                if (! Schema::hasIndex('client_location', 'idx_client_location_client_city_addr')) {
                    $table->index(['client_id', 'city_id', 'address'], 'idx_client_location_client_city_addr');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('clients')) {
            Schema::table('clients', function (Blueprint $table) {
                $table->dropIndex('idx_clients_company_id');
                $table->dropIndex('idx_clients_website');
                $table->dropIndex('idx_clients_company_created');
            });
        }

        if (Schema::hasTable('client_contact_info')) {
            Schema::table('client_contact_info', function (Blueprint $table) {
                $table->dropIndex('idx_client_contact_info_client_id');
                $table->dropIndex('idx_client_contact_info_client_id_id');
                $table->dropIndex('idx_client_contact_info_email');
            });
        }

        if (Schema::hasTable('client_file')) {
            Schema::table('client_file', function (Blueprint $table) {
                $table->dropIndex('idx_client_file_client_id');
                $table->dropIndex('idx_client_file_company_id');
            });
        }

        if (Schema::hasTable('client_location')) {
            Schema::table('client_location', function (Blueprint $table) {
                $table->dropIndex('idx_client_location_company_id');
                $table->dropIndex('idx_client_location_client_city_addr');
            });
        }
    }
};
