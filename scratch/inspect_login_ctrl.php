<?php
$lines = file('app/Http/Controllers/Auth/LoginController.php');
foreach ($lines as $i => $l) {
    if (stripos($l, 'function login') !== false) {
        for ($j = max(0, $i - 2); $j < min(count($lines), $i + 60); $j++) {
            echo ($j + 1) . ": " . $lines[$j];
        }
        break;
    }
}
