<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Ownership Engine Phase 1 (schema only — see docs/OWNERSHIP_ENGINE_DESIGN.md §7.3, §19).
// Zero-computation read path: every UI row partial reads these three columns only — no
// join to ownership_clusters/ownership_events, no engine call, at render time (§5.1/§19).
// The Ownership Engine (later phases) is the only writer; nothing else may set these columns
// (enforced by the CI guard added in a later phase).
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('add_job_wise_condidates', function (Blueprint $table) {
            // 0/null = undetermined (private — no cache value written yet), 1 = owner,
            // 2 = duplicate. Positioned after the existing `duplicate` column (added by
            // 2026_07_19_140013_add_duplicate_detection_columns.php) to keep the two
            // duplicate-related concerns adjacent in the schema.
            $table->tinyInteger('ownership_state')->nullable()->after('duplicate');
            $table->unsignedBigInteger('ownership_cluster_id')->nullable()->after('ownership_state');
            $table->timestamp('ownership_evaluated_at', 6)->nullable()->after('ownership_cluster_id');
        });
    }

    public function down(): void
    {
        Schema::table('add_job_wise_condidates', function (Blueprint $table) {
            $table->dropColumn(['ownership_state', 'ownership_cluster_id', 'ownership_evaluated_at']);
        });
    }
};
