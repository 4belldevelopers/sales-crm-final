<?php

$create = file_get_contents("resources/views/service_company/client/create.blade.php");
$edit = file_get_contents("resources/views/service_company/account/edit.blade.php");

// Compare tagify inputs
preg_match_all('/id="([^"]*tagify[^"]*)"/i', $create, $tagCreate);
preg_match_all('/id="([^"]*tagify[^"]*)"/i', $edit, $tagEdit);

echo "Tagify IDs in client/create: " . json_encode(array_values(array_unique($tagCreate[1]))) . PHP_EOL;
echo "Tagify IDs in account/edit: " . json_encode(array_values(array_unique($tagEdit[1]))) . PHP_EOL;

// Check for offline / local caching / WASM / browser execution
$checks = [
    'localStorage',
    'sessionStorage',
    'debounce',
    'idempotency',
    'X-Idempotency-Key',
    'client_intelligence',
    'Brandfetch',
    'Clearbit',
    'canvas',
    'compressAndSaveLogo',
    'validateInternalField',
    'industriesBundle',
    'checkDomainAvailability',
    'tax_id',
    'social',
    'card-sub-card',
    '1366'
];

echo "=== Feature presence comparison ===" . PHP_EOL;
foreach ($checks as $chk) {
    $hasCreate = stripos($create, $chk) !== false ? 'YES' : 'NO';
    $hasEdit = stripos($edit, $chk) !== false ? 'YES' : 'NO';
    echo sprintf("%-25s | Create: %-4s | Edit: %-4s\n", $chk, $hasCreate, $hasEdit);
}
