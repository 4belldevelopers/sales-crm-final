<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeMail extends Mailable
{
    use Queueable, SerializesModels;
    public $userName;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($userName)
    {
        //
        $this->userName = $userName;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // return $this->view('view.name'); resources\views\email\company-profile\email-confirmation.blade.php
        return $this->view('email.company-profile.email-confirmation')
        ->subject('Welcome to Our Application');
    }
}
