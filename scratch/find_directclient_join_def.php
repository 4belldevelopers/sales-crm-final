<?php
$lines = file('app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php');
for ($i = 0; $i < count($lines); $i++) {
    if (strpos($lines[$i], 'function directclientCandidateJoinList') !== false) {
        echo "Line " . ($i + 1) . "\n";
        for ($j = $i; $j <= min(count($lines) - 1, $i + 50); $j++) {
            echo "  " . ($j + 1) . ": " . $lines[$j];
        }
        break;
    }
}
