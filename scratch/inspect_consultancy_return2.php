<?php
$lines = file('app/Http/Controllers/ServiceCompany/ServiceCompanyConsultancyController.php');
for ($i = 1660; $i <= min(1710, count($lines) - 1); $i++) {
    echo ($i + 1) . ": " . $lines[$i];
}
