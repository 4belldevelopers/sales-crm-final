<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Order;

class PhonePeService
{
    protected $merchantId;
    protected $apiKey;
    protected $keyIndex;
    protected $hostUrl;

    public function __construct()
    {
        $this->merchantId = config('services.phonepe.merchant_id');
        $this->apiKey = config('services.phonepe.api_key');
        $this->keyIndex = config('services.phonepe.api_key_index');
        $this->hostUrl = config('services.phonepe.host_url');
    }

    public function createOrder($amount)
    {
        // Generate a unique transaction ID
        $transactionId = uniqid('ORD_');

        // Create a new order
        $order = Order::create([
            'transaction_id' => $transactionId,
            'amount' => $amount,
        ]);

        return $order;
    }

    public function initiatePayment(Order $order)
    {
        $endpoint = $this->hostUrl . '/pg/v1/pay';

        $payload = [
            'merchantId' => $this->merchantId,
            'transactionId' => $order->transaction_id,
            'amount' => $order->amount * 100,  // amount in paise
            'merchantUserId' => 'USER_ID',
            'callbackUrl' => route('phonepe.callback'),
        ];

        $encryptedPayload = $this->encryptPayload(json_encode($payload));

        $response = Http::withHeaders([
            'X-VERIFY' => $this->generateVerificationHash($encryptedPayload),
            'Content-Type' => 'application/json',
        ])->post($endpoint, [
            'request' => $encryptedPayload,
        ]);

        return $response->json();
    }

    private function encryptPayload($payload)
    {
        // Add your encryption logic based on PhonePe's requirement
        return $payload;
    }

    private function generateVerificationHash($payload)
    {
        $checksum = hash_hmac('sha256', $payload, $this->apiKey);
        return $checksum . "###" . $this->keyIndex;
    }
}
