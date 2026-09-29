<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Intent: Create sys_idempotency_keys, sys_outbox_events tables and add aggregate_version
     * to job_applications to enforce transactionally coordinated idempotency and OCC CQRS
     * per docs/drawer_candidate.md.
     */
    public function up(): void
    {
        // 1. Transactionally Coordinated Idempotency Keys table
        if (! Schema::hasTable('sys_idempotency_keys')) {
            Schema::create('sys_idempotency_keys', function (Blueprint $table) {
                $table->id();
                $table->integer('tenant_id');
                $table->string('idempotency_key', 64);
                $table->string('request_hash', 64);
                $table->enum('status', ['PROCESSING', 'COMPLETED', 'FAILED_FINAL', 'FAILED_RETRYABLE'])->default('PROCESSING');
                $table->integer('response_status')->nullable();
                $table->longText('response_body')->nullable();
                $table->text('last_error')->nullable();
                $table->timestamp('expires_at');
                $table->timestamp('completed_at')->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

                $table->unique(['tenant_id', 'idempotency_key'], 'idx_idempotency_tenant_key');
                $table->index('expires_at', 'idx_idempotency_expires');
            });
        }

        // 2. Outbox Events table for Candidate Drawer CQRS and Watermark-Guaranteed ES Rebuilds
        if (! Schema::hasTable('sys_outbox_events')) {
            Schema::create('sys_outbox_events', function (Blueprint $table) {
                $table->id();
                $table->integer('tenant_id');
                $table->string('idempotency_key', 64)->nullable();
                $table->string('aggregate_type', 64)->default('Candidate');
                $table->unsignedBigInteger('aggregate_id');
                $table->unsignedInteger('aggregate_version');
                $table->string('event_type', 64);
                $table->json('payload');
                $table->enum('status', ['PENDING', 'PROCESSING', 'COMPLETED', 'FAILED', 'DEAD_LETTER'])->default('PENDING');
                $table->unsignedTinyInteger('attempts')->default(0);
                $table->timestamp('available_at')->useCurrent();
                $table->timestamp('processed_at')->nullable();
                $table->text('last_error')->nullable();
                $table->timestamp('locked_at')->nullable();
                $table->timestamp('created_at')->useCurrent();

                $table->unique(['tenant_id', 'idempotency_key'], 'idx_outbox_tenant_idempotency');
                $table->index(['status', 'available_at'], 'idx_outbox_status_available');
                $table->index(['aggregate_type', 'aggregate_id', 'aggregate_version'], 'idx_outbox_aggregate');
            });
        }

        // 3. Extend job_applications with aggregate_version for Optimistic Concurrency Control (OCC)
        if (Schema::hasTable('job_applications')) {
            Schema::table('job_applications', function (Blueprint $table) {
                if (! Schema::hasColumn('job_applications', 'aggregate_version')) {
                    if (Schema::hasColumn('job_applications', 'resume_version')) {
                        $table->unsignedInteger('aggregate_version')->default(1)->after('resume_version');
                    } else {
                        $table->unsignedInteger('aggregate_version')->default(1);
                    }
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('job_applications')) {
            Schema::table('job_applications', function (Blueprint $table) {
                if (Schema::hasColumn('job_applications', 'aggregate_version')) {
                    $table->dropColumn('aggregate_version');
                }
            });
        }

        Schema::dropIfExists('sys_outbox_events');
        Schema::dropIfExists('sys_idempotency_keys');
    }
};
