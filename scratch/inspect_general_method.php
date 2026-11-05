<?php
$gm = file_get_contents(__DIR__ . '/../app/Helper/GeneralMethod.php');
$pos1 = strpos($gm, 'function DirectClientJobsCountAllStatus');
$pos2 = strpos($gm, 'function DirectClientCandidatesCountList');

echo "DirectClientJobsCountAllStatus:\n";
echo substr($gm, $pos1, 1000) . "\n\n";

echo "DirectClientCandidatesCountList:\n";
echo substr($gm, $pos2, 1000) . "\n\n";
