<?php
$lines = file('app/Http/Controllers/ServiceCompany/ServiceCompanyConsultancyController.php');
for ($i = 1490; $i <= 1520; $i++) {
    echo ($i + 1) . ": " . $lines[$i];
}
