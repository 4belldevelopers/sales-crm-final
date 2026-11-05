<?php
$lines = file('app/Services/ServiceCompany/ClientActivityReadService.php');
for ($i = 415; $i <= 470; $i++) {
    echo ($i + 1) . ": " . $lines[$i];
}
