<?php
$lines = file('app/Http/Controllers/ServiceCompany/ServiceCompanyConsultancyController.php');
for ($i = 1200; $i <= 1300; $i++) {
    if (isset($lines[$i]) && strpos($lines[$i], 'company') !== false) {
        echo ($i + 1) . ": " . trim($lines[$i]) . "\n";
    }
}
