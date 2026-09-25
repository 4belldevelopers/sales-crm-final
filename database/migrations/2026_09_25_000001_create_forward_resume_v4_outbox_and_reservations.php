<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Intent: Tables and columns for Forward Resume V4 Transactional Outbox,
     * Durable Coin Reservations, Forensic Event Journal, and Verification Metadata.
     */
    public function up(): void
    {
        // 1. Outbox Events Table
        if (! Schema::hasTable('outbox_events')) {
            Schema::create('outbox_events', function (Blueprint $table) {
                $table->id();
                $table->string('event_id', 36)->unique();
                $table->string('aggregate_type', 64);
                $table->string('aggregate_id', 36);
                $table->string('event_type', 64);
                $table->json('payload');
                $table->enum('status', ['PENDING', 'PUBLISHED', 'FAILED'])->default('PENDING');
                $table->unsignedTinyInteger('attempts')->default(0);
                $table->timestamp('published_at')->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();

                $table->index(['status', 'created_at'], 'idx_status_created');
                $table->index(['aggregate_type', 'aggregate_id'], 'idx_agg_type_id');
            });
        }

        // 2. Coin Reservations Table
        if (! Schema::hasTable('coin_reservations')) {
            Schema::create('coin_reservations', function (Blueprint $table) {
                $table->id();
                $table->string('reservation_id', 36)->unique();
                $table->unsignedInteger('company_id');
                $table->unsignedInteger('user_id');
                $table->string('forward_request_id', 36)->unique();
                $table->integer('amount');
                $table->enum('status', ['RESERVED', 'COMMITTING', 'COMMITTED', 'RELEASED', 'EXPIRED'])->default('RESERVED');
                $table->timestamp('expires_at');
                $table->timestamp('committed_at')->nullable();
                $table->timestamp('released_at')->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();

                $table->index(['company_id', 'status'], 'idx_company_status');
                $table->index(['expires_at', 'status'], 'idx_expires_status');
            });
        }

        // 3. Forward Resume Event Journal Table
        if (! Schema::hasTable('forward_resume_events')) {
            Schema::create('forward_resume_events', function (Blueprint $table) {
                $table->id();
                $table->string('event_id', 36)->unique();
                $table->string('forward_request_id', 36);
                $table->string('event_type', 64);
                $table->string('previous_state', 64)->nullable();
                $table->string('new_state', 64);
                $table->enum('actor_type', ['USER', 'SYSTEM', 'WORKER', 'RECONCILIATION']);
                $table->unsignedBigInteger('actor_id')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamp('created_at')->useCurrent();

                $table->index('forward_request_id', 'idx_fwd_request');
                $table->index('event_type', 'idx_event_type');
            });
        }

        // 4. Update forward_requests table if columns missing
        if (Schema::hasTable('forward_requests')) {
            Schema::table('forward_requests', function (Blueprint $table) {
                if (! Schema::hasColumn('forward_requests', 'worker_token')) {
                    $table->string('worker_token', 64)->nullable()->after('status');
                }
                if (! Schema::hasColumn('forward_requests', 'coin_reservation_id')) {
                    $table->string('coin_reservation_id', 36)->nullable()->after('worker_token');
                }
                if (! Schema::hasColumn('forward_requests', 'processing_started_at')) {
                    $table->timestamp('processing_started_at')->nullable()->after('coin_reservation_id');
                }
            });
        }

        // 5. Update forward_attachments table if columns missing
        if (Schema::hasTable('forward_attachments')) {
            Schema::table('forward_attachments', function (Blueprint $table) {
                if (! Schema::hasColumn('forward_attachments', 'expected_sha256')) {
                    $table->string('expected_sha256', 64)->nullable()->after('mime_type');
                }
                if (! Schema::hasColumn('forward_attachments', 'expected_size')) {
                    $table->unsignedInteger('expected_size')->nullable()->after('expected_sha256');
                }
                if (! Schema::hasColumn('forward_attachments', 'verified_at')) {
                    $table->timestamp('verified_at')->nullable()->after('status');
                }
                if (! Schema::hasColumn('forward_attachments', 'failed_at')) {
                    $table->timestamp('failed_at')->nullable()->after('verified_at');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('forward_attachments')) {
            Schema::table('forward_attachments', function (Blueprint $table) {
                if (Schema::hasColumn('forward_attachments', 'failed_at')) {
                    $table->dropColumn('failed_at');
                }
                if (Schema::hasColumn('forward_attachments', 'verified_at')) {
                    $table->dropColumn('verified_at');
                }
                if (Schema::hasColumn('forward_attachments', 'expected_size')) {
                    $table->dropColumn('expected_size');
                }
                if (Schema::hasColumn('forward_attachments', 'expected_sha256')) {
                    $table->dropColumn('expected_sha256');
                }
            });
        }

        if (Schema::hasTable('forward_requests')) {
            Schema::table('forward_requests', function (Blueprint $table) {
                if (Schema::hasColumn('forward_requests', 'processing_started_at')) {
                    $table->dropColumn('processing_started_at');
                }
                if (Schema::hasColumn('forward_requests', 'coin_reservation_id')) {
                    $table->dropColumn('coin_reservation_id');
                }
                if (Schema::hasColumn('forward_requests', 'worker_token')) {
                    $table->dropColumn('worker_token');
                }
            });
        }

        Schema::dropIfExists('forward_resume_events');
        Schema::dropIfExists('coin_reservations');
        Schema::dropIfExists('outbox_events');
    }
};
