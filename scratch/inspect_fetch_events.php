<?php
$lines = file(__DIR__ . '/../app/Services/ServiceCompany/ClientActivityReadService.php');
for ($i = 480; $i <= 580; $i++) {
    echo "[$i] " . ($lines[$i - 1] ?? '');
}
