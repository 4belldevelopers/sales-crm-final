<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\InterviewSchedule;
use App\Models\User; // Or your candidate model
use App\Notifications\InterviewReminderNotification;
use Carbon\Carbon;

class SendInterviewReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'interview:reminder';
    protected $description = 'Send interview reminders 30 minutes before interview time';
    // protected $signature = 'app:send-interview-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    // protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        $targetTime = $now->copy()->addMinutes(60);
        
        $interviews = InterviewSchedule::whereBetween('schedule_date', [$now, $targetTime])->get();

        
        if($interviews){

            foreach ($interviews as $interview) {

                $user = User::withoutGlobalScopes()->find(3);
                    if ($user) {
                        $user->notify(new InterviewReminderNotification($interview));
                        $this->info("✅ Notification sent to: {$user->name}");
                    }
            
            }

            $this->info('Interview reminders sent at ' . now());

        }


    }
}
