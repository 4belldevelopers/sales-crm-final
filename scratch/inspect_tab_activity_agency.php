<?php
$a = file_get_contents(__DIR__ . '/../resources/views/service_company/consultancy/dviewMore.blade.php');
$pos = strpos($a, 'tab-activity');
echo substr($a, $pos - 100, 400);
