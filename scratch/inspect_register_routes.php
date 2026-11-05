<?php
$lines = file('routes/web.php');
foreach ($lines as $i => $l) {
    if (stripos($l, "'register'") !== false || stripos($l, '"register"') !== false || stripos($l, 'RegisterController') !== false || stripos($l, 'invitationCompanyRegister') !== false) {
        echo ($i + 1) . ": " . trim($l) . "\n";
    }
}
