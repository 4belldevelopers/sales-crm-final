<?php
$lines = file(__DIR__ . '/../app/Services/ServiceCompany/ClientActivityReadService.php');
for ($i = 215; $i <= 290; $i++) {
    echo "[$i] " . ($lines[$i - 1] ?? '');
}
