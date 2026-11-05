<?php
$lines = file('resources/views/service_company/ajax_pages/client_jobs_side_bar_ajax.blade.php');
for ($i = 58; $i <= 72; $i++) {
    echo ($i + 1) . ": " . $lines[$i];
}
