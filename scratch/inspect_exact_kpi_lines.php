<?php
$lines = file(__DIR__ . '/../app/Services/ServiceCompany/ClientActivityReadService.php');
echo implode("", array_slice($lines, 488, 140));
