<?php
$lines = file('app/Http/Controllers/ServiceCompany/ServiceCompanyConsultancyController.php');
for ($i = 1210; $i <= 1240; $i++) {
    echo ($i + 1) . ": " . $lines[$i];
}
