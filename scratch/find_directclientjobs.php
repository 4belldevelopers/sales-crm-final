<?php
$c = file_get_contents(__DIR__ . '/../resources/views/service_company/client/dviewMore.blade.php');
$lines = file(__DIR__ . '/../resources/views/service_company/client/dviewMore.blade.php');
foreach ($lines as $idx => $line) {
    if (strpos($line, 'directclientJobsListajax') !== false) {
        echo "Line " . ($idx + 1) . ": " . trim($line) . "\n";
        for ($k = max(0, $idx - 5); $k <= min(count($lines) - 1, $idx + 25); $k++) {
            echo "   [" . ($k + 1) . "] " . $lines[$k];
        }
    }
}
