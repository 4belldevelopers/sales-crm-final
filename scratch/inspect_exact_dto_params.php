<?php
$content = file_get_contents('app/DTO/ServiceCompany/ClientActivityQuery.php');
preg_match('/__construct\([^)]+\)/s', $content, $m);
echo $m[0];
