<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo route('service_company.consultancy.dashboard_ajax', 5) . "\n";
echo route('service_company.consultancy.allreq_ajax', 5) . "\n";
