<?php
$aFile = file_get_contents(__DIR__ . '/../resources/views/service_company/consultancy/dviewMore.blade.php');
preg_match_all('/<script\b[^>]*>(.*?)<\/script>/is', $aFile, $scripts);
echo "Found " . count($scripts[1]) . " script tags in consultancy/dviewMore\n";
foreach ($scripts[1] as $idx => $s) {
    echo "Script $idx length: " . strlen($s) . " bytes\n";
    preg_match_all('/(fetch\([^)]+\)|\$\.ajax\([^)]+\)|\$\.get\([^)]+\)|\$\.post\([^)]+\)|axios\.[a-z]+\([^)]+\))/i', $s, $calls);
    foreach ($calls[0] as $c) {
        echo "  Call: " . substr(preg_replace('/\s+/', ' ', $c), 0, 150) . "\n";
    }
}
