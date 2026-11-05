<?php
$lines = file(__DIR__ . '/../resources/views/service_company/client/components/tab-activity.blade.php');
for ($i = 120; $i <= 180; $i++) {
    echo "[$i] " . ($lines[$i - 1] ?? '');
}
