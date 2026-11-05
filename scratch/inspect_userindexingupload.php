<?php
$lines = file('app/Helper/GeneralMethod.php');
foreach ($lines as $i => $l) {
    if (stripos($l, 'function userIndexingupload') !== false) {
        for ($j = max(0, $i - 2); $j < min(count($lines), $i + 60); $j++) {
            echo ($j + 1) . ": " . $lines[$j];
        }
        break;
    }
}
