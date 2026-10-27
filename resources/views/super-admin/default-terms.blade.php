@extends('layouts.superadmin_layout')

@push('head-script')
<style>
/* ==========================================================================
   Master Enterprise Scoped Styling — Default Terms Edit / View Module
   Strictly Optimized for 15.6" Screens (1366x768 & 1920x1080) and Smaller
   Follows UI/UX Standard: Modern Enterprise Card + Sub-Card Design System
   ========================================================================== */
#kt_body {
    overflow-x: hidden !important;
}

/* ==========================================================================
   Top Gap Optimization: Collapse Metronic toolbar-fixed height and wrapper padding
   ========================================================================== */
#kt_toolbar {
    display: none !important;
    height: 0 !important;
    min-height: 0 !important;
    padding: 0 !important;
    margin: 0 !important;
}

.header-fixed.toolbar-fixed #kt_wrapper,
.header-fixed #kt_wrapper,
.toolbar-fixed #kt_wrapper,
#kt_wrapper {
    padding-top: calc(65px + 12px) !important;
}

@media (max-width: 991.98px) {
    .header-fixed.toolbar-fixed #kt_wrapper,
    .header-fixed #kt_wrapper,
    .toolbar-fixed #kt_wrapper,
    #kt_wrapper {
        padding-top: calc(55px + 8px) !important;
    }
}

#kt_content,
.content {
    padding-top: 0 !important;
    margin-top: 0 !important;
}

.cd-terms-edit-wrap {
    font-family: 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif !important;
    box-sizing: border-box !important;
    width: 100% !important;
    max-width: 100% !important;
    overflow-x: hidden !important;
    padding: 0 16px 40px 16px !important;
    margin-top: 0 !important;
}

.cd-terms-edit-wrap * {
    box-sizing: border-box !important;
}

/* Guarantee font icon preservation across all FontAwesome and Metronic icon classes */
.cd-terms-edit-wrap i,
.cd-terms-edit-wrap [class*="fa-"],
.cd-terms-edit-wrap .fa,
.cd-terms-edit-wrap .fas,
.cd-terms-edit-wrap .far,
.cd-terms-edit-wrap .fab,
.cd-terms-edit-wrap [class*="ki-"] {
    font-family: "Font Awesome 6 Free", "FontAwesome", "Font Awesome 5 Free", sans-serif !important;
    font-style: normal !important;
    font-weight: 900 !important;
}

/* ==========================================================================
   Tier 1: Executive Header & Navigation Toolbar Card
   ========================================================================== */
.cd-edit-header-card {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 10px !important;
    padding: 12px 18px !important;
    margin-bottom: 12px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 14px !important;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04) !important;
    flex-wrap: wrap !important;
    width: 100% !important;
}

.cd-header-left-suite {
    display: flex !important;
    align-items: center !important;
    gap: 12px !important;
    min-width: 0 !important;
}

.cd-back-btn {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 6px !important;
    height: 36px !important;
    padding: 0 12px !important;
    border-radius: 8px !important;
    background: #f1f5f9 !important;
    color: #475569 !important;
    border: 1px solid #e2e8f0 !important;
    font-size: 12px !important;
    font-weight: 600 !important;
    text-decoration: none !important;
    transition: all 0.15s ease !important;
    white-space: nowrap !important;
}

.cd-back-btn:hover {
    background: #e2e8f0 !important;
    color: #0f172a !important;
}

.cd-edit-header-icon {
    width: 38px !important;
    height: 38px !important;
    border-radius: 8px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 16px !important;
    flex-shrink: 0 !important;
}

.cd-icon-offers { background: #eff6ff !important; color: #2563eb !important; }
.cd-icon-legal { background: #ecfdf5 !important; color: #059669 !important; }
.cd-icon-agency { background: #f5f3ff !important; color: #7c3aed !important; }
.cd-icon-commercial { background: #fffbeb !important; color: #d97706 !important; }

.cd-edit-header-title {
    font-size: 15px !important;
    font-weight: 700 !important;
    color: #0f172a !important;
    line-height: 1.25 !important;
}

.cd-edit-header-meta {
    display: flex !important;
    align-items: center !important;
    gap: 6px !important;
    margin-top: 2px !important;
    flex-wrap: wrap !important;
}

.cd-header-right-suite {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    flex-wrap: wrap !important;
}

/* Audience Pills */
.cd-aud-pill {
    font-size: 11px !important;
    font-weight: 600 !important;
    padding: 3px 9px !important;
    border-radius: 6px !important;
}

.cd-aud-candidates { background: #eff6ff !important; color: #1d4ed8 !important; border: 1px solid #dbeafe !important; }
.cd-aud-website { background: #ecfdf5 !important; color: #047857 !important; border: 1px solid #d1fae5 !important; }
.cd-aud-vendors { background: #fffbeb !important; color: #b45309 !important; border: 1px solid #fef3c7 !important; }
.cd-aud-agencies { background: #f5f3ff !important; color: #6d28d9 !important; border: 1px solid #ede9fe !important; }

/* Status Badges */
.cd-stat-badge {
    display: inline-flex !important;
    align-items: center !important;
    gap: 5px !important;
    font-size: 11px !important;
    font-weight: 600 !important;
    padding: 3px 9px !important;
    border-radius: 12px !important;
}

.cd-stat-configured {
    background: #ecfdf5 !important;
    color: #047857 !important;
    border: 1px solid #a7f3d0 !important;
}

.cd-stat-configured i { color: #10b981 !important; font-size: 7px !important; }

.cd-stat-empty {
    background: #fffbeb !important;
    color: #b45309 !important;
    border: 1px solid #fde68a !important;
}

.cd-stat-empty i { color: #f59e0b !important; font-size: 7px !important; }

/* Quick Save Button */
.cd-btn-quick-save {
    display: inline-flex !important;
    align-items: center !important;
    gap: 6px !important;
    height: 36px !important;
    padding: 0 16px !important;
    border-radius: 8px !important;
    background: #009ef7 !important;
    color: #ffffff !important;
    border: none !important;
    font-size: 12px !important;
    font-weight: 600 !important;
    cursor: pointer !important;
    transition: all 0.15s ease !important;
}

.cd-btn-quick-save:hover {
    background: #0086d4 !important;
    box-shadow: 0 2px 8px rgba(0, 158, 247, 0.3) !important;
}

@media (max-width: 767.98px) {
    .cd-edit-header-card {
        padding: 10px 14px !important;
        gap: 10px !important;
    }
    .cd-header-left-suite {
        width: 100% !important;
    }
    .cd-header-right-suite {
        width: 100% !important;
        justify-content: space-between !important;
    }
}

/* ==========================================================================
   Tier 2: Interactive Token Insertion Toolbar Card
   ========================================================================== */
.cd-edit-tokens-card {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 10px !important;
    padding: 10px 16px !important;
    margin-bottom: 12px !important;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
    width: 100% !important;
}

.cd-tokens-header {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    margin-bottom: 8px !important;
    flex-wrap: wrap !important;
    gap: 8px !important;
}

.cd-tokens-label {
    font-size: 11px !important;
    font-weight: 700 !important;
    color: #475569 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.4px !important;
    display: flex !important;
    align-items: center !important;
    gap: 6px !important;
}

.cd-tokens-sublabel {
    font-size: 11px !important;
    color: #94a3b8 !important;
}

.cd-tokens-bar {
    display: flex !important;
    flex-wrap: wrap !important;
    gap: 6px !important;
    align-items: center !important;
}

.cd-token-btn {
    display: inline-flex !important;
    align-items: center !important;
    gap: 5px !important;
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace !important;
    font-size: 11px !important;
    font-weight: 600 !important;
    padding: 3px 8px !important;
    background: #eff6ff !important;
    color: #1d4ed8 !important;
    border: 1px solid #bfdbfe !important;
    border-radius: 6px !important;
    cursor: pointer !important;
    user-select: none !important;
    transition: all 0.15s ease !important;
}

.cd-token-btn:hover {
    background: #dbeafe !important;
    border-color: #93c5fd !important;
    transform: translateY(-1px) !important;
    box-shadow: 0 2px 4px rgba(37, 99, 235, 0.1) !important;
}

.cd-token-btn:active {
    transform: translateY(0) !important;
}

.cd-token-btn i.fa-plus,
.cd-token-btn i.fa-table {
    font-size: 9px !important;
    opacity: 0.7 !important;
}

.cd-token-table-btn {
    background: #f5f3ff !important;
    color: #6d28d9 !important;
    border-color: #ddd6fe !important;
}

.cd-token-table-btn:hover {
    background: #ede9fe !important;
    border-color: #c4b5fd !important;
}

@keyframes tokenPulse {
    0% { transform: scale(1); background: #dbeafe; }
    50% { transform: scale(1.08); background: #009ef7; color: #ffffff; border-color: #009ef7; }
    100% { transform: scale(1); background: #eff6ff; }
}

.token-copied-pulse {
    animation: tokenPulse 0.45s ease-in-out !important;
}

/* ==========================================================================
   Tier 3: Master Editor Container Card & Commercial Sub-Cards
   ========================================================================== */
.cd-editor-container-card {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 10px !important;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04) !important;
    padding: 16px 20px !important;
    margin-bottom: 14px !important;
    width: 100% !important;
}

.cd-editor-card-header {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    margin-bottom: 12px !important;
    padding-bottom: 10px !important;
    border-bottom: 1px solid #f1f5f9 !important;
    flex-wrap: wrap !important;
    gap: 8px !important;
}

.cd-section-badge-icon {
    width: 32px !important;
    height: 32px !important;
    border-radius: 6px !important;
    background: #eff6ff !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 14px !important;
    flex-shrink: 0 !important;
}

.cd-editor-card-title {
    font-size: 13.5px !important;
    font-weight: 700 !important;
    color: #0f172a !important;
    line-height: 1.2 !important;
}

.cd-editor-counters {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    font-size: 11.5px !important;
    color: #64748b !important;
    flex-wrap: wrap !important;
}

.cd-counter-pill {
    background: #f8fafc !important;
    border: 1px solid #e2e8f0 !important;
    padding: 3px 9px !important;
    border-radius: 6px !important;
    display: inline-flex !important;
    align-items: center !important;
    font-size: 11px !important;
}

/* CKEditor Skin Normalization */
.cke_chrome {
    border: 1px solid #cbd5e1 !important;
    border-radius: 8px !important;
    overflow: hidden !important;
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04) !important;
}

.cke_top {
    background: #f8fafc !important;
    border-bottom: 1px solid #e2e8f0 !important;
    padding: 6px !important;
}

.cke_bottom {
    background: #f8fafc !important;
    border-top: 1px solid #e2e8f0 !important;
    padding: 4px 8px !important;
}

/* Agency Commercial Defaults Sub-Cards */
.cd-commercial-section-card {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 10px !important;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
    padding: 18px 20px !important;
    margin-bottom: 14px !important;
    width: 100% !important;
}

.cd-commercial-section-header {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    margin-bottom: 16px !important;
    padding-bottom: 12px !important;
    border-bottom: 1px solid #f1f5f9 !important;
    flex-wrap: wrap !important;
    gap: 10px !important;
}

.cd-commercial-section-title {
    font-size: 13.5px !important;
    font-weight: 700 !important;
    color: #0f172a !important;
}

.cd-form-label {
    font-size: 12px !important;
    font-weight: 600 !important;
    color: #334155 !important;
    margin-bottom: 5px !important;
    display: block !important;
}

.cd-form-input,
.cd-form-select {
    height: 38px !important;
    border-radius: 7px !important;
    border: 1px solid #cbd5e1 !important;
    background-color: #ffffff !important;
    font-size: 12.5px !important;
    color: #0f172a !important;
    padding: 0 12px !important;
    transition: all 0.15s ease !important;
}

.cd-form-input:focus,
.cd-form-select:focus {
    border-color: #009ef7 !important;
    box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.15) !important;
    outline: none !important;
}

.cd-pricing-row {
    background: #f8fafc !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 8px !important;
    padding: 8px 12px !important;
    margin-bottom: 8px !important;
    transition: all 0.15s ease !important;
}

.cd-pricing-row:hover {
    background: #f1f5f9 !important;
    border-color: #cbd5e1 !important;
}

.cd-btn-add-row {
    display: inline-flex !important;
    align-items: center !important;
    gap: 6px !important;
    background: #eff6ff !important;
    color: #2563eb !important;
    border: 1px dashed #bfdbfe !important;
    font-size: 11.5px !important;
    font-weight: 600 !important;
    padding: 5px 12px !important;
    border-radius: 6px !important;
    cursor: pointer !important;
    transition: all 0.15s ease !important;
}

.cd-btn-add-row:hover {
    background: #dbeafe !important;
    border-color: #93c5fd !important;
}

.cd-btn-remove-row {
    width: 36px !important;
    height: 36px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    background: #fef2f2 !important;
    color: #ef4444 !important;
    border: 1px solid #fecaca !important;
    border-radius: 6px !important;
    cursor: pointer !important;
    transition: all 0.15s ease !important;
}

.cd-btn-remove-row:hover {
    background: #fee2e2 !important;
    color: #dc2626 !important;
}

/* ==========================================================================
   Tier 4: Bottom Sticky Action Strip
   ========================================================================== */
.cd-edit-bottom-strip {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 10px !important;
    box-shadow: 0 4px 14px rgba(15, 23, 42, 0.08) !important;
    padding: 12px 20px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 12px !important;
    flex-wrap: wrap !important;
    width: 100% !important;
    position: sticky !important;
    bottom: 12px !important;
    z-index: 100 !important;
}

@media (max-width: 767.98px) {
    .cd-edit-bottom-strip {
        padding: 10px 14px !important;
        gap: 10px !important;
    }
    .cd-strip-meta {
        width: 100% !important;
    }
    .cd-strip-actions {
        width: 100% !important;
        justify-content: space-between !important;
    }
}

.cd-strip-meta {
    display: flex !important;
    align-items: center !important;
    gap: 12px !important;
    font-size: 11.5px !important;
    color: #64748b !important;
    flex-wrap: wrap !important;
}

.cd-strip-actions {
    display: flex !important;
    align-items: center !important;
    gap: 10px !important;
}

.cd-btn-cancel {
    display: inline-flex !important;
    align-items: center !important;
    gap: 6px !important;
    height: 38px !important;
    padding: 0 16px !important;
    border-radius: 8px !important;
    background: #f8fafc !important;
    color: #475569 !important;
    border: 1px solid #e2e8f0 !important;
    font-size: 12.5px !important;
    font-weight: 600 !important;
    text-decoration: none !important;
    transition: all 0.15s ease !important;
}

.cd-btn-cancel:hover {
    background: #f1f5f9 !important;
    color: #0f172a !important;
    border-color: #cbd5e1 !important;
}

.cd-btn-save-primary {
    display: inline-flex !important;
    align-items: center !important;
    gap: 6px !important;
    height: 38px !important;
    padding: 0 20px !important;
    border-radius: 8px !important;
    background: #009ef7 !important;
    color: #ffffff !important;
    border: none !important;
    font-size: 12.5px !important;
    font-weight: 600 !important;
    cursor: pointer !important;
    transition: all 0.15s ease !important;
}

.cd-btn-save-primary:hover {
    background: #0086d4 !important;
    box-shadow: 0 4px 12px rgba(0, 158, 247, 0.3) !important;
}
</style>
@endpush

@php
    use App\Helper\GeneralMethod;
@endphp

@section('content')
<div class="cd-terms-edit-wrap">
    <form id="editSettings" class="ajax-form">
        @csrf
        <input type="hidden" name="encrypt_id" id="encrypt_id" value="{{ $encrypt_id }}">
        <input type="hidden" name="purpose" id="purpose" value="{{ $title }}">
        <input type="hidden" name="used" id="used" value="{{ $used }}">

        {{-- Tier 1: Executive Header & Navigation Toolbar Card --}}
        @php
            $aud = $used ?? 'For Website';
            $audClass = 'cd-aud-website';
            if (stripos($aud, 'Candidate') !== false) {
                $audClass = 'cd-aud-candidates';
            } elseif (stripos($aud, 'Vendor') !== false) {
                $audClass = 'cd-aud-vendors';
            } elseif (stripos($aud, 'Agenc') !== false) {
                $audClass = 'cd-aud-agencies';
            }
        @endphp
        <div class="cd-edit-header-card">
            <div class="cd-header-left-suite">
                <a href="{{ route('superadmin.company.defaultTermsList') }}" class="cd-back-btn" title="Return to Terms List">
                    <i class="fa fa-arrow-left"></i>
                    <span>Back to List</span>
                </a>
                <div class="cd-edit-header-icon cd-icon-{{ $termCategory ?? 'legal' }}">
                    <i class="fa {{ $termIcon ?? 'fa-file-lines' }}"></i>
                </div>
                <div class="d-flex flex-column">
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span class="cd-edit-header-title">{{ $title }}</span>
                        <span class="badge badge-light-secondary text-muted fw-bold font-monospace" style="font-size: 11px; padding: 2px 7px;">{{ $seqId ?? '#TERM-001' }}</span>
                        @if(!empty($categoryLabel))
                            <span class="badge badge-light-primary fw-semibold" style="font-size: 11px; padding: 2px 7px;">{{ $categoryLabel }}</span>
                        @endif
                    </div>
                    @if(!empty($scopeNote))
                        <div class="text-muted fs-8 mt-1" style="line-height: 1.3;">{{ $scopeNote }}</div>
                    @endif
                </div>
            </div>
            <div class="cd-header-right-suite">
                <span class="cd-aud-pill {{ $audClass }}">
                    <i class="fa fa-users me-1 fs-9"></i> {{ $aud }}
                </span>
                @if($isConfigured ?? false)
                    <span class="cd-stat-badge cd-stat-configured" title="Template has active content">
                        <i class="fa fa-circle"></i> Configured
                    </span>
                @else
                    <span class="cd-stat-badge cd-stat-empty" title="Template has not been authored yet">
                        <i class="fa fa-circle"></i> Needs Setup
                    </span>
                @endif
                <button type="button" id="btn_quick_save_top" class="cd-btn-quick-save" title="Save changes immediately">
                    <i class="fa fa-save"></i>
                    <span>Quick Save</span>
                </button>
            </div>
        </div>

        @if(!empty($fallbackNotice))
            <div class="alert alert-light-warning d-flex align-items-center p-3 mb-3 border border-warning border-dashed" style="border-radius: 8px;">
                <i class="fa fa-circle-info text-warning fs-4 me-3"></i>
                <div class="d-flex flex-column">
                    <span class="fw-bold text-dark fs-7">Template Starter Copy Notice</span>
                    <span class="text-muted fs-8">{{ $fallbackNotice }}</span>
                </div>
            </div>
        @endif

        {{-- Tier 2: Interactive Variable Token Insertion Toolbar Card --}}
        @if(!empty($supportedTokens) || !empty($publicRoute))
        <div class="cd-edit-tokens-card">
            <div class="cd-tokens-header">
                <div class="cd-tokens-label">
                    <i class="fa fa-code text-primary fs-8"></i>
                    @if(!empty($supportedTokens))
                        <span>Available Dynamic Tokens</span>
                        <span class="badge badge-light-primary ms-1" style="font-size: 10px; padding: 2px 6px;">
                            {{ count($supportedTokens) }} Tokens
                        </span>
                    @else
                        <span>Live Public Route</span>
                        <span class="badge badge-light-success ms-1" style="font-size: 10px; padding: 2px 6px;">Production</span>
                    @endif
                </div>
                <div class="cd-tokens-sublabel">
                    @if(!empty($supportedTokens))
                        @if(isset($agencyCommercialDefaults))
                            <i class="fa fa-copy fs-9 me-1"></i> Click to copy commercial token to clipboard for use in Offer Letters
                        @else
                            <i class="fa fa-mouse-pointer fs-9 me-1"></i> Click any token below to insert at active editor cursor
                        @endif
                    @elseif(!empty($publicRoute))
                        <i class="fa fa-globe fs-9 me-1"></i> Publicly accessible to candidates and companies
                    @endif
                </div>
            </div>
            
            <div class="cd-tokens-bar">
                @if(!empty($supportedTokens))
                    @foreach($supportedTokens as $token)
                        @php
                            $isTableToken = str_starts_with($token, '{');
                            $tokenClass = $isTableToken ? 'cd-token-table-btn' : '';
                        @endphp
                        <button type="button" class="cd-token-btn {{ $tokenClass }}" data-token="{{ $token }}" title="Click to insert {{ $token }}">
                            <i class="fa {{ $isTableToken ? 'fa-table' : 'fa-plus' }}"></i>
                            <span>{{ $token }}</span>
                        </button>
                    @endforeach
                @endif

                @if(!empty($publicRoute))
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span class="badge badge-light-secondary font-monospace text-dark px-3 py-2" style="font-size: 11.5px; border: 1px solid #e2e8f0;">
                            <i class="fa fa-link text-muted me-1"></i> {{ url($publicRoute) }}
                        </span>
                        <a href="{{ url($publicRoute) }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-light-primary py-1 px-3 fs-8 fw-bold">
                            <i class="fa fa-arrow-up-right-from-square me-1"></i> View Live Page
                        </a>
                    </div>
                @endif
            </div>
        </div>
        @endif

        {{-- Tier 3: Master Editor Container / Commercial Sub-Cards Matrix --}}
        <div id="cd_terms_edit_content">
            @if(isset($agencyCommercialDefaults))
                {{-- Agency Commercial Defaults (ID 13): 4-Sub-Card Enterprise Matrix --}}
                <textarea id="editor1" name="editor1" class="d-none"></textarea>
                <div id="agencyCommercialDefaultsFields">
                    
                    {{-- Sub-Card A: Full-Time Agency Commercial Defaults --}}
                    <div class="cd-commercial-section-card">
                        <div class="cd-commercial-section-header">
                            <div class="d-flex align-items-center gap-2">
                                <div class="cd-section-badge-icon" style="background: #eff6ff; color: #2563eb;">
                                    <i class="fa fa-briefcase"></i>
                                </div>
                                <div>
                                    <div class="cd-commercial-section-title">Full-Time Placement Commercial Defaults</div>
                                    <div class="text-muted fs-8">Standard recruitment commission models, guarantee periods, and credit payment SLA for full-time hires</div>
                                </div>
                            </div>
                            <span class="badge badge-light-primary fw-bold" style="font-size: 11px;">Full-Time ATS Scope</span>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="cd-form-label">Pricing Model</label>
                                <select class="form-select cd-form-select" name="fulltime_fee_model">
                                    <option value="percentage" {{ (@$agencyCommercialDefaults->fulltime_fee_model ?: 'percentage') === 'percentage' ? 'selected' : '' }}>Percentage of CTC</option>
                                    <option value="fixed" {{ @$agencyCommercialDefaults->fulltime_fee_model === 'fixed' ? 'selected' : '' }}>Fixed Fee</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="cd-form-label">Success Fee %</label>
                                <div class="input-group">
                                    <input class="form-control cd-form-input" type="number" step="0.01" name="fulltime_success_fee_percentage" value="{{ @$agencyCommercialDefaults->fulltime_success_fee_percentage ?? 8.33 }}">
                                    <span class="input-group-text bg-light text-muted border-start-0" style="font-size: 12px; border-color: #cbd5e1;">%</span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="cd-form-label">Fixed Fee Amount</label>
                                <input class="form-control cd-form-input" type="number" step="0.01" name="fulltime_fixed_fee_amount" value="{{ @$agencyCommercialDefaults->fulltime_fixed_fee_amount }}" placeholder="e.g. 50000">
                            </div>
                            <div class="col-md-3">
                                <label class="cd-form-label">Fixed Fee Unit</label>
                                <select class="form-select cd-form-select" name="fulltime_fixed_fee_unit">
                                    <option value="Per Hire" {{ (@$agencyCommercialDefaults->fulltime_fixed_fee_unit ?: 'Per Hire') === 'Per Hire' ? 'selected' : '' }}>Per Hire</option>
                                    <option value="Per Joining" {{ @$agencyCommercialDefaults->fulltime_fixed_fee_unit === 'Per Joining' ? 'selected' : '' }}>Per Joining</option>
                                </select>
                                <input type="hidden" name="fulltime_fixed_fee_currency" value="{{ @$agencyCommercialDefaults->fulltime_fixed_fee_currency ?? 4 }}">
                            </div>
                            <div class="col-md-3">
                                <label class="cd-form-label">Payment Terms</label>
                                <div class="input-group">
                                    <input class="form-control cd-form-input" type="number" name="fulltime_payment_terms_days" value="{{ @$agencyCommercialDefaults->fulltime_payment_terms_days ?? 30 }}">
                                    <span class="input-group-text bg-light text-muted border-start-0" style="font-size: 12px; border-color: #cbd5e1;">Days</span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="cd-form-label">Replacement Period</label>
                                <div class="input-group">
                                    <input class="form-control cd-form-input" type="number" name="fulltime_replacement_period_days" value="{{ @$agencyCommercialDefaults->fulltime_replacement_period_days ?? 90 }}">
                                    <span class="input-group-text bg-light text-muted border-start-0" style="font-size: 12px; border-color: #cbd5e1;">Days</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="cd-form-label">Fee Calculation Basis</label>
                                <input class="form-control cd-form-input" name="fulltime_basis" value="{{ @$agencyCommercialDefaults->fulltime_basis ?: 'Annual Fixed CTC' }}">
                            </div>
                        </div>
                    </div>

                    {{-- Sub-Card B: Full-Time Experience-Based Pricing Matrix --}}
                    <div class="cd-commercial-section-card">
                        <div class="cd-commercial-section-header">
                            <div class="d-flex align-items-center gap-2">
                                <div class="cd-section-badge-icon" style="background: #f5f3ff; color: #7c3aed;">
                                    <i class="fa fa-layer-group"></i>
                                </div>
                                <div>
                                    <div class="cd-commercial-section-title">Full-Time Experience-Based Pricing Schedule</div>
                                    <div class="text-muted fs-8">Tiered commission fee rates segmented by candidate years of experience</div>
                                </div>
                            </div>
                            <button type="button" class="cd-btn-add-row" id="btn_add_ft_row">
                                <i class="fa fa-plus fs-9"></i> Add Experience Tier
                            </button>
                        </div>

                        <div id="ft_experience_rows_wrap">
                            @php
                                $ftPricingRows = collect(@$agencyCommercialDefaults->fulltime_experience_pricing ?: [])->values();
                            @endphp
                            @forelse($ftPricingRows as $experienceRow)
                                <div class="cd-pricing-row row g-3 align-items-center">
                                    <div class="col-md-6">
                                        <label class="cd-form-label text-muted fs-9 d-md-none">Experience Band</label>
                                        <input class="form-control cd-form-input" name="fulltime_experience_label[]" value="{{ data_get($experienceRow, 'label') }}" placeholder="e.g. 0-2 Years">
                                    </div>
                                    <div class="col-md-5">
                                        <label class="cd-form-label text-muted fs-9 d-md-none">Fee Rate</label>
                                        <input class="form-control cd-form-input" name="fulltime_experience_rate[]" value="{{ data_get($experienceRow, 'rate') }}" placeholder="e.g. 8.33%">
                                    </div>
                                    <div class="col-md-1 text-end">
                                        <button type="button" class="cd-btn-remove-row" title="Delete Row">
                                            <i class="fa fa-trash-can"></i>
                                        </button>
                                    </div>
                                </div>
                            @empty
                                <div class="cd-pricing-row row g-3 align-items-center">
                                    <div class="col-md-6">
                                        <input class="form-control cd-form-input" name="fulltime_experience_label[]" value="0-2 Years" placeholder="e.g. 0-2 Years">
                                    </div>
                                    <div class="col-md-5">
                                        <input class="form-control cd-form-input" name="fulltime_experience_rate[]" value="8.33%" placeholder="e.g. 8.33%">
                                    </div>
                                    <div class="col-md-1 text-end">
                                        <button type="button" class="cd-btn-remove-row" title="Delete Row">
                                            <i class="fa fa-trash-can"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- Sub-Card C: Contract Staffing Commercial Defaults --}}
                    <div class="cd-commercial-section-card">
                        <div class="cd-commercial-section-header">
                            <div class="d-flex align-items-center gap-2">
                                <div class="cd-section-badge-icon" style="background: #fffbeb; color: #d97706;">
                                    <i class="fa fa-file-contract"></i>
                                </div>
                                <div>
                                    <div class="cd-commercial-section-title">Contract Staffing Commercial Defaults</div>
                                    <div class="text-muted fs-8">Monthly and hourly billing models, minimum duration, payment cycles, and replacement policies</div>
                                </div>
                            </div>
                            <span class="badge badge-light-warning fw-bold text-dark" style="font-size: 11px;">Contract ATS Scope</span>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="cd-form-label">Billing Model</label>
                                <select class="form-select cd-form-select" name="contract_billing_model">
                                    <option value="monthly" {{ (@$agencyCommercialDefaults->contract_billing_model ?: 'monthly') === 'monthly' ? 'selected' : '' }}>Monthly Rate</option>
                                    <option value="hourly" {{ @$agencyCommercialDefaults->contract_billing_model === 'hourly' ? 'selected' : '' }}>Hourly Rate</option>
                                </select>
                            </div>
                            <div class="col-md-5">
                                <label class="cd-form-label">Default Rate Display</label>
                                <input class="form-control cd-form-input" name="contract_rate_display" value="{{ @$agencyCommercialDefaults->contract_rate_display ?: 'Depend upon job requirements' }}">
                            </div>
                            <div class="col-md-2">
                                <label class="cd-form-label">Payment Terms</label>
                                <div class="input-group">
                                    <input class="form-control cd-form-input" type="number" name="contract_payment_terms_days" value="{{ @$agencyCommercialDefaults->contract_payment_terms_days ?? 30 }}">
                                    <span class="input-group-text bg-light text-muted border-start-0" style="font-size: 12px; border-color: #cbd5e1;">Days</span>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <label class="cd-form-label">Min Duration</label>
                                <div class="input-group">
                                    <input class="form-control cd-form-input" type="number" name="contract_min_duration_months" value="{{ @$agencyCommercialDefaults->contract_min_duration_months ?? 3 }}">
                                    <span class="input-group-text bg-light text-muted border-start-0" style="font-size: 12px; border-color: #cbd5e1;">Mos</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="cd-form-label">Billing Cycle</label>
                                <input class="form-control cd-form-input" name="contract_billing_cycle" value="{{ @$agencyCommercialDefaults->contract_billing_cycle ?: 'Monthly in Advance' }}">
                            </div>
                            <div class="col-md-6">
                                <label class="cd-form-label">Replacement Policy</label>
                                <input class="form-control cd-form-input" name="contract_replacement_policy" value="{{ @$agencyCommercialDefaults->contract_replacement_policy ?: 'As per Contract Terms' }}">
                                <input type="hidden" name="contract_rate_amount" value="{{ @$agencyCommercialDefaults->contract_rate_amount ?? 80000 }}">
                                <input type="hidden" name="contract_rate_currency" value="{{ @$agencyCommercialDefaults->contract_rate_currency ?? 4 }}">
                            </div>
                        </div>
                    </div>

                    {{-- Sub-Card D: Contract Experience-Based Pricing Matrix --}}
                    <div class="cd-commercial-section-card">
                        <div class="cd-commercial-section-header">
                            <div class="d-flex align-items-center gap-2">
                                <div class="cd-section-badge-icon" style="background: #ecfdf5; color: #059669;">
                                    <i class="fa fa-bars-staggered"></i>
                                </div>
                                <div>
                                    <div class="cd-commercial-section-title">Contract Experience-Based Pricing Schedule</div>
                                    <div class="text-muted fs-8">Contractor rates or commercial note matrix segmented by experience level</div>
                                </div>
                            </div>
                            <button type="button" class="cd-btn-add-row" id="btn_add_contract_row">
                                <i class="fa fa-plus fs-9"></i> Add Contract Tier
                            </button>
                        </div>

                        <div id="contract_experience_rows_wrap">
                            @php
                                $contractPricingRows = collect(@$agencyCommercialDefaults->contract_experience_pricing ?: [])->values();
                            @endphp
                            @forelse($contractPricingRows as $experienceRow)
                                <div class="cd-pricing-row row g-3 align-items-center">
                                    <div class="col-md-6">
                                        <label class="cd-form-label text-muted fs-9 d-md-none">Experience Band</label>
                                        <input class="form-control cd-form-input" name="contract_experience_label[]" value="{{ data_get($experienceRow, 'label') }}" placeholder="e.g. Senior Contractors (5+ Years)">
                                    </div>
                                    <div class="col-md-5">
                                        <label class="cd-form-label text-muted fs-9 d-md-none">Rate / Note</label>
                                        <input class="form-control cd-form-input" name="contract_experience_rate[]" value="{{ data_get($experienceRow, 'rate') }}" placeholder="e.g. Standard Monthly Markup">
                                    </div>
                                    <div class="col-md-1 text-end">
                                        <button type="button" class="cd-btn-remove-row" title="Delete Row">
                                            <i class="fa fa-trash-can"></i>
                                        </button>
                                    </div>
                                </div>
                            @empty
                                <div class="cd-pricing-row row g-3 align-items-center">
                                    <div class="col-md-6">
                                        <input class="form-control cd-form-input" name="contract_experience_label[]" value="Standard Contractor" placeholder="e.g. Standard Contractor">
                                    </div>
                                    <div class="col-md-5">
                                        <input class="form-control cd-form-input" name="contract_experience_rate[]" value="As per Job Requirement" placeholder="e.g. As per Job Requirement">
                                    </div>
                                    <div class="col-md-1 text-end">
                                        <button type="button" class="cd-btn-remove-row" title="Delete Row">
                                            <i class="fa fa-trash-can"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>

                </div>
            @else
                {{-- Standard Terms & Offer Letters (IDs 1–12): Master Enterprise Rich Editor --}}
                <div class="cd-editor-container-card">
                    <div class="cd-editor-card-header">
                        <div class="d-flex align-items-center gap-2">
                            <div class="cd-section-badge-icon">
                                <i class="fa fa-file-pen text-primary"></i>
                            </div>
                            <div>
                                <div class="cd-editor-card-title">Template Master Document Body</div>
                                <div class="text-muted fs-8">Rich formatted content stored centrally in MySQL <code>{{ $storageTable ?? 'global_settings' }}</code> &amp; delivered via ES indices</div>
                            </div>
                        </div>
                        <div class="cd-editor-counters">
                            <span class="cd-counter-pill" title="Character count">
                                <i class="fa fa-font text-muted me-1"></i>
                                <strong id="cd_char_count">{{ number_format($charCount ?? 0) }}</strong> characters
                            </span>
                            <span class="cd-counter-pill" title="Word count">
                                <i class="fa fa-file-word text-muted me-1"></i>
                                <strong id="cd_word_count">{{ number_format($wordCount ?? 0) }}</strong> words
                            </span>
                            <span class="cd-counter-pill text-success" id="cd_editor_status" title="Active Editor State">
                                <i class="fa fa-circle-check text-success me-1"></i> Authoring
                            </span>
                        </div>
                    </div>

                    <div class="cd-editor-wrapper">
                        <textarea id="editor1" name="editor1" rows="18">{{ (@$th_email_template->terms) ? @$th_email_template->terms : '' }}</textarea>
                    </div>
                </div>
            @endif

            <div class="cd-edit-bottom-strip">
                <div class="cd-strip-meta">
                    <span class="badge badge-light-secondary font-monospace text-dark px-3 py-2" style="font-size: 11px; border: 1px solid #e2e8f0;">
                        <i class="fa fa-database text-primary me-1"></i> Storage: <strong>{{ $storageTable ?? 'global_settings' }}</strong>
                    </span>
                    <span class="badge badge-light-secondary font-monospace text-dark px-3 py-2" style="font-size: 11px; border: 1px solid #e2e8f0;">
                        <i class="fa fa-code-branch text-info me-1"></i> Column: <code>{{ $dbField ?? 'terms' }}</code>
                    </span>
                    <span class="badge badge-light-success text-success px-3 py-2" style="font-size: 11px; border: 1px solid #bbf7d0;">
                        <i class="fa fa-shield-check text-success me-1"></i> Encrypted ID Verified
                    </span>
                    @if(isset($charCount) && $charCount > 0)
                        <span class="text-muted fs-8 d-none d-lg-inline">
                            <i class="fa fa-clock-rotate-left me-1"></i> Current Version: {{ number_format($charCount) }} chars
                        </span>
                    @endif
                </div>
                <div class="cd-strip-actions">
                    <a href="{{ route('superadmin.company.defaultTermsList') }}" class="cd-btn-cancel" title="Discard unsaved edits and return to listing">
                        <i class="fa fa-times"></i>
                        <span>Cancel</span>
                    </a>
                    <button type="button" id="save-form" class="cd-btn-save-primary" title="Save changes to MySQL database">
                        <i class="fa fa-save"></i>
                        <span>Save Changes</span>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('footer-script')
<script src="{{ asset('demo/ckeditor/ckeditor.js') }}"></script>
<script>
$(document).ready(function() {
    if (CKEDITOR.env.ie && CKEDITOR.env.version < 9) {
        CKEDITOR.tools.enableHtml5Elements(document);
    }
    CKEDITOR.config.height = 'auto';
    CKEDITOR.config.width = 'auto';

    if ($('#editor1').length && !$('#editor1').hasClass('d-none')) {
        var editorInstance = CKEDITOR.replace('editor1', {
            height: 480,
            toolbarGroups: [
                { name: 'clipboard', groups: ['clipboard', 'undo'] },
                { name: 'editing', groups: ['find', 'selection', 'spellchecker'] },
                { name: 'links' },
                { name: 'insert' },
                { name: 'forms' },
                { name: 'tools' },
                { name: 'document', groups: ['mode', 'document', 'doctools'] },
                { name: 'others' },
                { name: 'basicstyles', groups: ['basicstyles', 'cleanup'] },
                { name: 'paragraph', groups: ['list', 'indent', 'blocks', 'align', 'bidi'] },
                { name: 'styles' },
                { name: 'colors' }
            ]
        });

        editorInstance.on('change', function() {
            var data = editorInstance.getData();
            var tempDiv = document.createElement('div');
            tempDiv.innerHTML = data;
            var text = (tempDiv.textContent || tempDiv.innerText || '').trim();
            var charCount = text.length;
            var words = charCount > 0 ? text.split(/\s+/).filter(Boolean).length : 0;
            $('#cd_char_count').text(charCount.toLocaleString());
            $('#cd_word_count').text(words.toLocaleString());
        });
    }

    // Dynamic Row Handler for Full-Time Experience Tiers
    $('#btn_add_ft_row').click(function(e) {
        e.preventDefault();
        var template = '<div class="cd-pricing-row row g-3 align-items-center">' +
            '<div class="col-md-6">' +
                '<label class="cd-form-label text-muted fs-9 d-md-none">Experience Band</label>' +
                '<input class="form-control cd-form-input" name="fulltime_experience_label[]" placeholder="e.g. 5-8 Years">' +
            '</div>' +
            '<div class="col-md-5">' +
                '<label class="cd-form-label text-muted fs-9 d-md-none">Fee Rate</label>' +
                '<input class="form-control cd-form-input" name="fulltime_experience_rate[]" placeholder="e.g. 10.00%">' +
            '</div>' +
            '<div class="col-md-1 text-end">' +
                '<button type="button" class="cd-btn-remove-row" title="Delete Row">' +
                    '<i class="fa fa-trash-can"></i>' +
                '</button>' +
            '</div>' +
        '</div>';
        $('#ft_experience_rows_wrap').append(template);
    });

    // Dynamic Row Handler for Contract Experience Tiers
    $('#btn_add_contract_row').click(function(e) {
        e.preventDefault();
        var template = '<div class="cd-pricing-row row g-3 align-items-center">' +
            '<div class="col-md-6">' +
                '<label class="cd-form-label text-muted fs-9 d-md-none">Experience Band</label>' +
                '<input class="form-control cd-form-input" name="contract_experience_label[]" placeholder="e.g. Senior Contractors (5+ Years)">' +
            '</div>' +
            '<div class="col-md-5">' +
                '<label class="cd-form-label text-muted fs-9 d-md-none">Rate / Note</label>' +
                '<input class="form-control cd-form-input" name="contract_experience_rate[]" placeholder="e.g. Standard Monthly Markup">' +
            '</div>' +
            '<div class="col-md-1 text-end">' +
                '<button type="button" class="cd-btn-remove-row" title="Delete Row">' +
                    '<i class="fa fa-trash-can"></i>' +
                '</button>' +
            '</div>' +
        '</div>';
        $('#contract_experience_rows_wrap').append(template);
    });

    // Remove row delegate
    $(document).on('click', '.cd-btn-remove-row', function(e) {
        e.preventDefault();
        $(this).closest('.cd-pricing-row').remove();
    });

    // Tier 2: Click to insert dynamic token into CKEditor or copy to clipboard
    $(document).on('click', '.cd-token-btn', function(e) {
        e.preventDefault();
        var token = $(this).data('token');
        if (!token) return;

        var $btn = $(this);
        $btn.addClass('token-copied-pulse');
        setTimeout(function() {
            $btn.removeClass('token-copied-pulse');
        }, 500);

        if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances.editor1 && !$('#editor1').hasClass('d-none')) {
            var editor = CKEDITOR.instances.editor1;
            editor.insertText(token);
            editor.focus();

            if (typeof toastr !== 'undefined') {
                toastr.options = { "timeOut": 1500, "positionClass": "toast-bottom-right" };
                toastr.success('Inserted ' + token + ' at cursor', 'Token Inserted');
            }
        } else {
            if (navigator.clipboard) {
                navigator.clipboard.writeText(token);
            }
            if (typeof toastr !== 'undefined') {
                toastr.options = { "timeOut": 1500, "positionClass": "toast-bottom-right" };
                toastr.success('Copied ' + token + ' to clipboard', 'Token Copied');
            }
        }
    });

    $('#btn_quick_save_top').click(function(e) {
        e.preventDefault();
        $('#save-form').trigger('click');
    });

    $('#save-form').click(function() {
        var id = 0;
        var url = "{{ route('superadmin.company.addEditTermsupdate', ':id') }}";
        url = url.replace(':id', id);
        var token = "{{ csrf_token() }}";
        var purpose = $('#purpose').val();
        var encrypt_id = $('#encrypt_id').val();
        var editor1 = (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances.editor1) ? CKEDITOR.instances.editor1.getData() : $('#editor1').val();

        var requestData = {
            '_token': token,
            '_method': 'POST',
            'purpose': purpose,
            'editor1': editor1,
            'encrypt_id': encrypt_id
        };

        if ($('#agencyCommercialDefaultsFields').length) {
            requestData = $('#editSettings').serializeArray().reduce(function(payload, item) {
                if (payload[item.name] !== undefined) {
                    if (!Array.isArray(payload[item.name])) payload[item.name] = [payload[item.name]];
                    payload[item.name].push(item.value);
                } else {
                    payload[item.name] = item.value;
                }
                return payload;
            }, requestData);
        }

        var $btn = $(this);
        var $topBtn = $('#btn_quick_save_top');
        var $statusBadge = $('#cd_editor_status');
        var origHtml = $btn.html();
        var origTopHtml = $topBtn.html();

        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i> Saving...');
        $topBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i> Saving...');
        if ($statusBadge.length) {
            $statusBadge.removeClass('text-success text-danger').addClass('text-warning').html('<i class="fa fa-spinner fa-spin text-warning me-1"></i> Saving...');
        }

        $.ajax({
            type: 'POST',
            url: url,
            data: requestData,
            success: function(response) {
                $btn.prop('disabled', false).html(origHtml);
                $topBtn.prop('disabled', false).html(origTopHtml);

                if (response.status === "success") {
                    if ($statusBadge.length) {
                        $statusBadge.removeClass('text-warning text-danger').addClass('text-success').html('<i class="fa fa-circle-check text-success me-1"></i> Saved Just Now');
                    }
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Saved Successfully!',
                            text: 'Default term master copy has been saved to the database.',
                            showCancelButton: true,
                            confirmButtonText: '<i class="fa fa-arrow-left me-1"></i> Back to Terms List',
                            cancelButtonText: 'Stay on this page',
                            confirmButtonColor: '#009ef7',
                            cancelButtonColor: '#64748b'
                        }).then(function(result) {
                            if (result.isConfirmed) {
                                window.location.href = "{{ route('superadmin.company.defaultTermsList') }}";
                            }
                        });
                    } else if (typeof toastr !== 'undefined') {
                        toastr.success('Saved successfully!', 'Success');
                    } else {
                        alert('Saved successfully!');
                    }
                } else {
                    if ($statusBadge.length) {
                        $statusBadge.removeClass('text-warning text-success').addClass('text-danger').html('<i class="fa fa-circle-exclamation text-danger me-1"></i> Save Issue');
                    }
                    alert(response.message || 'An error occurred while saving.');
                }
            },
            error: function(xhr) {
                $btn.prop('disabled', false).html(origHtml);
                $topBtn.prop('disabled', false).html(origTopHtml);
                if ($statusBadge.length) {
                    $statusBadge.removeClass('text-warning text-success').addClass('text-danger').html('<i class="fa fa-circle-exclamation text-danger me-1"></i> Save Failed');
                }
                var errorMsg = 'An error occurred while saving.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Save Failed',
                        text: errorMsg,
                        confirmButtonColor: '#009ef7'
                    });
                } else {
                    alert(errorMsg);
                }
            }
        });
    });
});
</script>
@endpush
