@extends('layouts.app')
@push('head-script')
<link rel="stylesheet" href="{{ asset('assets/node_modules/dropify/dist/css/dropify.min.css') }}<?php echo TM_VERSION; ?>">
@endpush
@section('content')
<style>
.error{ color:#FF0000;}
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
</style>
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">      
        <form class="ajax-form" method="POST" id="createForm" name="createForm" enctype="multipart/form-data">
          @csrf
          <div class="row">
            
			     <div class="col-md-12">
              <div class="form-group">
                <h4 class="card-title pt-2 pb-2">
                  <i class="fa fa-plus-square"></i>&nbsp;Add Vendor
                </h4>               
              </div>
            </div>
      
           <div class="col-md-12">
              <div class="form-group">
                <h4 for="email" class="border-bottom p-2 bg-light-secondary">
                  <i class="fa fa-sign-in"></i>&nbsp;Login Detail
                </h4>        
              </div>
            </div>

			     <div class="col-12">
              <div class="row">
    			       <div class="col-md-6">
                  <div class="form-group">
                    <label for="email">Email </label>
                    <input type="email" class="form-control" name="email" id="email" placeholder="Enter Your Email" maxlength="254" inputmode="email" autocomplete="email" autocapitalize="none" spellcheck="false">
                    <div id="email_typo_hint" class="email-typo-hint" style="display: none; margin-top: 5px; font-size: 11.5px; color: #b45309; background: #fef3c7; border: 1px solid #fde68a; border-radius: 6px; padding: 4px 8px;"></div>
                  </div>
                </div>

    			       <div class="col-md-6">
                  <div class="form-group">
                    <label for="email">Password </label>
                    <input type="password" class="form-control" name="password" id="password" placeholder="Enter Your Password">
                  </div>
                </div>
              </div>                 
           </div>
			
			     <div class="col-md-12">
              <div class="form-group">
                <h4 for="email" class="border-bottom mt-3 p-2 bg-light-secondary">
                  <i class="fa fa-building"></i>&nbsp;Business Detail
                </h4>        
              </div>
            </div>

            <div class="col-12 pt-1">
              <div class="row">      
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="name">Consultancy Name</label>
                    <input type="text" class="form-control" name="name" id="name" placeholder="Enter Your Consultancy Name">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                      <div class="form-row">
                          <div class="col-sm-4">
                            <label>Country Code</label>
                              <select name="calling_code" id="calling_code" class="form-control selectpicker" data-live-search="true" data-width="100%" >
                                  @foreach ($calling_codes as $code => $value)
                                      <option @if($value['dial_code'] == $user->calling_code ) selected @endif  value="{{ $value['dial_code'] }}">{{ $value['dial_code'] . ' - ' . $value['name'] }}
                                      </option>
                                  @endforeach
                              </select>
                          </div>
                          <div class="col-sm-8">
                            <label>@lang('app.mobile')&nbsp;Number</label>
                              <input type="number" class="form-control" name="mobile" placeholder="Enter Your Number">
                          </div>
                      </div>
                  </div>
                </div>
                <!-- <div class="col-md-6">
                  <div class="form-group">
                      <label>@lang('app.mobile')</label>
                      <input type="text" class="form-control" name="mobile" placeholder="Enter Mobile Number">
                  </div>
                </div> -->    
              </div>   
            </div>  
			
            <div class="col-12 pt-2">
              <div class="row">        
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="contact_person_name">Contact Person name</label>
                    <input type="text" class="form-control" name="contact_person_name" id="contact_person_name" placeholder="Enter Consultancy Contact Person Name">
                  </div>
                </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="status">Status</label>
                      <select class="form-control" name="status" id="status">
                    <option value="active">Active</option>
                    <option value="inactive">In-Active</option>
                       </select>
                    </div>
                  </div>
                </div>  
            </div>  					
						
            <div class="col-12 pt-2">
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="website">Website</label>
                    <input type="text" class="form-control" name="website" id="website" placeholder="Enter URL">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="file">Document Upload</label>
                    <input type="file" class="form-control upload-document"  name="image[]" accept=".png,.jpg,.jpeg" multiple>
                  </div>
                </div>
              </div>  
            </div>  

            <div class="col-12 pt-2">
              <div class="row">  
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="payout">Payout(Days)</label>
                    <input type="number" class="form-control" name="payout" id="payout" placeholder="Enter Payout Days" maxlength="5" onkeypress="return isNumberKey(event,this)">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="commission">Commission(%)</label>
                    <input type="number" class="form-control filterme" name="commission" id="commission" placeholder="Enter Commission(%)" maxlength="5" onkeypress="return isNumberKey(event,this)">
                  </div>
                </div>
              </div>  
            </div>  

            <div class="col-md-12 pt-2">
              <div class="form-group">
                <label for="detail">Detail</label>
                <textarea class="form-control" id="detail" name="detail" rows="4" placeholder="Enter Description"></textarea>
              </div>
            </div>

            <div class="col-md-12 pt-2">
              <div class="form-group">
                <label for="feedback">Feedback</label>
                <textarea class="form-control" id="feedback" name="feedback" rows="4" placeholder="Write a Message..."></textarea>
              </div>
            </div>	 
							
							
            <hr>
          </div>     
            <div class="row py-4">
                <div class="col-md-12 text-center">
                    <button type="button" id="save-form" class="btn btn-light-success btn-sm py-1">
                      <i class="fa fa-save fa-sm"></i>&nbsp;@lang('app.save')
                    </button>
                    <button type="reset" class="btn btn-light-warning btn-sm py-1">
                      <i class="fa fa-spinner fa-sm"></i>&nbsp;@lang('app.reset')
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
<script>
        $('.dropify').dropify({
            messages: {
                default: '@lang("app.dragDrop")',
                replace: '@lang("app.dragDropReplace")',
                remove: '@lang("app.remove")',
                error: '@lang('app.largeFile')'
            }
        });
 </script>
<script>
		


        // For select 2
        $(".select2").select2();

        var room = 1;

        $('#add-more').click(function () {

            room++;
            var objTo 			= document.getElementById('education_fields')
            var divtest 		= document.createElement("div");
            divtest.setAttribute("class", "form-group removeclass" + room);
            var rdiv 			= 'removeclass' + room;
            divtest.innerHTML 	= '<div class="row"><div class="col-sm-9 nopadding"><div class="form-group"><div class="input-group"> <input type="text" name="name[]" class="form-control" placeholder="Client name"><div class="input-group-append"> <button class="btn btn-danger" type="button" onclick="remove_education_fields(' + room + ');"> <i class="fa fa-minus"></i> </button></div></div></div></div><div class="clear"></div></row>';

            objTo.appendChild(divtest)
        })

        function remove_education_fields(rid) {
            $('.removeclass' + rid).remove();
        }

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

        $(document).on('paste', '#email', function () {
            var self = this;
            setTimeout(function () {
                var clean = window.JeroEmailBoxEngine ? window.JeroEmailBoxEngine.cleanDeterministic($(self).val()) : $(self).val().trim();
                if (clean !== $(self).val()) {
                    $(self).val(clean).trigger('input');
                }
            }, 10);
        });

        $(document).on('blur', '#email', function () {
            var raw = $(this).val();
            var clean = window.JeroEmailBoxEngine ? window.JeroEmailBoxEngine.cleanDeterministic(raw) : raw.trim();
            if (clean !== raw) {
                $(this).val(clean);
            }
            var $hint = $('#email_typo_hint');
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
                    $hint.html('<i class="fa fa-lightbulb-o text-warning"></i> ' + suggestion.message + ' <a href="javascript:void(0);" class="apply-email-fix font-weight-bold text-primary ml-1" data-target="#email" data-fixed="' + suggestion.suggested + '">Apply</a>').show();
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
                $('#email_typo_hint').hide().empty();
            }
        });

        $('#save-form').click(function () {
            var raw = $('#email').val();
            var email = window.JeroEmailBoxEngine ? window.JeroEmailBoxEngine.cleanDeterministic(raw) : raw.trim();
            $('#email').val(email);

            if (window.JeroEmailBoxEngine) {
                var syntax = window.JeroEmailBoxEngine.validateSyntax(email);
                if (!syntax.isValid) {
                    $('#email_typo_hint').html('<i class="fa fa-exclamation-circle text-danger"></i> ' + syntax.error).show();
                    swal("Invalid Email", syntax.error, "error");
                    return false;
                }
            }

            $.easyAjax({
                url			: '{{route('admin.consultancy.store')}}',
                container	: '#createForm',
                type		: "POST",
                redirect	: true,
				file: true
               
            })
        });
    </script>

 <script type="text/javascript">

        function isNumberKey(evt, obj) {

            var charCode = (evt.which) ? evt.which : event.keyCode
            var value = obj.value;
            var dotcontains = value.indexOf(".") != -1;
            if (dotcontains)
                if (charCode == 46) return false;
            if (charCode == 46) return true;
            if (charCode > 31 && (charCode < 48 || charCode > 57))
                return false;
            return true;
        }

</script>
@endpush