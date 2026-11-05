<?php
$lines = file('app/Http/Controllers/ServiceCompany/ServiceCompanyConsultancyController.php');
for ($i = 1760; $i <= min(1810, count($lines) - 1); $i++) {
    echo ($i + 1) . ": " . $lines[$i];
}
