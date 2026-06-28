<?php  
  use App\Helper\GeneralMethod;
?>
<html>

<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<title>Meecruit - Acceptance Invite</title>
	<meta http-equiv="x-dns-prefetch-control" value-equiv="off">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<style>
		/*@import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,200;0,300;0,400;1,200&display=swap');*/
	</style>
</head>

<body style="width:auto;margin: 0;padding: 0;box-sizing: border-box;background: #eee;">
	<div class="moz-forward-container">
		<table class="hedrtop" width="600" height="380" cellspacing="0" cellpadding="0" border="0" align="center">
			<tbody style="font-family: Arial,Helvetica,sans-serif;line-height: 1.5;font-weight: normal;font-size: 15px;color: #2F3044;min-height: 100%;margin: 0;padding: 0;width: 100%;">
				<tr>
					<td>
						<table width="100%" cellspacing="0" cellpadding="10" border="0" bgcolor="#fff" align="center">
							<tbody>
								<!-- This email will be send on Internal and Team Member vendor -->
								<!-- Subject:: Acceptance Invite-->
								<tr>
									<td style="line-height: normal;padding: 1em;" bgcolor="#FFFFFF">
										<!-- <p class="name"></p> -->
										<div class="message-section">
											<div class="col-12">
												<div>
													{{-- <p style="padding-top:.2em;margin:0;padding:2px 0;">Hi "Staffing partner name"</p> --}}
													<p style="text-align: justify;">You have been invited to join the Talentmily team from <a href="javascript:void(0);" rel="noopener" style="text-decoration:none;color: #009EF7">support@talentmily.com</a>.
														started, accept the invite below:</p>

													<div style="padding-bottom: 20px; text-align:center;">
														<a href="{{ $data->url }}" rel="noopener"
															style="text-decoration:none;display:inline-block;text-align:center;padding:0.75575rem 1.3rem;font-size:0.925rem;line-height:1.5;border-radius:0.35rem;color:#ffffff;background-color:#009EF7;border:0px;margin-right:0.75rem!important;font-weight:600!important;outline:none!important;vertical-align:middle">Accept
															Invite
														</a>
														<p>You can capy to the below link :<a href="{{ $data->url }}" rel="noopener" style="text-decoration:none;color: #009EF7">{{ $data->url }}</a></p>
													</div>
													
													<p style="text-align: left;">Joining the team will give you access to the team's
													dashboard, including information about our products. </p>
													
													<p style="text-align: left;">You can find answers to most questions and get in touch
													with us at <a href="javascript:void(0);" rel="noopener" style="text-decoration:none;color: #009EF7">support@talentmily.com</a>.</p>
												</div>
												<?php  
                                    $company_name = GeneralMethod::CompantName();
                                     ?>
                                    <p style="padding-bottom:.5em;margin:0;padding:2px 0;">Best Regards,<br>
                                       "<?= $company_name->company_name ?>"<br>
                                       Recruitment Department
                                    </p>
											</div>
										</div>
									</td>
								</tr>
							
							</tbody>
						</table>
					</td>
				</tr>
			</tbody>
		</table>

		@include('email.global_settings')
	</div>
	<body>

</html>