<?php
$lines = file(__DIR__ . '/../app/Http/Controllers/ServiceCompany/ServiceCompanyConsultancyController.php');
for ($i = 1170; $i <= 1205; $i++) {
    echo "[$i] " . ($lines[$i - 1] ?? '');
}
