<?php
$lines = file('app/Http/Controllers/ServiceCompany/ServiceCompanyConsultancyController.php');
for ($i = 1310; $i <= 1345; $i++) {
    echo ($i + 1) . ": " . $lines[$i];
}
