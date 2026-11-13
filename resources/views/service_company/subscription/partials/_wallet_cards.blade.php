{{-- 
  JERO ATS — Service Company Subscription & Wallet Center: Dual Wallet Selector Partial (Rule 20 Card + Sub-Card)
  Intent: Renders Operational and Sales coin wallet cards as discrete interactive sub-cards inside Tier 3 Primary Card 1.
  Preserves .wallet-card, .package-switch, .opra-tab-plan, .salse-tab-plan, and data-kt-plan contracts.
--}}
<div class="sub-wallets-grid">
    {{-- Operational Wallet Sub-Card --}}
    <div class="wallet-card wallet-card-operational package-switch opra-tab-plan wallet-card-active sub-wallet-subcard"
        data-kt-plan="operational" role="button" tabindex="0" aria-label="Select Operational Wallet">
        <div class="sub-wallet-top">
            <div class="sub-wallet-identity">
                <span class="wallet-card-icon sub-icon-purple"><i class="fas fa-cogs"></i></span>
                <div class="sub-wallet-title-group">
                    <div class="sub-wallet-title-row">
                        <span class="sub-wallet-name">Operational Wallet</span>
                        @if(!empty($current_coins_operational))
                            <span class="sub-status-pill sub-pill-active"><i class="fas fa-circle"></i> ACTIVE</span>
                        @else
                            <span class="sub-status-pill sub-pill-inactive"><i class="fas fa-circle"></i> NO ACTIVE PLAN</span>
                        @endif
                    </div>
                    <div class="sub-wallet-plan-meta">
                        @if(!empty($current_coins_operational))
                            <i class="fas fa-tag"></i>
                            <span class="fw-semibold">{{ $current_coins_operational[0]['title'] ?? '—' }}</span>
                            @if(!empty($current_coins_operational[0]['end_date']))
                                <span class="sub-wallet-expiry">&bull; Expires {{ date('d M Y', strtotime($current_coins_operational[0]['end_date'])) }}</span>
                            @endif
                        @else
                            <span>Recruitment search, resume unlocks &amp; AI matching</span>
                        @endif
                    </div>
                </div>
            </div>
            <span class="wallet-card-check" title="Active Wallet"><i class="fas fa-check-circle"></i></span>
        </div>

        <div class="sub-wallet-balance-row">
            <div>
                <div class="wallet-card-balance sub-num">{{ number_format($remain_coins ?? 0) }}</div>
                <div class="wallet-card-balance-label">Coins Available</div>
            </div>
            <div class="wallet-ring" style="background: conic-gradient(#6d28d9 {{ ($op_used_pct ?? 0) * 3.6 }}deg, #e2e8f0 0deg);">
                <span class="wallet-ring-label">
                    <span class="wallet-ring-pct sub-num">{{ $op_used_pct ?? 0 }}%</span>
                    <span class="wallet-ring-sub">Used</span>
                </span>
            </div>
        </div>

        <div class="sub-wallet-metrics-grid wallet-card-stats">
            <div class="sub-wallet-metric-box">
                <div class="sub-wallet-metric-val sub-num">{{ number_format($op_coins_used ?? 0) }}</div>
                <div class="wallet-card-stat-label">Used Coins</div>
            </div>
            <div class="sub-wallet-metric-box">
                <div class="sub-wallet-metric-val sub-num">{{ number_format($op_total_purchased ?? 0) }}</div>
                <div class="wallet-card-stat-label">Purchased</div>
            </div>
            <div class="sub-wallet-metric-box">
                <div class="sub-wallet-metric-val sub-num">{{ $op_days_left ?? 0 }}</div>
                <div class="wallet-card-stat-label">Days Validity</div>
            </div>
        </div>
    </div>

    {{-- Sales Wallet Sub-Card --}}
    <div class="wallet-card wallet-card-sales package-switch sub-wallet-subcard"
        data-kt-plan="sales" role="button" tabindex="0" aria-label="Select Sales Wallet">
        <div class="sub-wallet-top">
            <div class="sub-wallet-identity">
                <span class="wallet-card-icon sub-icon-emerald"><i class="fas fa-briefcase"></i></span>
                <div class="sub-wallet-title-group">
                    <div class="sub-wallet-title-row">
                        <span class="sub-wallet-name">Sales Wallet</span>
                        @if(!empty($current_coins_sales))
                            <span class="sub-status-pill sub-pill-active"><i class="fas fa-circle"></i> ACTIVE</span>
                        @else
                            <span class="sub-status-pill sub-pill-inactive"><i class="fas fa-circle"></i> NO ACTIVE PLAN</span>
                        @endif
                    </div>
                    <div class="sub-wallet-plan-meta">
                        @if(!empty($current_coins_sales))
                            <i class="fas fa-tag"></i>
                            <span class="fw-semibold">{{ $current_coins_sales[0]['title'] ?? '—' }}</span>
                            @if(!empty($current_coins_sales[0]['end_date']))
                                <span class="sub-wallet-expiry">&bull; Expires {{ date('d M Y', strtotime($current_coins_sales[0]['end_date'])) }}</span>
                            @endif
                        @else
                            <span>Marketplace leads, client pitches &amp; agency visibility</span>
                        @endif
                    </div>
                </div>
            </div>
            <span class="wallet-card-check" title="Active Wallet"><i class="fas fa-check-circle"></i></span>
        </div>

        <div class="sub-wallet-balance-row">
            <div>
                <div class="wallet-card-balance sub-num">{{ number_format($remain_coins_sales ?? 0) }}</div>
                <div class="wallet-card-balance-label">Coins Available</div>
            </div>
            <div class="wallet-ring" style="background: conic-gradient(#059669 {{ ($sal_used_pct ?? 0) * 3.6 }}deg, #e2e8f0 0deg);">
                <span class="wallet-ring-label">
                    <span class="wallet-ring-pct sub-num">{{ $sal_used_pct ?? 0 }}%</span>
                    <span class="wallet-ring-sub">Used</span>
                </span>
            </div>
        </div>

        <div class="sub-wallet-metrics-grid wallet-card-stats">
            <div class="sub-wallet-metric-box">
                <div class="sub-wallet-metric-val sub-num">{{ number_format($sal_coins_used ?? 0) }}</div>
                <div class="wallet-card-stat-label">Used Coins</div>
            </div>
            <div class="sub-wallet-metric-box">
                <div class="sub-wallet-metric-val sub-num">{{ number_format($sal_total_purchased ?? 0) }}</div>
                <div class="wallet-card-stat-label">Purchased</div>
            </div>
            <div class="sub-wallet-metric-box">
                <div class="sub-wallet-metric-val sub-num">{{ $sal_days_left ?? 0 }}</div>
                <div class="wallet-card-stat-label">Days Validity</div>
            </div>
        </div>
    </div>
</div>
