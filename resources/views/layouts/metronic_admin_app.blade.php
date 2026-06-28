<!DOCTYPE html>
<html lang="en">
	<!--begin::Head-->
	<head><base href="">
		<title>
					@if (auth()->user()->is_superadmin === 1)
							@lang('app.superAdminPanel')
					@else
							@lang('app.adminPanel')
			
					@endif | {{ __($pageTitle) }}
		</title>
		<meta name="description" content="{{ __($pageTitle) }}" />
		<meta name="keywords" content="{{ __($pageTitle) }}" />
		<meta charset="utf-8" />
		<!--<meta property="og:locale" content="en_US" />
		<meta property="og:type" content="article" />
		<meta property="og:title" content="Metronic - Bootstrap 5 HTML, VueJS, React, Angular &amp; Laravel Admin Dashboard Theme" />
		<meta property="og:url" content="https://keenthemes.com/metronic" />
		<meta property="og:site_name" content="Keenthemes | Metronic" />
		<link rel="canonical" href="Https://preview.keenthemes.com/metronic8" />-->
		<!--<link rel="shortcut icon" href="assets/media/logos/favicon.ico" />-->
		
		<!-- Favicon icon -->
		<link rel="apple-touch-icon" sizes="57x57" href="{{ asset('favicon/apple-icon-57x57.png') }}">
		<link rel="apple-touch-icon" sizes="60x60" href="{{ asset('favicon/apple-icon-60x60.png') }}">
		<link rel="apple-touch-icon" sizes="72x72" href="{{ asset('favicon/apple-icon-72x72.png') }}">
		<link rel="apple-touch-icon" sizes="76x76" href="{{ asset('favicon/apple-icon-76x76.png') }}">
		<link rel="apple-touch-icon" sizes="114x114" href="{{ asset('favicon/apple-icon-114x114.png') }}">
		<link rel="apple-touch-icon" sizes="120x120" href="{{ asset('favicon/apple-icon-120x120.png') }}">
		<link rel="apple-touch-icon" sizes="144x144" href="{{ asset('favicon/apple-icon-144x144.png') }}">
		<link rel="apple-touch-icon" sizes="152x152" href="{{ asset('favicon/apple-icon-152x152.png') }}">
		<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicon/apple-icon-180x180.png') }}">
		<link rel="icon" type="image/png" sizes="192x192" href="{{ asset('favicon/android-icon-192x192.png') }}">
		<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon/favicon-32x32.png') }}">
		<link rel="icon" type="image/png" sizes="96x96" href="{{ asset('favicon/favicon-96x96.png') }}">
		<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon/favicon-16x16.png') }}">
		<link rel="manifest" href="{{ asset('favicon/manifest.json') }}">

		<!--begin::Fonts-->
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
		<!--end::Fonts-->
		<!--begin::Page Vendor Stylesheets(used by this page)--> 
		<link href="{{ asset('metronic_assets/plugins/custom/fullcalendar/fullcalendar.bundle.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet" type="text/css" />
		<!--end::Page Vendor Stylesheets-->
		<!--begin::Global Stylesheets Bundle(used by all pages)-->  
		<link href="{{ asset('metronic_assets/plugins/global/plugins.bundle.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet" type="text/css" />
		<link href="{{ asset('metronic_assets/css/style.bundle.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet" type="text/css" /> 
		<!--end::Global Stylesheets Bundle-->
		<link href="{{ asset('metronic_assets/css/custom.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet" type="text/css" /> 
		
		
		@stack('head-script')
		
		
	</head>
	<!--end::Head-->
	<!--begin::Body-->
	<body id="kt_body" class="header-fixed header-tablet-and-mobile-fixed toolbar-enabled toolbar-fixed toolbar-tablet-and-mobile-fixed aside-enabled aside-fixed" style="--kt-toolbar-height:55px;--kt-toolbar-height-tablet-and-mobile:55px">
		<!--begin::Main-->
		<!--begin::Root-->
		<div class="d-flex flex-column flex-root">
			<!--begin::Page-->
			<div class="page d-flex flex-row flex-column-fluid">
				<!--begin::Aside-->
				<div id="kt_aside" class="aside aside-dark aside-hoverable" data-kt-drawer="true" data-kt-drawer-name="aside" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'200px', '300px': '250px'}" data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_aside_mobile_toggle">
					<!--begin::Brand-->
					<div class="aside-logo flex-column-auto" id="kt_aside_logo">
						<!--begin::Logo-->
						<a href="{{ route('admin.dashboard') }}">
							<img alt="Logo" src="{{ asset('img/Mily_recruit.png')}}" class="logo" style="width:150px; height:50px;" />
						</a>
						<!--end::Logo-->
						<!--begin::Aside toggler-->
						<div id="kt_aside_toggle" class="btn btn-icon w-auto px-0 btn-active-color-primary aside-toggle" data-kt-toggle="true" data-kt-toggle-state="active" data-kt-toggle-target="body" data-kt-toggle-name="aside-minimize">
							<!--begin::Svg Icon | path: icons/duotone/Navigation/Angle-double-left.svg-->
							<span class="svg-icon svg-icon-1 rotate-180">
								<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
									<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
										<polygon points="0 0 24 0 24 24 0 24" />
										<path d="M5.29288961,6.70710318 C4.90236532,6.31657888 4.90236532,5.68341391 5.29288961,5.29288961 C5.68341391,4.90236532 6.31657888,4.90236532 6.70710318,5.29288961 L12.7071032,11.2928896 C13.0856821,11.6714686 13.0989277,12.281055 12.7371505,12.675721 L7.23715054,18.675721 C6.86395813,19.08284 6.23139076,19.1103429 5.82427177,18.7371505 C5.41715278,18.3639581 5.38964985,17.7313908 5.76284226,17.3242718 L10.6158586,12.0300721 L5.29288961,6.70710318 Z" fill="#000000" fill-rule="nonzero" transform="translate(8.999997, 11.999999) scale(-1, 1) translate(-8.999997, -11.999999)" />
										<path d="M10.7071009,15.7071068 C10.3165766,16.0976311 9.68341162,16.0976311 9.29288733,15.7071068 C8.90236304,15.3165825 8.90236304,14.6834175 9.29288733,14.2928932 L15.2928873,8.29289322 C15.6714663,7.91431428 16.2810527,7.90106866 16.6757187,8.26284586 L22.6757187,13.7628459 C23.0828377,14.1360383 23.1103407,14.7686056 22.7371482,15.1757246 C22.3639558,15.5828436 21.7313885,15.6103465 21.3242695,15.2371541 L16.0300699,10.3841378 L10.7071009,15.7071068 Z" fill="#000000" fill-rule="nonzero" opacity="0.5" transform="translate(15.999997, 11.999999) scale(-1, 1) rotate(-270.000000) translate(-15.999997, -11.999999)" />
									</g>
								</svg>
							</span>
							<!--end::Svg Icon-->
						</div>
						<!--end::Aside toggler-->
					</div>
					<!--end::Brand-->
					
					
					
					<!--begin::Aside menu-->
						@include('sections.metronic-left-sidebar')					
					<!--end::Aside menu-->
					
					
					
					<!--begin::Footer-->
					<div class="aside-footer flex-column-auto pt-5 pb-7 px-5" id="kt_aside_footer">
						<a href="{{ route('admin.subscribe.index') }}" class="btn btn-custom btn-primary w-100" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-dismiss-="click" title="Subscription">
							<span class="btn-label">Subscription</span>
							<!--begin::Svg Icon | path: icons/duotone/General/Clipboard.svg-->
							<span class="svg-icon btn-icon svg-icon-2">
								<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
									<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
										<rect x="0" y="0" width="24" height="24" />
										<path d="M8,3 L8,3.5 C8,4.32842712 8.67157288,5 9.5,5 L14.5,5 C15.3284271,5 16,4.32842712 16,3.5 L16,3 L18,3 C19.1045695,3 20,3.8954305 20,5 L20,21 C20,22.1045695 19.1045695,23 18,23 L6,23 C4.8954305,23 4,22.1045695 4,21 L4,5 C4,3.8954305 4.8954305,3 6,3 L8,3 Z" fill="#000000" opacity="0.3" />
										<path d="M11,2 C11,1.44771525 11.4477153,1 12,1 C12.5522847,1 13,1.44771525 13,2 L14.5,2 C14.7761424,2 15,2.22385763 15,2.5 L15,3.5 C15,3.77614237 14.7761424,4 14.5,4 L9.5,4 C9.22385763,4 9,3.77614237 9,3.5 L9,2.5 C9,2.22385763 9.22385763,2 9.5,2 L11,2 Z" fill="#000000" />
										<rect fill="#000000" opacity="0.3" x="7" y="10" width="5" height="2" rx="1" />
										<rect fill="#000000" opacity="0.3" x="7" y="14" width="9" height="2" rx="1" />
									</g>
								</svg>
							</span>
							<!--end::Svg Icon-->
						</a>
					</div>
					<!--end::Footer-->
				</div>
				<!--end::Aside-->
				
				
				<!--begin::Wrapper-->
				<div class="wrapper d-flex flex-column flex-row-fluid" id="kt_wrapper">
					
					<!--begin::Header-->					
					@include('sections.metronic-top-header')
					<!--end::Header-->
					
					
					
					<!--begin::Content-->
					<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
						 @yield('content')
					</div>
					<!--end::Content-->
					
					
					
					
					<!--begin::Footer-->
					<div class="footer py-4 d-flex flex-lg-column" id="kt_footer">
						<!--begin::Container-->
						<div class="container-fluid d-flex flex-column flex-md-row align-items-center justify-content-between">
							<!--begin::Copyright-->
							<div class="text-dark order-2 order-md-1">  
								<span class="text-muted fw-bold me-1">&copy; {{ \Carbon\Carbon::today()->year }}</span>
								<a href="" target="_blank" class="text-gray-800 text-hover-primary">@lang('app.by') {{ $companyName }}</a>
							</div>
							<!--end::Copyright-->
							<!--begin::Menu-->
							<!--<ul class="menu menu-gray-600 menu-hover-primary fw-bold order-1">
								<li class="menu-item">
									<a href="https://keenthemes.com" target="_blank" class="menu-link px-2">About</a>
								</li>
								<li class="menu-item">
									<a href="https://keenthemes.com/support" target="_blank" class="menu-link px-2">Support</a>
								</li>
								<li class="menu-item">
									<a href="https://1.envato.market/EA4JP" target="_blank" class="menu-link px-2">Purchase</a>
								</li>
							</ul>-->
							<!--end::Menu-->
						</div>
						<!--end::Container-->
					</div>
					<!--end::Footer-->
				</div>
				<!--end::Wrapper-->
			</div>
			<!--end::Page-->
		</div>
		
		
		
		<!--end::Root-->
		<!--begin::Drawers-->
		<!--begin::Activities drawer-->
		
		<!--end::Activities drawer-->
		
		
		<!--begin::Chat drawer-->
		
		<!--end::Chat drawer-->
		<!--begin::Exolore drawer toggle-->
		
		<!--end::Exolore drawer toggle-->
		<!--begin::Exolore drawer-->
		
		<!--end::Exolore drawer-->
		<!--end::Drawers-->
		<!--begin::Modals-->
		<!--begin::Modal - Invite Friends-->
		
		<!--end::Modal - Invite Friend-->
		<!--begin::Modal - Create App-->
		
		<!--end::Modal - Create App-->
		
		
		
		
		
		
		
		
		
		
		
		
		<!--end::Modals-->
		<!--begin::Scrolltop-->
		<div id="kt_scrolltop" class="scrolltop" data-kt-scrolltop="true">
			<!--begin::Svg Icon | path: icons/duotone/Navigation/Up-2.svg-->
			<span class="svg-icon">
				<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
					<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
						<polygon points="0 0 24 0 24 24 0 24" />
						<rect fill="#000000" opacity="0.5" x="11" y="10" width="2" height="10" rx="1" />
						<path d="M6.70710678,12.7071068 C6.31658249,13.0976311 5.68341751,13.0976311 5.29289322,12.7071068 C4.90236893,12.3165825 4.90236893,11.6834175 5.29289322,11.2928932 L11.2928932,5.29289322 C11.6714722,4.91431428 12.2810586,4.90106866 12.6757246,5.26284586 L18.6757246,10.7628459 C19.0828436,11.1360383 19.1103465,11.7686056 18.7371541,12.1757246 C18.3639617,12.5828436 17.7313944,12.6103465 17.3242754,12.2371541 L12.0300757,7.38413782 L6.70710678,12.7071068 Z" fill="#000000" fill-rule="nonzero" />
					</g>
				</svg>
			</span>
			<!--end::Svg Icon-->
		</div>
		<!--end::Scrolltop-->
		<!--end::Main-->
		<!--begin::Javascript-->
		<!--begin::Global Javascript Bundle(used by all pages)-->
		<script src="{{ asset('metronic_assets/plugins/global/plugins.bundle.js') }}<?php echo TM_VERSION; ?>"></script>
		<script src="{{ asset('metronic_assets/js/scripts.bundle.js') }}<?php echo TM_VERSION; ?>"></script>
		<!--end::Global Javascript Bundle-->
		<!--begin::Page Vendors Javascript(used by this page)-->
		<script src="{{ asset('metronic_assets/plugins/custom/fullcalendar/fullcalendar.bundle.js') }}<?php echo TM_VERSION; ?>"></script>
		<!--end::Page Vendors Javascript-->
		<!--begin::Page Custom Javascript(used by this page)-->
		<script src="{{ asset('metronic_assets/js/custom/widgets.js') }}<?php echo TM_VERSION; ?>"></script>
		<script src="{{ asset('metronic_assets/js/custom/apps/chat/chat.js') }}<?php echo TM_VERSION; ?>"></script>
		<script src="{{ asset('metronic_assets/js/custom/modals/create-app.js') }}<?php echo TM_VERSION; ?>"></script>
		<script src="{{ asset('metronic_assets/js/custom/modals/upgrade-plan.js') }}<?php echo TM_VERSION; ?>"></script>
		<!--end::Page Custom Javascript-->
		<!--end::Javascript-->
		
		
		
		
		
		
		
		
		
		
<script src="{{ asset('assets/plugins/jquery/jquery.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/node_modules/popper/popper.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/plugins/datatables/jquery.dataTables.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/plugins/slimScroll/jquery.slimscroll.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/plugins/fastclick/fastclick.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/dist/js/adminlte.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src='https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.0/dist/js/bootstrap-select.min.js'></script>
<script src="{{ asset('assets/node_modules/sweetalert/sweetalert.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('froiden-helper/helper.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/node_modules/toast-master/js/jquery.toast.js') }}<?php echo TM_VERSION; ?>"></script>
{{--sticky note script--}}
<script src="{{ asset('js/cbpFWTabs.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/plugins/icheck/icheck.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/plugins/icheck/icheck.init.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/node_modules/Magnific-Popup-master/dist/jquery.magnific-popup.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/node_modules/Magnific-Popup-master/dist/jquery.magnific-popup-init.js') }}<?php echo TM_VERSION; ?>"></script>

<script>

    $('form input').keydown(function (e) 
	{		
        if (e.keyCode == 13) {
            e.preventDefault();
            $( "#save-form" ).click();
            return false;
        }
	});

    $('body').on('click', '.right-side-toggle', function () 
	{
        $("body").removeClass("control-sidebar-slide-open");
	})

    $(function () 
	{
        $('.selectpicker').selectpicker({
            style: 'btn-info',
            size: 4
        });
	});   

    function languageOptions() 
	{
		
        return {
            processing:     "@lang('modules.datatables.processing')",
            search:         "@lang('modules.datatables.search')",
            lengthMenu:    "@lang('modules.datatables.lengthMenu')",
            info:           "@lang('modules.datatables.info')",
            infoEmpty:      "@lang('modules.datatables.infoEmpty')",
            infoFiltered:   "@lang('modules.datatables.infoFiltered')",
            infoPostFix:    "@lang('modules.datatables.infoPostFix')",
            loadingRecords: "@lang('modules.datatables.loadingRecords')",
            zeroRecords:    "@lang('modules.datatables.zeroRecords')",
            emptyTable:     "@lang('modules.datatables.emptyTable')",
            paginate: {
                first:      "@lang('modules.datatables.paginate.first')",
                previous:   "@lang('modules.datatables.paginate.previous')",
                next:       "@lang('modules.datatables.paginate.next')",
                last:       "@lang('modules.datatables.paginate.last')",
            },
            aria: {
                sortAscending:  "@lang('modules.datatables.aria.sortAscending')",
                sortDescending: "@lang('modules.datatables.aria.sortDescending')",
            },
        }
	}

    $('.language-switcher').change(function () {
        var lang = $(this).val();
        $.easyAjax({
            url: '{{ route("superadmin.language-settings.change-language") }}',
            data: {'lang': lang},
            success: function (data) {
                if (data.status == 'success') {
                    window.location.reload();
                }
            }
        });
    });

    $('#mark-notification-read').click(function () {
        var token = '{{ csrf_token() }}';
        $.easyAjax({
            type: 'POST',
            url: '{{ route("mark-notification-read") }}',
            data: {'_token': token},
            success: function (data) {
                if (data.status == 'success') {
                    $('.top-notifications').remove();
                    $('#top-notification-dropdown .notify').remove();
                    window.location.reload();
                }
            }
        });
    });

    @if($user->hasRole('admin'))

        function addOrEditStickyNote(id)
        {

            var url = '';
            var method = 'POST';
            if(id === undefined || id == "" || id == null) {
                url =  '{{ route('admin.sticky-note.store') }}'
            } else{
                url = "{{ route('admin.sticky-note.update',':id') }}";
                url = url.replace(':id', id);
                var stickyID = $('#stickyID').val();
                method = 'PUT'
            }

            var noteText = $('#notetext').val();
            var stickyColor = $('#stickyColor').val();
            $.easyAjax({
                url: url,
                container: '#responsive-modal',
                type: method,
                data:{'notetext':noteText,'stickyColor':stickyColor,'_token':'{{ csrf_token() }}'},
                success: function (response) {
                    $("#responsive-modal").modal('hide');
                    getNoteData();
                }
            })
        }

        // FOR SHOWING FEEDBACK DETAIL IN MODEL
        function showCreateNoteModal()
		{
            var url = '{{ route('admin.sticky-note.create') }}';
            $.ajaxModal('#responsive-modal', url);
            return false;
		}

        // FOR SHOWING FEEDBACK DETAIL IN MODEL
        function showEditNoteModal(id)
		{
            var url = '{{ route('admin.sticky-note.edit',':id') }}';
            url  = url.replace(':id',id);
            $.ajaxModal('#responsive-modal', url);
            return false;
		}

        function selectColor(id)
		{
			
            $('.icolors li.active ').removeClass('active');
            $('#'+id).addClass('active');
            $('#stickyColor').val(id);
		}

        function deleteSticky(id)
		{

            swal({
                title: "Are you sure?",
                text: "You will not be able to recover the deleted Sticky Note!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "No, cancel please!",
                closeOnConfirm: true,
                closeOnCancel: true
            }, function(isConfirm){

                if (isConfirm) {
                    var url = "{{ route('admin.sticky-note.destroy',':id') }}";
                    url = url.replace(':id', id);
                    var token = "{{ csrf_token() }}";
                    $.easyAjax({
                        type: 'POST',
                        url: url,
                        data: {'_token': token, '_method': 'DELETE'},
                        success: function (response) {
                            $('#stickyBox_'+id).hide('slow');
                            $("#responsive-modal").modal('hide');
                            getNoteData();
                        }
                    });
                }
            });
		}

        //getting all chat data according to user
        function getNoteData()
		{
			
            var url = "{{ route('admin.sticky-note.index') }}";
            $.easyAjax({
                type: 'GET',
                url: url,
                messagePosition: '',
                data:  {},
                container: ".noteBox",
                success: function (response) {
                    //set notes in box
                    $('#sticky-note-list').html(response.stickyNotes);
                    $('#sticky-note-header span.badge').html(response.count);
                }
            });
		}

        // search input implementation
        function search($input, doneTypingInterval, type) 
		{
            var $anchor = $input.siblings('a');
            var typingTimer, fn;
            if (type == 'data') {
                fn = loadData;
            }
            if (type == 'table') {
                fn = redrawTable;                    
            }

            $input.on('keyup', function (e) {
                if ($(this).val() !== '' || ($(this).val().length >= 0 && e.key === 'Backspace')) {
                    clearTimeout(typingTimer);
                    typingTimer = setTimeout(() => {
                        fn();
                    }, doneTypingInterval);
                }
                $(this).val() !== '' ? $anchor.removeClass('d-none') : $anchor.addClass('d-none');
            })

            $input.on('keydown', function () {
                clearTimeout(typingTimer);
            });

            $anchor.click(function(e) {
                $(this).siblings('input').val('');
                fn();
                $anchor.addClass('d-none');
                $(this).siblings('input').focus();
            })
		}

        //    sticky notes script

        var stickyNoteOpen = $('#open-sticky-bar');
        var stickyNoteClose = $('#close-sticky-bar');
        var stickyNotes = $('#footer-sticky-notes');
        var viewportHeight = Math.max(document.documentElement.clientHeight, window.innerHeight || 0);
        var stickyNoteHeaderHeight = stickyNotes.height();

        $('#sticky-note-list').css('max-height', viewportHeight-150);

        stickyNoteOpen.click(function () {
            $('#sticky-note-list').toggle(function () {
                $(this).animate({
                    height: (viewportHeight-150)
                })
            });
            stickyNoteClose.toggle();
            stickyNoteOpen.toggle();
        })

        stickyNoteClose.click(function () {
        $('#sticky-note-list').toggle(function () {
            $(this).animate({
                height: 0
            })
        });
        stickyNoteOpen.toggle();
        stickyNoteClose.toggle();
    })

    @endif

</script>



<script>
$( document ).ready(function() {
				
			var currentControllerName   =   $('#currentControllerName').val();
			if(currentControllerName == 'AdminAdvancedSearchController')
			{
				$('#btnFiterSubmitSearch').hide();
				 $('#common_search').keyup(function() {
								tableLoad('filter');
					});
			}
			else
			{
						$('#btnFiterSubmitSearch').click(function () {
							var common_search       =   $('#common_search').val();          
							var url                 =   '<?php  echo url('admin/advsearch?common_search='); ?>'+common_search;          
							window.location.href    = url;
					   });  
			}
				
}); 
</script>

@stack('footer-script')
		
	</body>
	<!--end::Body-->
</html>

