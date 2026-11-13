<?php

require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Company;
use App\Models\User;

$user = User::where('is_superadmin', 1)->first();
auth()->login($user);

echo "Logged in as user {$user->id}, company_id = " . var_export($user->company_id, true) . ", roles count = " . $user->roles->count() . "\n";

try {
    $c1 = Company::findOrFail(3);
    echo "WITH SCOPE: Found company {$c1->id} - {$c1->company_name}\n";
} catch (\Throwable $e) {
    echo "WITH SCOPE: FAILED -> " . $e->getMessage() . "\n";
}

try {
    $c2 = Company::withoutGlobalScopes()->findOrFail(3);
    echo "WITHOUT GLOBAL SCOPES: Found company {$c2->id} - {$c2->company_name}\n";
} catch (\Throwable $e) {
    echo "WITHOUT GLOBAL SCOPES: FAILED -> " . $e->getMessage() . "\n";
}
