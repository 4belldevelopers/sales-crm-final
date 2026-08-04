<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_counters', function (Blueprint $t) {
            $t->unsignedInteger('company_id')->primary();
            $t->unsignedInteger('total_jobs')->default(0);
            $t->unsignedInteger('hiring_jobs')->default(0);
            $t->unsignedInteger('on_hold_jobs')->default(0);
            $t->unsignedInteger('closed_jobs')->default(0);
            $t->unsignedInteger('removed_jobs')->default(0);
            $t->unsignedInteger('last_job_id')->default(0);
            $t->timestamp('last_event_at')->nullable();
            $t->index('last_event_at', 'idx_jc_last_event');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_counters');
    }
};
