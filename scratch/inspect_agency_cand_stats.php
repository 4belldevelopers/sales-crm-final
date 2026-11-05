<?php
$lines = file(__DIR__ . '/../app/Http/Controllers/ServiceCompany/ServiceCompanyConsultancyController.php');
for ($i = 1330; $i <= 1390; $i++) {
    echo "[$i] " . ($lines[$i - 1] ?? '');
}
