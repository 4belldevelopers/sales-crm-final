<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Helper\Reply;
use App\Models\LinkedInSetting;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Throwable;

/**
 * Class LinkedInSettingController
 *
 * SuperAdmin controller managing global LinkedIn OAuth 2.0 (OpenID Connect) credentials.
 * Authoritative integration point configuring client_id, client_secret, authorized callback URL,
 * and global activation status consumed by:
 * - FrontJobsController (public careers / candidate "Apply with LinkedIn")
 * - Admin & ServiceCompany LinkedInSettingsControllers (tenant activation gates)
 * - BaseControllers across all organizational contexts
 *
 * Architecture & Tenancy:
 * - Singleton pattern: linked_in_settings table is always accessed as an authoritative singleton.
 * - Tenancy cache invalidation: clearing Redis/file cache on write guarantees zero stale data.
 * - Dynamic SSL / Reverse proxy awareness: prevents false lockouts on localhost and reverse proxies.
 */
class LinkedInSettingController extends SuperAdminBaseController
{
    /**
     * Cache key for global LinkedIn setting.
     */
    public const CACHE_KEY = 'linkedin_global_setting';

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'menu.linkedInSettings';
        $this->pageIcon = 'icon-settings';
    }

    /**
     * Display the LinkedIn Integration & OAuth Management Hub.
     *
     * @return Factory|View
     */
    public function index()
    {
        // Singleton guarantee: ensure non-null record exists
        $this->linkedInSetting = LinkedInSetting::firstOrCreate([], [
            'status' => 'disable',
            'client_id' => '',
            'client_secret' => '',
            'callback_url' => '',
        ]);

        // Dynamic, authoritative callback URL
        $callbackUrl = route('jobs.linkedinCallback', 'linkedin');
        $this->linkedInSetting->callback_url = $callbackUrl;

        // Intelligent SSL and environment detection
        $isLocal = app()->environment('local') || in_array(request()->getHost(), ['localhost', '127.0.0.1'], true);
        $isHttps = request()->isSecure()
            || request()->header('x-forwarded-proto') === 'https'
            || Str::startsWith($callbackUrl, 'https://');

        // Legacy compatibility flag
        $this->httpsContains = $isHttps || $isLocal;

        // Structured diagnostic payload for Tier 1 & Tier 2 UI cards
        $this->sslData = [
            'is_secure' => $isHttps,
            'is_local' => $isLocal,
            'scheme' => $isHttps ? 'https' : 'http',
            'host' => request()->getHost(),
            'callback_url' => $callbackUrl,
            'status' => $isHttps ? 'verified' : ($isLocal ? 'local_dev' : 'advisory'),
            'label' => $isHttps ? 'HTTPS Enforced & Verified' : ($isLocal ? 'Local Development (HTTP Allowed)' : 'SSL Certificate Advisory'),
            'badge_class' => $isHttps ? 'badge-light-success text-success' : ($isLocal ? 'badge-light-info text-info' : 'badge-light-warning text-warning'),
        ];

        return view('super-admin.linked-in-settings.edit', $this->data);
    }

    /**
     * Update global LinkedIn credentials and status.
     *
     * @param  int  $id
     * @return array|JsonResponse
     */
    public function update(Request $request, $id)
    {
        $setting = LinkedInSetting::withoutGlobalScopes()->findOrFail($id);

        $request->validate([
            'client_id' => 'nullable|string|max:191',
            'client_secret' => 'nullable|string|max:191',
            'status' => 'nullable|string|in:enable,disable,on,off,1,0',
        ]);

        // Parse requested status
        if ($request->has('status')) {
            $newStatus = in_array($request->input('status'), ['enable', 'on', '1', true], true) ? 'enable' : 'disable';
        } else {
            $newStatus = 'disable';
        }

        $clientId = trim((string) $request->input('client_id', $setting->client_id));
        $submittedSecret = trim((string) $request->input('client_secret', ''));

        // Credential preservation: if blank or masked with bullets, preserve existing secret
        $clientSecret = ($submittedSecret === '' || preg_match('/^[•*]+$/u', $submittedSecret))
            ? (string) $setting->client_secret
            : $submittedSecret;

        // Enforce business integrity: do not allow enabling with empty keys
        if ($newStatus === 'enable' && (empty($clientId) || empty($clientSecret))) {
            return Reply::error(__('Please provide both Client ID and Client Secret before enabling LinkedIn integration.'));
        }

        $setting->client_id = $clientId;
        $setting->client_secret = $clientSecret;
        $setting->callback_url = route('jobs.linkedinCallback', 'linkedin');
        $setting->status = $newStatus;
        $setting->save();

        // Multi-tenant cache eviction (Rule #6)
        Cache::forget(self::CACHE_KEY);
        Cache::forget('linkedin_setting');

        if ($request->ajax()) {
            return Reply::success(__('menu.settings').' '.__('messages.updatedSuccessfully'), [
                'status' => $setting->status,
                'is_enabled' => $setting->status === 'enable',
            ]);
        }

        return Reply::redirect(route('superadmin.linkedin-settings.index'), __('menu.settings').' '.__('messages.updatedSuccessfully'));
    }

    /**
     * Toggle or update the activation status of LinkedIn integration.
     *
     * @param  int  $id
     * @return array|JsonResponse
     */
    public function updateStatus(Request $request, $id)
    {
        $setting = LinkedInSetting::withoutGlobalScopes()->findOrFail($id);

        // Determine requested target status
        if ($request->has('status')) {
            $targetStatus = in_array($request->input('status'), ['enable', 'on', '1', true], true) ? 'enable' : 'disable';
        } else {
            // Toggle
            $targetStatus = ($setting->status === 'enable') ? 'disable' : 'enable';
        }

        // Validate credentials if activating
        if ($targetStatus === 'enable' && (empty($setting->client_id) || empty($setting->client_secret))) {
            return Reply::error(__('Please configure Client ID and Client Secret before enabling LinkedIn integration.'));
        }

        $setting->status = $targetStatus;
        $setting->save();

        // Multi-tenant cache eviction (Rule #6)
        Cache::forget(self::CACHE_KEY);
        Cache::forget('linkedin_setting');

        if ($request->ajax()) {
            return Reply::success(__('menu.settings').' '.__('messages.updatedSuccessfully'), [
                'status' => $setting->status,
                'is_enabled' => $setting->status === 'enable',
            ]);
        }

        return Reply::redirect(route('superadmin.linkedin-settings.index'), __('menu.settings').' '.__('messages.updatedSuccessfully'));
    }

    /**
     * Test handshake and diagnostic reachability to LinkedIn OAuth API.
     *
     * @return array|JsonResponse
     */
    public function testConnection(Request $request)
    {
        $setting = LinkedInSetting::firstOrCreate([]);

        $clientId = trim((string) $request->input('client_id', $setting->client_id));
        $submittedSecret = trim((string) $request->input('client_secret', ''));
        $clientSecret = ($submittedSecret === '' || preg_match('/^[•*]+$/u', $submittedSecret))
            ? (string) $setting->client_secret
            : $submittedSecret;

        if (empty($clientId) || empty($clientSecret)) {
            return Reply::error(__('Client ID and Client Secret are required to run the connection diagnostic.'));
        }

        $startTime = microtime(true);

        try {
            // Ping LinkedIn OAuth token endpoint with client credentials
            $response = Http::timeout(8)
                ->asForm()
                ->post('https://www.linkedin.com/oauth/v2/accessToken', [
                    'grant_type' => 'client_credentials',
                    'client_id' => $clientId,
                    'client_secret' => $clientSecret,
                ]);

            $latencyMs = (int) round((microtime(true) - $startTime) * 1000);
            $statusCode = $response->status();
            $body = $response->json() ?? [];

            // 200 OK: Successful token exchange
            if ($statusCode === 200) {
                return Reply::success(__('LinkedIn API handshake successful! Credentials verified.'), [
                    'latency_ms' => $latencyMs,
                    'status_code' => $statusCode,
                    'reachability' => 'connected',
                    'details' => __('Active OAuth handshake succeeded.'),
                ]);
            }

            // 400/401 with OAuth error: LinkedIn is reachable, but keys/scopes rejected
            if (isset($body['error'])) {
                $errorDesc = $body['error_description'] ?? $body['error'];

                // Special note: OpenID apps often use authorization_code flow rather than client_credentials
                if ($body['error'] === 'unauthorized_client' || $body['error'] === 'invalid_client') {
                    return Reply::error(__('LinkedIn reached in '.$latencyMs.'ms, but rejected the credentials: ').$errorDesc, [
                        'latency_ms' => $latencyMs,
                        'status_code' => $statusCode,
                        'reachability' => 'reachable_rejected',
                        'details' => $errorDesc,
                    ]);
                }

                return Reply::success(__('LinkedIn OAuth endpoint reached in '.$latencyMs.'ms (Status: '.$statusCode.').'), [
                    'latency_ms' => $latencyMs,
                    'status_code' => $statusCode,
                    'reachability' => 'endpoint_verified',
                    'details' => $errorDesc,
                ]);
            }

            return Reply::success(__('LinkedIn OAuth endpoint reached in '.$latencyMs.'ms (Status: '.$statusCode.').'), [
                'latency_ms' => $latencyMs,
                'status_code' => $statusCode,
                'reachability' => 'endpoint_verified',
                'details' => __('HTTP '.$statusCode.' received from LinkedIn OAuth server.'),
            ]);
        } catch (Throwable $e) {
            $latencyMs = (int) round((microtime(true) - $startTime) * 1000);

            return Reply::error(__('Unable to connect to LinkedIn OAuth services: ').$e->getMessage(), [
                'latency_ms' => $latencyMs,
                'reachability' => 'unreachable',
            ]);
        }
    }
}
