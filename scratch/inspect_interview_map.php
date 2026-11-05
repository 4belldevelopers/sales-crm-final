<?php
$content = file_get_contents('app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php');
$pos = strpos($content, '$rawSourceData = array_map');
echo substr($content, $pos, 1500);
