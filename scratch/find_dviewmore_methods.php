<?php
$files = glob(__DIR__ . '/../app/Http/Controllers/ServiceCompany/*.php');
foreach ($files as $f) {
    $content = file_get_contents($f);
    if (strpos($content, 'dviewMore') !== false) {
        echo basename($f) . " contains 'dviewMore'\n";
        preg_match_all('/function\s+([a-zA-Z0-9_]+)\s*\([^)]*\)[\s\S]*?dviewMore/i', $content, $m);
        foreach ($m[1] as $fn) {
            echo "   -> in function $fn()\n";
        }
    }
}
