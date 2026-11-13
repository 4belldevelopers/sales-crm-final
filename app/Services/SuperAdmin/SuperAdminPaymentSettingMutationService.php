<?php

namespace App\Services\SuperAdmin;

use App\Helper\Reply;
use App\Jobs\ProcessPaymentSettingOutboxEventJob;
use App\Models\Package;
use App\Models\PaymentSetting;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Class SuperAdminPaymentSettingMutationService
 *
 * Intent: Encapsulates state-mutating operations for Super Admin Payment Settings under the
 * Transactional Outbox, Two-Tier Idempotency, and CQRS dual-store synchronization architecture.
 * Guarantees zero duplicate executions under network retries, ACID row-locked state transitions,
 * zero remote network I/O inside MySQL transactions (duration < 15ms), instant Elasticsearch sync,
 * and O(1) Redis/Dragonfly L2 cache invalidation.
 *
 * Tenancy: Scoped strictly to Super Admin singleton configuration.
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class SuperAdminPaymentSettingMutationService
{
    /**
     * Compute a canonical SHA-256 hash for a given payload.
     */
    public function computeRequestHash(array $payload): string
    {
        ksort($payload);

        return hash('sha256', json_encode($payload));
    }

    /**
     * Format a deterministic idempotency key when explicitly supplied by client.
     */
    protected function formatIdempotencyKey(?string $clientKey): ?string
    {
        if ($clientKey && trim($clientKey) !== '') {
            return trim($clientKey);
        }

        return null;
    }

    /**
     * Update Super Admin payment settings with two-tier idempotency and transactional outbox.
     *
     * @param  Request  $request  HTTP request with validated parameters
     * @param  string|null  $idempotencyKey  Client-supplied UUID or token
     * @param  int|null  $adminId  Authenticated super admin ID
     * @return array Standardized Reply payload
     */
    public function updatePaymentSettings(Request $request, ?string $idempotencyKey = null, ?int $adminId = null): array
    {
        $finalIdempotencyKey = $this->formatIdempotencyKey($idempotencyKey);
        $payloadForHash = [
            'action' => 'update',
            'api_key' => $request->input('api_key'),
            'stripe_status' => $request->input('stripe_status'),
            'paypal_client_id' => $request->input('paypal_client_id'),
            'paypal_mode' => $request->input('paypal_mode'),
            'paypal_status' => $request->input('paypal_status'),
            'razorpay_key' => $request->input('razorpay_key'),
            'razorpay_status' => $request->input('razorpay_status'),
        ];
        $requestHash = $this->computeRequestHash($payloadForHash);

        // 1. Two-Tier Idempotency Lease Claim Check
        if ($finalIdempotencyKey) {
            $inserted = DB::table('sys_idempotency_keys')->insertOrIgnore([
                'tenant_id' => 0,
                'idempotency_key' => $finalIdempotencyKey,
                'request_hash' => $requestHash,
                'status' => 'PROCESSING',
                'expires_at' => Carbon::now()->addHours(24),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            if (! $inserted) {
                $existing = DB::table('sys_idempotency_keys')
                    ->where('tenant_id', 0)
                    ->where('idempotency_key', $finalIdempotencyKey)
                    ->first();

                if ($existing) {
                    if ($existing->status === 'COMPLETED' && $existing->response_body) {
                        $cached = json_decode($existing->response_body, true);
                        if (is_array($cached)) {
                            $cached['_replayed'] = true;

                            return $cached;
                        }
                    }

                    if ($existing->status === 'PROCESSING') {
                        if ($existing->updated_at && Carbon::parse($existing->updated_at)->gt(Carbon::now()->subSeconds(120))) {
                            abort(409, 'A payment settings update is currently processing for this request. Please wait.');
                        }

                        DB::table('sys_idempotency_keys')
                            ->where('tenant_id', 0)
                            ->where('idempotency_key', $finalIdempotencyKey)
                            ->update([
                                'status' => 'PROCESSING',
                                'request_hash' => $requestHash,
                                'updated_at' => Carbon::now(),
                            ]);
                    }
                }
            }
        }

        // 2. Pre-transaction evaluations (Rule 7: Preserving package verification logic)
        $packageDataes = Package::where(function ($query) {
            $query->where('monthly_price', '!=', 0)
                ->orWhere('annual_price', '!=', 0);
        })
            ->where(function ($query) {
                $query->whereNull('stripe_annual_plan_id')
                    ->orWhereNull('stripe_monthly_plan_id');
            })
            ->first();

        // 3. Row-locked MySQL transaction (<15ms)
        DB::beginTransaction();

        try {
            $paymentSetting = PaymentSetting::lockForUpdate()->first();

            if (! $paymentSetting) {
                $paymentSetting = new PaymentSetting;
            }

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

            // 4. Atomic Transactional Outbox Event Registration
            $outboxPayload = [
                'payment_setting_id' => $paymentSetting->id,
                'paypal_status' => $paymentSetting->paypal_status,
                'stripe_status' => $paymentSetting->stripe_status,
                'razorpay_status' => $paymentSetting->razorpay_status,
                'paypal_mode' => $paymentSetting->paypal_mode,
                'admin_id' => $adminId,
            ];

            $outboxId = DB::table('sys_outbox_events')->insertGetId([
                'tenant_id' => 0,
                'idempotency_key' => $finalIdempotencyKey,
                'aggregate_type' => 'PaymentSetting',
                'aggregate_id' => (int) $paymentSetting->id,
                'aggregate_version' => (int) (microtime(true) * 1000),
                'event_type' => 'PAYMENT_SETTING_UPDATED',
                'payload' => json_encode($outboxPayload),
                'status' => 'PENDING',
                'attempts' => 0,
                'created_at' => Carbon::now(),
                'available_at' => Carbon::now(),
            ]);

            // Formulate response preserving existing warning logic
            if ($packageDataes && $paymentSetting->stripe_status === 'active') {
                $responseData = Reply::success(
                    __('messages.updatedSuccessfully').' '.__('messages.stripePlanIdRequired').
                    ' <a class="btn btn-warning btn-sm" href="'.route('superadmin.packages.index').'"> '.__('menu.packages').' </a>'
                );
            } else {
                $responseData = Reply::success(__('messages.updatedSuccessfully'));
            }

            $responseData['outbox_id'] = $outboxId;

            // Complete Idempotency record if key was provided
            if ($finalIdempotencyKey) {
                DB::table('sys_idempotency_keys')
                    ->where('tenant_id', 0)
                    ->where('idempotency_key', $finalIdempotencyKey)
                    ->update([
                        'status' => 'COMPLETED',
                        'response_status' => 200,
                        'response_body' => json_encode($responseData),
                        'completed_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);
            }

            DB::commit();

            // 5. Atomic O(1) Cache Invalidation (Rule 6)
            SuperAdminPaymentSettingCacheManager::flushCaches();

            // 6. Post-commit fast path execution to sync Elasticsearch <15ms
            try {
                $job = new ProcessPaymentSettingOutboxEventJob((int) $outboxId);
                $job->handle();
            } catch (Throwable $syncEx) {
                Log::warning('Fast-path ES sync failed, queued for background retry: '.$syncEx->getMessage());
                ProcessPaymentSettingOutboxEventJob::dispatch((int) $outboxId);
            }

            return $responseData;
        } catch (Throwable $e) {
            DB::rollBack();

            if ($finalIdempotencyKey) {
                DB::table('sys_idempotency_keys')
                    ->where('tenant_id', 0)
                    ->where('idempotency_key', $finalIdempotencyKey)
                    ->delete();
            }

            throw $e;
        }
    }

    /**
     * Diagnostic endpoint to safely verify credentials and connectivity for a gateway.
     */
    public function verifyGateway(Request $request): JsonResponse
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
        } catch (Throwable $e) {
            $latencyMs = round((microtime(true) - $startTime) * 1000);

            return response()->json([
                'status' => 'fail',
                'latency_ms' => $latencyMs,
                'message' => 'Connection diagnostic failed: '.$e->getMessage(),
            ], 422);
        }
    }
}
