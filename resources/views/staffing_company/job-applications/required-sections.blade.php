@if($section_visibility['profile_image'] == 'yes')
<div class="row">
  <div class="col-md-6 pt-4">
    <div class="form-group">
      <h5>@lang('modules.front.photo')</h5>
      <input class="select-file w-100 mb-2" accept=".png,.jpg,.jpeg" type="file" name="photo">
      <br>
      <?php 
            if(!empty(@$application) && !is_null(@$application->photo)) 
            {
            ?>
      <a target="_blank" href="{{ asset('user-uploads/candidate-photos/'.@$application->photo) }}" class="btn btn-sm btn-light-primary py-0 px-1"> <i class="fa fa-camera"></i>&nbsp;@lang('app.view') </a>
      <?php 
            } 
            ?>
      &nbsp;<span>|</span> <span class="text-primary">@lang('modules.front.photoFileType')</span>
      <!-- <h5>@lang('modules.front.resume')</h5> -->
    </div>
    <?php 
         if(!empty(@$application) && !is_null(@$application->photo)) 
         {
         ?>
    <a target="_blank" href="{{ asset('user-uploads/candidate-photos/'.@$application->photo) }}" class="btn btn-sm btn-primary py-0 px-1"> <i class="fa fa-camera"></i>&nbsp;@lang('app.view') </a> &nbsp;| <span class="tex
         <?php 
            } 
            ?>
         </div>
         @endif
         @if($section_visibility['resume'] == 'yes')
         <!-- <div class="col-md-4 pl-4 pr-4 pb-4 pt-4 b-b">
    <h5>@lang('modules.front.resume')</h5>
  </div>
  -->
  <div class="col-md-6 pt-4">
    <div class="form-group">
      <h5> @lang('modules.front.resume') </h5>
      <input class="select-file w-100 mb-2" accept=".png,.jpg,.jpeg,.pdf,.doc,.docx,.xls,.xlsx,.rtf" type="file" name="resume">
    </div>
    @if (!empty($application) && $application->resume_url) <a target="_blank" href="{{ $application->resume_url }}" class="btn btn-sm btn-light-primary px-1 py-0"> <i class="fa fa-file"></i>&nbsp;@lang('app.view') </a> &nbsp;| <span class="text-primary">@lang('modules.front.resumeFileType')</span> @endif </div>
  @endif
  @if($section_visibility['cover_letter'] == 'yes')
  <!-- <div class="col-md-4 pl-4 pr-4 pt-4 b-b">
      <h5>@lang('modules.front.coverLetter')</h5>
      </div> -->
  <div class="col-md-12 pt-4">
    <div class="form-group">
      <h5> @lang('modules.front.coverLetter') </h5>
      <textarea class="form-control" name="cover_letter" rows="4">
                    {{ !empty($application) ? $application->city : '' }}
                </textarea>
    </div>
  </div>
  @endif </div>
