<?php
$c = file_get_contents(__DIR__ . '/../resources/views/service_company/client/dviewMore.blade.php');

preg_match_all('/url\s*:\s*[`"\']([^`"\']+)[\'"`]/i', $c, $urls);
echo "ALL URLS IN CLIENT DRAWER:\n";
print_r(array_unique($urls[1]));
