<?php

$clientController = file_get_contents("app/Http/Controllers/ServiceCompany/ServiceCompanyClientController.php");
$accountController = file_get_contents("app/Http/Controllers/ServiceCompany/ServiceCompanyAccountSettingsController.php");

preg_match('/public function store\(Request \$request\)\s*\{(.*?)(?=DB::|\$client\s*=|\$company\s*=)/s', $clientController, $m1);
preg_match('/public function update\(Request \$request, \$id\)\s*\{(.*?)(?=DB::|\$client\s*=|\$company\s*=)/s', $accountController, $m2);

echo "=== Client store start ===" . PHP_EOL;
echo substr($m1[1] ?? '', 0, 1500) . PHP_EOL;

echo "=== Account update start ===" . PHP_EOL;
echo substr($m2[1] ?? '', 0, 1500) . PHP_EOL;
