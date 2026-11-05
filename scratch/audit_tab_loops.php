<?php
$c = file_get_contents(__DIR__ . '/../resources/views/service_company/client/dviewMore.blade.php');
$a = file_get_contents(__DIR__ . '/../resources/views/service_company/consultancy/dviewMore.blade.php');

$tabs = ['requisitions', 'candidate_list', 'interview_tracker', 'candidate_joining', 'candidateDashboard'];

echo "=== CLIENT DRAWER TABS SIZES & LOOPS ===\n";
foreach ($tabs as $t) {
    if (preg_match('/id=["\']' . $t . '["\'][\s\S]*?(?=<div class="tab-pane|<div role="tabpanel" id=|$)/i', $c, $m)) {
        $len = strlen($m[0]);
        preg_match_all('/@foreach\s*\((.*?)\)/', $m[0], $f);
        echo "Tab #$t: length $len, foreach: " . implode(' | ', $f[1]) . "\n";
    }
}

echo "\n=== AGENCY DRAWER TABS SIZES & LOOPS ===\n";
foreach ($tabs as $t) {
    if (preg_match('/id=["\']' . $t . '["\'][\s\S]*?(?=<div class="tab-pane|<div role="tabpanel" id=|$)/i', $a, $m)) {
        $len = strlen($m[0]);
        preg_match_all('/@foreach\s*\((.*?)\)/', $m[0], $f);
        echo "Tab #$t: length $len, foreach: " . implode(' | ', $f[1]) . "\n";
    }
}
