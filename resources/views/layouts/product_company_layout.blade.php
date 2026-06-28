<!DOCTYPE html>
<html lang="en">
   <head>
      <base href="">
      <title>
         @if (auth()->user()->is_superadmin === 1)
         	@lang('app.superAdminPanel')
         @else
         	@lang('Meecruit') 
         @endif | {{ __($pageTitle) }}
      </title>
      <meta name="description" content="{{ __($pageTitle) }}" />
      <meta name="keywords" content="{{ __($pageTitle) }}" />
      <meta name="viewport" content="width=device-width, initial-scale=1" />
      <meta charset="utf-8" />
      <link rel="shortcut icon" type="image/x-icon"  href="https://bot.4bell.com/img/Favicon.png">
      <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
      <link href="{{ asset('demo/plugins/custom/fullcalendar/fullcalendar.bundle.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />
      <link href="{{ asset('demo/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
      <link href="{{ asset('demo/plugins/global/plugins.bundle.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />
      <link href="{{ asset('demo/css/style.bundle.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />
      @stack('head-script')
<style>
@media  only screen and (min-width: 1024px) and (max-width: 1440px) { #kt_activitiesApplication { width: 1025px;} #kt_activitiesIntTeam { width: 1025; } }
@media  only screen and (min-width: 1440px) and (max-width: 2560px) { #kt_activitiesApplication{ width: 1200px; } #kt_activitiesIntTeam {width: 1200px; } }
#kt_activitiesIntTeam {z-index:9999; }
</style>

</head>
   <!--end::Head-->
   <!--begin::Body-->
   <body id="kt_body" class="header-fixed header-tablet-and-mobile-fixed toolbar-enabled toolbar-fixed aside-enabled aside-fixed" style="--kt-toolbar-height:55px;--kt-toolbar-height-tablet-and-mobile:55px">
      <div class="d-flex flex-column flex-root">
         <div class="page d-flex flex-row flex-column-fluid">
            @include('sections.product_company.aside-header-base')
            <div class="wrapper d-flex flex-column flex-row-fluid" id="kt_wrapper">
               @include('sections.product_company.header-base')
               @yield('content')	
               @include('sections.product_company.footer')
            </div>
         </div>
      </div>

      @include('sections.product_company.activities-drawer')
     
      <!--begin::Scrolltop-->
      <div id="kt_scrolltop" class="scrolltop" data-kt-scrolltop="true">

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
      
	  <div id="kt_activitiesApplication_toggle"></div>
      <div id="kt_activitiesApplication" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activitiesApplication" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesApplication_toggle" data-kt-drawer-close="#kt_activitiesApplication_close">
         <div class="card shadow-none rounded-0 w-100" id="application_show_right_bar" ></div>
      </div>
	  
      <div class="modal fade" id="interview_schedule" tabindex="-1" aria-hidden="true">
         <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content" id="app_interview_schedule_content"></div>
         </div>
      </div>

      <div id="kt_activitiesIntTeam_toggle"></div>
      <div id="kt_activitiesIntTeam" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesIntTeam_toggle" data-kt-drawer-close="#kt_activitiesIntTeam_close">
         <div class="card shadow-none rounded-0 w-100" id="kt_activitiesIntTeam_body"></div>
      </div>


<div id="kt_activitiesBenchApplication_toggle"></div>
<div id="kt_activitiesBenchApplication" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activitiesBenchApplication" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesBenchApplication_toggle" data-kt-drawer-close="#kt_activitiesBenchApplication_close">
         <div class="card shadow-none rounded-0 w-100" id="bench_application_show_right_bar" ></div>
</div>

<div id="kt_activitiesPublicBenchApplication_toggle"></div>
<div id="kt_activitiesPublicBenchApplication" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activitiesPublicBenchApplication" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesPublicBenchApplication_toggle" data-kt-drawer-close="#kt_activitiesPublicBenchApplication_close">
         <div class="card shadow-none rounded-0 w-100" id="public_bench_application_show_right_bar" ></div>
</div>
	  	  	  	  
<script src="{{ asset('demo/plugins/global/plugins.bundle.js') }}{{ TM_VERSION }}"></script>
<script src="{{ asset('demo/js/scripts.bundle.js') }}{{ TM_VERSION }}"></script>
<script src="{{ asset('demo/plugins/custom/fullcalendar/fullcalendar.bundle.js') }}{{ TM_VERSION }}"></script>
<script src="{{ asset('demo/js/custom/widgets.js') }}{{ TM_VERSION }}"></script>
<script src="{{ asset('demo/js/custom/apps/chat/chat.js') }}{{ TM_VERSION }}"></script>
<script src="{{ asset('demo/js/custom/modals/create-app.js') }}{{ TM_VERSION }}"></script>
<script src="{{ asset('demo/js/custom/modals/upgrade-plan.js') }}{{ TM_VERSION }}"></script>
<script src="{{ asset('assets/plugins/jquery/jquery.min.js') }}{{ TM_VERSION }}"></script>	
<script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}{{ TM_VERSION }}"></script>
<script src="{{ asset('assets/plugins/datatables/jquery.dataTables.js') }}{{ TM_VERSION }}"></script>	
<script src="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.js') }}{{ TM_VERSION }}"></script>
	  
<script>

$( document ).ready(function() {
 
			 table.on('click', '.pbench_show_application_detail', function ()
			 {			 
							$('#kt_activitiesPublicBenchApplication_toggle').trigger('click');
							var id    		=	$(this).data('row-id');
							var url 		=	"{{ route('product_company.bench-candidates.public_show',':id') }}";
							url     		=	url.replace(':id', id);		
							var baseurl    	=	"{{ asset('assets/images/Loading2.gif') }}"; 
							$.ajax({
											type  		:	'GET',
											url   		:	url,
											beforeSend	:	function() {
																$('#public_bench_application_show_right_bar').html('<img src="'+baseurl+'" height="35px" width="100%">');
											},
											success 	:	function (response) {
																$('#public_bench_application_show_right_bar').html(response.view);
											}
									});
			 });  
 
});


</script>	 	  
<script>

$( document ).ready(function() {

	
			 table.on('click', '.bench_show_application_detail', function ()
			 {

				$('#kt_activitiesBenchApplication_toggle').trigger('click');
				var id    		= $(this).data('row-id');
				var url 		= "{{ route('product_company.bench-candidates.show',':id') }}";
				url     		= url.replace(':id', id);		
				var baseurl    	= "{{ asset('assets/images/Loading2.gif') }}"; 
				$.ajax({
								type  : 'GET',
								url   : url,
								beforeSend: function() {
											  $('#bench_application_show_right_bar').html('<img src="'+baseurl+'" height="35px" width="100%">');
									},
								success : function (response) 
								{
									$('#bench_application_show_right_bar').html(response.view);
								}
						});
			 }); 
 
 
 
});


</script>	   
<script>
         $('form input').keydown(function (e)
		 {         
             if (e.keyCode == 13)
			 {         
                 e.preventDefault();         
                 $( "#save-form" ).click();         
                 return false;         
             }         
         });
         
         $('body').on('click', '.right-side-toggle', function (){         
            $("body").removeClass("control-sidebar-slide-open");
         })
         
         $(function ()
		 {         
            $('.selectpicker').selectpicker({         	
				style	: 'btn-info',
                size	: 4
            });         
         });
         
         function languageOptions()
		 {         
             return
			 {         
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
         
                 paginate:
				 {         
                     first:      "@lang('modules.datatables.paginate.first')",         
                     previous:   "@lang('modules.datatables.paginate.previous')",         
                     next:       "@lang('modules.datatables.paginate.next')",         
                     last:       "@lang('modules.datatables.paginate.last')",         
                 },
         
                 aria:
				 {         
                    sortAscending:  "@lang('modules.datatables.aria.sortAscending')",
                    sortDescending: "@lang('modules.datatables.aria.sortDescending')",
                 },         
             }         
         }
         
         $('.language-switcher').change(function ()
		 {         
             var lang = $(this).val();
             $.ajax({
						 url		: 	'{{ route("superadmin.language-settings.change-language") }}',         
						 data		: 	{'lang': lang},         
						 success	: 	function (data){         
											if (data.status == 'success'){         
												window.location.reload();
											}
										}         
				 });         
         });
         
         $('#mark-notification-read').click(function ()
		 {         
             var token = '{{ csrf_token() }}';         
             $.ajax(
			{         
                 type		: 'POST',         
                 url		: '{{ route("mark-notification-read") }}',         
                 data		: {'_token': token},         
                 success	: function (data){         
												 if (data.status == 'success'){         
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
				var url 		= '';
				var method 		= 'POST';
				if(id === undefined || id == "" || id == null){
					url 			=  '{{ route('product_company.sticky-note.store') }}'
				}else{  
					url 			= "{{ route('product_company.sticky-note.update',':id') }}";
					url 			= url.replace(':id', id);
					var stickyID 	= $('#stickyID').val();
					method 			= 'PUT'         
				}      
                  
                var noteText = $('#notetext').val();
         		var stickyColor = $('#stickyColor').val();
                $.ajax(
				{         
                    url			:	url,
                    container	:	'#responsive-modal',
                    type		:	method,
                    data		:	{'notetext':noteText,'stickyColor':stickyColor,'_token':'{{ csrf_token() }}'},
         	        success		:	function (response){         
										$("#responsive-modal").modal('hide');
										getNoteData();
									}
                })
            }
             // FOR SHOWING FEEDBACK DETAIL IN MODEL
             function showCreateNoteModal(){
                 var url = '{{ route('product_company.sticky-note.create') }}';
                 $.ajaxModal('#responsive-modal', url);
                 return false;        
             }
             // FOR SHOWING FEEDBACK DETAIL IN MODEL
             function showEditNoteModal(id){
                 var url 	= '{{ route('product_company.sticky-note.edit',':id') }}';
                 url  		= url.replace(':id',id);
                 $.ajaxModal('#responsive-modal', url);
                 return false;
             }
         
             function selectColor(id){
                 $('.icolors li.active ').removeClass('active');
                 $('#'+id).addClass('active');
                 $('#stickyColor').val(id);
             }
         
             function deleteSticky(id){
         
                 swal({
         
						 title				: "Are you sure?",         
						 text				: "You will not be able to recover the deleted Sticky Note!",         
						 type				: "warning",         
						 showCancelButton	: true,         
						 confirmButtonColor	: "#DD6B55",         
						 confirmButtonText	: "Yes, delete it!",         
						 cancelButtonText	: "No, cancel please!",         
						 closeOnConfirm		: true,         
						 closeOnCancel		: true         
                 },
				 function(isConfirm)
				 {         
                    if (isConfirm)
					{         
                        var url 	=	"{{ route('product_company.sticky-note.destroy',':id') }}";
         				url 		=	url.replace(':id', id);
                         var token 	=	"{{ csrf_token() }}";
                         $.ajax({
									 type		: 'POST',
									 url		: url,
									 data		: {'_token': token, '_method': 'DELETE'},
									 success	: function (response) {
														 $('#stickyBox_'+id).hide('slow');
														 $("#responsive-modal").modal('hide');
														 getNoteData();
													}
                         });
                     }
                 });
         
             }
             //getting all chat data according to user
             function getNoteData(){
         
                 var url 		= 	"{{ route('product_company.sticky-note.index') }}";
                 $.ajax({
							 type			: 'GET',
							 url			: url,
							 messagePosition: '',
							 data			:  {},
							 container		: ".noteBox",
							 success		: function (response) {
															 $('#sticky-note-list').html(response.stickyNotes);
															 $('#sticky-note-header span.badge').html(response.count);
														 }
						 });
             }
             // search input implementation
             function search($input, doneTypingInterval, type) {
         
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
             var stickyNoteOpen 		=	$('#open-sticky-bar');
             var stickyNoteClose 		=	$('#close-sticky-bar');
             var stickyNotes 			=	$('#footer-sticky-notes');
             var viewportHeight 		=	Math.max(document.documentElement.clientHeight, window.innerHeight || 0);
             var stickyNoteHeaderHeight =	stickyNotes.height();
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
             if(currentControllerName == 'AdminAdvancedSearchController'){
                 $('#btnFiterSubmitSearch').hide();
                  $('#common_search').keyup(function() {
                                 tableLoad('filter');
                     });
             }else {         
				$('#btnFiterSubmitSearch').click(function () {
					var common_search       =   $('#common_search').val();          
					var url                 =   "{{ url('product_company/advsearch?common_search=') }}"+common_search;                  
					window.location.href    = 	url;
				});  
             }         
        
        }); 
         
      </script>	
      @stack('footer-script')
      <script>	
         table.on('click', '.show-application-detail', function ()
         {

         	$('#kt_activitiesApplication_toggle').trigger('click');
         	var id    		= 	$(this).data('row-id');
         	var url 		= 	"{{ route('product_company.job-applications.show',':id') }}";
         	url     		= 	url.replace(':id', id);		   
         	var baseurl    	= 	"{{ asset('assets/images/Loading2.gif') }}"; 
         	$.ajax({
         					type  		: 	'GET',
         					url   		: 	url,
         					beforeSend	: 	function() {
                                          		$('#application_show_right_bar').html('<img src="'+baseurl+'" height="35px" width="100%">');
                            				},
         					success 	:	function (response) {
         										$('#application_show_right_bar').html(response.view);
         									}
           			});
         }); 
         
         $('body').on('click', '#app_interview_schedule', function(){
         				
         			var id    		=	$(this).attr('rel');
         			var url 		=	"{{ route('product_company.job-applications.create-schedule',':id') }}";
         			url     		=	url.replace(':id', id);		   
         			var baseurl    	=	"{{ asset('assets/images/Loading2.gif') }}"; 
         			$.ajax({
         							type  		: 	'GET',
         							url   		: 	url,
         							beforeSend	: 	function() {
         														$('#app_interview_schedule_content').html('<img src="'+baseurl+'" height="35px" width="100%">');
         											},
         							success 	: 	function (response) {
														$('#app_interview_schedule_content').html(response.view);		
													}
         				});	
         });	
         
         $('body').on('click', '.add_panel', function(){
         	
         				$('#kt_activitiesIntTeam_toggle').trigger('click');
         				
         				var panellist	=	$('#panellist').val();
         				var token 		=	"{{ csrf_token() }}";
         				
         				var id    		=	1;
         				var url   		=	"{{ route('product_company.job-applications.recruiter',':id') }}"; 
         				url     		=	url.replace(':id', id);  		
         				var baseurl    	=	"{{ asset('assets/images/Loading2.gif') }}";    		
         				$.ajax({
         							type  		: 	'POST',
         							data		: 	{'_token': token, 'panellist' : panellist},
         							url   		: 	url,
         							beforeSend	: 	function() {
         														$('#kt_activitiesIntTeam_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
         											},
         							success 	: 	function (response) {
														$('#kt_activitiesIntTeam_body').html(response.view);
													}
         				}); 				
         		
         });
      </script>		
   </body>
   <!--end::Body-->
</html>