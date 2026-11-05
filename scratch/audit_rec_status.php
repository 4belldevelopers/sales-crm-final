<?php
$c = file_get_contents(__DIR__ . '/../app/Http/Controllers/ServiceCompany/ServiceCompanyConsultancyController.php');
preg_match_all('/(?:recommendation_relation|rec_rel).+?final_status.+?;/s', $c, $m);
print_r($m[0]);
