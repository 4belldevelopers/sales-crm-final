<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Helper\Reply;
use App\Http\Requests\SmtpSetting\UpdateSmtpSetting;
use App\Notifications\SuperAdminTestEmailNotification;
use App\Services\SuperAdmin\SuperAdminSmtpSettingMutationService;
use App\Services\SuperAdmin\SuperAdminSmtpSettingReadService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\RateLimiter;
use InvalidArgumentException;
use Throwable;

/**
 * Class SuperAdminSmtpSettingController
 *
 * Authoritative controller managing JERO's 5 dedicated SMTP delivery gateways:
 * - Gateway 8: Mass Mail Engine
 * - Gateway 9: Candidate & Resume Dispatch
 * - Gateway 10: Interview Reminders & Calendar Invites (.ics)
 * - Gateway 11: System Notifications & Tenant Alerts
 * - Gateway 12: Accounts, Offers & Billing Desk
 *
 * Implements strict CQRS (100% Elasticsearch reads / 0% MySQL reads), Two-Tier Idempotency,
 * Transactional Outbox (<15ms MySQL transactions), L2 Redis/Dragonfly caching, password preservation,
 * and multi-stage diagnostic testing.
 *
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class SuperAdminSmtpSettingController extends SuperAdminBaseController
{
    /**
     * Target primary gateway IDs strictly permitted in Super Admin.
     *
     * @var array<int>
     */
    public const GATEWAY_IDS = [8, 9, 10, 11, 12];

    public function __construct(
        protected ?SuperAdminSmtpSettingReadService $readService = null,
        protected ?SuperAdminSmtpSettingMutationService $mutationService = null
    ) {
        parent::__construct();
        $this->pageTitle = 'menu.mailSetting';
        $this->pageIcon = 'ti-email';
        $this->readService = $readService ?? app(SuperAdminSmtpSettingReadService::class);
        $this->mutationService = $mutationService ?? app(SuperAdminSmtpSettingMutationService::class);
    }

    /**
     * Display the SMTP multi-gateway settings management dashboard.
     * 100% CQRS read path from Elasticsearch & L2 Dragonfly/Redis cache.
     * 0% MySQL reads.
     *
     * @return Factory|View
     */
    public function index()
    {
        $catalog = $this->readService->getGatewaysCatalog();

        $this->smtpSetting = $catalog['gateways'];
        $this->kpiStats = $catalog['kpiStats'];

        return view('super-admin.mail-setting.index', $this->data);
    }

    /**
     * Persist updated configurations across all 5 gateways atomically.
     * Powered by SuperAdminSmtpSettingMutationService with Transactional Outbox
     * and Two-Tier Idempotency.
     *
     * @param  mixed  $id
     * @return array
     */
    public function update(UpdateSmtpSetting $request, $id = null)
    {
        $validated = $request->validator ? $request->validated() : $request->all();
        $idempotencyKey = $request->header('X-Idempotency-Key') ?: $request->input('_idempotency_key');
        $adminId = auth()->id();

        try {
            return $this->mutationService->updateSettings($validated, $idempotencyKey, $adminId, $id);
        } catch (InvalidArgumentException $e) {
            return Reply::error($e->getMessage());
        } catch (Throwable $e) {
            Log::error('SuperAdmin SMTP Settings update failure: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return Reply::error('Failed to update SMTP settings: '.$e->getMessage());
        }
    }

    /**
     * Dispatch live diagnostic verification email with multi-stage reporting and rate limiting.
     * Fetches gateway credentials from CQRS Read Service / L2 Cache (0% MySQL reads).
     *
     * @return array
     */
    public function sendTestEmail(Request $request)
    {
        // Rate limiting: maximum 5 test requests per minute per administrator
        $rateLimitKey = 'smtp_test_admin_'.(auth()->id() ?: $request->ip());
        if (RateLimiter::tooManyAttempts($rateLimitKey, 5)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);

            return response()->json(
                Reply::error("Rate limit exceeded. Please wait {$seconds} seconds before dispatching another diagnostic test."),
                429
            );
        }
        RateLimiter::hit($rateLimitKey, 60);

        $request->validate([
            'test_email' => 'required|email|max:191',
            'gateway_id' => 'nullable|integer|in:8,9,10,11,12',
        ]);

        $gatewayId = (int) ($request->gateway_id ?: 8);
        $gateway = $this->readService->getGatewayById($gatewayId);

        if (! $gateway) {
            return Reply::error('Target SMTP Gateway record not found.');
        }

        $stages = [
            'config_validation' => 'PASSED',
            'network_connect' => 'PENDING',
            'tls_handshake' => 'PENDING',
            'smtp_auth' => 'PENDING',
            'submission' => 'PENDING',
            'server_acceptance' => 'PENDING',
        ];

        // Step 1: Perform live socket handshake and timing diagnostics
        $handshake = $gateway->verifySmtp();
        if (! $handshake['success']) {
            $stages['network_connect'] = 'FAILED';

            return Reply::error('SMTP Handshake Failed: '.$handshake['message'], null, [
                'stages' => $stages,
                'latency_ms' => $handshake['latency_ms'] ?? 0,
            ]);
        }

        $stages['network_connect'] = 'PASSED';
        $stages['tls_handshake'] = 'PASSED';
        $stages['smtp_auth'] = 'PASSED';

        // Step 2: Dynamically configure mailer and dispatch verification email
        try {
            Mail::purge();

            $host = $gateway->notification_mail_host ?: $gateway->mail_host;
            $port = (int) ($gateway->notification_mail_port ?: $gateway->mail_port ?: 465);
            $username = $gateway->notification_mail_username ?: $gateway->mail_username;
            $password = $gateway->notification_mail_password ?: $gateway->mail_password;
            $encryption = strtolower((string) ($gateway->notification_mail_encryption ?: $gateway->mail_encryption));
            if ($encryption === 'none' || empty($encryption)) {
                $encryption = null;
            }

            $fromEmail = $gateway->mail_from_email ?: $gateway->email_id ?: 'noreply@jeroworks.in';
            $fromName = $gateway->mail_from_name ?: 'JERO.WORKS';

            config([
                'mail.default' => 'smtp',
                'mail.mailers.smtp.transport' => 'smtp',
                'mail.mailers.smtp.host' => $host,
                'mail.mailers.smtp.port' => $port,
                'mail.mailers.smtp.username' => $username,
                'mail.mailers.smtp.password' => $password,
                'mail.mailers.smtp.encryption' => $encryption,
                'mail.from.address' => $fromEmail,
                'mail.from.name' => $fromName,
            ]);

            $notificationData = [
                'gateway_id' => $gateway->id,
                'gateway_title' => $gateway->gateway_title,
                'host' => $host,
                'port' => $port,
                'encryption' => $encryption ?: 'NONE',
                'from_email' => $fromEmail,
                'from_name' => $fromName,
            ];

            $stages['submission'] = 'PASSED';

            Notification::route('mail', $request->test_email)
                ->notify(new SuperAdminTestEmailNotification($notificationData));

            $stages['server_acceptance'] = 'ACCEPTED_BY_SMTP_SERVER';

            return Reply::successWithData('Diagnostic email accepted by SMTP server for delivery to '.$request->test_email, [
                'gateway_id' => $gateway->id,
                'gateway_title' => $gateway->gateway_title,
                'latency_ms' => $handshake['latency_ms'] ?? 0,
                'host' => $host,
                'port' => $port,
                'from_email' => $fromEmail,
                'stages' => $stages,
                'status_note' => 'Accepted by upstream SMTP relay for delivery (downstream inbox placement subject to recipient MX routing).',
            ]);
        } catch (Throwable $e) {
            $stages['submission'] = 'FAILED';
            $stages['server_acceptance'] = 'FAILED';

            // Redact credentials from error logs and response
            $sanitizedMessage = str_replace(
                [$password, $username],
                ['[REDACTED]', '[REDACTED]'],
                $e->getMessage()
            );

            Log::error('SuperAdmin SMTP test dispatch failed: '.$sanitizedMessage, [
                'gateway_id' => $gatewayId,
            ]);

            return Reply::error('Failed to submit test email to SMTP server: '.$sanitizedMessage, null, [
                'stages' => $stages,
            ]);
        }
    }
}
