<?php
$lines = explode("\n", file_get_contents('resources/views/service_company/account/edit.blade.php'));
foreach ($lines as $i => $line) {
    if (strpos($line, 'tab-contacts') !== false || strpos($line, 'clientContactManager') !== false || strpos($line, 'Step 4') !== false || strpos($line, 'Step 5') !== false || strpos($line, 'Step 6') !== false) {
        if (strlen(trim($line)) < 120) {
            echo ($i + 1) . ": " . trim($line) . "\n";
        }
    }
}
