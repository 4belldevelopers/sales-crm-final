<?php
$lines = file('app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php');
for ($i = 1930; $i <= min(1960, count($lines) - 1); $i++) {
    echo ($i + 1) . ": " . $lines[$i];
}
