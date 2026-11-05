<?php
$content = file_get_contents('resources/views/service_company/client/dviewMore.blade.php');
preg_match_all('/<script\b[^>]*>(.*?)<\/script>/is', $content, $scripts);
$script4 = $scripts[1][4];

$cards = ['cd-job-card', 'cd-cand-card', 'cd-it-card', 'cd-oj-card'];
foreach ($cards as $c) {
    $pos = strpos($script4, $c);
    if ($pos !== false) {
        echo "=== Found $c at $pos ===\n";
        echo substr($script4, $pos, 800) . "\n...\n";
    }
}
