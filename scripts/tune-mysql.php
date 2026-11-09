<?php
/**
 * scripts/tune-mysql.php
 *
 * Safely applies optimal dynamic concurrency parameters to live MySQL 8.0 instance:
 *   - max_connections: 151 -> 500 (Prevents worker connection exhaustion)
 *   - thread_cache_size: 9 -> 64 (Eliminates thread creation CPU spikes)
 *   - wait_timeout: 28800 -> 600 (Reclaims idle/dead connections)
 *   - interactive_timeout: 28800 -> 600
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

$host = $env['DB_HOST'] ?? '127.0.0.1';
$port = $env['DB_PORT'] ?? '3306';
$user = $env['DB_USERNAME'] ?? 'root';
$pass = $env['DB_PASSWORD'] ?? '';
$db   = $env['DB_DATABASE'] ?? 'dbmaster';

echo "\n\033[36m=============================================================\033[0m\n";
echo "\033[36m ⚡ JERO LIVE MYSQL DYNAMIC CONCURRENCY OPTIMIZER\033[0m\n";
echo "\033[36m=============================================================\033[0m\n\n";

try {
    $pdo = new PDO("mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 5,
    ]);

    // 1. Fetch current settings
    $stmt = $pdo->query("SHOW VARIABLES WHERE Variable_name IN ('max_connections', 'thread_cache_size', 'wait_timeout', 'interactive_timeout')");
    $before = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

    echo "\033[33m--- Before Tuning ---\033[0m\n";
    foreach ($before as $k => $v) {
        echo "  - {$k}: {$v}\n";
    }

    // 2. Apply optimizations
    echo "\n\033[32m▶ Applying Dynamic Optimizations...\033[0m\n";
    $pdo->exec("SET GLOBAL max_connections = 500");
    $pdo->exec("SET GLOBAL thread_cache_size = 64");
    $pdo->exec("SET GLOBAL wait_timeout = 600");
    $pdo->exec("SET GLOBAL interactive_timeout = 600");
    $pdo->exec("SET GLOBAL innodb_buffer_pool_size = 1073741824");

    // 3. Fetch updated settings
    $stmt = $pdo->query("SHOW VARIABLES WHERE Variable_name IN ('max_connections', 'thread_cache_size', 'wait_timeout', 'interactive_timeout', 'innodb_buffer_pool_size')");
    $after = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

    echo "\n\033[32m--- After Tuning (Live & Verified) ---\033[0m\n";
    foreach ($after as $k => $v) {
        echo "  \033[32m✓ {$k}:\033[0m {$v}\n";
    }

    echo "\n\033[36m=============================================================\033[0m\n";
    echo "\033[32m 🟢 MySQL Concurrency Tuning Applied Successfully!\033[0m\n";
    echo "\033[36m=============================================================\033[0m\n\n";

} catch (Exception $e) {
    echo "\033[31m❌ Error tuning MySQL: {$e->getMessage()}\033[0m\n";
    exit(1);
}
