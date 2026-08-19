<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CouponController extends SuperAdminBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'Coupons';
    }

    public function index(Request $request)
    {
        $perPage = (int) $request->input('per_page', $request->input('size', $request->input('limit', 20)));
        $perPage = min(max($perPage, 1), 100);
        $this->coupons = Coupon::orderByDesc('id')->paginate($perPage)->appends($request->query());

        return view('super-admin.coupons.index', $this->data);
    }

    public function addEdit(Request $request)
    {
        $this->update = false;

        if ($request->id) {
            $this->coupon = Coupon::withoutGlobalScopes()->findOrFail(decrypt($request->id));
            $this->update = true;
        }

        return view('super-admin.coupons.add-edit', $this->data);
    }

    public function store(Request $request)
    {
        $couponId = $request->coupon_id ? decrypt($request->coupon_id) : null;

        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:255|unique:coupons,code,' . ($couponId ?? 'NULL') . ',id',
            'discount_percent' => 'required|numeric|min:0.01|max:100',
            'expiry_date' => 'required|date',
            'max_redemptions' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = [
            'code' => strtoupper($request->code),
            'discount_percent' => $request->discount_percent,
            'expiry_date' => $request->expiry_date,
            'max_redemptions' => $request->max_redemptions,
            'status' => $request->status ? 'active' : 'inactive',
        ];

        if ($couponId) {
            Coupon::where('id', $couponId)->update($data);
        } else {
            $data['created_by'] = auth()->id();
            Coupon::create($data);
        }

        return redirect()->route('superadmin.coupons.index')->with('success', 'Coupon saved successfully.');
    }

    public function toggleStatus(Request $request)
    {
        $coupon = Coupon::withoutGlobalScopes()->findOrFail($request->id);
        $coupon->status = $coupon->status === 'active' ? 'inactive' : 'active';
        $coupon->save();

        return response()->json(['status' => 'success', 'new_status' => $coupon->status]);
    }

    public function destroy(Request $request)
    {
        Coupon::where('id', $request->id)->delete();

        return response()->json(['status' => 'success']);
    }
}
