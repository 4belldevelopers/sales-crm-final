<?php

$create = file_get_contents("resources/views/service_company/client/create.blade.php");
$edit = file_get_contents("resources/views/service_company/account/edit.blade.php");

// Find script tags
preg_match_all('/<script(?![^>]*src)[^>]*>(.*?)<\/script>/is', $create, $inCreate);
preg_match_all('/<script(?![^>]*src)[^>]*>(.*?)<\/script>/is', $edit, $inEdit);

$jsCreate = implode("\n", $inCreate[1]);
$jsEdit = implode("\n", $inEdit[1]);

// Let's find unique variable names or functions
preg_match_all('/(?:var|let|const|function)\s+([a-zA-Z0-9_]+)/', $jsCreate, $varsCreate);
preg_match_all('/(?:var|let|const|function)\s+([a-zA-Z0-9_]+)/', $jsEdit, $varsEdit);

$uniqueToCreateJS = array_diff(array_unique($varsCreate[1]), array_unique($varsEdit[1]));
$uniqueToEditJS = array_diff(array_unique($varsEdit[1]), array_unique($varsCreate[1]));

echo "=== JS Identifiers unique to create ===" . PHP_EOL;
print_r(array_values($uniqueToCreateJS));

echo "=== JS Identifiers unique to edit ===" . PHP_EOL;
print_r(array_values($uniqueToEditJS));
