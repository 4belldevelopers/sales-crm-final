
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<title>Meecruit - Active to Hold Requisition</title>
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
               <table class="hedrtop" width="600" height="450" cellspacing="0" cellpadding="0" border="0" bgcolor="#fff" align="center">
                  <tbody style="font-size: 15px;">
                        <!-- This email will be send to Everyone -->
                        <!-- Subject: Subject: "IOS Developer moved from Active to On Hold!" -->
                        <tr>
                           <td style="line-height: normal;padding: 1em;" bgcolor="#FFFFFF">
                              <!-- <p class="name"></p> -->
                              <div class="message-section">
                                 <div class="col-12">
                                    <div>
                                       <p>Hi {{ $data->username }}</p>
                                       <p style="text-align: justify;"> <span
                                             style="font-weight: 600;">
                                           
                                           I am writing to inform you that the status of the job position detailed below has been updated.
                                           
                                           <!--"{{ $data->job_id }} - {{ $data->job_title }}"</span> from <span style="font-weight: 600;">"{{ $data->status_pr }}-->
                                           <!--  to {{ $data->status_curr }}".</span> We will let you know if we required more profiles.-->
                                       </p>
                                       <p style="display:flex;">
                                          <span style="width:28%;font-weight:600;">Status</span>
                                          <span style="width:1%">:</span>
                                          <span style="width:69%;">
                                             @if($data->status_curr=='active')
                                             <span style="color:#0c9936;font-weight: 600;">{{ $data->status_curr }}</span>
                                             @endif
                                             @if($data->status_curr=='hold')
                                             <span style="color:#ffa800;font-weight: 600;">{{ $data->status_curr }}</span>
                                             @endif
                                             @if($data->status_curr=='inactive')
                                             <span style="color:#ff0000;font-weight: 600;">{{ $data->status_curr }}</span>
                                             @endif
                                          </span>
                                       </p>
                                       <p style="display:flex;">
                                          <span style="width:28%;font-weight: 600;">Job Title</span>
                                          <span style="width: 70%;">:&nbsp;{{ $data->job_title }}</span>
                                       </p>
                                       <p style="display:flex;">
                                          <span style="width:28%;font-weight: 600;">Location</span>
                                          <span style="width: 70%;">:&nbsp;{{ $data->location }}</span>
                                       </p>
                                        <p style="display:flex;">
                                          <span style="width:28%;font-weight: 600;">{{ (@$data->selfCompany) ? 'Company' : 'Client' }} </span>
                                          <span style="width: 70%;">:&nbsp;{{ @$data->companyOrclientName }}</span>
                                       </p>
                                    </div>
                                    
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