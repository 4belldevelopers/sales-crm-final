<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Ownership Engine Phase 1 (schema only — see docs/OWNERSHIP_ENGINE_DESIGN.md §18.5/§24,
// and Revision 1 §3). The Ownership Engine's live write paths never query `timeline` (§24 —
// "first reached Sourcing" is answered by ownership_clusters, not by scanning history). This
// index exists solely for the one-time historical backfill (Phase 4): synthesizing past
// ownership_events from `timeline`'s existing MIN(create_at) per (application_id, job_id,
// master_process_name) — confirmed these are `timeline`'s real column names (not the similarly
// named `updatetimeline` table, a different table with an unrelated purpose). Without this
// index that backfill query is a full table scan on a table with no natural per-tenant size
// cap; with it, it's an indexed range scan.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('timeline', function (Blueprint $table) {
            $table->index(
                ['application_id', 'job_id', 'master_process_name', 'create_at'],
                'timeline_ownership_backfill_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::table('timeline', function (Blueprint $table) {
            $table->dropIndex('timeline_ownership_backfill_idx');
        });
    }
};
