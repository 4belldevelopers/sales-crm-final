<?php
$content = file_get_contents('resources/views/service_company/ajax_pages/custom_client_candidates_join.blade.php');
$pos = strpos($content, 'post_company_name');
echo substr($content, $pos - 200, 800);
