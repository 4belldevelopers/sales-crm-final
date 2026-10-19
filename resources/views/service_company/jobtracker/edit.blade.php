@extends('layouts.service_company_layout')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title mb-4">@lang('app.edit')</h4>
        <form class="ajax-form" method="POST" id="createForm">
          @csrf
          @method('PUT')
          <div class="row">
            <div class="col-md-4 pl-4 pr-4 pb-4  pb-4 b-b">
              <h5>@lang('modules.front.personalInformation')</h5>
            </div>
            <div class="col-md-8 pb-4 b-b">
              <div class="form-group">
                <input class="form-control" type="text" value="{{ $application->full_name }}" name="full_name" placeholder="@lang('app.name')">
              </div>
              <div class="form-group">
                <input class="form-control" type="email" name="email" id="email" value="{{ $application->email }}" placeholder="@lang('app.email')" maxlength="254" inputmode="email" autocomplete="email" autocapitalize="none" spellcheck="false">
                <div id="email_typo_hint" class="email-typo-hint-container mt-1" style="display: none; font-size: 11px;"></div>
              </div>
              <div class="form-group">
                <input class="form-control" type="tel" name="phone" value="{{ $application->phone }}"
                                           placeholder="@lang('app.phone')">
              </div>
              <div class="form-group">
                <h6>@lang('modules.front.photo')</h6>
                <input class="select-file" accept=".png,.jpg,.jpeg" type="file" name="photo">
                <br>
                <span class="help-block">@lang('modules.front.photoFileType')</span> </div>
              @if(!is_null($application->photo))
              <p> <a target="_blank" href="{{ asset_url('candidate-photos/'.$application->photo) }}" class="btn btn-sm btn-primary">@lang('app.view')</a> </p>
              @endif </div>
            <div class="col-md-4 pl-4 pr-4 pb-4 pt-4 b-b">
              <h5>@lang('modules.front.resume')</h5>
            </div>
            <div class="col-md-8 pb-4 pt-4 b-b">
              <div class="form-group">
                <input class="select-file" type="file" name="resume">
                <br>
              </div>
              <p> <a target="_blank" href="{{ route('service_company.cvtracker.download', $application->id) }}" class="btn btn-sm btn-primary">@lang('app.download')</a> </p>
            </div>
            <div class="col-md-4  pl-4 pr-4 pb-4 pt-4 pt-4 b-b">
              <h5>@lang('modules.front.coverLetter')</h5>
            </div>
            <div class="col-md-8 pb-4 pt-4 b-b">
              <div class="form-group">
                <textarea class="form-control" name="cover_letter" rows="4">{{ $application->cover_letter }}</textarea>
              </div>
            </div>
            @if(count($jobQuestion) > 0)
            <div class="col-md-4 pl-4 pr-4 pt-4 b-b" id="questionBoxTitle">
              <h5>@lang('modules.front.additionalDetails')</h5>
            </div>
            <div class="col-md-8 pt-4 b-b" id="questionBox"> @forelse($jobQuestion as $question)
              <div class="form-group">
                <input class="form-control" type="text" id="answer[{{ $question->question->id}}]" value="{{ $question->getAnswerByQuestion($application->id) }}" name="answer[{{ $question->question->id}}]" placeholder="{{ $question->question->question }} ?">
              </div>
              @empty
              @endforelse </div>
            @endif
            <div class="col-md-4 pl-4 pr-4 pt-4">
              <h5>@lang('app.status')</h5>
            </div>
            <div class="col-md-8 pt-4">
              <div class="form-group">
                <select name="status_id" id="status_id" class="form-control">
                  
                  
                                        @foreach($statuses as $status)
                                            
                  
                  <option
                                                    @if($application->status_id == $status->id) selected @endif
                  value="{{ $status->id }}">{{ ucwords($status->status) }}</option>
                  
                  
                                        @endforeach
                                    
                
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
<script>
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
    window.JeroEmailBoxEngine.setupEmailBox('#email', '#email_typo_hint');

    $(document).on('click', '#email_typo_hint .apply-email-fix', function(e) {
        e.preventDefault();
        var sugg = $(this).attr('data-suggestion');
        if (sugg) {
            $('#email').val(sugg).trigger('input').trigger('change');
            $('#email_typo_hint').slideUp(100, function() { $(this).empty(); });
        }
    });
});

        $('#save-form').click(function () {
            var emailVal = window.JeroEmailBoxEngine ? window.JeroEmailBoxEngine.cleanDeterministic($('#email').val()) : ($('#email').val() || '').trim();
            $('#email').val(emailVal);
            if (emailVal && window.JeroEmailBoxEngine && !window.JeroEmailBoxEngine.validateSyntax(emailVal)) {
                var sugg = window.JeroEmailBoxEngine.suggestCorrection(emailVal);
                if (sugg) {
                    window.JeroEmailBoxEngine.renderTypoPill('#email_typo_hint', sugg, '#email');
                }
                swal.fire({
                    text: 'Please enter a valid email address.',
                    icon: 'warning'
                });
                $('#email').focus();
                return false;
            }

            $.easyAjax({
                url: '{{route('service_company.cvtracker.update', $application->id)}}',
                container: '#createForm',
                type: "POST",
                redirect: true,
                file:true,
                error: function (response) {
                    handleFails(response);
                }
            })
        });


        function handleFails(response) {

            if (typeof response.responseJSON.errors != "undefined") {
                var keys = Object.keys(response.responseJSON.errors);
                console.log(keys);
                $('#createForm').find(".has-error").find(".help-block").remove();
                $('#createForm').find(".has-error").removeClass("has-error");

                for (var i = 0; i < keys.length; i++) {
                    // Escape dot that comes with error in array fields
                    var key = keys[i].replace(".", '\\.');
                    var formarray = keys[i];

                    // If the response has form array
                    if(formarray.indexOf('.') >0){
                        var array = formarray.split('.');
                        response.responseJSON.errors[keys[i]] = response.responseJSON.errors[keys[i]];
                        key = array[0]+'['+array[1]+']';
                    }

                    var ele = $('#createForm').find("[name='" + key + "']");

                    var grp = ele.closest(".form-group");
                    $(grp).find(".help-block").remove();

                    //check if wysihtml5 editor exist
                    var wys = $(grp).find(".wysihtml5-toolbar").length;

                    if(wys > 0){
                        var helpBlockContainer = $(grp);
                    }
                    else{
                        var helpBlockContainer = $(grp).find("div:first");
                    }
                    if($(ele).is(':radio')){
                        helpBlockContainer = $(grp).find("div:eq(2)");
                    }

                    if (helpBlockContainer.length == 0) {
                        helpBlockContainer = $(grp);
                    }

                    helpBlockContainer.append('<div class="help-block">' + response.responseJSON.errors[keys[i]] + '</div>');
                    $(grp).addClass("has-error");
                }

                if (keys.length > 0) {
                    var element = $("[name='" + keys[0] + "']");
                    if (element.length > 0) {
                        $("html, body").animate({scrollTop: element.offset().top - 150}, 200);
                    }
                }
            }
        }

    </script>
@endpush 