<?php
$cc = file_get_contents(__DIR__ . '/../app/Http/Controllers/ServiceCompany/ServiceCompanyClientController.php');
$ac = file_get_contents(__DIR__ . '/../app/Http/Controllers/ServiceCompany/ServiceCompanyConsultancyController.php');

preg_match_all('/new ClientActivityQuery\([\s\S]*?\)/i', $cc, $mcc);
echo "=== CLIENT CONTROLLER ClientActivityQuery ===\n";
foreach ($mcc[0] as $q) {
    echo $q . "\n\n";
}

preg_match_all('/new ClientActivityQuery\([\s\S]*?\)/i', $ac, $mac);
echo "=== AGENCY CONTROLLER ClientActivityQuery ===\n";
foreach ($mac[0] as $q) {
    echo $q . "\n\n";
}
