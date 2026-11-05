<?php
$lines = file(__DIR__ . '/../app/Services/ServiceCompany/ClientActivityReadService.php');
for ($i = 210; $i <= 230; $i++) {
    echo "[$i] " . ($lines[$i - 1] ?? '');
}
