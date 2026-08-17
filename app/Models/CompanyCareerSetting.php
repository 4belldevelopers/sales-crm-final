<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model: CompanyCareerSetting
 *
 * Intent: Eloquent model representing company career page settings, tenant branding,
 * hero configurations, template design parameters, and customizable statistics.
 *
 * Tenancy: All queries are strictly scoped by company_id.
 *
 * Author: Antigravity AI
 * Date: August 17, 2026
 */
class CompanyCareerSetting extends Model
{
    use HasFactory;

    protected $table = 'company_career_settings';

    protected $fillable = [
        'company_id',
        'primary_color',
        'secondary_color',
        'font_family',
        'default_language',
        'currency',
        'page_layout',
        'jobs_display',
        'career_page_status',
        'td_template',
        'td_color_primary',
        'td_color_secondary',
        'td_color_accent',
        'td_color_bg',
        'td_theme_mode',
        'td_layout',
        'td_logo_path',
        'td_favicon_path',
        'td_hero_img1_path',
        'td_hero_img2_path',
        'td_opt_life',
        'td_opt_testi',
        'td_opt_animation',
        'td_custom_css',
        'stat_departments',
        'stat_countries',
        'stat_employees',
        'stat_retention',
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
        'ci_industry',
        'ci_company_name',
        'ci_headquarters',
        'ci_tagline',
        'ci_website',
        'ci_linkedin',
        'ci_founded_year',
        'ci_company_size',
        'ci_twitter',
        'ci_about',
        'ci_mission',
        'ci_vision',
        'benefits_enabled',
        'benefits_title',
        'benefits_description',
        'benefits_display_style',
        'benefits_count',
        'js_display_style',
        'js_per_page',
        'js_sort_by',
        'js_show_posted_date',
        'js_show_experience',
        'js_show_salary',
        'js_show_tags',
        'js_apply_option',
        'seo_meta_title',
        'seo_meta_desc',
        'seo_keywords',
        'seo_robots',
        'seo_canonical',
        'seo_linkedin',
        'seo_twitter',
        'seo_facebook',
        'seo_instagram',
        'seo_youtube',
        'adv_caching',
        'adv_cache_ttl',
        'adv_cdn',
        'adv_img_opt',
        'adv_maintenance',
        'adv_maintenance_msg',
        'adv_ga4_id',
        'adv_fb_pixel',
        'adv_linkedin_tag',
        'adv_hotjar_id',
    ];

    protected $casts = [
        'company_id' => 'integer',
        'career_page_status' => 'boolean',
        'td_opt_life' => 'boolean',
        'td_opt_testi' => 'boolean',
        'td_opt_animation' => 'boolean',
        'hero_cta1_newtab' => 'boolean',
        'hero_cta2_newtab' => 'boolean',
        'benefits_enabled' => 'boolean',
        'benefits_count' => 'integer',
        'js_per_page' => 'integer',
        'js_show_posted_date' => 'boolean',
        'js_show_experience' => 'boolean',
        'js_show_salary' => 'boolean',
        'js_show_tags' => 'boolean',
        'adv_caching' => 'boolean',
        'adv_cache_ttl' => 'integer',
        'adv_cdn' => 'boolean',
        'adv_img_opt' => 'boolean',
        'adv_maintenance' => 'boolean',
        'stat_departments' => 'integer',
        'stat_countries' => 'integer',
        'ci_founded_year' => 'integer',
    ];

    /**
     * Scope query to active company tenant.
     */
    public function scopeForCompany(Builder $query, ?int $companyId = null): Builder
    {
        $tenantId = $companyId ?? (auth()->check() ? auth()->user()->company_id : null);

        return $query->where('company_id', $tenantId);
    }
}
