<?php
$content = file_get_contents('resources/views/service_company/client/dviewMore.blade.php');
preg_match_all('/<script\b[^>]*>(.*?)<\/script>/is', $content, $scripts);
$script4 = $scripts[1][4];
echo "Script 4 first 1500 chars:\n" . substr($script4, 0, 1500) . "\n";
