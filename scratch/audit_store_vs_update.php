<?php

require 'vendor/autoload.php';

// Let's inspect the fields, form handling, validation, and post-processing in ServiceCompanyClientController::store vs ServiceCompanyAccountSettingsController::update

$clientController = file_get_contents("app/Http/Controllers/ServiceCompany/ServiceCompanyClientController.php");
$accountController = file_get_contents("app/Http/Controllers/ServiceCompany/ServiceCompanyAccountSettingsController.php");

// Extract store method in clientController
preg_match('/public function store\(Request \$request\)(.*?)(?=public function|\z)/s', $clientController, $mStore);
$storeCode = $mStore[1] ?? '';

// Extract update method in accountController
preg_match('/public function update\(Request \$request, \$id\)(.*?)(?=public function|\z)/s', $accountController, $mUpdate);
$updateCode = $mUpdate[1] ?? '';

echo "Client store() length: " . strlen($storeCode) . " bytes" . PHP_EOL;
echo "Account update() length: " . strlen($updateCode) . " bytes" . PHP_EOL;

// Check validation rules in both
preg_match('/\$request->validate\(\[(.*?)\]\);/s', $storeCode, $vStore);
preg_match('/\$request->validate\(\[(.*?)\]\);/s', $updateCode, $vUpdate);

echo "--- Client store validation ---" . PHP_EOL;
echo ($vStore[1] ?? 'None found') . PHP_EOL;

echo "--- Account update validation ---" . PHP_EOL;
echo ($vUpdate[1] ?? 'None found') . PHP_EOL;
