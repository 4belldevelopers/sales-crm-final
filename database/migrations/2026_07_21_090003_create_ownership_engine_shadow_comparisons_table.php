<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Ownership Engine Phase 8 — docs/OWNERSHIP_ENGINE_DESIGN.md, "Shadow comparison". Records
// every legacy-vs-engine comparison run by ownership-engine:shadow-compare — an observation,
// not an assertion of correctness either way (Finding 5: the two systems answer different
// questions). Read-only from the legacy system's perspective; this table is purely additive.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ownership_engine_shadow_comparisons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('subject_row_id');
            $table->unsignedInteger('scoping_company_id');
            $table->unsignedInteger('job_id');

            $table->tinyInteger('legacy_wise_duplicate')->nullable();
            $table->tinyInteger('legacy_profile_duplicate')->nullable();

            $table->tinyInteger('engine_ownership_state')->nullable();
            $table->unsignedBigInteger('engine_owner_row_id')->nullable();
            $table->string('engine_decision_rule', 10)->nullable();

            // both_clear | legacy_flagged_engine_undetermined | legacy_clear_engine_owner |
            // legacy_flagged_engine_owner | legacy_clear_engine_duplicate |
            // legacy_flagged_engine_duplicate — a descriptive category, never a boolean
            // "mismatch" (Finding 5: the two systems answer different questions).
            $table->string('category', 40);

            $table->timestamp('compared_at');
            $table->timestamps();

            $table->index('subject_row_id');
            $table->index(['scoping_company_id', 'compared_at']);
            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ownership_engine_shadow_comparisons');
    }
};
