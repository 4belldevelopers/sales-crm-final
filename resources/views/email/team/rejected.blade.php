<?php  
  use App\Helper\GeneralMethod;
?>
<html>

<head>
   <meta http-equiv="content-type" content="text/html; charset=UTF-8">
   <title>Meecruit - Rejected</title>
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
                        <!-- Subject:: Duplicate "Candidate name" on "Requisiton name"-->
                        <tr>
                           <td style="line-height: normal;padding: 1em;" bgcolor="#FFFFFF">
                              <!-- <p class="name"></p> -->
                              <div class="message-section">
                                 <div class="col-12">
                                    <div>
                                       <p style="padding-top:.2em;margin:0;padding:2px 0;">Hi "Internal/Team Member"</p>
                                       <p style="text-align: justify;">Below candidates, who you submitted for <span style="font-weight: 600;">"Job ID - Job
                                             Title"</span> kindly find the status below</p>

                                       <p style="display:flex;">
                                          <span style="width:28%;font-weight: 600;">Candidate Status</span>
                                          <span style="width: 1%">:</span>
                                          <span style="width: 68%;">
                                             "<span style="color:#ff0202;font-weight: 600">Duplicate</span>"
                                          </span>
                                       </p>
                                       <p style="display:flex;">
                                          <span style="width:28%;font-weight: 600;">Current Stage</span>
                                          <span style="width: 1%">:</span>
                                          <span style="width: 68%;">
                                             "<span style="color:#ff0202;font-weight: 600">Rejected</span>"
                                          </span>
                                       </p>
                                       <p style="display:flex;">
                                          <span style="width:28%;font-weight: 600;">Sub Stage</span>
                                          <span style="width: 1%">:</span>
                                          <span style="width: 68%;">
                                             <span style="color:gray;font-weight: 600;">"If Any"</span>
                                          </span>
                                       </p>
                                       <p style="display:flex;">
                                          <span style="width:28%;font-weight: 600;">Candidate Name</span>
                                          <span style="width: 70%;">:&nbsp;"Sagun"</span>
                                       </p>
                                       <p style="display:flex;">
                                          <span style="width:28%;font-weight: 600;">Experience</span>
                                          <span style="width: 70%;">
                                             <span>:&nbsp;"4 Yrs"</span>
                                          </span>
                                       </p>
                                       <p style="display:flex;">
                                          <span style="width:28%;font-weight: 600;">Position</span>
                                          <span style="width: 70%;">
                                             <span>:&nbsp;"Job Title"</span>
                                          </span>
                                       </p>
                                       <p style="display:flex;">
                                          <span style="width:28%;font-weight: 600;">Notice Period</span>
                                          <span style="width: 70%;">
                                             <span>:&nbsp;"15 Days"</span>
                                          </span>
                                       </p>
                                       <p style="display:flex;">
                                          <span style="width:28%;font-weight: 600;">Reffered By</span>
                                          <span style="width: 70%;">
                                             <span>:&nbsp;"Anjali ("Company name")"</span>
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