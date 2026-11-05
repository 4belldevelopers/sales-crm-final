<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

function testAgencySync($partnerCompanyId, $company_id = 2) {
    $agencyTermsSpecific = DB::table('agency_commercial_terms')->where('company_id', $partnerCompanyId)->first();
    $agencyTermsFallback = DB::table('agency_commercial_terms')->where('company_id', $company_id)->first();
    
    $commStateDTO = App\Services\CommercialReadModelService::getTenantCommercialsState($partnerCompanyId);
    $ftDTO = $commStateDTO->fulltime;
    $ctDTO = $commStateDTO->contract;

    $isContractActive = ($ctDTO && (int) $ctDTO->status === 1) || (! empty($agencyTermsSpecific) && ! empty($agencyTermsSpecific->contract_billing_model));
    $isFulltimeActive = ($ftDTO && (int) $ftDTO->status === 1) || (! empty($agencyTermsSpecific) && ! empty($agencyTermsSpecific->fulltime_fee_model));

    echo "Company $partnerCompanyId:" . PHP_EOL;
    echo "  FT active: " . ($isFulltimeActive ? "YES" : "NO") . " (status=" . ($ftDTO ? $ftDTO->status : 'null') . ")" . PHP_EOL;
    echo "  CT active: " . ($isContractActive ? "YES" : "NO") . " (status=" . ($ctDTO ? $ctDTO->status : 'null') . ")" . PHP_EOL;
}

testAgencySync(3); // TechNova Solutions (has both active)
testAgencySync(6); // Agency 6 (has neither active)
testAgencySync(1); // Company 1 (has both active)
