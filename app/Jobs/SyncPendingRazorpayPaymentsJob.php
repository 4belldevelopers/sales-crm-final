<?php

/**
 * Background queue job to sync pending Razorpay order payment statuses for a company.
 *
 * Decouples outbound cURL HTTP calls to api.razorpay.com from the web request cycle
 * in ManageSubscriptionController@loadTransactionHistory (Subscription Listing V2 Phase 2).
 *
 * Tenancy: Filtered by company_id parameter (Rule #3).
 */

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyncPendingRazorpayPaymentsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $companyId;

    public int $tries = 3;

    public int $backoff = 30;

    public int $timeout = 60;

    /**
     * Create a new job instance.
     */
    public function __construct(int $companyId)
    {
        $this->companyId = $companyId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Tenancy: Filtered by company_id utilizing idx_company_gateway_payment index
        $transactionHistory = DB::table('cart_transaction_history')
            ->select(['id', 'company_id', 'payment_orderId', 'payment_Id', 'payment_gateway', 'cart_id'])
            ->where('company_id', $this->companyId)
            ->where('payment_gateway', 'razorpay')
            ->where(function ($query) {
                $query->whereNull('payment_Id')
                    ->orWhere('payment_Id', '=', '');
            })
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        if ($transactionHistory->isEmpty()) {
            return;
        }

        $paymentSettings = DB::table('payment_settings')->select('payment_settings.*')->first();
        if (! $paymentSettings || empty($paymentSettings->razorpay_key) || empty($paymentSettings->razorpay_secret)) {
            Log::warning('SyncPendingRazorpayPaymentsJob: Missing Razorpay credentials in payment_settings');

            return;
        }

        $key = $paymentSettings->razorpay_key;
        $secret = $paymentSettings->razorpay_secret;

        foreach ($transactionHistory as $trVal) {
            if (! empty($trVal->payment_orderId) && $trVal->payment_gateway === 'razorpay') {
                $orderID = $trVal->payment_orderId;

                try {
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, "https://api.razorpay.com/v1/orders/{$orderID}/payments");
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_USERPWD, "{$key}:{$secret}");
                    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

                    $response = curl_exec($ch);
                    if (! curl_errno($ch)) {
                        $payments = json_decode($response, true);
                        if (! empty($payments['items'])) {
                            foreach ($payments['items'] as $payment) {
                                if (isset($payment['status'])) {
                                    DB::table('cart_transaction_history')
                                        ->where('payment_orderId', $orderID)
                                        ->update(['status' => $payment['status']]);
                                }
                            }
                        }
                    } else {
                        Log::error('SyncPendingRazorpayPaymentsJob: cURL error', [
                            'order_id' => $orderID,
                            'error' => curl_error($ch),
                        ]);
                    }
                    curl_close($ch);
                } catch (\Throwable $e) {
                    Log::error('SyncPendingRazorpayPaymentsJob execution exception', [
                        'company_id' => $this->companyId,
                        'order_id' => $orderID,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }
    }
}
