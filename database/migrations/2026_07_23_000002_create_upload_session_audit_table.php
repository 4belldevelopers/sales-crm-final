<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Append-only audit log for resume_uploads state transitions.
 *
 * Per the architecture review:
 *   - Never DELETE rows. Subject to regulatory retention (SOC 2 / GDPR Art. 30).
 *     The GC job over the resume_uploads table does NOT touch this table.
 *   - Default retention: 7 years (SOC 2 baseline); make configurable at the app layer.
 *
 * Companion: App\Models\UploadSessionAudit. The model class refuses updates and
 * deletes; this is the schema-level backstop for append-only.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('upload_session_audit')) {
            return;
        }

        Schema::create('upload_session_audit', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->char('session_uuid', 36);
            $table->unsignedInteger('company_id');

            // Lifecycle event vocabulary. Kept narrow — adding new actions is rare.
            $table->string('action', 32)->comment([
                'Created',
                'UploadStart',
                'UploadSuccess',
                'UploadFailed',
                'ParseStart',
                'ParseDone',
                'ParseFailed',
                'Linked',
                'Aborted',
                'Replaced',
                'Completed',
                'CleaningStart',
                'CleaningR2Done',
                'CleaningDone',
                'CleaningFailed',
                'DeleteFailed',
                'Expired',
                'RedactedForGDPR',
                'Replay',
            ]);

            $table->unsignedInteger('actor_id')->nullable();
            $table->string('actor_ip', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->char('request_id', 36)->nullable();

            // Free-form per-action metadata (status_from, status_to, file_size, error, etc.)
            $table->json('metadata_json')->nullable();

            $table->unsignedInteger('latency_ms')->nullable();

            $table->timestamp('created_at')->useCurrent();

            // "Show me all events for session X" queries.
            $table->index(['session_uuid', 'created_at'], 'idx_session_time');
            // "Show me all DELETE_FAILED events in the last hour" — alerting.
            $table->index(['company_id', 'action', 'created_at'], 'idx_company_action_time');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('upload_session_audit');
    }
};
