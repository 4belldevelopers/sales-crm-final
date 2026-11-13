{{-- 
  JERO ATS — Subscription Transactions Card Stream (Rule 20 Card + Sub-Card Design System)
  Intent: Complete Modern Card + Sub-Card transaction ledger featuring:
          1) 4-column Transaction Telemetry KPI Sub-Cards (computed in-memory from current page)
          2) Dedicated White Filter Sub-Tabs Card (All, Operational, Sales, Paid, Pending, Failed)
          3) Primary Transaction Cards with 5 discrete upper Sub-Cards + 6th Bottom Activity Strip & PDF Receipt CTA
          4) Preserved AJAX pagination (.pagination a.page-link) and client-side live search scoping.
--}}
@php
    use App\Helper\GeneralMethod;

    $txTotalCount = method_exists($cart_transaction_history, 'total')
        ? $cart_transaction_history->total()
        : count($cart_transaction_history);

    $txRows = [];
    $opTxCount = 0;
    $salTxCount = 0;
    $paidTxCount = 0;
    $pendingTxCount = 0;
    $failedTxCount = 0;
    $txCoinsSum = 0;
    $txPaidInr = 0.0;
    $txPaidUsd = 0.0;

    foreach ($cart_transaction_history as $val) {
        $ordno = sprintf('%05d', $val->id);
        $currSymbol = ($val->currency == 'INR') ? '₹' : '$';
        $gatewayLabel = ($val->payment_mode === 'Payment Link' || $val->payment_mode === 'Online')
            ? 'Online'
            : ucfirst((string) ($val->payment_gateway ?? 'Gateway'));
        $channelLabel = ($val->payment_mode === 'Online') ? 'Website' : ($val->payment_mode ?? 'Online');

        if ($val->payment_mode != 'Payment Link' && $val->status == 'created') {
            $status_color = 'danger';
            $status_text = 'Failed';
        } else {
            $status_get = GeneralMethod::paymentstatus($val->status, 'razorpay');
            $status_color = @$status_get->status_color ?: 'secondary';
            $status_text = @$status_get->status_text ?: ucfirst((string) $val->status);
        }

        $statusKey = 'neutral';
        $accentClass = 'sub-tx-accent-neutral';
        $badgeClass = 'sub-tx-pill-neutral';
        if (in_array($status_color, ['success', 'primary'])) {
            $statusKey = 'success';
            $accentClass = 'sub-tx-accent-success';
            $badgeClass = 'sub-tx-pill-success';
            $paidTxCount++;
            if (is_numeric($val->credit)) {
                $txCoinsSum += (int) $val->credit;
            }
            if (is_numeric($val->total_price)) {
                if ($val->currency == 'INR') {
                    $txPaidInr += (float) $val->total_price;
                } else {
                    $txPaidUsd += (float) $val->total_price;
                }
            }
        } elseif ($status_color === 'danger') {
            $statusKey = 'danger';
            $accentClass = 'sub-tx-accent-danger';
            $badgeClass = 'sub-tx-pill-danger';
            $failedTxCount++;
        } elseif ($status_color === 'warning') {
            $statusKey = 'warning';
            $accentClass = 'sub-tx-accent-warning';
            $badgeClass = 'sub-tx-pill-warning';
            $pendingTxCount++;
        }

        $walletKey = strtolower((string) ($val->event_type ?? 'operational')) === 'sales' ? 'sales' : 'operational';
        if ($walletKey === 'sales') {
            $salTxCount++;
        } else {
            $opTxCount++;
        }

        $walletTypeLabel = ucfirst($walletKey);
        $searchScope = strtolower(trim("#{$ordno} {$val->product_name} {$walletKey} {$val->credit} {$val->total_price} {$gatewayLabel} {$channelLabel} {$status_text} {$val->updated_at}"));

        $txRows[] = (object) [
            'raw' => $val,
            'ordno' => $ordno,
            'currSymbol' => $currSymbol,
            'gatewayLabel' => $gatewayLabel,
            'channelLabel' => $channelLabel,
            'status_text' => $status_text,
            'statusKey' => $statusKey,
            'accentClass' => $accentClass,
            'badgeClass' => $badgeClass,
            'walletKey' => $walletKey,
            'walletTypeLabel' => $walletTypeLabel,
            'isSalesWallet' => $walletKey === 'sales',
            'searchScope' => $searchScope,
        ];
    }

    $pageOrderCount = count($txRows);
    $clearedDisplay = '₹' . number_format($txPaidInr, 2);
    if ($txPaidUsd > 0 && $txPaidInr > 0) {
        $clearedDisplay = '₹' . number_format($txPaidInr, 0) . ' + $' . number_format($txPaidUsd, 0);
    } elseif ($txPaidUsd > 0 && $txPaidInr <= 0) {
        $clearedDisplay = '$' . number_format($txPaidUsd, 2);
    }
@endphp

<div class="sub-stream sub-tx-container" data-tx-total="{{ $txTotalCount }}">

    {{-- ────────────────────────────────────────────────────────────
         TX CARD 1: LEDGER TELEMETRY & KPI SUB-CARDS
         ──────────────────────────────────────────────────────────── --}}
    <div class="sub-primary-card">
        <div class="sub-card-header">
            <div class="sub-card-header-left">
                <div class="sub-cat-icon sub-icon-blue">
                    <i class="fas fa-exchange-alt"></i>
                </div>
                <div>
                    <h4 class="sub-card-title">Subscription &amp; Coin Order Ledger</h4>
                    <p class="sub-card-desc">Authoritative billing history, wallet allocation breakdown, payment gateway telemetry, and GST receipts.</p>
                </div>
            </div>
            <div class="sub-card-header-right">
                <span class="sub-status-badge sub-badge-blue sub-num">
                    <i class="fas fa-receipt"></i> {{ number_format($txTotalCount) }} Total Orders
                </span>
            </div>
        </div>

        <div class="sub-card-body">
            <div class="sub-tx-kpi-grid">
                <div class="sub-kpi-subcard">
                    <div>
                        <div class="sub-kpi-val sub-num">{{ number_format($txTotalCount) }}</div>
                        <div class="sub-kpi-label">Total Ledger Orders</div>
                    </div>
                    <span class="sub-kpi-mini-icon sub-icon-blue"><i class="fas fa-list-ul"></i></span>
                </div>
                <div class="sub-kpi-subcard">
                    <div>
                        <div class="sub-kpi-val sub-num">{{ number_format($paidTxCount) }}</div>
                        <div class="sub-kpi-label">Successful (This Page)</div>
                    </div>
                    <span class="sub-kpi-mini-icon sub-icon-emerald"><i class="fas fa-check-circle"></i></span>
                </div>
                <div class="sub-kpi-subcard">
                    <div>
                        <div class="sub-kpi-val sub-num">{{ number_format($txCoinsSum) }}</div>
                        <div class="sub-kpi-label">Coins Credited (Page)</div>
                    </div>
                    <span class="sub-kpi-mini-icon sub-icon-purple"><i class="fas fa-coins"></i></span>
                </div>
                <div class="sub-kpi-subcard">
                    <div>
                        <div class="sub-kpi-val sub-num">{{ $clearedDisplay }}</div>
                        <div class="sub-kpi-label">Cleared Value (Page)</div>
                    </div>
                    <span class="sub-kpi-mini-icon sub-icon-amber"><i class="fas fa-shield-alt"></i></span>
                </div>
            </div>
        </div>
    </div>

    {{-- ────────────────────────────────────────────────────────────
         TX CARD 2: DEDICATED FILTER SUB-TABS CARD
         ──────────────────────────────────────────────────────────── --}}
    @if ($pageOrderCount > 0)
        <div class="sub-tx-filter-card" id="txFilterSubtabsBar">
            <div class="sub-tx-filter-group">
                <button type="button" class="sub-tx-filter-btn js-tx-filter-btn active" data-tx-filter="all">
                    <span>All Orders</span>
                    <span class="sub-tx-filter-badge sub-num">{{ $pageOrderCount }}</span>
                </button>
                <button type="button" class="sub-tx-filter-btn js-tx-filter-btn" data-tx-filter="operational">
                    <i class="fas fa-cogs"></i>
                    <span>Operational</span>
                    <span class="sub-tx-filter-badge sub-num">{{ $opTxCount }}</span>
                </button>
                <button type="button" class="sub-tx-filter-btn js-tx-filter-btn" data-tx-filter="sales">
                    <i class="fas fa-briefcase"></i>
                    <span>Sales</span>
                    <span class="sub-tx-filter-badge sub-num">{{ $salTxCount }}</span>
                </button>
                <button type="button" class="sub-tx-filter-btn js-tx-filter-btn" data-tx-filter="success">
                    <span>Paid</span>
                    <span class="sub-tx-filter-badge badge-paid sub-num">{{ $paidTxCount }}</span>
                </button>
                @if ($pendingTxCount > 0)
                    <button type="button" class="sub-tx-filter-btn js-tx-filter-btn" data-tx-filter="warning">
                        <span>Pending</span>
                        <span class="sub-tx-filter-badge badge-pending sub-num">{{ $pendingTxCount }}</span>
                    </button>
                @endif
                @if ($failedTxCount > 0)
                    <button type="button" class="sub-tx-filter-btn js-tx-filter-btn" data-tx-filter="danger">
                        <span>Failed</span>
                        <span class="sub-tx-filter-badge badge-failed sub-num">{{ $failedTxCount }}</span>
                    </button>
                @endif
            </div>
            <div class="sub-tx-filter-meta sub-num" id="txVisibleCountLabel">
                Showing {{ $pageOrderCount }} {{ $pageOrderCount === 1 ? 'order' : 'orders' }} on this page
            </div>
        </div>
    @endif

    {{-- ────────────────────────────────────────────────────────────
         TX CARD 3: PRIMARY TRANSACTION CARD STREAM (5 SUB-CARDS + BOTTOM STRIP)
         ──────────────────────────────────────────────────────────── --}}
    <div class="sub-primary-card">
        <div class="sub-card-body">
            <div class="sub-tx-stream" id="walletTransactionsStream">
                @forelse ($txRows as $row)
                    @php $val = $row->raw; @endphp
                    <div class="sub-tx-card {{ $row->accentClass }}"
                        data-search-text="{{ $row->searchScope }}"
                        data-tx-wallet="{{ $row->walletKey }}"
                        data-tx-status="{{ $row->statusKey }}">

                        <div class="sub-tx-grid">
                            {{-- Sub-Card 1: Order ID & Reference --}}
                            <div class="sub-tx-subcard">
                                <span class="sub-tx-micro-label">ORDER REFERENCE</span>
                                <div class="sub-tx-order-id sub-num">#{{ $row->ordno }}</div>
                                <div class="sub-tx-meta">
                                    <i class="fas fa-file-invoice"></i> Order ID &bull; Cart #{{ $val->cart_id }}
                                </div>
                            </div>

                            {{-- Sub-Card 2: Package & Wallet Scope --}}
                            <div class="sub-tx-subcard">
                                <span class="sub-tx-micro-label">PACKAGE &amp; WALLET</span>
                                <div class="sub-tx-pkg-name" title="{{ $val->product_name }}">{{ $val->product_name }}</div>
                                <div class="sub-tx-meta">
                                    <span class="sub-tx-wallet-tag {{ $row->isSalesWallet ? 'sub-tag-emerald' : 'sub-tag-purple' }}">
                                        <i class="fas {{ $row->isSalesWallet ? 'fa-briefcase' : 'fa-cogs' }}"></i>
                                        {{ $row->walletTypeLabel }} Wallet
                                    </span>
                                </div>
                            </div>

                            {{-- Sub-Card 3: Coins Credited --}}
                            <div class="sub-tx-subcard">
                                <span class="sub-tx-micro-label">COINS CREDITED</span>
                                <div class="sub-tx-coins sub-num">
                                    <i class="fas fa-coins"></i> {{ is_numeric($val->credit) ? number_format((float) $val->credit) : $val->credit }}
                                </div>
                                <div class="sub-tx-meta">{{ $row->walletTypeLabel }} Coins</div>
                            </div>

                            {{-- Sub-Card 4: Amount & Currency --}}
                            <div class="sub-tx-subcard">
                                <span class="sub-tx-micro-label">AMOUNT &amp; CURRENCY</span>
                                <div class="sub-tx-amount sub-num">{{ $row->currSymbol }}{{ is_numeric($val->total_price) ? number_format((float) $val->total_price, 2) : $val->total_price }}</div>
                                <div class="sub-tx-meta">
                                    <i class="fas fa-globe"></i> {{ strtoupper((string) ($val->currency ?? 'INR')) }} &bull; {{ $row->gatewayLabel }}
                                </div>
                            </div>

                            {{-- Sub-Card 5: Payment Status --}}
                            <div class="sub-tx-subcard">
                                <span class="sub-tx-micro-label">PAYMENT STATUS</span>
                                <div class="sub-tx-status-row">
                                    <span class="sub-tx-status-pill {{ $row->badgeClass }}">{{ $row->status_text }}</span>
                                </div>
                                <div class="sub-tx-meta">
                                    <i class="fas fa-shield-alt"></i> {{ $row->channelLabel }}
                                </div>
                            </div>
                        </div>

                        {{-- Sub-Card 6: Bottom Activity Strip --}}
                        <div class="sub-tx-bottom-strip">
                            <div class="sub-tx-bottom-meta">
                                <span><i class="far fa-clock"></i> {{ $val->updated_at }}</span>
                                <span class="sub-tx-dot">&bull;</span>
                                <span><i class="fas fa-credit-card"></i> {{ $row->gatewayLabel }} ({{ $row->channelLabel }})</span>
                            </div>
                            <a href="{{ route('service_company.subscribe.receiptdownload', [encrypt($val->cart_id)]) }}"
                                class="sub-tx-receipt-btn" title="Download GST PDF Receipt">
                                <i class="fas fa-file-download"></i> Download Receipt
                            </a>
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
                    <div class="sub-empty-desc">Try switching the filter tab or clearing your search query.</div>
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

</div>
