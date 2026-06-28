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

class CandidatesMailAndResumeForward
{
    public function sendEmail($to, $subject, $view, $data, $attachments, $fromAddress, $fromName, $cc = [],$company_id='')
    {

            // ✅ 1. Get SMTP Config
             $smtp = EmailSetting::where('status','=',0)->where('id', 9)->orderByDesc('id')->first();
            if (!$smtp) {
                throw new \Exception('SMTP config not found');
            }
            // ✅ 2. Purge old mailer (VERY IMPORTANT for queues)
            Mail::purge();

            // ✅ 3. Set dynamic config
            $this->setDynamicMailConfig($smtp,$fromName);

            $fromAddress_reply = $fromAddress;
            $fromName_reply    = $fromName;

            Mail::send($view, ['data' => $data], function ($msg) use ($to, $subject,$attachments, $fromName, $cc,$fromAddress_reply,$fromName_reply,$smtp) {

                $msg->to($to)
                    ->subject($subject)
                    ->from($smtp->email_id, $fromName); // 🔥 KEEP FIXED

                // ✅ CC
                if (!empty($fromAddress_reply)) {
                    $cc[] = $fromAddress_reply;
                }
                if (!empty($cc)) {
                    foreach ($cc as $ccAddress) {
                        $msg->cc($ccAddress);
                    }
                }

                // ✅ BCC
                // if (!empty($fromAddress_reply)) {
                //     $msg->bcc($fromAddress_reply);
                // }

                // ✅ Reply-To
                if (!empty($fromAddress_reply)) {
                    $msg->replyTo($fromAddress_reply, $fromName_reply);
                }
                
                // ✅ Attachment from URL (Queue Safe)
                // ✅ Dynamic Attachments
                if(count($attachments) > 0){
                    foreach ($attachments as $attachment) {
                        // dd($attachment['path']);
                        // $attachment = $this->downloadAttachment($attachment['path']);
                        $fileContent = file_get_contents($attachment['path']);
                        $attachmentData = $this->downloadAttachment($attachment['path']);
                        if ($attachmentData) {
                            if(!empty($attachmentData['filename'])){
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
             $dailyCount = $smtp->dailyCount+1;
             DB::table('smtp_settings')->where('id','=',$smtp->id)->update(['dailyCount'=>$dailyCount]);
            $created_at = now();
            $inserted = DB::table('email_count_companies')->insertGetId(['to_email'=>$to,'subject'=>$subject,'mailBody'=>json_encode($data),'emailType'=>2,'company_id'=>$company_id,'created_at'=>$created_at,'updated_at'=>$created_at]);
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
								'emailType' => 2,
								'company_id' => $company_id,
								'created_at' => ($created_at) ? Carbon::parse($created_at)->toIso8601String() : null,
								'updated_at' => ($created_at) ? Carbon::parse($created_at)->toIso8601String() : null
							],
							'doc_as_upsert' => true,
							'detect_noop' => false
						],
                    ]);

            return true;
       
    }
    
    private function setDynamicMailConfig($smtp,$fromName)
    {
        config([
            'mail.default' => 'smtp',
            'mail.mailers.smtp.transport' => 'smtp',
            'mail.mailers.smtp.host' => $smtp->notification_mail_host,
            'mail.mailers.smtp.port' => $smtp->notification_mail_port,
            'mail.mailers.smtp.username' => $smtp->notification_mail_username,
            'mail.mailers.smtp.password' => $smtp->notification_mail_password,
            'mail.mailers.smtp.encryption' => $smtp->notification_mail_encryption,
            'mail.from.address' => $smtp->email_id,
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

        


?>