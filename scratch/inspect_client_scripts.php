<?php
$content = file_get_contents('resources/views/service_company/client/dviewMore.blade.php');
preg_match_all('/<script\b[^>]*>(.*?)<\/script>/is', $content, $scripts);
echo "Total scripts in client/dviewMore.blade.php: " . count($scripts[1]) . "\n";
foreach ($scripts[1] as $idx => $script) {
    echo "--- Script $idx (Length: " . strlen($script) . ") ---\n";
    echo substr(trim($script), 0, 400) . "\n...\n";
}
