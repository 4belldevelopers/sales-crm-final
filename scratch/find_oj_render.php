<?php
$content = file_get_contents('resources/views/service_company/client/dviewMore.blade.php');
$pos = strpos($content, 'directfetchCandidatesJoin');
if ($pos !== false) {
    echo "Found directfetchCandidatesJoin at $pos:\n";
    echo substr($content, $pos, 1500) . "\n...\n";
}
