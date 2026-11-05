<?php
$content = file_get_contents('app/Services/Candidate/CandidateDrawerQueryService.php');
$pos = strpos($content, "'direct'");
while ($pos !== false) {
    echo "Found 'direct' at $pos:\n";
    echo substr($content, $pos - 50, 400) . "\n---\n";
    $pos = strpos($content, "'direct'", $pos + 1);
}
