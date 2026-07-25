<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Phase 5 of the resume-upload session migration.
 *
 * Adds the Reconciled terminal state + cleanup_reason forensic column to
 * resume_uploads so the reconciler can record WHY a row was deleted (vs
 * the GC job's nightly sweep) and ops can diff GC vs Reconciler volumes.
 *
 * Reconciliation is a separate stream from the GC job because:
 *   - GC runs on a tight nightly schedule (03:30 IST) and only sees
 *     Parsed/AbortedBySubmit/Replaced/ParseFailed/ManualReviewRequired
 *     past expires_at.
 *   - Reconciler runs weekly (Phase 5 schedule) and catches the
 *     longer-tail Uploaded-but-never-linked case where the user dropped off
 *     without ever submitting — those rows are physically on R2 with no
 *     pointer.
 *
 * Backward compat: additive — does not modify existing rows' status,
 * does not change transitions in code that depend on the old enum.
 *
 * Tenant isolation: cleanup_reason is a per-row forensic column; queries
 * against it MUST still filter by company_id (see CLAUDE.md BUG-03).
 */
return new class extends Migration
{
    public function up(): void
    {
        // 1) Extend the status ENUM to include Reconcile's terminal state.
        // We use a raw ALTER for MySQL so this works regardless of whether
        // doctrine/dbal is wired in (Laravel 11 doesn't need it for ENUM,
        // but the project's CI is conservative).
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE resume_uploads MODIFY COLUMN status ENUM(
                'Created',
                'Uploading',
                'Uploaded',
                'UploadFailed',
                'Parsing',
                'Parsed',
                'ParseFailed',
                'ManualReviewRequired',
                'Linked',
                'AbortedBySubmit',
                'Replaced',
                'Completed',
                'Cleaning',
                'DeleteFailed',
                'Expired',
                'RedactedForGDPR',
                'Reconciled'
            ) NOT NULL DEFAULT 'Created'");
        }
        // For SQLite (test env) the schema is hand-built in tests, so the
        // enum column is wired there with the new value already; this
        // migration is a no-op.

        // 2) cleanup_reason — short string identifying which subsystem
        // performed the cleanup (or "n/a" for safety when GC ran with no
        // specific cause). Bounded at 64 chars so it cannot balloon.
        Schema::table('resume_uploads', function (Blueprint $table) {
            $table->string('cleanup_reason', 64)->nullable()->after('cleanup_attempt_count');
        });

        // 3) Composite index supports ops queries like
        //    "show me all rows reconciler deleted in last 7 days"
        //    without scanning the whole table.
        $sm = Schema::getConnection()->getDoctrineSchemaManager();
        $indexes = method_exists($sm, 'listTableIndexes') ? $sm->listTableIndexes('resume_uploads') : [];
        $exists = false;
        foreach ($indexes as $idx) {
            if (method_exists($idx, 'getName') && $idx->getName() === 'idx_status_cleanup_reason') {
                $exists = true;
                break;
            }
        }
        if (! $exists) {
            Schema::table('resume_uploads', function (Blueprint $table) {
                $table->index(['status', 'cleanup_reason'], 'idx_status_cleanup_reason');
            });
        }
    }

    public function down(): void
    {
        Schema::table('resume_uploads', function (Blueprint $table) {
            $table->dropIndex('idx_status_cleanup_reason');
        });
        Schema::table('resume_uploads', function (Blueprint $table) {
            $table->dropColumn('cleanup_reason');
        });
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE resume_uploads MODIFY COLUMN status ENUM(
                'Created',
                'Uploading',
                'Uploaded',
                'UploadFailed',
                'Parsing',
                'Parsed',
                'ParseFailed',
                'ManualReviewRequired',
                'Linked',
                'AbortedBySubmit',
                'Replaced',
                'Completed',
                'Cleaning',
                'DeleteFailed',
                'Expired',
                'RedactedForGDPR'
            ) NOT NULL DEFAULT 'Created'");
        }
    }
};
