<?php
$clientDrawer = file_get_contents(__DIR__ . '/../resources/views/service_company/client/dviewMore.blade.php');
$agencyDrawer = file_get_contents(__DIR__ . '/../resources/views/service_company/consultancy/dviewMore.blade.php');

function extractTabContent($html, $tabId) {
    if (preg_match('/<div[^>]+id=["\']' . $tabId . '["\'][^>]*>(.*?)<\/div>\s*<!--\s*\/' . $tabId . '\s*-->/is', $html, $m)) {
        return substr($m[1], 0, 800);
    }
    if (preg_match('/<div[^>]+id=["\']' . $tabId . '["\'][^>]*>(.*?)<\/div>/is', $html, $m)) {
        return substr($m[1], 0, 800);
    }
    return 'Not found';
}

$tabs = ['requisitions', 'candidate_list', 'interview_tracker', 'candidate_joining', 'candidateDashboard'];

echo "=== TAB IMPLEMENTATION AUDIT ===\n";
foreach ($tabs as $tab) {
    echo "\n--- TAB: #$tab ---\n";
    echo "[CLIENT DRAWER]:\n";
    if (preg_match('/<div[^>]+id=["\']' . $tab . '["\'][^>]*>(.{1,500})/is', $clientDrawer, $m)) {
        echo "  " . trim(preg_replace('/\s+/', ' ', strip_tags($m[0]))) . "\n";
    }
    if (preg_match('/url\s*:\s*["\']([^"\']+' . $tab . '[^"\']*)["\']|route\(["\']([^"\']+)["\']\)/i', $clientDrawer, $rm)) {
        echo "  Endpoint: " . ($rm[1] ?? $rm[2] ?? '') . "\n";
    }

    echo "[AGENCY DRAWER]:\n";
    if (preg_match('/<div[^>]+id=["\']' . $tab . '["\'][^>]*>(.{1,500})/is', $agencyDrawer, $m)) {
        echo "  " . trim(preg_replace('/\s+/', ' ', strip_tags($m[0]))) . "\n";
    }
}
