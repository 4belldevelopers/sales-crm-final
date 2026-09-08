<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: CreateCompanyCareerSettingsTable
 *
 * Intent: Creates the permanent schema table `company_career_settings` for storing
 * tenant career page branding, hero sections, stats, layout styles, and template design attributes.
 * Replaces runtime DDL execution inside ServiceCompanyAccountSettingsController.
 *
 * Tenancy: All rows are strictly scoped by company_id (UNIQUE key).
 *
 * Author: Antigravity AI
 * Date: August 17, 2026
 */
return new class extends Migration
{
    /**
     * Run the migrations.
    */
    public function up(): void
    {
        if (! Schema::hasTable('company_career_settings')) {
            Schema::create('company_career_settings', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('company_id')->unique();
                $table->string('primary_color', 20)->default('#2563EB');
                $table->string('secondary_color', 20)->default('#10B981');
                $table->string('font_family', 50)->default('Poppins');
                $table->string('default_language', 10)->default('en');
                $table->string('currency', 10)->default('INR');
                $table->string('page_layout', 20)->default('modern');
                $table->string('jobs_display', 20)->default('card');
                $table->boolean('career_page_status')->default(true);
                $table->string('td_template', 30)->default('modern');
                $table->string('td_color_primary', 20)->default('#2563EB');
                $table->string('td_color_secondary', 20)->default('#1F2937');
                $table->string('td_color_accent', 20)->default('#10B981');
                $table->string('td_color_bg', 20)->default('#FFFFFF');
                $table->string('td_theme_mode', 10)->default('light');
                $table->string('td_layout', 20)->default('wide');
                $table->string('td_logo_path', 500)->nullable();
                $table->string('td_favicon_path', 500)->nullable();
                $table->string('td_hero_img1_path', 500)->nullable();
                $table->string('td_hero_img2_path', 500)->nullable();
                $table->boolean('td_opt_life')->default(true);
                $table->boolean('td_opt_testi')->default(true);
                $table->boolean('td_opt_animation')->default(true);
                $table->text('td_custom_css')->nullable();
                $table->integer('stat_departments')->default(15);
                $table->integer('stat_countries')->default(5);
                $table->string('stat_employees', 20)->default('220+');
                $table->string('stat_retention', 20)->default('95%');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_career_settings');
    }
};
