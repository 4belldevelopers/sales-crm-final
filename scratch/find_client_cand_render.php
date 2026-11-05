<?php
$c = file_get_contents(__DIR__ . '/../resources/views/service_company/client/dviewMore.blade.php');
$lines = file(__DIR__ . '/../resources/views/service_company/client/dviewMore.blade.php');
foreach ($lines as $idx => $line) {
    if (strpos($line, 'function renderCandidateCard') !== false || strpos($line, 'cd-cand-card') !== false) {
        echo "Line " . ($idx + 1) . ": " . trim($line) . "\n";
    }
}
