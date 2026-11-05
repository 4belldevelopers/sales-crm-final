<?php
$lines = file(__DIR__ . '/../app/Services/ServiceCompany/ClientActivityReadService.php');
for ($i = 108; $i <= 120; $i++) {
    echo "[$i] " . ($lines[$i - 1] ?? '');
}
