<?php
$cc = file_get_contents(__DIR__ . '/../app/Http/Controllers/ServiceCompany/ServiceCompanyClientController.php');
$ac = file_get_contents(__DIR__ . '/../app/Http/Controllers/ServiceCompany/ServiceCompanyConsultancyController.php');

preg_match('/public function dviewMore\b[^{]+\{([\s\S]{1,3000})\n    \}/i', $cc, $mcc);
echo "=== CLIENT CONTROLLER dviewMore (First 2000 chars) ===\n";
echo substr($mcc[0] ?? 'NOT FOUND', 0, 2000) . "\n\n";

preg_match('/public function dviewMore\b[^{]+\{([\s\S]{1,3000})\n    \}/i', $ac, $mac);
echo "=== AGENCY CONTROLLER dviewMore (First 2000 chars) ===\n";
echo substr($mac[0] ?? 'NOT FOUND', 0, 2000) . "\n\n";
