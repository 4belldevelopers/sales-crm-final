<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Harden Super Admin Jobs V3 Architecture
 *
 * Adds:
 * 1. jobs.aggregate_version: Authoritative monotonic version for Optimistic Concurrency Control (OCC).
 * 2. sys_idempotency_keys.owner_token & lease_version: Fencing tokens for distributed lease ownership.
 * 3. sys_outbox_deliveries: Dedicated delivery ledger preventing lost updates across concurrent destinations.
 *
 * Author: Antigravity AI
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md.
 */
return new class extends Migration
{
    public function up(): void
    {
        // 1. Authoritative aggregate_version on jobs table
        if (Schema::hasTable('jobs')) {
            Schema::table('jobs', function (Blueprint $table) {
                if (! Schema::hasColumn('jobs', 'aggregate_version')) {
                    $table->unsignedInteger('aggregate_version')->default(1)->after('status');
                }
            });

            $existingJobsIndexes = collect(DB::select("SHOW INDEX FROM `jobs`"))->pluck('Key_name')->unique()->toArray();
            if (! in_array('idx_jobs_agg_ver', $existingJobsIndexes)) {
                Schema::table('jobs', function (Blueprint $table) {
                    $table->index(['id', 'aggregate_version'], 'idx_jobs_agg_ver');
                });
            }
        }

        // 2. Fencing tokens on sys_idempotency_keys
        if (Schema::hasTable('sys_idempotency_keys')) {
            Schema::table('sys_idempotency_keys', function (Blueprint $table) {
                if (! Schema::hasColumn('sys_idempotency_keys', 'owner_token')) {
                    $table->string('owner_token', 64)->nullable()->after('request_hash')
                        ->comment('Fencing token: prevents expired/stale workers from overwriting new lease owners');
                }
                if (! Schema::hasColumn('sys_idempotency_keys', 'lease_version')) {
                    $table->unsignedInteger('lease_version')->default(1)->after('owner_token');
                }
            });

            $existingIdempIndexes = collect(DB::select("SHOW INDEX FROM `sys_idempotency_keys`"))->pluck('Key_name')->unique()->toArray();
            if (! in_array('idx_idempotency_ownership', $existingIdempIndexes)) {
                Schema::table('sys_idempotency_keys', function (Blueprint $table) {
                    $table->index(['tenant_id', 'idempotency_key', 'owner_token'], 'idx_idempotency_ownership');
                });
            }
        }

        // 3. Dedicated Outbox Delivery Ledger
        if (! Schema::hasTable('sys_outbox_deliveries')) {
            Schema::create('sys_outbox_deliveries', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('outbox_id');
                $table->string('destination', 32); // 'redis', 'elasticsearch', 'websocket'
                $table->enum('status', ['PENDING', 'PROCESSING', 'COMPLETED', 'FAILED', 'DEAD_LETTER', 'DEGRADED'])->default('PENDING');
                $table->unsignedTinyInteger('attempts')->default(0);
                $table->string('owner_token', 64)->nullable();
                $table->timestamp('lease_expires_at')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->text('last_error')->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

                $table->unique(['outbox_id', 'destination'], 'idx_delivery_outbox_dest');
                $table->index(['destination', 'status', 'lease_expires_at'], 'idx_delivery_claim');
                $table->index(['outbox_id', 'status'], 'idx_delivery_outbox_status');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('sys_outbox_deliveries')) {
            Schema::dropIfExists('sys_outbox_deliveries');
        }

        if (Schema::hasTable('sys_idempotency_keys')) {
            $existingIdempIndexes = collect(DB::select("SHOW INDEX FROM `sys_idempotency_keys`"))->pluck('Key_name')->unique()->toArray();
            Schema::table('sys_idempotency_keys', function (Blueprint $table) use ($existingIdempIndexes) {
                if (in_array('idx_idempotency_ownership', $existingIdempIndexes)) {
                    $table->dropIndex('idx_idempotency_ownership');
                }
                if (Schema::hasColumn('sys_idempotency_keys', 'owner_token')) {
                    $table->dropColumn('owner_token');
                }
                if (Schema::hasColumn('sys_idempotency_keys', 'lease_version')) {
                    $table->dropColumn('lease_version');
                }
            });
        }

        if (Schema::hasTable('jobs')) {
            $existingJobsIndexes = collect(DB::select("SHOW INDEX FROM `jobs`"))->pluck('Key_name')->unique()->toArray();
            Schema::table('jobs', function (Blueprint $table) use ($existingJobsIndexes) {
                if (in_array('idx_jobs_agg_ver', $existingJobsIndexes)) {
                    $table->dropIndex('idx_jobs_agg_ver');
                }
                if (Schema::hasColumn('jobs', 'aggregate_version')) {
                    $table->dropColumn('aggregate_version');
                }
            });
        }
    }
};
