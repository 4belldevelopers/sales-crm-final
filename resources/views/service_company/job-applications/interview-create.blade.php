<?php
use App\Helper\GeneralMethod;
$defaultTimeZone = GeneralMethod::defaultTimeZone();
$currency_code =  GeneralMethod::defaultCurrency();
$country_code =  GeneralMethod::countryCodeFun();

$isdCompanyAddress = '';
if (!empty(auth()->user()->company)) {
    $comp = auth()->user()->company;
    $addrParts = array_filter([$comp->address ?? '', $comp->city ?? '', $comp->state ?? '', $comp->country ?? '']);
    if (!empty($addrParts)) {
        $isdCompanyAddress = implode(', ', $addrParts);
    }
}
if (empty($isdCompanyAddress)) {
    $isdCompanyAddress = '5th Floor, Orion Business Park, MG Road Ballia - 0, Uttar Pradesh - India';
}
?>
<style>
    .swal2-container {
        z-index: 2247483647 !important;
    }
    #kt_activitiesIntTeamtra {
        z-index: 99999999999 !important;
    }
    .tox-statusbar__branding {
        display: none
    }

    /* ===================================================================
       Interview Schedule drawer content — enterprise right-drawer layout.
       Scoped under .isd-wrap so it never leaks into the rest of the app.
       Real inputs/selects keep their original ids/names so every existing
       script (submitForm, getInterviewModeChild, getVideoMeetingUrl,
       accessEmailTemplate, the panel/contact-person modal) works unchanged.
       =================================================================== */
    .isd-wrap { font-family: 'Poppins', sans-serif; color: #181c32; background: #fbfbfd; }
    .isd-wrap * { font-family: 'Poppins', sans-serif; box-sizing: border-box; }

    .isd-body { display: flex; align-items: stretch; background: #fbfbfd; }
    .isd-main { flex: 1 1 66%; min-width: 0; padding: 18px 22px 18px; }
    .isd-main > .isd-card:last-of-type { margin-bottom: 0; }
    .isd-sidebar { flex: 0 0 34%; max-width: 380px; min-width: 310px; padding: 18px 18px 18px; border-left: 1px solid #eef0f4; background: #fff; }
    .isd-sidebar .isd-summary-group:last-child { margin-bottom: 0; }
    #add_team_member:not(.show) { display: none !important; }

    @media (max-width: 1200px) {
        .isd-body { flex-direction: column; }
        .isd-sidebar { flex: 1 1 100%; max-width: 100%; min-width: 0; border-left: none; border-top: 1px solid #eef0f4; }
    }

    .isd-card { background: #fff; border: 1px solid #eef0f4; border-radius: 12px; padding: 16px 18px; margin-bottom: 14px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); }
    .isd-sec-head { display: flex; align-items: center; gap: 12px; margin-bottom: 14px; }
    .isd-sec-num { width: 24px; height: 24px; border-radius: 50%; color: #fff; font-size: 11px; font-weight: 700; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .isd-sec-title { font-size: 14px; font-weight: 700; color: #181c32; line-height: 1.25; }
    .isd-sec-sub { font-size: 11px; color: #7e8299; font-weight: 400; margin-top: 2px; }

    /* Section 1: Grid */
    .isd-grid-details-4 { display: grid; grid-template-columns: 1.3fr 1.1fr 1.2fr 1fr; gap: 12px; }
    .isd-grid-details-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 12px; }
    @media (max-width: 1400px) {
        .isd-grid-details-4 { grid-template-columns: 1fr 1fr; }
    }
    @media (max-width: 768px) {
        .isd-grid-details-4 { grid-template-columns: 1fr; }
        .isd-grid-details-2 { grid-template-columns: 1fr; }
    }

    .isd-field label { display: block; font-size: 11px; font-weight: 600; color: #3f4254; margin-bottom: 6px; }
    .isd-field label .req { color: #f64e60; margin-left: 2px; }

    .isd-input-group { position: relative; display: flex; align-items: center; background: #f8f9fb; border: 1.5px solid #e4e6ef; border-radius: 8px; transition: all 0.15s ease; }
    .isd-input-group:focus-within { border-color: #3e97ff; background: #fff; box-shadow: 0 0 0 3px rgba(62,151,255,0.08); }
    .isd-input-group input, .isd-input-group select {
        width: 100%; border: none; background: transparent; font-size: 12px; color: #181c32;
        font-family: 'Poppins', sans-serif; font-weight: 500; padding: 8px 10px 8px 34px; outline: none;
    }
    .isd-input-group select { cursor: pointer; appearance: none; -webkit-appearance: none; padding-right: 28px; }
    .isd-ig-prefix { position: absolute; left: 10px; font-size: 14px; color: #7e8299; pointer-events: none; }
    .isd-ig-suffix { position: absolute; right: 10px; font-size: 13px; color: #a1a5b7; pointer-events: none; }

    .isd-readonly-group { background: #f1f1f4 !important; border-color: #e4e6ef !important; cursor: not-allowed; }
    .isd-readonly-input { color: #7e8299 !important; font-weight: 600 !important; cursor: not-allowed !important; padding-left: 14px !important; }

    /* Calendar row */
    .isd-cal-container { margin-top: 14px; background: #f8fafc; border: 1.5px solid #eef2f6; border-radius: 10px; padding: 12px 14px; }
    .isd-sublabel { font-size: 11px; font-weight: 600; color: #3f4254; margin-bottom: 8px; display: block; }
    .isd-cal-row { display: flex; gap: 10px; }
    @media (max-width: 900px) { .isd-cal-row { flex-direction: column; } }
    .isd-cal-btn {
        flex: 1; display: inline-flex; align-items: center; justify-content: center; gap: 8px;
        border: 1.5px solid #e4e6ef; border-radius: 8px; padding: 8px 12px; font-size: 11.5px;
        font-weight: 600; color: #3f4254; background: #fff; cursor: pointer; transition: all .15s ease;
    }
    .isd-cal-btn:hover { border-color: #3e97ff; color: #3e97ff; background: #f5f9ff; }

    /* Section 2: Mode Cards */
    .isd-mode-cards {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
        margin-bottom: 0;
    }
    .isd-mode-card {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 10px;
        min-height: 60px;
        border: 1.5px solid #e5e7eb;
        border-radius: 12px;
        background: #fff;
        cursor: pointer;
        position: relative;
        transition: all .15s ease;
        min-width: 0;
    }
    .isd-mode-card:hover { border-color: #cbd5e1; }
    .isd-mode-card.is-active {
        border-color: #10b981;
        background: #f0fdf4;
    }
    .isd-mode-ic {
        width: 36px;
        height: 36px;
        min-width: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .isd-mode-ic svg {
        width: 18px;
        height: 18px;
    }
    .isd-mode-ic-phone { background: #eff6ff; color: #0284c7; }
    .isd-mode-card.is-active .isd-mode-ic-phone { background: #dcfce7; color: #0284c7; }
    .isd-mode-ic-video { background: #dcfce7; color: #16a34a; }
    .isd-mode-ic-f2f   { background: #fee2e2; color: #dc2626; }
    .isd-mode-card-info {
        flex: 1 1 auto;
        min-width: 0;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .isd-mode-title {
        font-size: clamp(11.5px, 0.95vw, 13px);
        font-weight: 700;
        color: #111827;
        line-height: 1.2;
        white-space: nowrap;
        letter-spacing: -0.1px;
    }
    .isd-mode-card.is-active .isd-mode-title { color: #047857; }
    .isd-mode-sub {
        font-size: clamp(9.5px, 0.76vw, 10.5px);
        color: #6b7280;
        margin-top: 2px;
        line-height: 1.2;
        white-space: nowrap;
    }
    .isd-mode-radio {
        width: 16px;
        height: 16px;
        min-width: 16px;
        border-radius: 50%;
        border: 2px solid #cbd5e1;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        margin-left: auto;
        background: #fff;
        transition: all .15s ease;
    }
    .isd-mode-card.is-active .isd-mode-radio { border-color: #10b981; background: #10b981; }
    .isd-mode-card.is-active .isd-mode-radio::after {
        content: ''; width: 5px; height: 5px; border-radius: 50%; background: #fff; display: block;
    }

    /* Sub modes / Leaf section */
    .isd-leaf-section { margin-top: 16px; padding-top: 16px; border-top: 1px dashed #e5e7eb; }
    .isd-leaf-head { margin-bottom: 12px; }
    .isd-leaf-title { font-size: 13.5px; font-weight: 700; color: #111827; line-height: 1.3; }
    .isd-leaf-sub { font-size: 11px; color: #6b7280; margin-top: 2px; }

    /* Modern Sub-Cards (Used for Telephone, Video, and Face to Face) */
    .isd-sub-cards, .isd-loc-cards { display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; }
    .isd-sub-cards.cols-4 { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; }
    .isd-sub-cards.cols-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; }
    .isd-sub-card, .isd-loc-card {
        display: flex; align-items: center; gap: 10px; padding: 10px 14px; min-height: 64px;
        border: 1.5px solid #e5e7eb; border-radius: 12px; background: #fff;
        cursor: pointer; position: relative; transition: all .15s ease; min-width: 0;
    }
    .isd-sub-cards.cols-4 .isd-sub-card { padding: 9px 12px; gap: 8px; min-height: 60px; }
    .isd-sub-card:hover, .isd-loc-card:hover { border-color: #cbd5e1; }
    .isd-sub-card.is-selected, .isd-loc-card.is-selected {
        border-color: #10b981; background: #f0fdf4;
    }
    .isd-sub-ic, .isd-loc-ic {
        width: 38px; height: 38px; min-width: 38px; border-radius: 50%; display: flex;
        align-items: center; justify-content: center; flex-shrink: 0;
    }
    .isd-sub-cards.cols-4 .isd-sub-ic { width: 34px; height: 34px; min-width: 34px; }
    .isd-sub-ic-pin, .isd-loc-ic-pin { background: #ede9fe; color: #7c3aed; border-radius: 12px; }
    .isd-sub-ic-link, .isd-loc-ic-link { background: #eff6ff; color: #2563eb; border-radius: 50%; }
    .isd-sub-ic-phonecall { background: #10b981; color: #fff; border-radius: 12px; }
    .isd-sub-ic-meet { background: #fff; border-radius: 10px; border: 1px solid #eef2f6; box-shadow: 0 1px 3px rgba(0,0,0,0.04); }
    .isd-sub-ic-teams { background: #f3f0fc; color: #5059c9; border-radius: 10px; }
    .isd-sub-ic-zoom { background: transparent; border-radius: 50%; }

    .isd-sub-info, .isd-loc-info { flex: 1; min-width: 0; display: flex; flex-direction: column; justify-content: center; }
    .isd-sub-line1, .isd-loc-line1 {
        font-size: 12px; font-weight: 700; color: #111827; line-height: 1.35;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .isd-sub-line2, .isd-loc-line2 {
        font-size: 11px; font-weight: 600; color: #4b5563; line-height: 1.35;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-top: 2px;
    }
    .isd-sub-title, .isd-loc-title {
        font-size: clamp(12px, 0.9vw, 13.5px); font-weight: 700; color: #111827; line-height: 1.3;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .isd-sub-cards.cols-4 .isd-sub-title { font-size: 12px; }
    .isd-sub-card.is-selected .isd-sub-title, .isd-loc-card.is-selected .isd-loc-title { color: #047857; }
    .isd-sub-sub, .isd-loc-sub {
        font-size: clamp(10px, 0.78vw, 11px); color: #6b7280; margin-top: 2px;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .isd-sub-cards.cols-4 .isd-sub-sub { font-size: 10px; }

    .isd-sub-radio, .isd-loc-radio {
        width: 18px; height: 18px; min-width: 18px; border-radius: 50%; border: 2px solid #cbd5e1;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        margin-left: auto; background: #fff; transition: all .15s ease;
    }
    .isd-sub-cards.cols-4 .isd-sub-radio { width: 16px; height: 16px; min-width: 16px; }
    .isd-sub-card.is-selected .isd-sub-radio, .isd-loc-card.is-selected .isd-loc-radio {
        border-color: #10b981; background: #10b981;
    }
    .isd-sub-card.is-selected .isd-sub-radio::after, .isd-loc-card.is-selected .isd-loc-radio::after {
        content: ''; width: 5px; height: 5px; border-radius: 50%; background: #fff; display: block;
    }
    .isd-sub-cards.cols-4 .isd-sub-card.is-selected .isd-sub-radio::after { width: 4px; height: 4px; }

    /* Auto generation banner (kept hidden in favor of meeting link box) */
    .isd-auto-gen-banner {
        display: none !important;
    }

    /* Provider Result Area: Meeting Link Card */
    .isd-custom-address-card {
        margin-top: 14px; background: #f8fafc; border: 1.5px solid #e2e8f0;
        border-radius: 12px; padding: 12px 14px; position: relative; transition: all 0.2s ease;
    }
    .isd-custom-address-head {
        display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;
    }
    .isd-custom-address-label {
        font-size: 12px; font-weight: 700; color: #1e293b; margin: 0; display: flex; align-items: center; gap: 4px;
    }
    .isd-custom-address-badge {
        font-size: 10.5px; font-weight: 600; background: #eff6ff; color: #2563eb;
        padding: 3px 9px; border-radius: 6px; display: inline-flex; align-items: center; gap: 4px;
        border: 1px solid #dbeafe;
    }
    .isd-custom-input-group {
        position: relative; display: flex; align-items: center; background: #ffffff;
        border: 1.5px solid #e2e8f0; border-radius: 8px; transition: border-color .15s ease, box-shadow .15s ease;
    }
    .isd-custom-input-group:focus-within {
        border-color: #10b981; box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.12);
    }
    .isd-custom-input-prefix {
        position: absolute; left: 12px; display: flex; align-items: center; justify-content: center;
        pointer-events: none; color: #64748b;
    }
    .isd-custom-input {
        width: 100%; border: none; background: transparent; font-size: 12.5px;
        font-family: 'Poppins', sans-serif; color: #0f172a; padding: 9px 84px 9px 36px; outline: none;
    }
    .isd-custom-input[readonly] {
        cursor: default; color: #1e293b; font-weight: 500;
    }
    .isd-custom-input-actions {
        position: absolute; right: 6px; display: flex; align-items: center; gap: 4px;
    }
    .isd-custom-btn {
        width: 28px; height: 28px; border-radius: 6px; border: 1px solid #e2e8f0;
        background: #f8fafc; color: #64748b; display: flex; align-items: center;
        justify-content: center; cursor: pointer; font-size: 13px; transition: all .15s ease;
    }
    .isd-custom-btn:hover { background: #ffffff; color: #0f172a; border-color: #cbd5e1; }
    .isd-custom-address-help {
        font-size: 11px; color: #64748b; margin-top: 6px; display: flex; align-items: center; gap: 5px;
    }
    .isd-custom-address-help i { font-size: 13px; color: #94a3b8; }

    #url_div { display: none !important; }

    @media (max-width: 1366px) {
        .isd-mode-cards { gap: 8px; }
        .isd-mode-card { padding: 8px 8px; gap: 6px; min-height: 56px; }
        .isd-mode-ic { width: 32px; height: 32px; min-width: 32px; }
        .isd-mode-ic svg { width: 16px; height: 16px; }
        .isd-mode-title { font-size: 11.5px; }
        .isd-mode-sub { font-size: 9.5px; }
        .isd-sub-card, .isd-loc-card { padding: 8px 10px; min-height: 58px; gap: 8px; }
        .isd-sub-ic, .isd-loc-ic { width: 34px; height: 34px; min-width: 34px; }
        .isd-sub-title, .isd-loc-title { font-size: 12px; }
        .isd-sub-sub, .isd-loc-sub { font-size: 10px; }
    }
    @media (max-width: 1050px) {
        .isd-mode-cards { grid-template-columns: 1fr; gap: 8px; }
        .isd-sub-cards, .isd-loc-cards, .isd-sub-cards.cols-4, .isd-sub-cards.cols-3 { grid-template-columns: 1fr !important; }
    }

    /* Link row */
    .isd-link-row { position: relative; }
    .isd-link-row input { padding-right: 108px; }
    .isd-link-actions { position: absolute; right: 5px; top: 4px; display: flex; gap: 5px; }
    .isd-icon-btn { width: 30px; height: 30px; border-radius: 7px; border: 1px solid #e4e6ef; background: #fff; display: flex; align-items: center; justify-content: center; cursor: pointer; color: #7e8299; font-size: 14px; }
    .isd-icon-btn:hover { background: #f8f9fb; color: #3e97ff; }

    /* Section 3: Panel */
    .isd-btn-add-panel {
        border: 1px solid #f59e0b; color: #d97706; background: #fff; border-radius: 7px;
        padding: 6px 14px; font-size: 11.5px; font-weight: 600; cursor: pointer;
        white-space: nowrap; display: inline-flex; align-items: center; gap: 6px; margin-left: auto;
        transition: all .15s ease;
    }
    .isd-btn-add-panel:hover { background: #fffbeb; color: #b45309; border-color: #d97706; }
    .isd-panel-list { display: flex; flex-direction: column; gap: 8px; }
    .isd-panel-empty { font-size: 11.5px; color: #a1a5b7; text-align: center; padding: 12px 0; background: #f8fafc; border-radius: 8px; border: 1px dashed #e2e8f0; }
    .isd-panel-row { display: flex; align-items: center; gap: 12px; border: 1.5px solid #eef0f4; border-radius: 10px; padding: 10px 14px; background: #fff; transition: all .15s ease; }
    .isd-panel-row:hover { border-color: #cbd5e1; }
    .isd-avatar { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 13.5px; flex-shrink: 0; }
    .isd-panel-info { flex: 1; min-width: 0; }
    .isd-panel-name { font-size: 12.5px; font-weight: 700; color: #181c32; }
    .isd-panel-meta { font-size: 11px; color: #7e8299; display: flex; align-items: center; gap: 6px; margin-top: 2px; }
    .isd-dot-avail { width: 6px; height: 6px; border-radius: 50%; background: #10b981; display: inline-block; }
    .isd-badge-lead { background: #FEF3C7; color: #D97706; font-size: 10.5px; font-weight: 600; padding: 3px 8px; border-radius: 6px; margin-left: auto; flex-shrink: 0; }
    .isd-badge-member { background: #F1F5F9; color: #475569; font-size: 10.5px; font-weight: 600; padding: 3px 8px; border-radius: 6px; margin-left: auto; flex-shrink: 0; }
    .isd-panel-more { color: #a1a5b7; font-size: 14px; cursor: pointer; padding: 4px; border-radius: 4px; margin-left: 6px; }
    .isd-panel-more:hover { color: #181c32; background: #f1f1f4; }
    .isd-panel-del { width: 26px; height: 26px; border-radius: 7px; border: none; background: transparent; color: #7e8299; cursor: pointer; font-size: 14px; display: flex; align-items: center; justify-content: center; margin-left: 4px; }
    .isd-panel-del:hover { background: #fee2e2; color: #ef4444; }

    /* Section 4: Notifications */
    .isd-notify-grid-2x2 { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
    @media (max-width: 768px) { .isd-notify-grid-2x2 { grid-template-columns: 1fr; } }
    .isd-notify-card {
        display: flex; align-items: center; gap: 10px; border: 1.5px solid #eef0f4;
        border-radius: 10px; padding: 10px 12px; background: #fff; cursor: pointer;
        transition: all .15s ease;
    }
    .isd-notify-card:hover { border-color: #cbd5e1; }
    .isd-notify-ic { width: 34px; height: 34px; border-radius: 8px; background: #dcfce7; color: #16a34a; display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0; }
    .isd-notify-title { font-size: 11.5px; font-weight: 600; color: #181c32; line-height: 1.3; }
    .isd-notify-sub { font-size: 10px; color: #7e8299; line-height: 1.3; margin-top: 1px; }
    .isd-notify-card input[type="checkbox"] { width: 16px; height: 16px; accent-color: #10b981; border-radius: 4px; cursor: pointer; margin-left: auto; flex-shrink: 0; }

    /* Email customize banner */
    .isd-email-banner {
        margin-top: 12px; background: #fffbeb; border: 1px solid #fef3c7; border-radius: 10px;
        padding: 10px 14px; display: flex; align-items: center; justify-content: space-between;
        cursor: pointer; transition: all .15s ease;
    }
    .isd-email-banner:hover { background: #fef3c7; border-color: #fde68a; }
    .isd-email-banner-ic { color: #f59e0b; font-size: 18px; display: flex; align-items: center; }
    .isd-email-banner-title { font-size: 12px; font-weight: 700; color: #d97706; display: inline-block; margin-right: 8px; }
    .isd-email-banner-sub { font-size: 11px; color: #92400e; display: inline-block; }
    .isd-email-banner-arrow { color: #f59e0b; font-size: 15px; }

    /* Interview Summary (Right side) */
    .isd-sidebar-header { display: flex; align-items: center; gap: 10px; padding-bottom: 12px; border-bottom: 1px solid #f1f1f4; margin-bottom: 14px; }
    .isd-sidebar-ic { width: 34px; height: 34px; border-radius: 8px; background: #dcfce7; color: #16a34a; display: flex; align-items: center; justify-content: center; font-size: 17px; flex-shrink: 0; }
    .isd-sidebar-title { font-size: 13.5px; font-weight: 700; color: #181c32; }
    .isd-sidebar-sub { font-size: 11px; color: #7e8299; margin-top: 1px; }

    .isd-summary-group { margin-bottom: 16px; }
    .isd-summary-group-title { font-size: 11.5px; font-weight: 700; color: #181c32; margin-bottom: 8px; }
    .isd-sum-row-item { display: flex; align-items: center; justify-content: space-between; gap: 8px; padding: 4px 0; font-size: 11.5px; }
    .isd-sum-row-item .k { color: #7e8299; display: inline-flex; align-items: center; gap: 6px; font-size: 11.5px; }
    .isd-sum-row-item .k i { color: #10b981; font-size: 14px; width: 14px; }
    .isd-sum-row-item.k-teal .k i { color: #0d9488; }
    .isd-sum-row-item.k-gray .k i { color: #7e8299; }
    .isd-sum-row-item .v { color: #181c32; font-weight: 700; font-size: 11.5px; text-align: right; word-break: break-word; }
    .isd-sum-row-item .v.muted { color: #b5b5c3; font-weight: 500; }
    .isd-v-stacked { display: flex; flex-direction: column; align-items: flex-end; }
    .isd-v-stacked .v-sub { font-size: 10.5px; color: #9ca3af; font-weight: 500; }

    .isd-stage-pill {
        background: #FEF3C7; color: #D97706; border-radius: 12px; padding: 2px 9px;
        font-size: 10.5px; font-weight: 600; display: inline-flex; align-items: center; gap: 5px;
    }
    .isd-stage-dot { width: 6px; height: 6px; border-radius: 50%; background: #D97706; }

    .isd-sum-avatar {
        width: 20px; height: 20px; border-radius: 50%; display: inline-flex;
        align-items: center; justify-content: center; font-size: 9px; font-weight: 700;
    }

    .isd-notif-status { display: inline-flex; align-items: center; gap: 4px; font-size: 11px; font-weight: 600; }
    .isd-notif-status.enabled { color: #10b981; }
    .isd-notif-status.disabled { color: #9ca3af; font-weight: 500; }

    .isd-hidden-native { position: absolute; width: 1px; height: 1px; opacity: 0; pointer-events: none; overflow: hidden; }
    #url_div { display: none !important; }

    .isd-email-inline-head { font-size: 13px; font-weight: 700; color: #181c32; margin-bottom: 14px; }

    /* mm-* form design — matches mass_mail / fwd_resume modern style */
    .mm-wrap { font-family: 'Poppins', sans-serif; font-size: 13px; color: #374151; display: flex; flex-direction: column; height: 100%; background: #fff; border-radius: 12px; }
    .mm-form-body { flex: 1; }
    .mm-row { display: flex; align-items: flex-start; padding: 16px 24px; border-bottom: 1px solid #F3F4F6; gap: 16px; }
    .mm-label-col { width: 150px; flex-shrink: 0; display: flex; align-items: center; gap: 8px; padding-top: 9px; font-size: 12.5px; font-weight: 600; color: #374151; }
    .mm-label-icon { width: 28px; height: 28px; border-radius: 7px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .mm-label-icon svg { width: 14px; height: 14px; }
    .mm-ic-blue   { background: #EFF6FF; color: #2563EB; }
    .mm-ic-green  { background: #DCFCE7; color: #16A34A; }
    .mm-ic-purple { background: #F5F3FF; color: #7C3AED; }
    .mm-ic-teal   { background: #F0FDF4; color: #0D9488; }
    .mm-ic-orange { background: #FFF7ED; color: #EA580C; }
    .mm-req { color: #EF4444; margin-left: 1px; }
    .mm-field-col { flex: 1; min-width: 0; position: relative; }
    .mm-input { width: 100%; border: 1px solid #E5E7EB; border-radius: 8px; padding: 9px 14px; font-size: 13px; color: #374151; font-family: 'Poppins', sans-serif; background: #F9FAFB; outline: none; transition: border-color 0.15s; }
    .mm-input:focus { border-color: #93C5FD; background: #fff; }
    .mm-input[readonly] { color: #6B7280; cursor: default; background: #F3F4F6; }
    .mm-hint { font-size: 11.5px; color: #9CA3AF; margin-top: 5px; line-height: 1.4; }
    .mm-select { width: 100%; border: 1px solid #E5E7EB; border-radius: 8px; padding: 9px 36px 9px 14px; font-size: 13px; color: #374151; font-family: 'Poppins', sans-serif; background: #F9FAFB url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%239CA3AF' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E") no-repeat right 12px center; appearance: none; -webkit-appearance: none; outline: none; cursor: pointer; transition: border-color 0.15s; }
    .mm-select:focus { border-color: #93C5FD; background-color: #fff; }
    .mm-chips-box { display: flex; flex-wrap: wrap; align-items: center; gap: 6px; border: 1px solid #E5E7EB; border-radius: 8px; padding: 6px 10px; background: #F9FAFB; min-height: 40px; }
    .mm-chip { display: inline-flex; align-items: center; gap: 6px; background: #EFF6FF; border: 1px solid #DBEAFE; border-radius: 20px; padding: 3px 10px 3px 4px; font-size: 12.5px; color: #1E40AF; font-family: 'Poppins', sans-serif; }
    .mm-chip-av { width: 22px; height: 22px; border-radius: 50%; background: #DC2626; color: #fff; font-size: 10px; font-weight: 700; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .mm-chip-text { max-width: 260px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .mm-chip-x { background: none; border: none; padding: 0; margin-left: 2px; color: #93C5FD; cursor: default; font-size: 13px; line-height: 1; }
    .mm-add-email-btn { position: absolute; right: 0; top: 0; display: flex; align-items: center; gap: 5px; font-size: 12.5px; font-weight: 600; color: #2563EB; background: none; border: none; cursor: pointer; padding: 9px 14px; text-decoration: none; }
    .mm-add-email-btn:hover { color: #1d4ed8; }
    .mm-input-with-btn { padding-right: 110px; }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk="
    crossorigin="anonymous"></script>

<script src="{{ asset('demo/ckeditor/ckeditor.js') }}"></script>
<script>
    if (CKEDITOR.env.ie && CKEDITOR.env.version < 9)
        CKEDITOR.tools.enableHtml5Elements(document);
    CKEDITOR.config.height = 350;
    CKEDITOR.config.width = 'auto';

    var initSample = (function() {
        var wysiwygareaAvailable = isWysiwygareaAvailable(),
            isBBCodeBuiltIn = !!CKEDITOR.plugins.get('bbcode');

        return function() {
            var editorElement = CKEDITOR.document.getById('editor1');

            if (isBBCodeBuiltIn) {
                editorElement.setHtml(
                    'Hello world!\n\n' +
                    'I\'m an instance of [url=https://ckeditor.com]CKEditor[/url].'
                );
            }
            if (wysiwygareaAvailable) {
                CKEDITOR.replace('editor1');
            } else {
                editorElement.setAttribute('contenteditable', 'true');
                CKEDITOR.inline('editor1');
            }
        };

        function isWysiwygareaAvailable() {
            if (CKEDITOR.revision == ('%RE' + 'V%')) {
                return true;
            }
            return !!CKEDITOR.plugins.get('wysiwygarea');
        }

    })();

    initSample();
</script>

<script>
    function accessEmailTemplate(template_id, application_id) {

        var interviewers = $('#panellist').val();
        var videoUrl = $('#url').val();
        var interview_mode = $('#interview_mode').val();
        var jobId = $('#jobId').val();
        var interviewDate = $('#interviewDate').val();
        var interviewTime = $('#interviewTime').val();
        var timezone = $('#timezone').val();
        if (template_id > 0) {

            var id = template_id;
            var url = "{{ route('service_company.job-applications.accessEmailTemplate', ':id') }}";
            url = url.replace(':id', id);
            var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
            var token = "{{ csrf_token() }}";
            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    '_token': token,
                    '_method': 'POST',
                    'template_id': template_id,
                    'interviewDate': interviewDate,
                    'application_id': application_id,
                    'interviewers': interviewers,
                    'videoUrl': videoUrl,
                    'interview_mode': interview_mode,
                    'interviewTime': interviewTime,
                    'timezone': timezone,
                    'jobId': jobId
                },
                beforeSend: function() {
                    $('#childInterview').html('<img src="' + baseurl + '" height="35px" width="100%">');
                },
                success: function(response) {
                    $('#subject').val(response.template_subject);
                    $('#editor1').html(response.template_description);
                    CKEDITOR.instances.editor1.setData(response.template_description);
                    setTimeout(() => {
                        $('#editor1').val(response.template_description);
                    }, 500);
                },
                complete: function(data) {}
            });
        } else {
            $('#subject').val('');
            $('#editor').html('');
        }
    }
</script>

<form class="form fv-plugins-bootstrap fv-plugins-framework" id="app_interview_create_frm" name="app_interview_create_frm"
    method="post" enctype="multipart/form-data">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <input type="hidden" name="application_id" id="application_id" value="{{ @$application->id }}" />
    <input type="hidden" name="wiseId" id="wiseId" value="{{ @$application->wiseId }}" />
    <input type="hidden" name="jobId" id="jobId" value="{{ @$application->jobId }}" />
    <input type="hidden" name="master_status_id" id="master_status_id" value="{{ @$application->master_status_id }}" />
    <input type="hidden" name="child_status_id" id="child_status_id" value="{{ @$application->child_status_id }}" />

    @php
        $isdFullName = trim(@$application->full_name);

        // Candidate-proposed interview slots, captured during the candidate's own
        // application submission (job_applications.interviewDateOne..Six /
        // interview_mode_one..six) — real data, not tracked anywhere else.
        $isdAvailabilitySlots = [];
        for ($isdI = 1; $isdI <= 6; $isdI++) {
            $isdDateField = 'interviewDate' . ['One', 'Two', 'Three', 'Four', 'Five', 'Six'][$isdI - 1];
            $isdModeField = 'interview_mode_' . ['one', 'two', 'three', 'four', 'five', 'six'][$isdI - 1];
            $isdSlotDate = @$application->{$isdDateField};
            if (!empty($isdSlotDate)) {
                $isdAvailabilitySlots[] = [
                    'date' => $isdSlotDate,
                    'mode' => @$application->{$isdModeField},
                ];
            }
        }
    @endphp

    <div id="div_interview_schedule_first" class="isd-wrap">
        <div class="isd-body">
            <div class="isd-main">

                <!-- Section 1: Interview Details -->
                <div class="isd-sec isd-card">
                    <div class="isd-sec-head">
                        <span class="isd-sec-num" style="background:#F59E0B;">1</span>
                        <div>
                            <div class="isd-sec-title">Interview Details</div>
                            <div class="isd-sec-sub">When do you want to schedule this interview?</div>
                        </div>
                    </div>
                    <div class="isd-grid-details-4">
                        <div class="isd-field">
                            <label>Interview Date <span class="req">*</span></label>
                            <div class="isd-input-group">
                                <i class="ti ti-calendar isd-ig-prefix"></i>
                                <input name="interviewDate" id="interviewDate" type="date"
                                    min="{{ date('Y-m-d') }}"
                                    value="{{ ($datadate) ? \Carbon\Carbon::parse(@$datadate)->format('Y-m-d') : date('Y-m-d') }}">
                                <i class="ti ti-calendar-event isd-ig-suffix"></i>
                            </div>
                        </div>
                        <div class="isd-field">
                            <label>Start Time <span class="req">*</span></label>
                            <div class="isd-input-group">
                                <i class="ti ti-clock isd-ig-prefix"></i>
                                <input name="interviewTime" id="interviewTime" type="time"
                                    value="{{ ($datatime) ? \Carbon\Carbon::parse(@$datatime)->format('H:i') : '10:30' }}">
                                <i class="ti ti-chevron-down isd-ig-suffix"></i>
                            </div>
                        </div>
                        <div class="isd-field">
                            <label>Duration <span class="req">*</span></label>
                            <div class="isd-input-group">
                                <i class="ti ti-clock isd-ig-prefix"></i>
                                <select id="isdDuration">
                                    <option value="15">15 Minutes</option>
                                    <option value="30">30 Minutes</option>
                                    <option value="45" selected>45 Minutes</option>
                                    <option value="60">60 Minutes</option>
                                    <option value="90">90 Minutes</option>
                                </select>
                                <i class="ti ti-chevron-down isd-ig-suffix"></i>
                            </div>
                        </div>
                        <div class="isd-field">
                            <label>Ends At</label>
                            <div class="isd-input-group isd-readonly-group">
                                <input type="text" id="isdEndsAt" readonly value="11:15 AM" class="isd-readonly-input">
                            </div>
                        </div>
                    </div>

                    <div class="isd-grid-details-2">
                        <div class="isd-field">
                            <label>Timezone <span class="req">*</span></label>
                            <div class="isd-input-group">
                                <i class="ti ti-world isd-ig-prefix"></i>
                                <select name="timezone" id="timezone" aria-label="Select a Time Zone">
                                    <option value="">Select a timezone...</option>
                                    @if(!empty($timezone))
                                        <optgroup label="Popular Time Zones">
                                            @foreach($timezone as $tval)
                                                @if($tval->priority)
                                                    @php $sel = ($defaultTimeZone === $tval->time_zone || $defaultTimeZone === $tval->timezone_abbreviation) ? 'selected="selected"' : ''; @endphp
                                                    {!! '<option value="'.$tval->timezone_abbreviation.'" '.$sel.'>('.$tval->gmt_offse.') '.$tval->country_name.' - '.$tval->timezone_abbreviation.'</option>' !!}
                                                @endif
                                            @endforeach
                                        </optgroup>
                                        <optgroup label="All Time Zones">
                                            @foreach($timezone as $tval)
                                                @if(!$tval->priority)
                                                    @php $sel = ($defaultTimeZone === $tval->time_zone || $defaultTimeZone === $tval->timezone_abbreviation) ? 'selected="selected"' : ''; @endphp
                                                    {!! '<option value="'.$tval->timezone_abbreviation.'" '.$sel.'>('.$tval->gmt_offse.') '.$tval->country_name.' - '.$tval->timezone_abbreviation.'</option>' !!}
                                                @endif
                                            @endforeach
                                        </optgroup>
                                    @endif
                                </select>
                                <i class="ti ti-chevron-down isd-ig-suffix"></i>
                            </div>
                        </div>
                        <div class="isd-field">
                            <label>Interview Round <span class="req">*</span></label>
                            <div class="isd-input-group">
                                <i class="ti ti-layers-subtract isd-ig-prefix"></i>
                                <select id="isdInterviewRound">
                                    <option value="">Select Round</option>
                                    @if(!empty($interview_processes) && count($interview_processes))
                                        @foreach($interview_processes as $ipRow)
                                            <option value="{{ $ipRow->id }}" {{ strtolower($ipRow->name) == 'technical round' ? 'selected' : '' }}>{{ ucfirst($ipRow->name) }}</option>
                                        @endforeach
                                    @else
                                        @php
                                            $isdStaticRounds = [
                                                'Screening', 'Technical Round', 'Technical Round 1', 'Technical Round 2',
                                                'Hiring Manager Round', 'HR Round', 'Final Round',
                                            ];
                                        @endphp
                                        @foreach($isdStaticRounds as $isdRoundName)
                                            <option value="{{ $isdRoundName }}" {{ $isdRoundName == 'Technical Round' ? 'selected' : '' }}>{{ $isdRoundName }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <i class="ti ti-chevron-down isd-ig-suffix"></i>
                            </div>
                        </div>
                    </div>

                    <div class="isd-cal-container">
                        <label class="isd-sublabel">Add to Calendar</label>
                        <div class="isd-cal-row">
                            <button type="button" class="isd-cal-btn" id="isdIcsCalBtn">
                                <i class="ti ti-calendar-event text-success" style="font-size:16px;"></i>
                                <span>Add to Calendar (ICS)</span>
                            </button>
                            <button type="button" class="isd-cal-btn" id="isdGoogleCalBtn">
                                <svg width="16" height="16" viewBox="0 0 24 24"><path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.82-2.4 3.68v3.05h3.88c2.27-2.09 3.665-5.17 3.665-9.17z"/><path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.25v3.15C3.26 21.36 7.34 24 12 24z"/><path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.58H1.25C.45 8.18 0 9.98 0 12s.45 3.82 1.25 5.42l4.03-3.15z"/><path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.34 0 3.26 2.64 1.25 6.58l4.03 3.15c.95-2.83 3.6-4.98 6.72-4.98z"/></svg>
                                <span>Google Calendar</span>
                            </button>
                            <button type="button" class="isd-cal-btn" id="isdOutlookCalBtn">
                                <svg width="16" height="16" viewBox="0 0 24 24"><path fill="#0078D4" d="M14.5 2H22c.83 0 1.5.67 1.5 1.5v17c0 .83-.67 1.5-1.5 1.5h-7.5V2z"/><path fill="#106EBE" d="M14.5 22H2c-.83 0-1.5-.67-1.5-1.5V14h14v8z"/><path fill="#28A8EA" d="M14.5 2H2C1.17 2 .5 2.67.5 3.5V14h14V2z"/><circle cx="7" cy="11.5" r="3.5" fill="#fff"/></svg>
                                <span>Outlook Calendar</span>
                            </button>
                        </div>
                    </div>
                    <input type="hidden" class="btn-check" name="interview_type" value="1" id="fresh" {{ (@$reschedule == 1) ? '' : 'checked="checked"' }}>
                </div>

                <!-- Section 2: Interview Mode -->
                <div class="isd-sec isd-card" id="isdSectionMode" data-fallback-address="{{ $isdCompanyAddress }}">
                    <div class="isd-sec-head">
                        <span class="isd-sec-num" style="background:#10B981;">2</span>
                        <div>
                            <div class="isd-sec-title">Interview Mode</div>
                            <div class="isd-sec-sub">How will this interview happen?</div>
                        </div>
                    </div>

                    <div class="isd-mode-cards">
                        <div class="isd-mode-card" data-family-val="1">
                            <div class="isd-mode-ic isd-mode-ic-phone">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor">
                                    <path transform="scale(-1, 1) translate(-24, 0)" d="M20.01 15.38c-1.23 0-2.42-.2-3.53-.56a.977.977 0 0 0-1.01.24l-2.2 2.2a15.053 15.053 0 0 1-6.59-6.59l2.2-2.21a.96.96 0 0 0 .25-1A11.36 11.36 0 0 1 8.57 3.9c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1 0 9.39 7.61 17 17 17 .55 0 1-.45 1-1v-3.52c0-.55-.45-1-.99-1z"/>
                                </svg>
                            </div>
                            <div class="isd-mode-card-info">
                                <div class="isd-mode-title">Telephone</div>
                                <div class="isd-mode-sub">Phone interview</div>
                            </div>
                            <span class="isd-mode-radio"></span>
                        </div>

                        <div class="isd-mode-card is-active" data-family-val="2">
                            <div class="isd-mode-ic isd-mode-ic-video">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M17 10.5V7c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1v10c0 .55.45 1 1 1h12c.55 0 1-.45 1-1v-3.5l4 4v-11l-4 4z"/>
                                </svg>
                            </div>
                            <div class="isd-mode-card-info">
                                <div class="isd-mode-title">Video</div>
                                <div class="isd-mode-sub">Online meeting</div>
                            </div>
                            <span class="isd-mode-radio"></span>
                        </div>

                        <div class="isd-mode-card" data-family-val="3">
                            <div class="isd-mode-ic isd-mode-ic-f2f">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C7.58 2 4 5.58 4 10c0 5.25 8 12 8 12s8-6.75 8-12c0-4.42-3.58-8-8-8zm0 11c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3z"/>
                                </svg>
                            </div>
                            <div class="isd-mode-card-info">
                                <div class="isd-mode-title">Face to Face</div>
                                <div class="isd-mode-sub">In-person interview</div>
                            </div>
                            <span class="isd-mode-radio"></span>
                        </div>
                    </div>

                    <div id="isdLeafSection" class="isd-leaf-section" style="display:none;">
                        <div class="isd-leaf-head">
                            <div class="isd-leaf-title" id="isdLeafTitle">Choose Interview Location</div>
                            <div class="isd-leaf-sub" id="isdLeafSub">Select the interview location for face to face meeting</div>
                        </div>
                        <div class="isd-sub-cards" id="isdSubCards"></div>
                        <div class="isd-loc-cards" id="isdLocCards" style="display:none;"></div>
                        <div class="isd-tile-row" id="isdLeafTiles" style="display:none;"></div>
                        <div id="isdAutoGenBanner" class="isd-auto-gen-banner" style="display:none;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#6B7280" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path>
                                <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path>
                            </svg>
                            <span>Meeting link will be generated automatically</span>
                        </div>
                    </div>

                    <select name="interview_mode" id="interview_mode" onchange="getInterviewModeChild(this.value, {{ $job_id }});" class="isd-hidden-native">
                        <option value="">Select Interview Mode</option>
                        @if (!empty($interview_mode))
                            @foreach ($interview_mode as $int_mode_val)
                                <option value="{{ @$int_mode_val->id }}" {{ (@$datatmode == $int_mode_val->name || $int_mode_val->id == 2) ? 'selected' : '' }}>{{ @$int_mode_val->name }}</option>
                            @endforeach
                        @endif
                    </select>
                    <span id="childInterview" class="isd-hidden-native">
                        <select name="interview_mode_lavel" id="interview_mode_lavels" data-style="">
                            <option value="">Select mode</option>
                        </select>
                    </span>

                    <div class="row" style="display:none" id="url_div">
                        <div class="col-lg-12">
                            <label class="text-dark my-3 fw-bolder fs-5 d-flex justify-content-between align-items-center"><span id="lbl_call_mode">&nbsp;</span></label>
                            <input type="text" name="url" id="url" class="form-control form-control-solid">
                        </div>
                    </div>
                    <div class="isd-custom-address-card" id="isdMeetingLinkWrap" style="display:none;">
                        <div class="isd-custom-address-head">
                            <label class="isd-custom-address-label">
                                <span id="isdMeetingLinkLabel">Meeting URL / Address</span> <span class="req text-danger">*</span>
                            </label>
                            <span class="isd-custom-address-badge" id="isdCustomAddressBadge"><i class="ti ti-link"></i> Custom Link</span>
                        </div>
                        <div class="isd-custom-input-group">
                            <span class="isd-custom-input-prefix">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path>
                                    <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path>
                                </svg>
                            </span>
                            <input type="text" id="isdMeetingLinkDisplay" class="isd-custom-input" placeholder="https://...">
                            <div class="isd-custom-input-actions">
                                <span class="isd-custom-btn" title="Open Link" id="isdLinkOpen"><i class="ti ti-external-link"></i></span>
                                <span class="isd-custom-btn" title="Refresh" id="isdLinkRefresh"><i class="ti ti-refresh"></i></span>
                                <span class="isd-custom-btn" title="Copy Link" id="isdLinkCopy"><i class="ti ti-clipboard"></i></span>
                            </div>
                        </div>
                        <div class="isd-custom-address-help" id="isdCustomAddressHelp">
                            <i class="ti ti-info-circle"></i> <span>Enter the meeting link, location, or custom meeting address.</span>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Interview Panel -->
                <div class="isd-sec isd-card">
                    <div class="isd-sec-head">
                        <span class="isd-sec-num" style="background:#F59E0B;">3</span>
                        <div>
                            <div class="isd-sec-title">Interview Panel</div>
                            <div class="isd-sec-sub">Who will conduct the interview?</div>
                        </div>
                        <a href="javascript:;" class="isd-btn-add-panel in_tr_email_panel" data-panel="1"><i class="ti ti-plus"></i> Add Interview Panel</a>
                    </div>
                    <input class="isd-hidden-native" name="panellist" id="panellist" value="">
                    <div class="isd-panel-list" id="isdPanelList"></div>
                </div>

                <!-- Section 4: Notifications -->
                <div class="isd-sec isd-card">
                    <div class="isd-sec-head">
                        <span class="isd-sec-num" style="background:#0D9488;">4</span>
                        <div>
                            <div class="isd-sec-title">Notifications</div>
                            <div class="isd-sec-sub">Who should be notified?</div>
                        </div>
                    </div>
                    <div class="isd-notify-grid-2x2">
                        <label class="isd-notify-card">
                            <div class="isd-notify-ic"><i class="ti ti-mail"></i></div>
                            <div>
                                <div class="isd-notify-title">Send interview invitation to candidate</div>
                                <div class="isd-notify-sub">Automatically send email with meeting details</div>
                            </div>
                            <input type="checkbox" id="isdNotifyCandidate" checked>
                        </label>
                        <label class="isd-notify-card">
                            <div class="isd-notify-ic"><i class="ti ti-users"></i></div>
                            <div>
                                <div class="isd-notify-title">Notify interview panel</div>
                                <div class="isd-notify-sub">Send invitation to all selected interviewers</div>
                            </div>
                            <input type="checkbox" id="isdNotifyPanel" checked>
                        </label>
                        <label class="isd-notify-card">
                            <div class="isd-notify-ic"><i class="ti ti-brand-whatsapp"></i></div>
                            <div>
                                <div class="isd-notify-title">Send WhatsApp notification</div>
                                <div class="isd-notify-sub">Send meeting details via WhatsApp</div>
                            </div>
                            <input type="checkbox" id="isdNotifyWhatsapp">
                        </label>
                        <label class="isd-notify-card">
                            <div class="isd-notify-ic"><i class="ti ti-layout-grid"></i></div>
                            <div>
                                <div class="isd-notify-title">Add to candidate portal</div>
                                <div class="isd-notify-sub">Show interview details in candidate portal</div>
                            </div>
                            <input type="checkbox" id="isdNotifyPortal">
                        </label>
                    </div>

                    <div class="isd-email-banner" id="isdCustomizeEmailBanner">
                        <div class="d-flex align-items-center gap-3">
                            <div class="isd-email-banner-ic"><i class="ti ti-mail"></i></div>
                            <div>
                                <span class="isd-email-banner-title">Customize interview email</span>
                                <span class="isd-email-banner-sub">Edit email template, add custom message and meeting instructions</span>
                            </div>
                        </div>
                        <i class="ti ti-chevron-right isd-email-banner-arrow"></i>
                    </div>
                    <a href="javascript:void(0);" id="send_email_invies" style="display:none;"></a>
                </div>

                <input type="hidden" name="mailstatus" id="mailstatus" value="0">
                <input type="hidden" name="email_template_set" id="email_template_set" value="0">
            </div>

            <div class="isd-sidebar">
                <div class="isd-sidebar-header">
                    <div class="isd-sidebar-ic"><i class="ti ti-clipboard-list"></i></div>
                    <div>
                        <div class="isd-sidebar-title">Interview Summary</div>
                        <div class="isd-sidebar-sub">Review the details before scheduling</div>
                    </div>
                </div>

                <!-- Group 1: Candidate Information -->
                <div class="isd-summary-group">
                    <div class="isd-summary-group-title">Candidate Information</div>
                    <div class="isd-sum-row-item k-gray">
                        <span class="k"><i class="ti ti-user"></i> Candidate</span>
                        <span class="v" id="isdSumCandidate">{{ ucwords($isdFullName) }}</span>
                    </div>
                    <div class="isd-sum-row-item k-gray">
                        <span class="k"><i class="ti ti-file-text"></i> Job</span>
                        <div class="isd-v-stacked">
                            <span class="v" id="isdSumJob">{{ @$application->job_title ?: 'Position' }}</span>
                            <span class="v-sub">#J{{ @$application->jobId ?: (@$application->job_id ?: '1024') }}</span>
                        </div>
                    </div>
                    <div class="isd-sum-row-item k-gray">
                        <span class="k"><i class="ti ti-target"></i> Current Stage</span>
                        <span class="isd-stage-pill"><span class="isd-stage-dot"></span> {{ trim(@$application->master_status_name) ?: 'In Review' }}</span>
                    </div>
                </div>

                <hr style="border:none;border-top:1px dashed #f1f1f4;margin:12px 0;">

                <!-- Group 2: Schedule -->
                <div class="isd-summary-group">
                    <div class="isd-summary-group-title">Schedule</div>
                    <div class="isd-sum-row-item">
                        <span class="k"><i class="ti ti-calendar"></i> Date</span>
                        <span class="v" id="isdSumDate">{{ date('d M Y') }}</span>
                    </div>
                    <div class="isd-sum-row-item">
                        <span class="k"><i class="ti ti-clock"></i> Time</span>
                        <span class="v" id="isdSumTime">10:30 AM – 11:15 AM (45 mins)</span>
                    </div>
                    <div class="isd-sum-row-item">
                        <span class="k"><i class="ti ti-world"></i> Timezone</span>
                        <span class="v" id="isdSumTz">(UTC +05:30) IST</span>
                    </div>
                </div>

                <hr style="border:none;border-top:1px dashed #f1f1f4;margin:12px 0;">

                <!-- Group 3: Interview Details -->
                <div class="isd-summary-group">
                    <div class="isd-summary-group-title">Interview Details</div>
                    <div class="isd-sum-row-item">
                        <span class="k"><i class="ti ti-video"></i> Mode</span>
                        <span class="v" id="isdSumMode">Video Interview</span>
                    </div>
                    <div class="isd-sum-row-item">
                        <span class="k"><i class="ti ti-users"></i> Interview Panel</span>
                        <div class="d-flex align-items-center gap-1">
                            <span class="v" id="isdSumPanelCount">0 Interviewers</span>
                            <span id="isdSumPanelAvatars" class="d-flex align-items-center gap-1 ms-1"></span>
                        </div>
                    </div>
                    <div class="isd-sum-row-item">
                        <span class="k"><i class="ti ti-layers-subtract"></i> Interview Round</span>
                        <span class="v" id="isdSumRound">Technical Round</span>
                    </div>
                </div>

                <hr style="border:none;border-top:1px dashed #f1f1f4;margin:12px 0;">

                <!-- Group 4: Notifications -->
                <div class="isd-summary-group">
                    <div class="isd-summary-group-title">Notifications</div>
                    <div class="isd-sum-row-item k-teal">
                        <span class="k"><i class="ti ti-mail"></i> Candidate Invitation</span>
                        <span class="isd-notif-status enabled" id="isdSumNotifCandidate"><i class="ti ti-circle-check-filled"></i> Enabled</span>
                    </div>
                    <div class="isd-sum-row-item k-teal">
                        <span class="k"><i class="ti ti-users"></i> Interview Panel Notification</span>
                        <span class="isd-notif-status enabled" id="isdSumNotifPanel"><i class="ti ti-circle-check-filled"></i> Enabled</span>
                    </div>
                    <div class="isd-sum-row-item k-teal">
                        <span class="k"><i class="ti ti-brand-whatsapp"></i> WhatsApp Notification</span>
                        <span class="isd-notif-status disabled" id="isdSumNotifWhatsapp"><i class="ti ti-circle-x-filled"></i> Disabled</span>
                    </div>
                    <div class="isd-sum-row-item k-teal">
                        <span class="k"><i class="ti ti-layout-grid"></i> Candidate Portal</span>
                        <span class="isd-notif-status disabled" id="isdSumNotifPortal"><i class="ti ti-circle-x-filled"></i> Disabled</span>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div id="div_interview_schedule_second" class="isd-wrap" style="display:none">
        <div class="mm-wrap">
            <div class="mm-form-body">
                <div class="isd-email-inline-head" style="padding: 18px 24px 8px; font-size: 16px; font-weight: 700; color: #111827;">Send Email</div>

                {{-- From + Reply To --}}
                <div class="mm-row">
                    <div class="mm-label-col">
                        <span class="mm-label-icon mm-ic-blue">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m2 7 10 7 10-7"/></svg>
                        </span>
                        From
                    </div>
                    <div class="mm-field-col" style="display:flex;gap:12px;align-items:center;">
                        <select class="mm-select" name="from_email" id="from_email" style="flex:1;min-width:0;">
                            <option value="notify@jeroworks.com">{{ auth()->user()->name ?? 'Jero' }} &lt;notify@jeroworks.com&gt;</option>
                        </select>
                        <div style="display:flex;align-items:center;gap:6px;flex-shrink:0;font-size:12.5px;font-weight:600;color:#374151;">
                            <span class="mm-label-icon mm-ic-purple" style="width:24px;height:24px;border-radius:6px;display:inline-flex;align-items:center;justify-content:center;">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 17 4 12 9 7"/><path d="M20 18v-2a4 4 0 0 0-4-4H4"/></svg>
                            </span>
                            Reply To
                        </div>
                        <select class="mm-select" name="reply_to_email" id="reply_to_email" style="flex:1;min-width:0;">
                            <option value="{{ auth()->user()->email ?? 'notify@jeroworks.com' }}">{{ auth()->user()->email ?? 'notify@jeroworks.com' }}</option>
                        </select>
                    </div>
                </div>

                {{-- To Email ID (chip display) --}}
                <div class="mm-row">
                    <div class="mm-label-col">
                        <span class="mm-label-icon mm-ic-blue">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        </span>
                        To <span class="mm-req">*</span>
                    </div>
                    <div class="mm-field-col">
                        <input type="hidden" name="to_email" id="to_email" value="{{ @$application->email }}">
                        <div class="mm-chips-box">
                            @php
                                $cName = trim((string) @$application->full_name) ?: 'Candidate';
                                $cInit = strtoupper(substr($cName, 0, 1) ?: 'C');
                                $cEmail = @$application->email ?: '';
                            @endphp
                            <div class="mm-chip">
                                <span class="mm-chip-av">{{ $cInit }}</span>
                                <span class="mm-chip-text">{{ $cEmail }}</span>
                                <button type="button" class="mm-chip-x">&times;</button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CC Email ID --}}
                <div class="mm-row">
                    <div class="mm-label-col">
                        <span class="mm-label-icon mm-ic-green">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        </span>
                        CC Email ID
                    </div>
                    <div class="mm-field-col">
                        <div style="position:relative;">
                            <input class="mm-input mm-input-with-btn" name="cc_email" id="cc_email" value="" placeholder="Type an email..." maxlength="254" inputmode="email" autocomplete="email" autocapitalize="none" spellcheck="false">
                            <a href="javascript:;" class="mm-add-email-btn in_tr_email_panel" data-panel="0">+ Add Email</a>
                        </div>
                        <div id="cc_email_typo_hint" class="email-typo-hint-container mt-1" style="display: none; font-size: 11px;"></div>
                        <div class="mm-hint">Add email addresses separated by commas</div>
                    </div>
                </div>

                {{-- Template --}}
                <div class="mm-row">
                    <div class="mm-label-col">
                        <span class="mm-label-icon mm-ic-purple">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        </span>
                        Template
                    </div>
                    <div class="mm-field-col">
                        <select name="email_template" id="email_template" class="mm-select"
                            onchange="accessEmailTemplate(this.value, {{ @$application->id }} );">
                            <option value="">Select Email Template...</option>
                            @if (!empty($email_template))
                                @foreach ($email_template as $email_temp)
                                    @if ($email_temp->tempForInterview == 1 && $selfCompany == 1)
                                        <option value="{{ $email_temp->e_temp_id }}">
                                            {{ str_replace(['[{CLIENT_NAME}]', '[{COMPANY_NAME}]'], [@$clientTemp, @$companyTemp], $email_temp->display_name) }}
                                        </option>
                                    @endif
                                    @if ($selfCompany == '')
                                        <option value="{{ $email_temp->e_temp_id }}">
                                            {{ str_replace(['[{CLIENT_NAME}]', '[{COMPANY_NAME}]'], [@$clientTemp, @$companyTemp], $email_temp->display_name) }}
                                        </option>
                                    @endif
                                @endforeach
                            @endif
                        </select>
                        <div class="mm-hint">Choosing a template replaces the Subject and Body below</div>
                    </div>
                </div>

                {{-- Subject --}}
                <div class="mm-row">
                    <div class="mm-label-col">
                        <span class="mm-label-icon mm-ic-blue">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                        </span>
                        Subject <span class="mm-req">*</span>
                    </div>
                    <div class="mm-field-col">
                        <input type="text" name="subject" id="subject" placeholder="Enter email subject" class="mm-input">
                    </div>
                </div>

                {{-- Email Body --}}
                <div class="mm-row">
                    <div class="mm-label-col">
                        <span class="mm-label-icon mm-ic-green">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                        </span>
                        Email Body
                    </div>
                    <div class="mm-field-col">
                        <textarea id="editor1" name="editor1" class="mm-input" rows="10" placeholder="">&nbsp;</textarea>
                    </div>
                </div>

                {{-- Upload Files --}}
                <div class="mm-row">
                    <div class="mm-label-col">
                        <span class="mm-label-icon mm-ic-teal">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                        </span>
                        Upload Files
                    </div>
                    <div class="mm-field-col">
                        <input type="file" name="attachment" id="attachment" class="dropzone-select btn btn-sm btn-primary me-2" />
                        <div class="mm-hint">Max file size: 2MB</div>
                    </div>
                </div>

            </div>
            <!-- Back/Send Email live in the shared drawer topbar now -->
            <button type="button" class="divback isd-hidden-native"></button>
            <button type="button" id="sendEmailBtn" class="isd-hidden-native"></button>
        </div>
    </div>
</form>

<!--begin::Add Contact Person Modal-->
<div class="modal fade" tabindex="-1" aria-hidden="true" id="add_team_member" style="display: none !important;">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header" id="add_team_member_header">
                <h2 class="fw-bolder">Add Contact Person</h2>
                <div class="btn btn-icon btn-sm modal-close-team">
                    <span class="svg-icon svg-icon-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
                            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
                        </svg>
                    </span>
                </div>
            </div>
            <div class="modal-body scroll-y mx-5">
                <form id="add_team_member_form" name="add_team_member_form" class="form" action="#" method="post">
                    <meta name="csrf-token" content="{{ csrf_token() }}" />
                    @csrf
                    @method('POST')
                    <div class="row client-cperson-row">
                        <div class="row client-cperson-container" style="position: relative;">
                            <span class="form-text text-muted form-label fw-bolder countContact" style="position: absolute; top: 0;"></span>
                            <div class="col-xl-12">
                                <div class="form-group mb-0 mt-7 client-cperson-container-child fv-plugins-icon-container" id="remove">
                                    <input type="text" name="cp_name" id="cp_name_0" class="form-control form-control-lg form-control-solid rounded me-5" value="" placeholder="Enter person name">
                                    <span class="form-text text-muted">Please enter contact person name</span>
                                    <div class="fv-plugins-message-container"></div>
                                </div>
                            </div>
                            <div class="col-xl-12">
                                <div class="form-group mb-0 mt-7 client-cperson-container-child fv-plugins-icon-container" id="remove">
                                    <input type="email" name="cp_email" id="cp_email_0" class="form-control form-control-lg form-control-solid rounded me-5" value="" placeholder="Enter email" maxlength="254" inputmode="email" autocomplete="email" autocapitalize="none" spellcheck="false">
                                    <div id="cp_email_0_typo_hint" class="email-typo-hint-container mt-1" style="display: none; font-size: 11px;"></div>
                                    <span class="form-text text-muted">Please enter contact email</span>
                                    <div class="fv-plugins-message-container"></div>
                                </div>
                            </div>
                            <div class="col-xl-12">
                                <div class="form-group mb-0 mt-7 client-cperson-container-child fv-plugins-icon-container" id="remove">
                                    <input type="number" name="cp_number" id="cp_number_0" class="form-control form-control-lg form-control-solid rounded me-5" value="" placeholder="Enter contact number">
                                    <input type="hidden" value="{{ @$country_code }}" id="phone_country_code" name="phone_country_code">
                                    <span class="form-text text-muted">Please enter contact number</span>
                                    <div class="fv-plugins-message-container"></div>
                                </div>
                            </div>
                            <div class="col-xl-12">
                                <div class="form-group mb-0 mt-7 client-cperson-container-child fv-plugins-icon-container" id="remove">
                                    <input type="text" name="cp_desigination" id="cp_desigination_0" class="form-control form-control-lg form-control-solid rounded" value="" placeholder="Enter desigination" style="">
                                    <span class="form-text text-muted">Please enter desigination</span>
                                    <div class="fv-plugins-message-container"></div>
                                </div>
                            </div>
                            <input type="hidden" name="client_idMail" id="client_idMail" value="{{$application->client_id}}">
                            <input type="hidden" name="jobIdMail" id="jobIdMail" value="{{$application->jobId}}">
                            <input type="hidden" name="self_company" id="self_company" value="{{$application->self_company}}">
                            <input type="hidden" name="client_name" id="client_name" value="{{$application->client_name}}">
                            <input type="hidden" name="panelValue" id="panelValue" value="1">

                            <div class="text-center col-xl-12">
                                <button type="reset" class="btn btn-light me-3 modal-close-team">Close</button>
                                <button class="btn btn-primary " id="hiring_contact_person_add"> <span class="indicator-label">Submit</span> <span
                                        class="indicator-progress">Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span> </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!--end::Add  Contact Person popup-->

<script>
    // ══════════════════════════════════════════════════════════════════════════════
    // 100% CLIENT-FIRST GOD-LEVEL EMAIL VALIDATION ENGINE (ZERO SERVER STRAIN)
    // ══════════════════════════════════════════════════════════════════════════════
    window.JeroEmailBoxEngine = {
        domainTypos: {
            'gamil.com': 'gmail.com', 'gmal.com': 'gmail.com', 'gmaill.com': 'gmail.com', 'gmai.com': 'gmail.com',
            'gmial.com': 'gmail.com', 'gmaik.com': 'gmail.com', 'gamil.co': 'gmail.com', 'gmail.con': 'gmail.com',
            'gmail.co': 'gmail.com', 'gmaill.co': 'gmail.com', 'gmaill.con': 'gmail.com', 'gmail.cpm': 'gmail.com',
            'gmail.cm': 'gmail.com', 'gmail.om': 'gmail.com', 'g.mail.com': 'gmail.com',
            'hotmial.com': 'hotmail.com', 'hotmai.com': 'hotmail.com', 'hotmaill.com': 'hotmail.com',
            'hotmial.co': 'hotmail.com', 'hotmail.con': 'hotmail.com', 'hotmsil.com': 'hotmail.com',
            'yaho.com': 'yahoo.com', 'yahooo.com': 'yahoo.com', 'yaho.co': 'yahoo.com', 'yahoo.con': 'yahoo.com',
            'yaho.in': 'yahoo.in', 'yahooo.in': 'yahoo.in', 'yaho.co.in': 'yahoo.co.in',
            'outlok.com': 'outlook.com', 'outloo.com': 'outlook.com', 'outlook.con': 'outlook.com',
            'outlok.co': 'outlook.com', 'outlock.com': 'outlook.com',
            'iclud.com': 'icloud.com', 'icoud.com': 'icloud.com', 'icloud.con': 'icloud.com'
        },
        cleanDeterministic: function(val) {
            if (!val || typeof val !== 'string') return '';
            var cleaned = val.replace(/[\u00A0\u1680\u180E\u2000-\u200B\u202F\u205F\u3000\uFEFF]/g, ' ')
                             .trim()
                             .replace(/^mailto:\s*/i, '')
                             .replace(/^<+|>+$/g, '')
                             .replace(/^"+|"+$/g, '')
                             .replace(/^'+|'+$/g, '')
                             .replace(/[.,;:]+$/, '')
                             .trim();
            return cleaned;
        },
        validateSyntax: function(email) {
            if (!email || typeof email !== 'string') return false;
            var trimmed = email.trim();
            if (trimmed.length === 0 || trimmed.length > 254) return false;
            if ((trimmed.match(/@/g) || []).length !== 1) return false;
            var parts = trimmed.split('@');
            var local = parts[0];
            var domain = parts[1];
            if (!local || !domain) return false;
            if (local.length > 64) return false;
            if (local.startsWith('.') || local.endsWith('.') || local.indexOf('..') !== -1) return false;
            if (domain.startsWith('.') || domain.endsWith('.') || domain.startsWith('-') || domain.endsWith('-')) return false;
            if (domain.indexOf('..') !== -1) return false;
            var rfcPattern = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)+$/;
            return rfcPattern.test(trimmed);
        },
        suggestCorrection: function(email) {
            if (!email || typeof email !== 'string') return null;
            var raw = email.trim();
            if (raw.indexOf(',') !== -1) {
                raw = raw.replace(/,/g, '.');
            }
            var atMatches = raw.match(/@/g) || [];
            if (atMatches.length !== 1) return null;
            var parts = raw.split('@');
            var local = parts[0].trim();
            var domain = (parts[1] || '').trim().toLowerCase();
            if (!local || !domain) return null;
            if (this.domainTypos[domain]) {
                return local + '@' + this.domainTypos[domain];
            }
            var tldFixes = {
                'con': 'com', 'cpm': 'com', 'comm': 'com', 'cmo': 'com', 'ocm': 'com',
                'coom': 'com', 'ent': 'net', 'og': 'org', 'ogr': 'org'
            };
            var dParts = domain.split('.');
            if (dParts.length >= 2) {
                var last = dParts[dParts.length - 1];
                if (tldFixes[last]) {
                    dParts[dParts.length - 1] = tldFixes[last];
                    var suggestedDomain = dParts.join('.');
                    if (suggestedDomain !== domain) {
                        return local + '@' + suggestedDomain;
                    }
                }
            }
            return null;
        },
        renderTypoPill: function(containerSelector, suggestedEmail, targetInputSelector) {
            var $hint = $(containerSelector);
            if (!$hint.length) return;
            if (suggestedEmail) {
                $hint.html(
                    '<div class="d-inline-flex align-items-center bg-light-primary text-primary px-2 py-1 rounded fs-8 fw-semibold mt-1">' +
                        '<i class="ki-outline ki-information-2 fs-7 me-1"></i>' +
                        '<span>Did you mean <strong>' + $('<div>').text(suggestedEmail).html() + '</strong>?</span>' +
                        '<button type="button" class="btn btn-xs btn-link text-primary fw-bold p-0 ms-2 text-decoration-underline apply-email-fix" data-target="' + targetInputSelector + '" data-suggestion="' + $('<div>').text(suggestedEmail).html() + '">Apply</button>' +
                    '</div>'
                ).slideDown(150);
            } else {
                $hint.slideUp(100, function() { $(this).empty(); });
            }
        },
        setupEmailBox: function(inputSelector, hintSelector) {
            var self = this;
            var $input = $(inputSelector);
            if (!$input.length) return;

            $input.on('paste', function(e) {
                var pasted = (e.originalEvent || e).clipboardData ? (e.originalEvent || e).clipboardData.getData('text') : '';
                if (pasted) {
                    e.preventDefault();
                    var cleaned = self.cleanDeterministic(pasted);
                    var current = $input.val();
                    if ($input.is('#cc_email') && current) {
                        $input.val(current + ', ' + cleaned);
                    } else {
                        $input.val(cleaned);
                    }
                    $input.trigger('input').trigger('change');
                }
            });

            $input.on('input', function() {
                var val = $(this).val();
                var parts = val.split(/[,;]/);
                var lastPart = parts[parts.length - 1].trim();
                var suggestion = self.suggestCorrection(lastPart);
                if (suggestion && suggestion.toLowerCase() !== lastPart.toLowerCase()) {
                    self.renderTypoPill(hintSelector, suggestion, inputSelector);
                } else {
                    $(hintSelector).slideUp(100, function() { $(this).empty(); });
                }
            });

            $input.on('blur', function() {
                var val = $(this).val();
                if (val) {
                    var parts = val.split(/[,;]/).map(function(p) { return self.cleanDeterministic(p); }).filter(Boolean);
                    $(this).val(parts.join(', '));
                }
            });
        }
    };

    $(document).ready(function() {
        window.JeroEmailBoxEngine.setupEmailBox('#cc_email', '#cc_email_typo_hint');
        window.JeroEmailBoxEngine.setupEmailBox('#cp_email_0', '#cp_email_0_typo_hint');

        $(document).on('click', '.apply-email-fix', function(e) {
            e.preventDefault();
            var targetSel = $(this).attr('data-target');
            var suggestion = $(this).attr('data-suggestion');
            if (targetSel && suggestion) {
                var $tgt = $(targetSel);
                var val = $tgt.val();
                var parts = val.split(/[,;]/);
                if (parts.length > 1) {
                    parts[parts.length - 1] = ' ' + suggestion;
                    $tgt.val(parts.join(','));
                } else {
                    $tgt.val(suggestion);
                }
                $(this).closest('.email-typo-hint-container').slideUp(100, function() { $(this).empty(); });
                $tgt.trigger('input').trigger('change');
            }
        });

        if ($('#add_team_member').length && $('#add_team_member').parent().is('#app_interview_schedule_content, form, .isd-wrap')) {
            $('body > #add_team_member').remove();
            $('#add_team_member').appendTo('body');
        }

        let primaryPhoneInput = null;
        const phoneElem = document.querySelector("#cp_number_0");
        if (window.intlTelInput && phoneElem) {
            primaryPhoneInput = window.intlTelInput(phoneElem, {
                separateDialCode: true,
                initialCountry: "{{ $country_code }}",
                preferredCountries: ["in", "us", "gb", "ca", "au"],
            });
        }

        function updateCountryCode(inputField, hiddenField) {
            if (!inputField || typeof inputField.getSelectedCountryData !== 'function') return;
            const selectedCountryData = inputField.getSelectedCountryData();
            const countryCode = selectedCountryData.iso2;
            $(hiddenField).val(countryCode);
        }
        $("#cp_number_0").on("input change", function () {
            updateCountryCode(primaryPhoneInput, "#phone_country_code");
        });

        $(".iti__flag-container").on("click", function (event) {
            const targetInput = $(event.target).closest(".iti").find("input");
            if (targetInput.is("#cp_number_0")) {
                updateCountryCode(primaryPhoneInput, "#phone_country_code");
            }
        });

        setTimeout(function () {
            $(".iti__flag-container").click();
        },500);

        $(document).on('click', '#hiring_contact_person_add', function (e) {
            e.preventDefault();
            var client_id = $('#client_idMail').val();
            var cp_name_0 = $('#cp_name_0').val();
            var cp_email_0 = $('#cp_email_0').val();
            var cp_number_0 = $('#cp_number_0').val();
            var panel = $('#panelValue').val();
            var cp_desigination_0 = $('#cp_desigination_0').val();
            var phone_country_code = $('#phone_country_code').val();

            if (client_id > 0 && cp_name_0 !='' && cp_email_0 !='' && cp_desigination_0 !='') {
                var cleanedCpEmail = window.JeroEmailBoxEngine ? window.JeroEmailBoxEngine.cleanDeterministic(cp_email_0) : cp_email_0.trim();
                if (window.JeroEmailBoxEngine && !window.JeroEmailBoxEngine.validateSyntax(cleanedCpEmail)) {
                    var sugg = window.JeroEmailBoxEngine.suggestCorrection(cleanedCpEmail);
                    if (sugg) {
                        window.JeroEmailBoxEngine.renderTypoPill('#cp_email_0_typo_hint', sugg, '#cp_email_0');
                    }
                    Swal.fire({
                        title: 'Invalid Email Address',
                        text: 'Please enter a valid email address for the contact person.',
                        icon: 'warning'
                    });
                    $('#cp_email_0').focus();
                    return false;
                }
                cp_email_0 = cleanedCpEmail;

                $('.indicator-label').hide();
                $('#hiring_contact_person_add').attr('disabled',true);
                $('.indicator-progress').show();

                var token = "{{ csrf_token() }}";
                var id = client_id;
                var url = "{{ route('service_company.jobs.mail_contact_person', ':id') }}";
                url = url.replace(':id', id);
                var baseurl = "{{ asset('assets/images/Loading2.gif') }}";

                $.ajax({
                    type: 'POST',
                    url: url,
                    data: {
                        '_token': token,
                        'cp_name_0': cp_name_0,
                        'cp_email_0': cp_email_0,
                        'cp_number_0': cp_number_0,
                        'cp_desigination_0': cp_desigination_0,
                        'phone_country_code': phone_country_code,
                        'id': id
                    },
                    success: function(response) {
                        $('.indicator-label').show();
                        $('#hiring_contact_person_add').attr('disabled',false);
                        $('.indicator-progress').hide();
                        $('#Iteam_Table').prepend(response.view);

                        if(panel){
                            var hiring_namager = $('#cc_email').val();
                            if(hiring_namager==''){
                                $('#cc_email').val(cp_name_0);
                            }else{
                                $('#cc_email').val(hiring_namager+','+cp_name_0);
                            }
                            if (typeof isdRegisterPanelMeta === 'function') { isdRegisterPanelMeta(cp_name_0, cp_email_0, cp_desigination_0); }
                            isdSyncChipsFromField();
                        }else{
                            var hiring_namager2 = $('#cc_email').val();
                            if(hiring_namager2==''){
                                $('#cc_email').val(response.contact_person_name);
                            }else{
                                $('#cc_email').val(hiring_namager2+','+response.contact_person_name);
                            }
                        }
                        $('#add_team_member_form')[0].reset();
                        $('#add_team_member').modal('hide');
                    }
                });

            } else {
                alert("Please Enter the Conract Person Details after Add");
            }
        });

        $(document).on('click', '.modal-close-team', function () {
            $('#add_team_member').modal('hide');
        });

        var update = "{{ @$update }}";

        if (update) {
            var scheduledate = "{{ @$scheduledate }}";
            if (scheduledate) {
                // scheduledate is "Y-m-d H:i:s" from the DB; native date/time
                // inputs need it split into their own ISO pieces.
                $('#interviewDate').val(scheduledate.substring(0, 10));
                $('#interviewTime').val(scheduledate.substring(11, 16));
            }
            var interviewmode = "{{ @$interviewmode }}";
            $('#interview_mode').val(interviewmode).change();
            var interviewmodelavel = "{{ @$interviewmodelavel }}";
            setTimeout(() => {
                if (interviewmode == 2) {
                    $('#interview_mode_lavels').val(6).change();
                } else {
                    $('#interview_mode_lavels').val(interviewmodelavel).change();
                }
            }, 500);

            var emailtemplateset = "{{ @$emailtemplateset }}";
            if (emailtemplateset != 0) {
                $('#email_template').val(emailtemplateset).change();
            }

            var interviewers = "{{ @$interviewers }}";
            $('#panellist').val(interviewers);
            isdSyncChipsFromField();
            var interviewmentionlink = "{{ @$interviewmentionlink }}";
            setTimeout(() => {
                $('#url').val(interviewmentionlink);
            }, 500);
        }

        $('body').on('click', '#send_email_invies', function() {
            var interviewDate = $('#interviewDate').val();
            var interview_mode = $('#interview_mode').val();
            var interviewTime = $('#interviewTime').val();
            var timezone = $('#timezone').val();
            var interview_mode_lavels = $('#interview_mode_lavels').val();
            if (interview_mode == '') {
                Swal.fire("Required", "Interview Mode is required", "warning")
                return false;
            }
            if (timezone == '') {
                Swal.fire("Required", "Interview Time Zone is required", "warning")
                return false;
            }
            if (interview_mode_lavels == '') {
                Swal.fire("Required", "Interview Sub Mode is required", "warning")
                return false;
            }
            if (interviewDate == '') {
                Swal.fire("Required", "Date is required", "warning")
                return false;
            }
            if (interviewTime == '') {
                Swal.fire("Required", "Time is required", "warning")
                return false;
            }

            $('#div_interview_schedule_first').hide();
            $('#div_interview_schedule_second').show();
            window.isdShellSetStep('email');
        });
        $('body').on('click', '.divback', function() {
            $('#div_interview_schedule_first').show();
            $('#div_interview_schedule_second').hide();
            window.isdShellSetStep('main');
        });
    });

    // Called directly by this file's own Save button and, via a global
    // reference, by the shared drawer shell's "Schedule Interview" button in
    // service_company_layout.blade.php — assigning to window explicitly here
    // removes any doubt about scope now that it's invoked cross-file.
    window.submitForm = function submitForm() {

        var interviewDate = $('#interviewDate').val();
        var interview_mode = $('#interview_mode').val();
        var interviewTime = $('#interviewTime').val();
        var timezone = $('#timezone').val();
        var interview_mode_lavels = $('#interview_mode_lavels').val();
        if (interview_mode == '') {
            Swal.fire("Required", "Interview Mode is required", "warning")
            return false;
        }
        if(interview_mode_lavels == ''){
            Swal.fire("Required", "Interview Sub Mode is required", "warning")
            return false;
        }
        if(interviewDate == ''){
            Swal.fire("Required", "Date is required", "warning")
            return false;
        }
        if(interviewTime == ''){
            Swal.fire("Required", "Time is required", "warning")
            return false;
        }
        if (timezone == '') {
            Swal.fire("Required", "Interview Time Zone is required", "warning")
            return false;
        }

        $("#interview_schedule_drawer_primary").prop("disabled", true);
        $('#interview_schedule_drawer_primary .indicator-label').hide();
        $('#interview_schedule_drawer_primary .indicator-progress').show();

        $.ajax({
            url: '{{ route('service_company.job-applications.store-schedule') }}',
            container: '#app_interview_create_frm',
            type: "POST",
            data: $('#app_interview_create_frm').serialize(),
            success: function(response) {
                if (response.status == 'success') {
                    Swal.fire("Success", "Interview schedule successfully!", "success").then(function() {
                        $('#interview_schedule_drawer_close').trigger('click');
                        location.reload();
                    });
                }
                if (response.status == 'coinCheck') {
                    Swal.fire("Success", ` ${response.message}`, "warning").then(function() {
                        $('#interview_schedule_drawer_close').trigger('click');
                    });
                }
            },
            complete: function() {
                $("#interview_schedule_drawer_primary").prop("disabled", false);
                $('#interview_schedule_drawer_primary .indicator-label').show();
                $('#interview_schedule_drawer_primary .indicator-progress').hide();
            }
        })
    }

    $(document).ready(function() {
        $(document).on('click', '#sendEmailBtn', function(e) {
            e.preventDefault();
            var ccEmailVal = ($('#cc_email').val() || '').trim();
            if (ccEmailVal && window.JeroEmailBoxEngine) {
                var ccParts = ccEmailVal.split(/[,;]/).map(function(s) { return window.JeroEmailBoxEngine.cleanDeterministic(s); }).filter(Boolean);
                for (var ci = 0; ci < ccParts.length; ci++) {
                    if (!window.JeroEmailBoxEngine.validateSyntax(ccParts[ci])) {
                        var sugg = window.JeroEmailBoxEngine.suggestCorrection(ccParts[ci]);
                        if (sugg) {
                            window.JeroEmailBoxEngine.renderTypoPill('#cc_email_typo_hint', sugg, '#cc_email');
                        }
                        Swal.fire({
                            title: 'Invalid CC Email',
                            text: 'The CC email address "' + ccParts[ci] + '" is invalid. Please enter a valid email address.',
                            icon: 'warning'
                        });
                        $('#cc_email').focus();
                        return false;
                    }
                }
            }
            $(this).prop('disabled', true);
            var formData = new FormData($('#app_interview_create_frm')[0]);
            $('.indicator-label').hide();
            $('.indicator-progress').show();
            $.ajax({
                url: '{{ route('service_company.job-applications.emailschedule') }}',
                container: '#app_interview_create_frm',
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                redirect: true,
                success: function(response) {
                    $('.indicator-label').show();
                    $('.indicator-progress').hide();
                    if (response.status == 'faild') {
                        Swal.fire("Warning", "The file must not be larger than 2 MB ", "warning").then(function() {});
                    }
                    if (response.status == 'success') {
                        $('#mailstatus').val(1);
                        var email_template = $('#email_template').val();
                        $('#email_template_set').val(email_template);
                        var savesend = "{{ @$savesend }}";
                        if (savesend) {
                            submitForm();
                        } else {
                            Swal.fire("Success", "Interview schedule successfully!", "success").then(function() {
                                $('#interview_schedule_drawer_close').trigger('click');
                            });
                        }
                    }
                }
            })
        });
    });

    function getInterviewModeChild(id, job_id) {
        $('#url_div').hide();
        $('#lbl_call_mode').html('');
        $('#url').hide('');
        $('#isdMeetingLinkWrap').hide();

        var url = "{{ route('service_company.job-applications.getinterviewchild', ':id') }}";
        url = url.replace(':id', id);
        var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
        var token = "{{ csrf_token() }}";

        $.ajax({
            type: 'POST',
            url: url,
            data: {
                '_token': token,
                '_method': 'POST',
                'id': id,
                'job_id': job_id
            },
            beforeSend: function() {
                $('#childInterview').html('<img src="' + baseurl + '" height="35px" width="100%">');
            },
            success: function(response) {
                $('#childInterview').html('');
                $('#childInterview').html(response.data);
                isdRenderLeafTiles();
                isdUpdateSummary();
            },
            complete: function(data) {}
        });
    }

    function getVideoMeetingUrl(currentId, ParentId, job_id) {
        $("#url").prop("readonly", false);
         $("#isdMeetingLinkDisplay").prop("readonly", true);
        var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
        $('#url_div').hide();
        $('#url').val('');
        if ((ParentId == 2) && (currentId == 2)) { // FOR GOOGLE
            $('#url_div').show();
            $('#url').show();
            $('#lbl_call_mode').show();
            $('#lbl_call_mode').html('Google Meet Link');
            var interviewDate = $('#interviewDate').val();
            var interviewTime = $('#interviewTime').val();
            if (interviewDate == '') { alert("Please select interview date"); return false; }
            if (interviewTime == '') { alert("Please select interview Time"); return false; }
            var url = "{{ route('service_company.job-applications.getGoogleVideoUrl', ':currentId') }}";
            url = url.replace(':currentId', currentId);
            var token = "{{ csrf_token() }}";
            $.ajax({
                type: 'POST',
                url: url,
                data: { '_token': token, '_method': 'POST', 'currentId': currentId, 'ParentId': ParentId, 'job_id': job_id, 'interviewDate': interviewDate, 'interviewTime': interviewTime },
                beforeSend: function() { $('#url_div').show(); $('#url').show(); $('#url').attr('placeholder', 'Generating Link .............'); isdSetLinkLoading(); },
                success: function(response) {
                    if (response.status == "success") {
                        $('#url_div').show();
                        $('#url').val(response.getHangoutLink);
                    } else {
                        Swal.fire({ title: 'Not Available', text: `${response.getHangoutLink ?? ''} is currently not available, kindly use other options to schedule the interview`, icon: 'question', showCancelButton: false, confirmButtonText: 'Ok', cancelButtonText: 'No, cancel', reverseButtons: true });
                        $('.sub_status').hide();
                    }
                    isdSyncMeetingLinkDisplay();
                },
            });
        }
        if ((ParentId == 2) && (currentId == 5)) { // Microsoft Teams
            $('#url_div').show();
            $('#url').show();
            $('#lbl_call_mode').show();
            $('#lbl_call_mode').html('Microsoft Teams Meeting Link');
            var interviewDate = $('#interviewDate').val();
            var interviewTime = $('#interviewTime').val();
            if (interviewDate == '') { alert("Please select interview date"); return false; }
            if (interviewTime == '') { alert("Please select interview Time"); return false; }
            var url = "{{ route('service_company.job-applications.getGoogleVideoUrl', ':currentId') }}";
            url = url.replace(':currentId', currentId);
            var token = "{{ csrf_token() }}";
            $.ajax({
                type: 'POST',
                url: url,
                data: { '_token': token, '_method': 'POST', 'currentId': currentId, 'ParentId': ParentId, 'job_id': job_id, 'interviewDate': interviewDate, 'interviewTime': interviewTime },
                beforeSend: function() { $('#url_div').show(); $('#url').show(); $('#url').attr('placeholder', 'Generating Link .............'); isdSetLinkLoading(); },
                success: function(response) {
                    if (response.status == "success") {
                        $('#url_div').show();
                        $('#url').val(response.getHangoutLink);
                    } else {
                        Swal.fire({ title: 'Not Available', text: `${response.getHangoutLink ?? ''} is currently not available, kindly use other options to schedule the interview`, icon: 'question', showCancelButton: false, confirmButtonText: 'Ok', cancelButtonText: 'No, cancel', reverseButtons: true });
                        $('.sub_status').hide();
                    }
                    isdSyncMeetingLinkDisplay();
                },
            });
        }
        if ((ParentId == 2) && (currentId == 3)) { // Zoom
            var interviewDate = $('#interviewDate').val();
            var interviewTime = $('#interviewTime').val();
            if (interviewDate == '') { alert("Please select interview date"); return false; }
            if (interviewTime == '') { alert("Please select interview Time"); return false; }
            var url = "{{ route('service_company.job-applications.getGoogleVideoUrl', ':currentId') }}";
            url = url.replace(':currentId', currentId);
            var token = "{{ csrf_token() }}";
            $.ajax({
                type: 'POST',
                url: url,
                data: { '_token': token, '_method': 'POST', 'currentId': currentId, 'ParentId': ParentId, 'job_id': job_id, 'interviewDate': interviewDate, 'interviewTime': interviewTime },
                beforeSend: function() { $('#url_div').show(); $('#url').show(); $('#url').attr('placeholder', 'Generating Link .............'); isdSetLinkLoading(); },
                success: function(response) {
                    if (response.status == "success") {
                        $('#url_div').show();
                        $('#url').show();
                        $('#lbl_call_mode').show();
                        $('#lbl_call_mode').html('Zoom Link');
                        if (response.mode == 'zoom') { $('#url').val(response.getHangoutLink); } else { $('#url').val(''); }
                    } else {
                        Swal.fire({ title: 'Not Available', text: `${response.getHangoutLink ?? ''} is currently not available, kindly use other options to schedule the interview.`, icon: 'question', showCancelButton: false, confirmButtonText: 'Ok', cancelButtonText: 'No, cancel', reverseButtons: true });
                        $('.sub_status').hide();
                    }
                    isdSyncMeetingLinkDisplay();
                },
                complete: function(data) {}
            });
        }
        if ((ParentId == 2) && currentId == 6) { // Other video
            $('#url_div').show(); $('#url').show(); $('#lbl_call_mode').show();
            $('#lbl_call_mode').html('Enter Your Meeting Link'); $('#url').attr('placeholder', 'Enter Link here');
            $("#isdMeetingLinkDisplay").prop("readonly", false);
            isdSyncMeetingLinkDisplay();
        } else if ((ParentId == 1) && ((currentId == 1) || (currentId == 4))) { // Phone / Skype
            $('#url_div').show(); $('#url').show(); $('#url').attr('placeholder', 'Enter Link here');
            $('#lbl_call_mode').show(); $('#lbl_call_mode').html('');
            if (currentId == 1) { $('#lbl_call_mode').html('Phone Call'); $('#url').val('Phone Call'); $("#url").prop("readonly", true); }
            if (currentId == 4) { $('#lbl_call_mode').html('Skype Call'); $('#url').val('Skype Call'); $("#url").prop("readonly", true); }
            isdSyncMeetingLinkDisplay();
        } else if ((ParentId == 3) && (currentId >= 0 || currentId === 'company_address')) {
            var location = $('#interview_mode_lavels :selected').text();
            if (currentId === 'company_address' || (parseInt(currentId, 10) > 0)) {
                $('#lbl_call_mode').html('Interview Address');
                $('#url').val(location);
                $("#isdMeetingLinkDisplay").prop("readonly", true).val(location);
                $('#isdMeetingLinkWrap').hide();
            } else {
                // "Other address" — manual entry, not a saved client location.
                $('#lbl_call_mode').html('Enter the Interview Address');
                $('#isdMeetingLinkLabel').html('Enter the Interview Address');
                $('#url').val('');
                $("#isdMeetingLinkDisplay").prop("readonly", false).val('').attr('placeholder', 'Enter custom interview address...');
                $('#isdMeetingLinkWrap').show();
                $('#isdMeetingLinkDisplay').focus();
            }
            isdSyncMeetingLinkDisplay();
        }
        isdUpdateSummary();
    }
    /* =====================================================================
       Presentation-only glue: renders the icon tile-grid on top of the real
       #interview_mode / #interview_mode_lavels selects (built above), keeps
       chips in sync with the real #panellist field, and drives the live
       "Interview Summary" sidebar. None of this changes what gets submitted
       or how — it only mirrors/sets the same real fields the legacy
       dropdowns always used.
       ===================================================================== */
    var isdFamilyIcons = { 1: 'ti-phone-filled', 2: 'ti-video', 3: 'ti-map-pin-filled', 4: 'ti-dots' };
    var isdLeafIcons = { 'google meet':'ti-brand-google-filled', 'zoom':'ti-brand-zoom', 'microsoft teams':'ti-brand-teams', 'skype call':'ti-brand-skype', 'phone call':'ti-phone-filled' };
    // Custom brand SVGs (full-color, self-contained) — used in place of the
    // Tabler icon font for the modes an icon was actually supplied for.
    // Keyed the same way isdFamilyIcons / isdLeafIcons are looked up.
    var isdCustomSvg = {
        'family-1': '<svg viewBox="0 0 122.88 122.27" xmlns="http://www.w3.org/2000/svg"><path fill="#1E0478" d="M33.84,50.25c4.13,7.45,8.89,14.6,15.07,21.12c6.2,6.56,13.91,12.53,23.89,17.63c0.74,0.36,1.44,0.36,2.07,0.11 c0.95-0.36,1.92-1.15,2.87-2.1c0.74-0.74,1.66-1.92,2.62-3.21c3.84-5.05,8.59-11.32,15.3-8.18c0.15,0.07,0.26,0.15,0.41,0.21 l22.38,12.87c0.07,0.04,0.15,0.11,0.21,0.15c2.95,2.03,4.17,5.16,4.2,8.71c0,3.61-1.33,7.67-3.28,11.1 c-2.58,4.53-6.38,7.53-10.76,9.51c-4.17,1.92-8.81,2.95-13.27,3.61c-7,1.03-13.56,0.37-20.27-1.69 c-6.56-2.03-13.17-5.38-20.39-9.84l-0.53-0.34c-3.31-2.07-6.89-4.28-10.4-6.89C31.12,93.32,18.03,79.31,9.5,63.89 C2.35,50.95-1.55,36.98,0.58,23.67c1.18-7.3,4.31-13.94,9.77-18.32c4.76-3.84,11.17-5.94,19.47-5.2c0.95,0.07,1.8,0.62,2.25,1.44 l14.35,24.26c2.1,2.72,2.36,5.42,1.21,8.12c-0.95,2.21-2.87,4.25-5.49,6.15c-0.77,0.66-1.69,1.33-2.66,2.03 c-3.21,2.33-6.86,5.02-5.61,8.18L33.84,50.25L33.84,50.25L33.84,50.25z"/></svg>',
        'family-2': '<svg viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg"><path d="M170.666667 256h469.333333c46.933333 0 85.333333 38.4 85.333333 85.333333v341.333334c0 46.933333-38.4 85.333333-85.333333 85.333333H170.666667c-46.933333 0-85.333333-38.4-85.333334-85.333333V341.333333c0-46.933333 38.4-85.333333 85.333334-85.333333z" fill="#4CAF50"/><path d="M938.666667 746.666667l-213.333334-128V405.333333l213.333334-128z" fill="#388E3C"/></svg>',
        'family-3': '<svg viewBox="0 0 72 72" xmlns="http://www.w3.org/2000/svg"><path fill="#d22f27" d="M52.5733,29.11c0-9.3149-7.1338-16.8923-15.9034-16.8923S20.7665,19.795,20.7665,29.114c.0027.4653.2238,11.6088,12.9612,31.2452a3.4626,3.4626,0,0,0,2.8174,1.9343c1.8394,0,3.0941-2.0266,3.2162-2.2327C52.58,40.4135,52.58,29.5534,52.5733,29.11ZM36.67,35.9135a7.0826,7.0826,0,1,1,7.0826-7.0826A7.0907,7.0907,0,0,1,36.67,35.9135Z"/><path fill="#ea5a47" d="M52.5733,29.11c0-9.3149-7.1338-16.8923-15.9034-16.8923a14.96,14.96,0,0,0-3.8645.5254c8.3948.4493,15.0989,7.8225,15.0989,16.849.0068.4436.0068,11.3036-12.812,30.9509a5.8543,5.8543,0,0,1-.5865.7972,2.8549,2.8549,0,0,0,2.0393.9534c1.8394,0,3.0941-2.0266,3.2162-2.2327C52.58,40.4135,52.58,29.5534,52.5733,29.11Z"/><path fill="none" stroke="#000000" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M36.5451,62.2935a3.4628,3.4628,0,0,1-2.8174-1.9343C20.99,40.7228,20.7692,29.5793,20.7665,29.114c0-9.3191,7.1337-16.8964,15.9034-16.8964S52.5733,19.7949,52.5733,29.11c.0068.4436.0068,11.3036-12.812,30.9509C39.6392,60.2669,38.3845,62.2935,36.5451,62.2935Z"/><path fill="none" stroke="#000000" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M36.67,35.9135a7.0826,7.0826,0,1,1,7.0827-7.0826A7.0906,7.0906,0,0,1,36.67,35.9135Z"/></svg>',
        'leaf-zoom': '<svg viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg" fill-rule="evenodd" clip-rule="evenodd" stroke-linejoin="round" stroke-miterlimit="2"><g transform="matrix(6.4 0 0 6.4 -32 -32)"><clipPath id="zmclip1"><path d="M84.062 37.188v15.624c0 17.248-14.002 31.25-31.25 31.25H37.188c-17.248 0-31.25-14.002-31.25-31.25V37.188c0-17.248 14.002-31.25 31.25-31.25h15.624c17.248 0 31.25 14.002 31.25 31.25z"/></clipPath><g clip-path="url(#zmclip1)"><path d="M5 13a8 8 0 018-8h64a8 8 0 018 8v64a8 8 0 01-8 8H13a8 8 0 01-8-8V13z" fill="url(#zmgrad1)" fill-rule="nonzero"/></g></g><path d="M152.08 295.512H88.07c-4.489 0-8.501-2.677-10.223-6.825a11.048 11.048 0 012.4-12.063l44.335-44.335H92.806c-8.728 0-15.805-7.077-15.805-15.804h59.03c4.49 0 8.502 2.683 10.218 6.831a11.024 11.024 0 01-2.393 12.056l-44.335 44.336h36.75c8.733 0 15.81 7.076 15.81 15.804zM435 246.119c0-16.99-13.83-30.821-30.821-30.821-9.089 0-17.275 3.96-22.92 10.243-5.644-6.282-13.83-10.243-22.918-10.243-16.991 0-30.822 13.83-30.822 30.821v49.393c8.728 0 15.804-7.077 15.804-15.804v-33.589c0-8.276 6.741-15.01 15.018-15.01 8.282 0 15.017 6.734 15.017 15.01v33.589c0 8.727 7.076 15.804 15.804 15.804v-49.393c0-8.276 6.734-15.01 15.017-15.01 8.276 0 15.017 6.734 15.017 15.01v33.589c0 8.727 7.07 15.804 15.804 15.804v-49.393zm-115.383 9.882c0 22.475-18.223 40.698-40.697 40.698-22.48 0-40.704-18.223-40.704-40.698 0-22.48 18.223-40.703 40.704-40.703 22.474 0 40.697 18.223 40.697 40.703zm-15.804 0c0-13.752-11.147-24.893-24.893-24.893-13.753 0-24.893 11.14-24.893 24.893 0 13.747 11.14 24.894 24.893 24.894 13.746 0 24.893-11.147 24.893-24.894zm-71.918 0c0 22.475-18.217 40.698-40.697 40.698-22.481 0-40.698-18.223-40.698-40.698 0-22.48 18.217-40.703 40.698-40.703 22.48 0 40.697 18.223 40.697 40.703zm-15.804 0c0-13.752-11.147-24.893-24.893-24.893-13.747 0-24.894 11.14-24.894 24.893 0 13.747 11.147 24.894 24.894 24.894 13.746 0 24.893-11.147 24.893-24.894z" fill="#fff" fill-rule="nonzero"/><defs><linearGradient id="zmgrad1" x1="0" y1="0" x2="1" y2="0" gradientUnits="userSpaceOnUse" gradientTransform="rotate(-60 88.792 30.85) scale(109.282)"><stop offset="0" stop-color="#0845bf"/><stop offset=".6" stop-color="#0b5cff"/><stop offset="1" stop-color="#4f90ee"/></linearGradient></defs></svg>',
        'leaf-google meet': '<svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg"><path fill="#F5BB41" d="M91.084,20.921L77.229,32.352V18.068c0-3.528-2.84-6.386-6.346-6.386H25.405L3.723,33.502v32.996v15.434c0,3.528,2.84,6.386,6.346,6.386h15.336h45.478c3.506,0,6.346-2.858,6.346-6.386V68.18l13.918,11.485c2.083,1.639,5.13,0.144,5.13-2.522V23.39C96.277,20.692,93.162,19.212,91.084,20.921z M56.076,66.498H25.405V33.502h30.671V50V66.498z"/><path fill="#2167D1" d="M3.723,66.498v15.434c0,3.528,2.84,6.386,6.346,6.386h15.336v-21.82H3.723z"/><rect fill="#3D84F3" height="32.996" width="21.682" x="3.723" y="33.502"/><path fill="#4CA853" d="M91.084,20.921L77.229,32.352V68.18l13.918,11.485c2.083,1.639,5.13,0.144,5.13-2.522V23.39C96.277,20.692,93.162,19.212,91.084,20.921z"/><path fill="#4CA853" d="M56.076,50v16.498H25.405v21.82h45.478c3.506,0,6.346-2.858,6.346-6.386V68.18L56.076,50z"/><polygon fill="#398039" points="77.229,68.18 77.229,32.363 56.076,50"/><polygon fill="#D74F3F" points="3.723,33.502 25.405,33.502 25.405,11.682"/><path fill="#F5BB41" d="M70.883,11.682H25.405v21.82h30.671V50l21.153-17.637v-0.011V18.068C77.229,14.54,74.389,11.682,70.883,11.682z"/></svg>',
        'leaf-microsoft teams': '<svg viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg"><path fill="#5059C9" d="M10.765 6.875h3.616c.342 0 .619.276.619.617v3.288a2.272 2.272 0 01-2.274 2.27h-.01a2.272 2.272 0 01-2.274-2.27V7.199c0-.179.145-.323.323-.323zM13.21 6.225c.808 0 1.464-.655 1.464-1.462 0-.808-.656-1.463-1.465-1.463s-1.465.655-1.465 1.463c0 .807.656 1.462 1.465 1.462z"/><path fill="#7B83EB" d="M8.651 6.225a2.114 2.114 0 002.117-2.112A2.114 2.114 0 008.65 2a2.114 2.114 0 00-2.116 2.112c0 1.167.947 2.113 2.116 2.113zM11.473 6.875h-5.97a.611.611 0 00-.596.625v3.75A3.669 3.669 0 008.488 15a3.669 3.669 0 003.582-3.75V7.5a.611.611 0 00-.597-.625z"/><path fill="#000000" d="M8.814 6.875v5.255a.598.598 0 01-.596.595H5.193a3.951 3.951 0 01-.287-1.476V7.5a.611.611 0 01.597-.624h3.31z" opacity=".1"/><path fill="#000000" d="M8.488 6.875v5.58a.6.6 0 01-.596.595H5.347a3.22 3.22 0 01-.267-.65 3.951 3.951 0 01-.172-1.15V7.498a.61.61 0 01.596-.624h2.985z" opacity=".2"/><path fill="#000000" d="M8.488 6.875v4.93a.6.6 0 01-.596.595H5.08a3.951 3.951 0 01-.172-1.15V7.498a.61.61 0 01.596-.624h2.985z" opacity=".2"/><path fill="#000000" d="M8.163 6.875v4.93a.6.6 0 01-.596.595H5.079a3.951 3.951 0 01-.172-1.15V7.498a.61.61 0 01.596-.624h2.66z" opacity=".2"/><path fill="#000000" d="M8.814 5.195v1.024c-.055.003-.107.006-.163.006-.055 0-.107-.003-.163-.006A2.115 2.115 0 016.593 4.6h1.625a.598.598 0 01.596.594z" opacity=".1"/><path fill="#000000" d="M8.488 5.52v.699a2.115 2.115 0 01-1.79-1.293h1.195a.598.598 0 01.595.594z" opacity=".2"/><path fill="#000000" d="M8.163 5.52v.647a2.115 2.115 0 01-1.465-1.242h.87a.598.598 0 01.595.595z" opacity=".2"/><path fill="url(#pgmt1)" d="M1.597 4.925h5.969c.33 0 .597.267.597.596v5.958a.596.596 0 01-.597.596h-5.97A.596.596 0 011 11.479V5.521c0-.33.267-.596.597-.596z"/><path fill="#ffffff" d="M6.152 7.193H4.959v3.243h-.76V7.193H3.01v-.63h3.141v.63z"/><defs><linearGradient id="pgmt1" x1="2.244" x2="6.906" y1="4.46" y2="12.548" gradientUnits="userSpaceOnUse"><stop stop-color="#5A62C3"/><stop offset=".5" stop-color="#4D55BD"/><stop offset="1" stop-color="#3940AB"/></linearGradient></defs></svg>',
        'leaf-phone call': '<svg viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"><rect width="512" height="512" rx="15%" fill="#20df40"/><path fill="#ffffff" d="M395 338c-10-9-22-17-33-23-23-16-43-17-59 6-10 14-23 16-37 10-39-18-68-43-84-82-8-16-8-31 10-42 9-6 19-14 18-28-1-18-44-78-62-84-8-2-14-2-22 0-41 14-57 46-42 85a451 451 0 0 0 251 248l17 5c26 0 57-25 67-50 9-24-10-34-24-45z"/><path fill="none" stroke="#ffffff" stroke-linecap="round" stroke-width="17" d="M268 138s41-1 72 30c31 30 27 74 27 74m-87-63s18-2 33 14c15 15 13 34 13 34M255 98s64-3 111 44c47 48 43 114 43 114"/></svg>',
        'leaf-other': '<svg viewBox="0 0 512.176 512.176" xmlns="http://www.w3.org/2000/svg"><path fill="#1689FC" d="M253.422 172.886L346.8 79.507c11.341-11.342 26.288-17.016 41.229-17.016 14.94 0 29.887 5.674 41.229 17.016l.068.072c11.296 11.334 16.948 26.251 16.948 41.157 0 14.937-5.673 29.884-17.013 41.226l-95.985 95.987c-6.917 6.917-15.622 11.896-24.986 14.67-9.375 2.775-19.459 3.322-29.103 1.37-12.796-2.59-20.783-9.21-28.727-15.795-16.45-17.096-43.529-18.599-62.063-.065l-.075.075c4.42 13.375 13.19 26.417 24.152 37.514 11.64 11.781 25.634 21.27 39.45 26.58 19.788 7.605 41.42 9.238 62.029 4.903 19.889-4.186 38.83-13.981 54.226-29.377l97.982-97.981c21.144-21.144 31.719-49.029 31.719-76.914 0-27.868-10.561-55.732-31.676-76.872l-.044-.042c-21.145-21.145-49.028-31.719-76.913-31.719-27.886 0-55.769 10.574-76.913 31.717l-97.982 97.983c-5.604 5.604-10.447 11.639-14.522 17.981a108.1 108.1 0 00-5.37 9.341 124.273 124.273 0 0126.274-2.805c11.003 0 22.005 1.459 32.688 4.373z"/><path fill="#5B9FFC" d="M214.476 297.698l37.91-37.911c7.339 6.035 15.038 11.82 26.801 14.202 9.644 1.952 19.728 1.405 29.103-1.37 9.364-2.774 18.069-7.753 24.986-14.67l95.985-95.987c11.34-11.342 17.013-26.289 17.013-41.226 0-14.185-5.118-28.381-15.349-39.488l35.235-35.233.044.042c21.115 21.14 31.676 49.004 31.676 76.872 0 27.885-10.575 55.77-31.719 76.914l-97.982 97.981c-15.396 15.396-34.337 25.191-54.226 29.377-20.609 4.335-42.241 2.702-62.029-4.903-13.034-5.009-26.225-13.737-37.448-24.6z"/><path fill="#A3D4FF" d="M41.107 471.058h.001c45.123 45.124 118.96 45.124 164.083 0l96.531-96.531a114.972 114.972 0 0028.236-46.213c-29.88 4.82-60.152 5.081-76.034.476l-95.341 95.34c-20.391 20.392-53.763 20.394-74.158 0-20.393-20.393-20.392-53.763.001-74.157l94.298-94.297c20.393-20.394 53.763-20.394 74.156-.001 1.992 1.991 6.739 4.885 12.357 7.736 22.287 11.305 43.499 7.568 63.749-9.638a114.963 114.963 0 00-27.264-43.33c-45.123-45.123-118.961-45.123-164.084 0l-96.531 96.531c-45.123 45.123-45.123 118.961 0 164.084z"/><path fill="#BFE0FF" d="M84.045 428.128l-38.208 38.208.002.002c21.143 21.144 49.027 31.718 76.914 31.718 27.886 0 55.769-10.574 76.914-31.718l97.981-97.981c5.798-5.798 10.771-12.039 14.913-18.581a106.457 106.457 0 004.983-8.762l-.685.148a124.32 124.32 0 01-63.515-3.282l-88.581 88.58c-11.342 11.342-26.289 17.016-41.23 17.016-14.185 0-28.382-5.119-39.488-15.348zm213.601-213.601l-35.11 35.11c5.672 4.593 11.642 8.813 19.443 10.391 7.33 1.483 15.056 1.051 22.29-1.09 6.2-1.837 12.019-4.952 16.884-9.214a107.295 107.295 0 00-8.817-16.984c-4.083-6.39-8.98-12.503-14.688-18.212l-.002-.001z"/><path fill="#1A1A1A" fill-rule="nonzero" d="M176.52 176.649a121.866 121.866 0 0111.304-22.38 122.478 122.478 0 0116.419-20.382l97.981-97.981C326.16 11.97 357.709 0 389.247 0c68.144 0 122.929 54.782 122.929 122.929 0 31.54-11.971 63.087-35.906 87.023l-97.982 97.981a121.905 121.905 0 01-42.769 27.74 122.073 122.073 0 01-10.898 21.696 122.37 122.37 0 01-16.864 21.096l-97.981 97.981c-47.639 47.639-126.409 47.641-174.046 0-47.64-47.637-47.639-126.407 0-174.046l97.981-97.981a121.978 121.978 0 0142.809-27.77zm76.824 161.231l-88.581 88.58c-22.575 22.575-59.882 22.575-82.457 0-22.67-22.671-22.669-59.784-.002-82.458l95.984-95.982c8.853-8.852 20.073-14.579 32.443-16.521 25.13-3.947 36.941 5.84 48.833 15.695 6.602 5.474 13.243 10.978 22.415 12.834 7.33 1.483 15.056 1.051 22.29-1.09 6.2-1.837 12.019-4.952 16.884-9.214-16.645-40.465-56.234-66.915-100.419-66.915-27.887 0-55.771 10.574-76.914 31.719l-97.981 97.981c-42.087 42.087-42.088 111.744 0 153.829 42.085 42.088 111.742 42.086 153.828 0l97.981-97.981c5.798-5.798 10.771-12.039 14.913-18.581a106.457 106.457 0 004.983-8.762l-.685.148a124.32 124.32 0 01-63.515-3.282zm78.67-98.887l87.135-87.136c8.552-8.554 12.829-19.836 12.829-31.121a43.808 43.808 0 00-43.949-43.949c-11.285 0-22.57 4.278-31.121 12.829l-88.438 88.44c28.065 11.729 50.64 33.492 63.544 60.937zm-78.592-66.107L346.8 79.507c11.341-11.342 26.288-17.016 41.229-17.016 32.278 0 58.245 25.968 58.245 58.245 0 14.937-5.673 29.884-17.013 41.226l-95.985 95.987c-6.917 6.917-15.622 11.896-24.986 14.67-9.375 2.775-19.459 3.322-29.103 1.37-12.796-2.59-20.783-9.21-28.727-15.795-9.427-7.813-18.791-15.57-37.552-12.624-9.378 1.471-17.887 5.935-24.586 12.634 4.42 13.375 13.19 26.417 24.152 37.514 11.64 11.781 25.634 21.27 39.45 26.58 19.788 7.605 41.42 9.238 62.029 4.903 19.889-4.186 38.83-13.981 54.226-29.377l97.982-97.981c21.144-21.144 31.719-49.029 31.719-76.914A108.31 108.31 0 00389.247 14.296c-27.886 0-55.769 10.574-76.913 31.717l-97.982 97.983c-5.604 5.604-10.447 11.639-14.522 17.981a108.1 108.1 0 00-5.37 9.341 124.273 124.273 0 0126.274-2.805c11.003 0 22.005 1.459 32.688 4.373zm-76.169 96.387l-84.84 84.838c-17.12 17.123-17.121 45.117.002 62.24 17.022 17.023 45.216 17.023 62.239 0l84.217-84.215c-13.02-6.271-25.752-15.483-36.561-26.423-10.645-10.774-19.509-23.299-25.057-36.44z"/></svg>'
    };
    function isdSetIconContent($icEl, key, fallbackIconClass){
        var svg = isdCustomSvg[key];
        if (svg) {
            $icEl.html(svg).css({ background: 'transparent' });
            return true;
        }
        $icEl.html('<i class="ti ' + fallbackIconClass + '"></i>');
        return false;
    }
    // Brand-ish colors per icon, matching the reference design (each mode gets
    // its own recognizable color instead of one flat blue box).
    var isdIconColors = {
        'ti-brand-google-filled': { bg: '#e6f4ea', fg: '#1a7f37' },
        'ti-brand-zoom':   { bg: '#e8f0fe', fg: '#2d8cff' },
        'ti-brand-teams':  { bg: '#f1edfd', fg: '#5059c9' },
        'ti-brand-skype':  { bg: '#e6f7ff', fg: '#00aff0' },
        'ti-phone-filled': { bg: '#e6faf3', fg: '#03a678' },
        'ti-video':        { bg: '#eef4ff', fg: '#3e97ff' },
        'ti-map-pin-filled': { bg: '#f7edff', fg: '#a35ee8' },
        'ti-dots':         { bg: '#f1f1f4', fg: '#a1a5b7' },
        'ti-apps-filled':  { bg: '#f1f1f4', fg: '#a1a5b7' }
    };
    function isdColorIcon($icEl, iconClass){
        var c = isdIconColors[iconClass];
        if (c) { $icEl.css({ background: c.bg, color: c.fg }); }
    }

    function isdLeafIcon(name){
        var key = (name||'').toLowerCase();
        return isdLeafIcons[key] || 'ti-map-pin-filled';
    }
    function isdLeafCustomKey(name){
        var key = (name||'').toLowerCase();
        if (isdCustomSvg['leaf-' + key]) return 'leaf-' + key;
        if (key.indexOf('other') !== -1) return 'leaf-other';
        return null;
    }

    function isdRenderLeafTiles(){
        var mode = $('#interview_mode').val();
        var $leafSec = $('#isdLeafSection');
        var $subCards = $('#isdSubCards').empty();
        $('#isdLeafTiles').empty().hide();
        $('#isdLocCards').empty().hide();
        $('#isdAutoGenBanner').hide();

        var opts = $('#interview_mode_lavels option');
        var real = opts.filter(function(){ return $(this).val() !== ''; });

        if (mode == '3') { // Face to Face
            $subCards.removeClass('cols-3 cols-4');
            $('#isdLeafTitle').text('Choose Interview Location');
            $('#isdLeafSub').text('Select the interview location for face to face meeting');

            var fallbackAddress = $('#isdSectionMode').data('fallback-address') || '5th Floor, Orion Business Park, MG Road Ballia - 0, Uttar Pradesh - India';

            var savedOptions = real.filter(function(){ return $(this).val() !== '0'; });
            var otherOption = real.filter(function(){ return $(this).val() === '0'; });

            // If no saved option came from the backend, inject fallback/company address as primary card
            if (savedOptions.length === 0) {
                var injectedVal = 'company_address';
                if ($('#interview_mode_lavels option[value="' + injectedVal + '"]').length === 0) {
                    $('#interview_mode_lavels').prepend('<option value="' + injectedVal + '">' + $('<div>').text(fallbackAddress).html() + '</option>');
                }
                savedOptions = $('#interview_mode_lavels option[value="' + injectedVal + '"]');
            }

            // Render saved location card(s)
            savedOptions.each(function(i){
                var val = $(this).val();
                var rawText = $(this).text().trim();
                var card = $('<div class="isd-sub-card isd-loc-card" data-sub-val="' + val + '" data-loc-val="' + val + '"></div>');
                card.append('<div class="isd-sub-ic isd-sub-ic-pin"><svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C7.58 2 4 5.58 4 10c0 5.25 8 12 8 12s8-6.75 8-12c0-4.42-3.58-8-8-8zm0 11c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3z"/></svg></div>');

                var info = $('<div class="isd-sub-info"></div>');
                var parts = rawText.split(',');
                if (parts.length >= 2) {
                    var l1 = parts.slice(0, Math.min(2, parts.length - 1)).join(',').trim() + ',';
                    var l2 = parts.slice(Math.min(2, parts.length - 1)).join(',').trim();
                    info.append($('<div class="isd-sub-line1"></div>').text(l1));
                    info.append($('<div class="isd-sub-line2"></div>').text(l2));
                } else {
                    info.append($('<div class="isd-sub-line1"></div>').text(rawText));
                }
                card.append(info);
                card.append('<span class="isd-sub-radio"></span>');
                $subCards.append(card);
            });

            // Render "Other address" card
            var otherVal = otherOption.length ? otherOption.val() : '0';
            var otherCard = $('<div class="isd-sub-card isd-loc-card" data-sub-val="' + otherVal + '" data-loc-val="' + otherVal + '">' +
                '<div class="isd-sub-ic isd-sub-ic-link"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg></div>' +
                '<div class="isd-sub-info">' +
                    '<div class="isd-sub-title">Other address</div>' +
                    '<div class="isd-sub-sub">Add a custom address</div>' +
                '</div>' +
                '<span class="isd-sub-radio"></span>' +
            '</div>');
            $subCards.append(otherCard);

            // Select active location card
            var curVal = $('#interview_mode_lavels').val();
            if (!curVal || curVal === '') {
                curVal = savedOptions.first().val();
                $('#interview_mode_lavels').val(curVal);
            }

            $subCards.find('.isd-sub-card').removeClass('is-selected');
            $subCards.find('.isd-sub-card[data-sub-val="' + curVal + '"]').addClass('is-selected');
            $leafSec.show();

            if (curVal === '0') {
                $('#lbl_call_mode').html('Enter the Interview Address');
                $('#isdMeetingLinkLabel').html('Enter the Interview Address');
                $("#isdMeetingLinkDisplay").prop("readonly", false).attr('placeholder', 'Enter custom interview address...');
                $('#isdMeetingLinkWrap').show();
            } else {
                var chosenText = $('#interview_mode_lavels option:selected').text();
                $('#lbl_call_mode').html('Interview Address');
                $('#url').val(chosenText);
                $("#isdMeetingLinkDisplay").prop("readonly", true).val(chosenText);
                $('#isdMeetingLinkWrap').hide();
            }
            isdUpdateSummary();
            return;
        }

        if (mode == '1') { // Telephone
            $subCards.removeClass('cols-3 cols-4');
            $('#isdLeafTitle').text('Choose Telephone Option');
            $('#isdLeafSub').text('Select how you want to conduct the phone interview');

            var phoneOpt = real.filter(function(){ return $(this).text().toLowerCase().indexOf('phone') !== -1; });
            var phoneVal = phoneOpt.length ? phoneOpt.val() : '1';

            var card1 = $('<div class="isd-sub-card" data-sub-val="' + phoneVal + '">' +
                '<div class="isd-sub-ic isd-sub-ic-phonecall">' +
                    '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">' +
                        '<path d="M5 4h4l2 5l-2.5 1.5a11 11 0 0 0 5 5l1.5 -2.5l5 2v4a2 2 0 0 1 -2 2a16 16 0 0 1 -15 -15a2 2 0 0 1 2 -2"></path>' +
                        '<path d="M15 7a2 2 0 0 1 2 2"></path>' +
                        '<path d="M15 3a6 6 0 0 1 6 6"></path>' +
                    '</svg>' +
                '</div>' +
                '<div class="isd-sub-info">' +
                    '<div class="isd-sub-title">Phone Call</div>' +
                    '<div class="isd-sub-sub">Conduct interview via phone call</div>' +
                '</div>' +
                '<span class="isd-sub-radio"></span>' +
            '</div>');
            $subCards.append(card1);

            var otherTeleOpt = real.filter(function(){ return $(this).val() != phoneVal; });
            var otherTeleVal = otherTeleOpt.length ? otherTeleOpt.first().val() : '4';
            var card2 = $('<div class="isd-sub-card" data-sub-val="' + otherTeleVal + '">' +
                '<div class="isd-sub-ic isd-sub-ic-link">' +
                    '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">' +
                        '<path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path>' +
                        '<path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path>' +
                    '</svg>' +
                '</div>' +
                '<div class="isd-sub-info">' +
                    '<div class="isd-sub-title">Other Telecon Mode</div>' +
                    '<div class="isd-sub-sub">Add a custom telecon mode (e.g., dial-in, SIP, etc.)</div>' +
                '</div>' +
                '<span class="isd-sub-radio"></span>' +
            '</div>');
            $subCards.append(card2);

            var curVal = $('#interview_mode_lavels').val();
            if (!curVal || (curVal != phoneVal && curVal != otherTeleVal)) {
                curVal = phoneVal;
                $('#interview_mode_lavels').val(curVal);
            }
            $subCards.find('.isd-sub-card').removeClass('is-selected');
            $subCards.find('.isd-sub-card[data-sub-val="' + curVal + '"]').addClass('is-selected');
            $leafSec.show();
            if (curVal == phoneVal) {
                $('#url').val('Phone Call');
                $('#lbl_call_mode').html('Phone Call');
                $('#isdMeetingLinkWrap').hide();
            }
            isdSyncMeetingLinkDisplay();
            isdUpdateSummary();
            return;
        }

        if (mode == '2') { // Video
            $subCards.addClass('cols-4').removeClass('cols-3');
            $('#isdLeafTitle').text('Choose an online meeting provider');
            $('#isdLeafSub').text('Select the platform to host your video interview');

            // Ensure all 4 provider options exist in native #interview_mode_lavels
            var ensureOption = function(val, text) {
                if ($('#interview_mode_lavels option[value="' + val + '"]').length === 0) {
                    $('#interview_mode_lavels').append('<option value="' + val + '">' + text + '</option>');
                }
            };
            ensureOption('2', 'Google Meeting');
            ensureOption('5', 'Microsoft Meeting');
            ensureOption('3', 'Zoom Meeting');
            ensureOption('6', 'Other address');

            // 1. Google Meet card
            var meetCard = $('<div class="isd-sub-card" data-sub-val="2">' +
                '<div class="isd-sub-ic isd-sub-ic-meet">' +
                    '<svg width="22" height="22" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg"><path fill="#F5BB41" d="M91.084,20.921L77.229,32.352V18.068c0-3.528-2.84-6.386-6.346-6.386H25.405L3.723,33.502v32.996v15.434c0,3.528,2.84,6.386,6.346,6.386h15.336h45.478c3.506,0,6.346-2.858,6.346-6.386V68.18l13.918,11.485c2.083,1.639,5.13,0.144,5.13-2.522V23.39C96.277,20.692,93.162,19.212,91.084,20.921z M56.076,66.498H25.405V33.502h30.671V50V66.498z"/><path fill="#2167D1" d="M3.723,66.498v15.434c0,3.528,2.84,6.386,6.346,6.386h15.336v-21.82H3.723z"/><rect fill="#3D84F3" height="32.996" width="21.682" x="3.723" y="33.502"/><path fill="#4CA853" d="M91.084,20.921L77.229,32.352V68.18l13.918,11.485c2.083,1.639,5.13,0.144,5.13-2.522V23.39C96.277,20.692,93.162,19.212,91.084,20.921z"/><path fill="#4CA853" d="M56.076,50v16.498H25.405v21.82h45.478c3.506,0,6.346-2.858,6.346-6.386V68.18L56.076,50z"/><polygon fill="#398039" points="77.229,68.18 77.229,32.363 56.076,50"/><polygon fill="#D74F3F" points="3.723,33.502 25.405,33.502 25.405,11.682"/><path fill="#F5BB41" d="M70.883,11.682H25.405v21.82h30.671V50l21.153-17.637v-0.011V18.068C77.229,14.54,74.389,11.682,70.883,11.682z"/></svg>' +
                '</div>' +
                '<div class="isd-sub-info">' +
                    '<div class="isd-sub-title">Google Meet</div>' +
                    '<div class="isd-sub-sub">Generate a Google Meet link</div>' +
                '</div>' +
                '<span class="isd-sub-radio"></span>' +
            '</div>');
            $subCards.append(meetCard);

            // 2. Microsoft Teams card
            var teamsCard = $('<div class="isd-sub-card" data-sub-val="5">' +
                '<div class="isd-sub-ic isd-sub-ic-teams">' +
                    '<svg width="22" height="22" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg"><path fill="#5059C9" d="M10.765 6.875h3.616c.342 0 .619.276.619.617v3.288a2.272 2.272 0 01-2.274 2.27h-.01a2.272 2.272 0 01-2.274-2.27V7.199c0-.179.145-.323.323-.323zM13.21 6.225c.808 0 1.464-.655 1.464-1.462 0-.808-.656-1.463-1.465-1.463s-1.465.655-1.465 1.463c0 .807.656 1.462 1.465 1.462z"/><path fill="#7B83EB" d="M8.651 6.225a2.114 2.114 0 002.117-2.112A2.114 2.114 0 008.65 2a2.114 2.114 0 00-2.116 2.112c0 1.167.947 2.113 2.116 2.113zM11.473 6.875h-5.97a.611.611 0 00-.596.625v3.75A3.669 3.669 0 008.488 15a3.669 3.669 0 003.582-3.75V7.5a.611.611 0 00-.597-.625z"/><path fill="#5A62C3" d="M1.597 4.925h5.969c.33 0 .597.267.597.596v5.958a.596.596 0 01-.597.596h-5.97A.596.596 0 011 11.479V5.521c0-.33.267-.596.597-.596z"/><path fill="#ffffff" d="M6.152 7.193H4.959v3.243h-.76V7.193H3.01v-.63h3.141v.63z"/></svg>' +
                '</div>' +
                '<div class="isd-sub-info">' +
                    '<div class="isd-sub-title">Microsoft Teams</div>' +
                    '<div class="isd-sub-sub">Generate a Microsoft Teams meeting link</div>' +
                '</div>' +
                '<span class="isd-sub-radio"></span>' +
            '</div>');
            $subCards.append(teamsCard);

            // 3. Zoom card
            var zoomCard = $('<div class="isd-sub-card" data-sub-val="3">' +
                '<div class="isd-sub-ic isd-sub-ic-zoom">' +
                    '<svg width="34" height="34" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg"><circle cx="50" cy="50" r="48" fill="#0B5CFF"/><text x="50" y="58" font-family=\"\'Poppins\', sans-serif\" font-size="22" font-weight="700" fill="#FFFFFF" text-anchor="middle">zoom</text></svg>' +
                '</div>' +
                '<div class="isd-sub-info">' +
                    '<div class="isd-sub-title">Zoom</div>' +
                    '<div class="isd-sub-sub">Generate a Zoom link</div>' +
                '</div>' +
                '<span class="isd-sub-radio"></span>' +
            '</div>');
            $subCards.append(zoomCard);

            // 4. Other Address card
            var otherVideoCard = $('<div class="isd-sub-card" data-sub-val="6">' +
                '<div class="isd-sub-ic isd-sub-ic-link">' +
                    '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">' +
                        '<path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path>' +
                        '<path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path>' +
                    '</svg>' +
                '</div>' +
                '<div class="isd-sub-info">' +
                    '<div class="isd-sub-title">Other address</div>' +
                    '<div class="isd-sub-sub">Add a custom meeting link</div>' +
                '</div>' +
                '<span class="isd-sub-radio"></span>' +
            '</div>');
            $subCards.append(otherVideoCard);

            var curVal = $('#interview_mode_lavels').val();
            if (!curVal || (curVal !== '2' && curVal !== '5' && curVal !== '3' && curVal !== '6')) {
                curVal = '2';
                $('#interview_mode_lavels').val(curVal);
            }
            $subCards.find('.isd-sub-card').removeClass('is-selected');
            $subCards.find('.isd-sub-card[data-sub-val="' + curVal + '"]').addClass('is-selected');
            $leafSec.show();

            isdSyncMeetingLinkDisplay();

            if (curVal == '2' || curVal == '5' || curVal == '3') {
                var curUrl = ($('#url').val() || '').trim();
                var jobId = $('#jobId').val();
                if (!curUrl && jobId) {
                    getVideoMeetingUrl(curVal, 2, jobId);
                }
            }
            return;
        }
    }

    $(document).on('click', '.isd-mode-card[data-family-val]', function(){
        var val = $(this).data('family-val');
        $('.isd-mode-card').removeClass('is-active');
        $(this).addClass('is-active');
        $('#interview_mode').val(val).trigger('change');
    });

    $(document).on('change', '#interview_mode', function(){
        var val = $(this).val();
        $('.isd-mode-card').removeClass('is-active');
        $('.isd-mode-card[data-family-val="'+val+'"]').addClass('is-active');
        isdRenderLeafTiles();
        isdUpdateSummary();
    });

    $(document).on('click', '.isd-sub-card[data-sub-val], .isd-loc-card[data-loc-val]', function(){
        var val = $(this).data('sub-val') || $(this).data('loc-val');
        $('.isd-sub-card, .isd-loc-card').removeClass('is-selected');
        $(this).addClass('is-selected');
        $('#interview_mode_lavels').val(val).trigger('change');

        var mode = $('#interview_mode').val();
        if (mode == '2') {
            var jobId = $('#jobId').val();
            if (val == '6') {
                var cur = ($('#url').val() || '').trim();
                if (cur.indexOf('meet.google.com') !== -1 || cur.indexOf('teams.microsoft.com') !== -1 || cur.indexOf('zoom.us') !== -1) {
                    $('#url').val('');
                }
            } else if (val == '2' || val == '5' || val == '3') {
                getVideoMeetingUrl(val, 2, jobId);
            }
            isdSyncMeetingLinkDisplay();
        }
    });

    $(document).on('change', '#interview_mode_lavels', function(){
        var val = $(this).val();
        $('.isd-sub-card, .isd-loc-card').removeClass('is-selected');
        $('.isd-sub-card[data-sub-val="'+val+'"], .isd-loc-card[data-loc-val="'+val+'"]').addClass('is-selected');
        isdSyncMeetingLinkDisplay();
    });

    function isdSyncMeetingLinkDisplay(){
        var mode = $('#interview_mode').val();
        var childVal = $('#interview_mode_lavels').val();
        var currentUrl = ($('#url').val() || '').trim();

        $('#isdAutoGenBanner').hide();

        if (mode == '3') { // Face to Face
            if (childVal !== '0') {
                $('#isdMeetingLinkWrap').hide();
            } else {
                $('#isdMeetingLinkLabel').text('Enter the Interview Address');
                $('#isdCustomAddressBadge').html('<i class="ti ti-map-pin"></i> Custom Location');
                $('#isdCustomAddressHelp').html('<i class="ti ti-info-circle"></i> <span>Enter the in-person meeting address, building, or room number.</span>');
                $('#isdLinkRefresh').hide();
                $('#isdMeetingLinkDisplay').prop('readonly', false).attr('placeholder', 'Enter custom interview address...').val(currentUrl);
                $('#isdMeetingLinkWrap').show();
            }
            isdUpdateSummary();
            return;
        }

        if (mode == '2') { // Video
            $('#isdMeetingLinkWrap').show();
            if (childVal == '2') { // Google Meet
                $('#isdMeetingLinkLabel').text('Google Meet Link');
                $('#isdCustomAddressBadge').html('<i class="ti ti-brand-google"></i> Google Meet');
                $('#isdCustomAddressHelp').html('<i class="ti ti-info-circle"></i> <span>Google Meet link is generated automatically. You can refresh or copy the link.</span>');
                $('#isdLinkRefresh').show();
                $('#isdMeetingLinkDisplay').prop('readonly', true).attr('placeholder', 'Generating Google Meet link...').val(currentUrl);
            } else if (childVal == '5') { // Microsoft Teams
                $('#isdMeetingLinkLabel').text('Microsoft Teams Meeting Link');
                $('#isdCustomAddressBadge').html('<i class="ti ti-brand-windows"></i> Microsoft Teams');
                $('#isdCustomAddressHelp').html('<i class="ti ti-info-circle"></i> <span>Microsoft Teams link is generated automatically. You can refresh or copy the link.</span>');
                $('#isdLinkRefresh').show();
                $('#isdMeetingLinkDisplay').prop('readonly', true).attr('placeholder', 'Generating Microsoft Teams link...').val(currentUrl);
            } else if (childVal == '3') { // Zoom
                $('#isdMeetingLinkLabel').text('Zoom Meeting Link');
                $('#isdCustomAddressBadge').html('<i class="ti ti-video"></i> Zoom');
                $('#isdCustomAddressHelp').html('<i class="ti ti-info-circle"></i> <span>Zoom meeting link is generated automatically. You can refresh or copy the link.</span>');
                $('#isdLinkRefresh').show();
                $('#isdMeetingLinkDisplay').prop('readonly', true).attr('placeholder', 'Generating Zoom link...').val(currentUrl);
            } else if (childVal == '6') { // Other Address
                $('#isdMeetingLinkLabel').text('Meeting URL / Address');
                $('#isdCustomAddressBadge').html('<i class="ti ti-link"></i> Custom Link');
                $('#isdCustomAddressHelp').html('<i class="ti ti-info-circle"></i> <span>Enter the meeting link, location, or custom meeting address.</span>');
                $('#isdLinkRefresh').hide();
                $('#isdMeetingLinkDisplay').prop('readonly', false).attr('placeholder', 'https://...').val(currentUrl);
            } else {
                $('#isdMeetingLinkLabel').text('Meeting URL / Address');
                $('#isdCustomAddressBadge').html('<i class="ti ti-video"></i> Online Meeting');
                $('#isdCustomAddressHelp').html('<i class="ti ti-info-circle"></i> <span>Meeting link will be generated automatically.</span>');
                $('#isdLinkRefresh').show();
                $('#isdMeetingLinkDisplay').prop('readonly', true).attr('placeholder', 'Generating meeting link...').val(currentUrl);
            }
            isdUpdateSummary();
            return;
        }

        if (mode == '1') { // Telephone
            if (childVal == '1') { // Phone Call
                $('#isdMeetingLinkWrap').hide();
            } else { // Other Telecon
                $('#isdMeetingLinkLabel').text('Telecon Mode / Dial-in Details');
                $('#isdCustomAddressBadge').html('<i class="ti ti-phone"></i> Custom Telecon');
                $('#isdCustomAddressHelp').html('<i class="ti ti-info-circle"></i> <span>Enter the dial-in details or conference bridge.</span>');
                $('#isdLinkRefresh').hide();
                $('#isdMeetingLinkDisplay').prop('readonly', false).attr('placeholder', 'e.g. dial-in number, passcode, or SIP URI').val(currentUrl);
                $('#isdMeetingLinkWrap').show();
            }
            isdUpdateSummary();
            return;
        }

        $('#isdMeetingLinkWrap').hide();
        isdUpdateSummary();
    }
    function isdSetLinkLoading(){
        $('#isdMeetingLinkWrap').show();
        $('#isdMeetingLinkDisplay').val('').attr('placeholder', 'Generating link...');
    }
    $(document).on('click', '#isdLinkRefresh', function(){
        var parentVal = $('#interview_mode').val();
        var childVal = $('#interview_mode_lavels').val();
        var jobId = $('#jobId').val();
        if (parentVal && childVal) { getVideoMeetingUrl(childVal, parentVal, jobId); }
    });
    $(document).on('click', '#isdLinkCopy', function(){
        var v = $('#isdMeetingLinkDisplay').val();
        if (v && navigator.clipboard) {
            navigator.clipboard.writeText(v).then(function(){
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Link copied to clipboard',
                    showConfirmButton: false,
                    timer: 2000
                });
            });
        }
    });
    $(document).on('click', '#isdLinkOpen', function(){
        var v = $('#isdMeetingLinkDisplay').val();
        if (v && v.indexOf('http') === 0) { window.open(v, '_blank'); }
    });
    // Keep the plain #url input's own manual edits mirrored into the display copy too.
    $(document).on('input', '#url', function(){ $('#isdMeetingLinkDisplay').val($(this).val()); });
    $(document).on('input', '#isdMeetingLinkDisplay', function(){ $('#url').val($(this).val()); isdUpdateSummary(); });

    // ---- panel chips: presentation over the real, single #panellist field ----
    var isdPanelMeta = {};
    function isdRegisterPanelMeta(name, email, designation){
        if (!name) return;
        isdPanelMeta[name.trim()] = { email: (email||'').trim(), designation: (designation||'').trim() };
    }
    function isdRemoveFromCcEmail(email){
        var cc = ($('#cc_email').val() || '');
        var arr = cc.split(',').map(function(s){ return s.trim(); })
            .filter(function(s){ return s !== '' && s.toLowerCase() !== email.trim().toLowerCase(); });
        $('#cc_email').val(arr.join(','));
    }
    function isdResetPanelPickerButton(name){
        $('.cp_select_panel[data-title="'+name+'"]').each(function(){
            if ($(this).attr('data-assign') == '1') {
                $(this).removeClass('btn-light-danger').addClass('btn-light-primary').text('Add').attr('data-assign', '0');
            }
        });
    }
    function isdPanelListArray(){
        var raw = ($('#panellist').val() || '').trim();
        if (!raw) return [];
        return raw.split(/[,;]/).map(function(s){ return s.trim(); }).filter(function(s){ return s !== ''; });
    }

    function isdGetInitials(name) {
        if (!name) return '??';
        var parts = name.trim().split(/\s+/);
        if (parts.length >= 2) {
            return (parts[0][0] + parts[1][0]).toUpperCase();
        }
        return name.trim().substring(0, 2).toUpperCase();
    }

    var isdAvatarPalettes = [
        { bg: '#EFF6FF', fg: '#2563EB' },
        { bg: '#FEF3C7', fg: '#D97706' },
        { bg: '#F3E8FF', fg: '#7C3AED' },
        { bg: '#ECFDF5', fg: '#059669' },
        { bg: '#FEE2E2', fg: '#DC2626' }
    ];

    function isdRenderPanelChips(){
        var list = isdPanelListArray();
        var wrap = $('#isdPanelList').empty();
        if (list.length === 0) {
            wrap.append('<div class="isd-panel-empty">No panelists added yet — click "+ Add Interview Panel" above</div>');
        }
        list.forEach(function(entry, idx){
            var meta = isdPanelMeta[entry];
            var initial = isdGetInitials(entry);
            var pal = isdAvatarPalettes[idx % isdAvatarPalettes.length];

            var row = $('<div class="isd-panel-row"></div>');
            row.append('<div class="isd-avatar" style="background:' + pal.bg + ';color:' + pal.fg + ';">' + initial + '</div>');
            
            var info = $('<div class="isd-panel-info"></div>');
            info.append($('<div class="isd-panel-name"></div>').text(entry));
            
            var subText = (meta && meta.email) ? meta.email : ((meta && meta.designation) ? meta.designation : 'Available');
            info.append($('<div class="isd-panel-meta"><span class="isd-dot-avail"></span><span>' + $('<div>').text(subText).html() + '</span></div>'));
            row.append(info);

            if (idx === 0) {
                row.append('<span class="isd-badge-lead">Lead Interviewer</span>');
            } else {
                row.append('<span class="isd-badge-member">Panel Member</span>');
            }

            var del = $('<button type="button" class="isd-panel-del" title="Remove interviewer"><i class="ti ti-trash"></i></button>');
            del.on('click', function(){
                if (meta && meta.email) { isdRemoveFromCcEmail(meta.email); }
                isdResetPanelPickerButton(entry);
                delete isdPanelMeta[entry];
                list.splice(idx, 1);
                $('#panellist').val(list.join(','));
                isdRenderPanelChips();
            });
            row.append(del);
            wrap.append(row);
        });

        // Update panel summary in right sidebar
        var countText = list.length === 0 ? '0 Interviewers' : (list.length === 1 ? '1 Interviewer' : list.length + ' Interviewers');
        $('#isdSumPanelCount').text(countText);
        var $avWrap = $('#isdSumPanelAvatars').empty();
        list.forEach(function(entry, idx){
            var initial = isdGetInitials(entry);
            var pal = isdAvatarPalettes[idx % isdAvatarPalettes.length];
            $avWrap.append('<span class="isd-sum-avatar" style="background:' + pal.bg + ';color:' + pal.fg + ';" title="' + $('<div>').text(entry).html() + '">' + initial + '</span>');
        });

        isdUpdateSummary();
    }
    function isdSyncChipsFromField(){ isdRenderPanelChips(); }

    // ---- Live Ends At calculation ----
    function isdFormatTime12(hours, minutes) {
        var ampm = hours >= 12 ? 'PM' : 'AM';
        var h = hours % 12;
        h = h ? h : 12;
        var m = minutes < 10 ? '0' + minutes : minutes;
        return h + ':' + m + ' ' + ampm;
    }

    function isdComputeEndsAt() {
        var t = $('#interviewTime').val();
        var duration = parseInt($('#isdDuration').val() || '45', 10);
        if (!t) {
            $('#isdEndsAt').val('11:15 AM');
            return { start12: '10:30 AM', end12: '11:15 AM', duration: 45 };
        }
        var parts = t.split(':');
        var h = parseInt(parts[0], 10);
        var m = parseInt(parts[1], 10);
        if (isNaN(h) || isNaN(m)) {
            $('#isdEndsAt').val('11:15 AM');
            return { start12: '10:30 AM', end12: '11:15 AM', duration: 45 };
        }

        var startMinutes = h * 60 + m;
        var endMinutes = (startMinutes + duration) % (24 * 60);

        var endH = Math.floor(endMinutes / 60);
        var endM = endMinutes % 60;

        var startFormatted = isdFormatTime12(h, m);
        var endFormatted = isdFormatTime12(endH, endM);

        $('#isdEndsAt').val(endFormatted);
        return {
            start12: startFormatted,
            end12: endFormatted,
            duration: duration
        };
    }

    function isdFormatDateDisplay(dateStr) {
        if (!dateStr) return 'Not set';
        var parts = dateStr.split('-');
        if (parts.length !== 3) return dateStr;
        var y = parseInt(parts[0], 10);
        var m = parseInt(parts[1], 10) - 1;
        var d = parseInt(parts[2], 10);
        var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        var dayFormatted = d < 10 ? '0' + d : d;
        return dayFormatted + ' ' + (months[m] || '') + ' ' + y;
    }

    function isdFormatTimezoneDisplay() {
        var $sel = $('#timezone option:selected');
        var val = $('#timezone').val();
        if (!val) return 'Not set';
        var text = $sel.text() || '';
        var matchGmt = text.match(/\(([^)]+)\)/);
        var offset = matchGmt ? matchGmt[1] : '';
        if (offset && !offset.toLowerCase().startsWith('utc') && !offset.toLowerCase().startsWith('gmt')) {
            offset = 'UTC ' + offset;
        }
        if (offset) {
            return '(' + offset + ') ' + val;
        }
        return val;
    }

    function isdFormatModeDisplay() {
        var parentVal = $('#interview_mode').val();
        var childVal = $('#interview_mode_lavels').val();
        var childText = $.trim($('#interview_mode_lavels option:selected').text());
        var hasChild = childVal ? true : false;
        
        if (parentVal == '1') {
            return (hasChild && childText && childText !== 'Select Mode') ? ('Telephone (' + childText + ')') : 'Telephone';
        } else if (parentVal == '2') {
            return (hasChild && childText && childText !== 'Select Mode') ? ('Video (' + childText + ')') : 'Video';
        } else if (parentVal == '3') {
            return 'Face to Face';
        }
        return 'Not set';
    }

    // ---- live "Interview Summary" sidebar ----
    function isdUpdateSummary(){
        var d = $('#interviewDate').val();
        $('#isdSumDate').text(isdFormatDateDisplay(d));

        var timeInfo = isdComputeEndsAt();
        if (timeInfo) {
            $('#isdSumTime').text(timeInfo.start12 + ' – ' + timeInfo.end12 + ' (' + timeInfo.duration + ' mins)');
        }

        $('#isdSumTz').text(isdFormatTimezoneDisplay());
        $('#isdSumMode').text(isdFormatModeDisplay());

        var roundText = $('#isdInterviewRound option:selected').text();
        $('#isdSumRound').text((roundText && roundText !== 'Select Round') ? roundText : 'Technical Round');

        function setBadge($el, isChecked) {
            if (isChecked) {
                $el.removeClass('disabled').addClass('enabled').html('<i class="ti ti-circle-check-filled"></i> Enabled');
            } else {
                $el.removeClass('enabled').addClass('disabled').html('<i class="ti ti-circle-x-filled"></i> Disabled');
            }
        }
        setBadge($('#isdSumNotifCandidate'), $('#isdNotifyCandidate').is(':checked'));
        setBadge($('#isdSumNotifPanel'), $('#isdNotifyPanel').is(':checked'));
        setBadge($('#isdSumNotifWhatsapp'), $('#isdNotifyWhatsapp').is(':checked'));
        setBadge($('#isdSumNotifPortal'), $('#isdNotifyPortal').is(':checked'));
    }

    $(document).on('change input', '#interviewDate, #interviewTime, #isdDuration, #timezone, #isdInterviewRound', function(){
        isdComputeEndsAt();
        isdUpdateSummary();
    });

    $(document).on('change', '#isdNotifyCandidate, #isdNotifyPanel, #isdNotifyWhatsapp, #isdNotifyPortal', function(){
        isdUpdateSummary();
    });

    $(document).on('click', '#isdCustomizeEmailBanner', function(){
        $('#send_email_invies').trigger('click');
    });

    $(document).on('click', '#isdIcsCalBtn', function(){
        $('#isdOutlookCalBtn').trigger('click');
    });

    // Native date/time inputs otherwise only open their picker when the tiny
    // built-in icon is clicked — showPicker() opens it from anywhere in the field.
    $(document).on('click', '#interviewDate, #interviewTime', function(){
        if (this.showPicker) { try { this.showPicker(); } catch (e) {} }
    });

    // ---- Add to Calendar: fully client-side, built from the real form fields ----
    function isdBuildEventDetails(){
        var d = $('#interviewDate').val();
        var t = $('#interviewTime').val();
        if (!d || !t) return null;
        var dParts = d.split('-');
        var tParts = t.split(':');
        if (dParts.length !== 3 || tParts.length < 2) return null;
        var yyyy = parseInt(dParts[0], 10), mm = parseInt(dParts[1], 10), dd = parseInt(dParts[2], 10);
        var hh = parseInt(tParts[0], 10), min = parseInt(tParts[1], 10);

        var durationMin = parseInt($('#isdDuration').val() || '45', 10);
        var start = new Date(yyyy, mm - 1, dd, hh, min);
        var end = new Date(start.getTime() + durationMin * 60000);

        function pad(n){ return (n < 10 ? '0' : '') + n; }
        function fmt(dt){
            return dt.getFullYear() + pad(dt.getMonth() + 1) + pad(dt.getDate()) + 'T' + pad(dt.getHours()) + pad(dt.getMinutes()) + '00';
        }

        var candidateName = $.trim($('#isdSumCandidate').text()) || 'Candidate';
        var jobTitle = $.trim($('#isdSumJob').text()) || 'Job';
        var roundName = $('#isdSumRound').text() || 'Interview';
        return {
            title: 'Interview: ' + candidateName + ' - ' + roundName + ' (' + jobTitle + ')',
            start: fmt(start),
            end: fmt(end),
            location: $('#isdMeetingLinkDisplay').val() || ''
        };
    }
    $(document).on('click', '#isdGoogleCalBtn', function(){
        var ev = isdBuildEventDetails();
        if (!ev) { Swal.fire('Required', 'Please select date and time first', 'warning'); return; }
        var url = 'https://calendar.google.com/calendar/render?action=TEMPLATE&text=' + encodeURIComponent(ev.title) +
            '&dates=' + ev.start + '/' + ev.end + '&location=' + encodeURIComponent(ev.location);
        window.open(url, '_blank');
    });
    $(document).on('click', '#isdOutlookCalBtn', function(){
        var ev = isdBuildEventDetails();
        if (!ev) { Swal.fire('Required', 'Please select date and time first', 'warning'); return; }
        var ics = 'BEGIN:VCALENDAR\r\nVERSION:2.0\r\nBEGIN:VEVENT\r\nDTSTART:' + ev.start + '\r\nDTEND:' + ev.end +
            '\r\nSUMMARY:' + ev.title + '\r\nLOCATION:' + ev.location + '\r\nEND:VEVENT\r\nEND:VCALENDAR';
        var blob = new Blob([ics], { type: 'text/calendar' });
        var link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = 'interview.ics';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    });

    $(document).ready(function(){
        var curMode = $('#interview_mode').val() || '2';
        $('#interview_mode').val(curMode);
        $('.isd-mode-card').removeClass('is-active');
        $('.isd-mode-card[data-family-val="' + curMode + '"]').addClass('is-active');

        var update = "{{ @$update }}";
        if (!update) {
            getInterviewModeChild(curMode, {{ $job_id }});
        }

        isdComputeEndsAt();
        isdRenderPanelChips();
        isdUpdateSummary();
        if (typeof window.isdShellSetStep === 'function') {
            window.isdShellSetStep('main');
        }
    });
</script>
