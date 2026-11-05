<?php
$lines = file(__DIR__ . '/../app/Services/ServiceCompany/ClientActivityReadService.php');
for ($i = 100; $i <= 130; $i++) {
    echo "[$i] " . ($lines[$i - 1] ?? '');
}
