<form class="ajax-form" method="POST" id="ajax-form" name="ajax-form" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="conversations_id" id="conversations_id"
        value="{{ @$conversations_id ? @$conversations_id : '' }}" />
    <input type="hidden" name="job_id" id="job_id" value="{{ @$job_id ? @$job_id : '' }}" />
    <input type="hidden" name="recommendation_relation_id" id="recommendation_relation_id"
        value="{{ @$recommendation_relation_id ? @$recommendation_relation_id : '' }}" />

    <div id="load_interview_feedback">
        <div class="modal-body scroll-y py-0 px-lg-7">
            <div data-kt-element="options">
                <div class="pb-5">


                    <!--begin::Feedback status-->
                    <label class="text-dark fw-bolder fs-5">Conversation Status</label>
                    <div class="row py-5 mb-3">

                        @for ($j = 0; $j <= 5; $j++)
                            @php
                                $sel = '';
                                if (@$conversations->status == $j) {
                                    $sel = 'checked="checked"';
                                }
                            @endphp

                            <div class="col-lg-2">
                                <input type="radio" class="btn-check" name="app_int_feedback_status"
                                    id="app_int_feedback_status_{{ $j }}" value="{{ $j }}"
                                    {{ @$sel }}>
                                <label
                                    class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center"
                                    for="app_int_feedback_status_{{ $j }}">
                                    <span class="d-block fw-bold text-start"> <span
                                            class="text-dark-800 fw-bolder d-block fs-5">
                                            {{ ucwords($feedback_status[$j]['name']) }} </span> </span>
                                </label>
                            </div>
                        @endfor

                    </div>
                    <!--end::Feedback status-->
                    <!--begin::Comment & rating-->
                    <div class="row">
                        <div class="py-5 d-flex align-items-center justify-content-between">
                            <label class="text-dark fw-bolder fs-5">Comment</label>
                            <div class="rating">
                                <!--begin::Star 1-->
                                <label class="rating-label" for="kt_rating_2_input_0"> <span
                                        class="svg-icon svg-icon-1">
                                        RESET
                                    </span> </label>
                                <input class="rating-input" name="rating" value="0" type="radio"
                                    checked="checked" id="kt_rating_2_input_0" />
                                <!--end::Star 1-->

                                <!--begin::Star 1-->
                                <label class="rating-label" for="kt_rating_2_input_1"> <span
                                        class="svg-icon svg-icon-1">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px"
                                            viewBox="0 0 24 24" version="1.1">
                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                <polygon points="0 0 24 0 24 24 0 24" />
                                                <path
                                                    d="M12,18 L7.91561963,20.1472858 C7.42677504,20.4042866 6.82214789,20.2163401 6.56514708,19.7274955 C6.46280801,19.5328351 6.42749334,19.309867 6.46467018,19.0931094 L7.24471742,14.545085 L3.94038429,11.3241562 C3.54490071,10.938655 3.5368084,10.3055417 3.92230962,9.91005817 C4.07581822,9.75257453 4.27696063,9.65008735 4.49459766,9.61846284 L9.06107374,8.95491503 L11.1032639,4.81698575 C11.3476862,4.32173209 11.9473121,4.11839309 12.4425657,4.36281539 C12.6397783,4.46014562 12.7994058,4.61977315 12.8967361,4.81698575 L14.9389263,8.95491503 L19.5054023,9.61846284 C20.0519472,9.69788046 20.4306287,10.2053233 20.351211,10.7518682 C20.3195865,10.9695052 20.2170993,11.1706476 20.0596157,11.3241562 L16.7552826,14.545085 L17.5353298,19.0931094 C17.6286908,19.6374458 17.263103,20.1544017 16.7187666,20.2477627 C16.5020089,20.2849396 16.2790408,20.2496249 16.0843804,20.1472858 L12,18 Z"
                                                    fill="#000000" />
                                            </g>
                                        </svg>
                                    </span> </label>
                                <input class="rating-input" name="rating" value="1" type="radio"
                                    checked="checked" id="kt_rating_2_input_1" />
                                <!--end::Star 1-->
                                <!--begin::Star 2-->
                                <label class="rating-label" for="kt_rating_2_input_2"> <span
                                        class="svg-icon svg-icon-1">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px"
                                            viewBox="0 0 24 24" version="1.1">
                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                <polygon points="0 0 24 0 24 24 0 24" />
                                                <path
                                                    d="M12,18 L7.91561963,20.1472858 C7.42677504,20.4042866 6.82214789,20.2163401 6.56514708,19.7274955 C6.46280801,19.5328351 6.42749334,19.309867 6.46467018,19.0931094 L7.24471742,14.545085 L3.94038429,11.3241562 C3.54490071,10.938655 3.5368084,10.3055417 3.92230962,9.91005817 C4.07581822,9.75257453 4.27696063,9.65008735 4.49459766,9.61846284 L9.06107374,8.95491503 L11.1032639,4.81698575 C11.3476862,4.32173209 11.9473121,4.11839309 12.4425657,4.36281539 C12.6397783,4.46014562 12.7994058,4.61977315 12.8967361,4.81698575 L14.9389263,8.95491503 L19.5054023,9.61846284 C20.0519472,9.69788046 20.4306287,10.2053233 20.351211,10.7518682 C20.3195865,10.9695052 20.2170993,11.1706476 20.0596157,11.3241562 L16.7552826,14.545085 L17.5353298,19.0931094 C17.6286908,19.6374458 17.263103,20.1544017 16.7187666,20.2477627 C16.5020089,20.2849396 16.2790408,20.2496249 16.0843804,20.1472858 L12,18 Z"
                                                    fill="#000000" />
                                            </g>
                                        </svg>
                                    </span> </label>
                                <input class="rating-input" name="rating" value="2" type="radio"
                                    id="kt_rating_2_input_2" />
                                <!--end::Star 2-->
                                <!--begin::Star 3-->
                                <label class="rating-label" for="kt_rating_2_input_3"> <span
                                        class="svg-icon svg-icon-1">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px"
                                            viewBox="0 0 24 24" version="1.1">
                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                <polygon points="0 0 24 0 24 24 0 24" />
                                                <path
                                                    d="M12,18 L7.91561963,20.1472858 C7.42677504,20.4042866 6.82214789,20.2163401 6.56514708,19.7274955 C6.46280801,19.5328351 6.42749334,19.309867 6.46467018,19.0931094 L7.24471742,14.545085 L3.94038429,11.3241562 C3.54490071,10.938655 3.5368084,10.3055417 3.92230962,9.91005817 C4.07581822,9.75257453 4.27696063,9.65008735 4.49459766,9.61846284 L9.06107374,8.95491503 L11.1032639,4.81698575 C11.3476862,4.32173209 11.9473121,4.11839309 12.4425657,4.36281539 C12.6397783,4.46014562 12.7994058,4.61977315 12.8967361,4.81698575 L14.9389263,8.95491503 L19.5054023,9.61846284 C20.0519472,9.69788046 20.4306287,10.2053233 20.351211,10.7518682 C20.3195865,10.9695052 20.2170993,11.1706476 20.0596157,11.3241562 L16.7552826,14.545085 L17.5353298,19.0931094 C17.6286908,19.6374458 17.263103,20.1544017 16.7187666,20.2477627 C16.5020089,20.2849396 16.2790408,20.2496249 16.0843804,20.1472858 L12,18 Z"
                                                    fill="#000000" />
                                            </g>
                                        </svg>
                                    </span> </label>
                                <input class="rating-input" name="rating" value="3" type="radio"
                                    id="kt_rating_2_input_3" />
                                <!--end::Star 3-->
                                <!--begin::Star 4-->
                                <label class="rating-label" for="kt_rating_2_input_4"> <span
                                        class="svg-icon svg-icon-1">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px"
                                            viewBox="0 0 24 24" version="1.1">
                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                <polygon points="0 0 24 0 24 24 0 24" />
                                                <path
                                                    d="M12,18 L7.91561963,20.1472858 C7.42677504,20.4042866 6.82214789,20.2163401 6.56514708,19.7274955 C6.46280801,19.5328351 6.42749334,19.309867 6.46467018,19.0931094 L7.24471742,14.545085 L3.94038429,11.3241562 C3.54490071,10.938655 3.5368084,10.3055417 3.92230962,9.91005817 C4.07581822,9.75257453 4.27696063,9.65008735 4.49459766,9.61846284 L9.06107374,8.95491503 L11.1032639,4.81698575 C11.3476862,4.32173209 11.9473121,4.11839309 12.4425657,4.36281539 C12.6397783,4.46014562 12.7994058,4.61977315 12.8967361,4.81698575 L14.9389263,8.95491503 L19.5054023,9.61846284 C20.0519472,9.69788046 20.4306287,10.2053233 20.351211,10.7518682 C20.3195865,10.9695052 20.2170993,11.1706476 20.0596157,11.3241562 L16.7552826,14.545085 L17.5353298,19.0931094 C17.6286908,19.6374458 17.263103,20.1544017 16.7187666,20.2477627 C16.5020089,20.2849396 16.2790408,20.2496249 16.0843804,20.1472858 L12,18 Z"
                                                    fill="#000000" />
                                            </g>
                                        </svg>
                                    </span> </label>
                                <input class="rating-input" name="rating" value="4" type="radio"
                                    id="kt_rating_2_input_4" />
                                <!--end::Star 4-->
                                <!--begin::Star 5-->
                                <label class="rating-label" for="kt_rating_2_input_5"> <span
                                        class="svg-icon svg-icon-1">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px"
                                            viewBox="0 0 24 24" version="1.1">
                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                <polygon points="0 0 24 0 24 24 0 24" />
                                                <path
                                                    d="M12,18 L7.91561963,20.1472858 C7.42677504,20.4042866 6.82214789,20.2163401 6.56514708,19.7274955 C6.46280801,19.5328351 6.42749334,19.309867 6.46467018,19.0931094 L7.24471742,14.545085 L3.94038429,11.3241562 C3.54490071,10.938655 3.5368084,10.3055417 3.92230962,9.91005817 C4.07581822,9.75257453 4.27696063,9.65008735 4.49459766,9.61846284 L9.06107374,8.95491503 L11.1032639,4.81698575 C11.3476862,4.32173209 11.9473121,4.11839309 12.4425657,4.36281539 C12.6397783,4.46014562 12.7994058,4.61977315 12.8967361,4.81698575 L14.9389263,8.95491503 L19.5054023,9.61846284 C20.0519472,9.69788046 20.4306287,10.2053233 20.351211,10.7518682 C20.3195865,10.9695052 20.2170993,11.1706476 20.0596157,11.3241562 L16.7552826,14.545085 L17.5353298,19.0931094 C17.6286908,19.6374458 17.263103,20.1544017 16.7187666,20.2477627 C16.5020089,20.2849396 16.2790408,20.2496249 16.0843804,20.1472858 L12,18 Z"
                                                    fill="#000000" />
                                            </g>
                                        </svg>
                                    </span> </label>
                                <input class="rating-input" name="rating" value="5" type="radio"
                                    id="kt_rating_2_input_5" />
                                <!--end::Star 5-->
                            </div>
                        </div>
                        <textarea name="feedback_comment" id="feedback_comment" class="form-control form-control-solid" rows="5"
                            placeholder="Write comment here..."></textarea>
                    </div>
                    <!--end::Comment & rating-->
                </div>
            </div>
            <div class="modal-footer flex-center">
                <div class="div1">
                    <!--begin::Cancel-->
                    <button type="button" id="" class="btn btn-secondary me-3"
                        data-bs-dismiss="modal">Cancel</button>
                    <!--end::Cancel-->
                    <!--begin::Submit-->
                    <button type="button" id="submit-frm-btn" class="btn btn-primary"> <span
                            class="indicator-label">Submit</span> <span class="indicator-progress">Please wait...
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span> </button>
                    <!--end::Submit-->
                </div>
                <div class="div2" style="position: absolute;right: 2%;">
                    <!--begin::Rescheduled-->

                    <!--end::Rescheduled-->
                </div>
            </div>



<div class="timeline timeline-3">
  <div class="timeline-items">
      <div id="applicant-notes" class="col-sm-12">
        <ul class="list-unstyled ">
          @if($conversations_log)
              @foreach($conversations_log as $conversation_log)
                    <li class="media timeline-items">

                        <div class="timeline-item">


                            <div class="timeline-media">
                                <span class="svg-icon svg-icon-warning svg-icon-2x">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                        height="24px" viewBox="0 0 24 24" version="1.1">
                                        <g stroke="none" stroke-width="1" fill="none"
                                            fill-rule="evenodd">
                                            <polygon points="0 0 24 0 24 24 0 24"></polygon>
                                            <path
                                                d="M12.9336061,16.072447 L19.36,10.9564761 L19.5181585,10.8312381 C20.1676248,10.3169571 20.2772143,9.3735535 19.7629333,8.72408713 C19.6917232,8.63415859 19.6104327,8.55269514 19.5206557,8.48129411 L12.9336854,3.24257445 C12.3871201,2.80788259 11.6128799,2.80788259 11.0663146,3.24257445 L4.47482784,8.48488609 C3.82645598,9.00054628 3.71887192,9.94418071 4.23453211,10.5925526 C4.30500305,10.6811601 4.38527899,10.7615046 4.47382636,10.8320511 L4.63,10.9564761 L11.0659024,16.0730648 C11.6126744,16.5077525 12.3871218,16.5074963 12.9336061,16.072447 Z"
                                                fill="#000000" fill-rule="nonzero"></path>
                                            <path
                                                d="M11.0563554,18.6706981 L5.33593024,14.122919 C4.94553994,13.8125559 4.37746707,13.8774308 4.06710397,14.2678211 C4.06471678,14.2708238 4.06234874,14.2738418 4.06,14.2768747 L4.06,14.2768747 C3.75257288,14.6738539 3.82516916,15.244888 4.22214834,15.5523151 C4.22358765,15.5534297 4.2250303,15.55454 4.22647627,15.555646 L11.0872776,20.8031356 C11.6250734,21.2144692 12.371757,21.2145375 12.909628,20.8033023 L19.7677785,15.559828 C20.1693192,15.2528257 20.2459576,14.6784381 19.9389553,14.2768974 C19.9376429,14.2751809 19.9363245,14.2734691 19.935,14.2717619 L19.935,14.2717619 C19.6266937,13.8743807 19.0546209,13.8021712 18.6572397,14.1104775 C18.654352,14.112718 18.6514778,14.1149757 18.6486172,14.1172508 L12.9235044,18.6705218 C12.377022,19.1051477 11.6029199,19.1052208 11.0563554,18.6706981 Z"
                                                fill="#000000" opacity="0.3"></path>
                                        </g>
                                    </svg>
                                </span>
                            </div>

                            <div class="timeline-content">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div class="me-2 fs-6"> <a href="javascript:void(0);"
                                            class="text-dark text-hover-primary fw-bolder me-2"><span
                                                class="text-muted me-2">{{ date("d M Y h:i: A",strtotime( $conversation_log->created_at)) }}</span></a>
                                    </div>
                                    <div class="dropdown ml-2" data-toggle="tooltip" title=""
                                        data-placement="left" data-original-title="New Project">
                                        <a href="javascript:;" class="delete-note" data-note-id="{{  $conversation_log->id }}"><svg
                                                class="svg-inline--fa fa-trash ml-1 text-danger"
                                                aria-hidden="true" focusable="false" data-prefix="fas"
                                                data-icon="trash" role="img"
                                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"
                                                data-fa-i2svg="">
                                                <path fill="currentColor"
                                                    d="M53.21 467c1.562 24.84 23.02 45 47.9 45h245.8c24.88 0 46.33-20.16 47.9-45L416 128H32L53.21 467zM432 32H320l-11.58-23.16c-2.709-5.42-8.25-8.844-14.31-8.844H153.9c-6.061 0-11.6 3.424-14.31 8.844L128 32H16c-8.836 0-16 7.162-16 16V80c0 8.836 7.164 16 16 16h416c8.838 0 16-7.164 16-16V48C448 39.16 440.8 32 432 32z">
                                                </path>
                                            </svg><!-- <i class="fa fa-trash ml-1 text-danger"></i> Font Awesome fontawesome.com --></a>
                                    </div>
                                </div>
                                <p class="p-0 fs-6 comment-para note-text">{{  $conversation_log->comment }}</p>
                                <div class="note-textarea"></div>
                            </div>
                        </div>
                    </li>
              @endforeach
          @endif
            
        </ul>
      </div>
  </div>
</div>

</div>
</form>


<script>
    $('#submit-frm-btn').click(function() {

        var app_int_feedback_status = $('input[name="app_int_feedback_status"]:checked').val();
        //var  rating2					=	$('input[name="rating2"]:checked').val();   
        var feedback_comment = $('#feedback_comment').val();

        var id = $('#conversations_id').val();
        var job_id = $('#job_id').val();
        var recommendation_relation_id = $('#recommendation_relation_id').val();
        var rating = $("input[name='rating']:checked").val();
        var url = "{{ route('superadmin.company.updateInterview', ':id') }}";
        url = url.replace(':id', id);
        var token = "{{ csrf_token() }}";
        var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
        if (feedback_comment == '') {
            Swal.fire("Required", "Comment is required", "warning")
            return false;
        }

        $("#submit-frm-btn").prop("disabled", true);
        $('.indicator-label').hide();
        $('.indicator-progress').show();
        $.ajax({
            type: 'POST',
            url: url,
            beforeSend: function() {
                $('#modal-content-body').html('<img src="' + baseurl +
                    '" height="35px" width="100%">');
            },

            data: {
                '_token': token,
                '_method': 'POST',
                'id': id,
                'conversations_id': id,
                'app_int_feedback_status': app_int_feedback_status,
                'feedback_comment': feedback_comment,
                'job_id': job_id,
                'rating': rating,
                'recommendation_relation_id': recommendation_relation_id
            },
            success: function(response) {
                if (response.status == "success") {
                  $("#submit-frm-btn").prop("disabled", false);
                  $('.indicator-label').show();
                  $('.indicator-progress').hide();
                    // table._fnDraw();
                    fetchJobRequestAcceptAjax();
                    fetchjobrequestpending();
                    // alert(response.date_set);
                    // console.log(response.logsValue);
                    Swal.fire("Success", "Interview Feedback submit successfully!", "success").then(
                        function() {
                          var list = '<li class="media timeline-items"> <div class="timeline-item"> <div class="timeline-media"> <span class="svg-icon svg-icon-warning svg-icon-2x"> <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1"> <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"> <polygon points="0 0 24 0 24 24 0 24"></polygon> <path d="M12.9336061,16.072447 L19.36,10.9564761 L19.5181585,10.8312381 C20.1676248,10.3169571 20.2772143,9.3735535 19.7629333,8.72408713 C19.6917232,8.63415859 19.6104327,8.55269514 19.5206557,8.48129411 L12.9336854,3.24257445 C12.3871201,2.80788259 11.6128799,2.80788259 11.0663146,3.24257445 L4.47482784,8.48488609 C3.82645598,9.00054628 3.71887192,9.94418071 4.23453211,10.5925526 C4.30500305,10.6811601 4.38527899,10.7615046 4.47382636,10.8320511 L4.63,10.9564761 L11.0659024,16.0730648 C11.6126744,16.5077525 12.3871218,16.5074963 12.9336061,16.072447 Z" fill="#000000" fill-rule="nonzero"></path> <path d="M11.0563554,18.6706981 L5.33593024,14.122919 C4.94553994,13.8125559 4.37746707,13.8774308 4.06710397,14.2678211 C4.06471678,14.2708238 4.06234874,14.2738418 4.06,14.2768747 L4.06,14.2768747 C3.75257288,14.6738539 3.82516916,15.244888 4.22214834,15.5523151 C4.22358765,15.5534297 4.2250303,15.55454 4.22647627,15.555646 L11.0872776,20.8031356 C11.6250734,21.2144692 12.371757,21.2145375 12.909628,20.8033023 L19.7677785,15.559828 C20.1693192,15.2528257 20.2459576,14.6784381 19.9389553,14.2768974 C19.9376429,14.2751809 19.9363245,14.2734691 19.935,14.2717619 L19.935,14.2717619 C19.6266937,13.8743807 19.0546209,13.8021712 18.6572397,14.1104775 C18.654352,14.112718 18.6514778,14.1149757 18.6486172,14.1172508 L12.9235044,18.6705218 C12.377022,19.1051477 11.6029199,19.1052208 11.0563554,18.6706981 Z" fill="#000000" opacity="0.3"></path> </g> </svg> </span> </div> <div class="timeline-content"> <div class="d-flex align-items-center justify-content-between mb-3"> <div class="me-2 fs-6"> <a href="javascript:void(0);" class="text-dark text-hover-primary fw-bolder me-2"><span class="text-muted me-2">'+response.date_set+'</span></a>  </div> <div class="dropdown ml-2" data-toggle="tooltip" title="" data-placement="left" data-original-title="New Project"> <a href="javascript:;" class="delete-note" data-note-id="'+response.log_id+'"><svg class="svg-inline--fa fa-trash ml-1 text-danger" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="trash" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" data-fa-i2svg=""> <path fill="currentColor" d="M53.21 467c1.562 24.84 23.02 45 47.9 45h245.8c24.88 0 46.33-20.16 47.9-45L416 128H32L53.21 467zM432 32H320l-11.58-23.16c-2.709-5.42-8.25-8.844-14.31-8.844H153.9c-6.061 0-11.6 3.424-14.31 8.844L128 32H16c-8.836 0-16 7.162-16 16V80c0 8.836 7.164 16 16 16h416c8.838 0 16-7.164 16-16V48C448 39.16 440.8 32 432 32z"> </path> </svg><!-- <i class="fa fa-trash ml-1 text-danger"></i> Font Awesome fontawesome.com --></a> </div> </div> <p class="p-0 fs-6 comment-para note-text">'+response.logsValue.comment+'</p> <div class="note-textarea"></div> </div> </div> </li>';
                            $('#feedback_comment').val('');
                            $('.list-unstyled').prepend(list);
                        });
                }
            }
        });
    });
   $('.delete-note').click(function(){
        let noteId = $(this).data('note-id');
        var ele = $(this);
        var url 		= "{{ route('superadmin.company.notedestroy', ':noteId') }}";
        url 			= url.replace(':id', noteId);
        var token 		= '{{ csrf_token() }}';
        $.ajax({
   							type: 'POST',
   							url: url,
   							data: {'_token':token, '_method': 'POST','noteId':noteId},
   							success: function (response) {
   								if(response.status == 'success') {
                    Swal.fire("Success", "Your text note has been deleted", "success");
                    ele.closest('.timeline-items').remove();	
   								}
   							}
   						});
         return false;
   });
  

</script>
