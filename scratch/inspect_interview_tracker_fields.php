<?php
$content = file_get_contents('app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php');
$pos = strpos($content, 'function directclientCandidateInterviewTracker');
echo substr($content, $pos, 2500);
