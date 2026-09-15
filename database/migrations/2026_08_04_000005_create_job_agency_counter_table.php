<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tracks per-job agency activity counts so the listing page can avoid
 * live SQL aggregates over bench_applications and recommendation_relation.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('job_agency_counter')) {
            return;
        }

        Schema::create('job_agency_counter', function (Blueprint $t) {
            $t->unsignedInteger('job_id')->primary();
            $t->unsignedInteger('company_id');
            $t->unsignedInteger('bench_total')->default(0);
            $t->unsignedInteger('rec_sent')->default(0);
            $t->unsignedInteger('rec_accepted')->default(0);
            $t->unsignedInteger('rec_rejected')->default(0);
            $t->timestamp('last_event_at')->nullable();
            $t->index(['company_id', 'last_event_at'], 'idx_jac_company_event');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_agency_counter');
    }
};
