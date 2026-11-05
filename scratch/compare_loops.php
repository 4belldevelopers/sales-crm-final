<?php
$clientContent = file_get_contents('resources/views/service_company/client/dviewMore.blade.php');
$agencyContent = file_get_contents('resources/views/service_company/consultancy/dviewMore.blade.php');

echo "=== CLIENT DRAWER FOREACH LOOPS ===\n";
preg_match_all('/@foreach\s*\(([^)]+)\)/', $clientContent, $mClient);
foreach ($mClient[1] as $loop) {
    echo "Client: " . substr(trim($loop), 0, 100) . "\n";
}

echo "\n=== AGENCY DRAWER FOREACH LOOPS ===\n";
preg_match_all('/@foreach\s*\(([^)]+)\)/', $agencyContent, $mAgency);
foreach ($mAgency[1] as $loop) {
    echo "Agency: " . substr(trim($loop), 0, 100) . "\n";
}
