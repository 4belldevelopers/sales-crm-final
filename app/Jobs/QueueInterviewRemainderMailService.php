<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Str;
use Illuminate\Queue\SerializesModels;
use App\Services\InterviewRemainderMail;

class QueueInterviewRemainderMailService implements ShouldQueue
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
    protected $interviewRemainderMail;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($email,$subject,$view,$data,$attachments,$fromAddress,$senderName,$cc,$company_id)
    {
        $this->email = $email;
        $this->subject = $subject;
        $this->view = $view;
        $this->data = $data;
        $this->attachments = $attachments;
        $this->fromAddress = $fromAddress;
        $this->senderName = $senderName;
        $this->cc = $cc;
        $this->company_id =$company_id;
        if (empty($this->uuid)) {
            $this->uuid = (string) Str::uuid();
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(InterviewRemainderMail $interviewRemainderMail)
    {
        $interviewRemainderMail->sendEmail($this->email,$this->subject,$this->view, $this->data, $this->attachments, $this->fromAddress,$this->senderName,$this->cc,$this->company_id);
    }
}
