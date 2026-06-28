<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Notifications\InterviewReminder;
use App\Jobs\WhatsAppTextSend;
use App\Models\User;
use App\Services\ElasticService;
use App\Jobs\QueueInterviewRemainderMailService;

class SendInterviewReminder extends Command
{
    protected $signature = 'interview:send-reminder';
    protected $description = 'Send interview reminders (1 hour and 15 minutes before)';

    protected $elasticService;

    public function __construct(ElasticService $elasticService)
    {
        parent::__construct();
        $this->elasticService = $elasticService;
    }

    private function baseUrl()
    {
        return config('services.evolution.url');
    }

    private function getInstance($id)
    {
        return DB::table('whatsapp_instances')
            ->where('user_id', $id)
            ->first();
    }

    private function formatWhatsappMessage($html)
    {
        if (!$html) return '';

        $html = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $html = str_replace(["\r\n", "\r"], "\n", $html);
        $html = preg_replace('/<br\s*\/?>/i', "\n", $html);
        $html = preg_replace('/<\/p>/i', "\n", $html);

        $html = preg_replace('/<\s*strong\s*>(.*?)<\s*\/\s*strong\s*>/is', '*$1*', $html);
        $html = preg_replace('/<\s*b\s*>(.*?)<\s*\/\s*b\s*>/is', '*$1*', $html);

        $html = preg_replace('/<\s*em\s*>(.*?)<\s*\/\s*em\s*>/is', '_$1_', $html);
        $html = preg_replace('/<\s*i\s*>(.*?)<\s*\/\s*i\s*>/is', '_$1_', $html);

        $html = strip_tags($html);
        $html = preg_replace('/\n+/', "\n", $html);

        return trim($html);
    }

    public function handle()
    {

        $lockKey = 'lock:interview:send-reminder';
        $lockTTL = 55;

        $lock = Redis::set($lockKey, now()->timestamp, 'EX', $lockTTL, 'NX');

        if (!$lock) {
            Log::warning("🔒 Redis Lock Active — Skipping execution.");
            return Command::SUCCESS;
        }

        Log::info("🚀 Interview reminder command started");

        $now = Carbon::now();

        $start15 = $now->copy()->addMinutes(15)->startOfMinute();
        $end15   = $start15->copy()->addMinute();

        $start60 = $now->copy()->addHour()->startOfMinute();
        $end60   = $start60->copy()->addMinute();

        Log::info("⏳ 15min window: {$start15} → {$end15}");
        Log::info("⏳ 60min window: {$start60} → {$end60}");

        $interviews = DB::table('interview_schedules')
            ->select(
                'id',
                'schedule_date',
                'timezone_abbreviation',
                'interview_job_wise_con_id',
                'interview_mode',
                'url_link',
                'schedule_to_time',
                'reminder_15_sent',
                'reminder_60_sent'
            )
            ->whereNull('available_status')
            ->where(function ($q) use ($start15, $end15, $start60, $end60) {

                $q->where(function ($qq) use ($start15, $end15) {
                    $qq->whereBetween('schedule_date', [$start15, $end15])
                       ->where('reminder_15_sent', 0);
                })
                ->orWhere(function ($qq) use ($start60, $end60) {
                    $qq->whereBetween('schedule_date', [$start60, $end60])
                       ->where('reminder_60_sent', 0);
                });

            })
            ->get();

        Log::info("📊 Interviews found: " . $interviews->count());

        if ($interviews->isEmpty()) {
            return Command::SUCCESS;
        }

        foreach ($interviews as $interview) {

            try {

                $params = [
                    'index' => ELASTIC_CANDIDATES,
                    'id'    => $interview->interview_job_wise_con_id,
                ];

                $response = $this->elasticService->getClient()->get($params);
                $candidateData = (object) $response['_source'];

            } catch (\Exception $e) {

                Log::error("Elastic error: " . $e->getMessage());
                continue;
            }

            $jobAssignUsers = DB::table('job_assign')
                ->where('job_id', $candidateData->jobId)
                ->whereNotNull('spok_user_id')
                ->pluck('spok_user_id')
                ->toArray();

            if (!empty($candidateData->submitted_by) &&
                !in_array($candidateData->submitted_by, $jobAssignUsers)) {

                $jobAssignUsers[] = $candidateData->submitted_by;
            }

            if (empty($jobAssignUsers)) {
                continue;
            }

            $users = User::whereIn('id', $jobAssignUsers)->get()->keyBy('id');
            $usersMain = User::where('id', $candidateData->submitted_by)->first();

            $phone = preg_replace('/[^0-9]/', '', $candidateData->phone);

            if (strlen($phone) == 10) {
                $phone = '91' . $phone;
            }

            $InterviewMode = '';

            if ($interview->interview_mode == 1) $InterviewMode = 'Telephonic';
            if ($interview->interview_mode == 2) $InterviewMode = 'Video';
            if ($interview->interview_mode == 3) $InterviewMode = 'F2F';
            $heading =  '';
            if($interview->interview_mode == 3){
                $heading =  'Address:';
            }

            $dateTime = date('d-M-Y', strtotime($interview->schedule_date))
                .' '.$interview->schedule_to_time
                .' ('.$interview->timezone_abbreviation.')';

            $videoUrl = $heading.'- '.$interview->url_link;

            $payload = (object)[
                'id' => $interview->id,
                'interview_time' => Carbon::parse($interview->schedule_date)
                    ->format('d M y h:i A').' ('.$interview->timezone_abbreviation.')',
                'candidate_name' => $candidateData->full_name ?? '',
                'job_title' => ucfirst($candidateData->title ?? ''),
                'company_name' => $candidateData->company_name ?? ''
            ];

            $templateId = null;
            $updateColumn = null;
            $emailTemplateId = null;
             if($interview->interview_mode==3){
                if ($interview->schedule_date >= $start60 && $interview->schedule_date <= $end60 && $interview->reminder_60_sent == 0) {
                    $templateId = 38;
                    $emailTemplateId = 56;
                    $updateColumn = 'reminder_60_sent';
                } elseif ($interview->schedule_date >= $start15 && $interview->schedule_date <= $end15 && $interview->reminder_15_sent == 0) {
                    $templateId = 37;
                    $emailTemplateId = 55;
                    $updateColumn = 'reminder_15_sent';
                }
                
             }elseif($interview->interview_mode==1){

                if ($interview->schedule_date >= $start60 && $interview->schedule_date <= $end60 && $interview->reminder_60_sent == 0) {
                    $templateId = 54;
                    $emailTemplateId = 58;
                    $updateColumn = 'reminder_60_sent';
                } elseif ($interview->schedule_date >= $start15 && $interview->schedule_date <= $end15 && $interview->reminder_15_sent == 0) {
                    $templateId = 53;
                    $emailTemplateId = 57;
                    $updateColumn = 'reminder_15_sent';
                }
                
             }else{
                if ($interview->schedule_date >= $start60 && $interview->schedule_date <= $end60 && $interview->reminder_60_sent == 0) {
                    $templateId = 28;
                    $emailTemplateId = 39;
                    $updateColumn = 'reminder_60_sent';
                } elseif ($interview->schedule_date >= $start15 && $interview->schedule_date <= $end15 && $interview->reminder_15_sent == 0) {
                    $templateId = 29;
                    $emailTemplateId = 40;
                    $updateColumn = 'reminder_15_sent';
                }

             }
            

            foreach ($jobAssignUsers as $uid) {

                if (isset($users[$uid])) {
                    if ($templateId) {
                        if ($interview->schedule_date >= $start60 && $interview->schedule_date <= $end60 && $interview->reminder_60_sent == 0) {
                                $users[$uid]->notify(new InterviewReminder($payload));
                        } elseif ($interview->schedule_date >= $start15 && $interview->schedule_date <= $end15 && $interview->reminder_15_sent == 0) {
                                $users[$uid]->notify(new InterviewReminder($payload));
                        }
                    }

                }
            }

            

            if ($templateId) {

                $instance = $this->getInstance($candidateData->submitted_by);

                if ($instance) {

                    $dataRow = DB::table('th_email_template')
                        ->where('e_temp_id', $templateId)
                        ->first();
                    $dataRowEmail = DB::table('th_email_template')
                        ->where('e_temp_id', $emailTemplateId)
                        ->first();

                    if ($dataRow) {

                        $message = str_replace(
                            ["[{CANDIDATE_NAME}]", "[{INTERVIEW_MODE}]", "[{INTERVIEW_DATE_TIME}]", "[{INTERVIEW_LINK_URL}]"],
                            [$candidateData->full_name, $InterviewMode, $dateTime, $videoUrl],
                            $dataRow->template_description
                        );

                        $message = $this->formatWhatsappMessage($message);

                        $template_description = str_replace(
                            ["[{CANDIDATE_NAME}]", "[{INTERVIEW_MODE}]", "[{INTERVIEW_DATE_TIME}]", "[{INTERVIEW_LINK_URL}]"],
                            [$candidateData->full_name, $InterviewMode, $dateTime, $videoUrl],
                            $dataRowEmail->template_description
                        );

                       $data = [
                            'message' => $template_description
                        ];
                       $type = 2;
                        WhatsAppTextSend::dispatch(
                            $phone,
                            $message,
                            $instance->instance_name,
                            $instance->token,
                            $this->baseUrl(),
                            $candidateData->company_id,
                            $type
                        )->onConnection('emails');

                        DB::table('interview_schedules')
                            ->where('id', $interview->id)
                            ->update([$updateColumn => 1]);
                            
                        $cc = [];
                        $attachments = [];
                        QueueInterviewRemainderMailService::dispatch(
							$candidateData->email,
							$dataRowEmail->template_subject,
							'email.common.index',
							(object) $data,
							$attachments,
							$usersMain->email,
							$usersMain->name,
							$cc,
                            $candidateData->company_id
						)->onConnection('emails');
                    }
                }
            }

            Log::info("✅ Interview {$interview->id} reminder processed");
        }

        Log::info("🏁 Interview reminder command finished");

        return Command::SUCCESS;
    }
}