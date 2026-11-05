<?php
$serviceCode = file_get_contents(__DIR__ . '/../app/Services/ServiceCompany/ClientActivityReadService.php');

$pos = strpos($serviceCode, 'function resolveJobProfiles');
echo substr($serviceCode, $pos, 1500);
