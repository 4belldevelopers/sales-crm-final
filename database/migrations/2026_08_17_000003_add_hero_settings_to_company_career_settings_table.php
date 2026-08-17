<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: AddHeroSettingsToCompanyCareerSettingsTable
 *
 * Intent: Adds hero section customization columns (layout, badge, heading, description, CTAs, background style)
 * to `company_career_settings` table.
 *
 * Tenancy: Scoped strictly per tenant company_id.
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
        if (Schema::hasTable('company_career_settings')) {
            Schema::table('company_career_settings', function (Blueprint $table) {
                if (! Schema::hasColumn('company_career_settings', 'hero_layout')) {
                    $table->string('hero_layout', 30)->nullable()->default('left-right');
                }
                if (! Schema::hasColumn('company_career_settings', 'hero_badge')) {
                    $table->string('hero_badge', 255)->nullable();
                }
                if (! Schema::hasColumn('company_career_settings', 'hero_heading')) {
                    $table->string('hero_heading', 255)->nullable();
                }
                if (! Schema::hasColumn('company_career_settings', 'hero_description')) {
                    $table->text('hero_description')->nullable();
                }
                if (! Schema::hasColumn('company_career_settings', 'hero_cta1_text')) {
                    $table->string('hero_cta1_text', 100)->nullable();
                }
                if (! Schema::hasColumn('company_career_settings', 'hero_cta1_link')) {
                    $table->string('hero_cta1_link', 255)->nullable();
                }
                if (! Schema::hasColumn('company_career_settings', 'hero_cta1_newtab')) {
                    $table->boolean('hero_cta1_newtab')->default(false);
                }
                if (! Schema::hasColumn('company_career_settings', 'hero_cta2_text')) {
                    $table->string('hero_cta2_text', 100)->nullable();
                }
                if (! Schema::hasColumn('company_career_settings', 'hero_cta2_link')) {
                    $table->string('hero_cta2_link', 255)->nullable();
                }
                if (! Schema::hasColumn('company_career_settings', 'hero_cta2_newtab')) {
                    $table->boolean('hero_cta2_newtab')->default(false);
                }
                if (! Schema::hasColumn('company_career_settings', 'hero_bg_style')) {
                    $table->string('hero_bg_style', 30)->nullable()->default('gradient');
                }
                if (! Schema::hasColumn('company_career_settings', 'hero_bg_gradient')) {
                    $table->string('hero_bg_gradient', 255)->nullable();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('company_career_settings')) {
            Schema::table('company_career_settings', function (Blueprint $table) {
                $table->dropColumn([
                    'hero_layout',
                    'hero_badge',
                    'hero_heading',
                    'hero_description',
                    'hero_cta1_text',
                    'hero_cta1_link',
                    'hero_cta1_newtab',
                    'hero_cta2_text',
                    'hero_cta2_link',
                    'hero_cta2_newtab',
                    'hero_bg_style',
                    'hero_bg_gradient',
                ]);
            });
        }
    }
};
