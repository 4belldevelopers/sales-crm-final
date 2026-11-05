<?php
$lines = file(__DIR__ . '/../app/DTO/ServiceCompany/ClientAccessContext.php');
for ($i = 40; $i <= 100; $i++) {
    echo "[$i] " . ($lines[$i - 1] ?? '');
}
