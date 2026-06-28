<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ForwardResumeMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $data;
    public $attachments;
    public function __construct($data,$attachments)
    {
        $this->data = $data;
        $this->attachments = $attachments;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

       $mail = $this->view('email.resume-forward.forward-resume')
                    ->subject($this->data->subject);
                    foreach ($this->attachments as $attachment) {
                        $mail->attach($attachment['file'], $attachment['options']);
                    }

        return $mail;
    }
}
