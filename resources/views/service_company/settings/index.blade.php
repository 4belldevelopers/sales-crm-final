@extends('layouts.service_company_layout')

@push('head-script')
<link rel="stylesheet" href="{{ asset('assets/node_modules/dropify/dist/css/dropify.min.css') }}<?php echo TM_VERSION; ?>">
<style>
.content-header{display: none;}
</style>
@endpush

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title pt-2 pb-2"> <i class="fa fa-building"></i>&nbsp;Business Setting </h4>
        <form id="editSettings" class="ajax-form">
          @csrf
          @method('PUT')
          <div class="row pt-2">
            <div class="col-md-4 offset-md-4">
              <div class="form-group">
                <div class="card">
                  <div class="card-body">
                    <input type="file" id="input-file-now" name="logo" class="dropify"
                                                data-default-file="{{ $global->logo_url }}"
                                            />
                    <div class="col-12 text-center">
                      <label for="exampleInputPassword1"> Upload&nbsp;@lang('modules.accountSettings.companyLogo') </label>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label for="company_name">@lang('modules.accountSettings.companyName')</label>
                <input type="text" class="form-control" id="company_name" name="company_name" value="{{ $global->company_name }}" placeholder="Enter Company Name">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="company_name">@lang('modules.accountSettings.companyUrlLink')</label>
                <input type="text" class="form-control" id="company_slug" name="company_slug" value="{{ $global->career_page_link }}" placeholder="Enter Company URL">
                <input type="hidden" id="slug" value="{{ $global->career_page_link }}" name="slug" placeholder="Enter Company URL">
                <span class="text-success">@lang('modules.accountSettings.companyUrlLinkWillBe') </span> : <span class="font-weight-bold " style="border: none" id="slugValue">{{ $global->career_page_link }}</span> </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="company_email">@lang('modules.accountSettings.companyEmail')</label>
                <input type="email" class="form-control" id="company_email" name="company_email" value="{{ $global->company_email }}" placeholder="Enter Company Email" maxlength="254" inputmode="email" autocomplete="email" autocapitalize="none" spellcheck="false">
                <div id="company_email_typo_hint" class="email-typo-hint" style="display: none; margin-top: 5px; font-size: 11.5px; color: #b45309; background: #fef3c7; border: 1px solid #fde68a; border-radius: 6px; padding: 4px 8px;"></div>
              </div>
            </div>
          </div>
          <div class="row pt-2">
            <div class="col-md-4">
              <div class="form-group">
                <label for="company_phone">@lang('modules.accountSettings.companyPhone')</label>
                <x-jero-phone-input
                  id="company_phone"
                  name="company_phone"
                  countryCodeName="company_phone_code"
                  countryCodeId="company_phone_code"
                  label=""
                  :required="false"
                  :value="$global->company_phone"
                  :countryCode="$global->calling_code ?: '+91'"
                  placeholder="Enter Company Phone"
                />
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="exampleInputPassword1">@lang('modules.accountSettings.companyWebsite')</label>
                <input type="text" class="form-control" id="website" name="website" value="{{ $global->website }}" placeholder="Enter Company Website">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="exampleInputPassword1">@lang('modules.accountSettings.jobOpeningHeading')</label>
                <input type="text" class="form-control" id="job_opening_title" name="job_opening_title" value="{{ $global->job_opening_title }}" placeholder="Enter Career Page Heading">
              </div>
            </div>
          </div>
          <div class="row pt-2">
            <div class="col-md-6">
              <div class="form-group">
                <label for="address">@lang('modules.accountSettings.defaultTimezone')</label>
                <select name="timezone" id="timezone" class="form-control select2 custom-select">
                  
                                        @foreach($timezones as $tz)
                                            
                  <option @if($global->timezone == $tz) selected @endif>{{ $tz }}</option>
                  
                                        @endforeach
                                    
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="address" class="mb-1">@lang('modules.accountSettings.changeLanguage')</label>
                <select class="form-control" name="locale">
                  
                                        @foreach($languageSettings as $language)
                                            
                  <option value="{{ $language->language_code }}" @if($global->locale == $language->language_code) selected @endif  data-content='<span class="flag-icon flag-icon-{{ $language->language_code }}"></span> {{ $language->language_name }}'>{{ $language->language_name }}</option>
                  
                                        @endforeach
                                    
                </select>
              </div>
            </div>
          </div>
          <hr>
          <div class="row"> @if(module_enabled('Subdomain'))
            <div class="form-group">
              <label><?php echo app('translator')->get('modules.frontCms.loginBackroundImage'); ?></label>
              <div class="card">
                <div class="card-body">
                  <input type="file" id="input-file-now" name="login_background" class="dropify" data-default-file="{{ $global->login_background_image_url }}"
                                            />
                </div>
              </div>
            </div>
            @endif </div>
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <label for="address">@lang('modules.accountSettings.companyAddress')</label>
                <textarea class="form-control" id="address" rows="2" name="address">{{ $global->address }}</textarea>
              </div>
            </div>
          </div>
          <div class="row pt-2">
            <div class="col-12">
              <div class="form-group">
                <label for="address">@lang('modules.accountSettings.jobOpeningText')</label>
                <textarea class="form-control" id="job_opening_text" rows="3" name="job_opening_text">{{ $global->job_opening_text }}</textarea>
              </div>
            </div>
          </div>
          <div class="row py-4">
            <div class="col-12 text-center">
              <button type="button" id="save-form" class="btn btn-light-success btn-sm"> @lang('app.save') </button>
              <button type="reset" class="btn btn-light-danger btn-sm"> @lang('app.reset') </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@push('footer-script')
<script src="{{ asset('assets/node_modules/select2/dist/js/select2.full.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/bootstrap-select/bootstrap-select.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/dropify/dist/js/dropify.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="//cdn.jsdelivr.net/npm/speakingurl@14.0.1/speakingurl.min.js" type="text/javascript"></script>
<script src="{{ asset('assets/plugins/slugify/src/slugify.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script>
// =========================================================================
// JeroEmailBoxEngine: 100% Client-First (Zero Server Strain) Email Engine
// RFC 5321 (254 char), Deterministic Sanitizer, 3-State Typo Detection
// =========================================================================
window.JeroEmailBoxEngine = window.JeroEmailBoxEngine || (function () {
    var CONSUMER_DOMAIN_TYPOS = {
        'gmai.com': 'gmail.com', 'gamil.com': 'gmail.com', 'gmial.com': 'gmail.com',
        'gmaill.com': 'gmail.com', 'gmaik.com': 'gmail.com', 'gmeil.com': 'gmail.com',
        'gmaul.com': 'gmail.com', 'gmal.com': 'gmail.com', 'gmaili.com': 'gmail.com',
        'yaho.com': 'yahoo.com', 'yahooo.com': 'yahoo.com', 'ymail.com': 'yahoo.com',
        'outlok.com': 'outlook.com', 'outloo.com': 'outlook.com',
        'hotmial.com': 'hotmail.com', 'hotmai.com': 'hotmail.com',
        'iclou.com': 'icloud.com', 'iclod.com': 'icloud.com',
        'redifmail.com': 'rediffmail.com', 'rediffmial.com': 'rediffmail.com'
    };

    var CONSUMER_TLD_TYPOS = {
        'con': 'com', 'cmo': 'com', 'ocm': 'com', 'cpm': 'com',
        'comm': 'com', 'coom': 'com', 'ent': 'net', 'og': 'org', 'ogr': 'org'
    };

    return {
        cleanDeterministic: function (raw) {
            if (!raw) return '';
            var str = String(raw).trim();
            str = str.replace(/[\u00A0\u1680\u180E\u2000-\u200B\u202F\u205F\u3000\uFEFF]/g, '');
            str = str.replace(/^['"`<]+|['"`>]+$/g, '');
            str = str.replace(/^mailto:\s*/i, '');
            var match = str.match(/<([^>]+)>/);
            if (match && match[1]) {
                str = match[1];
            }
            str = str.replace(/[.,;:]+$/, '');
            return str.toLowerCase().replace(/\s+/g, '');
        },

        getSuggestion: function (email) {
            if (!email || email.indexOf('@') === -1) return null;
            var parts = email.split('@');
            if (parts.length !== 2) return null;

            var local = parts[0];
            var domain = parts[1];

            if (domain.indexOf(',') !== -1) {
                var commaFixed = domain.replace(/,([a-zA-Z]{2,})$/, '.$1');
                return {
                    suggested: local + '@' + commaFixed,
                    message: 'Did you mean <strong>' + local + '@' + commaFixed + '</strong>?'
                };
            }

            if (CONSUMER_DOMAIN_TYPOS[domain]) {
                var fixedEmail = local + '@' + CONSUMER_DOMAIN_TYPOS[domain];
                return {
                    suggested: fixedEmail,
                    message: 'Did you mean <strong>' + fixedEmail + '</strong>?'
                };
            }

            var dParts = domain.split('.');
            if (dParts.length >= 2) {
                var root = dParts[0];
                var tld = dParts[dParts.length - 1];
                if (CONSUMER_TLD_TYPOS[tld] && (root === 'gmail' || root === 'yahoo' || root === 'outlook' || root === 'hotmail')) {
                    dParts[dParts.length - 1] = CONSUMER_TLD_TYPOS[tld];
                    var fixed = local + '@' + dParts.join('.');
                    return {
                        suggested: fixed,
                        message: 'Did you mean <strong>' + fixed + '</strong>?'
                    };
                }
            }

            return null;
        },

        validateSyntax: function (email) {
            if (!email || email.trim() === '') {
                return { isValid: false, error: 'Email address is required.' };
            }

            if (email.length > 254) {
                return { isValid: false, error: 'Email exceeds maximum allowed length of 254 characters.' };
            }

            if (/\s/.test(email)) {
                return { isValid: false, error: 'Email address cannot contain spaces.' };
            }

            if (email.indexOf(',') !== -1) {
                if (email.indexOf('@') !== -1 && email.split('@')[1].indexOf(',') !== -1) {
                    return { isValid: false, error: 'Domain name contains a comma instead of a period (e.g. gmail,com).' };
                }
                return { isValid: false, error: 'Email address cannot contain commas.' };
            }

            var parts = email.split('@');
            if (parts.length < 2) {
                return { isValid: false, error: 'Email is missing the "@" symbol.' };
            }
            if (parts.length > 2) {
                return { isValid: false, error: 'Email contains more than one "@" symbol.' };
            }

            var local = parts[0];
            var domain = parts[1];

            if (local.length === 0) {
                return { isValid: false, error: 'Email username cannot be empty.' };
            }
            if (local.length > 64) {
                return { isValid: false, error: 'Email username cannot exceed 64 characters.' };
            }

            if (local.indexOf('..') !== -1 || domain.indexOf('..') !== -1) {
                return { isValid: false, error: 'Email cannot contain consecutive dots ("..").' };
            }

            if (local.startsWith('.') || local.endsWith('.')) {
                return { isValid: false, error: 'Email username cannot start or end with a dot.' };
            }
            if (domain.startsWith('.') || domain.endsWith('.')) {
                return { isValid: false, error: 'Domain name cannot start or end with a dot.' };
            }

            if (domain.indexOf('_') !== -1) {
                return { isValid: false, error: 'Domain name cannot contain underscores (use hyphens instead).' };
            }

            if (!/^[a-z0-9]([a-z0-9-]*[a-z0-9])?(\.[a-z0-9]([a-z0-9-]*[a-z0-9])?)+$/.test(domain)) {
                return { isValid: false, error: 'Domain name format is invalid (letters, numbers, and hyphens only).' };
            }

            var tld = domain.split('.').pop();
            if (!tld || tld.length < 2 || /^\d+$/.test(tld)) {
                return { isValid: false, error: 'Domain must end with a valid extension (e.g. .com, .org).' };
            }

            return { isValid: true, error: null };
        }
    };
})();

//        jQuery(function($) {
            $.slugify("Ätschi Bätschi"); // "aetschi-baetschi"
            $('#slug').slugify('#company_slug'); // Type as you slug

//            $("#slug-target").slugify("#slug-source", {
//                separator: '_' // If you want to change separator from hyphen (-) to underscore (_).
//            });
//        });

        $('#company_slug').on('keyup change', function() {
            if ($('#slug').val() !== '' && $('#slug').val() !== undefined) {
                $('#slugValue').html($('#slug').val());
            } else {

            }
        });
        // For select 2
        $(".select2").select2();

        $('.dropify').dropify({
            messages: {
                default: '@lang("app.dragDrop")',
                replace: '@lang("app.dragDropReplace")',
                remove: '@lang("app.remove")',
                error: '@lang('app.largeFile')'
            }
        });

        $(document).on('paste', '#company_email', function () {
            var self = this;
            setTimeout(function () {
                var clean = window.JeroEmailBoxEngine ? window.JeroEmailBoxEngine.cleanDeterministic($(self).val()) : $(self).val().trim();
                if (clean !== $(self).val()) {
                    $(self).val(clean).trigger('input');
                }
            }, 10);
        });

        $(document).on('blur', '#company_email', function () {
            var raw = $(this).val();
            var clean = window.JeroEmailBoxEngine ? window.JeroEmailBoxEngine.cleanDeterministic(raw) : raw.trim();
            if (clean !== raw) {
                $(this).val(clean);
            }
            var $hint = $('#company_email_typo_hint');
            if (!clean) {
                $hint.hide().empty();
                return;
            }
            if (window.JeroEmailBoxEngine) {
                var syntax = window.JeroEmailBoxEngine.validateSyntax(clean);
                if (!syntax.isValid) {
                    $hint.html('<i class="fa fa-exclamation-circle text-danger"></i> ' + syntax.error).show();
                    return;
                }
                var suggestion = window.JeroEmailBoxEngine.getSuggestion(clean);
                if (suggestion) {
                    $hint.html('<i class="fa fa-lightbulb-o text-warning"></i> ' + suggestion.message + ' <a href="javascript:void(0);" class="apply-email-fix font-weight-bold text-primary ml-1" data-target="#company_email" data-fixed="' + suggestion.suggested + '">Apply</a>').show();
                } else {
                    $hint.hide().empty();
                }
            }
        });

        $(document).on('click', '.apply-email-fix', function (e) {
            e.preventDefault();
            var target = $(this).data('target');
            var fixed = $(this).data('fixed');
            if (target && fixed) {
                $(target).val(fixed).trigger('input').trigger('change');
                $('#company_email_typo_hint').hide().empty();
            }
        });

        $('#save-form').click(function () {
            var raw = $('#company_email').val();
            var email = window.JeroEmailBoxEngine ? window.JeroEmailBoxEngine.cleanDeterministic(raw) : raw.trim();
            $('#company_email').val(email);

            if (email && window.JeroEmailBoxEngine) {
                var syntax = window.JeroEmailBoxEngine.validateSyntax(email);
                if (!syntax.isValid) {
                    $('#company_email_typo_hint').html('<i class="fa fa-exclamation-circle text-danger"></i> ' + syntax.error).show();
                    swal("Invalid Email", syntax.error, "error");
                    return false;
                }
            }

            $.easyAjax({
                url: '{{route('service_company.settings.update', $global->id)}}',
                container: '#editSettings',
                type: "POST",
                redirect: true,
                file: true
            })
        });
    </script>
@endpush 