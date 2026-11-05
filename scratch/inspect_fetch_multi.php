<?php
$lines = file(__DIR__ . '/../app/Services/ServiceCompany/ClientActivityReadService.php');
for ($i = 610; $i <= 710; $i++) {
    echo "[$i] " . ($lines[$i - 1] ?? '');
}
