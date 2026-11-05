<?php
$ccLines = file(__DIR__ . '/../app/Http/Controllers/ServiceCompany/ServiceCompanyClientController.php');
$acLines = file(__DIR__ . '/../app/Http/Controllers/ServiceCompany/ServiceCompanyConsultancyController.php');

echo "=== CLIENT LISTING DATA ENGINE ===\n";
for ($i = 0; $i < count($ccLines); $i++) {
    if (strpos($ccLines[$i], 'function all_clients_data_ajax') !== false) {
        for ($k = $i; $k < $i + 150; $k++) {
            if (isset($ccLines[$k]) && preg_match('/(Elastic|DB::|new ElasticClient|execute)/', $ccLines[$k])) {
                echo "Line " . ($k + 1) . ": " . trim($ccLines[$k]) . "\n";
            }
        }
        break;
    }
}

echo "\n=== AGENCY LISTING DATA ENGINE ===\n";
for ($i = 0; $i < count($acLines); $i++) {
    if (strpos($acLines[$i], 'function data_ajax') !== false) {
        for ($k = $i; $k < $i + 150; $k++) {
            if (isset($acLines[$k]) && preg_match('/(Elastic|DB::|new ElasticClient|execute|table\()/', $acLines[$k])) {
                echo "Line " . ($k + 1) . ": " . trim($acLines[$k]) . "\n";
            }
        }
        break;
    }
}
