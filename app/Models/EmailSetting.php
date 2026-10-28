<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use PHPMailer\PHPMailer\Exception as PHPMailerException;
use PHPMailer\PHPMailer\PHPMailer;
use Throwable;

/**
 * Class EmailSetting
 *
 * Eloquent model representing system-level SMTP gateway configurations.
 * Maps to table `smtp_settings` supporting JERO's 5 dedicated mail gateways:
 * - Gateway 8: Mass Mail Engine
 * - Gateway 9: Candidate & Forward Resume Dispatch
 * - Gateway 10: Interview Reminders & Calendar Invites (.ics)
 * - Gateway 11: System & Tenant Notifications
 * - Gateway 12: Accounts, Offers & Billing Desk
 */
class EmailSetting extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'smtp_settings';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'set_smtp_message',
        'is_active',
        'gateway_title',
        'gateway_badge',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'verified' => 'boolean',
        'status' => 'integer',
        'dailyCount' => 'integer',
        'mail_port' => 'integer',
    ];

    /**
     * Verify SMTP connectivity and authentication via genuine socket handshake.
     * Replaces legacy deprecated SwiftMailer with modern PHPMailer transport.
     */
    public function verifySmtp(): array
    {
        if ($this->mail_driver !== 'smtp') {
            return [
                'success' => true,
                'message' => 'Driver is set to non-SMTP ('.$this->mail_driver.'). Socket test skipped.',
                'latency_ms' => 0,
            ];
        }

        $mail = new PHPMailer(true);
        $start = microtime(true);

        try {
            $mail->isSMTP();
            $mail->Host = (string) ($this->notification_mail_host ?: $this->mail_host);
            $mail->Port = (int) ($this->notification_mail_port ?: $this->mail_port ?: 587);
            $mail->SMTPAuth = true;
            $mail->Username = (string) ($this->notification_mail_username ?: $this->mail_username);
            $mail->Password = (string) ($this->notification_mail_password ?: $this->mail_password);
            $mail->Timeout = 10;
            $mail->SMTPDebug = 0;

            $encryption = strtolower((string) ($this->notification_mail_encryption ?: $this->mail_encryption));
            if ($encryption === 'ssl' || $mail->Port === 465) {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            } elseif ($encryption === 'tls' || $mail->Port === 587) {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            } else {
                $mail->SMTPSecure = false;
                $mail->SMTPAutoTLS = false;
            }

            // Execute handshake and authentication test
            $connected = $mail->smtpConnect();
            $latency = round((microtime(true) - $start) * 1000, 2);

            if ($connected) {
                $mail->smtpClose();

                if (! $this->verified) {
                    $this->verified = true;
                    $this->save();
                }

                return [
                    'success' => true,
                    'message' => __('messages.smtpSuccess') ?: 'SMTP connection & handshake verified successfully.',
                    'latency_ms' => $latency,
                ];
            }

            $this->verified = false;
            $this->save();

            return [
                'success' => false,
                'message' => 'SMTP handshake returned false without error code.',
                'latency_ms' => $latency,
            ];

        } catch (PHPMailerException $e) {
            $latency = round((microtime(true) - $start) * 1000, 2);
            $this->verified = false;
            $this->save();

            return [
                'success' => false,
                'message' => $e->getMessage(),
                'latency_ms' => $latency,
            ];
        } catch (Throwable $e) {
            $latency = round((microtime(true) - $start) * 1000, 2);
            $this->verified = false;
            $this->save();

            return [
                'success' => false,
                'message' => $e->getMessage(),
                'latency_ms' => $latency,
            ];
        }
    }

    /**
     * Check if the gateway is currently active.
     * In legacy database schema: status 0 = Active, status 1 = Inactive.
     */
    public function getIsActiveAttribute(): bool
    {
        return (int) $this->status === 0;
    }

    /**
     * Get clean gateway title for UI display.
     */
    public function getGatewayTitleAttribute(): string
    {
        $id = (int) $this->id;

        return match ($id) {
            8 => 'Mass Mail Engine',
            9 => 'Candidate & Resume Dispatch',
            10 => 'Interview Reminder Engine',
            11 => 'System Notification Gateway',
            12 => 'Accounts, Offers & Billing Desk',
            default => (string) ($this->mailtype ?: 'Gateway #'.$id),
        };
    }

    /**
     * Get gateway badge label (e.g. Gateway #08).
     */
    public function getGatewayBadgeAttribute(): string
    {
        return sprintf('Gateway #%02d', (int) $this->id);
    }

    /**
     * Legacy backward-compatible alert banner attribute.
     */
    public function getSetSmtpMessageAttribute(): ?string
    {
        if (! $this->verified && $this->mail_driver === 'smtp') {
            return '<div class="alert alert-danger">'
                .__('messages.smtpNotSet')
                .' <a href="'.route('admin.smtp-settings.index').'" class="btn btn-info btn-small">Visit SMTP Settings <i class="fa fa-arrow-right"></i></a>'
                .'</div>';
        }

        return null;
    }
}
