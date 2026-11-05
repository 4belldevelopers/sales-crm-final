<?php
$lines = file('app/Http/Controllers/ServiceCompany/ServiceCompanyTeamController.php');
foreach ($lines as $num => $line) {
    if (stripos($line, 'add_team_member_form') !== false) {
        for ($i = max(0, $num - 5); $i < min(count($lines), $num + 45); $i++) {
            echo ($i + 1) . ": " . $lines[$i];
        }
        break;
    }
}
