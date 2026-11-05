<?php
$aFile = file_get_contents(__DIR__ . '/../resources/views/service_company/consultancy/dviewMore.blade.php');
preg_match('/<script\b[^>]*>(.*?)<\/script>/is', $aFile, $s);
echo substr($s[1], 0, 4000);
