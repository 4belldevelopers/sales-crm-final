@php
    // Navigation KPI Tab Cards for the AppTracker header (Client Tracker .cl-stat-grid design standard).
    // Six routes (Pool / Similar Matching / In-house Bench / Agency Bench / Bench Talent Requests / Career Page)
    // include this same partial with their own $tabactive flags, so active highlight is decided server-side.
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
            // This is a count against the JOB'S DEFAULT skills/location/
            // experience — not your live filter selection on the Similar
            // Candidates page (starring a skill mandatory, adding a
            // location, etc. only affects that page's own count while
            // you're on it). The two are answering different questions by
            // design, so a mismatch between this card and the Similar
            // Candidates page's own number is expected, not a bug.
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
        [
            'tab_key' => 'agency_bench',
            'title' => 'Agency Bench Talents',
            'sub' => 'Partner agency talent',
            'sub_icon' => 'M4 9h16 M4 15h16 M10 3l-2 18 M16 3l-2 18',
            'url' => $tabactive['agencyBench_url'],
            'id' => $tabactive['agencyBench_id'],
            'value' => $tabactive['public_bench_counter'],
            'active' => $tabactive['agencyBench_active'],
            'color' => '#ea580c',
            'bg' => '#fff7ed',
            'tint' => '#fff7ed',
            'border' => '#fb923c',
            'deep' => '#9a3412',
            'lbl_active' => '#c2410c',
            'glow' => 'rgba(234, 88, 12, 0.12)',
            'icon' => 'M20 7H4a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2',
        ],
        [
            'tab_key' => 'bench_req',
            'title' => 'Bench Talent Requests',
            'sub' => 'Vendor submissions',
            'sub_icon' => 'M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0-18 0 M12 7v5l3 2',
            'url' => $tabactive['benchTalentReq_url'],
            'id' => $tabactive['benchTalentReq_id'],
            'value' => $tabactive['recomnded_counter'],
            'active' => $tabactive['benchTalentReq_active'],
            'color' => '#db2777',
            'bg' => '#fdf2f8',
            'tint' => '#fdf2f8',
            'border' => '#f472b6',
            'deep' => '#9d174d',
            'lbl_active' => '#be185d',
            'glow' => 'rgba(219, 39, 119, 0.12)',
            'icon' => 'M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z M14 2v6h6 M16 13H8 M16 17H8',
        ],
        [
            'tab_key' => 'career',
            'title' => 'Career Page',
            'sub' => 'Direct job applicants',
            'sub_icon' => 'M20 6 9 17l-5-5',
            'url' => $tabactive['career_url'],
            'id' => $tabactive['career_id'],
            'value' => $tabactive['career_counter'],
            'active' => $tabactive['career_active'],
            'color' => '#0d9488',
            'bg' => '#f0fdfa',
            'tint' => '#f0fdfa',
            'border' => '#5eead4',
            'deep' => '#115e59',
            'lbl_active' => '#0f766e',
            'glow' => 'rgba(13, 148, 136, 0.12)',
            'icon' => 'M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Z M3.6 9h16.8 M3.6 15h16.8 M11.5 3a17 17 0 0 0 0 18 M12.5 3a17 17 0 0 1 0 18',
        ],
    ];
@endphp

<style>
    /* AppTracker Header Tabs — Client Tracker .cl-stat-grid Enterprise Standard */
    .apth-grid {
        display: grid !important;
        grid-template-columns: repeat(6, minmax(0, 1fr)) !important;
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

    /* 15.6" Laptop (1366x768) & Tablet/Mobile Scaling — Identical to Client Tracker */
    @media (max-width: 1440px) {
        .apth-grid {
            grid-template-columns: repeat(6, minmax(0, 1fr)) !important;
            gap: 6px !important;
        }
        .apth-card {
            padding: 8px 8px !important;
        }
        .apth-card-top {
            gap: 7px !important;
            margin-bottom: 5px !important;
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
            margin-top: 2px !important;
        }
        .apth-sub-left svg,
        .apth-arrow {
            width: 10px !important;
            height: 10px !important;
        }
    }
    @media (max-width: 1150px) {
        .apth-grid {
            grid-template-columns: repeat(6, minmax(0, 1fr)) !important;
            gap: 5px !important;
        }
        .apth-card {
            padding: 7px 6px !important;
            border-radius: 9px !important;
        }
        .apth-card-top {
            gap: 6px !important;
            margin-bottom: 4px !important;
        }
        .apth-icon {
            width: 26px !important;
            height: 26px !important;
            min-width: 26px !important;
            border-radius: 6px !important;
        }
        .apth-icon svg {
            width: 13px !important;
            height: 13px !important;
        }
        .apth-value {
            font-size: 15.5px !important;
        }
        .apth-title {
            font-size: 9.5px !important;
        }
        .apth-sub {
            font-size: 9px !important;
            margin-top: 1px !important;
        }
    }
    @media (max-width: 900px) {
        .apth-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
            gap: 6px !important;
        }
    }
    @media (max-width: 576px) {
        .apth-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            gap: 6px !important;
        }
    }
    @media (max-width: 400px) {
        .apth-grid {
            grid-template-columns: minmax(0, 1fr) !important;
        }
    }
</style>

<div class="apth-grid" role="navigation" aria-label="AppTracker sections">
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
