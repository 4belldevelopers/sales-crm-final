<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Hardens sys_idempotency_keys with execution lease deadline (processing_until)
     * and candidate_outbox_events with DLQ replay audit tracking columns.
     */
    public function up(): void
    {
        if (Schema::hasTable('sys_idempotency_keys')) {
            Schema::table('sys_idempotency_keys', function (Blueprint $table) {
                if (! Schema::hasColumn('sys_idempotency_keys', 'processing_until')) {
                    $table->timestamp('processing_until')->nullable()->after('last_error')
                        ->comment('Execution lease deadline (NOW() + 60s); distinguishes active in-flight from abandoned crash');
                }
            });

            $existingIdempIndexes = collect(DB::select("SHOW INDEX FROM `sys_idempotency_keys`"))->pluck('Key_name')->unique()->toArray();
            if (! in_array('idx_idempotency_lease', $existingIdempIndexes)) {
                Schema::table('sys_idempotency_keys', function (Blueprint $table) {
                    $table->index(['status', 'processing_until'], 'idx_idempotency_lease');
                });
            }
        }

        if (Schema::hasTable('candidate_outbox_events')) {
            Schema::table('candidate_outbox_events', function (Blueprint $table) {
                if (! Schema::hasColumn('candidate_outbox_events', 'replayed_by')) {
                    $table->string('replayed_by', 64)->nullable()->after('last_error');
                }
                if (! Schema::hasColumn('candidate_outbox_events', 'replayed_at')) {
                    $table->timestamp('replayed_at')->nullable()->after('replayed_by');
                }
                if (! Schema::hasColumn('candidate_outbox_events', 'replay_reason')) {
                    $table->string('replay_reason', 255)->nullable()->after('replayed_at');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('sys_idempotency_keys')) {
            $existingIdempIndexes = collect(DB::select("SHOW INDEX FROM `sys_idempotency_keys`"))->pluck('Key_name')->unique()->toArray();
            Schema::table('sys_idempotency_keys', function (Blueprint $table) use ($existingIdempIndexes) {
                if (in_array('idx_idempotency_lease', $existingIdempIndexes)) {
                    $table->dropIndex('idx_idempotency_lease');
                }
                if (Schema::hasColumn('sys_idempotency_keys', 'processing_until')) {
                    $table->dropColumn('processing_until');
                }
            });
        }

        if (Schema::hasTable('candidate_outbox_events')) {
            Schema::table('candidate_outbox_events', function (Blueprint $table) {
                if (Schema::hasColumn('candidate_outbox_events', 'replay_reason')) {
                    $table->dropColumn('replay_reason');
                }
                if (Schema::hasColumn('candidate_outbox_events', 'replayed_at')) {
                    $table->dropColumn('replayed_at');
                }
                if (Schema::hasColumn('candidate_outbox_events', 'replayed_by')) {
                    $table->dropColumn('replayed_by');
                }
            });
        }
    }
};
