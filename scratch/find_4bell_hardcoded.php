<?php
$lines = file('resources/views/service_company/consultancy/dviewMore.blade.php');
for ($i = 0; $i < count($lines); $i++) {
    if (strpos($lines[$i], '4Bell') !== false || strpos($lines[$i], '4bell') !== false) {
        echo "Line " . ($i + 1) . ": " . trim($lines[$i]) . "\n";
    }
}
