<?php
$content = file_get_contents('resources/views/service_company/team/index.blade.php');
$lines = explode("\n", $content);
foreach ($lines as $num => $line) {
    if (stripos($line, 'saveCommunicationRoles') !== false || stripos($line, 'update_communication_roles') !== false || stripos($line, 'set_representative') !== false) {
        echo "Line " . ($num + 1) . ": " . trim($line) . "\n";
    }
}
