@extends('layouts.superadmin_layout')



@push('head-script')
    <link rel="stylesheet" href="//cdn.datatables.net/fixedheader/3.1.5/css/fixedHeader.bootstrap.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
    <!--<link rel="stylesheet" href="{{ asset('assets/node_modules/dropify/dist/css/dropify.min.css') }}">-->


    <style>
        body {
            overflow-x: hidden;
        }

        .company-logo {
            max-height: 50px;
            max-width: 120px;
        }

        .company-logo-div {
            border-radius: 2px;
            padding: 2px 2px 2px 2px;
            margin-bottom: 2px;
        }

        .symbol.symbol-circle,
        .symbol.symbol-circle .symbol-label,
        .symbol.symbol-circle>img {
            border-radius: 10%;
        }

        @media only screen and (min-width: 1024px) and (max-width: 1440px) {
            #kt_activitiesDefaultPackage {
                width: 1025px;
            }
        }

        @media only screen and (min-width: 1440px) and (max-width: 2560px) {
            #kt_activitiesDefaultPackage {
                width: 1200px;
            }
        }

        /* Dynamic Template Variables Helper */
        .var-helper-card {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 10px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }
        .var-helper-header {
            background: #F8FAFC;
            border-bottom: 1px solid #EDF2F7;
            padding: 12px 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
        }
        .var-search-box {
            position: relative;
            min-width: 220px;
        }
        .var-search-box input {
            height: 34px;
            border-radius: 6px;
            font-size: 12px;
            border: 1px solid #CBD5E1;
            padding-left: 32px;
            padding-right: 12px;
            background: #FFFFFF;
        }
        .var-search-box .search-icon {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #94A3B8;
        }
        .var-cat-tabs {
            display: flex;
            align-items: center;
            gap: 4px;
            padding: 8px 14px;
            background: #FFFFFF;
            border-bottom: 1px solid #EDF2F7;
            overflow-x: auto;
            white-space: nowrap;
        }
        .var-cat-btn {
            background: transparent;
            border: 1px solid transparent;
            border-radius: 6px;
            padding: 5px 12px;
            font-size: 12px;
            font-weight: 600;
            color: #64748B;
            cursor: pointer;
            transition: all 0.15s ease-in-out;
        }
        .var-cat-btn:hover {
            color: #1E40AF;
            background: #F1F5F9;
        }
        .var-cat-btn.active {
            background: #EFF6FF;
            color: #1D4ED8;
            border-color: #BFDBFE;
        }
        .var-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 10px;
            max-height: 280px;
            overflow-y: auto;
            padding: 14px 16px;
            background: #FAFAFB;
        }
        .var-card {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 8px;
            padding: 9px 12px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 5px;
            transition: all 0.15s ease;
            cursor: pointer;
            user-select: none;
        }
        .var-card:hover {
            border-color: #93C5FD;
            box-shadow: 0 3px 8px rgba(37, 99, 235, 0.08);
            transform: translateY(-1px);
        }
        .var-card-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
        }
        .var-token-pill {
            font-family: 'SFMono-Regular', Menlo, Monaco, Consolas, monospace;
            font-size: 11.5px;
            font-weight: 700;
            color: #1E40AF;
            background: #EFF6FF;
            border: 1px solid #DBEAFE;
            border-radius: 5px;
            padding: 2px 7px;
            letter-spacing: -0.2px;
        }
        .var-card-desc {
            font-size: 11.5px;
            color: #475569;
            line-height: 1.35;
        }
        .var-card-actions {
            display: flex;
            align-items: center;
            gap: 5px;
            margin-top: 2px;
        }
        .var-act-btn {
            font-size: 10px;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 4px;
            border: 1px solid transparent;
            cursor: pointer;
            transition: all 0.12s ease-in-out;
            line-height: 1.35;
            text-decoration: none !important;
        }
        .var-act-btn-body {
            background: #EFF6FF;
            color: #1D4ED8;
            border-color: #BFDBFE;
        }
        .var-act-btn-body:hover {
            background: #DBEAFE;
            color: #1E3A8A;
        }
        .var-act-btn-sub {
            background: #FEF3C7;
            color: #92400E;
            border-color: #FDE68A;
        }
        .var-act-btn-sub:hover {
            background: #FDE68A;
            color: #78350F;
        }
        .var-act-btn-copy {
            background: #F1F5F9;
            color: #475569;
            border-color: #E2E8F0;
            margin-left: auto;
        }
        .var-act-btn-copy:hover {
            background: #E2E8F0;
            color: #0F172A;
        }
        .var-target-toggle {
            display: inline-flex;
            align-items: center;
            background: #F1F5F9;
            border: 1px solid #CBD5E1;
            border-radius: 6px;
            padding: 2px;
            gap: 2px;
        }
        .var-target-choice {
            padding: 3px 10px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            color: #475569;
            cursor: pointer;
            border: none;
            background: transparent;
            transition: all 0.15s;
        }
        .var-target-choice.active {
            background: #2563EB;
            color: #FFFFFF;
            box-shadow: 0 1px 3px rgba(37, 99, 235, 0.3);
        }
    </style>
@endpush

@php 
		use App\Helper\GeneralMethod;
		$salaryMode 					=	GeneralMethod::salaryMode();
		$currencySymbole 				=	GeneralMethod::currencySymbole();
			
@endphp 




@section('content')
    <!--begin::toolbar-->
    <div class="toolbar" id="kt_toolbar">
        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
            <div data-kt-swapper="true" data-kt-swapper-mode="prepend"
                data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}"
                class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
                <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">E-mail Template <span
                        class="h-20px border-gray-200 border-start ms-3 mx-2 d-none"></span> <small
                        class="text-muted fs-7 fw-bold my-1 ms-1 d-none">#XRS-45670</small> </h1>
            </div>
        </div>
    </div>


    <!--begin::Content-->
    <div class="content d-flex flex-column flex-column-fluid pt-4" id="kt_content">
        <!--begin::Post-->
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <!--begin::Container-->
            <div id="kt_content_container" class="container-fluid px-3">
                <!--begin::Card-->
                <div class="card">
                    <!--begin::Card body-->
                    <div class="card-body pt-3 px-5">

                        <table>
                            <tbody>
                            </tbody>
                        </table>
                        <div class="table-responsive">
                            
                        </div>
                        <form id="editSettings" class="ajax-form">
                            @csrf

                            <div class="form-group row mb-4">
                                <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">Template name</label>
                                <div class="col-lg-10 col-xl-10">
                                  <input class="form-control form-control-solid" name="template" id="template" placeholder="Enter Template name" value="{{( @$th_email_template->display_name) ?  @$th_email_template->display_name : '' }}" >
                                  <input type="hidden" name="tokenId" id="tokenId" value="{{ (@$th_email_template->e_temp_id) ? @$th_email_template->e_temp_id : 0 }}" >
                                  <span class="form-text text-muted">Please enter Template name</span> </div>
                              </div>


                        <div class="form-group row mb-4">
                            <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">Subject</label>
                            <div class="col-lg-10 col-xl-10">
                              <input class="form-control form-control-solid" name="subject" id="subject" placeholder="Enter subject" value="{{ (@$th_email_template->template_subject) ? @$th_email_template->template_subject : '' }}" >
                              <span class="form-text text-muted">Please enter subject</span> </div>
                          </div>
                          <div class="form-group row mb-4">
                            <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">Status </label>
                            <div class="col-lg-10 col-xl-10">
                          <select name="status_is" id="status_is" class="form-select form-select-solid" aria-label="Select status_is">
                            <option value="1" {{ @$th_email_template->status ? ($th_email_template->status==1) ? 'selected' : '' : '' }}>Active</option>
                            <option value="0" {{ @$th_email_template->status ? ($th_email_template->status==0) ? 'selected' : '' : '' }}>Inactive</option>    				
                          </select>
                        </div>
                       </div>
                       <div class="form-group row mb-4">
                        <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">Template Types </label>
                        <div class="col-lg-10 col-xl-10">
                      <select name="temptype_is" id="temptype_is" class="form-select form-select-solid" aria-label="Select temptype_is">
                        <option value="1" {{ @$th_email_template->temptype ? ($th_email_template->temptype==1) ? 'selected' : '' : '' }}>Fwd Resume</option>
                        <option value="2" {{ @$th_email_template->temptype ? ($th_email_template->temptype==2) ? 'selected' : '' : '' }}>Mass Mail</option>  
                        <option value="3" {{ @$th_email_template->temptype ? ($th_email_template->temptype==3) ? 'selected' : '' : '' }}>Interview Mail</option>  
                        <option value="4" {{ @$th_email_template->temptype ? ($th_email_template->temptype==4) ? 'selected' : '' : '' }}>Send Mail</option> 
                        <option value="5" {{ @$th_email_template->temptype ? ($th_email_template->temptype==5) ? 'selected' : '' : '' }}>Bench Reject Mail</option> 
                        <option value="6" {{ @$th_email_template->temptype ? ($th_email_template->temptype==6) ? 'selected' : '' : '' }}>Job Reject Mail Marketplace</option> 
                        <option value="7" {{ @$th_email_template->temptype ? ($th_email_template->temptype==7) ? 'selected' : '' : '' }}>Candidate Send Offer Letter</option> 
                        <option value="8" {{ @$th_email_template->temptype ? ($th_email_template->temptype==8) ? 'selected' : '' : '' }}>Management Offer Letter Approval</option>
                        <option value="9" {{ @$th_email_template->temptype ? ($th_email_template->temptype==9) ? 'selected' : '' : '' }}>Whatsapp Template</option> 
                      </select>
                    </div>
                   </div>

                        <div class="form-group row mb-4" id="row_temp_for_interview" style="{{ (@$th_email_template->temptype == 4 || empty($th_email_template->temptype)) ? '' : 'display:none;' }}">
                          <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">Audience Variation</label>
                          <div class="col-lg-10 col-xl-10">
                            <select name="tempForInterview_is" id="tempForInterview_is" class="form-select form-select-solid" aria-label="Select Audience Variation">
                              <option value="1" {{ @$th_email_template->tempForInterview == 1 ? 'selected' : '' }}>1 - Company Opportunity (Internal / Direct Hiring)</option>
                              <option value="2" {{ @$th_email_template->tempForInterview == 2 ? 'selected' : '' }}>2 - Client Opportunity (Staffing / Client Job)</option>
                              <option value="3" {{ @$th_email_template->tempForInterview == 3 ? 'selected' : '' }}>3 - Client Opportunity with Company Payroll</option>
                              <option value="4" {{ @$th_email_template->tempForInterview == 4 ? 'selected' : '' }}>4 - Documents Required</option>
                            </select>
                            <span class="form-text text-muted">Controls whether this template applies to internal jobs, client staffing jobs, or required document requests</span>
                          </div>
                        </div>

                        <!-- Dynamic Template Variables Helper Panel -->
                        <div class="form-group row mb-4">
                          <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">
                            Dynamic Variables
                            <span class="d-block text-muted fs-8 fw-normal mt-1">1-Click Insert into Subject or Body</span>
                          </label>
                          <div class="col-lg-10 col-xl-10">
                            <div class="var-helper-card">
                              <!-- Header: Search + Target Toggle -->
                              <div class="var-helper-header">
                                <div class="d-flex align-items-center gap-2">
                                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                    <line x1="16" y1="13" x2="8" y2="13"></line>
                                    <line x1="16" y1="17" x2="8" y2="17"></line>
                                    <polyline points="10 9 9 9 8 9"></polyline>
                                  </svg>
                                  <span class="fw-bolder fs-6 text-gray-800">Smart Template Variables</span>
                                  <span class="badge badge-light-primary fw-bold fs-8">Click Any Pill To Insert</span>
                                </div>
                                <div class="d-flex align-items-center gap-3">
                                  <!-- Quick Search -->
                                  <div class="var-search-box">
                                    <span class="search-icon">
                                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                                    </span>
                                    <input type="text" id="varSearchInput" class="form-control" placeholder="Search variables (e.g. name, hr, salary)...">
                                  </div>
                                  <!-- Target Field Toggle -->
                                  <div class="d-flex align-items-center gap-1">
                                    <span class="fs-8 text-gray-500 fw-semibold">Insert into:</span>
                                    <div class="var-target-toggle">
                                      <button type="button" class="var-target-choice active" data-target="body">Body</button>
                                      <button type="button" class="var-target-choice" data-target="subject">Subject</button>
                                    </div>
                                  </div>
                                </div>
                              </div>

                              <!-- Category Filter Tabs -->
                              <div class="var-cat-tabs">
                                <button type="button" class="var-cat-btn active" data-cat="all">All Variables</button>
                                <button type="button" class="var-cat-btn" data-cat="candidate">👤 Candidate</button>
                                <button type="button" class="var-cat-btn" data-cat="company">🏢 Company & Client</button>
                                <button type="button" class="var-cat-btn" data-cat="job">💼 Job & Position</button>
                                <button type="button" class="var-cat-btn" data-cat="recruiter">👨‍💼 Recruiter</button>
                                <button type="button" class="var-cat-btn" data-cat="offer">📅 Offer & Interview</button>
                              </div>

                              <!-- Variables Grid Container -->
                              <div class="var-grid" id="varGridContainer">

                                <!-- Candidate Category -->
                                <div class="var-card" data-cat="candidate" data-token="[{CANDIDATE_NAME}]" data-desc="Full name of candidate">
                                  <div class="var-card-top">
                                    <span class="var-token-pill">[{CANDIDATE_NAME}]</span>
                                    <span class="badge badge-light-success fs-9 py-0 px-2">Candidate</span>
                                  </div>
                                  <div class="var-card-desc">Candidate's full name (e.g. Rahul Sharma)</div>
                                  <div class="var-card-actions">
                                    <button type="button" class="var-act-btn var-act-btn-body" onclick="insertVariableToken('[{CANDIDATE_NAME}]', 'body')">+ Body</button>
                                    <button type="button" class="var-act-btn var-act-btn-sub" onclick="insertVariableToken('[{CANDIDATE_NAME}]', 'subject')">+ Subject</button>
                                    <button type="button" class="var-act-btn var-act-btn-copy" onclick="insertVariableToken('[{CANDIDATE_NAME}]', 'copy')" title="Copy to clipboard">📋 Copy</button>
                                  </div>
                                </div>

                                <div class="var-card" data-cat="candidate" data-token="[{CANDIDATE_ID}]" data-desc="Unique candidate ID">
                                  <div class="var-card-top">
                                    <span class="var-token-pill">[{CANDIDATE_ID}]</span>
                                    <span class="badge badge-light-success fs-9 py-0 px-2">Candidate</span>
                                  </div>
                                  <div class="var-card-desc">Unique candidate application ID number</div>
                                  <div class="var-card-actions">
                                    <button type="button" class="var-act-btn var-act-btn-body" onclick="insertVariableToken('[{CANDIDATE_ID}]', 'body')">+ Body</button>
                                    <button type="button" class="var-act-btn var-act-btn-sub" onclick="insertVariableToken('[{CANDIDATE_ID}]', 'subject')">+ Subject</button>
                                    <button type="button" class="var-act-btn var-act-btn-copy" onclick="insertVariableToken('[{CANDIDATE_ID}]', 'copy')" title="Copy to clipboard">📋 Copy</button>
                                  </div>
                                </div>

                                <!-- Company & Client Category -->
                                <div class="var-card" data-cat="company" data-token="[{COMPANY_NAME}]" data-desc="Recruiter or hiring company name">
                                  <div class="var-card-top">
                                    <span class="var-token-pill">[{COMPANY_NAME}]</span>
                                    <span class="badge badge-light-primary fs-9 py-0 px-2">Company</span>
                                  </div>
                                  <div class="var-card-desc">Hiring company or recruiter organization name</div>
                                  <div class="var-card-actions">
                                    <button type="button" class="var-act-btn var-act-btn-body" onclick="insertVariableToken('[{COMPANY_NAME}]', 'body')">+ Body</button>
                                    <button type="button" class="var-act-btn var-act-btn-sub" onclick="insertVariableToken('[{COMPANY_NAME}]', 'subject')">+ Subject</button>
                                    <button type="button" class="var-act-btn var-act-btn-copy" onclick="insertVariableToken('[{COMPANY_NAME}]', 'copy')" title="Copy to clipboard">📋 Copy</button>
                                  </div>
                                </div>

                                <div class="var-card" data-cat="company" data-token="[{CLIENT_NAME}]" data-desc="Client organization name">
                                  <div class="var-card-top">
                                    <span class="var-token-pill">[{CLIENT_NAME}]</span>
                                    <span class="badge badge-light-primary fs-9 py-0 px-2">Company</span>
                                  </div>
                                  <div class="var-card-desc">Client company name (auto-falls back if internal)</div>
                                  <div class="var-card-actions">
                                    <button type="button" class="var-act-btn var-act-btn-body" onclick="insertVariableToken('[{CLIENT_NAME}]', 'body')">+ Body</button>
                                    <button type="button" class="var-act-btn var-act-btn-sub" onclick="insertVariableToken('[{CLIENT_NAME}]', 'subject')">+ Subject</button>
                                    <button type="button" class="var-act-btn var-act-btn-copy" onclick="insertVariableToken('[{CLIENT_NAME}]', 'copy')" title="Copy to clipboard">📋 Copy</button>
                                  </div>
                                </div>

                                <div class="var-card" data-cat="company" data-token="[{COMPANY_WEBSITE}]" data-desc="Recruiter company website URL">
                                  <div class="var-card-top">
                                    <span class="var-token-pill">[{COMPANY_WEBSITE}]</span>
                                    <span class="badge badge-light-primary fs-9 py-0 px-2">Company</span>
                                  </div>
                                  <div class="var-card-desc">Company official website URL (e.g. www.jero.works)</div>
                                  <div class="var-card-actions">
                                    <button type="button" class="var-act-btn var-act-btn-body" onclick="insertVariableToken('[{COMPANY_WEBSITE}]', 'body')">+ Body</button>
                                    <button type="button" class="var-act-btn var-act-btn-sub" onclick="insertVariableToken('[{COMPANY_WEBSITE}]', 'subject')">+ Subject</button>
                                    <button type="button" class="var-act-btn var-act-btn-copy" onclick="insertVariableToken('[{COMPANY_WEBSITE}]', 'copy')" title="Copy to clipboard">📋 Copy</button>
                                  </div>
                                </div>

                                <div class="var-card" data-cat="company" data-token="[{CLIENT_WEBSITE}]" data-desc="Client organization website URL">
                                  <div class="var-card-top">
                                    <span class="var-token-pill">[{CLIENT_WEBSITE}]</span>
                                    <span class="badge badge-light-primary fs-9 py-0 px-2">Company</span>
                                  </div>
                                  <div class="var-card-desc">Client company official website URL</div>
                                  <div class="var-card-actions">
                                    <button type="button" class="var-act-btn var-act-btn-body" onclick="insertVariableToken('[{CLIENT_WEBSITE}]', 'body')">+ Body</button>
                                    <button type="button" class="var-act-btn var-act-btn-sub" onclick="insertVariableToken('[{CLIENT_WEBSITE}]', 'subject')">+ Subject</button>
                                    <button type="button" class="var-act-btn var-act-btn-copy" onclick="insertVariableToken('[{CLIENT_WEBSITE}]', 'copy')" title="Copy to clipboard">📋 Copy</button>
                                  </div>
                                </div>

                                <div class="var-card" data-cat="company" data-token="[{COMPANY_DESCRIPTION}]" data-desc="About company overview">
                                  <div class="var-card-top">
                                    <span class="var-token-pill">[{COMPANY_DESCRIPTION}]</span>
                                    <span class="badge badge-light-primary fs-9 py-0 px-2">Company</span>
                                  </div>
                                  <div class="var-card-desc">About the company profile & background</div>
                                  <div class="var-card-actions">
                                    <button type="button" class="var-act-btn var-act-btn-body" onclick="insertVariableToken('[{COMPANY_DESCRIPTION}]', 'body')">+ Body</button>
                                    <button type="button" class="var-act-btn var-act-btn-sub" onclick="insertVariableToken('[{COMPANY_DESCRIPTION}]', 'subject')">+ Subject</button>
                                    <button type="button" class="var-act-btn var-act-btn-copy" onclick="insertVariableToken('[{COMPANY_DESCRIPTION}]', 'copy')" title="Copy to clipboard">📋 Copy</button>
                                  </div>
                                </div>

                                <div class="var-card" data-cat="company" data-token="[{CLIENT_DESCRIPTION}]" data-desc="About client overview">
                                  <div class="var-card-top">
                                    <span class="var-token-pill">[{CLIENT_DESCRIPTION}]</span>
                                    <span class="badge badge-light-primary fs-9 py-0 px-2">Company</span>
                                  </div>
                                  <div class="var-card-desc">About the client company profile & background</div>
                                  <div class="var-card-actions">
                                    <button type="button" class="var-act-btn var-act-btn-body" onclick="insertVariableToken('[{CLIENT_DESCRIPTION}]', 'body')">+ Body</button>
                                    <button type="button" class="var-act-btn var-act-btn-sub" onclick="insertVariableToken('[{CLIENT_DESCRIPTION}]', 'subject')">+ Subject</button>
                                    <button type="button" class="var-act-btn var-act-btn-copy" onclick="insertVariableToken('[{CLIENT_DESCRIPTION}]', 'copy')" title="Copy to clipboard">📋 Copy</button>
                                  </div>
                                </div>

                                <div class="var-card" data-cat="company" data-token="[{COMPANY_ADDRESS}]" data-desc="Company registered address">
                                  <div class="var-card-top">
                                    <span class="var-token-pill">[{COMPANY_ADDRESS}]</span>
                                    <span class="badge badge-light-primary fs-9 py-0 px-2">Company</span>
                                  </div>
                                  <div class="var-card-desc">Registered company office / job location address</div>
                                  <div class="var-card-actions">
                                    <button type="button" class="var-act-btn var-act-btn-body" onclick="insertVariableToken('[{COMPANY_ADDRESS}]', 'body')">+ Body</button>
                                    <button type="button" class="var-act-btn var-act-btn-sub" onclick="insertVariableToken('[{COMPANY_ADDRESS}]', 'subject')">+ Subject</button>
                                    <button type="button" class="var-act-btn var-act-btn-copy" onclick="insertVariableToken('[{COMPANY_ADDRESS}]', 'copy')" title="Copy to clipboard">📋 Copy</button>
                                  </div>
                                </div>

                                <div class="var-card" data-cat="company" data-token="[{NO_OF_EMP}]" data-desc="Employee size range">
                                  <div class="var-card-top">
                                    <span class="var-token-pill">[{NO_OF_EMP}]</span>
                                    <span class="badge badge-light-primary fs-9 py-0 px-2">Company</span>
                                  </div>
                                  <div class="var-card-desc">Company employee count (e.g. 50-200+ employees)</div>
                                  <div class="var-card-actions">
                                    <button type="button" class="var-act-btn var-act-btn-body" onclick="insertVariableToken('[{NO_OF_EMP}]', 'body')">+ Body</button>
                                    <button type="button" class="var-act-btn var-act-btn-sub" onclick="insertVariableToken('[{NO_OF_EMP}]', 'subject')">+ Subject</button>
                                    <button type="button" class="var-act-btn var-act-btn-copy" onclick="insertVariableToken('[{NO_OF_EMP}]', 'copy')" title="Copy to clipboard">📋 Copy</button>
                                  </div>
                                </div>

                                <!-- Job Category -->
                                <div class="var-card" data-cat="job" data-token="[{JOB_TITLE}]" data-desc="Designation or job title">
                                  <div class="var-card-top">
                                    <span class="var-token-pill">[{JOB_TITLE}]</span>
                                    <span class="badge badge-light-warning fs-9 py-0 px-2">Job</span>
                                  </div>
                                  <div class="var-card-desc">Position / designation title (e.g. Junior Java Developer)</div>
                                  <div class="var-card-actions">
                                    <button type="button" class="var-act-btn var-act-btn-body" onclick="insertVariableToken('[{JOB_TITLE}]', 'body')">+ Body</button>
                                    <button type="button" class="var-act-btn var-act-btn-sub" onclick="insertVariableToken('[{JOB_TITLE}]', 'subject')">+ Subject</button>
                                    <button type="button" class="var-act-btn var-act-btn-copy" onclick="insertVariableToken('[{JOB_TITLE}]', 'copy')" title="Copy to clipboard">📋 Copy</button>
                                  </div>
                                </div>

                                <div class="var-card" data-cat="job" data-token="[{JOB_LOCATION}]" data-desc="Job location city and state">
                                  <div class="var-card-top">
                                    <span class="var-token-pill">[{JOB_LOCATION}]</span>
                                    <span class="badge badge-light-warning fs-9 py-0 px-2">Job</span>
                                  </div>
                                  <div class="var-card-desc">City, state, and country location of the job</div>
                                  <div class="var-card-actions">
                                    <button type="button" class="var-act-btn var-act-btn-body" onclick="insertVariableToken('[{JOB_LOCATION}]', 'body')">+ Body</button>
                                    <button type="button" class="var-act-btn var-act-btn-sub" onclick="insertVariableToken('[{JOB_LOCATION}]', 'subject')">+ Subject</button>
                                    <button type="button" class="var-act-btn var-act-btn-copy" onclick="insertVariableToken('[{JOB_LOCATION}]', 'copy')" title="Copy to clipboard">📋 Copy</button>
                                  </div>
                                </div>

                                <div class="var-card" data-cat="job" data-token="[{JOB_DESCRIPTION}]" data-desc="Full job description">
                                  <div class="var-card-top">
                                    <span class="var-token-pill">[{JOB_DESCRIPTION}]</span>
                                    <span class="badge badge-light-warning fs-9 py-0 px-2">Job</span>
                                  </div>
                                  <div class="var-card-desc">Complete job responsibilities and requirements</div>
                                  <div class="var-card-actions">
                                    <button type="button" class="var-act-btn var-act-btn-body" onclick="insertVariableToken('[{JOB_DESCRIPTION}]', 'body')">+ Body</button>
                                    <button type="button" class="var-act-btn var-act-btn-sub" onclick="insertVariableToken('[{JOB_DESCRIPTION}]', 'subject')">+ Subject</button>
                                    <button type="button" class="var-act-btn var-act-btn-copy" onclick="insertVariableToken('[{JOB_DESCRIPTION}]', 'copy')" title="Copy to clipboard">📋 Copy</button>
                                  </div>
                                </div>

                                <div class="var-card" data-cat="job" data-token="[{WORK_SHIFT}]" data-desc="Work shift timing">
                                  <div class="var-card-top">
                                    <span class="var-token-pill">[{WORK_SHIFT}]</span>
                                    <span class="badge badge-light-warning fs-9 py-0 px-2">Job</span>
                                  </div>
                                  <div class="var-card-desc">Shift hours (e.g. Day Shift, 9:00 AM - 6:00 PM)</div>
                                  <div class="var-card-actions">
                                    <button type="button" class="var-act-btn var-act-btn-body" onclick="insertVariableToken('[{WORK_SHIFT}]', 'body')">+ Body</button>
                                    <button type="button" class="var-act-btn var-act-btn-sub" onclick="insertVariableToken('[{WORK_SHIFT}]', 'subject')">+ Subject</button>
                                    <button type="button" class="var-act-btn var-act-btn-copy" onclick="insertVariableToken('[{WORK_SHIFT}]', 'copy')" title="Copy to clipboard">📋 Copy</button>
                                  </div>
                                </div>

                                <div class="var-card" data-cat="job" data-token="[{JOB_TYPE}]" data-desc="Employment type">
                                  <div class="var-card-top">
                                    <span class="var-token-pill">[{JOB_TYPE}]</span>
                                    <span class="badge badge-light-warning fs-9 py-0 px-2">Job</span>
                                  </div>
                                  <div class="var-card-desc">Employment type (Full-Time, Contract, Part-Time)</div>
                                  <div class="var-card-actions">
                                    <button type="button" class="var-act-btn var-act-btn-body" onclick="insertVariableToken('[{JOB_TYPE}]', 'body')">+ Body</button>
                                    <button type="button" class="var-act-btn var-act-btn-sub" onclick="insertVariableToken('[{JOB_TYPE}]', 'subject')">+ Subject</button>
                                    <button type="button" class="var-act-btn var-act-btn-copy" onclick="insertVariableToken('[{JOB_TYPE}]', 'copy')" title="Copy to clipboard">📋 Copy</button>
                                  </div>
                                </div>

                                <div class="var-card" data-cat="job" data-token="[{JOB_ID}]" data-desc="Job reference ID">
                                  <div class="var-card-top">
                                    <span class="var-token-pill">[{JOB_ID}]</span>
                                    <span class="badge badge-light-warning fs-9 py-0 px-2">Job</span>
                                  </div>
                                  <div class="var-card-desc">System job reference / requisition number</div>
                                  <div class="var-card-actions">
                                    <button type="button" class="var-act-btn var-act-btn-body" onclick="insertVariableToken('[{JOB_ID}]', 'body')">+ Body</button>
                                    <button type="button" class="var-act-btn var-act-btn-sub" onclick="insertVariableToken('[{JOB_ID}]', 'subject')">+ Subject</button>
                                    <button type="button" class="var-act-btn var-act-btn-copy" onclick="insertVariableToken('[{JOB_ID}]', 'copy')" title="Copy to clipboard">📋 Copy</button>
                                  </div>
                                </div>

                                <!-- Recruiter Category -->
                                <div class="var-card" data-cat="recruiter" data-token="[{HR_NAME}]" data-desc="Recruiter person name">
                                  <div class="var-card-top">
                                    <span class="var-token-pill">[{HR_NAME}]</span>
                                    <span class="badge badge-light-info fs-9 py-0 px-2">Recruiter</span>
                                  </div>
                                  <div class="var-card-desc">Logged-in recruiter / HR executive name</div>
                                  <div class="var-card-actions">
                                    <button type="button" class="var-act-btn var-act-btn-body" onclick="insertVariableToken('[{HR_NAME}]', 'body')">+ Body</button>
                                    <button type="button" class="var-act-btn var-act-btn-sub" onclick="insertVariableToken('[{HR_NAME}]', 'subject')">+ Subject</button>
                                    <button type="button" class="var-act-btn var-act-btn-copy" onclick="insertVariableToken('[{HR_NAME}]', 'copy')" title="Copy to clipboard">📋 Copy</button>
                                  </div>
                                </div>

                                <div class="var-card" data-cat="recruiter" data-token="[{HR_EMAIL}]" data-desc="Recruiter email address">
                                  <div class="var-card-top">
                                    <span class="var-token-pill">[{HR_EMAIL}]</span>
                                    <span class="badge badge-light-info fs-9 py-0 px-2">Recruiter</span>
                                  </div>
                                  <div class="var-card-desc">Official email address of the recruiter</div>
                                  <div class="var-card-actions">
                                    <button type="button" class="var-act-btn var-act-btn-body" onclick="insertVariableToken('[{HR_EMAIL}]', 'body')">+ Body</button>
                                    <button type="button" class="var-act-btn var-act-btn-sub" onclick="insertVariableToken('[{HR_EMAIL}]', 'subject')">+ Subject</button>
                                    <button type="button" class="var-act-btn var-act-btn-copy" onclick="insertVariableToken('[{HR_EMAIL}]', 'copy')" title="Copy to clipboard">📋 Copy</button>
                                  </div>
                                </div>

                                <div class="var-card" data-cat="recruiter" data-token="[{HR_PHONE}]" data-desc="Recruiter contact number">
                                  <div class="var-card-top">
                                    <span class="var-token-pill">[{HR_PHONE}]</span>
                                    <span class="badge badge-light-info fs-9 py-0 px-2">Recruiter</span>
                                  </div>
                                  <div class="var-card-desc">Direct mobile / contact phone number of the recruiter</div>
                                  <div class="var-card-actions">
                                    <button type="button" class="var-act-btn var-act-btn-body" onclick="insertVariableToken('[{HR_PHONE}]', 'body')">+ Body</button>
                                    <button type="button" class="var-act-btn var-act-btn-sub" onclick="insertVariableToken('[{HR_PHONE}]', 'subject')">+ Subject</button>
                                    <button type="button" class="var-act-btn var-act-btn-copy" onclick="insertVariableToken('[{HR_PHONE}]', 'copy')" title="Copy to clipboard">📋 Copy</button>
                                  </div>
                                </div>

                                <div class="var-card" data-cat="recruiter" data-token="[{WHAT_LINK}]" data-desc="Clickable WhatsApp link">
                                  <div class="var-card-top">
                                    <span class="var-token-pill">[{WHAT_LINK}]</span>
                                    <span class="badge badge-light-info fs-9 py-0 px-2">Recruiter</span>
                                  </div>
                                  <div class="var-card-desc">Clickable "Connect on WhatsApp" link button</div>
                                  <div class="var-card-actions">
                                    <button type="button" class="var-act-btn var-act-btn-body" onclick="insertVariableToken('[{WHAT_LINK}]', 'body')">+ Body</button>
                                    <button type="button" class="var-act-btn var-act-btn-sub" onclick="insertVariableToken('[{WHAT_LINK}]', 'subject')">+ Subject</button>
                                    <button type="button" class="var-act-btn var-act-btn-copy" onclick="insertVariableToken('[{WHAT_LINK}]', 'copy')" title="Copy to clipboard">📋 Copy</button>
                                  </div>
                                </div>

                                <div class="var-card" data-cat="recruiter" data-token="[{MANAGER_NAME}]" data-desc="Hiring manager name">
                                  <div class="var-card-top">
                                    <span class="var-token-pill">[{MANAGER_NAME}]</span>
                                    <span class="badge badge-light-info fs-9 py-0 px-2">Recruiter</span>
                                  </div>
                                  <div class="var-card-desc">Assigned hiring manager / contact person name</div>
                                  <div class="var-card-actions">
                                    <button type="button" class="var-act-btn var-act-btn-body" onclick="insertVariableToken('[{MANAGER_NAME}]', 'body')">+ Body</button>
                                    <button type="button" class="var-act-btn var-act-btn-sub" onclick="insertVariableToken('[{MANAGER_NAME}]', 'subject')">+ Subject</button>
                                    <button type="button" class="var-act-btn var-act-btn-copy" onclick="insertVariableToken('[{MANAGER_NAME}]', 'copy')" title="Copy to clipboard">📋 Copy</button>
                                  </div>
                                </div>

                                <!-- Offer & Interview Category -->
                                <div class="var-card" data-cat="offer" data-token="[{INTERVIEW_DATE_TIME}]" data-desc="Scheduled interview time">
                                  <div class="var-card-top">
                                    <span class="var-token-pill">[{INTERVIEW_DATE_TIME}]</span>
                                    <span class="badge badge-light-danger fs-9 py-0 px-2">Interview</span>
                                  </div>
                                  <div class="var-card-desc">Scheduled interview date and time</div>
                                  <div class="var-card-actions">
                                    <button type="button" class="var-act-btn var-act-btn-body" onclick="insertVariableToken('[{INTERVIEW_DATE_TIME}]', 'body')">+ Body</button>
                                    <button type="button" class="var-act-btn var-act-btn-sub" onclick="insertVariableToken('[{INTERVIEW_DATE_TIME}]', 'subject')">+ Subject</button>
                                    <button type="button" class="var-act-btn var-act-btn-copy" onclick="insertVariableToken('[{INTERVIEW_DATE_TIME}]', 'copy')" title="Copy to clipboard">📋 Copy</button>
                                  </div>
                                </div>

                                <div class="var-card" data-cat="offer" data-token="[{INTERVIEW_LINK}]" data-desc="Meeting link (Google Meet/Zoom)">
                                  <div class="var-card-top">
                                    <span class="var-token-pill">[{INTERVIEW_LINK}]</span>
                                    <span class="badge badge-light-danger fs-9 py-0 px-2">Interview</span>
                                  </div>
                                  <div class="var-card-desc">Video call interview URL (Google Meet / Zoom / Teams)</div>
                                  <div class="var-card-actions">
                                    <button type="button" class="var-act-btn var-act-btn-body" onclick="insertVariableToken('[{INTERVIEW_LINK}]', 'body')">+ Body</button>
                                    <button type="button" class="var-act-btn var-act-btn-sub" onclick="insertVariableToken('[{INTERVIEW_LINK}]', 'subject')">+ Subject</button>
                                    <button type="button" class="var-act-btn var-act-btn-copy" onclick="insertVariableToken('[{INTERVIEW_LINK}]', 'copy')" title="Copy to clipboard">📋 Copy</button>
                                  </div>
                                </div>

                                <div class="var-card" data-cat="offer" data-token="[{INTERVIEW_MODE}]" data-desc="Mode of interview">
                                  <div class="var-card-top">
                                    <span class="var-token-pill">[{INTERVIEW_MODE}]</span>
                                    <span class="badge badge-light-danger fs-9 py-0 px-2">Interview</span>
                                  </div>
                                  <div class="var-card-desc">Interview format (Online Video / In-Person / Telephonic)</div>
                                  <div class="var-card-actions">
                                    <button type="button" class="var-act-btn var-act-btn-body" onclick="insertVariableToken('[{INTERVIEW_MODE}]', 'body')">+ Body</button>
                                    <button type="button" class="var-act-btn var-act-btn-sub" onclick="insertVariableToken('[{INTERVIEW_MODE}]', 'subject')">+ Subject</button>
                                    <button type="button" class="var-act-btn var-act-btn-copy" onclick="insertVariableToken('[{INTERVIEW_MODE}]', 'copy')" title="Copy to clipboard">📋 Copy</button>
                                  </div>
                                </div>

                                <div class="var-card" data-cat="offer" data-token="[{JOINING_DATE}]" data-desc="Proposed joining date">
                                  <div class="var-card-top">
                                    <span class="var-token-pill">[{JOINING_DATE}]</span>
                                    <span class="badge badge-light-danger fs-9 py-0 px-2">Offer</span>
                                  </div>
                                  <div class="var-card-desc">Expected joining date (e.g. 01 Nov 2026)</div>
                                  <div class="var-card-actions">
                                    <button type="button" class="var-act-btn var-act-btn-body" onclick="insertVariableToken('[{JOINING_DATE}]', 'body')">+ Body</button>
                                    <button type="button" class="var-act-btn var-act-btn-sub" onclick="insertVariableToken('[{JOINING_DATE}]', 'subject')">+ Subject</button>
                                    <button type="button" class="var-act-btn var-act-btn-copy" onclick="insertVariableToken('[{JOINING_DATE}]', 'copy')" title="Copy to clipboard">📋 Copy</button>
                                  </div>
                                </div>

                                <div class="var-card" data-cat="offer" data-token="[{SALARY_AMOUNT}]" data-desc="Offered salary or CTC">
                                  <div class="var-card-top">
                                    <span class="var-token-pill">[{SALARY_AMOUNT}]</span>
                                    <span class="badge badge-light-danger fs-9 py-0 px-2">Offer</span>
                                  </div>
                                  <div class="var-card-desc">Offered salary or compensation package amount</div>
                                  <div class="var-card-actions">
                                    <button type="button" class="var-act-btn var-act-btn-body" onclick="insertVariableToken('[{SALARY_AMOUNT}]', 'body')">+ Body</button>
                                    <button type="button" class="var-act-btn var-act-btn-sub" onclick="insertVariableToken('[{SALARY_AMOUNT}]', 'subject')">+ Subject</button>
                                    <button type="button" class="var-act-btn var-act-btn-copy" onclick="insertVariableToken('[{SALARY_AMOUNT}]', 'copy')" title="Copy to clipboard">📋 Copy</button>
                                  </div>
                                </div>

                                <div class="var-card" data-cat="offer" data-token="[{SALARY_MODE}]" data-desc="Salary period">
                                  <div class="var-card-top">
                                    <span class="var-token-pill">[{SALARY_MODE}]</span>
                                    <span class="badge badge-light-danger fs-9 py-0 px-2">Offer</span>
                                  </div>
                                  <div class="var-card-desc">Salary frequency (Per Annum, Per Month, Per Hour)</div>
                                  <div class="var-card-actions">
                                    <button type="button" class="var-act-btn var-act-btn-body" onclick="insertVariableToken('[{SALARY_MODE}]', 'body')">+ Body</button>
                                    <button type="button" class="var-act-btn var-act-btn-sub" onclick="insertVariableToken('[{SALARY_MODE}]', 'subject')">+ Subject</button>
                                    <button type="button" class="var-act-btn var-act-btn-copy" onclick="insertVariableToken('[{SALARY_MODE}]', 'copy')" title="Copy to clipboard">📋 Copy</button>
                                  </div>
                                </div>

                                <div class="var-card" data-cat="offer" data-token="[{CURRENCY}]" data-desc="Currency symbol">
                                  <div class="var-card-top">
                                    <span class="var-token-pill">[{CURRENCY}]</span>
                                    <span class="badge badge-light-danger fs-9 py-0 px-2">Offer</span>
                                  </div>
                                  <div class="var-card-desc">Currency code / symbol (e.g. ₹, $, €)</div>
                                  <div class="var-card-actions">
                                    <button type="button" class="var-act-btn var-act-btn-body" onclick="insertVariableToken('[{CURRENCY}]', 'body')">+ Body</button>
                                    <button type="button" class="var-act-btn var-act-btn-sub" onclick="insertVariableToken('[{CURRENCY}]', 'subject')">+ Subject</button>
                                    <button type="button" class="var-act-btn var-act-btn-copy" onclick="insertVariableToken('[{CURRENCY}]', 'copy')" title="Copy to clipboard">📋 Copy</button>
                                  </div>
                                </div>

                                <div class="var-card" data-cat="offer" data-token="[{OFFER_LETTER_LINK}]" data-desc="Online offer acceptance URL">
                                  <div class="var-card-top">
                                    <span class="var-token-pill">[{OFFER_LETTER_LINK}]</span>
                                    <span class="badge badge-light-danger fs-9 py-0 px-2">Offer</span>
                                  </div>
                                  <div class="var-card-desc">Candidate online offer letter review & acceptance link</div>
                                  <div class="var-card-actions">
                                    <button type="button" class="var-act-btn var-act-btn-body" onclick="insertVariableToken('[{OFFER_LETTER_LINK}]', 'body')">+ Body</button>
                                    <button type="button" class="var-act-btn var-act-btn-sub" onclick="insertVariableToken('[{OFFER_LETTER_LINK}]', 'subject')">+ Subject</button>
                                    <button type="button" class="var-act-btn var-act-btn-copy" onclick="insertVariableToken('[{OFFER_LETTER_LINK}]', 'copy')" title="Copy to clipboard">📋 Copy</button>
                                  </div>
                                </div>

                              </div>

                              <!-- No Results Notice -->
                              <div id="varNoResults" class="p-6 text-center text-muted fs-7" style="display:none;">
                                No variables match your search filter.
                              </div>

                              <!-- Card Footer Info -->
                              <div class="d-flex align-items-center justify-content-between py-2 px-4 bg-light border-top border-gray-200 fs-8 text-muted">
                                <div>
                                  💡 <strong>1-Click Action:</strong> Click any variable or <strong>+ Body</strong> to insert into CKEditor, or <strong>+ Subject</strong> for email title.
                                </div>
                                <div id="varFeedbackMsg" class="text-success fw-bold" style="display:none;"></div>
                              </div>
                            </div>
                          </div>
                        </div>

                          <!--begin::Body-->
                          <div class="form-group row mb-4">
                            <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">Body</label>
                            <div class="col-lg-10 col-xl-10">
                              <textarea  id="editor1" name="editor1" row="10" >{{ (@$th_email_template->template_description) ? @$th_email_template->template_description : '' }}</textarea>
                              <span class="form-text text-muted"> Please enter email template body content </span> </div>
                          </div>

                          <div class="form-row row fv-row">
                            <div class="col-12 text-center py-10">

                            <button type="button" id="save-form" class="btn btn-success waves-effect waves-light m-r-10">
                                @lang('app.save')
                            </button>

                        </div>
                    </div>
                    </form>

                    </div>
                    <!--end::Card body-->
                </div>
                <!--end::Card-->
            </div>
            <!--end::Container-->
        </div>
        <!--end::Post-->





    </div>
    <!--end::Content-->


@endsection

@push('footer-script')
    <script src="//cdn.datatables.net/fixedheader/3.1.5/js/dataTables.fixedHeader.min.js"></script>
    <script src="//cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
    <script src="//cdn.datatables.net/responsive/2.2.3/js/responsive.bootstrap.min.js"></script>
    <script src="//cdn.datatables.net/colreorder/1.5.2/js/dataTables.colReorder.min.js"></script>
    <script src="//cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
    <script src="//cdn.datatables.net/buttons/1.6.1/js/buttons.colVis.min.js"></script>
    <script src="{{ asset('demo/v7/js/pages/crud/forms/widgets/select2.js') }}<?php echo TM_VERSION; ?>"></script>
    <script src="{{ asset('demo/cdn/js/bootstrap-select.js') }}<?php echo TM_VERSION; ?>"></script>

    <script src="{{ asset('demo/ckeditor/ckeditor.js') }}"></script>
    <script>
									
        if ( CKEDITOR.env.ie && CKEDITOR.env.version < 9 )
            
        CKEDITOR.tools.enableHtml5Elements( document );
        CKEDITOR.config.height 		=	350;
        CKEDITOR.config.width 		=	'auto';
        
        var initSample 				= ( function() 
        {
                var wysiwygareaAvailable 	= isWysiwygareaAvailable(),
                isBBCodeBuiltIn 			= !!CKEDITOR.plugins.get( 'bbcode' );
        
            return function() {
                                        var editorElement = CKEDITOR.document.getById( 'editor1' );
                                
                                        if ( isBBCodeBuiltIn ) {
                                            editorElement.setHtml(
                                                'Hello world!\n\n' +
                                                'I\'m an instance of [url=https://ckeditor.com]CKEditor[/url].'
                                            );
                                        }
                                        if ( wysiwygareaAvailable ) {
                                            CKEDITOR.replace( 'editor1' );
                                        } else {
                                            editorElement.setAttribute( 'contenteditable', 'true' );   
                                            CKEDITOR.inline( 'editor1' );
                                        }							
                            };
            function isWysiwygareaAvailable() 
            {
                if ( CKEDITOR.revision == ( '%RE' + 'V%' ) ) {
                    return true;
                }
                return !!CKEDITOR.plugins.get( 'wysiwygarea' );
            }
            
        } )();
        
        
        initSample();
        
    </script>

    <script>
        // 1-Click Variable Helper Engine
        var currentVarTarget = 'body';

        $('.var-target-choice').on('click', function(e) {
            e.preventDefault();
            $('.var-target-choice').removeClass('active');
            $(this).addClass('active');
            currentVarTarget = $(this).data('target') || 'body';
        });

        // Switch target automatically when Subject input receives focus
        $('#subject').on('focus', function() {
            currentVarTarget = 'subject';
            $('.var-target-choice').removeClass('active');
            $('.var-target-choice[data-target="subject"]').addClass('active');
        });

        // Switch target to Body when CKEditor receives focus
        if (typeof CKEDITOR !== 'undefined') {
            CKEDITOR.on('instanceReady', function(evt) {
                if (evt.editor && evt.editor.name === 'editor1') {
                    evt.editor.on('focus', function() {
                        currentVarTarget = 'body';
                        $('.var-target-choice').removeClass('active');
                        $('.var-target-choice[data-target="body"]').addClass('active');
                    });
                }
            });
        }

        // Toggle Audience Variation row based on Template Type (Send Mail = 4)
        $('#temptype_is').on('change', function() {
            if ($(this).val() == '4') {
                $('#row_temp_for_interview').slideDown(150);
            } else {
                $('#row_temp_for_interview').slideUp(150);
            }
        });

        function copyVariableToClipboard(text) {
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(text);
            } else {
                var temp = document.createElement('textarea');
                temp.value = text;
                document.body.appendChild(temp);
                temp.select();
                document.execCommand('copy');
                document.body.removeChild(temp);
            }
        }

        window.insertVariableToken = function(token, explicitTarget, event) {
            if (event) {
                event.stopPropagation();
            }

            var target = explicitTarget || currentVarTarget;
            copyVariableToClipboard(token);

            if (target === 'copy') {
                var $fb = $('#varFeedbackMsg');
                $fb.html('✓ Copied <strong>' + token + '</strong> to clipboard!').stop(true, true).fadeIn(150).delay(2500).fadeOut(400);
                if (typeof toastr !== 'undefined') {
                    toastr.info(token + ' copied to clipboard', 'Copied', { timeOut: 1800, positionClass: 'toast-bottom-right' });
                }
                return;
            }

            if (target === 'subject') {
                var subjectInput = document.getElementById('subject');
                if (subjectInput) {
                    var start = subjectInput.selectionStart || subjectInput.value.length;
                    var end = subjectInput.selectionEnd || subjectInput.value.length;
                    var val = subjectInput.value;
                    subjectInput.value = val.substring(0, start) + token + val.substring(end);
                    subjectInput.focus();
                    var nextPos = start + token.length;
                    subjectInput.setSelectionRange(nextPos, nextPos);
                }
            } else {
                if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances && CKEDITOR.instances.editor1) {
                    CKEDITOR.instances.editor1.insertHtml(token);
                    CKEDITOR.instances.editor1.focus();
                } else {
                    var bodyTextarea = document.getElementById('editor1');
                    if (bodyTextarea) {
                        bodyTextarea.value += token;
                    }
                }
            }

            var targetLabel = (target === 'subject') ? 'Subject' : 'Email Body';
            var $fb = $('#varFeedbackMsg');
            $fb.html('✓ <strong>' + token + '</strong> inserted into ' + targetLabel + ' and copied!').stop(true, true).fadeIn(150).delay(2500).fadeOut(400);

            if (typeof toastr !== 'undefined') {
                toastr.success(token + ' inserted into ' + targetLabel, 'Variable Added', { timeOut: 2000, positionClass: 'toast-bottom-right' });
            }
        };

        // Make card click trigger default target insertion
        $(document).on('click', '.var-card', function(e) {
            // Prevent double triggering if clicked directly on a button inside the card
            if ($(e.target).closest('.var-act-btn').length > 0) {
                return;
            }
            var token = $(this).data('token');
            if (token) {
                window.insertVariableToken(token);
            }
        });

        // Filter by category
        $('.var-cat-btn').on('click', function(e) {
            e.preventDefault();
            $('.var-cat-btn').removeClass('active');
            $(this).addClass('active');
            applyVariableFilters();
        });

        // Real-time search filter
        $('#varSearchInput').on('keyup input', function() {
            applyVariableFilters();
        });

        function applyVariableFilters() {
            var activeCat = $('.var-cat-btn.active').data('cat') || 'all';
            var query = ($('#varSearchInput').val() || '').toLowerCase().trim();

            var visibleCount = 0;
            $('.var-card').each(function() {
                var cardCat = $(this).data('cat') || '';
                var cardToken = ($(this).data('token') || '').toLowerCase();
                var cardDesc = ($(this).data('desc') || '').toLowerCase();

                var matchesCat = (activeCat === 'all' || cardCat === activeCat);
                var matchesQuery = (!query || cardToken.indexOf(query) !== -1 || cardDesc.indexOf(query) !== -1);

                if (matchesCat && matchesQuery) {
                    $(this).show();
                    visibleCount++;
                } else {
                    $(this).hide();
                }
            });

            if (visibleCount === 0) {
                $('#varNoResults').show();
            } else {
                $('#varNoResults').hide();
            }
        }

        // Form Submission
        $('#save-form').click(function() {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
            });

            var id = $('#tokenId').val();
            var url = "{{ route('superadmin.company.addEditemailThemesupdate', ':id') }}";
            url = url.replace(':id', id);
            var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
            var token = "{{ csrf_token() }}";

            var template = $('#template').val();
            var subject = $('#subject').val();
            var status_is = $('#status_is').val();
            var temptype_is = $('#temptype_is').val();
            var tempForInterview_is = $('#tempForInterview_is').val() || 1;
            var editor1 = (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances && CKEDITOR.instances.editor1)
                ? CKEDITOR.instances.editor1.getData()
                : ($('#editor1').val() || '');

            var urlredirect = "{{ route('superadmin.company.emailthemes') }}";

            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    '_token': token,
                    '_method': 'POST',
                    'template': template,
                    'subject': subject,
                    'editor1': editor1,
                    'tokenId': id,
                    'status_is': status_is,
                    'temptype_is': temptype_is,
                    'tempForInterview_is': tempForInterview_is
                },
                beforeSend: function() {
                    $('#main_container').html('<img src="' + baseurl + '" height="35px" width="100%">');
                },
                success: function(response) {
                    if (response.status == "success") {
                        swalWithBootstrapButtons.fire(
                            'Success!',
                            'Email template saved successfully.',
                            'success'
                        );
                        window.location.href = urlredirect;
                    }
                }
            });
        });


    
    $('#ck-editor__editable_inline').html("testing");

        $('body').on('click', '#editDefaultPackage', function() {

            $('#kt_activitiesDefaultPackage_toggle').trigger('click');
            var url = "{{ route('superadmin.defaultpackages.editDefaultPackage') }}";
            var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
            $.ajax({
                type: 'GET',
                url: url,
                beforeSend: function() {
                    $('#kt_activitiesDefaultPackage_body').html('<img src="' + baseurl +
                        '" height="35px" width="100%">');
                },
                success: function(response) {
                    setTimeout(
                        () => {
                            if (response.status == "success") {
                                $('#kt_activitiesDefaultPackage_body').html(response.view);
                            }
                        }, 500);

                }
            });
        });
    </script>
@endpush
