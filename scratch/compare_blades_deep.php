<?php

$create = file_get_contents("resources/views/service_company/client/create.blade.php");
$edit = file_get_contents("resources/views/service_company/account/edit.blade.php");

// Check form actions and methods
preg_match('/<form[^>]+>/i', $create, $f1);
preg_match('/<form[^>]+>/i', $edit, $f2);

echo "Form tag in client/create: " . ($f1[0] ?? 'NONE') . PHP_EOL;
echo "Form tag in account/edit: " . ($f2[0] ?? 'NONE') . PHP_EOL;

// Check navigation tabs or sections
preg_match_all('/<li[^>]*class="[^"]*nav-item[^"]*"[^>]*>.*?<\/li>/is', $create, $tabs1);
preg_match_all('/<li[^>]*class="[^"]*nav-item[^"]*"[^>]*>.*?<\/li>/is', $edit, $tabs2);

echo "Nav items count in client/create: " . count($tabs1[0]) . PHP_EOL;
echo "Nav items count in account/edit: " . count($tabs2[0]) . PHP_EOL;

// Check difference in script tags
preg_match_all('/<script[^>]*src=["\']([^"\']+)["\']/i', $create, $s1);
preg_match_all('/<script[^>]*src=["\']([^"\']+)["\']/i', $edit, $s2);

echo "External scripts in create: " . json_encode($s1[1]) . PHP_EOL;
echo "External scripts in edit: " . json_encode($s2[1]) . PHP_EOL;

// Check difference in inline script length
preg_match_all('/<script(?![^>]*src)[^>]*>(.*?)<\/script>/is', $create, $in1);
preg_match_all('/<script(?![^>]*src)[^>]*>(.*?)<\/script>/is', $edit, $in2);

$totalIn1 = array_sum(array_map('strlen', $in1[1]));
$totalIn2 = array_sum(array_map('strlen', $in2[1]));

echo "Total inline JS in create: " . $totalIn1 . " bytes" . PHP_EOL;
echo "Total inline JS in edit: " . $totalIn2 . " bytes" . PHP_EOL;
