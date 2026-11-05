<?php
$lines = file('app/Http/Controllers/ServiceCompany/ServiceCompanyConsultancyController.php');
for ($i = 1300; $i <= 1420; $i++) {
    echo ($i + 1) . ": " . $lines[$i];
}
