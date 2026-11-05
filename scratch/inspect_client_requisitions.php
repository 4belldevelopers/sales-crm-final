<?php
$c = file_get_contents(__DIR__ . '/../resources/views/service_company/client/dviewMore.blade.php');
$pos = strpos($c, 'id="requisitions"');
$slice = substr($c, $pos, 3000);
echo $slice;
