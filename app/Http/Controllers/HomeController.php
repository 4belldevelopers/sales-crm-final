<?php

namespace App\Http\Controllers;

use App\Helper\GeneralMethod;
use App\Helper\Reply;
use App\Imports\MeetingImport;
use App\Jobs\QueueMailService;
use App\Jobs\QueueNotificationMailService;
use App\Jobs\WhatsAppTextSend;
use App\Mail\InterviewScheduleMail;
use App\Models\AddJobWiseCondidate;
use App\Models\Client;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\OfferJobApplication;
use App\Services\ElasticService;
use App\Services\MailService;
use Carbon\Carbon;
use DB;
use Google\Client as GoogleClient;
use Google\Service\Calendar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class HomeController extends Controller
{
    protected $mailService;

    protected $elasticService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(MailService $mailService, ElasticService $elasticService)
    {
        parent::__construct();
        // $this->middleware('auth');
        $this->mailService = $mailService;
        $this->elasticService = $elasticService;

    }

    public function salaryCalculate(Request $request)
    {
        $hoursPerMonth = 160;
        $monthsPerYear = 12;

        $currencyConverter = GeneralMethod::currencyConverter();
        $rates = $currencyConverter['usd_based_rates'] ?? [
            'USD' => 1,
            'INR' => 83,
            'EUR' => 0.87,
            'JPY' => 159.65,
            'GBP' => 0.76,
            'AED' => 3.67,
        ];

        $amount = (float) $request->amount;
        $type = $request->type;
        $currency = trim($request->currency);

        /*
        |--------------------------------------------------------------------------
        | Convert To Hourly
        |--------------------------------------------------------------------------
        */

        if ($type == 'hourly') {

            $hourly = $amount;

        } elseif ($type == 'monthly') {

            $hourly = $amount / $hoursPerMonth;

        } elseif ($type == 'annual') {

            $monthly = $amount / $monthsPerYear;
            $hourly = $monthly / $hoursPerMonth;

        } else {

            $hourly = 0;
        }

        /*
        |--------------------------------------------------------------------------
        | Final Values
        |--------------------------------------------------------------------------
        */

        $monthly = $hourly * $hoursPerMonth;
        $annual = $monthly * $monthsPerYear;

        /*
        |--------------------------------------------------------------------------
        | Convert Current Currency To USD
        |--------------------------------------------------------------------------
        */

        if ($currency == 'USD') {

            $usdHourly = $hourly;

        } else {

            $usdHourly = $hourly / $rates[$currency];
        }

        /*
        |--------------------------------------------------------------------------
        | Convert USD To INR
        |--------------------------------------------------------------------------
        */

        $inrHourly = $usdHourly * $rates['INR'];

        /*
        |--------------------------------------------------------------------------
        | Monthly / Annual
        |--------------------------------------------------------------------------
        */

        $usdMonthly = $usdHourly * $hoursPerMonth;
        $usdAnnual = $usdMonthly * $monthsPerYear;

        $inrMonthly = $inrHourly * $hoursPerMonth;
        $inrAnnual = $inrMonthly * $monthsPerYear;

        /*
        |--------------------------------------------------------------------------
        | Response
        |--------------------------------------------------------------------------
        */

        $response = [

            'hourly' => number_format($hourly, 2, '.', ''),
            'monthly' => number_format($monthly, 2, '.', ''),
            'annual' => number_format($annual, 2, '.', ''),

            'usd_hourly' => '',
            'usd_monthly' => '',
            'usd_annual' => '',

            'inr_hourly' => '',
            'inr_monthly' => '',
            'inr_annual' => '',
        ];

        /*
        |--------------------------------------------------------------------------
        | INR Selected
        |--------------------------------------------------------------------------
        */

        if ($currency == 'INR') {

            $response['usd_hourly'] = number_format($usdHourly, 2, '.', '');
            $response['usd_monthly'] = number_format($usdMonthly, 2, '.', '');
            $response['usd_annual'] = number_format($usdAnnual, 2, '.', '');
        }

        /*
        |--------------------------------------------------------------------------
        | USD Selected
        |--------------------------------------------------------------------------
        */

        elseif ($currency == 'USD') {

            $response['inr_hourly'] = number_format($inrHourly, 2, '.', '');
            $response['inr_monthly'] = number_format($inrMonthly, 2, '.', '');
            $response['inr_annual'] = number_format($inrAnnual, 2, '.', '');
        }

        /*
        |--------------------------------------------------------------------------
        | Other Currency
        |--------------------------------------------------------------------------
        */

        else {

            $response['usd_hourly'] = number_format($usdHourly, 2, '.', '');
            $response['usd_monthly'] = number_format($usdMonthly, 2, '.', '');
            $response['usd_annual'] = number_format($usdAnnual, 2, '.', '');

            $response['inr_hourly'] = number_format($inrHourly, 2, '.', '');
            $response['inr_monthly'] = number_format($inrMonthly, 2, '.', '');
            $response['inr_annual'] = number_format($inrAnnual, 2, '.', '');
        }

        // dd(response()->json($response));

        return response()->json($response);
    }

    private function baseUrl()
    {
        return config('services.evolution.url');
    }

    private function getInstance($userid)
    {
        return DB::table('whatsapp_instances')
            ->where('user_id', $userid)
            ->first();
    }

    // 1️⃣ Create Instance
    public function create(Request $request)
    {

        // $userId = auth()->user()->id;
        $userId = (int) $request->userid;
        $user = tenant_table('users')->select('company_id', 'name')->where('id', $userId)->first();

        $companyId = $user->company_id;
        $username = Str::slug($user->name, '_'); // clean name

        $instanceName = config('services.evolution.instance_pre').$companyId.'_'.$username.'_'.$userId;

        // $instanceName = config('services.evolution.instance_pre').$userId;

        // dd($instanceName);

        $response = Http::withHeaders([
            'apikey' => config('services.evolution.global_key'),
        ])->get(
            $this->baseUrl().'/instance/connectionState/'.$instanceName
        );
        $data = $response->json();

        if (($data['status'] ?? null) == 404) {
            $token = Str::random(40);

            DB::table('whatsapp_instances')->updateOrInsert(
                ['user_id' => $userId],
                [
                    'company_id' => $companyId,
                    'instance_name' => $instanceName,
                    'token' => $token,
                    'status' => 'creating',
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );

            $response = Http::withHeaders([
                'apikey' => config('services.evolution.global_key'),
            ])->post(
                $this->baseUrl().'/instance/create',
                [
                    'instanceName' => $instanceName,
                    'token' => $token,
                    'qrcode' => true,
                    'integration' => 'WHATSAPP-BAILEYS',
                ]);

            return response()->json(['success' => true]);

        } else {

            return response()->json(['success' => true]);
        }

    }

    // 2️⃣ Get QR
    public function qr(Request $request)
    {
        $userid = (int) $request->userid;
        $instance = $this->getInstance($userid);

        if (! $instance) {
            return response()->json(['qr' => null, 'error' => 'Instance not found'], 200);
        }

        $response = Http::withHeaders([
            'apikey' => config('services.evolution.global_key'),
        ])->get(
            $this->baseUrl().'/instance/connect/'.$instance->instance_name
        );

        $data = $response->json();

        return response()->json([
            'qr' => $data['base64'] ?? null,
        ]);
    }

    // 3️⃣ Check Status
    public function status(Request $request)
    {
        $userid = (int) $request->userid;
        $instance = $this->getInstance($userid);

        if (! $instance) {
            return response()->json(['instance' => null, 'error' => 'Instance not found'], 200);
        }

        $response = Http::withHeaders([
            'apikey' => config('services.evolution.global_key'),
        ])->get(
            $this->baseUrl().'/instance/connectionState/'.@$instance->instance_name
        );

        $data = $response->json();

        $responseConnected = Http::withHeaders([
            'apikey' => config('services.evolution.global_key'),
        ])->get(
            $this->baseUrl().'/instance/fetchInstances'
        );
        $dataConnected = $responseConnected->json();

        $connectedNumber = collect($dataConnected)
            ->firstWhere('name', @$instance->instance_name)['ownerJid'] ?? null;

        if ($connectedNumber) {
            $connectedNumber = explode('@', $connectedNumber)[0];
        }

        $data['instance']['connectedNumber'] = $connectedNumber;

        if (isset($data['instance']['state'])) {
            DB::table('whatsapp_instances')
                ->where('user_id', auth()->id())
                ->update([
                    'status' => 1,
                    'updated_at' => now(),
                ]);
        }

        return $data;
    }

    // 4️⃣ Restart
    public function restart(Request $request)
    {
        $userid = (int) $request->userid;
        $instance = $this->getInstance($userid);

        Http::withHeaders([
            'apikey' => config('services.evolution.global_key'),
        ])->post(
            $this->baseUrl().'/instance/restart/'.$instance->instance_name
        );

        return response()->json(['success' => true]);
    }

    public function delete(Request $request)
    {
        $userid = (int) $request->userid;
        $instance = $this->getInstance($userid);

        $response = Http::withHeaders([
            'apikey' => config('services.evolution.global_key'),
        ])->delete(
            $this->baseUrl().'/instance/delete/'.$instance->instance_name
        );

        $data = $response->json();

        // dd($data);

        if (! $instance) {
            return response()->json(['error' => 'Instance not found'], 404);
        }

        $data = $response->json();
        //    dd($data);

        if ($response->successful()) {

            DB::table('whatsapp_instances')
                ->where('user_id', $userid)
                ->delete();
        }

        $response = Http::withHeaders([
            'apikey' => config('services.evolution.global_key'),
        ])->get(
            $this->baseUrl().'/instance/connectionState/'.$instance->instance_name
        );
        $data = $response->json();

        $this->classname = '';
        $this->textvalue = '';
        $this->connectBtn = '';
        $this->connectedNumberGet = '';

        $this->classname = 'btn-primary';
        $this->textvalue = 'Click to Connect';
        $this->connectBtn = 'connectBtn';
        $this->connectedNumberGet = '';

        return response()->json([
            'status' => $response->status(),
            'body' => $response->json(),
            'classname' => $this->classname,
            'textvalue' => $this->textvalue,
            'connectBtn' => $this->connectBtn,
            'connectedNumberGet' => $this->connectedNumberGet,
        ]);
    }

    // 5️⃣ Send Message
    public function send(Request $request)
    {
        $request->validate([
            'phone' => 'required',
            'message' => 'required',
        ]);

        $eventId = 45;
        $coinsRequ = 1;
        $companyId = (int) auth()->user()->company_id;
        $coinsDeductions = GeneralMethod::coinsValidationsAllcompany($companyId, $eventId, $coinsRequ);
        if ($coinsDeductions->status) {
            return Reply::dataOnly(['status' => 'coinCheck', 'message' => $coinsDeductions->message, 'company_coins' => $coinsDeductions->company_coins]);
        }

        $userid = (int) $request->userid;
        $instance = $this->getInstance($userid);

        if (! $instance) {
            return response()->json(['error' => 'Instance not found'], 404);
        }

        // 🔹 Clean phone number
        $phone = preg_replace('/[^0-9]/', '', $request->phone);

        // 🔹 If 10 digit Indian number, auto add 91
        if (strlen($phone) == 10) {
            $phone = '91'.$phone;
        }

        // 🔹 Final validation: must start with 91 and be 12 digits
        if (! preg_match('/^91[0-9]{10}$/', $phone)) {
            return response()->json([
                'error' => 'Invalid Indian phone number format',
            ], 422);
        }

        $response = Http::withHeaders([
            'apikey' => $instance->token,
        ])->post(
            $this->baseUrl().'/chat/whatsappNumbers/'.@$instance->instance_name,
            [
                'numbers' => [$phone],
            ]
        );
        if (! $response->successful()) {
            $this->exists_number = false;
            $exists = false;

        } else {
            $data = $response->json();
            $exists = $data[0]['exists'] ?? false;
        }
        if (! $exists) {
            return response()->json(['status' => 'error', 'message' => 'This number ('.$phone.') is not available on WhatsApp']);
        }
        $templatewhatsapp = $request->templatewhatsapp;
        $th_email_template = DB::table('th_email_template')->select('template_subject')->where('e_temp_id', '=', $templatewhatsapp)->first();
        $comment = '';
        if ($th_email_template) {
            $comment = $th_email_template->template_subject;
        }
        $type = 1;
        $message = $this->formatWhatsappMessage($request->message);
        WhatsAppTextSend::dispatch($phone, $message, $instance->instance_name, $instance->token, $this->baseUrl(), $companyId, $type)->onConnection('emails');
        $coinsDeductions = GeneralMethod::coinsDeductions((int) $companyId, $eventId, $userid, $companyId, '', '');
        $timelineArr = [
            'event_id' => $eventId,
            'event_by_company_id' => $companyId,
            'event_by_user_id' => $userid,
            'master_process_name' => 'Whatsapp Event',
            // 'child_process_name'	=>

            'application_id' => $request->application_id,
            'application_email' => $request->application_email,
            'job_id' => $request->job_id,
            'comment' => $comment,
            'note' => 'Send Whatsapp',
        ];
        GeneralMethod::UpdateTimeline($timelineArr);

        return response()->json([
            'status' => 'success',
            'message' => 'Whatsapp message has been sent',
        ]);

        // $response = Http::withHeaders([
        //     'apikey' => $instance->token
        // ])->post(
        //     $this->baseUrl()."/message/sendText/".$instance->instance_name,
        // [
        //     "number" => $phone,
        //     "text"   => $message
        // ]);

        // dd($response->json());

        // return $response->json();
    }

    private function formatWhatsappMessage($html)
    {
        if (! $html) {
            return '';
        }

        // Decode entities first
        $html = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // Normalize line breaks
        $html = str_replace(["\r\n", "\r"], "\n", $html);

        // Convert <br> and </p> to new line
        $html = preg_replace('/<br\s*\/?>/i', "\n", $html);
        $html = preg_replace('/<\/p>/i', "\n", $html);

        // Convert bold tags to WhatsApp format
        $html = preg_replace('/<\s*strong\s*>(.*?)<\s*\/\s*strong\s*>/is', '*$1*', $html);
        $html = preg_replace('/<\s*b\s*>(.*?)<\s*\/\s*b\s*>/is', '*$1*', $html);

        // Convert italic
        $html = preg_replace('/<\s*em\s*>(.*?)<\s*\/\s*em\s*>/is', '_$1_', $html);
        $html = preg_replace('/<\s*i\s*>(.*?)<\s*\/\s*i\s*>/is', '_$1_', $html);

        // Remove remaining HTML
        $html = strip_tags($html);

        // Remove extra spaces
        $html = preg_replace('/\n+/', "\n", $html);
        $html = trim($html);

        return $html;
    }

    public function questionsStore(Request $request)
    {

        if ($request->deleteChk == 'Yes') {

            // TENANCY NOTE: Guard delete query with company_id to prevent cross-tenant deletion
            $query = DB::table('interview_questions')->where('id', (int) $request->questionsId);
            if (auth()->check() && ! auth()->user()->is_superadmin && auth()->user()->company_id) {
                $query->where('company_id', auth()->user()->company_id);
            }
            $deleted = $query->delete();

            $cachekeys = [
                'jobDetails_version_user_'.auth()->user()->id,
            ];
            foreach ($cachekeys as $key) {
                GeneralMethod::cacheClean($key);
            }

            if (! $deleted) {
                return response()->json([
                    'status' => false,
                    'message' => 'Question not found',
                    'html' => '',
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'Question deleted successfully',
                'html' => '',
            ]);

        }
        //  dd($request->all());
        $request->validate([
            'questions' => 'required',
            'job_id' => 'required',
        ]);

        DB::beginTransaction();

        try {
            //  DB::table('interview_questions')
            //     ->where('job_id', (int) $request->job_id)
            //     ->delete();
            $company_id = auth()->user()->company_id;
            if ($request->questionsId > 0) {
                // dd($request->questionsId);
                $data = [
                    'questions' => $request->questions,
                    'job_id' => (int) $request->job_id,
                    'company_id' => $company_id,
                    'user_id' => auth()->user()->id,
                    'updated_at' => now(),
                ];

                DB::table('interview_questions')
                    ->where('id', (int) $request->questionsId)
                    ->update($data);

            } else {
                $data = [
                    'questions' => $request->question,
                    'job_id' => (int) $request->job_id,
                    'company_id' => $company_id,
                    'user_id' => auth()->user()->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                DB::table('interview_questions')->insert($data);
                $idGet = DB::getPdo()->lastInsertId();

            }
            $cachekeys = [
                'jobDetails_version_user_'.auth()->user()->id,
            ];
            foreach ($cachekeys as $key) {
                GeneralMethod::cacheClean($key);
            }
            DB::commit();

            $html = $request->questions;

            return response()->json([
                'status' => true,
                'message' => 'Questions saved successfully',
                'html' => $html,
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Failed to save questions',
                'html' => '',
            ], 500);
        }
    }

    public function generate()
    {
        // Generate 6 digit numeric captcha
        $captcha = random_int(1000, 9999);

        // Store in session (expire in 2 mins)
        Session::put('captcha_code', $captcha);
        Session::put('captcha_expires', now()->addMinutes(2));

        // Create image
        $image = imagecreate(160, 50);
        imagecolorallocate($image, 255, 255, 255);

        $textColor = imagecolorallocate($image, 30, 30, 30);

        imagettftext(
            $image,
            26,
            rand(-5, 5),
            35,
            38,
            $textColor,
            public_path('fonts/DejaVuSerif-Italic.ttf'),
            $captcha
        );

        // Noise
        for ($i = 0; $i < 80; $i++) {
            imagesetpixel($image, rand(0, 160), rand(0, 50), $textColor);
        }

        ob_start();
        imagepng($image);
        $imageData = ob_get_clean();
        imagedestroy($image);

        return response($imageData)->header('Content-Type', 'image/png');
    }

    public function generateChar()
    {
        // Generate 4 digit numeric captcha
        // Create 4-character mixed captcha
        $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'; // removed confusing chars
        $captcha = '';
        for ($i = 0; $i < 4; $i++) {
            $captcha .= $characters[random_int(0, strlen($characters) - 1)];
        }

        // Store hashed captcha (more secure)
        Session::put('captcha_code', hash('sha256', $captcha));
        Session::put('captcha_expires', now()->addMinutes(3));

        // Create image
        $image = imagecreate(180, 45);
        imagecolorallocate($image, 255, 255, 255);

        $textColor = imagecolorallocate($image, 20, 20, 20);

        // Draw characters separately with rotation
        $x = 20;
        for ($i = 0; $i < 4; $i++) {
            imagettftext(
                $image,
                28,
                rand(-15, 15),
                $x,
                40,
                $textColor,
                public_path('fonts/DejaVuSerif-Italic.ttf'),
                $captcha[$i]
            );
            $x += 25;
        }

        // Noise
        for ($i = 0; $i < 100; $i++) {
            imagesetpixel($image, rand(0, 180), rand(0, 55), $textColor);
        }

        for ($i = 0; $i < 4; $i++) {
            imageline(
                $image,
                rand(0, 180), rand(0, 55),
                rand(0, 180), rand(0, 55),
                $textColor
            );
        }

        ob_start();
        imagepng($image);
        $img = ob_get_clean();
        imagedestroy($image);

        return response($img)->header('Content-Type', 'image/png');

    }

    public function sendInterviewMail()
    {
        $start = '2025-11-27 19:00:00';
        $end = '2025-11-27 20:00:00';

        $event = [
            'title' => 'Software Developer Interview',
            'description' => 'Meecruit Interview',
            'start' => $start,
            'end' => $end,
            'date' => date('d M Y', strtotime($start)),
            'time' => date('h:i A', strtotime($start)),
            'location' => 'Google Meet',
            'google_link' => 'https://calendar.google.com/calendar/render?action=TEMPLATE'.
                            '&text=Software+Developer+Interview'.
                            '&dates='.gmdate('Ymd\THis\Z', strtotime($start)).'/'.
                                        gmdate('Ymd\THis\Z', strtotime($end)).
                            '&details=Meecruit+Interview'.
                            '&location=Google+Meet',
        ];

        Mail::to('rajveer@4bell.com')->send(new InterviewScheduleMail($event));

        return 'Mail Sent!';
    }

    public function downloadResume($id, $encodedPath)
    {
        // base64url decode (encoded with rtrim(strtr(base64_encode($url),'+/','-_'),'=') in blade views)
        $pad = strlen($encodedPath) % 4 ? 4 - strlen($encodedPath) % 4 : 0;
        $resumeUrl = base64_decode(strtr($encodedPath.str_repeat('=', $pad), '-_', '+/'));

        $s3Path = str_replace('https://file.jero.works/', '', $resumeUrl);

        // Derive candidate name from job_applications (fall back to file basename if not found)
        // TENANCY NOTE: Enforce company_id guard for authenticated tenant users to prevent cross-tenant IDOR
        $appQuery = DB::table('job_applications')->where('id', $id);
        if (auth()->check() && ! auth()->user()->is_superadmin && auth()->user()->company_id) {
            $appQuery->where('company_id', auth()->user()->company_id);
        }
        $job_application = $appQuery->first();
        $candidateName = $job_application
            ? 'JERO_'.str_replace(' ', '_', $job_application->full_name)
            : 'JERO_'.pathinfo($s3Path, PATHINFO_FILENAME);

        $ext = strtolower(pathinfo($s3Path, PATHINFO_EXTENSION));
        $customName = $candidateName.'.'.$ext;

        // If not found at stored path, try PDF fallback (covers records with corrupted .htm extension in DB)
        if (! Storage::disk('r2')->exists($s3Path)) {
            $fallbackPath = preg_replace('/\.[^.\/]+$/', '.pdf', $s3Path);
            if ($fallbackPath !== $s3Path && Storage::disk('r2')->exists($fallbackPath)) {
                $s3Path = $fallbackPath;
                $customName = $candidateName.'.pdf';
            } else {
                abort(404, 'Resume file not found. Please re-upload the resume.');
            }
        }

        return Storage::disk('r2')->download($s3Path, $customName);
    }

    public function benchCandidates(Request $request, $companyName, $companyId)
    {
        // dd($id);
        $company_id = (int) $companyId;
        $to_company_id = auth()->check() ? auth()->user()->company_id : 0;

        $salaryMode = GeneralMethod::salaryMode();
        $currencySymbole = GeneralMethod::currencySymbole();
        $valCheck = $request->input('sortingfilter');
        $country = '';
        $searchQuery = $request->input('q');
        $page = max((int) $request->input('page', 1), 1);
        $size = (int) $request->input('size', 10); // Default 20 results per page
        $from = ($page - 1) * $size;
        $requiredSkills = [];
        $searchTerms = request('cus_search');
        $custom_search = $searchTerms; // json_decode($searchTerms, true);

        $sort = [];

        // 1. Sort: ads_switch 'Yes' on top (pre-computed integer field: 0 for Yes, 1 for No)
        $sort[] = ['ads_switch_rank' => ['order' => 'asc', 'unmapped_type' => 'integer']];
        $sort[] = ['ads_switch_order' => ['order' => 'asc', 'unmapped_type' => 'integer']];

        // 2. Sort: skill match count (highest first if required skills provided)
        if (! empty($requiredSkills)) {
            $sort[] = [
                '_script' => [
                    'type' => 'number',
                    'script' => [
                        'lang' => 'painless',
                        'source' => "
							int matchCount = 0;
							if (doc.containsKey('job_application_skills.skills.keyword')) {
								for (s in params.skills) {
									if (doc['job_application_skills.skills.keyword'].contains(s)) {
										matchCount += 1;
									}
								}
							}
							return matchCount;
						",
                        'params' => [
                            'skills' => array_values($requiredSkills),
                        ],
                    ],
                    'order' => 'desc',
                ],
            ];
        }

        // 3. Sort: based on selected filter value (uses pre-computed numeric fields for notice period)
        switch ($valCheck) {
            case 1:
                $sort[] = ['updated_at' => ['order' => 'desc']];
                break;
            case 2:
                $sort[] = ['updated_at' => ['order' => 'asc']];
                break;
            case 3:
                $sort[] = ['ctc' => ['order' => 'asc', 'unmapped_type' => 'float']];
                break;
            case 4:
                $sort[] = ['ctc' => ['order' => 'desc', 'unmapped_type' => 'float']];
                break;
            case 5:
                $sort[] = ['notice_period_days' => ['order' => 'asc', 'unmapped_type' => 'integer']];
                $sort[] = ['np_days' => ['order' => 'asc', 'unmapped_type' => 'integer']];
                break;
            case 6:
                $sort[] = ['notice_period_days' => ['order' => 'desc', 'unmapped_type' => 'integer']];
                $sort[] = ['np_days' => ['order' => 'desc', 'unmapped_type' => 'integer']];
                break;
            case 7:
                $sort[] = ['total_exp' => ['order' => 'desc', 'unmapped_type' => 'float']];
                break;
            case 8:
                $sort[] = ['total_exp' => ['order' => 'asc', 'unmapped_type' => 'float']];
                break;
            case 9:
                $sort[] = ['notice_period_days' => ['order' => 'asc', 'unmapped_type' => 'integer']];
                $sort[] = ['np_days' => ['order' => 'asc', 'unmapped_type' => 'integer']];
                break;
            default:
                $sort[] = ['id' => ['order' => 'desc', 'unmapped_type' => 'long']];
                break;
        }

        $boolQuery = [
            'bool' => [
                'should' => [
                    // First condition
                    [
                        'bool' => [
                            'must' => array_merge([
                                ['term' => ['company_id' => $company_id]],
                                ['term' => ['status' => 2]],
                                ['term' => ['condidate_type' => 'bench_condidate']],
                            ], $this->getSearchConditions($custom_search, $country)),
                        ],
                    ],
                ],
                'minimum_should_match' => 1,
                'filter' => [], // We'll add all filters here
            ],
        ];

        $params = [
            'index' => ELASTIC_CANDIDATES_BENCH,
            'request_cache' => true,
            'body' => [
                /* Intent: Source filtering to retrieve only required card display fields, reducing network payload & memory footprint */
                '_source' => [
                    'includes' => [
                        'id',
                        'company_id',
                        'full_name',
                        'current_designation',
                        'total_exp',
                        'wm',
                        'np_id',
                        'serving_notice_period_mode',
                        'notice_period_days',
                        'np_days',
                        'city_name',
                        'country_name',
                        'qualification_name',
                        'bill_rate_amount',
                        'bill_rate_currency',
                        'bill_rate_mode',
                        'comission_type',
                        'outsourcing_type',
                        'status',
                        'skills',
                        'skill_experiences',
                        'candidate_skills',
                        'ads_switch',
                        'countforadd',
                        'updated_at',
                    ],
                ],
                'from' => $from,
                'size' => $size,
                'sort' => $sort,
                'query' => $boolQuery,
            ],
        ];

        if (! $this->elasticService->indexExists(ELASTIC_CANDIDATES_BENCH)) {
            $total = 0;
            $data = [];
        } else {
            $response = $this->elasticService->getClient()->search($params);
            $total = $response['hits']['total']['value'] ?? 0;
            $data = array_map(function ($hit) {
                return $hit['_source'];
            }, $response['hits']['hits']);
        }

        /* Intent: Cache tenant bench candidate portal metadata in Redis (TTL 1 Hour) to eliminate per-request database queries on public bench portal */
        /* Tenancy: Scoped by company_id */
        $tenantMeta = Cache::remember("tenant_bench_meta:{$company_id}", 3600, function () use ($company_id) {
            $account = DB::table('companies')->select('*')->where('id', $company_id)->first();
            $client = Client::select('clients.id', 'clients.tags')
                ->where('clients.company_id', '=', $company_id)
                ->where('clients.self_company', '=', 1)
                ->first();

            $industries_name = '';
            if ($account && $account->industries > 0) {
                $industries = DB::table('td_industries')
                    ->select('td_industries.ind_id', 'td_industries.ind_name')
                    ->where('td_industries.ind_id', '=', $account->industries)
                    ->orderBy('td_industries.ind_name', 'ASC')
                    ->first();
                $industries_name = $industries->ind_name ?? '';
            }

            $company_city_name = '';
            $company_country_name = '';

            if ($client) {
                $client_location = DB::table('client_location')
                    ->select('client_location.city', 'client_location.city_id', 'client_location.address', 'client_location.postcode', 'client_location.country_id')
                    ->where('client_location.client_id', $client->id)
                    ->orderBy('client_location.id', 'ASC')
                    ->first();

                if ($client_location) {
                    if ($client_location->country_id == 101 && $client_location->city_id) {
                        $city_obj = DB::table('city_list')->select('city_list.city')->where('city_list.city_id', $client_location->city_id)->first();
                        $company_city_name = ucwords($city_obj->city ?? '');
                    } else {
                        $company_city_name = ucwords($client_location->city ?? '');
                    }

                    if ($client_location->country_id > 0) {
                        $country_name = GeneralMethod::getCountryName($client_location->country_id);
                        foreach ($country_name as $cval) {
                            $company_country_name = ucwords($cval['name'] ?? '');
                        }
                    }
                }
            }

            $fullTimeStatusRate = DB::table('commericial_details')
                ->where('company_id', $company_id)
                ->where('commtype', 2)
                ->min('rate');

            $contractStatusRate = DB::table('commericial_details')
                ->where('company_id', $company_id)
                ->where('commtype', 1)
                ->min('rate');

            $fullTimeStatus = DB::table('commericials')->where('company_id', $company_id)->where('commtype', 2)->first();
            $contractStatus = DB::table('commericials')->where('company_id', $company_id)->where('commtype', 1)->first();

            return [
                'account' => $account,
                'client' => $client,
                'industries_name' => $industries_name,
                'company_city_name' => $company_city_name,
                'company_country_name' => $company_country_name,
                'fullTimeStatusRate' => $fullTimeStatusRate,
                'contractStatusRate' => $contractStatusRate,
                'fullTimeStatus' => $fullTimeStatus,
                'contractStatus' => $contractStatus,
            ];
        });

        $account = $tenantMeta['account'];
        $client = $tenantMeta['client'];
        $industries_name = $tenantMeta['industries_name'];
        $company_city_name = $tenantMeta['company_city_name'];
        $company_country_name = $tenantMeta['company_country_name'];
        $fullTimeStatusRate = $tenantMeta['fullTimeStatusRate'];
        $contractStatusRate = $tenantMeta['contractStatusRate'];
        $fullTimeStatus = $tenantMeta['fullTimeStatus'];
        $contractStatus = $tenantMeta['contractStatus'];

        // dd($fullTimeStatus,$contractStatus,$fullTimeStatusRate, $contractStatusRate);
        $currencySymbole = GeneralMethod::currencySymbole();
        $salary_mode = ['Monthly/Annual', 'Monthly', 'Hourly', 'Annual', 'Hourly/Annual'];

        //    if($account){

        //    }else{
        //     $data = [];
        //    }
        //    dd($account,$data);

        /* Intent: Pre-fetch agency recommendation relation status to pass to view and eliminate Blade header DB query leak */
        /* Tenancy: Scoped by company_id and to_company_id */
        $recommendateRow = DB::table('recommendation_relation')
            ->select('id', 'from_company_id', 'from_status', 'to_status', 'final_status')
            ->where(function ($q) use ($company_id, $to_company_id) {
                $q->where(function ($q2) use ($company_id, $to_company_id) {
                    $q2->where('from_company_id', $company_id)->where('to_company_id', $to_company_id);
                })->orWhere(function ($q2) use ($company_id, $to_company_id) {
                    $q2->where('from_company_id', $to_company_id)->where('to_company_id', $company_id);
                });
            })->first();

        $pagination = [
            'total' => $total,
            'current_page' => $page,
            'account' => $account,
            'client' => $client,
            'industries_name' => $industries_name,
            'company_country_name' => $company_country_name,
            'company_city_name' => $company_city_name,
            'fullTimeStatusRate' => $fullTimeStatusRate,
            'contractStatusRate' => $contractStatusRate,
            'fullTimeStatus' => $fullTimeStatus,
            'contractStatus' => $contractStatus,
            'currencySymbole' => $currencySymbole,
            'salary_mode' => $salary_mode,
            'valCheck' => $valCheck,
            'to_company_id' => $to_company_id,
            'company_id' => $company_id,
            'searchTerms' => $searchTerms,
            'data' => $data,
            'recommendateRow' => $recommendateRow,
            'last_page' => ceil($total / $size),
            'listType' => 'allcandidate',
        ];
        // dd(ceil($total / $size));

        return view('bench-condidates', $pagination);
    }

    public function careerJobPage(Request $request, $companyName, $companyId)
    {
        $company_id = (int) $companyId;

        /* Intent: Cache tenant career metadata in Redis (TTL 1 Hour) to prevent per-request database queries on public career portal */
        $tenantMeta = Cache::remember("tenant_career_meta:{$company_id}", 3600, function () use ($company_id) {
            /* Intent: Multi-tenant scoped company lookup for public career portal */
            $account = DB::table('companies')->select('*')->where('id', $company_id)->first();
            $client = Client::select('clients.id', 'clients.tags')
                ->where('clients.company_id', '=', $company_id)
                ->where('clients.self_company', '=', 1)
                ->first();

            $industries_name = '';
            if ($account && $account->industries > 0) {
                $industries = DB::table('td_industries')
                    ->select('td_industries.ind_id', 'td_industries.ind_name')
                    ->where('td_industries.ind_id', '=', $account->industries)
                    ->orderBy('td_industries.ind_name', 'ASC')
                    ->first();
                $industries_name = $industries->ind_name ?? '';
            }

            $company_city_name = '';
            $company_country_name = '';

            if ($client) {
                $client_location = DB::table('client_location')
                    ->select('client_location.city', 'client_location.city_id', 'client_location.address', 'client_location.postcode', 'client_location.country_id')
                    ->where('client_location.client_id', $client->id)
                    ->orderBy('client_location.id', 'ASC')
                    ->first();

                if ($client_location) {
                    if ($client_location->country_id == 101 && $client_location->city_id) {
                        $city_obj = DB::table('city_list')->select('city_list.city')->where('city_list.city_id', $client_location->city_id)->first();
                        $company_city_name = ucwords($city_obj->city ?? '');
                    } else {
                        $company_city_name = ucwords($client_location->city ?? '');
                    }

                    if ($client_location->country_id > 0) {
                        $country_name = GeneralMethod::getCountryName($client_location->country_id);
                        foreach ($country_name as $cval) {
                            $company_country_name = ucwords($cval['name'] ?? '');
                        }
                    }
                }
            }

            $fullTimeStatusRate = DB::table('commericial_details')
                ->where('company_id', $company_id)
                ->where('commtype', 2)
                ->min('rate');

            $contractStatusRate = DB::table('commericial_details')
                ->where('company_id', $company_id)
                ->where('commtype', 1)
                ->min('rate');

            $fullTimeStatus = DB::table('commericials')->where('company_id', $company_id)->where('commtype', 2)->first();
            $contractStatus = DB::table('commericials')->where('company_id', $company_id)->where('commtype', 1)->first();

            $careerSettings = DB::table('company_career_settings')->where('company_id', $company_id)->first();

            return [
                'account' => $account,
                'client' => $client,
                'industries_name' => $industries_name,
                'company_city_name' => $company_city_name,
                'company_country_name' => $company_country_name,
                'fullTimeStatusRate' => $fullTimeStatusRate,
                'contractStatusRate' => $contractStatusRate,
                'fullTimeStatus' => $fullTimeStatus,
                'contractStatus' => $contractStatus,
                'careerSettings' => $careerSettings,
            ];
        });

        $account = $tenantMeta['account'];
        $client = $tenantMeta['client'];
        $industries_name = $tenantMeta['industries_name'];
        $company_city_name = $tenantMeta['company_city_name'];
        $company_country_name = $tenantMeta['company_country_name'];
        $fullTimeStatusRate = $tenantMeta['fullTimeStatusRate'];
        $contractStatusRate = $tenantMeta['contractStatusRate'];
        $fullTimeStatus = $tenantMeta['fullTimeStatus'];
        $contractStatus = $tenantMeta['contractStatus'];
        $careerSettings = $tenantMeta['careerSettings'];

        // dd($fullTimeStatus,$contractStatus,$fullTimeStatusRate, $contractStatusRate);
        $currencySymbole = GeneralMethod::currencySymbole();
        $salary_mode = ['Monthly/Annual', 'Monthly', 'Hourly', 'Annual', 'Hourly/Annual'];

        $job_status = GeneralMethod::job_status();
        $salaryMode = GeneralMethod::salaryMode();
        $currencySymbole = GeneralMethod::currencySymbole();

        $searchQuery = $request->input('q');
        $page = max((int) $request->input('page', 1), 1);
        $size = (int) $request->input('size', 10); // Default 20 results per page
        $from = ($page - 1) * $size;
        $custom_status = 'active';
        $custom_hiringtype = request('custom_hiringtype');
        $custom_search = trim(request('custom_search'));

        $statusArr = ['active', 'hold', 'inactive', '2'];
        $must = [];
        $should = [];
        $filter = [];
        $sort = [];
        $valCheck = request('custom_hiringtype');
        switch ($valCheck) {
            case 1:
                $sort[] = ['id' => ['order' => 'desc']];
                break;
            case 2:
                $sort[] = ['id' => ['order' => 'asc']];
                break;
            case 3:
                $sort[] = ['max_salary' => ['order' => 'asc']];
                break;
            case 4:
                $sort[] = ['max_salary' => ['order' => 'desc']];
                break;
            case 5:
                $sort[] = ['client_name' => ['order' => 'asc']];
                break;
            case 6:
                $sort[] = ['client_name' => ['order' => 'desc']];
                break;
            default:
                $sort[] = ['id' => ['order' => 'desc']];
        }

        if ($custom_status !== '' && $custom_status !== 'all') {
            if (in_array($custom_status, ['active', 'hold', 'inactive'])) {
                $statusArr = [$custom_status];
            } elseif ($custom_status === 'removed') {
                $statusArr = [];
                // $must[] = [
                // 	'nested' => [
                // 		'path' => 'recommendation_relation',
                // 		'query' => [
                // 			'term' => ['recommendation_relation.final_status' => 2],
                // 		],
                // 	],
                // ];

            }
        }

        $filter[] = ['terms' => ['status' => $statusArr]];

        if ($custom_hiringtype !== '' && $custom_hiringtype !== 'All') {
            if ($custom_hiringtype == 7) {
                $filter[] = ['term' => ['employment_type' => 0]];
            } elseif ($custom_hiringtype == 8) {
                $filter[] = ['term' => ['employment_type' => 1]];
            }
        }

        /* ── Career page sidebar filters ── */

        // Experience filter — OR logic across selected ranges
        $filterExp = array_filter((array) $request->input('filter_exp', []));
        if (! empty($filterExp)) {
            $expClauses = [];
            foreach ($filterExp as $exp) {
                switch ($exp) {
                    case 'Fresher':
                        $expClauses[] = ['range' => ['min_exp' => ['lte' => 0]]];
                        break;
                    case '1 – 3 Years':
                        $expClauses[] = ['range' => ['min_exp' => ['gte' => 1, 'lte' => 3]]];
                        break;
                    case '3 – 5 Years':
                        $expClauses[] = ['range' => ['min_exp' => ['gte' => 3, 'lte' => 5]]];
                        break;
                    case '5 – 8 Years':
                        $expClauses[] = ['range' => ['min_exp' => ['gte' => 5, 'lte' => 8]]];
                        break;
                    case '8+ Years':
                        $expClauses[] = ['range' => ['min_exp' => ['gte' => 8]]];
                        break;
                }
            }
            if (! empty($expClauses)) {
                $filter[] = ['bool' => ['should' => $expClauses, 'minimum_should_match' => 1]];
            }
        }

        // Employment type filter — Full-time=0, Contract=1, Part-time=2, Internship=3
        $filterEmpType = array_filter((array) $request->input('filter_emp_type', []));
        if (! empty($filterEmpType)) {
            $empMap = ['Full-time' => 0, 'Contract' => 1, 'Part-time' => 2, 'Internship' => 3];
            $empClauses = [];
            foreach ($filterEmpType as $et) {
                if (isset($empMap[$et])) {
                    $empClauses[] = ['term' => ['employment_type' => $empMap[$et]]];
                }
            }
            if (! empty($empClauses)) {
                $filter[] = ['bool' => ['should' => $empClauses, 'minimum_should_match' => 1]];
            }
        }

        // Location filter — nested client_city_jobs.city_name, plus Remote = work_type:'yes'
        $filterLocation = array_filter((array) $request->input('filter_location', []));
        if (! empty($filterLocation)) {
            $locClauses = [];
            foreach ($filterLocation as $loc) {
                if (strtolower($loc) === 'remote') {
                    $locClauses[] = ['term' => ['work_type' => 'yes']];
                } else {
                    $locClauses[] = [
                        'nested' => [
                            'path' => 'client_city_jobs',
                            'query' => ['match' => ['client_city_jobs.city_name' => $loc]],
                        ],
                    ];
                }
            }
            $filter[] = ['bool' => ['should' => $locClauses, 'minimum_should_match' => 1]];
        }

        // Department filter — matches job_category_name field
        $filterDept = array_filter((array) $request->input('filter_dept', []), fn ($d) => $d !== 'All Departments');
        if (! empty($filterDept)) {
            $deptClauses = [];
            foreach (array_values($filterDept) as $dept) {
                $deptClauses[] = ['match' => ['job_category_name' => $dept]];
            }
            $filter[] = ['bool' => ['should' => $deptClauses, 'minimum_should_match' => 1]];
        }

        // Skills filter — nested job_metronics_skills.skill_name
        $filterSkills = array_filter((array) $request->input('filter_skills', []));
        if (! empty($filterSkills)) {
            $skillClauses = [];
            foreach ($filterSkills as $sk) {
                $skillClauses[] = [
                    'nested' => [
                        'path' => 'job_metronics_skills',
                        'query' => ['match' => ['job_metronics_skills.skill_name' => $sk]],
                    ],
                ];
            }
            $filter[] = ['bool' => ['should' => $skillClauses, 'minimum_should_match' => 1]];
        }

        if (! empty($custom_search)) {
            $searchShould = [
                ['match_phrase_prefix' => ['title' => strtolower($custom_search)]],
                ['match' => ['title' => ['query' => $custom_search, 'boost' => 2]]],
                ['wildcard' => ['client_or_company_name' => strtolower($custom_search).'*']],
                [
                    'nested' => [
                        'path' => 'job_assign',
                        'query' => ['wildcard' => ['job_assign.user_name' => strtolower($custom_search).'*']],
                    ],
                ],
                [
                    'nested' => [
                        'path' => 'client_contact_info_jobs',
                        'query' => ['wildcard' => ['client_contact_info_jobs.contact_person_name' => strtolower($custom_search).'*']],
                    ],
                ],
                [
                    'nested' => [
                        'path' => 'client_city_jobs',
                        'query' => ['wildcard' => ['client_city_jobs.city_name' => strtolower($custom_search).'*']],
                    ],
                ],
                [
                    'nested' => [
                        'path' => 'client_city_jobs',
                        'query' => ['wildcard' => ['client_city_jobs.country_name' => strtolower($custom_search).'*']],
                    ],
                ],
            ];

            if (is_numeric($custom_search)) {
                $searchShould[] = ['term' => ['id' => (int) $custom_search]];
            }

            $must[] = [
                'bool' => [
                    'should' => $searchShould,
                    'minimum_should_match' => 1,
                ],
            ];
        }

        $must[] = [
            'term' => ['comId' => $company_id],
        ];

        $params = [
            'index' => ELASTIC_JOBS,
            'request_cache' => true,
            'body' => [
                'from' => $from,
                'size' => $size,
                'sort' => $sort,
                'track_total_hits' => true,
                'query' => [
                    'bool' => [
                        'must' => $must,
                        'filter' => $filter,
                    ],
                ],
            ],
        ];

        if (! $this->elasticService->indexExists(ELASTIC_JOBS)) {
            $total = 0;
            $data = [];
        } else {
            $response = $this->elasticService->getClient()->search($params);
            $total = $response['hits']['total']['value'] ?? 0;
            $data = array_map(function ($hit) {
                return $hit['_source'];
            }, $response['hits']['hits']);
        }

        /* Intent: Batch eager load skills, city locations, and company logos for current page jobs to prevent N+1 queries */
        $jobSkillsMap = [];
        $jobLocationsMap = [];
        $jobLogosMap = [];

        if (! empty($data)) {
            $jobIds = array_filter(array_map(function ($item) {
                return (int) ($item['id'] ?? 0);
            }, $data));

            if (! empty($jobIds)) {
                /* Intent: Batch load skills for all jobs on current page */
                $skillsRows = DB::table('job_metronics_skills')
                    ->select('job_id', 'skill_name')
                    ->whereIn('job_id', $jobIds)
                    ->get();

                foreach ($skillsRows as $sRow) {
                    $jobSkillsMap[$sRow->job_id][] = $sRow->skill_name;
                }

                /* Intent: Batch load city locations for all jobs on current page */
                $locRows = DB::table('client_city_jobs')
                    ->leftJoin('city_list', 'city_list.city_id', '=', 'client_city_jobs.city_id')
                    ->select('client_city_jobs.job_id', 'city_list.city as cn')
                    ->whereIn('client_city_jobs.job_id', $jobIds)
                    ->get();

                foreach ($locRows as $loc) {
                    $cityName = ucwords(mb_substr(@$loc->cn, 0, 18)) ?: 'N/A';
                    $jobLocationsMap[$loc->job_id][] = $cityName;
                }

                /* Intent: Batch load company/client logo paths to eliminate openCompanyDetails N+1 query */
                $logoRows = DB::table('jobs')
                    ->select(
                        'jobs.id as job_id',
                        'clients.logo_logical_path',
                        'companies.logo as company_logo',
                        'recommendation_relation.to_company_id'
                    )
                    ->join('clients', 'clients.id', '=', 'jobs.client_id')
                    ->join('companies', 'companies.id', '=', 'jobs.company_id')
                    ->leftJoin('recommendation_relation', 'recommendation_relation.job_id', '=', 'jobs.id')
                    ->whereIn('jobs.id', $jobIds)
                    ->get();

                foreach ($logoRows as $lRow) {
                    $lPath = '';
                    if (! empty($lRow->logo_logical_path)) {
                        $lPath = $lRow->logo_logical_path;
                        if ($lRow->to_company_id == $company_id && ! empty($lRow->company_logo)) {
                            $lPath = $lRow->company_logo;
                        }
                    }
                    $jobLogosMap[$lRow->job_id] = $lPath;
                }
            }
        }

        $pagination = [
            'total' => @$total,
            'current_page' => @$page,
            'account' => $account,
            'industries_name' => $industries_name,
            'client' => $client,
            'company_country_name' => $company_country_name,
            'company_city_name' => $company_city_name,
            'fullTimeStatusRate' => $fullTimeStatusRate,
            'contractStatusRate' => $contractStatusRate,
            'fullTimeStatus' => $fullTimeStatus,
            'contractStatus' => $contractStatus,
            'currencySymbole' => @$currencySymbole,
            'salary_mode' => @$salary_mode,
            'valCheck' => @$valCheck,
            'to_company_id' => @$to_company_id,
            'company_id' => @$company_id,
            'careerSettings' => @$careerSettings,
            'searchTerms' => @$searchTerms,
            'data' => @$data,
            'jobSkillsMap' => $jobSkillsMap,
            'jobLocationsMap' => $jobLocationsMap,
            'jobLogosMap' => $jobLogosMap,
            'last_page' => ceil(@$total / @$size),
            'listType' => 'allcandidate',
        ];

        /* Intent: Support AJAX dynamic filtering for public career portal without full page reloads */
        if ($request->expectsJson() || $request->ajax() || $request->has('json')) {
            return response()->json([
                'status' => 'success',
                'total' => (int) $total,
                'current_page' => (int) $page,
                'last_page' => (int) ceil(@$total / @$size),
                'data' => $data,
                'jobSkillsMap' => $jobSkillsMap,
                'jobLocationsMap' => $jobLocationsMap,
                'jobLogosMap' => $jobLogosMap,
            ]);
        }

        return view('career-job-page', $pagination);
    }

    /* Intent: Dedicated JSON API endpoint for public career portal AJAX filtering and autocomplete */
    public function careerJobApi(Request $request, $companyId)
    {
        $companyName = $request->input('companyName', 'company');
        $request->merge(['json' => 1]);

        return $this->careerJobPage($request, $companyName, $companyId);
    }

    public function whatsappTermsAndConditions($jobId = null, $company_id = null)
    {
        $pageTitle = 'Terms And Conditions';

        return view('whatsapp-terms-and-conditions', compact('pageTitle'));
    }

    public function jobApply($jobId = null, $company_id = null)
    {

        // $jobId = GeneralMethod::value_decrypt($jobId);
        // dd($jobId);
        $pageTitle = __('Add Candidate');

        $account = DB::table('companies')->select('*')->where('id', $company_id)->first();
        $client = Client::select('clients.id', 'clients.tags')->where('clients.company_id', '=', $company_id)->where('clients.self_company', '=', 1)->first();
        $industries_name = '';
        if ($account && ! empty($account->industries) && $account->industries > 0) {
            $industries = DB::table('td_industries')
                ->select('td_industries.ind_id', 'td_industries.ind_name')
                ->where('td_industries.ind_id', '=', @$account->industries)
                ->orderBy('td_industries.ind_name', 'ASC')
                ->first();
            $industries_name = $industries->ind_name ?? '';
        }

        /* Intent: Candidate count scoped to company for tenant safety */
        $candidateCount = JobApplication::where('company_id', $company_id)->count();

        $gender = ['male' => __('modules.front.male'), 'female' => __('modules.front.female'), 'others' => __('modules.front.others')];

        /* Intent: Tenant-scoped active jobs selection query for job apply page dropdown */
        $jobs = Job::select('jobs.id', 'jobs.title', 'jobs.min_salary', 'jobs.max_salary', 'jobs.created_at', 'clients.name as client_name', 'client_location.city', 'client_location.postcode', 'client_location.city as location_city')->leftJoin('clients', 'clients.id', 'jobs.client_id')->leftJoin('client_location_jobs', 'client_location_jobs.job_id', '=', 'jobs.id')->leftJoin('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')->where('jobs.status', '=', 'active')->where('jobs.company_id', '=', $company_id)->groupBy('jobs.id')->get();

        /* Intent: Cache static qualification master reference data for 24h to avoid DB query load */
        $qualification = Cache::remember('qualification_master_list', 86400, function () {
            return DB::table('qualification_master')->select('*')->whereNotIn('id', [57, 58, 59])->orderBy('qualification_master.display_order', 'ASC')->get();
        });

        $appId = null;

        $jobDetail = Job::select('jobs.id', 'jobs.company_id', 'jobs.title', 'jobs.min_salary', 'jobs.max_salary', 'jobs.created_at', 'jobs.min_exp', 'jobs.max_exp', 'jobs.status', 'jobs.total_positions', 'clients.name as client_name', 'client_location.city', 'client_location.postcode', 'client_location.city as location_city', 'jobs.min_sal_mode', 'jobs.max_sal_mode', 'jobs.min_salary_currency', 'jobs.max_salary_currency')
            // ->join('job_assign', 'job_assign.job_id', 'jobs.id')
            ->leftJoin('clients', 'clients.id', 'jobs.client_id')
            ->leftJoin('client_location_jobs', 'client_location_jobs.job_id', '=', 'jobs.id')
            ->leftJoin('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
            ->where('jobs.id', '=', $jobId)
            ->first();

        $page_event = 'ADD';
        $currency = GeneralMethod::currency();
        $salaryMode = GeneralMethod::salaryMode();
        /* Intent: Cache static interview mode reference master data for 24h */
        $interview_mode = Cache::remember('interview_mode_master_list', 86400, function () {
            return DB::table('interview_mode')->select('*')->orderBy('interview_mode.id', 'ASC')->get();
        });
        $statelist = GeneralMethod::statelist(101);
        $job_additional_detail = '';
        $job_additional_detail = DB::table('job_additional_detail')
            ->select('*')
            ->where('job_additional_detail.company_id', '=', $company_id)
            ->where('job_additional_detail.job_id', '=', $jobId)
            ->orderBy('job_additional_detail.id', 'ASC')
            ->get();

        $jobLocation = DB::table('client_location_jobs')
            ->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
            ->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')
            ->leftJoin('state_list', 'state_list.id', '=', 'client_location.state')
            ->select('client_location.city', 'client_location.address', 'client_location.postcode', 'client_location.country_id', 'city_list.city as city_name', 'state_list.state as state_name')
            ->where('client_location_jobs.job_id', $jobId)
            ->orderBy('client_location_jobs.id', 'DESC')
            ->first();

        $cities = GeneralMethod::allCities();

        $default_loc = GeneralMethod::allCitiesSelected([]);

        $jobSkill = DB::table('job_metronics_skills')
            ->leftJoin('candidate_job_skill_exp', function ($join) {
                $join->on('candidate_job_skill_exp.job_skill_id', '=', 'job_metronics_skills.id'); // Add the candidate condition
            })
            ->select('job_metronics_skills.*', 'candidate_job_skill_exp.skill_value')
            ->where('job_metronics_skills.job_id', '=', $jobId)
            ->groupBy('job_metronics_skills.id')
            ->get();

        // dd($this->jobSkill);
        $skillShow = false;

        // $this->countries = GeneralMethod::getAllCountryList();
        $statelist = '';
        $cityListArr = '';
        $currentPage = 1;
        $this->currencyConverter = GeneralMethod::currencyConverter();

        return view('career-apply', compact('pageTitle', 'account', 'client', 'industries_name', 'jobs', 'gender', 'qualification', 'jobId', 'appId', 'jobDetail', 'page_event', 'currency', 'salaryMode', 'interview_mode', 'statelist', 'job_additional_detail', 'jobLocation', 'cities', 'default_loc', 'jobSkill', 'skillShow', 'statelist', 'cityListArr', 'currentPage', 'company_id'));
    }

    public function jobDetails($jobId = null, $company_id = null)
    {

        // $jobId = GeneralMethod::value_decrypt($jobId);
        $id = $jobId;
        // dd($jobId);
        $pageTitle = __('Add Candidate');

        $account = DB::table('companies')->select('*')->where('id', $company_id)->first();
        $client = Client::select('clients.id', 'clients.tags')->where('clients.company_id', '=', $company_id)->where('clients.self_company', '=', 1)->first();
        $industries_name = '';
        if ($account && ! empty($account->industries) && $account->industries > 0) {
            $industries = DB::table('td_industries')
                ->select('td_industries.ind_id', 'td_industries.ind_name')
                ->where('td_industries.ind_id', '=', @$account->industries)
                ->orderBy('td_industries.ind_name', 'ASC')
                ->first();
            $industries_name = $industries->ind_name ?? '';
        }

        $salaryMode = GeneralMethod::salaryMode();
        $employmentType = GeneralMethod::employmentType();

        $job = Job::Select(
            'jobs.id',
            'jobs.title',
            'jobs.work_type',
            'jobs.slug',
            'jobs.company_id as comId',
            'jobs.job_description',
            'jobs.job_requirement',
            'jobs.total_positions',
            'jobs.work_shifts',
            'jobs.location_id',
            'jobs.category_id',
            'jobs.start_date',
            'jobs.end_date',
            'jobs.status',
            'jobs.required_columns',
            'jobs.meta_details',
            'jobs.section_visibility',
            'jobs.created_at',
            'jobs.updated_at',
            'jobs.priority',
            'jobs.client_id',
            'jobs.min_exp',
            'jobs.max_exp',
            'jobs.min_salary',
            'jobs.max_salary',
            'jobs.qualification',
            'jobs.employment_type',
            'jobs.min_salary_currency',
            'jobs.max_salary_currency',
            'jobs.min_sal_mode',
            'jobs.max_sal_mode',
            'jobs.carrier_website',
            'clients.name as client_name',
            'clients.self_company',
            'clients.description as client_description',
            'companies.description as company_description',
            'companies.company_name as company_name',
            'companies.logo as company_logo',
            'clients.logo_logical_path',
            'job_categories.name as cat_name',
            'qualification_master.name as qualification_name'
        )
            ->leftJoin('clients', 'clients.id', '=', 'jobs.client_id')
            ->leftJoin('companies', 'companies.id', '=', 'jobs.company_id')
            ->leftJoin('job_categories', 'job_categories.id', '=', 'jobs.category_id')
            ->leftJoin('qualification_master', 'qualification_master.id', '=', 'jobs.qualification')
            ->where('jobs.id', $id)
            ->first();

        if (! $job) {
            abort(404, 'Job opening not found or no longer active.');
        }

        $skillArr = DB::table('job_metronics_skills')->select('*')->where('job_id', $job->id)->get();

        // Company Data
        $company_arr = DB::table('companies')->select('*')->where('id', $job->comId)->first();
        $company_name = @$company_arr->company_name;
        $category_name = 'N/A';

        $jobLocation = '';

        if ($job->id > 0) {

            $jobLocation = DB::table('client_location_jobs')
                ->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
                ->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')
                // ->join('countries', 'countries.id', '=', 'client_location.country_id')
                ->select('client_location.city', 'client_location.address', 'client_location.postcode', 'client_location.country_id', 'city_list.city as city_name')
                ->where('client_location_jobs.job_id', $job->id)
                // ->orderBy('client_location_jobs.id', 'DESC')
                ->first();

            $jobLocationCount = DB::table('client_location_jobs')
                ->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
                ->join('countries', 'countries.id', '=', 'client_location.country_id')
                ->select('client_location.city', 'client_location.address', 'client_location.postcode', 'countries.country_name')
                ->where('client_location_jobs.job_id', $job->id)
                // ->orderBy('client_location_jobs.id', 'DESC')
                ->count();

            // if($job->comId==$company_id){
            $clientContactPersonListing = DB::table('client_contact_info_jobs')
                ->join('client_contact_info', 'client_contact_info.id', '=', 'client_contact_info_jobs.client_contact_info_id')
                ->select('client_contact_info.contact_person_name')
                ->where('client_contact_info_jobs.job_id', $job->id)
                ->get();

        }

        $assignTeamCount = DB::table('job_assign')
            ->join('users', 'users.id', '=', 'job_assign.user_id')
            ->select('users.id', 'users.name', 'users.email')
            ->where('job_assign.job_id', $job->id)
            ->orderBy('users.name', 'DESC')
            ->count();
        // RECRUITER LIST
        $recruiterlist = DB::table('job_assign')
            ->join('users', 'users.id', '=', 'job_assign.user_id')
            ->join('role_user', 'role_user.user_id', 'users.id')
            ->join('roles', 'roles.id', 'role_user.role_id')
            ->leftJoin('users_profile', 'users_profile.user_id', '=', 'users.id')
            ->leftJoin('add_job_wise_condidates', function ($join) {
                $join->on('add_job_wise_condidates.job_id', '=', 'job_assign.job_id')
                    ->on('add_job_wise_condidates.submitted_by', '=', 'users.id');
            })
            ->join('companies', 'companies.id', 'users.company_id')
            ->select(
                'users.id',
                'users.name',
                'users.email',
                'users.image',
                'users.mobile',
                'roles.display_name',
                'users_profile.designation',
                DB::raw('count(add_job_wise_condidates.id) as allCandidate'),
                DB::raw('SUM(CASE WHEN add_job_wise_condidates.master_status_id = 3 THEN 1 ELSE 0 END) AS Interview'),
                DB::raw('SUM(CASE WHEN add_job_wise_condidates.master_status_id = 6 THEN 1 ELSE 0 END) AS Joined')
            )
            ->where('job_assign.job_id', $job->id)
            // ->where('job_assign.user_id', auth()->user()->id)
            ->where('companies.id', $company_id)
            ->where('users.type', '=', 0)
            ->groupBy('users.id')
            ->orderBy('users.name', 'DESC')
            ->get();

        // dd(auth()->user()->id,$company_id,$job->id);

        $jobCitySel = DB::table('client_city_jobs')
            ->join('city_list', 'city_list.city_id', '=', 'client_city_jobs.city_id')
            ->where('client_city_jobs.job_id', $job->id)
            ->pluck('city_list.city')
            ->toArray();

        if ($job->work_type == 'yes') {
            $jobCitySelected = 'Remote';
        } elseif ($job->work_type == 'no') {
            $jobCitySelected = implode(',', $jobCitySel);
        } elseif ($job->work_type == 'any') {
            $jobCitySelected = 'Remote/'.implode(',', $jobCitySel);
        } elseif ($job->work_type == 'other') {
            $jobCitySelected = ''.implode(',', $jobCitySel);
        }

        $jobId = $job->id;

        $contactQuery = DB::table('users')
            ->join('job_assign', 'job_assign.user_id', 'users.id')
            ->select('users.name', 'users.image')
            ->where('users.company_id', $company_id)
            ->where('job_assign.company_id', $company_id)
            ->where('job_assign.job_id', $jobId)
            ->get();

        $recommendedDataList = DB::table('recommendation_relation');
        $recommendedDataList = $recommendedDataList->select(
            'companies.id',
            'companies.company_name',
            'companies.logo',
            'companies.industries',
            'td_industries.ind_name',
            'jobs.title',
            'jobs.id as jobid',
            DB::raw('SUM(CASE WHEN job_applications.master_status_id = 3 THEN 1 ELSE 0 END) AS Interview'),
            DB::raw('SUM(CASE WHEN job_applications.id > 0 THEN 1 ELSE 0 END) AS Upload'),
            DB::raw('SUM(CASE WHEN job_applications.master_status_id = 6 THEN 1 ELSE 0 END) AS Joined')

        );
        $recommendedDataList = $recommendedDataList->join('companies', 'companies.id', 'recommendation_relation.to_company_id');
        $recommendedDataList = $recommendedDataList->join('jobs', 'jobs.id', 'recommendation_relation.job_id');

        $recommendedDataList = $recommendedDataList->leftJoin('td_industries', 'td_industries.ind_id', 'companies.industries');
        $recommendedDataList = $recommendedDataList->leftJoin('add_job_wise_condidates as job_applications', function ($join) {
            $join->on('job_applications.thirdparty_company_id', '=', 'companies.id');
            $join->on('job_applications.job_id', '=', 'jobs.id');
        });

        $recommendedDataList = $recommendedDataList->where('companies.id', '!=', $company_id);
        $recommendedDataList = $recommendedDataList->where('recommendation_relation.final_status', 1);
        $recommendedDataList = $recommendedDataList->where('recommendation_relation.job_id', '=', $job->id);
        $recommendedDataList = $recommendedDataList->groupBy('companies.id');
        $recommendedDataList = $recommendedDataList->get();

        $currencySymbole = GeneralMethod::currencySymbole();
        $salary_mode = ['Monthly/Annual', 'Monthly', 'Hourly', 'Annual', 'Hourly/Annual'];

        $appId = null;

        $page_event = 'ADD';
        $currency = GeneralMethod::currency();
        $salaryMode = GeneralMethod::salaryMode();
        $statelist = GeneralMethod::statelist(101);
        $job_additional_detail = '';

        // dd($this->jobSkill);
        $skillShow = false;

        // $this->countries = GeneralMethod::getAllCountryList();
        $statelist = '';
        $cityListArr = '';
        $currentPage = 1;

        return view('career-job-details', compact('job', 'account', 'client', 'industries_name', 'company_name', 'employmentType', 'jobId', 'appId', 'page_event', 'currency', 'salaryMode', 'statelist', 'job_additional_detail', 'jobLocation', 'skillShow', 'cityListArr', 'currentPage', 'company_id', 'skillArr', 'company_arr', 'category_name', 'jobLocation', 'jobLocationCount', 'clientContactPersonListing', 'assignTeamCount', 'recruiterlist', 'jobCitySelected'));
    }

    public function careerStore(Request $request)
    {
        /* Intent: Delegate job application submission to V2 JobApplyService for batch inserts and async queue indexing */
        return app(\App\Services\JobApplyService::class)->submitApplication($request);
    }

    public function formatInterviewDateTime($date, $time, $defaultTimeFormat)
    {
        if (empty($date) || empty($time)) {
            return null; // if either is missing, return null
        }

        $eventStartTime = $date.' '.$time;

        if ($defaultTimeFormat == 1) {
            return date('Y-m-d H:i:s', strtotime($eventStartTime)); // 12-hour format
        } else {
            return date('Y-m-d H:i:s', strtotime($eventStartTime));   // 24-hour format
        }
    }

    public function uploadresume(Request $request)
    {

        // Raw-byte upload: browser sends xhr.send(file), PHP reads php://input.
        // No multipart = no upload_tmp_dir write, no temp file on disk.
        $originalName = urldecode($request->header('X-File-Name', 'resume.pdf'));
        $fileSize = (int) $request->header('X-File-Size', 0);
        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        $allowedExts = ['pdf', 'doc', 'docx'];
        $allowedMimes = [
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ];

        if (! in_array($ext, $allowedExts)) {
            return response()->json(['success' => false, 'message' => 'Only PDF, DOC, DOCX files are allowed.'], 422);
        }

        if ($fileSize > 6 * 1024 * 1024) {
            return response()->json(['success' => false, 'message' => 'Maximum file size is 6 MB.'], 422);
        }

        $objectKey = 'temp/'.\Illuminate\Support\Str::uuid().'.'.$ext;

        $stream = fopen('php://input', 'r');
        \Illuminate\Support\Facades\Storage::disk('r2')->put($objectKey, $stream);
        fclose($stream);

        session([
            'resume_temp_path' => $objectKey,
            'resume_original_name' => $originalName,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'File uploaded successfully',
            'file_path' => $objectKey,
            'original_name' => $originalName,
        ]);

    }

    public function resumExtract(Request $request)
    {

        // dd($request->all());
        if (is_array($request->resumeText)) {
            $textData = implode(',', $request->resumeText);
        } else {
            $textData = $request->resumeText;
        }

        // dd($textData);

        $textData = str_replace('Resume', ' ', $textData);
        $textData = str_replace('Curriculum Vitae', ' ', $textData);
        $textData = str_replace('CURRICULUM - VITAE', ' ', $textData);
        $textData = str_replace('CURRICULUM VITAE', ' ', $textData);
        $textData = str_replace('Curriculum-vitae', ' ', $textData);
        $textData = str_replace('CV', ' ', $textData);
        $textData = str_replace('CURRICULUM', ' ', $textData);
        $textData = str_replace('Vitae', ' ', $textData);
        $textData = str_replace('Curriculum', ' ', $textData);
        $textData = str_replace('VITAE', ' ', $textData);
        $textData = str_replace('NAME', ' ', $textData);
        $textData = str_replace('Name', ' ', $textData);
        $textData = str_replace('Greater', ' ', $textData);
        $textData = str_replace('Address', ' ', $textData);
        $textData = str_replace('PERSONAL INFORMATION', ' ', $textData);
        $textData = str_replace('Contact Mobile', ' ', $textData);
        $textData = str_replace('Email', ' ', $textData);
        $textData = str_replace('email', ' ', $textData);
        $textData = str_replace('Contact', ' ', $textData);
        $textData = str_replace('contact', ' ', $textData);
        $textData = str_replace('Mobile', ' ', $textData);
        $textData = str_replace('mobile', ' ', $textData);
        $textData = str_replace('Key Skills', ' ', $textData);
        $textData = str_replace('Skills', ' ', $textData);
        $textData = str_replace('Objective', ' ', $textData);
        $textData = str_replace('Highly', ' ', $textData);
        $textData = str_replace('Highlights', ' ', $textData);
        $textData = str_replace('vitae', ' ', $textData);
        $textData = str_replace('in meeting its', ' ', $textData);
        $textData = str_replace('Committed', ' ', $textData);
        $textData = str_replace('I would', ' ', $textData);
        $textData = str_replace('Organization Post From', ' ', $textData);
        $textData = str_replace('BIO DATA', ' ', $textData);
        $textData = str_replace('Qualified', ' ', $textData);
        $textData = str_replace('I have', ' ', $textData);
        $textData = str_replace('SPECIAL', ' ', $textData);

        // dd($textData,$textData1);
        // dd($textData);
        // Exclude common header words
        $textData = $this->excludeHeaderWords($textData);

        // dd($textData);

        // Check for repeated text patterns within the initial first and last 50 words
        $name = $this->checkAdjacentNouns($textData);

        // If name is not found, check for both adjacent text nouns condition within the initial 100 words
        if (! $name) {
            $name = $this->checkRepeatedText($textData);
        }

        // If none of the above conditions identify a name, resort to regular expression matching of common name patterns
        if (! $name) {
            $name = $this->matchWithRegexes($textData);
        }
        $numberRegexes = [
            '/\b\d{10}\b/',                        // 10-digit number
            '/\b\d{3}-\d{3}-\d{4}\b/',            // XXX-XXX-XXXX format
            '/\b\(\d{3}\)\s?\d{3}-\d{4}\b/',      // (XXX) XXX-XXXX format
        ];

        $emailRegex = ['/\b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Z|a-z]{2,}\b/'];
        $number = $this->matchWithRegexesCommon($textData, $numberRegexes);
        $email = $this->matchWithRegexesCommon($textData, $emailRegex);

        $locationSet = $this->locationCountryStateCity($textData);

        $parseDateOfBirth = $this->parseDateOfBirth($textData);
        // dd($parseDateOfBirth->getData()->dob);
        $dob = $parseDateOfBirth->getData()->dob;
        // dd($dob,$parseDateOfBirth);
        // dd($textData);
        $parseCurrentEmployer = $this->parseCurrentEmployer($textData);
        $currentjob = str_replace(':', '', $parseCurrentEmployer->getData()->employer_name);
        // dd($parseCurrentEmployer);

        $careerSummary = $this->extractCareerSummary($textData);

        $jobTitles = [
            'sap sd/wm architect',
            'react.js + typescript',
            'plsql developer',
            'fullstack developer',
            'salesforce functional/business analyst',
            'salesforce techno functional',
            'salesforce technical architect',
            'linux admin',
            'server admin l3',
            'platform administrator – dynatrace, splunk',
            'php developer',
            'dotnet lead',
            'finance audit with ca',
            'technical architect (java+cloud)',
            'senior plsql postgres developer',
            'python & node developer',
            'angular developer',
            'mobile automation tester',
            'dotnet with azure',
            'sql lead',
            'php',
            'laravel',
            'ssis , ssrs & power bi',
            'php developer with laravel',
            'academic content writer',
            'business analyst (bfsi/insurance)',
            'scrum master (bfsi domain/insurance)',
            'fullstack developer',
            'python developer',
            'reactjs developer',
            'java backend developer',
            'technical support engineer',
            'data engineer',
            'data analyst',
            'project manager',
            'business analyst',
            'node.js developer',
            'java fullstack developer',
            'qa engineer',
            'principal engineer',
            'network segmentation engineer',
            'email security engineer',
            'sailpoint developer',
            'sailpoint l3 support & developer',
            'oracle dba with aws',
            'web designer',
            'full stack developer',
            'ui/ux designer',
            'devops engineer',
            'python developer',
            'ibm api connect developer',
            'ibm api connect/azure apim architect',
            'python-django developer',
            'data engineer (aws, python, sql, databricks, snowflake)',
            'business development executive',
            'sap ui5/abap developer',
            'sap abap rap developer',
            'devops engineer with aws and kubernetes',
            'dotnet tcp/ip developer',
            'database engineer',
            'angular developer',
            'data engineer lead with azure',
            'senior data engineer with azure',
            'xamarin developer',
            'node.js developer with sap capm',
            'finance controller',
            'sap abap architect',
            'health connection integration engineer',
            'senior .net developer with azure',
            'mocc consultant (sap basis administrator)',
            'mq administrator',
            'fullstack developer',
            'mern stack developer',
            'automation tester with selenium',
            'react.js + typescript developer',
            'network engineer l2',
            'network engineer l3',
            'qa automation lead',
            'sdet (selenium + python)',
            'sales manager',
            'regional business leader',
            'java developer (tier 1 / tier 2)',
            'automation tester',
            'python developer',
            'jr. salesforce developer',
            'sr. salesforce developer',
            'automation testing engineer',
            'qa lead (automation testing)',
            'senior principal engineer (java)',
            'senior data engineer (snaplogic + azure data bricks or snaplogic + synapse analytics)',
            'php developer with laravel and aws',
            'sap mm consultant',
            'executive assistant (female)',
            'dotnet developer',
            'java developer (tier 1 / tier 2)',
            'aws data engineer',
            'react.js with next.js developer',
            'devops engineer',
            'php developer with codeigniter',
            'java developer',
            'lead sdet (selenium + java/python)',
            'web automation tester',
            'manual tester',
            'automation tester',
            'qa manager (tier 1 / tier 2) (ecommerce or internet domain)',
            'java architect (java + react + aws)',
            'python architect',
            'l3 application security engineer',
            'llm',
            'rpa expert (c#/java, sql / t-sql, api, abby, python, r, huggingface)',
            'rpa developer (automation anywhere, sql, ssis, tensorflow, abby, python/r, java)',
            'program manager',
            'realestate-project sales manager (male candidate)',
            'product manager',
            'technical architect (java/python)',
            'devops lead',
            'database administrator (sql and no sql)',
            'sr. platform manager',
            'lims developer',
            'inside sales executive',
            'field sales executive (tier 1 and tier 2)',
            'data scientist/ai-ml engineer',
            'python & node developer',
            'data science',
            'business analyst (tier 1 and tier 2)',
            'frontend developer (react with typescript) (tier 1 college)',
            'plsql developer',
            'automation tester',
            'backend developer (java/python/node/golang)',
            'backend developer (node/golang)',
            'business analyst',
            'database developer (sql, postgres, no sql)',
            'database developer',
            'flutter developer',
            'frontend developer (react/vue.js)',
            'frontend developer',
            'frontend architect (mobile and web)',
            'senior devops engineer',
            'senior executive- performance marketing',
            'senior executive growth team',
            'seo specialist',
            'social media marketing executive',
            'drupal developer',
            'program manager',
            'project manager - technical',
            'java developer',
            'functional tester',
            'java lead developer',
            'hcl cdp developer',
            'hcl unica api developer',
            'hcl unica campaign developer',
            'hcl unica technical architect',
            'automation tester',
            'academic content writer (for management subject)',
            'senior embedded linux software engineer',
            'hardware engineer',
            'scrum master (it domain)',
            'salesforce developer',
            'fullstack developer',
            'technical lead (php, laravel+vue.js)',
            'senior .net developer',
            'aws data engineer (aws, python, pyspark, sql, snowflake)',
            'adobe campaign developer (adobe campaign standard, javascript, adobe administration)',
            'backend developer (node.js, typescript)',
            'drupal developer (acquia, studio)',
            'assistant operation manager - ar follow up/billing',
            'backend developer (python with django)',
            'frontend developer (angular)',
            'senior tech lead - python lead',
            'senior security analyst-cyber security',
            'data science lead',
            'data science ml',
            'data science cv',
            '.net developer',
            'manager ops',
            'angular developer',
            'ssis, ssrs & power bi',
            'ror developer',
            'lead sdet (selenium + python)',
            'lead ops',
            'executive ops',
            'mern developer',
            'mobile automation testing-appium',
            'python+react developer',
            'saas infrastructure engineer',
            'cisco sme',
            'content writer',
            'senior cloud network engineer',
            '.net technical architect (c#, angular)',
            '.netcore full stack developer',
            'java developer',
            'ad sales senior executive/assistant manager',
            'government sales manager',
            'marketing executive',
            'input desk manager',
            'saas sales',
            'frontend developer (javascript, typescript, react)',
            '.net + angular developer',
            'lead frontend developer (javascript, typescript, react)',
            '.netcore+angular.js/blazor developer',
            'gcp data engineer (apache airflow, big data, teradata, google kubernetes engine)',
            'java developer',
            'java fullstack developer',
            'angular developer',
            'angular developer',
            'java developer',
            'python developer+aws',
            'research engineer-path planning',
            'research engineer-perception',
            'slam developer',
            'senior android developer',
            'automation engineer',
            'compliance and grc analyst',
            'network administrator',
            'windows administrator',
            'database administrator-ms sql',
            '.net developer (asp .net, asp .net core, c#, ado .net)angular',
            'disaster recovery specialist',
            'senior/lead uft automation engineer',
            '.netcore+angular+mvc, microservices developer',
            'dotnet developer',
            '.netcore+angular8 developer',
            'c/c++ developer',
            'dba',
            'frontend developer-react',
            'backend developer-java',
            'qa tester',
            'data science engineer',
            'program manager',
            'ui/ux designer',
            'embedded engineering manager',
            'golang developer',
            'angular fullstack developer',
            'embedded developer',
            '.net developer-webapi, dot net core, entity framework',
            '.netcore+azure developer',
            'senior/lead qa automation engineer (selenium+java)',
            'azure data engineer',
            '.net fullstack developer',
            'product manager',
            'devops lead',
            'operations support lead',
            'devops engineer with oci',
            'net core developer-(financial accountancy & inventory module)',
            'senior software engineer (.netcore+angular/react)',
            'java developer with microservices',
        ];

        $job_metronics_skills = DB::table('job_metronics_skills')->where('job_id', $request->jobId)->pluck('skill_name')->toArray();

        // dd($job_metronics_skills);

        // Initialize an empty array to store matched job titles
        $matchedJobTitles = [];

        if (count($job_metronics_skills) > 0) {
            // Loop through the job titles array to find matches in the text
            foreach ($job_metronics_skills as $jobTitle) {
                if (stripos($textData, $jobTitle) !== false) {
                    // If a match is found, add it to the matched job titles array
                    $matchedJobTitles[] = ucwords($jobTitle);
                }
            }
        } else {
            foreach ($jobTitles as $jobTitle) {
                if (stripos($textData, $jobTitle) !== false) {
                    // If a match is found, add it to the matched job titles array
                    $matchedJobTitles[] = ucwords($jobTitle);
                }
            }
        }
        $matchedqualification = '';
        $qualification_row = DB::table('qualification_master')
            ->select('qualification_master.id', 'qualification_master.name')
            ->pluck('name', 'id')->toArray();
        // dd($qualification_row);
        if ($qualification_row) {
            foreach ($qualification_row as $key => $qualifiactionTitle) {
                if (stripos($textData, $qualifiactionTitle) !== false) {
                    // If a match is found, add it to the matched job titles array
                    $matchedqualification = ucwords($key);
                }
            }

        }
        // dd($matchedqualification);

        $location = '';
        // dd($locationSet['city'] ,$locationSet['state'] , $locationSet['country']);
        if ($locationSet['city'] && $locationSet['state'] && $locationSet['country']) {
            $location = $locationSet['city'].' | '.$locationSet['state'].' | '.$locationSet['country'];

            // $city = $locationSet['city'];
            // $city_row_city= DB::table('city_list')
            // 			   ->join('state_list','state_list.id','=','city_list.state_id')
            // 			   ->join('countries','countries.id','=','state_list.country_id')
            // 			   ->select('city_list.city','countries.country_name','state_list.state')
            // 			   ->where('city_list.city', 'LIKE', "%{$city}%")
            // 			   ->first();
            // 			   if($city_row_city){
            // 				   $location  =  $city_row_city->city. ' | ' . $city_row_city->state . ' | ' . $city_row_city->country_name;
            // 			   }
        }

        // LinkedIn
        preg_match('/(?:https?:\/\/)?(?:www\.)?linkedin\.[a-z.]+\/[^\s]+/i', $textData, $linkedinMatch);
        $linkedin = $linkedinMatch[0] ?? null;

        // GitHub
        preg_match('/(?:https?:\/\/)?(?:www\.)?github\.[a-z.]+\/[^\s]+/i', $textData, $githubMatch);
        $github = $githubMatch[0] ?? null;

        // Behance
        preg_match('/(?:https?:\/\/)?(?:www\.)?behance\.[a-z.]+\/[^\s]+/i', $textData, $behanceMatch);
        $behance = $behanceMatch[0] ?? null;

        // dd($locationSet['city']);
        // dd($matchedJobTitles);
        // If validation passes, return the parsed data
        return response()->json([
            'name' => str_replace('of', '', $name),
            'number' => $number,
            'email' => $email,
            'city' => ($locationSet['city']) ? $locationSet['city'] : '',
            'state' => ($locationSet['state']) ? $locationSet['state'] : '',
            'country' => ($location) ? $location : '',
            'dob' => ($dob) ? $dob : '',
            'skills' => array_unique($matchedJobTitles),
            'qualification' => ($matchedqualification),
            'currentjob' => ($currentjob) ? $currentjob : '',
            'linkedin' => $linkedin,
            'github' => $github,
            'behance' => $behance,
            'careerSummary' => $careerSummary,
        ]);

    }

    private function extractCareerSummary($text)
    {

        $headings = [
            'Career Objective', 'Career Summary', 'Professional Summary', 'Objective',
            'Profile Summary', 'Executive Summary', 'Summary', 'About Me', 'Profile',
            'Career Highlights', 'Overview', 'Personal Statement', 'Introduction',
        ];

        $pattern = '/(?:'.implode('|', array_map('preg_quote', $headings)).')\s*[:|\-]?\s*(.*?)(?=\n\s*\n|\n\s*[A-Z][A-Za-z\s]+[:|\-]|\z)/is';

        if (preg_match($pattern, $text, $match)) {
            $summary = trim(preg_replace('/\s+/', ' ', $match[1]));

            if (str_word_count($summary) > 100) {
                $words = preg_split('/\s+/', $summary);
                $summary = implode(' ', array_slice($words, 0, 100));
            }

            return $summary;
        }

        return null;

    }

    protected function getSearchConditions($custom_search, $country)
    {
        $conditions = [];

        // Apply custom search if it's not empty (Intent: High-performance ES prefix search eliminating unindexed leading wildcards)
        if (! empty($custom_search)) {
            $searchTerm = strtolower(trim($custom_search));
            $conditions[] = [
                'bool' => [
                    'should' => [
                        [
                            'match_phrase_prefix' => [
                                'full_name' => [
                                    'query' => $searchTerm,
                                    'max_expansions' => 10,
                                ],
                            ],
                        ],
                        [
                            'match_phrase_prefix' => [
                                'current_designation' => [
                                    'query' => $searchTerm,
                                    'max_expansions' => 10,
                                ],
                            ],
                        ],
                        [
                            'match_phrase_prefix' => [
                                'skills' => [
                                    'query' => $searchTerm,
                                    'max_expansions' => 10,
                                ],
                            ],
                        ],
                        [
                            'term' => ['email' => $searchTerm],
                        ],
                        [
                            'term' => ['phone' => $searchTerm],
                        ],
                        [
                            'match_phrase_prefix' => [
                                'country_name' => [
                                    'query' => $searchTerm,
                                    'max_expansions' => 5,
                                ],
                            ],
                        ],
                        [
                            'match_phrase_prefix' => [
                                'qualification_name' => [
                                    'query' => $searchTerm,
                                    'max_expansions' => 5,
                                ],
                            ],
                        ],
                        [
                            'match_phrase_prefix' => [
                                'state_name' => [
                                    'query' => $searchTerm,
                                    'max_expansions' => 5,
                                ],
                            ],
                        ],
                        [
                            'match_phrase_prefix' => [
                                'city_name' => [
                                    'query' => $searchTerm,
                                    'max_expansions' => 5,
                                ],
                            ],
                        ],
                        [
                            'match_phrase_prefix' => [
                                'posted_by' => [
                                    'query' => $searchTerm,
                                    'max_expansions' => 5,
                                ],
                            ],
                        ],
                    ],
                    'minimum_should_match' => 1,
                ],
            ];
        }

        return $conditions;
    }

    // Helper method for notice period filter
    protected function getNoticePeriodFilter($app_int_notice, $current_date)
    {
        switch ($app_int_notice) {
            case 8: // Immediate
                return [
                    'bool' => [
                        'must' => [
                            ['term' => ['np_id.keyword' => 'Immediate']],
                            ['term' => ['serving_notice_period_mode' => 2]],
                        ],
                    ],
                ];
            case 2: // 0-15 days
                return [
                    'bool' => [
                        'must' => [
                            ['range' => ['np_id' => ['gte' => 0, 'lte' => 15]]],
                            ['term' => ['serving_notice_period_mode' => 0]],
                        ],
                    ],
                ];
            case 3: // 0-30 days
                return [
                    'bool' => [
                        'must' => [
                            ['range' => ['np_id' => ['gte' => 0, 'lte' => 30]]],
                            ['term' => ['serving_notice_period_mode' => 0]],
                        ],
                    ],
                ];
            case 4: // 0-60 days
                return [
                    'bool' => [
                        'must' => [
                            ['range' => ['np_id' => ['gte' => 0, 'lte' => 60]]],
                            ['term' => ['serving_notice_period_mode' => 0]],
                        ],
                    ],
                ];
            case 5: // 0-90 days
                return [
                    'bool' => [
                        'must' => [
                            ['range' => ['np_id' => ['gte' => 0, 'lte' => 90]]],
                            ['term' => ['serving_notice_period_mode' => 0]],
                        ],
                    ],
                ];
            case 6: // Any notice period
                return [
                    'bool' => [
                        'must' => [
                            ['range' => ['np_id' => ['gt' => 0]]],
                            ['term' => ['serving_notice_period_mode' => 0]],
                        ],
                    ],
                ];
            case 7: // After current date
                return [
                    'bool' => [
                        'must' => [
                            ['range' => ['np_id' => ['gt' => $current_date]]],
                            ['term' => ['serving_notice_period_mode' => 1]],
                        ],
                    ],
                ];
            case 9: // Before current date
                return [
                    'bool' => [
                        'must' => [
                            ['range' => ['np_id' => ['lt' => $current_date]]],
                            ['term' => ['serving_notice_period_mode' => 1]],
                        ],
                    ],
                ];
            default:
                return null;
        }
    }

    public function clientRequestIndexElastic(Request $request)
    {

        if (! $this->elasticService->indexExists(ELASTIC_CLIENT_AND_AGENCY_RELATION_INDEX)) {
            $params = [
                'index' => ELASTIC_CLIENT_AND_AGENCY_RELATION_INDEX,
                'body' => [
                    'settings' => [
                        'number_of_shards' => 1,
                        'number_of_replicas' => 0,
                        'index.blocks.read_only_allow_delete' => false,
                        'index.blocks.write' => false,
                        'index.blocks.read' => false,
                        'index.blocks.metadata' => false,
                        'index.lifecycle.name' => null,
                        'index.lifecycle.rollover_alias' => null,
                        'index.requests.cache.enable' => true,
                        'analysis' => [
                            'analyzer' => [
                                'comma_analyzer' => [
                                    'type' => 'custom',
                                    'tokenizer' => 'comma_tokenizer',
                                    'filter' => ['lowercase', 'trim'],
                                ],
                                'number_list_analyzer' => [
                                    'type' => 'custom',
                                    'tokenizer' => 'number_list_tokenizer',
                                    'filter' => ['trim'],
                                ],
                            ],
                            'tokenizer' => [
                                'comma_tokenizer' => [
                                    'type' => 'pattern',
                                    'pattern' => '\\s*,\\s*',
                                ],
                                'number_list_tokenizer' => [
                                    'type' => 'pattern',
                                    'pattern' => '\\s*,\\s*',
                                ],
                            ],
                            'filter' => [
                                'trim' => [
                                    'type' => 'trim',
                                ],
                            ],
                        ],
                    ],
                    'mappings' => [
                        'properties' => [
                            'id' => ['type' => 'long'],
                            'from_company_id' => ['type' => 'long'],
                            'from_client_id' => ['type' => 'long'],
                            'from_user_id' => ['type' => 'long'],
                            'from_status' => ['type' => 'long'],
                            'from_created_at' => ['type' => 'date'],
                            'to_company_id' => ['type' => 'long'],
                            'to_client_id' => ['type' => 'long'],
                            'to_user_id' => ['type' => 'long'],
                            'to_status' => ['type' => 'long'],
                            'to_created_at' => ['type' => 'date'],
                            'final_status' => ['type' => 'long'],
                            'created_at' => ['type' => 'date'],
                            'updated_at' => ['type' => 'date'],
                        ],
                    ],
                ],
            ];

            $this->elasticService->getClient()->cluster()->putSettings([
                'body' => [
                    'persistent' => [
                        'action.destructive_requires_name' => true,
                    ],
                ],
            ]);
            $response = $this->elasticService->getClient()->indices()->create($params);
        }

        $staffing_company_relation = DB::table('staffing_company_relation')
            ->get()
            ->map(function ($s) {
                return [
                    'id' => (int) $s->id,
                    'from_company_id' => (int) $s->from_company_id,
                    'from_client_id' => (int) $s->from_client_id,
                    'from_user_id' => (int) $s->from_user_id,
                    'from_status' => (int) $s->from_status,
                    'from_created_at' => ($s->from_created_at) ? Carbon::parse($s->from_created_at)->toIso8601String() : null,
                    'to_company_id' => (int) $s->to_company_id,
                    'to_client_id' => (int) $s->to_client_id,
                    'to_user_id' => (int) $s->to_user_id,
                    'to_status' => (int) $s->to_status,
                    'to_created_at' => ($s->to_created_at) ? Carbon::parse($s->to_created_at)->toIso8601String() : null,
                    'final_status' => (int) $s->final_status,
                    'created_at' => ($s->created_at) ? Carbon::parse($s->created_at)->toIso8601String() : null,
                    'updated_at' => ($s->updated_at) ? Carbon::parse($s->updated_at)->toIso8601String() : null,
                ];
            })
            ->toArray();
        foreach ($staffing_company_relation as $row) {
            $row = (object) $row;
            $response = $this->elasticService->getClient()->update([
                'index' => ELASTIC_CLIENT_AND_AGENCY_RELATION_INDEX,
                'id' => $row->id,
                'retry_on_conflict' => 5,
                'body' => [
                    'doc' => [
                        'id' => $row->id,
                        'from_company_id' => $row->from_company_id,
                        'from_client_id' => $row->from_client_id,
                        'from_user_id' => $row->from_user_id,
                        'from_status' => $row->from_status,
                        'from_created_at' => $row->from_created_at,
                        'to_company_id' => $row->to_company_id,
                        'to_client_id' => $row->to_client_id,
                        'to_user_id' => $row->to_user_id,
                        'to_status' => $row->to_status,
                        'to_created_at' => $row->to_created_at,
                        'final_status' => $row->final_status,
                        'created_at' => $row->created_at,
                        'updated_at' => $row->updated_at,
                    ],
                    'doc_as_upsert' => true,
                    'detect_noop' => false,
                ],
            ]);
        }

        $searchQuery = $request->input('q');
        $page = max((int) $request->input('page', 1), 1);
        $size = (int) $request->input('size', 500); // Default 20 results per page
        $from = ($page - 1) * $size;
        $sort = [];
        $params = [
            'index' => ELASTIC_CLIENT_AND_AGENCY_RELATION_INDEX,
            'body' => [
                'from' => $from,
                'size' => $size,
                'sort' => $sort,
            ],
        ];
        $response = $this->elasticService->getClient()->search($params);

        $total = $response['hits']['total']['value'] ?? 0;
        $data = array_map(function ($hit) {
            return $hit['_source'];
        }, $response['hits']['hits']);

        $params = [
            'index' => ELASTIC_CLIENT_LIST,
            'id' => 63205,
        ];

        $response = $this->elasticService->getClient()->get($params);
        $company_job = (object) $response['_source'];

        // GeneralMethod::clientStaffingPartner(63205);

        $clientRequestDataList = DB::table('companies')
            ->select(
                'companies.id',
                'companies.company_name',
                'companies.logo',
                'companies.industries',
                'clients.id as client_id',
                'clients.tags',
                'td_industries.ind_name',
                'staffing_company_invitation.id as staffing_company_invitation_id',
                'staffing_company_invitation.from_company_id',
                'staffing_company_invitation.from_client_id',
                'staffing_company_invitation.from_user_id',
                'staffing_company_invitation.from_status',
                'staffing_company_invitation.from_created_at',
                'staffing_company_invitation.to_company_id',
                'staffing_company_invitation.to_email',
                'staffing_company_invitation.to_status',
                'staffing_company_invitation.job_id',
                'staffing_company_invitation.to_invitation_status'

            )

            ->join('staffing_company_invitation', 'staffing_company_invitation.from_company_id', '=', 'companies.id')
            ->join('clients', 'clients.company_id', '=', 'companies.id')
            ->leftJoin('td_industries', 'td_industries.ind_id', '=', 'companies.industries');
        // dd($company_id);
        $clientRequestDataList = $clientRequestDataList->where('clients.self_company', '=', 1);
        // $clientRequestDataList = $clientRequestDataList->where('staffing_company_invitation.id', '=',141);
        $clientRequestDataList = $clientRequestDataList->get();

        $bulkData = [];
        $ik = 1;
        foreach ($clientRequestDataList as $row) {
            $client_id = $row->client_id;
            $locInfo = DB::table('client_location')
                ->select('client_location.id', 'client_location.city', 'client_location.address', 'client_location.postcode', 'client_location.country_id', 'city_list.city as city_name', 'countries.country_name')
                ->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')
                ->leftJoin('countries', 'countries.id', '=', 'client_location.country_id')
                ->where('client_id', $client_id)
                ->first();
            $country_name = '';
            $city_name = '';
            if ($locInfo) {
                $country_name = $locInfo->country_name;
                $city_name = $locInfo->city_name;
            }

            $bulkData[] = [
                'index' => [
                    '_index' => ELASTIC_CLIENT_REQUEST,
                    '_id' => $row->staffing_company_invitation_id,
                ],
            ];
            $updateTime = now()->toIso8601String();
            $bulkData[] = [
                'id' => $row->staffing_company_invitation_id,
                'company_id' => $row->id,
                'company_name' => $row->company_name,
                'logo' => $row->logo,
                'industries' => $row->industries,
                'client_id' => $row->client_id,
                'tags' => $row->tags,
                'ind_name' => $row->ind_name,
                'country_name' => $country_name,
                'staffing_company_invitation_id' => $row->staffing_company_invitation_id,
                'from_company_id' => $row->from_company_id,
                'from_created_at' => ($row->from_created_at) ? Carbon::parse($row->from_created_at)->toIso8601String() : null,
                'updated_at' => $updateTime,
                'created_at' => $updateTime,
                'from_client_id' => $row->from_client_id,
                'from_user_id' => $row->from_user_id,
                'from_status' => $row->from_status,
                'to_company_id' => $row->to_company_id,
                'to_email' => $row->to_email,
                'to_status' => $row->to_status,
                'job_id' => $row->job_id,
                'to_invitation_status' => $row->to_invitation_status,
                'skillArr' => $skillArr ?? [],
                'city_name' => $city_name,
            ];
            $ik++;
        }
        $response = $this->elasticService->getClient()->bulk([
            'body' => $bulkData,
        ]);

        $searchQuery = $request->input('q');
        $page = max((int) $request->input('page', 1), 1);
        $size = (int) $request->input('size', 500); // Default 20 results per page
        $from = ($page - 1) * $size;
        $sort = [];
        $params = [
            'index' => ELASTIC_CLIENT_REQUEST,
            'body' => [
                'from' => $from,
                'size' => $size,
                'sort' => $sort,
            ],
        ];
        $response = $this->elasticService->getClient()->search($params);

        $total = $response['hits']['total']['value'] ?? 0;
        $data = array_map(function ($hit) {
            return $hit['_source'];
        }, $response['hits']['hits']);

    }

    public function jobRequestIndexElastic(Request $request)
    {

        // if (!$this->elasticService->indexExists(ELASTIC_JOB_REQUEST)) {
        //         $params = [
        //             'index' => ELASTIC_JOB_REQUEST,
        //             'body' => [
        //                 'settings' => [
        //                     'number_of_shards' => 1,
        //                     'number_of_replicas' => 0,
        //                     'index.blocks.read_only_allow_delete' => false,
        //                     'index.blocks.write' => false,
        //                     'index.blocks.read' => false,
        //                     'index.blocks.metadata' => false,
        //                     'index.lifecycle.name' => null,
        //                     'index.lifecycle.rollover_alias' => null,
        //                     'index.requests.cache.enable' => true,
        //                     'analysis' => [
        //                         'analyzer' => [
        //                             'comma_analyzer' => [
        //                                 'type' => 'custom',
        //                                 'tokenizer' => 'pattern',
        //                                 'pattern' => '\\s*,\\s*', // Handles spaces around commas
        //                                 'filter' => ['lowercase', 'trim']
        //                             ],
        //                             'number_list_analyzer' => [
        //                                 'type' => 'custom',
        //                                 'tokenizer' => 'number_list_tokenizer',
        //                                 'filter' => ['trim']
        //                             ]
        //                         ],
        //                         'tokenizer' => [
        //                             'number_list_tokenizer' => [
        //                                 'type' => 'pattern',
        //                                 'pattern' => '\\s*,\\s*',
        //                                 'group' => -1
        //                             ]
        //                         ]
        //                     ]
        //                 ],
        //                 'mappings' => [
        //                     'properties' => [
        //                         'max_exp' => ['type' => 'float'],
        //                         'min_exp' => ['type' => 'float'],
        //                     ]
        //                 ]
        //             ]
        //         ];
        //         $this->elasticService->getClient()->cluster()->putSettings([
        //             'body' => [
        //                 'persistent' => [
        //                     'action.destructive_requires_name' => true
        //                 ]
        //             ]
        //         ]);
        //         $response = $this->elasticService->getClient()->indices()->create($params);
        // }

        $categories = DB::table('jobs')
            ->select(
                'jobs.id',
                'jobs.title',
                'jobs.total_positions',
                'jobs.work_type',
                'jobs.employment_type',
                'jobs.priority',
                'jobs.status',
                'jobs.min_exp',
                'jobs.max_exp',
                'jobs.created_at',
                'jobs.start_date',
                'jobs.end_date',
                'jobs.min_salary',
                'jobs.max_salary',
                'jobs.min_sal_mode',
                'jobs.max_sal_mode',
                'jobs.min_salary_currency',
                'jobs.max_salary_currency',
                'clients.name as clients_name',
                'clients.logo_logical_path',
                'companies.logo',
                'companies.company_name',
                'recommendation_relation.id as recommendation_relation_id',
                'recommendation_relation.from_company_id',
                'recommendation_relation.accepterms',
                'recommendation_relation.from_user_id',
                'recommendation_relation.from_status',
                'recommendation_relation.from_created_at',
                'recommendation_relation.to_company_id',
                'recommendation_relation.to_user_id',
                'recommendation_relation.job_id',
                'recommendation_relation.to_status',
                'recommendation_relation.to_created_at',
                'recommendation_relation.final_status',
                'recommendation_relation.conversation_id',
                'conversations.status as conversations_status',
                'recommendation_relation.add_status',
                'recommendation_relation.created_at as created_at_rec',
                'recommendation_relation.updated_at as updated_at_rec',
                'to_com.logo as logo_to',
                'to_com.company_name as company_name_to'
            )
            ->join('clients', 'clients.id', 'jobs.client_id')
            ->join('companies', 'companies.id', 'clients.company_id')
            ->join('recommendation_relation', 'recommendation_relation.job_id', 'jobs.id')
            ->leftJoin('conversations', 'conversations.id', '=', 'recommendation_relation.conversation_id')
            ->leftJoin('companies as to_com', 'to_com.id', '=', 'recommendation_relation.to_company_id')
            // ->leftJoin('job_applications', 'job_applications.job_id', '=', 'jobs.id')
            ->where('jobs.id', '>', '0');
        // $categories =  $categories->where('recommendation_relation.id', 139);
        // $categories->whereIn('recommendation_relation.final_status', [0, 3]);
        //  $categories =  $categories->groupBy('jobs.id');
        $categories = $categories->orderBy('jobs.id', 'DESC')->get();

        //   dd($categories);

        $bulkData = [];
        $ik = 1;
        foreach ($categories as $row) {

            $client_loc = DB::table('client_location_jobs')
                ->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
                ->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')
                ->select(
                    'client_location.address',
                    'client_location.city',
                    'client_location.postcode',
                    'client_location.country_id',
                    'city_list.city as city_name',
                )
                ->where('client_location_jobs.job_id', $row->id)
                ->first();
            $city_name = '';
            if ($client_loc) {
                $city_name = ($client_loc->city_name) ? $client_loc->city_name : null;
            }

            $skillArr = DB::table('job_metronics_skills')
                ->where('job_id', $row->id)
                ->where('company_id', $row->from_company_id)
                ->pluck('skill_name')
                ->toArray();

            $response = $this->elasticService->getClient()->update([
                'index' => ELASTIC_JOB_REQUEST,
                'id' => $row->recommendation_relation_id,
                'retry_on_conflict' => 5,
                'body' => [
                    'doc' => [
                        'id' => $row->recommendation_relation_id,
                        'title' => $row->title,
                        'total_positions' => $row->total_positions,
                        'work_type' => $row->work_type,
                        'employment_type' => $row->employment_type,
                        'priority' => $row->priority,
                        'accepterms' => $row->accepterms,
                        'status' => $row->status,
                        'min_exp' => $row->min_exp,
                        'max_exp' => $row->max_exp,
                        'created_at' => ($row->created_at) ? Carbon::parse($row->created_at)->toIso8601String() : null,
                        'start_date' => ($row->start_date) ? Carbon::parse($row->start_date)->toIso8601String() : null,
                        'end_date' => ($row->end_date) ? Carbon::parse($row->end_date)->toIso8601String() : null,
                        'min_salary' => $row->min_salary,
                        'max_salary' => $row->max_salary,
                        'min_sal_mode' => $row->min_sal_mode,
                        'max_sal_mode' => $row->max_sal_mode,
                        'min_salary_currency' => $row->min_salary_currency,
                        'max_salary_currency' => $row->max_salary_currency,
                        'clients_name' => $row->clients_name,
                        'logo_logical_path' => $row->logo_logical_path,
                        'logo' => $row->logo,
                        'company_name' => $row->company_name,
                        'recommendation_relation_id' => $row->recommendation_relation_id,
                        'from_company_id' => $row->from_company_id,
                        'from_user_id' => $row->from_user_id,
                        'from_status' => $row->from_status,
                        'from_created_at' => ($row->from_created_at) ? Carbon::parse($row->from_created_at)->toIso8601String() : null,
                        'to_company_id' => $row->to_company_id,
                        'logo_to' => $row->logo_to,
                        'company_name_to' => $row->company_name_to,
                        'to_user_id' => $row->to_user_id,
                        'job_id' => $row->id,
                        'to_status' => $row->to_status,
                        'to_created_at' => ($row->to_created_at) ? Carbon::parse($row->to_created_at)->toIso8601String() : null,
                        'final_status' => $row->final_status,
                        'conversation_id' => $row->conversation_id,
                        'conversations_status' => $row->conversations_status,
                        'add_status' => $row->add_status,
                        'updated_at_rec' => ($row->updated_at_rec) ? Carbon::parse($row->updated_at_rec)->toIso8601String() : null,
                        'created_at_rec' => ($row->created_at_rec) ? Carbon::parse($row->created_at_rec)->toIso8601String() : null,
                        'skillArr' => $skillArr ?? [],
                        'city_name' => $city_name,
                    ],
                    'doc_as_upsert' => true,
                    'detect_noop' => false,
                ],
            ]);

        }

        $searchQuery = $request->input('q');
        $page = max((int) $request->input('page', 1), 1);
        $size = (int) $request->input('size', 500); // Default 20 results per page
        $from = ($page - 1) * $size;
        $sort = [];
        $params = [
            'index' => ELASTIC_JOB_REQUEST,
            'body' => [
                'from' => $from,
                'size' => $size,
                'sort' => $sort,
            ],
        ];
        $response = $this->elasticService->getClient()->search($params);

        $total = $response['hits']['total']['value'] ?? 0;
        $data = array_map(function ($hit) {
            return $hit['_source'];
        }, $response['hits']['hits']);

    }

    public function candidateRequestIndexElastic(Request $request)
    {

        if (! $this->elasticService->indexExists(ELASTIC_CANDIDATES_REQUEST)) {
            $params = [
                'index' => ELASTIC_CANDIDATES_REQUEST,
                'body' => [
                    'settings' => [
                        'number_of_shards' => 1,
                        'number_of_replicas' => 0,
                        'index.blocks.read_only_allow_delete' => false,
                        'index.blocks.write' => false,
                        'index.blocks.read' => false,
                        'index.blocks.metadata' => false,
                        'index.lifecycle.name' => null,
                        'index.lifecycle.rollover_alias' => null,
                        'index.requests.cache.enable' => true,
                        'analysis' => [
                            'analyzer' => [
                                'comma_analyzer' => [
                                    'type' => 'custom',
                                    'tokenizer' => 'pattern',
                                    'pattern' => '\\s*,\\s*', // Handles spaces around commas
                                    'filter' => ['lowercase', 'trim'],
                                ],
                                'number_list_analyzer' => [
                                    'type' => 'custom',
                                    'tokenizer' => 'number_list_tokenizer',
                                    'filter' => ['trim'],
                                ],
                            ],
                            'tokenizer' => [
                                'number_list_tokenizer' => [
                                    'type' => 'pattern',
                                    'pattern' => '\\s*,\\s*',
                                    'group' => -1,
                                ],
                            ],
                        ],
                    ],
                    'mappings' => [
                        'properties' => [
                            'email' => ['type' => 'keyword'],
                            'phone' => ['type' => 'keyword'],
                            'total_exp' => ['type' => 'float'],
                            'rel_exp' => ['type' => 'float'],
                            'duplicate_candidate' => ['type' => 'long'],
                        ],
                    ],
                ],
            ];
            $this->elasticService->getClient()->cluster()->putSettings([
                'body' => [
                    'persistent' => [
                        'action.destructive_requires_name' => true,
                    ],
                ],
            ]);
            $response = $this->elasticService->getClient()->indices()->create($params);
        }

        $rec_candidate_assign = DB::table('rec_candidate_assign')
            ->join('job_applications', 'rec_candidate_assign.source_application_id', 'job_applications.id')
            ->leftjoin('jobs', 'jobs.id', 'rec_candidate_assign.source_jobid')
            ->leftjoin('master_status', 'master_status.id', 'job_applications.master_status_id')
            ->leftjoin('job_skills', 'jobs.id', 'job_skills.job_id')
            ->leftjoin('job_locations', 'job_locations.id', 'jobs.location_id')
            ->leftjoin('clients', 'clients.id', 'jobs.client_id')
            ->leftjoin('users', 'users.id', 'job_applications.submitted_by')
            ->leftjoin('companies', 'companies.id', 'users.company_id')
            ->leftjoin('qualification_master', 'qualification_master.id', 'job_applications.qualification')
            ->leftjoin('application_status', 'application_status.id', 'job_applications.status_id')
            ->leftJoin('countries', 'countries.id', '=', 'job_applications.country')
            ->leftjoin('state_list', 'state_list.id', 'job_applications.state')
            ->leftjoin('city_list', 'city_list.city_id', 'job_applications.city_id')
            ->leftjoin('bench_applications', 'bench_applications.id', 'rec_candidate_assign.source_from_id')
            ->select(
                'job_applications.*',
                'application_status.status as a_status',
                'jobs.title',
                'clients.name as client_name',
                'users.name as posted_by',
                'users.image as posted_by_image',
                'qualification_master.name as qualification_name',
                'master_status.name',
                'master_status.css_style',
                'state_list.state as state_name',
                'city_list.city as city_name',
                'countries.country_name',
                'bench_applications.id as bench_applications_id',
                'companies.company_name',
                'rec_candidate_assign.parent_id',
                'rec_candidate_assign.source_from_company_id',
                'rec_candidate_assign.source_from_user_id',
                'rec_candidate_assign.source_from_id',
                'rec_candidate_assign.source_from_type',
                'rec_candidate_assign.source_company_id',
                'rec_candidate_assign.source_user_id',
                'rec_candidate_assign.source_application_id',
                'rec_candidate_assign.source_jobid',
                'rec_candidate_assign.ac_status',
                'rec_candidate_assign.id as rec_candidate_assign_id',
                'rec_candidate_assign.created_at as created_at_rec',
                'rec_candidate_assign.updated_at as updated_at_rec'
            )
            ->get();

        $bulkData = [];
        $ik = 1;
        foreach ($rec_candidate_assign as $application) {

            $duplicate_candidate = DB::table('add_job_wise_condidates')
                ->where('job_application_id', $application->id)
                ->where('job_id', $application->source_jobid)
                ->count();

            $bulkData[] = [
                'index' => [
                    '_index' => ELASTIC_CANDIDATES_REQUEST,
                    '_id' => $application->rec_candidate_assign_id,
                ],
            ];

            $bulkData[] = [
                'id' => $application->rec_candidate_assign_id,
                'candidate_id' => $application->id,
                'full_name' => $application->full_name,
                'duplicate' => $application->duplicate,
                'company_id' => $application->company_id,
                'email' => $application->email,
                'phone' => $application->phone,
                'job_id' => $application->source_jobid,
                'skills' => $application->skills,
                'current_designation' => $application->current_designation,
                'total_exp' => $application->total_exp,
                'rel_exp' => $application->rel_exp,
                'pay_rate_currency' => $application->pay_rate_currency,
                'bill_rate_currency' => $application->bill_rate_currency,
                'pay_rate_amount' => $application->pay_rate_amount,
                'bill_rate_amount' => $application->bill_rate_amount,
                'pay_rate_mode' => $application->pay_rate_mode,
                'bill_rate_mode' => $application->bill_rate_mode,
                'outsourcing_type' => $application->outsourcing_type,
                'np_id' => $application->np_id,
                'immediate_join' => $application->np_id,
                'serving_notice_period_mode' => $application->serving_notice_period_mode,
                'comission_type' => $application->comission_type,
                'current_company' => $application->current_company,
                'country' => $application->country,
                'state' => $application->state,
                'city' => $application->city,
                'city_id' => $application->city_id,
                'last_active' => ($application->updated_at) ? Carbon::parse($application->updated_at)->toIso8601String() : null,
                'a_status' => $application->a_status,
                'title' => $application->title,
                'client_name' => $application->client_name,
                'posted_by' => $application->posted_by,
                'posted_by_image' => $application->posted_by_image,
                'duplicate_candidate' => $duplicate_candidate,
                'name' => $application->name,
                'css_style' => $application->css_style,
                'country_name' => $application->country_name,
                'state_name' => $application->state_name,
                'city_name' => $application->city_name,
                'bench_applications_id' => $application->bench_applications_id,
                'company_name' => $application->company_name,
                'parent_id' => $application->parent_id,
                'source_from_company_id' => $application->source_from_company_id,
                'source_from_user_id' => $application->source_from_user_id,
                'source_from_type' => $application->source_from_type,
                'source_company_id' => $application->source_company_id,
                'source_user_id' => $application->source_user_id,
                'source_application_id' => $application->source_application_id,
                'source_jobid' => $application->source_jobid,
                'ac_status' => $application->ac_status,
                'rec_candidate_assign_id' => $application->rec_candidate_assign_id,
                'updated_at' => ($application->updated_at_rec) ? Carbon::parse($application->updated_at_rec)->toIso8601String() : null,
                'created_at' => ($application->created_at_rec) ? Carbon::parse($application->created_at_rec)->toIso8601String() : null,
                'ctc' => $application->ctc,
                'ctc_hourly' => $application->ctc_hourly,
                'ctc_monthly' => $application->ctc_monthly,
                'ctc_yearly' => $application->ctc_yearly,
                'ectc_hourly' => $application->ectc_hourly,
                'ectc_monthly' => $application->ectc_monthly,
                'ectc_yearly' => $application->ectc_yearly,
                'current_ctc_cur' => $application->current_ctc_cur,
                'current_ctc_mode' => $application->current_ctc_mode,
                'ectc' => $application->ectc,
                'current_ectc_cur' => $application->current_ectc_cur,
                'current_ectc_mode' => $application->current_ectc_mode,
                'condidate_type' => $application->condidate_type,
                'phone_code' => $application->phone_code,
                'second_phone_code' => $application->second_phone_code,
                'secondary_mobile' => $application->secondary_mobile,
                'rating' => $application->rating,
                'skype_id' => $application->skype_id,
                'cover_letter' => $application->cover_letter,
                'passport' => $application->passport,
                'process_id' => $application->process_id,
                'secondrary_email' => $application->secondrary_email,
                'pincode' => $application->pincode,
                'comment' => $application->comment,
                'dob' => $application->dob,
                'status' => $application->status,
                'resume' => $application->resume,
                'qualification' => $application->qualification,
                'qualification_name' => $application->qualification_name,
                'percentage_hike' => $application->percentage_hike,
            ];
            $ik++;
        }
        $response = $this->elasticService->getClient()->bulk([
            'body' => $bulkData,
        ]);

        $searchQuery = $request->input('q');
        $page = max((int) $request->input('page', 1), 1);
        $size = (int) $request->input('size', 500); // Default 20 results per page
        $from = ($page - 1) * $size;
        $sort = [];
        $params = [
            'index' => ELASTIC_CANDIDATES_REQUEST,
            'body' => [
                'from' => $from,
                'size' => $size,
                'sort' => $sort,
            ],
        ];
        $response = $this->elasticService->getClient()->search($params);

        $total = $response['hits']['total']['value'] ?? 0;
        $data = array_map(function ($hit) {
            return $hit['_source'];
        }, $response['hits']['hits']);

    }

    public function benchPlacedIndexElastic(Request $request)
    {

        if (! $this->elasticService->indexExists(ELASTIC_BENCH_PLACED)) {
            $params = [
                'index' => ELASTIC_BENCH_PLACED,
                'body' => [
                    'settings' => [
                        'number_of_shards' => 1,
                        'number_of_replicas' => 0,
                        'index.blocks.read_only_allow_delete' => false,
                        'index.blocks.write' => false,
                        'index.blocks.read' => false,
                        'index.blocks.metadata' => false,
                        'index.lifecycle.name' => null,
                        'index.lifecycle.rollover_alias' => null,
                        'index.requests.cache.enable' => true,
                        'analysis' => [
                            'analyzer' => [
                                'comma_analyzer' => [
                                    'type' => 'custom',
                                    'tokenizer' => 'comma_tokenizer',
                                    'filter' => ['lowercase', 'trim'],
                                ],
                                'number_list_analyzer' => [
                                    'type' => 'custom',
                                    'tokenizer' => 'number_list_tokenizer',
                                    'filter' => ['trim'],
                                ],
                            ],
                            'tokenizer' => [
                                'comma_tokenizer' => [
                                    'type' => 'pattern',
                                    'pattern' => '\\s*,\\s*',
                                ],
                                'number_list_tokenizer' => [
                                    'type' => 'pattern',
                                    'pattern' => '\\s*,\\s*',
                                ],
                            ],
                            'filter' => [
                                'trim' => [
                                    'type' => 'trim',
                                ],
                            ],
                        ],
                    ],
                    'mappings' => [
                        'properties' => [
                            'id' => ['type' => 'integer'],
                            'source_comapny_id' => ['type' => 'integer'],
                            'bench_id' => ['type' => 'integer'],
                            'client_company_id' => ['type' => 'integer'],
                            'thirdparty_company_id' => ['type' => 'integer'],
                            'client_job_id' => ['type' => 'integer'],
                            'client_application_id' => ['type' => 'integer'],
                            'full_name' => [
                                'type' => 'text',
                                'fields' => ['keyword' => ['type' => 'keyword']],
                            ],
                            'current_designation' => [
                                'type' => 'text',
                                'fields' => ['keyword' => ['type' => 'keyword']],
                            ],
                            'email' => ['type' => 'keyword'],
                            'phone' => ['type' => 'keyword'],
                            'total_exp' => ['type' => 'float'],
                            'rel_exp' => ['type' => 'float'],
                            'pay_rate_currency' => ['type' => 'keyword'],
                            'pay_rate_amount' => ['type' => 'keyword'],
                            'pay_rate_mode' => ['type' => 'keyword'],
                            'bill_rate_currency' => ['type' => 'keyword'],
                            'bill_rate_amount' => ['type' => 'double'],
                            'bill_rate_mode' => ['type' => 'keyword'],
                            'job_title' => [
                                'type' => 'text',
                                'fields' => ['keyword' => ['type' => 'keyword']],
                            ],
                            'company_name' => [
                                'type' => 'text',
                                'fields' => ['keyword' => ['type' => 'keyword']],
                            ],
                            'qualification' => [
                                'type' => 'text',
                                'fields' => ['keyword' => ['type' => 'keyword']],
                            ],
                            'skills' => [
                                'type' => 'text',
                                'fields' => ['keyword' => ['type' => 'keyword']],
                            ],
                            'country' => ['type' => 'keyword'],
                            'state' => ['type' => 'keyword'],
                            'city' => ['type' => 'keyword'],
                            'assign_to' => [
                                'type' => 'text',
                                'fields' => ['keyword' => ['type' => 'keyword']],
                            ],
                            'master_status_name' => [
                                'type' => 'text',
                                'fields' => ['keyword' => ['type' => 'keyword']],
                            ],
                            'css_style' => ['type' => 'text'],
                            'placed_end_date' => ['type' => 'date'],
                            'offeramount' => ['type' => 'double'],
                            'joineddate' => ['type' => 'date'],
                            'application_stage' => ['type' => 'keyword'],
                            'application_child_stage' => ['type' => 'keyword'],
                            'bench_status' => ['type' => 'keyword'],
                            'create_at' => ['type' => 'date'],
                            'offercurrency' => ['type' => 'integer'],
                            'offermode' => ['type' => 'integer'],
                            'placedID' => ['type' => 'long'],
                            'jobID' => ['type' => 'long'],
                            'bench_place_id' => ['type' => 'long'],
                            'employment_type' => ['type' => 'integer'],
                            'company_id' => ['type' => 'long'],
                            'comId' => ['type' => 'long'],
                        ],
                    ],
                ],
            ];

            $this->elasticService->getClient()->cluster()->putSettings([
                'body' => [
                    'persistent' => [
                        'action.destructive_requires_name' => true,
                    ],
                ],
            ]);

            $response = $this->elasticService->getClient()->indices()->create($params);
        }

        $placedApplications = DB::table('job_applications')
            ->select('bench_place.*',
                'bench_master_status.name as master_status_name',
                'bench_master_status.css_style',
                'offer_job_applications.offer_amount as offeramount',
                'offer_job_applications.joined_date as joineddate',
                'jobs.company_id as comId',
                'job_applications.company_id',
                'job_applications.np_id',
                'job_applications.serving_notice_period_mode',
                'job_applications.thirdparty_company_id',
                'jobs.employment_type as employment_type',
                'add_job_wise_condidates.id as placedID',
                'add_job_wise_condidates.job_id as jobID',
                'offer_job_applications.offer_currency as offercurrency',
                'offer_job_applications.offer_mode as offermode'
            );
        $placedApplications = $placedApplications->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id')
            ->join('master_status', function ($join) {
                $join->on('master_status.id', 'add_job_wise_condidates.master_status_id');
            });
        $placedApplications = $placedApplications->join('jobs', function ($join) {
            $join->on('jobs.id', 'add_job_wise_condidates.job_id');
        });
        $placedApplications = $placedApplications->join('companies', function ($join) {
            $join->on('companies.id', 'add_job_wise_condidates.company_id');
        })
            ->join('offer_job_applications', function ($join) {
                $join->on('offer_job_applications.joined', 'add_job_wise_condidates.id');
            });
        $placedApplications = $placedApplications->join('bench_master_status', 'bench_master_status.id', 'job_applications.status');
        $placedApplications = $placedApplications->join('bench_place', 'bench_place.bench_id', 'job_applications.id');

        $placedApplications = $placedApplications->leftjoin('qualification_master', 'qualification_master.id', 'job_applications.qualification');

        $placedApplications = $placedApplications->where('job_applications.condidate_type', '=', 'bench_condidate');
        $placedApplications = $placedApplications->groupBy('bench_place.id')->get();

        foreach ($placedApplications as $row) {
            $bulkData[] = [
                'index' => [
                    '_index' => ELASTIC_BENCH_PLACED,
                    '_id' => $row->id,
                ],
            ];

            $bulkData[] = [
                'id' => @$row->id,
                'source_comapny_id' => @$row->source_comapny_id,
                'bench_id' => @$row->bench_id,
                'client_company_id' => @$row->client_company_id,
                'client_job_id' => @$row->client_job_id,
                'thirdparty_company_id' => @$row->thirdparty_company_id,
                'client_application_id' => @$row->client_application_id,
                'full_name' => @$row->full_name ? strtolower($row->full_name) : null,
                'np_id' => $row->np_id,
                'immediate_join' => $row->np_id,
                'serving_notice_period_mode' => $row->serving_notice_period_mode,
                'current_designation' => @$row->current_designation,
                'email' => @$row->email ? strtolower($row->email) : null,
                'phone' => @$row->phone,
                'total_exp' => @$row->total_exp,
                'rel_exp' => @$row->rel_exp,
                'pay_rate_currency' => @$row->pay_rate_currency,
                'pay_rate_amount' => @$row->pay_rate_amount,
                'pay_rate_mode' => @$row->pay_rate_mode,
                'bill_rate_currency' => @$row->bill_rate_currency,
                'bill_rate_amount' => @$row->bill_rate_amount,
                'bill_rate_mode' => @$row->bill_rate_mode,
                'job_title' => @$row->job_title ? strtolower($row->job_title) : null,
                'company_name' => @$row->company_name ? strtolower($row->company_name) : null,
                'qualification' => @$row->qualification ? strtolower($row->qualification) : null,
                'skills' => @$row->skills ? strtolower($row->skills) : null,
                'country' => ($row->country) ? strtolower($row->country) : null,
                'state' => ($row->state) ? strtolower($row->state) : null,
                'city' => ($row->city) ? strtolower($row->city) : null,
                'assign_to' => @$row->assign_to,
                'placed_end_date' => ($row->placed_end_date) ? Carbon::parse($row->placed_end_date)->toIso8601String() : null,
                'offeramount' => @$row->offeramount,
                'joineddate' => ($row->joineddate) ? Carbon::parse($row->joineddate)->toIso8601String() : null,
                'application_stage' => @$row->application_stage,
                'application_child_stage' => @$row->application_child_stage,
                'bench_status' => @$row->bench_status ? strtolower($row->bench_status) : null,
                'created_at' => ($row->create_at) ? Carbon::parse($row->create_at)->toIso8601String() : null,
                'offercurrency' => @$row->offercurrency,
                'offermode' => @$row->offermode,
                'jobID' => @$row->jobID,
                'placedID' => @$row->placedID,
                'bench_place_id' => @$row->id,
                'comId' => @$row->comId,
                'company_id' => @$row->company_id,
                'employment_type' => @$row->employment_type,
                'css_style' => @$row->css_style,
                'master_status_name' => @$row->master_status_name,
            ];
        }

        $response = $this->elasticService->getClient()->bulk([
            'body' => $bulkData,
        ]);

        $searchQuery = $request->input('q');
        $page = max((int) $request->input('page', 1), 1);
        $size = (int) $request->input('size', 500); // Default 20 results per page
        $from = ($page - 1) * $size;
        $sort = [];
        $params = [
            'index' => ELASTIC_BENCH_PLACED,
            'body' => [
                'from' => $from,
                'size' => $size,
                'sort' => $sort,
            ],
        ];
        $response = $this->elasticService->getClient()->search($params);

        $total = $response['hits']['total']['value'] ?? 0;
        $data = array_map(function ($hit) {
            return $hit['_source'];
        }, $response['hits']['hits']);

    }

    public function usersIndexElastic(Request $request)
    {

        if (! $this->elasticService->indexExists(ELASTIC_USERS_LIST)) {
            $params = [
                'index' => ELASTIC_USERS_LIST,
                'body' => [
                    'settings' => [
                        'number_of_shards' => 1,
                        'number_of_replicas' => 0,
                        'index.blocks.read_only_allow_delete' => false,
                        'index.blocks.write' => false,
                        'index.blocks.read' => false,
                        'index.blocks.metadata' => false,
                        'index.lifecycle.name' => null,
                        'index.lifecycle.rollover_alias' => null,
                        'index.requests.cache.enable' => true,
                        'analysis' => [
                            'analyzer' => [
                                'comma_analyzer' => [
                                    'type' => 'custom',
                                    'tokenizer' => 'comma_tokenizer',
                                    'filter' => ['lowercase', 'trim'],
                                ],
                                'number_list_analyzer' => [
                                    'type' => 'custom',
                                    'tokenizer' => 'number_list_tokenizer',
                                    'filter' => ['trim'],
                                ],
                            ],
                            'tokenizer' => [
                                'comma_tokenizer' => [
                                    'type' => 'pattern',
                                    'pattern' => '\\s*,\\s*',
                                ],
                                'number_list_tokenizer' => [
                                    'type' => 'pattern',
                                    'pattern' => '\\s*,\\s*',
                                ],
                            ],
                            'filter' => [
                                'trim' => [
                                    'type' => 'trim',
                                ],
                            ],
                        ],
                    ],
                    'mappings' => [
                        'properties' => [
                            'company_id' => ['type' => 'long'],
                            'company_name' => ['type' => 'text'],
                            'root_user' => ['type' => 'long'],
                            'name' => ['type' => 'text'],
                            'email' => ['type' => 'keyword'],
                            'calling_code' => ['type' => 'text'],
                            'mobile' => ['type' => 'keyword'],
                            'mobile_verified' => ['type' => 'integer'],
                            'password' => ['type' => 'text'],
                            'remember_token' => ['type' => 'text'],
                            'created_at' => ['type' => 'date'],
                            'updated_at' => ['type' => 'date'],
                            'image' => ['type' => 'text'],
                            'is_superadmin' => ['type' => 'long'],
                            'email_verification_code' => ['type' => 'text'],
                            'status' => ['type' => 'keyword'],
                            'working_status' => ['type' => 'integer'],
                            'type' => ['type' => 'integer'],
                            'active_session' => ['type' => 'text'],
                            'session_time' => ['type' => 'integer'],
                            'last_login' => ['type' => 'date'],
                            'auto_date_team' => ['type' => 'date'],
                            'session_id' => ['type' => 'text'],
                            'super_password' => ['type' => 'text'],
                            'super_admin_login_code' => ['type' => 'text'],
                            'role_name' => ['type' => 'text'],
                            'display_name' => ['type' => 'text'],
                            'designation' => ['type' => 'text'],
                            'country_name' => ['type' => 'text'],
                            'state_name' => ['type' => 'text'],
                            'city_name' => ['type' => 'text'],
                            'candidate_relation' => [
                                'type' => 'nested',
                                'properties' => [
                                    'id' => ['type' => 'long'],
                                    'application_id' => ['type' => 'long'],
                                    'master_process_name' => ['type' => 'keyword'],
                                    'job_id' => ['type' => 'long'],
                                    'created_at' => ['type' => 'date'],
                                    'updated_at' => ['type' => 'date'],
                                ],
                            ],
                        ],
                    ],
                ],
            ];
            $this->elasticService->getClient()->cluster()->putSettings([
                'body' => [
                    'persistent' => [
                        'action.destructive_requires_name' => true,
                    ],
                ],
            ]);

            $response = $this->elasticService->getClient()->indices()->create($params);
        }

        $teams = tenant_table('users')
            ->leftjoin('role_user', 'role_user.user_id', 'users.id')
            ->leftjoin('roles', 'roles.id', 'role_user.role_id')
            ->leftJoin('users_profile', 'users_profile.user_id', '=', 'users.id')
            ->join('companies', 'companies.id', '=', 'users.company_id')
            ->select(
                'users.*',
                'users_profile.id as user_pr_id',
                'users_profile.designation',
                'roles.display_name',
                'roles.name as role_name',
                'companies.company_name'
            )
            ->groupBy('users.id')
            ->get();

        foreach ($teams as $row) {
            $country_name = '';
            $state_name = '';
            $city_name = '';
            $users_profile = DB::table('users_profile')
                ->leftJoin('countries', 'countries.id', '=', 'users_profile.country')
                ->leftJoin('state_list', 'state_list.id', '=', 'users_profile.state')
                ->leftJoin('city_list', 'city_list.city_id', '=', 'users_profile.city_id')
                ->select('state_list.state as state_name', 'city_list.city as city_name', 'countries.country_name')
                ->where('users_profile.id', '=', $row->user_pr_id)
                ->first();
            if ($users_profile) {
                $country_name = $users_profile->country_name;
                $state_name = $users_profile->state_name;
                $city_name = $users_profile->city_name;
            }

            $candidate_relation = DB::table('updatetimeline')
                ->select('master_process_name', 'job_id', 'application_id', 'create_at', 'update_at')
                ->where('event_by_user_id', $row->id)
                ->whereIn('master_process_name', ['Sourcing', 'Joined'])
                ->get()
                ->map(function ($s) {
                    return [
                        'master_process_name' => $s->master_process_name,
                        'application_id' => (int) $s->application_id,
                        'job_id' => (int) $s->job_id,
                        'created_at' => ($s->create_at) ? Carbon::parse($s->create_at)->toIso8601String() : null,
                        'updated_at' => ($s->update_at) ? Carbon::parse($s->update_at)->toIso8601String() : null,
                    ];
                })
                ->toArray();

            $bulkData[] = [
                'index' => [
                    '_index' => ELASTIC_USERS_LIST,
                    '_id' => $row->id,
                ],
            ];

            $bulkData[] = [
                'id' => @$row->id,
                'company_id' => @$row->company_id,
                'company_name' => strtolower(@$row->company_name),
                'root_user' => @$row->root_user,
                'name' => strtolower(@$row->name),
                'email' => @$row->email ? strtolower($row->email) : null,
                'calling_code' => @$row->calling_code,
                'mobile' => @$row->mobile,
                'mobile_verified' => @$row->mobile_verified,
                'password' => @$row->password,
                'remember_token' => @$row->remember_token,
                'updated_at' => ($row->updated_at) ? Carbon::parse($row->updated_at)->toIso8601String() : null,
                'created_at' => ($row->created_at) ? Carbon::parse($row->created_at)->toIso8601String() : null,
                'image' => @$row->image,
                'is_superadmin' => @$row->is_superadmin,
                'email_verification_code' => @$row->email_verification_code,
                'status' => @$row->status,
                'working_status' => @$row->working_status,
                'type' => @$row->type,
                'active_session' => @$row->active_session,
                'session_time' => @$row->session_time,
                'last_login' => ($row->last_login) ? Carbon::parse($row->last_login)->toIso8601String() : null,
                'auto_date_team' => ($row->auto_date_team) ? Carbon::parse($row->auto_date_team)->toIso8601String() : null,
                'session_id' => @$row->session_id,
                'super_password' => @$row->super_password,
                'super_admin_login_code' => @$row->super_admin_login_code,
                'display_name' => ($row->display_name) ? strtolower($row->display_name) : null,
                'role_name' => ($row->role_name) ? strtolower($row->role_name) : null,
                'designation' => ($row->designation) ? strtolower($row->designation) : null,
                'country_name' => ($country_name) ? strtolower($country_name) : null,
                'state_name' => ($state_name) ? strtolower($state_name) : null,
                'city_name' => ($city_name) ? strtolower($city_name) : null,
                'candidate_relation' => $candidate_relation ?? [],
            ];
        }
        $response = $this->elasticService->getClient()->bulk([
            'body' => $bulkData,
        ]);

        $searchQuery = $request->input('q');
        $page = max((int) $request->input('page', 1), 1);
        $size = (int) $request->input('size', 500); // Default 20 results per page
        $from = ($page - 1) * $size;
        $sort = [];
        $params = [
            'index' => ELASTIC_USERS_LIST,
            'body' => [
                'from' => $from,
                'size' => $size,
                'sort' => $sort,
            ],
        ];
        $response = $this->elasticService->getClient()->search($params);

        $total = $response['hits']['total']['value'] ?? 0;
        $data = array_map(function ($hit) {
            return $hit['_source'];
        }, $response['hits']['hits']);

    }

    public function clientListIndexElastic(Request $request)
    {

        // $mapping = $this->elasticService->getClient()->indices()->getMapping([
        //     'index' => ELASTIC_CLIENT_LIST
        //      ]);
        // dd($mapping);
        //  GeneralMethod::clientStaffingPartner(63205);

        if (! $this->elasticService->indexExists(ELASTIC_CLIENT_LIST)) {
            $params = [
                'index' => ELASTIC_CLIENT_LIST,
                'body' => [
                    'settings' => [
                        'number_of_shards' => 1,
                        'number_of_replicas' => 0,
                        'index.blocks.read_only_allow_delete' => false,
                        'index.blocks.write' => false,
                        'index.blocks.read' => false,
                        'index.blocks.metadata' => false,
                        'index.lifecycle.name' => null,
                        'index.lifecycle.rollover_alias' => null,
                        'index.requests.cache.enable' => true,
                        'analysis' => [
                            'analyzer' => [
                                'comma_analyzer' => [
                                    'type' => 'custom',
                                    'tokenizer' => 'comma_tokenizer',
                                    'filter' => ['lowercase', 'trim'],
                                ],
                                'number_list_analyzer' => [
                                    'type' => 'custom',
                                    'tokenizer' => 'number_list_tokenizer',
                                    'filter' => ['trim'],
                                ],
                            ],
                            'tokenizer' => [
                                'comma_tokenizer' => [
                                    'type' => 'pattern',
                                    'pattern' => '\\s*,\\s*',
                                ],
                                'number_list_tokenizer' => [
                                    'type' => 'pattern',
                                    'pattern' => '\\s*,\\s*',
                                ],
                            ],
                            'filter' => [
                                'trim' => [
                                    'type' => 'trim',
                                ],
                            ],
                        ],
                    ],
                    'mappings' => [
                        'properties' => [
                            'company_id' => ['type' => 'long'],
                            'company_name' => ['type' => 'text'],
                            'industries' => ['type' => 'text'],
                            'logo' => ['type' => 'text'],
                            'company_type' => ['type' => 'integer'],
                            'client_id' => ['type' => 'long'],
                            'tags' => ['type' => 'text'],
                            'created_at' => ['type' => 'date'],
                            'name' => ['type' => 'text'],
                            'email' => ['type' => 'keyword'],
                            'contact_number' => ['type' => 'keyword'],
                            'logo_logical_path' => ['type' => 'text'],
                            'industry' => ['type' => 'text'],
                            'clients_company_id' => ['type' => 'long'],
                            'ind_name' => ['type' => 'text'],
                            'main_ind_name' => ['type' => 'text'],
                            'staffing_company_relation_id' => ['type' => 'long'],
                            'from_company_id' => ['type' => 'long'],
                            'to_company_id' => ['type' => 'long'],
                            'to_created_at' => ['type' => 'date'],
                            'from_created_at' => ['type' => 'date'],
                            'staffing_company_relation' => [
                                'type' => 'nested',
                                'properties' => [
                                    'id' => ['type' => 'long'],
                                    'from_company_id' => ['type' => 'long'],
                                    'from_client_id' => ['type' => 'long'],
                                    'from_user_id' => ['type' => 'long'],
                                    'from_status' => ['type' => 'long'],
                                    'from_created_at' => ['type' => 'date'],
                                    'to_company_id' => ['type' => 'long'],
                                    'to_client_id' => ['type' => 'long'],
                                    'to_user_id' => ['type' => 'long'],
                                    'to_status' => ['type' => 'long'],
                                    'to_created_at' => ['type' => 'date'],
                                    'final_status' => ['type' => 'long'],
                                    'created_at' => ['type' => 'date'],
                                    'updated_at' => ['type' => 'date'],
                                ],
                            ],
                            'staffing_company' => [
                                'type' => 'nested',
                                'properties' => [
                                    'id' => ['type' => 'long'],
                                    'from_company_id' => ['type' => 'long'],
                                    'from_client_id' => ['type' => 'long'],
                                    'from_user_id' => ['type' => 'long'],
                                    'from_status' => ['type' => 'long'],
                                    'from_created_at' => ['type' => 'date'],
                                    'to_company_id' => ['type' => 'long'],
                                    'to_client_id' => ['type' => 'long'],
                                    'to_user_id' => ['type' => 'long'],
                                    'to_status' => ['type' => 'long'],
                                    'to_created_at' => ['type' => 'date'],
                                    'final_status' => ['type' => 'long'],
                                    'created_at' => ['type' => 'date'],
                                    'updated_at' => ['type' => 'date'],
                                ],
                            ],
                            'client_location' => [
                                'type' => 'nested',
                                'properties' => [
                                    'id' => ['type' => 'long'],
                                    'company_id' => ['type' => 'long'],
                                    'client_id' => ['type' => 'long'],
                                    'city_id' => ['type' => 'long'],
                                    'country_id' => ['type' => 'long'],
                                    'state' => ['type' => 'long'],
                                    'city_name' => ['type' => 'keyword'],
                                    'state_name' => ['type' => 'keyword'],
                                    'country_name' => ['type' => 'keyword'],
                                    'address' => ['type' => 'text', 'fields' => [
                                        'keyword' => [
                                            'type' => 'keyword',
                                            'ignore_above' => 256,
                                        ],
                                    ]],
                                ],
                            ],
                            'client_contact_info' => [
                                'type' => 'nested',
                                'properties' => [
                                    'id' => ['type' => 'long'],
                                    'client_id' => ['type' => 'long'],
                                    'contact_person_email' => ['type' => 'keyword'],
                                    'calling_code' => ['type' => 'text'],
                                    'contact_person_number' => ['type' => 'keyword'],
                                    'contact_person_designation' => ['type' => 'text'],
                                    'contact_person_name' => ['type' => 'text', 'fields' => [
                                        'keyword' => [
                                            'type' => 'keyword',
                                            'ignore_above' => 256,
                                        ],
                                    ]],
                                    'created_at' => ['type' => 'date'],
                                    'updated_at' => ['type' => 'date'],
                                ],
                            ],
                            'jobs' => [
                                'type' => 'nested',
                                'properties' => [
                                    'id' => ['type' => 'long'],
                                    'title' => ['type' => 'text'],
                                    'employment_type' => ['type' => 'keyword'],
                                    'status' => ['type' => 'keyword'],
                                    'skills' => [
                                        'type' => 'nested',
                                        'properties' => [
                                            'skill_name' => ['type' => 'keyword'],
                                            'skill_id' => ['type' => 'keyword'],
                                        ],
                                    ],
                                ],
                            ],
                            'candidate_relation' => [
                                'type' => 'nested',
                                'properties' => [
                                    'id' => ['type' => 'long'],
                                    'application_id' => ['type' => 'long'],
                                    'master_process_name' => ['type' => 'keyword'],
                                    'job_id' => ['type' => 'long'],
                                    'created_at' => ['type' => 'date'],
                                    'updated_at' => ['type' => 'date'],
                                ],
                            ],
                            // Add more top-level fields as needed
                        ],
                    ],
                ],
            ];

            $this->elasticService->getClient()->cluster()->putSettings([
                'body' => [
                    'persistent' => [
                        'action.destructive_requires_name' => true,
                    ],
                ],
            ]);
            $response = $this->elasticService->getClient()->indices()->create($params);
        }

        // $params = [
        //     'index' => ELASTIC_CLIENT_LIST,
        //     'body' => [
        //         'properties' => [
        //             'company_sizes' => ['type' => 'long'],
        //             'founded' => ['type' => 'long']
        //             ]
        //         ]
        // ];
        // $response = $this->elasticService->getClient()->indices()->putMapping($params);
        // dd("dkd");

        //  $coin_events = DB::table('clients')->get();
        //    $ky = 0;
        //     foreach ($coin_events as $row) {

        //         $response = $this->elasticService->getClient()->update([
        //                 'index' => ELASTIC_CLIENT_LIST,
        //                 'id'    => $row->id,
        // 				'retry_on_conflict' => 5,
        //                 'body'  => [
        //                     'doc' => [
        // 						'id' =>  $row->id,
        // 						'website' => $row->website,
        // 						'company_sizes' => $row->company_sizes,
        // 						'founded' => $row->founded
        // 					],
        // 					'doc_as_upsert' => true,
        // 					'detect_noop' => false
        // 				],
        //             ]);
        //         $ky++;
        //     }
        //     dd($ky);

        $clients = DB::table('companies')
            ->select(
                'companies.id',
                'companies.company_name',
                'companies.industries',
                'companies.industries_type',
                'companies.logo',
                'companies.company_type',
                'companies.description as companies_description',
                'clients.id as client_id',
                'clients.tags',
                'clients.description',
                'clients.self_company',
                'clients.created_at',
                'td_industries.ind_name',
                'mainCompany.ind_name as main_ind_name',
                'staffing_company_relation.id as staffing_company_relation_id',
                'staffing_company_relation.from_company_id',
                'staffing_company_relation.to_company_id',
                'staffing_company_relation.to_created_at',
                'staffing_company_relation.from_created_at',
                'clients.website',
                'clients.name',
                'clients.founded',
                'clients.company_sizes',
                'clients.email',
                'clients.contact_number',
                'clients.logo_logical_path',
                'clients.industry',
                'clients.company_id as clients_company_id',
            )
            ->leftJoin('staffing_company_relation', 'staffing_company_relation.from_company_id', '=', 'companies.id')
            ->join('clients', 'clients.company_id', '=', 'companies.id')
            ->leftJoin('client_location', function ($join) {
                $join->on('client_location.client_id', '=', 'clients.id');
            })
            ->leftJoin('client_contact_info', function ($join) {
                $join->on('client_contact_info.client_id', '=', 'clients.id');

            })
            ->leftJoin('td_industries as mainCompany', 'mainCompany.ind_id', '=', 'companies.industries')
            ->leftJoin('td_industries', 'td_industries.ind_id', '=', 'clients.industry')
        // ->where('companies.id',163)
            ->groupBy('clients.id')
        // ->limit(2)
            ->cursor();
        // dd($clients);
        foreach ($clients as $row) {

            // dd($row);

            $staffing_company_relation = DB::table('staffing_company_relation')
                ->where('staffing_company_relation.from_company_id', $row->id)
                ->get()
                ->map(function ($s) {
                    return [
                        'id' => (int) $s->id,
                        'from_company_id' => (int) $s->from_company_id,
                        'from_client_id' => (int) $s->from_client_id,
                        'from_user_id' => (int) $s->from_user_id,
                        'from_status' => (int) $s->from_status,
                        'from_created_at' => ($s->from_created_at) ? Carbon::parse($s->from_created_at)->toIso8601String() : null,
                        'to_company_id' => (int) $s->to_company_id,
                        'to_client_id' => (int) $s->to_client_id,
                        'to_user_id' => (int) $s->to_user_id,
                        'to_status' => (int) $s->to_status,
                        'to_created_at' => ($s->to_created_at) ? Carbon::parse($s->to_created_at)->toIso8601String() : null,
                        'final_status' => (int) $s->final_status,
                        'created_at' => ($s->created_at) ? Carbon::parse($s->created_at)->toIso8601String() : null,
                        'updated_at' => ($s->updated_at) ? Carbon::parse($s->updated_at)->toIso8601String() : null,
                    ];
                })
                ->toArray();
            $staffing_company = DB::table('staffing_company_relation')
                ->where('staffing_company_relation.to_company_id', $row->id)
                ->get()
                ->map(function ($s) {
                    return [
                        'id' => (int) $s->id,
                        'from_company_id' => (int) $s->from_company_id,
                        'from_client_id' => (int) $s->from_client_id,
                        'from_user_id' => (int) $s->from_user_id,
                        'from_status' => (int) $s->from_status,
                        'from_created_at' => ($s->from_created_at) ? Carbon::parse($s->from_created_at)->toIso8601String() : null,
                        'to_company_id' => (int) $s->to_company_id,
                        'to_client_id' => (int) $s->to_client_id,
                        'to_user_id' => (int) $s->to_user_id,
                        'to_status' => (int) $s->to_status,
                        'to_created_at' => ($s->to_created_at) ? Carbon::parse($s->to_created_at)->toIso8601String() : null,
                        'final_status' => (int) $s->final_status,
                        'created_at' => ($s->created_at) ? Carbon::parse($s->created_at)->toIso8601String() : null,
                        'updated_at' => ($s->updated_at) ? Carbon::parse($s->updated_at)->toIso8601String() : null,
                    ];
                })
                ->toArray();

            $client_location = DB::table('client_location')
                ->leftjoin('countries', 'countries.id', 'client_location.country_id')
                ->leftjoin('state_list', 'state_list.id', 'client_location.state')
                ->leftjoin('city_list', 'city_list.city_id', 'client_location.city_id')
                ->select('client_location.*', 'state_list.state as state_name', 'city_list.city as city_name', 'countries.country_name')
                ->where('client_location.client_id', $row->client_id)
                ->get()
                ->map(function ($s) {
                    return [
                        'id' => (int) $s->id,
                        'company_id' => (int) $s->company_id,
                        'client_id' => (int) $s->client_id,
                        'city_id' => (int) $s->city_id,
                        'country_id' => (int) $s->country_id,
                        'state' => (int) $s->state,
                        'country_name' => strtolower($s->country_name),
                        'state_name' => strtolower($s->state_name),
                        'city_name' => strtolower($s->city_name),
                        'address' => strtolower($s->address),
                    ];
                })
                ->toArray();
            $client_contact_info = DB::table('client_contact_info')
                ->select('client_contact_info.*')
                ->where('client_contact_info.client_id', $row->client_id)
                ->get()
                ->map(function ($s) {
                    return [
                        'id' => (int) $s->id,
                        'client_id' => (int) $s->client_id,
                        'contact_person_email' => $s->contact_person_email,
                        'calling_code' => $s->calling_code,
                        'contact_person_number' => $s->contact_person_number,
                        'contact_person_designation' => $s->contact_person_designation,
                        'contact_person_name' => strtolower($s->contact_person_name),
                        'created_at' => ($s->created_at) ? Carbon::parse($s->created_at)->toIso8601String() : null,
                        'updated_at' => ($s->updated_at) ? Carbon::parse($s->updated_at)->toIso8601String() : null,
                    ];
                })
                ->toArray();

            $candidate_relation = DB::table('updatetimeline')
                ->select('master_process_name', 'job_id', 'application_id', 'create_at', 'update_at')
                ->where('event_by_company_id', $row->id)
                ->whereIn('master_process_name', ['Sourcing', 'Joined'])
                ->get()
                ->map(function ($s) {
                    return [
                        'master_process_name' => $s->master_process_name,
                        'application_id' => (int) $s->application_id,
                        'job_id' => (int) $s->job_id,
                        'created_at' => ($s->create_at) ? Carbon::parse($s->create_at)->toIso8601String() : null,
                        'updated_at' => ($s->update_at) ? Carbon::parse($s->update_at)->toIso8601String() : null,
                    ];
                })
                ->toArray();

            // dd($client_contact_info);

            $jobs = [];

            $bulkData[] = [
                'index' => [
                    '_index' => ELASTIC_CLIENT_LIST,
                    '_id' => $row->client_id,
                ],
            ];

            $bulkData[] = [
                'id' => $row->client_id,
                'company_id' => $row->id,
                'company_name' => strtolower($row->company_name),
                'industries' => $row->industries,
                'website' => $row->website,
                'logo' => $row->logo,
                'company_type' => $row->company_type,
                'industries_type' => $row->industries_type,
                'client_id' => $row->client_id,
                'tags' => $row->tags,
                'created_at' => ($row->created_at) ? Carbon::parse($row->created_at)->toIso8601String() : null,
                'self_company' => $row->self_company,
                'name' => $row->name,
                'email' => $row->email,
                'founded' => $row->founded,
                'company_sizes' => $row->company_sizes,
                'contact_number' => $row->contact_number,
                'companies_description' => $row->companies_description,
                'description' => $row->description,
                'logo_logical_path' => $row->logo_logical_path,
                'industry' => $row->industry,
                'clients_company_id' => $row->clients_company_id,
                'ind_name' => $row->ind_name,
                'main_ind_name' => $row->main_ind_name,
                'staffing_company_relation_id' => $row->staffing_company_relation_id,
                'from_company_id' => $row->from_company_id,
                'to_company_id' => $row->to_company_id,
                'to_created_at' => ($row->to_created_at) ? Carbon::parse($row->to_created_at)->toIso8601String() : null,
                'from_created_at' => ($row->from_created_at) ? Carbon::parse($row->from_created_at)->toIso8601String() : null,
                'staffing_company_relation' => $staffing_company_relation ?? [],
                'staffing_company' => $staffing_company ?? [],
                'client_location' => $client_location ?? [],
                'client_contact_info' => $client_contact_info ?? [],
                'jobs' => $jobs ?? [],
                'candidate_relation' => $candidate_relation ?? [],
            ];

        }
        $response = $this->elasticService->getClient()->bulk([
            'body' => $bulkData,
        ]);

        $searchQuery = $request->input('q');
        $page = max((int) $request->input('page', 1), 1);
        $size = (int) $request->input('size', 500); // Default 20 results per page
        $from = ($page - 1) * $size;
        $sort = [];
        $params = [
            'index' => ELASTIC_CLIENT_LIST,
            'body' => [
                'from' => $from,
                'size' => $size,
                'sort' => $sort,
            ],
        ];
        $response = $this->elasticService->getClient()->search($params);

        $total = $response['hits']['total']['value'] ?? 0;
        $data = array_map(function ($hit) {
            return $hit['_source'];
        }, $response['hits']['hits']);

        // dd( $clients);

    }

    public function interviewTrackerIndexElastic(Request $request)
    {

        if (! $this->elasticService->indexExists(ELASTIC_INERTVIEW_TRACKER)) {
            $params = [
                'index' => ELASTIC_INERTVIEW_TRACKER,
                'body' => [
                    'settings' => [
                        'number_of_shards' => 1,
                        'number_of_replicas' => 0,
                        'index.blocks.read_only_allow_delete' => false,
                        'index.blocks.write' => false,
                        'index.blocks.read' => false,
                        'index.blocks.metadata' => false,
                        'index.lifecycle.name' => null,
                        'index.lifecycle.rollover_alias' => null,
                        'index.requests.cache.enable' => true,
                        'analysis' => [
                            'analyzer' => [
                                'comma_analyzer' => [
                                    'type' => 'custom',
                                    'tokenizer' => 'comma_tokenizer',
                                    'filter' => ['lowercase', 'trim'],
                                ],
                                'number_list_analyzer' => [
                                    'type' => 'custom',
                                    'tokenizer' => 'number_list_tokenizer',
                                    'filter' => ['trim'],
                                ],
                            ],
                            'tokenizer' => [
                                'comma_tokenizer' => [
                                    'type' => 'pattern',
                                    'pattern' => '\\s*,\\s*',
                                ],
                                'number_list_tokenizer' => [
                                    'type' => 'pattern',
                                    'pattern' => '\\s*,\\s*',
                                ],
                            ],
                            'filter' => [
                                'trim' => [
                                    'type' => 'trim',
                                ],
                            ],
                        ],
                    ],
                    'mappings' => [
                        'properties' => [
                            'full_name' => ['type' => 'text'],
                            'email' => ['type' => 'keyword'],
                            'phone' => ['type' => 'keyword'],
                            'posted_by' => ['type' => 'keyword'],
                            'state_name' => ['type' => 'keyword'],
                            'city_name' => ['type' => 'keyword'],
                            'country_name' => ['type' => 'keyword'],
                            'status' => ['type' => 'keyword'],
                            'candidate_type' => ['type' => 'keyword'],
                            'schedule_date' => ['type' => 'date'],
                            'master_status_id' => ['type' => 'integer'],
                            'child_status_id' => ['type' => 'integer'],
                            'client_name' => ['type' => 'text'],
                            'available_status' => ['type' => 'long'],
                            'reschedule' => ['type' => 'date'],
                            'feedback' => ['type' => 'long'],
                            'interview_mode' => ['type' => 'integer'],
                            'interviewers' => ['type' => 'text'],
                            'interview_type' => ['type' => 'long'],
                            'schedule_from_time' => ['type' => 'text'],
                            'real_company_id' => ['type' => 'long'],
                            'mailstatus' => ['type' => 'integer'],
                            'timezone_abbreviation' => ['type' => 'text'],
                            'schedule_to_time' => ['type' => 'text'],
                            'source' => ['type' => 'text'],
                            'posted_by_image' => ['type' => 'text'],
                            'job_application_id' => ['type' => 'long'],
                            'master_status_name' => ['type' => 'text'],
                            'child_status_name' => ['type' => 'text'],
                            'client_id' => ['type' => 'long'],
                            'job_title' => ['type' => 'text'],
                            'job_id' => ['type' => 'long'],
                            'job_company_id' => ['type' => 'long'],
                            'company_name' => ['type' => 'text'],
                            'current_designation' => ['type' => 'text'],
                            'thirdparty_company_id' => ['type' => 'long'],
                            'mode_name' => ['type' => 'text'],
                            'mode_lavel_name' => ['type' => 'text'],
                            'css_style' => ['type' => 'text'],
                            'average_star_rating' => ['type' => 'integer'],
                            'job_status' => ['type' => 'keyword'],
                            'recommendation_id' => ['type' => 'long'],
                            'from_company_id' => ['type' => 'long'],
                            'final_status' => ['type' => 'long'],
                            'to_company_id' => ['type' => 'long'],
                            'wiseID' => ['type' => 'long'],
                            'ClientComID' => ['type' => 'long'],
                            'jobId' => ['type' => 'long'],
                            'uc_company_name' => ['type' => 'text'],
                            'interview_mention_link' => ['type' => 'text'],
                            'company_id' => ['type' => 'long'],
                            'user_accept_status' => ['type' => 'keyword'],
                            'created_at' => ['type' => 'date'],
                            'updated_at' => ['type' => 'date'],
                            'url_link' => ['type' => 'text'],
                            'other_email' => ['type' => 'text'],
                            'message' => ['type' => 'text'],
                            'interview_created_by' => ['type' => 'integer'], //
                            'interview_job_id' => ['type' => 'integer'],
                            'interview_job_wise_con_id' => ['type' => 'integer'],
                            'email_template' => ['type' => 'integer'],
                            'recommendation_relation' => [
                                'type' => 'nested',
                                'properties' => [
                                    'id' => ['type' => 'long'],
                                    'from_company_id' => ['type' => 'long'],
                                    'from_user_id' => ['type' => 'long'],
                                    'from_status' => ['type' => 'long'],
                                    'from_created_at' => ['type' => 'date'],
                                    'to_company_id' => ['type' => 'long'],
                                    'to_user_id' => ['type' => 'long'],
                                    'job_id' => ['type' => 'long'],
                                    'to_status' => ['type' => 'long'],
                                    'to_created_at' => ['type' => 'date'],
                                    'final_status' => ['type' => 'long'],
                                    'conversation_id' => ['type' => 'long'],
                                    'add_status' => ['type' => 'long'],
                                    'created_at' => ['type' => 'date'],
                                    'updated_at' => ['type' => 'date'],
                                ],
                            ],
                            'job_assign' => [
                                'type' => 'nested',
                                'properties' => [
                                    'id' => ['type' => 'long'],
                                    'company_id' => ['type' => 'long'],
                                    'job_id' => ['type' => 'long'],
                                    'spok_user_id' => ['type' => 'long'],
                                    'user_id' => ['type' => 'long'],
                                    'user_name' => ['type' => 'text', 'fields' => [
                                        'keyword' => [
                                            'type' => 'keyword',
                                            'ignore_above' => 256,
                                        ],
                                    ]],
                                    'created_at' => ['type' => 'date'],
                                    'updated_at' => ['type' => 'date'],
                                ],
                            ],
                            // Add more top-level fields as needed
                        ],
                    ],
                ],
            ];
            $this->elasticService->getClient()->cluster()->putSettings([
                'body' => [
                    'persistent' => [
                        'action.destructive_requires_name' => true,
                    ],
                ],
            ]);

            $response = $this->elasticService->getClient()->indices()->create($params);
        }

        // dd("k");

        $interviewShedule = DB::table('interview_schedules')
            ->join('job_applications', 'job_applications.id', 'interview_schedules.job_application_id')
            ->join('add_job_wise_condidates', 'interview_schedules.interview_job_wise_con_id', 'add_job_wise_condidates.id')
        // ->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id')
            ->join('jobs', function ($join) {
                $join->on('jobs.id', 'add_job_wise_condidates.job_id');
            })
            ->join('companies', function ($join) {
                $join->on('companies.id', 'jobs.company_id');
            })
            ->leftjoin('clients', 'clients.id', 'jobs.client_id')
            ->leftjoin('users', 'users.id', 'add_job_wise_condidates.submitted_by')
            ->leftjoin('countries', 'countries.id', 'job_applications.country')
            ->leftjoin('state_list', 'state_list.id', 'job_applications.state')
            ->leftjoin('city_list', 'city_list.city_id', 'job_applications.city_id')
            ->leftjoin('companies as uc', 'uc.id', '=', 'users.company_id')
            ->leftjoin('companies as can_c', 'can_c.id', '=', 'job_applications.company_id')
            ->leftjoin('interview_feedback', 'interview_feedback.interview_id', 'interview_schedules.id')
        // 'interview_schedules.stage'
        // ->leftjoin('companies', 'companies.id', 'jobs.company_id')
            ->leftjoin('master_status', 'master_status.id', 'interview_schedules.stage')
            ->leftjoin('interview_mode', 'interview_mode.id', 'interview_schedules.interview_mode')
            ->leftjoin('interview_mode_lavel', 'interview_mode_lavel.id', 'interview_schedules.interview_mode_lavel')
            ->leftjoin('candidate_status', 'candidate_status.id', 'interview_schedules.substage')
            ->leftJoin('recommendation_relation', 'recommendation_relation.job_id', '=', 'jobs.id')
            ->leftJoin('updatetimeline', 'updatetimeline.interview_schedules_id', 'interview_schedules.id')
            ->select(
                'interview_schedules.id',
                'job_applications.full_name',
                'job_applications.email',
                'job_applications.phone',
                'interview_schedules.status',
                'interview_schedules.schedule_date',
                'interview_schedules.stage as master_status_id',
                'interview_schedules.substage as child_status_id',
                'clients.name as client_name',
                'state_list.state as state_name',
                'city_list.city as city_name',
                'countries.country_name',
                'can_c.id as company_id',
                'interview_schedules.available_status',
                'interview_schedules.reschedule',
                'interview_schedules.feedback',
                'interview_schedules.interview_mode',
                'interview_schedules.interviewers',
                'interview_schedules.interview_type',
                'interview_schedules.schedule_from_time',
                'interview_schedules.company_id as real_company_id',
                'interview_schedules.mailstatus',
                'interview_schedules.timezone_abbreviation',
                'interview_schedules.schedule_to_time',
                'interview_schedules.user_accept_status',
                'interview_schedules.created_at',
                'interview_schedules.updated_at',
                'interview_schedules.url_link',
                'interview_schedules.other_email',
                'interview_schedules.message',
                'interview_schedules.interview_created_by',
                'interview_schedules.interview_job_id',
                'interview_schedules.interview_job_wise_con_id',
                'interview_schedules.email_template',
                'users.name as source',
                'users.name as posted_by',
                'users.image as posted_by_image',
                'interview_schedules.job_application_id',
                'master_status.name as master_status_name',
                'candidate_status.name as child_status_name',
                'jobs.title as job_title',
                'jobs.client_id',
                'jobs.company_id as job_company_id',
                'interview_schedules.interview_job_id as job_id',
                'companies.company_name',
                'job_applications.current_designation',
                'job_applications.condidate_type',
                'job_applications.thirdparty_company_id',
                'interview_mode.name as mode_name',
                'interview_mode_lavel.name as mode_lavel_name',
                'master_status.css_style',
                'interview_schedules.average_star_rating',
                'jobs.status as job_status',
                'recommendation_relation.id as recommendation_id',
                'recommendation_relation.from_company_id',
                'recommendation_relation.final_status',
                'recommendation_relation.to_company_id',
                'add_job_wise_condidates.id as wiseID',
                'add_job_wise_condidates.thirdparty_company_id as ClientComID',
                'add_job_wise_condidates.job_id as jobId',
                'add_job_wise_condidates.submitted_by',
                'uc.company_name as uc_company_name',
                'updatetimeline.interview_mention_link'
            )
            ->groupBy('interview_schedules.id')
            ->cursor();

        foreach ($interviewShedule as $row) {

            $recommendation_relation = DB::table('recommendation_relation')
                ->where('id', $row->recommendation_id)
                ->get()
                ->map(function ($s) {
                    return [
                        'id' => (int) $s->id,
                        'from_company_id' => (int) $s->from_company_id,
                        'from_user_id' => (int) $s->from_user_id,
                        'from_status' => (int) $s->from_status,
                        'from_created_at' => ($s->from_created_at) ? Carbon::parse($s->from_created_at)->toIso8601String() : null,
                        'to_company_id' => (int) $s->to_company_id,
                        'to_user_id' => (int) $s->to_user_id,
                        'job_id' => (int) $s->job_id,
                        'to_status' => (int) $s->to_status,
                        'to_created_at' => ($s->to_created_at) ? Carbon::parse($s->to_created_at)->toIso8601String() : null,
                        'final_status' => (int) $s->final_status,
                        'conversation_id' => (int) $s->conversation_id,
                        'add_status' => (int) $s->add_status,
                        'created_at' => ($s->created_at) ? Carbon::parse($s->created_at)->toIso8601String() : null,
                        'updated_at' => ($s->updated_at) ? Carbon::parse($s->updated_at)->toIso8601String() : null,
                    ];
                })
                ->toArray();

            $job_assign = DB::table('job_assign')
                ->join('users', 'users.id', '=', 'job_assign.user_id')
                ->select('users.name as user_name', 'job_assign.*')
                ->where('job_assign.job_id', $row->jobId)
                ->get()
                ->map(function ($s) {
                    return [
                        'id' => (int) $s->id,
                        'company_id' => (int) $s->company_id,
                        'job_id' => (int) $s->job_id,
                        'spok_user_id' => (int) $s->spok_user_id,
                        'user_id' => (int) $s->user_id,
                        'user_name' => strtolower($s->user_name),
                        'created_at' => ($s->created_at) ? Carbon::parse($s->created_at)->toIso8601String() : null,
                        'updated_at' => ($s->updated_at) ? Carbon::parse($s->updated_at)->toIso8601String() : null,
                    ];
                })
                ->toArray();

            $exists = $this->elasticService->getClient()->exists([
                'index' => ELASTIC_CANDIDATES,
                'id' => $row->wiseID,
            ]);
            if ($exists) {
                $params = [
                    'index' => ELASTIC_CANDIDATES,
                    'id' => $row->wiseID,
                ];

                $response = $this->elasticService->getClient()->get($params);
                $data = (object) $response['_source'];
                $row->full_name = $data->full_name;
                $row->email = $data->email;
                $row->phone = $data->phone;
                $row->current_designation = $data->current_designation;
                $row->condidate_type = $data->condidate_type;

            }

            // dd($data);

            $bulkData[] = [
                'index' => [
                    '_index' => ELASTIC_INERTVIEW_TRACKER,
                    '_id' => $row->id,
                ],
            ];

            $bulkData[] = [
                'id' => $row->id,
                'full_name' => strtolower($row->full_name),
                'email' => $row->email,
                'phone' => $row->phone,
                'posted_by' => strtolower($row->posted_by),
                'submitted_by' => $row->submitted_by,
                'state_name' => strtolower($row->state_name),
                'job_company_id' => $row->job_company_id,
                'city_name' => strtolower($row->city_name),
                'country_name' => strtolower($row->country_name),
                'candidate_type' => $row->condidate_type,
                'schedule_date' => ($row->schedule_date) ? Carbon::parse($row->schedule_date)->toIso8601String() : null,
                'master_status_id' => $row->master_status_id,
                'child_status_id' => $row->child_status_id,
                'client_name' => $row->client_name,
                'available_status' => $row->available_status,
                'reschedule' => ($row->reschedule) ? Carbon::parse($row->reschedule)->toIso8601String() : null,
                'feedback' => $row->feedback,
                'interview_mode' => $row->interview_mode,
                'interviewers' => $row->interviewers,
                'interview_type' => $row->interview_type,
                'schedule_from_time' => $row->schedule_from_time,
                'real_company_id' => $row->real_company_id,
                'mailstatus' => $row->mailstatus,
                'timezone_abbreviation' => $row->timezone_abbreviation,
                'schedule_to_time' => $row->schedule_to_time,
                'source' => $row->source,
                'posted_by_image' => $row->posted_by_image,
                'job_application_id' => $row->job_application_id,
                'master_status_name' => $row->master_status_name,
                'child_status_name' => $row->child_status_name,
                'job_title' => $row->job_title,
                'client_id' => $row->client_id,
                'job_id' => $row->job_id,
                'company_name' => $row->company_name,
                'current_designation' => $row->current_designation,
                'thirdparty_company_id' => $row->thirdparty_company_id,
                'mode_name' => $row->mode_name,
                'mode_lavel_name' => $row->mode_lavel_name,
                'css_style' => $row->css_style,
                'average_star_rating' => $row->average_star_rating,
                'job_status' => $row->job_status,
                'recommendation_id' => $row->recommendation_id,
                'from_company_id' => $row->from_company_id,
                'final_status' => $row->final_status,
                'to_company_id' => $row->to_company_id,
                'wiseID' => $row->wiseID,
                'ClientComID' => $row->ClientComID,
                'jobId' => $row->jobId,
                'uc_company_name' => $row->uc_company_name,
                'interview_mention_link' => $row->interview_mention_link,
                'company_id' => $row->company_id, //
                'user_accept_status' => $row->user_accept_status,
                'created_at' => ($row->created_at) ? Carbon::parse($row->created_at)->toIso8601String() : null,
                'updated_at' => ($row->updated_at) ? Carbon::parse($row->updated_at)->toIso8601String() : null,
                'url_link' => $row->url_link,
                'other_email' => $row->other_email,
                'message' => $row->message,
                'interview_created_by' => $row->interview_created_by,
                'interview_job_id' => $row->interview_job_id,
                'interview_job_wise_con_id' => $row->interview_job_wise_con_id,
                'email_template' => $row->email_template,
                'recommendation_relation' => $recommendation_relation ?? [],
                'job_assign' => $job_assign ?? [],
            ];

        }

        $response = $this->elasticService->getClient()->bulk([
            'body' => $bulkData,
        ]);

    }

    public function jobIndexElastic(Request $request)
    {

        // $params = [
        //         'index' => ELASTIC_JOBS,
        //         'body' => [
        //             'properties' => [
        //                 'client_or_company_name' => ['type' => 'keyword'],
        //                 'textUpdate' => ['type' => 'long'],
        //             ]
        //         ]
        // ];

        // $response = $this->elasticService->getClient()->indices()->putMapping($params);

        // dd($response);

        $searchQuery = $request->input('q');
        $page = max((int) $request->input('page', 1), 1);
        $size = (int) $request->input('size', 500); // Default 20 results per page
        $from = ($page - 1) * $size;
        $sort = [];
        $params = [
            'index' => ELASTIC_JOBS,
            'body' => [
                'from' => $from,
                'size' => $size,
                'sort' => $sort,
                'query' => [
                    'bool' => [
                        'must' => [],
                        'must_not' => [
                            ['term' => ['textUpdate' => 10]], // ❌ exclude textUpdate = 1
                        ],
                    ],
                ],
            ],
        ];
        $response = $this->elasticService->getClient()->search($params);

        $total = $response['hits']['total']['value'] ?? 0;
        $data = array_map(function ($hit) {
            return $hit['_source'];
        }, $response['hits']['hits']);

        foreach ($data as $candidate_val) {
            $row = (object) $candidate_val;

            $logos = DB::table('companies')
                ->select('companies.*', 'clients.logo_logical_path', 'jobs.total_positions', 'jobs.company_id as companyChech', 'clients.name as clients_name')
                ->join('jobs', 'companies.id', 'jobs.company_id')
                ->join('clients', 'clients.id', 'jobs.client_id')->where('jobs.client_id', $row->client_id)->first();

            $client_name_com = '';
            $client_name = @$logos->clients_name;
            if (@$logos->companyChech != auth()->user()->company_id) {
                $client_name = @$logos->company_name;
                $client_name = ucwords(substr(@$client_name, 0, 30));
                if (@$logos->companyChech != auth()->user()->company_id) {
                    $client_name = @$logos->company_name;
                    $client_name = ucwords(substr(@$client_name, 0, 30));
                }

            }
            $client_name_com = $client_name;

            $updateParams = [
                'index' => ELASTIC_JOBS,
                'id' => $row->id,
                'body' => [
                    'doc' => [
                        'client_or_company_name' => strtolower($client_name_com),
                        'textUpdate' => 10,
                    ],
                ],
            ];
            $this->elasticService->getClient()->update($updateParams);

        }

        if (! $this->elasticService->indexExists(ELASTIC_JOBS)) {
            $params = [
                'index' => ELASTIC_JOBS,
                'body' => [
                    'settings' => [
                        'number_of_shards' => 1,
                        'number_of_replicas' => 0,
                        'index.blocks.read_only_allow_delete' => false,
                        'index.blocks.write' => false,
                        'index.blocks.read' => false,
                        'index.blocks.metadata' => false,
                        'index.lifecycle.name' => null,
                        'index.lifecycle.rollover_alias' => null,
                        'index.requests.cache.enable' => true,
                        'analysis' => [
                            'analyzer' => [
                                'comma_analyzer' => [
                                    'type' => 'custom',
                                    'tokenizer' => 'comma_tokenizer',
                                    'filter' => ['lowercase', 'trim'],
                                ],
                                'number_list_analyzer' => [
                                    'type' => 'custom',
                                    'tokenizer' => 'number_list_tokenizer',
                                    'filter' => ['trim'],
                                ],
                            ],
                            'tokenizer' => [
                                'comma_tokenizer' => [
                                    'type' => 'pattern',
                                    'pattern' => '\\s*,\\s*',
                                ],
                                'number_list_tokenizer' => [
                                    'type' => 'pattern',
                                    'pattern' => '\\s*,\\s*',
                                ],
                            ],
                            'filter' => [
                                'trim' => [
                                    'type' => 'trim',
                                ],
                            ],
                        ],
                    ],
                    'mappings' => [
                        'properties' => [
                            'status' => ['type' => 'keyword'],
                            'client_name' => ['type' => 'keyword'],
                            'company_name' => ['type' => 'keyword'],
                            'recommendation_relation' => [
                                'type' => 'nested',
                                'properties' => [
                                    'id' => ['type' => 'long'],
                                    'from_company_id' => ['type' => 'long'],
                                    'from_user_id' => ['type' => 'long'],
                                    'from_status' => ['type' => 'long'],
                                    'from_created_at' => ['type' => 'date'],
                                    'to_company_id' => ['type' => 'long'],
                                    'to_user_id' => ['type' => 'long'],
                                    'job_id' => ['type' => 'long'],
                                    'to_status' => ['type' => 'long'],
                                    'to_created_at' => ['type' => 'date'],
                                    'final_status' => ['type' => 'long'],
                                    'conversation_id' => ['type' => 'long'],
                                    'add_status' => ['type' => 'long'],
                                    'created_at' => ['type' => 'date'],
                                    'updated_at' => ['type' => 'date'],
                                ],
                            ],
                            'job_metronics_skills' => [
                                'type' => 'nested',
                                'properties' => [
                                    'id' => ['type' => 'long'],
                                    'company_id' => ['type' => 'long'],
                                    'job_id' => ['type' => 'long'],
                                    'skill_name' => ['type' => 'text', 'fields' => [
                                        'keyword' => [
                                            'type' => 'keyword',
                                            'ignore_above' => 256,
                                        ],
                                    ]],
                                ],
                            ],
                            'job_assign' => [
                                'type' => 'nested',
                                'properties' => [
                                    'id' => ['type' => 'long'],
                                    'company_id' => ['type' => 'long'],
                                    'job_id' => ['type' => 'long'],
                                    'spok_user_id' => ['type' => 'long'],
                                    'user_id' => ['type' => 'long'],
                                    'user_name' => ['type' => 'text', 'fields' => [
                                        'keyword' => [
                                            'type' => 'keyword',
                                            'ignore_above' => 256,
                                        ],
                                    ]],
                                    'created_at' => ['type' => 'date'],
                                    'updated_at' => ['type' => 'date'],
                                ],
                            ],
                            'client_city_jobs' => [
                                'type' => 'nested',
                                'properties' => [
                                    'id' => ['type' => 'long'],
                                    'company_id' => ['type' => 'long'],
                                    'job_id' => ['type' => 'long'],
                                    'city_id' => ['type' => 'long'],
                                    'country_id' => ['type' => 'long'],
                                    'city_name' => ['type' => 'text', 'fields' => [
                                        'keyword' => [
                                            'type' => 'keyword',
                                            'ignore_above' => 256,
                                        ],
                                    ]],
                                    'country_name' => ['type' => 'text', 'fields' => [
                                        'keyword' => [
                                            'type' => 'keyword',
                                            'ignore_above' => 256,
                                        ],
                                    ]],
                                ],
                            ],
                            'client_contact_info_jobs' => [
                                'type' => 'nested',
                                'properties' => [
                                    'id' => ['type' => 'long'],
                                    'company_id' => ['type' => 'long'],
                                    'job_id' => ['type' => 'long'],
                                    'client_contact_info_id' => ['type' => 'long'],
                                    'contact_person_name' => ['type' => 'text', 'fields' => [
                                        'keyword' => [
                                            'type' => 'keyword',
                                            'ignore_above' => 256,
                                        ],
                                    ]],
                                ],
                            ],
                            'client_location_jobs' => [
                                'type' => 'nested',
                                'properties' => [
                                    'id' => ['type' => 'long'],
                                    'company_id' => ['type' => 'long'],
                                    'job_id' => ['type' => 'long'],
                                    'client_location_id' => ['type' => 'long'],
                                ],
                            ],
                            'client_location' => [
                                'type' => 'nested',
                                'properties' => [
                                    'id' => ['type' => 'long'],
                                    'company_id' => ['type' => 'long'],
                                    'client_id' => ['type' => 'long'],
                                    'city_id' => ['type' => 'long'],
                                    'country_id' => ['type' => 'long'],
                                    'state' => ['type' => 'long'],
                                    'address' => ['type' => 'text', 'fields' => [
                                        'keyword' => [
                                            'type' => 'keyword',
                                            'ignore_above' => 256,
                                        ],
                                    ]],
                                ],
                            ],
                            'client_or_company_name' => ['type' => 'keyword'],
                            'textUpdate' => ['type' => 'long'],
                            // Add more top-level fields as needed
                        ],
                    ],
                ],
            ];

            $this->elasticService->getClient()->cluster()->putSettings([
                'body' => [
                    'persistent' => [
                        'action.destructive_requires_name' => true,
                    ],
                ],
            ]);

            $response = $this->elasticService->getClient()->indices()->create($params);
        }

        $jobs = DB::table('jobs')->select(
            'jobs.*',
            'clients.name as clients_name',
            'clients.logo_logical_path',
            'clients.self_company',
            'clients.company_id as client_company_id',
            'companies.company_name',
            'companies.logo')
            ->leftJoin('clients', 'clients.id', 'jobs.client_id')
            ->join('companies', 'companies.id', 'jobs.company_id')
            ->get();

        $bulkData = [];
        foreach ($jobs as $row) {

            $job_metronics_skills = DB::table('job_metronics_skills')
                ->where('job_id', $row->id)
                ->get()
                ->map(function ($s) {
                    return [
                        'id' => $s->id,
                        'company_id' => $s->company_id,
                        'job_id' => $s->job_id,
                        'skill_name' => strtolower($s->skill_name),
                    ];
                })
                ->unique('skill_name')
                ->values()
                ->toArray();

            // ($application->updated_at) ? Carbon::parse($application->updated_at)->toIso8601String() : null,
            $recommendation_relation = DB::table('recommendation_relation')
                ->where('job_id', $row->id)
                ->get()
                ->map(function ($s) {
                    return [
                        'id' => (int) $s->id,
                        'from_company_id' => (int) $s->from_company_id,
                        'from_user_id' => (int) $s->from_user_id,
                        'from_status' => (int) $s->from_status,
                        'from_created_at' => ($s->from_created_at) ? Carbon::parse($s->from_created_at)->toIso8601String() : null,
                        'to_company_id' => (int) $s->to_company_id,
                        'to_user_id' => (int) $s->to_user_id,
                        'job_id' => (int) $s->job_id,
                        'to_status' => (int) $s->to_status,
                        'to_created_at' => ($s->to_created_at) ? Carbon::parse($s->to_created_at)->toIso8601String() : null,
                        'final_status' => (int) $s->final_status,
                        'conversation_id' => (int) $s->conversation_id,
                        'add_status' => (int) $s->add_status,
                        'created_at' => ($s->created_at) ? Carbon::parse($s->created_at)->toIso8601String() : null,
                        'updated_at' => ($s->updated_at) ? Carbon::parse($s->updated_at)->toIso8601String() : null,
                    ];
                })
                ->toArray();

            $job_assign = DB::table('job_assign')
                ->join('users', 'users.id', '=', 'job_assign.user_id')
                ->select('users.name as user_name', 'job_assign.*')
                ->where('job_assign.job_id', $row->id)
                ->get()
                ->map(function ($s) {
                    return [
                        'id' => (int) $s->id,
                        'company_id' => (int) $s->company_id,
                        'job_id' => (int) $s->job_id,
                        'spok_user_id' => (int) $s->spok_user_id,
                        'user_id' => (int) $s->user_id,
                        'user_name' => strtolower($s->user_name),
                        'created_at' => ($s->created_at) ? Carbon::parse($s->created_at)->toIso8601String() : null,
                        'updated_at' => ($s->updated_at) ? Carbon::parse($s->updated_at)->toIso8601String() : null,
                    ];
                })
                ->toArray();

            $job_qualifications = DB::table('job_qualifications')
                ->where('job_id', $row->id)
                ->get()
                ->map(function ($row) {
                    return (array) $row;
                })
                ->toArray();

            $client_location_jobs = DB::table('client_location_jobs')
                ->where('job_id', $row->id)
                ->get()
                ->map(function ($s) {
                    return [
                        'id' => (int) $s->id,
                        'company_id' => (int) $s->company_id,
                        'client_location_id' => (int) $s->client_location_id,
                        'job_id' => (int) $s->job_id,
                    ];
                })
            // ->map(function ($row){
            //     return (array) $row;
            // })
                ->toArray();

            $client_location = DB::table('client_location')
                ->where('client_id', $row->client_id)
                ->get()
                ->map(function ($s) {
                    return [
                        'id' => (int) $s->id,
                        'company_id' => (int) $s->company_id,
                        'client_id' => (int) $s->client_id,
                        'city_id' => (int) $s->city_id,
                        'country_id' => (int) $s->country_id,
                        'state' => (int) $s->state,
                        'address' => strtolower($s->address),
                    ];
                })
                ->toArray();

            $client_city_jobs = DB::table('client_city_jobs')
                ->leftJoin('countries', 'countries.id', '=', 'client_city_jobs.country_id')
                ->leftJoin('city_list', 'city_list.city_id', '=', 'client_city_jobs.city_id')
                ->select('city_list.city as city_name', 'countries.country_name', 'client_city_jobs.*')
                ->where('job_id', $row->id)
                ->get()
                ->map(function ($s) {
                    return [
                        'id' => (int) $s->id,
                        'company_id' => (int) $s->company_id,
                        'job_id' => (int) $s->job_id,
                        'city_id' => (int) $s->city_id,
                        'country_id' => (int) $s->country_id,
                        'city_name' => strtolower($s->city_name),
                        'country_name' => strtolower($s->country_name),
                    ];
                })
                ->toArray();

            // dd($client_city_jobs);

            $client_contact_info_jobs = DB::table('client_contact_info_jobs')
                ->join('client_contact_info', 'client_contact_info.id', '=', 'client_contact_info_jobs.client_contact_info_id')
                ->select('client_contact_info.contact_person_name', 'client_contact_info_jobs.*')
                ->where('client_contact_info_jobs.job_id', $row->id)
                ->get()
                ->map(function ($s) {
                    return [
                        'id' => (int) $s->id,
                        'company_id' => (int) $s->company_id,
                        'job_id' => (int) $s->job_id,
                        'client_contact_info_id' => (int) $s->client_contact_info_id,
                        'contact_person_name' => strtolower($s->contact_person_name),
                    ];
                })
                ->toArray();

            $logos = DB::table('companies')
                ->select('companies.*', 'clients.logo_logical_path', 'jobs.total_positions', 'clients.name as clients_name')
                ->join('jobs', 'companies.id', 'jobs.company_id')
                ->join('clients', 'clients.id', 'jobs.client_id')->where('jobs.id', $row->id)->first();

            $client_name_com = '';
            $client_name = @$logos->clients_name;
            if (@$logos->company_id != auth()->user()->company_id) {
                $client_name = @$logos->company_name;
                $client_name = ucwords(substr(@$client_name, 0, 30));
                if (@$logos->company_id != auth()->user()->company_id) {
                    $client_name = @$logos->company_name;
                    $client_name = ucwords(substr(@$client_name, 0, 30));
                }

            }
            $client_name_com = $client_name;

            $bulkData[] = [
                'index' => [
                    '_index' => ELASTIC_JOBS,
                    '_id' => $row->id,
                ],
            ];

            $bulkData[] = [
                'id' => $row->id,
                'title' => strtolower($row->title),
                'comId' => $row->company_id,
                'slug' => $row->slug,
                'job_description' => $row->job_description,
                'job_requirement' => $row->job_requirement,
                'total_positions' => $row->total_positions,
                'location_id' => $row->location_id,
                'category_id' => $row->category_id, //
                'start_date' => ($row->start_date) ? Carbon::parse($row->start_date)->toIso8601String() : null,
                'end_date' => ($row->end_date) ? Carbon::parse($row->end_date)->toIso8601String() : null,
                'status' => $row->status,
                'required_columns' => $row->required_columns,
                'meta_details' => is_array($row->meta_details) ? $row->meta_details : json_decode($row->meta_details, true),
                'section_visibility' => $row->section_visibility,
                'created_at' => ($row->created_at) ? Carbon::parse($row->created_at)->toIso8601String() : null,
                'updated_at' => ($row->updated_at) ? Carbon::parse($row->updated_at)->toIso8601String() : null,
                'priority' => $row->priority,
                'client_id' => $row->client_id,
                'min_exp' => $row->min_exp,
                'max_exp' => $row->max_exp,
                'min_salary' => $row->min_salary,
                'max_salary' => $row->max_salary,
                'qualification' => $row->qualification,
                'employment_type' => $row->employment_type,
                'min_salary_currency' => $row->min_salary_currency,
                'max_salary_currency' => $row->max_salary_currency,
                'min_sal_mode' => $row->min_sal_mode,
                'max_sal_mode' => $row->max_sal_mode,
                'carrier_website' => $row->carrier_website,
                'work_type' => $row->work_type,
                'show_vendor' => $row->show_vendor,
                'closeType' => $row->closeType,
                'min_yearly' => $row->min_yearly,
                'min_monthly' => $row->min_monthly,
                'min_hourly' => $row->min_hourly,
                'max_yearly' => $row->max_yearly,
                'max_monthly' => $row->max_monthly,
                'max_hourly' => $row->max_hourly,
                'auto_date_job' => ($row->auto_date_job) ? Carbon::parse($row->auto_date_job)->toIso8601String() : null,
                'public_market_status' => $row->public_market_status,
                'work_shifts' => $row->work_shifts,
                'job_assign' => $job_assign ?? [],
                'job_qualifications' => $job_qualifications ?? [],
                'client_location_jobs' => $client_location_jobs ?? [],
                'client_location' => $client_location ?? [],
                'client_city_jobs' => $client_city_jobs ?? [],
                'recommendation_relation' => $recommendation_relation ?? [],
                'job_metronics_skills' => $job_metronics_skills ?? [],
                'client_contact_info_jobs' => $client_contact_info_jobs ?? [],
                'client_name' => strtolower($row->clients_name),
                'logo_logical_path' => $row->logo_logical_path,
                'self_company' => $row->self_company,
                'client_company_id' => $row->client_company_id,
                'company_name' => strtolower($row->company_name),
                'client_or_company_name' => strtolower($client_name_com),
                'logo' => $row->logo,
            ];

        }
        // dd($bulkData);
        $response = $this->elasticService->getClient()->bulk([
            'body' => $bulkData,
        ]);

        $searchQuery = $request->input('q');
        $page = max((int) $request->input('page', 1), 1);
        $size = (int) $request->input('size', 500); // Default 20 results per page
        $from = ($page - 1) * $size;
        $sort = [];
        $params = [
            'index' => ELASTIC_JOBS,
            'body' => [
                'from' => $from,
                'size' => $size,
                'sort' => $sort,
            ],
        ];
        $response = $this->elasticService->getClient()->search($params);

        $total = $response['hits']['total']['value'] ?? 0;
        $data = array_map(function ($hit) {
            return $hit['_source'];
        }, $response['hits']['hits']);

        // dd($applications);

    }

    public function candidateBenchIndexElastic(Request $request)
    {

        $params = [
            'index' => ELASTIC_CANDIDATES_BENCH,
            'body' => [
                'properties' => [
                    'text' => ['type' => 'text', 'analyzer' => 'standard'],
                    'textUpdate' => ['type' => 'long'],
                ],
            ],
        ];

        $response = $this->elasticService->getClient()->indices()->putMapping($params);

        $searchQuery = $request->input('q');
        $page = max((int) $request->input('page', 1), 1);
        $size = (int) $request->input('size', 1000); // Default 20 results per page
        $from = ($page - 1) * $size;
        $sort = [];
        $params = [
            'index' => ELASTIC_CANDIDATES_BENCH,
            'body' => [
                'from' => $from,
                'size' => $size,
                'sort' => $sort,
                'query' => [
                    'bool' => [
                        'must' => [],
                        'filter' => [
                            ['exists' => ['field' => 'resume']],
                        ],
                        'must_not' => [
                            ['term' => ['textUpdate' => 1]], // ❌ exclude textUpdate = 1
                        ],
                    ],
                ],
            ],
        ];
        $response = $this->elasticService->getClient()->search($params);

        $total = $response['hits']['total']['value'] ?? 0;
        $data = array_map(function ($hit) {
            return $hit['_source'];
        }, $response['hits']['hits']);

        foreach ($data as $candidate_val) {
            $row = (object) $candidate_val;
            $fileUrl = $row->resume;
            $text = GeneralMethod::extractTextFromUrl($fileUrl);
            $updateParams = [
                'index' => ELASTIC_CANDIDATES_BENCH,
                'id' => $row->id, // This must be the ES document ID
                'body' => [
                    'doc' => [
                        'text' => $text,
                        'textUpdate' => 1,
                    ],
                ],
            ];

            $this->elasticService->getClient()->update($updateParams);

        }
        // dd($total,$data);

        if (! $this->elasticService->indexExists(ELASTIC_CANDIDATES_BENCH)) {
            $params = [
                'index' => ELASTIC_CANDIDATES_BENCH,
                'body' => [
                    'settings' => [
                        'number_of_shards' => 1,
                        'number_of_replicas' => 0,
                        'index.blocks.read_only_allow_delete' => false,
                        'index.blocks.write' => false,
                        'index.blocks.read' => false,
                        'index.blocks.metadata' => false,
                        'index.lifecycle.name' => null,
                        'index.lifecycle.rollover_alias' => null,
                        'index.requests.cache.enable' => true,
                        'analysis' => [
                            'analyzer' => [
                                'comma_analyzer' => [
                                    'type' => 'custom',
                                    'tokenizer' => 'pattern',
                                    'pattern' => '\\s*,\\s*', // Handles spaces around commas
                                    'filter' => ['lowercase', 'trim'],
                                ],
                                'number_list_analyzer' => [
                                    'type' => 'custom',
                                    'tokenizer' => 'number_list_tokenizer',
                                    'filter' => ['trim'],
                                ],
                            ],
                            'tokenizer' => [
                                'number_list_tokenizer' => [
                                    'type' => 'pattern',
                                    'pattern' => '\\s*,\\s*',
                                    'group' => -1,
                                ],
                            ],
                        ],
                    ],
                    'mappings' => [
                        'properties' => [
                            'email' => ['type' => 'keyword'],
                            'phone' => ['type' => 'keyword'],
                            'ads_switch' => ['type' => 'keyword'],
                            'downloadCount' => ['type' => 'long'],
                            'total_exp' => ['type' => 'float'],
                            'rel_exp' => ['type' => 'float'],
                            'viewCount' => ['type' => 'long'],
                            'text' => ['type' => 'text', 'analyzer' => 'standard'],
                            'textUpdate' => ['type' => 'long'],
                            'linkedin' => ['type' => 'text'],
                            'github' => ['type' => 'text'],
                            'behance' => ['type' => 'text'],
                            'otherLink' => ['type' => 'text'],
                        ],
                    ],
                ],
            ];
            $this->elasticService->getClient()->cluster()->putSettings([
                'body' => [
                    'persistent' => [
                        'action.destructive_requires_name' => true,
                    ],
                ],
            ]);
            $response = $this->elasticService->getClient()->indices()->create($params);
        }

        $applications = JobApplication::select(
            'job_applications.id',
            'job_applications.thirdparty_company_id',
            'job_applications.outsourcing_type',
            'job_applications.full_name',
            'job_applications.company_id',
            'job_applications.email',
            'job_applications.phone',
            'job_applications.np_id',
            'job_applications.serving_notice_period_mode',
            'job_applications.country',
            'job_applications.submitted_by',
            'job_applications.state',
            'job_applications.pay_rate_currency',
            'job_applications.pay_rate_amount',
            'job_applications.pay_rate_mode',
            'job_applications.city',
            'job_applications.city_id',
            'job_applications.skills',
            'job_applications.photo',
            'job_applications.job_id',
            'job_applications.total_exp',
            'job_applications.ads_switch',
            'job_applications.rel_exp',
            'job_applications.ctc',
            'job_applications.current_ctc_cur',
            'job_applications.current_ctc_mode',
            'job_applications.current_designation',
            'job_applications.ectc',
            'job_applications.current_ectc_cur',
            'job_applications.current_ectc_mode',
            'job_applications.current_company',
            'job_applications.ctc_hourly',
            'job_applications.ctc_monthly',
            'job_applications.ctc_yearly',
            'job_applications.ectc_hourly',
            'job_applications.ectc_monthly',
            'job_applications.ectc_yearly',
            'job_applications.status',
            'job_applications.duplicate',
            'job_applications.bill_rate_currency',
            'job_applications.bill_rate_amount',
            'job_applications.bill_rate_mode',
            'job_applications.condidate_type',
            'job_applications.master_status_id',
            'job_applications.is_viewed',
            'job_applications.comission_type',
            'job_applications.column_priority',
            'job_applications.phone_code',
            'job_applications.second_phone_code',
            'job_applications.secondary_mobile',
            'job_applications.resume',
            'job_applications.rating',
            'job_applications.skype_id',
            'job_applications.cover_letter',
            'job_applications.passport',
            'job_applications.process_id',
            'job_applications.qualification',
            'job_applications.secondrary_email',
            'job_applications.city',
            'job_applications.pincode',
            'job_applications.percentage_hike',
            'job_applications.comment',
            'job_applications.countforadd',
            'job_applications.superadminstatus',
            'job_applications.created_at',
            'job_applications.updated_at',
            'job_applications.dob',
            'job_applications.deleted_at',
            'companies.company_name',
            'users.name as posted_by',
            'users.image as posted_by_image',
            'bench_master_status.name as master_status_name',
            'bench_master_status.css_style',
            'qualification_master.name as qualification_name',
            'state_list.state as state_name',
            'city_list.city as city_name',
            'countries.country_name'
        )->join('bench_master_status', 'bench_master_status.id', 'job_applications.status')
            ->leftjoin('users', 'users.id', 'job_applications.submitted_by')
            ->leftjoin('companies', 'companies.id', 'job_applications.company_id')
            ->leftjoin('qualification_master', 'qualification_master.id', 'job_applications.qualification')
            ->leftJoin('countries', 'countries.id', '=', 'job_applications.country')
            ->leftJoin('state_list', 'state_list.id', '=', 'job_applications.state')
            ->leftJoin('city_list', 'city_list.city_id', '=', 'job_applications.city_id')
            ->where('condidate_type', '=', 'bench_condidate')
            ->get();

        // dd( $applications);

        $bulkData = [];
        $ik = 1;
        foreach ($applications as $application) {

            $add_job_wise_condidates = DB::table('add_job_wise_condidates')
                ->where('job_application_id', $application->id)
                ->get()
                ->map(fn ($s) => [
                    'id' => $s->id,
                    'job_application_id' => $s->job_application_id,
                    'job_id' => $s->job_id,
                    'status_id' => $s->status_id,
                    'master_status_id' => $s->master_status_id,
                    'child_status_id' => $s->child_status_id,
                ])
                ->toArray();

            $job_application_skills = DB::table('job_application_skills')
                ->where('job_application_id', $application->id)
                ->get()
            // ->map(fn($row) => (array) $row)
                ->map(fn ($s) => [
                    'id' => $s->id,
                    'job_application_id' => $s->id,
                    'skills' => strtolower($s->skills),
                ])
                ->toArray();

            $viewCount = DB::table('bench_count_view_download')
                ->where('type', '=', 1)
                ->where('status', '=', 0)
                ->where('candidate_id', '=', $application->id)
                ->count();
            $downloadCount = DB::table('bench_count_view_download')
                ->where('type', '=', 2)
                ->where('status', '=', 0)
                ->where('candidate_id', '=', $application->id)
                ->count();

            $hmData = DB::table('bench_assign')
                ->join('users', 'users.id', '=', 'bench_assign.user_id')
                ->select('users.id', 'users.name', 'users.image')
                ->where('bench_assign.bench_applications_id', '=', $application->id)
                ->where('bench_assign.company_id', '=', $application->company_id)
                ->where('users.company_id', '=', $application->company_id)
                ->orderBy('users.name', 'ASC')
                ->get();

            $bulkData[] = [
                'index' => [
                    '_index' => ELASTIC_CANDIDATES_BENCH,
                    '_id' => $application->id,
                ],
            ];

            $bulkData[] = [
                'id' => $application->id,
                'thirdparty_company_id' => $application->thirdparty_company_id,
                'full_name' => $application->full_name,
                'company_id' => $application->company_id,
                'comission_type' => $application->comission_type,
                'outsourcing_type' => $application->outsourcing_type,
                'email' => $application->email,
                'pay_rate_currency' => $application->pay_rate_currency,
                'pay_rate_amount' => $application->pay_rate_amount,
                'pay_rate_mode' => $application->pay_rate_mode,
                'phone' => $application->phone,
                'np_id' => $application->np_id,
                'immediate_join' => $application->np_id,
                'serving_notice_period_mode' => $application->serving_notice_period_mode,
                'state' => $application->state,
                'city' => $application->city,
                'submitted_by' => $application->submitted_by,
                'country_name' => $application->country_name,
                'state_name' => $application->state_name,
                'city_name' => $application->city_name,
                'city_id' => $application->city_id,
                'skills' => $application->skills,
                'photo' => $application->photo,
                'job_id' => $application->job_id,
                'jobId' => $application->jobId,
                'total_exp' => $application->total_exp,
                'rel_exp' => $application->rel_exp,
                'ctc' => $application->ctc,
                'ctc_hourly' => $application->ctc_hourly,
                'ctc_monthly' => $application->ctc_monthly,
                'ctc_yearly' => $application->ctc_yearly,
                'ectc_hourly' => $application->ectc_hourly,
                'ectc_monthly' => $application->ectc_monthly,
                'ectc_yearly' => $application->ectc_yearly,
                'ads_switch' => $application->ads_switch,
                'job_application_skills' => $job_application_skills,
                'hiring_hr' => $hmData,
                'jobs' => $add_job_wise_condidates,
                'viewCount' => $viewCount,
                'downloadCount' => $downloadCount,
                'current_ctc_cur' => $application->current_ctc_cur,
                'current_ctc_mode' => $application->current_ctc_mode,
                'current_designation' => $application->current_designation,
                'ectc' => $application->ectc,
                'current_ectc_cur' => $application->current_ectc_cur,
                'current_ectc_mode' => $application->current_ectc_mode,
                'current_company' => $application->current_company,
                'duplicate' => $application->duplicate,
                'bill_rate_currency' => $application->bill_rate_currency,
                'bill_rate_amount' => $application->bill_rate_amount,
                'bill_rate_mode' => $application->bill_rate_mode,
                'condidate_type' => $application->condidate_type,
                'master_status_id' => $application->master_status_id,
                'posted_by' => $application->posted_by,
                'is_viewed' => $application->is_viewed,
                'column_priority' => $application->column_priority,
                'phone_code' => $application->phone_code,
                'second_phone_code' => $application->second_phone_code,
                'secondary_mobile' => $application->secondary_mobile,
                'rating' => $application->rating,
                'skype_id' => $application->skype_id,
                'cover_letter' => $application->cover_letter,
                'passport' => $application->passport,
                'process_id' => $application->process_id,
                'secondrary_email' => $application->secondrary_email,
                'pincode' => $application->pincode,
                'comment' => $application->comment,
                'dob' => $application->dob,
                'updated_at' => ($application->updated_at) ? Carbon::parse($application->updated_at)->toIso8601String() : null,
                'created_at' => ($application->created_at) ? Carbon::parse($application->created_at)->toIso8601String() : null,
                'deleted_at' => ($application->deleted_at) ? Carbon::parse($application->created_at)->toIso8601String() : null,
                'posted_by_image' => $application->posted_by_image,
                'master_status_name' => $application->master_status_name,
                'css_style' => $application->css_style,
                'company_name' => $application->company_name,
                'status' => $application->status,
                'resume' => $application->resume,
                'qualification' => $application->qualification,
                'qualification_name' => $application->qualification_name,
                'percentage_hike' => $application->percentage_hike,
                'superadminstatus' => $application->superadminstatus,
                'countforadd' => $application->countforadd,
            ];
            $ik++;
        }
        $response = $this->elasticService->getClient()->bulk([
            'body' => $bulkData,
        ]);

        $searchQuery = $request->input('q');
        $page = max((int) $request->input('page', 1), 1);
        $size = (int) $request->input('size', 500); // Default 20 results per page
        $from = ($page - 1) * $size;
        $sort = [];
        $params = [
            'index' => ELASTIC_CANDIDATES_BENCH,
            'body' => [
                'from' => $from,
                'size' => $size,
                'sort' => $sort,
            ],
        ];
        $response = $this->elasticService->getClient()->search($params);

        $total = $response['hits']['total']['value'] ?? 0;
        $data = array_map(function ($hit) {
            return $hit['_source'];
        }, $response['hits']['hits']);

    }

    public function candidateIndexElastic(Request $request)
    {

        $add_job_wise_condidates = tenant_table('add_job_wise_condidates')->select('id', 'job_id')->orderByDesc(column: 'created_at')->limit(50)->get();

        foreach ($add_job_wise_condidates as $add_job_wise) {
            GeneralMethod::JobApplicationsAddEditUpload($add_job_wise->id);
        }

        $mapping = $this->elasticService->getClient()->indices()->getMapping([
            'index' => ELASTIC_CANDIDATES,
        ]);

        // $job_wise =  DB::table('add_job_wise_condidates')
        //     ->whereIn('master_status_id',[5,6])
        //     ->select('add_job_wise_condidates.id','add_job_wise_condidates.job_id')
        //     ->cursor();

        // foreach ($job_wise as $row) {
        //     $offer_job = DB::table('offer_job_applications')
        //     ->where('joined', $row->id)
        //     ->where('job_id',$row->job_id)
        //     ->first();
        //     if($offer_job){
        //         $main_tds = DB::table('offer_create_main_tds')->where('offer_id', $offer_job->id)->first();
        //         if($main_tds){
        //             $employment_type = GeneralMethod::employmentType();
        //             $of_employment_type = $employment_type[$main_tds->employment_type];

        //             $updateTime = now()->toIso8601String();
        //             $updateParams_e = [
        //                 'index' => ELASTIC_CANDIDATES,
        //                 'id' => $row->id,
        //                 'body' => [
        //                     'doc' => [
        //                         'of_employment_type' => $of_employment_type,
        //                     ],
        //                     'doc_as_upsert' => false // Changed to false since we verified existence
        //                 ],
        //                 'refresh' => true // Force immediate refresh
        //             ];
        //             // dd($updateParams);

        //             $redudds = $this->elasticService->getClient()->update($updateParams_e);

        //         }

        //     }

        // }

        // dd("sjdjd");

        // $params = [
        //     'index' => ELASTIC_CANDIDATES,
        //     'body' => [
        //         'properties' => [
        //             'of_employment_type' => ['type' => 'keyword']
        //             ]
        //         ]
        // ];
        // $response = $this->elasticService->getClient()->indices()->putMapping($params);
        // dd($response);

        // $searchQuery = $request->input('q');
        // $page = max((int) $request->input('page', 1), 1);
        // $size = (int) $request->input('size', 500); // Default 20 results per page
        // $from = ($page - 1) * $size;
        // $sort = [];
        // $params = [
        //     'index' => ELASTIC_CANDIDATES,
        //     'body' => [
        //         'from' => $from,
        //         'size' => $size,
        //         'sort' => $sort,
        //         'query' => [
        //             'bool' => [
        //                 'must' => [],
        //                 'must_not' => [
        //                     [ 'term' => [ 'textUpdate' => 9 ] ] // ❌ exclude textUpdate = 1
        //                 ]
        //             ]
        //         ]
        //     ]
        // ];
        // $response = $this->elasticService->getClient()->search($params);

        // $total = $response['hits']['total']['value'] ?? 0;
        // $data = array_map(function ($hit) {
        // 	return $hit['_source'];
        // }, $response['hits']['hits']);

        // foreach($data as $candidate_val){
        //     $row = (object) $candidate_val;

        //         $client_name_com = GeneralMethod::companyDetailsByClient($row->jobId);

        //         $updateParams = [
        // 			'index' => ELASTIC_CANDIDATES,
        // 			'id' =>$row->id,
        // 			'body' => [
        // 				'doc' => [
        // 					 'client_or_company_name' => strtolower($client_name_com),
        //                      'textUpdate' => 9
        // 				]
        // 			],
        // 		];
        // 		$this->elasticService->getClient()->update($updateParams);

        // }

        // dd($total,$data);

        if (! $this->elasticService->indexExists(ELASTIC_CANDIDATES)) {
            $params = [
                'index' => ELASTIC_CANDIDATES,
                'body' => [
                    'settings' => [
                        'number_of_shards' => 1,
                        'number_of_replicas' => 0,
                        'index.blocks.read_only_allow_delete' => false,
                        'index.blocks.write' => false,
                        'index.blocks.read' => false,
                        'index.blocks.metadata' => false,
                        'index.lifecycle.name' => null,
                        'index.lifecycle.rollover_alias' => null,
                        'index.requests.cache.enable' => true,
                        'analysis' => [
                            'analyzer' => [
                                'comma_analyzer' => [
                                    'type' => 'custom',
                                    'tokenizer' => 'pattern',
                                    'pattern' => '\\s*,\\s*', // Handles spaces around commas
                                    'filter' => ['lowercase', 'trim'],
                                ],
                                'number_list_analyzer' => [
                                    'type' => 'custom',
                                    'tokenizer' => 'number_list_tokenizer',
                                    'filter' => ['trim'],
                                ],
                            ],
                            'tokenizer' => [
                                'number_list_tokenizer' => [
                                    'type' => 'pattern',
                                    'pattern' => '\\s*,\\s*',
                                    'group' => -1,
                                ],
                            ],
                        ],
                    ],
                    'mappings' => [
                        'properties' => [
                            'email' => ['type' => 'keyword'],
                            'phone' => ['type' => 'keyword'],
                            'posted_by' => ['type' => 'keyword'],
                            'client_name' => ['type' => 'keyword'],
                            'client_id' => ['type' => 'long'],
                            'confidential' => ['type' => 'integer'],
                            'state_name' => ['type' => 'keyword'],
                            'city_name' => ['type' => 'keyword'],
                            'country_name' => ['type' => 'keyword'],
                            'company_name' => ['type' => 'keyword'],
                            'client_or_company_name' => ['type' => 'keyword'],
                            'total_exp' => ['type' => 'float'],
                            'rel_exp' => ['type' => 'float'],
                            'of_joined_date' => ['type' => 'date'],
                            'accept_date_time' => ['type' => 'date'],
                            'approved_date_time' => ['type' => 'date'],
                            'send_approved_date_time' => ['type' => 'date'],
                            'send_accept_date_time' => ['type' => 'date'],
                            'of_stage_date' => ['type' => 'date'],
                            'of_created_date' => ['type' => 'date'],
                            'of_expired_date' => ['type' => 'date'],
                            'text' => ['type' => 'text', 'analyzer' => 'standard'],
                            'textUpdate' => ['type' => 'long'],
                            'linkedin' => ['type' => 'text'],
                            'github' => ['type' => 'text'],
                            'behance' => ['type' => 'text'],
                            'otherLink' => ['type' => 'text'],
                            'interviewDateOne' => ['type' => 'date'],
                            'interview_mode_one' => ['type' => 'keyword'],
                            'interviewDateTwo' => ['type' => 'date'],
                            'interview_mode_two' => ['type' => 'keyword'],
                            'interviewDateThree' => ['type' => 'date'],
                            'interview_mode_three' => ['type' => 'keyword'],
                            'interviewDateFour' => ['type' => 'date'],
                            'interview_mode_four' => ['type' => 'keyword'],
                            'interviewDateFive' => ['type' => 'date'],
                            'interview_mode_five' => ['type' => 'keyword'],
                            'interviewDateSix' => ['type' => 'date'],
                            'interviewTimeSix' => ['type' => 'keyword'],
                            'interview_mode_six' => ['type' => 'keyword'],
                        ],
                    ],
                ],
            ];
            $this->elasticService->getClient()->cluster()->putSettings([
                'body' => [
                    'persistent' => [
                        'action.destructive_requires_name' => true,
                    ],
                ],
            ]);
            $response = $this->elasticService->getClient()->indices()->create($params);
            // dd($response);
        }

        $add_job_wise_condidates = DB::table('add_job_wise_condidates')
            ->select('add_job_wise_condidates.id', 'add_job_wise_condidates.job_id')
            ->cursor();

        // ->whereBetween('add_job_wise_condidates.created_at', [
        //         '2025-05-01 00:00:00',
        //         '2025-06-18 23:00:00'
        //     ])

        $bulkData = [];
        $batchSize = 1000;
        $count = 0;

        foreach ($add_job_wise_condidates as $add_job_wise) {

            $id = $add_job_wise->id;
            $jobId = $add_job_wise->job_id;
            $applications = JobApplication::select(
                'job_applications.id',
                'job_applications.thirdparty_company_id',
                'job_applications.full_name',
                'job_applications.email',
                'job_applications.phone',
                'job_applications.np_id',
                'job_applications.serving_notice_period_mode',
                'job_applications.country',
                'job_applications.state',
                'job_applications.city',
                'job_applications.city_id',
                'job_applications.skills',
                'job_applications.photo',
                'job_applications.job_id',
                'job_applications.total_exp',
                'job_applications.rel_exp',
                'job_applications.ctc',
                'job_applications.current_ctc_cur',
                'job_applications.current_ctc_mode',
                'job_applications.current_designation',
                'job_applications.ectc',
                'job_applications.current_ectc_cur',
                'job_applications.current_ectc_mode',
                'job_applications.current_company',
                'job_applications.ctc_hourly',
                'job_applications.ctc_monthly',
                'job_applications.ctc_yearly',
                'job_applications.ectc_hourly',
                'job_applications.ectc_monthly',
                'job_applications.ectc_yearly',
                'job_applications.status',
                'add_job_wise_condidates.skill_match',
                'add_job_wise_condidates.existing',
                'add_job_wise_condidates.placed_end_date',
                'add_job_wise_condidates.rec_candidate_assign_id',
                'job_applications.duplicate',
                'job_applications.bill_rate_currency',
                'job_applications.bill_rate_amount',
                'job_applications.bill_rate_mode',
                'job_applications.condidate_type',
                'job_applications.master_status_id',
                'job_applications.is_viewed',
                'job_applications.column_priority',
                'job_applications.phone_code',
                'job_applications.second_phone_code',
                'job_applications.secondary_mobile',
                'job_applications.resume',
                'job_applications.rating',
                'job_applications.skype_id',
                'job_applications.cover_letter',
                'job_applications.passport',
                'job_applications.process_id',
                'job_applications.qualification',
                'job_applications.secondrary_email',
                'job_applications.city',
                'job_applications.pincode',
                'job_applications.percentage_hike',
                'job_applications.comment',
                'job_applications.dob',
                'job_applications.interviewDateOne',
                'job_applications.interviewDateTwo',
                'job_applications.interviewDateThree',
                'job_applications.interviewDateFour',
                'job_applications.interviewDateFive',
                'job_applications.interviewDateSix',
                'job_applications.linkedin',
                'job_applications.github',
                'job_applications.behance',
                'job_applications.otherLink',
                'job_applications.interview_mode_one',
                'job_applications.interview_mode_two',
                'job_applications.interview_mode_three',
                'job_applications.interview_mode_four',
                'job_applications.interview_mode_five',
                'job_applications.interview_mode_six',
                'job_applications.outsourcing_type',
                'job_applications.pay_rate_currency',
                'job_applications.pay_rate_amount',
                'job_applications.pay_rate_mode',
                'job_applications.company_id as other_company',
                'jobs.title',
                'jobs.client_id',
                'jobs.employment_type',
                'jobs.company_id as client_comapny_id',
                'job_locations.location',
                'clients.name as client_name',
                'users.name as posted_by',
                'users.image as posted_by_image',
                'master_status.name',
                'master_status.css_style',
                'qualification_master.name as qualification_name',
                'state_list.state as state_name',
                'city_list.city as city_name',
                'countries.country_name',
                'recommendation_relation.final_status',
                'recommendation_relation.id as recommendation_id',
                'recommendation_relation.from_company_id',
                'recommendation_relation.to_company_id',
                'companies.company_name',
                'documents.hashname',
                'app_com.company_name as apl_company_name',
                'owncom.company_name as post_company_name',
                'cur_com.company_name as cur_company_name',
                'jobs.status as job_status',
                'add_job_wise_condidates.child_status_id as childStatus',
                'add_job_wise_condidates.id as wiseId',
                'add_job_wise_condidates.master_status_id as job_master_status_id',
                'offer_job_applications.proposed_ctc',
                'offer_job_applications.any_offer as of_any_offer',
                'offer_job_applications.offer_approved as of_offer_approved',
                'offer_job_applications.offer_release as of_offer_release',
                'offer_job_applications.offer_acceptance as of_offer_acceptance',
                'offer_job_applications.joined as of_joined',
                'offer_job_applications.joined_date as of_joined_date',
                'offer_job_applications.offer_currency as of_offer_currency',
                'offer_job_applications.offer_mode as of_offer_mode',
                'offer_job_applications.offer_amount as of_offer_amount',
                'offer_job_applications.id as offer_id',
                'offer_job_applications.document',
                'offer_job_applications.sub_for_approval',
                'offer_job_applications.created_at as of_stage_date',
                'add_job_wise_condidates.thirdparty_company_id as ClientComID',
                'add_job_wise_condidates.updated_at',
                'add_job_wise_condidates.created_at',
                'add_job_wise_condidates.company_id',
                'add_job_wise_condidates.submitted_by',
                'job_applications.deleted_at',
                'add_job_wise_condidates.job_id as jobId'
            )
                ->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id')
                ->join('master_status', function ($join) {
                    $join->on('master_status.id', 'add_job_wise_condidates.master_status_id');
                })
                ->leftJoin('jobs', function ($join) {
                    $join->on('jobs.id', 'add_job_wise_condidates.job_id');
                })
                ->leftJoin('recommendation_relation', function ($join) {
                    $join->on('recommendation_relation.job_id', 'jobs.id');
                })
                // ->leftJoin('recommendation_relation', 'recommendation_relation.job_id', '=', 'jobs.id')
                ->leftJoin('companies', function ($join) {
                    $join->on('companies.id', 'add_job_wise_condidates.company_id');
                })
                ->leftJoin('companies as owncom', function ($join) {
                    $join->on('owncom.id', 'job_applications.thirdparty_company_id');
                })
                ->leftjoin('companies as app_com', function ($join) {
                    $join->on('app_com.id', 'job_applications.company_id');
                })
                ->leftjoin('companies as cur_com', function ($join) {
                    $join->on('cur_com.id', 'jobs.company_id');
                })
                ->leftJoin('offer_job_applications', function ($join) use ($jobId) {
                    $join->on('offer_job_applications.application_id', 'job_applications.id');
                    $join->where('offer_job_applications.job_id', $jobId);
                    // $join->where('offer_job_applications.company_id', $company_id);
                })
                // ->leftJoin('jobs', 'jobs.id', '=', 'job_applications.job_id')
                ->leftjoin('clients', 'clients.id', 'jobs.client_id')
                // ->leftjoin('notice_periods', 'notice_periods.np_id', 'job_applications.np_id')
                ->leftjoin('job_locations', 'job_locations.id', 'jobs.location_id')
                ->leftjoin('users', 'users.id', 'add_job_wise_condidates.submitted_by')
                ->leftjoin('documents', 'documents.documentable_id', 'job_applications.id')
                ->leftjoin('qualification_master', 'qualification_master.id', 'job_applications.qualification')
                ->leftJoin('countries', 'countries.id', '=', 'job_applications.country')
                ->leftJoin('state_list', 'state_list.id', '=', 'job_applications.state')
                ->leftJoin('city_list', 'city_list.city_id', '=', 'job_applications.city_id')
                ->where('add_job_wise_condidates.id', $id)
                ->orderByDesc('documents.id')->cursor();

            // dd($applications);

            $bulkData = [];

            foreach ($applications as $application) {

                $wiseId = $application->wiseId;

                $client_name_com = GeneralMethod::companyDetailsByClient($application->jobId);

                $exists = $this->elasticService->getClient()->exists([
                    'index' => ELASTIC_CANDIDATES,
                    'id' => $wiseId,
                ]);

                // dd($addJobWiseCondidate_id);

                if (! $exists) {

                    $intRow = DB::table('interview_schedules')
                        ->select('*')
                        ->where('job_application_id', $application->id)
                        ->where('interview_job_id', $application->jobId)
                        ->orderBy('interview_schedules.id', 'DESC')
                        ->first();

                    $job_application_skills = DB::table('job_application_skills')
                        ->where('job_application_id', $application->id)
                        ->get()
                    // ->map(fn($row) => (array) $row)
                        ->map(fn ($s) => [
                            'id' => $s->id,
                            'job_application_id' => $s->id,
                            'skills' => strtolower($s->skills),
                        ])
                        ->toArray();

                    $bulkData[] = [
                        'index' => [
                            '_index' => ELASTIC_CANDIDATES,
                            '_id' => $wiseId,
                        ],
                    ];
                    // $application->last_login ? Carbon::parse($application->last_login)->toIso8601String() : null

                    $bulkData[] = [
                        'id' => $wiseId,
                        'candidate_id' => $application->id,
                        'thirdparty_company_id' => $application->thirdparty_company_id,
                        'full_name' => strtolower($application->full_name),
                        'email' => $application->email,
                        'phone' => $application->phone,
                        'np_id' => $application->np_id,
                        'immediate_join' => $application->np_id,
                        'serving_notice_period_mode' => $application->serving_notice_period_mode,
                        'country_name' => strtolower($application->country_name),
                        'state_name' => strtolower($application->state_name),
                        'city_name' => strtolower($application->city_name),
                        'city_id' => $application->city_id,
                        'skills' => $application->skills,
                        'photo' => $application->photo,
                        'job_id' => $application->job_id,
                        'jobId' => $application->jobId,
                        'total_exp' => $application->total_exp,
                        'rel_exp' => $application->rel_exp,
                        'ctc' => $application->ctc,
                        'outsourcing_type' => $application->outsourcing_type,
                        'pay_rate_currency' => $application->pay_rate_currency,
                        'pay_rate_amount' => $application->pay_rate_amount,
                        'pay_rate_mode' => $application->pay_rate_mode,
                        'job_application_skills' => $job_application_skills,
                        'current_ctc_cur' => $application->current_ctc_cur,
                        'current_ctc_mode' => $application->current_ctc_mode,
                        'current_designation' => $application->current_designation,
                        'ectc' => $application->ectc,
                        'current_ectc_cur' => $application->current_ectc_cur,
                        'current_ectc_mode' => $application->current_ectc_mode,
                        'current_company' => $application->current_company,
                        'duplicate' => $application->duplicate,
                        'bill_rate_currency' => $application->bill_rate_currency,
                        'bill_rate_amount' => $application->bill_rate_amount,
                        'bill_rate_mode' => $application->bill_rate_mode,
                        'condidate_type' => $application->condidate_type,
                        'master_status_id' => $application->master_status_id,
                        'title' => strtolower($application->title),
                        'client_id' => $application->client_id,
                        'employment_type' => $application->employment_type,
                        'client_comapny_id' => $application->client_comapny_id,
                        'location' => $application->location,
                        'client_name' => strtolower($application->client_name),
                        'posted_by' => strtolower($application->posted_by),
                        'posted_by_image' => $application->posted_by_image,
                        'name' => $application->name,
                        'post_company_name' => $application->post_company_name,
                        'css_style' => $application->css_style,
                        'qualification_name' => strtolower($application->qualification_name),
                        'final_status' => $application->final_status,
                        'recommendation_id' => $application->recommendation_id,
                        'from_company_id' => $application->from_company_id,
                        'to_company_id' => $application->to_company_id,
                        'company_id' => $application->company_id,
                        'company_name' => strtolower($application->company_name),
                        'apl_company_name' => strtolower($application->apl_company_name),
                        'cur_company_name' => strtolower($application->cur_company_name),
                        'job_status' => $application->job_status,
                        'childStatus' => $application->childStatus,
                        'wiseId' => $application->wiseId,
                        'job_master_status_id' => $application->job_master_status_id,
                        'of_any_offer' => $application->of_any_offer,
                        'of_offer_approved' => $application->of_offer_approved,
                        'of_offer_release' => $application->of_offer_release,
                        'of_offer_acceptance' => $application->of_offer_acceptance,
                        'of_joined' => $application->of_joined,
                        'of_joined_date' => (! empty($application->of_joined_date) && $application->of_joined_date != '0000-00-00 00:00:00') ? Carbon::parse($application->of_joined_date)->toIso8601String() : null,
                        'of_offer_currency' => $application->of_offer_currency,
                        'of_offer_mode' => $application->of_offer_mode,
                        'of_offer_amount' => $application->of_offer_amount,
                        'offer_id' => $application->offer_id,
                        'ClientComID' => $application->ClientComID,
                        'is_viewed' => $application->is_viewed,
                        'column_priority' => $application->column_priority,
                        'phone_code' => $application->phone_code,
                        'second_phone_code' => $application->second_phone_code,
                        'secondary_mobile' => $application->secondary_mobile,
                        'ctc_hourly' => $application->ctc_hourly,
                        'ctc_monthly' => $application->ctc_monthly,
                        'ctc_yearly' => $application->ctc_yearly,
                        'ectc_hourly' => $application->ectc_hourly,
                        'ectc_monthly' => $application->ectc_monthly,
                        'ectc_yearly' => $application->ectc_yearly,
                        'status' => $application->status,
                        'skill_match' => $application->skill_match,
                        'existing' => $application->existing,
                        'placed_end_date' => $application->placed_end_date,
                        'rec_candidate_assign_id' => $application->rec_candidate_assign_id,
                        'resume' => $application->hashname,
                        'rating' => $application->rating,
                        'skype_id' => $application->skype_id,
                        'cover_letter' => $application->cover_letter,
                        'passport' => $application->passport,
                        'updated_at' => ($application->updated_at) ? Carbon::parse($application->updated_at)->toIso8601String() : null,
                        'created_at' => ($application->created_at) ? Carbon::parse($application->created_at)->toIso8601String() : null,
                        'of_stage_date' => ($application->of_stage_date) ? Carbon::parse($application->of_stage_date)->toIso8601String() : null,
                        'deleted_at' => ($application->deleted_at) ? Carbon::parse($application->created_at)->toIso8601String() : null,
                        'process_id' => $application->process_id,
                        'qualification' => $application->qualification,
                        'secondrary_email' => $application->secondrary_email,
                        'city' => $application->city,
                        'country' => $application->country,
                        'pincode' => $application->pincode,
                        'state' => $application->state,
                        'percentage_hike' => $application->percentage_hike,
                        'comment' => $application->comment,
                        'dob' => $application->dob,
                        'other_company' => $application->other_company,
                        'master_status_name' => $application->master_status_name,
                        'proposed_ctc' => $application->proposed_ctc,
                        'document' => $application->document,
                        'sub_for_approval' => $application->sub_for_approval,
                        'own_company_name' => $application->own_company_name,
                        'client_or_company_name' => strtolower($client_name_com),
                        'rjobid' => $application->rjobid,
                        'submitted_by' => $application->submitted_by,
                        'hashname' => $application->hashname,
                        'linkedin' => $application->linkedin,
                        'github' => $application->github,
                        'behance' => $application->behance,
                        'otherLink' => $application->otherLink,
                        'interviewDateOne' => ($application->interviewDateOne) ? Carbon::parse($application->interviewDateOne)->toIso8601String() : null,
                        'interview_mode_one' => $application->interview_mode_one,
                        'interviewDateTwo' => ($application->interviewDateTwo) ? Carbon::parse($application->interviewDateTwo)->toIso8601String() : null,
                        'interview_mode_two' => $application->interview_mode_two,
                        'interviewDateThree' => ($application->interviewDateThree) ? Carbon::parse($application->interviewDateThree)->toIso8601String() : null,
                        'interview_mode_three' => $application->interview_mode_three,
                        'interviewDateFour' => ($application->interviewDateFour) ? Carbon::parse($application->interviewDateFour)->toIso8601String() : null,
                        'interview_mode_four' => $application->interview_mode_four,
                        'interviewDateFive' => ($application->interviewDateFive) ? Carbon::parse($application->interviewDateFive)->toIso8601String() : null,
                        'interview_mode_five' => $application->interview_mode_five,
                        'interviewDateSix' => ($application->interviewDateSix) ? Carbon::parse($application->interviewDateSix)->toIso8601String() : null,
                        'interview_mode_six' => $application->interview_mode_six,
                        'schedule_date' => ($intRow) ? Carbon::parse($intRow->schedule_date)->toIso8601String() : null,
                        'timezone_abbreviation' => ($intRow) ? $intRow->timezone_abbreviation : null,
                        'schedule_to_time' => ($intRow) ? $intRow->schedule_to_time : null,
                    ];

                    if (++$count % $batchSize === 0) {
                        $this->elasticService->getClient()->bulk(['body' => $bulkData]);
                        $bulkData = []; // clear memory
                    }

                }

            }

            // dd($bulkData);
            if (! empty($bulkData)) {
                $this->elasticService->getClient()->bulk(['body' => $bulkData]);
            }
        }
        // dd($response);

        $searchQuery = $request->input('q');
        $page = max((int) $request->input('page', 1), 1);
        $size = (int) $request->input('size', 500); // Default 20 results per page
        $from = ($page - 1) * $size;
        $sort = [];
        $params = [
            'index' => ELASTIC_CANDIDATES,
            'body' => [
                'from' => $from,
                'size' => $size,
                'sort' => $sort,
            ],
        ];
        $response = $this->elasticService->getClient()->search($params);

        $total = $response['hits']['total']['value'] ?? 0;
        $data = array_map(function ($hit) {
            return $hit['_source'];
        }, $response['hits']['hits']);

    }

    public function candidateCareerApplyIndexElastic(Request $request)
    {

        // GeneralMethod::careerStore(1171);
        // dd("jf");

        if (! $this->elasticService->indexExists(ELASTIC_CAREER_APPLY)) {
            $params = [
                'index' => ELASTIC_CAREER_APPLY,
                'body' => [
                    'settings' => [
                        'number_of_shards' => 1,
                        'number_of_replicas' => 0,
                        'index.blocks.read_only_allow_delete' => false,
                        'index.blocks.write' => false,
                        'index.blocks.read' => false,
                        'index.blocks.metadata' => false,
                        'index.lifecycle.name' => null,
                        'index.lifecycle.rollover_alias' => null,
                        'index.requests.cache.enable' => true,
                        'analysis' => [
                            'analyzer' => [
                                'comma_analyzer' => [
                                    'type' => 'custom',
                                    'tokenizer' => 'pattern',
                                    'pattern' => '\\s*,\\s*', // Handles spaces around commas
                                    'filter' => ['lowercase', 'trim'],
                                ],
                                'number_list_analyzer' => [
                                    'type' => 'custom',
                                    'tokenizer' => 'number_list_tokenizer',
                                    'filter' => ['trim'],
                                ],
                            ],
                            'tokenizer' => [
                                'number_list_tokenizer' => [
                                    'type' => 'pattern',
                                    'pattern' => '\\s*,\\s*',
                                    'group' => -1,
                                ],
                            ],
                        ],
                    ],
                    'mappings' => [
                        'properties' => [
                            'email' => ['type' => 'keyword'],
                            'phone' => ['type' => 'keyword'],
                            'status_type' => ['type' => 'keyword'],
                            'ads_switch' => ['type' => 'keyword'],
                            'downloadCount' => ['type' => 'long'],
                            'total_exp' => ['type' => 'float'],
                            'rel_exp' => ['type' => 'float'],
                            'viewCount' => ['type' => 'long'],
                            'text' => ['type' => 'text', 'analyzer' => 'standard'],
                            'textUpdate' => ['type' => 'long'],
                            'linkedin' => ['type' => 'text'],
                            'github' => ['type' => 'text'],
                            'behance' => ['type' => 'text'],
                            'otherLink' => ['type' => 'text'],
                            'status' => ['type' => 'long'],
                            'interviewDateOne' => ['type' => 'date'],
                            'interview_mode_one' => ['type' => 'keyword'],
                            'interviewDateTwo' => ['type' => 'date'],
                            'interview_mode_two' => ['type' => 'keyword'],
                            'interviewDateThree' => ['type' => 'date'],
                            'interview_mode_three' => ['type' => 'keyword'],
                            'interviewDateFour' => ['type' => 'date'],
                            'interview_mode_four' => ['type' => 'keyword'],
                            'interviewDateFive' => ['type' => 'date'],
                            'interview_mode_five' => ['type' => 'keyword'],
                            'interviewDateSix' => ['type' => 'date'],
                            'dob' => ['type' => 'date'],
                            'submitted_by' => ['type' => 'long'],
                            'interview_mode_six' => ['type' => 'keyword'],
                            'secondrary_email' => ['type' => 'keyword', 'fields' => ['keyword' => ['type' => 'keyword']]],
                        ],
                    ],
                ],
            ];
            $this->elasticService->getClient()->cluster()->putSettings([
                'body' => [
                    'persistent' => [
                        'action.destructive_requires_name' => true,
                    ],
                ],
            ]);
            $response = $this->elasticService->getClient()->indices()->create($params);
        }

        $searchQuery = $request->input('q');
        $page = max((int) $request->input('page', 1), 1);
        $size = (int) $request->input('size', 500); // Default 20 results per page
        $from = ($page - 1) * $size;
        $sort = [];
        $params = [
            'index' => ELASTIC_CAREER_APPLY,
            'body' => [
                'from' => $from,
                'size' => $size,
                'sort' => $sort,
            ],
        ];
        $response = $this->elasticService->getClient()->search($params);

        $total = $response['hits']['total']['value'] ?? 0;
        $data = array_map(function ($hit) {
            return $hit['_source'];
        }, $response['hits']['hits']);

    }

    public function companyIndexElastic(Request $request)
    {

        // $params = [
        //     'index' => ELASTIC_COMPANIES_INDEX,
        //     'body' => [
        //         'properties' => [
        //             'accountVerify' => ['type' => 'integer'],
        //             ]
        //         ]
        // ];
        // $response = $this->elasticService->getClient()->indices()->putMapping($params);

        // dd($response);

        //   $coin_events = DB::table('companies')->get();
        //    $ky = 0;
        //     foreach ($coin_events as $row) {

        //         $response = $this->elasticService->getClient()->update([
        //                 'index' => ELASTIC_COMPANIES_INDEX,
        //                 'id'    => $row->id,
        // 				'retry_on_conflict' => 5,
        //                 'body'  => [
        //                     'doc' => [
        // 						'id' =>  $row->id,
        // 						'website' => $row->website,
        // 					],
        // 					'doc_as_upsert' => true,
        // 					'detect_noop' => false
        // 				],
        //             ]);
        //         $ky++;
        //     }
        //     dd($ky);

        $params = [
            'index' => ELASTIC_COMPANIES_INDEX,
            'id' => 2,
        ];

        $response = $this->elasticService->getClient()->get($params);
        $company_job = (object) $response['_source'];

        $CompanyAddEditUpload = GeneralMethod::CompanyAddEditUpload(2);

        $params = [
            'index' => ELASTIC_COMPANIES_INDEX,
            'body' => [
                'properties' => [
                    'website' => ['type' => 'text'],
                ],
            ],
        ];
        $response = $this->elasticService->getClient()->indices()->putMapping($params);

        if (! $this->elasticService->indexExists(ELASTIC_COMPANIES_INDEX)) {
            $params = [
                'index' => ELASTIC_COMPANIES_INDEX,
                'body' => [
                    'settings' => [
                        'number_of_shards' => 1,
                        'number_of_replicas' => 0,
                        'index.blocks.read_only_allow_delete' => false,
                        'index.blocks.write' => false,
                        'index.blocks.read' => false,
                        'index.blocks.metadata' => false,
                        'index.lifecycle.name' => null,
                        'index.lifecycle.rollover_alias' => null,
                        'index.requests.cache.enable' => true,
                        'analysis' => [
                            'analyzer' => [
                                'comma_analyzer' => [
                                    'type' => 'custom',
                                    'tokenizer' => 'comma_tokenizer',
                                    'filter' => ['lowercase', 'trim'],
                                ],
                                'number_list_analyzer' => [
                                    'type' => 'custom',
                                    'tokenizer' => 'number_list_tokenizer',
                                    'filter' => ['trim'],
                                ],
                            ],
                            'tokenizer' => [
                                'comma_tokenizer' => [
                                    'type' => 'pattern',
                                    'pattern' => '\\s*,\\s*',
                                ],
                                'number_list_tokenizer' => [
                                    'type' => 'pattern',
                                    'pattern' => '\\s*,\\s*',
                                ],
                            ],
                            'filter' => [
                                'trim' => [
                                    'type' => 'trim',
                                ],
                            ],
                        ],
                    ],
                    'mappings' => [
                        'properties' => [
                            'id' => ['type' => 'long'],
                            'leadCount' => ['type' => 'long'],
                            'status' => ['type' => 'keyword'],
                            'boostkeywords_skills' => [
                                'type' => 'nested',
                                'properties' => [
                                    'id' => ['type' => 'long'],
                                    'campaign_name' => ['type' => 'text', 'fields' => ['keyword' => ['type' => 'keyword']]],
                                    'campaign_type' => ['type' => 'long'],
                                    'clickCount' => ['type' => 'long'],
                                    'company_id' => ['type' => 'long'],
                                    'country_id' => ['type' => 'long'],
                                    'skill_name' => ['type' => 'text', 'fields' => ['keyword' => ['type' => 'keyword']]],
                                    'status' => ['type' => 'long'],
                                ],
                            ],
                            'job_metronics_skills' => [
                                'type' => 'nested',
                                'properties' => [
                                    'id' => ['type' => 'long'],
                                    'company_id' => ['type' => 'long'],
                                    'job_id' => ['type' => 'long'],
                                    'skill_name' => ['type' => 'text', 'fields' => ['keyword' => ['type' => 'keyword']]],
                                ],
                            ],
                            'company_coins' => [
                                'type' => 'nested',
                                'properties' => [
                                    'id' => ['type' => 'long'],
                                    'actual_coins' => ['type' => 'float'],
                                    'amount' => ['type' => 'float'],
                                    'auto_job_coins' => ['type' => 'date'],
                                    'auto_team_coins' => ['type' => 'date'],
                                    'balance_coin' => ['type' => 'float'],
                                    'package_name' => ['type' => 'text'],
                                    'package_type' => ['type' => 'long'],
                                    'coin_order_id' => ['type' => 'long'],
                                    'company_id' => ['type' => 'long'],
                                    'coins' => ['type' => 'float'],
                                    'created_at' => ['type' => 'date'],
                                    'updated_at' => ['type' => 'date'],
                                    'currency' => ['type' => 'keyword'],
                                    'discount' => ['type' => 'float'],
                                    'end_date' => ['type' => 'date'],
                                    'event_type' => ['type' => 'keyword'],
                                    'free_pack_status' => ['type' => 'keyword'],
                                    'free_update_date' => ['type' => 'date'],
                                    'start_date' => ['type' => 'date'],
                                    'status' => ['type' => 'keyword'],
                                ],
                            ],
                        ],
                    ],
                ],
            ];

            $this->elasticService->getClient()->cluster()->putSettings([
                'body' => [
                    'persistent' => [
                        'action.destructive_requires_name' => true,
                    ],
                ],
            ]);

            $response = $this->elasticService->getClient()->indices()->create($params);
        }
        $comid = '1';

        $companies = Cache::remember("vendor_ids_for_company_{$comid}", 300, function () {
            return DB::table('companies')
                ->select('companies.*', 'td_industries.ind_name as industry_name')
                ->leftJoin('td_industries', 'td_industries.ind_id', '=', 'companies.industries')
                // ->where('id', '!=', $comid)
                ->get();
        });
        // dd($companies);

        $bulkData = [];
        foreach ($companies as $company) {
            $company_id = $company->id;

            $industry = DB::table('td_industries')
                ->where('ind_id', $company->industries)
                ->first();

            $company_coins = DB::table('company_coins')
                ->where('company_id', $company_id)
                ->get()
                ->map(function ($c) {
                    $coin_orders = DB::table('coin_orders')
                        ->join('coin_package_types', 'coin_package_types.id', '=', 'coin_orders.coin_package_id')
                        ->join('coin_packages', 'coin_packages.coin_package_type_id', '=', 'coin_package_types.id')
                        ->where('coin_orders.id', $c->coin_order_id)
                        ->where('coin_orders.status', 1)
                        ->select('coin_package_types.title', 'coin_packages.custome_pck')
                        ->first();
                    $package_name = null;
                    $package_type = null;
                    // dd($coin_orders);
                    if ($coin_orders) {
                        $package_name = @$coin_orders->title;
                        $package_type = @$coin_orders->custome_pck;
                    }

                    return [
                        'id' => (int) $c->id,
                        'company_id' => (int) $c->company_id,
                        'coins' => (float) $c->coins,
                        'actual_coins' => (float) $c->actual_coins,
                        'balance_coin' => (float) $c->balance_coin,
                        'package_name' => $package_name,
                        'package_type' => (int) $package_type,
                        'amount' => (float) $c->amount,
                        'discount' => (float) $c->discount,
                        'currency' => ($c->currency),
                        'coin_order_id' => (int) $c->coin_order_id,
                        'status' => ($c->status),
                        'event_type' => ($c->event_type),
                        'free_pack_status' => ($c->free_pack_status),
                        'start_date' => ($c->start_date) ? Carbon::parse($c->start_date)->toIso8601String() : null,
                        'end_date' => ($c->end_date) ? Carbon::parse($c->end_date)->toIso8601String() : null,
                        'auto_job_coins' => ($c->auto_job_coins) ? Carbon::parse($c->auto_job_coins)->toIso8601String() : null,
                        'auto_team_coins' => ($c->auto_team_coins) ? Carbon::parse($c->auto_team_coins)->toIso8601String() : null,
                        'free_update_date' => ($c->free_update_date) ? Carbon::parse($c->free_update_date)->toIso8601String() : null,
                        'created_at' => ($c->created_at) ? Carbon::parse($c->created_at)->toIso8601String() : null,
                        'updated_at' => ($c->updated_at) ? Carbon::parse($c->updated_at)->toIso8601String() : null,
                    ];
                })
                ->toArray();

            $commericial_countries = DB::table('commericial_countries')
                ->where('company_id', $company_id)
                ->get()
                ->map(fn ($row) => (array) $row)
                ->toArray();

            $team_members = DB::table('users')
                ->where('company_id', $company_id)
                ->where('root_user', '!=', 1)
                ->get()
                ->map(fn ($row) => (array) $row)
                ->toArray();

            $clients = DB::table('clients')
                ->where('company_id', $company_id)
                ->where('self_company', '=', 1)
                ->get()
                ->map(function ($client) {
                    $address = DB::table('client_location')
                        ->leftJoin('countries', 'countries.id', '=', 'client_location.country_id')
                        ->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')
                        ->leftJoin('state_list', 'state_list.id', '=', 'client_location.state')
                        ->select('city_list.city as city_name', 'state_list.state as state_name', 'countries.country_name')
                        ->where('client_id', $client->id)
                        ->get()
                        ->map(fn ($s) => [
                            'city_name' => $s->city_name,
                            'state_name' => $s->state_name,
                            'country_name' => $s->country_name,
                        ])
                        ->toArray();

                    return [
                        'locations_address' => $address,
                    ];
                })
                ->toArray();

            $job_metronics_skills = DB::table('job_metronics_skills')
                ->where('company_id', $company_id)
                ->get()
                ->map(function ($s) {
                    return [
                        'id' => (int) $s->id,
                        'job_id' => (int) $s->job_id,
                        'company_id' => (int) $s->company_id,
                        'skill_name' => strtolower($s->skill_name),
                    ];
                })
                ->toArray();

            $boostkeywords_skills = DB::table('boostkeywords')
                ->where('company_id', $company_id)
                ->get()
                ->map(function ($s) {
                    return [
                        'id' => (int) $s->id,
                        'campaign_name' => strtolower($s->campaign_name),
                        'campaign_type' => $s->campaign_type,
                        'clickCount' => $s->clickCount,
                        'company_id' => $s->company_id,
                        'country_id' => $s->country_id,
                        'skill_name' => strtolower($s->skill_name),
                        'status' => $s->status,
                    ];
                })
                ->toArray();

            $staffing_relations = DB::table('staffing_company_relation')
                ->where('from_company_id', $company_id)
                ->orWhere('to_company_id', $company_id)
                ->get()
                ->map(fn ($row) => (array) $row)
                ->toArray();

            $recommendation_relations = DB::table('recommendation_relation')
                ->where('from_company_id', $company_id)
                ->orWhere('to_company_id', $company_id)
                ->get()
                ->map(fn ($row) => (array) $row)
                ->toArray();

            $jobs = DB::table('jobs')
                ->leftJoin('recommendation_relation', 'recommendation_relation.job_id', '=', 'jobs.id')
                ->where(function ($query) use ($company_id) {
                    $query->where(function ($q) use ($company_id) {
                        $q->where('jobs.company_id', $company_id);
                    })->orWhere(function ($q) use ($company_id) {
                        $q->where('recommendation_relation.to_company_id', $company_id)
                            ->where('recommendation_relation.final_status', '>', 0);
                    });
                })
                ->get()
                ->map(function ($job) {
                    $skills = DB::table('job_metronics_skills')
                        ->where('job_id', $job->id)
                        ->get()
                        ->map(fn ($s) => [
                            'skill_id' => $s->id,
                            'skill_name' => strtolower($s->skill_name),
                        ])
                        ->toArray();

                    return [
                        'job_id' => $job->id,
                        'job_title' => $job->title,
                        'employment_type' => $job->employment_type,
                        'status' => $job->status,
                        'skills' => $skills,
                    ];
                })
                ->toArray();

            $candidates = DB::table('add_job_wise_condidates')
                ->join('job_applications', 'job_applications.id', '=', 'add_job_wise_condidates.job_application_id')
                ->select('job_applications.id', 'job_applications.full_name', 'job_applications.condidate_type', 'add_job_wise_condidates.master_status_id')
                ->where('add_job_wise_condidates.company_id', $company_id)
                ->get()
                ->map(function ($c) {
                    $skills = DB::table('job_application_skills')
                        ->join('job_applications', 'job_applications.id', '=', 'job_application_skills.job_application_id')
                        ->select('job_application_skills.*')
                        ->where('job_application_skills.job_application_id', $c->id)
                        ->get()
                        ->map(fn ($s) => [
                            'skill_id' => $s->id,
                            'skill_name' => strtolower($s->skills),
                        ])
                        ->toArray();

                    return [
                        'candidate_id' => $c->id,
                        'candidate_name' => $c->full_name,
                        'profile_type' => $c->condidate_type,
                        'skills' => $skills,
                        'master_status_id' => $c->master_status_id,
                    ];
                })
                ->toArray();

            $bench_candidates = DB::table('job_applications')
                ->select('job_applications.id', 'job_applications.full_name', 'job_applications.condidate_type', 'job_applications.status')
                ->where('job_applications.company_id', $company_id)
                ->where('job_applications.condidate_type', '!=', 'other')
                ->get()
                ->map(function ($c) {
                    $skills = DB::table('job_application_skills')
                        ->join('job_applications', 'job_applications.id', '=', 'job_application_skills.job_application_id')
                        ->select('job_application_skills.*')
                        ->where('job_application_skills.job_application_id', $c->id)
                        ->get()
                        ->map(fn ($s) => [
                            'skill_id' => $s->id,
                            'skill_name' => strtolower($s->skills),
                        ])
                        ->toArray();

                    return [
                        'candidate_id' => $c->id,
                        'candidate_name' => $c->full_name,
                        'profile_type' => $c->condidate_type,
                        'skills' => $skills,
                        'status' => $c->status,
                    ];
                })
                ->toArray();

            $commericials = DB::table('commericials')
                ->where('company_id', $company_id)
                ->get()
                ->map(function ($comm) {
                    $details = DB::table('commericial_details')
                        ->where('commericial_id', $comm->id)
                        ->get()
                        ->map(fn ($d) => [
                            'exp' => $d->exp,
                            'rate' => $d->rate,
                            'commtype' => $d->commtype,
                        ])
                        ->toArray();

                    return [
                        'id' => $comm->id,
                        'rateType' => $comm->rateType,
                        'status' => $comm->status,
                        'commtype' => $comm->commtype,
                        'sal_mode' => $comm->sal_mode,
                        'salary_currency' => $comm->salary_currency,
                        'commericial_details' => $details,
                    ];
                })
                ->toArray();

            $response = $this->elasticService->getClient()->update([
                'index' => ELASTIC_COMPANIES_INDEX,
                'id' => $company_id,
                'retry_on_conflict' => 5,
                'body' => [
                    'doc' => [
                        'id' => $company_id,
                        'company_name' => $company->company_name,
                        'logo' => $company->logo,
                        'description' => $company->description,
                        'company_type' => $company->company_type,
                        'leadCount' => $company->leadCount,
                        'last_login' => $company->last_login ? Carbon::parse($company->last_login)->toIso8601String() : null,
                        'industry' => $industry ? [
                            'ind_id' => $industry->ind_id,
                            'industry_name' => $industry->ind_name,
                        ] : null,
                        'company_coins' => $company_coins ?? [],
                        'commericial_countries' => $commericial_countries,
                        'team_members' => $team_members,
                        'locations_address' => $clients,
                        'job_metronics_skills' => $job_metronics_skills,
                        'boostkeywords_skills' => $boostkeywords_skills,
                        'staffing_company_relation' => $staffing_relations,
                        'recommendation_relation' => $recommendation_relations,
                        'status' => $company->status,
                        'jobs' => $jobs,
                        'candidates' => $candidates,
                        'bench_candidates' => $bench_candidates,
                        'commericials' => $commericials,
                        'created_at' => ($company->created_at) ? Carbon::parse($company->created_at)->toIso8601String() : null,
                        'updated_at' => ($company->updated_at) ? Carbon::parse($company->updated_at)->toIso8601String() : null,
                    ],
                    'doc_as_upsert' => true,
                    'detect_noop' => false,
                ],
            ]);
        }

        // dd($response);

        $searchQuery = $request->input('q');
        $page = max((int) $request->input('page', 1), 1);
        $size = (int) $request->input('size', 500); // Default 20 results per page
        $from = ($page - 1) * $size;
        $sort = [];
        $params = [
            'index' => ELASTIC_COMPANIES_INDEX,
            'body' => [
                'from' => $from,
                'size' => $size,
                'sort' => $sort,
            ],
        ];
        $response = $this->elasticService->getClient()->search($params);

        $total = $response['hits']['total']['value'] ?? 0;
        $data = array_map(function ($hit) {
            return $hit['_source'];
        }, $response['hits']['hits']);

    }

    public function locationIndexElastic(Request $request)
    {

        // $params = [
        //     'index' => ELASTIC_LOCATIONS,
        //     'id'    => 134054, // your document ID
        //     'body'  => [
        //         'doc' => [
        //             'city_name' => 'suryapet'
        //         ]
        //     ]
        // ];

        // $red = $this->elasticService->getClient()->update($params);
        // dd($red);

        // $term = "suryapet";

        //     $shouldClauses[] = [
        //         'wildcard' => [
        //             'city_name' => '*' . strtolower($term) . '*'
        //         ]
        //     ];

        // $params = [
        //     'index' => ELASTIC_LOCATIONS,
        //     'body' => [
        //         'size' => 100,
        //         'sort' => [
        //             ['city_name' => 'asc']
        //         ],
        //         'query' => [
        //             'bool' => [
        //                 'should' => $shouldClauses,
        //                 'minimum_should_match' => 1
        //             ]
        //         ]
        //     ]
        // ];

        // $response = $this->elasticService->getClient()->search($params);

        // $cities = collect($response['hits']['hits'])->map(function ($hit) {
        //     $src = $hit['_source'];
        //     return ucwords($src['id']) . ' | ' . ($src['city_name']) . ' | ' . ucwords($src['state_name']) . ' | ' . ucwords($src['country_name']);
        // });
        // dd($cities);

        if (! $this->elasticService->indexExists(ELASTIC_LOCATIONS)) {
            $params = [
                'index' => ELASTIC_LOCATIONS,
                'body' => [
                    'settings' => [
                        'number_of_shards' => 1,
                        'number_of_replicas' => 0,
                        'index.blocks.read_only_allow_delete' => false,
                        'index.blocks.write' => false,
                        'index.blocks.read' => false,
                        'index.blocks.metadata' => false,
                        'index.lifecycle.name' => null,
                        'index.lifecycle.rollover_alias' => null,
                        'index.requests.cache.enable' => true,
                        'analysis' => [
                            'analyzer' => [
                                'comma_analyzer' => [
                                    'type' => 'custom',
                                    'tokenizer' => 'pattern',
                                    'pattern' => '\\s*,\\s*', // Handles spaces around commas
                                    'filter' => ['lowercase', 'trim'],
                                ],
                                'number_list_analyzer' => [
                                    'type' => 'custom',
                                    'tokenizer' => 'number_list_tokenizer',
                                    'filter' => ['trim'],
                                ],
                            ],
                            'tokenizer' => [
                                'number_list_tokenizer' => [
                                    'type' => 'pattern',
                                    'pattern' => '\\s*,\\s*',
                                    'group' => -1,
                                ],
                            ],
                        ],
                    ],
                    'mappings' => [
                        'properties' => [
                            'id' => ['type' => 'long'],
                            'city_name' => ['type' => 'keyword'],
                            'state_name' => ['type' => 'keyword'],
                            'country_name' => ['type' => 'keyword'],
                        ],
                    ],
                ],
            ];
            $this->elasticService->getClient()->cluster()->putSettings([
                'body' => [
                    'persistent' => [
                        'action.destructive_requires_name' => true,
                    ],
                ],
            ]);
            $response = $this->elasticService->getClient()->indices()->create($params);
            // dd($response);
        }

        $cities = DB::table('city_list')
            ->join('state_list', 'state_list.id', '=', 'city_list.state_id')
            ->join('countries', 'countries.id', '=', 'state_list.country_id')
            ->select(
                'city_list.city_id as id',
                'city_list.city as city_name',
                'state_list.state as state_name',
                'countries.country_name'
            )
            ->where('city_list.city', '!=', '')
            ->orderBy('city_list.city')
            ->cursor();

        $bulkData = [];
        $batchSize = 1000;
        $count = 0;

        foreach ($cities as $row) {
            $exists = $this->elasticService->getClient()->exists([
                'index' => ELASTIC_LOCATIONS,
                'id' => $row->id,
            ]);
            if (! $exists) {
                $bulkData[] = [
                    'index' => [
                        '_index' => ELASTIC_LOCATIONS,
                        '_id' => $row->id,
                    ],
                ];

                $bulkData[] = [
                    'id' => $row->id,
                    'city_name' => strtolower($row->city_name),
                    'state_name' => strtolower($row->state_name),
                    'country_name' => strtolower($row->country_name),
                ];

                // If batch size reached, send to Elasticsearch
                if (++$count % $batchSize === 0) {
                    $this->elasticService->getClient()->bulk(['body' => $bulkData]);
                    $bulkData = []; // clear memory
                }
            }
        }

        if (! empty($bulkData)) {
            $this->elasticService->getClient()->bulk(['body' => $bulkData]);
        }

    }

    public function teamRankElastic(Request $request)
    {

        if (! $this->elasticService->indexExists(ELASTIC_TEAM_RANK)) {
            $params = [
                'index' => ELASTIC_TEAM_RANK,
                'body' => [
                    'settings' => [
                        'number_of_shards' => 1,
                        'number_of_replicas' => 0,
                        'index.blocks.read_only_allow_delete' => false,
                        'index.blocks.write' => false,
                        'index.blocks.read' => false,
                        'index.blocks.metadata' => false,
                        'index.lifecycle.name' => null,
                        'index.lifecycle.rollover_alias' => null,
                        'index.requests.cache.enable' => true,
                        'analysis' => [
                            'analyzer' => [
                                'comma_analyzer' => [
                                    'type' => 'custom',
                                    'tokenizer' => 'pattern',
                                    'pattern' => '\\s*,\\s*', // Handles spaces around commas
                                    'filter' => ['lowercase', 'trim'],
                                ],
                                'number_list_analyzer' => [
                                    'type' => 'custom',
                                    'tokenizer' => 'number_list_tokenizer',
                                    'filter' => ['trim'],
                                ],
                            ],
                            'tokenizer' => [
                                'number_list_tokenizer' => [
                                    'type' => 'pattern',
                                    'pattern' => '\\s*,\\s*',
                                    'group' => -1,
                                ],
                            ],
                        ],
                    ],
                    'mappings' => [
                        'properties' => [
                            'id' => ['type' => 'long'],
                            'company_id' => ['type' => 'long'],
                            'user_id' => ['type' => 'long'],
                            'candidate_id' => ['type' => 'long'],
                            'profileUpload' => ['type' => 'long'], // +1 point
                            'interviewScheduled' => ['type' => 'long'], // +10 points
                            'interviewNoShow' => ['type' => 'long'], // –5 points
                            'offerReleased' => ['type' => 'long'], // +300 points
                            'candidateJoined' => ['type' => 'long'], // +500 points
                            'jobsPosted' => ['type' => 'long'], // +2 pts per job
                            'feedbackGivenOnCVs' => ['type' => 'long'], // +3 pts per feedback
                            'postJobToVendorMarket' => ['type' => 'long'], // +2 pts per vendor post
                            'outsourceJobsToVendors' => ['type' => 'long'], // +5 pts per vendor assignment
                            'listBenchCandidates' => ['type' => 'long'], // +3 pts per candidate listed
                            'respondToBenchRequests' => ['type' => 'long'], // +4 pts per response
                            'created_at' => ['type' => 'date'],
                            'updated_at' => ['type' => 'date'],
                            'interestedCandidates' => ['type' => 'long'], // +2 point
                            'notInterestedCandidates' => ['type' => 'long'], // -1 point
                            'teamCaptainInterviewScheduled' => ['type' => 'long'], // +2 point
                            'teamCaptainInterviewNoShow' => ['type' => 'long'], // -1 point
                            'teamCaptainOfferReleased' => ['type' => 'long'], // +20 point
                            'sourcing' => ['type' => 'long'], // +1 point
                            'screening' => ['type' => 'long'], // +1 point
                            'evaluation' => ['type' => 'long'], // +1 point
                            'negotiation' => ['type' => 'long'], // +1 point
                            'backout' => ['type' => 'long'], // join point
                            'backoutTeam' => ['type' => 'long'], // join point
                            'teamCaptainCandidateJoined' => ['type' => 'text'], // +30 point
                        ],
                    ],
                ],
            ];
            $this->elasticService->getClient()->cluster()->putSettings([
                'body' => [
                    'persistent' => [
                        'action.destructive_requires_name' => true,
                    ],
                ],
            ]);
            $response = $this->elasticService->getClient()->indices()->create($params);

        }

        // $params = [
        //      'index' => ELASTIC_TEAM_RANK,
        //      'body' => [
        //          'properties' => [
        //             'sourcing' => ['type' => 'long'], //+1 point
        //             'screening' => ['type' => 'long'], //+1 point
        //             'evaluation' => ['type' => 'long'], //+1 point
        //             'negotiation' => ['type' => 'long'], //+1 point
        //             'backout' => ['type' => 'long'], //join point
        //             'backoutTeam' => ['type' => 'long'], //join point
        //          ]
        //      ]
        // ];

        // $response = $this->elasticService->getClient()->indices()->putMapping($params);

        // dd( $response );

        $searchQuery = $request->input('q');
        $page = max((int) $request->input('page', 1), 1);
        $size = (int) $request->input('size', 500); // Default 20 results per page
        $from = ($page - 1) * $size;
        $sort = [
            ['doc.created_at' => ['order' => 'desc']],
        ];
        $params = [
            'index' => ELASTIC_TEAM_RANK,
            'body' => [
                'from' => $from,
                'size' => $size,
                'sort' => $sort,
            ],
        ];
        $response = $this->elasticService->getClient()->search($params);

        $total = $response['hits']['total']['value'] ?? 0;
        $data = array_map(function ($hit) {
            return $hit['_source'];
        }, $response['hits']['hits']);

    }

    public function skillskElastic(Request $request)
    {

        $data = DB::table('job_metronics_skills')->select('skill_name', 'created_at', 'updated_at')->get();
        $count = 0;

        if (! $this->elasticService->indexExists(ELASTIC_SKILLS)) {

            $params = [
                'index' => ELASTIC_SKILLS,
                'body' => [
                    'settings' => [
                        'number_of_shards' => 1,
                        'number_of_replicas' => 0,
                        'index.blocks.read_only_allow_delete' => false,
                        'index.blocks.write' => false,
                        'index.blocks.read' => false,
                        'index.blocks.metadata' => false,
                        'index.lifecycle.name' => null,
                        'index.lifecycle.rollover_alias' => null,
                        'index.requests.cache.enable' => true,
                        'analysis' => [
                            'analyzer' => [
                                'comma_analyzer' => [
                                    'type' => 'custom',
                                    'tokenizer' => 'pattern',
                                    'pattern' => '\\s*,\\s*', // Handles spaces around commas
                                    'filter' => ['lowercase', 'trim'],
                                ],
                                'number_list_analyzer' => [
                                    'type' => 'custom',
                                    'tokenizer' => 'number_list_tokenizer',
                                    'filter' => ['trim'],
                                ],
                            ],
                            'tokenizer' => [
                                'number_list_tokenizer' => [
                                    'type' => 'pattern',
                                    'pattern' => '\\s*,\\s*',
                                    'group' => -1,
                                ],
                            ],
                        ],
                    ],
                    'mappings' => [
                        'properties' => [
                            'id' => ['type' => 'keyword'],   // <-- add id
                            'skill_name' => ['type' => 'keyword'],
                            'created_at' => ['type' => 'date'],
                            'updated_at' => ['type' => 'date'],
                        ],
                    ],
                ],
            ];
            $this->elasticService->getClient()->cluster()->putSettings([
                'body' => [
                    'persistent' => [
                        'action.destructive_requires_name' => true,
                    ],
                ],
            ]);
            $response = $this->elasticService->getClient()->indices()->create($params);
        }

        foreach ($data as $row) {

            $skill_name = strtolower(trim($row->skill_name));

            $result = $this->elasticService->getClient()->search([
                'index' => ELASTIC_SKILLS,
                'body' => [
                    'query' => [
                        'term' => [
                            'skill_name' => ['value' => $skill_name],
                        ],
                    ],
                ],
            ]);

            // Skip if already exists
            if ($result['hits']['total']['value'] > 0) {
                // $this->elasticService->getClient()->line("⚠️ Skipped duplicate: {$skill_name}");

                continue;
            }

            $customId = uniqid('skill_', true); // e.g., skill_64f9a7b3a12c4
            $response = $this->elasticService->getClient()->index([
                'index' => ELASTIC_SKILLS,
                'id' => $customId,   // store as ES _id
                'body' => [
                    'skill_name' => $skill_name,
                    'created_at' => now()->toIso8601String(),
                    'updated_at' => now()->toIso8601String(),
                ],
            ]);
        }

        $searchQuery = $request->input('q');
        $page = max((int) $request->input('page', 1), 1);
        $size = (int) $request->input('size', 500); // Default 20 results per page
        $from = ($page - 1) * $size;
        $sort = [
            ['created_at' => ['order' => 'desc']],
        ];
        $params = [
            'index' => ELASTIC_SKILLS,
            'body' => [
                'from' => $from,
                'size' => $size,
                'sort' => $sort,
            ],
        ];
        $response = $this->elasticService->getClient()->search($params);

        $total = $response['hits']['total']['value'] ?? 0;
        $data = array_map(function ($hit) {
            return array_merge(
                ['id' => $hit['_id']],   // include ES id
                $hit['_source']
            );
        }, $response['hits']['hits']);

    }

    public function candidateSkillskExpElastic(Request $request)
    {

        $data = DB::table('candidate_job_skill_exp')->get();

        $count = 0;

        if (! $this->elasticService->indexExists(ELASTIC_CANDIDATE_SKILLS_EXP)) {

            $params = [
                'index' => ELASTIC_CANDIDATE_SKILLS_EXP,
                'body' => [
                    'settings' => [
                        'number_of_shards' => 1,
                        'number_of_replicas' => 0,
                        'index.blocks.read_only_allow_delete' => false,
                        'index.blocks.write' => false,
                        'index.blocks.read' => false,
                        'index.blocks.metadata' => false,
                        'index.lifecycle.name' => null,
                        'index.lifecycle.rollover_alias' => null,
                        'index.requests.cache.enable' => true,
                        'analysis' => [
                            'analyzer' => [
                                'comma_analyzer' => [
                                    'type' => 'custom',
                                    'tokenizer' => 'pattern',
                                    'pattern' => '\\s*,\\s*', // Handles spaces around commas
                                    'filter' => ['lowercase', 'trim'],
                                ],
                                'number_list_analyzer' => [
                                    'type' => 'custom',
                                    'tokenizer' => 'number_list_tokenizer',
                                    'filter' => ['trim'],
                                ],
                            ],
                            'tokenizer' => [
                                'number_list_tokenizer' => [
                                    'type' => 'pattern',
                                    'pattern' => '\\s*,\\s*',
                                    'group' => -1,
                                ],
                            ],
                        ],
                    ],
                    'mappings' => [
                        'properties' => [
                            'id' => ['type' => 'long'],   // <-- add id
                            'job_skill_id' => ['type' => 'long'],
                            'skill_name' => ['type' => 'keyword'],
                            'skill_value' => ['type' => 'keyword'],
                            'job_id' => ['type' => 'long'],
                            'candidate_id' => ['type' => 'long'],
                            'created_at' => ['type' => 'date'],
                            'updated_at' => ['type' => 'date'],
                        ],
                    ],
                ],
            ];
            $this->elasticService->getClient()->cluster()->putSettings([
                'body' => [
                    'persistent' => [
                        'action.destructive_requires_name' => true,
                    ],
                ],
            ]);
            $response = $this->elasticService->getClient()->indices()->create($params);
        }

        // foreach ($data as $row) {

        //      $skills = DB::table('job_metronics_skills')->select('skill_name')->where('id', '=', $row->job_skill_id)->first();
        //     //   dd($row,$skills->skill_name);

        //     $customId = uniqid('skill_', true); // e.g., skill_64f9a7b3a12c4
        //     $response = $this->elasticService->getClient()->index([
        //             'index' => ELASTIC_CANDIDATE_SKILLS_EXP,
        //             'id'    =>$row->id,   // store as ES _id
        //             'body'  => [
        //                 'skill_name' => $skills->skill_name ?? '',
        //                 'job_skill_id' => $row->job_skill_id ?? '',
        //                 'skill_value' => $row->skill_value ?? '',
        //                 'job_id' => $row->job_id ?? '',
        //                 'candidate_id' => $row->candidate_id ?? '',
        //                 'created_at' =>  now()->toIso8601String(),
        //                 'updated_at' =>  now()->toIso8601String()
        //             ]
        //         ]);
        // }

        $searchQuery = $request->input('q');
        $page = max((int) $request->input('page', 1), 1);
        $size = (int) $request->input('size', 500); // Default 20 results per page
        $from = ($page - 1) * $size;
        $sort = [
            ['created_at' => ['order' => 'desc']],
        ];
        $params = [
            'index' => ELASTIC_CANDIDATE_SKILLS_EXP,
            'body' => [
                'from' => $from,
                'size' => $size,
                'sort' => $sort,
            ],
        ];
        $response = $this->elasticService->getClient()->search($params);

        $total = $response['hits']['total']['value'] ?? 0;
        $data = array_map(function ($hit) {
            return array_merge(
                ['id' => $hit['_id']],   // include ES id
                $hit['_source']
            );
        }, $response['hits']['hits']);

    }

    public function candidateJobQuestionElastic(Request $request)
    {

        $data = DB::table('application_additional_detail')->get();

        $count = 0;

        if (! $this->elasticService->indexExists(ELASTIC_CANDIDATE_JOB_QUESTION)) {

            $params = [
                'index' => ELASTIC_CANDIDATE_JOB_QUESTION,
                'body' => [
                    'settings' => [
                        'number_of_shards' => 1,
                        'number_of_replicas' => 0,
                        'index.blocks.read_only_allow_delete' => false,
                        'index.blocks.write' => false,
                        'index.blocks.read' => false,
                        'index.blocks.metadata' => false,
                        'index.lifecycle.name' => null,
                        'index.lifecycle.rollover_alias' => null,
                        'index.requests.cache.enable' => true,
                        'analysis' => [
                            'analyzer' => [
                                'comma_analyzer' => [
                                    'type' => 'custom',
                                    'tokenizer' => 'pattern',
                                    'pattern' => '\\s*,\\s*', // Handles spaces around commas
                                    'filter' => ['lowercase', 'trim'],
                                ],
                                'number_list_analyzer' => [
                                    'type' => 'custom',
                                    'tokenizer' => 'number_list_tokenizer',
                                    'filter' => ['trim'],
                                ],
                            ],
                            'tokenizer' => [
                                'number_list_tokenizer' => [
                                    'type' => 'pattern',
                                    'pattern' => '\\s*,\\s*',
                                    'group' => -1,
                                ],
                            ],
                        ],
                    ],
                    'mappings' => [
                        'properties' => [
                            'id' => ['type' => 'long'],
                            'job_id' => ['type' => 'long'],
                            'application_id' => ['type' => 'long'],
                            'question_id' => ['type' => 'long'],
                            'job_detail_value' => ['type' => 'keyword'],
                            'application_detail_value' => ['type' => 'keyword'],
                            'created_at' => ['type' => 'date'],
                            'updated_at' => ['type' => 'date'],
                        ],
                    ],
                ],
            ];
            $this->elasticService->getClient()->cluster()->putSettings([
                'body' => [
                    'persistent' => [
                        'action.destructive_requires_name' => true,
                    ],
                ],
            ]);
            $response = $this->elasticService->getClient()->indices()->create($params);
        }

        foreach ($data as $row) {

            $response = $this->elasticService->getClient()->index([
                'index' => ELASTIC_CANDIDATE_JOB_QUESTION,
                'id' => $row->id,   // store as ES _id
                'body' => [
                    'job_id' => $row->job_id ?? '',
                    'application_id' => $row->application_id ?? '',
                    'question_id' => $row->question_id ?? '',
                    'job_detail_value' => $row->job_detail_value ?? '',
                    'application_detail_value' => $row->application_detail_value ?? '',
                    'created_at' => now()->toIso8601String(),
                    'updated_at' => now()->toIso8601String(),
                ],
            ]);
        }

        $searchQuery = $request->input('q');
        $page = max((int) $request->input('page', 1), 1);
        $size = (int) $request->input('size', 500); // Default 20 results per page
        $from = ($page - 1) * $size;
        $sort = [
            ['created_at' => ['order' => 'desc']],
        ];
        $params = [
            'index' => ELASTIC_CANDIDATE_JOB_QUESTION,
            'body' => [
                'from' => $from,
                'size' => $size,
                'sort' => $sort,
            ],
        ];
        $response = $this->elasticService->getClient()->search($params);

        $total = $response['hits']['total']['value'] ?? 0;
        $data = array_map(function ($hit) {
            return array_merge(
                ['id' => $hit['_id']],   // include ES id
                $hit['_source']
            );
        }, $response['hits']['hits']);

    }

    public function coinEventsElastic(Request $request)
    {

        if (! $this->elasticService->indexExists(ELASTIC_COIN_EVENTS)) {
            $params = [
                'index' => ELASTIC_COIN_EVENTS,
                'body' => [
                    'settings' => [
                        'number_of_shards' => 1,
                        'number_of_replicas' => 0,
                        'index.blocks.read_only_allow_delete' => false,
                        'index.blocks.write' => false,
                        'index.blocks.read' => false,
                        'index.blocks.metadata' => false,
                        'index.lifecycle.name' => null,
                        'index.lifecycle.rollover_alias' => null,
                        'index.requests.cache.enable' => true,
                        'analysis' => [
                            'analyzer' => [
                                'comma_analyzer' => [
                                    'type' => 'custom',
                                    'tokenizer' => 'comma_tokenizer',
                                    'filter' => ['lowercase', 'trim'],
                                ],
                                'number_list_analyzer' => [
                                    'type' => 'custom',
                                    'tokenizer' => 'number_list_tokenizer',
                                    'filter' => ['trim'],
                                ],
                            ],
                            'tokenizer' => [
                                'comma_tokenizer' => [
                                    'type' => 'pattern',
                                    'pattern' => '\\s*,\\s*',
                                ],
                                'number_list_tokenizer' => [
                                    'type' => 'pattern',
                                    'pattern' => '\\s*,\\s*',
                                ],
                            ],
                            'filter' => [
                                'trim' => [
                                    'type' => 'trim',
                                ],
                            ],
                        ],
                    ],
                    'mappings' => [
                        'properties' => [
                            'id' => ['type' => 'long'],
                            'event_type' => ['type' => 'keyword'],
                            'title' => ['type' => 'text'],
                            'default_rate' => ['type' => 'double'],
                            'description' => ['type' => 'text'],
                            'deduction_type' => ['type' => 'keyword'],
                            'minimum_required' => ['type' => 'double'],
                            'status' => ['type' => 'integer'],
                        ],
                    ],
                ],
            ];

            $this->elasticService->getClient()->cluster()->putSettings([
                'body' => [
                    'persistent' => [
                        'action.destructive_requires_name' => true,
                    ],
                ],
            ]);

            $response = $this->elasticService->getClient()->indices()->create($params);
        }

        $coin_events = DB::table('coin_events')->get();

        foreach ($coin_events as $row) {

            $response = $this->elasticService->getClient()->update([
                'index' => ELASTIC_COIN_EVENTS,
                'id' => $row->id,
                'retry_on_conflict' => 5,
                'body' => [
                    'doc' => [
                        'id' => $row->id,
                        'event_type' => $row->event_type,
                        'title' => $row->title,
                        'default_rate' => $row->default_rate,
                        'description' => $row->description,
                        'deduction_type' => $row->deduction_type,
                        'minimum_required' => $row->minimum_required,
                        'status' => $row->status,
                    ],
                    'doc_as_upsert' => true,
                    'detect_noop' => false,
                ],
            ]);

        }

        $page = max((int) $request->input('page', 1), 1);
        $size = (int) $request->input('size', 500); // Default 20 results per page
        $from = ($page - 1) * $size;
        $sort = [];
        $params = [
            'index' => ELASTIC_COIN_EVENTS,
            'body' => [
                'from' => $from,
                'size' => $size,
                'sort' => $sort,
            ],
        ];
        $response = $this->elasticService->getClient()->search($params);

        $total = $response['hits']['total']['value'] ?? 0;
        $data = array_map(function ($hit) {
            return $hit['_source'];
        }, $response['hits']['hits']);

    }

    public function compnayCoinsElastic(Request $request)
    {

        if (! $this->elasticService->indexExists(ELASTIC_COMPANY_COINS)) {
            $params = [
                'index' => ELASTIC_COMPANY_COINS,
                'body' => [
                    'settings' => [
                        'number_of_shards' => 1,
                        'number_of_replicas' => 0,
                        'index.blocks.read_only_allow_delete' => false,
                        'index.blocks.write' => false,
                        'index.blocks.read' => false,
                        'index.blocks.metadata' => false,
                        'index.lifecycle.name' => null,
                        'index.lifecycle.rollover_alias' => null,
                        'index.requests.cache.enable' => true,
                        'analysis' => [
                            'analyzer' => [
                                'comma_analyzer' => [
                                    'type' => 'custom',
                                    'tokenizer' => 'comma_tokenizer',
                                    'filter' => ['lowercase', 'trim'],
                                ],
                                'number_list_analyzer' => [
                                    'type' => 'custom',
                                    'tokenizer' => 'number_list_tokenizer',
                                    'filter' => ['trim'],
                                ],
                            ],
                            'tokenizer' => [
                                'comma_tokenizer' => [
                                    'type' => 'pattern',
                                    'pattern' => '\\s*,\\s*',
                                ],
                                'number_list_tokenizer' => [
                                    'type' => 'pattern',
                                    'pattern' => '\\s*,\\s*',
                                ],
                            ],
                            'filter' => [
                                'trim' => [
                                    'type' => 'trim',
                                ],
                            ],
                        ],
                    ],
                    'mappings' => [
                        'properties' => [
                            'id' => ['type' => 'long'],
                            'coin_order_id' => ['type' => 'long'],
                            'company_id' => ['type' => 'long'],
                            'event_type' => ['type' => 'keyword'],
                            'currency' => ['type' => 'text'],
                            'amount' => ['type' => 'double'],
                            'discount' => ['type' => 'double'],
                            'coins' => ['type' => 'double'],
                            'actual_coins' => ['type' => 'double'],
                            'balance_coin' => ['type' => 'double'],
                            'status' => ['type' => 'keyword'],
                            'free_pack_status' => ['type' => 'keyword'],
                            'start_date' => ['type' => 'date'],
                            'end_date' => ['type' => 'date'],
                            'free_update_date' => ['type' => 'date'],
                            'auto_team_coins' => ['type' => 'date'],
                            'auto_job_coins' => ['type' => 'date'],
                            'created_at' => ['type' => 'date'],
                            'updated_at' => ['type' => 'date'],
                        ],
                    ],
                ],
            ];

            $this->elasticService->getClient()->cluster()->putSettings([
                'body' => [
                    'persistent' => [
                        'action.destructive_requires_name' => true,
                    ],
                ],
            ]);

            $response = $this->elasticService->getClient()->indices()->create($params);
        }

        $company_coins = DB::table('company_coins')->get();

        foreach ($company_coins as $row) {

            $response = $this->elasticService->getClient()->update([
                'index' => ELASTIC_COMPANY_COINS,
                'id' => $row->id,
                'retry_on_conflict' => 5,
                'body' => [
                    'doc' => [
                        'id' => $row->id,
                        'coin_order_id' => $row->coin_order_id,
                        'company_id' => $row->company_id,
                        'event_type' => $row->event_type,
                        'currency' => $row->currency,
                        'amount' => $row->amount,
                        'discount' => $row->discount,
                        'coins' => $row->coins,
                        'actual_coins' => $row->actual_coins,
                        'balance_coin' => $row->balance_coin,
                        'status' => $row->status,
                        'free_pack_status' => $row->free_pack_status,
                        'start_date' => ($row->start_date) ? Carbon::parse($row->start_date)->toIso8601String() : null,
                        'end_date' => ($row->end_date) ? Carbon::parse($row->end_date)->toIso8601String() : null,
                        'free_update_date' => ($row->free_update_date) ? Carbon::parse($row->free_update_date)->toIso8601String() : null,
                        'auto_team_coins' => ($row->auto_team_coins) ? Carbon::parse($row->auto_team_coins)->toIso8601String() : null,
                        'auto_job_coins' => ($row->auto_job_coins) ? Carbon::parse($row->auto_job_coins)->toIso8601String() : null,
                        'updated_at' => ($row->updated_at) ? Carbon::parse($row->updated_at)->toIso8601String() : null,
                        'created_at' => ($row->created_at) ? Carbon::parse($row->created_at)->toIso8601String() : null,
                    ],
                    'doc_as_upsert' => true,
                    'detect_noop' => false,
                ],
            ]);

        }

        $page = max((int) $request->input('page', 1), 1);
        $size = (int) $request->input('size', 500); // Default 20 results per page
        $from = ($page - 1) * $size;
        $sort = [];
        $params = [
            'index' => ELASTIC_COMPANY_COINS,
            'body' => [
                'from' => $from,
                'size' => $size,
                'sort' => $sort,
            ],
        ];
        $response = $this->elasticService->getClient()->search($params);

        $total = $response['hits']['total']['value'] ?? 0;
        $data = array_map(function ($hit) {
            return $hit['_source'];
        }, $response['hits']['hits']);

    }

    public function coinPackagesElastic(Request $request)
    {

        if (! $this->elasticService->indexExists(ELASTIC_COIN_PACKAGES)) {
            $params = [
                'index' => ELASTIC_COIN_PACKAGES,
                'body' => [
                    'settings' => [
                        'number_of_shards' => 1,
                        'number_of_replicas' => 0,
                        'index.blocks.read_only_allow_delete' => false,
                        'index.blocks.write' => false,
                        'index.blocks.read' => false,
                        'index.blocks.metadata' => false,
                        'index.lifecycle.name' => null,
                        'index.lifecycle.rollover_alias' => null,
                        'index.requests.cache.enable' => true,
                        'analysis' => [
                            'analyzer' => [
                                'comma_analyzer' => [
                                    'type' => 'custom',
                                    'tokenizer' => 'comma_tokenizer',
                                    'filter' => ['lowercase', 'trim'],
                                ],
                                'number_list_analyzer' => [
                                    'type' => 'custom',
                                    'tokenizer' => 'number_list_tokenizer',
                                    'filter' => ['trim'],
                                ],
                            ],
                            'tokenizer' => [
                                'comma_tokenizer' => [
                                    'type' => 'pattern',
                                    'pattern' => '\\s*,\\s*',
                                ],
                                'number_list_tokenizer' => [
                                    'type' => 'pattern',
                                    'pattern' => '\\s*,\\s*',
                                ],
                            ],
                            'filter' => [
                                'trim' => [
                                    'type' => 'trim',
                                ],
                            ],
                        ],
                    ],
                    'mappings' => [
                        'properties' => [
                            'id' => ['type' => 'long'],
                            'coin_package_type_id' => ['type' => 'long'],
                            'price' => ['type' => 'double'],
                            'price_usd' => ['type' => 'double'],
                            'total_price_monthly' => ['type' => 'double'],
                            'total_price_days' => ['type' => 'double'],
                            'total_price_rate' => ['type' => 'double'],
                            'total_price_usd_monthly' => ['type' => 'double'],
                            'total_price_usd_days' => ['type' => 'double'],
                            'total_price_usd_rate' => ['type' => 'double'],
                            'visibility_show' => ['type' => 'integer'],
                            'coins' => ['type' => 'double'],
                            'coins_auto' => ['type' => 'double'],
                            'discount' => ['type' => 'integer'],
                            'days' => ['type' => 'integer'],
                            'status' => ['type' => 'integer'],
                            'custome_pck' => ['type' => 'integer'],
                            'event_type' => ['type' => 'keyword'],
                            'created_at' => ['type' => 'date'],
                            'updated_at' => ['type' => 'date'],
                        ],
                    ],
                ],
            ];

            $this->elasticService->getClient()->cluster()->putSettings([
                'body' => [
                    'persistent' => [
                        'action.destructive_requires_name' => true,
                    ],
                ],
            ]);

            $response = $this->elasticService->getClient()->indices()->create($params);
        }

        $skip = 0;
        $take = 500;

        $coin_packages = DB::table('coin_packages')->skip($skip)->take($take)->get();

        foreach ($coin_packages as $row) {

            $response = $this->elasticService->getClient()->update([
                'index' => ELASTIC_COIN_PACKAGES,
                'id' => $row->id,
                'retry_on_conflict' => 5,
                'body' => [
                    'doc' => [
                        'id' => $row->id,
                        'coin_package_type_id' => $row->coin_package_type_id,
                        'price' => $row->price,
                        'price_usd' => $row->price_usd,
                        'total_price_monthly' => $row->total_price_monthly,
                        'total_price_days' => $row->total_price_days,
                        'total_price_rate' => $row->total_price_rate,
                        'total_price_usd_monthly' => $row->total_price_usd_monthly,
                        'total_price_usd_days' => $row->total_price_usd_days,
                        'total_price_usd_rate' => $row->total_price_usd_rate,
                        'visibility_show' => $row->visibility_show,
                        'coins' => $row->coins,
                        'coins_auto' => $row->coins_auto,
                        'discount' => $row->discount,
                        'days' => $row->days,
                        'status' => $row->status,
                        'custome_pck' => $row->custome_pck,
                        'event_type' => $row->event_type,
                        'updated_at' => ($row->updated_at) ? Carbon::parse($row->updated_at)->toIso8601String() : null,
                        'created_at' => ($row->created_at) ? Carbon::parse($row->created_at)->toIso8601String() : null,
                    ],
                    'doc_as_upsert' => true,
                    'detect_noop' => false,
                ],
            ]);

        }

        $page = max((int) $request->input('page', 1), 1);
        $size = (int) $request->input('size', 500); // Default 20 results per page
        $from = ($page - 1) * $size;
        $sort = [];
        $params = [
            'index' => ELASTIC_COIN_PACKAGES,
            'body' => [
                'from' => $from,
                'size' => $size,
                'sort' => $sort,
            ],
        ];
        $response = $this->elasticService->getClient()->search($params);

        $total = $response['hits']['total']['value'] ?? 0;
        $data = array_map(function ($hit) {
            return $hit['_source'];
        }, $response['hits']['hits']);

    }

    public function coinOrderEventsElastic(Request $request)
    {

        if (! $this->elasticService->indexExists(ELASTIC_COIN_ORDER_EVENTS)) {
            $params = [
                'index' => ELASTIC_COIN_ORDER_EVENTS,
                'body' => [
                    'settings' => [
                        'number_of_shards' => 1,
                        'number_of_replicas' => 0,
                        'index.blocks.read_only_allow_delete' => false,
                        'index.blocks.write' => false,
                        'index.blocks.read' => false,
                        'index.blocks.metadata' => false,
                        'index.lifecycle.name' => null,
                        'index.lifecycle.rollover_alias' => null,
                        'index.requests.cache.enable' => true,
                        'analysis' => [
                            'analyzer' => [
                                'comma_analyzer' => [
                                    'type' => 'custom',
                                    'tokenizer' => 'comma_tokenizer',
                                    'filter' => ['lowercase', 'trim'],
                                ],
                                'number_list_analyzer' => [
                                    'type' => 'custom',
                                    'tokenizer' => 'number_list_tokenizer',
                                    'filter' => ['trim'],
                                ],
                            ],
                            'tokenizer' => [
                                'comma_tokenizer' => [
                                    'type' => 'pattern',
                                    'pattern' => '\\s*,\\s*',
                                ],
                                'number_list_tokenizer' => [
                                    'type' => 'pattern',
                                    'pattern' => '\\s*,\\s*',
                                ],
                            ],
                            'filter' => [
                                'trim' => [
                                    'type' => 'trim',
                                ],
                            ],
                        ],
                    ],
                    'mappings' => [
                        'properties' => [
                            'id' => ['type' => 'long'],
                            'company_id' => ['type' => 'long'],
                            'coin_event_id' => ['type' => 'long'],
                            'deductions' => ['type' => 'double'],
                            'deduction_type' => ['type' => 'keyword'],
                            'coin_order_id' => ['type' => 'long'],
                            'required_coin' => ['type' => 'double'],
                            'event_type' => ['type' => 'keyword'],
                        ],
                    ],
                ],
            ];

            $this->elasticService->getClient()->cluster()->putSettings([
                'body' => [
                    'persistent' => [
                        'action.destructive_requires_name' => true,
                    ],
                ],
            ]);

            $response = $this->elasticService->getClient()->indices()->create($params);
        }
        $skip = 0;
        $take = 500;
        $coin_packages = DB::table('coin_order_events')->skip($skip)->take($take)->get();

        foreach ($coin_packages as $row) {

            $response = $this->elasticService->getClient()->update([
                'index' => ELASTIC_COIN_ORDER_EVENTS,
                'id' => $row->id,
                'retry_on_conflict' => 5,
                'body' => [
                    'doc' => [
                        'id' => $row->id,
                        'company_id' => $row->company_id,
                        'coin_event_id' => $row->coin_event_id,
                        'deductions' => $row->deductions,
                        'deduction_type' => $row->deduction_type,
                        'coin_order_id' => $row->coin_order_id,
                        'required_coin' => $row->required_coin,
                        'event_type' => $row->event_type,
                    ],
                    'doc_as_upsert' => true,
                    'detect_noop' => false,
                ],
            ]);

        }

        $page = max((int) $request->input('page', 1), 1);
        $size = (int) $request->input('size', 500); // Default 20 results per page
        $from = ($page - 1) * $size;
        $sort = [];
        $params = [
            'index' => ELASTIC_COIN_ORDER_EVENTS,
            'body' => [
                'from' => $from,
                'size' => $size,
                'sort' => $sort,
                'track_total_hits' => true,
            ],
        ];
        $response = $this->elasticService->getClient()->search($params);

        $total = $response['hits']['total']['value'] ?? 0;
        $data = array_map(function ($hit) {
            return $hit['_source'];
        }, $response['hits']['hits']);

    }

    public function coinOrdersElastic(Request $request)
    {

        if (! $this->elasticService->indexExists(ELASTIC_COIN_ORDERS)) {
            $params = [
                'index' => ELASTIC_COIN_ORDERS,
                'body' => [
                    'settings' => [
                        'number_of_shards' => 1,
                        'number_of_replicas' => 0,
                        'index.blocks.read_only_allow_delete' => false,
                        'index.blocks.write' => false,
                        'index.blocks.read' => false,
                        'index.blocks.metadata' => false,
                        'index.lifecycle.name' => null,
                        'index.lifecycle.rollover_alias' => null,
                        'index.requests.cache.enable' => true,
                        'analysis' => [
                            'analyzer' => [
                                'comma_analyzer' => [
                                    'type' => 'custom',
                                    'tokenizer' => 'comma_tokenizer',
                                    'filter' => ['lowercase', 'trim'],
                                ],
                                'number_list_analyzer' => [
                                    'type' => 'custom',
                                    'tokenizer' => 'number_list_tokenizer',
                                    'filter' => ['trim'],
                                ],
                            ],
                            'tokenizer' => [
                                'comma_tokenizer' => [
                                    'type' => 'pattern',
                                    'pattern' => '\\s*,\\s*',
                                ],
                                'number_list_tokenizer' => [
                                    'type' => 'pattern',
                                    'pattern' => '\\s*,\\s*',
                                ],
                            ],
                            'filter' => [
                                'trim' => [
                                    'type' => 'trim',
                                ],
                            ],
                        ],
                    ],
                    'mappings' => [
                        'properties' => [
                            'id' => ['type' => 'long'],
                            'coin_package_id' => ['type' => 'long'],
                            'price' => ['type' => 'double'],
                            'currency' => ['type' => 'keyword'],
                            'coins' => ['type' => 'double'],
                            'event_type' => ['type' => 'keyword'],
                            'company_id' => ['type' => 'long'],
                            'discount' => ['type' => 'double'],
                            'status' => ['type' => 'integer'],
                            'days' => ['type' => 'integer'],
                            'created_at' => ['type' => 'date'],
                            'updated_at' => ['type' => 'date'],
                            'payment_orderId' => ['type' => 'text'],
                            'payment_Id' => ['type' => 'text'],
                            'cart_id' => ['type' => 'long'],
                            'payment_link_id' => ['type' => 'text'],
                        ],
                    ],
                ],
            ];

            $this->elasticService->getClient()->cluster()->putSettings([
                'body' => [
                    'persistent' => [
                        'action.destructive_requires_name' => true,
                    ],
                ],
            ]);

            $response = $this->elasticService->getClient()->indices()->create($params);
        }
        $skip = 0;
        $take = 500;

        $coin_packages = DB::table('coin_orders')->skip($skip)->take($take)->get();

        foreach ($coin_packages as $row) {

            $response = $this->elasticService->getClient()->update([
                'index' => ELASTIC_COIN_ORDERS,
                'id' => $row->id,
                'retry_on_conflict' => 5,
                'body' => [
                    'doc' => [
                        'id' => $row->id,
                        'company_id' => $row->company_id,
                        'coin_package_id' => $row->coin_package_id,
                        'price' => $row->price,
                        'currency' => $row->currency,
                        'coins' => $row->coins,
                        'event_type' => $row->event_type,
                        'discount' => $row->discount,
                        'status' => $row->status,
                        'days' => $row->days,
                        'updated_at' => ($row->updated_at) ? Carbon::parse($row->updated_at)->toIso8601String() : null,
                        'created_at' => ($row->created_at) ? Carbon::parse($row->created_at)->toIso8601String() : null,
                        'payment_orderId' => $row->payment_orderId,
                        'payment_Id' => $row->payment_Id,
                        'cart_id' => $row->cart_id,
                        'payment_link_id' => $row->payment_link_id,
                    ],
                    'doc_as_upsert' => true,
                    'detect_noop' => false,
                ],
            ]);

        }

        $page = max((int) $request->input('page', 1), 1);
        $size = (int) $request->input('size', 500); // Default 20 results per page
        $from = ($page - 1) * $size;
        $sort = [];
        $params = [
            'index' => ELASTIC_COIN_ORDERS,
            'body' => [
                'from' => $from,
                'size' => $size,
                'sort' => $sort,
                'track_total_hits' => true,
            ],
        ];
        $response = $this->elasticService->getClient()->search($params);

        $total = $response['hits']['total']['value'] ?? 0;
        $data = array_map(function ($hit) {
            return $hit['_source'];
        }, $response['hits']['hits']);

    }

    public function coinTransectionLogsElastic(Request $request)
    {

        if (! $this->elasticService->indexExists(ELASTIC_COIN_TRANSECTION_LOGS)) {
            $params = [
                'index' => ELASTIC_COIN_TRANSECTION_LOGS,
                'body' => [
                    'settings' => [
                        'number_of_shards' => 1,
                        'number_of_replicas' => 0,
                        'index.blocks.read_only_allow_delete' => false,
                        'index.blocks.write' => false,
                        'index.blocks.read' => false,
                        'index.blocks.metadata' => false,
                        'index.lifecycle.name' => null,
                        'index.lifecycle.rollover_alias' => null,
                        'index.requests.cache.enable' => true,
                        'analysis' => [
                            'analyzer' => [
                                'comma_analyzer' => [
                                    'type' => 'custom',
                                    'tokenizer' => 'comma_tokenizer',
                                    'filter' => ['lowercase', 'trim'],
                                ],
                                'number_list_analyzer' => [
                                    'type' => 'custom',
                                    'tokenizer' => 'number_list_tokenizer',
                                    'filter' => ['trim'],
                                ],
                            ],
                            'tokenizer' => [
                                'comma_tokenizer' => [
                                    'type' => 'pattern',
                                    'pattern' => '\\s*,\\s*',
                                ],
                                'number_list_tokenizer' => [
                                    'type' => 'pattern',
                                    'pattern' => '\\s*,\\s*',
                                ],
                            ],
                            'filter' => [
                                'trim' => [
                                    'type' => 'trim',
                                ],
                            ],
                        ],
                    ],
                    'mappings' => [
                        'properties' => [
                            'id' => ['type' => 'long'],
                            'from_company_id' => ['type' => 'long'],
                            'company_id' => ['type' => 'long'],
                            'event_by' => ['type' => 'integer'],
                            'event' => ['type' => 'text'],
                            'currency' => ['type' => 'keyword'],
                            'amount' => ['type' => 'double'],
                            'discount' => ['type' => 'double'],
                            'coins' => ['type' => 'double'],
                            'transection_type' => ['type' => 'keyword'],
                            'event_type' => ['type' => 'keyword'],
                            'settlement_coins' => ['type' => 'double'],
                            'balance_coins' => ['type' => 'double'],
                            'team_id' => ['type' => 'long'],
                            'job_id' => ['type' => 'long'],
                            'coin_order_id' => ['type' => 'long'],
                            'remain_coins' => ['type' => 'double'],
                            'actual_coins' => ['type' => 'double'],
                            'start_date' => ['type' => 'date'],
                            'end_date' => ['type' => 'date'],
                            'created_at' => ['type' => 'date'],
                            'updated_at' => ['type' => 'date'],
                            'payment_orderId' => ['type' => 'text'],
                            'payment_Id' => ['type' => 'text'],
                            'cart_id' => ['type' => 'long'],
                            'payment_link_id' => ['type' => 'text'],
                        ],
                    ],
                ],
            ];

            $this->elasticService->getClient()->cluster()->putSettings([
                'body' => [
                    'persistent' => [
                        'action.destructive_requires_name' => true,
                    ],
                ],
            ]);

            $response = $this->elasticService->getClient()->indices()->create($params);
        }
        $skip = 69027;
        $take = 1000;

        $coin_packages = DB::table('coin_transection_logs')->skip($skip)->take($take)->get();

        foreach ($coin_packages as $row) {

            $response = $this->elasticService->getClient()->update([
                'index' => ELASTIC_COIN_TRANSECTION_LOGS,
                'id' => $row->id,
                'retry_on_conflict' => 5,
                'body' => [
                    'doc' => [
                        'id' => $row->id,
                        'from_company_id' => $row->from_company_id,
                        'company_id' => $row->company_id,
                        'event_by' => $row->event_by,
                        'event' => $row->event,
                        'currency' => $row->currency,
                        'amount' => $row->amount,
                        'discount' => $row->discount,
                        'coins' => $row->coins,
                        'transection_type' => $row->transection_type,
                        'event_type' => $row->event_type,
                        'settlement_coins' => $row->settlement_coins,
                        'balance_coins' => $row->balance_coins,
                        'team_id' => $row->team_id,
                        'job_id' => $row->job_id,
                        'coin_order_id' => $row->coin_order_id,
                        'remain_coins' => $row->remain_coins,
                        'actual_coins' => $row->actual_coins,
                        'start_date' => ($row->start_date) ? Carbon::parse($row->start_date)->toIso8601String() : null,
                        'end_date' => ($row->end_date) ? Carbon::parse($row->end_date)->toIso8601String() : null,
                        'created_at' => ($row->created_at) ? Carbon::parse($row->created_at)->toIso8601String() : null,
                        'updated_at' => ($row->updated_at) ? Carbon::parse($row->updated_at)->toIso8601String() : null,
                        'payment_orderId' => $row->payment_orderId,
                        'payment_Id' => $row->payment_Id,
                        'cart_id' => $row->cart_id,
                        'payment_link_id' => $row->payment_link_id,
                    ],
                    'doc_as_upsert' => true,
                    'detect_noop' => false,
                ],
            ]);

        }

        $page = max((int) $request->input('page', 1), 1);
        $size = (int) $request->input('size', 500); // Default 20 results per page
        $from = ($page - 1) * $size;
        $sort = [];
        $params = [
            'index' => ELASTIC_COIN_TRANSECTION_LOGS,
            'body' => [
                'from' => $from,
                'size' => $size,
                'sort' => $sort,
                'track_total_hits' => true,
            ],
        ];
        $response = $this->elasticService->getClient()->search($params);

        $total = $response['hits']['total']['value'] ?? 0;
        $data = array_map(function ($hit) {
            return $hit['_source'];
        }, $response['hits']['hits']);

    }

    public function coinPackageTypesElastic(Request $request)
    {

        if (! $this->elasticService->indexExists(ELASTIC_COIN_PACKAGE_TYPES)) {
            $params = [
                'index' => ELASTIC_COIN_PACKAGE_TYPES,
                'body' => [
                    'settings' => [
                        'number_of_shards' => 1,
                        'number_of_replicas' => 0,
                        'index.blocks.read_only_allow_delete' => false,
                        'index.blocks.write' => false,
                        'index.blocks.read' => false,
                        'index.blocks.metadata' => false,
                        'index.lifecycle.name' => null,
                        'index.lifecycle.rollover_alias' => null,
                        'index.requests.cache.enable' => true,
                        'analysis' => [
                            'analyzer' => [
                                'comma_analyzer' => [
                                    'type' => 'custom',
                                    'tokenizer' => 'comma_tokenizer',
                                    'filter' => ['lowercase', 'trim'],
                                ],
                                'number_list_analyzer' => [
                                    'type' => 'custom',
                                    'tokenizer' => 'number_list_tokenizer',
                                    'filter' => ['trim'],
                                ],
                            ],
                            'tokenizer' => [
                                'comma_tokenizer' => [
                                    'type' => 'pattern',
                                    'pattern' => '\\s*,\\s*',
                                ],
                                'number_list_tokenizer' => [
                                    'type' => 'pattern',
                                    'pattern' => '\\s*,\\s*',
                                ],
                            ],
                            'filter' => [
                                'trim' => [
                                    'type' => 'trim',
                                ],
                            ],
                        ],
                    ],
                    'mappings' => [
                        'properties' => [
                            'id' => ['type' => 'long'],
                            'title' => ['type' => 'text'],
                            'days' => ['type' => 'integer'],
                            'event_type' => ['type' => 'keyword'],
                            'created_at' => ['type' => 'date'],
                            'updated_at' => ['type' => 'date'],
                        ],
                    ],
                ],
            ];

            $this->elasticService->getClient()->cluster()->putSettings([
                'body' => [
                    'persistent' => [
                        'action.destructive_requires_name' => true,
                    ],
                ],
            ]);

            $response = $this->elasticService->getClient()->indices()->create($params);
        }
        $skip = 0;
        $take = 500;

        $coin_packages = DB::table('coin_package_types')->skip($skip)->take($take)->get();

        foreach ($coin_packages as $row) {

            $response = $this->elasticService->getClient()->update([
                'index' => ELASTIC_COIN_PACKAGE_TYPES,
                'id' => $row->id,
                'retry_on_conflict' => 5,
                'body' => [
                    'doc' => [
                        'id' => $row->id,
                        'title' => $row->title,
                        'days' => $row->days,
                        'event_type' => $row->event_type,
                        'updated_at' => ($row->updated_at) ? Carbon::parse($row->updated_at)->toIso8601String() : null,
                        'created_at' => ($row->created_at) ? Carbon::parse($row->created_at)->toIso8601String() : null,
                    ],
                    'doc_as_upsert' => true,
                    'detect_noop' => false,
                ],
            ]);

        }

        $page = max((int) $request->input('page', 1), 1);
        $size = (int) $request->input('size', 500); // Default 20 results per page
        $from = ($page - 1) * $size;
        $sort = [];
        $params = [
            'index' => ELASTIC_COIN_PACKAGE_TYPES,
            'body' => [
                'from' => $from,
                'size' => $size,
                'sort' => $sort,
                'track_total_hits' => true,
            ],
        ];
        $response = $this->elasticService->getClient()->search($params);

        $total = $response['hits']['total']['value'] ?? 0;
        $data = array_map(function ($hit) {
            return $hit['_source'];
        }, $response['hits']['hits']);

    }

    public function candidateTimeLineElastic(Request $request)
    {

        if (! $this->elasticService->indexExists(ELASTIC_CANDIDATE_TIMELINE)) {
            $params = [
                'index' => ELASTIC_CANDIDATE_TIMELINE,
                'body' => [
                    'settings' => [
                        'number_of_shards' => 1,
                        'number_of_replicas' => 0,
                        'index.blocks.read_only_allow_delete' => false,
                        'index.blocks.write' => false,
                        'index.blocks.read' => false,
                        'index.blocks.metadata' => false,
                        'index.lifecycle.name' => null,
                        'index.lifecycle.rollover_alias' => null,
                        'index.requests.cache.enable' => true,
                        'analysis' => [
                            'analyzer' => [
                                'comma_analyzer' => [
                                    'type' => 'custom',
                                    'tokenizer' => 'comma_tokenizer',
                                    'filter' => ['lowercase', 'trim'],
                                ],
                                'number_list_analyzer' => [
                                    'type' => 'custom',
                                    'tokenizer' => 'number_list_tokenizer',
                                    'filter' => ['trim'],
                                ],
                            ],
                            'tokenizer' => [
                                'comma_tokenizer' => [
                                    'type' => 'pattern',
                                    'pattern' => '\\s*,\\s*',
                                ],
                                'number_list_tokenizer' => [
                                    'type' => 'pattern',
                                    'pattern' => '\\s*,\\s*',
                                ],
                            ],
                            'filter' => [
                                'trim' => [
                                    'type' => 'trim',
                                ],
                            ],
                        ],
                    ],
                    'mappings' => [
                        'properties' => [
                            'id' => ['type' => 'long'],
                            'event_id' => ['type' => 'long'],
                            'event_by_company_id' => ['type' => 'long'],
                            'event_by_user_id' => ['type' => 'long'],
                            'master_process_name' => ['type' => 'text'],
                            'child_process_name' => ['type' => 'text'],
                            'application_id' => ['type' => 'long'],
                            'application_email' => ['type' => 'keyword'],
                            'job_id' => ['type' => 'long'],
                            'note' => ['type' => 'text'],
                            'interview_schedule_date' => ['type' => 'date'],
                            'interview_from_time' => ['type' => 'date'],
                            'interview_to_time' => ['type' => 'date'],
                            'interview_mode' => ['type' => 'text'],
                            'interview_mode_child_level' => ['type' => 'text'],
                            'interview_mention_link' => ['type' => 'text'],
                            'interviewers' => ['type' => 'text'],
                            'interview_additional_email' => ['type' => 'text'],
                            'interview_message' => ['type' => 'text'],
                            'comment' => ['type' => 'text'],
                            'interview_schedules_id' => ['type' => 'long'],
                            'average_star_rating' => ['type' => 'long'],
                            'prev_process_name' => ['type' => 'text'],
                            'prev_process_child_name' => ['type' => 'text'],
                            'offer_currency' => ['type' => 'long'],
                            'offer_mode' => ['type' => 'long'],
                            'offer_amount' => ['type' => 'double'],
                            'joined_date' => ['type' => 'date'],
                            'candidate_status' => ['type' => 'long'],
                            'management_status' => ['type' => 'long'],
                            'accept_date_time' => ['type' => 'date'],
                            'approved_date_time' => ['type' => 'date'],
                            'send_approved_date_time' => ['type' => 'date'],
                            'send_accept_date_time' => ['type' => 'date'],
                            'email_id_hr' => ['type' => 'text'],
                            'name_hr' => ['type' => 'text'],
                            'yearly_gross' => ['type' => 'double'],
                            'yearly_final_gross' => ['type' => 'double'],
                            'mtypeSection' => ['type' => 'long'],
                            'designation_offer' => ['type' => 'text'],
                            'timezone_abbreviation' => ['type' => 'text'],
                            'confidential' => ['type' => 'long'],
                            'application_event' => ['type' => 'keyword'],
                            'title' => ['type' => 'text'],
                            'employment_type' => ['type' => 'keyword'],
                            'name' => ['type' => 'text'],
                            'image' => ['type' => 'text'],
                            'client_name' => ['type' => 'text'],
                            'available_status' => ['type' => 'long'],
                            'reschedule' => ['type' => 'date'],
                            'feedback' => ['type' => 'long'],
                            'stage' => ['type' => 'text'],
                            'status_name' => ['type' => 'text'],
                            'cur_companyname' => ['type' => 'text'],
                            'css_style' => ['type' => 'text'],
                            'created_at' => ['type' => 'date'],
                            'updated_at' => ['type' => 'date'],
                        ],
                    ],
                ],
            ];

            $this->elasticService->getClient()->cluster()->putSettings([
                'body' => [
                    'persistent' => [
                        'action.destructive_requires_name' => true,
                    ],
                ],
            ]);

            $response = $this->elasticService->getClient()->indices()->create($params);
        }
        $skip = 1500;
        $take = 500;

        // $coin_packages = DB::table('updatetimeline')->skip($skip)->take($take)->get();

        $coin_packages = DB::table('updatetimeline')->select(
            'updatetimeline.*',
            'application_event.event_name',
            'jobs.id as job_id',
            'jobs.title',
            'jobs.employment_type',
            'users.name',
            'users.image',
            'clients.name as client_name',
            'interview_schedules.available_status',
            'interview_schedules.reschedule',
            'interview_schedules.feedback',
            'interview_schedules.stage',
            'master_status.name as status_name',
            'companies.company_name as cur_companyname',
            'master_status.css_style'
        );
        $coin_packages = $coin_packages->leftjoin('application_event', 'application_event.id', '=', 'updatetimeline.event_id');
        $coin_packages = $coin_packages->join('job_applications', 'job_applications.id', '=', 'updatetimeline.application_id');
        $coin_packages = $coin_packages->leftjoin('interview_schedules', 'interview_schedules.id', 'updatetimeline.interview_schedules_id');
        $coin_packages = $coin_packages->leftjoin('master_status', 'master_status.id', 'interview_schedules.stage');
        $coin_packages = $coin_packages->join('users', 'users.id', '=', 'updatetimeline.event_by_user_id');
        $coin_packages = $coin_packages->leftjoin('companies', 'companies.id', 'users.company_id');
        $coin_packages = $coin_packages->join('jobs', 'jobs.id', '=', 'updatetimeline.job_id');
        $coin_packages = $coin_packages->join('clients', 'clients.id', '=', 'jobs.client_id');
        $coin_packages = $coin_packages->orderBy('updatetimeline.id', 'DESC');
        $coin_packages = $coin_packages->skip($skip)->take($take)->get();

        // dd($coin_packages);

        foreach ($coin_packages as $row) {

            $response = $this->elasticService->getClient()->update([
                'index' => ELASTIC_CANDIDATE_TIMELINE,
                'id' => $row->id,
                'retry_on_conflict' => 5,
                'body' => [
                    'doc' => [
                        'id' => $row->id,
                        'event_id' => $row->event_id,
                        'event_by_company_id' => $row->event_by_company_id,
                        'event_by_user_id' => $row->event_by_user_id,
                        'master_process_name' => $row->master_process_name,
                        'child_process_name' => $row->child_process_name,
                        'application_id' => $row->application_id,
                        'application_email' => $row->application_email,
                        'job_id' => $row->job_id,
                        'note' => $row->note,
                        'interview_schedule_date' => ($row->interview_schedule_date) ? Carbon::parse($row->interview_schedule_date)->toIso8601String() : null,
                        'interview_from_time' => ($row->interview_from_time) ? Carbon::parse($row->interview_from_time)->toIso8601String() : null,
                        'interview_to_time' => ($row->interview_to_time) ? Carbon::parse($row->interview_to_time)->toIso8601String() : null,
                        'interview_mode' => $row->interview_mode,
                        'interview_mode_child_level' => $row->interview_mode_child_level,
                        'interview_mention_link' => $row->interview_mention_link,
                        'interviewers' => $row->interviewers,
                        'interview_additional_email' => $row->interview_additional_email,
                        'interview_message' => $row->interview_message,
                        'comment' => $row->comment,
                        'interview_schedules_id' => $row->interview_schedules_id,
                        'average_star_rating' => $row->average_star_rating,
                        'prev_process_name' => $row->prev_process_name,
                        'prev_process_child_name' => $row->prev_process_child_name,
                        'offer_currency' => $row->offer_currency,
                        'offer_mode' => $row->offer_mode,
                        'offer_amount' => (float) $row->offer_amount,
                        'joined_date' => ($row->joined_date) ? Carbon::parse($row->joined_date)->toIso8601String() : null,
                        'candidate_status' => $row->candidate_status,
                        'management_status' => $row->management_status,
                        'accept_date_time' => ($row->accept_date_time) ? Carbon::parse($row->accept_date_time)->toIso8601String() : null,
                        'approved_date_time' => ($row->approved_date_time) ? Carbon::parse($row->approved_date_time)->toIso8601String() : null,
                        'send_approved_date_time' => ($row->send_approved_date_time) ? Carbon::parse($row->send_approved_date_time)->toIso8601String() : null,
                        'send_accept_date_time' => ($row->send_accept_date_time) ? Carbon::parse($row->send_accept_date_time)->toIso8601String() : null,
                        'email_id_hr' => $row->email_id_hr,
                        'name_hr' => $row->name_hr,
                        'yearly_gross' => $row->yearly_gross,
                        'yearly_final_gross' => $row->yearly_final_gross,
                        'mtypeSection' => $row->mtypeSection,
                        'designation_offer' => $row->designation_offer,
                        'timezone_abbreviation' => $row->timezone_abbreviation,
                        'confidential' => $row->confidential,
                        'event_name' => $row->event_name,
                        'title' => $row->title,
                        'employment_type' => $row->employment_type,
                        'name' => $row->name,
                        'image' => $row->image,
                        'client_name' => $row->client_name,
                        'available_status' => $row->available_status,
                        'reschedule' => ($row->reschedule) ? Carbon::parse($row->reschedule)->toIso8601String() : null,
                        'feedback' => $row->feedback,
                        'stage' => $row->stage,
                        'status_name' => $row->status_name,
                        'cur_companyname' => $row->cur_companyname,
                        'css_style' => $row->css_style,
                        'updated_at' => ($row->update_at) ? Carbon::parse($row->update_at)->toIso8601String() : null,
                        'created_at' => ($row->create_at) ? Carbon::parse($row->create_at)->toIso8601String() : null,
                    ],
                    'doc_as_upsert' => true,
                    'detect_noop' => false,
                ],
            ]);

        }

        $page = max((int) $request->input('page', 1), 1);
        $size = (int) $request->input('size', 500); // Default 20 results per page
        $from = ($page - 1) * $size;
        $sort = [];
        $params = [
            'index' => ELASTIC_CANDIDATE_TIMELINE,
            'body' => [
                'from' => $from,
                'size' => $size,
                'sort' => $sort,
                'track_total_hits' => true,
            ],
        ];
        $response = $this->elasticService->getClient()->search($params);

        $total = $response['hits']['total']['value'] ?? 0;
        $data = array_map(function ($hit) {
            return $hit['_source'];
        }, $response['hits']['hits']);

    }

    public function benchAssignElastic(Request $request)
    {

        if (! $this->elasticService->indexExists(ELASTIC_BENCH_ASSIGN)) {
            $params = [
                'index' => ELASTIC_BENCH_ASSIGN,
                'body' => [
                    'settings' => [
                        'number_of_shards' => 1,
                        'number_of_replicas' => 0,
                        'index.blocks.read_only_allow_delete' => false,
                        'index.blocks.write' => false,
                        'index.blocks.read' => false,
                        'index.blocks.metadata' => false,
                        'index.lifecycle.name' => null,
                        'index.lifecycle.rollover_alias' => null,
                        'index.requests.cache.enable' => true,
                        'analysis' => [
                            'analyzer' => [
                                'comma_analyzer' => [
                                    'type' => 'custom',
                                    'tokenizer' => 'comma_tokenizer',
                                    'filter' => ['lowercase', 'trim'],
                                ],
                                'number_list_analyzer' => [
                                    'type' => 'custom',
                                    'tokenizer' => 'number_list_tokenizer',
                                    'filter' => ['trim'],
                                ],
                            ],
                            'tokenizer' => [
                                'comma_tokenizer' => [
                                    'type' => 'pattern',
                                    'pattern' => '\\s*,\\s*',
                                ],
                                'number_list_tokenizer' => [
                                    'type' => 'pattern',
                                    'pattern' => '\\s*,\\s*',
                                ],
                            ],
                            'filter' => [
                                'trim' => [
                                    'type' => 'trim',
                                ],
                            ],
                        ],
                    ],
                    'mappings' => [
                        'properties' => [
                            'id' => ['type' => 'long'],
                            'user_id' => ['type' => 'long'],
                            'company_id' => ['type' => 'long'],
                            'bench_applications_id' => ['type' => 'long'],
                            'name' => ['type' => 'text'],
                            'calling_code' => ['type' => 'text'],
                            'email' => ['type' => 'keyword'],
                            'mobile' => ['type' => 'text'],
                            'designation' => ['type' => 'text'],
                            'updated_at' => ['type' => 'date'],
                            'created_at' => ['type' => 'date'],
                        ],
                    ],
                ],
            ];

            $this->elasticService->getClient()->cluster()->putSettings([
                'body' => [
                    'persistent' => [
                        'action.destructive_requires_name' => true,
                    ],
                ],
            ]);

            $response = $this->elasticService->getClient()->indices()->create($params);
        }

        $skip = 1500;
        $take = 500;

        // $coin_packages = DB::table('updatetimeline')->skip($skip)->take($take)->get();

        $coin_packages = DB::table('bench_assign')
            ->select('bench_assign.created_at', 'bench_assign.updated_at', 'bench_assign.id', 'job_applications.id as bench_applications_id', 'users.id as user_id', 'users.name', 'users.calling_code', 'users.email', 'users.mobile', 'users_profile.designation')
            ->join('users', 'users.id', '=', 'bench_assign.user_id')
            ->join('job_applications', 'job_applications.id', '=', 'bench_assign.bench_applications_id')
            ->leftjoin('users_profile', 'users_profile.user_id', 'users.id')
            ->get();

        foreach ($coin_packages as $row) {

            $response = $this->elasticService->getClient()->update([
                'index' => ELASTIC_BENCH_ASSIGN,
                'id' => $row->id,
                'retry_on_conflict' => 5,
                'body' => [
                    'doc' => [
                        'id' => $row->id,
                        'users_id' => $row->user_id,
                        'name' => $row->name,
                        'calling_code' => $row->calling_code,
                        'email' => $row->email,
                        'mobile' => $row->mobile,
                        'bench_applications_id' => $row->bench_applications_id,
                        'designation' => $row->designation,
                        'updated_at' => ($row->updated_at) ? Carbon::parse($row->updated_at)->toIso8601String() : null,
                        'created_at' => ($row->created_at) ? Carbon::parse($row->created_at)->toIso8601String() : null,
                    ],
                    'doc_as_upsert' => true,
                    'detect_noop' => false,
                ],
            ]);

        }

        $page = max((int) $request->input('page', 1), 1);
        $size = (int) $request->input('size', 500); // Default 20 results per page
        $from = ($page - 1) * $size;
        $sort = [];
        $params = [
            'index' => ELASTIC_BENCH_ASSIGN,
            'body' => [
                'from' => $from,
                'size' => $size,
                'sort' => $sort,
                'track_total_hits' => true,
            ],
        ];
        $response = $this->elasticService->getClient()->search($params);

        $total = $response['hits']['total']['value'] ?? 0;
        $data = array_map(function ($hit) {
            return $hit['_source'];
        }, $response['hits']['hits']);

    }

    public function benchAdditionalDetailElastic(Request $request)
    {

        if (! $this->elasticService->indexExists(ELASTIC_BENCH_ADDITIONAL_DETAIL)) {
            $params = [
                'index' => ELASTIC_BENCH_ADDITIONAL_DETAIL,
                'body' => [
                    'settings' => [
                        'number_of_shards' => 1,
                        'number_of_replicas' => 0,
                        'index.blocks.read_only_allow_delete' => false,
                        'index.blocks.write' => false,
                        'index.blocks.read' => false,
                        'index.blocks.metadata' => false,
                        'index.lifecycle.name' => null,
                        'index.lifecycle.rollover_alias' => null,
                        'index.requests.cache.enable' => true,
                        'analysis' => [
                            'analyzer' => [
                                'comma_analyzer' => [
                                    'type' => 'custom',
                                    'tokenizer' => 'comma_tokenizer',
                                    'filter' => ['lowercase', 'trim'],
                                ],
                                'number_list_analyzer' => [
                                    'type' => 'custom',
                                    'tokenizer' => 'number_list_tokenizer',
                                    'filter' => ['trim'],
                                ],
                            ],
                            'tokenizer' => [
                                'comma_tokenizer' => [
                                    'type' => 'pattern',
                                    'pattern' => '\\s*,\\s*',
                                ],
                                'number_list_tokenizer' => [
                                    'type' => 'pattern',
                                    'pattern' => '\\s*,\\s*',
                                ],
                            ],
                            'filter' => [
                                'trim' => [
                                    'type' => 'trim',
                                ],
                            ],
                        ],
                    ],
                    'mappings' => [
                        'properties' => [
                            'id' => ['type' => 'long'],
                            'bench_id' => ['type' => 'long'],
                            'custom_keys' => ['type' => 'text'],
                            'custom_keys_value' => ['type' => 'text'],
                            'create_at' => ['type' => 'date'],
                            'update_at' => ['type' => 'date'],
                        ],
                    ],
                ],
            ];

            $this->elasticService->getClient()->cluster()->putSettings([
                'body' => [
                    'persistent' => [
                        'action.destructive_requires_name' => true,
                    ],
                ],
            ]);

            $response = $this->elasticService->getClient()->indices()->create($params);
        }

        $skip = 0;
        $take = 500;

        $coin_packages = DB::table('bench_additional_detail')->skip($skip)->take($take)->get();

        foreach ($coin_packages as $row) {

            $response = $this->elasticService->getClient()->update([
                'index' => ELASTIC_BENCH_ADDITIONAL_DETAIL,
                'id' => $row->id,
                'retry_on_conflict' => 5,
                'body' => [
                    'doc' => [
                        'id' => $row->id,
                        'bench_id' => $row->bench_id,
                        'custom_keys' => $row->custom_keys,
                        'custom_keys_value' => $row->custom_keys_value,
                        'create_at' => ($row->create_at) ? Carbon::parse($row->create_at)->toIso8601String() : null,
                        'update_at' => ($row->update_at) ? Carbon::parse($row->update_at)->toIso8601String() : null,
                    ],
                    'doc_as_upsert' => true,
                    'detect_noop' => false,
                ],
            ]);

        }

        $page = max((int) $request->input('page', 1), 1);
        $size = (int) $request->input('size', 500); // Default 20 results per page
        $from = ($page - 1) * $size;
        $sort = [];
        $params = [
            'index' => ELASTIC_BENCH_ADDITIONAL_DETAIL,
            'body' => [
                'from' => $from,
                'size' => $size,
                'sort' => $sort,
                'track_total_hits' => true,
            ],
        ];
        $response = $this->elasticService->getClient()->search($params);

        $total = $response['hits']['total']['value'] ?? 0;
        $data = array_map(function ($hit) {
            return $hit['_source'];
        }, $response['hits']['hits']);

    }

    public function applicantNotesElastic(Request $request)
    {

        if (! $this->elasticService->indexExists(ELASTIC_APPLICANT_NOTES)) {
            $params = [
                'index' => ELASTIC_APPLICANT_NOTES,
                'body' => [
                    'settings' => [
                        'number_of_shards' => 1,
                        'number_of_replicas' => 0,
                        'index.blocks.read_only_allow_delete' => false,
                        'index.blocks.write' => false,
                        'index.blocks.read' => false,
                        'index.blocks.metadata' => false,
                        'index.lifecycle.name' => null,
                        'index.lifecycle.rollover_alias' => null,
                        'index.requests.cache.enable' => true,
                        'analysis' => [
                            'analyzer' => [
                                'comma_analyzer' => [
                                    'type' => 'custom',
                                    'tokenizer' => 'comma_tokenizer',
                                    'filter' => ['lowercase', 'trim'],
                                ],
                                'number_list_analyzer' => [
                                    'type' => 'custom',
                                    'tokenizer' => 'number_list_tokenizer',
                                    'filter' => ['trim'],
                                ],
                            ],
                            'tokenizer' => [
                                'comma_tokenizer' => [
                                    'type' => 'pattern',
                                    'pattern' => '\\s*,\\s*',
                                ],
                                'number_list_tokenizer' => [
                                    'type' => 'pattern',
                                    'pattern' => '\\s*,\\s*',
                                ],
                            ],
                            'filter' => [
                                'trim' => [
                                    'type' => 'trim',
                                ],
                            ],
                        ],
                    ],
                    'mappings' => [
                        'properties' => [
                            'id' => ['type' => 'long'],
                            'note_text' => ['type' => 'text'],
                            'name' => ['type' => 'text'],
                            'posted_by_image' => ['type' => 'text'],
                            'add_job_wise_condidates_id' => ['type' => 'long'],
                            'job_application_id' => ['type' => 'long'],
                            'user_id' => ['type' => 'long'],
                            'created_at' => ['type' => 'date'],
                            'updated_at' => ['type' => 'date'],
                        ],
                    ],
                ],
            ];

            $this->elasticService->getClient()->cluster()->putSettings([
                'body' => [
                    'persistent' => [
                        'action.destructive_requires_name' => true,
                    ],
                ],
            ]);

            $response = $this->elasticService->getClient()->indices()->create($params);
        }

        $skip = 0;
        $take = 500;

        // $coin_packages = DB::table('applicant_notes')->skip($skip)->take($take)->get();
        $coin_packages = DB::table('applicant_notes')
            ->select(
                'applicant_notes.id',
                'applicant_notes.note_text',
                'applicant_notes.user_id',
                'applicant_notes.created_at',
                'applicant_notes.updated_at',
                'users.name',
                'add_job_wise_condidates.id as add_job_wise_condidates_id',
                'add_job_wise_condidates.job_application_id',
                'users.image as posted_by_image'
            )
            ->join('job_applications', 'job_applications.id', 'applicant_notes.job_application_id')
            ->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id')
        // ->where('company_id', user()->company_id)
            ->join('users', 'users.id', '=', 'applicant_notes.user_id')
            ->orderBy('id', 'ASC')
            ->get();

        foreach ($coin_packages as $row) {

            $response = $this->elasticService->getClient()->update([
                'index' => ELASTIC_APPLICANT_NOTES,
                'id' => $row->id,
                'retry_on_conflict' => 5,
                'body' => [
                    'doc' => [
                        'id' => $row->id,
                        'note_text' => $row->note_text,
                        'name' => $row->name,
                        'posted_by_image' => $row->posted_by_image,
                        'user_id' => $row->user_id,
                        'add_job_wise_condidates_id' => $row->add_job_wise_condidates_id,
                        'job_application_id' => $row->job_application_id,
                        'created_at' => ($row->created_at) ? Carbon::parse($row->created_at)->toIso8601String() : null,
                        'updated_at' => ($row->updated_at) ? Carbon::parse($row->updated_at)->toIso8601String() : null,
                    ],
                    'doc_as_upsert' => true,
                    'detect_noop' => false,
                ],
            ]);

        }

        $page = max((int) $request->input('page', 1), 1);
        $size = (int) $request->input('size', 500); // Default 20 results per page
        $from = ($page - 1) * $size;
        $sort = [];
        $params = [
            'index' => ELASTIC_APPLICANT_NOTES,
            'body' => [
                'from' => $from,
                'size' => $size,
                'sort' => $sort,
                'track_total_hits' => true,
            ],
        ];
        $response = $this->elasticService->getClient()->search($params);

        $total = $response['hits']['total']['value'] ?? 0;
        $data = array_map(function ($hit) {
            return $hit['_source'];
        }, $response['hits']['hits']);

    }

    public function jobCategoriesElastic(Request $request)
    {

        $params = [
            'index' => ELASTIC_JOB_CATEGORIES,
            'body' => [
                'size' => 10000,
                'query' => [
                    'bool' => [
                        'must' => [
                            ['term' => ['company_id' => 2]],
                        ],
                    ],
                ],
            ],
        ];

        $response = $this->elasticService->getClient()->search($params);
        $intRow = empty($response['hits']['hits'])
    ? []
    : array_map(function ($hit) {
        return (object) $hit['_source'];
    }, $response['hits']['hits']);

        // $intRow =  isset($response['hits']['hits'][0]) ? (object) $response['hits']['hits'][0]['_source'] : null;

        if (! $this->elasticService->indexExists(ELASTIC_JOB_CATEGORIES)) {
            $params = [
                'index' => ELASTIC_JOB_CATEGORIES,
                'body' => [
                    'settings' => [
                        'number_of_shards' => 1,
                        'number_of_replicas' => 0,
                        'index.blocks.read_only_allow_delete' => false,
                        'index.blocks.write' => false,
                        'index.blocks.read' => false,
                        'index.blocks.metadata' => false,
                        'index.lifecycle.name' => null,
                        'index.lifecycle.rollover_alias' => null,
                        'index.requests.cache.enable' => true,
                        'analysis' => [
                            'analyzer' => [
                                'comma_analyzer' => [
                                    'type' => 'custom',
                                    'tokenizer' => 'comma_tokenizer',
                                    'filter' => ['lowercase', 'trim'],
                                ],
                                'number_list_analyzer' => [
                                    'type' => 'custom',
                                    'tokenizer' => 'number_list_tokenizer',
                                    'filter' => ['trim'],
                                ],
                            ],
                            'tokenizer' => [
                                'comma_tokenizer' => [
                                    'type' => 'pattern',
                                    'pattern' => '\\s*,\\s*',
                                ],
                                'number_list_tokenizer' => [
                                    'type' => 'pattern',
                                    'pattern' => '\\s*,\\s*',
                                ],
                            ],
                            'filter' => [
                                'trim' => [
                                    'type' => 'trim',
                                ],
                            ],
                        ],
                    ],
                    'mappings' => [
                        'properties' => [
                            'id' => ['type' => 'long'],
                            'company_id' => ['type' => 'long'],
                            'name' => ['type' => 'text'],
                            'created_at' => ['type' => 'date'],
                            'updated_at' => ['type' => 'date'],
                        ],
                    ],
                ],
            ];

            $this->elasticService->getClient()->cluster()->putSettings([
                'body' => [
                    'persistent' => [
                        'action.destructive_requires_name' => true,
                    ],
                ],
            ]);

            $response = $this->elasticService->getClient()->indices()->create($params);
        }

        $skip = 0;
        $take = 1000;

        $coin_packages = DB::table('job_categories')->skip($skip)->take($take)->get();

        foreach ($coin_packages as $row) {

            $response = $this->elasticService->getClient()->update([
                'index' => ELASTIC_JOB_CATEGORIES,
                'id' => $row->id,
                'retry_on_conflict' => 5,
                'body' => [
                    'doc' => [
                        'id' => $row->id,
                        'company_id' => $row->company_id,
                        'name' => $row->name,
                        'created_at' => ($row->created_at) ? Carbon::parse($row->created_at)->toIso8601String() : null,
                        'updated_at' => ($row->updated_at) ? Carbon::parse($row->updated_at)->toIso8601String() : null,
                    ],
                    'doc_as_upsert' => true,
                    'detect_noop' => false,
                ],
            ]);
        }

        $page = max((int) $request->input('page', 1), 1);
        $size = (int) $request->input('size', 500); // Default 20 results per page
        $from = ($page - 1) * $size;
        $sort = [];
        $params = [
            'index' => ELASTIC_JOB_CATEGORIES,
            'body' => [
                'from' => $from,
                'size' => $size,
                'sort' => $sort,
                'track_total_hits' => true,
            ],
        ];
        $response = $this->elasticService->getClient()->search($params);

        $total = $response['hits']['total']['value'] ?? 0;
        $data = array_map(function ($hit) {
            return $hit['_source'];
        }, $response['hits']['hits']);

    }

    public function emailNotificationsElastic(Request $request)
    {

        // $params = [
        // 		'index' => ELASTIC_JOB_CATEGORIES,
        // 		'body'  => [
        //             'size' => 10000,
        // 			'query' => [
        // 				'bool' => [
        // 					'must' => [
        // 						['term' => ['company_id' => 2]],
        // 					],
        // 				],
        // 			],
        // 		],
        // ];

        // $response = $this->elasticService->getClient()->search($params);
        // $intRow = empty($response['hits']['hits'])
        // ? []
        // : array_map(function ($hit) {
        //     return (object) $hit['_source'];
        // }, $response['hits']['hits']);

        // // $intRow =  isset($response['hits']['hits'][0]) ? (object) $response['hits']['hits'][0]['_source'] : null;

        // dd($intRow );

        if (! $this->elasticService->indexExists(ELASTIC_EMAIL_NOTIFICATIONS)) {
            $params = [
                'index' => ELASTIC_EMAIL_NOTIFICATIONS,
                'body' => [
                    'settings' => [
                        'number_of_shards' => 1,
                        'number_of_replicas' => 0,
                        'index.blocks.read_only_allow_delete' => false,
                        'index.blocks.write' => false,
                        'index.blocks.read' => false,
                        'index.blocks.metadata' => false,
                        'index.lifecycle.name' => null,
                        'index.lifecycle.rollover_alias' => null,
                        'index.requests.cache.enable' => true,
                        'analysis' => [
                            'analyzer' => [
                                'comma_analyzer' => [
                                    'type' => 'custom',
                                    'tokenizer' => 'comma_tokenizer',
                                    'filter' => ['lowercase', 'trim'],
                                ],
                                'number_list_analyzer' => [
                                    'type' => 'custom',
                                    'tokenizer' => 'number_list_tokenizer',
                                    'filter' => ['trim'],
                                ],
                            ],
                            'tokenizer' => [
                                'comma_tokenizer' => [
                                    'type' => 'pattern',
                                    'pattern' => '\\s*,\\s*',
                                ],
                                'number_list_tokenizer' => [
                                    'type' => 'pattern',
                                    'pattern' => '\\s*,\\s*',
                                ],
                            ],
                            'filter' => [
                                'trim' => [
                                    'type' => 'trim',
                                ],
                            ],
                        ],
                    ],
                    'mappings' => [
                        'properties' => [
                            'id' => ['type' => 'long'],
                            'purpose' => ['type' => 'text'],
                            'subject' => ['type' => 'text'],
                            'from_email_id' => ['type' => 'text'],
                            'body_description' => ['type' => 'text'],
                            'status' => ['type' => 'long'],
                            'mailType' => ['type' => 'long'], // 	1-Recruitment Notification 2-Sales Notification 3-Mass Mail Template 4-Candidate Send Email Template 5-Client Send Email Template
                            'created_at' => ['type' => 'date'],
                            'updated_at' => ['type' => 'date'],
                        ],
                    ],
                ],
            ];

            $this->elasticService->getClient()->cluster()->putSettings([
                'body' => [
                    'persistent' => [
                        'action.destructive_requires_name' => true,
                    ],
                ],
            ]);

            $response = $this->elasticService->getClient()->indices()->create($params);
        }

        $skip = 0;
        $take = 1000;

        $coin_packages = DB::table('email_notifications')->skip($skip)->take($take)->get();

        foreach ($coin_packages as $row) {

            $response = $this->elasticService->getClient()->update([
                'index' => ELASTIC_EMAIL_NOTIFICATIONS,
                'id' => $row->id,
                'retry_on_conflict' => 5,
                'body' => [
                    'doc' => [
                        'id' => $row->id,
                        'purpose' => $row->purpose,
                        'subject' => $row->subject,
                        'from_email_id' => $row->from_email_id,
                        'body_description' => $row->body_description,
                        'status' => $row->status,
                        'mailType' => $row->mailType,
                        'created_at' => ($row->created_at) ? Carbon::parse($row->created_at)->toIso8601String() : null,
                        'updated_at' => ($row->updated_at) ? Carbon::parse($row->updated_at)->toIso8601String() : null,
                    ],
                    'doc_as_upsert' => true,
                    'detect_noop' => false,
                ],
            ]);
        }

        $page = max((int) $request->input('page', 1), 1);
        $size = (int) $request->input('size', 500); // Default 20 results per page
        $from = ($page - 1) * $size;
        $sort = [];
        $params = [
            'index' => ELASTIC_EMAIL_NOTIFICATIONS,
            'body' => [
                'from' => $from,
                'size' => $size,
                'sort' => $sort,
                'track_total_hits' => true,
            ],
        ];
        $response = $this->elasticService->getClient()->search($params);

        $total = $response['hits']['total']['value'] ?? 0;
        $data = array_map(function ($hit) {
            return $hit['_source'];
        }, $response['hits']['hits']);

    }

    public function mailPermissionsElastic(Request $request)
    {

        if (! $this->elasticService->indexExists(ELASTIC_EMAIL_PERMISSIONS)) {
            $params = [
                'index' => ELASTIC_EMAIL_PERMISSIONS,
                'body' => [
                    'settings' => [
                        'number_of_shards' => 1,
                        'number_of_replicas' => 0,
                        'index.blocks.read_only_allow_delete' => false,
                        'index.blocks.write' => false,
                        'index.blocks.read' => false,
                        'index.blocks.metadata' => false,
                        'index.lifecycle.name' => null,
                        'index.lifecycle.rollover_alias' => null,
                        'index.requests.cache.enable' => true,
                        'analysis' => [
                            'analyzer' => [
                                'comma_analyzer' => [
                                    'type' => 'custom',
                                    'tokenizer' => 'comma_tokenizer',
                                    'filter' => ['lowercase', 'trim'],
                                ],
                                'number_list_analyzer' => [
                                    'type' => 'custom',
                                    'tokenizer' => 'number_list_tokenizer',
                                    'filter' => ['trim'],
                                ],
                            ],
                            'tokenizer' => [
                                'comma_tokenizer' => [
                                    'type' => 'pattern',
                                    'pattern' => '\\s*,\\s*',
                                ],
                                'number_list_tokenizer' => [
                                    'type' => 'pattern',
                                    'pattern' => '\\s*,\\s*',
                                ],
                            ],
                            'filter' => [
                                'trim' => [
                                    'type' => 'trim',
                                ],
                            ],
                        ],
                    ],
                    'mappings' => [
                        'properties' => [
                            'id' => ['type' => 'long'],
                            'role_name' => ['type' => 'text'],
                            'notification_id' => ['type' => 'long'],
                        ],
                    ],
                ],
            ];

            $this->elasticService->getClient()->cluster()->putSettings([
                'body' => [
                    'persistent' => [
                        'action.destructive_requires_name' => true,
                    ],
                ],
            ]);

            $response = $this->elasticService->getClient()->indices()->create($params);
        }

        $skip = 0;
        $take = 1000;

        $coin_packages = DB::table('mail_permissions')->skip($skip)->take($take)->get();

        foreach ($coin_packages as $row) {

            $response = $this->elasticService->getClient()->update([
                'index' => ELASTIC_EMAIL_PERMISSIONS,
                'id' => $row->id,
                'retry_on_conflict' => 5,
                'body' => [
                    'doc' => [
                        'id' => $row->id,
                        'role_name' => $row->role_name,
                        'notification_id' => $row->notification_id,
                    ],
                    'doc_as_upsert' => true,
                    'detect_noop' => false,
                ],
            ]);
        }

        $page = max((int) $request->input('page', 1), 1);
        $size = (int) $request->input('size', 500); // Default 20 results per page
        $from = ($page - 1) * $size;
        $sort = [];
        $params = [
            'index' => ELASTIC_EMAIL_PERMISSIONS,
            'body' => [
                'from' => $from,
                'size' => $size,
                'sort' => $sort,
                'track_total_hits' => true,
            ],
        ];
        $response = $this->elasticService->getClient()->search($params);

        $total = $response['hits']['total']['value'] ?? 0;
        $data = array_map(function ($hit) {
            return $hit['_source'];
        }, $response['hits']['hits']);

    }

    public function masterNotificationsElastic(Request $request)
    {

        $templateData = DB::table('th_email_template')->select('*')->where('th_email_template.status', 1)->where('th_email_template.temptype', 1)->get();

        // dd($templateData);

        $params = [
            'index' => ELASTIC_EMAIL_TEMPLATE,
            'body' => [
                'size' => 10000,
                'query' => [
                    'bool' => [
                        'must' => [
                            ['term' => ['status' => 1]], // OK
                            ['match' => ['temptype' => 1]],    // TEXT field
                        ],
                    ],
                ],
            ],
        ];

        $response = $this->elasticService->getClient()->search($params);

        $th_email_template = collect(value: $response['hits']['hits'] ?? [])
            ->map(function ($hit) {
                return (object) $hit['_source'];
            });

        //  $company_id = 2;
        //  $type = "Recuitment";
        // 			$params = [
        // 		'index' => ELASTIC_MASTER_NOTIFICATIONS,
        // 		'body'  => [
        // 			'size' => 10000,
        // 			'query' => [
        // 				'bool' => [
        // 					'must' => [
        // 						['term'  => ['company_id' => (int) $company_id]], // OK
        // 						['match' => ['notifications_name' => $type]],    // TEXT field
        // 					],
        // 				],
        // 			],
        // 		],
        // 	];

        // $response = $this->elasticService->getClient()->search($params);
        // $data =  isset($response['hits']['hits'][0]) ? (object) $response['hits']['hits'][0]['_source'] : null;

        // dd($data);

        if (! $this->elasticService->indexExists(ELASTIC_MASTER_NOTIFICATIONS)) {
            $params = [
                'index' => ELASTIC_MASTER_NOTIFICATIONS,
                'body' => [
                    'settings' => [
                        'number_of_shards' => 1,
                        'number_of_replicas' => 0,
                        'index.blocks.read_only_allow_delete' => false,
                        'index.blocks.write' => false,
                        'index.blocks.read' => false,
                        'index.blocks.metadata' => false,
                        'index.lifecycle.name' => null,
                        'index.lifecycle.rollover_alias' => null,
                        'index.requests.cache.enable' => true,
                        'analysis' => [
                            'analyzer' => [
                                'comma_analyzer' => [
                                    'type' => 'custom',
                                    'tokenizer' => 'comma_tokenizer',
                                    'filter' => ['lowercase', 'trim'],
                                ],
                                'number_list_analyzer' => [
                                    'type' => 'custom',
                                    'tokenizer' => 'number_list_tokenizer',
                                    'filter' => ['trim'],
                                ],
                            ],
                            'tokenizer' => [
                                'comma_tokenizer' => [
                                    'type' => 'pattern',
                                    'pattern' => '\\s*,\\s*',
                                ],
                                'number_list_tokenizer' => [
                                    'type' => 'pattern',
                                    'pattern' => '\\s*,\\s*',
                                ],
                            ],
                            'filter' => [
                                'trim' => [
                                    'type' => 'trim',
                                ],
                            ],
                        ],
                    ],
                    'mappings' => [
                        'properties' => [
                            'id' => ['type' => 'long'],
                            'notifications_name' => ['type' => 'text'],
                            'notifications_email' => ['type' => 'long'],
                            'notifications_phone' => ['type' => 'long'],
                            'priority' => ['type' => 'long'],
                            'status' => ['type' => 'long'],
                            'email' => ['type' => 'text'],
                            'mobile' => ['type' => 'text'],
                            'company_id' => ['type' => 'long'],
                        ],
                    ],
                ],
            ];

            $this->elasticService->getClient()->cluster()->putSettings([
                'body' => [
                    'persistent' => [
                        'action.destructive_requires_name' => true,
                    ],
                ],
            ]);

            $response = $this->elasticService->getClient()->indices()->create($params);
        }

        $skip = 0;
        $take = 1000;

        $coin_packages = DB::table('master_notifications')->skip($skip)->take($take)->get();

        foreach ($coin_packages as $row) {

            $response = $this->elasticService->getClient()->update([
                'index' => ELASTIC_MASTER_NOTIFICATIONS,
                'id' => $row->id,
                'retry_on_conflict' => 5,
                'body' => [
                    'doc' => [
                        'id' => $row->id,
                        'notifications_name' => $row->notifications_name,
                        'notifications_email' => $row->notifications_email,
                        'notifications_phone' => $row->notifications_phone,
                        'priority' => $row->priority,
                        'status' => $row->status,
                        'email' => $row->email,
                        'mobile' => $row->mobile,
                        'company_id' => $row->company_id,
                    ],
                    'doc_as_upsert' => true,
                    'detect_noop' => false,
                ],
            ]);
        }

        $page = max((int) $request->input('page', 1), 1);
        $size = (int) $request->input('size', 500); // Default 20 results per page
        $from = ($page - 1) * $size;
        $sort = [];
        $params = [
            'index' => ELASTIC_MASTER_NOTIFICATIONS,
            'body' => [
                'from' => $from,
                'size' => $size,
                'sort' => $sort,
                'track_total_hits' => true,
            ],
        ];
        $response = $this->elasticService->getClient()->search($params);

        $total = $response['hits']['total']['value'] ?? 0;
        $data = array_map(function ($hit) {
            return $hit['_source'];
        }, $response['hits']['hits']);

    }

    public function thEmailTemplateElastic(Request $request)
    {

        if (! $this->elasticService->indexExists(ELASTIC_EMAIL_TEMPLATE)) {
            $params = [
                'index' => ELASTIC_EMAIL_TEMPLATE,
                'body' => [
                    'settings' => [
                        'number_of_shards' => 1,
                        'number_of_replicas' => 0,
                        'index.blocks.read_only_allow_delete' => false,
                        'index.blocks.write' => false,
                        'index.blocks.read' => false,
                        'index.blocks.metadata' => false,
                        'index.lifecycle.name' => null,
                        'index.lifecycle.rollover_alias' => null,
                        'index.requests.cache.enable' => true,
                        'analysis' => [
                            'analyzer' => [
                                'comma_analyzer' => [
                                    'type' => 'custom',
                                    'tokenizer' => 'comma_tokenizer',
                                    'filter' => ['lowercase', 'trim'],
                                ],
                                'number_list_analyzer' => [
                                    'type' => 'custom',
                                    'tokenizer' => 'number_list_tokenizer',
                                    'filter' => ['trim'],
                                ],
                            ],
                            'tokenizer' => [
                                'comma_tokenizer' => [
                                    'type' => 'pattern',
                                    'pattern' => '\\s*,\\s*',
                                ],
                                'number_list_tokenizer' => [
                                    'type' => 'pattern',
                                    'pattern' => '\\s*,\\s*',
                                ],
                            ],
                            'filter' => [
                                'trim' => [
                                    'type' => 'trim',
                                ],
                            ],
                        ],
                    ],
                    'mappings' => [
                        'properties' => [
                            'id' => ['type' => 'long'],
                            'e_temp_id' => ['type' => 'long'],
                            'template_name' => ['type' => 'text'],
                            'display_name' => ['type' => 'text'],
                            'template_subject' => ['type' => 'text'],
                            'template_description' => ['type' => 'text'],
                            'template_message' => ['type' => 'text'],
                            'mail_to' => ['type' => 'text'],
                            'mail_cc' => ['type' => 'text'],
                            'mail_bcc' => ['type' => 'text'],
                            'mail_from' => ['type' => 'text'],
                            'status' => ['type' => 'long'],
                            'curr_date' => ['type' => 'date'],
                            'temptype' => ['type' => 'long'], // 1- fwd_resume, 2- mass-mail, 3- interview, 4-send mail , 5-bench reject
                            'tempForInterview' => ['type' => 'long'],
                        ],
                    ],
                ],
            ];

            $this->elasticService->getClient()->cluster()->putSettings([
                'body' => [
                    'persistent' => [
                        'action.destructive_requires_name' => true,
                    ],
                ],
            ]);

            $response = $this->elasticService->getClient()->indices()->create($params);
        }

        // $mapping = $this->elasticService->getClient()->indices()->getMapping([
        //      'index' => ELASTIC_EMAIL_TEMPLATE
        //      ]);
        // dd($mapping);

        // $params = [
        //         'index' => ELASTIC_EMAIL_TEMPLATE,
        //         'body' => [
        //         'properties' => [
        //         'company_id' => ['type' => 'long'],
        //         'orderByShow' => ['type' => 'integer'],
        //         ]
        //     ]
        // ];

        // $response = $this->elasticService->getClient()->indices()->putMapping($params);

        // dd("gdgd");

        $skip = 0;
        $take = 1000;

        $coin_packages = DB::table('th_email_template')->skip($skip)->take($take)->get();
        // dd( $coin_packages);

        foreach ($coin_packages as $row) {

            $response = $this->elasticService->getClient()->update([
                'index' => ELASTIC_EMAIL_TEMPLATE,
                'id' => $row->e_temp_id,
                'retry_on_conflict' => 5,
                'body' => [
                    'doc' => [
                        'id' => $row->e_temp_id,
                        'e_temp_id' => $row->e_temp_id,
                        'template_name' => $row->template_name,
                        'display_name' => $row->display_name,
                        'template_subject' => $row->template_subject,
                        'template_description' => $row->template_description,
                        'template_message' => $row->template_message,
                        'mail_to' => $row->mail_to,
                        'mail_cc' => $row->mail_cc,
                        'mail_bcc' => $row->mail_bcc,
                        'mail_from' => $row->mail_from,
                        'status' => $row->status,
                        'curr_date' => ($row->curr_date) ? Carbon::parse($row->curr_date)->toIso8601String() : null,
                        'temptype' => $row->temptype,
                        'tempForInterview' => $row->tempForInterview,
                    ],
                    'doc_as_upsert' => true,
                    'detect_noop' => false,
                ],
            ]);
        }

        $page = max((int) $request->input('page', 1), 1);
        $size = (int) $request->input('size', 500); // Default 20 results per page
        $from = ($page - 1) * $size;
        $sort = [];
        $params = [
            'index' => ELASTIC_EMAIL_TEMPLATE,
            'body' => [
                'from' => $from,
                'size' => $size,
                'sort' => $sort,
                'track_total_hits' => true,
            ],
        ];
        $response = $this->elasticService->getClient()->search($params);

        $total = $response['hits']['total']['value'] ?? 0;
        $data = array_map(function ($hit) {
            return $hit['_source'];
        }, $response['hits']['hits']);

    }

    public function whatsappAndMailelastic(Request $request)
    {

        if (! $this->elasticService->indexExists(ELASTIC_EMAIL_COUNT)) {
            $params = [
                'index' => ELASTIC_EMAIL_COUNT,
                'body' => [
                    'settings' => [
                        'number_of_shards' => 1,
                        'number_of_replicas' => 0,
                        'index.blocks.read_only_allow_delete' => false,
                        'index.blocks.write' => false,
                        'index.blocks.read' => false,
                        'index.blocks.metadata' => false,
                        'index.lifecycle.name' => null,
                        'index.lifecycle.rollover_alias' => null,
                        'index.requests.cache.enable' => true,
                        'analysis' => [
                            'analyzer' => [
                                'comma_analyzer' => [
                                    'type' => 'custom',
                                    'tokenizer' => 'comma_tokenizer',
                                    'filter' => ['lowercase', 'trim'],
                                ],
                                'number_list_analyzer' => [
                                    'type' => 'custom',
                                    'tokenizer' => 'number_list_tokenizer',
                                    'filter' => ['trim'],
                                ],
                            ],
                            'tokenizer' => [
                                'comma_tokenizer' => [
                                    'type' => 'pattern',
                                    'pattern' => '\\s*,\\s*',
                                ],
                                'number_list_tokenizer' => [
                                    'type' => 'pattern',
                                    'pattern' => '\\s*,\\s*',
                                ],
                            ],
                            'filter' => [
                                'trim' => [
                                    'type' => 'trim',
                                ],
                            ],
                        ],
                    ],
                    'mappings' => [
                        'properties' => [
                            'id' => ['type' => 'long'],
                            'to_email' => ['type' => 'text'],
                            'subject' => ['type' => 'text'],
                            'mailBody' => ['type' => 'text'],
                            'emailType' => ['type' => 'integer'],
                            'company_id' => ['type' => 'long'],
                            'created_at' => ['type' => 'date'],
                            'updated_at' => ['type' => 'date'],
                        ],
                    ],
                ],
            ];

            $this->elasticService->getClient()->cluster()->putSettings([
                'body' => [
                    'persistent' => [
                        'action.destructive_requires_name' => true,
                    ],
                ],
            ]);

            $response = $this->elasticService->getClient()->indices()->create($params);
        }

        if (! $this->elasticService->indexExists(ELASTIC_WHATSAPP_COUNT)) {
            $params = [
                'index' => ELASTIC_WHATSAPP_COUNT,
                'body' => [
                    'settings' => [
                        'number_of_shards' => 1,
                        'number_of_replicas' => 0,
                        'index.blocks.read_only_allow_delete' => false,
                        'index.blocks.write' => false,
                        'index.blocks.read' => false,
                        'index.blocks.metadata' => false,
                        'index.lifecycle.name' => null,
                        'index.lifecycle.rollover_alias' => null,
                        'index.requests.cache.enable' => true,
                        'analysis' => [
                            'analyzer' => [
                                'comma_analyzer' => [
                                    'type' => 'custom',
                                    'tokenizer' => 'comma_tokenizer',
                                    'filter' => ['lowercase', 'trim'],
                                ],
                                'number_list_analyzer' => [
                                    'type' => 'custom',
                                    'tokenizer' => 'number_list_tokenizer',
                                    'filter' => ['trim'],
                                ],
                            ],
                            'tokenizer' => [
                                'comma_tokenizer' => [
                                    'type' => 'pattern',
                                    'pattern' => '\\s*,\\s*',
                                ],
                                'number_list_tokenizer' => [
                                    'type' => 'pattern',
                                    'pattern' => '\\s*,\\s*',
                                ],
                            ],
                            'filter' => [
                                'trim' => [
                                    'type' => 'trim',
                                ],
                            ],
                        ],
                    ],
                    'mappings' => [
                        'properties' => [
                            'id' => ['type' => 'long'],
                            'phone' => ['type' => 'text'],
                            'messageBody' => ['type' => 'text'],
                            'messageType' => ['type' => 'integer'],
                            'company_id' => ['type' => 'long'],
                            'created_at' => ['type' => 'date'],
                            'updated_at' => ['type' => 'date'],
                        ],
                    ],
                ],
            ];

            $this->elasticService->getClient()->cluster()->putSettings([
                'body' => [
                    'persistent' => [
                        'action.destructive_requires_name' => true,
                    ],
                ],
            ]);

            $response = $this->elasticService->getClient()->indices()->create($params);
        }

        $skip = 0;
        $take = 1000;

        $whatsapp_count_companies = DB::table('whatsapp_count_companies')->skip($skip)->take($take)->get();
        // dd( $coin_packages);

        foreach ($whatsapp_count_companies as $row) {

            $response = $this->elasticService->getClient()->update([
                'index' => ELASTIC_WHATSAPP_COUNT,
                'id' => $row->id,
                'retry_on_conflict' => 5,
                'body' => [
                    'doc' => [
                        'id' => $row->id,
                        'phone' => $row->phone,
                        'messageBody' => $row->messageBody,
                        'messageType' => $row->messageType,
                        'company_id' => $row->company_id,
                        'created_at' => ($row->created_at) ? Carbon::parse($row->created_at)->toIso8601String() : null,
                        'updated_at' => ($row->updated_at) ? Carbon::parse($row->updated_at)->toIso8601String() : null,
                    ],
                    'doc_as_upsert' => true,
                    'detect_noop' => false,
                ],
            ]);
        }

        $page = max((int) $request->input('page', 1), 1);
        $size = (int) $request->input('size', 500); // Default 20 results per page
        $from = ($page - 1) * $size;
        $sort = [];
        $params = [
            'index' => ELASTIC_WHATSAPP_COUNT,
            'body' => [
                'from' => $from,
                'size' => $size,
                'sort' => $sort,
                'track_total_hits' => true,
            ],
        ];
        $response = $this->elasticService->getClient()->search($params);

        $total = $response['hits']['total']['value'] ?? 0;
        $data = array_map(function ($hit) {
            return $hit['_source'];
        }, $response['hits']['hits']);

        // dd($total,$data);

        $email_count_companies = DB::table('email_count_companies')->skip($skip)->take($take)->get();
        // dd( $coin_packages);

        foreach ($email_count_companies as $row) {

            $response = $this->elasticService->getClient()->update([
                'index' => ELASTIC_EMAIL_COUNT,
                'id' => $row->id,
                'retry_on_conflict' => 5,
                'body' => [
                    'doc' => [
                        'id' => $row->id,
                        'to_email' => $row->to_email,
                        'subject' => $row->subject,
                        'mailBody' => $row->mailBody,
                        'emailType' => $row->emailType,
                        'company_id' => $row->company_id,
                        'created_at' => ($row->created_at) ? Carbon::parse($row->created_at)->toIso8601String() : null,
                        'updated_at' => ($row->updated_at) ? Carbon::parse($row->updated_at)->toIso8601String() : null,
                    ],
                    'doc_as_upsert' => true,
                    'detect_noop' => false,
                ],
            ]);
        }

        $page = max((int) $request->input('page', 1), 1);
        $size = (int) $request->input('size', 500); // Default 20 results per page
        $from = ($page - 1) * $size;
        $sort = [];
        $params = [
            'index' => ELASTIC_EMAIL_COUNT,
            'body' => [
                'from' => $from,
                'size' => $size,
                'sort' => $sort,
                'track_total_hits' => true,
            ],
        ];
        $response = $this->elasticService->getClient()->search($params);

        $total = $response['hits']['total']['value'] ?? 0;
        $data = array_map(function ($hit) {
            return $hit['_source'];
        }, $response['hits']['hits']);

    }

    public function companySkillselastic(Request $request)
    {

        if (! $this->elasticService->indexExists(ELASTIC_COMPANY_SKILLS)) {
            $params = [
                'index' => ELASTIC_COMPANY_SKILLS,
                'body' => [
                    'settings' => [
                        'number_of_shards' => 1,
                        'number_of_replicas' => 0,
                        'index.blocks.read_only_allow_delete' => false,
                        'index.blocks.write' => false,
                        'index.blocks.read' => false,
                        'index.blocks.metadata' => false,
                        'index.lifecycle.name' => null,
                        'index.lifecycle.rollover_alias' => null,
                        'index.requests.cache.enable' => true,
                        'analysis' => [
                            'analyzer' => [
                                'comma_analyzer' => [
                                    'type' => 'custom',
                                    'tokenizer' => 'comma_tokenizer',
                                    'filter' => ['lowercase', 'trim'],
                                ],
                                'number_list_analyzer' => [
                                    'type' => 'custom',
                                    'tokenizer' => 'number_list_tokenizer',
                                    'filter' => ['trim'],
                                ],
                            ],
                            'tokenizer' => [
                                'comma_tokenizer' => [
                                    'type' => 'pattern',
                                    'pattern' => '\\s*,\\s*',
                                ],
                                'number_list_tokenizer' => [
                                    'type' => 'pattern',
                                    'pattern' => '\\s*,\\s*',
                                ],
                            ],
                            'filter' => [
                                'trim' => [
                                    'type' => 'trim',
                                ],
                            ],
                        ],
                    ],
                    'mappings' => [
                        'properties' => [
                            'id' => ['type' => 'long'],
                            'company_id ' => ['type' => 'long'],
                            'job_id ' => ['type' => 'long'],
                            'skill_name' => ['type' => 'text'],
                            'created_at' => ['type' => 'date'],
                            'updated_at' => ['type' => 'date'],
                        ],
                    ],
                ],
            ];

            $this->elasticService->getClient()->cluster()->putSettings([
                'body' => [
                    'persistent' => [
                        'action.destructive_requires_name' => true,
                    ],
                ],
            ]);

            $response = $this->elasticService->getClient()->indices()->create($params);
        }

        $skip = 0;
        $take = 15000;

        $job_metronics_skills = DB::table('job_metronics_skills')->skip($skip)->take($take)->get();

        foreach ($job_metronics_skills as $row) {
            $skill_name = strtolower(trim($row->skill_name));
            if (empty($skill_name)) {
                continue;
            }

            $documentId = md5($row->company_id.'_'.$skill_name);

            $response = $this->elasticService->getClient()->update([
                'index' => ELASTIC_COMPANY_SKILLS,
                'id' => $documentId,
                'retry_on_conflict' => 5,
                'body' => [
                    'doc' => [
                        'id' => $row->id,
                        'company_id' => $row->company_id,
                        'job_id' => $row->job_id,
                        'skill_name' => $skill_name,
                        'created_at' => ($row->created_at) ? Carbon::parse($row->created_at)->toIso8601String() : null,
                        'updated_at' => ($row->updated_at) ? Carbon::parse($row->updated_at)->toIso8601String() : null,
                    ],
                    'doc_as_upsert' => true,
                    'detect_noop' => true,
                ],
            ]);
        }

        $page = max((int) $request->input('page', 1), 1);
        $size = (int) $request->input('size', 500); // Default 20 results per page
        $from = ($page - 1) * $size;
        $sort = [];
        $params = [
            'index' => ELASTIC_COMPANY_SKILLS,
            'body' => [
                'from' => $from,
                'size' => $size,
                'sort' => $sort,
                'track_total_hits' => true,
            ],
        ];
        $response = $this->elasticService->getClient()->search($params);

        $total = $response['hits']['total']['value'] ?? 0;
        $data = array_map(function ($hit) {
            return $hit['_source'];
        }, $response['hits']['hits']);

    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function deleteTemplate(Request $request)
    {
        $templateId = $request->id;
        $company_id = auth()->user()->company_id;

        if (! $templateId) {
            return response()->json(['status' => false, 'message' => 'Invalid template ID'], 400);
        }
        // ✅ Fetch template
        $template = DB::table('th_email_template')->where('e_temp_id', $templateId)->first();
        if (! $template) {
            return response()->json(['status' => false, 'message' => 'Template not found'], 404);
        }
        // 🚨 STRICT SECURITY
        // Allow ONLY if template belongs to logged-in company/user
        if ($template->company_id != $company_id) {
            return response()->json([
                'status' => false,
                'message' => '⚠️ You Can Delete Only Custom Templates',
            ], 403);
        }

        // ✅ Delete
        DB::table('th_email_template')->where('e_temp_id', $templateId)->delete();
        $this->elasticService->getClient()->delete([
            'index' => ELASTIC_EMAIL_TEMPLATE,
            'id' => $templateId,
        ]);

        return response()->json(['status' => true, 'message' => 'Template deleted successfully']);
    }

    public function index()
    {
        return view('home');
    }

    // ** Cron start here */
    /** Start update Currency Rates */
    public function updateCurrencyRates()
    {
        $currencies = DB::table('currencies')->whereNot('currency_code', 'INR')->pluck('currency_code')->toArray();

        foreach ($currencies as $code) {
            $response = Http::get('https://api.exchangerate-api.com/v4/latest/'.$code);

            if (! $response->successful()) {
                return 'API failed';
            }
            $rates = $response->json()['rates'];

            // ✅ Step 1: Get USD → INR
            $otherToInr = $rates['INR'];
            $usdToOther = $rates['USD'];

            if (! isset($rates[$code])) {
                continue;
            }

            // ✅ Reverse (INR → currency)
            DB::table('currencies')
                ->where('currency_code', $code)
                ->update([
                    'usd_based_rates' => round($usdToOther, 2),   // e.g. 1 USD = 83 INR
                    'inr_based_rates' => round($otherToInr, 2), // e.g. 1 INR = 0.012 USD
                    'updated_at' => now(),
                ]);
            if ($code == 'USD') {
                $code = 'INR';
                DB::table('currencies')
                    ->where('currency_code', $code)
                    ->update([
                        'usd_based_rates' => round($otherToInr, 2),   // e.g. 1 USD = 83 INR
                        'inr_based_rates' => round($usdToOther, 2), // e.g. 1 INR = 0.012 USD
                        'updated_at' => now(),
                    ]);
            }
        }
        $url = url('super-admin/currency-settings');

        return response()->json([
            'status' => true,
            'type' => 'success',
            'url' => $url,
            'message' => 'Currency updated successfully',
        ]);

    }
    /** end update Currency Rates */

    /** Start Similar Candidate Match */
    public function companyWiseJobs(Request $request)
    {

        // $company_id = auth()->user()->company_id;

        $companies = DB::table('companies')
            ->join('users', 'users.company_id', '=', 'companies.id')
            ->where('users.root_user', '=', 1)
            ->select('companies.*')->get();

        if (count($companies) > 0) {

            foreach ($companies as $val) {
                $company_id = $val->id;
                $allActiveJobs = DB::table('jobs')->select(
                    'jobs.id',
                    'clients.name as clients_name',
                    'recommendation_relation.id as recommendation_id',
                    'recommendation_relation.from_company_id',
                    'recommendation_relation.final_status',
                    'companies.company_name',
                    'recommendation_relation.to_company_id')
                    ->join('clients', 'clients.id', 'jobs.client_id')
                    ->join('companies', 'companies.id', 'jobs.company_id')
                    ->leftJoin('add_job_wise_condidates as job_applications', 'job_applications.job_id', '=', 'jobs.id')
                    ->leftJoin('recommendation_relation', 'recommendation_relation.job_id', '=', 'jobs.id')
                    ->leftJoin('job_metronics_skills', 'job_metronics_skills.job_id', 'jobs.id')
                    ->leftJoin('job_assign', function ($join) use ($company_id) {
                        $join->on('job_assign.job_id', '=', 'jobs.id');
                        $join->on('job_assign.company_id', '=', DB::raw("'".$company_id."'"));

                    })
                    ->where('jobs.id', '>', '0');
                $allActiveJobs = $allActiveJobs->where('jobs.company_id', '=', $company_id);
                $allActiveJobs = $allActiveJobs->where('jobs.status', '=', 'active');
                // dd("djd");
                $allActiveJobs = $allActiveJobs->orWhere(function ($query) use ($company_id) {
                    $query->where('recommendation_relation.to_company_id', '=', $company_id)
                        ->where('recommendation_relation.to_status', '=', 1);

                });
                $allActiveJobs = $allActiveJobs->distinct('job_applications.id');
                $allActiveJobs = $allActiveJobs->groupBy('jobs.id');

                $allActiveJobs = $allActiveJobs->get();

                // dd($allActiveJobs);
                $emailData = [];

                if (count($allActiveJobs) > 0) {

                    foreach ($allActiveJobs as $job_val) {

                        //  dd( $job_val);
                        $requiredSkills = [];
                        $jobid = $job_val->id;
                        $job_status = GeneralMethod::job_status();
                        $salaryMode = GeneralMethod::salaryMode();
                        $currencySymbole = GeneralMethod::currencySymbole();

                        $emailData = DB::table('job_assign')->join('users', 'users.id', '=', 'job_assign.user_id')->select('users.id', 'users.name', 'users.email')->where('job_assign.job_id', '=', $jobid)->where('job_assign.company_id', '=', $company_id)->where('users.company_id', '=', $company_id)->pluck('users.email')->toArray();

                        $requiredSkills = DB::table('job_metronics_skills')
                            ->where('job_id', '=', $jobid)
                            ->pluck('skill_name')
                            ->toArray();

                        $job = Job::withoutGlobalScopes()->findOrFail($jobid);
                        $job_title = $job->title;

                        $min_exp = ($job->min_exp >= 1) ? $job->min_exp - 1 : $job->min_exp;
                        $max_exp = $job->max_exp + 1;

                        $sal_mode = $job->min_sal_mode;
                        $currency = GeneralMethod::currency();  // Currency code 		dd($requiredSkills);

                        $currencyCheck = $currency[$job->min_salary_currency];
                        $min_salary_currency = $job->min_salary_currency;

                        $convertedAmount = $job->max_salary; // $job->max_salary;
                        if ($job->min_salary_currency > 0) {
                            $convertedAmount = GeneralMethod::convertToINR($convertedAmount, $currencyCheck);
                        }
                        $ectcdivided = GeneralMethod::salaryDividedByMode($sal_mode, $convertedAmount);
                        $min_salary = 0; // $job->min_salary;
                        // $max_salary = $job->max_salary;

                        if ($sal_mode == 0) {
                            $max_salary = $ectcdivided->yearly;

                        } elseif ($sal_mode == 1) {
                            $max_salary = $ectcdivided->monthly;

                        } elseif ($sal_mode == 2) {
                            $max_salary = $ectcdivided->hourly;

                        }

                        // dd($requiredSkills);
                        $default_loc = GeneralMethod::JobcitiesTags($jobid); // $gdFilterData->default_loc;
                        $qualification = GeneralMethod::allQualificationSelected($jobid); // $gdFilterData->qualification;

                        $location = [];
                        $parts = explode('|', $default_loc);
                        // Trim to remove any leading or trailing whitespace
                        $cityName = trim($parts[0]);
                        $loc_data = DB::table('city_list')->where('city_list.city', '=', $cityName)->first();
                        if ($loc_data) {
                            array_push($location, $loc_data->city_id);
                        }
                        $education = [];
                        $education_data = DB::table('qualification_master')->where('qualification_master.name', '=', $qualification)->first();
                        if ($education_data) {
                            $educationfiletr = GeneralMethod::qualificationFilterCondition($education_data->id);
                            $education = array_merge($education, $educationfiletr);
                        }

                        // dd($education);

                        $subQuery = DB::table('job_applications')
                            ->leftJoin('job_application_skills', 'job_applications.id', '=', 'job_application_skills.job_application_id')
                            ->whereIn('job_application_skills.skills', $requiredSkills)
                            ->orWhereIn('job_applications.current_designation', $requiredSkills) // Replace with actual current designation condition
                            ->select('job_applications.id')
                            ->groupBy('job_applications.id');

                        // Exclude candidates already in add_job_wise_condidates for the same job
                        $existingCandidatesQuery = DB::table('add_job_wise_condidates')
                            ->select('job_application_id')
                            ->where('job_id', '=', $jobid);

                        $jobApplications = DB::table('job_applications')
                            ->join('job_application_skills', 'job_applications.id', '=', 'job_application_skills.job_application_id')
                            ->leftJoin('add_job_wise_condidates', 'job_applications.id', '=', 'add_job_wise_condidates.job_application_id')
                            ->leftJoin('rec_candidate_assign', function ($join) use ($jobid, $company_id) {
                                $join->on('rec_candidate_assign.source_application_id', '=', 'job_applications.id')
                                    ->where('rec_candidate_assign.source_jobid', '!=', $jobid)
                                    ->where('rec_candidate_assign.source_from_type', '=', '2')
                                    ->where('rec_candidate_assign.source_company_id', '=', $company_id)
                                    ->where('rec_candidate_assign.source_from_company_id', '!=', $company_id);
                            })->join('master_status', function ($join) {
                                $join->on('master_status.id', 'add_job_wise_condidates.master_status_id');
                            })
                            ->join('jobs', 'add_job_wise_condidates.job_id', '=', 'jobs.id')
                            ->leftJoin('companies', 'companies.id', '=', 'jobs.company_id')
                            ->leftjoin('job_locations', 'job_locations.id', 'jobs.location_id')
                            ->leftjoin('clients', 'clients.id', 'jobs.client_id')
                            ->leftjoin('users', 'users.id', 'add_job_wise_condidates.submitted_by')
                            ->leftjoin('companies as uc', 'uc.id', '=', 'users.company_id')
                            ->leftjoin('qualification_master', 'qualification_master.id', 'job_applications.qualification')
                            ->leftjoin('application_status', 'application_status.id', 'job_applications.status_id')
                            ->leftjoin('state_list', 'state_list.id', 'job_applications.state')
                            ->leftjoin('bench_applications', 'bench_applications.id', 'rec_candidate_assign.source_from_id')
                            ->whereNotIn('job_applications.id', $existingCandidatesQuery)
                            ->whereIn('job_applications.id', $subQuery)
                            ->where('job_applications.master_status_id', '!=', 6)
                            ->where('job_applications.condidate_type', '=', 'other')
                            ->where(function ($query) use ($company_id) {
                                $query->where('add_job_wise_condidates.company_id', '=', $company_id)
                                    ->orWhere('add_job_wise_condidates.thirdparty_company_id', '=', $company_id);
                            })
                            ->where(function ($query) use ($company_id) {
                                $query->where('add_job_wise_condidates.thirdparty_company_id', '>', 0)
                                    ->where(function ($subQuery) use ($company_id) {
                                        $subQuery->where('add_job_wise_condidates.master_status_id', '!=', 8)
                                            ->orWhere(function ($innerSubQuery) use ($company_id) {
                                                $innerSubQuery->where('add_job_wise_condidates.thirdparty_company_id', '=', $company_id)
                                                    ->where('add_job_wise_condidates.master_status_id', '=', 8);
                                            });
                                    })
                                    ->orWhere('add_job_wise_condidates.thirdparty_company_id', '=', 0);
                            });

                        $jobApplications = $jobApplications->where(function ($query) use ($min_exp, $max_exp, $min_salary, $max_salary, $location, $education, $sal_mode) {
                            // if (\request('min_exp') != '' && \request('max_exp') != '') {
                            $query->whereBetween('job_applications.total_exp', [$min_exp, $max_exp]);
                            // }
                            // if (\request('max_sal') != '' && \request('min_salary_currency') != '' && \request('sal_mode') != '') {
                            if ($sal_mode == 2) {
                                $query->whereBetween('job_applications.ectc_hourly', [$min_salary, $max_salary]);
                            } elseif ($sal_mode == 1) {
                                $query->whereBetween('job_applications.ectc_monthly', [$min_salary, $max_salary]);
                            } else {
                                // dd($min_salary, $max_salary);
                                $query->whereBetween('job_applications.ectc_yearly', [$min_salary, $max_salary]);
                            }

                            // }
                            if (count($location) > 0) {
                                $query->whereIn('job_applications.city_id', $location);
                            }
                            if (count($education) > 0) {
                                $query->whereIn('job_applications.qualification', $education);
                            }

                        });

                        $jobApplications = $jobApplications->select(
                            'job_applications.*',
                            'jobs.title',
                            'job_locations.location',
                            'jobs.company_id as companyIdCheck',
                            'jobs.status as jobStatus',
                            'companies.company_name',
                            'add_job_wise_condidates.job_id as jobId',
                            'add_job_wise_condidates.thirdparty_company_id as ClientComID',
                            'application_status.status as a_status',
                            'clients.name as client_name',
                            'users.name as posted_by',
                            'users.image as posted_by_image',
                            'master_status.name',
                            'master_status.css_style',
                            'qualification_master.name as qualification_name',
                            'state_list.state as state_name',
                            'bench_applications.id as bench_applications_id',
                            'uc.company_name as uc_company_name',
                            'add_job_wise_condidates.child_status_id as childStatusId',
                            'add_job_wise_condidates.id as wiseId',
                            DB::raw('
                            (SELECT COUNT(*) 
                            FROM job_metronics_skills 
                            WHERE job_metronics_skills.job_id = '.$jobid.') as total_skills
                        '),
                            DB::raw('
                            (SELECT COUNT(*) 
                            FROM job_application_skills 
                            INNER JOIN job_metronics_skills 
                            ON FIND_IN_SET(job_metronics_skills.skill_name, job_application_skills.skills)
                            WHERE job_application_skills.job_application_id = job_applications.id 
                            AND job_metronics_skills.job_id = '.$jobid.') as matching_skills
                        '),
                            DB::raw('
                            CASE
                                WHEN (SELECT COUNT(*) 
                                    FROM job_metronics_skills 
                                    WHERE job_metronics_skills.job_id = '.$jobid.') > 0
                                THEN 
                                    (SELECT COUNT(*) 
                                    FROM job_application_skills 
                                    INNER JOIN job_metronics_skills 
                                    ON FIND_IN_SET(job_metronics_skills.skill_name, job_application_skills.skills)
                                    WHERE job_application_skills.job_application_id = job_applications.id 
                                    AND job_metronics_skills.job_id = '.$jobid.') 
                                    / 
                                    (SELECT COUNT(*) 
                                    FROM job_metronics_skills 
                                    WHERE job_metronics_skills.job_id = '.$jobid.') * 100
                                ELSE 0
                            END as skill_match_percentage
                        ')
                        )
                            ->groupBy('job_applications.id');
                        //  dd(request('sortingfilter'));

                        $jobApplications = $jobApplications->get();

                        $tracker_html = '';
                        if (count($jobApplications) > 0) {
                            $tracker_html = '<table align="left" border="1" style="border-collapse:collapse; border:solid 1px black; width:1272px">
                                    <thead>
                                    <tr style="mso-yfti-irow:0;mso-yfti-firstrow:yes;height:15.75pt"> 
                                    <td valign="bottom" style="border:solid windowtext 1.0pt;border-left:none; background:#BDD7EE;padding:1.5pt 2.25pt 1.5pt 2.25pt;height:15.75pt; overflow:hidden"> <p class="MsoNormal"><b><u><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">S.no<o:p></o:p></span></u></b></p></td>
                                        
                                    <td valign="bottom" style="border:solid windowtext 1.0pt;border-left:none; background:#BDD7EE;padding:1.5pt 2.25pt 1.5pt 2.25pt;height:15.75pt; overflow:hidden"> <p class="MsoNormal"><b><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">Name of the candidate<o:p></o:p></span></b></p></td>

                                    <td valign="bottom" style="border:solid windowtext 1.0pt;border-left:none; background:#BDD7EE;padding:1.5pt 2.25pt 1.5pt 2.25pt;height:15.75pt; overflow:hidden"> <p class="MsoNormal"><b><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">Notice<o:p></o:p></span></b></p></td>
                                
                                    <td valign="bottom" style="border:solid windowtext 1.0pt;border-left:none; background:#BDD7EE;padding:1.5pt 2.25pt 1.5pt 2.25pt;height:15.75pt; overflow:hidden"> <p class="MsoNormal"><b><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">Total Exp<o:p></o:p></span></b></p></td>
                                    <td valign="bottom" style="border:solid windowtext 1.0pt;border-left:none; background:#BDD7EE;padding:1.5pt 2.25pt 1.5pt 2.25pt;height:15.75pt; overflow:hidden"> <p class="MsoNormal"><b><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">Rel Exp<o:p></o:p></span></b></p></td>
                                    <td valign="bottom" style="border:solid windowtext 1.0pt;border-left:none; background:#BDD7EE;padding:1.5pt 2.25pt 1.5pt 2.25pt;height:15.75pt; overflow:hidden"> <p class="MsoNormal"><b><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">CCTC<o:p></o:p></span></b></p></td>
                                    <td valign="bottom" style="border:solid windowtext 1.0pt;border-left:none; background:#BDD7EE;padding:1.5pt 2.25pt 1.5pt 2.25pt;height:15.75pt; overflow:hidden"> <p class="MsoNormal"><b><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">ECTC<o:p></o:p></span></b></p></td>

                                    <td valign="bottom" style="border:solid windowtext 1.0pt;border-left:none; background:#BDD7EE;padding:1.5pt 2.25pt 1.5pt 2.25pt;height:15.75pt; overflow:hidden"> <p class="MsoNormal"><b><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">Location<o:p></o:p></span></b></p></td>

                                    <td valign="bottom" style="border:solid windowtext 1.0pt;border-left:none; background:#BDD7EE;padding:1.5pt 2.25pt 1.5pt 2.25pt;height:15.75pt; overflow:hidden"> <p class="MsoNormal"><b><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">Action<o:p></o:p></span></b></p></td>
                                    </tr>
                                    </thead>';

                            $cnt = 1;
                            foreach ($jobApplications as $appData) {

                                $existing_path = 'javascript:;';
                                if ($company_id == $appData->companyIdCheck) {
                                    $jobtype = 'Custom';
                                    $existing_path = url(
                                        '/service_company/apptracker/similar_candidates/'.GeneralMethod::value_encrypt(@$jobid),
                                    );
                                } else {
                                    $jobtype = 'Client';
                                    $existing_path = url('/service_company/apptracker/dsimilar_candidates/'.GeneralMethod::value_encrypt(@$jobid));
                                }
                                $job_title = $appData->title;
                                $currencyText = GeneralMethod::currency();
                                $ctc = '';
                                $ectc = '';
                                $current_ctc_cur = $currencyText[@$min_salary_currency];

                                $current_ectc_cur = '';
                                $current_ectc_cur = $currencyText[@$min_salary_currency];
                                // dd($row->ectc_yearly);
                                $currencymain = GeneralMethod::currency(); // Currency code
                                $currency = $currencymain[@$min_salary_currency];
                                // dd($currency);
                                $convertedAmounEctc = GeneralMethod::conversionRates($currency, $appData->ectc_yearly, 0);
                                $convertedAmounCtc = GeneralMethod::conversionRates($currency, $appData->ctc_yearly, 0);
                                // dd($convertedAmounEctc,$convertedAmounCtc,$convertedAmounEctc[$sal_mode]);
                                $ectc = $convertedAmounEctc[$sal_mode];
                                $ctc = $convertedAmounCtc[$sal_mode];

                                $ctc = GeneralMethod::thousandsCurrencyFormat($ctc);

                                $ectc = GeneralMethod::thousandsCurrencyFormat($ectc);

                                // dd($ectc,$ctc);

                                $current_ctc_mode = '';
                                $current_ctc_mode = mb_substr($salaryMode[$sal_mode], 0, 1);
                                // $current_ctc_mode		= mb_substr($salaryMode[0], 0, 1);

                                $current_ectc_mode = '';
                                $current_ectc_mode = mb_substr($salaryMode[$sal_mode], 0, 1);
                                $np = $appData->np_id;
                                if (empty($np)) {
                                    $np = 'N/A';
                                }
                                if ($appData->serving_notice_period_mode == 0) {
                                    if (! empty($appData->np_id)) {
                                        $np = $appData->np_id.'  Days';
                                    }
                                } elseif ($appData->serving_notice_period_mode == 1) {
                                    if (! empty($appData->np_id)) {
                                        $np = GeneralMethod::ymdTodmyNotTime($appData->np_id);
                                    }
                                } else {
                                    $np = $appData->np_id; // GeneralMethod::ymdTodmyNotTime($row->created_at)
                                }

                                $country = '';
                                $row_country_name = '';

                                if (empty($appData->country)) {
                                    $row_country_name = '----';
                                } else {
                                    if (! empty(@$appData->country)) {
                                        if (is_numeric(@$appData->country)) {
                                            $country_name_Arr = GeneralMethod::getCountryName(@$appData->country);
                                            foreach ($country_name_Arr as $cval) {
                                                $row_country_name = ucwords($cval['name']);
                                            }
                                        }
                                    }
                                }

                                $state = '';
                                $row_state_name = '';
                                if (empty($appData->state)) {
                                    $row_state_name = '----';
                                } else {
                                    if (@$appData->country == 101) {
                                        $row_state_name = @$appData->state_name;
                                    } else {
                                        $state = @$appData->state;
                                        if (is_numeric(@$appData->country) && is_numeric($state)) {
                                            if ($state > 0) {
                                                $state_name_Arr = GeneralMethod::getStateName(@$appData->country, $state);
                                                foreach ($state_name_Arr as $sval) {
                                                    $row_state_name = ucwords($sval['name']);
                                                }
                                            }
                                        }
                                    }
                                }

                                $city = '';
                                $row_city_name = '';

                                if (@$appData->country == 101) {
                                    $row_city = DB::table('city_list')
                                        ->select('*')
                                        ->where('city_id', @$appData->city_id)
                                        ->first();
                                    $row_city_name = @$row_city->city;
                                } else {
                                    $row_city_name = @$row->city;
                                }
                                $clients_name = ucwords(mb_substr(@$job_val->clients_name, 0, 30));
                                $recommendation_id = $job_val->recommendation_id;
                                if ($recommendation_id > 0 && @$job_val->to_company_id == $company_id) {
                                    $clients_name = ucwords(mb_substr(@$job_val->company_name, 0, 30));

                                    // dd("k",$clients_name);
                                }

                                // dd( $clients_name,@$job_val);

                                $tracker_html .= '
                                    <tr style="mso-yfti-irow:1;mso-yfti-lastrow:yes;height:15.75pt"> 
                                    <td valign="bottom" style="border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt; border-right:solid windowtext 1.0pt;background:white;padding:1.5pt 2.25pt 1.5pt 2.25pt; height:15.75pt;overflow:hidden"> <p class="MsoNormal"><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">'.$cnt.'<o:p></o:p></span></p></td>

                                    <td valign="bottom" style="border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt; border-right:solid windowtext 1.0pt;background:white;padding:1.5pt 2.25pt 1.5pt 2.25pt; height:15.75pt;overflow:hidden"> <p class="MsoNormal"><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">'.ucwords(@$appData->full_name).'<br>
                                    '.ucwords(mb_substr(@$appData->current_designation, 0, 27)).'<o:p></o:p></span></p></td>

                                    <td valign="bottom" style="border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt; border-right:solid windowtext 1.0pt;background:white;padding:1.5pt 2.25pt 1.5pt 2.25pt; height:15.75pt;overflow:hidden"> <p class="MsoNormal"><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">'.@$np.'<br><span>'.ucwords(@$appData->current_company).' </span><o:p></o:p></span></p></td>
    
                                    <td valign="bottom" style="border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt; border-right:solid windowtext 1.0pt;background:white;padding:1.5pt 2.25pt 1.5pt 2.25pt; height:15.75pt;overflow:hidden"> <p class="MsoNormal"><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">'.((@$appData->total_exp) ? @$appData->total_exp.' Y' : '').'<o:p></o:p></span></p></td>
                                    <td valign="bottom" style="border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt; border-right:solid windowtext 1.0pt;background:white;padding:1.5pt 2.25pt 1.5pt 2.25pt; height:15.75pt;overflow:hidden"> <p class="MsoNormal"><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">'.((@$appData->rel_exp) ? @$appData->rel_exp.' Y' : '').'<o:p></o:p></span></p></td>
                                    <td valign="bottom" style="border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt; border-right:solid windowtext 1.0pt;background:white;padding:1.5pt 2.25pt 1.5pt 2.25pt; height:15.75pt;overflow:hidden"> <p class="MsoNormal"><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">'.@$current_ctc_cur.' '.@$ctc.'/'.$current_ctc_mode.'<o:p></o:p></span></p></td>
                                    <td valign="bottom" style="border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt; border-right:solid windowtext 1.0pt;background:white;padding:1.5pt 2.25pt 1.5pt 2.25pt; height:15.75pt;overflow:hidden"> <p class="MsoNormal"><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">'.$current_ectc_cur.' '.@$ectc.'/'.$current_ctc_mode.'<o:p></o:p></span></p></td>

                                    <td valign="bottom" style="border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt; border-right:solid windowtext 1.0pt;background:white;padding:1.5pt 2.25pt 1.5pt 2.25pt; height:15.75pt;overflow:hidden"> <p class="MsoNormal"><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">'.ucwords(mb_substr($row_city_name, 0, 10)).'|'.ucfirst(mb_substr($row_state_name, 0, 5)).'|'.strtoupper(mb_substr($row_country_name, 0, 2)).'<o:p></o:p></span></p></td>


                                    <td valign="bottom" style="border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt; border-right:solid windowtext 1.0pt;background:white;padding:1.5pt 2.25pt 1.5pt 2.25pt; height:15.75pt;overflow:hidden"> <p class="MsoNormal"><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif"><a href="'.$existing_path.'">View Resume</a><o:p></o:p></span></p></td>				
    
                                    </tr>';

                                $cnt = $cnt + 1;
                            }
                            $tracker_html .= '<table>';
                        }
                        // dd($jobApplications);

                        $basecompany = GeneralMethod::baseCompany();
                        $senderName = ($basecompany->companyName) ? $basecompany->companyName : '';
                        $fromAddress = ($basecompany->companyEmail) ? $basecompany->companyEmail : '';
                        $cc = $emailData;
                        $attachments = [];

                        if (count($jobApplications) > 0) {
                            $checkMailPermision = GeneralMethod::mailPermission(36);
                            $email_notifications = DB::table('email_notifications')->where('id', 36)->where('status', 1)->first();

                            $subject = str_replace(['[JOB_TITLE]', '[CLIENT_COMPANY_NAME]', '[JOB_ID]'], [@$job_title, @$job_val->clients_name, @$jobid], @$email_notifications->subject);

                            $tracker_html = str_replace(['[JOB_TITLE]', '[CLIENT_COMPANY_NAME]', '[JOB_ID]', '[TRACKER]'], [@$job_title, @$job_val->clients_name, @$jobid, $tracker_html], @$email_notifications->body_description);

                            $data = ['message' => $tracker_html, 'subject' => $subject, 'company_name' => '4Bell Technology', 'consultant_name' => '4Bell Technology', 'position' => 'PHP'];
                            $email = $val->company_email;
                            $viewfile = 'email.common.index';

                            if ($checkMailPermision) {
                                QueueNotificationMailService::dispatch(
                                    $email,
                                    $subject,
                                    $viewfile,
                                    $data,
                                    $attachments,
                                    $fromAddress,
                                    $senderName,
                                    $cc,
                                    $company_id
                                )->onConnection('emails');
                            }

                        }

                        // $this->mailService->sendEmail('4belldevelopers@gmail.com',$subject,'email.common.index', $data, $attachments, $fromAddress,$senderName,$cc);

                        // dd("l");

                    }
                }

            }

        }

    }
    /** End Similar Candidate Match */

    /** Start Market Bench Candidate Match */
    public function marketcompanyWiseJobs(Request $request)
    {

        // $company_id = auth()->user()->company_id;

        $companies = DB::table('companies')
            ->join('users', 'users.company_id', '=', 'companies.id')
            ->where('users.root_user', '=', 1)
            ->select('companies.*')->get();

        // dd( $companies);

        if (count($companies) > 0) {

            foreach ($companies as $val) {

                $company_id = $val->id;
                $allActiveJobs = DB::table('jobs')->select(
                    'jobs.id',
                    'jobs.title',
                    'jobs.work_type',
                    'jobs.employment_type',
                    'jobs.total_positions',
                    'jobs.priority',
                    'jobs.status',
                    'jobs.min_exp',
                    'jobs.max_exp',
                    'jobs.created_at',
                    'jobs.start_date',
                    'jobs.end_date',
                    'jobs.min_salary',
                    'jobs.max_salary',
                    'jobs.min_sal_mode',
                    'jobs.max_sal_mode',
                    'jobs.company_id as companyidcount',
                    'jobs.min_salary_currency',
                    'jobs.max_salary_currency',
                    'clients.name as clients_name',
                    'clients.logo_logical_path',
                    'clients.self_company',
                    'companies.company_name')
                    ->join('clients', 'clients.id', 'jobs.client_id')
                    ->join('companies', 'companies.id', 'jobs.company_id')
                    ->leftJoin('add_job_wise_condidates as job_applications', 'job_applications.job_id', '=', 'jobs.id')
                    ->leftJoin('job_metronics_skills', 'job_metronics_skills.job_id', 'jobs.id')
                    ->leftJoin('job_assign', function ($join) use ($company_id) {
                        $join->on('job_assign.job_id', '=', 'jobs.id');
                        $join->on('job_assign.company_id', '=', DB::raw("'".$company_id."'"));

                    })
                    ->where('jobs.id', '>', '0');
                $allActiveJobs = $allActiveJobs->where('jobs.company_id', '=', $company_id);
                $allActiveJobs = $allActiveJobs->where('jobs.status', '=', 'active');
                // dd("djd");

                $allActiveJobs = $allActiveJobs->distinct('job_applications.id');
                $allActiveJobs = $allActiveJobs->groupBy('jobs.id');

                $allActiveJobs = $allActiveJobs->get();

                $emailData = [];

                if (count($allActiveJobs) > 0) {

                    foreach ($allActiveJobs as $job_val) {

                        $requiredSkills = [];
                        $jobid = $job_val->id;
                        $job_status = GeneralMethod::job_status();
                        $salaryMode = GeneralMethod::salaryMode();
                        $currencySymbole = GeneralMethod::currencySymbole();
                        // dd($jobid);
                        $emailData = DB::table('job_assign')->join('users', 'users.id', '=', 'job_assign.user_id')->select('users.id', 'users.name', 'users.email')->where('job_assign.job_id', '=', $jobid)->where('job_assign.company_id', '=', $company_id)->where('users.company_id', '=', $company_id)->pluck('users.email')->toArray();

                        // dd( $emailData);

                        $requiredSkills = DB::table('job_metronics_skills')
                            ->where('job_id', '=', $jobid)
                            ->pluck('skill_name')
                            ->toArray();

                        $job = Job::withoutGlobalScopes()->findOrFail($jobid);
                        $job_title = $job->title;

                        $min_exp = ($job->min_exp >= 1) ? $job->min_exp - 1 : $job->min_exp;
                        $max_exp = $job->max_exp + 1;

                        $sal_mode = $job->min_sal_mode;
                        $currency = GeneralMethod::currency();  // Currency code 		dd($requiredSkills);

                        $currencyCheck = $currency[$job->min_salary_currency];
                        $min_salary_currency = $job->min_salary_currency;

                        $convertedAmount = $job->max_salary; // $job->max_salary;
                        if ($job->min_salary_currency > 0) {
                            $convertedAmount = GeneralMethod::convertToINR($convertedAmount, $currencyCheck);
                        }
                        $ectcdivided = GeneralMethod::salaryDividedByMode($sal_mode, $convertedAmount);
                        $min_salary = 0; // $job->min_salary;
                        // $max_salary = $job->max_salary;

                        if ($sal_mode == 0) {
                            $max_salary = $ectcdivided->yearly;

                        } elseif ($sal_mode == 1) {
                            $max_salary = $ectcdivided->monthly;

                        } elseif ($sal_mode == 2) {
                            $max_salary = $ectcdivided->hourly;

                        }

                        // dd($requiredSkills);
                        $default_loc = GeneralMethod::JobcitiesTags($jobid); // $gdFilterData->default_loc;
                        $qualification = GeneralMethod::allQualificationSelected($jobid); // $gdFilterData->qualification;

                        $location = [];
                        $parts = explode('|', $default_loc);
                        // Trim to remove any leading or trailing whitespace
                        $cityName = trim($parts[0]);
                        $loc_data = DB::table('city_list')->where('city_list.city', '=', $cityName)->first();
                        if ($loc_data) {
                            array_push($location, $loc_data->city_id);
                        }
                        $education = [];
                        $education_data = DB::table('qualification_master')->where('qualification_master.name', '=', $qualification)->first();
                        if ($education_data) {
                            $educationfiletr = GeneralMethod::qualificationFilterCondition($education_data->id);
                            $education = array_merge($education, $educationfiletr);
                        }

                        // dd($education);

                        $subQuery = DB::table('job_applications')
                            ->leftJoin('job_application_skills', 'job_applications.id', '=', 'job_application_skills.job_application_id')
                            ->whereIn('job_application_skills.skills', $requiredSkills)
                            ->orWhereIn('job_applications.current_designation', $requiredSkills) // Replace with actual current designation condition
                            ->select('job_applications.id')
                            ->groupBy('job_applications.id');

                        // Exclude candidates already in add_job_wise_condidates for the same job
                        $existingCandidatesQuery = DB::table('add_job_wise_condidates')
                            ->select('job_application_id')
                            ->where('job_id', '=', $jobid);

                        $benchApplications = DB::table('job_applications')
                            ->join('job_application_skills', 'job_applications.id', '=', 'job_application_skills.job_application_id')
                            ->leftJoin('add_job_wise_condidates', 'job_applications.id', '=', 'add_job_wise_condidates.job_application_id')
                            ->leftjoin('package_active', 'package_active.company_id', 'job_applications.company_id')
                            ->join('company_coins', 'company_coins.company_id', 'job_applications.company_id')
                            ->leftjoin('bench_master_status', function ($join) {
                                $join->on('bench_master_status.id', 'add_job_wise_condidates.master_status_id');
                            })
                            ->leftjoin('qualification_master', 'qualification_master.id', 'job_applications.qualification')
                            ->where('job_applications.condidate_type', '=', 'bench_condidate')
                            ->whereIn('job_applications.id', $subQuery)
                            ->where('job_applications.status', 2)
                        // ->where('job_applications.master_status_id', '!=', 6)
                            ->whereNotIn('job_applications.id', $existingCandidatesQuery)
                            ->where('job_applications.company_id', '!=', $company_id)
                            ->where('company_coins.coins', '>', 0)
                            ->where('company_coins.end_date', '>=', date('Y-m-d'));

                        $benchApplications = $benchApplications->where(function ($query) use ($min_exp, $max_exp, $min_salary, $max_salary, $location, $education) {
                            $query->where('job_applications.countforadd', '<', 3);

                            $query->whereBetween('job_applications.total_exp', [$min_exp, $max_exp]);
                            $query->whereBetween('job_applications.ectc_hourly', [$min_salary, $max_salary]);
                            $query->whereBetween('job_applications.ectc_monthly', [$min_salary, $max_salary]);
                            $query->whereBetween('job_applications.ectc_yearly', [$min_salary, $max_salary]);
                            $query->whereIn('job_applications.city_id', $location);
                            $query->whereIn('job_applications.qualification', $education);

                        });
                        $benchApplications = $benchApplications->select(
                            'job_applications.*',
                            'qualification_master.name as qualification_name',
                            'bench_master_status.name as master_status_name',
                            'bench_master_status.css_style',
                            'add_job_wise_condidates.id as wiseId',
                            DB::raw('
                                                        (SELECT COUNT(*) 
                                                        FROM job_metronics_skills 
                                                        WHERE job_metronics_skills.job_id = '.$jobid.') as total_skills
                                                    '),
                            DB::raw('
                                                        (SELECT COUNT(*) 
                                                        FROM job_application_skills 
                                                        INNER JOIN job_metronics_skills 
                                                        ON FIND_IN_SET(job_metronics_skills.skill_name, job_application_skills.skills)
                                                        WHERE job_application_skills.job_application_id = job_applications.id 
                                                        AND job_metronics_skills.job_id = '.$jobid.') as matching_skills
                                                    '),
                            DB::raw('
                                                        CASE
                                                            WHEN (SELECT COUNT(*) 
                                                                FROM job_metronics_skills 
                                                                WHERE job_metronics_skills.job_id = '.$jobid.') > 0
                                                            THEN 
                                                                (SELECT COUNT(*) 
                                                                FROM job_application_skills 
                                                                INNER JOIN job_metronics_skills 
                                                                ON FIND_IN_SET(job_metronics_skills.skill_name, job_application_skills.skills)
                                                                WHERE job_application_skills.job_application_id = job_applications.id 
                                                                AND job_metronics_skills.job_id = '.$jobid.') 
                                                                / 
                                                                (SELECT COUNT(*) 
                                                                FROM job_metronics_skills 
                                                                WHERE job_metronics_skills.job_id = '.$jobid.') * 100
                                                            ELSE 0
                                                        END as skill_match_percentage
                                                    ')
                        )
                            ->groupBy('job_applications.id');

                        $benchApplications = $benchApplications->get();

                        $tracker_html = '';
                        if (count($benchApplications) > 0) {

                            $tracker_html = '<table align="left" border="1" style="border-collapse:collapse; border:solid 1px black; width:1272px">
                                    <thead>
                                    <tr style="mso-yfti-irow:0;mso-yfti-firstrow:yes;height:15.75pt"> 
                                    <td valign="bottom" style="border:solid windowtext 1.0pt;border-left:none; background:#BDD7EE;padding:1.5pt 2.25pt 1.5pt 2.25pt;height:15.75pt; overflow:hidden"> <p class="MsoNormal"><b><u><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">S.no<o:p></o:p></span></u></b></p></td>
                                    <td valign="bottom" style="border:solid windowtext 1.0pt;background:#BDD7EE; padding:1.5pt 2.25pt 1.5pt 2.25pt;height:15.75pt"> <p class="MsoNormal"><b><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">Jobs</span></b><b><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif;mso-fareast-language: EN-IN"><o:p></o:p></span></b></p></td>				
                                    <td valign="bottom" style="border:solid windowtext 1.0pt;border-left:none; background:#BDD7EE;padding:1.5pt 2.25pt 1.5pt 2.25pt;height:15.75pt; overflow:hidden"> <p class="MsoNormal"><b><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">Name of the candidate<o:p></o:p></span></b></p></td>

                                    <td valign="bottom" style="border:solid windowtext 1.0pt;border-left:none; background:#BDD7EE;padding:1.5pt 2.25pt 1.5pt 2.25pt;height:15.75pt; overflow:hidden"> <p class="MsoNormal"><b><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">Notice<o:p></o:p></span></b></p></td>
                                
                                    <td valign="bottom" style="border:solid windowtext 1.0pt;border-left:none; background:#BDD7EE;padding:1.5pt 2.25pt 1.5pt 2.25pt;height:15.75pt; overflow:hidden"> <p class="MsoNormal"><b><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">Total Exp<o:p></o:p></span></b></p></td>
                                    <td valign="bottom" style="border:solid windowtext 1.0pt;border-left:none; background:#BDD7EE;padding:1.5pt 2.25pt 1.5pt 2.25pt;height:15.75pt; overflow:hidden"> <p class="MsoNormal"><b><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">Rel Exp<o:p></o:p></span></b></p></td>
                                    

                                    <td valign="bottom" style="border:solid windowtext 1.0pt;border-left:none; background:#BDD7EE;padding:1.5pt 2.25pt 1.5pt 2.25pt;height:15.75pt; overflow:hidden"> <p class="MsoNormal"><b><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">Bill Rate<o:p></o:p></span></b></p></td>

                                    <td valign="bottom" style="border:solid windowtext 1.0pt;border-left:none; background:#BDD7EE;padding:1.5pt 2.25pt 1.5pt 2.25pt;height:15.75pt; overflow:hidden"> <p class="MsoNormal"><b><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">Location<o:p></o:p></span></b></p></td>

                                    <td valign="bottom" style="border:solid windowtext 1.0pt;border-left:none; background:#BDD7EE;padding:1.5pt 2.25pt 1.5pt 2.25pt;height:15.75pt; overflow:hidden"> <p class="MsoNormal"><b><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">Action<o:p></o:p></span></b></p></td>
                                    </tr>
                                    </thead>';

                            $cnt = 1;
                            foreach ($benchApplications as $appData) {

                                $existing_path = 'javascript:;';
                                $jobtype = 'Internal';
                                $currencyText = GeneralMethod::currency();
                                $ctc = '';
                                $ectc = '';
                                $current_ctc_cur = $currencyText[@$min_salary_currency];

                                $current_ectc_cur = '';
                                $current_ectc_cur = $currencyText[@$min_salary_currency];
                                // dd($row->ectc_yearly);
                                $currencymain = GeneralMethod::currency(); // Currency code
                                $currency = $currencymain[@$min_salary_currency];
                                // dd($currency);
                                $convertedAmounEctc = GeneralMethod::conversionRates($currency, $appData->ectc_yearly, 0);
                                $convertedAmounCtc = GeneralMethod::conversionRates($currency, $appData->ctc_yearly, 0);
                                // dd($convertedAmounEctc,$convertedAmounCtc,$convertedAmounEctc[$sal_mode]);
                                $ectc = $convertedAmounEctc[$appData->current_ectc_mode];
                                $ctc = $convertedAmounCtc[$appData->current_ectc_mode];

                                $ctc = GeneralMethod::thousandsCurrencyFormat($ctc);

                                $ectc = GeneralMethod::thousandsCurrencyFormat($ectc);

                                // dd($ectc,$ctc);

                                $current_ctc_mode = '';
                                $current_ctc_mode = mb_substr($salaryMode[$appData->current_ectc_mode], 0, 1);
                                // $current_ctc_mode		= mb_substr($salaryMode[0], 0, 1);

                                $current_ectc_mode = '';
                                $current_ectc_mode = mb_substr($salaryMode[$appData->current_ectc_mode], 0, 1);
                                $np = $appData->np_id;
                                if (empty($np)) {
                                    $np = 'N/A';
                                }
                                if ($appData->serving_notice_period_mode == 0) {
                                    if (! empty($appData->np_id)) {
                                        $np = $appData->np_id.'  Days';
                                    }
                                } elseif ($appData->serving_notice_period_mode == 1) {
                                    if (! empty($appData->np_id)) {
                                        $np = GeneralMethod::ymdTodmyNotTime($appData->np_id);
                                    }
                                } else {
                                    $np = $appData->np_id; // GeneralMethod::ymdTodmyNotTime($row->created_at)
                                }

                                $country = '';
                                $row_country_name = '';

                                if (empty($appData->country)) {
                                    $row_country_name = '----';
                                } else {
                                    if (! empty(@$appData->country)) {
                                        if (is_numeric(@$appData->country)) {
                                            $country_name_Arr = GeneralMethod::getCountryName(@$appData->country);
                                            foreach ($country_name_Arr as $cval) {
                                                $row_country_name = ucwords($cval['name']);
                                            }
                                        }
                                    }
                                }

                                $state = '';
                                $row_state_name = '';
                                if (empty($appData->state)) {
                                    $row_state_name = '----';
                                } else {
                                    if (@$appData->country == 101) {
                                        $row_state_name = @$appData->state_name;
                                    } else {
                                        $state = @$appData->state;
                                        if (is_numeric(@$appData->country) && is_numeric($state)) {
                                            if ($state > 0) {
                                                $state_name_Arr = GeneralMethod::getStateName(@$appData->country, $state);
                                                foreach ($state_name_Arr as $sval) {
                                                    $row_state_name = ucwords($sval['name']);
                                                }
                                            }
                                        }
                                    }
                                }

                                $city = '';
                                $row_city_name = '';

                                if (@$appData->country == 101) {
                                    $row_city = DB::table('city_list')
                                        ->select('*')
                                        ->where('city_id', @$appData->city_id)
                                        ->first();
                                    $row_city_name = @$row_city->city;
                                } else {
                                    $row_city_name = @$row->city;
                                }
                                $clients_name = ucwords(mb_substr(@$job_val->clients_name, 0, 30));

                                $tracker_html .= '
                                    <tr style="mso-yfti-irow:1;mso-yfti-lastrow:yes;height:15.75pt"> 
                                    <td valign="bottom" style="border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt; border-right:solid windowtext 1.0pt;background:white;padding:1.5pt 2.25pt 1.5pt 2.25pt; height:15.75pt;overflow:hidden"> <p class="MsoNormal"><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">'.$cnt.'<o:p></o:p></span></p></td>
                                    <td valign="bottom" style="border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt; border-right:solid windowtext 1.0pt;background:white;padding:1.5pt 2.25pt 1.5pt 2.25pt; height:15.75pt;overflow:hidden"> <p class="MsoNormal"><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">'.ucwords($job_title).'<br><span>R'.(@$jobid).'-'.$jobtype.' </span><o:p></o:p></span></p></td>

                                    <td valign="bottom" style="border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt; border-right:solid windowtext 1.0pt;background:white;padding:1.5pt 2.25pt 1.5pt 2.25pt; height:15.75pt;overflow:hidden"> <p class="MsoNormal"><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">'.ucwords(@$appData->full_name).'<br>
                                    '.ucwords(mb_substr(@$appData->current_designation, 0, 27)).'<o:p></o:p></span></p></td>

                                    <td valign="bottom" style="border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt; border-right:solid windowtext 1.0pt;background:white;padding:1.5pt 2.25pt 1.5pt 2.25pt; height:15.75pt;overflow:hidden"> <p class="MsoNormal"><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">'.@$np.'<br><span>'.ucwords(@$appData->current_company).' </span><o:p></o:p></span></p></td>
    
                                    <td valign="bottom" style="border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt; border-right:solid windowtext 1.0pt;background:white;padding:1.5pt 2.25pt 1.5pt 2.25pt; height:15.75pt;overflow:hidden"> <p class="MsoNormal"><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">'.((@$appData->total_exp) ? @$appData->total_exp.' Y' : '').'<o:p></o:p></span></p></td>
                                    <td valign="bottom" style="border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt; border-right:solid windowtext 1.0pt;background:white;padding:1.5pt 2.25pt 1.5pt 2.25pt; height:15.75pt;overflow:hidden"> <p class="MsoNormal"><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">'.((@$appData->rel_exp) ? @$appData->rel_exp.' Y' : '').'<o:p></o:p></span></p></td>
                                    
                                    <td valign="bottom" style="border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt; border-right:solid windowtext 1.0pt;background:white;padding:1.5pt 2.25pt 1.5pt 2.25pt; height:15.75pt;overflow:hidden"> <p class="MsoNormal"><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">'.$current_ectc_cur.' '.' '.@$ectc.'/'.$current_ctc_mode.'<o:p></o:p></span></p></td>

                                    <td valign="bottom" style="border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt; border-right:solid windowtext 1.0pt;background:white;padding:1.5pt 2.25pt 1.5pt 2.25pt; height:15.75pt;overflow:hidden"> <p class="MsoNormal"><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">'.ucwords(mb_substr($row_city_name, 0, 10)).'|'.ucfirst(mb_substr($row_state_name, 0, 5)).'|'.strtoupper(mb_substr($row_country_name, 0, 2)).'<o:p></o:p></span></p></td>


                                    <td valign="bottom" style="border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt; border-right:solid windowtext 1.0pt;background:white;padding:1.5pt 2.25pt 1.5pt 2.25pt; height:15.75pt;overflow:hidden"> <p class="MsoNormal"><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif"><a href="'.$existing_path.'">View</a><o:p></o:p></span></p></td>				
    
                                    </tr>';

                                $cnt = $cnt + 1;
                            }
                            $tracker_html .= '<table>';
                        }

                        $basecompany = GeneralMethod::baseCompany();
                        $senderName = ($basecompany->companyName) ? $basecompany->companyName : '';
                        $fromAddress = ($basecompany->companyEmail) ? $basecompany->companyEmail : '';
                        $cc = $emailData;
                        $attachments = [];
                        if (count($benchApplications) > 0) {
                            $checkMailPermision = GeneralMethod::mailPermission(35);
                            $email_notifications = DB::table('email_notifications')->where('id', 35)->where('status', 1)->first();
                            $subject = str_replace(['[JOB_TITLE]', '[CLIENT_COMPANY_NAME]', '[JOB_ID]'], [@$job_title, @$job_val->clients_name, @$jobid], @$email_notifications->subject);
                            $tracker_html = str_replace(['[JOB_TITLE]', '[CLIENT_COMPANY_NAME]', '[JOB_ID]', '[TRACKER]'], [@$job_title, @$job_val->clients_name, @$jobid, $tracker_html], @$email_notifications->body_description);

                            $data = ['message' => $tracker_html, 'subject' => $subject, 'company_name' => '4Bell Technology', 'consultant_name' => '4Bell Technology', 'position' => 'PHP'];
                            $email = $val->company_email;
                            $viewfile = 'email.common.index';
                            // dd($email);
                            if ($checkMailPermision) {
                                QueueNotificationMailService::dispatch(
                                    $email,
                                    $subject,
                                    $viewfile,
                                    $data,
                                    $attachments,
                                    $fromAddress,
                                    $senderName,
                                    $cc,
                                    $company_id
                                )->onConnection('emails');
                            }
                        }

                        // $this->mailService->sendEmail($val->company_email,$subject,'email.common.index', $data, $attachments, $fromAddress,$senderName,$cc);

                        // dd("l");

                    }
                }

            }

        }

        // dd($companies);

    }
    /** End Market Bench Candidate Match */

    /** Start Market Jobs Match */
    public function marketJobs(Request $request)
    {

        $job_status = GeneralMethod::job_status();
        $salaryMode = GeneralMethod::salaryMode();
        $currencySymbole = GeneralMethod::currencySymbole();

        // $companies = DB::table('companies')
        // ->join('users', 'users.company_id', '=', 'companies.id')
        // ->whereIn('company_type',[1,2])
        // ->where('users.root_user','=',1)
        // ->select('companies.*')->get();

        $searchQuery = $request->input('q');
        $page = max((int) $request->input('page', 1), 1);
        $size = (int) $request->input('size', 10000); // Default 20 results per page
        $from = ($page - 1) * $size;

        $must[] = [
            'bool' => [
                'should' => [
                    // Case 2: self_company = 1 and nested relation matches
                    [
                        'bool' => [
                            'must' => [
                                ['terms' => ['company_type' => [1, 2]]],
                                ['term' => ['status' => 'active']],
                            ],
                        ],
                    ],
                ],
                'minimum_should_match' => 1,
            ],
        ];

        $params = [
            'index' => ELASTIC_COMPANIES_INDEX,
            'body' => [
                'from' => $from,
                'size' => $size,
                'sort' => [],
                'query' => [
                    'bool' => [
                        'must' => $must,
                    ],
                ],
            ],
        ];

        if (! $this->elasticService->indexExists(ELASTIC_JOBS)) {
            $total = 0;
            $companies = [];
        } else {
            $response = $this->elasticService->getClient()->search($params);
            $total = $response['hits']['total']['value'] ?? 0;
            $companies = array_map(function ($hit) {
                return $hit['_source'];
            }, $response['hits']['hits']);
        }

        if (count($companies) > 0) {

            foreach ($companies as $rowArr) {
                $val = (object) $rowArr;
                $company_id = $val->id;
                // $existingJobQuery = DB::table('recommendation_relation')
                // ->where('to_company_id', '=', $company_id)
                // ->pluck('job_id')->toArray();

                $searchQuery = $request->input('q');
                $page = max((int) $request->input('page', 1), 1);
                $size = (int) $request->input('size', 10000); // Default 20 results per page
                $from = ($page - 1) * $size;

                $must = [];
                $should = [];
                $filter = [];

                $must[] = [
                    'bool' => [
                        'must' => [
                            [
                                'term' => ['public_market_status' => 2],
                            ],
                        ],
                        'must_not' => [
                            [
                                'term' => [
                                    'comId' => $company_id,
                                ],
                            ],
                            [
                                'nested' => [
                                    'path' => 'recommendation_relation',
                                    'query' => [
                                        'bool' => [
                                            'must' => [
                                                ['terms' => ['recommendation_relation.final_status' => [1, 2]]],
                                                ['term' => ['recommendation_relation.to_company_id' => $company_id]],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ];
                $statusArr = ['active'];
                $filter[] = ['terms' => ['status' => $statusArr]];
                $params = [
                    'index' => ELASTIC_JOBS,
                    'request_cache' => true,
                    'body' => [
                        'from' => $from,
                        'size' => $size,
                        'sort' => [],
                        'track_total_hits' => true,
                        'query' => [
                            'bool' => [
                                'must' => $must,
                                'filter' => $filter,
                            ],
                        ],
                    ],
                ];

                if (! $this->elasticService->indexExists(ELASTIC_JOBS)) {
                    $total = 0;
                    $categories = [];
                } else {
                    $response = $this->elasticService->getClient()->search($params);
                    $total = $response['hits']['total']['value'] ?? 0;
                    $categories = array_map(function ($hit) {
                        return $hit['_source'];
                    }, $response['hits']['hits']);
                }
                // dd($categories,$company_id);

                // $categories = DB::table('jobs')->select('jobs.id','jobs.title','jobs.total_positions','jobs.work_type','jobs.employment_type','jobs.priority','jobs.status','jobs.min_exp','jobs.max_exp','jobs.created_at','jobs.start_date','jobs.end_date','jobs.min_salary','jobs.max_salary','jobs.min_sal_mode','jobs.max_yearly','jobs.max_monthly','jobs.max_hourly','jobs.min_yearly','jobs.min_monthly','jobs.min_hourly','jobs.max_sal_mode','jobs.company_id as from_company_id','jobs.min_salary_currency','jobs.max_salary_currency','recommendation_relation.to_company_id','clients.name as clients_name','clients.logo_logical_path','recommendation_relation.id as recommendation_relation_id','recommendation_relation.final_status','companies.logo','companies.company_name')
                // ->join('clients', 'clients.id', 'jobs.client_id')
                // ->join('companies', 'companies.id', 'clients.company_id')
                // ->leftJoin('recommendation_relation', 'recommendation_relation.job_id', 'jobs.id')
                // ->leftJoin('job_metronics_skills', 'job_metronics_skills.job_id', '=', 'jobs.id')
                // ->leftJoin('client_location_jobs', 'client_location_jobs.job_id', '=', 'jobs.id')
                // ->leftJoin('rec_candidate_assign', 'rec_candidate_assign.source_jobid', '=', 'jobs.id')
                // ->leftJoin('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
                // ->where('jobs.id', '>', '0');

                // $categories->where(function ($query)use($existingJobQuery,$company_id) {
                //     $query->whereNotIn('jobs.id',$existingJobQuery)
                //     ->where('jobs.company_id','!=',$company_id)
                //     ->where('jobs.public_market_status','=',2)
                //     ->where('jobs.status','=','active');
                // });
                // $categories = $categories->groupBy('jobs.id');
                // $categories = $categories->orderBy('jobs.id', 'DESC');

                // $categories = $categories->get();

                $tracker_html = '';
                if (count($categories) > 0) {

                    $tracker_html = '<table align="left" border="1" style="border-collapse:collapse; border:solid 1px black; width:1272px">
                    <thead>
                    <tr style="mso-yfti-irow:0;mso-yfti-firstrow:yes;height:15.75pt"> 
                    <td valign="bottom" style="border:solid windowtext 1.0pt;border-left:none; background:#BDD7EE;padding:1.5pt 2.25pt 1.5pt 2.25pt;height:15.75pt; overflow:hidden"> <p class="MsoNormal"><b><u><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">S.no<o:p></o:p></span></u></b></p></td>
                    <td valign="bottom" style="border:solid windowtext 1.0pt;background:#BDD7EE; padding:1.5pt 2.25pt 1.5pt 2.25pt;height:15.75pt"> <p class="MsoNormal"><b><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">Jobs</span></b><b><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif;mso-fareast-language: EN-IN"><o:p></o:p></span></b></p></td>                    
                    <td valign="bottom" style="border:solid windowtext 1.0pt;background:#BDD7EE; padding:1.5pt 2.25pt 1.5pt 2.25pt;height:15.75pt"> <p class="MsoNormal"><b><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">Status</span></b><b><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif;mso-fareast-language: EN-IN"><o:p></o:p></span></b></p></td>                
                    <td valign="bottom" style="border:solid windowtext 1.0pt;border-left:none; background:#BDD7EE;padding:1.5pt 2.25pt 1.5pt 2.25pt;height:15.75pt; overflow:hidden"> <p class="MsoNormal"><b><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">Exp<o:p></o:p></span></b></p></td>
                    <td valign="bottom" style="border:solid windowtext 1.0pt;border-left:none; background:#BDD7EE;padding:1.5pt 2.25pt 1.5pt 2.25pt;height:15.75pt; overflow:hidden"> <p class="MsoNormal"><b><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">Salary<o:p></o:p></span></b></p></td>
                    <td valign="bottom" style="border:solid windowtext 1.0pt;border-left:none; background:#BDD7EE;padding:1.5pt 2.25pt 1.5pt 2.25pt;height:15.75pt; overflow:hidden"> <p class="MsoNormal"><b><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">Location<o:p></o:p></span></b></p></td>

                    
                    <td valign="bottom" style="border:solid windowtext 1.0pt;border-left:none; background:#BDD7EE;padding:1.5pt 2.25pt 1.5pt 2.25pt;height:15.75pt; overflow:hidden"> <p class="MsoNormal"><b><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">Date<o:p></o:p></span></b></p></td>

                    <td valign="bottom" style="border:solid windowtext 1.0pt;border-left:none; background:#BDD7EE;padding:1.5pt 2.25pt 1.5pt 2.25pt;height:15.75pt; overflow:hidden"> <p class="MsoNormal"><b><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">Action<o:p></o:p></span></b></p></td>
                    </tr>
                    </thead>';

                    $cnt = 1;

                    foreach ($categories as $rowArrj) {
                        $row = (object) $rowArrj;
                        $currencyText = GeneralMethod::currency();
                        $status = $active = $hold = $inactive = $class = $recommendation_id = '';
                        $status = ucwords($row->status);
                        $class = $job_status[$row->status];
                        $recommendation_id = 0;
                        if ($status == 'Active') {
                            $status = 'Hiring';
                        }
                        if ($status == 'Inactive') {
                            $status = 'Closed';
                        }
                        $final_status = '';
                        // if (
                        //     ($row->final_status == 2 || $row->final_status == 3) &&
                        //     @$row->to_company_id == auth()->user()->company_id
                        // ) {
                        //     $status = 'Removed';
                        //     $class = 'closed-bg';
                        // }

                        $recommendation_text = '';
                        // if (@$row->to_company_id != auth()->user()->company_id) {
                        //     $recommendation_text = 'Client';
                        // }

                        $min_sal_mode = $max_sal_mode = $max_salary_currency = $max_salary = $min_salary = '';
                        $sal_mode = ($row->min_sal_mode) ? $row->min_sal_mode : 0;
                        $min_salary_currency = ($row->min_salary_currency) ? $row->min_salary_currency : 0;
                        $min_sal_mode = $salaryMode[$sal_mode];
                        $max_sal_mode = $salaryMode[$sal_mode];
                        $salary_currency = $currencyText[@$min_salary_currency];

                        $cur = $min_salary_currency;
                        $mod = $sal_mode;
                        $currencymain = GeneralMethod::currency(); // Currency code
                        $currency = $currencymain[$cur];
                        $convertedAmounEctc = GeneralMethod::conversionRates($currency, $row->max_yearly, 0);
                        $convertedAmounCtc = GeneralMethod::conversionRates($currency, $row->min_yearly, 0);

                        $ectc = $convertedAmounEctc[$mod];
                        $ctc = $convertedAmounCtc[$mod];

                        $max_salary = '';
                        $max_salary = GeneralMethod::thousandsCurrencyFormat($ectc);
                        $min_salary = '';
                        $min_salary = GeneralMethod::thousandsCurrencyFormat($ctc);

                        $jobid = $row->id;

                        $employment_type = 'Full-time';
                        if ($row->employment_type == 1) {
                            $employment_type = 'Contract';
                        }
                        $client_loc = DB::table('client_location_jobs')
                            ->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
                            ->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')
                            ->select(
                                'client_location.address',
                                'client_location.city',
                                'client_location.postcode',
                                'client_location.country_id',
                                'city_list.city as city_name',
                            )
                            ->where('client_location_jobs.job_id', $jobid)
                            ->first();
                        $jobCitySelected = DB::table('client_city_jobs')->where('client_city_jobs.job_id', $jobid)->count();
                        $city = mb_substr(@$client_loc->city_name, 0, 6);
                        if ($row->work_type == 'yes') {
                            $city = 'Remote';
                        } elseif ($row->work_type == 'no') {
                            if ($jobCitySelected == 1 || $jobCitySelected == 0) {
                                $city = mb_substr(@$client_loc->city_name, 0, 21);
                            } else {
                                $city = 'Multiple';
                            }
                        } elseif ($row->work_type == 'any') {
                            if ($jobCitySelected == 1 || $jobCitySelected == 0) {
                                $city = mb_substr(@$client_loc->city_name, 0, 21);
                            } else {
                                $city = 'Multiple';
                            }
                        }

                        $total_positions = '';
                        $total_positions = number_format($row->total_positions);

                        $created_at = '';
                        $created_at = $row->created_at;
                        $newDate = GeneralMethod::ymdTodmyNotTime($created_at); // date("d M, Y", strtotime($created_at));

                        $existing_path = url('/service_company/jobs/market-jobs/0');

                        $tracker_html .= '
                                    <tr style="mso-yfti-irow:1;mso-yfti-lastrow:yes;height:15.75pt"> 
                                    <td valign="bottom" style="border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt; border-right:solid windowtext 1.0pt;background:white;padding:1.5pt 2.25pt 1.5pt 2.25pt; height:15.75pt;overflow:hidden"> <p class="MsoNormal"><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">'.$cnt.'<o:p></o:p></span></p></td>
                                    <td valign="bottom" style="border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt; border-right:solid windowtext 1.0pt;background:white;padding:1.5pt 2.25pt 1.5pt 2.25pt; height:15.75pt;overflow:hidden"> <p class="MsoNormal"><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">'.ucwords(mb_substr(@$row->title, 0, 30)).'<br><span>-------</span><o:p></o:p></span></p></td>

                                    <td valign="bottom" style="border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt; border-right:solid windowtext 1.0pt;background:white;padding:1.5pt 2.25pt 1.5pt 2.25pt; height:15.75pt;overflow:hidden"> <p class="MsoNormal"><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif"<span>'.$status.'<br>'.@$row->id.'-'.$recommendation_text.' </span><o:p></o:p></span></p></td>

                                    <td valign="bottom" style="border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt; border-right:solid windowtext 1.0pt;background:white;padding:1.5pt 2.25pt 1.5pt 2.25pt; height:15.75pt;overflow:hidden"> <p class="MsoNormal"><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">'.((@$row->max_exp) ? @$row->max_exp.'Y' : '').' <br>
                                    '.((@$row->min_exp) ? @$row->min_exp.' Y' : '').' <o:p></o:p></span></p></td>

                                    <td valign="bottom" style="border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt; border-right:solid windowtext 1.0pt;background:white;padding:1.5pt 2.25pt 1.5pt 2.25pt; height:15.75pt;overflow:hidden"> <p class="MsoNormal"><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">'.$salary_currency.' '.$max_salary.'/'.$min_sal_mode.'<br><span>'.$salary_currency.' '.$min_salary.'/'.$max_sal_mode.' </span><o:p></o:p></span></p></td>
    
                                    <td valign="bottom" style="border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt; border-right:solid windowtext 1.0pt;background:white;padding:1.5pt 2.25pt 1.5pt 2.25pt; height:15.75pt;overflow:hidden"> <p class="MsoNormal"><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">'.$city.'<br>'.ucwords(@$employment_type).'<o:p></o:p></span></p></td>

                                    <td valign="bottom" style="border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt; border-right:solid windowtext 1.0pt;background:white;padding:1.5pt 2.25pt 1.5pt 2.25pt; height:15.75pt;overflow:hidden"> <p class="MsoNormal"><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">'.$newDate.'<br>'.$total_positions.' Opening<o:p></o:p></span></p></td>


                                    <td valign="bottom" style="border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt; border-right:solid windowtext 1.0pt;background:white;padding:1.5pt 2.25pt 1.5pt 2.25pt; height:15.75pt;overflow:hidden"> <p class="MsoNormal"><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif"><b>Apply As Agency</b><br><a href="'.$existing_path.'">Click Here</a><o:p></o:p></span></p></td>				
    
                                    </tr>';

                        $cnt = $cnt + 1;
                    }

                    $tracker_html .= '<table>';

                    $basecompany = GeneralMethod::baseCompany();
                    $senderName = ($basecompany->companyName) ? $basecompany->companyName : '';
                    $fromAddress = ($basecompany->companyEmail) ? $basecompany->companyEmail : '';

                    $cc = [];
                    $attachments = [];
                    if (count($categories) > 0) {
                        $existing_path = url('/service_company/jobs/market-jobs/0');
                        $checkMailPermision = GeneralMethod::mailPermission(39);
                        $email_notifications = DB::table('email_notifications')->where('id', 39)->where('status', 1)->first();

                        $subject = $email_notifications->subject;

                        $tracker_html = str_replace(['[TRACKER]'], [$tracker_html], @$email_notifications->body_description);

                        $click_here = '<a href="'.$existing_path.'">Click Here </a>';
                        $tracker_html = str_replace(['[{JOB_LINK}]', '[{CLICK_HERE}]'], [$existing_path, $click_here], $tracker_html);

                        $data = ['message' => $tracker_html, 'subject' => $subject, 'company_name' => '4Bell Technology', 'consultant_name' => '4Bell Technology', 'position' => 'PHP'];
                        $notificationemail = GeneralMethod::notification($company_id, 'Sales');
                        $defailsEmail = DB::table('companies')->where('id', $company_id)->first()->company_email;
                        $email = $notificationemail->email ?? $defailsEmail ?? '';
                        $viewfile = 'email.common.index';

                        if ($checkMailPermision) {
                            QueueNotificationMailService::dispatch(
                                $email,
                                $subject,
                                $viewfile,
                                $data,
                                $attachments,
                                $fromAddress,
                                $senderName,
                                $cc,
                                $company_id
                            )->onConnection('emails');

                        }

                    }
                }

            }

        }

    }
    /** End Market Jobs  Match */

    /** Start Recommended Jobs Match */
    public function recommenedJobs(Request $request)
    {
        $job_status = GeneralMethod::job_status();
        $salaryMode = GeneralMethod::salaryMode();
        $currencySymbole = GeneralMethod::currencySymbole();
        $currencyText = GeneralMethod::currency();
        $companies = DB::table('companies')
            ->join('users', 'users.company_id', '=', 'companies.id')
            ->where('users.root_user', '=', 1)
            ->select('companies.*')->get();

        // dd( $companies);
        if (count($companies) > 0) {

            foreach ($companies as $val) {
                $company_id = $val->id;
                $benchApplications = DB::table('job_applications')
                    ->select(
                        'job_applications.id',
                        'job_applications.city_id',
                        'job_applications.full_name',
                        'job_applications.current_designation',
                        'job_applications.outsourcing_type',
                        'job_applications.phone',
                        'job_applications.secondary_mobile',
                        'job_applications.dob',
                        'job_applications.email',
                        'job_applications.secondrary_email',
                        'job_applications.qualification',
                        'job_applications.total_exp',
                        'job_applications.rel_exp',
                        'job_applications.serving_notice_period_mode',
                        'job_applications.np_id',
                        'job_applications.pay_rate_currency',
                        'job_applications.pay_rate_amount',
                        'job_applications.pay_rate_mode',
                        'job_applications.bill_rate_currency',
                        'job_applications.bill_rate_amount',
                        'job_applications.bill_rate_mode',
                        'job_applications.comission_type',
                        'job_applications.country',
                        'job_applications.state',
                        'job_applications.city',
                        'job_applications.pincode',
                        'job_applications.skills',
                        'job_applications.photo',
                        'job_applications.resume',
                        'job_applications.status',
                        'job_applications.employee_number',
                        'job_applications.current_company',
                        'job_applications.created_at',
                        'job_applications.superadminstatus',
                        'qualification_master.name as qualification_name',
                        'bench_master_status.name as master_status_name',
                        'bench_master_status.css_style',

                        DB::raw('COUNT(add_job_wise_condidates.id) as allCandidate'),
                        DB::raw('SUM(CASE WHEN add_job_wise_condidates.master_status_id = 1 THEN 1 ELSE 0 END) AS Sourcing'),
                        DB::raw('SUM(CASE WHEN add_job_wise_condidates.master_status_id = 2 THEN 1 ELSE 0 END) AS Screening'),
                        DB::raw('SUM(CASE WHEN add_job_wise_condidates.master_status_id = 3 THEN 1 ELSE 0 END) AS Interview'),
                        DB::raw('SUM(CASE WHEN add_job_wise_condidates.master_status_id = 4 THEN 1 ELSE 0 END) AS Negotiation'),
                        DB::raw('SUM(CASE WHEN add_job_wise_condidates.master_status_id = 5 THEN 1 ELSE 0 END) AS Offered'),
                        DB::raw('SUM(CASE WHEN add_job_wise_condidates.master_status_id = 6 THEN 1 ELSE 0 END) AS Joined'),
                        DB::raw('SUM(CASE WHEN add_job_wise_condidates.master_status_id = 7 THEN 1 ELSE 0 END) AS Rejected'),
                        DB::raw('SUM(CASE WHEN add_job_wise_condidates.master_status_id = 8 THEN 1 ELSE 0 END) AS InReview')

                    );
                $benchApplications = $benchApplications->leftjoin('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id');
                $benchApplications = $benchApplications->join('bench_master_status', 'bench_master_status.id', 'job_applications.status');
                $benchApplications = $benchApplications->leftjoin('qualification_master', 'qualification_master.id', 'job_applications.qualification');
                $benchApplications = $benchApplications->leftjoin('rec_candidate_assign', function ($q) use ($company_id) {
                    $q->on('rec_candidate_assign.id', '=', 'add_job_wise_condidates.rec_candidate_assign_id');
                    $q->on('rec_candidate_assign.source_from_id', '=', 'job_applications.id');
                    $q->on('job_applications.id', '=', 'rec_candidate_assign.source_application_id');
                    $q->where('rec_candidate_assign.source_from_company_id', '=', $company_id);
                });
                $statusbench = 2;
                $benchApplications = $benchApplications->where(function ($query) use ($statusbench) {
                    $query->where('job_applications.status', '=', $statusbench);
                });
                $benchApplications = $benchApplications->where('job_applications.company_id', '=', auth()->user()->company_id);
                $benchApplications = $benchApplications->groupBy('job_applications.id');
                $benchApplications = $benchApplications->get();

                if (count($benchApplications) > 0) {

                    foreach ($benchApplications as $bench_val) {
                        $bench_id = $bench_val->id;

                        $gdFilterData = GeneralMethod::canGgdFilterData($bench_id);
                        $min_exp = ($gdFilterData->min_exp >= 1) ? $gdFilterData->min_exp - 1 : $gdFilterData->min_exp;
                        $max_exp = $gdFilterData->max_exp + 1;

                        $sal_mode = ($gdFilterData->min_sal_mode) ? $gdFilterData->min_sal_mode : 0;
                        $min_salary_currency = ($gdFilterData->min_salary_currency) ? $gdFilterData->min_salary_currency : 0;

                        $min_salary = ($gdFilterData->min_salary) ? $gdFilterData->min_salary : 0;
                        $max_salary = ($gdFilterData->max_salary) ? $gdFilterData->max_salary : 0;
                        $currency = GeneralMethod::currency();  // Currency code

                        $ecurrency = $currency[$min_salary_currency];
                        $econvertedAmount = (int) $max_salary;
                        if ($min_salary_currency > 0) {
                            $econvertedAmount = GeneralMethod::convertToINR($econvertedAmount, $ecurrency);
                        }

                        $ectcdivided = GeneralMethod::salaryDividedByMode($sal_mode, $econvertedAmount);

                        $currency = $currency[$min_salary_currency];
                        $convertedAmount = (int) $min_salary;
                        if ($min_salary_currency > 0) {
                            $convertedAmount = GeneralMethod::convertToINR($convertedAmount, $currency);
                        }

                        $ctcdivided = GeneralMethod::salaryDividedByMode($sal_mode, $convertedAmount);
                        if ($sal_mode == 2) {
                            $max_salary = @$ectcdivided->hourly;
                            $min_salary = @$ctcdivided->hourly;
                        } elseif ($sal_mode == 1) {
                            $max_salary = @$ectcdivided->monthly;
                            $min_salary = @$ctcdivided->monthly;
                        } elseif ($sal_mode == 0) {
                            $max_salary = @$ectcdivided->yearly;
                            $min_salary = @$ctcdivided->yearly;
                        }

                        $location = DB::table('city_list')
                            ->where('city_list.city', $gdFilterData->default_loc)
                            ->pluck('city_list.city_id')
                            ->toArray();

                        $requiredSkills = explode(',', $gdFilterData->default_skill);

                        $subQuery = DB::table('jobs')
                            ->leftJoin('job_metronics_skills', 'jobs.id', '=', 'job_metronics_skills.job_id')
                            ->whereIn('job_metronics_skills.skill_name', $requiredSkills)
                            ->where('jobs.company_id', '!=', $company_id)
                            ->orWhereIn('jobs.title', $requiredSkills) // Replace with actual current designation condition
                            ->pluck('jobs.id')->toArray();
                        // dd($subQuery->get());
                        $existingCandidatesQuery = DB::table('add_job_wise_condidates')
                            ->where('job_application_id', '=', $request->condidate_id)
                            ->pluck('add_job_wise_condidates.job_id')->toArray();

                        $result_job = array_diff($subQuery, $existingCandidatesQuery);

                        // dd($result_job);

                        $alldatalist = DB::table('jobs')
                            ->select(
                                'jobs.id',
                                'jobs.title',
                                'jobs.work_type',
                                'jobs.show_vendor',
                                'jobs.total_positions',
                                'jobs.employment_type',
                                'jobs.priority',
                                'jobs.status',
                                'jobs.min_exp',
                                'jobs.max_exp',
                                'jobs.created_at',
                                'jobs.start_date',
                                'jobs.end_date',
                                'jobs.min_salary',
                                'jobs.max_salary',
                                'jobs.min_sal_mode',
                                'jobs.max_yearly',
                                'jobs.max_monthly',
                                'jobs.max_hourly',
                                'jobs.min_yearly',
                                'jobs.min_monthly',
                                'jobs.min_hourly',
                                'jobs.max_sal_mode',
                                'jobs.min_salary_currency',
                                'jobs.max_salary_currency',
                                'clients.name as clients_name',
                                'clients.logo_logical_path',
                                'clients.self_company',
                                'clients.company_id as client_company_id',
                                'companies.company_name',
                                'companies.logo',
                                'recommendation_relation.id as recommendation_id',
                                'recommendation_relation.from_company_id',
                                'recommendation_relation.final_status',
                                'recommendation_relation.to_company_id'
                            )
                            ->join('clients', 'clients.id', 'jobs.client_id')
                            ->join('companies', 'companies.id', 'jobs.company_id')
                            ->leftJoin('recommendation_relation', 'recommendation_relation.job_id', '=', 'jobs.id')
                            ->leftJoin('job_metronics_skills', 'job_metronics_skills.job_id', '=', 'jobs.id')
                            ->leftJoin('client_location_jobs', 'client_location_jobs.job_id', '=', 'jobs.id')
                            ->leftJoin('rec_candidate_assign', 'rec_candidate_assign.source_jobid', '=', 'jobs.id')
                            ->leftJoin('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id');
                        $alldatalist = $alldatalist->whereIn('jobs.id', $result_job);
                        $alldatalist = $alldatalist->where('jobs.company_id', '!=', $company_id);
                        $alldatalist = $alldatalist->where(function ($qry) use ($location, $min_exp, $max_exp, $sal_mode, $min_salary, $max_salary) {

                            $qry->whereIn('client_location.city_id', $location);
                            $qry->where('jobs.min_exp', '>=', $min_exp);
                            $qry->where('jobs.max_exp', '<=', $max_exp);

                            if ($sal_mode == 2) {
                                $qry->whereBetween('jobs.max_hourly', [$min_salary, $max_salary]);
                            } elseif ($sal_mode == 1) {
                                $qry->whereBetween('jobs.max_monthly', [$min_salary, $max_salary]);
                            } elseif ($sal_mode == 0) {
                                $qry->whereBetween('jobs.max_yearly', [$min_salary, $max_salary]);
                            }

                        });

                        $custom_status = 'active';
                        $alldatalist = $alldatalist->where('jobs.status', $custom_status);

                        $alldatalist = $alldatalist->distinct('job_applications.id');
                        $alldatalist = $alldatalist->groupBy('jobs.id');
                        $alldatalist = $alldatalist->orderBy('jobs.id', 'DESC');
                        $alldatalist = $alldatalist->get();

                        $tracker_html = '';

                        if (count($alldatalist) > 0) {

                            $tracker_html = '<table align="left" border="1" style="border-collapse:collapse; border:solid 1px black; width:1272px">
                                <thead>
                                <tr style="mso-yfti-irow:0;mso-yfti-firstrow:yes;height:15.75pt"> 
                                <td valign="bottom" style="border:solid windowtext 1.0pt;border-left:none; background:#BDD7EE;padding:1.5pt 2.25pt 1.5pt 2.25pt;height:15.75pt; overflow:hidden"> <p class="MsoNormal"><b><u><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">S.no<o:p></o:p></span></u></b></p></td>
                                <td valign="bottom" style="border:solid windowtext 1.0pt;background:#BDD7EE; padding:1.5pt 2.25pt 1.5pt 2.25pt;height:15.75pt"> <p class="MsoNormal"><b><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">Jobs</span></b><b><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif;mso-fareast-language: EN-IN"><o:p></o:p></span></b></p></td>                    
                                <td valign="bottom" style="border:solid windowtext 1.0pt;background:#BDD7EE; padding:1.5pt 2.25pt 1.5pt 2.25pt;height:15.75pt"> <p class="MsoNormal"><b><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">Status</span></b><b><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif;mso-fareast-language: EN-IN"><o:p></o:p></span></b></p></td>                
                                <td valign="bottom" style="border:solid windowtext 1.0pt;border-left:none; background:#BDD7EE;padding:1.5pt 2.25pt 1.5pt 2.25pt;height:15.75pt; overflow:hidden"> <p class="MsoNormal"><b><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">Exp<o:p></o:p></span></b></p></td>
                                <td valign="bottom" style="border:solid windowtext 1.0pt;border-left:none; background:#BDD7EE;padding:1.5pt 2.25pt 1.5pt 2.25pt;height:15.75pt; overflow:hidden"> <p class="MsoNormal"><b><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">Salary<o:p></o:p></span></b></p></td>
                                <td valign="bottom" style="border:solid windowtext 1.0pt;border-left:none; background:#BDD7EE;padding:1.5pt 2.25pt 1.5pt 2.25pt;height:15.75pt; overflow:hidden"> <p class="MsoNormal"><b><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">Location<o:p></o:p></span></b></p></td>

                                
                                <td valign="bottom" style="border:solid windowtext 1.0pt;border-left:none; background:#BDD7EE;padding:1.5pt 2.25pt 1.5pt 2.25pt;height:15.75pt; overflow:hidden"> <p class="MsoNormal"><b><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">Date<o:p></o:p></span></b></p></td>

                                <td valign="bottom" style="border:solid windowtext 1.0pt;border-left:none; background:#BDD7EE;padding:1.5pt 2.25pt 1.5pt 2.25pt;height:15.75pt; overflow:hidden"> <p class="MsoNormal"><b><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">Action<o:p></o:p></span></b></p></td>
                                </tr>
                                </thead>';

                            $cnt = 1;

                            foreach ($alldatalist as $row) {
                                $currencyText = GeneralMethod::currency();
                                $jobid = $row->id;
                                $existing_path = url('/service_company/bench-candidates/recommend_req/'.$bench_id.'');
                                $clients_name = ucwords(mb_substr(@$row->clients_name, 0, 15));
                                $recommendation_id = $row->recommendation_id;
                                if ($recommendation_id > 0 && @$row->to_company_id == $company_id) {
                                    $clients_name = ucwords(mb_substr(@$row->company_name, 0, 15));
                                }

                                $min_sal_mode = $max_sal_mode = $max_salary_currency = $max_salary = $min_salary = '';
                                $sal_mode = ($row->max_sal_mode) ? $row->max_sal_mode : 0;
                                $min_salary_currency = ($row->min_salary_currency) ? $row->min_salary_currency : 0;
                                $min_sal_mode = strtoupper(mb_substr($salaryMode[$sal_mode], 0, 1));
                                $max_sal_mode = strtoupper(mb_substr($salaryMode[$sal_mode], 0, 1));
                                $salary_currency = $currencyText[@$min_salary_currency];

                                $cur = $min_salary_currency;
                                $mod = $sal_mode;
                                $currencymain = GeneralMethod::currency(); // Currency code
                                $currency = $currencymain[$cur];
                                $convertedAmounEctc = GeneralMethod::conversionRates($currency, $row->max_yearly, 0);
                                $convertedAmounCtc = GeneralMethod::conversionRates($currency, $row->min_yearly, 0);

                                $ectc = $convertedAmounEctc[$mod];
                                $ctc = $convertedAmounCtc[$mod];

                                $max_salary = GeneralMethod::thousandsCurrencyFormat($ectc);
                                $min_salary = GeneralMethod::thousandsCurrencyFormat($ctc);

                                $client_loc = DB::table('client_location_jobs')
                                    ->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
                                    ->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')
                                    ->select(
                                        'client_location.address',
                                        'client_location.city',
                                        'client_location.postcode',
                                        'client_location.country_id',
                                        'city_list.city as city_name',
                                    )
                                    ->where('client_location_jobs.job_id', $jobid)
                                    ->first();

                                $jobCitySelected = DB::table('client_city_jobs')->where('client_city_jobs.job_id', $jobid)->count();
                                $city = mb_substr(@$client_loc->city_name, 0, 6);
                                if ($row->work_type == 'yes') {
                                    $city = 'Remote';
                                } elseif ($row->work_type == 'no') {
                                    if ($jobCitySelected == 1 || $jobCitySelected == 0) {
                                        $city = mb_substr(@$client_loc->city_name, 0, 21);
                                    } else {
                                        $city = 'Multiple';
                                    }
                                } elseif ($row->work_type == 'any') {
                                    if ($jobCitySelected == 1 || $jobCitySelected == 0) {
                                        $city = mb_substr(@$client_loc->city_name, 0, 21);
                                    } else {
                                        $city = 'Multiple';
                                    }
                                }
                                $employment_type = 'Full-time';
                                if ($row->employment_type == 1) {
                                    $employment_type = 'Contract';
                                }
                                $status = $active = $hold = $inactive = $class = $recommendation_id = '';
                                $status = ucwords($row->status);
                                $class = $job_status[$row->status];
                                $recommendation_id = $row->recommendation_id;

                                if ($status == 'Active') {
                                    $status = 'Hiring';
                                }

                                if ($status == 'Inactive') {
                                    $status = 'Closed';
                                }
                                $final_status = '';
                                if (
                                    ($row->final_status == 2 || $row->final_status == 3) &&
                                    @$row->to_company_id == $company_id
                                ) {
                                    $status = 'Removed';
                                    $class = 'closed-bg';
                                }
                                $recommendation_text = 'Custom';
                                if (@$row->to_company_id != $company_id) {
                                    $recommendation_text = 'Client';
                                }

                                $total_positions = '';
                                $total_positions = number_format($row->total_positions);

                                $created_at = '';
                                $created_at = $row->created_at;
                                $newDate = GeneralMethod::ymdTodmyNotTime($created_at); // date("d M, Y", strtotime($created_at));

                                $tracker_html .= '
                                                <tr style="mso-yfti-irow:1;mso-yfti-lastrow:yes;height:15.75pt"> 
                                                <td valign="bottom" style="border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt; border-right:solid windowtext 1.0pt;background:white;padding:1.5pt 2.25pt 1.5pt 2.25pt; height:15.75pt;overflow:hidden"> <p class="MsoNormal"><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">'.$cnt.'<o:p></o:p></span></p></td>
                                                <td valign="bottom" style="border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt; border-right:solid windowtext 1.0pt;background:white;padding:1.5pt 2.25pt 1.5pt 2.25pt; height:15.75pt;overflow:hidden"> <p class="MsoNormal"><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">'.ucwords(mb_substr(@$row->title, 0, 30)).'<br><span>'.ucwords(mb_substr($row->company_name, 0, 30)).'</span><o:p></o:p></span></p></td>

                                                <td valign="bottom" style="border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt; border-right:solid windowtext 1.0pt;background:white;padding:1.5pt 2.25pt 1.5pt 2.25pt; height:15.75pt;overflow:hidden"> <p class="MsoNormal"><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif"<span>'.((@$row->max_exp) ? @$row->max_exp.' Y' : '').'<br>'.((@$row->min_exp) ? @$row->min_exp.' Y' : '').' </span><o:p></o:p></span></p></td>

                                                <td valign="bottom" style="border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt; border-right:solid windowtext 1.0pt;background:white;padding:1.5pt 2.25pt 1.5pt 2.25pt; height:15.75pt;overflow:hidden"> <p class="MsoNormal"><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">'.$salary_currency.' '.$max_salary.'/'.$min_sal_mode.'<br><span>'.$salary_currency.' '.$min_salary.'/'.$max_sal_mode.' </span><o:p></o:p></span></p></td>

                                                <td valign="bottom" style="border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt; border-right:solid windowtext 1.0pt;background:white;padding:1.5pt 2.25pt 1.5pt 2.25pt; height:15.75pt;overflow:hidden"> <p class="MsoNormal"><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">'.$city.'<br>'.ucwords(@$employment_type).'<o:p></o:p></span></p></td>

                                                <td valign="bottom" style="border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt; border-right:solid windowtext 1.0pt;background:white;padding:1.5pt 2.25pt 1.5pt 2.25pt; height:15.75pt;overflow:hidden"> <p class="MsoNormal"><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif"<span>'.$status.'<br>'.$recommendation_text.' </span><o:p></o:p></span></p></td>


                                                <td valign="bottom" style="border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt; border-right:solid windowtext 1.0pt;background:white;padding:1.5pt 2.25pt 1.5pt 2.25pt; height:15.75pt;overflow:hidden"> <p class="MsoNormal"><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">'.$newDate.'<br>'.$total_positions.'<o:p></o:p></span></p></td>
                                                


                                                <td valign="bottom" style="border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt; border-right:solid windowtext 1.0pt;background:white;padding:1.5pt 2.25pt 1.5pt 2.25pt; height:15.75pt;overflow:hidden"> <p class="MsoNormal"><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif"><a href="'.$existing_path.'">View</a><o:p></o:p></span></p></td>				

                                                </tr>';
                                $cnt = $cnt + 1;

                            }

                            $tracker_html .= '<table>';

                        }

                        $subject = 'Recommend Jobs';
                        $basecompany = GeneralMethod::baseCompany();
                        $senderName = ($basecompany->companyName) ? $basecompany->companyName : '';
                        $fromAddress = ($basecompany->companyEmail) ? $basecompany->companyEmail : '';

                        $cc = [];
                        $attachments = [];
                        if (count($alldatalist) > 0) {
                            $checkMailPermision = GeneralMethod::mailPermission(38);
                            $email_notifications = DB::table('email_notifications')->where('id', 38)->where('status', 1)->first();
                            $subject = str_replace(['[BENCH_CANDIDATE_NAME]', '[BENCH_ID]', '[BENCH_TITLE]'], [@$bench_val->full_name, @$bench_val->id, @$bench_val->current_designation], @$email_notifications->subject);

                            $tracker_html = str_replace(['[BENCH_CANDIDATE_NAME]', '[BENCH_ID]', '[TRACKER]'], [@$bench_val->full_name, @$bench_val->id, $tracker_html], @$email_notifications->body_description);

                            $data = ['message' => $tracker_html, 'subject' => $subject, 'company_name' => '4Bell Technology', 'consultant_name' => '4Bell Technology', 'position' => 'PHP'];
                            $email = $val->company_email;
                            $viewfile = 'email.common.index';

                            if ($checkMailPermision) {
                                QueueNotificationMailService::dispatch(
                                    $email,
                                    $subject,
                                    $viewfile,
                                    $data,
                                    $attachments,
                                    $fromAddress,
                                    $senderName,
                                    $cc,
                                    $company_id
                                )->onConnection('emails');
                            }

                        }

                    }
                }

            }
        }

    }
    /** End Recommended Match */

    /** Start Partner Recommened Match */
    public function partnerRecommened(Request $request)
    {

        $companies = DB::table('companies')
            ->join('users', 'users.company_id', '=', 'companies.id')
            ->whereIn('companies.company_type', [1, 2])
            ->where('users.root_user', '=', 1)
            ->select('companies.*')->get();

        if (count($companies) > 0) {

            foreach ($companies as $val) {

                $company_id = $val->id;
                $allActiveJobs = DB::table('jobs')->select(
                    'jobs.id',
                    'jobs.title',
                    'jobs.work_type',
                    'jobs.employment_type',
                    'jobs.total_positions',
                    'jobs.priority',
                    'jobs.status',
                    'jobs.min_exp',
                    'jobs.max_exp',
                    'jobs.created_at',
                    'jobs.start_date',
                    'jobs.end_date',
                    'jobs.min_salary',
                    'jobs.max_salary',
                    'jobs.min_sal_mode',
                    'jobs.max_sal_mode',
                    'jobs.company_id as companyidcount',
                    'jobs.min_salary_currency',
                    'jobs.max_salary_currency',
                    'clients.name as clients_name',
                    'clients.logo_logical_path',
                    'clients.self_company',
                    'companies.company_name')
                    ->join('clients', 'clients.id', 'jobs.client_id')
                    ->join('companies', 'companies.id', 'jobs.company_id')
                    ->leftJoin('add_job_wise_condidates as job_applications', 'job_applications.job_id', '=', 'jobs.id')
                    ->leftJoin('job_metronics_skills', 'job_metronics_skills.job_id', 'jobs.id')
                    ->leftJoin('job_assign', function ($join) use ($company_id) {
                        $join->on('job_assign.job_id', '=', 'jobs.id');
                        $join->on('job_assign.company_id', '=', DB::raw("'".$company_id."'"));

                    })
                    ->where('jobs.id', '>', '0');
                $allActiveJobs = $allActiveJobs->where('jobs.company_id', '=', $company_id);
                $allActiveJobs = $allActiveJobs->where('jobs.status', '=', 'active');
                // dd("djd");

                $allActiveJobs = $allActiveJobs->distinct('job_applications.id');
                $allActiveJobs = $allActiveJobs->groupBy('jobs.id');

                $allActiveJobs = $allActiveJobs->get();
                if (count($allActiveJobs) > 0) {

                    foreach ($allActiveJobs as $job_val) {

                        $jobId = $job_val->id;
                        $employmentType = GeneralMethod::employmentType();
                        $currentRowJob = Job::select(
                            'title',
                            'min_exp',
                            'max_exp',
                            'min_salary',
                            'max_salary',
                            'min_salary_currency',
                            'max_salary_currency',
                            'min_sal_mode',
                            'max_sal_mode',
                            'employment_type')
                            ->where('id', $jobId)
                            ->first();
                        $parent_employment_type = $currentRowJob->employment_type;

                        $currentJobSkill = DB::table('job_metronics_skills')
                            ->where('job_metronics_skills.job_id', $jobId)
                            ->pluck('job_metronics_skills.skill_name')
                            ->toArray();
                        $jobTitle = $currentRowJob->title;
                        array_push($currentJobSkill, $jobTitle);

                        $skillCaseStatements = '';
                        foreach ($currentJobSkill as $skill) {
                            $skillCaseStatements .= "CASE WHEN job_metronics_skills.skill_name LIKE '%{$skill}%' THEN 20 ELSE 0 END + ";
                            $skillCaseStatements .= "CASE WHEN jobs.title LIKE '%{$skill}%' THEN 20 ELSE 0 END + ";
                        }
                        // Remove the trailing ' + '
                        $skillCaseStatements = rtrim($skillCaseStatements, ' + ');
                        $totalSkills = count($currentJobSkill);

                        $job_country_id = DB::table('client_city_jobs')
                            ->leftJoin('city_list', 'city_list.city_id', '=', 'client_city_jobs.city_id')
                            ->leftJoin('state_list', 'state_list.id', '=', 'city_list.state_id')
                            ->leftJoin('countries', 'countries.id', '=', 'state_list.country_id')
                            ->select('countries.id')
                            ->where('client_city_jobs.job_id', '=', $jobId)
                            ->first();
                        $country_id_job = 101;
                        if ($job_country_id) {
                            $country_id_job = $job_country_id->id;
                        }

                        $boostkeywordcheck = '';
                        foreach ($currentJobSkill as $skill) {
                            $boostkeywordcheck .= "CASE WHEN package_active.boostkeywords_text LIKE '%{$skill}%' THEN 1 ELSE 0 END + ";
                        }
                        // Remove the trailing ' + '
                        $boostkeywordcheck = rtrim($boostkeywordcheck, ' + ');

                        $skillCaseStatements = implode(' + ', array_map(function ($skill) {
                            return "CASE WHEN jobs.title LIKE '%".$skill."%' OR job_metronics_skills.skill_name LIKE '%".$skill."%' THEN 10 ELSE 0 END";
                        }, $currentJobSkill));
                        $skillCaseStatements = rtrim($skillCaseStatements, ' + ');
                        // dd($skillCaseStatements);
                        $skillsCount = count($currentJobSkill);

                        $clientMainLocation = GeneralMethod::companyMainAddress($company_id);

                        $jobMinExp = $currentRowJob->min_exp;
                        $jobMaxExp = $currentRowJob->max_exp;
                        $jobMinSalary = $currentRowJob->min_salary;
                        $jobMaxSalary = $currentRowJob->max_salary;
                        $jobSalaryCurrency = $currentRowJob->max_salary_currency;
                        $jobSalaryMode = $currentRowJob->max_sal_mode;

                        $randdonRecom = 20;
                        $remainRecom = 20;

                        $recommendedDataList = DB::table('companies')
                            ->select(
                                'companies.id',
                                'companies.company_name',
                                'companies.logo',
                                'companies.industries',
                                'companies.success_per',
                                'jobs.title',
                                DB::raw('count(DISTINCT job_applications.job_id) as SimilarReq'),
                                DB::raw('count(DISTINCT job_applications.id) as Profiles'),
                                'jobs.employment_type',
                                DB::raw("
                                            GROUP_CONCAT(
                                                DISTINCT CASE 
                                                    WHEN job_metronics_skills.skill_name IN ('".implode("','", $currentJobSkill)."') 
                                                    AND job_metronics_skills.company_id = companies.id 
                                                    THEN job_metronics_skills.skill_name 
                                                    ELSE NULL 
                                                END 
                                                SEPARATOR ', '
                                            ) as matched_skills
                                        "),
                                DB::raw("
                                            (
                                                -- Calculate skill match value
                                                (
                                                    COUNT(
                                                        CASE 
                                                            WHEN job_metronics_skills.skill_name IN ('".implode("','", $currentJobSkill)."') 
                                                            THEN 1
                                                            ELSE NULL
                                                        END
                                                    ) * 10
                                                ) +
                                                
                                                -- Additional conditions
                                                CASE WHEN jobs.min_exp >= {$jobMinExp} THEN 1 ELSE 0 END +
                                                CASE WHEN jobs.max_exp <= {$jobMaxExp} THEN 1 ELSE 0 END +
                                                CASE WHEN jobs.min_salary >= {$jobMinSalary} THEN 1 ELSE 0 END +
                                                CASE WHEN jobs.max_salary <= {$jobMaxSalary} THEN 1 ELSE 0 END +
                                                CASE WHEN commericial_countries.country_id = {$country_id_job} THEN 1 ELSE 0 END +
                                                CASE WHEN jobs.employment_type = {$parent_employment_type} THEN 1 ELSE 0 END
                                            ) as rank_count_total
                                        "),
                                DB::raw("
                                            (
                                                -- Calculate skill match value
                                                (
                                                    COUNT(
                                                        CASE 
                                                            WHEN job_metronics_skills.skill_name IN ('".implode("','", $currentJobSkill)."') 
                                                            THEN 1
                                                            ELSE NULL
                                                        END
                                                    ) * 10
                                                ) 
                                            ) as rank_count_skil
                                        "),
                                'job_metronics_skills.skill_name',
                                'td_industries.ind_name',
                                'staffing_company_relation.from_company_id',
                                'staffing_company_relation.to_company_id',
                                'staffing_company_relation.final_status',
                                'recommendation_relation.id as recommendation_relation_id',
                                'recommendation_relation.from_status as rel_from_status',
                                'recommendation_relation.to_status as rel_to_status',
                                'recommendation_relation.to_status as rel_final_status',
                                'recommendation_relation.job_id as rel_job_id',
                                'recommendation_relation.from_company_id as rel_from_company_id',
                                'recommendation_relation.final_status as recommendation_final_status',
                                'package_active.PermamentRecruitmentLeadIndia_balance',
                                'package_active.PermamentRecruitmentLeadIndia_status',
                                'package_active.BoostKeywordsIndia_balance',
                                'package_active.BoostKeywordsIndia_status',
                                'package_active.BoostKeywordsInternational_balance',
                                'package_active.BoostKeywordsInternational_status',
                                'package_active.PermamentRecruitmentLeadInternational_balance',
                                'package_active.PermamentRecruitmentLeadInternational_status',
                                'package_active.ContractRecruitmentLeadIndia_balance',
                                'package_active.ContractRecruitmentLeadIndia_status',
                                'package_active.ContractRecruitmentLeadInternational_balance',
                                'package_active.ContractRecruitmentLeadInternational_status',
                                'package_active.newjobrequest_status',
                                'package_active.newjobrequest_balance',
                                'package_active.newclientrequest_status',
                                'package_active.newclientrequest_balance',
                                'package_active.boostkeywords_status',
                                'city_list.city as city_name',
                                'state_list.state',
                                'countries.country_name',
                                'client_location.city',
                                'client_location.address',
                                DB::raw('COUNT(job_applications.id) as applications_count'),
                                DB::raw("
                                                (
                                                    {$skillCaseStatements} +
                                                    CASE WHEN jobs.min_exp >= {$jobMinExp} THEN 1 ELSE 0 END +
                                                    CASE WHEN jobs.max_exp <= {$jobMaxExp} THEN 1 ELSE 0 END +
                                                    CASE WHEN jobs.min_salary >= {$jobMinSalary} THEN 1 ELSE 0 END +
                                                    CASE WHEN jobs.max_salary <= {$jobMaxSalary} THEN 1 ELSE 0 END +
                                                    CASE WHEN commericial_countries.country_id = {$country_id_job} THEN 1 ELSE 0 END + 
                                                    CASE WHEN jobs.employment_type = {$parent_employment_type} THEN 1 ELSE 0 END
                                                ) as rank_count
                                            "),
                                DB::raw("
                                                (
                                                    {$boostkeywordcheck}
                                                ) as boostkeywordCount
                                            "),
                                DB::raw("
                                            (
                                                SELECT COUNT(DISTINCT job_metronics_skills.skill_name)
                                                FROM job_metronics_skills
                                                WHERE job_metronics_skills.company_id = companies.id 
                                                AND job_metronics_skills.skill_name IN ('".implode("','", $currentJobSkill)."')
                                            ) / {$skillsCount} * 100 as skill_match_percentage
                                        "),
                                DB::raw("
                                                LEAST(
                                                    ROUND(
                                                        (
                                                            (
                                                                {$skillCaseStatements} +
                                                                CASE WHEN jobs.min_exp >= {$jobMinExp} THEN 15 ELSE 0 END +
                                                                CASE WHEN jobs.max_exp <= {$jobMaxExp} THEN 15 ELSE 0 END +
                                                                CASE WHEN jobs.min_salary >= {$jobMinSalary} THEN 15 ELSE 0 END +
                                                                CASE WHEN jobs.max_salary <= {$jobMaxSalary} THEN 15 ELSE 0 END
                                                            ) / (2 * {$totalSkills} + 4)
                                                        ) * 100, 2
                                                    ), 100
                                                ) as match_percentage
                                            ")
                            );

                        $recommendedDataList = $recommendedDataList->join('jobs', function ($join) use ($company_id) {
                            $join->on('jobs.company_id', '=', 'companies.id')
                                ->on('jobs.company_id', '!=', DB::raw("'".$company_id."'"));
                        });

                        if ($parent_employment_type == 0) {
                            // 0 : Fulltime(In job)      New Job Request :: Fulltime Recruitment Leads  FULLTIME_RECRUITMENT_LEAD(In package_active)

                            if ($clientMainLocation == $country_id_job) {

                                $recommendedDataList = $recommendedDataList->leftJoin('company_coins', function ($join) {
                                    $join->on('company_coins.company_id', '=', 'companies.id')
                                        ->where('company_coins.coins', '>', 0);
                                });

                                $recommendedDataList = $recommendedDataList->leftJoin('package_active', function ($join) {
                                    $join->on('package_active.company_id', '=', 'companies.id');
                                });

                            } else {
                                $recommendedDataList = $recommendedDataList->leftJoin('company_coins', function ($join) {
                                    $join->on('company_coins.company_id', '=', 'companies.id')
                                        ->where('company_coins.coins', '>', 0);
                                });

                                $recommendedDataList = $recommendedDataList->leftJoin('package_active', function ($join) {
                                    $join->on('package_active.company_id', '=', 'companies.id');
                                });
                            }
                        } elseif ($parent_employment_type == 1) {
                            // 1	: Contract(In job)      New Client Request :: Contract Recruitment Lead   CONTRACT_RECRUITMENT_LEAD(In package_active)

                            if ($clientMainLocation == $country_id_job) {
                                $recommendedDataList = $recommendedDataList->leftJoin('company_coins', function ($join) {
                                    $join->on('company_coins.company_id', '=', 'companies.id')
                                        ->where('company_coins.coins', '>', 0);
                                });

                                $recommendedDataList = $recommendedDataList->join('package_active', function ($join) {
                                    $join->on('package_active.company_id', '=', 'companies.id');

                                });
                            } else {
                                $recommendedDataList = $recommendedDataList->leftJoin('company_coins', function ($join) {
                                    $join->on('company_coins.company_id', '=', 'companies.id')
                                        ->where('company_coins.coins', '>', 0);
                                });
                                $recommendedDataList = $recommendedDataList->join('package_active', function ($join) {
                                    $join->on('package_active.company_id', '=', 'companies.id');

                                });
                            }
                        }
                        if ($parent_employment_type == 0) {
                            $recommendedDataList = $recommendedDataList->join('commericials', function ($join) {
                                $join->on('commericials.company_id', '=', 'companies.id')
                                    ->where('commericials.rateType', '=', 2)
                                    ->where('commericials.status', '=', 1);
                            });
                        } elseif ($parent_employment_type == 1) {
                            $recommendedDataList = $recommendedDataList->join('commericials', function ($join) {
                                $join->on('commericials.company_id', '=', 'companies.id')
                                    ->where('commericials.rateType', '=', 1)
                                    ->where('commericials.status', '=', 1);
                            });
                        }
                        $recommendedDataList = $recommendedDataList->leftJoin('commericial_countries', 'commericial_countries.company_id', 'companies.id');
                        $recommendedDataList = $recommendedDataList->join('add_job_wise_condidates as job_applications', 'job_applications.job_id', 'jobs.id');
                        $recommendedDataList = $recommendedDataList->leftJoin('job_metronics_skills', 'job_metronics_skills.job_id', '=', 'jobs.id');
                        $recommendedDataList = $recommendedDataList->leftJoin('client_city_jobs', 'client_city_jobs.job_id', '=', 'jobs.id');
                        $recommendedDataList = $recommendedDataList->leftJoin('client_location_jobs', 'client_location_jobs.job_id', '=', 'jobs.id');
                        $recommendedDataList = $recommendedDataList->leftJoin('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id');

                        $recommendedDataList = $recommendedDataList->leftJoin('countries', 'countries.id', '=', 'client_location.country_id');

                        $recommendedDataList = $recommendedDataList->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id');

                        $recommendedDataList = $recommendedDataList->leftJoin('state_list', 'state_list.id', '=', 'client_location.state');

                        $recommendedDataList = $recommendedDataList->leftJoin('staffing_company_relation', function ($join) use ($company_id) {
                            $join->on('staffing_company_relation.to_company_id', '=', 'companies.id');
                            $join->on('staffing_company_relation.from_company_id', '=', DB::raw("'".$company_id."'"));
                        });
                        // lation.from_company_id', '!=', DB::raw("
                        $recommendedDataList = $recommendedDataList->leftJoin('recommendation_relation', function ($join) use ($jobId) {
                            $join->on('recommendation_relation.to_company_id', '=', 'companies.id');
                            $join->on('recommendation_relation.final_status', '=', DB::raw("'1'"));
                            $join->on('recommendation_relation.job_id', '=', DB::raw("'".$jobId."'"));
                        });

                        $recommendedDataList = $recommendedDataList->leftJoin('td_industries', 'td_industries.ind_id', '=', 'companies.industries');

                        $recommendedDataList = $recommendedDataList->where('companies.company_type', '!=', 3);

                        if (! empty($currentJobSkill)) {
                            // dd($currentJobSkill);
                            $recommendedDataList = $recommendedDataList->where(function ($subquery) use ($currentJobSkill) {
                                foreach ($currentJobSkill as $skill) {
                                    $subquery->where('jobs.title', 'LIKE', "%{$skill}%");
                                    $subquery->orWhere('job_metronics_skills.skill_name', 'LIKE', "%{$skill}%");
                                }
                            });
                        }

                        if ($recommendedDataList->exists()) {

                            // dd($recommendedDataList->exists());

                            $temp_skill_name = '';
                            $recommendedDataList = $recommendedDataList->where(function ($subquery) use ($company_id, $country_id_job) {
                                $subquery->where('companies.id', '!=', $company_id)
                                    ->whereDate('company_coins.end_date', '>=', date('Y-m-d'));
                                $subquery->where('commericial_countries.country_id', $country_id_job);
                                $subquery->whereNull('recommendation_relation.final_status');
                            });
                        }

                        $recommendedDataList = $recommendedDataList->groupBy('companies.id')->having('applications_count', '>', 0);

                        $recommendedDataList = $recommendedDataList->take($remainRecom);

                        $recommendedDataList = $recommendedDataList->orderByDesc('skill_match_percentage'); // Order by the computed rank count
                        $recommendedDataList = $recommendedDataList->orderByDesc('SimilarReq');
                        $recommendedDataList = $recommendedDataList->orderByDesc('Profiles');
                        $recommendedDataList = $recommendedDataList->orderByDesc('company_coins.coins');

                        $mergedUsers = $recommendedDataList;

                        // dd($mergedUsers->count());
                        $recIDFilter = $mergedUsers->pluck('id');
                        $recIDFilterArray = $recIDFilter->toArray();
                        // dd($recIDFilterArray);

                        $topCount = count($mergedUsers->get());

                        // dd($boostkeywordsDataList->get(),$recIDFilterArray,$topCount,$boostIDFilterArray);
                        $randdon = 20;
                        $remain = $randdon - $topCount;

                        if ($remain != 0) {
                            $rendomDataList = DB::table('companies')
                                ->select(
                                    'companies.id',
                                    'companies.company_name',
                                    'companies.logo',
                                    'companies.industries',
                                    'companies.success_per',
                                    'jobs.title',
                                    DB::raw('COUNT(job_applications.id) as applications_count'),
                                    DB::raw('count(DISTINCT job_applications.job_id) as SimilarReq'),
                                    DB::raw('count(DISTINCT job_applications.id) as Profiles'),
                                    'jobs.employment_type',
                                    DB::raw("
                                                            GROUP_CONCAT(
                                                                DISTINCT CASE 
                                                                    WHEN job_metronics_skills.skill_name IN ('".implode("','", $currentJobSkill)."') 
                                                                    AND job_metronics_skills.company_id = companies.id 
                                                                    THEN job_metronics_skills.skill_name 
                                                                    ELSE NULL 
                                                                END 
                                                                SEPARATOR ', '
                                                            ) as matched_skills
                                                        "),
                                    DB::raw("
                                                            (
                                                                -- Calculate skill match value
                                                                (
                                                                    COUNT(
                                                                        CASE 
                                                                            WHEN job_metronics_skills.skill_name IN ('".implode("','", $currentJobSkill)."') 
                                                                            THEN 1
                                                                            ELSE NULL
                                                                        END
                                                                    ) * 10
                                                                ) +
                                                                
                                                                -- Additional conditions
                                                                CASE WHEN jobs.min_exp >= {$jobMinExp} THEN 1 ELSE 0 END +
                                                                CASE WHEN jobs.max_exp <= {$jobMaxExp} THEN 1 ELSE 0 END +
                                                                CASE WHEN jobs.min_salary >= {$jobMinSalary} THEN 1 ELSE 0 END +
                                                                CASE WHEN jobs.max_salary <= {$jobMaxSalary} THEN 1 ELSE 0 END +
                                                                CASE WHEN commericial_countries.country_id = {$country_id_job} THEN 1 ELSE 0 END +
                                                                CASE WHEN jobs.employment_type = {$parent_employment_type} THEN 1 ELSE 0 END
                                                            ) as rank_count_total
                                                        "),
                                    DB::raw("
                                                            (
                                                                -- Calculate skill match value
                                                                (
                                                                    COUNT(
                                                                        CASE 
                                                                            WHEN job_metronics_skills.skill_name IN ('".implode("','", $currentJobSkill)."') 
                                                                            THEN 1
                                                                            ELSE NULL
                                                                        END
                                                                    ) * 10
                                                                ) 
                                                            ) as rank_count_skil
                                                        "),
                                    'job_metronics_skills.skill_name',
                                    'td_industries.ind_name',
                                    'staffing_company_relation.from_company_id',
                                    'staffing_company_relation.to_company_id',
                                    'staffing_company_relation.final_status',
                                    'recommendation_relation.id as recommendation_relation_id',
                                    'recommendation_relation.from_status as rel_from_status',
                                    'recommendation_relation.to_status as rel_to_status',
                                    'recommendation_relation.to_status as rel_final_status',
                                    'recommendation_relation.job_id as rel_job_id',
                                    'recommendation_relation.from_company_id as rel_from_company_id',
                                    'recommendation_relation.final_status as recommendation_final_status',
                                    'package_active.PermamentRecruitmentLeadIndia_balance',
                                    'package_active.PermamentRecruitmentLeadIndia_status',
                                    'package_active.BoostKeywordsIndia_balance',
                                    'package_active.BoostKeywordsIndia_status',
                                    'package_active.BoostKeywordsInternational_balance',
                                    'package_active.BoostKeywordsInternational_status',
                                    'package_active.PermamentRecruitmentLeadInternational_balance',
                                    'package_active.PermamentRecruitmentLeadInternational_status',
                                    'package_active.ContractRecruitmentLeadIndia_balance',
                                    'package_active.ContractRecruitmentLeadIndia_status',
                                    'package_active.ContractRecruitmentLeadInternational_balance',
                                    'package_active.ContractRecruitmentLeadInternational_status',
                                    'package_active.newjobrequest_status',
                                    'package_active.newjobrequest_balance',
                                    'package_active.newclientrequest_status',
                                    'package_active.newclientrequest_balance',
                                    'package_active.boostkeywords_status',
                                    'city_list.city as city_name',
                                    'state_list.state',
                                    'countries.country_name',
                                    'client_location.city',
                                    'client_location.address',
                                    DB::raw("
                                                            (
                                                                {$skillCaseStatements} +
                                                                CASE WHEN jobs.min_exp >= {$jobMinExp} THEN 1 ELSE 0 END +
                                                                CASE WHEN jobs.max_exp <= {$jobMaxExp} THEN 1 ELSE 0 END +
                                                                CASE WHEN jobs.min_salary >= {$jobMinSalary} THEN 1 ELSE 0 END +
                                                                CASE WHEN jobs.max_salary <= {$jobMaxSalary} THEN 1 ELSE 0 END +
                                                                CASE WHEN commericial_countries.country_id = {$country_id_job} THEN 1 ELSE 0 END + 
                                                                CASE WHEN jobs.employment_type = {$parent_employment_type} THEN 1 ELSE 0 END
                                                            ) as rank_count
                                                        "),
                                    DB::raw("
                                                            (
                                                                {$boostkeywordcheck}
                                                            ) as boostkeywordCount
                                                        "),
                                    DB::raw("
                                                        (
                                                            SELECT COUNT(DISTINCT job_metronics_skills.skill_name)
                                                            FROM job_metronics_skills
                                                            WHERE job_metronics_skills.company_id = companies.id 
                                                            AND job_metronics_skills.skill_name IN ('".implode("','", $currentJobSkill)."')
                                                        ) / {$skillsCount} * 100 as skill_match_percentage
                                                    "),
                                    DB::raw("
                                                            LEAST(
                                                                ROUND(
                                                                    (
                                                                        (
                                                                            {$skillCaseStatements} +
                                                                            CASE WHEN jobs.min_exp >= {$jobMinExp} THEN 15 ELSE 0 END +
                                                                            CASE WHEN jobs.max_exp <= {$jobMaxExp} THEN 15 ELSE 0 END +
                                                                            CASE WHEN jobs.min_salary >= {$jobMinSalary} THEN 15 ELSE 0 END +
                                                                            CASE WHEN jobs.max_salary <= {$jobMaxSalary} THEN 15 ELSE 0 END
                                                                        ) / (2 * {$totalSkills} + 4)
                                                                    ) * 100, 2
                                                                ), 100
                                                            ) as match_percentage
                                                        ")
                                );

                            $rendomDataList = $rendomDataList->join('jobs', function ($join) use ($company_id) {
                                $join->on('jobs.company_id', '=', 'companies.id')
                                    ->on('jobs.company_id', '!=', DB::raw("'".$company_id."'"));
                            });

                            if ($parent_employment_type == 0) {
                                // 0 : Fulltime(In job)      New Job Request :: Fulltime Recruitment Leads  FULLTIME_RECRUITMENT_LEAD(In package_active)

                                if ($clientMainLocation == $country_id_job) {
                                    $rendomDataList = $rendomDataList->leftJoin('company_coins', function ($join) {
                                        $join->on('company_coins.company_id', '=', 'companies.id')
                                            ->where('company_coins.coins', '>', 0);
                                    });
                                    $rendomDataList = $rendomDataList->leftJoin('package_active', function ($join) {
                                        $join->on('package_active.company_id', '=', 'companies.id');
                                    });
                                } else {
                                    $rendomDataList = $rendomDataList->leftJoin('company_coins', function ($join) {
                                        $join->on('company_coins.company_id', '=', 'companies.id')
                                            ->where('company_coins.coins', '>', 0);
                                    });
                                    $rendomDataList = $rendomDataList->leftJoin('package_active', function ($join) {
                                        $join->on('package_active.company_id', '=', 'companies.id');
                                    });
                                }
                            } elseif ($parent_employment_type == 1) {
                                // 1	: Contract(In job)      New Client Request :: Contract Recruitment Lead   CONTRACT_RECRUITMENT_LEAD(In package_active)

                                if ($clientMainLocation == $country_id_job) {
                                    $rendomDataList = $rendomDataList->leftJoin('company_coins', function ($join) {
                                        $join->on('company_coins.company_id', '=', 'companies.id')
                                            ->where('company_coins.coins', '>', 0);
                                    });
                                    $rendomDataList = $rendomDataList->join('package_active', function ($join) {
                                        $join->on('package_active.company_id', '=', 'companies.id');

                                    });
                                } else {
                                    $rendomDataList = $rendomDataList->leftJoin('company_coins', function ($join) {
                                        $join->on('company_coins.company_id', '=', 'companies.id')
                                            ->where('company_coins.coins', '>', 0);
                                    });
                                    $rendomDataList = $rendomDataList->join('package_active', function ($join) {
                                        $join->on('package_active.company_id', '=', 'companies.id');

                                    });
                                }
                            }

                            if ($parent_employment_type == 0) {
                                $rendomDataList = $rendomDataList->join('commericials', function ($join) {
                                    $join->on('commericials.company_id', '=', 'companies.id')
                                        ->where('commericials.rateType', '=', 2)
                                        ->where('commericials.status', '=', 1);
                                });
                            } elseif ($parent_employment_type == 1) {
                                $rendomDataList = $rendomDataList->join('commericials', function ($join) {
                                    $join->on('commericials.company_id', '=', 'companies.id')
                                        ->where('commericials.rateType', '=', 1)
                                        ->where('commericials.status', '=', 1);
                                });
                            }

                            $rendomDataList = $rendomDataList->leftJoin('commericial_countries', 'commericial_countries.company_id', 'companies.id');
                            $rendomDataList = $rendomDataList->join('add_job_wise_condidates as job_applications', 'job_applications.job_id', 'jobs.id');
                            $rendomDataList = $rendomDataList->leftJoin('job_metronics_skills', 'job_metronics_skills.job_id', '=', 'jobs.id');
                            $rendomDataList = $rendomDataList->leftJoin('client_city_jobs', 'client_city_jobs.job_id', '=', 'jobs.id');
                            $rendomDataList = $rendomDataList->leftJoin('client_location_jobs', 'client_location_jobs.job_id', '=', 'jobs.id');
                            $rendomDataList = $rendomDataList->leftJoin('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id');

                            $rendomDataList = $rendomDataList->leftJoin('countries', 'countries.id', '=', 'client_location.country_id');

                            $rendomDataList = $rendomDataList->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id');

                            $rendomDataList = $rendomDataList->leftJoin('state_list', 'state_list.id', '=', 'client_location.state');

                            $rendomDataList = $rendomDataList->leftJoin('staffing_company_relation', function ($join) use ($company_id) {
                                $join->on('staffing_company_relation.to_company_id', '=', 'companies.id');
                                $join->on('staffing_company_relation.from_company_id', '=', DB::raw("'".$company_id."'"));
                            });
                            // dd( $company_id); lation.from_company_id', '!=', DB::raw("
                            $rendomDataList = $rendomDataList->leftJoin('recommendation_relation', function ($join) use ($jobId) {
                                $join->on('recommendation_relation.to_company_id', '=', 'companies.id');
                                $join->on('recommendation_relation.final_status', '=', DB::raw("'1'"));
                                $join->on('recommendation_relation.job_id', '=', DB::raw("'".$jobId."'"));
                            });

                            $rendomDataList = $rendomDataList->leftJoin('td_industries', 'td_industries.ind_id', '=', 'companies.industries');

                            $rendomDataList = $rendomDataList->whereNotIn('companies.id', $recIDFilterArray);
                            $rendomDataList = $rendomDataList->where('companies.company_type', '!=', 3);

                            $temp_skill_name = '';
                            $rendomDataList = $rendomDataList->where(function ($subquery) use ($company_id, $recIDFilterArray, $country_id_job) {
                                $subquery->where('companies.id', '!=', $company_id)
                                    ->whereNotIn('companies.id', $recIDFilterArray)
                                    ->whereDate('company_coins.end_date', '>=', date('Y-m-d'));
                                $subquery->where('commericial_countries.country_id', $country_id_job);
                                $subquery->whereNull('recommendation_relation.final_status');
                            });

                            $rendomDataList = $rendomDataList->groupBy('companies.id')->having('applications_count', '>', 0);
                            $rendomDataList = $rendomDataList->take($remain);
                            $rendomDataList = $rendomDataList->orderByDesc('skill_match_percentage'); // Order by the computed rank count
                            $rendomDataList = $rendomDataList->orderByDesc('SimilarReq');
                            $rendomDataList = $rendomDataList->orderByDesc('Profiles');
                            $rendomDataList = $rendomDataList->orderByDesc('company_coins.coins');
                            $mergedUsers = $recommendedDataList->union($rendomDataList);

                            $mergedUsers = $mergedUsers->get();

                        }

                        // dd($company_id,$mergedUsers);

                        if (count($mergedUsers) > 0) {
                            $tracker_html = '<table align="left" border="1" style="border-collapse:collapse; border:solid 1px black; width:1272px">
                                            <thead>
                                            <tr style="mso-yfti-irow:0;mso-yfti-firstrow:yes;height:15.75pt"> 
                                            <td valign="bottom" style="border:solid windowtext 1.0pt;border-left:none; background:#BDD7EE;padding:1.5pt 2.25pt 1.5pt 2.25pt;height:15.75pt; overflow:hidden"> <p class="MsoNormal"><b><u><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">S.no<o:p></o:p></span></u></b></p></td>
                                            <td valign="bottom" style="border:solid windowtext 1.0pt;background:#BDD7EE; padding:1.5pt 2.25pt 1.5pt 2.25pt;height:15.75pt"> <p class="MsoNormal"><b><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">Name</span></b><b><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif;mso-fareast-language: EN-IN"><o:p></o:p></span></b></p></td>                    
                                            <td valign="bottom" style="border:solid windowtext 1.0pt;background:#BDD7EE; padding:1.5pt 2.25pt 1.5pt 2.25pt;height:15.75pt"> <p class="MsoNormal"><b><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">Jobs</span></b><b><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif;mso-fareast-language: EN-IN"><o:p></o:p></span></b></p></td>                
                                            <td valign="bottom" style="border:solid windowtext 1.0pt;border-left:none; background:#BDD7EE;padding:1.5pt 2.25pt 1.5pt 2.25pt;height:15.75pt; overflow:hidden"> <p class="MsoNormal"><b><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">Rates<o:p></o:p></span></b></p></td>
                                            <td valign="bottom" style="border:solid windowtext 1.0pt;border-left:none; background:#BDD7EE;padding:1.5pt 2.25pt 1.5pt 2.25pt;height:15.75pt; overflow:hidden"> <p class="MsoNormal"><b><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">Size<o:p></o:p></span></b></p></td>  
                    
                                            <td valign="bottom" style="border:solid windowtext 1.0pt;border-left:none; background:#BDD7EE;padding:1.5pt 2.25pt 1.5pt 2.25pt;height:15.75pt; overflow:hidden"> <p class="MsoNormal"><b><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">Action<o:p></o:p></span></b></p></td>
                                            </tr>
                                            </thead>';

                            $cnt = 1;

                            foreach ($mergedUsers as $row) {

                                $companies_id = $row->id;
                                $alldatalist = DB::table('jobs')
                                    ->join('add_job_wise_condidates', 'add_job_wise_condidates.job_id', 'jobs.id')
                                    ->join('job_applications', 'job_applications.id', 'add_job_wise_condidates.job_application_id')
                                    ->join('job_application_skills', 'job_application_skills.job_application_id', 'job_applications.id')
                                    ->where('jobs.status', '=', 'active')
                                    ->whereIn('job_application_skills.skills', $currentJobSkill);
                                $hiringtype = $parent_employment_type;
                                if ($hiringtype === '1' || $hiringtype === '0') {
                                    $alldatalist = $alldatalist->where('jobs.employment_type', '=', $hiringtype);
                                }

                                $alldatalist = $alldatalist->where(function ($qry) use ($companies_id) {
                                    $qry->where('add_job_wise_condidates.company_id', '=', $companies_id)
                                        ->orWhere('add_job_wise_condidates.thirdparty_company_id', '=', $companies_id);
                                })->select(
                                    DB::raw('count(DISTINCT jobs.id) as SimilarReq'),
                                    DB::raw('count(DISTINCT CASE WHEN job_applications.condidate_type = "bench_condidate" THEN job_applications.id END) as benchProfiles'),
                                    DB::raw('count(DISTINCT CASE WHEN job_applications.condidate_type != "bench_condidate" THEN job_applications.id END) as otherProfiles')
                                )
                                    ->get();
                                // dd($alldatalist);
                                $Profiles = 0;
                                $titleffc = 'Profiles';
                                $SimilarReq = 0;
                                if ($hiringtype === '1') {
                                    if ($alldatalist[0]->benchProfiles > 0) {
                                        $Profiles = $alldatalist[0]->benchProfiles;
                                        $SimilarReq = $alldatalist[0]->SimilarReq;
                                        $titleffc = 'Bench';
                                    } else {
                                        $Profiles = $alldatalist[0]->otherProfiles;
                                        $SimilarReq = $alldatalist[0]->SimilarReq;
                                        $titleffc = 'Profiles';
                                    }
                                } elseif ($hiringtype === '0') {

                                    $Profiles = $alldatalist[0]->otherProfiles;
                                    $SimilarReq = $alldatalist[0]->SimilarReq;
                                    $titleffc = 'Profiles';

                                } else {
                                    $Profiles = ($alldatalist[0]->benchProfiles) + ($alldatalist[0]->otherProfiles);
                                    $SimilarReq = $alldatalist[0]->SimilarReq;
                                }

                                if ($parent_employment_type == 1) {
                                    $commtype = 1;
                                } else {
                                    $commtype = 2; // full time
                                }
                                $rates = '';
                                $currency = GeneralMethod::currency();
                                $salaryMode = GeneralMethod::salaryMode();
                                $currencySymbole = GeneralMethod::currencySymbole();
                                $minimumRate = DB::table('commericials')
                                    ->join('commericial_details', 'commericial_details.commericial_id', '=', 'commericials.id')
                                    ->where('commericials.commtype', '=', $commtype)
                                    ->where('commericials.company_id', '=', $row->id)
                                    // Intent: rate cards use the same active global experience-band source as Commercials settings.
                                    ->whereExists(function ($experienceBands) use ($jobMinExp, $jobMaxExp) {
                                        $experienceBands->selectRaw('1')
                                            ->from('commercial_experience_bands as experience_bands')
                                            ->where('experience_bands.is_active', 1)
                                            ->whereColumn('commericial_details.exp', '>=', 'experience_bands.min_experience')
                                            ->whereColumn('commericial_details.exp', '<=', 'experience_bands.max_experience')
                                            ->where('experience_bands.max_experience', '>=', $jobMinExp)
                                            ->where('experience_bands.min_experience', '<=', $jobMaxExp);
                                    })
                                    ->min('commericial_details.rate');

                                $minimumRateType = DB::table('commericials')
                                    ->select('commericials.*')
                                    ->where('commericials.commtype', '=', $commtype)
                                    ->where('commericials.company_id', '=', $row->id)
                                    ->first();
                                // sal_mode /salary_currency

                                if ($minimumRateType) {
                                    if ($minimumRateType->commtype == 1) {
                                        $current_cur = $currencySymbole[@$jobSalaryCurrency];
                                        $current_mode = $salaryMode[@$jobSalaryMode];
                                        $currencymain = GeneralMethod::currency();  // Currency code
                                        // dd($currency);

                                        $currency = $currencymain[@$minimumRateType->salary_currency];

                                        $convertedAmoun = GeneralMethod::conversionRates($currency, $minimumRate, @$minimumRateType->sal_mode);
                                        // dd($convertedAmoun[$jobSalaryMode]);
                                        $exchangeRates = [
                                            'INR' => 1,
                                            'EUR' => 90.33,  // Example exchange rate for EUR to INR
                                            'JPY' => 0.68,   // Example exchange rate for JPY to INR
                                            'GBP' => 103.70, // Example exchange rate for GBP to INR
                                            'USD' => 83.63,   // Example exchange rate for USD to INR
                                            'AED' => 20.38,   // Example exchange rate for AED to INR
                                        ];
                                        // dd($jobSalaryCurrency);
                                        $currencydd = $currencymain[@$jobSalaryCurrency];
                                        $exchangeRateInrToEur = 1 / $exchangeRates[$currencydd];

                                        // Convert INR to EUR
                                        $amount = $convertedAmoun[$jobSalaryMode]; // *

                                        $currencymain = GeneralMethod::currency();  // Currency code
                                        $currency = $currencymain[@$jobSalaryCurrency];
                                        $econvertedAmount = $minimumRate;
                                        if ($minimumRateType->salary_currency > 0) {
                                            $econvertedAmount = GeneralMethod::convertToINR($minimumRate, $minimumRateType->salary_currency);
                                        }

                                        $convertedAmounEctc = GeneralMethod::conversionRates($currency, $econvertedAmount, $minimumRateType->sal_mode);
                                        $minimumRate = $convertedAmounEctc[@$jobSalaryMode];

                                        $salary = GeneralMethod::thousandsCurrencyFormat($minimumRate);

                                        $rates = $current_cur.$salary.'/'.$current_mode;
                                        $jobtype = 'Contract';
                                    } else {

                                        $rates = $minimumRate.'%';
                                        $jobtype = 'Full time';

                                    }
                                }

                                $Sizecount = DB::table('users')->where('company_id', $row->id)->count();
                                $regStatus = 'UnRegistered';
                                $regStatusCss = 'text';
                                if (! empty($row->from_company_id)) {
                                    if (($row->from_company_id == $company_id) && ($row->to_company_id == $row->id) && ($row->final_status == 1)) {
                                        $regStatus = 'Registered';

                                        $regStatusCss = 'text-success';
                                    }
                                }

                                if ($SimilarReq > 0 && $Profiles > 0) {
                                    $existing_path = url('/service_company/jobs');

                                    $tracker_html .= '
                                                            <tr style="mso-yfti-irow:1;mso-yfti-lastrow:yes;height:15.75pt"> 
                                                            <td valign="bottom" style="border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt; border-right:solid windowtext 1.0pt;background:white;padding:1.5pt 2.25pt 1.5pt 2.25pt; height:15.75pt;overflow:hidden"> <p class="MsoNormal"><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">'.$cnt.'<o:p></o:p></span></p></td>
                                                            <td valign="bottom" style="border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt; border-right:solid windowtext 1.0pt;background:white;padding:1.5pt 2.25pt 1.5pt 2.25pt; height:15.75pt;overflow:hidden"> <p class="MsoNormal"><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">'.substr(ucwords(@$row->company_name), 0, 30).'<br><span>'.substr(ucwords(@$row->ind_name), 0, 27).'</span><o:p></o:p></span></p></td>
                            
                                                            <td valign="bottom" style="border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt; border-right:solid windowtext 1.0pt;background:white;padding:1.5pt 2.25pt 1.5pt 2.25pt; height:15.75pt;overflow:hidden"> <p class="MsoNormal"><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif"<span>'.$SimilarReq.' Similar work experience<br>'.$Profiles.' '.$titleffc.' Available </span><o:p></o:p></span></p></td>
                            
                                                            <td valign="bottom" style="border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt; border-right:solid windowtext 1.0pt;background:white;padding:1.5pt 2.25pt 1.5pt 2.25pt; height:15.75pt;overflow:hidden"> <p class="MsoNormal"><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">'.$rates.' <br>
                                                            '.$jobtype.' <o:p></o:p></span></p></td>
                            
                                                            <td valign="bottom" style="border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt; border-right:solid windowtext 1.0pt;background:white;padding:1.5pt 2.25pt 1.5pt 2.25pt; height:15.75pt;overflow:hidden"> <p class="MsoNormal"><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">'.$regStatus.'<br><span>'.$Sizecount.'Member </span><o:p></o:p></span></p></td>
                            
                            
                                                            <td valign="bottom" style="border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt; border-right:solid windowtext 1.0pt;background:white;padding:1.5pt 2.25pt 1.5pt 2.25pt; height:15.75pt;overflow:hidden"> <p class="MsoNormal"><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif"><a href="'.$existing_path.'">View List</a><o:p></o:p></span></p></td>				
                            
                                                            </tr>';
                                    $cnt = $cnt + 1;
                                }
                            }

                            $tracker_html .= '<table>';

                        }

                        $basecompany = GeneralMethod::baseCompany();
                        $senderName = ($basecompany->companyName) ? $basecompany->companyName : '';
                        $fromAddress = ($basecompany->companyEmail) ? $basecompany->companyEmail : '';

                        if (count($mergedUsers) > 0) {
                            $checkMailPermision = GeneralMethod::mailPermission(37);
                            $email_notifications = DB::table('email_notifications')->where('id', 37)->where('status', 1)->first();

                            $subject = str_replace(['[JOB_TITLE]', '[CLIENT_COMPANY_NAME]', '[JOB_ID]'], [@$job_val->title, @$job_val->clients_name, @$job_val->id], @$email_notifications->subject);

                            $tracker_html = str_replace(['[JOB_TITLE]', '[CLIENT_COMPANY_NAME]', '[JOB_ID]', '[TRACKER]'], [@$job_val->title, @$job_val->clients_name, @$job_val->id, $tracker_html], @$email_notifications->body_description);

                            $cc = [];
                            $attachments = [];
                            $data = ['message' => $tracker_html, 'subject' => $subject, 'company_name' => '4Bell Technology', 'consultant_name' => '4Bell Technology', 'position' => 'PHP'];
                            $email = $val->company_email;
                            $viewfile = 'email.common.index';

                            if ($checkMailPermision) {
                                QueueNotificationMailService::dispatch(
                                    $email,
                                    $subject,
                                    $viewfile,
                                    $data,
                                    $attachments,
                                    $fromAddress,
                                    $senderName,
                                    $cc,
                                    $company_id
                                )->onConnection('emails');
                            }

                        }

                    }

                }

            }

        }

    }
    /** End Partner Recommened Match */

    /** Start Notification Mail Reset */
    public function notificationMailReset()
    {
        $smtp = DB::table('smtp_settings')->where('status', '=', 0)->get();
        if ($smtp) {
            foreach ($smtp as $val) {
                $dailyCount = 0;
                DB::table('smtp_settings')->where('id', '=', $val->id)->update(['dailyCount' => $dailyCount]);
            }
        }
    }
    /** End Notification Mail Reset */

    /** Start Notification Mail Reset */
    public function pendingProfilnotificationMail()
    {

        $profilesCompletion = DB::table('companies')
            ->join('users', 'users.company_id', '=', 'companies.id')
            ->select('users.email', 'users.name', 'users.id', 'users.company_id')
            ->whereIn('companies.company_type', [1, 2])
            ->where('companies.industries', '=', null)
            ->where('companies.description', '=', null)
            ->where('users.root_user', '=', 1)
            ->where('users.status', '=', 'active')
            ->get();

        $clientsprofiles = DB::table('clients')
            ->join('countries', 'countries.id', '=', 'clients.company_id')
            ->join('users', 'users.company_id', '=', 'companies.id')
            ->leftJoin('client_location', 'client_location.client_id', '=', 'clients.id')
            ->select('users.email', 'users.name', 'users.id', 'users.company_id')
            ->whereIn('companies.company_type', [1, 2])
            ->where('clients.tags', '=', null)
            ->where('client_location.address', '=', null)
            ->where('users.root_user', '=', 1)
            ->get();

        $basecompany = GeneralMethod::baseCompany();
        $senderName = ($basecompany->companyName) ? $basecompany->companyName : '';
        $fromAddress = ($basecompany->companyEmail) ? $basecompany->companyEmail : '';

        //    dd(count($profilesCompletion));
        if (count($profilesCompletion) > 0) {
            $tracker_html = '';
            foreach ($profilesCompletion as $com_val) {
                $company_id = $com_val->company_id;
                $checkMailPermision = GeneralMethod::mailPermission(41);
                $email_notifications = DB::table('email_notifications')->where('id', 41)->where('status', 1)->first();

                $subject = @$email_notifications->subject;

                $encode = encrypt($com_val->id);
                $Url = url('/service_company/account/'.$encode);

                $AcceptInvite = '<a href="'.$Url.'" rel="noopener" style="text-decoration:none;display:inline-block;text-align:center;padding:0.75575rem 1.3rem;font-size:0.925rem;line-height:1.5;border-radius:0.35rem;color:#ffffff;background-color:#009EF7;border:0px;margin-right:0.75rem!important;font-weight:600!important;outline:none!important;vertical-align:middle">Confirm Services & Rates</a>';
                $urlLink = '<a href="'.$Url.'" rel="noopener" style="text-decoration:none;color: #009EF7">'.$Url.'</a>';

                $tracker_html = str_replace(['[{VENDOR_NAME}]', '[ACCEPT_INVITE]', '[URL_LINK]'], [@$com_val->name, $AcceptInvite, $urlLink], @$email_notifications->body_description);

                $cc = [];
                $attachments = [];
                $data = ['message' => $tracker_html, 'subject' => $subject, 'company_name' => '4Bell Technology', 'consultant_name' => '4Bell Technology', 'position' => 'PHP'];
                $email = $com_val->email;
                $viewfile = 'email.common.index';

                if ($checkMailPermision) {
                    QueueNotificationMailService::dispatch(
                        $email,
                        $subject,
                        $viewfile,
                        $data,
                        $attachments,
                        $fromAddress,
                        $senderName,
                        $cc,
                        $company_id
                    )->onConnection('emails');
                }
            }

        } elseif (count($clientsprofiles) > 0) {
            $tracker_html = '';
            foreach ($clientsprofiles as $com_val) {
                $company_id = $com_val->company_id;
                $checkMailPermision = GeneralMethod::mailPermission(41);
                $email_notifications = DB::table('email_notifications')->where('id', 41)->where('status', 1)->first();

                $subject = @$email_notifications->subject;

                $encode = encrypt($com_val->id);
                $Url = url('/service_company/account/'.$encode);

                $AcceptInvite = '<a href="'.$Url.'" rel="noopener" style="text-decoration:none;display:inline-block;text-align:center;padding:0.75575rem 1.3rem;font-size:0.925rem;line-height:1.5;border-radius:0.35rem;color:#ffffff;background-color:#009EF7;border:0px;margin-right:0.75rem!important;font-weight:600!important;outline:none!important;vertical-align:middle">Confirm Services & Rates</a>';
                $urlLink = '<a href="'.$Url.'" rel="noopener" style="text-decoration:none;color: #009EF7">'.$Url.'</a>';

                $tracker_html = str_replace(['[{VENDOR_NAME}]', '[ACCEPT_INVITE]', '[URL_LINK]'], [@$com_val->name, $AcceptInvite, $urlLink], @$email_notifications->body_description);

                $cc = [];
                $attachments = [];
                $data = ['message' => $tracker_html, 'subject' => $subject, 'company_name' => '4Bell Technology', 'consultant_name' => '4Bell Technology', 'position' => 'PHP'];
                $email = $com_val->email;
                $viewfile = 'email.common.index';

                if ($checkMailPermision) {
                    QueueNotificationMailService::dispatch(
                        $email,
                        $subject,
                        $viewfile,
                        $data,
                        $attachments,
                        $fromAddress,
                        $senderName,
                        $cc,
                        $company_id
                    )->onConnection('emails');
                }
            }
        }

        //    dd( $profilesCompletion);

    }
    /** End Notification Mail Reset */

    /** Start job Request Auto Expired After Valid Date */
    public function jobRequestAutoExpiredAfterValidDate()
    {
        $jobexpireation = DB::table('features')->select('features.*')->where('id', '=', 1)->orderBy('features.id', 'ASC')->first();

        if (@$jobexpireation) {
            if ($jobexpireation->status) {
                $valid_day = $jobexpireation->days;
                $valid_date = date('Y-m-d H:i:s', strtotime("-{$valid_day} days"));

                $countJobs = DB::table('recommendation_relation')->where('created_at', '<', $valid_date)->where('final_status', '=', 0)->get();
                if (count($countJobs) > 0) {
                    foreach ($countJobs as $row) {
                        $exists = $this->elasticService->getClient()->exists([
                            'index' => ELASTIC_JOB_REQUEST,
                            'id' => $row->id,
                        ]);
                        if ($exists) {
                            $updateTime = now()->toIso8601String();
                            $updateParams = [
                                'index' => ELASTIC_JOB_REQUEST,
                                'id' => $row->id,
                                'body' => [
                                    'doc' => [
                                        'final_status' => 3,
                                        'updated_at' => $updateTime,
                                    ],
                                    'doc_as_upsert' => false, // Changed to false since we verified existence
                                ],
                                'refresh' => true, // Force immediate refresh
                            ];
                            $this->elasticService->getClient()->update($updateParams);
                        }
                        DB::table('recommendation_relation')->where('id', $row->id)->update(['final_status' => 3]);

                    }

                }

            }
        }

    }
    /** End job Request Auto Expired After Valid Date */

    /** Start client request auto expired after valid date */
    public function clientRequestAutoExpiredAfterValidDate()
    {
        $clientexpireation = DB::table('features')->select('features.*')->where('id', '=', 2)->orderBy('features.id', 'ASC')->first();

        if (@$clientexpireation) {
            if ($clientexpireation->status) {
                $valid_day = $clientexpireation->days;
                $valid_date = date('Y-m-d H:i:s', strtotime("-{$valid_day} days"));

                $clientCount = DB::table('staffing_company_invitation')->where('from_created_at', '<', $valid_date)->where('to_invitation_status', '=', 0)->get();

                if (count($clientCount) > 0) {
                    foreach ($clientCount as $row) {
                        $exists = $this->elasticService->getClient()->exists([
                            'index' => ELASTIC_CLIENT_REQUEST,
                            'id' => $row->id,
                        ]);
                        if ($exists) {
                            $updateTime = now()->toIso8601String();
                            $updateParams = [
                                'index' => ELASTIC_CLIENT_REQUEST,
                                'id' => $row->id,
                                'body' => [
                                    'doc' => [
                                        'to_invitation_status' => 2,
                                        'updated_at' => $updateTime,
                                    ],
                                    'doc_as_upsert' => false, // Changed to false since we verified existence
                                ],
                                'refresh' => true, // Force immediate refresh
                            ];
                            $this->elasticService->getClient()->update($updateParams);
                        }
                        DB::table('staffing_company_invitation')->where('id', $row->id)->update(['to_invitation_status' => 2]);

                    }

                }

            }
        }

    }
    /** End  client request auto expired after valid date */

    /** Start candidate request after valid date */
    public function candidateRequestAutoExpiredAfterValidDate()
    {
        $benchCandidateExpireation = DB::table('features')->select('features.*')->where('id', '=', 4)->orderBy('features.id', 'ASC')->first();

        if (@$benchCandidateExpireation) {
            if ($benchCandidateExpireation->status) {
                $valid_day = $benchCandidateExpireation->days;
                $valid_date = date('Y-m-d H:i:s', strtotime("-{$valid_day} days"));

                $candidateRequCount = DB::table('rec_candidate_assign')->where('created_at', '<', $valid_date)->where('ac_status', '=', 0)->get();

                // dd($candidateRequCount);

                if (count($candidateRequCount) > 0) {
                    foreach ($candidateRequCount as $row) {
                        $exists = $this->elasticService->getClient()->exists([
                            'index' => ELASTIC_CANDIDATES_REQUEST,
                            'id' => $row->id,
                        ]);
                        if ($exists) {
                            $updateTime = now()->toIso8601String();
                            $updateParams = [
                                'index' => ELASTIC_CANDIDATES_REQUEST,
                                'id' => $row->id,
                                'body' => [
                                    'doc' => [
                                        'ac_status' => 2,
                                        'updated_at' => $updateTime,
                                    ],
                                    'doc_as_upsert' => false, // Changed to false since we verified existence
                                ],
                                'refresh' => true, // Force immediate refresh
                            ];
                            $this->elasticService->getClient()->update($updateParams);
                        }
                        DB::table('rec_candidate_assign')->where('id', $row->id)->update(['ac_status' => 2]);

                    }

                }

            }
        }

    }
    /** End candidate request after valid date */

    /** Start Jobs after valid date */
    public function jobAutoExpiredAfterValidDate()
    {
        $end_date = date('Y-m-d H:i:s');
        // $jobsCount = DB::table('jobs')->where('status','=','active')->where('end_date', '<',$end_date)->pluck('jobs.id')->toArray();

        $end_date = date('Y-m-d'); // or your existing $end_date

        $params = [
            'index' => ELASTIC_JOBS,
            'size' => 10000, // ES max result window
            '_source' => ['id'], // fetch only job ID
            'body' => [
                'query' => [
                    'bool' => [
                        'must' => [
                            [
                                'term' => [
                                    'status' => 'active',
                                ],
                            ],
                            [
                                'range' => [
                                    'end_date' => [
                                        'lt' => $end_date,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $response = $this->elasticService->getClient()->search($params);

        $jobsCount = [];

        if (! empty($response['hits']['hits'])) {
            foreach ($response['hits']['hits'] as $hit) {
                if (isset($hit['_source']['id'])) {
                    $jobsCount[] = $hit['_source']['id'];
                }
            }
        }

        // dd($jobsCount);

        // dd($jobsCount,$end_date);

        if (count(@$jobsCount) > 0) {
            $valid_day = 0;
            $valid_date = date('Y-m-d H:i:s', strtotime("-{$valid_day} days"));

            // dd($valid_date);

            $candidateRequCount = DB::table('rec_candidate_assign')->where('created_at', '<', $valid_date)->where('ac_status', '=', 0)->get();

            DB::table('jobs')->whereIn('id', $jobsCount)->where('status', '=', 'active')->update(['status' => 'inactive', 'closeType' => 1, 'public_market_status' => 0]);
            DB::table('recommendation_relation')->whereIn('job_id', $jobsCount)->where('final_status', '=', 1)->update(['final_status' => 2]);

            foreach ($jobsCount as $jobid) {

                $m_n_id = 43;

                $checkMailPermision = GeneralMethod::mailPermission($m_n_id);

                if ($checkMailPermision && $m_n_id > 0) {
                    $job = Job::withoutGlobalScopes()->find($jobid);
                    $main_user = DB::table('users')->where('users.root_user', 1)->where('company_id', $job->company_id)->first();

                    $senderName = ($main_user) ? $main_user->name : '';
                    $fromAddress = ($main_user) ? $main_user->email : '';

                    $company_id = $job->company_id;
                    $mailTypeSubject = GeneralMethod::mailTypeSubject($m_n_id, 1);
                    $notificationemail = GeneralMethod::notification($company_id, 'Recuitment');
                    $currentJobId = $jobid;
                    $currentUserId = $main_user->id;
                    $max_salary = $min_salary = $max_sal_mode = $min_sal_mode = '';

                    $employmentType = GeneralMethod::employmentType();
                    $employment_type = (int) $job->employment_type;
                    $employment_type = $employmentType[$employment_type];
                    $currency = GeneralMethod::currencySymbole();
                    $currencyGet = GeneralMethod::currencySymboleforexcell();
                    $salaryMode = GeneralMethod::salaryMode();

                    $recruiter = DB::table('users')
                        ->join('job_assign', 'job_assign.user_id', '=', 'users.id')
                        ->where('job_assign.job_id', $currentJobId)->pluck('users.name')->toArray();

                    $recruiterdata = (implode(',', $recruiter));

                    $hiring_namager = DB::table('client_contact_info')
                        ->join('client_contact_info_jobs', 'client_contact_info_jobs.client_contact_info_id', '=', 'client_contact_info.id')
                        ->where('client_contact_info_jobs.job_id', $currentJobId)->pluck('client_contact_info.contact_person_name')->toArray();
                    $hiring_namagerdata = (implode(',', $hiring_namager));

                    $strAdded = DB::table('clients')->select('companies.company_name', 'clients.name', 'clients.self_company')->join('companies', 'companies.id', '=', 'clients.company_id')->where('clients.id', $job->client_id)->first();

                    $contact_person_name = '';
                    if ($strAdded) {
                        if (@$strAdded->self_company == 1) {
                            $contact_person_name = @$strAdded->company_name;
                        } else {
                            $contact_person_name = @$strAdded->company_name;
                            if ($company_id == $job->company_id) {
                                $contact_person_name = @$strAdded->name;
                            }

                        }
                    }

                    // dd($strAdded);

                    $jobAdditional = Job::Select('clients.name as client_name', 'clients.description as client_description', 'clients.logo_logical_path', 'job_categories.name as cat_name', 'qualification_master.name as qualification_name')
                        ->leftJoin('clients', 'clients.id', '=', 'jobs.client_id')
                        ->leftJoin('job_categories', 'job_categories.id', '=', 'jobs.category_id')
                        ->leftJoin('qualification_master', 'qualification_master.id', '=', 'jobs.qualification')
                        ->withoutGlobalScopes()->find($jobid);

                    // dd($jobAdditional,$job->id);

                    $assignTeamemail = DB::table('job_assign')
                        ->join('users', 'users.id', '=', 'job_assign.user_id')
                        ->where('users.company_id', $company_id)
                        ->where('job_assign.job_id', $job->id)
                        ->pluck('users.email')->toArray();

                    $cc = $assignTeamemail;
                    $attachments = [];

                    $oprnlink = url('/service_company/apptracker/clientapp/'.GeneralMethod::value_encrypt($job->id).'/all');

                    $subject = str_replace(['[{JOB_TITLE}]', '[{ClientCompany}]', '[{Location}]', '[{JobID}]'], [@$job->title, @$contact_person_name, 'R-'.@$job->id], $mailTypeSubject->subject);

                    $maillink = "You may access and manage the job posting using the link below:<br><br><a href='".$oprnlink."' style='color: #080feb; font-weight: bold;'>Click here to open the job</a> <br><br> If the above link does not open directly, you may copy and paste the below link it into your browser.<br><br><a href='".$oprnlink."' style='color: #080feb; font-weight: bold;'>".$oprnlink.'</a>';

                    if ($job->status == 'active') {
                        $status_job = "<strong><span style='color:#27ae60'>Hiring</span></strong>";
                    } elseif ($job->status == 'inactive') {
                        $status_job = "<strong><span style='color:#ff0202'>Closed</span></strong>";
                    } else {
                        $status_job = "<strong><span style='color:#ffab00'>Hold</span></strong>";
                    }

                    $template_description = str_replace(['[{status}]', '[{clientCompany}]', '[{job_id}]', '[{job_title}]', '[{experience}]', '[{salaryrange}]', '[{NoofOpenings}]', '[{location}]', '[{remote}]', '[{JobType}]', '[{Department}]', '[{Qualification}]', '[{hiringManager}]', '[{recruiter}]', '[{addedby}]', '[{JobDescriptions}]', '[{maillink}]'], [@$status_job, @$contact_person_name, ' '.@$job->id, @$job->title, @$job->min_exp.'-'.@$job->max_exp.' Years', $currencyGet[@$job->min_salary_currency].' '.@$job->min_salary.' to '.@$job->max_salary.' '.$max_sal_mode,  @$job->total_positions, @$client_loc->city_name, @$work_type, @$employment_type, $jobAdditional->cat_name, $jobAdditional->qualification_name, @$hiring_namagerdata, $recruiterdata, @$contact_person_name, @$job->job_description, $maillink], $mailTypeSubject->body_description);

                    $data = [
                        'message' => $template_description,
                    ];

                    $defailsEmail = DB::table('companies')->where('id', $company_id)->first()->company_email;
                    $to_Mail_Id = $notificationemail->email ?? $defailsEmail ?? '';
                    $teamsEmail = DB::table('users')->where('users.id', $currentUserId)->where('company_id', $company_id)->first();

                    QueueNotificationMailService::dispatch(
                        $teamsEmail->email,
                        $subject,
                        'email.common.index',
                        (object) $data,
                        $attachments,
                        $fromAddress,
                        $senderName,
                        $cc,
                        $company_id
                    )->onConnection('emails');
                }

                $job->status = 'inactive';
                $job->save();

                $exists_jobs = $this->elasticService->getClient()->exists([
                    'index' => ELASTIC_JOBS,
                    'id' => $jobid,
                ]);
                if ($exists_jobs) {
                    $updateParams = [
                        'index' => ELASTIC_JOBS,
                        'id' => $jobid,
                        'body' => [
                            'doc' => [
                                'status' => 'inactive',
                            ],
                            'doc_as_upsert' => false, // Changed to false since we verified existence
                        ],
                        'refresh' => true, // Force immediate refresh
                    ];
                    $this->elasticService->getClient()->update($updateParams);

                }
            }

            return 1;
        }

        return 2;

    }
    /** End jobs after valid date */

    /** Start user removed after valid date */
    public function userRemoved()
    {
        $valid_day = 2;
        $valid_date = date('Y-m-d H:i:s', strtotime("-{$valid_day} days"));
        $users = DB::table('users')->where('created_at', '<', $valid_date)->where('status', '=', 'inactive')->where('root_user', '=', 0)->where('password', '=', '')->get();

        if ($users) {
            foreach ($users as $row) {
                $exists = $this->elasticService->getClient()->exists([
                    'index' => ELASTIC_USERS_LIST,
                    'id' => $row->id,
                ]);
                if ($exists) {

                    $updateTime = now()->toIso8601String();
                    $deleteParams = [
                        'index' => ELASTIC_USERS_LIST,
                        'id' => $row->id,
                        'refresh' => true, // Optional: force immediate visibility of deletion
                    ];
                    $this->elasticService->getClient()->delete($deleteParams);
                }
                DB::table('users')->where('id', $row->id)->delete();

            }

        }
    }
    /** End user removed after valid date */

    /** Start Offer Expired after 2 days (offer must be accepted within two days otherwise it should be considered expired) */
    public function offerExpired()
    {

        $now = Carbon::now();
        $nowIso = $now->toIso8601String();

        // Step 1: Search matching candidate documents in Elasticsearch
        $searchParams = [
            'index' => ELASTIC_CANDIDATES,
            'body' => [
                '_source' => ['id', 'candidate_id', 'full_name', 'email', 'company_id', 'submitted_by', 'of_offer_mode', 'of_offer_currency', 'of_offer_amount', 'of_joined_date', 'jobId'], // Ensure these fields are fetched
                'query' => [
                    'bool' => [
                        'must' => [
                            ['term' => ['of_offer_acceptance' => 1]],
                            ['term' => ['job_master_status_id' => 5]],
                        ],
                        'filter' => [
                            [
                                'range' => [
                                    'send_accept_date_time' => [
                                        'lte' => $now->subDays(4)->toIso8601String(),
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'size' => 10000, // Adjust based on your expected match volume
            ],
        ];

        $results = $this->elasticService->getClient()->search($searchParams);
        $hits = $results['hits']['hits'] ?? [];

        $data = array_map(function ($hit) {
            return $hit['_source'];
        }, $results['hits']['hits']);

        if (empty($hits)) {
            return response()->json(['message' => 'No records to update']);
        }

        // Collect IDs and candidate_ids
        $candidateIds = [];
        $docIds = [];
        $jobIds = [];

        foreach ($hits as $hit) {
            $docIds[] = $hit['_id'];
            $candidateIds[] = $hit['_source']['candidate_id'] ?? null;
        }

        $candidateIds = array_filter(array_unique($candidateIds));

        // dd($candidateIds,$hits,$data);

        // Step 2: Update Elasticsearch
        $updateParams = [
            'index' => ELASTIC_CANDIDATES,
            'body' => [
                'script' => [
                    'source' => '
                        ctx._source.of_offer_acceptance = 4;
                        ctx._source.of_expired_date = params.now;
                    ',
                    'lang' => 'painless',
                    'params' => [
                        'now' => $nowIso,
                    ],
                ],
                'query' => [
                    'ids' => [
                        'values' => $docIds,
                    ],
                ],
            ],
        ];

        $this->elasticService->getClient()->updateByQuery($updateParams);

        foreach ($data as $rowArr) {
            $row = (object) $rowArr;
            $candidateId = $row->candidate_id;
            DB::table('updatetimeline')->insert([
                'event_id' => 10,
                'event_by_company_id' => $row->company_id,
                'event_by_user_id' => $row->submitted_by,
                'master_process_name' => 'Expired',
                'application_id' => $candidateId,
                'application_email' => $row->email,
                'job_id' => $row->jobId,
                'offer_amount' => $row->of_offer_amount,
                'joined_date' => date('Y-m-d H:i:s', strtotime($row->of_joined_date)),
                'candidate_status' => 4,
                'mtypeSection' => 4,
            ]);
        }

        return 1;

    }
    /** End Offer Expired after 2 days (offer must be accepted within two days otherwise it should be considered expired)*/

    // ** Cron end here */
    public function skillSuggestions(Request $request)
    {

        if (! $request->has('q')) {
            return response()->json(['error' => 'No query provided'], 400);
        }
        $query = $request->input('q');

        $queryArr = array_map('trim', explode(',', $query));
        if (count($queryArr) > 1) {
            $query = $queryArr;
        }

        $shouldClauses = [];

        foreach ($queryArr as $term) {
            $shouldClauses[] = [
                'wildcard' => [
                    'skill_name' => '*'.strtolower(trim($term)).'*',
                ],
            ];
        }
        $params = [
            'index' => ELASTIC_SKILLS,
            'body' => [
                'size' => 100,
                'sort' => [
                    ['skill_name' => 'asc'],
                ],
                'query' => [
                    'bool' => [
                        'should' => $shouldClauses,
                        'minimum_should_match' => 1,
                    ],
                ],
            ],
        ];

        $response = $this->elasticService->getClient()->search($params);

        $cities = collect($response['hits']['hits'])->map(function ($hit) {
            $src = $hit['_source'];

            return ucwords($src['skill_name']);
        })->unique()
            ->values();

        return ($cities) ? $cities : '';

    }

    public function skillSuggestions_old(Request $request)
    {
        $keyword = strtolower(trim($request->input('q')));
        $skillsPath = public_path('skills/skills.json');
        $skillsJson = file_get_contents($skillsPath);
        $skills = json_decode($skillsJson, true);

        $suggestions = [];

        // Search for exact key match first
        foreach ($skills as $skill => $related) {
            if (strtolower($skill) === $keyword) {
                $suggestions = array_merge([$skill], $related);
                break;
            }
        }

        // If still no match, check if keyword is inside related values
        if (empty($suggestions)) {
            foreach ($skills as $skill => $related) {
                foreach ($related as $rel) {
                    if (stripos($rel, $keyword) !== false) {
                        $suggestions = array_merge([$rel], $skills[$rel] ?? [$skill]);
                        break 2;
                    }
                }
            }
        }

        return response()->json(array_values(array_unique($suggestions)));

    }

    public function citySuggestions(Request $request)
    {
        if (! $request->has('q')) {
            return response()->json(['error' => 'No query provided'], 400);
        }
        $query = $request->input('q');

        $queryArr = array_map('trim', explode(',', $query));
        if (count($queryArr) > 1) {
            $query = $queryArr;
        }

        $shouldClauses = [];

        foreach ($queryArr as $term) {
            $shouldClauses[] = [
                'wildcard' => [
                    'city_name' => '*'.strtolower($term).'*',
                ],
            ];
        }
        $params = [
            'index' => ELASTIC_LOCATIONS,
            'body' => [
                'size' => 100,
                'sort' => [
                    ['city_name' => 'asc'],
                ],
                'query' => [
                    'bool' => [
                        'should' => $shouldClauses,
                        'minimum_should_match' => 1,
                    ],
                ],
            ],
        ];

        $response = $this->elasticService->getClient()->search($params);

        $cities = collect($response['hits']['hits'])->map(function ($hit) {
            $src = $hit['_source'];

            return ucwords($src['city_name']).' | '.ucwords($src['state_name']).' | '.ucwords($src['country_name']);
        });

        return ($cities) ? $cities : '';

        // dd($queryArr);

        $cities = DB::table('city_list')
            ->join('state_list', 'state_list.id', '=', 'city_list.state_id')
            ->join('countries', 'countries.id', '=', 'state_list.country_id')
            ->select('city_list.city as city_name', 'state_list.state as state_name', 'countries.country_name');
        if (count($queryArr) > 1) {
            $cities = $cities->where(function ($queryBuilder) use ($queryArr) {
                foreach ($queryArr as $q) {
                    $queryBuilder->orWhere('city_list.city', 'LIKE', "%$q%");
                }
            });
        } else {
            $cities = $cities->where('city_list.city', 'LIKE', "%$query%");
        }

        $cities = $cities->where('city_list.city', '!=', '')
            ->orderBy('city_list.city')
            ->limit(10)
            ->cursor()
            ->map(function ($city) {
                return $city->city_name.' | '.$city->state_name.' | '.$city->country_name;
            });

        return ($cities) ? $cities : '';

    }

    public function getStates(Request $request)
    {
        $countryId = $request->query('country');

        $states = DB::table('state_list')->where('country_id', $countryId)->get(['id', 'state as name']);

        // dd( $states);
        return response()->json($states);
    }

    public function submit(Request $request)
    {

        if (! DB::table('billing_address_companies')->where('company_id', '=', $request->company_id)->exists()) {
            DB::table('billing_address_companies')->insert([
                'company_id' => $request->company_id,
                'country_id' => $request->country,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'phone' => $request->phone,
                'building' => $request->building,
                'street' => $request->street,
                'state_id' => $request->state_id,
                'city' => $request->city,
                'postal_code' => $request->postal_code,
                'organization' => $request->organization,
                'pan_no' => $request->pan_no,
                'gstin' => $request->gstin,
            ]);
        } else {
            DB::table('billing_address_companies')->where('company_id', '=', $request->company_id)->update([
                'company_id' => $request->company_id,
                'country_id' => $request->country,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'phone' => $request->phone,
                'building' => $request->building,
                'street' => $request->street,
                'state_id' => $request->state_id,
                'city' => $request->city,
                'postal_code' => $request->postal_code,
                'organization' => $request->organization,
                'pan_no' => $request->pan_no,
                'gstin' => $request->gstin,
            ]);
        }

        return response()->json([
            'message' => 'Form submitted successfully!',
            'usernamebill' => $request->first_name.' '.$request->last_name,
            'street' => $request->street,
            'billing_gst' => $request->gstin,
            'billing_pan' => $request->pan_no,
        ]);
    }

    public function getCities(Request $request)
    {
        $stateId = $request->query('state');

        $cities = DB::table('city_list')->where('state_id', $stateId)->get(['city_id as id', 'city as name']);

        return response()->json($cities);
    }

    public function offerPreviewSignature($Id, $mtype, $email, $fromemail)
    {

        $offerId = decrypt($Id); // $request->offerId;
        $mtypeSection = decrypt($mtype); // $request->offerId;
        $emailSet = decrypt($email); // $request->offerId;
        $from_email = decrypt($fromemail); // $request->offerId;

        $company_id = ''; // auth()->user()->company_id;
        // dd( $company_id);
        if ($mtypeSection == 1) {

            $offer_create_main_tds = DB::table('offer_create_main_tds')
                ->join('offer_job_applications', 'offer_job_applications.id', '=', 'offer_create_main_tds.offer_id')
                ->select('offer_create_main_tds.*')
                ->where('offer_create_main_tds.candidate_status', '=', 1)
                ->where('offer_create_main_tds.status', 1)
                ->where('offer_job_applications.id', '=', $offerId)
                ->get();
            // dd($offer_create_main_tds);
            if ($offer_create_main_tds) {
                foreach ($offer_create_main_tds as $offer_create_main) {
                    $createdAt = Carbon::parse($offer_create_main->updated_at);
                    $currentTime = Carbon::now();

                    // dd($currentTime->diffInHours($createdAt));

                    if ($currentTime->diffInHours($createdAt) >= 72) {
                        // dd($offer_create_main->id);
                        // Update the status after 72 hours
                        DB::table('offer_create_main_tds')
                            ->where('id', $offer_create_main->id)
                            ->update(['candidate_status' => 4]); // Assuming 0 is the updated status
                    }
                }
            }
        }

        $exits_offer = DB::table('offer_create_main_tds')->where('offer_id', '=', $offerId)->where('status', '=', 1)->orderByDesc('id')->first();
        if ($exits_offer) {

            $offer_terms_conditions = DB::table('offer_terms_conditions')->where('offer_id', '=', $offerId)->orderByDesc('id')->first();
            $offerDetails = DB::table('offer_create_main_tds')
                ->join('offer_job_applications', 'offer_create_main_tds.offer_id', '=', 'offer_job_applications.id')
                ->where('offer_job_applications.id', $offerId)
                ->where('offer_create_main_tds.status', '=', 1)
                ->select(
                    'offer_create_main_tds.offer_id',
                    'offer_create_main_tds.employment_type',
                    'offer_create_main_tds.id as of_main_td',
                    'offer_create_main_tds.currency',
                    'offer_create_main_tds.salary_ctc',
                    'offer_create_main_tds.accept_date_time',
                    'offer_create_main_tds.signature_candidate',
                    'offer_create_main_tds.salary_mode',
                    'offer_create_main_tds.designation',
                    'offer_create_main_tds.joined_date',
                    'offer_create_main_tds.monthly_gross',
                    'offer_create_main_tds.yearly_gross',
                    'offer_create_main_tds.monthly_bounus_gross',
                    'offer_create_main_tds.yearly_bonus_gross',
                    'offer_create_main_tds.monthly_insurence_gross',
                    'offer_create_main_tds.yearly_insurence_gross',
                    'offer_create_main_tds.yearly_final_gross',
                    'offer_create_main_tds.monthly_final_gross'
                )->get()
                ->map(function ($offer) {
                    // Fetch Basic offer points
                    $basicPoints = DB::table('basic_offer_points')
                        ->where('offer_create_main_td_id', $offer->of_main_td)
                        ->select(
                            'id',
                            'poins as basic_poins',
                            'Percentage as basic_percentage',
                            'condition_s as basic_condition_s',
                            'monthly as basic_monthly',
                            'yearly as basic_yearly'
                        )->get()->toArray();

                    // Fetch Bonus offer points
                    $bonusPoints = DB::table('bonus_offer_points')
                        ->where('offer_create_main_td_id', $offer->of_main_td)
                        ->select(
                            'id',
                            'poins as bonus_poins',
                            'Percentage as bonus_percentage',
                            'condition_s as bonus_condition_s',
                            'monthly as bonus_monthly',
                            'yearly as bonus_yearly'
                        )->get()->toArray();

                    // Fetch Insurance offer points
                    $insurancePoints = DB::table('insurence_offer_points')
                        ->where('offer_create_main_td_id', $offer->of_main_td)
                        ->select(
                            'id',
                            'poins as insurence_poins',
                            'Percentage as insurence_percentage',
                            'condition_s as insurence_condition_s',
                            'monthly as insurence_monthly',
                            'yearly as insurence_yearly'
                        )->get()->toArray();

                    // Merge all into the main offer array
                    return [
                        'offer_id' => $offer->offer_id,
                        'employment_type' => $offer->employment_type,
                        'of_main_td' => $offer->of_main_td,
                        'accept_date_time' => $offer->accept_date_time,
                        'signature_candidate' => $offer->signature_candidate,
                        'currency' => $offer->currency,
                        'salary_ctc' => $offer->salary_ctc,
                        'salary_mode' => $offer->salary_mode,
                        'designation' => $offer->designation,
                        'joined_date' => $offer->joined_date,
                        'monthly_gross' => $offer->monthly_gross,
                        'yearly_gross' => $offer->yearly_gross,
                        'monthly_bounus_gross' => $offer->monthly_bounus_gross,
                        'yearly_bonus_gross' => $offer->yearly_bonus_gross,
                        'monthly_insurence_gross' => $offer->monthly_insurence_gross,
                        'yearly_insurence_gross' => $offer->yearly_insurence_gross,
                        'yearly_final_gross' => $offer->yearly_final_gross,
                        'monthly_final_gross' => $offer->monthly_final_gross,
                        'Basic_offer_points' => $basicPoints,
                        'Bonus_offer_points' => $bonusPoints,
                        'Insurance_offer_points' => $insurancePoints,
                    ];
                });

        }

        $rowDataOfferDetails = GeneralMethod::offerLetterdetails($offerId);

        $address = (@$rowDataOfferDetails) ? @$rowDataOfferDetails->address : '';
        $comapny_name = (@$rowDataOfferDetails) ? @$rowDataOfferDetails->comapny_name : '';
        $logo = (@$rowDataOfferDetails) ? @$rowDataOfferDetails->logo : '';
        $job_title = (@$rowDataOfferDetails) ? @$rowDataOfferDetails->job_title : '';
        $candidate_name = (@$rowDataOfferDetails) ? @$rowDataOfferDetails->full_name : '';
        $candidate_email = (@$rowDataOfferDetails) ? @$rowDataOfferDetails->email : '';
        $job_id = (@$rowDataOfferDetails) ? @$rowDataOfferDetails->job_id : '';
        $application_id = (@$rowDataOfferDetails) ? @$rowDataOfferDetails->application_id : '';

        //    dd($locArr);

        return view('signature-pad', compact('offerDetails', 'offer_terms_conditions', 'offerId', 'exits_offer', 'address', 'comapny_name', 'logo', 'job_title', 'candidate_name', 'mtypeSection', 'application_id', 'job_id', 'emailSet', 'from_email'));
    }

    public function candidateMailResponse($wise_condidateId, $mtype, $email, $to_email, $subject, $userId, $companyId, $insertId)
    {

        $wise_condidateId = decrypt($wise_condidateId); // $request->offerId;
        $mtypeSection = decrypt($mtype); // $request->offerId;
        $emailSet = decrypt($email); // $request->offerId;
        $to_email = decrypt($to_email); // $request->offerId;
        $subject = decrypt($subject); // $request->offerId;
        $userId = decrypt($userId); // $request->offerId;
        $companyId = decrypt($companyId); // $request->offerId;
        $insertId = decrypt($insertId); // $request->offerId;

        if (DB::table('sendmailforcandidates')->where('id', '=', $insertId)->where('status', '=', 1)->exists()) {
            $interestedCandidates = 0;
            $text_ap = '';
            $notInterestedCandidates = 0;
            if ($mtypeSection == 1) {
                $interestedCandidates = 2;
                $text_ap = 'Interested';
            } else {
                $notInterestedCandidates = -1;
                $text_ap = 'Not Interested';
            }

            $AddJobWiseCondidate = AddJobWiseCondidate::withoutGlobalScopes()->find($wise_condidateId);
            $params = [
                'index' => ELASTIC_CANDIDATES,
                'id' => $wise_condidateId,
            ];

            $response = $this->elasticService->getClient()->get($params);
            $data = (object) $response['_source'];

            $jobid = $AddJobWiseCondidate->job_id;
            $jobs = Job::withoutGlobalScopes()->find($jobid);

            $companyOrClientName = GeneralMethod::companyOrClientName($jobs->client_id);

            $teamRankElastic = [
                'company_id' => $AddJobWiseCondidate->company_id,
                'user_id' => $AddJobWiseCondidate->submitted_by,
                'candidate_id' => $AddJobWiseCondidate->id,
                'profileUpload' => 0,
                'interviewScheduled' => 0,
                'interviewNoShow' => 0,
                'offerReleased' => 0,
                'candidateJoined' => 0,
                'jobsPosted' => 0,
                'feedbackGivenOnCVs' => 0,
                'postJobToVendorMarket' => 0,
                'outsourceJobsToVendors' => 0,
                'listBenchCandidates' => 0,
                'respondToBenchRequests' => 0,
                'interestedCandidates' => $interestedCandidates,
                'notInterestedCandidates' => $notInterestedCandidates,
            ];
            GeneralMethod::teamRankElastic($teamRankElastic);

            $timelineArr = [
                'event_id' => 11,
                'event_by_company_id' => $companyId,
                'event_by_user_id' => $userId,
                'master_process_name' => 'SendMail',
                'application_id' => $AddJobWiseCondidate->job_application_id,
                'application_email' => $to_email,
                'job_id' => $AddJobWiseCondidate->job_id,
                'comment' => $subject,
                'note' => $text_ap,
            ];
            GeneralMethod::UpdateTimeline($timelineArr);

            $dataMail = [
                'status' => 0, // or whatever status you want
                'updated_at' => now(),
            ];

            $message = '';

            $userDetails = DB::table('users')
                ->select('users.name', 'users.email', 'users.company_id', 'users.id')
                ->where('email', '=', $emailSet)
                ->where('root_user', '=', 1)
                ->first();

            $senderName = $userDetails->name;
            $fromAddress = $userDetails->email;
            $company_id = $userDetails->company_id;

            $email_notifications = DB::table('email_notifications')->where('id', 46)->where('status', 1)->first();

            $subject = str_replace(['[Status]', '[clientCompanyName]', '[title]', '[jobId]'], [$text_ap, $companyOrClientName->company, $jobs->title, $jobs->id], $email_notifications->subject);

            $tracker_html = str_replace(['[Status]', '[RecipientName]', '[CandidateName]', '[CandidateEmail]', '[clientCompanyName]', '[title]', '[jobId]'], [$text_ap, $senderName, $data->full_name, $data->email, $companyOrClientName->company, $jobs->title, $jobs->id], @$email_notifications->body_description);

            $data = ['message' => $tracker_html, 'subject' => $subject, 'company_name' => '', 'consultant_name' => '', 'position' => '', 'condidate_name' => ''];

            $attachments = [];
            $cc = [];

            QueueNotificationMailService::dispatch(
                $emailSet,
                $subject,
                'email.resume-forward.sendmail',
                (object) $data,
                $attachments,
                $fromAddress,
                $senderName,
                $cc,
                $company_id
            )->onConnection('emails');

            DB::table('sendmailforcandidates')->where('id', '=', $insertId)->update($dataMail);

            return view('candidate_mail_response', compact('mtypeSection'));

        } else {
            $mtypeSection = 3;

            return view('candidate_mail_response', compact('mtypeSection'));
        }

        // dd($wise_condidateId,$mtypeSection,$emailSet,$to_email,$subject,$userId,$companyId,$insertId);

    }

    public function saveSignature(Request $request)
    {
        $dataURL = $request->input('signature');
        // return back()->with('success', 'Signature saved successfully!');
        // dd($request->all());
        $offerId = @$request->offerid;
        $emailSet = @$request->emailSet;
        $email_id_hr = $emailSet;
        $name_hr = '';
        $name_phone = '';
        $mtypeId = $request->mtypeSection;
        $from_email_return = $request->from_email;
        $accept_date_time = '';
        $approved_date_time = '';
        $event_id = 0;
        // dd( $emailSet);
        $signatures = '';
        $comment_box = @$request->comment_box;
        $rowDataOfferDetails = GeneralMethod::offerLetterdetails($offerId);

        $address = (@$rowDataOfferDetails) ? @$rowDataOfferDetails->address : '';
        $comapny_name = (@$rowDataOfferDetails) ? @$rowDataOfferDetails->comapny_name : '';
        $logo = (@$rowDataOfferDetails) ? @$rowDataOfferDetails->logo : '';
        $job_title = (@$rowDataOfferDetails) ? @$rowDataOfferDetails->job_title : '';
        $candidate_name = (@$rowDataOfferDetails) ? @$rowDataOfferDetails->full_name : '';
        $candidate_email = (@$rowDataOfferDetails) ? @$rowDataOfferDetails->email : '';
        $job_id = (@$rowDataOfferDetails) ? @$rowDataOfferDetails->job_id : '';
        $application_id = (@$rowDataOfferDetails) ? @$rowDataOfferDetails->application_id : '';
        $company_id = (@$rowDataOfferDetails) ? @$rowDataOfferDetails->company_id : '';
        $detailsVendorSide = GeneralMethod::companyAsVendor($company_id, $company_id);
        $status = '';
        $update_status = '';
        $exits_offer = DB::table('offer_create_main_tds')->where('offer_id', '=', $offerId)->where('status', '=', 1)->orderByDesc('id')->first();
        $users = DB::table('client_contact_info')->where('contact_person_email', '=', $emailSet)->orderByDesc('id')->first();

        $offer_job_applications = DB::table('offer_job_applications')->select('joined')->where('id', '=', $offerId)->first();

        $addJobWiseCondidate_id = ($offer_job_applications->joined) ? $offer_job_applications->joined : 0;

        $rowDataOfferDetails = GeneralMethod::offerLetterdetails($offerId);

        $senderName = @$rowDataOfferDetails->username;
        $fromAddress = @$rowDataOfferDetails->useremail;

        if ($request->mtypeSection == 1) {
            $status = decrypt($request->status_value);
            if ($request->signature_type == 1) {

                // Extract MIME type
                if (preg_match('/^data:image\/(\w+);base64,/', $dataURL, $type)) {
                    $extension = strtolower($type[1]); // png, jpeg, jpg
                    $dataURL = substr($dataURL, strpos($dataURL, ',') + 1);
                } else {
                    return back()->with('error', 'Invalid image format');
                }

                // Validate extension
                if (! in_array($extension, ['png', 'jpg', 'jpeg'])) {
                    return back()->with('error', 'Only PNG or JPG images allowed');
                }

                // Decode base64
                $image = base64_decode($dataURL);

                if ($image === false) {
                    return back()->with('error', 'Base64 decode failed');
                }

                // Generate filename
                $signatures = uniqid().'_'.$request->offerid.'.'.$extension;
                $fileName = 'signatures/'.$signatures;

                // Save file
                Storage::disk('public')->put($fileName, $image);

                // // Remove "data:image/png;base64," from the dataURL to get only the base64 content
                // $base64Image = str_replace('data:image/png;base64,', '', $dataURL);
                // $base64Image = str_replace(' ', '+', $base64Image);

                // // Decode the image
                // $image = base64_decode($base64Image);

                // // Define a unique filename
                // $signatures = uniqid().$request->offerid.".png";
                // $fileName = 'signatures/' . $signatures;
                // // Save the file in the 'public/signatures' directory
                // Storage::disk('public')->put($fileName, $image);

                DB::table('offer_create_main_tds')->where('offer_id', $offerId)->where('status', '=', 1)->update(['signature_candidate' => @$signatures, 'accept_date_time' => date('Y-m-d H:i:s'), 'candidate_status' => $status]);

            }

            $this->mail_send_offer_download($offerId);

            if ($status == 2) {
                $filename = 'offer-letter-'.$offerId.'.pdf';
                // Use disk path, not URL — MailService reads the file directly from disk.
                // asset()/url() produce URLs that are unreliable in queue workers and when
                // APP_URL doesn't match the actual server host.
                $path = storage_path('app/public/'.$filename);

                $attachments[] = [
                    'path' => $path,
                    'options' => [],
                ];

                $notificationId = 30;
                $notificationType = 6;

                $cc = [];

                // dd($cc);

                $mailTypeSubject = GeneralMethod::mailTypeSubject(31, 10);

                $subject = str_replace(['[{CANDIDATE_NAME}]', '[{JOB_TITLE}]', '[{COMPANY_NAME}]', '[{JOB_ID}]'], [$candidate_name, $job_title, $comapny_name, $job_id], $mailTypeSubject->subject);
                $template_description = $mailTypeSubject->body_description;

                $template_description = str_replace(['[{CANDIDATE_NAME}]', '[{COMPANY_NAME}]', '[{JOB_TITLE}]', '[{JOB_ID}]', '[{HR_NAME}]', '[{HR_EMAIL}]', '[{HR_PHONE}]', '[{COMMENTS}]'], [$candidate_name, $comapny_name, $job_title, $job_id, $detailsVendorSide->client_name, $detailsVendorSide->client_email, $detailsVendorSide->client_phone, $comment_box], $template_description);

                $mailto = $detailsVendorSide->client_email;

                $message = $template_description;
                $data = ['message' => $message, 'subject' => $subject, 'company_name' => '4Bell Technology', 'consultant_name' => '4Bell Technology', 'position' => 'PHP'];

                // QueueNotificationMailService
                QueueMailService::dispatch(
                    $from_email_return,
                    $subject,
                    'email.common.index',
                    $data,
                    $attachments,
                    $fromAddress,
                    $senderName,
                    $cc,
                    $company_id
                )->onConnection('emails');

                $mailTypeSubject = GeneralMethod::mailTypeSubject(30, 6);

                $subject = str_replace(['[{CANDIDATE_NAME}]', '[{JOB_TITLE}]', '[{COMPANY_NAME}]', '[{JOB_ID}]'], [$candidate_name, $job_title, $comapny_name, $job_id], $mailTypeSubject->subject);
                $template_description = $mailTypeSubject->body_description;

                $template_description = str_replace(['[{CANDIDATE_NAME}]', '[{COMPANY_NAME}]', '[{JOB_TITLE}]', '[{JOB_ID}]', '[{HR_NAME}]', '[{HR_EMAIL}]', '[{HR_PHONE}]', '[{COMMENTS}]'], [$candidate_name, $comapny_name, $job_title, $job_id, $detailsVendorSide->client_name, $detailsVendorSide->client_email, $detailsVendorSide->client_phone, $comment_box], $template_description);

                $mailto = $detailsVendorSide->client_email;

                $message = $template_description;
                $data = ['message' => $message, 'subject' => $subject, 'company_name' => '4Bell Technology', 'consultant_name' => '4Bell Technology', 'position' => 'PHP'];

                QueueMailService::dispatch(
                    $emailSet,
                    $subject,
                    'email.common.index',
                    $data,
                    $attachments,
                    $fromAddress,
                    $senderName,
                    $cc,
                    $company_id
                )->onConnection('emails');

                //    $this->mailService->sendEmail($mailto, $subject,'email.common.index', $data, $attachments, $fromAddress, $senderName,$cc);

                // ── Candidate offer-acceptance confirmation email ─────────────────────────
                // Duplicate guard: $exits_offer is the pre-update row. If candidate_status
                // was already 2 the offer was previously accepted and the confirmation was
                // already sent — skip to avoid spamming the candidate.
                // Acceptance remains saved even if this email dispatch throws.
                if (! empty($candidate_email) && @$exits_offer->candidate_status != 2) {
                    $candidatePdfPath = storage_path('app/public/offer-letter-'.$offerId.'.pdf');

                    // mail_send_offer_download() above already generated the PDF.
                    // Regenerate only if the file is somehow absent (edge case guard).
                    if (! file_exists($candidatePdfPath)) {
                        try {
                            $candidatePdf = app('dompdf.wrapper');
                            $candidatePdf->loadView('offer-letter', compact(
                                'offerDetails', 'offer_terms_conditions', 'offerId', 'exits_offer',
                                'address', 'comapny_name', 'logo', 'job_title', 'candidate_name'
                            ));
                            file_put_contents($candidatePdfPath, $candidatePdf->output());
                        } catch (\Throwable $pdfEx) {
                            \Log::error('OfferAcceptedMail: PDF regen failed for offer '.$offerId.': '.$pdfEx->getMessage());
                        }
                    }

                    $candidateAttachments = file_exists($candidatePdfPath)
                        ? [['path' => $candidatePdfPath, 'filename' => 'Offer-Letter.pdf', 'options' => []]]
                        : [];

                    $candidateSubject = 'Offer Acceptance Confirmation – '.$comapny_name;
                    $candidateBody = '<p>Dear '.htmlspecialchars($candidate_name).',</p>'
                        .'<p><strong>Congratulations!</strong></p>'
                        .'<p>We have successfully received your acceptance of the offer for the position of '
                        .'<strong>'.htmlspecialchars($job_title).'</strong> at <strong>'.htmlspecialchars($comapny_name).'</strong>.</p>'
                        .'<p>Please find your signed/final offer letter attached for your records.</p>'
                        .'<p>Our HR team will contact you regarding the next onboarding steps.</p>'
                        .'<p>We look forward to welcoming you to the team.</p>'
                        .'<p>Regards,<br><strong>'.htmlspecialchars($comapny_name).'</strong><br>HR Team</p>';

                    $candidateData = [
                        'message' => $candidateBody,
                        'subject' => $candidateSubject,
                        'company_name' => $comapny_name,
                        'consultant_name' => $comapny_name,
                        'position' => $job_title,
                    ];

                    try {
                        // Send synchronously — QueueMailService requires a running queue worker
                        // which is not guaranteed on all environments. Direct send ensures the
                        // candidate receives the confirmation immediately on acceptance.
                        $this->mailService->sendEmail(
                            $candidate_email,
                            $candidateSubject,
                            'email.common.index',
                            $candidateData,
                            $candidateAttachments,
                            $fromAddress,
                            $senderName,
                            [],
                            $company_id
                        );
                    } catch (\Throwable $mailEx) {
                        \Log::error('OfferAcceptedMail: send failed for offer '.$offerId.' to '.$candidate_email.': '.$mailEx->getMessage());
                    }
                }
                // ─────────────────────────────────────────────────────────────────────────

            } else {
                $attachments = [];
                $notificationId = 32;
                $notificationType = 7;
                $mailto = $detailsVendorSide->client_email;

            }
            $event_id = 7;
            $accept_date_time = date('Y-m-d H:i:s');
            DB::table('offer_create_main_tds')->where('offer_id', $offerId)->where('status', '=', 1)->update(['signature_candidate' => @$signatures, 'accept_date_time' => date('Y-m-d H:i:s'), 'candidate_status' => $status]);

            $exists = $this->elasticService->getClient()->exists([
                'index' => ELASTIC_CANDIDATES,
                'id' => $addJobWiseCondidate_id,
            ]);

            if ($exists) {

                // 2. Prepare the update timestamp
                $updateTime = now()->toIso8601String();
                // 3. Execute update with debugging
                $updateParams = [
                    'index' => ELASTIC_CANDIDATES,
                    'id' => $addJobWiseCondidate_id,
                    'body' => [
                        'doc' => [
                            'of_offer_acceptance' => (int) $status,
                            'accept_date_time' => $updateTime,
                            'updated_at' => $updateTime,
                        ],
                        'doc_as_upsert' => false, // Changed to false since we verified existence
                    ],
                    'refresh' => true, // Force immediate refresh
                ];

                $this->elasticService->getClient()->update($updateParams);

                DB::table('offer_job_applications')->where('id', $offerId)->update(['offer_acceptance' => $status]);

            }

        } else {

            $email_id_hr = (@$users) ? $emailSet : $emailSet;
            $name_hr = (@$users) ? $users->contact_person_name : '';
            $name_phone = (@$users) ? $users->contact_person_number : '';
            $status = decrypt($request->status_value);
            if ($status == 2) {

                // Remove "data:image/png;base64," from the dataURL to get only the base64 content
                $update_status = 2;
                $notificationId = 33;
                $notificationType = 8;
                $mailto = $detailsVendorSide->client_email;
            } else {
                $update_status = 3;
                $notificationId = 34;
                $notificationType = 9;
                $mailto = $detailsVendorSide->client_email;
            }
            $event_id = 8;
            $attachments = [];
            $approved_date_time = date('Y-m-d H:i:s');
            DB::table('offer_create_main_tds')->where('offer_id', @$request->offerid)->where('status', '=', 1)->update(['approved_date_time' => date('Y-m-d H:i:s'), 'management_status' => $update_status]);

            $exists = $this->elasticService->getClient()->exists([
                'index' => ELASTIC_CANDIDATES,
                'id' => $addJobWiseCondidate_id,
            ]);

            if ($exists) {

                // 2. Prepare the update timestamp
                $updateTime = now()->toIso8601String();
                // 3. Execute update with debugging
                $updateParams = [
                    'index' => ELASTIC_CANDIDATES,
                    'id' => $addJobWiseCondidate_id,
                    'body' => [
                        'doc' => [
                            'of_offer_approved' => (int) $status,
                            'approved_date_time' => $updateTime,
                            'updated_at' => $updateTime,
                        ],
                        'doc_as_upsert' => false, // Changed to false since we verified existence
                    ],
                    'refresh' => true, // Force immediate refresh
                ];

                $this->elasticService->getClient()->update($updateParams);

                DB::table('offer_job_applications')->where('id', $offerId)->update(['offer_acceptance' => $status]);

            }

            $cc = [];

            $mailTypeSubject = GeneralMethod::mailTypeSubject($notificationId, $notificationType);
            $subject = str_replace(['[{CANDIDATE_NAME}]', '[{JOB_TITLE}]', '[{COMPANY_NAME}]', '[{JOB_ID}]'], [$candidate_name, $job_title, $comapny_name, $job_id], $mailTypeSubject->subject);
            $template_description = $mailTypeSubject->body_description;

            $template_description = str_replace(['[{CANDIDATE_NAME}]', '[{COMPANY_NAME}]', '[{JOB_TITLE}]', '[{JOB_ID}]', '[{HR_NAME}]', '[{HR_EMAIL}]', '[{HR_PHONE}]', '[{COMMENTS}]'], [$candidate_name, $comapny_name, $job_title, $job_id, $name_hr, $email_id_hr, $name_phone, $comment_box], $template_description);

            $message = $template_description;
            $data = ['message' => $message, 'subject' => $subject, 'company_name' => '4Bell Technology', 'consultant_name' => '4Bell Technology', 'position' => 'PHP'];

            // $values['note'] = @$comment;
            // GeneralMethod::UpdateTimeline($values);

            // resources\views\email\common\index.blade.php
            QueueMailService::dispatch(
                $from_email_return,
                $subject,
                'email.common.index',
                $data,
                $attachments,
                $fromAddress,
                $senderName,
                $cc,
                $company_id
            )->onConnection('emails');

            // $result = $this->mailService->sendEmail($mailto, $subject,'email.common.index', $data, $attachments, $fromAddress, $senderName,$cc);

        }

        $Offertimelime = OfferJobApplication::withoutGlobalScopes()->find($offerId);

        $option_currency_val = @$request->current_ctc_cur;
        $joined_date = @$request->dob ? date('Y-m-d H:i:s', strtotime(@$request->dob)) : 'NULL';
        $ctc = @$request->ctc;
        $current_ctc_mode = @$request->current_ctc_mode;

        $Offertimelime->offer_currency = @$option_currency_val ? @$option_currency_val : 'NULL';
        $Offertimelime->offer_mode = @$current_ctc_mode ? @$current_ctc_mode : 'NULL';
        $Offertimelime->offer_amount = @$ctc ? @$ctc : 'NULL';
        $Offertimelime->joined_date = @$joined_date ? $joined_date : 'NULL';

        $date = date('Y-m-d H:i:s');

        $userDetails = DB::table('users')
            ->select('users.name', 'users.email', 'users.company_id', 'users.id')
            ->where('email', '=', $email_id_hr)
            ->first();

        $values = [
            'event_id' => $event_id,
            'event_by_company_id' => (@$userDetails) ? $userDetails->company_id : @$rowDataOfferDetails->usercompany_id,
            'event_by_user_id' => (@$userDetails) ? $userDetails->id : @$rowDataOfferDetails->user_id,

            'application_id' => @$Offertimelime->application_id ? @$Offertimelime->application_id : null,
            'application_email' => @$Offertimelime->email ? @$Offertimelime->email : null,
            'job_id' => @$Offertimelime->job_id ? @$Offertimelime->job_id : null,
            'note' => $comment_box,
            'create_at' => @$date ? @$date : null,
            'update_at' => @$date ? @$date : null,

        ];
        $jobApplication = AddJobWiseCondidate::withoutGlobalScopes()->find($Offertimelime->joined);
        if (@$jobApplication->master_status_id > 0) {
            $masterStatus = DB::table('master_status')->select('master_status.name')->where('id', $jobApplication->master_status_id)->first();
            $values['master_process_name'] = @$masterStatus->name;
        }
        if (@$jobApplication->child_status_id > 0) {
            $masterStatus = DB::table('candidate_status')->select('candidate_status.name')->where('candidate_status.id', $jobApplication->child_status_id)->first();
            $values['child_process_name'] = @$masterStatus->name;
        }

        $values['designation_offer'] = @$exits_offer->designation_offer;
        $values['offer_currency'] = @$exits_offer->currency;
        $values['offer_amount'] = @$exits_offer->salary_ctc;
        $values['joined_date'] = @$exits_offer->joined_date;
        $values['offer_mode'] = @$exits_offer->salary_mode;
        $values['candidate_status'] = ($request->mtypeSection == 1) ? $status : '';
        $values['management_status'] = ($request->mtypeSection == 2) ? $update_status : '';
        $values['accept_date_time'] = $accept_date_time;
        $values['approved_date_time'] = $approved_date_time;
        $values['email_id_hr'] = $email_id_hr;
        $values['name_hr'] = $name_hr;
        $values['yearly_gross'] = $exits_offer->yearly_gross;
        $values['yearly_final_gross'] = $exits_offer->yearly_final_gross;
        $values['mtypeSection'] = $mtypeId;
        GeneralMethod::UpdateTimeline($values);

        return back()->with('success', 'Signature saved successfully!');
    }

    public function mail_send_offer_download($offerId)
    {

        $coinnotdeduct = false;

        $exits_offer = DB::table('offer_create_main_tds')->where('offer_id', '=', $offerId)->where('status', '=', 1)->first();

        // dd($exits_offer);

        $rowDataOfferDetails = GeneralMethod::offerLetterdetails($offerId);

        $address = (@$rowDataOfferDetails) ? @$rowDataOfferDetails->address : '';
        $comapny_name = (@$rowDataOfferDetails) ? @$rowDataOfferDetails->comapny_name : '';
        $logo = (@$rowDataOfferDetails) ? @$rowDataOfferDetails->logo : '';
        $job_title = (@$rowDataOfferDetails) ? @$rowDataOfferDetails->job_title : '';
        $candidate_name = (@$rowDataOfferDetails) ? @$rowDataOfferDetails->full_name : '';

        if ($exits_offer) {

            $offer_terms_conditions = DB::table('offer_terms_conditions')->where('offer_id', '=', $offerId)->orderByDesc('id')->first();
            $offerDetails = DB::table('offer_create_main_tds')
                ->join('offer_job_applications', 'offer_create_main_tds.offer_id', '=', 'offer_job_applications.id')
                ->where('offer_job_applications.id', $offerId)
                ->where('offer_create_main_tds.status', '=', 1)
                ->select(
                    'offer_create_main_tds.offer_id',
                    'offer_create_main_tds.employment_type',
                    'offer_create_main_tds.id as of_main_td',
                    'offer_create_main_tds.currency',
                    'offer_create_main_tds.salary_ctc',
                    'offer_create_main_tds.accept_date_time',
                    'offer_create_main_tds.signature_candidate',
                    'offer_create_main_tds.salary_mode',
                    'offer_create_main_tds.designation',
                    'offer_create_main_tds.joined_date',
                    'offer_create_main_tds.monthly_gross',
                    'offer_create_main_tds.yearly_gross',
                    'offer_create_main_tds.monthly_bounus_gross',
                    'offer_create_main_tds.yearly_bonus_gross',
                    'offer_create_main_tds.monthly_insurence_gross',
                    'offer_create_main_tds.yearly_insurence_gross',
                    'offer_create_main_tds.yearly_final_gross',
                    'offer_create_main_tds.monthly_final_gross'
                )->get()
                ->map(function ($offer) {
                    // Fetch Basic offer points
                    $basicPoints = DB::table('basic_offer_points')
                        ->where('offer_create_main_td_id', $offer->of_main_td)
                        ->select(
                            'id',
                            'poins as basic_poins',
                            'Percentage as basic_percentage',
                            'condition_s as basic_condition_s',
                            'monthly as basic_monthly',
                            'yearly as basic_yearly'
                        )->get()->toArray();

                    // Fetch Bonus offer points
                    $bonusPoints = DB::table('bonus_offer_points')
                        ->where('offer_create_main_td_id', $offer->of_main_td)
                        ->select(
                            'id',
                            'poins as bonus_poins',
                            'Percentage as bonus_percentage',
                            'condition_s as bonus_condition_s',
                            'monthly as bonus_monthly',
                            'yearly as bonus_yearly'
                        )->get()->toArray();

                    // Fetch Insurance offer points
                    $insurancePoints = DB::table('insurence_offer_points')
                        ->where('offer_create_main_td_id', $offer->of_main_td)
                        ->select(
                            'id',
                            'poins as insurence_poins',
                            'Percentage as insurence_percentage',
                            'condition_s as insurence_condition_s',
                            'monthly as insurence_monthly',
                            'yearly as insurence_yearly'
                        )->get()->toArray();

                    // Merge all into the main offer array
                    return [
                        'offer_id' => $offer->offer_id,
                        'employment_type' => $offer->employment_type,
                        'of_main_td' => $offer->of_main_td,
                        'accept_date_time' => $offer->accept_date_time,
                        'signature_candidate' => $offer->signature_candidate,
                        'currency' => $offer->currency,
                        'salary_ctc' => $offer->salary_ctc,
                        'salary_mode' => $offer->salary_mode,
                        'designation' => $offer->designation,
                        'joined_date' => $offer->joined_date,
                        'monthly_gross' => $offer->monthly_gross,
                        'yearly_gross' => $offer->yearly_gross,
                        'monthly_bounus_gross' => $offer->monthly_bounus_gross,
                        'yearly_bonus_gross' => $offer->yearly_bonus_gross,
                        'monthly_insurence_gross' => $offer->monthly_insurence_gross,
                        'yearly_insurence_gross' => $offer->yearly_insurence_gross,
                        'yearly_final_gross' => $offer->yearly_final_gross,
                        'monthly_final_gross' => $offer->monthly_final_gross,
                        'Basic_offer_points' => $basicPoints,
                        'Bonus_offer_points' => $bonusPoints,
                        'Insurance_offer_points' => $insurancePoints,
                    ];
                });

        }

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('offer-letter', compact('offerDetails', 'offer_terms_conditions', 'offerId', 'exits_offer', 'address', 'comapny_name', 'logo', 'job_title', 'candidate_name'));

        // Save PDF to a temporary file or generate the raw content signature-pad.blade.php
        $pdfContent = $pdf->output();
        $filename = 'offer-letter-'.$offerId.'.pdf';
        // Option 1: Save PDF to storage and attach file path
        $pdfPath = storage_path('app/public/'.$filename.'');
        file_put_contents($pdfPath, $pdfContent);
        $path = str_replace('index.php', '', url('storage/app/public/'.$filename.''));

        $filename = 'offer-letter-'.$offerId.'.pdf';
        // Option 1: Save PDF to storage and attach file path
        $pdfPath = storage_path('app/public/'.$filename.'');
        file_put_contents($pdfPath, $pdfContent);
        // ✅ Store correctly
        Storage::disk('public')->put($filename, $pdf->output());
        //    $path = str_replace('index.php','',url('storage/app/public/'.$filename.''));
        $path = asset('storage/'.$filename);

        //    dd($path);

    }

    public function fwd_offer_download(Request $request)
    {

        $offerId = $request->offerId;
        $coinnotdeduct = true;
        if (@$request->coinnotdeduct) {
            $coinnotdeduct = false;
        }

        $exits_offer = DB::table('offer_create_main_tds')->where('offer_id', '=', $offerId)->where('status', '=', 1)->first();

        $rowDataOfferDetails = GeneralMethod::offerLetterdetails($offerId);

        $address = (@$rowDataOfferDetails) ? @$rowDataOfferDetails->address : '';
        $comapny_name = (@$rowDataOfferDetails) ? @$rowDataOfferDetails->comapny_name : '';
        $logo = (@$rowDataOfferDetails) ? @$rowDataOfferDetails->logo : '';
        $job_title = (@$rowDataOfferDetails) ? @$rowDataOfferDetails->job_title : '';
        $candidate_name = (@$rowDataOfferDetails) ? @$rowDataOfferDetails->full_name : '';

        if ($exits_offer) {

            $offer_terms_conditions = DB::table('offer_terms_conditions')->where('offer_id', '=', $offerId)->orderByDesc('id')->first();
            $offerDetails = DB::table('offer_create_main_tds')
                ->join('offer_job_applications', 'offer_create_main_tds.offer_id', '=', 'offer_job_applications.id')
                ->where('offer_job_applications.id', $offerId)
                ->where('offer_create_main_tds.status', '=', 1)
                ->select(
                    'offer_create_main_tds.offer_id',
                    'offer_create_main_tds.employment_type',
                    'offer_create_main_tds.id as of_main_td',
                    'offer_create_main_tds.currency',
                    'offer_create_main_tds.salary_ctc',
                    'offer_create_main_tds.accept_date_time',
                    'offer_create_main_tds.signature_candidate',
                    'offer_create_main_tds.salary_mode',
                    'offer_create_main_tds.designation',
                    'offer_create_main_tds.joined_date',
                    'offer_create_main_tds.monthly_gross',
                    'offer_create_main_tds.yearly_gross',
                    'offer_create_main_tds.monthly_bounus_gross',
                    'offer_create_main_tds.yearly_bonus_gross',
                    'offer_create_main_tds.monthly_insurence_gross',
                    'offer_create_main_tds.yearly_insurence_gross',
                    'offer_create_main_tds.yearly_final_gross',
                    'offer_create_main_tds.monthly_final_gross'
                )->get()
                ->map(function ($offer) {
                    // Fetch Basic offer points
                    $basicPoints = DB::table('basic_offer_points')
                        ->where('offer_create_main_td_id', $offer->of_main_td)
                        ->select(
                            'id',
                            'poins as basic_poins',
                            'Percentage as basic_percentage',
                            'condition_s as basic_condition_s',
                            'monthly as basic_monthly',
                            'yearly as basic_yearly'
                        )->get()->toArray();

                    // Fetch Bonus offer points
                    $bonusPoints = DB::table('bonus_offer_points')
                        ->where('offer_create_main_td_id', $offer->of_main_td)
                        ->select(
                            'id',
                            'poins as bonus_poins',
                            'Percentage as bonus_percentage',
                            'condition_s as bonus_condition_s',
                            'monthly as bonus_monthly',
                            'yearly as bonus_yearly'
                        )->get()->toArray();

                    // Fetch Insurance offer points
                    $insurancePoints = DB::table('insurence_offer_points')
                        ->where('offer_create_main_td_id', $offer->of_main_td)
                        ->select(
                            'id',
                            'poins as insurence_poins',
                            'Percentage as insurence_percentage',
                            'condition_s as insurence_condition_s',
                            'monthly as insurence_monthly',
                            'yearly as insurence_yearly'
                        )->get()->toArray();

                    // Merge all into the main offer array
                    return [
                        'offer_id' => $offer->offer_id,
                        'employment_type' => $offer->employment_type,
                        'of_main_td' => $offer->of_main_td,
                        'accept_date_time' => $offer->accept_date_time,
                        'signature_candidate' => $offer->signature_candidate,
                        'currency' => $offer->currency,
                        'salary_ctc' => $offer->salary_ctc,
                        'salary_mode' => $offer->salary_mode,
                        'designation' => $offer->designation,
                        'joined_date' => $offer->joined_date,
                        'monthly_gross' => $offer->monthly_gross,
                        'yearly_gross' => $offer->yearly_gross,
                        'monthly_bounus_gross' => $offer->monthly_bounus_gross,
                        'yearly_bonus_gross' => $offer->yearly_bonus_gross,
                        'monthly_insurence_gross' => $offer->monthly_insurence_gross,
                        'yearly_insurence_gross' => $offer->yearly_insurence_gross,
                        'yearly_final_gross' => $offer->yearly_final_gross,
                        'monthly_final_gross' => $offer->monthly_final_gross,
                        'Basic_offer_points' => $basicPoints,
                        'Bonus_offer_points' => $bonusPoints,
                        'Insurance_offer_points' => $insurancePoints,
                    ];
                });

        }

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('offer-letter', compact('offerDetails', 'offer_terms_conditions', 'offerId', 'exits_offer', 'address', 'comapny_name', 'logo', 'job_title', 'candidate_name'));

        //    $filename = "offer-letter-" . $offerId . ".pdf";

        // // ✅ Store correctly
        // Storage::disk('public')->put($filename, $pdf->output());

        // // ✅ Correct URL
        // $url = asset('storage/' . $filename);

        // return Reply::dataOnly([
        //     'status' => 'success',
        //     'url' => $url
        // ]);

        // Save PDF to a temporary file or generate the raw content signature-pad.blade.php
        $pdfContent = $pdf->output();
        $filename = 'offer-letter-'.$offerId.'.pdf';
        // Option 1: Save PDF to storage and attach file path
        $pdfPath = storage_path('app/public/'.$filename.'');
        file_put_contents($pdfPath, $pdfContent);
        // ✅ Store correctly
        Storage::disk('public')->put($filename, $pdf->output());
        //    $path = str_replace('index.php','',url('storage/app/public/'.$filename.''));
        // asset() uses APP_URL which may not match the actual host/subdirectory;
        // request()->root() gives the correct base URL from the live request.
        $path = request()->root().'/storage/'.$filename;
        //    dd($path);
        if ($coinnotdeduct) {
            $eventId = 36;
            $coinsRequ = 1;
            $companyId = (int) auth()->user()->company_id;

            $packageStatus = GeneralMethod::coinsValidationsAllcompany($companyId, $eventId, $coinsRequ);
            if ($packageStatus->status) {
                return response()->json(['status' => 'errors', 'message' => $packageStatus->message]);
            }

            $coinsDeductions = GeneralMethod::coinsDeductions((int) auth()->user()->company_id, $eventId, auth()->user()->id, auth()->user()->company_id, '', '');
            if ($coinsDeductions->status) {
                return response()->json(['status' => 'errors', 'message' => $coinsDeductions->message]);
            }
        }

        return Reply::dataOnly(['status' => 'success', 'url' => $path]);

        $exits_offer = DB::table('offer_create_main_tds')->where('offer_id', '=', $offerId)->where('status', '=', 1)->first();
        if ($exits_offer) {
            $offer_terms_conditions = DB::table('offer_terms_conditions')->where('offer_id', '=', $offerId)->orderByDesc('id')->first();

            $offerDetails = DB::table('offer_create_main_tds')
                ->join('offer_job_applications', 'offer_create_main_tds.offer_id', '=', 'offer_job_applications.id')
                ->where('offer_job_applications.id', $request->offerId)
                ->where('offer_create_main_tds.status', '=', 1)
                ->select(
                    'offer_create_main_tds.offer_id',
                    'offer_create_main_tds.employment_type',
                    'offer_create_main_tds.id as of_main_td',
                    'offer_create_main_tds.currency',
                    'offer_create_main_tds.salary_ctc',
                    'offer_create_main_tds.salary_mode',
                    'offer_create_main_tds.designation',
                    'offer_create_main_tds.joined_date',
                    'offer_create_main_tds.monthly_gross',
                    'offer_create_main_tds.yearly_gross',
                    'offer_create_main_tds.monthly_bounus_gross',
                    'offer_create_main_tds.yearly_bonus_gross',
                    'offer_create_main_tds.monthly_insurence_gross',
                    'offer_create_main_tds.yearly_insurence_gross',
                    'offer_create_main_tds.yearly_final_gross',
                    'offer_create_main_tds.monthly_final_gross'
                )->get()
                ->map(function ($offer) {
                    // Fetch Basic offer points
                    $basicPoints = DB::table('basic_offer_points')
                        ->where('offer_create_main_td_id', $offer->of_main_td)
                        ->select(
                            'id',
                            'poins as basic_poins',
                            'Percentage as basic_percentage',
                            'condition_s as basic_condition_s',
                            'monthly as basic_monthly',
                            'yearly as basic_yearly'
                        )->get()->toArray();

                    // Fetch Bonus offer points
                    $bonusPoints = DB::table('bonus_offer_points')
                        ->where('offer_create_main_td_id', $offer->of_main_td)
                        ->select(
                            'id',
                            'poins as bonus_poins',
                            'Percentage as bonus_percentage',
                            'condition_s as bonus_condition_s',
                            'monthly as bonus_monthly',
                            'yearly as bonus_yearly'
                        )->get()->toArray();

                    // Fetch Insurance offer points
                    $insurancePoints = DB::table('insurence_offer_points')
                        ->where('offer_create_main_td_id', $offer->of_main_td)
                        ->select(
                            'id',
                            'poins as insurence_poins',
                            'Percentage as insurence_percentage',
                            'condition_s as insurence_condition_s',
                            'monthly as insurence_monthly',
                            'yearly as insurence_yearly'
                        )->get()->toArray();

                    // Merge all into the main offer array
                    return [
                        'offer_id' => $offer->offer_id,
                        'employment_type' => $offer->employment_type,
                        'of_main_td' => $offer->of_main_td,
                        'currency' => $offer->currency,
                        'salary_ctc' => $offer->salary_ctc,
                        'salary_mode' => $offer->salary_mode,
                        'designation' => $offer->designation,
                        'joined_date' => $offer->joined_date,
                        'monthly_gross' => $offer->monthly_gross,
                        'yearly_gross' => $offer->yearly_gross,
                        'monthly_bounus_gross' => $offer->monthly_bounus_gross,
                        'yearly_bonus_gross' => $offer->yearly_bonus_gross,
                        'monthly_insurence_gross' => $offer->monthly_insurence_gross,
                        'yearly_insurence_gross' => $offer->yearly_insurence_gross,
                        'yearly_final_gross' => $offer->yearly_final_gross,
                        'monthly_final_gross' => $offer->monthly_final_gross,
                        'Basic_offer_points' => $basicPoints,
                        'Bonus_offer_points' => $bonusPoints,
                        'Insurance_offer_points' => $insurancePoints,
                    ];
                });

            // return view('offer-letter',compact('offerDetails','offer_terms_conditions'));
            $pdf = app('dompdf.wrapper');
            $pdf->loadView('offer-letter', compact('offerDetails', 'offer_terms_conditions'));

            // Save PDF to a temporary file or generate the raw content
            $pdfContent = $pdf->output();
            $filename = 'offer-letter-'.$offerId.'.pdf';
            // Option 1: Save PDF to storage and attach file path
            $pdfPath = storage_path('app/public/'.$filename.'');
            file_put_contents($pdfPath, $pdfContent);
            $path = str_replace('index.php', '', url('storage/app/public/'.$filename.''));
            // dd($path);

            return Reply::dataOnly(['status' => 'success', 'url' => $path]);
        } else {
            return Reply::dataOnly(['status' => 'error', 'url' => '']);
        }

    }

    /**
     * Stream the offer-letter PDF directly to the browser for candidate download.
     * No file storage, no URL construction, no storage-symlink dependency.
     * Accessible without auth — candidates open this from the public signature-pad page.
     */
    public function streamOfferLetterPdf($offerId)
    {
        $exits_offer = DB::table('offer_create_main_tds')
            ->where('offer_id', $offerId)->where('status', 1)->first();
        if (! $exits_offer) {
            abort(404, 'Offer letter not found.');
        }

        $rowDataOfferDetails = GeneralMethod::offerLetterdetails($offerId);
        $address = $rowDataOfferDetails ? $rowDataOfferDetails->address : '';
        $comapny_name = $rowDataOfferDetails ? $rowDataOfferDetails->comapny_name : '';
        $logo = $rowDataOfferDetails ? $rowDataOfferDetails->logo : '';
        $job_title = $rowDataOfferDetails ? $rowDataOfferDetails->job_title : '';
        $candidate_name = $rowDataOfferDetails ? $rowDataOfferDetails->full_name : '';

        $offer_terms_conditions = DB::table('offer_terms_conditions')
            ->where('offer_id', $offerId)->orderByDesc('id')->first();

        $offerDetails = DB::table('offer_create_main_tds')
            ->join('offer_job_applications', 'offer_create_main_tds.offer_id', '=', 'offer_job_applications.id')
            ->where('offer_job_applications.id', $offerId)
            ->where('offer_create_main_tds.status', 1)
            ->select(
                'offer_create_main_tds.offer_id',
                'offer_create_main_tds.employment_type',
                'offer_create_main_tds.id as of_main_td',
                'offer_create_main_tds.currency',
                'offer_create_main_tds.salary_ctc',
                'offer_create_main_tds.accept_date_time',
                'offer_create_main_tds.signature_candidate',
                'offer_create_main_tds.salary_mode',
                'offer_create_main_tds.designation',
                'offer_create_main_tds.joined_date',
                'offer_create_main_tds.monthly_gross',
                'offer_create_main_tds.yearly_gross',
                'offer_create_main_tds.monthly_bounus_gross',
                'offer_create_main_tds.yearly_bonus_gross',
                'offer_create_main_tds.monthly_insurence_gross',
                'offer_create_main_tds.yearly_insurence_gross',
                'offer_create_main_tds.yearly_final_gross',
                'offer_create_main_tds.monthly_final_gross'
            )->get()->map(function ($offer) {
                $basicPoints = DB::table('basic_offer_points')
                    ->where('offer_create_main_td_id', $offer->of_main_td)
                    ->select('id', 'poins as basic_poins', 'Percentage as basic_percentage',
                        'condition_s as basic_condition_s', 'monthly as basic_monthly', 'yearly as basic_yearly')
                    ->get()->toArray();
                $bonusPoints = DB::table('bonus_offer_points')
                    ->where('offer_create_main_td_id', $offer->of_main_td)
                    ->select('id', 'poins as bonus_poins', 'Percentage as bonus_percentage',
                        'condition_s as bonus_condition_s', 'monthly as bonus_monthly', 'yearly as bonus_yearly')
                    ->get()->toArray();
                $insurancePoints = DB::table('insurence_offer_points')
                    ->where('offer_create_main_td_id', $offer->of_main_td)
                    ->select('id', 'poins as insurence_poins', 'Percentage as insurence_percentage',
                        'condition_s as insurence_condition_s', 'monthly as insurence_monthly', 'yearly as insurence_yearly')
                    ->get()->toArray();

                return [
                    'offer_id' => $offer->offer_id,
                    'employment_type' => $offer->employment_type,
                    'of_main_td' => $offer->of_main_td,
                    'accept_date_time' => $offer->accept_date_time,
                    'signature_candidate' => $offer->signature_candidate,
                    'currency' => $offer->currency,
                    'salary_ctc' => $offer->salary_ctc,
                    'salary_mode' => $offer->salary_mode,
                    'designation' => $offer->designation,
                    'joined_date' => $offer->joined_date,
                    'monthly_gross' => $offer->monthly_gross,
                    'yearly_gross' => $offer->yearly_gross,
                    'monthly_bounus_gross' => $offer->monthly_bounus_gross,
                    'yearly_bonus_gross' => $offer->yearly_bonus_gross,
                    'monthly_insurence_gross' => $offer->monthly_insurence_gross,
                    'yearly_insurence_gross' => $offer->yearly_insurence_gross,
                    'yearly_final_gross' => $offer->yearly_final_gross,
                    'monthly_final_gross' => $offer->monthly_final_gross,
                    'Basic_offer_points' => $basicPoints,
                    'Bonus_offer_points' => $bonusPoints,
                    'Insurance_offer_points' => $insurancePoints,
                ];
            });

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('offer-letter', compact(
            'offerDetails', 'offer_terms_conditions', 'offerId',
            'exits_offer', 'address', 'comapny_name', 'logo', 'job_title', 'candidate_name'
        ));

        // Stream PDF directly — Content-Type: application/pdf, Content-Disposition: attachment
        return $pdf->download('Offer-Letter-'.$offerId.'.pdf');
    }

    public function indexregister()
    {

        return view('saas-front.registration_success');
    }

    public function truncateTables()
    {
        $tables = ['client_city_jobs', 'client_contact_info_jobs', 'jobs', 'job_additional_detail', 'job_applications', 'job_assign', 'job_countries', 'job_metronics_skills', 'job_skills', 'recommendation_relation', 'rec_candidate_assign', 'interview_feedback', 'interview_schedules', 'invitation_consultancy', 'applicant_notes', 'application_additional_detail', 'bench_additional_detail', 'bench_applications', 'bench_assign', 'bench_place', 'offer_job_applications', 'client_location_jobs', 'timeline', 'updatetimeline', 'add_job_wise_condidates'];

        foreach ($tables as $table) {
            DB::table($table)->truncate();
        }

        return 'Tables truncated successfully!';
    }

    public function oauth2callback()
    {

        $client = new GoogleClient;
        $client->setClientId(config('google.client_id'));
        $client->setClientSecret(config('google.client_secret'));
        $client->setRedirectUri(config('google.redirect_uri'));
        $client->addScope(Calendar::CALENDAR);

        $authUrl = $client->createAuthUrl();

        return redirect($authUrl);
        // dd($authUrl);
    }

    public function handleGoogleCallback(Request $request)
    {
        $client = new GoogleClient;
        $client->setClientId(config('google.client_id'));
        $client->setClientSecret(config('google.client_secret'));
        $client->setRedirectUri(config('google.redirect_uri'));
        $client->addScope(Calendar::CALENDAR);

        $token = $client->fetchAccessTokenWithAuthCode($request->input('code'));

        $client->setAccessToken($token);

        $calendarService = new Calendar($client);

        // Now you can use $calendarService to create Google Meet meetings
    }

    public function createGoogleMeet()
    {
        $client = new GoogleClient;

        $client->setAccessToken(session('google_access_token'));

        $calendarService = new Calendar($client);

        $event = new \Google\Service\Calendar\Event([
            'summary' => 'Google Meet Meeting',
            'start' => [
                'dateTime' => '2024-08-26T10:00:00-07:00',
                'timeZone' => 'America/Los_Angeles',
            ],
            'end' => [
                'dateTime' => '2024-08-26T11:00:00-07:00',
                'timeZone' => 'America/Los_Angeles',
            ],
            'conferenceData' => [
                'createRequest' => [
                    'conferenceSolutionKey' => ['type' => 'hangoutsMeet'],
                    'requestId' => 'some-random-string',
                ],
            ],
        ]);

        $event = $calendarService->events->insert('primary', $event, ['conferenceDataVersion' => 1]);

        return $event->getHangoutLink();
    }

    public function CountrycityList(Request $request, $id)
    {
        $cityIdArray = [];
        if (! empty($request->cityId)) {
            if (count($request->cityId) > 0) {
                $cityIdArray = $request->cityId;
            }
        }

        $countryId = (explode(',', $id));
        $Rawcitylist = DB::table('city_list')
            ->join('state_list', 'state_list.id', '=', 'city_list.state_id')
            ->join('countries', 'countries.id', '=', 'state_list.country_id')
            ->select('city_list.city_id as id', 'city_list.city as name', 'state_list.state', 'countries.country_name', 'countries.country_code')
            ->whereIn('state_list.country_id', $countryId)
            ->where('city_list.city', '!=', '')
            ->orderBy('city_list.city')
            ->get()->toArray();
        $citylist = json_decode(json_encode($Rawcitylist), true);

        $custom = '<option>Select City</option>';
        foreach ($citylist as $sval) {
            if (in_array($sval['id'], $cityIdArray)) {
                $selected = 'selected';
            } else {
                $selected = '';
            }
            $custom .= '<option value="'.$sval['id'].'" data-full-text="'.$sval['name'].' " '.$selected.'>'.$sval['name'].' , '.$sval['state'].' , '.$sval['country_name'].' </option>';
        }

        return Reply::dataOnly(['status' => 'success', 'data' => $custom]);
    }

    private function matchWithRegexesCommon($text, $regexes)
    {
        foreach ($regexes as $regex) {
            preg_match($regex, $text, $matches);
            if (! empty($matches)) {
                return $matches[0];
            }
        }

        return null;
    }

    private function excludeHeaderWords($text)
    {
        // Common header words to exclude
        $headerWords = ['Resume', 'CV', 'Curriculum Vitae', 'CURRICULUM - VITAE', 'CURRICULUM VITAE', 'Curriculum-vitae'];

        // Remove common header words from the text
        foreach ($headerWords as $word) {
            $text = preg_replace("/\b$word\b/i", '', $text);
        }

        return $text;
    }

    private function checkRepeatedText($text)
    {
        // Get the first and last 50 words
        $words = str_word_count($text, 1);
        $first50Words = implode(' ', array_slice($words, 0, 50));
        $last50Words = implode(' ', array_slice($words, -50));

        // Filter out numbers and email addresses from the first and last 50 words
        $filteredFirst50Words = preg_replace('/\b\d+\b/', '', $first50Words);
        $filteredFirst50Words = preg_replace('/\b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Z|a-z]{2,}\b/', '', $filteredFirst50Words);

        $filteredLast50Words = preg_replace('/\b\d+\b/', '', $last50Words);
        $filteredLast50Words = preg_replace('/\b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Z|a-z]{2,}\b/', '', $filteredLast50Words);

        // Check for repeated text in the filtered first and last 50 words
        preg_match('/\b([A-Za-z]+)\b.*\b\1\b/', $filteredFirst50Words, $firstMatch);
        preg_match('/\b([A-Za-z]+)\b.*\b\1\b/', $filteredLast50Words, $lastMatch);

        // If repeated text is found in both ranges, consider them as a potential name
        if (! empty($firstMatch) && ! empty($lastMatch)) {
            return $firstMatch[1];
        }

        return null;
    }

    private function checkAdjacentNouns($text)
    {

        // Get the first 100 words
        $first100Words = implode(' ', array_slice(str_word_count($text, 1), 0, 100));

        // Identify nouns within the first 100 words
        preg_match_all('/\b[A-Za-z]+\b/i', $first100Words, $nounMatches);

        // If two adjacent nouns are found within the first 100 words, consider them as a potential name
        if (! empty($nounMatches[0])) {
            $nouns = $nounMatches[0];

            for ($i = 0; $i < count($nouns) - 1; $i++) {
                if ($i + 1 < count($nouns) && $nouns[$i + 1] != '') {
                    $potentialName = $nouns[$i].' '.$nouns[$i + 1];
                    if (! $this->isCommonNoun($potentialName)) {
                        if (! $this->isCommonNoun($potentialName)) {
                            return $nouns[$i].' '.$nouns[$i + 1];
                        }
                    }

                }
            }
        }

        return null;
    }

    private function matchWithRegexes($text)
    {
        // Define regular expressions for different data types
        $regexes = [
            '/\b[A-Z][a-z]+ [A-Z][a-z]+\b/',       // First and last name with capital initials
            '/\b[A-Z][a-z]+\b/',                   // Single capitalized name
            '/\b[a-zA-Z]+ [a-zA-Z]+\b/',           // First and last name with any case
            '/\b[A-Z][a-z]+ [A-Z]\. [A-Z][a-z]+\b/', // First name, middle initial, last name
            '/\b[A-Z]\. [A-Z]\. [A-Z][a-z]+\b/',   // Initials format (e.g., J. K. Rowling)
            '/\b[A-Z][a-z]+ [A-Z][a-z]+ [JS]r\.?\b/', // Name with Jr./Sr. suffix
            '/\b[A-Z][a-z]+ [A-Z][a-z]+ III\b/',   // Name with Roman numeral suffix
            '/\b[a-zA-Z]+-[a-zA-Z]+\b/',           // Hyphenated last name (e.g., Maria Rodriguez-Garcia)
            '/\b[A-Z][a-z]+ [A-Z][a-z]+ [A-Z][a-z]+\b/', // Compound last names (e.g., Jean-Luc Picard)
            '/\b(?:Dr\.|Mr\.|Ms\.|Mrs\.) [A-Z][a-z]+\b/', // Names with prefixes (e.g., Dr. John Smith)
        ];

        // Match data using regular expressions
        foreach ($regexes as $regex) {
            preg_match($regex, $text, $matches);
            if (! empty($matches)) {
                return $matches[0];
            }
        }

        return null;
    }

    private function isCommonNoun($word)
    {
        // List of common nouns to exclude
        $commonNouns = ['mobile', 'email', 'address', 'phone', 'fax'];

        // Check if the word is a common noun
        return in_array(strtolower($word), $commonNouns);
    }

    private function locationCountryStateCity($text)
    {
        // Get the text data from the request

        // Define the list of Indian cities with their respective states and countries
        $indianCities = [
            'Mumbai' => ['Maharashtra', 'India'],
            'Delhi' => ['Delhi', 'India'],
            'Bangalore' => ['Karnataka', 'India'],
            'Hyderabad' => ['Telangana', 'India'],
            'Ahmedabad' => ['Gujarat', 'India'],
            'Chennai' => ['Tamil Nadu', 'India'],
            'Kolkata' => ['West Bengal', 'India'],
            'Surat' => ['Gujarat', 'India'],
            'Pune' => ['Maharashtra', 'India'],
            'Jaipur' => ['Rajasthan', 'India'],
            'Lucknow' => ['Uttar Pradesh', 'India'],
            'Kanpur' => ['Uttar Pradesh', 'India'],
            'Nagpur' => ['Maharashtra', 'India'],
            'Patna' => ['Bihar', 'India'],
            'Indore' => ['Madhya Pradesh', 'India'],
            'Thane' => ['Maharashtra', 'India'],
            'Bhopal' => ['Madhya Pradesh', 'India'],
            'Visakhapatnam' => ['Andhra Pradesh', 'India'],
            'Vadodara' => ['Gujarat', 'India'],
            'Firozabad' => ['Uttar Pradesh', 'India'],
            'Ludhiana' => ['Punjab', 'India'],
            'Rajkot' => ['Gujarat', 'India'],
            'Agra' => ['Uttar Pradesh', 'India'],
            'Siliguri' => ['West Bengal', 'India'],
            'Nashik' => ['Maharashtra', 'India'],
            'Faridabad' => ['Haryana', 'India'],
            'Patiala' => ['Punjab', 'India'],
            'Meerut' => ['Uttar Pradesh', 'India'],
            'Kalyan-Dombivli' => ['Maharashtra', 'India'],
            'Vasai-Virar' => ['Maharashtra', 'India'],
            'Varanasi' => ['Uttar Pradesh', 'India'],
            'Srinagar' => ['Jammu and Kashmir', 'India'],
            'Dhanbad' => ['Jharkhand', 'India'],
            'Jodhpur' => ['Rajasthan', 'India'],
            'Amritsar' => ['Punjab', 'India'],
            'Raipur' => ['Chhattisgarh', 'India'],
            'Allahabad' => ['Uttar Pradesh', 'India'],
            'Coimbatore' => ['Tamil Nadu', 'India'],
            'Jabalpur' => ['Madhya Pradesh', 'India'],
            'Gwalior' => ['Madhya Pradesh', 'India'],
            'Vijayawada' => ['Andhra Pradesh', 'India'],
            'Madurai' => ['Tamil Nadu', 'India'],
            'Guwahati' => ['Assam', 'India'],
            'Chandigarh' => ['Chandigarh', 'India'],
            'Hubli-Dharwad' => ['Karnataka', 'India'],
            'Amroha' => ['Uttar Pradesh', 'India'],
            'Moradabad' => ['Uttar Pradesh', 'India'],
            'Gurgaon' => ['Haryana', 'India'],
            'Aligarh' => ['Uttar Pradesh', 'India'],
            'Solapur' => ['Maharashtra', 'India'],
            'Ranchi' => ['Jharkhand', 'India'],
            'Jalandhar' => ['Punjab', 'India'],
            'Tiruchirappalli' => ['Tamil Nadu', 'India'],
            'Bhubaneswar' => ['Odisha', 'India'],
            'Salem' => ['Tamil Nadu', 'India'],
            'Warangal' => ['Telangana', 'India'],
            'Mira-Bhayandar' => ['Maharashtra', 'India'],
            'Thiruvananthapuram' => ['Kerala', 'India'],
            'Bhiwandi' => ['Maharashtra', 'India'],
            'Saharanpur' => ['Uttar Pradesh', 'India'],
            'Guntur' => ['Andhra Pradesh', 'India'],
            'Amravati' => ['Maharashtra', 'India'],
            'Bikaner' => ['Rajasthan', 'India'],
            'Noida' => ['Uttar Pradesh', 'India'],
            'Jamshedpur' => ['Jharkhand', 'India'],
            'Bhilai Nagar' => ['Chhattisgarh', 'India'],
            'Cuttack' => ['Odisha', 'India'],
            'Kochi' => ['Kerala', 'India'],
            'Udaipur' => ['Rajasthan', 'India'],
            'Bhavnagar' => ['Gujarat', 'India'],
            'Dehradun' => ['Uttarakhand', 'India'],
            'Asansol' => ['West Bengal', 'India'],
            'Nanded' => ['Maharashtra', 'India'],
            'Ajmer' => ['Rajasthan', 'India'],
            'Jamnagar' => ['Gujarat', 'India'],
            'Ujjain' => ['Madhya Pradesh', 'India'],
            'Sangli-Miraj & Kupwad' => ['Maharashtra', 'India'],
            'Loni' => ['Uttar Pradesh', 'India'],
            'Jhansi' => ['Uttar Pradesh', 'India'],
            'Pondicherry' => ['Puducherry', 'India'],
            'Nellore' => ['Andhra Pradesh', 'India'],
            'Jammu' => ['Jammu and Kashmir', 'India'],
            'Belagavi' => ['Karnataka', 'India'],
            'Raurkela' => ['Odisha', 'India'],
            'Mangaluru' => ['Karnataka', 'India'],
            'Tirunelveli' => ['Tamil Nadu', 'India'],
            'Malegaon' => ['Maharashtra', 'India'],
            'Gaya' => ['Bihar', 'India'],
            'Tiruppur' => ['Tamil Nadu', 'India'],
            'Davanagere' => ['Karnataka', 'India'],
            'Kozhikode' => ['Kerala', 'India'],
            'Akola' => ['Maharashtra', 'India'],
            'Kurnool' => ['Andhra Pradesh', 'India'],
            'Bokaro Steel City' => ['Jharkhand', 'India'],
            'Rajahmundry' => ['Andhra Pradesh', 'India'],
            'Ballari' => ['Karnataka', 'India'],
            'Agartala' => ['Tripura', 'India'],
            'Bhagalpur' => ['Bihar', 'India'],
            'Latur' => ['Maharashtra', 'India'],
            'Dhule' => ['Maharashtra', 'India'],
            'Korba' => ['Chhattisgarh', 'India'],
            'Bhilwara' => ['Rajasthan', 'India'],
            'Berhampur' => ['Odisha', 'India'],
            'Muzaffarpur' => ['Bihar', 'India'],
            'Ahmednagar' => ['Maharashtra', 'India'],
            'Mathura' => ['Uttar Pradesh', 'India'],
            'Kollam' => ['Kerala', 'India'],
            'Avadi' => ['Tamil Nadu', 'India'],
            'Kadapa' => ['Andhra Pradesh', 'India'],
            'Anantapur' => ['Andhra Pradesh', 'India'],
            'Kamarhati' => ['West Bengal', 'India'],
            'Bilaspur' => ['Chhattisgarh', 'India'],
            'Shahjahanpur' => ['Uttar Pradesh', 'India'],
            'Satara' => ['Maharashtra', 'India'],
            'Bijapur' => ['Karnataka', 'India'],
            'Rampur' => ['Uttar Pradesh', 'India'],
            'Shivamogga' => ['Karnataka', 'India'],
            'Chandrapur' => ['Maharashtra', 'India'],
            'Junagadh' => ['Gujarat', 'India'],
            'Thrissur' => ['Kerala', 'India'],
            'Alwar' => ['Rajasthan', 'India'],
            'Bardhaman' => ['West Bengal', 'India'],
            'Kulti' => ['West Bengal', 'India'],
            'Nizamabad' => ['Telangana', 'India'],
            'Parbhani' => ['Maharashtra', 'India'],
            'Tumkur' => ['Karnataka', 'India'],
            'Khammam' => ['Telangana', 'India'],
            'Ozhukarai' => ['Puducherry', 'India'],
            'Bihar Sharif' => ['Bihar', 'India'],
            'Panipat' => ['Haryana', 'India'],
            'Darbhanga' => ['Bihar', 'India'],
            'Bally' => ['West Bengal', 'India'],
            'Aizawl' => ['Mizoram', 'India'],
            'Dewas' => ['Madhya Pradesh', 'India'],
            'Karnal' => ['Haryana', 'India'],
            'Bathinda' => ['Punjab', 'India'],
            'Jalna' => ['Maharashtra', 'India'],
            'Eluru' => ['Andhra Pradesh', 'India'],
            'Barasat' => ['West Bengal', 'India'],
            'Kirari Suleman Nagar' => ['Delhi', 'India'],
            'Purnia' => ['Bihar', 'India'],
            'Satna' => ['Madhya Pradesh', 'India'],
            'Mau' => ['Uttar Pradesh', 'India'],
            'Sonipat' => ['Haryana', 'India'],
            'Farrukhabad' => ['Uttar Pradesh', 'India'],
            'Sagar' => ['Madhya Pradesh', 'India'],
            'Rourkela' => ['Odisha', 'India'],
            'Durg' => ['Chhattisgarh', 'India'],
            'Imphal' => ['Manipur', 'India'],
            'Ratlam' => ['Madhya Pradesh', 'India'],
            'Hapur' => ['Uttar Pradesh', 'India'],
            'Arrah' => ['Bihar', 'India'],
            'Karimnagar' => ['Telangana', 'India'],
            'Etawah' => ['Uttar Pradesh', 'India'],
            'Ambernath' => ['Maharashtra', 'India'],
            'North Dumdum' => ['West Bengal', 'India'],
            'Bharatpur' => ['Rajasthan', 'India'],
            'Begusarai' => ['Bihar', 'India'],
            'New Delhi' => ['Delhi', 'India'],
            'Gandhidham' => ['Gujarat', 'India'],
            'Baranagar' => ['West Bengal', 'India'],
            'Tiruvottiyur' => ['Tamil Nadu', 'India'],
            'Pondicherry' => ['Puducherry', 'India'],
            'Sikar' => ['Rajasthan', 'India'],
            'Thoothukudi' => ['Tamil Nadu', 'India'],
            'Rewa' => ['Madhya Pradesh', 'India'],
            'Mirzapur' => ['Uttar Pradesh', 'India'],
            'Raichur' => ['Karnataka', 'India'],
            'Pali' => ['Rajasthan', 'India'],
            'Ramagundam' => ['Telangana', 'India'],
            'Silchar' => ['Assam', 'India'],
            'Haridwar' => ['Uttarakhand', 'India'],
            'Vijayanagaram' => ['Andhra Pradesh', 'India'],
            'Tenali' => ['Andhra Pradesh', 'India'],
            'Nagercoil' => ['Tamil Nadu', 'India'],
            'Sri Ganganagar' => ['Rajasthan', 'India'],
            'Karawal Nagar' => ['Delhi', 'India'],
            'Mango' => ['Jharkhand', 'India'],
            'Thanjavur' => ['Tamil Nadu', 'India'],
            'Bulandshahr' => ['Uttar Pradesh', 'India'],
            'Uluberia' => ['West Bengal', 'India'],
            'Katni' => ['Madhya Pradesh', 'India'],
            'Sambalpur' => ['Odisha', 'India'],
            'Singrauli' => ['Madhya Pradesh', 'India'],
            'Nadiad' => ['Gujarat', 'India'],
            'Secunderabad' => ['Telangana', 'India'],
            'Naihati' => ['West Bengal', 'India'],
            'Yamunanagar' => ['Haryana', 'India'],
            'Bidar' => ['Karnataka', 'India'],
            'Pallavaram' => ['Tamil Nadu', 'India'],
            'Bidhan Nagar' => ['West Bengal', 'India'],
            'Munger' => ['Bihar', 'India'],
            'Panchkula' => ['Haryana', 'India'],
            'Burhanpur' => ['Madhya Pradesh', 'India'],
            'Raurkela Industrial Township' => ['Odisha', 'India'],
            'Kharagpur' => ['West Bengal', 'India'],
            'Dindigul' => ['Tamil Nadu', 'India'],
            'Gandhinagar' => ['Gujarat', 'India'],
            'Hospet' => ['Karnataka', 'India'],
            'Nangloi Jat' => ['Delhi', 'India'],
            'Malda' => ['West Bengal', 'India'],
            'Ongole' => ['Andhra Pradesh', 'India'],
            'Deoghar' => ['Jharkhand', 'India'],
            'Chapra' => ['Bihar', 'India'],
            'Haldia' => ['West Bengal', 'India'],
            'Khandwa' => ['Madhya Pradesh', 'India'],
            'Nandyal' => ['Andhra Pradesh', 'India'],
            'Morena' => ['Madhya Pradesh', 'India'],
            'Bhind' => ['Madhya Pradesh', 'India'],
            'Bhalswa Jahangir Pur' => ['Delhi', 'India'],
            'Madhyamgram' => ['West Bengal', 'India'],
            'Bhiwani' => ['Haryana', 'India'],
            'Berhampore' => ['West Bengal', 'India'],
            'Ambala' => ['Haryana', 'India'],
            'Morbi' => ['Gujarat', 'India'],
            'Fatehpur' => ['Uttar Pradesh', 'India'],
            'Bijnor' => ['Uttar Pradesh', 'India'],
            'Raebareli' => ['Uttar Pradesh', 'India'],
            'Khora' => ['Uttar Pradesh', 'India'],
            'Bhusawal' => ['Maharashtra', 'India'],
            'Orai' => ['Uttar Pradesh', 'India'],
            'Bahraich' => ['Uttar Pradesh', 'India'],
            'Phusro' => ['Jharkhand', 'India'],
            'Vellore' => ['Tamil Nadu', 'India'],
            'Mahesana' => ['Gujarat', 'India'],
            'Sambhal' => ['Uttar Pradesh', 'India'],
            'Raiganj' => ['West Bengal', 'India'],
            'Sirsa' => ['Haryana', 'India'],
            'Danapur' => ['Bihar', 'India'],
            'Serampore' => ['West Bengal', 'India'],
            'Sultan Pur Majra' => ['Delhi', 'India'],
            'Guna' => ['Madhya Pradesh', 'India'],
            'Jaunpur' => ['Uttar Pradesh', 'India'],
            'Panvel' => ['Maharashtra', 'India'],
            'Shivpuri' => ['Madhya Pradesh', 'India'],
            'Surendranagar Dudhrej' => ['Gujarat', 'India'],
            'Unnao' => ['Uttar Pradesh', 'India'],
            'Chinsurah' => ['West Bengal', 'India'],
            'Alappuzha' => ['Kerala', 'India'],
            'Kottayam' => ['Kerala', 'India'],
            'Machilipatnam' => ['Andhra Pradesh', 'India'],
            'Shimla' => ['Himachal Pradesh', 'India'],
            'Adoni' => ['Andhra Pradesh', 'India'],
            'Udupi' => ['Karnataka', 'India'],
            'Katihar' => ['Bihar', 'India'],
            'Proddatur' => ['Andhra Pradesh', 'India'],
            'Mahbubnagar' => ['Telangana', 'India'],
            'Saharsa' => ['Bihar', 'India'],
            'Dibrugarh' => ['Assam', 'India'],
            'Jorhat' => ['Assam', 'India'],
            'Hazaribagh' => ['Jharkhand', 'India'],
            'Hindupur' => ['Andhra Pradesh', 'India'],
            'Nagaon' => ['Assam', 'India'],
            'Sasaram' => ['Bihar', 'India'],
            'Hajipur' => ['Bihar', 'India'],
            'Bongaigaon' => ['Assam', 'India'],
            'North Barrackpur' => ['West Bengal', 'India'],
            'Karaikudi' => ['Tamil Nadu', 'India'],
            'Hooghly' => ['West Bengal', 'India'],
            'Bhuj' => ['Gujarat', 'India'],
            'Tiruvannamalai' => ['Tamil Nadu', 'India'],
            'Kumbakonam' => ['Tamil Nadu', 'India'],
            'Modinagar' => ['Uttar Pradesh', 'India'],
            'Unnao' => ['Uttar Pradesh', 'India'],
            'Chinsurah' => ['West Bengal', 'India'],
            'Alappuzha' => ['Kerala', 'India'],
            'Kottayam' => ['Kerala', 'India'],
            'Machilipatnam' => ['Andhra Pradesh', 'India'],
            'Shimla' => ['Himachal Pradesh', 'India'],
            'Adoni' => ['Andhra Pradesh', 'India'],
            'Udupi' => ['Karnataka', 'India'],
            'Katihar' => ['Bihar', 'India'],
            'Proddatur' => ['Andhra Pradesh', 'India'],
            'Mahbubnagar' => ['Telangana', 'India'],
            'Saharsa' => ['Bihar', 'India'],
            'Dibrugarh' => ['Assam', 'India'],
            'Jorhat' => ['Assam', 'India'],
            'Hazaribagh' => ['Jharkhand', 'India'],
            'Hindupur' => ['Andhra Pradesh', 'India'],
            'Nagaon' => ['Assam', 'India'],
            'Sasaram' => ['Bihar', 'India'],
            'Hajipur' => ['Bihar', 'India'],
            'Bongaigaon' => ['Assam', 'India'],
            'North Barrackpur' => ['West Bengal', 'India'],
            'Karaikudi' => ['Tamil Nadu', 'India'],
            'Hooghly' => ['West Bengal', 'India'],
            'Bhuj' => ['Gujarat', 'India'],
            'Tiruvannamalai' => ['Tamil Nadu', 'India'],
            'Kumbakonam' => ['Tamil Nadu', 'India'],
            'Modinagar' => ['Uttar Pradesh', 'India'],
            'Mahbubnagar' => ['Telangana', 'India'],
            'Godhra' => ['Gujarat', 'India'],
            'Porbandar' => ['Gujarat', 'India'],
            'Palakkad' => ['Kerala', 'India'],
            'Pudukkottai' => ['Tamil Nadu', 'India'],
            'Hosur' => ['Tamil Nadu', 'India'],
            'Faridkot' => ['Punjab', 'India'],
            'Alappuzha' => ['Kerala', 'India'],
            'Kottayam' => ['Kerala', 'India'],
            'Machilipatnam' => ['Andhra Pradesh', 'India'],
            'Shimla' => ['Himachal Pradesh', 'India'],
            'Adoni' => ['Andhra Pradesh', 'India'],
            'Udupi' => ['Karnataka', 'India'],
            'Katihar' => ['Bihar', 'India'],
            'Proddatur' => ['Andhra Pradesh', 'India'],
            'Mahbubnagar' => ['Telangana', 'India'],
            'Saharsa' => ['Bihar', 'India'],
            'Dibrugarh' => ['Assam', 'India'],
            'Jorhat' => ['Assam', 'India'],
            'Hazaribagh' => ['Jharkhand', 'India'],
            'Hindupur' => ['Andhra Pradesh', 'India'],
            'Nagaon' => ['Assam', 'India'],
            'Sasaram' => ['Bihar', 'India'],
            'Hajipur' => ['Bihar', 'India'],
            'Bongaigaon' => ['Assam', 'India'],
            'North Barrackpur' => ['West Bengal', 'India'],
            'Karaikudi' => ['Tamil Nadu', 'India'],
            'Hooghly' => ['West Bengal', 'India'],
            'Bhuj' => ['Gujarat', 'India'],
            'Tiruvannamalai' => ['Tamil Nadu', 'India'],
            'Kumbakonam' => ['Tamil Nadu', 'India'],
            'Modinagar' => ['Uttar Pradesh', 'India'],
            'Mahbubnagar' => ['Telangana', 'India'],
            'Godhra' => ['Gujarat', 'India'],
            'Porbandar' => ['Gujarat', 'India'],
            'Palakkad' => ['Kerala', 'India'],
            'Anand' => ['Gujarat', 'India'],
            'Pudukkottai' => ['Tamil Nadu', 'India'],
            'Hosur' => ['Tamil Nadu', 'India'],
            'Faridkot' => ['Punjab', 'India'],
        ];

        // Initialize an empty array to store matching cities
        $matchingCities = ['city' => '', 'state' => '', 'country' => ''];

        // Loop through the Indian cities list and check for matches in the text
        foreach ($indianCities as $city => $details) {
            // Check if the city name appears in the text
            if (stripos($text, $city) !== false) {
                // If found, add the city along with its state and country to the result array
                $matchingCities = [
                    'city' => $city,
                    'state' => $details[0],
                    'country' => $details[1],
                ];
            }
        }

        // Return the matching cities
        return $matchingCities;
    }

    private function parseDateOfBirth($text)
    {
        $textData = $text;

        // Define common date formats
        $indianDateFormats = [
            'd/m/Y', 'd-m-Y', 'd.m.Y', 'd-m-y', 'd.m.y', 'd/m/y',
        ];
        $westernDateFormats = [
            'm/d/Y', 'm-d-Y', 'm.d.Y', 'm-d-y', 'm.d.y', 'm/d/y',
        ];

        // Formats with text-based months
        $textMonthFormats = [
            'F j, Y',  // August 31, 1990
            'M j, Y',  // Aug 31, 1990
            'F j Y',   // August 31 1990 (without commas)
            'M j Y',    // Aug 31 1990 (without commas)
        ];

        $dateFormats = array_merge($indianDateFormats, $westernDateFormats, $textMonthFormats);

        // Reverse document lines
        $lines = array_reverse(preg_split("/\r\n|\n|\r/", $textData));

        foreach ($lines as $line) {
            $line = str_replace(
                ['DOB:', 'th', 'rd', 'st', 'nd', ':', ' : ', 'Date of birth', 'Date Of Birth', 'date of birth', 'DOB', ' '],
                '',
                $line
            );

            foreach ($dateFormats as $format) {
                // Parse the date
                $date = date_create_from_format($format, $line);

                // Check if the parsed date is within 10 years from today
                if ($date !== false &&
                    $date <= date_sub(date_create(), date_interval_create_from_date_string('10 years')) &&
                    $date >= date_sub(date_create(), date_interval_create_from_date_string('150 years')) &&
                    checkdate($date->format('m'), $date->format('d'), $date->format('Y'))) {
                    return response()->json([
                        'dob' => $date->format('Y-m-d'),
                    ]);
                }
            }
        }

        // Return null if no valid date is found
        return response()->json([
            'dob' => null,
        ]);
    }

    public function parseCurrentEmployer($text)
    {
        // Resume text extracted from the request
        $resumeText = $text;

        // List of formats indicating current employment status
        $currentEmploymentFormats = [
            // Existing formats
            'Actively Employed',
            'Currently Employed',
            'Currently Engaged As',
            'Currently Engaged With',
            'Currently Occupied As',
            'Currently Occupied With',
            'Currently Holding Position Of',
            'Currently Holding Role Of',
            'Currently in Active Employment',
            'Currently in Present Job',
            'Currently in Present Role',
            'Currently in Present Employment',
            'Currently in Current Position',
            'Currently in Present Position',
            'Currently on the Job',
            'Currently on the Position',
            'Currently on the Role',
            'Currently on Employment',
            'Currently in Service',
            'Currently in Work',
            'Currently Serving As',
            'Currently Serving With',
            'Currently Working As',
            'Currently Working With',
            'Employed As',
            'Employed At',
            'Employed In',
            'Employed With',
            'Engaged in Employment',
            'Engaged in Job',
            'Engaged in Position',
            'Engaged in Role',
            'Holding Position Of',
            'Holding Role Of',
            'In Active Employment',
            'In Current Position',
            'In Current Role',
            'In Current Employment',
            'In Current Job',
            'In Current Position',
            'In Present Employment',
            'In Present Job',
            'In Present Position',
            'In Present Role',
            'Job Status: Employed',
            'Occupation: Employed',
            'Presently Employed At',
            'Presently Employed With',
            'Presently Engaged As',
            'Presently Engaged With',
            'Presently Occupied As',
            'Presently Occupied With',
            'Presently Holding Position Of',
            'Presently Holding Role Of',
            'Presently in Active Employment',
            'Presently in Current Position',
            'Presently in Current Role',
            'Presently in Current Employment',
            'Presently in Current Job',
            'Presently in Present Position',
            'Presently in Present Role',
            'Presently on the Job',
            'Presently on the Position',
            'Presently on the Role',
            'Presently on Employment',
            'Presently in Service',
            'Presently in Work',
            'Presently Serving As',
            'Presently Serving With',
            'Presently Working As',
            'Presently Working With',
            'Still Holding Position Of',
            'Still Holding Role Of',
            'Still in Active Employment',
            'Still in Current Position',
            'Still in Current Role',
            'Still in Current Employment',
            'Still in Current Job',
            'Still in Present Employment',
            'Still in Present Job',
            'Still in Present Position',
            'Still in Present Role',
            'Still on the Job',
            'Still on the Position',
            'Still on the Role',
            'Still on Employment',
            'Still in Service',
            'Still in Work',
            'Still Serving As',
            'Still Serving With',
            'Still Working As',
            'Still Working With',
            'Actively Working at',
            'Current Employer',
        ];

        // List of additional formats indicating experience
        $experienceKeywords = [
            // Existing keywords
            'Worked at', 'Previous Employer', 'Company', 'Employment History',
            'Career', 'Professional Background', 'Previous Work Experience', 'Work History',
            'Employment Record', 'Job History', 'Work Experience', 'Experience with',
            'Employer', 'Prior Work', 'Former Employer', 'Company Name', 'Previous Job',
            'Worked As', 'Employment Details',
            'Work Experience', 'Professional Experience', 'Previous Experience',
            'Experience with', 'Professional Experience Summary', 'Job Experience', 'Previous Positions',
            'Job Roles', 'Roles and Responsibilities', 'Current Role', 'Current Job',
            'Current Employment', 'Present Role', 'Present Job', 'Current Position',
            'Present Employment', 'Job Profile', 'Current Company', 'Present Company', 'Experience', 'Experience in',
        ];

        // Common designation keywords to exclude from employer name
        $designationKeywords = [
            // Existing keywords
            'CEO', 'CTO', 'CFO', 'COO', 'President', 'Vice President', 'Director', 'Manager', 'Supervisor', 'Lead',
            'Administrator', 'Coordinator', 'Consultant', 'Analyst', 'Engineer', 'Developer', 'Designer', 'Specialist',
            'Officer', 'Executive', 'Assistant', 'Representative', 'Associate', 'Advisor', 'Agent', 'Technician',
            'Operator', 'Accountant', 'Auditor', 'Counselor', 'Educator', 'Trainer', 'Coach', 'Therapist', 'Physician',
            'Nurse', 'Scientist', 'Researcher', 'Writer', 'Editor', 'Designer', 'Artist', 'Photographer', 'Architect',
            'Lawyer', 'Attorney', 'Advocate', 'Paralegal', 'Secretary', 'Receptionist', 'Clerk', 'Assistant', 'Aide',
            'Coordinator', 'Assistant', 'Intern', 'Trainee', 'Volunteer', 'Freelancer', 'Contractor',
        ];

        // Additional conditions and patterns
        $additionalConditions = [
            // Additional conditions for better results
            // For example:
            'Actively Working at', 'Currently Holding Position as', 'Currently Engaged with Company',
            'Currently Employed as',
        ];

        // Combine all conditions and keywords
        $allConditions = array_merge($currentEmploymentFormats, $experienceKeywords, $additionalConditions);

        // dd($allConditions);

        // Check if any of the formats indicate current employment status
        foreach ($allConditions as $condition) {
            // Check for the condition in the resume text
            if (stripos($resumeText, $condition) !== false) {
                // If a match is found, extract the employer name
                // dd($resumeText, $condition, $designationKeywords);
                $employerName = $this->extractEmployerName($resumeText, $condition, $designationKeywords);

                // Return the condition and employer name
                return response()->json(['current_employer_format' => $condition, 'employer_name' => $employerName]);
            }
        }

        // If no match is found, return null
        return response()->json(['current_employer_format' => null, 'employer_name' => null]);
    }

    // Helper function to extract the employer name
    private function extractEmployerName($resumeText, $keyword, $designationKeywords)
    {
        // Find the position of the keyword in the text
        $keywordIndex = stripos($resumeText, $keyword);
        // Extract text after the keyword
        $textAfterKeyword = substr($resumeText, $keywordIndex + strlen($keyword));
        // Split text into individual words
        // trim(preg_replace('/\s+/', ' ', $string));
        $words = explode(' ', trim(preg_replace('/\s+/', ' ', $textAfterKeyword)));
        // Limit words to 20
        $limitedWords = array_slice($words, 0, 20);
        $nounCount = 0;
        $employerName = '';
        // Iterate through words to find the first noun
        foreach ($limitedWords as $word) {
            if ($this->isNoun($word) && ! $this->isDesignation($word, $designationKeywords)) {
                // Concatenate the noun to employerName
                $employerName .= ($employerName ? ' ' : '').$word;
                $nounCount++;
                // Break the loop if maximum adjacent nouns reached
                if ($nounCount >= 3) {
                    break;
                }
            } elseif ($nounCount > 0) {
                // Break the loop if a non-noun is encountered after finding at least one noun
                break;
            }
        }

        return $employerName ?: null; // If no noun is found
    }

    // Subfunction to determine if a word is a noun
    private function isNoun($word)
    {
        // Check if word is not empty
        return ! empty($word);
    }

    // Subfunction to determine if a word is a common designation keyword
    private function isDesignation($word, $designationKeywords)
    {
        // Check if word exists in the list of designation keywords
        return in_array(strtolower($word), array_map('strtolower', $designationKeywords));
    }

    // Main function to parse the current employer from resume text
    private function parseCurrentEmployerw($text)
    {
        // Resume text extracted from the request
        $resumeText = $text;

        // List of formats indicating current employment status
        $currentEmploymentFormats = [
            // Existing formats
            'Actively Employed',
            'Currently Employed',
            'Currently Engaged As',
            'Currently Engaged With',
            'Currently Occupied As',
            'Currently Occupied With',
            'Currently Holding Position Of',
            'Currently Holding Role Of',
            'Currently in Active Employment',
            'Currently in Present Job',
            'Currently in Present Role',
            'Currently in Present Employment',
            'Currently in Current Position',
            'Currently in Present Position',
            'Currently on the Job',
            'Currently on the Position',
            'Currently on the Role',
            'Currently on Employment',
            'Currently in Service',
            'Currently in Work',
            'Currently Serving As',
            'Currently Serving With',
            'Currently Working As',
            'Currently Working With',
            'Employed As',
            'Employed At',
            'Employed In',
            'Employed With',
            'Engaged in Employment',
            'Engaged in Job',
            'Engaged in Position',
            'Engaged in Role',
            'Holding Position Of',
            'Holding Role Of',
            'In Active Employment',
            'In Current Position',
            'In Current Role',
            'In Current Employment',
            'In Current Job',
            'In Current Position',
            'In Present Employment',
            'In Present Job',
            'In Present Position',
            'In Present Role',
            'Job Status: Employed',
            'Occupation: Employed',
            'Presently Employed At',
            'Presently Employed With',
            'Presently Engaged As',
            'Presently Engaged With',
            'Presently Occupied As',
            'Presently Occupied With',
            'Presently Holding Position Of',
            'Presently Holding Role Of',
            'Presently in Active Employment',
            'Presently in Current Position',
            'Presently in Current Role',
            'Presently in Current Employment',
            'Presently in Current Job',
            'Presently in Present Position',
            'Presently in Present Role',
            'Presently on the Job',
            'Presently on the Position',
            'Presently on the Role',
            'Presently on Employment',
            'Presently in Service',
            'Presently in Work',
            'Presently Serving As',
            'Presently Serving With',
            'Presently Working As',
            'Presently Working With',
            'Still Holding Position Of',
            'Still Holding Role Of',
            'Still in Active Employment',
            'Still in Current Position',
            'Still in Current Role',
            'Still in Current Employment',
            'Still in Current Job',
            'Still in Present Employment',
            'Still in Present Job',
            'Still in Present Position',
            'Still in Present Role',
            'Still on the Job',
            'Still on the Position',
            'Still on the Role',
            'Still on Employment',
            'Still in Service',
            'Still in Work',
            'Still Serving As',
            'Still Serving With',
            'Still Working As',
            'Still Working With',
        ];

        // List of additional formats indicating experience
        $experienceKeywords = [
            // Existing keywords
            'Experience:', 'Worked at', 'Previous Employer:', 'Company:', 'Employment History:',
            'Career:', 'Professional Background:', 'Previous Work Experience:', 'Work History:',
            'Employment Record:', 'Job History:', 'Work Experience:', 'Experience with',
            'Employer:', 'Prior Work:', 'Former Employer:', 'Company Name:', 'Previous Job:',
            'Worked As:', 'Employment Details:',
            'Work Experience:', 'Professional Experience:', 'Previous Experience:', 'Experience in',
            'Experience with', 'Professional Experience Summary:', 'Job Experience:', 'Previous Positions:',
            'Job Roles:', 'Roles and Responsibilities:', 'Current Role:', 'Current Job:',
            'Current Employment:', 'Present Role:', 'Present Job:', 'Current Position:',
            'Present Employment:', 'Job Profile:', 'Current Company:', 'Present Company:',
        ];

        // Common designation keywords to exclude from employer name
        $designationKeywords = [
            // Existing keywords
            'CEO', 'CTO', 'CFO', 'COO', 'President', 'Vice President', 'Director', 'Manager', 'Supervisor', 'Lead',
            'Administrator', 'Coordinator', 'Consultant', 'Analyst', 'Engineer', 'Developer', 'Designer', 'Specialist',
            'Officer', 'Executive', 'Assistant', 'Representative', 'Associate', 'Advisor', 'Agent', 'Technician',
            'Operator', 'Accountant', 'Auditor', 'Counselor', 'Educator', 'Trainer', 'Coach', 'Therapist', 'Physician',
            'Nurse', 'Scientist', 'Researcher', 'Writer', 'Editor', 'Designer', 'Artist', 'Photographer', 'Architect',
            'Lawyer', 'Attorney', 'Advocate', 'Paralegal', 'Secretary', 'Receptionist', 'Clerk', 'Assistant', 'Aide',
            'Coordinator', 'Assistant', 'Intern', 'Trainee', 'Volunteer', 'Freelancer', 'Contractor',
        ];

        // Additional conditions and patterns
        $additionalConditions = [
            // Additional conditions for better results
            // For example:
            'Actively Working at', 'Currently Holding Position as', 'Currently Engaged with Company',
            'Currently Employed as',
        ];

        // Combine all conditions and keywords
        $allConditions = array_merge($currentEmploymentFormats, $experienceKeywords, $additionalConditions);

        // Check if any of the formats indicate current employment status
        foreach ($allConditions as $condition) {
            // Check for the condition in the resume text
            if (stripos($resumeText, $condition) !== false) {
                // If a match is found, extract the employer name
                $employerName = $this->extractEmployerName($resumeText, $condition, $designationKeywords);

                // Return the condition and employer name
                return response()->json(['current_employer_format' => $condition, 'employer_name' => $employerName]);
            }
        }

        // If no match is found, return null
        return response()->json(['current_employer_format' => null, 'employer_name' => null]);
    }

    // Helper function to extract the employer name
    private function extractEmployerNamew($resumeText, $keyword, $designationKeywords)
    {
        // Find the position of the keyword in the text
        $keywordIndex = stripos($resumeText, $keyword);
        // Extract text after the keyword
        $textAfterKeyword = substr($resumeText, $keywordIndex + strlen($keyword));
        // Split text into individual words
        $words = explode(' ', $textAfterKeyword);
        // Limit words to 50
        $limitedWords = array_slice($words, 0, 50);
        // Iterate through words to find the first noun
        foreach ($limitedWords as $word) {
            if ($this->isNoun($word) && ! $this->isDesignation($word, $designationKeywords)) {
                return $word;
            }
        }

        return null; // If no noun is found
    }

    // Subfunction to determine if a word is a noun
    private function isNounw($word)
    {
        // Check if word is not empty
        return ! empty($word);
    }

    // Subfunction to determine if a word is a common designation keyword
    private function isDesignationw($word, $designationKeywords)
    {
        // Check if word exists in the list of designation keywords
        return in_array(strtolower($word), array_map('strtolower', $designationKeywords));
    }

    public function benchCandidateDetails(Request $request, $candi_id)
    {

        $company_id = $this->job_additional_detail = $this->bench_assign = '';
        $company_id = (int) $request->company_id;
        $id = (int) $candi_id;

        $cachePayload = [
            'id' => (int) $id,
            'company' => $company_id,
        ];

        $versionKey = 'benchviewmore_version_user_'.$id;
        $version = cache()->get($versionKey, 1);
        $cacheKeySet = 'ats:benchviewmore:'.$id.':'.$version.':'.md5(json_encode($cachePayload));
        /* ----------------------------
        ⚡ Check DragonflyDB first
        ----------------------------- */
        if (cache()->has($cacheKeySet)) {
            return response()->json(cache()->get($cacheKeySet));
        }

        $this->currency = GeneralMethod::currency();
        $this->salaryMode = GeneralMethod::salaryMode();
        // dd($this->currency);
        $this->benchApplication = JobApplication::select(
            'job_applications.id',
            'job_applications.company_id',
            'job_applications.submitted_by',
            'job_applications.status_type',
            'job_applications.ctc',
            'job_applications.ectc',
            'job_applications.ads_switch',
            'job_applications.submitted_by as editAllow',
            'job_applications.full_name',
            'job_applications.current_designation',
            'job_applications.phone_code',
            'job_applications.second_phone_code',
            'job_applications.outsourcing_type',
            'job_applications.phone',
            'job_applications.phone_code',
            'job_applications.second_phone_code',
            'job_applications.secondary_mobile',
            'job_applications.dob',
            'job_applications.email',
            'job_applications.secondrary_email',
            'job_applications.qualification',
            'job_applications.total_exp',
            'job_applications.rel_exp',
            'job_applications.serving_notice_period_mode',
            'job_applications.np_id',
            'job_applications.pay_rate_currency',
            'job_applications.pay_rate_amount',
            'job_applications.pay_rate_mode',
            'job_applications.bill_rate_currency',
            'job_applications.bill_rate_amount',
            'job_applications.bill_rate_mode',
            'job_applications.comission_type',
            'job_applications.country',
            'job_applications.state',
            'job_applications.city',
            'job_applications.pincode',
            'job_applications.skills',
            'job_applications.photo',
            'job_applications.resume',
            'job_applications.status',
            'job_applications.offer_amount',
            'job_applications.joined_date',
            'job_applications.created_at',
            'job_applications.updated_at',
            'job_applications.employee_number',
            'job_applications.linkedin',
            'job_applications.github',
            'job_applications.behance',
            'job_applications.otherLink',
            'qualification_master.name as qualification_name',
            'bench_master_status.name as master_status_name',
            'state_list.state as state_name',
            'city_list.city as city_name',
            'users.name as posted_by',
            'companies.company_name',
            'users.image as posted_by_image'
        )
            ->leftjoin('users', 'users.id', 'job_applications.submitted_by')
            ->leftjoin('qualification_master', 'qualification_master.id', 'job_applications.qualification')
            ->leftJoin('bench_master_status', 'bench_master_status.id', '=', 'job_applications.status')
            ->leftJoin('state_list', 'state_list.id', '=', 'job_applications.state')
            ->leftjoin('companies', 'companies.id', 'users.company_id')
            ->leftJoin('city_list', 'city_list.city_id', '=', 'job_applications.city_id');
        $this->benchApplication = $this->benchApplication->where('job_applications.id', $id);
        $this->benchApplication = $this->benchApplication->first();
        $this->job_additional_detail = DB::table('bench_additional_detail')->select('*')->where('bench_additional_detail.bench_id', '=', @$this->benchApplication->id)->orderBy('bench_additional_detail.id', 'ASC')->get();
        $this->bench_assign = DB::table('bench_assign')->select('users.id', 'users.name')->leftjoin('users', 'users.id', 'bench_assign.user_id')->where('bench_assign.bench_applications_id', '=', @$this->benchApplication->id)->orderBy('bench_assign.id', 'ASC')->get();

        $params = [
            'index' => ELASTIC_BENCH_ASSIGN,
            'body' => [
                'query' => [
                    'bool' => [
                        'must' => [
                            ['term' => ['bench_applications_id' => $id]],
                        ],
                    ],
                ],
                'sort' => [
                    ['id' => ['order' => 'desc']],
                ],
                'size' => 10000, // optional: get more records
            ],
        ];

        $response = $this->elasticService->getClient()->search($params);
        // return users
        $this->bench_assign_count = $response['hits']['total']['value'] ?? 0;
        $this->bench_assign_row = collect($response['hits']['hits'] ?? [])->map(fn ($hit) => (object) $hit['_source']);

        $this->boostkeywords = '';

        $boostkeywords = DB::table('benchboostkeywordcandidates')->where('candidate_id', $id)->pluck('skill_name')->toArray();
        if (count($boostkeywords) > 0) {
            $this->boostkeywords = implode(',', $boostkeywords);
        }

        $this->skillExperiences = DB::table('bench_candidate_skill_experience')
            ->where('candidate_id', $this->benchApplication->id)
            ->get();
        $this->skillExperiencesCount = DB::table('bench_candidate_skill_experience')
            ->where('candidate_id', $this->benchApplication->id)
            ->count();

        //  dd($id,$company_id,$this->data);

        $data = [
            'benchApplication' => $this->benchApplication,
            'job_additional_detail' => $this->job_additional_detail,
            'bench_assign' => $this->bench_assign,
            'bench_assign_count' => $this->bench_assign_count,
            'bench_assign_row' => $this->bench_assign_row,
            'boostkeywords' => $this->boostkeywords,
            'skillExperiences' => $this->skillExperiences,
            'skillExperiencesCount' => $this->skillExperiencesCount,
            'currency' => $this->currency,
            'userPermissions' => [],
            'salaryMode' => $this->salaryMode,
        ];

        $view = view('benchshowdetail', $data)->render();
        $responseData = Reply::dataOnly(['status' => 'success', 'view' => $view]);

        cache()->put($cacheKeySet, $responseData, now()->addMinutes(BENCH_VIEWMORE));

        return $responseData;

    }

    public function validEmailChk(Request $request)
    {

        $status = 'success';

        $validator = Validator::make($request->all(), [
            'email' => [
                'required',
                'email:rfc,dns',
                'disposable_email',
            ],
        ], [
            'email.required' => 'Email is required',
            'email.email' => 'Please enter a valid email',
            'email.disposable_email' => 'Temporary emails are not allowed',
        ]);

        if ($validator->fails()) {
            return Reply::dataOnly(['status' => 'emailchecker', 'message' => $validator->errors()->first()]);

        }

        return Reply::dataOnly(['status' => $status]);
    }

    public function importMeetings(Request $request)
    {
        $request->validate([
            'meeting_link_import' => 'required|file|mimes:xlsx,csv',
        ]);

        $url = '';
        $tableName = $request->input('tablename');
        if ($tableName === 'google_meeting_lists') {
            $url = url('super-admin/google-meeting-setting');
        } elseif ($tableName === 'team_meeting_lists') {
            $url = url('super-admin/team-meeting-setting');
        }

        $file = $request->file('meeting_link_import');
        Excel::queueImport(new MeetingImport($tableName), $file);

        return response()->json([
            'status' => true,
            'url' => $url,
            'message' => 'Import started! Processing in background.',
        ]);
    }
}
