<?php
$lines = file(__DIR__ . '/../app/Http/Controllers/ServiceCompany/ServiceCompanyConsultancyController.php');
for ($i = 1350; $i <= 1425; $i++) {
    echo "[$i] " . ($lines[$i - 1] ?? '');
}
