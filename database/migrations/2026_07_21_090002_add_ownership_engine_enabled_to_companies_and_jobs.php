<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Ownership Engine Phase 8 — docs/OWNERSHIP_ENGINE_DESIGN.md, Finding 6.
// Nullable boolean overrides: null = inherit from ownership_engine_settings.globally_enabled
// (or the company override, for the job column); true/false = explicit override at that
// level. Job-level takes precedence over company-level, which takes precedence over global —
// the kill switch overrides all three unconditionally. Does not touch any existing column.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->boolean('ownership_engine_enabled')->nullable()->default(null)->after('enable_cross_agency_duplicate');
        });

        Schema::table('jobs', function (Blueprint $table) {
            $table->boolean('ownership_engine_enabled')->nullable()->default(null)->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('ownership_engine_enabled');
        });

        Schema::table('jobs', function (Blueprint $table) {
            $table->dropColumn('ownership_engine_enabled');
        });
    }
};
