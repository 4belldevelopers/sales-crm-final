<?php
$lines = file('app/Services/ServiceCompany/ClientJobsReadService.php');
for ($i = 250; $i <= min(320, count($lines) - 1); $i++) {
    echo ($i + 1) . ": " . $lines[$i];
}
