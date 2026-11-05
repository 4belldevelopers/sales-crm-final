<?php
$lines = file('app/Http/Controllers/ServiceCompany/ServiceCompanyConsultancyController.php');
for ($i = 1420; $i <= 1490; $i++) {
    echo ($i + 1) . ": " . $lines[$i];
}
