<?php
$lines = file(__DIR__ . '/../app/Services/ServiceCompany/ClientActivityReadService.php');
foreach ($lines as $num => $l) {
    if (strpos($l, 'isDirect') !== false || strpos($l, 'agencyCandidateAppIds') !== false) {
        echo "Line " . ($num + 1) . ": " . trim($l) . "\n";
    }
}
