<?php
$content = file_get_contents('resources/views/service_company/team/index.blade.php');
$lines = explode("\n", $content);
foreach ($lines as $num => $line) {
    if (stripos($line, 'registered') !== false || stripos($line, 'span_team') !== false || stripos($line, 'tm-stat-card') !== false) {
        echo "Line " . ($num + 1) . ": " . trim($line) . "\n";
    }
}
