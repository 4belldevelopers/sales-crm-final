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
    $salaryMode = GeneralMethod::salaryMode();
    $currencySymbole = GeneralMethod::currencySymbole();

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

                        <div class="table-responsive">
                            <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4"
                                id="currentPackage">
                                <thead class="">
                                    <tr class="fw-bolder text-muted">
                                        <th class="">Sr.No.</th>
                                        <th class="min-w-150px">Purpose</th>
                                        <th class="min-w-150px">Use</th>
                                        <th class="min-w-150px">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="defaultPackageBody">


                                    <tr>
                                        <td><a href="javascript:void(0);"
                                                class="text-dark fw-bolder text-hover-primary d-block fs-6">1</a>
                                        </td>
                                        <td><a href="javascript:void(0);"
                                                class="text-dark fw-bolder text-hover-primary d-block fs-6">Vendor Terms</a>
                                        </td>
                                       
                                        <td> For Vendors</td>
                                        <td class="">
                                            <a href="{{ route('superadmin.company.defaultTerms',[encrypt(1)]) }}"
                                                class="" data-toggle="tooltip" data-original-title="Edit">
                                                <i class="fa fa-edit text-primary fs-4" aria-hidden="true">Edit/View</i>
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><a href="javascript:void(0);"
                                                class="text-dark fw-bolder text-hover-primary d-block fs-6">2</a>
                                        </td>
                                        <td><a href="javascript:void(0);"
                                                class="text-dark fw-bolder text-hover-primary d-block fs-6">Candidate Offer terms</a>
                                        </td>
                                       
                                        <td> For Candidates</td>
                                        <td class="">
                                            <a href="{{ route('superadmin.company.defaultTerms',[encrypt(2)]) }}"
                                                class="" data-toggle="tooltip" data-original-title="Edit">
                                                <i class="fa fa-edit text-primary fs-4" aria-hidden="true">Edit/View</i>
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><a href="javascript:void(0);"
                                                class="text-dark fw-bolder text-hover-primary d-block fs-6">3</a>
                                        </td>
                                        <td><a href="javascript:void(0);"
                                                class="text-dark fw-bolder text-hover-primary d-block fs-6">Privacy policy</a>
                                        </td>
                                       
                                        <td> For Website </td>
                                        <td class="">
                                            <a href="{{ route('superadmin.company.defaultTerms',[encrypt(3)]) }}"
                                                class="" data-toggle="tooltip" data-original-title="Edit">
                                                <i class="fa fa-edit text-primary fs-4" aria-hidden="true">Edit/View</i>
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><a href="javascript:void(0);"
                                                class="text-dark fw-bolder text-hover-primary d-block fs-6">4</a>
                                        </td>
                                        <td><a href="javascript:void(0);"
                                                class="text-dark fw-bolder text-hover-primary d-block fs-6">Terms and Conditions</a>
                                        </td>
                                       
                                        <td> For Website</td>
                                        <td class="">
                                            <a href="{{ route('superadmin.company.defaultTerms',[encrypt(4)]) }}"
                                                class="" data-toggle="tooltip" data-original-title="Edit">
                                                <i class="fa fa-edit text-primary fs-4" aria-hidden="true">Edit/View</i>
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><a href="javascript:void(0);"
                                                class="text-dark fw-bolder text-hover-primary d-block fs-6">5</a>
                                        </td>
                                        <td><a href="javascript:void(0);"
                                                class="text-dark fw-bolder text-hover-primary d-block fs-6">Payment policy</a>
                                        </td>
                                       
                                        <td> For Website</td>
                                        <td class="">
                                            <a href="{{ route('superadmin.company.defaultTerms',[encrypt(5)]) }}"
                                                class="" data-toggle="tooltip" data-original-title="Edit">
                                                <i class="fa fa-edit text-primary fs-4" aria-hidden="true">Edit/View</i>
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><a href="javascript:void(0);"
                                                class="text-dark fw-bolder text-hover-primary d-block fs-6">6</a>
                                        </td>
                                        <td><a href="javascript:void(0);"
                                                class="text-dark fw-bolder text-hover-primary d-block fs-6">Fraud Alery</a>
                                        </td>
                                       
                                        <td> For Website</td>
                                        <td class="">
                                            <a href="{{ route('superadmin.company.defaultTerms',[encrypt(6)]) }}"
                                                class="" data-toggle="tooltip" data-original-title="Edit">
                                                <i class="fa fa-edit text-primary fs-4" aria-hidden="true">Edit/View</i>
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><a href="javascript:void(0);"
                                                class="text-dark fw-bolder text-hover-primary d-block fs-6">7</a>
                                        </td>
                                        <td><a href="javascript:void(0);"
                                                class="text-dark fw-bolder text-hover-primary d-block fs-6">Trust and Safety</a>
                                        </td>
                                       
                                        <td> For Website</td>
                                        <td class="">
                                            <a href="{{ route('superadmin.company.defaultTerms',[encrypt(7)]) }}"
                                                class="" data-toggle="tooltip" data-original-title="Edit">
                                                <i class="fa fa-edit text-primary fs-4" aria-hidden="true">Edit/View</i>
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><a href="javascript:void(0);"
                                                class="text-dark fw-bolder text-hover-primary d-block fs-6">8</a>
                                        </td>
                                        <td><a href="javascript:void(0);"
                                                class="text-dark fw-bolder text-hover-primary d-block fs-6">Report Issue</a>
                                        </td>
                                       
                                        <td> For Website</td>
                                        <td class="">
                                            <a href="{{ route('superadmin.company.defaultTerms',[encrypt(8)]) }}"
                                                class="" data-toggle="tooltip" data-original-title="Edit">
                                                <i class="fa fa-edit text-primary fs-4" aria-hidden="true">Edit/View</i>
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><a href="javascript:void(0);"
                                                class="text-dark fw-bolder text-hover-primary d-block fs-6">9</a>
                                        </td>
                                        <td><a href="javascript:void(0);"
                                                class="text-dark fw-bolder text-hover-primary d-block fs-6">Grievances</a>
                                        </td>
                                       
                                        <td> For Website</td>
                                        <td class="">
                                            <a href="{{ route('superadmin.company.defaultTerms',[encrypt(9)]) }}"
                                                class="" data-toggle="tooltip" data-original-title="Edit">
                                                <i class="fa fa-edit text-primary fs-4" aria-hidden="true">Edit/View</i>
                                            </a>
                                        </td>
                                    </tr>   
                                </tbody>
                            </table>
                        </div>



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
        if (CKEDITOR.env.ie && CKEDITOR.env.version < 9)

            CKEDITOR.tools.enableHtml5Elements(document);
        CKEDITOR.config.height = 350;
        CKEDITOR.config.width = 'auto';

        var initSample = (function() {
            var wysiwygareaAvailable = isWysiwygareaAvailable(),
                isBBCodeBuiltIn = !!CKEDITOR.plugins.get('bbcode');

            return function() {
                var editorElement = CKEDITOR.document.getById('editor1');

                if (isBBCodeBuiltIn) {
                    editorElement.setHtml(
                        'Hello world!\n\n' +
                        'I\'m an instance of [url=https://ckeditor.com]CKEditor[/url].'
                    );
                }
                if (wysiwygareaAvailable) {
                    CKEDITOR.replace('editor1');
                } else {
                    editorElement.setAttribute('contenteditable', 'true');
                    CKEDITOR.inline('editor1');
                }
            };

            function isWysiwygareaAvailable() {
                if (CKEDITOR.revision == ('%RE' + 'V%')) {
                    return true;
                }
                return !!CKEDITOR.plugins.get('wysiwygarea');
            }

        })();


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

            var id = 0;
            var url = "{{ route('superadmin.company.addEditTermsupdate', ':id') }}";
            url = url.replace(':id', id);
            var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
            var token = "{{ csrf_token() }}";

            var purpose = $('#purpose').val();
            var editor1 = CKEDITOR.instances.editor1.getData(); //$('#editor1').val();

            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    '_token': token,
                    '_method': 'POST',
                    'purpose': purpose,
                    'editor1': editor1
                },
                beforeSend: function() {
                    $('#main_container').html('<img src="' + baseurl + '" height="35px" width="100%">');
                },
                success: function(response) {
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
