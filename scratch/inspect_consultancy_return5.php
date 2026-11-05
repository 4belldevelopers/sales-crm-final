<?php
$lines = file('app/Http/Controllers/ServiceCompany/ServiceCompanyConsultancyController.php');
for ($i = 1811; $i <= min(1860, count($lines) - 1); $i++) {
    echo ($i + 1) . ": " . $lines[$i];
}
