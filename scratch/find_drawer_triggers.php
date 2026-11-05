<?php
$lines = file('resources/views/service_company/team/index.blade.php');
foreach ($lines as $num => $line) {
    if (stripos($line, 'Add/Invite Members') !== false || stripos($line, 'openInviteDrawer') !== false || stripos($line, 'kt_drawer_invite') !== false || stripos($line, 'populateInviteRoleOptions') !== false) {
        echo ($num + 1) . ": " . trim($line) . "\n";
    }
}
