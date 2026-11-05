<?php
$content = file_get_contents('resources/views/service_company/ajax_pages/client_jobs_side_bar_ajax.blade.php');
$pos = strpos($content, 'cd-logo-box');
echo substr($content, $pos - 200, 1500);
