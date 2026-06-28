<link rel="stylesheet" href="{{ asset('assets/plugins/jquery-bar-rating-master/dist/themes/fontawesome-stars.css') }}">
<style>

    .right-panel-box{
        overflow-x: scroll;
        max-height: 100vh;
    }
    .resume-button{
        text-align: center; margin-top: 1rem
    }


</style>
<?php 
		//echo '<pre>'; print_r($application->resume);  
?>
<div class="rpanel-title"> Candidate Resume <span><i class="ti-close right-side-toggle"></i></span> </div>
<div class="r-panel-body p-3">
<div class="row font-12">
  <div class="col-md-4">
    <h5>Full Name </h5>
  </div>
  <div class="col-md-8">
    <div class="form-group"> {{ ucwords($application->full_name) }} </div>
  </div>
  <div class="col-md-12">
    <h5>Read Resume</h5>
  </div>
  <div class="col-md-12">
    <?php 
  			$resume		= '';
			echo $resume		=	$application->resume;
			echo '<br>'; 
			$ext 				= pathinfo($resume);
			
			echo '<pre>'; print_r($ext); 
			if(($ext['extension'] == 'doc') || ($ext['extension'] == 'docx') || ($ext['extension'] =='DOC') || ($ext['extension'] == 'DOCX') )
			{
				$fullresume =	asset('user-uploads/resumes/'.$resume); 
				echo '<br>';
				echo asset('user-uploads/resumes/'.$resume);
				echo '<br>';
?>
    <iframe src="https://docs.google.com/viewer?embedded=true&url=<?php echo @$fullresume; ?>" frameborder="no" style="width:100%;height:160px"></iframe>
    <iframe src="https://docs.google.com/gview?url=<?php echo @$fullresume; ?>&embedded=true"></iframe>
    <iframe src='https://view.officeapps.live.com/op/embed.aspx?src=<?php echo @$fullresume; ?>' width='px' height='px' frameborder='0'></iframe>
    <iframe src="https://docs.google.com/viewer?embedded=true&url=localhost/ats2020/public/user-uploads/resumes/c144f058efda581703b2cc8a0e9e75b1.docx" frameborder="no" style="width:100%;height:160px"></iframe>
    <?php  
}
else if( ($ext['extension'] == 'pdf') || ($ext['extension'] == 'PDF')) 
{
?>
    <iframe src="http://docs.google.com/viewer?url=https://jobmilee.com/ats2020/public/user-uploads/resumes/5a681fc636b7ac076f2af67ec94e26d7.docx&embedded=true&embedded=true" frameborder="no" style="width:100%;height:900px"></iframe>
    <?php 
} 
?>
  </div>
  <div class="col-8 right-panel-box"> </div>
</div>
@if($user->can('edit_job_applications'))
<script src="{{ asset('assets/plugins/jquery-bar-rating-master/dist/jquery.barrating.min.js') }}" type="text/javascript"></script>
<script>
    $('#example-fontawesome').barrating({
        theme: 'fontawesome-stars',
        showSelectedRating: false,
        onSelect:function(value, text, event){
            if(event !== undefined && value !== ''){
                var url = "{{ route('staffing_company.job-applications.rating-save',':id') }}";
                url = url.replace(':id', {{$application->id}});
                var token = '{{ csrf_token() }}';
                var id = {{$application->id}};
                $.easyAjax({
                    type: 'Post',
                    url: url,
                    container: '#example-fontawesome',
                    data: {'rating':value, '_token':token},
                    success: function (response) {
                        $('#example-fontawesome_'+id).barrating('set', value);
                    }
                });
            }

        }
    });
    @if($application->rating !== null)
        $('#example-fontawesome').barrating('set', {{$application->rating}});
    @endif

    $('#view-application-details').click(function () {
        var url = "{{ route('staffing_company.job-applications.viewDetails') }}";
        var id = {{$application->id}};
        var token = '{{ csrf_token() }}';

        $.easyAjax({
            type: 'POST',
            url: url,
            data: {'_token':token, 'id':id},
            success: function (response) {
                if(response.status == 'success') {
                    $('#email-'+id).html(response.email);
                    $('#phone-'+id).html(response.phone);
                    $('#resume-'+id).html('<a target="_blank" href="'+response.resume+'" class="btn btn-sm btn-primary">@lang('app.view') @lang('modules.jobApplication.resume')</a>');
                }
            }
        });
    })

    $('#add-note').click(function () {
        var url = "{{ route('staffing_company.applicant-note.store') }}";
        var id = {{$application->id}};
        var note = $('#note_text').val();
        var token = '{{ csrf_token() }}';

        $.easyAjax({
            type: 'POST',
            url: url,
            data: {'_token':token, 'id':id, 'note': note},
            success: function (response) {
                if(response.status == 'success') {
                    $('#applicant-notes').html(response.view);
                    $('#note_text').val('');
                }
            }
        });
    });

    $('body').on('click', '.edit-note', function() {
        $(this).hide();
        let noteId = $(this).data('note-id');
        $('body').find('#note-'+noteId+' .note-text').hide();

        let noteText = $('body').find('#note-'+noteId+' .note-text').html();
        let textArea = '<textarea id="edit-note-text-'+noteId+'" class="form-control" row="4">'+noteText+'</textarea><a class="update-note" data-note-id="'+noteId+'" href="javascript:;"><i class="fa fa-check"></i> @lang("app.save")</a>';
        $('body').find('#note-'+noteId+' .note-textarea').html(textArea);
    });

    $('body').on('click', '.update-note', function () {
        let noteId = $(this).data('note-id');

        var url = "{{ route('staffing_company.applicant-note.update', ':id') }}";
        url = url.replace(':id', noteId);

        var note = $('#edit-note-text-'+noteId).val();
        var token = '{{ csrf_token() }}';

        $.easyAjax({
            type: 'POST',
            url: url,
            data: {'_token':token, 'noteId':noteId, 'note': note, '_method': 'PUT'},
            success: function (response) {
                if(response.status == 'success') {
                    $('#applicant-notes').html(response.view);
                }
            }
        });
    });

    $('body').on('click', '.delete-note', function(){
        let noteId = $(this).data('note-id');
        swal({
            title: "@lang('errors.areYouSure')",
            text: "@lang('errors.deleteWarning')",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "@lang('app.delete')",
            cancelButtonText: "@lang('app.cancel')",
            closeOnConfirm: true,
            closeOnCancel: true
        }, function(isConfirm){
            if (isConfirm) {

                var url = "{{ route('staffing_company.applicant-note.destroy', ':id') }}";
                url = url.replace(':id', noteId);

                var token = '{{ csrf_token() }}';

                $.easyAjax({
                    type: 'POST',
                    url: url,
                    data: {'_token':token, '_method': 'DELETE'},
                    success: function (response) {
                        if(response.status == 'success') {
                            $('#applicant-notes').html(response.view);
                        }
                    }
                });
            }
        });
    });

</script>
@endif
@if(!is_null($application->skype_id))
<script src="https://swc.cdn.skype.com/sdk/v1/sdk.min.js"></script>
@endif