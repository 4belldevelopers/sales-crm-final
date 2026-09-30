@extends('layouts.product_company_layout')
@push('head-script')
<style>
body {overflow-x:hidden; } .dropzone {padding: 1.8rem 1.75rem!important; border: 2px dashed #e6e7e8!important; background:transparent!important; text-align: inherit!important;}	.font-size-icon {font-size: 1.3em; } .dashed-border-top{border-top:1px dashed gray;} .form {clear: both; }
.form--disabled input, .form--disabled textarea, .form--disabled select{ padding-left: 0; padding-top: 4px; border: 0 solid #efefef; background: white; height: auto; transition: all 0.2s ease-in-out; font-size: 1.05rem; font-weight: 500; color: #3f4254; text-transform: capitalize;}
.enabled_input{display: block; width: 100%; border-radius: 2px; border: 1px solid #efefef; outline: none; -webkit-appearance: none; background-color: #f5f8fa; border-color: #f5f8fa; color: #5e6278; padding: 0.5em; transition: color 0.2s ease, background-color 0.2s ease;}
#calender::-webkit-inner-spin-button,
#calender::-webkit-calendar-picker-indicator { display: none; -webkit-appearance: none; }
#dob::-webkit-inner-spin-button,
#dob::-webkit-calendar-picker-indicator { display: none; -webkit-appearance: none; }
.br-currency-select2-dropdown {
    font-family: 'Poppins', sans-serif;
    font-size: 13px;
    border: 1px solid #cbd5e1 !important;
    border-radius: 8px !important;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.12), 0 8px 10px -6px rgba(0, 0, 0, 0.06) !important;
    overflow: hidden;
    z-index: 1060;
}
.br-currency-select2-dropdown .select2-search--dropdown {
    padding: 8px 10px;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
}
.br-currency-select2-dropdown .select2-search--dropdown .select2-search__field {
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    padding: 6px 12px;
    font-size: 13px;
    outline: none;
    font-family: 'Poppins', sans-serif;
    background-color: #ffffff;
    width: 100%;
}
.br-currency-select2-dropdown .select2-search--dropdown .select2-search__field:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
}
.br-currency-select2-dropdown .select2-results__options {
    max-height: 260px;
}
.br-currency-select2-dropdown .select2-results__option {
    padding: 7px 12px;
    font-size: 13px;
    font-weight: 500;
    color: #334155;
    line-height: 1.4;
}
.br-currency-select2-dropdown .select2-results__option--highlighted[aria-selected] {
    background-color: #3b82f6 !important;
    color: #ffffff !important;
}
.br-currency-select2-dropdown .select2-results__option[aria-selected="true"] {
    background-color: #eff6ff;
    color: #1d4ed8;
    font-weight: 600;
}
</style>
@endpush

@section('content')
@php 
	use App\Helper\GeneralMethod;	
	$customTitleId	=	0;
@endphp 

<!--begin::Toolbar-->
<div class="toolbar" id="kt_toolbar">
  <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
    <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
      <div class="d-flex flex-column">
        <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1"> <a href="javascript:void(0);" class="text-gray-800 mb-1">Add Talent Pool</a> </h1>
      </div>
      <span class="h-20px border-gray-200 border-start mx-4"></span>
      <span class="text-dark-800 fw-bold" id="kt_subheader_total">Enter talent pool details and submit</span> </div>
  </div>
</div>

<div class="content d-flex flex-column flex-column-fluid pt-4" id="kt_content">
  <div class="post d-flex flex-column-fluid" id="kt_post">
    <div id="kt_content_container" class="container-fluid px-3">
      <div class="card">
        <div class="card-body pt-3 px-5">
          <h3 class="py-3"></h3>
          <form class="ajax-form" method="POST" id="createForm" name="createForm" enctype="multipart/form-data">
            @csrf
            <div class="mb-5">
              <div class="flex-column">
                <div class="form-group row fv-row mb-10">
                  <div class="col-lg-4 col-xl-4 text-center">
                    <div class="image-input image-input-outline" data-kt-image-input="true">
                      <div class="image-input-wrapper w-90px h-90px" style="background-image: url({{ asset('img/default.png') }})"></div>
                      <label class="btn btn-icon btn-circle btn-active-color-primary w-20px h-20px bg-white shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" data-bs-dismiss="click" title="Change avatar"> <i class="bi bi-pencil-fill fs-9"></i>
                      <input type="file" name="photo" id="avatar" accept=".png, .jpg, .jpeg" />
                      <input type="hidden" name="avatar_remove" />
                      </label>
                      <span class="btn btn-icon btn-circle btn-active-color-primary w-20px h-20px bg-white shadow" data-kt-image-input-action="cancel" data-bs-toggle="tooltip" data-bs-dismiss="click" title="Cancel avatar"> <i class="bi bi-x fs-4"></i> </span>
                      <span class="btn btn-icon btn-circle btn-active-color-primary w-20px h-20px bg-white shadow" data-kt-image-input-action="remove" data-bs-toggle="tooltip" data-bs-dismiss="click" title="Remove avatar"> <i class="bi bi-x fs-4"></i> </span>
                    </div>
                    <span class="form-text text-muted d-block">Allowed file types: png,jpg, jpeg.</span>
                  </div>
                  <div class="col-lg-8 col-xl-8">
                    <input class="select-file w-100 mb-2 dropzone bg-light " accept=".png,.jpg,.jpeg,.pdf,.doc,.docx,.xls,.xlsx,.rtf" type="file" name="resume" id="resume">
                    <div class="fv-row"> <span class="form-text text-muted d-block">Allowed file types: Pdf or Word. &nbsp;</span> </div>
                  </div>
                </div>
                <div class="form-group row fv-row mb-10">
                  <div class="col-lg-4">
                    <label class="form-label required">Full Name:</label>
                    <input type="text" class="form-control form-control-solid" name="full_name" id="full_name" placeholder="Enter full name" value="">
                    <span class="form-text text-muted">Please enter your full name</span> </div>
                  <div class="col-lg-4">
                    <label class="form-label required">Designation:</label>
                    <input type="text" class="form-control form-control-solid" name="current_designation" id="current_designation" placeholder="Enter designation" value="">
                    <span class="form-text text-muted">Please enter your designation</span> </div>
                  <div class="col-lg-4">
                    <label class="form-label required">Outsource type</label>
                    <select class="form-select form-select-solid" arial-label="Select Outsource type" name="outsourcing_type" id="outsourcing_type">
                      <option value="">Select Outsource type</option>
                      <option value="1">Fulltime </option>
                      <option value="2">Contract</option>
                    </select>
                    <span class="form-text text-muted">Please enter your Outsource type</span> </div>
                </div>
                <div class="form-group row fv-row mb-10 fv-plugins-icon-container has-danger">
                  <div class="col-lg-4">
                    <label class="form-label required">Primary Number:</label>
                    <input type="number" class="form-control form-control-solid" name="phone" id="phone" placeholder="Enter primary number" value="">
                    <span class="form-text text-muted">Please enter primary number</span> </div>
                  <div class="col-lg-4">
                    <label class="form-label">Secondary Number:</label>
                    <input type="number" class="form-control form-control-solid" name="secondary_mobile" id="secondary_mobile" placeholder="Enter secondary number" value="">
                    <span class="form-text text-muted">Please enter secondary number</span> </div>
                  <div class="col-lg-4">
                    <label class="form-label">Date of Birth:</label>
                    <input type="date" class="form-control form-control-solid"  name="dob" id="dob" placeholder="Enter dob" value="" onclick="this.showPicker()" >
                    <span class="form-text text-muted">Please enter your dob</span> </div>
                </div>
                <div class="form-group row fv-row mb-10">
                  <div class="col-lg-4">
                    <label class="form-label required">Primary Email:</label>
                    <input type="email" class="form-control form-control-solid" name="email" id="email" placeholder="Enter primary number" value="">
                    <span class="form-text text-muted">Please enter primary email</span> </div>
                  
				  <div class="col-lg-4">
                    <label class="form-label">Employee Number:</label>
                    <input type="email" class="form-control form-control-solid" name="employee_number" id="employee_number" placeholder="Enter employee number" value="">
                    <span class="form-text text-muted">Please enter employee number</span> 
				  </div>
                  <div class="col-lg-4">
                    <label class="form-label required">Qualification:</label>
                    <select class="form-select form-select-solid"  name="qualification" id="qualification" aria-label="Select qualification">
                      <option value="" >Select Qualification</option>
@foreach($qualification as $data_value)
	<option value="{{ @$data_value->id}}" >{{ @$data_value->name }}</option>	
@endforeach
                    </select>
                    <span class="form-text text-muted">Please select your qualification</span> </div>
                </div>
                <div class="form-group row fv-row mb-10">
                  <div class="col-lg-4">
                    <label class="form-label required">Total Experience:</label>
                    <input type="number" class="form-control form-control-solid" name="total_exp" id="total_exp" placeholder="Enter total experience" value="">
                    <span class="form-text text-muted">Please enter total experience</span> </div>
                  <div class="col-lg-4">
                    <label class="form-label required">Relevant Experience:</label>
                    <input type="number" class="form-control form-control-solid" name="rel_exp" id="rel_exp" placeholder="Enter relevant experience" value="">
                    <span class="form-text text-muted">Please enter relevant experience</span> </div>
                  <div class="col-lg-4">
                    <label class="form-label fs-7">
                    <div class="d-inline-flex">
                      <label class="radio-inline cursor-pointer">
                      <input  class="form-check-input h-15px w-15px serving_notice_period mt-0 me-1 cursor-pointer" type="radio" name="serving_notice_period" value="0" checked onclick="rbutton(this.value);" >
                      Notice period </label>
                    </div>
                    <div class="d-inline-flex">
                      <label class="radio-inline cursor-pointer">
                      <input class="form-check-input h-15px w-15px serving_notice_period mt-0 me-1 cursor-pointer" type="radio" name="serving_notice_period" value="1" onclick="rbutton(this.value);" >
                      Available from </label>
                    </div>
                    <div class="d-inline-flex">
                      <label class="radio-inline cursor-pointer">
                      <input class="form-check-input h-15px w-15px serving_notice_period mt-0 me-1 cursor-pointer" type="radio" name="serving_notice_period" value="2" onclick="rbutton(this.value);" >
                      Immediate </label>
                    </div>
                    </label>
                    <input type="text" name="np_id" id="np_id" class="form-control form-control-solid np_id" placeholder=""  value="Immediate" readonly="" style="display:none">
                    <input type="number" name="ddl_np_id" id="ddl_np_id" class="form-control form-control-solid"  placeholder="Enter Notice Period" value="" style="display:none" >
                    <input type="date" id="calender" name="calender" class="form-control form-control-solid" onclick="this.showPicker()"  >
                    <span class="form-text text-muted">Please enter noitce period</span> </div>
                </div>
                <div class="form-group row fv-row mb-10">
                  <div class="col-lg-4">
                    <label class="form-label required">Pay rate</label>
                    <div class="row">
                      <div class="col-4 pe-0">
                        <select class="form-select form-select-solid select2-currency-dropdown" arial-label="Select Currency" id="pay_rate_currency" name="pay_rate_currency" data-control="select2" data-placeholder="Search currency...">
@foreach($currency as $key=>$val)
    @php
        $cMeta = \App\Helper\GeneralMethod::currencyEnterpriseMeta($val);
        $short = !empty($cMeta['symbol']) ? $cMeta['code'].' ('.$cMeta['symbol'].')' : $cMeta['code'];
    @endphp
	<option value="{{ $key }}" data-code="{{ $val }}" data-short="{{ $short }}" data-search="{{ $cMeta['keywords'] }}">{{ $cMeta['label'] }}</option>	
@endforeach
                        </select>
                      </div>
                      <div class="col-4 px-2">
                        <input type="number" class="form-control form-control-solid" name="pay_rate_amount" id="pay_rate_amount" placeholder="Enter pay rate" value="">
                      </div>
                      <div class="col-4 pl-0">
                        <select class="form-select form-select-solid" arial-label="Select Annual Salary" name="pay_rate_mode" id="pay_rate_mode">
@foreach($salaryMode as $key=>$val)
	<option value="{{ $key }}">{{ $val }}</option>	
@endforeach
                        </select>
                      </div>
                    </div>
                    <span class="form-text text-muted">Please enter your pay rate</span> </div>
                  <div class="col-lg-4">
                    <label class="form-label required">Bill rate</label>
                    <div class="row">
                      <div class="col-4 pe-0">
                        <select class="form-select form-select-solid select2-currency-dropdown" arial-label="Select Currency" name="bill_rate_currency" id="bill_rate_currency" data-control="select2" data-placeholder="Search currency...">
@foreach($currency as $key=>$val)
    @php
        $cMeta = \App\Helper\GeneralMethod::currencyEnterpriseMeta($val);
        $short = !empty($cMeta['symbol']) ? $cMeta['code'].' ('.$cMeta['symbol'].')' : $cMeta['code'];
    @endphp
	<option value="{{ $key }}" data-code="{{ $val }}" data-short="{{ $short }}" data-search="{{ $cMeta['keywords'] }}">{{ $cMeta['label'] }}</option>	
@endforeach
                        </select>
                      </div>
                      <div class="col-4 px-2">
                        <input type="number" class="form-control form-control-solid" name="bill_rate_amount" id="bill_rate_amount" placeholder="Enter Bill rate" value="" >
                      </div>
                      <div class="col-4 pl-0">
                        <select class="form-select form-select-solid" arial-label="Select Annual Salary" name="bill_rate_mode" id="bill_rate_mode">
@foreach($salaryMode as $key=>$val)
	<option value="{{ $key }}">{{ $val }}</option>	
@endforeach
                        </select>
                      </div>
                      <span class="form-text text-muted">Please enter your bill rate</span> </div>
                  </div>
                  <div class="col-lg-4">
                    <label class="form-label required">Comission type</label>
                    <select class="form-select form-select-solid" arial-label="Select Comission type" name="comission_type" id="comission_type">
                      <option value="">Select Commision type</option>
                      <option value="1">Free</option>
                      <option value="2">Paid</option>
                    </select>
                    <span class="form-text text-muted">Please enter your comission type</span> </div>
                </div>
                <div class="form-group row fv-row mb-10">
                  <div class="col-lg-4">
                    <label class="form-label required">Country</label>
                    <select class="form-select form-select-solid" arial-label="Select Currency" name="country" id="country" >
                      <option value="">Select Country</option>
@foreach($countries as $countryVal)																
	<option value="{{ @$countryVal['id'] }}" >{{ @$countryVal['name'] }}</option>
@endforeach
                    </select>
                    <span class="form-text text-muted">Please enter your Country</span> </div>
                  <div class="col-lg-4">
                    <label class="form-label required">State</label>
                    <select class="form-select form-select-solid change_state" arial-label="Select Currency" name="state" id="state">
                      <option value="">Select State</option>
                    </select>
                    <span class="form-text text-muted">Please enter your State</span> </div>
                  <div class="col-lg-4">
                    <div class="row">
                      <div class="col-6 pe-0">
                        <label class="form-label">City</label>
                        <input type="text" class="form-control form-control-solid" name="city" id="city" placeholder="Enter city" value="" style="display:none;">
                        <select class="form-select form-control form-control-lg form-control-solid" name="city_id" id="city_id" >
                          <option value="">Select City</option>
                        </select>
                        <span class="form-text text-muted">Please enter your city</span> </div>
                      <div class="col-6">
                        <label class="form-label required">Pincode</label>
                        <input type="number" class="form-control form-control-solid" name="pincode" id="pincode" placeholder="Enter pincode" value="<?php echo @$application->pincode; ?>">
                        <span class="form-text text-muted">Please enter your pincode</span> </div>
                    </div>
                  </div>
                </div>
                <div class="form-group row fv-row mb-10">
                  <div class="col-lg-12">
                    <label class="form-label required">Skills</label>
                    <input class="form-control form-control-sm form-control-solid js-tagify value_JSON" value="" placeholder="Enter skills" id="skills" name="skills" />
                    <span class="form-text text-muted">Please enter your skills</span> </div>
                </div>
                <div class="form-group row fv-row">
                  <div class="col-lg-12">
                    <div class="dashed-border-top pt-10">
                      <h3 class="font-weight-bold text-dark">Enter Custom Details</h3>
                      <div class="form-group">
                        <div class="col-sm-12 px-0">
                          <div class="form-group mb-0" id="addmore"> </div>
                          <div class="row custom-detail-container">
                            <div class="form-group mb-0 mt-7 all_custom_details" id="remove">
                              <div class="fv-row row" >
                                <div class="col-lg-4">
                                  <div class="d-flex align-items-center justify-content-between px-2 mb-1">
                                    <div class="form form--disabled" id="form_lavel_input_{{@$counter}}_1">
                                      <input type="text" name="customDetailsQuestion[]" value="Title" class="enabled_input">
                                    </div>
                                    <span class="svg-icon svg-icon-primary svg-icon-2 cursor-pointer c-link form--disabled formEditMode" data-row-id="{{ @$counter }}" data-row-sno="1" >
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                      <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24"/>
                                        <path d="M8,17.9148182 L8,5.96685884 C8,5.56391781 8.16211443,5.17792052 8.44982609,4.89581508 L10.965708,2.42895648 C11.5426798,1.86322723 12.4640974,1.85620921 13.0496196,2.41308426 L15.5337377,4.77566479 C15.8314604,5.0588212 16,5.45170806 16,5.86258077 L16,17.9148182 C16,18.7432453 15.3284271,19.4148182 14.5,19.4148182 L9.5,19.4148182 C8.67157288,19.4148182 8,18.7432453 8,17.9148182 Z" fill="#000000" fill-rule="nonzero" transform="translate(12.000000, 10.707409) rotate(-135.000000) translate(-12.000000, -10.707409) "/>
                                        <rect fill="#000000" opacity="0.3" x="5" y="20" width="15" height="2" rx="1"/>
                                      </g>
                                    </svg>
                                    </span> </div>
                                  <input type="text" name="customDetailsAnswer[]"  class="form-control form-control-lg form-control-solid rounded me-5" placeholder="Enter Custom detail">
                                </div>
                                <div class="col-lg-4">
                                  <div class="d-flex align-items-center justify-content-between px-2 mb-2">
                                    <div class="form form--disabled" id="form_lavel_input_{{@$counter}}_2">
                                      <input type="text" name="customDetailsQuestion[]" value="Title" class="enabled_input">
                                    </div>
                                    <span class="svg-icon svg-icon-primary svg-icon-2 cursor-pointer form--disabled formEditMode" data-row-id="{{ @$counter }}" data-row-sno="2" >
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                      <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24"/>
                                        <path d="M8,17.9148182 L8,5.96685884 C8,5.56391781 8.16211443,5.17792052 8.44982609,4.89581508 L10.965708,2.42895648 C11.5426798,1.86322723 12.4640974,1.85620921 13.0496196,2.41308426 L15.5337377,4.77566479 C15.8314604,5.0588212 16,5.45170806 16,5.86258077 L16,17.9148182 C16,18.7432453 15.3284271,19.4148182 14.5,19.4148182 L9.5,19.4148182 C8.67157288,19.4148182 8,18.7432453 8,17.9148182 Z" fill="#000000" fill-rule="nonzero" transform="translate(12.000000, 10.707409) rotate(-135.000000) translate(-12.000000, -10.707409) "/>
                                        <rect fill="#000000" opacity="0.3" x="5" y="20" width="15" height="2" rx="1"/>
                                      </g>
                                    </svg>
                                    </span> </div>
                                  <input type="text" name="customDetailsAnswer[]"  class="form-control form-control-lg form-control-solid rounded me-5" placeholder="Enter Custom detail">
                                </div>
                                <div class="col-lg-4">
                                  <div class="d-flex align-items-center justify-content-between px-2 mb-2">
                                    <div class="form form--disabled" id="form_lavel_input_{{@$counter}}_3">
                                      <input type="text" name="customDetailsQuestion[]" value="Title" class="enabled_input">
                                    </div>
                                    <span class="svg-icon svg-icon-primary svg-icon-2 cursor-pointer form--disabled formEditMode" data-row-id="{{@$counter}}" data-row-sno="3" >
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                      <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24"/>
                                        <path d="M8,17.9148182 L8,5.96685884 C8,5.56391781 8.16211443,5.17792052 8.44982609,4.89581508 L10.965708,2.42895648 C11.5426798,1.86322723 12.4640974,1.85620921 13.0496196,2.41308426 L15.5337377,4.77566479 C15.8314604,5.0588212 16,5.45170806 16,5.86258077 L16,17.9148182 C16,18.7432453 15.3284271,19.4148182 14.5,19.4148182 L9.5,19.4148182 C8.67157288,19.4148182 8,18.7432453 8,17.9148182 Z" fill="#000000" fill-rule="nonzero" transform="translate(12.000000, 10.707409) rotate(-135.000000) translate(-12.000000, -10.707409) "/>
                                        <rect fill="#000000" opacity="0.3" x="5" y="20" width="15" height="2" rx="1"/>
                                      </g>
                                    </svg>
                                    </span> </div>
                                  <input type="text" name="customDetailsAnswer[]"  class="form-control form-control-lg form-control-solid rounded me-5" placeholder="Enter Custom detail">
                                </div>
                              </div>
                            </div>
                          </div>
                          <div id="customDetailLoader"></div>
                          <input type="hidden" name="customTitleId" id="customTitleId" value="{{ @$customTitleId }}"  />
                          <div class="d-flex justify-content-center py-5 my-5">
                            <button type="button" class="btn btn-light-success font-weight-bolder text-uppercase me-5 add_custom_detail" id="add_custom_detail">
                            <span class="svg-icon svg-icon svg-icon me-0">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                              <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <rect fill="#000000" x="4" y="11" width="16" height="2" rx="1"></rect>
                                <rect fill="#000000" opacity="0.3" transform="translate(12.000000, 12.000000) rotate(-270.000000) translate(-12.000000, -12.000000) " x="4" y="11" width="16" height="2" rx="1">
								</rect>
                              </g>
                            </svg>
                            </span> Add more </button>
                            <button type="button" class="btn btn-light-danger font-weight-bolder text-uppercase remove_cperson" id="remove_custom_detail">
                            <span class="svg-icon svg-icon svg-icon-2x">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                              <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <rect fill="#000000" x="4" y="11" width="16" height="2" rx="1"></rect>
                              </g>
                            </svg>
                            </span>
                            Remove </button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="form-row row fv-row">
                  <div class="col-12 text-center py-10">
                    <button type="button" name="save" id="save-form" value="save" class="btn btn-primary">Submit</button>
                    <a href="{{ route('product_company.bench-candidates.talent_pool') }}" class="btn btn-secondary">Cancel</a> </div>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection


@push('footer-script')
<script>
		var skills = document.querySelector("#skills");
		new Tagify(skills);		
</script>

<script src="https://code.jquery.com/jquery-3.6.0.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tagify/3.14.3/tagify.css">
<link rel="stylesheet" href="{{ asset('assets/plugins/datepicker/datepicker3.css') }}">
<script src="{{ asset('assets/plugins/datepicker/bootstrap-datepicker.js') }}"></script>
<script src="{{ asset('assets/node_modules/select2/dist/js/select2.full.min.js') }}"></script>
<script src="{{ asset('front/assets/js/location.js') }}"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>

<script>

document.addEventListener('DOMContentLoaded', function () {
    var today = new Date().toISOString().split('T')[0];
    document.getElementById('dob').setAttribute('max', today);
});

  $('#country').change(function(){  
   				var cid				=	$(this).val() ;
				if(cid == 101){
								$('#city_id').show();	
								$('#city').hide();	
				}else{
								$('#city').show();	
								$('#city_id').hide();	
				}
				var url 			=	"{{ route('product_company.job-applications.statelist',':cid') }}";
				url 				=	url.replace(':cid', cid);
				$.ajax({
								type	: 'GET',
								url		: url,
								success	: function (response) {																		
																	$('#state').html(response.data);	
															   }						   
				});   		
  });
  
  $(document).on('change', '.change_state', function() {
  		 
				var country				=	$('#country').val(); 
   				var state_id			=	this.value;							
				if(country == 101){
							$('#city_id').show();	
							$('#city').hide();	
				}else{
							$('#city').show();	
							$('#city_id').hide();	
				}				
				var url 			=	"{{ route('product_company.clients.citylist',':state_id') }}";
				url 				=	url.replace(':state_id', state_id);
				$.ajax({
								type	: 'GET',
								url		: url,
								success	: function (response) {		
																	$('#city_id').html(response.data);
															   }						   
				});	
  });  		
  
  function initBenchCurrencySelect2() {
      if (window.jQuery && $.fn.select2) {
          $('.select2-currency-dropdown').each(function() {
              var $el = $(this);
              if ($el.hasClass('select2-hidden-accessible')) {
                  $el.select2('destroy');
              }
              $el.select2({
                  width: '100%',
                  dropdownParent: $(document.body),
                  dropdownCssClass: 'br-currency-select2-dropdown',
                  placeholder: 'Search currency...',
                  minimumResultsForSearch: 0,
                  templateSelection: function(data) {
                      if (!data.id) return data.text;
                      var $opt = $(data.element);
                      var short = $opt.data('short') || $opt.data('code');
                      return short ? short : data.text;
                  },
                  matcher: function (params, data) {
                      if ($.trim(params.term) === '') {
                          return data;
                      }
                      if (typeof data.text === 'undefined') {
                          return null;
                      }
                      var term = params.term.toLowerCase();
                      var text = data.text.toLowerCase();
                      var $opt = $(data.element);
                      var code = ($opt.data('code') || '').toString().toLowerCase();
                      var searchKw = ($opt.data('search') || '').toString().toLowerCase();

                      if (text.indexOf(term) > -1 || code.indexOf(term) > -1 || searchKw.indexOf(term) > -1) {
                          return data;
                      }
                      return null;
                  }
              });
          });
      }
  }

  $('body').on('change', '#pay_rate_currency', function(){
      var val = $(this).val();
      if ($('#bill_rate_currency').val() !== val) {
          $('#bill_rate_currency').val(val).trigger('change.select2');
      }
  });
  $('body').on('change', '#bill_rate_currency', function(){
      var val = $(this).val();
      if ($('#pay_rate_currency').val() !== val) {
          $('#pay_rate_currency').val(val).trigger('change.select2');
      }
  });
   $('body').on('change', '#pay_rate_mode', function(){
   			var	pay_rate_mode	=	$(this).val();
			document.getElementById("bill_rate_mode").selectedIndex = pay_rate_mode;
   });
   $('body').on('change', '#bill_rate_mode', function(){
   			var	bill_rate_mode	=	$(this).val();
			document.getElementById("pay_rate_mode").selectedIndex = bill_rate_mode;
   });
   
   $( document ).ready(function() {
       initBenchCurrencySelect2();
									   $(".serving_notice_period").click(function(){
											   $('#calender').val('');
											   var selected 	=	$(".serving_notice_period:checked");
											   var selVal		=	selected.val();
											   if(selVal == 0){
													$('#np_id').hide();
													$('#ddl_np_id').hide();	
													$('#calender').show();
											   }else if(selVal == 1){
													$('#np_id').hide();
													$('#ddl_np_id').hide();	
													$('#calender').show();
											   }else if(selVal == 2){
													$('#np_id').show();	
													$('#ddl_np_id').hide();
													$('#calender').hide();		
											   } 
									   });
   });
   
  	$(document).on('click', '.formEditMode', function(){
	
				var id    			=	$(this).data('row-id');
				var sno    			=	$(this).data('row-sno');
				var foamStatus		=	$(this).hasClass('form--disabled'); 
				if(foamStatus == true){
					$(this).html('<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><polygon points="0 0 24 0 24 24 0 24"/><path d="M9.26193932,16.6476484 C8.90425297,17.0684559 8.27315905,17.1196257 7.85235158,16.7619393 C7.43154411,16.404253 7.38037434,15.773159 7.73806068,15.3523516 L16.2380607,5.35235158 C16.6013618,4.92493855 17.2451015,4.87991302 17.6643638,5.25259068 L22.1643638,9.25259068 C22.5771466,9.6195087 22.6143273,10.2515811 22.2474093,10.6643638 C21.8804913,11.0771466 21.2484189,11.1143273 20.8356362,10.7474093 L17.0997854,7.42665306 L9.26193932,16.6476484 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" transform="translate(14.999995, 11.000002) rotate(-180.000000) translate(-14.999995, -11.000002) "/><path d="M4.26193932,17.6476484 C3.90425297,18.0684559 3.27315905,18.1196257 2.85235158,17.7619393 C2.43154411,17.404253 2.38037434,16.773159 2.73806068,16.3523516 L11.2380607,6.35235158 C11.6013618,5.92493855 12.2451015,5.87991302 12.6643638,6.25259068 L17.1643638,10.2525907 C17.5771466,10.6195087 17.6143273,11.2515811 17.2474093,11.6643638 C16.8804913,12.0771466 16.2484189,12.1143273 15.8356362,11.7474093 L12.0997854,8.42665306 L4.26193932,17.6476484 Z" fill="#000000" fill-rule="nonzero" transform="translate(9.999995, 12.000002) rotate(-180.000000) translate(-9.999995, -12.000002) "/></g></svg>');
					$(this).removeClass('form--disabled').addClass('form--enabled');	
					$('#form_lavel_input_'+id+'_'+sno).removeClass('form--disabled').addClass('form--enabled');
				}else{
					$(this).html('<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><rect x="0" y="0" width="24" height="24"></rect><path d="M8,17.9148182 L8,5.96685884 C8,5.56391781 8.16211443,5.17792052 8.44982609,4.89581508 L10.965708,2.42895648 C11.5426798,1.86322723 12.4640974,1.85620921 13.0496196,2.41308426 L15.5337377,4.77566479 C15.8314604,5.0588212 16,5.45170806 16,5.86258077 L16,17.9148182 C16,18.7432453 15.3284271,19.4148182 14.5,19.4148182 L9.5,19.4148182 C8.67157288,19.4148182 8,18.7432453 8,17.9148182 Z" fill="#000000" fill-rule="nonzero" transform="translate(12.000000, 10.707409) rotate(-135.000000) translate(-12.000000, -10.707409) "></path><rect fill="#000000" opacity="0.3" x="5" y="20" width="15" height="2" rx="1"></rect></g></svg>'); 
					$(this).removeClass('form--enabled').addClass('form--disabled');
					$('#form_lavel_input_'+id+'_'+sno).removeClass('form--enabled').addClass('form--disabled');
				}			
	});
   
</script>
<script>
	$('#add_custom_detail').click(function(){
					
						var customTitleId			=	$('#customTitleId').val();
						customTitleId++;
						$('#customTitleId').val(customTitleId);
						$('#customDetailLoader').show();
						$('#customDetailLoader').html('');
						var baseurl    			=	"{{ asset('assets/images/Loading2.gif') }}"; 	
						var counter				=	customTitleId ;
						var url 				=	"{{ route('product_company.bench-candidates.add_custom_detail',':counter') }}";
						url 					=	url.replace(':counter', counter);
						$.ajax({
									type		: 'GET',
									url			: url,
									beforeSend	: function() {
																			$('#customDetailLoader').html('<img src="'+baseurl+'" height="35px" width="100%">');
															},
									success		: function (response) {																		
																			$('#customDetailLoader').hide();
																			$('.all_custom_details').last().append(response.data);
																	  }		
						});				
	});
	$('#remove_custom_detail').click(function(){
   			$('.all_custom_details').children().last().remove();
   	});
</script>
<script>   

"use strict";
var KTAddCandidate = function () {
    var form;
    var submitButton;
    var validator;
    var handleForm = function (e) {
        validator = FormValidation.formValidation(
            form,
            {
                fields: {					
                    		'full_name'				: 	{	validators: {	notEmpty:	{message: "Full name is required" } } },					
                    		'current_designation'	:	{validators:	{	notEmpty:	{	message: "Current designation is required" } }	},
                    		'outsourcing_type'		:	{	validators:{ notEmpty:{message: "Outsource type is required"} }	},					
                    		'phone'					: 	{validators:{notEmpty:{ message: "Primary number is required"},	digits:{message: "Primary number must contains only digits"},stringLength:{ min: 10,	max: 10,	message: "Primary number must contains 10 digits only"}}},
					 		'email'					: 	{ validators: {notEmpty: {message: 'Primary email is required'}, emailAddress:{message: 'The value is not a valid email address' }} },
                    		'qualification'			:	{validators:{ notEmpty: { message: 'Qualification is required'} } },
                    		'total_exp'				:  	{ validators: { notEmpty: { message:"Total Experience is required"}, digits: { message: "Total Experience must contain only digits" } } },
                    		'rel_exp'				: 	{validators:{notEmpty: { message:"Relevant Experience is required"},digits: { message: "Relevant Experience must contain only digits"}} },

                    		'pay_rate' 				: 	{ validators: { notEmpty: { message: "Pay rate is required"}, digits: { message: "Pay rate must containts only digits" }} },
                    		'bill_rate' 			:	{validators:{notEmpty:{ message: "Bill rate is required"},	digits:{message: "Bill rate must contains only digits"}	} },
                    		'comission_type' 		: 	{validators: { notEmpty: { message:"Comission type is required" } } },
                    		'country'				: 	{validators: { notEmpty: { message: "Country is required" } }},
                    		'state'					: 	{ validators:{ notEmpty: { message: "State is required" } } },
					
                   	 		'pincode'				: 	{ validators: {notEmpty:{message: "Pincode is required" }, digits:{message: "Pincode must contain digits"},stringLength: {min: 6,max: 6, message: "Pincode contain 6 digit only"}} },                    
                    		'skills'				: 	{ validators:{ notEmpty: {message: 'Skills is required'}}},
                    		'specialties'			: 	{validators:{ notEmpty:{ message: "Specialties is required"} }}
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap: new FormValidation.plugins.Bootstrap5({
                        rowSelector: '.fv-row'
                    })
                }
            }
        );

        submitButton.addEventListener('click', function (e) {
            e.preventDefault();
            validator.validate().then(function (status) {
                if (status == 'Valid') {
                  $("#save-form").prop("disabled", true);
                  $("#save-form").html(
                    `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Waiting...`
                  );
                  
                    submitButton.setAttribute('data-kt-indicator', 'on');
                    submitButton.disabled = true;
                    setTimeout(function () {
                        submitButton.removeAttribute('data-kt-indicator');
                        submitButton.disabled = true;
													var formData 	=	new FormData($('#createForm')[0]);							
													$.ajax({
															   url 				: '{{route('product_company.bench-candidates.store')}}'+'?_token=' + '{{ csrf_token() }}',								   
															   type 			: 'POST',
															   data 			: formData,
															   processData		: false,  	
															   contentType		: false,  	
															   redirect			: true,
															   success			: function (response) {
																
																					 Swal.fire({
																										text				: "You have successfully Submitted",
																										icon				: "success",
																										buttonsStyling		: false,
																										confirmButtonText	: "Ok, got it!",
																										customClass			: {
																																	confirmButton: "btn btn-primary"
																															   }
																									}).then(function (result) {
																										if (result.isConfirmed) {
																											form.querySelector('[name="email1"]').value = "";
																											form.querySelector('[name="password1"]').value = "";
																										}
																									});
																					window.location = response.url;
																				},
																error			: function (response) {
																				}
														});
                    }, 500);
                } else {
							Swal.fire({
											text				: "Sorry, looks like there are some errors detected, please try again.",
											icon				: "error",
											buttonsStyling		: false,
											confirmButtonText	: "Ok, got it!",
											customClass			: {
																		confirmButton: "btn btn-primary"
																  }
									});
                }
            });
        });
    }

    return {
				init: function () {
										form 			=	document.querySelector('#createForm');
										submitButton 	=	document.querySelector('#save-form');
										handleForm();
									}
			};
}();


KTUtil.onDOMContentLoaded(function () {
    KTAddCandidate.init();
});
  
</script>



@endpush
