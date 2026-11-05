<?php
$ac = file_get_contents(__DIR__ . '/../app/Http/Controllers/ServiceCompany/ServiceCompanyConsultancyController.php');
$cc = file_get_contents(__DIR__ . '/../app/Http/Controllers/ServiceCompany/ServiceCompanyClientController.php');

$posAc = strpos($ac, 'function dashboard_ajax');
echo "=== AGENCY dashboard_ajax ===\n";
echo substr($ac, $posAc, 1500) . "\n\n";

$posCc = strpos($cc, 'performance-analytics');
if ($posCc === false) $posCc = strpos($cc, 'performanceAnalytics');
echo "=== CLIENT performanceAnalytics ===\n";
echo substr($cc, $posCc - 50, 1500) . "\n\n";
