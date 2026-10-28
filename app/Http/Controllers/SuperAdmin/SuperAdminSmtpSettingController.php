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
 * Implements atomic transactional updating, multi-tenant cache invalidation,
 * and live socket handshake testing.
 */
class SuperAdminSmtpSettingController extends SuperAdminBaseController
{
    /**
     * Target primary gateway IDs managed by Super Admin.
     *
     * @var array<int>
     */
    protected const GATEWAY_IDS = [8, 9, 10, 11, 12];

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
                // Multi-gateway array batch update
                if (is_array($hostInput)) {
                    foreach ($hostInput as $gatewayId => $host) {
                        $gatewayId = (int) $gatewayId;
                        if (! in_array($gatewayId, self::GATEWAY_IDS, true)) {
                            continue;
                        }

                        $encryption = $validated['mail_encryption'][$gatewayId] ?? null;
                        if ($encryption === 'none' || empty($encryption)) {
                            $encryption = null;
                        }

                        $fromEmail = trim((string) ($validated['from_mail'][$gatewayId] ?? ''));
                        $fromName = trim((string) ($validated['mail_from_name'][$gatewayId] ?? 'JERO.WORKS'));
                        $port = (int) ($validated['mail_port'][$gatewayId] ?? 465);
                        $username = trim((string) ($validated['mail_username'][$gatewayId] ?? ''));
                        $password = (string) ($validated['mail_password'][$gatewayId] ?? '');
                        $status = (int) ($validated['status'][$gatewayId] ?? 0);
                        $dailyCount = (int) ($validated['daily_used'][$gatewayId] ?? 0);

                        $updateData = [
                            'mail_host' => trim((string) $host),
                            'mail_port' => $port,
                            'mail_username' => $username,
                            'mail_password' => $password,
                            'mail_encryption' => $encryption,
                            'notification_mail_host' => trim((string) $host),
                            'notification_mail_port' => $port,
                            'notification_mail_username' => $username,
                            'notification_mail_password' => $password,
                            'notification_mail_encryption' => $encryption,
                            'email_id' => $fromEmail,
                            'mail_from_email' => $fromEmail,
                            'mail_from_name' => $fromName,
                            'status' => $status,
                            'dailyCount' => $dailyCount,
                            'updated_at' => now(),
                        ];

                        DB::table('smtp_settings')->where('id', $gatewayId)->update($updateData);
                    }
                } else {
                    // Single gateway update fallback
                    $targetId = (int) ($id ?: 8);
                    $encryption = $validated['mail_encryption'] ?? null;
                    if ($encryption === 'none' || empty($encryption)) {
                        $encryption = null;
                    }
                    $fromEmail = trim((string) ($validated['from_mail'] ?? $validated['mail_from_email'] ?? ''));

                    $updateData = [
                        'mail_host' => trim((string) $hostInput),
                        'mail_port' => (int) ($validated['mail_port'] ?? 465),
                        'mail_username' => trim((string) ($validated['mail_username'] ?? '')),
                        'mail_password' => (string) ($validated['mail_password'] ?? ''),
                        'mail_encryption' => $encryption,
                        'notification_mail_host' => trim((string) $hostInput),
                        'notification_mail_port' => (int) ($validated['mail_port'] ?? 465),
                        'notification_mail_username' => trim((string) ($validated['mail_username'] ?? '')),
                        'notification_mail_password' => (string) ($validated['mail_password'] ?? ''),
                        'notification_mail_encryption' => $encryption,
                        'email_id' => $fromEmail,
                        'mail_from_email' => $fromEmail,
                        'mail_from_name' => trim((string) ($validated['mail_from_name'] ?? 'JERO.WORKS')),
                        'status' => (int) ($validated['status'] ?? 0),
                        'updated_at' => now(),
                    ];

                    DB::table('smtp_settings')->where('id', $targetId)->update($updateData);
                }

                // Invalidate multi-tenant cache keys
                Cache::forget('smtp_settings:default');
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
     * Dispatch live diagnostic verification email across a specified gateway.
     *
     * @return array
     */
    public function sendTestEmail(Request $request)
    {
        $request->validate([
            'test_email' => 'required|email|max:191',
            'gateway_id' => 'nullable|integer|in:8,9,10,11,12',
        ]);

        $gatewayId = (int) ($request->gateway_id ?: 8);
        $gateway = EmailSetting::where('id', $gatewayId)->first() ?: EmailSetting::first();

        if (! $gateway) {
            return Reply::error('Target SMTP Gateway record not found.');
        }

        // Step 1: Perform live socket handshake and timing diagnostics
        $handshake = $gateway->verifySmtp();
        if (! $handshake['success']) {
            return Reply::error('SMTP Handshake Failed: '.$handshake['message']);
        }

        // Step 2: Dynamically reconfigure mailer and dispatch verification email
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

            Notification::route('mail', $request->test_email)
                ->notify(new SuperAdminTestEmailNotification($notificationData));

            return Reply::successWithData('Live test email dispatched successfully to '.$request->test_email, [
                'gateway_id' => $gateway->id,
                'gateway_title' => $gateway->gateway_title,
                'latency_ms' => $handshake['latency_ms'] ?? 0,
                'host' => $host,
                'port' => $port,
                'from_email' => $fromEmail,
            ]);
        } catch (Throwable $e) {
            Log::error('SuperAdmin SMTP live test dispatch failed: '.$e->getMessage(), [
                'gateway_id' => $gatewayId,
                'trace' => $e->getTraceAsString(),
            ]);

            return Reply::error('Failed to dispatch test email: '.$e->getMessage());
        }
    }
}
