<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = DB::table('users')->where('email', 'like', 'recruiter2_99992%')->first();
echo "USER ROW:\n";
echo json_encode($user, JSON_PRETTY_PRINT) . "\n\n";

if ($user) {
    echo "USER COMPANY:\n";
    $company = DB::table('companies')->where('id', $user->company_id)->first();
    echo json_encode($company, JSON_PRETTY_PRINT) . "\n\n";

    echo "INVITATIONS FOR EMAIL OR USER:\n";
    if (Schema::hasTable('invitations')) {
        $inv = DB::table('invitations')->where('email', 'like', 'recruiter2_99992%')->get();
        echo "invitations table: " . json_encode($inv, JSON_PRETTY_PRINT) . "\n";
    }
    if (Schema::hasTable('team_invitations')) {
        $tinv = DB::table('team_invitations')->where('email', 'like', 'recruiter2_99992%')->get();
        echo "team_invitations table: " . json_encode($tinv, JSON_PRETTY_PRINT) . "\n";
    }
    if (Schema::hasTable('user_invitations')) {
        $uinv = DB::table('user_invitations')->where('email', 'like', 'recruiter2_99992%')->get();
        echo "user_invitations table: " . json_encode($uinv, JSON_PRETTY_PRINT) . "\n";
    }

    echo "\nOUTBOX EVENTS FOR THIS USER/EMAIL:\n";
    $outbox = DB::table('sys_outbox_events')
        ->where('payload', 'like', '%recruiter2_99992%')
        ->get();
    echo json_encode($outbox, JSON_PRETTY_PRINT) . "\n";
}
