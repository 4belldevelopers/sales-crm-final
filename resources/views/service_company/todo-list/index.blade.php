@extends('layouts.service_company_layout')

@push('head-script')
<link rel="stylesheet" href="//cdn.datatables.net/fixedheader/3.1.5/css/fixedHeader.bootstrap.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
<style>
/*select
{
    padding:.1em .1em!important;
}*/
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
                  <li class="navi-item"> <a href="#" class="navi-link custom-width"><span class="navi-text">20</span></a> </li>
                  <li class="navi-item"> <a href="#" class="navi-link custom-width"><span class="navi-text">30</span></a> </li>
                </ul>
              </div>
            </div>
          </div>
          <div class="col-md-6 text-right">
            <div class="input-group mr-2" style="width:fit-content;display:inline-flex;">
              <input type="text" name="inputSearch" id="inputSearch" class="form-control" placeholder="Search..." style="padding: 0.3em .5em;">
            </div>
            <!-- <a href="{{ route('service_company.clients.create') }}" class="btn btn-light-warning btn-sm mr-1 px-2 py-1"><i class="fa fa-pencil fa-sm"></i>&nbsp;New Client</a> -->
            <a href="javascript:showNewTodoForm();" class="btn btn-light-warning btn-sm mr-1 px-2 py-1"> <i class="fa fa-pencil fa-sm"></i>&nbsp;@lang('app.createNew')</a> <a onclick="exportJobApplication()">
            <button class="btn btn-light-primary  btn-sm ml-1 mr-2 px-2 py-1" type="button"> <i class="fa fa-download fa-sm"></i>&nbsp; </button>
            </a> </div>
        </div>
        @endsection
        <table id="todo-table" class="table table-bordered table-striped table-hover table-checkable w-100">
          <thead>
            <tr class="table-bg-custom">
              <th style="width:5%;">S. No.</th>
              <th style="width:45%;">@lang('app.title')</th>
              <th style="width:40;">@lang('app.status')</th>
              <th style="width:10%;">@lang('app.action')</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection

@push('footer-script')
<script src="{{ asset('js/dataTables.fixedHeader.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/dataTables.responsive.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/responsive.bootstrap.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script>
        function showNewTodoForm() {
            let url = "{{ route('service_company.todo-items.create') }}"

            $.ajaxModal('#application-md-modal', url);
        }

        function showUpdateTodoForm(id) {
            let url = "{{ route('service_company.todo-items.edit', ':id') }}"
            url = url.replace(':id', id);

            $.ajaxModal('#application-md-modal', url);
        }

        function updateTodoStatus(id) {
            const title = $('#status-'+id).data('title');
            let url = "{{route('service_company.todo-items.update', ':id')}}"
            url = url.replace(':id', id);

            let data = {
                _token: '{{ csrf_token() }}',
                _method: 'PUT',
                id: id,
                status: $('#status-'+id).val(),
                title: title
            }

            $.easyAjax({
                url: url,
                container: '#todo-table',
                type: "POST",
                data: data,
                success: function (response) {
                    if(response.status == 'success'){
                        $.unblockUI();
                        todoTable._fnDraw();
                    }
                }
            })
        }

        function deleteTodoItem(id) {
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
            }, function(isConfirm){
                if (isConfirm) {
                    let url = "{{ route('service_company.todo-items.destroy', ':id') }}";
                    url = url.replace(':id', id);

                    let data = {
                        _token: '{{ csrf_token() }}',
                        _method: 'DELETE'
                    }

                    $.easyAjax({
                        url,
                        data,
                        type: 'POST',
                        container: '#roleMemberTable',
                        success: function (response) {
                            if (response.status == 'success') {
                                $.unblockUI();
                                todoTable._fnDraw();
                            }
                        }
                    })
                }
            });
        }

        var todoTable = $('#todo-table').dataTable({
            responsive: true,
            processing: true,
            serverSide: true,

            "bFilter": false,
            stateSave   : true,
            "bInfo": false,
           "bLengthChange": false,
           oLanguage: {
               sLengthMenu: "_MENU_",
            },

            ajax: '{!! route('service_company.todo-items.index') !!}',
            language: {
                "url": "<?php echo __("app.datatable") ?>"
            },
            "fnDrawCallback": function( oSettings ) {
                $("body").tooltip({
                    selector: '[data-toggle="tooltip"]'
                });
            },
            order: [[0, 'DESC']],
            columns: [
                { data: 'DT_Row_Index', name: 'DT_Row_Index', orderable: false, searchable: false},
                { data: 'title', name: 'title' },
                { data: 'status', name: 'status' },
                { data: 'action', name: 'action'}
            ]
        });
        new $.fn.dataTable.FixedHeader( todoTable );

        $('body').on('click', '#create-todo-item', function () {

            $.easyAjax({
                url: "{{route('service_company.todo-items.store')}}",
                container: '#createTodoItem',
                type: "POST",
                data: $('#createTodoItem').serialize(),
                success: function (response) {
                    if(response.status == 'success'){
                        $.unblockUI();
                        todoTable._fnDraw();

                        $('#application-md-modal').modal('hide');
                    }
                }
            })
        });


        $('body').on('click', '#update-todo-item', function () {
            const id = $(this).data('id');
            let url = "{{route('service_company.todo-items.update', ':id')}}"
            url = url.replace(':id', id);

            $.easyAjax({
                url: url,
                container: '#editTodoItem',
                type: "POST",
                data: $('#editTodoItem').serialize(),
                success: function (response) {
                    if(response.status == 'success'){
                        $.unblockUI();
                        todoTable._fnDraw();

                        $('#application-md-modal').modal('hide');
                    }
                }
            })
        });
    </script>
@endpush 