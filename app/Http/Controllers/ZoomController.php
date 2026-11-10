<?php

namespace App\Http\Controllers;

use App\Helper\Reply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ZoomController extends Controller
{
    // Renamed from 'authorize' to 'redirectToZoom'

    private function getZoomAccessToken()
    {
        return app(\App\Services\Zoom\ZoomTokenManager::class)->getAccessToken();
    }

    public function createZoomMeeting()
    {
        try {
            $zoomMeetingService = app(\App\Services\Zoom\ZoomMeetingService::class);
            $result = $zoomMeetingService->createInterviewMeeting([
                'topic' => 'Laravel 7 Zoom Meeting',
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
                ],
            ]);

            if ($result['status'] === 'success') {
                return response()->json($result['raw'] ?? ['join_url' => $result['join_url']]);
            }

            return response()->json(['error' => $result['error'] ?? 'Failed to create Zoom meeting'], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function redirectToZoom()
    {
        $configService = app(\App\Services\Zoom\ZoomConfigurationService::class);
        $config = $configService->getConfiguration();
        $clientId = $config['client_id'];
        $redirect_uri = $config['account_id'];
        $authorizeUrl = 'https://zoom.us/oauth/authorize?response_type=code&client_id='.$clientId.'&redirect_uri='.urlencode($redirect_uri);

        return Reply::dataOnly(['status' => 'expired', 'authUrl' => $authorizeUrl, 'eventId' => '']);
    }

    public function callback(Request $request)
    {
        $configService = app(\App\Services\Zoom\ZoomConfigurationService::class);
        $config = $configService->getConfiguration();
        $clientId = $config['client_id'];
        $clientSecret = $config['client_secret'];
        $redirect_uri = $config['account_id'];

        $code = $request->input('code');

        $response = Http::asForm()->post('https://zoom.us/oauth/token', [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => $redirect_uri,
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
        ]);

        if ($response->successful()) {
            $minutes = 15 * 365 * 24 * 60; // 15 years in minutes
            $accessToken = $response->json()['access_token'];
            $newRefreshToken = $response->json()['refresh_token'];
            DB::table('zoomsettings')->where('id', 1)->update(['zoom_access_token' => $newRefreshToken]);
            Cookie::queue(Cookie::make('zoom_access_token', $accessToken, $minutes, '/', null, true, true, false, 'Lax'));

            return view('access_token', ['status' => true]);
        } else {
            return view('access_token', ['status' => false]);
        }
    }

    public function createMeeting(Request $request)
    {
        try {
            $topic = $request->input('topic', 'Interview Meeting');
            $zoomMeetingService = app(\App\Services\Zoom\ZoomMeetingService::class);

            $result = $zoomMeetingService->createInterviewMeeting([
                'topic' => $topic,
                'start_time' => '2024-08-30T15:00:00Z',
                'duration' => 60,
                'timezone' => 'UTC',
                'settings' => [
                    'join_before_host' => true,
                ],
            ]);

            if ($result['status'] === 'success') {
                return response()->json(['join_url' => $result['join_url']]);
            }

            return response()->json(['error' => $result['error'] ?? 'Meeting creation failed'], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
