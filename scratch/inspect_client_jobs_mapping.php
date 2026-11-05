<?php
$content = file_get_contents('app/Services/ServiceCompany/ClientJobsReadService.php');
$pos = strpos($content, 'foreach ($rawJobs as $job)');
if ($pos !== false) {
    echo substr($content, $pos, 2500);
} else {
    // search for array_map or loop over rawJobs
    preg_match('/\$rawJobs[^\n]+/i', $content, $m, PREG_OFFSET_CAPTURE);
    if ($m) {
        echo substr($content, $m[0][1], 2500);
    }
}
