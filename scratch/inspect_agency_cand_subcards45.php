<?php
$lines = file('resources/views/service_company/consultancy/dviewMore.blade.php');
for ($i = 3668; $i <= 3690; $i++) {
    echo ($i + 1) . ": " . $lines[$i];
}
