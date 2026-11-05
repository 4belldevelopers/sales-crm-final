<?php
$content = file_get_contents('resources/views/service_company/client/dviewMore.blade.php');
$pos = strpos($content, 'cd-cand-subcard-requisition');
if ($pos !== false) {
    echo substr($content, $pos - 50, 1200);
} else {
    // search for jobTitle or similar in candidate card
    $pos = strpos($content, 'cd-cand-card');
    echo substr($content, $pos, 2500);
}
