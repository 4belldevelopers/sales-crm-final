<?php
$content = file_get_contents('app/Services/ServiceCompany/ClientJobsReadService.php');
$pos = strpos($content, 'new JobsTabViewModel');
if ($pos !== false) {
    echo substr($content, $pos - 1500, 2000);
}
