<?php
$lines = file('app/Services/ServiceCompany/ClientActivityReadService.php');
for ($i = 0; $i < count($lines); $i++) {
    if (strpos($lines[$i], 'displayJobCompanyName') !== false) {
        echo "Line " . ($i + 1) . ": " . trim($lines[$i]) . "\n";
    }
}
