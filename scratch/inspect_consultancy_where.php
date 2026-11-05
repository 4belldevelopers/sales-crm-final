<?php
$lines = file(__DIR__ . '/../app/Http/Controllers/ServiceCompany/ServiceCompanyConsultancyController.php');
for ($i = 1335; $i <= 1355; $i++) {
    echo "[$i] " . ($lines[$i - 1] ?? '');
}
