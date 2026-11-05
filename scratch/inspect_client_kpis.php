<?php
$cc = file_get_contents(__DIR__ . '/../app/Http/Controllers/ServiceCompany/ServiceCompanyClientController.php');
$lines = file(__DIR__ . '/../app/Http/Controllers/ServiceCompany/ServiceCompanyClientController.php');
for ($i = 4130; $i <= 4165; $i++) {
    echo "[$i] " . ($lines[$i - 1] ?? '');
}
