<?php 
							  
				$countryListing	= $stateListing =	'';
				foreach($countryArr as $cont){
						$sel	=	'';
						if($cont['id'] == 101){
							$sel	=	'selected="selected"';
						}
						$countryListing	.= '<option value="'.$cont['id'].'"  '.@$sel.' >'.$cont['name'].'</option>';	
				}										
			 				 			
					  
?>

<div class="row client-address-container-child" id="clientAddr_<?php echo @$cid; ?>">
  <div class="form-group">
    <label class="form-label required fw-bolder">Address:</label>
    <input type="text" class="form-control form-control-lg form-control-solid" name="address[]" placeholder="Enter address" value="" id="address_<?php echo @$cid; ?>">
    <span class="form-text text-muted">Please enter your Address</span> </div>
  <div class="col-xl-3">
    <div class="form-group fv-plugins-icon-container">
      <label class="form-label required fw-bolder">Country:</label>
      <input type="hidden" name="hid_country_id[]" id="hid_country_id_<?php echo @$cid; ?>" class="gethiddencountry" value="" />
      <select name="country[]" class="form-control form-control-lg form-control-solid country" id="country_<?php echo @$cid; ?>">
        <option value="">Select</option>
        <?php echo @$countryListing;?>
      </select>
      <span class="form-text text-muted">Please select your country</span>
      <div class="fv-plugins-message-container"></div>
    </div>
  </div>
  <div class="col-xl-3">
    <div class="form-group fv-plugins-icon-container">
      <label class="form-label required fw-bolder">State:</label>
      <select name="state[]" class="form-control form-control-lg form-control-solid change_state" id="state_<?php echo @$cid; ?>">
        <option value="">Select</option>
      </select>
      <span class="form-text text-muted">Please select your state</span>
      <div class="fv-plugins-message-container"></div>
    </div>
  </div>
  <div class="col-xl-3">
    <div class="form-group fv-plugins-icon-container">
      <label class="form-label required fw-bolder">City</label>
      <input type="text" class="form-control form-control-lg form-control-solid" name="city[]" placeholder="Enter city" value="" id="city_<?php echo @$cid; ?>">
      <select class="form-select form-control form-control-lg form-control-solid" name="city_id[]" id="city_id_<?php echo @$cid; ?>" style="display:none">
        <option value="">Select City</option>
      </select>
      <span class="form-text text-muted">Please enter your city:</span>
      <div class="fv-plugins-message-container"></div>
    </div>
  </div>
  <div class="col-xl-3">
    <div class="form-group fv-plugins-icon-container">
      <label class="form-label required fw-bolder">Postcode:</label>
      <input type="text" class="form-control form-control-lg form-control-solid" name="postcode[]" placeholder="Enter postcode" value="" id="postcode_<?php echo @$cid; ?>">
      <span class="form-text text-muted">Please enter your postcode</span>
      <div class="fv-plugins-message-container"></div>
    </div>
  </div>
</div>


<script>
$(document ).ready(function() { 

  setTimeout(() => {
  var cid =  $("#country_{{@$cid}}").val();
    setTimeout(() => {
    $("#country_{{@$cid}}").val(cid).change();
   }, 500);
 
  }, 500);

 

});

</script>
