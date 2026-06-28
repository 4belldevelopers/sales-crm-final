<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Support\Str;

class InterviewReminder extends Notification implements ShouldQueue
{
    use Queueable;

    public $uniq;
    protected $interview;

    public function __construct($interview)
    {
        $this->uniq = (string) Str::uuid();
        $this->interview = $interview;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast']; // Reverb
    }
    public function dataPayload()
    {
        return [
            'uniq'               => $this->uniq,
            'title' => 'Interview Reminder',
            'message' => 'Interview Reminder',
            'candidate_name' => $this->interview->candidate_name ?? '',
            'job_title' => $this->interview->job_title ?? '',
            'company_name'  => $this->interview->company_name ?? '',
            'interview_datetime' => $this->interview->interview_time ?? '',
            'interview_id'       => $this->interview->id ?? null,
            'url'                => url('/interviews/' . ($this->interview->id ?? '')),
        ];
    }

     public function toArray($notifiable)
    {
        return $this->dataPayload();
    }
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage($this->dataPayload());
    }
}
