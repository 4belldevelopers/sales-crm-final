<?php
/*** ########################################################
Controller    	: 	ManageSubscriptionController
Purmose			: 
Author			:	Abu
Company			:	4Bell
Date			:	05-April-2021	
########################################################** */

namespace App\Http\Controllers\ServiceCompany;

use App\Models\Company;
use App\Models\CompanyPackage;
use App\Models\GlobalSetting;
use App\Helper\Reply;
use App\Http\Requests\Payment\PaymentRequest;
use App\Models\PaymentSetting;
use App\PaypalInvoice;
use App\Models\RazorpayInvoice;
use App\Models\RazorpaySubscription;
use App\Traits\StripeSettings;
use Carbon\Carbon;
use App\Jobs\QueueNotificationMailService;
use Illuminate\Http\Request;
use App\Helper\GeneralMethod;
use App\Package;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use PayPal\Api\Agreement;
use PayPal\Api\AgreementStateDescriptor;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use Razorpay\Api\Api;
use Stripe\Subscription;
use App\Services\MailService;
use Yajra\DataTables\DataTables;

class ManageSubscriptionController extends ServiceCompanyBaseController
{
    use StripeSettings;
    protected $mailService;
    public function __construct(MailService $mailService)
    {
        parent::__construct();
        $this->pageTitle 	= 'menu.subscription';
        $this->pageIcon 	= 'ti-settings';
        $this->mailService = $mailService;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
	 
    public function index()
    {      	
        // die;	
        		
		$company_id       			=	auth()->user()->company_id;
		$this->package_active		=	DB:: table('package_active')->select('package_active.*')->where('package_active.company_id', '=', $company_id)->first();	
        
        $this->company = Company::withoutGlobalScope('company')->findOrFail($company_id);
        $this->companyPackage = CompanyPackage::activePackage($company_id);

        $this->account = DB::table('companies')->select('*')->where('id', $company_id)->first();
        $this->package_active_all = DB::table('package_active')->join('package_invoice','package_invoice.id','=','package_active.package_invoice_id')->join('package_order_items','package_order_items.invoice_id','=','package_invoice.id')->join('package_features','package_features.id','=','package_order_items.feature_id')->select('package_active.*','package_features.title','package_features.field_name','package_features.id as gg_is')->where('package_active.company_id', $company_id)->get();

        $this->package_active_details = DB::table('package_active')->join('package_invoice','package_invoice.id','=','package_active.package_invoice_id')->join('packages','packages.id','=','package_invoice.package_id')->select('package_active.*','packages.name')->where('package_active.company_id', $company_id)->first();

        $this->company_coins = DB::table('company_coins')
        ->join('coin_orders','coin_orders.id','=','company_coins.coin_order_id')
        ->join('coin_packages','coin_packages.id','=','coin_orders.coin_package_id')
        ->join('coin_package_types','coin_package_types.id','=','coin_packages.coin_package_type_id')
		->where('company_coins.company_id',$company_id)
        ->where('company_coins.event_type','operational')
        ->where('coin_orders.status',1)
        ->select('company_coins.*','coin_package_types.title','coin_packages.custome_pck','coin_orders.id as coin_order_id','coin_orders.coins as order_coins')
        ->get()
        ->map(function ($coinOrder) {
            $coinOrder->coin_order_events = DB::table('coin_order_events')
            ->join('coin_events','coin_events.id','=','coin_order_events.coin_event_id')
                ->where('coin_order_id', $coinOrder->coin_order_id)
                ->select('coin_order_events.*','coin_events.title','coin_events.description')
                ->get()
                ->toArray();
            return (array) $coinOrder;
        })->toArray();

        $this->company_coins_sales = DB::table('company_coins')
        ->join('coin_orders','coin_orders.id','=','company_coins.coin_order_id')
        ->join('coin_packages','coin_packages.id','=','coin_orders.coin_package_id')
        ->join('coin_package_types','coin_package_types.id','=','coin_packages.coin_package_type_id')
		->where('company_coins.company_id',$company_id)
        ->where('company_coins.event_type','sales')
        ->where('coin_orders.status',1)
        ->select('company_coins.*','coin_package_types.title','coin_packages.custome_pck','coin_orders.id as coin_order_id','coin_orders.coins as order_coins')
        ->get()
        ->map(function ($coinOrder) {
            $coinOrder->coin_order_events = DB::table('coin_order_events')
            ->join('coin_events','coin_events.id','=','coin_order_events.coin_event_id')
                ->where('coin_order_id', $coinOrder->coin_order_id)
                ->select('coin_order_events.*','coin_events.title','coin_events.description')
                ->get()
                ->toArray();
            return (array) $coinOrder;
        })->toArray();

        $this->operational_package = DB::table('coin_packages')
        ->join('coin_package_types','coin_package_types.id','=','coin_packages.coin_package_type_id')
        ->select('coin_packages.*','coin_package_types.title')
        ->where('coin_packages.event_type', '=', 'operational')
        ->get()
        ->map(function($row){
            $row->features = DB::table('coin_events')
                ->where('coin_events.event_type', $row->event_type)
                ->select('coin_events.*')
                ->get()
                ->toArray();
            return (array) $row;
        });
        $this->sales_package = DB::table('coin_packages')
        ->join('coin_package_types','coin_package_types.id','=','coin_packages.coin_package_type_id')
        ->select('coin_packages.*','coin_package_types.title', DB::raw('
        (SELECT COUNT(*) FROM coin_events 
         WHERE coin_events.event_type = coin_packages.event_type) as event_count'))
        ->where('coin_packages.event_type', '=', 'sales')
        ->get()
        ->map(function($row){
            $row->features = DB::table('coin_events')
                ->where('coin_events.event_type', $row->event_type)
                ->select('coin_events.*')
                ->get()
                ->toArray();
            return (array) $row;
        });

        dd($this->data);

        return view('service_company.subscription.index', $this->data);
    }

    public function newindex()
    {      	
        // die;	
        
        		
		$company_id       			=	auth()->user()->company_id;
		$this->package_active		=	DB:: table('package_active')->select('package_active.*')->where('package_active.company_id', '=', $company_id)->first();	

       $this->carts_count =   DB::table('carts')->where('company_id','=',$company_id)->where('status','=',0)->count();
       $this->top_up_coin_packages_op = DB::table('top_up_coin_packages')->where('event_type','=','operational')->where('status','=',1)->get(); 
       $this->top_up_coin_packages_sal = DB::table('top_up_coin_packages')->where('event_type','=','sales')->where('status','=',1)->get(); 
        
        $this->company = Company::withoutGlobalScope('company')->findOrFail($company_id);
        $this->companyPackage = CompanyPackage::activePackage($company_id);
        $this->company_id = $company_id;

        $this->top_up_logs_op =   DB::table('top_up_logs')->select('top_up_logs.*')->leftJoin('carts','carts.id','=','top_up_logs.cart_id')->where('top_up_logs.event_type','=','operational')->where('top_up_logs.company_id','=',$company_id)->get();

        $this->top_up_logs_sal =   DB::table('top_up_logs')->select('top_up_logs.*')->leftJoin('carts','carts.id','=','top_up_logs.cart_id')->where('top_up_logs.event_type','=','sales')->where('top_up_logs.company_id','=',$company_id)->get();

        // Transaction history (with Razorpay status sync) now lives on its own
        // page — see transactions() — so this heavy per-load query + curl
        // sync no longer runs on every Subscription page visit.

        $this->account = DB::table('companies')->select('*')->where('id', $company_id)->first();
        $this->package_active_all = DB::table('package_active')->join('package_invoice','package_invoice.id','=','package_active.package_invoice_id')->join('package_order_items','package_order_items.invoice_id','=','package_invoice.id')->join('package_features','package_features.id','=','package_order_items.feature_id')->select('package_active.*','package_features.title','package_features.field_name','package_features.id as gg_is')->where('package_active.company_id', $company_id)->get();

        $this->package_active_details = DB::table('package_active')->join('package_invoice','package_invoice.id','=','package_active.package_invoice_id')->join('packages','packages.id','=','package_invoice.package_id')->select('package_active.*','packages.name')->where('package_active.company_id', $company_id)->first();

        // dd("hi");

        $this->current_coins_operational = DB::table('company_coins')
        ->join('coin_orders','coin_orders.id','=','company_coins.coin_order_id')
        ->join('coin_packages','coin_packages.id','=','coin_orders.coin_package_id')
        ->join('coin_package_types','coin_package_types.id','=','coin_packages.coin_package_type_id')
		->where('company_coins.company_id',$company_id)
        ->where('company_coins.event_type','operational')
        ->where('coin_orders.status',1)
        ->select('company_coins.*','coin_package_types.title','coin_packages.custome_pck','coin_orders.id as coin_order_id','coin_orders.coins as order_coins','coin_orders.coin_package_id','coin_orders.price as order_price')
        ->get()
        ->map(function ($coinOrder) {
            $coinOrder->coin_order_events = DB::table('coin_order_events')
            ->join('coin_events','coin_events.id','=','coin_order_events.coin_event_id')
                ->where('coin_order_id', $coinOrder->coin_order_id)
                ->select('coin_order_events.*','coin_events.title')
                ->get()
                ->toArray();
            return (array) $coinOrder;
        })->toArray();

        $this->current_coins_sales = DB::table('company_coins')
        ->join('coin_orders','coin_orders.id','=','company_coins.coin_order_id')
        ->join('coin_packages','coin_packages.id','=','coin_orders.coin_package_id')
        ->join('coin_package_types','coin_package_types.id','=','coin_packages.coin_package_type_id')
		->where('company_coins.company_id',$company_id)
        ->where('company_coins.event_type','sales')
        ->where('coin_orders.status',1)
        ->select('company_coins.*','coin_package_types.title','coin_packages.custome_pck','coin_orders.id as coin_order_id','coin_orders.coins as order_coins','coin_orders.coin_package_id','coin_orders.price as order_price')
        ->get()
        ->map(function ($coinOrder) {
            $coinOrder->coin_order_events = DB::table('coin_order_events')
            ->join('coin_events','coin_events.id','=','coin_order_events.coin_event_id')
                ->where('coin_order_id', $coinOrder->id)
                ->select('coin_order_events.*','coin_events.title')
                ->get()
                ->toArray();
            return (array) $coinOrder;
        })->toArray();

        $this->operational_package = DB::table('coin_packages')
        ->join('coin_package_types','coin_package_types.id','=','coin_packages.coin_package_type_id')
        ->select('coin_packages.*','coin_package_types.title')
        ->where('coin_packages.custome_pck','!=',1)
        ->where('coin_packages.event_type', '=', 'operational')
        ->get()
        ->map(function($row){
            $row->features = DB::table('coin_package_points')
                ->where('coin_package_points.coin_package_id', $row->id)
                ->select('coin_package_points.*')
                ->get()
                ->toArray();
            return (array) $row;
        });

        // dd($this->operational_package);
        $this->sales_package = DB::table('coin_packages')
        ->join('coin_package_types','coin_package_types.id','=','coin_packages.coin_package_type_id')
        ->select('coin_packages.*','coin_package_types.title', DB::raw('
        (SELECT COUNT(*) FROM coin_events 
         WHERE coin_events.event_type = coin_packages.event_type) as event_count'))
         ->where('coin_packages.custome_pck','!=',1)
        ->where('coin_packages.event_type', '=', 'sales')
        ->get()
        ->map(function($row){
            $row->features = DB::table('coin_package_points')
                ->where('coin_package_points.coin_package_id', $row->id)
                ->select('coin_package_points.*')
                ->get()
                ->toArray();
            return (array) $row;
        });
        $this->username  =	auth()->user()->name;

        // dd( $this->sales_package);


        $actual_count  = DB::table('coin_transection_logs')
        ->select('coin_transection_logs.coins')
        ->orderByDesc('coin_transection_logs.id')
		->where('coin_transection_logs.event_type','=','operational')
        ->where('coin_transection_logs.transection_type','=','credit')
        ->where(function($query) use ($company_id) {
            $query->where('coin_transection_logs.company_id', $company_id);
        })->first();

		$remain_coins  = DB::table('company_coins')
        ->select('company_coins.coins')
        ->orderByDesc('company_coins.id')
		->where('company_coins.event_type','=','operational')
        ->where(function($query) use ($company_id) {
            $query->where('company_coins.company_id', $company_id);
        })->first();
		$this->actual_count = $actual_count->coins ?? 0;
        $this->remain_coins = $remain_coins->coins ?? 0;

        // Wallet summary stats for the Overview tab — derived entirely from
        // $current_coins_operational (already fetched above), so this adds
        // zero extra queries.
        // Total purchased must come from company_coins.actual_coins (the amount
        // originally credited per batch), not coin_orders.coins — the latter is
        // the package size at purchase time and isn't guaranteed to line up
        // batch-for-batch with $remain_coins (also read from company_coins),
        // which made "purchased - available" go negative and get clamped to 0,
        // silently showing "Coins Used: 0" regardless of actual usage.
        $this->op_total_purchased = collect($this->current_coins_operational)->sum('actual_coins');
        $this->op_total_spend     = collect($this->current_coins_operational)->sum('order_price');
        $this->op_coins_used      = max(0, $this->op_total_purchased - $this->remain_coins);
        $opExpiry = collect($this->current_coins_operational)->pluck('end_date')->filter()->max();
        $this->op_plan_expiry = $opExpiry ? Carbon::parse($opExpiry) : null;
        $this->op_days_left   = $this->op_plan_expiry ? max(0, (int) now()->diffInDays($this->op_plan_expiry, false)) : 0;


        $actual_count_sales  = DB::table('coin_transection_logs')
        ->select('coin_transection_logs.coins')
        ->orderByDesc('coin_transection_logs.id')
		->where('coin_transection_logs.event_type','=','sales')
        ->where('coin_transection_logs.transection_type','=','credit')
        ->where(function($query) use ($company_id) {
            $query->where('coin_transection_logs.company_id', $company_id);
        })->first();

		$remain_coins_sales  = DB::table('company_coins')
        ->select('company_coins.coins')
        ->orderByDesc('company_coins.id')
		->where('company_coins.event_type','=','sales')
        ->where(function($query) use ($company_id) {
            $query->where('company_coins.company_id', $company_id);
        })->first();
		$this->actual_count_sales = $actual_count_sales->coins ?? 0;
        $this->remain_coins_sales = $remain_coins_sales->coins ?? 0;

        // Same wallet summary stats, sales side — zero extra queries.
        // Same fix as operational — use actual_coins (company_coins), consistent
        // with $remain_coins_sales, not coin_orders.coins.
        $this->sal_total_purchased = collect($this->current_coins_sales)->sum('actual_coins');
        $this->sal_total_spend     = collect($this->current_coins_sales)->sum('order_price');
        $this->sal_coins_used      = max(0, $this->sal_total_purchased - $this->remain_coins_sales);
        $salExpiry = collect($this->current_coins_sales)->pluck('end_date')->filter()->max();
        $this->sal_plan_expiry = $salExpiry ? Carbon::parse($salExpiry) : null;
        $this->sal_days_left   = $this->sal_plan_expiry ? max(0, (int) now()->diffInDays($this->sal_plan_expiry, false)) : 0;

        // Used-coins percentage for the wallet cards' progress ring (pure
        // arithmetic on values already computed above — no extra queries).
        $this->op_used_pct  = $this->op_total_purchased > 0 ? min(100, round(($this->op_coins_used / $this->op_total_purchased) * 100)) : 0;
        $this->sal_used_pct = $this->sal_total_purchased > 0 ? min(100, round(($this->sal_coins_used / $this->sal_total_purchased) * 100)) : 0;

        return view('service_company.subscription.newindex', $this->data);
    }

    /**
     * Dedicated Transactions > Subscription page. Split out of newindex()
     * so the main Subscription page no longer pays for this query + the
     * blocking Razorpay status-sync curl calls on every visit.
     *
     * @return \Illuminate\Http\Response
     */
    public function transactions()
    {
        $company_id = auth()->user()->company_id;
        $this->company_id = $company_id;
        $this->cart_transaction_history = $this->loadTransactionHistory($company_id);

        return view('service_company.transactions.subscription', $this->data);
    }

    /**
     * Lazy-load endpoint for the Transactions tab on the redesigned
     * Subscription & Wallet Center page (newindex). Kept separate from
     * newindex() itself so the tab's data — and its Razorpay status-sync
     * calls — only run when the user actually opens the tab, not on every
     * Subscription page load.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function transactionsAjax()
    {
        $company_id = auth()->user()->company_id;
        $cart_transaction_history = $this->loadTransactionHistory($company_id);

        $html = view('service_company.ajax_pages.subscription_transactions', [
            'cart_transaction_history' => $cart_transaction_history,
        ])->render();

        return response()->json(['html' => $html]);
    }

    /**
     * Shared query behind both transactions() and transactionsAjax(): syncs
     * pending Razorpay orders' status, then returns the paginated, tenant-
     * scoped transaction history.
     *
     * @param int $company_id
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    private function loadTransactionHistory($company_id)
    {
        $transaction_history = DB::table('cart_transaction_history')->select('cart_transaction_history.*')
        ->where('company_id','=',$company_id)
        ->where('payment_gateway', 'razorpay')
        ->where(function ($query) {
            $query->whereNull('payment_Id')
                  ->orWhere('payment_Id', '=', '');
        })->orderByDesc('id')->limit(5)->get();

        $payment_settings = DB::table('payment_settings')->select('payment_settings.*')->first();
        $key = $payment_settings->razorpay_key;
        $secret = $payment_settings->razorpay_secret;

        foreach($transaction_history as $tr_val){
            if($tr_val->payment_orderId !='' && $tr_val->payment_gateway=='razorpay'){

                $orderID = $tr_val->payment_orderId;

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "https://api.razorpay.com/v1/orders/{$orderID}/payments");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_USERPWD, "{$key}:{$secret}");

                $response = curl_exec($ch);
                if (!curl_errno($ch)) {
                    $payments = json_decode($response, true);

                    if (!empty($payments['items'])) {
                        foreach ($payments['items'] as $payment) {
                            DB::table('cart_transaction_history')->where('payment_orderId','=',$orderID)->update(['status'=>$payment['status']]);
                        }
                    }
                }
                curl_close($ch);
            }
        }

        return DB::table('cart_transaction_history')->select('cart_transaction_history.*')->where('cart_transaction_history.company_id','=',$company_id)->orderByDesc('id')->paginate(50);
    }


    public function  transectionLog(){
        
        $company_id = auth()->user()->company_id;

        // dd($company_id);
        $pagetype = request('pagetype');

        if($pagetype){
            $page = 1;
            if($pagetype=='operationaltab'){
                $page_opera = request('page');
                $page_sales = 1;
                
            }elseif($pagetype=='salestab'){
                $page_sales = request('page');
                $page_opera = 1;
            }
        }else{
            $page = request('page');
            if($page==null){
                $page_opera = 1;
                $page_sales = 1;
            }
        }
        
        $perPage = 20;
        // dd($pagetype);
        $this->coin_transection_logs_sales  = DB::table('coin_transection_logs')
        ->join('users','users.id','=','coin_transection_logs.event_by')
        ->leftJoin('companies','companies.id','=','coin_transection_logs.from_company_id')
        ->select('coin_transection_logs.*','users.name','companies.company_name')
        ->orderByDesc('coin_transection_logs.id')
        ->where('coin_transection_logs.event_type','=','sales')
        ->where('coin_transection_logs.coins','>',0)
        ->where(function($query) use ($company_id) {
            $query->where('coin_transection_logs.company_id', $company_id);
                //   ->orWhere('coin_transection_logs.from_company_id', $company_id);
        })->skip(($page_sales - 1) * $perPage)->paginate($perPage);

        // dd($this->coin_transection_logs_sales);

        $this->coin_transection_logs  = DB::table('coin_transection_logs')
        ->join('users','users.id','=','coin_transection_logs.event_by')
        ->leftJoin('companies','companies.id','=','coin_transection_logs.from_company_id')
        ->select('coin_transection_logs.*','users.name','companies.company_name')
        ->orderByDesc('coin_transection_logs.id')
        ->where('coin_transection_logs.event_type','=','operational')
        ->where('coin_transection_logs.coins','>',0)
        ->where(function($query) use ($company_id) {
            $query->where('coin_transection_logs.company_id', $company_id);
                //   ->orWhere('coin_transection_logs.from_company_id', $company_id);
        })->skip(($page_opera - 1) * $perPage)->paginate($perPage);
        
        // dd($this->coin_transection_logs);

        return view('service_company.subscription.transection-logs', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function invoice()
    {
        $this->pageTitle ='menu.subscriptionInvoice';
        return view('service_company.subscription.paypal-invoice', $this->data);
    }

    public function data()
    {
        $stripe = DB::table("stripe_invoices")
            ->join('packages', 'packages.id', 'stripe_invoices.package_id')
            ->selectRaw('stripe_invoices.id ,stripe_invoices.invoice_id , packages.name as name, "Stripe" as method,stripe_invoices.amount, stripe_invoices.pay_date as paid_on ,stripe_invoices.next_pay_date,stripe_invoices.created_at')
            ->whereNotNull('stripe_invoices.pay_date')
            ->where('stripe_invoices.company_id', company()->id);

        $razorpay = DB::table("razorpay_invoices")
            ->join('packages', 'packages.id', 'razorpay_invoices.package_id')
            ->selectRaw('razorpay_invoices.id ,razorpay_invoices.invoice_id , packages.name as name, "Razorpay" as method,razorpay_invoices.amount, razorpay_invoices.pay_date as paid_on ,razorpay_invoices.next_pay_date,razorpay_invoices.created_at')
            ->whereNotNull('razorpay_invoices.pay_date')
            ->where('razorpay_invoices.company_id', company()->id);

        $paypal = DB::table("paypal_invoices")
            ->join('packages', 'packages.id', 'paypal_invoices.package_id')
            ->selectRaw('paypal_invoices.id,"" as invoice_id, packages.name as name, "Paypal" as method ,paypal_invoices.total as amount, paypal_invoices.paid_on,paypal_invoices.next_pay_date, paypal_invoices.created_at')
            ->where('paypal_invoices.status', 'paid')
            ->where('paypal_invoices.company_id', user()->company_id)
            ->orderBy('paypal_invoices.created_at', 'desc')
            ->union($stripe)
            ->union($razorpay)
            ->get();

        $paypalData = $paypal->sortByDesc(function ($temp, $key) {
            return Carbon::parse($temp->created_at)->getTimestamp();
        })->all();

        return DataTables::of($paypalData)
            ->editColumn('name', function ($row) {
                return ucfirst($row->name);
            })
            ->editColumn('paid_on', function ($row) {
                if(!is_null($row->paid_on)) {
                    return Carbon::parse($row->paid_on)->format('d-m-Y');
                }
                return '-';
            })
            ->editColumn('next_pay_date', function ($row) {
                if(!is_null($row->next_pay_date)) {
                    return Carbon::parse($row->next_pay_date)->format('d-m-Y');
                }
                return '-';
            })
            ->addColumn('action', function ($row) {
                if($row->method == 'Stripe'){
                    return '<a href="'.route('service_company.subscribe.invoice-download', $row->invoice_id).'" class="btn btn-primary btn-circle waves-effect" data-toggle="tooltip" data-original-title="Download"><span></span> <i class="fa fa-download"></i></a>';
                }
                if($row->method == 'Paypal'){
                    return '<a href="'.route('service_company.subscribe.paypal-invoice-download', $row->id).'" class="btn btn-primary btn-circle waves-effect" data-toggle="tooltip" data-original-title="Download"><span></span> <i class="fa fa-download"></i></a>';
                }
                if($row->method == 'Razorpay'){
                    return '<a href="'.route('service_company.subscribe.razorpay-invoice-download', $row->id).'" class="btn btn-primary btn-circle waves-effect" data-toggle="tooltip" data-original-title="Download"><span></span> <i class="fa fa-download"></i></a>';
                }
                return '';
            })

            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
    }


	
	
	public function historylisting()
    {
		
			$company_id       	=	auth()->user()->company_id;
        	//$categories 		=	Company::with('package')->withoutGlobalScope('company')->get();	
				$packageInvoice =	DB::table('package_invoice')
									->join('companies', 'companies.id', '=', 'package_invoice.company_id')
									->select('package_invoice.*', 'companies.company_name')
									->where('package_invoice.company_id', '=', $company_id);			
		
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
				->addIndexColumn()
				->rawColumns(['chkbox', 'InvoiceNo', 'Company', 'PaymentMode', 'AmountPaid', 'InvoiceDownload', 'Create']) 
				->make(true);
    }

	public function viewMore($id)
	{
				$company_id       		=	'';
				$company_id       		=	auth()->user()->company_id;
				$this->invoice_id		=	$id;
				
				$this->packageDetail	=	DB::table('package_invoice')
											->join('companies', 'companies.id', '=', 'package_invoice.company_id')
											->select('package_invoice.id', 'package_invoice.create_at','package_invoice.invoice_id', 'package_invoice.payment_mode', 'package_invoice.grand_total','package_invoice.cvaccess_startdate', 'package_invoice.cvaccess_enddate', 'package_invoice.cvaccess_status', 'package_invoice.cvaccess_quantity', 'package_invoice.cvaccess_price',  'package_invoice.cvaccess_totalprice','package_invoice.users_startdate', 'package_invoice.users_enddate', 'package_invoice.users_status', 'package_invoice.users_quantity', 'package_invoice.users_price',  'package_invoice.users_totalprice', 'package_invoice.newclientrequest_startdate', 'package_invoice.newclientrequest_enddate', 'package_invoice.newclientrequest_status', 'package_invoice.newclientrequest_quantity', 'package_invoice.newclientrequest_price',  'package_invoice.newclientrequest_totalprice', 'package_invoice.newjobrequest_startdate', 'package_invoice.newjobrequest_enddate', 'package_invoice.newjobrequest_status', 'package_invoice.newjobrequest_quantity', 'package_invoice.newjobrequest_price',  'package_invoice.newjobrequest_totalprice', 'package_invoice.newbenchclientrequest_startdate', 'package_invoice.newbenchclientrequest_enddate', 'package_invoice.newbenchclientrequest_status', 'package_invoice.newbenchclientrequest_quantity', 'package_invoice.newbenchclientrequest_price',  'package_invoice.newbenchclientrequest_totalprice', 'package_invoice.emails_startdate', 'package_invoice.emails_enddate', 'package_invoice.emails_status', 'package_invoice.emails_quantity', 'package_invoice.emails_price',  'package_invoice.emails_totalprice', 'package_invoice.oldcvaccess_startdate', 'package_invoice.oldcvaccess_enddate', 'package_invoice.oldcvaccess_status', 'package_invoice.oldcvaccess_quantity', 'package_invoice.oldcvaccess_price',  'package_invoice.oldcvaccess_totalprice','package_invoice.boostkeywords_startdate', 'package_invoice.boostkeywords_enddate', 'package_invoice.boostkeywords_status', 'package_invoice.boostkeywords_price', 'package_invoice.boostkeywords_totalprice', 'package_invoice.boostkeywords_text', 'package_invoice.boostkeywords_quantity','companies.company_name', 'companies.status', 'companies.company_email', 'companies.company_phone', 'companies.website', 'companies.company_type','companies.company_size', 'companies.founded' 
											)
											->where('package_invoice.id', '=', $this->invoice_id)
											->where('package_invoice.company_id', '=', $company_id)
											->first();
			
			
			
			 

                                    $this->package_invoice_comment_count	=	DB:: table('package_invoice_comment')
                                                                            ->select('package_invoice_comment.*')
                                                                            ->where('package_invoice_comment.invoice_id', '=', $this->invoice_id) 
                                                                            ->orderBy('package_invoice_comment.id', 'DESC')
                                                                            ->count(); 
												
                                    $this->package_invoice_comment			=	DB:: table('package_invoice_comment')
                                                                            ->select('package_invoice_comment.*')
                                                                            ->where('package_invoice_comment.invoice_id', '=', $this->invoice_id) 
                                                                            ->orderBy('package_invoice_comment.id', 'DESC')
                                                                            ->get(); 			
			
					
                                    // return view('service_company.subscription.index', $this->data);
                                                            
                                    $view 				=	view('service_company.subscription.viewMore', $this->data)->render();
                                    return Reply::dataOnly(['status' => 'success', 'view' => $view]);			

	}

    public function payment(PaymentRequest $request) {

        $this->setStripConfigs();
        $token = $request->payment_method;
        $email = $request->stripeEmail;

        $plan = Package::find($request->plan_id);

        $stripe = DB::table("stripe_invoices")
            ->join('packages', 'packages.id', 'stripe_invoices.package_id')
            ->selectRaw('stripe_invoices.id , "Stripe" as method, stripe_invoices.pay_date as paid_on ,stripe_invoices.next_pay_date')
            ->whereNotNull('stripe_invoices.pay_date')
            ->where('stripe_invoices.company_id', company()->id);

        $razorpay = DB::table("razorpay_invoices")
            ->join('packages', 'packages.id', 'razorpay_invoices.package_id')
            ->selectRaw('razorpay_invoices.id ,"Razorpay" as method, razorpay_invoices.pay_date as paid_on ,razorpay_invoices.next_pay_date')
            ->whereNotNull('razorpay_invoices.pay_date')
            ->where('razorpay_invoices.company_id', company()->id);

        $allInvoices = DB::table("paypal_invoices")
            ->join('packages', 'packages.id', 'paypal_invoices.package_id')
            ->selectRaw('paypal_invoices.id, "Paypal" as method, paypal_invoices.paid_on,paypal_invoices.next_pay_date')
            ->where('paypal_invoices.status', 'paid')
            ->whereNull('paypal_invoices.end_on')
            ->where('paypal_invoices.company_id', company()->id)
            ->union($stripe)
            ->union($razorpay)
            ->get();

        $firstInvoice = $allInvoices->sortByDesc(function ($temp, $key) {
            return Carbon::parse($temp->paid_on)->getTimestamp();
        })->first();

        $subcriptionCancel = true;

        if(!is_null($firstInvoice) && $firstInvoice->method == 'Paypal'){
            $subcriptionCancel = $this->cancelSubscriptionPaypal();
        }
        if(!is_null($firstInvoice) && $firstInvoice->method == 'Razorpay'){
            $subcriptionCancel = $this->cancelSubscriptionPaypal();
        }

        if($subcriptionCancel){

            $company = Company::where('id', company()->id)->first();
            $subscription = $company->subscriptions;

            try {
                if ($subscription->count() > 0) {
                    $company->subscription('main')->swap($plan->{'stripe_'. $request->type .'_plan_id'});
                }
                else {
                    $company->newSubscription('main', $plan->{'stripe_'.$request->type.'_plan_id'})->create($token, [
                        'email' => $email,
                    ]);
                }

                $company->package_id = $plan->id;
                $company->package_type = $request->type;
                $company->status = 'active'; // Set company status active
                $company->licence_expire_on = null;
                $company->save();

                CompanyPackage::where('company_id', $company->id)
                    ->update(['status' => 'inactive', 'end_date' => Carbon::now(company()->timezone)->format('Y-m-d')]);

                $companyPackages = new CompanyPackage();
                $companyPackages->package_id = $plan->id;
                $companyPackages->package_id = $plan->id;
                $companyPackages->company_id = $company->id;
                $companyPackages->start_date = Carbon::now(company()->timezone)->format('Y-m-d');
                if ($company->package_type == 'monthly') {
                    $companyPackages->end_date = Carbon::now(company()->timezone)->addMonth()->format('Y-m-d');
                } else {
                    $companyPackages->end_date = Carbon::now(company()->timezone)->addYear()->format('Y-m-d');
                }
                $companyPackages->status = 'active';
                $companyPackages->save();

                $company->package_id = $plan->id;
                $company->package_type = $request->type;

                // Set company status active
                $company->status = 'active';
                $company->licence_expire_on = null;

                $company->save();
                \Session::flash('success', 'Payment successfully done.');
                return redirect(route('service_company.subscribe.index'));

            } catch (\Exception $e) {
                return back()->withError($e->getMessage())->withInput();
            }
        }
        return back()->withError('User not found by ID ' . $request->input('user_id'))->withInput();
    }

    // Stripe invoice Download
    /**
     * @param Request $request
     * @param $invoiceId
     * @return mixed
     */
    public function download(Request $request, $invoiceId) 
	{
        $this->setStripConfigs();
        $this->company = company();
        return $this->company->downloadInvoice($invoiceId, [
            'vendor'  => $this->company->company_name,
            'product' => $this->company->package->name,
            'global' => GlobalSetting::first(),
            'logo' => $this->company->logo,
        ]);
    }

    public function paypalInvoiceDownload($id)
    {
        $this->invoice = PaypalInvoice::with(['company','currency','package'])->findOrFail($id);

        $this->company = company();
        $this->superSettings =  GlobalSetting::first();
        $pdf = app('dompdf.wrapper');
        $pdf->loadView('paypal-invoice.invoice-1', $this->data);
        $filename = $this->invoice->paid_on->format("dS M Y").'-'.$this->invoice->next_pay_date->format("dS M Y");
        return $pdf->download($filename . '.pdf');
    }

    public function invoiceDownload($id)
    {
        $this->invoice = CompanyPackage::with(['company','package'])->findOrFail($id);
        $this->amount = 0;

        if($this->invoice->package_type == 'monthly'){
            $this->amount = $this->invoice->package->monthly_price;

        }elseif($this->invoice->package_type == 'annual'){
            $this->amount = $this->invoice->package->annual_price;
        }

        $this->company = company();
        $this->superSettings =  GlobalSetting::with('currency')->first();
        $pdf = app('dompdf.wrapper');
        $pdf->loadView('default-invoice.invoice-1', $this->data);
        $filename = $this->invoice->start_date->format("dS M Y").'-'.$this->invoice->end_date->format("dS M Y");
        return $pdf->download($filename . '.pdf');
    }

    public function cancelSubscriptionPaypal()
	{
        $credential = PaymentSetting::first();
        $paypal_conf = Config::get('paypal');
        $api_context = new ApiContext(new OAuthTokenCredential($credential->paypal_client_id, $credential->paypal_secret));
        $api_context->setConfig($paypal_conf['settings']);

        $paypalInvoice = PaypalInvoice::whereNotNull('transaction_id')->whereNull('end_on')
            ->where('company_id', company()->id)->where('status', 'paid')->first();

        if($paypalInvoice){
            $agreementId = $paypalInvoice->transaction_id;
            $agreement = new Agreement();

            $agreement->setId($agreementId);
            $agreementStateDescriptor = new AgreementStateDescriptor();
            $agreementStateDescriptor->setNote("Cancel the agreement");

            try {
                $agreement->cancel($agreementStateDescriptor, $api_context);
                $cancelAgreementDetails = Agreement::get($agreement->getId(), $api_context);

                // Set subscription end date
                $paypalInvoice->end_on = Carbon::parse($cancelAgreementDetails->agreement_details->final_payment_date)->format('Y-m-d H:i:s');
                $paypalInvoice->save();

            } catch (\Exception $ex) {
                //\Session::put('error','Some error occur, sorry for inconvenient');
                return false;
            }

            return true;
        }
    }

    public function cancelSubscription($type) 
	{
        $credential = PaymentSetting::first();
        if($type == 'Paypal') {
            $credential = PaymentSetting::first();
            $paypal_conf = Config::get('paypal');
            $api_context = new ApiContext(new OAuthTokenCredential($credential->paypal_client_id, $credential->paypal_secret));
            $api_context->setConfig($paypal_conf['settings']);

            $paypalInvoice = PaypalInvoice::whereNotNull('transaction_id')->whereNull('end_on')
                ->where('company_id', company()->id)->where('status', 'paid')->first();

            if ($paypalInvoice) {
                $agreementId = $paypalInvoice->transaction_id;
                $agreement = new Agreement();

                $agreement->setId($agreementId);
                $agreementStateDescriptor = new AgreementStateDescriptor();
                $agreementStateDescriptor->setNote("Cancel the agreement");

                try {
                    $agreement->cancel($agreementStateDescriptor, $api_context);
                    $cancelAgreementDetails = Agreement::get($agreement->getId(), $api_context);

                    // Set subscription end date
                    $paypalInvoice->end_on = Carbon::parse($cancelAgreementDetails->agreement_details->final_payment_date)->format('Y-m-d H:i:s');
                    $paypalInvoice->save();
                } catch (\Exception $ex) {
                    \Session::put('error', 'Some error occur, sorry for inconvenient');
                    return Reply::redirect(route('service_company.subscribe.index'));
                }
            }

        }elseif($type == 'Razorpay'){

                $apiKey    = $credential->razorpay_key;
                $secretKey = $credential->razorpay_secret;
                $api       = new Api($apiKey, $secretKey);

                // Get subscription for unsubscribe
                $subscriptionData = RazorpaySubscription::where('company_id', company()->id)->whereNull('ends_at')->first();
                if($subscriptionData){
                    try {
      //                  $subscriptions = $api->subscription->all();
                        $subscription  = $api->subscription->fetch($subscriptionData->subscription_id);
                        if($subscription->status == 'active'){

                            // unsubscribe plan
                            $subData = $api->subscription->fetch($subscriptionData->subscription_id)->cancel(['cancel_at_cycle_end' => 1]);

                            // plan will be end on this date
                            $subscriptionData->ends_at = \Carbon\Carbon::createFromTimestamp($subData->end_at)->format('Y-m-d');
                            $subscriptionData->save();
                        }
                        elseif ($subscription->status == 'cancelled'){
                            $subscriptionData->ends_at = \Carbon\Carbon::createFromTimestamp($subscription->end_at)->format('Y-m-d');
                            $subscriptionData->save();
                        }

                    } catch (\Exception $ex) {
                        \Session::put('error','Some error occur, sorry for inconvenient');
                        return Reply::redirect(route('service_company.subscribe.index'));
                    }
                }

            } else{
            $this->setStripConfigs();
            $company = company();
            $subscription = Subscription::where('company_id', company()->id)->whereNull('ends_at')->first();
            if($subscription){
                try {
                    $company->subscription('main')->cancel();
                } catch (\Exception $ex) {
                    \Session::put('error','Some error occur, sorry for inconvenient');
                    return Reply::redirect(route('service_company.subscribe.index'));
                }
            }
        }

        return Reply::redirect(route('service_company.subscribe.index'), __('messages.unsubscribeSuccess'));
    }

    public function cancelSubscriptionRazorpay()
	{
        $credential = PaymentSetting::first();
        $apiKey    = $credential->razorpay_key;
        $secretKey = $credential->razorpay_secret;
        $api       = new Api($apiKey, $secretKey);

        // Get subscription for unsubscribe
        $subscriptionData = RazorpaySubscription::where('company_id', company()->id)->whereNull('ends_at')->first();

        if($subscriptionData){
            try {
        //                  $subscriptions = $api->subscription->all();
                $subscription  = $api->subscription->fetch($subscriptionData->subscription_id);
                if($subscription->status == 'active'){

                    // unsubscribe plan
                    $subData = $api->subscription->fetch($subscriptionData->subscription_id)->cancel(['cancel_at_cycle_end' => 0]);

                    // plan will be end on this date
                    $subscriptionData->ends_at = \Carbon\Carbon::createFromTimestamp($subData->end_at)->format('Y-m-d');
                    $subscriptionData->save();
                }

            } catch (\Exception $ex) {
                return false;
            }
            return true;
        }
    }

    public function selectPackage(Request $request, $packageID) 
	{
        $this->setStripConfigs();
        $this->package = Package::findOrFail($packageID);
        $this->company = company();
        $this->type    = $request->type;
        $this->stripeSettings = PaymentSetting::first();

        if(is_null($this->global->logo)) {
            $this->logo = asset('assets/images/logo.png');
        }
        else if(!is_null($this->superSettings->logo)){
            $this->logo = asset('user-uploads/global-logo/'.$this->superSettings->logo) ;
        }
        else {
            $this->logo = '' ;
        }
        return View::make('service_company.subscription.payment-method-show', $this->data);
    }

    public function history()
    {
		
				//echo '<pre>'; print_r('Call'); exit;  
				
				$company_id       		=	'';
				$company_id       		=	auth()->user()->company_id;
				$this->pageTitle		=	'menu.history';
				//$this->companyPackage 	=	CompanyPackage::activePackage($this->global->id);
				//$this->company 			=	Company::with('packages')->withoutGlobalScope('company')->findOrFail($this->global->id);
				
				$this->invoice_counter	=	DB :: table('package_invoice')->select('package_invoice.id')->where('package_invoice.company_id', '=', $company_id )->count();
				
				
				return view('service_company.subscription.history', $this->data); 
    }

    public function historyData()
    {
        $packages = CompanyPackage::select('company_packages.id','packages.name','company_packages.package_type',
            'company_packages.start_date','company_packages.end_date','company_packages.status',
            'company_packages.stripe_id','stripe_invoices.invoice_id', 'company_packages.paypal_id')
            ->join('packages', 'packages.id', 'company_packages.package_id')
            ->leftJoin('stripe_invoices', 'stripe_invoices.id', 'company_packages.stripe_id')
            ->leftJoin('paypal_invoices', 'paypal_invoices.id', 'company_packages.paypal_id')
        ->where('company_packages.company_id', $this->global->id)->get();


        return DataTables::of($packages)
            ->editColumn('name', function ($row) {
                return ucfirst($row->name);
            })
            ->editColumn('start_date', function ($row) {
                if(!is_null($row->start_date)) {
                    return Carbon::parse($row->start_date)->format('d M, Y');
                }
                return '-';
            })
            ->addColumn('payment_method', function ($row) {

                if($row->stripe_id != null){
                    return 'Stripe';
                }
                else if($row->paypal_id != null){
                    return 'Paypal';
                }
                else{
                    return 'Cash';
                }
            })
            ->editColumn('end_date', function ($row) {
                if(!is_null($row->end_date)) {
                    return Carbon::parse($row->end_date)->format('d M, Y');
                }
                return '-';
            })

            ->editColumn('package_type', function ($row) {
                if(!is_null($row->package_type)) {
                    return ucfirst($row->package_type);
                }
                return '-';
            })
            ->editColumn('status', function ($row) {
                if ($row->status == 'active'){
                    return '<label class="badge bg-success">'.__('app.active').'</label>';

                }
                else{
                    return '<label class="badge bg-danger">'.__('app.inactive').'</label>';
                }

            })
            ->addColumn('action', function ($row) {
                $string = '';

                if($row->stripe_id != null){
                    $string .= '<a href="'.route('service_company.subscribe.invoice-download', $row->invoice_id).'" class="btn btn-primary btn-circle waves-effect" data-toggle="tooltip" data-original-title="Download"><span></span> <i class="fa fa-download"></i></a>';
                }
               else if($row->paypal_id != null){
                   $string .= '<a href="'.route('service_company.subscribe.paypal-invoice-download', $row->paypal_id).'" class="btn btn-primary btn-circle waves-effect" data-toggle="tooltip" data-original-title="Download"><span></span> <i class="fa fa-download"></i></a>';
                }
                else{
                    $string .= '<a href="'.route('service_company.subscribe.default-invoice-download', $row->id).'" class="btn btn-primary btn-circle waves-effect" data-toggle="tooltip" data-original-title="Download"><span></span> <i class="fa fa-download"></i></a>';
                }
                 return   $string;

            })

            ->rawColumns(['action', 'status'])
            ->removeColumn('stripe_id')
            ->removeColumn('paypal_id')
            ->addIndexColumn()
            ->make(true);
    }

    public function razorpayPayment(Request $request)
	{
        $credential = PaymentSetting::first();
        $this->company = company();
        $apiKey    = $credential->razorpay_key;
        $secretKey = $credential->razorpay_secret;

        $paymentId = request('paymentId');
        $razorpaySignature = $request->razorpay_signature;
        $subscriptionId = $request->subscription_id;

        $api = new Api($apiKey, $secretKey);

        $plan = Package::find($request->plan_id);
        $type = $request->type;

        $expectedSignature = hash_hmac('sha256', $paymentId . '|' . $subscriptionId, $secretKey);

        if($expectedSignature === $razorpaySignature){

            try {
                $api->payment->fetch($paymentId);

                $payment = $api->payment->fetch($paymentId); // Returns a particular payment

                if ($payment->status == 'authorized') {
                    //TODO::change INR into default currency code
                    $payment->capture(array('amount' => $payment->amount, 'currency' => 'INR'));
                }

                $company = $this->company;

                $company->package_id = $plan->id;
                $company->package_type = $type;

                // Set company status active
                $company->status = 'active';
                $company->licence_expire_on = null;

                $company->save();

                $subscription = new RazorpaySubscription();

                $subscription->subscription_id = $subscriptionId;
                $subscription->company_id      = company()->id;
                $subscription->razorpay_id     = $paymentId;
                $subscription->razorpay_plan   = $type;
                $subscription->quantity        = 1;
                $subscription->save();

                //send superadmin notification
//                $superAdmin = User::whereNull('company_id')->get();
//                Notification::send($superAdmin, new CompanyUpdatedPlan($company, $plan->id));

                return Reply::redirect(route('service_company.subscription'), 'Payment successfully done.');

            } catch (\Exception $e) {
                return back()->withError($e->getMessage())->withInput();
            }
        }
    }

    public function razorpaySubscription(Request $request)
	{
        	$credential = PaymentSetting::first();

			$plan = Package::find($request->plan_id);
			$type =  $request->type;

        	$planID = ($type == 'annual') ? $plan->razorpay_annual_plan_id : $plan->razorpay_monthly_plan_id;

			$apiKey    = $credential->razorpay_key;
			$secretKey = $credential->razorpay_secret;

        	$api        = new Api($apiKey, $secretKey);
        	$subscription  = $api->subscription->create(array('plan_id' => $planID, 'customer_notify' => 1, 'total_count' => 2));

        	return Reply::dataOnly(['subscriprion' => $subscription->id]);
    }

    public function razorpayInvoiceDownload($id)
    {
        $this->company = company();
        $this->invoice = RazorpayInvoice::with(['company','currency','package'])->findOrFail($id);
        $pdf = app('dompdf.wrapper');
        $pdf->loadView('razorpay-invoice.invoice-1', $this->data);
        $filename = $this->invoice->pay_date->format("dS M Y").'-'.$this->invoice->next_pay_date->format($this->global->date_format);
        return $pdf->download($filename . '.pdf');
    }

    public function checkoutSubscribe(Request $request)
    {  
          decrypt($request->id);
          $id =  decrypt($request->id);	
       
        //   dd( $id,   $request->topup);
        $currency = GeneralMethod::convertINRToUSD();
        $company_coins_cart = '';
        $company_coins_cart= '';

          if($request->topup){
            $top_up_coins_cart = DB::table('top_up_coin_packages')
            ->where('top_up_coin_packages.id',$id)
            ->select('top_up_coin_packages.*')
            ->first();
          }else{
            $company_coins_cart = DB::table('coin_packages')
          ->join('coin_package_types','coin_package_types.id','=','coin_packages.coin_package_type_id')
          ->where('coin_packages.id',$id)
          ->select('coin_packages.*','coin_package_types.title')
          ->first();
          }
         


          if ($company_coins_cart) {
            $subscription_type = 'yes';
            $product_name = $company_coins_cart->title;
            $event_type = $company_coins_cart->event_type;
            $duration = $company_coins_cart->days;
            $discount = $company_coins_cart->discount;
            $credit = $company_coins_cart->coins;
            $price = ($currency=='INR') ? $company_coins_cart->price : $company_coins_cart->price_usd;
            if($currency == 'INR'){
                $gst = 18;
                $gst_amount = ($price * $gst) / 100;
                $total_price = $price + $gst_amount;
            }else{
                $gst = 0; // GST might not apply for USD or set appropriate tax value
                $gst_amount = ($price * $gst) / 100; // Modify this if other tax applies
                $total_price = $price + $gst_amount; // Total price in USD
            }

            
        } elseif ($top_up_coins_cart) {
            $subscription_type = 'no';
            $product_name = "Top-up Coins";
            $event_type = $top_up_coins_cart->event_type;
            $duration = "Existing Package";
            $discount = 0;
            $credit = $top_up_coins_cart->coins;
            $price = ($currency=='INR') ? $top_up_coins_cart->price : $top_up_coins_cart->price_usd;

             if($currency == 'INR'){
                $gst = 18;
                $gst_amount = ($price * $gst) / 100;
                $total_price = $price + $gst_amount;
            }else{
                $gst = 0; // GST might not apply for USD or set appropriate tax value
                $gst_amount = ($price * $gst) / 100; // Modify this if other tax applies
                $total_price = $price + $gst_amount; // Total price in USD
            }

            
        }

         DB::table('carts')->where('status','=',0)->delete();
        // Create the cart value only if one of the cart types is found
        if ($company_coins_cart || $top_up_coins_cart) {
            $cartValue = [
                'company_id' => auth()->user()->company_id,
                'subscription_type' => $subscription_type,
                'product_name' => $product_name,
                'pck_top_id' => $id,
                'event_type' =>$event_type,
                'duration' => $duration,
                'discount' => $discount,
                'credit' => $credit,
                'currency' => $currency,
                'price' => $price,
                'gst' => $gst,
                'gst_amount' => $gst_amount,
                'total_price' => $total_price,
                'user_id' => auth()->user()->id
            ];
        
            // dd($cartValue);
            // Insert the cart record
            DB::table('carts')->insert($cartValue);
        }
    	
        		
		$company_id       			=	auth()->user()->company_id;
		
        return Reply::dataOnly(['status' => 'success']);

        // return view('service_company.subscription.checkout-subscribe', $this->data);
    }

    public function checkoutSubscribeIist(Request $request)
    { 

        
        $company_id       			=	auth()->user()->company_id;
        $this->carts = DB::table('carts')->where('company_id', '=', $company_id)->where('status','=',0)->first();

        $this->carts_count = DB::table('carts')->where('company_id', '=', $company_id)->where('status','=',0)->count();

       
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

             


        $this->company_id =  $company_id;

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




			


        return view('service_company.subscription.checkout-subscribe', $this->data);
    }

    public function deletecheckout(Request $request){
             decrypt($request->id);
             $id =  decrypt($request->id);
             $this->carts = DB::table('carts')->where('id',$id)->delete();
    }

    public function cartcheckout(Request $request) 
    {
        // dd($request->all());
        $id =  decrypt($request->id);
        $row = DB::table('carts')->where('id','=',$id)->where('status','=',0)->first();
        // dd($row);
        $topUptype 	= $row->event_type;
        $company_id 	= $row->company_id;
        $price 			= $row->price+$row->gst_amount;
        $coins 			= $row->credit;
        $currency 			= $row->currency;
        $pck_top_id 			= $row->pck_top_id;
        $start_date = ''; 
        $end_date = '';
        $coin_order_id = '';

    //   dd($company_id);
        $company_coins = DB::table('company_coins')->where('event_type','=',$topUptype)->where('company_id','=',$company_id)->whereDate('end_date', '>=', now())->first();
          $update_coins = $coins;
        if($company_coins){
            $update_coins = $company_coins->coins+$coins;
        }

       

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
                ->where('client_id', $clients->id)
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

       
        $createPayment = GeneralMethod::createPayment($price,$currency);

        // dd($createPayment);

        $maxId = DB::table('cart_transaction_history')->max('id');
        $pre_fixed = DB::table('pre_fixed_settings')->where('type','invoice')->first();
        $number = ($maxId > 0)? $maxId+1 : 1; 
        $padded_number = str_pad($number, 5, '0', STR_PAD_LEFT);
        $invoices_no =  $pre_fixed->pre_fixed.$padded_number;
        $invoices_date = date("Y-m-d H:i:s"); 

        if($row) {
            // Insert the data into the `cart_archives` table
            $insertGetId =  DB::table('cart_transaction_history')->insertGetId([
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
                'status' => $createPayment['status'],
                "payment_orderId" => $createPayment['id'],
                'payment_gateway' => 'razorpay',
                'invoiceId' => $invoices_no,
                'invoice_date' => $invoices_date,
                'payment_mode' => 'Online',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);
        }

        // dd($insertGetId);


        if($row->subscription_type=='yes'){
		$company_coins_upgrade = DB::table('coin_packages')
        ->where('coin_packages.id','=',$pck_top_id)
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

        // dd($company_coins_upgrade);

        if(@$company_coins_upgrade){

            foreach($company_coins_upgrade as $package_sales){

                $newDate = date("Y-m-d");
                $start_date = date("Y-m-d"); 
                $end_date = date("Y-m-d", strtotime($start_date . " +".$package_sales['days']." days")); 
               
                    $coin_orders = [
                        'coin_package_id'=>$package_sales['id'],
                        "currency" =>$currency,
                        'price'=>$price,
                        'coins'=>$coins,
                        'days'=>$package_sales['days'],
                        'event_type'=>$package_sales['event_type'],
                        'company_id'=>$company_id,
                        'discount'=>$package_sales['discount'],
                        "payment_orderId" => $createPayment['id'],
                        'status'=>2,
                        'created_at'=>date("Y-m-d H:i:s"),
                        'updated_at'=>date("Y-m-d H:i:s")	
                    ];
               
                    $coin_order_id = DB::table('coin_orders')->insertGetId($coin_orders);
                    foreach($package_sales['coin_order_events'] as $package_value){
                        $coin_order_events = [
                            'company_id'=>$company_id,
                            'coin_event_id'=>$package_value->id,
                            'deductions'=>$package_value->default_rate,
                            'deduction_type'=>$package_value->deduction_type,
                            'coin_order_id'=>$coin_order_id,
                            'required_coin'=>$package_value->minimum_required,
                            'event_type'=>$package_value->event_type
                        ];
                        // dd($coin_order_events);
                        DB::table('coin_order_events')->insertGetId($coin_order_events);
                    }

                   
                    $coinEvents = DB::table('coin_orders')
                    ->join('coin_packages','coin_packages.id','=','coin_orders.coin_package_id')
                    ->join('coin_package_types','coin_package_types.id','=','coin_packages.coin_package_type_id')
                    ->where('coin_orders.id',$coin_order_id)
                    ->select('coin_package_types.title')->first();
                    $coinEventspackage = ($coinEvents) ? $coinEvents->title : '';
                    
                    $coin_transection_logs = [
                            'coin_order_id'=>$coin_order_id,
                            'from_company_id'=>$company_id,
                            'company_id'=>$company_id,
                            'event_by'=>auth()->user()->id,    
                            'event'=> $coinEventspackage. " ",
                            "currency" =>$currency,
                            'amount'=>$price,
                            'discount'=>$package_sales['discount'],
                            'coins'=>0,
							'remain_coins'=>0,
							'actual_coins'=>0,
                            'transection_type'=>'credit',
                            'settlement_coins'=>0,
                            'event_type'=>$package_sales['event_type'],
                            'start_date'=>$start_date,
                            'end_date'=>$end_date,
                            "payment_orderId" => $createPayment['id'],
                            'created_at'=>date("Y-m-d H:i:s"),
                            'updated_at'=>date("Y-m-d H:i:s")	
                    ];

                    DB::table('coin_transection_logs')->insertGetId($coin_transection_logs);
                
            }
        }

    }else{


       if($company_coins){

        $value = [
            "company_id" =>$company_id,            
            "currency" =>$currency,
            "price" =>$price,
            "coins" => $coins,
            "custome_chk" =>0,
            "event_type" => $topUptype,
            "created_at" => date("Y-m-d H:i:s"),
            "updated_at" => date("Y-m-d H:i:s"),
            "payment_orderId" => $createPayment['id'],
            "status" => 0
        ];

        DB::table('top_up_logs')->insert($value);

                $coin_transection_logs = [
                    'coin_order_id'=>$coin_order_id,
                    'from_company_id'=>$company_id,
                    'company_id'=>$company_id,
                    'event_by'=>auth()->user()->id,    
                    'event'=>"Top-up Coins",
                    "currency" =>$currency,
                    'amount'=>$price,
                    'discount'=>"",
                    'coins'=>0,
                    'remain_coins'=>0,
                    'actual_coins'=>0,
                    'transection_type'=>'credit',
                    'settlement_coins'=>0,
                    'event_type'=>$topUptype,
                    'start_date'=>$start_date,
                    'end_date'=>$end_date,
                    "payment_orderId" => $createPayment['id'],
                    'created_at'=>date("Y-m-d H:i:s"),
                    'updated_at'=>date("Y-m-d H:i:s")	
            ];

            DB::table('coin_transection_logs')->insertGetId($coin_transection_logs);


       }

    }

        $billing_address_companies = DB::table('billing_address_companies')->where('company_id','=',$company_id)->first();
        $sold_by_address = DB::table('sold_by_address')->first();
        if($billing_address_companies && $insertGetId){

            DB::table('billing_address_by_orders')->insert([
                'cart_transaction_id' => $insertGetId,
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
                'gstin' =>($billing_address_companies) ? $billing_address_companies->gstin : '',
                'pan_no' =>($billing_address_companies) ? $billing_address_companies->pan_no : '',
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



            return response()->json([
                'message' => '',
                'status' => 'success',
                'orderId' => $createPayment['id'],
                'amount' => $createPayment['amount'],
                'update_coins' => $update_coins,
                'company_id' => $company_id,
                'event_type' =>$topUptype,
                'start_date' =>$start_date,
                'end_date' =>$end_date,
                'coin_order_id' =>$coin_order_id,
                'subscription_type' =>$row->subscription_type,
                'cart_id' => $id,
                'currency' => $createPayment['currency']
            ]);

    
    }

    public function receiptdownload(Request $request){

        $id =  decrypt($request->id);
        // dd($id);
         if($id>0){
        $company_id       			=	auth()->user()->company_id;
        $clients  = DB::table('clients')
        ->leftJoin('companies', 'companies.id', '=', 'clients.company_id')
        ->leftJoin('users','users.company_id','=','companies.id',)
        ->select('clients.id','companies.company_name','users.name')
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
				'client_location.address',
				'client_location.postcode',
				'client_location.country_id',
				'city_list.city as city_name',
				'state_list.state as state_name',
				'countries.country_name'
			)
			->where('client_id', $clients->id)
			->first();
            $company_name  = @$clients->company_name;
            $user_name  = @$clients->name;
            $locArrLocation  = '';
            if($locInfo){
                $city_name = @$locInfo->city;
				
				$locArrLocation = ucwords(@$locInfo->address) . ' ' . ucwords($city_name) . ' , ' . ucwords(@$locInfo->state_name) . ' , ' . ucwords(@$locInfo->country_name);
            }

          //    dd($locArrLocation);


        $reciepts =   DB::table('cart_transaction_history')
        ->select('cart_transaction_history.*',
        'billing_address_by_orders.organization',
        'billing_address_by_orders.postal_code',
        'billing_address_by_orders.gstin',
        'billing_address_by_orders.street',
        'billing_address_by_orders.building',
        'billing_address_by_orders.phone',
        'billing_address_by_orders.last_name',
        'billing_address_by_orders.first_name',
        'billing_address_by_orders.pan_no',
        'billing_address_by_orders.country_id',
        'billing_address_by_orders.state_id',
        'billing_address_by_orders.city',
        'countries.country_name',
        'state_list.state',
        'city_list.city',
        'billing_address_by_orders.sold_organization',
        'billing_address_by_orders.sold_postal_code',
        'billing_address_by_orders.sold_gstin',
        'billing_address_by_orders.sold_street',
        'billing_address_by_orders.sold_building',
        'billing_address_by_orders.sold_phone',
        'billing_address_by_orders.sold_last_name',
        'billing_address_by_orders.sold_first_name',
        'billing_address_by_orders.sold_pan_no',
        'billing_address_by_orders.sold_country_id',
        'billing_address_by_orders.sold_state_id',
        'billing_address_by_orders.sold_city',
        'sold_countries.country_name as sold_country_name',
        'sold_state_list.state as sold_state',
        'sold_city_list.city as sold_city',
        )
        ->join('carts','carts.id','=','cart_transaction_history.cart_id')
        ->leftJoin('billing_address_by_orders','billing_address_by_orders.cart_transaction_id','=','cart_transaction_history.id')
        ->leftJoin('countries','countries.id','=','billing_address_by_orders.country_id')
        ->leftJoin('state_list','state_list.id','=','billing_address_by_orders.state_id')
        ->leftJoin('city_list','city_list.city_id','=','billing_address_by_orders.city')
        ->leftJoin('countries as sold_countries','sold_countries.id','=','billing_address_by_orders.sold_country_id')
        ->leftJoin('state_list as sold_state_list','sold_state_list.id','=','billing_address_by_orders.sold_state_id')
        ->leftJoin('city_list as sold_city_list','sold_city_list.city_id','=','billing_address_by_orders.sold_city')
        ->where('cart_transaction_history.cart_id','=',$id)->first();


        $sold_by_fixed = DB::table('sold_by_address')->select('sold_by_address.*',
        'countries.country_name',
        'state_list.state',        
        'city_list.city',
        )
        ->leftJoin('countries','countries.id','=','sold_by_address.country_id')
        ->leftJoin('state_list','state_list.id','=','sold_by_address.state_id')
        ->leftJoin('city_list','city_list.city_id','=','sold_by_address.city')->first();


        // dd( $reciepts);

        $paymentStatus = $reciepts->status;
        $status_text = '';
        $status_color = '';
        
        switch ($paymentStatus) {
            case 'created':
                $status_text = 'Initiated';
                $status_color = 'color: rgba(224, 11, 11, 0.07)';
                break;
            case 'authorized':
                $status_text = 'Authorized';
                $status_color = 'color: rgba(224, 11, 11, 0.07)';
                break;
            case 'captured':
                $status_text = 'PAID';
                $status_color = 'color: rgba(11, 224, 64, 0.07)';
                break;
            case 'failed':
                $status_text = 'FAILED';
                $status_color = 'color: rgba(224, 11, 11, 0.07)';
                break;
            case 'refunded':
                $status_text = 'Refunded';
                $status_color = 'color: rgba(224, 11, 11, 0.07)';
                break;
            case 'partially_refunded':
                $status_text = 'Partially refunded';
                $status_color = 'color: rgba(224, 11, 11, 0.07)';
                break;
            default:
                $status_text = 'Unknown status';
                $status_color = 'color: rgba(224, 11, 11, 0.07)';
                break;
        }
   


        
             //  dd($reciepts,$id);
            // Generate the PDF
            //  return view('receipt',compact('reciepts','locArrLocation','company_name','user_name','status_text','status_color','sold_by_fixed'));
           $pdf = app('dompdf.wrapper');
           $pdf->loadView('receipt', compact('reciepts','locArrLocation','company_name','user_name','status_text','status_color','sold_by_fixed'));
            //    $pdf = Pdf::loadView('invoice');
            // dd($pdf->output());

            $recieptno = rand(1111,9999);
            // Generate a filename
            // $fileName = 'receipt_'.$recieptno.'.pdf';
            $fileName = (@$reciepts->invoiceId) ? $reciepts->invoiceId.'.pdf'  : $recieptno.'.pdf';
            if($reciepts){
                return response($pdf->output(), 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="'.$fileName.'"');
            }else{
                return back()->withErrors(['error_get' => 'Receipt not found by ID']);
            }

            // dd($fileName);

            // Save the PDF to storage (optional, if you want to save it)
            // $pdf->save(storage_path('app/public/invoices/' . $fileName));

            // Return the file as a download
          
        }else{
            return back()->withErrors(['error_get' => 'Receipt not found by ID']);
        }


    }
    public function customCheckoutSubscribe(Request $request)
    {  
          decrypt($request->id);
          $id =  decrypt($request->id);	
       
        //   dd( $id,   $request->topup);
        $currency = GeneralMethod::convertINRToUSD();
        $top_up_coins_cart = '';
        $company_coins_cart= '';
        $subject =  '';
        $type =  '';

          if($request->topup){
            $top_up_coins_cart = DB::table('top_up_coin_packages')
            ->where('top_up_coin_packages.id',$id)
            ->select('top_up_coin_packages.*')
            ->first();
            $subject =  'Custom Top-Up package';
            $type =  @$top_up_coins_cart->event_type;
          }else{
            $company_coins_cart = DB::table('coin_packages')
          ->join('coin_package_types','coin_package_types.id','=','coin_packages.coin_package_type_id')
          ->where('coin_packages.id',$id)
          ->select('coin_packages.*','coin_package_types.title')
          ->first();
          $subject =  'Custom subscription package';
          $type =  @$company_coins_cart->event_type;
          }

          
        $id =  decrypt($request->id);

        $company_id       			=	auth()->user()->company_id;
        $clients  = DB::table('clients')
        ->leftJoin('companies', 'companies.id', '=', 'clients.company_id')
        ->leftJoin('users','users.company_id','=','companies.id',)
        ->select('clients.id','companies.company_name','companies.id as ComID','users.name','users.email','users.mobile')
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
				'client_location.address',
				'client_location.postcode',
				'client_location.country_id',
				'city_list.city as city_name',
				'state_list.state as state_name',
				'countries.country_name'
			)
			->where('client_id', $clients->id)
			->first();
            $company_name  = @$clients->company_name;
            $company_id  = @$clients->ComID;
            $user_name  = @$clients->name;
            $user_email  = @$clients->email;
            $mobile  = @$clients->mobile;
            $locArrLocation  = '';
            if($locInfo){
                $city_name = @$locInfo->city;
				$locArrLocation = ucwords(@$locInfo->address) . ' ' . ucwords($city_name) . ' , ' . ucwords(@$locInfo->state_name) . ' , ' . ucwords(@$locInfo->country_name);
            }
            
            $message = "<p><strong>Name:</strong> {$user_name}</p>
                        <p><strong>Company Name:</strong> {$company_name} </p>
                        <p><strong>Location:</strong> {$locArrLocation}</p>
                        <p><strong>ID:</strong> {$company_id}</p>
                        <p><strong>Number:</strong> {$mobile}</p>
                        <p><strong>Email Address:</strong> {$user_email}</p>
                        <p><strong>Package Type:</strong> {$type} </p>
                        <p><strong>Issue:</strong> {$subject}</p>";
            $datamail = ['message' => $message];
            $basecompany = GeneralMethod::baseCompany();
            $senderName = $basecompany->companyName ?? '';
            $fromAddress = $basecompany->companyEmail ?? '';
            $cc = [];
            $mailService = new MailService();
            $to_Mail_Id = "jeroinf@yopmail.com";

            QueueNotificationMailService::dispatch(
                $to_Mail_Id,
                $subject,
                'email.common.index',
                (object) $datamail,
                [],
                $fromAddress,
                $senderName,
                $cc,
                $company_id
            )->onConnection('emails');

            // $result =   $mailService->sendEmail($to_Mail_Id, $subject, 'email.common.index', (object) $datamail, [], $fromAddress, $senderName, $cc);

          dd($top_up_coins_cart,$company_coins_cart);
         


        		
		$company_id       			=	auth()->user()->company_id;
		
        return Reply::dataOnly(['status' => 'success']);

        // return view('service_company.subscription.checkout-subscribe', $this->data);
    }



}
