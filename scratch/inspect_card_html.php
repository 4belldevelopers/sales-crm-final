<?php
$content = file_get_contents('resources/views/service_company/team/index.blade.php');
$lines = explode("\n", $content);
$start = 0;
foreach ($lines as $num => $line) {
    if (strpos($line, 'function buildTeamMemberCardHtml') !== false) {
        $start = $num;
        break;
    }
}
for ($i = $start; $i < min(count($lines), $start + 120); $i++) {
    echo ($i + 1) . ": " . $lines[$i] . "\n";
}
