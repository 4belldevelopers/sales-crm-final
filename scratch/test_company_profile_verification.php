<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$_SERVER['REQUEST_URI'] = '/service_company/account/1/edit';
$_SERVER['HTTP_HOST'] = 'testing.jero.works';
if (!isset($_SESSION)) {
    $_SESSION = [];
}
$_SESSION['profile_popup_modal_show'] = 0;

// Simulate authenticated tenant user
$simUser = new \App\Models\User();
$simUser->id = 1;
$simUser->name = 'Test Admin';
$simUser->email = 'admin@acme.com';
$simUser->company_id = 1;
\Illuminate\Support\Facades\Auth::setUser($simUser);

echo "=== JERO GOD-LEVEL COMPANY PROFILE TEST SUITE ===\n\n";

// 1. Verify Controller Instantiation
try {
    $controller = $app->make(\App\Http\Controllers\ServiceCompany\ServiceCompanyAccountSettingsController::class);
    echo "✓ [PASS] ServiceCompanyAccountSettingsController instantiated successfully.\n";
} catch (\Throwable $e) {
    echo "✗ [FAIL] Controller instantiation: " . $e->getMessage() . "\n";
    exit(1);
}

// 2. Verify Routes
$routesToVerify = [
    'service_company.account.edit',
    'service_company.account.update',
    'service_company.account.onboarding',
    'service_company.account.onboarding.save',
    'service_company.account.index'
];

foreach ($routesToVerify as $routeName) {
    if (\Illuminate\Support\Facades\Route::has($routeName)) {
        echo "✓ [PASS] Route exists: {$routeName}\n";
    } else {
        echo "✗ [FAIL] Route missing: {$routeName}\n";
        exit(1);
    }
}

// 3. Verify Blade Compilation with Simulated Data
try {
    $simulatedCompany = (object)[
        'id' => 1,
        'company_name' => 'Acme Technologies Inc.',
        'website' => 'https://acmetech.com',
        'domain_key' => 'acmetech.com',
        'logo' => 'https://file.jero.works/logo.png',
        'industries' => 147,
        'company_size_from' => 51,
        'company_size' => 200,
        'company_type' => 2,
        'founded' => 2018,
        'career_page_link' => 'https://acmetech.com/careers',
        'description' => '<p>Acme is a global leader in cloud and software architecture.</p>',
        'address' => '100 Market St, Suite 500'
    ];

    $simulatedClient = (object)[
        'id' => 10,
        'name' => 'Acme Technologies Inc.',
        'website' => 'https://acmetech.com',
        'industry' => 147,
        'company_size_from' => 51,
        'company_sizes' => 200,
        'company_type' => 2,
        'founded' => 2018,
        'tax_id' => 'US-EIN-9928172',
        'career_page_link' => 'https://acmetech.com/careers',
        'logo' => 'https://file.jero.works/logo.png',
        'description' => '<p>Acme is a global leader in cloud and software architecture.</p>',
        'tags' => 'Custom Software,Cloud DevOps,AI Engineering'
    ];

    $simulatedLocations = collect([
        (object)[
            'id' => 1,
            'client_id' => 10,
            'country_id' => 101,
            'city' => 'Bengaluru, Karnataka, India',
            'city_name' => 'Bengaluru',
            'city_id' => 1001,
            'address' => '42 Brigade Road',
            'postcode' => '560001',
            'country_name' => 'India',
            'state_name' => 'Karnataka',
            'state' => 12
        ],
        (object)[
            'id' => 2,
            'client_id' => 10,
            'country_id' => 1,
            'city' => 'San Francisco, California, USA',
            'city_name' => 'San Francisco',
            'city_id' => 2001,
            'address' => '500 Howard Street',
            'postcode' => '94105',
            'country_name' => 'United States',
            'state_name' => 'California',
            'state' => 5
        ]
    ]);

    $simulatedTags = collect([
        (object)['tag_name' => 'Laravel'],
        (object)['tag_name' => 'Vue.js'],
        (object)['tag_name' => 'Docker'],
        (object)['tag_name' => 'AWS']
    ]);

    $department = \App\Services\IndustryTaxonomyService::getGlobalManifest();
    $companyTypeTaxonomy = \App\Helper\GeneralMethod::companyTypeTaxonomy();
    $companyType = \App\Helper\GeneralMethod::companyType();
    $countryArr = \App\Helper\GeneralMethod::getAllCountryList();
    $statelist = \App\Helper\GeneralMethod::statelist(101);
    $cities = \App\Helper\GeneralMethod::allCities();

    $viewData = [
        'pageTitle' => 'Company Profile',
        'companyName' => 'Acme Technologies Inc.',
        'global_company_type' => 2,
        'user_type' => 'company',
        'logged_in_as' => 'company',
        'superadmin' => null,
        'userPermissions' => [],
        'user' => $simUser,
        'global' => (object)['company_name' => 'Jero ATS', 'logo' => ''],
        'package' => null,
        'company_id' => 1,
        'account' => $simulatedCompany,
        'company' => $simulatedCompany,
        'client' => $simulatedClient,
        'client_location' => $simulatedLocations,
        'company_tags' => $simulatedTags,
        'department' => $department,
        'companyTypeTaxonomy' => $companyTypeTaxonomy,
        'companyType' => $companyType,
        'countryArr' => $countryArr,
        'statelist' => $statelist,
        'cities' => $cities,
        'page_mode' => 'EDIT',
        'switchSection' => 0
    ];

    // Compile and render Edit View
    $htmlEdit = \Illuminate\Support\Facades\View::make('service_company.account.edit', $viewData)->render();
    echo "✓ [PASS] 'service_company.account.edit' rendered successfully (" . strlen($htmlEdit) . " bytes HTML output).\n";

    // Compile and render Onboarding View
    $viewData['page_mode'] = 'ONBOARDING';
    $htmlOnboarding = \Illuminate\Support\Facades\View::make('service_company.account.onboarding', $viewData)->render();
    echo "✓ [PASS] 'service_company.account.onboarding' rendered successfully (" . strlen($htmlOnboarding) . " bytes HTML output).\n";

    // Requirement: Primary Domain MUST BE REMOVED from the UI
    if (strpos($htmlEdit, 'Primary Domain') !== false) {
        echo "✗ [FAIL] 'Primary Domain' found in rendered HTML - User instructed to remove it!\n";
        exit(1);
    }
    echo "✓ [PASS] 'Primary Domain' is completely removed from UI.\n";

    // Requirement: Hidden domain_key input for backend persistence exists
    if (strpos($htmlEdit, 'name="domain_key"') === false) {
        echo "✗ [FAIL] Missing hidden domain_key input!\n";
        exit(1);
    }
    echo "✓ [PASS] Hidden domain_key input exists for persistence.\n";

    // Verify key UI elements & Modern Card + Sub-Card architecture
    $assertions = [
        'Company Profile' => 'Title rendered',
        'data-step="1"' => 'Step 1 Stepper link',
        'data-step="2"' => 'Step 2 Stepper link',
        'data-step="3"' => 'Step 3 Stepper link',
        'data-step="4"' => 'Step 4 Stepper link',
        'data-step="5"' => 'Step 5 Stepper link',
        'company_name' => 'Company Name field',
        'website' => 'Website URL field',
        'industry_display' => 'Industry Combobox',
        'company_size_display' => 'Company Size Combobox',
        'company_type_display' => 'Company Type Combobox',
        'founded_display' => 'Founded Year Combobox',
        'jero_csize_select_wrap' => 'Company Size Select Wrap',
        'jero_ctype_select_wrap' => 'Company Type Select Wrap',
        'jero_founded_select_wrap' => 'Founded Year Select Wrap',
        'JeroCompanySizeEngine' => 'JeroCompanySizeEngine script',
        'JeroCompanyTypeEngine' => 'JeroCompanyTypeEngine script',
        'JeroFoundedYearEngine' => 'JeroFoundedYearEngine script',
        'jero_company_type_seed_data' => 'Company Type JSON Seed Data',
        'kt_company_description_quill' => 'Quill Description Editor',
        'desc_char_count' => '2000 Char live counter',
        'profile_apply_desc_suggestion' => 'AI Apply Suggestion button',
        'tag_search_input' => 'Technologies search & input',
        'kt_tagify_company_services' => 'Services Tagify component',
        'HEADQUARTERS' => 'Headquarters Location Badge',
        'btn_add_additional_location' => 'Add Branch Location button',
        'profile_readiness_pct' => 'Profile Readiness Score display',
        'submitBtn' => 'Final Submit Action CTA',
        'loadFile(event)' => 'Logo upload loadFile handler',
        'removeCompanyLogo()' => 'Logo remove handler',
        'god-fields-grid' => '2-Column 15.6 Screen Responsive Grid'
    ];

    foreach ($assertions as $needle => $label) {
        if (strpos($htmlEdit, $needle) !== false) {
            echo "✓ [PASS] (Edit View) Found {$label}.\n";
        } else {
            echo "✗ [FAIL] (Edit View) Missing {$label} ('{$needle}')!\n";
            exit(1);
        }

        if (strpos($htmlOnboarding, $needle) !== false) {
            echo "✓ [PASS] (Onboarding View) Found {$label}.\n";
        } else {
            echo "✗ [FAIL] (Onboarding View) Missing {$label} ('{$needle}')!\n";
            exit(1);
        }
    }

} catch (\Throwable $e) {
    echo "✗ [FAIL] View rendering failed: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}

echo "\n=== ALL 30+ GOD-LEVEL VERIFICATION CHECKS PASSED PERFECTLY ON BOTH VIEWS ===\n";
