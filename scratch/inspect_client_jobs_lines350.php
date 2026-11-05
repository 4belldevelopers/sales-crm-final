<?php
$lines = file('app/Services/ServiceCompany/ClientJobsReadService.php');
for ($i = 350; $i <= min(420, count($lines) - 1); $i++) {
    echo ($i + 1) . ": " . $lines[$i];
}
