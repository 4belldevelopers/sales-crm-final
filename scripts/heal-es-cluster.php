<?php
/**
 * scripts/heal-es-cluster.php
 *
 * Sets number_of_replicas to 0 across all Elasticsearch indices on single-node staging.
 * Heals cluster from Yellow to 100% Green.
 */

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

$url = "http://{$host}:{$port}/*/_settings";
$payload = json_encode([
    'index' => [
        'number_of_replicas' => 0
    ]
]);

echo "Sending update to {$url}...\n";

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERPWD, "{$user}:{$pass}");
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);

$res = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err = curl_error($ch);
curl_close($ch);

if ($err) {
    echo "cURL Error: {$err}\n";
    exit(1);
}

echo "HTTP Code: {$code}\n";
echo "Response: {$res}\n";

// Check health immediately after
sleep(1);
$healthUrl = "http://{$host}:{$port}/_cluster/health";
$ch = curl_init($healthUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERPWD, "{$user}:{$pass}");
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$hRes = curl_exec($ch);
curl_close($ch);

echo "\nUpdated Cluster Health:\n";
$decoded = json_decode($hRes, true);
if ($decoded) {
    echo json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
} else {
    echo $hRes . "\n";
}
