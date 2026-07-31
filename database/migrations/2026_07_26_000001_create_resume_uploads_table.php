<?php
// Intent: Create the resume_uploads table for v2 upload session tracking.
// Stores session metadata only — file bytes never traverse PHP.
// All feature flags that reference this table default to OFF.

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resume_uploads', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            // Tenant scope — CLAUDE.md rule 3: always filter by company_id
            $table->unsignedInteger('company_id')->index();
            $table->unsignedInteger('user_id')->nullable()->index(); // null for public career pages

            // File metadata (no bytes stored here — bytes go to R2)
            $table->string('original_filename', 255)->nullable();
            $table->string('stored_filename', 255)->nullable();
            $table->char('sha256_hex', 64)->nullable();          // SHA256 of file bytes (client-stated, informational)
            $table->string('mime_type', 127)->nullable();
            $table->unsignedBigInteger('file_size_bytes')->nullable(); // cap: 6 MB

            // Cloudflare R2 references
            $table->string('r2_temp_key', 512)->nullable();     // object key in temp bucket
            $table->text('r2_temp_url')->nullable();             // presigned PUT URL
            $table->string('r2_url', 512)->nullable();           // final baked URL after reconcile

            // Idempotency — INV-4: INDEX only, NOT UNIQUE
            // Per-request random UUID from crypto.randomUUID(); replays the session on same key
            $table->char('idempotency_key', 64)->nullable();

            // Status — matches ResumeUploadStatus enum
            $table->string('status', 32)->default('Pending')->index();

            // Links to candidate records (set on submit)
            $table->unsignedInteger('candidate_id')->nullable();
            $table->unsignedInteger('bench_application_id')->nullable();
            $table->unsignedInteger('linked_candidate_id')->nullable(); // v2 conservative dedupe link

            // REMOVED 2026-07-27: parser_started_at, parser_completed_at,
            // parser_attempts, last_parser_pod_id — no server-side parser in v2
            $table->text('last_error')->nullable();  // generic error code for any failure

            // Lifecycle
            $table->timestamp('upload_completed_at')->nullable();
            $table->timestamp('expires_at')->nullable();          // Created + 30 min TTL
            $table->string('cleanup_reason', 64)->nullable();

            $table->timestamps();

            // Compound indexes
            // INV-4: idempotency_key uses INDEX (not UNIQUE) — same key → replay; different key → new session
            $table->index(['company_id', 'idempotency_key'], 'resume_uploads_company_idem_key_index');

            // Tenant-scoped UUID lookup (show/refresh/finalize)
            $table->unique(['uuid', 'company_id'], 'resume_uploads_uuid_company_unique');

            // Cron sweep: find expired or stuck sessions
            $table->index(['status', 'expires_at'], 'resume_uploads_status_expires_at_index');

            // Tenant listing with pagination
            $table->index(['company_id', 'created_at'], 'resume_uploads_company_created_at_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resume_uploads');
    }
};
