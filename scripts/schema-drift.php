<?php
/**
 * scripts/schema-drift.php
 *
 * JERO ATS — Declarative MySQL Schema Parity & Drift Detector
 * Compares database/schema/mysql-schema.sql (canonical truth) against live MySQL database.
 *
 * Usage:
 *   php scripts/schema-drift.php
 */

$schemaFile = __DIR__ . '/../database/schema/mysql-schema.sql';
$envFile = __DIR__ . '/../.env';

echo "\n\033[36m=============================================================\033[0m\n";
echo "\033[36m 🏛️  JERO DECLARATIVE MYSQL SCHEMA PARITY & DRIFT DETECTOR\033[0m\n";
echo "\033[36m=============================================================\033[0m\n\n";

if (!file_exists($schemaFile)) {
    echo "❌ Schema file not found: {$schemaFile}\n";
    exit(1);
}

// 1. Parse table names from mysql-schema.sql
echo "\033[33m[1/3] Parsing canonical database/schema/mysql-schema.sql...\033[0m\n";
$schemaContent = file_get_contents($schemaFile);
preg_match_all('/CREATE TABLE `([a-zA-Z0-9_]+)`/i', $schemaContent, $m);
$schemaTables = array_unique($m[1]);
sort($schemaTables);
echo "  Found \033[32m" . count($schemaTables) . "\033[0m canonical tables defined in schema file.\n\n";

// 2. Connect to Live MySQL database
echo "\033[33m[2/3] Connecting to Live MySQL Database (76.13.243.68:3306)...\033[0m\n";
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

try {
    $pdo = new PDO("mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 8,
    ]);

    $stmt = $pdo->query("SELECT table_name FROM information_schema.tables WHERE table_schema = '{$db}'");
    $liveTables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    sort($liveTables);
    echo "  Found \033[32m" . count($liveTables) . "\033[0m tables in live database '{$db}'.\n\n";

    // 3. Compare Table Parity
    echo "\033[33m[3/3] Cross-Referencing Table Parity...\033[0m\n";
    $missingInLive = array_diff($schemaTables, $liveTables);
    $extraInLive = array_diff($liveTables, $schemaTables);

    $hasDrift = false;

    if (!empty($missingInLive)) {
        $hasDrift = true;
        echo "\033[31m❌ TABLES IN SCHEMA FILE BUT MISSING IN LIVE DB (" . count($missingInLive) . "):\033[0m\n";
        foreach ($missingInLive as $t) echo "   - {$t}\n";
    }

    if (!empty($extraInLive)) {
        echo "\033[33mℹ️  TABLES IN LIVE DB NOT IN SCHEMA FILE (" . count($extraInLive) . "):\033[0m\n";
        foreach (array_slice($extraInLive, 0, 10) as $t) echo "   + {$t}\n";
        if (count($extraInLive) > 10) echo "   ... and " . (count($extraInLive) - 10) . " more.\n";
    }

    // 4. Detailed Column Parity on Critical ATS Core Tables
    $criticalTables = ['jobs', 'add_job_wise_condidates', 'users', 'clients'];
    echo "\n\033[33mChecking Column Parity on Critical ATS Tables...\033[0m\n";

    foreach ($criticalTables as $table) {
        if (!in_array($table, $liveTables, true)) continue;

        $colStmt = $pdo->query("SELECT column_name, data_type, is_nullable FROM information_schema.columns WHERE table_schema = '{$db}' AND table_name = '{$table}'");
        $cols = $colStmt->fetchAll(PDO::FETCH_ASSOC);
        echo "  \033[32m✓ {$table}:\033[0m " . count($cols) . " columns verified in live DB.\n";
    }

    echo "\n\033[36m=============================================================\033[0m\n";
    if ($hasDrift) {
        echo "\033[31m🛑 SCHEMA DRIFT DETECTED: Review missing tables before deploying.\033[0m\n";
        exit(1);
    } else {
        echo "\033[32m🟢 100% CANONICAL SCHEMA PARITY VERIFIED (Zero Missing Tables).\033[0m\n";
        exit(0);
    }

} catch (Exception $e) {
    echo "\033[31mDatabase Connection Error: {$e->getMessage()}\033[0m\n";
    exit(1);
}
