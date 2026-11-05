<?php
$serviceCode = file_get_contents(__DIR__ . '/../app/Services/ServiceCompany/ClientActivityReadService.php');

$pos = strpos($serviceCode, 'protected function executeReadPipeline');
echo substr($serviceCode, $pos + 500, 3000);
