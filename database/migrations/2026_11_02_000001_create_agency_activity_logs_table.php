<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Create agency_activity_logs table
 *
 * Intent: Transactional write store for the Agency 360° Activity & Audit trail.
 * Enforces immutable business event logging with actor attribution, source tracking,
 * and previous/new state snapshots.
 *
 * Scoped by agency_company_id and service_company_id.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('agency_activity_logs')) {
            Schema::create('agency_activity_logs', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('event_uuid', 36)->unique();
                $table->unsignedInteger('agency_company_id');
                $table->unsignedInteger('service_company_id')->default(0)->comment('Employer / Sourcing Company ID');
                $table->enum('category', [
                    'ACCOUNT',
                    'TEAM',
                    'SERVICES',
                    'JOBS',
                    'CANDIDATES',
                    'INTERVIEWS',
                    'OFFERS',
                    'JOINING',
                    'COMMUNICATION',
                    'CLIENT',
                    'MARKETPLACE',
                    'PERFORMANCE',
                    'COMPLIANCE',
                    'RELATIONSHIP',
                    'SYSTEM',
                ]);
                $table->string('event_type', 64);
                $table->string('event_title', 255);
                $table->text('event_description')->nullable();
                $table->unsignedInteger('actor_id')->default(0);
                $table->string('actor_name', 191)->default('System');
                $table->string('actor_role', 64)->default('system');
                $table->enum('source', [
                    'AGENCY_PORTAL',
                    'CLIENT_PORTAL',
                    'INTERNAL_ATS',
                    'MARKETPLACE',
                    'ADMIN_PANEL',
                    'SYSTEM_AUTOMATION',
                    'EMAIL',
                    'API',
                ])->default('INTERNAL_ATS');
                $table->string('entity_type', 64)->default('Agency');
                $table->unsignedBigInteger('entity_id')->default(0);
                $table->unsignedInteger('related_job_id')->nullable();
                $table->string('related_job_title', 255)->nullable();
                $table->unsignedBigInteger('related_candidate_id')->nullable();
                $table->string('related_candidate_name', 191)->nullable();
                $table->unsignedInteger('related_client_id')->nullable();
                $table->string('related_client_name', 191)->nullable();
                $table->json('previous_value')->nullable();
                $table->json('new_value')->nullable();
                $table->json('metadata')->nullable();
                $table->enum('visibility_scope', [
                    'PUBLIC',
                    'INTERNAL_ONLY',
                    'AGENCY_ONLY',
                    'SUPERADMIN_ONLY',
                ])->default('PUBLIC');
                $table->timestamps();

                $table->index(['agency_company_id', 'created_at'], 'idx_aal_agency_time');
                $table->index(['agency_company_id', 'category', 'created_at'], 'idx_aal_agency_cat_time');
                $table->index(['service_company_id', 'agency_company_id'], 'idx_aal_svc_agency');
                $table->index(['related_job_id'], 'idx_aal_job');
                $table->index(['related_candidate_id'], 'idx_aal_cand');
                $table->index(['event_type'], 'idx_aal_event_type');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agency_activity_logs');
    }
};
