<?php
$files = [
    'resources/views/service_company/ajax_pages/team-pagination.blade.php',
    'resources/views/service_company/team/index.blade.php'
];
foreach ($files as $f) {
    if (file_exists($f)) {
        $lines = explode("\n", file_get_contents($f));
        foreach ($lines as $num => $line) {
            if (stripos($line, 'showing') !== false) {
                echo "$f line " . ($num + 1) . ": " . trim($line) . "\n";
            }
        }
    }
}
