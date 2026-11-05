<?php
$lines = file(__DIR__ . '/../app/Services/ServiceCompany/ClientActivityReadService.php');
for ($i = 260; $i <= 360; $i++) {
    echo "[$i] " . ($lines[$i - 1] ?? '');
}
