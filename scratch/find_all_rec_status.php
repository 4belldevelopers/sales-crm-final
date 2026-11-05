<?php
$files = glob(__DIR__ . '/../app/**/*.php');
foreach ($files as $file) {
    $c = file_get_contents($file);
    if (strpos($c, 'recommendation_relation') !== false && strpos($c, 'final_status') !== false) {
        preg_match_all('/(?:final_status)[^;]+;/s', $c, $m);
        echo basename($file) . ":\n";
        foreach ($m[0] as $match) {
            echo "  " . substr(trim($match), 0, 100) . "\n";
        }
    }
}
