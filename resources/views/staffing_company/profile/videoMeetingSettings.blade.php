@extends('layouts.staffing_company_layout')

@push('head-script')
<link rel="stylesheet" href="{{ asset('assets/node_modules/dropify/dist/css/dropify.min.css') }}<?php echo TM_VERSION; ?>">
<style>
.content-header
{
    display: none;
}
.bootstrap-select>.dropdown-toggle
{
    background-color: #efefef;
    border-color: #e8e8e8;
    color: #404040;
    outline: none;
    border: none;
}
.btn-info:not(:disabled):not(.disabled).active,
.btn-info:not(:disabled):not(.disabled):active,
.show > .btn-info.dropdown-toggle
{
    background-color: #efefef;
    border-color: #e8e8e8;
    color: #000;
    outline: none;
    border: none;
}
.bootstrap-select .dropdown-toggle .filter-option:focus
{
    outline: none;
    border: none;
}

/*Radio btn css*/
.radio-tile-group
{
    display: flex;
    flex-wrap: wrap;
    justify-content: left;
}
.radio-tile-group .input-container{position:relative;height:2.6rem;width:11.3rem;}
.radio-tile-group .input-container .radio-button{opacity: 0;position: absolute;top: 0;left: 0;height: 100%;width: 100%;margin: 0;cursor: pointer;height:5vh}
.radio-tile-group .input-container .radio-tile{display: flex;flex-direction: column;align-items: center;justify-content: center;width: 100%;height: 100%;border: 2px solid #079ad9;border-radius: 3px;padding: .9rem;transition: transform 300ms ease;}
.radio-tile-group .input-container .radio-tile-label{text-align: center;text-transform: capitalize;color: #079ad9;font-size: 13px;margin:0;}
.radio-tile-group .input-container .radio-button:checked + .radio-tile{background-color: #079ad9;border:0px;color: white;transform: scale(1.1, 1.1);}
.modal-header{outline: 0;padding: 15px 0px 15px 0px!important;}
.radio-tile-group .input-container .radio-button:checked + .radio-tile .radio-tile-label
{color: white;background-color: #079ad9;margin:0;font-size: 13px;}
.modal-header{outline: 0;padding: 15px 0px 15px 0px!important;}
.radio-tile-group .input-container .radio-button:checked + .radio-tile .radio-tile-label
{color: white;background-color: #079ad9;margin:0;}
/*Radio btn css end*/

</style>
@endpush
 
@section('content')
<?php 
		//echo '<pre>'; print_r($user); exit; 
?>
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title pt-2 pb-2"> <i class="fa fa-cogs"></i>&nbsp;Video Meeting Settings </h4>
        <form id="editSettings" class="ajax-form">
          @csrf
          @method('POST')
          <div class="row pt-3">
            <div class="col-12">
              <h4 class="text-dark border-bottom p-2 bg-light-secondary mb-1"> <i class="fa fa-info-circle"></i>&nbsp;Zoom Meeting </h4>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="name">ZOOM STATUS</label>
                <select name="zoom_status" id="zoom_status" class="form-control">
                  <option value="">Status</option>
                  <option value="1">Active</option>
                  <option value="0">InActive</option>
                </select>
              </div>
            </div>
          </div>
          <div class="row pt-2">
            <div class="col-md-6">
              <div class="form-group">
                <label for="email">ZOOM API KEY</label>
                <input type="text" class="form-control" id="zoom_api_key" name="zoom_api_key" value="{{ $company->zoom_api_key }}" placeholder="ZOOM API KEY">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="company_phone">ZOOM API SECRET</label>
                <input type="text" class="form-control" id="zoom_api_secret" name="zoom_api_secret" value="{{ $company->zoom_api_secret }}" placeholder="ZOOM API SECRET">
                <!-- <span class="help-block">Leave blank to keep current password</span> -->
              </div>
            </div>
          </div>
          <div class="row pt-3">
            <div class="col-12">
              <h4 class="text-dark border-bottom p-2 bg-light-secondary mb-1"> <i class="fa fa-info-circle"></i>&nbsp;Google Meeting </h4>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="name">GOOGLE STATUS</label>
                <select name="google_status" id="google_status" class="form-control">
                  <option value="">Status</option>
                  <option value="1" <?php echo @$google_status_active; ?> >Active</option>
                  <option value="0" <?php echo @$google_status_inactive; ?> >InActive</option>
                </select>
              </div>
            </div>
          </div>
          <div class="row pt-2">
            <div class="col-md-6">
              <div class="form-group">
                <label for="email">GOOGLE API KEY</label>
                <input type="text" class="form-control" id="google_api_key" name="google_api_key" value="{{ $company->google_api_key }}" placeholder="GOOGLE API KEY">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="company_phone">GOOGLE API SECRET</label>
                <input type="text" class="form-control" id="google_api_secret" name="google_api_secret" value="{{ $company->google_api_secret }}" placeholder="GOOGLE API SECRET">
                <!-- <span class="help-block">Leave blank to keep current password</span> -->
              </div>
            </div>
          </div>
          <div class="row pt-3">
            <div class="col-12">
              <h4 class="text-dark border-bottom p-2 bg-light-secondary mb-1"> <i class="fa fa-info-circle"></i>&nbsp; Microsoft Teams Meeting </h4>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="name">MICROSOFT STATUS</label>
                <select name="microsoft_status" id="microsoft_status" class="form-control">
                  <option value="">Status</option>
                  <option value="1">Active</option>
                  <option value="0">InActive</option>
                </select>
              </div>
            </div>
          </div>
          <div class="row pt-2">
            <div class="col-md-6">
              <div class="form-group">
                <label for="email">MICROSOFT API KEY</label>
                <input type="text" class="form-control" id="microsoft_api_key" name="microsoft_api_key" value="{{ $company->microsoft_api_key }}" placeholder="MICROSOFT API KEY">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="company_phone">MICROSOFT API SECRET</label>
                <input type="text" class="form-control" id="microsoft_api_secret" name="microsoft_api_secret" value="{{ $company->microsoft_api_secret }}" placeholder="MICROSOFT API SECRET">
                <!-- <span class="help-block">Leave blank to keep current password</span> -->
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-12 text-center pt-4">
              <button type="button" id="save-form" class="btn btn-light-success btn-sm mr-2"> <i class="fa fa-save"></i>&nbsp;Save & Submit </button>
              <button type="reset" class="btn btn-light-warning btn-sm"> <i class="fa fa-spinner"></i> @lang('app.reset') </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@push('footer-script')
<script src="{{ asset('assets/node_modules/dropify/dist/js/dropify.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script>
        $('.dropify').dropify({
            messages: {
                default: '@lang("app.dragDrop")',
                replace: '@lang("app.dragDropReplace")',
                remove: '@lang("app.remove")',
                error: '@lang('app.largeFile')'
            }
        });

        $('body').on('click', '#change-mobile', function () {
            $.easyAjax({
                url: "{{ route('changeMobile') }}",
                container: '#verify-mobile',
                type: "GET",
                success: function (response) {
                    $('#verify-mobile').html(response.view);
                    $('.selectpicker').selectpicker({
                        style: 'btn-info',
                        size: 4
                    });
                }
            })
        });

        $('#save-form').click(function () {
            $.easyAjax({
                url: '{{route('staffing_company.profile.videoupdate', $user->id)}}',
                container: '#editSettings',
                type: "POST",
                redirect: true,
                file: true
            })
        });
    </script>
<script>
        var x = '';
        
        function clearLocalStorage() {
            localStorage.removeItem('otp_expiry');
            localStorage.removeItem('otp_attempts');
        }

        function checkSessionAndRemove() {
            $.easyAjax({
                url: '{{ route('removeSession') }}',
                type: 'GET',
                data: {'sessions': ['verify:request_id']}
            })
        }

        function startCounter(deadline) {
            x = setInterval(function() {
                var now = new Date().getTime();
                var t = deadline - now;

                var days = Math.floor(t / (1000 * 60 * 60 * 24));
                var hours = Math.floor((t%(1000 * 60 * 60 * 24))/(1000 * 60 * 60));
                var minutes = Math.floor((t % (1000 * 60 * 60)) / (1000 * 60));
                var seconds = Math.floor((t % (1000 * 60)) / 1000);

                $('#demo').html('Time Left: '+minutes + ":" + seconds);
                $('.attempts_left').html(`${localStorage.getItem('otp_attempts')} attempts left`);

                if (t <= 0) {
                    clearInterval(x);
                    clearLocalStorage();
                    checkSessionAndRemove();
                    location.href = '{{ route('staffing_company.profile.index') }}'
                }
            }, 1000);
        }

        if (localStorage.getItem('otp_expiry') !== null) {
            let localExpiryTime = localStorage.getItem('otp_expiry');
            let now = new Date().getTime();

            if (localExpiryTime - now < 0) {
                clearLocalStorage();
                checkSessionAndRemove();
            }
            else {
                $('#otp').focus().select();
                startCounter(localStorage.getItem('otp_expiry'));
            }
        }

        function sendOTPRequest() {
            $.easyAjax({
                url: '{{ route('sendOtpCode.account') }}',
                type: 'POST',
                container: '#request-otp-form',
                messagePosition: 'inline',
                data: $('#request-otp-form').serialize(),
                success: function (response) {
                    if (response.status == 'success') {
                        localStorage.setItem('otp_attempts', 3);

                        $('#verify-mobile').html(response.view);
                        $('.attempts_left').html(`3 attempts left`);

                        let html = `<div class="alert alert-info mb-0" role="alert">
                            @lang('messages.info.verifyAlert')
                            <a href="{{ route('staffing_company.profile.index') }}" class="btn btn-warning">
                                @lang('menu.profile')
                            </a>
                        </div>`;

                        $('#verify-mobile-info').html(html);
                        $('#otp').focus();

                        var now = new Date().getTime();
                        var deadline = new Date(now + parseInt('{{ config('nexmo.settings.pin_expiry') }}')*1000).getTime();

                        localStorage.setItem('otp_expiry', deadline);
                        // intialize countdown
                        startCounter(deadline);
                    }
                    if (response.status == 'fail') {
                        $('#mobile').focus();
                    }
                }
            });
        }

        function sendVerifyRequest() {
            $.easyAjax({
                url: '{{ route('verifyOtpCode.account') }}',
                type: 'POST',
                container: '#verify-otp-form',
                messagePosition: 'inline',
                data: $('#verify-otp-form').serialize(),
                success: function (response) {
                    if (response.status == 'success') {
                        clearLocalStorage();

                        $('#verify-mobile').html(response.view);
                        $('#verify-mobile-info').html('');

                        // select2 reinitialize
                        $('.selectpicker').selectpicker({
                            style: 'btn-info',
                            size: 4
                        });
                    }
                    if (response.status == 'fail') {
                        // show number of attempts left
                        let currentAttempts = localStorage.getItem('otp_attempts');

                        if (currentAttempts == 1) {
                            clearLocalStorage();
                        }
                        else {
                            currentAttempts -= 1;

                            $('.attempts_left').html(`${currentAttempts} attempts left`);
                            $('#otp').focus().select();
                            localStorage.setItem('otp_attempts', currentAttempts);
                        }

                        if (Object.keys(response.data).length > 0) {
                            $('#verify-mobile').html(response.data.view);

                            // select2 reinitialize
                            $('.selectpicker').selectpicker({
                                style: 'btn-info',
                                size: 4
                            });

                            clearInterval(x);
                        }
                    }
                }
            });
        }

        $('body').on('submit', '#request-otp-form', function (e) {
            e.preventDefault();
            sendOTPRequest();
        })

        $('body').on('click', '#request-otp', function () {
            sendOTPRequest();
        })

        $('body').on('submit', '#verify-otp-form', function (e) {
            e.preventDefault();
            sendVerifyRequest();
        })

        $('body').on('click', '#verify-otp', function() {
            sendVerifyRequest();
        })
    </script>
@endpush