<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\ServiceCompany\ServiceCompanyTeamController;
use App\Models\User;
use Illuminate\Http\Request;

// Find an admin user in company 2
$companyUser = User::where('company_id', 2)->where('root_user', 1)->first() 
    ?? User::where('company_id', 2)->first();

echo "Acting as User #{$companyUser->id} ({$companyUser->name}) for Company #2\n";
auth()->login($companyUser);

$controller = app(ServiceCompanyTeamController::class);
$req = Request::create('/service_company/team/data_ajax', 'POST', [
    'page' => 1,
    'size' => 20,
    'statusTab' => 'all',
]);
$req->headers->set('X-Requested-With', 'XMLHttpRequest');

$res = $controller->data_ajax($req);
$data = json_decode($res->getContent(), true);

echo "Total records returned: " . ($data['total'] ?? 'N/A') . "\n";
$html = $data['html'] ?? '';

echo "HTML contains 'kajal': " . (stripos($html, 'kajal') !== false ? 'YES' : 'NO') . "\n";
echo "HTML contains 'snehil': " . (stripos($html, 'snehil') !== false ? 'YES' : 'NO') . "\n";
echo "HTML contains 'Awaiting Signup': " . (stripos($html, 'Awaiting Signup') !== false ? 'YES' : 'NO') . "\n";

// Check status text for kajal
if (preg_match('/kajal.*?tm-status-text[^>]*>([^<]+)/s', $html, $m)) {
    echo "Kajal status in rendered HTML: " . trim($m[1]) . "\n";
}

// Check status text for snehil
if (preg_match('/snehil.*?tm-status-text[^>]*>([^<]+)/s', $html, $m)) {
    echo "Snehil status in rendered HTML: " . trim($m[1]) . "\n";
}
