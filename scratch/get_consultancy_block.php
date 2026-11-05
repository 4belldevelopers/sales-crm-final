<?php
$lines = file(__DIR__ . '/../app/Http/Controllers/ServiceCompany/ServiceCompanyConsultancyController.php');
echo implode("", array_slice($lines, 1305, 45));
