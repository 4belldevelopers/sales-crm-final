<?php
$lines = file(__DIR__ . '/../app/Services/ServiceCompany/ClientActivityReadService.php');
for ($i = 485; $i <= 515; $i++) {
    echo "[$i] " . ($lines[$i - 1] ?? '');
}
