<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// New table backing the Job Status drawer's "Status Timeline" panel — an
// append-only audit trail of job.status transitions (none existed before).
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_status_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('job_id');
            $table->unsignedInteger('company_id')->nullable();
            $table->string('from_status', 20)->nullable();
            $table->string('to_status', 20);
            $table->unsignedInteger('changed_by')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('job_id');
            $table->index('company_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_status_logs');
    }
};
