<?php
$lines = file('app/Http/Controllers/SaasFront/SaasFrontController.php');
foreach ($lines as $i => $l) {
    if (stripos($l, 'function teamReg') !== false) {
        for ($j = max(0, $i - 5); $j < min(count($lines), $i + 150); $j++) {
            echo ($j + 1) . ": " . $lines[$j];
        }
        break;
    }
}
