
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
{{-- @dd(""fkjf",$data) --}}
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
                                       <p style="text-align: justify;">A new job has been added to your account. Please refer to the details provided below.</p>
                                       <p style="display:flex;">
                                          <span style="width:28%;font-weight:600;">Status:</span>
                                          <span style="width:1%">:</span>
                                          <span style="width:69%;">
                                             <span style="color:#007bff;font-weight: 600;">{{ ucfirst(@$data->status) }}</span>
                                          </span>
                                       </p>
                                       <p style="display:flex;">
                                          <span style="width:28%;font-weight: 600;">{{ (@$data->self_company) ? 'Company' : 'Client' }} :</span>
                                          <span style="width: 70%;">:&nbsp;{{ @$data->clientCompany }}</span>
                                       </p>
                                       <p style="display:flex;">
                                          <span style="width:28%;font-weight: 600;">Job ID</span>
                                          <span style="width: 70%;">:&nbsp;{{ @$data->job_id }}</span>
                                       </p>
                                       
                                       <p style="display:flex;">
                                          <span style="width:28%;font-weight: 600;">Job Title</span>
                                          <span style="width: 70%;">:&nbsp;{{ @$data->job_title }}</span>
                                       </p>
                                       <p style="display:flex;">
                                          <span style="width:28%;font-weight: 600;">Experience</span>
                                          <span style="width: 70%;">:&nbsp;{{ @$data->experience }}</span>
                                       </p>
                                       <p style="display:flex;">
                                          <span style="width:28%;font-weight: 600;">Salary:</span>
                                          <span style="width: 70%;">:&nbsp;{{ @$data->salaryrange }}</span>
                                       </p>
                                       <p style="display:flex;">
                                          <span style="width:28%;font-weight: 600;">No. of Openings</span>
                                          <span style="width: 70%;">:&nbsp;{{ @$data->NoofOpenings }}
                                          </span>
                                       </p>
                                       <p style="display:flex;">
                                          <span style="width:28%;font-weight: 600;">Work Location</span>
                                          <span style="width: 70%;">:&nbsp;{{ @$data->location }}</span>
                                       </p>
                                       <p style="display:flex;">
                                          <span style="width:28%;font-weight: 600;">Remote</span>
                                          <span style="width: 70%;">:&nbsp;{{ @$data->remote }}</span>
                                       </p>
                                       <p style="display:flex;">
                                          <span style="width:28%;font-weight: 600;">Job Type</span>
                                          <span style="width: 70%;">:&nbsp;{{ @$data->JobType }}</span>
                                       </p>
                                        <p style="display:flex;">
                                          <span style="width:28%;font-weight: 600;">Department</span>
                                          <span style="width: 70%;">:&nbsp;{{ @$data->Department }}</span>
                                       </p>
                                        <p style="display:flex;">
                                          <span style="width:28%;font-weight: 600;">Qualification</span>
                                          <span style="width: 70%;">:&nbsp;{{ @$data->Qualification }}</span>
                                       </p>
                                       <p style="display:flex;">
                                          <span style="width:28%;font-weight: 600;">Hiring Manager</span>
                                          <span style="width: 70%;">:&nbsp;{{ @$data->hiringManager }}</span>
                                       </p>
                                       <p style="display:flex;">
                                          <span style="width:28%;font-weight: 600;">Recruiter</span>
                                          <span style="width: 70%;">:&nbsp;{{ @$data->recruiter }}</span>
                                       </p>
                                       <p style="display:flex;">
                                          <span style="width:28%;font-weight: 600;">Added By</span>
                                          <span style="width: 70%;">:&nbsp;{{ @$data->addedby }}</span>
                                       </p>
                                       <p style="display:flex;">
                                          <span style="width:28%;font-weight: 600;">Job Descriptions</span>
                                          <span style="width: 70%;">:
                                             {!! @$data->JobDescriptions !!}
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