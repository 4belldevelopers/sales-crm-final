<?php

namespace App\Jobs;

use App\Services\NotificationMailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class QueueNotificationMailService implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $email;

    public $subject;

    public $view;

    public $data;

    public $attachments;

    public $fromAddress;

    public $senderName;

    public $cc;

    public $company_id;

    public $replyTo;

    protected $notificationMailService;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($email, $subject, $view, $data, $attachments, $fromAddress, $senderName, $cc, $company_id, $replyTo = [])
    {
        $this->email = $email;
        $this->subject = $subject;
        $this->view = $view;
        $this->data = $data;
        $this->attachments = $attachments;
        $this->fromAddress = $fromAddress;
        $this->senderName = $senderName;
        $this->cc = $cc;
        $this->company_id = $company_id;
        $this->replyTo = $replyTo;
        if (empty($this->uuid)) {
            $this->uuid = (string) Str::uuid();
        }
    }

    /**
     * Maximum number of attempts before marking job as failed.
     * Prevents infinite runaway retry loops.
     */
    public int $tries = 3;

    /**
     * Backoff delays in seconds between retries [15s, 60s, 180s].
     * Prevents rapid hammer loops during transient network or SMTP timeouts.
     */
    public array $backoff = [15, 60, 180];

    /**
     * Maximum execution time for the job in seconds.
     */
    public int $timeout = 60;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(NotificationMailService $notificationMailService)
    {
        // Tenancy-scoped idempotency key (company_id + normalized recipient + subject hash)
        $normalizedRecipient = strtolower(trim((string) $this->email));
        $recipientSubjectHash = md5($normalizedRecipient.':'.$this->subject);
        $idempotencyKey = 'ats:mail_sent:comp_'.$this->company_id.':'.$recipientSubjectHash;
        $lockKey = 'lock:mail_send:comp_'.$this->company_id.':'.$recipientSubjectHash;

        // If an identical notification was successfully sent recently, suppress duplicate send
        if (cache()->has($idempotencyKey)) {
            \Illuminate\Support\Facades\Log::info('QueueNotificationMailService: duplicate delivery suppressed', [
                'company_id' => $this->company_id,
                'email' => $this->email,
                'subject' => $this->subject,
            ]);

            return;
        }

        $lock = cache()->lock($lockKey, 30);
        if (! $lock->get()) {
            \Illuminate\Support\Facades\Log::info('QueueNotificationMailService: concurrent execution suppressed by lock', [
                'company_id' => $this->company_id,
                'email' => $this->email,
                'subject' => $this->subject,
            ]);

            return;
        }

        try {
            // Re-check idempotency key after acquiring lock to guard against race condition
            if (cache()->has($idempotencyKey)) {
                return;
            }

            $notificationMailService->sendEmail(
                $this->email,
                $this->subject,
                $this->view,
                $this->data,
                $this->attachments,
                $this->fromAddress,
                $this->senderName,
                $this->cc,
                $this->company_id,
                $this->replyTo
            );

            // Mark as sent for 300 seconds (5 min) to guard against duplicate queued copies
            cache()->put($idempotencyKey, true, 300);
        } finally {
            optional($lock)->release();
        }
    }

    /**
     * Handle job failure with structured error logging.
     */
    public function failed(?\Throwable $exception): void
    {
        \Illuminate\Support\Facades\Log::error('QueueNotificationMailService permanently failed', [
            'company_id' => $this->company_id,
            'email' => $this->email,
            'subject' => $this->subject,
            'error' => $exception ? $exception->getMessage() : 'Unknown error',
        ]);
    }
}
