<?php
$a = file_get_contents(__DIR__ . '/../resources/views/service_company/consultancy/dviewMore.blade.php');
preg_match_all('/@include\([\'"][^\'"]*tab-activity[^\'"]*[\'"][^)]*\)/', $a, $m);
print_r($m[0]);
