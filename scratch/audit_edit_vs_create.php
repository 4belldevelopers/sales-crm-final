<?php

$create = file_get_contents("resources/views/service_company/client/create.blade.php");
$edit = file_get_contents("resources/views/service_company/account/edit.blade.php");

// Extract routes
preg_match_all("/route\(['\"]([^'\"]+)['\"]/i", $create, $rCreate);
preg_match_all("/route\(['\"]([^'\"]+)['\"]/i", $edit, $rEdit);

echo "=== Routes in client/create ===" . PHP_EOL;
print_r(array_values(array_unique($rCreate[1])));

echo "=== Routes in account/edit ===" . PHP_EOL;
print_r(array_values(array_unique($rEdit[1])));

// Extract function declarations in JS
preg_match_all("/function\s+([a-zA-Z0-9_]+)\s*\(/i", $create, $fnCreate);
preg_match_all("/function\s+([a-zA-Z0-9_]+)\s*\(/i", $edit, $fnEdit);

$uniqueToCreate = array_diff(array_unique($fnCreate[1]), array_unique($fnEdit[1]));
$uniqueToEdit = array_diff(array_unique($fnEdit[1]), array_unique($fnCreate[1]));

echo "=== JS Functions Unique to client/create (" . count($uniqueToCreate) . ") ===" . PHP_EOL;
print_r(array_values($uniqueToCreate));

echo "=== JS Functions Unique to account/edit (" . count($uniqueToEdit) . ") ===" . PHP_EOL;
print_r(array_values($uniqueToEdit));
