<?php
$lines = file('resources/views/service_company/team/index.blade.php');
foreach ($lines as $num => $line) {
    if (preg_match('/function\s+(updateLocalStatTabCounts|getDerivedRecords|buildTeamMemberCardHtml|fetchTeams|setTeamTab)/', $line, $m)) {
        echo "Line " . ($num + 1) . ": " . $m[1] . "\n";
    }
}
