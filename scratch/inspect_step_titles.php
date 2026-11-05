<?php

$create = file_get_contents("resources/views/service_company/client/create.blade.php");
$edit = file_get_contents("resources/views/service_company/account/edit.blade.php");

preg_match_all('/class="[^"]*step[^"]*"[^>]*data-wizard-type="step"/i', $create, $stCreate);
preg_match_all('/class="[^"]*step[^"]*"[^>]*data-wizard-type="step"/i', $edit, $stEdit);

preg_match_all('/<div class="wizard-step-label">\s*<h3 class="wizard-step-title">(.*?)<\/h3>/s', $create, $titlesCreate);
preg_match_all('/<div class="wizard-step-label">\s*<h3 class="wizard-step-title">(.*?)<\/h3>/s', $edit, $titlesEdit);

echo "Create wizard step titles: " . json_encode(array_map('trim', $titlesCreate[1])) . PHP_EOL;
echo "Edit wizard step titles: " . json_encode(array_map('trim', $titlesEdit[1])) . PHP_EOL;
