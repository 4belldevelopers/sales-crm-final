<?php
$lines = file(__DIR__ . '/../resources/views/service_company/client/dviewMore.blade.php');
foreach ($lines as $idx => $line) {
    if (strpos($line, 'ClientInterviewStoreDirect') !== false || strpos($line, 'cd-it-card') !== false || strpos($line, 'cd-oj-card') !== false) {
        echo "Line " . ($idx + 1) . ": " . trim($line) . "\n";
    }
}
