<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Helper\Reply;
use App\Http\Requests\SuperAdmin\PaymentSetting\UpdateRequest;
use App\Models\Package;
use App\Models\PaymentSetting;
use App\Traits\PaymentSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SuperAdminPaymentSettingsController extends SuperAdminBaseController
{
    use PaymentSettings;

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.paymentGatewayCredential';
        $this->pageIcon = 'icon-settings';
    }

    public function index()
    {
        // Safe entity instantiation: guarantee non-null record
        $this->credentials = PaymentSetting::firstOrNew();

        // Gateway Metrics for Tier 1 Toolbar & Tier 2 Sub-Tabs
        $this->totalGateways = 3;
        $this->activeGatewaysCount = 0;
        if ($this->credentials->paypal_status === 'active') {
            $this->activeGatewaysCount++;
        }
        if ($this->credentials->stripe_status === 'active') {
            $this->activeGatewaysCount++;
        }
        if ($this->credentials->razorpay_status === 'active') {
            $this->activeGatewaysCount++;
        }
        $this->inactiveGatewaysCount = $this->totalGateways - $this->activeGatewaysCount;

        // Authoritative Webhook endpoints
        $this->paypalWebhookUrl = route('verify-billing-ipn');
        $this->stripeWebhookUrl = route('save_webhook');
        $this->razorpayWebhookUrl = route('save_razorpay-webhook');

        return view('super-admin.payment-setting.index', $this->data);
    }

    public function update(UpdateRequest $request, $id = null)
    {
        DB::beginTransaction();
        try {
            $paymentSetting = PaymentSetting::firstOrNew();

            // Save Stripe Credentials (preserves existing secret if left empty)
            if ($request->has('api_key')) {
                $paymentSetting->api_key = $request->api_key;
            }
            if ($request->filled('api_secret') || empty($paymentSetting->api_secret)) {
                $paymentSetting->api_secret = $request->api_secret;
            }
            if ($request->filled('webhook_key') || empty($paymentSetting->webhook_key)) {
                $paymentSetting->webhook_key = $request->webhook_key;
            }

            // Save Paypal Credentials (preserves existing secret if left empty)
            if ($request->has('paypal_client_id')) {
                $paymentSetting->paypal_client_id = $request->paypal_client_id;
            }
            if ($request->filled('paypal_secret') || empty($paymentSetting->paypal_secret)) {
                $paymentSetting->paypal_secret = $request->paypal_secret;
            }
            if ($request->has('paypal_mode')) {
                $paymentSetting->paypal_mode = in_array($request->paypal_mode, ['sandbox', 'live'], true) ? $request->paypal_mode : 'sandbox';
            }

            // Save Active Statuses strictly according to MySQL enum definitions
            $isPaypalActive = $request->has('paypal_status') && in_array($request->paypal_status, ['on', 'active', '1', true], true);
            $paymentSetting->paypal_status = $isPaypalActive ? 'active' : 'inactive';

            $isStripeActive = $request->has('stripe_status') && in_array($request->stripe_status, ['on', 'active', '1', true], true);
            $paymentSetting->stripe_status = $isStripeActive ? 'active' : 'inactive';

            // Note: Schema column razorpay_status is enum('active','deactive')
            $isRazorpayActive = $request->has('razorpay_status') && in_array($request->razorpay_status, ['on', 'active', '1', true], true);
            $paymentSetting->razorpay_status = $isRazorpayActive ? 'active' : 'deactive';

            // Save Razorpay Credentials (preserves existing secret if left empty)
            if ($request->has('razorpay_key')) {
                $paymentSetting->razorpay_key = $request->razorpay_key;
            }
            if ($request->filled('razorpay_secret') || empty($paymentSetting->razorpay_secret)) {
                $paymentSetting->razorpay_secret = $request->razorpay_secret;
            }
            if ($request->filled('razorpay_webhook_secret') || empty($paymentSetting->razorpay_webhook_secret)) {
                $paymentSetting->razorpay_webhook_secret = $request->razorpay_webhook_secret;
            }

            $paymentSetting->save();
            DB::commit();

            // Rule #6: Atomic Cache Invalidation
            Cache::forget('superadmin_payment_setting');

            // Rule #7: Preserve existing package check & warning logic
            $packageDataes = Package::where(function ($query) {
                $query->where('monthly_price', '!=', 0)
                    ->orWhere('annual_price', '!=', 0);
            })
                ->where(function ($query) {
                    $query->whereNull('stripe_annual_plan_id')
                        ->orWhereNull('stripe_monthly_plan_id');
                })
                ->first();

            if ($packageDataes && $paymentSetting->stripe_status === 'active') {
                return Reply::success(
                    __('messages.updatedSuccessfully').' '.__('messages.stripePlanIdRequired').
                    ' <a class="btn btn-warning btn-sm" href="'.route('superadmin.packages.index').'"> '.__('menu.packages').' </a>'
                );
            }

            return Reply::success(__('messages.updatedSuccessfully'));
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('SuperAdmin PaymentSettings Update Error: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return Reply::error('Failed to update payment settings: '.$e->getMessage());
        }
    }

    /**
     * Diagnostic endpoint to safely verify credentials and connectivity for a gateway.
     */
    public function verifyGateway(Request $request)
    {
        $gateway = $request->input('gateway');
        $startTime = microtime(true);

        try {
            switch ($gateway) {
                case 'stripe':
                    $secret = $request->input('api_secret') ?: ($request->input('secret'));
                    if (empty($secret)) {
                        return response()->json([
                            'status' => 'fail',
                            'message' => 'Please provide a Stripe Secret Key to test.',
                        ], 422);
                    }

                    if (! class_exists(\Stripe\Stripe::class)) {
                        return response()->json([
                            'status' => 'fail',
                            'message' => 'Stripe PHP SDK is not installed.',
                        ], 500);
                    }

                    \Stripe\Stripe::setMaxNetworkRetries(0);
                    \Stripe\Stripe::setApiKey($secret);
                    \Stripe\Balance::retrieve();

                    $latencyMs = round((microtime(true) - $startTime) * 1000);
                    $isTestMode = str_starts_with($secret, 'sk_test_') || str_starts_with($secret, 'rk_test_');

                    return response()->json([
                        'status' => 'success',
                        'latency_ms' => $latencyMs,
                        'environment' => $isTestMode ? 'Test Mode' : 'Live Production',
                        'message' => 'Stripe connection authenticated successfully! ('.$latencyMs.'ms latency)',
                    ]);

                case 'razorpay':
                    $key = $request->input('razorpay_key');
                    $secret = $request->input('razorpay_secret');

                    if (empty($key) || empty($secret)) {
                        return response()->json([
                            'status' => 'fail',
                            'message' => 'Please provide both Razorpay Key ID and Secret Key to test.',
                        ], 422);
                    }

                    // Test credentials against Razorpay items endpoint
                    $response = Http::withBasicAuth($key, $secret)
                        ->timeout(6)
                        ->get('https://api.razorpay.com/v1/items?count=1');

                    $latencyMs = round((microtime(true) - $startTime) * 1000);

                    if ($response->successful()) {
                        $isTest = str_starts_with($key, 'rzp_test_');

                        return response()->json([
                            'status' => 'success',
                            'latency_ms' => $latencyMs,
                            'environment' => $isTest ? 'Test Mode' : 'Live Production',
                            'message' => 'Razorpay API credentials authenticated successfully! ('.$latencyMs.'ms latency)',
                        ]);
                    }

                    if ($response->status() === 401) {
                        return response()->json([
                            'status' => 'fail',
                            'message' => 'Razorpay authentication failed: Invalid Key ID or Key Secret.',
                        ], 422);
                    }

                    return response()->json([
                        'status' => 'fail',
                        'message' => 'Razorpay error: '.($response->json('error.description') ?? 'HTTP '.$response->status()),
                    ], 422);

                case 'paypal':
                    $clientId = $request->input('paypal_client_id');
                    $secret = $request->input('paypal_secret');
                    $mode = $request->input('paypal_mode') ?: 'sandbox';

                    if (empty($clientId) || empty($secret)) {
                        return response()->json([
                            'status' => 'fail',
                            'message' => 'Please provide both PayPal Client ID and Secret to test.',
                        ], 422);
                    }

                    $baseUrl = ($mode === 'live')
                        ? 'https://api-m.paypal.com'
                        : 'https://api-m.sandbox.paypal.com';

                    $response = Http::asForm()
                        ->withBasicAuth($clientId, $secret)
                        ->timeout(7)
                        ->post($baseUrl.'/v1/oauth2/token', [
                            'grant_type' => 'client_credentials',
                        ]);

                    $latencyMs = round((microtime(true) - $startTime) * 1000);

                    if ($response->successful() && $response->json('access_token')) {
                        return response()->json([
                            'status' => 'success',
                            'latency_ms' => $latencyMs,
                            'environment' => ucfirst($mode),
                            'message' => 'PayPal OAuth token generated successfully in '.ucfirst($mode).' mode! ('.$latencyMs.'ms latency)',
                        ]);
                    }

                    return response()->json([
                        'status' => 'fail',
                        'message' => 'PayPal authentication failed. Check your Client ID, Secret, and Mode ('.ucfirst($mode).'). Error: '.($response->json('error_description') ?? 'Authentication rejected'),
                    ], 422);

                default:
                    return response()->json([
                        'status' => 'fail',
                        'message' => 'Unknown payment gateway specified.',
                    ], 400);
            }
        } catch (\Throwable $e) {
            $latencyMs = round((microtime(true) - $startTime) * 1000);

            return response()->json([
                'status' => 'fail',
                'latency_ms' => $latencyMs,
                'message' => 'Connection diagnostic failed: '.$e->getMessage(),
            ], 422);
        }
    }
}
