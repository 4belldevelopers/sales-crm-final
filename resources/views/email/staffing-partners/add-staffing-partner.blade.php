<?php  
  use App\Helper\GeneralMethod;
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<title>Meecruit - Add Staffing Partner</title>
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
               <table class="hedrtop" width="600" height="400" cellspacing="0" cellpadding="0" border="0" bgcolor="#fff" align="center">
                  <tbody style="font-size: 15px;">
                        <!-- This email will be send on Added by and admin -->
                        <!-- Subject:: New Staffing partner added -->
                        <tr>
                           <td style="line-height: normal;padding: 1em;" bgcolor="#FFFFFF">
                              <!-- <p class="name"></p> -->
                              <div class="message-section">
                                 <div class="col-12">
                                    <p style="padding-top:.2em;margin:0;padding:2px 0;">Dear "Added by/Admin Name"
                                    </p>
                                    <p>You have been invited to join the Talentmily team from <span style="font-weight: 600;">"Company Name".</span></p>
                                    <div>
                                       <p style="display:flex;">
                                          <span style="width: 28%;font-weight: 600;">Status</span>
                                          <span style="width: 1%;">:</span>
                                          <span style="width: 69%">
                                             "<span style="color:#3699ff;font-weight: 600">Active</span>&nbsp;|
                                             <span style="color:#ff0202;font-weight: 600">Closed</span>"
                                          </span>
                                       </p>
                                       <p style="display:flex;">
                                          <span style="width:28%;font-weight: 600;">Staffing Partner ID</span>
                                          <span style="width: 70%;">:&nbsp;"1001"</span>
                                       </p>
                                       <p style="display:flex;">
                                          <span style="width:28%;font-weight: 600;">Staffing Partner Name</span>
                                          <span style="width: 70%;">:&nbsp;"Wipro"</span>
                                       </p>
                                       <p style="display:flex;">
                                          <span style="width:28%;font-weight: 600;">
                                             Contact Person</span>
                                          <span style="width: 70%;">:&nbsp;"Dummy One"</span>
                                       </p>
                                       <p style="display:flex;">
                                          <span style="width: 28%;font-weight:600;">Mobile</span>
                                          <span style="width: 70%">:&nbsp;"0909090909"</span>
                                       </p>
                                       <p style="display:flex;">
                                          <span style="width: 28%;font-weight: 600">Email</span>
                                          <span style="width: 70%;">:&nbsp;"new@wipro.com"</span>
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