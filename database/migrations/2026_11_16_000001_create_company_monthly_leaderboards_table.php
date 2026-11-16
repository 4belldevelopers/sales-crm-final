<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Create Company Monthly Leaderboards Table
 *
 * Intent: Store authoritative, pre-calculated monthly leaderboard snapshots per company.
 * Enables 0% live recalculation strain on page loads, ensuring high-concurrency ATS performance
 * and strict data consistency between Team Member and Company Profile views.
 *
 * Tenancy: Scoped by company_id and period_month (e.g., '2026-10').
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('company_monthly_leaderboards')) {
            Schema::create('company_monthly_leaderboards', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('company_id')->comment('Company / Tenant ID');
                $table->string('period_month', 7)->comment('Reporting period in YYYY-MM format, e.g. 2026-10');
                $table->longText('leaderboard_data')->comment('JSON serialized array of ranked company members');
                $table->integer('total_members')->default(0)->comment('Total members evaluated in this monthly snapshot');
                $table->enum('status', ['draft', 'published'])->default('published')->comment('Publication status');
                $table->timestamp('published_at')->nullable()->comment('Timestamp when leaderboard snapshot was published');
                $table->timestamps();

                $table->unique(['company_id', 'period_month'], 'uk_company_period');
                $table->index(['company_id', 'status'], 'idx_company_status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_monthly_leaderboards');
    }
};
