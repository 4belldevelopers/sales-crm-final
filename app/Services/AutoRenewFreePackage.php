<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Helper\Reply;
use Illuminate\Support\Facades\Storage;
use App\Helper\GeneralMethod;
use App\Models\Job;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
use App\Models\Client;
use App\Jobs\QueueNotificationMailService;
use Carbon\Carbon;
use App\Services\MailService;
use App\Services\ElasticService;
use Illuminate\Support\Facades\Mail;

class AutoRenewFreePackage
{
    protected $mailService;
    protected $elasticService;

    public function __construct(MailService $mailService,ElasticService $elasticService)
    {
      
        $this->mailService = $mailService;
        $this->elasticService = $elasticService;
        
    }

    /**
     * Close expired active jobs for a company
     */
    public function autoRenewFreePackageCompany(int $companyid): int
    {
      //    dd($companyid);
       $company_coins_free = DB::table('coin_packages')
            ->where('coin_packages.custome_pck',2)
            ->select('coin_packages.*')
            ->get()
            ->map(function ($coinOrder) {
							$coinOrder->coin_order_events = DB::table('coin_events')
								->where('event_type', $coinOrder->event_type)
								->select('coin_events.*')
								->where('status','=',1)
								->get()
								->toArray();
							return (array) $coinOrder;
			})
            ->toArray();

            foreach($company_coins_free as $free_package){
                    $free_package = (object) $free_package;
                    $event_type =  $free_package->event_type;
                    $company_coins_check = DB::table('company_coins')->where('company_id',$companyid)->where('event_type',$event_type)->first();


                    $free_update_date = Carbon::parse($company_coins_check->end_date);
                    $today = Carbon::today();
                    $currentMonth = $today->month;
                    $currentYear = $today->year;
                    $diffInDays = abs($today->diffInDays($free_update_date));
                    $days = 30;
                    $specificDay = 30;
                    $currency = GeneralMethod::convertINRToUSD();
                    if (Carbon::today()->gt(Carbon::parse($company_coins_check->end_date))) {
                        foreach($company_coins_free as $package_sales){
                            $currentDate = date("Y-m-d");
                            $newDate = date("Y-m-d", strtotime($currentDate . " +".$days." days"));
                            
                            $coin_orders = [
                                'coin_package_id'=>$package_sales['id'],
                                "currency" =>$currency,
                                'price'=>($currency=='INR') ? $package_sales['price'] : $package_sales['price_usd'],
                                'coins'=>$package_sales['coins'],
                                'days'=>$package_sales['days'],
                                'event_type'=>$package_sales['event_type'],
                                'company_id'=>$companyid,
                                'discount'=>$package_sales['discount'],
                                'status'=>1,
                                'created_at'=>date("Y-m-d H:i:s"),
                                'updated_at'=>date("Y-m-d H:i:s")	
                            ];
                            $coin_order_id = DB::table('coin_orders')->insertGetId($coin_orders);
                            $response = $this->elasticService->getClient()->update([
                                'index' => ELASTIC_COIN_ORDERS,
                                'id'    => $coin_order_id,
                                'retry_on_conflict' => 5,
                                'body'  => [
                                    'doc' => [ 
                                        'id' =>  $coin_order_id,
                                        'coin_package_id'=>$package_sales['id'],
                                        "currency" =>$currency,
                                        'price'=>($currency=='INR') ? $package_sales['price'] : $package_sales['price_usd'],
                                        'coins'=>$package_sales['coins'],
                                        'days'=> $package_sales['days'],
                                        'event_type'=>$package_sales['event_type'],
                                        'company_id'=>$companyid,
                                        'discount'=>$package_sales['discount'],
                                        'status'=>1,
                                        'updated_at' => now()->toIso8601String(),
                                        'created_at' => now()->toIso8601String()
                                    ],
                                    'doc_as_upsert' => true,
                                    'detect_noop' => false
                                ],
                            ]);
                            foreach($package_sales['coin_order_events'] as $package_value){
                                $coin_order_events = [
                                    'company_id'=>$companyid,
                                    'coin_event_id'=>$package_value->id,
                                    'deductions'=>$package_value->default_rate,
                                    'deduction_type'=>$package_value->deduction_type,
                                    'coin_order_id'=>$coin_order_id,
                                    'required_coin'=>$package_value->minimum_required,
                                    'event_type'=>$package_value->event_type
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
                                            'company_id'=>$companyid,
                                            'coin_event_id'=>$package_value->id,
                                            'deductions'=>$package_value->default_rate,
                                            'deduction_type'=>$package_value->deduction_type,
                                            'coin_order_id'=>$coin_order_id,
                                            'required_coin'=>$package_value->minimum_required,
                                            'event_type'=>$package_value->event_type
                                        ],
                                        'doc_as_upsert' => true,
                                        'detect_noop' => false
                                    ],
                                ]);
                            }
                            $company_coins = [
                                    'coin_order_id'=>$coin_order_id,
                                    'company_id'=>$companyid,
                                    'event_type'=>$package_sales['event_type'],
                                    "currency" =>$currency,
                                    'amount'=>($currency=='INR') ? $package_sales['price'] : $package_sales['price_usd'],
                                    'discount'=>$package_sales['discount'],
                                    'coins'=>$package_sales['coins'],
                                    'actual_coins'=>$package_sales['coins'],
                                    'balance_coin'=>0,
                                    'status'=>'valid',
                                    'free_pack_status'=>'active',
                                    'start_date'=>$currentDate,
                                    'end_date'=>$newDate,
                                    'free_update_date'=>$currentDate,
                                    'created_at'=>date("Y-m-d H:i:s"),
                                    'updated_at'=>date("Y-m-d H:i:s")				
                            ];
                            DB::table('company_coins')->where('company_id',$companyid)->where('event_type',$package_sales['event_type'])->update($company_coins);

                            $response = $this->elasticService->getClient()->update([
                                        'index' => ELASTIC_COMPANY_COINS,
                                        'id'    => $companyid,
                                        'retry_on_conflict' => 5,
                                        'body'  => [
                                            'doc' => [ 
                                                'id' =>  $companyid,
                                                'coin_order_id' => $coin_order_id,
                                                'company_id' => $companyid,
                                                'event_type' => $package_sales['event_type'],
                                                'currency' => $currency,
                                                'amount' => ($currency=='INR') ? $package_sales['price'] : $package_sales['price_usd'],
                                                'discount' => $package_sales['discount'],
                                                'coins' => $package_sales['coins'],
                                                'actual_coins' => $package_sales['coins'],
                                                'balance_coin' => 0,
                                                'status' => 'valid',
                                                'free_pack_status' => 'active',
                                                'start_date' => ($currentDate) ? Carbon::parse($currentDate)->toIso8601String() : null,
                                                'end_date' => ($newDate) ? Carbon::parse($newDate)->toIso8601String() : null,
                                                'free_update_date' => ($currentDate) ? Carbon::parse($currentDate)->toIso8601String() : null,
                                                'auto_team_coins' => now()->toIso8601String(),
                                                'auto_job_coins' => now()->toIso8601String(),
                                                'updated_at' => now()->toIso8601String(),
                                                'created_at' => now()->toIso8601String(),
                                            ],
                                            'doc_as_upsert' => true,
                                            'detect_noop' => false
                                        ],
                                    ]);

                                    
                                    $coinEvents = DB::table('coin_orders')
                                    ->join('coin_packages','coin_packages.id','=','coin_orders.coin_package_id')
                                    ->join('coin_package_types','coin_package_types.id','=','coin_packages.coin_package_type_id')
                                    ->where('coin_orders.id',$coin_order_id)
                                    ->select('coin_package_types.title')->first();
                                    $coinEventspackage = ($coinEvents) ? $coinEvents->title : '';

                                    $coin_transection_logs = [
                                            'coin_order_id'=>$coin_order_id,
                                            'from_company_id'=>$companyid,
                                            'company_id'=>$companyid,
                                            'event_by'=>1,
                                            'event'=> $coinEventspackage. " ",
                                            "currency" =>$currency,
                                            'amount'=>($currency=='INR') ? $package_sales['price'] : $package_sales['price_usd'],
                                            'discount'=>$package_sales['discount'],
                                            'coins'=>$package_sales['coins'],
                                            'transection_type'=>'credit',
                                            'settlement_coins'=>0,
                                            'event_type'=>$package_sales['event_type'],
                                            'start_date'=>$currentDate,
                                            'end_date'=>$newDate,
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
                                                    'from_company_id'=>$companyid,
                                                    'company_id'=>$companyid,
                                                    'event_by'=>1,
                                                    'event'=> $coinEventspackage. " ",
                                                    "currency" =>$currency,
                                                    'amount'=>($currency=='INR') ? $package_sales['price'] : $package_sales['price_usd'],
                                                    'discount'=>$package_sales['discount'],
                                                    'coins'=>$package_sales['coins'],
                                                    'transection_type'=>'credit',
                                                    'settlement_coins'=>0,
                                                    'event_type'=>$package_sales['event_type'],
                                                    'start_date' => ($currentDate) ? Carbon::parse($currentDate)->toIso8601String() : null,
                                                    'end_date' => ($newDate) ? Carbon::parse($newDate)->toIso8601String()  : null,
                                                    'created_at' => now()->toIso8601String(),
                                                    'updated_at' => now()->toIso8601String()
                                                ],
                                                'doc_as_upsert' => true,
                                                'detect_noop' => false
                                            ],
                                    ]);

                            //    dd($currentDate,$newDate);
                        }
                        

                    }

            }
       return true;
    }
    
}
