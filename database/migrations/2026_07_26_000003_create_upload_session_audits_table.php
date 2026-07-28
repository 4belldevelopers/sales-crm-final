<?php
// Intent: Immutable audit log for every state transition in the upload
// lifecycle. Append-only — rows are never updated or deleted.

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('upload_session_audits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('resume_upload_id');
            $table->foreign('resume_upload_id')
                  ->references('id')
                  ->on('resume_uploads')
                  ->cascadeOnDelete();

            // Tenant scope (denormalised for query safety — CLAUDE.md rule 3)
            $table->unsignedInteger('company_id')->nullable()->index();
            $table->unsignedInteger('user_id')->nullable();

            // What happened
            $table->string('action', 64);     // ActionCreated, ActionUploadCompleted, etc.
            $table->json('payload')->nullable(); // previous_status, duration_ms, error_code, etc.

            // Runtime context
            $table->string('request_id', 64)->nullable(); // ULID from X-Request-ID header
            $table->string('pod_id', 64)->nullable();     // hostname() of the Octane pod
            $table->string('error_code', 64)->nullable(); // normalized error code, if any
            $table->text('message_safe')->nullable();     // sanitized human message

            // Privacy-safe IP fingerprint for abuse investigation
            $table->char('ip_hash', 64)->nullable();

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->index(['resume_upload_id', 'created_at'], 'audit_session_time_idx');
            $table->index(['action', 'created_at'], 'audit_action_time_idx');
            $table->index(['company_id', 'created_at'], 'audit_company_time_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('upload_session_audits');
    }
};
