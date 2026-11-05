<?php
$a = file_get_contents(__DIR__ . '/../resources/views/service_company/consultancy/index.blade.php');

preg_match_all('/<div[^>]+id=["\']([^"\']+)["\']/is', substr($a, 0, 10000), $m);
echo "AGENCY IDS IN FIRST 10K:\n";
print_r(array_slice($m[1], 0, 30));

preg_match_all('/class=["\']([^"\']*(?:card|table|row|item|list)[^"\']*)["\']/is', substr($a, 0, 10000), $mc);
echo "AGENCY CLASSES IN FIRST 10K:\n";
print_r(array_unique(array_slice($mc[1], 0, 20)));
