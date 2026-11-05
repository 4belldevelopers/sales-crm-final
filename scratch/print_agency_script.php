<?php
$content = file_get_contents('resources/views/service_company/consultancy/dviewMore.blade.php');
preg_match_all('/<script\b[^>]*>(.*?)<\/script>/is', $content, $scripts);
echo $scripts[1][0];
