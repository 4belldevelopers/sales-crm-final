<?php
$lines = file('app/Http/Controllers/ServiceCompany/ServiceCompanyConsultancyController.php');
for ($i = 1280; $i <= 1350; $i++) {
    echo ($i + 1) . ": " . $lines[$i];
}
