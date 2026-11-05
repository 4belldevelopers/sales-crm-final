<?php
$lines = file('resources/views/service_company/consultancy/dviewMore.blade.php');

function showRange($lines, $start, $end) {
    for ($i = $start - 1; $i < min($end, count($lines)); $i++) {
        echo ($i + 1) . ": " . $lines[$i];
    }
}

echo "=== CANDIDATE LIST (Lines 3512 to 3560) ===\n";
showRange($lines, 3512, 3560);
