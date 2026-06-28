@extends('layouts.product_company_layout')

@push('head-script')
<link rel="stylesheet" href="//cdn.datatables.net/fixedheader/3.1.5/css/fixedHeader.bootstrap.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
@endpush
<style>
.tooltip
{
  position: relative;
  display: inline-block;
  border-bottom: 1px dotted black;
}

.tooltip .tooltiptext
{
  visibility: hidden;
  width: 250px;
  background-color: #0056b3;
  color: #fff;
  text-align: left;
  border-radius: 6px;
  padding: 10px;

  /*Position the tooltip*/
  position: absolute;
  z-index: 5;
  min-height:150px;
}
.tooltip:hover .tooltiptext
{
  visibility: visible;
}
#btnFiterSubmitSearch
{
    padding: .5em .20em!important;
}

table.table-bordered.dataTable td:first-child, table.table-bordered.dataTable td:first-child
{
    width: 5%;
}

table.table-bordered.dataTable td:last-child, table.table-bordered.dataTable td:last-child
{
    text-align: left!important;
}
.table td, .table tr
{
    height: 100%;
    line-height: 30px;
}
.table th
{
    height: 100%;
    line-height: 20px;
}
</style>

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                @section('create-button') 
                    <div class="row">
                        <div class="col-md-6">                    
                            <div class="dropdown dropdown-inline">
                                <button class="dropbtn bg-light dropdown-toggle dropbtn-border">
                                    10
                                </button>
                                <div class="dropdown-content">
                                  <ul class="navi">
                                    <li class="navi-item">
                                        <a href="#" class="navi-link custom-width">
                                            <span class="navi-text">20</span>
                                        </a>
                                    </li>
                                    <li class="navi-item">
                                        <a href="#" class="navi-link custom-width">
                                            <span class="navi-text">30</span>
                                        </a>
                                    </li>
                                  </ul>
                                </div>
                            </div>
                            <!-- Page Title -->
                            <span class="card-title pt-2 pb-2">&nbsp;Interview Process</span>
                            <!-- Page Title End -->
                        </div>
                        <div class="col-md-6 text-right">
                            <div class="input-group mr-2" style="width:fit-content;display:inline-flex;">
                                <input type="text" name="inputSearch" id="inputSearch" class="form-control" placeholder="Search..." style="padding: 0.3em .5em;">
                            </div>
                            <a href="{{ route('product_company.candidateStatus.create') }}" class="btn btn-light-warning btn-sm px-2 py-1"> 
                                <i class="fa fa-plus-square"></i>&nbsp;Add Interview Process
                            </a>
                            <a onclick="">
                                <button class="btn btn-light-primary  btn-sm ml-1 mr-2 px-2 py-1" type="button">
                                    <i class="fa fa-download fa-sm"></i>
                                </button>
                            </a>                             
                        </div>
                    </div>
                @endsection
            <div class="table-responsive">
    			<table id="myTable" class="table table-bordered table-striped table-hover table-checkable">
    				<thead>
    					<tr class="table-bg-custom">
        					<th>No.</th>
        					<th>Parent Name</th>
        					<th>Child Name</th>
    					</tr>
    				</thead>			
    	
                    <?php 
                	$z=1; 
                	if(!empty($master_status)){
                	foreach($master_status as $mlist){		
                    ?>
    					<tr id="row_<?php echo $mlist->id; ?>">
    						<td><?php echo $z; ?>.</td>
    						<td style="font-weight:bold"><?php echo $mlist->name ?></td>
    						<td></td>
    					</tr>					
    					
                        <?php 
            			$candidate_status_Arr 	= DB::table('candidate_status')
            								->select('*')
            								->where('candidate_status.parent', '=', $mlist->id)
            								//->where('candidate_status.company_id', '=', $mlist->id)
            								->where('candidate_status.company_id', user()->company_id)
            								->orderBy('priority', 'ASC')
            								->get();
            					
                                			$child    = 1;					
                                			if(!empty($candidate_status_Arr)){
                                				foreach($candidate_status_Arr as $csval){
                                            ?>					
                            <tr  id="row_'.$cval->id.'">
                        		<td><?php echo $z; ?></td>
                        		<td style="margin-left:20px">Label = <?php echo @$child; ?></td>
                        		<td style="margin-left:20px"><?php echo @$mlist->name ?> &nbsp; >> &nbsp;<?php echo @$csval->name ?></td>
                            </tr>					
                        					
                        <?php 
                        		$child = $child + 1; 	
                         }}
                         
                        ?>					
                        <?php 
                        		$z = $z+1; 
                        		}
                        	}	
                        ?>
    			</table>
            </div>
        </div>
    </div>
</div>
</div>
@endsection

@push('footer-script')
<script src="//cdn.datatables.net/fixedheader/3.1.5/js/dataTables.fixedHeader.min.js"></script>
<script src="//cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
<script src="//cdn.datatables.net/responsive/2.2.3/js/responsive.bootstrap.min.js"></script>
<script src="//cdn.datatables.net/colreorder/1.5.2/js/dataTables.colReorder.min.js"></script>
<script src="//cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
<script src="//cdn.datatables.net/buttons/1.6.1/js/buttons.colVis.min.js"></script>



    <script>

    //Add New Script
    var table = $('#myTable').dataTable({
        responsive: true,
        processing: false,
        serverSide: true,
        ajax: '{!! route('product_company.candidateStatus.data') !!}',
        language: languageOptions(),
        "fnDrawCallback": function( oSettings ) {
            $("body").tooltip({
                selector: '[data-toggle="tooltip"]'
            });
        },

    "columnDefs": [

        { "width": "2%", "targets": 0 },
        { "width": "20%", "targets": 1 },
        { "width": "78%", "targets": 2 },
        { "width": "0%", "targets": 3 }
        ]
    });
    //New Script End

$('body').on('click', '.sa-params', function()
{
    var id = $(this).data('row-id');
    swal({
        title				: "@lang('errors.areYouSure')",
        text				: "@lang('errors.deleteWarning')",
        type				: "warning",
        showCancelButton	: true,
        confirmButtonColor	: "#DD6B55",
        confirmButtonText	: "@lang('app.delete')",
        cancelButtonText	: "@lang('app.cancel')",
        closeOnConfirm		: true,
        closeOnCancel		: true
    }, function(isConfirm){
        if (isConfirm) {

            var url = "{{ route('product_company.candidateStatus.destroy',':id') }}";
            url = url.replace(':id', id);

            var token = "{{ csrf_token() }}";

            $.easyAjax({
                type: 'POST',
                url: url,
                data: {'_token': token, '_method': 'DELETE'},
                success: function (response) {
                     if (response.status == "success") {
                        $.unblockUI();
                        //swal("Deleted!", response.message, "success");
                        //table._fnDraw();
						$('#row_'+id).hide();
                    }
                }
            });
        }
    });
});

$('body').on('click', '.client-user-delete', function(){
    var id = $(this).data('row-id');
	
    swal({
        title				: "@lang('errors.areYouSure')",
        text				: "@lang('errors.deleteWarning')",
        type				: "warning",
        showCancelButton	: true,
        confirmButtonColor	: "#DD6B55",
        confirmButtonText	: "@lang('app.delete')",
        cancelButtonText	: "@lang('app.cancel')",
        closeOnConfirm		: true,
        closeOnCancel		: true
    }, function(isConfirm){
        if (isConfirm) {

            var url = "{{ route('product_company.clients.deleteUser') }}";
          //  url = url.replace(':id', id);

            var token = "{{ csrf_token() }}";

            $.easyAjax({
                type: 'POST',
                url: url,
                data: {'_token': token, 'id':id},
                success: function (response) {
                    if (response.status == "success") {
                        $.unblockUI();
//                                    swal("Deleted!", response.message, "success");
                        table._fnDraw();
                    }
                }
            });
        }
    });
});		

$('body').on('click', '.client-location-delete', function(){
    var id = $(this).data('row-id');
	
    swal({
        title				: "@lang('errors.areYouSure')",
        text				: "@lang('errors.deleteWarning')",
        type				: "warning",
        showCancelButton	: true,
        confirmButtonColor	: "#DD6B55",
        confirmButtonText	: "@lang('app.delete')",
        cancelButtonText	: "@lang('app.cancel')",
        closeOnConfirm		: true,
        closeOnCancel		: true
    }, function(isConfirm){
        if (isConfirm) {

            var url = "{{ route('product_company.clients.deleteAddress') }}";
          //  url = url.replace(':id', id);

            var token = "{{ csrf_token() }}";

            $.easyAjax({
                type: 'POST',
                url: url,
                data: {'_token': token, 'id':id},
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
</script>
@endpush 