<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\CompanyProfileRestrictionService;
use Carbon\Carbon;
use Illuminate\Support\Facades\View;

echo "================================================================================" . PHP_EOL;
echo "TEST SUITE: LOCKED COMPANY FIELDS EDITING RESTRICTED WARNING" . PHP_EOL;
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

// 1. Verify Service Constants
assertCheck(
    CompanyProfileRestrictionService::WARNING_TITLE === 'Editing Restricted',
    "CompanyProfileRestrictionService::WARNING_TITLE equals 'Editing Restricted'"
);

assertCheck(
    CompanyProfileRestrictionService::MSG_EDITING_RESTRICTED === "You can't edit these company details. Please contact your Administrator for assistance.",
    "CompanyProfileRestrictionService::MSG_EDITING_RESTRICTED matches exact required text"
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
    str_contains($lockedHtml, 'data-company-field-locked="true"'),
    "Company Website input has is-locked-company-field and data-company-field-locked attributes"
);

assertCheck(
    str_contains($lockedHtml, 'id="client_auto_fetch"') &&
    str_contains($lockedHtml, 'showLockedCompanyFieldWarning'),
    "Fetch Details button triggers showLockedCompanyFieldWarning when clicked in locked mode"
);

// Check Field 2: Company Name
assertCheck(
    str_contains($lockedHtml, 'id="client_id"') &&
    str_contains($lockedHtml, 'name="name"') &&
    str_contains($lockedHtml, 'is-locked-company-field'),
    "Company Name input has is-locked-company-field and data-company-field-locked attributes"
);

// Check Field 3: Industry Combobox
assertCheck(
    str_contains($lockedHtml, 'id="industry_display"') &&
    str_contains($lockedHtml, 'id="jero_ind_select_wrap"') &&
    !str_contains($lockedHtml, 'id="jero_ind_select_wrap" data-company-field-locked="true" style="pointer-events: none;"'),
    "Industry combobox wrapper does NOT have pointer-events: none, allowing click detection"
);

// Check Field 4: Company Type Combobox
assertCheck(
    str_contains($lockedHtml, 'id="company_type_display"') &&
    str_contains($lockedHtml, 'id="jero_ctype_select_wrap"') &&
    !str_contains($lockedHtml, 'id="jero_ctype_select_wrap" data-company-field-locked="true" style="pointer-events: none;"'),
    "Company Type combobox wrapper does NOT have pointer-events: none, allowing click detection"
);

// Check Script and SweetAlert Configuration
assertCheck(
    str_contains($lockedHtml, 'title: title') &&
    str_contains($lockedHtml, 'text: message') &&
    str_contains($lockedHtml, 'icon: \'warning\'') &&
    str_contains($lockedHtml, 'confirmButtonText: \'OK\''),
    "SweetAlert configuration contains warning icon, 'Editing Restricted' title, and 'OK' button"
);

assertCheck(
    str_contains($lockedHtml, 'var isWarningOpen = false;') &&
    str_contains($lockedHtml, 'Swal.isVisible()'),
    "Single popup guard prevents repeated warning popups while one is already visible"
);

assertCheck(
    str_contains($lockedHtml, '[\'click\', \'mousedown\', \'mouseup\', \'dblclick\']') &&
    str_contains($lockedHtml, '[\'focus\', \'focusin\']') &&
    str_contains($lockedHtml, '[\'keydown\', \'keypress\', \'keyup\']') &&
    str_contains($lockedHtml, '[\'input\', \'beforeinput\', \'paste\', \'cut\', \'change\']'),
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
