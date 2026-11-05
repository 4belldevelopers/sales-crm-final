<?php
$content = file_get_contents('app/Http/Controllers/ServiceCompany/ServiceCompanyTeamController.php');
$lines = explode("\n", $content);
foreach ($lines as $num => $line) {
    if (stripos($line, 'function setCommunicationRoles') !== false || stripos($line, 'setCommunicationRoles') !== false || stripos($line, 'set_communication_roles') !== false) {
        echo "Line " . ($num + 1) . ": " . trim($line) . "\n";
    }
}
