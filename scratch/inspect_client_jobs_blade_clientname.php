<?php
$lines = file('resources/views/service_company/ajax_pages/client_jobs_side_bar_ajax.blade.php');
for ($i = 30; $i <= 65; $i++) {
    echo ($i + 1) . ": " . $lines[$i];
}
