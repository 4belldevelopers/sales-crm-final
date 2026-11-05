<?php
$clientListing = file_get_contents(__DIR__ . '/../resources/views/service_company/client/index.blade.php');
$agencyListing = file_get_contents(__DIR__ . '/../resources/views/service_company/consultancy/index.blade.php');

echo "CLIENT LISTING LENGTH: " . strlen($clientListing) . " bytes\n";
echo "AGENCY LISTING LENGTH: " . strlen($agencyListing) . " bytes\n";

// Check KPI cards / headers
preg_match_all('/<div[^>]+class=["\'][^"\']*(?:kpi|card|summary|stat|badge)[^"\']*["\'][^>]*>(.*?)<\/div>/is', substr($clientListing, 0, 5000), $m);
echo "CLIENT TOP ELEMENTS:\n";
foreach (array_slice($m[0], 0, 5) as $el) {
    echo "  " . trim(preg_replace('/\s+/', ' ', strip_tags($el))) . "\n";
}

// Find table headers in both
preg_match('/<thead[^>]*>(.*?)<\/thead>/is', $clientListing, $cTh);
echo "\nCLIENT TABLE HEADERS:\n" . (isset($cTh[1]) ? trim(preg_replace('/\s+/', ' ', strip_tags($cTh[1]))) : "None") . "\n";

preg_match('/<thead[^>]*>(.*?)<\/thead>/is', $agencyListing, $aTh);
echo "\nAGENCY TABLE HEADERS:\n" . (isset($aTh[1]) ? trim(preg_replace('/\s+/', ' ', strip_tags($aTh[1]))) : "None") . "\n";
