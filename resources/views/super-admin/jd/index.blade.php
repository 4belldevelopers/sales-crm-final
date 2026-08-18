@extends('layouts.superadmin_layout')

@push('head-script')

<link rel="stylesheet" href="//cdn.datatables.net/fixedheader/3.1.5/css/fixedHeader.bootstrap.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
@endpush

<style>
    body
    {
        overflow-x: hidden;
    }

    <style>
.border-radius-5 {border-radius: 5px!important; }
.ml-2 {margin-left: 2em;}
@media only screen and (min-width: 900px) and (max-width: 2560px)
{
  .table td, .table th, .table tr {width: max-content!important; }
  .table > :not(caption) > * > * { padding: 0.75em 0rem!important; }
  td:nth-child(n+2),
  th:nth-child(n+2) { width: revert!important; }
}
@media only screen and (min-width: 1024px) and (max-width: 1440px)
{
  #kt_activitiesClient { width: 1025px; }
}
@media only screen and (min-width: 1440px) and (max-width: 2560px)
{
  #kt_activitiesClient { width: 1200px; }
}
@media only screen and (min-width: 1024px) and (max-width: 1440px)
{
  #kt_activitiesDirectClient { width: 1025px; }
}
@media only screen and (min-width: 1440px) and (max-width: 2560px)
{
  #kt_activitiesDirectClient {width: 1200px;}
}
.navi {
    padding: 0;
    margin: 0;
    display: block;
    list-style: none;
}
.pb-5, .py-5 {
    padding-bottom: 1.25rem !important;
}
.pt-5, .py-5 {
    padding-top: 1.25rem !important;
}
@media (min-width: 205px) {
    .dropdown-menu.dropdown-menu-sm {
        width: 175px;
    }
}
.dropdown-menu.show {
    display: block;
}
.navi .navi-item .navi-link {
    -webkit-transition: all 0.15s ease;
    transition: all 0.15s ease;
    color: #3F4254;
}
.navi .navi-item .navi-link {
    font-size: 1rem;
}
.navi .navi-item .navi-link {
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-align: center;
    -ms-flex-align: center;
    align-items: center;
    padding: 0.75rem 1.5rem;
}
a, button {
    outline: 0 !important;
}
</style>

</style>
@php
    use App\Helper\GeneralMethod;
    use Illuminate\Support\Facades\DB;
    $currency = GeneralMethod::convertINRToUSD();
    $payment_settings = DB::table('payment_settings')->select('payment_settings.*')->first();

@endphp
@section('create-button')
<a href="{{ route('superadmin.company.create') }}" class="btn btn-dark btn-sm m-l-15"><i class="fa fa-plus-circle"></i> @lang('app.createNew')</a>
@endsection

@section('content')

<!--begin::toolbar-->
<div class="toolbar" id="kt_toolbar">
	<div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
		<div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
			<h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">{{ ucfirst($jd_status) }} JD List
                <span class="h-20px border-gray-200 border-start ms-3 mx-2 d-none"></span>
			<small class="text-muted fs-7 fw-bold my-1 ms-1 d-none">#XRS-45670</small>
		</h1></div>
        <div class="d-flex align-items-center py-1">

              <!--begin::Dropdown-->
                    <div class="btn-group ml-2">
                        <button type="button" class="btn btn-primary font-weight-bold btn-sm px-3 font-size-base">
                            Add New JD
                        </button>

                        <button type="button" class="btn btn-primary font-weight-bold btn-sm px-3 font-size-base dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        </button>

                        <div class="dropdown-menu dropdown-menu-sm p-0 m-0 dropdown-menu-right">
                            <ul class="navi py-5">
                                <li class="navi-item">
                                    <a href="{{ route('superadmin.company.createjd',[encrypt(1),encrypt(0)]) }}" class="navi-link">
                                        <span class="navi-icon"><i class="fa fa-edit" aria-hidden="true"></i> </span>
                                        <span class="navi-text">Manually Add </span>
                                    </a>
                                </li>
                                <li class="navi-item">
                                    <a href="{{ route('superadmin.company.createjd',[encrypt(2),encrypt(0)]) }}" class="navi-link">
                                        <span class="navi-icon"><i class="fa fa-edit" aria-hidden="true"></i> </span>
                                        <span class="navi-text">By Google Sheet</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <!--end::Dropdown-->
		</div>
	</div>
</div>
<!--end::toolbar-->

<!--begin::content-->
<div class="post d-flex flex-column-fluid mt-5" id="kt_post">
    <div id="kt_content_container" class="container-fluid px-3">
    
        <div class="row gy-5 g-xl-8">
            <div class="col-xl-12 mt-4">
                <div class="card card-xl-stretch mb-5 mb-xl-8">
                    {{-- <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="text-muted mt-1 fw-bold fs-7">Over {{ number_format($totalCompanies) }} JD</span>
                        </h3>
                    </div> --}}
                    <div class="card-body py-3">
                        <div class="table-responsive">
                            <table id="myTable2" class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
								<thead>
									<tr>
										<th> <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input class="form-check-input widget-9-check" type="checkbox" value="1">
                                            </div>
										</th>										
										<th>Title</th>
										<th>Experience</th>
										<th>Skills</th>
                                        <th>Action</th>
									</tr>
								</thead>
							</table>
					
					
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



@endsection
@push('footer-script')
<script src="{{ asset('demo/cdn/js/dataTables.fixedHeader.min.js') }}{{ TM_VERSION }}"></script>
<script src="{{ asset('js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('js/responsive.bootstrap.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('assets/node_modules/bootstrap-datepicker/bootstrap-datepicker.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/moment/moment.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="{{ asset('assets/plugins/calendar/dist/fullcalendar.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/plugins/calendar/dist/locale-all.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="https://cdn.ckeditor.com/ckeditor5/34.1.0/classic/ckeditor.js"></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

<script>
    

	  var table = $('#myTable2').dataTable({
            
				responsive     	: true, 
      			processing      : true,
				ordering		: false, //disable ordering      			
      			"bLengthChange"   : true,   
      			"lengthMenu"      : [[10, 25, 50, 100], [10, 25, 50, 100]],
				sDom			: 'lrtip',   
				
			//responsive: true,
            // processing: true,
			searching: false,
            serverSide: true,
            ajax: '{!! route('superadmin.company.jdlistdata') !!}'+'?jd_status=' + '{{ $jd_status }}',
            language: {
                "url": "<?php echo __("app.datatable") ?>"
            },
            "fnDrawCallback": function( oSettings ) {
                $("body").tooltip({
                    selector: '[data-toggle="tooltip"]'
                });
            },
            columns: [
                { data: 'chkbox', name: 'chkbox'},
			    { data: 'title', name: 'title' },
                { data: 'experience', name: 'experience' },
                { data: 'skills', name: 'skills' },
                { data: 'action', name: 'action' }
            ]
        });

        new $.fn.dataTable.FixedHeader( table );



    $(document).on('submit','#client_new_form', function(e) {
                e.preventDefault(); // Prevent the form from submitting via the browser
                var form = $(this);
                // var url = form.attr('action');
                var formData = form.serialize();
                $('.indicator-label-client').hide();
                $('.indicator-progress-client').show();
                $('#client_new_submit').prop('disabled',true);
                $.ajax({
                    type: "POST",
                    url: '{{route('superadmin.company.googleSheetInsertdataInDatabase')}}'+'?_token=' + '{{ csrf_token() }}',		
                    data: formData,
                    success: function(response) {
                      $('#client_new_submit').prop('disabled',false);
                      if(response.status=='error'){
                        alert(response.message);
                        $('.indicator-label-client').show();
                        $('.indicator-progress-client').hide();
                        return false;
                      }
                      $("#client_new_form")[0].reset();
                      $('.indicator-label-client').show();
                      $('.indicator-progress-client').hide();
                      $('#successButtonclient').show();
                      $('#errorButtonclient').show();
                       $('#successButtonclient').text(response.done+" client uploaded successfully");
                       $('#errorButtonclient').text(response.pendind+" client pending");
                       setTimeout(() => {
                        $('#successButtonclient').hide();
                        $('#errorButtonclient').hide();
                       }, 2500);

                    },
                    error: function(xhr, status, error) {

                      var errors = xhr.responseJSON.errors;
                        $('.error').empty(); // Clear any previous errors

                        if (errors.googlesheetid) {
                          alert("Client google sheet Id id required");
                          $('.indicator-label-client').show();
                          $('.indicator-progress-client').hide();
                          return false;
                            // $('#name-error').html(errors.googlesheetid[0]);
                        }
                        if (errors.teams) {
                          alert("Team is required");
                          $('.indicator-label-client').show();
                          $('.indicator-progress-client').hide();
                          return false;
                            // $('#email-error').html(errors.job_id[0]);
                        }
                    }
                });
    });

   
    
		
</script>




@endpush
