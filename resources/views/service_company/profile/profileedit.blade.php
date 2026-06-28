@extends('layouts.service_company_layout')
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
      <span class="text-dark-800 fw-bold" id="kt_subheader_total">Edit profile details and save</span> </div>
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
                    <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" title="" data-bs-original-title="Change avatar"> <i class="bi bi-pencil-fill fs-7"></i>
                    <!--begin::Inputs-->
                    <input type="file" name="avatar" accept=".png, .jpg, .jpeg">
                    <input type="hidden" name="avatar_remove">
                    <!--end::Inputs-->
                    </label>
                    <!--end::Label-->
                    <!--begin::Cancel-->
                    <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="" data-bs-original-title="Cancel avatar"> <i class="bi bi-x fs-2"></i> </span>
                    <!--end::Cancel-->
                    <!--begin::Remove-->
                    <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="" data-bs-original-title="Remove avatar"> <i class="bi bi-x fs-2"></i> </span>
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
                  <input type="email" name="email" class="form-control form-control-lg form-control-solid" placeholder="Enter Email" value="">
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
                <label class="col-lg-3 col-form-label fw-bold fs-6"> <span class="required">Country</span> </label>
                <!--end::Label-->
                <!--begin::Col-->
                <div class="col-lg-9 fv-row">
                  <select name="country" aria-label="Select a Country" data-placeholder="Select a country..." class="form-select form-select-solid form-select-lg fw-bold">
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
                <label class="col-lg-3 col-form-label fw-bold fs-6"> <span class="required">State</span> </label>
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
                <label class="col-lg-3 col-form-label fw-bold fs-6"> <span class="">City</span> </label>
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