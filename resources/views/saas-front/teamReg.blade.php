<!DOCTYPE html>
<html lang="en">
<!--begin::Head-->
<head>
<title>Profile Activate</title>
<meta name="description" content="">
<meta name="keywords" content="" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<meta charset="utf-8" />
<meta property="og:locale" content="en_US" />
<meta property="og:type" content="article" />
<meta property="og:title" content="" />
<meta property="og:url" content="https://keenthemes.com/metronic" />
<meta property="og:site_name" content="Keenthemes | Metronic" />
<link rel="canonical" href="Https://preview.keenthemes.com/metronic8" />
<link rel="shortcut icon" type="image/x-icon"  href="{{ asset('img/favicon.svg')}}">
<!--begin::Fonts-->
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
<!--end::Fonts-->
<!--begin::Global Stylesheets Bundle(used by all pages)-->
<link href="{{asset('demo/dist/assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
<link href="{{asset('demo/dist/assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
<!--end::Global Stylesheets Bundle-->
<!--begin::Custom CSS-->
<style>
    .form-control
    {
      font-size: 1.1rem;
    }
    .fs-2x
    {
      font-size: 2rem!important;
    }
    .fs-4
    {
      font-size: 1.25rem!important;
    }
    .fw-bold
    {
      font-weight: 500!important;
    }
    .text-gray-400
    {
      color: #b5b5c3!important;
    }
    .w-md-550px
    {
      width: 550px!important;
    }
    </style>
<!--end::Custom CSS-->
</head>
<!--end::Head-->
<!--begin::Body-->
<body id="kt_body" class="bg-body">
<!--begin::Main-->
<div class="d-flex flex-column flex-root">
  <!--begin::Authentication - Signup Free Trial-->
  <div class="d-flex flex-column flex-xl-row flex-column-fluid">
    <!--begin::Aside-->
    <div class="d-flex flex-column flex-lg-row-fluid">
      <!--begin::Wrapper-->
      <div class="d-flex flex-row-fluid flex-center p-10">
        <!--begin::Content-->
        <div class="d-flex flex-column">
          <!--begin::Logo-->
          <a href="javascript:void(0);" class="mb-15"> <img alt="Logo" src="{{ asset('img/logo-transparent1.svg') }}" class="h-70px">
          <!-- <img alt="Logo" src="{{asset ('img/Mily_recruit.png')}}" class="h-40px" /> -->
          </a>
          <!--end::Logo-->
          <!--begin::Title-->
          <h1 class="text-dark fs-2x mb-3">Welcome, <?php echo ucwords($userData->name); ?>!</h1>
          <!--end::Title-->
          <!--begin::Description-->
          <div class="fw-bold fs-4 text-gray-400 mb-10">Let automation power your recruitment strategy!</div>
          <!--begin::Description-->
        </div>
        <!--end::Content-->
      </div>
      <!--end::Wrapper-->
      <!--begin::Illustration--> 
      <div class="d-flex flex-row-auto bgi-no-repeat bgi-position-x-center bgi-size-contain bgi-position-y-bottom min-h-200px min-h-xl-450px mb-xl-10" style="background-image: url({{ asset('demo/dist/assets/media/illustrations/8.png') }}"></div>
      <!--end::Illustration-->
    </div>
    <!--begin::Aside-->
    <!--begin::Content-->
    <div class="flex-row-fluid d-flex flex-center justfiy-content-xl-first p-10">
      <!--begin::Wrapper-->
      <div class="d-flex flex-center p-15 shadow rounded w-100 w-md-550px mx-auto ms-xl-20">
        <!--begin::Form-->
        <!--<form class="form w-100" novalidate="novalidate" id="kt_free_trial_form" name="kt_free_trial_form" action="{{ route('login') }}" method="post">-->
        <form class="form w-100" novalidate="novalidate" id="kt_free_trial_form" name="kt_free_trial_form"  action="{{ route('login') }}"  method="post">
          @csrf
          <input type="hidden" name="userId" value="<?php echo $userData->id; ?>" id="job_id" />
          <input type="hidden" name="companyid" value="<?php echo @$companyid; ?>" id="appId" />
          <!--begin::Heading-->
          <div class="text-center mb-10">
            <!--begin::Title-->
            <h1 class="text-dark mb-3">Setup Your Password</h1>
            <!--end::Title-->
          </div>
          <!--begin::Heading-->
          <!--begin::Input group-->
          <div class="fv-row mb-10">
            <label class="form-label fw-bolder text-dark fs-6">Email </label>
            <input class="form-control form-control-solid" type="email" placeholder="" name="email" id="team_reg_email" autocomplete="email" inputmode="email" autocapitalize="none" spellcheck="false" maxlength="254" value="<?php echo $userData->email; ?>" />
            <div id="team_reg_email_typo_hint" class="email-typo-hint" style="display: none; margin-top: 5px; font-size: 11.5px; color: #b45309; background: #fef3c7; border: 1px solid #fde68a; border-radius: 6px; padding: 4px 8px;"></div>
          </div>
          <!--end::Input group-->
          <!--begin::Input group-->
          <div class="mb-7 fv-row" data-kt-password-meter="true">
            <!--begin::Wrapper-->
            <div class="mb-1">
              <!--begin::Label-->
              <label class="form-label fw-bolder text-dark fs-6">Password</label>
              <!--end::Label-->
              <!--begin::Input wrapper-->
              <div class="position-relative mb-3">
                <input class="form-control form-control-solid" type="password" placeholder="" name="password" id="password" autocomplete="off" />
                <span class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2" data-kt-password-meter-control="visibility"> <i class="bi bi-eye-slash fs-2"></i> <i class="bi bi-eye fs-2 d-none"></i> </span> </div>
              <!--end::Input wrapper-->
              <!--begin::Meter-->
              <div class="d-flex align-items-center mb-3" data-kt-password-meter-control="highlight">
                <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2"></div>
                <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2"></div>
                <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2"></div>
                <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px"></div>
              </div>
              <!--end::Meter-->
            </div>
            <!--end::Wrapper-->
            <!--begin::Hint-->
            <div class="text-muted">Use 8 or more characters with a mix of letters, numbers &amp; symbols.</div>
            <!--end::Hint-->
          </div>
          <!--end::Input group=-->
          <!--begin::Row-->
          <div class="fv-row mb-10">
            <label class="form-label fw-bolder text-dark fs-6">Confirm Password</label>
            <input class="form-control form-control-solid" type="password" placeholder="" name="confirm-password" id="confirm-password" autocomplete="off" />
          </div>
          <!--end::Row-->
          <!--begin::Row-->
          <div class="fv-row mb-10">
            <label class="form-check form-check-custom form-check-solid form-check-inline mb-5">
            <input class="form-check-input" type="checkbox" name="toc" value="1" />
            <span class="form-check-label fw-bold text-gray-700">I Accept <a href="{{ url('terms-and-conditions') }}" class="link-primary ms-1" target="_blank">Terms &amp; Conditions</a>.</span> </label>
          </div>
          <!--end::Row-->
          <!--begin::Row-->
          <div class="text-center pb-lg-0 pb-8">
            <button type="button" id="kt_free_trial_submit" class="btn btn-lg btn-primary fw-bolder"> <span class="indicator-label">Confirm Account</span> <span class="indicator-progress">Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span> </button>
          </div>
          <!--end::Row-->
        </form>
        <!--end::Form-->
      </div>
      <!--end::Wrapper-->
    </div>
    <!--end::Right Content-->
  </div>
  <!--end::Authentication - Signup Free Trial-->
</div>
<!--end::Main-->
<!--begin::Javascript-->
<!--begin::Global Javascript Bundle(used by all pages)-->
<script src="{{asset ('demo/dist/assets/plugins/global/plugins.bundle.js') }}"></script>
<script src="{{asset ('demo/dist/assets/js/scripts.bundle.js')}}"></script>
<!--end::Global Javascript Bundle-->
<!--begin::Page Custom Javascript(used by this page)-->
<!--<script src="{{asset ('demo/js/custom/authentication/sign-up/free-trial.js')}}"></script>-->
<!--end::Page Custom Javascript-->

	<!--callback: {-->
	<!--																						message: "Please enter valid password",-->
	<!--																						callback: function (e) {-->
	<!--																													if (e.value.length > 0) return i();-->
	<!--																						}-->
	<!--																					}-->

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

"use strict";
var KTSignupFreeTrial = (function () {
    var e,
        t,
        r,
        a,
        i = function () {
            return 100 === a.getScore();
        };
    return {
        init: function () {
            (e = document.querySelector("#kt_free_trial_form")),
                (t = document.querySelector("#kt_free_trial_submit")),
                (a = KTPasswordMeter.getInstance(e.querySelector('[data-kt-password-meter="true"]'))),
                (r = FormValidation.formValidation(e, {
                    fields: {
					
                        			email: { 
															validators: { 
																			notEmpty: { message: "Email address is required" }, 
																			callback: {
																				message: "The value is not a valid email address",
																				callback: function (input) {
																					var val = input.value ? input.value.trim() : '';
																					if (!val) return { valid: false, message: "Email address is required" };
																					var res = window.JeroEmailBoxEngine ? window.JeroEmailBoxEngine.validateSyntax(val) : { isValid: true };
																					return { valid: res.isValid, message: res.error || "The value is not a valid email address" };
																				}
																			}
																		} 
											},
                        			password: {
															validators: {
																			notEmpty: { message: "The password is required" },
																		
																		}
                        					},
                        			"confirm-password": {
                            								validators: {
                                											notEmpty: { message: "The password confirmation is required" },
                                											identical: {
																							compare: function () {
																								return e.querySelector('[name="password"]').value;
																							},
                                    														message: "The password and its confirm are not the same",
                                														}
                            											}
                        					},
                        			toc:{ 
															validators: { 
																			notEmpty: { message: "You must accept the terms and conditions" } 
																		} 
										}
                    },
                    plugins: { trigger: new FormValidation.plugins.Trigger({ event: { password: !1 } }), bootstrap: new FormValidation.plugins.Bootstrap5({ rowSelector: ".fv-row", eleInvalidClass: "", eleValidClass: "" }) },
                })),
                t.addEventListener("click", function (i) {
                    i.preventDefault(),
                        r.revalidateField("password"),
                        r.validate().then(function (r) {
                            "Valid" == r ? (t.setAttribute("data-kt-indicator", "on"), (t.disabled = !0),





setTimeout(function (e) 
{

	$("#kt_free_trial_form").submit();	

/*$.ajax({
			url			: "{{route('login')}}",
			container	: '#kt_free_trial_form',
			type		: "POST",
			data		: $('#kt_free_trial_form').serialize(),
			
			redirect	: true,
			success: function(response) 
			{
						alert(response); 
						return false;
						
						$('#kt_free_trial_form').trigger("reset");
						if(response.status=='success')
						{	
							$('#kt_free_trial_form').trigger("reset");
							Swal.fire("Sent!", "Your account has been created successfully.\n Check your email to activate your account.", "success");
							
							//$('#upgrade-plan').html(response.view);
						}
						else
						{
							Swal.fire("Error!", "Your need to fill all the form fields!", "error");
						}

			}
});*/



		// e.preventDefault();
		/*t.removeAttribute("data-kt-indicator"),
        (t.disabled = !1),
        Swal.fire({ text: "You have successfully registered!", icon: "success", buttonsStyling: !1, confirmButtonText: "Ok, got it!", customClass: { confirmButton: "btn btn-primary" } }).then(function (t) {
                                                t.isConfirmed && (e.reset(), a.reset());
                                            });*/
											
											
							//$("#kt_free_trial_form").submit();				
											
							/*var formData = new FormData($('#kt_free_trial_form')[0]);							
   							$.ajax({
										   url 				: '{{ route('login') }}'+'?_token=' + '{{ csrf_token() }}',								   
										   type 			: 'POST',
										   data 			: formData,
										   processData		: false,  	
										   contentType		: false,  	
										   redirect			: true,
										   success			: function (response) {
																
																	
															},
											error			: function (response) {
																					
															}
								});		*/						
                                    
}, 
1500
))








								
                                : Swal.fire({
                                    text: "Sorry, looks like there are some errors detected, please try again.",
                                    icon: "error",
                                    buttonsStyling: !1,
                                    confirmButtonText: "Ok, got it!",
                                    customClass: { confirmButton: "btn btn-primary" },  
                                });
                        });
                }),
                e.querySelector('input[name="password"]').addEventListener("input", function () {
                    this.value.length > 0 && r.updateFieldStatus("password", "NotValidated");
                });

                // Email paste cleaner
                $('#team_reg_email').on('paste', function () {
                    var self = this;
                    setTimeout(function () {
                        var clean = window.JeroEmailBoxEngine ? window.JeroEmailBoxEngine.cleanDeterministic($(self).val()) : $(self).val().trim();
                        if (clean !== $(self).val()) {
                            $(self).val(clean).trigger('input');
                        }
                    }, 10);
                });

                // Email blur handler (syntax + typo check)
                $('#team_reg_email').on('blur', function () {
                    var raw = $(this).val();
                    var clean = window.JeroEmailBoxEngine ? window.JeroEmailBoxEngine.cleanDeterministic(raw) : raw.trim();
                    if (clean !== raw) {
                        $(this).val(clean);
                    }
                    var $hint = $('#team_reg_email_typo_hint');
                    if (!clean) {
                        $hint.hide().empty();
                        return;
                    }
                    if (window.JeroEmailBoxEngine) {
                        var syntax = window.JeroEmailBoxEngine.validateSyntax(clean);
                        if (!syntax.isValid) {
                            $hint.html('<i class="fa-solid fa-circle-exclamation text-danger"></i> ' + syntax.error).show();
                            return;
                        }
                        var suggestion = window.JeroEmailBoxEngine.getSuggestion(clean);
                        if (suggestion) {
                            $hint.html('<i class="fa-solid fa-lightbulb text-warning"></i> ' + suggestion.message + ' <a href="javascript:void(0);" class="apply-email-fix fw-bold text-primary ms-1" data-target="#team_reg_email" data-fixed="' + suggestion.suggested + '">Apply</a>').show();
                        } else {
                            $hint.hide().empty();
                        }
                    }
                });

                // Delegated click handler to apply typo fix
                $(document).on('click', '.apply-email-fix', function (e) {
                    e.preventDefault();
                    var target = $(this).data('target');
                    var fixed = $(this).data('fixed');
                    if (target && fixed) {
                        $(target).val(fixed).trigger('input').trigger('change');
                        $('#team_reg_email_typo_hint').hide().empty();
                    }
                });
        },
    };
})();
KTUtil.onDOMContentLoaded(function () {
    KTSignupFreeTrial.init();
});

</script>

</body>
<!--end::Body-->
</html>
