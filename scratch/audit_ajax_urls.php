<?php
$cFile = file_get_contents(__DIR__ . '/../resources/views/service_company/client/dviewMore.blade.php');
$aFile = file_get_contents(__DIR__ . '/../resources/views/service_company/consultancy/dviewMore.blade.php');

echo "=== CLIENT DRAWER AJAX URLS ===\n";
preg_match_all('/url\s*:\s*["\']([^"\']+)["\']|url\s*:\s*`([^`]+)`/i', $cFile, $cm);
$cUrls = array_unique(array_filter(array_merge($cm[1], $cm[2])));
foreach ($cUrls as $u) {
    if (strpos($u, 'http') === false || strpos($u, 'route(') !== false || strpos($u, '/') !== false) {
        echo "  - $u\n";
    }
}

echo "\n=== AGENCY DRAWER AJAX URLS ===\n";
preg_match_all('/url\s*:\s*["\']([^"\']+)["\']|url\s*:\s*`([^`]+)`/i', $aFile, $am);
$aUrls = array_unique(array_filter(array_merge($am[1], $am[2])));
foreach ($aUrls as $u) {
    if (strpos($u, 'http') === false || strpos($u, 'route(') !== false || strpos($u, '/') !== false) {
        echo "  - $u\n";
    }
}
