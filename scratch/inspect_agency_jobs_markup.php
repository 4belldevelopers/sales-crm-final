<?php
$lines = file('resources/views/service_company/consultancy/dviewMore.blade.php');
for ($i = 3350; $i <= 3430; $i++) {
    echo ($i + 1) . ": " . $lines[$i];
}
