<?php
$lines = file('resources/views/service_company/consultancy/dviewMore.blade.php');
for ($i = 3395; $i <= 3425; $i++) {
    echo ($i + 1) . ": " . $lines[$i];
}
