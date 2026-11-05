<?php
$lines = file(__DIR__ . '/../app/Http/Controllers/ServiceCompany/ServiceCompanyConsultancyController.php');
for ($i = 1280; $i <= 1305; $i++) {
    echo "[$i] " . ($lines[$i - 1] ?? '');
}
