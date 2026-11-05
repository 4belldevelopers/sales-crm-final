<?php
$content = file_get_contents('resources/views/service_company/ajax_pages/client_jobs_side_bar_ajax.blade.php');
$pos = strpos($content, '$logoUrl');
echo substr($content, $pos - 500, 1000);
