<?php
$ctrl = file_get_contents(__DIR__ . '/../app/Http/Controllers/ServiceCompany/ServiceCompanyConsultancyController.php');
preg_match_all('/staffing_company_relation.+?;/s', $ctrl, $matches);
echo "ConsultancyController matches: " . count($matches[0]) . PHP_EOL;
foreach ($matches[0] as $i => $m) {
    echo "--- Occ $i ---\n" . substr($m, 0, 200) . "...\n";
}

$ctrl2 = file_get_contents(__DIR__ . '/../app/Http/Controllers/ServiceCompany/ServiceCompanyClientController.php');
preg_match_all('/staffing_company_relation.+?;/s', $ctrl2, $matches2);
echo "\nClientController matches: " . count($matches2[0]) . PHP_EOL;
foreach ($matches2[0] as $i => $m) {
    echo "--- Occ $i ---\n" . substr($m, 0, 200) . "...\n";
}
