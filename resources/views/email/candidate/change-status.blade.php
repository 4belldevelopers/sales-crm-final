
<html>

<head>
   <meta http-equiv="content-type" content="text/html; charset=UTF-8">
   <title>Meecruit - Change Status</title>
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
                     <tbody style="font-size: 15px;">
                        <!-- This email will be send on Internal and Team Member vendor -->
                        <!-- Subject:: Status changed "Candidate name" | "Requisiton name" | "Company name/Client name" -->
                        <tr>
                           <td style="line-height: normal;padding: 1em;" bgcolor="#FFFFFF">
                              <!-- <p class="name"></p> -->
                              <div class="message-section">
                                 <div class="col-12">
                                    <div>
                                       <p style="padding-top:.2em;margin:0;padding:2px 0;">Hi {{ @$data->VendorTeam }}</p>
                                       <p style="text-align: justify;">Below candidates, who you submitted for <span style="font-weight: 600;">"{{ @$data->ReqID }} - {{ @$data->Reqname }}"</span> has moved to <span style="font-weight: 600;"> </span> below stage.</p>

                                       <p style="display:flex;">
                                          <span style="width:28%;font-weight: 600;">Candidate Status</span>
                                             <span style="width: 1%">:</span>
                                             <span style="width: 68%;">
                                            
                                                @if(@$data->CandidateStatus==1)
                                                <span style="color:#ff0202;font-weight: 600">Duplicate</span>&nbsp;
                                                @endif
                                                @if(@$data->CandidateStatus==0)
                                                <span style="color:#06c732;font-weight: 600">Fresh</span>
                                                @endif
                                                
                                             </span>
                                       </p>
                                       <p style="display:flex;">
                                          <span style="width:28%;font-weight: 600;">Status Changed</span>
                                             <span style="width: 1%">:</span>
                                             <span style="width: 68%;">
                                                <span style="color:#8b6727;font-weight: 600">{{ @$data->prev_process_name }}</span>
                                                to
                                                <span style="color:#ff4098;font-weight: 600">{{ @$data->CurrentStage }}</span>
                                             </span>
                                       </p>
                                       <p style="display:flex;">
                                          <span style="width:28%;font-weight: 600;">Current Sub Stage</span>
                                          <span style="width: 1%">:</span>
                                          <span style="width: 68%;">
                                             <span style="color:gray;font-weight: 600;">{{ @$data->SubStage }}</span>
                                          </span>
                                       </p>
                                       <p style="display:flex;">
                                          <span style="width:28%;font-weight: 600;">Candidate Name</span>
                                          <span style="width: 70%;">:&nbsp;{{ @$data->CandidateName }}</span>
                                       </p>
                                       <p style="display:flex;">
                                          <span style="width:28%;font-weight: 600;">Experience</span>
                                          <span style="width: 70%;">
                                             <span>:&nbsp;{{ @$data->Experience }} Yrs</span>
                                          </span>
                                       </p>
                                       <p style="display:flex;">
                                          <span style="width:28%;font-weight: 600;">Position</span>
                                          <span style="width: 70%;">
                                             <span>:&nbsp;{{ @$data->Position }}</span>
                                          </span>
                                       </p>
                                       <p style="display:flex;">
                                          <span style="width:28%;font-weight: 600;">{{ (@$data->selfCompany) ? 'Company' : 'Client' }} </span>
                                          <span style="width: 70%;">:&nbsp;{{ @$data->companyOrclientName }}</span>
                                       </p>
                                       <p style="display:flex;">
                                          <span style="width:28%;font-weight: 600;">Notice Period</span>
                                          <span style="width: 70%;">
                                             <span>:&nbsp;{{ @$data->NoticePeriod }}</span>
                                          </span>
                                       </p>
                                       <p style="display:flex;">
                                          <span style="width:28%;font-weight: 600;">Reffered By</span>
                                          <span style="width: 70%;">
                                             <span>:&nbsp;{{ @$data->RefferedBy }} - {{ (@$data->VendorTeam)}}</span>
                                          </span>
                                       </p>
                                       <p style="display:flex;">
                                          <span style="width:28%;font-weight: 600;">Submitted On</span>
                                          <span style="width: 70%;">
                                             <span>:&nbsp;{{ @$data->SubmittedOn }}</span>
                                          </span>
                                       </p>
                                       <p style="display:flex;">
                                          <span style="width:28%;font-weight: 600;">Comment</span>
                                          <span style="width: 70%;">
                                             <span>:&nbsp;{{ @$data->comments }}</span>
                                          </span>
                                       </p>
                                    </div>
                                   
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