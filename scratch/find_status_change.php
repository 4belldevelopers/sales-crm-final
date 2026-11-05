<?php
$content = file_get_contents('app/Http/Controllers/ServiceCompany/ServiceCompanyTeamController.php');
$lines = explode("\n", $content);
foreach ($lines as $num => $line) {
    if (stripos($line, 'function changeStatusUser') !== false || stripos($line, 'function statusChange') !== false || stripos($line, 'changeStatus') !== false) {
        echo "Line " . ($num + 1) . ": " . trim($line) . "\n";
    }
}
