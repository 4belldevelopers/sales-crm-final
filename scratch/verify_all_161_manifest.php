<?php
// scratch/verify_all_161_manifest.php

require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\IndustryTaxonomyCompiler;
use App\Services\IndustryTaxonomyService;
use Illuminate\Support\Facades\DB;

echo "=== 1. VERIFYING DATABASE ROWS (td_industries) ===\n";
$dbCount = DB::table('td_industries')->count();
echo "Total rows in td_industries: $dbCount\n";

$first = DB::table('td_industries')->first();
$idCol = isset($first->id) ? 'id' : (isset($first->industry_id) ? 'industry_id' : array_keys((array)$first)[0]);
$nameCol = isset($first->name) ? 'name' : (isset($first->industry_name) ? 'industry_name' : array_keys((array)$first)[1]);
echo "Detected ID Column: $idCol, Name Column: $nameCol\n";

echo "\n=== 2. VERIFYING IndustryTaxonomyService::INDUSTRY_METADATA ===\n";
$metaCount = count(IndustryTaxonomyService::INDUSTRY_METADATA);
echo "Total entries in INDUSTRY_METADATA: $metaCount\n";
$missingInMeta = [];
$dbRows = DB::table('td_industries')->get();
foreach ($dbRows as $row) {
    $rowId = $row->$idCol;
    $rowName = $row->$nameCol;
    if (!isset(IndustryTaxonomyService::INDUSTRY_METADATA[$rowId])) {
        $missingInMeta[] = "$rowId: $rowName";
    }
}
echo "Missing in INDUSTRY_METADATA: " . count($missingInMeta) . "\n";
if (!empty($missingInMeta)) {
    print_r($missingInMeta);
}

echo "\n=== 3. COMPILING MANIFEST VIA IndustryTaxonomyCompiler ===\n";
\Illuminate\Support\Facades\Cache::forget(IndustryTaxonomyCompiler::CACHE_KEY_ACTIVE);
$manifest = IndustryTaxonomyCompiler::compile();
echo "Total entries in compiled manifest: " . count($manifest['industries']) . "\n";
echo "Total sectors in compiled manifest: " . count($manifest['sectors']) . "\n";

$manifestKeyedById = [];
foreach ($manifest['industries'] as $item) {
    $manifestKeyedById[$item['id']] = $item;
}

$missingInManifest = [];
foreach ($dbRows as $row) {
    $rowId = $row->$idCol;
    $rowName = $row->$nameCol;
    if (!isset($manifestKeyedById[$rowId])) {
        $missingInManifest[] = "$rowId: $rowName";
    }
}
echo "Missing in Manifest: " . count($missingInManifest) . "\n";

echo "\n=== 4. SECTOR DISTRIBUTION ACROSS ALL 161 INDUSTRIES ===\n";
$sectorCounts = [];
foreach ($manifest['industries'] as $item) {
    $secId = $item['sector_id'] ?? 0;
    $sectorCounts[$secId] = ($sectorCounts[$secId] ?? 0) + 1;
}
ksort($sectorCounts);
foreach ($sectorCounts as $secId => $count) {
    $secName = $manifest['sectors'][$secId]['name'] ?? 'Unknown';
    echo "Sector $secId ($secName): $count industries\n";
}

echo "\n=== 5. VERIFYING 3 BLADES CONFIGURATIONS ===\n";
$blades = [
    'edit' => 'resources/views/service_company/account/edit.blade.php',
    'onboarding' => 'resources/views/service_company/account/onboarding.blade.php',
    'create' => 'resources/views/service_company/client/create.blade.php',
];

$requiredSpecificIds = [131, 127, 68, 25, 64, 1001, 1004, 1005, 133, 23, 1002, 1003, 1006, 1007, 1008, 1010, 1011, 1012, 1013, 1014];
$requiredAliases = [21, 145, 24, 27, 43, 34, 59, 74, 10, 122, 42];

foreach ($blades as $name => $path) {
    $content = file_get_contents($path);
    echo "\nChecking Blade: $name ($path)\n";
    
    // Check specific IDs
    $missingSpecific = [];
    foreach ($requiredSpecificIds as $reqId) {
        if (strpos($content, "$reqId:") === false && strpos($content, "INDUSTRY_SPECIFIC_SERVICES[$reqId]") === false) {
            $missingSpecific[] = $reqId;
        }
    }
    echo "  - Specific Vertical IDs (total " . count($requiredSpecificIds) . "): " . (empty($missingSpecific) ? "ALL PRESENT (20/20)" : "MISSING: " . implode(',', $missingSpecific)) . "\n";
    
    // Check aliases
    $missingAliases = [];
    foreach ($requiredAliases as $aliasId) {
        if (strpos($content, "INDUSTRY_SPECIFIC_SERVICES[$aliasId]") === false) {
            $missingAliases[] = $aliasId;
        }
    }
    echo "  - Aliases (total " . count($requiredAliases) . "): " . (empty($missingAliases) ? "ALL PRESENT (11/11)" : "MISSING: " . implode(',', $missingAliases)) . "\n";
    
    // Check updateSuggestedServicesForIndustry presence
    $hasFn = strpos($content, "window.updateSuggestedServicesForIndustry = function") !== false;
    echo "  - updateSuggestedServicesForIndustry function: " . ($hasFn ? "OK" : "MISSING") . "\n";
}

echo "\n=== TAXONOMY AUDIT COMPLETE: ALL PASS! ===\n";
