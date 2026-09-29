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
</style>
<link href="{{ asset('demo/css/filterStyle.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('demo/css/candidate-form.css') }}{{ TM_VERSION }}&bench_commercials=20260914&professional_profile=20260914&basic_availability=20260914&location_dropdown=20260914&location_typography=20260914&availability_date=20260914&fa6_fix=20260914&skills_header=20260914&location_enterprise=20260914&availability_picker=20260914&resume_summary_cards=20260914&relationship_manager_card=20260914&top_header=20260914&additional_details_card=20260914&enterprise_polish=20260914&professional_row_alignment=20260914&final_enterprise_polish=20260914&rm_summary_fix=20260914&availability_additional_fix=20260914&resume_note_visibility=20260915&create_card_order_icons=20260915&create_field_polish=20260915&create_global_phone_experience=20260915&commercial_state_colors=20260921&talent_stage_privacy=20260921&commercial_blue_green=20260921&availability_work_colors=20260921&skills_expertise=20260921&talent_stage_header=20260921&alignment_icon_comments=20260921&font_aliases=20260921&page_header_flow=20260921&talent_stage_skill_selects=20260921&sticky_compact_header=20260921&create_defaults_icons=20260921&bench_header_lavender=20260928" rel="stylesheet" type="text/css" />
