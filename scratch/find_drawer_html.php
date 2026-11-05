<?php
$lines = file('resources/views/service_company/team/index.blade.php');
foreach ($lines as $num => $line) {
    if (stripos($line, 'inviteDrawer') !== false || stripos($line, 'inviteForm') !== false || stripos($line, 'inviteOverlay') !== false) {
        echo ($num + 1) . ": " . trim($line) . "\n";
    }
}
