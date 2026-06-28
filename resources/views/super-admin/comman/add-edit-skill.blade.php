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
                <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">Skills <span
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
                                <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">Skills</label>
                                <div class="col-lg-10 col-xl-10">
                                    <input type="hidden" name="tokenId" id="tokenId" value="{{ (@$skill_id) ? @$skill_id : 0 }}" >
                                  <input class="form-control form-control-solid" name="skill_name" id="skill_name" placeholder="Enter skill" value="{{( @$skill_name !='new') ?  @$skill_name : '' }}" >
                                </div>
                           </div>                       
                       <!--begin::Body-->
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

        var skill_id = '{{@$skill_id}}';
        var skill_name = '{{@$skill_name}}';
        var maxItemsCount = 1;
        if(skill_name=='new' || skill_id==0){
             maxItemsCount = 50;
        }

         var input = document.querySelector('input[name=skill_name]');

        var tagifyskill =  new Tagify(input, {
        whitelist: [''],
        dropdown: {
            maxItems: 20,           // <- mixumum allowed rendered suggestions 
            classname: "", // <- custom classname for this dropdown, so it could be targeted tagify__inline__suggestions
            enabled: 0,             // <- show suggestions on focus
            closeOnSelect: true    // <- do not hide the suggestions dropdown once an item has been selected
        },
        maxTags: maxItemsCount,
        transformTag: function(tagData) {
        // Remove special characters, allow only letters, numbers, and spaces
        tagData.value = tagData.value.replace(/[^a-zA-Z0-9 +#\/.]/g, '');
        return tagData;
        },
        validate: function(tagData) {
            // Prevent empty tags after cleaning
            return tagData.value.trim().length > 0 ? true : false;
        }
    });
        
    </script>

    <script>


                       $('#save-form').click(function(e) {
                         e.preventDefault();
                        const swalWithBootstrapButtons = Swal.mixin({
                                customClass: {
                                                    confirmButton: 'btn btn-success',
                                                    cancelButton: 'btn btn-danger'
                                                },
                                buttonsStyling: false
                            })
                                    var id        	=	$('#tokenId').val();;
									var url       	=	"{{ route('superadmin.company.addEditSkillUpdate',':id')}}"+"?_token=" + "{{ csrf_token() }}";
                                    
									url         	=	url.replace(':id', id);
									var baseurl     =	"{{ asset('assets/images/Loading2.gif') }}"; 
									var token       =	"{{ csrf_token() }}";
									
									var skill_name	=	$('#skill_name').val();
                                    var urlredirect       	=	"{{ route('superadmin.company.skillskElastic') }}";
	                                var formData = new FormData($('#editSettings')[0]);		
									$.ajax({
													type		: 'POST',
													url			: url,
                                                    data 	    : formData,
                                                    processData		: false,  	// tell jQuery not to process the data
                                                    contentType		: false,  	// tell jQuery not to set contentType
                                                    redirect			: true,
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
