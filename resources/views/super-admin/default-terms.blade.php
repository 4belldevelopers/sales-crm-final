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

        .cke_button__customfields {
            margin-left: auto;
            float: right;
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
                <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">Default Terms<span
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
                                <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">Terms</label>
                                <div class="col-lg-5 col-xl-5">
                                    <input type="hidden" name="encrypt_id" id="encrypt_id" value="{{ $encrypt_id }}" >
                                  <input class="form-control form-control-solid" name="purpose" id="purpose" placeholder="Enter purpose" value="{{ $title }}" readonly>
                                </div>
                                <div class="col-lg-5 col-xl-5">
                                    <input class="form-control form-control-solid" name="used" id="used" placeholder="Enter used" value="{{ $used }}" readonly>
                                </div>
                           </div>
                   
                       <!--begin::Body-->
                          <div class="form-group row mb-4">
                            <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">Body</label>
                            <div class="col-lg-10 col-xl-10">
                              <textarea  id="editor1" name="editor1" row="10" >{{ (@$th_email_template->terms) ? @$th_email_template->terms : '' }}</textarea>
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
                                            var editorElement = CKEDITOR.document.getById( 'editor1' );
                                    
                                            if ( isBBCodeBuiltIn ) {
                                                editorElement.setHtml(
                                                    'Hello world!\n\n' +
                                                    'I\'m an instance of [url=https://ckeditor.com]CKEditor[/url].'
                                                );
                                            }
                                            if ( wysiwygareaAvailable ) {
                                                // CKEDITOR.replace( 'editor1' );
                                                CKEDITOR.replace('editor1', {
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


                       $('#save-form').click(function() {
                        
                        const swalWithBootstrapButtons = Swal.mixin({
                                customClass: {
                                                    confirmButton: 'btn btn-success',
                                                    cancelButton: 'btn btn-danger'
                                                },
                                buttonsStyling: false
                            })
                            
                                    var id        	=	0;
									var url       	=	"{{ route('superadmin.company.addEditTermsupdate',':id') }}";
									url         	=	url.replace(':id', id);
									var baseurl     =	"{{ asset('assets/images/Loading2.gif') }}"; 
									var token       =	"{{ csrf_token() }}";
									
									var purpose	=	$('#purpose').val();
                                    var encrypt_id	=	$('#encrypt_id').val();
                                	var editor1		= CKEDITOR.instances.editor1.getData(); //$('#editor1').val();

									$.ajax({
													type		: 'POST',
													url			: url,
													data		: {'_token': token, '_method': 'POST', 'purpose' : purpose,'editor1':editor1,'encrypt_id':encrypt_id},
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
