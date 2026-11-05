<?php
$lines = file(__DIR__ . '/../app/Services/ServiceCompany/ClientActivityReadService.php');
for ($i = 650; $i <= 740; $i++) {
    echo "[$i] " . ($lines[$i - 1] ?? '');
}
