<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PhonePePaymentController extends Controller
{
    public function createPayment(Request $request)
    {
        $merchantId = env('PHONEPE_MERCHANT_ID');
        $apiKey = env('PHONEPE_API_KEY');
        $keyIndex = env('PHONEPE_API_KEY_INDEX');
        $hostUrl = env('PHONEPE_HOST_URL');

        // Payment request data
        $amount = $request->input('amount') * 100; // PhonePe expects amount in paise (1 INR = 100 paise)
        $transactionId = 'TXN_' . time();
        $callbackUrl = route('phonepe.callback');

        // Payment Payload
        $data = [
            'merchantId' => $merchantId,
            'transactionId' => $transactionId,
            'amount' => $amount,
            'callbackUrl' => $callbackUrl,
            'merchantOrderId' => 'ORDER_' . time(),
            'paymentInstrument' => ['type' => 'UPI'],
        ];

        dd($data);

        // Convert to JSON and base64 encode
        $dataJson = json_encode($data);
        $payload = base64_encode($dataJson);

        // Generate checksum using HMAC SHA256
        $checksum = hash_hmac('sha256', "/pg/v1/pay/$payload", $apiKey);

        // Create headers
        $headers = [
            'Content-Type' => 'application/json',
            'X-VERIFY' => $checksum . '###' . $keyIndex,
        ];

        // Send request to PhonePe
        $response = Http::withHeaders($headers)->post($hostUrl . '/pg/v1/pay', [
            'request' => $payload,
        ]);
        // dd($response);
        // Handle the response
        if ($response->successful()) {
            // Assuming you redirect the user to the payment page
            return redirect()->to($response['data']['redirectUrl']);
        }

        // Handle errors
        return response()->json([
            'status' => 'error',
            'message' => 'Payment creation failed',
        ], 500);
    }

    // Callback to handle the PhonePe response
    public function callback(Request $request)
    {
        // Handle the payment status here
        return response()->json($request->all());
    }
}

