<?php
require __DIR__ . '/../vendor/autoload.php';

$c = file_get_contents(__DIR__ . '/../app/Http/Controllers/ServiceCompany/ServiceCompanyClientController.php');
$lines = explode("\n", $c);
foreach ($lines as $i => $line) {
    if (strpos($line, 'function dviewMore') !== false || strpos($line, 'function viewMore') !== false) {
        echo "Found at line " . ($i+1) . ": $line\n";
        for ($k = $i; $k < min(count($lines), $i + 40); $k++) {
            echo ($k+1) . ": " . $lines[$k] . "\n";
        }
    }
}
