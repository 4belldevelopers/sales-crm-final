<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\DB;

echo "=== USER 99992 FULL ROW ===\n";
$u = DB::table('users')->where('id', 99992)->first();
echo json_encode($u, JSON_PRETTY_PRINT) . "\n";

echo "=== ANY REQUESTS / APPROVALS / INVITATIONS FOR COMPANY 2 ===\n";
// Let's check tables that might contain requests or approvals
$tables = ['team_invitations', 'company_join_requests', 'user_invitations', 'company_members', 'company_users'];
foreach ($tables as $t) {
    if (\Illuminate\Support\Facades\Schema::hasTable($t)) {
        echo "Table: $t\n";
        $rows = DB::table($t)->where('company_id', 2)->get();
        echo json_encode($rows, JSON_PRETTY_PRINT) . "\n";
    }
}

// Let's check all tables with 'invite' or 'request' or 'approval' in name
$allTables = DB::select('SHOW TABLES');
$tableKey = 'Tables_in_' . env('DB_DATABASE');
$matchTables = [];
foreach ($allTables as $tableObj) {
    $tname = $tableObj->$tableKey;
    if (preg_match('/invite|request|approval|member/i', $tname)) {
        $matchTables[] = $tname;
    }
}
echo "Matching tables: " . implode(', ', $matchTables) . "\n";
