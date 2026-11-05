<?php
$lines = file(__DIR__ . '/../app/Http/Controllers/ServiceCompany/ServiceCompanyConsultancyController.php');
for ($i = 1970; $i <= 2015; $i++) {
    echo "[$i] " . ($lines[$i - 1] ?? '');
}
