<?php
$content = file_get_contents('resources/views/service_company/consultancy/dviewMore.blade.php');
preg_match_all('/<button[^>]+data-bs-target=["\']#([^"\']+)["\']/', $content, $m);
print_r($m[1]);

preg_match_all('/<div[^>]+class=["\'][^"\']*tab-pane[^"\']*["\'][^>]*id=["\']([^"\']+)["\']/', $content, $m2);
print_r($m2[1]);
