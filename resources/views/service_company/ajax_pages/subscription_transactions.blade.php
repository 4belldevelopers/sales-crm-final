@php
    use App\Helper\GeneralMethod;
@endphp
<div class="transactions-table-wrap" style="overflow-x:auto;">
    <table class="table align-middle table-row-dashed fs-6 gy-5 table-custom-class w-100"
        id="walletTransactionsTable">
        <thead>
            <tr class="fw-bolder text-muted">
                <th>Order ID</th>
                <th>Package</th>
                <th>Coins</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Download</th>
            </tr>
        </thead>
        <tbody class="text-gray-600 fw-bold">
            @forelse ($cart_transaction_history as $val)
                <tr>
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
<div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mt-5 px-1">
    <div class="text-muted fs-7">
        Showing
        <span class="fw-bold text-dark">{{ $cart_transaction_history->firstItem() }}</span>
        to
        <span class="fw-bold text-dark">{{ $cart_transaction_history->lastItem() }}</span>
        of
        <span class="fw-bold text-dark">{{ $cart_transaction_history->total() }}</span>
        results
    </div>
    @if ($cart_transaction_history->hasPages())
        <ul class="pagination pagination-sm mb-0">
            {{-- Previous --}}
            @if ($cart_transaction_history->onFirstPage())
                <li class="page-item disabled"><span class="page-link">&laquo;</span></li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $cart_transaction_history->previousPageUrl() }}">&laquo;</a>
                </li>
            @endif

            {{-- Page numbers --}}
            @foreach ($cart_transaction_history->getUrlRange(1, $cart_transaction_history->lastPage()) as $page => $url)
                @if ($page == $cart_transaction_history->currentPage())
                    <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                @else
                    <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                @endif
            @endforeach

            {{-- Next --}}
            @if ($cart_transaction_history->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $cart_transaction_history->nextPageUrl() }}">&raquo;</a>
                </li>
            @else
                <li class="page-item disabled"><span class="page-link">&raquo;</span></li>
            @endif
        </ul>
    @endif
</div>

<style>
    .transactions-table-wrap table thead th {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .5px;
        color: #a1a5b7;
        border-bottom: 1px solid #eff2f5;
        padding: 10px 12px;
        white-space: nowrap;
    }
    .transactions-table-wrap table tbody td {
        padding: 12px 12px;
        vertical-align: middle;
        border-bottom: 1px dashed #eff2f5;
    }
    .transactions-table-wrap table tbody tr:last-child td {
        border-bottom: none;
    }
    .transactions-table-wrap .pagination .page-link {
        font-size: 12px;
        font-weight: 600;
        border-radius: 6px !important;
        margin: 0 2px;
        border: 1px solid #e4e6ef;
        color: #5e6278;
        padding: 5px 10px;
    }
    .transactions-table-wrap .pagination .page-item.active .page-link {
        background: #6d28d9;
        border-color: #6d28d9;
        color: #fff;
    }
    .transactions-table-wrap .pagination .page-item.disabled .page-link {
        color: #b5b5c3;
        background: #f5f8fa;
    }
    .transactions-table-wrap .pagination .page-link:hover {
        background: #f5f3ff;
        color: #6d28d9;
        border-color: #ddd6fe;
    }
</style>
