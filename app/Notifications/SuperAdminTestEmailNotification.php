<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Class SuperAdminTestEmailNotification
 *
 * Diagnostic verification notification dispatched by Super Admin to test
 * live socket delivery, authentication, and routing across JERO's 5 mail gateways.
 */
class SuperAdminTestEmailNotification extends Notification
{
    use Queueable;

    /**
     * Diagnostic payload metadata.
     */
    public array $gatewayData;

    /**
     * Create a new notification instance.
     *
     * @param  array  $gatewayData  Diagnostic socket information
     */
    public function __construct(array $gatewayData = [])
    {
        $this->gatewayData = $gatewayData;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     */
    public function toMail($notifiable): MailMessage
    {
        $gatewayId = $this->gatewayData['gateway_id'] ?? 'N/A';
        $gatewayTitle = $this->gatewayData['gateway_title'] ?? 'Global Gateway';
        $host = $this->gatewayData['host'] ?? 'N/A';
        $port = $this->gatewayData['port'] ?? 'N/A';
        $encryption = strtoupper((string) ($this->gatewayData['encryption'] ?? 'NONE'));
        $fromEmail = $this->gatewayData['from_email'] ?? config('mail.from.address');
        $fromName = $this->gatewayData['from_name'] ?? config('mail.from.name');
        $timestamp = now()->timezone('UTC')->format('Y-m-d H:i:s').' UTC';

        return (new MailMessage)
            ->subject(sprintf('[JERO SMTP Verification] Live Test Email — Gateway #%s (%s)', $gatewayId, $gatewayTitle))
            ->greeting('Hello Super Administrator,')
            ->line('This is an authoritative verification email dispatched by your JERO Recruitment SaaS platform to validate socket delivery and credential authentication.')
            ->line('✅ **SMTP Gateway Connection & Handshake Status: VERIFIED**')
            ->line(sprintf('• **Gateway Identity:** Gateway #%s — %s', $gatewayId, $gatewayTitle))
            ->line(sprintf('• **SMTP Host:** %s', $host))
            ->line(sprintf('• **Port & Protocol:** %s (%s)', $port, $encryption))
            ->line(sprintf('• **Sender Envelope:** %s <%s>', $fromName, $fromEmail))
            ->line(sprintf('• **Verification Timestamp:** %s', $timestamp))
            ->action('Manage SMTP Settings', route('superadmin.smtp-settings.index'))
            ->line('If you or an authorized administrator did not request this test, please inspect your Super Admin audit logs immediately.')
            ->salutation('Warm regards,'.PHP_EOL.'JERO Engineering & Delivery Infrastructure');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     */
    public function toArray($notifiable): array
    {
        return $this->gatewayData;
    }
}
