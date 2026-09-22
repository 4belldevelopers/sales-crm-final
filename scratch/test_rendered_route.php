<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = DB::table('users')->where('company_id', 2)->first();
auth()->loginUsingId($user->id);

$c = app(App\Http\Controllers\ServiceCompany\ServiceCompanyConsultancyController::class);
$res = $c->dviewMore(5);
$viewStr = is_array($res) ? $res['view'] : (is_a($res, 'Illuminate\Http\JsonResponse') ? $res->getData(true)['view'] : '');

echo str_contains($viewStr, 'consultancy_ajax/dashboard') ? "FOUND CORRECT ROUTE IN VIEW\n" : "NOT FOUND\n";
echo str_contains($viewStr, 'service_company_ajax/dashboard') ? "STILL HAS OLD ROUTE\n" : "OLD ROUTE GONE\n";
