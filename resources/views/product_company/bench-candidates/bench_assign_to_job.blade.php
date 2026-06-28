<style>
.form-check.form-check-custom .form-check-label
{ margin-left: 2.55rem; }
</style>
<form>
  <div class="card-body position-relative px-0 py-0" id="kt_activities_body">
    <div id="kt_activities_scroll" class="position-relative scroll-y bg-light pt-2 h-100" data-kt-scroll="true" data-kt-scroll-height="auto" data-kt-scroll-wrappers="#kt_activities_body" data-kt-scroll-dependencies="#kt_activities_header, #kt_activities_footer" data-kt-scroll-offset="5px" style="">
      <div class="card card-custom mx-2">
        <div class="card-body py-7">
          <table id="bench_explore_listing" class="table align-middle table-row-dashed fs-6 gy-5">
            <thead>
              <tr class="fw-bolder text-muted">
                <th class="w-10px pe-0"> <div class="form-check form-check-sm form-check-custom form-check-solid me-3"> &nbsp; </div></th>
                <th class="d-flex align-items-center border-0">Requisition</th>
                <th class="" data-bs-toggle="tooltip" title="Experience">Exp.</th>
                <th class="">Salary</th>
                <th class="">Location</th>
                <th class="">Status</th>
                <th class="">Added Date</th>
                <th class="">Contact Person</th>
				<th class="">Offer Amount</th>
				<th class="">Join Date</th>
                <th class="">Interview</th>
                <th class="">Stage</th>
              </tr>
            </thead>
          </table>
        </div>
      </div>
    </div>
  </div>
</form>

<script>

		   $(document).ready(function() { 
			  $('#inputSearch').keyup(function() {
				  tableLoad('load');   
			  });
		   });
    
 			var table;
           	tableLoad('load');
			function tableLoad(type)
			{
				
           
									var bench_id       	=	{{ @$currentBenchId }};
									var stage  			=	'all';
									var inputSearch 	=	$('#inputSearch').val();
        
									table 				=	$('#bench_explore_listing').dataTable({
  
												responsive     	:	true, 
												processing      :	true,
												ordering		:	false, //disable ordering      			
												destroy       	:	true,   
												stateSave     	:	true,      
												"bLengthChange" :	false,   
												sDom			:	'lrtip',         
														 
												ajax			:	'{!! route('product_company.bench-candidates.bench_assign_to_job_listing') !!}?bench_id='+bench_id+'&stage='+stage+'&search='+inputSearch,
											   "fnDrawCallback" :	function (oSettings) {
																							$("body").tooltip({selector: '[data-toggle="tooltip"]' });
																	},
												"drawCallback"	:	function(settings) {},
											  	"initComplete"	:	function( settings, json){ 
											  
																								$('.clientStatusLoader').html('');
																								$('.jobLoader').html('');
																								$('.sourceLoader').html('');
																								
																								$('.search_np_idLoader').html('');
																								$('.search_passportLoader').html('');
																								$('.statusLoader').html('');            							
																	 }, 
											  	"columnDefs"		:
																	  [ 
																				{ "width": "50px", "targets": 0, "width": "4%", "class":""},
																				{  "width": "200px", "targets": 1, "class":"d-flex align-items-center" },																				
																				{  "width": "50px"  , "targets": 2},		
																				{  "width": "100px", "targets": 3 },		
																				{  "width": "200px", "targets": 4 }, 
											
																				{  "width": "50px" ,"targets": 5 },
																				{  "width": "200px" , "targets": 6 },              
																				{  "width": "150px" , "targets": 7 },	
																				{  "width": "150px", "targets": 8 },	
																				{ "width": "20px", "targets": 9 } 	
																		], 
 
												   columns			: [             
																			{data: 'chkbox', name: 'chkbox', orderable: false, searchable: false},            
																			{data: 'Requisition', name: 'Requisition',  orderable: false, searchable: false, width: '15%'},						
																			{data: 'Exp', name: 'Exp', searchable : false, orderable : false, width: '15%'},						
																			{data: 'Salary', name: 'Salary', searchable : false, orderable : false,  width: '5%'},
																			{data: 'Location', name: 'Location',  searchable : false, orderable : false,  width: '10%'},   
																			
																			{data: 'Status', name: 'Status', searchable : false, orderable : false},
																			{data: 'AddedDate', name: 'AddedDate',  searchable : false, orderable : false},
																			{data: 'ContactPerson', name: 'ContactPerson', orderable: false},
																		    {data: 'offeramount', name: 'offeramount', orderable : false},
																		    {data: 'joindate', name: 'joindate', orderable : false}, 
																			{data: 'Interview', name: 'Interview', orderable : false},   
																			{data: 'Stage', name: 'Stage', orderable: false}
																		]         
      
             }); 
           new $.fn.dataTable.FixedHeader(table); 
		   
			} 




	
</script>


