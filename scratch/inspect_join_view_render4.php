<?php
$lines = file('app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php');
for ($i = 2030; $i <= min(2065, count($lines) - 1); $i++) {
    echo ($i + 1) . ": " . $lines[$i];
}
