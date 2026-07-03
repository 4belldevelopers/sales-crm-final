<?php
  $additionalIndex = (int) @$cid + 1;
?>
<div class="client-address-card is-additional client-address-container-child" id="clientAddr_<?php echo @$cid; ?>">
  <div class="client-address-card-head">
    <h4>Additional Address (<?php echo max(1, $additionalIndex); ?>)</h4>
    <button type="button" class="client-remove-address-btn remove_address_single" data-target="clientAddr_<?php echo @$cid; ?>"><i class="la la-trash"></i> Remove</button>
  </div>
  <div class="client-field fv-plugins-icon-container mb-3">
    <label class="form-label required" for="kt_tagify_work_location_<?php echo @$cid; ?>">City</label>
    <div class="input-group input-group-solid">
      <span class="input-group-text"><i class="la la-map-marker"></i></span>
      <input class="form-control form-control-solid" value="" id="kt_tagify_work_location_<?php echo @$cid; ?>" name="kt_tagify_work_location[]" placeholder="Start typing to search for a city"/>
    </div>
    <span class="form-text text-muted">Start typing to search for a city.</span>
  </div>
  <div class="client-field fv-plugins-icon-container">
    <label class="form-label required" for="address_<?php echo @$cid; ?>">Full Address</label>
    <textarea class="form-control form-control-solid client-address-textarea" name="address[]" rows="4" maxlength="500" placeholder="Building, street, area, landmark, pincode, etc." id="address_<?php echo @$cid; ?>"></textarea>
    <div class="d-flex justify-content-between mt-1">
      <span class="form-text text-muted">Provide the complete and accurate address for better identification.</span>
      <span class="client-char-count"><span class="client-address-count">0</span>/500</span>
    </div>
  </div>
</div>

<script>
$(document).ready(function () {
  var inputloc = document.querySelector("#kt_tagify_work_location_{{ @$cid }}");
  var cities = @json($cities);

  if (inputloc) {
    var tagifylocation = new Tagify(inputloc, {
      whitelist: cities,
      enforceWhitelist: true,
      maxTags: 1,
      dropdown: {
        maxItems: 20,
        classname: "",
        enabled: 0,
        closeOnSelect: true
      }
    });

    tagifylocation.on('input', function () {
      tagifylocation.dropdown.show();
    });

    tagifylocation.on('dropdown:select', function (e) {
      tagifylocation.addTags([e.detail.data.value]);
    });

    function debounce(func, delay) {
      let timer;
      return function (...args) {
        clearTimeout(timer);
        timer = setTimeout(() => func.apply(this, args), delay);
      };
    }

    const fetchSuggestions = debounce(function (query) {
      $.ajax({
        url: "{{ url('/') }}" + "/city-job-suggestions?q=" + query,
        method: 'GET',
        success: function (data) {
          if (Array.isArray(data)) {
            tagifylocation.settings.whitelist = data;
            tagifylocation.dropdown.show.call(tagifylocation);
          }
        }
      });
    }, 200);

    tagifylocation.on('input', function (e) {
      var query = e.detail.value.trim();
      if (query.length < 2) return;
      fetchSuggestions(query);
    });
  }

  if (typeof window.clientWizardUi !== 'undefined') {
    window.clientWizardUi.syncAdditionalAddressEmpty();
    window.clientWizardUi.bindAddressCounters(document.getElementById('clientAddr_{{ @$cid }}'));
  }
});
</script>
