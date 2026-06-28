<?php

namespace App\Services;

use Carbon\Carbon;

class CalendarService
{
    public function googleLink($title, $description, $location, Carbon $start, Carbon $end)
    {
        $params = [
            'action' => 'TEMPLATE',
            'text' => $title,
            'details' => $description,
            'location' => $location,
            'dates' => $start->format('Ymd\THis\Z') . '/' . $end->format('Ymd\THis\Z'),
        ];

        return 'https://calendar.google.com/calendar/render?' . http_build_query($params);
    }

    public function outlookLink($title, $description, $location, Carbon $start, Carbon $end)
    {
        $params = [
            'subject' => $title,
            'body' => $description,
            'startdt' => $start->toIso8601String(),
            'enddt'   => $end->toIso8601String(),
            'location' => $location,
        ];

        return 'https://outlook.live.com/calendar/0/deeplink/compose?' . http_build_query($params);
    }
}
