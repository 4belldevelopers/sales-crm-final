@if ($job->required_columns['gender'])
<label for="ectc" style="display: none;">@lang('modules.front.gender')</label>
<div class="form-group d-none"> @foreach ($gender as $key => $value)
  <div class="form-check form-check-inline mt-1" style="display: unset;">
    <input @if (!empty($application) && $key == $application->
    gender) checked @endif class="form-check-input" type="radio" name="gender" id="{{ $key }}" value="{{ $key }}">
    <label class="form-check-label" for="{{ $key }}">{{ ucFirst($value) }}</label>
  </div>
  @endforeach </div>
@endif
@if ($job->required_columns['dob'])
<div class="form-group">
  <label for="ectc">@lang('modules.front.dob')</label>
  <input class="form-control form-control-lg dob mt-1" type="text" name="dob"
    placeholder="@lang('modules.front.dob')" autocomplete="none">
</div>
@endif
@if ($job->required_columns['country'])
<div class="row mt-3">
  <div class="col-md-12">
    <h6> <strong> @lang('modules.front.country') </strong> </h6>
  </div>
  <div class="col-md-4">
    <div class="form-group mt-1">
      <select class="select2 countries" name="country" id="countryId">
        <option value="0">@lang('modules.front.selectCountry')</option>
      </select>
    </div>
  </div>
  <div class="col-md-4">
    <div class="form-group mt-1">
      <select class="select2 states" name="state" id="stateId">
        <option value="0">@lang('modules.front.selectState')</option>
      </select>
    </div>
  </div>
  <div class="col-md-4">
    <div class="form-group mt-1">
      <input class="form-control" type="text" name="city" id="cityId" placeholder="@lang('modules.front.selectCity')" value="{{ !empty($application) ? $application->cover_letter : '' }}">
    </div>
  </div>
</div>
@endif