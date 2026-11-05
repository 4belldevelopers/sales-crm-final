<?php
$files = glob(__DIR__ . '/../app/**/*.php');
foreach ($files as $file) {
    $c = file_get_contents($file);
    if (strpos($c, 'ClientActivityQuery') !== false) {
        $lines = explode("\n", $c);
        foreach ($lines as $i => $l) {
            if (strpos($l, 'new ClientActivityQuery') !== false || strpos($l, 'viewerType') !== false) {
                echo basename($file) . " L" . ($i+1) . ": " . trim($l) . "\n";
            }
        }
    }
}
