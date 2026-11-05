<?php
$lines = file('resources/views/service_company/team/index.blade.php');
foreach ($lines as $num => $line) {
    if (stripos($line, 'TeamMemberUpdated') !== false || stripos($line, 'handleTeamMemberRealtimeUpdate') !== false) {
        echo ($num + 1) . ": " . trim($line) . "\n";
    }
}
