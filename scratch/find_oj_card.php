<?php
$content = file_get_contents('resources/views/service_company/client/dviewMore.blade.php');
$pos = strpos($content, 'cd-oj-card');
if ($pos !== false) {
    echo "Found cd-oj-card at $pos:\n";
    echo substr($content, $pos - 50, 600) . "\n...\n";
}
