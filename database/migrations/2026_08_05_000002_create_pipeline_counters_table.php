<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Creates pipeline_counters — the MySQL snapshot store that the
 * counters:reconcile command (Step 9) syncs from ES every 5 minutes.
 * loadcounter() reads Redis first (Cache::flexible), falls back here,
 * then ES only on cold start.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('pipeline_counters')) {
            return;
        }

        Schema::create('pipeline_counters', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('job_id');
            $table->unsignedInteger('company_id');
            $table->unsignedInteger('all_candidate')->default(0);
            $table->unsignedInteger('sourcing')->default(0);
            $table->unsignedInteger('screening')->default(0);
            $table->unsignedInteger('interview')->default(0);
            $table->unsignedInteger('negotiation')->default(0);
            $table->unsignedInteger('offered')->default(0);
            $table->unsignedInteger('joined')->default(0);
            $table->unsignedInteger('rejected')->default(0);
            $table->timestamp('computed_at')->nullable();
            $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();

            $table->unique(['job_id', 'company_id'], 'uk_pipeline_counters_job');
            $table->index('company_id', 'idx_pipeline_counters_company');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pipeline_counters');
    }
};
