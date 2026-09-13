<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Intent: Tables for Ephemeral Cloudflare R2 Forward Resume Ingestion,
     * Multi-Recipient Delivery Barrier, and Delivery Attempt Idempotency.
     */
    public function up(): void
    {
        if (! Schema::hasTable('forward_requests')) {
            Schema::create('forward_requests', function (Blueprint $table) {
                $table->id();
                $table->uuid('uuid')->unique();
                $table->unsignedInteger('company_id')->index();
                $table->unsignedInteger('user_id')->nullable()->index();
                $table->unsignedSmallInteger('candidate_count')->default(1);
                $table->unsignedSmallInteger('recipient_count')->default(1);
                $table->string('status', 32)->default('QUEUED')->index();
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();

                $table->index(['company_id', 'created_at'], 'idx_fwd_req_comp_created');
            });
        }

        if (! Schema::hasTable('forward_attachments')) {
            Schema::create('forward_attachments', function (Blueprint $table) {
                $table->id();
                $table->uuid('uuid')->unique();
                $table->unsignedBigInteger('forward_request_id')->nullable()->index();
                $table->unsignedInteger('company_id')->index();
                $table->unsignedInteger('candidate_id')->nullable()->index();
                $table->string('r2_object_key', 512);
                $table->string('filename', 255);
                $table->unsignedBigInteger('file_size_bytes')->nullable();
                $table->string('mime_type', 64)->default('application/pdf');
                $table->string('status', 32)->default('PENDING')->index();
                $table->timestamp('expires_at')->nullable()->index();
                $table->timestamp('deleted_at')->nullable();
                $table->timestamps();

                $table->index(['company_id', 'uuid'], 'idx_fwd_att_comp_uuid');
                $table->index(['status', 'expires_at'], 'idx_fwd_att_status_exp');
            });
        }

        if (! Schema::hasTable('forward_delivery_attempts')) {
            Schema::create('forward_delivery_attempts', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('forward_request_id')->index();
                $table->string('recipient_email', 255);
                $table->string('status', 32)->default('PENDING')->index();
                $table->unsignedSmallInteger('attempt_count')->default(0);
                $table->char('idempotency_key', 64)->index();
                $table->string('provider_message_id', 255)->nullable();
                $table->text('last_error')->nullable();
                $table->timestamp('sent_at')->nullable();
                $table->timestamps();

                $table->unique(['forward_request_id', 'recipient_email'], 'idx_fwd_attempts_req_recipient');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forward_delivery_attempts');
        Schema::dropIfExists('forward_attachments');
        Schema::dropIfExists('forward_requests');
    }
};
