<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$u = DB::table('users')->where('id', 99992)->first();
print_r($u);

$repService = app(App\Services\CompanyRepresentativeService::class);
$roles = $repService->getAllCompanyCommunicationRoles(2);
echo "Roles map:\n";
print_r($roles);
