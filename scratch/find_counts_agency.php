<?php
$lines = file(__DIR__ . '/../app/Http/Controllers/ServiceCompany/ServiceCompanyConsultancyController.php');
foreach ($lines as $i => $l) {
    if (strpos($l, 'sourcing_count') !== false || strpos($l, 'ActiveJobs') !== false) {
        echo "Line " . ($i + 1) . ": " . trim($l) . "\n";
    }
}
