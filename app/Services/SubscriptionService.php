<?php

/**
 * SubscriptionService — extracted subscription & wallet data aggregation service.
 *
 * Extracted from ManageSubscriptionController (V2 Phase 6).
 * All methods return plain data arrays for view rendering; no direct view knowledge here.
 *
 * Tenancy: Every raw DB::table() query explicitly filters by company_id parameter (Rule #3).
 */

namespace App\Services;

use App\Models\Company;
use App\Models\CompanyPackage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SubscriptionService
{
    /**
     * Fetch complete Wallet Center dataset for a tenant company.
     *
     * @param int $companyId
     * @return array
     */
    public function getWalletCenterData(int $companyId): array
    {
        $packageActive = DB::table('package_active')
            ->select('package_active.*')
            ->where('package_active.company_id', '=', $companyId)
            ->first();

        $cartsCount = DB::table('carts')
            ->where('company_id', '=', $companyId)
            ->where('status', '=', 0)
            ->count();

        $topUpCoinPackagesOp = Cache::remember('top_up_coin_packages_op', 86400, function () {
            return DB::table('top_up_coin_packages')->where('event_type', '=', 'operational')->where('status', '=', 1)->get();
        });

        $topUpCoinPackagesSal = Cache::remember('top_up_coin_packages_sal', 86400, function () {
            return DB::table('top_up_coin_packages')->where('event_type', '=', 'sales')->where('status', '=', 1)->get();
        });

        $company = Company::withoutGlobalScope('company')->findOrFail($companyId);
        $companyPackage = CompanyPackage::activePackage($companyId);

        $topUpLogsOp = DB::table('top_up_logs')
            ->select('top_up_logs.*')
            ->leftJoin('carts', 'carts.id', '=', 'top_up_logs.cart_id')
            ->where('top_up_logs.event_type', '=', 'operational')
            ->where('top_up_logs.company_id', '=', $companyId)
            ->get();

        $topUpLogsSal = DB::table('top_up_logs')
            ->select('top_up_logs.*')
            ->leftJoin('carts', 'carts.id', '=', 'top_up_logs.cart_id')
            ->where('top_up_logs.event_type', '=', 'sales')
            ->where('top_up_logs.company_id', '=', $companyId)
            ->get();

        $account = $company;

        $packageActiveAll = DB::table('package_active')
            ->join('package_invoice', 'package_invoice.id', '=', 'package_active.package_invoice_id')
            ->join('package_order_items', 'package_order_items.invoice_id', '=', 'package_invoice.id')
            ->join('package_features', 'package_features.id', '=', 'package_order_items.feature_id')
            ->select('package_active.*', 'package_features.title', 'package_features.field_name', 'package_features.id as gg_is')
            ->where('package_active.company_id', $companyId)
            ->get();

        $packageActiveDetails = DB::table('package_active')
            ->join('package_invoice', 'package_invoice.id', '=', 'package_active.package_invoice_id')
            ->join('packages', 'packages.id', '=', 'package_invoice.package_id')
            ->select('package_active.*', 'packages.name')
            ->where('package_active.company_id', $companyId)
            ->first();

        // Operational current coins batch eager loading
        $rawOpCoins = DB::table('company_coins')
            ->join('coin_orders', 'coin_orders.id', '=', 'company_coins.coin_order_id')
            ->join('coin_packages', 'coin_packages.id', '=', 'coin_orders.coin_package_id')
            ->join('coin_package_types', 'coin_package_types.id', '=', 'coin_packages.coin_package_type_id')
            ->where('company_coins.company_id', $companyId)
            ->where('company_coins.event_type', 'operational')
            ->where('coin_orders.status', 1)
            ->select('company_coins.*', 'coin_package_types.title', 'coin_packages.custome_pck', 'coin_orders.id as coin_order_id', 'coin_orders.coins as order_coins', 'coin_orders.coin_package_id', 'coin_orders.price as order_price')
            ->get();

        $opCoinOrderIds = $rawOpCoins->pluck('coin_order_id')->filter()->unique()->toArray();
        $opOrderEventsGrouped = collect();
        if (!empty($opCoinOrderIds)) {
            $opOrderEventsGrouped = DB::table('coin_order_events')
                ->join('coin_events', 'coin_events.id', '=', 'coin_order_events.coin_event_id')
                ->whereIn('coin_order_id', $opCoinOrderIds)
                ->select('coin_order_events.*', 'coin_events.title')
                ->get()
                ->groupBy('coin_order_id');
        }

        $currentCoinsOperational = $rawOpCoins->map(function ($coinOrder) use ($opOrderEventsGrouped) {
            $coinOrderId = $coinOrder->coin_order_id;
            $coinOrder->coin_order_events = isset($opOrderEventsGrouped[$coinOrderId])
                ? $opOrderEventsGrouped[$coinOrderId]->toArray()
                : [];
            return (array) $coinOrder;
        })->toArray();

        // Sales current coins batch eager loading
        $rawSalCoins = DB::table('company_coins')
            ->join('coin_orders', 'coin_orders.id', '=', 'company_coins.coin_order_id')
            ->join('coin_packages', 'coin_packages.id', '=', 'coin_orders.coin_package_id')
            ->join('coin_package_types', 'coin_package_types.id', '=', 'coin_packages.coin_package_type_id')
            ->where('company_coins.company_id', $companyId)
            ->where('company_coins.event_type', 'sales')
            ->where('coin_orders.status', 1)
            ->select('company_coins.*', 'coin_package_types.title', 'coin_packages.custome_pck', 'coin_orders.id as coin_order_id', 'coin_orders.coins as order_coins', 'coin_orders.coin_package_id', 'coin_orders.price as order_price')
            ->get();

        $salCoinOrderIds = $rawSalCoins->pluck('coin_order_id')->filter()->unique()->toArray();
        $salOrderEventsGrouped = collect();
        if (!empty($salCoinOrderIds)) {
            $salOrderEventsGrouped = DB::table('coin_order_events')
                ->join('coin_events', 'coin_events.id', '=', 'coin_order_events.coin_event_id')
                ->whereIn('coin_order_id', $salCoinOrderIds)
                ->select('coin_order_events.*', 'coin_events.title')
                ->get()
                ->groupBy('coin_order_id');
        }

        $currentCoinsSales = $rawSalCoins->map(function ($coinOrder) use ($salOrderEventsGrouped) {
            $coinOrderId = $coinOrder->coin_order_id;
            $coinOrder->coin_order_events = isset($salOrderEventsGrouped[$coinOrderId])
                ? $salOrderEventsGrouped[$coinOrderId]->toArray()
                : [];
            return (array) $coinOrder;
        })->toArray();

        // Master Coin Packages & Feature Points cached in Redis
        $masterPackagesCached = Cache::remember('master_coin_packages_with_features', 86400, function () {
            $rawOpPackages = DB::table('coin_packages')
                ->join('coin_package_types', 'coin_package_types.id', '=', 'coin_packages.coin_package_type_id')
                ->select('coin_packages.*', 'coin_package_types.title')
                ->where('coin_packages.custome_pck', '!=', 1)
                ->where('coin_packages.event_type', '=', 'operational')
                ->get();

            $rawSalPackages = DB::table('coin_packages')
                ->join('coin_package_types', 'coin_package_types.id', '=', 'coin_packages.coin_package_type_id')
                ->select('coin_packages.*', 'coin_package_types.title', DB::raw('
                (SELECT COUNT(*) FROM coin_events 
                 WHERE coin_events.event_type = coin_packages.event_type) as event_count'))
                ->where('coin_packages.custome_pck', '!=', 1)
                ->where('coin_packages.event_type', '=', 'sales')
                ->get();

            $allPkgIds = $rawOpPackages->pluck('id')->merge($rawSalPackages->pluck('id'))->filter()->unique()->toArray();
            $pkgPointsGrouped = collect();
            if (!empty($allPkgIds)) {
                $pkgPointsGrouped = DB::table('coin_package_points')
                    ->whereIn('coin_package_points.coin_package_id', $allPkgIds)
                    ->select('coin_package_points.*')
                    ->get()
                    ->groupBy('coin_package_id');
            }

            $opPackages = $rawOpPackages->map(function ($row) use ($pkgPointsGrouped) {
                $row->features = isset($pkgPointsGrouped[$row->id])
                    ? $pkgPointsGrouped[$row->id]->toArray()
                    : [];
                return (array) $row;
            });

            $salPackages = $rawSalPackages->map(function ($row) use ($pkgPointsGrouped) {
                $row->features = isset($pkgPointsGrouped[$row->id])
                    ? $pkgPointsGrouped[$row->id]->toArray()
                    : [];
                return (array) $row;
            });

            return [
                'operational_package' => $opPackages,
                'sales_package'       => $salPackages,
            ];
        });

        $operationalPackage = $masterPackagesCached['operational_package'];
        $salesPackage       = $masterPackagesCached['sales_package'];
        $username           = auth()->user()->name ?? '';

        $actualCount = DB::table('coin_transection_logs')
            ->select('coin_transection_logs.coins')
            ->orderByDesc('coin_transection_logs.id')
            ->where('coin_transection_logs.event_type', '=', 'operational')
            ->where('coin_transection_logs.transection_type', '=', 'credit')
            ->where('coin_transection_logs.company_id', $companyId)
            ->first();

        $remainCoins = DB::table('company_coins')
            ->select('company_coins.coins')
            ->orderByDesc('company_coins.id')
            ->where('company_coins.event_type', '=', 'operational')
            ->where('company_coins.company_id', $companyId)
            ->first();

        $actualCountVal = $actualCount->coins ?? 0;
        $remainCoinsVal = $remainCoins->coins ?? 0;

        $opTotalCredited = (int) DB::table('coin_transection_logs')
            ->where('company_id', $companyId)
            ->where('event_type', 'operational')
            ->where('transection_type', 'credit')
            ->sum('coins');

        $opTotalPurchased = $opTotalCredited;
        $opTotalSpend     = collect($currentCoinsOperational)->sum('order_price');
        $opCoinsUsed      = max(0, $opTotalPurchased - (int) $remainCoinsVal);
        $opExpiry         = collect($currentCoinsOperational)->pluck('end_date')->filter()->max();
        $opPlanExpiry     = $opExpiry ? Carbon::parse($opExpiry) : null;
        $opDaysLeft       = $opPlanExpiry ? max(0, (int) now()->diffInDays($opPlanExpiry, false)) : 0;

        $actualCountSales = DB::table('coin_transection_logs')
            ->select('coin_transection_logs.coins')
            ->orderByDesc('coin_transection_logs.id')
            ->where('coin_transection_logs.event_type', '=', 'sales')
            ->where('coin_transection_logs.transection_type', '=', 'credit')
            ->where('coin_transection_logs.company_id', $companyId)
            ->first();

        $remainCoinsSales = DB::table('company_coins')
            ->select('company_coins.coins')
            ->orderByDesc('company_coins.id')
            ->where('company_coins.event_type', '=', 'sales')
            ->where('company_coins.company_id', $companyId)
            ->first();

        $actualCountSalesVal = $actualCountSales->coins ?? 0;
        $remainCoinsSalesVal = $remainCoinsSales->coins ?? 0;

        $salTotalCredited = (int) DB::table('coin_transection_logs')
            ->where('company_id', $companyId)
            ->where('event_type', 'sales')
            ->where('transection_type', 'credit')
            ->sum('coins');

        $salTotalPurchased = $salTotalCredited;
        $salTotalSpend     = collect($currentCoinsSales)->sum('order_price');
        $salCoinsUsed      = max(0, $salTotalPurchased - (int) $remainCoinsSalesVal);
        $salExpiry         = collect($currentCoinsSales)->pluck('end_date')->filter()->max();
        $salPlanExpiry     = $salExpiry ? Carbon::parse($salExpiry) : null;
        $salDaysLeft       = $salPlanExpiry ? max(0, (int) now()->diffInDays($salPlanExpiry, false)) : 0;

        $opUsedPct  = $opTotalPurchased > 0 ? min(100, round(($opCoinsUsed / $opTotalPurchased) * 100)) : 0;
        $salUsedPct = $salTotalPurchased > 0 ? min(100, round(($salCoinsUsed / $salTotalPurchased) * 100)) : 0;

        return [
            'package_active'             => $packageActive,
            'carts_count'                => $cartsCount,
            'top_up_coin_packages_op'    => $topUpCoinPackagesOp,
            'top_up_coin_packages_sal'   => $topUpCoinPackagesSal,
            'company'                    => $company,
            'companyPackage'             => $companyPackage,
            'company_id'                 => $companyId,
            'top_up_logs_op'             => $topUpLogsOp,
            'top_up_logs_sal'            => $topUpLogsSal,
            'cart_transaction_history'   => null, // Lazy-loaded via AJAX when opening Transactions tab
            'account'                    => $account,
            'package_active_all'         => $packageActiveAll,
            'package_active_details'     => $packageActiveDetails,
            'current_coins_operational'  => $currentCoinsOperational,
            'current_coins_sales'        => $currentCoinsSales,
            'operational_package'        => $operationalPackage,
            'sales_package'              => $salesPackage,
            'username'                   => $username,
            'actual_count'               => $actualCountVal,
            'remain_coins'               => $remainCoinsVal,
            'op_total_purchased'         => $opTotalPurchased,
            'op_total_spend'             => $opTotalSpend,
            'op_coins_used'              => $opCoinsUsed,
            'op_plan_expiry'             => $opPlanExpiry,
            'op_days_left'               => $opDaysLeft,
            'actual_count_sales'         => $actualCountSalesVal,
            'remain_coins_sales'         => $remainCoinsSalesVal,
            'sal_total_purchased'        => $salTotalPurchased,
            'sal_total_spend'            => $salTotalSpend,
            'sal_coins_used'             => $salCoinsUsed,
            'sal_plan_expiry'            => $salPlanExpiry,
            'sal_days_left'              => $salDaysLeft,
            'op_used_pct'                => $opUsedPct,
            'sal_used_pct'               => $salUsedPct,
        ];
    }
}
