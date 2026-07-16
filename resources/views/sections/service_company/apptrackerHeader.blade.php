@php
    // Hero KPI cards for the AppTracker header. Six routes (Pool / Similar Matching /
    // In-house Bench / Agency Bench / Bench Talent Requests / Career Page) each
    // include this same partial with their own $tabactive flags, so the "active" card
    // here is decided entirely server-side per page — no JS tab-switch logic needed.
    $apthCards = [
        [
            'title' => 'Total Candidates In Job',
            'url' => $tabactive['pool_url'],
            'id' => $tabactive['pool_id'],
            'value' => $tabactive['pool_counter'],
            'active' => $tabactive['pool_active'],
            'bg' => '#dbeafe',
            'ic' => '#2563eb',
            'icon' => 'M17 20h5v-2a4 4 0 0 0-3-3.87M9 20H4v-2a4 4 0 0 1 3-3.87m6-1.13a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z',
        ],
        [
            'title' => 'Similar Candidates',
            'url' => $tabactive['similar_url'],
            'id' => $tabactive['similar_id'],
            'value' => $tabactive['existing_counter'],
            'active' => $tabactive['similar_active'],
            'bg' => '#dcfce7',
            'ic' => '#16a34a',
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
            'title' => 'In-house Bench Talents',
            'url' => $tabactive['inhouseBench_url'],
            'id' => $tabactive['inhouseBench_id'],
            'value' => $tabactive['internal_bench_counter'],
            'active' => $tabactive['inhouseBench_active'],
            'bg' => '#ede9fe',
            'ic' => '#7c3aed',
            'icon' => 'M11.47 3.84a.75.75 0 0 1 1.06 0l8.69 8.69a.75.75 0 1 0 1.06-1.06l-8.689-8.69a2.25 2.25 0 0 0-3.182 0l-8.69 8.69a.75.75 0 1 0 1.061 1.06l8.69-8.69ZM12 5.432l8.159 8.159a2.29 2.29 0 0 1 .091.086v6.198c0 1.035-.84 1.875-1.875 1.875H15a.75.75 0 0 1-.75-.75v-4.5a.75.75 0 0 0-.75-.75h-3a.75.75 0 0 0-.75.75V21a.75.75 0 0 1-.75.75H5.625a1.875 1.875 0 0 1-1.875-1.875v-6.198a2.29 2.29 0 0 1 .091-.086L12 5.432Z',
        ],
        [
            'title' => 'Agency Bench Talents',
            'url' => $tabactive['agencyBench_url'],
            'id' => $tabactive['agencyBench_id'],
            'value' => $tabactive['public_bench_counter'],
            'active' => $tabactive['agencyBench_active'],
            'bg' => '#ffedd5',
            'ic' => '#ea580c',
            'icon' => 'M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z',
        ],
        [
            'title' => 'Bench Talent Requests',
            'url' => $tabactive['benchTalentReq_url'],
            'id' => $tabactive['benchTalentReq_id'],
            'value' => $tabactive['recomnded_counter'],
            'active' => $tabactive['benchTalentReq_active'],
            'bg' => '#ffe4e6',
            'ic' => '#e11d48',
            'icon' => 'M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3ZM6 6h.008v.008H6V6Z',
        ],
        [
            'title' => 'Career Page',
            'url' => $tabactive['career_url'],
            'id' => $tabactive['career_id'],
            'value' => $tabactive['career_counter'],
            'active' => $tabactive['career_active'],
            'bg' => '#ccfbf1',
            'ic' => '#0d9488',
            'icon' => 'M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A8.959 8.959 0 0 1 3 12c0-.778.099-1.533.284-2.253',
        ],
    ];
@endphp

<style>
    .apth-grid {
        display: grid;
        grid-template-columns: repeat(6, minmax(0, 1fr));
        gap: 10px;
        width: 100%;
        font-family: 'Poppins', sans-serif;
    }
    .apth-card {
        display: flex;
        flex-direction: column;
        min-width: 0;
        overflow: hidden;
        background: #fff;
        border: 1px solid #e4e6ef;
        border-radius: 10px;
        padding: 10px 12px;
        text-decoration: none;
        transition: border-color .15s, background-color .15s;
    }
    .apth-card:hover {
        text-decoration: none;
        border-color: #b5b5c3;
    }
    .apth-card.active {
        border-color: var(--apth-color);
        background: var(--apth-bg);
    }
    .apth-icon {
        width: 19px;
        height: 19px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .apth-icon svg {
        width: 10px;
        height: 10px;
    }
    .apth-title {
        font-size: 11px;
        font-weight: 500;
        color: #7e8299;
        margin: 0 0 5px;
        line-height: 1.3;
        display: -webkit-box;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 2;
        overflow: hidden;
        transition: color .15s, font-weight .15s;
    }
    .apth-card.active .apth-title {
        font-weight: 700;
        color: #181c32;
    }
    .apth-hint {
        cursor: help;
        color: #b5b5c3;
        font-size: 10px;
    }
    .apth-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 6px;
    }
    .apth-row-left {
        display: flex;
        align-items: center;
        gap: 6px;
        min-width: 0;
    }
    .apth-value {
        font-size: 13px;
        font-weight: 700;
        color: #181c32;
        line-height: 1.2;
        min-width: 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .apth-viewlist {
        font-size: 9px;
        font-weight: 500;
        color: var(--bs-primary, #3699ff);
        white-space: nowrap;
        display: inline-flex;
        align-items: center;
        gap: 2px;
        flex-shrink: 0;
    }
    .apth-viewlist svg {
        width: 11px;
        height: 11px;
        flex-shrink: 0;
    }
    @media (max-width: 992px) {
        .apth-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    }
    @media (max-width: 576px) {
        .apth-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    @media (max-width: 400px) {
        .apth-grid { grid-template-columns: minmax(0, 1fr); }
    }
</style>

<div class="apth-grid">
    @foreach ($apthCards as $card)
        <a href="{{ $card['url'] }}"
            class="apth-card {{ $card['active'] ? 'active' : '' }}"
            style="--apth-color: {{ $card['ic'] }}; --apth-bg: {{ $card['bg'] }};">
            <p class="apth-title">{{ $card['title'] }}
                @if (!empty($card['hint']))
                    <span class="apth-hint" id="apth_hint_{{ $card['id'] }}" title="{{ $card['hint'] }}">ⓘ</span>
                @endif
            </p>
            <div class="apth-row">
                <span class="apth-row-left">
                    <span class="apth-icon" style="background: {{ $card['bg'] }};">
                        <svg viewBox="0 0 24 24" fill="none" stroke="{{ $card['ic'] }}" stroke-width="1.6"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="{{ $card['icon'] }}" />
                        </svg>
                    </span>
                    <span class="apth-value" id="{{ $card['id'] }}">{{ $card['value'] }}</span>
                </span>
                <span class="apth-viewlist">
                    View List
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                    </svg>
                </span>
            </div>
        </a>
    @endforeach
</div>
