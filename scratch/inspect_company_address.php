<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$c = \App\Models\Company::withoutGlobalScopes()->find(1);
echo "Company 1: " . json_encode([
    'address' => $c->address,
    'calling_code' => $c->calling_code,
    'company_phone' => $c->company_phone,
    'company_name' => $c->company_name,
    'timezone' => $c->timezone,
]) . PHP_EOL;

// Check job_locations
$locs = \DB::table('job_locations')->where('company_id', 1)->get();
echo "Job locations: " . json_encode($locs) . PHP_EOL;

// Check users for company 1
$u = \App\Models\User::withoutGlobalScopes()->where('company_id', 1)->first();
echo "User 1: " . json_encode([
    'calling_code' => $u->calling_code ?? null,
    'country_id' => $u->country_id ?? null,
    'address' => $u->address ?? null,
]) . PHP_EOL;

// Check countries table
$sampleCountries = \DB::table('countries')->limit(5)->get();
echo "Sample countries: " . json_encode($sampleCountries) . PHP_EOL;
