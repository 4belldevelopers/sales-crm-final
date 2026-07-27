@extends('layouts.superadmin_layout')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">{{ $update ? 'Edit Coupon' : 'Add Coupon' }}</h4>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('superadmin.coupons.store') }}">
                    @csrf
                    @if ($update)
                        <input type="hidden" name="coupon_id" value="{{ encrypt($coupon->id) }}">
                    @endif

                    <div class="row">
                        <div class="col-md-3">
                            <label for="code">Coupon Code</label>
                            <input type="text" class="form-control form-control-solid" name="code" id="code"
                                value="{{ old('code', $update ? $coupon->code : '') }}" placeholder="e.g. FREE50" required>
                        </div>
                        <div class="col-md-2">
                            <label for="discount_percent">Discount %</label>
                            <input type="number" step="0.01" min="0.01" max="100" class="form-control form-control-solid"
                                name="discount_percent" id="discount_percent"
                                value="{{ old('discount_percent', $update ? $coupon->discount_percent : '') }}" required>
                        </div>
                        <div class="col-md-2">
                            <label for="expiry_date">Expiry Date</label>
                            <input type="date" class="form-control form-control-solid" name="expiry_date" id="expiry_date"
                                value="{{ old('expiry_date', $update ? $coupon->expiry_date->format('Y-m-d') : '') }}" required>
                        </div>
                        <div class="col-md-2">
                            <label for="max_redemptions">Max Redemptions</label>
                            <input type="number" min="1" class="form-control form-control-solid" name="max_redemptions"
                                id="max_redemptions" value="{{ old('max_redemptions', $update ? $coupon->max_redemptions : '') }}" required>
                        </div>
                        <div class="col-md-2">
                            <label class="d-block">Status</label>
                            <label class="form-check form-switch form-check-custom form-check-solid mt-2">
                                <input class="form-check-input" type="checkbox" name="status" value="1"
                                    {{ (!$update || $coupon->status === 'active') ? 'checked' : '' }}>
                                <span class="form-check-label">Active</span>
                            </label>
                        </div>
                    </div>

                    <div class="mt-6">
                        <button type="submit" class="btn btn-primary">Save Coupon</button>
                        <a href="{{ route('superadmin.coupons.index') }}" class="btn btn-light">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
