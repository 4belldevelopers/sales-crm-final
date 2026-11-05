<?php
$content = file_get_contents('resources/views/service_company/client/dviewMore.blade.php');
preg_match_all('/<div[^>]+class=["\'][^"\']*tab-pane[^"\']*["\'][^>]*id=["\']([^"\']+)["\']/', $content, $m2);
print_r($m2[1]);
