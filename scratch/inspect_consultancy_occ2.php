<?php
$lines = file(__DIR__ . '/../app/Http/Controllers/ServiceCompany/ServiceCompanyConsultancyController.php');
for ($i = 1300; $i <= 1350; $i++) {
    echo "[$i] " . ($lines[$i - 1] ?? '');
}
