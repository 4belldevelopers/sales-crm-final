@extends('layouts.superadmin_layout')

@push('head-script')
    <style>
        body {
            overflow-x: hidden;
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: #F8FAFC;
        }

        /* Top Action Bar - Compact 15.6" Header */
        .template-header-bar {
            background: #FFFFFF;
            border-bottom: 1px solid #E2E8F0;
            padding: 10px 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
        }

        .header-icon-box {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background: #FFFBEB;
            border: 1px solid #FDE68A;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #D97706;
            flex-shrink: 0;
        }

        .btn-save-yellow {
            background-color: #F59E0B !important;
            border-color: #F59E0B !important;
            color: #111827 !important;
            font-weight: 700 !important;
            font-size: 11.5px !important;
            padding: 6px 16px !important;
            border-radius: 6px !important;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 1px 3px rgba(245, 158, 11, 0.2);
            transition: all 0.15s ease-in-out;
        }

        .btn-save-yellow:hover {
            background-color: #D97706 !important;
            border-color: #D97706 !important;
            color: #FFFFFF !important;
            transform: translateY(-1px);
            box-shadow: 0 3px 8px rgba(217, 119, 6, 0.28);
        }

        .btn-cancel-light {
            background: #FFFFFF !important;
            border: 1px solid #D1D5DB !important;
            color: #374151 !important;
            font-weight: 600 !important;
            font-size: 11.5px !important;
            padding: 6px 14px !important;
            border-radius: 6px !important;
            transition: all 0.15s ease-in-out;
            text-decoration: none !important;
        }

        .btn-cancel-light:hover {
            background: #F3F4F6 !important;
            color: #111827 !important;
        }

        /* Section Cards - Compact Spacing */
        .section-card {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 9px;
            padding: 13px 16px;
            margin-bottom: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
            position: relative;
        }

        .section-header {
            display: flex;
            align-items: center;
            gap: 9px;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 1px solid #F1F5F9;
        }

        .section-header-icon {
            width: 30px;
            height: 30px;
            border-radius: 7px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .icon-purple {
            background: #F5F3FF;
            color: #7C3AED;
            border: 1px solid #EDE9FE;
        }

        .icon-blue {
            background: #EFF6FF;
            color: #2563EB;
            border: 1px solid #DBEAFE;
        }

        .icon-gray {
            background: #F8FAFC;
            color: #475569;
            border: 1px solid #E2E8F0;
        }

        .icon-amber {
            background: #FFFBEB;
            color: #D97706;
            border: 1px solid #FDE68A;
        }

        .section-title {
            font-size: 13.5px;
            font-weight: 700;
            color: #0F172A;
            margin: 0;
            line-height: 1.25;
        }

        .section-subtitle {
            font-size: 10.5px;
            color: #64748B;
            margin: 1px 0 0 0;
        }

        /* Form Inputs */
        .form-label-custom {
            font-size: 11px;
            font-weight: 600;
            color: #1E293B;
            margin-bottom: 3px;
            display: flex;
            align-items: center;
            gap: 3px;
        }

        .form-label-custom .req {
            color: #EF4444;
        }

        .form-control-custom,
        .form-select-custom {
            height: 32px;
            border-radius: 6px;
            border: 1px solid #CBD5E1;
            font-size: 11.5px;
            color: #0F172A;
            padding: 4px 10px;
            background-color: #FFFFFF;
            transition: all 0.15s ease-in-out;
            width: 100%;
        }

        .form-control-custom:focus,
        .form-select-custom:focus {
            border-color: #F59E0B;
            box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.12);
            outline: none;
            background-color: #FFFFFF;
        }

        .form-hint {
            font-size: 10px;
            color: #64748B;
            margin-top: 2px;
            display: block;
        }

        /* Custom Editor Toolbar Strip */
        .editor-custom-toolbar {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 3px;
            padding: 5px 8px;
            background: #F8FAFC;
            border: 1px solid #CBD5E1;
            border-bottom: none;
            border-top-left-radius: 6px;
            border-top-right-radius: 6px;
        }

        .ed-tool-btn {
            background: transparent;
            border: 1px solid transparent;
            border-radius: 4px;
            padding: 2px 5px;
            font-size: 11.5px;
            color: #475569;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            line-height: 1;
            transition: all 0.1s ease;
        }

        .ed-tool-btn:hover {
            background: #E2E8F0;
            color: #0F172A;
        }

        .ed-tool-sep {
            width: 1px;
            height: 14px;
            background: #CBD5E1;
            margin: 0 2px;
        }

        .ed-select-style {
            height: 24px;
            font-size: 11px;
            border-radius: 4px;
            border: 1px solid #CBD5E1;
            background: #FFFFFF;
            padding: 0 5px;
            color: #334155;
            cursor: pointer;
        }

        .ed-personalize-btn {
            background: #EFF6FF;
            color: #1D4ED8;
            border: 1px solid #BFDBFE;
            font-size: 10.5px;
            font-weight: 600;
            padding: 2px 7px;
            border-radius: 4px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 3px;
            margin-left: auto;
            transition: all 0.15s ease;
        }

        .ed-personalize-btn:hover {
            background: #DBEAFE;
            color: #1E3A8A;
        }

        /* Editor Container wrapper */
        .editor-wrapper {
            border-bottom-left-radius: 6px;
            border-bottom-right-radius: 6px;
            overflow: hidden;
            border: 1px solid #CBD5E1;
        }

        .cke_chrome {
            border: none !important;
            box-shadow: none !important;
        }

        .editor-footer-info {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 4px 8px;
            background: #F8FAFC;
            border-top: 1px solid #E2E8F0;
            font-size: 10px;
            color: #64748B;
        }

        /* Right Panel: Available Variables */
        .variables-panel {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 9px;
            padding: 14px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
            position: sticky;
            top: 14px;
            display: flex;
            flex-direction: column;
        }

        .var-search-input {
            height: 28px;
            border-radius: 6px;
            border: 1px solid #CBD5E1;
            font-size: 11px;
            padding: 2px 8px 2px 26px;
            width: 100%;
            background: #FFFFFF;
            transition: all 0.15s ease;
        }

        .var-search-input:focus {
            border-color: #F59E0B;
            outline: none;
            box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.12);
        }

        .var-search-wrap {
            position: relative;
        }

        .var-search-wrap .search-icon {
            position: absolute;
            left: 8px;
            top: 50%;
            transform: translateY(-50%);
            color: #94A3B8;
            font-size: 11px;
        }

        /* Category Pill Tabs - Clean Multi-Row Wrap without Horiz Scrollbar */
        .var-pill-tabs {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 4px 6px;
            margin-bottom: 8px;
            padding-bottom: 6px;
            border-bottom: 1px solid #F1F5F9;
        }

        .var-pill-btn {
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 14px;
            padding: 3px 9px;
            font-size: 10.5px;
            font-weight: 500;
            color: #475569;
            white-space: nowrap;
            cursor: pointer;
            transition: all 0.12s ease-in-out;
            line-height: 1.25;
        }

        .var-pill-btn:hover {
            background: #F1F5F9;
            color: #0F172A;
            border-color: #CBD5E1;
        }

        .var-pill-btn.active {
            background: #FEF3C7;
            border-color: #FDE68A;
            color: #92400E;
            font-weight: 700;
        }

        /* Variables List Container */
        .var-cards-list {
            max-height: calc(100vh - 250px);
            overflow-y: auto;
            padding-right: 2px;
            display: flex;
            flex-direction: column;
            gap: 5px;
            scrollbar-width: thin;
        }

        .var-cards-list::-webkit-scrollbar {
            width: 4px;
        }

        .var-cards-list::-webkit-scrollbar-thumb {
            background: #CBD5E1;
            border-radius: 4px;
        }

        .var-item-card {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 7px;
            padding: 7px 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            cursor: pointer;
            transition: all 0.12s ease-in-out;
            user-select: none;
        }

        .var-item-card:hover {
            border-color: #93C5FD;
            background: #F8FAFC;
            box-shadow: 0 1px 4px rgba(37, 99, 235, 0.05);
            transform: translateY(-1px);
        }

        .var-item-left {
            display: flex;
            flex-direction: column;
            gap: 2px;
            min-width: 0;
            flex: 1;
        }

        .var-token-badge {
            font-family: 'SFMono-Regular', Menlo, Monaco, Consolas, monospace;
            font-size: 10.5px;
            font-weight: 700;
            color: #2563EB;
            background: #EFF6FF;
            border: 1px solid #DBEAFE;
            border-radius: 4px;
            padding: 1px 6px;
            display: inline-block;
            letter-spacing: -0.2px;
            width: fit-content;
        }

        .var-item-desc {
            font-size: 10.5px;
            color: #64748B;
            line-height: 1.25;
            margin: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .var-copy-btn {
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 5px;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #64748B;
            font-size: 11px;
            cursor: pointer;
            transition: all 0.1s ease;
            flex-shrink: 0;
        }

        .var-copy-btn:hover {
            background: #2563EB;
            border-color: #2563EB;
            color: #FFFFFF;
        }

        /* Target Insertion Indicator */
        .var-target-bar {
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 6px;
            padding: 3px 8px;
            margin-bottom: 7px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 10px;
            color: #475569;
        }

        /* Toast notification */
        .token-toast {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #0F172A;
            color: #FFFFFF;
            padding: 7px 14px;
            border-radius: 6px;
            font-size: 11.5px;
            font-weight: 500;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.16);
            display: flex;
            align-items: center;
            gap: 6px;
            z-index: 99999;
            opacity: 0;
            transform: translateY(12px);
            transition: all 0.22s ease;
            pointer-events: none;
        }

        .token-toast.show {
            opacity: 1;
            transform: translateY(0);
        }

        .emoji-picker-menu {
            position: absolute;
            right: 0;
            top: 38px;
            background: #FFFFFF;
            border: 1px solid #CBD5E1;
            border-radius: 6px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
            padding: 6px;
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 3px;
            z-index: 1050;
            font-size: 15px;
            display: none;
        }

        .emoji-picker-menu span {
            cursor: pointer;
            padding: 2px;
            text-align: center;
            border-radius: 4px;
            transition: background 0.1s;
        }

        .emoji-picker-menu span:hover {
            background: #F1F5F9;
        }
    </style>
@endpush

@section('content')
    <!-- Top Action Bar matching Image 1 -->
    <div class="template-header-bar mb-3">
        <div class="d-flex align-items-center">
            <a href="{{ route('superadmin.company.emailthemes') }}" class="btn btn-sm btn-icon btn-white border border-gray-300 rounded-circle me-3 hover-elevate-up" style="width: 30px; height: 30px;" title="Back to Templates">
                <i class="bi bi-arrow-left fs-5 text-gray-700"></i>
            </a>
            <div class="header-icon-box me-3">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="4" width="20" height="16" rx="2"></rect>
                    <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path>
                </svg>
            </div>
            <div>
                <h1 class="fs-5 fw-bolder text-gray-900 m-0">
                    {{ @$th_email_template->e_temp_id ? 'Edit Email Template' : 'Add Email Template' }}
                </h1>
                <p class="fs-9 text-muted m-0">Create a new email template for recruitment communication.</p>
            </div>
        </div>
        <div class="d-flex align-items-center">
            <a href="{{ route('superadmin.company.emailthemes') }}" class="btn-cancel-light me-2">Cancel</a>
            <button type="button" id="save-form" class="btn-save-yellow">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                    <polyline points="17 21 17 13 7 13 7 21"></polyline>
                    <polyline points="7 3 7 8 15 8"></polyline>
                </svg>
                Save Template
            </button>
        </div>
    </div>

    <!-- Main Content Container: 2 Columns - Balanced for 15.6" Displays -->
    <div class="container-fluid px-3 pb-6">
        <form id="editSettings" class="ajax-form">
            @csrf
            <input type="hidden" name="tokenId" id="tokenId" value="{{ (@$th_email_template->e_temp_id) ? @$th_email_template->e_temp_id : 0 }}">

            <div class="row g-3">
                <!-- Left Column: Form & Editor (Col 7) -->
                <div class="col-xl-7 col-lg-7">

                    <!-- Card 1: Template Details -->
                    <div class="section-card">
                        <div class="section-header">
                            <div class="section-header-icon icon-purple">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                    <line x1="16" y1="13" x2="8" y2="13"></line>
                                    <line x1="16" y1="17" x2="8" y2="17"></line>
                                    <polyline points="10 9 9 9 8 9"></polyline>
                                </svg>
                            </div>
                            <div>
                                <h2 class="section-title">Template Details</h2>
                                <p class="section-subtitle">Provide basic information for your email template.</p>
                            </div>
                        </div>

                        <div class="row g-2">
                            <!-- Template Name -->
                            <div class="col-md-6">
                                <label class="form-label-custom">Template Name <span class="req">*</span></label>
                                <input type="text" class="form-control-custom" name="template" id="template" placeholder="Job Openings | Apply Now" value="{{ (@$th_email_template->display_name) ? @$th_email_template->display_name : (@$th_email_template->template_name ?: '') }}" required>
                                <span class="form-hint">Give a descriptive name for easy identification.</span>
                            </div>

                            <!-- Template Type -->
                            <div class="col-md-6">
                                <label class="form-label-custom">Template Type <span class="req">*</span></label>
                                <div class="position-relative">
                                    <select name="temptype_is" id="temptype_is" class="form-select-custom" required>
                                        <option value="4" {{ (@$th_email_template->temptype == 4 || empty($th_email_template->temptype)) ? 'selected' : '' }}>Send Mail</option>
                                        <option value="3" {{ (@$th_email_template->temptype == 3) ? 'selected' : '' }}>Interview Mail</option>
                                        <option value="2" {{ (@$th_email_template->temptype == 2) ? 'selected' : '' }}>Mass Mail</option>
                                        <option value="1" {{ (@$th_email_template->temptype == 1) ? 'selected' : '' }}>Fwd Resume</option>
                                        <option value="5" {{ (@$th_email_template->temptype == 5) ? 'selected' : '' }}>Bench Reject Mail</option>
                                        <option value="6" {{ (@$th_email_template->temptype == 6) ? 'selected' : '' }}>Job Reject Mail Marketplace</option>
                                        <option value="7" {{ (@$th_email_template->temptype == 7) ? 'selected' : '' }}>Candidate Send Offer Letter</option>
                                        <option value="8" {{ (@$th_email_template->temptype == 8) ? 'selected' : '' }}>Management Offer Letter Approval</option>
                                    </select>
                                </div>
                                <span class="form-hint">Select the type of email template.</span>
                            </div>

                            <!-- Subject Line -->
                            <div class="col-md-6">
                                <label class="form-label-custom">Subject <span class="req">*</span></label>
                                <div class="position-relative">
                                    <input type="text" class="form-control-custom pe-7" name="subject" id="subject" placeholder="Job Openings | [{COMPANY_NAME}] | Apply Now" value="{{ (@$th_email_template->template_subject) ? @$th_email_template->template_subject : '' }}" required>
                                    <button type="button" class="btn btn-sm btn-icon position-absolute end-0 top-50 translate-middle-y me-1" id="btnToggleEmoji" title="Insert Emoji">
                                        <i class="bi bi-emoji-smile fs-6 text-gray-500"></i>
                                    </button>
                                    <div class="emoji-picker-menu" id="emojiPickerMenu">
                                        <span>👋</span><span>🤝</span><span>🎉</span><span>💼</span><span>🚀</span><span>📢</span>
                                        <span>📅</span><span>✨</span><span>🔥</span><span>💡</span><span>✅</span><span>📧</span>
                                    </div>
                                </div>
                                <span class="form-hint">Email subject line. Use variables for dynamic content.</span>
                            </div>

                            <!-- Status -->
                            <div class="col-md-6">
                                <label class="form-label-custom">Status</label>
                                <select name="status_is" id="status_is" class="form-select-custom">
                                    <option value="1" {{ (@$th_email_template && @$th_email_template->status == 1) || !isset($th_email_template->status) ? 'selected' : '' }}>● Active</option>
                                    <option value="0" {{ (@$th_email_template && @$th_email_template->status == 0) ? 'selected' : '' }}>● Inactive</option>
                                </select>
                                <span class="form-hint">Enable or disable this template.</span>
                            </div>
                        </div>
                    </div>

                    <!-- Card 2: Email Body -->
                    <div class="section-card">
                        <div class="section-header">
                            <div class="section-header-icon icon-blue">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h2 class="section-title">Email Body</h2>
                                <p class="section-subtitle">Create your email content using the rich text editor. Insert variables to personalize the email.</p>
                            </div>
                        </div>

                        <!-- Editor Wrapper matching the reference toolbar design -->
                        <div class="editor-wrapper">
                            <div class="editor-custom-toolbar">
                                <select class="ed-select-style" id="edStyleSelect" onchange="applyEditorFormat(this.value)">
                                    <option value="p">Normal</option>
                                    <option value="h1">Heading 1</option>
                                    <option value="h2">Heading 2</option>
                                    <option value="h3">Heading 3</option>
                                </select>

                                <div class="ed-tool-sep"></div>

                                <button type="button" class="ed-tool-btn" onclick="execEditorCmd('bold')" title="Bold (Ctrl+B)"><b>B</b></button>
                                <button type="button" class="ed-tool-btn" onclick="execEditorCmd('italic')" title="Italic (Ctrl+I)"><i>I</i></button>
                                <button type="button" class="ed-tool-btn" onclick="execEditorCmd('underline')" title="Underline (Ctrl+U)"><u>U</u></button>

                                <div class="ed-tool-sep"></div>

                                <button type="button" class="ed-tool-btn" onclick="execEditorCmd('bulletedList')" title="Bulleted List"><i class="bi bi-list-ul"></i></button>
                                <button type="button" class="ed-tool-btn" onclick="execEditorCmd('numberedList')" title="Numbered List"><i class="bi bi-list-ol"></i></button>

                                <div class="ed-tool-sep"></div>

                                <button type="button" class="ed-tool-btn" onclick="execEditorCmd('link')" title="Insert Link"><i class="bi bi-link-45deg fs-7"></i></button>
                                <button type="button" class="ed-tool-btn" onclick="execEditorCmd('image')" title="Insert Image"><i class="bi bi-image fs-7"></i></button>
                                <button type="button" class="ed-tool-btn" onclick="execEditorCmd('table')" title="Insert Table"><i class="bi bi-table fs-7"></i></button>
                                <button type="button" class="ed-tool-btn" onclick="execEditorCmd('source')" title="View HTML Source"><i class="bi bi-code-slash fs-7"></i></button>

                                <div class="ed-tool-sep"></div>

                                <button type="button" class="ed-tool-btn" onclick="execEditorCmd('justifyleft')" title="Align Left"><i class="bi bi-text-left"></i></button>
                                <button type="button" class="ed-tool-btn" onclick="execEditorCmd('justifycenter')" title="Align Center"><i class="bi bi-text-center"></i></button>
                                <button type="button" class="ed-tool-btn" onclick="execEditorCmd('justifyright')" title="Align Right"><i class="bi bi-text-right"></i></button>

                                <div class="ed-personalize-btn dropdown-toggle" id="btnPersonalizeDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-magic fs-8"></i>
                                    Personalize
                                </div>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 py-2" aria-labelledby="btnPersonalizeDropdown" style="font-size: 11px; max-height: 220px; overflow-y: auto;">
                                    <li><h6 class="dropdown-header text-uppercase text-muted fw-bold fs-9">Candidate Tokens</h6></li>
                                    <li><a class="dropdown-item py-1 px-3" href="javascript:void(0);" onclick="insertVariableToken('[{CANDIDATE_NAME}]')"><code>[{CANDIDATE_NAME}]</code> &middot; Candidate Name</a></li>
                                    <li><a class="dropdown-item py-1 px-3" href="javascript:void(0);" onclick="insertVariableToken('[{CANDIDATE_EMAIL}]')"><code>[{CANDIDATE_EMAIL}]</code> &middot; Candidate Email</a></li>
                                    <li><a class="dropdown-item py-1 px-3" href="javascript:void(0);" onclick="insertVariableToken('[{CANDIDATE_PHONE}]')"><code>[{CANDIDATE_PHONE}]</code> &middot; Phone Number</a></li>
                                    <li><a class="dropdown-item py-1 px-3" href="javascript:void(0);" onclick="insertVariableToken('[{EMAIL_TRACKER}]')"><code>[{EMAIL_TRACKER}]</code> &middot; Excel Tracker Table</a></li>
                                    <li><hr class="dropdown-divider my-1"></li>
                                    <li><h6 class="dropdown-header text-uppercase text-muted fw-bold fs-9">Job & Company Tokens</h6></li>
                                    <li><a class="dropdown-item py-1 px-3" href="javascript:void(0);" onclick="insertVariableToken('[{JOB_TITLE}]')"><code>[{JOB_TITLE}]</code> &middot; Job Title</a></li>
                                    <li><a class="dropdown-item py-1 px-3" href="javascript:void(0);" onclick="insertVariableToken('[{COMPANY_NAME}]')"><code>[{COMPANY_NAME}]</code> &middot; Company Name</a></li>
                                    <li><a class="dropdown-item py-1 px-3" href="javascript:void(0);" onclick="insertVariableToken('[{CLIENT_NAME}]')"><code>[{CLIENT_NAME}]</code> &middot; Client Name</a></li>
                                    <li><a class="dropdown-item py-1 px-3" href="javascript:void(0);" onclick="insertVariableToken('[{WORK_LOCATION}]')"><code>[{WORK_LOCATION}]</code> &middot; Work Location</a></li>
                                </ul>
                            </div>

                            <textarea name="editor1" id="editor1" rows="8" class="form-control" style="min-height: 240px;">{{ (@$th_email_template->template_description) ? @$th_email_template->template_description : '' }}</textarea>

                            <div class="editor-footer-info">
                                <span>Rich Text Editor &middot; Use variables from the right panel to dynamically populate fields</span>
                                <div>
                                    <span id="charCount">0</span>/5000 characters
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 3: Additional Options -->
                    <div class="section-card">
                        <div class="section-header">
                            <div class="section-header-icon icon-gray">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="3"></circle>
                                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1 2.83 0l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                                </svg>
                            </div>
                            <div>
                                <h2 class="section-title">Additional Options</h2>
                                <p class="section-subtitle">Configure optional settings for this template.</p>
                            </div>
                        </div>

                        <div class="row g-2">
                            <!-- Client Requirement / Position Scope -->
                            <div class="col-md-6">
                                <label class="form-label-custom">Position Scope (Client Required) <span class="req">*</span></label>
                                <select name="client_required_is" id="client_required_is" class="form-select-custom">
                                    <option value="0" {{ empty($th_email_template->client_required) ? 'selected' : '' }}>● All Positions (Internal Organization & Client)</option>
                                    <option value="1" {{ !empty($th_email_template->client_required) ? 'selected' : '' }}>● Client Positions Only (Hide for Internal / My Organization)</option>
                                </select>
                                <span class="form-hint">When set to "Client Positions Only", this template is automatically hidden for internal positions with no client.</span>
                            </div>

                            <!-- Audience Validation -->
                            <div class="col-md-6">
                                <label class="form-label-custom">Audience Validation</label>
                                <select name="tempForInterview_is" id="tempForInterview_is" class="form-select-custom">
                                    <option value="1" {{ @$th_email_template->tempForInterview == 1 ? 'selected' : '' }}>1 - Company Opportunity (Internal / Direct Hiring)</option>
                                    <option value="2" {{ @$th_email_template->tempForInterview == 2 ? 'selected' : '' }}>2 - Client Opportunity (Staffing / Client Job)</option>
                                    <option value="3" {{ @$th_email_template->tempForInterview == 3 ? 'selected' : '' }}>3 - Client Opportunity with Company Payroll</option>
                                    <option value="4" {{ @$th_email_template->tempForInterview == 4 ? 'selected' : '' }}>4 - Documents Required</option>
                                </select>
                                <span class="form-hint">Select when this template can be used.</span>
                            </div>

                            <!-- Dynamic Variables Option -->
                            <div class="col-md-6">
                                <label class="form-label-custom">Dynamic Variables</label>
                                <select name="enable_dynamic_vars" id="enable_dynamic_vars" class="form-select-custom">
                                    <option value="1" selected>Enable dynamic variables</option>
                                    <option value="0">Disable dynamic variables</option>
                                </select>
                                <span class="form-hint">Allow users to use dynamic variables while sending emails.</span>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Right Column: Available Variables Panel (Col 5) - Generous Breathing Room -->
                <div class="col-xl-5 col-lg-5">
                    <div class="variables-panel">
                        <!-- Header with Inline Search Box matching Reference Design -->
                        <div class="d-flex align-items-center justify-content-between mb-2 gap-2">
                            <div class="d-flex align-items-center gap-2">
                                <div class="section-header-icon icon-amber">
                                    <span class="fs-6 fw-bolder font-monospace">{ }</span>
                                </div>
                                <div>
                                    <h2 class="section-title">Available Variables</h2>
                                    <p class="section-subtitle">Use these variables to personalize your email.</p>
                                </div>
                            </div>
                            <div class="var-search-wrap" style="max-width: 165px; width: 100%;">
                                <i class="bi bi-search search-icon"></i>
                                <input type="text" class="var-search-input" id="varSearchInput" placeholder="Search variables...">
                            </div>
                        </div>

                        <!-- Target Bar -->
                        <div class="var-target-bar">
                            <span class="fs-10 text-muted">Clicking card inserts into:</span>
                            <div class="d-flex gap-1">
                                <button type="button" class="btn btn-xs py-0 px-2 border target-btn-choice" id="targetBodyBtn" onclick="setInsertTarget('body')">Body</button>
                                <button type="button" class="btn btn-xs py-0 px-2 border target-btn-choice" id="targetSubjectBtn" onclick="setInsertTarget('subject')">Subject</button>
                            </div>
                        </div>

                        <!-- Category Tabs - Multiline Wrap without Overlapping Scrollbars -->
                        <div class="var-pill-tabs" id="varCategoryTabs">
                            <button type="button" class="var-pill-btn active" data-cat="candidate">Candidate</button>
                            <button type="button" class="var-pill-btn" data-cat="job">Job</button>
                            <button type="button" class="var-pill-btn" data-cat="company">Company</button>
                            <button type="button" class="var-pill-btn" data-cat="client">Client / End Customer</button>
                            <button type="button" class="var-pill-btn" data-cat="recruiter">Recruiter</button>
                            <button type="button" class="var-pill-btn" data-cat="other">Other</button>
                        </div>

                        <!-- Variables List -->
                        <div class="var-cards-list" id="varCardsList">

                            <!-- CANDIDATE CATEGORY -->
                            <div class="var-item-card" data-cat="candidate" data-token="[{CANDIDATE_NAME}]" data-desc="Candidate full name" onclick="handleCardClick(this)">
                                <div class="var-item-left">
                                    <span class="var-token-badge">[{CANDIDATE_NAME}]</span>
                                    <p class="var-item-desc">Candidate full name</p>
                                </div>
                                <button type="button" class="var-copy-btn" onclick="copyTokenDirect(event, '[{CANDIDATE_NAME}]')" title="Copy variable">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                            </div>

                            <div class="var-item-card" data-cat="candidate" data-token="[{CANDIDATE_EMAIL}]" data-desc="Candidate email address" onclick="handleCardClick(this)">
                                <div class="var-item-left">
                                    <span class="var-token-badge">[{CANDIDATE_EMAIL}]</span>
                                    <p class="var-item-desc">Candidate email address</p>
                                </div>
                                <button type="button" class="var-copy-btn" onclick="copyTokenDirect(event, '[{CANDIDATE_EMAIL}]')" title="Copy variable">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                            </div>

                            <div class="var-item-card" data-cat="candidate" data-token="[{CANDIDATE_PHONE}]" data-desc="Candidate phone number" onclick="handleCardClick(this)">
                                <div class="var-item-left">
                                    <span class="var-token-badge">[{CANDIDATE_PHONE}]</span>
                                    <p class="var-item-desc">Candidate phone number</p>
                                </div>
                                <button type="button" class="var-copy-btn" onclick="copyTokenDirect(event, '[{CANDIDATE_PHONE}]')" title="Copy variable">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                            </div>

                            <div class="var-item-card" data-cat="candidate" data-token="[{CURRENT_LOCATION}]" data-desc="Current location" onclick="handleCardClick(this)">
                                <div class="var-item-left">
                                    <span class="var-token-badge">[{CURRENT_LOCATION}]</span>
                                    <p class="var-item-desc">Current location</p>
                                </div>
                                <button type="button" class="var-copy-btn" onclick="copyTokenDirect(event, '[{CURRENT_LOCATION}]')" title="Copy variable">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                            </div>

                            <div class="var-item-card" data-cat="candidate" data-token="[{TOTAL_EXPERIENCE}]" data-desc="Total years of experience" onclick="handleCardClick(this)">
                                <div class="var-item-left">
                                    <span class="var-token-badge">[{TOTAL_EXPERIENCE}]</span>
                                    <p class="var-item-desc">Total years of experience</p>
                                </div>
                                <button type="button" class="var-copy-btn" onclick="copyTokenDirect(event, '[{TOTAL_EXPERIENCE}]')" title="Copy variable">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                            </div>

                            <div class="var-item-card" data-cat="candidate" data-token="[{HIGHEST_QUALIFICATION}]" data-desc="Highest qualification" onclick="handleCardClick(this)">
                                <div class="var-item-left">
                                    <span class="var-token-badge">[{HIGHEST_QUALIFICATION}]</span>
                                    <p class="var-item-desc">Highest qualification</p>
                                </div>
                                <button type="button" class="var-copy-btn" onclick="copyTokenDirect(event, '[{HIGHEST_QUALIFICATION}]')" title="Copy variable">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                            </div>

                            <div class="var-item-card" data-cat="candidate" data-token="[{PRIMARY_SKILLS}]" data-desc="Primary skills" onclick="handleCardClick(this)">
                                <div class="var-item-left">
                                    <span class="var-token-badge">[{PRIMARY_SKILLS}]</span>
                                    <p class="var-item-desc">Primary skills</p>
                                </div>
                                <button type="button" class="var-copy-btn" onclick="copyTokenDirect(event, '[{PRIMARY_SKILLS}]')" title="Copy variable">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                            </div>

                            <div class="var-item-card" data-cat="candidate" data-token="[{NOTICE_PERIOD}]" data-desc="Notice period" onclick="handleCardClick(this)">
                                <div class="var-item-left">
                                    <span class="var-token-badge">[{NOTICE_PERIOD}]</span>
                                    <p class="var-item-desc">Notice period</p>
                                </div>
                                <button type="button" class="var-copy-btn" onclick="copyTokenDirect(event, '[{NOTICE_PERIOD}]')" title="Copy variable">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                            </div>

                            <div class="var-item-card" data-cat="candidate" data-token="[{EXPECTED_CTC}]" data-desc="Expected CTC" onclick="handleCardClick(this)">
                                <div class="var-item-left">
                                    <span class="var-token-badge">[{EXPECTED_CTC}]</span>
                                    <p class="var-item-desc">Expected CTC</p>
                                </div>
                                <button type="button" class="var-copy-btn" onclick="copyTokenDirect(event, '[{EXPECTED_CTC}]')" title="Copy variable">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                            </div>

                            <div class="var-item-card" data-cat="candidate" data-token="[{CURRENT_CTC}]" data-desc="Current CTC" onclick="handleCardClick(this)">
                                <div class="var-item-left">
                                    <span class="var-token-badge">[{CURRENT_CTC}]</span>
                                    <p class="var-item-desc">Current CTC</p>
                                </div>
                                <button type="button" class="var-copy-btn" onclick="copyTokenDirect(event, '[{CURRENT_CTC}]')" title="Copy variable">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                            </div>

                            <div class="var-item-card" data-cat="candidate" data-token="[{CANDIDATE_COMMENT}]" data-desc="Candidate remarks & notes" onclick="handleCardClick(this)">
                                <div class="var-item-left">
                                    <span class="var-token-badge">[{CANDIDATE_COMMENT}]</span>
                                    <p class="var-item-desc">Candidate remarks & notes</p>
                                </div>
                                <button type="button" class="var-copy-btn" onclick="copyTokenDirect(event, '[{CANDIDATE_COMMENT}]')" title="Copy variable">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                            </div>

                            <div class="var-item-card" data-cat="candidate" data-token="[{EMAIL_TRACKER}]" data-desc="Excel-style candidate skills, Q&A and interview availability tracker table" onclick="handleCardClick(this)">
                                <div class="var-item-left">
                                    <span class="var-token-badge">[{EMAIL_TRACKER}]</span>
                                    <p class="var-item-desc">Excel-style Candidate & Skills matrix tracker table</p>
                                </div>
                                <button type="button" class="var-copy-btn" onclick="copyTokenDirect(event, '[{EMAIL_TRACKER}]')" title="Copy variable">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                            </div>

                            <!-- JOB CATEGORY -->
                            <div class="var-item-card" data-cat="job" data-token="[{JOB_TITLE}]" data-desc="Job opportunity designation / title" onclick="handleCardClick(this)" style="display: none;">
                                <div class="var-item-left">
                                    <span class="var-token-badge">[{JOB_TITLE}]</span>
                                    <p class="var-item-desc">Job opportunity designation / title</p>
                                </div>
                                <button type="button" class="var-copy-btn" onclick="copyTokenDirect(event, '[{JOB_TITLE}]')" title="Copy variable">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                            </div>

                            <div class="var-item-card" data-cat="job" data-token="[{WORK_LOCATION}]" data-desc="Job location / city" onclick="handleCardClick(this)" style="display: none;">
                                <div class="var-item-left">
                                    <span class="var-token-badge">[{WORK_LOCATION}]</span>
                                    <p class="var-item-desc">Job location / city</p>
                                </div>
                                <button type="button" class="var-copy-btn" onclick="copyTokenDirect(event, '[{WORK_LOCATION}]')" title="Copy variable">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                            </div>

                            <div class="var-item-card" data-cat="job" data-token="[{JOB_LOCATION}]" data-desc="Work location alias" onclick="handleCardClick(this)" style="display: none;">
                                <div class="var-item-left">
                                    <span class="var-token-badge">[{JOB_LOCATION}]</span>
                                    <p class="var-item-desc">Work location alias</p>
                                </div>
                                <button type="button" class="var-copy-btn" onclick="copyTokenDirect(event, '[{JOB_LOCATION}]')" title="Copy variable">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                            </div>

                            <div class="var-item-card" data-cat="job" data-token="[{JOB_DESCRIPTION}]" data-desc="Complete job description text" onclick="handleCardClick(this)" style="display: none;">
                                <div class="var-item-left">
                                    <span class="var-token-badge">[{JOB_DESCRIPTION}]</span>
                                    <p class="var-item-desc">Complete job description text</p>
                                </div>
                                <button type="button" class="var-copy-btn" onclick="copyTokenDirect(event, '[{JOB_DESCRIPTION}]')" title="Copy variable">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                            </div>

                            <div class="var-item-card" data-cat="job" data-token="[{SALARY_AMOUNT}]" data-desc="Offered salary or package" onclick="handleCardClick(this)" style="display: none;">
                                <div class="var-item-left">
                                    <span class="var-token-badge">[{SALARY_AMOUNT}]</span>
                                    <p class="var-item-desc">Offered salary or package</p>
                                </div>
                                <button type="button" class="var-copy-btn" onclick="copyTokenDirect(event, '[{SALARY_AMOUNT}]')" title="Copy variable">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                            </div>

                            <div class="var-item-card" data-cat="job" data-token="[{JOB_TYPE}]" data-desc="Job type (Full-time, Contract, Remote)" onclick="handleCardClick(this)" style="display: none;">
                                <div class="var-item-left">
                                    <span class="var-token-badge">[{JOB_TYPE}]</span>
                                    <p class="var-item-desc">Job type (Full-time, Contract, Remote)</p>
                                </div>
                                <button type="button" class="var-copy-btn" onclick="copyTokenDirect(event, '[{JOB_TYPE}]')" title="Copy variable">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                            </div>

                            <div class="var-item-card" data-cat="job" data-token="[{WORK_SHIFT}]" data-desc="Work shift / hours (e.g. Day, Night)" onclick="handleCardClick(this)" style="display: none;">
                                <div class="var-item-left">
                                    <span class="var-token-badge">[{WORK_SHIFT}]</span>
                                    <p class="var-item-desc">Work shift / hours (e.g. Day, Night)</p>
                                </div>
                                <button type="button" class="var-copy-btn" onclick="copyTokenDirect(event, '[{WORK_SHIFT}]')" title="Copy variable">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                            </div>

                            <div class="var-item-card" data-cat="job" data-token="[{JOB_LINK}]" data-desc="Direct link to job post" onclick="handleCardClick(this)" style="display: none;">
                                <div class="var-item-left">
                                    <span class="var-token-badge">[{JOB_LINK}]</span>
                                    <p class="var-item-desc">Direct link to job post</p>
                                </div>
                                <button type="button" class="var-copy-btn" onclick="copyTokenDirect(event, '[{JOB_LINK}]')" title="Copy variable">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                            </div>

                            <div class="var-item-card" data-cat="job" data-token="[{NUMBER_OF_VACANCIES}]" data-desc="Total number of open positions" onclick="handleCardClick(this)" style="display: none;">
                                <div class="var-item-left">
                                    <span class="var-token-badge">[{NUMBER_OF_VACANCIES}]</span>
                                    <p class="var-item-desc">Total number of open positions</p>
                                </div>
                                <button type="button" class="var-copy-btn" onclick="copyTokenDirect(event, '[{NUMBER_OF_VACANCIES}]')" title="Copy variable">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                            </div>

                            <!-- COMPANY CATEGORY -->
                            <div class="var-item-card" data-cat="company" data-token="[{COMPANY_NAME}]" data-desc="Your recruitment company / employer organization" onclick="handleCardClick(this)" style="display: none;">
                                <div class="var-item-left">
                                    <span class="var-token-badge">[{COMPANY_NAME}]</span>
                                    <p class="var-item-desc">Your recruitment company / employer organization</p>
                                </div>
                                <button type="button" class="var-copy-btn" onclick="copyTokenDirect(event, '[{COMPANY_NAME}]')" title="Copy variable">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                            </div>

                            <div class="var-item-card" data-cat="company" data-token="[{COMPANY_WEBSITE}]" data-desc="Organization website URL" onclick="handleCardClick(this)" style="display: none;">
                                <div class="var-item-left">
                                    <span class="var-token-badge">[{COMPANY_WEBSITE}]</span>
                                    <p class="var-item-desc">Organization website URL</p>
                                </div>
                                <button type="button" class="var-copy-btn" onclick="copyTokenDirect(event, '[{COMPANY_WEBSITE}]')" title="Copy variable">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                            </div>

                            <div class="var-item-card" data-cat="company" data-token="[{COMPANY_DESCRIPTION}]" data-desc="About the company overview" onclick="handleCardClick(this)" style="display: none;">
                                <div class="var-item-left">
                                    <span class="var-token-badge">[{COMPANY_DESCRIPTION}]</span>
                                    <p class="var-item-desc">About the company overview</p>
                                </div>
                                <button type="button" class="var-copy-btn" onclick="copyTokenDirect(event, '[{COMPANY_DESCRIPTION}]')" title="Copy variable">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                            </div>

                            <div class="var-item-card" data-cat="company" data-token="[{COMPANY_ADDRESS}]" data-desc="Company registered address" onclick="handleCardClick(this)" style="display: none;">
                                <div class="var-item-left">
                                    <span class="var-token-badge">[{COMPANY_ADDRESS}]</span>
                                    <p class="var-item-desc">Company registered address</p>
                                </div>
                                <button type="button" class="var-copy-btn" onclick="copyTokenDirect(event, '[{COMPANY_ADDRESS}]')" title="Copy variable">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                            </div>

                            <div class="var-item-card" data-cat="company" data-token="[{NO_OF_EMP}]" data-desc="Company size / employee count" onclick="handleCardClick(this)" style="display: none;">
                                <div class="var-item-left">
                                    <span class="var-token-badge">[{NO_OF_EMP}]</span>
                                    <p class="var-item-desc">Company size / employee count</p>
                                </div>
                                <button type="button" class="var-copy-btn" onclick="copyTokenDirect(event, '[{NO_OF_EMP}]')" title="Copy variable">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                            </div>

                            <!-- CLIENT CATEGORY -->
                            <div class="var-item-card" data-cat="client" data-token="[{CLIENT_NAME}]" data-desc="Client organization / hiring partner name" onclick="handleCardClick(this)" style="display: none;">
                                <div class="var-item-left">
                                    <span class="var-token-badge">[{CLIENT_NAME}]</span>
                                    <p class="var-item-desc">Client organization / hiring partner name</p>
                                </div>
                                <button type="button" class="var-copy-btn" onclick="copyTokenDirect(event, '[{CLIENT_NAME}]')" title="Copy variable">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                            </div>

                            <div class="var-item-card" data-cat="client" data-token="[{CLIENT_COMPANY_NAME}]" data-desc="Client company name alias" onclick="handleCardClick(this)" style="display: none;">
                                <div class="var-item-left">
                                    <span class="var-token-badge">[{CLIENT_COMPANY_NAME}]</span>
                                    <p class="var-item-desc">Client company name alias</p>
                                </div>
                                <button type="button" class="var-copy-btn" onclick="copyTokenDirect(event, '[{CLIENT_COMPANY_NAME}]')" title="Copy variable">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                            </div>

                            <div class="var-item-card" data-cat="client" data-token="[{CLIENT_WEBSITE}]" data-desc="Client official website URL" onclick="handleCardClick(this)" style="display: none;">
                                <div class="var-item-left">
                                    <span class="var-token-badge">[{CLIENT_WEBSITE}]</span>
                                    <p class="var-item-desc">Client official website URL</p>
                                </div>
                                <button type="button" class="var-copy-btn" onclick="copyTokenDirect(event, '[{CLIENT_WEBSITE}]')" title="Copy variable">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                            </div>

                            <div class="var-item-card" data-cat="client" data-token="[{CLIENT_DESCRIPTION}]" data-desc="Client organization description" onclick="handleCardClick(this)" style="display: none;">
                                <div class="var-item-left">
                                    <span class="var-token-badge">[{CLIENT_DESCRIPTION}]</span>
                                    <p class="var-item-desc">Client organization description</p>
                                </div>
                                <button type="button" class="var-copy-btn" onclick="copyTokenDirect(event, '[{CLIENT_DESCRIPTION}]')" title="Copy variable">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                            </div>

                            <!-- RECRUITER CATEGORY -->
                            <div class="var-item-card" data-cat="recruiter" data-token="[{HR_NAME}]" data-desc="Sender recruiter / HR contact name" onclick="handleCardClick(this)" style="display: none;">
                                <div class="var-item-left">
                                    <span class="var-token-badge">[{HR_NAME}]</span>
                                    <p class="var-item-desc">Sender recruiter / HR contact name</p>
                                </div>
                                <button type="button" class="var-copy-btn" onclick="copyTokenDirect(event, '[{HR_NAME}]')" title="Copy variable">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                            </div>

                            <div class="var-item-card" data-cat="recruiter" data-token="[{HR_EMAIL}]" data-desc="Recruiter contact email address" onclick="handleCardClick(this)" style="display: none;">
                                <div class="var-item-left">
                                    <span class="var-token-badge">[{HR_EMAIL}]</span>
                                    <p class="var-item-desc">Recruiter contact email address</p>
                                </div>
                                <button type="button" class="var-copy-btn" onclick="copyTokenDirect(event, '[{HR_EMAIL}]')" title="Copy variable">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                            </div>

                            <div class="var-item-card" data-cat="recruiter" data-token="[{HR_PHONE}]" data-desc="Recruiter phone number" onclick="handleCardClick(this)" style="display: none;">
                                <div class="var-item-left">
                                    <span class="var-token-badge">[{HR_PHONE}]</span>
                                    <p class="var-item-desc">Recruiter phone number</p>
                                </div>
                                <button type="button" class="var-copy-btn" onclick="copyTokenDirect(event, '[{HR_PHONE}]')" title="Copy variable">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                            </div>

                            <div class="var-item-card" data-cat="recruiter" data-token="[{WHAT_LINK}]" data-desc="Recruiter WhatsApp direct link" onclick="handleCardClick(this)" style="display: none;">
                                <div class="var-item-left">
                                    <span class="var-token-badge">[{WHAT_LINK}]</span>
                                    <p class="var-item-desc">Recruiter WhatsApp direct link</p>
                                </div>
                                <button type="button" class="var-copy-btn" onclick="copyTokenDirect(event, '[{WHAT_LINK}]')" title="Copy variable">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                            </div>

                            <!-- OTHER CATEGORY -->
                            <div class="var-item-card" data-cat="other" data-token="[{INTERVIEW_DATE_TIME}]" data-desc="Scheduled interview date and time" onclick="handleCardClick(this)" style="display: none;">
                                <div class="var-item-left">
                                    <span class="var-token-badge">[{INTERVIEW_DATE_TIME}]</span>
                                    <p class="var-item-desc">Scheduled interview date and time</p>
                                </div>
                                <button type="button" class="var-copy-btn" onclick="copyTokenDirect(event, '[{INTERVIEW_DATE_TIME}]')" title="Copy variable">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                            </div>

                            <div class="var-item-card" data-cat="other" data-token="[{INTERVIEW_LINK}]" data-desc="Virtual interview video meeting link" onclick="handleCardClick(this)" style="display: none;">
                                <div class="var-item-left">
                                    <span class="var-token-badge">[{INTERVIEW_LINK}]</span>
                                    <p class="var-item-desc">Virtual interview video meeting link</p>
                                </div>
                                <button type="button" class="var-copy-btn" onclick="copyTokenDirect(event, '[{INTERVIEW_LINK}]')" title="Copy variable">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                            </div>

                            <div class="var-item-card" data-cat="other" data-token="[{INTERVIEW_QUESTIONS}]" data-desc="Interview / screening questions list" onclick="handleCardClick(this)" style="display: none;">
                                <div class="var-item-left">
                                    <span class="var-token-badge">[{INTERVIEW_QUESTIONS}]</span>
                                    <p class="var-item-desc">Interview / screening questions list</p>
                                </div>
                                <button type="button" class="var-copy-btn" onclick="copyTokenDirect(event, '[{INTERVIEW_QUESTIONS}]')" title="Copy variable">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                            </div>

                            <div class="var-item-card" data-cat="other" data-token="[{INTERVIEW_MODE}]" data-desc="Interview mode (Phone / Video / F2F)" onclick="handleCardClick(this)" style="display: none;">
                                <div class="var-item-left">
                                    <span class="var-token-badge">[{INTERVIEW_MODE}]</span>
                                    <p class="var-item-desc">Interview mode (Phone / Video / F2F)</p>
                                </div>
                                <button type="button" class="var-copy-btn" onclick="copyTokenDirect(event, '[{INTERVIEW_MODE}]')" title="Copy variable">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                            </div>

                            <div class="var-item-card" data-cat="other" data-token="[{OFFER_LETTER_LINK}]" data-desc="Offer letter download or view link" onclick="handleCardClick(this)" style="display: none;">
                                <div class="var-item-left">
                                    <span class="var-token-badge">[{OFFER_LETTER_LINK}]</span>
                                    <p class="var-item-desc">Offer letter download or view link</p>
                                </div>
                                <button type="button" class="var-copy-btn" onclick="copyTokenDirect(event, '[{OFFER_LETTER_LINK}]')" title="Copy variable">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                            </div>

                            <div class="var-item-card" data-cat="other" data-token="[{DATE}]" data-desc="Current date" onclick="handleCardClick(this)" style="display: none;">
                                <div class="var-item-left">
                                    <span class="var-token-badge">[{DATE}]</span>
                                    <p class="var-item-desc">Current date</p>
                                </div>
                                <button type="button" class="var-copy-btn" onclick="copyTokenDirect(event, '[{DATE}]')" title="Copy variable">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                            </div>

                            <div class="var-item-card" data-cat="other" data-token="[{EMAIL_TRACKER}]" data-desc="Excel-style candidate skills, Q&A and interview availability tracker table" onclick="handleCardClick(this)" style="display: none;">
                                <div class="var-item-left">
                                    <span class="var-token-badge">[{EMAIL_TRACKER}]</span>
                                    <p class="var-item-desc">Excel-style Candidate & Skills matrix tracker table</p>
                                </div>
                                <button type="button" class="var-copy-btn" onclick="copyTokenDirect(event, '[{EMAIL_TRACKER}]')" title="Copy variable">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                            </div>

                            <div id="noVariablesFound" class="text-center py-4 text-muted fs-8" style="display: none;">
                                <i class="bi bi-search fs-4 text-gray-400 d-block mb-1"></i>
                                No variables match your search.
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </form>
    </div>

    <!-- Toast Notification for Token Insert/Copy -->
    <div id="tokenToast" class="token-toast">
        <i class="bi bi-check2-circle text-success fs-6"></i>
        <span id="tokenToastText">Token copied to clipboard</span>
    </div>
@endsection

@push('footer-script')
    <script src="{{ asset('demo/ckeditor/ckeditor.js') }}"></script>
    <script>
        var activeInsertTarget = 'body'; // 'body' or 'subject'

        $(document).ready(function() {
            // Update Active Target Visual State
            updateTargetButtons();

            // Init CKEditor 4 with compact 15.6" height (240px)
            if (typeof CKEDITOR !== 'undefined') {
                CKEDITOR.config.height = 240;
                CKEDITOR.config.width = 'auto';
                CKEDITOR.config.removePlugins = 'elementspath';
                CKEDITOR.config.resize_enabled = false;

                CKEDITOR.replace('editor1', {
                    toolbar: [
                        { name: 'document', items: ['Source'] },
                        { name: 'clipboard', items: ['Undo', 'Redo'] },
                        { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike'] },
                        { name: 'paragraph', items: ['NumberedList', 'BulletedList', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'] },
                        { name: 'links', items: ['Link', 'Unlink'] },
                        { name: 'insert', items: ['Image', 'Table', 'HorizontalRule'] },
                        { name: 'styles', items: ['Format', 'Font', 'FontSize'] },
                        { name: 'colors', items: ['TextColor', 'BGColor'] }
                    ]
                });

                CKEDITOR.on('instanceReady', function(evt) {
                    if (evt.editor && evt.editor.name === 'editor1') {
                        evt.editor.on('focus', function() {
                            setInsertTarget('body');
                        });
                        evt.editor.on('change', function() {
                            updateCharCount();
                        });
                        evt.editor.on('key', function() {
                            setTimeout(updateCharCount, 50);
                        });
                        updateCharCount();
                    }
                });
            }

            // Subject focus sets target to subject
            $('#subject').on('focus', function() {
                setInsertTarget('subject');
            });

            // Emoji Picker Toggle
            $('#btnToggleEmoji').on('click', function(e) {
                e.stopPropagation();
                $('#emojiPickerMenu').toggle();
            });

            $('#emojiPickerMenu span').on('click', function(e) {
                e.stopPropagation();
                var emoji = $(this).text();
                var subjectVal = $('#subject').val();
                $('#subject').val(subjectVal + ' ' + emoji).focus();
                $('#emojiPickerMenu').hide();
            });

            $(document).on('click', function() {
                $('#emojiPickerMenu').hide();
            });

            // Category Tab Switching
            $('.var-pill-btn').on('click', function() {
                $('.var-pill-btn').removeClass('active');
                $(this).addClass('active');
                filterVariables();
            });

            // Live Search
            $('#varSearchInput').on('input keyup', function() {
                filterVariables();
            });
        });

        function setInsertTarget(target) {
            activeInsertTarget = target;
            updateTargetButtons();
        }

        function updateTargetButtons() {
            if (activeInsertTarget === 'body') {
                $('#targetBodyBtn').removeClass('btn-light').addClass('btn-primary fw-bold text-white').css({'background':'#2563EB','color':'#FFF'});
                $('#targetSubjectBtn').removeClass('btn-primary fw-bold text-white').addClass('btn-light text-muted').css({'background':'#FFF','color':'#64748B'});
            } else {
                $('#targetSubjectBtn').removeClass('btn-light').addClass('btn-primary fw-bold text-white').css({'background':'#2563EB','color':'#FFF'});
                $('#targetBodyBtn').removeClass('btn-primary fw-bold text-white').addClass('btn-light text-muted').css({'background':'#FFF','color':'#64748B'});
            }
        }

        function filterVariables() {
            var activeCat = $('.var-pill-btn.active').data('cat') || 'all';
            var query = ($('#varSearchInput').val() || '').toLowerCase().trim();
            var visibleCount = 0;

            $('.var-item-card').each(function() {
                var cardCat = $(this).data('cat') || '';
                var token = ($(this).data('token') || '').toLowerCase();
                var desc = ($(this).data('desc') || '').toLowerCase();

                var matchesCat = (activeCat === 'all' || cardCat === activeCat);
                var matchesQuery = (!query || token.indexOf(query) !== -1 || desc.indexOf(query) !== -1);

                if (matchesCat && matchesQuery) {
                    $(this).show();
                    visibleCount++;
                } else {
                    $(this).hide();
                }
            });

            if (visibleCount === 0) {
                $('#noVariablesFound').show();
            } else {
                $('#noVariablesFound').hide();
            }
        }

        function handleCardClick(cardElement) {
            var token = $(cardElement).data('token');
            insertVariableToken(token, activeInsertTarget);
        }

        function insertVariableToken(token, target) {
            var useTarget = target || activeInsertTarget;

            if (useTarget === 'subject') {
                var subjectInput = document.getElementById('subject');
                if (subjectInput) {
                    var startPos = subjectInput.selectionStart;
                    var endPos = subjectInput.selectionEnd;
                    var val = subjectInput.value;
                    subjectInput.value = val.substring(0, startPos) + ' ' + token + ' ' + val.substring(endPos, val.length);
                    subjectInput.focus();
                    subjectInput.selectionStart = subjectInput.selectionEnd = startPos + token.length + 2;
                }
                showToast('Inserted ' + token + ' into Subject');
            } else {
                if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances && CKEDITOR.instances.editor1) {
                    CKEDITOR.instances.editor1.insertHtml(' ' + token + ' ');
                    CKEDITOR.instances.editor1.focus();
                } else {
                    var textarea = document.getElementById('editor1');
                    if (textarea) {
                        textarea.value += ' ' + token + ' ';
                    }
                }
                updateCharCount();
                showToast('Inserted ' + token + ' into Body');
            }
        }

        function copyTokenDirect(event, token) {
            event.stopPropagation();
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(token).then(function() {
                    showToast('Copied ' + token + ' to clipboard');
                });
            } else {
                var tempInput = document.createElement("input");
                tempInput.value = token;
                document.body.appendChild(tempInput);
                tempInput.select();
                document.execCommand("copy");
                document.body.removeChild(tempInput);
                showToast('Copied ' + token + ' to clipboard');
            }
        }

        function showToast(message) {
            $('#tokenToastText').text(message);
            $('#tokenToast').addClass('show');
            setTimeout(function() {
                $('#tokenToast').removeClass('show');
            }, 2200);
        }

        function updateCharCount() {
            var text = '';
            if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances && CKEDITOR.instances.editor1) {
                text = CKEDITOR.instances.editor1.getData().replace(/<[^>]*>/g, '').trim();
            } else {
                text = ($('#editor1').val() || '').trim();
            }
            $('#charCount').text(text.length);
        }

        // Custom Toolbar Proxy Actions
        function execEditorCmd(cmd) {
            if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances && CKEDITOR.instances.editor1) {
                var editor = CKEDITOR.instances.editor1;
                editor.focus();
                if (editor.commands[cmd]) {
                    editor.execCommand(cmd);
                }
            }
        }

        function applyEditorFormat(tag) {
            if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances && CKEDITOR.instances.editor1) {
                var editor = CKEDITOR.instances.editor1;
                editor.focus();
                var style = new CKEDITOR.style({ element: tag });
                editor.applyStyle(style);
            }
        }

        // Save Template Form Submission
        $('#save-form').on('click', function(e) {
            e.preventDefault();

            var template = $('#template').val().trim();
            var subject = $('#subject').val().trim();
            var temptype_is = $('#temptype_is').val();
            var status_is = $('#status_is').val();
            var client_required_is = $('#client_required_is').val();
            var tempForInterview_is = $('#tempForInterview_is').val();
            var tokenId = $('#tokenId').val();

            var editor1 = '';
            if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances && CKEDITOR.instances.editor1) {
                editor1 = CKEDITOR.instances.editor1.getData();
            } else {
                editor1 = $('#editor1').val();
            }

            if (!template) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Template Name Required',
                    text: 'Please provide a descriptive name for your email template.',
                    confirmButtonText: 'OK',
                    customClass: { confirmButton: 'btn btn-primary' }
                });
                $('#template').focus();
                return;
            }

            if (!subject) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Subject Line Required',
                    text: 'Please enter a subject line for your email template.',
                    confirmButtonText: 'OK',
                    customClass: { confirmButton: 'btn btn-primary' }
                });
                $('#subject').focus();
                return;
            }

            var saveBtn = $('#save-form');
            var originalBtnHtml = saveBtn.html();
            saveBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Saving...');

            $.ajax({
                type: 'POST',
                url: "{{ route('superadmin.company.addEditemailThemesupdate') }}",
                data: {
                    '_token': "{{ csrf_token() }}",
                    'tokenId': tokenId,
                    'template': template,
                    'subject': subject,
                    'editor1': editor1,
                    'status_is': status_is,
                    'temptype_is': temptype_is,
                    'client_required_is': client_required_is,
                    'tempForInterview_is': tempForInterview_is
                },
                success: function(response) {
                    saveBtn.prop('disabled', false).html(originalBtnHtml);
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Email template saved successfully.',
                            timer: 1500,
                            showConfirmButton: false
                        });
                        setTimeout(function() {
                            window.location.href = "{{ route('superadmin.company.emailthemes') }}";
                        }, 1200);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Failed to save email template.',
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function(xhr) {
                    saveBtn.prop('disabled', false).html(originalBtnHtml);
                    var msg = 'An unexpected error occurred while saving.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Save Failed',
                        text: msg,
                        confirmButtonText: 'OK'
                    });
                }
            });
        });
    </script>
@endpush
