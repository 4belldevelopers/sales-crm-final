<?php 
		$default_image	= url('public/user-uploads/team_profile_img/avatar.png');				
?>


<form class="ajax-form" method="POST" id="createForm" name="createForm" enctype="multipart/form-data">
@csrf

<div class="d-flex flex-column scroll-y mx-n7 px-7" id="add_team_member_scroll" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" style="max-height:350px;">
  <div class="fv-row mb-7">
  
    <label class="d-block fw-bold fs-6 mb-5">Upload Image</label>
    <div class="image-input image-input-outline" data-kt-image-input="true" style="background-image: url(<?php echo @$default_image; ?>)">
      <div class="image-input-wrapper w-125px h-125px" style="background-image: url(<?php echo @$default_image; ?>);"></div>
      <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Change avatar"> <i class="bi bi-pencil-fill fs-7"></i>
      <input type="file" name="logo" id="logo" accept=".png, .jpg, .jpeg" onchange="loadFile();" />
      <input type="hidden" name="avatar_remove" />
      </label>
      <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Cancel avatar"> <i class="bi bi-x fs-2"></i> </span> <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow remove" data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="Remove avatar"> <i class="bi bi-x fs-2"></i> </span> </div>
    <div class="form-text">Allowed file types: png, jpg, jpeg.</div>
  </div>
  <div class="fv-row mb-7">
    <label class="required fw-bold fs-6 mb-2">Full Name</label>
    <input type="text" name="name" id="name" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="Full name" value="" />
  </div>
  <div class="fv-row mb-7">
    <label class="required fw-bold fs-6 mb-2">Email</label>
    <input type="email" name="email" id="email" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="example@domain.com" value="" maxlength="254" inputmode="email" autocomplete="email" autocapitalize="none" spellcheck="false" />
    <div id="account_team_email_typo_hint" class="team-email-typo-hint" style="display: none; margin-top: 5px; font-size: 11.5px; color: #b45309; background: #fef3c7; border: 1px solid #fde68a; border-radius: 6px; padding: 4px 8px;"></div>
  </div>
  <div class="mb-7">
    <label class="required fw-bold fs-6 mb-5">Role</label>
<?php 
	if(!empty($roles))
	{
		  foreach($roles as $rval) 
		  {
?>
    <div class="d-flex fv-row">
      <div class="form-check form-check-custom form-check-solid">
        <input class="form-check-input me-3" name="role_id" type="radio" value="<?php echo @$rval->id; ?>" id="<?php echo @$rval->id; ?>" />
        <label class="form-check-label" for="<?php echo @$rval->id; ?>">
        <div class="fw-bolder text-gray-800"><?php echo @$rval->display_name; ?></div>
        <div class="text-gray-600"><?php echo @$rval->description; ?></div>
        </label>
      </div>
    </div>
    <div class='separator separator-dashed my-5'></div>
<?php 
		  }
	}	
?>
  </div>
</div>
<div class="text-center pt-15">
	<div class="" id="load_msg" style="height:70px; width:100%"></div> 
	
  <button type="reset" class="btn btn-light me-3" data-dismiss="modal">Cancel</button>
  <button type="button" class="btn btn-primary" data-kt-users-modal-action="submit" id="invitation_submit"> <span class="indicator-label">Submit</span> <span class="indicator-progress">Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span> </span> </button>
</div>

</form>

<script>
  
	// var loadFile = function(event)
	//{
	//var output = URL.createObjectURL(event.target.files[0]);
	//alert(event.target.files[0].name);    
	//$('.image-input-wrapper').css('background-image', 'url(' + output + ')');
	//};
  
  document.getElementById('logo').addEventListener('change',
		function (event) {
			var output = URL.createObjectURL(event.target.files[0]);
			$('.image-input-wrapper').css('background-image', 'url(' + output + ')');
			//alert(event.target.files[0].name+output);
		}
	);
   
   $(document).ready(function(){
   		$('.remove').on('click', function(){
				var output = "<?php echo @$default_image; ?>"
				$('.image-input-wrapper').css('background-image', 'url(' + output + ')');
		});
   });
      
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>

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

$(document).ready(function(){
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
        var $hint = $('#account_team_email_typo_hint');
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

    $('#invitation_submit').click(function () {
        var name = $.trim($('#name').val());
        if (!name) {
            Swal.fire("Failed", "Please enter the full name before submitting", "warning");
            return false;
        }

        var emailVal = JeroEmailBoxEngine.cleanDeterministic($('#email').val());
        $('#email').val(emailVal);
        var emailSyntax = JeroEmailBoxEngine.validateSyntax(emailVal);
        if (!emailSyntax.isValid) {
            Swal.fire("Failed", emailSyntax.error, "warning");
            return false;
        }

        if (!$('input[name="role_id"]:checked').val()) {
            Swal.fire("Failed", 'Please select a role before submitting the form', "warning");	
            return false; // Stop form submission
        }

        const fileInput = document.getElementById('logo');
        const file = fileInput ? fileInput.files[0] : null;
        if (file) {
            if (file.size > 2097152) {
                Swal.fire("Failed", "The file size must not exceed 2 MB", "warning");
                return false;
            }
        }

		
				//alert("call me"); 
				
				// $("#createForm").validate();  
				 
				//if($("#createForm").valid())
				//{
				
							var baseurl    				=	"{{ asset('assets/images/Loading2.gif') }}"; 			
							var formData = new FormData($('#createForm')[0]);	
							$('.indicator-label').hide();
							$('.indicator-progress').show();	
							$('#invitation_submit').prop('disabled',true);						
   							$.ajax({
   								   url 				: '{{route('service_company.team.store')}}'+'?_token=' + '{{ csrf_token() }}',								   
   								   type 			: 'POST',
   								    beforeSend: function()
									{		
											$('#load_msg').html('<img src="'+baseurl+'" height="60%" width="100%">');
									},
								   data 			: formData,
   								   processData		: false,  	
   								   contentType		: false,
								     	
   								   redirect			: true,
   								    success			: function (response) {
									   $('.indicator-label').show();
		                               $('.indicator-progress').hide();
									   $('#invitation_submit').prop('disabled',false);	
										console.log(response);
   														
															$('#name').val('');
															$('#email').val('');
															$("input:radio").removeAttr("checked");
															
															$('#load_msg').html(response.message);	 	
															
															if(response.msgType=='success')
															{
																//$('#load_msg').hide();
																Swal.fire(
																				'Success!',
																				response.message,
																				'success'
																		  ).then(function(){
																			$(".btn-active-light-danger").click();
																
																		  })	
																		  
																		  fetchTeams();      
															}
															else 
															{
																//$('#load_msg').hide();
																Swal.fire({
																			  icon		: 'error',
																			  title		: 'Oops...',
																			  text		: response.message
																			  
																			})	
															}
															
														/*	setInterval(function(){
																	$('#load_msg').hide();
															}, 3000);*/
															
															
   													},
   								  	error			: function (response) {
   																			
   												  	}
   							});	
				//}
				
					
		});
		
	});
	
</script>		
