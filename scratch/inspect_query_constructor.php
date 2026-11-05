<?php
$content = file_get_contents('app/DTO/ServiceCompany/ClientActivityQuery.php');
$pos = strpos($content, '__construct');
echo substr($content, $pos, 600);
