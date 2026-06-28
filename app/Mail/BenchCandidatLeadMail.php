<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BenchCandidatLeadMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $data;
    public $senderName;
    public function __construct($data , $senderName)
    {
        $this->data = $data;
        $this->senderName =  $senderName;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('email.candidate.bench-candidate-lead')
        ->subject('Bench Candidate Leads')->from(config('mail.from.address'), $this->senderName);
    }
}
