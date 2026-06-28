@extends('layouts.superadmin_layout')

<!--begin::external css-->
<style>
body
{
    overflow-x: hidden;
}
</style>
<!--end::external css-->

@section('content')

<!--begin::toolbar-->
<div class="toolbar" id="kt_toolbar" style="">
	<div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
        <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
            <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">Smtp Settings</h1>
        </div>
	</div>
</div>
<!--end::toolbar-->

<div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form id="editSettings" class="ajax-form">
                        @csrf
                        @method('PUT')
                      
                        <div class="form-group">
                            <label for="name">@lang('app.mailDriver')</label>
                            <div class="form-group">
                                <label class="radio-inline">
                                    <input type="radio" class="checkbox" onchange="getDriverValue(this);" value="smtp"
                                       checked name="mail_driver">SMTP
                                    </label>
                                   
                            </div>
                        </div> 
                        @if(@$smtpSetting)

                        @foreach($smtpSetting as $val)

                            <div class="mb-5" id="smtp_div" style="background: #f5f5f5; padding: 16px; border-radius: 11px;">
                                <div class="page-title d-flex align-items-center me-3 mb-5 mt-lg-8">
                                    <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">{{  $val->mailtype }}</h1>
                                </div>

                                <div class="row">
                                    <div class="col-6 form-group">
                                        <label for="email">@lang('app.mailHost')</label>
                                        <input type="email" class="form-control" id="mail_host_{{ $val->id }}" name="mail_host[{{ $val->id }}]" value="{{ $val->mail_host }}">
                                    </div>
                                    <div class="col-6 form-group">
                                        <label for="mail_port">@lang('app.mailPort')</label>
                                        <input type="text" class="form-control" id="mail_port_{{ $val->id }}" name="mail_port[{{ $val->id }}]" value="{{ $val->mail_port }}">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-4 form-group">
                                        <label for="mail_username">@lang('app.mailUsername')</label>
                                        <input type="text" class="form-control" id="mail_username_{{ $val->id }}" name="mail_username[{{ $val->id }}]" value="{{ $val->mail_username }}">
                                    </div>
                                    <div class="col-4 form-group">
                                        <label for="mail_password">@lang('app.mailPassword')</label>
                                        <input type="text" class="form-control" id="mail_password_{{ $val->id }}" name="mail_password[{{ $val->id }}]" value="{{ $val->mail_password }}">
                                    </div>
                                    <div class="col-4 form-group">
                                        <label for="mail_from_email">@lang('app.mailEncryption')</label>
                                        <select class="form-control" name="mail_encryption[{{ $val->id }}]" id="mail_encryption_{{ $val->id }}">
                                            <option value="tls" @if($val->mail_encryption == 'tls') selected @endif>
                                                @lang('app.tls')
                                            </option>
                                            <option value="ssl" @if($val->mail_encryption == 'ssl') selected @endif>
                                                @lang('app.ssl')
                                            </option>
                                            <option value="none" @if($val->mail_encryption == null) selected @endif>
                                                @lang('app.none')
                                            </option>
                                        </select>
                                    </div>
                                </div>


                                <div class="row">
                                    <div class="col-4 form-group">
                                        <label for="email">From Email</label>
                                        <input type="email" class="form-control" id="from_mail_{{ $val->id }}t" name="from_mail[{{ $val->id }}]" value="{{ $val->mail_from_email }}">
                                    </div>
                                    <div class="col-4 form-group">
                                        <label for="mail_port">Status</label>
                                        <select class="form-control" name="status[{{ $val->id }}]" id="status_{{ $val->id }}">
                                            <option value="0" @if($val->status == 0) selected @endif>
                                                Active
                                            </option>
                                            <option value="1" @if($val->status == 1) selected @endif>
                                               Inactive
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-4 form-group">
                                        <label for="email">Daily Mail used</label>
                                        <input type="email" class="form-control" id="daily_used_{{ $val->id }}" name="daily_used[{{ $val->id }}]" value="{{ $val->dailyCount }}">
                                    </div>
                                </div>

                                
                            </div>
                        @endforeach    

                        @endif


                        {{-- <div class="mb-5" id="mass_mail_smtp_div" style="background: #f5f5f5; padding: 16px; border-radius: 11px;">
                           
                                <div class="page-title d-flex align-items-center me-3 mb-5 mt-lg-1">
                                    <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">Mass Smtp Settings</h1>
                                </div>
                            

                            <div class="row">
                                <div class="col-6 form-group">
                                    <label for="mass_mail_host">@lang('app.mailHost')</label>
                                    <input type="email" class="form-control" id="mass_mail_host" name="mass_mail_host" value="{{ $smtpSetting->mass_mail_host }}">
                                </div>
                                <div class="col-6 form-group">
                                    <label for="mass_mail_port">@lang('app.mailPort')</label>
                                    <input type="text" class="form-control" id="mass_mail_port" name="mass_mail_port" value="{{ $smtpSetting->mass_mail_port }}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-4 form-group">
                                    <label for="mass_mail_username">@lang('app.mailUsername')</label>
                                    <input type="text" class="form-control" id="mass_mail_username" name="mass_mail_username" value="{{ $smtpSetting->mass_mail_username }}">
                                </div>
                                <div class="col-4 form-group">
                                    <label for="mass_mail_password">@lang('app.mailPassword')</label>
                                    <input type="text" class="form-control" id="mass_mail_password" name="mass_mail_password" value="{{ $smtpSetting->mass_mail_password }}">
                                </div>
                                <div class="col-4 form-group">
                                    <label for="mass_mail_encryption">@lang('app.mailEncryption')</label>
                                    <select class="form-control" name="mass_mail_encryption" id="mass_mail_encryption">
                                        <option value="tls" @if($smtpSetting->mass_mail_encryption == 'tls') selected @endif>
                                            @lang('app.tls')
                                        </option>
                                        <option value="ssl" @if($smtpSetting->mass_mail_encryption == 'ssl') selected @endif>
                                            @lang('app.ssl')
                                        </option>
                                        <option value="none" @if($smtpSetting->mass_mail_encryption == null) selected @endif>
                                            @lang('app.none')
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-5" id="notification_mail_smtp_div" style="background: #f5f5f5; padding: 16px; border-radius: 11px;">
                            <div class="page-title d-flex align-items-center me-3 mb-5 mt-lg-1">
                                <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">Notification Smtp Settings</h1>
                            </div>
                            <div class="row">
                                <div class="col-6 form-group">
                                    <label for="notification_mail_host">@lang('app.mailHost')</label>
                                    <input type="email" class="form-control" id="notification_mail_host" name="notification_mail_host" value="{{ $smtpSetting->notification_mail_host }}">
                                </div>
                                <div class="col-6 form-group">
                                    <label for="notification_mail_port">@lang('app.mailPort')</label>
                                    <input type="text" class="form-control" id="notification_mail_port" name="notification_mail_port" value="{{ $smtpSetting->notification_mail_port }}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-4 form-group">
                                    <label for="notification_mail_username">@lang('app.mailUsername')</label>
                                    <input type="text" class="form-control" id="notification_mail_username" name="notification_mail_username" value="{{ $smtpSetting->notification_mail_username }}">
                                </div>
                                <div class="col-4 form-group">
                                    <label for="notification_mail_password">@lang('app.mailPassword')</label>
                                    <input type="text" class="form-control" id="notification_mail_password" name="notification_mail_password" value="{{ $smtpSetting->notification_mail_password }}">
                                </div>
                                <div class="col-4 form-group">
                                    <label for="notification_mail_encryption">@lang('app.mailEncryption')</label>
                                    <select class="form-control" name="notification_mail_encryption" id="notification_mail_encryption">
                                        <option value="tls" @if($smtpSetting->notification_mail_encryption == 'tls') selected @endif>
                                            @lang('app.tls')
                                        </option>
                                        <option value="ssl" @if($smtpSetting->notification_mail_encryption == 'ssl') selected @endif>
                                            @lang('app.ssl')
                                        </option>
                                        <option value="none" @if($smtpSetting->notification_mail_encryption == null) selected @endif>
                                            @lang('app.none')
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div> --}}



                        <div class="form-group d-none">
                            <label for="mail_from_name">@lang('app.mailFromName')</label>
                            <input type="text" class="form-control" id="mail_from_name" name="mail_from_name" value="dddd">
                        </div>
                        <div class="form-group d-none">
                            <label for="mail_from_email">@lang('app.mailFromEmail')</label>
                            <input type="email" class="form-control" id="mail_from_email" name="mail_from_email" value="dddd">
                        </div>

                        <div class="row justify-content-center">
                            <div class="col-4 form-group d-flex justify-content-center">
                                <button type="button" id="save-form" class="btn btn-success waves-effect waves-light m-r-10">@lang('app.save')</button>
                                <button type="reset" class="btn btn-inverse waves-effect waves-light">@lang('app.reset')</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('footer-script')
    <script>
        // Update Mail Setting
        $('#save-form').click(function ()
        {
            $.ajax({
                url: '{{route('superadmin.smtp-settings.update', 1)}}',
                type: "POST",
                container: '#editSettings',
                messagePosition: "inline",
                data: $('#editSettings').serialize(),
                success: function (response)
                {
                    if (response.status == 'error') {
                        $('#alert').prepend('<div class="alert alert-danger">{{__('messages.smtpError')}}</div>')
                    } else {
                        $('#alert').show();
                        alert('Updated Successfully');
                    }
                }
            })
        });

        $('#send-test-email').click(function ()
        {
            $('#testMailModal').modal('show')
        });
        $('#send-test-email-submit').click(function ()
        {
            $.ajax({
                url: '{{route('superadmin.smtp-settings.sendTestEmail')}}',
                type: "GET",
                messagePosition: "inline",
                container: "#testEmail",
                data: $('#testEmail').serialize()

            })
        });


        function getDriverValue(sel) {
            if (sel.value == 'mail') {
                $('#smtp_div').hide();
                $('#alert').hide();
            } else {
                $('#smtp_div').show();
                $('#alert').show();
            }
        }

       
    </script>

@endpush