<?php

namespace App\Services;

use App\Helper\GeneralMethod;
use App\Models\Job;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Enterprise Centralized Currency Management & Conversion Engine
 *
 * Intent: Provides authoritative currency resolution, multi-tenant synchronization,
 * high-precision FX conversions, and dual-currency matching across the JERO platform.
 *
 * Tenancy: Fully tenant-aware via company_id, with multi-tenant atomic cache invalidation.
 * CQRS: Zero MySQL reads on user request queries; operates via Redis caching and pre-warmed snapshots.
 */
class CurrencyService
{
    /**
     * Cache TTL for static currency metadata (24 hours).
     */
    const TTL_METADATA = 86400;

    /**
     * Cache TTL for tenant default currency (24 hours).
     */
    const TTL_TENANT = 86400;

    /**
     * Default fallback currency code.
     */
    const DEFAULT_CURRENCY = 'USD';

    /**
     * Default fallback currency key (USD = 4).
     */
    const DEFAULT_CURRENCY_KEY = 4;

    /**
     * Currency flags mapping for all 46 system currencies.
     */
    const CURRENCY_FLAGS = [
        'INR' => '🇮🇳', 'EUR' => '🇪🇺', 'JPY' => '🇯🇵', 'GBP' => '🇬🇧', 'USD' => '🇺🇸', 'AED' => '🇦🇪',
        'CAD' => '🇨🇦', 'AUD' => '🇦🇺', 'SGD' => '🇸🇬', 'CHF' => '🇨🇭', 'CNY' => '🇨🇳', 'NZD' => '🇳🇿',
        'ZAR' => '🇿🇦', 'BRL' => '🇧🇷', 'PHP' => '🇵🇭', 'MYR' => '🇲🇾', 'THB' => '🇹🇭', 'IDR' => '🇮🇩',
        'VND' => '🇻🇳', 'SEK' => '🇸🇪', 'NOK' => '🇳🇴', 'DKK' => '🇩🇰', 'PLN' => '🇵🇱', 'MXN' => '🇲🇽',
        'HKD' => '🇭🇰', 'TWD' => '🇹🇼', 'KRW' => '🇰🇷', 'TRY' => '🇹🇷', 'SAR' => '🇸🇦', 'QAR' => '🇶🇦',
        'KWD' => '🇰🇼', 'BHD' => '🇧🇭', 'OMR' => '🇴🇲', 'EGP' => '🇪🇬', 'ILS' => '🇮🇱', 'CZK' => '🇨🇿',
        'HUF' => '🇭🇺', 'RON' => '🇷🇴', 'CLP' => '🇨🇱', 'COP' => '🇨🇴', 'ARS' => '🇦🇷', 'NGN' => '🇳🇬',
        'KES' => '🇰🇪', 'PKR' => '🇵🇰', 'BDT' => '🇧🇩', 'LKR' => '🇱🇰', 'NPR' => '🇳🇵',
    ];

    /**
     * Standard Country Code (ISO2) -> Currency Code mapping.
     */
    const COUNTRY_TO_CURRENCY = [
        'IN' => 'INR', 'US' => 'USD', 'GB' => 'GBP', 'UK' => 'GBP', 'AE' => 'AED',
        'CA' => 'CAD', 'AU' => 'AUD', 'SG' => 'SGD', 'CH' => 'CHF', 'JP' => 'JPY',
        'CN' => 'CNY', 'NZ' => 'NZD', 'ZA' => 'ZAR', 'BR' => 'BRL', 'PH' => 'PHP',
        'MY' => 'MYR', 'TH' => 'THB', 'ID' => 'IDR', 'VN' => 'VND', 'SE' => 'SEK',
        'NO' => 'NOK', 'DK' => 'DKK', 'PL' => 'PLN', 'MX' => 'MXN', 'HK' => 'HKD',
        'TW' => 'TWD', 'KR' => 'KRW', 'TR' => 'TRY', 'SA' => 'SAR', 'QA' => 'QAR',
        'KW' => 'KWD', 'BH' => 'BHD', 'OM' => 'OMR', 'EG' => 'EGP', 'IL' => 'ILS',
        'CZ' => 'CZK', 'HU' => 'HUF', 'RO' => 'RON', 'CL' => 'CLP', 'CO' => 'COP',
        'AR' => 'ARS', 'NG' => 'NGN', 'KE' => 'KES', 'PK' => 'PKR', 'BD' => 'BDT',
        'LK' => 'LKR', 'NP' => 'NPR',
        // Eurozone countries
        'DE' => 'EUR', 'FR' => 'EUR', 'IT' => 'EUR', 'ES' => 'EUR', 'NL' => 'EUR',
        'BE' => 'EUR', 'IE' => 'EUR', 'AT' => 'EUR', 'PT' => 'EUR', 'FI' => 'EUR',
        'GR' => 'EUR', 'SK' => 'EUR', 'SI' => 'EUR', 'LU' => 'EUR', 'CY' => 'EUR',
        'EE' => 'EUR', 'LV' => 'EUR', 'LT' => 'EUR', 'MT' => 'EUR',
    ];

    /**
     * Phone calling code -> Currency Code mapping.
     */
    const CALLING_CODE_TO_CURRENCY = [
        '91' => 'INR', '1' => 'USD', '44' => 'GBP', '971' => 'AED',
        '61' => 'AUD', '65' => 'SGD', '41' => 'CHF', '81' => 'JPY',
        '86' => 'CNY', '64' => 'NZD', '27' => 'ZAR', '55' => 'BRL',
        '63' => 'PHP', '60' => 'MYR', '66' => 'THB', '62' => 'IDR',
        '84' => 'VND', '46' => 'SEK', '47' => 'NOK', '45' => 'DKK',
        '48' => 'PLN', '52' => 'MXN', '852' => 'HKD', '886' => 'TWD',
        '82' => 'KRW', '90' => 'TRY', '966' => 'SAR', '974' => 'QAR',
        '965' => 'KWD', '973' => 'BHD', '968' => 'OMR', '20' => 'EGP',
        '972' => 'ILS', '420' => 'CZK', '36' => 'HUF', '40' => 'RON',
        '56' => 'CLP', '57' => 'COP', '54' => 'ARS', '234' => 'NGN',
        '254' => 'KES', '92' => 'PKR', '880' => 'BDT', '94' => 'LKR',
        '977' => 'NPR',
        // Eurozone calling codes
        '49' => 'EUR', '33' => 'EUR', '39' => 'EUR', '34' => 'EUR',
        '31' => 'EUR', '32' => 'EUR', '353' => 'EUR', '43' => 'EUR',
        '351' => 'EUR', '358' => 'EUR', '30' => 'EUR',
    ];

    /**
     * In-memory static cache for the currency JSON dataset.
     *
     * @var array<string, mixed>|null
     */
    private static ?array $currencyJsonData = null;

    /**
     * Load full enterprise currency dataset from static JSON file.
     * Serves 100% of currency metadata and mappings with ZERO MySQL queries.
     *
     * @return array<string, mixed>
     */
    public static function loadCurrencyJson(): array
    {
        if (self::$currencyJsonData !== null) {
            return self::$currencyJsonData;
        }

        $resourcesPath = resource_path('data/currencies.json');
        $publicPath = public_path('country-state-city/currencies.json');

        $filePath = file_exists($resourcesPath) ? $resourcesPath : (file_exists($publicPath) ? $publicPath : null);

        if ($filePath && file_exists($filePath)) {
            $raw = @file_get_contents($filePath);
            if ($raw) {
                $decoded = json_decode($raw, true);
                if (is_array($decoded) && ! empty($decoded['setting_currencies'])) {
                    return self::$currencyJsonData = $decoded;
                }
            }
        }

        // Defensive fallback if file is missing (e.g. fresh test environment)
        return self::$currencyJsonData = [
            'currencies' => [],
            'master_currencies' => [],
            'setting_currencies' => [],
            'key_to_code' => [],
            'code_to_key' => [],
            'symbols' => [],
            'rates_to_inr' => [],
            'rates_to_usd' => [],
        ];
    }

    /**
     * Get all active system currencies indexed by getKey (0 => 'INR', 1 => 'EUR', ...).
     * Served directly from static JSON / in-memory cache (0% SQL queries).
     *
     * @return array<int, string>
     */
    public static function getCurrencies(): array
    {
        $data = self::loadCurrencyJson();
        if (! empty($data['key_to_code'])) {
            $list = [];
            for ($i = 0; $i <= 5; $i++) {
                if (isset($data['key_to_code'][$i])) {
                    $list[$i] = $data['key_to_code'][$i];
                }
            }
            foreach ($data['key_to_code'] as $k => $c) {
                $list[(int) $k] = $c;
            }

            return $list;
        }

        return [
            0 => 'INR', 1 => 'EUR', 2 => 'JPY', 3 => 'GBP', 4 => 'USD', 5 => 'AED',
            6 => 'CAD', 7 => 'AUD', 8 => 'SGD', 9 => 'CHF', 10 => 'NZD', 11 => 'ZAR',
        ];
    }

    /**
     * Get reverse mapping of currency code to integer getKey ('INR' => 0, 'USD' => 4, ...).
     * Served directly from static JSON / in-memory cache (0% SQL queries).
     *
     * @return array<string, int>
     */
    public static function getCurrencyMap(): array
    {
        $data = self::loadCurrencyJson();
        if (! empty($data['code_to_key'])) {
            return $data['code_to_key'];
        }

        return [
            'INR' => 0, 'EUR' => 1, 'JPY' => 2, 'GBP' => 3, 'USD' => 4, 'AED' => 5,
        ];
    }

    /**
     * Get currency symbols map ('INR' => '₹', 'USD' => '$', ...).
     * Served directly from static JSON / in-memory cache (0% SQL queries).
     *
     * @return array<string, string>
     */
    public static function getCurrencySymbols(): array
    {
        $data = self::loadCurrencyJson();
        if (! empty($data['symbols'])) {
            return $data['symbols'];
        }

        return [
            'INR' => '₹', 'USD' => '$', 'EUR' => '€', 'GBP' => '£', 'AED' => 'د.إ',
            'CAD' => 'CA$', 'AUD' => 'A$', 'SGD' => 'S$', 'JPY' => '¥', 'CHF' => 'CHF',
            'NZD' => 'NZ$', 'ZAR' => 'R',
        ];
    }

    /**
     * Get currency symbol for any currency code (served from JSON with enterprise meta fallback).
     */
    public static function getCurrencySymbol(?string $code): string
    {
        if (empty($code)) {
            return '$';
        }
        $code = strtoupper(trim($code));
        $data = self::loadCurrencyJson();
        if (isset($data['symbols'][$code])) {
            return $data['symbols'][$code];
        }
        $meta = GeneralMethod::currencyEnterpriseMeta($code);
        if (! empty($meta['symbol']) && $meta['symbol'] !== $code) {
            return $meta['symbol'];
        }

        return $code.' ';
    }

    /**
     * Get all currencies with complete UI metadata (code, name, symbol, flag, search keywords).
     * Served directly from static JSON (0% SQL queries).
     *
     * @return array<int, array>
     */
    public static function getAllCurrenciesWithMeta(): array
    {
        $data = self::loadCurrencyJson();
        if (! empty($data['setting_currencies'])) {
            return $data['setting_currencies'];
        }

        return [];
    }

    /**
     * Get unified enterprise currency list (113 unique currencies) matching Company Profile -> Settings.
     * Served directly from static JSON with 0% MySQL database strain.
     *
     * @return array<string, array{key: int, code: string, name: string, symbol: string, flag: string, label: string, keywords: string, countries: array<string>}>
     */
    public static function getSettingCurrenciesList(): array
    {
        $data = self::loadCurrencyJson();
        if (! empty($data['setting_currencies'])) {
            return $data['setting_currencies'];
        }

        return [];
    }

    /**
     * Automatically determine the default currency for a country name, ISO2 code, or phone dial code.
     *
     * @return string ISO-3 currency code (e.g. 'INR', 'USD', 'EUR')
     */
    public static function getDefaultCurrencyForCountry(?string $countryOrCode): string
    {
        if (empty($countryOrCode)) {
            return self::DEFAULT_CURRENCY;
        }

        $input = trim($countryOrCode);
        $upper = strtoupper($input);

        // 1. Direct ISO-2 lookup
        if (isset(self::COUNTRY_TO_CURRENCY[$upper])) {
            return self::COUNTRY_TO_CURRENCY[$upper];
        }

        // 2. Strip dial code prefix (+91 -> 91)
        $cleanDial = ltrim($input, '+');
        if (isset(self::CALLING_CODE_TO_CURRENCY[$cleanDial])) {
            return self::CALLING_CODE_TO_CURRENCY[$cleanDial];
        }

        // 3. Match against countries table (cached 24h)
        $detected = Cache::remember('geo_curr_detect:'.md5(strtolower($input)), self::TTL_METADATA, function () use ($input) {
            $cleanInput = trim($input);
            $country = DB::table('countries')
                ->where('country_name', 'LIKE', $cleanInput)
                ->orWhere('country_code', $cleanInput)
                ->first();

            if ($country && ! empty($country->currency)) {
                $curr = strtoupper(trim($country->currency));
                // Verify the currency exists in our system currencies
                $validCurrencies = array_values(self::getCurrencies());
                if (in_array($curr, $validCurrencies, true)) {
                    return $curr;
                }
            }

            // Check if country name contains common country markers
            $lower = strtolower($cleanInput);
            if (str_contains($lower, 'india') || str_contains($lower, 'bharat')) {
                return 'INR';
            }
            if (str_contains($lower, 'united states') || str_contains($lower, 'usa') || str_contains($lower, 'america')) {
                return 'USD';
            }
            if (str_contains($lower, 'united kingdom') || str_contains($lower, 'england') || str_contains($lower, 'britain') || str_contains($lower, 'scotland') || str_contains($lower, 'wales')) {
                return 'GBP';
            }
            if (str_contains($lower, 'emirates') || str_contains($lower, 'dubai') || str_contains($lower, 'uae') || str_contains($lower, 'abu dhabi')) {
                return 'AED';
            }
            if (str_contains($lower, 'germany') || str_contains($lower, 'france') || str_contains($lower, 'italy') || str_contains($lower, 'spain') || str_contains($lower, 'netherlands') || str_contains($lower, 'ireland')) {
                return 'EUR';
            }
            if (str_contains($lower, 'canada')) {
                return 'CAD';
            }
            if (str_contains($lower, 'australia')) {
                return 'AUD';
            }
            if (str_contains($lower, 'singapore')) {
                return 'SGD';
            }
            if (str_contains($lower, 'japan')) {
                return 'JPY';
            }

            return null;
        });

        return $detected ?: self::DEFAULT_CURRENCY;
    }

    /**
     * Get default currency integer key for a country.
     */
    public static function getDefaultCurrencyKeyForCountry(?string $countryOrCode): int
    {
        $code = self::getDefaultCurrencyForCountry($countryOrCode);

        return self::resolveCurrencyKey($code);
    }

    /**
     * Get the tenant's default platform currency ISO code.
     *
     * @return string (e.g. 'USD', 'INR', 'EUR')
     */
    public static function getTenantCurrency(?int $companyId = null): string
    {
        if (empty($companyId)) {
            if (auth()->check()) {
                $companyId = (int) auth()->user()->company_id;
            }
        }

        if (empty($companyId)) {
            return self::DEFAULT_CURRENCY;
        }

        return Cache::remember("tenant_default_currency:{$companyId}", self::TTL_TENANT, function () use ($companyId) {
            $company = DB::table('companies')
                ->select('currency', 'calling_code')
                ->where('id', $companyId)
                ->first();

            if ($company && ! empty($company->currency)) {
                $curr = $company->currency;
                if (is_numeric($curr)) {
                    $key = (int) $curr;
                    // Intent: Check master_currency first since Company Profile -> Settings saves master_currency.id
                    $data = self::loadCurrencyJson();
                    if (isset($data['master_id_to_code'][$key])) {
                        return $data['master_id_to_code'][$key];
                    }
                }

                return self::resolveCurrencyCode($curr);
            }

            // Fallback: check company_career_settings table
            $careerCurrency = DB::table('company_career_settings')
                ->where('company_id', $companyId)
                ->value('currency');
            if (! empty($careerCurrency)) {
                return strtoupper(trim($careerCurrency));
            }

            // Fallback: HQ Calling Code Auto-Detection
            if ($company && ! empty($company->calling_code)) {
                return self::getDefaultCurrencyForCountry((string) $company->calling_code);
            }

            return self::DEFAULT_CURRENCY;
        });
    }

    /**
     * Get the tenant's default platform currency integer key (`getKey`).
     */
    public static function getTenantCurrencyKey(?int $companyId = null): int
    {
        if (empty($companyId) && auth()->check()) {
            $companyId = (int) auth()->user()->company_id;
        }

        if (empty($companyId)) {
            return self::DEFAULT_CURRENCY_KEY;
        }

        return (int) Cache::remember("tenant_default_currency_key:{$companyId}", self::TTL_TENANT, function () use ($companyId) {
            $code = self::getTenantCurrency($companyId);

            return self::resolveCurrencyKey($code);
        });
    }

    /**
     * Get the tenant's default platform currency symbol.
     */
    public static function getTenantCurrencySymbol(?int $companyId = null): string
    {
        $code = self::getTenantCurrency($companyId);
        $symbols = self::getCurrencySymbols();

        return $symbols[$code] ?? '$';
    }

    /**
     * Set tenant platform currency and synchronize across all settings.
     *
     * @param  string|int  $currency  Code ('USD') or getKey (4) or master_currency id
     */
    public static function setTenantCurrency(int $companyId, string|int $currency): void
    {
        $code = null;
        if (is_numeric($currency)) {
            $data = self::loadCurrencyJson();
            $num = (int) $currency;
            if (isset($data['master_id_to_code'][$num])) {
                $code = $data['master_id_to_code'][$num];
            }
        }
        if (! $code) {
            $code = self::resolveCurrencyCode($currency);
        }
        $code = strtoupper(trim($code));
        $key = self::resolveCurrencyKey($code);
        $companyCurrencyVal = is_numeric($currency) ? (int) $currency : $code;

        DB::table('companies')
            ->where('id', $companyId)
            ->update([
                'currency' => $companyCurrencyVal,
                'updated_at' => now(),
            ]);

        // Sync company_career_settings if table exists
        try {
            DB::table('company_career_settings')
                ->updateOrInsert(
                    ['company_id' => $companyId],
                    ['currency' => $code, 'updated_at' => now()]
                );
        } catch (\Throwable $e) {
            Log::warning('Could not sync company_career_settings currency: '.$e->getMessage());
        }

        // Sync default commercials salary_currency for Contract (1) and Fulltime (2)
        try {
            DB::table('commericials')
                ->where('company_id', $companyId)
                ->whereIn('commtype', [1, 2])
                ->update(['salary_currency' => $key]);
        } catch (\Throwable $e) {
            Log::warning('Could not sync commercials salary_currency: '.$e->getMessage());
        }

        // Invalidate tenant currency caches
        self::invalidateTenantCache($companyId);
    }

    /**
     * Multi-tenant atomic cache invalidation for currency keys.
     */
    public static function invalidateTenantCache(int $companyId): void
    {
        Cache::forget("tenant_default_currency:{$companyId}");
        Cache::forget("tenant_default_currency_key:{$companyId}");
        Cache::forget("tenant_default_currency_symbol:{$companyId}");
        Cache::forget("tenant_currency_code:{$companyId}");
        Cache::forget("tenant:{$companyId}:currency_bench_show_mode");
        Cache::forget("tenant_defaults_payload:{$companyId}");
        Cache::forget("company_career_settings:{$companyId}");
        Cache::forget("tenant_career_meta:{$companyId}");
        Cache::forget("account_settings:company:{$companyId}");
    }

    /**
     * Resolve any currency representation (key or code) to uppercase ISO code.
     * Serves directly from pre-indexed JSON dictionaries (0% SQL queries).
     */
    public static function resolveCurrencyCode(string|int|null $currency): string
    {
        if ($currency === null || $currency === '') {
            return self::DEFAULT_CURRENCY;
        }

        $data = self::loadCurrencyJson();

        if (is_numeric($currency)) {
            $key = (int) $currency;
            if (isset($data['key_to_code'][$key])) {
                return $data['key_to_code'][$key];
            }
            if ($key >= 1000 && isset($data['key_to_code'][$key - 1000])) {
                return $data['key_to_code'][$key - 1000];
            }

            return self::DEFAULT_CURRENCY;
        }

        $clean = strtoupper(trim((string) $currency));
        if (isset($data['code_to_key'][$clean])) {
            return $clean;
        }

        return ! empty($clean) ? $clean : self::DEFAULT_CURRENCY;
    }

    /**
     * Resolve any currency representation (key or code) to integer getKey.
     * Serves directly from pre-indexed JSON dictionaries (0% SQL queries).
     */
    public static function resolveCurrencyKey(string|int|null $currency): int
    {
        if ($currency === null || $currency === '') {
            return self::DEFAULT_CURRENCY_KEY;
        }

        $data = self::loadCurrencyJson();

        if (is_numeric($currency)) {
            $num = (int) $currency;
            if (isset($data['key_to_code'][$num])) {
                return $num;
            }

            return $num;
        }

        $code = strtoupper(trim((string) $currency));
        if (isset($data['code_to_key'][$code])) {
            return (int) $data['code_to_key'][$code];
        }

        return self::DEFAULT_CURRENCY_KEY;
    }

    /**
     * Universal, high-precision cross-currency conversion using USD as pivot.
     * Formula: Converted = (Amount / RateFromUSD) * RateToUSD
     */
    public static function convert(float|int|null $amount, string|int|null $fromCurrency, string|int|null $toCurrency = 'USD', ?string $version = null): float
    {
        if ($amount === null || ! is_numeric($amount)) {
            return 0.0;
        }
        $amount = (float) $amount;
        if ($amount == 0.0) {
            return 0.0;
        }

        $from = self::resolveCurrencyCode($fromCurrency);
        $to = self::resolveCurrencyCode($toCurrency);

        if ($from === $to) {
            return $amount;
        }

        $snapshot = GeneralMethod::getLatestFxSnapshot($version);
        $rates = $snapshot['rates_json'] ?? [];

        $rateFrom = isset($rates[$from]) ? (float) $rates[$from] : null;
        $rateTo = isset($rates[$to]) ? (float) $rates[$to] : null;

        if ($rateFrom && $rateTo && $rateFrom > 0 && $rateTo > 0) {
            return ($amount / $rateFrom) * $rateTo;
        }

        // Fallback: check currencies.json rates directly (0% SQL queries)
        $data = self::loadCurrencyJson();
        $rfFrom = $data['rates_to_usd'][$from] ?? null;
        $rfTo = $data['rates_to_usd'][$to] ?? null;
        if ($rfFrom && $rfTo && $rfFrom > 0 && $rfTo > 0) {
            return ($amount / $rfFrom) * $rfTo;
        }

        $ratesFallback = GeneralMethod::currencyConverter()['usd_based_rates'] ?? [];
        $rfFrom = isset($ratesFallback[$from]) ? (float) $ratesFallback[$from] : 1.0;
        $rfTo = isset($ratesFallback[$to]) ? (float) $ratesFallback[$to] : 1.0;
        if ($rfFrom > 0 && $rfTo > 0) {
            return ($amount / $rfFrom) * $rfTo;
        }

        return $amount;
    }

    /**
     * High-precision currency conversion with full audit trail metadata.
     */
    public static function convertWithAudit(float|int|null $amount, string|int|null $fromCurrency, string|int|null $toCurrency = 'USD', ?string $version = null): array
    {
        $from = self::resolveCurrencyCode($fromCurrency);
        $to = self::resolveCurrencyCode($toCurrency);
        $snapshot = GeneralMethod::getLatestFxSnapshot($version);
        $rates = $snapshot['rates_json'] ?? [];

        $rateFrom = isset($rates[$from]) ? (float) $rates[$from] : 1.0;
        $rateTo = isset($rates[$to]) ? (float) $rates[$to] : 1.0;

        $effectiveRate = ($rateFrom > 0) ? ($rateTo / $rateFrom) : 1.0;
        $converted = ($amount !== null && is_numeric($amount)) ? ((float) $amount * $effectiveRate) : 0.0;

        return [
            'original_amount' => (float) $amount,
            'converted_amount' => round($converted, 4),
            'from_currency' => $from,
            'to_currency' => $to,
            'exchange_rate' => round($effectiveRate, 6),
            'snapshot_version' => $snapshot['version'] ?? 'active',
            'effective_from' => $snapshot['effective_from'] ?? now()->toIso8601String(),
            'calculated_at' => now()->toIso8601String(),
        ];
    }

    /**
     * Get the authoritative currency code for a given job or job ID.
     *
     * @param  object|array|int|string|null  $jobOrId
     * @return string (e.g. 'USD', 'INR', 'AED')
     */
    public static function getJobCurrencyCode($jobOrId): string
    {
        if (empty($jobOrId)) {
            return self::getTenantCurrency();
        }

        if (is_numeric($jobOrId)) {
            $jobId = (int) $jobOrId;

            return Cache::remember("job_currency_code:{$jobId}", self::TTL_METADATA, function () use ($jobId) {
                $job = DB::table('jobs')
                    ->select('id', 'company_id', 'min_salary_currency', 'max_salary_currency')
                    ->where('id', $jobId)
                    ->first();

                if (! $job) {
                    return self::getTenantCurrency();
                }

                $currencyKey = $job->min_salary_currency ?? ($job->max_salary_currency ?? null);
                if ($currencyKey !== null && $currencyKey !== '') {
                    return self::resolveCurrencyCode($currencyKey);
                }

                return self::getTenantCurrency($job->company_id ?? null);
            });
        }

        if (is_array($jobOrId)) {
            $jobOrId = (object) $jobOrId;
        }

        $currencyKey = $jobOrId->min_salary_currency ?? ($jobOrId->max_salary_currency ?? null);
        if ($currencyKey !== null && $currencyKey !== '') {
            return self::resolveCurrencyCode($currencyKey);
        }

        $companyId = $jobOrId->company_id ?? null;

        return self::getTenantCurrency($companyId);
    }

    /**
     * Get the authoritative currency integer key for a given job or job ID.
     *
     * @param  object|array|int|string|null  $jobOrId
     */
    public static function getJobCurrencyKey($jobOrId): int
    {
        $code = self::getJobCurrencyCode($jobOrId);

        return self::resolveCurrencyKey($code);
    }

    /**
     * Get the currency symbol for a given job or job ID.
     *
     * @param  object|array|int|string|null  $jobOrId
     */
    public static function getJobCurrencySymbol($jobOrId): string
    {
        $code = self::getJobCurrencyCode($jobOrId);
        $symbols = self::getCurrencySymbols();

        return $symbols[$code] ?? '$';
    }

    /**
     * Get comprehensive authoritative metadata for a given job or job ID.
     *
     * @param  object|array|int|string|null  $jobOrId
     * @return array{code: string, key: int, symbol: string, flag: string, name: string}
     */
    public static function getJobCurrencyMeta($jobOrId): array
    {
        $code = self::getJobCurrencyCode($jobOrId);
        $key = self::resolveCurrencyKey($code);
        $symbol = self::getJobCurrencySymbol($jobOrId);
        $flag = self::CURRENCY_FLAGS[$code] ?? '🌐';
        $meta = GeneralMethod::currencyEnterpriseMeta($code);

        return [
            'code' => $code,
            'key' => $key,
            'symbol' => $symbol,
            'flag' => $flag,
            'name' => $meta['name'] ?? $code,
            'label' => $meta['label'] ?? "$code ($symbol)",
        ];
    }

    /**
     * Convert candidate's compensation to the job's currency on-the-fly.
     * Never mutates candidate's original compensation in the database.
     *
     * @param  object|array|null  $candidate
     * @param  object|array|null  $job
     */
    public static function convertCandidateToJobCurrency($candidate, $job): array
    {
        if (is_array($candidate)) {
            $candidate = (object) $candidate;
        }
        if (is_array($job)) {
            $job = (object) $job;
        }

        $jobCode = self::getJobCurrencyCode($job);
        $symbols = self::getCurrencySymbols();
        $jobSymbol = $symbols[$jobCode] ?? '$';

        $origCtc = (float) ($candidate->ctc ?? 0);
        $origCtcCurKey = $candidate->current_ctc_cur ?? null;
        $origCtcCode = self::resolveCurrencyCode($origCtcCurKey);
        if ($origCtc <= 0 && ! empty($candidate->ctc_yearly)) {
            $origCtc = (float) $candidate->ctc_yearly;
            $origCtcCode = 'INR';
        }
        $origCtcSymbol = $symbols[$origCtcCode] ?? $origCtcCode;

        $origEctc = (float) ($candidate->ectc ?? 0);
        $origEctcCurKey = $candidate->current_ectc_cur ?? $origCtcCurKey;
        $origEctcCode = self::resolveCurrencyCode($origEctcCurKey);
        if ($origEctc <= 0 && ! empty($candidate->ectc_yearly)) {
            $origEctc = (float) $candidate->ectc_yearly;
            $origEctcCode = 'INR';
        }
        $origEctcSymbol = $symbols[$origEctcCode] ?? $origEctcCode;

        $convertedCtc = self::convert($origCtc, $origCtcCode, $jobCode);
        $convertedEctc = self::convert($origEctc, $origEctcCode, $jobCode);

        $isConverted = ($origEctcCode !== $jobCode) || ($origCtcCode !== $jobCode);

        // Format dual compensation strings
        $formattedCtc = self::formatDualCompensationString($convertedCtc, $jobCode, $jobSymbol, $origCtc, $origCtcCode, $origCtcSymbol);
        $formattedEctc = self::formatDualCompensationString($convertedEctc, $jobCode, $jobSymbol, $origEctc, $origEctcCode, $origEctcSymbol);

        return [
            'job_currency' => $jobCode,
            'job_currency_symbol' => $jobSymbol,
            'is_converted' => $isConverted,
            'original_ctc' => $origCtc,
            'original_ctc_currency' => $origCtcCode,
            'original_ctc_symbol' => $origCtcSymbol,
            'converted_ctc' => round($convertedCtc, 2),
            'formatted_ctc' => $formattedCtc,
            'original_ectc' => $origEctc,
            'original_ectc_currency' => $origEctcCode,
            'original_ectc_symbol' => $origEctcSymbol,
            'converted_ectc' => round($convertedEctc, 2),
            'formatted_ectc' => $formattedEctc,
        ];
    }

    /**
     * Evaluate candidate expected compensation against job budget in common currency.
     * Normalizes both values to annual basis in the job's currency.
     *
     * @param  object|array|null  $job
     * @param  object|array|null  $candidate
     */
    public static function evaluateBudgetMatch($job, $candidate): array
    {
        if (is_array($job)) {
            $job = (object) $job;
        }
        if (is_array($candidate)) {
            $candidate = (object) $candidate;
        }

        if (! $job || ! $candidate) {
            return [
                'under_budget' => false,
                'status' => 'not_specified',
                'job_max' => 0.0,
                'candidate_ectc_in_job_currency' => 0.0,
                'ratio' => 0.0,
                'score' => 0.8,
            ];
        }

        $jobCurrCode = self::getJobCurrencyCode($job);

        // 1. Determine job maximum annual budget in job currency
        $jobMaxAnnual = (float) ($job->max_salary ?? 0);
        $jobMode = (int) ($job->max_sal_mode ?? ($job->min_sal_mode ?? 0));
        // Normalize mode to annual: 0 = annual, 1 = monthly (*12), 2 = hourly (*1920)
        if ($jobMode === 1) {
            $jobMaxAnnual *= 12.0;
        } elseif ($jobMode === 2) {
            $jobMaxAnnual *= 1920.0;
        }

        // 2. Determine candidate annual ECTC in candidate's original currency
        $candEctc = (float) ($candidate->ectc ?? 0);
        $candCurKey = $candidate->current_ectc_cur ?? ($candidate->current_ctc_cur ?? null);
        $candCurCode = self::resolveCurrencyCode($candCurKey);
        $candMode = (int) ($candidate->current_ectc_mode ?? ($candidate->current_ctc_mode ?? 0));

        // Normalize candidate mode to annual
        $candAnnual = $candEctc;
        if ($candMode === 1) {
            $candAnnual *= 12.0;
        } elseif ($candMode === 2) {
            $candAnnual *= 1920.0;
        }

        // Fallback: if candidate ectc is 0, check ectc_yearly (which is indexed in INR)
        if ($candAnnual <= 0 && ! empty($candidate->ectc_yearly)) {
            $candAnnual = (float) $candidate->ectc_yearly;
            $candCurCode = 'INR';
        }

        if ($jobMaxAnnual <= 0 || $candAnnual <= 0) {
            return [
                'under_budget' => true,
                'status' => 'not_specified',
                'job_max' => $jobMaxAnnual,
                'candidate_ectc_in_job_currency' => $candAnnual,
                'ratio' => 1.0,
                'score' => 0.8,
                'job_currency' => $jobCurrCode,
            ];
        }

        // Convert candidate annual to job currency
        $candAnnualInJobCurr = self::convert($candAnnual, $candCurCode, $jobCurrCode);

        $underBudget = ($candAnnualInJobCurr <= $jobMaxAnnual);
        $ratio = ($jobMaxAnnual > 0) ? ($candAnnualInJobCurr / $jobMaxAnnual) : 1.0;

        if ($underBudget) {
            $score = 1.0;
            $status = 'within_budget';
        } else {
            $score = max(0.0, 1.0 - (($ratio - 1.0) * 2.0));
            $status = 'over_budget';
        }

        return [
            'under_budget' => $underBudget,
            'status' => $status,
            'job_max' => round($jobMaxAnnual, 2),
            'candidate_ectc_in_job_currency' => round($candAnnualInJobCurr, 2),
            'job_currency' => $jobCurrCode,
            'ratio' => round($ratio, 3),
            'score' => round($score, 3),
        ];
    }

    /**
     * Format a dual-currency string showing converted amount with original amount if different.
     * E.g. "$120,000 (₹1,00,00,000)" or "₹20,00,000".
     */
    protected static function formatDualCompensationString(float $convertedAmount, string $jobCurr, string $jobSymbol, float $origAmount, string $origCurr, string $origSymbol): string
    {
        if ($convertedAmount <= 0 && $origAmount <= 0) {
            return '—';
        }

        $formattedConverted = $jobSymbol.GeneralMethod::thousandsCurrencyFormat($convertedAmount);

        if ($jobCurr === $origCurr || $origAmount <= 0) {
            return $formattedConverted;
        }

        $formattedOrig = $origSymbol.GeneralMethod::thousandsCurrencyFormat($origAmount);

        return "{$formattedConverted} ({$formattedOrig})";
    }

    /**
     * High-precision salary view formatter replacing legacy formatViewSalary with FX snapshot.
     *
     * @param  float|int|null  $amountINR
     * @param  string|int|null  $targetCurrency
     * @param  int  $mode  (0 = yearly, 1 = monthly, 2 = hourly)
     */
    public static function formatViewSalary($amountINR, $targetCurrency, int $mode = 0): object
    {
        $HOURS_PER_MONTH = 160;
        $MONTHS_PER_YEAR = 12;

        $targetCode = self::resolveCurrencyCode($targetCurrency);
        $targetKey = self::resolveCurrencyKey($targetCurrency);
        $symbols = self::getCurrencySymbols();
        $symbol = $symbols[$targetCode] ?? '₹';

        $amount = self::convert($amountINR, 'INR', $targetCode);

        $yearly = $amount;
        $monthly = $yearly / $MONTHS_PER_YEAR;
        $hourly = $monthly / $HOURS_PER_MONTH;

        $value = match ($mode) {
            1 => $monthly,
            2 => $hourly,
            default => $yearly,
        };

        return (object) [
            'amount' => number_format($value, 2, '.', ''),
            'symbol' => $symbol,
            'currency' => $targetKey,
            'currency_code' => $targetCode,
            'mode' => $mode,
        ];
    }
}
