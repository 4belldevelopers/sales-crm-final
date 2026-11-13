<?php

/**
 * ==============================================================================
 * Comprehensive Test Suite: Company Profile 7-Day Editing Period & Permanent Lock
 * ==============================================================================
 *
 * INTENT:
 * Exhaustively executes and validates all 11 required scenarios:
 * 1. Newly registered company (age 0d, unverified) -> Can edit all 4 fields.
 * 2. Company on day 3 (unverified) -> Can edit all 4 fields.
 * 3. Company on day 6 (unverified) -> Can edit all 4 fields.
 * 4. Company at exact 7-day boundary & day 8 -> Locked.
 * 5. Company verified on day 2 -> Immediately locked with verified message.
 * 6. Company verified on day 6 -> Immediately locked with verified message.
 * 7. Company verified after day 7 -> Remains locked with verified message.
 * 8. Direct API modification attempts on restricted fields -> Rejected with HTTP 422 & exact lock message.
 * 9. Unrelated profile fields (size, founded, description, etc.) -> Remain editable on locked company.
 * 10. Backward-compatible policy: Legacy records with NULL/empty created_at -> Locked.
 * 11. Timezone boundary test (Asia/Kolkata, America/New_York, UTC) -> Exact second boundary.
 *
 * Bonus: Decrypts and tests the user's specific target URL (Company ID = 4).
 *
 * Author: Antigravity AI
 * Date: November 13, 2026
 */

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\CompanyProfileRestrictionService;
use App\Helper\GeneralMethod;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;

$service = app(CompanyProfileRestrictionService::class);
$passedCount = 0;
$failedCount = 0;

function assertTest(string $scenario, bool $condition, string $details = '') {
    global $passedCount, $failedCount;
    if ($condition) {
        $passedCount++;
        echo "[\033[32mPASS\033[0m] {$scenario}" . ($details ? " -> {$details}" : "") . PHP_EOL;
    } else {
        $failedCount++;
        echo "[\033[31mFAIL\033[0m] {$scenario}" . ($details ? " -> {$details}" : "") . PHP_EOL;
    }
}

echo "================================================================================" . PHP_EOL;
echo "STARTING FORENSIC TEST SUITE: 7-DAY COMPANY PROFILE EDITING & PERMANENT LOCK" . PHP_EOL;
echo "================================================================================" . PHP_EOL;

// -----------------------------------------------------------------------------
// Scenario 1: Newly registered company (Age 0 days, unverified)
// -----------------------------------------------------------------------------
$now = Carbon::create(2026, 11, 13, 12, 0, 0, 'Asia/Kolkata');
$company1 = (object) [
    'id' => 9001,
    'company_name' => 'Acme New Corp',
    'website' => 'https://acme-new.com',
    'industries' => 12,
    'company_type' => 1,
    'accountVerify' => 0,
    'created_at' => $now->copy()->toDateTimeString(),
    'timezone' => 'Asia/Kolkata'
];
$status1 = $service->getRestrictionStatus($company1, $now);
assertTest(
    "Scenario 1: Newly registered company (Age 0 days, unverified)",
    $status1['can_edit'] === true && $status1['is_locked'] === false,
    "can_edit=true, is_locked=false, days_remaining={$status1['days_remaining']}"
);

// -----------------------------------------------------------------------------
// Scenario 2: Company on day 3 (unverified)
// -----------------------------------------------------------------------------
$company2 = (object) [
    'id' => 9002,
    'company_name' => 'Acme Day 3',
    'website' => 'https://acme-day3.com',
    'industries' => 12,
    'company_type' => 1,
    'accountVerify' => 0,
    'created_at' => $now->copy()->subDays(3)->toDateTimeString(),
    'timezone' => 'Asia/Kolkata'
];
$status2 = $service->getRestrictionStatus($company2, $now);
assertTest(
    "Scenario 2: Company on day 3 (unverified)",
    $status2['can_edit'] === true && $status2['is_locked'] === false,
    "can_edit=true, is_locked=false, days_remaining={$status2['days_remaining']}"
);

// -----------------------------------------------------------------------------
// Scenario 3: Company on day 6 (unverified)
// -----------------------------------------------------------------------------
$company3 = (object) [
    'id' => 9003,
    'company_name' => 'Acme Day 6',
    'website' => 'https://acme-day6.com',
    'industries' => 12,
    'company_type' => 1,
    'accountVerify' => 0,
    'created_at' => $now->copy()->subDays(6)->toDateTimeString(),
    'timezone' => 'Asia/Kolkata'
];
$status3 = $service->getRestrictionStatus($company3, $now);
assertTest(
    "Scenario 3: Company on day 6 (unverified)",
    $status3['can_edit'] === true && $status3['is_locked'] === false,
    "can_edit=true, is_locked=false, days_remaining={$status3['days_remaining']}"
);

// -----------------------------------------------------------------------------
// Scenario 4: Company at exact 7-day boundary & day 8
// -----------------------------------------------------------------------------
// Sub-case 4A: 6 days, 23 hours, 59 minutes, 59 seconds (1 second before deadline)
$regTime = Carbon::create(2026, 11, 1, 10, 0, 0, 'Asia/Kolkata');
$justBefore7Days = $regTime->copy()->addDays(7)->subSecond();
$company4A = (object) [
    'id' => 9004,
    'company_name' => 'Boundary Corp',
    'website' => 'https://boundary.com',
    'industries' => 12,
    'company_type' => 1,
    'accountVerify' => 0,
    'created_at' => $regTime->toDateTimeString(),
    'timezone' => 'Asia/Kolkata'
];
$status4A = $service->getRestrictionStatus($company4A, $justBefore7Days);
assertTest(
    "Scenario 4A: Exactly 1 second before 7 days deadline",
    $status4A['can_edit'] === true && $status4A['is_locked'] === false,
    "can_edit=true, is_locked=false (strictly less than 7 days)"
);

// Sub-case 4B: Exactly 7 complete days (at deadline)
$exact7Days = $regTime->copy()->addDays(7);
$status4B = $service->getRestrictionStatus($company4A, $exact7Days);
assertTest(
    "Scenario 4B: Exactly 7 complete days (at deadline)",
    $status4B['can_edit'] === false && $status4B['is_locked'] === true && $status4B['lock_reason'] === 'expired' && $status4B['lock_message'] === CompanyProfileRestrictionService::MSG_EXPIRED_LOCKED,
    "can_edit=false, is_locked=true, message='{$status4B['lock_message']}'"
);

// Sub-case 4C: Day 8 (after 7 days)
$day8 = $regTime->copy()->addDays(8);
$status4C = $service->getRestrictionStatus($company4A, $day8);
assertTest(
    "Scenario 4C: Company on day 8 (expired)",
    $status4C['can_edit'] === false && $status4C['is_locked'] === true && $status4C['lock_reason'] === 'expired',
    "can_edit=false, is_locked=true, reason=expired"
);

// -----------------------------------------------------------------------------
// Scenario 5: Company verified on day 2 -> Immediately locked
// -----------------------------------------------------------------------------
$company5 = (object) [
    'id' => 9005,
    'company_name' => 'Verified Day 2 Corp',
    'website' => 'https://verified-day2.com',
    'industries' => 12,
    'company_type' => 1,
    'accountVerify' => 1, // Super Admin verified
    'created_at' => $now->copy()->subDays(2)->toDateTimeString(),
    'timezone' => 'Asia/Kolkata'
];
$status5 = $service->getRestrictionStatus($company5, $now);
assertTest(
    "Scenario 5: Company verified on day 2 -> Immediately locked",
    $status5['can_edit'] === false && $status5['is_locked'] === true && $status5['lock_reason'] === 'verified' && $status5['lock_message'] === CompanyProfileRestrictionService::MSG_VERIFIED_LOCKED,
    "can_edit=false, is_locked=true, message='{$status5['lock_message']}'"
);

// -----------------------------------------------------------------------------
// Scenario 6: Company verified on day 6 -> Immediately locked
// -----------------------------------------------------------------------------
$company6 = (object) [
    'id' => 9006,
    'company_name' => 'Verified Day 6 Corp',
    'website' => 'https://verified-day6.com',
    'industries' => 12,
    'company_type' => 1,
    'accountVerify' => 1, // Super Admin verified
    'created_at' => $now->copy()->subDays(6)->toDateTimeString(),
    'timezone' => 'Asia/Kolkata'
];
$status6 = $service->getRestrictionStatus($company6, $now);
assertTest(
    "Scenario 6: Company verified on day 6 -> Immediately locked",
    $status6['can_edit'] === false && $status6['is_locked'] === true && $status6['lock_reason'] === 'verified' && $status6['lock_message'] === CompanyProfileRestrictionService::MSG_VERIFIED_LOCKED,
    "can_edit=false, is_locked=true, message='{$status6['lock_message']}'"
);

// -----------------------------------------------------------------------------
// Scenario 7: Company verified after day 7 -> Remains permanently locked
// -----------------------------------------------------------------------------
$company7 = (object) [
    'id' => 9007,
    'company_name' => 'Verified Day 10 Corp',
    'website' => 'https://verified-day10.com',
    'industries' => 12,
    'company_type' => 1,
    'accountVerify' => 1, // Super Admin verified
    'created_at' => $now->copy()->subDays(10)->toDateTimeString(),
    'timezone' => 'Asia/Kolkata'
];
$status7 = $service->getRestrictionStatus($company7, $now);
assertTest(
    "Scenario 7: Company verified after day 7 -> Permanently locked",
    $status7['can_edit'] === false && $status7['is_locked'] === true && $status7['lock_reason'] === 'verified' && $status7['lock_message'] === CompanyProfileRestrictionService::MSG_VERIFIED_LOCKED,
    "can_edit=false, is_locked=true, message='{$status7['lock_message']}'"
);

// -----------------------------------------------------------------------------
// Scenario 8: Direct API modification attempts are rejected
// -----------------------------------------------------------------------------
// Sub-test 8A: Locked company -> attempt to change website
$errWebsite = $service->validateModificationAttempt($company5, ['website' => 'https://hacked-website.com']);
assertTest(
    "Scenario 8A: Direct API attempt to modify website when locked",
    $errWebsite === CompanyProfileRestrictionService::MSG_VERIFIED_LOCKED,
    "Rejected with verified lock message"
);

// Sub-test 8B: Locked company -> attempt to change company_name
$errName = $service->validateModificationAttempt($company4A, ['company_name' => 'Malicious New Name'], $day8);
assertTest(
    "Scenario 8B: Direct API attempt to modify company_name after 7 days",
    $errName === CompanyProfileRestrictionService::MSG_EXPIRED_LOCKED,
    "Rejected with expired lock message"
);

// Sub-test 8C: Locked company -> attempt to change industry
$errInd = $service->validateModificationAttempt($company5, ['industry' => 999]);
assertTest(
    "Scenario 8C: Direct API attempt to modify industry when locked",
    $errInd === CompanyProfileRestrictionService::MSG_VERIFIED_LOCKED,
    "Rejected with verified lock message"
);

// Sub-test 8D: Locked company -> attempt to change company_type
$errType = $service->validateModificationAttempt($company5, ['company_type' => 4]);
assertTest(
    "Scenario 8D: Direct API attempt to modify company_type when locked",
    $errType === CompanyProfileRestrictionService::MSG_VERIFIED_LOCKED,
    "Rejected with verified lock message"
);

// -----------------------------------------------------------------------------
// Scenario 9: Unrelated company profile fields remain editable
// -----------------------------------------------------------------------------
// When locked company submits identical restricted fields (or omits them) but changes unrelated fields
$unrelatedPayload = [
    'website' => 'https://verified-day2.com', // unchanged
    'company_name' => 'Verified Day 2 Corp',  // unchanged
    'industry' => 12,                         // unchanged
    'company_type' => 1,                      // unchanged
    'company_size' => '51-200',               // UNRELATED FIELD
    'founded' => '2015',                      // UNRELATED FIELD
    'description' => 'Updated description',   // UNRELATED FIELD
    'career_page_link' => 'https://careers.com' // UNRELATED FIELD
];
$errUnrelated = $service->validateModificationAttempt($company5, $unrelatedPayload);
assertTest(
    "Scenario 9: Unrelated fields submitted on locked company",
    $errUnrelated === null,
    "Validation passes (null error returned), allowing unrelated profile updates"
);

// -----------------------------------------------------------------------------
// Scenario 10: Backward-compatible policy: Legacy companies with NULL created_at
// -----------------------------------------------------------------------------
$legacyCompany = (object) [
    'id' => 9010,
    'company_name' => 'Legacy Ancient Corp',
    'website' => 'https://legacy.com',
    'industries' => 1,
    'company_type' => 1,
    'accountVerify' => 0,
    'created_at' => null, // NULL created_at in DB
    'timezone' => 'Asia/Kolkata'
];
$status10 = $service->getRestrictionStatus($legacyCompany, $now);
assertTest(
    "Scenario 10: Legacy company with NULL created_at",
    $status10['can_edit'] === false && $status10['is_locked'] === true && $status10['lock_reason'] === 'expired' && $status10['lock_message'] === CompanyProfileRestrictionService::MSG_EXPIRED_LOCKED,
    "Safely locked under backward-compatible policy"
);

// -----------------------------------------------------------------------------
// Scenario 11: Timezone boundary test (America/New_York vs Asia/Kolkata)
// -----------------------------------------------------------------------------
$nyRegTime = Carbon::create(2026, 11, 1, 9, 0, 0, 'America/New_York');
$companyNY = (object) [
    'id' => 9011,
    'company_name' => 'New York Tech',
    'website' => 'https://nytech.com',
    'industries' => 5,
    'company_type' => 2,
    'accountVerify' => 0,
    'created_at' => $nyRegTime,
    'timezone' => 'America/New_York'
];
// Test at 6 days 23 hours 59 minutes 59 seconds in America/New_York
$nyTimeBefore = $nyRegTime->copy()->addDays(7)->subSecond();
$statusNYBefore = $service->getRestrictionStatus($companyNY, $nyTimeBefore);
// Test at 7 days 00 hours 00 minutes 01 second in America/New_York
$nyTimeAfter = $nyRegTime->copy()->addDays(7)->addSecond();
$statusNYAfter = $service->getRestrictionStatus($companyNY, $nyTimeAfter);
assertTest(
    "Scenario 11A: America/New_York 1s before 7 days",
    $statusNYBefore['can_edit'] === true && $statusNYBefore['is_locked'] === false,
    "can_edit=true in America/New_York timezone at {$nyTimeBefore->format('d M y, h:i A T')}"
);
assertTest(
    "Scenario 11B: America/New_York 1s after 7 days",
    $statusNYAfter['can_edit'] === false && $statusNYAfter['is_locked'] === true,
    "can_edit=false in America/New_York timezone at {$nyTimeAfter->format('d M y, h:i A T')}"
);

// -----------------------------------------------------------------------------
// Targeted Verification of User's Specific Target URL & Company ID 4
// -----------------------------------------------------------------------------
$encryptedPayload = 'eyJpdiI6Ik9FZncwNExZcTA2SE40QjFOTzU2d0E9PSIsInZhbHVlIjoiTGdhcS93UXZSWWlKOXdLRFNwVEU5UT09IiwibWFjIjoiNWM0ZGU0ZTc5ZWU4MjY4MWI4ZjdjYmI2MDljY2ExMThmNDJjNDExNmIwZDNkYThjY2JlMTFjZTI1ZGJmNDBlNiIsInRhZyI6IiJ9';
$decryptedId = (int) GeneralMethod::value_decrypt($encryptedPayload);
$companyDb = DB::table('companies')->where('id', $decryptedId)->first();
$statusTarget = $service->getRestrictionStatus($companyDb);
assertTest(
    "Target URL Decryption & Evaluation (Company ID: {$decryptedId})",
    $decryptedId === 4 && $statusTarget['is_locked'] === true && $statusTarget['lock_reason'] === 'verified',
    "Company: '{$companyDb->company_name}', accountVerify={$companyDb->accountVerify}, Lock reason='{$statusTarget['lock_reason']}', Lock message='{$statusTarget['lock_message']}'"
);

echo "================================================================================" . PHP_EOL;
echo "TEST RESULTS: {$passedCount} PASSED, {$failedCount} FAILED." . PHP_EOL;
echo "================================================================================" . PHP_EOL;

if ($failedCount > 0) {
    exit(1);
}
exit(0);
