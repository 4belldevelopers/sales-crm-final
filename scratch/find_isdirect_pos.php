<?php
$c = file_get_contents(__DIR__ . '/../app/Services/ServiceCompany/ClientActivityReadService.php');
$pos1 = strpos($c, '$isDirect = ($query->clientType === \'direct\');');
$pos2 = strpos($c, '$isDirect = ($query->clientType === \'direct\');', $pos1 + 10);

echo "POS 1: $pos1\n";
echo substr($c, $pos1 - 60, 200) . "\n\n";

echo "POS 2: $pos2\n";
echo substr($c, $pos2 - 60, 200) . "\n\n";
