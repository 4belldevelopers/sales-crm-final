<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Add Checkout Subscribe V2 Scalability & Performance Indexes
 *
 * Intent: Add targeted composite and lookup indexes across billing_address_companies,
 * client_location, state_list, and city_list tables to eliminate full table scans
 * during checkout and dynamic location selection under 10K+ concurrent recruiter load.
 * Tenancy: All tenant-scoped tables incorporate company_id or client_id.
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
        // 1. Billing Address Companies Table: Tenant lookup during checkout
        if (Schema::hasTable('billing_address_companies')) {
            Schema::table('billing_address_companies', function (Blueprint $table) {
                if (! Schema::hasIndex('billing_address_companies', 'idx_billing_company_id')) {
                    $table->index(['company_id'], 'idx_billing_company_id');
                }
            });
        }

        // 2. Client Location Table: Client and tenant relation lookup
        if (Schema::hasTable('client_location')) {
            Schema::table('client_location', function (Blueprint $table) {
                if (! Schema::hasIndex('client_location', 'idx_client_location_client_id')) {
                    $table->index(['client_id'], 'idx_client_location_client_id');
                }
                if (! Schema::hasIndex('client_location', 'idx_client_location_comp_client')) {
                    $table->index(['company_id', 'client_id'], 'idx_client_location_comp_client');
                }
            });
        }

        // 3. State List Table: Country-based state dropdown lookup
        if (Schema::hasTable('state_list')) {
            Schema::table('state_list', function (Blueprint $table) {
                if (! Schema::hasIndex('state_list', 'idx_state_list_country_id')) {
                    $table->index(['country_id'], 'idx_state_list_country_id');
                }
            });
        }

        // 4. City List Table: State-based city dropdown lookup
        if (Schema::hasTable('city_list')) {
            Schema::table('city_list', function (Blueprint $table) {
                if (! Schema::hasIndex('city_list', 'idx_city_list_state_id')) {
                    $table->index(['state_id'], 'idx_city_list_state_id');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('billing_address_companies')) {
            Schema::table('billing_address_companies', function (Blueprint $table) {
                $table->dropIndex('idx_billing_company_id');
            });
        }

        if (Schema::hasTable('client_location')) {
            Schema::table('client_location', function (Blueprint $table) {
                $table->dropIndex('idx_client_location_client_id');
                $table->dropIndex('idx_client_location_comp_client');
            });
        }

        if (Schema::hasTable('state_list')) {
            Schema::table('state_list', function (Blueprint $table) {
                $table->dropIndex('idx_state_list_country_id');
            });
        }

        if (Schema::hasTable('city_list')) {
            Schema::table('city_list', function (Blueprint $table) {
                $table->dropIndex('idx_city_list_state_id');
            });
        }
    }
};
