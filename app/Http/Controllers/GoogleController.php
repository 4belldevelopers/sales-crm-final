<?php

namespace App\Http\Controllers;

use Google\Client as GoogleClient;
use Google\Service\Calendar;
use Illuminate\Http\Request;
use App\Helper\Reply;
use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;

class GoogleController extends Controller
{
        public function createMeet()
    {
        $client = new Google_Client();
        $client->setAuthConfig(storage_path('google-service-account.json'));
        $client->addScope(Google_Service_Calendar::CALENDAR);
        $client->setSubject('your_calendar_owner_email@gmail.com'); // calendar owner email

        $service = new Google_Service_Calendar($client);

        $event = new Google_Service_Calendar_Event([
            'summary' => 'Server-side Google Meet',
            'start' => [
                'dateTime' => now()->addMinutes(5)->toIso8601String(),
                'timeZone' => 'Asia/Kolkata',
            ],
            'end' => [
                'dateTime' => now()->addMinutes(35)->toIso8601String(),
                'timeZone' => 'Asia/Kolkata',
            ],
            'conferenceData' => [
                'createRequest' => [
                    'requestId' => uniqid(),
                    'conferenceSolutionKey' => ['type' => 'hangoutsMeet'],
                ],
            ],
        ]);

        $createdEvent = $service->events->insert('primary', $event, ['conferenceDataVersion' => 1]);

        return response()->json([
            'meet_link' => $createdEvent->getHangoutLink(),
        ]);
    }
    
    public function redirectToGoogle()
    {
        $client = new GoogleClient();
        $client->setClientId(config('google.client_id'));
        $client->setClientSecret(config('google.client_secret'));
        $client->setRedirectUri(config('google.redirect_uri'));
        $client->addScope(Calendar::CALENDAR);

        $authUrl = $client->createAuthUrl();
        // dd( $authUrl);
        return Reply::dataOnly(['status' => 'expired', 'authUrl' =>  $authUrl,'eventId'=>'']);
        // return redirect($authUrl);
    }

    public function handleGoogleCallback(Request $request)
    {
        $client = new GoogleClient();
        $client->setClientId(config('google.client_id'));
        $client->setClientSecret(config('google.client_secret'));
        $client->setRedirectUri(config('google.redirect_uri'));
        $client->addScope(Calendar::CALENDAR);

        $token = $client->fetchAccessTokenWithAuthCode($request->input('code'));
        // Check if there's an error in the token response
        if (isset($token['error'])) {
            // Handle the error accordingly
            return redirect('/service_company/dashboard')->with('error_token', 'Failed to authenticate with Google: ' . $token['error_description']);
        }

        // dd($request->input('code'),$token);

        $client->setAccessToken($token);
        session(['google_access_token' => $token]);

        return redirect('/service_company/dashboard')->with('status_token', 'Authenticated successfully.');

        // $calendarService = new Calendar($client);

        // Now you can use $calendarService to create Google Meet meetings
    }

    public function createGoogleMeet()
{
    $client = new GoogleClient();
    $client->setAccessToken(session('google_access_token'));

    $calendarService = new Calendar($client);

    $event = new \Google\Service\Calendar\Event([
        'summary' => 'Google Meet Meeting',
        'start' => [
            'dateTime' => '2024-08-26T10:00:00-07:00',
            'timeZone' => 'America/Los_Angeles',
        ],
        'end' => [
            'dateTime' => '2024-08-26T11:00:00-07:00',
            'timeZone' => 'America/Los_Angeles',
        ],
        'conferenceData' => [
            'createRequest' => [
                'conferenceSolutionKey' => ['type' => 'hangoutsMeet'],
                'requestId' => 'some-random-string',
            ],
        ],
    ]);

    $event = $calendarService->events->insert('primary', $event, ['conferenceDataVersion' => 1]);

    return $event->getHangoutLink();
}


}
