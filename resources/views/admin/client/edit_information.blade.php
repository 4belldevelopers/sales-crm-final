@extends('layouts.app_ng')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">Edit Member Information</h4>
        <form class="ajax-form" method="POST" id="createForm" enctype="multipart/form-data">
          @csrf
         
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="name">Name</label>
                <input type="text" class="form-control" name="contact_person_name" id="contact_person_name" value="{{ $cc_info->contact_person_name }}" placeholder="Name">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="email">Email </label>
                <input type="email" class="form-control" name="contact_person_email" id="contact_person_email"  value="{{ $cc_info->contact_person_email  }}" placeholder="Email" maxlength="254" inputmode="email" autocomplete="email" autocapitalize="none" spellcheck="false">
                <div id="contact_person_email_typo_hint" class="email-typo-hint-container mt-1" style="display: none; font-size: 11px;"></div>
              </div>
            </div>
            <div class="col-md-6 mt-5">
              <div class="form-group">
                <label for="website">Contact Number</label>
                <input type="text" class="form-control" name="contact_person_number" id="contact_person_number" value="{{ $cc_info->contact_person_number }}" placeholder="Contact Number">
              </div>
            </div>
            <div class="col-md-6 mt-5">
              <div class="form-group">
                <label for="commission">Designation</label>
                <input type="text" class="form-control" name="contact_person_designation" id="contact_person_designation" value="{{ $cc_info->contact_person_designation  }}" placeholder="Designation">
              </div>
            </div>
          </div>
          <div class="row mt-5">
              <div class="col-12 text-center">
                <button type="button" id="save-form" class="btn btn-success btn-sm"><i class="fa fa-spinner"></i> Update</button>
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
    window.JeroEmailBoxEngine.setupEmailBox('#contact_person_email', '#contact_person_email_typo_hint');

    $(document).on('click', '#contact_person_email_typo_hint .apply-email-fix', function(e) {
        e.preventDefault();
        var sugg = $(this).attr('data-suggestion');
        if (sugg) {
            $('#contact_person_email').val(sugg).trigger('input').trigger('change');
            $('#contact_person_email_typo_hint').slideUp(100, function() { $(this).empty(); });
        }
    });
});

      	$('#save-form').click(function () {
            var emailVal = window.JeroEmailBoxEngine ? window.JeroEmailBoxEngine.cleanDeterministic($('#contact_person_email').val()) : ($('#contact_person_email').val() || '').trim();
            $('#contact_person_email').val(emailVal);
            if (emailVal && window.JeroEmailBoxEngine && !window.JeroEmailBoxEngine.validateSyntax(emailVal)) {
                var sugg = window.JeroEmailBoxEngine.suggestCorrection(emailVal);
                if (sugg) {
                    window.JeroEmailBoxEngine.renderTypoPill('#contact_person_email_typo_hint', sugg, '#contact_person_email');
                }
                swal.fire({
                    text: 'Please enter a valid email address.',
                    icon: 'warning'
                });
                $('#contact_person_email').focus();
                return false;
            }

            $.easyAjax({
                url			: '{{route('admin.clients.update_info', $cc_info->id)}}',
                container	: '#createForm',
                type		: "POST",
                redirect	: true,
				file		: true,
                data		: $('#createForm').serialize()
            })
        });
    </script>
@endpush