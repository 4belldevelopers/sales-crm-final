{{-- 
  JERO ATS — Subscription Transactions Card Stream (Rule 20 Card + Sub-Card Design System)
  Intent: Replaces flat HTML table with discrete Card + Sub-Card transaction rows, semantic left-border accents,
          client-side search scoping, and preserved AJAX pagination (.pagination a.page-link).
--}}
@php
    use App\Helper\GeneralMethod;
    $txTotalCount = method_exists($cart_transaction_history, 'total') ? $cart_transaction_history->total() : count($cart_transaction_history);
@endphp

<div class="sub-primary-card sub-tx-container" data-tx-total="{{ $txTotalCount }}">
    <div class="sub-card-header">
        <div class="sub-card-header-left">
            <div class="sub-cat-icon sub-icon-blue">
                <i class="fas fa-exchange-alt"></i>
            </div>
            <div>
                <h4 class="sub-card-title">Subscription &amp; Coin Order Ledger</h4>
                <p class="sub-card-desc">Authoritative billing history, payment gateway telemetry, and GST receipt downloads.</p>
            </div>
        </div>
        <span class="sub-status-badge sub-badge-blue sub-num">
            <i class="fas fa-receipt"></i> {{ number_format($txTotalCount) }} Total Orders
        </span>
    </div>

    <div class="sub-card-body">
        <div class="sub-tx-stream" id="walletTransactionsStream">
            @forelse ($cart_transaction_history as $val)
                @php
                    $ordno = sprintf('%05d', $val->id);
                    $currSymbol = ($val->currency == 'INR') ? '₹' : '$';
                    $gatewayLabel = ($val->payment_mode === 'Payment Link' || $val->payment_mode === 'Online')
                        ? 'Online'
                        : ucfirst($val->payment_gateway ?? 'Gateway');
                    $channelLabel = ($val->payment_mode === 'Online') ? 'Website' : ($val->payment_mode ?? 'Online');

                    if ($val->payment_mode != 'Payment Link' && $val->status == 'created') {
                        $status_color = 'danger';
                        $status_text = 'Failed';
                    } else {
                        $status_get = GeneralMethod::paymentstatus($val->status, 'razorpay');
                        $status_color = @$status_get->status_color ?: 'secondary';
                        $status_text = @$status_get->status_text ?: ucfirst((string) $val->status);
                    }

                    $accentClass = 'sub-tx-accent-neutral';
                    $badgeClass = 'sub-tx-pill-neutral';
                    if (in_array($status_color, ['success', 'primary'])) {
                        $accentClass = 'sub-tx-accent-success';
                        $badgeClass = 'sub-tx-pill-success';
                    } elseif ($status_color === 'danger') {
                        $accentClass = 'sub-tx-accent-danger';
                        $badgeClass = 'sub-tx-pill-danger';
                    } elseif ($status_color === 'warning') {
                        $accentClass = 'sub-tx-accent-warning';
                        $badgeClass = 'sub-tx-pill-warning';
                    }

                    $walletTypeLabel = ucfirst((string) ($val->event_type ?? 'operational'));
                    $isSalesWallet = strtolower((string) ($val->event_type ?? '')) === 'sales';
                    $searchScope = strtolower(trim("#{$ordno} {$val->product_name} {$val->event_type} {$val->credit} {$val->total_price} {$gatewayLabel} {$channelLabel} {$status_text}"));
                @endphp

                <div class="sub-tx-card {{ $accentClass }}" data-search-text="{{ $searchScope }}">
                    <div class="sub-tx-grid">
                        {{-- Sub-Card 1: Order ID & Timestamp --}}
                        <div class="sub-tx-subcard">
                            <span class="sub-tx-micro-label">ORDER &amp; DATE</span>
                            <div class="sub-tx-order-id sub-num">#{{ $ordno }}</div>
                            <div class="sub-tx-meta">
                                <i class="far fa-clock"></i> {{ $val->updated_at }}
                            </div>
                        </div>

                        {{-- Sub-Card 2: Package & Wallet Scope --}}
                        <div class="sub-tx-subcard">
                            <span class="sub-tx-micro-label">PACKAGE &amp; WALLET</span>
                            <div class="sub-tx-pkg-name" title="{{ $val->product_name }}">{{ $val->product_name }}</div>
                            <div class="sub-tx-meta">
                                <span class="sub-tx-wallet-tag {{ $isSalesWallet ? 'sub-tag-emerald' : 'sub-tag-purple' }}">
                                    <i class="fas {{ $isSalesWallet ? 'fa-briefcase' : 'fa-cogs' }}"></i>
                                    {{ $walletTypeLabel }} Wallet
                                </span>
                            </div>
                        </div>

                        {{-- Sub-Card 3: Coins Credited --}}
                        <div class="sub-tx-subcard">
                            <span class="sub-tx-micro-label">COINS CREDITED</span>
                            <div class="sub-tx-coins sub-num">
                                <i class="fas fa-coins"></i> {{ is_numeric($val->credit) ? number_format((float) $val->credit) : $val->credit }}
                            </div>
                            <div class="sub-tx-meta">{{ $walletTypeLabel }} Coins</div>
                        </div>

                        {{-- Sub-Card 4: Amount & Payment Gateway --}}
                        <div class="sub-tx-subcard">
                            <span class="sub-tx-micro-label">AMOUNT &amp; GATEWAY</span>
                            <div class="sub-tx-amount sub-num">{{ $currSymbol }}{{ is_numeric($val->total_price) ? number_format((float) $val->total_price, 2) : $val->total_price }}</div>
                            <div class="sub-tx-meta">
                                <i class="fas fa-shield-alt"></i> {{ $gatewayLabel }} &bull; {{ $channelLabel }}
                            </div>
                        </div>

                        {{-- Sub-Card 5: Payment Status & Receipt Action --}}
                        <div class="sub-tx-subcard sub-tx-subcard-action">
                            <span class="sub-tx-micro-label">STATUS &amp; RECEIPT</span>
                            <div class="sub-tx-status-row">
                                <span class="sub-tx-status-pill {{ $badgeClass }}">{{ $status_text }}</span>
                                <a href="{{ route('service_company.subscribe.receiptdownload', [encrypt($val->cart_id)]) }}"
                                    class="sub-tx-receipt-btn" title="Download PDF Receipt">
                                    <i class="fas fa-file-download"></i> Receipt
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="sub-empty-subcard">
                    <div class="sub-empty-icon"><i class="fas fa-receipt"></i></div>
                    <div class="sub-empty-title">No transactions yet</div>
                    <div class="sub-empty-desc">Completed coin purchases and subscription invoices for your organization will appear here.</div>
                </div>
            @endforelse

            <div class="sub-empty-subcard" id="txSearchEmptyState" style="display: none;">
                <div class="sub-empty-icon"><i class="fas fa-search"></i></div>
                <div class="sub-empty-title">No matching transactions</div>
                <div class="sub-empty-desc">Try clearing the search query or searching by order number, package name, or status.</div>
            </div>
        </div>

        @if ($txTotalCount > 0)
            <div class="sub-tx-footer">
                <div class="sub-tx-showing sub-num">
                    Showing
                    <strong>{{ $cart_transaction_history->firstItem() }}</strong>
                    to
                    <strong>{{ $cart_transaction_history->lastItem() }}</strong>
                    of
                    <strong>{{ $cart_transaction_history->total() }}</strong>
                    results
                </div>
                @if ($cart_transaction_history->hasPages())
                    <ul class="pagination pagination-sm mb-0">
                        @if ($cart_transaction_history->onFirstPage())
                            <li class="page-item disabled"><span class="page-link">&laquo;</span></li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $cart_transaction_history->previousPageUrl() }}">&laquo;</a>
                            </li>
                        @endif

                        @foreach ($cart_transaction_history->getUrlRange(1, $cart_transaction_history->lastPage()) as $page => $url)
                            @if ($page == $cart_transaction_history->currentPage())
                                <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                            @else
                                <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                            @endif
                        @endforeach

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
        @endif
    </div>
</div>
