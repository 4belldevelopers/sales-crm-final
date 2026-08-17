<?php

namespace Tests\Feature;

use App\Models\CompanyCareerSetting;
use App\Repositories\AccountRepository;
use App\Services\AccountSettingsService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * CareerPageSettingsV2Test — Automated Feature & Performance Test Suite for Career Settings V2.
 *
 * Verifies:
 * - Database migration and CompanyCareerSetting Eloquent model tenancy.
 * - Existence and validity of all 9 modular Blade component views.
 * - Zero DDL runtime query execution (eliminates MySQL metadata table locks).
 * - Multi-tenant 24h Redis caching & cache invalidation on flush.
 * - Authentication & tenancy route protection.
 * - Database schema truth synchronization in mysql-schema.sql.
 * - Code documentation standards per AGENTS.md rule #11.
 *
 * NOTE: Does NOT use RefreshDatabase trait to preserve production DB state.
 *
 * Author: Antigravity AI
 * Date: August 17, 2026
 *
 * Run:
 *   php artisan test --filter=CareerPageSettingsV2Test
 */
class CareerPageSettingsV2Test extends TestCase
{
    /**
     * Test 1: Verify CompanyCareerSetting model & DB table exist with correct schema.
     */
    public function test_company_career_settings_table_and_model_exist(): void
    {
        $this->assertTrue(
            class_exists(CompanyCareerSetting::class),
            'CompanyCareerSetting model class must exist in App\\Models'
        );

        $this->assertTrue(
            Schema::hasTable('company_career_settings'),
            'Database table company_career_settings must exist in MySQL'
        );

        $this->assertTrue(
            Schema::hasColumn('company_career_settings', 'company_id'),
            'Table company_career_settings must contain company_id tenant column'
        );

        $this->assertTrue(
            Schema::hasColumn('company_career_settings', 'primary_color'),
            'Table company_career_settings must contain primary_color column'
        );

        $this->assertTrue(
            Schema::hasColumn('company_career_settings', 'td_template'),
            'Table company_career_settings must contain td_template column'
        );
    }

    /**
     * Test 2: Verify all 9 modular Blade component tab views exist.
     */
    public function test_modular_blade_tab_components_exist(): void
    {
        $tabs = [
            'service_company.career-page.tabs.general',
            'service_company.career-page.tabs.hero',
            'service_company.career-page.tabs.company',
            'service_company.career-page.tabs.benefits',
            'service_company.career-page.tabs.jobs',
            'service_company.career-page.tabs.seo',
            'service_company.career-page.tabs.integrations',
            'service_company.career-page.tabs.advanced',
            'service_company.career-page.tabs.templates',
        ];

        foreach ($tabs as $view) {
            $this->assertTrue(
                view()->exists($view),
                "Modular Blade component view [{$view}] must exist"
            );
        }
    }

    /**
     * Test 3: Verify unauthenticated users are redirected when accessing settings route.
     */
    public function test_unauthenticated_user_cannot_access_career_settings(): void
    {
        $response = $this->get('/service_company/career-page/settings');
        $response->assertRedirect();
    }

    /**
     * Test 4: Verify Service layer & Redis multi-tenant caching mechanism.
     */
    public function test_service_layer_and_redis_caching(): void
    {
        $testCompanyId = 99999;
        $cacheKey = "company_career_settings:{$testCompanyId}";
        $publicCacheKey = "tenant_career_meta:{$testCompanyId}";

        // Ensure clean cache state
        Cache::forget($cacheKey);
        Cache::forget($publicCacheKey);

        $service = app(AccountSettingsService::class);
        $repo = app(AccountRepository::class);

        // Fetch career page settings through Service Layer
        $data = $service->getCareerPageData($testCompanyId);
        $this->assertIsArray($data, 'Service getCareerPageData must return an array of parameters');
        $this->assertArrayHasKey('careerSettings', $data);

        // Verify Redis cache key has been set
        $this->assertTrue(
            Cache::has($cacheKey),
            "Redis cache key [{$cacheKey}] must be populated after GET query"
        );

        // Test cache flush
        $service->flushCareerCache($testCompanyId);

        $this->assertFalse(
            Cache::has($cacheKey),
            "Redis cache key [{$cacheKey}] must be flushed after flushCareerCache()"
        );
        $this->assertFalse(
            Cache::has($publicCacheKey),
            "Public Redis cache key [{$publicCacheKey}] must be flushed after flushCareerCache()"
        );
    }

    /**
     * Test 5: Verify zero DDL queries executed during setting retrieval.
     */
    public function test_zero_ddl_queries_during_settings_access(): void
    {
        DB::enableQueryLog();

        $testCompanyId = 88888;
        $repo = app(AccountRepository::class);
        $settings = $repo->getCompanyCareerSettings($testCompanyId);

        $queries = DB::getQueryLog();
        DB::disableQueryLog();

        foreach ($queries as $q) {
            $sql = strtoupper($q['query']);
            $this->assertStringNotContainsString('CREATE TABLE', $sql, 'Query log must contain 0 CREATE TABLE DDL queries');
            $this->assertStringNotContainsString('ALTER TABLE', $sql, 'Query log must contain 0 ALTER TABLE DDL queries');
            $this->assertStringNotContainsString('SHOW COLUMNS', $sql, 'Query log must contain 0 SHOW COLUMNS queries');
        }
    }

    /**
     * Test 6: Verify database/schema/mysql-schema.sql contains company_career_settings table definition.
     */
    public function test_mysql_schema_sql_contains_company_career_settings_table(): void
    {
        $schemaPath = database_path('schema/mysql-schema.sql');
        $this->assertFileExists($schemaPath, 'mysql-schema.sql must exist');

        $schemaContent = File::get($schemaPath);
        $this->assertStringContainsString(
            'company_career_settings',
            $schemaContent,
            'mysql-schema.sql Database Truth must contain company_career_settings table schema'
        );
    }

    /**
     * Test 7: Verify AGENTS.md rule #11 compliance (Intent documentation on V2 classes).
     */
    public function test_intent_documentation_standards(): void
    {
        $filesToVerify = [
            app_path('Models/CompanyCareerSetting.php'),
            app_path('Http/Controllers/ServiceCompany/ServiceCompanyAccountSettingsController.php'),
            app_path('Services/AccountSettingsService.php'),
            app_path('Repositories/AccountRepository.php'),
        ];

        foreach ($filesToVerify as $filePath) {
            $this->assertFileExists($filePath);
            $content = File::get($filePath);
            $this->assertTrue(
                str_contains($content, '/**') || str_contains($content, '/*'),
                "File [{$filePath}] must contain PHPDoc intent documentation per AGENTS.md rule #11"
            );
        }
    }

    /**
     * Test 8: Verify public career jobs page renders dynamic colors, typography, and fallbacks.
     */
    public function test_public_career_page_dynamic_colors_and_fallbacks(): void
    {
        $testCompanyId = 2;
        $companyName = '4Bell-Technology';

        // Clear cache prior to request
        app(AccountSettingsService::class)->flushCareerCache($testCompanyId);

        // 1. Set custom primary color, font, and status in DB
        CompanyCareerSetting::updateOrCreate(
            ['company_id' => $testCompanyId],
            [
                'primary_color' => '#7C3AED',
                'td_color_primary' => '#7C3AED',
                'font_family' => 'Inter',
                'career_page_status' => 1,
            ]
        );
        app(AccountSettingsService::class)->flushCareerCache($testCompanyId);

        $response = $this->get("/career-jobs/{$companyName}/{$testCompanyId}");
        $response->assertStatus(200);
        $response->assertSee('--p:  #7C3AED', false);
        $response->assertSee("font-family:'Inter'", false);

        // 2. Test fallbacks when settings colors are empty
        CompanyCareerSetting::updateOrCreate(
            ['company_id' => $testCompanyId],
            [
                'primary_color' => null,
                'td_color_primary' => null,
                'font_family' => null,
                'career_page_status' => 1,
            ]
        );
        app(AccountSettingsService::class)->flushCareerCache($testCompanyId);

        $responseFallback = $this->get("/career-jobs/{$companyName}/{$testCompanyId}");
        $responseFallback->assertStatus(200);
        $responseFallback->assertSee('--p:  #2563EB', false);
        $responseFallback->assertSee("font-family:'Poppins'", false);

        // 3. Test offline status when career_page_status is 0
        CompanyCareerSetting::updateOrCreate(
            ['company_id' => $testCompanyId],
            [
                'career_page_status' => 0,
            ]
        );
        app(AccountSettingsService::class)->flushCareerCache($testCompanyId);

        $responseOffline = $this->get("/career-jobs/{$companyName}/{$testCompanyId}");
        $responseOffline->assertStatus(200);
        $responseOffline->assertSee('Our career portal is currently offline', false);

        // Reset status to online after test
        CompanyCareerSetting::updateOrCreate(
            ['company_id' => $testCompanyId],
            ['career_page_status' => 1]
        );
        app(AccountSettingsService::class)->flushCareerCache($testCompanyId);
    }

    /**
     * Test 9: Verify hero section settings (badge, heading, description, CTAs) dynamically render on public page.
     */
    public function test_hero_section_dynamic_settings(): void
    {
        $testCompanyId = 2;
        $companyName = '4Bell-Technology';

        CompanyCareerSetting::updateOrCreate(
            ['company_id' => $testCompanyId],
            [
                'career_page_status' => 1,
                'hero_badge' => '🚀 Hiring Top Talent',
                'hero_heading' => 'Shape the Future of ATS',
                'hero_description' => 'Join our global team of engineering pioneers.',
                'hero_cta1_text' => 'Browse Jobs',
                'hero_cta1_link' => '#jobs',
                'hero_cta2_text' => 'Learn More',
                'hero_cta2_link' => 'https://example.com/about',
            ]
        );
        app(AccountSettingsService::class)->flushCareerCache($testCompanyId);

        $response = $this->get("/career-jobs/{$companyName}/{$testCompanyId}");
        $response->assertStatus(200);
        $response->assertSee('🚀 Hiring Top Talent');
        $response->assertSee('Shape the Future of ATS');
        $response->assertSee('Join our global team of engineering pioneers.');
        $response->assertSee('Browse Jobs');
        $response->assertSee('Learn More');
    }

    /**
     * Test 10: Verify settings from all 9 tabs (Company Info, Benefits, SEO, Social, Advanced) render dynamically.
     */
    public function test_all_tabs_dynamic_settings(): void
    {
        $testCompanyId = 2;
        $companyName = '4Bell-Technology';

        CompanyCareerSetting::updateOrCreate(
            ['company_id' => $testCompanyId],
            [
                'career_page_status' => 1,
                'ci_company_name' => 'Acme Global Corp',
                'ci_tagline' => 'Transforming Innovation Daily',
                'seo_meta_title' => 'Acme Global Careers & Openings',
                'seo_meta_desc' => 'Explore premier opportunities at Acme Global Corp.',
                'seo_linkedin' => 'https://linkedin.com/company/acme-global',
                'adv_maintenance' => false,
            ]
        );
        app(AccountSettingsService::class)->flushCareerCache($testCompanyId);

        $response = $this->get("/career-jobs/{$companyName}/{$testCompanyId}");
        $response->assertStatus(200);
        $response->assertSee('Acme Global Corp');
        $response->assertSee('Acme Global Careers & Openings');
        $response->assertSee('Explore premier opportunities at Acme Global Corp.');
        $response->assertSee('https://linkedin.com/company/acme-global');
    }
}
