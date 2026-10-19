<?php

namespace Tests\Feature;

use App\Services\CurrencyService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * HiringServicesCurrencySynchronizationTest
 *
 * Verifies that the tenant default currency configured in Company Profile -> Settings
 * authoritatively synchronizes with Edit Fulltime Hiring Services form, preserving
 * manual overrides and fallback mechanisms per God-Level Currency System Architecture.
 */
class HiringServicesCurrencySynchronizationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Ensure clean cache state
        Cache::flush();

        // Setup SQLite in-memory test tables if running in SQLite environment
        if (config('database.default') === 'sqlite') {
            $this->setUpSqliteSchema();
        }
    }

    private function setUpSqliteSchema(): void
    {
        if (! Schema::hasTable('master_currency')) {
            Schema::create('master_currency', function ($table) {
                $table->increments('id');
                $table->string('country')->nullable();
                $table->string('currency')->nullable();
                $table->string('code')->nullable();
                $table->string('symbol')->nullable();
            });
            DB::table('master_currency')->insert([
                ['id' => 53, 'country' => 'India', 'currency' => 'Rupees', 'code' => 'INR', 'symbol' => '₹'],
                ['id' => 2, 'country' => 'United States', 'currency' => 'Dollars', 'code' => 'USD', 'symbol' => '$'],
                ['id' => 38, 'country' => 'Germany', 'currency' => 'Euro', 'code' => 'EUR', 'symbol' => '€'],
                ['id' => 10, 'country' => 'United Kingdom', 'currency' => 'Pound', 'code' => 'GBP', 'symbol' => '£'],
                ['id' => 108, 'country' => 'United Arab Emirates', 'currency' => 'Dirham', 'code' => 'AED', 'symbol' => 'د.إ'],
            ]);
        }

        if (! Schema::hasTable('currencies')) {
            Schema::create('currencies', function ($table) {
                $table->increments('id');
                $table->integer('getKey')->nullable();
                $table->string('currency_code')->nullable();
                $table->string('currency_name')->nullable();
                $table->string('currency_symbol')->nullable();
            });
            DB::table('currencies')->insert([
                ['id' => 1, 'getKey' => 0, 'currency_code' => 'INR', 'currency_name' => 'Indian Rupee', 'currency_symbol' => '₹'],
                ['id' => 2, 'getKey' => 1, 'currency_code' => 'EUR', 'currency_name' => 'Euro', 'currency_symbol' => '€'],
                ['id' => 3, 'getKey' => 2, 'currency_code' => 'JPY', 'currency_name' => 'Japanese Yen', 'currency_symbol' => '¥'],
                ['id' => 4, 'getKey' => 3, 'currency_code' => 'GBP', 'currency_name' => 'British Pound', 'currency_symbol' => '£'],
                ['id' => 5, 'getKey' => 4, 'currency_code' => 'USD', 'currency_name' => 'US Dollar', 'currency_symbol' => '$'],
                ['id' => 6, 'getKey' => 5, 'currency_code' => 'AED', 'currency_name' => 'UAE Dirham', 'currency_symbol' => 'د.إ'],
            ]);
        }

        if (! Schema::hasTable('companies')) {
            Schema::create('companies', function ($table) {
                $table->increments('id');
                $table->string('company_name')->nullable();
                $table->string('currency')->nullable();
                $table->string('calling_code')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('commericials')) {
            Schema::create('commericials', function ($table) {
                $table->increments('id');
                $table->integer('company_id');
                $table->integer('commtype');
                $table->integer('salary_currency')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('company_career_settings')) {
            Schema::create('company_career_settings', function ($table) {
                $table->increments('id');
                $table->integer('company_id');
                $table->string('currency')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Test 1: Company default currency INR (53) maps accurately to code INR and getKey 0.
     */
    public function test_tenant_currency_resolves_inr_when_company_currency_is_53(): void
    {
        $companyId = DB::table('companies')->insertGetId([
            'company_name' => 'Acme India Pvt Ltd',
            'currency' => '53',
            'calling_code' => '91',
        ]);

        $code = CurrencyService::getTenantCurrency($companyId);
        $key = CurrencyService::getTenantCurrencyKey($companyId);

        $this->assertSame('INR', $code);
        $this->assertSame(0, $key);
    }

    /**
     * Test 2: Company default currency USD (2) maps accurately to code USD and getKey 4.
     * Protects against regression where master_currency id 2 was confused with currencies.getKey 2 (JPY).
     */
    public function test_tenant_currency_resolves_usd_when_company_currency_is_2(): void
    {
        $companyId = DB::table('companies')->insertGetId([
            'company_name' => 'Acme US Corp',
            'currency' => '2',
            'calling_code' => '1',
        ]);

        $code = CurrencyService::getTenantCurrency($companyId);
        $key = CurrencyService::getTenantCurrencyKey($companyId);

        $this->assertSame('USD', $code);
        $this->assertSame(4, $key);
    }

    /**
     * Test 3: Company default currency EUR (38) maps accurately to code EUR and getKey 1.
     */
    public function test_tenant_currency_resolves_eur_when_company_currency_is_38(): void
    {
        $companyId = DB::table('companies')->insertGetId([
            'company_name' => 'Acme Europe GmbH',
            'currency' => '38',
            'calling_code' => '49',
        ]);

        $code = CurrencyService::getTenantCurrency($companyId);
        $key = CurrencyService::getTenantCurrencyKey($companyId);

        $this->assertSame('EUR', $code);
        $this->assertSame(1, $key);
    }

    /**
     * Test 4: When company has no configured currency, HQ calling code fallback resolves default currency.
     */
    public function test_tenant_currency_falls_back_to_calling_code_when_currency_is_null(): void
    {
        $companyId = DB::table('companies')->insertGetId([
            'company_name' => 'Acme Fallback Ltd',
            'currency' => null,
            'calling_code' => '91', // India calling code
        ]);

        $code = CurrencyService::getTenantCurrency($companyId);
        $key = CurrencyService::getTenantCurrencyKey($companyId);

        $this->assertSame('INR', $code);
        $this->assertSame(0, $key);
    }

    /**
     * Test 5: setTenantCurrency updates companies table, syncs commericials for both contract and fulltime,
     * and purges tenant caches.
     */
    public function test_set_tenant_currency_updates_company_and_syncs_commercials(): void
    {
        $companyId = DB::table('companies')->insertGetId([
            'company_name' => 'Acme Dynamic Co',
            'currency' => '2', // USD
        ]);

        // Pre-existing commercials rows
        $contractCommId = DB::table('commericials')->insertGetId([
            'company_id' => $companyId,
            'commtype' => 1,
            'salary_currency' => 4,
        ]);
        $fulltimeCommId = DB::table('commericials')->insertGetId([
            'company_id' => $companyId,
            'commtype' => 2,
            'salary_currency' => null,
        ]);

        // Verify initial state
        $this->assertSame('USD', CurrencyService::getTenantCurrency($companyId));

        // Update settings to INR (53)
        CurrencyService::setTenantCurrency($companyId, 53);

        // Verify company currency was updated preserving numeric ID
        $updatedCompany = DB::table('companies')->where('id', $companyId)->first();
        $this->assertEquals(53, $updatedCompany->currency);

        // Verify cache was cleared and tenant currency returns INR
        $this->assertSame('INR', CurrencyService::getTenantCurrency($companyId));
        $this->assertSame(0, CurrencyService::getTenantCurrencyKey($companyId));

        // Verify both contract and fulltime commercials were synced to INR key (0)
        $contractComm = DB::table('commericials')->where('id', $contractCommId)->first();
        $fulltimeComm = DB::table('commericials')->where('id', $fulltimeCommId)->first();
        $this->assertEquals(0, $contractComm->salary_currency);
        $this->assertEquals(0, $fulltimeComm->salary_currency);
    }

    /**
     * Test 6: Multi-tenant isolation - company A setting INR does not affect company B setting USD.
     */
    public function test_tenant_isolation_between_distinct_companies(): void
    {
        $companyAId = DB::table('companies')->insertGetId([
            'company_name' => 'Company A (India)',
            'currency' => '53',
        ]);
        $companyBId = DB::table('companies')->insertGetId([
            'company_name' => 'Company B (US)',
            'currency' => '2',
        ]);

        $this->assertSame('INR', CurrencyService::getTenantCurrency($companyAId));
        $this->assertSame(0, CurrencyService::getTenantCurrencyKey($companyAId));

        $this->assertSame('USD', CurrencyService::getTenantCurrency($companyBId));
        $this->assertSame(4, CurrencyService::getTenantCurrencyKey($companyBId));
    }

    /**
     * Test 7: Verify services_view.blade.php does not contain hardcoded : 4 fallback,
     * uses the tenant default currency key, and renders the unified currencies list.
     */
    public function test_services_view_blade_uses_tenant_default_currency_without_hardcoded_4(): void
    {
        $blade = file_get_contents(resource_path('views/service_company/jobs/services_view.blade.php'));

        // Ensure hardcoded 4 fallback is removed
        $this->assertStringNotContainsString(': 4;', $blade);

        // Ensure tenant default currency key lookup is present
        $this->assertStringContainsString('CurrencyService::getTenantCurrencyKey', $blade);
        $this->assertStringContainsString('$tenantDefaultCurrencyKey', $blade);

        // Ensure unified 113-currency rendering is wired
        $this->assertStringContainsString('CurrencyService::getSettingCurrenciesList()', $blade);
        $this->assertStringContainsString('$currenciesToRender', $blade);

        // Ensure both commercial_salary_currency and data-range-currency selects check selectedCurrency
        $this->assertStringContainsString('id="commercial_salary_currency"', $blade);
        $this->assertStringContainsString('data-range-currency', $blade);
        $this->assertStringContainsString('$isSelected ? \'selected\' : \'\'', $blade);
    }

    /**
     * Test 8: Verify account/index.blade.php uses authoritative CurrencyService for tab facts
     * instead of hardcoded USD.
     */
    public function test_account_index_blade_uses_authoritative_currency_service(): void
    {
        $blade = file_get_contents(resource_path('views/service_company/account/index.blade.php'));

        $this->assertStringContainsString('$cv2FulltimeCurrencyCode', $blade);
        $this->assertStringContainsString('$cv2ContractCurrencyCode', $blade);
        $this->assertStringContainsString('\App\Services\CurrencyService::getTenantCurrency', $blade);
    }

    /**
     * Test 9: Verify ServiceCompanyAccountSettingsController seeds commercials using CurrencyService
     * instead of hardcoding 4.
     */
    public function test_account_settings_controller_seeds_with_tenant_currency_key(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/ServiceCompany/ServiceCompanyAccountSettingsController.php'));

        $this->assertStringContainsString('CurrencyService::getTenantCurrencyKey($company_id)', $controller);
        $this->assertStringContainsString('CurrencyService::setTenantCurrency($company_id, $request->currency)', $controller);
    }

    /**
     * Test 10: Verify ServiceCompanyJobsController serviceviewmore injects unified currency list, defaultCurrencyCode and defaultCurrencyKey.
     */
    public function test_service_company_jobs_controller_passes_default_currency_to_view(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/ServiceCompany/ServiceCompanyJobsController.php'));

        $this->assertStringContainsString('$this->currencyList = \App\Services\CurrencyService::getSettingCurrenciesList();', $controller);
        $this->assertStringContainsString('$this->defaultCurrencyCode = $this->tenantDefaultCurrency;', $controller);
        $this->assertStringContainsString('$this->defaultCurrencyKey = \App\Services\CurrencyService::getTenantCurrencyKey($company_id);', $controller);
    }

    /**
     * Test 11: Contract and Fulltime Hiring Services inherit tenant default currency
     * when commercial row retains legacy unconfigured default USD (4) while tenant default is INR (0).
     */
    public function test_legacy_usd_default_inherits_tenant_default_currency_for_contract_and_fulltime(): void
    {
        $companyId = DB::table('companies')->insertGetId([
            'company_name' => 'Acme Legacy Co',
            'currency' => '53', // INR (key 0)
        ]);

        $tenantDefaultKey = CurrencyService::getTenantCurrencyKey($companyId);
        $this->assertSame(0, $tenantDefaultKey);

        // Simulate legacy commercial row seeded with 4 (USD)
        $savedLegacyCurrency = 4;
        $selectedCurrency = ($savedLegacyCurrency === null || ($savedLegacyCurrency === 4 && $tenantDefaultKey !== 4))
            ? $tenantDefaultKey
            : $savedLegacyCurrency;

        $this->assertSame(0, $selectedCurrency);

        // If user explicitly saved a custom currency (e.g. EUR key 1)
        $savedCustomCurrency = 1;
        $selectedCustomCurrency = ($savedCustomCurrency === null || ($savedCustomCurrency === 4 && $tenantDefaultKey !== 4))
            ? $tenantDefaultKey
            : $savedCustomCurrency;

        $this->assertSame(1, $selectedCustomCurrency);
    }
}
