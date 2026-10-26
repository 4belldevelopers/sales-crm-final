@extends('layouts.superadmin_layout')

@php
    $isEdit = (@$tokenID > 0);
    $entryType = (int) ($entryType ?? 1);
    $pageTitle = $isEdit ? 'Edit Job Description' : ($entryType === 2 ? 'Bulk Import via Google Sheet' : 'Create Job Description');
    $pageSubtitle = $isEdit 
        ? 'Update job specification, requirements, and review status in the master catalog.' 
        : ($entryType === 2 
            ? 'Sync and import multiple verified job descriptions from a connected Google Spreadsheet.' 
            : 'Fill in job details, experience requirements, skills, and full description.');
@endphp

@section('header_title')
<div class="jd-header-title d-flex flex-column justify-content-center my-0">
    <h1 class="d-flex align-items-center text-gray-900 fw-bold my-0">{{ $pageTitle }}</h1>
    <span class="header-subtitle">{{ $pageSubtitle }}</span>
</div>
@endsection

@push('head-script')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
/* ==========================================================================
   Super Admin — JD Create & Import Isolated Enterprise Styles
   Design Standard: Modern Card + Sub-Card Enterprise Design System
   Screen Target: 15.6" screens (1366x768 & 1920x1080) with zero horizontal overflow
   ========================================================================== */

:root {
    --jd-primary: #009ef7;
    --jd-primary-hover: #0088d6;
    --jd-primary-light: #f1faff;
    --jd-text-dark: #0f172a;
    --jd-text-muted: #64748b;
    --jd-border-color: #e2e8f0;
    --jd-card-bg: #ffffff;
    --jd-bg-page: #f8fafc;
}

body {
    overflow-x: hidden;
    background-color: var(--jd-bg-page);
    font-family: 'Poppins', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
}

/* Top Navigation Header Title Stack & Subtitle */
.jd-header-title h1 {
    font-family: 'Poppins', sans-serif !important;
    font-size: 1.35rem !important;
    font-weight: 600 !important;
    color: #181c32 !important;
    line-height: 1.2 !important;
    margin: 0 !important;
}
.jd-header-title .header-subtitle {
    font-size: 0.8rem !important;
    font-weight: 500 !important;
    color: #7e8299 !important;
    line-height: 1.2 !important;
    margin-top: 3px !important;
}
@media (max-width: 991.98px) {
    .jd-header-title h1 {
        font-size: 1.1rem !important;
    }
    .jd-header-title .header-subtitle {
        display: none !important;
    }
}

/* Collapse redundant legacy toolbar gap */
#kt_header_search {
    display: none !important;
}
#kt_header {
    border-bottom: 1px solid #eff2f5 !important;
}
#kt_toolbar {
    display: none !important;
}
@media (min-width: 992px) {
    #jd-create-page-container {
        margin-top: -48px !important;
    }
}
@media (max-width: 991.98px) {
    #jd-create-page-container {
        margin-top: -38px !important;
    }
}

/* Sticky Top Action Toolbar Card */
.jd-top-nav-card {
    background: #ffffff;
    border: 1px solid var(--jd-border-color);
    border-radius: 12px;
    padding: 12px 20px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    position: sticky !important;
    top: 70px !important;
    z-index: 95 !important;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05) !important;
    transition: top 0.2s ease, box-shadow 0.2s ease;
}
@media (max-width: 991.98px) {
    .jd-top-nav-card {
        top: 60px !important;
    }
}
.jd-nav-left {
    display: flex;
    align-items: center;
    gap: 14px;
}
.jd-back-btn {
    display: inline-flex;
    align-items: center;
    padding: 8px 14px;
    background-color: #f1f5f9;
    color: #334155;
    font-size: 13px;
    font-weight: 600;
    border-radius: 8px;
    text-decoration: none;
    transition: all 0.15s ease;
    border: 1px solid #e2e8f0;
}
.jd-back-btn:hover {
    background-color: #e2e8f0;
    color: var(--jd-text-dark);
}
.jd-nav-right {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}
.jd-mode-pills-wrap {
    display: inline-flex;
    align-items: center;
    gap: 8px;
}
.jd-nav-divider {
    width: 1px;
    height: 26px;
    background-color: #e2e8f0;
    margin: 0 2px;
}
.jd-action-buttons-wrap {
    display: inline-flex;
    align-items: center;
    gap: 8px;
}
.jd-mode-pill {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    font-size: 13px;
    font-weight: 600;
    border-radius: 8px;
    text-decoration: none;
    transition: all 0.15s ease;
    border: 1px solid transparent;
    line-height: 1.4;
    white-space: nowrap;
}
.jd-mode-pill.active {
    background-color: #009ef7;
    color: #ffffff !important;
    box-shadow: 0 2px 6px rgba(0, 158, 247, 0.25);
}
.jd-mode-pill:not(.active) {
    background-color: #f8fafc;
    color: #64748b;
    border-color: #e2e8f0;
}
.jd-mode-pill:not(.active):hover {
    background-color: #f1f5f9;
    color: #0f172a;
}

/* Form Cards & Sub-Card Architecture */
.jd-card-container {
    background: #ffffff;
    border: 1px solid var(--jd-border-color);
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
    margin-bottom: 24px;
    overflow: hidden;
}
.jd-card-header {
    padding: 18px 24px;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: space-between;
    background-color: #ffffff;
}
.jd-card-title {
    font-size: 15px;
    font-weight: 600;
    color: #0f172a;
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 0;
}
.jd-card-body {
    padding: 24px;
}

/* Form Controls & Labels */
.jd-form-group {
    margin-bottom: 20px;
}
.jd-label {
    font-size: 13px;
    font-weight: 600;
    color: #334155;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 4px;
}
.jd-label .req {
    color: #ef4444;
    font-weight: 700;
}
.jd-input, .jd-select {
    width: 100%;
    padding: 10px 14px;
    font-size: 13px;
    font-weight: 500;
    color: #1e293b;
    background-color: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    transition: all 0.15s ease;
    outline: none;
}
.jd-input:focus, .jd-select:focus {
    border-color: #009ef7;
    box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.15);
}
.jd-input::placeholder {
    color: #94a3b8;
}
.jd-helper-text {
    font-size: 12px;
    color: #64748b;
    margin-top: 6px;
    display: block;
}

/* Tagify Styling */
.tagify {
    border-radius: 8px !important;
    border-color: #cbd5e1 !important;
    padding: 4px 8px !important;
    min-height: 44px !important;
}
.tagify:focus-within {
    border-color: #009ef7 !important;
    box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.15) !important;
}
.tagify__tag {
    background-color: #e0f2fe !important;
    color: #0369a1 !important;
    border-radius: 6px !important;
    font-weight: 500 !important;
    font-size: 12px !important;
}
.tagify__tag-text {
    color: #0369a1 !important;
}

/* Instruction Sub-Cards for Google Sheet Mode */
.jd-instruction-box {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 18px;
}
.jd-step-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 10px;
    background: #e0f2fe;
    color: #0284c7;
    font-size: 12px;
    font-weight: 700;
    border-radius: 6px;
    margin-bottom: 10px;
}
.jd-service-email-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    padding: 10px 14px;
    margin-top: 10px;
    gap: 12px;
}
.jd-email-text {
    font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
    font-size: 13px;
    font-weight: 600;
    color: #0f172a;
    word-break: break-all;
}
.jd-copy-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    background-color: #4f46e5;
    color: #ffffff;
    border: none;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: background-color 0.15s ease;
    white-space: nowrap;
}
.jd-copy-btn:hover {
    background-color: #4338ca;
}
.jd-copy-btn.copied {
    background-color: #10b981;
}

/* Form Action Buttons */
.jd-form-actions {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 12px;
    padding: 16px 24px;
    background-color: #f8fafc;
    border-top: 1px solid #e2e8f0;
    border-radius: 0 0 12px 12px;
}
.jd-btn-submit {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 18px;
    background-color: #009ef7;
    color: #ffffff;
    font-size: 13px;
    font-weight: 600;
    border-radius: 8px;
    border: none;
    cursor: pointer;
    transition: all 0.15s ease;
    box-shadow: 0 2px 4px rgba(0, 158, 247, 0.2);
    line-height: 1.4;
    white-space: nowrap;
}
.jd-btn-submit:hover {
    background-color: #0088d6;
    color: #ffffff;
}
.jd-btn-submit:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}
.jd-btn-cancel {
    display: inline-flex;
    align-items: center;
    padding: 8px 16px;
    background-color: #ffffff;
    color: #475569;
    font-size: 13px;
    font-weight: 600;
    border-radius: 8px;
    border: 1px solid #cbd5e1;
    text-decoration: none;
    transition: all 0.15s ease;
    line-height: 1.4;
    white-space: nowrap;
}
.jd-btn-cancel:hover {
    background-color: #f1f5f9;
    color: #0f172a;
}

/* Result Feedback Badges */
.jd-result-banner {
    display: none;
    margin-bottom: 20px;
    padding: 14px 18px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 500;
}
.jd-result-banner.success {
    background-color: #ecfdf5;
    border: 1px solid #a7f3d0;
    color: #065f46;
}
.jd-result-banner.warning {
    background-color: #fffbeb;
    border: 1px solid #fde68a;
    color: #92400e;
}
.jd-result-banner.danger {
    background-color: #fef2f2;
    border: 1px solid #fecaca;
    color: #991b1b;
}

/* Guide Table for Sheet Columns */
.jd-column-guide-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
    font-size: 12px;
}
.jd-column-guide-table th {
    background-color: #f1f5f9;
    color: #475569;
    font-weight: 600;
    padding: 8px 12px;
    border: 1px solid #e2e8f0;
    text-align: left;
}
.jd-column-guide-table td {
    padding: 8px 12px;
    border: 1px solid #e2e8f0;
    color: #334155;
}
</style>
@endpush

@section('content')
<div class="container-fluid px-4 pt-3 pb-4" id="jd-create-page-container">

    <!-- Main Form Element wrapping Top Action Toolbar and Input Cards -->
    <form id="client_new_form" action="javascript:void(0);" method="POST">
        @csrf
        <input type="hidden" name="entryType" value="{{ $entryType }}" id="entryType">
        <input type="hidden" name="tokenID" value="{{ @$tokenID }}" id="tokenID">

        <!-- Top Action & Navigation Toolbar -->
        <div class="jd-top-nav-card">
            <div class="jd-nav-left">
                <a href="{{ route('superadmin.company.jdlist', 'pending') }}" class="jd-back-btn">
                    <i class="fa fa-arrow-left me-2"></i> Back to JD List
                </a>
                @if($entryType == 2)
                    <span class="badge badge-light-success fs-7 fw-semibold px-3 py-2">
                        <i class="fa fa-google text-success me-1"></i> Google Sheet Import
                    </span>
                @else
                    <span class="badge badge-light-primary fs-7 fw-semibold px-3 py-2">
                        <i class="fa fa-pencil text-primary me-1"></i> {{ $isEdit ? 'Edit Mode' : 'Manual Add Mode' }}
                    </span>
                @endif
            </div>

            <div class="jd-nav-right">
                <button type="submit" id="client_new_submit" class="jd-btn-submit">
                    <span class="indicator-label-client">
                        @if($entryType == 2)
                            <i class="fa fa-cloud-download me-1"></i> Sync & Import JDs
                        @else
                            <i class="fa fa-check me-1"></i> {{ $isEdit ? 'Update Job Description' : 'Save & Publish JD' }}
                        @endif
                    </span>
                    <span class="indicator-progress-client" style="display: none;">
                        <i class="fa fa-spinner fa-spin me-1"></i> {{ $entryType == 2 ? 'Syncing JDs, please wait...' : 'Saving, please wait...' }}
                    </span>
                </button>
            </div>
        </div>

        <!-- Live Feedback Notification Banners -->
        <div id="successButtonclient" class="jd-result-banner success">
            <i class="fa fa-check-circle me-2"></i> <span id="success-banner-text">Operation completed successfully.</span>
        </div>
        <div id="duplicaterButtonclient" class="jd-result-banner warning">
            <i class="fa fa-exclamation-triangle me-2"></i> <span id="duplicate-banner-text">Duplicate records identified.</span>
        </div>
        <div id="errorButtonclient" class="jd-result-banner danger">
            <i class="fa fa-times-circle me-2"></i> <span id="error-banner-text">Some rows could not be imported.</span>
        </div>

        @if($entryType == 2)
        <!-- ================================================================== -->
        <!-- FORM 2: BULK IMPORT VIA GOOGLE SHEET                              -->
        <!-- ================================================================== -->
        <div class="row g-4">
            <!-- Left Column: Google Sheet URL & Configuration -->
            <div class="col-lg-6">
                <div class="jd-card-container">
                    <div class="jd-card-header">
                        <h3 class="jd-card-title">
                            <i class="fa fa-table text-success"></i> Google Sheet Connection
                        </h3>
                    </div>
                    <div class="jd-card-body">
                        <!-- Google Sheet Link -->
                        <div class="jd-form-group">
                            <label class="jd-label" for="googlesheetlink">
                                <span>Google Sheet Full URL</span>
                            </label>
                            <input type="text" 
                                   class="jd-input" 
                                   id="googlesheetlink" 
                                   name="googlesheetlink" 
                                   value="https://docs.google.com/spreadsheets/d/1aHkQvqnPs2L4ajhMhZLqr9VjvEY7hiU6bPjpueS6XLg/edit?gid=0#gid=0"
                                   placeholder="https://docs.google.com/spreadsheets/d/{SPREADSHEET_ID}/edit">
                            <span class="jd-helper-text">
                                <i class="fa fa-magic text-primary me-1"></i> Paste your full spreadsheet link here; the ID will be automatically extracted.
                            </span>
                        </div>

                        <!-- Google Sheet ID -->
                        <div class="jd-form-group">
                            <label class="jd-label" for="googlesheetid">
                                <span>Google Sheet ID</span> <span class="req">*</span>
                            </label>
                            <input type="text" 
                                   class="jd-input font-monospace" 
                                   id="googlesheetid" 
                                   name="googlesheetid" 
                                   value="1aHkQvqnPs2L4ajhMhZLqr9VjvEY7hiU6bPjpueS6XLg"
                                   placeholder="e.g. 1aHkQvqnPs2L4ajhMhZLqr9VjvEY7hiU6bPjpueS6XLg" 
                                   required>
                            <span class="jd-helper-text">Unique alphanumeric identifier found in your sheet URL between /d/ and /edit.</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Step-by-Step Instructions & Service Account -->
            <div class="col-lg-6">
                <div class="jd-card-container">
                    <div class="jd-card-header">
                        <h3 class="jd-card-title">
                            <i class="fa fa-info-circle text-primary"></i> Setup & Permission Instructions
                        </h3>
                    </div>
                    <div class="jd-card-body">
                        <!-- Step 1: Share Access -->
                        <div class="jd-instruction-box">
                            <span class="jd-step-badge"><i class="fa fa-user-plus"></i> Step 1: Grant Access</span>
                            <p class="fs-7 text-dark mb-2">
                                Open your Google Sheet, click the blue <strong>Share</strong> button, and add this service account email with <strong>Viewer</strong> access:
                            </p>
                            <div class="jd-service-email-bar">
                                <span class="jd-email-text" id="textToCopy">get-google-api-data@get-goolge-sheet.iam.gserviceaccount.com</span>
                                <button type="button" class="jd-copy-btn" id="copy-service-btn" onclick="copyServiceEmail()">
                                    <i class="fa fa-clone"></i> <span id="copy-btn-text">Copy Email</span>
                                </button>
                            </div>
                        </div>

                        <!-- Step 2: Expected Columns Structure -->
                        <div class="jd-instruction-box mb-0">
                            <span class="jd-step-badge"><i class="fa fa-columns"></i> Step 2: Spreadsheet Format</span>
                            <p class="fs-7 text-dark mb-2">
                                Ensure your active sheet (Sheet1) contains these headers starting in row 1:
                            </p>
                            <div class="table-responsive">
                                <table class="jd-column-guide-table">
                                    <thead>
                                        <tr>
                                            <th>Column</th>
                                            <th>Field Name</th>
                                            <th>Sample Value</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><strong>Col A (1)</strong></td>
                                            <td>Title <span class="text-danger">*</span></td>
                                            <td>Senior Laravel Developer</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Col B (2)</strong></td>
                                            <td>JD Details</td>
                                            <td>Full responsibilities & requirements...</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Col C (3)</strong></td>
                                            <td>Minimum Exp</td>
                                            <td>2</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Col D (4)</strong></td>
                                            <td>Maximum Exp</td>
                                            <td>5</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Col E (5)</strong></td>
                                            <td>Skills <span class="text-danger">*</span></td>
                                            <td>PHP, Laravel, MySQL, Docker</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @else
        <!-- ================================================================== -->
        <!-- FORM 1: MANUALLY ADD / EDIT JOB DESCRIPTION                       -->
        <!-- ================================================================== -->
        <div class="jd-card-container">
            <div class="jd-card-header">
                <h3 class="jd-card-title">
                    <i class="fa fa-briefcase text-primary"></i> Basic Job Specification
                </h3>
            </div>
            <div class="jd-card-body">
                <div class="row g-4">
                    <!-- Job Title -->
                    <div class="col-md-8">
                        <div class="jd-form-group mb-0">
                            <label class="jd-label" for="title">
                                <span>Job Title</span> <span class="req">*</span>
                            </label>
                            <input type="text" 
                                   class="jd-input" 
                                   id="title" 
                                   name="title" 
                                   value="{{ (@$jd_details) ? $jd_details->title : '' }}" 
                                   placeholder="e.g. Senior Full Stack Laravel Developer" 
                                   required>
                            <span class="jd-helper-text">Official job title that will be displayed in the ATS job catalog.</span>
                        </div>
                    </div>

                    <!-- Initial Status -->
                    <div class="col-md-4">
                        <div class="jd-form-group mb-0">
                            <label class="jd-label" for="jd_status">
                                <span>Initial Review Status</span>
                            </label>
                            <select class="jd-select" name="jd_status" id="jd_status">
                                <option value="0" {{ (@$jd_details && @$jd_details->status == 0) ? 'selected' : '' }}>Pending Review</option>
                                <option value="1" {{ (@$jd_details && @$jd_details->status == 1) ? 'selected' : '' }}>Approved</option>
                                <option value="2" {{ (@$jd_details && @$jd_details->status == 2) ? 'selected' : '' }}>Declined</option>
                            </select>
                            <span class="jd-helper-text">Sets which list tab this JD appears under upon creation.</span>
                        </div>
                    </div>

                    <!-- Minimum Experience -->
                    <div class="col-md-6">
                        <div class="jd-form-group mb-0">
                            <label class="jd-label" for="minimum_experience">
                                <span>Minimum Experience</span>
                            </label>
                            <select class="jd-select" name="minimum_experience" id="minimum_experience">
                                <option value="" disabled {{ !@$jd_details ? 'selected' : '' }}>Select Experience</option>
                                <option value="0" {{ (@$jd_details && @$jd_details->minimum_exp == 0) ? 'selected' : '' }}>Freshers (0 Years)</option>
                                @for($i = 1; $i <= 30; $i++)
                                    @php
                                        $yearText = ($i > 1) ? 'Years' : 'Year';
                                        $selected = (@$jd_details && (int)$jd_details->minimum_exp === $i) ? 'selected' : '';
                                    @endphp
                                    <option value="{{ $i }}" {{ $selected }}>{{ $i }} {{ $yearText }}</option>
                                @endfor
                            </select>
                            <span class="jd-helper-text">Baseline minimum years of industry experience.</span>
                        </div>
                    </div>

                    <!-- Maximum Experience -->
                    <div class="col-md-6">
                        <div class="jd-form-group mb-0">
                            <label class="jd-label" for="maximum_experience">
                                <span>Maximum Experience</span>
                            </label>
                            <select class="jd-select" name="maximum_experience" id="maximum_experience">
                                <option value="" disabled {{ !@$jd_details ? 'selected' : '' }}>Select Experience</option>
                                <option value="0" {{ (@$jd_details && @$jd_details->maxmum_exp == 0) ? 'selected' : '' }}>Freshers (0 Years)</option>
                                @for($i = 1; $i <= 30; $i++)
                                    @php
                                        $yearText = ($i > 1) ? 'Years' : 'Year';
                                        $selected = (@$jd_details && (int)$jd_details->maxmum_exp === $i) ? 'selected' : '';
                                    @endphp
                                    <option value="{{ $i }}" {{ $selected }}>{{ $i }} {{ $yearText }}</option>
                                @endfor
                            </select>
                            <span class="jd-helper-text">Maximum experience ceiling for candidate qualification.</span>
                        </div>
                    </div>

                    <!-- Skills Required (Tagify) -->
                    <div class="col-12">
                        <div class="jd-form-group mb-0">
                            <label class="jd-label" for="kt_tagify_2">
                                <span>Required Skills</span> <span class="req">*</span>
                            </label>
                            <input type="text" 
                                   class="jd-input" 
                                   name="skills" 
                                   value="{{ (@$jd_details) ? $jd_details->skills : '' }}" 
                                   id="kt_tagify_2" 
                                   placeholder="Type skill and press Enter (e.g. Laravel, React, MySQL)" 
                                   required>
                            <span class="jd-helper-text">Skills will be rendered as searchable pill badges across the ATS. Press Enter or comma to add.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2: Full Job Description Details (CKEditor) -->
        <div class="jd-card-container">
            <div class="jd-card-header">
                <h3 class="jd-card-title">
                    <i class="fa fa-file-text-o text-primary"></i> Detailed Job Description
                </h3>
            </div>
            <div class="jd-card-body">
                <div class="jd-form-group mb-0">
                    <textarea id="editor1" name="jd_details" rows="12">{{ (@$jd_details) ? $jd_details->jd : '' }}</textarea>
                    <span class="jd-helper-text mt-2">Include responsibilities, required qualifications, nice-to-haves, and job overview.</span>
                </div>
        </div>
        @endif

    </form>
</div>
@endsection

@push('footer-script')
<script src="{{ asset('demo/ckeditor/ckeditor.js') }}"></script>
<script>
(function() {
    'use strict';

    // 1. Initialize Tagify on Skills input if present
    var skillsInput = document.querySelector("#kt_tagify_2");
    if (skillsInput && typeof Tagify !== 'undefined') {
        new Tagify(skillsInput, {
            whitelist: [],
            maxTags: 25,
            dropdown: {
                maxItems: 20,
                classname: "tags-look",
                enabled: 0,
                closeOnSelect: false
            }
        });
    }

    // 2. Initialize CKEditor for rich job description
    if (typeof CKEDITOR !== 'undefined' && document.getElementById('editor1')) {
        CKEDITOR.config.height = 320;
        CKEDITOR.config.width = 'auto';
        CKEDITOR.config.toolbar = [
            { name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
            { name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote' ] },
            { name: 'styles', items: [ 'Format', 'Font', 'FontSize' ] },
            { name: 'colors', items: [ 'TextColor', 'BGColor' ] },
            { name: 'insert', items: [ 'Table', 'HorizontalRule' ] },
            { name: 'tools', items: [ 'Maximize' ] }
        ];
        CKEDITOR.replace('editor1');
    }

    // 3. Automated Google Sheet ID Extractor from pasted URL
    $('#googlesheetlink').on('input paste', function() {
        var self = this;
        setTimeout(function() {
            var url = $(self).val().trim();
            if (url) {
                // Match /spreadsheets/d/([a-zA-Z0-9-_]+)
                var match = url.match(/\/spreadsheets\/d\/([a-zA-Z0-9-_]+)/);
                if (match && match[1]) {
                    $('#googlesheetid').val(match[1]).trigger('input');
                }
            }
        }, 100);
    });

    // 4. Experience Validation Check
    function validateExperienceRange() {
        var minVal = parseInt($('#minimum_experience').val());
        var maxVal = parseInt($('#maximum_experience').val());
        if (!isNaN(minVal) && !isNaN(maxVal)) {
            if (minVal > maxVal) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Experience Range Warning',
                        text: 'Maximum experience must be greater than or equal to minimum experience.',
                        icon: 'warning',
                        confirmButtonColor: '#009ef7'
                    });
                } else {
                    alert('Maximum experience must be greater than or equal to minimum experience.');
                }
                $('#maximum_experience').val('');
                return false;
            }
        }
        return true;
    }
    $('#minimum_experience, #maximum_experience').on('change', validateExperienceRange);

    // 5. AJAX Form Submission
    $(document).on('submit', '#client_new_form', function(e) {
        e.preventDefault();

        // Update CKEditor instance content to textarea if active
        if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances.editor1) {
            CKEDITOR.instances.editor1.updateElement();
        }

        var form = $(this);
        var entryType = parseInt($('#entryType').val()) || 1;
        var submitBtn = $('#client_new_submit');
        var labelSpan = $('.indicator-label-client');
        var progressSpan = $('.indicator-progress-client');

        // Validation for manual mode
        if (entryType !== 2) {
            if (!validateExperienceRange()) return false;
            var titleVal = $('#title').val().trim();
            if (!titleVal) {
                showWarning('Job Title is required.');
                return false;
            }
            var skillsVal = $('#kt_tagify_2').val().trim();
            if (!skillsVal) {
                showWarning('Please enter at least one skill.');
                return false;
            }
        } else {
            var sheetId = $('#googlesheetid').val().trim();
            if (!sheetId) {
                showWarning('Google Sheet ID is required.');
                return false;
            }
        }

        // Toggle loading state
        labelSpan.hide();
        progressSpan.show();
        submitBtn.prop('disabled', true);

        var formData = form.serialize();

        $.ajax({
            type: "POST",
            url: '{{ route("superadmin.company.jdgoogleSheetInsertdataInDatabase") }}?_token={{ csrf_token() }}',
            data: formData,
            dataType: 'json',
            success: function(response) {
                submitBtn.prop('disabled', false);
                labelSpan.show();
                progressSpan.hide();

                if (response.status === 'error') {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Import Error',
                            text: response.message || 'The spreadsheet is invalid or inaccessible.',
                            icon: 'error',
                            confirmButtonColor: '#ef4444'
                        });
                    } else {
                        alert(response.message);
                    }
                    return false;
                }

                // Cross-tab broadcast to notify catalog list tabs immediately
                if ('BroadcastChannel' in window) {
                    try {
                        var bc = new BroadcastChannel('jero_jd_pending_sync');
                        bc.postMessage({ type: 'INVALIDATE_CACHE', timestamp: Date.now() });
                    } catch (e) {}
                }
                try {
                    localStorage.setItem('jero_sa_jd_invalidation_trigger', Date.now().toString());
                } catch (e) {}

                // Success Handling
                if (entryType === 2) {
                    var doneCount = response.done || 0;
                    var dupCount = response.duplicate || 0;
                    var pendCount = response.pendind || 0;

                    var successMsg = doneCount + " Job Descriptions imported successfully!";
                    if (dupCount > 0) successMsg += " (" + dupCount + " duplicates skipped)";

                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Import Complete',
                            text: successMsg,
                            icon: 'success',
                            confirmButtonColor: '#10b981',
                            confirmButtonText: 'View Pending JDs'
                        }).then(function() {
                            window.location.href = '{{ route("superadmin.company.jdlist", "pending") }}';
                        });
                    } else {
                        alert(successMsg);
                        window.location.href = '{{ route("superadmin.company.jdlist", "pending") }}';
                    }
                } else {
                    var successText = '{{ @$tokenID > 0 ? "Job Description updated successfully!" : "Job Description created successfully!" }}';
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Saved Successfully',
                            text: successText,
                            icon: 'success',
                            confirmButtonColor: '#009ef7',
                            confirmButtonText: 'Go to JD List'
                        }).then(function() {
                            window.location.href = '{{ route("superadmin.company.jdlist", "pending") }}';
                        });
                    } else {
                        alert(successText);
                        window.location.href = '{{ route("superadmin.company.jdlist", "pending") }}';
                    }
                }
            },
            error: function(xhr) {
                submitBtn.prop('disabled', false);
                labelSpan.show();
                progressSpan.hide();

                var errText = 'An error occurred while saving. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    var errs = xhr.responseJSON.errors;
                    if (errs.title) errText = errs.title[0] || errs.title;
                    else if (errs.skills) errText = errs.skills[0] || errs.skills;
                    else if (errs.googlesheetid) errText = errs.googlesheetid[0] || errs.googlesheetid;
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errText = xhr.responseJSON.message;
                }

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Validation Error',
                        text: errText,
                        icon: 'warning',
                        confirmButtonColor: '#ef4444'
                    });
                } else {
                    alert(errText);
                }
            }
        });
    });

    function showWarning(msg) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Required Field',
                text: msg,
                icon: 'warning',
                confirmButtonColor: '#009ef7'
            });
        } else {
            alert(msg);
        }
    }

})();

// Global copy helper for service email
function copyServiceEmail() {
    var email = document.getElementById("textToCopy").innerText.trim();
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(email).then(function() {
            setCopyFeedback();
        }).catch(function() {
            fallbackCopy(email);
        });
    } else {
        fallbackCopy(email);
    }
}

function fallbackCopy(text) {
    var tempInput = document.createElement("input");
    tempInput.value = text;
    document.body.appendChild(tempInput);
    tempInput.select();
    document.execCommand("copy");
    document.body.removeChild(tempInput);
    setCopyFeedback();
}

function setCopyFeedback() {
    var btn = document.getElementById("copy-service-btn");
    var txt = document.getElementById("copy-btn-text");
    if (btn && txt) {
        btn.classList.add("copied");
        txt.innerText = "Copied!";
        setTimeout(function() {
            btn.classList.remove("copied");
            txt.innerText = "Copy Email";
        }, 2000);
    }
}
</script>
@endpush
