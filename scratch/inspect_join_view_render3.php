<?php
$lines = file('app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php');
for ($i = 1970; $i <= min(2010, count($lines) - 1); $i++) {
    echo ($i + 1) . ": " . $lines[$i];
}
