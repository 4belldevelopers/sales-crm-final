<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$permanentCommercial = (object) [
    'id' => 1,
    'active' => true,
    'rate_type' => 2,
    'header_label' => 'PERCENTAGE OF CTC',
    'mode_badge' => '5 Tiers',
    'default_rate' => '10%',
    'default_rate_sub' => 'Default Rate',
    'placement_fee' => '10% of Annual CTC',
    'sal_mode_label' => 'Annual CTC',
    'model_label' => 'Percentage of CTC',
    'currency_code' => 'INR',
    'currency_symbol' => '₹',
    'replacement_days' => 90,
    'replacement_type' => 'Free Replacement',
    'replacement_clause' => 'Candidate Resignation / Early Exit',
    'payment_terms' => 'Net 30 Days',
    'billing_frequency' => 'One Time',
    'billing_trigger' => 'Candidate Joined',
    'countries' => ['India', 'United States', 'Japan'],
];

$contractCommercial = (object) [
    'id' => 2,
    'active' => true,
    'rate_type' => 1,
    'header_label' => 'CONTRACT BILL RATE',
    'mode_badge' => 'Hourly',
    'default_rate' => '₹ 5',
    'default_rate_sub' => '/ Hour',
    'commercial_rate' => '₹ 5 / Hour',
    'sal_mode_label' => 'Hourly Rate',
    'model_label' => 'Contract Bill Rate',
    'currency_code' => 'INR',
    'currency_symbol' => '₹',
    'replacement_days' => 90,
    'replacement_type' => 'Free Replacement Contractor',
    'replacement_clause' => 'Contractor Early Departure',
    'payment_terms' => 'Net 30 Days',
    'billing_cycle' => 'Monthly in Advance',
    'billing_trigger' => 'Monthly in Advance',
    'countries' => ['India'],
];

$viewData = [
    'id' => 101,
    'directClientDetail' => (object) ['name' => 'Agency Test', 'company_id' => 101],
    'hasActiveCommercials' => true,
    'hasPermanentStaffing' => true,
    'hasContractStaffing' => true,
    'permanentCommercial' => $permanentCommercial,
    'contractCommercial' => $contractCommercial,
];

$rendered = view('service_company.consultancy.dviewMore', $viewData)->render();

// Check 5 rows in Fulltime
$checks = [
    'FT Row 1 Default Rate' => str_contains($rendered, 'Default Rate:') && str_contains($rendered, '10%'),
    'FT Row 2 Invoicing' => str_contains($rendered, 'Invoicing:') && str_contains($rendered, 'One Time Fee') && str_contains($rendered, 'Candidate Joined'),
    'FT Row 3 Payment Terms' => str_contains($rendered, 'Payment Terms:') && str_contains($rendered, 'Net 30 Days'),
    'FT Row 4 Replacement (Dedicated)' => str_contains($rendered, 'Replacement:') && str_contains($rendered, '90 Days') && str_contains($rendered, 'Free Replacement'),
    'FT Row 5 Operations' => str_contains($rendered, 'Operations:') && str_contains($rendered, 'Operate in <strong>3 Countries:</strong>') && str_contains($rendered, '>IN<') && str_contains($rendered, '>US<') && str_contains($rendered, '>JPN<'),
    'CT Row 1 Bill Rate' => str_contains($rendered, 'Bill Rate:') && str_contains($rendered, '₹ 5'),
    'CT Row 2 Invoicing' => str_contains($rendered, 'Monthly in Advance Invoicing'),
    'CT Row 3 Payment Terms' => str_contains($rendered, 'Payment Terms:'),
    'CT Row 4 Replacement (Dedicated)' => str_contains($rendered, 'Free Replacement Contractor'),
    'CT Row 5 Operations' => str_contains($rendered, 'Operate in <strong>1 Country:</strong>'),
    'No redundant bottom strip' => !str_contains($rendered, 'Fulltime terms bind automatically') && !str_contains($rendered, 'Contract terms apply automatically'),
];

echo "VERIFICATION RESULTS:\n";
$allPass = true;
foreach ($checks as $name => $passed) {
    echo ($passed ? "[PASS] " : "[FAIL] ") . $name . "\n";
    if (!$passed) $allPass = false;
}

if ($allPass) {
    echo "ALL 11 5-ROW STRATEGY CHECKS PASSED!\n";
} else {
    exit(1);
}
