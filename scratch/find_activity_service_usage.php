<?php
$files = glob(__DIR__ . '/../app/Http/Controllers/**/*.php');
foreach ($files as $f) {
    $c = file_get_contents($f);
    if (strpos($c, 'ClientActivityQuery') !== false || strpos($c, 'ClientActivityReadService') !== false) {
        echo "Found in: " . basename($f) . "\n";
        preg_match_all('/(?:ClientActivityQuery|ClientActivityReadService)[\s\S]{1,150}/', $c, $m);
        foreach ($m[0] as $occ) {
            echo "   " . substr($occ, 0, 100) . "...\n";
        }
    }
}
