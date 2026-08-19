<?php

namespace App\Http\Controllers;

use App\Mail\MeetingMail;
use App\Services\CalendarService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class MeetingController extends Controller
{
    public function send(CalendarService $calendar)
    {

        $title = 'Interview with Candidate';
        $description = 'Please join using this link: https://meet.google.com/xyz-123';
        $location = 'Online Meeting';
        $start = Carbon::now()->addDay();
        $end = Carbon::now()->addDay()->addHour();

        $googleLink = $calendar->googleLink($title, $description, $location, $start, $end);
        $outlookLink = $calendar->outlookLink($title, $description, $location, $start, $end);
        Mail::to('4belldevelopers@gmail.com')->send(new MeetingMail($googleLink, $outlookLink));

        return 'Meeting email sent!';
    }
}
