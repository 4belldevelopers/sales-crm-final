<?php
/**
 * JERO ATS — CQRS Data Drift & Synchronization Detector
 * Powered by Antigravity God-Level Tooling Suite
 *
 * Compares MySQL (Write Store) against Elasticsearch (Read Store) for any entity.
 * Detects stale candidate/job records, missing indexed fields, and sync failures.
 */

require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$type = strtolower($argv[1] ?? 'job');
$id = $argv[2] ?? null;

if (!$id) {
    echo "Usage: php scripts/cqrs-drift-detector.php [job|candidate] <ENTITY_ID>\n";
    exit(1);
}

// 1. Fetch from MySQL
$mysqlRow = null;
$esIndex = '';
$compareFields = [];

if ($type === 'job') {
    $mysqlRow = \Illuminate\Support\Facades\DB::table('jobs')->where('id', $id)->first();
    $esIndex = 'ats_jobs_elastic';
    $compareFields = [
        'id' => ['mysql' => 'id', 'es' => 'id'],
        'company_id' => ['mysql' => 'company_id', 'es' => 'comId'],
        'title' => ['mysql' => 'title', 'es' => 'title'],
        'status' => ['mysql' => 'status', 'es' => 'status'],
        'min_salary' => ['mysql' => 'min_salary', 'es' => 'min_salary'],
        'max_salary' => ['mysql' => 'max_salary', 'es' => 'max_salary'],
        'total_positions' => ['mysql' => 'total_positions', 'es' => 'total_positions']
    ];
} elseif ($type === 'candidate') {
    $mysqlRow = \Illuminate\Support\Facades\DB::table('add_job_wise_condidates')->where('id', $id)->first();
    $esIndex = 'ats_candidates_elastic';
    $compareFields = [
        'id' => ['mysql' => 'id', 'es' => 'id'],
        'company_id' => ['mysql' => 'company_id', 'es' => 'company_id'],
        'email' => ['mysql' => 'email', 'es' => 'email'],
        'name' => ['mysql' => 'full_name', 'es' => 'full_name']
    ];
} else {
    fwrite(STDERR, "Unsupported entity type: {$type}\n");
    exit(1);
}

if (!$mysqlRow) {
    echo "❌ MySQL Record not found for {$type} ID: {$id}\n";
    exit(1);
}

// 2. Fetch from Elasticsearch
$envFile = __DIR__ . '/../.env';
$env = [];
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (str_starts_with(trim($line), '#') || !str_contains($line, '=')) continue;
        [$k, $v] = explode('=', $line, 2);
        $env[trim($k)] = trim(trim($v), "'\"");
    }
}
$host = $env['ELASTICSEARCH_HOST'] ?? '76.13.243.68';
$port = $env['ELASTICSEARCH_PORT'] ?? '9200';
$user = $env['ELASTICSEARCH_USER'] ?? 'elastic';
$pass = $env['ELASTICSEARCH_PASS'] ?? 'JeroES@123';

$esUrl = "http://{$host}:{$port}/{$esIndex}/_doc/{$id}";
$ch = curl_init($esUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERPWD, "{$user}:{$pass}");
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
$esResponse = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$esDoc = null;
if ($httpCode === 200) {
    $decoded = json_decode($esResponse, true);
    $esDoc = $decoded['_source'] ?? null;
}

// 3. Diff and Display
echo "\n=============================================================\n";
echo " ⚖️  JERO CQRS DRIFT & SYNCHRONIZATION DETECTOR\n";
echo " Entity: {$type} | ID: {$id} | ES Index: {$esIndex}\n";
echo "=============================================================\n\n";

if (!$esDoc) {
    echo "❌ CRITICAL DRIFT: Entity exists in MySQL but is MISSING from Elasticsearch! (HTTP {$httpCode})\n";
    echo "💡 Action needed: Re-index this record using the relevant observer/job.\n\n";
    exit(1);
}

echo "| Field | MySQL (Write Store) | Elasticsearch (Read Store) | Sync Status |\n";
echo "| --- | --- | --- | :---: |\n";

$hasDrift = false;
$mysqlArr = (array)$mysqlRow;

foreach ($compareFields as $label => $keys) {
    $mysqlVal = (string)($mysqlArr[$keys['mysql']] ?? 'N/A');
    $esVal = (string)($esDoc[$keys['es']] ?? 'N/A');

    $isMatch = ($mysqlVal === $esVal);
    if (!$isMatch && ($label === 'title' || $label === 'name')) {
        // Soft match for trimmed text
        $isMatch = (trim(strtolower($mysqlVal)) === trim(strtolower($esVal)));
    }

    $statusIcon = $isMatch ? '✅ MATCH' : '❌ DRIFT';
    if (!$isMatch) $hasDrift = true;

    echo "| {$label} | " . substr($mysqlVal, 0, 30) . " | " . substr($esVal, 0, 30) . " | {$statusIcon} |\n";
}

echo "\n=============================================================\n";
if ($hasDrift) {
    echo " ⚠️  VERDICT: Data Drift Detected between MySQL and Elasticsearch!\n";
} else {
    echo " ✅ VERDICT: 100% CQRS Synchronization Verified (Zero Drift).\n";
}
echo "=============================================================\n\n";
