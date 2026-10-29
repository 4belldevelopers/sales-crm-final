<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Helper\Reply;
use App\Http\Requests\SuperAdmin\StoreCurrency;
use App\Models\Currency;
use App\Services\CurrencyService;
use App\Services\ReferenceDataCache;
use Illuminate\Support\Facades\DB;

/**
 * SuperAdmin Currency Settings & FX Engine Controller
 *
 * Intent: Manages global multi-tenant currency definitions, exchange rates,
 * and synchronizes MySQL writes with static JSON stores (currencies.json).
 *
 * Tenancy: Global SuperAdmin configuration. Changes invalidate tenant cache atomically.
 * CQRS: Writes persist to MySQL and atomically recompile currencies.json for zero-SQL reads.
 */
class SuperAdminCurrencyController extends SuperAdminBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'currencySetting';
        $this->pageIcon = __('ti-settings');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Order base currencies first (USD, INR, EUR, GBP), then alphabetically
        $this->currencies = Currency::orderByRaw("FIELD(currency_code, 'USD', 'INR', 'EUR', 'GBP') DESC, currency_name ASC")->get();

        // Calculate KPI summary metrics for Tier 1 cards
        $this->totalCurrencies = $this->currencies->count();
        $this->baseCurrencies = $this->currencies->whereIn('currency_code', ['USD', 'INR'])->count();
        $this->lastSyncTimestamp = Currency::max('updated_at');

        // Master currency reference catalog for quick-select preset modal
        $this->masterCurrencies = ReferenceDataCache::masterCurrencies();
        $this->masterCurrenciesCount = $this->masterCurrencies->count();

        // Country name mapping for all system currencies (0% SQL queries)
        $this->countryMap = CurrencyService::getCountryMap();

        return view('super-admin.currencies.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->masterCurrencies = ReferenceDataCache::masterCurrencies();

        return view('super-admin.currencies.create', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCurrency $request)
    {
        $code = strtoupper(trim($request->currency_code));

        // Assign safe collision-free getKey
        $resolvedKey = CurrencyService::resolveCurrencyKey($code);
        $existingKeys = Currency::pluck('getKey')->toArray();

        if (in_array($resolvedKey, $existingKeys) || ($resolvedKey === 4 && $code !== 'USD')) {
            $maxKey = (int) Currency::max('getKey');
            $getKey = max($maxKey + 1, 133);
        } else {
            $getKey = $resolvedKey;
        }

        DB::transaction(function () use ($request, $code, $getKey) {
            $currency = new Currency;
            $currency->currency_name = trim($request->currency_name);
            $currency->currency_symbol = trim($request->currency_symbol);
            $currency->currency_code = $code;
            $currency->inr_based_rates = round((float) $request->inr_based_rates, 4);
            $currency->usd_based_rates = round((float) $request->usd_based_rates, 4);
            $currency->getKey = $getKey;
            $currency->save();
        });

        // Atomic compilation to currencies.json and Redis cache purge
        CurrencyService::syncCurrenciesJson();

        return Reply::redirect(route('superadmin.currency-settings.index'), __('messages.createdSuccessfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->route('superadmin.currency-settings.edit', $id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->currency = Currency::withoutGlobalScopes()->findOrFail($id);
        $this->masterCurrencies = ReferenceDataCache::masterCurrencies();

        return view('super-admin.currencies.edit', $this->data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreCurrency $request, $id)
    {
        $currency = Currency::withoutGlobalScopes()->findOrFail($id);
        $code = strtoupper(trim($request->currency_code));
        $isBase = in_array(strtoupper($currency->currency_code), ['USD', 'INR']);

        // Guard: Prevent renaming base anchor currencies code
        if ($isBase && $code !== strtoupper($currency->currency_code)) {
            return Reply::error('The ISO code of system anchor currencies (USD and INR) cannot be modified.');
        }

        DB::transaction(function () use ($currency, $request, $code, $isBase) {
            $currency->currency_name = trim($request->currency_name);
            $currency->currency_symbol = trim($request->currency_symbol);
            if (! $isBase) {
                $currency->currency_code = $code;
            }
            $currency->inr_based_rates = round((float) $request->inr_based_rates, 4);
            $currency->usd_based_rates = round((float) $request->usd_based_rates, 4);
            $currency->save();
        });

        // Atomic compilation to currencies.json and Redis cache purge
        CurrencyService::syncCurrenciesJson();

        return Reply::redirect(route('superadmin.currency-settings.index'), __('messages.createdSuccessfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $currency = Currency::withoutGlobalScopes()->findOrFail($id);
        $code = strtoupper(trim($currency->currency_code));

        // Guard: Base anchor currencies cannot be deleted
        if (in_array($code, ['USD', 'INR']) || in_array((int) $currency->getKey, [0, 4])) {
            return Reply::error('Base system anchor currencies (USD and INR) cannot be deleted as they anchor the global FX engine.');
        }

        // Guard: Check if currency is in use by companies
        $inCompanyUse = DB::table('companies')->where('currency', $currency->getKey)->exists();
        if ($inCompanyUse) {
            return Reply::error('This currency cannot be deleted because it is currently assigned as the default currency for one or more companies.');
        }

        // Delete record safely
        $currency->delete();

        // Atomic compilation to currencies.json and Redis cache purge
        CurrencyService::syncCurrenciesJson();

        return Reply::success(__('messages.recordDeleted'));
    }
}
