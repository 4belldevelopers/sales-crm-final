<?php
$content = file_get_contents('resources/views/service_company/team/index.blade.php');
$lines = explode("\n", $content);
foreach ($lines as $num => $line) {
    if (stripos($line, 'comm-drawer-toggle') !== false || stripos($line, 'data-channel') !== false) {
        echo "Line " . ($num + 1) . ": " . trim($line) . "\n";
    }
}
