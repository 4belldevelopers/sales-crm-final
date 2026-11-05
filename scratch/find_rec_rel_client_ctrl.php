<?php
$cc = file_get_contents(__DIR__ . '/../app/Http/Controllers/ServiceCompany/ServiceCompanyClientController.php');
preg_match_all('/recommendation_relation[\s\S]{1,200}/i', $cc, $m);
echo "recommendation_relation occurrences in ServiceCompanyClientController:\n";
foreach ($m[0] as $idx => $occ) {
    echo "--- Occ #$idx ---\n" . substr($occ, 0, 150) . "\n";
}
