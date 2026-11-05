<?php
$cc = file_get_contents(__DIR__ . '/../app/Http/Controllers/ServiceCompany/ServiceCompanyClientController.php');
$ac = file_get_contents(__DIR__ . '/../app/Http/Controllers/ServiceCompany/ServiceCompanyConsultancyController.php');

$posCc = strpos($cc, 'function data_ajax');
if ($posCc === false) $posCc = strpos($cc, 'function all_clients_data');
echo "CLIENT LISTING DATA METHOD:\n" . substr($cc, $posCc, 1200) . "\n\n";

$posAc = strpos($ac, 'function data_ajax');
echo "AGENCY LISTING DATA METHOD:\n" . substr($ac, $posAc, 1200) . "\n\n";
