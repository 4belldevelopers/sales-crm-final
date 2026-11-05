<?php
$c = file_get_contents(__DIR__ . '/../resources/views/service_company/client/dviewMore.blade.php');
$pos = strpos($c, 'id="requisitions"');
$slice = substr($c, $pos, 8000);

preg_match_all('/<div[^>]+id=["\']([^"\']+)["\']/i', $slice, $ids);
echo "IDs in requisitions slice:\n";
print_r($ids[1]);

preg_match_all('/\$\.ajax\(\{\s*url\s*:\s*([^,\n]+)/i', $slice, $aj);
echo "AJAX calls in slice:\n";
print_r($aj[1]);
