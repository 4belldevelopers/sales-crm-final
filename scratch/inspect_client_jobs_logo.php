<?php
$content = file_get_contents('app/Services/ServiceCompany/ClientJobsReadService.php');
$pos = strpos($content, 'logoMap');
while ($pos !== false) {
    echo "Found 'logoMap' at $pos:\n";
    echo substr($content, $pos - 50, 400) . "\n---\n";
    $pos = strpos($content, 'logoMap', $pos + 1);
}
