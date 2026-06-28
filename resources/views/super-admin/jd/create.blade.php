@extends('layouts.superadmin_layout')

@push('head-script')
      <link rel="stylesheet" href="{{ asset('assets/node_modules/switchery/dist/switchery.min.css') }}">
    <!--end::Add New Css of Metronic-7-->

    <style>
        body {
            overflow-x: hidden;
        }
    </style>
@endpush
@section('content')
    <!--begin::Toolbar-->
    <div class="toolbar" id="kt_toolbar">
        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
            <div data-kt-swapper="true" data-kt-swapper-mode="prepend"
                data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}"
                class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
                <div class="d-flex flex-column">
                    <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">
                        <a href="javascript:void(0);" class="text-gray-800 mb-1">{{ (@$tokenID > 0) ? 'Update' : 'Add' }} JD</a>
                    </h1>
                </div>
                <span class="h-20px border-gray-200 border-start mx-4"></span>
                <span class="text-dark-800 fw-bold" id="kt_subheader_total">Enter Jd {{ ($entryType==2) ? 'Google Sheet ID' : 'details' }} and submit</span>
            </div>
        </div>
    </div>
    <!--end::Toolbar-->

    <!--begin::content-->
    <div class="content d-flex flex-column flex-column-fluid pt-4 pb-5" id="kt_content">
        <div class="d-flex flex-column-fluid">
            <div class="container-fluid px-3">
                <div class="card card-custom">
                    <div class="card-body p-0">
                        <div class="wizard wizard-1" id="kt_wizard" data-wizard-state="first" data-wizard-clickable="false">
                            
                            <div class="row justify-content-center my-10 px-8 my-lg-15 px-lg-10">
                                <div class="col-xl-12 col-xxl-12">
                                     <!--begin::Form-->
                                     <form id="client_new_form" class="form" action="#">
                                        <input type="hidden" name="entryType" value="{{ $entryType }}" id="entryType">
                                        <input type="hidden" name="tokenID" value="{{ @$tokenID }}" id="tokenID">
                                        <!--begin::Input group-->
                                        @if($entryType==2) 
                                        <div class="d-flex flex-column mb-7 fv-row">
                                                <!--begin::Label-->
                                                <div class="row">
                                                    <div class="col-xxl-8 mb-5 mb-xl-10">
                                                        <label class="d-flex align-items-center fs-6 fw-semibold form-label mb-2">
                                                            <span class="">JD Google Sheet Link</span>
                                                        </label>
                                                        <!--end::Label-->
                            
                                                        <input type="text" class="form-control form-control-solid" placeholder="" name="googlesheetlink" value="https://docs.google.com/spreadsheets/d/1aHkQvqnPs2L4ajhMhZLqr9VjvEY7hiU6bPjpueS6XLg/edit?gid=0#gid=0"/>
                                                    </div>

                                                    <div class="col-xxl-4 mb-5 mb-xl-10">
                                                        <label class="d-flex align-items-center fs-6 fw-semibold form-label mb-2">
                                                            <span class="required">JD Google Sheet Id</span>
                                                        </label>
                                                        <!--end::Label-->
                            
                                                        <input type="text" class="form-control form-control-solid" placeholder="" name="googlesheetid" value="1aHkQvqnPs2L4ajhMhZLqr9VjvEY7hiU6bPjpueS6XLg"/>
                                                    </div>
                                                </div>
                                            </div> 
                                            @else 

                                            <div class="row">
                                            <div class="col-xxl-4 mb-5 mb-xl-10">
                                                 <!--begin::Label-->
                                                 <label class="d-flex align-items-center fs-6 fw-semibold form-label mb-2">
                                                    <span class="required">Title</span>
                                                 </label>
                                                <!--end::Label-->
                                                <input type="text" class="form-control form-control-solid" placeholder="" name="title" value="{{ (@$jd_details) ? $jd_details->title : '' }}" required="">
                                            </div> 
                                            <div class="col-xxl-3 mb-5 mb-xl-10">
                                                 <!--begin::Label-->
                                                 <label class="d-flex align-items-center fs-6 fw-semibold form-label mb-2">
                                                    <span class="">Minimum Experience</span>
                                                 </label>
                                                <!--end::Label-->
                                                <select class="form-select form-select-solid" name="minimum_experience" id="minimum_experience">
                                                    <option value="" disabled="" selected="">Select Experience</option>
                                                    <option value="0">Freshers</option>
                                                    <?php 
                                                      $yearText = "Year";
                                                      for($i=1; $i<=30; $i++)
                                                      {
                                                        if($i > 9){
                                                          $yearText = "Years";
                                                        }                            
                                                        $min_exp		=	$sel	=	'';
                                                        $min_exp		=	(@$jd_details) ? $jd_details->minimum_exp : '';
                                                        if($min_exp == @$i ){
                                                          $sel		=	'selected="selected"' ;	
                                                        }                              
                                                        echo '<option value="'.$i.'"   '.$sel.'>'.$i.' &nbsp; '.$yearText.'</option>';
                                                      }
                                                    ?>                      
                                                </select>

                                            </div> 
                                            <div class="col-xxl-3 mb-5 mb-xl-10">
                                                 <!--begin::Label-->
                                                 <label class="d-flex align-items-center fs-6 fw-semibold form-label mb-2">
                                                    <span class="">Maximum Experience</span>
                                                 </label>
                                                <!--end::Label-->
                                                <select class="form-select form-select-solid" name="maximum_experience" id="maximum_experience" >
                                                    <option value=""  disabled selected >Select Experience</option>
                                                    <option value="0">Freshers</option>
                                                    <?php 
                                                        $yearText = "Year";
                                                        for($i=1; $i<=30; $i++)
                                                        {
                                                          if($i > 9){
                                                            $yearText = "Years";
                                                          }			
                                                          $max_exp		=	$sel	=	'';
                                                          $max_exp		=	(@$jd_details) ? $jd_details->maxmum_exp : '';
                                                          if($max_exp == @$i ){
                                                            $sel		=	'selected="selected"' ;	
                                                          }		
                                                          echo '<option value="'.$i.'"  '.$sel.'>'.$i.' &nbsp; '.$yearText.'</option>';
                                                        }
                                                      ?>
                                                  </select>


                                            </div> 
                                            <div class="col-xxl-2 mb-5 mb-xl-10">
                                                <!--begin::Label-->
                                                <label class="d-flex align-items-center fs-6 fw-semibold form-label mb-2">
                                                   <span class="">Select Status</span>
                                                </label>
                                               <!--end::Label-->
                                               <select class="form-select form-select-solid" name="jd_status" id="jd_status" >
                                                   <option value="0" {{ (@$jd_details->status==0) ? 'selected' : '' }} >Pending</option>
                                                   <option value="1" {{ (@$jd_details->status==1) ? 'selected' : '' }}>Approved</option>
                                                   <option value="2" {{ (@$jd_details) ? ((@$jd_details->status==2) ? 'selected' : '') : 'selected' }}>Decline</option>
                                                 </select>
                                           </div> 

                                            </div>                                      
                                                                                 
                                            <div class="row">
                                            <div class="col-xxl-12 mb-5 mb-xl-10">
                                                 <!--begin::Label-->
                                                 <label class="d-flex align-items-center fs-6 fw-semibold form-label mb-2">
                                                    <span class="required">Skills</span>
                                                 </label>
                                                <!--end::Label-->
                                                <input type="text" class="form-control form-control-solid" placeholder="" name="skills" value="{{ (@$jd_details) ? $jd_details->skills : '' }}" id="kt_tagify_2">
                                            </div> 
                                            </div>
                                            <div class="row">
                                                <!--begin::Label-->
                                                <label class="d-flex align-items-center fs-6 fw-semibold form-label mb-2">
                                                   <span class="">JD Details</span>
                                                </label>
                                               <!--end::Label-->
                                               <div class="col-xxl-12 mb-5 mb-xl-10">
                                                <textarea  id="editor1" name="jd_details" row="10" >{{ (@$jd_details) ? $jd_details->jd : '' }}</textarea>
                                               </div> 
                                               </div> 
                                            @endif

                                           
                                        <!--begin::Actions-->
                                        <div class="text-center pt-15">
                                            <button id="duplicaterButtonclient" class="btn btn-danger" style="margin: 1px; width: 100%; display: none;"></button>
                                            <button id="successButtonclient" class="btn btn-success" style="margin: 1px; width: 100%; display: none;"></button>
                                            <button id="errorButtonclient" class="btn btn-danger" style="margin: 1px; width: 100%; display: none;"></button>
                                            <button type="submit" id="client_new_submit" class="btn btn-primary">
                                                <span class="indicator-label indicator-label-client">
                                                    Submit
                                                </span>
                                                <span class="indicator-progress indicator-progress-client">
                                                    Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                                </span>
                                            </button>
                                        </div>
                                        @if($entryType==2) 
                                        <br>
                                            <p><strong>Step 1.</strong> Kindly share your Google Sheet to this below ID<br> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span id="textToCopy">get-google-api-data@get-goolge-sheet.iam.gserviceaccount.com</span> <span class="copy-button" onclick="copyText()" style="background: rebeccapurple; padding: 9px; border-radius: 6px; color: white; cursor: pointer;">Click to Copy Text</span></p>

                                            <p><strong>Step 2.</strong> Find out your Google Sheet ID from URL and in between d and Edit </span>
                                                <img alt="Pic" src="{{ str_replace("index.php","",url('public/tracker-teams-image.png')) }}"> </p>
                                        @endif

                                        <!--end::Actions-->
                                    </form>
                                    <!--end::Form-->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('footer-script')
    <script src="{{ asset('assets/node_modules/select2/dist/js/select2.full.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/node_modules/bootstrap-select/bootstrap-select.min.js') }}" type="text/javascript">
    </script>
    <script src="{{ asset('assets/node_modules/dropify/dist/js/dropify.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/iCheck/icheck.min.js') }}"></script>
    <script src="{{ asset('assets/node_modules/bootstrap-datepicker/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>

        <script src="{{ asset('demo/ckeditor/ckeditor.js') }}"></script>
        <script>

            var input2 = document.querySelector("#kt_tagify_2");
                // new Tagify(input2);

            var tagifyskill =  new Tagify(input2, {
                    whitelist: [''],
                    maxTags: 10,
                });

    
                    
            if ( CKEDITOR.env.ie && CKEDITOR.env.version < 9 )
                
            CKEDITOR.tools.enableHtml5Elements( document );
            CKEDITOR.config.height 		=	250;
            CKEDITOR.config.width 		=	'auto';
    
            // Load the custom plugin
            
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
        //Flat red color scheme for iCheck
        $('input[type="checkbox"].flat-red').iCheck({
            checkboxClass: 'icheckbox_flat-blue',
        })

        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd'
        })

        $('#featured').on('ifChecked', function(event) {
            $("#featureDateBox").show();
        });
        $('#featured').on('ifUnchecked', function(event) {
            $("#featureDateBox").hide();
        });

        // For select 2
        $(".select2").select2();

        $('.dropify').dropify({
            messages: {
                default: '@lang('app.dragDrop')',
                replace: '@lang('app.dragDropReplace')',
                remove: '@lang('app.remove')',
                error: '@lang('app.largeFile')'
            }
        });

        $('#save-form').click(function() {
            $.easyAjax({
                url: '{{ route('superadmin.company.store') }}',
                container: '#editSettings',
                type: "POST",
                redirect: true,
                file: true
            })
        });
    </script>

    <!--begin::Editor-->
    <script>
        // Class definition

        var KTTinymce = function() {
            // Private functions
            var demos = function() {
                tinymce.init({
                    selector: '#kt-tinymce-4',
                    menubar: false,
                    toolbar: [
                        'styleselect fontselect fontsizeselect do redo | cut copy paste | bold italic | link image | alignleft aligncenter alignright alignjustify bullist numlist | outdent indent | blockquote subscript superscript | advlist | autolink | lists charmap | print preview |  code'
                    ],
                    plugins: 'advlist autolink link image lists charmap print preview code'
                });
            }

            return {
                // public functions
                init: function() {
                    demos();
                }
            };
        }();

        // Initialization
        jQuery(document).ready(function() {
            KTTinymce.init();
        });
    </script>
    <!--end::Editor-->

    



    <script>

            $(document).on('submit','#client_new_form', function(e) {
                e.preventDefault(); // Prevent the form from submitting via the browser
                var form = $(this);
                // var url = form.attr('action');
                var tokenID = $('#entryType').val();
                var formData = form.serialize();
                $('.indicator-label-client').hide();
                $('.indicator-progress-client').show();
                $('#client_new_submit').prop('disabled',true);
                $.ajax({
                    type: "POST",
                    url: '{{route('superadmin.company.jdgoogleSheetInsertdataInDatabase')}}'+'?_token=' + '{{ csrf_token() }}',		
                    data: formData,
                    success: function(response) {
                      $('#client_new_submit').prop('disabled',false);
                      if(response.status=='error'){
                        alert(response.message);
                        $('.indicator-label-client').show();
                        $('.indicator-progress-client').hide();
                        return false;
                      }
                      //   $("#client_new_form")[0].reset();
                      $('.indicator-label-client').show();
                      $('.indicator-progress-client').hide();
                      $('#successButtonclient').show();                     
                      if(tokenID==2){
                       
                        $('#successButtonclient').text(response.done+" JD uploaded successfully");
                        if(response.duplicate > 0){
                            $('#duplicaterButtonclient').show();
                            $('#duplicaterButtonclient').text(response.duplicate+" JD Duplicate");
                        }
                        if(response.pendind > 0){
                            $('#errorButtonclient').show();
                            $('#errorButtonclient').text(response.pendind+" JD pending"); 
                        }
                        
                      }else{
                        $('#successButtonclient').text(" JD Updated successfully");
                      }
                       setTimeout(() => {
                        $('#successButtonclient').hide();
                        $('#errorButtonclient').hide();
                        $('#duplicaterButtonclient').hide();
                       }, 2500);

                    },
                    error: function(xhr, status, error) {

                      var errors = xhr.responseJSON.errors;
                        $('.error').empty(); // Clear any previous errors

                        if (errors.googlesheetid) {
                          Swal.fire({
                                title: 'Warning',
                                text: "JD google sheet Id id required",
                                icon: 'warning',
                                allowOutsideClick: false,
                            });

                          $('.indicator-label-client').show();
                          $('.indicator-progress-client').hide();
                          $('#client_new_submit').prop('disabled',false);
                          return false;
                            // $('#name-error').html(errors.googlesheetid[0]);
                        }
                        if (errors.title) {
                            Swal.fire({
                                title: 'Warning',
                                text: errors.title,
                                icon: 'warning',
                                allowOutsideClick: false,
                            });
                          $('.indicator-label-client').show();
                          $('.indicator-progress-client').hide();
                          $('#client_new_submit').prop('disabled',false);
                          return false;
                            // $('#email-error').html(errors.job_id[0]);
                        }
                        if (errors.skills) {
                            Swal.fire({
                                title: 'Warning',
                                text: errors.skills,
                                icon: 'warning',
                                allowOutsideClick: false,
                            });
                          $('.indicator-label-client').show();
                          $('.indicator-progress-client').hide();
                          $('#client_new_submit').prop('disabled',false);
                          return false;
                            // $('#email-error').html(errors.job_id[0]);
                        }
                    }
                });
            });

            $(document).on("change","#minimum_experience",function(){
            experience_check()
            });
            $(document).on("change","#maximum_experience",function(){
            experience_check()
            });


            function experience_check(){
                var min_exp = parseInt($("#minimum_experience").val());
                var max_exp = parseInt($("#maximum_experience").val());
                if(min_exp != ''){
                    if(min_exp > max_exp){
                    Swal.fire({
                        title: 'Warning',
                        text: 'Select the maximum experience greater than the minimum experience',
                        icon: 'warning',
                        allowOutsideClick: false,
                    });
                    $("#maximum_experience").val('');
                    }
                }
            }

            function copyText() {
        // Get the text to be copied
        var textToCopy = document.getElementById("textToCopy").innerText;
        
        // Create a temporary input element
        var tempInput = document.createElement("input");
        tempInput.value = textToCopy;
        document.body.appendChild(tempInput);
        
        // Select the text
        tempInput.select();
        tempInput.setSelectionRange(0, 99999); // For mobile devices
        
        // Copy the text
        document.execCommand("copy");
        
        // Remove the temporary input element
        document.body.removeChild(tempInput);
        
        // Provide feedback to the user
        alert("Text copied to clipboard: " + textToCopy);
    }



    </script>


@endpush
