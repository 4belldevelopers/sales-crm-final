<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$dto3 = App\Services\CommercialReadModelService::getTenantCommercialsState(3);
echo "Agency 3: FT status=" . $dto3->fulltime->status . ", CT status=" . $dto3->contract->status . PHP_EOL;

$dto6 = App\Services\CommercialReadModelService::getTenantCommercialsState(6);
echo "Agency 6: FT status=" . $dto6->fulltime->status . ", CT status=" . $dto6->contract->status . PHP_EOL;

$dto1 = App\Services\CommercialReadModelService::getTenantCommercialsState(1);
echo "Agency 1: FT status=" . $dto1->fulltime->status . ", CT status=" . $dto1->contract->status . PHP_EOL;
