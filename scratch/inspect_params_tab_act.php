<?php
$tabAct = file_get_contents(__DIR__ . '/../resources/views/service_company/client/components/tab-activity.blade.php');
$lines = file(__DIR__ . '/../resources/views/service_company/client/components/tab-activity.blade.php');
foreach ($lines as $idx => $line) {
    if (strpos($line, 'var params') !== false || strpos($line, 'data: params') !== false) {
        echo "Line " . ($idx + 1) . ":\n";
        for ($k = max(0, $idx - 5); $k <= min(count($lines) - 1, $idx + 25); $k++) {
            echo "  [$k] " . $lines[$k];
        }
    }
}
