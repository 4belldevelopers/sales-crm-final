@extends('layouts.product_company_layout')

@push('head-script')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<link rel="stylesheet" href="//cdn.datatables.net/fixedheader/3.1.5/css/fixedHeader.bootstrap.min.css">
<!--<link href="{{ asset('css/fixedHeader.bootstrap.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('css/responsive.bootstrap.min.css') }}" rel="stylesheet" type="text/css">-->
<!-- Responsive CSS -->
<link rel="stylesheet" href="//cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
<!-- Responsive CSS End -->
<link href="{{ asset('assets/dist/css/adminlte.min.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet" type="text/css">
<link href="{{ asset('css/custom.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet" type="text/css">
<link href="{{ asset('assets/icons/themify-icons/themify-icons.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet" type="text/css">
<script>

$(document).on('click', function (e) {
		var flag	=	0;
		if ($('.control-sidebar-slide-open').is(':visible')){
			if ($(e.target).closest("#right-sidebar-content").length === 0) {
					flag	=	1;
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

.dataTables_wrapper
{
  padding-left:1em!important;
}  
.mb-20
{
  margin-bottom: 20px
}
.text-success a
{
  color:#007bff!important;
  background-color:#e1f0ff;
  font-weight:500;
  /*padding:.1em 0em;*/
  border-radius:3px;
  display: block;
  text-align: center;
  margin: 0.2em 0em;
}
.text-success a:hover,
.text-danger a:focus
{
  color:#ffffff!important;
  background-color:#007bff;
}

/*Badge*/
.badge-secondary
{
  color:#343a40!important;
  background-color:#f2f2f2;
  text-align: center;
}
a:hover > .badge-secondary
{
  color:#ffffff!important;
  background-color:#343a40;  
  font-weight:600;
}
.badge-primary
{
  color:#007bff!important;
  background-color:#e1f0ff;
  text-align: center;
}
a:hover > .badge-primary
{
  color:#ffffff!important;
  background-color:#007bff;  
  font-weight:600;
}
.badge-warning
{
  color:#ffa800!important;
  background-color:#fff4de;
  text-align: center;
}
a:hover > .badge-warning
{
  color:#ffffff!important;
  background-color:#ffa800;  
  font-weight:600;
}
.badge-danger
{
  color:#f64e60!important;
  background-color:#ffe2e5;
  text-align: center;
}
a:hover > .badge-danger
{
  color:#ffffff!important;
  background-color:#f64e60;
  font-weight:600;
}
/*Badge End*/
.text-warning a
{
  color: #ffa800!important;
  background-color: #fff4de!important;
  font-weight:500!important;
  display:block;
  text-align:center;
  border-radius:3px;
  /*padding:.1em 0em;*/
  margin: 0.2em 0em;
}
.text-warning a:hover,
.text-danger a:focus
{
  background-color:#ffa800!important;
  color:#fff!important;
}
.text-danger a
{
  color: #f64e60!important;
  background-color: #f9e2e5!important;
  font-weight:500!important;
  display:block;
  text-align:center;
  border-radius:3px;
  /*padding:.1em 0em;*/
  margin: 0.2em 0em;
}
.text-danger a:hover,
.text-danger a:focus
{
  color: #fff!important;
  background-color:#ff001b!important;
}
.bg-light-primary a
{
  color: #3699ff;
  background-color:#e1f0ff;
}
.text-black a
{
  color:#3a3a3a!important;
  font-weight:bold!important;
}
.offcanvas.offcanvas-right
{
  height:100%;
}
body
{
  margin: 0!important;
  padding:0!important;
}
.dropdown-toggle.btn:after, .dropdown-toggle.nav-link:after
{
  content:""!important;margin:0;
}
.dropbtn
{
  background-color:#f2f2f2;
  border:none;
}
.dropdown
{
  position:relative;
  display:inline-block;
}
.dropdown-content
{
  display:none;
  position:absolute;
  background-color:#FFFFFF;
  min-width:200px;
  box-shadow:0px 8px 16px 0px rgba(0,0,0,0.2);z-index: 1;
}
.dropdown-content a
{
  color:black;
  padding:0.75rem 0.0rem;
  text-decoration:none;
  display:block;
  text-align:left;
}
.dropdown-content a:hover
{
  background-color:#187de4;
}
.dropdown:hover .dropdown-content
{
  display:block;
}
.dropdown-content1
{
  min-width: 100px!important;
}
.card.card-custom > .card-body
{
  padding:0rem .9rem 2rem .3rem;
}
table
{
  margin:0 auto;
  width:100%;
  clear:both;
  /*border-collapse:collapse;*/
  /*table-layout:fixed;*/
}

/*Table Indexing Dropdown*/
#myTable_length label
{
  position: absolute;
  margin-top: -8.5%;
/*  position: fixed;
  top: 10.5%;*/
}
/*Media Query*/
@media only screen and (min-width: 1440px) and (max-width: 2560px)
{
    #myTable_length label
    {
        margin-top: -4%;
    }
}
@media only screen and (min-width: 1080px) and (max-width: 1920px)
{
    #myTable_length label
    {
        margin-top: -5.5%;
    }
}
@media only screen and (min-width: 1050px) and (max-width: 1680px)
{
    #myTable_length label
    {
        margin-top: -6.1%;
    }
}
@media only screen and (min-width: 864px) and (max-width: 1536px)
{
    #myTable_length label
    {
        margin-top: -6.9%;
    }
}
@media only screen and (min-width: 900px) and (max-width: 1440px)
{
    #myTable_length label
    {
        margin-top: -7.3%;
    }
}
@media only screen and (min-width: 900px) and (max-width: 1600px)
{
    #myTable_length label
    {
        margin-top: -6.5%;
    }
}
@media only screen and (min-width: 800px) and (max-width: 1280px)
{
    #myTable_length label
    {
        margin-top: -8.9%;
    }
}
@media only screen and (min-width: 768px) and (max-width: 1366px)
{
    #myTable_length label
    {
        margin-top: -7.9%;
    }
}
/*Media Query End*/
select.form-control:not([size]):not([multiple])
{
  /*padding: 0em 1.2em 0em .5em;   */
  padding: 0em .9em 0em .4em; 
  -moz-padding-left: 0em .9em 0em 0.5em!important; 
  -webkit-padding-left: 0em .9em 0em 0.5em!important;
}

div.dataTables_wrapper div.dataTables_length select
{
  /*width: auto;*/
  width: unset;
  display: inline-block;
}
/*select.form-control:not([size]):not([multiple])
{
  padding: 3px 0px 3px 10px;
  -moz-padding-left: 3px 0px 3px 10px!important;
  -webkit-padding-left: 3px 0px 3px 10px!important;
}*/
/*Table Indexing Dropdown End*/


/*th.dt-complex-header
{
  border-top-width: 0 !important;
}
thead, tbody tr
{
  display: table;
  width: 50%; 
  table-layout: fixed;  
} */
/*table.dataTable tbody th,
table.dataTable tbody td
{
  text-overflow: ellipsis;
  overflow: hidden;
  white-space: normal;
}*/

</style>
@endpush

@if(in_array("add_jobs", $userPermissions))
  @section('create-button')
<div class="row align-items-center">
  <div class="col-md-7 text-left d-flex">
    <!-- invisible -->
    <div class="dropdown dropdown-inline invisible">
      <button class="dropbtn bg-light dropdown-toggle dropbtn-border">10</button>
      <div class="dropdown-content">
        <ul class="navi">
          <li class="navi-item"> <a href="Javascript:void(0);" class="navi-link custom-width"> <span class="navi-text">20</span> </a> </li>
          <li class="navi-item"> <a href="Javascript:void(0);" class="navi-link custom-width"> <span class="navi-text">30</span> </a> </li>
        </ul>
      </div>
    </div>
    <div class="all-positions pl-3 padding-left-browser">
      <h3 class="card-label mb-0" style="font-size: 14px;"> <span class="d-block text-dark font-size-sm mt-custom">
        <div class="d-flex align-items-center">
        <a href="#" class=""></a> <a href="Javascript:void(0);" class="text-secondary mr-1">All Position&nbsp; <span class="badge badge-secondary badge-sm">{{ number_format($totalJobs) }}</span> </a> <a href="Javascript:void(0);" class="text-primary mr-1">&nbsp;Active <span class="badge badge-primary badge-sm">{{ number_format($activeJobs) }}</span> </a> <a href="Javascript:void(0);" class="text-warning mr-1">&nbsp;Hold <span class="badge badge-warning badge-sm">{{ number_format($holdJobs) }}</span> </a> <a href="Javascript:void(0);" class="text-danger mr-1">&nbsp;Closed <span class="badge badge-danger badge-sm">{{ number_format($inactiveJobs) }}</span> </a>
        <!--end::Button-->
        </span>
        <!-- All Jobs -->
      </h3>
    </div>
  </div>
  <div class="col-md-5 text-right searchbar-parent">
    <div class="input-group mr-2 searchbar-child" style="">
      <input type="text" name="inputSearch" id="inputSearch" class="form-control" placeholder="Search..." style="padding: 0.3em .5em;">
    </div>
    <a href="{{ route('product_company.jobs.create') }}" class="btn btn-light-warning btn-sm mr-1 px-2 py-1"> <i class="fa fa-plus-square fa-sm"></i>&nbsp;Add Requisition </a>
    <!-- <div class="dropdown dropdown-inline mr-1">
            <button class="dropbtn btn btn-light-danger btn-sm dropdown-toggle p-1">
              <i class="fa fa-user fa-sm"></i>
                Upload Profile
            </button>
            <div class="dropdown-content">
              <ul class="navi flex-column navi-hover py-2">
                <li class="navi-item">
                  <a href="{{ route('product_company.job-applications.create') }}" class="navi-link">
                    <span class="navi-icon"><i class="fa fa-user"></i></span>
                    <span class="navi-text">Single Profile</span>
                  </a>
                </li>
                <li class="navi-item">
                  <a href="{{ route('product_company.bulkupload.index') }}" class="navi-link">
                    <span class="navi-icon"><i class="fa fa-users"></i></span>
                    <span class="navi-text">Multiple Profile</span>
                  </a>
                </li>
              </ul>
            </div>
          </div> -->
    <a href="{{ route('product_company.job-applications.create') }}" class="btn btn-light-danger btn-sm mr-1 px-2 py-1"> <i class="fa fa-upload"></i>&nbsp;Upload Profile</a> <a onclick="exportExcel()">
    <button class="btn btn-light-primary btn-sm mr-1 px-2 py-1" type="button"> <i class="fa fa-download fa-sm"></i> </button>
    </a> </div>
</div>
@endsection
@endif

@section('content')
<div class="modal fade bs-modal-sm in" id="scheduleDetailModal_priority" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md" id="modal-data-application">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <span class="caption-subject font-red-sunglo bold uppercase" id="modelHeading_priority"></span></div>
      <div class="modal-body">Loading... </div>
      <div class="modal-footer">
        <button type="button" class="btn default" data-dismiss="modal">Close</button>
        <button type="button" class="btn blue">Update</button>
      </div>
    </div>
  </div>
</div>
<div class="d-flex flex-column-fluid">
  <div class="container-fluid px-0">
    <div class="card card-custom">
      <div class="card-header flex-wrap border-0 p-0"> </div>
      <div class="card-body">
        <table id="myTable" class="table table-bordered table-striped table-hover table-checkable">
          <thead>
            <tr class="table-bg-custom">
              <th class="table_id">No.</th>
              <!-- <th><i class="fa fa-clock-o fa-sm text-dark"></i></th> -->
              <th>Date</th>
              <th>Status</th>
              <th>Job Title</th>
              <th>Hiring</th>
              <th> <div class="tooltip font-weight-700">JD <span class="tooltiptext font-weight-500">Job Description</span> </div></th>
              <th> <div class="tooltip font-weight-700">Exp <span class="tooltiptext font-weight-500">Experience</span> </div></th>
              <th> <div class="tooltip font-weight-700">No <span class="tooltiptext font-weight-500">No. of Openings</span> </div></th>
              <th> <div class="tooltip font-weight-700">Loc <span class="tooltiptext font-weight-500">Location</span> </div></th>
              <th> <div class="tooltip font-weight-700">REP <span class="tooltiptext font-weight-500">Representative</span> </div></th>
              <th> <div class="tooltip font-weight-700">HR <span class="tooltiptext font-weight-500">Human Resources</span> </div></th>
              <th class="custom-bdr-right"> <div class="tooltip font-weight-700">TOT <span class="tooltiptext font-weight-500">Total</span> </div></th>
              <th> <div class="tooltip font-weight-700">SOU <span class="tooltiptext font-weight-500">Sourcing</span> </div></th>
              <th> <div class="tooltip font-weight-700">SCR <span class="tooltiptext font-weight-500">Screening</span> </div></th>
              <th> <div class="tooltip font-weight-700">INT <span class="tooltiptext font-weight-500">Interview</span> </div></th>
              <th> <div class="tooltip font-weight-700">NEO <span class="tooltiptext font-weight-500">Negotation</span> </div></th>
              <th> <div class="tooltip font-weight-700">OFF <span class="tooltiptext font-weight-500">Offer</span> </div></th>
              <th> <div class="tooltip font-weight-700">JOI <span class="tooltiptext font-weight-500">Joining</span> </div></th>
              <th> <div class="tooltip font-weight-700">REJ <span class="tooltiptext font-weight-500">Rejected</span> </div></th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
</div>
<div class="modal fade bs-modal-sm in" id="scheduleDetailModalStatus" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md" id="modal-data-application">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <span class="caption-subject font-red-sunglo bold uppercase" id="modelHeadingStatus"></span> </div>
      <div class="modal-body"> Loading... </div>
      <div class="modal-footer">
        <button type="button" class="btn default" data-dismiss="modal">Close</button>
        <button type="button" class="btn blue">Save changes</button>
      </div>
    </div>
  </div>
</div>
<!-- Modal start -->
<div class="modal fade" id="exampleModalSizeLg" tabindex="-1" role="dialog" aria-labelledby="exampleModalSizeLg" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document"> ... </div>
</div>
<!-- Modal End -->
<div class="modal fade right" id="sidebar-right" tabindex="-1" role="dialog" aria-labelledby="exampleModalSizeLg">
  <div class="modal-dialog modal-lg" role="document" >
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Right Sidebar</h4>
      </div>
      <div class="modal-body">
        <ul class="nav nav-pills nav-stacked text-center lead">
          <li role="presentation" class="active"> <a href="#home">Home</a> </li>
          <li role="presentation" class="dropdown"> <a href="#" class="dropdown-toggle" id="myTabDrop1" data-toggle="dropdown" aria-controls="myTabDrop1-contents" aria-expanded="false">Services <span class="fa fa-fw fa-chevron-down"></span> </a>
            <ul class="dropdown-menu btn-block">
              <li> <a href="#design" role="tab">Design</a> </li>
              <li> <a href="#develop" role="tab">Develop</a> </li>
              <li> <a href="#support" role="tab">Support</a> </li>
            </ul>
          </li>
          <li role="presentation"> <a href="#about">About</a> </li>
        </ul>
        <hr>
        <div class="form-group hide">
          <div class="input-group">
            <input class="form-control" placeholder="Search" name="customSearch" id="customSearch" class="customSearch">
            <span class="input-group-btn">
            <button class="btn btn-default" type="button"> <i class="fa fa-fw fa-search"></i> </button>
            </span> </div>
        </div>
        <div class="form-group has-feedback">
          <input type="text" class="form-control" id="search-right" name="search-right" class="search-right" placeholder="Search">
          <span class="glyphicon glyphicon-search form-control-feedback" aria-hidden="true"></span> </div>
        <hr>
        <div class="btn-group-vertical center-block"> <a class="btn btn-instagram btn-lg" href="https://www.instagram.com/jorenerene/" target="_blank"> <i class="fa fa-fw fa-instagram"></i> </a> <a class="btn btn-twitter btn-lg" href="https://www.twitter.com/jorenerene/" target="_blank"> <i class="fa fa-fw fa-twitter"></i> </a> <a class="btn btn-github btn-lg" href="https://www.github.com/jorenerene/" target="_blank"> <i class="fa fa-fw fa-github"></i> </a> </div>
      </div>
    </div>
  </div>
</div>
<div class="modal fade bs-modal-lg in" id="JobDetailModalStatus" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" id="modal-data-application" style="margin:.5rem auto;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <span class="caption-subject font-red-sunglo bold uppercase" id="JobDetailModalStatusData"></span></div>
    </div>
  </div>
</div>
<div class="modal fade bs-modal-md in" id="scheduleDetailModal_vis" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md" id="modal-data-application" style="margin:.5rem auto;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <span class="caption-subject font-red-sunglo bold uppercase" id="modelHeading_vis"></span></div>
    </div>
  </div>
</div>
@endsection
@push('footer-script')
<script src="{{ asset('js/dataTables.fixedHeader.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/dataTables.responsive.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/responsive.bootstrap.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/dataTables.colReorder.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/dataTables.buttons.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/buttons.colVis.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script>
$(document).ready(function() {	
$('#inputSearch').keyup(function() {
  	table._fnDraw();
});
});

$(document).ready(function(){	

$('body').on('click', '.right-side-toggle', function () {

    $("body").removeClass("control-sidebar-slide-open");

})

$(document).on('click', '.priority', function ()
  {
    var job_id        	= $(this).attr('data-id');
    if(typeof scheduleDate === "undefined") {
    scheduleDate 		= '';
  }
  var url       		= "{{ route('product_company.jobs.priority',':id') }}";
  url         		= url.replace(':id', job_id);
  $('#modelHeading_priority').html('Schedule');
  $.ajaxModal('#scheduleDetailModal_priority', url);
  });
});  

function editJobStatus(job_id)
{
  var id        = job_id;
  if(typeof scheduleDate === "undefined")
  {
     scheduleDate  = '';
  }
  var url       = "{{ route('product_company.jobs.editJobStatus',':id') }}";
  url         = url.replace(':id', id);
  $('#modelHeadingStatus').html('Schedule');
  $.ajaxModal('#scheduleDetailModalStatus', url);
}
</script>
<script>
var table = $('#myTable').dataTable({


      responsive     : false, 
      processing      : true,
      "scrollY"     : 200,
      // "autoWidth"     : false,
      autoWidth     : false, 
      destroy       : true,   
      stateSave     : true,
      colReorder      : true,     
      scrollCollapse    : true,       
      "bFilter"     : true, 
      "bLengthChange"   : false,          
      "fixedHeader":
       {
        "header": false,
        "footer": false  
       }, 
   
       "bInfo"     : false,
       "bLengthChange" : false,
       oLanguage:
       {
         sLengthMenu: "_MENU_",
       },		
		
		"lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],  
		"pageLength": 10,	
		// bFilter: false, bInfo: false,
			 
   /* lengthMenu: [
                  [10, 25, 50, 100,150,200,250,300],
                  [10, 25, 50, 100,150, 200, 250, 300]
                ],	*/	

		//"fixedHeader": false,
  	//responsive: true,
   	//serverSide: true,
		//stateSave : true,
    // "bFilter": true,
		//"paging":   true,      
		//lengthMenu: [[10, 25, 50, 100,150,200,250,300], [10, 25, 50, 100,150, 200, 250, 300]], 
    // "bLengthChange": false,
  	/*oLanguage:
    {
      sLengthMenu: "_MENU_", 
	  },*/
		colReorder: true,
		buttons:
    [{
        text      			:"Visibility Control",
        extend      		: 'colvis',
 				collectionLayout	: 'two-column',
				colReorder    		: true,
				columnText    		: function ( dt, idx, title ) {
  			return (idx+1)+': '+title;
		},
		dropup      		: true
}],
createdRow: function ( row, data, index )
{
	var suffix    = data['status'];
	var extnumber = parseInt(suffix.replace(/[^0-9]/g,''));
	if ( data['status'] == '<a href="Javascript:void(0);" onclick="editJobStatus('+extnumber+');">Closed</a>' )
  {
    $('td', row).eq(2).addClass('text-danger','');
		$('td', row).eq(3).addClass('text-black','font-weight-normal');
	}
	else if ( data['status'] == '<a href="Javascript:void(0);" onclick="editJobStatus('+extnumber+');">Hold</a>' )
  {
		$('td', row).eq(2).addClass('text-warning','');
		$('td', row).eq(3).addClass('text-black','font-weight-normal');
	} 
	else
  {
    $('td', row).eq(2).addClass('text-success','');
		$('td', row).eq(3).addClass('text-black','font-weight-normal');
	}

  $('td', row).eq(11).addClass('custom-bdr-right');
},          
			/* "fixedHeader": { 
  		"header": true, 
	 	 	"footer": true 
  	  },*/
			"columnDefs":
      [ 
        // 39
				{ "width": "4%", "targets": 0 },		
				{ "width": "4%", "targets": 1 },		
				{ "width": "7%", "targets": 2 },		
  			{ "width": "24%", "targets": 3 }, 

        // 23
				{ "width": "16%", "targets": 4 },
				{ "width": "3%", "targets": 5 },              
				{ "width": "4%", "targets": 6 },	

        // 38
				{ "width": "3%", "targets": 7 },		
				{ "width": "3%", "targets": 8 },	
				{ "width": "5%", "targets": 9 }, 
				{ "width": "3%", "targets": 10 },    
				{ "width": "3%", "targets": 11 },             
				{ "width": "3%", "targets": 12 },
				{ "width": "3%", "targets": 13 },   
				{ "width": "3%", "targets": 14 }, 
				{ "width": "3%", "targets": 15 },
				{ "width": "3%", "targets": 16 },
				{ "width": "3%", "targets": 17 },            
				{ "width": "3%", "targets": 18 }   
		  ],  


   		ajax: {'url' : '{!! route('product_company.jobs.data') !!}',
      "data": function ( d )
      {
        d.search = $('#inputSearch').val()
      }
    },

    language:
    {
      "url": "<?php echo __("app.datatable") ?>"
    },

    "fnDrawCallback": function( oSettings )
    {
      $("body").tooltip({
      selector: '[data-toggle="tooltip"]'
    });
},

"fnRowCallback" : function(nRow, aData, iDisplayIndex)
{
  $("td:first", nRow).html(iDisplayIndex +1);
  return nRow;
},

columns:
[
  { data: 'DT_Row_Index', name: 'id',  orderable: true, searchable: false},          
  /*{ data: 'id', name: 'id' , orderable: false, searchable: false},*/
  { data: 'days', name: 'days', orderable: false, searchable: false },
	/*{ data: 'priority', name: 'priority', orderable: true },*/
	{ data: 'status', name: 'status', orderable: false, searchable: false },
	{ data: 'title', name: 'title', orderable: false, searchable: false },
	{ data: 'clients_name', name: 'clients_name', orderable: false, searchable: false },
	{ data: 'jd', name: 'jd', orderable: false, searchable: false  },
	{ data: 'exp', name: 'exp', orderable: false, searchable: false  },
	{ data: 'total_positions', name: 'jobs.total_positions', orderable: false, searchable: false },
	{ data: 'client_location', name: 'client_location', orderable: false, searchable: false },
	{ data: 'client_contact', name: 'client_contact', orderable: false, searchable: false  },
	{ data: 'assign', name: 'assign', orderable: false, searchable: false },              
	{ data: 'candidate_counter', name: 'candidate_counter', orderable: false, searchable: false },
  { data: 'Sourcing', name: 'Sourcing', orderable: false, searchable: false },
	{ data: 'Screening', name: 'Screening', orderable: false, searchable: false },
	{ data: 'Interview', name: 'Interview', orderable: false, searchable: false },
  { data: 'Negotiation', name: 'Negotiation', orderable: false, searchable: false },
  { data: 'Offered', name: 'Offered', orderable: false, searchable: false },
	{ data: 'Joined', name: 'Joined', orderable: false, searchable: false },
	{ data: 'Rejected', name: 'Rejected', orderable: false, searchable: false }		   
]
}); 

new $.fn.dataTable.FixedHeader( table );

	//table.columns.adjust().draw();   
	//new $.fn.dataTable( table );
	//table.columns.adjust().draw();

table.on('click', '.show-detail', function ()
{
		//alert('call me');
    $(".right-sidebar").slideDown(50).addClass("shw-rside");
    var id    = $(this).data('row-id');
    var url   = "{{ route('product_company.jobs.viewmore',':id') }}";
    url     = url.replace(':id', id);
    $.easyAjax({
		type  : 'GET',
		url   : url,
  	success : function (response) {

	  if (response.status == "success")
      {
        $('#right-sidebar-content').html(response.view);
		  }
		}
  });
}); 

$('#filter-company, #filter-status').change(function ()
{
  table._fnDraw();
})

$('#column_visibility_control_btn').click(function()
{
  //alert("call");
  var id  = 12;
  if(typeof scheduleDate === "undefined")
  {
    scheduleDate  = '';
  }
  var url       = "{{ route('product_company.jobs.load_visibility_control',':id') }}";
  url         = url.replace(':id', id);
  $('#modelHeading_vis').html('Schedule');
  $.ajaxModal('#scheduleDetailModal_vis', url);
});

$('body').on('click', '.sa-params', function()
{
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
},

function(isConfirm)
{
 if (isConfirm)
  {
    var url = "{{ route('product_company.jobs.destroy',':id') }}";
    url = url.replace(':id', id);
    var token = "{{ csrf_token() }}";

    $.easyAjax({
    type: 'POST',
    url: url,
    data: {'_token': token, '_method': 'DELETE'},
    success: function (response)
    {
      if (response.status == "success")
      {
        $.unblockUI();
        //swal("Deleted!", response.message, "success");
        table._fnDraw();
      }
    }
  });
}
});
});

function updateJobStatus(id, statusName)
{
  var id        	= id;
  var statusName  = statusName;
  var url       	= "{{ route('product_company.jobs.updatestatus',':id') }}";
  url         	= url.replace(':id', id);
  var token       = "{{ csrf_token() }}";
  $.easyAjax({
	  type		: 'POST',
	  url		: url,
	  data		: {'_token': token, '_method': 'POST', 'id':id, 'statusName':statusName},
	  success	: function (response)
    {
		  table._fnDraw();
		}
  });
}

function exportExcel()
{
	var table 		        = $('#myTable').dataTable();
	var length 		        = table.fnGetData().length; 	
	var oSettings         = table.fnSettings();
  var start             = oSettings._iDisplayStart;					
  var url 			        = '{{ route('product_company.jobs.export', [':start' , ':length']) }}';
  url 					        = url.replace(':start', start);
	url 					        = url.replace(':length', length);
	window.location.href  = url;
}
</script>
@endpush 