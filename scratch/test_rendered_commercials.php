<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::where('company_id', 2)->first();
auth()->login($user);

$controller = app(\App\Http\Controllers\ServiceCompany\ServiceCompanyConsultancyController::class);

$response = $controller->dviewMore(3);
$data = is_array($response) ? $response : $response->getData(true);
$html = $data['view'];

$start = strpos($html, '<!-- Commercial Terms Cards');
$end = strpos($html, '<!-- ══════════════════════════════════════════════════════════════════════', $start);
echo substr($html, $start, $end - $start);
