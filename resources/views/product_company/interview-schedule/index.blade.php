@extends('layouts.product_company_layout')
@push('head-script')
<link rel="stylesheet" href="{{ asset('assets/plugins/calendar/dist/fullcalendar.css') }}<?php echo TM_VERSION; ?>">
<link rel="stylesheet" href="//cdn.datatables.net/fixedheader/3.1.5/css/fixedHeader.bootstrap.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
<link rel="stylesheet" href="{{ asset('assets/node_modules/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css') }}<?php echo TM_VERSION; ?>">
<style>
.fc-toolbar button
{
  position:relative;
  text-transform:capitalize;
}
.content .card
{
  box-shadow:0 6px 15px rgb(36 37 38 / 8%);
  height:100%;
}
</style>
@endpush

@section('content')
<div class="row bg-white"> @section('create-button')
  <div class="row">
    <div class="col-md-6 text-left">
      <div class="dropdown dropdown-inline">
        <button class="dropbtn bg-light dropdown-toggle"> 10 </button>
        <div class="dropdown-content">
          <ul class="navi">
            <li class="navi-item"> <a href="#" class="navi-link custom-width"><span class="navi-text">20</span></a> </li>
            <li class="navi-item"> <a href="#" class="navi-link custom-width"><span class="navi-text">30</span></a> </li>
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
      @if(in_array("view_schedule", $userPermissions)) <a href="{{ route('product_company.interview-schedule.table-view') }}" class="btn btn-sm btn-light-danger p-1"> <i class="fa fa-table fa-sm"></i> @lang('app.tableView') </a> @endif    
      
      @if(in_array("add_schedule", $userPermissions)) <a href="#" data-toggle="modal" onclick="createSchedule()" class="btn btn-sm btn-light-warning p-1 waves-effect waves-light rounded-0"> <i class="fa fa-plus-circle fa-sm"></i> @lang('modules.interviewSchedule.addInterviewSchedule')</a> @endif </div>
  </div>
  @endsection
  <div class="col-md-8">
    <div class="card">
      <div class="card-body">
        <div id="calendar"></div>
      </div>
      <!-- /.card-body -->
    </div>
  </div>
  <div class="col-md-4">
    <div class="card">
      <div class="card-header d-flex p-0 ui-sortable-handle">
        <h3 class="card-title p-3"> <i class="fa fa-file"></i> @lang('modules.interviewSchedule.interviewSchedule') </h3>
      </div>
      <!-- /.card-header -->
      <div class="card-body"> @forelse($upComingSchedules as $key => $upComingSchedule)
        <div> @php
          $date = \Carbon\Carbon::createFromFormat('Y-m-d', $key);
          @endphp
          <h4>{{ $date->format('M d, Y') }}</h4>
          <ul class="scheduleul" style="padding: 0;">
            @forelse($upComingSchedule as $key => $dtData)
            <li class="deco" id="schedule-{{$dtData->id}}" onclick="getScheduleDetail(event, {{$dtData->id}}) " style="list-style: none;">
              <h5 class="text-muted mb-3" style="">{{ ucfirst($dtData->title) }}</h5>
              <div style="float: right"> @if($user->can('edit_schedule')) <span style="">
                <button onclick="editUpcomingSchedule(event, '{{ $dtData->id }}')" class="btn notify-button editSchedule" title="@lang('app.edit')"> <i class="fa fa-edit text-primary"></i> </button>
                </span> @endif
                @if($user->can('delete_schedule')) <span style="">
                <button data-schedule-id="{{ $dtData->id }}" class="btn notify-button deleteSchedule" title="@lang('app.delete')"> <i class="fa fa-trash text-danger"></i> </button>
                </span> @endif </div>
              <div class="clearfix" style=""></div>
              <div class="direct-chat-name" style="font-size: 13px">{{ ucfirst($dtData->full_name) }}
                &nbsp;<span class="direct-chat-timestamp" style="font-size: 13px">{{ $dtData->schedule_date->format('h:i a') }}</span></div>
              @if(in_array($user->id, $dtData->employee->pluck('user_id')->toArray()))
              @php
              $empData = $dtData->employeeData($user->id);
              @endphp
              
              @if($empData->user_accept_status == 'accept')
              <label class="badge badge-success float-right">@lang('app.accepted')</label>
              @elseif($empData->user_accept_status == 'refuse')
              <label class="badge badge-danger float-right">@lang('app.refused')</label>
              @else <span class="float-right">
              <button onclick="employeeResponse({{$empData->id}}, 'accept')" class="btn btn-sm btn-success notify-button responseButton">@lang('app.accept')</button>
              <button onclick="employeeResponse({{$empData->id}}, 'refuse')" class="btn btn-sm btn-danger notify-button responseButton">@lang('app.refuse')</button>
              </span> @endif
              @endif </li>
            @if($key != (count($upComingSchedule)-1))
            <hr>
            @endif
            @empty
            
            @endforelse
          </ul>
        </div>
        <hr>
        @empty
        <div>
          <p>@lang('messages.noUpcomingScheduleFund')</p>
        </div>
        @endforelse </div>
      <!-- /.card-body -->
    </div>
  </div>
</div>
{{--Ajax Modal Start for--}}
<div class="modal fade bs-modal-md in" id="scheduleDetailModal" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
  <div class="modal-dialog modal-lg" id="modal-data-application">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <span class="caption-subject font-red-sunglo bold uppercase" id="modelHeading"></span> </div>
      <div class="modal-body"> Loading... </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-success">Save changes</button>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
{{--Ajax Modal Ends--}}

    {{--Ajax Modal Start for--}}
<div class="modal fade bs-modal-md in" id="scheduleEditModal" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
  <div class="modal-dialog modal-lg" id="modal-data-application">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <span class="caption-subject font-red-sunglo bold uppercase" id="modelHeading"></span> </div>
      <div class="modal-body"> Loading... </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-success">Save changes</button>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
{{--Ajax Modal Ends--}}
@endsection

@push('footer-script')
<script src="{{ asset('assets/node_modules/select2/dist/js/select2.full.min.js') }}<?php echo TM_VERSION; ?>"type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/bootstrap-select/bootstrap-select.min.js') }}<?php echo TM_VERSION; ?>"type="text/javascript"></script>
<script src="//cdn.datatables.net/fixedheader/3.1.5/js/dataTables.fixedHeader.min.js"></script>
<script src="//cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
<script src="//cdn.datatables.net/responsive/2.2.3/js/responsive.bootstrap.min.js"></script>
<script src="{{ asset('assets/node_modules/moment/moment.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="{{ asset('assets/plugins/calendar/dist/fullcalendar.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/plugins/calendar/dist/jquery.fullcalendar.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/plugins/calendar/dist/locale-all.js') }}<?php echo TM_VERSION; ?>"></script>
<script>

        userCanAdd		= false;
        userCanEdit 	= false;
        @if($user->can('add_schedule'))
            userCanAdd 	= true;
        @endif
        @if($user->can('edit_schedule'))
            userCanEdit = true;
        @endif
        taskEvents 		= [
								@foreach($schedules as $schedule)
								{
									id: '{{ ucfirst($schedule->id) }}',
									title: '{{ $schedule->title }} on {{ $schedule->full_name }}',
									start: '{{ $schedule->schedule_date }}',
									end: '{{ $schedule->schedule_date }}',
								},
								@endforeach
        				 ];

</script>
<script src="{{ asset('js/schedule-calendar.js') }}<?php echo TM_VERSION; ?>"></script>
<script>
        // Schedule create modal view

        @if($user->can('edit_schedule'))
        // Schedule create modal view
        function editUpcomingSchedule(event, id) {
            if (!$(event.target).closest('.editSchedule').length) {
                return false;
            }
            var url = "{{ route('product_company.interview-schedule.edit',':id') }}";
            url = url.replace(':id', id);
            $('#modelHeading').html('Schedule');
            $('#scheduleEditModal').modal('hide');
            $.ajaxModal('#scheduleEditModal', url);
        }
        @endif

        // Update Schedule
        function reloadSchedule() {
            $.easyAjax({
                url: '{{route('product_company.interview-schedule.index')}}',
                container: '#updateSchedule',
                type: "GET",
                success: function (response) {
                    $('.upcomingdata').html(response.data);

                    taskEvents = [];
                    response.scheduleData.forEach(function(schedule){
                        const taskEvent = {
                            id: schedule.id,
                            title: schedule.title +' on '+  schedule.full_name ,
                            start: schedule.schedule_date ,
                            end: schedule.schedule_date,
                        };
                        taskEvents.push(taskEvent);
                    });

                    $.CalendarApp.reInit();
                }
            })
        }
        @if($user->can('delete_schedule'))
        $('body').on('click', '.deleteSchedule', function (event) {
            var id = $(this).data('schedule-id');
            if (!$(event.target).closest('.deleteSchedule').length) {
                return false;
            }
            swal({
                title: "@lang('errors.areYouSure')",
                text: "@lang('errors.deleteWarning')",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "@lang('app.delete')",
                cancelButtonText: "@lang('app.cancel')",
                closeOnConfirm: true,
                closeOnCancel: true
            }, function (isConfirm) {
                if (isConfirm) {

                    var url = "{{ route('product_company.interview-schedule.destroy',':id') }}";
                    url = url.replace(':id', id);

                    var token = "{{ csrf_token() }}";

                    $.easyAjax({
                        type: 'POST',
                        url: url,
                        data: {'_token': token, '_method': 'DELETE'},
                        success: function (response) {
                            if (response.status == "success") {
                                $.unblockUI();
                                $('#schedule-'+id).remove();
                                // Schedule create modal view
                                reloadSchedule();
                            }
                        }
                    });
                }
            });
        });
        @endif
        // Employee Response on schedule
        function employeeResponse(id, type) {
            var msg;

            if (type == 'accept') {
                msg = "@lang('errors.acceptSchedule')";
            } else {
                msg = "@lang('errors.refuseSchedule')";
            }
            swal({
                title: "@lang('errors.areYouSure')",
                text: msg,
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "@lang('app.yes')",
                cancelButtonText: "@lang('app.cancel')",
                closeOnConfirm: true,
                closeOnCancel: true
            }, function (isConfirm) {
                if (isConfirm) {
                    var url = "{{ route('product_company.interview-schedule.response',[':id',':type']) }}";
                    url = url.replace(':id', id);
                    url = url.replace(':type', type);

                    // update values for all tasks
                    $.easyAjax({
                        url: url,
                        type: 'GET',
                        success: function (response) {
                            if (response.status == 'success') {
                                window.location.reload();
                            }
                        }
                    });
                }
            });
        }

        // schedule detail
        var getScheduleDetail = function (event, id) {

            if ($(event.target).closest('.editSchedule, .deleteSchedule, .responseButton').length) {
                return false;
            }

            var url = '{{ route('product_company.interview-schedule.show', ':id')}}';
            url = url.replace(':id', id);

            $('#modelHeading').html('Schedule');
            $.ajaxModal('#scheduleDetailModal', url);
        }
        @if($user->can('add_schedule'))

        // Schedule create modal view
        function createSchedule(scheduleDate) {
            if (typeof scheduleDate === "undefined") {
                scheduleDate = '';
            }
            var url = '{{ route('product_company.interview-schedule.create')}}?date=' + scheduleDate;
            $('#modelHeading').html('Schedule');
            $.ajaxModal('#scheduleDetailModal', url);
        }
		
		
        @endif
		
		

        @if($user->can('add_schedule'))
            function addScheduleModal(start, end, allDay) {
            var scheduleDate;
            if (start) {
                var sd = new Date(start);
                var curr_date = sd.getDate();
                if (curr_date < 10) {
                    curr_date = '0' + curr_date;
                }
                var curr_month = sd.getMonth();
                curr_month = curr_month + 1;
                if (curr_month < 10) {
                    curr_month = '0' + curr_month;
                }
                var curr_year = sd.getFullYear();
                scheduleDate = curr_year + '-' + curr_month + '-' + curr_date;
            }

            createSchedule(scheduleDate);
        }
        @endif
</script>
@endpush