<?php

$create = file_get_contents("resources/views/service_company/client/create.blade.php");
$edit = file_get_contents("resources/views/service_company/account/edit.blade.php");

preg_match_all('/data-wizard-type="step-content"[^>]*>(.*?)(?=<div[^>]*data-wizard-type="step-content"|\z)/s', $create, $stepsCreate);
preg_match_all('/data-wizard-type="step-content"[^>]*>(.*?)(?=<div[^>]*data-wizard-type="step-content"|\z)/s', $edit, $stepsEdit);

echo "Step count in create: " . count($stepsCreate[0]) . PHP_EOL;
foreach ($stepsCreate[1] as $idx => $content) {
    preg_match('/<h[1-5][^>]*>(.*?)<\/h[1-5]>/i', $content, $h);
    echo "  Create Step " . ($idx+1) . ": " . strip_tags($h[1] ?? 'No title') . PHP_EOL;
}

echo "Step count in edit: " . count($stepsEdit[0]) . PHP_EOL;
foreach ($stepsEdit[1] as $idx => $content) {
    preg_match('/<h[1-5][^>]*>(.*?)<\/h[1-5]>/i', $content, $h);
    echo "  Edit Step " . ($idx+1) . ": " . strip_tags($h[1] ?? 'No title') . PHP_EOL;
}
