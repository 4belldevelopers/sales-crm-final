<?php
$content = file_get_contents('resources/views/service_company/consultancy/dviewMore.blade.php');
preg_match_all('/(?:route|url)\s*\(\s*[\'"]([^\'"]+)[\'"]/', $content, $m);
print_r(array_unique($m[1]));
