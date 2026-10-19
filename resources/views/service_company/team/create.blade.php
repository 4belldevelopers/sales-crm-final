@extends('layouts.product_company_layout')
<style>
.content .card 
{
    box-shadow: none!important;
}
.content-header
{
    display: none;
}

#btnFiterSubmitSearch
{
    padding:0.5rem 0.20rem!important;
}    
.bootstrap-select>.dropdown-toggle
{
    color: #3f4254;
    background-color: #fff;
    background-clip: padding-box;
    border: 1px solid #d2d2d2;
    box-shadow: 0 1px 1px rgb(0 0 0 / 8%);
    border-radius: 3px;
}
.btn-info:not(:disabled):not(.disabled):active
{
    color: #333;
    background-color: #ececec;
    border-color: #ececec;
}

.dropify-wrapper
{
    border: 2px dashed #E5E5E5!important;
    border-radius: 3px;
    -webkit-transition: border-color .15s linear;
    transition: border-color .15s linear;
}

</style>
@push('head-script')
<link rel="stylesheet" href="{{ asset('assets/node_modules/dropify/dist/css/dropify.min.css') }}<?php echo TM_VERSION; ?>">
@endpush

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title pt-2"> <i class="fa fa-plus-square"></i>&nbsp;Add Profile </h4>
        <form id="editSettings" class="ajax-form">
          @csrf
          <div class="row">
            <div class="col-md-4 offset-md-4">
              <div class="form-group">
                <input type="file" id="input-file-now" name="image" accept=".png,.jpg,.jpeg" class="dropify" data-default-file="{{ asset('avatar.png')  }}"/>
                <!-- <div class="col-12 text-center">    
                                        <label for="exampleInputPassword1">Upload Profile</label>
                                    </div> -->
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="name">@lang('app.name')</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Enter Your Name..">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <x-jero-phone-input
                    id="mobile"
                    name="mobile"
                    countryCodeName="calling_code"
                    countryCodeId="calling_code"
                    value=""
                    countryCode=""
                    label="Mobile"
                    :required="false"
                    placeholder="Enter mobile number" />
              </div>
            </div>
          </div>
          <div class="row mt-2">
            <div class="col-md-6">
              <div class="form-group">
                <label for="email">@lang('app.email')</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Enter Your Email.." maxlength="254" inputmode="email" autocomplete="email" autocapitalize="none" spellcheck="false">
                <div id="team_email_typo_hint" class="team-email-typo-hint" style="display: none; margin-top: 5px; font-size: 11.5px; color: #b45309; background: #fef3c7; border: 1px solid #fde68a; border-radius: 6px; padding: 4px 8px;"></div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="company_phone">@lang('app.password')</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Enter Your Password...">
              </div>
            </div>
          </div>
          <div class="col-12 mt-2 px-0">
            <div class="form-group">
              <label for="company_phone">@lang('modules.permission.roleName')</label>
              <select class="form-control" name="role_id" id="role_id">
                
                                    @foreach($roles as $role)
                                        
                <option value="{{ $role->id }}">{{ $role->display_name }}</option>
                
                                    @endforeach
                                
              </select>
            </div>
          </div>
          <div class="row mb-3 mt-3">
            <div class="col-12 text-center">
              <button type="button" id="save-form" class="btn btn-light-success btn-sm mr-2"> <i class="fa fa-save"></i>&nbsp;@lang('app.save') </button>
              <button type="reset" class="btn btn-light-warning btn-sm"> <i class="fa fa-spinner"></i>&nbsp;@lang('app.reset') </button>
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
        // =========================================================================
        // JeroEmailBoxEngine: 100% Client-First (Zero Server Strain) Email Engine
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

        $('.dropify').dropify({
            messages: {
                default: "Drag or Drop Your Profile",
                replace: '@lang("app.dragDropReplace")',
                remove: '@lang("app.remove")',
                error: '@lang('app.largeFile')'
            }
        });

        // Paste & Blur deterministic cleanup and typo suggestions for Email
        $(document).on('paste', '#email', function () {
            var self = this;
            setTimeout(function () {
                var clean = JeroEmailBoxEngine.cleanDeterministic($(self).val());
                if (clean !== $(self).val()) {
                    $(self).val(clean).trigger('input');
                }
            }, 10);
        });

        $(document).on('blur change', '#email', function () {
            var raw = $(this).val();
            var clean = JeroEmailBoxEngine.cleanDeterministic(raw);
            if (clean !== raw) {
                $(this).val(clean);
            }
            var $hint = $('#team_email_typo_hint');
            if (!clean) {
                $hint.hide().empty();
                return;
            }
            var syntax = JeroEmailBoxEngine.validateSyntax(clean);
            if (!syntax.isValid) {
                $hint.html('<i class="fa fa-exclamation-circle text-danger"></i> ' + syntax.error).show();
                return;
            }
            var suggestion = JeroEmailBoxEngine.getSuggestion(clean);
            if (suggestion) {
                $hint.html('<i class="fa fa-lightbulb-o text-warning"></i> ' + suggestion.message + ' <a href="javascript:void(0);" class="apply-email-fix fw-bold text-primary ms-1" data-target="#email" data-fixed="' + suggestion.suggested + '">Apply</a>').show();
            } else {
                $hint.hide().empty();
            }
        });

        $(document).on('click', '.apply-email-fix', function (e) {
            e.preventDefault();
            var targetSelector = $(this).data('target');
            var fixed = $(this).data('fixed');
            if (targetSelector && fixed) {
                $(targetSelector).val(fixed).trigger('input').trigger('change');
                $(this).closest('.team-email-typo-hint').hide().empty();
            }
        });

        $('#save-form').click(function () {
            var emailVal = JeroEmailBoxEngine.cleanDeterministic($('#email').val());
            $('#email').val(emailVal);
            var emailSyntax = JeroEmailBoxEngine.validateSyntax(emailVal);
            if (!emailSyntax.isValid) {
                if (typeof Swal !== 'undefined' && typeof Swal.fire === 'function') {
                    Swal.fire({ text: emailSyntax.error, icon: "warning" });
                } else {
                    alert(emailSyntax.error);
                }
                $('#email').focus();
                return false;
            }

            $.ajax({
                url         : '{{route('service_company.team.store')}}',
                container   : '#editSettings',
                type        : "POST",
                redirect    : true,
                data        : $('#editSettings').serialize(),
                file        : true
            });
        });
    </script>
@endpush