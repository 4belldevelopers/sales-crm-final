<?php
$lines = file('app/Services/ServiceCompany/ClientJobsReadService.php');
for ($i = 412; $i <= 425; $i++) {
    echo ($i + 1) . ": " . $lines[$i];
}
