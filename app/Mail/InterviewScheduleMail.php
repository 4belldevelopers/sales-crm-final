<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Helper\GeneralMethod;

class InterviewScheduleMail extends Mailable
{
    use Queueable, SerializesModels;

    public $event;

    public function __construct($event)
    {
        $this->event = $event;
    }

    public function build()
    {
        $ics = GeneralMethod::generate([
            'uid'         => uniqid()."@meecruit.com",
            'timestamp'   => gmdate('Ymd\THis\Z'),
            'start'       => gmdate('Ymd\THis\Z', strtotime($this->event['start'])),
            'end'         => gmdate('Ymd\THis\Z', strtotime($this->event['end'])),
            'summary'     => $this->event['title'],
            'description' => $this->event['description'],
            'location'    => $this->event['location'],
        ]);

        return $this->subject("Interview Scheduled - ".$this->event['title'])
            ->view('email.interview')
            ->with(['event' => $this->event])
            ->attachData($ics, 'event.ics', [
                'mime' => 'text/calendar; charset=utf-8; method=REQUEST'
            ])
            ->withSwiftMessage(function ($message) {
                $message->getHeaders()
                    ->addTextHeader('Content-Type', 'text/calendar; method=REQUEST; charset="UTF-8"');
                $message->getHeaders()
                    ->addTextHeader('Content-class', 'urn:content-classes:calendarmessage');
            });
    }
}
