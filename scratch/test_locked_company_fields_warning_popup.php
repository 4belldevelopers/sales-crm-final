<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\CompanyProfileRestrictionService;
use Carbon\Carbon;
use Illuminate\Support\Facades\View;

echo "================================================================================" . PHP_EOL;
echo "TEST SUITE: LOCKED COMPANY FIELDS EDITING RESTRICTED WARNING POPUP & MESSAGING" . PHP_EOL;
echo "================================================================================" . PHP_EOL;

$passCount = 0;
$failCount = 0;

function assertCheck($condition, $description) {
    global $passCount, $failCount;
    if ($condition) {
        $passCount++;
        echo "[\033[32mPASS\033[0m] " . $description . PHP_EOL;
    } else {
        $failCount++;
        echo "[\033[31mFAIL\033[0m] " . $description . PHP_EOL;
    }
}

// 1. Verify Service Constants and Dynamic Warning Formatter
assertCheck(
    CompanyProfileRestrictionService::WARNING_TITLE === 'Editing Restricted',
    "CompanyProfileRestrictionService::WARNING_TITLE equals 'Editing Restricted'"
);

assertCheck(
    CompanyProfileRestrictionService::MSG_EDITING_RESTRICTED === "You can't edit these company details. Please contact your Administrator for assistance.",
    "CompanyProfileRestrictionService::MSG_EDITING_RESTRICTED matches default generic text"
);

assertCheck(
    CompanyProfileRestrictionService::formatFieldWarningMessage('website') === "You can't edit Company Website. Please contact your Administrator for assistance.",
    "formatFieldWarningMessage('website') returns exact per-field warning for Company Website"
);

assertCheck(
    CompanyProfileRestrictionService::formatFieldWarningMessage('Company Name') === "You can't edit Company Name. Please contact your Administrator for assistance.",
    "formatFieldWarningMessage('Company Name') returns exact per-field warning for Company Name"
);

assertCheck(
    CompanyProfileRestrictionService::formatFieldWarningMessage('industry') === "You can't edit Industry. Please contact your Administrator for assistance.",
    "formatFieldWarningMessage('industry') returns exact per-field warning for Industry"
);

assertCheck(
    CompanyProfileRestrictionService::formatFieldWarningMessage('company_type') === "You can't edit Company Type. Please contact your Administrator for assistance.",
    "formatFieldWarningMessage('company_type') returns exact per-field warning for Company Type"
);

// 2. Verify getRestrictionStatus includes warning_title and warning_message
$service = app(CompanyProfileRestrictionService::class);
$lockedCompany = (object) [
    'id' => 999,
    'accountVerify' => 1,
    'created_at' => Carbon::now()->subDays(2),
    'timezone' => 'Asia/Kolkata',
];
$status = $service->getRestrictionStatus($lockedCompany);

assertCheck(
    isset($status['warning_title']) && $status['warning_title'] === 'Editing Restricted',
    "getRestrictionStatus contains warning_title 'Editing Restricted'"
);

assertCheck(
    isset($status['warning_message']) && $status['warning_message'] === "You can't edit these company details. Please contact your Administrator for assistance.",
    "getRestrictionStatus contains warning_message"
);

// 3. Render View for Locked Company and inspect DOM & Script
$lockedViewData = [
    'account' => (object) [
        'id' => 4,
        'company_name' => 'Acme Corp',
        'website' => 'https://acme.com',
        'industries' => 1,
        'company_type' => 1,
        'accountVerify' => 1,
        'created_at' => Carbon::now()->subDays(10),
        'timezone' => 'Asia/Kolkata',
        'logo' => null,
    ],
    'company' => null,
    'company_id' => 4,
    'profileRestriction' => $status,
    'website' => 'https://acme.com',
    'name' => 'Acme Corp',
    'company_name' => 'Acme Corp',
    'industry' => 1,
    'company_type' => 1,
    'company_sizes' => '11-50',
    'founded' => '2020',
    'department' => [
        (object) ['ind_id' => 1, 'ind_name' => 'Information Technology'],
    ],
    'companyType' => [
        1 => 'Private Limited',
    ],
    'logo' => '',
    'tax_id' => '',
];

$lockedHtml = View::make('service_company.account.components.tab-basic-info', $lockedViewData)->render();

// Check Field 1: Website
assertCheck(
    str_contains($lockedHtml, 'id="website"') &&
    str_contains($lockedHtml, 'name="website"') &&
    str_contains($lockedHtml, 'is-locked-company-field') &&
    str_contains($lockedHtml, 'data-locked-field-name="Company Website"'),
    "Company Website input has data-locked-field-name='Company Website'"
);

assertCheck(
    str_contains($lockedHtml, 'id="client_auto_fetch"') &&
    str_contains($lockedHtml, 'data-locked-field-name="Company Website"') &&
    str_contains($lockedHtml, "showLockedCompanyFieldWarning('Company Website')"),
    "Fetch Details button triggers showLockedCompanyFieldWarning('Company Website') when clicked in locked mode"
);

// Check Field 2: Company Name
assertCheck(
    str_contains($lockedHtml, 'id="client_id"') &&
    str_contains($lockedHtml, 'name="name"') &&
    str_contains($lockedHtml, 'data-locked-field-name="Company Name"'),
    "Company Name input has data-locked-field-name='Company Name'"
);

// Check Field 3: Industry Combobox
assertCheck(
    str_contains($lockedHtml, 'id="industry_display"') &&
    str_contains($lockedHtml, 'data-locked-field-name="Industry"') &&
    str_contains($lockedHtml, 'id="jero_ind_select_wrap"') &&
    !str_contains($lockedHtml, 'id="jero_ind_select_wrap" data-company-field-locked="true" style="pointer-events: none;"'),
    "Industry combobox has data-locked-field-name='Industry' and does NOT have pointer-events: none"
);

// Check Field 4: Company Type Combobox
assertCheck(
    str_contains($lockedHtml, 'id="company_type_display"') &&
    str_contains($lockedHtml, 'data-locked-field-name="Company Type"') &&
    str_contains($lockedHtml, 'id="jero_ctype_select_wrap"') &&
    !str_contains($lockedHtml, 'id="jero_ctype_select_wrap" data-company-field-locked="true" style="pointer-events: none;"'),
    "Company Type combobox has data-locked-field-name='Company Type' and does NOT have pointer-events: none"
);

// Check Elevated Modal Design & Custom Classes
assertCheck(
    str_contains($lockedHtml, 'jero-locked-modal-popup') &&
    str_contains($lockedHtml, 'jero-locked-modal-icon-badge') &&
    str_contains($lockedHtml, 'jero-locked-modal-ok-btn') &&
    str_contains($lockedHtml, 'jero-locked-field-badge'),
    "Elevated modal design classes present (icon-badge, field-badge, custom popup, and theme ok-btn)"
);

// Check Field Resolution Logic and Dynamic Messaging Script
assertCheck(
    str_contains($lockedHtml, 'function resolveFieldName(target)') &&
    str_contains($lockedHtml, "You can't edit \" + resolvedField + \". Please contact your Administrator for assistance."),
    "Dynamic per-field resolution and message formatting present in script"
);

assertCheck(
    str_contains($lockedHtml, 'var isWarningOpen = false;') &&
    str_contains($lockedHtml, 'Swal.isVisible()'),
    "Single popup guard prevents repeated warning popups while one is already visible"
);

assertCheck(
    str_contains($lockedHtml, "['click', 'mousedown', 'mouseup', 'dblclick']") &&
    str_contains($lockedHtml, "['focus', 'focusin']") &&
    str_contains($lockedHtml, "['keydown', 'keypress', 'keyup']") &&
    str_contains($lockedHtml, "['input', 'beforeinput', 'paste', 'cut', 'change']"),
    "Event interceptors register mouse clicks, focus (Tab navigation), keyboard keys, and text paste/input"
);

// 4. Render View for UNLOCKED Company and verify clean behavior
$unlockedCompany = (object) [
    'id' => 1001,
    'accountVerify' => 0,
    'created_at' => Carbon::now()->subDays(2),
    'timezone' => 'Asia/Kolkata',
];
$unlockedStatus = $service->getRestrictionStatus($unlockedCompany);

$unlockedViewData = $lockedViewData;
$unlockedViewData['profileRestriction'] = $unlockedStatus;
$unlockedViewData['account']->accountVerify = 0;
$unlockedViewData['account']->created_at = Carbon::now()->subDays(2);

$unlockedHtml = View::make('service_company.account.components.tab-basic-info', $unlockedViewData)->render();

assertCheck(
    !str_contains($unlockedHtml, 'is-locked-company-field') &&
    !str_contains($unlockedHtml, 'data-company-field-locked="true"'),
    "Unlocked company view does NOT contain is-locked-company-field or data-company-field-locked"
);

assertCheck(
    !str_contains($unlockedHtml, 'showLockedCompanyFieldWarning'),
    "Unlocked company view does NOT render showLockedCompanyFieldWarning script"
);

// 5. Backend Direct Modification Attempt
$unauthorizedData = [
    'website' => 'https://hacked-domain.com',
    'name' => 'Hacked Company Name',
];
$lockViolation = $service->validateModificationAttempt($lockedCompany, $unauthorizedData);

assertCheck(
    $lockViolation !== null,
    "Backend rejects direct unauthorized update attempt on locked company"
);

echo "================================================================================" . PHP_EOL;
echo "TEST RESULTS: {$passCount} PASSED, {$failCount} FAILED." . PHP_EOL;
echo "================================================================================" . PHP_EOL;

if ($failCount > 0) {
    exit(1);
}
