<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::where('company_id', 2)->first();
auth()->login($user);

$controller = app(\App\Http\Controllers\ServiceCompany\ServiceCompanyConsultancyController::class);

try {
    $response = $controller->dviewMore(3);
    dump('Response type:', is_array($response) ? 'array' : get_class($response));
    if (is_array($response)) {
        dump('Keys:', array_keys($response));
        dump('View length:', strlen($response['view']));
    }
} catch (\Throwable $e) {
    dump('Exception: ' . $e->getMessage(), $e->getTraceAsString());
}
