<?php
$a = file_get_contents(__DIR__ . '/../resources/views/service_company/consultancy/index.blade.php');
$c = file_get_contents(__DIR__ . '/../resources/views/service_company/client/index.blade.php');

$aContent = substr($a, strpos($a, '@section(\'content\')'), 4000);
$cContent = substr($c, strpos($c, '@section(\'content\')'), 4000);

file_put_contents(__DIR__ . '/listing_sections.txt', "=== AGENCY CONTENT ===\n" . $aContent . "\n\n=== CLIENT CONTENT ===\n" . $cContent);
echo "Written listing_sections.txt\n";
