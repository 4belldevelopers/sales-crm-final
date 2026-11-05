<?php
$c = file_get_contents(__DIR__ . '/../resources/views/service_company/client/dviewMore.blade.php');
$a = file_get_contents(__DIR__ . '/../resources/views/service_company/consultancy/dviewMore.blade.php');

preg_match_all('/url\s*:\s*["\']([^"\']+)["\']/i', $c, $mc);
echo "CLIENT DRAWER AJAX URLS:\n";
print_r(array_unique($mc[1]));

preg_match_all('/url\s*:\s*["\']([^"\']+)["\']/i', $a, $ma);
echo "\nAGENCY DRAWER AJAX URLS:\n";
print_r(array_unique($ma[1]));
