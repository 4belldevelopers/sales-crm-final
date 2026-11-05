<?php
$lines = file('resources/views/service_company/client/dviewMore.blade.php');
for ($i = 0; $i < count($lines); $i++) {
    if (strpos($lines[$i], 'cur_company_name || row.client_name') !== false) {
        echo "Line " . ($i + 1) . ": " . trim($lines[$i]) . "\n";
        for ($j = max(0, $i - 5); $j <= min(count($lines) - 1, $i + 5); $j++) {
            echo "  " . ($j + 1) . ": " . $lines[$j];
        }
    }
}
