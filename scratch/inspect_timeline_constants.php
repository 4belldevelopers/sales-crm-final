<?php
$c = file_get_contents(__DIR__ . '/../app/Services/ServiceCompany/ClientActivityReadService.php');
$lines = explode("\n", $c);
foreach ($lines as $i => $l) {
    if (strpos($l, 'TIMELINE') !== false || strpos($l, 'timeline') !== false && strpos($l, 'index') !== false) {
        echo "L" . ($i+1) . ": " . trim($l) . "\n";
    }
}
