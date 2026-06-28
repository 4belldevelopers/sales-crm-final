 @extends('layouts.superadmin_layout')
@push('head-script')
@endpush

@section('content')
<?php 
	
	 use App\Helper\GeneralMethod;
   $country_code =  GeneralMethod::countryCodeFun();
   $phone_code = $team->calling_code ? $team->calling_code : $country_code;

	 $background_image		=	url('public/user-uploads/team_profile_img/avatar.png'); 				//'https://preview.keenthemes.com/metronic8/demo1/assets/media/avatars/blank.png';
	 if(!empty(@$team->image) && strlen(@$team->image)>10){
   			//$background_image		=  url('public/user-uploads/team_profile_img/'.@$team->image);  	
   			$background_image		=  @$team->image;  
   	}

?>
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
          <form id="kt_account_profile_details_form" name="kt_account_profile_details_form" method="post" class="form fv-plugins-bootstrap5 fv-plugins-framework" novalidate="novalidate" enctype="multipart/form-data">
            <meta name="csrf-token" content="{{ csrf_token() }}" />
            <input type="hidden" name="teamUserId" id="teamUserId" value="{{$team->id}}" />
            @csrf  
            @method('PUT')
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
                    <div class="image-input-wrapper w-125px h-125px" style="background-image: url('{{ $background_image}}')"></div>
                    <!--end::Preview existing avatar-->
                    <!--begin::Label-->
                    <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" title="" data-bs-original-title="Change avatar"> <i class="bi bi-pencil-fill fs-7"></i>
                    <!--begin::Inputs-->
                    <input type="file" name="avatar" id="avatar" accept=".png, .jpg, .jpeg">
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
                  <input type="text" name="name" id="name" class="form-control form-control-lg form-control-solid" placeholder="Enter Full name" value="{{$team->name}}">
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
                  <input type="text" name="designation" id="designation" class="form-control form-control-lg form-control-solid" placeholder="Enter Designation" value="{{$team->designation}}">
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
                  <input type="email" name="email" id="email" class="form-control form-control-lg form-control-solid" placeholder="Enter Email" value="{{$team->email}}" >
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
                  <input type="number" name="mobile" id="mobile" maxlength="10" class="form-control form-control-lg form-control-solid" placeholder="Enter Phone" value="{{$team->mobile}}">
                  <input type="hidden" value="{{ @$phone_code }}" id="phone_country_code" name="phone_country_code">
                  <div class="fv-plugins-message-container invalid-feedback"></div>
                </div>
                <!--end::Col-->
              </div>
              <!--end::Phone-->
              <!--begin::Country-->
              <div class="row mb-6">
                <!--begin::Label-->
                {{-- <label class="col-lg-3 col-form-label fw-bold fs-6"> <span class="required">Country</span> </label> --}}
                <!--end::Label-->
                <!--begin::Col-->
                {{-- <div class="col-lg-9 fv-row">
                  <select name="country" id="country" aria-label="Select a Country" data-control="select2" data-placeholder="Select a country..." class="form-select form-select-solid form-select-lg fw-bold country">
                    <option value="" >Select a Country...</option> --}}
                    <?php 
                    foreach($countryArr as $cval){
                    $sel	=	'';
                    if( $team->country == $cval['id']){
                    $sel	=	'selected="selected"';	
                    $countryName = @$cval['name'];
                    }
                    // echo	'<option value="'.@$cval['id'].'"  '.$sel.'>'.@$cval['name'].'</option>'; 
                    }
                    ?>
                  {{-- </select> --}}
                {{-- </div> --}}
                <!--end::Col-->
              {{-- </div> --}}
              <!--end::Country-->
              <!--begin::State-->
              {{-- <div class="row mb-6"> --}}
                <!--begin::Label-->
                {{-- <label class="col-lg-3 col-form-label fw-bold fs-6"> <span class="required">State</span> </label> --}}
                <!--end::Label-->
                <!--begin::Col-->
                {{-- <div class="col-lg-9 fv-row"> --}}
                  {{-- <select name="state" id="state" aria-label="Select a State" data-control="select2" data-placeholder="Select a state..." class="form-select form-select-solid form-select-lg fw-bold change_state">
                    <option value="" >Select a State...</option> --}}
                    <?php 
                    foreach($statelist as $sval){
                    $sel	=	'';
                    if($team->state == $sval['id']){
                    $sel	=	'selected="selected"';	
                    $stateName = $sval['name'];
                    }
                    // echo '<option  value="'.$sval['id'].'" '.$sel.'>'.$sval['name'].'</option>';	
                    }
                    ?>
                  {{-- </select> --}}
                {{-- </div> --}}
                <!--end::Col-->
              {{-- </div> --}}
              <!--end::State-->
              <!--begin::City-->
              <div class="row mb-6">
                <!--begin::Label-->
                <label class="col-lg-3 col-form-label fw-bold fs-6"> <span class="">City</span> </label>
                <!--end::Label-->
                <!--begin::Col-->
                <div class="col-lg-9 fv-row">

                 

                  <?php 

                    // $country			=	'';
                    // $country			=	$team->country;

                    // $display_city		=	'';
                    // $display_city_id	= 'style="display:none"';		
                    // if($country == 101){
                    //   $display_city_id	=	'';
                    //   $display_city		=	'style="display:none"';	
                        
                    // }
                    // else{
                    //   $display_city_id	= 'style="display:none"';			
                    // }
                                      
                    ?>
                  {{-- <input type="text" name="city" id="city" class="form-control form-control-lg form-control-solid" placeholder="Enter city" value="{{$team->city}}"  <?php //echo @$display_city; ?> >
                  <select class="form-select form-control form-control-lg form-control-solid" name="city_id" id="city_id" <?php //echo @$display_city_id; ?> >
                    <option value="">Select City</option> --}}
                    <?php
 
                      $sel			=	'';
                      //$state		=	'';
                      //$state		=	@$application->state;
                      if(!empty($cityListArr)){
                        foreach($cityListArr as $key=>$sVal){
                          $sel	=	'';
                          if($team->city_id == @$sVal['id'] ){
                            $sel	=	'selected="selected"';	
                            $cityName = @$sVal['name'];
                          }
                          //echo '<option value="'.@$sVal['id'].'"  '.@$sel.' >'.@$sVal['name'].'</option>';	
                        }
                      }
                     
                    ?>
                  {{-- </select> --}}
                 <?php
                 $return = '';
                 if(!empty(@$cityName) &&  !empty(@$stateName) && !empty(@$countryName) ){
                  $return =  @$cityName . ' | ' . @$stateName . ' | ' . @$countryName;
                 }
                 

                  // dd($return);
                  ?>
                  <input class="form-control form-control-lg form-control-solid" id="kt_tagify_work_location" name="kt_tagify_work_location"  value="" placeholder="Enter your Location..."/>
                  
                </div>
                <!--end::Col-->
              </div>
              <!--end::City-->
            </div>
            <!--end::Card body-->
            <!--begin::Actions-->
            <div class="card-footer d-flex justify-content-center py-6 px-9">
              <button type="button" class="btn btn-light btn-active-light-primary me-2 cancel" id="cancel">Cancel</button>
              <button type="submit" class="btn btn-primary" id="updateProfile" name="updateProfile">Save Changes</button>
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
<?php 

	//echo '<pre>'; print_r($team->name); 
	
	//echo '<pre>'; print_r($statelist); 
		
?>
@endsection

@push('footer-script')
<script src="https://unpkg.com/@yaireo/tagify"></script>
<script src="https://unpkg.com/@yaireo/tagify/dist/tagify.polyfills.min.js"></script>
<link href="https://unpkg.com/@yaireo/tagify/dist/tagify.css" rel="stylesheet" type="text/css" />
<!--begin::Script-->
<script>

  $(document).ready(function () {
    // Initialize intl-tel-input for Primary and Secondary phone fields
    const primaryPhoneInput = window.intlTelInput(document.querySelector("#mobile"), {
      separateDialCode: true,
      initialCountry: "{{ $phone_code }}",
      preferredCountries: ["in", "us", "gb", "ca", "au"],
    });


      // Function to update hidden country code inputs
    function updateCountryCode(inputField, hiddenField) {
      const selectedCountryData = inputField.getSelectedCountryData();
      //$(hiddenField).val(selectedCountryData.dialCode); // Set the country code in the hidden field
      const countryCode = selectedCountryData.iso2; // e.g., 'us', 'in', etc.
      $(hiddenField).val(countryCode);

    }
    // Update country code on input or country flag selection
    $("#mobile").on("input change", function () {
      updateCountryCode(primaryPhoneInput, "#phone_country_code");
    });

   
    // Update country code dynamically when the user selects a different country flag
    $(".iti__flag-container").on("click", function (event) {
      const targetInput = $(event.target).closest(".iti").find("input");

      if (targetInput.is("#mobile")) {
        updateCountryCode(primaryPhoneInput, "#phone_country_code");
      } 
      
    });

    setTimeout(function () {	
      $(".iti__flag-container").click();
    },500);

  });


</script>

<script>
    /******/ (() => { // webpackBootstrap
/******/ 	"use strict";
var __webpack_exports__ = {};
/*!*********************************************************************************************!*\
  !*** FORM VALIDATION***!
  \*********************************************************************************************/


  setTimeout(() => {
  

  const cities = @json($cities);
  
  let inputId = `#kt_tagify_work_location`;
  let inputElement = document.querySelector(inputId);
  
  if (inputElement) {
      let tagifyInstance = new Tagify(inputElement, {
          whitelist: cities,
          enforceWhitelist: true, // Prevents users from adding custom values
          maxTags: 1,
          dropdown: {
              maxItems: 20,           // maximum allowed rendered suggestions
              classname: "", // custom classname for this dropdown
              enabled: 0,             // show suggestions on focus
              closeOnSelect: true    // do not hide the suggestions dropdown once an item has been selected
          }
      });
  
      // Prevent creating tags on blur or enter key
      tagifyInstance.on('input', function(e) {
          tagifyInstance.dropdown.show(); // show the dropdown immediately
      });
  
      // Create tags only on select
      tagifyInstance.on('dropdown:select', function(e) {
          tagifyInstance.addTags([e.detail.data.value]);
      });
  
      tagifyInstance.on('add', function(e) {
        // Remove the tag if it wasn't selected from the dropdown
        // if (!cities.includes(e.detail.data.value)) {
        //   tagifyInstance.removeTag(e.detail.data.value);
        // }
      });


      function debounce(func, delay) {
        let timer;
        return function (...args) {
            clearTimeout(timer);
            timer = setTimeout(() => func.apply(this, args), delay);
        };
    }

         // Debounced function to fetch suggestions
    const fetchSuggestions = debounce(function(query) {
        $.ajax({
            url: "{{ url('/') }}" + "/city-job-suggestions?q=" + query,
            method: 'GET',
            success: function(data) {
                console.warn("Fetched Cities: ", data);

                if (Array.isArray(data)) {
                    tagifyInstance.settings.whitelist = data; // Update whitelist
                    // tagifyInstance.dropdown.show(query); // Show suggestions
                    tagifyInstance.dropdown.show.call(tagifyInstance);
                } else {
                    console.error("Invalid API response format: Expected array.");
                }
            },
            error: function(err) {
                console.error("Error fetching cities:", err);
            }
        });
    }, 200); // 300ms delay

    // Listen for user input to trigger API call
    tagifyInstance.on('input', function(e) {
        var query = e.detail.value.trim();
        if (query.length < 2) return;
        fetchSuggestions(query);
    });

    setTimeout(() => {
      var city = "{{@$return}}";
      if(city !=''){
        var cityName = city.split(" | ")[0]; // Get the first part (Ballia)
        fetchSuggestions(cityName);
      }
    }, 100);
    setTimeout(() => {
        var defaultLocation = {!! json_encode(@$return) !!}; // Ensure proper Blade syntax for JavaScript
        if (defaultLocation) {
          tagifyInstance.addTags([defaultLocation]); // Add the default location as a tag
          tagifyInstance.dropdown.hide();
        }
    }, 2000); // 3 seconds delay

  
  }
  
  }, 1500);

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
                    mobile: {
                        validators: {
                            notEmpty: {
                                message: 'Phone is required'
                            },
                            stringLength: {
                                min: 10,
                                max: 10,
                                message: 'Phone number must be 10 digits long'
                            },
                            regexp: {
                                regexp: /^[0-9]+$/,
                                message: 'Phone number can only contain digits'
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
<script>

$( document ).ready(function() {

    	$(document).on('change', '.country', function() { 
   	
   					cid						=	this.value;	   				
   					var url 			=	"{{ route('superadmin.company.statelist',':cid') }}";
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
           var url 			=	"{{ route('superadmin.company.citylist',':state_id') }}";
           url 				=	url.replace(':state_id', state_id);
           $.ajax({
                   type	: 'GET',
                   url		: url,
                   success	: function (response) {		
                                     $('#city_id').html(response.data);
                                    }							   
           });								
          });

	
});
	
</script>
<script> 
   
 $(document).on('click', '#updateProfile', function() {
   	 		
	
   	var data 			=	new FormData();
   	var form_data 		=	$('#kt_account_profile_details_form').serializeArray();
   	$.each(form_data, function (key, input) {
       		data.append(input.name, input.value);
   	});
   	
   	var file_data 		= $('input[name="avatar"]')[0].files;
   	for (var i = 0; i < file_data.length; i++) {
       		data.append("logo", file_data[i]);
   	}
   
   	/*var kt_docs_tinymce_hidden	=	tinymce.get("kt_docs_tinymce_hidden").getContent();
    	data.append("description", kt_docs_tinymce_hidden);
   	data.append('key', 'value');*/
   
   	$.ajax({
       			//url					: '{{route('product_company.clients.store')}}',
   				url					: '{{route('superadmin.team.update', @$team->id)}}',
   				headers				: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
       			method				: "POST",
       			processData			: false,
       			contentType			: false,
      		 	data				: data,
   				redirect			: true,
       			success				: function (data) {
   										//alert(data.status); 
   										if(data.status == 'success'){
   												 window.location = '{{ route('superadmin.company.index') }}' ;
   												//{"status":"success","message":"menu.clients created successfully.","action":"redirect","url":"https:\/\/rm.vibhanshujaiswal.com\/product_company\/clients"}	
   										}
   										
       			}
       			//error: function (e) {
           				//error
       			//}
   			});
   
   });
 
 
$(document).ready(function(){



 $(document).on('click', '#cancel', function(){
 		alert("call me"); 
 }); 
 
 $('.cancel').click(function(){ 
 	alert("call"); 
 }); 

}); 

</script>
@endpush 