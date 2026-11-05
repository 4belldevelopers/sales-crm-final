<?php
$clientDrawer = file_get_contents(__DIR__ . '/../resources/views/service_company/client/dviewMore.blade.php');
$agencyDrawer = file_get_contents(__DIR__ . '/../resources/views/service_company/consultancy/dviewMore.blade.php');

echo "=== CLIENT DRAWER TABS ===\n";
preg_match_all('/<a[^>]+href=["\']#([^"\']+)["\'][^>]*>(.*?)<\/a>/is', $clientDrawer, $m1);
foreach ($m1[1] as $idx => $id) {
    $text = trim(strip_tags($m1[2][$idx]));
    if (!empty($text)) {
        echo "  - Tab ID: #$id | Text: $text\n";
    }
}

echo "\n=== AGENCY DRAWER TABS ===\n";
preg_match_all('/<a[^>]+href=["\']#([^"\']+)["\'][^>]*>(.*?)<\/a>/is', $agencyDrawer, $m2);
foreach ($m2[1] as $idx => $id) {
    $text = trim(strip_tags($m2[2][$idx]));
    if (!empty($text)) {
        echo "  - Tab ID: #$id | Text: $text\n";
    }
}
