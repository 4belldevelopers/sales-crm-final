<?php
$lines = file(__DIR__ . '/../app/Http/Controllers/ServiceCompany/ServiceCompanyConsultancyController.php');
for ($i = 1395; $i <= 1490; $i++) {
    echo "[$i] " . ($lines[$i - 1] ?? '');
}
