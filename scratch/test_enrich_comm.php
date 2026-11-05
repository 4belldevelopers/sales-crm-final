<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$partnerCompanyId = 3;
$company_id = 2;

$commStateDTO = \App\Services\CommercialReadModelService::getTenantCommercialsState($partnerCompanyId);
$ftDTO = $commStateDTO->fulltime;
$ctDTO = $commStateDTO->contract;

$agencyTerms = \Illuminate\Support\Facades\DB::table('agency_commercial_terms')
    ->where('company_id', $partnerCompanyId)
    ->first() ?: \Illuminate\Support\Facades\DB::table('agency_commercial_terms')
    ->where('company_id', $company_id)
    ->first();

$settingsJson = ($agencyTerms && !empty($agencyTerms->settings_json))
    ? (is_array($agencyTerms->settings_json) ? $agencyTerms->settings_json : json_decode($agencyTerms->settings_json, true))
    : [];

$partnerCompany = \Illuminate\Support\Facades\DB::table('companies')->where('id', $partnerCompanyId)->first();
$directClientDetail = \Illuminate\Support\Facades\DB::table('clients')->where('company_id', $partnerCompanyId)->first();

// --- 1. FULLTIME DATA RESOLUTION ---
$ftActive = ($ftDTO && (int)$ftDTO->status === 1);
$ftRateType = $ftDTO ? (int)$ftDTO->rateType : 2;
$isFreeFT = ($ftRateType === 0);
$ftModelLabel = $ftDTO ? $ftDTO->rateTypeLabel : ($ftRateType === 2 ? 'Percentage of CTC' : ($ftRateType === 1 ? 'Fixed Placement Fee' : 'No Fee Applicable (Free Placement)'));
$ftHeaderLabel = $isFreeFT ? 'FREE PLACEMENT' : ($ftRateType === 1 ? 'FIXED PLACEMENT' : 'PERCENTAGE OF CTC');
$ftModeBadgeText = $isFreeFT ? '0% Fee' : '5 Tiers';

$ftFirstRate = ($ftDTO && !empty($ftDTO->rates)) ? (float) data_get(collect($ftDTO->rates)->sortBy('exp')->first(), 'rate', 8.33) : 8.33;
if (!empty($partnerCompany->success_per) && $partnerCompany->success_per > 0) {
    $ftFirstRate = (float)$partnerCompany->success_per;
}
$ftCurrencyCode = $ftDTO ? $ftDTO->currencyCode : 'INR';
$ftCurrencySymbol = \App\Services\CurrencyService::getCurrencySymbol($ftCurrencyCode) ?: '₹';

if ($ftRateType === 2) {
    $ftDefaultRateText = number_format($ftFirstRate, ($ftFirstRate == (int) $ftFirstRate ? 0 : 2)) . '%';
    $ftSalModeText = 'Annual CTC';
} elseif ($ftRateType === 1) {
    $ftDefaultRateText = $ftCurrencySymbol . ' ' . number_format($ftFirstRate, ($ftFirstRate == (int) $ftFirstRate ? 0 : 2));
    $ftSalModeText = 'Per Hire';
} else {
    $ftDefaultRateText = 'Free (0%)';
    $ftSalModeText = 'Direct Candidate Hire';
}

$ftReplacementDays = $ftDTO ? $ftDTO->replacementPeriodDays : 90;
$ftReplacementType = $ftDTO ? $ftDTO->replacementTypeLabel : 'Free Replacement';
$ftReplacementClause = $ftDTO ? $ftDTO->replacementEligibilityLabel : 'Candidate Resignation / Early Exit';

$ftPaymentDays = $ftDTO ? $ftDTO->paymentTermsDays : 30;
$ftPaymentTermsDays = ($ftPaymentDays === 0) ? 'Due Immediately' : "Net {$ftPaymentDays} Days";
$ftBillingFrequency = $ftDTO ? $ftDTO->billingFrequencyLabel : 'One Time';
$ftBillingTrigger = $ftDTO ? $ftDTO->billingTriggerLabel : 'Candidate Joined';

$ftCountries = ($ftDTO && !empty($ftDTO->countryNames)) ? $ftDTO->countryNames : ['India'];

dump('Fulltime Resolved:', [
    'active' => $ftActive,
    'header_label' => $ftHeaderLabel,
    'mode_badge' => $ftModeBadgeText,
    'rate_text' => $ftDefaultRateText,
    'sal_mode' => $ftSalModeText,
    'model_label' => $ftModelLabel,
    'replacement_days' => $ftReplacementDays,
    'replacement_type' => $ftReplacementType,
    'replacement_clause' => $ftReplacementClause,
    'payment_terms' => $ftPaymentTermsDays,
    'billing_frequency' => $ftBillingFrequency,
    'billing_trigger' => $ftBillingTrigger,
    'countries' => $ftCountries,
]);

// --- 2. CONTRACT DATA RESOLUTION ---
$ctActive = ($ctDTO && (int)$ctDTO->status === 1);
$ctRateType = $ctDTO ? (int)$ctDTO->rateType : 1;
$isFreeCT = ($ctRateType === 0);
$ctModelLabel = $ctDTO ? $ctDTO->rateTypeLabel : ($ctRateType === 2 ? 'Markup on Pay Rate' : ($ctRateType === 1 ? 'Contract Bill Rate' : 'Zero Margin (Pass-Through)'));
$ctHeaderLabel = $isFreeCT ? 'ZERO MARGIN' : ($ctRateType === 2 ? 'MARKUP ON PAY' : 'CONTRACT BILL RATE');

$ctSalMode = $ctDTO ? $ctDTO->salMode : 2;
$ctSalModeLabel = $isFreeCT ? 'At-Cost Wage Pass-Through' : match($ctSalMode) {
    2 => 'Hourly Contract Rate',
    1 => 'Monthly Staffing Rate',
    0 => 'Annual Staffing Rate',
    default => 'Hourly Contract Rate',
};
$ctModeBadgeText = $isFreeCT ? '0% Margin' : match($ctSalMode) {
    2 => 'Hourly',
    1 => 'Monthly',
    0 => 'Annual',
    default => 'Hourly',
};

$ctCurrencyCode = $ctDTO ? $ctDTO->currencyCode : 'INR';
$ctCurrencySymbol = \App\Services\CurrencyService::getCurrencySymbol($ctCurrencyCode) ?: '₹';

$ctFirstRate = ($ctDTO && !empty($ctDTO->rates)) ? (float) data_get(collect($ctDTO->rates)->sortBy('exp')->first(), 'rate', 5.0) : 5.0;

if (!empty($settingsJson['contract_rate_amount']) && (float)$settingsJson['contract_rate_amount'] > 0) {
    $ctDefaultRateText = $ctCurrencySymbol . ' ' . number_format((float)$settingsJson['contract_rate_amount']);
    $ctDefaultRateSub = '/ ' . ucfirst($agencyTerms->contract_billing_model ?? 'Month');
} elseif ($ctRateType === 2) {
    $ctDefaultRateText = number_format($ctFirstRate, ($ctFirstRate == (int) $ctFirstRate ? 0 : 1)) . '%';
    $ctDefaultRateSub = 'Markup on Pay';
} elseif ($ctRateType === 1) {
    $ctDefaultRateText = $ctCurrencySymbol . ' ' . number_format($ctFirstRate, ($ctFirstRate == (int) $ctFirstRate ? 0 : 2));
    $ctDefaultRateSub = match($ctSalMode) { 2 => '/ Hour', 1 => '/ Month', default => '/ Month' };
} else {
    $ctDefaultRateText = 'Pass-Through (0%)';
    $ctDefaultRateSub = 'Zero Margin';
}

$ctReplacementDays = $ctDTO ? $ctDTO->replacementPeriodDays : 30;
$ctReplacementType = $ctDTO ? $ctDTO->replacementTypeLabel : 'Contractor Replacement';
$ctReplacementClause = $ctDTO ? $ctDTO->replacementEligibilityLabel : 'Contractor Early Departure';

$ctPaymentDays = $ctDTO ? $ctDTO->paymentTermsDays : 30;
$ctPaymentTermsDays = ($ctPaymentDays === 0) ? 'Due Immediately' : "Net {$ctPaymentDays} Days";

$ctBillingCycle = !empty($settingsJson['contract_billing_cycle']) ? $settingsJson['contract_billing_cycle'] : ($ctDTO ? $ctDTO->billingFrequencyLabel : 'Monthly');
$ctBillingTrigger = !empty($settingsJson['contract_invoice_raising']) ? $settingsJson['contract_invoice_raising'] : ($ctDTO ? $ctDTO->billingTriggerLabel : 'Approved Timesheet');

$ctMinDuration = (!empty($agencyTerms->contract_min_duration_months) && (int)$agencyTerms->contract_min_duration_months > 0)
    ? (int)$agencyTerms->contract_min_duration_months . ' Months'
    : '3 Months';

$ctEarlyTermination = !empty($settingsJson['contract_early_termination']) ? $settingsJson['contract_early_termination'] : 'As per contract terms';
$ctOvertimePolicy = !empty($settingsJson['contract_overtime_policy']) ? $settingsJson['contract_overtime_policy'] : 'As per client terms';

$ctCountries = ($ctDTO && !empty($ctDTO->countryNames)) ? $ctDTO->countryNames : ['India'];

dump('Contract Resolved:', [
    'active' => $ctActive,
    'header_label' => $ctHeaderLabel,
    'mode_badge' => $ctModeBadgeText,
    'rate_text' => $ctDefaultRateText,
    'rate_sub' => $ctDefaultRateSub,
    'sal_mode' => $ctSalModeLabel,
    'model_label' => $ctModelLabel,
    'replacement_days' => $ctReplacementDays,
    'replacement_type' => $ctReplacementType,
    'replacement_clause' => $ctReplacementClause,
    'payment_terms' => $ctPaymentTermsDays,
    'billing_cycle' => $ctBillingCycle,
    'billing_trigger' => $ctBillingTrigger,
    'min_duration' => $ctMinDuration,
    'early_termination' => $ctEarlyTermination,
    'overtime_policy' => $ctOvertimePolicy,
    'countries' => $ctCountries,
]);
