<?php
$c = file_get_contents(__DIR__ . '/../app/Http/Controllers/ServiceCompany/ServiceCompanyConsultancyController.php');
$lines = explode("\n", $c);
foreach ($lines as $i => $l) {
    if (strpos($l, 'sharedJob') !== false || strpos($l, 'job_location') !== false || strpos($l, 'Jobs breakdown') !== false) {
        echo "Line " . ($i+1) . ": $l\n";
    }
}
