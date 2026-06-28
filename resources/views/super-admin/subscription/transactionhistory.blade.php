@extends('layouts.superadmin_layout')
@php 
   use App\Helper\GeneralMethod;
@endphp
@push('head-script')
    <link rel="stylesheet" href="//cdn.datatables.net/fixedheader/3.1.5/css/fixedHeader.bootstrap.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
    <!--<link rel="stylesheet" href="{{ asset('assets/node_modules/dropify/dist/css/dropify.min.css') }}">-->
    <link href="{{ asset('demo/plugins/custom/fullcalendar/fullcalendar.bundle.css') }}{{ TM_VERSION }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('demo/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('demo/plugins/global/plugins.bundle.css') }}{{ TM_VERSION }}" rel="stylesheet"
        type="text/css" />
    <style>
        body {
            overflow-x: hidden;
        }

        .company-logo {
            max-height: 50px;
            max-width: 120px;
        }

        .company-logo-div {
            border-radius: 2px;
            padding: 2px 2px 2px 2px;
            margin-bottom: 2px;
        }

        .symbol.symbol-circle,
        .symbol.symbol-circle .symbol-label,
        .symbol.symbol-circle>img {
            border-radius: 10%;
        }

        @media only screen and (min-width: 1024px) and (max-width: 1440px) {
            #kt_activitiesDefaultPackage {
                width: 1025px;
            }
        }

        @media only screen and (min-width: 1440px) and (max-width: 2560px) {
            #kt_activitiesDefaultPackage {
                width: 1200px;
            }
        }

        @media  only screen and (min-width: 1024px) and (max-width: 1440px){#kt_activitiesBenchApplication{width: 1025px;}}
        @media  only screen and (min-width: 1440px) and (max-width: 2560px){#kt_activitiesBenchApplication{width: 1200px;}}

    </style>
@endpush

@section('content')
    <!--begin::toolbar-->
    <div class="toolbar" id="kt_toolbar">
        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
            <div data-kt-swapper="true" data-kt-swapper-mode="prepend"
                data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}"
                class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
                
            </div>
           
        </div>
    </div>


    <!--begin::Content-->
    <div class="content d-flex flex-column flex-column-fluid pt-4" id="kt_content">
        <!--begin::Post-->
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <!--begin::Container-->
            <div id="kt_content_container" class="container-fluid px-3">
                <!--begin::Card-->
                <div class="card">
                    <!--begin::Card body-->
                    <div class="card-body pt-3 px-5">

                        <div id="benchListingTable_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer"><div id="benchListingTable_processing" class="dataTables_processing card" style="display: none;">Processing...</div>
                    <table
                        class="table align-middle table-row-dashed fs-6 gy-5 table-custom-class dataTable no-footer"
                        id="myTable" role="grid" aria-describedby="myTable_info"
                        style="width: 1270px;">
                        <thead>
                            <tr class="fw-bolder text-muted" role="row">
                                <th class="w-10px pe-0 sorting_disabled" rowspan="1"
                                    colspan="1" style="width: 40px;">
                                    <div
                                        class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                        <input class="form-check-input datatable_chk chk_list"
                                            type="checkbox" data-kt-check="true"
                                            data-kt-check-target="#myTable .form-check-input"
                                            value="1">
                                    </div>
                                </th>
                                <th class="sorting_disabled" rowspan="1" colspan="1"
                                    style="width: 103px;">Order ID</th>
                                <th class="sorting_disabled" rowspan="1" colspan="1"
                                    style="width: 103px;">Company/</th>
                                <th class="sorting_disabled" rowspan="1" colspan="1"
                                    style="width: 103px;">Package</th>
                                <th class="sorting_disabled" rowspan="1" colspan="1"
                                    style="width: 103px;">Coins</th>
                                <th class="sorting_disabled" rowspan="1" colspan="1"
                                    style="width: 103px;">Amount</th>
                                <th class="sorting_disabled" rowspan="1" colspan="1"
                                    style="width: 103px;">Transection</th>
                                <th class="sorting_disabled" rowspan="1" colspan="1"
                                    style="width: 103px;">Status</th>
                                <th class="sorting_disabled" rowspan="1" colspan="1"
                                    style="width: 103px;">Location</th>
                                <th class="sorting_disabled" rowspan="1" colspan="1"
                                    style="width: 103px;">GST/TAX</th>
                                <th class="sorting_disabled" rowspan="1" colspan="1"
                                    style="width: 440px;">Invoice</th>
                                <th class="sorting_disabled" rowspan="1" colspan="1"
                                    style="width: 20px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="18px" height="18px"
                                    viewBox="0 0 24 24" version="1.1"
                                    class="kt-svg-icon">
                                        <g stroke="none" stroke-width="1"
                                            fill="none"
                                            fill-rule="evenodd">
                                            <rect id="bound" x="0" y="0"
                                                width="24"
                                                height="24" />
                                            <path
                                                d="M2,13 C2,12.5 2.5,12 3,12 C3.5,12 4,12.5 4,13 C4,13.3333333 4,15 4,18 C4,19.1045695 4.8954305,20 6,20 L18,20 C19.1045695,20 20,19.1045695 20,18 L20,13 C20,12.4477153 20.4477153,12 21,12 C21.5522847,12 22,12.4477153 22,13 L22,18 C22,20.209139 20.209139,22 18,22 L6,22 C3.790861,22 2,20.209139 2,18 C2,15 2,13.3333333 2,13 Z"
                                                id="Path-57" fill="red"
                                                fill-rule="nonzero"
                                                opacity="0.3" />
                                            <rect id="Rectangle"
                                                fill="red" opacity="0.3"
                                                transform="translate(12.000000, 8.000000) rotate(-180.000000) translate(-12.000000, -8.000000) "
                                                x="11" y="1" width="2"
                                                height="14"
                                                rx="1" />
                                            <path
                                                d="M7.70710678,15.7071068 C7.31658249,16.0976311 6.68341751,16.0976311 6.29289322,15.7071068 C5.90236893,15.3165825 5.90236893,14.6834175 6.29289322,14.2928932 L11.2928932,9.29289322 C11.6689749,8.91681153 12.2736364,8.90091039 12.6689647,9.25670585 L17.6689647,13.7567059 C18.0794748,14.1261649 18.1127532,14.7584547 17.7432941,15.1689647 C17.3738351,15.5794748 16.7415453,15.6127532 16.3310353,15.2432941 L12.0362375,11.3779761 L7.70710678,15.7071068 Z"
                                                id="Path-102" fill="#000000"
                                                fill-rule="nonzero"
                                                transform="translate(12.000004, 12.499999) rotate(-180.000000) translate(-12.000004, -12.499999) " />
                                        </g>
                                    </svg>
                            </th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 fw-bold">
                            @if ($cart_transaction_history)
                                @foreach ($cart_transaction_history as $val)
                                    <tr role="row" class="odd">

                                        <td>
                                            <div
                                                class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input class="form-check-input chk_list_item "
                                                    name="chk_list_item[]" type="checkbox"
                                                    data-jobid="11" value="1015">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                @php
                                                    $number = $val->id;
                                                    $ordno = sprintf('%05d', $number);
                                                @endphp
                                                <a href="javascript:void(0);"
                                                    class="text-dark fw-bolder text-hover-primary fs-6">#{{ $ordno }}</a>
                                                <span
                                                    class="fw-bold text-muted d-block fs-7">{{ $val->updated_at }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <a href="javascript:void(0);"
                                                    class="text-dark fw-bolder text-hover-primary fs-6">{{ $val->company_name  }}</a>
                                                <span
                                                    class="fw-bold text-muted d-block fs-7">{{ $val->gstin }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <a href="javascript:void(0);"
                                                    class="text-dark fw-bolder text-hover-primary fs-6">{{ $val->product_name }}</a>
                                                <span
                                                    class="fw-bold text-muted d-block fs-7">{{ $val->event_type }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <a href="javascript:void(0);"
                                                    class="text-dark fw-bolder text-hover-primary fs-6">{{ $val->credit }}</a>
                                                <span
                                                    class="fw-bold text-muted d-block fs-7">Coins</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <a href="javascript:void(0);"
                                                    class="text-dark fw-bolder text-hover-primary fs-6"><i
                                                        class="fa fa-{{ $val->currency == 'INR' ? 'inr' : 'usd' }}"
                                                        aria-hidden="true"></i>
                                                        {{ $val->currency == 'INR' ? 'INR' : 'USD' }} {{ $val->total_price }}</a>
                                                <span
                                                    class="fw-bold text-muted d-block fs-7">{{ $val->payment_mode === 'Payment Link' || $val->payment_mode === 'Online' ? 'Online' : ucfirst($val->payment_gateway) }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            @if($val->payment_mode === 'Cash')
                                                    {{ 'Cash' }}
                                            @elseif($val->payment_mode === 'Cheque')
                                            Cheque No: {{ ($val->chequenumber) ? $val->chequenumber : '-----------' }}
                                            @elseif($val->payment_mode === 'Bank Transfer')
                                            {{ ($val->transactionid) ? $val->transactionid :'-----------' }}
                                            @elseif($val->payment_mode === 'Online')
                                            {{ ($val->payment_Id) ? $val->payment_Id : '------------'}}
                                            @elseif($val->payment_mode === 'Payment Link')
                                            {{ ($val->payment_Id) ? $val->payment_Id : '------------'}}
                                            @endif
                                        </td>  

                                        <td>
                                            <div class="">
                                                @php
                                                    if (
                                                        $val->payment_mode !=
                                                            'Payment Link' &&
                                                        $val->status == 'created'
                                                    ) {
                                                        $status_color = 'danger';
                                                        $status_text = 'Failed';
                                                    } else {
                                                        $status_get = GeneralMethod::paymentstatus(
                                                            $val->status,
                                                            'razorpay',
                                                        );
                                                        $status_color = @$status_get->status_color;
                                                        $status_text = @$status_get->status_text;
                                                    }

                                                @endphp

                                                <span
                                                    class="fw-bolder fs-6 text-{{ @$status_color }}">{{ @$status_text }}
                                                </span>
                                                <span
                                                    class="fw-bold text-muted d-block fs-7">{{ ($val->payment_mode === 'Online') ? 'Website' : $val->payment_mode }}</span>
                                            </div>
                                        </td>
                                        <td>


                                            <div class="d-flex flex-column">
                                                <a href="javascript:void(0);"
                                                    class="text-dark fw-bolder text-hover-primary fs-6">{{ $val->city }}</a>
                                                <span
                                                    class="fw-bold text-muted d-block fs-7">{{ ucfirst(substr($val->state, 0, 10)) }} | {{ strtoupper(substr($val->country_name, 0, 2)) }}</span>
                                            </div>
                                        </td>
                                        <td data="{{ encrypt($val->id) }}">
                                            <select name="gst_on" id="gst_on_get" class="form-select form-select-solid" data-control="select2" data-hide-search="true" required="">
                                                <option value="Yes" {{ ($val->invoice_approval == 'Yes') ? 'selected' : '' }}>Yes</option>
                                                <option value="No" {{ ($val->invoice_approval == 'No') ? 'selected' : '' }}>No</option>
                                            </select>
                                        </td>
                                        <td  data="{{ encrypt($val->id) }}">
                                            <input type="text" class="form-control form-control-solid invoice_no" placeholder="Invoice No." name="invoice_no" value="{{ $val->approval_invoice_no }}">
                                            <span class="fw-bold text-muted d-block fs-7">{{ ($val->approval_invoice_date) ? date("d M y h:i:s" , strtotime($val->approval_invoice_date)) : '' }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <a href="{{ route('superadmin.company.receiptdownload', [encrypt($val->cart_id)]) }}"
                                                    class="text-danger fw-bolder text-hover-primary fs-6">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        xmlns:xlink="http://www.w3.org/1999/xlink"
                                                        width="18px" height="18px"
                                                        viewBox="0 0 24 24" version="1.1"
                                                        class="kt-svg-icon">
                                                        <g stroke="none" stroke-width="1"
                                                            fill="none"
                                                            fill-rule="evenodd">
                                                            <rect id="bound" x="0" y="0"
                                                                width="24"
                                                                height="24" />
                                                            <path
                                                                d="M2,13 C2,12.5 2.5,12 3,12 C3.5,12 4,12.5 4,13 C4,13.3333333 4,15 4,18 C4,19.1045695 4.8954305,20 6,20 L18,20 C19.1045695,20 20,19.1045695 20,18 L20,13 C20,12.4477153 20.4477153,12 21,12 C21.5522847,12 22,12.4477153 22,13 L22,18 C22,20.209139 20.209139,22 18,22 L6,22 C3.790861,22 2,20.209139 2,18 C2,15 2,13.3333333 2,13 Z"
                                                                id="Path-57" fill="red"
                                                                fill-rule="nonzero"
                                                                opacity="0.3" />
                                                            <rect id="Rectangle"
                                                                fill="red" opacity="0.3"
                                                                transform="translate(12.000000, 8.000000) rotate(-180.000000) translate(-12.000000, -8.000000) "
                                                                x="11" y="1" width="2"
                                                                height="14"
                                                                rx="1" />
                                                            <path
                                                                d="M7.70710678,15.7071068 C7.31658249,16.0976311 6.68341751,16.0976311 6.29289322,15.7071068 C5.90236893,15.3165825 5.90236893,14.6834175 6.29289322,14.2928932 L11.2928932,9.29289322 C11.6689749,8.91681153 12.2736364,8.90091039 12.6689647,9.25670585 L17.6689647,13.7567059 C18.0794748,14.1261649 18.1127532,14.7584547 17.7432941,15.1689647 C17.3738351,15.5794748 16.7415453,15.6127532 16.3310353,15.2432941 L12.0362375,11.3779761 L7.70710678,15.7071068 Z"
                                                                id="Path-102" fill="#000000"
                                                                fill-rule="nonzero"
                                                                transform="translate(12.000004, 12.499999) rotate(-180.000000) translate(-12.000004, -12.499999) " />
                                                        </g>
                                                    </svg>
                                                </a>

                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>

                  {{ $cart_transaction_history->links() }}               
        </div>








                    </div>
                    <!--end::Card body-->
                </div>
                <!--end::Card-->
            </div>
            <!--end::Container-->
        </div>
        <!--end::Post-->

    </div>
    <!--end::Content-->



    <!--begin::Mass Mail - Modal-->
<div class="modal fade" id="disapprove" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
      <div class="modal-content">
        <div class="modal-header flex-stack">
          <h2>Bench Candidate Reject</h2>
          <div class="btn btn-sm btn-icon btn-active-color-primary" data-dismiss="modal">
            <span class="svg-icon svg-icon-1">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
              <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
              <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
            </svg>
            </span>
          </div>
        </div>
        <div class="card-body position-relative px-0 py-0" id="application_reject_mail_body_content"></div>
      </div>
    </div>
  </div>
  <!--end::Mass Mail - Modal-->



    <div id="kt_activitiesBenchApplication_toggle"></div>
    <div id="kt_activitiesBenchApplication" class="bg-body" data-kt-drawer="true"
        data-kt-drawer-name="activitiesBenchApplication" data-kt-drawer-activate="true" data-kt-drawer-overlay="true"
        data-kt-drawer-width="" data-kt-drawer-direction="end"
        data-kt-drawer-toggle="#kt_activitiesBenchApplication_toggle"
        data-kt-drawer-close="#kt_activitiesBenchApplication_close">
        <div class="card shadow-none rounded-0 w-100" id="bench_application_show_right_bar"></div>
    </div>


@endsection

@push('footer-script')
    <script src="//cdn.datatables.net/fixedheader/3.1.5/js/dataTables.fixedHeader.min.js"></script>
    <script src="//cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
    <script src="//cdn.datatables.net/responsive/2.2.3/js/responsive.bootstrap.min.js"></script>
    <script src="//cdn.datatables.net/colreorder/1.5.2/js/dataTables.colReorder.min.js"></script>
    <script src="//cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
    <script src="//cdn.datatables.net/buttons/1.6.1/js/buttons.colVis.min.js"></script>


    <script>

        $(document).on('change','input[name="invoice_no"]',function(e){
            e.preventDefault();
                let tdElement = $(this).closest('td');
                let dataValue = tdElement.attr('data');
                let invoice_no = $(this).val();
                let gst_on = '';
                var url   		= "{{ route('superadmin.company.invoiceupdate') }}";
                Swal.fire({
                    title: 'Are you sure?',
                    text: "Do you want to submit the form?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, submit it!'
                }).then((result) => {
                    if (result.isConfirmed) {
		               
                                $.ajax({
                                        type  : 'GET',
                                        url   : url,
                                        data  : {'dataValue':dataValue,'invoice_no':invoice_no,'gst_on':gst_on},
                                        success : function (response) {
                                            
                                            Swal.fire('Success', response.message, 'success');
                                        }
                                });
                    }
                });
        });

        $(document).on('change','select[name="gst_on"]',function(e){
            e.preventDefault();
                let tdElement = $(this).closest('td');
                let dataValue = tdElement.attr('data');
                let invoice_no = $(this).val();
                let gst_on = 1;
                var url   		= "{{ route('superadmin.company.invoiceupdate') }}";
                Swal.fire({
                    title: 'Are you sure?',
                    text: "Do you want to submit the form?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, submit it!'
                }).then((result) => {
                    if (result.isConfirmed) {
		               
                                $.ajax({
                                        type  : 'GET',
                                        url   : url,
                                        data  : {'dataValue':dataValue,'invoice_no':invoice_no,'gst_on':gst_on},
                                        success : function (response) {
                                            
                                            Swal.fire('Success', response.message, 'success');   
                                        }
                                });
                    }
                });
        })

  
    </script>
@endpush
