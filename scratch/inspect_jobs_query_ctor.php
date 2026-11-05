<?php
$content = file_get_contents('app/DTO/ServiceCompany/ClientJobsQuery.php');
preg_match('/__construct\([^)]+\)/s', $content, $m);
echo $m[0];
