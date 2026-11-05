<?php
$lines = file('resources/views/service_company/client/dviewMore.blade.php');
for ($i = 8474; $i <= 8510; $i++) {
    echo ($i + 1) . ": " . $lines[$i];
}
