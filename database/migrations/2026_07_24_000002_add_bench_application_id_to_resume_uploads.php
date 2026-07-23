<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Phase 3 — add bench_application_id so a session can be Linked to either
 * a JobApplication (candidate row) OR a BenchApplication (bench row).
 *
 * The existing controller flow writes bench resumes into the
 * `job_applications` table using the bench row's id, which is a legacy
 * quirk that Phase 3 keeps intact (we don't migrate historical data here).
 * For new uploads, we keep the same write + also point candidate_id at 0
 * and bench_application_id at the bench row, so the session graph is
 * unambiguous going forward.
 *
 * Both columns stay nullable; the legacy code path uses one OR the other.
 * resource_type (added in the previous migration) is the discriminator.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('resume_uploads', 'bench_application_id')) {
            Schema::table('resume_uploads', function (Blueprint $table) {
                $table->unsignedInteger('bench_application_id')->nullable()->after('candidate_id');
                $table->index('bench_application_id', 'idx_bench_application_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('resume_uploads', 'bench_application_id')) {
            Schema::table('resume_uploads', function (Blueprint $table) {
                $table->dropIndex('idx_bench_application_id');
                $table->dropColumn('bench_application_id');
            });
        }
    }
};
