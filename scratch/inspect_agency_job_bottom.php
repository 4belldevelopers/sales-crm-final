<?php
$lines = file('resources/views/service_company/consultancy/dviewMore.blade.php');
for ($i = 3485; $i <= 3505; $i++) {
    echo ($i + 1) . ": " . $lines[$i];
}
