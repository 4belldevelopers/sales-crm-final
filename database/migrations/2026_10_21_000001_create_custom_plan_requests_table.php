<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration creating custom_plan_requests and custom_plan_request_logs tables.
 *
 * Intent: Persist customer enquiries for custom subscription plans and top-up packages,
 * maintain human-readable unique references (CPR-YYYYMM-XXXX), provide status management
 * (New, In Progress, Contacted, Resolved, Closed), and record status transition audits.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('custom_plan_requests')) {
            Schema::create('custom_plan_requests', function (Blueprint $table) {
                $table->id();
                $table->string('request_reference', 64)->unique();
                $table->unsignedInteger('company_id')->nullable()->index('idx_cpr_company_id');
                $table->unsignedInteger('user_id')->nullable()->index('idx_cpr_user_id');
                $table->string('customer_name', 191);
                $table->string('customer_email', 191)->index('idx_cpr_customer_email');
                $table->string('customer_mobile', 50)->nullable();
                $table->string('company_name', 191)->nullable();
                $table->text('location')->nullable();
                $table->string('request_type', 100)->default('Custom Subscription Plan');
                $table->string('package_type', 50)->nullable(); // operational / sales
                $table->unsignedInteger('package_id')->nullable();
                $table->string('package_title', 191)->nullable();
                $table->enum('status', ['New', 'In Progress', 'Contacted', 'Resolved', 'Closed'])->default('New')->index('idx_cpr_status');
                $table->text('message')->nullable();
                $table->text('admin_notes')->nullable();
                $table->unsignedInteger('assigned_to')->nullable()->index('idx_cpr_assigned_to');
                $table->unsignedInteger('updated_by')->nullable();
                $table->timestamp('last_status_updated_at')->nullable();
                $table->timestamps();

                $table->index('created_at', 'idx_cpr_created_at');
            });
        }

        if (! Schema::hasTable('custom_plan_request_logs')) {
            Schema::create('custom_plan_request_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('custom_plan_request_id')->index('idx_cprl_request_id');
                $table->string('previous_status', 32)->nullable();
                $table->string('new_status', 32);
                $table->text('notes')->nullable();
                $table->unsignedInteger('updated_by')->nullable();
                $table->timestamp('created_at')->useCurrent();

                $table->foreign('custom_plan_request_id')
                    ->references('id')
                    ->on('custom_plan_requests')
                    ->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_plan_request_logs');
        Schema::dropIfExists('custom_plan_requests');
    }
};
