<?php

require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\DTO\Commercials\ServiceTermDTO;
use App\DTO\Commercials\CommercialStateDTO;
use App\Services\CommercialReadModelService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

echo "=== TESTING FULLTIME VS CONTRACT SPECIALIZED RECRUITMENT DOMAIN DTO & SYNC ===\n\n";

$passed = 0;
$failed = 0;

function assertCondition($name, $condition, $details = '') {
    global $passed, $failed;
    if ($condition) {
        echo " [PASS] {$name}\n";
        if ($details) echo "        {$details}\n";
        $passed++;
    } else {
        echo " [FAIL] {$name}\n";
        if ($details) echo "        {$details}\n";
        $failed++;
    }
}

// 1. Test Fulltime ServiceTermDTO instantiation & domain labels
$ftData = [
    'id' => 101,
    'company_id' => 1,
    'commtype' => 2,
    'status' => 1,
    'rateType' => 2, // Percentage of CTC
    'sal_mode' => 0,
    'salary_currency' => 4,
    'currency_code' => 'INR',
    'replacement_period_days' => 90,
    'replacement_type' => 'free_replacement',
    'replacement_eligibility' => 'early_exit',
    'billing_frequency' => 'one_time',
    'billing_trigger' => 'candidate_joined',
    'payment_terms_days' => 30,
    'commericial_details' => [['exp' => 0, 'rate' => 8.33]],
    'commericial_countries' => [1, 2],
    'country_names' => ['India', 'United States']
];

$ftDTO = ServiceTermDTO::fromArray($ftData, 2);

assertCondition(
    'Fulltime DTO rateTypeLabel specialized to Permanent Recruitment (% of CTC)',
    $ftDTO->rateTypeLabel === 'Percentage of CTC',
    "rateTypeLabel: '{$ftDTO->rateTypeLabel}'"
);

assertCondition(
    'Fulltime DTO salModeLabel specialized to Annual CTC',
    $ftDTO->salModeLabel === 'Annual CTC',
    "salModeLabel: '{$ftDTO->salModeLabel}'"
);

assertCondition(
    'Fulltime DTO replacement labels specialized to Free Replacement on Early Exit',
    $ftDTO->replacementTypeLabel === 'Free Replacement' && $ftDTO->replacementEligibilityLabel === 'Candidate Resignation / Early Exit',
    "Type: '{$ftDTO->replacementTypeLabel}', Eligibility: '{$ftDTO->replacementEligibilityLabel}'"
);

assertCondition(
    'Fulltime DTO billing trigger specialized to Candidate Joined (One Time)',
    $ftDTO->billingTriggerLabel === 'Candidate Joined' && $ftDTO->billingFrequencyLabel === 'One Time',
    "Trigger: '{$ftDTO->billingTriggerLabel}', Cadence: '{$ftDTO->billingFrequencyLabel}'"
);

// 2. Test Contract ServiceTermDTO instantiation & contingent staffing domain labels
$ctData = [
    'id' => 102,
    'company_id' => 1,
    'commtype' => 1,
    'status' => 1,
    'rateType' => 2, // Markup on Pay Rate
    'sal_mode' => 1, // Monthly Staffing
    'salary_currency' => 4,
    'currency_code' => 'INR',
    'replacement_period_days' => 5, // 5 Days Trial
    'replacement_type' => 'trial_guarantee',
    'replacement_eligibility' => 'backfill_sla',
    'billing_frequency' => 'monthly',
    'billing_trigger' => 'approved_timesheet',
    'payment_terms_days' => 30,
    'commericial_details' => [['exp' => 0, 'rate' => 20]],
    'commericial_countries' => [1],
    'country_names' => ['India']
];

$ctDTO = ServiceTermDTO::fromArray($ctData, 1);

assertCondition(
    'Contract DTO rateTypeLabel specialized to Markup on Pay Rate',
    $ctDTO->rateTypeLabel === 'Markup on Pay Rate',
    "rateTypeLabel: '{$ctDTO->rateTypeLabel}'"
);

assertCondition(
    'Contract DTO salModeLabel specialized to Monthly Staffing',
    $ctDTO->salModeLabel === 'Monthly Staffing',
    "salModeLabel: '{$ctDTO->salModeLabel}'"
);

assertCondition(
    'Contract DTO replacement labels specialized to Satisfaction Trial Guarantee & 48h Backfill SLA',
    $ctDTO->replacementTypeLabel === 'Satisfaction Trial Guarantee' && $ctDTO->replacementEligibilityLabel === '48h Backfill SLA',
    "Type: '{$ctDTO->replacementTypeLabel}', Eligibility: '{$ctDTO->replacementEligibilityLabel}'"
);

assertCondition(
    'Contract DTO billing trigger specialized to Approved Timesheet (Monthly Cycle)',
    $ctDTO->billingTriggerLabel === 'Approved Timesheet' && $ctDTO->billingFrequencyLabel === 'Monthly',
    "Trigger: '{$ctDTO->billingTriggerLabel}', Cadence: '{$ctDTO->billingFrequencyLabel}'"
);

// 3. Test CommercialReadModelService projection and cache invalidation
$state = CommercialReadModelService::getTenantCommercialsState(1);
assertCondition(
    'CommercialReadModelService generates complete multi-service state',
    $state instanceof CommercialStateDTO && $state->fulltime !== null && $state->contract !== null,
    "Fulltime ID: {$state->fulltime->id}, Contract ID: {$state->contract->id}"
);

// Test 0% MySQL reads on cached hit
DB::flushQueryLog();
DB::enableQueryLog();
$cachedState = CommercialReadModelService::getTenantCommercialsState(1);
$queries = DB::getQueryLog();
DB::disableQueryLog();

assertCondition(
    '0% MySQL queries on cached CommercialReadModelService fetch (Rule 1)',
    count($queries) === 0,
    "Executed " . count($queries) . " queries (target: 0)"
);

echo "\n=========================================\n";
echo "SUMMARY: {$passed} PASSED, {$failed} FAILED\n";
echo "=========================================\n";

if ($failed > 0) {
    exit(1);
}
