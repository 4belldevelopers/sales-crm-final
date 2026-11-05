<?php
$c = file_get_contents(__DIR__ . '/../resources/views/service_company/client/dviewMore.blade.php');

preg_match_all('/(?:route|url)\s*\(\s*[\'"]([^\'"]+)[\'"]/i', $c, $m);
echo "ROUTE / URL CALLS IN CLIENT DRAWER:\n";
print_r(array_unique($m[1]));
