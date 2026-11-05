<?php
$content = file_get_contents('app/Services/Candidate/CandidateDrawerQueryService.php');
echo "File length: " . strlen($content) . "\n";
echo substr($content, 0, 2000) . "\n...\n";
