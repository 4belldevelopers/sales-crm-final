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
    ];

    protected $casts = [
        'company_id' => 'integer',
        'career_page_status' => 'boolean',
        'td_opt_life' => 'boolean',
        'td_opt_testi' => 'boolean',
        'td_opt_animation' => 'boolean',
        'stat_departments' => 'integer',
        'stat_countries' => 'integer',
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
