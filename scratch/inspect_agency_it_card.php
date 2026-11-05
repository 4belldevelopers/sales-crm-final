<?php
$lines = file('resources/views/service_company/consultancy/dviewMore.blade.php');

function showRange($lines, $start, $end) {
    for ($i = $start - 1; $i < min($end, count($lines)); $i++) {
        echo ($i + 1) . ": " . $lines[$i];
    }
}

echo "=== INTERVIEW CARD DETAILS (3800 to 3870) ===\n";
showRange($lines, 3800, 3870);
