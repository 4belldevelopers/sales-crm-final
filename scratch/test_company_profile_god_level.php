<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = \Illuminate\Http\Request::capture()
);

echo "========================================================================\n";
echo "TESTING GOD-LEVEL COMPANY PROFILE OPTIMIZATIONS\n";
echo "========================================================================\n\n";

// 1. Test Static Reference Cache
echo "1. Checking Static Reference Caches:\n";
$cat = cache()->get('global_job_categories_all');
$ind = cache()->get('global_td_industries_all');
$carr = cache()->get('global_country_arr_list');
$statelist = cache()->get('global_state_list_101');
echo "   - Job categories cached: " . (is_countable($cat) ? count($cat) : "empty (will populate on first load)") . "\n";
echo "   - Industries cached: " . (is_countable($ind) ? count($ind) : "empty (will populate on first load)") . "\n";
echo "   - Country list cached: " . (is_countable($carr) ? count($carr) : "empty (will populate on first load)") . "\n";
echo "   - State list cached: " . (is_countable($statelist) ? count($statelist) : "empty (will populate on first load)") . "\n";

// 2. Test Blade Views for Readiness Modal Parity
echo "\n2. Verifying Blade Readiness Gate Fix:\n";
$editBlade = file_get_contents("resources/views/service_company/account/edit.blade.php");
$onboardingBlade = file_get_contents("resources/views/service_company/account/onboarding.blade.php");

$hasEditMarkSuccessDirect = (strpos($editBlade, "Authoritative synchronous completion") !== false && strpos($editBlade, "markSuccess();") !== false);
$hasOnboardingMarkSuccessDirect = (strpos($onboardingBlade, "Authoritative synchronous completion") !== false && strpos($onboardingBlade, "markSuccess();") !== false);

echo "   - account/edit.blade.php has synchronous markSuccess fallback: " . ($hasEditMarkSuccessDirect ? "PASS" : "FAIL") . "\n";
echo "   - account/onboarding.blade.php has synchronous markSuccess fallback: " . ($hasOnboardingMarkSuccessDirect ? "PASS" : "FAIL") . "\n";

// Check that 'Internal Client' text was removed from company edit/onboarding
$hasBogusClientTextInEdit = (strpos($editBlade, "Finalizing client record") !== false || strpos($editBlade, "Your internal client is 100% indexed") !== false);
echo "   - account/edit.blade.php has clean company wording: " . (!$hasBogusClientTextInEdit ? "PASS" : "FAIL") . "\n";

// 3. Test Controller Code Parity
echo "\n3. Controller Code Parity Verification:\n";
$ctrl = file_get_contents("app/Http/Controllers/ServiceCompany/ServiceCompanyAccountSettingsController.php");

$hasValidation = (strpos($ctrl, "validateCompanySubmission") !== false);
$hasBulkLocationInsert = (strpos($ctrl, "DB::table('client_location')->insert(\$locationsToInsert)") !== false);
$hasBulkCommercialInsert = (strpos($ctrl, "DB::table('commericial_countries')->insert(\$commCountries1)") !== false);
$hasCachedCityLookup = (strpos($ctrl, "city_lookup_") !== false);

echo "   - validateCompanySubmission present: " . ($hasValidation ? "PASS" : "FAIL") . "\n";
echo "   - Bulk client_location insert present: " . ($hasBulkLocationInsert ? "PASS" : "FAIL") . "\n";
echo "   - Bulk commercial_countries insert present: " . ($hasBulkCommercialInsert ? "PASS" : "FAIL") . "\n";
echo "   - Cached city lookup present: " . ($hasCachedCityLookup ? "PASS" : "FAIL") . "\n";

echo "\n=== ALL VERIFICATIONS COMPLETE ===\n";
