<?php
$c = file_get_contents(__DIR__ . '/../app/Http/Controllers/ServiceCompany/ServiceCompanyConsultancyController.php');
$pos = strpos($c, 'function dviewMore');
if ($pos !== false) {
    echo substr($c, $pos, 3500);
} else {
    echo "Not found";
}
