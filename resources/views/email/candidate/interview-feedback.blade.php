
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<title>Meecruit - Interview Schedule</title>
<meta http-equiv="x-dns-prefetch-control" value-equiv="off">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
   /*@import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,200;0,300;0,400;1,200&display=swap');*/
</style>
</head>
<body style="width:auto;margin: 0;padding: 0;box-sizing: border-box;background: #eee;">
<div class="moz-forward-container">
   <table width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#EFEFEF" align="center">
      <tbody style="font-family: Arial,Helvetica,sans-serif;line-height: 1.5;font-weight: normal;font-size: 15px;color: #2F3044;min-height: 100%;margin: 0;padding: 0;width: 100%;">
      <tr>
            <td>
               <table class="hedrtop" width="1000" cellspacing="0" cellpadding="0" border="0" bgcolor="#fff" align="center">
                  <tbody style="font-size: 15px;">
                   <!-- Subject:: Interview Scheduled | "Requisition name" | "Company name" | "Interview date and time" | "Screening Stage"  stage -->
                        <tr>
                           <td style="line-height: normal;padding: 1em;" bgcolor="#FFFFFF">
                              <!-- <p class="name"></p> -->
                              <div class="message-section">
                                 <div class="col-12">
                                    <p style="padding-top:.2em;margin:0;padding:2px 0;">Dear&nbsp;{{ $data->CandidateName }}</p>
                                    <p style="text-align: justify;">
                                        Kindly find the feedback of your last Interview</p>
                                        
                
                                        
                                        <table  width="1000">
                                        	<tbody>
                                        		<tr>
                                        			<td>
                                        			<strong>Interview Status</strong>
                                        			</td>
                                        			<td>
                                        			{{ $data->feedback_status }}
                                        			</td>
                                        		</tr>
                                        		<tr>
                                        			<td>
                                        			<strong>Candidate Name</strong>
                                        			</td>
                                        			<td>{{ $data->CandidateName }}</td>
                                        		</tr>
                                        		<tr>
                                        			<td>
                                        			<strong>Company/Client Name</strong>
                                        			</td>
                                        			<td>{{ $data->CompanyName }}</td>
                                        		</tr>
                                        		<tr>
                                        			<td>
                                        			<strong>Job Title</strong>
                                        			</td>
                                        			<td>{{ $data->Reqname }}</td>
                                        		</tr>
                                        		<tr>
                                        			<td>
                                        			<strong>Job ID</strong>
                                        			</td>
                                        			<td>{{ $data->ReqID }}</td>
                                        		</tr>
                                        		<tr>
                                        			<td>
                                        			<strong>Interview Date &amp; Time</strong>
                                        			</td>
                                        			<td>{{ $data->InterviewDate }}</td>
                                        		</tr>
                                        		<tr>
                                        			<td>
                                        			<strong>Stage</strong>
                                        			</td>
                                        			<td>{{ $data->Stage }}</td>
                                        		</tr>
                                        		<tr>
                                        			<td>
                                        			<strong>Substage</strong>
                                        			</td>
                                        			<td>{{ $data->Substage }}</td>
                                        		</tr>
												{{-- <tr>
                                        			<td>
                                        			<strong>Comment</strong>
                                        			</td>
                                        			<td>{{ $data->feedback_comment }}</td>
                                        		</tr> --}}
                                        	</tbody>
                                        </table>


										@if(empty($data->feedbackMapped))
										<p>No data available</p>
									@else

									
									   <!-- FEEDBACK TABLE -->
									   <!-- FEEDBACK TABLE .collapse{visibility:collapse}-->
										<table width="100%" cellpadding="0" cellspacing="0" border="0"
											style="border-collapse: collapse; font-size:14px;">

											<tr style="background:#1A73E8; color:#fff;">
												<th style="padding:10px; text-align:left; width:180px;
														border:1px solid #d0d0d0;">
													Skill
												</th>
												<th style="padding:10px; text-align:left; width:120px;
														border:1px solid #d0d0d0;">
													Rating
												</th>
												<th style="padding:10px; text-align:left; width:280px;
														border:1px solid #d0d0d0;">
													Remarks
												</th>
											</tr>

											@foreach($data->feedbackMapped as $feedbackId => $feedbacks)

											<!-- SECTION HEADER -->
											<tr>
												<td colspan="3" 
													style="background:#f1f5ff; padding:10px; 
													border:1px solid #d0d0d0; font-weight:bold;">
													Feedback ID: {{ $feedbackId }}

													<div style="font-size:12px; color:#555; margin-top:5px;">
														Reviewer: {{ $feedbacks['by_name'] }} ({{ $feedbacks['designation'] }})<br>
														{{ date("d M Y h:i A", strtotime($feedbacks['created_at'])) }}
													</div>
												</td>
											</tr>

											@foreach($feedbacks['feedbackdata'] as $feedback)

											<tr style="background:#fff;">
												<!-- SKILL -->
												<td style="padding:10px; width:180px; border:1px solid #d0d0d0; vertical-align:top;">
													<b>{{ $feedback['skills'] }}</b><br>
													<span style="color:#777; font-size:12px;">
														{{ $feedback['experience'] }}
													</span>
												</td>

												<!-- RATING -->
												<td style="padding:10px; width:120px; border:1px solid #d0d0d0; vertical-align:top;">
													<span style="font-size:12px; color:#555;">
														{{ $feedback['ratings'] == 1 ? 'Bad' : '' }}
														{{ $feedback['ratings'] == 2 ? 'Poor' : '' }}
														{{ $feedback['ratings'] == 3 ? 'Average' : '' }}
														{{ $feedback['ratings'] == 4 ? 'Best' : '' }}
														{{ $feedback['ratings'] == 5 ? 'Very Best' : '' }}
													</span>
												</td>

												<!-- REMARKS -->
												<td style="padding:10px; width:280px; border:1px solid #d0d0d0; vertical-align:top;">
													{{ $feedback['remarks'] }}
												</td>
											</tr>

											@endforeach

											<!-- COMMENT ROW -->
											<tr style="background:#fafafa;">
												<td style="padding:10px; font-weight:bold; width:180px; border:1px solid #d0d0d0;">
													Final Comment
												</td>
												<td colspan="2" style="padding:10px; width:400px; border:1px solid #d0d0d0;">
													{{ $feedbacks['message'] }}
												</td>
											</tr>

											@endforeach

										</table>


									@endif



                                                                        
                                    <!--<p style="display:flex;">-->
                                    <!--   <span style="width:27%;font-weight: 600;">About Organization</span>-->
                                    <!--   <span style="width:70%;text-align: justify;">{!! $data->AboutOrganization !!}-->
                                    <!--   </span>-->
                                    <!--</p>-->
                                    
                                    <!-- <p>For additional information or clarifications, please get in touch with Payal Arora!</p> -->
                                    <p style="padding-bottom:.5em;margin:0;padding:2px 0;">Best Regards,<br>
                                       Recruitment Department<br>
                                       {{ $data->CompanyName }}<br>
                                    
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