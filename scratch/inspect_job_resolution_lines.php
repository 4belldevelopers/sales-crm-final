<?php
$lines = file(__DIR__ . '/../app/Services/ServiceCompany/ClientActivityReadService.php');
for ($i = 360; $i <= 460; $i++) {
    echo "[$i] " . ($lines[$i - 1] ?? '');
}
