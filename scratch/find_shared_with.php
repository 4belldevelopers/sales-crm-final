<?php
$files = glob(__DIR__ . '/../resources/**/*.php');
$files = array_merge($files, glob(__DIR__ . '/../app/**/*.php'));
foreach ($files as $file) {
    $c = file_get_contents($file);
    if (stripos($c, 'shared with your agency') !== false || stripos($c, 'shared with') !== false) {
        $lines = explode("\n", $c);
        foreach ($lines as $i => $l) {
            if (stripos($l, 'shared with') !== false) {
                echo basename($file) . " L" . ($i+1) . ": " . trim($l) . "\n";
            }
        }
    }
}
