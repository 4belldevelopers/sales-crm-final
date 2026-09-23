<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = DB::table('users')->where('company_id', 2)->first();
auth()->loginUsingId($user->id);

$controller = app(\App\Http\Controllers\ServiceCompany\ServiceCompanyConsultancyController::class);
$res = $controller->dviewMore(5);

$ref = new ReflectionClass($controller);
$prop = $ref->getProperty('data');
$prop->setAccessible(true);
$data = $prop->getValue($controller);
print_r($data['directClientDetail']);
echo "\n--- counts ---\n";
print_r($data['counts']);
