<?php
$content = file_get_contents('resources/views/service_company/ajax_pages/custom_client_candidates_join.blade.php');
$pos = strpos($content, 'Sub-Card 2');
echo substr($content, $pos, 1500);
