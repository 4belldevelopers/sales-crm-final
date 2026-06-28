<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function createPayment(Request $request)
    {

        //........................................
        $payment_settings = DB::table('payment_settings')->select('payment_settings.*')->first();
        $key = $payment_settings->razorpay_key;
        $secret = $payment_settings->razorpay_secret;

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://api.razorpay.com/v1/orders');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);

        // The parameters for creating a Razorpay order
        $postData = [
            'amount' => 5000 * 100, // ₹5000 in paisa (₹5000.00)
            'currency' => 'INR', // USD
            'receipt' => 'order_rcptid_11',
            'payment_capture' => 0 // Auto-capture payment (1 means capture automatically)
        ];

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));

        // Razorpay authentication
        curl_setopt($ch, CURLOPT_USERPWD, $key . ':' . $secret);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

        // Execute the cURL request and get the response
        $result = curl_exec($ch);

        if (curl_errno($ch)) {
            // Handle cURL errors
            return 'Error:' . curl_error($ch);
        }

        curl_close($ch);

        $order = json_decode($result, true);

        return response()->json([
            'success' => true,
            'orderId' => $order['id'],
            'amount' => $order['amount'],
            'name' => "",
            'email' => "",
            'currency' => $order['currency']
        ]);

        // dd($order);

        // return view('payment-page', ['orderId' => $order['id'],'currency' => $order['currency'],'amount' => $order['amount']]);

    }

    public function paymentCallback(Request $request)
    {
        
        $input = $request->all();

        // dd($input);

        $payment_settings = DB::table('payment_settings')->select('payment_settings.*')->first();

         $key = $payment_settings->razorpay_key;
         $secret = $payment_settings->razorpay_secret;

        /**
         * @var mixed
         * */
         
            $apiKey = $payment_settings->razorpay_key;
            $apiSecret = $payment_settings->razorpay_secret;
            $paymentId =  $input['razorpay_payment_id'];  // Replace with the actual payment ID
            $amount = $input['razorpay_amount']; //5000 * 100; // Amount in cents
            $update_coins = $input['razorpay_update_coins']; //5000 * 100; // Amount in cents
            $company_id = $input['razorpay_company_id']; //5000 * 100; // Amount in cents
            $topUptype = $input['razorpay_event_type']; //5000 * 100; // Amount in cents
            $razorpay_order_id = $input['razorpay_order_id']; //5000 * 100; // Amount in cents
            $razorpay_start_date = $input['razorpay_start_date']; //5000 * 100; // Amount in cents
            $razorpay_end_date = $input['razorpay_end_date']; //5000 * 100; // Amount in cents
            $razorpay_coin_order_id = $input['razorpay_coin_order_id']; //5000 * 100; // Amount in cents
            $razorpay_subscription_type = $input['razorpay_subscription_type']; //5000 * 100; // Amount in cents
            $razorpay_cart_id = $input['razorpay_cart_id']; //5000 * 100; // Amount in cents
            // dd( $razorpay_cart_id);

            $ch = curl_init("https://api.razorpay.com/v1/payments/$paymentId/capture");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['amount' => $amount]));
            curl_setopt($ch, CURLOPT_USERPWD, "$apiKey:$apiSecret");
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
            ]);

            $response = curl_exec($ch);
            curl_close($ch);

            $captureResponse = json_decode($response, true);

            // dd($captureResponse);
            
    

        // Signature verification using cURL (or using Razorpay PHP SDK)
        $generatedSignature = hash_hmac('sha256', $input['razorpay_order_id'] . '|' . $input['razorpay_payment_id'], $secret);
        if($captureResponse['status']=="captured") {

           if($razorpay_subscription_type=='yes'){
            $company_coins = DB::table('company_coins')->where('event_type','=',$topUptype)->where('company_id','=',$company_id)->first();
            $actual_coins = DB::table('cart_transaction_history')->where('payment_orderId','=',$razorpay_order_id)->first();
            // $actual_coins = $update_coins-$company_coins->coins;
            DB::table('company_coins')->where('event_type','=',$topUptype)->where('company_id','=',$company_id)->update(['coins'=>$update_coins,'actual_coins'=>$actual_coins->credit,'start_date'=>$razorpay_start_date,'end_date'=>$razorpay_end_date,'coin_order_id'=>$razorpay_coin_order_id,'updated_at'=>date("Y-m-d H:i:s")]);

            DB::table('coin_transection_logs')->where('payment_orderId','=',$razorpay_order_id)->update(['payment_Id'=>$paymentId,'cart_id'=>$razorpay_cart_id,'coins'=>$update_coins,'remain_coins'=>0,'actual_coins'=>$actual_coins->credit]);
            DB::table('coin_orders')->where('payment_orderId','=',$razorpay_order_id)->update(['payment_Id'=>$paymentId,'status'=>1,'cart_id'=>$razorpay_cart_id]);
           }else{
            $actual_coins = DB::table('cart_transaction_history')->where('payment_orderId','=',$razorpay_order_id)->first();
            DB::table('company_coins')->where('event_type','=',$topUptype)->where('company_id','=',$company_id)->update(['coins'=>$update_coins,'updated_at'=>date("Y-m-d H:i:s")]);

            DB::table('coin_transection_logs')->where('payment_orderId','=',$razorpay_order_id)->update(['payment_Id'=>$paymentId,'cart_id'=>$razorpay_cart_id,'coins'=>$update_coins,'remain_coins'=>0,'actual_coins'=>$actual_coins->credit]);

            DB::table('top_up_logs')->where('payment_orderId','=',$razorpay_order_id)->update(['payment_Id'=>$paymentId,'status'=>1,'cart_id'=>$razorpay_cart_id]);
            
           }

           DB::table('cart_transaction_history')->where('payment_orderId','=',$razorpay_order_id)->update(['payment_Id'=>$paymentId,'status'=>$captureResponse['status'],'method'=>$captureResponse['method'],'bank'=>$captureResponse['bank'],'international'=>$captureResponse['international'],'updated_at'=>date("Y-m-d h:i:s"),'cart_id'=>$razorpay_cart_id,'invoice_date'=>date("Y-m-d H:i:s")]);

             DB::table('carts')->where('id','=',$razorpay_cart_id)->update(['status'=>1,'updated_at'=>date("Y-m-d H:i:s")]);
            return response()->json(['status' => 'captured']);

        }elseif($captureResponse['status']=="authorized") {
               
            DB::table('cart_transaction_history')->where('payment_orderId','=',$razorpay_order_id)->update(['payment_Id'=>$paymentId,'status'=>$captureResponse['status'],'updated_at'=>date("Y-m-d h:i:s"),'cart_id'=>$razorpay_cart_id,'invoice_date'=>date("Y-m-d H:i:s")]);
            return response()->json(['status' => 'authorized']);
        }elseif($captureResponse['status']=="failed") {
           
            DB::table('cart_transaction_history')->where('payment_orderId','=',$razorpay_order_id)->update(['payment_Id'=>$paymentId,'status'=>$captureResponse['status'],'updated_at'=>date("Y-m-d h:i:s"),'cart_id'=>$razorpay_cart_id,'invoice_date'=>date("Y-m-d H:i:s")]);

           return response()->json(['status' => 'failed']);
        }elseif($captureResponse['status']=="refunded") {
           
            DB::table('cart_transaction_history')->where('payment_orderId','=',$razorpay_order_id)->update(['payment_Id'=>$paymentId,'status'=>$captureResponse['status'],'updated_at'=>date("Y-m-d h:i:s"),'cart_id'=>$razorpay_cart_id,'invoice_date'=>date("Y-m-d H:i:s")]);

          return response()->json(['status' => 'refunded']);
        }elseif($captureResponse['status']=="partially_refunded") {
            
            DB::table('cart_transaction_history')->where('payment_orderId','=',$razorpay_order_id)->update(['payment_Id'=>$paymentId,'status'=>$captureResponse['status'],'updated_at'=>date("Y-m-d h:i:s"),'cart_id'=>$razorpay_cart_id,'invoice_date'=>date("Y-m-d H:i:s")]);

           return response()->json(['status' => 'partially_refunded']);
        }elseif($captureResponse['status']=="created") { 

            DB::table('cart_transaction_history')->where('payment_orderId','=',$razorpay_order_id)->update(['payment_Id'=>$paymentId,'status'=>$captureResponse['status'],'updated_at'=>date("Y-m-d h:i:s"),'cart_id'=>$razorpay_cart_id,'invoice_date'=>date("Y-m-d H:i:s")]);
          return response()->json(['status' => 'created']);
        }else{
           
            DB::table('cart_transaction_history')->where('payment_orderId','=',$razorpay_order_id)->update(['payment_Id'=>$paymentId,'status'=>$captureResponse['status'],'updated_at'=>date("Y-m-d h:i:s"),'cart_id'=>$razorpay_cart_id,'invoice_date'=>date("Y-m-d H:i:s")]);

          return response()->json(['status' => 'error']);
        }
    }

    public function linkPaymentCallback(Request $request){
              $input = $request->all();
              $razorpay_payment_id =  $input['razorpay_payment_id']; 
              $razorpay_payment_link_id =  $input['razorpay_payment_link_id']; 
              $razorpay_payment_link_status =  $input['razorpay_payment_link_status']; 
              $razorpay_signature =  $input['razorpay_signature']; 

              $paymentId =  $razorpay_payment_id; // Replace with your actual payment ID
              $captureResponse = $this->getPaymentStatus($paymentId);

            //   dd( $paymentStatus);

               $link_payments = DB::table('link_payments')->where('payment_link_id','=',$razorpay_payment_link_id)->first();
              if($captureResponse['status']=='captured'){
                 $link_payments = DB::table('link_payments')->where('payment_link_id','=',$razorpay_payment_link_id)->first();
                if($link_payments->subscription_type=='no'){

                    
                    $actual_coins = DB::table('cart_transaction_history')->where('payment_link_id','=',$razorpay_payment_link_id)->first();

                    // dd($captureResponse['order_id'],$actual_coins);

                    DB::table('company_coins')->where('event_type','=',$link_payments->event_type)->where('company_id','=',$link_payments->company_id)->update(['coins'=>$link_payments->update_coins,'updated_at'=>date("Y-m-d H:i:s")]);

                    DB::table('coin_transection_logs')->where('payment_link_id','=',$razorpay_payment_link_id)->update(['payment_Id'=>$paymentId,'cart_id'=>$link_payments->cart_id,'coins'=>$link_payments->update_coins,'remain_coins'=>0,'actual_coins'=>$actual_coins->credit]);

                    DB::table('top_up_logs')->where('payment_link_id','=',$razorpay_payment_link_id)->update(['payment_Id'=>$paymentId,'status'=>1,'cart_id'=>$link_payments->cart_id,'payment_orderId'=>$captureResponse['order_id']]);


                }else{

                    $company_coins = DB::table('company_coins')->where('event_type','=',$link_payments->event_type)->where('company_id','=',$link_payments->company_id)->first();
                    $actual_coins = DB::table('cart_transaction_history')->where('payment_link_id','=',$razorpay_payment_link_id)->first();
                    // $actual_coins = $link_payments->update_coins-$company_coins->coins;
                    DB::table('company_coins')->where('event_type','=',$link_payments->event_type)->where('company_id','=',$link_payments->company_id)->update(['coins'=>$link_payments->update_coins,'actual_coins'=>$link_payments->coins,'start_date'=>$link_payments->start_date,'end_date'=>$link_payments->end_date,'coin_order_id'=>$link_payments->coin_order_id,'updated_at'=>date("Y-m-d H:i:s")]);
        
                    DB::table('coin_transection_logs')->where('payment_link_id','=',$razorpay_payment_link_id)->update(['payment_Id'=>$paymentId,'payment_orderId'=>$captureResponse['order_id'],'cart_id'=>$link_payments->cart_id,'coins'=>$link_payments->update_coins,'remain_coins'=>0,'actual_coins'=>$link_payments->coins]);
                    DB::table('coin_orders')->where('payment_link_id','=',$razorpay_payment_link_id)->update(['payment_Id'=>$paymentId,'payment_orderId'=>$captureResponse['order_id'],'status'=>1,'cart_id'=>$link_payments->cart_id]);

                }
                DB::table('link_payments')->where('payment_link_id','=',$razorpay_payment_link_id)->update(['payment_orderId'=>$captureResponse['order_id'],'status'=>1]);
                DB::table('cart_transaction_history')->where('payment_link_id','=',$razorpay_payment_link_id)->update(['payment_Id'=>$paymentId,'payment_orderId'=>$captureResponse['order_id'],'status'=>$captureResponse['status'],'method'=>$captureResponse['method'],'bank'=>$captureResponse['bank'],'international'=>$captureResponse['international'],'updated_at'=>date("Y-m-d H:i:s"),'cart_id'=>$link_payments->cart_id,'invoice_date'=>date("Y-m-d H:i:s")]);

                DB::table('carts')->where('id','=',$link_payments->cart_id)->update(['status'=>1,'updated_at'=>date("Y-m-d H:i:s")]);
                // return view('success', ['paymentLink' => $link_payments->payment_link]);
                return redirect()->route('login');
                 
              }else{
                // return view('success', ['paymentLink' => $link_payments->payment_link]);
                return redirect()->route('login');

              }
              

              
             
    }
    private function getPaymentStatus($paymentId) {
        // Razorpay credentials
        $payment_settings = DB::table('payment_settings')->select('payment_settings.*')->first();
        $apiKey = $payment_settings->razorpay_key;
        $apiSecret = $payment_settings->razorpay_secret;
    
        // Razorpay API endpoint for payment details
        $url = 'https://api.razorpay.com/v1/payments/' . $paymentId;
    
        // Initialize cURL session
        $ch = curl_init();
    
        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $apiKey . ':' . $apiSecret); // For authentication
    
        // Execute cURL request and get response
        $response = curl_exec($ch);
    
        // Check for errors
        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            curl_close($ch);
            return 'cURL error: ' . $error_msg;
        }
    
        // Close cURL session
        curl_close($ch);
    
        // Decode the JSON response
        $paymentDetails = json_decode($response, true);
    
        // Check if there's an error in the API response
        if (isset($paymentDetails['error'])) {
            return 'API Error: ' . $paymentDetails['error']['description'];
        }
    
        // Return the payment details
        return $paymentDetails;
    }


}
