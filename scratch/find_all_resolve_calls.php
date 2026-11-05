<?php
$serviceCode = file_get_contents(__DIR__ . '/../app/Services/ServiceCompany/ClientActivityReadService.php');

$lines = file(__DIR__ . '/../app/Services/ServiceCompany/ClientActivityReadService.php');
foreach ($lines as $num => $l) {
    if (strpos($l, 'resolveClientJobIds') !== false) {
        echo "Line " . ($num + 1) . ": " . trim($l) . "\n";
    }
}
