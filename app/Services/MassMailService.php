<?php

namespace App\Services;

use App\Models\EmailSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class MassMailService
{
    public function sendEmail($to, $subject, $view, $data, $attachments, $fromAddress, $fromName, $cc = [], $company_id = '')
    {

        // ✅ 1. Get SMTP Config
        $smtp = EmailSetting::where('status', '=', 0)->where('id', 8)->orderByDesc('id')->first();
        if (! $smtp) {
            $smtp = EmailSetting::where('status', '=', 0)->orderByDesc('id')->first() ?: EmailSetting::first();
        }
        if (! $smtp) {
            throw new \Exception('SMTP config not found');
        }
        // ✅ 2. Purge old mailer (VERY IMPORTANT for queues)
        Mail::purge();

        // ✅ 3. Set dynamic config
        $this->setDynamicMailConfig($smtp);

        $fromAddress_reply = $fromAddress;
        $fromName_reply = $fromName;
        $fromMailAddr = (! empty($smtp->mail_from_email)) ? $smtp->mail_from_email : $fromAddress;

        Mail::send($view, ['data' => $data], function ($msg) use ($to, $subject, $attachments, $fromName, $cc, $fromAddress_reply, $fromName_reply, $fromMailAddr) {

            $msg->to($to)
                ->subject($subject)
                ->from($fromMailAddr, $fromName);

            // ✅ CC
            if (! empty($fromAddress_reply)) {
                $cc[] = $fromAddress_reply;
            }
            if (! empty($cc)) {
                foreach ($cc as $ccAddress) {
                    $msg->cc($ccAddress);
                }
            }

            // ✅ BCC
            // if (!empty($fromAddress_reply)) {
            //     $msg->bcc($fromAddress_reply);
            // }

            // ✅ Reply-To
            if (! empty($fromAddress_reply)) {
                $msg->replyTo($fromAddress_reply, $fromName_reply);
            }

            // ✅ Attachment from URL (Queue Safe)
            // ✅ Dynamic Attachments
            if (count($attachments) > 0) {
                foreach ($attachments as $attachment) {
                    // dd($attachment['path']);
                    // $attachment = $this->downloadAttachment($attachment['path']);
                    $fileContent = file_get_contents($attachment['path']);
                    $attachmentData = $this->downloadAttachment($attachment['path']);
                    if ($attachmentData) {
                        if (! empty($attachmentData['filename'])) {
                            $filename = $attachment['filename'] ?? $attachmentData['filename'];
                            $msg->attachData(
                                $fileContent,
                                $filename, // auto file name
                                [
                                    'mime' => 'application/pdf',
                                ]
                            );
                        }
                    }

                }
            }

        });
        $dailyCount = $smtp->dailyCount + 1;
        DB::table('smtp_settings')->where('id', '=', $smtp->id)->update(['dailyCount' => $dailyCount]);
        $created_at = now();
        $inserted = DB::table('email_count_companies')->insertGetId(['to_email' => $to, 'subject' => $subject, 'mailBody' => json_encode($data), 'emailType' => 1, 'company_id' => $company_id, 'created_at' => $created_at, 'updated_at' => $created_at]);
        $elasticService = app(ElasticService::class);
        $response = $elasticService->getClient()->update([
            'index' => ELASTIC_EMAIL_COUNT,
            'id' => $inserted,
            'retry_on_conflict' => 5,
            'body' => [
                'doc' => [
                    'id' => $inserted,
                    'to_email' => $to,
                    'subject' => $subject,
                    'mailBody' => json_encode($data),
                    'emailType' => 1,
                    'company_id' => $company_id,
                    'created_at' => ($created_at) ? Carbon::parse($created_at)->toIso8601String() : null,
                    'updated_at' => ($created_at) ? Carbon::parse($created_at)->toIso8601String() : null,
                ],
                'doc_as_upsert' => true,
                'detect_noop' => false,
            ],
        ]);

        return true;

        // Old code

        $mail = new PHPMailer(true);
        $fromAddress_reply = $fromAddress;
        $fromName_reply = $fromName;
        // smtp.elasticemail.com	Vibhanshu@4bell.com	F35C64FB9C2B8BE5DEBC3B13DF05198B5947
        $smtp = EmailSetting::where('status', '=', 0)->orderByDesc('id')->first();
        if ($smtp && ! empty($smtp->mail_from_email)) {
            $fromAddress = $smtp->mail_from_email;
        }

        // dd($fromAddress_reply,$fromAddress);

        try {
            // Server settings

            $mail->SMTPDebug = 0;
            $mail->isSMTP();
            $mail->Host = (@$smtp->notification_mail_host) ? $smtp->notification_mail_host : 'smtp-relay.brevo.com';
            $mail->SMTPAuth = true;
            $mail->Username = (@$smtp->email_id) ? $smtp->email_id : '619eeb001@smtp-brevo.com';
            $mail->Password = (@$smtp->notification_mail_password) ? $smtp->notification_mail_password : 'rqFS19y8VbxXRZLs';
            $mail->SMTPSecure = (@$smtp->notification_mail_encryption) ? $smtp->notification_mail_encryption : 'ssl';
            $mail->Port = (@$smtp->notification_mail_port) ? $smtp->notification_mail_port : 465;

            // Recipients
            $mail->addBCC($fromAddress_reply);
            $mail->setFrom($fromAddress, $fromName);
            $mail->addAddress($to);
            if (count($cc) > 0) {
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
            if (count($attachments) > 0) {
                foreach ($attachments as $attachment) {
                    // dd($attachment['path']);
                    // $attachment = $this->downloadAttachment($attachment['path']);
                    $attachmentData = $this->downloadAttachment($attachment['path']);
                    if ($attachmentData) {
                        if (! empty($attachmentData['filename'])) {
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
            $dataical = (array) $data;
            if (isset($dataical['interviewLink']) && ! empty($dataical['interviewLink']) &&
            isset($dataical['eventTitle']) && ! empty($dataical['eventTitle']) &&
            isset($dataical['eventDescription']) && ! empty($dataical['eventDescription']) &&
            isset($dataical['eventStartTime']) && ! empty($dataical['eventStartTime']) &&
            isset($dataical['eventEndTime']) && ! empty($dataical['eventEndTime'])) {

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
                $mail->addStringAttachment($ical, 'interview.ics', 'base64', 'text/calendar; charset=utf-8; method=REQUEST');

            }

            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            // $mail->Body    = $body;
            $mail->Body = view($view, ['data' => $data])->render();
            $mail->send();
            $dailyCount = $smtp->dailyCount + 1;
            DB::table('smtp_settings')->where('id', '=', $smtp->id)->update(['dailyCount' => $dailyCount]);

            return true;

        } catch (Exception $e) {
            return "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }

    private function setDynamicMailConfig($smtp, $fromName)
    {
        config([
            'mail.default' => 'smtp',
            'mail.mailers.smtp.transport' => 'smtp',
            'mail.mailers.smtp.host' => $smtp->notification_mail_host,
            'mail.mailers.smtp.port' => $smtp->notification_mail_port,
            'mail.mailers.smtp.username' => $smtp->notification_mail_username,
            'mail.mailers.smtp.password' => $smtp->notification_mail_password,
            'mail.mailers.smtp.encryption' => $smtp->notification_mail_encryption,
            'mail.from.address' => ! empty($smtp->mail_from_email) ? $smtp->mail_from_email : config('mail.from.address'),
            'mail.from.name' => $fromName,
        ]);
    }

    private function downloadAttachment($url)
    {
        try {
            $response = Http::get($url);
            $filename = basename($url);
            $attachment = [
                'filename' => ($filename) ? $filename : '',
                'data' => $response->body(),
            ];

            return $attachment;
        } catch (\Exception $e) {
            return null;
        }
    }
}
