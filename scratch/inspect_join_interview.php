<?php
$content = file_get_contents('app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php');
foreach (['directclientCandidateJoinList', 'directclientCandidateInterviewTracker'] as $fn) {
    preg_match('/public function ' . $fn . '\b[^{]*\{/', $content, $m, PREG_OFFSET_CAPTURE);
    if ($m) {
        $start = $m[0][1];
        echo "=== Function: $fn ===\n";
        echo substr($content, $start, 2000) . "\n...\n";
    }
}
