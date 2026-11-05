<?php
$files = ['app/Http/Controllers/Admin/AdminAccountSettingsController.php', 'app/Http/Controllers/AdminAccountSettingsController.php'];
foreach ($files as $f) {
    if (file_exists($f)) {
        $lines = file($f);
        foreach ($lines as $i => $l) {
            if (stripos($l, 'function add_team_member_form') !== false) {
                for ($j = max(0, $i - 3); $j < min(count($lines), $i + 35); $j++) {
                    echo ($j + 1) . ": " . $lines[$j];
                }
                break;
            }
        }
    }
}
