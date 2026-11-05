<?php
$content = file_get_contents('resources/views/service_company/ajax_pages/client_jobs_side_bar_ajax.blade.php');
echo "File length: " . strlen($content) . "\n";
echo substr($content, 0, 1500) . "\n...\n";
