<?php
$lines = file('resources/views/service_company/consultancy/dviewMore.blade.php');

function showRange($lines, $start, $end) {
    for ($i = $start - 1; $i < min($end, count($lines)); $i++) {
        echo ($i + 1) . ": " . $lines[$i];
    }
}

echo "=== OFFERS & JOININGS IN AGENCY DRAWER (3950 to 4020) ===\n";
showRange($lines, 3950, 4020);
