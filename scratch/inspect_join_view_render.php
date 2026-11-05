<?php
$lines = file('app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php');
for ($i = 1880; $i <= min(1930, count($lines) - 1); $i++) {
    echo ($i + 1) . ": " . $lines[$i];
}
