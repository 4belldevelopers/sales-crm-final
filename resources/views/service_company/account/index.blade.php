@extends('layouts.service_company_layout')
<?php
use App\Helper\GeneralMethod;

// dd("jd");
$productAllowCompany = GeneralMethod::companiesType();
?>
<link rel="stylesheet" href="{{ asset('demo/plugins/custom/jkanban/jkanban.bundle.css') }}">
<style>
body {overflow-x:hidden; }
.table > :not(caption) > * > * { padding: 0.9rem 0.2rem!important; }
.kanban-container .kanban-board { float: none; flex-shrink: 0; margin-bottom: 1.25rem; background: #fff; border-radius: 0.475rem; padding-right: 1.5em!important; }
header { position: relative !important; top: 0; left: 0; display: flex; flex-direction: row; flex-wrap: wrap; width: 100%; height: 80px; background-color: #318ebb; box-shadow: 0 0px 8px rgb(212 212 212 / 40%);}
.svg-icon.svg-icon-danger-Sourcing svg [fill]:not(.permanent):not(g) { transition: fill 0.3s ease; fill: #318ebb !important }
.svg-icon.svg-icon-danger-Screening svg [fill]:not(.permanent):not(g) { transition: fill 0.3s ease;fill: #c000ff !important }
.svg-icon.svg-icon-danger-Evaluation svg [fill]:not(.permanent):not(g) { transition: fill 0.3s ease; fill: #009ef7 !important }
.svg-icon.svg-icon-danger-Negotiation svg [fill]:not(.permanent):not(g) { transition: fill 0.3s ease; fill: #ff4098 !important}
.svg-icon.svg-icon-danger-Offer svg [fill]:not(.permanent):not(g) { transition: fill 0.3s ease; fill: #ffab00 !important}
.svg-icon.svg-icon-danger-Joined svg [fill]:not(.permanent):not(g) { transition: fill 0.3s ease; fill: #50cd89 !important }


@media only screen and (min-width: 1024px) and (max-width: 1440px){
  #kt_activitiesJob {width: 1025px;}
}
@media only screen and (min-width: 1440px) and (max-width: 2560px){
  #kt_activitiesJob {width: 1200px;}
}

.keyword_compant {
    display: flex !important;
}
.swal2-container.swal2-center>.swal2-popup {
    display: block !important;
}
.card {
    box-shadow: 0px 0px 20px 8px rgb(1 6 24 / 2%) !important;
    border: 0px solid;
}

input[type="radio"]:checked {
    border-color: transparent;
    background-color: currentColor;
}

/* Company settings: visual-only enterprise card system; existing form contracts stay intact. */
.company-settings-v2,.company-settings-v2 *{font-family:'Poppins',-apple-system,BlinkMacSystemFont,sans-serif!important;box-sizing:border-box}
.company-settings-v2{color:#101e4e;font-size:12px;font-weight:500;padding:0 0 2px}
.company-settings-v2 .csv2-heading{margin:0 0 18px}
.company-settings-v2 .csv2-heading h2{margin:0;color:#0e1b4d;font-size:16px;font-weight:600;line-height:1.3}
.company-settings-v2 .csv2-heading p,.company-settings-v2 .csv2-section-sub{margin:4px 0 0;color:#52638b;font-size:11px;font-weight:500;line-height:1.45}
.company-settings-v2 .card{display:block!important;height:auto!important;min-height:0!important;margin:0!important;background:transparent!important;border:0!important;border-radius:0!important;box-shadow:none!important;position:relative!important;flex:none!important}
.company-settings-v2>.card{overflow:hidden;background:#fff!important;border:1px solid #cfd8e8!important;border-radius:10px!important;box-shadow:0 3px 14px rgba(16,30,78,.07)!important}
.company-settings-v2 .card.d-none,.company-settings-v2 .settings-email-notifications{display:none!important}
.company-settings-v2 .settings-general-card,.company-settings-v2 .settings-duplicate-card,.company-settings-v2 .settings-department-card{margin:14px!important;background:#fff!important;border:1px solid #d3dcea!important;border-radius:9px!important;box-shadow:0 2px 7px rgba(16,30,78,.055)!important}
.company-settings-v2 .card-header{min-height:0!important;padding:20px 20px 9px!important;border:0!important;background:#fff!important}
.company-settings-v2 .card-title h3{margin:0;color:#101e4e;font-size:13px!important;font-weight:600!important}
.company-settings-v2 .settings-section-content,.company-settings-v2>.card>.card-body{display:block!important;height:auto!important;visibility:visible!important;opacity:1!important}
.company-settings-v2 #setting_one>.card-body{display:grid!important;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px;padding:12px 20px 22px!important;border:0!important}
.company-settings-v2 #setting_one .row{display:grid!important;grid-template-columns:48px minmax(0,1fr);grid-template-rows:auto auto;column-gap:12px;align-items:center;min-width:0;min-height:82px;margin:0!important;padding:12px!important;background:#fff;border:1px solid #ccd7e8;border-radius:8px;box-shadow:0 1px 3px rgba(16,30,78,.035);transition:border-color .15s,box-shadow .15s}
.company-settings-v2 #setting_one .row:hover,.company-settings-v2 #setting_one .row:focus-within{border-color:#8eaae0;box-shadow:0 4px 14px rgba(25,68,150,.10)}
.company-settings-v2 #setting_one .row.d-none{display:none!important}
.company-settings-v2 #setting_one .row>label{grid-column:2;grid-row:1;width:auto;max-width:none;margin:0!important;padding:0;color:#13224f;font-size:11px!important;font-weight:600!important;line-height:1.35}
.company-settings-v2 #setting_one .row>.fv-row{grid-column:2;grid-row:2;width:auto;max-width:none;min-width:0;padding:0}
.company-settings-v2 .csv2-field-icon{grid-column:1;grid-row:1/3;display:flex;width:42px;height:42px;align-items:center;justify-content:center;border-radius:10px}
.company-settings-v2 .csv2-field-icon svg{width:23px;height:23px;fill:none;stroke:currentColor;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round}
.company-settings-v2 .csv2-blue{color:#1764ef;background:#edf4ff}.company-settings-v2 .csv2-purple{color:#7638eb;background:#f3edff}.company-settings-v2 .csv2-green{color:#159957;background:#ebf9f0}.company-settings-v2 .csv2-orange{color:#f46a22;background:#fff1e8}
.company-settings-v2 #setting_one .form-select{width:100%;min-height:30px;padding:3px 29px 3px 0!important;color:#334873;font-size:11px;font-weight:500;border:0!important;border-radius:0!important;background-color:transparent!important;box-shadow:none!important;cursor:pointer;text-overflow:ellipsis}
.company-settings-v2 #setting_one .form-select:focus{color:#172a58;outline:0}
.company-settings-v2 #setting_one .form-select option,.company-settings-v2 #setting_one .form-select optgroup{color:#354a76;font-family:'Poppins',sans-serif;font-size:11px;font-weight:500;background:#fff}
.company-settings-v2 #setting_one .select2-container{width:100%!important;min-width:0}
.company-settings-v2 #setting_one .select2-container .select2-selection--single{height:30px!important;padding:0!important;background:transparent!important;border:0!important;border-radius:0!important;box-shadow:none!important}
.company-settings-v2 #setting_one .select2-container .select2-selection__rendered{height:30px!important;padding:5px 28px 4px 0!important;color:#334873!important;font-size:11px!important;font-weight:500!important;line-height:21px!important;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.company-settings-v2 #setting_one .select2-container .select2-selection__arrow{right:0!important;top:2px!important;width:22px!important;height:26px!important}
.company-settings-v2 #setting_one .select2-container--focus .select2-selection--single{outline:0!important}
.select2-dropdown.csv2-enterprise-dropdown,.csv2-enterprise-select .select2-dropdown{z-index:1065;margin-top:7px;padding:8px;background:#fff;border:1px solid #dce3ef!important;border-radius:9px!important;box-shadow:0 16px 38px rgba(16,30,78,.18)!important;font-family:'Poppins',sans-serif;overflow:hidden}
.select2-dropdown.csv2-enterprise-dropdown.csv2-timezone-dropdown{width:min(390px,calc(100vw - 24px))!important;min-width:min(390px,calc(100vw - 24px))!important}
.select2-dropdown.csv2-enterprise-dropdown.csv2-timeformat-dropdown{width:min(290px,calc(100vw - 24px))!important;min-width:min(290px,calc(100vw - 24px))!important}
.select2-dropdown.csv2-enterprise-dropdown .select2-search--dropdown{padding:3px 3px 8px}
.select2-dropdown.csv2-enterprise-dropdown .select2-search--dropdown::before{content:'Search';display:block;margin:0 0 5px 2px;color:#263a68;font-size:10px;font-weight:500;letter-spacing:.1px}
.select2-dropdown.csv2-enterprise-dropdown .select2-search__field{height:36px;padding:7px 11px;color:#263a68;font-family:'Poppins',sans-serif;font-size:11px;font-weight:500;border:1px solid #cdd8e7!important;border-radius:6px!important;outline:0}
.select2-dropdown.csv2-enterprise-dropdown .select2-search__field:focus{border-color:#1764ef!important;box-shadow:0 0 0 3px rgba(23,100,239,.08)}
.select2-dropdown.csv2-enterprise-dropdown .select2-results__options{max-height:260px!important;padding:2px;scrollbar-width:thin;scrollbar-color:#cbd5e1 transparent}
.select2-dropdown.csv2-enterprise-dropdown .select2-results__group{padding:9px 10px 5px;color:#172957;font-size:10px;font-weight:500;letter-spacing:.2px;text-transform:uppercase}
.select2-dropdown.csv2-enterprise-dropdown .select2-results__option{min-height:36px;margin:2px 0;padding:8px 10px;color:#354a76;font-size:11px;font-weight:500;line-height:19px;border-radius:6px}
.select2-dropdown.csv2-enterprise-dropdown.csv2-timezone-dropdown .select2-results__option{white-space:normal;overflow-wrap:anywhere}
.select2-dropdown.csv2-enterprise-dropdown.csv2-timeformat-dropdown .select2-results__option{display:flex;align-items:center;min-height:42px;padding:9px 11px;white-space:normal}
.select2-dropdown.csv2-enterprise-dropdown .select2-results__option--highlighted[aria-selected]{color:#154dbb!important;background:#edf4ff!important}
.select2-dropdown.csv2-enterprise-dropdown .select2-results__option[aria-selected=true]{position:relative;padding-right:34px;color:#0f49bd!important;font-weight:600;background:#e8f1ff!important}
.select2-dropdown.csv2-enterprise-dropdown .select2-results__option[aria-selected=true]::after{content:'✓';position:absolute;right:11px;top:50%;transform:translateY(-50%);display:flex;width:18px;height:18px;align-items:center;justify-content:center;color:#fff;font-size:10px;background:#1764ef;border-radius:50%}
.select2-dropdown.csv2-enterprise-dropdown.select2-dropdown--above{margin-top:-7px;margin-bottom:7px}
.company-settings-v2 #setting_one .form-text{display:none}
.company-settings-v2 #application_duplicate_settings>.card-body{padding:12px 20px 22px!important;border:0!important}
.company-settings-v2 #application_duplicate_settings .row{display:grid!important;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px;margin:0!important}
.company-settings-v2 #application_duplicate_settings .col-lg-4{width:auto;max-width:none;padding:0}
.company-settings-v2 #application_duplicate_settings label.option{display:grid!important;grid-template-columns:26px minmax(0,1fr);height:130px;margin:0;padding:15px;background:#fff;border:1px solid #ccd7e8;border-radius:8px;box-shadow:0 1px 3px rgba(16,30,78,.035);cursor:pointer;transition:border-color .15s,box-shadow .15s}
.company-settings-v2 #application_duplicate_settings label.option:hover{border-color:#b8c8ed}
.company-settings-v2 #application_duplicate_settings label.option:has(input:checked){border-color:#1764ff;box-shadow:0 0 0 1px #1764ff inset}
.company-settings-v2 #application_duplicate_settings .option-control{grid-column:1;margin:1px 0 0}
.company-settings-v2 #application_duplicate_settings .option-label{grid-column:2;min-width:0}
.company-settings-v2 #application_duplicate_settings .option-head{display:flex!important;align-items:flex-start;justify-content:space-between;gap:8px;min-height:34px}
.company-settings-v2 #application_duplicate_settings .option-title{color:#142453;font-size:11px!important;font-weight:600!important;line-height:1.4}
.company-settings-v2 #application_duplicate_settings .option-body{display:block;color:#56678f;font-size:10px;font-weight:500;line-height:1.5}
.company-settings-v2 #application_duplicate_settings input[type=radio]{width:17px;height:17px;margin:0;accent-color:#1764ff}
.company-settings-v2 .csv2-days-row{display:flex;align-items:center;gap:10px;margin-top:15px;color:#56678f;font-size:10px;font-weight:500}
.company-settings-v2 #application_duplicate_settings input[type=number]{width:74px!important;height:32px;flex:0 0 74px;margin:0;padding:5px 9px;color:#172957;font-size:11px;font-weight:600;background:#fff;border:1px solid #ccd7e8;border-radius:6px!important;outline:0}
.company-settings-v2 #application_duplicate_settings input[type=number]:focus{border-color:#1764ff;box-shadow:0 0 0 3px rgba(23,100,255,.09)}
.company-settings-v2 .settings-department-card .card-header{display:flex;align-items:center;justify-content:space-between;padding-top:20px!important}
.company-settings-v2 .settings-department-card .card-header>.card-title:last-child{margin-left:auto!important}
.company-settings-v2 .settings-department-card .btn-primary{display:inline-flex;align-items:center;gap:6px;padding:8px 14px;color:#fff;font-size:11px;font-weight:600;background:#1457e8;border:1px solid #1457e8;border-radius:5px;box-shadow:0 3px 8px rgba(20,87,232,.16)}
.company-settings-v2 .settings-department-card .btn-primary::before{content:'+';font-size:14px;line-height:1}
.company-settings-v2 .dept_content{display:grid!important;grid-template-columns:repeat(4,minmax(0,1fr));gap:11px;padding:10px 20px 22px!important;border:0!important}
.company-settings-v2 .dept_content .form-group,.company-settings-v2 .dept_content .row{display:contents!important}
.company-settings-v2 .dept_content .form-check,.company-settings-v2 .dept_content .form-text{display:none!important}
.company-settings-v2 .dept_content .col-lg-4{width:auto;max-width:none;padding:0}
.company-settings-v2 .dept_content .badge{position:relative;width:100%;min-height:44px;padding:8px 34px 8px 55px!important;color:#40517b;font-weight:500;background:#fff!important;border:1px solid #ccd7e8;border-radius:7px;box-shadow:0 1px 3px rgba(16,30,78,.035);overflow:hidden}
.company-settings-v2 .dept_content .badge::before{content:'';position:absolute;left:12px;top:9px;width:26px;height:26px;border-radius:6px;background:#edf4ff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%231764ef' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2'/%3E%3Ccircle cx='9' cy='7' r='4'/%3E%3Cpath d='M22 21v-2a4 4 0 0 0-3-3.87'/%3E%3C/svg%3E") center/14px no-repeat}
.company-settings-v2 .dept_content .col-lg-4:nth-of-type(4n+2) .badge::before{background-color:#f3edff;filter:hue-rotate(35deg)}
.company-settings-v2 .dept_content .col-lg-4:nth-of-type(4n+3) .badge::before{background-color:#ebf9f0;filter:hue-rotate(105deg)}
.company-settings-v2 .dept_content .col-lg-4:nth-of-type(4n+4) .badge::before{background-color:#fff1e8;filter:hue-rotate(185deg)}
.company-settings-v2 .dept_content .badge .fs-4{width:100%;font-size:11px!important;font-weight:500!important;text-align:left;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.company-settings-v2 .dept_content .edit_delete{position:absolute;right:10px;top:50%;transform:translateY(-50%)}
.company-settings-v2 .dept_content .dept_delete{display:flex;width:24px;height:24px;align-items:center;justify-content:center;color:#64759b!important}
.company-settings-v2 .dept_content .dept_delete:hover{color:#e11d48!important}
.company-settings-v2 .dept_content .dept_delete .svg-icon{display:none!important}
.company-settings-v2 .dept_content .dept_delete::before{content:'×';font-size:17px;font-weight:400;line-height:1}
.csv2-tab-icon{display:inline-flex;width:16px;height:16px;margin-right:6px;vertical-align:-3px;color:#1764ef}
.csv2-tab-icon svg{width:16px;height:16px;fill:none;stroke:currentColor;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round}
.nav-link[href="#settings"] .nav-text{font-family:'Poppins',sans-serif!important;font-weight:600!important}
.csv2-account-tabs-header{min-height:0!important;padding:4px 0!important;border-bottom:1px solid #e7edf5!important}
.csv2-account-tabs-wrap{width:100%;min-width:0;overflow:visible}
.csv2-account-tabs{display:flex!important;flex-wrap:nowrap!important;align-items:stretch;width:100%;min-width:0;margin:0!important;padding:0!important;gap:0;overflow:visible;font-family:'Poppins',sans-serif!important}
.csv2-account-tabs::-webkit-scrollbar{display:none}
.csv2-account-tabs .nav-item{display:flex;flex:1 1 0;min-width:0;margin:0!important}
.csv2-account-tabs .csv2-account-tab{position:relative;display:flex!important;width:100%;min-width:0;align-items:center;justify-content:center;gap:5px;min-height:58px;margin:0!important;padding:11px 6px!important;color:#64748b!important;font-family:'Poppins',sans-serif!important;border:0!important;border-right:1px solid #e9eef5!important;border-radius:0!important;background:transparent!important;white-space:nowrap;transition:color .15s,background-color .15s}
.csv2-account-tabs .nav-item:last-child .csv2-account-tab{border-right:0!important}
.csv2-account-tabs .csv2-account-tab::after{content:'';position:absolute;right:8px;bottom:0;left:8px;height:2px;background:transparent;border-radius:2px 2px 0 0}
.csv2-account-tabs .csv2-account-tab:hover,.csv2-account-tabs .csv2-account-tab:focus{color:#2456ad!important;background:#f8fafc!important;outline:0}
.csv2-account-tabs .csv2-account-tab.active{color:#2563eb!important;background:rgba(37,99,235,.10)!important}
.csv2-account-tabs .csv2-account-tab.active::after{background:#2563eb}
.csv2-account-tabs .csv2-account-tab-icon{display:flex;width:14px;height:14px;flex:0 0 14px;align-items:center;justify-content:center;color:currentColor}
.csv2-account-tabs .csv2-account-tab-icon svg{width:14px;height:14px;fill:none;stroke:currentColor;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round}
.csv2-account-tabs .csv2-account-tab-copy{display:flex;flex-direction:column;justify-content:center;min-width:0;overflow:hidden;line-height:1.25}
.csv2-account-tabs .csv2-account-tab-title{display:block;max-width:100%;overflow:hidden;color:currentColor;font-size:11px;font-weight:500;line-height:1.15;text-overflow:ellipsis}
.csv2-account-tabs .csv2-account-tab-sub{display:block;max-width:100%;margin-top:3px;overflow:hidden;color:#7b89aa;font-size:9px;font-weight:500;line-height:1.15;text-align:left;text-overflow:ellipsis}
.csv2-account-tabs .csv2-account-tab.active .csv2-account-tab-title{font-weight:600}
.csv2-account-tabs .csv2-account-tab.active .csv2-account-tab-sub{color:#5578c3}
@media(min-width:1440px){.csv2-account-tabs .csv2-account-tab{gap:6px;padding-right:8px!important;padding-left:8px!important}.csv2-account-tabs .csv2-account-tab-icon{width:15px;height:15px;flex-basis:15px}.csv2-account-tabs .csv2-account-tab-icon svg{width:15px;height:15px}.csv2-account-tabs .csv2-account-tab-title{font-size:11.5px}.csv2-account-tabs .csv2-account-tab-sub{font-size:9.5px}}
@media(max-width:900px){.csv2-account-tabs-wrap{overflow-x:auto;scrollbar-width:none}.csv2-account-tabs{width:max-content;min-width:100%;overflow:visible}.csv2-account-tabs .nav-item{flex:0 0 auto}.csv2-account-tabs .csv2-account-tab{width:auto;min-width:112px;padding-right:10px!important;padding-left:10px!important}.csv2-account-tabs .csv2-account-tab-title{font-size:11px}.csv2-account-tabs .csv2-account-tab-sub{font-size:9px}}
.company-settings-v2 .card-footer{display:flex!important;align-items:center;margin:0 12px 12px;padding:13px 16px!important;background:#fbfcff!important;border:1px solid #edf0f6!important;border-radius:7px;justify-content:flex-end!important}
.company-settings-v2 .card-footer .btn{min-width:130px;padding:9px 14px;font-size:11px;font-weight:600;border-radius:5px}
.company-settings-v2 .card-footer .btn-primary{color:#fff;background:#1457e8;border-color:#1457e8}
.company-settings-v2 .csv2-save-spinner{display:none;width:15px;height:15px;margin-right:7px;vertical-align:-3px;border:2px solid rgba(255,255,255,.42);border-top-color:#fff;border-radius:50%;animation:csv2-save-spin .7s linear infinite}
.company-settings-v2 #updateCompanyProfile.is-loading .csv2-save-spinner{display:inline-block}
.company-settings-v2 #updateCompanyProfile:disabled{cursor:not-allowed;opacity:.78}
@keyframes csv2-save-spin{to{transform:rotate(360deg)}}
@media(prefers-reduced-motion:reduce){.company-settings-v2 .csv2-save-spinner{animation-duration:1.4s}}
@media(max-width:1199px){.company-settings-v2 #setting_one>.card-body{grid-template-columns:repeat(2,minmax(0,1fr))}.company-settings-v2 .dept_content{grid-template-columns:repeat(3,minmax(0,1fr))}}
@media(max-width:767px){.company-settings-v2 #setting_one>.card-body,.company-settings-v2 #application_duplicate_settings .row{grid-template-columns:1fr}.company-settings-v2 .dept_content{grid-template-columns:repeat(2,minmax(0,1fr))}.company-settings-v2 .card-footer{flex-wrap:wrap;gap:8px}}

/* Notifications tab: uses the existing tenant email-routing backend in a dense two-column layout. */
.csn,.csn *{font-family:'Poppins',-apple-system,BlinkMacSystemFont,sans-serif!important;box-sizing:border-box}
.csn{color:#13224f;padding:0 2px 14px}
.csn-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px}
.csn-card{min-width:0;background:#fff;border:1px solid #d3dcea;border-radius:10px;box-shadow:0 2px 7px rgba(16,30,78,.055);overflow:hidden}
.csn-card-head{display:flex;align-items:center;gap:12px;padding:15px 16px 10px}
.csn-head-icon{display:flex;width:38px;height:38px;flex:0 0 38px;align-items:center;justify-content:center;border-radius:8px}
.csn-head-icon svg,.csn-item-icon svg,.csn-email-icon svg{width:18px;height:18px;fill:none;stroke:currentColor;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round}
.csn-purple{color:#6338ed;background:#f1ebff}.csn-green{color:#0d9c55;background:#e7f9ef}
.csn-card-title{color:#111f4d;font-size:12px;font-weight:600;line-height:1.35}
.csn-card-sub{margin-top:3px;color:#5c6d94;font-size:10px;font-weight:500;line-height:1.4}
.csn-card-body{padding:7px 16px 14px}
.csn-label{display:block;margin:0 0 6px;color:#172957;font-size:10.5px;font-weight:600}
.csn-email-wrap{position:relative;margin-bottom:12px}
.csn-email{width:100%;height:36px;padding:7px 38px 7px 11px;color:#2e426f;font-size:11px;font-weight:500;background:#fff;border:1px solid #ccd7e8;border-radius:6px;outline:0;transition:border-color .15s,box-shadow .15s}
.csn-email:focus{border-color:#7456ee;box-shadow:0 0 0 3px rgba(99,56,237,.08)}
.csn-email-icon{position:absolute;right:10px;top:50%;display:flex;width:18px;height:18px;align-items:center;justify-content:center;color:#5b6e99;transform:translateY(-50%);pointer-events:none}
.csn-category-label{margin:0 0 7px;color:#172957;font-size:10.5px;font-weight:600}
.csn-list{display:grid;gap:0;border:1px solid #e1e7f0;border-radius:8px;overflow:hidden}
.csn-item{display:grid;grid-template-columns:28px minmax(0,1fr) 14px;align-items:center;gap:9px;min-height:50px;padding:7px 10px;background:#fff;border-bottom:1px solid #e9edf4}
.csn-item:last-child{border-bottom:0}
.csn-item-icon{display:flex;width:27px;height:27px;align-items:center;justify-content:center;border-radius:6px}
.csn-item-icon svg{width:14px;height:14px}
.csn-item-title{color:#172957;font-size:10.5px;font-weight:600;line-height:1.3}
.csn-item-desc{margin-top:2px;color:#61719a;font-size:9px;font-weight:500;line-height:1.35}
.csn-item-arrow{color:#41577f;font-size:15px;font-weight:500;text-align:center}
.csn-sales .csn-item-title{color:#087343}
.csn-actions{display:flex;align-items:center;justify-content:flex-end;gap:10px;margin-top:14px;padding:12px 14px;background:#fff;border:1px solid #d3dcea;border-radius:9px;box-shadow:0 2px 7px rgba(16,30,78,.045)}
.csn-actions .btn{min-width:128px;padding:9px 14px;font-size:11px;font-weight:600;border-radius:5px}
.csn-actions .btn-primary{background:#6338ed;border-color:#6338ed}
.csn-save-spinner{display:none;width:15px;height:15px;margin-right:7px;vertical-align:-3px;border:2px solid rgba(255,255,255,.42);border-top-color:#fff;border-radius:50%;animation:csv2-save-spin .7s linear infinite}
#saveNotificationSettings.is-loading .csn-save-spinner{display:inline-block}
#saveNotificationSettings:disabled{cursor:not-allowed;opacity:.78}
@media(max-width:1100px){.csn-item{min-height:50px}.csn-item-desc{font-size:8.5px}}
@media(max-height:800px) and (min-width:901px){.csn-card-head{padding:11px 14px 7px}.csn-head-icon{width:34px;height:34px}.csn-card-body{padding:6px 14px 11px}.csn-email-wrap{margin-top:5px}.csn-email{height:34px}.csn-category-label{margin:9px 0 6px}.csn-item{min-height:43px;padding:5px 9px}.csn-item-icon{width:27px;height:27px}.csn-item-desc{font-size:8px;line-height:1.25}.csn-actions{margin-top:10px;padding:10px 12px}}
@media(max-width:900px){.csn-grid{grid-template-columns:1fr}.csn-actions{position:sticky;bottom:0;z-index:2}.csn-actions .btn{flex:1;min-width:0}}

/* ===== Company Dashboard redesign (scoped .cad) — Poppins, <=14px, Metronic-friendly ===== */
.cad, .cad * { font-family:'Poppins',-apple-system,BlinkMacSystemFont,sans-serif; box-sizing:border-box; }
.cad { font-size:13px; color:#1f2937; }
.cad-hero { display:grid; grid-template-columns:1.5fr 1.1fr 0.9fr; gap:16px; margin:0 8px 14px; }
.cad-coins { display:flex; flex-direction:column; gap:12px; }
.cad-coin { padding:13px 15px; display:flex; flex-direction:column; gap:8px; }
.cad-coin-top { display:flex; align-items:flex-start; gap:11px; }
.cad-coin-ic { width:38px; height:38px; flex:0 0 38px; border-radius:10px; display:inline-flex; align-items:center; justify-content:center; }
.cad-coin-ic svg { width:19px; height:19px; }
.cad-coin-ic.op { background:#eef2ff; color:#3b55e6; } .cad-coin-ic.sa { background:#e8fff3; color:#16a34a; }
.cad-coin-mid { flex:1; min-width:0; }
.cad-coin-lbl { font-size:11.5px; color:#64748b; font-weight:600; }
.cad-coin-num { font-size:14px; font-weight:800; color:#0f172a; line-height:1.2; }
.cad-coin-sub { font-size:10px; color:#94a3b8; }
.cad-coin-btn { font-size:11px; font-weight:600; color:#3b55e6; text-decoration:none; border:1px solid #e2e8f0; border-radius:7px; padding:4px 9px; white-space:nowrap; }
.cad-coin-btn:hover { background:#f1f5f9; color:#3b55e6; }
.cad-coin-foot { font-size:10.5px; color:#94a3b8; border-top:1px dashed #eef0f5; padding-top:7px; }
.cad-coin-foot b { color:#475569; font-weight:600; }
@media (max-width:1400px){ .cad-hero { grid-template-columns:1.4fr 1fr; } .cad-coins { grid-column:1 / -1; flex-direction:row; } .cad-coin { flex:1; } }
@media (max-width:768px){ .cad-coins { flex-direction:column; } }
.cad-card { background:#fff; border:1px solid #eef0f5; border-radius:14px; box-shadow:0 1px 3px rgba(20,30,60,.04); }
.cad-id { padding:18px 20px; display:flex; gap:16px; align-items:flex-start; }
.cad-logo { width:74px; height:74px; border-radius:16px; flex-shrink:0; overflow:hidden; background:linear-gradient(135deg,#eef2ff,#e7efff); display:flex; align-items:center; justify-content:center; font-size:14px; font-weight:700; color:#3b55e6; border:1px solid #e6ecf5; }
.cad-logo img { width:100%; height:100%; object-fit:cover; }
.cad-id-main { flex:1; min-width:0; }
.cad-name { display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
.cad-name h2 { font-size:14px; font-weight:700; color:#0f172a; margin:0; }
.cad-verif { width:16px; height:16px; color:#00a3ff; flex-shrink:0; }
.cad-emp-chip { display:inline-flex; align-items:center; gap:5px; background:#f1edff; color:#6741d9; font-size:10.5px; font-weight:600; padding:3px 9px; border-radius:20px; }
.cad-tag { font-size:12px; color:#64748b; margin-top:3px; }
.cad-meta { display:flex; flex-wrap:wrap; gap:8px 16px; margin-top:11px; }
.cad-meta a, .cad-meta span { display:inline-flex; align-items:center; gap:6px; font-size:11.5px; color:#475569; text-decoration:none; }
.cad-meta svg { width:14px; height:14px; color:#94a3b8; flex-shrink:0; }
.cad-meta a:hover { color:#3b55e6; }
.cad-actions { display:flex; gap:8px; flex-wrap:wrap; margin-top:14px; }
.cad-btn { display:inline-flex; align-items:center; justify-content:center; gap:7px; padding:8px 15px; border-radius:8px; font-size:12.5px; font-weight:600; cursor:pointer; border:1px solid #e2e8f0; background:#fff; color:#334155; text-decoration:none; transition:all .15s; }
.cad-btn svg { width:14px; height:14px; }
.cad-btn:hover { background:#f1f5f9; color:#334155; }
.cad-btn-primary { background:#3b55e6; color:#fff; border-color:#3b55e6; box-shadow:0 4px 12px rgba(59,85,230,.22); }
.cad-btn-primary:hover { background:#2f46c4; color:#fff; }
.cad-pc { padding:16px 20px; }
.cad-pc-h { font-size:13px; font-weight:700; color:#0f172a; margin-bottom:10px; }
.cad-pc-body { display:flex; gap:16px; align-items:center; }
.cad-donut { position:relative; width:104px; height:104px; flex-shrink:0; }
.cad-donut svg { transform:rotate(-90deg); }
.cad-donut-c { position:absolute; inset:0; display:flex; flex-direction:column; align-items:center; justify-content:center; }
.cad-donut-pct { font-size:14px; font-weight:800; color:#0f172a; line-height:1; }
.cad-donut-lbl { font-size:9.5px; color:#94a3b8; margin-top:2px; }
.cad-pc-list { flex:1; display:flex; flex-direction:column; gap:6px; }
.cad-pc-item { display:flex; align-items:center; justify-content:space-between; font-size:11.5px; color:#475569; }
.cad-pc-state { display:inline-flex; align-items:center; gap:4px; font-size:10.5px; font-weight:600; }
.cad-pc-state.done { color:#16a34a; }
.cad-pc-state.pend { color:#d97706; }
.cad-pc-state svg { width:13px; height:13px; }
.cad-pc-foot { margin-top:11px; padding-top:10px; border-top:1px dashed #eef0f5; font-size:11px; color:#94a3b8; }
.cad-stats { display:grid; grid-template-columns:repeat(4,1fr); gap:12px; margin:0 8px 14px; }
.cad-stat { background:#fff; border:1px solid #eef0f5; border-radius:12px; padding:13px 15px; display:flex; align-items:center; gap:12px; box-shadow:0 1px 3px rgba(20,30,60,.04); }
.cad-stat-ic { width:40px; height:40px; border-radius:11px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.cad-stat-ic svg { width:19px; height:19px; }
.cad-bl { background:#eef2ff; color:#3b55e6; } .cad-gr { background:#e8fff3; color:#16a34a; } .cad-am { background:#fff8e6; color:#d97706; } .cad-pu { background:#f5efff; color:#7c3aed; }
.cad-stat-lbl { font-size:11.5px; color:#64748b; }
.cad-stat-num { font-size:14px; font-weight:800; color:#0f172a; line-height:1.2; }
.cad-grid2 { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
.cad-sec { padding:18px 20px; }
.cad-sec-h { display:flex; align-items:center; gap:9px; font-size:14px; font-weight:700; color:#0f172a; margin-bottom:14px; }
.cad-sec-h .ci { width:30px; height:30px; border-radius:8px; display:flex; align-items:center; justify-content:center; }
.cad-sec-h .ci svg { width:16px; height:16px; }
.cad-desc { font-size:12.5px; color:#475569; line-height:1.6; }
.cad-row { display:flex; align-items:flex-start; justify-content:space-between; gap:12px; padding:9px 0; border-bottom:1px dashed #eef2f7; }
.cad-row:last-child { border-bottom:none; }
.cad-row .k { font-size:12px; color:#94a3b8; flex-shrink:0; }
.cad-row .v { font-size:12.5px; font-weight:600; color:#1f2937; text-align:right; word-break:break-word; }
.cad-row .v a { color:#3b55e6; text-decoration:none; }
.cad-tags { display:flex; flex-wrap:wrap; gap:6px; }
.cad-tagi { background:#eef2ff; color:#3b5bdb; font-size:10.5px; font-weight:600; padding:3px 10px; border-radius:6px; }
.cad-grid3 { display:grid; grid-template-columns:repeat(3,1fr); gap:16px; }
.cad-chip { font-size:10px; font-weight:700; padding:2px 9px; border-radius:20px; display:inline-flex; align-items:center; gap:4px; white-space:nowrap; }
.cad-chip svg { width:11px; height:11px; }
.cad-chip.ok { background:#e8fff3; color:#16a34a; }
.cad-chip.warn { background:#fff8e6; color:#d97706; }
.cad-chip.off { background:#fff1f3; color:#e11d48; }
.cad-mini { border:1px solid #eef0f5; border-radius:11px; padding:12px 13px; margin-bottom:10px; }
.cad-mini:last-child { margin-bottom:0; }
.cad-mini-h { display:flex; align-items:center; justify-content:space-between; gap:8px; font-size:12px; font-weight:700; color:#0f172a; }
.cad-mini-v { font-size:14px; font-weight:800; color:#0f172a; margin-top:6px; }
.cad-mini-s { font-size:10.5px; color:#94a3b8; margin-top:2px; }
.cad-team { display:flex; flex-wrap:wrap; gap:6px; margin-top:12px; }
.cad-team-av { width:30px; height:30px; border-radius:50%; overflow:hidden; background:#eef2ff; color:#3b55e6; display:inline-flex; align-items:center; justify-content:center; font-size:11px; font-weight:700; border:1px solid #e6ecf5; }
.cad-team-av img { width:100%; height:100%; object-fit:cover; }
.cad-adm { display:flex; gap:11px; align-items:flex-start; padding:9px 0; border-bottom:1px dashed #eef2f7; }
.cad-adm:last-child { border-bottom:none; }
.cad-adm-av { width:34px; height:34px; flex:0 0 34px; border-radius:50%; overflow:hidden; background:#eef2ff; color:#3b55e6; display:inline-flex; align-items:center; justify-content:center; font-size:12px; font-weight:700; border:1px solid #e6ecf5; }
.cad-adm-av img { width:100%; height:100%; object-fit:cover; }
.cad-adm-mid { min-width:0; }
.cad-adm-name { font-size:12.5px; font-weight:700; color:#0f172a; display:flex; align-items:center; gap:6px; flex-wrap:wrap; }
.cad-adm-pri { font-style:normal; font-size:9px; font-weight:700; color:#3b55e6; background:#e6ebff; padding:1px 6px; border-radius:10px; }
.cad-adm-role { font-size:11px; color:#64748b; margin-top:1px; }
.cad-adm-c { font-size:10.5px; color:#94a3b8; margin-top:2px; word-break:break-word; }
@media (max-width:1100px){ .cad-hero { grid-template-columns:1fr; } .cad-stats { grid-template-columns:repeat(2,1fr); } .cad-grid2 { grid-template-columns:1fr; } .cad-grid3 { grid-template-columns:1fr 1fr; } }
@media (max-width:768px){ .cad-stats { grid-template-columns:1fr 1fr; } .cad-grid3 { grid-template-columns:1fr; } }

/* ===== Interview Pipeline Builder (scoped in components/tab-interview-stages.blade.php) ===== */

/* ===== Agency Terms & Conditions template (scoped .atc) ===== */
.atc, .atc * { font-family:'Poppins',-apple-system,BlinkMacSystemFont,sans-serif; }
.atc-head { display:flex; align-items:flex-start; justify-content:space-between; gap:16px; flex-wrap:wrap; margin:4px 2px 16px; }
.atc-title { font-size:14px; font-weight:700; color:#0f172a; }
.atc-sub { font-size:12px; color:#94a3b8; margin-top:4px; max-width:620px; line-height:1.5; }
.atc-sub b { color:#3b55e6; font-weight:600; }
.atc-head-actions { display:flex; gap:8px; flex-wrap:wrap; }
.atc-btn { display:inline-flex; align-items:center; gap:6px; padding:8px 15px; border-radius:8px; font-size:12.5px; font-weight:600; cursor:pointer; border:1px solid #e2e8f0; background:#fff; color:#334155; text-decoration:none; transition:all .15s; }
.atc-btn:hover { background:#f1f5f9; color:#334155; }
.atc-btn svg { width:14px; height:14px; }
.atc-btn-primary { background:#3b55e6; color:#fff; border-color:#3b55e6; box-shadow:0 4px 12px rgba(59,85,230,.22); }
.atc-btn-primary:hover { background:#2f46c4; color:#fff; }
.atc-btn-danger { background:#fff; color:#e11d48; border-color:#fadce2; }
.atc-btn-danger:hover { background:#fff1f3; color:#e11d48; }
.atc-card { background:#fff; border:1px solid #eef0f5; border-radius:14px; box-shadow:0 1px 3px rgba(20,30,60,.04); margin:0 2px 14px; }
.atc-meta { display:flex; align-items:center; flex-wrap:wrap; gap:18px 26px; padding:15px 18px; }
.atc-meta-id { display:flex; align-items:center; gap:12px; }
.atc-meta-ic { width:42px; height:42px; flex:0 0 42px; border-radius:11px; background:#f1edff; color:#6741d9; display:inline-flex; align-items:center; justify-content:center; }
.atc-meta-ic svg { width:21px; height:21px; }
.atc-meta-lbl { font-size:10.5px; color:#94a3b8; font-weight:600; text-transform:uppercase; letter-spacing:.3px; }
.atc-meta-v { font-size:12.5px; font-weight:700; color:#0f172a; margin-top:2px; }
.atc-status { display:inline-flex; align-items:center; gap:6px; font-size:12px; font-weight:700; color:#16a34a; }
.atc-status .dot { width:7px; height:7px; border-radius:50%; background:#16a34a; }
.atc-meta .atc-btn { margin-left:auto; }
.atc-sec { padding:16px 18px; }
.atc-sec-h { display:flex; align-items:flex-start; justify-content:space-between; gap:12px; margin-bottom:13px; }
.atc-sec-t { font-size:13px; font-weight:700; color:#0f172a; }
.atc-sec-s { font-size:11px; color:#94a3b8; margin-top:2px; }
.atc-vars { display:grid; grid-template-columns:repeat(5,1fr); gap:10px; }
.atc-var { border:1px solid #eef0f5; border-radius:10px; padding:10px 11px; }
.atc-var code { font-size:11px; font-weight:700; color:#6741d9; background:#f1edff; padding:2px 7px; border-radius:6px; display:inline-block; }
.atc-var-s { font-size:11px; color:#64748b; margin-top:6px; }
.atc-notice { display:flex; gap:11px; align-items:flex-start; background:#fff8eb; border:1px solid #fdebc8; border-radius:12px; padding:13px 16px; margin:0 2px 14px; }
.atc-notice-ic { width:26px; height:26px; flex:0 0 26px; border-radius:50%; background:#fdebc8; color:#d97706; display:inline-flex; align-items:center; justify-content:center; font-size:13px; font-weight:800; font-style:italic; }
.atc-notice-t { font-size:12.5px; font-weight:700; color:#b45309; }
.atc-notice-s { font-size:11.5px; color:#92723a; line-height:1.5; margin-top:3px; }
.atc-editor { border:1px solid #eef0f5; border-radius:11px; padding:16px 18px; max-height:520px; overflow:auto; background:#fff; }
.atc-editor, .atc-editor * { font-size:13px; line-height:1.6; color:#334155; }
.atc-editor h1,.atc-editor h2,.atc-editor h3 { font-size:14px !important; }
.agency-agreement,.agency-agreement *{box-sizing:border-box;font-family:'Poppins',sans-serif!important}.agency-agreement{color:#151b3b;font-size:12px}.aat-hero{display:flex;align-items:flex-start;justify-content:space-between;gap:18px;margin:0 2px 16px}.aat-title-wrap{display:flex;gap:14px;min-width:0}.aat-hero-icon,.aat-sec-icon,.aat-flow-icon{display:inline-flex;align-items:center;justify-content:center;color:#6c3ff2;background:#f1ecff;border-radius:10px;flex:0 0 auto}.aat-hero-icon{width:50px;height:50px}.aat-hero-icon svg{width:27px;height:27px}.aat-title{margin:0;color:#111733;font-size:20px!important;font-weight:600!important;line-height:1.25}.aat-sub{max-width:650px;margin:6px 0 0;color:#53617f;font-size:12px;line-height:1.55}.aat-actions{display:grid;grid-template-columns:1fr 1fr;gap:9px;min-width:245px}.aat-status-row{display:flex;align-items:center;gap:8px;color:#53617f;font-size:11px;grid-column:1/-1}.aat-pill{display:inline-flex;align-items:center;gap:6px;padding:5px 9px;border-radius:7px;font-size:11px;font-weight:600}.aat-pill.ok{color:#0c8f52;background:#e8f9ef}.aat-pill.v{color:#6941c6;background:#f0e9ff}.aat-card{margin:0 2px 16px;padding:18px;background:#fff;border:1px solid #e8edf5;border-radius:14px;box-shadow:0 2px 6px rgba(15,23,42,.035)}.aat-sec-head{display:flex;align-items:flex-start;justify-content:space-between;gap:12px;margin-bottom:16px}.aat-sec-title{display:flex;gap:12px;align-items:flex-start}.aat-sec-icon{width:34px;height:34px}.aat-sec-icon svg{width:18px;height:18px}.aat-sec-t{font-size:14px;font-weight:600;color:#111733}.aat-sec-s{margin-top:2px;color:#73809a;font-size:11px}.aat-btn{display:inline-flex;align-items:center;justify-content:center;gap:7px;min-height:34px;padding:8px 14px;border:1px solid #d8c9ff;border-radius:7px;background:#fff;color:#6941c6;font-size:11px;font-weight:600;text-decoration:none}.aat-btn.primary{background:linear-gradient(135deg,#7c4dff,#673ab7);border-color:#673ab7;color:#fff}.aat-btn svg{width:14px;height:14px;fill:none;stroke:currentColor;stroke-width:2}.aat-commercial-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px}.aat-commercial{padding:18px;border:1px solid #e5ebf3;border-radius:12px;background:#fff}.aat-commercial.purple{background:linear-gradient(135deg,#fff,#fbf8ff)}.aat-commercial.orange{background:linear-gradient(135deg,#fff,#fff9f1)}.aat-service-head{display:flex;align-items:center;justify-content:space-between;gap:10px;margin-bottom:14px}.aat-service-title{display:flex;align-items:center;gap:12px;color:#5f3bd5;font-size:13px;font-weight:600}.aat-service-title.orange{color:#d56a00}.aat-avatar{width:48px;height:48px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;background:#f1ecff;color:#6c3ff2}.aat-avatar.orange{background:#fff0de;color:#d56a00}.aat-avatar svg{width:23px;height:23px;fill:none;stroke:currentColor;stroke-width:2}.aat-tag{padding:6px 9px;border-radius:7px;background:#f1ecff;color:#6c3ff2;font-size:10px;font-weight:600}.aat-tag.orange{background:#fff0de;color:#d56a00}.aat-rows{display:grid;gap:0}.aat-row{display:grid;grid-template-columns:190px 1fr;gap:12px;align-items:center;min-height:36px;border-bottom:1px solid #edf1f6;color:#24304d;font-size:11.5px}.aat-row:last-child{border-bottom:0}.aat-row .label{display:flex;align-items:center;gap:9px;color:#53617f}.aat-row .label svg{width:14px;height:14px;color:#7c4dff;fill:none;stroke:currentColor;stroke-width:2}.aat-row .value{font-weight:600;color:#1b2440}.aat-note{margin-top:14px;padding:10px 12px;border:1px solid #ddd1ff;border-radius:7px;background:#fbf9ff;color:#5f3bd5;font-size:11px}.aat-note.orange{border-color:#ffd9a8;background:#fffaf2;color:#c45f00}.aat-two-col{display:grid;grid-template-columns:minmax(0,.95fr) minmax(0,1.35fr);gap:16px}.aat-rule-grid{display:grid;gap:10px}.aat-rule{display:grid;grid-template-columns:190px 1fr;gap:12px;font-size:11.5px}.aat-rule .k{color:#53617f}.aat-rule .v{font-weight:600;color:#1b2440}.aat-yes{color:#12935a}.aat-no{color:#dc3545}.aat-flow{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px;align-items:start;text-align:center}.aat-flow-step{position:relative}.aat-flow-step:not(:last-child):after{content:'→';position:absolute;right:-12px;top:27px;color:#a293d6;font-weight:600}.aat-flow-icon{width:56px;height:56px;margin:0 auto 10px;border:1px solid #dcd2ff;border-radius:50%;background:#fbf9ff}.aat-flow-icon svg{width:24px;height:24px;fill:none;stroke:currentColor;stroke-width:1.9}.aat-flow-n{font-size:13px;font-weight:700;color:#111733}.aat-flow-t{margin-top:5px;color:#111733;font-size:11.5px;font-weight:600}.aat-flow-s{margin-top:3px;color:#65718d;font-size:10.5px;line-height:1.45}.aat-vars{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:12px}.aat-var{padding:12px;border:1px solid #e8edf5;border-radius:9px;background:#fff}.aat-var code{display:block;color:#6941c6;font-size:10.5px;font-weight:700;background:transparent}.aat-var span{display:block;margin-top:5px;color:#65718d;font-size:10.5px}.aat-editor-card .atc-editor{border-radius:0 0 10px 10px;max-height:410px}.aat-editor-toolbar{display:flex;align-items:center;justify-content:space-between;gap:12px;margin:-4px 0 12px}.aat-important{display:flex;gap:10px;margin:0 2px 18px;padding:14px 16px;border:1px solid #ddd1ff;border-radius:10px;background:#fbf9ff;color:#4d3a91;font-size:11.5px;line-height:1.5}.aat-form{display:none;margin-top:12px;padding:14px;border:1px dashed #d8c9ff;border-radius:10px;background:#fbf9ff}.aat-form.open{display:block}.aat-form-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px}.aat-form label{display:block;color:#34405f;font-size:10.5px;font-weight:600}.aat-form input,.aat-form select{width:100%;height:34px;margin-top:5px;padding:6px 9px;border:1px solid #dbe3ee;border-radius:7px;background:#fff;color:#17203a;font-size:11px}
.aat-drawer-overlay{position:fixed;top:0;right:0;bottom:0;left:220px;background:rgba(15,23,42,.34);opacity:0;visibility:hidden;z-index:1060;transition:opacity .18s ease}.aat-drawer-overlay.show{opacity:1;visibility:visible}.aat-drawer{position:fixed;top:0;right:0;bottom:0;width:min(1260px,calc((100vw - 220px)*.9));max-width:calc(100vw - 220px);background:#f8fafc;box-shadow:-18px 0 42px rgba(15,23,42,.18);z-index:1061;transform:translateX(105%);transition:transform .22s ease;overflow:auto;font-family:'Poppins',sans-serif}.aat-drawer.open{transform:translateX(0)}.aat-drawer-head{position:sticky;top:0;z-index:2;display:flex;align-items:center;justify-content:space-between;gap:16px;padding:18px 22px;background:#fff;border-bottom:1px solid #e6ebf3}.aat-drawer-title{display:flex;gap:14px;align-items:center}.aat-drawer-title h3{margin:0;color:#111733;font-size:20px!important;font-weight:600!important}.aat-drawer-title p{margin:5px 0 0;color:#53617f;font-size:12px}.aat-drawer-close{width:34px;height:34px;border:1px solid #dce3ee;border-radius:8px;background:#fff;color:#64748b;font-size:22px;line-height:1}.aat-drawer-meta{display:flex;gap:8px;flex-wrap:wrap;justify-content:flex-end;color:#53617f;font-size:11px}.aat-drawer-body{padding:18px 22px 86px}.aat-drawer-tabs{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:16px;padding:0 6px;border-bottom:1px solid #e6ebf3;background:#fff;border-radius:12px 12px 0 0}.aat-drawer-tab{min-height:52px;padding:0 18px;border:0;border-bottom:3px solid transparent;background:transparent;color:#31405f;font-size:13px;font-weight:600}.aat-drawer-tab.active{color:#6938ef;border-color:#6938ef}.aat-edit-alert{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:16px;padding:13px 16px;border:1px solid #b9c7ff;border-radius:8px;background:#f6f8ff;color:#53617f;font-size:12px}.aat-edit-section{margin-bottom:16px;border:1px solid #e5ebf3;border-radius:12px;background:#fff;overflow:hidden}.aat-edit-section-head{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:18px;border-bottom:1px solid #eef2f7}.aat-edit-section-title{display:flex;align-items:center;gap:12px}.aat-edit-section-title h4{margin:0;color:#111733;font-size:16px!important;font-weight:600!important}.aat-edit-section-title p{margin:3px 0 0;color:#697797;font-size:11px}.aat-edit-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:18px 28px;padding:18px}.aat-field label{display:flex;align-items:center;gap:6px;margin:0 0 8px;color:#1f2a44;font-size:11px;font-weight:600}.aat-field label .req{color:#f04438}.aat-control,.aat-field select,.aat-field input{width:100%;height:42px;padding:9px 12px;border:1px solid #dce3ee;border-radius:7px;background:#fff;color:#24304d;font-size:12px;font-weight:500}.aat-input-group{display:grid;grid-template-columns:82px minmax(0,1fr);align-items:center}.aat-input-group select,.aat-input-group input{border-radius:0}.aat-input-group select:first-child,.aat-input-group input:first-child{border-radius:7px 0 0 7px}.aat-input-group select:last-child,.aat-input-group input:last-child{border-radius:0 7px 7px 0;border-left:0}.aat-input-suffix{display:grid;grid-template-columns:minmax(0,1fr) 62px}.aat-input-suffix input{border-radius:7px 0 0 7px}.aat-input-suffix span{display:flex;align-items:center;justify-content:center;border:1px solid #dce3ee;border-left:0;border-radius:0 7px 7px 0;color:#53617f;background:#fff;font-size:12px}.aat-checks{display:flex;align-items:center;gap:10px;flex-wrap:wrap;min-height:42px}.aat-checks label{display:inline-flex;align-items:center;gap:6px;margin:0;padding:8px 9px;border:1px solid #e5ebf3;border-radius:7px;color:#5138b8;background:#fff;font-size:11px}.aat-checks input{width:14px;height:14px;accent-color:#6d3df2}.aat-calc{grid-column:1/-1;margin-top:4px;padding:14px;border:1px solid #e3d7ff;border-radius:8px;background:#fbf8ff}.aat-calc.orange{border-color:#ffd59c;background:#fffaf2}.aat-calc h5{margin:0 0 12px;color:#6d3df2;font-size:13px;font-weight:600}.aat-calc.orange h5{color:#e16b00}.aat-calc-row{display:grid;grid-template-columns:1fr 28px 1fr 28px 1fr;align-items:center;gap:10px;max-width:980px;margin:0 auto;padding:12px;border:1px solid #eee7ff;border-radius:8px;background:#fff}.aat-calc-cell{color:#53617f;font-size:11px}.aat-calc-cell b{display:block;margin-top:4px;color:#111733;font-size:14px}.aat-general-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:18px;padding:18px}.aat-drawer-footer{position:sticky;bottom:0;z-index:3;display:flex;align-items:center;justify-content:space-between;gap:12px;padding:14px 22px;background:#fff;border-top:1px solid #e5ebf3}.aat-footer-actions{display:flex;gap:10px}.aat-btn.ghost{border-color:#dce3ee;color:#34405f}.aat-btn.save{min-width:150px}
.aga-drawer-overlay{position:fixed;top:0;right:0;bottom:0;left:220px;background:rgba(15,23,42,.35);opacity:0;visibility:hidden;z-index:1070;transition:opacity .18s ease}.aga-drawer-overlay.show{opacity:1;visibility:visible}.aga-drawer{position:fixed;top:0;right:0;bottom:0;width:min(1380px,calc((100vw - 220px)*.9));max-width:calc(100vw - 220px);background:#f8fafc;box-shadow:-18px 0 46px rgba(15,23,42,.2);z-index:1071;transform:translateX(105%);transition:transform .22s ease;overflow:auto;font-family:'Poppins',sans-serif;color:#101833}.aga-drawer.open{transform:translateX(0)}.aga-head{position:sticky;top:0;z-index:4;display:flex;align-items:center;justify-content:space-between;gap:16px;padding:18px 22px;background:#fff;border-bottom:1px solid #e6ebf3}.aga-title-wrap{display:flex;align-items:center;gap:14px}.aga-title-wrap h3{margin:0;font-size:20px!important;font-weight:600!important;color:#101833}.aga-title-wrap p{margin:5px 0 0;color:#53617f;font-size:12px}.aga-head-actions{display:flex;align-items:center;gap:9px;flex-wrap:wrap;justify-content:flex-end}.aga-meta{display:grid;gap:4px;margin-right:12px;color:#52607b;font-size:11px}.aga-info{display:flex;align-items:center;justify-content:space-between;gap:12px;margin:14px 22px 0;padding:12px 14px;border:1px solid #d8d3ff;border-radius:8px;background:#fbfaff;color:#4d35b0;font-size:12px}.aga-shell{display:grid;grid-template-columns:230px minmax(0,1fr) 245px;gap:14px;padding:14px 22px 22px}.aga-side,.aga-main,.aga-right{background:#fff;border:1px solid #e5ebf3;border-radius:12px}.aga-side{padding:14px}.aga-side h4,.aga-right h4{margin:0 0 12px;font-size:13px!important;font-weight:600!important;color:#101833}.aga-search{width:100%;height:35px;margin-bottom:10px;padding:8px 10px;border:1px solid #dce3ee;border-radius:7px;color:#53617f;font-size:11px}.aga-outline{display:grid;gap:2px;margin:0;padding:0;list-style:none}.aga-outline li{display:flex;align-items:center;justify-content:space-between;gap:8px;padding:8px 7px;border-radius:7px;color:#101833;font-size:11px;font-weight:600}.aga-outline li.active{background:#f1ecff;color:#6338e8;border-left:3px solid #6338e8}.aga-sublist{margin:3px 0 9px 14px;padding:0;list-style:none;color:#53617f;font-size:10.5px}.aga-sublist li{padding:4px 0}.aga-add-section{width:100%;margin:12px 0;padding:9px;border:1px solid #d8c9ff;border-radius:7px;background:#fff;color:#6338e8;font-size:11px;font-weight:600}.aga-settings{margin-top:12px;padding-top:12px;border-top:1px solid #edf1f6}.aga-settings label{display:block;margin:9px 0 5px;color:#53617f;font-size:10.5px}.aga-settings select{width:100%;height:34px;border:1px solid #dce3ee;border-radius:7px;background:#fff;color:#24304d;font-size:11px}.aga-main{overflow:hidden}.aga-tabs{display:flex;align-items:center;justify-content:space-between;padding:0 14px;border-bottom:1px solid #eef2f7}.aga-tabs-left{display:flex;gap:18px}.aga-tab{display:inline-flex;align-items:center;gap:7px;min-height:48px;border:0;border-bottom:3px solid transparent;background:transparent;color:#31405f;font-size:12px;font-weight:600}.aga-tab.active{color:#6338e8;border-color:#6338e8}.aga-tools{display:flex;gap:8px;flex-wrap:wrap}.aga-toolbar{display:flex;align-items:center;gap:7px;padding:12px 14px;border-bottom:1px solid #eef2f7;background:#fff}.aga-toolbar select,.aga-toolbar button{height:34px;border:1px solid #dce3ee;border-radius:6px;background:#fff;color:#24304d;font-size:11px}.aga-toolbar select{padding:0 10px}.aga-toolbar button{min-width:34px;font-weight:700}.aga-editor-wrap{padding:12px 14px}.aga-editor-card{border:1px solid #e5ebf3;border-radius:10px;overflow:hidden;background:#fff}.aga-editor-card textarea{min-height:560px}.aga-editor-card .cke{border:0!important}.aga-editor-card .cke_contents{min-height:560px!important}.aga-editor-foot{display:flex;align-items:center;justify-content:space-between;padding:10px 14px;border-top:1px solid #eef2f7;color:#53617f;font-size:10.5px}.aga-right{padding:14px}.aga-var-tabs{display:grid;grid-template-columns:1fr 1fr;margin-bottom:10px;border-bottom:1px solid #e5ebf3}.aga-var-tabs span{padding:8px;text-align:center;color:#53617f;font-size:11px;font-weight:600}.aga-var-tabs .active{color:#6338e8;border-bottom:3px solid #6338e8}.aga-var-list{display:grid;gap:7px;max-height:285px;overflow:auto;padding-right:4px}.aga-var-item{display:flex;align-items:center;justify-content:space-between;gap:8px}.aga-var-item code{padding:4px 7px;border:1px solid #ded6ff;border-radius:5px;background:#fbf9ff;color:#6338e8;font-size:10px}.aga-var-item span{color:#53617f;font-size:10px}.aga-card{margin-top:12px;padding:13px;border:1px solid #e5ebf3;border-radius:10px;background:#fff}.aga-meta-list{display:grid;gap:9px}.aga-meta-row{display:flex;justify-content:space-between;gap:10px;color:#53617f;font-size:11px}.aga-meta-row b{color:#17203a;font-weight:600}.aga-tips{border-color:#ffd9a8;background:#fffaf2;color:#9a4f00}.aga-tips ul{margin:0;padding-left:18px}.aga-tips li{margin:6px 0;font-size:11px}.aga-close{width:34px;height:34px;border:1px solid #dce3ee;border-radius:8px;background:#fff;color:#64748b;font-size:22px;line-height:1}
.avd-overlay{position:fixed;top:0;right:0;bottom:0;left:220px;background:rgba(15,23,42,.34);opacity:0;visibility:hidden;z-index:1080;transition:opacity .18s ease}.avd-overlay.show{opacity:1;visibility:visible}.avd-drawer{position:fixed;top:0;right:0;bottom:0;width:min(1380px,calc((100vw - 220px)*.9));max-width:calc(100vw - 220px);background:#f8fafc;box-shadow:-18px 0 46px rgba(15,23,42,.2);z-index:1081;transform:translateX(105%);transition:transform .22s ease;overflow:auto;font-family:'Poppins',sans-serif;color:#101833}.avd-drawer.open{transform:translateX(0)}.avd-head{display:flex;align-items:center;justify-content:space-between;gap:18px;padding:20px 22px;background:#fff;border-bottom:1px solid #e6ebf3}.avd-title{display:flex;align-items:center;gap:14px}.avd-title h3{margin:0;color:#101833;font-size:22px!important;font-weight:600!important}.avd-title p{margin:6px 0 0;color:#53617f;font-size:12px;line-height:1.5}.avd-actions{display:flex;gap:10px;align-items:center;flex-wrap:wrap;margin-left:auto}.avd-body{display:grid;grid-template-columns:235px minmax(0,1fr);gap:14px;padding:14px 22px 22px}.avd-card{background:#fff;border:1px solid #e5ebf3;border-radius:12px;box-shadow:0 2px 6px rgba(15,23,42,.025)}.avd-pad{padding:16px}.avd-card h4{margin:0 0 14px;color:#101833;font-size:14px!important;font-weight:600!important}.avd-cat{display:grid;gap:5px}.avd-cat button{display:flex;align-items:center;justify-content:space-between;gap:8px;width:100%;min-height:40px;padding:9px 10px;border:0;border-radius:8px;background:transparent;color:#20304f;font-size:12px;font-weight:500;text-align:left}.avd-cat button.active{background:#f2ecff;color:#6338e8;font-weight:600}.avd-cat span{display:inline-flex;align-items:center;gap:8px}.avd-help{display:grid;gap:16px;margin-top:14px;padding:16px;background:linear-gradient(180deg,#fff,#fbfaff)}.avd-help-item{display:flex;gap:10px;color:#53617f;font-size:11px;line-height:1.45}.avd-help-item b{display:block;color:#101833;font-size:12px}.avd-filter{display:grid;grid-template-columns:minmax(0,1fr) 160px 145px 190px;gap:12px;padding:14px;border-bottom:1px solid #edf1f6}.avd-input,.avd-select{height:38px;border:1px solid #dce3ee;border-radius:7px;background:#fff;color:#34405f;font-size:11px;padding:8px 10px}.avd-check{display:flex;align-items:center;gap:8px;color:#34405f;font-size:11px}.avd-table{width:100%;border-collapse:collapse}.avd-table th{padding:11px 12px;background:#f8fafc;border-bottom:1px solid #e5ebf3;color:#52617e;font-size:10px;font-weight:700;text-align:left}.avd-table td{padding:11px 12px;border-bottom:1px solid #edf1f6;color:#17203a;font-size:11.5px;vertical-align:middle}.avd-code{display:inline-flex;padding:4px 7px;border:1px solid #ded6ff;border-radius:5px;background:#fbf9ff;color:#6338e8;font-size:10px;font-weight:700}.avd-badge{display:inline-flex;padding:4px 8px;border-radius:6px;font-size:10px;font-weight:600}.avd-badge.agency{background:#f1ecff;color:#6338e8}.avd-badge.company{background:#eaf3ff;color:#0b6ff0}.avd-badge.job{background:#fff0de;color:#c75d00}.avd-badge.commercial{background:#e7f8f1;color:#07895c}.avd-badge.system{background:#fff7d6;color:#a36c00}.avd-status{display:inline-flex;align-items:center;gap:5px;padding:4px 8px;border-radius:6px;background:#e9f8f1;color:#068752;font-size:10px;font-weight:600}.avd-status:before{content:'';width:5px;height:5px;border-radius:50%;background:#09a76f}.avd-row-actions{display:flex;gap:9px;color:#34405f}.avd-row-actions button{border:0;background:transparent;color:#34405f;font-size:15px}.avd-foot{display:flex;align-items:center;justify-content:space-between;padding:12px 14px;color:#52617e;font-size:11px}.avd-pages{display:flex;gap:6px}.avd-pages span{display:inline-flex;align-items:center;justify-content:center;min-width:28px;height:28px;border:1px solid #e5ebf3;border-radius:6px;background:#fff;color:#34405f}.avd-pages .active{background:#6338e8;color:#fff}.avd-info{display:flex;align-items:flex-start;justify-content:space-between;gap:12px;margin:4px 22px 22px;padding:14px 16px;border:1px solid #ddd1ff;border-radius:10px;background:#fbf9ff;color:#4d3a91;font-size:11.5px;line-height:1.5}.avd-empty{display:none;padding:24px;text-align:center;color:#53617f;font-size:12px}
.apv-overlay{position:fixed;top:0;right:0;bottom:0;left:220px;background:rgba(15,23,42,.34);opacity:0;visibility:hidden;z-index:1090;transition:opacity .18s ease}.apv-overlay.show{opacity:1;visibility:visible}.apv-drawer{position:fixed;top:0;right:0;bottom:0;width:min(1380px,calc((100vw - 220px)*.9));max-width:calc(100vw - 220px);background:#f8fafc;box-shadow:-18px 0 46px rgba(15,23,42,.2);z-index:1091;transform:translateX(105%);transition:transform .22s ease;overflow:auto;font-family:'Poppins',sans-serif;color:#101833}.apv-drawer.open{transform:translateX(0)}.apv-head{display:flex;align-items:center;justify-content:space-between;gap:18px;padding:20px 22px;background:#fff;border-bottom:1px solid #e6ebf3}.apv-title{display:flex;align-items:center;gap:14px}.apv-title h3{margin:0;color:#101833;font-size:22px!important;font-weight:600!important}.apv-title p{margin:6px 0 0;color:#53617f;font-size:12px;line-height:1.5}.apv-meta{display:grid;gap:6px;margin-left:auto;color:#53617f;font-size:11px}.apv-actions{display:flex;align-items:center;gap:10px;flex-wrap:wrap}.apv-body{display:grid;grid-template-columns:270px minmax(0,1fr) 270px;gap:14px;padding:14px 22px 22px}.apv-card{background:#fff;border:1px solid #e5ebf3;border-radius:12px;box-shadow:0 2px 6px rgba(15,23,42,.025)}.apv-pad{padding:16px}.apv-card h4{margin:0 0 12px;color:#101833;font-size:14px!important;font-weight:600!important}.apv-card p{color:#53617f;font-size:11px;line-height:1.45}.apv-select{width:100%;height:38px;border:1px solid #dce3ee;border-radius:7px;background:#fff;color:#24304d;font-size:11px;padding:8px 10px}.apv-detail{display:grid;gap:14px}.apv-detail-row{display:flex;gap:10px;color:#101833;font-size:11.5px;line-height:1.45}.apv-detail-row span:first-child{width:24px;height:24px;border-radius:7px;background:#f2ecff;color:#6338e8;display:inline-flex;align-items:center;justify-content:center;flex:0 0 auto}.apv-detail-row b{display:block;margin-bottom:3px;color:#53617f;font-size:10.5px;font-weight:500}.apv-other{margin-top:16px;padding-top:14px;border-top:1px solid #edf1f6}.apv-other-row{display:flex;justify-content:space-between;gap:12px;margin:9px 0;color:#53617f;font-size:11px}.apv-other-row b{color:#101833;font-weight:600}.apv-main{overflow:hidden}.apv-toolbar{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:0 16px;border-bottom:1px solid #eef2f7}.apv-tabs{display:flex;gap:18px}.apv-tab{min-height:50px;border:0;border-bottom:3px solid transparent;background:transparent;color:#31405f;font-size:12px;font-weight:600}.apv-tab.active{color:#6338e8;border-color:#6338e8}.apv-tools{display:flex;gap:9px;align-items:center}.apv-alert{margin:16px;padding:11px 13px;border:1px solid #bed0ff;border-radius:7px;background:#f6f9ff;color:#3155c9;font-size:11.5px}.apv-document{margin:0 16px 16px;padding:28px 46px;max-height:610px;overflow:auto;border:1px solid #dfe6f1;border-radius:8px;background:#fff;color:#111733;font-size:13px;line-height:1.75}.apv-document h1,.apv-document h2,.apv-document h3{text-align:center;color:#111733;font-size:18px!important;font-weight:700!important}.apv-document .apv-party-grid{display:grid;grid-template-columns:1fr 80px 1fr;gap:18px;align-items:center;text-align:center;margin:24px 0}.apv-document .apv-party-name{color:#4f2bd5;font-size:15px;font-weight:700}.apv-document hr{border:0;border-top:1px solid #dfe6f1;margin:24px 0}.apv-summary-list{display:grid;gap:13px}.apv-summary-row{display:flex;justify-content:space-between;gap:12px;color:#53617f;font-size:11px}.apv-summary-row b{color:#101833}.apv-commercial{margin-top:14px;padding:15px;border-radius:10px;background:#fbfaff}.apv-commercial.orange{background:#fffaf2}.apv-commercial h5{margin:0 0 12px;color:#6338e8;font-size:13px;font-weight:600}.apv-commercial.orange h5{color:#d56a00}.apv-readonly{margin-top:14px;padding:14px;border:1px solid #ddd1ff;border-radius:10px;background:#fbf9ff;color:#4d3a91;font-size:11.5px;line-height:1.5}.apv-info{display:flex;align-items:center;justify-content:space-between;gap:12px;margin:0 22px 22px;padding:14px 16px;border:1px solid #ddd1ff;border-radius:10px;background:#fbf9ff;color:#4d3a91;font-size:11.5px}.apv-plain{display:none;white-space:pre-wrap;font-family:'Poppins',sans-serif}
@media (max-width:1200px){ .atc-vars { grid-template-columns:repeat(3,1fr); } }
@media (max-width:1200px){.aga-shell{grid-template-columns:210px minmax(0,1fr)}.aga-right{grid-column:1/-1}.aga-head{align-items:flex-start;flex-direction:column}.aga-head-actions{justify-content:flex-start}.aga-drawer{width:calc(100vw - 220px)}}
@media (max-width:1200px){.avd-body{grid-template-columns:220px minmax(0,1fr)}.avd-right{grid-column:1/-1}.avd-filter{grid-template-columns:1fr 150px 140px}.avd-check{grid-column:1/-1}.avd-head{align-items:flex-start;flex-direction:column}.avd-drawer{width:calc(100vw - 220px)}}
@media (max-width:1200px){.apv-body{grid-template-columns:250px minmax(0,1fr)}.apv-body>aside:last-child{grid-column:1/-1}.apv-head{align-items:flex-start;flex-direction:column}.apv-meta{margin-left:0}.apv-drawer{width:calc(100vw - 220px)}}
@media (max-width:1200px){.aat-commercial-grid,.aat-two-col{grid-template-columns:1fr}.aat-vars{grid-template-columns:repeat(3,1fr)}.aat-form-grid{grid-template-columns:repeat(2,1fr)}}
@media (max-width:768px){ .atc-vars { grid-template-columns:1fr 1fr; } .atc-meta .atc-btn { margin-left:0; } .aat-hero{display:block}.aat-actions{margin-top:12px;min-width:0}.aat-flow,.aat-vars,.aat-form-grid{grid-template-columns:1fr}.aat-row,.aat-rule{grid-template-columns:1fr}.aat-flow-step:after{display:none}.aga-drawer-overlay,.avd-overlay,.apv-overlay{left:0}.aga-drawer,.avd-drawer,.apv-drawer{width:96vw;max-width:96vw}.aga-shell{grid-template-columns:1fr;padding:12px}.aga-side{order:2}.aga-right{order:3}.aga-toolbar{overflow:auto}.aga-info{margin-left:12px;margin-right:12px}.avd-body{grid-template-columns:1fr;padding:12px}.avd-filter{grid-template-columns:1fr}.avd-table-wrap{overflow:auto}.avd-head,.avd-info{margin-left:12px;margin-right:12px}.avd-head{padding:16px 12px}.apv-body{grid-template-columns:1fr;padding:12px}.apv-head,.apv-info{margin-left:12px;margin-right:12px}.apv-head{padding:16px 12px}.apv-document{padding:20px;max-height:560px}.apv-document .apv-party-grid{grid-template-columns:1fr} }

/* Agency variables drawer: single-column layout with client-side paging for laptop-sized screens. */
.avd-body{grid-template-columns:minmax(0,1fr)!important;padding:12px 18px 18px!important;gap:12px!important}.avd-filter{grid-template-columns:minmax(220px,1fr) 158px 142px 170px;gap:10px;padding:13px}.avd-table-wrap{max-height:calc(100vh - 292px);overflow:auto}.avd-table{min-width:920px;table-layout:fixed}.avd-table th,.avd-table td{padding:9px 10px;word-break:break-word}.avd-table th:nth-child(1),.avd-table td:nth-child(1){width:170px}.avd-table th:nth-child(2),.avd-table td:nth-child(2){width:150px}.avd-table th:nth-child(3),.avd-table td:nth-child(3){width:150px}.avd-table th:nth-child(4),.avd-table td:nth-child(4){width:120px}.avd-table th:nth-child(5),.avd-table td:nth-child(5){width:260px}.avd-table th:nth-child(6),.avd-table td:nth-child(6){width:86px}.avd-table th:nth-child(7),.avd-table td:nth-child(7){width:74px}.avd-foot{position:sticky;bottom:0;background:#fff}.avd-pages button{min-width:30px;height:30px;border:1px solid #dce3ee;border-radius:7px;background:#fff;color:#34405f;font-size:11px;font-weight:600}.avd-pages button.active{border-color:#6338e8;background:#6338e8;color:#fff}.avd-pages button:disabled{opacity:.45;cursor:not-allowed}@media(max-width:1200px){.avd-filter{grid-template-columns:minmax(190px,1fr) 145px 132px}.avd-check{grid-column:1/-1}.avd-table-wrap{max-height:calc(100vh - 325px)}}@media(max-width:768px){.avd-body{padding:10px!important}.avd-title h3{font-size:18px!important}.avd-title p{font-size:11px}.avd-filter{grid-template-columns:1fr}.avd-table{min-width:850px}.avd-table-wrap{max-height:calc(100vh - 345px)}.avd-foot{align-items:flex-start;flex-direction:column;gap:8px}}

/* Agency preview drawer: keep the document readable while fitting 15-16 inch and smaller screens. */
.apv-head{padding:14px 18px!important}.apv-title h3{font-size:19px!important}.apv-body{grid-template-columns:minmax(0,1fr) 255px!important;gap:12px!important;padding:12px 18px 16px!important}.apv-toolbar{min-height:48px;padding:0 12px}.apv-tabs{gap:12px}.apv-tab{min-height:46px;font-size:11px}.apv-tools{gap:7px}.apv-tools .aat-btn{min-height:32px;padding:7px 10px}.apv-alert{margin:12px;padding:10px 12px}.apv-document{margin:0 12px 12px!important;padding:22px 32px!important;max-height:calc(100vh - 210px)!important;font-size:12px!important;line-height:1.65!important}.apv-document h1,.apv-document h2,.apv-document h3{font-size:16px!important}.apv-card h4{font-size:13px!important}.apv-pad{padding:13px!important}.apv-summary-list{gap:10px}.apv-commercial{margin-top:12px;padding:12px}.apv-info{margin:0 18px 16px!important;padding:11px 13px!important}@media(max-width:1200px){.apv-body{grid-template-columns:minmax(0,1fr) 230px!important;padding:10px 14px 14px!important}.apv-document{padding:20px 24px!important;max-height:calc(100vh - 225px)!important}.apv-tools .apv-select{width:96px!important}.apv-summary-row{display:block}.apv-summary-row b{display:block;margin-top:3px}}@media(max-width:991px){.apv-body{grid-template-columns:1fr!important}.apv-body>aside:last-child{grid-column:auto!important}.apv-document{max-height:560px!important}.apv-toolbar{align-items:flex-start;flex-direction:column;padding:10px 12px}.apv-tools{width:100%;justify-content:flex-start;flex-wrap:wrap}}@media(max-width:768px){.apv-head{padding:14px 12px!important}.apv-title h3{font-size:18px!important}.apv-body{padding:10px!important}.apv-document{padding:18px!important}.apv-info{margin:0 10px 12px!important}}
.apv-party-intro{max-width:760px;margin:0 auto 28px;padding:0 0 20px;border-bottom:1px solid #dfe6f1;text-align:center;color:#101833}.apv-party-intro h2{margin:0 0 16px!important;color:#101833!important;font-size:16px!important;font-weight:700!important;letter-spacing:.02em;text-align:center!important}.apv-party-intro .apv-effective{margin:0 0 20px;color:#101833;font-size:11px;line-height:1.5}.apv-party-intro .apv-effective b{font-weight:700}.apv-party-grid{display:grid;grid-template-columns:minmax(0,1fr) 58px minmax(0,1fr);gap:18px;align-items:start}.apv-party-box{text-align:center}.apv-party-name{margin-bottom:6px;color:#4f2bd5;font-size:13px;font-weight:700;line-height:1.35}.apv-party-address{max-width:250px;margin:0 auto;color:#101833;font-size:10.5px;line-height:1.45}.apv-party-role{margin-top:7px;color:#101833;font-size:10.5px;line-height:1.45}.apv-party-role b{font-weight:700}.apv-party-and{align-self:center;color:#53617f;font-size:11px;font-weight:700}.apv-party-closing{max-width:580px;margin:17px auto 0;color:#101833;font-size:10.5px;line-height:1.55}.apv-party-closing b{font-weight:700}@media(max-width:768px){.apv-party-grid{grid-template-columns:1fr;gap:12px}.apv-party-and{padding:2px 0}.apv-party-intro{margin-bottom:20px}.apv-party-intro h2{font-size:15px!important}}

/* Edit agreement drawer: compact editor-first layout for 15.6-inch and smaller screens. */
.aga-head{padding:14px 18px!important}.aga-title-wrap h3{font-size:19px!important}.aga-title-wrap p{font-size:11.5px!important;line-height:1.45}.aga-head-actions{gap:8px}.aga-head-actions .aat-btn{min-height:32px;padding:7px 11px;font-size:10.5px}.aga-shell{grid-template-columns:minmax(0,1fr) 228px!important;gap:12px!important;padding:12px 18px 16px!important}.aga-tabs{min-height:46px;padding:0 12px}.aga-tabs-left{gap:12px}.aga-tab{min-height:44px;font-size:11px}.aga-tools{gap:7px}.aga-tools .aat-btn{min-height:32px;padding:7px 10px}.aga-toolbar{gap:6px;padding:10px 12px;overflow:auto}.aga-toolbar select,.aga-toolbar button{height:32px;font-size:10.5px}.aga-editor-wrap{padding:10px 12px}.aga-editor-card .cke_contents{min-height:calc(100vh - 235px)!important}.aga-editor-foot{padding:8px 12px;font-size:10px}.aga-right{padding:12px!important}.aga-var-tabs{grid-template-columns:1fr!important}.aga-var-list{max-height:calc(100vh - 310px);gap:6px}.aga-var-item{padding:5px 6px;border-radius:7px;cursor:pointer}.aga-var-item:hover{background:#f5f1ff}.aga-var-item code{cursor:pointer}.aga-card{padding:11px!important}.aga-tips{display:none!important}@media(max-width:1200px){.aga-shell{grid-template-columns:minmax(0,1fr) 205px!important;padding:10px 14px 14px!important}.aga-editor-card .cke_contents{min-height:calc(100vh - 255px)!important}.aga-var-list{max-height:calc(100vh - 330px)}.aga-meta-row{display:block}.aga-meta-row b{display:block;margin-top:3px}}@media(max-width:991px){.aga-shell{grid-template-columns:1fr!important}.aga-right{order:2}.aga-main{order:1}.aga-var-list{max-height:220px}.aga-head{align-items:flex-start;flex-direction:column}.aga-head-actions{justify-content:flex-start}.aga-editor-card .cke_contents{min-height:520px!important}}@media(max-width:768px){.aga-head{padding:14px 12px!important}.aga-title-wrap h3{font-size:18px!important}.aga-shell{padding:10px!important}.aga-toolbar{padding:8px}.aga-editor-wrap{padding:8px}.aga-tools{width:100%;justify-content:flex-start}}

/* Commercials tab: presentation-only layer. It reuses the cached account payload and existing service actions. */
.commercials-v2,.commercials-v2 *{box-sizing:border-box;font-family:'Poppins',sans-serif!important}.commercials-v2{color:#111b3d;font-size:12px;font-weight:500;padding:2px 0 8px}.commercials-v2 .cv2-head{display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:18px}.commercials-v2 .cv2-title{margin:0;color:#101735;font-size:16px!important;font-weight:600!important;line-height:1.25}.commercials-v2 .cv2-sub{max-width:600px;margin:6px 0 0;color:#52638b;font-size:12px;font-weight:500;line-height:1.5}.commercials-v2 .cv2-stat-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px;margin-bottom:18px}.commercials-v2 .cv2-stat{display:flex;align-items:center;min-width:0;gap:13px;padding:16px;border:1px solid #e5eaf4;border-radius:11px;background:#fff;box-shadow:0 1px 3px rgba(15,23,66,.03)}.commercials-v2 .cv2-stat-icon,.commercials-v2 .cv2-service-icon{display:flex;flex:0 0 auto;align-items:center;justify-content:center;border-radius:11px}.commercials-v2 .cv2-stat-icon{width:46px;height:46px}.commercials-v2 .cv2-stat-icon svg{width:23px;height:23px}.commercials-v2 .cv2-service-icon{width:48px;height:48px}.commercials-v2 .cv2-service-icon svg{width:25px;height:25px}.commercials-v2 .blue{color:#1d3dff;background:#eaf1ff}.commercials-v2 .green{color:#08ac58;background:#e8f9ee}.commercials-v2 .amber{color:#f58911;background:#fff1e5}.commercials-v2 .cv2-stat-copy{min-width:0}.commercials-v2 .cv2-label{color:#52638b;font-size:11px;font-weight:500;line-height:1.35}.commercials-v2 .cv2-value{margin-top:3px;color:#101735;font-size:16px;font-weight:600;line-height:1.2;font-variant-numeric:tabular-nums}.commercials-v2 .cv2-hint{margin-top:5px;color:#08a851;font-size:10.5px;font-weight:500;line-height:1.25}.commercials-v2 .cv2-section-title{margin:0;color:#111b3d;font-size:14px;font-weight:600;line-height:1.35}.commercials-v2 .cv2-section-sub{margin:3px 0 12px;color:#52638b;font-size:11px;font-weight:500}.commercials-v2 .cv2-services{display:grid;gap:16px}.commercials-v2 .cv2-service{padding:16px 18px;border:1px solid #e2e8f3;border-radius:11px;background:#fff;box-shadow:0 1px 3px rgba(15,23,66,.025)}.commercials-v2 .cv2-service-top{display:flex;align-items:flex-start;justify-content:space-between;gap:16px}.commercials-v2 .cv2-service-name{display:flex;align-items:center;gap:13px;min-width:0}.commercials-v2 .cv2-service-name h4{margin:0;color:#101735;font-size:14px!important;font-weight:600!important;line-height:1.35}.commercials-v2 .cv2-service-desc{margin:3px 0 0;color:#52638b;font-size:11px;font-weight:500;line-height:1.4}.commercials-v2 .cv2-badge{display:inline-flex;align-items:center;margin-left:7px;padding:3px 8px;border-radius:5px;background:#e8f8ed;color:#08a851;font-size:10px;font-weight:600;vertical-align:2px}.commercials-v2 .cv2-badge.is-off{color:#c2410c;background:#fff2e8}.commercials-v2 .cv2-switch{display:inline-flex;align-items:center;gap:8px;flex:0 0 auto}.commercials-v2 .form-switch{margin:0}.commercials-v2 .form-check-input{width:38px!important;height:21px!important;margin:0!important;cursor:pointer}.commercials-v2 .cv2-switch-text{color:#52638b;font-size:10.5px;font-weight:600}.commercials-v2 .cv2-service-main{display:grid;grid-template-columns:minmax(0,1fr) 230px;gap:18px;margin-top:14px;padding-top:14px;border-top:1px solid #edf0f6}.commercials-v2 .cv2-disabled{opacity:.45}.commercials-v2 .cv2-facts{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:0}.commercials-v2 .cv2-fact{min-width:0;padding:0 14px;border-right:1px solid #e8ecf3}.commercials-v2 .cv2-fact:first-child{padding-left:0}.commercials-v2 .cv2-fact:last-child{border:0}.commercials-v2 .cv2-fact-label{color:#52638b;font-size:10px;font-weight:500;line-height:1.3}.commercials-v2 .cv2-fact-value{margin-top:5px;color:#111b3d;font-size:11px;font-weight:600;line-height:1.3;word-break:break-word}.commercials-v2 .cv2-fact-value.rate{color:#0a9f57}.commercials-v2 .cv2-side{display:grid;gap:8px;align-content:start;padding-left:18px;border-left:1px solid #e8ecf3}.commercials-v2 .cv2-side-row{display:flex;justify-content:space-between;gap:8px;color:#52638b;font-size:10px;font-weight:500}.commercials-v2 .cv2-side-row b{color:#111b3d;font-size:10.5px;font-weight:600;text-align:right;font-variant-numeric:tabular-nums}.commercials-v2 .cv2-bottom{display:flex;align-items:flex-end;justify-content:space-between;gap:16px;margin-top:14px}.commercials-v2 .cv2-country-label{margin:0 0 8px;color:#111b3d;font-size:11px;font-weight:600}.commercials-v2 .cv2-country-chip{display:inline-flex;align-items:center;gap:8px;min-height:38px;padding:7px 12px;color:#33466f;background:#f8faff;border:1px solid #e1e7f2;border-radius:7px;font-size:10.5px;font-weight:500}.commercials-v2 .cv2-country-chip svg{width:15px;height:15px;color:#2764ff}.commercials-v2 .cv2-country-chip b{color:#101735;font-weight:600}.commercials-v2 .cv2-edit{display:inline-flex!important;align-items:center;gap:8px;padding:9px 13px!important;color:#173af5!important;background:#fff!important;border:1px solid #b9c4ff!important;border-radius:7px!important;font-size:11px!important;font-weight:600!important;white-space:nowrap}.commercials-v2 .cv2-edit:hover{background:#f5f7ff!important}.commercials-v2 .cv2-edit svg{width:15px;height:15px}.commercials-v2 .cv2-note{display:flex;align-items:center;gap:14px;margin-top:16px;padding:13px 16px;border:1px solid #cfead8;border-radius:9px;background:#f7fdf8;color:#40547e}.commercials-v2 .cv2-note-icon{display:flex;flex:0 0 auto;align-items:center;justify-content:center;width:34px;height:34px;color:#08a851;background:#e7f8ec;border-radius:50%}.commercials-v2 .cv2-note-icon svg{width:21px;height:21px}.commercials-v2 .cv2-note p{margin:0;font-size:10.5px;font-weight:500;line-height:1.5}.commercials-v2 .cv2-note b{color:#111b3d;font-weight:600}@media(max-width:1199px){.commercials-v2 .cv2-stat-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.commercials-v2 .cv2-service-main{grid-template-columns:1fr}.commercials-v2 .cv2-side{grid-template-columns:repeat(3,minmax(0,1fr));padding:12px 0 0;border-top:1px solid #e8ecf3;border-left:0}.commercials-v2 .cv2-side-row{display:block}.commercials-v2 .cv2-side-row b{display:block;margin-top:3px;text-align:left}}@media(max-width:767px){.commercials-v2 .cv2-head,.commercials-v2 .cv2-service-top,.commercials-v2 .cv2-bottom{display:block}.commercials-v2 .cv2-switch{margin-top:12px}.commercials-v2 .cv2-stat-grid{grid-template-columns:1fr}.commercials-v2 .cv2-facts{grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}.commercials-v2 .cv2-fact:nth-child(2){border-right:0}.commercials-v2 .cv2-fact:nth-child(n+3){padding-left:0}.commercials-v2 .cv2-side{grid-template-columns:1fr 1fr}.commercials-v2 .cv2-edit{margin-top:12px}.commercials-v2 .cv2-note{align-items:flex-start}}

/* Compact enterprise service switch: native checkbox state and existing AJAX classes remain unchanged. */
.commercials-v2 .cv2-switch{min-height:28px;padding:3px;border:1px solid #e2e8f3;border-radius:8px;background:#f8faff}.commercials-v2 .cv2-switch .form-switch{display:flex;align-items:center;min-height:20px}.commercials-v2 .cv2-switch .form-check-input{position:relative;width:36px!important;height:20px!important;margin:0!important;appearance:none!important;-webkit-appearance:none!important;background:#cbd5e1!important;border:0!important;border-radius:999px!important;box-shadow:none!important;outline:0;transition:background-color .16s ease}.commercials-v2 .cv2-switch .form-check-input::after{position:absolute;top:3px;left:3px;width:14px;height:14px;content:'';background:#fff;border-radius:50%;box-shadow:0 1px 2px rgba(15,23,42,.24);transition:transform .16s ease}.commercials-v2 .cv2-switch .form-check-input:checked{background:#2563eb!important}.commercials-v2 .cv2-switch .form-check-input:checked::after{transform:translateX(16px)}.commercials-v2 .cv2-switch .form-check-input:focus-visible{box-shadow:0 0 0 3px rgba(37,99,235,.22)!important}.commercials-v2 .cv2-switch-text{display:none!important}
.candidate-config-shell{display:grid;grid-template-columns:244px minmax(0,1fr);gap:18px;align-items:start;font-family:'Poppins',sans-serif}.candidate-config-nav{padding:12px;border:1px solid #e5eaf4;border-radius:12px;background:#fff}.candidate-config-nav h4{margin:0 0 12px;padding:4px 6px 12px;border-bottom:1px solid #edf0f6;color:#111b3d;font-size:13px;font-weight:600}.candidate-config-group{margin:14px 6px 6px;color:#8391b1;font-size:10px;font-weight:600;letter-spacing:.04em;text-transform:uppercase}.candidate-config-link{display:flex;align-items:center;gap:10px;margin:4px 0;padding:11px 10px;border-radius:8px;color:#52638b!important;font-size:12px;font-weight:500;text-decoration:none}.candidate-config-link i{width:20px;height:20px;display:inline-flex;align-items:center;justify-content:center;border-radius:6px;background:#f1f4ff;color:#275df6;font-size:11px}.candidate-config-link span{display:block}.candidate-config-link small{display:block;margin-top:2px;color:#8391b1;font-size:10px;font-weight:500}.candidate-config-link.active,.candidate-config-link:hover{background:#eef3ff;color:#1b55f6!important}.candidate-config-link.active i{background:#dce8ff}.candidate-config-panel{min-width:0;padding:2px 0}.candidate-config-panel .card-header{padding-top:0!important}.candidate-config-panel .card-title h3{font-family:'Poppins',sans-serif;font-size:16px;font-weight:600}@media(max-width:991px){.candidate-config-shell{grid-template-columns:1fr}.candidate-config-nav{display:flex;gap:8px;overflow-x:auto}.candidate-config-nav h4,.candidate-config-group{display:none}.candidate-config-link{flex:0 0 auto;margin:0}.candidate-config-link small{display:none}}
/* Candidate offer-letter editing uses the original editor and persistence handler inside a focused right drawer. */
.offer-letter-drawer-ov{position:fixed;inset:0;background:rgba(15,23,42,.42);opacity:0;visibility:hidden;transition:opacity .18s ease;z-index:1054}.offer-letter-drawer-ov.show{opacity:1;visibility:visible}.offer-letter-drawer{--offer-drawer-sidebar:220px;position:fixed!important;top:0;right:0;bottom:0;width:min(1180px,calc((100vw - var(--offer-drawer-sidebar))*0.9));max-width:calc(100vw - var(--offer-drawer-sidebar) - 18px);min-width:0;margin:0!important;padding:0!important;background:#f5f8fa;box-shadow:-12px 0 32px rgba(15,23,42,.18);z-index:1055;overflow:auto}.offer-letter-drawer-header{position:sticky;top:0;z-index:2;display:flex;align-items:center;justify-content:space-between;gap:14px;padding:14px 18px;background:#fff;border-bottom:1px solid #e4e9f2}.offer-letter-drawer-title{margin:0;color:#111b3d;font-size:15px;font-weight:600}.offer-letter-drawer-sub{margin:2px 0 0;color:#64748b;font-size:11px;font-weight:500}.offer-letter-drawer-actions{display:flex;align-items:center;gap:8px}.offer-letter-drawer-actions .btn{font-family:'Poppins',sans-serif;font-size:11px;font-weight:600}.offer-letter-drawer-close{width:32px;height:32px;border:1px solid #dce3ef;border-radius:7px;background:#fff;color:#64748b;font-size:20px;line-height:1;cursor:pointer}.offer-letter-drawer-body{padding:16px 18px 24px;background:#f5f8fa}.offer-letter-drawer .cke{width:100%!important;border:1px solid #dce3ef!important;border-radius:8px;overflow:hidden}.offer-letter-drawer .cke_contents{min-height:calc(100vh - 190px)!important}.offer-letter-drawer .card-footer{display:none!important}@media(max-width:1199px){.offer-letter-drawer{--offer-drawer-sidebar:160px;width:calc((100vw - var(--offer-drawer-sidebar))*0.9);max-width:calc(100vw - var(--offer-drawer-sidebar) - 14px)}}@media(max-width:991px){.offer-letter-drawer{--offer-drawer-sidebar:0px;width:92vw;max-width:92vw}.offer-letter-drawer-header{align-items:flex-start;padding:12px}.offer-letter-drawer-actions{flex-wrap:wrap;justify-content:flex-end}.offer-letter-drawer-body{padding:12px}.offer-letter-drawer .cke_contents{min-height:calc(100vh - 210px)!important}}@media(max-width:575px){.offer-letter-drawer{width:94vw;max-width:94vw}.offer-letter-drawer-actions .btn{padding:8px 10px}}
.settings-offer-template,.settings-offer-template *{box-sizing:border-box;font-family:'Poppins',sans-serif}.settings-offer-template{font-size:12px;color:#181c32}.settings-offer-template .co-tpl-header{display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:14px;flex-wrap:wrap}.settings-offer-template .co-tpl-title{margin:0;color:#181c32;font-size:14px!important;font-weight:700!important}.settings-offer-template .co-tpl-sub{margin-top:2px;color:#7e8299;font-size:11px;line-height:1.45}.settings-offer-template .co-tpl-select-wrap{display:flex;align-items:center;gap:8px;flex-shrink:0}.settings-offer-template .co-tpl-select-label{color:#181c32;font-size:12px;font-weight:700;white-space:nowrap}.settings-offer-template .co-tpl-select{height:36px;min-width:220px;padding:4px 10px;border:1px solid #e4e6ef;border-radius:8px;background:#fff;color:#3f4254;font-size:12px!important}.settings-offer-template .co-tpl-layout{display:flex;align-items:flex-start;gap:16px;background:#fff}.settings-offer-template .co-tpl-editor-col{flex:1 1 auto;min-width:0}.settings-offer-template .co-tpl-side{flex:0 0 210px;max-width:210px;display:flex;flex-direction:column}.settings-offer-template .co-tpl-editor-col,.settings-offer-template .co-tpl-side{padding:14px;border:1px solid #eef1f7;border-radius:14px;background:#fff;box-shadow:0 1px 3px rgba(24,28,50,.05)}.settings-offer-template .co-tpl-subcard{margin-bottom:10px;padding:10px 12px;border:1px solid #eef1f7;border-radius:10px;background:#f9fafd}.settings-offer-template .co-tpl-tabs{display:flex;gap:4px;margin-bottom:12px;border-bottom:1px solid #eef1f7}.settings-offer-template .co-tpl-tab{padding:7px 12px;border:0;border-bottom:2px solid transparent;background:none;color:#a1a5b7;font-size:12px!important;font-weight:600;cursor:pointer}.settings-offer-template .co-tpl-tab--active{color:#1b84ff;border-bottom-color:#1b84ff}.settings-offer-template .co-tpl-meta-row{display:flex;align-items:center;gap:16px;flex-wrap:wrap}.settings-offer-template .co-tpl-meta-item{color:#7e8299;font-size:10px}.settings-offer-template .co-tpl-meta-item b{margin-left:4px;color:#181c32;font-weight:600}.settings-offer-template .co-letterhead{display:flex;align-items:flex-start;justify-content:space-between;gap:12px}.settings-offer-template .co-letterhead-logo img{display:block;max-width:150px;max-height:40px;object-fit:contain}.settings-offer-template .co-letterhead-address{max-width:260px;text-align:right;color:#7e8299;font-size:10px;line-height:1.5}.settings-offer-template .co-letterhead-company{color:#181c32;font-size:12px;font-weight:700}.settings-offer-template .co-tpl-side-title{color:#181c32;font-size:12px;font-weight:700}.settings-offer-template .co-tpl-side-sub{margin:3px 0 8px;color:#7e8299;font-size:10px;line-height:1.4}.settings-offer-template .co-var-chips{display:flex;flex-wrap:wrap;gap:5px}.settings-offer-template .co-var-chip{padding:4px 8px;border:1px solid #e0e7ff;border-radius:7px;background:#eef3ff;color:#4f46e5;font-size:10px!important;font-weight:600;white-space:nowrap;cursor:pointer}.settings-offer-template .co-var-chip:hover{background:#e0e7ff}.settings-offer-template .co-tpl-note{color:#7e8299;font-size:10px;line-height:1.5}.settings-offer-template .co-btn-reset{width:100%;height:36px;border:1px solid #f1416c!important;border-radius:8px;background:#fff!important;color:#f1416c!important;font-size:11px!important;font-weight:600!important}.settings-offer-template .co-tpl-preview{min-height:320px;max-height:460px;overflow:auto;padding:18px;border:1px solid #eef1f7;border-radius:10px;background:#fff;color:#334155;font-size:12px;line-height:1.6}.settings-offer-template .offer-letter-editor-shell .cke_contents{min-height:calc(100vh - 360px)!important}@media(max-width:1100px){.settings-offer-template .co-tpl-layout{flex-direction:column}.settings-offer-template .co-tpl-side{width:100%;max-width:none;flex:1 1 auto}.settings-offer-template .co-tpl-header{flex-direction:column;align-items:stretch}.settings-offer-template .co-tpl-select-wrap{width:100%}.settings-offer-template .co-tpl-select{flex:1 1 auto;min-width:0}}@media(max-width:575px){.settings-offer-template .co-letterhead{display:block}.settings-offer-template .co-letterhead-address{max-width:none;margin-top:10px;text-align:left}}
.settings-offer-template .co-tpl-zoom{display:flex;align-items:center;gap:6px}.settings-offer-template .co-tpl-zoom-btn{width:24px;height:24px;border:1px solid #e4e6ef;border-radius:6px;background:#fff;color:#5e6278;font-size:12px!important;font-weight:700;line-height:1;cursor:pointer}.settings-offer-template .co-tpl-zoom-btn:hover{background:#f5f8fa}.settings-offer-template .co-tpl-zoom-pct{display:inline-block;min-width:30px;text-align:center;color:#7e8299;font-size:10px}.settings-offer-template .co-tpl-download-btn{display:inline-flex;align-items:center;gap:5px;padding:5px 10px;border:0;border-radius:7px;background:#eef3ff;color:#4f46e5;font-size:10px!important;font-weight:600;cursor:pointer}.settings-offer-template .co-tpl-download-btn:hover{background:#e0e7ff}.settings-offer-template .co-tpl-download-btn svg{width:11px;height:11px}.settings-offer-template .co-tpl-toolbar-extra{align-items:center;justify-content:space-between;gap:10px}.settings-offer-template .offer-letter-editor-shell .cke_contents{min-height:calc(100vh - 410px)!important}
/* Candidate Configuration: card/sub-card layout for 15.6-inch and smaller screens while reusing existing forms and handlers. */
.candidate-config-shell{grid-template-columns:228px minmax(0,1fr)!important;gap:14px!important;width:100%;min-width:0}.candidate-config-nav{position:sticky;top:78px;padding:12px!important;border:1px solid #dbe4f1!important;border-radius:10px!important;box-shadow:0 3px 12px rgba(15,23,42,.045)!important}.candidate-config-nav h4{font-size:12.5px!important}.candidate-config-link{min-height:54px;padding:10px!important;border:1px solid transparent}.candidate-config-link.active{border-color:#dbe6ff}.candidate-config-link i{width:28px!important;height:28px!important;flex:0 0 28px}.candidate-config-panel{min-width:0!important;padding:0!important}.candidate-config-main-card{min-width:0;padding:15px;border:1px solid #dbe4f1;border-radius:10px;background:#fff;box-shadow:0 3px 12px rgba(15,23,42,.045)}.candidate-config-card-head{display:flex;align-items:flex-start;justify-content:space-between;gap:12px;margin-bottom:14px;padding-bottom:12px;border-bottom:1px solid #edf1f7}.candidate-config-title{margin:0;color:#101735;font-size:15px!important;font-weight:600!important;line-height:1.3}.candidate-config-subtitle{margin:4px 0 0;color:#667594;font-size:11px;font-weight:500;line-height:1.45}.candidate-config-actions{display:flex;align-items:center;gap:8px;flex-wrap:wrap}.candidate-config-actions .btn{display:inline-flex;align-items:center;gap:6px;min-height:34px;padding:8px 13px!important;border-radius:7px!important;font-family:'Poppins',sans-serif!important;font-size:11px!important;font-weight:600!important}.candidate-config-subcard{min-width:0;margin-bottom:12px;padding:13px;border:1px solid #e4eaf3;border-radius:9px;background:#fbfcff}.candidate-config-subcard:last-child{margin-bottom:0}.candidate-config-subcard-head{display:flex;align-items:flex-start;justify-content:space-between;gap:10px;margin-bottom:10px}.candidate-config-subcard-title{color:#13224f;font-size:12.5px;font-weight:600;line-height:1.35}.candidate-config-subcard-sub{margin-top:2px;color:#7b89a8;font-size:10.5px;font-weight:500;line-height:1.45}.candidate-config-preview{max-height:calc(100vh - 285px);min-height:330px;overflow:auto;padding:14px 16px;border:1px solid #e4eaf3;border-radius:8px;background:#fff;color:#24314d;font-size:12px;font-weight:400;line-height:1.65}.candidate-config-preview *{max-width:100%;font-family:'Poppins',sans-serif!important}.candidate-config-main-card .card-header,.candidate-config-main-card .card-body{padding:0!important;border:0!important;background:transparent!important}.candidate-config-main-card .row{margin-right:0!important;margin-left:0!important}.candidate-config-main-card .col-lg-12{padding-right:0!important;padding-left:0!important}@media(max-width:1199px){.candidate-config-shell{grid-template-columns:214px minmax(0,1fr)!important;gap:12px!important}.candidate-config-main-card{padding:13px}.candidate-config-preview{max-height:none;min-height:300px}}@media(max-width:991px){.candidate-config-shell{grid-template-columns:1fr!important}.candidate-config-nav{position:relative;top:auto;display:flex;gap:8px;overflow-x:auto}.candidate-config-link{flex:0 0 210px}.candidate-config-card-head{align-items:stretch;flex-direction:column}.candidate-config-actions{justify-content:flex-start}}@media(max-width:575px){.candidate-config-main-card{padding:11px;border-radius:8px}.candidate-config-subcard{padding:10px}.candidate-config-link{flex-basis:185px}.candidate-config-preview{padding:12px;min-height:260px}.candidate-config-actions .btn{width:100%;justify-content:center}}
/* Main Candidate Offer Letter preview mirrors the drawer preview while remaining a read-only card. */
.settings-offer-template .co-tpl-layout--main-preview{display:block!important;background:transparent!important}.settings-offer-template .co-tpl-layout--main-preview .co-tpl-editor-col{width:100%;max-width:none;box-shadow:none}.settings-offer-template .candidate-offer-main-preview{max-height:calc(100vh - 360px);min-height:360px}.settings-offer-template .candidate-offer-main-preview p{margin-bottom:12px}@media(max-width:991px){.settings-offer-template .candidate-offer-main-preview{max-height:none;min-height:300px}}
.candidate-config-card-head>.card-title:not(.candidate-config-actions){display:block!important;min-width:0}.candidate-config-card-head .candidate-config-subtitle{display:block!important;clear:both}
.candidate-config-link i svg,.candidate-action-icon svg{width:13px;height:13px;fill:none;stroke:currentColor;stroke-width:1.9;stroke-linecap:round;stroke-linejoin:round}.candidate-action-icon{display:inline-flex;align-items:center;justify-content:center;margin-right:6px;color:currentColor;line-height:1}
.aat-input-group.aat-fixed-fee-group{grid-template-columns:82px minmax(0,1fr)}.aat-calc-row.simple{grid-template-columns:minmax(0,1fr) 28px minmax(0,1fr)}
.jero-btn-loading{position:relative;pointer-events:none;opacity:.82}.jero-btn-loading:before{content:'';display:inline-block;width:13px;height:13px;margin-right:7px;border:2px solid currentColor;border-right-color:transparent;border-radius:50%;vertical-align:-2px;animation:jero-btn-spin .65s linear infinite}.jero-btn-loading.btn-icon:before,.jero-btn-loading.aat-drawer-close:before,.jero-btn-loading.aga-close:before{margin-right:0}@keyframes jero-btn-spin{to{transform:rotate(360deg)}}
.aat-edit-pill{border:0;cursor:pointer}.aat-drawer.agency-commercial-mode{background:#f6f8fc;color:#111b3d}.aat-drawer.agency-commercial-mode .aat-drawer-head{min-height:72px;padding:12px 18px}.aat-drawer.agency-commercial-mode .aat-drawer-title{gap:11px}.aat-drawer.agency-commercial-mode .aat-hero-icon{width:38px;height:38px;border-radius:8px;background:#eef2ff;color:#4f46e5}.aat-drawer.agency-commercial-mode .aat-hero-icon svg{width:20px;height:20px}.aat-drawer.agency-commercial-mode .aat-drawer-title h3{font-size:16.5px!important}.aat-drawer.agency-commercial-mode .aat-drawer-title p{font-size:11px;line-height:1.45}.aat-drawer.agency-commercial-mode .aat-drawer-body{padding:14px 14px 82px}.aat-drawer.agency-commercial-mode .aat-drawer-tabs{display:none}.aat-drawer.agency-commercial-mode .aat-edit-alert{margin-bottom:12px;padding:10px 12px;border-color:#d6e3ff;background:#f4f8ff;color:#405781;font-size:10.5px}.aat-drawer.agency-commercial-mode .aat-edit-section{margin-bottom:12px;border-color:#dbe3ef;border-radius:10px;box-shadow:0 1px 3px rgba(15,23,42,.045)}.aat-drawer.agency-commercial-mode .aat-edit-section-head{padding:14px 16px}.aat-drawer.agency-commercial-mode .aat-edit-section-title h4{font-size:13px!important}.aat-drawer.agency-commercial-mode .aat-edit-section-title p{font-size:10.5px}.aat-drawer.agency-commercial-mode .aat-edit-grid{grid-template-columns:repeat(3,minmax(0,1fr));gap:14px;padding:16px;background:linear-gradient(135deg,#fbfcff,#f8f7ff)}.aat-drawer.agency-commercial-mode .aat-field label{font-size:10.5px}.aat-drawer.agency-commercial-mode .aat-field select,.aat-drawer.agency-commercial-mode .aat-field input,.aat-drawer.agency-commercial-mode .aat-control{height:36px;font-size:11px}.aat-drawer.agency-commercial-mode .aat-calc{padding:12px}.aat-drawer.agency-commercial-mode .aat-drawer-footer{padding:10px 14px}.aat-drawer.agency-commercial-mode .aat-btn.save{min-width:140px}.aat-exp-table{grid-column:1/-1;overflow:hidden;border:1px solid #dce4ef;border-radius:8px;background:#fff}.aat-exp-head,.aat-exp-row{display:grid;grid-template-columns:minmax(160px,1fr) minmax(120px,.75fr) minmax(130px,.8fr);align-items:center;gap:10px}.aat-exp-head{min-height:34px;padding:7px 10px;color:#657395;background:#f7f9fc;border-bottom:1px solid #dfe6ef;font-size:9.8px;font-weight:600}.aat-exp-row{min-height:48px;padding:7px 10px;border-bottom:1px solid #e8edf4}.aat-exp-row:last-child{border-bottom:0}.aat-exp-row input{height:32px!important}.aat-exp-basis{color:#53617f;font-size:10.5px}.aat-drawer.agency-commercial-fulltime [data-agency-section="contract"],.aat-drawer.agency-commercial-contract [data-agency-section="fulltime"]{display:none}.aat-drawer.agency-commercial-fulltime [data-agency-section="general"],.aat-drawer.agency-commercial-contract [data-agency-section="general"]{display:block}@media(max-width:1200px){.aat-drawer.agency-commercial-mode .aat-edit-grid{grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}.aat-drawer.agency-commercial-mode{width:calc(100vw - 220px);max-width:calc(100vw - 220px)}}@media(max-width:768px){.aat-drawer-overlay{left:0}.aat-drawer.agency-commercial-mode{width:100vw;max-width:100vw}.aat-drawer.agency-commercial-mode .aat-edit-grid{grid-template-columns:1fr}.aat-drawer.agency-commercial-mode .aat-drawer-head{align-items:flex-start;flex-direction:column}.aat-drawer.agency-commercial-mode .aat-drawer-meta{justify-content:flex-start}.aat-exp-head,.aat-exp-row{grid-template-columns:1fr}.aat-exp-head{display:none}}

/* Intent: cev3-inspired model radios + range table styling inside the agency commercials drawer, mirroring the Edit Services reference. */
.aat-drawer.agency-commercial-mode .aat-edit-section[data-agency-section="fulltime aat-contract-only"] .aat-edit-grid{display:flex;flex-direction:column;gap:10px}.aat-drawer.agency-commercial-mode .aat-edit-section .aat-edit-grid>.aat-model-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px;grid-column:1/-1}
.aat-model-option{position:relative;display:flex;flex-direction:row;align-items:flex-start;gap:12px;width:100%;min-height:96px;padding:14px;color:#27365a;background:#fff;border:1px solid #dce3ee;border-radius:9px;text-align:left;transition:border-color .16s,box-shadow .16s,background .16s;cursor:pointer;overflow:hidden}
.aat-model-option:hover{border-color:#aaa5ff;background:#fcfbff}
.aat-model-option.active{border-color:#6657ff;background:#fcfbff;box-shadow:0 0 0 1px rgba(102,87,255,.12)}
.aat-model-option>span{display:flex;flex:1 1 auto;flex-direction:column;align-items:flex-start;min-width:0;padding-right:24px}
.aat-model-option b{display:block;margin:2px 0 7px;color:#111b3d;font-size:12px;font-weight:600;line-height:1.2;word-break:break-word}
.aat-model-option small{display:block;width:100%;color:#617091;font-size:10.5px;font-weight:500;line-height:1.55;word-break:break-word;overflow-wrap:break-word}
.aat-model-option i{position:absolute;left:18px;bottom:13px;width:16px;height:16px;border:1.5px solid #cdd5e3;border-radius:50%;flex:0 0 16px}
.aat-model-option.active i{border:5px solid #5b45f5}
.aat-model-icon{display:flex;flex:0 0 34px;width:34px;height:34px;align-items:center;justify-content:center;color:#4f46e5;background:#eff0ff;border-radius:8px;font-size:15px;font-weight:600}
.aat-help-inline{color:#94a3b8;font-weight:500;font-size:9.8px;margin-left:4px}
.aat-label{display:block;margin:0 0 6px;color:#26365c;font-size:10.5px;font-weight:600}
.aat-default-pricing-card{padding:16px;background:linear-gradient(135deg,#fbfcff,#f8f7ff)}
.aat-default-pricing-card,.aat-policy-card,.aat-payment-card{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px;padding:16px;background:linear-gradient(135deg,#fbfcff,#f8f7ff);border:1px solid #e3e6f2;border-radius:8px}
.aat-info{display:flex;align-items:flex-start;gap:8px;grid-column:1/-1;margin-top:6px;padding:9px 10px;color:#386252;background:#f0fbf6;border:1px solid #cceedd;border-radius:7px;font-size:10.5px;line-height:1.45}
.aat-info svg{flex:0 0 15px;width:15px;height:15px;fill:none;stroke:currentColor;stroke-width:1.8}
.aat-info.blue{color:#405781;background:#f4f8ff;border-color:#d6e3ff}
.aat-policy-note{display:flex;grid-column:1/-1;min-height:54px;align-items:center;gap:9px;padding:11px;color:#435783;background:#f6f5ff;border:1px solid #d8d5ff;border-radius:7px;font-size:10px;line-height:1.5}
.aat-policy-note>span{display:flex;width:22px;height:22px;align-items:center;justify-content:center;flex:0 0 22px;color:#4f46e5;border:1px solid #8d86ff;border-radius:50%;font-weight:600}
.aat-policy-note b{color:#1d2c5f}
.aat-range-table{grid-column:1/-1;overflow:hidden;border:1px solid #dce4ef;border-radius:8px;background:#fff}
.aat-range-head,.aat-range-row{display:grid;grid-template-columns:minmax(150px,1.05fr) minmax(120px,.72fr) minmax(120px,.72fr) minmax(160px,1fr) 34px;align-items:center;gap:10px}
.aat-range-head{min-height:34px;padding:7px 10px;color:#657395;background:#f7f9fc;border-bottom:1px solid #dfe6ef;font-size:9.8px;font-weight:600}
.aat-range-row{min-height:48px;padding:7px 10px;border-bottom:1px solid #e8edf4}
.aat-range-row:last-child{border-bottom:0}
.aat-range-row .aat-control{height:32px!important;font-size:10.5px;padding:4px 8px}
.aat-range-label{overflow:hidden;color:#24345a;font-size:10.5px;font-weight:600;text-overflow:ellipsis;white-space:nowrap}
.aat-rate-wrap{position:relative;display:flex;align-items:center}
.aat-rate-wrap .aat-control{padding-right:30px;font-variant-numeric:tabular-nums}
.aat-rate-suffix{position:absolute;right:10px;top:50%;color:#657395;font-size:10px;font-weight:600;transform:translateY(-50%)}
.aat-range-reset{display:flex;width:30px;height:30px;align-items:center;justify-content:center;color:#4f46e5;background:#f1f3ff;border:1px solid #d9dcff;border-radius:6px;font-size:14px;font-weight:600;cursor:pointer}
.aat-range-reset:hover{color:#fff;background:#4f46e5}
.aat-drawer.agency-commercial-mode .aat-help{display:block;margin:5px 0 0;color:#697797;font-size:9.8px;font-weight:500;line-height:1.4}
/* cev3-inspired fixed-fee dynamic mode within the agency commercials drawer. */
.aat-drawer.agency-commercial-mode .aat-fixed-only{display:none}
.aat-drawer.agency-commercial-mode.is-fixed-fee .aat-fixed-only{display:block}
.aat-drawer.agency-commercial-mode .aat-default-pricing-card{grid-template-columns:repeat(3,minmax(0,1fr))}
.aat-drawer.agency-commercial-mode.is-fixed-fee .aat-default-pricing-card{grid-template-columns:repeat(5,minmax(0,1fr))}
.aat-drawer.agency-commercial-mode.is-fixed-fee .aat-default-currency{order:1}
.aat-drawer.agency-commercial-mode.is-fixed-fee .aat-default-rate{order:2}
.aat-drawer.agency-commercial-mode.is-fixed-fee .aat-default-applied{order:3}
.aat-drawer.agency-commercial-mode.is-fixed-fee .aat-fixed-trigger-field{order:4}
.aat-drawer.agency-commercial-mode.is-fixed-fee .aat-fixed-frequency-field{order:5}
.aat-drawer.agency-commercial-mode.is-fixed-fee .aat-range-head,.aat-drawer.agency-commercial-mode.is-fixed-fee .aat-range-row{grid-template-columns:minmax(145px,1.05fr) minmax(100px,.7fr) minmax(120px,.82fr) minmax(135px,.9fr) minmax(115px,.78fr) 34px}
.aat-contract-range-table .aat-range-head,.aat-contract-range-table .aat-range-row{grid-template-columns:minmax(150px,1.05fr) minmax(120px,.72fr) minmax(160px,1fr) 34px}
.aat-drawer.agency-commercial-mode .aat-standard-pricing-field{display:none}
.aat-drawer.agency-commercial-mode .aat-range-head .aat-standard-pricing-field,.aat-drawer.agency-commercial-mode .aat-range-row .aat-standard-pricing-field{display:block}
.aat-drawer.agency-commercial-mode.is-fixed-fee .aat-range-head .aat-percentage-only,.aat-drawer.agency-commercial-mode.is-fixed-fee .aat-range-row .aat-percentage-only{display:none}
.aat-drawer.agency-commercial-mode:not(.is-fixed-fee) .aat-range-head .aat-fixed-only,.aat-drawer.agency-commercial-mode:not(.is-fixed-fee) .aat-range-row .aat-fixed-only{display:none}
.aat-drawer.agency-commercial-mode.is-fixed-fee .aat-range-head,.aat-drawer.agency-commercial-mode.is-fixed-fee .aat-range-row{grid-template-columns:minmax(145px,1.05fr) minmax(100px,.7fr) minmax(120px,.82fr) minmax(135px,.9fr) minmax(115px,.78fr) 34px}
@media(max-width:1199px){.aat-drawer.agency-commercial-mode .aat-default-pricing-card,.aat-drawer.agency-commercial-mode .aat-policy-card,.aat-drawer.agency-commercial-mode .aat-payment-card{grid-template-columns:repeat(2,minmax(0,1fr))}.aat-drawer.agency-commercial-mode.is-fixed-fee .aat-default-pricing-card{grid-template-columns:repeat(2,minmax(0,1fr))}.aat-drawer.agency-commercial-mode .aat-range-head,.aat-drawer.agency-commercial-mode .aat-range-row{grid-template-columns:minmax(135px,1fr) minmax(95px,.6fr) minmax(120px,.7fr) 30px;gap:8px}}
@media(max-width:767px){.aat-drawer.agency-commercial-mode .aat-range-table,.aat-drawer.agency-commercial-mode .aat-contract-range-table{overflow-x:auto}.aat-drawer.agency-commercial-mode .aat-range-head,.aat-drawer.agency-commercial-mode .aat-range-row{grid-template-columns:140px 95px 120px 30px;min-width:430px}.aat-drawer.agency-commercial-mode.is-fixed-fee .aat-range-head,.aat-drawer.agency-commercial-mode.is-fixed-fee .aat-range-row{grid-template-columns:145px 95px 130px 140px 115px 32px;min-width:660px}.aat-drawer.agency-commercial-mode .aat-edit-section .aat-edit-grid>.aat-model-grid{grid-template-columns:1fr}.aat-drawer.agency-commercial-mode .aat-default-pricing-card,.aat-drawer.agency-commercial-mode .aat-policy-card,.aat-drawer.agency-commercial-mode .aat-payment-card{grid-template-columns:1fr}.aat-drawer.agency-commercial-mode.is-fixed-fee .aat-default-pricing-card{grid-template-columns:1fr}}@media(min-width:768px) and (max-width:1199px){.aat-drawer.agency-commercial-mode .aat-edit-section .aat-edit-grid>.aat-model-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}

/* ── Modern Card + Sub-Card Enterprise Design System (Internal Client Drawer Standard - Rule 20) ── */
.cd-scope {
  box-sizing: border-box;
  font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
  font-size: 12px !important;
  color: #334155;
  background: #f8fafc;
}
.cd-scope *, .cd-scope *::before, .cd-scope *::after {
  box-sizing: border-box;
}
.cd-scope i, .cd-scope [class*="fa-"], .cd-scope [class^="fa-"], .cd-scope .fa, .cd-scope .fas, .cd-scope .far, .cd-scope .fab, .cd-scope .fa-solid, .cd-scope .fa-regular {
  font-family: "Font Awesome 6 Free", "Font Awesome 5 Free", "FontAwesome" !important;
  font-style: normal !important;
  font-variant: normal !important;
  text-rendering: auto !important;
  line-height: 1 !important;
  display: inline-block;
}
.cd-scope .fa-solid, .cd-scope .fas { font-weight: 900 !important; }
.cd-scope .fa-regular, .cd-scope .far { font-weight: 400 !important; }

/* ── Top Action Header Toolbar ── */
.cd-top-bar {
  background: #ffffff !important;
  border: 1px solid #e2e8f0 !important;
  border-radius: 10px !important;
  margin: 10px 14px 10px 14px !important;
  padding: 8px 14px !important;
  display: flex !important;
  align-items: center !important;
  justify-content: flex-start !important;
  position: sticky !important;
  top: 0 !important;
  z-index: 30 !important;
  box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04) !important;
  gap: 10px !important;
  flex-wrap: wrap !important;
  width: auto !important;
  box-sizing: border-box !important;
}
.cd-top-actions {
  display: flex !important;
  align-items: center !important;
  gap: 8px !important;
  flex-wrap: wrap !important;
}
.cd-top-actions-left {
  justify-content: flex-start !important;
  width: 100% !important;
}
/* ── Dedicated Specialties Card Styles ── */
.cd-specialties-grid {
  display: flex !important;
  flex-wrap: wrap !important;
  gap: 7px !important;
  align-items: center !important;
}
.cd-specialty-chip {
  display: inline-flex !important;
  align-items: center !important;
  gap: 6px !important;
  background: #f8fafc !important;
  border: 1px solid #e2e8f0 !important;
  border-radius: 6px !important;
  padding: 5px 10px !important;
  font-size: 11.5px !important;
  font-weight: 500 !important;
  color: #1e293b !important;
  transition: all 0.15s ease-in-out !important;
  line-height: 1.25 !important;
}
.cd-specialty-chip:hover {
  background: #eff6ff !important;
  border-color: #bfdbfe !important;
  color: #1d4ed8 !important;
}
.cd-specialty-bullet {
  width: 6px !important;
  height: 6px !important;
  border-radius: 50% !important;
  background: #3b82f6 !important;
  flex-shrink: 0 !important;
}
.cd-specialty-text {
  letter-spacing: -0.01em !important;
}
.cd-hdr-btn {
  height: 32px !important;
  padding: 0 12px !important;
  border-radius: 8px !important;
  font-size: 12px !important;
  font-weight: 600 !important;
  display: inline-flex !important;
  align-items: center !important;
  gap: 6px !important;
  cursor: pointer !important;
  text-decoration: none !important;
  transition: all 0.15s ease-in-out !important;
  line-height: 1 !important;
  border: 1px solid transparent !important;
}
.cd-hdr-btn-white-edit {
  background: #ffffff !important;
  border-color: #e2e8f0 !important;
  color: #334155 !important;
  box-shadow: 0 1px 2px rgba(15, 23, 42, 0.03) !important;
}
.cd-hdr-btn-white-edit:hover {
  background: #eff6ff !important;
  border-color: #bfdbfe !important;
  color: #009ef7 !important;
}
.cd-hdr-btn-outline {
  background: #ffffff !important;
  border-color: #e2e8f0 !important;
  color: #334155 !important;
  box-shadow: 0 1px 2px rgba(15, 23, 42, 0.03) !important;
}
.cd-hdr-btn-outline:hover {
  background: #ecfdf5 !important;
  border-color: #059669 !important;
  color: #059669 !important;
}
.cd-hdr-btn-white-job {
  background: #ffffff !important;
  border-color: #e2e8f0 !important;
  color: #334155 !important;
  box-shadow: 0 1px 2px rgba(15, 23, 42, 0.03) !important;
}
.cd-hdr-btn-white-job:hover {
  background: #eff6ff !important;
  border-color: #bfdbfe !important;
  color: #009ef7 !important;
}

/* ── Streamlined Minimalist Hero Identity ── */
.cd-hero-card.cd-hero-streamlined,
.cd-hero-card {
  background: #ffffff !important;
  border: 1px solid #e2e8f0 !important;
  border-radius: 10px !important;
  margin: 0 14px 12px 14px !important;
  padding: 0 !important;
  box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
  width: auto !important;
  box-sizing: border-box !important;
  overflow: hidden !important;
}
.cd-hero-main-row {
  display: flex !important;
  align-items: center !important;
  justify-content: space-between !important;
  padding: 12px 18px !important;
  gap: 16px !important;
}
@media (max-width: 768px) {
  .cd-hero-main-row {
    flex-direction: column !important;
    align-items: flex-start !important;
    gap: 10px !important;
  }
}
.cd-hero-left-flank {
  display: flex !important;
  align-items: center !important;
  gap: 12px !important;
  min-width: 0 !important;
  flex: 1 1 auto !important;
}
.cd-hero-avatar-wrap {
  position: relative !important;
  flex-shrink: 0 !important;
}
.cd-hero-avatar {
  width: 48px !important;
  height: 48px !important;
  border-radius: 10px !important;
  background: #ffffff !important;
  color: #0f172a !important;
  border: 1px solid #e2e8f0 !important;
  box-shadow: 0 1px 3px rgba(15, 23, 42, 0.05) !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  font-size: 15px !important;
  font-weight: 700 !important;
  overflow: hidden !important;
  padding: 6px !important;
  box-sizing: border-box !important;
}
.cd-hero-avatar img {
  max-width: 100% !important;
  max-height: 100% !important;
  width: auto !important;
  height: auto !important;
  object-fit: contain !important;
  display: block !important;
}
.cd-hero-avatar.is-initials {
  background: #f8fafc !important;
  color: #2563eb !important;
  padding: 0 !important;
}
.cd-hero-verified-check {
  position: absolute !important;
  top: -2px !important;
  right: -2px !important;
  width: 15px !important;
  height: 15px !important;
  border-radius: 50% !important;
  background: #10b981 !important;
  border: 2px solid #ffffff !important;
  color: #ffffff !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  font-size: 7.5px !important;
  box-shadow: 0 1px 3px rgba(0,0,0,0.15) !important;
}
.cd-hero-left-meta {
  display: flex !important;
  flex-direction: column !important;
  gap: 3px !important;
  min-width: 0 !important;
}
.cd-hero-title-wrap {
  display: flex !important;
  align-items: center !important;
  gap: 8px !important;
}
.cd-hero-name {
  font-size: 15.5px !important;
  font-weight: 700 !important;
  color: #0f172a !important;
  line-height: 1.25 !important;
  white-space: nowrap !important;
  overflow: hidden !important;
  text-overflow: ellipsis !important;
  letter-spacing: -0.2px !important;
  margin: 0 !important;
}
.cd-hero-id-badge {
  font-size: 10.5px !important;
  font-weight: 700 !important;
  color: #2563eb !important;
  background: #eff6ff !important;
  border: 1px solid #bfdbfe !important;
  padding: 1px 6px !important;
  border-radius: 4px !important;
  font-family: monospace !important;
  flex-shrink: 0 !important;
}
.cd-hero-web-wrap {
  display: flex !important;
  align-items: center !important;
  font-size: 12px !important;
}
.cd-hero-web-link {
  display: inline-flex !important;
  align-items: center !important;
  gap: 5px !important;
  font-size: 12px !important;
  font-weight: 500 !important;
  color: #2563eb !important;
  text-decoration: none !important;
  transition: color 0.15s ease !important;
}
.cd-hero-web-link:hover {
  text-decoration: underline !important;
  color: #1d4ed8 !important;
}
.cd-hero-web-empty {
  font-size: 11.5px !important;
  color: #94a3b8 !important;
}
.cd-hero-right-flank {
  display: flex !important;
  flex-direction: column !important;
  align-items: flex-end !important;
  gap: 6px !important;
  flex-shrink: 0 !important;
}
@media (max-width: 768px) {
  .cd-hero-right-flank {
    align-items: flex-start !important;
    width: 100% !important;
  }
}
.cd-hero-scope-row {
  display: flex !important;
  align-items: center !important;
}
.cd-pill-scope-internal {
  display: inline-flex !important;
  align-items: center !important;
  gap: 4px !important;
  background: #eff6ff !important;
  color: #2563eb !important;
  border: 1px solid #bfdbfe !important;
  font-size: 10.5px !important;
  font-weight: 600 !important;
  padding: 2.5px 8px !important;
  border-radius: 5px !important;
  line-height: 1.2 !important;
}
.cd-hero-status-pos-row {
  display: inline-flex !important;
  align-items: center !important;
  gap: 8px !important;
  font-size: 12px !important;
}
.cd-status-badge-active {
  display: inline-flex !important;
  align-items: center !important;
  gap: 6px !important;
  background: #ecfdf5 !important;
  color: #059669 !important;
  border: 1px solid #a7f3d0 !important;
  padding: 2px 7px !important;
  border-radius: 5px !important;
  font-size: 10.5px !important;
  font-weight: 600 !important;
  line-height: 1.2 !important;
}
.cd-pulse-emerald {
  width: 7px !important;
  height: 7px !important;
  border-radius: 50% !important;
  background: #10b981 !important;
  position: relative !important;
  flex-shrink: 0 !important;
}
.cd-status-sep {
  color: #cbd5e1 !important;
  font-weight: 300 !important;
  margin: 0 1px !important;
}
.cd-hero-positions-link {
  display: inline-flex !important;
  align-items: center !important;
  gap: 6px !important;
  font-size: 11.5px !important;
  font-weight: 500 !important;
  color: #334155 !important;
  text-decoration: none !important;
  cursor: pointer !important;
  padding: 3px 10px !important;
  border-radius: 6px !important;
  background: #ffffff !important;
  border: 1px solid #e2e8f0 !important;
  box-shadow: 0 1px 2px rgba(15, 23, 42, 0.03) !important;
  transition: all 0.15s ease !important;
  line-height: 1.2 !important;
}
.cd-hero-positions-link:hover {
  background: #fffbeb !important;
  border-color: #fde68a !important;
  color: #92400e !important;
}
.cd-hero-positions-link .cd-hero-pos-num {
  font-weight: 700 !important;
  font-size: 12px !important;
  color: #0f172a !important;
  margin-right: 2px !important;
}
.cd-hero-positions-link .cd-hero-pos-label {
  font-weight: 500 !important;
  color: #475569 !important;
}
.cd-hero-bottom-tags-row {
  background: #f8fafc !important;
  border-top: 1px solid #f1f5f9 !important;
  padding: 6px 18px !important;
  display: flex !important;
  align-items: center !important;
  justify-content: space-between !important;
  font-size: 11.5px !important;
  gap: 8px !important;
  flex-wrap: wrap !important;
}
.cd-hero-tags-left {
  display: flex !important;
  align-items: center !important;
  gap: 6px !important;
  flex-wrap: wrap !important;
}
.cd-hero-tags-label {
  font-weight: 600 !important;
  color: #64748b !important;
  font-size: 11.5px !important;
  display: inline-flex !important;
  align-items: center !important;
  margin-right: 2px !important;
}
.cd-hero-tag-pill {
  display: inline-flex !important;
  align-items: center !important;
  background: #ffffff !important;
  border: 1px solid #e2e8f0 !important;
  color: #475569 !important;
  font-size: 11px !important;
  font-weight: 500 !important;
  padding: 1px 7px !important;
  border-radius: 4px !important;
  line-height: 1.3 !important;
}
.cd-hero-tag-more {
  display: inline-flex !important;
  align-items: center !important;
  font-size: 11px !important;
  font-weight: 600 !important;
  color: #2563eb !important;
  margin-left: 2px !important;
}

/* ── 6 Metric KPI Summary Row ── */
.cd-kpi-grid {
  display: grid !important;
  grid-template-columns: repeat(6, minmax(0, 1fr)) !important;
  gap: 10px !important;
  margin: 0 14px 10px 14px !important;
  width: calc(100% - 28px) !important;
  box-sizing: border-box !important;
}
@media (max-width: 1280px) {
  .cd-kpi-grid { grid-template-columns: repeat(3, minmax(0, 1fr)) !important; }
}
@media (max-width: 640px) {
  .cd-kpi-grid { grid-template-columns: repeat(2, minmax(0, 1fr)) !important; }
}
.cd-kpi-card {
  background: #ffffff !important;
  border: 1px solid #e2e8f0 !important;
  border-radius: 10px !important;
  padding: 9px 11px !important;
  display: flex !important;
  align-items: center !important;
  gap: 10px !important;
  box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
  transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
  cursor: pointer !important;
  text-decoration: none !important;
  min-width: 0 !important;
  box-sizing: border-box !important;
  position: relative !important;
}
.cd-kpi-card:hover {
  transform: translateY(-2px) !important;
  box-shadow: 0 4px 12px rgba(15, 23, 42, 0.06) !important;
  border-color: #cbd5e1 !important;
}
.cd-kpi-icon {
  width: 36px !important;
  height: 36px !important;
  border-radius: 8px !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  font-size: 13.5px !important;
  flex-shrink: 0 !important;
  transition: transform 0.2s ease !important;
}
.cd-kpi-card:hover .cd-kpi-icon { transform: scale(1.08) !important; }
.cd-kpi-icon-blue    { background: #eff6ff !important; color: #2563eb !important; }
.cd-kpi-icon-emerald { background: #ecfdf5 !important; color: #059669 !important; }
.cd-kpi-icon-purple  { background: #f5f3ff !important; color: #7c3aed !important; }
.cd-kpi-icon-amber   { background: #fffbeb !important; color: #d97706 !important; }
.cd-kpi-icon-rose    { background: #fdf2f8 !important; color: #db2777 !important; }
.cd-kpi-icon-teal    { background: #f0fdfa !important; color: #0d9488 !important; }
.cd-kpi-body {
  display: flex !important;
  flex-direction: column !important;
  min-width: 0 !important;
  flex: 1 !important;
}
.cd-kpi-num {
  font-size: 17px !important;
  font-weight: 700 !important;
  color: #0f172a !important;
  line-height: 1.15 !important;
  letter-spacing: -0.02em !important;
  font-family: 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif !important;
  white-space: nowrap !important;
  overflow: hidden !important;
  text-overflow: ellipsis !important;
}
.cd-kpi-lbl {
  font-size: 11px !important;
  font-weight: 500 !important;
  color: #64748b !important;
  line-height: 1.25 !important;
  margin-top: 2px !important;
  white-space: nowrap !important;
  overflow: hidden !important;
  text-overflow: ellipsis !important;
  letter-spacing: -0.01em !important;
}

/* ── Sub-Tabs Card ── */
.cd-drawer-tabs-card {
  background: #ffffff !important;
  border: 1px solid #e2e8f0 !important;
  border-radius: 8px !important;
  padding: 5px 8px !important;
  margin: 0 14px 10px 14px !important;
  box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04) !important;
  width: auto !important;
  box-sizing: border-box !important;
  overflow: visible !important;
  display: flex !important;
  align-items: center !important;
  min-height: 48px !important;
  height: auto !important;
}
.cd-drawer-tabs-list {
  display: flex !important;
  align-items: center !important;
  gap: 6px !important;
  overflow-x: auto !important;
  overflow-y: visible !important;
  list-style: none !important;
  padding: 2px 0 !important;
  margin: 0 !important;
  flex-wrap: nowrap !important;
  width: 100% !important;
  min-height: 38px !important;
  height: auto !important;
}
.cd-drawer-tabs-list .nav-item {
  margin: 0 !important;
  padding: 0 !important;
  flex-shrink: 0 !important;
  display: flex !important;
  align-items: center !important;
  height: 34px !important;
}
.cd-tab-pill-btn {
  background: #f8fafc !important;
  border: 1px solid #e2e8f0 !important;
  border-radius: 6px !important;
  padding: 0 12px !important;
  font-size: 11px !important;
  font-weight: 600 !important;
  color: #475569 !important;
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  gap: 6px !important;
  height: 34px !important;
  text-decoration: none !important;
  box-shadow: 0 1px 2px rgba(15, 23, 42, 0.02) !important;
  transition: all 0.15s ease-in-out !important;
  cursor: pointer !important;
  white-space: nowrap !important;
  flex-shrink: 0 !important;
  line-height: 1 !important;
  box-sizing: border-box !important;
}
.cd-tab-pill-btn i {
  font-size: 11px !important;
  color: #64748b !important;
  transition: color 0.15s !important;
}
.cd-tab-pill-btn:hover {
  background: #f1f5f9 !important;
  border-color: #cbd5e1 !important;
  color: #0f172a !important;
}
.cd-tab-pill-btn:hover i { color: #009ef7 !important; }
.cd-tab-pill-btn.active {
  background: #009ef7 !important;
  border-color: #009ef7 !important;
  color: #ffffff !important;
  box-shadow: 0 2px 5px rgba(0, 158, 247, 0.22) !important;
}
.cd-tab-pill-btn.active i { color: #ffffff !important; }
.cd-tab-pill-badge {
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  min-width: 17px !important;
  height: 16px !important;
  padding: 0 4.5px !important;
  border-radius: 999px !important;
  font-size: 9.5px !important;
  font-weight: 700 !important;
  background: #eff6ff !important;
  color: #2563eb !important;
  line-height: 16px !important;
}
.cd-tab-pill-btn.active .cd-tab-pill-badge {
  background: rgba(255, 255, 255, 0.28) !important;
  color: #ffffff !important;
}

/* ── Overview 2-Column Grid ── */
.cd-grid-2col {
  display: grid !important;
  grid-template-columns: minmax(0, 1.25fr) minmax(0, 0.95fr) !important;
  gap: 14px !important;
  padding: 0 14px 20px 14px !important;
  align-items: start !important;
  background: #f8fafc !important;
}
@media (max-width: 1024px) {
  .cd-grid-2col { grid-template-columns: 1fr !important; }
}
.cd-card-panel {
  background: #ffffff !important;
  border: 1px solid #e2e8f0 !important;
  border-radius: 10px !important;
  padding: 14px 16px !important;
  margin-bottom: 14px !important;
  box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04) !important;
}
.cd-card-panel:last-child { margin-bottom: 0 !important; }
.cd-card-hdr {
  display: flex !important;
  align-items: center !important;
  justify-content: space-between !important;
  margin-bottom: 12px !important;
  padding-bottom: 8px !important;
  border-bottom: 1px solid #f1f5f9 !important;
}
.cd-card-hdr-left {
  display: flex !important;
  align-items: center !important;
  gap: 8px !important;
}
.cd-card-hdr-title {
  font-size: 13.5px !important;
  font-weight: 700 !important;
  color: #0f172a !important;
}
.cd-card-hdr-link {
  font-size: 12px !important;
  color: #2563eb !important;
  font-weight: 600 !important;
  text-decoration: none !important;
  display: inline-flex !important;
  align-items: center !important;
  gap: 4px !important;
  cursor: pointer !important;
}
.cd-card-hdr-link:hover { text-decoration: underline !important; }
.cd-about-body {
  font-size: 12px !important;
  font-weight: 400 !important;
  line-height: 1.65 !important;
  color: #475569 !important;
}

/* ── Recent Activity Timeline ── */
.cd-timeline-list {
  position: relative !important;
  padding-left: 28px !important;
}
.cd-timeline-list::before {
  content: "" !important;
  position: absolute !important;
  top: 14px !important;
  bottom: 14px !important;
  left: 13px !important;
  width: 1.5px !important;
  background: #e2e8f0 !important;
}
.cd-tl-item {
  position: relative !important;
  display: flex !important;
  align-items: flex-start !important;
  justify-content: space-between !important;
  gap: 12px !important;
  padding: 8px 0 !important;
  border-bottom: 1px dashed #f1f5f9 !important;
}
.cd-tl-item:last-child { border-bottom: none !important; padding-bottom: 0 !important; }
.cd-tl-item:first-child { padding-top: 0 !important; }
.cd-tl-dot {
  position: absolute !important;
  left: -28px !important;
  top: 9px !important;
  width: 26px !important;
  height: 26px !important;
  border-radius: 50% !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  font-size: 11.5px !important;
  border: 2px solid #ffffff !important;
  box-shadow: 0 1px 3px rgba(0,0,0,0.06) !important;
  z-index: 1 !important;
}
.cd-tl-dot-green  { background: #ecfdf5 !important; color: #059669 !important; }
.cd-tl-dot-purple { background: #f5f3ff !important; color: #7c3aed !important; }
.cd-tl-dot-orange { background: #fff7ed !important; color: #ea580c !important; }
.cd-tl-dot-blue   { background: #eff6ff !important; color: #2563eb !important; }
.cd-tl-info { flex: 1 !important; min-width: 0 !important; }
.cd-tl-heading {
  font-size: 12.5px !important;
  font-weight: 600 !important;
  color: #0f172a !important;
  line-height: 1.25 !important;
}
.cd-tl-sub {
  font-size: 11px !important;
  font-weight: 400 !important;
  color: #64748b !important;
  line-height: 1.25 !important;
  margin-top: 2px !important;
}
.cd-tl-meta-right {
  display: flex !important;
  align-items: center !important;
  gap: 10px !important;
  flex-shrink: 0 !important;
}
.cd-tl-time {
  font-size: 11px !important;
  font-weight: 400 !important;
  color: #94a3b8 !important;
  white-space: nowrap !important;
}
.cd-tl-badge {
  font-size: 10.5px !important;
  font-weight: 600 !important;
  padding: 2.5px 8px !important;
  border-radius: 6px !important;
  line-height: 1.2 !important;
  white-space: nowrap !important;
}
.cd-tl-badge-blue   { background: #eff6ff !important; color: #2563eb !important; border: 1px solid #bfdbfe !important; }
.cd-tl-badge-amber  { background: #fffbeb !important; color: #d97706 !important; border: 1px solid #fde68a !important; }
.cd-tl-badge-orange { background: #fff7ed !important; color: #ea580c !important; border: 1px solid #fed7aa !important; }
.cd-tl-badge-green  { background: #ecfdf5 !important; color: #059669 !important; border: 1px solid #a7f3d0 !important; }

/* ── Vertical Info List ── */
.cd-vertical-info-list {
  display: flex !important;
  flex-direction: column !important;
  width: 100% !important;
}
.cd-vinfo-row {
  display: flex !important;
  align-items: flex-start !important;
  justify-content: space-between !important;
  gap: 12px !important;
  padding: 9px 2px !important;
  border-bottom: 1px solid #f1f5f9 !important;
  min-width: 0 !important;
  transition: background-color 0.15s ease !important;
}
.cd-vinfo-row:first-child { padding-top: 2px !important; }
.cd-vinfo-row:last-child { border-bottom: none !important; padding-bottom: 2px !important; }
.cd-vinfo-row:hover { background-color: #fcfdfe !important; }
.cd-vinfo-key {
  display: flex !important;
  align-items: center !important;
  gap: 8px !important;
  flex: 0 0 132px !important;
  min-width: 110px !important;
}
.cd-vinfo-icon {
  width: 26px !important;
  height: 26px !important;
  border-radius: 6px !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  font-size: 11px !important;
  flex-shrink: 0 !important;
}
.cd-vinfo-icon.red     { background: #fef2f2 !important; color: #ef4444 !important; }
.cd-vinfo-icon.blue    { background: #eff6ff !important; color: #2563eb !important; }
.cd-vinfo-icon.green   { background: #ecfdf5 !important; color: #059669 !important; }
.cd-vinfo-icon.amber   { background: #fefce8 !important; color: #ca8a04 !important; }
.cd-vinfo-icon.purple  { background: #f5f3ff !important; color: #7c3aed !important; }
.cd-vinfo-icon.sky     { background: #f0f9ff !important; color: #0284c7 !important; }
.cd-vinfo-icon.emerald { background: #f0fdf4 !important; color: #16a34a !important; }
.cd-vinfo-icon.orange  { background: #fff7ed !important; color: #ea580c !important; }
.cd-vinfo-icon.slate   { background: #f8fafc !important; color: #475569 !important; border: 1px solid #e2e8f0 !important; }
.cd-vinfo-icon.cyan    { background: #ecfeff !important; color: #0891b2 !important; }
.cd-vinfo-icon.indigo  { background: #eef2ff !important; color: #4f46e5 !important; }
.cd-vinfo-label {
  font-size: 11.5px !important;
  font-weight: 600 !important;
  color: #64748b !important;
  letter-spacing: -0.1px !important;
  line-height: 1.3 !important;
}
.cd-vinfo-val {
  flex: 1 !important;
  min-width: 0 !important;
  text-align: right !important;
  display: flex !important;
  flex-direction: column !important;
  align-items: flex-end !important;
  justify-content: center !important;
  font-size: 12.5px !important;
  font-weight: 500 !important;
  color: #0f172a !important;
  word-break: break-word !important;
}
.cd-vinfo-link {
  color: #2563eb !important;
  font-weight: 600 !important;
  text-decoration: none !important;
  display: inline-flex !important;
  align-items: center !important;
  gap: 4px !important;
  transition: color 0.15s ease !important;
}
.cd-vinfo-link:hover {
  color: #1d4ed8 !important;
  text-decoration: underline !important;
}
.cd-career-pill {
  display: inline-flex !important;
  align-items: center !important;
  gap: 4px !important;
  background: #ecfdf5 !important;
  color: #059669 !important;
  border: 1px solid #a7f3d0 !important;
  padding: 2px 7px !important;
  border-radius: 4px !important;
  font-size: 11px !important;
  font-weight: 600 !important;
  text-decoration: none !important;
  transition: all 0.15s ease !important;
}
.cd-career-pill:hover {
  background: #059669 !important;
  color: #ffffff !important;
  border-color: #059669 !important;
}
.cd-tax-wrap {
  display: inline-flex !important;
  align-items: center !important;
  gap: 6px !important;
}
.cd-tax-code {
  font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace !important;
  font-size: 11.5px !important;
  font-weight: 600 !important;
  background: #f1f5f9 !important;
  color: #0f172a !important;
  padding: 2px 7px !important;
  border-radius: 4px !important;
  border: 1px solid #e2e8f0 !important;
  letter-spacing: 0.3px !important;
}
.cd-copy-btn {
  background: transparent !important;
  border: 1px solid #e2e8f0 !important;
  border-radius: 4px !important;
  color: #64748b !important;
  width: 22px !important;
  height: 22px !important;
  padding: 0 !important;
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  font-size: 10px !important;
  cursor: pointer !important;
  transition: all 0.15s ease !important;
}
.cd-copy-btn:hover {
  background: #f8fafc !important;
  color: #0f172a !important;
  border-color: #cbd5e1 !important;
}
.cd-type-badge-pill {
  font-size: 11px !important;
  font-weight: 600 !important;
  padding: 2px 7px !important;
  border-radius: 4px !important;
  background: #f0f9ff !important;
  color: #0284c7 !important;
  border: 1px solid #bae6fd !important;
}
.cd-vintage-chip {
  font-size: 10.5px !important;
  color: #64748b !important;
  font-weight: 500 !important;
  margin-left: 4px !important;
}
.cd-social-links-row {
  display: inline-flex !important;
  align-items: center !important;
  gap: 5px !important;
  flex-wrap: wrap !important;
  justify-content: flex-end !important;
}
.cd-social-btn {
  width: 24px !important;
  height: 24px !important;
  border-radius: 5px !important;
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  font-size: 11px !important;
  color: #ffffff !important;
  text-decoration: none !important;
  transition: opacity 0.15s ease, transform 0.15s ease !important;
}
.cd-social-btn:hover {
  opacity: 0.9 !important;
  transform: translateY(-1px) !important;
  color: #ffffff !important;
}
.cd-social-btn.linkedin { background: #0a66c2 !important; }
.cd-social-btn.twitter  { background: #0f172a !important; }
.cd-social-btn.github   { background: #24292f !important; }
.cd-social-btn.youtube  { background: #ff0000 !important; }
.cd-social-btn.facebook { background: #1877f2 !important; }
.cd-social-btn.instagram{ background: linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888) !important; }

/* ── Enterprise Location Sub-Cards ── */
.cd-loc-group {
  display: flex !important;
  flex-direction: column !important;
  gap: 10px !important;
  width: 100% !important;
}
.cd-loc-card {
  background: #ffffff !important;
  border: 1px solid #e2e8f0 !important;
  border-left: 3.5px solid #94a3b8 !important;
  border-radius: 8px !important;
  padding: 10px 14px !important;
  text-align: left !important;
  width: 100% !important;
  box-shadow: 0 1px 2px rgba(15, 23, 42, 0.03) !important;
  transition: all 0.2s ease !important;
  box-sizing: border-box !important;
}
.cd-loc-card:hover {
  border-color: #cbd5e1 !important;
  box-shadow: 0 3px 6px rgba(15, 23, 42, 0.05) !important;
}
.cd-loc-card.is-primary,
.cd-loc-card.hq {
  background: #ffffff !important;
  border-color: #dbeafe !important;
  border-left: 3.5px solid #2563eb !important;
}
.cd-loc-header {
  display: flex !important;
  align-items: center !important;
  justify-content: space-between !important;
  gap: 8px !important;
  margin-bottom: 6px !important;
}
.cd-loc-header-left {
  display: inline-flex !important;
  align-items: center !important;
  gap: 6px !important;
  flex-shrink: 0 !important;
}
.cd-loc-badge {
  display: inline-flex !important;
  align-items: center !important;
  font-size: 10px !important;
  font-weight: 700 !important;
  line-height: 1 !important;
  white-space: nowrap !important;
  flex-shrink: 0 !important;
  padding: 3px 7px !important;
  border-radius: 4px !important;
  letter-spacing: 0.2px !important;
  text-transform: uppercase !important;
}
.cd-loc-badge.loc-primary {
  background: #eff6ff !important;
  color: #1d4ed8 !important;
  border: 1px solid #bfdbfe !important;
}
.cd-loc-badge.loc-secondary {
  background: #f1f5f9 !important;
  color: #334155 !important;
  border: 1px solid #cbd5e1 !important;
}
.cd-loc-hq-tag {
  display: inline-flex !important;
  align-items: center !important;
  font-size: 9px !important;
  font-weight: 800 !important;
  line-height: 1 !important;
  text-transform: uppercase !important;
  letter-spacing: 0.4px !important;
  background: #2563eb !important;
  color: #ffffff !important;
  padding: 2.5px 6px !important;
  border-radius: 4px !important;
  white-space: nowrap !important;
  flex-shrink: 0 !important;
}
.cd-loc-country-pill {
  display: inline-flex !important;
  align-items: center !important;
  font-size: 10.5px !important;
  font-weight: 600 !important;
  line-height: 1 !important;
  color: #475569 !important;
  background: #f8fafc !important;
  border: 1px solid #e2e8f0 !important;
  padding: 3px 9px !important;
  border-radius: 12px !important;
  white-space: nowrap !important;
  flex-shrink: 0 !important;
}
.cd-loc-city-line {
  display: flex !important;
  align-items: center !important;
  gap: 6px !important;
  font-size: 12.5px !important;
  line-height: 1.35 !important;
  margin-bottom: 4px !important;
  word-break: break-word !important;
}
.cd-loc-city-name {
  font-weight: 700 !important;
  color: #0f172a !important;
}
.cd-loc-street-line {
  display: flex !important;
  align-items: flex-start !important;
  gap: 5px !important;
  font-size: 11.5px !important;
  color: #64748b !important;
  line-height: 1.4 !important;
  word-break: break-word !important;
  margin-bottom: 3px !important;
  padding-left: 17px !important;
}
.cd-loc-meta-line {
  display: flex !important;
  align-items: center !important;
  gap: 6px !important;
  margin-top: 4px !important;
  padding-left: 17px !important;
}
.cd-loc-meta-chip {
  display: inline-flex !important;
  align-items: center !important;
  background: #f8fafc !important;
  border: 1px solid #e2e8f0 !important;
  border-radius: 4px !important;
  padding: 2px 7px !important;
  font-size: 10px !important;
  font-weight: 500 !important;
  color: #64748b !important;
  line-height: 1.2 !important;
}
.cd-loc-meta-chip strong {
  color: #0f172a !important;
  font-weight: 700 !important;
  margin-left: 3px !important;
}

/* ── Administrators List ── */
.cd-adm-item {
  display: flex !important;
  align-items: center !important;
  gap: 10px !important;
  padding: 8px 0 !important;
  border-bottom: 1px dashed #f1f5f9 !important;
}
.cd-adm-item:last-child { border-bottom: none !important; padding-bottom: 0 !important; }
.cd-adm-avatar {
  width: 34px !important;
  height: 34px !important;
  border-radius: 8px !important;
  background: #eff6ff !important;
  color: #2563eb !important;
  border: 1px solid #bfdbfe !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  font-size: 12px !important;
  font-weight: 700 !important;
  flex-shrink: 0 !important;
  overflow: hidden !important;
}
.cd-adm-avatar img {
  width: 100% !important;
  height: 100% !important;
  object-fit: cover !important;
}
.cd-adm-info { flex: 1 !important; min-width: 0 !important; }
.cd-adm-name-row {
  display: flex !important;
  align-items: center !important;
  gap: 6px !important;
}
.cd-adm-name {
  font-size: 12px !important;
  font-weight: 600 !important;
  color: #0f172a !important;
  white-space: nowrap !important;
  overflow: hidden !important;
  text-overflow: ellipsis !important;
}
.cd-adm-pri-badge {
  font-size: 9px !important;
  font-weight: 700 !important;
  background: #ecfdf5 !important;
  color: #059669 !important;
  border: 1px solid #a7f3d0 !important;
  padding: 1px 5px !important;
  border-radius: 3px !important;
}
.cd-adm-role {
  font-size: 10.5px !important;
  color: #64748b !important;
  margin-top: 1px !important;
}
.cd-adm-contacts {
  font-size: 10.5px !important;
  color: #94a3b8 !important;
  margin-top: 2px !important;
}
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

@section('content')
 
@php $logo_path				= '';//	url('img/client-default.png'); @endphp
@if(!empty(@$account->logo) && strlen(@$account->logo)>10)
		@php //$logo_path		=  url('public/user-uploads/company-logo/'.@$account->logo); @endphp
			@php $logo_path		=  @$account->logo; 
      
      @endphp 
@endif
@php
  if (!empty(@$account->website) && empty(@$account->logo)){
          $domainGet = GeneralMethod::getDomain($account->website);
          $googleIcon = "https://www.google.com/s2/favicons?domain={$domainGet}&sz=256";    
          $google = GeneralMethod::getDomainIconCompany($googleIcon,$account->id,false);
          $logo_path  = ($google) ? $googleIcon : '';
  }

@endphp
 
<!--begin::Toolbar-->
<div class="toolbar d-none" id="kt_toolbar">
  <!--begin::Container-->
  <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack px-5">
    <!--begin::Page title-->
    <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
      <!--begin::All Client-->
      <div class="d-flex flex-column">
        <h5 class="d-flex align-items-center text-dark fw-bolder my-1"> <span class="text-gray-800 mb-1">Company Profile</span> </h5>
      </div>
      <!--end::All Client-->
      <!--begin::Separator-->
      <span class="h-20px border-gray-200 border-start mx-4 d-none"></span>
      <!--end::Separator-->
    </div>
    <!--end::Page title-->
    <!--begin::Actions-->
    <div class="d-flex align-items-center py-1">
      <!--begin::Search-->
      <div class="d-flex align-items-center position-relative my-1">
        <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
        <span class="svg-icon svg-icon-1 position-absolute ms-6">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
          <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
          <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />
        </svg>
        </span>
        <!--end::Svg Icon-->
        <input type="hidden" id="inputSearch" data-kt-user-table-filter="search" class="form-control form-control-solid w-250px ps-14" style="padding: .6rem 1rem;" placeholder="Search..." />
      </div>
      <!--end::Search-->
    </div>
    <!--end::Actions-->
  </div>
  <!--end::Container-->
</div>
<!--end::Toolbar-->
<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid pt-0" id="kt_content" style="margin-top:-50px;">
<!--begin::Post-->
<div class="post d-flex flex-column-fluid" id="kt_post">
<!--begin::Container-->
<div id="kt_content_container" class="container-fluid px-0">
{{-- Keep commercial and advertising workspaces focused; their existing data and handlers are unchanged. --}}
@php
  // Intent: retain overview-only display values even when the commercial or ads workspace hides the dashboard hero.
  $cad_pc = [
    'Basic Information' => (!empty(@$account->company_name) && !empty(@$industries_name)),
    'Company Details'   => (!empty(@$account->company_size) || !empty(@$account->founded)),
    'Branding'          => !empty($logo_path),
    'Description'       => !empty(trim(strip_tags((string)@$account->description))),
    'Location'          => (!empty($company_city_name) || !empty($company_country_name)),
    'Website'           => !empty(@$account->website),
  ];
  $cad_done  = count(array_filter($cad_pc));
  $cad_total = count($cad_pc);
  $cad_pct   = $cad_total ? round($cad_done/$cad_total*100) : 0;
  $cad_r = 52; $cad_circ = 2*M_PI*$cad_r; $cad_off = $cad_circ*(1-$cad_pct/100);
  $cad_initial = strtoupper(substr(trim((string)@$account->company_name),0,1));
  $cad_size = '';
  $userPermissions = (array)($userPermissions ?? []);
  if(!empty(@$account->company_size_from)){
    $cad_size = (@$account->company_size_from >= 10000) ? $account->company_size_from.'+ employees' : trim($account->company_size_from.' - '.@$account->company_size).' employees';
  }
@endphp
@if(request('tab') !== 'services' && request('tab') !== 'ads' && request('ads') !== 'show')
<div class="cd-scope">
  <!-- ── TIER 1: TOP ACTION HEADER TOOLBAR CARD (Actions on Left per Rule 20) ── -->
  <div class="cd-top-bar">
    <div class="cd-top-actions cd-top-actions-left">
      @if(in_array("edit_settings", $userPermissions) || empty($userPermissions))
      <a href="{{ route('service_company.account.edit', GeneralMethod::value_encrypt($company_id)) }}" class="cd-hdr-btn cd-hdr-btn-white-edit" title="Edit Company Profile">
        <i class="fas fa-pen-to-square fa-solid text-primary me-1"></i> Edit Profile
      </a>
      @endif
      @if(in_array('add_jobs', $userPermissions) || empty($userPermissions))
      <a href="{{ url('service_company/jobs/create') }}" class="cd-hdr-btn cd-hdr-btn-white-job" title="Post New Job Opening">
        <i class="fas fa-plus fa-solid text-primary me-1"></i> Add Job
      </a>
      @endif
      @if(in_array('add_team', $userPermissions) || empty($userPermissions))
      <a href="{{ route('service_company.team.index', ['action' => 'invite']) }}" class="cd-hdr-btn cd-hdr-btn-outline" title="Add / Invite Team Member">
        <i class="fas fa-user-plus fa-solid text-success me-1"></i> Add Team
      </a>
      @endif
    </div>
  </div>

  <!-- ── HERO MASTER CARD: STREAMLINED MINIMALIST HEADER ── -->
  <div class="cd-hero-card cd-hero-streamlined">
    <div class="cd-hero-main-row">
      <!-- Left Flank: Company Identity & Website -->
      <div class="cd-hero-left-flank">
        <div class="cd-hero-avatar-wrap">
          @php
            $cad_initial = strtoupper(substr(trim((string)@$account->company_name), 0, 1));
            $resolvedLogo = !empty($logo_path) ? $logo_path : (!empty($account->logo) ? (str_starts_with($account->logo, 'http') ? $account->logo : asset_url('company-logo/'.$account->logo)) : '');
          @endphp
          <div class="cd-hero-avatar {{ empty($resolvedLogo) ? 'is-initials' : '' }}">
            @if (!empty($resolvedLogo))
              <img src="{{ $resolvedLogo }}" alt="{{ $account->company_name }}">
            @else
              {{ $cad_initial }}
            @endif
          </div>
          <div class="cd-hero-verified-check" title="Verified Service Company">
            <i class="fas fa-check"></i>
          </div>
        </div>
        <div class="cd-hero-left-meta">
          <div class="cd-hero-title-wrap">
            <h2 class="cd-hero-name" title="{{ $account->company_name }}">{{ ucwords(@$account->company_name) }}</h2>
            <span class="cd-hero-id-badge">#SC-{{ $account->id ?? $company_id }}</span>
          </div>
          <div class="cd-hero-web-wrap">
            @php $webClean = @$account->website ?: (@$client->website ?: ''); @endphp
            @if (!empty($webClean) && $webClean !== '-')
              <a href="{{ str_starts_with($webClean, 'http') ? $webClean : 'https://'.$webClean }}" target="_blank" class="cd-hero-web-link" title="Visit Official Website">
                <i class="fas fa-globe text-primary"></i>
                <span>{{ preg_replace('#^https?://(www\.)?#i', '', rtrim($webClean, '/')) }}</span>
                <i class="fas fa-arrow-up-right-from-square" style="font-size: 9px;"></i>
              </a>
            @else
              <span class="cd-hero-web-empty text-muted">
                <i class="fas fa-globe text-muted me-1"></i>No Website Provided
              </span>
            @endif
          </div>
        </div>
      </div>

      <!-- Right Flank: Scope & Status / Positions -->
      <div class="cd-hero-right-flank">
        <div class="cd-hero-scope-row">
          <span class="cd-pill-scope-internal">
            <i class="far fa-building me-1"></i> Service Company
          </span>
        </div>
        <div class="cd-hero-status-pos-row">
          <span class="cd-status-badge-active" title="Company workspace is active">
            <span class="cd-pulse-emerald"></span> Active
          </span>
          <span class="cd-status-sep">|</span>
          <a href="{{ url('service_company/jobs') }}" class="cd-hero-positions-link" title="View Active Job Positions">
            <span class="cd-hero-pos-icon"><i class="fas fa-bolt text-warning"></i></span>
            <span class="cd-hero-pos-num">{{ $kpiActiveJobs ?? $activeJobs ?? 0 }}</span>
            <span class="cd-hero-pos-label">Active {{ ($kpiActiveJobs ?? $activeJobs ?? 0) == 1 ? 'Position' : 'Positions' }}</span>
          </a>
        </div>
      </div>
    </div>

    <!-- Bottom Row: Dynamic Tags Strip -->
    <div class="cd-hero-bottom-tags-row">
      <div class="cd-hero-tags-left">
        <span class="cd-hero-tags-label"><i class="fas fa-tags me-1"></i>Tags:</span>
        @php
          $tagNames = [];
          if (!empty($tags) && count($tags) > 0) {
            foreach($tags as $t) {
              if (!empty($t->tag_name)) { $tagNames[] = $t->tag_name; }
            }
          }
          if (empty($tagNames) && !empty($client->tags)) {
            foreach(explode(',', $client->tags) as $tg) {
              if (trim($tg) !== '') { $tagNames[] = trim($tg); }
            }
          }
        @endphp
        @if (!empty($tagNames) && count($tagNames) > 0)
          @foreach (array_slice($tagNames, 0, 5) as $tn)
            <span class="cd-hero-tag-pill">{{ ucwords($tn) }}</span>
          @endforeach
          @if (count($tagNames) > 5)
            <span class="cd-hero-tag-more">+{{ count($tagNames) - 5 }}</span>
          @endif
        @else
          <span class="cd-hero-tag-pill" style="background:#ffffff; color:#64748b; border:1px solid #e2e8f0;">{{ $industries_name ?: 'Staffing & Recruiting' }}</span>
          <span class="cd-hero-tag-pill" style="background:#ffffff; color:#64748b; border:1px solid #e2e8f0;">Talent Marketplace</span>
        @endif
      </div>
    </div>
  </div>

  <!-- ── 6 METRIC KPI SUMMARY ROW: AUTHORITATIVE BUSINESS TOTALS (Rule 20 Compliant) ── -->
  <div class="cd-kpi-grid">
    <!-- KPI 1: All Jobs -->
    <a href="{{ url('service_company/jobs') }}" class="cd-kpi-card" title="Total jobs created till date">
      <div class="cd-kpi-icon cd-kpi-icon-blue">
        <i class="fas fa-layer-group fa-solid"></i>
      </div>
      <div class="cd-kpi-body">
        <div class="cd-kpi-num" id="cdKpiTotalJobs">{{ number_format($kpiTotalJobs ?? 0) }}</div>
        <div class="cd-kpi-lbl">All Jobs</div>
      </div>
    </a>

    <!-- KPI 2: Active Jobs -->
    <a href="{{ url('service_company/jobs') }}" class="cd-kpi-card" title="Total jobs currently active">
      <div class="cd-kpi-icon cd-kpi-icon-emerald">
        <i class="fas fa-bolt fa-solid"></i>
      </div>
      <div class="cd-kpi-body">
        <div class="cd-kpi-num" id="cdKpiActiveJobs">{{ number_format($kpiActiveJobs ?? $activeJobs ?? 0) }}</div>
        <div class="cd-kpi-lbl">Active Jobs</div>
      </div>
    </a>

    <!-- KPI 3: All Candidates -->
    <a href="{{ route('service_company.cvtracker.index') }}" class="cd-kpi-card" title="Total applications in system">
      <div class="cd-kpi-icon cd-kpi-icon-purple">
        <i class="fas fa-user-check fa-solid"></i>
      </div>
      <div class="cd-kpi-body">
        <div class="cd-kpi-num" id="cdKpiAllCandidates">{{ number_format($kpiCandidatesSubmitted ?? 0) }}</div>
        <div class="cd-kpi-lbl">All Candidates</div>
      </div>
    </a>

    <!-- KPI 4: All Interviews -->
    <a href="{{ route('service_company.interview-schedule.index') }}" class="cd-kpi-card" title="Total interviews scheduled">
      <div class="cd-kpi-icon cd-kpi-icon-amber">
        <i class="fas fa-calendar-check fa-solid"></i>
      </div>
      <div class="cd-kpi-body">
        <div class="cd-kpi-num" id="cdKpiAllInterviews">{{ number_format($kpiInterviews ?? 0) }}</div>
        <div class="cd-kpi-lbl">All Interviews</div>
      </div>
    </a>

    <!-- KPI 5: All Offers -->
    <a href="{{ route('service_company.applicationoffer.index') }}" class="cd-kpi-card" title="Total candidate offers generated">
      <div class="cd-kpi-icon cd-kpi-icon-rose">
        <i class="fas fa-file-signature fa-solid"></i>
      </div>
      <div class="cd-kpi-body">
        <div class="cd-kpi-num" id="cdKpiAllOffers">{{ number_format($kpiOffers ?? 0) }}</div>
        <div class="cd-kpi-lbl">All Offers</div>
      </div>
    </a>

    <!-- KPI 6: All Joinings -->
    <a href="{{ route('service_company.job-onboard.index') }}" class="cd-kpi-card" title="Total candidate joinings confirmed">
      <div class="cd-kpi-icon cd-kpi-icon-teal">
        <i class="fas fa-handshake fa-solid"></i>
      </div>
      <div class="cd-kpi-body">
        <div class="cd-kpi-num" id="cdKpiAllJoinings">{{ number_format($kpiJoined ?? 0) }}</div>
        <div class="cd-kpi-lbl">All Joinings</div>
      </div>
    </a>
  </div>
</div>
@endif
<!--beign::Company Details (legacy, hidden)-->
<div class="card mb-5 mb-xl-5 mx-2 d-none">
  <div class="card-body pt-5 pb-0 px-5">
    <!--begin::Details-->
    <div class="d-flex flex-wrap flex-sm-nowrap mb-3">
      <!--begin: Pic-->
      <div class="me-7 mb-4">
        <div class="symbol symbol-100px symbol-lg-140px symbol-fixed position-relative">
          @php
          $bgTextDecore = $strVal = '';
          $str = trim($account->company_name);
          $str = $strVal = strtolower($str[0]);
          @endphp
          @if (!empty($logo_path)) 
          <img src="{{ @$logo_path }}" alt="image">
          @else
          <div class="symbol-label fs-3 {{ @$bgTextDecore}} border-radius-5"><span>{{ ucwords(@$strVal)}}</span></div>
          @endif


          <div class="position-absolute translate-middle bottom-0 start-100 mb-6 bg-success rounded-circle border border-4 border-white h-20px w-20px"></div>
        </div>
      </div>
      <!--end::Pic-->
      <!--begin::Info-->
      <div class="flex-grow-1">
        <!--begin::Row-1-->
        <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
          <!--begin::Candidate Details-->
          <div class="d-flex flex-column">
           
            <div class="d-flex align-items-center mb-2"> 
				<span class="text-gray-900 fs-2 fw-bolder me-2">{{ ucwords(@$account->company_name) }}</span> 
				<a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="Verified">
              <span class="svg-icon svg-icon-1 svg-icon-success">
              <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24">
                <path d="M10.0813 3.7242C10.8849 2.16438 13.1151 2.16438 13.9187 3.7242V3.7242C14.4016 4.66147 15.4909 5.1127 16.4951 4.79139V4.79139C18.1663 4.25668 19.7433 5.83365 19.2086 7.50485V7.50485C18.8873 8.50905 19.3385 9.59842 20.2758 10.0813V10.0813C21.8356 10.8849 21.8356 13.1151 20.2758 13.9187V13.9187C19.3385 14.4016 18.8873 15.491 19.2086 16.4951V16.4951C19.7433 18.1663 18.1663 19.7433 16.4951 19.2086V19.2086C15.491 18.8873 14.4016 19.3385 13.9187 20.2758V20.2758C13.1151 21.8356 10.8849 21.8356 10.0813 20.2758V20.2758C9.59842 19.3385 8.50905 18.8873 7.50485 19.2086V19.2086C5.83365 19.7433 4.25668 18.1663 4.79139 16.4951V16.4951C5.1127 15.491 4.66147 14.4016 3.7242 13.9187V13.9187C2.16438 13.1151 2.16438 10.8849 3.7242 10.0813V10.0813C4.66147 9.59842 5.1127 8.50905 4.79139 7.50485V7.50485C4.25668 5.83365 5.83365 4.25668 7.50485 4.79139V4.79139C8.50905 5.1127 9.59842 4.66147 10.0813 3.7242V3.7242Z" fill="#00A3FF"></path>
                <path class="permanent" d="M14.8563 9.1903C15.0606 8.94984 15.3771 8.9385 15.6175 9.14289C15.858 9.34728 15.8229 9.66433 15.6185 9.9048L11.863 14.6558C11.6554 14.9001 11.2876 14.9258 11.048 14.7128L8.47656 12.4271C8.24068 12.2174 8.21944 11.8563 8.42911 11.6204C8.63877 11.3845 8.99996 11.3633 9.23583 11.5729L11.3706 13.4705L14.8563 9.1903Z" fill="white"></path>
              </svg>
              </span>
              </a> 
			  </div>
            
            <div class="d-flex flex-wrap fw-bold fs-6 pe-2"> <a href="javascript:void(0);" class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2">
              <span class="svg-icon svg-icon-4 me-1">
              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                  <rect x="0" y="0" width="24" height="24"/>
                  <path d="M5.84026576,8 L18.1597342,8 C19.1999115,8 20.0664437,8.79732479 20.1528258,9.83390904 L20.8194924,17.833909 C20.9112219,18.9346631 20.0932459,19.901362 18.9924919,19.9930915 C18.9372479,19.9976952 18.8818364,20 18.8264009,20 L5.1735991,20 C4.0690296,20 3.1735991,19.1045695 3.1735991,18 C3.1735991,17.9445645 3.17590391,17.889153 3.18050758,17.833909 L3.84717425,9.83390904 C3.93355627,8.79732479 4.80008849,8 5.84026576,8 Z M10.5,10 C10.2238576,10 10,10.2238576 10,10.5 L10,11.5 C10,11.7761424 10.2238576,12 10.5,12 L13.5,12 C13.7761424,12 14,11.7761424 14,11.5 L14,10.5 C14,10.2238576 13.7761424,10 13.5,10 L10.5,10 Z" fill="#000000"/>
                  <path d="M10,8 L8,8 L8,7 C8,5.34314575 9.34314575,4 11,4 L13,4 C14.6568542,4 16,5.34314575 16,7 L16,8 L14,8 L14,7 C14,6.44771525 13.5522847,6 13,6 L11,6 C10.4477153,6 10,6.44771525 10,7 L10,8 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                </g>
              </svg>
              </span> 
			  	{{ @$industries_name }}
			  </a> 
			  <a href="javascript:void(0);" class="d-flex align-items-center text-gray-400 text-hover-primary mb-2 me-5">
              <span class="svg-icon svg-icon-4 me-1">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                <path opacity="0.3" d="M18.0624 15.3453L13.1624 20.7453C12.5624 21.4453 11.5624 21.4453 10.9624 20.7453L6.06242 15.3453C4.56242 13.6453 3.76242 11.4453 4.06242 8.94534C4.56242 5.34534 7.46242 2.44534 11.0624 2.04534C15.8624 1.54534 19.9624 5.24534 19.9624 9.94534C20.0624 12.0453 19.2624 13.9453 18.0624 15.3453Z" fill="black"></path>
                <path d="M12.0624 13.0453C13.7193 13.0453 15.0624 11.7022 15.0624 10.0453C15.0624 8.38849 13.7193 7.04535 12.0624 7.04535C10.4056 7.04535 9.06241 8.38849 9.06241 10.0453C9.06241 11.7022 10.4056 13.0453 12.0624 13.0453Z" fill="black"></path>
              </svg>
              </span>
			  	{{ ucwords($company_city_name) }} {{ ' , ' }} {{ ucwords($company_country_name) }}
              </a>
			  
			   <a href="javascript:void(0);" class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2">
              <span class="svg-icon svg-icon-4 me-1">
              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                  <polygon points="0 0 24 0 24 24 0 24"></polygon>
                  <path d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                  <path d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z" fill="#000000" fill-rule="nonzero"></path>
                </g>
              </svg>
              </span>
              ------- </a> 
			  </div>
            <!--end::Designaiton-->
          </div>
          <!--end::Company Details-->
        </div>
        <!--end::Row-1-->
        <!--begin::Row-2-->
        <div class="d-flex flex-wrap flex-stack">
          <!--begin::Wrapper-->
          <div class="d-flex flex-column flex-grow-1 pe-8">
            <div class="row w-75"> <span class="">
				 @if(!empty($client->tags))
					 @php $tags	=	explode(',', $client->tags); @endphp 
					 @foreach($tags as $key=>$val) 
						{!! '<span class="badge badge-light me-2">'.ucwords(@$val).'</span>' !!}
					 @endforeach
				 @endif		
              </span> </div>
          </div>
          <!--end::Wrapper-->
        </div>
        <!--end::Row-2-->
      </div>
      <!--end::Info-->
    </div>
    <!--begin::separator-->
    {{-- <div class="separator separator-solid my-7"></div> --}}
    <!--end::separator-->
    <!--begin::section-->
    <div class="d-flex align-items-center flex-wrap pb-7 d-none">
      <!--begin: Item1-->
      <div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
        <!--begin::Svg-->
        <span class="svg-icon svg-icon-dark-800 svg-icon-3x me-2">
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
          <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
            <rect x="0" y="0" width="24" height="24"/>
            <path d="M13.5,21 L13.5,18 C13.5,17.4477153 13.0522847,17 12.5,17 L11.5,17 C10.9477153,17 10.5,17.4477153 10.5,18 L10.5,21 L5,21 L5,4 C5,2.8954305 5.8954305,2 7,2 L17,2 C18.1045695,2 19,2.8954305 19,4 L19,21 L13.5,21 Z M9,4 C8.44771525,4 8,4.44771525 8,5 L8,6 C8,6.55228475 8.44771525,7 9,7 L10,7 C10.5522847,7 11,6.55228475 11,6 L11,5 C11,4.44771525 10.5522847,4 10,4 L9,4 Z M14,4 C13.4477153,4 13,4.44771525 13,5 L13,6 C13,6.55228475 13.4477153,7 14,7 L15,7 C15.5522847,7 16,6.55228475 16,6 L16,5 C16,4.44771525 15.5522847,4 15,4 L14,4 Z M9,8 C8.44771525,8 8,8.44771525 8,9 L8,10 C8,10.5522847 8.44771525,11 9,11 L10,11 C10.5522847,11 11,10.5522847 11,10 L11,9 C11,8.44771525 10.5522847,8 10,8 L9,8 Z M9,12 C8.44771525,12 8,12.4477153 8,13 L8,14 C8,14.5522847 8.44771525,15 9,15 L10,15 C10.5522847,15 11,14.5522847 11,14 L11,13 C11,12.4477153 10.5522847,12 10,12 L9,12 Z M14,12 C13.4477153,12 13,12.4477153 13,13 L13,14 C13,14.5522847 13.4477153,15 14,15 L15,15 C15.5522847,15 16,14.5522847 16,14 L16,13 C16,12.4477153 15.5522847,12 15,12 L14,12 Z" fill="#000000"/>
            <rect fill="#FFFFFF" x="13" y="8" width="3" height="3" rx="1"/>
            <path d="M4,21 L20,21 C20.5522847,21 21,21.4477153 21,22 L21,22.4 C21,22.7313708 20.7313708,23 20.4,23 L3.6,23 C3.26862915,23 3,22.7313708 3,22.4 L3,22 C3,21.4477153 3.44771525,21 4,21 Z" fill="#000000" opacity="0.3"/>
          </g>
        </svg>
        </span>
        <!--end::Svg Icon-->
        <div class="d-flex flex-column text-dark-75"> <span class="fw-bolder fs-6">Type</span> <span class="fw-bolder fs-3">
            
				@if($account->industries_type >= 0)
					{{ $industries_type_arr[$account->industries_type] }}    
				@endif						
          </span> </div>
      </div>
      <!--end: Item1-->
      <!--begin: Item2-->
      <div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
        <!--begin::Svg-->
        <span class="svg-icon svg-icon-dark-800 svg-icon-3x me-2">
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
          <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
            <polygon points="0 0 24 0 24 24 0 24"></polygon>
            <path d="M18,14 C16.3431458,14 15,12.6568542 15,11 C15,9.34314575 16.3431458,8 18,8 C19.6568542,8 21,9.34314575 21,11 C21,12.6568542 19.6568542,14 18,14 Z M9,11 C6.790861,11 5,9.209139 5,7 C5,4.790861 6.790861,3 9,3 C11.209139,3 13,4.790861 13,7 C13,9.209139 11.209139,11 9,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
            <path d="M17.6011961,15.0006174 C21.0077043,15.0378534 23.7891749,16.7601418 23.9984937,20.4 C24.0069246,20.5466056 23.9984937,21 23.4559499,21 L19.6,21 C19.6,18.7490654 18.8562935,16.6718327 17.6011961,15.0006174 Z M0.00065168429,20.1992055 C0.388258525,15.4265159 4.26191235,13 8.98334134,13 C13.7712164,13 17.7048837,15.2931929 17.9979143,20.2 C18.0095879,20.3954741 17.9979143,21 17.2466999,21 C13.541124,21 8.03472472,21 0.727502227,21 C0.476712155,21 -0.0204617505,20.45918 0.00065168429,20.1992055 Z" fill="#000000" fill-rule="nonzero"></path>
          </g>
        </svg>
        </span>
        <!--end::Svg Icon-->
        <div class="d-flex flex-column text-dark-75"> 
		    	<span class="fw-bolder fs-6">Employees</span> <span class="fw-bolder fs-3">{{ @$account->company_size }}</span> 
		    </div>
      </div>
      <!--end: Item2-->
      <!--begin: Item3-->
      <div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
              <!--begin::Svg-->
              <span class="svg-icon svg-icon-dark-800 svg-icon-3x me-2">
              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                  <rect x="0" y="0" width="24" height="24"></rect>
                  <path d="M4.875,20.75 C4.63541667,20.75 4.39583333,20.6541667 4.20416667,20.4625 L2.2875,18.5458333 C1.90416667,18.1625 1.90416667,17.5875 2.2875,17.2041667 C2.67083333,16.8208333 3.29375,16.8208333 3.62916667,17.2041667 L4.875,18.45 L8.0375,15.2875 C8.42083333,14.9041667 8.99583333,14.9041667 9.37916667,15.2875 C9.7625,15.6708333 9.7625,16.2458333 9.37916667,16.6291667 L5.54583333,20.4625 C5.35416667,20.6541667 5.11458333,20.75 4.875,20.75 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                  <path d="M2,11.8650466 L2,6 C2,4.34314575 3.34314575,3 5,3 L19,3 C20.6568542,3 22,4.34314575 22,6 L22,15 C22,15.0032706 21.9999948,15.0065399 21.9999843,15.009808 L22.0249378,15 L22.0249378,19.5857864 C22.0249378,20.1380712 21.5772226,20.5857864 21.0249378,20.5857864 C20.7597213,20.5857864 20.5053674,20.4804296 20.317831,20.2928932 L18.0249378,18 L12.9835977,18 C12.7263047,14.0909841 9.47412135,11 5.5,11 C4.23590829,11 3.04485894,11.3127315 2,11.8650466 Z M6,7 C5.44771525,7 5,7.44771525 5,8 C5,8.55228475 5.44771525,9 6,9 L15,9 C15.5522847,9 16,8.55228475 16,8 C16,7.44771525 15.5522847,7 15,7 L6,7 Z" fill="#000000"></path>
                </g>
              </svg>
              </span>
              <!--end::Svg Icon-->
              <div class="d-flex flex-column text-dark-75"> 
               <span class="fw-bolder fs-6">Active Req.</span> <span class="fw-bolder fs-3">{{ @$activeJobs }}</span> 
              </div>
      </div>
      <!--end: Item3-->
      <!--begin: Item4-->
      <div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
        <!--begin::Svg-->
        <span class="svg-icon svg-icon-dark-800 svg-icon-3x me-2">
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
          <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
            <rect x="0" y="0" width="24" height="24"></rect>
            <path d="M7,3 L17,3 C19.209139,3 21,4.790861 21,7 C21,9.209139 19.209139,11 17,11 L7,11 C4.790861,11 3,9.209139 3,7 C3,4.790861 4.790861,3 7,3 Z M7,9 C8.1045695,9 9,8.1045695 9,7 C9,5.8954305 8.1045695,5 7,5 C5.8954305,5 5,5.8954305 5,7 C5,8.1045695 5.8954305,9 7,9 Z" fill="#000000"></path>
            <path d="M7,13 L17,13 C19.209139,13 21,14.790861 21,17 C21,19.209139 19.209139,21 17,21 L7,21 C4.790861,21 3,19.209139 3,17 C3,14.790861 4.790861,13 7,13 Z M17,19 C18.1045695,19 19,18.1045695 19,17 C19,15.8954305 18.1045695,15 17,15 C15.8954305,15 15,15.8954305 15,17 C15,18.1045695 15.8954305,19 17,19 Z" fill="#000000" opacity="0.3"></path>
          </g>
          </svg>
          </span>
          <!--end::Svg Icon-->
          <div class="d-flex flex-column text-dark-75"> 
            <span class="fw-bolder fs-6">Founded</span> <span class="fw-bolder fs-3">{{ @$yearAgo }}</span> 
          </div>
      </div>
      <!--end: Item4-->
      <!--begin: Item5-->
      <div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
          <!--begin::Svg-->
          <span class="svg-icon svg-icon-dark-800 svg-icon-3x me-2">
          <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
              <rect x="0" y="0" width="24" height="24"></rect>
              <path d="M12,21 C7.02943725,21 3,16.9705627 3,12 C3,7.02943725 7.02943725,3 12,3 C16.9705627,3 21,7.02943725 21,12 C21,16.9705627 16.9705627,21 12,21 Z M14.1654881,7.35483745 L9.61055177,10.3622525 C9.47921741,10.4489666 9.39637436,10.592455 9.38694497,10.7495509 L9.05991526,16.197949 C9.04337012,16.4735952 9.25341309,16.7104632 9.52905936,16.7270083 C9.63705011,16.7334903 9.74423017,16.7047714 9.83451193,16.6451626 L14.3894482,13.6377475 C14.5207826,13.5510334 14.6036256,13.407545 14.613055,13.2504491 L14.9400847,7.80205104 C14.9566299,7.52640477 14.7465869,7.28953682 14.4709406,7.27299168 C14.3629499,7.26650974 14.2557698,7.29522855 14.1654881,7.35483745 Z" fill="#000000"></path>
            </g>
          </svg>
          </span>
          <!--end::Svg Icon-->
          <div class="d-flex flex-column text-dark-75"> <span class="fw-bolder fs-6">Joined</span> <span class="fw-bolder fs-3">{{ @$joined }}</span> </div>
      </div>
      <!--end: Item5-->
      <!--begin: Item-->
      <div class="d-flex align-items-center flex-lg-fill my-1">
            <!--begin::Svg Icon-->
            <span class="svg-icon svg-icon-dark-800 svg-icon-3x me-2">
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
              <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                <rect x="0" y="0" width="24" height="24"/>
                <path d="M16,15.6315789 L16,12 C16,10.3431458 14.6568542,9 13,9 L6.16183229,9 L6.16183229,5.52631579 C6.16183229,4.13107011 7.29290239,3 8.68814808,3 L20.4776218,3 C21.8728674,3 23.0039375,4.13107011 23.0039375,5.52631579 L23.0039375,13.1052632 L23.0206157,17.786793 C23.0215995,18.0629336 22.7985408,18.2875874 22.5224001,18.2885711 C22.3891754,18.2890457 22.2612702,18.2363324 22.1670655,18.1421277 L19.6565168,15.6315789 L16,15.6315789 Z" fill="#000000"/>
                <path d="M1.98505595,18 L1.98505595,13 C1.98505595,11.8954305 2.88048645,11 3.98505595,11 L11.9850559,11 C13.0896254,11 13.9850559,11.8954305 13.9850559,13 L13.9850559,18 C13.9850559,19.1045695 13.0896254,20 11.9850559,20 L4.10078614,20 L2.85693427,21.1905292 C2.65744295,21.3814685 2.34093638,21.3745358 2.14999706,21.1750444 C2.06092565,21.0819836 2.01120804,20.958136 2.01120804,20.8293182 L2.01120804,18.32426 C1.99400175,18.2187196 1.98505595,18.1104045 1.98505595,18 Z M6.5,14 C6.22385763,14 6,14.2238576 6,14.5 C6,14.7761424 6.22385763,15 6.5,15 L11.5,15 C11.7761424,15 12,14.7761424 12,14.5 C12,14.2238576 11.7761424,14 11.5,14 L6.5,14 Z M9.5,16 C9.22385763,16 9,16.2238576 9,16.5 C9,16.7761424 9.22385763,17 9.5,17 L11.5,17 C11.7761424,17 12,16.7761424 12,16.5 C12,16.2238576 11.7761424,16 11.5,16 L9.5,16 Z" fill="#000000" opacity="0.3"/>
              </g>
            </svg>
            </span>
        <!--end::Svg Icon-->
        <div class="symbol-group symbol-hover">
					 		
          @if(!empty($team))
              @foreach($team as $teamVal)
                @php
                  $name				=	'';
                  $name				=	ucwords($teamVal->name);
                  $logo_path			=	@$teamVal->image; 	
                  $bgTextDecore =  GeneralMethod::icoinText($name);	
                @endphp	
                @if(!empty($logo_path))
                {!! '<div class="symbol symbol1 symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="'.@$name.'"><img alt="Pic" src="'.@$logo_path.'"></div>' !!}
                @else
                <div class="symbol symbol1 symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="{{ @$name }}">
                  
                  <div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip2" data-html="true" title="{{ ucwords(@$name)}}" >
                    <div class="symbol symbol-circle symbol-30px overflow-hidden me-2 border-radius-100" style="float:left">
                    <a href="javascript:void(0);" data-row-id="11" class="show-detail border-radius-100" id="kt_activities3_toggle">
                      <div class="symbol-label fs-8 fs-8 {{ $bgTextDecore}} border-radius-100">{{ substr($name, 0, 1)}}</div>
                    </a>		 
                    </div>
                    </div>

                </div>
                @endif

              @endforeach
          @endif                                 
          @if($team_count > 5)
                {!! '<div class="symbol symbol1 symbol-30 symbol-circle symbol-light" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="List.."><span class="symbol-label symbol-label1 font-weight-bold">5+</span></div>' !!}
          @endif                                  
 
        </div>
      </div>
      <!--end: Item-->
    </div>
    <!--end::sections-->
    <!--end::Details-->
  </div>
</div>
<!--end::Company Details-->
<!--begin::Tabs-->
@php
  $cad_stageCount = count((array)@$interviewStage_sourcing)+count((array)@$interviewStage_screening)+count((array)@$interviewStage_evaluation)+count((array)@$interviewStage_negotiation)+count((array)@$interviewStage_offered)+count((array)@$interviewStage_joined)+count((array)@$interviewStage_reject);
@endphp
<div class="cd-scope">
@if(!@$hide_account_tabs_header)
  <!-- ── TIER 2: SUB-TABS CARD WITH WHITE BACKGROUND (Rule 20 Compliant) ── -->
  <div class="cd-drawer-tabs-card">
    <ul class="cd-drawer-tabs-list nav nav-tabs border-0 flex-nowrap" role="tablist">
      <li class="nav-item" role="presentation">
        <a class="cd-tab-pill-btn nav-link {{ !empty($overview_tab) ? 'active' : '' }}" data-bs-toggle="tab" data-toggle="tab" href="#overview" role="tab" aria-selected="{{ !empty($overview_tab) ? 'true' : 'false' }}" title="Overview">
          <i class="fas fa-house fa-solid"></i>
          <span>Overview</span>
        </a>
      </li>
      <li class="nav-item" role="presentation">
        <a class="cd-tab-pill-btn nav-link" data-bs-toggle="tab" data-toggle="tab" href="#recent_activity" role="tab" aria-selected="false" title="Recent Activity Feed">
          <i class="fas fa-clock-rotate-left fa-solid"></i>
          <span>Activity</span>
        </a>
      </li>
      <li class="nav-item" role="presentation">
        <a class="cd-tab-pill-btn nav-link" data-bs-toggle="tab" data-toggle="tab" href="#interview_Stage" role="tab" aria-selected="false" title="Interview Stages">
          <i class="fas fa-layer-group fa-solid"></i>
          <span>Interview Stages</span>
          <span class="cd-tab-pill-badge" style="{{ $cad_stageCount > 0 ? '' : 'display:none;' }}">{{ $cad_stageCount }}</span>
        </a>
      </li>
      <li class="nav-item" role="presentation">
        <a class="cd-tab-pill-btn nav-link" data-bs-toggle="tab" data-toggle="tab" href="#settings" role="tab" aria-selected="false" title="General Settings">
          <i class="fas fa-gear fa-solid"></i>
          <span>General</span>
        </a>
      </li>
      <li class="nav-item" role="presentation">
        <a class="cd-tab-pill-btn nav-link" data-bs-toggle="tab" data-toggle="tab" href="#notifications" role="tab" aria-selected="false" title="Notification Preferences">
          <i class="fas fa-bell fa-solid"></i>
          <span>Notifications</span>
        </a>
      </li>
      <li class="nav-item" role="presentation">
        <a class="cd-tab-pill-btn nav-link candidate-config-main-tab" data-bs-toggle="tab" data-toggle="tab" href="#offerterms" role="tab" aria-selected="false" title="Candidate Configurations">
          <i class="fas fa-file-contract fa-solid"></i>
          <span>Candidate Configurations</span>
        </a>
      </li>
      <li class="nav-item" role="presentation">
        <a class="cd-tab-pill-btn nav-link" data-bs-toggle="tab" data-toggle="tab" href="#terms" role="tab" aria-selected="false" title="Agency Agreement Terms">
          <i class="fas fa-handshake-angle fa-solid"></i>
          <span>Agency Agreement</span>
        </a>
      </li>
    </ul>
  </div>
@endif
</div>

<div class="card card-custom mx-2 border-0 bg-transparent shadow-none">
<!--Begin::Body-->
<div class="card-body py-0 px-0">
<div class="tab-content {{ @$hide_account_tabs_header ? 'pt-0' : 'pt-2' }}">
<!--begin::Overview Content-->
<div class="tab-pane {{$overview_tab}}" id="overview" role="tabpanel">
  @if(in_array("view_settings", $userPermissions))
  <div class="cd-scope">
    <div class="cd-grid-2col">
      <!-- ── Left Column ── -->
      <div>
        <!-- Card 1: About the Company -->
        <div class="cd-card-panel">
          <div class="cd-card-hdr">
            <div class="cd-card-hdr-left">
              <div class="cd-top-bar-icon" style="width:28px; height:28px; font-size:12px;">
                <i class="fas fa-building fa-solid"></i>
              </div>
              <span class="cd-card-hdr-title">About the Company</span>
            </div>
            @if(!empty($webClean) && $webClean !== '-')
            <a href="{{ str_starts_with($webClean, 'http') ? $webClean : 'https://'.$webClean }}" target="_blank" class="cd-card-hdr-link" title="Visit Official Website">
              {{ preg_replace('#^https?://(www\.)?#i', '', rtrim($webClean, '/')) }} <i class="fas fa-external-link-alt fa-solid" style="font-size:9px;"></i>
            </a>
            @endif
          </div>
          <div class="cd-about-body">
            @if (!empty(trim(strip_tags((string)@$account->description))))
              {!! @$account->description !!}
            @elseif (!empty(trim(strip_tags((string)@$client->description))))
              {!! @$client->description !!}
            @else
              {{ ucwords(@$account->company_name) }} is an established corporate enterprise specializing in {{ @$industries_name ?: 'Staffing & Recruiting Solutions' }}, providing scalable services and talent solutions with a strong market presence.
            @endif
          </div>
        </div>

        <!-- Card 2: Specialties & Core Domains (Dedicated Section per User Requirement) -->
        <div class="cd-card-panel">
          <div class="cd-card-hdr">
            <div class="cd-card-hdr-left">
              <div class="cd-top-bar-icon" style="width:28px; height:28px; font-size:12px; background:#eff6ff; color:#2563eb;">
                <i class="fas fa-tags fa-solid"></i>
              </div>
              <span class="cd-card-hdr-title">Specialties &amp; Core Domains</span>
            </div>
            @php $specCount = count($tagNames ?? []); @endphp
            @if($specCount > 0)
              <span class="badge bg-light-primary text-primary fw-semibold" style="font-size:10px; padding:3px 8px; border-radius: 999px;">
                {{ $specCount }} {{ $specCount === 1 ? 'Specialty' : 'Specialties' }}
              </span>
            @endif
          </div>
          <div class="cd-card-body" style="padding: 12px 16px;">
            @if(!empty($tagNames) && count($tagNames) > 0)
              <div class="cd-specialties-grid">
                @foreach($tagNames as $tg)
                  <div class="cd-specialty-chip">
                    <span class="cd-specialty-bullet"></span>
                    <span class="cd-specialty-text">{{ ucwords(trim($tg)) }}</span>
                  </div>
                @endforeach
              </div>
            @else
              <div class="p-3 text-center text-muted" style="font-size: 11.5px;">
                <i class="fas fa-tags me-1 text-muted"></i> No specialties configured for this company.
              </div>
            @endif
          </div>
        </div>

        <!-- Card 3: Recent Activity (Dynamic Live Timeline) -->
        <div class="cd-card-panel">
          <div class="cd-card-hdr">
            <div class="cd-card-hdr-left">
              <div class="cd-top-bar-icon" style="width:28px; height:28px; font-size:12px; background:#fffbeb; color:#d97706;">
                <i class="fas fa-clock-rotate-left fa-solid"></i>
              </div>
              <span class="cd-card-hdr-title">Recent Activity</span>
            </div>
            <a href="javascript:void(0);" class="cd-card-hdr-link cd-view-all-activity" onclick="$('.cd-tab-pill-btn[href=\'#recent_activity\']').tab('show');" title="View all activity stream">
              View all activity <i class="fas fa-arrow-right fa-solid ms-1" style="font-size:9px;"></i>
            </a>
          </div>
          <div class="cd-timeline-list" id="cdOverviewRecentActivityList">
            <div class="p-4 text-center text-muted" style="font-size: 11.5px;">
              <i class="fas fa-spinner fa-spin me-1 text-primary"></i> Loading live activity feed…
            </div>
          </div>
        </div>

        <!-- Card 3: Office Locations & Registered Address -->
        <div class="cd-card-panel">
          <div class="cd-card-hdr">
            <div class="cd-card-hdr-left">
              <div class="cd-top-bar-icon" style="width:28px; height:28px; font-size:12px; background:#fef2f2; color:#ef4444;">
                <i class="fas fa-location-dot fa-solid"></i>
              </div>
              <span class="cd-card-hdr-title">Office Locations &amp; Registered Address</span>
            </div>
            @php $locCount = count($allLocations ?? []); @endphp
            <span class="badge bg-light-primary text-primary fw-semibold" style="font-size:10px; padding:3px 7px;">
              <i class="fas fa-building me-1"></i>{{ $locCount > 1 ? $locCount . ' Locations' : ($locCount === 1 ? '1 Location' : 'Headquarters') }}
            </span>
          </div>

          <div class="cd-loc-group">
            @if ($locCount > 0)
              @foreach ($allLocations as $idx => $loc)
                @php
                  $lCity = !empty($loc->city_name_resolved) ? $loc->city_name_resolved : (!empty($loc->city_name) ? $loc->city_name : ($loc->city ?? ''));
                  $lState = !empty($loc->state_name) ? $loc->state_name : (!empty($loc->state) ? $loc->state : '');
                  $lCountry = !empty($loc->country_name) ? $loc->country_name : (!empty($loc->country) ? $loc->country : '');
                  $lAddr = trim(!empty($loc->address) ? $loc->address : '');
                  $lPostcode = !empty($loc->postcode) && $loc->postcode !== '0' && $loc->postcode !== 0 ? trim((string)$loc->postcode) : '';
                  $isHq = ($idx === 0);
                  $cityStateParts = array_filter([$lCity ? ucwords(trim($lCity)) : '', $lState ? ucwords(trim($lState)) : '']);
                  $cityStateText = implode(', ', $cityStateParts);
                @endphp
                <div class="cd-loc-card {{ $isHq ? 'is-primary hq' : '' }}">
                  <div class="cd-loc-header">
                    <div class="cd-loc-header-left">
                      <span class="cd-loc-badge {{ $isHq ? 'loc-primary' : 'loc-secondary' }}">
                        <i class="fas fa-map-marker-alt me-1"></i>Location {{ $idx + 1 }}
                      </span>
                      @if($isHq)
                        <span class="cd-loc-hq-tag">HQ</span>
                      @endif
                    </div>
                    @if($lCountry)
                      <span class="cd-loc-country-pill">
                        <i class="fas fa-globe-americas me-1 text-primary" style="font-size:9.5px;"></i>{{ ucwords(trim($lCountry)) }}
                      </span>
                    @endif
                  </div>
                  @if($cityStateText)
                    <div class="cd-loc-city-line">
                      <i class="fas fa-location-dot text-danger" style="font-size:11px; flex-shrink:0;"></i>
                      <span class="cd-loc-city-name">{{ $cityStateText }}</span>
                    </div>
                  @endif
                  @if($lAddr)
                    <div class="cd-loc-street-line">
                      <span>{{ $lAddr }}</span>
                    </div>
                  @endif
                  @if($lPostcode)
                    <div class="cd-loc-meta-line">
                      <span class="cd-loc-meta-chip">
                        <i class="fas fa-hashtag text-muted me-1" style="font-size: 9.5px;"></i>Postal Code: <strong>{{ $lPostcode }}</strong>
                      </span>
                    </div>
                  @endif
                </div>
              @endforeach
            @else
              @php
                $fallbackCityState = trim(ucwords($company_city_name).(($company_city_name && $company_country_name)?', ':'').ucwords($company_country_name), ', ');
                $fallbackAddr = trim((string)@$account->address);
              @endphp
              <div class="cd-loc-card is-primary hq">
                <div class="cd-loc-header">
                  <div class="cd-loc-header-left">
                    <span class="cd-loc-badge loc-primary"><i class="fas fa-map-marker-alt me-1"></i>Location 1</span>
                    <span class="cd-loc-hq-tag">HQ</span>
                  </div>
                  @if($company_country_name)
                    <span class="cd-loc-country-pill"><i class="fas fa-globe-americas me-1 text-primary" style="font-size:9.5px;"></i>{{ ucwords(trim($company_country_name)) }}</span>
                  @endif
                </div>
                @if($fallbackCityState)
                  <div class="cd-loc-city-line">
                    <i class="fas fa-location-dot text-danger" style="font-size:11px; flex-shrink:0;"></i>
                    <span class="cd-loc-city-name">{{ $fallbackCityState }}</span>
                  </div>
                @endif
                @if($fallbackAddr)
                  <div class="cd-loc-street-line">
                    <span>{{ $fallbackAddr }}</span>
                  </div>
                @endif
              </div>
            @endif
          </div>
        </div>
      </div>

      <!-- ── Right Column ── -->
      <div>
        <!-- Card 1: Company Information (God-Level Executive Vertical Listing) -->
        <div class="cd-card-panel">
          <div class="cd-card-hdr">
            <div class="cd-card-hdr-left">
              <div class="cd-top-bar-icon" style="width:28px; height:28px; font-size:12px; background:#f5f3ff; color:#7c3aed;">
                <i class="fas fa-circle-info fa-solid"></i>
              </div>
              <span class="cd-card-hdr-title">Company Information</span>
            </div>
            <span class="badge bg-light-primary text-primary fw-semibold" style="font-size:10px; padding:3px 7px;">
              <i class="fas fa-shield-halved me-1"></i>Verified Entity
            </span>
          </div>

          @php
            $rawDetail = @$client->detail;
            $cDetail = !empty($rawDetail) ? (is_array($rawDetail) ? $rawDetail : json_decode($rawDetail, true)) : [];
            $careerPageUrl = !empty($account->career_page_link) ? $account->career_page_link : ($cDetail['career_page_url'] ?? url('career/' . ($account->sub_domain ?: $account->id)));
            $taxId = trim((string)(@$account->tax_number ?: (@$account->gstin ?: (@$cDetail['tax_id'] ?? ''))));

            $cleanSocialUrl = function($u) {
                if (empty($u) || !is_string($u)) return null;
                $u = trim($u);
                if (in_array(strtolower($u), ['disable', 'enable', 'null', 'undefined', '-', 'none', 'false', '0'], true)) return null;
                if (!preg_match('#^(https?://|[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5})#i', $u)) return null;
                return (str_starts_with($u, 'http://') || str_starts_with($u, 'https://')) ? $u : ('https://' . $u);
            };

            $rawLinkedin = trim((string)(@$cDetail['linkedin_url'] ?: (@$cDetail['linkedin'] ?: (@$account->linkedin !== 'disable' && @$account->linkedin !== 'enable' ? @$account->linkedin : ''))));
            $rawTwitter = trim((string)(@$cDetail['twitter_url'] ?: (@$cDetail['twitter'] ?: '')));
            $rawGithub = trim((string)(@$cDetail['github_url'] ?: (@$cDetail['github'] ?: '')));
            $rawYoutube = trim((string)(@$cDetail['youtube_url'] ?: (@$cDetail['youtube'] ?: '')));
            $rawFacebook = trim((string)(@$cDetail['facebook_url'] ?: (@$cDetail['facebook'] ?: '')));
            $rawInstagram = trim((string)(@$cDetail['instagram_url'] ?: (@$cDetail['instagram'] ?: '')));

            $validLinkedin = $cleanSocialUrl($rawLinkedin);
            $validTwitter = $cleanSocialUrl($rawTwitter);
            $validGithub = $cleanSocialUrl($rawGithub);
            $validYoutube = $cleanSocialUrl($rawYoutube);
            $validFacebook = $cleanSocialUrl($rawFacebook);
            $validInstagram = $cleanSocialUrl($rawInstagram);

            $hasSocialLinks = (!empty($validLinkedin) || !empty($validTwitter) || !empty($validGithub) || !empty($validYoutube) || !empty($validFacebook) || !empty($validInstagram));

            $typeLabel = 'Enterprise Service';
            if (isset($account->industries_type) && !empty($industries_type_arr) && isset($industries_type_arr[$account->industries_type])) {
              $typeLabel = $industries_type_arr[$account->industries_type];
            } elseif (!empty($account->company_type) && !empty($companyType) && isset($companyType[$account->company_type])) {
              $typeLabel = $companyType[$account->company_type];
            }

            $fYear = @$account->founded ?: 2010;
            $currYr = (int)date('Y');
            $vintage = ($currYr - (int)$fYear);
          @endphp

          <div class="cd-vertical-info-list">
            <!-- Website -->
            <div class="cd-vinfo-row">
              <div class="cd-vinfo-key">
                <div class="cd-vinfo-icon blue"><i class="fas fa-globe"></i></div>
                <span class="cd-vinfo-label">Website</span>
              </div>
              <div class="cd-vinfo-val">
                @if (!empty($webClean) && $webClean !== '-')
                  <a href="{{ str_starts_with($webClean, 'http') ? $webClean : 'https://'.$webClean }}" target="_blank" class="cd-vinfo-link">
                    {{ preg_replace('#^https?://(www\.)?#i', '', rtrim($webClean, '/')) }} <i class="fas fa-arrow-up-right-from-square" style="font-size:9px;"></i>
                  </a>
                @else
                  <span class="text-muted">—</span>
                @endif
              </div>
            </div>

            <!-- Careers Portal -->
            @if (!empty($careerPageUrl))
            <div class="cd-vinfo-row">
              <div class="cd-vinfo-key">
                <div class="cd-vinfo-icon green"><i class="fas fa-briefcase"></i></div>
                <span class="cd-vinfo-label">Careers Portal</span>
              </div>
              <div class="cd-vinfo-val">
                @php $careerClean = preg_replace('#^https?://(www\.)?#i', '', rtrim($careerPageUrl, '/')); @endphp
                <a href="{{ str_starts_with($careerPageUrl, 'http') ? $careerPageUrl : 'https://'.$careerPageUrl }}" target="_blank" class="cd-career-pill" title="{{ $careerPageUrl }}">
                  <span>{{ strlen($careerClean) > 28 ? substr($careerClean, 0, 26).'…' : $careerClean }}</span>
                  <i class="fas fa-arrow-up-right-from-square" style="font-size:8px;"></i>
                </a>
              </div>
            </div>
            @endif

            <!-- Tax ID / GSTIN -->
            @if (!empty($taxId))
            <div class="cd-vinfo-row">
              <div class="cd-vinfo-key">
                <div class="cd-vinfo-icon amber"><i class="fas fa-receipt"></i></div>
                <span class="cd-vinfo-label">Tax ID / GSTIN</span>
              </div>
              <div class="cd-vinfo-val">
                <div class="cd-tax-wrap">
                  <span class="cd-tax-code">{{ $taxId }}</span>
                  <button type="button" class="cd-copy-btn" title="Copy Tax ID" onclick="navigator.clipboard.writeText('{{ $taxId }}'); $(this).find('i').removeClass('fa-copy far').addClass('fas fa-check text-success'); setTimeout(() => $(this).find('i').removeClass('fas fa-check text-success').addClass('far fa-copy'), 1500);">
                    <i class="far fa-copy"></i>
                  </button>
                </div>
              </div>
            </div>
            @endif

            <!-- Industry -->
            <div class="cd-vinfo-row">
              <div class="cd-vinfo-key">
                <div class="cd-vinfo-icon purple"><i class="fas fa-industry"></i></div>
                <span class="cd-vinfo-label">Industry</span>
              </div>
              <div class="cd-vinfo-val">
                <span class="fw-semibold text-gray-800">{{ $industries_name ?: 'Staffing & Recruiting Solutions' }}</span>
              </div>
            </div>

            <!-- Company Type -->
            <div class="cd-vinfo-row">
              <div class="cd-vinfo-key">
                <div class="cd-vinfo-icon sky"><i class="fas fa-landmark"></i></div>
                <span class="cd-vinfo-label">Company Type</span>
              </div>
              <div class="cd-vinfo-val">
                <span class="cd-type-badge-pill">{{ $typeLabel }}</span>
              </div>
            </div>

            <!-- Workforce Scale -->
            <div class="cd-vinfo-row">
              <div class="cd-vinfo-key">
                <div class="cd-vinfo-icon emerald"><i class="fas fa-users"></i></div>
                <span class="cd-vinfo-label">Workforce Scale</span>
              </div>
              <div class="cd-vinfo-val">
                <span class="fw-semibold text-gray-800">{{ $cad_size ?: '51 – 200 Employees' }}</span>
              </div>
            </div>

            <!-- Founded Year -->
            <div class="cd-vinfo-row">
              <div class="cd-vinfo-key">
                <div class="cd-vinfo-icon orange"><i class="fas fa-calendar-alt"></i></div>
                <span class="cd-vinfo-label">Founded</span>
              </div>
              <div class="cd-vinfo-val">
                <div>
                  <span class="fw-semibold text-gray-800">{{ $fYear }}</span>
                  @if($vintage > 0)
                    <span class="cd-vintage-chip">({{ $vintage }} yrs active)</span>
                  @endif
                </div>
              </div>
            </div>

            <!-- Headquarters -->
            <div class="cd-vinfo-row">
              <div class="cd-vinfo-key">
                <div class="cd-vinfo-icon red"><i class="fas fa-map-pin"></i></div>
                <span class="cd-vinfo-label">Headquarters</span>
              </div>
              <div class="cd-vinfo-val">
                <span class="fw-semibold text-gray-800">{{ trim(ucwords($company_city_name).(($company_city_name&&$company_country_name)?', ':'').ucwords($company_country_name),', ') ?: '—' }}</span>
              </div>
            </div>

            <!-- Social Channels (Only valid configured URLs displayed per User Requirement) -->
            @if($hasSocialLinks)
            <div class="cd-vinfo-row">
              <div class="cd-vinfo-key">
                <div class="cd-vinfo-icon indigo"><i class="fas fa-share-nodes"></i></div>
                <span class="cd-vinfo-label">Social Channels</span>
              </div>
              <div class="cd-vinfo-val">
                <div class="cd-social-links-row">
                  @if(!empty($validLinkedin))
                    <a href="{{ $validLinkedin }}" target="_blank" class="cd-social-btn linkedin" title="LinkedIn Profile"><i class="fab fa-linkedin-in"></i></a>
                  @endif
                  @if(!empty($validTwitter))
                    <a href="{{ $validTwitter }}" target="_blank" class="cd-social-btn twitter" title="X (Twitter) Profile"><i class="fab fa-x-twitter"></i></a>
                  @endif
                  @if(!empty($validGithub))
                    <a href="{{ $validGithub }}" target="_blank" class="cd-social-btn github" title="GitHub Profile"><i class="fab fa-github"></i></a>
                  @endif
                  @if(!empty($validYoutube))
                    <a href="{{ $validYoutube }}" target="_blank" class="cd-social-btn youtube" title="YouTube Channel"><i class="fab fa-youtube"></i></a>
                  @endif
                  @if(!empty($validFacebook))
                    <a href="{{ $validFacebook }}" target="_blank" class="cd-social-btn facebook" title="Facebook Page"><i class="fab fa-facebook-f"></i></a>
                  @endif
                  @if(!empty($validInstagram))
                    <a href="{{ $validInstagram }}" target="_blank" class="cd-social-btn instagram" title="Instagram Profile"><i class="fab fa-instagram"></i></a>
                  @endif
                </div>
              </div>
            </div>
            @endif
          </div>
        </div>

        <!-- Card 2: Platform Settings (Renamed per User Requirement) -->
        @php
          $cad_cur = (!empty($currencyArr) && is_iterable($currencyArr)) ? optional(collect($currencyArr)->firstWhere('id', @$account->currency)) : optional(null);
          $cad_tf  = (!empty($time_formatArr) && is_iterable($time_formatArr)) ? optional(collect($time_formatArr)->firstWhere('id', @$account->time_format)) : optional(null);
          $cad_lang= (!empty($languagesArr) && is_iterable($languagesArr)) ? optional(collect($languagesArr)->firstWhere('id', @$account->language)) : optional(null);
          $cad_dup = (@$account->app_duplicate == 2) ? 'Overall system'.((@$account->app_duplicate_days)?' · '.$account->app_duplicate_days.' days':'')
                    : ((@$account->app_duplicate == 1) ? 'Job-wise'.((@$account->app_duplicate_days)?' · '.$account->app_duplicate_days.' days':'') : 'Disabled');
        @endphp
        <div class="cd-card-panel">
          <div class="cd-card-hdr">
            <div class="cd-card-hdr-left">
              <div class="cd-top-bar-icon" style="width:28px; height:28px; font-size:12px; background:#eff6ff; color:#2563eb;">
                <i class="fas fa-sliders fa-solid"></i>
              </div>
              <span class="cd-card-hdr-title">Platform Settings</span>
            </div>
          </div>
          <div class="cd-vertical-info-list">
            <div class="cd-vinfo-row">
              <div class="cd-vinfo-key">
                <div class="cd-vinfo-icon slate"><i class="fas fa-copy"></i></div>
                <span class="cd-vinfo-label">Duplicate Status</span>
              </div>
              <div class="cd-vinfo-val"><span class="fw-semibold text-gray-800">{{ $cad_dup }}</span></div>
            </div>
            <div class="cd-vinfo-row">
              <div class="cd-vinfo-key">
                <div class="cd-vinfo-icon sky"><i class="fas fa-clock"></i></div>
                <span class="cd-vinfo-label">Time Zone</span>
              </div>
              <div class="cd-vinfo-val"><span class="fw-semibold text-gray-800">{{ @$account->timezone ?: 'UTC' }}</span></div>
            </div>
            <div class="cd-vinfo-row">
              <div class="cd-vinfo-key">
                <div class="cd-vinfo-icon green"><i class="fas fa-money-bill-wave"></i></div>
                <span class="cd-vinfo-label">Currency</span>
              </div>
              <div class="cd-vinfo-val"><span class="fw-semibold text-gray-800">{{ $cad_cur->code ? $cad_cur->code.' · '.$cad_cur->country : '—' }}</span></div>
            </div>
            <div class="cd-vinfo-row">
              <div class="cd-vinfo-key">
                <div class="cd-vinfo-icon amber"><i class="fas fa-hourglass-half"></i></div>
                <span class="cd-vinfo-label">Time Format</span>
              </div>
              <div class="cd-vinfo-val"><span class="fw-semibold text-gray-800">{{ $cad_tf->name ?: '—' }}</span></div>
            </div>
            <div class="cd-vinfo-row">
              <div class="cd-vinfo-key">
                <div class="cd-vinfo-icon purple"><i class="fas fa-language"></i></div>
                <span class="cd-vinfo-label">Language</span>
              </div>
              <div class="cd-vinfo-val"><span class="fw-semibold text-gray-800">{{ $cad_lang->name ?: '—' }}</span></div>
            </div>
          </div>
        </div>

        <!-- Card 3: Key Administrators & Stakeholders -->
        <div class="cd-card-panel">
          <div class="cd-card-hdr">
            <div class="cd-card-hdr-left">
              <div class="cd-top-bar-icon" style="width:28px; height:28px; font-size:12px; background:#ecfdf5; color:#059669;">
                <i class="fas fa-user-shield fa-solid"></i>
              </div>
              <span class="cd-card-hdr-title">Key Administrators</span>
            </div>
            <span class="badge bg-light-success text-success fw-semibold" style="font-size:10px; padding:3px 7px;">
              {{ count(@$administrators ?? []) }} Active
            </span>
          </div>

          <div>
            @forelse(@$administrators ?? [] as $adm)
              <div class="cd-adm-item">
                <div class="cd-adm-avatar">
                  @if(!empty($adm->image))
                    <img src="{{ $adm->image }}" alt="{{ $adm->name }}">
                  @else
                    {{ strtoupper(substr($adm->name, 0, 1)) }}
                  @endif
                </div>
                <div class="cd-adm-info">
                  <div class="cd-adm-name-row">
                    <span class="cd-adm-name">{{ ucwords($adm->name) }}</span>
                    @if(@$adm->root_user)
                      <span class="cd-adm-pri-badge">Primary</span>
                    @endif
                  </div>
                  <div class="cd-adm-role">{{ @$adm->designation ?: 'Administrator' }}</div>
                  <div class="cd-adm-contacts">
                    <span>{{ $adm->email }}</span>
                    @if(@$adm->mobile) · <span>{{ $adm->mobile }}</span>@endif
                  </div>
                </div>
              </div>
            @empty
              <div class="p-3 text-center text-muted" style="font-size:11.5px;">No administrators found.</div>
            @endforelse
          </div>
        </div>
      </div>
    </div>
  </div>
  @endif
</div>
<!--end::overview Content-->
<!--begin::Recent Activity Content (Company Users Activity Timeline)-->
<div class="tab-pane" id="recent_activity" role="tabpanel">
  @include('service_company.account.components.tab-company-activity')
</div>
<!--end::Recent Activity Content-->
<!--begin::Interview Stage Content-->
<div class="tab-pane ips" id="interview_Stage" role="tabpanel">
  @include('service_company.account.components.tab-interview-stages')
</div>
<!--end::Interview Stage Content-->

<!--begin::Terms & conditions Content-->
<div class="tab-pane atc" id="terms" role="tabpanel">
  @php
    $atcCustom  = !empty(trim(strip_tags((string)@$account->terms)));
    $atcUpdated = @$account->updated_at;
    $aatTerms = @$agencyCommercialTerms ?: (object) [];
    $aatCurrency = optional($currencyArr->firstWhere('id', @$aatTerms->contract_rate_currency));
    $aatCurrencyCode = $aatCurrency->code ?: ([0 => 'INR', 1 => 'EUR', 2 => 'JPY', 3 => 'GBP', 4 => 'USD'][@$aatTerms->contract_rate_currency ?? 4] ?? 'USD');
    $aatFulltimeFeeModel = @$aatTerms->fulltime_fee_model ?: 'percentage';
    $aatFulltimeFixedCurrency = optional($currencyArr->firstWhere('id', @$aatTerms->fulltime_fixed_fee_currency));
    $aatFulltimeFixedCurrencyCode = $aatFulltimeFixedCurrency->code ?: ([0 => 'INR', 1 => 'EUR', 2 => 'JPY', 3 => 'GBP', 4 => 'INR'][@$aatTerms->fulltime_fixed_fee_currency ?? 4] ?? 'INR');
    $aatFulltimeFixedUnit = @$aatTerms->fulltime_fixed_fee_unit ?: 'Per Hire';
    $aatFulltimeFixedAmount = @$aatTerms->fulltime_fixed_fee_amount;
    $aatFulltimeFixedLabel = $aatFulltimeFixedCurrencyCode.' '.number_format((float) ($aatFulltimeFixedAmount ?? 0), 2).' / '.$aatFulltimeFixedUnit;
    $aatFulltimePercentageLabel = number_format((float) (@$aatTerms->fulltime_success_fee_percentage ?? 8.33), 2).'% of Annual CTC';
    $aatFulltimeFeeLabel = $aatFulltimeFeeModel === 'fixed' ? $aatFulltimeFixedLabel : $aatFulltimePercentageLabel;
    $aatContractRateDisplay = @$aatTerms->contract_rate_display ?: 'Depend upon job requirements';
    $aatDefaultExperiencePricing = [
      ['label' => '0 - 2 Years', 'rate' => $aatFulltimeFeeModel === 'fixed' ? ($aatFulltimeFixedAmount ?? '') : (@$aatTerms->fulltime_success_fee_percentage ?? 8.33)],
      ['label' => '3 - 5 Years', 'rate' => $aatFulltimeFeeModel === 'fixed' ? ($aatFulltimeFixedAmount ?? '') : (@$aatTerms->fulltime_success_fee_percentage ?? 8.33)],
      ['label' => '6 - 8 Years', 'rate' => $aatFulltimeFeeModel === 'fixed' ? ($aatFulltimeFixedAmount ?? '') : (@$aatTerms->fulltime_success_fee_percentage ?? 8.33)],
      ['label' => '9+ Years', 'rate' => $aatFulltimeFeeModel === 'fixed' ? ($aatFulltimeFixedAmount ?? '') : (@$aatTerms->fulltime_success_fee_percentage ?? 8.33)],
    ];
    $aatFulltimeExperiencePricing = collect(@$aatTerms->fulltime_experience_pricing ?: $aatDefaultExperiencePricing)->values();
    $aatContractExperiencePricing = collect(@$aatTerms->contract_experience_pricing ?: [
      ['label' => '0 - 2 Years', 'rate' => $aatContractRateDisplay],
      ['label' => '3 - 5 Years', 'rate' => $aatContractRateDisplay],
      ['label' => '6 - 8 Years', 'rate' => $aatContractRateDisplay],
      ['label' => '9+ Years', 'rate' => $aatContractRateDisplay],
    ])->values();
    $aatBuildExperienceRows = function ($rows, $basis) {
      return collect($rows)->map(function ($row) use ($basis) {
        return '<tr><td>'.e(data_get($row, 'label')).'</td><td>'.e(data_get($row, 'rate')).'</td><td>'.e($basis).'</td></tr>';
      })->implode('');
    };
    $aatTableStyle = 'style="width:100%;border-collapse:collapse;margin:10px 0 16px;font-size:12px;"';
    $aatCellStyle = 'style="border:1px solid #dfe6f1;padding:8px 10px;text-align:left;"';
    $aatHeadCellStyle = 'style="border:1px solid #dfe6f1;padding:8px 10px;text-align:left;background:#f7f9fc;font-weight:600;"';
    $aatFulltimeExperienceHtml = '<table '.$aatTableStyle.'><thead><tr><th '.$aatHeadCellStyle.'>Experience Band</th><th '.$aatHeadCellStyle.'>Agency Rate</th><th '.$aatHeadCellStyle.'>Basis</th></tr></thead><tbody>'.$aatBuildExperienceRows($aatFulltimeExperiencePricing, $aatFulltimeFeeModel === 'fixed' ? $aatFulltimeFixedUnit : 'of Annual CTC').'</tbody></table>';
    $aatContractExperienceHtml = '<table '.$aatTableStyle.'><thead><tr><th '.$aatHeadCellStyle.'>Experience Band</th><th '.$aatHeadCellStyle.'>Agency Rate</th><th '.$aatHeadCellStyle.'>Basis</th></tr></thead><tbody>'.$aatBuildExperienceRows($aatContractExperiencePricing, ucfirst(@$aatTerms->contract_billing_model ?: 'monthly').' billing').'</tbody></table>';
    $aatFulltimeCommercialHtml = '<table '.$aatTableStyle.'><tbody><tr><th '.$aatHeadCellStyle.'>Success Fee</th><td '.$aatCellStyle.'>'.e($aatFulltimeFeeLabel).'</td></tr><tr><th '.$aatHeadCellStyle.'>Payment Timeline</th><td '.$aatCellStyle.'>'.e((int) (@$aatTerms->fulltime_payment_terms_days ?? 30).' Days from Invoice').'</td></tr><tr><th '.$aatHeadCellStyle.'>Replacement Period</th><td '.$aatCellStyle.'>'.e((int) (@$aatTerms->fulltime_replacement_period_days ?? 90).' Days').'</td></tr><tr><th '.$aatHeadCellStyle.'>Basis</th><td '.$aatCellStyle.'>Annual Fixed CTC</td></tr></tbody></table>';
    $aatContractCommercialHtml = '<table '.$aatTableStyle.'><tbody><tr><th '.$aatHeadCellStyle.'>Agency Rate</th><td '.$aatCellStyle.'>'.e($aatContractRateDisplay).'</td></tr><tr><th '.$aatHeadCellStyle.'>Billing Cycle</th><td '.$aatCellStyle.'>'.e(@$aatTerms->contract_billing_cycle ?: 'Monthly in Advance').'</td></tr><tr><th '.$aatHeadCellStyle.'>Minimum Duration</th><td '.$aatCellStyle.'>'.e((int) (@$aatTerms->contract_min_duration_months ?? 3).' Months').'</td></tr><tr><th '.$aatHeadCellStyle.'>Replacement Policy</th><td '.$aatCellStyle.'>'.e(@$aatTerms->contract_replacement_policy ?: 'As per Contract Terms').'</td></tr></tbody></table>';
    $aatAgencyCommercialSummaryHtml = '<div class="agency-commercial-summary"><h3>Agency Commercial Terms</h3><h4>Full-Time Hiring</h4>'.$aatFulltimeCommercialHtml.'<h4>Full-Time Experience-Based Pricing</h4>'.$aatFulltimeExperienceHtml.'<h4>Contract Hiring</h4>'.$aatContractCommercialHtml.'<h4>Contract Experience-Based Pricing</h4>'.$aatContractExperienceHtml.'</div>';
    $aatUpdated = @$aatTerms->updated_at ?: $atcUpdated;
    $aatUpdatedBy = @$aatTerms->updated_by_name ?: (optional(auth()->user())->name ?: 'System');
    $aatVersion = @$aatTerms->version ?: '1.0';
    $aatActive = (@$aatTerms->status ?: 'active') === 'active';
    $atcVars = [
      '{TODAY_DATE}'       => "Today's Date",
      '{AGENCY_NAME}'      => 'Agency Name',
      '{AGENCY_ADDRESS}'   => 'Agency Address',
      '{COMPANY_NAME}'     => 'Company Name',
      '{COMPANY_ADDRESS}'  => 'Company Address',
      '{JOB_TITLE}'        => 'Job Title',
      '{SUCCESS_FEE}'      => 'Agency Success Fee',
      '{CONTRACT_RATE}'    => 'Contract Agency Rate',
      '{PAYMENT_TERMS}'    => 'Payment Terms',
      '{REPLACEMENT_DAYS}' => 'Replacement Period',
      '{AGENCY_COMMERCIAL_SUMMARY}' => 'Agency Commercial Summary',
      '{FULLTIME_COMMERCIAL_TABLE}' => 'Full-Time Commercial Table',
      '{FULLTIME_EXPERIENCE_PRICING}' => 'Full-Time Experience Pricing',
      '{CONTRACT_COMMERCIAL_TABLE}' => 'Contract Commercial Table',
      '{CONTRACT_EXPERIENCE_PRICING}' => 'Contract Experience Pricing',
    ];
    $agencyPreviewCompanyAddress = trim(ucwords($company_city_name).(($company_city_name && $company_country_name) ? ', ' : '').ucwords($company_country_name), ', ');
    $agencyPreviewCompanyAddress = $agencyPreviewCompanyAddress ?: (@$account->address ?: 'Tower A, 4th Floor, Sector-135, Noida, UP 201301');
    // Intent: expose a complete agency-agreement variable catalog from data already loaded on this page; no extra page-load queries are introduced.
    $agencyVariableCatalog = [
      ['code' => '{AGENCY_NAME}', 'desc' => 'Agency Name', 'category' => 'Agency Information', 'source' => 'Agency Profile', 'example' => 'TalentVerse Solutions Pvt. Ltd.', 'status' => 'Active'],
      ['code' => '{AGENCY_ADDRESS}', 'desc' => 'Agency Address', 'category' => 'Agency Information', 'source' => 'Agency Profile', 'example' => 'B-403, 4th Floor, Skyline Tech Park, Sector-62, Noida, UP 201309', 'status' => 'Active'],
      ['code' => '{AGENCY_CONTACT}', 'desc' => 'Primary Contact Person', 'category' => 'Agency Information', 'source' => 'Agency Profile', 'example' => 'Rahul Sharma', 'status' => 'Active'],
      ['code' => '{AGENCY_EMAIL}', 'desc' => 'Agency Email', 'category' => 'Agency Information', 'source' => 'Agency Profile', 'example' => 'rahul.sharma@talentverse.in', 'status' => 'Active'],
      ['code' => '{AGENCY_GST}', 'desc' => 'Agency GST Number', 'category' => 'Agency Information', 'source' => 'Agency Profile', 'example' => '09AAECT1234F1Z5', 'status' => 'Active'],
      ['code' => '{AGENCY_PAN}', 'desc' => 'Agency PAN Number', 'category' => 'Agency Information', 'source' => 'Agency Profile', 'example' => 'AAECT1234F', 'status' => 'Active'],
      ['code' => '{COMPANY_NAME}', 'desc' => 'Company / Client Name', 'category' => 'Company Information', 'source' => 'Company Profile', 'example' => @$account->company_name ?: @$account->name ?: '4Bell Technology', 'status' => 'Active'],
      ['code' => '{COMPANY_ADDRESS}', 'desc' => 'Company Address', 'category' => 'Company Information', 'source' => 'Company Profile', 'example' => $agencyPreviewCompanyAddress, 'status' => 'Active'],
      ['code' => '{COMPANY_CONTACT}', 'desc' => 'Company Contact Person', 'category' => 'Company Information', 'source' => 'Company Profile', 'example' => optional(auth()->user())->name ?: 'Vibhanshu Jaiswal', 'status' => 'Active'],
      ['code' => '{COMPANY_EMAIL}', 'desc' => 'Company Email', 'category' => 'Company Information', 'source' => 'Company Profile', 'example' => optional(auth()->user())->email ?: 'company@example.com', 'status' => 'Active'],
      ['code' => '{JOB_TITLE}', 'desc' => 'Job Title', 'category' => 'Job Information', 'source' => 'Job Details', 'example' => 'Senior PHP Developer', 'status' => 'Active'],
      ['code' => '{JOB_ID}', 'desc' => 'Job ID / Reference', 'category' => 'Job Information', 'source' => 'Job Details', 'example' => 'JW/2026/087', 'status' => 'Active'],
      ['code' => '{HIRING_TYPE}', 'desc' => 'Hiring Type', 'category' => 'Job Information', 'source' => 'Job Details', 'example' => 'Full-Time', 'status' => 'Active'],
      ['code' => '{JOINING_DATE}', 'desc' => 'Expected Joining Date', 'category' => 'Job Information', 'source' => 'Job Details', 'example' => '01 Sep 2026', 'status' => 'Active'],
      ['code' => '{FULLTIME_FEE}', 'desc' => 'Full-Time Success Fee', 'category' => 'Commercial Terms', 'source' => 'Commercial Setup', 'example' => $aatFulltimeFeeLabel, 'status' => 'Active'],
      ['code' => '{FULLTIME_PAYMENT_DAYS}', 'desc' => 'Full-Time Payment Timeline', 'category' => 'Commercial Terms', 'source' => 'Commercial Setup', 'example' => (int) (@$aatTerms->fulltime_payment_terms_days ?? 30).' Days from Invoice', 'status' => 'Active'],
      ['code' => '{FULLTIME_REPLACEMENT_DAYS}', 'desc' => 'Full-Time Replacement Period', 'category' => 'Commercial Terms', 'source' => 'Commercial Setup', 'example' => (int) (@$aatTerms->fulltime_replacement_period_days ?? 90).' Days', 'status' => 'Active'],
      ['code' => '{FULLTIME_COMMERCIAL_TABLE}', 'desc' => 'Full-Time Commercial Table', 'category' => 'Commercial Terms', 'source' => 'Commercial Setup', 'example' => $aatFulltimeCommercialHtml, 'status' => 'Active'],
      ['code' => '{FULLTIME_EXPERIENCE_PRICING}', 'desc' => 'Full-Time Experience Pricing', 'category' => 'Commercial Terms', 'source' => 'Commercial Setup', 'example' => $aatFulltimeExperienceHtml, 'status' => 'Active'],
      ['code' => '{CONTRACT_RATE}', 'desc' => 'Contract Agency Rate', 'category' => 'Commercial Terms', 'source' => 'Commercial Setup', 'example' => $aatContractRateDisplay, 'status' => 'Active'],
      ['code' => '{CONTRACT_MIN_DURATION}', 'desc' => 'Minimum Contract Duration', 'category' => 'Commercial Terms', 'source' => 'Commercial Setup', 'example' => (int) (@$aatTerms->contract_min_duration_months ?? 3).' Months', 'status' => 'Active'],
      ['code' => '{CONTRACT_PAYMENT_DAYS}', 'desc' => 'Contract Payment Timeline', 'category' => 'Commercial Terms', 'source' => 'Commercial Setup', 'example' => (int) (@$aatTerms->contract_payment_terms_days ?? 30).' Days from Invoice', 'status' => 'Active'],
      ['code' => '{CONTRACT_COMMERCIAL_TABLE}', 'desc' => 'Contract Commercial Table', 'category' => 'Commercial Terms', 'source' => 'Commercial Setup', 'example' => $aatContractCommercialHtml, 'status' => 'Active'],
      ['code' => '{CONTRACT_EXPERIENCE_PRICING}', 'desc' => 'Contract Experience Pricing', 'category' => 'Commercial Terms', 'source' => 'Commercial Setup', 'example' => $aatContractExperienceHtml, 'status' => 'Active'],
      ['code' => '{AGENCY_COMMERCIAL_SUMMARY}', 'desc' => 'Complete Agency Commercial Summary', 'category' => 'Commercial Terms', 'source' => 'Commercial Setup', 'example' => $aatAgencyCommercialSummaryHtml, 'status' => 'Active'],
      ['code' => '{TODAY_DATE}', 'desc' => "Today's Date", 'category' => 'Agreement System', 'source' => 'System', 'example' => date('d M Y'), 'status' => 'Active'],
      ['code' => '{AGREEMENT_VERSION}', 'desc' => 'Agreement Version', 'category' => 'Agreement System', 'source' => 'System', 'example' => 'Version '.$aatVersion, 'status' => 'Active'],
      ['code' => '{EFFECTIVE_DATE}', 'desc' => 'Effective Date', 'category' => 'Agreement System', 'source' => 'System', 'example' => date('d M Y', strtotime(@$aatUpdated ?: now())), 'status' => 'Active'],
    ];
    $agencyVariableCategories = collect($agencyVariableCatalog)->groupBy('category')->map->count();
    $agencyPreviewValues = collect($agencyVariableCatalog)->pluck('example', 'code')->all();
    $agencyPreviewValues['{SUCCESS_FEE}'] = $agencyPreviewValues['{FULLTIME_FEE}'] ?? '8.33% of Annual CTC';
    $agencyPreviewValues['{PAYMENT_TERMS}'] = $agencyPreviewValues['{FULLTIME_PAYMENT_DAYS}'] ?? '30 Days from Invoice';
    $agencyPreviewValues['{REPLACEMENT_DAYS}'] = $agencyPreviewValues['{FULLTIME_REPLACEMENT_DAYS}'] ?? '90 Days';
    $agencyPreviewAgreementHtml = strtr((string) ((@$account->terms) ? @$account->terms : $global_settings->terms), $agencyPreviewValues);
  @endphp
  <div class="agency-agreement">
    <!-- Intent: dynamic agency-facing agreement UI; client Services/Commercials remain separate. -->
    <div class="aat-hero">
      <div class="aat-title-wrap">
        <span class="aat-hero-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 3h9l3 3v15H6z"/><path d="M14 3v4h4M9 12h6M9 16h4"/><path d="m5 8-2 2 2 2"/></svg></span>
        <div>
          <h3 class="aat-title">Agency Agreement &amp; Commercial Terms</h3>
          <p class="aat-sub">Define agency-facing commercial terms and the standard agreement that governs agency participation and candidate submissions.</p>
        </div>
      </div>
      <div class="aat-actions">
        <a href="javascript:;" class="aat-btn" id="agencyAgreementPreview"><svg viewBox="0 0 24 24"><path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>Preview as Agency</a>
        <a href="javascript:;" id="editBtn" class="aat-btn primary"><svg viewBox="0 0 24 24"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>Edit Agreement</a>
      </div>
    </div>
    <div class="apv-overlay" id="agencyPreviewDrawerOverlay"></div>
    <div class="apv-drawer" id="agencyPreviewDrawer" aria-hidden="true">
      <!-- Intent: render the agency-facing preview from existing agreement HTML and variable values without adding a server-side preview flow. -->
      <div class="apv-head">
        <div class="apv-title">
          <span class="aat-hero-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="3" width="16" height="18" rx="2"/><path d="M8 8h8M8 12h4"/><path d="M2 12s4-5 10-5 10 5 10 5-4 5-10 5S2 12 2 12Z"/></svg></span>
          <div><h3>Preview Agreement as Agency</h3><p>This is how the agreement will look for the agency after variables are populated.</p></div>
        </div>
        <div class="apv-actions">
          <button type="button" class="aat-btn primary" id="agencyPreviewDownloadPdf"><svg viewBox="0 0 24 24"><path d="M12 3v12"/><path d="m7 10 5 5 5-5"/><path d="M5 21h14"/></svg>Download PDF</button>
          <button type="button" class="aga-close" id="closeAgencyPreviewDrawer" aria-label="Close preview">&times;</button>
        </div>
      </div>
      <div class="apv-body">
        <main class="apv-card apv-main">
          <div class="apv-toolbar">
            <div class="apv-tabs"><button type="button" class="apv-tab active" data-apv-view="document">Document Preview</button><button type="button" class="apv-tab" data-apv-view="plain">Plain Text View</button></div>
            <div class="apv-tools"><select class="apv-select" style="width:110px;"><option>Fit Width</option><option>100%</option></select><button type="button" class="aat-btn" id="agencyPreviewPrint">Print</button><button type="button" class="aat-btn">⛶</button></div>
          </div>
          <div class="apv-alert">This preview shows exactly how the agreement will appear to the selected agency.</div>
          <div class="apv-document" id="agencyPreviewDocument">
            <div class="apv-party-intro">
              <h2>AGENCY TERMS &amp; CONDITIONS</h2>
              <p class="apv-effective">This Agreement is made and executed on <b>{{ $agencyPreviewValues['{TODAY_DATE}'] }}</b> (<b>“Effective Date”</b>) by and between:</p>
              <div class="apv-party-grid">
                <div class="apv-party-box">
                  <div class="apv-party-name">{{ $agencyPreviewValues['{COMPANY_NAME}'] }}</div>
                  <div class="apv-party-address">{{ $agencyPreviewValues['{COMPANY_ADDRESS}'] }}</div>
                  <div class="apv-party-role">(hereinafter referred to as the <b>“Client”</b>)</div>
                </div>
                <div class="apv-party-and">AND</div>
                <div class="apv-party-box">
                  <div class="apv-party-name">{{ $agencyPreviewValues['{AGENCY_NAME}'] }}</div>
                  <div class="apv-party-address">{{ $agencyPreviewValues['{AGENCY_ADDRESS}'] }}</div>
                  <div class="apv-party-role">(hereinafter referred to as the <b>“Agency”</b>)</div>
                </div>
              </div>
              <p class="apv-party-closing">The Client and the Agency are hereinafter collectively referred to as the <b>“Parties”</b> and individually as a <b>“Party”</b>.</p>
            </div>
            {!! $agencyPreviewAgreementHtml !!}
          </div>
          <pre class="apv-document apv-plain" id="agencyPreviewPlain">{{ trim(strip_tags($agencyPreviewAgreementHtml)) }}</pre>
        </main>
        <aside>
          <div class="apv-card apv-pad">
            <h4>Agreement Summary</h4>
            <div class="apv-summary-list">
              <div class="apv-summary-row"><span>Type</span><b>Master Agreement</b></div>
              <div class="apv-summary-row"><span>Effective From</span><b>{{ date('d M Y') }}</b></div>
              <div class="apv-summary-row"><span>Agreement Duration</span><b>{{ (int) (@$aatTerms->agreement_duration_months ?? 12) }} Months</b></div>
              <div class="apv-summary-row"><span>Auto Renewal</span><b>{{ (@$aatTerms->auto_renewal ?? 1) ? 'Yes' : 'No' }}</b></div>
              <div class="apv-summary-row"><span>Applicable To</span><b>{{ @$aatTerms->applies_to ?: 'All Agencies' }}</b></div>
            </div>
          </div>
          <div class="apv-card apv-pad" style="margin-top:14px;">
            <h4>Commercial Summary</h4>
            <div class="apv-commercial"><h5>Full-Time Hiring</h5><div class="apv-summary-list"><div class="apv-summary-row"><span>Success Fee</span><b>{{ $agencyPreviewValues['{FULLTIME_FEE}'] }}</b></div><div class="apv-summary-row"><span>Payment Timeline</span><b>{{ $agencyPreviewValues['{FULLTIME_PAYMENT_DAYS}'] }}</b></div><div class="apv-summary-row"><span>Replacement Period</span><b>{{ $agencyPreviewValues['{FULLTIME_REPLACEMENT_DAYS}'] }}</b></div><div class="apv-summary-row"><span>Basis</span><b>Annual Fixed CTC</b></div></div></div>
            <div class="apv-commercial orange"><h5>Contract Hiring</h5><div class="apv-summary-list"><div class="apv-summary-row"><span>Agency Rate</span><b>{{ $agencyPreviewValues['{CONTRACT_RATE}'] }}</b></div><div class="apv-summary-row"><span>Billing Cycle</span><b>{{ @$aatTerms->contract_billing_cycle ?: 'Monthly in Advance' }}</b></div><div class="apv-summary-row"><span>Minimum Duration</span><b>{{ $agencyPreviewValues['{CONTRACT_MIN_DURATION}'] }}</b></div><div class="apv-summary-row"><span>Replacement</span><b>{{ @$aatTerms->contract_replacement_policy ?: 'As per Contract Terms' }}</b></div></div></div>
          </div>
        </aside>
      </div>
      <div class="apv-info"><span>This is how the agreement will be presented to the agency. Agencies must accept this agreement before they can submit candidates.</span></div>
    </div>

    <div class="aat-card">
      <div class="aat-sec-head">
        <div class="aat-sec-title"><span class="aat-sec-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3v18M17 7H9.5a3.5 3.5 0 0 0 0 7H14a3.5 3.5 0 0 1 0 7H6"/></svg></span><div><div class="aat-sec-t">1. Commercial Terms for Agencies</div><div class="aat-sec-s">Default agency commercial structure reflected in the agreement and marketplace job terms.</div></div></div>
        <span></span>
      </div>
      <div class="aat-commercial-grid">
        <div class="aat-commercial purple">
          <div class="aat-service-head"><div class="aat-service-title"><span class="aat-avatar"><svg viewBox="0 0 24 24"><circle cx="12" cy="7" r="4"/><path d="M4 21v-2a8 8 0 0 1 16 0v2"/></svg></span>Full-Time Hiring</div><button type="button" class="aat-tag aat-edit-pill" data-agency-commercial-open="fulltime">Edit Fulltime Commercials</button></div>
          <div class="aat-rows">
            <div class="aat-row"><span class="label">Success Fee</span><span class="value">{{ $aatFulltimeFeeLabel }}</span></div>
            <div class="aat-row"><span class="label">Payment Timeline</span><span class="value">{{ (int) (@$aatTerms->fulltime_payment_terms_days ?? 30) }} Days from Invoice</span></div>
            <div class="aat-row"><span class="label">Replacement Period</span><span class="value">{{ (int) (@$aatTerms->fulltime_replacement_period_days ?? 90) }} Days</span></div>
            <div class="aat-row"><span class="label">Basis</span><span class="value">Annual Fixed CTC</span></div>
          </div>
          <div class="aat-note">Fee is applicable on confirmed joiners only.</div>
        </div>
        <div class="aat-commercial orange">
          <div class="aat-service-head"><div class="aat-service-title orange"><span class="aat-avatar orange"><svg viewBox="0 0 24 24"><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg></span>Contract Hiring</div><button type="button" class="aat-tag orange aat-edit-pill" data-agency-commercial-open="contract">Edit Contract Commercials</button></div>
          <div class="aat-rows">
            <div class="aat-row"><span class="label">Billing Model</span><span class="value">{{ ucfirst(@$aatTerms->contract_billing_model ?: 'monthly') }} Rate</span></div>
            <div class="aat-row"><span class="label">Agency Rate</span><span class="value">{{ $aatContractRateDisplay }}</span></div>
            <div class="aat-row"><span class="label">Payment Timeline</span><span class="value">{{ (int) (@$aatTerms->contract_payment_terms_days ?? 30) }} Days from Invoice</span></div>
            <div class="aat-row"><span class="label">Minimum Contract Duration</span><span class="value">{{ (int) (@$aatTerms->contract_min_duration_months ?? 3) }} Months</span></div>
            <div class="aat-row"><span class="label">Replacement Policy</span><span class="value">{{ @$aatTerms->contract_replacement_policy ?: 'As per Contract Terms' }}</span></div>
          </div>
          <div class="aat-note orange">Rates may vary job-wise if custom terms are defined.</div>
        </div>
      </div>
      <div class="aat-drawer-overlay" id="agencyCommercialDrawerOverlay"></div>
      @php
        // Derived defaults — kept here at the include site so the partial stays pure
        // (the previous inline drawer body computed these inline; the agency_commercials_drawer
        // partial expects them as already-resolved values).
        $defaultTenantCurrencyKey = \App\Helper\GeneralMethod::defaultCurrencyKey();
        $aatFulltimeDefaultRate = (float) (@$aatTerms->fulltime_success_fee_percentage ?? 8.33);
        $aatFixedFeeCurrency = (int) (@$aatTerms->fulltime_fixed_fee_currency ?? $defaultTenantCurrencyKey);
        $aatGeneralCurrency = (int) (@$aatTerms->general_currency ?? $defaultTenantCurrencyKey);
        $aatPaymentTerms = (int) (@$aatTerms->fulltime_payment_terms_days ?? 30);
        $aatReplacementPeriodDays = (int) (@$aatTerms->fulltime_replacement_period_days ?? 90);
        $aatFulltimePaymentTermsDays = (int) (@$aatTerms->fulltime_payment_terms_days ?? 30);
        $aatContractPaymentTermsDays = (int) (@$aatTerms->contract_payment_terms_days ?? 30);
        $aatContractMinDuration = (int) (@$aatTerms->contract_min_duration_months ?? 3);
        $aatFixedRate = (float) (@$aatTerms->fulltime_fixed_fee_amount ?? 0);
        $aatExperienceBands = \Illuminate\Support\Facades\Cache::get('commercial_experience_bands:v1');
        if (!collect($aatExperienceBands)->isEmpty()) {
            $aatExperienceBands = collect($aatExperienceBands)->map(function ($band) {
                return [
                    'label' => (string) $band->label,
                    'indexes' => range((int) $band->min_experience, (int) $band->max_experience),
                ];
            })->values()->all();
        } else {
            $aatExperienceBands = [
                ['label' => '0 - 2 Years', 'indexes' => range(0, 2)],
                ['label' => '3 - 5 Years', 'indexes' => range(3, 5)],
                ['label' => '6 - 8 Years', 'indexes' => range(6, 8)],
                ['label' => '9+ Years', 'indexes' => range(9, 30)],
            ];
        }
        $aatPartialVars = [
          'aatTerms' => $aatTerms,
          'aatActive' => $aatActive,
          'aatVersion' => $aatVersion,
          'aatFulltimeFeeModel' => $aatFulltimeFeeModel,
          'aatFulltimeDefaultRate' => $aatFulltimeDefaultRate,
          'aatFulltimeFixedUnit' => $aatFulltimeFixedUnit,
          'aatFulltimeFixedLabel' => $aatFulltimeFixedLabel,
          'aatFulltimeFixedAmount' => $aatFulltimeFixedAmount,
          'aatFulltimePercentageLabel' => $aatFulltimePercentageLabel,
          'aatFulltimeFeeLabel' => $aatFulltimeFeeLabel,
          'aatFulltimeExperiencePricing' => $aatFulltimeExperiencePricing,
          'aatContractExperiencePricing' => $aatContractExperiencePricing,
          'aatContractRateDisplay' => $aatContractRateDisplay,
          'aatFixedFeeCurrency' => $aatFixedFeeCurrency,
          'aatGeneralCurrency' => $aatGeneralCurrency,
          'aatPaymentTerms' => $aatPaymentTerms,
          'aatReplacementPeriodDays' => $aatReplacementPeriodDays,
          'aatFulltimePaymentTermsDays' => $aatFulltimePaymentTermsDays,
          'aatContractPaymentTermsDays' => $aatContractPaymentTermsDays,
          'aatContractMinDuration' => $aatContractMinDuration,
          'aatFixedRate' => $aatFixedRate,
          'aatExperienceBands' => $aatExperienceBands,
        ];
      @endphp
      {{-- Agency commercials drawer body lives in its own partial so client commercials and agency commercials stay separate. Saved through AgencyCommercialTermsService into the dedicated agency_commercial_terms table (one row per company_id). --}}
      @include('service_company.account.agency_commercials_drawer', $aatPartialVars)
    </div>

    <div class="aat-two-col">
      <div class="aat-card">
        <div class="aat-sec-head"><div class="aat-sec-title"><span class="aat-sec-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3l8 4v6c0 5-3.5 8-8 9-4.5-1-8-4-8-9V7z"/></svg></span><div><div class="aat-sec-t">2. Agreement Rules</div><div class="aat-sec-s">Define when and how this agreement applies.</div></div></div></div>
        <div class="aat-rule-grid">
          <div class="aat-rule"><span class="k">Applies To</span><span class="v">{{ @$aatTerms->applies_to ?: 'All Agencies' }}</span></div>
          <div class="aat-rule"><span class="k">Acceptance Required</span><span class="v aat-yes">{{ (@$aatTerms->acceptance_required ?? 1) ? 'Yes' : 'No' }}</span></div>
          <div class="aat-rule"><span class="k">Acceptance Trigger</span><span class="v">{{ @$aatTerms->acceptance_trigger ?: 'Before Candidate Submission' }}</span></div>
          <div class="aat-rule"><span class="k">Submission Before Acceptance</span><span class="v {{ (@$aatTerms->submission_before_acceptance ?? 0) ? 'aat-yes' : 'aat-no' }}">{{ (@$aatTerms->submission_before_acceptance ?? 0) ? 'Yes' : 'No' }}</span></div>
          <div class="aat-rule"><span class="k">Effective From</span><span class="v">{{ @$aatTerms->effective_from ?: 'Agency Acceptance Date' }}</span></div>
          <div class="aat-rule"><span class="k">Agreement Duration</span><span class="v">{{ (int) (@$aatTerms->agreement_duration_months ?? 12) }} Months</span></div>
          <div class="aat-rule"><span class="k">Auto Renewal</span><span class="v aat-yes">{{ (@$aatTerms->auto_renewal ?? 1) ? 'Yes' : 'No' }}</span></div>
          <div class="aat-rule"><span class="k">Agency Can Reject</span><span class="v aat-yes">{{ (@$aatTerms->agency_can_reject ?? 1) ? 'Yes' : 'No' }}</span></div>
        </div>
      </div>
      <div class="aat-card">
        <div class="aat-sec-head"><div class="aat-sec-title"><span class="aat-sec-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/></svg></span><div><div class="aat-sec-t">3. Acceptance Workflow</div><div class="aat-sec-s">How this agreement flows in the system.</div></div></div></div>
        <div class="aat-flow">
          <div class="aat-flow-step"><span class="aat-flow-icon"><svg viewBox="0 0 24 24"><rect x="3" y="6" width="18" height="15" rx="2"/><path d="M8 6V4h8v2"/></svg></span><div class="aat-flow-n">1</div><div class="aat-flow-t">Job Assigned</div><div class="aat-flow-s">Job is assigned to the agency.</div></div>
          <div class="aat-flow-step"><span class="aat-flow-icon"><svg viewBox="0 0 24 24"><path d="M6 3h9l3 3v15H6z"/><path d="M9 13h6M9 17h4"/></svg></span><div class="aat-flow-n">2</div><div class="aat-flow-t">T&amp;C Shared</div><div class="aat-flow-s">Agreement is shared with agency.</div></div>
          <div class="aat-flow-step"><span class="aat-flow-icon"><svg viewBox="0 0 24 24"><path d="M12 3l8 4v6c0 5-3.5 8-8 9-4.5-1-8-4-8-9V7z"/><path d="m9 12 2 2 4-4"/></svg></span><div class="aat-flow-n">3</div><div class="aat-flow-t">Agency Accepts</div><div class="aat-flow-s">Agency reviews and accepts the agreement.</div></div>
          <div class="aat-flow-step"><span class="aat-flow-icon"><svg viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-8 0v2"/><circle cx="12" cy="7" r="4"/><path d="M20 8v6M23 11h-6"/></svg></span><div class="aat-flow-n">4</div><div class="aat-flow-t">Submission Enabled</div><div class="aat-flow-s">Candidate submission is enabled.</div></div>
        </div>
        <div class="aat-note orange">Agencies cannot submit candidates until applicable Terms &amp; Conditions are accepted.</div>
      </div>
    </div>

    <div class="aat-card">
      <div class="aat-sec-head"><div class="aat-sec-title"><span class="aat-sec-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16v16H4z"/><path d="M8 8h8M8 12h5"/></svg></span><div><div class="aat-sec-t">4. Dynamic Variables</div><div class="aat-sec-s">These variables are automatically replaced with actual values.</div></div></div><button type="button" class="aat-btn" id="openAgencyVariablesDrawer"><svg viewBox="0 0 24 24"><path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>View All Variables</button></div>
      <div class="aat-vars">
        @foreach($atcVars as $code => $desc)
          <div class="aat-var"><code>{{ $code }}</code><span>{{ $desc }}</span></div>
        @endforeach
      </div>
      <div class="avd-overlay" id="agencyVariablesDrawerOverlay"></div>
      <div class="avd-drawer" id="agencyVariablesDrawer" aria-hidden="true">
        <!-- Intent: provide a read-only, searchable agency variable management surface without adding write-side business logic. -->
        <div class="avd-head">
          <div class="avd-title">
            <span class="aat-hero-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 3h9l3 3v15H6z"/><path d="M14 3v4h4"/><path d="m9 13 2 2 4-4"/></svg></span>
            <div><h3>All Variables</h3><p>Manage dynamic variables used in the agreement. These placeholders will be automatically replaced with actual values.</p></div>
          </div>
          <div class="avd-actions">
            <button type="button" class="aga-close" id="closeAgencyVariablesDrawer" aria-label="Close variables drawer">&times;</button>
          </div>
        </div>
        <div class="avd-body">
          <main class="avd-card">
            <div class="avd-filter">
              <input type="text" class="avd-input" id="agencyVariableSearch" placeholder="Search variables by name or description...">
              <select class="avd-select" id="agencyVariableCategoryFilter"><option value="all">All Categories</option>@foreach($agencyVariableCategories as $categoryName => $categoryCount)<option value="{{ $categoryName }}">{{ $categoryName }}</option>@endforeach</select>
              <select class="avd-select" id="agencyVariableSourceFilter"><option value="all">All Sources</option><option>Agency Profile</option><option>Company Profile</option><option>Job Details</option><option>Commercial Setup</option><option>System</option></select>
              <label class="avd-check"><input type="checkbox" id="agencyVariableSystemFilter" checked> Show System Variables</label>
            </div>
            <div class="avd-table-wrap">
              <table class="avd-table" id="agencyVariablesTable">
                <thead><tr><th>VARIABLE</th><th>DESCRIPTION</th><th>CATEGORY</th><th>SOURCE</th><th>EXAMPLE VALUE</th><th>STATUS</th><th>ACTIONS</th></tr></thead>
                <tbody>
                  @foreach($agencyVariableCatalog as $variableRow)
                    @php
                      $categoryClass = str_contains($variableRow['category'], 'Agency') ? 'agency' : (str_contains($variableRow['category'], 'Company') ? 'company' : (str_contains($variableRow['category'], 'Job') ? 'job' : (str_contains($variableRow['category'], 'Commercial') ? 'commercial' : 'system')));
                    @endphp
                    <tr data-avd-row data-code="{{ $variableRow['code'] }}" data-desc="{{ strtolower($variableRow['desc'].' '.$variableRow['code'].' '.strip_tags((string) $variableRow['example'])) }}" data-category="{{ $variableRow['category'] }}" data-source="{{ $variableRow['source'] }}">
                      <td><span class="avd-code">{{ $variableRow['code'] }}</span></td>
                      <td>{{ $variableRow['desc'] }}</td>
                      <td><span class="avd-badge {{ $categoryClass }}">{{ $variableRow['category'] }}</span></td>
                      <td>{{ $variableRow['source'] }}</td>
                      <td>{{ \Illuminate\Support\Str::limit(strip_tags((string) $variableRow['example']), 90) }}</td>
                      <td><span class="avd-status">{{ $variableRow['status'] }}</span></td>
                      <td><span class="avd-row-actions"><button type="button" class="avd-copy" title="Copy variable">⧉</button><button type="button" class="avd-insert" title="Insert in agreement">✎</button></span></td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
              <div class="avd-empty" id="agencyVariablesEmpty">No variables matched your filters.</div>
            </div>
            <div class="avd-foot"><span id="agencyVariablesShowing">Showing 1 to {{ min(8, count($agencyVariableCatalog)) }} of {{ count($agencyVariableCatalog) }} variables</span><div class="avd-pages" id="agencyVariablesPagination"></div></div>
          </main>
        </div>
        <div class="avd-info"><div><b>How Variables Work</b><br>Variables are placeholders enclosed in { } braces. They are automatically replaced with actual values when the agreement is shown to agencies.<br>Example: <b>{AGENCY_NAME}</b> will be shown as “TalentVerse Solutions Pvt. Ltd.”</div><button type="button" class="aga-close" id="closeAgencyVariablesInfo" aria-label="Hide info">&times;</button></div>
      </div>
    </div>

    <div class="aat-card aat-editor-card">
      <div class="aat-editor-toolbar">
        <div class="aat-sec-title"><span class="aat-sec-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 3h12v18H6z"/><path d="M9 8h6M9 12h6M9 16h4"/></svg></span><div><div class="aat-sec-t">5. Agreement Content</div><div class="aat-sec-s">Edit the terms and conditions that will be shared with agencies.</div></div></div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;"><a href="javascript:;" id="setToDefaultCompanyTermsTop" class="aat-btn setToDefaultCompanyTermsTop">Load Default</a><a href="javascript:;" id="updateCompanyTermsTop" class="aat-btn primary updateCompanyTermsTop">Save Agreement</a></div>
      </div>
      <form id="terms_content" name="terms_content" class="form fv-plugins-bootstrap5 fv-plugins-framework" novalidate="novalidate" method="post">
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <input type="hidden" name="pageterms" id="pageterms" value="terms" />
        @csrf
        @method('POST')
        <div class="atc-editor view-terms" id="viewTerms"> {!! (@$account->terms) ? @$account->terms :  $global_settings->terms !!} </div>
        <div class="aga-drawer-overlay" id="agencyAgreementDrawerOverlay"></div>
        <div class="edit-terms aga-drawer" id="editTerms" style="display:none;" aria-hidden="true">
          <!-- Intent: present the existing company terms editor in an agency-focused drawer while reusing the current save/reset business flow. -->
          <div class="aga-head">
            <div class="aga-title-wrap">
              <span class="aat-hero-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 3h9l3 3v15H6z"/><path d="M14 3v4h4M9 12h6M9 16h4"/><path d="m12 18 4-4"/></svg></span>
              <div>
                <h3>Edit Agreement</h3>
                <p>Create and manage the master agreement that will be shared with agencies.</p>
              </div>
            </div>
            <div class="aga-head-actions">
              <button type="button" class="aat-btn" id="agencyAgreementPreviewDrawer"><svg viewBox="0 0 24 24"><path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>Preview as Agency</button>
              <button type="button" class="aat-btn primary updateCompanyTermsTop"><svg viewBox="0 0 24 24"><path d="m20 6-11 11-5-5"/></svg>Save &amp; Publish</button>
              <button type="button" class="aga-close" id="closeAgencyAgreementDrawer" aria-label="Close agreement editor">&times;</button>
            </div>
          </div>
          <div class="aga-shell">
            <main class="aga-main">
              <div class="aga-toolbar">
                <select><option>Heading 2</option><option>Paragraph</option></select>
                <select><option>Poppins</option></select>
                <select><option>14</option><option>13</option><option>12</option></select>
                <button type="button">B</button><button type="button"><i>I</i></button><button type="button"><u>U</u></button><button type="button">A</button><button type="button">•</button><button type="button">≡</button><button type="button">☷</button><button type="button">⌁</button><button type="button">↶</button><button type="button">↷</button>
              </div>
              <div class="aga-editor-wrap">
                <div class="aga-editor-card">
                  <textarea id="kt_docs_tinymce_hidden" name="kt_docs_tinymce_hidden" class="tox-target">{!! (@$account->terms) ? @$account->terms :  $global_settings->terms !!}</textarea>
                  <div class="aga-editor-foot"><span>Words: <b id="agaWordCount">--</b> &nbsp; Characters: <b id="agaCharCount">--</b></span><span>Saved 2 minutes ago ✓</span></div>
                </div>
              </div>
            </main>
            <aside class="aga-right">
              <h4>Insert Variables</h4>
              <p style="margin:0 0 12px;color:#53617f;font-size:11px;">Click or drag variables to insert in the content.</p>
              <div class="aga-var-tabs"><span class="active">All Variables</span></div>
              <input type="text" class="aga-search" placeholder="Search variables...">
              <div class="aga-var-list">
                @foreach($atcVars as $code => $desc)
                  <div class="aga-var-item"><code>{{ $code }}</code><span>{{ str_contains($code, 'AGENCY') ? 'Agency' : (str_contains($code, 'COMPANY') ? 'Company' : 'System') }}</span></div>
                @endforeach
              </div>
              <div class="aga-card">
                <h4>Agreement Metadata</h4>
                <div class="aga-meta-list">
                  <div class="aga-meta-row"><span>Agreement Type</span><b>Master Agreement</b></div>
                  <div class="aga-meta-row"><span>Effective From</span><b>{{ date('d M Y', strtotime(@$aatUpdated ?: now())) }}</b></div>
                  <div class="aga-meta-row"><span>Agreement Duration</span><b>{{ (int) (@$aatTerms->agreement_duration_months ?? 12) }} Months</b></div>
                  <div class="aga-meta-row"><span>Auto Renewal</span><b>{{ (@$aatTerms->auto_renewal ?? 1) ? 'Yes' : 'No' }}</b></div>
                  <div class="aga-meta-row"><span>Applicable To</span><b>{{ @$aatTerms->applies_to ?: 'All Agencies' }}</b></div>
                  <div class="aga-meta-row"><span>Acceptance Required</span><b>{{ (@$aatTerms->acceptance_required ?? 1) ? 'Yes' : 'No' }}</b></div>
                </div>
              </div>
              <div class="aga-card aga-tips">
                <h4>Tips for best results</h4>
                <ul>
                  <li>Use variables for dynamic content</li>
                  <li>Keep clauses clear and precise</li>
                  <li>Review before publishing</li>
                </ul>
              </div>
            </aside>
          </div>
          <div class="card-footer d-flex justify-content-center py-6 px-9 bg-white d-none">
            <button class="btn btn-primary me-2 updateCompanyTerms" type="button" id="updateCompanyTerms">Save Changes</button>
            <button class="btn btn-primary me-2 setToDefaultCompanyTerms" type="button" id="setToDefaultCompanyTerms">Set to Default</button>
          </div>
        </div>
      </form>
    </div>

    <div class="aat-important"><b>Important:</b> Existing agencies who have already accepted a previous version will continue to be governed by the version they accepted until they accept a newer version.</div>
  </div>
</div>
<!--end::Terms & Canditions Content-->

<!--begin::offerterms Terms & conditions Content-->
<div class="tab-pane" id="offerterms" role="tabpanel">
  @php
    // Intent: define the company-level candidate offer template catalog from existing company columns with global defaults as fallback.
    $candidateOfferTemplates = [
      [
        'field' => 'offer_terms',
        'label' => 'Fulltime Offer Letter',
        'type' => 'Full Time',
        'small' => !empty(trim(strip_tags((string) @$account->offer_terms))) ? 'Configured template' : 'Default template',
        'content' => (@$account->offer_terms) ? @$account->offer_terms : @$global_settings->offer_terms,
      ],
      [
        'field' => 'internship_offer_terms',
        'label' => 'Internship Offer Letter',
        'type' => 'Internship',
        'small' => !empty(trim(strip_tags((string) @$account->internship_offer_terms))) ? 'Configured template' : 'Default template',
        'content' => (@$account->internship_offer_terms) ? @$account->internship_offer_terms : (@$global_settings->internship_offer_terms ?: @$global_settings->offer_terms),
      ],
      [
        'field' => 'contract_offer_terms',
        'label' => 'Contract Offer Letter',
        'type' => 'Contract',
        'small' => !empty(trim(strip_tags((string) @$account->contract_offer_terms))) ? 'Configured template' : 'Default template',
        'content' => (@$account->contract_offer_terms) ? @$account->contract_offer_terms : (@$global_settings->contract_offer_terms ?: @$global_settings->offer_terms),
      ],
      [
        'field' => 'agency_offer_terms',
        'label' => 'Agency Offer terms',
        'type' => 'Contract',
        'small' => !empty(trim(strip_tags((string) @$account->agency_offer_terms))) ? 'Configured template' : 'Default template',
        'content' => (@$account->agency_offer_terms) ? @$account->agency_offer_terms : (@$global_settings->agency_offer_terms ?: @$global_settings->offer_terms),
      ],
    ];
    $activeCandidateOfferTemplate = $candidateOfferTemplates[0];
    // Intent: keep candidate configuration icons local to the template UI so they render even when icon fonts are unavailable.
    $candidateOfferIcon = '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 3h9l3 3v15H6z"/><path d="M14 3v4h4M9 11h6M9 15h5"/></svg>';
    $candidateEditIcon = '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"/></svg>';
    $candidateResetIcon = '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 7v6h6"/><path d="M21 17a9 9 0 0 1-15-6.7L3 13"/></svg>';
    $candidateSaveIcon = '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><path d="M17 21v-8H7v8M7 3v5h8"/></svg>';
  @endphp
  <div class="candidate-config-shell">
    <aside class="candidate-config-nav" aria-label="Candidate configuration sections">
      <h4>Documents &amp; Templates</h4>
      <div class="candidate-config-group">Salary Annexures</div>
      <a class="candidate-config-link" data-toggle="tab" href="#offercomponent"><i class="far fa-money-bill-alt"></i><span>Standard Payroll Structure<small>{{ count($offer_points ?? []) }} salary components</small></span></a>
      <div class="candidate-config-group">Offer Letter Templates</div>
      @foreach($candidateOfferTemplates as $candidateOfferTemplate)
        <a class="candidate-config-link candidate-offer-template-link {{ $loop->first ? 'active' : '' }}" data-toggle="tab" href="#offerterms" data-template-field="{{ $candidateOfferTemplate['field'] }}" data-template-label="{{ $candidateOfferTemplate['label'] }}" data-template-type="{{ $candidateOfferTemplate['type'] }}" data-template-content='@json($candidateOfferTemplate["content"])'><i>{!! $candidateOfferIcon !!}</i><span>{{ $candidateOfferTemplate['label'] }}<small>{{ $candidateOfferTemplate['small'] }}</small></span></a>
      @endforeach
    </aside>
    <div class="candidate-config-panel">
  <div class="candidate-config-main-card">
  <!--begin::Overview header-->
  <div class="card-header cursor-pointer px-0 candidate-config-card-head">
    <!--begin::Card title-->
    <div class="card-title m-0">
      <h3 class="fw-bolder m-0 candidate-config-title" id="candidateOfferTemplateTitle">{{ $activeCandidateOfferTemplate['label'] }}</h3>
      <p class="candidate-config-subtitle">Manage the reusable offer letter template used during candidate offer generation.</p>
    </div>
    <!--end::Card title-->
    <!--begin::Action-->
    <div class="card-title m-0 candidate-config-actions">
    <a href="javascript:;" class="btn btn-success me-1 align-self-center updateCompanyTermsofferTop d-none" id="updateCompanyTermsofferTop">Save Changes</a>
    <a href="javascript:;" class="btn btn-warning me-1 align-self-center setToDefaultCompanyTermsofferTop d-none" id="setToDefaultCompanyTermsofferTop">Set to Default</a>
    <button type="button" class="btn btn-primary align-self-center" id="editBtnoffer"><span class="candidate-action-icon">{!! $candidateEditIcon !!}</span>Edit</button>
    </div>
    <!--ensd::Action-->
  </div>
  <!--end::Terms & Canditions header-->
  @php
    // Intent: reuse the same dynamic offer-letter preview identity in the main view and edit drawer without changing persistence logic.
    $settingsOfferLogo = (!empty(@$account->logo) && strlen(@$account->logo) > 10) ? @$account->logo : asset('assets/logo-not-found.png');
    if (!empty(@$account->website) && empty(@$account->logo)) {
      $settingsOfferDomain = GeneralMethod::getDomain($account->website);
      $settingsOfferGoogleIcon = "https://www.google.com/s2/favicons?domain={$settingsOfferDomain}&sz=256";
      $settingsOfferLogo = GeneralMethod::getDomainIconCompany($settingsOfferGoogleIcon, $account->id, false) ? $settingsOfferGoogleIcon : asset('assets/logo-not-found.png');
    }
    $settingsOfferAddress = trim(ucwords($company_city_name).(($company_city_name && $company_country_name) ? ', ' : '').ucwords($company_country_name), ', ');
    $settingsOfferVariables = [
      '[CANDIDATE_NAME]'   => 'Candidate Name',
      '[CANDIDATE_EMAIL]'  => 'Candidate Email',
      '[CANDIDATE_MOBILE]' => 'Candidate Mobile',
      '[COMPANY_NAME]'     => 'Company Name',
      '[JOB_TITLE]'        => 'Job Title',
      '[JOINING_DATE]'     => 'Joining Date',
      '[TODAY_DATE]'       => "Today's Date",
    ];
  @endphp
  <div class="card-body px-0 py-7"> 
    <!--begin::Terms & Canditions Content-->
    <div class="row">
      <div class="candidate-config-subcard">
        <div class="candidate-config-subcard-head">
          <div>
            <div class="candidate-config-subcard-title">Template Preview</div>
            <div class="candidate-config-subcard-sub">This content is dynamically merged with candidate, job, and company details in the offer workflow.</div>
          </div>
        </div>
        <div class="settings-offer-template">
          <div class="co-tpl-layout co-tpl-layout--main-preview">
            <div class="co-tpl-editor-col">
              <div class="co-tpl-tabs">
                <button type="button" class="co-tpl-tab co-tpl-tab--active">Preview</button>
              </div>
              <div class="co-tpl-subcard co-tpl-meta-row">
                <span class="co-tpl-meta-item">Type<b class="candidate-offer-template-type">{{ $activeCandidateOfferTemplate['type'] }}</b></span>
                <span class="co-tpl-meta-item">Status<b style="color:#0f9463">Active</b></span>
              </div>
              <div class="co-tpl-subcard">
                <div class="co-letterhead">
                  <div class="co-letterhead-logo">
                    <img src="{{ $settingsOfferLogo }}" alt="{{ e(@$account->company_name ?: 'Company') }}" onerror="this.src='{{ asset('assets/logo-not-found.png') }}'">
                  </div>
                  <div class="co-letterhead-address">
                    <div class="co-letterhead-company">{{ ucwords(@$account->company_name ?: 'Company Name') }}</div>
                    @if(!empty($settingsOfferAddress))
                      <div>{{ $settingsOfferAddress }}</div>
                    @endif
                  </div>
                </div>
              </div>
              <div class="col-lg-12 mb-0 view-terms co-tpl-preview candidate-offer-main-preview" id="viewTermsoffer">{!! $activeCandidateOfferTemplate['content'] !!}</div>
            </div>
          </div>
        </div>
      </div>
      <div class="offer-letter-drawer-ov" id="offerLetterDrawerOverlay" onclick="closeOfferLetterDrawer()"></div>
      <form  id="terms_contentoffer" name="terms_contentoffer" class="form fv-plugins-bootstrap5 fv-plugins-framework" novalidate="novalidate" method="post">
          <meta name="csrf-token" content="{{ csrf_token() }}" />
          <input type="hidden" name="pageterms" id="pagetermsoffer" value="termsoffer" />
          <input type="hidden" name="descriptiontype" id="candidateOfferTemplateField" value="{{ $activeCandidateOfferTemplate['field'] }}" />
          @csrf  
          @method('POST')
        <!--begin::Col-Desc-->
       


        <div class="col-lg-12 edit-terms offer-letter-drawer" id="editTermsoffer" style="display:none;">
          <div class="offer-letter-drawer-header">
            <div><h4 class="offer-letter-drawer-title" id="candidateOfferDrawerTitle">Edit {{ $activeCandidateOfferTemplate['label'] }}</h4><p class="offer-letter-drawer-sub">Update the reusable template used when generating candidate offers.</p></div>
            <div class="offer-letter-drawer-actions">
              <button type="button" class="btn btn-light-primary setToDefaultCompanyTermsofferTop"><span class="candidate-action-icon">{!! $candidateResetIcon !!}</span>Load Default</button>
              <button type="button" class="btn btn-primary updateCompanyTermsofferTop"><span class="candidate-action-icon">{!! $candidateSaveIcon !!}</span>Save Changes</button>
              <button type="button" class="offer-letter-drawer-close" aria-label="Close offer letter editor" onclick="closeOfferLetterDrawer()">&times;</button>
            </div>
          </div>
          <div class="offer-letter-drawer-body">
            <div class="settings-offer-template">
              <div class="co-tpl-layout">
                <div class="co-tpl-editor-col">
                  <div class="co-tpl-tabs">
                    <button type="button" class="co-tpl-tab co-tpl-tab--active" data-settings-offer-tab="editor">Template Editor</button>
                    <button type="button" class="co-tpl-tab" data-settings-offer-tab="preview">Preview</button>
                  </div>

                  <div class="co-tpl-subcard co-tpl-meta-row">
                    <span class="co-tpl-meta-item">Type<b class="candidate-offer-template-type">{{ $activeCandidateOfferTemplate['type'] }}</b></span>
                    <span class="co-tpl-meta-item">Status<b style="color:#0f9463">Active</b></span>
                  </div>

                  <div class="settings-offer-tabpane" data-settings-offer-pane="editor">
                    <div class="co-tpl-subcard">
                      <div class="co-letterhead">
                        <div class="co-letterhead-logo">
                          <img src="{{ $settingsOfferLogo }}" alt="{{ e(@$account->company_name ?: 'Company') }}" onerror="this.src='{{ asset('assets/logo-not-found.png') }}'">
                        </div>
                        <div class="co-letterhead-address">
                          <div class="co-letterhead-company">{{ ucwords(@$account->company_name ?: 'Company Name') }}</div>
                          @if(!empty($settingsOfferAddress))
                            <div>{{ $settingsOfferAddress }}</div>
                          @endif
                        </div>
                      </div>
                    </div>
                    <div class="offer-letter-editor-shell">
                      <textarea id="kt_docs_tinymce_hiddenoffer" name="kt_docs_tinymce_hiddenoffer" class="tox-target">{!! $activeCandidateOfferTemplate['content'] !!}</textarea>
                    </div>
                    <div class="co-tpl-subcard co-tpl-toolbar-extra" style="margin-top:10px;margin-bottom:0;display:flex;">
                      <div class="co-tpl-zoom">
                        <button type="button" class="co-tpl-zoom-btn" id="settings_offer_zoom_out" aria-label="Zoom out">&minus;</button>
                        <span class="co-tpl-zoom-pct" id="settings_offer_zoom_pct">100%</span>
                        <button type="button" class="co-tpl-zoom-btn" id="settings_offer_zoom_in" aria-label="Zoom in">+</button>
                      </div>
                      <button type="button" class="co-tpl-download-btn" id="settings_offer_download_sample">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v12m0 0-4-4m4 4 4-4M4 19h16"/></svg>
                        Download Sample
                      </button>
                    </div>
                  </div>

                  <div class="settings-offer-tabpane d-none" data-settings-offer-pane="preview">
                    <div class="co-tpl-subcard">
                      <div class="co-letterhead">
                        <div class="co-letterhead-logo">
                          <img src="{{ $settingsOfferLogo }}" alt="{{ e(@$account->company_name ?: 'Company') }}" onerror="this.src='{{ asset('assets/logo-not-found.png') }}'">
                        </div>
                        <div class="co-letterhead-address">
                          <div class="co-letterhead-company">{{ ucwords(@$account->company_name ?: 'Company Name') }}</div>
                          @if(!empty($settingsOfferAddress))
                            <div>{{ $settingsOfferAddress }}</div>
                          @endif
                        </div>
                      </div>
                    </div>
                    <div id="settings_offer_preview_content" class="co-tpl-preview"></div>
                  </div>
                </div>

                <div class="co-tpl-side">
                  <div class="co-tpl-subcard">
                    <div class="co-tpl-side-title">Insert Variables</div>
                    <div class="co-tpl-side-sub">Click to insert dynamic candidate and company fields at the cursor.</div>
                    <div class="co-var-chips">
                      @foreach($settingsOfferVariables as $token => $label)
                        <button type="button" class="co-var-chip" data-settings-offer-variable="{{ $token }}">{{ $label }}</button>
                      @endforeach
                    </div>
                  </div>
                  <div class="co-tpl-subcard" style="margin-bottom:0;">
                    <div class="co-tpl-side-title">About this template</div>
                    <div class="co-tpl-note" style="margin-top:6px;">This is your company's default Offer Letter Template. Changes saved here are reused on candidate offer generation pages.</div>
                  </div>
                  <button type="button" class="co-btn-reset setToDefaultCompanyTermsofferTop" style="margin-top:10px;">Reset Template</button>
                </div>
              </div>
            </div>
          
        
          <div class="card-footer d-flex justify-content-center py-6 px-9 bg-white d-none">
          
            <button class="btn btn-primary me-2 updateCompanyTermsoffer" type="button" id="updateCompanyTermsoffer">Save Changes</button>
            <button class="btn btn-primary me-2 setToDefaultCompanyTermsoffer" type="button" id="setToDefaultCompanyTermsoffer">Set to Default</button>
          </div>
          </div>
        </div>
  
      </form>

    </div>

  </div>
  </div>
</div>
  </div>
  </div>
<!--end::offerterms Terms & Canditions Content-->


<!--begin::agencyofferterms Terms & Conditions Content-->
<div class="tab-pane" id="agencyofferterms" role="tabpanel">
  <!--begin::Overview header-->
  <div class="card-header cursor-pointer px-0">
    <!--begin::Card title-->
    <div class="card-title m-0">
      <h3 class="fw-bolder m-0">Terms & Conditions</h3>
    </div>
    <!--end::Card title-->
    <!--begin::Action-->
    <div class="card-title m-0">
    <a href="javascript:;" class="btn btn-success me-1 align-self-center updateCompanyTermsagencyofferTop" id="updateCompanyTermsagencyofferTop">Save Changes</a>
    <a href="javascript:;" class="btn btn-warning me-1 align-self-center setToDefaultCompanyTermsagencyofferTop" id="setToDefaultCompanyTermsagencyofferTop">Set to Default</a>
    <a href="javascript:;" class="btn btn-primary align-self-center" id="editBtnagencyoffer">Edit</a>
    </div>
    <!--end::Action-->
  </div>
  <!--end::Terms & Conditions header-->
  <div class="card-body px-0 py-7">
    <!--begin::Terms & Conditions Content-->
    <div class="row">
      <div class="col-lg-12 mb-7 view-terms" id="viewTermsagencyoffer">{!! (@$account->agency_offer_terms) ? @$account->agency_offer_terms : ((@$global_settings->agency_offer_terms) ? $global_settings->agency_offer_terms : '') !!}</div>
      <form id="terms_contentagencyoffer" name="terms_contentagencyoffer" class="form fv-plugins-bootstrap5 fv-plugins-framework" novalidate="novalidate" method="post">
          <meta name="csrf-token" content="{{ csrf_token() }}" />
          <input type="hidden" name="pageterms" id="pagetermsagencyoffer" value="termsagencyoffer" />
          @csrf
          @method('POST')
        <div class="col-lg-12 edit-terms" id="editTermsagencyoffer" style="display:none;">
            <textarea id="kt_docs_tinymce_hiddenagencyoffer" name="kt_docs_tinymce_hiddenagencyoffer" class="tox-target">{!! (@$account->agency_offer_terms) ? @$account->agency_offer_terms : ((@$global_settings->agency_offer_terms) ? $global_settings->agency_offer_terms : '') !!}</textarea>
          <div class="card-footer d-flex justify-content-center py-6 px-9 bg-white d-none">
            <button class="btn btn-primary me-2 updateCompanyTermsagencyoffer" type="button" id="updateCompanyTermsagencyoffer">Save Changes</button>
            <button class="btn btn-primary me-2 setToDefaultCompanyTermsagencyoffer" type="button" id="setToDefaultCompanyTermsagencyoffer">Set to Default</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
<!--end::agencyofferterms Terms & Conditions Content-->


<!--begin::Offer components Content-->
<div class="tab-pane" id="offercomponent" role="tabpanel">
  <div class="candidate-config-shell">
    <aside class="candidate-config-nav" aria-label="Candidate configuration sections">
      <h4>Documents &amp; Templates</h4>
      <div class="candidate-config-group">Salary Annexures</div>
      <a class="candidate-config-link active" data-toggle="tab" href="#offercomponent"><i class="far fa-money-bill-alt"></i><span>Standard Payroll Structure<small>{{ count($offer_points ?? []) }} salary components</small></span></a>
      <div class="candidate-config-group">Offer Letter Templates</div>
      @foreach($candidateOfferTemplates as $candidateOfferTemplate)
        <a class="candidate-config-link candidate-offer-template-link" data-toggle="tab" href="#offerterms" data-template-field="{{ $candidateOfferTemplate['field'] }}" data-template-label="{{ $candidateOfferTemplate['label'] }}" data-template-type="{{ $candidateOfferTemplate['type'] }}" data-template-content='@json($candidateOfferTemplate["content"])'><i>{!! $candidateOfferIcon !!}</i><span>{{ $candidateOfferTemplate['label'] }}<small>{{ $candidateOfferTemplate['small'] }}</small></span></a>
      @endforeach
    </aside>
    <div class="candidate-config-panel">
  @php
    $sstMain  = collect($offer_points ?? []);
    $sstAdd   = collect($offer_point_other ?? []);
    $sstMainTotal = round($sstMain->sum('percentage'), 2);
    $sstAddTotal  = round($sstAdd->sum('percentage'), 2);
    $sstOverall   = round($sstMainTotal + $sstAddTotal, 2);
    $sstViewLabel = [0 => 'Annually', 1 => 'Monthly', 2 => 'Both'];
    $sstCondLabel = [0 => 'Amount', 1 => 'Basic'];
    $sstMainValid = abs($sstMainTotal - 100) < 0.01;
  @endphp
  <style>
    .sst-wrap { --b:#e4e6ef; --bg:#f5f8fa; --t:#181c32; --tm:#7e8299; --tl:#a1a5b7; --p:#7239ea; --g:#50cd89; }
    .sst-head { display:flex; align-items:flex-start; justify-content:space-between; gap:12px; flex-wrap:wrap; margin-bottom:6px; }
    .sst-title { font-size:18px; font-weight:700; color:var(--t); display:flex; align-items:center; gap:10px; }
    .sst-badge { display:inline-flex; align-items:center; gap:5px; background:#e8fff3; color:#47be7d; font-size:11px; font-weight:600; padding:3px 10px; border-radius:50px; }
    .sst-sub { font-size:12.5px; color:var(--tm); margin-top:2px; }
    .sst-meta { display:flex; align-items:center; gap:24px; flex-wrap:wrap; font-size:12px; color:var(--tm); padding:10px 0 16px; border-bottom:1px solid var(--b); margin-bottom:18px; }
    .sst-meta .mi { display:flex; align-items:center; gap:7px; } .sst-meta .mi i { color:var(--tl); }
    .sst-meta .mi b { color:var(--t); font-weight:600; }
    .sst-layout { display:grid; grid-template-columns:1fr 320px; gap:18px; align-items:start; }
    @media (max-width:1400px){ .sst-layout { grid-template-columns:1fr; } }
    .sst-cards { display:grid; grid-template-columns:repeat(2,1fr); gap:14px; margin-bottom:18px; }
    @media (max-width:600px){ .sst-cards { grid-template-columns:1fr; } }
    .sst-card { background:#fff; border:1px solid var(--b); border-radius:12px; padding:16px; }
    .sst-card-l { font-size:12px; color:var(--tm); font-weight:600; margin-bottom:8px; }
    .sst-card-v { font-size:24px; font-weight:700; color:var(--t); line-height:1; margin-bottom:8px; }
    .sst-card-v.p { color:var(--p); }
    .sst-card-note { display:inline-flex; align-items:center; gap:5px; font-size:11.5px; font-weight:500; }
    .sst-card-note.ok { color:var(--g); } .sst-card-note.add { color:var(--p); } .sst-card-note.neutral { color:var(--tm); }
    .sst-card.valid { background:#f8fff9; border-color:#cdf3df; }
    .sst-valid-row { display:flex; align-items:center; gap:10px; }
    .sst-valid-ic { width:34px; height:34px; border-radius:8px; background:#e8fff3; color:var(--g); display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .sst-section { background:#fff; border:1px solid var(--b); border-radius:12px; padding:20px 22px; margin-bottom:18px; }
    .sst-section-t { font-size:15px; font-weight:700; color:var(--t); }
    .sst-section-s { font-size:12px; color:var(--tm); margin-bottom:14px; }
    .sst-table { width:100%; border-collapse:collapse; }
    .sst-table thead th { font-size:11px; font-weight:600; color:var(--tm); text-transform:uppercase; letter-spacing:.3px; text-align:left; padding:10px 12px; border-bottom:1px solid var(--b); white-space:nowrap; }
    .sst-table tbody td { padding:13px 12px; font-size:13px; color:var(--t); border-bottom:1px solid #f4f6f9; vertical-align:middle; }
    .sst-table tbody tr:last-child td { border-bottom:none; }
    .sst-name { font-weight:600; color:var(--t); }
    .sst-pill { display:inline-flex; align-items:center; padding:2px 8px; border-radius:6px; font-size:10.5px; font-weight:600; margin-left:8px; }
    .sst-pill.main { background:#eef2ff; color:#5b4dff; } .sst-pill.add { background:#f8f5ff; color:var(--p); }
    .sst-vchip { display:inline-flex; align-items:center; padding:4px 10px; border-radius:6px; background:var(--bg); color:#5e6278; font-size:12px; font-weight:500; }
    .sst-pct { font-weight:700; color:var(--t); } .sst-muted { color:var(--tl); }
    .sst-foot-ok { background:#f8fff9; border:1px solid #cdf3df; border-radius:10px; padding:12px 16px; font-size:13px; font-weight:600; color:#2d8a5a; display:flex; align-items:center; gap:8px; margin-top:6px; }
    .sst-help { display:grid; grid-template-columns:1fr 1.4fr; gap:20px; background:var(--bg); border:1px solid var(--b); border-radius:12px; padding:18px 22px; }
    @media (max-width:992px){ .sst-help { grid-template-columns:1fr; } }
    .sst-help h4 { font-size:13.5px; font-weight:700; color:var(--t); margin:0 0 6px; }
    .sst-help p { font-size:12.5px; color:var(--tm); line-height:1.6; margin:0; }
    .sst-ex { display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
    .sst-ex .chip { background:#fff; border:1px solid var(--b); border-radius:8px; padding:8px 12px; font-size:12px; font-weight:600; color:var(--t); }
    .sst-ex .chip.add { background:#fff8f0; border-color:#ffe0c2; color:#c2671a; } .sst-ex .op { color:var(--tm); font-weight:600; }
    /* preview */
    .sst-preview { background:#fff; border:1px solid var(--b); border-radius:12px; padding:18px; position:sticky; top:80px; }
    .sst-pv-title { font-size:14px; font-weight:700; color:var(--p); display:flex; align-items:center; gap:7px; margin-bottom:3px; }
    .sst-pv-sub { font-size:11.5px; color:var(--tm); margin-bottom:14px; }
    .sst-pv-lbl { font-size:11.5px; font-weight:600; color:var(--t); margin:10px 0 5px; }
    .sst-pv-amt { display:flex; gap:8px; }
    .sst-pv-amt input { flex:1; border:1px solid var(--b); border-radius:8px; padding:8px 10px; font-size:13px; outline:none; }
    .sst-pv-calc { background:var(--p); color:#fff; border:none; border-radius:8px; padding:8px 14px; font-size:12.5px; font-weight:600; cursor:pointer; }
    .sst-pv-toggle { display:flex; gap:6px; margin-top:4px; }
    .sst-pv-toggle button { flex:1; border:1px solid var(--b); background:#fff; color:var(--tm); border-radius:7px; padding:6px; font-size:11.5px; font-weight:600; cursor:pointer; }
    .sst-pv-toggle button.on { background:#5b4dff; color:#fff; border-color:#5b4dff; }
    .sst-pv-box { background:#fafbfc; border:1px solid var(--b); border-radius:10px; padding:12px; margin-top:12px; }
    .sst-pv-row { display:flex; justify-content:space-between; align-items:center; font-size:12px; color:var(--tm); padding:5px 0; }
    .sst-pv-row .v { font-weight:600; color:var(--t); }
    .sst-pv-row.total { border-top:1px solid var(--b); margin-top:6px; padding-top:9px; font-weight:700; color:var(--t); }
    .sst-donut-wrap { display:flex; align-items:center; gap:14px; margin-top:14px; }
    .sst-leg { font-size:11.5px; color:var(--tm); flex:1; }
    .sst-leg .li { display:flex; align-items:center; gap:6px; margin-bottom:6px; }
    .sst-leg .dot { width:9px; height:9px; border-radius:50%; }
    .sst-vchecks { margin-top:16px; } .sst-vchecks h5 { font-size:12.5px; font-weight:700; color:var(--t); margin:0 0 8px; }
    .sst-vchecks .ck { display:flex; align-items:flex-start; gap:7px; font-size:11.5px; color:var(--tm); margin-bottom:7px; }
    .sst-vchecks .ck i { font-size:12px; margin-top:1px; } .sst-vchecks .ck.ok i { color:var(--g); } .sst-vchecks .ck.warn i { color:#f6a723; }
    /* edit drawer */
    .sst-drawer-ov { position:fixed; inset:0; background:rgba(0,0,0,.4); z-index:1090; opacity:0; visibility:hidden; transition:opacity .2s; }
    .sst-drawer-ov.show { opacity:1; visibility:visible; }
    .sst-drawer { position:fixed; top:0; right:0; height:100vh; width:min(1600px,calc(95vw - 200px)); min-width:720px; max-width:calc(100vw - 12px); background:#fff; z-index:1091; box-shadow:-8px 0 40px rgba(0,0,0,.15); transform:translateX(100%); transition:transform .25s ease; display:flex; flex-direction:column; }
    .sst-drawer.show { transform:translateX(0); }
    .sst-drawer-h { display:flex; align-items:center; justify-content:space-between; gap:14px; padding:12px 18px; border-bottom:1px solid var(--b); }
    .sst-drawer-h .t { font-size:15px; font-weight:700; color:var(--t); }
    .sst-drawer-actions { display:flex; align-items:center; justify-content:flex-end; gap:8px; margin-left:auto; }
    .sst-drawer-actions .btn { font-size:12px; font-weight:600; white-space:nowrap; }
    .sst-drawer-x { width:34px; height:34px; border:none; background:var(--bg); border-radius:8px; cursor:pointer; font-size:15px; color:var(--tm); }
    .sst-drawer iframe { flex:1; width:100%; border:none; }
    /* Read-only compensation view; editing remains in the existing drawer to preserve salary persistence behavior. */
    .sst-comp-overview { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:12px; margin:16px 0 18px; padding:14px; border:1px solid var(--b); border-radius:10px; background:#fbfcff; }
    .sst-comp-field { min-width:0; padding-right:12px; border-right:1px solid var(--b); }
    .sst-comp-field:last-child { padding-right:0; border-right:0; }
    .sst-comp-label { display:block; margin-bottom:6px; color:var(--tm); font-size:10.5px; font-weight:600; }
    .sst-comp-value { display:block; overflow:hidden; color:var(--t); font-size:12.5px; font-weight:600; text-overflow:ellipsis; white-space:nowrap; }
    .salary-compensation { display:grid; gap:14px; }
    .salary-comp-card { border:1px solid var(--b); border-radius:10px; background:#fff; overflow:hidden; }
    .salary-comp-heading { display:flex; align-items:center; justify-content:space-between; gap:12px; padding:14px 16px 0; }
    .salary-comp-heading h4 { margin:0; color:var(--t); font-size:14px; font-weight:600; }
    .salary-comp-heading p { margin:3px 0 0; color:var(--tm); font-size:11.5px; font-weight:500; }
    .salary-comp-grid { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:12px; padding:14px 16px 16px; }
    .salary-comp-control { min-width:0; }
    .salary-comp-control label { display:block; margin-bottom:5px; color:var(--t); font-size:11px; font-weight:600; }
    .salary-comp-control label i { width:14px; margin-right:4px; color:#4d72ff; text-align:center; }
    .salary-comp-readonly { display:flex; align-items:center; min-height:35px; padding:8px 10px; border:1px solid #d8e0ee; border-radius:6px; color:#34405d; background:#fff; font-size:12px; font-weight:500; }
    .salary-comp-table-wrap { overflow-x:auto; padding:12px 16px 16px; }
    .salary-comp-table { width:100%; min-width:760px; border-collapse:separate; border-spacing:0; border:1px solid #e3e8f2; border-radius:7px; overflow:hidden; }
    .salary-comp-table th { padding:9px 10px; background:#f7f9fc; border-bottom:1px solid #e3e8f2; color:#60708f; font-size:10.5px; font-weight:600; text-align:left; }
    .salary-comp-table td { padding:9px 10px; border-bottom:1px solid #edf0f5; color:#26334f; font-size:11.5px; font-weight:500; vertical-align:middle; }
    .salary-comp-table tbody tr:last-child td { border-bottom:0; }
    .salary-comp-no { color:#7b88a2; font-size:10.5px; font-weight:600; }
    .salary-comp-total { display:flex; justify-content:space-between; gap:12px; padding:10px 16px; border-top:1px solid #d8e9e0; background:#ecfff3; color:#177245; font-size:11.5px; font-weight:600; }
    .salary-comp-total.additional { border-top-color:#eadfff; background:#faf7ff; color:#6b3ed1; }
    .sst-legacy-layout { display:none !important; }
    @media(max-width:991px){ .sst-comp-overview,.salary-comp-grid { grid-template-columns:repeat(2,minmax(0,1fr)); } .sst-comp-field:nth-child(2){ border-right:0; } }
    @media(max-width:767px){ .sst-drawer { min-width:0; width:100vw; } .sst-drawer-actions .btn { padding:7px 9px; font-size:11px; } }
    @media(max-width:575px){ .sst-comp-overview,.salary-comp-grid { grid-template-columns:1fr; } .sst-comp-field{ padding:0 0 10px; border-right:0; border-bottom:1px solid var(--b); } .sst-comp-field:last-child{ padding-bottom:0; border-bottom:0; } }
  </style>

  <div class="sst-wrap candidate-config-main-card">
    <div class="sst-head candidate-config-card-head">
      <div>
        <div class="sst-title">Salary Annexure <span class="sst-badge"><i class="fas fa-check" style="font-size:8px;"></i>Active</span></div>
        <div class="sst-sub">Standard Payroll Structure · Primary template for candidate offers.</div>
      </div>
      <button type="button" class="btn btn-primary align-self-center" onclick="openSalaryEditDrawer()"><i class="fas fa-pen me-1" style="font-size:11px;"></i> Edit</button>
    </div>

    <div class="sst-meta">
      <span class="mi"><i class="fas fa-hashtag"></i>Template ID: <b>TMPL-{{ str_pad(auth()->user()->company_id, 4, '0', STR_PAD_LEFT) }}</b></span>
      <span class="mi"><i class="far fa-calendar"></i>Created By: <b>{{ ucwords(auth()->user()->name) }}</b></span>
      <span class="mi"><i class="far fa-clock"></i>Last Updated: <b>{{ now()->format('d M Y') }}</b></span>
    </div>

    <div class="sst-comp-overview candidate-config-subcard" aria-label="Compensation details">
      <div class="sst-comp-field"><span class="sst-comp-label">Compensation Type</span><span class="sst-comp-value">Salary Annexure</span></div>
      <div class="sst-comp-field"><span class="sst-comp-label">Main Components</span><span class="sst-comp-value">{{ $sstMain->count() }} components · {{ number_format($sstMainTotal, 2) }}%</span></div>
      <div class="sst-comp-field"><span class="sst-comp-label">Additional Components</span><span class="sst-comp-value">{{ $sstAdd->count() }} components · {{ number_format($sstAddTotal, 2) }}%</span></div>
      <div class="sst-comp-field"><span class="sst-comp-label">Validation</span><span class="sst-comp-value" style="color:{{ $sstMainValid ? '#1a9c61' : '#d97706' }}">{{ $sstMainValid ? 'Main total is 100%' : 'Main total requires review' }}</span></div>
    </div>

    <div class="salary-compensation" aria-label="Salary annexure compensation details">
      <section class="salary-comp-card candidate-config-subcard">
        <div class="salary-comp-heading">
          <div><h4>Compensation Details</h4><p>These offer details are captured when an offer is created from this salary annexure.</p></div>
        </div>
        <div class="salary-comp-grid">
          <div class="salary-comp-control"><label><i class="fas fa-wallet"></i>Currency</label><div class="salary-comp-readonly">Selected when creating an offer</div></div>
          <div class="salary-comp-control"><label><i class="fas fa-wallet"></i>Enter Amount (CTC)</label><div class="salary-comp-readonly">Set when creating an offer</div></div>
          <div class="salary-comp-control"><label><i class="fas fa-calendar-alt"></i>Salary Format</label><div class="salary-comp-readonly">Selected when creating an offer</div></div>
          <div class="salary-comp-control"><label><i class="fas fa-briefcase"></i>Offered Designation</label><div class="salary-comp-readonly">Set when creating an offer</div></div>
          <div class="salary-comp-control"><label><i class="far fa-calendar-alt"></i>Date of Joining (DOJ)</label><div class="salary-comp-readonly">Set when creating an offer</div></div>
          <div class="salary-comp-control"><label><i class="fas fa-briefcase"></i>Job Type</label><div class="salary-comp-readonly">Selected when creating an offer</div></div>
        </div>
      </section>

      <section class="salary-comp-card candidate-config-subcard">
        <div class="salary-comp-heading">
          <div><h4>Main Components <span style="color:#7e8299;font-size:11px;font-weight:500;">(must total 100%)</span></h4><p>Core salary components used in the payroll structure.</p></div>
        </div>
        <div class="salary-comp-table-wrap">
          <table class="salary-comp-table">
            <thead><tr><th>#</th><th>Component Name</th><th>Applicable</th><th>Allocation</th><th>Condition</th><th>Monthly</th><th>Annually</th></tr></thead>
            <tbody>
              @forelse ($sstMain as $i => $c)
                <tr>
                  <td class="salary-comp-no">{{ $i + 1 }}</td>
                  <td><strong>{{ $c->points }}</strong></td>
                  <td>{{ $sstViewLabel[$c->view_type] ?? 'Both' }}</td>
                  <td>{{ number_format($c->percentage, 2) }}%</td>
                  <td>{{ $sstCondLabel[$c->condition_check] ?? 'Amount' }}</td>
                  <td>—</td><td>—</td>
                </tr>
              @empty
                <tr><td colspan="7" style="text-align:center;color:#7e8299;">No main components configured.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
        <div class="salary-comp-total"><span>Total Main Components</span><span>{{ number_format($sstMainTotal, 2) }}%</span></div>
      </section>

      <section class="salary-comp-card candidate-config-subcard">
        <div class="salary-comp-heading">
          <div><h4>Additional Components <span style="color:#7e8299;font-size:11px;font-weight:500;">(optional)</span></h4><p>Additional allowances applied above the main salary allocation.</p></div>
        </div>
        <div class="salary-comp-table-wrap">
          <table class="salary-comp-table">
            <thead><tr><th>#</th><th>Component Name</th><th>Applicable</th><th>Allocation</th><th>Condition</th><th>Monthly</th><th>Annually</th></tr></thead>
            <tbody>
              @forelse ($sstAdd as $i => $c)
                <tr>
                  <td class="salary-comp-no">{{ $i + 1 }}</td>
                  <td><strong>{{ $c->points }}</strong></td>
                  <td>{{ $sstViewLabel[$c->view_type] ?? 'Both' }}</td>
                  <td>{{ number_format($c->percentage, 2) }}%</td>
                  <td>{{ $sstCondLabel[$c->condition_check] ?? 'Amount' }}</td>
                  <td>—</td><td>—</td>
                </tr>
              @empty
                <tr><td colspan="7" style="text-align:center;color:#7e8299;">No additional components configured.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
        <div class="salary-comp-total additional"><span>Total Additional Components</span><span>{{ number_format($sstAddTotal, 2) }}%</span></div>
      </section>

      {{-- A structure has no candidate CTC of its own; final amounts are calculated from the live offer CTC. --}}
      <section class="salary-comp-card candidate-config-subcard" aria-label="Final CTC">
        <div class="salary-comp-heading">
          <div><h4>Final CTC <span style="color:#7e8299;font-size:11px;font-weight:500;">(Cost to Company)</span></h4><p>Summary of the total cost including main and additional components.</p></div>
        </div>
        <div class="salary-comp-table-wrap">
          <table class="salary-comp-table">
            <thead><tr><th>Component</th><th>Allocation</th><th>Currency</th><th>Monthly</th><th>Annually</th></tr></thead>
            <tbody>
              <tr>
                <td><strong>Total CTC (Main + Additional)</strong></td>
                <td>{{ number_format($sstOverall, 2) }}%</td>
                <td>Selected on offer</td>
                <td>Calculated on offer</td>
                <td>Calculated on offer</td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>
    </div>

    {{-- Legacy preview remains in markup only to preserve its existing client-side hooks; it is intentionally not rendered. --}}
    <div class="sst-layout sst-legacy-layout" aria-hidden="true">
      <div class="sst-mainside">
        <!-- Summary cards -->
        <div class="sst-cards">
          <div class="sst-card">
            <div class="sst-card-l">Main Components (Must Total 100%)</div>
            <div class="sst-card-v">{{ number_format($sstMainTotal, 2) }}%</div>
            @if ($sstMainValid)
              <span class="sst-card-note ok"><i class="fas fa-check-circle"></i>Total is exactly 100%</span>
            @else
              <span class="sst-card-note neutral"><i class="fas fa-info-circle"></i>Must be exactly 100%</span>
            @endif
          </div>
          <div class="sst-card">
            <div class="sst-card-l">Additional Components (Above 100%)</div>
            <div class="sst-card-v p">{{ number_format($sstAddTotal, 2) }}%</div>
            <span class="sst-card-note add"><i class="fas fa-plus"></i>Allowed above 100%</span>
          </div>
          <div class="sst-card">
            <div class="sst-card-l">Overall Structure</div>
            <div class="sst-card-v p">{{ number_format($sstOverall, 2) }}%</div>
            <span class="sst-card-note neutral"><i class="fas fa-chart-bar"></i>Main + Additional</span>
          </div>
          <div class="sst-card valid">
            <div class="sst-valid-row">
              <div class="sst-valid-ic"><i class="fas fa-shield-alt"></i></div>
              <div>
                <div class="sst-card-l" style="color:#47be7d;margin-bottom:2px;">Validation Status</div>
                <div class="sst-sub">{{ $sstMainValid ? 'Structure is valid and ready to use.' : 'Main components must total 100%.' }}</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Main Components -->
        <div class="sst-section">
          <div class="sst-section-t">Main Components (Must Total 100%)</div>
          <div class="sst-section-s">These are core components. Total percentage must be exactly 100%.</div>
          <div style="overflow-x:auto;">
            <table class="sst-table">
              <thead><tr><th style="width:40px;">#</th><th>Component Name</th><th>View</th><th>Percentage (%)</th><th>Conditions</th><th>Monthly (Preview)</th><th>Annually (Preview)</th></tr></thead>
              <tbody>
                @forelse ($sstMain as $i => $c)
                  <tr>
                    <td class="sst-muted">{{ $i + 1 }}</td>
                    <td><span class="sst-name">{{ $c->points }}</span><span class="sst-pill main">Main</span></td>
                    <td><span class="sst-vchip">{{ $sstViewLabel[$c->view_type] ?? 'Both' }}</span></td>
                    <td><span class="sst-pct">{{ number_format($c->percentage, 2) }}</span> %</td>
                    <td><span class="sst-vchip">{{ $sstCondLabel[$c->condition_check] ?? 'Amount' }}</span></td>
                    <td class="sst-muted">—</td><td class="sst-muted">—</td>
                  </tr>
                @empty
                  <tr><td colspan="7" class="sst-muted" style="text-align:center;padding:24px;">No main components configured.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
          @if ($sstMain->count())
            <div class="sst-foot-ok"><i class="fas {{ $sstMainValid ? 'fa-check-circle' : 'fa-exclamation-circle' }}"></i>{{ $sstMainValid ? 'Total of main components is exactly 100.00%' : 'Total of main components is '.number_format($sstMainTotal,2).'%' }}</div>
          @endif
        </div>

        <!-- Additional Components -->
        <div class="sst-section">
          <div class="sst-section-t">Additional Components (Above 100%) <span style="font-weight:500;color:var(--tm);font-size:12px;">- Optional</span></div>
          <div class="sst-section-s">These components are additional and will be added on top of the main 100%.</div>
          <div style="overflow-x:auto;">
            <table class="sst-table">
              <thead><tr><th style="width:40px;">#</th><th>Component Name</th><th>View</th><th>Percentage (%)</th><th>Conditions</th><th>Monthly (Preview)</th><th>Annually (Preview)</th></tr></thead>
              <tbody>
                @forelse ($sstAdd as $i => $c)
                  <tr>
                    <td class="sst-muted">{{ $i + 1 }}</td>
                    <td><span class="sst-name">{{ $c->points }}</span><span class="sst-pill add">Additional</span></td>
                    <td><span class="sst-vchip">{{ $sstViewLabel[$c->view_type] ?? 'Both' }}</span></td>
                    <td><span class="sst-pct">{{ number_format($c->percentage, 2) }}</span> %</td>
                    <td><span class="sst-vchip">{{ $sstCondLabel[$c->condition_check] ?? 'Amount' }}</span></td>
                    <td class="sst-muted">—</td><td class="sst-muted">—</td>
                  </tr>
                @empty
                  <tr><td colspan="7" class="sst-muted" style="text-align:center;padding:24px;">No additional components configured.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
          @if ($sstAdd->count())
            <div class="sst-foot-ok" style="color:#7239ea;background:#f8f5ff;border-color:#e7defb;"><i class="fas fa-plus-circle"></i>Total additional components: {{ number_format($sstAddTotal, 2) }}% (Allowed above 100%)</div>
          @endif
        </div>

        <!-- How it works -->
        <div class="sst-help">
          <div>
            <h4><i class="fas fa-lightbulb" style="color:#f6a723;"></i> How it works?</h4>
            <p>Main components must total exactly 100%. Additional components can exceed 100% and will be added on top of the main 100%.</p>
          </div>
          <div>
            <h4>Examples</h4>
            <div class="sst-ex">
              <span class="chip">CTC Amount</span><span class="op">=</span>
              <span class="chip">Main {{ number_format($sstMainTotal, 2) }}%</span><span class="op">+</span>
              <span class="chip add">Additional {{ number_format($sstAddTotal, 2) }}%</span><span class="op">=</span>
              <span class="chip">Total Cost to Company</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Preview sidebar -->
      <div class="sst-preview">
        <div class="sst-pv-title"><i class="fas fa-eye"></i>Preview Salary Structure</div>
        <div class="sst-pv-sub">See how this structure calculates for a given amount.</div>
        <div class="sst-pv-lbl">Enter Amount</div>
        <div class="sst-pv-amt"><input type="text" id="sstvAmount" placeholder="Enter amount"><button type="button" class="sst-pv-calc" id="sstvCalc">Calculate</button></div>
        <div class="sst-pv-lbl">View As</div>
        <div class="sst-pv-toggle"><button type="button" class="on" data-vmode="monthly">Monthly View</button><button type="button" data-vmode="annual">Annual View</button></div>
        <div class="sst-pv-box">
          <div style="font-size:12.5px;font-weight:700;color:var(--p);margin-bottom:8px;" id="sstvHead">Breakup Preview (Monthly)</div>
          <div class="sst-pv-row"><span>Main Components ({{ number_format($sstMainTotal,0) }}%)</span><span class="v" id="sstvMain">—</span></div>
          <div class="sst-pv-row"><span>Additional Components ({{ number_format($sstAddTotal,2) }}%)</span><span class="v" id="sstvAdd">—</span></div>
          <div class="sst-pv-row total"><span>Total Cost to Company</span><span class="v" id="sstvTotal">—</span></div>
          <div class="sst-donut-wrap">
            <svg width="80" height="80" viewBox="0 0 42 42">
              <circle cx="21" cy="21" r="15.9155" fill="none" stroke="#eef2f5" stroke-width="6"></circle>
              <circle id="sstvDonutMain" cx="21" cy="21" r="15.9155" fill="none" stroke="#16a34a" stroke-width="6" stroke-dasharray="80 20" stroke-dashoffset="25" transform="rotate(-90 21 21)"></circle>
              <circle id="sstvDonutAdd" cx="21" cy="21" r="15.9155" fill="none" stroke="#7239ea" stroke-width="6" stroke-dasharray="20 80" stroke-dashoffset="-55" transform="rotate(-90 21 21)"></circle>
            </svg>
            <div class="sst-leg">
              <div class="li"><span class="dot" style="background:#16a34a"></span>Main ({{ number_format($sstMainTotal,0) }}%)<span style="margin-left:auto;font-weight:600;color:var(--t)" id="sstvLegMain">—</span></div>
              <div class="li"><span class="dot" style="background:#7239ea"></span>Additional ({{ number_format($sstAddTotal,2) }}%)<span style="margin-left:auto;font-weight:600;color:var(--t)" id="sstvLegAdd">—</span></div>
            </div>
          </div>
        </div>
        <div class="sst-vchecks">
          <h5>Validation Checks</h5>
          <div class="ck {{ $sstMainValid ? 'ok' : 'warn' }}"><i class="fas {{ $sstMainValid ? 'fa-check-circle' : 'fa-exclamation-triangle' }}"></i><span>Main components total is exactly 100%</span></div>
          <div class="ck ok"><i class="fas fa-check-circle"></i><span>All formulas are valid</span></div>
          <div class="ck ok"><i class="fas fa-check-circle"></i><span>All required components have conditions</span></div>
          <div class="ck warn"><i class="fas fa-exclamation-triangle"></i><span>Additional components exceed 100% (Allowed)</span></div>
        </div>
      </div>
    </div>
  </div>

  <!-- Edit drawer (opens the redesigned edit page embedded, same page) -->
  <div class="sst-drawer-ov" id="sstDrawerOv" onclick="closeSalaryEditDrawer()"></div>
  <div class="sst-drawer" id="sstDrawer">
    <div class="sst-drawer-h">
      <span class="t">Edit Salary Template</span>
      <div class="sst-drawer-actions">
        <button type="button" id="sstSaveTemplateButton" class="btn btn-primary" onclick="submitSalaryTemplateFromDrawer()"><i class="far fa-save me-1"></i>Save Template</button>
        <button type="button" id="sstResetTemplateButton" class="btn" style="background:#5b21d6;color:#fff;" onclick="resetSalaryTemplateDrawer()" title="Load the Super Admin default components into this editor"><i class="fas fa-undo me-1"></i>Reset</button>
      </div>
      <button type="button" class="sst-drawer-x" onclick="closeSalaryEditDrawer()">&times;</button>
    </div>
    <iframe id="sstDrawerFrame" src="about:blank" title="Edit Salary Template"></iframe>
  </div>

  <script>
    (function () {
      function num(v){ v = parseFloat(String(v).replace(/[^0-9.\-]/g,'')); return isNaN(v) ? 0 : v; }
      function inr(n){ try { return '₹ ' + Math.round(n).toLocaleString('en-IN'); } catch(e){ return '₹ ' + Math.round(n); } }
      function setT(id,v){ var e=document.getElementById(id); if(e) e.textContent=v; }
      var mainPct = {{ $sstMainTotal }}, addPct = {{ $sstAddTotal }}, vmode='monthly';
      function pv(){
        var amt = num((document.getElementById('sstvAmount')||{}).value);
        var base = (vmode==='monthly') ? amt/12 : amt;
        var m = base*mainPct/100, a = base*addPct/100, tot = m+a;
        setT('sstvHead','Breakup Preview ('+(vmode==='monthly'?'Monthly':'Annual')+')');
        if (amt>0){ setT('sstvMain',inr(m)); setT('sstvAdd',inr(a)); setT('sstvTotal',inr(tot)); setT('sstvLegMain',inr(m)); setT('sstvLegAdd',inr(a)); }
        else { ['sstvMain','sstvAdd','sstvTotal','sstvLegMain','sstvLegAdd'].forEach(function(id){ setT(id,'—'); }); }
        var t=mainPct+addPct, mf=t>0?(mainPct/t)*100:100, af=100-mf;
        var dm=document.getElementById('sstvDonutMain'), da=document.getElementById('sstvDonutAdd');
        if(dm){ dm.setAttribute('stroke-dasharray', mf.toFixed(2)+' '+(100-mf).toFixed(2)); dm.setAttribute('stroke-dashoffset','25'); }
        if(da){ da.setAttribute('stroke-dasharray', af.toFixed(2)+' '+(100-af).toFixed(2)); da.setAttribute('stroke-dashoffset',(25-mf).toFixed(2)); }
      }
      document.addEventListener('input', function(e){ if(e.target && e.target.id==='sstvAmount') pv(); });
      document.addEventListener('click', function(e){
        var t = e.target.closest ? e.target.closest('[data-vmode]') : null;
        if(t){ vmode=t.getAttribute('data-vmode'); document.querySelectorAll('.sst-pv-toggle button').forEach(function(b){ b.classList.toggle('on', b===t); }); pv(); }
        if(e.target.id==='sstvCalc') pv();
      });
      pv();
    })();
    function openSalaryEditDrawer(){
      var f = document.getElementById('sstDrawerFrame');
      if (f && (!f.src || f.src === 'about:blank')) f.src = "{{ route('service_company.account.offerAmountChange', [encrypt(0)]) }}?embed=1";
      document.getElementById('sstDrawer').classList.add('show');
      document.getElementById('sstDrawerOv').classList.add('show');
      document.body.style.overflow = 'hidden';
    }
    // Intent: give the existing drawer actions immediate, non-blocking progress feedback while preserving their form and data behavior.
    function setSalaryDrawerActionLoading(buttonId, isLoading, loadingText){
      var button = document.getElementById(buttonId);
      if (!button) return;
      if (!button.dataset.idleHtml) button.dataset.idleHtml = button.innerHTML;
      button.disabled = isLoading;
      button.setAttribute('aria-busy', isLoading ? 'true' : 'false');
      button.innerHTML = isLoading
        ? '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>' + loadingText
        : button.dataset.idleHtml;
    }
    // The drawer header submits the existing embedded form, preserving its current validation and save endpoint.
    function submitSalaryTemplateFromDrawer(){
      var frame = document.getElementById('sstDrawerFrame');
      var save = frame && frame.contentWindow && frame.contentWindow.document.getElementById('save-form');
      if (save) {
        setSalaryDrawerActionLoading('sstSaveTemplateButton', true, 'Saving…');
        save.click();
      }
    }
    // Reset intentionally loads the Super Admin master components for review; the company data is unchanged until Save Template submits the existing form.
    function resetSalaryTemplateDrawer(){
      var frame = document.getElementById('sstDrawerFrame');
      if (frame) {
        setSalaryDrawerActionLoading('sstResetTemplateButton', true, 'Loading defaults…');
        frame.addEventListener('load', function completeSalaryTemplateReset(){
          setSalaryDrawerActionLoading('sstResetTemplateButton', false);
          frame.removeEventListener('load', completeSalaryTemplateReset);
        });
        frame.src = "{{ route('service_company.account.offerAmountChange', [encrypt(1)]) }}?embed=1";
      }
    }
    // The embedded editor dispatches this only after its existing AJAX request completes.
    window.addEventListener('salary-template-save-complete', function(){
      setSalaryDrawerActionLoading('sstSaveTemplateButton', false);
    });
    function closeSalaryEditDrawer(){
      document.getElementById('sstDrawer').classList.remove('show');
      document.getElementById('sstDrawerOv').classList.remove('show');
      document.body.style.overflow = '';
    }
  </script>
</div>
  </div>
  </div>
<!--end::Offer components-->

<script>
  // Intent: switch the existing candidate configuration panes directly because the vertical links are nested inside a Bootstrap tab pane.
  document.addEventListener('click', function (event) {
    var link = event.target.closest ? event.target.closest('.candidate-config-link') : null;
    if (!link) return;

    event.preventDefault();
    var target = document.querySelector(link.getAttribute('href'));
    if (!target) return;

    document.querySelectorAll('#offerterms, #offercomponent').forEach(function (pane) {
      pane.classList.remove('active', 'show');
    });
    target.classList.add('active', 'show');

    document.querySelectorAll('.candidate-config-link').forEach(function (item) {
      item.classList.toggle('active', item === link);
    });
    if (link.classList.contains('candidate-offer-template-link') && typeof setCandidateOfferTemplateContent === 'function') {
      setCandidateOfferTemplateContent(
        link.getAttribute('data-template-field'),
        link.getAttribute('data-template-label'),
        link.getAttribute('data-template-content') || '',
        true,
        link.getAttribute('data-template-type')
      );
    }
    document.querySelectorAll('.candidate-config-main-tab').forEach(function (item) {
      item.classList.add('active');
    });
  }, true);
</script>




{{-- Services & Commercials Management Tab — God-Level Modern Card + Sub-Card Enterprise Design System (Rule 20) --}}
<div class="tab-pane {{$services_tab}}" id="services" role="tabpanel">
  @include('service_company.account.v2.tabs.services')
</div>


        <!--begin::boost keyword Content-->
        <div class="tab-pane {{$boostkeyword_tab}}" id="boostkeyword" role="tabpanel">

           <!--begin::Overview header-->
                <div class="card-header cursor-pointer px-0">
                   <!--begin::Card title-->
                        <div class="card-title m-0">
                          <h3 class="fw-bolder m-0">Ads Settings</h3>
                        </div>
                      <!--end::Card title-->
                      <!--begin::Action-->
                        <a href="javascript:;" class="btn btn-primary align-self-center" id="editBtnCampaign">Create Campaign</a>
                    <!--ensd::Action-->
                </div>
            <!--end::Terms & Canditions header-->

            <table
                                                    class="table align-middle table-row-dashed fs-6 gy-5 table-custom-class dataTable no-footer"
                                                    id="myTable" role="grid" aria-describedby="myTable_info"
                                                    style="width: 1270px;">
                                                    <thead>
                                                        <tr class="fw-bolder text-muted" role="row">
                                                            
                                                            <th class="sorting_disabled">Sr.No.</th>
                                                            <th class="sorting_disabled">Status</th>
                                                            <th class="sorting_disabled">Campaign</th>
                                                            <th class="sorting_disabled">Keyword</th>
                                                            <th class="sorting_disabled">Country</th>
                                                            <th class="sorting_disabled">Type</th>
                                                            <th class="sorting_disabled">Click</th>
                                                            <th class="sorting_disabled">Cost per click</th>
                                                            <th class="sorting_disabled">Sales Coins</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="text-gray-600 fw-bold">

                                                      @if($boostkeywords)
                                                        @foreach ($boostkeywords as $key=> $boostkeyword)
                                                        @php
                                                              $salse_coins = DB::table('company_coins')->where('company_id','=',$boostkeyword->company_id)->where('event_type','=','sales')->first();

                                                              $chk = $disabled = '';
                                                              $status = $boostkeyword->status;
                                                              $status_text = 'Inactive';
                                                              $status_text_color = "text-danger";
                                                              if ($status == 1) {
                                                                  $chk = 'checked="checked"';
                                                                  $status_text = 'Active';
                                                                  $status_text_color = "text-success";
                                                              }
                                                              $coin_event_id = '';
                                                              if($boostkeyword->country_id==101 && $boostkeyword->campaign_type==0){
                                                                $coin_event_id= '19';
                                                              }elseif($boostkeyword->country_id==101 && $boostkeyword->campaign_type==1){
                                                                $coin_event_id= '21';
                                                              }elseif($boostkeyword->country_id!=101 && $boostkeyword->campaign_type==1){
                                                                $coin_event_id= '20';
                                                              }elseif($boostkeyword->country_id!=101 && $boostkeyword->campaign_type==1){
                                                                $coin_event_id= '22';
                                                              }

                                                              $deductionCoins = DB::table('coin_order_events')
                                                              ->where('coin_order_id','=',$salse_coins->coin_order_id)
                                                              ->where('coin_event_id','=',$coin_event_id)
                                                              ->where('event_type','=','sales')
                                                              ->first();

                                                        @endphp


                                                        <tr class="fw-bolder text-muted" role="row">
                                                            <td class="sorting_disabled">{{($key+1)}}.</td>
                                                            <td class="sorting_disabled">
                                                              <div class="form-check form-switch form-switch-sm form-check-custom form-check-solid">
                                                                <input class="form-check-input chkStatus" type="checkbox" value="active" name="staus" {{@$chk}}  onclick="boostHandleClick(this, {{$boostkeyword->id}})" {{$disabled}} >
                                                              </div>
                                                                                      <span class="fw-bold d-block fs-7 {{$status_text_color }} status_text">{{$status_text}}</span>
                                                            </td>
                                                            <td class="sorting_disabled">{{$boostkeyword->campaign_name}}</td>
                                                            <td class="sorting_disabled">{{$boostkeyword->skill_name}}</td>
                                                            <td class="sorting_disabled">{{$boostkeyword->country_name}}</td>
                                                            <td class="sorting_disabled">{{$boostkeyword->campaign_type==1 ? 'Contract' : 'Full-time'}}</td>
                                                            <td class="sorting_disabled">{{$boostkeyword->clickCount}}</td>
                                                            <td class="sorting_disabled">{{($deductionCoins) ? $deductionCoins->deductions : ''}} Sales Coin</td>
                                                            <td class="sorting_disabled">{{$salse_coins->coins}}</td>
                                                          </tr>
                                                        @endforeach


                                                      @endif

                                                    </tbody>
                                                  </table>


            {{-- <form id="boost_keyword_form" class="form" action="#">
                    <div class="card-body px-0 py-7">
                            <div class="col-lg-12 mt-7">
                                <input type="hidden" name="keyword_compant_id" id="keyword_compant_id" value="{{ @$account->id }}" />
                                <label class="form-label required fw-bolder">Enter Keywords:</label>
                                <input class="form-control form-control-solid keyword_compant" name="tags" value="{{ $boostkeywords }}" placeholder="Enter Keywords" value="" id="tags"/>
                                <span class="form-text text-muted">Please select your keywords</span> 
                            </div>
                                <!--end::Tags-->
                    </div>

                    <div class="text-center pt-15">
                        <button type="submit" id="operational_top_up_new_submit" class="btn btn-primary">
                            <span class="indicator-label indicator-label-top-up">Submit</span>
                            <span class="indicator-progress indicator-progress-top-up">Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                            </span>
                        </button>
                    </div>
            </form> --}}
          </div>

    <!--end::boost keyword Content-->




<!--begin::Settings Content-->
<div class="tab-pane" id="settings" role="tabpanel">
  @include('service_company.account.components.tab-general-settings')
</div>
<!--end::Settings Content-->

<!--begin::Notifications Content-->
<div class="tab-pane csn" id="notifications" role="tabpanel">
  @if(in_array("view_settings", $userPermissions))
    @php
      // Intent: document the existing recruitment and sales routing coverage without adding new persistence fields.
      $recruitmentNotificationItems = [
        ['Candidate Stages', 'Candidate moves across applied, shortlisted, interview, offered and other stages.'],
        ['Agencies Status', 'Agency performance, submission and status changes.'],
        ['Offers Update', 'Offer creation, approval, release, acceptance and decline.'],
        ['Joining Update', 'Candidate joining, postponement and cancellation updates.'],
        ['Bench Candidate Request', 'Bench candidates requested for a job.'],
        ['Team Member Status', 'Team invitations, role changes and account status updates.'],
        ['Bench Notification on Jobs', 'Bench candidates submitted or placed on jobs.'],
        ['Additional Notifications', 'Notes, comments and other important recruitment actions.'],
      ];
      $salesNotificationItems = [
        ['New Job Request from Direct Client', 'A new job request raised by a direct client.'],
        ['Bench Added on Client Jobs', 'Bench candidates added or submitted on client jobs.'],
        ['Subscription Status', 'Subscription activation, renewal, expiry and plan changes.'],
        ['Client Communication', 'Client messages, feedback and important updates.'],
        ['Invoice & Payment Status', 'Invoices, payments, due dates and payment failures.'],
        ['Contract & Agreement Updates', 'Contract creation, renewal, expiry and amendments.'],
        ['Low Coins / Wallet Alerts', 'Low coin balance and wallet actions requiring attention.'],
        ['Additional Sales Alerts', 'Other important sales updates and system alerts.'],
      ];
    @endphp
    <form id="notification_content" class="form" method="post" novalidate="novalidate">
      @csrf
      <input type="hidden" name="pagename" value="settings">
      <div class="csn-grid">
        <section class="csn-card">
          <div class="csn-card-head">
            <span class="csn-head-icon csn-purple" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg></span>
            <div><div class="csn-card-title">Recruitment Notifications</div><div class="csn-card-sub">Manage email routing for all hiring and recruitment activities.</div></div>
          </div>
          <div class="csn-card-body">
            <label class="csn-label" for="recuitment_email">Primary Email</label>
            <div class="csn-email-wrap">
              <input type="email" name="recuitment_email" id="recuitment_email" value="{{ (@$notification['Recuitment']->email) ? @$notification['Recuitment']->email : @$account->company_email }}" placeholder="Enter recruitment email" class="csn-email" required maxlength="254" inputmode="email" autocomplete="email" autocapitalize="none" spellcheck="false">
              <span class="csn-email-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg></span>
            </div>
            <div id="recuitment_email_typo_hint" class="email-typo-hint" style="display: none; margin-top: 5px; font-size: 11.5px; color: #b45309; background: #fef3c7; border: 1px solid #fde68a; border-radius: 6px; padding: 4px 8px;"></div>
            <div class="csn-category-label">Notification Categories Covered</div>
            <div class="csn-list">
              @foreach($recruitmentNotificationItems as $notificationIndex => $notificationItem)
                <div class="csn-item">
                  <span class="csn-item-icon csn-purple" aria-hidden="true"><svg viewBox="0 0 24 24"><rect x="5" y="4" width="14" height="16" rx="2"/><path d="M9 4V2h6v2M9 12l2 2 4-4"/></svg></span>
                  <div><div class="csn-item-title">{{ $notificationIndex + 1 }}. {{ $notificationItem[0] }}</div><div class="csn-item-desc">{{ $notificationItem[1] }}</div></div>
                  <span class="csn-item-arrow" aria-hidden="true">&rsaquo;</span>
                </div>
              @endforeach
            </div>
          </div>
        </section>

        <section class="csn-card csn-sales">
          <div class="csn-card-head">
            <span class="csn-head-icon csn-green" aria-hidden="true"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="7"/><path d="M12 7v5l3 2M8 3 5 6M16 3l3 3"/></svg></span>
            <div><div class="csn-card-title">Sales Notifications</div><div class="csn-card-sub">Manage email routing for sales and client related activities.</div></div>
          </div>
          <div class="csn-card-body">
            <label class="csn-label" for="sales_email">Primary Email</label>
            <div class="csn-email-wrap">
              <input type="email" name="sales_email" id="sales_email" value="{{ (@$notification['Sales']->email) ? @$notification['Sales']->email : @$account->company_email }}" placeholder="Enter sales email" class="csn-email" required maxlength="254" inputmode="email" autocomplete="email" autocapitalize="none" spellcheck="false">
              <span class="csn-email-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg></span>
            </div>
            <div id="sales_email_typo_hint" class="email-typo-hint" style="display: none; margin-top: 5px; font-size: 11.5px; color: #b45309; background: #fef3c7; border: 1px solid #fde68a; border-radius: 6px; padding: 4px 8px;"></div>
            <div class="csn-category-label">Notification Categories Covered</div>
            <div class="csn-list">
              @foreach($salesNotificationItems as $notificationIndex => $notificationItem)
                <div class="csn-item">
                  <span class="csn-item-icon csn-green" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M4 19V9l8-5 8 5v10H4Z"/><path d="M9 19v-5h6v5"/></svg></span>
                  <div><div class="csn-item-title">{{ $notificationIndex + 1 }}. {{ $notificationItem[0] }}</div><div class="csn-item-desc">{{ $notificationItem[1] }}</div></div>
                  <span class="csn-item-arrow" aria-hidden="true">&rsaquo;</span>
                </div>
              @endforeach
            </div>
          </div>
        </section>
      </div>
      @if(in_array("edit_settings", $userPermissions))
        <div class="csn-actions">
          <button type="reset" class="btn btn-secondary">Discard Changes</button>
          <button type="button" class="btn btn-primary" id="saveNotificationSettings"><span class="csn-save-spinner" aria-hidden="true"></span><span class="csn-save-label">Save Changes</span></button>
        </div>
      @endif
    </form>
  @endif
</div>
<!--end::Notifications Content-->
</div>
<!--end::Body-->
</div>
<!--end::Tabs-->
<!--end::Card-->
</div>
<!--end::Tabs-->
</div>
<!--end::Container-->
</div>


<!--begin::Add Interview Sub-Stage modal (Rule 20 Modern Card Standard)-->
<div class="modal fade" id="add_interview_sub_stage" tabindex="-1" aria-modal="true" role="dialog">
  <div class="modal-dialog modal-dialog-centered mw-550px">
    <div class="modal-content rounded-3 shadow border-0" style="border-radius:12px;">
      <div class="modal-header border-bottom py-4 px-6 d-flex align-items-center justify-content-between">
        <h3 class="modal-title fw-bold text-dark fs-5 d-flex align-items-center gap-2 mb-0">
          <i class="fas fa-plus-circle text-primary fs-5"></i>
          <span class="h1_title">Add Pipeline Sub-Stage</span>
        </h3>
        <div class="btn btn-sm btn-icon btn-active-light-primary" data-dismiss="modal" data-bs-dismiss="modal" style="cursor:pointer;">
          <i class="fas fa-times fs-6 text-muted"></i>
        </div>
      </div>
      <div class="modal-body py-5 px-6">
        <form id="add_interview_sub_stage_form" class="form" onsubmit="return false;">
          <input type="hidden" name="ltb_stage" id="ltb_stage" value="1" />
          <input type="hidden" name="ltb_root" id="ltb_root" value="root_sourcing" />
          <input type="hidden" name="ltb_class" id="ltb_class" value="bg-secondary" />
          
          <div class="mb-4">
            <label class="form-label fs-7 fw-bolder text-dark mb-2">Master Pipeline Stage <span class="text-danger">*</span></label>
            <select class="form-select form-select-solid" id="ips_modal_stage_picker">
              <option value="1" data-root="root_sourcing" data-class="bg-secondary">01 - Sourcing</option>
              <option value="2" data-root="root_screening" data-class="screening-light">02 - Screening</option>
              <option value="3" data-root="root_evaluation" data-class="evaluation-light">03 - Evaluation</option>
              <option value="4" data-root="root_negotation" data-class="negotation-light">04 - Negotiation</option>
              <option value="5" data-root="root_offer" data-class="offer-light">05 - Offer</option>
              <option value="6" data-root="root_joined" data-class="joined-light">06 - Joined</option>
              <option value="7" data-root="root_rejected" data-class="rejected-light">07 - Rejected</option>
            </select>
          </div>

          <div class="mb-4">
            <label class="form-label fs-7 fw-bolder text-dark mb-2"> <span class="required lbl_title">Sub-Stage Name</span> <span class="text-danger">*</span></label>
            <input type="text" class="form-control form-control-solid" placeholder="e.g. L1 Technical Interview, Client Assessment..." name="ltb_stage_name" id="ltb_stage_name" autocomplete="off">
          </div>

          <div class="alert alert-light-primary d-flex align-items-center p-3 mb-4 rounded border border-primary border-opacity-20" style="background:#f0f9ff; border:1px solid #bae6fd;">
            <i class="fas fa-info-circle text-primary fs-5 me-3"></i>
            <div class="fs-8 text-gray-700">Sub-stages appear immediately across Candidate Tracking, Kanban workflows, and Client Drawers.</div>
          </div>

          <div class="d-flex justify-content-end gap-3 pt-2">
            <button type="button" class="btn btn-light" data-dismiss="modal" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary" id="ltb_submit"><i class="fas fa-save me-1"></i> Save Sub-Stage</button>
          </div>
        </form>
        <div id="add_interview_sub_stage_form_loader" class="text-center py-5" style="display:none;"></div>
      </div>
    </div>
  </div>
</div>
<!--end::Add Interview Sub-Stage modal--> 

<!--begin::Google config-->
<div class="modal fade show" id="google_config" tabindex="-1" aria-modal="true" role="dialog">
  <div class="modal-dialog modal-dialog-centered mw-650px">
    <div class="modal-content rounded">
      <div class="modal-header pb-0 border-0 justify-content-end">
        <div class="btn btn-sm btn-icon btn-active-color-primary close" data-dismiss="modal" data-bs-dismiss="modal"> <span class="svg-icon svg-icon-1">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black"></rect>
            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black"></rect>
          </svg>
          </span> </div>
      </div>
      <div class="modal-body scroll-y px-5 px-lg-5 pt-0 pb-5">
        <form id="google_frm" name="google_frm" class="form fv-plugins-bootstrap5 fv-plugins-framework" action="post">
          <div class="alert alert-success alert-dismissible fade show" id="google_success_alert" style="display:none"> <strong>Success!</strong> Update goole configuration successfully.
            <button type="button" class="btn-close" data-dismiss="alert"></button>
          </div>
          <div class="alert alert-danger alert-dismissible fade show" id="google_warning_alert" style="display:none"> <strong>Warning!</strong> Goole configuration can not be blank.
            <button type="button" class="btn-close" data-dismiss="alert"></button>
          </div>
          <div class="mb-11 text-center">
            <h1 class="">Google Configuration Settings</h1>
          </div>
          <div class="google_load" id="google_load"></div>
        </form>
      </div>
    </div>
  </div>
</div>
<!--end::Google config-->

<!--begin::Add department modal-->
<div class="modal fade show" id="add_department" tabindex="-1" aria-modal="true" role="dialog">
  <div class="modal-dialog modal-dialog-centered mw-650px">
    <div class="modal-content rounded">
      <div class="modal-header pb-0 border-0 justify-content-end">
        <div class="btn btn-sm btn-icon btn-active-color-primary" data-dismiss="modal"> <span class="svg-icon svg-icon-1">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black"></rect>
            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black"></rect>
          </svg>
          </span> </div>
      </div>
      <div class="modal-body scroll-y px-5 px-lg-5 pt-0 pb-5">
        <form id="add_department_form" class="form fv-plugins-bootstrap5 fv-plugins-framework" action="post">
          <div class="alert alert-success alert-dismissible fade show" id="dept_success_alert" style="display:none"> <strong>Success!</strong> Department Added successfully.
            <button type="button" class="btn-close" data-dismiss="alert"></button>
          </div>
          <div class="alert alert-danger alert-dismissible fade show" id="dept_warning_alert" style="display:none"> <strong>Warning!</strong> Department can not be blank.
            <button type="button" class="btn-close" data-dismiss="alert"></button>
          </div>
          <div class="mb-11 text-center">
            <h1 class="">Add Department</h1>
          </div>
          <div class="d-flex flex-column mb-8 fv-row fv-plugins-icon-container fv-plugins-bootstrap5-row-invalid">
            <label class="d-flex align-items-center fs-6 fw-bold mb-2"> <span class="required">Department Name</span> </label>
            <input type="text" class="form-control form-control-solid" placeholder="Enter Department Name" name="dept_name" id="dept_name" maxlength="35">
            <div class="fv-plugins-message-container invalid-feedback"> </div>
          </div>
          <div class="text-center">
            <button type="button" class="btn btn-secondary me-3" data-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary submit_department">Submit</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<!--end::Add department modal-->

<!--begin::Zoom config-->
<div class="modal fade show" id="zoom_config" tabindex="-1" aria-modal="true" role="dialog">
  <div class="modal-dialog modal-dialog-centered mw-650px">
    <div class="modal-content rounded">
      <div class="modal-header pb-0 border-0 justify-content-end">
        <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal" data-dismiss="modal"> <span class="svg-icon svg-icon-1">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black"></rect>
            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black"></rect>
          </svg>
          </span> </div>
      </div>
      <div class="modal-body scroll-y px-5 px-lg-5 pt-0 pb-5">
        <form id="zoom_configfrm" class="form fv-plugins-bootstrap5 fv-plugins-framework zoom_configfrm" action="post">
          <div class="alert alert-success alert-dismissible fade show" id="zoom_success_alert" style="display:none"> <strong>Success!</strong> Update zoom configuration successfully.
            <button type="button" class="btn-close" data-dismiss="alert"></button>
          </div>
          <div class="alert alert-danger alert-dismissible fade show" id="zoom_warning_alert" style="display:none"> <strong>Warning!</strong> Zoom configuration can not be blank.
            <button type="button" class="btn-close" data-dismiss="alert"></button>
          </div>
          <div class="mb-11 text-center">
            <h1 class="">Zoom Configuration Settings</h1>
          </div>
          <div class="zoom_load" id="zoom_load"></div>
        </form>
      </div>
    </div>
  </div>
</div>
<!--begin::Zoom config-->

<!--begin::Google Sheet config-->
<div class="modal fade show" id="google_sheet_config" tabindex="-1" aria-modal="true" role="dialog">
  <div class="modal-dialog modal-dialog-centered mw-650px">
    <div class="modal-content rounded">
      <div class="modal-header pb-0 border-0 justify-content-end">
        <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal" data-dismiss="modal"> <span class="svg-icon svg-icon-1">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black"></rect>
            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black"></rect>
          </svg>
          </span> </div>
      </div>
      <div class="modal-body scroll-y px-5 px-lg-5 pt-0 pb-5">
        <form id="google_sheet_configfrm" class="form fv-plugins-bootstrap5 fv-plugins-framework google_sheet_configfrm" action="post">
          <div class="alert alert-success alert-dismissible fade show" id="google_sheet_success_alert" style="display:none"> <strong>Success!</strong> Update Google Sheet configuration successfully.
            <button type="button" class="btn-close" data-dismiss="alert"></button>
          </div>
          <div class="alert alert-danger alert-dismissible fade show" id="google_sheet_warning_alert" style="display:none"> <strong>Warning!</strong> Google Sheet configuration can not be blank.
            <button type="button" class="btn-close" data-dismiss="alert"></button>
          </div>
          <div class="mb-11 text-center">
            <h1 class="">Google Sheet Configuration Settings</h1>
          </div>
          <div class="google_sheet_load" id="google_sheet_load"></div>
        </form>
      </div>
    </div>
  </div>
</div>
<!--begin::Zoom config-->


<div id="kt_activitiesJob_toggle"></div>
<div id="kt_activitiesJob" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesJob_toggle" data-kt-drawer-close="#kt_activitiesJob_close">
  <div class="card shadow-none rounded-0 w-100">
    <div class="card-header px-5 py-0" id="kt_activities_header">
      <div class="d-flex align-items-center py-1">
        <h3 class="card-title fw-bolder text-dark"></h3>
        <span class="h-20px border-gray-200 border-start mx-4 d-none"></span> </div>
      <div class="card-toolbar">
        <button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5" id="kt_activitiesJob_close"> <span class="svg-icon svg-icon-1">
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
          <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)" fill="#000000">
            <rect fill="#000000" x="0" y="7" width="16" height="2" rx="1" />
            <rect fill="#000000" opacity="0.5" transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)" x="0" y="7" width="16" height="2" rx="1" />
          </g>
        </svg>
        </span> </button>
      </div>
    </div>
    <div class="card-body position-relative px-0 py-0" id="kt_activitiesJob_body"></div>
  </div>
</div>



@endsection
@push('footer-script')

<script src="{{ asset('demo/cdn/js/dataTables.fixedHeader.min.js') }}{{ TM_VERSION }}"></script>
<script src="{{ asset('demo/v7/js/pages/crud/forms/widgets/select2.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('demo/cdn/js/bootstrap-select.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('demo/ckeditor/ckeditor.js') }}"></script>
<script>
// =========================================================================
// JeroEmailBoxEngine: 100% Client-First (Zero Server Strain) Email Engine
// RFC 5321 (254 char), Deterministic Sanitizer, 3-State Typo Detection
// =========================================================================
window.JeroEmailBoxEngine = window.JeroEmailBoxEngine || (function () {
    var CONSUMER_DOMAIN_TYPOS = {
        'gmai.com': 'gmail.com', 'gamil.com': 'gmail.com', 'gmial.com': 'gmail.com',
        'gmaill.com': 'gmail.com', 'gmaik.com': 'gmail.com', 'gmeil.com': 'gmail.com',
        'gmaul.com': 'gmail.com', 'gmal.com': 'gmail.com', 'gmaili.com': 'gmail.com',
        'yaho.com': 'yahoo.com', 'yahooo.com': 'yahoo.com', 'ymail.com': 'yahoo.com',
        'outlok.com': 'outlook.com', 'outloo.com': 'outlook.com',
        'hotmial.com': 'hotmail.com', 'hotmai.com': 'hotmail.com',
        'iclou.com': 'icloud.com', 'iclod.com': 'icloud.com',
        'redifmail.com': 'rediffmail.com', 'rediffmial.com': 'rediffmail.com'
    };

    var CONSUMER_TLD_TYPOS = {
        'con': 'com', 'cmo': 'com', 'ocm': 'com', 'cpm': 'com',
        'comm': 'com', 'coom': 'com', 'ent': 'net', 'og': 'org', 'ogr': 'org'
    };

    return {
        cleanDeterministic: function (raw) {
            if (!raw) return '';
            var str = String(raw).trim();
            str = str.replace(/[\u00A0\u1680\u180E\u2000-\u200B\u202F\u205F\u3000\uFEFF]/g, '');
            str = str.replace(/^['"`<]+|['"`>]+$/g, '');
            str = str.replace(/^mailto:\s*/i, '');
            var match = str.match(/<([^>]+)>/);
            if (match && match[1]) {
                str = match[1];
            }
            str = str.replace(/[.,;:]+$/, '');
            return str.toLowerCase().replace(/\s+/g, '');
        },

        getSuggestion: function (email) {
            if (!email || email.indexOf('@') === -1) return null;
            var parts = email.split('@');
            if (parts.length !== 2) return null;

            var local = parts[0];
            var domain = parts[1];

            if (domain.indexOf(',') !== -1) {
                var commaFixed = domain.replace(/,([a-zA-Z]{2,})$/, '.$1');
                return {
                    suggested: local + '@' + commaFixed,
                    message: 'Did you mean <strong>' + local + '@' + commaFixed + '</strong>?'
                };
            }

            if (CONSUMER_DOMAIN_TYPOS[domain]) {
                var fixedEmail = local + '@' + CONSUMER_DOMAIN_TYPOS[domain];
                return {
                    suggested: fixedEmail,
                    message: 'Did you mean <strong>' + fixedEmail + '</strong>?'
                };
            }

            var dParts = domain.split('.');
            if (dParts.length >= 2) {
                var root = dParts[0];
                var tld = dParts[dParts.length - 1];
                if (CONSUMER_TLD_TYPOS[tld] && (root === 'gmail' || root === 'yahoo' || root === 'outlook' || root === 'hotmail')) {
                    dParts[dParts.length - 1] = CONSUMER_TLD_TYPOS[tld];
                    var fixed = local + '@' + dParts.join('.');
                    return {
                        suggested: fixed,
                        message: 'Did you mean <strong>' + fixed + '</strong>?'
                    };
                }
            }

            return null;
        },

        validateSyntax: function (email) {
            if (!email || email.trim() === '') {
                return { isValid: false, error: 'Email address is required.' };
            }

            if (email.length > 254) {
                return { isValid: false, error: 'Email exceeds maximum allowed length of 254 characters.' };
            }

            if (/\s/.test(email)) {
                return { isValid: false, error: 'Email address cannot contain spaces.' };
            }

            if (email.indexOf(',') !== -1) {
                if (email.indexOf('@') !== -1 && email.split('@')[1].indexOf(',') !== -1) {
                    return { isValid: false, error: 'Domain name contains a comma instead of a period (e.g. gmail,com).' };
                }
                return { isValid: false, error: 'Email address cannot contain commas.' };
            }

            var parts = email.split('@');
            if (parts.length < 2) {
                return { isValid: false, error: 'Email is missing the "@" symbol.' };
            }
            if (parts.length > 2) {
                return { isValid: false, error: 'Email contains more than one "@" symbol.' };
            }

            var local = parts[0];
            var domain = parts[1];

            if (local.length === 0) {
                return { isValid: false, error: 'Email username cannot be empty.' };
            }
            if (local.length > 64) {
                return { isValid: false, error: 'Email username cannot exceed 64 characters.' };
            }

            if (local.indexOf('..') !== -1 || domain.indexOf('..') !== -1) {
                return { isValid: false, error: 'Email cannot contain consecutive dots ("..").' };
            }

            if (local.startsWith('.') || local.endsWith('.')) {
                return { isValid: false, error: 'Email username cannot start or end with a dot.' };
            }
            if (domain.startsWith('.') || domain.endsWith('.')) {
                return { isValid: false, error: 'Domain name cannot start or end with a dot.' };
            }

            if (domain.indexOf('_') !== -1) {
                return { isValid: false, error: 'Domain name cannot contain underscores (use hyphens instead).' };
            }

            if (!/^[a-z0-9]([a-z0-9-]*[a-z0-9])?(\.[a-z0-9]([a-z0-9-]*[a-z0-9])?)+$/.test(domain)) {
                return { isValid: false, error: 'Domain name format is invalid (letters, numbers, and hyphens only).' };
            }

            var tld = domain.split('.').pop();
            if (!tld || tld.length < 2 || /^\d+$/.test(tld)) {
                return { isValid: false, error: 'Domain must end with a valid extension (e.g. .com, .org).' };
            }

            return { isValid: true, error: null };
        }
    };
})();

  $('input[name="app_duplicate"]').on('change', function () {
      $('input[name="app_duplicate"]').removeAttr('style');

      if ($(this).is(':checked')) {
          $(this).css({
              'border-color': 'transparent',
              'background-color': 'currentColor'
          });
      }
  }).trigger('change');

if ( CKEDITOR.env.ie && CKEDITOR.env.version < 9 )
            
            CKEDITOR.tools.enableHtml5Elements( document );
            CKEDITOR.config.height 		=	'auto';
            CKEDITOR.config.width 		=	'auto';

            CKEDITOR.plugins.add('customDropdown', {
                  icons: 'customDropdown',
                  init: function (editor) {
                      editor.ui.addRichCombo('customFields', {
                          label: 'Insert Field',
                          title: 'Insert Field',
                          voiceLabel: 'Insert Field',
                          className: 'cke_format',
                          multiSelect: false,

                          panel: {
                              css: [CKEDITOR.skin.getPath('editor')].concat(editor.config.contentsCss),
                              voiceLabel: editor.lang.panelVoiceLabel
                          },

                          init: function () {
                              // Add options to the dropdown
                              this.add('CANDIDATE_NAME', 'Candidate Name');
                              this.add('CANDIDATE_EMAIL', 'Candidate Email');
                              this.add('CANDIDATE_MOBILE', 'Candidate Mobile No.');
                              this.add('COMPANY_NAME', 'Company Name');
                              this.add('JOB_TITLE', 'Job Position');
                              this.add('JOINING_DATE', 'Joining date');
                              this.add('AGENCY_NAME', 'Agency Name');
                              this.add('AGENCY_ADDRESS', 'Agency Address');
                              this.add('COMPANY_NAME', 'Company Name');
                              this.add('COMPANY_ADDRESS', 'Company Address');
                              this.add('TODAY_DATE', 'Today Date');
                          },

                          onClick: function (value) {
                              // Insert the selected field into the editor
                              editor.insertText('[' + value + ']');
                          }
                      });
                  }
            });
            


            
            var initSample 				= ( function() 
            {
                    var wysiwygareaAvailable 	= isWysiwygareaAvailable(),
                    isBBCodeBuiltIn 			= !!CKEDITOR.plugins.get( 'bbcode' );
            
                return function() {
                                            var editorElement = CKEDITOR.document.getById( 'kt_docs_tinymce_hidden' );
                                    
                                            if ( isBBCodeBuiltIn ) {
                                                editorElement.setHtml(
                                                    'Hello world!\n\n' +
                                                    'I\'m an instance of [url=https://ckeditor.com]CKEditor[/url].'
                                                );
                                            }
                                            if ( wysiwygareaAvailable ) {
                                                // CKEDITOR.replace( 'kt_docs_tinymce_hidden' );
                                                CKEDITOR.replace('kt_docs_tinymce_hidden', {
                                                  extraPlugins: 'customDropdown', // Add the custom dropdown plugin
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
                                                      { name: 'colors' },
                                                      { name: 'customFields', groups: ['customFields'] }
                                                  ]
                                                });
                                            } else {
                                                editorElement.setAttribute( 'contenteditable', 'true' );   
                                                CKEDITOR.inline( 'kt_docs_tinymce_hidden' );
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


            var initSampleoffer 				= ( function() 
            {
                    var wysiwygareaAvailable 	= isWysiwygareaAvailable(),
                    isBBCodeBuiltIn 			= !!CKEDITOR.plugins.get( 'bbcode' );
            
                return function() {
                                            var editorElement = CKEDITOR.document.getById( 'kt_docs_tinymce_hiddenoffer' );
                                    
                                            if ( isBBCodeBuiltIn ) {
                                                editorElement.setHtml(
                                                    'Hello world!\n\n' +
                                                    'I\'m an instance of [url=https://ckeditor.com]CKEditor[/url].'
                                                );
                                            }
                                            if ( wysiwygareaAvailable ) {
                                                // CKEDITOR.replace( 'kt_docs_tinymce_hiddenoffer' );
                                                CKEDITOR.replace('kt_docs_tinymce_hiddenoffer', {
                                                  extraPlugins: 'customDropdown', // Add the custom dropdown plugin
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

                                            } else {
                                                editorElement.setAttribute( 'contenteditable', 'true' );   
                                                CKEDITOR.inline( 'kt_docs_tinymce_hiddenoffer' );
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

            initSampleoffer();


            var initSampleagencyoffer = ( function()
            {
                    var wysiwygareaAvailable = isWysiwygareaAvailable(),
                    isBBCodeBuiltIn = !!CKEDITOR.plugins.get( 'bbcode' );

                return function() {
                                            var editorElement = CKEDITOR.document.getById( 'kt_docs_tinymce_hiddenagencyoffer' );

                                            if ( isBBCodeBuiltIn ) {
                                                editorElement.setHtml(
                                                    'Hello world!\n\n' +
                                                    'I\'m an instance of [url=https://ckeditor.com]CKEditor[/url].'
                                                );
                                            }
                                            if ( wysiwygareaAvailable ) {
                                                CKEDITOR.replace('kt_docs_tinymce_hiddenagencyoffer', {
                                                  extraPlugins: 'customDropdown',
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
                                            } else {
                                                editorElement.setAttribute( 'contenteditable', 'true' );
                                                CKEDITOR.inline( 'kt_docs_tinymce_hiddenagencyoffer' );
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

            initSampleagencyoffer();




document.getElementById('add_interview_sub_stage_form').addEventListener('keydown', function(event) {
    if (event.key === 'Enter') {
      event.preventDefault(); 
    }
  });

  document.addEventListener('DOMContentLoaded', function() {
    var editBtn = document.getElementById('editBtn'); 
    var viewTerms = document.getElementById('viewTerms');
    var editTerms = document.getElementById('editTerms');
    var agreementOverlay = document.getElementById('agencyAgreementDrawerOverlay');
    var agreementClose = document.getElementById('closeAgencyAgreementDrawer');
    var updateCompanyTermsTop = document.getElementById('updateCompanyTermsTop'); 
    var setToDefaultCompanyTermsTop = document.getElementById('setToDefaultCompanyTermsTop'); 
    if (updateCompanyTermsTop) updateCompanyTermsTop.style.display = "none";
    if (setToDefaultCompanyTermsTop) setToDefaultCompanyTermsTop.style.display = "none";

    // Intent: open the existing agency agreement editor inside the new drawer without changing the existing save/reset API.
    window.openAgencyAgreementDrawer = function() {
        if (!editTerms || !agreementOverlay) return;
        if (viewTerms) viewTerms.style.display = "block";
        editTerms.querySelectorAll('.updateCompanyTermsTop, .setToDefaultCompanyTermsTop').forEach(function(actionButton) {
            actionButton.style.display = "inline-flex";
        });
        editTerms.style.display = "block";
        editTerms.classList.add('open');
        editTerms.setAttribute('aria-hidden', 'false');
        agreementOverlay.classList.add('show');
        document.body.classList.add('modal-open');
        setTimeout(function() {
            if (window.CKEDITOR && CKEDITOR.instances.kt_docs_tinymce_hidden) {
                var agreementHtml = CKEDITOR.instances.kt_docs_tinymce_hidden.getData() || '';
                var textOnly = agreementHtml.replace(/<[^>]+>/g, ' ').replace(/\s+/g, ' ').trim();
                var words = textOnly ? textOnly.split(' ').length : 0;
                var wordNode = document.getElementById('agaWordCount');
                var charNode = document.getElementById('agaCharCount');
                if (wordNode) wordNode.textContent = words;
                if (charNode) charNode.textContent = textOnly.length;
            }
        }, 100);
    };

    window.closeAgencyAgreementDrawer = function() {
        if (!editTerms || !agreementOverlay) return;
        editTerms.classList.remove('open');
        editTerms.setAttribute('aria-hidden', 'true');
        editTerms.style.display = "none";
        agreementOverlay.classList.remove('show');
        document.body.classList.remove('modal-open');
        if (viewTerms) viewTerms.style.display = "block";
        if (editBtn) editBtn.innerHTML = '<svg viewBox="0 0 24 24"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>Edit Agreement';
    };
    
    if (editBtn) editBtn.addEventListener('click', function(event) {
        event.preventDefault();
        window.openAgencyAgreementDrawer();
    });
    if (agreementOverlay) agreementOverlay.addEventListener('click', window.closeAgencyAgreementDrawer);
    if (agreementClose) agreementClose.addEventListener('click', window.closeAgencyAgreementDrawer);
});

// Intent: keep the candidate-offer preview in place while the existing CKEditor and its save flow operate in a focused drawer.
function openOfferLetterDrawer() {
    var drawer = document.getElementById('editTermsoffer');
    var overlay = document.getElementById('offerLetterDrawerOverlay');
    if (!drawer || !overlay) return;

    drawer.style.display = 'block';
    overlay.classList.add('show');
    document.body.style.overflow = 'hidden';
    window.setTimeout(function () {
        var editor = window.CKEDITOR && CKEDITOR.instances.kt_docs_tinymce_hiddenoffer;
        if (editor && editor.resize) editor.resize('100%', Math.max(320, window.innerHeight - 410), true);
        updateSettingsOfferPreview();
    }, 60);
}
function closeOfferLetterDrawer() {
    var drawer = document.getElementById('editTermsoffer');
    var overlay = document.getElementById('offerLetterDrawerOverlay');
    if (drawer) drawer.style.display = 'none';
    if (overlay) overlay.classList.remove('show');
    document.body.style.overflow = '';
}
function updateSettingsOfferPreview() {
    var preview = document.getElementById('settings_offer_preview_content');
    var editor = window.CKEDITOR && CKEDITOR.instances.kt_docs_tinymce_hiddenoffer;
    if (preview && editor) preview.innerHTML = editor.getData();
}
function getActiveCandidateOfferLink() {
    return document.querySelector('.candidate-offer-template-link.active') || document.querySelector('.candidate-offer-template-link');
}
function decodeCandidateOfferTemplateContent(html) {
    if (!html) return '';
    try {
        return JSON.parse(html);
    } catch (e) {
        return html;
    }
}
function setCandidateOfferTemplateContent(field, label, html, syncEditor, type) {
    var safeHtml = decodeCandidateOfferTemplateContent(html);
    var fieldInput = document.getElementById('candidateOfferTemplateField');
    var title = document.getElementById('candidateOfferTemplateTitle');
    var drawerTitle = document.getElementById('candidateOfferDrawerTitle');
    var preview = document.getElementById('viewTermsoffer');
    var templateType = type || 'Full Time';
    if (fieldInput && field) fieldInput.value = field;
    if (title && label) title.textContent = label;
    if (drawerTitle && label) drawerTitle.textContent = 'Edit ' + label;
    document.querySelectorAll('.candidate-offer-template-type').forEach(function(typeLabel) {
        typeLabel.textContent = templateType;
    });
    if (preview) preview.innerHTML = safeHtml;
    if (syncEditor) {
        var editor = window.CKEDITOR && CKEDITOR.instances.kt_docs_tinymce_hiddenoffer;
        if (editor) {
            editor.setData(safeHtml, function() { updateSettingsOfferPreview(); });
        } else {
            var textarea = document.getElementById('kt_docs_tinymce_hiddenoffer');
            if (textarea) textarea.value = safeHtml;
        }
    }
}
function updateActiveCandidateOfferTemplateCache(html) {
    var active = getActiveCandidateOfferLink();
    if (active) active.setAttribute('data-template-content', html || '');
}
function setSettingsOfferZoom(delta) {
    var pct = document.getElementById('settings_offer_zoom_pct');
    var editor = window.CKEDITOR && CKEDITOR.instances.kt_docs_tinymce_hiddenoffer;
    window.settingsOfferEditorZoom = Math.max(80, Math.min(130, (window.settingsOfferEditorZoom || 100) + delta));
    if (pct) pct.textContent = window.settingsOfferEditorZoom + '%';
    if (editor && editor.document && editor.document.getBody()) {
        editor.document.getBody().setStyle('zoom', String(window.settingsOfferEditorZoom / 100));
    }
}
function getCandidateEditButtonHtml() {
    return '<span class="candidate-action-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"/></svg></span>Edit';
}
document.addEventListener('DOMContentLoaded', function() {
    var editBtn = document.getElementById('editBtnoffer');
    if (editBtn) editBtn.addEventListener('click', openOfferLetterDrawer);
    document.querySelectorAll('.candidate-offer-template-link').forEach(function(templateLink) {
        templateLink.addEventListener('click', function(event) {
            event.preventDefault();
            document.querySelectorAll('.candidate-offer-template-link').forEach(function(link) { link.classList.remove('active'); });
            templateLink.classList.add('active');
            setCandidateOfferTemplateContent(
                templateLink.getAttribute('data-template-field'),
                templateLink.getAttribute('data-template-label'),
                templateLink.getAttribute('data-template-content') || '',
                true,
                templateLink.getAttribute('data-template-type')
            );
        });
    });
    document.querySelectorAll('[data-settings-offer-tab]').forEach(function(tabBtn) {
        tabBtn.addEventListener('click', function() {
            var target = tabBtn.getAttribute('data-settings-offer-tab');
            document.querySelectorAll('[data-settings-offer-tab]').forEach(function(btn) { btn.classList.remove('co-tpl-tab--active'); });
            document.querySelectorAll('[data-settings-offer-pane]').forEach(function(pane) { pane.classList.add('d-none'); });
            tabBtn.classList.add('co-tpl-tab--active');
            var pane = document.querySelector('[data-settings-offer-pane="' + target + '"]');
            if (pane) pane.classList.remove('d-none');
            if (target === 'preview') updateSettingsOfferPreview();
        });
    });
    document.querySelectorAll('[data-settings-offer-variable]').forEach(function(chip) {
        chip.addEventListener('click', function() {
            var token = chip.getAttribute('data-settings-offer-variable');
            var editor = window.CKEDITOR && CKEDITOR.instances.kt_docs_tinymce_hiddenoffer;
            if (token && editor) editor.insertText(token);
        });
    });
    var zoomOut = document.getElementById('settings_offer_zoom_out');
    var zoomIn = document.getElementById('settings_offer_zoom_in');
    var download = document.getElementById('settings_offer_download_sample');
    if (zoomOut) zoomOut.addEventListener('click', function() { setSettingsOfferZoom(-10); });
    if (zoomIn) zoomIn.addEventListener('click', function() { setSettingsOfferZoom(10); });
    if (download) download.addEventListener('click', function() {
        updateSettingsOfferPreview();
        var preview = document.getElementById('settings_offer_preview_content');
        var letterhead = document.querySelector('[data-settings-offer-pane="preview"] .co-tpl-subcard');
        var html = '<!doctype html><html><head><meta charset="utf-8"><title>Offer Letter Template</title></head><body>' + (letterhead ? letterhead.innerHTML : '') + (preview ? preview.innerHTML : '') + '</body></html>';
        var blob = new Blob([html], { type: 'text/html' });
        var link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = 'candidate-offer-letter-template.html';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        window.setTimeout(function() { URL.revokeObjectURL(link.href); }, 1000);
    });
});

document.addEventListener('DOMContentLoaded', function() {
    var editBtn = document.getElementById('editBtnofferComponent'); 
    var viewTerms = document.getElementById('viewoffercomponent');

    editBtn.addEventListener('click', function() {
        if (viewTerms.getAttribute('data-editing') === 'true') {
            // Switch to "View" mode
            viewTerms.querySelectorAll('.editable').forEach(function(cell) {
                var input = cell.querySelector('input');
                if (input) {
                    cell.textContent = input.value; // Replace input with text
                }
            });
            viewTerms.setAttribute('data-editing', 'false');
            editBtn.textContent = "Edit";

        } else {
            // Switch to "Edit" mode
            viewTerms.querySelectorAll('.editable').forEach(function(cell) {
                var text = cell.textContent;
                // Add form-control and form-control-solid classes
                cell.innerHTML = '<input type="text" value="' + text + '" class="form-control form-control-solid" style="width: 33%;">';
            });
            viewTerms.setAttribute('data-editing', 'true');
            editBtn.textContent = "View";
        }
    });
});

$(document).on('click','.updateCompanyTermsofferTop',function(){
      $('.updateCompanyTermsoffer').click();
})
$(document).on('click','.setToDefaultCompanyTermsofferTop',function(){
      $('.setToDefaultCompanyTermsoffer').click();
})

document.addEventListener('DOMContentLoaded', function() {
    var editBtn = document.getElementById('editBtnagencyoffer');
    var viewTerms = document.getElementById('viewTermsagencyoffer');
    var editTerms = document.getElementById('editTermsagencyoffer');
    var updateTop = document.getElementById('updateCompanyTermsagencyofferTop');
    var setDefaultTop = document.getElementById('setToDefaultCompanyTermsagencyofferTop');
    updateTop.style.display = "none";
    setDefaultTop.style.display = "none";

    editBtn.addEventListener('click', function() {
        if (viewTerms.style.display === "none") {
            viewTerms.style.display = "block";
            editTerms.style.display = "none";
            editBtn.textContent = "Edit";
            updateTop.style.display = "none";
            setDefaultTop.style.display = "none";
        } else {
            viewTerms.style.display = "none";
            editTerms.style.display = "block";
            editBtn.textContent = "View";
            updateTop.style.display = "block";
            setDefaultTop.style.display = "block";
        }
    });
});

$(document).on('click','.updateCompanyTermsagencyofferTop',function(){
      $('.updateCompanyTermsagencyoffer').click();
})
$(document).on('click','.setToDefaultCompanyTermsagencyofferTop',function(){
      $('.setToDefaultCompanyTermsagencyoffer').click();
})

$(document).on('click','.updateCompanyTermsTop',function(){
      $('.updateCompanyTerms').click();
})
$(document).on('click','.setToDefaultCompanyTermsTop',function(){
      $('.setToDefaultCompanyTerms').click();
})






var input = document.querySelector('input[name=tags]'); 
   // initialize Tagify on the above input node reference
new Tagify(input)


  

      // ── Interview Pipeline Builder: Modern Stack-Based Card + Sub-Card System (Rule 20) ──
      var ipsStageNameMap = {
          1: { name: 'Sourcing', root: 'root_sourcing', cls: 'bg-secondary' },
          2: { name: 'Screening', root: 'root_screening', cls: 'screening-light' },
          3: { name: 'Evaluation', root: 'root_evaluation', cls: 'evaluation-light' },
          4: { name: 'Negotiation', root: 'root_negotation', cls: 'negotation-light' },
          5: { name: 'Offer', root: 'root_offer', cls: 'offer-light' },
          6: { name: 'Joined', root: 'root_joined', cls: 'joined-light' },
          7: { name: 'Rejected', root: 'root_rejected', cls: 'rejected-light' }
      };

      $('body').on('click', '.interview_stage', function(e) {
          $('#add_interview_sub_stage_form').show();
          $('#add_interview_sub_stage_form_loader').hide();
          $('#ltb_stage_name').val('');

          var id = $(this).data('id') || 1;
          var stageInfo = ipsStageNameMap[id] || ipsStageNameMap[1];
          var root = $(this).data('root') || stageInfo.root;
          var ltb_class = $(this).data('class') || stageInfo.cls;

          $('.h1_title').html('Add ' + stageInfo.name + ' Sub-Stage');
          $('.lbl_title').html(stageInfo.name + ' Sub-Stage Name');

          $('#ltb_stage').val(id);
          $('#ltb_root').val(root);
          $('#ltb_class').val(ltb_class);
          $('#ips_modal_stage_picker').val(id);
      });

      $('body').on('change', '#ips_modal_stage_picker', function() {
          var id = parseInt($(this).val()) || 1;
          var stageInfo = ipsStageNameMap[id] || ipsStageNameMap[1];
          $('#ltb_stage').val(id);
          $('#ltb_root').val(stageInfo.root);
          $('#ltb_class').val(stageInfo.cls);
          $('.h1_title').html('Add ' + stageInfo.name + ' Sub-Stage');
          $('.lbl_title').html(stageInfo.name + ' Sub-Stage Name');
      });

      $('body').on('click', '#ltb_submit', function(e) {
          e.preventDefault();
          var ltb_stage = $('#ltb_stage').val();
          var ltb_root = $('#ltb_root').val();
          var ltb_stage_name = $.trim($('#ltb_stage_name').val());
          var ltb_class = $('#ltb_class').val();
          var url = "{{ route('service_company.account.addIntStage') }}";
          var token = "{{ csrf_token() }}";

          if (!ltb_stage_name) {
              if (typeof Swal !== 'undefined') {
                  Swal.fire({
                      icon: 'warning',
                      title: 'Sub-Stage Name Required',
                      text: 'Please enter a valid sub-stage name.',
                      confirmButtonColor: '#009ef7'
                  });
              } else {
                  alert('Please enter a valid sub-stage name.');
              }
              $('#ltb_stage_name').focus();
              return;
          }

          $.ajax({
              type: 'POST',
              url: url,
              beforeSend: function() {
                  $('#add_interview_sub_stage_form').hide();
                  $('#add_interview_sub_stage_form_loader').show().html(
                      '<div class="d-flex flex-column align-items-center py-5"><div class="spinner-border text-primary mb-3" role="status"><span class="visually-hidden">Saving...</span></div><div class="text-muted fs-7">Saving new sub-stage...</div></div>'
                  );
              },
              data: {
                  '_token': token,
                  'ltb_stage': ltb_stage,
                  'ltb_stage_name': ltb_stage_name,
                  'ltb_class': ltb_class
              },
              success: function(response) {
                  if (response.status == "success") {
                      var $targetList = $("#" + ltb_root);
                      if ($targetList.length) {
                          $targetList.append(response.stageHtml);
                      }
                      var stageKey = ltb_root.replace('root_', '');
                      $('#ipsEmpty_' + stageKey).hide();

                      if (typeof updateIpsCounters === 'function') {
                          updateIpsCounters();
                      }

                      $('#add_interview_sub_stage_form_loader').html(
                          '<div class="alert alert-success d-flex align-items-center p-4 rounded mb-0"><i class="fas fa-check-circle text-success fs-3 me-3"></i><div><strong>Success!</strong> Sub-stage added successfully.</div></div>'
                      );

                      setTimeout(function() {
                          $('#add_interview_sub_stage').modal('hide');
                          $('#add_interview_sub_stage_form').show();
                          $('#add_interview_sub_stage_form_loader').hide();
                          $('#ltb_stage_name').val('');
                      }, 900);
                  } else {
                      $('#add_interview_sub_stage_form').show();
                      $('#add_interview_sub_stage_form_loader').hide();
                      alert('Error adding sub-stage. Please try again.');
                  }
              },
              error: function() {
                  $('#add_interview_sub_stage_form').show();
                  $('#add_interview_sub_stage_form_loader').hide();
                  alert('Server error occurred while adding sub-stage.');
              }
          });
      });	  
      
      

      function boostHandleClick(currentValue, currentId) {
          var chkStatus = currentValue.checked;
          var userId = currentId;
          var statusText = $(currentValue).parents('td').find('.status_text');
          if ($(currentValue).is(':checked')) {
              statusText.text('Active').removeClass('text-danger').addClass('text-success');
          } else {
              statusText.text('Inactive').removeClass('text-success').addClass('text-danger');
          }
      
          var url = "{{ route('service_company.account.boostStatusUpdate') }}";
          var token = "{{ csrf_token() }}";
          var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
          $.ajax({
              type: 'POST',
              url: url,
              beforeSend: function() {},
              data: {
                  '_token': token,
                  'chkStatus': chkStatus,
                  'userId': userId
              },
              success: function(response) {
                  if (response.status == "success") {
                      fetchTeams(); 
                      $('#' + block).remove();  
                  }
              }
          });
        }

	 
      $('body').on('click', '.delete_process', function(){
                  var id = 123;
                  swal({
                      title				: "@lang('errors.areYouSure')",
                      text				: "@lang('errors.deleteWarning')",
                      type				: "warning",
                      showCancelButton	: true,
                      confirmButtonColor	: "#DD6B55",
                      confirmButtonText	: "@lang('app.delete')",
                      cancelButtonText	: "@lang('app.cancel')",
                      closeOnConfirm		: true,
                      closeOnCancel		: true
                  }, function(isConfirm){
                      if (isConfirm) {
                          var url 	=	"{{ route('service_company.job-categories.destroy',':id') }}";
                          url 		=	url.replace(':id', id);
                          var token 	=	"{{ csrf_token() }}";
                          $.ajax({
                      type	: 'POST',
                      url	: url,
                      data	: {'_token': token, '_method': 'DELETE'},
                      success: function (response) {
                        if (response.status == "success") {
                          $.unblockUI();
                          table._fnDraw();
                        }
                      }
                });
                      }
                  });
      });
    
      $(document).on('click', '.stage_block', function(e) { 
          e.preventDefault();
          e.stopPropagation();
          var block = $(this).attr('rel');		
          if (block > 0) {
              var url = "{{ route('service_company.account.delIntStage') }}";
              var token = "{{ csrf_token() }}";							
            
              const swalWithBootstrapButtons = Swal.mixin({
                  customClass: { confirmButton: 'btn btn-danger', cancelButton: 'btn btn-secondary me-3' },
                  buttonsStyling: false
              });

              swalWithBootstrapButtons.fire({
                  title: 'Delete Sub-Stage?',
                  text: "You won't be able to revert this sub-stage!",
                  icon: 'warning',
                  showCancelButton: true,
                  confirmButtonText: 'Yes, Delete it!',
                  cancelButtonText: 'No, cancel!',
                  reverseButtons: true
              }).then((result) => {					
                  if (result.isConfirmed) {
                      $.ajax({
                          type: 'POST',
                          url: url,
                          data: { '_token': token, 'block': block },
                          success: function (response) {
                              if (response.status == "success") {
                                  $('#' + block).fadeOut(200, function() {
                                      $(this).remove();
                                      if (typeof updateIpsCounters === 'function') {
                                          updateIpsCounters();
                                      }
                                  });
                                  swalWithBootstrapButtons.fire(
                                      'Deleted!',
                                      'Selected sub-stage has been deleted successfully.',
                                      'success'
                                  );
                              } else {
                                  swalWithBootstrapButtons.fire(
                                      'Error!',
                                      'Something went wrong, please try again.',
                                      'error'
                                  );
                              }								  
                          },
                          error: function() {
                              swalWithBootstrapButtons.fire(
                                  'Server Error!',
                                  'Unable to process deletion at this time.',
                                  'error'
                              );
                          }
                      });
                  } else if (result.dismiss === Swal.DismissReason.cancel) {
                      swalWithBootstrapButtons.fire('Cancelled', 'Selected sub-stage is safe :)', 'info');
                  }				
              });
          } 
      });  
			 
      $('body').on('click', '.google_config', function(){
                    var id    					=	123;
                    var url   					=	"{{ route('service_company.account.google',':id') }}";
                    url     					=	url.replace(':id', id);
                    var token 					=	"{{ csrf_token() }}";								
                    var baseurl    				=	"{{ asset('assets/images/Loading2.gif') }}"; 			
                    $.ajax({
                          type			:	'GET',
                          url				:	url,
                          beforeSend		:	function() {		
                                      $('#google_load').html('<img src="'+baseurl+'" height="60%" width="100%">');
                                  },
                          success			: function (response) {
                                              if (response.status == "success") {
                                                $('#google_load').html(response.view);	 	
                                              }	
                                          }
                    });
      });
   		
		  $('body').on('click', '.zoom_config', function(){   								
   								var id    					=	123;
   								var url   					=	"{{ route('service_company.account.zoom',':id') }}";
   								url     					=	url.replace(':id', id);
   								var token 					=	"{{ csrf_token() }}";								
   								var baseurl    				=	"{{ asset('assets/images/Loading2.gif') }}"; 			
   								$.ajax({
   											type		:	'GET',
   											url			:	url,
   											beforeSend	: 	function() {		
   														 				$('#zoom_load').html('<img src="'+baseurl+'" height="60%" width="100%">');
   															},
   											success		: 	function (response) {
   																					if (response.status == "success") {
   																						$('#zoom_load').html(response.view);	 	
   																					}	
   																			 }
   								});   								
   		});


      $('body').on('click', '.google_sheet_config', function(){   								
   								var id    					=	123;
   								var url   					=	"{{ route('service_company.account.zoom',':id') }}";
   								url     					=	url.replace(':id', id);
   								var token 					=	"{{ csrf_token() }}";								
   								var baseurl    				=	"{{ asset('assets/images/Loading2.gif') }}"; 			
   								$.ajax({
   											type		:	'GET',
   											url			:	url,
   											beforeSend	: 	function() {		
   														 				$('#google_sheet_load').html('<img src="'+baseurl+'" height="60%" width="100%">');
   															},
   											success		: 	function (response) {
   																					if (response.status == "success") {
   																						$('#google_sheet_load').html(response.view);	 	
   																					}	
   																			 }
   								});   								
   		});

   
   
</script>      

<script>

$(document).ready(function(){
        // Intent: give every real account-page action immediate loading feedback without changing the underlying AJAX or submit business logic.
        var buttonSpinnerSelector = 'button, a.btn, .aat-btn, .cv2-edit, .candidate-action-icon';
        var buttonSpinnerExclude = '.aat-drawer-close, .aga-close, .avd-close, .apv-close, .btn-icon, .nav-link, [data-toggle="tab"], .aga-tab, .aat-drawer-tab, .co-var-chip, .aga-var-item, .avd-row-actions button, [data-no-spinner]';
        function setButtonLoading($button) {
            if (!$button.length || $button.is(buttonSpinnerExclude) || $button.hasClass('jero-btn-loading')) {
                return;
            }
            $button.addClass('jero-btn-loading').attr('aria-busy', 'true');
            window.setTimeout(function () {
                $button.removeClass('jero-btn-loading').removeAttr('aria-busy');
            }, 12000);
        }
        $('body').on('click', buttonSpinnerSelector, function () {
            setButtonLoading($(this));
        });
        $(document).ajaxStop(function () {
            $('.jero-btn-loading').removeClass('jero-btn-loading').removeAttr('aria-busy');
        });
        // Intent: persist agency-facing commercial terms separately from client Services/Commercials.
        function openAgencyCommercialDrawer(mode) {
            var commercialMode = mode === 'contract' ? 'contract' : 'fulltime';
            var title = commercialMode === 'contract' ? 'Edit Contract Commercials' : 'Edit Fulltime Commercials';
            var subtitle = commercialMode === 'contract'
                ? 'Configure contract hiring agency commercials, experience pricing, replacement and payment terms.'
                : 'Configure full-time agency commercials, experience pricing, replacement and payment terms.';
            $('#agencyCommercialDrawerOverlay').addClass('show');
            $('#agency_commercial_terms_form')
                .removeClass('agency-commercial-fulltime agency-commercial-contract')
                .addClass('open agency-commercial-mode agency-commercial-' + commercialMode)
                .attr('aria-hidden', 'false');
            $('[data-agency-commercial-title]').text(title);
            $('[data-agency-commercial-subtitle]').text(subtitle);
            $('[data-agency-alert-text]').text(commercialMode === 'contract'
                ? 'Contract hiring commercials govern how agencies are paid for contract / temporary staffing. They populate the agreement.'
                : 'These commercial terms define how agencies will be paid for successful placements. They populate the agreement and can be overridden at job level if required.');
            $('body').addClass('modal-open');
            syncFulltimeFeeModelFields();
            syncAgencyReplacementSummary();
            syncAgencyContractReplacementSummary();
        }
        function closeAgencyCommercialDrawer() {
            $('#agencyCommercialDrawerOverlay').removeClass('show');
            $('#agency_commercial_terms_form').removeClass('open agency-commercial-fulltime agency-commercial-contract').attr('aria-hidden', 'true');
            $('body').removeClass('modal-open');
        }
        $('body').on('click', '[data-agency-commercial-open], #toggleAgencyCommercialTerms', function(){
            openAgencyCommercialDrawer($(this).data('agency-commercial-open') || 'fulltime');
        });
        $('body').on('click', '#cancelAgencyCommercialTerms, #closeAgencyCommercialDrawer, #agencyCommercialDrawerOverlay, .aat-edit-alert .aat-drawer-close', function(){
            closeAgencyCommercialDrawer();
        });
        // Cached selectors so the model-toggle handler stays fast on big DOM trees.
        var $aatDrawer = $('#agency_commercial_terms_form');
        var $aatPctOnly = $('[data-aat-percentage-only]');
        var $aatFixOnly = $('[data-aat-fixed-only]');
        var $aatRateSuffix = $('[data-aat-rate-suffix]');
        var $aatRateLabel = $('[data-aat-rate-label]');
        var $aatRateHelp = $('[data-aat-rate-help]');
        var $aatRangeRateLabel = $('[data-aat-range-rate-label]');
        var $aatDefaultPricingCopy = $('[data-aat-default-pricing-copy]');
        var $aatExperiencePricingCopy = $('[data-aat-experience-pricing-copy]');
        var $aatDefaultRateInput = $('[data-aat-default-rate-input]');
        var $aatPctName = 'fulltime_success_fee_percentage';
        var $aatFixName = 'fulltime_fixed_fee_amount';
        function syncFulltimeFeeModelFields() {
            var $feeModel = $('[name="fulltime_fee_model"]');
            var isFixedFee = $feeModel.val() === 'fixed';
            $aatDrawer.toggleClass('is-fixed-fee', isFixedFee);
            $aatPctOnly.toggle(!isFixedFee);
            $aatFixOnly.toggle(isFixedFee);
            $aatRateSuffix.text(isFixedFee ? '' : '%');
            $aatRangeRateLabel.text(isFixedFee ? 'Fixed Fee Amount' : 'Rate / Percentage');
            $aatRateLabel.text(isFixedFee ? 'Fixed Fee Amount' : 'Rate / Percentage');
            $aatRateHelp.text(isFixedFee ? 'The default amount used for each successful placement.' : 'Changing this rate updates every experience range.');
            $aatDefaultPricingCopy.text(isFixedFee ? 'Set the default fixed fee and how it will be charged.' : 'Set the default pricing for all experience ranges.');
            $aatExperiencePricingCopy.text(isFixedFee ? 'Define different fixed fees for different experience bands.' : 'Define pricing using clear experience ranges.');
            $aatDefaultRateInput.each(function () {
                var $input = $(this);
                var fallback = parseFloat($input.data('aat-fallback-rate')) || 0;
                var current = parseFloat($input.val());
                $input.attr('name', isFixedFee ? $aatFixName : $aatPctName);
                if (isNaN(current) || current === 0) $input.val(fallback);
            });
            $('[name="fulltime_success_fee_percentage"]').prop('disabled', isFixedFee);
            $('[name="fulltime_fixed_fee_amount"]').prop('disabled', !isFixedFee);
            $('[name="fulltime_fixed_fee_currency"]').prop('disabled', !isFixedFee);
        }
        function syncAgencyReplacementSummary() {
            var periodInput = $('[name="fulltime_replacement_period_days"]');
            var days = $('#aatReplacementDays');
            if (days.length) days.text(periodInput.val() || '0');
            var typeSelect = $('[data-aat-replacement-type]');
            var label = $('#aatReplacementSummary');
            if (label.length && typeSelect.length) {
                var text = typeSelect.find('option:selected').text();
                label.text(text ? text.toLowerCase() : 'replacement');
            }
        }
        function syncAgencyContractReplacementSummary() {
            var monthsInput = $('[name="contract_min_duration_months"]');
            var days = $('#aatContractReplacementDays');
            if (days.length) days.text(monthsInput.val() || '0');
            var typeSelect = $('[data-aat-contract-replacement-type]');
            var label = $('#aatContractReplacementSummary');
            if (label.length && typeSelect.length) {
                var text = typeSelect.find('option:selected').text();
                label.text(text ? text.toLowerCase() : 'replacement');
            }
        }
        $('body').on('click', '[data-aat-commercial-model]', function(){
            var $option = $(this);
            var model = $option.data('aat-commercial-model');
            $('[name="fulltime_fee_model"]').val(model);
            $('[data-aat-commercial-model]').each(function(){
                var isActive = $(this).data('aat-commercial-model') === model;
                $(this).toggleClass('active', isActive).attr('aria-checked', isActive ? 'true' : 'false');
            });
            if (model === 'fixed') {
                $('#aatCommercialModelHelp').text('Billing is calculated from the configured fixed rate for each experience range.');
            } else {
                $('#aatCommercialModelHelp').text("Placement fees are calculated as a percentage of the candidate's Cost to Company (CTC).");
            }
            syncFulltimeFeeModelFields();
        });
        $('body').on('input', '[data-aat-default-rate-input], [name="fulltime_success_fee_percentage"], [name="fulltime_fixed_fee_amount"]', function(){
            var sourceVal = this.value;
            $('[data-aat-row-rate-input]').each(function(){
                $(this).val(sourceVal);
                var idx = $(this).data('aat-row-index');
                $('[data-aat-row-rate-hidden-input][data-aat-row-index="' + idx + '"]').val(sourceVal);
            });
        });
        $('body').on('click', '[data-aat-range-reset]', function(){
            var idx = $(this).data('aat-row-index');
            var sourceVal = $('[data-aat-default-rate-input]').val();
            if (!sourceVal) {
                if ($('[name="fulltime_fee_model"]').val() === 'fixed') {
                    sourceVal = $('[name="fulltime_fixed_fee_amount"]').val();
                } else {
                    sourceVal = $('[name="fulltime_success_fee_percentage"]').val();
                }
            }
            $('[data-aat-row-rate-input][data-aat-row-index="' + idx + '"]').val(sourceVal);
            $('[data-aat-row-rate-hidden-input][data-aat-row-index="' + idx + '"]').val(sourceVal);
        });
        $('body').on('click', '[data-aat-contract-range-reset]', function(){
            var idx = $(this).data('aat-row-index');
            var sourceVal = $('[name="contract_rate_display"]').val();
            var $row = $('[data-aat-contract-row-rate-hidden-input][data-aat-row-index="' + idx + '"]');
            $row.val(sourceVal);
            $row.closest('.aat-range-row').find('input[name^="contract_range_rate"]').val(sourceVal);
        });
        $('body').on('input', '[data-aat-row-rate-input]', function(){
            var $input = $(this);
            var idx = $input.data('aat-row-index');
            $('[data-aat-row-rate-hidden-input][data-aat-row-index="' + idx + '"]').val($input.val());
        });
        $('body').on('input', 'input[name^="contract_range_rate"]', function(){
            var $input = $(this);
            var idx = $input.data('aat-row-index');
            $('[data-aat-contract-row-rate-hidden-input][data-aat-row-index="' + idx + '"]').val($input.val());
        });
        $('body').on('input', '[name="fulltime_replacement_period_days"]', syncAgencyReplacementSummary);
        $('body').on('input', '[name="contract_min_duration_months"]', syncAgencyContractReplacementSummary);
        $('body').on('change', '[data-aat-replacement-type]', syncAgencyReplacementSummary);
        $('body').on('change', '[data-aat-contract-replacement-type]', syncAgencyContractReplacementSummary);
        $('body').on('change', '[data-aat-default-currency]', function () { $('[data-aat-range-currency]').val(this.value); });
        $('body').on('change', '[data-aat-applied-on]', function () { $('[data-aat-range-applied-on]').val(this.value); });
        $('body').on('change', '[data-aat-fixed-trigger]', function () { $('[data-aat-range-fixed-trigger]').val(this.value); });
        $('body').on('change', '[data-aat-fixed-frequency]', function () { $('[data-aat-range-fixed-frequency]').val(this.value); });
        $('body').on('change', '[data-aat-range-currency]', function () { $('[data-aat-default-currency]').val(this.value); });
        $('body').on('change', '[data-aat-range-applied-on]', function () { $('[data-aat-applied-on]').val(this.value); });
        $('body').on('change', '[data-aat-range-fixed-trigger]', function () { $('[data-aat-fixed-trigger]').val(this.value); });
        $('body').on('change', '[data-aat-range-fixed-frequency]', function () { $('[data-aat-fixed-frequency]').val(this.value); });
        syncFulltimeFeeModelFields();
        $('body').on('click', '#saveAgencyCommercialTerms', function(){
            var $button = $(this);
            var form = document.getElementById('agency_commercial_terms_form');
            if (!form || $button.prop('disabled')) return;
            if ($('#agencyCommercialStatus').val() !== 'draft') {
                $('#agencyCommercialStatus').val('active');
            }

            var data = new FormData(form);
            $.ajax({
                url: '{{route('service_company.account.update', @$account->id)}}',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                method: 'POST',
                processData: false,
                contentType: false,
                data: data,
                beforeSend: function () {
                    $button.prop('disabled', true).text('Saving...');
                },
                success: function (response) {
                    if (response.status === 'success') {
                        Swal.fire('Success', response.message || 'Agency commercial terms saved successfully.', 'success');
                        closeAgencyCommercialDrawer();
                        window.location.reload();
                    } else {
                        Swal.fire('Notice', response.message || 'Agency commercial terms could not be saved.', 'warning');
                    }
                },
                error: function () {
                    Swal.fire('Error', 'Agency commercial terms could not be saved. Please try again.', 'error');
                },
                complete: function () {
                    $button.prop('disabled', false).text('Save & Update');
                }
            });
        });
        $('body').on('click', '#agencyAgreementPreview', function(){
            openAgencyPreviewDrawer();
        });
        $('body').on('click', '#agencyAgreementPreviewDrawer, #agencyAgreementPreviewTab', function(){
            openAgencyPreviewDrawer();
        });
        $('body').on('click', '.aga-var-item', function(){
            var variableCode = $(this).find('code').text();
            if (window.CKEDITOR && CKEDITOR.instances.kt_docs_tinymce_hidden) {
                CKEDITOR.instances.kt_docs_tinymce_hidden.insertText(variableCode);
            }
        });
        function openAgencyVariablesDrawer() {
            $('#agencyVariablesDrawerOverlay').addClass('show');
            $('#agencyVariablesDrawer').addClass('open').attr('aria-hidden', 'false');
            $('body').addClass('modal-open');
            agencyVariablesCurrentPage = 1;
            filterAgencyVariables();
        }
        function closeAgencyVariablesDrawer() {
            $('#agencyVariablesDrawerOverlay').removeClass('show');
            $('#agencyVariablesDrawer').removeClass('open').attr('aria-hidden', 'true');
            $('body').removeClass('modal-open');
        }
        var agencyVariablesCurrentPage = 1;
        var agencyVariablesPageSize = 8;
        function getFilteredAgencyVariableRows() {
            var searchText = ($('#agencyVariableSearch').val() || '').toLowerCase().trim();
            var categoryValue = $('#agencyVariableCategoryFilter').val() || 'all';
            var sourceValue = $('#agencyVariableSourceFilter').val() || 'all';
            var showSystem = $('#agencyVariableSystemFilter').is(':checked');
            return $('[data-avd-row]').filter(function(){
                var $row = $(this);
                var matchesSearch = !searchText || ($row.data('desc') || '').indexOf(searchText) !== -1;
                var matchesCategory = categoryValue === 'all' || $row.data('category') === categoryValue;
                var matchesSource = sourceValue === 'all' || $row.data('source') === sourceValue;
                var matchesSystem = showSystem || $row.data('category') !== 'Agreement System';
                return matchesSearch && matchesCategory && matchesSource && matchesSystem;
            });
        }
        function renderAgencyVariablesPagination(totalRows) {
            var totalPages = Math.max(1, Math.ceil(totalRows / agencyVariablesPageSize));
            agencyVariablesCurrentPage = Math.min(Math.max(agencyVariablesCurrentPage, 1), totalPages);
            var paginationHtml = '<button type="button" data-avd-page="prev" ' + (agencyVariablesCurrentPage === 1 ? 'disabled' : '') + '>‹</button>';
            for (var pageNumber = 1; pageNumber <= totalPages; pageNumber++) {
                paginationHtml += '<button type="button" data-avd-page="' + pageNumber + '" class="' + (pageNumber === agencyVariablesCurrentPage ? 'active' : '') + '">' + pageNumber + '</button>';
            }
            paginationHtml += '<button type="button" data-avd-page="next" ' + (agencyVariablesCurrentPage === totalPages ? 'disabled' : '') + '>›</button>';
            $('#agencyVariablesPagination').html(paginationHtml);
        }
        function filterAgencyVariables() {
            var $allRows = $('[data-avd-row]');
            var $filteredRows = getFilteredAgencyVariableRows();
            var totalFilteredRows = $filteredRows.length;
            var startIndex = (agencyVariablesCurrentPage - 1) * agencyVariablesPageSize;
            var endIndex = startIndex + agencyVariablesPageSize;

            renderAgencyVariablesPagination(totalFilteredRows);
            startIndex = (agencyVariablesCurrentPage - 1) * agencyVariablesPageSize;
            endIndex = startIndex + agencyVariablesPageSize;
            $allRows.hide();
            $filteredRows.slice(startIndex, endIndex).show();
            $('#agencyVariablesEmpty').toggle(totalFilteredRows === 0);
            $('#agencyVariablesShowing').text('Showing ' + (totalFilteredRows ? startIndex + 1 : 0) + ' to ' + Math.min(endIndex, totalFilteredRows) + ' of ' + totalFilteredRows + ' variables');
        }
        function insertAgencyVariable(variableCode) {
            closeAgencyVariablesDrawer();
            if (typeof openAgencyAgreementDrawer === 'function') openAgencyAgreementDrawer();
            setTimeout(function(){
                if (window.CKEDITOR && CKEDITOR.instances.kt_docs_tinymce_hidden) {
                    CKEDITOR.instances.kt_docs_tinymce_hidden.insertText(variableCode);
                }
            }, 150);
        }
        $('body').on('click', '#openAgencyVariablesDrawer', openAgencyVariablesDrawer);
        $('body').on('click', '#agencyVariablesDrawerOverlay, #closeAgencyVariablesDrawer', closeAgencyVariablesDrawer);
        $('body').on('input change', '#agencyVariableSearch, #agencyVariableCategoryFilter, #agencyVariableSourceFilter, #agencyVariableSystemFilter', function(){
            agencyVariablesCurrentPage = 1;
            filterAgencyVariables();
        });
        $('body').on('click', '#agencyVariablesPagination [data-avd-page]', function(){
            if ($(this).is(':disabled')) return;
            var requestedPage = $(this).data('avd-page');
            var totalPages = Math.max(1, Math.ceil(getFilteredAgencyVariableRows().length / agencyVariablesPageSize));
            if (requestedPage === 'prev') agencyVariablesCurrentPage--;
            if (requestedPage === 'next') agencyVariablesCurrentPage++;
            if (!isNaN(parseInt(requestedPage, 10))) agencyVariablesCurrentPage = parseInt(requestedPage, 10);
            agencyVariablesCurrentPage = Math.min(Math.max(agencyVariablesCurrentPage, 1), totalPages);
            filterAgencyVariables();
        });
        $('body').on('click', '.avd-copy', function(){
            var variableCode = $(this).closest('[data-avd-row]').data('code');
            if (navigator.clipboard) {
                navigator.clipboard.writeText(variableCode);
            }
            Swal.fire('Copied', variableCode + ' copied successfully.', 'success');
        });
        $('body').on('click', '.avd-insert', function(){
            insertAgencyVariable($(this).closest('[data-avd-row]').data('code'));
        });
        $('body').on('click', '#closeAgencyVariablesInfo', function(){
            $(this).closest('.avd-info').hide();
        });
        var agencyPreviewValues = @json($agencyPreviewValues);
        function escapeAgencyPreviewText(value) {
            return $('<div>').text(value || '').html();
        }
        function buildAgencyPreviewIntroHtml() {
            var todayDate = escapeAgencyPreviewText(agencyPreviewValues['{TODAY_DATE}']);
            var companyName = escapeAgencyPreviewText(agencyPreviewValues['{COMPANY_NAME}']);
            var companyAddress = escapeAgencyPreviewText(agencyPreviewValues['{COMPANY_ADDRESS}']);
            var agencyName = escapeAgencyPreviewText(agencyPreviewValues['{AGENCY_NAME}']);
            var agencyAddress = escapeAgencyPreviewText(agencyPreviewValues['{AGENCY_ADDRESS}']);

            return '<div class="apv-party-intro">'
                + '<h2>AGENCY TERMS &amp; CONDITIONS</h2>'
                + '<p class="apv-effective">This Agreement is made and executed on <b>' + todayDate + '</b> (<b>“Effective Date”</b>) by and between:</p>'
                + '<div class="apv-party-grid">'
                + '<div class="apv-party-box"><div class="apv-party-name">' + companyName + '</div><div class="apv-party-address">' + companyAddress + '</div><div class="apv-party-role">(hereinafter referred to as the <b>“Client”</b>)</div></div>'
                + '<div class="apv-party-and">AND</div>'
                + '<div class="apv-party-box"><div class="apv-party-name">' + agencyName + '</div><div class="apv-party-address">' + agencyAddress + '</div><div class="apv-party-role">(hereinafter referred to as the <b>“Agency”</b>)</div></div>'
                + '</div>'
                + '<p class="apv-party-closing">The Client and the Agency are hereinafter collectively referred to as the <b>“Parties”</b> and individually as a <b>“Party”</b>.</p>'
                + '</div>';
        }
        function populateAgencyPreviewHtml(sourceHtml) {
            var populatedHtml = sourceHtml || '';
            Object.keys(agencyPreviewValues).forEach(function(variableCode){
                populatedHtml = populatedHtml.split(variableCode).join(agencyPreviewValues[variableCode]);
            });
            return populatedHtml;
        }
        function refreshAgencyPreview() {
            var sourceHtml = $('#viewTerms').html() || '';
            if (window.CKEDITOR && CKEDITOR.instances.kt_docs_tinymce_hidden && $('#editTerms').hasClass('open')) {
                sourceHtml = CKEDITOR.instances.kt_docs_tinymce_hidden.getData();
            }
            var populatedHtml = populateAgencyPreviewHtml(sourceHtml);
            var previewHtml = buildAgencyPreviewIntroHtml() + populatedHtml;
            $('#agencyPreviewDocument').html(previewHtml);
            $('#agencyPreviewPlain').text($('<div>').html(previewHtml).text().replace(/\n{3,}/g, "\n\n").trim());
        }
        function openAgencyPreviewDrawer() {
            refreshAgencyPreview();
            if (typeof closeAgencyAgreementDrawer === 'function') closeAgencyAgreementDrawer();
            if (typeof closeAgencyVariablesDrawer === 'function') closeAgencyVariablesDrawer();
            $('#agencyPreviewDrawerOverlay').addClass('show');
            $('#agencyPreviewDrawer').addClass('open').attr('aria-hidden', 'false');
            $('body').addClass('modal-open');
        }
        function closeAgencyPreviewDrawer() {
            $('#agencyPreviewDrawerOverlay').removeClass('show');
            $('#agencyPreviewDrawer').removeClass('open').attr('aria-hidden', 'true');
            $('body').removeClass('modal-open');
        }
        $('body').on('click', '#agencyPreviewDrawerOverlay, #closeAgencyPreviewDrawer', closeAgencyPreviewDrawer);
        $('body').on('click', '[data-apv-view]', function(){
            $('[data-apv-view]').removeClass('active');
            $(this).addClass('active');
            var viewType = $(this).data('apv-view');
            $('#agencyPreviewDocument').toggle(viewType === 'document');
            $('#agencyPreviewPlain').toggle(viewType === 'plain');
        });
        $('body').on('click', '#agencyPreviewDownloadPdf, #agencyPreviewPrint', function(){
            refreshAgencyPreview();
            window.print();
        });

        // Intent: enhance the four existing local selects with searchable, keyboard-friendly enterprise menus; no remote data is requested.
        if ($.fn.select2) {
            $('#language, #timezone, #currency, #time_format').each(function(){
                var $settingSelect = $(this);
                var settingDropdownClass = 'csv2-enterprise-dropdown';
                if ($settingSelect.is('#timezone')) settingDropdownClass += ' csv2-timezone-dropdown';
                if ($settingSelect.is('#time_format')) settingDropdownClass += ' csv2-timeformat-dropdown';
                if (!$settingSelect.hasClass('select2-hidden-accessible')) {
                    $settingSelect.select2({
                        width: '100%',
                        dropdownCssClass: settingDropdownClass,
                        minimumResultsForSearch: ($settingSelect.is('#timezone, #currency')) ? 8 : Infinity
                    });
                }
            }).on('select2:open', function(){
                var $openedSelect = $(this);
                var $settingDropdown = $('.select2-dropdown').last().addClass('csv2-enterprise-dropdown');
                if ($openedSelect.is('#timezone')) {
                    $settingDropdown.addClass('csv2-timezone-dropdown');
                    $settingDropdown.find('.select2-search__field').attr('placeholder', 'Search time zones...');
                }
                if ($openedSelect.is('#time_format')) $settingDropdown.addClass('csv2-timeformat-dropdown');

                // Keep wide enterprise menus inside the viewport at laptop and tablet widths.
                setTimeout(function(){
                    var dropdownNode = $settingDropdown.get(0);
                    if (!dropdownNode) return;
                    var dropdownBounds = dropdownNode.getBoundingClientRect();
                    var horizontalShift = 0;
                    if (dropdownBounds.right > window.innerWidth - 12) horizontalShift = (window.innerWidth - 12) - dropdownBounds.right;
                    if (dropdownBounds.left + horizontalShift < 12) horizontalShift += 12 - (dropdownBounds.left + horizontalShift);
                    $settingDropdown.css('margin-left', horizontalShift + 'px');
                }, 0);
            });
        }
   
			$('.submit_department').click(function(){
   									var dept_name	=	$('#dept_name').val();								
									if(dept_name ==''){
											$('#dept_name').val('');	
											$("#dept_warning_alert").fadeTo(2000, 500).slideUp(500, function() {
												$("#dept_warning_alert").slideUp(500);
											});	
											return false;
									}
									var url 					= "{{ route('service_company.account.storeDept') }}";
									var token 					= "{{ csrf_token() }}";
									var baseurl    				= "{{ asset('assets/images/Loading2.gif') }}"; 		
									$.ajax({
												type		: 'POST',
												url			: url,
												beforeSend	: function() { $('.dept_content').html('<img src="'+baseurl+'" height="60%" width="100%">'); },
												data		: {'_token': token, 'dept_name':dept_name },
												success		: function (response) {
																						if (response.status == "success") {
																																$('#dept_name').val('');
																																$("#dept_success_alert").fadeTo(2000, 500).slideUp(500, function() {
																																	$("#dept_success_alert").slideUp(500);
																																});
																																$('.dept_content').html(response.html);
																																if ('BroadcastChannel' in window) {
																																	try {
																																		var bc = new BroadcastChannel('jero_dept_channel');
																																		bc.postMessage({ type: 'DEPT_UPDATED', timestamp: Date.now() });
																																	} catch(e) {}
																																}
																																try {
																																	localStorage.setItem('jero_dept_sync_timestamp', Date.now());
																																} catch(e) {}
																																if (response.already_exists && response.message) {
																																	Swal.fire({
																																		title: 'Department Exists',
																																		html: response.message,
																																		icon: 'info',
																																		confirmButtonColor: '#2563eb'
																																	});
																																}
																														  }
																				 }
									});
   			});
				
			  $('body').on('click', '.dept_delete', function(){
   	   			
   				   var id				=	$(this).attr('rel');   			   			
				   Swal.fire({
				   title				: "@lang('errors.areYouSure')",
				   text					: "@lang('errors.deleteWarning')",
				   type					: "warning",
				   showCancelButton		: true,
				   confirmButtonColor	: "#DD6B55",
				   confirmButtonText	: "@lang('app.delete')",
				   cancelButtonText		: "@lang('app.cancel')",
				   closeOnConfirm		: true,
				   closeOnCancel		: true
				   })
						   .then((result) => {
											  if (result.isConfirmed) {
											  
																			var url 		=	"{{ route('service_company.job-categories.destroy',':id') }}";
																			url 			=	url.replace(':id', id);
																			var token 		=	"{{ csrf_token() }}";
																			$.ajax({
																						type	: 'POST',
																						url		: url,
																						data	: {'_token': token, '_method': 'DELETE'},
																						success	: function (response) {
																														  if(response.status == "success") {
																																Swal.fire('Deleted!', 'Your record has been deleted.', 'success');
                                                                $('.dept_content').html(response.html);
                                                                if ('BroadcastChannel' in window) {
                                                                      try {
                                                                          var bc = new BroadcastChannel('jero_dept_channel');
                                                                          bc.postMessage({ type: 'DEPT_UPDATED', timestamp: Date.now() });
                                                                      } catch(e) {}
                                                                  }
                                                                  try {
                                                                      localStorage.setItem('jero_dept_sync_timestamp', Date.now());
                                                                  } catch(e) {}
                                                               } else {
                                                                  Swal.fire('Cannot Delete', response.message || 'Unable to delete department.', 'error');
                                                               }
                                                            }
                                                        });											   																			
																	  }
						   })	
   			});				
			
        $('body').on('click', '#updateCompanyProfile', function(){
            var $saveSettingsButton = $(this);
            if ($saveSettingsButton.prop('disabled')) return;

            var data 				=	new FormData();
            var form_data 			=	$('#setting_content').serializeArray();
            $.each(form_data, function (key, input) {
                data.append(input.name, input.value);
            });				
            
              $.ajax({
                    url					:	'{{route('service_company.account.update', @$account->id)}}',
                    headers				:	{'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    method				:	"POST",
                    processData			:	false,
                    contentType			:	false,
                    data				:	data,
                    redirect			:	true,
                    beforeSend         : function () {
                                            $saveSettingsButton
                                                .prop('disabled', true)
                                                .attr('aria-busy', 'true')
                                                .addClass('is-loading');
                                            $saveSettingsButton.find('.csv2-save-label').text('Saving...');
                                },
                    success				:	function (data) {

                                            if(data.status == 'warning'){
                                                Swal.fire({
                                                            text				: data.message,
                                                            icon				: "error",
                                                            buttonsStyling		: !1,
                                                            confirmButtonText	: "Ok, got it!",
                                                            customClass			: { confirmButton: "btn btn-light" },
                                                }); 

                                            }

                                          if(data.status == 'success'){
                                              Swal.fire("Success", "Account settings update successfully!", "success");	
                                              if (data.tenant_defaults && window.JeroTenantDefaults) {
                                                  window.JeroTenantDefaults.syncFromSettings(data.tenant_defaults);
                                              }
                                          }
                                },
                    complete            : function () {
                                            $saveSettingsButton
                                                .prop('disabled', false)
                                                .removeAttr('aria-busy')
                                                .removeClass('is-loading');
                                            $saveSettingsButton.find('.csv2-save-label').text('Save Changes');
                                }
                  });				
        });	

        // Paste and blur handlers for notification email routing
        $(document).on('paste', '#recuitment_email, #sales_email', function () {
            var self = this;
            setTimeout(function () {
                var clean = window.JeroEmailBoxEngine ? window.JeroEmailBoxEngine.cleanDeterministic($(self).val()) : $(self).val().trim();
                if (clean !== $(self).val()) {
                    $(self).val(clean).trigger('input');
                }
            }, 10);
        });

        $(document).on('blur', '#recuitment_email, #sales_email', function () {
            var raw = $(this).val();
            var clean = window.JeroEmailBoxEngine ? window.JeroEmailBoxEngine.cleanDeterministic(raw) : raw.trim();
            if (clean !== raw) {
                $(this).val(clean);
            }
            var targetId = $(this).attr('id');
            var $hint = $('#' + targetId + '_typo_hint');
            if (!clean) {
                $hint.hide().empty();
                return;
            }
            if (window.JeroEmailBoxEngine) {
                var syntax = window.JeroEmailBoxEngine.validateSyntax(clean);
                if (!syntax.isValid) {
                    $hint.html('<i class="fa fa-exclamation-circle text-danger"></i> ' + syntax.error).show();
                    return;
                }
                var suggestion = window.JeroEmailBoxEngine.getSuggestion(clean);
                if (suggestion) {
                    $hint.html('<i class="fa fa-lightbulb-o text-warning"></i> ' + suggestion.message + ' <a href="javascript:void(0);" class="apply-email-fix fw-bold text-primary ms-1" data-target="#' + targetId + '" data-fixed="' + suggestion.suggested + '">Apply</a>').show();
                } else {
                    $hint.hide().empty();
                }
            }
        });

        $(document).on('click', '.apply-email-fix', function (e) {
            e.preventDefault();
            var target = $(this).data('target');
            var fixed = $(this).data('fixed');
            if (target && fixed) {
                $(target).val(fixed).trigger('input').trigger('change');
                $(this).closest('.email-typo-hint').hide().empty();
            }
        });

        // Intent: save the two existing notification-routing fields independently from General settings.
        $('body').on('click', '#saveNotificationSettings', function(){
            var $saveNotificationButton = $(this);
            var notificationForm = document.getElementById('notification_content');

            if ($saveNotificationButton.prop('disabled') || !notificationForm) return;

            // Client-first Email Validation
            if (window.JeroEmailBoxEngine) {
                var recEmail = window.JeroEmailBoxEngine.cleanDeterministic($('#recuitment_email').val());
                $('#recuitment_email').val(recEmail);
                var recSyntax = window.JeroEmailBoxEngine.validateSyntax(recEmail);
                if (!recSyntax.isValid) {
                    $('#recuitment_email_typo_hint').html('<i class="fa fa-exclamation-circle text-danger"></i> ' + recSyntax.error).show();
                    Swal.fire('Invalid Recruitment Email', recSyntax.error, 'error');
                    return;
                }

                var salesEmail = window.JeroEmailBoxEngine.cleanDeterministic($('#sales_email').val());
                $('#sales_email').val(salesEmail);
                var salesSyntax = window.JeroEmailBoxEngine.validateSyntax(salesEmail);
                if (!salesSyntax.isValid) {
                    $('#sales_email_typo_hint').html('<i class="fa fa-exclamation-circle text-danger"></i> ' + salesSyntax.error).show();
                    Swal.fire('Invalid Sales Email', salesSyntax.error, 'error');
                    return;
                }
            }

            if (!notificationForm.checkValidity()) {
                notificationForm.reportValidity();
                return;
            }

            var data = new FormData(notificationForm);
            $.ajax({
                url: '{{route('service_company.account.update', @$account->id)}}',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                method: 'POST',
                processData: false,
                contentType: false,
                data: data,
                beforeSend: function () {
                    $saveNotificationButton
                        .prop('disabled', true)
                        .attr('aria-busy', 'true')
                        .addClass('is-loading');
                    $saveNotificationButton.find('.csn-save-label').text('Saving...');
                },
                success: function (response) {
                    if (response.status === 'warning') {
                        Swal.fire({
                            text: response.message,
                            icon: 'error',
                            buttonsStyling: false,
                            confirmButtonText: 'Ok, got it!',
                            customClass: {confirmButton: 'btn btn-light'}
                        });
                        return;
                    }

                    if (response.status === 'success') {
                        Swal.fire('Success', 'Notification settings updated successfully!', 'success');
                        return;
                    }

                    Swal.fire('Unable to save', response.message || 'Please try again.', 'error');
                },
                error: function (xhr) {
                    var response = xhr.responseJSON || {};
                    var message = response.message || 'Notification settings could not be saved. Please try again.';
                    Swal.fire('Unable to save', message, 'error');
                },
                complete: function () {
                    $saveNotificationButton
                        .prop('disabled', false)
                        .removeAttr('aria-busy')
                        .removeClass('is-loading');
                    $saveNotificationButton.find('.csn-save-label').text('Save Changes');
                }
            });
        });


            var agencyAgreementConflictConfirmed = false;
            function getAgencyCommercialConflictMessages(content) {
                var messages = [];
                var commercialVariables = ['{AGENCY_COMMERCIAL_SUMMARY}', '{FULLTIME_COMMERCIAL_TABLE}', '{FULLTIME_EXPERIENCE_PRICING}', '{CONTRACT_COMMERCIAL_TABLE}', '{CONTRACT_EXPERIENCE_PRICING}', '{FULLTIME_FEE}', '{CONTRACT_RATE}'];
                commercialVariables.forEach(function(variableCode) {
                    var occurrences = (content.match(new RegExp(variableCode.replace(/[{}]/g, '\\$&'), 'g')) || []).length;
                    if (occurrences > 1) {
                        messages.push(variableCode + ' is used ' + occurrences + ' times.');
                    }
                });
                var hasCommercialVariable = commercialVariables.some(function(variableCode) { return content.indexOf(variableCode) !== -1; });
                var plainText = $('<div>').html(content).text().toLowerCase();
                if (hasCommercialVariable && /(success fee|agency rate|commercial terms|replacement period|payment timeline|monthly rate)/i.test(plainText)) {
                    messages.push('Manual commercial wording is present along with dynamic commercial variables.');
                }

                return messages;
            }
            function confirmAgencyCommercialConflicts(content, continueCallback) {
                var messages = getAgencyCommercialConflictMessages(content);
                if (!messages.length || agencyAgreementConflictConfirmed) {
                    agencyAgreementConflictConfirmed = false;
                    continueCallback();
                    return;
                }
                Swal.fire({
                    icon: 'warning',
                    title: 'Commercial terms may conflict',
                    html: '<div style="text-align:left;font-size:13px;">' + messages.map(function(message) { return '• ' + $('<div>').text(message).html(); }).join('<br>') + '<br><br>Please review before saving.</div>',
                    showCancelButton: true,
                    confirmButtonText: 'Save Anyway',
                    cancelButtonText: 'Review'
                }).then(function(result) {
                    if (result.isConfirmed) {
                        agencyAgreementConflictConfirmed = true;
                        continueCallback();
                    }
                });
            }
            $('body').on('click', '#updateCompanyTerms', function(){
                    $("#updateCompanyTermsTop").text("Please wait...");
                    var data 				=	new FormData();
                    var kt_docs_tinymce_hidden = CKEDITOR.instances.kt_docs_tinymce_hidden.getData();	
                    if (!agencyAgreementConflictConfirmed) {
                        $("#updateCompanyTermsTop").text("Save Agreement");
                        confirmAgencyCommercialConflicts(kt_docs_tinymce_hidden, function(){
                            $('#updateCompanyTerms').trigger('click');
                        });
                        return;
                    }
                    agencyAgreementConflictConfirmed = false;
                    data.append("description", kt_docs_tinymce_hidden);
                    var form_data 			=	$('#terms_content').serializeArray();
                    $.each(form_data, function (key, input) {
                        data.append(input.name, input.value);
                    });		
                    data.append("descriptiontype", "terms");
                       
                      $.ajax({
                            url					:	'{{route('service_company.account.update', @$account->id)}}',
                            headers				:	{'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            method				:	"POST",
                            processData			:	false,
                            contentType			:	false,
                            data				:	data,
                            redirect			:	true,
                            success				:	function (data) {
                                  $("#updateCompanyTermsTop").text("Save Changes")
                                  
                                                  if(data.status == 'success'){
                                                    $('#viewTerms').html(data.terms);	
                                                      Swal.fire("Success", "Terms & Condition update successfully!", "success");	
                                                      $('#viewTerms').show();
                                                      $('#editTerms').hide(); 
                                                      if (typeof closeAgencyAgreementDrawer === 'function') closeAgencyAgreementDrawer();
                                                      $("#editBtn").html('<svg viewBox="0 0 24 24"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>Edit Agreement');
                                                      $('.updateCompanyTermsTop').hide(); 
                                                      $('.setToDefaultCompanyTermsTop').hide(); 

                                                  }
                                        }
                          });				
                });

                $('body').on('click', '#updateCompanyTermsoffer', function(){
                    $("#updateCompanyTermsofferTop").text("Please wait...");
                    var data 				=	new FormData();
                    var kt_docs_tinymce_hiddenoffer = CKEDITOR.instances.kt_docs_tinymce_hiddenoffer.getData();	
                    data.append("description", kt_docs_tinymce_hiddenoffer);
                    var form_data 			=	$('#terms_contentoffer').serializeArray();
                    $.each(form_data, function (key, input) {
                        data.append(input.name, input.value);
                    });		
                    data.set("descriptiontype", $('#candidateOfferTemplateField').val() || 'offer_terms');
                       
                      $.ajax({
                            url					:	'{{route('service_company.account.update', @$account->id)}}',
                            headers				:	{'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            method				:	"POST",
                            processData			:	false,
                            contentType			:	false,
                            data				:	data,
                            redirect			:	true,
                            success				:	function (data) {
                                                $("#updateCompanyTermsofferTop").text("Save Changes");
                                                  if(data.status == 'success'){
                                                    $('#viewTermsoffer').html(data.terms);	
                                                    updateActiveCandidateOfferTemplateCache(data.terms);
                                                       Swal.fire("Success", "Terms & Condition update successfully!", "success");
                                                       $('#viewTermsoffer').show();
                                                       $('#editTermsoffer').hide();
                                                       if (typeof closeOfferLetterDrawer === 'function') closeOfferLetterDrawer();
                                                       $("#editBtnoffer").html(getCandidateEditButtonHtml());
                                                       $('#updateCompanyTermsofferTop').hide();
                                                       $('#setToDefaultCompanyTermsofferTop').hide();

                                                  }
                                        }
                          });				
                });



                $('body').on('click', '#setToDefaultCompanyTerms', function(){
                    $("#setToDefaultCompanyTermsTop").text("Please wait...");
                    var data 				=	new FormData();
                    var kt_docs_tinymce_hidden = 'setDefault';	
                    data.append("description", kt_docs_tinymce_hidden);
                    var form_data 			=	$('#terms_content').serializeArray();
                    $.each(form_data, function (key, input) {
                        data.append(input.name, input.value);
                    });		
                    CKEDITOR.replace('kt_docs_tinymce_hidden');
                    data.append("description", kt_docs_tinymce_hidden);
                    data.append("descriptiontype", "terms");
                       
                      $.ajax({
                            url					:	'{{route('service_company.account.update', @$account->id)}}',
                            headers				:	{'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            method				:	"POST",
                            processData			:	false,
                            contentType			:	false,
                            data				:	data,
                            redirect			:	true,
                            success				:	function (data) {
                                    $("#setToDefaultCompanyTermsTop").text("Set to Default")
                                                  if(data.status == 'success'){
                                                    var kt_docs_tinymce_hidden = CKEDITOR.instances.kt_docs_tinymce_hidden.setData(data.terms);	
                                                  }
                                        }
                          });				
                });

                $('body').on('click', '#setToDefaultCompanyTermsoffer', function(){
                    
                    var data 				=	new FormData();
                    var kt_docs_tinymce_hiddenoffer = 'setDefault';	
                    data.append("description", kt_docs_tinymce_hiddenoffer);
                    $("#setToDefaultCompanyTermsofferTop").text("Please wait...");
                    var form_data 			=	$('#terms_contentoffer').serializeArray();
                    $.each(form_data, function (key, input) {
                        data.append(input.name, input.value);
                    });		
                    data.append("description", kt_docs_tinymce_hiddenoffer);
                    data.set("descriptiontype", $('#candidateOfferTemplateField').val() || 'offer_terms');
                       
                      $.ajax({
                            url					:	'{{route('service_company.account.update', @$account->id)}}',
                            headers				:	{'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            method				:	"POST",
                            processData			:	false,
                            contentType			:	false,
                            data				:	data,
                            redirect			:	true,
                            success				:	function (data) {
                                                    $("#setToDefaultCompanyTermsofferTop").text("Set to Default")
                                                  if(data.status == 'success'){
                                                    var kt_docs_tinymce_hidden = CKEDITOR.instances.kt_docs_tinymce_hiddenoffer.setData(data.terms);
                                                    $('#viewTermsoffer').html(data.terms);
                                                    updateActiveCandidateOfferTemplateCache(data.terms);
                                                  }
                                        }
                          });
                });

                $('body').on('click', '#updateCompanyTermsagencyoffer', function(){
                    $("#updateCompanyTermsagencyofferTop").text("Please wait...");
                    var data = new FormData();
                    var kt_docs_tinymce_hiddenagencyoffer = CKEDITOR.instances.kt_docs_tinymce_hiddenagencyoffer.getData();
                    data.append("description", kt_docs_tinymce_hiddenagencyoffer);
                    var form_data = $('#terms_contentagencyoffer').serializeArray();
                    $.each(form_data, function (key, input) {
                        data.append(input.name, input.value);
                    });
                    data.append("descriptiontype", "agency_offer_terms");

                      $.ajax({
                            url     :   '{{route('service_company.account.update', @$account->id)}}',
                            headers :   {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            method  :   "POST",
                            processData: false,
                            contentType: false,
                            data    :   data,
                            redirect:   true,
                            success :   function (data) {
                                                $("#updateCompanyTermsagencyofferTop").text("Save Changes");
                                                  if(data.status == 'success'){
                                                    $('#viewTermsagencyoffer').html(data.terms);
                                                      Swal.fire("Success", "Terms & Condition update successfully!", "success");
                                                      $('#viewTermsagencyoffer').show();
                                                      $('#editTermsagencyoffer').hide();
                                                      $("#editBtnagencyoffer").text("Edit");
                                                      $('.updateCompanyTermsagencyofferTop').hide();
                                                      $('.setToDefaultCompanyTermsagencyofferTop').hide();
                                                  }
                                        }
                          });
                });

                $('body').on('click', '#setToDefaultCompanyTermsagencyoffer', function(){

                    var data = new FormData();
                    data.append("description", 'setDefault');
                    $("#setToDefaultCompanyTermsagencyofferTop").text("Please wait...");
                    var form_data = $('#terms_contentagencyoffer').serializeArray();
                    $.each(form_data, function (key, input) {
                        data.append(input.name, input.value);
                    });
                    data.append("descriptiontype", "agency_offer_terms");

                      $.ajax({
                            url     :   '{{route('service_company.account.update', @$account->id)}}',
                            headers :   {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            method  :   "POST",
                            processData: false,
                            contentType: false,
                            data    :   data,
                            redirect:   true,
                            success :   function (data) {
                                                    $("#setToDefaultCompanyTermsagencyofferTop").text("Set to Default");
                                                  if(data.status == 'success'){
                                                    CKEDITOR.instances.kt_docs_tinymce_hiddenagencyoffer.setData(data.terms);
                                                  }
                                        }
                          });
                });


                $('.ser_show-detail').click(function(){
                  $('#kt_activitiesJob_toggle').trigger('click');		
                  var id    		=	$(this).attr('data-type');
                  var url   		=	"{{ route('service_company.jobs.serviceviewmore',':id') }}?viewmore=viewmore";
                  url     		=	url.replace(':id', id);
                  $.ajax({
                          type  		: 'GET',
                          url   		: url,
                          beforeSend	: function() {
                                                $('#kt_activitiesJob_body').html('<div class="d-flex flex-column align-items-center justify-content-center p-12 text-center" style="min-height:350px;"><div class="spinner-border text-primary mb-3" role="status" style="width:2.5rem;height:2.5rem;"></div><div class="fw-semibold text-gray-700 fs-6">Loading Commercial Configuration...</div></div>');
                                    },
                          success 	: function (response) {
                              $('#kt_activitiesJob_body').html(response.view);	
                          },
                          error 	: function (xhr) {
                              $('#kt_activitiesJob_body').html('<div class="d-flex flex-column align-items-center justify-content-center p-12 text-center text-danger" style="min-height:350px;"><i class="bi bi-exclamation-triangle fs-2x mb-2 text-danger"></i><div class="fw-bold fs-6 mb-1">Failed to load hiring services</div><div class="text-muted fs-8 mb-4">An error occurred while loading commercial terms. Please try again.</div><button type="button" class="btn btn-sm btn-primary ser_show-detail" data-type="'+id+'">Retry</button></div>');
                          }
                        });
                });



                $(document).on('click','.rateTypeClass', function(){
      var rateType = $(this).val();
      if(rateType==2){
            $('.rate-section').addClass('d-none');
            $('.rate-perecentage').removeClass('d-none');
      }else{
            $('.rate-section').removeClass('d-none');
            $('.rate-perecentage').addClass('d-none');
      }
      });

       $(document).on('change','select[name="sal_mode"]', function(){
            var selectedValue = $(this).val();
            $('select[name="sal_mode"]').val(selectedValue);
        });
        
        $(document).on('change','select[name="salary_currency"]', function(){
            var selectedValue = $(this).val();
            $('select[name="salary_currency"]').val(selectedValue);
        });


        $(document).on('change','.update_main_status_fulltime', function(){

           var $toggle = $(this);
           var changeStatus = $toggle.is(':checked') ? 1 : 0;
           var previousStatus = changeStatus ? 0 : 1;
           var id = $toggle.attr('data-row_id');
           $toggle.prop('disabled', true);
           
           var url       	=	"{{ route('service_company.jobs.updateserviceviewmore',':id') }}";
           var token       =	"{{ csrf_token() }}";
                $.ajax({
                                   type		: 'POST',
                                   url			: url,
                                   data		: {'_token': token, '_method': 'POST', 'id' : id, 'changeStatus':changeStatus},
                                   success 	: function (response) {
                                           if (response.status == "success") {
                                             $('#f_text').text(changeStatus ? 'ON' : 'OFF');
                                             $('#f_status_badge').text(changeStatus ? 'Active' : 'Inactive').toggleClass('is-off', !changeStatus);
                                             if(changeStatus==1){
                                                $('.class_change_fulltime').removeClass('cv2-disabled').attr('style','');
                                             }else{
                                                $('.class_change_fulltime').addClass('cv2-disabled').attr('style','opacity: 0.2 !important;');
                                             }
                                             $toggle.val(changeStatus);
                                           } else {
                                             $toggle.prop('checked', previousStatus === 1);
                                           }
                                     },
                                   error: function () {
                                     $toggle.prop('checked', previousStatus === 1);
                                   },
                                   complete: function () {
                                     $toggle.prop('disabled', false);
                                   }
                           });
                          


        });

        $(document).on('change','.contract_main_status_fulltime', function(){

        var $toggle = $(this);
        var changeStatus = $toggle.is(':checked') ? 1 : 0;
        var previousStatus = changeStatus ? 0 : 1;
        var id = $toggle.attr('data-row_id');
        $toggle.prop('disabled', true);

        var url       	=	"{{ route('service_company.jobs.updateserviceviewmore',':id') }}";
        var token       =	"{{ csrf_token() }}";
            $.ajax({
                                type		: 'POST',
                                url			: url,
                                data		: {'_token': token, '_method': 'POST', 'id' : id, 'changeStatus':changeStatus},
                                success 	: function (response) {
                                        if (response.status == "success") {
                                          $('#c_text').text(changeStatus ? 'ON' : 'OFF');
                                          $('#c_status_badge').text(changeStatus ? 'Active' : 'Inactive').toggleClass('is-off', !changeStatus);
                                          if(changeStatus==1){
                                                $('.class_change_contract').removeClass('cv2-disabled').attr('style','');
                                             }else{
                                                $('.class_change_contract').addClass('cv2-disabled').attr('style','opacity: 0.2 !important;');
                                             }
                                          $toggle.val(changeStatus);
                                        } else {
                                          $toggle.prop('checked', previousStatus === 1);
                                        }
                                  },
                                error: function () {
                                  $toggle.prop('checked', previousStatus === 1);
                                },
                                complete: function () {
                                  $toggle.prop('disabled', false);
                                }
                        });
                      


        });





        $('body').on('click', '#editBtnCampaign', function(){
			
              var url 				=	"{{ route('service_company.auth-custom-package.validatePackage') }}";
              var token 				=	"{{ csrf_token() }}";							
              var baseurl    			=	"{{ asset('assets/images/Loading2.gif') }}"; 
              var eventId 			=	0; 
              $.ajax({
                    type		: 'POST',
                    url			: url,
                    beforeSend	: function(){ },
                    data		: {'_token': token, 'event':'KEYWORD_ADD','eventId':eventId},
                    success		: function (response){
                                          if (response.status == "success"){  																					  																												                                                                                            	var url   			=	"{{url('service_company/kayaccount/key-add-create')}}";
                                                window.location 	=	url;
                                            }else if (response.status == "info"){   
                                              sweetAlert("Fail!", response.message, "error");	
                                            }										 
                                    }
                  });
          
        });



        // Intent: save the complete commercial editor payload immediately while preventing duplicate submissions.
        $(document).on('click', '#commercial-editor-v3 .copy_current_selected_applicant', function () {
            var $saveButton = $(this);
            var $editor = $saveButton.closest('#commercial-editor-v3');

            if ($saveButton.prop('disabled')) {
                return;
            }

            var id = 1;
            var url = "{{ route('service_company.jobs.addUpdateJobServices', ':id') }}".replace(':id', id);
            var token = "{{ csrf_token() }}";
            var arrCountry = $editor.find('.countries:checked').map(function () {
                return this.value;
            }).get();
            var countryCount = arrCountry.length;
            var rateVal = $editor.find('.rate_val').map(function () {
                return this.value;
            }).get();
            var rateTypeClass = $editor.find('[data-commercial-type]').val() || $editor.find('.rateTypeClass:checked').val() || $editor.find('.rateTypeClass').val();
            var mainStatus = $editor.find('.main_status').val();
            var salMode = $editor.find('select[name="sal_mode"]').val();
            var salaryCurrency = $editor.find('select[name="salary_currency"]').val();
            var replacementPeriodDays = $editor.find('[data-replacement-period]').val();
            var replacementType = $editor.find('[data-replacement-type]').val();
            var replacementEligibility = $editor.find('[data-replacement-eligibility]').val();
            var billingFrequency = $editor.find('[data-billing-frequency]').val();
            var billingTrigger = $editor.find('[data-billing-trigger]').val();
            var paymentTermsDays = $editor.find('[data-payment-terms]').val();
            var updateId = $editor.find('.updateId').val();
            var commType = $editor.find('.commType').val();

            if (String(commType) === '2') {
                rateVal = $editor.find('.perecentage_val').map(function () {
                    return this.value;
                }).get();
            }

            if (String(rateTypeClass) === '0') {
                rateVal = rateVal.map(function() { return '0'; });
            }

            $saveButton.prop('disabled', true).addClass('is-saving').attr('aria-busy', 'true');

            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    '_token': token,
                    '_method': 'POST',
                    'arrCountry': arrCountry,
                    'rate_val': rateVal,
                    'rateTypeClass': rateTypeClass,
                    'sal_mode': salMode,
                    'salary_currency': salaryCurrency,
                    'replacement_period_days': replacementPeriodDays,
                    'replacement_type': replacementType,
                    'replacement_eligibility': replacementEligibility,
                    'billing_frequency': billingFrequency,
                    'billing_trigger': billingTrigger,
                    'payment_terms_days': paymentTermsDays,
                    'updateId': updateId,
                    'commType': commType,
                    'main_status': mainStatus
                },
                success: function (response) {
                    if (response.status === 'success') {
                        if (String(commType) === '2') {
                            $('#fullTimeCount').text(countryCount);
                        }
                        if (String(commType) === '1') {
                            $('#contractCount').text(countryCount);
                        }

                        // Dispatch canonical state to window so ServicesTabEngine and any other open listeners re-hydrate
                        var serviceType = response.service_type || (String(commType) === '2' ? 'fulltime' : 'contract');
                        var eventDetail = {
                            version: response.version || 1,
                            service_type: serviceType,
                            state: response.commercial_state || {}
                        };

                        window.dispatchEvent(new CustomEvent('commercial:saved', { detail: eventDetail }));
                        $editor.get(0).dispatchEvent(new CustomEvent('commercial:saved', { detail: eventDetail }));

                        // 1. Direct instantaneous re-hydration of listing page
                        if (window.ServicesTabEngine && typeof window.ServicesTabEngine.rehydrateServiceCard === 'function') {
                            window.ServicesTabEngine.rehydrateServiceCard(serviceType, response.commercial_state);
                        }

                        // Ensure Services tab is active and visible
                        if ($('#services').length && !$('#services').hasClass('active')) {
                            $('.nav-link[href="#services"], .nav-link[data-bs-target="#services"]').trigger('click');
                            $('#services').addClass('active show');
                        }

                        // 2. Automatically close sliding drawer (#kt_activitiesJob) and return to listing page
                        var drawerEl = document.getElementById('kt_activitiesJob');
                        if (window.KTDrawer && KTDrawer.getInstance(drawerEl)) {
                            KTDrawer.getInstance(drawerEl).hide();
                        }
                        $('#kt_activitiesJob_close').trigger('click');
                        $('.cd-drw-close-btn').trigger('click');
                        $(drawerEl).removeClass('drawer-on');
                        $('.drawer-overlay').remove();
                        $('body').removeAttr('data-kt-drawer');

                        Swal.fire({toast: true, position: 'top-end', icon: 'success', title: 'Commercial terms saved successfully.', showConfirmButton: false, timer: 2200, timerProgressBar: true});
                        return;
                    }

                    Swal.fire({toast: true, position: 'top-end', icon: 'error', title: response.message || 'Services could not be saved.', showConfirmButton: false, timer: 3200});
                },
                error: function (xhr) {
                    if (xhr && xhr.status === 409) {
                        var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Commercial terms were updated by another administrator. Please refresh before saving.';
                        Swal.fire({toast: true, position: 'top-end', icon: 'warning', title: msg, showConfirmButton: false, timer: 4000});
                        return;
                    }
                    if (xhr && xhr.status === 403) {
                        Swal.fire({toast: true, position: 'top-end', icon: 'error', title: 'You are not authorized to update commercial terms.', showConfirmButton: false, timer: 3500});
                        return;
                    }
                    Swal.fire({toast: true, position: 'top-end', icon: 'error', title: 'Services could not be saved. Please try again.', showConfirmButton: false, timer: 3200});
                },
                complete: function () {
                    $saveButton.prop('disabled', false).removeClass('is-saving').removeAttr('aria-busy');
                }
            });
        });

    // Synchronize sub-tabs pill button active state (Rule 20 Modern Card + Sub-Card)
    $(document).on('shown.bs.tab', '.cd-drawer-tabs-list a[data-toggle="tab"], .cd-drawer-tabs-list a[data-bs-toggle="tab"]', function (e) {
        $('.cd-drawer-tabs-list .cd-tab-pill-btn').removeClass('active');
        $(e.target).addClass('active');
    });

    $(document).on('click', '.cd-drawer-tabs-list .cd-tab-pill-btn', function (e) {
        $('.cd-drawer-tabs-list .cd-tab-pill-btn').removeClass('active');
        $(this).addClass('active');
    });

    // ── Dynamically Initialize Company Activity Feed (Overview + Activity Tab) ──
    (function () {
        function refreshCompanyActivity() {
            if (window.CompanyActivityManager && typeof window.CompanyActivityManager.loadActivity === 'function') {
                window.CompanyActivityManager.loadActivity(false);
            }
        }

        // Re-hydrate / refresh when switching to Activity tab
        $(document).on('shown.bs.tab', '.cd-drawer-tabs-list a[href="#recent_activity"]', function (e) {
            refreshCompanyActivity();
        });

        // "View all activity ->" link switches smoothly to Activity tab
        $(document).on('click', '.cd-view-all-activity', function (e) {
            e.preventDefault();
            var $actTab = $('.cd-drawer-tabs-list .cd-tab-pill-btn[href="#recent_activity"]');
            if ($actTab.length) {
                if (typeof $actTab.tab === 'function') {
                    $actTab.tab('show');
                } else {
                    $actTab.trigger('click');
                }
                refreshCompanyActivity();
            }
        });
    })();

    // ── Interview Pipeline Builder (Stack-Based Architecture Rule 20) Helpers ──
    window.ipsToggleStage = function(cardId, event) {
        if ($(event.target).closest('.ips-btn-add-sub, .ips-btn-del-subcard, .ips-drag-handle, a, button').length) {
            return;
        }
        $('#' + cardId).toggleClass('collapsed');
    };

    window.ipsFilterStage = function(stageKey, btn) {
        $('#ipsFilterTabs .ips-tab-btn').removeClass('active');
        $(btn).addClass('active');

        if (stageKey === 'all') {
            $('.ips-stack-card').show().removeClass('collapsed');
        } else {
            $('.ips-stack-card').each(function() {
                if ($(this).data('stage') === stageKey) {
                    $(this).show().removeClass('collapsed');
                    $('html, body').animate({
                        scrollTop: $(this).offset().top - 90
                    }, 200);
                } else {
                    $(this).hide();
                }
            });
        }
    };

    window.ipsUpdatePriority = function(data) {
        if (!data || !data.length) return;
        $.ajax({
            type: 'POST',
            url: "{{ route('service_company.account.updatePriority') }}",
            data: {
                '_token': "{{ csrf_token() }}",
                'data': data
            },
            success: function(res) {}
        });
    };

    window.updateIpsCounters = function() {
        var globalTotal = 0;
        var stageKeys = ['sourcing', 'screening', 'evaluation', 'negotation', 'offer', 'joined', 'rejected'];
        stageKeys.forEach(function(key) {
            var rootId = 'root_' + key;
            var count = $('#' + rootId + ' > li.ips-subcard-item').length;
            globalTotal += count;

            // Update tab badge
            $('#ipsTabCount_' + key).text(count);

            // Update stage card count pill
            var $pill = $('#ipsCountPill_' + key);
            if ($pill.length) {
                $pill.html('<i class="fas fa-layer-group"></i> <span class="count-num">' + count + '</span> Sub-Stage' + (count === 1 ? '' : 's'));
                if (count > 0) {
                    $pill.addClass('has-items');
                    $('#ipsEmpty_' + key).hide();
                } else {
                    $pill.removeClass('has-items');
                    $('#ipsEmpty_' + key).show();
                }
            }

            // Re-index steps
            $('#' + rootId + ' > li.ips-subcard-item').each(function(idx) {
                $(this).find('.ips-subcard-seq').text('Step ' + (idx + 1));
            });
        });

        $('#ipsTabCountAll').text(globalTotal);
        $('#ipsGlobalTotalBadge').text(globalTotal + ' Sub-Stages Defined');
    };

    // Live 300ms Debounced Search for Pipeline Builder
    (function() {
        var searchTimer = null;
        $(document).on('input', '#ipsSearchInput', function() {
            clearTimeout(searchTimer);
            var q = $(this).val().trim().toLowerCase();
            if (q.length > 0) {
                $('#ipsSearchClear').addClass('visible');
            } else {
                $('#ipsSearchClear').removeClass('visible');
            }
            searchTimer = setTimeout(function() {
                ipsPerformSearch(q);
            }, 250);
        });

        $(document).on('click', '#ipsSearchClear', function() {
            $('#ipsSearchInput').val('');
            $(this).removeClass('visible');
            ipsPerformSearch('');
        });

        function ipsPerformSearch(q) {
            $('.ips-stack-card').each(function() {
                var $card = $(this);
                var title = $card.find('.ips-stack-title').text().toLowerCase();
                var desc = $card.find('.ips-stack-desc').text().toLowerCase();
                var hasSubMatch = false;

                $card.find('.ips-subcard-item').each(function() {
                    var name = ($(this).data('name') || $(this).text()).toLowerCase();
                    if (q === '' || name.indexOf(q) !== -1) {
                        $(this).show();
                        hasSubMatch = true;
                    } else {
                        $(this).hide();
                    }
                });

                if (q === '' || title.indexOf(q) !== -1 || desc.indexOf(q) !== -1 || hasSubMatch) {
                    $card.show().removeClass('collapsed');
                } else {
                    $card.hide();
                }
            });
        }
    })();

    // Initialize jQuery UI sortable on sub-cards if loaded
    $(document).ready(function() {
        if (typeof $.fn.sortable === 'function') {
            ['root_sourcing', 'root_screening', 'root_evaluation', 'root_negotation', 'root_offer', 'root_joined', 'root_rejected'].forEach(function(rootId) {
                $('#' + rootId).sortable({
                    handle: '.ips-drag-handle',
                    items: '> li.ips-subcard-item',
                    update: function(event, ui) {
                        var order = [];
                        $('#' + rootId + ' > li.ips-subcard-item').each(function(idx) {
                            order.push($(this).data('id') || $(this).attr('id'));
                            $(this).find('.ips-subcard-seq').text('Step ' + (idx + 1));
                        });
                        ipsUpdatePriority(order);
                    }
                });
            });
        }
    });


</script>	
@endpush
