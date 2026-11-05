<?php
$a = file_get_contents(__DIR__ . '/../resources/views/service_company/consultancy/dviewMore.blade.php');
$pos = strpos($a, '@foreach ($jobsList as $job)');
$slice = substr($a, $pos, 3000);
echo $slice;
