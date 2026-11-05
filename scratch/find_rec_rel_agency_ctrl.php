<?php
$ac = file_get_contents(__DIR__ . '/../app/Http/Controllers/ServiceCompany/ServiceCompanyConsultancyController.php');
preg_match_all('/recommendation_relation[\s\S]{1,250}/i', $ac, $m);
echo "recommendation_relation occurrences in ServiceCompanyConsultancyController:\n";
foreach ($m[0] as $idx => $occ) {
    echo "--- Occ #$idx ---\n" . substr($occ, 0, 200) . "\n";
}
