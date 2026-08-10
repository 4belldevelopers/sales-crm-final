<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * V2_login Phase 3.3 — audit trail for user-suspend and company-block actions.
 *
 * target_type: 'user' (single user suspended) | 'company' (all company users blocked).
 * actor_id: NULL if triggered by a system action (licence expiry, etc.).
 * reason: optional free-text, nullable varchar(255) — product decision from IMPLEMENTATION_PLAN.md §3.3.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('block_log', function (Blueprint $table) {
            $table->id();
            $table->string('target_type', 20)->comment('user | company');
            $table->unsignedBigInteger('target_id');
            $table->unsignedBigInteger('company_id')->nullable();
            $table->string('new_status', 50);
            $table->unsignedBigInteger('actor_id')->nullable();
            $table->string('reason', 255)->nullable();
            $table->timestamp('created_at')->nullable();

            $table->index(['target_type', 'target_id'], 'block_log_target');
            $table->index('company_id', 'block_log_company');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('block_log');
    }
};
