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
    <div id="email_typo_hint" class="team-email-typo-hint" style="display: none; margin-top: 5px; font-size: 11.5px; color: #b45309; background: #fef3c7; border: 1px solid #fde68a; border-radius: 6px; padding: 4px 8px;"></div>
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

$(document).ready(function(){

					$('form[id="createForm"]').validate({
															  rules: {
																		name	: 'required',
																		email	: {
																						required: true,
																						email: true
																				  }
																	 },
														  messages: {
																		name: 'name is required',
																		email: 'Enter a valid email'
																	}
									});


});


   // 100% Client-Side RFC 5321 Validation & Typo Engine
   window.JeroEmailBoxEngine = window.JeroEmailBoxEngine || {
       domainTypos: {
           'gamil.com': 'gmail.com', 'gmial.com': 'gmail.com', 'gmaill.com': 'gmail.com',
           'gmai.com': 'gmail.com', 'gmaii.com': 'gmail.com', 'gemail.com': 'gmail.com',
           'hotmial.com': 'hotmail.com', 'hotmai.com': 'hotmail.com', 'hotmil.com': 'hotmail.com',
           'yaho.com': 'yahoo.com', 'yahooo.com': 'yahoo.com', 'yhaoo.com': 'yahoo.com',
           'outlok.com': 'outlook.com', 'outloo.com': 'outlook.com',
           'iclou.com': 'icloud.com', 'icoud.com': 'icloud.com'
       },
       tldTypos: {
           'con': 'com', 'cmo': 'com', 'ocm': 'com', 'comm': 'com', 'coom': 'com',
           'ney': 'net', 'nte': 'net', 'nett': 'net',
           'og': 'org', 'orgg': 'org', 'ogr': 'org',
           'in': 'in', 'inn': 'in', 'iin': 'in',
           'co': 'co', 'cc': 'cc'
       },
       cleanDeterministic: function(val) {
           if (!val) return '';
           var s = String(val).replace(/[\u200B-\u200D\uFEFF\u00A0\u1680\u180E\u2000-\u200A\u202F\u205F\u3000]/g, ' ').trim();
           s = s.replace(/^mailto:\s*/i, '');
           s = s.replace(/^["'<(\[]+|[>"')\]]+$/g, '');
           s = s.replace(/[,;]+$/, '');
           return s.trim();
       },
       validateSyntax: function(email) {
           if (!email) return false;
           var cleaned = this.cleanDeterministic(email);
           if (cleaned.length < 5 || cleaned.length > 254) return false;
           var atIdx = cleaned.indexOf('@');
           if (atIdx <= 0 || atIdx !== cleaned.lastIndexOf('@')) return false;
           var local = cleaned.slice(0, atIdx);
           var domain = cleaned.slice(atIdx + 1);
           if (local.length > 64 || domain.length > 253) return false;
           var localRegex = /^[a-zA-Z0-9!#$%&'*+\-/=?^_`{|}~]+(?:\.[a-zA-Z0-9!#$%&'*+\-/=?^_`{|}~]+)*$/;
           if (!localRegex.test(local)) return false;
           if (domain.indexOf('.') === -1) return false;
           var parts = domain.split('.');
           if (parts.length < 2) return false;
           var domainPartRegex = /^[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?$/;
           for (var i = 0; i < parts.length; i++) {
               if (!parts[i] || !domainPartRegex.test(parts[i])) return false;
           }
           var tld = parts[parts.length - 1];
           return tld.length >= 2 && !/^\d+$/.test(tld);
       },
       suggestCorrection: function(email) {
           if (!email) return null;
           var cleaned = this.cleanDeterministic(email);
           var atIdx = cleaned.indexOf('@');
           if (atIdx <= 0 || atIdx !== cleaned.lastIndexOf('@')) return null;
           var local = cleaned.slice(0, atIdx);
           var domain = cleaned.slice(atIdx + 1).toLowerCase();
           if (!domain) return null;
           if (this.domainTypos[domain]) {
               return local + '@' + this.domainTypos[domain];
           }
           var lastDot = domain.lastIndexOf('.');
           if (lastDot > 0) {
               var mainDomain = domain.slice(0, lastDot);
               var ext = domain.slice(lastDot + 1);
               if (this.tldTypos[ext]) {
                   return local + '@' + mainDomain + '.' + this.tldTypos[ext];
               }
           }
           return null;
       },
       renderTypoPill: function(containerSelector, suggestion, inputSelector) {
           var $container = $(containerSelector);
           if (!$container.length) return;
           $container.html(
               '<div class="d-inline-flex align-items-center py-1 px-2 rounded bg-light-warning text-warning fs-8 border border-warning border-dashed">' +
                   '<i class="fa fa-info-circle text-warning me-1 fs-8"></i>' +
                   '<span>Did you mean <strong>' + $('<div>').text(suggestion).html() + '</strong>?</span>' +
                   '<button type="button" class="btn btn-xs btn-primary ms-2 px-2 py-0 apply-email-fix" data-suggestion="' + $('<div>').text(suggestion).html() + '" data-target="' + inputSelector + '">Apply</button>' +
               '</div>'
           ).slideDown(150);
       },
       setupEmailBox: function(inputSelector, hintSelector) {
           var self = this;
           var $input = $(inputSelector);
           if (!$input.length) return;

           $input.on('paste', function(e) {
               var pasted = (e.originalEvent || e).clipboardData ? (e.originalEvent || e).clipboardData.getData('text') : null;
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

   $(document).ready(function(){
        window.JeroEmailBoxEngine.setupEmailBox('#email', '#email_typo_hint');

        $(document).off('click', '#email_typo_hint .apply-email-fix').on('click', '#email_typo_hint .apply-email-fix', function(e) {
            e.preventDefault();
            var suggestion = $(this).attr('data-suggestion');
            if (suggestion) {
                $('#email').val(suggestion).trigger('input').trigger('change');
                $('#email_typo_hint').slideUp(100, function() { $(this).empty(); });
            }
        });

   		$('#invitation_submit').click(function () {
            var emailVal = window.JeroEmailBoxEngine.cleanDeterministic($('#email').val());
            $('#email').val(emailVal);
            if (!emailVal || !window.JeroEmailBoxEngine.validateSyntax(emailVal)) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ text: 'Please enter a valid email address.', icon: 'error' });
                } else {
                    alert('Please enter a valid email address.');
                }
                $('#email').focus();
                return false;
            }
		
				//alert("call me"); 
				
				// $("#createForm").validate();  
				 
				//if($("#createForm").valid())
				//{
				
							var baseurl    				=	"{{ asset('assets/images/Loading2.gif') }}"; 			
							var formData = new FormData($('#createForm')[0]);							
   							$.ajax({
   								   url 				: '{{route('superadmin.team.store')}}'+'?_token=' + '{{ csrf_token() }}',								   
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
																			$(".btn-active-icon-primary").click();
																
																		  })	
																		  
																		  table._fnDraw();      
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
