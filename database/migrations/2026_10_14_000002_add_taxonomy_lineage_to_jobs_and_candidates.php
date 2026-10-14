<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * JERO Taxonomy V2 — Phase 1: Additive Lineage Columns on jobs, job_applications, and bench_applications
     */
    public function up(): void
    {
        // 1. Add lineage columns to jobs
        if (Schema::hasTable('jobs')) {
            Schema::table('jobs', function (Blueprint $table) {
                if (!Schema::hasColumn('jobs', 'raw_designation')) {
                    $table->string('raw_designation', 191)->nullable()->after('title');
                }
                if (!Schema::hasColumn('jobs', 'canonical_designation_id')) {
                    $table->unsignedBigInteger('canonical_designation_id')->nullable()->after('raw_designation');
                    $table->index('canonical_designation_id', 'idx_jobs_canonical_desig_id');
                }
                if (!Schema::hasColumn('jobs', 'taxonomy_version')) {
                    $table->string('taxonomy_version', 32)->nullable()->after('canonical_designation_id');
                }
            });
        }

        // 2. Add lineage columns to job_applications (All candidate applications & bench talent)
        if (Schema::hasTable('job_applications')) {
            Schema::table('job_applications', function (Blueprint $table) {
                if (!Schema::hasColumn('job_applications', 'raw_designation')) {
                    $table->string('raw_designation', 191)->nullable()->after('current_designation');
                }
                if (!Schema::hasColumn('job_applications', 'canonical_designation_id')) {
                    $table->unsignedBigInteger('canonical_designation_id')->nullable()->after('raw_designation');
                    $table->index('canonical_designation_id', 'idx_job_app_canonical_desig_id');
                }
                if (!Schema::hasColumn('job_applications', 'taxonomy_version')) {
                    $table->string('taxonomy_version', 32)->nullable()->after('canonical_designation_id');
                }
            });
        }

        // 3. Add lineage columns to bench_applications
        if (Schema::hasTable('bench_applications')) {
            Schema::table('bench_applications', function (Blueprint $table) {
                if (!Schema::hasColumn('bench_applications', 'raw_designation')) {
                    $table->string('raw_designation', 191)->nullable()->after('current_designation');
                }
                if (!Schema::hasColumn('bench_applications', 'canonical_designation_id')) {
                    $table->unsignedBigInteger('canonical_designation_id')->nullable()->after('raw_designation');
                    $table->index('canonical_designation_id', 'idx_bench_app_canonical_desig_id');
                }
                if (!Schema::hasColumn('bench_applications', 'taxonomy_version')) {
                    $table->string('taxonomy_version', 32)->nullable()->after('canonical_designation_id');
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
                if (Schema::hasColumn('jobs', 'canonical_designation_id')) {
                    $table->dropIndex('idx_jobs_canonical_desig_id');
                    $table->dropColumn('canonical_designation_id');
                }
                if (Schema::hasColumn('jobs', 'raw_designation')) {
                    $table->dropColumn('raw_designation');
                }
                if (Schema::hasColumn('jobs', 'taxonomy_version')) {
                    $table->dropColumn('taxonomy_version');
                }
            });
        }

        if (Schema::hasTable('job_applications')) {
            Schema::table('job_applications', function (Blueprint $table) {
                if (Schema::hasColumn('job_applications', 'canonical_designation_id')) {
                    $table->dropIndex('idx_job_app_canonical_desig_id');
                    $table->dropColumn('canonical_designation_id');
                }
                if (Schema::hasColumn('job_applications', 'raw_designation')) {
                    $table->dropColumn('raw_designation');
                }
                if (Schema::hasColumn('job_applications', 'taxonomy_version')) {
                    $table->dropColumn('taxonomy_version');
                }
            });
        }

        if (Schema::hasTable('bench_applications')) {
            Schema::table('bench_applications', function (Blueprint $table) {
                if (Schema::hasColumn('bench_applications', 'canonical_designation_id')) {
                    $table->dropIndex('idx_bench_app_canonical_desig_id');
                    $table->dropColumn('canonical_designation_id');
                }
                if (Schema::hasColumn('bench_applications', 'raw_designation')) {
                    $table->dropColumn('raw_designation');
                }
                if (Schema::hasColumn('bench_applications', 'taxonomy_version')) {
                    $table->dropColumn('taxonomy_version');
                }
            });
        }
    }
};
