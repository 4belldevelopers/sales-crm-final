<?php
$lines = file('app/Http/Controllers/ServiceCompany/ServiceCompanyConsultancyController.php');
for ($i = 1710; $i <= min(1760, count($lines) - 1); $i++) {
    echo ($i + 1) . ": " . $lines[$i];
}
