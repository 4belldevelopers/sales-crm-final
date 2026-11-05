<?php
$tabAct = file_get_contents(__DIR__ . '/../resources/views/service_company/client/components/tab-activity.blade.php');
$lines = file(__DIR__ . '/../resources/views/service_company/client/components/tab-activity.blade.php');
foreach ($lines as $num => $l) {
    if (strpos($l, 'ClientActivityManager') !== false) {
        echo "Line " . ($num + 1) . ": " . trim($l) . "\n";
    }
}
