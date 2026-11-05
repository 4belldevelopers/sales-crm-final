<?php
$content = file_get_contents('app/Http/Controllers/ServiceCompany/ClientCandidateDrawerApiController.php');
echo "File length: " . strlen($content) . "\n";
preg_match('/public function directclientCandidateList\b[^{]*\{/', $content, $m, PREG_OFFSET_CAPTURE);
if ($m) {
    $start = $m[0][1];
    echo substr($content, $start, 2500) . "\n...\n";
}
