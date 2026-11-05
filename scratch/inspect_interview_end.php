<?php
$content = file_get_contents('app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php');
$pos = strpos($content, 'function directclientCandidateInterviewTracker');
$pos2 = strpos($content, 'function ', $pos + 50);
if ($pos2 !== false) {
    echo substr($content, $pos2 - 1200, 1200);
}
