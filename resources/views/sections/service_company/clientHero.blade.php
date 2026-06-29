{{-- Shared Clients hero header. The four stat cards double as tab navigation.
     $activeTab: 'all' | 'direct' | 'internal' | 'request' (defaults to 'all'). --}}
@php $activeTab = $activeTab ?? 'all'; @endphp
<div class="hero-section">
    <div class="hero-left">
        <div class="hero-title">
            Clients
            <svg class="shield-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                <path d="m9 12 2 2 4-4" stroke="#16A34A"/>
            </svg>
        </div>
        <div class="hero-sub">Manage and track your clients</div>
        <div class="stats-row">
            <a href="{{ route('service_company.clients.index') }}" class="stat-card {{ $activeTab == 'all' ? 'active' : '' }}">
                <div class="stat-icon blue">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>
                <div class="stat-info">
                    <div class="stat-num">{{ @$clientDataCnt['clientAll'] ?? 0 }}</div>
                    <div class="stat-label">All Clients</div>
                </div>
            </a>
            <a href="{{ url('service_company/clients/direct_client/0') }}" class="stat-card {{ $activeTab == 'direct' ? 'active' : '' }}">
                <div class="stat-icon green">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    </svg>
                </div>
                <div class="stat-info">
                    <div class="stat-num">{{ @$clientDataCnt['clientDirect'] ?? 0 }}</div>
                    <div class="stat-label">Direct Clients</div>
                </div>
            </a>
            <a href="{{ url('service_company/clients/custom_client/0') }}" class="stat-card {{ $activeTab == 'internal' ? 'active' : '' }}">
                <div class="stat-icon yellow">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                    </svg>
                </div>
                <div class="stat-info">
                    <div class="stat-num">{{ @$clientDataCnt['clientCustom'] ?? 0 }}</div>
                    <div class="stat-label">Internal Clients</div>
                </div>
            </a>
            <a href="{{ url('service_company/clients/client_request/0') }}" class="stat-card with-badge {{ $activeTab == 'request' ? 'active' : '' }}">
                <span class="new-badge">New</span>
                <div class="stat-icon purple">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                </div>
                <div class="stat-info">
                    <div class="stat-num">{{ @$clientDataCnt['clientRequest'] ?? 0 }}</div>
                    <div class="stat-label">Client Requests</div>
                </div>
            </a>
        </div>
    </div>
    <!-- Illustration -->
    <div class="illus-svg-wrap">
        <svg viewBox="0 0 220 160" xmlns="http://www.w3.org/2000/svg" width="220" height="160">
            <rect x="0" y="30" width="90" height="60" rx="10" fill="#EFF6FF" stroke="#BFDBFE" stroke-width="1"/>
            <circle cx="16" cy="60" r="9" fill="#94A3B8" transform="translate(8,0)"/>
            <rect x="34" y="52" width="46" height="6" rx="3" fill="#CBD5E1"/>
            <rect x="34" y="62" width="32" height="4" rx="2" fill="#E2E8F0"/>
            <rect x="34" y="70" width="22" height="4" rx="2" fill="#BFDBFE"/>
            <text x="64" y="76" font-size="8" fill="#F59E0B">★★★★★</text>
            <ellipse cx="120" cy="148" rx="22" ry="6" fill="#E2E8F0" opacity=".5"/>
            <rect x="108" y="90" width="24" height="38" rx="4" fill="#2563EB"/>
            <circle cx="120" cy="82" r="12" fill="#FBBF24"/>
            <path d="M108 80 Q120 68 132 80" fill="#1E293B"/>
            <rect x="108" y="78" width="4" height="14" rx="2" fill="#1E293B"/>
            <path d="M113 90 L120 98 L127 90" fill="white" opacity=".6"/>
            <rect x="100" y="92" width="10" height="5" rx="2.5" fill="#2563EB"/>
            <rect x="130" y="92" width="10" height="5" rx="2.5" fill="#2563EB"/>
            <rect x="112" y="126" width="7" height="16" rx="3" fill="#1E293B"/>
            <rect x="121" y="126" width="7" height="16" rx="3" fill="#1E293B"/>
            <ellipse cx="115" cy="143" rx="5" ry="3" fill="#111827"/>
            <ellipse cx="124" cy="143" rx="5" ry="3" fill="#111827"/>
            <ellipse cx="175" cy="148" rx="22" ry="6" fill="#E2E8F0" opacity=".5"/>
            <rect x="163" y="90" width="24" height="38" rx="4" fill="#1E40AF"/>
            <circle cx="175" cy="82" r="12" fill="#D97706"/>
            <path d="M163 78 Q175 68 187 78 Q185 72 175 70 Q165 72 163 78Z" fill="#1E293B"/>
            <path d="M168 88 Q175 94 182 88 Q179 92 175 93 Q171 92 168 88Z" fill="#92400E" opacity=".7"/>
            <path d="M168 90 L175 98 L182 90" fill="white" opacity=".6"/>
            <path d="M175 94 L173 108 L175 110 L177 108 Z" fill="#EF4444"/>
            <rect x="155" y="92" width="10" height="5" rx="2.5" fill="#1E40AF"/>
            <rect x="185" y="92" width="10" height="5" rx="2.5" fill="#1E40AF"/>
            <rect x="167" y="126" width="7" height="16" rx="3" fill="#1E293B"/>
            <rect x="176" y="126" width="7" height="16" rx="3" fill="#1E293B"/>
            <ellipse cx="170" cy="143" rx="5" ry="3" fill="#111827"/>
            <ellipse cx="179" cy="143" rx="5" ry="3" fill="#111827"/>
            <path d="M131 108 Q148 114 164 108" stroke="#F59E0B" stroke-width="3" stroke-linecap="round" fill="none"/>
            <circle cx="131" cy="108" r="4" fill="#FBBF24"/>
            <circle cx="164" cy="108" r="4" fill="#FBBF24"/>
            <rect x="150" y="10" width="68" height="38" rx="8" fill="white" stroke="#E5E7EB" stroke-width="1"/>
            <circle cx="162" cy="22" r="5" fill="#94A3B8"/>
            <rect x="171" y="16" width="36" height="5" rx="2.5" fill="#CBD5E1"/>
            <text x="171" y="34" font-size="9" fill="#F59E0B">★★★★★</text>
            <circle cx="205" cy="58" r="13" fill="#DCFCE7" stroke="#86EFAC" stroke-width="1.5"/>
            <path d="M199 58l4 4 7-7" stroke="#16A34A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
        </svg>
    </div>
</div>
