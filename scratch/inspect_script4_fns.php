<?php
$content = file_get_contents('resources/views/service_company/client/dviewMore.blade.php');
preg_match_all('/<script\b[^>]*>(.*?)<\/script>/is', $content, $scripts);
$script4 = $scripts[1][4];
preg_match_all('/function\s+([a-zA-Z0-9_]+)\s*\(/', $script4, $fns);
print_r(array_unique($fns[1]));
