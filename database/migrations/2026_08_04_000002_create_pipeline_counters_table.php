<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pipeline_counters', function (Blueprint $t) {
            $t->unsignedInteger('job_id')->primary();
            $t->unsignedInteger('company_id');
            $t->unsignedInteger('sourcing')->default(0);
            $t->unsignedInteger('screening')->default(0);
            $t->unsignedInteger('evaluation')->default(0);
            $t->unsignedInteger('negotiation')->default(0);
            $t->unsignedInteger('offered')->default(0);
            $t->unsignedInteger('joined')->default(0);
            $t->unsignedInteger('rejected')->default(0);
            $t->tinyInteger('fresh_data')->default(1);
            $t->timestamp('last_event_at')->nullable();
            $t->index(['job_id', 'company_id'], 'idx_pc_job_company');
            $t->index(['fresh_data', 'last_event_at'], 'idx_pc_fresh');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pipeline_counters');
    }
};
