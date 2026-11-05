<?php
require __DIR__ . '/../vendor/autoload.php';

$files = [
    'app/Http/Controllers/ServiceCompany/ServiceCompanyClientController.php',
    'app/Http/Controllers/ServiceCompany/ServiceCompanyConsultancyController.php',
];

foreach ($files as $file) {
    $c = file_get_contents(__DIR__ . '/../' . $file);
    preg_match_all('/(?:insert|create|update)\([^)]*staffing_company_relation/i', $c, $m);
    echo "$file:\n";
    $lines = explode("\n", $c);
    foreach ($lines as $i => $line) {
        if (strpos($line, 'staffing_company_relation') !== false && (strpos($line, 'insert') !== false || strpos($line, 'update') !== false)) {
            echo "  Line " . ($i+1) . ": $line\n";
            for ($k = max(0, $i-5); $k <= min(count($lines)-1, $i+15); $k++) {
                echo "    " . ($k+1) . ": " . $lines[$k] . "\n";
            }
        }
    }
}
