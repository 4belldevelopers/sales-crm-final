<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Tests\TestCase;

/**
 * CareerPageSettingsCardSubCardRedesignTest
 *
 * Intent: Verifies that the Service Company Career Page Settings page and all 9 modular tab partials
 * adhere strictly to the Rule 20 Modern Card + Sub-Card Enterprise Design System, preserve 100% of
 * careerPageSettingsSave() form field bindings, and avoid hardcoded tenant brand placeholders.
 */
class CareerPageSettingsCardSubCardRedesignTest extends TestCase
{
    protected function sharedTabViewData(): array
    {
        return [
            'company_id' => 42,
            'company' => (object) [
                'id' => 42,
                'company_name' => 'Apex Talent Labs',
                'logo' => null,
            ],
            'careerSettings' => (object) [
                'primary_color' => '#009EF7',
                'secondary_color' => '#1E293B',
                'font_family' => 'Poppins',
                'default_language' => 'en',
                'currency' => 'USD',
                'page_layout' => 'modern',
                'jobs_display' => 'card',
                'career_page_status' => 1,
                'td_template' => 'modern',
                'td_color_primary' => '#009EF7',
                'td_color_secondary' => '#1E293B',
                'td_color_accent' => '#10B981',
                'td_color_bg' => '#FFFFFF',
                'td_theme_mode' => 'light',
                'td_layout' => 'wide',
                'td_opt_life' => 1,
                'td_opt_testi' => 1,
                'td_opt_animation' => 1,
                'hero_layout' => 'left-right',
                'hero_badge' => 'Hiring Engineers',
                'hero_heading' => 'Join Apex Talent Labs',
                'hero_description' => 'Build next-gen hiring systems.',
                'ci_company_name' => 'Apex Talent Labs',
                'ci_headquarters' => 'Austin, TX',
                'ci_website' => 'https://apextalentlabs.example',
                'benefits_title' => 'Why Work With Us',
                'benefits_count' => 8,
                'js_display_style' => 'card',
                'js_per_page' => 12,
                'seo_meta_title' => 'Careers at Apex Talent Labs',
                'seo_meta_desc' => 'Explore open roles at Apex Talent Labs.',
                'seo_robots' => 'index,follow',
                'adv_caching' => 1,
                'adv_cache_ttl' => 86400,
                'adv_cdn' => 1,
                'adv_img_opt' => 1,
                'adv_maintenance' => 0,
            ],
            'companyName' => 'Apex Talent Labs',
            'careerUrl' => 'https://testing.jero.works/career-jobs/Apex-Talent-Labs/42',
            'companyHost' => 'apextalentlabs.example',
            'primaryColor' => '#009EF7',
            'secondaryColor' => '#1E293B',
            'fontFamily' => 'Poppins',
            'pageLayout' => 'modern',
            'jobsDisplay' => 'card',
            'isActive' => 1,
            'perfScore' => 97,
        ];
    }

    public function test_settings_shell_contains_rule_20_tiers_kpi_strip_and_global_search(): void
    {
        $source = File::get(resource_path('views/service_company/career-page/settings.blade.php'));

        $this->assertStringContainsString('cps-toolbar-card', $source);
        $this->assertStringContainsString('cps-search-subcard', $source);
        $this->assertStringContainsString('id="cpsGlobalSearch"', $source);
        $this->assertStringContainsString('cps-kpi-grid', $source);
        $this->assertStringContainsString('cps-tabs-card', $source);
        $this->assertStringContainsString('cps-tab-btn', $source);
        $this->assertStringContainsString("route('service_company.career.page.settings.save')", $source);
        $this->assertStringNotContainsString('4Bell', $source);
    }

    public function test_all_nine_tabs_render_card_subcard_architecture_and_preserve_form_fields(): void
    {
        $data = $this->sharedTabViewData();
        $tabs = [
            'general' => ['primary_color', 'secondary_color', 'font_family', 'default_language', 'currency', 'page_layout', 'jobs_display', 'career_page_status'],
            'hero' => ['hero_layout', 'hero_badge', 'hero_heading', 'hero_description', 'hero_cta1_text', 'hero_cta2_text', 'hero_bg_style', 'hero_bg_gradient'],
            'company' => ['ci_industry', 'ci_company_name', 'ci_headquarters', 'ci_tagline', 'ci_website', 'ci_linkedin', 'ci_founded_year', 'ci_company_size', 'ci_about', 'ci_mission', 'ci_vision'],
            'benefits' => ['benefits_enabled', 'benefits_title', 'benefits_description', 'benefits_display_style', 'benefits_count'],
            'jobs' => ['js_display_style', 'js_per_page', 'js_sort_by', 'js_show_posted_date', 'js_show_experience', 'js_show_salary', 'js_show_tags', 'js_apply_option'],
            'seo' => ['seo_meta_title', 'seo_meta_desc', 'seo_keywords', 'seo_robots', 'seo_canonical', 'seo_linkedin', 'seo_twitter'],
            'integrations' => ['intSearchInput', 'intCatSelect'],
            'advanced' => ['adv_caching', 'adv_cache_ttl', 'adv_cdn', 'adv_img_opt', 'adv_maintenance', 'adv_maintenance_msg', 'adv_ga4_id', 'cpsExportSettingsJson'],
            'templates' => ['td_template', 'td_color_primary', 'td_color_secondary', 'td_color_accent', 'td_color_bg', 'td_theme_mode', 'td_layout', 'td_custom_css', 'td_opt_life', 'td_opt_testi', 'td_opt_animation'],
        ];

        foreach ($tabs as $tab => $expectedTokens) {
            $html = View::make("service_company.career-page.tabs.{$tab}", $data)->render();

            $this->assertStringContainsString('cps-card', $html, "Tab [{$tab}] must render Rule 20 cps-card containers");
            $this->assertStringContainsString('cps-card-head', $html, "Tab [{$tab}] must render cps-card-head");
            $this->assertStringNotContainsString('4Bell', $html, "Tab [{$tab}] must not hardcode 4Bell placeholder");
            $this->assertStringNotContainsString('4bell.com', $html, "Tab [{$tab}] must not hardcode 4bell.com placeholder");

            foreach ($expectedTokens as $token) {
                $this->assertStringContainsString($token, $html, "Tab [{$tab}] must preserve field/hook [{$token}]");
            }
        }
    }
}
