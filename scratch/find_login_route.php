<?php
$lines = file('routes/web.php');
foreach ($lines as $i => $l) {
    if (stripos($l, "'login'") !== false || stripos($l, '"login"') !== false || stripos($l, 'LoginController') !== false) {
        echo ($i + 1) . ": " . trim($l) . "\n";
    }
}
