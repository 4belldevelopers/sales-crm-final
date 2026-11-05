<?php
$a = file_get_contents(__DIR__ . '/../resources/views/service_company/consultancy/index.blade.php');
$c = file_get_contents(__DIR__ . '/../resources/views/service_company/client/index.blade.php');

$posA = strpos($a, '@section(\'content\')');
echo "Agency @section('content') at char: $posA (line " . substr_count(substr($a, 0, $posA), "\n") . ")\n";

$posC = strpos($c, '@section(\'content\')');
echo "Client @section('content') at char: $posC (line " . substr_count(substr($c, 0, $posC), "\n") . ")\n";
