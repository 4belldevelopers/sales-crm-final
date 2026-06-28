<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MeetingMail extends Mailable
{
    use Queueable, SerializesModels;

    public $googleLink;
    public $outlookLink;

    public function __construct($googleLink, $outlookLink)
    {
        $this->googleLink  = $googleLink;
        $this->outlookLink = $outlookLink;
    }

    public function build()
    {
        return $this->subject('Your Meeting Details')
                    ->view('email.meeting');
    }
}
