@extends('layouts.service_company_layout')

@push('head-script')
<link rel="stylesheet" href="{{ asset('assets/node_modules/switchery/dist/switchery.min.css') }}<?php echo TM_VERSION; ?>">
<style>
body
{
  overflow-x:hidden;
}
.w-md-130px {
    width: 130px !important;
}
.active_plan
{
  width: fit-content;
}

.btn-group.hg-plan-btn-group
{
    width: 100%;
}
.shared-hosting .container_hosting-package .btn-group button.btn {
    padding: 10px 15px;
}
.shared-hosting .container_hosting-package .btn-group button.btn {
    padding: 14px 15px;
    text-align: left;
    width: 100%;
}
.btn.btn-info.hg-plan-btn-info {
    box-shadow: none;
    border-color: #ddd;
    width: 100%;
    float: none;
}
.btn-group .selected-term {
    display: inline;
    color: #333;
}
.btn-group .selected-term .term-length {
    color: #666;
    font-size: 14px;
    text-transform: capitalize;
}
.btn-group .selected-term .WebRupee.webrupee1, .btn-group .selected-term span.WebRupee {
    color: #0171bc;
}
.hg-plan-btn-group span.WebRupee {
    font-size: 22px;
}
.btn-group.hg-plan-btn-group .selected-term span.term-price, .btn-group.hg-plan-btn-group .selected-term span.term-description {
    color: #0171bc;
}
.hg-plan-btn-group span.term-price {
    font-size: 22px;
    font-weight: 800;
}
.btn-group .selected-term .term-price {
    font-weight: 700;
}
.btn-group.hg-plan-btn-group .selected-term span.term-price, .btn-group.hg-plan-btn-group .selected-term span.term-description {
    color: #0171bc;
}
.btn-group .selected-term .term-price {
    font-weight: 700;
}
.btn-group.hg-plan-btn-group .selected-term span.term-price, .btn-group.hg-plan-btn-group .selected-term span.term-description {
    color: #0171bc;
}
.selected-term .testsdfd {
    margin-left: 3%;
    margin-right: 12%;
    line-height: 17px;
    float: none;
}
.hg-percentage-discount {
      color: #f15922;
    font-size: 14px;
    font-weight: 500;
}
span.hg-actual-price, .plan-featured .hg-plan-btn-group .hg-actual-price del span.WebRupee, .hg-plan-btn-group .selected-term .testsdfd .hg-actual-price del span.WebRupee, .plan-featured .hg-plan-btn-group .selected-term .testsdfd .hg-actual-price del span.WebRupee {
    color: #767676;
}
span.hg-actual-price {
    display: block;
    font-size: 14px;
    font-weight: 500;
}
span.hg-actual-price, .plan-featured .hg-plan-btn-group .hg-actual-price del span.WebRupee, .hg-plan-btn-group .selected-term .testsdfd .hg-actual-price del span.WebRupee, .plan-featured .hg-plan-btn-group .selected-term .testsdfd .hg-actual-price del span.WebRupee {
    color: #767676;
}
.hg-plan-btn-group .hg-actual-price span.WebRupee {
    font-size: 14px;
}
.WebRupee {
    font-family: 'WebRupee' !important;
}
span.hg-actual-price {
    display: block;
    font-size: 14px;
    font-weight: 500;
}
span.hg-actual-price, .plan-featured .hg-plan-btn-group .hg-actual-price del span.WebRupee, .hg-plan-btn-group .selected-term .testsdfd .hg-actual-price del span.WebRupee, .plan-featured .hg-plan-btn-group .selected-term .testsdfd .hg-actual-price del span.WebRupee {
    color: #767676;
}

.package-term
{
  font-size:16px;
}
.dropdown-menu li
{
  padding: .8em 1em;
  cursor: pointer;
  border-bottom: 1px dotted lightgray;
  display: block;
}
.dropdown-menu li:last-child
{
  border-bottom: none;
}
</style>
@endpush

@section('content')
<!--begin::Toolbar-->
<div class="toolbar" id="kt_toolbar">
  <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack px-5">
    <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
      <div class="d-flex flex-column">
        <h5 class="d-flex align-items-center text-dark fw-bolder my-1"> <span class="text-gray-800 mb-1">Authenticate Custom Package</span> </h5>
        <span class="text-gray-600" id="">&nbsp;</span> </div>
      <span class="h-20px border-gray-200 border-start mx-4 d-none"></span> </div>
    <div class="d-flex align-items-center py-1 d-none ">
      
<!--begin::Add to cart-->
<a href="{{ route('service_company.subscribe.cart') }}" type="button" class="btn btn-sm btn-primary position-relative fs-6 me-3 mt-1"> <span class="svg-icon svg-icon-primary svg-icon-2">
	<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
		<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
		<rect x="0" y="0" width="24" height="24"/>
		<path d="M12,4.56204994 L7.76822128,9.6401844 C7.4146572,10.0644613 6.7840925,10.1217854 6.3598156,9.76822128 C5.9355387,9.4146572 5.87821464,8.7840925 6.23177872,8.3598156 L11.2317787,2.3598156 C11.6315738,1.88006147 12.3684262,1.88006147 12.7682213,2.3598156 L17.7682213,8.3598156 C18.1217854,8.7840925 18.0644613,9.4146572 17.6401844,9.76822128 C17.2159075,10.1217854 16.5853428,10.0644613 16.2317787,9.6401844 L12,4.56204994 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
		<path d="M3.28077641,9 L20.7192236,9 C21.2715083,9 21.7192236,9.44771525 21.7192236,10 C21.7192236,10.0817618 21.7091962,10.163215 21.6893661,10.2425356 L19.5680983,18.7276069 C19.234223,20.0631079 18.0342737,21 16.6576708,21 L7.34232922,21 C5.96572629,21 4.76577697,20.0631079 4.43190172,18.7276069 L2.31063391,10.2425356 C2.17668518,9.70674072 2.50244587,9.16380623 3.03824078,9.0298575 C3.11756139,9.01002735 3.1990146,9 3.28077641,9 Z M12,12 C11.4477153,12 11,12.4477153 11,13 L11,17 C11,17.5522847 11.4477153,18 12,18 C12.5522847,18 13,17.5522847 13,17 L13,13 C13,12.4477153 12.5522847,12 12,12 Z M6.96472382,12.1362967 C6.43125772,12.2792385 6.11467523,12.8275755 6.25761704,13.3610416 L7.29289322,17.2247449 C7.43583503,17.758211 7.98417199,18.0747935 8.51763809,17.9318517 C9.05110419,17.7889098 9.36768668,17.2405729 9.22474487,16.7071068 L8.18946869,12.8434035 C8.04652688,12.3099374 7.49818992,11.9933549 6.96472382,12.1362967 Z M17.0352762,12.1362967 C16.5018101,11.9933549 15.9534731,12.3099374 15.8105313,12.8434035 L14.7752551,16.7071068 C14.6323133,17.2405729 14.9488958,17.7889098 15.4823619,17.9318517 C16.015828,18.0747935 16.564165,17.758211 16.7071068,17.2247449 L17.742383,13.3610416 C17.8853248,12.8275755 17.5687423,12.2792385 17.0352762,12.1362967 Z" fill="#000000"/>
		</g>
	</svg>
	</span>Cart <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"> 20+ <span class="visually-hidden">Add to cart list</span> </span> 
</a>
<!--begin::Add to cart-->

    </div>
  </div>
</div>
<!--end::Toolbar-->
<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid pt-2 pb-0" id="kt_content">
  <div class="post d-flex flex-column-fluid" id="kt_post">
    <div id="kt_content_container" class="container-fluid px-3">
      <!--begin::card-->
      <div class="card bg-light">
        <!--begin::card body-->
        <div class="card-body pt-3 px-0 pb-4">
          <div class="card shadow-none">
            <!--begin::Header-->
            <div class="card-header px-7">
              <div class="card-title m-0">
                <h3 class="fw-bolder m-0">Authenticate Package</h3>
              </div>
              <a href="javascript:void(0);" class="btn btn-primary align-self-center d-none"> <span class="svg-icon svg-icon-2">
              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                  <rect x="0" y="0" width="24" height="24"/>
                  <path d="M5.5,4 L18.5,4 C19.3284271,4 20,4.67157288 20,5.5 L20,9.5 C20,10.3284271 19.3284271,11 18.5,11 L5.5,11 C4.67157288,11 4,10.3284271 4,9.5 L4,5.5 C4,4.67157288 4.67157288,4 5.5,4 Z M11,6 C10.4477153,6 10,6.44771525 10,7 C10,7.55228475 10.4477153,8 11,8 L13,8 C13.5522847,8 14,7.55228475 14,7 C14,6.44771525 13.5522847,6 13,6 L11,6 Z" fill="#000000" opacity="0.3"/>
                  <path d="M5.5,13 L18.5,13 C19.3284271,13 20,13.6715729 20,14.5 L20,18.5 C20,19.3284271 19.3284271,20 18.5,20 L5.5,20 C4.67157288,20 4,19.3284271 4,18.5 L4,14.5 C4,13.6715729 4.67157288,13 5.5,13 Z M11,15 C10.4477153,15 10,15.4477153 10,16 C10,16.5522847 10.4477153,17 11,17 L13,17 C13.5522847,17 14,16.5522847 14,16 C14,15.4477153 13.5522847,15 13,15 L11,15 Z" fill="#000000"/>
                </g>
              </svg>
              </span> See Pricing </a> </div>
            <!--end::Header-->
            <!--begin::Table-->
            <div class="card-body">
              <!--begin::Alert-->
              <div class="alert alert-dismissible bg-light-warning d-flex flex-center flex-column py-10 px-10 px-lg-20 mb-10">
                <!--begin::Icon-->
                <span class="svg-icon svg-icon-5tx svg-icon-warning mb-5">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="10" fill="currentColor"></rect>
                  <rect x="11" y="14" width="7" height="2" rx="1" transform="rotate(-90 11 14)" fill="currentColor"></rect>
                  <rect x="11" y="17" width="2" height="2" rx="1" transform="rotate(-90 11 17)" fill="currentColor"></rect>
                </svg>
                </span>
                <!--end::Icon-->
                <!--begin::Wrapper-->
                <div class="text-center">
                  <!--begin::Title-->
                  <h1 class="fw-bold mb-5 text-warning">Information</h1>
                  <!--end::Title-->
                  <!--begin::Separator-->
                  <div class="separator separator-dashed border-warning opacity-25 mb-5"></div>
                  <!--end::Separator-->
                  <!--begin::Content-->
                  <div class="mb-9 text-warning"> <?php echo @$message; ?> </div>
                  <!--end::Content-->
                  <!--begin::Buttons-->
                  <!--<div class="d-flex flex-center flex-wrap">
            <a href="#" class="btn btn-outline btn-outline-danger btn-active-danger m-2">Cancel</a>
            <a href="#" class="btn btn-danger m-2">Ok, I got it</a>
        </div>-->
                  <!--end::Buttons-->
                </div>
                <!--end::Wrapper-->
              </div>
              <!--end::Alert-->
            </div>
            <!--end::Table-->
          </div>
        </div>
        <!--end::card body-->
      </div>
      <!--end::card-->
    </div>
  </div>
</div>
<!--end::Content-->
@endsection

@push('footer-script')
<script src="https://js.stripe.com/v3/"></script>
<script src="{{ asset('assets/node_modules/switchery/dist/switchery.min.js') }}<?php echo TM_VERSION; ?>"></script>
@endpush 