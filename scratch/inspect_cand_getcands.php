<?php
$content = file_get_contents('app/Services/Candidate/CandidateDrawerQueryService.php');
$pos = strpos($content, 'function getCandidates');
echo substr($content, $pos, 3000);
