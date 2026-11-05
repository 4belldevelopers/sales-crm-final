<?php
$c = file_get_contents(__DIR__ . '/../app/Services/ServiceCompany/ClientActivityReadService.php');
$lines = explode("\n", $c);
for ($i = 645; $i <= 675; $i++) {
    echo ($i+1) . ": " . $lines[$i] . "\n";
}
