@extends('layouts.product_company_layout')

@push('head-script')
<link rel="stylesheet" href="//cdn.datatables.net/fixedheader/3.1.5/css/fixedHeader.bootstrap.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
<link rel="stylesheet" href="{{ asset('assets/node_modules/html5-editor/bootstrap-wysihtml5.css') }}<?php echo TM_VERSION; ?>">
<link rel="stylesheet" href="{{ asset('assets/node_modules/bootstrap-datepicker/bootstrap-datepicker.min.css') }}<?php echo TM_VERSION; ?>">
<link rel="stylesheet" href="{{ asset('assets/node_modules/multiselect/css/multi-select.css') }}<?php echo TM_VERSION; ?>">
<link rel="stylesheet" href="{{ asset('assets/plugins/iCheck/all.css') }}<?php echo TM_VERSION; ?>">
<link href="{{ asset('assets/dist/css/adminlte.min.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet" type="text/css" />
<link href="{{ asset('css/custom.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/icons/themify-icons/themify-icons.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet" type="text/css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.12.4/jquery.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script>
$(document).on('click', function (e) {
    var flag  = 0;
    if ($('.control-sidebar-slide-open').is(':visible')){
      if ($(e.target).closest("#right-sidebar-content").length === 0) {
          flag  = 1;
      }
    }     
    if ($('#scheduleDetailModal').is(':visible')){
          flag = 0;
    }
    if(flag == 1){
      $("body").removeClass("control-sidebar-slide-open");  
    }
});
</script>
<style>
.tooltip {position: relative;text-align:center;opacity:1;z-index: 0;}
.tooltip .tooltiptext {visibility: hidden;width: 250px;background-color: #0056b3;color: #fff;
  text-align: left;border-radius: 6px;padding: 10px;position: absolute;z-index: 5;min-height:auto;height:auto;}
.tooltip:hover .tooltiptext {visibility: visible;opacity: 1;}

table.table-bordered.dataTable tbody td:last-child{text-align: center;}
table.table-bordered.dataTable tbody td:first-child{text-align: center;}

body{margin: 0px!important;padding: 0px!important}
.view-data, .edit-data, .sa-params{padding: 2px;}

#reset-filters, #apply-filters{font-size: 11px;}
.mb-20{margin-bottom: 20px}
.datepicker{z-index: 9999 !important;}
.select2-container--default .select2-selection--single, .select2-selection .select2-selection--single {width: 100%;}
.select2-search {width: 100%;}
.select2.select2-container {width: 100% !important;}
.select2-search__field {
            width: 100% !important;
            display: block;
            padding: .375rem .75rem !important;
            font-size: 1rem;
            line-height: 1.5;
            color: #495057;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid #ced4da;
            transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
}

.d-block{display: block;}
.upcomingdata {height: 37.5rem; overflow-x: scroll;}
.notify-button{font-size: 0.730rem !important; line-height: 0.5 !important;}
.scheduleul{padding: 0 15px 0 11px;}
.margin-left-custom{margin-left: -0.4em;}
/*Radio btn css*/
.radio-tile-group{display: flex;flex-wrap: wrap;justify-content: left;}
.radio-tile-group .input-container{margin: .4rem;}
.radio-tile-group .input-container1{position: relative;height: 1rem;width: 11em;margin: .4rem;}
.radio-tile-group .input-container .radio-button{opacity: 0;position: absolute;top: 0;left: 0;height: 100%;width: 100%;margin: 0; cursor: pointer;height:5vh;}
.radio-tile-group .input-container .radio-tile{display: flex;flex-direction: column;align-items: center;justify-content: center;
  width: 100%;height: 100%;border: 2px solid #079ad9;border-radius: 3px;padding: 1rem;transition: transform 300ms ease;
    color: #079ad9;}
.radio-tile-group .input-container .radio-button:checked + .radio-tile .radio-tile-label {color: white;margin:0;}

.radio-tile-group .input-container .radio-tile {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 100%;
    border: 2px solid #079ad9;
    border-radius: 3px;
    padding: 1rem;
    transition: transform 300ms ease;
}


/*pending*/
.radio-tile-group .input-container .radio-button:checked + .radio-tile-pending
{
  background-color: #ffa808!important;
  border: 2px solid #ffa808!important; 
  color:#fff white;transform: scale(1.1, 1.1);
}
.radio-tile-group .input-container .radio-button:checked + .radio-tile-pending .radio-tile-label-pending
{
  color: #fff!important;
  background-color: #ffa808!important;
  margin:0;
}

/*shortlisted*/
.radio-tile-group .input-container .radio-button:checked + .radio-tile-shortlisted
{
  background-color: #06c732!important;
  border: 2px solid #06c732!important; 
  color:#fff white;transform: scale(1.1, 1.1);
}
.radio-tile-group .input-container .radio-button:checked + .radio-tile-shortlisted .radio-tile-label-shortlisted
{
  color: #fff!important;
  background-color: #06c732!important;
  margin:0;
}

/*reschedule*/
.radio-tile-group .input-container .radio-button:checked + .radio-tile-reschedule
{
  background-color: #079ad9!important;
  border: 2px solid #079ad9!important; 
  color:#fff white;
  transform: scale(1.1, 1.1);
}
.radio-tile-group .input-container .radio-button:checked + .radio-tile-reschedule .radio-tile-label-reschedule
{
  color: #fff!important;
  background-color: #079ad9!important;
  margin:0;
}

/*rejected*/
.radio-tile-group .input-container .radio-button:checked + .radio-tile-rejected
{
  background-color: #ff0202!important;
  border: 2px solid #ff0202!important; 
  color:#fff white;
  transform: scale(1.1, 1.1);
}
.radio-tile-group .input-container .radio-button:checked + .radio-tile-rejected .radio-tile-label-rejected
{
  color: #fff!important;
  background-color: #ff0202!important;
  margin:0;
}

/*other*/
.radio-tile-group .input-container .radio-button:checked + .radio-tile-other
{
  background-color: #aba7a7!important;
  border: 2px solid #aba7a7!important;
  color:#fff white;transform: scale(1.1, 1.1);
}
.radio-tile-group .input-container .radio-button:checked + .radio-tile-other .radio-tile-label-other
{
  color: #fff!important;
  background-color: #aba7a7!important;
  margin:0;
}
/*Radio btn css end*/


.content .card {
    height: 100vh;
}

/*Background color for Status*/
.available-bg
{
    background:#189435;
    color:#FFFFFF;
}
.not-available-bg
{
    background:#ffbf00;
    color:#FFFFFF;
}
.cancel-bg
{
    background:#ff001b;
    color:#FFFFFF;
}
.not-intrested
{
    background:#7d7d7d;
    color:#FFFFFF;
}
/*Background color for Status End*/

/*Background color for Feedback End*/
.pending-bg
{
    background:#ffbf00;
    color:#FFFFFF;
}
.shortlisted-bg
{
    background:#189435;
    color:#FFFFFF;
}
.rejected-bg
{
    background:#ff001b;
    color:#FFFFFF;
}
.rescheduled-bg
{
    background:#017cff;
    color:#FFFFFF;
}
.feedback-bg
{
    background:#ffa303;
    color:#FFFFFF;
}
/*Background color for Feedback End*/



</style>
@endpush

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body"> @section('create-button')
        <div class="row">
          <div class="col-md-6 text-left">
            <div class="dropdown dropdown-inline">
              <button class="dropbtn bg-light dropdown-toggle dropbtn-border"> 10 </button>
              <div class="dropdown-content">
                <ul class="navi">
                  <li class="navi-item"> <a href="javascript:void(0);" class="navi-link custom-width"> <span class="navi-text">20</span> </a> </li>
                  <li class="navi-item"> <a href="javascript:void(0);" class="navi-link custom-width"> <span class="navi-text">30</span> </a> </li>
                </ul>
              </div>
            </div>
            <!-- Page Title -->
            <span class="card-title pt-2 pb-2">All Interviews</span>
            <!-- Page Title End -->
          </div>
          <div class="col-md-6 text-right">
            <div class="input-group mr-2" style="width:fit-content;display:inline-flex;">
              <input type="text" name="inputSearch" id="inputSearch" class="form-control" placeholder="Search..." style="padding: 0.3em .5em;">
            </div>
            @if(in_array("view_schedule", $userPermissions)) <a href="{{ route('product_company.interview-schedule.index') }}" class="btn btn-sm btn-light-danger px-1 py-1 mr-2"> <i class="fa fa-table fa-sm"></i>&nbsp;Calendar View </a> @endif <a onclick="exportExcel()">
            <button class="btn btn-light-primary btn-sm mr-2 px-2 py-1" type="button"> <i class="fa fa-download fa-sm"></i>&nbsp; </button>
            </a> <a href="javascript:void(0);" id="toggle-filter" class="btn btn-light-dark btn-sm toggle-filter  px-2 py-1 mr-2"> <i class="fa fa-life-ring"></i></a> </div>
        </div>
        @endsection
        <div class="row" style="display: none; background: #fbfbfb;" id="ticket-filters">
          <div class="col-md-12">
            <form action="" class="row" id="filter-form" style="width: 100%;">
              <div class="col-md-5">
                <h5 >@lang('app.selectDateRange')</h5>
                <div class="example">
                  <div class="input-daterange input-group" id="date-range">
                    <input type="text" class="form-control" id="start-date" placeholder="Show Results From" value="" />
                    <span style="text-align: center" class="m-2">@lang('app.to')</span>
                    <input type="text" class="form-control" id="end-date" placeholder="Show Results To" value="" />
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <h5 ></h5>
                <div class="form-group" style="margin-top: 26px">
                  <label class="control-label col-xs-12">&nbsp;</label>
                  <button type="button" id="apply-filters" class="btn btn-light-primary btn-sm col-md-6 py-2 font-weight-500"> @lang('app.apply') <i class="fa fa-paper-plane fa-sm"></i> </button>
                  <button type="button" id="reset-filters" class="btn btn-light-warning btn-sm col-md-5 py-2 col-md-offset-1 font-weight-500">@lang('app.reset') <i class="fa fa-spinner fa-sm"></i> </button>
                </div>
              </div>
            </form>
          </div>
        </div>
        <div class="table-responsive">
          <table id="myTable" class="table table-bordered table-striped table-hover table-checkable">
            <thead>
              <tr class="table-bg-custom">
                <th>ID</th>
                <th>Date</th>
                <th>@lang('app.candidate')</th>
                <th>Position</th>
                <th>Client</th>
                <th>Status</th>
                <th>Mode</th>
                <th>Stage</th>
                <th>Sub Stage</th>
                <th>Source</th>
                <th>Interviewers</th>
                <!-- <th><div class="tooltip font-weight-700">INT<span class="tooltiptext font-weight-500">Interviewers</span></div></th> -->
                <th>Feedback</th>
                <th>@lang('app.action')</th>
              </tr>
            </thead>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- FEEDBACK Modal START  -->
<div class="modal fade" id="feebdack" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header modal-htcp">
        <h4 class="modal-title"><i class="icon-plus"></i>Subject : 1001 | Web Developer | 4Bell Technology | New Delhi | 24-Dec-2020/18:00&nbsp;</h4>
        <!-- <a href="javascript:void(0);"><i class="flaticon2-pen text-primary"></i></a> -->
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
      </div>
      <div class="modal-body">
        <form id="Schedule_feebdack" class="ajax-form" method="post">
          <div class="row">
            <div class="col-xs-6 col-md-6">
              <div class="form-group">
                <label class="d-block text-dark text-capitalize mt-0" style=""> <span class="font-weight-bold">Scheduled Date : </span> 24-Dec-2020 </label>
              </div>
            </div>
            <div class="col-xs-6 col-md-6">
              <div class="form-group chooseCandidate bootstrap-timepicker timepicker">
                <label class="d-block text-dark text-capitalize mt-0" style=""> <span class="font-weight-bold">Scheduled  Time : </span> 19:00 </label>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-xs-5 col-md-12">
              <div class="form-group" id="child" style="display:none">
                <label class="d-block text-dark text-capitalize" style=""> <span class="font-weight-bold mt-0">Interview Status Level : </span> Level - II </label>
              </div>
            </div>
          </div>
          <div class="row mt-0">
            <div class="col-xs-12 col-md-6">
              <div class="form-group">
                <label class="d-block text-dark text-capitalize" style=""> <span class="font-weight-bold mt-0">Candidate : </span> Abc 1214 </label>
              </div>
            </div>
            <div class="col-xs-12 col-md-6">
              <div class="form-group">
                <label class="d-block text-dark text-capitalize" style=""> <span class="font-weight-bold mt-0">Interviewers : </span> Abu Baker | Anjali Awasti | Nupur Mishra </label>
              </div>
            </div>
          </div>
          <div class="row mt-3">
            <div class="col-xs-12 col-md-12">
              <div class="form-group">
                <label class="custom-label-color font-weight-bold mt-0">Feedback</label>
                <div class="container">
                  <div class="radio-tile-group">
                    <div class="input-container input-container1">
                      <input id="pending" class="radio-button" type="radio" name="radio" checked="checked" />
                      <div class="radio-tile radio-tile-pending">
                        <label for="pending" class="radio-tile-label radio-tile-label-pending">Pending</label>
                      </div>
                    </div>
                    <div class="input-container input-container1">
                      <input id="shortlist" class="radio-button" type="radio" name="radio" />
                      <div class="radio-tile radio-tile-shortlisted">
                        <label for="shortlist" class="radio-tile-label radio-tile-label-shortlisted">Shortlisted</label>
                      </div>
                    </div>
                    <div class="input-container input-container1">
                      <input id="reschedule" class="radio-button" type="radio" name="radio" />
                      <div class="radio-tile radio-tile-reschedule">
                        <label for="reschedule" class="radio-tile-label radio-tile-label-reschedule">Reschedule</label>
                      </div>
                    </div>
                    <div class="input-container input-container1">
                      <input id="rejected" class="radio-button" type="radio" name="radio" />
                      <div class="radio-tile radio-tile-rejected">
                        <label for="rejected" class="radio-tile-label radio-tile-label-rejected">Rejected</label>
                      </div>
                    </div>
                    <div class="input-container input-container1">
                      <input id="other1" class="radio-button" type="radio" name="radio" />
                      <div class="radio-tile radio-tile-other">
                        <label for="other1" class="radio-tile-label radio-tile-label-other">Other</label>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row mt-9">
            <div class="col-xs-12 col-md-12">
              <div class="form-group">
                <label for="message-text" class="col-form-label pt-0 font-weight-bold mt-0">Feedback to Assessment Stage | Sub Stage</label>
                <textarea class="form-control" id="message-text" placeholder="Write a detail feedback for Interview" rows="6"></textarea>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="form-group row">
              <div class="col-12 col-form-label ml-2">
                <div class="checkbox-inline">
                  <label class="checkbox checkbox-primary mt-0">
                  <input type="checkbox" name="Checkboxes5"/ checked="checked">
                  <span class="mr-1"></span> Add Feedback </label>
                </div>
              </div>
            </div>
          </div>
          <!-- For Interviewer -->
          <style>
            .rounded-pill {height: 18px;}
            .inside-img{font-size:9px;padding:0.3em;font-weight:bold;border: 1px solid #13a6ec;background:#13a6ec;color: #fff;}
          </style>
        </form>
      </div>
      <div class="modal-footer py-5">
        <button type="button" class="btn btn-light-danger btn-sm p-1 mr-2" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-light-primary btn-sm p-1">Add Feedback</button>
      </div>
    </div>
  </div>
</div>
<!-- FEEDBACK Modal END  -->
{{--Ajax Modal Start for--}}
<div class="modal fade bs-modal-lg in" id="scheduleDetailModal_feedback" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
  <div class="modal-dialog modal-lg" id="modal-data-application">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <span class="caption-subject font-red-sunglo bold uppercase" id="modelHeading_feedback"></span> </div>
      <div class="modal-body"> Loading... </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light-danger mr-2" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-light-success">Save changes</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade bs-modal-lg in" id="scheduleDetailModal" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
  <div class="modal-dialog modal-lg" id="modal-data-application">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <span class="caption-subject font-red-sunglo bold uppercase" id="modelHeading"></span> </div>
      <div class="modal-body"> Loading... </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light-danger mr-2" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-light-success">Save changes</button>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<div class="modal fade bs-modal-exl in" id="moveApplicationModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md" id="modal-data-application">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <span class="caption-subject font-red-sunglo bold uppercase" id="moveApplicationmodelHeading"></span> </div>
      <div class="modal-body"> Loading... </div>
      <div class="modal-footer"> </div>
    </div>
  </div>
</div>
<div class="modal fade bs-modal-exl in" id="copyApplicationModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md" id="modal-data-application">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <span class="caption-subject font-red-sunglo bold uppercase" id="copyApplicationmodelHeading"></span> </div>
      <div class="modal-body"> Loading... </div>
      <div class="modal-footer"> </div>
    </div>
  </div>
</div>
{{--Ajax Modal Ends--}}
@endsection

@push('footer-script')
<script src="{{ asset('assets/node_modules/select2/dist/js/select2.full.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/bootstrap-select/bootstrap-select.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="//cdn.datatables.net/fixedheader/3.1.5/js/dataTables.fixedHeader.min.js"></script>
<script src="//cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
<script src="//cdn.datatables.net/responsive/2.2.3/js/responsive.bootstrap.min.js"></script>
<script src="{{ asset('assets/node_modules/moment/moment.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/bootstrap-datepicker/bootstrap-datepicker.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
<script>

$(document).ready(function() {

	
	$('#inputSearch').keyup(function() {
			//alert("call");
			tableLoad('filter'); 
	});
	
});

</script>
<script>    
    

        loadTable();
        $(".select2").select2({
            formatNoMatches: function () {
                return "{{ __('messages.noRecordFound') }}";
            }
        });
        $('#start-date').datepicker({
            // format: 'yyyy-mm-dd'
           // format: 'd-M-Y: H:I:s'
      format: 'yyyy-m-d'
        })
        $('#end-date').datepicker({
            // format: 'yyyy-mm-dd'
            //format: 'd-M-Y: H:I:s'
      format: 'yyyy-m-d'
        })

        var table;
        function loadTable(){
		
            var startDate 			= $('#start-date').val();

            if (startDate == '') {
                startDate = null;
            }

            var endDate 			= $('#end-date').val();

            if (endDate == '') {
                endDate = null;
            }

            var status 				= $('#status').val();
            var applicationID 		= $('#applicationID').val();

			 var inputSearch       	= $('#inputSearch').val();        
			 
            table   = $('#myTable').dataTable({
            
                        responsive  	: true,
                        processing  	: true,
                        serverSide  	: true,
                        destroy   		: true,
                        "bFilter" 		: false,
                        "bInfo"   		: false,
                        "bLengthChange"	: true, 
                        oLanguage 		: {
                                				sLengthMenu: "_MENU_",
                                		  },
								
								
                ajax    : '{!! route('product_company.interview-schedule.data') !!}?startDate=' + startDate + '&endDate=' + endDate + '&status=' + status + '&applicationID=' + applicationID+' &search= '+inputSearch,
				
				
                language: {
                        "url": "<?php echo __("app.datatable") ?>",
                        buttons: {
                    selectAll: "Select all items",
                    selectNone: "Select none"
                             }
                      },
                "fnDrawCallback": function( oSettings ) {
                    $("body").tooltip({
                        selector: '[data-toggle="tooltip"]'
                    });
                },
        
        "fnRowCallback" : function(nRow, aData, iDisplayIndex){
                    $("td:first", nRow).html(iDisplayIndex +1);
                    return nRow;
              }, 
          
            "columnDefs": [
  
            { "width": "10px", "targets": 0 },
            { "width": "10px", "targets": 1 },
            { "width": "190px", "targets": 2 },
            { "width": "20px", "targets": 3 },
            { "width": "20px", "targets": 4 },
            { "width": "30px", "targets": 5 },            
            { "width": "20px", "targets": 6 },
            { "width": "20px", "targets": 7 },
            { "width": "30px", "targets": 8 },
            { "width": "80px", "targets": 9 },
            { "width": "80px", "targets": 10 },
            { "width": "30px", "targets": 11 },            
            { "width": "30px", "targets": 12 }
    
          ],     
        
      columns: [
          {data: 'id', name:'id',width: '1%', searchable : false, orderable: false},  
          // { data: 'id', name: 'id'},
          { data: 'schedule_date', name: 'schedule_date',width: '20%', searchable : false, orderable: true},
          { data: 'full_name', name: 'full_name',width: '15%', searchable : false, orderable: false},
          { data: 'job_title', name: 'job_title',width: '11%', searchable : false, orderable: false},
          { data: 'company_name', name: 'company_name',width: '3%', searchable : false, orderable: false},
          { data: 'available_status', name: 'available_status',width: '4%', searchable : false, orderable: false},
          { data: 'interview_mode', name: 'interview_mode',width: '2%', searchable : false, orderable: false},
          { data: 'master_status_name', name: 'master_status_name',width: '2%', searchable : false, orderable: false},
          { data: 'child_status_name', name: 'child_status_name',width: '8%', searchable : false, orderable: false},
          { data: 'source', name: 'source',width: '2%', searchable : false, orderable: false},
          { data: 'interviewer', name: 'interviewer',width: '5%', searchable : false, orderable: false},
          { data: 'feedback_status', name: 'feedback_status',width: '3%', searchable : false, orderable: false},
          { data: 'action', name: 'action',width: '1%', searchable : false, orderable: false}
          ],
                buttons: [
                    'selectAll',
                    'selectNone'
                ],
                columnDefs: [ {
                targets     : 0,
                data      : null,
                defaultContent  : '',
                orderable   : false,
                className   : 'select-checkbox',
                'render'    : function (data, type, full, meta){
                  // return '<input type="checkbox" class="checkboxes" name="id[]" value="'
                  //     + $('<div/>').text(data).html() + '">';
                  return '<p class="text-center">1</p>';
                }
                      } ],
                select: {
                    style:    'os',
                    selector: 'td:first-child'
                },
                order: [[ 1, 'asc' ]]
            });
            new $.fn.dataTable.FixedHeader( table );

        }
  
  		 table.search( '' ).columns().search( '' ).draw();
  
  
  
  
  
    table.on('click', '.show-detail', function () 
    {   
            $(".right-sidebar").slideDown(50).addClass("shw-rside");

            var id      = $(this).data('row-id');
            var url     = "{{ route('product_company.job-applications.show',':id') }}";
            url       = url.replace(':id', id);

            $.easyAjax({
                type: 'GET',
                url: url,
                success: function (response) {
                    if (response.status == "success") {
                        $('#right-sidebar-content').html(response.view);
                    }
                }
            });
        }); 
    
    $('body').on('click', '.ti-close', function () {
      $("body").removeClass("control-sidebar-slide-open");
    })    

        // Handle click on "Select all" control
        $('#example-select-all').on('click', function(){
            // Check/uncheck all checkboxes in the table
            $('input[type="checkbox"]').prop('checked', this.checked);
        });

        // Employee Response on schedule
        $('#changeMultipleStatus').on('click', function(){
            var msg;
            var status = $('#statusMultiple').val();
            var selectedArray = [];
            $('.checkboxes:checked').each(function(){
                selectedArray.push($(this).val());
            });
            if(selectedArray.length > 0){
            swal({
          title       : "@lang('errors.askForCandidateEmail')",
          text        : msg,
          type        : "info",
          showCancelButton  : true,
          confirmButtonColor  : "#0c19dd",
          confirmButtonText : "@lang('app.yes')",
          cancelButtonText  : "@lang('app.no')",
          closeOnConfirm    : true,
          closeOnCancel   : true
            }, function (isConfirm) {
                if (isConfirm) {
                    changeMultipleStatus(status , 'yes')
                }
                else{
                    changeMultipleStatus(status , 'no')
                }
            });
            }else{
                $.showToastr('Please check atleast one checkbox', "error" );
                // alert('Please check atleast one checkbox');
            }
        });

        function changeMultipleStatus(status, mailToCandidate){
            var selectedArray = [];
            $('.checkboxes:checked').each(function(){
                selectedArray.push($(this).val());
            });
            var token = "{{ csrf_token() }}";
            var url = "{{ route('product_company.interview-schedule.change-status-multiple') }}";
            $.easyAjax({
              url     : url,
              type    : "POST",
              data    : {'_token': token, "id": selectedArray, "status": status,'mailToCandidate': mailToCandidate},
              container : '#myTable',
              success   : function (response) {
                                  $.unblockUI();
                                  table._fnDraw();
                                }
                  });
        }

        // Edit Data
        $('body').on('click', '.edit-data', function(){
            var id    = $(this).data('row-id');
            var url   = "{{ route('product_company.interview-schedule.edit',':id') }}";
            url     = url.replace(':id', id);
            $('#modelHeading').html('Schedule');
            $('#scheduleDetailModal').modal('hide');
            $.ajaxModal('#scheduleDetailModal', url);
        });
        // View Data
        $('body').on('click', '.view-data', function(){
            var id    = $(this).data('row-id');
            var url   = "{{ route('product_company.interview-schedule.show',':id') }}?table=yes";
            url     = url.replace(':id', id);
            $('#modelHeading').html('Schedule');
            $('#scheduleDetailModal').modal('hide');
            $.ajaxModal('#scheduleDetailModal', url);
        });

        // Delete Data
    
        table.on('click', '.sa-params', function () 
    {
    
      
           var id = $(this).data('row-id');
        swal({
          title       : "@lang('errors.areYouSure')",
          text        : "@lang('errors.deleteWarning')",
          type        : "warning",
          showCancelButton  : true,
          confirmButtonColor  : "#DD6B55",
          confirmButtonText : "@lang('app.delete')",
          cancelButtonText  : "@lang('app.cancel')", 
          closeOnConfirm    : true,
          closeOnCancel   : true
            }, function(isConfirm){
                if (isConfirm) {
                    var url     = "{{ route('product_company.interview-schedule.destroy',':id') }}";
                    url       = url.replace(':id', id);
                    var token = "{{ csrf_token() }}";
                    $.easyAjax({
                        type: 'POST',
                        url: url,
                        data: {'_token': token, '_method': 'DELETE'},
                        success: function (response) {
                            if (response.status == "success") {
                                $.unblockUI();
                                table._fnDraw();
                            }
                        }
                    });
                }
            });
      
      
        });
    
     
        // Filte Toggle
        $('.toggle-filter').click(function () {
            $('#ticket-filters').toggle('slide');
        })

        // Apply Filter
        $('#apply-filters').click(function () {
            loadTable();
        });

        // Reset Filters
        $('#reset-filters').click(function () {
            $('#filter-form')[0].reset();
            $('#status').val('all');
            $('#status').select2();
            $('#applicationID').val('all');
            $('#applicationID').select2();
            loadTable();
        })

    
    
    

    function updateFeedbackStatus(feedback_id, interview_id, application_id)
    {
      let set_feedback_id     = feedback_id;
      let set_interview_id    = interview_id;
      let set_application_id    = application_id;
      
      if(set_feedback_id == 3)
      {
          if (typeof scheduleDate === "undefined") {
            scheduleDate = '';
          }
          var url = '{{ route('product_company.interview-schedule.create_status')}}?application_id=' + set_application_id+'&interview_id='+set_interview_id+'&feedback_id='+set_feedback_id ;
          $('#modelHeading_feedback').html('Schedule');
          $.ajaxModal('#scheduleDetailModal_feedback', url);
        
          //$('#feebdack').modal('show'); 
      }
      
      if(set_feedback_id == 4)
      {
      
        if (typeof scheduleDate === "undefined") {
          scheduleDate = '';
        }
        var url = '{{ route('product_company.interview-schedule.create_status')}}?application_id=' + set_application_id+'&interview_id='+set_interview_id+'&feedback_id='+set_feedback_id ;
        $('#modelHeading').html('Schedule');
        $.ajaxModal('#scheduleDetailModal', url);
      
      }
      
      if((set_feedback_id == 1) || (set_feedback_id == 2))
      {
          var clientId    = 0;
          var url       = "{{ route('product_company.interview-schedule.updateFeedbackStatus',':clientId') }}";
                    url         = url.replace(':clientId', clientId);
          var baseurl     = "{{ asset('assets/images/AjaxLoader.gif') }}"; 
                    var token       = "{{ csrf_token() }}";
          
          
             $.easyAjax({
                                type: 'POST',
                                url: url,
                                data: {'_token': token, '_method': 'POST', 'status_id' : status_id, 'interview_id':interview_id, 'job_application_id': job_application_id},
                    beforeSend: function() {
                                  $('.clientStatusLoader').html('<img src="'+baseurl+'" height="30px" width="30px">');
                    },
                                success: function (response) {                                              
                      //update
                    },
                    complete: function (data) {
                        //$('.clientStatusLoader').html('');
                     }
                          });
          
          
          
          
     }    

        /*$(this).removeClass('available-bg'); 40[-]15[-]25

        $(this).removeClass('not-available-bg'); 
        
        $(this).removeClass('cancel-bg'); 
        
        $(this).removeClass('not-intrested'); 
        
        $(this).removeClass('bg-whites'); 
        
        if(status_id == 1)
        {     
          $(this).addClass('available-bg');         
        }

        else if(status_id == 2)
        { 
          $(this).addClass('not-available-bg'); 
        }

        else if(status_id == 3)
        { 
          $(this).addClass('cancel-bg'); 
        }

        else if(status_id == 4)
        { 
          $(this).addClass('not-intrested'); 
        }

        else
        {
          $(this).addClass('bg-whites');  
        }*/
      
    }

  
    
    function updateAvailableStatus(status_id, interview_id, job_application_id, className)
    {         
      var clientId    = 0;
      var url       = "{{ route('product_company.interview-schedule.updateAvailableStatus',':clientId') }}";
      url         = url.replace(':clientId', clientId);
      var baseurl     = "{{ asset('assets/images/AjaxLoader.gif') }}";        
      var token       = "{{ csrf_token() }}";
      
    
         $.easyAjax({
                type: 'POST',
                url: url,
                data: {'_token': token, '_method': 'POST', 'status_id' : status_id, 'interview_id':interview_id, 'job_application_id': job_application_id},
                beforeSend: function() {
                              $('.clientStatusLoader').html('<img src="'+baseurl+'" height="30px" width="30px">');
                },
                success: function (response) {
         
                  
                },
                complete: function (data) {
                    //$('.clientStatusLoader').html('');
                 }
            });       
              
    }
    
     table.on('change', '.updateAvailableStatus', function () 
    {
    
      //this.value
      var status_id         = this.value;
      var interview_id      = $(this).attr("data-interview_id");
      var job_application_id    = $(this).attr("data-application");
      var currentClass      = $(this).attr('class');
            
      var clientId        = 0;
      var url           = "{{ route('product_company.interview-schedule.updateAvailableStatus',':clientId') }}";
      url             = url.replace(':clientId', clientId);     
      var baseurl         = "{{ asset('assets/images/AjaxLoader.gif') }}";      
      var token           = "{{ csrf_token() }}";
          
        
             $.easyAjax({
                                type: 'POST',
                                url: url,
                                data: {'_token': token, '_method': 'POST', 'status_id' : status_id, 'interview_id':interview_id, 'job_application_id': job_application_id},
                    beforeSend: function() {
                                  $('.clientStatusLoader').html('<img src="'+baseurl+'" height="30px" width="30px">');
                    },
                                success: function (response) {
                         
                      
                    },
                    complete: function (data) {
                        //$('.clientStatusLoader').html('');
                     }
                          });
        
        $(this).removeClass('available-bg'); 

        $(this).removeClass('not-available-bg'); 
        
        $(this).removeClass('cancel-bg'); 
        
        $(this).removeClass('not-intrested'); 
        
        $(this).removeClass('bg-whites'); 
        
        if(status_id == 1)
        {     
          $(this).addClass('available-bg');         
        }

        else if(status_id == 2)
        { 
         $(this).addClass('not-available-bg'); 
        }

        else if(status_id == 3)
        { 
          $(this).addClass('cancel-bg'); 
        }

        else if(status_id == 4)
        { 
          $(this).addClass('not-intrested'); 
        }

        else
        {
          $(this).addClass('bg-whites');  
        } 
    });
    
    
     table.on('change', '.updateFeedbackStatus', function () 
    {
    
      //this.value
      var status_id         = this.value;
      var interview_id      = $(this).attr("data-interview_id");
      var job_application_id    = $(this).attr("data-application");
      var currentClass      = $(this).attr('class');
      
      let set_feedback_id     = status_id;
      let set_interview_id    = interview_id;
      let set_application_id    = job_application_id;
      
      if(set_feedback_id == 3)
      {
          if (typeof scheduleDate === "undefined") {
            scheduleDate = '';
          }
          var url = '{{ route('product_company.interview-schedule.create_feedbackfrm')}}?application_id=' + set_application_id+'&interview_id='+set_interview_id+'&feedback_id='+set_feedback_id ;
          $('#modelHeading_feedback').html('Schedule');
          $.ajaxModal('#scheduleDetailModal_feedback', url);
          
        //$('#feebdack').modal('show'); 
      }
      
      if(set_feedback_id == 4)
      {
      
        
        if (typeof scheduleDate === "undefined") {
          scheduleDate = '';
        }
        var url = '{{ route('product_company.interview-schedule.create_status')}}?application_id=' + set_application_id+'&interview_id='+set_interview_id+'&feedback_id='+set_feedback_id ;
        $('#modelHeading').html('Schedule');
        $.ajaxModal('#scheduleDetailModal', url);
      
      }
      
          var clientId    = 0;
          var url       = "{{ route('product_company.interview-schedule.updateFeedbackStatus',':clientId') }}";
                    url         = url.replace(':clientId', clientId);

          var baseurl     = "{{ asset('assets/images/AjaxLoader.gif') }}"; 
        
                    var token       = "{{ csrf_token() }}";
          
        
            
        
        if((set_feedback_id== 0) || (set_feedback_id== 1) || (set_feedback_id== 2) )
        {
           
          
           $.easyAjax({
                                type: 'POST',
                                url: url,
                                data: {'_token': token, '_method': 'POST', 'status_id' : status_id, 'interview_id':interview_id, 'job_application_id': job_application_id},
                    beforeSend: function() {
                                  $('.clientStatusLoader').html('<img src="'+baseurl+'" height="30px" width="30px">');
                    },
                                success: function (response) {
                         
                      
                    },
                    complete: function (data) {
                        //$('.clientStatusLoader').html('');
                     }
                          });         
        }
        
        
        
        $(this).removeClass('pending-bg'); 
        $(this).removeClass('shortlisted-bg'); 
        $(this).removeClass('rejected-bg'); 
        $(this).removeClass('feedback-bg'); 
        $(this).removeClass('rescheduled-bg');        
        if(status_id == 1){     
          $(this).addClass('shortlisted-bg');         
        }else if(status_id == 2){ 
          $(this).addClass('rejected-bg'); 
        }else if(status_id == 3){ 
          $(this).addClass('feedback-bg'); 
        }else if(status_id == 4){ 
          $(this).addClass('rescheduled-bg'); 
        }else{
          $(this).addClass('pending-bg');   
        } 
    });
        
    
     function exportExcel()
      {
    
        var table         = $('#myTable').dataTable();
        var length        = table.fnGetData().length;
        
        var oSettings       = table.fnSettings();
        var start           = oSettings._iDisplayStart;
        
        var url         = '{{ route('product_company.interview-schedule.export', [':start' , ':length']) }}';
        url           = url.replace(':start', start);
        url           = url.replace(':length', length);
        window.location.href  = url;
      
           }
        

    </script>
@endpush