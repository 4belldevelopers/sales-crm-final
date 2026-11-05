<?php
$content = file_get_contents('resources/views/service_company/client/dviewMore.blade.php');
preg_match_all('/<script\b[^>]*>(.*?)<\/script>/is', $content, $scripts);
$script4 = $scripts[1][4];

// Search for candidate card markup generation
$pos = strpos($script4, 'cd-cand-card');
if ($pos !== false) {
    echo "Found cd-cand-card at $pos:\n";
    echo substr($script4, $pos - 100, 1500) . "\n...\n";
} else {
    echo "cd-cand-card not found in script 4\n";
}
