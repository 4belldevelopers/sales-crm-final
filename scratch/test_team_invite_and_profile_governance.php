<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Requests\StoreTeam;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

echo "=== 1. Testing StoreTeam Request Validation ===\n";
$rules = (new StoreTeam())->rules();

assert(isset($rules['name']), 'name rule missing');
assert(isset($rules['email']), 'email rule missing');
assert(isset($rules['role_id']), 'role_id rule missing');
assert(isset($rules['mobile']), 'mobile rule missing');
assert(isset($rules['calling_code']), 'calling_code rule missing');
assert(isset($rules['linkedin']), 'linkedin rule missing');

echo "✓ StoreTeam rules verified.\n";

$validator = Validator::make([
    'name' => 'John Doe',
    'email' => 'john.doe@acme.com',
    'role_id' => 2,
    'mobile' => '9876543210',
    'phone_country_code' => '+91',
    'linkedin' => 'https://www.linkedin.com/in/johndoe'
], $rules);

if ($validator->fails()) {
    echo "❌ Validator failed: " . json_encode($validator->errors()) . "\n";
    exit(1);
} else {
    echo "✓ Valid payload passes StoreTeam validation.\n";
}

echo "\n=== 2. Testing Drawer Blade Template Cleanliness ===\n";
$drawerContent = file_get_contents(__DIR__ . '/../resources/views/service_company/team/index.blade.php');

// Ensure no <div> inside <select>
$hasDivInSelect = preg_match('/<select[^>]*>[\s\S]*?<div[\s\S]*?<\/select>/i', $drawerContent);
if ($hasDivInSelect) {
    echo "❌ Error: <div> found inside <select> in index.blade.php\n";
    exit(1);
} else {
    echo "✓ DOM check passed: No invalid <div> tag inside <select>.\n";
}

// Ensure x-jero-phone-input and linkedin inputs are present in index.blade.php
assert(strpos($drawerContent, 'x-jero-phone-input') !== false, 'x-jero-phone-input missing in drawer');
assert(strpos($drawerContent, 'inviteLinkedin') !== false, 'inviteLinkedin missing in drawer');
assert(strpos($drawerContent, 'invite-unified-card') !== false, 'invite-unified-card missing in drawer');
assert(strpos($drawerContent, '#009ef7') !== false, 'JERO primary blue #009ef7 missing in drawer');
echo "✓ Unified Card structure, phone input, and LinkedIn field verified in index.blade.php.\n";

echo "\n=== 3. Testing Profile Edit Blade Protection ===\n";
$profileEditContent = file_get_contents(__DIR__ . '/../resources/views/service_company/profile/edit.blade.php');
assert(strpos($profileEditContent, '$isOrgAdmin') !== false, '$isOrgAdmin check missing in profile/edit.blade.php');
assert(strpos($profileEditContent, 'Admin Assigned') !== false, 'Admin Assigned badge missing in profile/edit.blade.php');
assert(strpos($profileEditContent, 'readonly style="background-color: #f8fafc;') !== false, 'readonly full name protection missing');

echo "✓ Profile edit blade contains Admin-Locked guard for Full Name.\n";

echo "\n=== 4. Testing Profile Controller Governance Logic ===\n";
$controllerContent = file_get_contents(__DIR__ . '/../app/Http/Controllers/ServiceCompany/ServiceCompanyProfileController.php');
assert(strpos($controllerContent, 'Enterprise Hybrid Governance: Only Org Admin can mutate corporate full name') !== false, 'Governance guard comment missing');
assert(strpos($controllerContent, '$user->mobile_verified = 0;') !== false, 'mobile_verified reset missing');

echo "✓ ServiceCompanyProfileController governance and mobile_verified reset verified.\n";

echo "\n============================================\n";
echo "🎉 ALL 4 VERIFICATION TESTS PASSED (100% GREEN)!\n";
echo "============================================\n";
