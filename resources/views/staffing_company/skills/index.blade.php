@extends('layouts.staffing_company_layout')

@push('head-script')
   	<link rel="stylesheet" href="//cdn.datatables.net/fixedheader/3.1.5/css/fixedHeader.bootstrap.min.css">
	<link rel="stylesheet" href="//cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
@endpush



@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if(in_array("add_skills", $userPermissions))
                    @section('create-button')
                    <div class="row">
                        <div class="col-md-6 text-left">                    
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
                        </div>
                        <div class="col-md-6 text-right">
                            <div class="input-group mr-1" style="width:fit-content;display:inline-flex;">
                                <input type="text" name="inputSearch" id="inputSearch" class="form-control" placeholder="Search..." style="padding: 0.3em .5em;">
                            </div>
                            <a href="{{ route('staffing_company.skills.create') }}" class="btn btn-light-warning btn-sm px-2 py-1">
                                <i class="fa fa-plus-square"></i>&nbsp;Add Skills
                            </a>
                            <a onclick="">
                                <button class="btn btn-light-primary  btn-sm ml-1 mr-2 px-2 py-1" type="button">
                                    <i class="fa fa-download fa-sm"></i>
                                </button>
                            </a>                             
                        </div>
                    </div>
                    @endsection
                    @endif
                    <div class="table-responsive m-t-40">
                        <table id="myTable" class="table table-bordered table-striped table-hover table-checkable">
                            <thead>
                                <tr class="table-bg-custom">
                                    <th>#</th>
                                    <th>@lang('app.name')</th>
                                    <th>@lang('menu.jobCategories')</th>
                                    <th>@lang('app.action')</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('footer-script')
   <!-- <script src="//cdn.datatables.net/fixedheader/3.1.5/js/dataTables.fixedHeader.min.js"></script>
    <script src="//cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
    <script src="//cdn.datatables.net/responsive/2.2.3/js/responsive.bootstrap.min.js"></script>-->

<script src="//cdn.datatables.net/fixedheader/3.1.5/js/dataTables.fixedHeader.min.js"></script>
<script src="//cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
<script src="//cdn.datatables.net/responsive/2.2.3/js/responsive.bootstrap.min.js"></script>
<script src="//cdn.datatables.net/colreorder/1.5.2/js/dataTables.colReorder.min.js"></script>
<script src="//cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
<script src="//cdn.datatables.net/buttons/1.6.1/js/buttons.colVis.min.js"></script>

    <script>
	

$(document).ready(function() {
	
	$('#inputSearch').keyup(function() {       
		//alert("call");
		table._fnDraw();   
	});      
	   
});    

   	
        var table = $('#myTable').dataTable({
           
		   
				//responsive: true,  
				// processing: true,
				//serverSide: true,         
			
				/*responsive		: true,
				processing		: true,
				destroy			: true,
				stateSave		: true,
				colReorder		: true,
				sScrollXInner	: "110%",  
				bJQueryUI		: true,
				
				//"scrollX"		: true,
				
				"bFilter"		: false,
				"bInfo"			: false,
				"bLengthChange"	: true,

				oLanguage		: {
									sLengthMenu: "_MENU_",
								   },*/
								   
			
			
		responsive      : true,
        processing      : true,
        serverSide      : true,
        "bFilter"       : false,
        stateSave       : true,
        "bInfo"         : true,
        "bLengthChange" : true,
        oLanguage:
        {
          sLengthMenu: "_MENU_",
        },

        "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]], 
        "pageLength": 25,
			
			
           // ajax: '{!! route('staffing_company.skills.data') !!}',  
			
			ajax: {'url' : '{!! route('staffing_company.skills.data') !!}',  
			"data": function ( d ) 
				{
					d.search = $('#inputSearch').val()
				}
			},  
	
            language: languageOptions(),
            "fnDrawCallback": function( oSettings ) {
                $("body").tooltip({ 
                    selector: '[data-toggle="tooltip"]'
                });
            },

        "columnDefs": [ 

            { "width": "3%", "targets": 0 },
            { "width": "42%", "targets": 1 },
            { "width": "45%", "targets": 2 },
            { "width": "10%", "targets": 3 }   
            ],



        columns: [
            { data: 'DT_Row_Index', name:'id'},
                { data: 'name', name: 'name' },
                { data: 'category_id', name: 'category_id' },
                { data: 'action', name: 'action' }
        ]
    });
        new $.fn.dataTable.FixedHeader( table );

        $('body').on('click', '.sa-params', function(){
            var id = $(this).data('row-id');
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

                    var url = "{{ route('staffing_company.skills.destroy',':id') }}";
                    url = url.replace(':id', id);

                    var token = "{{ csrf_token() }}";

                    $.easyAjax({
                        type: 'POST',
                        url: url,
                        data: {'_token': token, '_method': 'DELETE'},
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

    </script>
@endpush