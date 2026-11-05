<?php
$c = file_get_contents(__DIR__ . '/../app/Services/ServiceCompany/ClientActivityReadService.php');
$lines = explode("\n", $c);
foreach ($lines as $i => $l) {
    if (strpos($l, 'local_') !== false || strpos($l, 'ats_') !== false) {
        echo "L" . ($i+1) . ": " . trim($l) . "\n";
    }
}
