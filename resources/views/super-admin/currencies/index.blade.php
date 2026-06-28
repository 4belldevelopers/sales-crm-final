@extends('layouts.superadmin_layout')

<style>
    body
    {
        overflow-x: hidden;
    }
</style>

@section('create-button')
<a href="{{ route('superadmin.currency-settings.create') }}" class="btn btn-dark btn-sm m-l-15">
    <i class="fa fa-plus-circle"></i> @lang('app.createNew')
</a>
@endsection
@section('content')

<!--begin::toolbar-->
<div class="toolbar" id="kt_toolbar">
	<div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
		<div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
			<h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">Currency settings</h1>
        </div>
        <div class="d-flex align-items-center py-1">
               <a href="javascript:;" class="btn btn-sm btn-light btn-active-primary mr-2 updateCurrencyRates">
                    <span class="svg-icon svg-icon-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path d="M17 1v4h-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M3 11a8 8 0 0 1 14-5l-2 2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M7 23v-4h4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M21 13a8 8 0 0 1-14 5l2-2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>    
                    <span id="updateCurrencyRatesText">Update Currency</span>
                </a>
		        <a href="{{ route('superadmin.currency-settings.create') }}" class="btn btn-sm btn-light btn-active-primary">
                    <span class="svg-icon svg-icon-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor"></rect>
                            <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="currentColor"></rect>
                        </svg>
                    </span>    
                    Add Currency
                </a>
		</div>
	</div>
</div>
<!--end::toolbar-->

<!--begin::Data Table-->
<div class="post d-flex flex-column-fluid mt-10" id="kt_post">
    <div id="kt_content_container" class="container-fluid px-3">
        <div class="row gy-5 g-xl-8">
            <div class="col-xl-12 mt-4">
                <div class="card card-xl-stretch mb-5 mb-xl-8">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bolder fs-3 mb-1">@lang('app.currency')</span>
                            <span class="text-muted mt-1 fw-bold fs-7">Over 50 contraries</span>
                        </h3>
                    </div>
                    <div class="card-body py-3">
                        <div class="table-responsive">
                            <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4" id="myTable">
                                <thead class="">
                                    <tr class="fw-bolder text-muted">
                                        <th class="w-25px">
                                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input class="form-check-input" type="checkbox" value="1" data-kt-check="true" data-kt-check-target=".widget-9-check">
                                            </div>
                                        </th>
                                        <th class="min-w-200px">Currency Name</th>
                                        <th class="min-w-150px">Currency Symbol</th>
                                        <th class="min-w-150px">Currency Code</th>
                                        <th class="min-w-150px">INR Based Rates</th>
                                        <th class="min-w-150px">USD Based Rates</th>
                                        <th class="min-w-100px">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="">
                                    @forelse ($currencies as $key=>$item)
                                    <tr id="row-{{ $item->id }}">
                                        <td>
                                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input class="form-check-input widget-9-check" type="checkbox" value="1">
                                            </div>
                                        </td>
                                        <td>

                                        <div class="d-flex align-items-center">
                                                <div class="symbol symbol-50px overflow-hidden me-3">
                                                    <a href="javascript:void(0);">
                                                        <div class="symbol-label fs-3 bg-light-danger text-danger">D</div>
                                                    </a>
                                                </div>
                                                <div class="d-flex justify-content-start flex-column">
                                                    <span class="text-dark fw-bolder fs-6">{{ $item->currency_name }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <a href="javascript:void(0);" class="text-dark fw-bolder d-block fs-6">{{ ucfirst($item->currency_symbol) }}</a>
                                        </td>
                                        <td class="">
                                            <a href="javascript:void(0);" class="text-dark fw-bolder d-block fs-6">{{ ucfirst($item->currency_code) }}</a>
                                        </td>
                                        
                                        <td class="">
                                            <a href="javascript:void(0);" class="text-dark fw-bolder d-block fs-6">{{ ucfirst($item->inr_based_rates) }}</a>
                                        </td>
                                        
                                        <td class="">
                                            <a href="javascript:void(0);" class="text-dark fw-bolder d-block fs-6">{{ ucfirst($item->usd_based_rates) }}</a>
                                        </td>
                                        <td>
                                            <div class="">
                                            
                                                <a href="{{ route('superadmin.currency-settings.edit', $item->id) }}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" data-toggle="tooltip" data-original-title="@lang('app.edit')">
                                                    <span class="svg-icon svg-icon-3">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                            <path opacity="0.3" d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z" fill="currentColor"></path>
                                                            <path d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z" fill="currentColor"></path>
                                                        </svg>
                                                    </span>
                                                </a>
                                                @if(!$item->is_trial)
                                                    <a href="javascript:;" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm sa-params-delete" data-toggle="tooltip" data-row-id="{{ $item->id }}" data-original-title="@lang('app.delete')">
                                                        <span class="svg-icon svg-icon-3">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                            <path d="M5 9C5 8.44772 5.44772 8 6 8H18C18.5523 8 19 8.44772 19 9V18C19 19.6569 17.6569 21 16 21H8C6.34315 21 5 19.6569 5 18V9Z" fill="currentColor"></path>
                                                            <path opacity="0.5" d="M5 5C5 4.44772 5.44772 4 6 4H18C18.5523 4 19 4.44772 19 5V5C19 5.55228 18.5523 6 18 6H6C5.44772 6 5 5.55228 5 5V5Z" fill="currentColor"></path>
                                                            <path opacity="0.5" d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V4H9V4Z" fill="currentColor"></path>
                                                        </svg>
                                                    </span>
                                                    </a>
                                                @endif
                                            </div>
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
        </div>
    </div>
</div>
<!--end::Data Table-->

<!--<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">@lang('app.currency')</h4>
                <table class="table table-bordered mt-4">
                    <thead>
                        <tr>
                            <th style="width: 20px">#</th>
                            <th>@lang('modules.currency.currencyName')</th>
                            <th>@lang('modules.currency.currencySymbol')</th>
                            <th>@lang('modules.currency.currencyCode')</th>
                            <th style="width: 150px">@lang('app.action')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($currencies as $key=>$item)
                        <tr id="row-{{ $item->id }}">
                            <td>{{ $key+1 }}.</td>
                            <td>
                                {{ $item->currency_name }}
                            </td>
                            <td>{{ ucfirst($item->currency_symbol) }}</td>
                            <td>{{ ucfirst($item->currency_code) }}</td>
                           <td>
                                <a href="{{ route('superadmin.currency-settings.edit', $item->id) }}" class="btn btn-primary btn-circle" data-toggle="tooltip" data-original-title="@lang('app.edit')">
                                    <i class="fa fa-pencil" aria-hidden="true"></i>
                                </a>
                                @if(!$item->is_trial)
                                    <a href="javascript:;" class="btn btn-danger btn-circle sa-params" data-toggle="tooltip" data-row-id="{{ $item->id }}" data-original-title="@lang('app.delete')">
                                        <i class="fa fa-times" aria-hidden="true"></i>
                                    </a>
                                @endif
                            </td>
                        </tr>
                        @empty
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>-->
@endsection

@push('footer-script')
    <script>

        $(document).on("click",".updateCurrencyRates",function(){
                 var id = 1;
                 var chkStatus = 1;
                 var url 				=	"{{ route('get.updateCurrencyRates') }}";
                 var token 				=	"{{ csrf_token() }}";
                 $('#updateCurrencyRatesText').text('Updating...');
                 $.ajax({
                    type		: 'GET',
                    url			: url,
                    beforeSend	: function(){ },
                    data		: {'_token': token, 'chkStatus':chkStatus, 'id': id },
                    success		: function (response){																																						
                                        if (response.status){  	
                                            $('#updateCurrencyRatesText').text('Update Currency');														  
                                            Swal.fire({
                                                icon: response.type,
                                                title: 'Updated!',
                                                text: response.message
                                            }).then(() => {
                                                location.reload();
                                            });
                                        } 
                    }
                });
           })


       $('body').on('click', '.sa-params-delete', function(){
            var id = $(this).data('row-id');
           Swal.fire({
                title: "@lang('errors.areYouSure')",
                text: "@lang('errors.deleteWarning')",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "@lang('app.delete')",
                cancelButtonText: "@lang('app.cancel')",
                closeOnConfirm: true,
                closeOnCancel: true
            }).then((result) => {
                if (result.isConfirmed) {
                    var url = "{{ route('superadmin.currency-settings.destroy',':id') }}";
                    url = url.replace(':id', id);
                    var token = "{{ csrf_token() }}";
                    $.ajax({
                        type: 'POST',
                        url: url,
                        data: {'_token': token, '_method': 'DELETE'},
                        success: function (response) {
                            if (response.status == "success")
                            {
                                // $.unblockUI();
                               Swal.fire({
                                    title: "Success!",
                                    text: response.message,
                                    icon: "success",
                                    confirmButtonText: "OK"
                                });
                                $('#row-'+id).remove();
                            }
                        }
                    });
                }
            });
        });
    </script>
@endpush