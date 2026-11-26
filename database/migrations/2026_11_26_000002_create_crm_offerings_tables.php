<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create dedicated JeroCRM Product / Services tables (isolated from recruitment `jobs`).
     */
    public function up(): void
    {
        if (! Schema::hasTable('crm_offerings')) {
            Schema::create('crm_offerings', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('company_id')->index();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->string('operation_id', 64)->nullable();

                // Step 1: Basic Info & Classification
                $table->enum('offering_class', ['product', 'service'])->default('product');
                $table->string('offering_type', 80)->default('Physical Product');
                $table->unsignedBigInteger('owner_client_id')->nullable();
                $table->unsignedBigInteger('address_id')->nullable();
                $table->string('name', 255);
                $table->string('crm_category', 150)->nullable();
                $table->unsignedBigInteger('industry_id')->nullable();

                // Step 1: Targeting, Capacity & Status
                $table->json('target_segments')->nullable();
                $table->unsignedInteger('available_capacity')->nullable();
                $table->boolean('is_unlimited_capacity')->default(false);
                $table->enum('status', ['draft', 'active', 'inactive', 'temporarily_unavailable', 'discontinued'])->default('active');

                // Step 1: Commercial Pricing
                $table->enum('pricing_visibility', ['public_catalog', 'client_portal', 'internal_only', 'confidential'])->default('public_catalog');
                $table->decimal('min_price', 15, 2)->default(0.00);
                $table->decimal('max_price', 15, 2)->nullable();
                $table->string('currency_code', 10)->default('INR');
                $table->string('pricing_model', 60)->default('Fixed Price');
                $table->string('pricing_frequency', 60)->default('One-Time');
                $table->boolean('is_negotiable')->default(false);

                // Step 1: Delivery, Availability & Validity
                $table->string('delivery_model', 60)->default('Remote');
                $table->string('service_availability', 60)->default('Business Hours');
                $table->string('coverage_scope', 80)->nullable();
                $table->string('validity_days', 30)->default('ongoing');
                $table->date('valid_until')->nullable();

                // Step 1: Conditional Product vs Service Specs
                $table->string('sku', 100)->nullable();
                $table->string('unit', 50)->nullable();
                $table->string('warranty', 120)->nullable();
                $table->string('brochure_path', 500)->nullable();
                $table->string('service_duration', 120)->nullable();
                $table->string('implementation_timeline', 120)->nullable();
                $table->text('sla_details')->nullable();

                // Step 1: Eligibility Criteria
                $table->json('eligibility_criteria')->nullable();

                // Step 2: Description, Scope & Features
                $table->longText('description')->nullable();
                $table->text('features_scope')->nullable();
                $table->text('specifications_eligibility')->nullable();
                $table->text('customer_benefits')->nullable();
                $table->text('additional_info')->nullable();
                $table->text('scope_of_work')->nullable();
                $table->text('deliverables')->nullable();

                // Step 4: Catalog Publishing Channels
                $table->boolean('visibility_internal_crm')->default(true);
                $table->boolean('visibility_client_portal')->default(true);
                $table->boolean('visibility_public_catalog')->default(false);

                $table->timestamps();
                $table->softDeletes();

                $table->unique(['company_id', 'operation_id'], 'uq_crm_offerings_comp_op');
                $table->unique(['company_id', 'sku'], 'uq_crm_offerings_comp_sku');
                $table->index(['company_id', 'offering_class', 'status'], 'idx_crm_offerings_comp_class_status');
            });
        }

        if (! Schema::hasTable('crm_offering_locations')) {
            Schema::create('crm_offering_locations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('company_id')->index();
                $table->unsignedBigInteger('offering_id');
                $table->string('location_name', 150);
                $table->timestamps();

                $table->foreign('offering_id', 'fk_crm_loc_offering')
                    ->references('id')->on('crm_offerings')->onDelete('cascade');
            });
        }

        if (! Schema::hasTable('crm_offering_tags')) {
            Schema::create('crm_offering_tags', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('company_id')->index();
                $table->unsignedBigInteger('offering_id');
                $table->string('tag_name', 120);
                $table->timestamps();

                $table->foreign('offering_id', 'fk_crm_tag_offering')
                    ->references('id')->on('crm_offerings')->onDelete('cascade');
            });
        }

        if (! Schema::hasTable('crm_offering_questions')) {
            Schema::create('crm_offering_questions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('company_id')->index();
                $table->unsignedBigInteger('offering_id');
                $table->string('question', 500);
                $table->string('answer_type', 40)->default('short');
                $table->boolean('is_required')->default(false);
                $table->json('options')->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();

                $table->foreign('offering_id', 'fk_crm_q_offering')
                    ->references('id')->on('crm_offerings')->onDelete('cascade');
            });
        }

        if (! Schema::hasTable('crm_offering_team')) {
            Schema::create('crm_offering_team', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('company_id')->index();
                $table->unsignedBigInteger('offering_id');
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('member_name', 150);
                $table->enum('role_type', ['owner', 'collaborator'])->default('collaborator');
                $table->boolean('is_lead')->default(false);
                $table->timestamps();

                $table->foreign('offering_id', 'fk_crm_team_offering')
                    ->references('id')->on('crm_offerings')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_offering_team');
        Schema::dropIfExists('crm_offering_questions');
        Schema::dropIfExists('crm_offering_tags');
        Schema::dropIfExists('crm_offering_locations');
        Schema::dropIfExists('crm_offerings');
    }
};
