<?php
$content = file_get_contents('app/Services/ServiceCompany/ClientJobsReadService.php');
$pos = strpos($content, 'new JobsTabViewModel');
$pos2 = strpos($content, 'new JobsTabViewModel', $pos + 20);
if ($pos2 !== false) {
    echo substr($content, $pos2 - 1500, 2500);
}
