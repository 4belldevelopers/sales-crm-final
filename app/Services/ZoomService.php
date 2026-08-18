<?php

// app/Services/ZoomService.php

namespace App\Services;

use GuzzleHttp\Client;

class ZoomService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://api.zoom.us/v2/',
            'headers' => [
                'Authorization' => 'Bearer ' . $this->getAccessToken(),
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    protected function getAccessToken()
    {
        $client = new Client();


        $response = $client->post('https://zoom.us/oauth/token', [
            'form_params' => [
                'grant_type' => 'client_credentials',
                'client_id' => config('services.zoom.client_id', env('ZOOM_API_KEY')),
                'client_secret' => config('services.zoom.client_secret', env('ZOOM_API_SECRET')),
            ],
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        // dd($data);

        return $data['access_token'];
    }

    public function createMeeting($data)
    {
        // dd($data);
        $response = $this->client->post('users/me/meetings', [
            'json' => $data,
        ]);
        // dd($response);
        return json_decode($response->getBody()->getContents(), true);
    }
}
