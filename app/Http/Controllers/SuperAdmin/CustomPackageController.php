<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Models\PaymentSetting;
use Illuminate\Http\Request;
use App\Models\Package;
use App\Helper\Reply;
use Illuminate\Support\Facades\DB;
use App\Helper\GeneralMethod;
use Yajra\DataTables\Facades\DataTables;
use App\Models\PackageFeature;
use App\Models\PackageOrderItem;
use Carbon\Carbon;
use App\Services\MailService;
use App\Services\ElasticService;
use Illuminate\Support\Facades\Http;

class CustomPackageController extends SuperAdminBaseController
{

	private $merchantId = "PGTESTPAYUAT";
    private $apiKey = "099eb0cd-02cf-4e2a-8aca-3e6c6aff0399";
    private $apiKeyIndex = "1";
    private $hostUrl = "https://api-preprod.phonepe.com/apis/hermes";
	protected $mailService;
    protected $elasticService;
	public function __construct(MailService $mailService,ElasticService $elasticService)
    {
        parent::__construct();
        $this->pageTitle 	= 'menu.packages';
        $this->pageIcon 	= 'icon-settings';
		$this->mailService = $mailService;
        $this->elasticService = $elasticService;
    }
	public function initiatePayment(Request $request)
    {
        // Payment details
        $transactionId = uniqid('ORD_');//'TXN123456789'; // Generate a unique transaction ID
        $amount = 10000; // Amount in paise (e.g., 100.00 INR = 10000 paise)
        $payload = [
            "merchantId" => $this->merchantId,
            "transactionId" => $transactionId,
            "amount" => $amount,
            "merchantOrderId" => 'ORD123456789', // Your order ID
            "successCallbackUrl" => url('/payment/success'),
            "failCallbackUrl" => url('/payment/fail'),
        ];

        // Convert payload to JSON
        $payloadJson = json_encode($payload);

        // Encrypt the payload
        $encryptedPayload = $this->encryptPayload($payloadJson);

        // Make the API request using cURL
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'X-VERIFY' => $this->generateXVerifyHeader($encryptedPayload),
            'X-KEY-INDEX' => $this->apiKeyIndex,
        ])->post("{$this->hostUrl}/v1/initiatePayment", [
            'request' => $encryptedPayload,
        ]);

        return $response->json();
    }
    // Method to encrypt the payload using the API key
    private function encryptPayload($payload)
    {
        return base64_encode(hash_hmac('sha256', $payload, $this->apiKey, true));
    }
    // Method to generate the X-VERIFY header
    private function generateXVerifyHeader($payload)
    {
        $salt = $this->apiKey . '/v1/initiatePayment';
        return hash('sha256', $payload . $salt);
    }
    public function index() {
       
	    /*$this->packages = Package::all();
        $this->paymentSetting = PaymentSetting::first();
        $this->packageData = Package::
        where(function($query){
            $query->where('monthly_price', '!=', 0);
            $query->orWhere('annual_price', '!=', 0);
        })
            ->where(function($query){
                $query->whereNull('stripe_annual_plan_id');
                $query->orWhereNull('stripe_monthly_plan_id');
            })
            ->first();*/
		$this->invoice_counter = '';//DB::table('package_invoice')->select('package_invoice.id')->count();					
				
			
       
	    return view('super-admin.custompackages.index', $this->data);
    }
	
	public function create($company_id = null) 
	{
		 $packageType = decrypt((request('packageType')));
		//  dd( $packageType);
    	$this->company_id 			=	$company_id;
		$this->company 				=	DB::table('companies')->select('*')->where('companies.id', $this->company_id)->first();
		$this->package_features_operations = PackageFeature::where('features_type', '=', '1')->get();
        $this->package_features_salse = PackageFeature::where('features_type', '=', '2')->get();
		$this->packages =  DB::table('coin_packages')
		->join('coin_package_types','coin_package_types.id','=','coin_packages.coin_package_type_id')
		->where('coin_packages.event_type', '=', $packageType)
        ->select('coin_packages.*','coin_package_types.title','coin_package_types.days')->get();//Package::where('status', '=', '1')->get();
	    //$this->paymentSetting = PaymentSetting::first();
		$this->link_payments =	DB::table('link_payments')->select('*')->where('status','=',0)->where('subscription_type','=','yes')->where('company_id', $company_id)->orderByDesc('id')->first();

		$clients  = DB::table('clients')->leftJoin('companies', 'companies.id', '=', 'clients.company_id')
            ->select('clients.id','companies.company_name','users.name')        
            ->leftJoin('users','users.company_id','=','companies.id',)
            ->where('clients.company_id', '=', $this->company_id)
            ->where('clients.self_company', '=', 1)
            ->first();

		$locInfo = DB::table('client_location')
			->leftJoin('countries', 'countries.id', '=', 'client_location.country_id')
			->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')
			->leftJoin('state_list', 'state_list.id', '=', 'client_location.state')
			->select(
				'client_location.id',
				'client_location.city',
                'client_location.city_id',
                'client_location.state as state_id',
				'client_location.address',
				'client_location.postcode',
				'client_location.country_id',
				'city_list.city as city_name',
				'state_list.state as state_name',
				'countries.country_name'
			)
			->where('client_id', $clients->id)
			->first();

		// dd( $locInfo);
        $this->countrie_id =  ($locInfo) ? $locInfo->country_id : '';
        $this->city_id =  ($locInfo) ? $locInfo->city_id : '';
        $this->state_id =  ($locInfo) ? $locInfo->state_id : '';
        
        $this->phone =  auth()->user()->mobile;
        $this->building = '';
        $this->postal_code = '';
        $this->gstin = '';
		$this->pan_no = '';

             


         $company_id =  $this->company_id;

        // dd($this->countrie_id);
		  
        $this->company_name  = @$clients->company_name;
        $this->user_name  = @$clients->name;
        $nameParts = explode(' ', $this->user_name);
        $this->firstName = $nameParts[0];
        $this->lastName = isset($nameParts[1]) ? $nameParts[1] : '';
		
           $this->locArr  = '';
            if($locInfo){
                $city_name = @$locInfo->city;
				
				$this->locArr = ucwords(@$locInfo->address) . ' ' . ucwords($city_name) . ' , ' . ucwords(@$locInfo->state_name) . ' , ' . ucwords(@$locInfo->country_name);
            }

            $this->countries = DB::table('countries')
			->select('countries.*')
			->get();

            $billing_address_companies = DB::table('billing_address_companies')->where('company_id','=',$company_id)->first();

            if($billing_address_companies){
                $this->countrie_id =  ($billing_address_companies) ? $billing_address_companies->country_id : '';
                $this->city_id =  ($billing_address_companies) ? $billing_address_companies->city : '';
                $this->state_id =  ($billing_address_companies) ? $billing_address_companies->state_id : '';
                $this->locArr = ($billing_address_companies) ? $billing_address_companies->street : '';
                $this->phone = ($billing_address_companies) ? $billing_address_companies->phone : '';
                $this->building = ($billing_address_companies) ? $billing_address_companies->building : '';
                $this->firstName = ($billing_address_companies) ? $billing_address_companies->first_name : '';
                $this->lastName = ($billing_address_companies) ? $billing_address_companies->last_name : '';
                $this->postal_code = ($billing_address_companies) ? $billing_address_companies->postal_code : '';
                $this->gstin = ($billing_address_companies) ? $billing_address_companies->gstin : '';
				$this->pan_no = ($billing_address_companies) ? $billing_address_companies->pan_no : '';
                $this->user_name  =  ($billing_address_companies) ? $billing_address_companies->first_name.' '.$billing_address_companies->last_name : '';
                
            }
			
        return view('super-admin.custompackages.create', $this->data);
    
	}

	public function packageFeature(Request $request){
		
		// dd($request->package_id);
		$coin_packages = DB::table('coin_packages')->select('*')->where('id', '=', $request->package_id)->first();
		// dd($coin_packages->event_type);
		$package_coin_events = DB::table('coin_events')
		->where('status','=',1)
		->where('event_type', '=', $coin_packages->event_type)
		->get();

		$currency = $request->currency_get;
		$gst_on_get = $request->gst_on_get;
		// dd($currency);

		$this->coin_events = $package_coin_events;
		$price = ($currency=="INR") ? $coin_packages->price : $coin_packages->price_usd;
		$this->price = ($currency=="INR") ? $coin_packages->price : $coin_packages->price_usd;

		if($currency == 'INR' && $gst_on_get=='Yes'){
			$gst = 18;
			$gst_amount = ($price * $gst) / 100;
			$total_price = $price + $gst_amount;
		}else{
			$gst = 0; // GST might not apply for USD or set appropriate tax value
			$gst_amount = ($price * $gst) / 100; // Modify this if other tax applies
			$total_price = $price + $gst_amount; // Total price in USD
		}
		    $this->gst = $gst; // GST might not apply for USD or set appropriate tax value
			$this->gst_amount = $gst_amount; // Modify this if other tax applies
			$this->total_price = $total_price; // Total price in USD

		// dd($this->price,$coin_packages->price,$coin_packages->price_usd);
		$this->coins = $coin_packages->coins;
		$this->discount = $coin_packages->discount;
		$this->event_type = $coin_packages->event_type;
		// dd($package_coin_events);
	
		$view = view('super-admin.custompackages.viewmorepackagefeature', $this->data)->render();

		$this->package = DB::table('coin_packages')->select('*')->where('id', '=', $request->package_id)->first(); //Package::findOrfail($request->package_id);
		$check = false;
		if($this->package){
			$check = true;
		}

		// dd($view);
        return Reply::dataOnly(['status' => 'success', 'view' => $view,'check'=>$check]);

		// dd($this->package_features_salse);


	}
	public function packageFeatureOld(Request $request){
		
		dd($request->package_id);
		$this->package_features_operations = PackageFeature::join('package_details','package_details.package_feature_id','=','package_features.id')->join('packages','packages.id','=','package_details.package_id')->select('package_details.*','package_features.title','package_features.id as feature_id','package_features.field_name','packages.custome_pck')->where('package_id', '=', $request->package_id)
		->where('package_id', '=', $request->package_id)->where('features_type', '=', '1')->get();
		// dd($this->package_features_operations);
        $this->package_features_salse = PackageFeature::join('package_details','package_details.package_feature_id','=','package_features.id')->join('packages','packages.id','=','package_details.package_id')->select('package_details.*','package_features.title','package_features.id as feature_id','package_features.field_name','packages.custome_pck')->where('package_id', '=', $request->package_id)
		->where('package_id', '=', $request->package_id)->where('features_type', '=', '2')->get();
		
		$view = view('super-admin.custompackages.viewmorepackagefeature', $this->data)->render();

		$this->package = Package::findOrfail($request->package_id);
		$check = false;
		if($this->package){
			$check = true;
		}

		// dd($view);
        return Reply::dataOnly(['status' => 'success', 'view' => $view,'check'=>$check]);

		// dd($this->package_features_salse);


	}
	public function lastdate(Request $request){
		// $this->package = Package::findOrfail($request->package_id);
		$this->package = DB::table('coin_packages')->select('*')->where('id', '=', $request->package_id)->first();
		$newDate = date("Y-m-d");
		// dd($this->package);
		$check = false;
		if(!@$this->package->custome_pck){
		if($this->package){
			$days = $this->package->days;
			$currentDate = date("Y-m-d",  strtotime($request->cvaccess_startdate)); // Get current date in YYYY-MM-DD format
			$newDate = date("Y-m-d", strtotime($currentDate . " +".$this->package->days." days")); // Add 356 days to the current date
			// dd($newDate,$this->package->trial_duration);
			$custome_pck = $this->package->custome_pck;
			if(@$this->package->custome_pck){
                  $check = true;
			}

			return Reply::dataOnly(['status' => 'success', 'newDate' => $newDate,'check'=>$check]);

		  }
	   }
		return Reply::dataOnly(['status' => 'success', 'newDate' =>$newDate,'check'=>$check]);

	}
	public function generatecustompackages(Request $request){

		$company_id = $request->company_id;
		$total_price = $request->total_price;
		$currency = $request->currency_get;
		$packages_id = $request->packages_id;
		$mainType = $request->mainType;
		$amount = $total_price * 100; // Amount in paise

		$payment_settings = DB::table('payment_settings')->select('payment_settings.*')->first();
		$apiKey = $payment_settings->razorpay_key;
		$apiSecret = $payment_settings->razorpay_secret;


		$company_details = DB::table('companies')->where('id','=',$company_id)->first();

		if($mainType==1){
			$packages =  DB::table('coin_packages')
			->join('coin_package_types','coin_package_types.id','=','coin_packages.coin_package_type_id')
			->where('coin_packages.id', '=', $packages_id)
			->select('coin_packages.*','coin_package_types.title','coin_package_types.days')->first();

			$product_name = ($packages) ? @$packages->title : '';
		    $event_type = ($packages) ? $packages->event_type : '';


		}else{
			$top_up_coins_cart = DB::table('top_up_coin_packages')
								->where('top_up_coin_packages.id',$packages_id)
								->select('top_up_coin_packages.*')
								->first();

			$product_name = "Top-up Coins";
			$event_type = ($top_up_coins_cart) ? $top_up_coins_cart->event_type : '';

		}

		 $paymentLinkData = [
			'amount' => $amount,
			 'currency' => $currency,
			 'description' => $product_name . ' - ' . $event_type,
			 'callback_url' => url('link-payment-callback/'),
			 'callback_method' => 'get',
			 'customer' => [
				 'name' => $company_details->company_name,
				 'contact' => $company_details->company_phone,
				 'email' => $company_details->company_email,
			 ],
			 'notify' => [
				 'sms' => true,
				 'email' => true,
			 ]
		 ];
		 



		//  dd($paymentLinkData);
		
		 // Create payment link using cURL
		 
		 $paymentLinkResponse = $this->createRazorpayPaymentLink($apiKey, $apiSecret, $paymentLinkData);

		 $paymentLink = $paymentLinkResponse['short_url'];
		 $Paymentstatus = $paymentLinkResponse['status'];
		 $payment_orderId = $paymentLinkResponse['id'];


		 return response()->json([
			'message' => '',
			'status' => 'success',
			'short_url' =>$paymentLink,
			'Paymentstatus' => $Paymentstatus,
			'payment_orderId' => $payment_orderId
		]); 



		//  dd( $paymentLinkResponse);


	}
	public function createInvoice(Request $request) 
	{
		// $call_back_url		=	url('link-payment-callback/');
	
		// echo '<pre>'; print_r($_REQUEST); exit; 
		$company_id = $request->company_id;
		$currency = $request->currency;
		$price = $request->price;
		$total_price = $request->total_price;
		$gst_amount = $request->gst_amount;
		$gst = $request->gst;
		$chequenumber 			= $request->chequenumber;
        $chequedate 			= $request->chequedate;
        $bankname 			= $request->bankname;
        $transactionid 			= $request->transactionid;
        $bankreference 			= $request->bankreference;
        $coins= $request->total_coins;
		$paymentmodeselector= $request->paymentmodeselector;
        $paymentmethodselector= $request->paymentmethodselector;

		$status= "";
        $Paymentstatus = 'captured';
        $payment_orderId = '';

        $paymentLink = '';
        $top_up_logs_status = 1;


		$paymentLinkPaymentstatus = $request->paymentLinkPaymentstatus;
		$paymentLinkpayment_orderId = $request->paymentLinkpayment_orderId;
		$paymentLinkshort_url = $request->paymentLinkshort_url;


		$company_coins_days = DB::table('coin_packages')->where('id',$request->packages_id)->first();
		$coin_orders = [
			'coin_package_id'=>$request->packages_id,
			'price'=>$request->price,
			'coins'=>$request->total_coins,
			'days'=>$company_coins_days->days,
			'event_type'=>$request->event_type,
			'company_id'=>$request->company_id,
			'discount'=>$request->discount,
			'status'=>1,
			'created_at'=>date("Y-m-d H:i:s"),
			'updated_at'=>date("Y-m-d H:i:s")	
		];
		// dd($coin_orders);
		// $coin_order_id = 1;
		$coin_order_id = DB::table('coin_orders')->insertGetId($coin_orders);

		$response = $this->elasticService->getClient()->update([
                        'index' => ELASTIC_COIN_ORDERS,
                        'id'    => $coin_order_id,
						'retry_on_conflict' => 5,
						'body'  => [
							'doc' => [ 
								'id' =>  $coin_order_id,
								'coin_package_id'=>$request->packages_id,
								'price'=>$request->price,
								'coins'=>$request->total_coins,
								'days'=> $company_coins_days->days,
								'event_type'=>$request->event_type,
								'company_id'=>$request->company_id,
								'discount'=>$request->discount,
								'status'=>1,
								'updated_at' => now()->toIso8601String(),
								'created_at' => now()->toIso8601String()
							],
							'doc_as_upsert' => true,
							'detect_noop' => false
						],
					]);


		for($i=0; $i< count($request->id); $i++){
			$coin_order_events = [
				'company_id'=>$request->company_id,
				'coin_event_id'=>$request->id[$i],
				'deductions'=>$request->default_rate[$i],
				'deduction_type'=>$request->deduction_type[$i],
				'coin_order_id'=>$coin_order_id,
				'required_coin'=>$request->minimum_required[$i],
				'event_type'=>$request->event_type
			];
			// dd($coin_order_events);
			$eventsId = DB::table('coin_order_events')->insertGetId($coin_order_events);

			$response = $this->elasticService->getClient()->update([
                        'index' => ELASTIC_COIN_ORDER_EVENTS,
                        'id'    => $eventsId,
						'retry_on_conflict' => 5,
                        'body'  => [
                            'doc' => [ 
								'id' =>  $eventsId,
                                'company_id'=>$request->company_id,
                                'coin_event_id'=>$request->id[$i],
                                'deductions'=>$request->default_rate[$i],
                                'deduction_type'=>$request->deduction_type[$i],
                                'coin_order_id'=>$coin_order_id,
                                'required_coin'=>$request->minimum_required[$i],
                                'event_type'=>$request->event_type
							],
							'doc_as_upsert' => true,
							'detect_noop' => false
						],
                    ]);

		}

		$coinEvents = DB::table('coin_orders')
		->join('coin_packages','coin_packages.id','=','coin_orders.coin_package_id')
        ->join('coin_package_types','coin_package_types.id','=','coin_packages.coin_package_type_id')
		->where('coin_orders.id',$coin_order_id)
        ->select('coin_package_types.title','coin_packages.event_type','coin_packages.custome_pck')->first();

		$custome_chk = ($coinEvents) ? @$coinEvents->custome_pck : 0;

		// dd($custome_chk,$coinEvents->custome_pck);


		if($paymentmodeselector=='Payment Link'){
            if($paymentmethodselector=='razorpay'){
            //    dd($total_price,$price);
            
                $payment_settings = DB::table('payment_settings')->select('payment_settings.*')->first();
                 $apiKey = $payment_settings->razorpay_key;
                 $apiSecret = $payment_settings->razorpay_secret;

                 $amount = $total_price * 100; // Amount in paise

				 $paymentLink = $paymentLinkshort_url;
				 $Paymentstatus = $paymentLinkPaymentstatus;
                 $payment_orderId = $paymentLinkpayment_orderId;

                                


                   $company_details = DB::table('companies')->where('id','=',$company_id)->first();
                   $product_name = $coinEvents->title;
                   $event_type = @$coinEvents->event_type;
                    $paymentLinkData = [
                       'amount' => $amount,
                        'currency' => $currency,
                        'description' => $product_name . ' - ' . $event_type,
                        'callback_url' => url('link-payment-callback/'),
                        'callback_method' => 'get',
                        'customer' => [
                            'name' => $company_details->company_name,
                            'contact' => $company_details->company_phone,
                            'email' => $company_details->company_email,
                        ],
                        'notify' => [
                            'sms' => true,
                            'email' => true,
                        ]
                    ];
                   
                    // Create payment link using cURL
                    
                    // $paymentLinkResponse = $this->createRazorpayPaymentLink($apiKey, $apiSecret, $paymentLinkData);

                    if (!empty($paymentLink)) {

                        $basecompany = GeneralMethod::baseCompany();
                        $senderName = ($basecompany->companyName) ? $basecompany->companyName : '';
                        $fromAddress = ($basecompany->companyEmail) ? $basecompany->companyEmail : '';
                        $attachments = array();
                        $cc = array();
                   
                       $subject = "Your Payment Link";

					   $message = "
                        <html>
                        <head>
                            <title>Your Payment Link</title>
                        </head>
                        <body>
                            <h1>Hello,</h1>
                            <p>We have generated a payment link for you. Please use the following link to complete your payment of ". ($currency == 'INR' ? '₹' : '$') ."" . number_format($amount / 100, 2) . ":</p>
                            <p><a href='$paymentLink'>$paymentLink</a></p>
                            <p>Thank you!</p>
                        </body>
                        </html>
                        ";
    
					$to_Mail_Id = $company_details->company_email;
                    

                    $datamail = [
						'message' => $message
					];
                    $result = $this->mailService->sendEmail($to_Mail_Id, $subject,'email.common.index', (object) $datamail, $attachments, $fromAddress, $senderName,$cc);
					

                     } else {
                        // return response()->json([
                        //     'message' => 'Failed to create payment link',
                        //     'status' => 'error'
                        // ]);
                     }


                     $status= "razorpay"; 
                     $top_up_logs_status = 0;

					//  $createPayment = GeneralMethod::createPayment($total_price, $currency);
                  
                    //  $Paymentstatus = $paymentLinkResponse['status'];
                    //  $payment_orderId = $paymentLinkResponse['id'];

                    // dd( $paymentLinkResponse);

            }else{
                $createPayment = GeneralMethod::createPayment($total_price, $currency);
                $status= "phonepe";
            }
        }else{

			
             
            $status= "offline";

        }


		$subscription_type = 'yes';
        $product_name = $coinEvents->title;
        $event_type = @$coinEvents->event_type;
        $duration = "Existing Package";
        $credit = $request->total_coins;


		
		if ($coinEvents) {
			$cartValue = [
				'company_id' => $company_id,
				'subscription_type' => $subscription_type,
				'product_name' => $product_name . ' - ' . $event_type,
				'pck_top_id' => $coin_order_id,
				'event_type' =>$event_type,
				'duration' => $duration,
				'credit' => $credit,
				'currency' => $currency,
				'price' => $price,
				'gst' => $gst,
				'status' => 2,
				'gst_amount' => $gst_amount,
				'total_price' => $total_price,
				'user_id' => auth()->user()->id
			];
		
			// dd($cartValue);
			// Insert the cart record
			DB::table('carts')->insert($cartValue);
			$cartsInsertedId = DB::getPdo()->lastInsertId();

			$response = $this->elasticService->getClient()->update([
                'index' => ELASTIC_CARTS,
                'id'    => $cartsInsertedId,
                'retry_on_conflict' => 5,
                'body'  => [
                    'doc' => [ 
                        'id' =>  $cartsInsertedId,
                        'company_id' => $company_id,
                        'subscription_type' => $subscription_type,
                        'product_name' => $product_name . ' - ' . $event_type,
                        'pck_top_id' => $coin_order_id,
                        'event_type' => $event_type,
                        'duration' => $duration,
                        'credit' => $credit,
                        'currency' => $currency,
                        'price' => $price,
                        'gst' => $gst,
                        'gst_amount' => $gst_amount,
                        'total_price' => $total_price,
                        'user_id' => auth()->user()->id,
                        'status' => 2,
                        'updated_at' => now()->toIso8601String(),
                        'created_at' => now()->toIso8601String()
                    ],
                    'doc_as_upsert' => true,
                    'detect_noop' => false
                ],
            ]);
			
			// dd("kfd");
		}
		$topUptype =  $event_type;

		$company_coins = DB::table('company_coins')->where('event_type','=',$topUptype)->where('company_id','=',$company_id)->whereDate('end_date', '>=', now())->first();
		$update_coins = $coins;
		if($company_coins){
			$update_coins = $company_coins->coins+$coins;
		}


		$row = DB::table('carts')->where('id','=',$cartsInsertedId)->where('status','=',2)->first();

		$maxId = DB::table('cart_transaction_history')->max('id');
        $pre_fixed = DB::table('pre_fixed_settings')->where('type','invoice')->first();
        $number = ($maxId > 0)? $maxId+1 : 1; 
        $padded_number = str_pad($number, 5, '0', STR_PAD_LEFT);
        $invoices_no =  $pre_fixed->pre_fixed.$padded_number;
        $invoices_date = date("Y-m-d H:i:s"); 
		

		DB::table('cart_transaction_history')->insert([
			'company_id' => $row->company_id,
			'subscription_type' => $row->subscription_type,
			'pck_top_id' => $row->pck_top_id,
			'event_type' => $row->event_type,
			'product_name' => $row->product_name,
			'duration' => $row->duration,
			'credit' => $row->credit,
			'currency' => $row->currency,
			'price' => $row->price,
			'gst' => $row->gst,
			'gst_amount' => $row->gst_amount,
			'total_price' => $row->total_price,
			'user_id' => $row->user_id,
			'status' =>  $Paymentstatus,
			"payment_link_id" => $payment_orderId,
			"chequenumber" => (@$chequenumber) ? $chequenumber : '',
			"chequedate" => (@$chequedate) ? date("Y-m-d", strtotime($chequedate)) : '',
			"bankname" => (@$bankname) ? $bankname : '',
			"transactionid" => (@$transactionid) ? $transactionid : '',
			"bankreference" => (@$bankreference) ? $bankreference : '',
			'payment_gateway' => $paymentmethodselector,
			'invoiceId' => $invoices_no,
			'invoice_date' => $invoices_date,
			'payment_mode' => $paymentmodeselector,
			'cart_id'=>$cartsInsertedId,
			'created_at' => date("Y-m-d H:i:s"),
			'updated_at' => date("Y-m-d H:i:s")
		]);
		$cartsInsertedIdHistoryID = DB::getPdo()->lastInsertId();


		 $response = $this->elasticService->getClient()->update([
                'index' => ELASTIC_CART_TRANSACTION_HISTORY,
                'id'    => $cartsInsertedIdHistoryID,
                'retry_on_conflict' => 5,
                'body'  => [
                    'doc' => [ 
                        'id' =>  $cartsInsertedIdHistoryID,
                        'company_id' => $row->company_id,
                        'subscription_type' => $row->subscription_type,
                        'pck_top_id' => $row->pck_top_id,
                        'event_type' => $row->event_type,
                        'product_name' => $row->product_name,
                        'duration' => $row->duration,
                        'credit' => $row->credit,
                        'currency' => $row->currency,
                        'price' => $row->price,
                        'gst' => $row->gst,
                        'gst_amount' => $row->gst_amount,
                        'total_price' => $row->total_price,
                        'user_id' => $row->user_id,
                        'status' => $Paymentstatus,
						"payment_link_id" => $payment_orderId,
						"chequenumber" => (@$chequenumber) ? $chequenumber : '',
						'chequedate' => ($chequedate) ? Carbon::parse($chequedate)->toIso8601String() : null,
						"bankname" => (@$bankname) ? $bankname : '',
						"transactionid" => (@$transactionid) ? $transactionid : '',
						"bankreference" => (@$bankreference) ? $bankreference : '',
						'payment_gateway' => $paymentmethodselector,
						'invoiceId' => $invoices_no,
						'invoice_date' => ($invoices_date) ? Carbon::parse($invoices_date)->toIso8601String() : null,
						'payment_mode' => $paymentmodeselector,
						'cart_id'=>$cartsInsertedId,
                        'created_at' => now()->toIso8601String(),
                        'updated_at' => now()->toIso8601String()
                    ],
                    'doc_as_upsert' => true,
                    'detect_noop' => false
                ],
            ]);



		DB::table('link_payments')->insert([
			'status' => '0',
			'company_id' => $row->company_id,
			'coins' => $row->credit,
			'coin_order_id' => $row->pck_top_id,
			'amount' => $total_price,
			'start_date'=>date("Y-m-d", strtotime($request->cvaccess_startdate)),
		    'end_date'=>date("Y-m-d", strtotime($request->cvaccess_enddate)),
			'update_coins' => $update_coins,
			'event_type' =>$row->event_type,
			'subscription_type' =>$row->subscription_type,
			'cart_id' => $cartsInsertedId,
			'currency' => $currency,
			'payment_link' => $paymentLink,
			'payment_link_id' => $payment_orderId
		]);


		if($paymentmodeselector=='Payment Link'){
			$coins_update = 0;
			$actual_coins_update = 0;
			$start_date = date("Y-m-d", strtotime($request->cvaccess_startdate));
			$end_date = date("Y-m-d", strtotime($request->cvaccess_enddate));
		}else{
			$actual_coins = DB::table('cart_transaction_history')->where('id','=', $cartsInsertedIdHistoryID)->first();
			// $actual_coins = $update_coins-$company_coins->coins;
		   
			$actual_coins_update = $actual_coins->credit;
			$coins_update = $update_coins;
			$start_date = date("Y-m-d", strtotime($request->cvaccess_startdate));
			$end_date = date("Y-m-d", strtotime($request->cvaccess_enddate));
			
			DB::table('company_coins')->where('event_type','=',$topUptype)->where('company_id','=',$company_id)->update(['coins'=>$coins_update,'actual_coins'=>$actual_coins_update,'start_date'=>$start_date,'coin_order_id'=>$coin_order_id,'end_date'=>$end_date,'updated_at'=>date("Y-m-d H:i:s")]);

			 $razorpay_start_date = ($start_date) ? Carbon::parse($start_date)->toIso8601String() : null;
             $razorpay_end_date = ($end_date) ? Carbon::parse($end_date)->toIso8601String() : null;

			 $company_coins_up_elastic = [
                'coins'=>$coins_update,
                'actual_coins'=>$actual_coins_update,
                'start_date'=>$razorpay_start_date,
                'end_date'=>$razorpay_end_date,
                'coin_order_id'=>$coin_order_id,
                'updated_at'=>now()->toIso8601String()	
            ];
            $params = [
                'index' => ELASTIC_COMPANY_COINS,
                'conflicts' => 'proceed', // avoid version conflict failures
                'body' => [
                    'script' => [
                        'lang'   => 'painless',
                        'source' => "
                            ctx._source.coins    = params.coins;
                            ctx._source.actual_coins           = params.actual_coins;
                            ctx._source.start_date         = params.start_date;
                            ctx._source.end_date            = params.end_date;
                            ctx._source.coin_order_id     = params.coin_order_id;
                            ctx._source.updated_at       = params.updated_at;
                        ",
                        'params' => $company_coins_up_elastic
                    ],
                    'query' => [
                        'bool' => [
                            'must' => [
                                ['term' => ['company_id' => $company_id]],
                                ['term' => ['event_type' => $topUptype]],
                            ]
                        ]
                    ]
                ]
            ];
            // 🚀 Execute update
            $response = $this->elasticService->getClient()->updateByQuery($params);

			
		}


		if($company_coins){

			$coin_transection_logs = [
				'coin_order_id'=>$coin_order_id,
				'from_company_id'=>$company_id,
				'company_id'=>$company_id,
				'event_by'=>1,
				'event'=> $coinEvents->title. " ",
				"currency" =>$currency,
				'amount'=>$price,
				'coins'=>$coins_update,
				'remain_coins'=>0,
				'actual_coins'=>$actual_coins_update,
				'transection_type'=>'credit',
				'settlement_coins'=>0,
				'event_type'=>$topUptype,
				'payment_link_id' => $payment_orderId,
				'start_date'=>$start_date,
				'end_date'=>$end_date,
				"payment_orderId" => $payment_orderId,
				'created_at'=>date("Y-m-d H:i:s"),
				'updated_at'=>date("Y-m-d H:i:s")	
		];

		$transection_logsId = DB::table('coin_transection_logs')->insertGetId($coin_transection_logs);

		$response = $this->elasticService->getClient()->update([
                                'index' => ELASTIC_COIN_TRANSECTION_LOGS,
                                'id'    => $transection_logsId,
                                'retry_on_conflict' => 5,
                                'body'  => [
                                    'doc' => [ 
										'coin_order_id'=>$coin_order_id,
										'from_company_id'=>$company_id,
										'company_id'=>$company_id,
										'event_by'=>1,
										'event'=> $coinEvents->title. " ",
										"currency" =>$currency,
										'amount'=>$price,
										'coins'=>$coins_update,
										'remain_coins'=>0,
										'actual_coins'=>$actual_coins_update,
										'transection_type'=>'credit',
										'settlement_coins'=>0,
										'event_type'=>$topUptype,
										'payment_link_id' => $payment_orderId,
                                        "payment_orderId" => $payment_orderId,
                                        'start_date' => ($start_date) ? Carbon::parse($start_date)->toIso8601String() : null,
                                        'end_date' => ($end_date) ? Carbon::parse($end_date)->toIso8601String()  : null,
                                        'created_at' => now()->toIso8601String(),
                                        'updated_at' => now()->toIso8601String()
                                    ],
                                    'doc_as_upsert' => true,
                                    'detect_noop' => false
                                ],
                    ]);


		if(!DB::table('billing_address_companies')->where('company_id','=',$company_id)->exists()){

            $clients  = DB::table('clients')->leftJoin('companies', 'companies.id', '=', 'clients.company_id')
            ->select('clients.id','companies.company_name','users.name')        
            ->leftJoin('users','users.company_id','=','companies.id',)
            ->where('clients.company_id', '=', $company_id)
            ->where('clients.self_company', '=', 1)
            ->first();
    
            $locInfo = DB::table('client_location')
                ->leftJoin('countries', 'countries.id', '=', 'client_location.country_id')
                ->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')
                ->leftJoin('state_list', 'state_list.id', '=', 'client_location.state')
                ->select(
                    'client_location.id',
                    'client_location.city',
                    'client_location.city_id',
                    'client_location.state as state_id',
                    'client_location.address',
                    'client_location.postcode',
                    'client_location.country_id',
                    'city_list.city as city_name',
                    'state_list.state as state_name',
                    'countries.country_name'
                )
                ->where('countries.id', $company_id)
                ->first();
            
            $phone =  auth()->user()->mobile;
              
            $company_name  = @$clients->company_name;
            $user_name  = @$clients->name;
            $nameParts = explode(' ', $user_name);
            $firstName = $nameParts[0];
            $lastName = isset($nameParts[1]) ? $nameParts[1] : '';
            
               $locArr  = '';
                if($locInfo){
                    $city_name = @$locInfo->city;
                    
                    $locArr = ucwords(@$locInfo->address) . ' ' . ucwords($city_name) . ' , ' . ucwords(@$locInfo->state_name) . ' , ' . ucwords(@$locInfo->country_name);
                }




            DB::table('billing_address_companies')->insert([
                'company_id' => $company_id,
                'first_name' =>$firstName,
                'last_name' => $lastName,
                'phone' => $phone,
                'street' => $locArr,
                'organization' => $company_name
            ]);
        }



		$billing_address_companies = DB::table('billing_address_companies')->where('company_id','=',$company_id)->first();
		$sold_by_address = DB::table('sold_by_address')->first();
        if($billing_address_companies && $cartsInsertedIdHistoryID){

            DB::table('billing_address_by_orders')->insert([
                'cart_transaction_id' => $cartsInsertedIdHistoryID,
                'company_id' => $company_id,
                'country_id' => ($billing_address_companies) ? $billing_address_companies->country_id : '',
                'first_name' => ($billing_address_companies) ? $billing_address_companies->first_name : '',
                'last_name' => ($billing_address_companies) ? $billing_address_companies->last_name : '',
                'phone' => ($billing_address_companies) ? $billing_address_companies->phone : '',
                'building' => ($billing_address_companies) ? $billing_address_companies->building : '',
                'street' => ($billing_address_companies) ? $billing_address_companies->street : '',
                'state_id' =>($billing_address_companies) ? $billing_address_companies->state_id : '',
                'city' => ($billing_address_companies) ? $billing_address_companies->city : '',
                'postal_code' => ($billing_address_companies) ? $billing_address_companies->postal_code : '',
                'organization' => ($billing_address_companies) ? $billing_address_companies->organization : '',
				'pan_no' =>($billing_address_companies) ? $billing_address_companies->pan_no : '',
                'gstin' =>($billing_address_companies) ? $billing_address_companies->gstin : '',
                'sold_country_id' => ($sold_by_address) ? $sold_by_address->country_id : '',
                'sold_first_name' => ($sold_by_address) ? $sold_by_address->first_name : '',
                'sold_last_name' => ($sold_by_address) ? $sold_by_address->last_name : '',
                'sold_phone' => ($sold_by_address) ? $sold_by_address->phone : '',
                'sold_building' => ($sold_by_address) ? $sold_by_address->building : '',
                'sold_street' => ($sold_by_address) ? $sold_by_address->street : '',
                'sold_state_id' =>($sold_by_address) ? $sold_by_address->state_id : '',
                'sold_city' => ($sold_by_address) ? $sold_by_address->city : '',
                'sold_postal_code' => ($sold_by_address) ? $sold_by_address->postal_code : '',
                'sold_organization' => ($sold_by_address) ? $sold_by_address->organization : '',
                'sold_gstin' =>($sold_by_address) ? $sold_by_address->gstin : '',
                'sold_pan_no' =>($sold_by_address) ? $sold_by_address->pan_no : ''
            ]);

        }




			$url		=	url('super-admin/company/');

			

				return response()->json([
					'message' => '',
					'status' => $status,
					'url' => $url
				]);    
		}

	}
	private function createRazorpayPaymentLink($apiKey, $apiSecret, $paymentLinkData)
    {
        $url = 'https://api.razorpay.com/v1/payment_links';
    
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $apiKey . ':' . $apiSecret);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($paymentLinkData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);
    
        $response = curl_exec($ch);
        $paymentLinkResponse = json_decode($response, true);
    
        curl_close($ch);
    
        return $paymentLinkResponse;
    }
   
	public function saveInvoice(Request $request)
	{
		
		//echo '<pre>'; print_r($_REQUEST); exit; 
		
		$company_id				=	'';
		$company_id				=	$request->company_id;		
		$companyRow 			=	DB::table('companies')->select('*')->where('companies.id', $company_id)->first();

		//$companyRow->company_type
		
		$serviceArr				=	$invoice_id	= $invoicePrimaryId = '';
		$serviceArr				=	$request->serviceArr;
		
		$t						=	date('Y-m-d H:i:s');
		$invoice_id				=	"XXX-".strtotime($t)."-XXX";
		$invoice_check = $request->invoice_check;
		
		if($invoice_check){
			$maxId = DB::table('invoices')->max('id');
			$pre_fixed = DB::table('pre_fixed_settings')->where('type','invoice')->first();
			$number = ($maxId > 0)? $maxId+1 : 1; 
			$padded_number = str_pad($number, 5, '0', STR_PAD_LEFT);
			$invoices_no =  $pre_fixed->pre_fixed.$padded_number;
			DB::table('invoices')->insert(['invoice_no'=>$invoices_no,'status'=>1]);
			$invoice_id    				=	$invoices_no; 
			$invoicePrimaryId  = DB::getPdo()->lastInsertId();
		}

		$companyRow 			=	DB::table('companies')->select('*')->where('companies.id', $company_id)->first();
		
		$order_val				=	array('company_id' => @$company_id, 'company_type' => @$companyRow->company_type);
		$invoice_val			=	array('company_id' => @$company_id, 'invoice_id' => @$invoice_id, 'invoicePrimaryId' => @$invoicePrimaryId, 'company_type' => @$companyRow->company_type);
		$active_val  			=	array('company_type' => @$companyRow->company_type);
		
		//package_order
		
		$subtotal		=	0;
		if(!empty($serviceArr)){
			foreach($serviceArr as $sArr){
				if($sArr['alt_item'] == 'cvaccess') { 

							$order_val['cvaccess_startdate'] 	=	$invoice_val['cvaccess_startdate'] 	=  	@$sArr['startdate'];
							$order_val['cvaccess_enddate'] 		=	$invoice_val['cvaccess_enddate'] 	=  	@$sArr['enddate'];
							$order_val['cvaccess_status'] 		=	$invoice_val['cvaccess_status'] 	= 	@$sArr['status'];
							$order_val['cvaccess_quantity'] 	=	$invoice_val['cvaccess_quantity'] 	=  	@$sArr['quantity'];
							$order_val['cvaccess_price'] 		=	$invoice_val['cvaccess_price'] 		=  	@$sArr['price'];
							$order_val['cvaccess_totalprice'] 	=	$invoice_val['cvaccess_totalprice'] = 	@$sArr['finalprice'];

							$active_val['cvaccess_startdate']	=	@$sArr['startdate'];
							$active_val['cvaccess_enddate']		=	@$sArr['enddate'];
							$active_val['cvaccess_status']		=	@$sArr['status'];
							$active_val['cvaccess_quantity']	=	@$sArr['quantity'];
							$active_val['cvaccess_used']		= 	0;
							$active_val['cvaccess_balance']		=	@$sArr['quantity'];	
																				
							$subtotal							=  $subtotal + @$sArr['finalprice'];												 
																	
				}elseif($sArr['alt_item'] == 'users') {

							$order_val['users_startdate'] 		=	$invoice_val['users_startdate'] 	=  	@$sArr['startdate'];
							$order_val['users_enddate'] 		=	$invoice_val['users_enddate'] 		=  	@$sArr['enddate'];
							$order_val['users_status'] 			=	$invoice_val['users_status'] 		= 	@$sArr['status'];
							$order_val['users_quantity'] 		=	$invoice_val['users_quantity'] 		=  	@$sArr['quantity'];
							$order_val['users_price'] 			=	$invoice_val['users_price'] 		=  	@$sArr['price'];
							$order_val['users_totalprice'] 		=	$invoice_val['users_totalprice'] 	= 	@$sArr['finalprice'];

							$active_val['users_startdate']		=	@$sArr['startdate'];
							$active_val['users_enddate']		=	@$sArr['enddate'];
							$active_val['users_status']			=	@$sArr['status'];
							$active_val['users_quantity']		=	@$sArr['quantity'];
							$active_val['users_used']			= 	0;
							$active_val['users_balance']		=	@$sArr['quantity'];	
																				
							$subtotal							=  $subtotal + @$sArr['finalprice'];	

				}elseif($sArr['alt_item'] == 'new_bench_client_request') {

					$order_val['newbenchclientrequest_startdate'] 	=	$invoice_val['newbenchclientrequest_startdate'] 	=  	@$sArr['startdate'];
					$order_val['newbenchclientrequest_enddate'] 	=	$invoice_val['newbenchclientrequest_enddate'] 	=  	@$sArr['enddate'];
					$order_val['newbenchclientrequest_status'] 		=	$invoice_val['newbenchclientrequest_status'] 		= 	@$sArr['status'];
					$order_val['newbenchclientrequest_quantity'] 	=	$invoice_val['newbenchclientrequest_quantity'] 		=  	@$sArr['quantity'];
					$order_val['newbenchclientrequest_price'] 		=	$invoice_val['newbenchclientrequest_price'] 		=  	@$sArr['price'];
					$order_val['newbenchclientrequest_totalprice'] 	=	$invoice_val['newbenchclientrequest_totalprice'] 	= 	@$sArr['finalprice'];

					$active_val['newbenchclientrequest_startdate']	=	@$sArr['startdate'];
					$active_val['newbenchclientrequest_enddate']	=	@$sArr['enddate'];
					$active_val['newbenchclientrequest_status']		=	@$sArr['status'];
					$active_val['newbenchclientrequest_quantity']	=	@$sArr['quantity'];
					$active_val['newbenchclientrequest_used']		= 	0;
					$active_val['newbenchclientrequest_balance']	=	@$sArr['quantity'];	
																		
					$subtotal										=	$subtotal + @$sArr['finalprice'];
				}elseif($sArr['alt_item'] == 'emails') {
					$order_val['emails_startdate'] 	=	$invoice_val['emails_startdate'] 	=  	@$sArr['startdate'];
					$order_val['emails_enddate'] 	=	$invoice_val['emails_enddate'] 		=  	@$sArr['enddate'];
					$order_val['emails_status'] 	=	$invoice_val['emails_status'] 		= 	@$sArr['status'];
					$order_val['emails_quantity'] 	=	$invoice_val['emails_quantity'] 	=  	@$sArr['quantity'];
					$order_val['emails_price'] 		=	$invoice_val['emails_price'] 		=  	@$sArr['price'];
					$order_val['emails_totalprice'] =	$invoice_val['emails_totalprice'] 	= 	@$sArr['finalprice'];

					$active_val['emails_startdate']	=	@$sArr['startdate'];
					$active_val['emails_enddate']	=	@$sArr['enddate'];
					$active_val['emails_status']	=	@$sArr['status'];
					$active_val['emails_quantity']	=	@$sArr['quantity'];
					$active_val['emails_used']		= 	0;
					$active_val['emails_balance']	=	@$sArr['quantity'];	
																		
					$subtotal						=	$subtotal + @$sArr['finalprice'];

				}elseif($sArr['alt_item'] == 'oldcvaccess') {

					$order_val['oldcvaccess_startdate'] 	=	$invoice_val['oldcvaccess_startdate'] 	=  	@$sArr['startdate'];
					$order_val['oldcvaccess_enddate'] 		=	$invoice_val['oldcvaccess_enddate'] 	=  	@$sArr['enddate'];
					$order_val['oldcvaccess_status'] 		=	$invoice_val['oldcvaccess_status'] 		= 	@$sArr['status'];
					$order_val['oldcvaccess_quantity'] 		=	$invoice_val['oldcvaccess_quantity'] 	=  	@$sArr['quantity'];
					$order_val['oldcvaccess_price'] 		=	$invoice_val['oldcvaccess_price'] 		=  	@$sArr['price'];
					$order_val['oldcvaccess_totalprice'] 	=	$invoice_val['oldcvaccess_totalprice'] 	= 	@$sArr['finalprice'];

					$active_val['oldcvaccess_startdate']	=	@$sArr['startdate'];
					$active_val['oldcvaccess_enddate']		=	@$sArr['enddate'];
					$active_val['oldcvaccess_status']		=	@$sArr['status'];
					$active_val['oldcvaccess_quantity']		=	@$sArr['quantity'];
					$active_val['oldcvaccess_used']			= 	0;
					$active_val['oldcvaccess_balance']		=	@$sArr['quantity'];	
																		
					$subtotal								=	$subtotal + @$sArr['finalprice'];

				}elseif($sArr['alt_item'] == 'new_client_request') {

					$order_val['newclientrequest_startdate'] 	=	$invoice_val['newclientrequest_startdate'] 	=  	@$sArr['startdate'];
					$order_val['newclientrequest_enddate'] 		=	$invoice_val['newclientrequest_enddate'] 	=  	@$sArr['enddate'];
					$order_val['newclientrequest_status'] 		=	$invoice_val['newclientrequest_status'] 	= 	@$sArr['status'];
					$order_val['newclientrequest_quantity'] 	=	$invoice_val['newclientrequest_quantity'] 	=  	@$sArr['quantity'];
					$order_val['newclientrequest_price'] 		=	$invoice_val['newclientrequest_price'] 		=  	@$sArr['price'];
					$order_val['newclientrequest_totalprice'] 	=	$invoice_val['newclientrequest_totalprice'] = 	@$sArr['finalprice'];

					$active_val['newclientrequest_startdate']	=	@$sArr['startdate'];
					$active_val['newclientrequest_enddate']		=	@$sArr['enddate'];
					$active_val['newclientrequest_status']		=	@$sArr['status'];
					$active_val['newclientrequest_quantity']	=	@$sArr['quantity'];
					$active_val['newclientrequest_used']		= 	0;
					$active_val['newclientrequest_balance']		=	@$sArr['quantity'];	
																		
					$subtotal									=  $subtotal + @$sArr['finalprice'];
																
				}elseif($sArr['alt_item'] == 'new_job_request') {

					$order_val['newjobrequest_startdate'] 	=	$invoice_val['newjobrequest_startdate'] 	=  	@$sArr['startdate'];
					$order_val['newjobrequest_enddate'] 		=	$invoice_val['newjobrequest_enddate'] 	=  	@$sArr['enddate'];
					$order_val['newjobrequest_status'] 		=	$invoice_val['newjobrequest_status'] 		= 	@$sArr['status'];
					$order_val['newjobrequest_quantity'] 	=	$invoice_val['newjobrequest_quantity'] 		=  	@$sArr['quantity'];
					$order_val['newjobrequest_price'] 		=	$invoice_val['newjobrequest_price'] 		=  	@$sArr['price'];
					$order_val['newjobrequest_totalprice'] 	=	$invoice_val['newjobrequest_totalprice'] 	= 	@$sArr['finalprice'];

					$active_val['newjobrequest_startdate']	=	@$sArr['startdate'];
					$active_val['newjobrequest_enddate']	=	@$sArr['enddate'];
					$active_val['newjobrequest_status']		=	@$sArr['status'];
					$active_val['newjobrequest_quantity']	=	@$sArr['quantity'];
					$active_val['newjobrequest_used']		= 	0;
					$active_val['newjobrequest_balance']	=	@$sArr['quantity'];	
																		
					$subtotal								=	$subtotal + @$sArr['finalprice'];
						
				}elseif($sArr['alt_item'] == 'boostkeywords') {

					$order_val['boostkeywords_startdate'] 		=	$invoice_val['boostkeywords_startdate'] 	=  	@$sArr['startdate'];
					$order_val['boostkeywords_enddate'] 		=	$invoice_val['boostkeywords_enddate'] 		=  	@$sArr['enddate'];
					$order_val['boostkeywords_status'] 			=	$invoice_val['boostkeywords_status'] 		= 	@$sArr['status'];
					$order_val['boostkeywords_quantity'] 		=	$invoice_val['boostkeywords_quantity'] 		=  	@$sArr['quantity'];

					$order_val['boostkeywords_price'] 			=	$invoice_val['boostkeywords_price'] 		=  	@$sArr['price'];
					$order_val['boostkeywords_totalprice'] 		=	$invoice_val['boostkeywords_totalprice'] 	= 	@$sArr['finalprice'];
					$string_version 							=	implode(' , ', @$sArr['keywords']);
					$order_val['boostkeywords_text'] 			=	$invoice_val['boostkeywords_text'] 			=  	@$string_version;

					$active_val['boostkeywords_startdate']		=	@$sArr['startdate'];
					$active_val['boostkeywords_enddate']		=	@$sArr['enddate'];
					$active_val['boostkeywords_status']			=	@$sArr['status'];
					$active_val['boostkeywords_text']			=	@$string_version;

					$active_val['boostkeywords_quantity']		=	@$sArr['quantity'];
					$active_val['boostkeywords_used']			= 	0;
					$active_val['boostkeywords_balance']		=	@$sArr['quantity'];	

					//$active_val['oldcvaccess_used']				= 	0;
					//$active_val['oldcvaccess_balance']			=	@$sArr['quantity'];	
																		
					$subtotal									=	$subtotal + @$sArr['finalprice'];


				}elseif($sArr['alt_item'] == 'InternalDatabaseaccess') {

					$order_val['internaldatabaseaccess_status']=$invoice_val['internaldatabaseaccess_status']=	@$sArr['status'];
					$order_val['internaldatabaseaccess_quantity']=$invoice_val['internaldatabaseaccess_quantity']=@$sArr['quantity'];
					$order_val['internaldatabaseaccess_price']=	$invoice_val['internaldatabaseaccess_price']= @$sArr['price'];
					$order_val['internaldatabaseaccess_totalprice']=$invoice_val['internaldatabaseaccess_totalprice']=@$sArr['finalprice'];
						
					$active_val['internaldatabaseaccess_status']		=	@$sArr['status'];	
					$active_val['internaldatabaseaccess_quantity']		=	@$sArr['quantity'];
					$active_val['internaldatabaseaccess_used']			= 	0;
					$active_val['internaldatabaseaccess_balance']		=	@$sArr['quantity'];	
					$subtotal									=	$subtotal + @$sArr['finalprice'];
						
				}elseif($sArr['alt_item'] == 'Interviewtracker') {

					$order_val['interviewtracker_status']=$invoice_val['interviewtracker_status']=	@$sArr['status'];
					$order_val['interviewtracker_quantity']=$invoice_val['interviewtracker_quantity']=@$sArr['quantity'];
					$order_val['interviewtracker_price']=	$invoice_val['interviewtracker_price']= @$sArr['price'];
					$order_val['interviewtracker_totalprice']=$invoice_val['interviewtracker_totalprice']=@$sArr['finalprice'];
						
					$active_val['interviewtracker_status']		=	@$sArr['status'];	
					$active_val['interviewtracker_quantity']		=	@$sArr['quantity'];
					$active_val['interviewtracker_used']			= 	0;
					$active_val['interviewtracker_balance']		=	@$sArr['quantity'];	
					$subtotal									=	$subtotal + @$sArr['finalprice'];
						
				}elseif($sArr['alt_item'] == 'Offertracker') {

					$order_val['offertracker_status']=$invoice_val['offertracker_status']=	@$sArr['status'];
					$order_val['offertracker_quantity']=$invoice_val['offertracker_quantity']=@$sArr['quantity'];
					$order_val['offertracker_price']=	$invoice_val['offertracker_price']= @$sArr['price'];
					$order_val['offertracker_totalprice']=$invoice_val['offertracker_totalprice']=@$sArr['finalprice'];
						
					$active_val['offertracker_status']		=	@$sArr['status'];	
					$active_val['offertracker_quantity']		=	@$sArr['quantity'];
					$active_val['offertracker_used']			= 	0;
					$active_val['offertracker_balance']		=	@$sArr['quantity'];	
					$subtotal									=	$subtotal + @$sArr['finalprice'];
						
				}elseif($sArr['alt_item'] == 'Joiningtracker') {

					$order_val['joiningtracker_status']=$invoice_val['joiningtracker_status']=	@$sArr['status'];
					$order_val['joiningtracker_quantity']=$invoice_val['joiningtracker_quantity']=@$sArr['quantity'];
					$order_val['joiningtracker_price']=	$invoice_val['joiningtracker_price']= @$sArr['price'];
					$order_val['joiningtracker_totalprice']=$invoice_val['joiningtracker_totalprice']=@$sArr['finalprice'];
						
					$active_val['joiningtracker_status']		=	@$sArr['status'];	
					$active_val['joiningtracker_quantity']		=	@$sArr['quantity'];
					$active_val['joiningtracker_used']			= 	0;
					$active_val['joiningtracker_balance']		=	@$sArr['quantity'];	
					$subtotal									=	$subtotal + @$sArr['finalprice'];
						
				}elseif($sArr['alt_item'] == 'PermissionforSHaringCVtoclienys') {

					$order_val['PermissionforSHaringCVtoclienys_status']=$invoice_val['PermissionforSHaringCVtoclienys_status']=	@$sArr['status'];
					$order_val['PermissionforSHaringCVtoclienys_quantity']=$invoice_val['PermissionforSHaringCVtoclienys_quantity']=@$sArr['quantity'];
					$order_val['PermissionforSHaringCVtoclienys_price']=	$invoice_val['PermissionforSHaringCVtoclienys_price']= @$sArr['price'];
					$order_val['PermissionforSHaringCVtoclienys_totalprice']=$invoice_val['PermissionforSHaringCVtoclienys_totalprice']=@$sArr['finalprice'];
						
					$active_val['PermissionforSHaringCVtoclienys_status']		=	@$sArr['status'];	
					$active_val['PermissionforSHaringCVtoclienys_quantity']		=	@$sArr['quantity'];
					$active_val['PermissionforSHaringCVtoclienys_used']			= 	0;
					$active_val['PermissionforSHaringCVtoclienys_balance']		=	@$sArr['quantity'];	
					$subtotal									=	$subtotal + @$sArr['finalprice'];
						
				}elseif($sArr['alt_item'] == 'PermamentRecruitmentLeadIndia') {

					$order_val['PermamentRecruitmentLeadIndia_status']=$invoice_val['PermamentRecruitmentLeadIndia_status']=	@$sArr['status'];
					$order_val['PermamentRecruitmentLeadIndia_quantity']=$invoice_val['PermamentRecruitmentLeadIndia_quantity']=@$sArr['quantity'];
					$order_val['PermamentRecruitmentLeadIndia_price']=	$invoice_val['PermamentRecruitmentLeadIndia_price']= @$sArr['price'];
					$order_val['PermamentRecruitmentLeadIndia_totalprice']=$invoice_val['PermamentRecruitmentLeadIndia_totalprice']=@$sArr['finalprice'];
						
					$active_val['PermamentRecruitmentLeadIndia_status']		=	@$sArr['status'];	
					$active_val['PermamentRecruitmentLeadIndia_quantity']		=	@$sArr['quantity'];
					$active_val['PermamentRecruitmentLeadIndia_used']			= 	0;
					$active_val['PermamentRecruitmentLeadIndia_balance']		=	@$sArr['quantity'];	
					$subtotal									=	$subtotal + @$sArr['finalprice'];
						
				}elseif($sArr['alt_item'] == 'PermamentRecruitmentLeadInternational') {

					$order_val['PermamentRecruitmentLeadInternational_status']=$invoice_val['PermamentRecruitmentLeadInternational_status']=	@$sArr['status'];
					$order_val['PermamentRecruitmentLeadInternational_quantity']=$invoice_val['PermamentRecruitmentLeadInternational_quantity']=@$sArr['quantity'];
					$order_val['PermamentRecruitmentLeadInternational_price']=	$invoice_val['PermamentRecruitmentLeadInternational_price']= @$sArr['price'];
					$order_val['PermamentRecruitmentLeadInternational_totalprice']=$invoice_val['PermamentRecruitmentLeadInternational_totalprice']=@$sArr['finalprice'];
						
					$active_val['PermamentRecruitmentLeadInternational_status']		=	@$sArr['status'];	
					$active_val['PermamentRecruitmentLeadInternational_quantity']		=	@$sArr['quantity'];
					$active_val['PermamentRecruitmentLeadInternational_used']			= 	0;
					$active_val['PermamentRecruitmentLeadInternational_balance']		=	@$sArr['quantity'];	
					$subtotal									=	$subtotal + @$sArr['finalprice'];
						
				}elseif($sArr['alt_item'] == 'ContractRecruitmentLeadIndia') {

					$order_val['ContractRecruitmentLeadIndia_status']=$invoice_val['ContractRecruitmentLeadIndia_status']=	@$sArr['status'];
					$order_val['ContractRecruitmentLeadIndia_quantity']=$invoice_val['ContractRecruitmentLeadIndia_quantity']=@$sArr['quantity'];
					$order_val['ContractRecruitmentLeadIndia_price']=	$invoice_val['ContractRecruitmentLeadIndia_price']= @$sArr['price'];
					$order_val['ContractRecruitmentLeadIndia_totalprice']=$invoice_val['ContractRecruitmentLeadIndia_totalprice']=@$sArr['finalprice'];
						
					$active_val['ContractRecruitmentLeadIndia_status']		=	@$sArr['status'];	
					$active_val['ContractRecruitmentLeadIndia_quantity']		=	@$sArr['quantity'];
					$active_val['ContractRecruitmentLeadIndia_used']			= 	0;
					$active_val['ContractRecruitmentLeadIndia_balance']		=	@$sArr['quantity'];	
					$subtotal									=	$subtotal + @$sArr['finalprice'];
						
				}elseif($sArr['alt_item'] == 'ContractRecruitmentLeadInternational') {

					$order_val['ContractRecruitmentLeadInternational_status']=$invoice_val['ContractRecruitmentLeadInternational_status']=	@$sArr['status'];
					$order_val['ContractRecruitmentLeadInternational_quantity']=$invoice_val['ContractRecruitmentLeadInternational_quantity']=@$sArr['quantity'];
					$order_val['ContractRecruitmentLeadInternational_price']=	$invoice_val['ContractRecruitmentLeadInternational_price']= @$sArr['price'];
					$order_val['ContractRecruitmentLeadInternational_totalprice']=$invoice_val['ContractRecruitmentLeadInternational_totalprice']=@$sArr['finalprice'];
						
					$active_val['ContractRecruitmentLeadInternational_status']		=	@$sArr['status'];	
					$active_val['ContractRecruitmentLeadInternational_quantity']		=	@$sArr['quantity'];
					$active_val['ContractRecruitmentLeadInternational_used']			= 	0;
					$active_val['ContractRecruitmentLeadInternational_balance']		=	@$sArr['quantity'];	
					$subtotal									=	$subtotal + @$sArr['finalprice'];
						
				}elseif($sArr['alt_item'] == 'BenchSalesIndia') {

					$order_val['BenchSalesIndia_status']=$invoice_val['BenchSalesIndia_status']=	@$sArr['status'];
					$order_val['BenchSalesIndia_quantity']=$invoice_val['BenchSalesIndia_quantity']=@$sArr['quantity'];
					$order_val['BenchSalesIndia_price']=	$invoice_val['BenchSalesIndia_price']= @$sArr['price'];
					$order_val['BenchSalesIndia_totalprice']=$invoice_val['BenchSalesIndia_totalprice']=@$sArr['finalprice'];
						
					$active_val['BenchSalesIndia_status']		=	@$sArr['status'];	
					$active_val['BenchSalesIndia_quantity']		=	@$sArr['quantity'];
					$active_val['BenchSalesIndia_used']			= 	0;
					$active_val['BenchSalesIndia_balance']		=	@$sArr['quantity'];	
					$subtotal									=	$subtotal + @$sArr['finalprice'];
						
				}elseif($sArr['alt_item'] == 'BenchSalesInternational') {

					$order_val['BenchSalesInternational_status']=$invoice_val['BenchSalesInternational_status']=	@$sArr['status'];
					$order_val['BenchSalesInternational_quantity']=$invoice_val['BenchSalesInternational_quantity']=@$sArr['quantity'];
					$order_val['BenchSalesInternational_price']=	$invoice_val['BenchSalesInternational_price']= @$sArr['price'];
					$order_val['BenchSalesInternational_totalprice']=$invoice_val['BenchSalesInternational_totalprice']=@$sArr['finalprice'];
						
					$active_val['BenchSalesInternational_status']		=	@$sArr['status'];	
					$active_val['BenchSalesInternational_quantity']		=	@$sArr['quantity'];
					$active_val['BenchSalesInternational_used']			= 	0;
					$active_val['BenchSalesInternational_balance']		=	@$sArr['quantity'];	
					$subtotal									=	$subtotal + @$sArr['finalprice'];
						
				}elseif($sArr['alt_item'] == 'BenchClientAcccessIndia') {

					$order_val['BenchClientAcccessIndia_status']=$invoice_val['BenchClientAcccessIndia_status']=	@$sArr['status'];
					$order_val['BenchClientAcccessIndia_quantity']=$invoice_val['BenchClientAcccessIndia_quantity']=@$sArr['quantity'];
					$order_val['BenchClientAcccessIndia_price']=	$invoice_val['BenchClientAcccessIndia_price']= @$sArr['price'];
					$order_val['BenchClientAcccessIndia_totalprice']=$invoice_val['BenchClientAcccessIndia_totalprice']=@$sArr['finalprice'];
						
					$active_val['BenchClientAcccessIndia_status']		=	@$sArr['status'];	
					$active_val['BenchClientAcccessIndia_quantity']		=	@$sArr['quantity'];
					$active_val['BenchClientAcccessIndia_used']			= 	0;
					$active_val['BenchClientAcccessIndia_balance']		=	@$sArr['quantity'];	
					$subtotal									=	$subtotal + @$sArr['finalprice'];
						
				}elseif($sArr['alt_item'] == 'BenchClientAccessInternational') {

					$order_val['BenchClientAccessInternational_status']=$invoice_val['BenchClientAccessInternational_status']=	@$sArr['status'];
					$order_val['BenchClientAccessInternational_quantity']=$invoice_val['BenchClientAccessInternational_quantity']=@$sArr['quantity'];
					$order_val['BenchClientAccessInternational_price']=	$invoice_val['BenchClientAccessInternational_price']= @$sArr['price'];
					$order_val['BenchClientAccessInternational_totalprice']=$invoice_val['BenchClientAccessInternational_totalprice']=@$sArr['finalprice'];
						
					$active_val['BenchClientAccessInternational_status']		=	@$sArr['status'];	
					$active_val['BenchClientAccessInternational_quantity']		=	@$sArr['quantity'];
					$active_val['BenchClientAccessInternational_used']			= 	0;
					$active_val['BenchClientAccessInternational_balance']		=	@$sArr['quantity'];	
					$subtotal									=	$subtotal + @$sArr['finalprice'];
						
				}elseif($sArr['alt_item'] == 'BoostKeywordsIndia') {

					$order_val['BoostKeywordsIndia_status']=$invoice_val['BoostKeywordsIndia_status']=	@$sArr['status'];
					$order_val['BoostKeywordsIndia_quantity']=$invoice_val['BoostKeywordsIndia_quantity']=@$sArr['quantity'];
					$order_val['BoostKeywordsIndia_price']=	$invoice_val['BoostKeywordsIndia_price']= @$sArr['price'];
					$order_val['BoostKeywordsIndia_totalprice']=$invoice_val['BoostKeywordsIndia_totalprice']=@$sArr['finalprice'];
					$string_version 							=	"";//implode(' , ', @$sArr['keywords']);
					$order_val['boostkeywords_text'] 			=	$invoice_val['boostkeywords_text'] 			=  	@$string_version;
						
					$active_val['BoostKeywordsIndia_status']		=	@$sArr['status'];	
					$active_val['BoostKeywordsIndia_quantity']		=	@$sArr['quantity'];
					$active_val['BoostKeywordsIndia_used']			= 	0;
					$active_val['BoostKeywordsIndia_balance']		=	@$sArr['quantity'];	
					$subtotal									=	$subtotal + @$sArr['finalprice'];
					$active_val['boostkeywords_text']			=	@$string_version;
						
				}elseif($sArr['alt_item'] == 'BoostKeywordsInternational') {

					$order_val['BoostKeywordsInternational_status']=$invoice_val['BoostKeywordsInternational_status']=	@$sArr['status'];
					$order_val['BoostKeywordsInternational_quantity']=$invoice_val['BoostKeywordsInternational_quantity']=@$sArr['quantity'];
					$order_val['BoostKeywordsInternational_price']=	$invoice_val['BoostKeywordsInternational_price']= @$sArr['price'];
					$order_val['BoostKeywordsInternational_totalprice']=$invoice_val['BoostKeywordsInternational_totalprice']=@$sArr['finalprice'];
					$string_version 							=	"";//implode(' , ', @$sArr['keywords']);
					$order_val['boostkeywords_text'] 			=	$invoice_val['boostkeywords_text'] 			=  	@$string_version;
						
					$active_val['BoostKeywordsInternational_status']		=	@$sArr['status'];	
					$active_val['BoostKeywordsInternational_quantity']		=	@$sArr['quantity'];
					$active_val['BoostKeywordsInternational_used']			= 	0;
					$active_val['BoostKeywordsInternational_balance']		=	@$sArr['quantity'];	
					$subtotal									=	$subtotal + @$sArr['finalprice'];
					$active_val['boostkeywords_text']			=	@$string_version;
						
				}elseif($sArr['alt_item'] == 'Limitperdayforleadsconsumptions') {

					$order_val['Limitperdayforleadsconsumptions_status']=$invoice_val['Limitperdayforleadsconsumptions_status']=	@$sArr['status'];
					$order_val['Limitperdayforleadsconsumptions_quantity']=$invoice_val['Limitperdayforleadsconsumptions_quantity']=@$sArr['quantity'];
					$order_val['Limitperdayforleadsconsumptions_price']=	$invoice_val['Limitperdayforleadsconsumptions_price']= @$sArr['price'];
					$order_val['Limitperdayforleadsconsumptions_totalprice']=$invoice_val['Limitperdayforleadsconsumptions_totalprice']=@$sArr['finalprice'];
						
					$active_val['Limitperdayforleadsconsumptions_status']		=	@$sArr['status'];	
					$active_val['Limitperdayforleadsconsumptions_quantity']		=	@$sArr['quantity'];
					$active_val['Limitperdayforleadsconsumptions_used']			= 	0;
					$active_val['Limitperdayforleadsconsumptions_balance']		=	@$sArr['quantity'];	
					$subtotal									=	$subtotal + @$sArr['finalprice'];
						
				}elseif($sArr['alt_item'] == 'discount') {
					$invoice_val['discount'] =	@$sArr['discount'];
				}elseif($sArr['alt_item'] == 'amount_total_paid') {
					$invoice_val['grand_total'] =	@$sArr['amount_total_paid'];
				}elseif($sArr['alt_item'] == 'payment_mode') {
					$invoice_val['payment_mode'] =	@$sArr['payment_mode'];
				}																								
			}
		}
		
		$active_val['package_id'] = $request->packages_id;
		$invoice_val['package_id'] = $request->packages_id;
		$order_val['package_id'] = $request->packages_id;

		$active_val['startdate'] = $request->startdate;
		$active_val['enddate'] = $request->enddate;
		$active_val['packages_status'] = (int) $request->packages_status;
		$active_val['payment_status'] = (int) $request->payment_status;

		$invoice_val['startdate'] = $request->startdate;
		$invoice_val['enddate'] = $request->enddate;
		$invoice_val['packages_status'] = (int) $request->packages_status;
		$invoice_val['payment_status'] = (int) $request->payment_status;

		$order_val['startdate'] = $request->startdate;
		$order_val['enddate'] = $request->enddate;
		$order_val['packages_status'] = (int) $request->packages_status;
		$order_val['payment_status'] = (int) $request->payment_status;

		DB::table('package_order')->insert($order_val);  
		$package_order_id    				=	DB::getPdo()->lastInsertId();
		
		$invoice_val['package_order_id'] 	=	@$package_order_id;	
		$invoice_val['subtotal'] 			=	@$subtotal;				
		DB::table('package_invoice')->insert($invoice_val);  
		$package_invoice_id    				=	DB::getPdo()->lastInsertId();
		
		//echo '<pre>'; print_r($_REQUEST); exit; 

		if(!empty($serviceArr)){
			foreach($serviceArr as $sArr){
				if((int) @$sArr['features_id'] > 0){
					$PackageOrderItem = new PackageOrderItem();
					$PackageOrderItem->package_id = $request->packages_id;
					$PackageOrderItem->package_order_id = $package_order_id;
					$PackageOrderItem->invoice_id = $package_invoice_id;
					$PackageOrderItem->	feature_id = (int) @$sArr['features_id'];
					$PackageOrderItem->qty = @$sArr['quantity'];
					$PackageOrderItem->price = @$sArr['price'];
					$PackageOrderItem->sub_total = @$sArr['finalprice'];
					$PackageOrderItem->status = @$sArr['status'];
					$PackageOrderItem->save();
				}
			}
		}
		
		if(!empty($serviceArr)){
				foreach($serviceArr as $sArr){
						if($sArr['alt_item'] == 'TAX') {
						
								$childArr 		=	@$sArr['label'];
								$per_text 		=	@$sArr['per_text'];
								$per_val 		=	@$sArr['per_val'];
								
								$cnt		=	0;
							 	if(!empty($childArr)){
									foreach($childArr as $childVal){
											
												$values			 = array(
																			'invoice_id' 					=>	@$package_invoice_id, 
																			'tax_name'						=>	@$childVal,
																			'tax_percentage'				=>	@$per_text[$cnt],
																			'tax_amount'					=>	@$per_val[$cnt]
																		);
												DB::table('package_invoice_tax')->insert($values);  
												$cnt++;
					
									}
								}
						}		
				}
		}	
		 
		$active_val['package_invoice_id']		=	@$package_invoice_id;	
		//  dd($request->all());
		if((int) $request->packages_status == 1 || (int) ($request->payment_status) ==1){
			DB::table('package_active')->where('company_id', $company_id)->limit(1)->update($active_val);
		}
		
		if($package_invoice_id > 0){
			// $url		=	url('super-admin/company/'.$company_id);
			$url		=	url('super-admin/company/');
			return Reply::dataOnly(['status' => 'success', 'url' => $url]);
		}else{
			return Reply::dataOnly(['status' => 'fail']);	
		}
	
	}
	
    public function store(Request $request) 
	{
	
		
        $package = new Package();
        $package->name = $request->name;
        $package->description = $request->description;
        $package->monthly_price = $request->monthly_price;
        $package->annual_price = $request->annual_price;
        $package->stripe_monthly_plan_id = $request->stripe_monthly_plan_id;
        $package->stripe_annual_plan_id = $request->stripe_annual_plan_id;
        $package->razorpay_annual_plan_id = $request->razorpay_annual_plan_id;
        $package->razorpay_monthly_plan_id = $request->razorpay_monthly_plan_id;

        if ($request->no_of_job_openings != '') {
            $package->no_of_job_openings = $request->no_of_job_openings;
        } else {
            $package->no_of_job_openings = null;
        }

        if ($request->no_of_candidate_access != '') {
            $package->no_of_candidate_access = $request->no_of_candidate_access;
        } else {
            $package->no_of_candidate_access = null;
        }

        if ($request->career_website) {
            $package->career_website = 1;
        } else {
            $package->career_website = 0;
        }

        if ($request->multiple_roles) {
            $package->multiple_roles = 1;
        } else {
            $package->multiple_roles = 0;
        }

        if ($request->recommended) {
            $package->recommended = 1;
        } else {
            $package->recommended = 0;
        }

        if ($request->status) {
            $package->status = 1;
        } else {
            $package->status = 0;
        }
    
        $package->save();

        return Reply::redirect(route('superadmin.packages.index'), __('messages.createdSuccessfully'));
    }

    public function edit($id) 
	{
        $this->package = Package::findOrfail($id);
        $this->paymentSetting = PaymentSetting::first();
        return view('super-admin.packages.edit', $this->data);
    }

    public function destroy($id) 
	{
        Package::destroy($id);
        return Reply::success(__('messages.recordDeleted'));
    }

	public function show(){
		// 
	}
	
	public function invoiceComment()
    {
		
        		//$categories 	= Company::with('package')->withoutGlobalScope('company')->get();	
				$myPackageHistory = DB::table('package_invoice_comment')->select('package_invoice_comment.*');			
				if (\request('invoice_id') != "")
				{
					$invoice_id		=	request('invoice_id');	
					$myPackageHistory->where('package_invoice_comment.invoice_id', '=', $invoice_id);
				}
				$myPackageHistory->groupBy('package_invoice_comment.id');
				$myPackageHistory->orderBy('package_invoice_comment.id', 'DESC');
							
				
				return DataTables::of($myPackageHistory)
				->editColumn('Name', function ($row) {
						$invoice_id				=	@$row->invoice_id;
						$payment_mode			=	@$row->payment_mode;
						$grand_total			=	number_format(@$row->grand_total);
								
						$cvaccess_startdate 					=	$cvaccess_enddate 	=	$cvaccess_quantity 	=	$cvaccess_price 	=	$cvaccess_totalprice 	= $cvaccess_status =	'';
						$cvaccess_startdate 					=	@$row->cvaccess_startdate ? date("d M, Y", strtotime(@$row->cvaccess_startdate)):'';
						$cvaccess_enddate 						=	@$row->cvaccess_enddate ? date("d M, Y", strtotime(@$row->cvaccess_enddate)):'';
						$cvaccess_quantity 						=	@$row->cvaccess_quantity ? number_format(@$row->cvaccess_quantity) :'';
						$cvaccess_price 						=	@$row->cvaccess_price ? number_format(@$row->cvaccess_price) :'';
						$cvaccess_totalprice 					=	@$row->cvaccess_totalprice ? number_format(@$row->cvaccess_totalprice) :'';
						$cvaccess_status						=	@$row->cvaccess_status == 1 ? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>': '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>';				
				
						$users_startdate 						=	$users_enddate 	=	$users_quantity 	=	$users_price 	=	$users_totalprice 	= $users_status =	'';
						$users_startdate 						=	@$row->users_startdate ? date("d M, Y", strtotime(@$row->users_startdate)):'';
						$users_enddate 							=	@$row->users_enddate ? date("d M, Y", strtotime(@$row->users_enddate)):'';
						$users_quantity 						=	@$row->users_quantity ? number_format(@$row->users_quantity) :'';
						$users_price 							=	@$row->users_price ? number_format(@$row->users_price) :'';
						$users_totalprice 						=	@$row->users_totalprice ? number_format(@$row->users_totalprice) :'';
						$users_status							=	@$row->users_status == 1 ? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>': '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>';				

						$newclientrequest_startdate 			=	$newclientrequest_enddate 	=	$newclientrequest_quantity 	=	$newclientrequest_price 	=	$newclientrequest_totalprice 	= $newclientrequest_status =	'';
						$newclientrequest_startdate 			=	@$row->newclientrequest_startdate ? date("d M, Y", strtotime(@$row->newclientrequest_startdate)):'';
						$newclientrequest_enddate 				=	@$row->newclientrequest_enddate ? date("d M, Y", strtotime(@$row->newclientrequest_enddate)):'';
						$newclientrequest_quantity 				=	@$row->newclientrequest_quantity ? number_format(@$row->newclientrequest_quantity) :'';
						$newclientrequest_price 				=	@$row->newclientrequest_price ? number_format(@$row->newclientrequest_price) :'';
						$newclientrequest_totalprice 			=	@$row->newclientrequest_totalprice ? number_format(@$row->newclientrequest_totalprice) :'';
						$newclientrequest_status				=	@$row->newclientrequest_status == 1 ? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>': '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>';		

						$newjobrequest_startdate 				=	$newjobrequest_enddate 	=	$newjobrequest_quantity 	=	$newjobrequest_price 	=	$newjobrequest_totalprice 	= $newjobrequest_status =	'';
						$newjobrequest_startdate 				=	@$row->newjobrequest_startdate ? date("d M, Y", strtotime(@$row->newjobrequest_startdate)):'';
						$newjobrequest_enddate 					=	@$row->newjobrequest_enddate ? date("d M, Y", strtotime(@$row->newjobrequest_enddate)):'';
						$newjobrequest_quantity 				=	@$row->newjobrequest_quantity ? number_format(@$row->newjobrequest_quantity) :'';
						$newjobrequest_price 					=	@$row->newjobrequest_price ? number_format(@$row->newjobrequest_price) :'';
						$newjobrequest_totalprice 				=	@$row->newjobrequest_totalprice ? number_format(@$row->newjobrequest_totalprice) :'';
						$newjobrequest_status					=	@$row->newjobrequest_status == 1 ? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>': '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>';

						//@$row->newjobrequest_totalprice

						$newbenchclientrequest_status 		=	$newbenchclientrequest_enddate 	=	$newbenchclientrequest_quantity 	=	$newbenchclientrequest_price 	=	$newbenchclientrequest_totalprice 	= $newbenchclientrequest_status =	'';
						$newbenchclientrequest_startdate 		=	@$row->newbenchclientrequest_startdate ? date("d M, Y", strtotime(@$row->newbenchclientrequest_startdate)):'';
						$newbenchclientrequest_enddate 			=	@$row->newbenchclientrequest_enddate ? date("d M, Y", strtotime(@$row->newbenchclientrequest_enddate)):'';
						$newbenchclientrequest_quantity 		=	@$row->newbenchclientrequest_quantity ? number_format(@$row->newbenchclientrequest_quantity) :'';
						$newbenchclientrequest_price 			=	@$row->newbenchclientrequest_price ? number_format(@$row->newbenchclientrequest_price) :'';
						$newbenchclientrequest_totalprice 		=	@$row->newbenchclientrequest_totalprice ? number_format(@$row->newbenchclientrequest_totalprice) :'';
						$newbenchclientrequest_status			=	@$row->newbenchclientrequest_status == 1 ? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>': '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>';

						$emails_startdate 						=	$emails_enddate 	=	$emails_quantity 	=	$emails_price 	=	$emails_totalprice 	= $emails_status =	'';
						$emails_startdate 						=	@$row->emails_startdate ? date("d M, Y", strtotime(@$row->emails_startdate)):'';
						$emails_enddate 						=	@$row->emails_enddate ? date("d M, Y", strtotime(@$row->emails_enddate)):'';
						$emails_quantity 						=	@$row->emails_quantity ? number_format(@$row->emails_quantity) :'';
						$emails_price 							=	@$row->emails_price ? number_format(@$row->emails_price) :'';
						$emails_totalprice 						=	@$row->emails_totalprice ? number_format(@$row->emails_totalprice) :'';
						$emails_status							=	@$row->emails_status == 1 ? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>': '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>'; 

						$oldcvaccess_startdate 					=	$oldcvaccess_enddate 	=	$oldcvaccess_quantity 	=	$oldcvaccess_price 	=	$oldcvaccess_totalprice 	= $oldcvaccess_status =	'';
						$oldcvaccess_startdate 					=	@$row->oldcvaccess_startdate ? date("d M, Y", strtotime(@$row->oldcvaccess_startdate)):'';
						$oldcvaccess_enddate 					=	@$row->oldcvaccess_enddate ? date("d M, Y", strtotime(@$row->oldcvaccess_enddate)):'';
						$oldcvaccess_quantity 					=	@$row->oldcvaccess_quantity ? number_format(@$row->oldcvaccess_quantity) :'';
						$oldcvaccess_price 						=	@$row->oldcvaccess_price ? number_format(@$row->oldcvaccess_price) :'';
						$oldcvaccess_totalprice 				=	@$row->oldcvaccess_totalprice ? number_format(@$row->oldcvaccess_totalprice) :'';
						$oldcvaccess_status						=	@$row->oldcvaccess_status == 1 ? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>': '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>'; 

						$path					=	url('super-admin/custompackages/invoiceDownload/'.@$row->id);
						$packageCommentCount	=	DB::table('package_invoice_comment')->select('*')->where('invoice_id', '=', @$row->id)->count();
						$packageComment			=	DB::table('package_invoice_comment')->select('*')->where('invoice_id', '=', @$row->id)->orderBy('package_invoice_comment.id', 'DESC')->get();

						$commentHtml	=  '';
						if($packageCommentCount > 0)
						{
							foreach($packageComment as $pc)
							{
								$commentHtml	.=	'<tr>
											<td>Posted Comment</td>
											<td colspan="2">'.$pc->comment.'</td>
											<td>'. date("d M, Y h:i A", strtotime(@$pc->created_at)).'</td>
										</tr>';	
							}
						}
				
						$html = '<div class="card" style="margin-top:10px;">
							<div class="card-body">
								<div class="table-responsive">			  		
								<table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4" id="currentPackage">
									<thead class="">
									<tr class="fw-bolder text-muted">
										<th class="min-w-200px"><h4 class="card-title">Invoice ID</h4></th>
										<th class="min-w-150px"><h4 class="card-title">Payment Mode</h4></th>
										<th class="min-w-150px"><h4 class="card-title">Amount Paid</h4></th>
										<th class="min-w-150px"><h4 class="card-title">Invoice Download</h4></th>
									</tr>
									</thead>
									<tbody class="">
									<tr>
										<td>'.@$invoice_id.'</td>
										<td>'.@$payment_mode.'</td>
										<td>&#x20b9;'.@$grand_total.'</td>
										<td> <a href="'.@$path.'"><i class="bi bi-download" style="color:#006600; height:35px; width:35px;"></i></a></td>
									</tr>
									<tr>
										<td>Comment</td>
										<td colspan="2"><textarea name="" cols="100" rows="3" id="comment_'.@$row->id.'"></textarea></td>
										<td><button type="button" class="btn btn-success font-weight-bolder text-uppercase px-5 py-2 comment_submit" rel="'.@$row->id.'"  >Submit</button></td>
									</tr>
									'.$commentHtml.'
									
									</tbody>
								</table>
								</div>
								<div class="table-responsive">
								<table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4" id="currentPackage">  
									<thead class="">
									<tr class="fw-bolder text-muted">
										<th class="min-w-200px">Product</th>
										<th class="min-w-150px">Start Date</th>
										<th class="min-w-150px">End Date</th>
										<th class="min-w-150px">Quantity</th>
										<th class="min-w-150px">Price</th>
										<th class="min-w-150px">Final Price</th>
										<th class="min-w-150px">Status</th>
									</tr>
									</thead>
									<tbody class="">
									<tr>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">CV Access / Received</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$cvaccess_startdate.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$cvaccess_enddate.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$cvaccess_quantity.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$cvaccess_price.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$cvaccess_totalprice.'</a></td>
										<td class="">'.@$cvaccess_status.'</td>
									</tr>
									<tr>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6" >Users</td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$users_startdate.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$users_enddate.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$users_quantity.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$users_price.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$users_totalprice.'</a></td>
										<td class="">'.@$users_status.'</td>
									</tr>
									<tr>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">New Client Request</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$newclientrequest_startdate.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$newclientrequest_enddate.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$newclientrequest_quantity.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$newclientrequest_price.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$newclientrequest_totalprice.'</a></td>
										<td class="">'.@$newclientrequest_status.'</td>
									</tr>
									<tr>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">New Job Request</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$newjobrequest_startdate.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$newjobrequest_enddate.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$newjobrequest_quantity.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$newjobrequest_price.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.$newjobrequest_totalprice.'</a></td>
										<td class="">'.@$newjobrequest_status.'</td>
									</tr>
									<tr>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">New Bench Client Request</td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$newbenchclientrequest_startdate.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$newbenchclientrequest_enddate.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$newbenchclientrequest_quantity.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.$newbenchclientrequest_price.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$newbenchclientrequest_totalprice.'</a></td>
										<td class="">'.@$newbenchclientrequest_status.'</td>
									</tr>
									<tr>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">Emails</td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$emails_startdate.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$emails_enddate.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$emails_quantity.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$emails_price.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$emails_totalprice.'</a></td>
										<td class="">'.@$emails_status.'</td>
									</tr>
									<tr>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">Old CV Access</td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$oldcvaccess_startdate.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$oldcvaccess_enddate.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$oldcvaccess_quantity.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$oldcvaccess_price.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$oldcvaccess_totalprice.'</a></td>
										<td class="">'.@$oldcvaccess_status.'</td>
									</tr>
									</tbody>
								</table>
								</div>
							</div>
							</div>';
						return $html;
				})
				->editColumn('Comment', function ($row) {
					return '<div class="form-check form-check-sm form-check-custom form-check-solid">
							'.@$row->comment.'
						  </div>';
				})
				->editColumn('Date', function ($row) {
					
					$created_at			=	$row->created_at;					
					$middle 			=	strtotime($created_at);             // returns bool(false)
					$new_date 			=	date('d M, Y H:i A', $middle);   // returns 1970-01-01 00:00:00

					return '<div class="form-check form-check-sm form-check-custom form-check-solid">
							'.$new_date.'
						  </div>';
				})	
				->addIndexColumn()
				->rawColumns(['Comment', 'Date']) 
				->make(true);
    } 
	public function myPackageHistory()
    {
				$company_type		=	'';
        		//$categories 		=	Company::with('package')->withoutGlobalScope('company')->get();	
				$myPackageHistory 	=	DB::table('package_invoice')->select('package_invoice.*');			

				if (\request('company_id') != "")
				{
					$company_id			=	request('company_id');	
					$myPackageHistory->where('package_invoice.company_id', '=', $company_id);
					$companyRow 		=	DB::table('companies')->select('*')->where('id', $company_id)->first();
					$company_type		=	$companyRow->company_type;
				}
		
				$myPackageHistory->groupBy('package_invoice.id');
				$myPackageHistory->orderBy('package_invoice.id', 'DESC');

				// dd($myPackageHistory);
						
				return DataTables::of($myPackageHistory)
				->editColumn('Name', function ($row) use($company_type) {
								$invoice_id				=	@$row->invoice_id;
								$payment_mode			=	@$row->payment_mode;
								$grand_total			=	number_format(@$row->grand_total);
										
								$cvaccess_startdate 					=	$cvaccess_enddate 	=	$cvaccess_quantity 	=	$cvaccess_price 	=	$cvaccess_totalprice 	= $cvaccess_status =	'';
								$cvaccess_startdate 					=	@$row->cvaccess_startdate ? date("d M, Y", strtotime(@$row->cvaccess_startdate)):'';
								$cvaccess_enddate 						=	@$row->cvaccess_enddate ? date("d M, Y", strtotime(@$row->cvaccess_enddate)):'';
								$cvaccess_quantity 						=	@$row->cvaccess_quantity ? number_format(@$row->cvaccess_quantity) :'';
								$cvaccess_price 						=	@$row->cvaccess_price ? number_format(@$row->cvaccess_price) :'';
								$cvaccess_totalprice 					=	@$row->cvaccess_totalprice ? number_format(@$row->cvaccess_totalprice) :'';
								$cvaccess_status						=	@$row->cvaccess_status == 1 ? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>': '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>';				
												
								$users_startdate 						=	$users_enddate 	=	$users_quantity 	=	$users_price 	=	$users_totalprice 	= $users_status =	'';
								$users_startdate 						=	@$row->users_startdate ? date("d M, Y", strtotime(@$row->users_startdate)):'';
								$users_enddate 							=	@$row->users_enddate ? date("d M, Y", strtotime(@$row->users_enddate)):'';
								$users_quantity 						=	@$row->users_quantity ? number_format(@$row->users_quantity) :'';
								$users_price 							=	@$row->users_price ? number_format(@$row->users_price) :'';
								$users_totalprice 						=	@$row->users_totalprice ? number_format(@$row->users_totalprice) :'';
								$users_status							=	@$row->users_status == 1 ? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>': '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>';				

								$newclientrequest_startdate 			=	$newclientrequest_enddate 	=	$newclientrequest_quantity 	=	$newclientrequest_price 	=	$newclientrequest_totalprice 	= $newclientrequest_status =	'';
								$newclientrequest_startdate 			=	@$row->newclientrequest_startdate ? date("d M, Y", strtotime(@$row->newclientrequest_startdate)):'';
								$newclientrequest_enddate 				=	@$row->newclientrequest_enddate ? date("d M, Y", strtotime(@$row->newclientrequest_enddate)):'';
								$newclientrequest_quantity 				=	@$row->newclientrequest_quantity ? number_format(@$row->newclientrequest_quantity) :'';
								$newclientrequest_price 				=	@$row->newclientrequest_price ? number_format(@$row->newclientrequest_price) :'';
								$newclientrequest_totalprice 			=	@$row->newclientrequest_totalprice ? number_format(@$row->newclientrequest_totalprice) :'';
								$newclientrequest_status				=	@$row->newclientrequest_status == 1 ? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>': '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>';		

								$newjobrequest_startdate 				=	$newjobrequest_enddate 	=	$newjobrequest_quantity 	=	$newjobrequest_price 	=	$newjobrequest_totalprice 	= $newjobrequest_status =	'';
								$newjobrequest_startdate 				=	@$row->newjobrequest_startdate ? date("d M, Y", strtotime(@$row->newjobrequest_startdate)):'';
								$newjobrequest_enddate 					=	@$row->newjobrequest_enddate ? date("d M, Y", strtotime(@$row->newjobrequest_enddate)):'';
								$newjobrequest_quantity 				=	@$row->newjobrequest_quantity ? number_format(@$row->newjobrequest_quantity) :'';
								$newjobrequest_price 					=	@$row->newjobrequest_price ? number_format(@$row->newjobrequest_price) :'';
								$newjobrequest_totalprice 				=	@$row->newjobrequest_totalprice ? number_format(@$row->newjobrequest_totalprice) :'';
								$newjobrequest_status					=	@$row->newjobrequest_status == 1 ? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>': '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>';

								//@$row->newjobrequest_totalprice

								$newbenchclientrequest_status 		=	$newbenchclientrequest_enddate 	=	$newbenchclientrequest_quantity 	=	$newbenchclientrequest_price 	=	$newbenchclientrequest_totalprice 	= $newbenchclientrequest_status =	'';
								$newbenchclientrequest_startdate 		=	@$row->newbenchclientrequest_startdate ? date("d M, Y", strtotime(@$row->newbenchclientrequest_startdate)):'';
								$newbenchclientrequest_enddate 			=	@$row->newbenchclientrequest_enddate ? date("d M, Y", strtotime(@$row->newbenchclientrequest_enddate)):'';
								$newbenchclientrequest_quantity 		=	@$row->newbenchclientrequest_quantity ? number_format(@$row->newbenchclientrequest_quantity) :'';
								$newbenchclientrequest_price 			=	@$row->newbenchclientrequest_price ? number_format(@$row->newbenchclientrequest_price) :'';
								$newbenchclientrequest_totalprice 		=	@$row->newbenchclientrequest_totalprice ? number_format(@$row->newbenchclientrequest_totalprice) :'';
								$newbenchclientrequest_status			=	@$row->newbenchclientrequest_status == 1 ? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>': '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>';

								$emails_startdate 						=	$emails_enddate 	=	$emails_quantity 	=	$emails_price 	=	$emails_totalprice 	= $emails_status =	'';
								$emails_startdate 						=	@$row->emails_startdate ? date("d M, Y", strtotime(@$row->emails_startdate)):'';
								$emails_enddate 						=	@$row->emails_enddate ? date("d M, Y", strtotime(@$row->emails_enddate)):'';
								$emails_quantity 						=	@$row->emails_quantity ? number_format(@$row->emails_quantity) :'';
								$emails_price 							=	@$row->emails_price ? number_format(@$row->emails_price) :'';
								$emails_totalprice 						=	@$row->emails_totalprice ? number_format(@$row->emails_totalprice) :'';
								$emails_status							=	@$row->emails_status == 1 ? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>': '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>'; 

								$oldcvaccess_startdate 					=	$oldcvaccess_enddate 	=	$oldcvaccess_quantity 	=	$oldcvaccess_price 	=	$oldcvaccess_totalprice 	= $oldcvaccess_status =	'';
								$oldcvaccess_startdate 					=	@$row->oldcvaccess_startdate ? date("d M, Y", strtotime(@$row->oldcvaccess_startdate)):'';
								$oldcvaccess_enddate 					=	@$row->oldcvaccess_enddate ? date("d M, Y", strtotime(@$row->oldcvaccess_enddate)):'';
								$oldcvaccess_quantity 					=	@$row->oldcvaccess_quantity ? number_format(@$row->oldcvaccess_quantity) :'';
								$oldcvaccess_price 						=	@$row->oldcvaccess_price ? number_format(@$row->oldcvaccess_price) :'';
								$oldcvaccess_totalprice 				=	@$row->oldcvaccess_totalprice ? number_format(@$row->oldcvaccess_totalprice) :'';
								$oldcvaccess_status						=	@$row->oldcvaccess_status == 1 ? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>': '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>'; 



								$boostkeywords_startdate  = $boostkeywords_enddate	=	$boostkeywords_status	=	$boostkeywords_price	=	$boostkeywords_totalprice	=	$boostkeywords_text		= $boostkeywords_quantity =	'';
								$boostkeywords_startdate 				=	@$row->boostkeywords_startdate ? date("d M, Y", strtotime(@$row->boostkeywords_startdate)):'';
								$boostkeywords_enddate 					=	@$row->boostkeywords_enddate ? date("d M, Y", strtotime(@$row->boostkeywords_enddate)):'';

								$boostkeywords_quantity 				=	@$row->boostkeywords_quantity ? number_format(@$row->boostkeywords_quantity) :'';
								$boostkeywords_price 					=	@$row->boostkeywords_price ? number_format(@$row->boostkeywords_price) :'';

								$boostkeywords_totalprice 				=	@$row->boostkeywords_totalprice ? number_format(@$row->boostkeywords_totalprice) :'';
								$boostkeywords_text 					=	@$row->boostkeywords_text ? @$row->boostkeywords_text :'';

								$boostkeywords_status					=	@$row->boostkeywords_status == 1 ? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>': '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>'; 
													
								
								$path					=	url('super-admin/custompackages/invoiceDownload/'.@$row->id);
								$packageCommentCount	=	DB::table('package_invoice_comment')->select('*')->where('invoice_id', '=', @$row->id)->count();
								$packageComment			=	DB::table('package_invoice_comment')->select('*')->where('invoice_id', '=', @$row->id)->orderBy('package_invoice_comment.id', 'DESC')->get();

								$commentHtml	=  '';
								if($packageCommentCount > 0)
								{
									foreach($packageComment as $pc)
									{
										$commentHtml	.=	'<tr>
													<td>Posted Comment</td>
													<td colspan="2">'.$pc->comment.'</td>
													<td>'. date("d M, Y h:i A", strtotime(@$pc->created_at)).'</td>
												</tr>';	
									}
								}
			
									
								$service_html		=	'';
				

								if($company_type != 3)
								{

											$service_html		=	'<tr>
													<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">Contract Recruitment Lead</a></td>
													<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$newclientrequest_startdate.'</a></td>
													<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$newclientrequest_enddate.'</a></td>
													<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$newclientrequest_quantity.'</a></td>
													<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$newclientrequest_price.'</a></td>
													<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$newclientrequest_totalprice.'</a></td>
													<td class="">'.@$newclientrequest_status.'</td>
												</tr>
												<tr>
													<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">Fulltime Recruitment Leads</a></td>
													<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$newjobrequest_startdate.'</a></td>
													<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$newjobrequest_enddate.'</a></td>
													<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$newjobrequest_quantity.'</a></td>
													<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$newjobrequest_price.'</a></td>
													<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.$newjobrequest_totalprice.'</a></td>
													<td class="">'.@$newjobrequest_status.'</td>
												</tr>
												<tr>
													<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">Boost Profile Keywords</a></td>
													<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$boostkeywords_startdate.'</a></td>
													<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$boostkeywords_enddate.'</a></td>
													
													<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$boostkeywords_quantity.'</a></td>
													<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$boostkeywords_price.'</a></td>
													<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$boostkeywords_totalprice.'</a></td>	
													<td class="">'.@$boostkeywords_status.'</td>
												</tr>
												
												<tr>
													<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">&nbsp;</a></td>  
													<td colspan="6"><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$boostkeywords_text.'</a></td>
													
												</tr>
												
												
												
												
												';
												
								}
					
					
					
								$html = '<div class="card" style="margin-top:10px;">
								<div class="card-body">
									<div class="table-responsive">			  		
									<table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4" id="currentPackage">
										<thead class="">
										<tr class="fw-bolder text-muted">
											<th class="min-w-200px"><h4 class="card-title">Invoice ID</h4></th>
											<th class="min-w-150px"><h4 class="card-title">Payment Mode</h4></th>
											<th class="min-w-150px"><h4 class="card-title">Amount Paid</h4></th>
											<th class="min-w-150px"><h4 class="card-title">Invoice Download</h4></th>
										</tr>
										</thead>
										<tbody class="">
										<tr>
											<td>'.@$invoice_id.'</td>
											<td>'.@$payment_mode.'</td>
											<td>&#x20b9;'.@$grand_total.'</td>
											<td> <a href="'.@$path.'"><i class="bi bi-download" style="color:#006600; height:35px; width:35px;"></i></a></td>
										</tr>
										<tr>
											<td>Comment</td>
											<td colspan="2"><textarea name="" cols="100" rows="3" id="comment_'.@$row->id.'"></textarea></td>
											<td><button type="button" class="btn btn-success font-weight-bolder text-uppercase px-5 py-2 comment_submit" rel="'.@$row->id.'"  >Submit</button></td>
										</tr>
										'.$commentHtml.'
										
										</tbody>
									</table>
									</div>
									<div class="table-responsive">
									<table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4" id="currentPackage">  
										<thead class="">
										<tr class="fw-bolder text-muted">
											<th class="min-w-200px">Product</th>
											<th class="min-w-150px">Start Date</th>
											<th class="min-w-150px">End Date</th>
											<th class="min-w-150px">Quantity</th>
											<th class="min-w-150px">Price</th>
											<th class="min-w-150px">Final Price</th>
											<th class="min-w-150px">Status</th>
										</tr>
										</thead>
										<tbody class="">
										<tr>
											<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">CV Access / Received</a></td>
											<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$cvaccess_startdate.'</a></td>
											<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$cvaccess_enddate.'</a></td>
											<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$cvaccess_quantity.'</a></td>
											<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$cvaccess_price.'</a></td>
											<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$cvaccess_totalprice.'</a></td>
											<td class="">'.@$cvaccess_status.'</td>
										</tr>
										<tr>
											<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6" >Users</td>
											<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$users_startdate.'</a></td>
											<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$users_enddate.'</a></td>
											<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$users_quantity.'</a></td>
											<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$users_price.'</a></td>
											<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$users_totalprice.'</a></td>
											<td class="">'.@$users_status.'</td>
										</tr>
										
										<tr>
											<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">New Bench Client Request</td>
											<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$newbenchclientrequest_startdate.'</a></td>
											<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$newbenchclientrequest_enddate.'</a></td>
											<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$newbenchclientrequest_quantity.'</a></td>
											<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.$newbenchclientrequest_price.'</a></td>
											<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$newbenchclientrequest_totalprice.'</a></td>
											<td class="">'.@$newbenchclientrequest_status.'</td>
										</tr>
										<tr>
											<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">Emails</td>
											<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$emails_startdate.'</a></td>
											<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$emails_enddate.'</a></td>
											<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$emails_quantity.'</a></td>
											<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$emails_price.'</a></td>
											<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$emails_totalprice.'</a></td>
											<td class="">'.@$emails_status.'</td>
										</tr>
										<tr>
											<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">Old CV Access</td>
											<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$oldcvaccess_startdate.'</a></td>
											<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$oldcvaccess_enddate.'</a></td>
											<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$oldcvaccess_quantity.'</a></td>
											<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$oldcvaccess_price.'</a></td>
											<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$oldcvaccess_totalprice.'</a></td>
											<td class="">'.@$oldcvaccess_status.'</td>
										</tr>
										
										
										'.@$service_html.'
										

											</tbody>
										</table>
										</div>
									</div>
									</div>';
								return $html;
				})
				->addIndexColumn()
				->rawColumns(['Name'])
				->make(true);
    }
	public function data()
    {
		
				//echo "call"; exit; 
				//$categories 		=	Company::with('package')->withoutGlobalScope('company')->get();	
				$packageInvoice =	DB::table('package_invoice')
									->join('companies', 'companies.id', '=', 'package_invoice.company_id')
									->join('packages','packages.id','=','package_invoice.package_id')
									->select('package_invoice.*', 'companies.company_name');			
		
				$packageInvoice->groupBy('package_invoice.id');
				$packageInvoice->orderBy('package_invoice.id', 'DESC');
							
				
			return DataTables::of($packageInvoice)
							
				->editColumn('chkbox', function ($row) {
					return '<div class="form-check form-check-sm form-check-custom form-check-solid">
							<input class="form-check-input" type="checkbox" value="1">
						  </div>';
				})	
				->editColumn('InvoiceNo', function ($row) {
					return '<div class="form-check form-check-sm form-check-custom form-check-solid">
								<a href="javascript:void(0);" data-row-id="'.@$row->id.'" class="fw-bold text-muted d-block fs-7 text-hover-primary show-detail">
									'.@$row->invoice_id.'
								</a> 							
						    </div>';
				})
				->editColumn('Company', function ($row) {
					return '<div class="form-check form-check-sm form-check-custom form-check-solid">
							'.@$row->company_name.'
						  </div>'; 
				})
				->editColumn('PaymentMode', function ($row) { 
					return '<div class="form-check form-check-sm form-check-custom form-check-solid">
							'.@$row->payment_mode.' 
						  </div>';
				})
				->editColumn('AmountPaid', function ($row) {
					return '<div class="form-check form-check-sm form-check-custom form-check-solid">
							'.@$row->grand_total.' 
						  </div>';
				})
				->editColumn('InvoiceDownload', function ($row) {
					return '<div class="form-check form-check-sm form-check-custom form-check-solid">
							<a href="'.url('super-admin/custompackages/invoiceDownload/'.$row->id).'"><i class="bi bi-download" style="color:#006600; height:35px; width:35px;"></i></a>
						  </div>';
				})
				->editColumn('Create', function ($row) {
					$create_at	=	'';
					$create_at	=	date("d M, Y h:i A", strtotime(@$row->create_at));
					
					return '<div class="form-check form-check-sm form-check-custom form-check-solid"> 
							'.@$create_at.' 
						  </div>';
				})
				->editColumn('Status', function ($row) {
					
					$payment_status = 'Pending';
					$classs = 'warning';
					if($row->payment_status >=1){
						$payment_status = 'Paid';
						$classs = 'success';
					}
					
					
					return '<span class="badge bg-'.$classs.'"> 
							'.$payment_status.'
						  </span>';
				})
				->addIndexColumn()
				->rawColumns(['chkbox', 'InvoiceNo', 'Company', 'PaymentMode', 'AmountPaid', 'InvoiceDownload', 'Create','Status']) 
				->make(true);
    }
	public function invoiceDownload($id)
    {
	 	
		$this->company      	=	auth()->user();	
		
		
		$this->invoiceRow 	=	DB::table('package_invoice')
								->select('package_invoice.*', 'companies.company_type', 'companies.logo', 'companies.company_email')
								->join('companies', 'companies.id', '=', 'package_invoice.company_id')
								->where('package_invoice.id', '=', $id)
								->first();
		
		$this->client_location_count 	= DB::table('client_location')
											->join('clients', 'clients.id', '=', 'client_location.client_id')
											->select('client_location.city', 'client_location.city_id', 'client_location.address', 'client_location.postcode', 'client_location.country_id')
											->where('clients.company_id', $this->invoiceRow->company_id)
											->where('clients.self_company', 1)
											->orderBy('client_location.id', 'ASC')
											->count();
											
		 $this->client_location 				= DB::table('client_location')
											->join('clients', 'clients.id', '=', 'client_location.client_id')
											->select('client_location.city', 'client_location.city_id', 'client_location.address', 'client_location.postcode', 'client_location.country_id')
											->where('clients.company_id', $this->invoiceRow->company_id)
											->where('clients.self_company', 1)
											->orderBy('client_location.id', 'ASC') 
											->first();
		$this->company_city_name	=	'';
		$this->company_country_name	=	'';
		if($this->client_location_count > 0)
		{
				if($this->client_location->country_id == 101)
				{
					$this->city_obj 				=	DB::table('city_list')->select('city_list.city')->where('city_list.city_id', $this->client_location->city_id)->first();
					$this->company_city_name		=	ucwords($this->city_obj->city);
				}
				else
				{
					$city							=	$this->client_location->city;
					$this->company_city_name		=	ucwords($city);	
				}
				
				if($this->client_location->country_id > 0){
					$country_name	= GeneralMethod::getCountryName($this->client_location->country_id);	
					foreach($country_name as $cval){
						$this->company_country_name	= ucwords($cval['name']);	
					}
				}
		
		}		

		$this->invoiceRowItems	=	DB::table('package_invoice')
								->select('package_invoice.*', 'companies.company_type', 'companies.logo', 'companies.company_email','package_features.title','package_order_items.status','package_order_items.qty','package_order_items.price','package_order_items.sub_total')->join('package_order_items','package_order_items.invoice_id','=','package_invoice.id')->join('package_features','package_features.id','=','package_order_items.feature_id')
								->join('companies', 'companies.id', '=', 'package_invoice.company_id')
								->where('package_invoice.id', '=', $id)
								->get();
					
		return view('razorpay-invoice.custom_package_invoice', $this->data);
		
    }
	
	public function submitcomment(Request $request)
	{
			$id					=	'';
			$id 				=	$request->id;
			
			$comment			=	'';
			$comment 			=	$request->comment;
			
			$value  			=	array('invoice_id' => @$id, 'comment' => @$comment);
			
			DB::table('package_invoice_comment')->insert($value);
			$comment_id    		=	DB::getPdo()->lastInsertId();
			
			if($comment_id  > 0){
				return Reply::dataOnly(['status' => 'success']);
			}else{
				return Reply::dataOnly(['status' => 'fail']);	
			}
			
        	
	}
	
	public function viewMore($id)
	{
				$this->invoice_id		=	$id;
				
				$this->packageDetail	=	DB::table('package_invoice')
				->join('companies', 'companies.id', '=', 'package_invoice.company_id')
				->select('package_invoice.id','package_invoice.create_at','package_invoice.invoice_id','package_invoice.payment_mode','package_invoice.grand_total','package_invoice.cvaccess_startdate','package_invoice.cvaccess_enddate','package_invoice.cvaccess_status', 'package_invoice.cvaccess_quantity','package_invoice.cvaccess_price','package_invoice.cvaccess_totalprice','package_invoice.users_startdate','package_invoice.users_enddate', 'package_invoice.users_status','package_invoice.users_quantity','package_invoice.users_price','package_invoice.users_totalprice','package_invoice.newclientrequest_startdate','package_invoice.newclientrequest_enddate','package_invoice.newclientrequest_status','package_invoice.newclientrequest_quantity', 'package_invoice.newclientrequest_price','package_invoice.newclientrequest_totalprice','package_invoice.newjobrequest_startdate','package_invoice.newjobrequest_enddate', 'package_invoice.newjobrequest_status','package_invoice.newjobrequest_quantity', 'package_invoice.newjobrequest_price','package_invoice.newjobrequest_totalprice','package_invoice.newbenchclientrequest_startdate','package_invoice.newbenchclientrequest_enddate', 'package_invoice.newbenchclientrequest_status','package_invoice.newbenchclientrequest_quantity','package_invoice.newbenchclientrequest_price',  'package_invoice.newbenchclientrequest_totalprice','package_invoice.emails_startdate', 'package_invoice.emails_enddate','package_invoice.emails_status','package_invoice.emails_quantity','package_invoice.emails_price','package_invoice.emails_totalprice', 'package_invoice.oldcvaccess_startdate', 'package_invoice.oldcvaccess_enddate', 'package_invoice.oldcvaccess_status','package_invoice.oldcvaccess_quantity', 'package_invoice.oldcvaccess_price','package_invoice.oldcvaccess_totalprice','package_invoice.boostkeywords_startdate','package_invoice.boostkeywords_enddate', 'package_invoice.boostkeywords_status','package_invoice.boostkeywords_price', 'package_invoice.boostkeywords_totalprice','package_invoice.boostkeywords_text', 'package_invoice.boostkeywords_quantity','companies.company_name','companies.status', 'companies.company_email','companies.company_phone', 'companies.website', 'companies.company_type','companies.company_size', 'companies.founded')
				->where('package_invoice.id', '=', $this->invoice_id)
				->first();

				$this->package_invoice_comment_count=DB:: table('package_invoice_comment')
				->select('package_invoice_comment.*')
				->where('package_invoice_comment.invoice_id', '=', $this->invoice_id)
				->orderBy('package_invoice_comment.id', 'DESC')
				->count(); 
												
				$this->package_invoice_comment	=	DB:: table('package_invoice_comment')
				->select('package_invoice_comment.*')
				->where('package_invoice_comment.invoice_id', '=', $this->invoice_id)
				->orderBy('package_invoice_comment.id', 'DESC')
				->get(); 			
									
			$view 				=	view('super-admin.custompackages.viewMore', $this->data)->render();
        	return Reply::dataOnly(['status' => 'success', 'view' => $view]);			

	}
	 
	public function editWallet_old($id)
	{
				
				$company_id				= '';
				$company_id				=	$id;		
				$companyRow 			=	DB::table('companies')->select('*')->where('companies.id', $company_id)->first();
		
				//echo '<pre>'; print_r($companyRow); exit;  
		
				$this->company_id		=	$id;				
				$this->packageDetail	=	DB::table('package_active')
				->join('companies', 'companies.id', '=', 'package_active.company_id')
				->select('package_active.id','package_active.cvaccess_quantity', 'package_active.cvaccess_used', 'package_active.cvaccess_balance', 'package_active.cvaccess_startdate', 'package_active.cvaccess_enddate', 'package_active.cvaccess_status', 'package_active.users_startdate', 'package_active.users_enddate', 'package_active.users_status', 'package_active.users_quantity', 'package_active.users_used', 'package_active.users_balance','package_active.newclientrequest_startdate', 'package_active.newclientrequest_enddate', 'package_active.newclientrequest_status', 'package_active.newclientrequest_quantity', 'package_active.newclientrequest_used', 'package_active.newclientrequest_balance','package_active.newbenchclientrequest_startdate', 'package_active.newbenchclientrequest_enddate', 'package_active.newbenchclientrequest_status', 'package_active.newbenchclientrequest_quantity', 'package_active.newbenchclientrequest_used', 'package_active.newbenchclientrequest_balance','package_active.newjobrequest_startdate', 'package_active.newjobrequest_enddate', 'package_active.newjobrequest_status', 'package_active.newjobrequest_quantity', 'package_active.newjobrequest_used', 'package_active.newjobrequest_balance','package_active.emails_startdate', 'package_active.emails_enddate', 'package_active.emails_status', 'package_active.emails_quantity', 'package_active.emails_used', 'package_active.emails_balance','package_active.oldcvaccess_startdate', 'package_active.oldcvaccess_enddate', 'package_active.oldcvaccess_status', 'package_active.oldcvaccess_quantity', 'package_active.oldcvaccess_used', 'package_active.oldcvaccess_balance','package_active.boostkeywords_startdate', 'package_active.boostkeywords_enddate', 'package_active.boostkeywords_status', 'package_active.boostkeywords_text','package_active.boostkeywords_quantity', 'package_active.boostkeywords_used', 'package_active.boostkeywords_balance','companies.company_name', 'companies.status', 'companies.company_email', 'companies.company_phone', 'companies.website', 'companies.company_type','companies.company_size', 'companies.founded'
				)->where('package_active.company_id', '=', $this->company_id)
				->first();
									
			$view 				=	view('super-admin.custompackages.editWallet', $this->data)->render();
        	return Reply::dataOnly(['status' => 'success', 'view' => $view]);			

	}

	public function editWallet($id)
	{
				
				$company_id				= '';
				$company_id				=	(int) $id;	
				// dd($company_id	);	
				$companyRow 			=	DB::table('companies')->select('*')->where('companies.id', $company_id)->first();


				$this->package_active_all = DB::table('package_active')->join('package_invoice','package_invoice.id','=','package_active.package_invoice_id')->join('package_order_items','package_order_items.invoice_id','=','package_invoice.id')->join('package_features','package_features.id','=','package_order_items.feature_id')->select('package_active.*','package_features.title','package_features.field_name','package_features.id as gg_is')->where('package_active.company_id', $company_id)->get();

				// dd($this->package_active_all);
				$this->package_active_details = DB::table('package_active')->join('package_invoice','package_invoice.id','=','package_active.package_invoice_id')->join('packages','packages.id','=','package_invoice.package_id')->select('package_active.*','packages.name')->where('package_active.company_id', $company_id)->first();

				//echo '<pre>'; print_r($companyRow); exit;  
		
				$this->company_id		=	$id;				
				$this->packageDetail	=	DB::table('package_active')
				->join('companies', 'companies.id', '=', 'package_active.company_id')
				->select('package_active.id','package_active.cvaccess_quantity', 'package_active.cvaccess_used', 'package_active.cvaccess_balance', 'package_active.cvaccess_startdate', 'package_active.cvaccess_enddate', 'package_active.cvaccess_status', 'package_active.users_startdate', 'package_active.users_enddate', 'package_active.users_status', 'package_active.users_quantity', 'package_active.users_used', 'package_active.users_balance','package_active.newclientrequest_startdate', 'package_active.newclientrequest_enddate', 'package_active.newclientrequest_status', 'package_active.newclientrequest_quantity', 'package_active.newclientrequest_used', 'package_active.newclientrequest_balance','package_active.newbenchclientrequest_startdate', 'package_active.newbenchclientrequest_enddate', 'package_active.newbenchclientrequest_status', 'package_active.newbenchclientrequest_quantity', 'package_active.newbenchclientrequest_used', 'package_active.newbenchclientrequest_balance','package_active.newjobrequest_startdate', 'package_active.newjobrequest_enddate', 'package_active.newjobrequest_status', 'package_active.newjobrequest_quantity', 'package_active.newjobrequest_used', 'package_active.newjobrequest_balance','package_active.emails_startdate', 'package_active.emails_enddate', 'package_active.emails_status', 'package_active.emails_quantity', 'package_active.emails_used', 'package_active.emails_balance','package_active.oldcvaccess_startdate', 'package_active.oldcvaccess_enddate', 'package_active.oldcvaccess_status', 'package_active.oldcvaccess_quantity', 'package_active.oldcvaccess_used', 'package_active.oldcvaccess_balance','package_active.boostkeywords_startdate', 'package_active.boostkeywords_enddate', 'package_active.boostkeywords_status', 'package_active.boostkeywords_text','package_active.boostkeywords_quantity', 'package_active.boostkeywords_used', 'package_active.boostkeywords_balance','companies.company_name', 'companies.status', 'companies.company_email', 'companies.company_phone', 'companies.website', 'companies.company_type','companies.company_size', 'companies.founded'
				)
				->where('package_active.company_id', '=', $this->company_id)
				->first();	
										
			$view 				=	view('super-admin.custompackages.editWallet', $this->data)->render();
        	return Reply::dataOnly(['status' => 'success', 'view' => $view]);			

	}
	
	public function update_old(Request $request, $id) 
	{
					$company_id						= '';
					$company_id						=	$id;		
					$companyRow 					=	DB::table('companies')->select('*')->where('companies.id', $company_id)->first();
				
				
				
					$company_id						=	'';
					$company_id						=	$id;
					
					$cvaccess_quantity				=	$cvaccess_used	=	$cvaccess_balance	=	$cvaccess_startdate	=	$cvaccess_enddate	=	$cvaccess_status	=	'';
					$cvaccess_quantity				=	@$request->cvaccess_quantity;
					$cvaccess_used					=	@$request->cvaccess_used;
					$cvaccess_balance				=	@$request->cvaccess_balance;
					$cvaccess_startdate				=	@$request->cvaccess_startdate;
					$cvaccess_enddate				=	@$request->cvaccess_enddate;
					$cvaccess_status				=	@$request->cvaccess_status;
					
					$users_quantity					=	$users_used	=	$users_balance	=	$users_startdate	=	$users_enddate	=	$users_status	=	'';	
					$users_quantity					=	@$request->users_quantity;
					$users_used						=	@$request->users_used;
					$users_balance					=	@$request->users_balance;
					$users_startdate				=	@$request->users_startdate;
					$users_enddate					=	@$request->users_enddate;
					$users_status					=	@$request->users_status;
									
					$newclientrequest_quantity		=	$newclientrequest_used	=	$newclientrequest_balance	=	$newclientrequest_startdate	=	$newclientrequest_enddate	=	$newclientrequest_status='';
					$newclientrequest_quantity		=	@$request->newclientrequest_quantity;
					$newclientrequest_used			=	@$request->newclientrequest_used;
					$newclientrequest_balance		=	@$request->newclientrequest_balance;
					$newclientrequest_startdate		=	@$request->newclientrequest_startdate;
					$newclientrequest_enddate		=	@$request->newclientrequest_enddate;
					$newclientrequest_status		=	@$request->newclientrequest_status;
					
					$newbenchclientrequest_quantity	=	$newbenchclientrequest_used	=	$newbenchclientrequest_balance	=	$newbenchclientrequest_startdate	=	$newbenchclientrequest_enddate	=	$newbenchclientrequest_status	=	'';
					$newbenchclientrequest_quantity	=	@$request->newbenchclientrequest_quantity;
					$newbenchclientrequest_used		=	@$request->newbenchclientrequest_used;
					$newbenchclientrequest_balance	=	@$request->newbenchclientrequest_balance;
					$newbenchclientrequest_startdate=	@$request->newbenchclientrequest_startdate;
					$newbenchclientrequest_enddate	=	@$request->newbenchclientrequest_enddate;
					$newbenchclientrequest_status	=	@$request->newbenchclientrequest_status;
										
					$newjobrequest_quantity			=	$newjobrequest_used	=	$newjobrequest_balance	=	$newjobrequest_startdate	=	$newjobrequest_enddate	=	$newjobrequest_status	=	'';
					$newjobrequest_quantity			=	@$request->newjobrequest_quantity;
					$newjobrequest_used				=	@$request->newjobrequest_used;
					$newjobrequest_balance			=	@$request->newjobrequest_balance;
					$newjobrequest_startdate		=	@$request->newjobrequest_startdate;
					$newjobrequest_enddate			=	@$request->newjobrequest_enddate;
					$newjobrequest_status			=	@$request->newjobrequest_status;
					
					$emails_quantity				=	$emails_used	=	$emails_balance	=	$emails_startdate	=	$emails_enddate	=	$emails_status	=	'';					
					$emails_quantity				=	@$request->emails_quantity;
					$emails_used					=	@$request->emails_used;
					$emails_balance					=	@$request->emails_balance;
					$emails_startdate				=	@$request->emails_startdate;
					$emails_enddate					=	@$request->emails_enddate;
					$emails_status					=	@$request->emails_status;
					
					
					$oldcvaccess_quantity			=	$oldcvaccess_used	=	$oldcvaccess_balance	=	$oldcvaccess_startdate	=	$oldcvaccess_enddate	=	$oldcvaccess_status	=	'';				
					$oldcvaccess_quantity			=	@$request->oldcvaccess_quantity;
					$oldcvaccess_used				=	@$request->oldcvaccess_used;
					$oldcvaccess_balance			=	@$request->oldcvaccess_balance;
					$oldcvaccess_startdate			=	@$request->oldcvaccess_startdate;
					$oldcvaccess_enddate			=	@$request->oldcvaccess_enddate;
					$oldcvaccess_status				=	@$request->oldcvaccess_status;
					
					
			
					$updateArr 			= array(
													'cvaccess_quantity' 			=> @$cvaccess_quantity,
													'cvaccess_used' 				=> @$cvaccess_used,
													'cvaccess_balance' 				=> @$cvaccess_balance,
													'cvaccess_startdate' 			=> @$cvaccess_startdate,  
													'cvaccess_enddate' 				=> @$cvaccess_enddate,  
													'cvaccess_status' 				=> @$cvaccess_status,
													
													'users_quantity' 				=> @$users_quantity,
													'users_used' 					=> @$users_used,
													'users_balance' 				=> @$users_balance,
													'users_startdate' 				=> @$users_startdate,
													'users_enddate' 				=> @$users_enddate,
													'users_status' 					=> @$users_status,
													
													'newbenchclientrequest_quantity' => @$newbenchclientrequest_quantity,
													'newbenchclientrequest_used' 	 => @$newbenchclientrequest_used,
													'newbenchclientrequest_balance'  => @$newbenchclientrequest_balance,
													'newbenchclientrequest_startdate'=> @$newbenchclientrequest_startdate,
													'newbenchclientrequest_enddate'  => @$newbenchclientrequest_enddate,
													'newbenchclientrequest_status' 	 => @$newbenchclientrequest_status,
													
													'emails_quantity' 				=> @$emails_quantity,
													'emails_used' 					=> @$emails_used,
													'emails_balance' 				=> @$emails_balance,
													'emails_startdate' 				=> @$emails_startdate,
													'emails_enddate' 				=> @$emails_enddate,
													'emails_status' 				=> @$emails_status,
													 
													 'oldcvaccess_quantity' 		=> @$oldcvaccess_quantity,
													 'oldcvaccess_used' 			=> @$oldcvaccess_used,
													 'oldcvaccess_balance' 			=> @$oldcvaccess_balance,
													 'oldcvaccess_startdate' 		=> @$oldcvaccess_startdate,
													 'oldcvaccess_enddate' 			=> @$oldcvaccess_enddate,
													 'oldcvaccess_status' 			=> @$oldcvaccess_status
													      
												);
					
					
					if($companyRow->company_type != 3){
						$updateArr['newjobrequest_quantity']		=	@$newjobrequest_quantity;		
						$updateArr['newjobrequest_used']			=	@$newjobrequest_used;		
						$updateArr['newjobrequest_balance']			=	@$newjobrequest_balance;		
						$updateArr['newjobrequest_startdate']		=	@$newjobrequest_startdate;		
						$updateArr['newjobrequest_enddate']			=	@$newjobrequest_enddate;		
						$updateArr['newjobrequest_status']			=	@$newjobrequest_status;	
						
						$updateArr['newclientrequest_quantity']		=	@$newclientrequest_quantity;	
						$updateArr['newclientrequest_used']			=	@$newclientrequest_used;	
						$updateArr['newclientrequest_balance']		=	@$newclientrequest_balance;	
						$updateArr['newclientrequest_startdate']	=	@$newclientrequest_startdate;	
						$updateArr['newclientrequest_enddate']		=	@$newclientrequest_enddate;	
						$updateArr['newclientrequest_status']		=	@$newclientrequest_status;		
					}
					//$companyRow 
					
					DB::table('package_active')->where('company_id', $company_id)->limit(1)->update($updateArr);
					
					//return Reply::redirect(route('superadmin.company.index'), __('messages.updatedSuccessfully'));
					
					$ncvaccess_quantity = $ncvaccess_used	= $ncvaccess_balance = $ncvaccess_startdate = $ncvaccess_enddate = $ncvaccess_status = '';    
					$ncvaccess_quantity		=	@$cvaccess_quantity 	? number_format(@$cvaccess_quantity): 0;
					$ncvaccess_used			=	@$cvaccess_used 		? number_format(@$cvaccess_used): 0;
					$ncvaccess_balance		=	@$cvaccess_balance 		? number_format(@$cvaccess_balance): 0;
					$ncvaccess_startdate	=	@$cvaccess_startdate 	? date("d M, Y", strtotime(@$cvaccess_startdate)): '';
					$ncvaccess_enddate		=	@$cvaccess_enddate 		? date("d M, Y", strtotime(@$cvaccess_enddate)): '';
					$ncvaccess_status		=   @$cvaccess_status == 1 	? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>': '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>'; 
					$datatable	=	'';
					$datatable	.=	'<tr>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">CV Access / Received</a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$ncvaccess_quantity.'</a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$ncvaccess_used.'</a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$ncvaccess_balance.'</a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$ncvaccess_startdate.'</a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$ncvaccess_enddate.' </a></td>
                    <td class="">'.@$ncvaccess_status.'</td>
                  </tr>';

					$n_newjobrequest_quantity	=	$n_newjobrequest_used	=	$n_newjobrequest_balance	=	$n_newjobrequest_startdate	=	$n_newjobrequest_enddate	=	$newjobrequest_status	=	'';
					$newjobrequest_quantity			=	@$request->newjobrequest_quantity;
					$newjobrequest_used				=	@$request->newjobrequest_used;
					$newjobrequest_balance			=	@$request->newjobrequest_balance;
					$newjobrequest_startdate		=	@$request->newjobrequest_startdate;
					$newjobrequest_enddate			=	@$request->newjobrequest_enddate;
					$newjobrequest_status			=	@$request->newjobrequest_status;
										

					$nusers_quantity = $nusers_used	= $nusers_balance = $nusers_startdate = $nusers_enddate = $nusers_status = '';    
					$nusers_quantity	=	@$users_quantity 	? number_format(@$users_quantity): 0;
					$nusers_used		=	@$users_used 		? number_format(@$users_used): 0;
					$nusers_balance		=	@$users_balance 	? number_format(@$users_balance): 0;
					$nusers_startdate	=	@$users_startdate 	? date("d M, Y", strtotime(@$users_startdate)): '';
					$nusers_enddate		=	@$users_enddate 	? date("d M, Y", strtotime(@$users_enddate)): '';
					$nusers_status		=   @$users_status == 1 ? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>': '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>'; 				
					$datatable	.='<tr>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6" >Users</a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$nusers_quantity.'</a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$nusers_used.'</a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$nusers_balance.'</a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$nusers_startdate.'</a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$nusers_enddate.' </a></td>
                    <td class="">'.@$nusers_status.'</td>
					</tr>';

					$nnewbenchclientrequest_quantity = $nnewbenchclientrequest_used	= $nnewbenchclientrequest_balance = $nnewbenchclientrequest_startdate = $nnewbenchclientrequest_enddate = $nnewbenchclientrequest_status = '';    
					$nnewbenchclientrequest_quantity	=	@$newbenchclientrequest_quantity 	? number_format(@$newbenchclientrequest_quantity): 0;
					$nnewbenchclientrequest_used		=	@$newbenchclientrequest_used 		? number_format(@$newbenchclientrequest_used): 0;
					$nnewbenchclientrequest_balance		=	@$newbenchclientrequest_balance 	? number_format(@$newbenchclientrequest_balance): 0;
					$nnewbenchclientrequest_startdate	=	@$newbenchclientrequest_startdate 	? date("d M, Y", strtotime(@$newbenchclientrequest_startdate)): '';
					$nnewbenchclientrequest_enddate		=	@$newbenchclientrequest_enddate 	? date("d M, Y", strtotime(@$newbenchclientrequest_enddate)): '';
					$nnewbenchclientrequest_status		=   @$newbenchclientrequest_status == 1 ? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>': '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>'; 
					$datatable	.='<tr>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">New Bench Client Request</a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$nnewbenchclientrequest_quantity.'</a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$nnewbenchclientrequest_used.'</a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$nnewbenchclientrequest_balance.'</a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$nnewbenchclientrequest_startdate.'</a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$nnewbenchclientrequest_enddate.'</a></td>
                    <td class="">'.@$nnewbenchclientrequest_status.'</td>
                    </tr>';  

					$nemails_quantity = $nemails_used	= $nemails_balance = $nemails_startdate = $nemails_enddate = $nemails_status = '';    
					$nemails_quantity		=	@$emails_quantity 	? number_format(@$emails_quantity): 0;
					$nemails_used			=	@$emails_used 		? number_format(@$emails_used): 0;
					$nemails_balance		=	@$emails_balance 	? number_format(@$emails_balance): 0;
					$nemails_startdate		=	@$emails_startdate 	? date("d M, Y", strtotime(@$emails_startdate)): '';
					$nemails_enddate		=	@$emails_enddate 	? date("d M, Y", strtotime(@$emails_enddate)): '';
					$nemails_status			=   @$emails_status == 1 ? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>': '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>'; 		 
				 	$datatable	.='<tr>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">Emails</a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$nemails_quantity.'</a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$nemails_used.'</a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$nemails_balance.'</a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$nemails_startdate.'</a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$nemails_enddate.'</a></td>
                    <td class="">'.@$nemails_status.'</td>
                    </tr>';
					$noldcvaccess_quantity = $noldcvaccess_used	= $noldcvaccess_balance = $noldcvaccess_startdate = $noldcvaccess_enddate = $noldcvaccess_status = '';    
					$noldcvaccess_quantity		=	@$oldcvaccess_quantity 	? number_format(@$oldcvaccess_quantity): 0;
					$noldcvaccess_used			=	@$oldcvaccess_used 		? number_format(@$oldcvaccess_used): 0;
					$noldcvaccess_balance		=	@$oldcvaccess_balance 	? number_format(@$oldcvaccess_balance): 0;
					$noldcvaccess_startdate		=	@$oldcvaccess_startdate 	? date("d M, Y", strtotime(@$oldcvaccess_startdate)): '';
					$noldcvaccess_enddate		=	@$oldcvaccess_enddate 	? date("d M, Y", strtotime(@$oldcvaccess_enddate)): '';
					$noldcvaccess_status			=   @$oldcvaccess_status == 1 ? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>': '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>'; 	
				 	$datatable	.='<tr>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">Old CV Access</a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$noldcvaccess_quantity.'</a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$noldcvaccess_used.'</a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$noldcvaccess_balance.'</a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$noldcvaccess_startdate.'</a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$noldcvaccess_enddate.'</a></td>
                    <td class="">'.@$noldcvaccess_status.'</td>
                    </tr>';
				
					if($companyRow->company_type != 3)
					{				  				   				  

										$datatable	.='';  	 					
										$nnewclientrequest_quantity = $nnewclientrequest_used	= $nnewclientrequest_balance = $nnewclientrequest_startdate = $nnewclientrequest_enddate = $nnewclientrequest_status = '';    
										$nnewclientrequest_quantity		=	@$newclientrequest_quantity 	? number_format(@$newclientrequest_quantity): 0;
										$nnewclientrequest_used			=	@$newclientrequest_used 		? number_format(@$newclientrequest_used): 0;
										$nnewclientrequest_balance		=	@$newclientrequest_balance 		? number_format(@$newclientrequest_balance): 0;
										$nnewclientrequest_startdate	=	@$newclientrequest_startdate 	? date("d M, Y", strtotime(@$newclientrequest_startdate)): '';
										$nnewclientrequest_enddate		=	@$newclientrequest_enddate 		? date("d M, Y", strtotime(@$newclientrequest_enddate)): '';
										$nnewclientrequest_status		=   @$newclientrequest_status == 1 	? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>': '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>'; 
										$datatable	.='<tr>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">Contract Recruitment Lead</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$nnewclientrequest_quantity.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$nnewclientrequest_used.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$nnewclientrequest_balance.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$nnewclientrequest_startdate.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$nnewclientrequest_enddate.'</a></td>
										<td class="">'.@$nnewclientrequest_status.'</td>
										</tr>';	
										$datatable	.='';  	 					
										$nnewclientrequest_quantity = $nnewclientrequest_used	= $nnewclientrequest_balance = $nnewclientrequest_startdate = $nnewclientrequest_enddate = $nnewclientrequest_status = '';    
										$nnewclientrequest_quantity		=	@$newclientrequest_quantity 	? number_format(@$newclientrequest_quantity): 0;
										$nnewclientrequest_used			=	@$newclientrequest_used 		? number_format(@$newclientrequest_used): 0;
										$nnewclientrequest_balance		=	@$newclientrequest_balance 		? number_format(@$newclientrequest_balance): 0;
										$nnewclientrequest_startdate	=	@$newclientrequest_startdate 	? date("d M, Y", strtotime(@$newclientrequest_startdate)): '';
										$nnewclientrequest_enddate		=	@$newclientrequest_enddate 		? date("d M, Y", strtotime(@$newclientrequest_enddate)): '';
										$nnewclientrequest_status		=   @$newclientrequest_status == 1 	? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>': '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>'; 
										$datatable	.='<tr>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">Fulltime Recruitment Leads</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$nnewclientrequest_quantity.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$nnewclientrequest_used.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$nnewclientrequest_balance.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$nnewclientrequest_startdate.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$nnewclientrequest_enddate.'</a></td>
										<td class="">'.@$nnewclientrequest_status.'</td>
										</tr>';	
									
										$datatable	.='';  	 					
										$nnewclientrequest_quantity = $nnewclientrequest_used	= $nnewclientrequest_balance = $nnewclientrequest_startdate = $nnewclientrequest_enddate = $nnewclientrequest_status = '';    
										$nnewclientrequest_quantity		=	@$newclientrequest_quantity 	? number_format(@$newclientrequest_quantity): 0;
										$nnewclientrequest_used			=	@$newclientrequest_used 		? number_format(@$newclientrequest_used): 0;
										$nnewclientrequest_balance		=	@$newclientrequest_balance 		? number_format(@$newclientrequest_balance): 0;
										$nnewclientrequest_startdate	=	@$newclientrequest_startdate 	? date("d M, Y", strtotime(@$newclientrequest_startdate)): '';
										$nnewclientrequest_enddate		=	@$newclientrequest_enddate 		? date("d M, Y", strtotime(@$newclientrequest_enddate)): '';
										$nnewclientrequest_status		=   @$newclientrequest_status == 1 	? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>': '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>'; 
										$datatable	.='<tr>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">Boost Profile Keywords</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$nnewclientrequest_quantity.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$nnewclientrequest_used.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$nnewclientrequest_balance.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$nnewclientrequest_startdate.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$nnewclientrequest_enddate.'</a></td>
										<td class="">'.@$nnewclientrequest_status.'</td>
									</tr>';	
									
																		
					}					
					
					return Reply::dataOnly(['status' => 'success', 'datatable'=> $datatable]); 			  
    }
	
	public function update(Request $request, $id) 
	{
		
					//echo '<pre>'; print_r($_REQUEST); exit;
					
					$company_id				= '';
					$company_id				=	$id;		
					$companyRow 			=	DB::table('companies')->select('*')->where('companies.id', $company_id)->first();
				
					$company_id		=	'';
					$company_id		=	$id;

					// dd($company_id);
					
					$cvaccess_quantity				=	$cvaccess_used	=	$cvaccess_balance	=	$cvaccess_startdate	=	$cvaccess_enddate	=	$cvaccess_status	=	'';

					$cvaccess_quantity				=	@$request->cvaccess_quantity;
					$cvaccess_used					=	@$request->cvaccess_used;
					$cvaccess_balance				=	@$request->cvaccess_balance;
					$cvaccess_startdate				=	@$request->cvaccess_startdate;
					$cvaccess_enddate				=	@$request->cvaccess_enddate;
					$cvaccess_status				=	@$request->cvaccess_status;
					
					$users_quantity					=	$users_used	=	$users_balance	=	$users_startdate	=	$users_enddate	=	$users_status	=	'';	
					$users_quantity					=	@$request->users_quantity;
					$users_used						=	@$request->users_used;
					$users_balance					=	@$request->users_balance;
					$users_startdate				=	@$request->users_startdate;
					$users_enddate					=	@$request->users_enddate;
					$users_status					=	@$request->users_status;
		
					$newclientrequest_quantity		=	$newclientrequest_used	=	$newclientrequest_balance	=	$newclientrequest_startdate	=	$newclientrequest_enddate	=	$newclientrequest_status='';
					$newclientrequest_quantity		=	@$request->newclientrequest_quantity;
					$newclientrequest_used			=	@$request->newclientrequest_used;
					$newclientrequest_balance		=	@$request->newclientrequest_balance;
					$newclientrequest_startdate		=	@$request->newclientrequest_startdate;
					$newclientrequest_enddate		=	@$request->newclientrequest_enddate;
					$newclientrequest_status		=	@$request->newclientrequest_status;
					
					$newbenchclientrequest_quantity	=	$newbenchclientrequest_used	=	$newbenchclientrequest_balance	=	$newbenchclientrequest_startdate	=	$newbenchclientrequest_enddate	=	$newbenchclientrequest_status	=	'';
					$newbenchclientrequest_quantity	=	@$request->newbenchclientrequest_quantity;
					$newbenchclientrequest_used		=	@$request->newbenchclientrequest_used;
					$newbenchclientrequest_balance	=	@$request->newbenchclientrequest_balance;
					$newbenchclientrequest_startdate=	@$request->newbenchclientrequest_startdate;
					$newbenchclientrequest_enddate	=	@$request->newbenchclientrequest_enddate;
					$newbenchclientrequest_status	=	@$request->newbenchclientrequest_status;
										
					$newjobrequest_quantity			=	$newjobrequest_used	=	$newjobrequest_balance	=	$newjobrequest_startdate	=	$newjobrequest_enddate	=	$newjobrequest_status	=	'';
					$newjobrequest_quantity			=	@$request->newjobrequest_quantity;
					$newjobrequest_used				=	@$request->newjobrequest_used;
					$newjobrequest_balance			=	@$request->newjobrequest_balance;
					$newjobrequest_startdate		=	@$request->newjobrequest_startdate;
					$newjobrequest_enddate			=	@$request->newjobrequest_enddate;
					$newjobrequest_status			=	@$request->newjobrequest_status;
					
					$emails_quantity				=	$emails_used	=	$emails_balance	=	$emails_startdate	=	$emails_enddate	=	$emails_status	=	'';					
					$emails_quantity				=	@$request->emails_quantity;
					$emails_used					=	@$request->emails_used;
					$emails_balance					=	@$request->emails_balance;
					$emails_startdate				=	@$request->emails_startdate;
					$emails_enddate					=	@$request->emails_enddate;
					$emails_status					=	@$request->emails_status;
				
					$oldcvaccess_quantity			=	$oldcvaccess_used	=	$oldcvaccess_balance	=	$oldcvaccess_startdate	=	$oldcvaccess_enddate	=	$oldcvaccess_status	=	'';				
					$oldcvaccess_quantity			=	@$request->oldcvaccess_quantity;
					$oldcvaccess_used				=	@$request->oldcvaccess_used;
					$oldcvaccess_balance			=	@$request->oldcvaccess_balance;
					$oldcvaccess_startdate			=	@$request->oldcvaccess_startdate;
					$oldcvaccess_enddate			=	@$request->oldcvaccess_enddate;
					$oldcvaccess_status				=	@$request->oldcvaccess_status;

					$internaldatabaseaccess_quantity					=	@$request->internaldatabaseaccess_quantity;
					$internaldatabaseaccess_used						=	@$request->internaldatabaseaccess_used;
					$internaldatabaseaccess_balance					=	@$request->internaldatabaseaccess_balance;
					$internaldatabaseaccess_status					=	@$request->internaldatabaseaccess_status;

					$interviewtracker_quantity					=	@$request->interviewtracker_quantity;
					$interviewtracker_used						=	@$request->interviewtracker_used;
					$interviewtracker_balance					=	@$request->interviewtracker_balance;
					$interviewtracker_status					=	@$request->interviewtracker_status;

					$Offertracker_quantity					=	@$request->Offertracker_quantity;
					$Offertracker_used						=	@$request->Offertracker_used;
					$Offertracker_balance					=	@$request->Offertracker_balance;
					$Offertracker_status					=	@$request->Offertracker_status;

					$Joiningtracker_quantity				=	@$request->Joiningtracker_quantity;
					$Joiningtracker_used					=	@$request->Joiningtracker_used;
					$Joiningtracker_balance					=	@$request->Joiningtracker_balance;
					$Joiningtracker_status					=	@$request->Joiningtracker_status;

					$PermissionforSHaringCVtoclienys_quantity				=	@$request->PermissionforSHaringCVtoclienys_quantity;
					$PermissionforSHaringCVtoclienys_used					=	@$request->PermissionforSHaringCVtoclienys_used;
					$PermissionforSHaringCVtoclienys_balance				=	@$request->PermissionforSHaringCVtoclienys_balance;
					$PermissionforSHaringCVtoclienys_status					=	@$request->PermissionforSHaringCVtoclienys_status;

					$PermamentRecruitmentLeadIndia_quantity					=	@$request->PermamentRecruitmentLeadIndia_quantity;
					$PermamentRecruitmentLeadIndia_used						=	@$request->PermamentRecruitmentLeadIndia_used;
					$PermamentRecruitmentLeadIndia_balance					=	@$request->PermamentRecruitmentLeadIndia_balance;
					$PermamentRecruitmentLeadIndia_status					=	@$request->PermamentRecruitmentLeadIndia_status;

					$PermamentRecruitmentLeadInternational_quantity					=	@$request->PermamentRecruitmentLeadInternational_quantity;
					$PermamentRecruitmentLeadInternational_used						=	@$request->PermamentRecruitmentLeadInternational_used;
					$PermamentRecruitmentLeadInternational_balance					=	@$request->PermamentRecruitmentLeadInternational_balance;
					$PermamentRecruitmentLeadInternational_status					=	@$request->PermamentRecruitmentLeadInternational_status;

					$ContractRecruitmentLeadIndia_quantity					=	@$request->ContractRecruitmentLeadIndia_quantity;
					$ContractRecruitmentLeadIndia_used						=	@$request->ContractRecruitmentLeadIndia_used;
					$ContractRecruitmentLeadIndia_balance					=	@$request->ContractRecruitmentLeadIndia_balance;
					$ContractRecruitmentLeadIndia_status					=	@$request->ContractRecruitmentLeadIndia_status;

					$Limitperdayforleadsconsumptions_quantity					=	@$request->Limitperdayforleadsconsumptions_quantity;
					$Limitperdayforleadsconsumptions_used						=	@$request->Limitperdayforleadsconsumptions_used;
					$Limitperdayforleadsconsumptions_balance					=	@$request->Limitperdayforleadsconsumptions_balance;
					$Limitperdayforleadsconsumptions_status					=	@$request->Limitperdayforleadsconsumptions_status;

					$ContractRecruitmentLeadInternational_quantity					=	@$request->ContractRecruitmentLeadInternational_quantity;
					$ContractRecruitmentLeadInternational_used						=	@$request->ContractRecruitmentLeadInternational_used;
					$ContractRecruitmentLeadInternational_balance					=	@$request->ContractRecruitmentLeadInternational_balance;
					$ContractRecruitmentLeadInternational_status					=	@$request->ContractRecruitmentLeadInternational_status;

					$BenchSalesIndia_quantity					=	@$request->BenchSalesIndia_quantity;
					$BenchSalesIndia_used						=	@$request->BenchSalesIndia_used;
					$BenchSalesIndia_balance					=	@$request->BenchSalesIndia_balance;
					$BenchSalesIndia_status					=	@$request->BenchSalesIndia_status;

					$BenchSalesInternational_quantity					=	@$request->BenchSalesInternational_quantity;
					$BenchSalesInternational_used						=	@$request->BenchSalesInternational_used;
					$BenchSalesInternational_balance					=	@$request->BenchSalesInternational_balance;
					$BenchSalesInternational_status					=	@$request->BenchSalesInternational_status;

					$BenchClientAcccessIndia_quantity					=	@$request->BenchClientAcccessIndia_quantity;
					$BenchClientAcccessIndia_used						=	@$request->BenchClientAcccessIndia_used;
					$BenchClientAcccessIndia_balance					=	@$request->BenchClientAcccessIndia_balance;
					$BenchClientAcccessIndia_status					=	@$request->BenchClientAcccessIndia_status;

					$BoostKeywordsInternational_quantity					=	@$request->BoostKeywordsInternational_quantity;
					$BoostKeywordsInternational_used						=	@$request->BoostKeywordsInternational_used;
					$BoostKeywordsInternational_balance					=	@$request->BoostKeywordsInternational_balance;
					$BoostKeywordsInternational_status					=	@$request->BoostKeywordsInternational_status;

					$BenchClientAccessInternational_quantity					=	@$request->BenchClientAccessInternational_quantity;
					$BenchClientAccessInternational_used						=	@$request->BenchClientAccessInternational_used;
					$BenchClientAccessInternational_balance					=	@$request->BenchClientAccessInternational_balance;
					$BenchClientAccessInternational_status					=	@$request->BenchClientAccessInternational_status;

					$BoostKeywordsIndia_quantity					=	@$request->BoostKeywordsIndia_quantity;
					$BoostKeywordsIndia_used						=	@$request->BoostKeywordsIndia_used;
					$BoostKeywordsIndia_balance					=	@$request->BoostKeywordsIndia_balance;
					$BoostKeywordsIndia_status					=	@$request->BoostKeywordsIndia_status;
					
					$boostkeywords_text			=	$boostkeywords_startdate	=	$boostkeywords_enddate	=	$boostkeywords_status	= $boostkeywords_quantity = $boostkeywords_used = $boostkeywords_balance = '';									
					
					if(!empty(@$request->boostkeywords_text)){
						$boostkeywords_text				=	@$request->boostkeywords_text;
						$boostkeywords_startdate		=	@$request->boostkeywords_startdate;
						$boostkeywords_enddate			=	@$request->boostkeywords_enddate;
						$boostkeywords_status			=	@$request->boostkeywords_status;
						
						$boostkeywords_quantity			=	@$request->boostkeywords_quantity;
						$boostkeywords_used				=	@$request->boostkeywords_used;
						$boostkeywords_balance			=	@$request->boostkeywords_balance;
						
						//$boostkeywords 				=	@$request->boostkeywords;
						$string_keywords				=	'';
						$boostkeywords_text 			=	json_decode($boostkeywords_text, true);
						$tagsArr 						=	array_column($boostkeywords_text, 'value');
						if(!empty($tagsArr)){
							$string_keywords			=	implode(',', $tagsArr);
						}
					}						 					
					//echo '<pre>'; print_r($_REQUEST); exit;   @$request->boostkeywords_text; 
			
					$updateArr 			= array(
													'cvaccess_quantity' 			=> @$cvaccess_quantity,
													'cvaccess_used' 				=> @$cvaccess_used,
													'cvaccess_balance' 				=> @$cvaccess_balance,
													// 'cvaccess_startdate' 			=> @$cvaccess_startdate,  
													// 'cvaccess_enddate' 				=> @$cvaccess_enddate,  
													'cvaccess_status' 				=> @$cvaccess_status,
													
													'users_quantity' 				=> @$users_quantity,
													'users_used' 					=> @$users_used,
													'users_balance' 				=> @$users_balance,
													// 'users_startdate' 				=> @$users_startdate,
													// 'users_enddate' 				=> @$users_enddate,
													'users_status' 					=> @$users_status,
													
													'newclientrequest_quantity' 	=> @$newclientrequest_quantity,
													'newclientrequest_used' 		=> @$newclientrequest_used,
													'newclientrequest_balance' 		=> @$newclientrequest_balance,
													'newclientrequest_startdate'	=> @$newclientrequest_startdate,
													// 'newclientrequest_enddate' 		=> @$newclientrequest_enddate,
													// 'newclientrequest_status' 		=> @$newclientrequest_status,
													
													'newbenchclientrequest_quantity' => @$newbenchclientrequest_quantity,
													'newbenchclientrequest_used' 	 => @$newbenchclientrequest_used,
													'newbenchclientrequest_balance'  => @$newbenchclientrequest_balance,
													// 'newbenchclientrequest_startdate'=> @$newbenchclientrequest_startdate,
													// 'newbenchclientrequest_enddate'  => @$newbenchclientrequest_enddate,
													'newbenchclientrequest_status' 	 => @$newbenchclientrequest_status,
													
													'newjobrequest_quantity' 		=> @$newjobrequest_quantity,
													'newjobrequest_used' 			=> @$newjobrequest_used,
													'newjobrequest_balance' 		=> @$newjobrequest_balance,
													// 'newjobrequest_startdate' 		=> @$newjobrequest_startdate,
													// 'newjobrequest_enddate' 		=> @$newjobrequest_enddate,
													'newjobrequest_status' 			=> @$newjobrequest_status,
													
													 'emails_quantity' 				=> @$emails_quantity,
													 'emails_used' 					=> @$emails_used,
													 'emails_balance' 				=> @$emails_balance,
													//  'emails_startdate' 			=> @$emails_startdate,
													//  'emails_enddate' 				=> @$emails_enddate,
													 'emails_status' 				=> @$emails_status,
													 
													 'oldcvaccess_quantity' 		=> @$oldcvaccess_quantity,
													 'oldcvaccess_used' 			=> @$oldcvaccess_used,
													 'oldcvaccess_balance' 			=> @$oldcvaccess_balance,
													//  'oldcvaccess_startdate' 		=> @$oldcvaccess_startdate,
													//  'oldcvaccess_enddate' 			=> @$oldcvaccess_enddate,
													 'oldcvaccess_status' 			=> @$oldcvaccess_status,
													 
													  'boostkeywords_quantity' 			=> @$boostkeywords_quantity,
													  'boostkeywords_used' 				=> @$boostkeywords_used,
													  'boostkeywords_balance' 			=> @$boostkeywords_balance,
													//   'boostkeywords_startdate' 		=> @$boostkeywords_startdate,
													//   'boostkeywords_enddate' 			=> @$boostkeywords_enddate,
													  'boostkeywords_status' 			=> @$boostkeywords_status,
													  
													  'boostkeywords_text' 				=> @$string_keywords,

													'internaldatabaseaccess_quantity' 			=> @$internaldatabaseaccess_quantity,
													'internaldatabaseaccess_used' 				=> @$internaldatabaseaccess_used,
													'internaldatabaseaccess_balance' 				=> @$internaldatabaseaccess_balance,
													'internaldatabaseaccess_status' 				=> @$internaldatabaseaccess_status,

													'interviewtracker_quantity' 			=> @$interviewtracker_quantity,
													'interviewtracker_used' 				=> @$interviewtracker_used,
													'interviewtracker_balance' 				=> @$interviewtracker_balance,
													'interviewtracker_status' 				=> @$interviewtracker_status,

													'Offertracker_quantity' 			=> @$Offertracker_quantity,
													'Offertracker_used' 				=> @$Offertracker_used,
													'Offertracker_balance' 				=> @$Offertracker_balance,
													'Offertracker_status' 				=> @$Offertracker_status,

													'Joiningtracker_quantity' 			=> @$Joiningtracker_quantity,
													'Joiningtracker_used' 				=> @$Joiningtracker_used,
													'Joiningtracker_balance' 				=> @$Joiningtracker_balance,
													'Joiningtracker_status' 				=> @$Joiningtracker_status,

													'PermissionforSHaringCVtoclienys_quantity' 			=> @$PermissionforSHaringCVtoclienys_quantity,
													'PermissionforSHaringCVtoclienys_used' 				=> @$PermissionforSHaringCVtoclienys_used,
													'PermissionforSHaringCVtoclienys_balance' 				=> @$PermissionforSHaringCVtoclienys_balance,
													'PermissionforSHaringCVtoclienys_status' 				=> @$PermissionforSHaringCVtoclienys_status,

													'PermamentRecruitmentLeadIndia_quantity' 			=> @$PermamentRecruitmentLeadIndia_quantity,
													'PermamentRecruitmentLeadIndia_used' 				=> @$PermamentRecruitmentLeadIndia_used,
													'PermamentRecruitmentLeadIndia_balance' 				=> @$PermamentRecruitmentLeadIndia_balance,
													'PermamentRecruitmentLeadIndia_status' 				=> @$PermamentRecruitmentLeadIndia_status,

													'PermamentRecruitmentLeadInternational_quantity' 			=> @$PermamentRecruitmentLeadInternational_quantity,
													'PermamentRecruitmentLeadInternational_used' 				=> @$PermamentRecruitmentLeadInternational_used,
													'PermamentRecruitmentLeadInternational_balance' 				=> @$PermamentRecruitmentLeadInternational_balance,
													'PermamentRecruitmentLeadInternational_status' 				=> @$PermamentRecruitmentLeadInternational_status,

													'ContractRecruitmentLeadIndia_quantity' 			=> @$ContractRecruitmentLeadIndia_quantity,
													'ContractRecruitmentLeadIndia_used' 				=> @$ContractRecruitmentLeadIndia_used,
													'ContractRecruitmentLeadIndia_balance' 				=> @$ContractRecruitmentLeadIndia_balance,
													'ContractRecruitmentLeadIndia_status' 				=> @$ContractRecruitmentLeadIndia_status,

													'BoostKeywordsInternational_quantity' 			=> @$BoostKeywordsInternational_quantity,
													'BoostKeywordsInternational_used' 				=> @$BoostKeywordsInternational_used,
													'BoostKeywordsInternational_balance' 				=> @$BoostKeywordsInternational_balance,
													'BoostKeywordsInternational_status' 				=> @$BoostKeywordsInternational_status,

													'ContractRecruitmentLeadInternational_quantity' 			=> @$ContractRecruitmentLeadInternational_quantity,
													'ContractRecruitmentLeadInternational_used' 				=> @$ContractRecruitmentLeadInternational_used,
													'ContractRecruitmentLeadInternational_balance' 				=> @$ContractRecruitmentLeadInternational_balance,
													'ContractRecruitmentLeadInternational_status' 				=> @$ContractRecruitmentLeadInternational_status,

													'BenchSalesIndia_quantity' 			=> @$BenchSalesIndia_quantity,
													'BenchSalesIndia_used' 				=> @$BenchSalesIndia_used,
													'BenchSalesIndia_balance' 				=> @$BenchSalesIndia_balance,
													'BenchSalesIndia_status' 				=> @$BenchSalesIndia_status,

													'BenchSalesInternational_quantity' 			=> @$BenchSalesInternational_quantity,
													'BenchSalesInternational_used' 				=> @$BenchSalesInternational_used,
													'BenchSalesInternational_balance' 				=> @$BenchSalesInternational_balance,
													'BenchSalesInternational_status' 				=> @$BenchSalesInternational_status,

													'BenchClientAcccessIndia_quantity' 			=> @$BenchClientAcccessIndia_quantity,
													'BenchClientAcccessIndia_used' 				=> @$BenchClientAcccessIndia_used,
													'BenchClientAcccessIndia_balance' 				=> @$BenchClientAcccessIndia_balance,
													'BenchClientAcccessIndia_status' 				=> @$BenchClientAcccessIndia_status,

													'BenchClientAccessInternational_quantity' 			=> @$BenchClientAccessInternational_quantity,
													'BenchClientAccessInternational_used' 				=> @$BenchClientAccessInternational_used,
													'BenchClientAccessInternational_balance' 				=> @$BenchClientAccessInternational_balance,
													'BenchClientAccessInternational_status' 				=> @$BenchClientAccessInternational_status,

													'BoostKeywordsIndia_quantity' 			=> @$BoostKeywordsIndia_quantity,
													'BoostKeywordsIndia_used' 				=> @$BoostKeywordsIndia_used,
													'BoostKeywordsIndia_balance' 				=> @$BoostKeywordsIndia_balance,
													'BoostKeywordsIndia_status' 				=> @$BoostKeywordsIndia_status,

													'Limitperdayforleadsconsumptions_quantity' 			=> @$Limitperdayforleadsconsumptions_quantity,
													'Limitperdayforleadsconsumptions_used' 				=> @$Limitperdayforleadsconsumptions_used,
													'Limitperdayforleadsconsumptions_balance' 				=> @$Limitperdayforleadsconsumptions_balance,
													'Limitperdayforleadsconsumptions_status' 				=> @$Limitperdayforleadsconsumptions_status,

													

			
													      
												);
					// dd($updateArr);
					
					DB::table('package_active')->where('company_id', $company_id)->limit(1)->update($updateArr);
					
					
					//return Reply::redirect(route('superadmin.company.index'), __('messages.updatedSuccessfully'));
					
					$ncvaccess_quantity = $ncvaccess_used	= $ncvaccess_balance = $ncvaccess_startdate = $ncvaccess_enddate = $ncvaccess_status = '';    
					$ncvaccess_quantity		=	@$cvaccess_quantity 	? number_format(@$cvaccess_quantity): 0;
					$ncvaccess_used			=	@$cvaccess_used 		? number_format(@$cvaccess_used): 0;
					$ncvaccess_balance		=	@$cvaccess_balance 		? number_format(@$cvaccess_balance): 0;
					$ncvaccess_startdate	=	@$cvaccess_startdate 	? date("d M, Y", strtotime(@$cvaccess_startdate)): '';
					$ncvaccess_enddate		=	@$cvaccess_enddate 		? date("d M, Y", strtotime(@$cvaccess_enddate)): '';
					$ncvaccess_status		=   @$cvaccess_status == 1 	? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>': '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>'; 
					$datatable	=	'';
					$datatable	.=	'<tr>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">CV Access / Received</a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$ncvaccess_quantity.'</a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$ncvaccess_used.'</a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$ncvaccess_balance.'</a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$ncvaccess_startdate.'</a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$ncvaccess_enddate.' </a></td>
                    <td class="">'.@$ncvaccess_status.'</td>
                  	</tr>';
					

					$nusers_quantity = $nusers_used	= $nusers_balance = $nusers_startdate = $nusers_enddate = $nusers_status = '';    
					$nusers_quantity	=	@$users_quantity 	? number_format(@$users_quantity): 0;
					$nusers_used		=	@$users_used 		? number_format(@$users_used): 0;
					$nusers_balance		=	@$users_balance 	? number_format(@$users_balance): 0;
					$nusers_startdate	=	@$users_startdate 	? date("d M, Y", strtotime(@$users_startdate)): '';
					$nusers_enddate		=	@$users_enddate 	? date("d M, Y", strtotime(@$users_enddate)): '';
					$nusers_status		=   @$users_status == 1 ? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>': '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>'; 				
					$datatable	.='<tr>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6" >Users</a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$nusers_quantity.'</a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$nusers_used.'</a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$nusers_balance.'</a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$nusers_startdate.'</a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$nusers_enddate.' </a></td>
                    <td class="">'.@$nusers_status.'</td>
					</tr>';

					$nnewbenchclientrequest_quantity = $nnewbenchclientrequest_used	= $nnewbenchclientrequest_balance = $nnewbenchclientrequest_startdate = $nnewbenchclientrequest_enddate = $nnewbenchclientrequest_status = '';    
					$nnewbenchclientrequest_quantity	=	@$newbenchclientrequest_quantity 	? number_format(@$newbenchclientrequest_quantity): 0;
					$nnewbenchclientrequest_used		=	@$newbenchclientrequest_used 		? number_format(@$newbenchclientrequest_used): 0;
					$nnewbenchclientrequest_balance		=	@$newbenchclientrequest_balance 	? number_format(@$newbenchclientrequest_balance): 0;
					$nnewbenchclientrequest_startdate	=	@$newbenchclientrequest_startdate 	? date("d M, Y", strtotime(@$newbenchclientrequest_startdate)): '';
					$nnewbenchclientrequest_enddate		=	@$newbenchclientrequest_enddate 	? date("d M, Y", strtotime(@$newbenchclientrequest_enddate)): '';
					$nnewbenchclientrequest_status		=   @$newbenchclientrequest_status == 1 ? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>': '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>'; 
					$datatable	.='<tr>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">New Bench Client Request</a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$nnewbenchclientrequest_quantity.'</a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$nnewbenchclientrequest_used.'</a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$nnewbenchclientrequest_balance.'</a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$nnewbenchclientrequest_startdate.'</a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$nnewbenchclientrequest_enddate.'</a></td>
                    <td class="">'.@$nnewbenchclientrequest_status.'</td>
                    </tr>';  

					$nemails_quantity = $nemails_used	= $nemails_balance = $nemails_startdate = $nemails_enddate = $nemails_status = '';    
					$nemails_quantity		=	@$emails_quantity 	? number_format(@$emails_quantity): 0;
					$nemails_used			=	@$emails_used 		? number_format(@$emails_used): 0;
					$nemails_balance		=	@$emails_balance 	? number_format(@$emails_balance): 0;
					$nemails_startdate		=	@$emails_startdate 	? date("d M, Y", strtotime(@$emails_startdate)): '';
					$nemails_enddate		=	@$emails_enddate 	? date("d M, Y", strtotime(@$emails_enddate)): '';
					$nemails_status			=   @$emails_status == 1 ? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>': '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>'; 		 
				 	$datatable	.='<tr>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">Emails</a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$nemails_quantity.'</a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$nemails_used.'</a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$nemails_balance.'</a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$nemails_startdate.'</a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$nemails_enddate.'</a></td>
                    <td class="">'.@$nemails_status.'</td>
                  	</tr>';

					$noldcvaccess_quantity = $noldcvaccess_used	= $noldcvaccess_balance = $noldcvaccess_startdate = $noldcvaccess_enddate = $noldcvaccess_status = '';    
					$noldcvaccess_quantity		=	@$oldcvaccess_quantity 	? number_format(@$oldcvaccess_quantity): 0;
					$noldcvaccess_used			=	@$oldcvaccess_used 		? number_format(@$oldcvaccess_used): 0;
					$noldcvaccess_balance		=	@$oldcvaccess_balance 	? number_format(@$oldcvaccess_balance): 0;
					$noldcvaccess_startdate		=	@$oldcvaccess_startdate 	? date("d M, Y", strtotime(@$oldcvaccess_startdate)): '';
					$noldcvaccess_enddate		=	@$oldcvaccess_enddate 	? date("d M, Y", strtotime(@$oldcvaccess_enddate)): '';
					$noldcvaccess_status			=   @$oldcvaccess_status == 1 ? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>': '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>'; 	
				 	$datatable	.='<tr>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">Old CV Access</a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$noldcvaccess_quantity.'</a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$noldcvaccess_used.'</a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$noldcvaccess_balance.'</a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$noldcvaccess_startdate.'</a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$noldcvaccess_enddate.'</a></td>
                    <td class="">'.@$noldcvaccess_status.'</td>
                  	</tr>';				  				   				  
				 
					if($companyRow->company_type != 3){
									
										$nnewclientrequest_quantity = $nnewclientrequest_used	= $nnewclientrequest_balance = $nnewclientrequest_startdate = $nnewclientrequest_enddate = $nnewclientrequest_status = '';    
										$nnewclientrequest_quantity		=	@$newclientrequest_quantity 	? number_format(@$newclientrequest_quantity): 0;
										$nnewclientrequest_used			=	@$newclientrequest_used 		? number_format(@$newclientrequest_used): 0;
										$nnewclientrequest_balance		=	@$newclientrequest_balance 		? number_format(@$newclientrequest_balance): 0;
										$nnewclientrequest_startdate	=	@$newclientrequest_startdate 	? date("d M, Y", strtotime(@$newclientrequest_startdate)): '';
										$nnewclientrequest_enddate		=	@$newclientrequest_enddate 		? date("d M, Y", strtotime(@$newclientrequest_enddate)): '';
										$nnewclientrequest_status		=   @$newclientrequest_status == 1 	? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>': '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>'; 
										$datatable	.='<tr>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">Contract Recruitment Lead</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$nnewclientrequest_quantity.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$nnewclientrequest_used.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$nnewclientrequest_balance.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$nnewclientrequest_startdate.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$nnewclientrequest_enddate.'</a></td>
										<td class="">'.@$nnewclientrequest_status.'</td>
										</tr>';

										$nnewjobrequest_quantity = $nnewjobrequest_used	= $nnewjobrequest_balance = $nnewjobrequest_startdate = $nnewjobrequest_enddate = $nnewjobrequest_status = '';    
										$nnewjobrequest_quantity	=	@$newjobrequest_quantity 	? number_format(@$newjobrequest_quantity): 0;
										$nnewjobrequest_used		=	@$newjobrequest_used 		? number_format(@$newjobrequest_used): 0;
										$nnewjobrequest_balance		=	@$newjobrequest_balance 	? number_format(@$newjobrequest_balance): 0;
										$nnewjobrequest_startdate	=	@$newjobrequest_startdate 	? date("d M, Y", strtotime(@$newjobrequest_startdate)): '';
										$nnewjobrequest_enddate		=	@$newjobrequest_enddate 	? date("d M, Y", strtotime(@$newjobrequest_enddate)): '';
										$nnewjobrequest_status		=   @$newjobrequest_status == 1 ? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>': '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>'; 
										$datatable	.='<tr>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">Fulltime Recruitment Leads</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$nnewjobrequest_quantity.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$nnewjobrequest_used.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$nnewjobrequest_balance.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$nnewjobrequest_startdate.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$nnewjobrequest_enddate.'</a></td>
										<td class="">'.@$nnewjobrequest_status.'</td>
										</tr>';

										$boostkeywords 				=	@$request->boostkeywords_text;
										$string_version  = '';
										if(!empty($boostkeywords)){
										$boostkeywords 				=	json_decode($boostkeywords, true);
										$tagsArr 					=	array_column($boostkeywords, 'value');
										$string_version 			= 	implode(',', $tagsArr);	
										}
										
										$nnewjobrequest_quantity 	= 	$nnewjobrequest_used	= $nnewjobrequest_balance = $nboostkeywords_startdate = $nboostkeywords_enddate = $nboostkeywords_status = '';    
										$nboostkeywords_status		=	@$boostkeywords_status 	? number_format(@$boostkeywords_status): 0;

										$nboostkeywords_startdate	=	@$boostkeywords_startdate 	? date("d M, Y", strtotime(@$boostkeywords_startdate)): '';
										$nboostkeywords_enddate		=	@$boostkeywords_enddate 	? date("d M, Y", strtotime(@$boostkeywords_enddate)): '';
										$nboostkeywords_status		=   @$boostkeywords_status == 1 ? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>': '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>'; 
										$datatable	.='<tr>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">Boost Profile Keywords</a></td>
										<td colspan="3"><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$string_version.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$nnewjobrequest_startdate.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$nboostkeywords_enddate.'</a></td>
										<td class="">'.@$nboostkeywords_status.'</td>
										</tr>';
									
					}				  
							  				  
				  	$datatable	.='';  				  					
					return Reply::dataOnly(['status' => 'success', 'datatable'=> $datatable]); 				
        
    }

}
