<?php
$lines = file('resources/views/service_company/consultancy/dviewMore.blade.php');
for ($i = 4050; $i <= 4105; $i++) {
    echo ($i + 1) . ": " . $lines[$i];
}
