<?php
$lines = file('resources/views/service_company/team/index.blade.php');
foreach ($lines as $num => $line) {
    if (stripos($line, '.invite-drawer') !== false || stripos($line, '.invite-overlay') !== false) {
        for ($i = max(0, $num - 2); $i < min(count($lines), $num + 20); $i++) {
            echo ($i + 1) . ": " . $lines[$i];
        }
        break;
    }
}
