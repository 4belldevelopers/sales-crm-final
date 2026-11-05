<?php
$lines = file('app/Http/Controllers/ServiceCompany/ServiceCompanyConsultancyController.php');
for ($i = 0; $i < count($lines); $i++) {
    if (strpos($lines[$i], 'dviewMore') !== false && strpos($lines[$i], 'view(') !== false) {
        echo "Line " . ($i + 1) . ": " . trim($lines[$i]) . "\n";
    }
}
