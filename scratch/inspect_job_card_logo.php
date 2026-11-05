<?php
$content = file_get_contents('resources/views/service_company/ajax_pages/client_jobs_side_bar_ajax.blade.php');
$pos = strpos($content, 'cd-job-logo-box');
if ($pos !== false) {
    echo substr($content, $pos - 100, 1000) . "\n...\n";
} else {
    echo "cd-job-logo-box not found\n";
    preg_match_all('/class=[\'"][^\'"]*logo[^\'"]*[\'"]/', $content, $m);
    print_r($m[0]);
}
