<?php
$lines = file('app/Http/Controllers/ServiceCompany/ServiceCompanyConsultancyController.php');
for ($i = 1620; $i <= min(1660, count($lines) - 1); $i++) {
    echo ($i + 1) . ": " . $lines[$i];
}
