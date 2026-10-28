<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Helper\Reply;
use App\Http\Requests\SmtpSetting\UpdateSmtpSetting;
use App\Models\EmailSetting;
use App\Notifications\SuperAdminTestEmailNotification;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
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
 * Implements atomic transactional updating, post-commit cache invalidation,
 * password preservation, counter protection, and multi-stage socket diagnostics.
 */
class SuperAdminSmtpSettingController extends SuperAdminBaseController
{
    /**
     * Target primary gateway IDs strictly permitted in Super Admin.
     *
     * @var array<int>
     */
    public const GATEWAY_IDS = [8, 9, 10, 11, 12];

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'menu.mailSetting';
        $this->pageIcon = 'ti-email';
    }

    /**
     * Display the SMTP multi-gateway settings management dashboard.
     *
     * @return Factory|View
     */
    public function index()
    {
        $gateways = EmailSetting::whereIn('id', self::GATEWAY_IDS)
            ->orderBy('id', 'asc')
            ->get();

        // Calculate KPI summary metrics for Tier 1 toolbar
        $totalGateways = $gateways->count();
        $activeGateways = $gateways->where('status', 0)->count();
        $inactiveGateways = $gateways->where('status', 1)->count();
        $totalDailySent = (int) $gateways->sum('dailyCount');

        $this->smtpSetting = $gateways;
        $this->kpiStats = [
            'total_gateways' => $totalGateways,
            'active_gateways' => $activeGateways,
            'inactive_gateways' => $inactiveGateways,
            'total_daily_sent' => $totalDailySent,
        ];

        return view('super-admin.mail-setting.index', $this->data);
    }

    /**
     * Persist updated configurations across all 5 gateways atomically.
     * Preserves existing passwords when omitted and protects live operational counters.
     *
     * @param  mixed  $id
     * @return array
     */
    public function update(UpdateSmtpSetting $request, $id = null)
    {
        $validated = $request->validator ? $request->validated() : $request->all();
        $hostInput = $validated['mail_host'] ?? null;

        try {
            DB::transaction(function () use ($validated, $hostInput, $id) {
                // Multi-gateway batch updating
                if (is_array($hostInput)) {
                    // Pre-verify that all requested gateways exist in database
                    $existingGateways = EmailSetting::whereIn('id', array_keys($hostInput))
                        ->get()
                        ->keyBy('id');

                    foreach ($hostInput as $gatewayId => $host) {
                        $gatewayId = (int) $gatewayId;

                        if (! in_array($gatewayId, self::GATEWAY_IDS, true)) {
                            throw new InvalidArgumentException("Unauthorized gateway ID ({$gatewayId}) submitted.");
                        }

                        $existing = $existingGateways->get($gatewayId);
                        if (! $existing) {
                            throw new InvalidArgumentException("Gateway record #{$gatewayId} does not exist in smtp_settings.");
                        }

                        $encryption = $validated['mail_encryption'][$gatewayId] ?? null;
                        if ($encryption === 'none' || empty($encryption)) {
                            $encryption = null;
                        }

                        $fromEmail = trim((string) ($validated['from_mail'][$gatewayId] ?? ''));
                        $fromName = trim((string) ($validated['mail_from_name'][$gatewayId] ?? 'JERO.WORKS'));
                        $port = (int) ($validated['mail_port'][$gatewayId] ?? 465);
                        $username = trim((string) ($validated['mail_username'][$gatewayId] ?? ''));

                        // Password preservation: retain existing password if omitted or blank
                        $submittedPassword = $validated['mail_password'][$gatewayId] ?? null;
                        $finalPassword = (! is_null($submittedPassword) && trim((string) $submittedPassword) !== '')
                            ? (string) $submittedPassword
                            : (string) $existing->mail_password;

                        $status = (int) ($validated['status'][$gatewayId] ?? 0);

                        // Note: dailyCount is explicitly NOT updated to prevent overwriting live counters
                        $updateData = [
                            'mail_host' => trim((string) $host),
                            'mail_port' => $port,
                            'mail_username' => $username,
                            'mail_password' => $finalPassword,
                            'mail_encryption' => $encryption,
                            'notification_mail_host' => trim((string) $host),
                            'notification_mail_port' => $port,
                            'notification_mail_username' => $username,
                            'notification_mail_password' => $finalPassword,
                            'notification_mail_encryption' => $encryption,
                            'email_id' => $fromEmail,
                            'mail_from_email' => $fromEmail,
                            'mail_from_name' => $fromName,
                            'status' => $status,
                            'updated_at' => now(),
                        ];

                        DB::table('smtp_settings')->where('id', $gatewayId)->update($updateData);
                    }
                } else {
                    // Single gateway fallback
                    $targetId = (int) ($id ?: 8);
                    if (! in_array($targetId, self::GATEWAY_IDS, true)) {
                        throw new InvalidArgumentException("Unauthorized gateway ID ({$targetId}).");
                    }

                    $existing = EmailSetting::find($targetId);
                    if (! $existing) {
                        throw new InvalidArgumentException("Gateway record #{$targetId} does not exist.");
                    }

                    $encryption = $validated['mail_encryption'] ?? null;
                    if ($encryption === 'none' || empty($encryption)) {
                        $encryption = null;
                    }
                    $fromEmail = trim((string) ($validated['from_mail'] ?? $validated['mail_from_email'] ?? ''));

                    $submittedPassword = $validated['mail_password'] ?? null;
                    $finalPassword = (! is_null($submittedPassword) && trim((string) $submittedPassword) !== '')
                        ? (string) $submittedPassword
                        : (string) $existing->mail_password;

                    $updateData = [
                        'mail_host' => trim((string) $hostInput),
                        'mail_port' => (int) ($validated['mail_port'] ?? 465),
                        'mail_username' => trim((string) ($validated['mail_username'] ?? '')),
                        'mail_password' => $finalPassword,
                        'mail_encryption' => $encryption,
                        'notification_mail_host' => trim((string) $hostInput),
                        'notification_mail_port' => (int) ($validated['mail_port'] ?? 465),
                        'notification_mail_username' => trim((string) ($validated['mail_username'] ?? '')),
                        'notification_mail_password' => $finalPassword,
                        'notification_mail_encryption' => $encryption,
                        'email_id' => $fromEmail,
                        'mail_from_email' => $fromEmail,
                        'mail_from_name' => trim((string) ($validated['mail_from_name'] ?? 'JERO.WORKS')),
                        'status' => (int) ($validated['status'] ?? 0),
                        'updated_at' => now(),
                    ];

                    DB::table('smtp_settings')->where('id', $targetId)->update($updateData);
                }

                // Reliable post-commit cache invalidation
                DB::afterCommit(function () {
                    try {
                        Cache::forget('smtp_settings:default');
                    } catch (Throwable $e) {
                        Log::warning('SMTP settings cache eviction warning: '.$e->getMessage());
                    }
                });
            });

            return Reply::success(__('messages.updatedSuccessfully'));
        } catch (Throwable $e) {
            Log::error('SuperAdmin SMTP Settings update failure: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return Reply::error('Failed to update SMTP settings: '.$e->getMessage());
        }
    }

    /**
     * Dispatch live diagnostic verification email with multi-stage reporting and rate limiting.
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
        $gateway = EmailSetting::where('id', $gatewayId)->first();

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
