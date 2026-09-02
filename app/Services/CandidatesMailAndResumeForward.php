<?php

namespace App\Services;

use App\Models\EmailSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class CandidatesMailAndResumeForward
{
    public function sendEmail($to, $subject, $view, $data, $attachments, $fromAddress, $fromName, $cc = [], $company_id = '')
    {
        /* Intent: Job-level idempotency guard & worker lock to prevent duplicate SMTP dispatches on queue worker retries or concurrent worker execution */
        $toHash = md5(strtolower(trim((string) $to)));
        $subjectHash = md5(trim((string) $subject));
        $jobSentKey = "candidate_mail_sent:{$company_id}:{$toHash}:{$subjectHash}";
        $workerLockKey = "mail_service_lock:{$company_id}:{$toHash}:{$subjectHash}";

        if (\Cache::has($jobSentKey)) {
            \Log::info('CandidatesMailAndResumeForward skipped duplicate SMTP dispatch via cache key', [
                'to' => $to,
                'subject' => $subject,
                'company_id' => $company_id,
            ]);

            return true;
        }

        $recentLog = DB::table('email_count_companies')
            ->where('to_email', $to)
            ->where('subject', $subject)
            ->where('emailType', 2)
            ->where('company_id', $company_id)
            ->where('created_at', '>=', now()->subMinutes(5))
            ->first();

        if ($recentLog) {
            \Log::info('CandidatesMailAndResumeForward skipped duplicate SMTP dispatch', [
                'to' => $to,
                'subject' => $subject,
                'company_id' => $company_id,
                'existing_id' => $recentLog->id,
            ]);

            return true;
        }

        $lock = \Cache::lock($workerLockKey, 30);
        if (! $lock->get()) {
            \Log::info('CandidatesMailAndResumeForward blocked concurrent worker dispatch', [
                'to' => $to,
                'subject' => $subject,
                'company_id' => $company_id,
            ]);

            return true;
        }

        try {
            // ✅ 1. Get SMTP Config
            $smtp = EmailSetting::where('status', '=', 0)->where('id', 9)->orderByDesc('id')->first();
            if (! $smtp) {
                $smtp = EmailSetting::where('status', '=', 0)->orderByDesc('id')->first() ?: EmailSetting::first();
            }
            if (! $smtp) {
                throw new \Exception('SMTP config not found');
            }
            // ✅ 2. Purge old mailer (VERY IMPORTANT for queues)
            Mail::purge();

            // ✅ 3. Set dynamic config
            $this->setDynamicMailConfig($smtp, $fromName);

            $fromAddress_reply = $fromAddress;
            $fromName_reply = $fromName;
            $fromMailAddr = (! empty($smtp->mail_from_email)) ? $smtp->mail_from_email : $fromAddress;

            Mail::send($view, ['data' => $data], function ($msg) use ($to, $subject, $attachments, $fromName, $cc, $fromAddress_reply, $fromName_reply, $fromMailAddr) {

                $msg->to($to)
                    ->subject($subject)
                    ->from($fromMailAddr, $fromName);

                // ✅ Clean & deduplicate CC array, excluding main candidate recipient ($to)
                $cleanCc = [];
                if (! empty($cc) && is_array($cc)) {
                    foreach ($cc as $ccAddr) {
                        $trimmedCc = strtolower(trim($ccAddr));
                        if (! empty($trimmedCc) && $trimmedCc !== strtolower(trim($to)) && filter_var($trimmedCc, FILTER_VALIDATE_EMAIL)) {
                            $cleanCc[] = $trimmedCc;
                        }
                    }
                }
                if (! empty($fromAddress_reply)) {
                    $trimmedReply = strtolower(trim($fromAddress_reply));
                    if ($trimmedReply !== strtolower(trim($to)) && filter_var($trimmedReply, FILTER_VALIDATE_EMAIL)) {
                        $cleanCc[] = $trimmedReply;
                    }
                }
                $cleanCc = array_values(array_unique($cleanCc));

                foreach ($cleanCc as $ccAddress) {
                    $msg->cc($ccAddress);
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
            $inserted = DB::table('email_count_companies')->insertGetId(['to_email' => $to, 'subject' => $subject, 'mailBody' => json_encode($data), 'emailType' => 2, 'company_id' => $company_id, 'created_at' => $created_at, 'updated_at' => $created_at]);
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
                        'emailType' => 2,
                        'company_id' => $company_id,
                        'created_at' => ($created_at) ? Carbon::parse($created_at)->toIso8601String() : null,
                        'updated_at' => ($created_at) ? Carbon::parse($created_at)->toIso8601String() : null,
                    ],
                    'doc_as_upsert' => true,
                    'detect_noop' => false,
                ],
            ]);

            \Cache::put($jobSentKey, true, 300);

            return true;
        } finally {
            $lock->release();
        }

    }

    private function setDynamicMailConfig($smtp, $fromName = '')
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
