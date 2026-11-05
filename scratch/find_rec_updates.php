<?php
$files = glob(__DIR__ . '/../app/**/*.php');
foreach ($files as $file) {
    $c = file_get_contents($file);
    if (strpos($c, 'recommendation_relation') !== false && strpos($c, 'final_status') !== false && strpos($c, 'update') !== false) {
        $lines = explode("\n", $c);
        foreach ($lines as $i => $l) {
            if (strpos($l, 'final_status') !== false) {
                echo basename($file) . " L" . ($i+1) . ": " . trim($l) . "\n";
            }
        }
    }
}
