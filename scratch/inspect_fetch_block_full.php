<?php
$lines = file(__DIR__ . '/../app/Services/ServiceCompany/ClientActivityReadService.php');
for ($i = 660; $i <= 780; $i++) {
    echo "[$i] " . ($lines[$i - 1] ?? '');
}
