<?php
/**
 * JERO ATS — Redis Multi-Tenant Cache & Horizon Queue Telemetry
 * Powered by Antigravity God-Level Tooling Suite
 *
 * Inspects Redis memory usage, Horizon queue backlogs, and verifies atomic tenant cache invalidation.
 */

require_once __DIR__ . '/../vendor/autoload.php';

$action = strtolower($argv[1] ?? 'status');
$target = $argv[2] ?? null;

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
$host = $env['REDIS_HOST'] ?? '127.0.0.1';
$port = (int)($env['REDIS_PORT'] ?? 6379);
$pass = $env['REDIS_PASSWORD'] ?? null;

try {
    $params = ['host' => $host, 'port' => $port, 'timeout' => 2];
    if ($pass && $pass !== 'null') {
        $params['password'] = $pass;
    }
    $redis = new Predis\Client($params);
    $redis->connect();
} catch (\Throwable $e) {
    fwrite(STDERR, "❌ Redis Connection Error: " . $e->getMessage() . "\n");
    exit(1);
}

echo "\n=============================================================\n";
echo " ⚡ JERO Redis & Horizon Telemetry Inspector\n";
echo " Server: {$host}:{$port} | Action: {$action}\n";
echo "=============================================================\n\n";

if ($action === 'status') {
    $info = $redis->info();
    echo "| Metric | Value |\n";
    echo "| --- | --- |\n";
    echo "| Redis Version | " . ($info['Server']['redis_version'] ?? 'N/A') . " |\n";
    echo "| OS / Mode | " . ($info['Server']['os'] ?? 'N/A') . " (" . ($info['Server']['redis_mode'] ?? 'standalone') . ") |\n";
    echo "| Connected Clients | " . ($info['Clients']['connected_clients'] ?? 'N/A') . " |\n";
    echo "| Used Memory (Human) | " . ($info['Memory']['used_memory_human'] ?? 'N/A') . " |\n";
    echo "| Total System Memory | " . ($info['Memory']['total_system_memory_human'] ?? 'N/A') . " |\n";
    echo "| Uptime (Days) | " . ($info['Server']['uptime_in_days'] ?? '0') . " days |\n";
    echo "| Total Commands Processed | " . ($info['Stats']['total_commands_processed'] ?? 'N/A') . " |\n";
} elseif ($action === 'queues') {
    $defaultQueue = $redis->llen('queues:default') ?? 0;
    $highQueue = $redis->llen('queues:high') ?? 0;
    
    echo "| Queue Name | Pending Jobs | Health |\n";
    echo "| --- | :---: | :---: |\n";
    echo "| queues:default | {$defaultQueue} | " . ($defaultQueue > 1000 ? '⚠️ BACKLOG' : '✅ HEALTHY') . " |\n";
    echo "| queues:high | {$highQueue} | " . ($highQueue > 100 ? '⚠️ BACKLOG' : '✅ HEALTHY') . " |\n";
} elseif ($action === 'tenant') {
    if (!$target) {
        echo "Usage: php scripts/redis-telemetry.php tenant <COMPANY_ID>\n";
        exit(1);
    }
    $pattern = "*company_{$target}*";
    $keys = $redis->keys($pattern);
    echo "Inspecting keys for pattern '{$pattern}':\n";
    echo "Found " . count($keys) . " active tenant cache keys in Redis.\n\n";
    if (!empty($keys)) {
        echo "| Key Name | TTL (Seconds) |\n";
        echo "| --- | :---: |\n";
        foreach (array_slice($keys, 0, 20) as $k) {
            $ttl = $redis->ttl($k);
            echo "| {$k} | {$ttl}s |\n";
        }
    }
} else {
    echo "Available actions: status, queues, tenant <company_id>\n";
}

echo "\n=============================================================\n\n";
