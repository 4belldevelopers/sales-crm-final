<?php
$lines = file(__DIR__ . '/../app/Services/ServiceCompany/ClientActivityReadService.php');
for ($i = 105; $i <= 135; $i++) {
    echo "[$i] " . ($lines[$i - 1] ?? '');
}
