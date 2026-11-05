<?php
$content = file_get_contents('resources/views/service_company/ajax_pages/custom_client_candidates_join.blade.php');
echo "Length: " . strlen($content) . "\n";
preg_match_all('/company[^\n]+/i', $content, $m);
foreach (array_slice($m[0], 0, 15) as $line) {
    echo "$line\n";
}
