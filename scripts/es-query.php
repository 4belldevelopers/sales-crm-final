<?php
/**
 * JERO ATS — Elasticsearch Inspection & DSL Query CLI Bridge
 * Enables Antigravity to inspect index mappings and test search queries safely.
 */

$path = $argv[1] ?? '/_cat/indices?v';
$method = strtoupper($argv[2] ?? 'GET');
$body = $argv[3] ?? null;

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

$url = "http://{$host}:{$port}/" . ltrim($path, '/');

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERPWD, "{$user}:{$pass}");
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

if ($body) {
    curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
}

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err = curl_error($ch);
curl_close($ch);

if ($err) {
    fwrite(STDERR, "Elasticsearch Connection Error: {$err}\n");
    exit(1);
}

// Format JSON if response is JSON
$decoded = json_decode($response, true);
if (json_last_error() === JSON_ERROR_NONE) {
    echo json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
} else {
    echo $response . "\n";
}
