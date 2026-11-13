@php
    // Hero KPI Tab Cards for the client-job AppTracker header (Pool / Similar / In-house Bench).
    // Same Client Tracker .cl-stat-grid visual language as sections/service_company/apptrackerHeader.blade.php.
    $apthCards = [
        [
            'tab_key' => 'pool',
            'title' => 'Total Candidates In Job',
            'sub' => 'Active job pipeline',
            'sub_icon' => 'M22 12h-4l-3 9L9 3l-3 9H2',
            'url' => $tabactive['pool_url'],
            'id' => $tabactive['pool_id'],
            'value' => $tabactive['pool_counter'],
            'active' => $tabactive['pool_active'],
            'color' => '#2563eb',
            'bg' => '#eff6ff',
            'tint' => '#eff6ff',
            'border' => '#60a5fa',
            'deep' => '#1e3a8a',
            'lbl_active' => '#1d4ed8',
            'glow' => 'rgba(37, 99, 235, 0.12)',
            'icon' => 'M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2 M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0-8 0 M22 21v-2a4 4 0 0 0-3-3.87 M16 3.13a4 4 0 0 1 0 7.75',
        ],
        [
            'tab_key' => 'similar',
            'title' => 'Similar Candidates',
            'sub' => 'Matched to job profile',
            'sub_icon' => 'm18 15-6-6-6 6',
            'url' => $tabactive['similar_url'],
            'id' => $tabactive['similar_id'],
            'value' => $tabactive['existing_counter'],
            'active' => $tabactive['similar_active'],
            'color' => '#16a34a',
            'bg' => '#ecfdf5',
            'tint' => '#ecfdf5',
            'border' => '#4ade80',
            'deep' => '#14532d',
            'lbl_active' => '#15803d',
            'glow' => 'rgba(22, 163, 74, 0.12)',
            'icon' => 'M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z',
            // Same clarification as apptrackerHeader.blade.php's Similar
            // Candidates card — this is the job's default count, not your
            // live filter selection on the Similar Candidates page itself.
            'hint' => 'Based on this job\'s default skills, location & experience — not any filters you\'ve customized on the Similar Candidates page itself.',
        ],
        [
            'tab_key' => 'inhouse_bench',
            'title' => 'In-house Bench Talents',
            'sub' => 'Internal bench pool',
            'sub_icon' => 'M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2 M8.5 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0-8 0 M17 11l2 2 4-4',
            'url' => $tabactive['inhouseBench_url'],
            'id' => $tabactive['inhouseBench_id'],
            'value' => $tabactive['internal_bench_counter'],
            'active' => $tabactive['inhouseBench_active'],
            'color' => '#7c3aed',
            'bg' => '#f5f3ff',
            'tint' => '#faf5ff',
            'border' => '#c084fc',
            'deep' => '#5b21b6',
            'lbl_active' => '#6d28d9',
            'glow' => 'rgba(124, 58, 237, 0.12)',
            'icon' => 'M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z M9 22V12h6v10',
        ],
    ];
@endphp

<style>
    /* ClientApp Header Tabs — Client Tracker .cl-stat-grid Enterprise Standard */
    .apth-grid {
        display: grid !important;
        grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
        gap: 8px !important;
        width: 100% !important;
        box-sizing: border-box !important;
        font-family: 'Poppins', sans-serif;
    }
    .apth-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 11px;
        padding: 10px 11px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        text-decoration: none !important;
        cursor: pointer;
        min-width: 0;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
        transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease, background-color 0.15s ease;
    }
    .apth-card:hover {
        transform: translateY(-1px);
        border-color: #cbd5e1;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
        text-decoration: none !important;
    }
    .apth-card.active {
        background: var(--apth-tint) !important;
        border-color: var(--apth-border) !important;
        box-shadow: 0 0 0 1.5px var(--apth-color), 0 4px 14px var(--apth-glow) !important;
        text-decoration: none !important;
    }
    .apth-card-top {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 6px;
        min-width: 0;
    }
    .apth-icon {
        width: 34px;
        height: 34px;
        min-width: 34px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        background: var(--apth-bg);
        color: var(--apth-color);
        transition: all 0.15s ease;
    }
    .apth-icon svg {
        width: 17px;
        height: 17px;
    }
    .apth-card.active .apth-icon {
        background: #ffffff !important;
        color: var(--apth-color) !important;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.14) !important;
    }
    .apth-meta {
        min-width: 0;
        flex: 1;
        overflow: hidden;
    }
    .apth-value-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 4px;
    }
    .apth-value {
        font-size: 19px;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.1;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        font-variant-numeric: tabular-nums;
        transition: color 0.15s ease;
    }
    .apth-card.active .apth-value {
        color: var(--apth-deep) !important;
    }
    .apth-active-dot {
        display: none;
        align-items: center;
        gap: 4px;
        font-size: 9.5px;
        font-weight: 700;
        color: var(--apth-color);
        background: #ffffff;
        border: 1px solid var(--apth-border);
        border-radius: 999px;
        padding: 1px 6px;
        line-height: 1.3;
        flex-shrink: 0;
    }
    .apth-card.active .apth-active-dot {
        display: inline-flex;
    }
    .apth-title {
        font-size: 11px;
        font-weight: 600;
        color: #64748b;
        margin: 2px 0 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        line-height: 1.2;
        display: flex;
        align-items: center;
        gap: 3px;
        transition: color 0.15s ease, font-weight 0.15s ease;
    }
    .apth-title-txt {
        min-width: 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .apth-card.active .apth-title {
        color: var(--apth-lbl-active) !important;
        font-weight: 700 !important;
    }
    .apth-hint {
        cursor: help;
        color: #94a3b8;
        font-size: 10px;
        flex-shrink: 0;
        line-height: 1;
    }
    .apth-sub {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 4px;
        font-size: 10px;
        font-weight: 500;
        line-height: 1.2;
        margin-top: 2px;
        color: var(--apth-color);
        min-width: 0;
    }
    .apth-sub-left {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        min-width: 0;
        overflow: hidden;
    }
    .apth-sub-left span {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .apth-sub-left svg {
        flex-shrink: 0;
        width: 11px;
        height: 11px;
    }
    .apth-arrow {
        flex-shrink: 0;
        width: 11px;
        height: 11px;
        opacity: 0.6;
        transition: transform 0.15s ease, opacity 0.15s ease;
    }
    .apth-card:hover .apth-arrow {
        transform: translateX(2px);
        opacity: 1;
    }
    .apth-card.active .apth-arrow {
        opacity: 0.95;
    }
    @media (max-width: 1440px) {
        .apth-grid {
            gap: 6px !important;
        }
        .apth-card {
            padding: 8px 10px !important;
        }
        .apth-icon {
            width: 30px !important;
            height: 30px !important;
            min-width: 30px !important;
            border-radius: 7px !important;
        }
        .apth-icon svg {
            width: 15px !important;
            height: 15px !important;
        }
        .apth-value {
            font-size: 17px !important;
        }
        .apth-title {
            font-size: 10px !important;
        }
        .apth-sub {
            font-size: 9.5px !important;
        }
    }
    @media (max-width: 768px) {
        .apth-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        }
    }
    @media (max-width: 480px) {
        .apth-grid {
            grid-template-columns: minmax(0, 1fr) !important;
        }
    }
</style>

<div class="apth-grid" role="navigation" aria-label="Client AppTracker sections">
    @foreach ($apthCards as $card)
        <a href="{{ $card['url'] }}"
            class="apth-card {{ $card['active'] ? 'active' : '' }}"
            data-tab="{{ $card['tab_key'] }}"
            title="{{ $card['title'] }}"
            style="--apth-color: {{ $card['color'] }}; --apth-bg: {{ $card['bg'] }}; --apth-tint: {{ $card['tint'] }}; --apth-border: {{ $card['border'] }}; --apth-deep: {{ $card['deep'] }}; --apth-lbl-active: {{ $card['lbl_active'] }}; --apth-glow: {{ $card['glow'] }};">
            <div class="apth-card-top">
                <div class="apth-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="{{ $card['icon'] }}" />
                    </svg>
                </div>
                <div class="apth-meta">
                    <div class="apth-value-row">
                        <div class="apth-value" id="{{ $card['id'] }}">{{ $card['value'] }}</div>
                        @if ($card['active'])
                            <span class="apth-active-dot">Active</span>
                        @endif
                    </div>
                    <div class="apth-title">
                        <span class="apth-title-txt">{{ $card['title'] }}</span>
                        @if (!empty($card['hint']))
                            <span class="apth-hint" id="apth_hint_{{ $card['id'] }}" title="{{ $card['hint'] }}">ⓘ</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="apth-sub">
                <span class="apth-sub-left">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="{{ $card['sub_icon'] }}" />
                    </svg>
                    <span>{{ $card['sub'] }}</span>
                </span>
                <svg class="apth-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 12h14" /><path d="m12 5 7 7-7 7" />
                </svg>
            </div>
        </a>
    @endforeach
</div>
