<?php
$lines = file('resources/views/service_company/consultancy/dviewMore.blade.php');

function showRange($lines, $start, $end) {
    for ($i = $start - 1; $i < min($end, count($lines)); $i++) {
        echo ($i + 1) . ": " . $lines[$i];
    }
}

echo "=== OFFERS & JOININGS CARD BODY (4030 to 4100) ===\n";
showRange($lines, 4030, 4100);
