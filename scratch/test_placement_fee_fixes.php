<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\DTO\Commercials\ServiceTermDTO;
use App\Helper\GeneralMethod;

echo "=== VERIFYING PLACEMENT FEE FIXES (ALL 5 POINTS) ===\n\n";

$testsPassed = 0;
$testsFailed = 0;

function assertCheck($condition, $description) {
    global $testsPassed, $testsFailed;
    if ($condition) {
        echo " [PASS] {$description}\n";
        $testsPassed++;
    } else {
        echo " [FAIL] {$description}\n";
        $testsFailed++;
    }
}

// -------------------------------------------------------------
// POINT 1 & 2: DTO Verification for Fee Types & Basis
// -------------------------------------------------------------

// Percentage Model
$dtoPercentage = ServiceTermDTO::fromArray([
    'id' => 1,
    'company_id' => 1,
    'commtype' => 2,
    'rateType' => 2,
    'sal_mode' => 0,
]);
assertCheck(
    $dtoPercentage->rateTypeLabel === 'Percentage of CTC' && $dtoPercentage->salModeLabel === 'Annual CTC',
    "Percentage Placement Fee -> RateTypeLabel: '{$dtoPercentage->rateTypeLabel}', Basis: '{$dtoPercentage->salModeLabel}' (Annual CTC)"
);

// Fixed Model
$dtoFixed = ServiceTermDTO::fromArray([
    'id' => 1,
    'company_id' => 1,
    'commtype' => 2,
    'rateType' => 1,
    'sal_mode' => 3,
]);
assertCheck(
    $dtoFixed->rateTypeLabel === 'Fixed Placement Fee' && $dtoFixed->salModeLabel === 'Per Hire',
    "Fixed Placement Fee -> RateTypeLabel: '{$dtoFixed->rateTypeLabel}', Basis: '{$dtoFixed->salModeLabel}' (Per Hire)"
);

// Free Model
$dtoFree = ServiceTermDTO::fromArray([
    'id' => 1,
    'company_id' => 1,
    'commtype' => 2,
    'rateType' => 0,
    'sal_mode' => 0,
]);
assertCheck(
    $dtoFree->rateTypeLabel === 'Free / No Placement Fee' && $dtoFree->salModeLabel === 'Not Applicable',
    "Free Model -> RateTypeLabel: '{$dtoFree->rateTypeLabel}', Basis: '{$dtoFree->salModeLabel}' (Not Applicable)"
);

// -------------------------------------------------------------
// POINT 5: GeneralMethod::defaultCountryId for Tenant
// -------------------------------------------------------------
$registeredCountryId = GeneralMethod::defaultCountryId(1);
assertCheck(
    $registeredCountryId === 101,
    "Tenant Company 1 registered country ID accurately resolved to 101 (India)"
);

// -------------------------------------------------------------
// POINT 1, 2, 3, 4, 5: Blade View Content Verification
// -------------------------------------------------------------
// Mock Auth user
$user = \App\Models\User::where('company_id', 1)->first() ?: new \App\Models\User(['company_id' => 1]);
auth()->login($user);

// Render Fulltime View with empty countries to test auto-selection
$viewData = [
    'sectionCheck' => 2, // Fulltime
    'commericials' => [
        [
            'id' => 1,
            'company_id' => 1,
            'rateType' => 1, // Fixed Placement Fee
            'commtype' => 2,
            'sal_mode' => 3,
            'salary_currency' => 4,
            'commericial_countries' => [], // Empty! Should trigger auto-selection
            'commericial_details' => [
                ['exp' => 0, 'rate' => 8.33], // Legacy fallback rate, should become 10,000 for Fixed
            ]
        ]
    ],
    'countries' => \Illuminate\Support\Facades\DB::table('countries')->whereIn('id', [1, 2, 101])->get(),
    'defaultCurrencyKey' => 4,
    'currencyList' => \App\Services\CurrencyService::getSettingCurrenciesList(),
];

$html = view('service_company.jobs.services_view', $viewData)->render();

// Point 1: Pink CSS Treatment check
assertCheck(
    str_contains($html, '#fff5f7') && str_contains($html, '#f43f5e'),
    "Blade contains subtle Pink styling (#fff5f7, #f43f5e) for Free / No Placement Fee"
);

// Point 2: Per Hire basis present and Annual CTC removed for Fixed fee
assertCheck(
    str_contains($html, '<option value="3" selected>Per Hire</option>'),
    "Blade has '<option value=\"3\" selected>Per Hire</option>' default selected on Fixed Placement Fee"
);
// Verify select options in HTML do NOT contain Annual CTC on Fixed fee
$selectHtml = preg_match('/<select id="commercial_salary_mode"[^>]*>(.*?)<\/select>/s', $html, $m) ? $m[1] : '';
assertCheck(
    !str_contains($selectHtml, 'Annual CTC') && str_contains($selectHtml, '<option value="3" selected>Per Hire</option>'),
    "Subcard select contains strictly 'Per Hire' and does NOT contain 'Annual CTC'"
);

// Point 2b: Seniority Matrix selects in Card 2 contain strictly 'Per Hire' and NOT 'Annual CTC'
preg_match_all('/<select[^>]*data-range-applied-on[^>]*>(.*?)<\/select>/s', $html, $rangeSelectMatches);
$allMatrixArePerHireOnly = count($rangeSelectMatches[1]) > 0 && collect($rangeSelectMatches[1])->every(function ($opt) {
    return str_contains($opt, '<option value="3" selected>Per Hire</option>') && !str_contains($opt, 'Annual CTC');
});
assertCheck(
    $allMatrixArePerHireOnly,
    "Seniority-Based Placement Fee Matrix (Card 2) is strictly 'Per Hire' only by default with Annual CTC removed"
);

// Point 3: Default amount is 10000 for Fixed Placement Fee
assertCheck(
    str_contains($html, 'value="10000"'),
    "Blade defaulted fixed fee rate to 10000.00 instead of 8.33"
);

// Point 3b: Seniority Matrix rows in Card 2 all have default amount 10000
preg_match_all('/<input[^>]*data-range-rate[^>]*value="([^"]*)"/s', $html, $matrixRateMatches);
$allMatrixRatesAre10k = count($matrixRateMatches[1]) > 0 && collect($matrixRateMatches[1])->every(function ($r) {
    return (float) $r === 10000.00;
});
assertCheck(
    $allMatrixRatesAre10k,
    "Seniority-Based Placement Fee Matrix (Card 2) tiers all default to 10,000 for Fixed Placement Fee"
);

// Point 4: Refresh icon button removed from Card 2
assertCheck(
    !str_contains($html, 'cd-tier-reset-btn') && !str_contains($html, 'data-range-reset'),
    "Blade completely removed refresh icon and reset button from Card 2"
);

// Point 5: Auto-select Services Country from tenant address
assertCheck(
    str_contains($html, 'id="countrySelectedHeaderBadge">1 Country Enabled</span>') || str_contains($html, '1 Countries Enabled') || str_contains($html, '1 Country Enabled'),
    "Blade auto-selected registered tenant country, showing 1 Country Enabled badge"
);

// Verify tenant country (India ID 101) checkbox is checked
$indiaChecked = (bool) preg_match('/id="commercial_country_2_101"[^>]*checked/i', $html);
assertCheck(
    $indiaChecked,
    "Blade rendered tenant registered country (India, ID 101) as checked"
);

echo "\n=========================================\n";
echo "SUMMARY: {$testsPassed} PASSED, {$testsFailed} FAILED\n";
echo "=========================================\n";

if ($testsFailed > 0) {
    exit(1);
}
