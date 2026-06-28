<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InterviewTrackerMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $data;
    public $subject;
     public $attachments;
    public function __construct($data,$subject,$attachments)
    {
        $this->data = $data;
        $this->subject = $subject;
         $this->attachments = $attachments;
    }

    /**
     * Build the message. 
     *
     * @return $this
     */
    public function build()
    {
      $mail = $this->view('email.candidate.interview-tracker')
        ->subject($this->subject)
        ->with('data',$this->data);
        foreach ($this->attachments as $attachment) {
                        $mail->attach($attachment['file'], $attachment['options']);
                    }
    return $mail;
        
    }
}
