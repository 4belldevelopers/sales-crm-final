<?php
$lines = file('resources/views/service_company/consultancy/dviewMore.blade.php');

function showRange($lines, $start, $end) {
    for ($i = $start - 1; $i < min($end, count($lines)); $i++) {
        echo ($i + 1) . ": " . $lines[$i];
    }
}

echo "=== CANDIDATE CARD (3580 to 3650) ===\n";
showRange($lines, 3580, 3650);
