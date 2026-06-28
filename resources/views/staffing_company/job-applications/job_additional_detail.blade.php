 @if(count($job_additional_detail) > 0)
<div class="col-12">
  <h4 class="text-dark border-bottom p-2 bg-light-secondary"> <i class="fa fa-info-circle"></i>&nbsp;Additional Details</h4>
</div>
<div class="col-sm-12">
  <div class="form-group">
    <!-- <label for="detail1">Contact Person Info </label> -->
    <div class="row mx-0 d-flex justify-content-right">
      <?php 
					$i=1; 
					$j=0;
					foreach($job_additional_detail as $val){
			?>
      <div class="boxs width-32 mr-2">
        <label class="detail1"><?php echo $val->detail_value ?></label>
        <input type="text" class="form-control d-block border p-1 font-weight-400 w-100 text-dark detail1" name="job_additional_detail[<?php echo $j; ?>]" placeholder="Enter Additional Details - <?php echo $i;?>">
      </div>
      <!-- <div class="input-group">
      <label class="form-control mb-0 detail1"><?php echo $val->
      detail_value ?>
      </label>
      &nbsp;
      <input type="text" name="job_additional_detail[<?php echo $j; ?>]" class="form-control mb-0 detail1" placeholder="Enter Additional Details - <?php echo $i;?>" >
      &nbsp; </div>
    -->
    <?php 
		   			$i++; $j++;
		   			}
		   ?>
  </div>
</div>
</div>
@endif 