<?php
$filesToCheck = [
    'app/Http/Controllers/ServiceCompany/ClientCandidateDrawerApiController.php',
    'app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php',
    'app/Http/Controllers/ServiceCompany/ServiceCompanyJobsController.php',
    'app/Http/Controllers/ServiceCompany/ServiceCompanyClientController.php'
];

foreach ($filesToCheck as $f) {
    if (file_exists($f)) {
        echo "Found: $f\n";
    } else {
        echo "NOT FOUND: $f\n";
    }
}
