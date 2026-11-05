<?php
$lines = file('app/Actions/Auth/AttemptLoginAction.php');
foreach ($lines as $i => $l) {
    if (stripos($l, 'userId') !== false || stripos($l, 'teamReg') !== false || stripos($l, 'email_verification_code') !== false || stripos($l, 'execute(') !== false) {
        for ($j = max(0, $i - 3); $j < min(count($lines), $i + 35); $j++) {
            echo ($j + 1) . ": " . $lines[$j];
        }
        echo "----\n";
    }
}
