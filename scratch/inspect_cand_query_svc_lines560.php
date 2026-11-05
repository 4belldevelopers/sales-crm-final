<?php
$lines = file('app/Services/Candidate/CandidateDrawerQueryService.php');
for ($i = 560; $i <= min(620, count($lines) - 1); $i++) {
    echo ($i + 1) . ": " . $lines[$i];
}
