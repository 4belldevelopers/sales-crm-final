<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('agency_activity_logs')) {
            Schema::table('agency_activity_logs', function (Blueprint $table) {
                if (! Schema::hasColumn('agency_activity_logs', 'idempotency_key')) {
                    $table->string('idempotency_key', 64)->nullable()->unique('idx_aal_idempotency')->after('event_uuid');
                }
                if (! Schema::hasColumn('agency_activity_logs', 'aggregate_version')) {
                    $table->unsignedInteger('aggregate_version')->default(1)->after('entity_id');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('agency_activity_logs')) {
            Schema::table('agency_activity_logs', function (Blueprint $table) {
                if (Schema::hasColumn('agency_activity_logs', 'idempotency_key')) {
                    $table->dropUnique('idx_aal_idempotency');
                    $table->dropColumn('idempotency_key');
                }
                if (Schema::hasColumn('agency_activity_logs', 'aggregate_version')) {
                    $table->dropColumn('aggregate_version');
                }
            });
        }
    }
};
