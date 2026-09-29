<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Intent: Create dual-lease transactional_outboxes table decoupling Elasticsearch
     * and Reverb WebSocket delivery states for 10K concurrent recruiters.
     */
    public function up(): void
    {
        if (! Schema::hasTable('transactional_outboxes')) {
            Schema::create('transactional_outboxes', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('tenant_id');
                $table->unsignedInteger('job_id');
                $table->unsignedBigInteger('version');
                $table->string('event_type', 64);
                $table->json('payload');

                // Independent Elasticsearch Delivery State
                $table->enum('es_status', ['pending', 'processing', 'completed', 'failed', 'dead_letter'])->default('pending');
                $table->unsignedTinyInteger('es_attempts')->default(0);
                $table->timestamp('es_locked_until')->nullable();
                $table->string('es_claim_token', 64)->nullable();
                $table->timestamp('es_available_at')->useCurrent();
                $table->timestamp('es_processed_at')->nullable();
                $table->text('es_last_error')->nullable();

                // Independent Reverb Delivery State
                $table->enum('reverb_status', ['pending', 'processing', 'completed', 'failed', 'dead_letter'])->default('pending');
                $table->unsignedTinyInteger('reverb_attempts')->default(0);
                $table->timestamp('reverb_locked_until')->nullable();
                $table->string('reverb_claim_token', 64)->nullable();
                $table->timestamp('reverb_available_at')->useCurrent();
                $table->timestamp('reverb_processed_at')->nullable();
                $table->text('reverb_last_error')->nullable();

                $table->timestamps();

                $table->index(['es_status', 'es_available_at', 'es_locked_until', 'es_attempts'], 'idx_es_claim');
                $table->index(['reverb_status', 'reverb_available_at', 'reverb_locked_until', 'reverb_attempts'], 'idx_reverb_claim');
                $table->index(['job_id', 'version'], 'idx_job_version');
                $table->index(['tenant_id', 'created_at'], 'idx_tenant_created');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactional_outboxes');
    }
};
