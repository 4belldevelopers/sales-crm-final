<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Hardens job_applications with client idempotency UUID, dual optimistic concurrency
     * versions (aggregate_version vs resume_version), and compound indices for 10K bench query performance.
     */
    public function up(): void
    {
        if (Schema::hasTable('job_applications')) {
            Schema::table('job_applications', function (Blueprint $table) {
                if (! Schema::hasColumn('job_applications', 'client_submission_uuid')) {
                    $table->string('client_submission_uuid', 64)->nullable()->after('id')
                        ->comment('Client-generated UUIDv4 preventing duplicate network submissions');
                }

                if (! Schema::hasColumn('job_applications', 'aggregate_version')) {
                    $table->unsignedInteger('aggregate_version')->default(1)->after('client_submission_uuid')
                        ->comment('Optimistic concurrency lock version incremented on ANY candidate profile mutation');
                }

                if (! Schema::hasColumn('job_applications', 'resume_version')) {
                    $table->unsignedInteger('resume_version')->default(1)->after('resume')
                        ->comment('Optimistic concurrency lock version incremented ONLY on resume asset mutations');
                }
            });

            // Compound index for bench candidate multi-tenant status queries (0% full scans)
            $existingIndexes = collect(DB::select("SHOW INDEX FROM `job_applications`"))->pluck('Key_name')->unique()->toArray();

            Schema::table('job_applications', function (Blueprint $table) use ($existingIndexes) {
                if (! in_array('idx_bench_company_type_status', $existingIndexes)) {
                    $table->index(['company_id', 'condidate_type', 'status'], 'idx_bench_company_type_status');
                }

                if (! in_array('uq_company_submission_uuid', $existingIndexes)) {
                    $table->unique(['company_id', 'client_submission_uuid'], 'uq_company_submission_uuid');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('job_applications')) {
            $existingIndexes = collect(DB::select("SHOW INDEX FROM `job_applications`"))->pluck('Key_name')->unique()->toArray();

            Schema::table('job_applications', function (Blueprint $table) use ($existingIndexes) {
                if (in_array('uq_company_submission_uuid', $existingIndexes)) {
                    $table->dropUnique('uq_company_submission_uuid');
                }
                if (in_array('idx_bench_company_type_status', $existingIndexes)) {
                    $table->dropIndex('idx_bench_company_type_status');
                }
                if (Schema::hasColumn('job_applications', 'client_submission_uuid')) {
                    $table->dropColumn('client_submission_uuid');
                }
                if (Schema::hasColumn('job_applications', 'aggregate_version')) {
                    $table->dropColumn('aggregate_version');
                }
                if (Schema::hasColumn('job_applications', 'resume_version')) {
                    $table->dropColumn('resume_version');
                }
            });
        }
    }
};
