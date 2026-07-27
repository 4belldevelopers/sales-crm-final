@extends('layouts.superadmin_layout')

@section('content')
<div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
        <div class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
            <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">Coupons</h1>
        </div>
        <div class="d-flex align-items-center py-1">
            <a href="{{ route('superadmin.coupons.addEdit') }}" class="btn btn-sm btn-light btn-active-primary">
                <i class="fa fa-plus-circle me-1"></i> Add Coupon
            </a>
        </div>
    </div>
</div>

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Discount</th>
                            <th>Expiry Date</th>
                            <th>Redemptions</th>
                            <th>Status</th>
                            <th style="width: 150px">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($coupons as $coupon)
                            <tr id="row-{{ $coupon->id }}">
                                <td><span class="fw-bold text-gray-800">{{ $coupon->code }}</span></td>
                                <td>{{ rtrim(rtrim(number_format($coupon->discount_percent, 2), '0'), '.') }}%</td>
                                <td>{{ $coupon->expiry_date->format('d M Y') }}</td>
                                <td>{{ $coupon->times_redeemed }} / {{ $coupon->max_redemptions }}</td>
                                <td>
                                    <a href="javascript:;" class="coupon-toggle-status" data-id="{{ $coupon->id }}">
                                        @if($coupon->status === 'active')
                                            <span class="badge bg-light-success text-success fs-7 py-1 px-3">Active</span>
                                        @else
                                            <span class="badge bg-light-danger text-danger fs-7 py-1 px-3">Inactive</span>
                                        @endif
                                    </a>
                                </td>
                                <td class="d-flex align-items-center gap-3">
                                    <a href="{{ route('superadmin.coupons.addEdit', encrypt($coupon->id)) }}" data-toggle="tooltip" title="Edit">
                                        <i class="fa fa-edit text-primary fs-4"></i>
                                    </a>
                                    <a href="javascript:;" class="coupon-delete" data-id="{{ $coupon->id }}" data-toggle="tooltip" title="Delete">
                                        <i class="fa fa-trash text-danger fs-4"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-10">No coupons yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                {{ $coupons->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('footer-script')
<script>
    $(document).on('click', '.coupon-toggle-status', function(e) {
        e.preventDefault();
        var $el = $(this);
        var id = $el.data('id');
        $.ajax({
            type: 'POST',
            url: "{{ route('superadmin.coupons.toggleStatus') }}",
            data: { '_token': "{{ csrf_token() }}", 'id': id },
            success: function(response) {
                if (response.status === 'success') {
                    location.reload();
                }
            }
        });
    });

    $(document).on('click', '.coupon-delete', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        if (!confirm('Delete this coupon? This cannot be undone.')) { return; }
        $.ajax({
            type: 'POST',
            url: "{{ route('superadmin.coupons.destroy') }}",
            data: { '_token': "{{ csrf_token() }}", 'id': id },
            success: function(response) {
                if (response.status === 'success') {
                    $('#row-' + id).remove();
                }
            }
        });
    });
</script>
@endpush
