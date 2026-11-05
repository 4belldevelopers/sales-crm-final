<?php
$c = file_get_contents(__DIR__ . '/../resources/views/service_company/client/dviewMore.blade.php');
$a = file_get_contents(__DIR__ . '/../resources/views/service_company/consultancy/dviewMore.blade.php');

preg_match_all("/@include\s*\([^)]+\)/", $c, $mc);
echo "=== CLIENT DRAWER INCLUDES ===\n" . implode("\n", array_unique($mc[0])) . "\n\n";

preg_match_all("/@include\s*\([^)]+\)/", $a, $ma);
echo "=== AGENCY DRAWER INCLUDES ===\n" . implode("\n", array_unique($ma[0])) . "\n\n";

// Now check listing views
$cl = file_get_contents(__DIR__ . '/../resources/views/service_company/client/index.blade.php');
$al = file_get_contents(__DIR__ . '/../resources/views/service_company/consultancy/index.blade.php');

preg_match_all("/@include\s*\([^)]+\)/", $cl, $mcl);
echo "=== CLIENT LISTING INCLUDES ===\n" . implode("\n", array_unique($mcl[0])) . "\n\n";

preg_match_all("/@include\s*\([^)]+\)/", $al, $mal);
echo "=== AGENCY LISTING INCLUDES ===\n" . implode("\n", array_unique($mal[0])) . "\n";
