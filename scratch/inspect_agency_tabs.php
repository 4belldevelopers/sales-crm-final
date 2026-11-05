<?php
$content = file_get_contents('resources/views/service_company/consultancy/dviewMore.blade.php');
echo 'Length: ' . strlen($content) . "\n";
preg_match_all('/id=["\']([a-zA-Z0-9_-]+)["\']/', $content, $m);
$ids = array_filter($m[1], function($id) {
    return str_contains($id, 'tab') || str_contains($id, 'jobs') || str_contains($id, 'candidates') || str_contains($id, 'interview') || str_contains($id, 'offer') || str_contains($id, 'performance');
});
print_r(array_values(array_unique($ids)));
