<?php
$content = file_get_contents('resources/views/service_company/team/index.blade.php');
$lines = explode("\n", $content);
foreach ($lines as $num => $line) {
    if (stripos($line, 'JeroTeamLocalStore') !== false || stripos($line, 'indexedDB') !== false || stripos($line, 'idb') !== false) {
        echo "Line " . ($num + 1) . ": " . trim($line) . "\n";
    }
}
