<?php
$lines = file('app/DTO/ServiceCompany/DirectClientCandidateDTO.php');
for ($i = 50; $i <= min(85, count($lines) - 1); $i++) {
    echo ($i + 1) . ": " . $lines[$i];
}
