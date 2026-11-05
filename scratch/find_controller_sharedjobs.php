<?php
$lines = file('app/Http/Controllers/ServiceCompany/ServiceCompanyConsultancyController.php');
$total = count($lines);
for ($i = 0; $i < $total; $i++) {
    if (strpos($lines[$i], 'sharedJobs') !== false || strpos($lines[$i], 'candidatesList') !== false) {
        echo "Controller Line " . ($i + 1) . ": " . trim($lines[$i]) . "\n";
    }
}
