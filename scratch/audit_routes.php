<?php
$cFile = file_get_contents(__DIR__ . '/../resources/views/service_company/client/dviewMore.blade.php');
$aFile = file_get_contents(__DIR__ . '/../resources/views/service_company/consultancy/dviewMore.blade.php');

echo "=== CLIENT DRAWER ROUTES ===\n";
preg_match_all('/route\s*\(\s*["\']([^"\']+)["\']/i', $cFile, $cm);
foreach (array_unique($cm[1]) as $r) {
    echo "  - $r\n";
}

echo "\n=== AGENCY DRAWER ROUTES ===\n";
preg_match_all('/route\s*\(\s*["\']([^"\']+)["\']/i', $aFile, $am);
foreach (array_unique($am[1]) as $r) {
    echo "  - $r\n";
}
