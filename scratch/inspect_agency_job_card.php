<?php
$a = file_get_contents(__DIR__ . '/../resources/views/service_company/consultancy/dviewMore.blade.php');
$pos = strpos($a, 'id="requisitions"');
$slice = substr($a, $pos, 4000);
echo $slice;
