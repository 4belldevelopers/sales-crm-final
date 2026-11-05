<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\ServiceCompany\ClientActivityReadService;

$service = app(ClientActivityReadService::class);
$ref = new ReflectionClass($service);

$resolveJobs = $ref->getMethod('resolveClientJobIds');
$resolveJobs->setAccessible(true);

$resolveCands = $ref->getMethod('resolveAgencyCandidateAppIds');
$resolveCands->setAccessible(true);

// Case 1: Company 2 (Direct Client) viewing Company 3 (Agency) in Agency Drawer
$jobs_2_to_3 = $resolveJobs->invoke($service, 2, 3, 'direct', 3, 'agency');
$cands_2_to_3 = $resolveCands->invoke($service, 2, $jobs_2_to_3, 3, 'direct', 'agency', 3);

echo "Case 1: Company 2 viewing Agency 3 in Agency Drawer:\n";
echo "  Jobs: " . json_encode($jobs_2_to_3) . "\n";
echo "  Candidates: " . json_encode($cands_2_to_3) . "\n";

// Case 2: Company 3 (Agency) viewing Company 2 (Direct Client) in Client Drawer
$jobs_3_as_agency = $resolveJobs->invoke($service, 3, 2, 'direct', 2, 'client');
$cands_3_as_agency = $resolveCands->invoke($service, 3, $jobs_3_as_agency, 2, 'direct', 'client', 2);

echo "\nCase 2: Company 3 viewing Direct Client 2 in Client Drawer:\n";
echo "  Jobs: " . json_encode($jobs_3_as_agency) . "\n";
echo "  Candidates: " . json_encode($cands_3_as_agency) . "\n";

// Case 3: Company 3 (Direct Client) viewing Company 2 (Agency) in Agency Drawer
$jobs_3_to_2 = $resolveJobs->invoke($service, 3, 2, 'direct', 2, 'agency');
$cands_3_to_2 = $resolveCands->invoke($service, 3, $jobs_3_to_2, 2, 'direct', 'agency', 2);

echo "\nCase 3: Company 3 viewing Agency 2 in Agency Drawer:\n";
echo "  Jobs: " . json_encode($jobs_3_to_2) . "\n";
echo "  Candidates: " . json_encode($cands_3_to_2) . "\n";

// Case 4: Company 2 (Agency) viewing Company 3 (Direct Client) in Client Drawer
$jobs_2_as_agency = $resolveJobs->invoke($service, 2, 3, 'direct', 3, 'client');
$cands_2_as_agency = $resolveCands->invoke($service, 2, $jobs_2_as_agency, 3, 'direct', 'client', 3);

echo "\nCase 4: Company 2 viewing Direct Client 3 in Client Drawer:\n";
echo "  Jobs: " . json_encode($jobs_2_as_agency) . "\n";
echo "  Candidates: " . json_encode($cands_2_as_agency) . "\n";

// Verification of isolation:
$cand_intersection = array_intersect($cands_2_to_3, $cands_3_to_2);
echo "\nCross-Relationship Candidate Intersection between Rel(2->3) and Rel(3->2): " . count($cand_intersection) . "\n";
if (count($cand_intersection) === 0) {
    echo "PERFECT: ZERO CANDIDATE LEAKAGE BETWEEN RELATIONSHIPS!\n";
} else {
    echo "ERROR: CANDIDATE LEAK DETECTED!\n";
}
