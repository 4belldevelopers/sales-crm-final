<?php
$a = file_get_contents(__DIR__ . '/../resources/views/service_company/consultancy/dviewMore.blade.php');
$ai = file_get_contents(__DIR__ . '/../resources/views/service_company/consultancy/index.blade.php');

preg_match_all('/<script[^>]*>(.*?)<\/script>/is', $a, $ms);
echo "AGENCY DRAWER SCRIPT TAGS COUNT: " . count($ms[0]) . "\n";
foreach ($ms[1] as $idx => $s) {
    echo "  Script #$idx (length " . strlen($s) . "): " . substr(trim(preg_replace('/\s+/', ' ', $s)), 0, 150) . "...\n";
}

preg_match_all('/function\s+([a-zA-Z0-9_]+)\s*\(/i', $a, $mf);
echo "AGENCY DRAWER FUNCTIONS: " . implode(', ', array_unique($mf[1])) . "\n";

// Now check what AJAX calls exist in consultancy/index.blade.php that deal with drawer or tabs
preg_match_all('/\$\.ajax\(\{\s*url\s*:\s*([^,\n]+)/i', $ai, $majax);
echo "\nAGENCY INDEX AJAX CALLS:\n";
print_r(array_unique($majax[1]));
