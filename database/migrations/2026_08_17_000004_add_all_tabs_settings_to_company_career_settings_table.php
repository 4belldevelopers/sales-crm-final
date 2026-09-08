<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: AddAllTabsSettingsToCompanyCareerSettingsTable
 *
 * Intent: Adds columns for all 9 Career Settings tabs (Company Info, Benefits,
 * Job Settings, SEO & Social, Advanced/Tracking) to `company_career_settings` table.
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
                /* Company Info */
                if (! Schema::hasColumn('company_career_settings', 'ci_industry')) {
                    $table->string('ci_industry', 100)->nullable();
                }
                if (! Schema::hasColumn('company_career_settings', 'ci_company_name')) {
                    $table->string('ci_company_name', 150)->nullable();
                }
                if (! Schema::hasColumn('company_career_settings', 'ci_headquarters')) {
                    $table->string('ci_headquarters', 255)->nullable();
                }
                if (! Schema::hasColumn('company_career_settings', 'ci_tagline')) {
                    $table->string('ci_tagline', 255)->nullable();
                }
                if (! Schema::hasColumn('company_career_settings', 'ci_website')) {
                    $table->string('ci_website', 255)->nullable();
                }
                if (! Schema::hasColumn('company_career_settings', 'ci_linkedin')) {
                    $table->string('ci_linkedin', 255)->nullable();
                }
                if (! Schema::hasColumn('company_career_settings', 'ci_founded_year')) {
                    $table->integer('ci_founded_year')->nullable();
                }
                if (! Schema::hasColumn('company_career_settings', 'ci_company_size')) {
                    $table->string('ci_company_size', 50)->nullable();
                }
                if (! Schema::hasColumn('company_career_settings', 'ci_twitter')) {
                    $table->string('ci_twitter', 255)->nullable();
                }
                if (! Schema::hasColumn('company_career_settings', 'ci_about')) {
                    $table->text('ci_about')->nullable();
                }
                if (! Schema::hasColumn('company_career_settings', 'ci_mission')) {
                    $table->text('ci_mission')->nullable();
                }
                if (! Schema::hasColumn('company_career_settings', 'ci_vision')) {
                    $table->text('ci_vision')->nullable();
                }

                /* Benefits Section */
                if (! Schema::hasColumn('company_career_settings', 'benefits_enabled')) {
                    $table->boolean('benefits_enabled')->default(true);
                }
                if (! Schema::hasColumn('company_career_settings', 'benefits_title')) {
                    $table->string('benefits_title', 150)->nullable();
                }
                if (! Schema::hasColumn('company_career_settings', 'benefits_description')) {
                    $table->text('benefits_description')->nullable();
                }
                if (! Schema::hasColumn('company_career_settings', 'benefits_display_style')) {
                    $table->string('benefits_display_style', 30)->nullable()->default('grid');
                }
                if (! Schema::hasColumn('company_career_settings', 'benefits_count')) {
                    $table->integer('benefits_count')->default(8);
                }

                /* Job Settings */
                if (! Schema::hasColumn('company_career_settings', 'js_display_style')) {
                    $table->string('js_display_style', 30)->nullable()->default('card');
                }
                if (! Schema::hasColumn('company_career_settings', 'js_per_page')) {
                    $table->integer('js_per_page')->default(12);
                }
                if (! Schema::hasColumn('company_career_settings', 'js_sort_by')) {
                    $table->string('js_sort_by', 30)->nullable()->default('newest');
                }
                if (! Schema::hasColumn('company_career_settings', 'js_show_posted_date')) {
                    $table->boolean('js_show_posted_date')->default(true);
                }
                if (! Schema::hasColumn('company_career_settings', 'js_show_experience')) {
                    $table->boolean('js_show_experience')->default(true);
                }
                if (! Schema::hasColumn('company_career_settings', 'js_show_salary')) {
                    $table->boolean('js_show_salary')->default(true);
                }
                if (! Schema::hasColumn('company_career_settings', 'js_show_tags')) {
                    $table->boolean('js_show_tags')->default(true);
                }
                if (! Schema::hasColumn('company_career_settings', 'js_apply_option')) {
                    $table->string('js_apply_option', 30)->nullable()->default('resume');
                }

                /* SEO & Social */
                if (! Schema::hasColumn('company_career_settings', 'seo_meta_title')) {
                    $table->string('seo_meta_title', 255)->nullable();
                }
                if (! Schema::hasColumn('company_career_settings', 'seo_meta_desc')) {
                    $table->text('seo_meta_desc')->nullable();
                }
                if (! Schema::hasColumn('company_career_settings', 'seo_keywords')) {
                    $table->text('seo_keywords')->nullable();
                }
                if (! Schema::hasColumn('company_career_settings', 'seo_robots')) {
                    $table->string('seo_robots', 50)->nullable()->default('index,follow');
                }
                if (! Schema::hasColumn('company_career_settings', 'seo_canonical')) {
                    $table->string('seo_canonical', 500)->nullable();
                }
                if (! Schema::hasColumn('company_career_settings', 'seo_linkedin')) {
                    $table->string('seo_linkedin', 255)->nullable();
                }
                if (! Schema::hasColumn('company_career_settings', 'seo_twitter')) {
                    $table->string('seo_twitter', 255)->nullable();
                }
                if (! Schema::hasColumn('company_career_settings', 'seo_facebook')) {
                    $table->string('seo_facebook', 255)->nullable();
                }
                if (! Schema::hasColumn('company_career_settings', 'seo_instagram')) {
                    $table->string('seo_instagram', 255)->nullable();
                }
                if (! Schema::hasColumn('company_career_settings', 'seo_youtube')) {
                    $table->string('seo_youtube', 255)->nullable();
                }

                /* Advanced / Analytics */
                if (! Schema::hasColumn('company_career_settings', 'adv_caching')) {
                    $table->boolean('adv_caching')->default(true);
                }
                if (! Schema::hasColumn('company_career_settings', 'adv_cache_ttl')) {
                    $table->integer('adv_cache_ttl')->default(86400);
                }
                if (! Schema::hasColumn('company_career_settings', 'adv_cdn')) {
                    $table->boolean('adv_cdn')->default(true);
                }
                if (! Schema::hasColumn('company_career_settings', 'adv_img_opt')) {
                    $table->boolean('adv_img_opt')->default(true);
                }
                if (! Schema::hasColumn('company_career_settings', 'adv_maintenance')) {
                    $table->boolean('adv_maintenance')->default(false);
                }
                if (! Schema::hasColumn('company_career_settings', 'adv_maintenance_msg')) {
                    $table->text('adv_maintenance_msg')->nullable();
                }
                if (! Schema::hasColumn('company_career_settings', 'adv_ga4_id')) {
                    $table->string('adv_ga4_id', 50)->nullable();
                }
                if (! Schema::hasColumn('company_career_settings', 'adv_fb_pixel')) {
                    $table->string('adv_fb_pixel', 50)->nullable();
                }
                if (! Schema::hasColumn('company_career_settings', 'adv_linkedin_tag')) {
                    $table->string('adv_linkedin_tag', 100)->nullable();
                }
                if (! Schema::hasColumn('company_career_settings', 'adv_hotjar_id')) {
                    $table->string('adv_hotjar_id', 50)->nullable();
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
                    'ci_industry', 'ci_company_name', 'ci_headquarters', 'ci_tagline',
                    'ci_website', 'ci_linkedin', 'ci_founded_year', 'ci_company_size',
                    'ci_twitter', 'ci_about', 'ci_mission', 'ci_vision',
                    'benefits_enabled', 'benefits_title', 'benefits_description',
                    'benefits_display_style', 'benefits_count',
                    'js_display_style', 'js_per_page', 'js_sort_by',
                    'js_show_posted_date', 'js_show_experience', 'js_show_salary',
                    'js_show_tags', 'js_apply_option',
                    'seo_meta_title', 'seo_meta_desc', 'seo_keywords', 'seo_robots',
                    'seo_canonical', 'seo_linkedin', 'seo_twitter', 'seo_facebook',
                    'seo_instagram', 'seo_youtube',
                    'adv_caching', 'adv_cache_ttl', 'adv_cdn', 'adv_img_opt',
                    'adv_maintenance', 'adv_maintenance_msg',
                    'adv_ga4_id', 'adv_fb_pixel', 'adv_linkedin_tag', 'adv_hotjar_id',
                ]);
            });
        }
    }
};
