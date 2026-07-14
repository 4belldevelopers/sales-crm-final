@php
    use App\Helper\GeneralMethod;
@endphp
<div class="transactions-table-wrap" style="overflow-x:auto;">
    <table class="table align-middle table-row-dashed fs-6 gy-5 table-custom-class dataTable no-footer w-100"
        id="walletTransactionsTable" role="grid">
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
