<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Phase 3 — extend resume_uploads so a single SSOT row can be Linked to
 * either a JobApplication (the HEAD-of-pipeline path) OR a BenchApplication
 * (the bench/agency path). Not polymorphic type juggling in app code —
 * just one tiny discriminator column.
 *
 * Backward compatibility: existing rows get resource_type='job_application'
 * because every row created up to Phase 3 was a JobApplication-Link.
 * Bench uploads created from this point onward will pick 'bench_application'.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('resume_uploads', 'resource_type')) {
            Schema::table('resume_uploads', function (Blueprint $table) {
                // ENUM-with-default nullable so legacy rows are explicitly labelled.
                $table->enum('resource_type', ['job_application', 'bench_application'])
                    ->default('job_application')
                    ->after('candidate_id');
                $table->index('resource_type', 'idx_resource_type');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('resume_uploads', 'resource_type')) {
            Schema::table('resume_uploads', function (Blueprint $table) {
                $table->dropIndex('idx_resource_type');
                $table->dropColumn('resource_type');
            });
        }
    }
};
