<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\MailService;
use Illuminate\Support\Facades\DB;

$event = DB::table('sys_outbox_events')->where('id', 188)->first();
$payload = json_decode($event->payload, true);

$responseData = $payload['response_data'] ?? [];
$emailData = $responseData['email_data'] ?? [];
$to = $emailData['to'] ?? $payload['email'];
$subject = $responseData['email_subject'] ?? 'Account Credential';
$view = $responseData['email_template'] ?? 'email.team.create-account';
$senderName = $responseData['sender_name'] ?? '';
$fromAddress = $responseData['from_address'] ?? '';
$companyId = (int) ($event->tenant_id ?: ($responseData['company_id'] ?? 2));

echo "To: {$to}\n";
echo "Subject: {$subject}\n";
echo "View: {$view}\n";
echo "Data: " . json_encode($emailData) . "\n";
echo "Sender: {$senderName} <{$fromAddress}>\n";
echo "Company: {$companyId}\n";

$mailService = app(MailService::class);
try {
    $res = $mailService->sendEmail(
        $to,
        $subject,
        $view,
        (object) $emailData,
        [],
        $fromAddress,
        $senderName,
        [],
        $companyId
    );
    echo "Mail result: " . var_export($res, true) . "\n";
} catch (\Throwable $e) {
    echo "Mail Exception: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
