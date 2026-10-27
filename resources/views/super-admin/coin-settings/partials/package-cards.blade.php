{{-- 
    Coin Packages Primary Stream Partial
    Enterprise Modern Card + Sub-Card System
    Fully responsive for 15.6" screens (1366x768 & 1920x1080)
--}}

@forelse ($coinEvents as $item)
    @php
        $itemPoints = $packagePoints[$item->id] ?? [];
        $activePointsCount = 0;
        foreach ($itemPoints as $pt) {
            if ($pt->status) $activePointsCount++;
        }

        // Deterministic icon & theme mapping based on package title
        $lowerTitle = strtolower($item->title);
        $iconClass = 'fa-cube';
        $tierTheme = 'tier-custom';

        if (str_contains($lowerTitle, 'free')) {
            $iconClass = 'fa-gift';
            $tierTheme = 'tier-free';
        } elseif (str_contains($lowerTitle, 'gold')) {
            $iconClass = 'fa-crown';
            $tierTheme = 'tier-gold';
        } elseif (str_contains($lowerTitle, 'silver')) {
            $iconClass = 'fa-medal';
            $tierTheme = 'tier-silver';
        } elseif (str_contains($lowerTitle, 'platinum')) {
            $iconClass = 'fa-gem';
            $tierTheme = 'tier-platinum';
        } elseif (str_contains($lowerTitle, 'custom')) {
            $iconClass = 'fa-sliders-h';
            $tierTheme = 'tier-custom';
        }
    @endphp

    <div class="cd-coin-card {{ $tierTheme }}" id="pkg-card-{{ $item->id }}" data-id="{{ $item->id }}" data-status="{{ $item->status }}" data-event="{{ $item->event_type }}">
        <div class="cd-coin-card-grid">
            
            {{-- Sub-Card 1: Package Identity & Type (Clickable Title & Dedicated Edit Option) --}}
            <div class="cd-subcard cd-subcard-identity">
                <div class="cd-identity-header">
                    <div class="cd-pkg-icon-box">
                        <i class="fa {{ $iconClass }}"></i>
                    </div>
                    <div class="cd-identity-meta">
                        <div class="cd-pkg-title-wrap">
                            <a href="{{ route('superadmin.company.addEditcoinPackages', encrypt($item->id)) }}" 
                               class="cd-pkg-title cd-pkg-title-link" 
                               title="Click to edit {{ $item->title }} package details">
                                <span>{{ $item->title }}</span>
                                <i class="fa fa-edit cd-title-edit-icon" title="Edit"></i>
                            </a>
                            <span class="cd-pkg-code">#PKG-{{ str_pad($item->id, 3, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <div class="cd-identity-pills">
                            @if ($item->event_type === 'operational')
                                <span class="cd-pill cd-pill-operational" title="Operational ATS Plan">
                                    <i class="fa fa-cogs"></i> Operational
                                </span>
                            @else
                                <span class="cd-pill cd-pill-sales" title="Sales & Outreach Plan">
                                    <i class="fa fa-bullhorn"></i> Sales
                                </span>
                            @endif

                            @if ($item->custome_pck == 2)
                                <span class="cd-pill cd-pill-free" title="Trial or Free Package">Free Tier</span>
                            @elseif ($item->custome_pck == 1)
                                <span class="cd-pill cd-pill-custom" title="Custom Negotiated Package">Custom</span>
                            @else
                                <span class="cd-pill cd-pill-standard" title="Standard Catalog Package">Standard</span>
                            @endif

                            <a href="{{ route('superadmin.company.addEditcoinPackages', encrypt($item->id)) }}" 
                               class="cd-pill cd-pill-edit-action" 
                               title="Edit {{ $item->title }} package">
                                <i class="fa fa-pencil-alt"></i> Edit
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sub-Card 2: Coin Allocation & Unit Rates --}}
            <div class="cd-subcard cd-subcard-coins">
                <div class="cd-subcard-label">Coin Allocation</div>
                <div class="cd-coins-display">
                    <span class="cd-coin-symbol"><i class="fa fa-coins text-warning"></i></span>
                    <span class="cd-coin-amount">{{ number_format($item->coins) }}</span>
                    <span class="cd-coin-unit">Coins</span>
                </div>
                <div class="cd-unit-rates">
                    <div class="cd-rate-item cd-curr-inr">
                        <span class="cd-rate-label">Unit Rate:</span>
                        <span class="cd-rate-val">₹{{ number_format($item->total_price_rate, 2) }}</span>
                        <span class="cd-rate-sub">/ coin</span>
                    </div>
                    <div class="cd-rate-item cd-curr-usd">
                        <span class="cd-rate-label">Unit Rate:</span>
                        <span class="cd-rate-val">${{ number_format($item->total_price_usd_rate, 2) }}</span>
                        <span class="cd-rate-sub">/ coin</span>
                    </div>
                </div>
            </div>

            {{-- Sub-Card 3: Pricing & Billing Cycle --}}
            <div class="cd-subcard cd-subcard-pricing">
                <div class="cd-subcard-label">Pricing & Billing</div>
                
                {{-- INR Pricing --}}
                <div class="cd-pricing-block cd-curr-inr">
                    <div class="cd-main-price">
                        <span class="cd-currency-sign">₹</span>
                        <span class="cd-price-number">{{ number_format($item->price, 2) }}</span>
                        <span class="cd-billing-term">/ Year</span>
                        @if ($item->discount > 0)
                            <span class="cd-discount-badge">-{{ $item->discount }}%</span>
                        @endif
                    </div>
                    <div class="cd-secondary-rates">
                        <span title="Monthly equivalent breakdown">₹{{ number_format($item->total_price_monthly, 2) }}/mo</span>
                        <span class="cd-rate-dot">•</span>
                        <span title="Daily equivalent breakdown">₹{{ number_format($item->total_price_days, 2) }}/day</span>
                    </div>
                </div>

                {{-- USD Pricing --}}
                <div class="cd-pricing-block cd-curr-usd">
                    <div class="cd-main-price">
                        <span class="cd-currency-sign">$</span>
                        <span class="cd-price-number">{{ number_format($item->price_usd ?? 0, 2) }}</span>
                        <span class="cd-billing-term">/ Year</span>
                        @if ($item->discount > 0)
                            <span class="cd-discount-badge">-{{ $item->discount }}%</span>
                        @endif
                    </div>
                    <div class="cd-secondary-rates">
                        <span title="Monthly equivalent breakdown">${{ number_format($item->total_price_usd_monthly ?? 0, 2) }}/mo</span>
                        <span class="cd-rate-dot">•</span>
                        <span title="Daily equivalent breakdown">${{ number_format($item->total_price_usd_days ?? 0, 2) }}/day</span>
                    </div>
                </div>
            </div>

            {{-- Sub-Card 4: Duration & Visibility --}}
            <div class="cd-subcard cd-subcard-validity">
                <div class="cd-subcard-label">Duration & Display</div>
                <div class="cd-duration-row">
                    <i class="fa fa-calendar-alt cd-duration-icon"></i>
                    <span class="cd-duration-days"><strong>{{ $item->days }}</strong> Days</span>
                    <span class="cd-duration-approx">({{ $item->days > 0 ? round($item->days / 30, 1) . ' Mos' : 'Unlimited' }})</span>
                </div>
                <div class="cd-visibility-row">
                    @if ($item->visibility_show == 1)
                        <span class="cd-vis-pill cd-vis-yearly" title="Plan visible on yearly selection mode">
                            <i class="fa fa-eye"></i> Yearly Portal Plan
                        </span>
                    @elseif ($item->visibility_show == 2)
                        <span class="cd-vis-pill cd-vis-monthly" title="Plan visible on monthly selection mode">
                            <i class="fa fa-eye"></i> Monthly Portal Plan
                        </span>
                    @else
                        <span class="cd-vis-pill cd-vis-daily" title="Plan visible on daily selection mode">
                            <i class="fa fa-eye"></i> Daily Portal Plan
                        </span>
                    @endif
                </div>
            </div>

            {{-- Sub-Card 5: Feature Access & Points --}}
            <div class="cd-subcard cd-subcard-features">
                <div class="cd-subcard-label">Features & Access</div>
                <div class="cd-features-header">
                    <span class="cd-features-count"><i class="fa fa-shield-alt text-primary me-1"></i> {{ $activePointsCount }} of {{ count($itemPoints) }} Active</span>
                </div>
                <div class="cd-features-preview">
                    @if (!empty($itemPoints))
                        @foreach (array_slice($itemPoints, 0, 2) as $pt)
                            <div class="cd-feature-pill {{ $pt->status ? 'is-enabled' : 'is-disabled' }}">
                                <i class="fa {{ $pt->status ? 'fa-check-circle text-success' : 'fa-times-circle text-muted' }}"></i>
                                <span class="cd-feature-text text-truncate">{{ $pt->title }}</span>
                            </div>
                        @endforeach
                    @else
                        <span class="text-muted fs-8 fst-italic">Standard platform points apply</span>
                    @endif
                </div>
                @if (count($itemPoints) > 0)
                    <button type="button" class="btn btn-sm btn-link p-0 cd-btn-view-points js-btn-view-points" 
                        data-pkg-id="{{ $item->id }}" 
                        data-pkg-name="{{ $item->title }}"
                        data-points='@json($itemPoints)'>
                        View All Features ({{ count($itemPoints) }}) <i class="fa fa-chevron-right fs-8 ms-1"></i>
                    </button>
                @endif
            </div>

        </div>

        {{-- Sub-Card 6: Bottom Activity & Action Strip with Explicit Edit Option --}}
        <div class="cd-coin-card-bottom">
            <div class="cd-bottom-left">
                <span class="cd-meta-item">
                    <i class="fa fa-clock text-muted me-1"></i> Added: {{ date('d M Y', strtotime($item->created_at)) }}
                </span>
                <span class="cd-meta-dot">•</span>
                <span class="cd-meta-item">
                    Updated: {{ date('d M Y, h:i A', strtotime($item->updated_at)) }}
                </span>
            </div>
            <div class="cd-bottom-right">
                {{-- Quick Status Toggle Switch --}}
                <div class="cd-status-control" title="Toggle active status without page reload">
                    <div class="form-check form-switch form-check-custom form-check-solid mb-0">
                        <input class="form-check-input h-20px w-35px js-toggle-pkg-status" 
                            type="checkbox" 
                            id="status-switch-{{ $item->id }}" 
                            data-id="{{ $item->id }}" 
                            {{ $item->status ? 'checked' : '' }}>
                        <label class="form-check-label ms-2 cd-status-text {{ $item->status ? 'text-success' : 'text-danger' }}" for="status-switch-{{ $item->id }}">
                            {{ $item->status ? 'Active' : 'Inactive' }}
                        </label>
                    </div>
                </div>

                {{-- Prominent Action Buttons with Text + Icon --}}
                <div class="cd-action-group">
                    <a href="{{ route('superadmin.company.addEditcoinPackages', encrypt($item->id)) }}" 
                       class="btn btn-sm btn-light-primary fw-bold cd-action-btn-pill" 
                       title="Edit Package Details & Pricing">
                        <i class="fa fa-edit me-1"></i> Edit
                    </a>
                    <button type="button" 
                            class="btn btn-sm btn-light-danger fw-bold cd-action-btn-pill js-btn-delete-pkg" 
                            data-id="{{ $item->id }}" 
                            data-name="{{ $item->title }}" 
                            title="Delete Package">
                        <i class="fa fa-trash me-1"></i> Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
@empty
    <div class="cd-empty-card">
        <div class="cd-empty-icon">
            <i class="fa fa-coins"></i>
        </div>
        <h4 class="cd-empty-title">No Coin Packages Found</h4>
        <p class="cd-empty-text">No packages match your search filter or selected category tab.</p>
        <div class="cd-empty-actions">
            <button type="button" class="btn btn-sm btn-light-primary fw-bold js-btn-reset-filters">
                <i class="fa fa-undo me-1"></i> Reset Filters
            </button>
            <a href="{{ route('superadmin.company.addEditcoinPackages') }}" class="btn btn-sm btn-primary fw-bold ms-2">
                <i class="fa fa-plus me-1"></i> Create New Package
            </a>
        </div>
    </div>
@endforelse

@if ($coinEvents->hasPages())
    <div class="cd-pagination-card">
        {{ $coinEvents->links() }}
    </div>
@endif
