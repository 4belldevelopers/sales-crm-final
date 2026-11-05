<?php
$lines = file('app/Http/Controllers/ServiceCompany/ServiceCompanyConsultancyController.php');
$total = count($lines);
for ($i = 0; $i < $total; $i++) {
    if (strpos($lines[$i], 'jobsList') !== false || strpos($lines[$i], 'candsList') !== false) {
        echo "Line " . ($i + 1) . ": " . trim($lines[$i]) . "\n";
    }
}
