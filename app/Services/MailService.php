<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\EmailSetting;
use App\Services\ElasticService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class MailService
{
    public function sendEmail($to, $subject, $view, $data, $attachments, $fromAddress, $fromName, $cc = [],$company_id='')
    {

        $mail = new PHPMailer(true);
            $fromAddress_reply = $fromAddress;
            $fromName_reply = $fromName;
            // smtp.elasticemail.com	Vibhanshu@4bell.com	F35C64FB9C2B8BE5DEBC3B13DF05198B5947
            $smtp = EmailSetting::where('status','=',0)->where('id', 12)->orderByDesc('id')->first();
            if($smtp->id==12 || $smtp->id==11){
                $fromAddress = ($smtp->mail_from_email) ? $smtp->mail_from_email : $fromAddress;
            }
       
        // dd($fromAddress_reply,$fromAddress);
 
        try {
            // Server settings

            $mail->SMTPDebug = 0;
            $mail->isSMTP();
            $mail->Host = (@$smtp->notification_mail_host) ? $smtp->notification_mail_host : 'smtp-relay.brevo.com';
            $mail->SMTPAuth = true;
            $mail->Username = (@$smtp->notification_mail_username) ? $smtp->notification_mail_username : '619eeb001@smtp-brevo.com';
            $mail->Password = (@$smtp->notification_mail_password) ? $smtp->notification_mail_password : 'rqFS19y8VbxXRZLs';
            $mail->SMTPSecure = (@$smtp->notification_mail_encryption) ? $smtp->notification_mail_encryption : 'ssl';
            $mail->Port = (@$smtp->notification_mail_port) ? $smtp->notification_mail_port : 465;
            
            // Recipients
            // $mail->addBCC($fromAddress_reply);
            $mail->setFrom($fromAddress, $fromName);
            $mail->addAddress($to);
            if (!empty($fromAddress_reply)) {
                    $cc[] = $fromAddress_reply;
            }
             if(count($cc) > 0){
                foreach ($cc as $ccAddress) {
                    $mail->addCC($ccAddress);
                }
             }
            
            // Reply-to
            // if ($replyTo) {
                $mail->addReplyTo($fromAddress_reply, $fromName_reply);
            // }
            
            // dd($attachments);
            // if(count($attachments) > 0){
            // foreach ($attachments as $attachment) {
            //     $attachment = $this->downloadAttachment($attachment['path']);
            //     if ($attachment) {
            //         if(!empty($attachment['filename'])){
            //             $mail->addStringAttachment($attachment['data'], $attachment['filename']); // Add attachments 
            //         }
                   
            //     }
            // }
            // }
            if(count($attachments) > 0){
            foreach ($attachments as $attachment) {
                // dd($attachment['path']);
                // $attachment = $this->downloadAttachment($attachment['path']);
                $attachmentData = $this->downloadAttachment($attachment['path']);
                if ($attachmentData) {
                    if(!empty($attachmentData['filename'])){
                        // dd($attachment['filename']);
                        // $candidateName = $attachment['resumeRow']; // safe for filenames
                        // $customName = $candidateName . '_' . date('Ymd_His') . '.' . pathinfo($attachment['filename'], PATHINFO_EXTENSION);
                        $filename = $attachment['filename'] ?? $attachmentData['filename'];

                          
                        $mail->addStringAttachment($attachmentData['data'], $filename); // Add attachments 
                    }
                   
                }
                // $mail->addAttachment($attachment['path'], $attachment['name']);
            }
            }
            $dataical = (array)$data;
            if(isset($dataical['interviewLink']) && !empty($dataical['interviewLink']) &&
            isset($dataical['eventTitle']) && !empty($dataical['eventTitle']) &&
            isset($dataical['eventDescription']) && !empty($dataical['eventDescription']) &&
            isset($dataical['eventStartTime']) && !empty($dataical['eventStartTime']) &&
            isset($dataical['eventEndTime']) && !empty($dataical['eventEndTime'])) {
        
            
             // Interview Details
             $interviewLink = $dataical['interviewLink']; // Interview link
             $eventTitle = $dataical['eventTitle'];
             $eventDescription = $dataical['eventDescription'];
             $eventStartTime = $dataical['eventStartTime']; // Replace with actual start time
             $eventEndTime = $dataical['eventEndTime'];   // Replace with actual end time
 
             // Generate iCalendar content
             $uid = uniqid(); // Unique identifier for the event
             $eventStart = date('Ymd\THis', strtotime($eventStartTime)); // Format: YYYYMMDDTHHMMSS
             $eventEnd = date('Ymd\THis', strtotime($eventEndTime));
             $eventTimestamp = date('Ymd\THis', time());
             $ical = "BEGIN:VCALENDAR\r\n";
             $ical .= "VERSION:2.0\r\n";
             $ical .= "PRODID:-//JERO//NONSGML v1.0//EN\r\n";
             $ical .= "METHOD:REQUEST\r\n";
             $ical .= "BEGIN:VEVENT\r\n";
             $ical .= "UID:$uid\r\n";
             $ical .= "DTSTAMP:$eventTimestamp\r\n";
             $ical .= "DTSTART:$eventStart\r\n";
             $ical .= "DTEND:$eventEnd\r\n";
             $ical .= "SUMMARY:$eventTitle\r\n";
             $ical .= "DESCRIPTION:$eventDescription\r\n";
             $ical .= "ORGANIZER;CN=\"$fromName\":mailto:$fromAddress\r\n";
             $ical .= "ATTENDEE;RSVP=TRUE;CN=\"$to\":mailto:$to\r\n";
             // Add CC and BCC to iCalendar ATTENDEE list
             foreach ($cc as $ccEmail) {
                 $ical .= "ATTENDEE;RSVP=TRUE;CN=\"CC User\":mailto:$ccEmail\r\n";
             }
             // foreach ($cc as $bccEmail) {
             //     $ical .= "ATTENDEE;RSVP=TRUE;CN=\"BCC User\":mailto:$bccEmail\r\n";
             // }
             $ical .= "LOCATION:$interviewLink\r\n";
             $ical .= "URL:$interviewLink\r\n";
             $ical .= "STATUS:CONFIRMED\r\n";
             $ical .= "SEQUENCE:0\r\n";
             $ical .= "PRIORITY:5\r\n";
             $ical .= "CLASS:PUBLIC\r\n";
             $ical .= "TRANSP:OPAQUE\r\n";
             $ical .= "END:VEVENT\r\n";
             $ical .= "END:VCALENDAR\r\n";
 
             // Attach iCalendar file
             $mail->addStringAttachment($ical, "interview.ics", 'base64', 'text/calendar; charset=utf-8; method=REQUEST');

            }
        
            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            // $mail->Body    = $body;
            $mail->Body = view($view, ['data'=>$data])->render();
            $mail->send();
            $dailyCount = $smtp->dailyCount+1;
            DB::table('smtp_settings')->where('id','=',$smtp->id)->update(['dailyCount'=>$dailyCount]);
            $created_at = now();
             $inserted = DB::table('email_count_companies')->insertGetId(['to_email'=>$to,'subject'=>$subject,'mailBody'=>json_encode($data),'emailType'=>5,'company_id'=>$company_id,'created_at'=>$created_at,'updated_at'=>$created_at]);
            $elasticService = app(ElasticService::class);
            $response = $elasticService->getClient()->update([
                        'index' => ELASTIC_EMAIL_COUNT,
                        'id'    => $inserted ,
						'retry_on_conflict' => 5,
                        'body'  => [
                            'doc' => [ 
								'id' =>  $inserted, 
								'to_email' => $to,
								'subject' => $subject,
								'mailBody' => json_encode($data),
								'emailType' => 5,
								'company_id' => $company_id,
								'created_at' => ($created_at) ? Carbon::parse($created_at)->toIso8601String() : null,
								'updated_at' => ($created_at) ? Carbon::parse($created_at)->toIso8601String() : null
							],
							'doc_as_upsert' => true,
							'detect_noop' => false
						],
                    ]);
            return true;
        } catch (Exception $e) {
            return "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }
    
    
    
    private function downloadAttachment($url)
    {
        try {
            $filename = basename($url);
            // Read directly from disk when a file path is given — avoids self-referencing
            // HTTP calls that break in queue workers when APP_URL doesn't match the server.
            if (!filter_var($url, FILTER_VALIDATE_URL) && file_exists($url)) {
                return ['filename' => $filename ?: '', 'data' => file_get_contents($url)];
            }
            $response = Http::get($url);
            return ['filename' => $filename ?: '', 'data' => $response->body()];
        } catch (\Exception $e) {
            return null;
        }
    }
    
}


?>