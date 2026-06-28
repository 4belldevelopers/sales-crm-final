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
                <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">E-mail Notification <span
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
                                <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">Notification Type </label>
                                <div class="col-lg-10 col-xl-10">
                                    <select name="mailType_is" id="mailType_is" class="form-select form-select-solid" aria-label="Select mailType_is">
                                        <option value="1" {{ @$th_email_template->mailType ? ($th_email_template->mailType==1) ? 'selected' : '' : '' }}>Recruitment Notification</option>
                                        <option value="2" {{ @$th_email_template->mailType ? ($th_email_template->mailType==2) ? 'selected' : '' : '' }}>Sales Notification</option>  
                                        <option value="3" {{ @$th_email_template->mailType ? ($th_email_template->mailType==3) ? 'selected' : '' : '' }}>Mass Mail Template</option>  
                                        <option value="4" {{ @$th_email_template->mailType ? ($th_email_template->mailType==4) ? 'selected' : '' : '' }}>Candidate Send Email Template</option> 
                                        <option value="5" {{ @$th_email_template->mailType ? ($th_email_template->mailType==5) ? 'selected' : '' : '' }}>Client Send Email Template</option> 
                                        <option value="6" {{ @$th_email_template->mailType ? ($th_email_template->mailType==6) ? 'selected' : '' : '' }}>Offer Accept</option> 
                                        <option value="7" {{ @$th_email_template->mailType ? ($th_email_template->mailType==7) ? 'selected' : '' : '' }}>Offer Reject</option> 
                                        <option value="8" {{ @$th_email_template->mailType ? ($th_email_template->mailType==8) ? 'selected' : '' : '' }}>Offer Approved</option> 
                                        <option value="9" {{ @$th_email_template->mailType ? ($th_email_template->mailType==9) ? 'selected' : '' : '' }}>Offer Not Approved</option> 
                                        <option value="10" {{ @$th_email_template->mailType ? ($th_email_template->mailType==10) ? 'selected' : '' : '' }}>Offer Approved Notification HR</option> 
                                    </select>
                                 </div>
                               </div>


                            <div class="form-group row mb-4">
                                <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">Purpose</label>
                                <div class="col-lg-10 col-xl-10">
                                  <input class="form-control form-control-solid" name="purpose" id="purpose" placeholder="Enter purpose" value="{{( @$th_email_template->purpose) ?  @$th_email_template->purpose : '' }}" >
                                </div>
                           </div>
                   
                   

                            <div class="form-group row mb-4">
                                <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">Subject</label>
                                <div class="col-lg-10 col-xl-10">
                                  <input class="form-control form-control-solid" name="subject" id="subject" placeholder="Enter subject" value="{{( @$th_email_template->subject) ?  @$th_email_template->subject : '' }}" >
                                  <input type="hidden" name="tokenId" id="tokenId" value="{{ (@$th_email_template->id) ? @$th_email_template->id : 0 }}" >
                                  <span class="form-text text-muted">Please enter subject</span> </div>
                              </div>


                        <div class="form-group row mb-4">
                            <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">From Email</label>
                            <div class="col-lg-10 col-xl-10">
                              <input class="form-control form-control-solid" name="from_email_id" id="from_email_id" placeholder="Enter email" value="{{ (@$th_email_template->from_email_id) ? @$th_email_template->from_email_id : 'noreply@jero.co.in' }}" >
                              <span class="form-text text-muted">Please enter email</span> </div>
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
                        <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">Roles </label>
                        <div class="col-lg-10 col-xl-10">
                            <div class="col-sm-4">
                                @if(@$roles)
                                              @foreach(@$roles as $valuser)

                                              @php
                                              $checked = "";
                                              if(in_array($valuser->display_name,$mail_permissions)){
                                                $checked = "checked";
                                              }else{
                                                $checked = "chcked";
                                              }

                                              @endphp

                                <div class="switchery-demo">
                                    
                                    <input type="checkbox" class="js-switch role_ck" name="role_ck"
                                        data-color="#99d683" value="{{ $valuser->display_name }}" data-size="small" {{ $checked }}/> 
                                        <label class="control-label">{{ $valuser->display_name }}</label>
                                </div>
                                @endforeach
                                @endif
                            </div>
                    </div>
                   </div>
                       
                       <!--begin::Body-->
                          <div class="form-group row mb-4">
                            <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">Body</label>
                            <div class="col-lg-10 col-xl-10">
                              <textarea  id="editor1" name="editor1" row="10" >{{ (@$th_email_template->body_description) ? @$th_email_template->body_description : '' }}</textarea>
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

        CKEDITOR.plugins.add('tagsDropdown', {
            icons: 'tagsDropdown',
            init: function (editor) {
                editor.ui.addRichCombo('tagsDropdown', {
                    label: 'Select Tag Attributes',
                    title: 'Select Tag Attributes',
                    toolbar: 'insert',
                    panel: {
                        css: [CKEDITOR.skin.getPath('editor')].concat(editor.config.contentsCss),
                        multiSelect: false
                    },

                    init: function () {
                        this.startGroup('Tag Attributes');
                        this.add(' [{JOB_TITLE}] ', 'JOB_TITLE1');
                        this.add(' [{JOB_TITLE}] ', 'JOB_TITLE2');
                        this.add(' [{JOB_TITLE}] ', 'JOB_TITLE3');
                        this.add(' [{JOB_TITLE}] ', 'JOB_TITLE4');
                        this.add(' [{JOB_TITLE}] ', 'JOB_TITLE5');
                        this.add(' [{JOB_TITLE}] ', 'JOB_TITLE6');
                        this.add(' [{JOB_TITLE}] ', 'JOB_TITLE7');
                        this.add(' [{JOB_TITLE}] ', 'JOB_TITLE8');
                        this.add(' [{JOB_TITLE}] ', 'JOB_TITLE9');
                        this.add(' [{JOB_TITLE}] ', 'JOB_TITLE10');
                        this.add(' [{JOB_TITLE}] ', 'JOB_TITLE11');
                        this.add(' [{JOB_TITLE}] ', 'JOB_TITLE12');
                        this.add(' [{JOB_TITLE}] ', 'JOB_TITLE13');
                        this.add(' [{JOB_TITLE}] ', 'JOB_TITLE14');
                        this.add(' [{JOB_TITLE}] ', 'JOB_TITLE15');
                        this.add(' [{JOB_TITLE}] ', 'JOB_TITLE15');
                        // Add more cities as needed
                    },

                    onClick: function (value) {
                        editor.insertHtml(value);
                    }
                });
            }
        });


									
        if ( CKEDITOR.env.ie && CKEDITOR.env.version < 9 )
            
        CKEDITOR.tools.enableHtml5Elements( document );
        CKEDITOR.config.height 		=	350;
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
                                            // CKEDITOR.replace('editor1', {
                                            //     extraPlugins: 'tagsDropdown',
                                            //     toolbar: [
                                            //         { name: 'clipboard', items: ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo'] },
                                            //         { name: 'editing', items: ['Find', 'Replace', '-', 'SelectAll', '-'] },
                                            //         { name: 'insert', items: ['Image', 'Table', 'HorizontalRule', 'SpecialChar'] },
                                            //         { name: 'paragraph', items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote'] },
                                            //         { name: 'links', items: ['Link', 'Unlink', 'Anchor'] },
                                            //         { name: 'tools', items: ['Maximize', 'ShowBlocks'] },
                                            //         { name: 'colors', items: ['TextColor', 'BGColor'] },
                                            //         { name: 'custom', items: ['tagsDropdown'] }
                                            //     ]
                                            // });
                                        } else {
                                            editorElement.setAttribute( 'contenteditable', 'true' );   
                                            CKEDITOR.inline( 'editor1' );
                                            // editorElement.setAttribute('contenteditable', 'true');
                                            // CKEDITOR.inline('editor1', {
                                            //     extraPlugins: 'tagsDropdown'
                                            // });
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


                       $('#save-form').click(function() {
                        
                        const swalWithBootstrapButtons = Swal.mixin({
                                customClass: {
                                                    confirmButton: 'btn btn-success',
                                                    cancelButton: 'btn btn-danger'
                                                },
                                buttonsStyling: false
                            })
                                    var id        	=	$('#tokenId').val();;
									var url       	=	"{{ route('superadmin.company.addEditnotificationupdate',':id') }}";
									url         	=	url.replace(':id', id);
									var baseurl     =	"{{ asset('assets/images/Loading2.gif') }}"; 
									var token       =	"{{ csrf_token() }}";
									
									var purpose	=	$('#purpose').val();
									var subject		=	$('#subject').val();
                                    var status_is		=	$('#status_is').val();
                                    var from_email_id		=	$('#from_email_id').val();
                                    var mailType_is		=	$('#mailType_is').val();
                                    var role_ck = $(".role_ck:checked").map(function() {
                                        return this.value;
                                        }).get();
                                	var editor1		= CKEDITOR.instances.editor1.getData(); //$('#editor1').val();
                                    var urlredirect       	=	"{{ route('superadmin.company.notification') }}";

									$.ajax({
													type		: 'POST',
													url			: url,
													data		: {'_token': token, '_method': 'POST', 'purpose' : purpose, 'subject' : subject, 'tokenId':id,'editor1':editor1,'status':status_is,'from_email_id':from_email_id,'role_ck':role_ck,'mailType_is':mailType_is},
													beforeSend	: function() {
																				$('#main_container').html('<img src="'+baseurl+'" height="35px" width="100%">');
													},
													success 	: function (response) {
																	if (response.status == "success") {
																		  
																		  swalWithBootstrapButtons.fire(
																										  'Success!',
																										  '',
																										  'success'
																										)
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
