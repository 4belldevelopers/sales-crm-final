<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Phase 1 of the resume-upload session migration.
 *
 * Source of truth for resume lifecycle: one row per upload attempt.
 * Replaces the implicit "file uploaded → temp → copy → row authored" two-stage flow
 * with an explicit, queryable, GC-friendly state machine.
 *
 * Backward compatibility: this table is purely additive. The existing
 * job_applications.resume column remains the running SSOT for legacy rows;
 * going forward, job_applications.current_resume_session_uuid (added in the
 * follow-up migration) becomes the SSOT for new uploads.
 *
 * Tenant isolation: every query to this table MUST filter by company_id.
 * See CLAUDE.md BUG-03 / memory/tenant-isolation-blanket-scopes-break-marketplace.md
 * for why raw DB::table without a blanket-scope is correct here.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('resume_uploads')) {
            return;
        }

        Schema::create('resume_uploads', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Public bearer token. UUIDv4 — never user-derived.
            $table->char('uuid', 36)->unique();

            // Tenant boundary — every read MUST filter by company_id.
            $table->unsignedInteger('company_id');

            // Upload actor (recruiter / super-admin / etc.).
            $table->unsignedInteger('user_id')->nullable();

            // Job the candidate was being created for; nullable because some
            // cleanup flows run without an attached job.
            $table->unsignedInteger('job_id')->nullable();

            // FK to job_applications.id. NULL until the Linked transition.
            // Use the application-level join (no DB-level FK) so legacy rows
            // without a session can still exist on job_applications.
            $table->unsignedInteger('candidate_id')->nullable();

            // Tracks superseded sessions (NEW session replaces OLD on edit).
            $table->char('parent_session_uuid', 36)->nullable();
            $table->char('replaces_session_uuid', 36)->nullable();

            $table->string('original_filename', 255);
            // unique within the tenant's R2 directory — never shared across tenants.
            $table->string('stored_filename', 255);
            $table->string('r2_path', 512);
            $table->string('r2_url', 512);
            $table->string('mime_type', 127);
            $table->unsignedBigInteger('file_size_bytes');

            // sha256 of bytes — for duplicate detection ONLY, never used in storage key.
            // (Storage key uniqueness comes from uniqid(), so tenant isolation holds.)
            $table->char('dedupe_hash', 64)->nullable();

            // State machine — see App\Models\ResumeUpload for transition rules.
            $table->enum('status', [
                'Created',                // row + audit created, nothing on R2 yet
                'Uploading',              // browser PUT in flight (POST /upload-session called)
                'Uploaded',               // R2 has the file, post-write verify passed
                'UploadFailed',
                'Parsing',
                'Parsed',
                'ParseFailed',
                'ManualReviewRequired',
                'Linked',                 // bound to a JobApplication row in a committed tx
                'AbortedBySubmit',        // submit() rolled back; user may retry
                'Replaced',               // superseded by another session on edit flow; old R2 file held until new Linked
                'Completed',              // Linked and side-effects (elastic, mail) succeeded
                'Cleaning',               // GC claimed this row, deleting R2 object
                'DeleteFailed',           // R2 delete exhausted 3 retries
                'Expired',                // past expires_at with no Linked transition — kept distinct from Cleaning
                'RedactedForGDPR',        // soft-redact mode (tenant opted-in); row kept, file replaced
            ])->default('Created');

            $table->string('parser_vendor', 64)->nullable();
            $table->enum('parser_status', ['Queued', 'Running', 'Done', 'Failed', 'Cancelled'])->nullable();
            $table->unsignedTinyInteger('parse_attempt_count')->default(0);

            // Parser output — fields recruiter sees on recovery.
            $table->json('parsed_data_json')->nullable();

            // Three-timestamp triplet lets you observe timing independently.
            $table->timestamp('upload_started_at')->nullable();
            $table->timestamp('upload_completed_at')->nullable();
            $table->timestamp('parse_started_at')->nullable();
            $table->timestamp('parse_completed_at')->nullable();
            $table->timestamp('linked_at')->nullable();

            // GC bookkeeping — bounded retries before DeleteFailed.
            $table->unsignedTinyInteger('cleanup_attempt_count')->default(0);

            // Set on upload_completed_at + configurable window (default 24h).
            $table->timestamp('expires_at')->nullable();

            // Used by /upload-session Idempotency-Key support.
            $table->string('idempotency_key', 128)->nullable();

            $table->timestamps();

            // GC's main scan: WHERE status IN ('Parsed','AbortedBySubmit','Replaced') AND expires_at < NOW()
            $table->index(['status', 'expires_at'], 'idx_status_expires');
            // Per-company active-session lookup (recovery flow, list my drafts, etc.)
            $table->index(['company_id', 'user_id', 'job_id'], 'idx_company_user_job');
            // Lookup by candidate once Linked.
            $table->index('candidate_id', 'idx_candidate');
            // Idempotency lookup; per-company because tenant scoping on the key would be a leak.
            $table->index(['company_id', 'idempotency_key'], 'idx_company_idempotency');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resume_uploads');
    }
};
