<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$registrationContext = [
    'context' => 'team_member',
    'context_version' => 'TEAM_V2',
    'invitation_id' => 1,
    'tenant' => [
        'id' => 1,
        'name' => 'TechNova Inc'
    ],
    'member' => [
        'name' => 'kajal',
        'email' => 'kajalverma5057@gmail.com',
        'role_name' => 'Recruiter'
    ]
];

view()->share('errors', new \Illuminate\Support\ViewErrorBag);

$view = view('auth.login', [
    'initialMode' => 'team_invite',
    'registrationContext' => $registrationContext
])->render();

echo "Rendered view length: " . strlen($view) . " bytes\n";

if (strpos($view, 'value="Kajal"') !== false) {
    echo "✓ [PASS] Found value=\"Kajal\" in rendered HTML (1st letter capitalized)!\n";
} else {
    echo "✗ [FAIL] Did not find value=\"Kajal\" in rendered HTML!\n";
    exit(1);
}

if (strpos($view, 'text-transform: capitalize') !== false) {
    echo "✓ [PASS] Found text-transform: capitalize in input style!\n";
} else {
    echo "✗ [FAIL] Did not find text-transform: capitalize!\n";
    exit(1);
}

echo "=== ALL CHECKS PASSED ===\n";
