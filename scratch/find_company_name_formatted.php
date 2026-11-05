<?php
$content = file_get_contents('resources/views/service_company/client/dviewMore.blade.php');
$pos = strpos($content, 'companyNameFormatted');
while ($pos !== false) {
    echo substr($content, $pos - 100, 300) . "\n---\n";
    $pos = strpos($content, 'companyNameFormatted', $pos + 1);
}
