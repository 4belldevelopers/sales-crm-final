<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Helper\Reply;
use Illuminate\Support\Facades\Cookie;

class ZoomController extends Controller
{
    // Renamed from 'authorize' to 'redirectToZoom'

    private function getZoomAccessToken()
    {
        $clientId = 'ma_AqFVMQLyelwJ2hhaKlg';
        $clientSecret = 'FBrziLAFmSk9AFqx7Rdsg5nDsmVBnihT';
        $accountId = '5PdHLPvqR0SY77pwiruTnA';

        $response = Http::asForm()
            ->withBasicAuth($clientId, $clientSecret)
            ->post('https://zoom.us/oauth/token', [
                'grant_type' => 'account_credentials',
                'account_id' => $accountId,
            ]);

        if ($response->successful()) {
            return $response->json()['access_token'];
        }

        throw new \Exception('Could not retrieve Zoom access token: ' . $response->body());
    }

    public function createZoomMeeting()
    {
        try {
            $accessToken = $this->getZoomAccessToken();

            $response = Http::withToken($accessToken)
                ->post('https://api.zoom.us/v2/users/me/meetings', [
                    'topic' => 'Laravel 7 Zoom Meeting',
                    'type' => 2,
                    'start_time' => now()->addMinutes(15)->toIso8601String(),
                    'duration' => 60,
                    'timezone' => 'Asia/Kolkata',
                    'agenda' => 'Test Zoom meeting using Server-to-Server OAuth',
                    'settings' => [
                        'host_video' => true,
                        'participant_video' => true,
                        'join_before_host' => true,
                        'mute_upon_entry' => true,
                        'approval_type' => 1,
                    ]
                ]);

            return response()->json($response->json());
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function redirectToZoom()
    {
        $zoomsettings = DB::table('zoomsettings')->select('zoomsettings.*')->first();
        $clientId = $zoomsettings->client_id;
        $redirect_uri = $zoomsettings->redirect_uri;
        $authorizeUrl = "https://zoom.us/oauth/authorize?response_type=code&client_id=" . $clientId . "&redirect_uri=" . urlencode($redirect_uri);
        return Reply::dataOnly(['status' => 'expired', 'authUrl' =>  $authorizeUrl, 'eventId'=>'']);
        return redirect($authorizeUrl);
    }

    public function callback(Request $request)
    {
        // dd("dhjd");
        $zoomsettings = DB::table('zoomsettings')->select('zoomsettings.*')->first();
        $clientId = $zoomsettings->client_id;
        $clientSecret = $zoomsettings->client_secret;
        $redirect_uri = $zoomsettings->redirect_uri;

        $code = $request->input('code');

        $response = Http::asForm()->post('https://zoom.us/oauth/token', [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => $redirect_uri,
            'client_id' =>$clientId,
            'client_secret' =>$clientSecret,
        ]);
        
        // dd($newAccessToken,$newRefreshToken);
        if ($response->successful()) {
        $minutes = 15 * 365 * 24 * 60; // 15 years in minutes
        $accessToken = $response->json()['access_token'];
        $newRefreshToken = $response->json()['refresh_token'];
        DB::table('zoomsettings')->update(['zoom_access_token'=>$newRefreshToken]);
        Cookie::queue(Cookie::make('zoom_access_token', $accessToken, $minutes, '/', null, true, true, false, 'Lax'));
        // session(['zoom_access_token' => $accessToken]);
           return view('access_token',['status'=>true]);
        }else{
           return view('access_token',['status'=>false]);  
        }

    }

    public function createMeeting(Request $request)
    {
        $accessToken = Cookie::get('zoom_access_token'); //session('zoom_access_token');
        // $meetingResponse  = Http::withToken($accessToken)->get('https://api.zoom.us/v2/users/me');
        // dd($meetingResponse->successful());

        $response = Http::withToken($accessToken)->post('https://api.zoom.us/v2/users/me/meetings', [
            'topic'      => $request->input('topic'),
            'type'       => 2, // Scheduled meeting
            'start_time' => '2024-08-30T15:00:00Z', // Static start time
            'duration'   => 60, // Static duration
            'timezone'   => 'UTC',
            'settings'   => [
                'join_before_host' => true, // Allow participants to join before the host
            ],
        ]);
        
        // Log the entire response for debugging
        // Log::error('Zoom API response', [
        //     'status' => $response->status(),
        //     'body'   => $response->body()
        // ]);
        
        if ($response->successful()) {
            $meetingData = $response->json();
            $joinUrl = $meetingData['join_url']; // Get the meeting join URL
            dd("1",$joinUrl, $response->status(),$response->body());
            return response()->json(['join_url' => $joinUrl]);
        } else {
            // Handle errors with more detail
            $errorData = $response->json();
            dd("2",$errorData,$response->status());
            return response()->json(['error' => $errorData], $response->status());
        }
        
    }
}
