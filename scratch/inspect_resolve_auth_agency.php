<?php
$ac = file_get_contents(__DIR__ . '/../app/Http/Controllers/ServiceCompany/ServiceCompanyConsultancyController.php');
$pos = strpos($ac, 'function resolveAuthorizedAgencyCompanyId');
echo substr($ac, $pos, 1500);
