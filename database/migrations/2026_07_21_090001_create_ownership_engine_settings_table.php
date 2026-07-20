<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Ownership Engine Phase 8 (Activation Readiness) — docs/OWNERSHIP_ENGINE_DESIGN.md, Finding 6.
// Single-row (id = 1) global configuration for the feature-flag hierarchy. globally_enabled
// defaults to false (Finding 6): activation is a deliberate future action, not a side effect
// of this migration running. kill_switch_active overrides every other flag unconditionally.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ownership_engine_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('globally_enabled')->default(false);
            $table->boolean('kill_switch_active')->default(false);
            $table->text('kill_switch_reason')->nullable();
            $table->timestamp('kill_switch_activated_at')->nullable();
            $table->timestamps();
        });

        DB::table('ownership_engine_settings')->insert([
            'id' => 1,
            'globally_enabled' => false,
            'kill_switch_active' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('ownership_engine_settings');
    }
};
