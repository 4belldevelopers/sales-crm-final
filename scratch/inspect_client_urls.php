<?php
$content = file_get_contents('resources/views/service_company/client/dviewMore.blade.php');
preg_match_all('/url:\s*["\']([^"\']+)["\']/', $content, $m);
print_r(array_unique($m[1]));
