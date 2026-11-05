<?php
$content = file_get_contents('resources/views/service_company/client/dviewMore.blade.php');
$pos = strpos($content, '<div class="cd-cand-card"');
if ($pos !== false) {
    echo substr($content, $pos + 3800, 2500);
}
