<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$state = \App\Services\CommercialReadModelService::getTenantCommercialsState(3);
dump('State for 3:', [
    'fulltime' => $state->fulltime,
    'contract' => $state->contract,
]);
