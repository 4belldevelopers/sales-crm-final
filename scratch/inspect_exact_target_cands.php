<?php
$lines = file(__DIR__ . '/../app/Services/ServiceCompany/ClientActivityReadService.php');
for ($i = 320; $i <= 370; $i++) {
    echo "[$i] " . ($lines[$i - 1] ?? '');
}
