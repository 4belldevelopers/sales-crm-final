<?php
$lines = file(__DIR__ . '/../app/Services/ServiceCompany/ClientActivityReadService.php');
for ($i = 488; $i <= 510; $i++) {
    echo "[$i] " . ($lines[$i - 1] ?? '');
}
