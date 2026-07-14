@extends('layouts.service_company_layout')
@php
    use App\Helper\GeneralMethod;
@endphp
@push('head-script')
    <style>
        .transactions-table-wrap {
            overflow-x: auto;
        }

        @media (max-width: 1366px) {

            .transactions-table-wrap table th,
            .transactions-table-wrap table td {
                padding-left: 0.5rem !important;
                padding-right: 0.5rem !important;
                font-size: 13px;
            }
        }

        @media (max-width: 991px) {
            .transactions-table-wrap table th,
            .transactions-table-wrap table td {
                white-space: nowrap;
            }
        }
    </style>
@endpush
@section('content')
    <!--begin::Toolbar-->
    <div class="toolbar" id="kt_toolbar">
        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack px-5">
            <div data-kt-swapper="true" data-kt-swapper-mode="prepend"
                data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}"
                class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
                <div class="d-flex flex-column">
                    <h5 class="d-flex align-items-center text-dark fw-bolder my-1">
                        <span class="text-gray-800 mb-0 pt-5">Transactions</span>
                    </h5>
                    <span class="text-gray-600">Subscription</span>
                </div>
            </div>
        </div>
    </div>
    <!--end::Toolbar-->
    <!--begin::Content-->
    <div class="content d-flex flex-column flex-column-fluid pt-2 pb-0" id="kt_content">
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-fluid px-3">
                <div class="card mt-2">
                    <div class="card-header border-0 pt-6">
                        <div class="card-title">
                            <span class="text-gray-900 fs-2 fw-bolder me-2">Transaction History</span>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <div class="transactions-table-wrap">
                            <table class="table align-middle table-row-dashed fs-6 gy-5 table-custom-class dataTable no-footer w-100"
                                id="myTable" role="grid" aria-describedby="myTable_info">
                                <thead>
                                    <tr class="fw-bolder text-muted" role="row">
                                        <th class="sorting_disabled">Order ID</th>
                                        <th class="sorting_disabled">Package</th>
                                        <th class="sorting_disabled">Coins</th>
                                        <th class="sorting_disabled">Amount</th>
                                        <th class="sorting_disabled">Status</th>
                                        <th class="sorting_disabled">Download</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-600 fw-bold">
                                    @forelse ($cart_transaction_history as $val)
                                        <tr role="row" class="odd">
                                            <td>
                                                <div class="d-flex flex-column">
                                                    @php
                                                        $ordno = sprintf('%05d', $val->id);
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
                                                        class="fw-bold text-muted d-block fs-7">{{ $val->event_type }} Coins</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <a href="javascript:void(0);"
                                                        class="text-dark fw-bolder text-hover-primary fs-6"><i
                                                            class="fa fa-{{ $val->currency == 'INR' ? 'inr' : 'usd' }}"
                                                            aria-hidden="true"></i>
                                                        {{ $val->total_price }}</a>
                                                    <span
                                                        class="fw-bold text-muted d-block fs-7">{{ $val->payment_mode === 'Payment Link' || $val->payment_mode === 'Online' ? 'Online' : ucfirst($val->payment_gateway) }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="">
                                                    @php
                                                        if ($val->payment_mode != 'Payment Link' && $val->status == 'created') {
                                                            $status_color = 'danger';
                                                            $status_text = 'Failed';
                                                        } else {
                                                            $status_get = GeneralMethod::paymentstatus($val->status, 'razorpay');
                                                            $status_color = @$status_get->status_color;
                                                            $status_text = @$status_get->status_text;
                                                        }
                                                    @endphp
                                                    <span
                                                        class="fw-bolder fs-6 text-{{ @$status_color }}">{{ @$status_text }}</span>
                                                    <span
                                                        class="fw-bold text-muted d-block fs-7">{{ $val->payment_mode === 'Online' ? 'Website' : $val->payment_mode }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <a href="{{ route('service_company.subscribe.receiptdownload', [encrypt($val->cart_id)]) }}"
                                                        class="text-danger fw-bolder text-hover-primary fs-6">
                                                        Download
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-10">No transactions yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-5">
                            {{ $cart_transaction_history->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end::Content-->
@endsection
