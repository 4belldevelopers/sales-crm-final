<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\CurrencyService;
use App\Services\ReferenceDataCache;
use Illuminate\Support\Facades\DB;

echo "=== ZERO SQL CURRENCY VERIFICATION ===\n";

DB::flushQueryLog();
DB::enableQueryLog();

// 1. Setting currencies count
$settingCurrencies = CurrencyService::getSettingCurrenciesList();
echo "1. Setting currencies count: " . count($settingCurrencies) . " (Expected: 113)\n";

// 2. Currencies array count
$currencies = CurrencyService::getCurrencies();
echo "2. Currencies array count: " . count($currencies) . " (Expected >= 12)\n";

// 3. Code resolutions
echo "3. Code resolutions:\n";
echo "   - Input: 0 => Output: " . CurrencyService::resolveCurrencyCode(0) . "\n";
echo "   - Input: 4 => Output: " . CurrencyService::resolveCurrencyCode(4) . "\n";
echo "   - Input: 22 => Output: " . CurrencyService::resolveCurrencyCode(22) . "\n";
echo "   - Input: 1001 => Output: " . CurrencyService::resolveCurrencyCode(1001) . "\n";
echo "   - Input: CAD => Output: " . CurrencyService::resolveCurrencyCode('CAD') . "\n";

// 4. Key resolutions
echo "4. Key resolutions:\n";
echo "   - Input: INR => Output: " . CurrencyService::resolveCurrencyKey('INR') . "\n";
echo "   - Input: USD => Output: " . CurrencyService::resolveCurrencyKey('USD') . "\n";
echo "   - Input: CAD => Output: " . CurrencyService::resolveCurrencyKey('CAD') . "\n";

// 5. Master currencies collection count
$master = ReferenceDataCache::masterCurrencies();
echo "5. Master currencies collection count: " . $master->count() . " (Expected: 132)\n";

// 6. 100 CAD in INR
$cadToInr = CurrencyService::convert(100, 'CAD', 'INR');
echo "6. 100 CAD in INR: " . $cadToInr . "\n";

// 7. Total SQL queries
$queryCount = count(DB::getQueryLog());
echo "7. Total SQL queries executed during all currency lookups: " . $queryCount . "\n\n";

if ($queryCount === 0) {
    echo ">>> SUCCESS: 100% ZERO SQL QUERIES CONFIRMED! <<<\n";
    exit(0);
} else {
    echo ">>> FAILED: " . $queryCount . " SQL queries were executed! <<<\n";
    print_r(DB::getQueryLog());
    exit(1);
}
