<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds the SSOT foreign key on job_applications back to resume_uploads.
 *
 * Pre-migration: job_applications.resume is the SSOT for legacy rows. (Affects
 * roughly all rows written by /uploadresume before 2026-07-23.)
 *
 * Post-migration: job_applications.current_resume_session_uuid is the SSOT
 * link; .resume becomes a denormalized cache populated on the Linked
 * transition. A reconciliation command (Phase 5) populates this column
 * retroactively from existing resume URLs.
 *
 * Both nullable so:
 *   - Old rows still resolve through .resume alone.
 *   - New rows can write the audit/transition before .resume is materialized.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            $table->char('current_resume_session_uuid', 36)->nullable()->after('resume');
            $table->index('current_resume_session_uuid', 'idx_current_resume_session');
        });
    }

    public function down(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            $table->dropIndex('idx_current_resume_session');
            $table->dropColumn('current_resume_session_uuid');
        });
    }
};
