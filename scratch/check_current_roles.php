<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$companyId = 2;
$repService = app(App\Services\CompanyRepresentativeService::class);
$rolesMap = $repService->getAllCompanyCommunicationRoles($companyId);
echo "Roles map for company 2:\n";
print_r($rolesMap);

$defaults = $repService->getDefaultRepresentatives($companyId);
echo "\nDefaults:\n";
print_r($defaults);
