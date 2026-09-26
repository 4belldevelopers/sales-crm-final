<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Intent: Create candidate_outbox_events, resume_assets tables and add V4 resilience
     * columns/indexes to job_applications for zero-loss transactional persistence under 10K concurrency.
     */
    public function up(): void
    {
        // 1. Transactional Outbox Events for Candidate indexing
        if (! Schema::hasTable('candidate_outbox_events')) {
            Schema::create('candidate_outbox_events', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('company_id');
                $table->string('dedupe_key', 191)->unique('uq_outbox_dedupe');
                $table->string('aggregate_type', 50)->default('Candidate');
                $table->unsignedBigInteger('aggregate_id');
                $table->string('event_type', 50);
                $table->unsignedSmallInteger('event_version')->default(1);
                $table->json('payload');
                $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
                $table->unsignedTinyInteger('retry_count')->default(0);
                $table->timestamp('available_at')->useCurrent();
                $table->timestamp('locked_at')->nullable();
                $table->string('locked_by', 100)->nullable();
                $table->string('lock_token', 36)->nullable();
                $table->text('last_error')->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

                $table->index(['status', 'available_at', 'locked_at'], 'idx_outbox_claim');
                $table->index(['company_id', 'aggregate_id'], 'idx_outbox_company_aggregate');
            });
        }

        // 2. Durable Resume Assets table
        if (! Schema::hasTable('resume_assets')) {
            Schema::create('resume_assets', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('company_id');
                $table->unsignedInteger('candidate_id')->nullable();
                $table->string('upload_session_id', 64)->nullable();
                $table->string('object_key', 500);
                $table->string('original_filename', 255);
                $table->string('mime_type', 100);
                $table->unsignedBigInteger('file_size');
                $table->char('checksum_sha256', 64)->nullable();
                $table->unsignedInteger('version')->default(1);
                $table->enum('status', ['ACTIVE', 'REMOVAL_PENDING', 'REMOVED', 'ORPHANED', 'CLEANUP_PENDING'])->default('ACTIVE');
                $table->enum('parser_status', ['PARSED', 'FAILED', 'BYPASSED'])->default('BYPASSED');
                $table->string('parser_version', 50)->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('attached_at')->nullable();
                $table->timestamp('removed_at')->nullable();

                $table->index(['candidate_id', 'status'], 'idx_resume_candidate');
                $table->index(['company_id', 'upload_session_id'], 'idx_resume_company_session');
                $table->index(['status', 'removed_at'], 'idx_resume_cleanup');
            });
        }

        // 3. Extend job_applications table with V4 resilience columns & indexes
        if (Schema::hasTable('job_applications')) {
            Schema::table('job_applications', function (Blueprint $table) {
                if (! Schema::hasColumn('job_applications', 'current_resume_id')) {
                    $table->unsignedBigInteger('current_resume_id')->nullable()->after('resume');
                }
                if (! Schema::hasColumn('job_applications', 'resume_version')) {
                    $table->unsignedInteger('resume_version')->default(1)->after('current_resume_id');
                }
                if (! Schema::hasColumn('job_applications', 'client_submission_uuid')) {
                    $table->string('client_submission_uuid', 36)->nullable()->after('resume_version');
                }
            });

            // Add indexes safely if they do not exist
            try {
                Schema::table('job_applications', function (Blueprint $table) {
                    $table->index('current_resume_id', 'idx_job_app_current_resume');
                });
            } catch (\Throwable) {}

            try {
                Schema::table('job_applications', function (Blueprint $table) {
                    $table->unique(['company_id', 'client_submission_uuid'], 'uq_company_submission_uuid');
                });
            } catch (\Throwable) {}
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('job_applications')) {
            Schema::table('job_applications', function (Blueprint $table) {
                try {
                    $table->dropUnique('uq_company_submission_uuid');
                } catch (\Throwable) {}
                try {
                    $table->dropIndex('idx_job_app_current_resume');
                } catch (\Throwable) {}

                if (Schema::hasColumn('job_applications', 'client_submission_uuid')) {
                    $table->dropColumn('client_submission_uuid');
                }
                if (Schema::hasColumn('job_applications', 'resume_version')) {
                    $table->dropColumn('resume_version');
                }
                if (Schema::hasColumn('job_applications', 'current_resume_id')) {
                    $table->dropColumn('current_resume_id');
                }
            });
        }

        Schema::dropIfExists('resume_assets');
        Schema::dropIfExists('candidate_outbox_events');
    }
};
