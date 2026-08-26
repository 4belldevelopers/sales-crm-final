{{-- Subscription & Wallet Center: Wallet Cards Header Partial --}}
<div class="row g-4 mb-5">
    <div class="col-md-6">
        <div class="wallet-card wallet-card-operational package-switch opra-tab-plan wallet-card-active"
            data-kt-plan="operational">
            <div class="d-flex align-items-start justify-content-between">
                <div class="d-flex align-items-center">
                    <span class="wallet-card-icon"><i class="fa fa-cog"></i></span>
                    <div class="ms-3">
                        <div class="fw-bold fs-6 text-gray-900">Operational Wallet</div>
                        @if(!empty($current_coins_operational))
                            <span class="badge badge-light-success fs-9">ACTIVE</span>
                        @else
                            <span class="badge badge-light-danger fs-9">NO ACTIVE PLAN</span>
                        @endif
                    </div>
                </div>
                <span class="wallet-card-check"><i class="fa fa-check-circle"></i></span>
            </div>
            @if(!empty($current_coins_operational))
            <div class="wallet-active-pkg-row mt-2">
                <i class="fa fa-tag me-1"></i>
                <span class="fw-bold">{{ $current_coins_operational[0]['title'] ?? '—' }}</span>
                @if(!empty($current_coins_operational[0]['end_date']))
                    <span class="text-muted ms-2 fs-9">· Expires {{ date('d M Y', strtotime($current_coins_operational[0]['end_date'])) }}</span>
                @endif
            </div>
            @endif
            <div class="d-flex align-items-end justify-content-between mt-3">
                <div>
                    <div class="wallet-card-balance">{{ number_format($remain_coins ?? 0) }}</div>
                    <div class="wallet-card-balance-label">Coins Available</div>
                </div>
                <div class="wallet-ring" style="background: conic-gradient(#6d28d9 {{ ($op_used_pct ?? 0) * 3.6 }}deg, #e4e6ef 0deg);">
                    <span class="wallet-ring-label"><span class="wallet-ring-pct">{{ $op_used_pct ?? 0 }}%</span><span class="wallet-ring-sub">Used</span></span>
                </div>
            </div>
            <div class="row text-center wallet-card-stats">
                <div class="col-4">
                    <div class="fw-bold fs-6">{{ number_format($op_coins_used ?? 0) }}</div>
                    <div class="wallet-card-stat-label">Used Coins</div>
                </div>
                <div class="col-4">
                    <div class="fw-bold fs-6">{{ number_format($op_total_purchased ?? 0) }}</div>
                    <div class="wallet-card-stat-label">Purchased</div>
                </div>
                <div class="col-4">
                    <div class="fw-bold fs-6">{{ $op_days_left ?? 0 }}</div>
                    <div class="wallet-card-stat-label">Days Validity</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="wallet-card wallet-card-sales package-switch"
            data-kt-plan="sales">
            <div class="d-flex align-items-start justify-content-between">
                <div class="d-flex align-items-center">
                    <span class="wallet-card-icon"><i class="fa fa-briefcase"></i></span>
                    <div class="ms-3">
                        <div class="fw-bold fs-6 text-gray-900">Sales Wallet</div>
                        @if(!empty($current_coins_sales))
                            <span class="badge badge-light-success fs-9">ACTIVE</span>
                        @else
                            <span class="badge badge-light-danger fs-9">NO ACTIVE PLAN</span>
                        @endif
                    </div>
                </div>
                <span class="wallet-card-check"><i class="fa fa-check-circle"></i></span>
            </div>
            @if(!empty($current_coins_sales))
            <div class="wallet-active-pkg-row mt-2">
                <i class="fa fa-tag me-1"></i>
                <span class="fw-bold">{{ $current_coins_sales[0]['title'] ?? '—' }}</span>
                @if(!empty($current_coins_sales[0]['end_date']))
                    <span class="text-muted ms-2 fs-9">· Expires {{ date('d M Y', strtotime($current_coins_sales[0]['end_date'])) }}</span>
                @endif
            </div>
            @endif
            <div class="d-flex align-items-end justify-content-between mt-3">
                <div>
                    <div class="wallet-card-balance">{{ number_format($remain_coins_sales ?? 0) }}</div>
                    <div class="wallet-card-balance-label">Coins Available</div>
                </div>
                <div class="wallet-ring" style="background: conic-gradient(#10a34a {{ ($sal_used_pct ?? 0) * 3.6 }}deg, #e4e6ef 0deg);">
                    <span class="wallet-ring-label"><span class="wallet-ring-pct">{{ $sal_used_pct ?? 0 }}%</span><span class="wallet-ring-sub">Used</span></span>
                </div>
            </div>
            <div class="row text-center wallet-card-stats">
                <div class="col-4">
                    <div class="fw-bold fs-6">{{ number_format($sal_coins_used ?? 0) }}</div>
                    <div class="wallet-card-stat-label">Used Coins</div>
                </div>
                <div class="col-4">
                    <div class="fw-bold fs-6">{{ number_format($sal_total_purchased ?? 0) }}</div>
                    <div class="wallet-card-stat-label">Purchased</div>
                </div>
                <div class="col-4">
                    <div class="fw-bold fs-6">{{ $sal_days_left ?? 0 }}</div>
                    <div class="wallet-card-stat-label">Days Validity</div>
                </div>
            </div>
        </div>
    </div>
</div>
