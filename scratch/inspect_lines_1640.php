<?php
$lines = file('app/Services/ServiceCompany/ClientActivityReadService.php');
for ($i = 1639; $i <= 1665; $i++) {
    echo ($i + 1) . ": " . $lines[$i];
}
