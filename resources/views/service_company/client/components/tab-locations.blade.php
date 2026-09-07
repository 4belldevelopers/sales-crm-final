<!--begin::Address Details-->
<div class="pb-5 client-address-step" data-wizard-type="step-content">
  <div class="client-step-layout">
    <div class="client-main-column">
      <div class="client-step-header">
        <div>
          <h3>Address Details</h3>
          <p>Enter the city and full address of this client.</p>
        </div>
      </div>
      <div class="client-info-banner"><i class="la la-info-circle" aria-hidden="true"></i><span>The first address you enter will be considered as the headquarters.</span></div>
      <?php
        $countryListing = $stateListing = '';
        foreach ($countryArr as $cont) {
          $sel = ($cont['id'] == 101) ? 'selected="selected"' : '';
          $countryListing .= '<option value="'.$cont['id'].'" '.$sel.'>'.$cont['name'].'</option>';
        }
        foreach ($statelist as $state) {
          $stateListing .= '<option value="'.$state['id'].'">'.$state['name'].'</option>';
        }

        $addressId = 0;
        $addressIdCount = 1;
        if ($page_event == 'EDIT') {
          $clhtml = '<div class="row client-address-container">';
          foreach ($client_location_arr as $clVal) {
            $countryListing = $stateListing = '';
            $countryName = '';
            foreach ($countryArr as $cont) {
              $sel = ($cont['id'] == $clVal->country_id) ? 'selected="selected"' : '';
              if ($cont['id'] == $clVal->country_id) {
                $countryName = $cont['name'];
              }
              $countryListing .= '<option value="'.$cont['id'].'" '.$sel.'>'.$cont['name'].'</option>';
            }
            $statelist = GeneralMethod::statelist($clVal->country_id);
            foreach ($statelist as $state) {
              $sel = ($state['id'] == $clVal->state) ? 'selected="selected"' : '';
              $stateListing .= '<option value="'.$state['id'].'" '.$sel.'>'.$state['name'].'</option>';
            }
            $statelist = DB::table('state_list')
              ->select('state_list.id', 'state_list.state as name', DB::raw('101 as country_id'))
              ->where('state_list.country_id', $clVal->country_id)
              ->orderBy('state_list.state')
              ->get()->toArray();
            $indianStatelist = json_decode(json_encode($statelist), true);
            $stateListing = '';
            $cityListing = '';
            $stateName = '';
            $cityName = '';
            foreach ($indianStatelist as $sRow) {
              $sel = ($sRow['id'] == $clVal->state) ? 'selected="selected"' : '';
              if ($sRow['id'] == $clVal->state) {
                $stateName = $sRow['name'];
              }
              $stateListing .= '<option value="'.$sRow['id'].'" '.$sel.'>'.$sRow['name'].'</option>';
            }
            $Rawcitylist = DB::table('city_list')->select('city_list.city_id as id', 'city_list.city as name')->where('city_list.state_id', $clVal->state)->orderBy('city_list.city')->get();
            foreach ($Rawcitylist as $cRow) {
              $sel = ($cRow->id == $clVal->city_id) ? 'selected="selected"' : '';
              if ($cRow->id == $clVal->city_id) {
                $cityName = $cRow->name;
              }
              $cityListing .= '<option value="'.$cRow->id.'" '.$sel.'>'.$cRow->name.'</option>';
            }
            $return = $cityName.' | '.$stateName.' | '.$countryName;
            $cardClass = ($addressIdCount === 1) ? '' : ' is-additional';
            $cardTitle = ($addressIdCount === 1) ? 'Headquarters (Primary)' : 'Additional Address ('.($addressIdCount - 1).')';
            $clhtml .= '
              <div class="client-address-card'.$cardClass.' client-address-container-child" id="clientAddr_'.$addressId.'">
                <div class="client-address-card-head">
                  <h4>'.$cardTitle.'</h4>
                  '.($addressIdCount > 1 ? '<button type="button" class="client-remove-address-btn remove_address_single" data-target="clientAddr_'.$addressId.'"><i class="la la-trash"></i> Remove</button>' : '').'
                </div>
                <div class="client-field fv-plugins-icon-container mb-3">
                  <label class="form-label required" for="kt_tagify_work_location_'.$addressId.'">City</label>
                  <div class="input-group input-group-solid">
                    <span class="input-group-text"><i class="la la-map-marker"></i></span>
                    <input type="hidden" name="hid_country_id[]" id="hid_country_id_'.$addressId.'" class="gethiddencountry" value="'.$clVal->country_id.'" />
                    <input class="form-control form-control-solid" value="'.$return.'" id="kt_tagify_work_location_'.$addressId.'" name="kt_tagify_work_location[]" placeholder="Start typing to search for a city"/>
                  </div>
                  <span class="form-text text-muted">Start typing to search for a city.</span>
                </div>
                <div class="client-field fv-plugins-icon-container">
                  <label class="form-label required" for="address_'.$addressId.'">Full Address</label>
                  <textarea class="form-control form-control-solid client-address-textarea" name="address[]" rows="4" maxlength="500" placeholder="Building, street, area, landmark, pincode, etc." id="address_'.$addressId.'">'.@$clVal->address.'</textarea>
                  <div class="d-flex justify-content-between mt-1">
                    <span class="form-text text-muted">Provide the complete and accurate address for better identification.</span>
                    <span class="client-char-count"><span class="client-address-count">0</span>/500</span>
                  </div>
                </div>
              </div>';
            $addressId++;
            $addressIdCount++;
          }
          $clhtml .= '</div>';
          echo $clhtml;
          $addressId--;
        } else {
      ?>
      <div class="row client-address-container">
        <div class="client-address-card client-address-container-child" id="clientAddr_<?php echo @$addressId; ?>">
          <div class="client-address-card-head">
            <h4>Headquarters (Primary)</h4>
          </div>
          <div class="client-field fv-plugins-icon-container mb-3">
            <label class="form-label required" for="kt_tagify_work_location_<?php echo @$addressId; ?>">City</label>
            <div class="input-group input-group-solid">
              <span class="input-group-text"><i class="la la-map-marker"></i></span>
              <input class="form-control form-control-solid" value="" id="kt_tagify_work_location_<?php echo @$addressId; ?>" name="kt_tagify_work_location[]" placeholder="Start typing to search for a city"/>
            </div>
            <span class="form-text text-muted">Start typing to search for a city.</span>
          </div>
          <div class="client-field fv-plugins-icon-container">
            <label class="form-label required" for="address_<?php echo @$addressId; ?>">Full Address</label>
            <textarea class="form-control form-control-solid client-address-textarea" name="address[]" rows="4" maxlength="500" placeholder="Building, street, area, landmark, pincode, etc." id="address_<?php echo @$addressId; ?>"></textarea>
            <div class="d-flex justify-content-between mt-1">
              <span class="form-text text-muted">Provide the complete and accurate address for better identification.</span>
              <span class="client-char-count"><span class="client-address-count">0</span>/500</span>
            </div>
          </div>
        </div>
      </div>
      <?php } ?>
      <section class="client-section-card mt-3">
        <div class="client-address-card-head">
          <div>
            <h4>Additional Address <span class="text-muted fw-normal">(Optional)</span></h4>
          </div>
          <button type="button" class="client-add-address-btn add_address"><i class="la la-plus" aria-hidden="true"></i> Add Another Address</button>
        </div>
        <div id="addressLoader"></div>
        <div class="client-address-empty" id="client_additional_empty">
          <i class="la la-building" aria-hidden="true"></i>
          <p>No additional address added yet.</p>
        </div>
      </section>
      <input type="hidden" name="addressId" id="addressId" value="<?php echo @$addressId; ?>" />
    </div>
    <aside class="client-side-column">
      <div class="client-helper-card client-helper-sky">
        <h4><i class="la la-map-marked-alt" aria-hidden="true"></i> About Headquarters</h4>
        <p>The first address is treated as the client headquarters and appears prominently on the client profile and job listings.</p>
      </div>
      <div class="client-helper-card client-helper-green">
        <h4><i class="la la-lightbulb-o" aria-hidden="true"></i> Tips for Accurate Address</h4>
        <ul>
          <li><i class="la la-check-circle" aria-hidden="true"></i><span>Search and select the correct city.</span></li>
          <li><i class="la la-check-circle" aria-hidden="true"></i><span>Include landmark and pincode in the full address.</span></li>
          <li><i class="la la-check-circle" aria-hidden="true"></i><span>Add branch offices as additional addresses if needed.</span></li>
        </ul>
      </div>
    </aside>
  </div>
</div>
<!--end::Address Details-->
