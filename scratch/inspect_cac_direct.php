<?php
$lines = file(__DIR__ . '/../app/DTO/ServiceCompany/ClientAccessContext.php');
for ($i = 101; $i <= 160; $i++) {
    echo "[$i] " . ($lines[$i - 1] ?? '');
}
