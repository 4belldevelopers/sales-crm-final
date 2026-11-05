<?php
$ac = file_get_contents(__DIR__ . '/../app/Http/Controllers/ServiceCompany/ServiceCompanyConsultancyController.php');

preg_match_all('/public function ([a-zA-Z0-9_]+)\(/i', $ac, $methods);
echo "ServiceCompanyConsultancyController public methods:\n";
print_r($methods[1]);
