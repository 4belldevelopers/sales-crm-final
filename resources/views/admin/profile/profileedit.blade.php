@extends('layouts.demo_admin_app')
@push('head-script')
@endpush
<style>

</style>
@section('content')

<!--begin::Toolbar-->
<div class="toolbar" id="kt_toolbar">
  <!--begin::Container-->
  <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
    <!--begin::Page title-->
    <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
      <!--begin::Add Requisition-->      
        <div class="d-flex flex-column">          
            <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1"><a href="javascript:void(0);" class="text-gray-800 mb-1">Edit Profile</a></h1>            
        </div>
      <!--end::Add Requisition-->
      <!--begin::Separator-->
      <span class="h-20px border-gray-200 border-start mx-4"></span>
      <!--end::Separator-->
      <span class="text-dark-800 fw-bold" id="kt_subheader_total">Edit profile details and save</span>
    </div>
    <!--end::Page title-->
  </div>
  <!--end::Container-->
</div>
<!--end::Toolbar-->

<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid pt-4" id="kt_content">
  <!--begin::Post-->
  <div class="post d-flex flex-column-fluid" id="kt_post">
    <!--begin::Container-->
    <div id="kt_content_container" class="container-xxl px-2">
      <!--begin::Card-->
      <div class="card">
        <!--begin::Card body-->
        <div class="card-body pt-3 px-5">
            <!--begin::Form-->
            <form id="kt_account_profile_details_form" class="form fv-plugins-bootstrap5 fv-plugins-framework" novalidate="novalidate">
                <!--begin::Card body-->
                <div class="card-body p-9">
                    <!--begin::Input group-->
                    <div class="row mb-6">
                        <!--begin::Label-->
                        <label class="col-lg-3 col-form-label fw-bold fs-6">Change Image</label>
                        <!--end::Label-->
                        <!--begin::Col-->
                        <div class="col-lg-9">
                            <!--begin::Image input-->
                            <div class="image-input image-input-outline" data-kt-image-input="true" style="background-image: url('')">
                            <!--begin::Preview existing avatar-->
                            <div class="image-input-wrapper w-125px h-125px" style="background-image: url('https://preview.keenthemes.com/metronic8/demo1/assets/media/avatars/blank.png')"></div>
                            <!--end::Preview existing avatar-->
                            <!--begin::Label-->
                            <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" title="" data-bs-original-title="Change avatar">
                                <i class="bi bi-pencil-fill fs-7"></i>
                                <!--begin::Inputs-->
                                <input type="file" name="avatar" accept=".png, .jpg, .jpeg">
                                <input type="hidden" name="avatar_remove">
                                <!--end::Inputs-->
                            </label>
                            <!--end::Label-->
                            <!--begin::Cancel-->
                            <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="" data-bs-original-title="Cancel avatar">
                            <i class="bi bi-x fs-2"></i>
                            </span>
                            <!--end::Cancel-->
                            <!--begin::Remove-->
                            <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="" data-bs-original-title="Remove avatar">
                            <i class="bi bi-x fs-2"></i>
                            </span>
                            <!--end::Remove-->
                            </div>
                            <!--end::Image input-->
                            <!--begin::Hint-->
                            <div class="form-text">Allowed file types: png, jpg, jpeg.</div>
                            <!--end::Hint-->
                        </div>
                        <!--end::Col-->
                    </div>
                    <!--end::Input group-->
                    <!--begin::Full Name-->
                    <div class="row mb-6">
                        <!--begin::Label-->
                        <label class="col-lg-3 col-form-label required fw-bold fs-6">Full Name</label>
                        <!--end::Label-->
                        <!--begin::Col-->
                        <div class="col-lg-9 fv-row fv-plugins-icon-container">
                            <input type="text" name="fullname" class="form-control form-control-lg form-control-solid" placeholder="Enter Full name" value="">
                            <div class="fv-plugins-message-container invalid-feedback"></div>
                        </div>
                        <!--end::Col-->
                    </div>
                    <!--end::Full Name-->
                    <!--begin::Designation-->
                    <div class="row mb-6">
                        <!--begin::Label-->
                        <label class="col-lg-3 col-form-label required fw-bold fs-6">Designation</label>
                        <!--end::Label-->
                        <!--begin::Col-->
                        <div class="col-lg-9 fv-row fv-plugins-icon-container">
                            <input type="text" name="designation" class="form-control form-control-lg form-control-solid" placeholder="Enter Designation" value="">
                            <div class="fv-plugins-message-container invalid-feedback"></div>
                        </div>
                        <!--end::Col-->
                    </div>
                    <!--end::Designation-->
                    <!--begin::Email-->
                    <div class="row mb-6">
                        <!--begin::Label-->
                        <label class="col-lg-3 col-form-label required fw-bold fs-6">Email</label>
                        <!--end::Label-->
                        <!--begin::Col-->
                        <div class="col-lg-9 fv-row fv-plugins-icon-container">
                            <input type="email" name="email" id="profile_email" class="form-control form-control-lg form-control-solid" placeholder="Enter Email" value="" maxlength="254" inputmode="email" autocomplete="email" autocapitalize="none" spellcheck="false">
                            <div id="profile_email_typo_hint" class="email-typo-hint-container mt-1" style="display: none; font-size: 11px;"></div>
                            <div class="fv-plugins-message-container invalid-feedback"></div>
                        </div>
                        <!--end::Col-->
                    </div>
                    <!--end::Email-->
                    <!--begin::Phone-->
                    <div class="row mb-6">
                        <!--begin::Label-->
                        <label class="col-lg-3 col-form-label required fw-bold fs-6">Phone</label>
                        <!--end::Label-->
                        <!--begin::Col-->
                        <div class="col-lg-9 fv-row fv-plugins-icon-container">
                            <input type="number" name="phone" class="form-control form-control-lg form-control-solid" placeholder="Enter Phone" value="">
                            <div class="fv-plugins-message-container invalid-feedback"></div>
                        </div>
                        <!--end::Col-->
                    </div>
                    <!--end::Phone-->
                    <!--begin::Country-->
                    <div class="row mb-6">
                        <!--begin::Label-->
                        <label class="col-lg-3 col-form-label fw-bold fs-6">
                            <span class="required">Country</span>
                        </label>
                        <!--end::Label-->
                        <!--begin::Col-->
                        <div class="col-lg-9 fv-row">
                            <select name="country" aria-label="Select a Country" data-control="select2" data-placeholder="Select a country..." class="form-select form-select-solid form-select-lg fw-bold">
                                <option value="">Select a Country...</option>
                                <option data-kt-flag="flags/india.svg" value="AF">India</option>
                                <option data-kt-flag="flags/aland-islands.svg" value="AX">Aland Islands</option>
                                <option data-kt-flag="flags/albania.svg" value="AL">Albania</option>
                                <option data-kt-flag="flags/algeria.svg" value="DZ">Algeria</option>
                                <option data-kt-flag="flags/eritrea.svg" value="ER">Eritrea</option>
                                <option data-kt-flag="flags/yemen.svg" value="YE">Yemen</option>
                                <option data-kt-flag="flags/zambia.svg" value="ZM">Zambia</option>
                                <option data-kt-flag="flags/zimbabwe.svg" value="ZW">Zimbabwe</option>
                            </select>
                        </div>
                        <!--end::Col-->
                    </div>
                    <!--end::Country-->
                    <!--begin::State-->
                    <div class="row mb-6">
                        <!--begin::Label-->
                        <label class="col-lg-3 col-form-label fw-bold fs-6">
                            <span class="required">State</span>
                        </label>
                        <!--end::Label-->
                        <!--begin::Col-->
                        <div class="col-lg-9 fv-row">
                            <select name="state" aria-label="Select a State" data-control="select2" data-placeholder="Select a state..." class="form-select form-select-solid form-select-lg fw-bold">
                                <option value="">Select a State...</option>
                                <option data-kt-flag="flags/uttar_pradesh.svg" value="AF">Uttar Pradesh</option>
                                <option data-kt-flag="flags/haryana.svg" value="AX">Haryana</option>
                                <option data-kt-flag="flags/gujrat.svg" value="AL">Gujrat</option>
                                <option data-kt-flag="flags/punjab.svg" value="DZ">Punjab</option>
                                <option data-kt-flag="flags/kolkata.svg" value="ER">Kolkata</option>
                            </select>
                        </div>
                        <!--end::Col-->
                    </div>
                    <!--end::State-->
                    <!--begin::City-->
                    <div class="row mb-6">
                        <!--begin::Label-->
                        <label class="col-lg-3 col-form-label fw-bold fs-6">
                            <span class="">City</span>
                        </label>
                        <!--end::Label-->
                        <!--begin::Col-->
                        <div class="col-lg-9 fv-row">
                            <select name="city" aria-label="Select a City" data-control="select2" data-placeholder="Select a city..." class="form-select form-select-solid form-select-lg fw-bold">
                                <option value="">Select a City...</option>
                                <option data-kt-flag="flags/noida.svg" value="AF">Noida</option>
                                <option data-kt-flag="flags/pune.svg" value="AX">Pune</option>
                                <option data-kt-flag="flags/mumbai.svg" value="AL">Mumbai</option>
                                <option data-kt-flag="flags/gurgaon.svg" value="DZ">Gurgaon</option>
                                <option data-kt-flag="flags/delhi.svg" value="ER">Delhi</option>
                            </select>
                        </div>
                        <!--end::Col-->
                    </div>
                    <!--end::City-->
                </div>
                <!--end::Card body-->
                <!--begin::Actions-->
                <div class="card-footer d-flex justify-content-center py-6 px-9">
                    <button type="button" class="btn btn-light btn-active-light-primary me-2">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="kt_account_profile_details_submit">Save Changes</button>
                </div>
                <!--end::Actions-->
                <input type="hidden">
            </form>
            <!--end::Form-->
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
<!--begin::Script-->
<script>
    /******/ (() => { // webpackBootstrap
/******/ 	"use strict";
var __webpack_exports__ = {};
/*!*********************************************************************************************!*\
  !*** FORM VALIDATION***!
  \*********************************************************************************************/


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
                $input.val(cleaned).trigger('input').trigger('change');
            }
        });

        $input.on('input', function() {
            var val = $(this).val();
            var cleaned = self.cleanDeterministic(val);
            var suggestion = self.suggestCorrection(cleaned);
            if (suggestion && suggestion.toLowerCase() !== cleaned.toLowerCase()) {
                self.renderTypoPill(hintSelector, suggestion, inputSelector);
            } else {
                $(hintSelector).slideUp(100, function() { $(this).empty(); });
            }
        });

        $input.on('blur', function() {
            var val = $(this).val();
            if (val) {
                $(this).val(self.cleanDeterministic(val));
            }
        });
    }
};

// Class definition
var KTAccountSettingsProfileDetails = function () {
    // Private variables
    var form;
    var submitButton;
    var validation;

    // Private functions
    var initValidation = function () {
        // Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
        validation = FormValidation.formValidation(
            form,
            {
                fields: {
                    fullname: {
                        validators: {
                            notEmpty: {
                                message: 'Full name is required'
                            }
                        }
                    },
                    designation: {
                        validators: {
                            notEmpty: {
                                message: 'Designation is required'
                            }
                        }
                    },
                    email: {
                        validators: {
                            notEmpty: {
                                message: 'Email is required'
                            },
                            callback: {
                                message: 'Please enter a valid email address',
                                callback: function(input) {
                                    if (!input.value) return true;
                                    var cleaned = window.JeroEmailBoxEngine ? window.JeroEmailBoxEngine.cleanDeterministic(input.value) : input.value.trim();
                                    return window.JeroEmailBoxEngine ? window.JeroEmailBoxEngine.validateSyntax(cleaned) : true;
                                }
                            }
                        }
                    },
                    phone: {
                        validators: {
                            notEmpty: {
                                message: 'Phone is required'
                            }
                        }
                    },
                    country: {
                        validators: {
                            notEmpty: {
                                message: 'Please select a country'
                            }
                        }
                    },
                    state: {
                        validators: {
                            notEmpty: {
                                message: 'Please select a state'
                            }
                        }
                    },
                    city: {
                        validators: {
                            notEmpty: {
                                message: 'Please select a city'
                            }
                        }
                    },
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    submitButton: new FormValidation.plugins.SubmitButton(),
                    //defaultSubmit: new FormValidation.plugins.DefaultSubmit(), // Uncomment this line to enable normal button submit after form validation
                    bootstrap: new FormValidation.plugins.Bootstrap5({
                        rowSelector: '.fv-row',
                        eleInvalidClass: '',
                        eleValidClass: ''
                    })
                }
            }
        );

        // Select2 validation integration
        $(form.querySelector('[name="country"]')).on('change', function() {
            // Revalidate the color field when an option is chosen
            validation.revalidateField('country');
        });

        $(form.querySelector('[name="state"]')).on('change', function() {
            // Revalidate the color field when an option is chosen
            validation.revalidateField('state');
        });

        $(form.querySelector('[name="city"]')).on('change', function() {
            // Revalidate the color field when an option is chosen
            validation.revalidateField('city');
        });
    }

    var handleForm = function () {
        submitButton.addEventListener('click', function (e) {
            e.preventDefault();

            validation.validate().then(function (status) {
                if (status == 'Valid') {

                    swal.fire({
                        text: "Thank you! You've updated your basic info",
                        icon: "success",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                            confirmButton: "btn fw-bold btn-light-primary"
                        }
                    });

                } else {
                    swal.fire({
                        text: "Sorry, looks like there are some errors detected, please try again.",
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                            confirmButton: "btn fw-bold btn-light-primary"
                        }
                    });
                }
            });
        });
    }

    // Public methods
    return {
        init: function () {
            form = document.getElementById('kt_account_profile_details_form');
            submitButton = form.querySelector('#kt_account_profile_details_submit');

            initValidation();

            if (window.JeroEmailBoxEngine) {
                window.JeroEmailBoxEngine.setupEmailBox('#profile_email', '#profile_email_typo_hint');
                $(document).off('click', '#profile_email_typo_hint .apply-email-fix').on('click', '#profile_email_typo_hint .apply-email-fix', function(e) {
                    e.preventDefault();
                    var suggestion = $(this).attr('data-suggestion');
                    if (suggestion) {
                        $('#profile_email').val(suggestion).trigger('input').trigger('change');
                        $('#profile_email_typo_hint').slideUp(100, function() { $(this).empty(); });
                    }
                });
            }
        }
    }
}();

// On document ready
KTUtil.onDOMContentLoaded(function() {
    KTAccountSettingsProfileDetails.init();
});

/******/ })()
;
//# FORM VALIDATION
</script>
<!--end::Script-->
@endpush