@extends('layouts.demo_admin_app')

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
                    <h4 class="card-title pt-2 pb-2">
                        <i class="fa fa-building"></i>&nbsp;Business Setting
                    </h4>
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
                                                <label for="exampleInputPassword1">
                                                    Upload&nbsp;@lang('modules.accountSettings.companyLogo')
                                                </label>
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
                                    <span class="text-success">@lang('modules.accountSettings.companyUrlLinkWillBe') </span> : <span class="font-weight-bold " style="border: none" id="slugValue">{{ $global->career_page_link }}</span>
                                </div>
                            </div>
                            <div class="col-md-4">    
                                <div class="form-group">
                                    <label for="company_email">@lang('modules.accountSettings.companyEmail')</label>
                                    <input type="email" class="form-control" id="company_email" name="company_email" value="{{ $global->company_email }}" placeholder="Enter Company Email" maxlength="254" inputmode="email" autocomplete="email" autocapitalize="none" spellcheck="false">
                                    <div id="company_email_typo_hint" class="email-typo-hint-container mt-1" style="display: none; font-size: 11px;"></div>
                                </div>
                            </div>    
                        </div>

                        <div class="row pt-2">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="company_phone">@lang('modules.accountSettings.companyPhone')</label>
                                    <input type="number" class="form-control" id="company_phone" name="company_phone" value="{{ $global->company_phone }}" placeholder="Enter Company Phone">
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

                        <div class="row">
                            @if(module_enabled('Subdomain'))
                                <div class="form-group">
                                    <label><?php echo app('translator')->get('modules.frontCms.loginBackroundImage'); ?></label>
                                    <div class="card">
                                        <div class="card-body">
                                            <input type="file" id="input-file-now" name="login_background" class="dropify" data-default-file="{{ $global->login_background_image_url }}"
                                            />
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

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
                                <button type="button" id="save-form" class="btn btn-light-success btn-sm">
                                    @lang('app.save')
                                </button>
                                <button type="reset" class="btn btn-light-danger btn-sm">
                                    @lang('app.reset')
                                </button>
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



// ══════════════════════════════════════════════════════════════════════════════
// 100% CLIENT-FIRST GOD-LEVEL EMAIL VALIDATION ENGINE (ZERO SERVER STRAIN)
// ══════════════════════════════════════════════════════════════════════════════
window.JeroEmailBoxEngine = {
    domainTypos: {
        'gamil.com': 'gmail.com', 'gmal.com': 'gmail.com', 'gmaill.com': 'gmail.com', 'gmai.com': 'gmail.com',
        'gmial.com': 'gmail.com', 'gmaik.com': 'gmail.com', 'gamil.co': 'gmail.com', 'gmail.con': 'gmail.com',
        'gmail.co': 'gmail.com', 'gmaill.co': 'gmail.com', 'gmaill.con': 'gmail.com', 'gmail.cpm': 'gmail.com',
        'gmail.cm': 'gmail.com', 'gmail.om': 'gmail.com', 'g.mail.com': 'gmail.com',
        'hotmial.com': 'hotmail.com', 'hotmai.com': 'hotmail.com', 'hotmaill.com': 'hotmail.com',
        'hotmial.co': 'hotmail.com', 'hotmail.con': 'hotmail.com', 'hotmsil.com': 'hotmail.com',
        'yaho.com': 'yahoo.com', 'yahooo.com': 'yahoo.com', 'yaho.co': 'yahoo.com', 'yahoo.con': 'yahoo.com',
        'yaho.in': 'yahoo.in', 'yahooo.in': 'yahoo.in', 'yaho.co.in': 'yahoo.co.in',
        'outlok.com': 'outlook.com', 'outloo.com': 'outlook.com', 'outlook.con': 'outlook.com',
        'outlok.co': 'outlook.com', 'outlock.com': 'outlook.com',
        'iclud.com': 'icloud.com', 'icoud.com': 'icloud.com', 'icloud.con': 'icloud.com'
    },
    cleanDeterministic: function(val) {
        if (!val || typeof val !== 'string') return '';
        var cleaned = val.replace(/[\u00A0\u1680\u180E\u2000-\u200B\u202F\u205F\u3000\uFEFF]/g, ' ')
                         .trim()
                         .replace(/^mailto:\s*/i, '')
                         .replace(/^<+|>+$/g, '')
                         .replace(/^"+|"+$/g, '')
                         .replace(/^'+|'+$/g, '')
                         .replace(/[.,;:]+$/, '')
                         .trim();
        return cleaned;
    },
    validateSyntax: function(email) {
        if (!email || typeof email !== 'string') return false;
        var trimmed = email.trim();
        if (trimmed.length === 0 || trimmed.length > 254) return false;
        if ((trimmed.match(/@/g) || []).length !== 1) return false;
        var parts = trimmed.split('@');
        var local = parts[0];
        var domain = parts[1];
        if (!local || !domain) return false;
        if (local.length > 64) return false;
        if (local.startsWith('.') || local.endsWith('.') || local.indexOf('..') !== -1) return false;
        if (domain.startsWith('.') || domain.endsWith('.') || domain.startsWith('-') || domain.endsWith('-')) return false;
        if (domain.indexOf('..') !== -1) return false;
        var rfcPattern = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)+$/;
        return rfcPattern.test(trimmed);
    },
    suggestCorrection: function(email) {
        if (!email || typeof email !== 'string') return null;
        var raw = email.trim();
        if (raw.indexOf(',') !== -1) {
            raw = raw.replace(/,/g, '.');
        }
        var atMatches = raw.match(/@/g) || [];
        if (atMatches.length !== 1) return null;
        var parts = raw.split('@');
        var local = parts[0].trim();
        var domain = (parts[1] || '').trim().toLowerCase();
        if (!local || !domain) return null;
        if (this.domainTypos[domain]) {
            return local + '@' + this.domainTypos[domain];
        }
        var tldFixes = {
            'con': 'com', 'cpm': 'com', 'comm': 'com', 'cmo': 'com', 'ocm': 'com',
            'coom': 'com', 'ent': 'net', 'og': 'org', 'ogr': 'org'
        };
        var dParts = domain.split('.');
        if (dParts.length >= 2) {
            var last = dParts[dParts.length - 1];
            if (tldFixes[last]) {
                dParts[dParts.length - 1] = tldFixes[last];
                var suggestedDomain = dParts.join('.');
                if (suggestedDomain !== domain) {
                    return local + '@' + suggestedDomain;
                }
            }
        }
        return null;
    },
    renderTypoPill: function(containerSelector, suggestedEmail, targetInputSelector) {
        var $hint = $(containerSelector);
        if (!$hint.length) return;
        if (suggestedEmail) {
            $hint.html(
                '<div class="d-inline-flex align-items-center bg-light-primary text-primary px-2 py-1 rounded fs-8 fw-semibold mt-1">' +
                    '<i class="fa fa-info-circle me-1"></i> ' +
                    '<span>Did you mean <strong>' + $('<div>').text(suggestedEmail).html() + '</strong>?</span>' +
                    '<button type="button" class="btn btn-xs btn-link text-primary fw-bold p-0 ms-2 text-decoration-underline apply-email-fix" data-target="' + targetInputSelector + '" data-suggestion="' + $('<div>').text(suggestedEmail).html() + '">Apply</button>' +
                '</div>'
            ).slideDown(150);
        } else {
            $hint.slideUp(100, function() { $(this).empty(); });
        }
    },
    setupEmailBox: function(inputSelector, hintSelector) {
        var self = this;
        var $input = $(inputSelector);
        if (!$input.length) return;

        $input.on('paste', function(e) {
            var pasted = (e.originalEvent || e).clipboardData ? (e.originalEvent || e).clipboardData.getData('text') : '';
            if (pasted) {
                e.preventDefault();
                var cleaned = self.cleanDeterministic(pasted);
                $input.val(cleaned).trigger('input').trigger('change');
            }
        });

        $input.on('input', function() {
            var val = $(this).val();
            var cleaned = self.cleanDeterministic(val);
            var suggestion = self.suggestCorrection(cleaned);
            if (suggestion && suggestion.toLowerCase() !== cleaned.toLowerCase()) {
                self.renderTypoPill(hintSelector, suggestion, inputSelector);
            } else {
                $(hintSelector).slideUp(100, function() { $(this).empty(); });
            }
        });

        $input.on('blur', function() {
            var val = $(this).val();
            if (val) {
                $(this).val(self.cleanDeterministic(val));
            }
        });
    }
};

$(document).ready(function() {
    window.JeroEmailBoxEngine.setupEmailBox('#company_email', '#company_email_typo_hint');

    $(document).on('click', '#company_email_typo_hint .apply-email-fix', function(e) {
        e.preventDefault();
        var sugg = $(this).attr('data-suggestion');
        if (sugg) {
            $('#company_email').val(sugg).trigger('input').trigger('change');
            $('#company_email_typo_hint').slideUp(100, function() { $(this).empty(); });
        }
    });
});

        $('#save-form').click(function () {
            var emailVal = window.JeroEmailBoxEngine ? window.JeroEmailBoxEngine.cleanDeterministic($('#company_email').val()) : ($('#company_email').val() || '').trim();
            $('#company_email').val(emailVal);
            if (emailVal && window.JeroEmailBoxEngine && !window.JeroEmailBoxEngine.validateSyntax(emailVal)) {
                var sugg = window.JeroEmailBoxEngine.suggestCorrection(emailVal);
                if (sugg) {
                    window.JeroEmailBoxEngine.renderTypoPill('#company_email_typo_hint', sugg, '#company_email');
                }
                swal.fire({
                    text: 'Please enter a valid company email address.',
                    icon: 'warning'
                });
                $('#company_email').focus();
                return false;
            }

            $.easyAjax({
                url: '{{route('admin.settings.update', $global->id)}}',
                container: '#editSettings',
                type: "POST",
                redirect: true,
                file: true
            })
        });
    </script>


@endpush
