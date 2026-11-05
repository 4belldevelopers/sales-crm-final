<?php
$lines = file('app/Http/Controllers/ServiceCompany/ServiceCompanyConsultancyController.php');
for ($i = 1270; $i <= 1305; $i++) {
    echo ($i + 1) . ": " . $lines[$i];
}
