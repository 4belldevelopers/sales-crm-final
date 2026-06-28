<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\DB;

class LogSuccessfulLogin
{
    public function handle(Login $event)
    {
        $user = $event->user;

        // previous session ID stored?
        if ($user->session_id) {
            // destroy previous session
            DB::table('sessions')
                ->where('id', $user->session_id)
                ->delete();
        }

        // store new session ID
        $user->session_id = session()->getId();
        $user->save();
    }
}
