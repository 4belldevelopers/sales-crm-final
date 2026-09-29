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

/* Currency dropdown in commercial rate fields */
.br-rate-field .ig {
    display: flex;
    position: relative;
    width: 100%;
}

.br-rate-field .ig input {
    flex: 1 1 auto;
    min-width: 0;
    min-height: 38px;
    border-radius: 6px 0 0 6px !important;
    border: 1px solid #d1d5db !important;
    border-right: none !important;
    padding: 8px 12px;
    font-size: 0.95rem;
    color: #0f172a;
    background: #ffffff;
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
}

.br-rate-field .ig input:focus {
    border-color: #3b82f6 !important;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15) !important;
    z-index: 2;
}

.ig-currency-box {
    position: relative;
    display: flex;
    align-items: center;
    background: #f8fafc;
    border: 1px solid #d1d5db;
    border-radius: 0 6px 6px 0;
    flex: 0 0 auto;
    transition: border-color 0.15s ease, background 0.15s ease;
}

.br-rate-field .ig:focus-within .ig-currency-box {
    border-color: #3b82f6;
}

.ig-currency-select {
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    background: transparent;
    border: none;
    outline: none;
    box-shadow: none;
    font-family: inherit;
    font-size: 13px;
    font-weight: 600;
    color: #1e293b;
    padding: 0 26px 0 12px;
    height: 100%;
    min-height: 38px;
    cursor: pointer;
    line-height: normal;
}

.ig-currency-chevron {
    position: absolute;
    right: 9px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 10px;
    color: #64748b;
    pointer-events: none;
}

.br-card.is-free-service .br-bill-panel .ig-currency-box {
    opacity: 0.6;
    background: #f1f5f9;
    cursor: not-allowed;
}

.br-card.is-free-service .br-bill-panel .ig-currency-select {
    cursor: not-allowed;
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
</style>
<link href="{{ asset('demo/css/filterStyle.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('demo/css/candidate-form.css') }}{{ TM_VERSION }}&bench_commercials=20260914&professional_profile=20260914&basic_availability=20260914&location_dropdown=20260914&location_typography=20260914&availability_date=20260914&fa6_fix=20260914&skills_header=20260914&location_enterprise=20260914&availability_picker=20260914&resume_summary_cards=20260914&relationship_manager_card=20260914&top_header=20260914&additional_details_card=20260914&enterprise_polish=20260914&professional_row_alignment=20260914&final_enterprise_polish=20260914&rm_summary_fix=20260914&availability_additional_fix=20260914&resume_note_visibility=20260915&create_card_order_icons=20260915&create_field_polish=20260915&create_global_phone_experience=20260915&commercial_state_colors=20260921&talent_stage_privacy=20260921&commercial_blue_green=20260921&availability_work_colors=20260921&skills_expertise=20260921&talent_stage_header=20260921&alignment_icon_comments=20260921&font_aliases=20260921&page_header_flow=20260921&talent_stage_skill_selects=20260921&sticky_compact_header=20260921&create_defaults_icons=20260921&bench_header_lavender=20260928" rel="stylesheet" type="text/css" />
