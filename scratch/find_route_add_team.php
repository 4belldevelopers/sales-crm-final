<?php
$lines = file('routes/web.php');
foreach ($lines as $num => $line) {
    if (stripos($line, 'add_team_member_form') !== false) {
        for ($i = max(0, $num - 3); $i < min(count($lines), $num + 5); $i++) {
            echo ($i + 1) . ": " . $lines[$i];
        }
        break;
    }
}
