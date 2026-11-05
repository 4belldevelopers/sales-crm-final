<?php
$content = file_get_contents('app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php');
$pos = strpos($content, 'function directclientCandidateInterviewTracker');
$posReturn = strpos($content, 'return response()->json(', $pos + 500);
if ($posReturn !== false) {
    echo substr($content, $posReturn - 1000, 1500);
}
