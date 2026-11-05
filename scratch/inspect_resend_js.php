<?php
$lines = file('resources/views/service_company/team/index.blade.php');
foreach ($lines as $i => $l) {
    if (stripos($l, 'reSendhandleClick') !== false) {
        for ($j = max(0, $i - 3); $j < min(count($lines), $i + 65); $j++) {
            echo ($j + 1) . ": " . $lines[$j];
        }
        break;
    }
}
