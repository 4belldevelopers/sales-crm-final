<?php
/**
 * JERO ATS — Read-Only Database Introspection CLI Bridge
 * Enables Antigravity to inspect schemas, column types, and query plans safely.
 *
 * Strictly enforces READ-ONLY commands (SELECT, SHOW, DESCRIBE, EXPLAIN).
 * Destructive queries (INSERT, UPDATE, DELETE, DROP, ALTER, TRUNCATE) are blocked.
 */

require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$query = $argv[1] ?? null;

if (!$query) {
    echo "Usage: php scripts/db-query.php \"<SQL_QUERY>\"\n";
    echo "Allowed: SELECT, SHOW, DESCRIBE, DESC, EXPLAIN\n";
    exit(1);
}

$trimmed = trim($query);
$upper = strtoupper($trimmed);

$allowedPrefixes = ['SELECT', 'SHOW', 'DESCRIBE', 'DESC', 'EXPLAIN'];
$isAllowed = false;

foreach ($allowedPrefixes as $prefix) {
    if (str_starts_with($upper, $prefix)) {
        $isAllowed = true;
        break;
    }
}

// Block any query containing destructive statements
$blockedWords = ['INSERT ', 'UPDATE ', 'DELETE ', 'DROP ', 'ALTER ', 'TRUNCATE ', 'REPLACE ', 'GRANT ', 'REVOKE '];
foreach ($blockedWords as $blocked) {
    if (str_contains($upper, $blocked)) {
        $isAllowed = false;
        break;
    }
}

if (!$isAllowed) {
    fwrite(STDERR, "❌ SECURITY ERROR: Only safe read-only queries (SELECT, SHOW, DESCRIBE, EXPLAIN) are permitted.\n");
    exit(1);
}

try {
    $results = \Illuminate\Support\Facades\DB::select($query);

    if (empty($results)) {
        echo "No records returned (0 rows).\n";
        exit(0);
    }

    $arrayResults = json_decode(json_encode($results), true);
    
    // Print in formatted JSON or Markdown table
    $columns = array_keys($arrayResults[0]);
    
    echo "| " . implode(" | ", $columns) . " |\n";
    echo "| " . implode(" | ", array_fill(0, count($columns), "---")) . " |\n";
    
    $maxRows = 30;
    $count = 0;
    foreach ($arrayResults as $row) {
        $cleanRow = array_map(function($val) {
            if (is_null($val)) return 'NULL';
            $str = (string)$val;
            return str_replace(["\n", "\r", "|"], [" ", "", "\\|"], substr($str, 0, 50));
        }, array_values($row));
        echo "| " . implode(" | ", $cleanRow) . " |\n";
        $count++;
        if ($count >= $maxRows) {
            $remaining = count($arrayResults) - $maxRows;
            if ($remaining > 0) {
                echo "| ... and {$remaining} more rows ... |\n";
            }
            break;
        }
    }
} catch (\Throwable $e) {
    fwrite(STDERR, "Database Query Error: " . $e->getMessage() . "\n");
    exit(1);
}
