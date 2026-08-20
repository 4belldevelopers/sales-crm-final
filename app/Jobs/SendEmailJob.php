<?php

namespace App\Jobs;

use App\Services\MassMailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class SendEmailJob implements ShouldQueue
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

    protected $massMailService;

    /**
     * Dispatch queue job only after database transaction has committed.
     */
    public bool $afterCommit = true;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($email, $subject, $view, $data, $attachments, $fromAddress, $senderName, $cc)
    {
        $this->onQueue('mail');
        $this->email = $email;
        $this->subject = $subject;
        $this->view = $view;
        $this->data = $data;
        $this->attachments = $attachments;
        $this->fromAddress = $fromAddress;
        $this->senderName = $senderName;
        $this->cc = $cc;
        if (empty($this->uuid)) {
            $this->uuid = (string) Str::uuid();
        }

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(MassMailService $massMailService)
    {
        $massMailService->sendEmail($this->email, $this->subject, $this->view, $this->data, $this->attachments, $this->fromAddress, $this->senderName, $this->cc);

    }
}
