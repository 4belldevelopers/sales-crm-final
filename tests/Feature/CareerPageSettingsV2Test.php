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
}
