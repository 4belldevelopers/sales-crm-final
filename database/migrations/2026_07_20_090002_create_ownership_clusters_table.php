<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Ownership Engine Phase 1 (schema only — see docs/OWNERSHIP_ENGINE_DESIGN.md §7.2, §22, §28).
// One row per identity+scope cluster — the lockable, point-queryable "current owner" pointer.
// This is the only table any write path locks (SELECT ... FOR UPDATE), and it locks at most
// one row per transaction (§8/§23) — the entire concurrency-safety argument in the design
// depends on this table staying narrow and its unique indexes staying the sole lookup path.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ownership_clusters', function (Blueprint $table) {
            $table->id();

            $table->unsignedInteger('scoping_company_id');
            $table->tinyInteger('scope_mode'); // 1 = Job, 2 = Company Overall
            $table->unsignedInteger('scope_key'); // job_id (mode=1) or scoping_company_id again (mode=2)

            $table->string('normalized_email', 190)->nullable();
            $table->string('normalized_phone', 32)->nullable();

            // add_job_wise_condidates.id of the permanent owner. Null = undetermined
            // (no company reservation and no agency has reached Sourcing yet — S1/S2/S14/S27).
            $table->unsignedBigInteger('owner_row_id')->nullable();
            $table->enum('owner_type', ['company', 'agency'])->nullable();

            // ER2 (Company Recruiter Reservation) or ER3 (Agency Ownership) — whichever
            // established this owner. Never rewritten once set (ER4 — ownership never flips).
            $table->string('decision_rule', 10)->nullable();

            // Written once, on the event that first sets owner_row_id, and never updated
            // again — this column IS the "ownership never flips" guarantee made structural.
            $table->timestamp('decided_at', 6)->nullable();

            // Traceability pointer to the ownership_events row that most recently touched
            // this cluster (§4 "which rule created ownership?"). No FK constraint, matching
            // this codebase's existing convention of app-layer-managed relations.
            $table->unsignedBigInteger('last_event_id')->nullable();

            $table->timestamps();

            // The lock target + point lookup for every write event (§18.1/§18.2 step 3,
            // §23). Nullable columns are intentional: MySQL treats multiple NULLs as
            // distinct under a unique index, so a phone-only identity doesn't collide with
            // (or block) an unrelated email-only identity sharing this scope.
            $table->unique(['scoping_company_id', 'scope_mode', 'scope_key', 'normalized_email'], 'oc_email_identity_unique');
            $table->unique(['scoping_company_id', 'scope_mode', 'scope_key', 'normalized_phone'], 'oc_phone_identity_unique');

            // Deliberately NOT indexing owner_row_id here — no query in the approved design
            // uses that lookup direction yet (§22/§28: "removed — not defensive"). Add it
            // only when a concrete consumer (e.g. an admin "what does this row own" tool)
            // actually needs it.
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ownership_clusters');
    }
};
