<?php
$clientFile = file_get_contents(__DIR__ . '/../app/Http/Controllers/ServiceCompany/ServiceCompanyClientController.php');
$agencyFile = file_get_contents(__DIR__ . '/../app/Http/Controllers/ServiceCompany/ServiceCompanyConsultancyController.php');

echo "=== CLIENT LISTING (all_clients_data_ajax) ===\n";
$pos1 = strpos($clientFile, 'function all_clients_data_ajax');
if ($pos1 !== false) {
    echo substr($clientFile, $pos1, 2000) . "\n\n";
} else {
    echo "all_clients_data_ajax not found\n\n";
}

echo "=== AGENCY LISTING (data_ajax) ===\n";
$pos2 = strpos($agencyFile, 'function data_ajax');
if ($pos2 !== false) {
    echo substr($agencyFile, $pos2, 2000) . "\n\n";
} else {
    echo "data_ajax not found\n\n";
}
