<?php
$lines = file('routes/web.php');
foreach ($lines as $i => $l) {
    if (stripos($l, 'teamReg') !== false) {
        for ($j = max(0, $i - 3); $j < min(count($lines), $i + 5); $j++) {
            echo ($j + 1) . ": " . $lines[$j];
        }
    }
}
