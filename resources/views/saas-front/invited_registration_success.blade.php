<!DOCTYPE html>
<html lang="en" >
<head>
  <meta charset="UTF-8">
  <title>CodePen - Success Message</title>
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/prefixfree/1.0.7/prefixfree.min.js"></script>

<style>
.success-message
{
	text-align: center;
    /* max-width: 500px; */
    position: relative;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}
.success-message__icon
{
  max-width: 75px;
  background: #3DC480;
  border-radius: 50%;
}
.success-message__title
{
  color: #3DC480;
}
.active .success-message__title
{
}
.success-message__content
{
	font-size: 16px;
    color: #838383;
    font-family: 'Poppins';
}
.active .success-message__content
{
  transform: translateY(0);
}
.icon-checkmark circle
{
  fill: #3DC480;
  transform-origin: 50% 50%;
  transform: scale(0);
  transition: transform 200ms cubic-bezier(0.22, 0.96, 0.38, 0.98);
}
.icon-checkmark path
{
  transition: stroke-dashoffset 350ms ease;
  transition-delay: 100ms;
}
.active .icon-checkmark circle
{
  transform: scale(1);
}
</style>
</head>
<body>
<div class="success-message">
    <svg viewBox="0 0 76 76" class="success-message__icon icon-checkmark">
        <circle cx="38" cy="38" r="36"/>
        <path fill="none" stroke="#FFFFFF" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" d="M17.7,40.9l10.9,10.9l28.7-28.7"/>
    </svg>
    <h1 class="success-message__title">Success!</h1>
    <div class="success-message__content">
        <p>
				Dear, <span class="fw-bold text-dark"><?php echo $user->name ?></span> thankyou for register with us.<br>
        </p>
    </div>

	  <p>
			  &nbsp; 
      </p>	
	    
      <p><a href="{{ route('login') }}" class="btn btn-primary">     Login      </a></p>
</div>
</body>
</html>

<script>

 $('body').on('click', '.Resend', function() {
           
			var id 		=	$(this).attr('rel');
			var url 	=	"{{ route('resend',':id') }}";
			url 		=	url.replace(':id', id);

			var baseurl =	"{{ asset('assets/images/AjaxLoader.gif') }}"; 
            $.ajax({
                		type: 'GET',
                		url: url,
						beforeSend: function() {
   							$('#loader').html('<img src="'+baseurl+'" height="50px" width="50px">');
   						},
                		success: function (response) {
															$('#loader').hide();
															if(response.status=='success')
															{	
																Swal.fire("Success!", "Account activation link has been sent successfully.", "success");
															}
															else
															{
																Swal.fire("Error!", "Your need to fill all the form fields!", "error");
															}
                		}
            });
			
		  
			
			
})


</script>