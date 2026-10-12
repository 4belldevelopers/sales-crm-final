<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\ServiceCompany\ServiceCompanyBenchCandidatesController;

Auth::loginUsingId(2);
$_SERVER['REQUEST_URI'] = '/service_company/bench-candidates/recommend_internal_req/6';

try {
    $controller = app(ServiceCompanyBenchCandidatesController::class);
    $req = Request::create('/service_company/bench-candidates/recommend_internal_req/6', 'GET');
    $req->id = 6;
    $res = $controller->recommend_internal_req($req);
    echo "SUCCESS: " . get_class($res) . "\n";
    $view = $res->render();
    echo "RENDERED: " . strlen($view) . " bytes\n";
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "FILE: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "TRACE:\n" . $e->getTraceAsString() . "\n";
}
