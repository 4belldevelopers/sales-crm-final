{{-- Phase 8 Frontend Optimization: Modular Form Styles Partial --}}
<style>
body { overflow-x: hidden; }
.dropzone { padding: 1.8rem 1.75rem !important; border: 2px dashed #e6e7e8 !important; background: transparent !important; text-align: inherit !important; }
.font-size-icon { font-size: 1.3em; }
.dashed-border-top { border-top: 1px dashed gray; }
.form { clear: both; }
.form--disabled input, .form--disabled textarea, .form--disabled select { padding-left: 0; padding-top: 4px; border: 0 solid #efefef; background: white; height: auto; transition: all 0.2s ease-in-out; font-size: 1.05rem; font-weight: 500; color: #3f4254; text-transform: capitalize; }
.enabled_input { display: block; width: 100%; border-radius: 2px; border: 1px solid #efefef; outline: none; -webkit-appearance: none; background-color: #f5f8fa; border-color: #f5f8fa; color: #5e6278; padding: 0.5em; transition: color 0.2s ease, background-color 0.2s ease; }
#calender::-webkit-inner-spin-button,
#calender::-webkit-calendar-picker-indicator { display: none; -webkit-appearance: none; }
#dob::-webkit-inner-spin-button,
#dob::-webkit-calendar-picker-indicator { display: none; -webkit-appearance: none; }
@media only screen and (min-width: 1024px) and (max-width: 1440px) {
  #kt_activitiesRecruiter { width: 1025px; }
}
@media only screen and (min-width: 1440px) and (max-width: 2560px) {
  #kt_activitiesRecruiter { width: 1200px; }
}
.tagify .tagify__tag div .tagify__tag-text { color: #0c0c0c !important; font-family: 'Poppins', sans-serif !important; font-weight: 500 !important; font-size: 13px !important; }
.tagify { height: 38px !important; font-family: 'Poppins', sans-serif !important; }
.disablesectionOn.setOpacity { cursor: not-allowed; background: #8a878d; opacity: 0.5; }

/* 15.6" Screen Optimization Scoped to Bench Candidates Module (Rule 17) */
#talent_create_app {
    max-width: 100%;
}

/* Equal Height Flex Pairing for Resume Upload and Career Summary */
#talent_create_app .equal-height-row {
    display: flex;
    flex-wrap: wrap;
}

#talent_create_app .equal-height-row > [class*='col-'] {
    display: flex;
    flex-direction: column;
}

#talent_create_app .equal-height-row .card {
    flex: 1 1 auto;
    margin-bottom: 1.25rem;
}

/* Dropzone Collapse: Completely Hide Upload Area When File Present */
#talent_create_app .dropzone.has-file,
.dropzone.has-file {
    display: none !important;
}

/* Single Unified Remove Action Badge */
#talent_create_app .uploaded-file-badge {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.75rem 1rem;
    background: #f4f6fa;
    border: 1px solid #e1e3ea;
    border-radius: 0.475rem;
}

/* 10% Vertical Height Compression on Enterprise Displays (1366x768 & 1920x1080) */
#talent_create_app .form-group,
#talent_create_app .fv-row {
    margin-bottom: 0.85rem !important;
}

#talent_create_app .form-control,
#talent_create_app .form-select {
    font-size: 13px !important;
    padding-top: 0.45rem !important;
    padding-bottom: 0.45rem !important;
    height: calc(1.5em + 0.9rem + 2px);
}

#talent_create_app .card-header {
    min-height: 48px !important;
    padding: 0.5rem 1.25rem !important;
}

#talent_create_app .card-body {
    padding: 1.25rem !important;
}

/* ==========================================================================
   Resume Uploaded View (Enterprise Green Success Banner & File Details UI)
   Matches attached reference design (media_1790666173035.png)
   ========================================================================== */
#talent_create_app .up-info,
.resume-card .up-info {
    display: none;
    margin-top: 0 !important;
}

#talent_create_app .up-info.show,
.resume-card .up-info.show {
    display: flex !important;
    flex-direction: column !important;
    flex: 1 1 auto !important;
    height: 100% !important;
    gap: 8px !important;
}

/* Hide dropzone & marketplace privacy note when file is uploaded */
.resume-card:has(.up-info.show) .ua,
.resume-card:has(.up-info.show) .resume-note,
#talent_create_app .resume-card:has(.up-info.show) .marketplace-privacy {
    display: none !important;
}

/* 1. Green Success Banner */
#talent_create_app .up-info-banner,
.resume-card .up-info-banner {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    padding: 7px 12px !important;
    background: #ecfdf5 !important;
    border: 1px solid #a7f3d0 !important;
    border-radius: 8px !important;
    margin-bottom: 0 !important;
    color: #065f46 !important;
    flex-shrink: 0 !important;
}

#talent_create_app .up-info-banner-left,
.resume-card .up-info-banner-left {
    display: flex;
    align-items: center;
    gap: 10px;
}

#talent_create_app .up-info-banner-check,
.resume-card .up-info-banner-check {
    width: 22px;
    height: 22px;
    border-radius: 50%;
    background: #10b981 !important;
    color: #fff !important;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    flex-shrink: 0;
}

#talent_create_app .up-info-banner-check i,
.resume-card .up-info-banner-check i {
    color: #fff !important;
    font-size: 10px !important;
}

#talent_create_app .up-info-banner-text,
.resume-card .up-info-banner-text {
    display: flex;
    flex-direction: column;
}

#talent_create_app .up-info-banner-text strong,
.resume-card .up-info-banner-text strong {
    font-size: 12px !important;
    font-weight: 700 !important;
    color: #047857 !important;
    line-height: 1.3;
}

#talent_create_app .up-info-banner-text small,
.resume-card .up-info-banner-text small {
    font-size: 10.5px !important;
    color: #4b5563 !important;
    line-height: 1.3;
    margin-top: 1px;
}

#talent_create_app .up-info-banner-close,
.resume-card .up-info-banner-close {
    background: transparent !important;
    border: 0 !important;
    color: #64748b !important;
    font-size: 15px !important;
    line-height: 1;
    padding: 4px;
    cursor: pointer;
    border-radius: 4px;
    transition: color .15s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

#talent_create_app .up-info-banner-close:hover,
.resume-card .up-info-banner-close:hover {
    color: #071434 !important;
}

/* 2. Uploaded File Row Card */
#talent_create_app .up-file-card,
.resume-card .up-file-card {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    padding: 8px 14px !important;
    background: #fff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 8px !important;
    margin-bottom: 0 !important;
    flex-shrink: 0 !important;
}

#talent_create_app .up-file-card-left,
.resume-card .up-file-card-left {
    display: flex;
    align-items: center;
    gap: 10px;
    min-width: 0;
}

#talent_create_app .up-file-badge,
.resume-card .up-file-badge {
    width: 36px;
    height: 36px;
    border-radius: 7px;
    background: #fee2e2 !important;
    color: #dc2626 !important;
    display: inline-flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

#talent_create_app .up-file-badge i,
.resume-card .up-file-badge i {
    font-size: 13px;
    line-height: 1;
    color: #dc2626 !important;
    margin-bottom: 1px;
}

#talent_create_app .up-file-badge-ext,
.resume-card .up-file-badge-ext {
    font-size: 8.5px;
    font-weight: 800;
    line-height: 1;
    letter-spacing: 0.5px;
    color: #dc2626 !important;
}

#talent_create_app .up-file-meta,
.resume-card .up-file-meta {
    flex: 1;
    min-width: 0;
}

#talent_create_app .up-file-name,
.resume-card .up-file-name {
    font-size: 13px !important;
    font-weight: 700 !important;
    color: #071434 !important;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    line-height: 1.3;
}

#talent_create_app .up-file-sub,
.resume-card .up-file-sub {
    font-size: 11px !important;
    color: #64748b !important;
    margin-top: 1px;
    line-height: 1.3;
}

#talent_create_app .up-file-actions,
.resume-card .up-file-actions {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-shrink: 0;
    margin-left: 14px;
}

#talent_create_app .up-btn,
.resume-card .up-btn {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 7px !important;
    font-size: 13px !important;
    font-weight: 600 !important;
    padding: 7px 16px !important;
    border-radius: 6px !important;
    cursor: pointer !important;
    transition: all .15s ease !important;
    font-family: inherit !important;
    line-height: 1.3 !important;
}

#talent_create_app .up-btn-remove,
.resume-card .up-btn-remove {
    background: #fff !important;
    border: 1px solid #fca5a5 !important;
    border-radius: 6px !important;
    color: #dc2626 !important;
    padding: 5px 12px !important;
    font-size: 12px !important;
    font-weight: 600 !important;
    cursor: pointer !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 6px !important;
    transition: all .15s ease !important;
}

#talent_create_app .up-btn-remove:hover,
.resume-card .up-btn-remove:hover {
    background: #fff1f2 !important;
    border-color: #f87171 !important;
}

#talent_create_app .up-btn-remove i,
.resume-card .up-btn-remove i {
    font-size: 12px !important;
    color: #dc2626 !important;
}

/* 3. Resume Details Card (2x2 Grid with Yellow Badges matching Image 2) */
#talent_create_app .up-details-card,
.resume-card .up-details-card {
    flex: 1 1 auto !important;
    display: flex !important;
    flex-direction: column !important;
    justify-content: center !important;
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 8px !important;
    padding: 10px 14px !important;
    margin-bottom: 0 !important;
}

#talent_create_app .up-details-grid,
.resume-card .up-details-grid {
    display: grid !important;
    grid-template-columns: 1fr 1fr !important;
    column-gap: 0 !important;
    row-gap: 8px !important;
    padding: 0 !important;
    margin: 0 !important;
}

#talent_create_app .up-detail-tile,
.resume-card .up-detail-tile {
    display: flex !important;
    align-items: center !important;
    gap: 10px !important;
    padding: 2px 10px 2px 0 !important;
}

#talent_create_app .up-details-grid > .up-detail-tile:nth-child(even),
.resume-card .up-details-grid > .up-detail-tile:nth-child(even) {
    border-left: 1px solid #edf2f7 !important;
    padding-left: 14px !important;
    padding-right: 0 !important;
}

#talent_create_app .up-detail-tile .up-detail-icon,
.resume-card .up-detail-tile .up-detail-icon {
    width: 32px !important;
    height: 32px !important;
    border-radius: 6px !important;
    background: #fff0a8 !important;
    color: #854d0e !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 14px !important;
    flex-shrink: 0 !important;
}

#talent_create_app .up-detail-tile .up-detail-icon i,
.resume-card .up-detail-tile .up-detail-icon i {
    color: #854d0e !important;
    font-size: 14px !important;
}

#talent_create_app .up-detail-lbl,
.resume-card .up-detail-lbl {
    font-size: 10.5px !important;
    font-weight: 500 !important;
    color: #64748b !important;
    line-height: 1.2 !important;
}

#talent_create_app .up-detail-val,
.resume-card .up-detail-val {
    font-size: 12px !important;
    font-weight: 700 !important;
    color: #071434 !important;
    margin-top: 1px !important;
    line-height: 1.3 !important;
}

#talent_create_app #upDetailParserStatus,
.resume-card #upDetailParserStatus {
    color: #059669 !important;
}

#talent_create_app #upDetailParserStatus span,
.resume-card #upDetailParserStatus span {
    font-size: 12px !important;
}

/* ==========================================================================
   Enterprise Required Fields Modal (Complete Required Fields)
   Source of Truth: media_1790668032103.png
   ========================================================================== */
.rf-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(15, 23, 42, 0.55);
    backdrop-filter: blur(3px);
    -webkit-backdrop-filter: blur(3px);
    z-index: 10500;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 16px;
    animation: rfFadeIn 0.18s ease-out;
}

@keyframes rfFadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes rfScaleIn {
    from { opacity: 0; transform: scale(0.96) translateY(8px); }
    to { opacity: 1; transform: scale(1) translateY(0); }
}

.rf-modal-box {
    background: #ffffff;
    border-radius: 16px;
    width: 100%;
    max-width: 460px;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    padding: 32px 30px 26px;
    position: relative;
    font-family: 'Poppins', sans-serif;
    animation: rfScaleIn 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    box-sizing: border-box;
}

.rf-modal-close {
    position: absolute;
    top: 16px;
    right: 16px;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #f1f5f9;
    border: none;
    color: #64748b;
    font-size: 14px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.15s ease;
    padding: 0;
}

.rf-modal-close:hover {
    background: #e2e8f0;
    color: #0f172a;
}

/* Warning circle at top center */
.rf-warning-icon-wrap {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    background: #fef9c3;
    border: 2px solid #facc15;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 16px;
}

.rf-warning-icon {
    font-size: 30px;
    font-weight: 700;
    color: #ca8a04;
    line-height: 1;
    font-family: 'Poppins', sans-serif;
}

/* Title & Description */
.rf-modal-title {
    font-size: 20px;
    font-weight: 700;
    color: #0f172a;
    text-align: center;
    margin: 0 0 8px 0;
    line-height: 1.25;
}

.rf-modal-desc {
    font-size: 13.5px;
    color: #64748b;
    text-align: center;
    line-height: 1.45;
    margin: 0 auto 22px;
    max-width: 360px;
}

/* Section Header */
.rf-section-header {
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.6px;
    color: #64748b;
    text-transform: uppercase;
    text-align: left;
    margin-bottom: 10px;
}

/* Interactive Field Rows */
.rf-fields-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
    max-height: 240px;
    overflow-y: auto;
    padding-right: 2px;
    margin-bottom: 12px;
}

.rf-field-row {
    background: #fef2f2;
    border: 1px solid #fee2e2;
    border-radius: 8px;
    padding: 10px 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    cursor: pointer;
    transition: all 0.15s ease;
    text-decoration: none;
    box-sizing: border-box;
}

.rf-field-row:hover {
    background: #fee2e2;
    border-color: #fca5a5;
    transform: translateY(-1px);
}

.rf-field-row-left {
    display: flex;
    align-items: center;
    gap: 12px;
    min-width: 0;
}

.rf-field-badge {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: #ef4444;
    color: #ffffff;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    font-weight: 800;
    flex-shrink: 0;
    line-height: 1;
}

.rf-field-name {
    font-size: 13.5px;
    font-weight: 600;
    color: #0f172a;
    line-height: 1.3;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.rf-field-chevron {
    color: #64748b;
    font-size: 12px;
    flex-shrink: 0;
    margin-left: 8px;
}

/* Helper text */
.rf-helper-text {
    font-size: 12px;
    color: #64748b;
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 20px;
    text-align: left;
}

.rf-helper-text i {
    font-size: 13px;
    color: #64748b;
}

/* Primary CTA button */
.rf-cta-btn {
    width: 100%;
    height: 44px;
    background: #1b84ff;
    color: #ffffff;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    font-family: 'Poppins', sans-serif;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    cursor: pointer;
    transition: background 0.15s ease, transform 0.1s ease;
}

.rf-cta-btn:hover {
    background: #1570ef;
}

.rf-cta-btn:active {
    transform: scale(0.99);
}

.rf-cta-btn i {
    font-size: 13px;
    transition: transform 0.15s ease;
}

.rf-cta-btn:hover i {
    transform: translateX(2px);
}

/* Temporary field attention highlight when navigated from modal */
.field-attention-highlight {
    outline: 2px solid #3b82f6 !important;
    box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.25) !important;
    border-radius: 6px !important;
    transition: all 0.2s ease-in-out !important;
    background-color: rgba(239, 246, 255, 0.4) !important;
}

/* Free Service Client Bill Rate Hover Notification & Locked State */
.br-rate-panel.br-bill-panel {
    position: relative;
}

.br-free-bill-hover-notice {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: #0f172a;
    color: #ffffff;
    padding: 14px 18px;
    border-radius: 12px;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.35), 0 8px 10px -6px rgba(0, 0, 0, 0.25);
    z-index: 60;
    max-width: 320px;
    width: 88%;
    text-align: left;
    pointer-events: none;
    backdrop-filter: blur(6px);
    border: 1px solid rgba(255, 255, 255, 0.12);
}

.br-free-bill-hover-inner {
    display: flex;
    align-items: flex-start;
    gap: 12px;
}

.br-free-bill-hover-inner i {
    color: #38bdf8;
    font-size: 18px;
    margin-top: 2px;
    flex-shrink: 0;
}

.br-free-bill-hover-text strong {
    display: block;
    font-size: 13px;
    font-weight: 600;
    color: #ffffff;
    margin-bottom: 4px;
    line-height: 1.25;
}

.br-free-bill-hover-text span {
    display: block;
    font-size: 11.5px;
    color: #94a3b8;
    line-height: 1.45;
}

.br-card.is-free-service .br-bill-panel {
    cursor: not-allowed;
    background: #f8fafc;
    border: 1.5px dashed #cbd5e1;
    position: relative;
    opacity: 0.85;
}

.br-card.is-free-service .br-bill-panel * {
    cursor: not-allowed;
}

.br-card.is-free-service .br-bill-panel:hover {
    border-color: #94a3b8;
    background: #f1f5f9;
}

/* ── Restored USD Conversion label (Approx $ X.XX) floating right on label ── */
.br-rate-field label {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    width: 100% !important;
    margin-bottom: 6px !important;
    font-size: 13px !important;
    font-weight: 600 !important;
    color: #1e293b !important;
}

.br-rate-field label .rq {
    margin-left: 2px !important;
    margin-right: auto !important;
    color: #ef4444 !important;
}

.br-rate-field label span[id$="_usd"],
label span[id$="_usd"] {
    float: right !important;
    margin-left: auto !important;
    color: #EF560A !important;
    font-size: 0.95rem !important;
    font-weight: 600 !important;
    background: transparent !important;
    border: none !important;
    padding: 0 !important;
    line-height: normal !important;
    white-space: nowrap !important;
}

/* ── br-panel-icon: Fix icon box sizing and alignment ── */
.br-panel-icon {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    flex: 0 0 auto !important;
    width: 40px !important;
    height: 40px !important;
    min-width: 40px !important;
    border-radius: 10px !important;
    font-size: 16px !important;
    line-height: 1 !important;
    overflow: hidden !important;
}
.br-panel-icon i {
    font-size: 15px !important;
    line-height: 1 !important;
    display: block !important;
}

/* ── Commercial Rate Input Group (.ig + .ig-a integrated seamless design) ── */
.br-rate-field .ig {
    display: flex !important;
    align-items: stretch !important;
    position: relative !important;
    width: 100% !important;
    gap: 0 !important;
}

.br-rate-field .ig input {
    flex: 1 1 auto !important;
    min-width: 0 !important;
    height: 38px !important;
    border-radius: 6px 0 0 6px !important;
    border: 1px solid #d1d5db !important;
    border-right: none !important;
    padding: 8px 12px !important;
    font-size: 0.95rem !important;
    color: #0f172a !important;
    background: #ffffff !important;
    box-sizing: border-box !important;
    margin: 0 !important;
    outline: none !important;
    transition: border-color 0.15s ease, box-shadow 0.15s ease !important;
}

.br-rate-field .ig input:focus {
    border-color: #3b82f6 !important;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15) !important;
    position: relative !important;
    z-index: 2 !important;
}

.br-rate-field .ig .ig-a,
.br-rate-field .ig .changeTextCurrency {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    flex: 0 0 auto !important;
    min-width: 54px !important;
    height: 38px !important;
    padding: 0 14px !important;
    background: #f8fafc !important;
    border: 1px solid #d1d5db !important;
    border-left: 1px solid #e2e8f0 !important;
    border-radius: 0 6px 6px 0 !important;
    font-size: 13px !important;
    font-weight: 600 !important;
    color: #475569 !important;
    white-space: nowrap !important;
    user-select: none !important;
    box-sizing: border-box !important;
    margin: 0 !important;
    transition: border-color 0.15s ease, background 0.15s ease, color 0.15s ease !important;
}

.br-rate-field .ig:focus-within .ig-a,
.br-rate-field .ig:focus-within .changeTextCurrency {
    border-color: #3b82f6 !important;
    background: #eff6ff !important;
    color: #1d4ed8 !important;
}

.br-card.is-free-service .br-bill-panel .ig input {
    background-color: #f1f5f9 !important;
    cursor: not-allowed !important;
}

.br-card.is-free-service .br-bill-panel .ig-a,
.br-card.is-free-service .br-bill-panel .changeTextCurrency {
    opacity: 0.6 !important;
    background: #f1f5f9 !important;
    cursor: not-allowed !important;
}

/* Margin Summary dynamic difference amount */
#br_summary_margin_diff.is-positive {
    color: #0AA65D !important;
}

#br_summary_margin_diff.is-negative {
    color: #E11D48 !important;
}

#br_summary_margin_diff.is-free,
#br_summary_margin_diff.is-neutral {
    color: #64748b !important;
    font-size: 0.95rem !important;
}

#br_summary_model {
    font-weight: 700 !important;
    color: #2563eb !important;
}

/* ── Redesigned Enterprise Relationship Manager Chip Card (media_1790683314858.png) ── */
.rm-chips-container {
    display: flex;
    flex-direction: column;
    gap: 10px;
    width: 100%;
    margin-top: 10px;
    margin-bottom: 8px;
}

.rm-chip-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #f8faff;
    border: 1px solid #bfdbfe;
    border-radius: 8px;
    padding: 10px 18px;
    min-height: 56px;
    transition: all 0.2s ease;
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
}

.rm-chip-card:hover {
    border-color: #93c5fd;
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.06);
    background: #ffffff;
}

.rm-chip-left {
    display: flex;
    align-items: center;
    gap: 12px;
    min-width: 190px;
}

.rm-chip-avatar {
    width: 38px;
    height: 38px;
    min-width: 38px;
    border-radius: 50%;
    background: #dbeafe;
    color: #1e40af;
    font-size: 13.5px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    text-transform: uppercase;
    flex-shrink: 0;
}

.rm-chip-identity {
    display: flex;
    flex-direction: column;
    min-width: 0;
}

.rm-chip-name {
    font-size: 13.5px;
    font-weight: 700;
    color: #0f172a;
    line-height: 1.25;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.rm-chip-sub {
    font-size: 11.5px;
    color: #64748b;
    line-height: 1.2;
    margin-top: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.rm-chip-divider {
    width: 1px;
    height: 28px;
    background: #e2e8f0;
    margin: 0 16px;
    flex-shrink: 0;
}

.rm-chip-col {
    display: flex;
    align-items: center;
    gap: 9px;
    flex: 1 1 auto;
    min-width: 0;
}

.rm-chip-icon {
    font-size: 13px;
    color: #64748b;
    flex-shrink: 0;
}

.rm-chip-text {
    font-size: 12.5px;
    color: #334155;
    font-weight: 500;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.rm-chip-actions {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-left: auto;
    flex-shrink: 0;
}

.rm-chip-badge-selected {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #dcfce7;
    color: #15803d;
    border-radius: 6px;
    padding: 5px 12px;
    font-size: 12px;
    font-weight: 600;
    line-height: 1;
}

.rm-chip-badge-selected i {
    color: #16a34a;
    font-size: 13px;
}

.rm-chip-btn-remove {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: transparent;
    border: none;
    outline: none;
    color: #ef4444;
    font-size: 12.5px;
    font-weight: 600;
    cursor: pointer;
    padding: 6px 10px;
    border-radius: 6px;
    transition: all 0.15s ease;
    font-family: inherit;
}

.rm-chip-btn-remove:hover {
    color: #dc2626;
    background: #fef2f2;
}

.rm-chip-btn-remove i {
    font-size: 12px;
}

/* Add another RM trigger button */
.rm-add-more-wrap {
    margin-top: 8px;
    margin-bottom: 6px;
}

.rm-add-more-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #ffffff;
    border: 1px dashed #93c5fd;
    color: #2563eb;
    border-radius: 6px;
    padding: 7px 14px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.15s ease;
    font-family: inherit;
}

.rm-add-more-btn:hover {
    background: #eff6ff;
    border-color: #3b82f6;
    color: #1d4ed8;
}

@media (max-width: 991px) {
    .rm-chip-card {
        flex-wrap: wrap;
        gap: 10px;
        padding: 12px 14px;
    }
    .rm-chip-divider {
        display: none;
    }
    .rm-chip-left {
        width: 100%;
    }
    .rm-chip-col {
        flex: 1 1 45%;
    }
    .rm-chip-actions {
        width: 100%;
        justify-content: flex-end;
        padding-top: 8px;
        border-top: 1px dashed #e2e8f0;
    }
}

/* ── Skills & Expertise: Minimum Skills Requirement Badge ── */
.se-badge--req {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 5px 13px;
    border-radius: 6px;
    background: #fffbeb;
    color: #92400e;
    border: 1px solid #fde68a;
    font-size: 11.5px;
    font-weight: 600;
    line-height: 1.4;
    letter-spacing: 0;
}
.se-badge--req i {
    color: #f59e0b;
    font-size: 12px;
}

/* ── Star Proficiency Rating: Highlight selected option in gold tones ── */
.se-card .sk select[name="ratings[]"] option {
    font-size: 13px;
    font-family: 'Poppins', sans-serif;
    color: #0B1545;
    font-weight: 500;
}
.se-card .sk select[name="ratings[]"] {
    color: #b45309;
    font-weight: 600;
}
.se-card .sk select[name="ratings[]"] option:first-child {
    color: #64748b;
    font-weight: 400;
}

/* ── Enterprise Validation Error Highlighting (media_1790669780356.png) ── */
.has-validation-error,
input.has-validation-error,
select.has-validation-error,
textarea.has-validation-error,
.form-control.has-validation-error,
.form-select.has-validation-error,
.tagify.has-validation-error,
.rm-picker.has-validation-error {
    border: 1.5px solid #ef4444 !important;
    background-color: #fef2f2 !important;
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.12) !important;
}

input.has-validation-error:focus,
select.has-validation-error:focus,
textarea.has-validation-error:focus,
.form-control.has-validation-error:focus,
.tagify.has-validation-error:focus-within,
.rm-picker.has-validation-error:focus-within {
    border-color: #dc2626 !important;
    background-color: #fef2f2 !important;
    box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.2) !important;
    outline: none !important;
}

.rm-picker.has-validation-error {
    border-radius: 8px !important;
}

#uploadBox.has-validation-error {
    border: 1.5px dashed #ef4444 !important;
    background-color: #fef2f2 !important;
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.12) !important;
    border-radius: 8px !important;
}

.aw-mode-grid.has-validation-error,
.wm-option-grid.has-validation-error {
    border: 1.5px solid #ef4444 !important;
    background-color: #fef2f2 !important;
    border-radius: 8px !important;
    padding: 6px !important;
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.12) !important;
}

.br-rate-field.has-validation-error .ig input {
    border: 1.5px solid #ef4444 !important;
    border-right: none !important;
    background-color: #fef2f2 !important;
}

.br-rate-field.has-validation-error .ig .ig-a,
.br-rate-field.has-validation-error .ig .changeTextCurrency {
    border: 1.5px solid #ef4444 !important;
    background-color: #fee2e2 !important;
    color: #dc2626 !important;
}

#skillExperienceWrapper tr.has-validation-error input,
#skillExperienceWrapper tr.has-validation-error select {
    border-color: #ef4444 !important;
    background-color: #fef2f2 !important;
}

.field-error-message {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-top: 6px;
    color: #ef4444;
    font-size: 12.5px;
    font-weight: 500;
    line-height: 1.4;
    animation: fieldErrorFadeIn 0.2s ease-in-out;
}

.field-error-message i {
    color: #ef4444;
    font-size: 13px;
    flex-shrink: 0;
}

.field-error-message span {
    color: #dc2626;
    font-size: 12.5px;
    font-weight: 500;
}

@keyframes fieldErrorFadeIn {
    from {
        opacity: 0;
        transform: translateY(-3px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}


/* ── Fix ITI phone flag floating over Gender label ── */
/* The .iti__flag-container is position:absolute — contain it strictly inside the phone field */
.bd-phone-control,
.fg.fv-row .bd-phone-control {
    position: relative;
    overflow: visible; /* allow ITI dropdown to show */
}
/* The ITI flag button must be contained within its own input wrapper */
.bd-phone-control .iti {
    position: relative;
    width: 100%;
    display: block;
}
/* Force the gender/dob row label area above the ITI phone widget */
.bd-grid--two .fg:first-child label,
.bd-grid--two .fg:last-child label {
    position: relative;
    z-index: 1;
    display: block;
}
/* Ensure ITI stays in its grid cell — do NOT bleed into sibling cells */
.bd-grid .bd-field:has(.iti__flag-container) {
    overflow: clip;
}

/* ── Clean Single Relationship Manager Search Box (Removes inner box-in-box) ── */
.rm-picker {
    display: flex !important;
    align-items: center !important;
    position: relative !important;
    width: 100% !important;
    min-height: 46px !important;
    height: 46px !important;
    padding: 0 16px !important;
    border: 1px solid #d8deec !important;
    border-radius: 8px !important;
    background: #ffffff !important;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04) !important;
    cursor: pointer !important;
    transition: border-color 0.15s ease, box-shadow 0.15s ease !important;
}
.rm-picker:hover,
.rm-picker:focus-within {
    border-color: #3b82f6 !important;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12) !important;
}
.rm-picker .rm-search-icon {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    color: #94a3b8 !important;
    font-size: 14px !important;
    margin-right: 10px !important;
    flex: 0 0 auto !important;
}
.rm-picker input#recruiter_display,
.rm-picker input[type="text"] {
    flex: 1 1 auto !important;
    min-width: 0 !important;
    border: 0 !important;
    border-radius: 0 !important;
    background: transparent !important;
    box-shadow: none !important;
    outline: none !important;
    padding: 0 !important;
    margin: 0 !important;
    height: auto !important;
    min-height: auto !important;
    font-size: 13.5px !important;
    color: #0f172a !important;
    cursor: pointer !important;
}
.rm-picker input#recruiter_display::placeholder {
    color: #94a3b8 !important;
    font-size: 13.5px !important;
    font-weight: 400 !important;
}
.rm-picker .rm-input-placeholder {
    display: none !important;
}
.rm-picker .rm-picker-divider {
    display: none !important;
}
.rm-picker .rm-picker-action {
    display: inline-flex !important;
    align-items: center !important;
    margin-left: auto !important;
    color: #2563eb !important;
    font-weight: 600 !important;
    font-size: 13px !important;
    white-space: nowrap !important;
    padding-left: 12px !important;
    cursor: pointer !important;
}
.rm-picker .rm-picker-chevron {
    display: none !important;
}

/* ── RM Drawer Sizing & Scrollability ── */
#kt_activitiesRecruiter {
    background: #ffffff !important;
}
#kt_activitiesRecruiter_body {
    height: calc(100vh - 65px) !important;
    overflow-y: auto !important;
    background: #ffffff !important;
}
#kt_activities_scroll {
    height: auto !important;
    min-height: 100% !important;
    overflow-y: visible !important;
}

/* ── Save to Bench Stage button — ensure it re-enables after error ── */
#save-form:not([data-kt-indicator="on"]) {
    pointer-events: auto !important;
    opacity: 1 !important;
}
#save-form[disabled]:not([data-kt-indicator="on"]) {
    pointer-events: auto !important;
}

/* ── Proficiency Rating Yellow Star Icons ── */
.se-enterprise-dropdown .fa-star,
.select2-results__option .fa-star,
.select2-selection__rendered .fa-star,
.se-field--select .fa-star {
    color: #f59e0b !important;
}

/* ── Categorized Highest Qualification & Nationality Dropdown Styling ── */
#nationality optgroup,
#qualification optgroup {
    font-weight: 700 !important;
    font-size: 11.5px !important;
    color: #1e40af !important;
    background: #f1f5f9 !important;
    padding: 7px 10px !important;
    letter-spacing: 0.25px;
}
#nationality option,
#qualification option {
    font-weight: 500 !important;
    font-size: 13px !important;
    color: #1e293b !important;
    background: #ffffff !important;
    padding: 6px 14px !important;
}

/* ── Adaptive Qualification & Nationality Select2 Styling ── */
.bd-control--select .select2-container {
    width: 100% !important;
    display: block !important;
}
.bd-control--select .select2-container .select2-selection--single {
    min-height: 42px !important;
    height: 42px !important;
    border: 1px solid #d8deec !important;
    border-radius: 6px !important;
    background-color: #ffffff !important;
    padding-left: 42px !important;
    padding-right: 28px !important;
    display: flex !important;
    align-items: center !important;
    box-shadow: none !important;
}
.bd-control--select .select2-container .select2-selection--single:focus,
.bd-control--select .select2-container--open .select2-selection--single {
    border-color: #3b82f6 !important;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12) !important;
}
.bd-control--select .select2-container .select2-selection__rendered {
    color: #0b1545 !important;
    font-size: 13.5px !important;
    font-weight: 500 !important;
    padding: 0 !important;
    line-height: normal !important;
}
.bd-control--select .select2-container .select2-selection__arrow {
    height: 40px !important;
    right: 12px !important;
}
.bd-control--select .select2-container .select2-selection__placeholder {
    color: #94a3b8 !important;
    font-weight: 400 !important;
}

/* Custom Dropdown Menu for Adaptive Select2 */
.jero-adaptive-dropdown {
    border: 1px solid #cbd5e1 !important;
    border-radius: 8px !important;
    box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.15), 0 8px 10px -6px rgba(15, 23, 42, 0.1) !important;
    z-index: 9999 !important;
}
.jero-adaptive-dropdown .select2-search--dropdown {
    padding: 8px !important;
    background: #f8fafc !important;
    border-bottom: 1px solid #e2e8f0 !important;
}
.jero-adaptive-dropdown .select2-search__field {
    border: 1px solid #cbd5e1 !important;
    border-radius: 6px !important;
    padding: 7px 10px !important;
    font-size: 13px !important;
    color: #0f172a !important;
    outline: none !important;
}
.jero-adaptive-dropdown .select2-search__field:focus {
    border-color: #3b82f6 !important;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.15) !important;
}
.jero-adaptive-dropdown .select2-results__group {
    font-weight: 700 !important;
    font-size: 11px !important;
    color: #1e40af !important;
    background: #f1f5f9 !important;
    padding: 7px 12px !important;
    border-top: 1px solid #e2e8f0;
    border-bottom: 1px solid #e2e8f0;
    letter-spacing: 0.3px;
}
.jero-adaptive-dropdown .select2-results__option {
    font-size: 13px !important;
    font-weight: 500 !important;
    color: #1e293b !important;
    padding: 7px 14px !important;
}
.jero-adaptive-dropdown .select2-results__option--highlighted {
    background-color: #eff6ff !important;
    color: #1d4ed8 !important;
}
.jero-adaptive-dropdown .select2-results__option[aria-selected="true"] {
    background-color: #e0e7ff !important;
    color: #312e81 !important;
    font-weight: 600 !important;
}
</style>
<link href="{{ asset('demo/css/filterStyle.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('demo/css/candidate-form.css') }}{{ TM_VERSION }}&bench_commercials=20260914&professional_profile=20260914&basic_availability=20260914&location_dropdown=20260914&location_typography=20260914&availability_date=20260914&fa6_fix=20260914&skills_header=20260914&location_enterprise=20260914&availability_picker=20260914&resume_summary_cards=20260914&relationship_manager_card=20260914&top_header=20260914&additional_details_card=20260914&enterprise_polish=20260914&professional_row_alignment=20260914&final_enterprise_polish=20260914&rm_summary_fix=20260914&availability_additional_fix=20260914&resume_note_visibility=20260915&create_card_order_icons=20260915&create_field_polish=20260915&create_global_phone_experience=20260915&commercial_state_colors=20260921&talent_stage_privacy=20260921&commercial_blue_green=20260921&availability_work_colors=20260921&skills_expertise=20260921&talent_stage_header=20260921&alignment_icon_comments=20260921&font_aliases=20260921&page_header_flow=20260921&talent_stage_skill_selects=20260921&sticky_compact_header=20260921&create_defaults_icons=20260921&bench_header_lavender=20260928" rel="stylesheet" type="text/css" />
