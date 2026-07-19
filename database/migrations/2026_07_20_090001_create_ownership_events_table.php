<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Ownership Engine Phase 1 (schema only — see docs/OWNERSHIP_ENGINE_DESIGN.md §7.1, §22, §28).
// Immutable, append-only log: the source of truth for ownership history, rebuild, and
// explainability. Never updated or deleted from application code; never queried directly
// outside app/Services/OwnershipEngine (enforced by the CI guard in a later phase).
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ownership_events', function (Blueprint $table) {
            $table->id();

            // Idempotency key (§23/§25 — queue retry / duplicate request safety). Time-ordered
            // (ULID/UUIDv7) binary(16) per §22/§28: half the storage of a UUID string, and
            // insert-ordered so this unique index doesn't fragment like a random UUIDv4 would.
            $table->addColumn('binary', 'event_uuid', ['length' => 16, 'fixed' => true]);

            // Which cluster this event belongs to. Not nullable: the write path always
            // resolves (or creates) the owning cluster row before it ever records an event
            // against it (§18.1/§18.2 step ordering) — there is no "orphan" event state.
            $table->unsignedBigInteger('cluster_id');

            // Scope snapshot at the moment this event was recorded — never re-derived from
            // the company's *current* app_duplicate setting, so a later setting change never
            // rewrites what this event meant when it happened (§4/§6 M-JobStatus discussion).
            $table->unsignedInteger('scoping_company_id');
            $table->tinyInteger('scope_mode'); // 1 = Job, 2 = Company Overall
            $table->unsignedInteger('scope_key'); // job_id (mode=1) or scoping_company_id again (mode=2)

            $table->string('normalized_email', 190)->nullable();
            $table->string('normalized_phone', 32)->nullable();

            // The add_job_wise_condidates row this specific event concerns.
            $table->unsignedBigInteger('subject_row_id');

            // RESERVATION_ATTEMPT | AGENCY_SOURCED | OWNER_ESTABLISHED | DUPLICATE_MARKED |
            // REBUILD_REPLAY — see app/Services/OwnershipEngine (Phase 2) for the closed set.
            $table->string('event_type', 40);

            // ER1..ER8 — which Engine Rule (docs/OWNERSHIP_ENGINE_DESIGN.md §2) fired.
            $table->string('decision_rule', 10)->nullable();

            // Informational only, never authoritative: which Master Tracker row (S1..S30)
            // this event is believed to match, for debugging against the doc (§3/§11).
            $table->string('scenario_ref', 10)->nullable();

            $table->unsignedBigInteger('previous_owner_row_id')->nullable();
            $table->unsignedBigInteger('new_owner_row_id')->nullable();

            // candidate_creation | stage_transition | job_status_change | settings_change |
            // manual_rebuild | admin_action
            $table->string('trigger_source', 40);

            $table->unsignedBigInteger('actor_user_id')->nullable(); // null = system-generated

            // Human-readable, generated from stored fields at write time (§11/§26) — not
            // free text authored ad hoc, so it stays reproducible under rebuild.
            $table->text('reason');

            // Microsecond precision (§9.4/§22): the authoritative decision instant, distinct
            // from created_at/updated_at, which just track row bookkeeping.
            $table->timestamp('event_time', 6);

            $table->timestamps();

            $table->unique('event_uuid');
            // Ordered replay range-scan for rebuild (§18.5/§21) — event_time is monotonically
            // increasing, so inserts append at the tail of this index (no mid-tree splits).
            $table->index(['cluster_id', 'event_time', 'id'], 'oe_cluster_replay_idx');
            // Per-row explainability lookup (§18.4) — a distinct leading column from the
            // index above, not redundant with it.
            $table->index('subject_row_id', 'oe_subject_row_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ownership_events');
    }
};
