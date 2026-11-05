<?php
$lines = file('resources/views/service_company/consultancy/dviewMore.blade.php');
$total = count($lines);
for ($i = 0; $i < $total; $i++) {
    if (strpos($lines[$i], '$jobsList') !== false || strpos($lines[$i], '$candsList') !== false) {
        echo "Blade Line " . ($i + 1) . ": " . trim($lines[$i]) . "\n";
    }
}
