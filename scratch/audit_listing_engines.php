<?php
$cc = file_get_contents(__DIR__ . '/../app/Http/Controllers/ServiceCompany/ServiceCompanyClientController.php');
$ac = file_get_contents(__DIR__ . '/../app/Http/Controllers/ServiceCompany/ServiceCompanyConsultancyController.php');

$posCc = strpos($cc, 'function all_clients_data_ajax');
$sliceCc = substr($cc, $posCc, 4000);

echo "CLIENT LISTING BACKEND:\n";
preg_match_all('/(ElasticService|ELASTIC_[A-Z_]+|DB::table|cache\(\)->)/', $sliceCc, $m1);
print_r(array_unique($m1[0]));

$posAc = strpos($ac, 'function data_ajax(Request $request)');
$sliceAc = substr($ac, $posAc, 4000);

echo "\nAGENCY LISTING BACKEND:\n";
preg_match_all('/(ElasticService|ELASTIC_[A-Z_]+|DB::table|cache\(\)->)/', $sliceAc, $m2);
print_r(array_unique($m2[0]));
