@extends('layouts.superadmin_layout')

<style>
    #kt_body
    {
        overflow-x: hidden;
    }
</style>

@section('create-button')
<a href="{{ route('superadmin.company.addEditTopUpcoinPackages') }}" class="btn btn-dark btn-sm m-l-15"><i class="fa fa-plus-circle"></i> @lang('app.createNew')</a>
@endsection

@section('content')
<div class="toolbar" id="kt_toolbar">
	<div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
		<div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
			<h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">Top Up Coins <span class="h-20px border-gray-200 border-start ms-3 mx-2 d-none"></span>
			<small class="text-muted fs-7 fw-bold my-1 ms-1 d-none">#XRS-45670</small>
		</div>
        <div class="d-flex align-items-center py-1">
		        <a href="{{ route('superadmin.company.addEditTopUpcoinPackages') }}" class="btn btn-sm btn-light btn-active-primary">
                <span class="svg-icon svg-icon-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor"></rect>
                        <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="currentColor"></rect>
                    </svg>
                </span>    
                Add Top Up</a>
		</div>
	</div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="col-md-12">
                    {{-- @if(!is_null(@$packageData) && @$paymentSetting->stripe_status == 'active') --}}

                        {{-- <div class="alert alert-success col-md-12 fw-normal">
                            <div class="col-md-12"> @lang('messages.stripePlanIdRequired')</div>
                        </div> --}}
                    {{-- @endif --}}
                </div>
                <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                    <thead>
                        <tr>
                            <th style="width: 20px">No.</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Amount (USD)</th>
                            <th>Coins</th>
                            <th>Discount(%)</th>
                            <th>Per Coin Price</th>
                            <th>Per Coin Price(USD)</th>
                            <th>@lang('app.status')</th>
                            <th style="width: 150px">@lang('app.action')</th>
                        </tr>

                    </thead>
                    <tbody>
                        @forelse (@$coinEvents as $key=>$item)
                        <tr id="row-{{ $item->id }}">
                            <td>{{ $key+1 }}.</td>
                            <td> {{ ($item->event_type) ?$item->event_type : '0' }} </td>
                            <td> {{ ($item->price) ?$item->price : 0 }} </td>
                            <td> {{ ($item->price_usd) ?$item->price_usd : 0 }} </td>
                            <td> {{ ($item->coins) ? $item->coins : 0 }} </td>
                            <td> {{ ($item->discount) ?($item->discount) : 0 }}%</td>
                            <td> {{ ($item->per_coin_rate) ?($item->per_coin_rate) : 0 }}</td>
                            <td> {{ ($item->per_coin_rate_usd) ?($item->per_coin_rate_usd) : 0 }}</td>
                            <td>
                                @if($item->status)
                                    <label class="badge text-success text-dark-800 fw-400 fs-6 bg-light-success badge-sm py-1 px-3">@lang('app.active')</label>
                                @else
                                    <label class="badge text-danger text-dark-800 fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">@lang('app.inactive')</label>
                                @endif
                            </td>
                            <td class="d-flex justify-content-evenly align-items-center border-bottom-0" style="margin: 20px 0 0 0;width: 100;">
                                    <a href="{{ route('superadmin.company.addEditTopUpcoinPackages', encrypt($item->id)) }}" class="" data-toggle="tooltip" data-original-title="@lang('app.edit')">
                                    <i class="fa fa-edit text-primary fs-4" aria-hidden="true"></i>
                                </a>
                            </td>
                        </tr>
                        @empty

                        @endforelse


                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('footer-script')
    <script>
       $('body').on('click', '.sa-params', function(){
            var id = $(this).data('row-id');
            swal({
                title: "@lang('errors.areYouSure')",
                text: "@lang('errors.deleteWarning')",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "@lang('app.delete')",
                cancelButtonText: "@lang('app.cancel')",
                closeOnConfirm: true,
                closeOnCancel: true
            }, function(isConfirm){
                if (isConfirm) {

                    var url = "{{ route('superadmin.packages.destroy',':id') }}";
                    url = url.replace(':id', id);

                    var token = "{{ csrf_token() }}";

                    $.easyAjax({
                        type: 'POST',
                        url: url,
                        data: {'_token': token, '_method': 'DELETE'},
                        success: function (response) {
                            if (response.status == "success") {
                                $.unblockUI();
//                                    swal("Deleted!", response.message, "success");
                                $('#row-'+id).remove();
                            }
                        }
                    });
                }
            });
        });
    </script>

@endpush