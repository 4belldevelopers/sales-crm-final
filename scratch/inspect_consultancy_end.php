<?php
$lines = file('app/Http/Controllers/ServiceCompany/ServiceCompanyConsultancyController.php');
for ($i = 1980; $i <= min(2010, count($lines) - 1); $i++) {
    echo ($i + 1) . ": " . $lines[$i];
}
