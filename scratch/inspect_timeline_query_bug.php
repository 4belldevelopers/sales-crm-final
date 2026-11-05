<?php
$lines = file(__DIR__ . '/../app/Services/ServiceCompany/ClientActivityReadService.php');
for ($i = 645; $i <= 675; $i++) {
    echo "[$i] " . ($lines[$i - 1] ?? '');
}
