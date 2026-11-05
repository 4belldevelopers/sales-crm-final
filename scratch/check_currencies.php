<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = \App\Models\User::whereNotNull('company_id')->first();
$companyId = $user->company_id;
$countryIds = \App\Services\CommercialReadModelService::getCompanyRegisteredCountryIds($companyId);
echo "Company ID: {$companyId}\n";
echo "Registered country IDs: " . json_encode($countryIds) . "\n";

$company = \Illuminate\Support\Facades\DB::table('companies')->where('id', $companyId)->first();
echo "Company country: " . ($company->country ?? 'none') . " | state: " . ($company->state ?? 'none') . " | city: " . ($company->city ?? 'none') . "\n";
