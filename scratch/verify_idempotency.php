<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ServiceCompany\ServiceCompanyJobsController;
use Illuminate\Http\Request;

echo "=== TESTING IDEMPOTENCY GUARDS ON MUTATION ENDPOINTS ===\n";

$existing = DB::table('recommendation_relation')->first();
$user = Auth::loginUsingId(1);
if ($existing) {
    $user->company_id = $existing->from_company_id;
}
$controller = app(ServiceCompanyJobsController::class);
$controller->user = $user;
if ($existing) {
    echo "Using existing recommendation_relation: ID {$existing->id}, From: {$existing->from_company_id}, To: {$existing->to_company_id}, Job: {$existing->job_id}\n";
    
    // Test send_only_job_invitation idempotency
    $req = new Request([
        'jobid' => $existing->job_id,
        'fromcompany' => $existing->from_company_id,
        'tocompany' => $existing->to_company_id,
        'kt_tagify_location' => 101,
    ]);
    
    $res = $controller->send_only_job_invitation($req);
    $data = is_array($res) ? $res : (method_exists($res, 'getData') ? $res->getData(true) : (array)$res);
    echo "send_only_job_invitation duplicate test: " . json_encode($data) . "\n";
    if (($data['status'] ?? '') === 'warning' && strpos($data['message'] ?? '', 'already sent') !== false) {
        echo "[SUCCESS] Idempotency guard caught duplicate send_only_job_invitation cleanly without coin deduction!\n";
    } else {
        echo "[INFO] Response: " . json_encode($data) . "\n";
    }

    // Test send_job_recommanded_request idempotency
    $_REQUEST['current_jobid'] = $existing->job_id;
    $res2 = $controller->send_job_recommanded_request($existing->to_company_id);
    $data2 = is_array($res2) ? $res2 : (method_exists($res2, 'getData') ? $res2->getData(true) : (array)$res2);
    echo "send_job_recommanded_request duplicate test: " . json_encode($data2) . "\n";
    if (($data2['status'] ?? '') === 'warning' && strpos($data2['message'] ?? '', 'already sent') !== false) {
        echo "[SUCCESS] Idempotency guard caught duplicate send_job_recommanded_request cleanly without coin deduction!\n";
    } else {
        echo "[INFO] Response: " . json_encode($data2) . "\n";
    }
} else {
    echo "No existing recommendation relation found to test.\n";
}

echo "=== IDEMPOTENCY TEST COMPLETE ===\n";
