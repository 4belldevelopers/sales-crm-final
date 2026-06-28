<!-- <title>jQuery UI Sortable - Default functionality</title>
  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <link rel="stylesheet" href="/resources/demos/style.css">
  <style>
  #sortable { list-style-type: none; margin: 0; padding: 0; width: 60%; }
  #sortable li { margin: 0 3px 3px 3px; padding: 0.4em; padding-left: 1.5em; font-size: 1.4em; height: 18px; }
  #sortable li span { position: absolute; margin-left: -1.3em; }
  </style>
 
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script>
  $( function() {
  	$.noConflict();
    $( "#sortable" ).sortable();
    $( "#sortable" ).disableSelection();
  } );
  </script>

<ul id="sortable">
  <li class="ui-state-default"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>Item 1</li>
  <li class="ui-state-default"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>Item 2</li>
  <li class="ui-state-default"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>Item 3</li>
  <li class="ui-state-default"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>Item 4</li>
  <li class="ui-state-default"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>Item 5</li>
  <li class="ui-state-default"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>Item 6</li>
  <li class="ui-state-default"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>Item 7</li>
</ul>-->
<!-- container / end -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
<div class="rpanel-title"> Dynamic Drag and Drop table column <span><i class="ti-close right-side-toggle"></i></span> </div>
<div class="r-panel-body p-3">
  <div class="row font-12">
    <div class="col-4 text-center">
      <div class="container">
        <div class="col-md-3">
          <div class="form-group">
            <table class="table table-bordered">
              <tr>
                <th>#</th>
                <th>Name</th>
                <th>Display</th>
              </tr>
              <tbody class="row_position">
                <?php

					$i=1; 
         			foreach($candidate_field as $cv_value){
					
					$check = '';
					if($cv_value->display_status == 1)
					{
						$check = 'checked="checked"';	
					}
						
     ?>
                <tr  id="<?php echo $cv_value->custom_field_id ?>">
                  <td><?php echo $i; ?></td>
                  <td><?php echo $cv_value->display_name; ?></td>
                  <td><input name="custom_field_display_status[]" class="chk_update" type="checkbox" value="<?php echo $cv_value->custom_field_id ?>"   <?php echo @$check ; ?> /></td>
                </tr>
                <?php 	$i++; } ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
	
	
	
 $( document ).ready(function() {
 
 	var table = $('#myTable').DataTable( {
        colReorder: true
    } );
	
	$('.chk_update').click(function(e) {
		
		var url 			= "{{ route('staffing_company.cvtracker.candidateRightSideBarUpdate_display') }}";
		var token 			= "{{ csrf_token() }}";
		
		var display_value 	= 0;
		var checked 		= $(this).prop('checked');
		var custom_id 		= $(this).val();
		if(checked == true)
		{
				display_value = 1;	
		}
		
			$.ajax({
					url:url,
					type:'post',
				   // data:{position:data, token: token},
					//data: {'_token': token, '_method': 'POST' },
					data: {'_token': token, '_method': 'POST', 'display_value' : display_value, 'custom_id' : custom_id },
					success:function(){
						//alert('your change successfully saved');
						tableLoad('load');
					}
        	})
		
  	});


	
    $( ".row_position" ).sortable({
        delay: 150,
        stop: function(event) {
		
		//event.stopPropagation();
		//alert("call me"); 
            var selectedData = new Array();
            $('.row_position>tr').each(function() {
                selectedData.push($(this).attr("id"));
				
				table.colReorder.order.push( $(this).attr("id") );
				
            });
            updateOrder(selectedData);
			
			
			
			
        }
    });

 
});

    function updateOrder(data) {
	
	
		alert("call");
			 var table = $('#myTable').DataTable( {
       			 colReorder: true,
    		} );
			
		var url 			= "{{ route('staffing_company.cvtracker.candidateRightSideBarUpdate') }}";
		var token 			= "{{ csrf_token() }}";
		 
        $.ajax({
            url:url,
            type:'post',
           // data:{position:data, token: token},
			data: {'_token': token, '_method': 'POST', 'position' : data},
            success:function(){
                //alert('your change successfully saved');
				tableLoad('load');
            }
        })
    }
	


 	
	

	

</script>
