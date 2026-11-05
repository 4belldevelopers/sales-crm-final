<?php
$content = file_get_contents('app/Http/Controllers/ServiceCompany/ServiceCompanyTeamController.php');
$lines = explode("\n", $content);
foreach ($lines as $num => $line) {
    if (stripos($line, 'userIndexingupload') !== false || stripos($line, 'function invite') !== false || stripos($line, 'function store') !== false || stripos($line, 'function add') !== false) {
        echo "Line " . ($num + 1) . ": " . trim($line) . "\n";
    }
}
