<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('application_counters', function (Blueprint $t) {
            $t->unsignedInteger('job_id')->primary();
            $t->unsignedInteger('total_apps')->default(0);
            $t->unsignedInteger('interview_apps')->default(0);
            $t->unsignedInteger('offer_apps')->default(0);
            $t->unsignedInteger('rejected_apps')->default(0);
            $t->timestamp('last_event_at')->nullable();
            $t->index('last_event_at', 'idx_ac_last_event');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_counters');
    }
};
