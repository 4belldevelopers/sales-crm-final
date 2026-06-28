<?php  
  use App\Helper\GeneralMethod;
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<title>Meecruit - Joined us today</title>
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
                                       <p style="">Hi "Internal/Vendor"</p>
                                       <p style="text-align: justify;">A Big Thank You for referring <span style="font-weight: 600;">"Applicant
                                             Name"</span> for career opportunities at <span style="font-weight: 600;">"Company Name".</span> We
                                          are glad to inform you that your referral has <span style="font-weight: 600;">joined.</span>
                                       </p>
                                       <p style="display:flex;">
                                          <span style="width:28%;font-weight: 600;">Applicant Status</span>
                                          <span style="width: 1%">:</span>
                                          <span style="width: 68%;">
                                             <span style="color:#c000ff;font-weight: 600;">"Joined"</span>
                                          </span>
                                       </p>
                                       <p style="display:flex;">
                                          <span style="width:28%;font-weight: 600;">Applicant Name</span>
                                          <span style="width: 70%;">:&nbsp;"Prateek Vats"</span>
                                       </p>
                                       <p style="display:flex;">
                                          <span style="width:28%;font-weight: 600;">Joining Date</span>
                                          <span style="width: 70%;">:&nbsp;"22-07-2021"</span>
                                       </p>
                                       <p style="display:flex;">
                                          <span style="width:28%;font-weight: 600;">Job Positions</span>
                                          <span style="width: 70%;">
                                             <span>:&nbsp;"5516 Dotnet Developer"</span>
                                          </span>
                                       </p>
                                       <p style="display:flex;">
                                          <span style="width:28%;font-weight: 600;">Hiring Manager</span>
                                          <span style="width: 70%;">
                                             <span>:&nbsp;Anjali</span>
                                          </span>
                                       </p>
                                       <p style="display:flex;">
                                          <span style="width:28%;font-weight: 600;">Location</span>
                                          <span style="width: 70%;">
                                             <span>:&nbsp;"Sector - 63, Noida"</span>
                                          </span>
                                       </p>
                                       <p style="display:flex;">
                                          <span style="width:28%;font-weight: 600;">Referred by</span>
                                          <span style="width: 70%;">
                                             <span>:&nbsp;"Praveen"</span>
                                          </span>
                                       </p>
                                       <p style="display:flex;">
                                          <span style="width:28%;font-weight: 600;">Submitted On</span>
                                          <span style="width: 70%;">
                                             <span>:&nbsp;"19-July-2021 | 4:58 PM"</span>
                                          </span>
                                       </p>
                                    </div>
                                    <?php  
                                    $company_name = GeneralMethod::CompantName();
                                     ?>
                                    <p style="padding-bottom:.5em;margin:0;padding:2px 0;">Best Regards,<br>
                                       "<?= $company_name->company_name ?>"<br>
                                       Recruitment Department
                                    </p>
                                    <p style="text-align: justify;">Replies to this message are undeliverable and will not reach the Human
                                       Resources Department. Please do not reply.</p>
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