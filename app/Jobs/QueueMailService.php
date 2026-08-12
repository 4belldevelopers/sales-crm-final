<?php

namespace App\Jobs;

use App\Services\MailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class QueueMailService implements ShouldQueue
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

    /**
     * @var array|string List of Reply-To email addresses for candidate replies.
     */
    public $replyTo;

    protected $sendMailService;

    // Bounded now that failures actually throw (previously they were always silently
    // "successful", so retries never mattered) — avoids looping forever on a permanently
    // bad address once this does start throwing.
    public $tries = 3;

    public $backoff = 60;

    /**
     * Create a new job instance.
     *
     * @param  string  $email  Recipient email
     * @param  string  $subject  Email subject
     * @param  string  $view  Blade template view path
     * @param  object|array  $data  Data passed to view
     * @param  array  $attachments  Email attachments array
     * @param  string  $fromAddress  Sender email address
     * @param  string  $senderName  Sender name
     * @param  array  $cc  CC recipient emails
     * @param  int|string  $company_id  Company ID scope
     * @param  array|string  $replyTo  Reply-To recipient emails
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
     * Execute the job.
     *
     * MailService::sendEmail() returns `true` on success but a STRING error message on
     * failure (it never throws) — this job used to ignore that return value entirely, so
     * a failed send (bad SMTP response, PHPMailer exception, etc.) looked identical to a
     * successful one to the queue: no retry, no failed_jobs row, nothing in the logs. The
     * candidate/approver just never received the email with zero trace of why. Now any
     * non-true result is thrown so Laravel's queue retries it and, if it keeps failing,
     * records the real error in failed_jobs instead of silently discarding it.
     *
     * @return void
     */
    public function handle(MailService $sendMailService)
    {
        $result = $sendMailService->sendEmail($this->email, $this->subject, $this->view, $this->data, $this->attachments, $this->fromAddress, $this->senderName, $this->cc, $this->company_id, $this->replyTo);
        if ($result !== true) {
            throw new \RuntimeException('QueueMailService failed to send to '.$this->email.': '.(is_string($result) ? $result : 'unknown error'));
        }
    }
}
