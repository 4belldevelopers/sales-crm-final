<?php
$content = file_get_contents('resources/views/service_company/team/index.blade.php');
echo "File length: " . strlen($content) . " bytes\n";

preg_match_all('/function\s+([a-zA-Z0-9_]+)/', $content, $m);
echo "Functions found: " . count($m[1]) . "\n";
print_r(array_slice(array_unique($m[1]), 0, 50));

if (preg_match_all('/teamState[a-zA-Z0-9_.]*/', $content, $m2)) {
    echo "teamState matches: " . count($m2[0]) . "\n";
} else {
    echo "No teamState matches\n";
}
