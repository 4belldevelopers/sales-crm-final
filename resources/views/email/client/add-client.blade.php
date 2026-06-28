
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<title>Meecruit - Add Client</title>
<meta http-equiv="x-dns-prefetch-control" value-equiv="off">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
   /*@import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,200;0,300;0,400;1,200&display=swap');*/
</style>
</head>
{{-- @dd($data->Location,$data->contacDetails) --}}
<body style="width:auto;margin: 0;padding: 0;box-sizing: border-box;background: #eee;">
<div class="moz-forward-container">
   <table width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#EFEFEF" align="center">
      <tbody style="font-family: Arial,Helvetica,sans-serif;line-height: 1.5;font-weight: normal;font-size: 15px;color: #2F3044;min-height: 100%;margin: 0;padding: 0;width: 100%;">
         <tr>
            <td>
               <table class="hedrtop" width="600" height="480" cellspacing="0" cellpadding="0" border="0" bgcolor="#fff" align="center">
                  <tbody style="font-size: 15px;">
                        <!-- This email will be send on Internal | Team Members | Added by | Submited on-->
                        <!-- Subject line : Added New Client "Client Name" -->
                        <tr>
                           <td style="line-height: normal;padding: 1em;" bgcolor="#FFFFFF">
                              <!-- <p class="name"></p> -->
                              <div class="message-section">
                                 <div class="col-12">
                                    <p style="padding-top:.2em;margin:0;padding:2px 0;">Dear {{ @$data->added_in_con_user }}</p>
                                    <p>You have successfully Added new client details within your account for <span style="font-weight: 600;">{{ @$data->CompanyName }}.</span>
                                    </p>
                                    <div>
                                       {{-- <p style="display:flex;">
                                          <span style="width:28%;font-weight: 600;">Status</span>
                                          <span style="width: 1%">:</span>
                                          <span style="width: 68%;">
                                             "<span style="color:#007bff;font-weight: 600">Active</span>&nbsp;|
                                             <span style="color:#ffa800;font-weight: 600">Hold</span>&nbsp;|
                                             <span style="color:#ff0202;font-weight: 600">Closed</span>"
                                          </span>
                                       </p> --}}
                                       <p style="display:flex;">
                                          <span style="width:28%;font-weight: 600;">Client ID</span>
                                          <span style="width: 70%;">:&nbsp;{{ @$data->ClientID }}</span>
                                       </p>
                                       <p style="display:flex;">
                                          <span style="width:28%;font-weight: 600;">Client Name</span>
                                          <span style="width: 70%;">:&nbsp;{{ @$data->ClientName }}</span>
                                       </p>
                                      
                                       <!--@if(@$data->Location)-->
                                       <!--@foreach(@$data->Location as $loca)-->
                                       <!--<p style="display:flex;">-->
                                       <!--   <span style="width:28%;font-weight: 600;">Location</span>-->
                                       <!--   <span style="width: 70%;">-->
                                       <!--      <span>:&nbsp;{{  $loca->address  }}</span>-->
                                       <!--   </span>-->
                                       <!--</p>-->
                                       <!--@endforeach-->
                                       <!--@endif-->

                                      
                                       
                                       @if(@$data->contacDetails)
                                       @foreach(@$data->contacDetails as $conta_val)
                                       <p style="display:flex;">
                                          <span style="width:28%;font-weight: 600;">Contact Person</span>
                                          <span style="width: 70%;">:&nbsp;{{ $conta_val->contact_person_name }}</span>
                                       </p>
                                       <p style="display:flex;">
                                          <span style="width:28%;font-weight: 600;">Contact Email</span>
                                          <span style="width: 70%;">:&nbsp;{{  $conta_val->contact_person_email }}</span>
                                       </p>
                                       <p style="display:flex;">
                                          <span style="width:28%;font-weight: 600;">Contact Number</span>
                                          <span style="width: 70%;">:&nbsp;{{  $conta_val->contact_person_number }}</span>
                                       </p>
                                       <p style="display:flex;">
                                          <span style="width:28%;font-weight: 600;">Designation</span>
                                          <span style="width: 70%;">:&nbsp;{{  $conta_val->contact_person_designation }}</span>
                                       </p>
                                         
                                       @endforeach
                                       @endif
                                       <p style="display:flex;">
                                          <span style="width:28%;font-weight: 600;">Added By</span>
                                          <span style="width: 70%;">:&nbsp;{{ @$data->addedBy }}</span>
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