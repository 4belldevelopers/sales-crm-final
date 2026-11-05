<?php
$content = file_get_contents('app/Http/Controllers/ServiceCompany/ServiceCompanyJobsController.php');
$pos = strpos($content, 'function client_job_data_ajax');
if ($pos !== false) {
    echo "Found client_job_data_ajax at $pos:\n";
    echo substr($content, $pos, 2500) . "\n...\n";
}
