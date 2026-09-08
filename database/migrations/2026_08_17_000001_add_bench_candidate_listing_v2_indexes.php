<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Add Bench Candidate Listing V2 Performance Indexes
 *
 * Intent: Add targeted composite and foreign key indexes across clients, commericials,
 * commericial_details, recommendation_relation, and bench_candidate_skill_experience tables
 * to optimize database lookups and prevent full table scans on public and internal bench pages.
 * Tenancy: All tenancy-scoped tables incorporate company_id or client_id.
 *
 * Author: Antigravity AI
 * Date: August 17, 2026
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Clients Table: Fast lookup for self-company profile by company_id
        if (Schema::hasTable('clients')) {
            Schema::table('clients', function (Blueprint $table) {
                if (! Schema::hasIndex('clients', 'idx_clients_company_self')) {
                    $table->index(['company_id', 'self_company'], 'idx_clients_company_self');
                }
            });
        }

        // 2. Commercials Table: Fast lookup for commercial configuration by company_id and commtype
        if (Schema::hasTable('commericials')) {
            Schema::table('commericials', function (Blueprint $table) {
                if (! Schema::hasIndex('commericials', 'idx_commericials_company_type')) {
                    $table->index(['company_id', 'commtype'], 'idx_commericials_company_type');
                }
            });
        }

        // 3. Commercial Details Table: Fast rate aggregate queries
        if (Schema::hasTable('commericial_details')) {
            Schema::table('commericial_details', function (Blueprint $table) {
                if (! Schema::hasIndex('commericial_details', 'idx_cd_company_comm')) {
                    $table->index(['company_id', 'commtype'], 'idx_cd_company_comm');
                }
            });
        }

        // 4. Recommendation Relation Table: Fast connection lookup between companies
        if (Schema::hasTable('recommendation_relation')) {
            Schema::table('recommendation_relation', function (Blueprint $table) {
                if (! Schema::hasIndex('recommendation_relation', 'idx_rr_from_to_final')) {
                    $table->index(['from_company_id', 'to_company_id', 'final_status'], 'idx_rr_from_to_final');
                }
            });
        }

        // 5. Bench Candidate Skill Experience Table: Candidate skill lookup
        if (Schema::hasTable('bench_candidate_skill_experience')) {
            Schema::table('bench_candidate_skill_experience', function (Blueprint $table) {
                if (! Schema::hasIndex('bench_candidate_skill_experience', 'idx_cand_skill')) {
                    $table->index(['candidate_id', 'skill_key'], 'idx_cand_skill');
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
                if (Schema::hasIndex('clients', 'idx_clients_company_self')) {
                    $table->dropIndex('idx_clients_company_self');
                }
            });
        }

        if (Schema::hasTable('commericials')) {
            Schema::table('commericials', function (Blueprint $table) {
                if (Schema::hasIndex('commericials', 'idx_commericials_company_type')) {
                    $table->dropIndex('idx_commericials_company_type');
                }
            });
        }

        if (Schema::hasTable('commericial_details')) {
            Schema::table('commericial_details', function (Blueprint $table) {
                if (Schema::hasIndex('commericial_details', 'idx_cd_company_comm')) {
                    $table->dropIndex('idx_cd_company_comm');
                }
            });
        }

        if (Schema::hasTable('recommendation_relation')) {
            Schema::table('recommendation_relation', function (Blueprint $table) {
                if (Schema::hasIndex('recommendation_relation', 'idx_rr_from_to_final')) {
                    $table->dropIndex('idx_rr_from_to_final');
                }
            });
        }

        if (Schema::hasTable('bench_candidate_skill_experience')) {
            Schema::table('bench_candidate_skill_experience', function (Blueprint $table) {
                if (Schema::hasIndex('bench_candidate_skill_experience', 'idx_cand_skill')) {
                    $table->dropIndex('idx_cand_skill');
                }
            });
        }
    }
};
