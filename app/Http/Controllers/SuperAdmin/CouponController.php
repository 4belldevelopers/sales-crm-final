<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Services\SuperAdmin\SuperAdminCouponMutationService;
use App\Services\SuperAdmin\SuperAdminCouponReadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Class CouponController
 *
 * Intent: Super Admin Coupons management controller refactored to strict CQRS architecture.
 * Read queries (index, pagination, item fetch) are 100% served by SuperAdminCouponReadService
 * via Elasticsearch (local_coupons) and L2 Redis/Dragonfly caching (0% MySQL reads).
 * State mutations (store, toggleStatus, destroy) are processed by SuperAdminCouponMutationService
 * under Two-Tier Idempotency (sys_idempotency_keys) and Transactional Outbox (sys_outbox_events).
 *
 * Preserves 100% identical UI view variables, workflows, and response payloads.
 *
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class CouponController extends SuperAdminBaseController
{
    public function __construct(
        protected SuperAdminCouponReadService $readService,
        protected SuperAdminCouponMutationService $mutationService
    ) {
        parent::__construct();
        $this->pageTitle = 'Coupons';
    }

    /**
     * Display listing of coupons.
     * Served 100% via Elasticsearch and L2 Dragonfly/Redis Cache (0% MySQL reads).
     */
    public function index(Request $request)
    {
        $perPage = (int) $request->input('per_page', $request->input('size', $request->input('limit', 20)));
        $page = (int) $request->input('page', 1);

        $this->coupons = $this->readService->getPaginatedCoupons($request->query(), $perPage, $page);

        return view('super-admin.coupons.index', $this->data);
    }

    /**
     * Show form for adding or editing a coupon.
     */
    public function addEdit(Request $request)
    {
        $this->update = false;

        if ($request->id) {
            $decryptedId = (int) decrypt($request->id);
            $coupon = $this->readService->getCouponById($decryptedId);

            if (! $coupon) {
                abort(404, 'Coupon not found.');
            }

            $this->coupon = $coupon;
            $this->update = true;
        }

        return view('super-admin.coupons.add-edit', $this->data);
    }

    /**
     * Store or update a coupon using Transactional Outbox and Two-Tier Idempotency.
     */
    public function store(Request $request)
    {
        $couponId = $request->coupon_id ? (int) decrypt($request->coupon_id) : null;

        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:255|unique:coupons,code,'.($couponId ?? 'NULL').',id',
            'discount_percent' => 'required|numeric|min:0.01|max:100',
            'expiry_date' => 'required|date',
            'max_redemptions' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = [
            'coupon_id' => $couponId,
            'code' => strtoupper((string) $request->code),
            'discount_percent' => $request->discount_percent,
            'expiry_date' => $request->expiry_date,
            'max_redemptions' => $request->max_redemptions,
            'status' => $request->status ? 'active' : 'inactive',
        ];

        $idempotencyKey = $request->header('X-Idempotency-Key') ?? $request->input('idempotency_key');

        $this->mutationService->saveCoupon($data, $idempotencyKey, auth()->id());

        return redirect()->route('superadmin.coupons.index')->with('success', 'Coupon saved successfully.');
    }

    /**
     * Toggle active/inactive status using Transactional Outbox and Two-Tier Idempotency.
     */
    public function toggleStatus(Request $request)
    {
        $id = (int) $request->id;
        $idempotencyKey = $request->header('X-Idempotency-Key');

        $result = $this->mutationService->toggleStatus($id, $idempotencyKey);

        return response()->json([
            'status' => 'success',
            'new_status' => $result['new_status'],
        ]);
    }

    /**
     * Delete a coupon using Transactional Outbox and Two-Tier Idempotency.
     */
    public function destroy(Request $request)
    {
        $id = (int) $request->id;
        $idempotencyKey = $request->header('X-Idempotency-Key');

        $this->mutationService->destroyCoupon($id, $idempotencyKey);

        return response()->json(['status' => 'success']);
    }
}
