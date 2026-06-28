<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MassMailingMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $data;
    public $attachments;
    public $senderName;
    public $fromAddress;
    public function __construct($data,$attachments,$senderName,$fromAddress)
    {
        $this->data = $data;
        $this->attachments = $attachments;
        $this->senderName =  $senderName;
        $this->fromAddress = $fromAddress;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // dd($this->fromAddress, $this->senderName);
        $mail = $this->view('email.resume-forward.massmail')
                    ->subject($this->data->subject)->from($this->fromAddress, $this->senderName);
                    foreach ($this->attachments as $attachment) {
                        $mail->attach($attachment['file'], $attachment['options']);
                    }

        return $mail;
    }
}
