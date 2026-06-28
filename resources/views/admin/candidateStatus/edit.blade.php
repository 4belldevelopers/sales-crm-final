@extends('layouts.app')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">@lang('app.edit')</h4>
        <form class="ajax-form" method="POST" id="createForm">
          @csrf
          <input name="_method" type="hidden" value="PUT">
          <div id="education_fields"></div>
          <div class="row">
		  
		  	<div class="col-md-12">
              <div class="form-group">
                <label for="name">Parent</label>
                <select name="parent" id="parent" class="form-control">
                  <option value="0" >New</option>
                  <?php 
							if(!empty($status)){
								foreach($status as $sval){
									$sel	=	'';
									if($candidate_status[0]->parent == @$sval->id){
										$sel	=	'selected="selected"';
									}
									echo '<option value="'.@$sval->id.'"  '.$sel.'>'.@$sval->name.'</option>';	
								}
							}
					?>
                </select>
              </div>
            </div>
			
			<?php 
					//echo '<pre>'; print_r($candidate_status[0]->parent);exit; 
			?>
            <div class="col-md-12">
              <div class="form-group">
                <label for="website">Status Name </label>
                <input type="text" class="form-control" name="title" id="title" value="<?php echo @$candidate_status[0]->name ?>" placeholder="Candidate Status Name">
              </div>
            </div>
			
			<?php 
					//echo '<pre>'; print_r($candidate_status[0]); 
			?>
			
			<div class="col-md-12">
              <div class="form-group">
                <label for="website">Priority</label>
                <select name="priority" id="priority" class="form-control">
					<option value="" >Select Priority</option>
					<?php 
							for($p=1; $p<=50; $p++){
								$sel	= '';
								if($candidate_status[0]->priority == $p){
									$sel	= 'selected="selected"';	
								}
								echo '<option value="'.$p.'" '.$sel.'>'.$p.'</option>';	
							}
					?>
				</select>
              </div>
            </div>
			
			
          </div>
          <button type="button" id="save-form" class="btn btn-success"><i
                                    class="fa fa-check"></i> @lang('app.save')</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@push('footer-script')
<script src="{{ asset('assets/node_modules/select2/dist/js/select2.full.min.js') }}<?php echo TM_VERSION; ?>"
            type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/bootstrap-select/bootstrap-select.min.js') }}<?php echo TM_VERSION; ?>"
            type="text/javascript"></script>
<script>
        // For select 2
        $(".select2").select2();

        var room = 1;

        

        $('#save-form').click(function () {
            $.easyAjax({
                url			: '{{route('admin.candidateStatus.update', $candidate_status[0]->id)}}',
                container	: '#createForm',
                type		: "POST",
                redirect	: true,
                data		: $('#createForm').serialize()
            })
        });
    </script>
@endpush