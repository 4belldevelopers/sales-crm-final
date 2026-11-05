<?php
$agencyListing = file_get_contents(__DIR__ . '/../resources/views/service_company/consultancy/index.blade.php');

preg_match_all('/<th[^>]*>(.*?)<\/th>/is', $agencyListing, $m);
echo "AGENCY TABLE THs:\n";
foreach ($m[1] as $th) {
    echo "  - " . trim(preg_replace('/\s+/', ' ', strip_tags($th))) . "\n";
}

preg_match('/<table[^>]*id=["\']([^"\']+)["\']/is', $agencyListing, $tabId);
echo "\nAGENCY TABLE ID: " . ($tabId[1] ?? 'None') . "\n";

$clientListing = file_get_contents(__DIR__ . '/../resources/views/service_company/client/index.blade.php');
preg_match_all('/<th[^>]*>(.*?)<\/th>/is', $clientListing, $mc);
echo "\nCLIENT TABLE THs:\n";
foreach ($mc[1] as $th) {
    echo "  - " . trim(preg_replace('/\s+/', ' ', strip_tags($th))) . "\n";
}

preg_match('/<table[^>]*id=["\']([^"\']+)["\']/is', $clientListing, $ctabId);
echo "\nCLIENT TABLE ID: " . ($ctabId[1] ?? 'None') . "\n";
