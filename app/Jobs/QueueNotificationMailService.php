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
     * Execute the job.
     *
     * @return void
     */
    public function handle(NotificationMailService $notificationMailService)
    {
        $notificationMailService->sendEmail($this->email, $this->subject, $this->view, $this->data, $this->attachments, $this->fromAddress, $this->senderName, $this->cc, $this->company_id, $this->replyTo);
    }
}
