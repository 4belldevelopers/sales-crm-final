<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<title>JERO - Joined us today</title>
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
               <table class="hedrtop" width="600" height="590" cellspacing="0" cellpadding="0" border="0" bgcolor="#fff" align="center">
                  <tbody style="font-size: 15px;">
                        <!-- This email will be send on Internal and Vendor -->
                        <!-- Subject: - Thank You – Your referral joined us today! -->
                        <tr>
                           <td style="line-height: normal;padding: 1em;" bgcolor="#FFFFFF">
                              <!-- <p class="name"></p> -->
                              <div class="message-section">
                                 <div class="col-12">
                                    <div>
                                       <p style="">Hi {{ @$data->VendorTeam }}</p>
                                       <p style="text-align: justify;">A Big Thank You for referring <span style="font-weight: 600;">{{ @$data->CandidateName }}  </span> for career opportunities at <span style="font-weight: 600;">{{ @$data->companyOrclientName }}.</span> We
                                          are glad to inform you that your referral has <span style="font-weight: 600;">joined.</span>
                                       </p>
                                       <p style="display:flex;">
                                          <span style="width:28%;font-weight: 600;">Candidate Status</span>
                                          <span style="width: 1%">:</span>
                                          <span style="width: 68%;">
                                             <span style="color:#06c732;font-weight: 600;">Joined</span>
                                          </span>
                                       </p>
                                       <p style="display:flex;">
                                          <span style="width:28%;font-weight: 600;">Candidate Name</span>
                                          <span style="width: 70%;">:&nbsp;{{ @$data->CandidateName }} </span>
                                       </p>
                                       <p style="display:flex;">
                                          <span style="width:28%;font-weight: 600;">Joining Date</span>
                                          <span style="width: 70%;">{{ @$data->Joiningdate }}</span>
                                       </p>
                                        <p style="display:flex;">
                                          <span style="width:28%;font-weight: 600;">Offer Amount</span>
                                          <span style="width: 70%;">{{ @$data->OfferAmount }}</span>
                                       </p>
                                       <p style="display:flex;">
                                          <span style="width:28%;font-weight: 600;">Joining Date</span>
                                          <span style="width: 70%;">{{ @$data->Joiningdate }}</span>
                                       </p>
                                        <p style="display:flex;">
                                          <span style="width:28%;font-weight: 600;">Position Type</span>
                                          <span style="width: 70%;">{{ @$data->employment_type }}</span>
                                       </p>
                                       <p style="display:flex;">
                                          <span style="width:28%;font-weight: 600;">Requisition</span>
                                          <span style="width: 70%;">
                                             <span>:&nbsp;{{ @$data->ReqID }} - {{ @$data->Reqname }}</span>
                                          </span>
                                       </p>
                                       
                                        <p style="display:flex;">
                                          <span style="width:28%;font-weight: 600;">{{ (@$data->selfCompany) ? 'Company' : 'Client' }} </span>
                                          <span style="width: 70%;">:&nbsp;{{ @$data->companyOrclientName }}</span>
                                       </p>
                                       
                                       <p style="display:flex;">
                                          <span style="width:28%;font-weight: 600;">Referred by</span>
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