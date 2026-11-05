<?php
$lines = file('resources/views/service_company/consultancy/dviewMore.blade.php');
for ($i = 3850; $i <= 3865; $i++) {
    echo ($i + 1) . ": " . $lines[$i];
}
