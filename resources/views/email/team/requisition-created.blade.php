<?php  
  use App\Helper\GeneralMethod;
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<title>Meecruit - Active Requisition</title>
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
                        <!-- This email will be send to Vendor -->
                        <!-- Subject: New Requisition "Req. ID" Added "Client/Company name" -->
                        <tr>
                           <td style="line-height: normal;padding: 1em;" bgcolor="#FFFFFF">
                              <!-- <p class="name"></p> -->
                              <div class="message-section">
                                 <div class="col-12">
                                    <div>
                                       <p style="">Hi "Team"</p>
                                       <p style="text-align: justify;">New Requisition is created at "client/company name".</p>
                                       <p style="text-align: justify;">The following link allows you to view the job description and refer a candidate online. You may also want to take a look at the list of all current job openings at our
                                          company.</p>
                                       <p style="display:flex;">
                                          <span style="width:28%;font-weight:600;">Status</span>
                                          <span style="width:1%">:</span>
                                          <span style="width:69%;">
                                             <span style="color:#007bff;font-weight: 600;">"Active"</span>
                                          </span>
                                       </p>
                                       <p style="display:flex;">
                                          <span style="width:28%;font-weight: 600;">Job Title</span>
                                          <span style="width: 70%;">:&nbsp;"Magento Developer"</span>
                                       </p>
                                       <p style="display:flex;">
                                          <span style="width:28%;font-weight: 600;">Job ID</span>
                                          <span style="width: 70%;">:&nbsp;"1001"</span>
                                       </p>
                                       <p style="display:flex;">
                                          <span style="width:28%;font-weight: 600;">Company Name</span>
                                          <span style="width: 70%;">:&nbsp;"4Bell Technology"</span>
                                       </p>
                                       <p style="display:flex;">
                                          <span style="width:28%;font-weight: 600;">Location</span>
                                          <span style="width: 70%;">:&nbsp;"Noida"</span>
                                       </p>
                                       <p style="display:flex;">
                                          <span style="width:28%;font-weight: 600;">Hiring Manager</span>
                                          <span style="width: 70%;">:&nbsp;"Amit Kumar"</span>
                                       </p>
                                       <p style="display:flex;">
                                          <span style="width:28%;font-weight: 600;">Recruiter</span>
                                          <span style="width: 70%;">:&nbsp;"Pradeep Kumar"</span>
                                       </p>
                                       <p style="display:flex;">
                                          <span style="width:28%;font-weight: 600;">Job Descriptions link</span>
                                          <span style="width: 70%;">:
                                             "<a href="javascript:void(0);"
                                                style="text-decoration:none;color: #009EF7">http://tm.vibhanshujaiswal.com/admin</a>"
                                          </span>
                                       </p>
                                       <p style="display:flex;">
                                          <span style="width:28%;font-weight: 600;">No. of Openings</span>
                                          <span style="width: 70%;">:&nbsp;"650 Openings"
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