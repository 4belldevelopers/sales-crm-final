<style>
    .tagify .tagify__tag div .tagify__tag-text {
      color: #0c0c0c !important;
      font-weight: 500 !important;
      font-size: 14px !important;
  }
  .client-address-container-child{
    background: #f9f9f940;
    border-radius: 14px;
    padding: 7px 0px;
    margin-bottom: 15px;
  }
  </style>
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
      <label class="form-label required fw-bolder">Address: <?php echo @$cid+1; ?></label>
      <input type="text" class="form-control form-control-lg form-control-solid" name="address[]" placeholder="Enter address" value="" id="address_<?php echo @$cid; ?>">
      <span class="form-text text-muted">Please enter your Address</span> 
     
    </div>
    <div class="col-xl-12">
      <div class="form-group fv-plugins-icon-container">
        <label class="form-label required fw-bolder">Select City:</label>
         <input class="form-control form-control-solid" value="" id="kt_tagify_work_location_<?php echo @$cid; ?>" name="kt_tagify_work_location[]" placeholder="Enter your Location..."/>
        <span class="form-text text-muted">Please select your city</span>
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
  
  
      var inputloc = document.querySelector("#kt_tagify_work_location_{{@$cid}}");
      var cities = @json($cities);
                               
      // Initialize Tagify script on the above inputs 
      var tagifylocation =  new Tagify(inputloc, {
          whitelist: cities,
          enforceWhitelist: true, // Prevents users from adding custom values
          maxTags: 1,
          dropdown: {
              maxItems: 20,           // <- mixumum allowed rendered suggestions
              classname: "", // <- custom classname for this dropdown, so it could be targeted
              enabled: 0,             // <- show suggestions on focus
              closeOnSelect: true    // <- do not hide the suggestions dropdown once an item has been selected
          }
      });
  
      // Prevent creating tags on blur or enter key
      tagifylocation.on('input', function(e) {
          tagify.dropdown.show(); // show the dropdown immediately
      });
  
      // Create tags only on select
      tagifylocation.on('dropdown:select', function(e) {
          tagify.addTags([e.detail.data.value]);
      });

      tagifylocation.on('add', function(e) {
        // Remove the tag if it wasn't selected from the dropdown
        // if (!cities.includes(e.detail.data.value)) {
        //     tagifylocation.removeTag(e.detail.data.value);
        // }
      });
  
      function debounce(func, delay) {     
            let timer;
            return function (...args) {
                clearTimeout(timer);
                timer = setTimeout(() => func.apply(this, args), delay);
            };
        }

        const fetchSuggestions = debounce(function(query) {
            $.ajax({
                url: "{{ url('/') }}" + "/city-job-suggestions?q=" + query,
                method: 'GET',
                success: function(data) {
                    console.warn("Fetched Cities: ", data);

                    if (Array.isArray(data)) {
                      tagifylocation.settings.whitelist = data; 
                      // tagifylocation.dropdown.show(query); 
                      tagifylocation.dropdown.show.call(tagifylocation);
                    } else {
                        console.error("Invalid API response format: Expected array.");
                    }
                },
                error: function(err) {
                    console.error("Error fetching cities:", err);
                }
            });
        }, 200); // 300ms delay
        tagifylocation.on('input', function(e) {
            var query = e.detail.value.trim();
            if (query.length < 2) return;
            fetchSuggestions(query);
        });
   
  
  });
  
  </script>
  