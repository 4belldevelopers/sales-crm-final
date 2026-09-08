<?php

namespace App\Helper;

use App\Models\Client;
use App\Models\Company;
use App\Models\EmailSetting;
use App\Models\Job;
use App\Models\JobApplication;
use App\Services\ElasticService;
use Carbon\Carbon;
use GuzzleHttp\Client as GuzzleHttpClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use PhpOffice\PhpWord\Element\Text;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\IOFactory;
use Smalot\PdfParser\Parser;

/**
 * Class Reply
 */
class GeneralMethod
{
    public static function generate($data)
    {
        return "BEGIN:VCALENDAR
VERSION:2.0
METHOD:REQUEST
BEGIN:VEVENT
UID:{$data['uid']}
DTSTAMP:{$data['timestamp']}
DTSTART:{$data['start']}
DTEND:{$data['end']}
SUMMARY:{$data['summary']}
DESCRIPTION:{$data['description']}
LOCATION:{$data['location']}
STATUS:CONFIRMED
END:VEVENT
END:VCALENDAR";
    }

    public static function generate_md5_hash($digits)
    {
        return md5($digits);
    }

    public static function n_digit_random($digits)
    {
        $temp = '';
        for ($i = 0; $i < $digits; $i++) {
            $temp .= rand(1, 9);
        }

        return (int) $temp;
    }

    public static function getValidUploadFileName($fname)
    {
        $fname = trim($fname);
        $pattern = "[?() ??'\"\/&#\,\;\:\.$]";
        $valid_file = preg_replace("/$pattern/", '-', $fname);
        // $valid_file=ereg_replace($pattern,"-",$fname);
        $valid_file = strtolower($valid_file);

        $valid_file = str_replace('[', '_', $valid_file);
        $valid_file = str_replace(']', '_', $valid_file);
        $valid_file = str_replace('{', '_', $valid_file);
        $valid_file = str_replace('}', '_', $valid_file);
        $valid_file = str_replace(' ', '_', $valid_file);

        $valid_file = str_replace('--', '-', $valid_file);
        $valid_file = str_replace('>', '', $valid_file);
        $valid_file = str_replace('<', '', $valid_file);
        $valid_file = str_replace('=', '-', $valid_file);
        $valid_file = str_replace("'", '-', $valid_file);
        $valid_file = str_replace('"', '-', $valid_file);
        $valid_file = str_replace('~', '-', $valid_file);
        $valid_file = str_replace('^', '-', $valid_file);
        $valid_file = str_replace('@', '', $valid_file);
        $valid_file = str_replace('!', '-', $valid_file);
        $valid_file = str_replace('&', '-', $valid_file);
        $valid_file = str_replace('*', '-', $valid_file);
        $valid_file = str_replace('+', '-', $valid_file);
        $valid_file = str_replace('?', '-', $valid_file);
        $valid_file = str_replace('(', '', $valid_file);
        $valid_file = str_replace(')', '', $valid_file);
        $valid_file = str_replace(',', '-', $valid_file);
        $valid_file = str_replace(':', '-', $valid_file);
        $valid_file = str_replace(';', '-', $valid_file);
        $valid_file = str_replace('$', '-', $valid_file);
        $valid_file = str_replace('#', '-', $valid_file);
        $valid_file = str_replace('%', '-', $valid_file);
        $valid_file = str_replace('__', '-', $valid_file);
        $valid_file = str_replace('__', '-', $valid_file);
        $valid_file = str_replace('__', '-', $valid_file);
        // $valid_file=str_replace(".","",$valid_file);
        $valid_file = str_replace('--', '-', $valid_file);
        if (substr($valid_file, -1) == '-') {
            $valid_file = substr($valid_file, 0, -1);
            // echo $valid_file;
        }

        return strtolower($valid_file);
    }

    public static function document_manage_directory($directory_name)
    {
        $random_number = $generate_hash = $parent_directory = $child = '';

        $random_number = self::n_digit_random(4);
        $generate_hash = self::generate_md5_hash($random_number);

        $parent_directory = substr($generate_hash, 0, 2);
        $child = substr($generate_hash, 2, 2);

        $CUSTOM_ROOT_PATH = public_path('user-uploads/client-document/');

        $employerPath = $CUSTOM_ROOT_PATH.$directory_name;

        // CREATE ROOT DIRECTORY
        if (! is_dir($CUSTOM_ROOT_PATH.$directory_name)) {
            mkdir($CUSTOM_ROOT_PATH.$directory_name, 0777, true);
        }

        // PARENT DIRECTORY
        if (! is_dir($employerPath.'/'.$parent_directory)) {
            mkdir($employerPath.'/'.$parent_directory, 0777, true);
        }
        // CHILD DIRECTORY
        if (! is_dir($employerPath.'/'.$parent_directory.'/'.$child)) {
            mkdir($employerPath.'/'.$parent_directory.'/'.$child, 0777, true);
        }

        return $employerPath.'/'.$parent_directory.'/'.$child;
    }

    public static function ymdTodmy($originalDate)
    {

        return date('d-M-Y H:i:s', strtotime($originalDate));

    }

    public static function ymdTodmyNotTime($originalDate)
    {

        return date('d M y', strtotime($originalDate));

    }

    /**
     * Icon + color + short description for a candidate_status (sub-stage/
     * rejection-reason) row — these are free-text, per-company admin-entered
     * names with no icon/description column in the DB, so all three are
     * inferred client-render-time via keyword match. Used by the Change
     * Candidate Stage drawer (stage_body_content/client_stage_body_content/
     * child_stage) as a display-only affordance; never affects what's
     * actually submitted (still just the candidate_status.id).
     *
     * 'icon' is inline SVG inner-markup (stroke-based, 24x24 viewBox), not a
     * Font Awesome class — this app only has Font Awesome **4.7.0** loaded
     * (see demo/css/font-awesome.min.css), which predates the fa5/6 "fas"
     * prefix and most icon names a newer FA cheat-sheet would suggest; SVG
     * matches the existing $hpStages/stage-pill icon convention and renders
     * reliably regardless of which icon font happens to be loaded.
     */
    public static function csdSubStatusMeta($name)
    {
        $n = strtolower((string) $name);
        $svgCopy = '<rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>';
        $svgClock = '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3.5 2"/>';
        $svgUser = '<circle cx="12" cy="8" r="3.5"/><path d="M5 20c0-3.5 3-6 7-6s7 2.5 7 6"/>';
        $svgDollar = '<path d="M12 1v22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>';
        $svgCalendar = '<rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>';
        $svgXCircle = '<circle cx="12" cy="12" r="9"/><path d="M9 9l6 6M15 9l-6 6"/>';
        $svgAlert = '<path d="M10.3 4 2 18a1.8 1.8 0 0 0 1.5 2.7h17a1.8 1.8 0 0 0 1.5-2.7L13.7 4a1.8 1.8 0 0 0-3.4 0Z"/><path d="M12 9v4M12 17h.01"/>';
        $svgUserX = '<circle cx="9" cy="8" r="3.5"/><path d="M2 20c0-3.5 3-6 7-6s7 2.5 7 6"/><path d="M17 9l4 4M21 9l-4 4"/>';
        $svgUserCheck = '<circle cx="9" cy="8" r="3.5"/><path d="M2 20c0-3.5 3-6 7-6"/><path d="M15 13l2.5 2.5L22 11"/>';
        $svgPause = '<rect x="6" y="4" width="4" height="16" rx="1"/><rect x="14" y="4" width="4" height="16" rx="1"/>';
        $svgShield = '<path d="M12 3l7 3v6c0 5-3.5 8-7 9-3.5-1-7-4-7-9V6l7-3Z"/>';
        $svgLogOut = '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5M21 12H9"/>';
        $svgPlusCircle = '<circle cx="12" cy="12" r="9"/><path d="M12 8v8M8 12h8"/>';
        $svgDot = '<circle cx="12" cy="12" r="3" fill="currentColor" stroke="none"/>';

        // [needle, icon, color, bg, description] — first match wins, so more
        // specific phrases are listed before their broader/generic overlaps.
        $map = [
            ['duplicate',       $svgCopy,       '#f1416c', '#fdedf0', 'Duplicate profile'],
            ['not interest',    $svgClock,      '#ffa800', '#fff4de', 'Candidate not interested'],
            ['not looking',     $svgClock,      '#ffa800', '#fff4de', 'Not actively looking'],
            ['not relevant',    $svgUser,       '#3e97ff', '#eef4ff', 'Profile not relevant to role'],
            ['mismatch',        $svgUser,       '#3e97ff', '#eef4ff', 'Does not match role'],
            ['stability',       $svgAlert,      '#ffa800', '#fff4de', 'Stability concerns'],
            ['high salary',     $svgDollar,     '#17c653', '#eafcf3', 'Salary expectations high'],
            ['salary',          $svgDollar,     '#17c653', '#eafcf3', 'Salary expectations high'],
            ['compensation',    $svgDollar,     '#17c653', '#eafcf3', 'Compensation expectations high'],
            ['notice',          $svgCalendar,   '#7239ea', '#f8f5ff', 'Notice period too long'],
            ['interview reject', $svgXCircle,    '#f1416c', '#fdedf0', 'Rejected in interview'],
            ['fake',            $svgAlert,      '#ffa800', '#fff4de', 'Fake or proxy candidate'],
            ['proxy',           $svgAlert,      '#ffa800', '#fff4de', 'Fake or proxy candidate'],
            ['no show',         $svgUserX,      '#f1416c', '#fdedf0', 'Candidate did not show up'],
            ['noshow',          $svgUserX,      '#f1416c', '#fdedf0', 'Candidate did not show up'],
            ['backed',          $svgUserX,      '#f1416c', '#fdedf0', 'Candidate backed out'],
            ['back out',        $svgUserX,      '#f1416c', '#fdedf0', 'Candidate backed out'],
            ['backout',         $svgUserX,      '#f1416c', '#fdedf0', 'Candidate backed out'],
            ['onboard',         $svgUserCheck,  '#17c653', '#eafcf3', 'Candidate onboarded'],
            ['hold',            $svgPause,      '#ffa800', '#fff4de', 'On hold'],
            ['schedul',         $svgCalendar,   '#3e97ff', '#eef4ff', 'Interview scheduled'],
            ['background',      $svgShield,     '#3e97ff', '#eef4ff', 'Background check pending'],
            ['bgv',             $svgShield,     '#3e97ff', '#eef4ff', 'Background check pending'],
            ['withdraw',        $svgLogOut,     '#7e8299', '#eef0f2', 'Candidate withdrew'],
            ['unresponsive',    $svgXCircle,    '#7e8299', '#eef0f2', 'Candidate unresponsive'],
            ['not contact',     $svgXCircle,    '#7e8299', '#eef0f2', 'Unable to reach candidate'],
            ['reject',          $svgXCircle,    '#f1416c', '#fdedf0', 'Rejected'],
            ['declin',          $svgXCircle,    '#f1416c', '#fdedf0', 'Declined'],
            ['other',           $svgPlusCircle, '#ffa800', '#fff4de', 'Add custom reason'],
        ];
        foreach ($map as [$needle, $icon, $color, $bg, $desc]) {
            if (strpos($n, $needle) !== false) {
                return ['icon' => $icon, 'color' => $color, 'bg' => $bg, 'desc' => $desc];
            }
        }

        return ['icon' => $svgDot, 'color' => '#7e8299', 'bg' => '#eef0f2', 'desc' => ''];
    }

    /**
     * Human-friendly display label for a permission group_name on the Roles & Permissions
     * screens. Display-only remap agreed with the product owner — the underlying DB
     * group_name is unchanged, so permissions/saving are unaffected. Used by both the
     * main role cards and the Add/Edit role drawer so the labels never drift apart.
     */
    public static function roleGroupLabel($groupName)
    {
        $map = [
            'staffing partners' => 'Agencies',
            'bench' => 'Inhouse Talents',
            'team' => 'Team/Users',
            'website' => 'Career Website',
            'salary break' => 'Compensation',
            'job applications' => 'Candidates',
            'schedule' => 'Interview Schedule',
            'offer' => 'Offers Tracker',
            'joining' => 'Joining Tracker',
            'salary' => 'Salary Details of Candidate',
        ];
        $key = strtolower(trim($groupName));

        return $map[$key] ?? ucwords($groupName);
    }

    public static function employmentType()
    {
        $array = [];
        $array[0] = 'Full-time';
        $array[1] = 'Contract';
        $array[2] = 'Freelance';
        $array[3] = 'Part Time';

        return $array;
    }

    public static function priority()
    {
        $array = [];
        $array[0] = 'Easy';
        $array[1] = 'Urgent';
        $array[2] = 'Very Urgent';

        return $array;
    }

    public static function department()
    {

        $department = DB::table('departments')
            ->select('departments.id as ind_id', 'departments.name as ind_name')
            ->orderBy('departments.name', 'ASC')
            ->get();

        // dd($department);

        $array = [];
        $array[0] = '';
        if ($department) {
            foreach ($department as $val) {
                array_push($array, $val->ind_name);
            }
        }
        // $array[0] 	= 'Accounts & Finance';
        // $array[1] 	= 'BPO / Telecaller';
        // $array[2] 	= 'Design / Creativity';
        // $array[3] 	= 'Engineer';
        // $array[4] 	= 'Events / Media';
        // $array[5] 	= 'Healthcare';
        // $array[6] 	= 'IT / Software';
        // $array[7] 	= 'Logistics / Warehouse';
        // $array[8] 	= 'Marketing / Content';
        // $array[9] 	= 'Sales / Business Development';

        // dd($array);

        return $array;
    }

    public static function industries()
    {

        $department = DB::table('td_industries')
            ->select('td_industries.ind_id', 'td_industries.ind_name')
            ->orderBy('td_industries.ind_name', 'ASC')
            ->get();

        // dd($department);

        $array = [];
        $array[0] = '';
        if ($department) {
            foreach ($department as $val) {
                array_push($array, $val->ind_name);
            }
        }
        // $array[0] 	= 'Accounts & Finance';
        // $array[1] 	= 'BPO / Telecaller';
        // $array[2] 	= 'Design / Creativity';
        // $array[3] 	= 'Engineer';
        // $array[4] 	= 'Events / Media';
        // $array[5] 	= 'Healthcare';
        // $array[6] 	= 'IT / Software';
        // $array[7] 	= 'Logistics / Warehouse';
        // $array[8] 	= 'Marketing / Content';
        // $array[9] 	= 'Sales / Business Development';

        // dd($array);

        return $array;
    }

    public static function icoinText($text)
    {
        // dd($text);

        $bgTextDecore = $strVal = '';
        $str = trim(@$text);
        $str = $strVal = strtolower($str[0]);
        // dd($str);
        $str = ord($str);
        if ($str >= 97 && $str <= 101) {
            $bgTextDecore = 'bg-light-danger text-danger';
        } elseif ($str >= 102 && $str <= 106) {
            $bgTextDecore = 'bg-light-warning text-warning';
        } elseif ($str >= 107 && $str <= 111) {
            $bgTextDecore = 'bg-light-primary text-primary';
        } elseif ($str >= 112 && $str <= 116) {
            $bgTextDecore = 'bg-light-success text-success';
        } elseif ($str >= 117 && $str <= 121) {
            $bgTextDecore = 'bg-dark text-secondary';
        } else {
            $bgTextDecore = 'bg-light-secondary text-dark';
        }

        return $bgTextDecore;

    }

    public static function manageTimeline($owner_id, $application_id, $job_id = '', $process_id = '', $status_name = '', $note = '', $process_child_name = '', $insert_type = '')
    {
        if (empty($insert_type)) {
            $insert_type = 0;
        }
        $date = date('Y-m-d H:i:s');
        $application_cnt = 0;
        $application_cnt = DB::table('job_applications')->select('*')->where('id', $application_id)->count();
        if ($application_cnt > 0) {
            $application_Row = DB::table('job_applications')->select('email')->where('id', $application_id)->first();
            $values = [
                'owner_id' => $owner_id,
                'application_id' => $application_id,
                'application_email' => $application_Row->email,
                'job_id' => $job_id,
                'process_id' => $process_id,
                'process_child_name' => $process_child_name,
                'status_name' => $status_name,
                'note' => $note,
                'insert_type' => $insert_type,
                'create_at' => $date,
                'update_at' => $date,
            ];
            DB::table('timeline')->insert($values);
        }

    }

    public static function candidateStatusColorCode($status)
    {
        // echo $status;
        // exit;

        $array = [];

        // color:white!important;
        // background:linear-gradient(to left, #01e074, #0581d9)!important;;

        $array['all']['Index'] = 'All_candidate';
        $array['all']['color'] = '#fff';
        // $array['all']['bg_class'] 			= 'bg-all linear-gradient(to left, #01e074, #0581d9)';
        $array['all']['bg_class'] = 'bg-total-active';

        $array['Sourcing']['Index'] = 'Sourcing';
        $array['Sourcing']['color'] = '#000000';
        $array['Sourcing']['bg'] = '#efefef';
        $array['Sourcing']['bg_class'] = 'bg-Asourcing';
        $array['Sourcing']['nav_text'] = 'text-Asourcing';
        $array['Sourcing']['tiles_bg'] = 'sourcing-bg';

        $array['Screening']['Index'] = 'Screening';
        $array['Screening']['color'] = '#ffab00';
        $array['Screening']['bg'] = '#fff9ed';
        $array['Screening']['bg_class'] = 'bg-Ascreening';
        $array['Screening']['nav_text'] = 'text-Ascreening';
        $array['Screening']['tiles_bg'] = 'screening-bg';

        $array['Interview']['Index'] = 'Interview';
        $array['Interview']['color'] = '#2896ff';
        $array['Interview']['bg'] = '#f0f8ff';
        $array['Interview']['bg_class'] = 'bg-Ainterview';
        $array['Interview']['nav_text'] = 'text-Ainterview';
        $array['Interview']['tiles_bg'] = 'interview';

        $array['Evaluation']['Index'] = 'Interview';
        $array['Evaluation']['color'] = '#2896ff';
        $array['Evaluation']['bg'] = '#f0f8ff';
        $array['Evaluation']['bg_class'] = 'bg-Ainterview';
        $array['Evaluation']['nav_text'] = 'text-Ainterview';
        $array['Evaluation']['tiles_bg'] = 'interview';

        $array['Negotiation']['Index'] = 'Negotiation';
        $array['Negotiation']['color'] = '#ff4098';
        $array['Negotiation']['bg'] = '#ffe6f2';
        $array['Negotiation']['bg_class'] = 'bg-Anegotation';
        $array['Negotiation']['nav_text'] = 'text-Anegotation';
        $array['Negotiation']['tiles_bg'] = 'negotation-bg';

        $array['Offered']['Index'] = 'Offered';
        $array['Offered']['color'] = '#06c732';
        $array['Offered']['bg'] = '#d4ffde';
        $array['Offered']['bg_class'] = 'bg-Aoffer';
        $array['Offered']['nav_text'] = 'text-Aoffer';
        $array['Offered']['tiles_bg'] = 'offer-bg';

        $array['Onboarded']['Index'] = 'Joined';
        $array['Onboarded']['color'] = '#06c732';
        $array['Onboarded']['bg'] = '#d4ffde';
        $array['Onboarded']['bg_class'] = 'bg-Aoffer';
        $array['Onboarded']['nav_text'] = 'text-Aoffer';
        $array['Onboarded']['tiles_bg'] = 'offer-bg';

        $array['Joined']['Index'] = 'Joined';
        $array['Joined']['color'] = '#c000ff';
        $array['Joined']['bg'] = '#f7deff';
        $array['Joined']['bg_class'] = 'bg-Ajoiner';
        $array['Joined']['nav_text'] = 'text-Ajoiner';
        $array['Joined']['tiles_bg'] = 'joined-bg';

        $array['Rejected']['Index'] = 'Rejected';
        $array['Rejected']['color'] = '#ff0202';
        $array['Rejected']['bg'] = '#ffe2e5';
        $array['Rejected']['bg_class'] = 'bg-Arejected';
        $array['Rejected']['nav_text'] = 'text-Arejected';
        $array['Rejected']['tiles_bg'] = 'rejected-bg';

        $array['InReview']['Index'] = 'Sourcing';
        $array['InReview']['color'] = '#000000';
        $array['InReview']['bg'] = '#efefef';
        $array['InReview']['bg_class'] = 'bg-Asourcing';
        $array['InReview']['nav_text'] = 'text-Asourcing';
        $array['InReview']['tiles_bg'] = 'sourcing-bg';

        $array['SendMail']['Index'] = 'Sourcing';
        $array['SendMail']['color'] = '#000000';
        $array['SendMail']['bg'] = '#efefef';
        $array['SendMail']['bg_class'] = 'bg-Asourcing';
        $array['SendMail']['nav_text'] = 'text-Asourcing';
        $array['SendMail']['tiles_bg'] = 'sourcing-bg';

        $array['Expired']['Index'] = 'Expired';
        $array['Expired']['color'] = '#ff0202';
        $array['Expired']['bg'] = '#ffe2e5';
        $array['Expired']['bg_class'] = 'bg-Arejected';
        $array['Expired']['nav_text'] = 'text-Arejected';
        $array['Expired']['tiles_bg'] = 'rejected-bg';
        $array['Expired']['tiles_bg'] = 'rejected-bg';

        $array['Whatsapp Event']['Index'] = 'Sourcing';
        $array['Whatsapp Event']['color'] = '#000000';
        $array['Whatsapp Event']['bg'] = '#efefef';
        $array['Whatsapp Event']['bg_class'] = 'bg-Asourcing';
        $array['Whatsapp Event']['nav_text'] = 'text-Asourcing';
        $array['Whatsapp Event']['tiles_bg'] = 'sourcing-bg';

        return $array[$status];
    }

    public static function available_status()
    {
        return cache()->rememberForever('ref:interview_status:all', function () {
            $interview_status = DB::table('interview_status')
                ->where('interview_status.status', 1)
                ->get();

            $array = [];

            foreach ($interview_status as $index => $status) {
                $array[$status->id] = [
                    'id' => $status->id,
                    'name' => $status->name,
                    'classname' => ($status->classname), // Assign classname dynamically
                ];
            }

            return $array;
        });
    }

    public static function interview_status()
    {

        $interview_status = DB::table('interview_status')
            ->where('interview_status.status', 1)
            ->get();

        return $interview_status;
    }

    public static function feedback_status()
    {
        return cache()->rememberForever('ref:interview_sub_status:all', function () {
            $interview_status = DB::table('interview_sub_status')
                ->where('interview_sub_status.status', 1)
                ->get();

            $array = [];

            foreach ($interview_status as $index => $status) {
                $array[$status->id] = [
                    'id' => $status->id,
                    'name' => $status->name,
                    'classname' => ($status->classname), // Assign classname dynamically
                ];
            }

            return $array;
        });
    }

    public static function conversation_status()
    {
        $array = [
            0 => ['name' => 'Conversation 1', 'classname' => 'shortlisted-bg'],
            1 => ['name' => 'Conversation 2', 'classname' => 'rescheduled-bg'],
            2 => ['name' => 'Conversation 3', 'classname' => 'rejected-bg'],
            3 => ['name' => 'Conversation 5', 'classname' => 'feedback-bg'],
            4 => ['name' => 'Conversation 5', 'classname' => 'pending-bg'],
            5 => ['name' => 'Conversation 6', 'classname' => 'rescheduled-bg'],
        ];

        return $array;
    }

    public static function interview_mode()
    {
        $array = [1 => 'Telecon', 2 => 'Video', 3 => 'F2F', 4 => 'Other'];

        return $array;
    }

    /**
     * Intent: Check duplicate candidates by email or phone within tenant company context.
     * Tenancy note: Filters by company_id or thirdparty_company_id.
     */
    public static function checkDuplicateCandidate($company_id, $app_duplicate, $job_id, $email, $app_duplicate_days, $phone)
    {
        $application_cnt = 0;
        if (empty($email) && empty($phone)) {
            return 0;
        }

        $days = (int) ($app_duplicate_days ?? 0);

        if ($app_duplicate == 1) {
            $query = DB::table('job_applications')
                ->where(function ($query) use ($company_id) {
                    $query->where('company_id', $company_id)
                        ->orWhere('thirdparty_company_id', $company_id);
                })
                ->where('job_id', $job_id)
                ->where(function ($query) use ($email, $phone) {
                    if (! empty($email) && ! empty($phone)) {
                        $query->where('email', $email)->orWhere('phone', $phone);
                    } elseif (! empty($email)) {
                        $query->where('email', $email);
                    } elseif (! empty($phone)) {
                        $query->where('phone', $phone);
                    }
                });

            if ($days > 0) {
                $query->where('job_applications.created_at', '>=', DB::raw('DATE_SUB(CURDATE(), INTERVAL '.$days.' DAY)'));
            }

            $application_cnt = $query->count();
        } elseif ($app_duplicate == 2) {
            $query = DB::table('job_applications')
                ->where(function ($query) use ($company_id) {
                    $query->where('company_id', $company_id)
                        ->orWhere('thirdparty_company_id', $company_id);
                })
                ->where(function ($query) use ($email, $phone) {
                    if (! empty($email) && ! empty($phone)) {
                        $query->where('email', $email)->orWhere('phone', $phone);
                    } elseif (! empty($email)) {
                        $query->where('email', $email);
                    } elseif (! empty($phone)) {
                        $query->where('phone', $phone);
                    }
                });

            if ($days > 0) {
                $query->where('job_applications.created_at', '>=', DB::raw('DATE_SUB(CURDATE(), INTERVAL '.$days.' DAY)'));
            }

            $application_cnt = $query->count();
        }

        return $application_cnt;
    }

    /**
     * Intent: Check duplicate candidates for client third-party company context.
     * Tenancy note: Filters by thirdparty_company_id.
     */
    public static function checkDuplicateCandidateClient($company_id, $app_duplicate, $job_id, $email, $app_duplicate_days, $phone)
    {
        $application_cnt = 0;
        if (empty($email) && empty($phone)) {
            return 0;
        }

        $days = (int) ($app_duplicate_days ?? 0);

        if ($app_duplicate == 1) {
            $query = DB::table('job_applications')
                ->where('thirdparty_company_id', $company_id)
                ->where('job_id', $job_id)
                ->where(function ($query) use ($email, $phone) {
                    if (! empty($email) && ! empty($phone)) {
                        $query->where('email', $email)->orWhere('phone', $phone);
                    } elseif (! empty($email)) {
                        $query->where('email', $email);
                    } elseif (! empty($phone)) {
                        $query->where('phone', $phone);
                    }
                });

            if ($days > 0) {
                $query->where('job_applications.created_at', '>=', DB::raw('DATE_SUB(CURDATE(), INTERVAL '.$days.' DAY)'));
            }

            $application_cnt = $query->count();
        } elseif ($app_duplicate == 2) {
            $query = DB::table('job_applications')
                ->where('thirdparty_company_id', $company_id)
                ->where(function ($query) use ($email, $phone) {
                    if (! empty($email) && ! empty($phone)) {
                        $query->where('email', $email)->orWhere('phone', $phone);
                    } elseif (! empty($email)) {
                        $query->where('email', $email);
                    } elseif (! empty($phone)) {
                        $query->where('phone', $phone);
                    }
                });

            if ($days > 0) {
                $query->where('job_applications.created_at', '>=', DB::raw('DATE_SUB(CURDATE(), INTERVAL '.$days.' DAY)'));
            }

            $application_cnt = $query->count();
        }

        return $application_cnt;
    }

    public static function getUserContribution($user_id)
    {
        $contributionArr = DB::table('clients')
            ->join('jobs', 'jobs.client_id', '=', 'clients.id')
            ->join('job_applications', 'job_applications.job_id', '=', 'jobs.id')
            ->join('users', 'users.id', '=', 'job_applications.submitted_by')
            ->select(
                'clients.name as client_name', 'jobs.title as job_title', 'jobs.id as job_id', DB::raw('COUNT(job_applications.id) as application_counter'),
                'users.name as user_name', 'users.id as user_id'
            )
            ->where('job_applications.company_id', $this->user->company_id)
            ->where('job_applications.submitted_by', $user_id)
            ->groupBy('job_applications.job_id')
            ->get();

        return $contributionArr;
    }

    public static function bgTextDecore($str)
    {
        $bgTextDecore = '';
        $str = trim($str);
        $str = strtolower($str[0]);
        $str = ord($str);
        if ($str >= 97 && $str <= 101) {
            $bgTextDecore = 'fs-3 bg-light-danger text-danger';
        } elseif ($str >= 102 && $str <= 106) {
            $bgTextDecore = 'fs-3 bg-light-warning text-warning';
        } elseif ($str >= 107 && $str <= 111) {
            $bgTextDecore = 'fs-3 bg-light-primary text-primary';
        } elseif ($str >= 112 && $str <= 116) {
            $bgTextDecore = 'fs-3 bg-light-success text-success';
        } elseif ($str >= 117 && $str <= 121) {
            $bgTextDecore = 'fs-3 bg-dark text-secondary';
        } else {
            $bgTextDecore = 'fs-3 bg-light-secondary text-dark';
        }

        return $bgTextDecore;
    }

    public static function job_status()
    {
        $array = ['hold' => 'hold-bg', 'active' => 'active-bg', 'inactive' => 'closed-bg'];

        return $array;
    }

    public static function timeAgo($time_ago)
    {
        // Convert the date string to a timestamp
        $time_ago = strtotime($time_ago);

        // Ensure the current time is in the same timezone as $time_ago
        $cur_time = date('Y-m-d H:i:s');
        $cur_time = strtotime($cur_time);

        // Calculate the time difference
        $time_elapsed = $cur_time - $time_ago;

        // echo "Current Time: " . date('Y-m-d H:i:s', $cur_time) . "<br>";
        // echo "Time Ago: " . date('Y-m-d H:i:s', $time_ago) . "<br>";

        // die;

        // Calculate time units
        $seconds = $time_elapsed;
        $minutes = floor($seconds / 60);
        $hours = floor($minutes / 60);
        $days = floor($hours / 24);
        $weeks = floor($days / 7);
        $months = floor($days / 30); // Approximation
        $years = floor($days / 365); // Approximation

        // Return the time ago string
        if ($seconds <= 60) {
            return 'just now';
        } elseif ($minutes <= 60) {
            return ($minutes == 1) ? 'one minute ago' : "$minutes minutes ago";
        } elseif ($hours <= 24) {
            return ($hours == 1) ? 'an hour ago' : "$hours hours ago";
        } elseif ($days <= 7) {
            return ($days == 1) ? 'yesterday' : "$days days ago";
        } elseif ($weeks <= 4.3) {
            return ($weeks == 1) ? 'a week ago' : "$weeks weeks ago";
        } elseif ($months <= 12) {
            return ($months == 1) ? 'a month ago' : "$months months ago";
        } else {
            return ($years == 1) ? 'one year ago' : "$years years ago";
        }
    }

    public static function thousandsCurrencyFormat($num)
    {

        if ($num >= 1000) {
            return number_format($num, 2);
            $x = round($num);
            $x_number_format = number_format($x);
            $x_array = explode(',', $x_number_format);
            $x_parts = ['k', 'm', 'b', 't'];
            $x_count_parts = count($x_array) - 1;
            // dd($x);
            $x_display = number_format($x, 2);
            $x_display = $x_array[0].((int) $x_array[1][0] !== 0 ? '.'.$x_array[1][0] : '');
            $x_display .= $x_parts[$x_count_parts - 1];

            $x_display = round($num / 1000, 1);
            $x_display .= $x_parts[0];

            return $x_number_format; // $x_display;

        }

        return number_format($num, 2);

        if ($num >= 1000) {

            $units = ['k', 'm', 'b', 't'];
            $power = floor(log($num, 1000));

            $value = $num / pow(1000, $power);

            return round($value, 1).$units[$power - 1];
        }

        return number_format($num, 2);

    }

    /** Format a number as compact K/M (e.g. 24500 → "24.5K", 1200000 → "1.2M") */
    public static function formatK($num): string
    {
        $num = (float) $num;
        if ($num >= 1_000_000) {
            $val = $num / 1_000_000;

            return rtrim(rtrim(number_format($val, 1), '0'), '.').'M';
        }
        if ($num >= 1_000) {
            $val = $num / 1_000;

            return rtrim(rtrim(number_format($val, 1), '0'), '.').'K';
        }

        return number_format($num, 0);
    }

    public static function salaryMode()
    {
        $array = [
            0 => 'Annual',
            1 => 'Monthly',
            2 => 'Hourly',
        ];

        return $array;
    }

    public static function salaryModeOffer()
    {
        $array = [
            0 => 'Annual',
            1 => 'Monthly',
            2 => 'Hourly',
            3 => 'Annual',
            4 => 'Annual',
        ];

        return $array;
    }

    public static function getAllCountryList()
    {
        $countriesArray = json_decode(file_get_contents(public_path('country-state-city/countries.json')), true)['countries'];

        return $countriesArray;

    }

    public static function statelist($country)
    {
        // $statesArray = json_decode(file_get_contents(public_path('country-state-city/states.json')), true)['states'];
        $countryId = $country;

        // dd($statesArray);

        $statesArray = DB::table('state_list')->select('state_list.id', 'state_list.state as name', 'state_list.country_id')->where('state_list.country_id', $country)->orderBy('state_list.state')->get()->map(function ($item, $key) {
            return [
                'id' => (string) ($key + 1),
                'name' => $item->name,
                'country_id' => (string) $item->country_id,
            ];
        })->toArray();

        // $ddydy  = (json_encode($statesArrayq ));

        // dd($statesArrayq,$statesArray);

        $filteredStates = array_filter($statesArray, function ($value) use ($countryId) {
            return $value['country_id'] == $countryId;
        });
        // dd($filteredStates);

        return $filteredStates;
    }

    public static function getCountryName($country)
    {
        // dd($country);
        if (! empty($country)) {
            $exists = DB::table('countries')->select('countries.*')->where('countries.id', $country)->first();
            if ($exists == null) {
                // dd($country);
                $filteredStates = [];

                return $filteredStates;

            }
            $countriesArray = json_decode(file_get_contents(public_path('country-state-city/countries.json')), true)['countries'];
            $countryId = $country;

            $countriesArray = DB::table('countries')->select('countries.*')->where('countries.id', $countryId)->orderBy('countries.country_name')->get()->map(function ($item, $key) {
                return [
                    'id' => (int) @$item->id,
                    'name' => @$item->country_name,
                    'sortname' => (string) @$item->country_code,
                ];
            })->toArray();

            $filteredStates = array_filter($countriesArray, function ($value) use ($countryId) {
                if ($value['id'] == $countryId) {
                    return $value;
                }
            });
        } else {
            $filteredStates = [];
        }

        // dd($filteredStates);
        return $filteredStates;
    }

    public static function getStateName($country, $state)
    {

        $exists = DB::table('state_list')->select('state_list.*')->where('state_list.id', $state)->where('state_list.country_id', $country)->first();
        if ($exists == null) {
            // dd($country);
            $filteredStates = [];

            return $filteredStates;

        }

        $statesArray = json_decode(file_get_contents(public_path('country-state-city/states.json')), true)['states'];
        $countryId = $country;

        $statesArray = DB::table('state_list')->select('state_list.*')->where('state_list.id', $state)->where('state_list.country_id', $countryId)->orderBy('state_list.state')->get()->map(function ($item, $key) {
            return [
                'id' => (int) $item->id,
                'country_id' => (int) $item->country_id,
                'state' => $item->state,
                'name' => $item->state,
                'image' => (string) $item->image,
            ];
        })->toArray();

        // dd($statesArray );

        $filteredStates = array_filter($statesArray, function ($value) use ($countryId) {
            if ($value['country_id'] == $countryId) {
                return $value;
            }
        });

        return $filteredStates;
    }

    public static function companyType()
    {
        $array = [
            0 => 'Private',
            1 => 'Public',
            2 => 'Joint-Venture',
            3 => 'Partnership Firm',
            4 => 'One Person',
            5 => 'Sole Proprietorship',
            6 => 'Branch Office',
            7 => 'Non-Government Organization',
        ];

        return $array;
    }

    public static function parentCompanyType()
    {
        $array = [
            0 => 'Other Companies',
            1 => 'Staffing Partners',
            2 => 'Consulting',
        ];

        return $array;
    }

    public static function defaultRoles()
    {
        $array = [
            0 => 'Administrator',
            1 => 'Sub Administrator',
            2 => 'Team Lead',
            3 => 'Recruiter',
            4 => 'Interviewer',
        ];
    }

    public static function accountCompanyType()
    {
        $array = [
            1 => 'Staffing Companies',
            2 => 'Consulting or Service Companies',
            3 => 'Corporate & Enterprise Companies',
        ];

        return $array;
    }

    public static function industries_type_arr()
    {
        $array = [
            0 => 'Public',
            1 => 'Private',
            2 => 'Joint-Venture',
            3 => 'Partnership Firm',
            4 => 'One Person',
            5 => 'Sole Proprietorship',
            6 => 'Branch Office',
            7 => 'Non-Government Organization',
        ];

        return $array;
    }

    public static function sentEmailUsingSMTP($to, $cc, $subject, $message, $from)
    {
        // dd($to, $cc, $subject, $message, $from);

        $MAIL_DRIVER = $_ENV['MAIL_DRIVER'];
        $MAIL_HOST = $_ENV['MAIL_HOST'];
        $MAIL_PORT = $_ENV['MAIL_PORT'];
        $MAIL_USERNAME = $_ENV['MAIL_USERNAME'];
        $MAIL_PASSWORD = $_ENV['MAIL_PASSWORD'];

        // Always set content-type when sending HTML email
        $headers = 'MIME-Version: 1.0'."\r\n";
        // $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1'."\r\n";

        // More headers
        $headers .= 'From: <'.$from.'>'."\r\n";

        if (! empty($cc)) {
            $headers .= 'Cc: '.$cc.''."\r\n";
        }

        $mail = mail($to, $subject, $message, $headers);

        if ($mail) {
            return true;
        } else {
            return false;
        }

    }

    public static function mail_attachment_old($filename, $raw_filename, $mailto, $from_mail, $from_name, $replyto, $bcc, $subject, $message)
    {

        $files = $filename;
        $to = $mailto;
        $from = $from_mail;
        $headers = "From: $from";

        $semi_rand = md5(time());
        $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";
        $headers .= "\nMIME-Version: 1.0\n"."Content-Type: multipart/mixed;\n"." boundary=\"{$mime_boundary}\"";

        $message = "This is a multi-part message in MIME format.\n\n"."--{$mime_boundary}\n"."Content-Type: text/plain; charset=\"iso-8859-1\"\n"."Content-Transfer-Encoding: 7bit\n\n".$message."\n\n";
        $message .= "--{$mime_boundary}\n";

        for ($x = 0; $x < count($files); $x++) {
            $file = fopen($files[$x], 'rb');
            $data = fread($file, filesize($files[$x]));
            fclose($file);
            $data = chunk_split(base64_encode($data));
            $message .= "Content-Type: {\"application/octet-stream\"};\n"." name=\"$raw_filename[$x]\"\n".
            "Content-Disposition: attachment;\n"." filename=\"$files[$x]\"\n".
            "Content-Transfer-Encoding: base64\n\n".$data."\n\n";
            $message .= "--{$mime_boundary}\n";
        }
        $ok = @mail($to, $subject, $message, $headers);
        if ($ok) {
            return true;
        } else {
            return false;
        }

    }

    public static function mail_attachment($filename, $raw_filename, $mailto, $from_mail, $from_name, $replyto, $bcc, $subject, $message)
    {

        // Sender info
        $from = $from_name.' <'.$from_mail.'>';
        $headers = "From: $from";

        // Boundary
        $semi_rand = md5(time());
        $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";

        // Headers for attachment
        $headers .= "\nMIME-Version: 1.0\n"."Content-Type: multipart/mixed;\n"." boundary=\"{$mime_boundary}\"";

        // Multipart boundary
        $message = "--{$mime_boundary}\n"."Content-Type: text/html; charset=\"UTF-8\"\n".
                                    "Content-Transfer-Encoding: 7bit\n\n".$message."\n\n";

        // Preparing attachment
        if (! empty($filename)) {
            for ($i = 0; $i < count($filename); $i++) {
                if (is_file($filename[$i])) {

                    $file_name = basename($filename[$i]);
                    $file_size = filesize($filename[$i]);
                    $message .= "--{$mime_boundary}\n";
                    $fp = @fopen($filename[$i], 'rb');
                    $data = @fread($fp, $file_size);
                    @fclose($fp);
                    $data = chunk_split(base64_encode($data));
                    $message .= 'Content-Type: application/octet-stream; name="'.$file_name."\"\n".
                    'Content-Description: '.$file_name."\n".
                    "Content-Disposition: attachment;\n".' filename="'.$file_name.'"; size='.$file_size.";\n".
                    "Content-Transfer-Encoding: base64\n\n".$data."\n\n";
                }
            }
        }

        $message .= "--{$mime_boundary}--";
        $returnpath = '-f'.$from_mail;

        // Send email
        $mail = mail($mailto, $subject, $message, $headers, $returnpath);

        // Return true if email sent, otherwise return false
        if ($mail) {
            return true;
        } else {
            return false;
        }

    }

    public static function UpdateTimeline($timelineArr)
    {

        $date = date('Y-m-d H:i:s');
        $application_cnt = 0;
        // $application_cnt		= DB::table('job_applications')->select('*')->where('id', $application_id)->count();
        // if($application_cnt > 0)
        // {
        // $application_Row	= DB::table('job_applications')->select('email')->where('id', $application_id)->first();
        $values = [
            'event_id' => @$timelineArr['event_id'] ? @$timelineArr['event_id'] : null,
            'event_by_company_id' => @$timelineArr['event_by_company_id'] ? @$timelineArr['event_by_company_id'] : null,
            'event_by_user_id' => @$timelineArr['event_by_user_id'] ? @$timelineArr['event_by_user_id'] : null,
            'master_process_name' => @$timelineArr['master_process_name'] ? @$timelineArr['master_process_name'] : null,
            'child_process_name' => @$timelineArr['child_process_name'] ? @$timelineArr['child_process_name'] : null,

            'application_id' => @$timelineArr['application_id'] ? @$timelineArr['application_id'] : null,
            'application_email' => @$timelineArr['application_email'] ? @$timelineArr['application_email'] : null,
            'job_id' => @$timelineArr['job_id'] ? @$timelineArr['job_id'] : null,
            'note' => @$timelineArr['note'] ? @$timelineArr['note'] : null,

            'create_at' => @$date,
            'update_at' => @$date,

            'prev_process_name' => @$timelineArr['prev_process_name'] ? @$timelineArr['prev_process_name'] : null,
            'prev_process_child_name' => @$timelineArr['prev_process_child_name'] ? @$timelineArr['prev_process_child_name'] : null,

            'interview_schedule_date' => @$timelineArr['interview_schedule_date'] ? @$timelineArr['interview_schedule_date'] : null,
            'interview_from_time' => @$timelineArr['interview_from_time'] ? @$timelineArr['interview_from_time'] : null,
            'interview_to_time' => @$timelineArr['interview_to_time'] ? @$timelineArr['interview_to_time'] : null,
            'interview_mode' => @$timelineArr['interview_mode'] ? $timelineArr['interview_mode'] : null,
            'interview_mode_child_level' => @$timelineArr['interview_mode_child_level'] ? @$timelineArr['interview_mode_child_level'] : null,

            'interview_mention_link' => @$timelineArr['interview_mention_link'] ? @$timelineArr['interview_mention_link'] : null,
            'interviewers' => @$timelineArr['interviewers'] ? @$timelineArr['interviewers'] : null,
            'interview_additional_email' => @$timelineArr['interview_additional_email'] ? @$timelineArr['interview_additional_email'] : null,
            'interview_message' => @$timelineArr['interview_message'] ? @$timelineArr['interview_message'] : null,
            'comment' => @$timelineArr['comment'] ? @$timelineArr['comment'] : null,

            'interview_schedules_id' => @$timelineArr['interview_schedules_id'] ? @$timelineArr['interview_schedules_id'] : null,
            'average_star_rating' => @$timelineArr['average_star_rating'] ? @$timelineArr['average_star_rating'] : null,

            'offer_currency' => @$timelineArr['offer_currency'] ? @$timelineArr['offer_currency'] : null,
            'offer_mode' => @$timelineArr['offer_mode'] ? @$timelineArr['offer_mode'] : null,
            'offer_amount' => @$timelineArr['offer_amount'] ? @$timelineArr['offer_amount'] : null,
            'joined_date' => @$timelineArr['joined_date'] ? @$timelineArr['joined_date'] : null,
            'candidate_status' => @$timelineArr['candidate_status'] ? @$timelineArr['candidate_status'] : 0,
            'management_status' => @$timelineArr['management_status'] ? @$timelineArr['management_status'] : 0,
            'accept_date_time' => @$timelineArr['accept_date_time'] ? @$timelineArr['accept_date_time'] : null,
            'approved_date_time' => @$timelineArr['approved_date_time'] ? @$timelineArr['approved_date_time'] : null,
            'send_approved_date_time' => @$timelineArr['send_approved_date_time'] ? @$timelineArr['send_approved_date_time'] : null,
            'send_accept_date_time' => @$timelineArr['send_accept_date_time'] ? @$timelineArr['send_accept_date_time'] : null,
            'email_id_hr' => @$timelineArr['email_id_hr'] ? @$timelineArr['email_id_hr'] : null,
            'name_hr' => @$timelineArr['name_hr'] ? @$timelineArr['name_hr'] : null,
            'yearly_gross' => @$timelineArr['yearly_gross'] ? @$timelineArr['yearly_gross'] : null,
            'yearly_final_gross' => @$timelineArr['yearly_final_gross'] ? @$timelineArr['yearly_final_gross'] : null,
            'mtypeSection' => @$timelineArr['mtypeSection'] ? @$timelineArr['mtypeSection'] : null,
            'designation_offer' => @$timelineArr['designation_offer'] ? @$timelineArr['designation_offer'] : null,
            'timezone_abbreviation' => @$timelineArr['timezone_abbreviation'] ? @$timelineArr['timezone_abbreviation'] : null,
            'confidential' => @$timelineArr['confidential'] ? @$timelineArr['confidential'] : null,

        ];
        $updatetimelineId = DB::table('updatetimeline')->insertGetId($values);

        $elasticService = app(ElasticService::class);

        $params = [
            'index' => ELASTIC_JOBS,
            'id' => @$timelineArr['job_id'] ? @$timelineArr['job_id'] : 0,
        ];

        $response = $elasticService->getClient()->get($params);

        $job_details = isset($response['_source']) ? (object) $response['_source'] : null;

        $params = [
            'index' => ELASTIC_USERS_LIST,
            'id' => @$timelineArr['event_by_user_id'] ? @$timelineArr['event_by_user_id'] : 0,
        ];

        $response = $elasticService->getClient()->get($params);

        $user_details = isset($response['_source']) ? (object) $response['_source'] : null;

        $interview_details = null;

        // Safely extract interview_schedules_id
        $interviewScheduleId = $timelineArr['interview_schedules_id'] ?? null;

        if (! empty($interviewScheduleId)) {
            try {
                $response = $elasticService->getClient()->get([
                    'index' => ELASTIC_INERTVIEW_TRACKER,
                    'id' => $interviewScheduleId,
                ]);

                $interview_details = isset($response['_source'])
                    ? (object) $response['_source']
                    : null;

            } catch (Missing404Exception $e) {
                // Document not found — this is OK
                $interview_details = null;

            } catch (\Throwable $e) {
                // Log only real failures (ES down, timeout, etc.)
                // \Log::error('Elastic interview tracker fetch failed', [
                // 	'index' => ELASTIC_INERTVIEW_TRACKER,
                // 	'id'    => $interviewScheduleId,
                // 	'error' => $e->getMessage(),
                // ]);

                $interview_details = null;
            }
        }

        $status_details = null;

        if (! empty($interview_details->stage ?? null)) {
            try {
                $response = $elasticService->getClient()->get([
                    'index' => ELASTIC_MASTER_STATUS,
                    'id' => $interview_details->stage ?? null,
                ]);

                $status_details = isset($response['_source'])
                    ? (object) $response['_source']
                    : null;

            } catch (Missing404Exception $e) {
                // Document not found — this is OK
                $status_details = null;

            } catch (\Throwable $e) {
                // Log only real failures (ES down, timeout, etc.)
                // \Log::error('Elastic interview tracker fetch failed', [
                // 	'index' => ELASTIC_MASTER_STATUS,
                // 	'id'    => $interview_details?->stage,
                // 	'error' => $e->getMessage(),
                // ]);

                $status_details = null;
            }
        }

        $params = [
            'index' => ELASTIC_CLIENT_LIST,
            'id' => $job_details->client_id,
        ];

        $response = $elasticService->getClient()->get($params);

        $client_details = isset($response['_source']) ? (object) $response['_source'] : null;

        $params = [
            'index' => ELASTIC_COMPANIES_INDEX,
            'id' => $user_details->company_id,
        ];

        $response = $elasticService->getClient()->get($params);

        $company_details = isset($response['_source']) ? (object) $response['_source'] : null;

        // return users

        $response = $elasticService->getClient()->update([
            'index' => ELASTIC_CANDIDATE_TIMELINE,
            'id' => $updatetimelineId,
            'retry_on_conflict' => 5,
            'body' => [
                'doc' => [
                    'id' => $updatetimelineId,
                    'event_id' => @$timelineArr['event_id'] ? @$timelineArr['event_id'] : null,
                    'event_by_company_id' => @$timelineArr['event_by_company_id'] ? @$timelineArr['event_by_company_id'] : null,
                    'event_by_user_id' => @$timelineArr['event_by_user_id'] ? @$timelineArr['event_by_user_id'] : null,
                    'master_process_name' => @$timelineArr['master_process_name'] ? @$timelineArr['master_process_name'] : null,
                    'child_process_name' => @$timelineArr['child_process_name'] ? @$timelineArr['child_process_name'] : null,
                    'application_id' => @$timelineArr['application_id'] ? @$timelineArr['application_id'] : null,
                    'application_email' => @$timelineArr['application_email'] ? @$timelineArr['application_email'] : null,
                    'job_id' => @$timelineArr['job_id'] ? @$timelineArr['job_id'] : null,
                    'note' => @$timelineArr['note'] ? @$timelineArr['note'] : null,
                    'interview_schedule_date' => (@$timelineArr['interview_schedule_date']) ? Carbon::parse(@$timelineArr['interview_schedule_date'])->toIso8601String() : null,
                    'interview_from_time' => (@$timelineArr['interview_from_time']) ? Carbon::parse(@$timelineArr['interview_from_time'])->toIso8601String() : null,
                    'interview_to_time' => (@$timelineArr['interview_to_time']) ? Carbon::parse(@$timelineArr['interview_to_time'])->toIso8601String() : null,
                    'interview_mode' => @$timelineArr['interview_mode'] ? $timelineArr['interview_mode'] : null,
                    'interview_mode_child_level' => @$timelineArr['interview_mode_child_level'] ? @$timelineArr['interview_mode_child_level'] : null,
                    'interview_mention_link' => @$timelineArr['interview_mention_link'] ? @$timelineArr['interview_mention_link'] : null,
                    'interviewers' => @$timelineArr['interviewers'] ? @$timelineArr['interviewers'] : null,
                    'interview_additional_email' => @$timelineArr['interview_additional_email'] ? @$timelineArr['interview_additional_email'] : null,
                    'interview_message' => @$timelineArr['interview_message'] ? @$timelineArr['interview_message'] : null,
                    'comment' => @$timelineArr['comment'] ? @$timelineArr['comment'] : null,
                    'interview_schedules_id' => @$timelineArr['interview_schedules_id'] ? @$timelineArr['interview_schedules_id'] : null,
                    'average_star_rating' => @$timelineArr['average_star_rating'] ? @$timelineArr['average_star_rating'] : null,
                    'prev_process_name' => @$timelineArr['prev_process_name'] ? @$timelineArr['prev_process_name'] : null,
                    'prev_process_child_name' => @$timelineArr['prev_process_child_name'] ? @$timelineArr['prev_process_child_name'] : null,
                    'offer_currency' => @$timelineArr['offer_currency'] ? @$timelineArr['offer_currency'] : null,
                    'offer_mode' => @$timelineArr['offer_mode'] ? @$timelineArr['offer_mode'] : null,
                    'offer_amount' => @$timelineArr['offer_amount'] ? (float) @$timelineArr['offer_amount'] : null,
                    'joined_date' => (@$timelineArr['joined_date']) ? Carbon::parse(@$timelineArr['joined_date'])->toIso8601String() : null,
                    'candidate_status' => @$timelineArr['candidate_status'] ? @$timelineArr['candidate_status'] : 0,
                    'management_status' => @$timelineArr['management_status'] ? @$timelineArr['management_status'] : 0,
                    'accept_date_time' => (@$timelineArr['accept_date_time']) ? Carbon::parse(@$timelineArr['accept_date_time'])->toIso8601String() : null,
                    'approved_date_time' => (@$timelineArr['approved_date_time']) ? Carbon::parse(@$timelineArr['approved_date_time'])->toIso8601String() : null,
                    'send_approved_date_time' => (@$timelineArr['send_approved_date_time']) ? Carbon::parse(@$timelineArr['send_approved_date_time'])->toIso8601String() : null,
                    'send_accept_date_time' => (@$timelineArr['send_accept_date_time']) ? Carbon::parse(@$timelineArr['send_accept_date_time'])->toIso8601String() : null,
                    'email_id_hr' => @$timelineArr['email_id_hr'] ? @$timelineArr['email_id_hr'] : null,
                    'name_hr' => @$timelineArr['email_id_hr'] ? @$timelineArr['email_id_hr'] : null,
                    'yearly_gross' => @$timelineArr['yearly_gross'] ? @$timelineArr['yearly_gross'] : null,
                    'yearly_final_gross' => @$timelineArr['yearly_final_gross'] ? @$timelineArr['yearly_final_gross'] : null,
                    'mtypeSection' => @$timelineArr['mtypeSection'] ? @$timelineArr['mtypeSection'] : null,
                    'designation_offer' => @$timelineArr['designation_offer'] ? @$timelineArr['designation_offer'] : null,
                    'timezone_abbreviation' => @$timelineArr['timezone_abbreviation'] ? @$timelineArr['timezone_abbreviation'] : null,
                    'confidential' => @$timelineArr['confidential'] ? @$timelineArr['confidential'] : null,
                    'event_name' => null,
                    'title' => $job_details->title ?? null,
                    'employment_type' => $job_details->employment_type ?? null,
                    'name' => $user_details->name ?? null,
                    'image' => $user_details->image ?? null,
                    'client_name' => $client_details->client_name ?? null,
                    'available_status' => $interview_details->stage ?? null,
                    'reschedule' => ! empty($interview_details->reschedule ?? null) ? Carbon::parse($interview_details->reschedule)->toIso8601String() : null,
                    'feedback' => $interview_details->feedback ?? null,
                    'stage' => $interview_details->stage ?? null,
                    'status_name' => $status_details->name ?? null,
                    'cur_companyname' => $company_details->company_name ?? null,
                    'css_style' => $status_details->css_style ?? null,
                    'updated_at' => now()->toIso8601String(),
                    'created_at' => now()->toIso8601String(),
                ],
                'doc_as_upsert' => true,
                'detect_noop' => false,
            ],
        ]);

        $updateTime = now()->toIso8601String();

        if (@$timelineArr['master_process_name'] == 'Sourcing' || @$timelineArr['master_process_name'] == 'Joined') {

            /* ===================== USERS ===================== */

            $userId = $timelineArr['event_by_user_id'] ?? null;

            if ($userId) {

                $exists = $elasticService->getClient()->exists([
                    'index' => ELASTIC_USERS_LIST,
                    'id' => $userId,
                ]);

                if ($exists) {

                    $existingDoc = $elasticService->getClient()->get([
                        'index' => ELASTIC_USERS_LIST,
                        'id' => $userId,
                    ]);

                    $candidateRelations = $existingDoc['_source']['candidate_relation'] ?? [];

                    $newRelation = [
                        'application_id' => $timelineArr['application_id'] ?? null,
                        'master_process_name' => $timelineArr['master_process_name'] ?? null,
                        'job_id' => $timelineArr['job_id'] ?? null,
                        'created_at' => $updateTime,
                        'updated_at' => $updateTime,
                    ];

                    /* ✅ Prevent Duplicate */
                    $existsAlready = false;
                    foreach ($candidateRelations as $relation) {
                        if (($relation['application_id'] ?? null) == $newRelation['application_id']) {
                            $existsAlready = true;
                            break;
                        }
                    }

                    if (! $existsAlready) {
                        $candidateRelations[] = $newRelation;
                    }

                    /* ✅ Limit Size (VERY IMPORTANT) */
                    $MAX_RELATIONS = 3000;
                    if (count($candidateRelations) > $MAX_RELATIONS) {
                        $candidateRelations = array_slice($candidateRelations, -$MAX_RELATIONS);
                    }

                    $updateParams = [
                        'index' => ELASTIC_USERS_LIST,
                        'id' => $userId,
                        'retry_on_conflict' => 5,
                        'body' => [
                            'doc' => [
                                'candidate_relation' => $candidateRelations,
                            ],
                            'doc_as_upsert' => true,
                            'detect_noop' => false,
                        ],
                    ];

                    $elasticService->getClient()->update($updateParams);
                }
            }

            /* ===================== CLIENT ===================== */

            $clientDetails = DB::table('clients')
                ->where('clients.company_id', '=', @$timelineArr['event_by_company_id'])
                ->where('self_company', '=', 1)
                ->first();

            if ($clientDetails) {

                $client_id = $clientDetails->id;

                $exists = $elasticService->getClient()->exists([
                    'index' => ELASTIC_CLIENT_LIST,
                    'id' => $client_id,
                ]);

                if ($exists) {

                    $clientExistingDoc = $elasticService->getClient()->get([
                        'index' => ELASTIC_CLIENT_LIST,
                        'id' => $client_id,
                    ]);

                    $clientCandidateRelations = $clientExistingDoc['_source']['candidate_relation'] ?? [];

                    $newRelation = [
                        'application_id' => $timelineArr['application_id'] ?? null,
                        'master_process_name' => $timelineArr['master_process_name'] ?? null,
                        'job_id' => $timelineArr['job_id'] ?? null,
                        'created_at' => $updateTime,
                        'updated_at' => $updateTime,
                    ];

                    /* ✅ Prevent Duplicate */
                    $existsAlready = false;
                    foreach ($clientCandidateRelations as $relation) {
                        if (($relation['application_id'] ?? null) == $newRelation['application_id']) {
                            $existsAlready = true;
                            break;
                        }
                    }

                    if (! $existsAlready) {
                        $clientCandidateRelations[] = $newRelation;
                    }

                    /* ✅ Limit Size */
                    $MAX_RELATIONS = 3000;
                    if (count($clientCandidateRelations) > $MAX_RELATIONS) {
                        $clientCandidateRelations = array_slice($clientCandidateRelations, -$MAX_RELATIONS);
                    }

                    $updateParams = [
                        'index' => ELASTIC_CLIENT_LIST,
                        'id' => $client_id,
                        'retry_on_conflict' => 5,
                        'body' => [
                            'doc' => [
                                'candidate_relation' => $clientCandidateRelations,
                            ],
                            'doc_as_upsert' => true,
                            'detect_noop' => false,
                        ],
                    ];

                    $elasticService->getClient()->update($updateParams);
                }
            }
        }

        // }

    }

    public static function getApplicantBenchChain($source_application_id, $source_jobid, $source_company_id, $type)
    {
        $benchAppRow = DB::table('rec_candidate_assign');
        $benchAppRow = $benchAppRow->select(
            'rec_candidate_assign.id', 'rec_candidate_assign.parent_id', 'rec_candidate_assign.source_from_company_id', 'rec_candidate_assign.source_from_user_id',
            'rec_candidate_assign.source_from_id', 'rec_candidate_assign.source_from_type', 'rec_candidate_assign.source_company_id', 'rec_candidate_assign.source_user_id',
            'rec_candidate_assign.source_application_id', 'rec_candidate_assign.source_jobid',

            'bench_applications.id as bench_applications_id',
            'bench_applications.company_id',
            'bench_applications.submitted_by'

        );
        $benchAppRow = $benchAppRow->join('bench_applications', 'bench_applications.id', '=', 'rec_candidate_assign.source_from_id');
        $benchAppRow = $benchAppRow->where('rec_candidate_assign.source_application_id', $source_application_id);
        $benchAppRow = $benchAppRow->where('rec_candidate_assign.source_jobid', $source_jobid);
        $benchAppRow = $benchAppRow->where('rec_candidate_assign.source_company_id', $source_company_id);
        $benchAppRow = $benchAppRow->where('rec_candidate_assign.source_from_type', 1);
        $benchAppRow = $benchAppRow->where('bench_applications.company_id', '!=', $source_company_id);
        if ($type == 'Cnt') {
            $benchAppRow = $benchAppRow->count();
        } else {
            $benchAppRow = $benchAppRow->first();
        }

        return $benchAppRow;
    }

    public static function checksubscription()
    {
        $company_id = auth()->user()->company_id;
        $package_active_row = DB::table('package_active')->select('*')->where('company_id', $company_id)->first();
        $end_date = $package_active_row->enddate;
        $current_date = date('Y-m-d');
        $end_date = Carbon::parse($end_date);
        $current_date = Carbon::parse($current_date);

        if ($end_date->gt($current_date) && $package_active_row->packages_status == 1) {
            // subscription end date is greater than
            return true;
        } elseif ($end_date->lt($current_date)) {
            // subscription end date is less than
            return true; // false;
        } else {
            // subscription end date and current date is equal
            if ($package_active_row->packages_status == 1) {
                return true;
            }

        }
    }

    public static function validatePackage($event)
    {

        // dd($event);
        $company_id = auth()->user()->company_id;

        $package_active = 0;
        $package_active_cnt = DB::table('package_active')->select('*')->where('company_id', $company_id)->count();

        $statusArr = [];
        $statusArr['EVENT'] = $event;
        $statusArr['STATUS'] = 0;
        $statusArr['EXPIRE'] = 'true';
        $statusArr['BALANCE'] = 0;

        if ($package_active_cnt > 0) {

            $package_active_row = '';
            $package_active_row = DB::table('package_active')->select('*')->where('company_id', $company_id)->first();
            // dd($package_active_row);
            if ($event == 'USERS') {
                $statusArr['STATUS'] = $package_active_row->users_status;
                $users_startdate = strtotime($package_active_row->startdate);
                $users_enddate = strtotime($package_active_row->enddate);
                $current = strtotime(date('Y-m-d'));
                if (($users_startdate <= $current) && ($users_enddate >= $current)) {
                    $statusArr['EXPIRE'] = 'false';
                }
                $statusArr['BALANCE'] = $package_active_row->users_balance;
                // 	dd($statusArr);
            } elseif ($event == 'CVACCESS') {

                $statusArr['STATUS'] = $package_active_row->packages_status;
                $users_startdate = strtotime($package_active_row->startdate);
                $users_enddate = strtotime($package_active_row->enddate);
                $current = strtotime(date('Y-m-d'));
                if (($users_startdate <= $current) && ($users_enddate >= $current)) {
                    $statusArr['EXPIRE'] = 'false';
                }
                $statusArr['BALANCE'] = $package_active_row->cvaccess_balance;

            } elseif ($event == 'NEW_BENCH_CLIENT_REQUEST') {

                $statusArr['STATUS'] = $package_active_row->BenchClientAccessInternational_status + $package_active_row->BenchClientAcccessIndia_status;
                $users_startdate = strtotime($package_active_row->startdate);
                $users_enddate = strtotime($package_active_row->enddate);
                $current = strtotime(date('Y-m-d'));
                if (($users_startdate <= $current) && ($users_enddate >= $current)) {
                    $statusArr['EXPIRE'] = 'false';
                }
                $statusArr['BALANCE'] = $package_active_row->BenchClientAcccessIndia_balance + $package_active_row->BenchClientAccessInternational_balance;
            }

        }

        return $statusArr;

    }

    public static function validatePackageRet($event)
    {

        $company_id = auth()->user()->company_id;

        $package_active = 0;
        $package_active_cnt = DB::table('package_active')->select('*')->where('company_id', $company_id)->count();

        $statusArr = [];
        $statusArr['EVENT'] = $event;
        $statusArr['STATUS'] = 0;
        $statusArr['EXPIRE'] = 'true';
        $statusArr['BALANCE'] = 0;

        $validatePackageRet = 0;

        if ($package_active_cnt > 0) {

            $package_active_row = '';
            $package_active_row = DB::table('package_active')->select('*')->where('company_id', $company_id)->first();

            // echo '<pre>'; print_r($package_active_row); exit;

            if ($event == 'USERS') {
                $statusArr['STATUS'] = $package_active_row->users_status;
                $users_startdate = strtotime($package_active_row->users_startdate);
                $users_enddate = strtotime($package_active_row->users_enddate);
                $current = strtotime(date('Y-m-d'));
                if (($users_startdate <= $current) && ($users_enddate >= $current)) {
                    $statusArr['EXPIRE'] = 'false';
                }
                $statusArr['BALANCE'] = $package_active_row->users_balance;

            } elseif ($event == 'CVACCESS') {

                $statusArr['STATUS'] = $package_active_row->BenchSalesIndia_status;
                $users_startdate = strtotime($package_active_row->startdate);
                $users_enddate = strtotime($package_active_row->enddate);
                $current = strtotime(date('Y-m-d'));
                if (($users_startdate <= $current) && ($users_enddate >= $current)) {
                    $statusArr['EXPIRE'] = 'false';
                }
                $statusArr['BALANCE'] = $package_active_row->BenchSalesIndia_balance;

                if ($statusArr['STATUS'] == 1) {
                    if ($statusArr['EXPIRE'] == 'false') {
                        if ($statusArr['BALANCE'] > 0) {
                            $validatePackageRet = 1;
                        } else {
                            $validatePackageRet = 0;
                        }
                    } else {
                        $validatePackageRet = 0;
                    }
                } else {
                    $validatePackageRet = 0;
                }

            } elseif ($event == 'BenchSalesIndia') {

                $statusArr['STATUS'] = $package_active_row->cvaccess_status;
                $users_startdate = strtotime($package_active_row->cvaccess_startdate);
                $users_enddate = strtotime($package_active_row->cvaccess_enddate);
                $current = strtotime(date('Y-m-d'));
                if (($users_startdate <= $current) && ($users_enddate >= $current)) {
                    $statusArr['EXPIRE'] = 'false';
                }
                $statusArr['BALANCE'] = $package_active_row->cvaccess_balance;

                if ($statusArr['STATUS'] == 1) {
                    if ($statusArr['EXPIRE'] == 'false') {
                        if ($statusArr['BALANCE'] > 0) {
                            $validatePackageRet = 1;
                        } else {
                            $validatePackageRet = 0;
                        }
                    } else {
                        $validatePackageRet = 0;
                    }
                } else {
                    $validatePackageRet = 0;
                }

            } elseif ($event == 'BenchSalesInternational') {

                $statusArr['STATUS'] = $package_active_row->BenchSalesInternational_status;
                $users_startdate = strtotime($package_active_row->startdate);
                $users_enddate = strtotime($package_active_row->enddate);
                $current = strtotime(date('Y-m-d'));
                if (($users_startdate <= $current) && ($users_enddate >= $current)) {
                    $statusArr['EXPIRE'] = 'false';
                }
                $statusArr['BALANCE'] = $package_active_row->BenchSalesInternational_balance;

                if ($statusArr['STATUS'] == 1) {
                    if ($statusArr['EXPIRE'] == 'false') {
                        if ($statusArr['BALANCE'] > 0) {
                            $validatePackageRet = 1;
                        } else {
                            $validatePackageRet = 0;
                        }
                    } else {
                        $validatePackageRet = 0;
                    }
                } else {
                    $validatePackageRet = 0;
                }

            }

        }

        return $validatePackageRet;

    }

    public static function packageRedeem($event_name, $event_table_name, $event_table_id, $source_to_company_id = '')
    {

        $company_id = $source_to_company_id ?: (auth()->user()?->company_id ?? null);
        if (! $company_id) {
            return false;
        }

        $package_active = 0;
        $package_active_cnt = DB::table('package_active')->select('*')->where('company_id', $company_id)->count();
        if (($package_active_cnt > 0) && ($event_name != '')) {
            $package_active_row = '';
            $package_active_row = DB::table('package_active')->select('*')->where('company_id', $company_id)->first();

            $f_id = DB::table('package_features')->select('id')->where('field_name', $event_name)->first()->id;

            if ($event_name == 'CVACCESS') {
                $startdate = $enddate = $status = $quantity = $used = $balance = '';

                $startdate = @$package_active_row->startdate;
                $enddate = @$package_active_row->enddate;
                $status = @$package_active_row->cvaccess_status;
                $quantity = @$package_active_row->cvaccess_quantity;
                $used = @$package_active_row->cvaccess_used;
                $balance = @$package_active_row->cvaccess_balance;

                $values = [
                    'package_invoice_id' => @$package_active_row->package_invoice_id,
                    'company_id' => $company_id,
                    'package_master_item' => $f_id,
                    'user_id' => auth()->user()->id,
                    'event_name' => $event_table_name,
                    'event_inserted_id' => @$event_table_id,
                ];
                DB::table('package_transactions')->insert($values);

                $used_cnt = @$package_active_row->cvaccess_used + 1;
                $balance_cnt = @$package_active_row->cvaccess_quantity - $used_cnt;
                DB::table('package_active')
                    ->where('company_id', @$company_id)
                    ->limit(1)
                    ->update(['cvaccess_used' => @$used_cnt,  'cvaccess_balance' => @$balance_cnt]);

            } elseif ($event_name == 'PermamentRecruitmentLeadIndia') {
                // $package_active_row			=	DB::table('package_active')->select('*')->where('company_id', $source_to_company_id)->first();
                $startdate = $enddate = $status = $quantity = $used = $balance = '';

                $startdate = @$package_active_row->startdate;
                $enddate = @$package_active_row->enddate;
                $status = @$package_active_row->PermamentRecruitmentLeadIndia_status;
                $quantity = @$package_active_row->PermamentRecruitmentLeadIndia_quantity;
                $used = @$package_active_row->PermamentRecruitmentLeadIndia_used;
                $balance = @$package_active_row->PermamentRecruitmentLeadIndia_balance;

                $values = [
                    'package_invoice_id' => @$package_active_row->package_invoice_id,
                    'company_id' => $company_id,
                    'package_master_item' => (int) $f_id,
                    'user_id' => auth()->user()->id,
                    'event_name' => $event_table_name,
                    'event_inserted_id' => @$event_table_id,
                ];
                DB::table('package_transactions')->insert($values);

                $used_cnt = @$package_active_row->PermamentRecruitmentLeadIndia_used + 1;
                $balance_cnt = @$package_active_row->PermamentRecruitmentLeadIndia_quantity - $used_cnt;
                DB::table('package_active')
                    ->where('company_id', @$company_id)
                    ->limit(1)
                    ->update(['PermamentRecruitmentLeadIndia_used' => @$used_cnt,  'PermamentRecruitmentLeadIndia_balance' => @$balance_cnt]);

            } elseif ($event_name == 'PermamentRecruitmentLeadInternational') {
                // $package_active_row			=	DB::table('package_active')->select('*')->where('company_id', $source_to_company_id)->first();
                $startdate = $enddate = $status = $quantity = $used = $balance = '';

                $startdate = @$package_active_row->startdate;
                $enddate = @$package_active_row->enddate;
                $status = @$package_active_row->PermamentRecruitmentLeadInternational_status;
                $quantity = @$package_active_row->PermamentRecruitmentLeadInternational_quantity;
                $used = @$package_active_row->PermamentRecruitmentLeadInternational_used;
                $balance = @$package_active_row->PermamentRecruitmentLeadInternational_balance;

                $values = [
                    'package_invoice_id' => @$package_active_row->package_invoice_id,
                    'company_id' => $company_id,
                    'package_master_item' => (int) $f_id,
                    'user_id' => auth()->user()->id,
                    'event_name' => $event_table_name,
                    'event_inserted_id' => @$event_table_id,
                ];
                DB::table('package_transactions')->insert($values);

                $used_cnt = @$package_active_row->PermamentRecruitmentLeadInternational_used + 1;
                $balance_cnt = @$package_active_row->PermamentRecruitmentLeadInternational_quantity - $used_cnt;
                DB::table('package_active')
                    ->where('company_id', @$company_id)
                    ->limit(1)
                    ->update(['PermamentRecruitmentLeadInternational_used' => @$used_cnt,  'PermamentRecruitmentLeadInternational_balance' => @$balance_cnt]);

            } elseif ($event_name == 'ContractRecruitmentLeadIndia') {
                // $package_active_row			=	DB::table('package_active')->select('*')->where('company_id', $source_to_company_id)->first();
                $startdate = $enddate = $status = $quantity = $used = $balance = '';

                $startdate = @$package_active_row->startdate;
                $enddate = @$package_active_row->enddate;
                $status = @$package_active_row->ContractRecruitmentLeadIndia_status;
                $quantity = @$package_active_row->ContractRecruitmentLeadIndia_quantity;
                $used = @$package_active_row->ContractRecruitmentLeadIndia_used;
                $balance = @$package_active_row->ContractRecruitmentLeadIndia_balance;

                $values = [
                    'package_invoice_id' => @$package_active_row->package_invoice_id,
                    'company_id' => $company_id,
                    'package_master_item' => (int) $f_id,
                    'user_id' => auth()->user()->id,
                    'event_name' => $event_table_name,
                    'event_inserted_id' => @$event_table_id,
                ];
                DB::table('package_transactions')->insert($values);

                $used_cnt = @$package_active_row->ContractRecruitmentLeadIndia_used + 1;
                $balance_cnt = @$package_active_row->ContractRecruitmentLeadIndia_quantity - $used_cnt;
                DB::table('package_active')
                    ->where('company_id', @$company_id)
                    ->limit(1)
                    ->update(['ContractRecruitmentLeadIndia_used' => @$used_cnt,  'ContractRecruitmentLeadIndia_balance' => @$balance_cnt]);

            } elseif ($event_name == 'ContractRecruitmentLeadInternational') {
                // $package_active_row			=	DB::table('package_active')->select('*')->where('company_id', $source_to_company_id)->first();
                $startdate = $enddate = $status = $quantity = $used = $balance = '';

                $startdate = @$package_active_row->startdate;
                $enddate = @$package_active_row->enddate;
                $status = @$package_active_row->ContractRecruitmentLeadInternational_status;
                $quantity = @$package_active_row->ContractRecruitmentLeadInternational_quantity;
                $used = @$package_active_row->ContractRecruitmentLeadInternational_used;
                $balance = @$package_active_row->ContractRecruitmentLeadInternational_balance;

                $values = [
                    'package_invoice_id' => @$package_active_row->package_invoice_id,
                    'company_id' => $company_id,
                    'package_master_item' => (int) $f_id,
                    'user_id' => auth()->user()->id,
                    'event_name' => $event_table_name,
                    'event_inserted_id' => @$event_table_id,
                ];
                DB::table('package_transactions')->insert($values);

                $used_cnt = @$package_active_row->ContractRecruitmentLeadInternational_used + 1;
                $balance_cnt = @$package_active_row->ContractRecruitmentLeadInternational_quantity - $used_cnt;
                DB::table('package_active')
                    ->where('company_id', @$company_id)
                    ->limit(1)
                    ->update(['ContractRecruitmentLeadInternational_used' => @$used_cnt,  'ContractRecruitmentLeadInternational_balance' => @$balance_cnt]);

            }

        }

    }

    public static function validateClientPackage($event, $company_id)
    {

        // $company_id 			=	auth()->user()->company_id;

        $package_active = 0;
        $package_active_cnt = DB::table('package_active')->select('*')->where('company_id', $company_id)->count();
        // dd($company_id,$package_active_cnt);
        $statusArr = [];
        $statusArr['EVENT'] = $event;
        $statusArr['STATUS'] = 0;
        $statusArr['EXPIRE'] = 'true';
        $statusArr['BALANCE'] = 0;

        if ($package_active_cnt > 0) {

            $package_active_row = '';
            $package_active_row = DB::table('package_active')->select('*')->where('company_id', $company_id)->first();
            // dd($company_id,$event,$package_active_row);

            // echo '<pre>'; print_r($package_active_row); exit;

            if ($event == 'USERS') {

                $statusArr['STATUS'] = $package_active_row->users_status;
                $users_startdate = strtotime($package_active_row->users_startdate);
                $users_enddate = strtotime($package_active_row->users_enddate);
                $current = strtotime(date('Y-m-d'));
                if (($users_startdate <= $current) && ($users_enddate >= $current)) {
                    $statusArr['EXPIRE'] = 'false';
                }
                $statusArr['BALANCE'] = $package_active_row->users_balance;

            } elseif ($event == 'CVACCESS') {

                $statusArr['STATUS'] = $package_active_row->cvaccess_status;
                $users_startdate = strtotime($package_active_row->cvaccess_startdate);
                $users_enddate = strtotime($package_active_row->cvaccess_enddate);
                $current = strtotime(date('Y-m-d'));
                if (($users_startdate <= $current) && ($users_enddate >= $current)) {
                    $statusArr['EXPIRE'] = 'false';
                }
                $statusArr['BALANCE'] = $package_active_row->cvaccess_balance;
                // dd($statusArr);

            } elseif ($event == 'NEW_BENCH_CLIENT_REQUEST') {

                $statusArr['STATUS'] = $package_active_row->newbenchclientrequest_status;
                $users_startdate = strtotime($package_active_row->newbenchclientrequest_startdate);
                $users_enddate = strtotime($package_active_row->newbenchclientrequest_enddate);
                $current = strtotime(date('Y-m-d'));
                if (($users_startdate <= $current) && ($users_enddate >= $current)) {
                    $statusArr['EXPIRE'] = 'false';
                }
                $statusArr['BALANCE'] = $package_active_row->newbenchclientrequest_balance;

            } elseif ($event == 'BOOSTKEYWORDS') {

                $statusArr['STATUS'] = $package_active_row->boostkeywords_status;
                $users_startdate = strtotime($package_active_row->boostkeywords_startdate);
                $users_enddate = strtotime($package_active_row->boostkeywords_enddate);
                $current = strtotime(date('Y-m-d'));
                if (($users_startdate <= $current) && ($users_enddate >= $current)) {
                    $statusArr['EXPIRE'] = 'false';
                }
                $statusArr['BALANCE'] = $package_active_row->boostkeywords_balance;

            } elseif ($event == 'CONTRACT_RECRUITMENT_LEAD') {
                $statusArr['STATUS'] = $package_active_row->newclientrequest_status;
                $users_startdate = strtotime($package_active_row->newclientrequest_startdate);
                $users_enddate = strtotime($package_active_row->newclientrequest_enddate);
                $current = strtotime(date('Y-m-d'));
                if (($users_startdate <= $current) && ($users_enddate >= $current)) {
                    $statusArr['EXPIRE'] = 'false';
                }
                $statusArr['BALANCE'] = $package_active_row->newclientrequest_balance;

            } elseif ($event == 'FULLTIME_RECRUITMENT_LEAD') {
                $statusArr['STATUS'] = $package_active_row->newjobrequest_status;
                $users_startdate = strtotime($package_active_row->newjobrequest_startdate);
                $users_enddate = strtotime($package_active_row->newjobrequest_enddate);
                $current = strtotime(date('Y-m-d'));
                if (($users_startdate <= $current) && ($users_enddate >= $current)) {
                    $statusArr['EXPIRE'] = 'false';
                }
                $statusArr['BALANCE'] = $package_active_row->newjobrequest_balance;

            } elseif ($event == 'PermamentRecruitmentLeadIndia') {
                $statusArr['STATUS'] = $package_active_row->PermamentRecruitmentLeadIndia_status;
                $users_startdate = strtotime($package_active_row->startdate);
                $users_enddate = strtotime($package_active_row->enddate);
                $current = strtotime(date('Y-m-d'));
                if (($users_startdate <= $current) && ($users_enddate >= $current)) {
                    $statusArr['EXPIRE'] = 'false';
                }
                $statusArr['BALANCE'] = $package_active_row->PermamentRecruitmentLeadIndia_balance;
                // dd($statusArr);
            } elseif ($event == 'PermamentRecruitmentLeadInternational') {
                $statusArr['STATUS'] = $package_active_row->PermamentRecruitmentLeadInternational_status;
                $users_startdate = strtotime($package_active_row->startdate);
                $users_enddate = strtotime($package_active_row->enddate);
                $current = strtotime(date('Y-m-d'));
                if (($users_startdate <= $current) && ($users_enddate >= $current)) {
                    $statusArr['EXPIRE'] = 'false';
                }
                $statusArr['BALANCE'] = $package_active_row->PermamentRecruitmentLeadInternational_balance;
            } elseif ($event == 'ContractRecruitmentLeadIndia') {
                $statusArr['STATUS'] = $package_active_row->ContractRecruitmentLeadIndia_status;
                $users_startdate = strtotime($package_active_row->startdate);
                $users_enddate = strtotime($package_active_row->enddate);
                $current = strtotime(date('Y-m-d'));
                if (($users_startdate <= $current) && ($users_enddate >= $current)) {
                    $statusArr['EXPIRE'] = 'false';
                }
                $statusArr['BALANCE'] = $package_active_row->ContractRecruitmentLeadIndia_balance;
            } elseif ($event == 'ContractRecruitmentLeadInternational') {
                $statusArr['STATUS'] = $package_active_row->ContractRecruitmentLeadInternational_status;
                $users_startdate = strtotime($package_active_row->startdate);
                $users_enddate = strtotime($package_active_row->enddate);
                $current = strtotime(date('Y-m-d'));
                if (($users_startdate <= $current) && ($users_enddate >= $current)) {
                    $statusArr['EXPIRE'] = 'false';
                }
                $statusArr['BALANCE'] = $package_active_row->ContractRecruitmentLeadInternational_balance;
            } elseif ($event == 'BoostKeywordsIndia') {
                $statusArr['STATUS'] = $package_active_row->BoostKeywordsIndia_status;
                $users_startdate = strtotime($package_active_row->startdate);
                $users_enddate = strtotime($package_active_row->enddate);
                $current = strtotime(date('Y-m-d'));
                if (($users_startdate <= $current) && ($users_enddate >= $current)) {
                    $statusArr['EXPIRE'] = 'false';
                }
                $statusArr['BALANCE'] = $package_active_row->BoostKeywordsIndia_balance;
            } elseif ($event == 'BoostKeywordsInternational') {
                $statusArr['STATUS'] = $package_active_row->BoostKeywordsInternational_status;
                $users_startdate = strtotime($package_active_row->startdate);
                $users_enddate = strtotime($package_active_row->enddate);
                $current = strtotime(date('Y-m-d'));
                if (($users_startdate <= $current) && ($users_enddate >= $current)) {
                    $statusArr['EXPIRE'] = 'false';
                }
                $statusArr['BALANCE'] = $package_active_row->BoostKeywordsInternational_balance;
            } elseif ($event == 'BenchSalesIndia') {
                $statusArr['STATUS'] = $package_active_row->BenchSalesIndia_status;
                $users_startdate = strtotime($package_active_row->startdate);
                $users_enddate = strtotime($package_active_row->enddate);
                $current = strtotime(date('Y-m-d'));
                if (($users_startdate <= $current) && ($users_enddate >= $current)) {
                    $statusArr['EXPIRE'] = 'false';
                }
                $statusArr['BALANCE'] = $package_active_row->BenchSalesIndia_balance;
            } elseif ($event == 'BenchSalesInternational') {
                $statusArr['STATUS'] = $package_active_row->BenchSalesInternational_status;
                $users_startdate = strtotime($package_active_row->startdate);
                $users_enddate = strtotime($package_active_row->enddate);
                $current = strtotime(date('Y-m-d'));
                if (($users_startdate <= $current) && ($users_enddate >= $current)) {
                    $statusArr['EXPIRE'] = 'false';
                }
                $statusArr['BALANCE'] = $package_active_row->BenchSalesInternational_balance;
            } elseif ($event == 'internaldatabaseaccess') {
                $statusArr['STATUS'] = $package_active_row->internaldatabaseaccess_status;
                $users_startdate = strtotime($package_active_row->startdate);
                $users_enddate = strtotime($package_active_row->enddate);
                $current = strtotime(date('Y-m-d'));
                if (($users_startdate <= $current) && ($users_enddate >= $current)) {
                    $statusArr['EXPIRE'] = 'false';
                }
                $statusArr['BALANCE'] = $package_active_row->internaldatabaseaccess_balance;
            } elseif ($event == 'BenchClientAcccessIndia') {
                $statusArr['STATUS'] = $package_active_row->BenchClientAcccessIndia_status;
                $users_startdate = strtotime($package_active_row->startdate);
                $users_enddate = strtotime($package_active_row->enddate);
                $current = strtotime(date('Y-m-d'));
                if (($users_startdate <= $current) && ($users_enddate >= $current)) {
                    $statusArr['EXPIRE'] = 'false';
                }
                $statusArr['BALANCE'] = $package_active_row->BenchClientAcccessIndia_balance;
            } elseif ($event == 'BenchClientAccessInternational') {
                $statusArr['STATUS'] = $package_active_row->BenchClientAccessInternational_status;
                $users_startdate = strtotime($package_active_row->startdate);
                $users_enddate = strtotime($package_active_row->enddate);
                $current = strtotime(date('Y-m-d'));
                if (($users_startdate <= $current) && ($users_enddate >= $current)) {
                    $statusArr['EXPIRE'] = 'false';
                }
                $statusArr['BALANCE'] = $package_active_row->BenchClientAccessInternational_balance;
            } elseif ($event == 'BenchClientAcccessIndia') {
                $statusArr['STATUS'] = $package_active_row->BenchClientAcccessIndia_status;
                $users_startdate = strtotime($package_active_row->startdate);
                $users_enddate = strtotime($package_active_row->enddate);
                $current = strtotime(date('Y-m-d'));
                if (($users_startdate <= $current) && ($users_enddate >= $current)) {
                    $statusArr['EXPIRE'] = 'false';
                }
                $statusArr['BALANCE'] = $package_active_row->BenchClientAcccessIndia_balance;
            } elseif ($event == 'emails') {
                $statusArr['STATUS'] = $package_active_row->emails_status;
                $users_startdate = strtotime($package_active_row->startdate);
                $users_enddate = strtotime($package_active_row->enddate);
                $current = strtotime(date('Y-m-d'));
                if (($users_startdate <= $current) && ($users_enddate >= $current)) {
                    $statusArr['EXPIRE'] = 'false';
                }
                $statusArr['BALANCE'] = $package_active_row->emails_balance;
            }

        }

        return $statusArr;

    }

    public static function validateClientPackageRet($event, $company_id)
    {

        // $company_id 			=	auth()->user()->company_id;

        $package_active = 0;
        $package_active_cnt = DB::table('package_active')->select('*')->where('company_id', $company_id)->count();

        $statusArr = [];
        $statusArr['EVENT'] = $event;
        $statusArr['STATUS'] = 0;
        $statusArr['EXPIRE'] = 'true';
        $statusArr['BALANCE'] = 0;

        $validatePackageRet = 0;

        if ($package_active_cnt > 0) {

            $package_active_row = '';
            $package_active_row = DB::table('package_active')->select('*')->where('company_id', $company_id)->first();

            // echo '<pre>'; print_r($package_active_row); exit;

            if ($event == 'USERS') {
                $statusArr['STATUS'] = $package_active_row->users_status;
                $users_startdate = strtotime($package_active_row->users_startdate);
                $users_enddate = strtotime($package_active_row->users_enddate);
                $current = strtotime(date('Y-m-d'));
                if (($users_startdate <= $current) && ($users_enddate >= $current)) {
                    $statusArr['EXPIRE'] = 'false';
                }
                $statusArr['BALANCE'] = $package_active_row->users_balance;

            } elseif ($event == 'CVACCESS') {

                $statusArr['STATUS'] = $package_active_row->cvaccess_status;
                $users_startdate = strtotime($package_active_row->cvaccess_startdate);
                $users_enddate = strtotime($package_active_row->cvaccess_enddate);
                $current = strtotime(date('Y-m-d'));
                if (($users_startdate <= $current) && ($users_enddate >= $current)) {
                    $statusArr['EXPIRE'] = 'false';
                }
                $statusArr['BALANCE'] = $package_active_row->cvaccess_balance;

                if ($statusArr['STATUS'] == 1) {
                    if ($statusArr['EXPIRE'] == 'false') {
                        if ($statusArr['BALANCE'] > 0) {
                            $validatePackageRet = 1;
                        } else {
                            $validatePackageRet = 0;
                        }
                    } else {
                        $validatePackageRet = 0;
                    }
                } else {
                    $validatePackageRet = 0;
                }

            }

        }

        return $validatePackageRet;

    }

    public static function clientPackageRedeem($event_name, $event_table_name, $event_table_id, $company_id)
    {

        // $company_id 			=	auth()->user()->company_id;
        $package_active = 0;
        $package_active_cnt = DB::table('package_active')->select('*')->where('company_id', $company_id)->count();

        if (($package_active_cnt > 0) && ($event_name != '')) {
            // dd($event_name);
            $f_id = DB::table('package_features')->select('id')->where('field_name', $event_name)->first('id');
            //  dd($f_id->id);
            $package_active_row = '';
            $package_active_row = DB::table('package_active')->select('*')->where('company_id', $company_id)->first();

            if ($event_name == 'CVACCESS') {

                $startdate = $enddate = $status = $quantity = $used = $balance = '';

                $startdate = @$package_active_row->cvaccess_startdate;
                $enddate = @$package_active_row->cvaccess_enddate;
                $status = @$package_active_row->cvaccess_status;
                $quantity = @$package_active_row->cvaccess_quantity;
                $used = @$package_active_row->cvaccess_used;
                $balance = @$package_active_row->cvaccess_balance;

                $values = [
                    'package_invoice_id' => @$package_active_row->package_invoice_id,
                    'company_id' => $company_id,
                    'package_master_item' => @$f_id->id,
                    'user_id' => auth()->user()->id,
                    'event_name' => $event_table_name,
                    'event_inserted_id' => @$event_table_id,
                ];
                DB::table('package_transactions')->insert($values);

                $used_cnt = @$package_active_row->cvaccess_used + 1;
                $balance_cnt = @$package_active_row->cvaccess_quantity - $used_cnt;
                DB::table('package_active')->where('company_id', @$company_id)->limit(1)->update(['cvaccess_used' => @$used_cnt,  'cvaccess_balance' => @$balance_cnt]);

            } elseif ($event_name == 'NEW_BENCH_CLIENT_REQUEST') {

                $startdate = $enddate = $status = $quantity = $used = $balance = '';

                $startdate = @$package_active_row->newbenchclientrequest_startdate;
                $enddate = @$package_active_row->newbenchclientrequest_enddate;
                $status = @$package_active_row->newbenchclientrequest_status;
                $quantity = @$package_active_row->newbenchclientrequest_quantity;
                $used = @$package_active_row->newbenchclientrequest_used;
                $balance = @$package_active_row->newbenchclientrequest_balance;

                $values = [
                    'package_invoice_id' => @$package_active_row->package_invoice_id,
                    'company_id' => $company_id,
                    'package_master_item' => @$f_id->id,
                    'user_id' => auth()->user()->id,
                    'event_name' => $event_table_name,
                    'event_inserted_id' => @$event_table_id,
                ];
                DB::table('package_transactions')->insert($values);

                $used_cnt = @$package_active_row->newbenchclientrequest_used + 1;
                $balance_cnt = @$package_active_row->newbenchclientrequest_quantity - $used_cnt;
                DB::table('package_active')->where('company_id', @$company_id)->limit(1)->update(['newbenchclientrequest_used' => @$used_cnt,  'newbenchclientrequest_balance' => @$balance_cnt]);

            } elseif ($event_name == 'BOOSTKEYWORDS') {

                $startdate = $enddate = $status = $quantity = $used = $balance = '';

                $startdate = @$package_active_row->boostkeywords_startdate;
                $enddate = @$package_active_row->boostkeywords_enddate;
                $status = @$package_active_row->boostkeywords_status;
                $quantity = @$package_active_row->boostkeywords_quantity;
                $used = @$package_active_row->boostkeywords_used;
                $balance = @$package_active_row->boostkeywords_balance;

                $values = [
                    'package_invoice_id' => @$package_active_row->package_invoice_id,
                    'company_id' => $company_id,
                    'package_master_item' => @$f_id->id,
                    'user_id' => auth()->user()->id,
                    'event_name' => $event_table_name,
                    'event_inserted_id' => @$event_table_id,
                ];
                DB::table('package_transactions')->insert($values);

                $used_cnt = @$package_active_row->boostkeywords_used + 1;
                $balance_cnt = @$package_active_row->boostkeywords_quantity - $used_cnt;
                DB::table('package_active')->where('company_id', @$company_id)->limit(1)->update(['boostkeywords_used' => @$used_cnt,  'boostkeywords_balance' => @$balance_cnt]);

            } elseif ($event_name == 'CONTRACT_RECRUITMENT_LEAD') {

                $startdate = $enddate = $status = $quantity = $used = $balance = '';

                $startdate = @$package_active_row->newclientrequest_startdate;
                $enddate = @$package_active_row->newclientrequest_enddate;
                $status = @$package_active_row->newclientrequest_status;
                $quantity = @$package_active_row->newclientrequest_quantity;
                $used = @$package_active_row->newclientrequest_used;
                $balance = @$package_active_row->newclientrequest_balance;

                $values = [
                    'package_invoice_id' => @$package_active_row->package_invoice_id,
                    'company_id' => $company_id,
                    'package_master_item' => @$f_id->id,
                    'user_id' => auth()->user()->id,
                    'event_name' => $event_table_name,
                    'event_inserted_id' => @$event_table_id,
                ];
                DB::table('package_transactions')->insert($values);

                $used_cnt = @$package_active_row->newclientrequest_used + 1;
                $balance_cnt = @$package_active_row->newclientrequest_quantity - $used_cnt;
                DB::table('package_active')->where('company_id', @$company_id)->limit(1)->update(['newclientrequest_used' => @$used_cnt,  'newclientrequest_balance' => @$balance_cnt]);

            } elseif ($event_name == 'FULLTIME_RECRUITMENT_LEAD') {

                $startdate = $enddate = $status = $quantity = $used = $balance = '';

                $startdate = @$package_active_row->newjobrequest_startdate;
                $enddate = @$package_active_row->newjobrequest_enddate;
                $status = @$package_active_row->newjobrequest_status;
                $quantity = @$package_active_row->newjobrequest_quantity;
                $used = @$package_active_row->newjobrequest_used;
                $balance = @$package_active_row->newjobrequest_balance;

                $values = [
                    'package_invoice_id' => @$package_active_row->package_invoice_id,
                    'company_id' => $company_id,
                    'package_master_item' => @$f_id->id,
                    'user_id' => auth()->user()->id,
                    'event_name' => $event_table_name,
                    'event_inserted_id' => @$event_table_id,
                ];
                DB::table('package_transactions')->insert($values);

                $used_cnt = @$package_active_row->newjobrequest_used + 1;
                $balance_cnt = @$package_active_row->newjobrequest_quantity - $used_cnt;
                DB::table('package_active')->where('company_id', @$company_id)->limit(1)->update(['newjobrequest_used' => @$used_cnt,  'newjobrequest_balance' => @$balance_cnt]);

            } elseif ($event_name == 'PermamentRecruitmentLeadIndia') {

                $startdate = $enddate = $status = $quantity = $used = $balance = '';

                $status = @$package_active_row->PermamentRecruitmentLeadIndia_status;
                $quantity = @$package_active_row->PermamentRecruitmentLeadIndia_quantity;
                $used = @$package_active_row->PermamentRecruitmentLeadIndia_used;
                $balance = @$package_active_row->PermamentRecruitmentLeadIndia_balance;
                // dd($f_id,@$package_active_row);

                $values = [
                    'package_invoice_id' => @$package_active_row->package_invoice_id,
                    'company_id' => $company_id,
                    'package_master_item' => @$f_id->id,
                    'user_id' => auth()->user()->id,
                    'event_name' => $event_table_name,
                    'event_inserted_id' => @$event_table_id,
                ];
                DB::table('package_transactions')->insert($values);

                $used_cnt = @$package_active_row->PermamentRecruitmentLeadIndia_used + 1;
                $balance_cnt = @$package_active_row->PermamentRecruitmentLeadIndia_quantity - $used_cnt;
                DB::table('package_active')->where('company_id', @$company_id)->limit(1)->update(['PermamentRecruitmentLeadIndia_used' => @$used_cnt,  'PermamentRecruitmentLeadIndia_balance' => @$balance_cnt]);

            } elseif ($event_name == 'PermamentRecruitmentLeadInternational') {

                $startdate = $enddate = $status = $quantity = $used = $balance = '';

                $status = @$package_active_row->PermamentRecruitmentLeadInternational_status;
                $quantity = @$package_active_row->PermamentRecruitmentLeadInternational_quantity;
                $used = @$package_active_row->PermamentRecruitmentLeadInternational_used;
                $balance = @$package_active_row->PermamentRecruitmentLeadInternational_balance;

                $values = [
                    'package_invoice_id' => @$package_active_row->package_invoice_id,
                    'company_id' => $company_id,
                    'package_master_item' => @$f_id->id,
                    'user_id' => auth()->user()->id,
                    'event_name' => $event_table_name,
                    'event_inserted_id' => @$event_table_id,
                ];
                DB::table('package_transactions')->insert($values);

                $used_cnt = @$package_active_row->PermamentRecruitmentLeadInternational_used + 1;
                $balance_cnt = @$package_active_row->PermamentRecruitmentLeadInternational_quantity - $used_cnt;
                DB::table('package_active')->where('company_id', @$company_id)->limit(1)->update(['PermamentRecruitmentLeadInternational_used' => @$used_cnt,  'PermamentRecruitmentLeadInternational_balance' => @$balance_cnt]);

            } elseif ($event_name == 'ContractRecruitmentLeadIndia') {

                $startdate = $enddate = $status = $quantity = $used = $balance = '';

                $status = @$package_active_row->ContractRecruitmentLeadIndia_status;
                $quantity = @$package_active_row->ContractRecruitmentLeadIndia_quantity;
                $used = @$package_active_row->ContractRecruitmentLeadIndia_used;
                $balance = @$package_active_row->ContractRecruitmentLeadIndia_balance;

                $values = [
                    'package_invoice_id' => @$package_active_row->package_invoice_id,
                    'company_id' => $company_id,
                    'package_master_item' => @$f_id->id,
                    'user_id' => auth()->user()->id,
                    'event_name' => $event_table_name,
                    'event_inserted_id' => @$event_table_id,
                ];
                DB::table('package_transactions')->insert($values);

                $used_cnt = @$package_active_row->ContractRecruitmentLeadIndia_used + 1;
                $balance_cnt = @$package_active_row->ContractRecruitmentLeadIndia_quantity - $used_cnt;
                DB::table('package_active')->where('company_id', @$company_id)->limit(1)->update(['ContractRecruitmentLeadIndia_used' => @$used_cnt,  'ContractRecruitmentLeadIndia_balance' => @$balance_cnt]);

            } elseif ($event_name == 'ContractRecruitmentLeadInternational') {

                $startdate = $enddate = $status = $quantity = $used = $balance = '';

                $status = @$package_active_row->ContractRecruitmentLeadInternational_status;
                $quantity = @$package_active_row->ContractRecruitmentLeadInternational_quantity;
                $used = @$package_active_row->ContractRecruitmentLeadInternational_used;
                $balance = @$package_active_row->ContractRecruitmentLeadInternational_balance;

                $values = [
                    'package_invoice_id' => @$package_active_row->package_invoice_id,
                    'company_id' => $company_id,
                    'package_master_item' => @$f_id->id,
                    'user_id' => auth()->user()->id,
                    'event_name' => $event_table_name,
                    'event_inserted_id' => @$event_table_id,
                ];
                DB::table('package_transactions')->insert($values);

                $used_cnt = @$package_active_row->ContractRecruitmentLeadInternational_used + 1;
                $balance_cnt = @$package_active_row->ContractRecruitmentLeadInternational_quantity - $used_cnt;
                DB::table('package_active')->where('company_id', @$company_id)->limit(1)->update(['ContractRecruitmentLeadInternational_used' => @$used_cnt,  'ContractRecruitmentLeadInternational_balance' => @$balance_cnt]);

            } elseif ($event_name == 'BoostKeywordsIndia') {

                $startdate = $enddate = $status = $quantity = $used = $balance = '';

                $status = @$package_active_row->BoostKeywordsIndia_status;
                $quantity = @$package_active_row->BoostKeywordsIndia_quantity;
                $used = @$package_active_row->BoostKeywordsIndia_used;
                $balance = @$package_active_row->BoostKeywordsIndia_balance;

                $values = [
                    'package_invoice_id' => @$package_active_row->package_invoice_id,
                    'company_id' => $company_id,
                    'package_master_item' => @$f_id->id,
                    'user_id' => auth()->user()->id,
                    'event_name' => $event_table_name,
                    'event_inserted_id' => @$event_table_id,
                ];
                DB::table('package_transactions')->insert($values);

                $used_cnt = @$package_active_row->BoostKeywordsIndia_used + 1;
                $balance_cnt = @$package_active_row->BoostKeywordsIndia_quantity - $used_cnt;
                DB::table('package_active')->where('company_id', @$company_id)->limit(1)->update(['BoostKeywordsIndia_used' => @$used_cnt,  'BoostKeywordsIndia_balance' => @$balance_cnt]);

            } elseif ($event_name == 'BoostKeywordsInternational') {

                $startdate = $enddate = $status = $quantity = $used = $balance = '';

                $status = @$package_active_row->BoostKeywordsInternational_status;
                $quantity = @$package_active_row->BoostKeywordsInternational_quantity;
                $used = @$package_active_row->BoostKeywordsInternational_used;
                $balance = @$package_active_row->BoostKeywordsInternational_balance;

                $values = [
                    'package_invoice_id' => @$package_active_row->package_invoice_id,
                    'company_id' => $company_id,
                    'package_master_item' => @$f_id->id,
                    'user_id' => auth()->user()->id,
                    'event_name' => $event_table_name,
                    'event_inserted_id' => @$event_table_id,
                ];
                DB::table('package_transactions')->insert($values);

                $used_cnt = @$package_active_row->BoostKeywordsInternational_used + 1;
                $balance_cnt = @$package_active_row->BoostKeywordsInternational_quantity - $used_cnt;
                DB::table('package_active')->where('company_id', @$company_id)->limit(1)->update(['BoostKeywordsInternational_used' => @$used_cnt,  'BoostKeywordsInternational_balance' => @$balance_cnt]);

            } elseif ($event_name == 'BenchSalesIndia') {

                $startdate = $enddate = $status = $quantity = $used = $balance = '';

                $status = @$package_active_row->BenchSalesIndia_status;
                $quantity = @$package_active_row->BenchSalesIndia_quantity;
                $used = @$package_active_row->BenchSalesIndia_used;
                $balance = @$package_active_row->BenchSalesIndia_balance;

                $values = [
                    'package_invoice_id' => @$package_active_row->package_invoice_id,
                    'company_id' => $company_id,
                    'package_master_item' => @$f_id->id,
                    'user_id' => auth()->user()->id,
                    'event_name' => $event_table_name,
                    'event_inserted_id' => @$event_table_id,
                ];
                DB::table('package_transactions')->insert($values);

                $used_cnt = @$package_active_row->BenchSalesIndia_used + 1;
                $balance_cnt = @$package_active_row->BenchSalesIndia_quantity - $used_cnt;
                DB::table('package_active')->where('company_id', @$company_id)->limit(1)->update(['BenchSalesIndia_used' => @$used_cnt,  'BenchSalesIndia_balance' => @$balance_cnt]);

                $package_active_check = DB::table('package_active')->select('*')->where('company_id', $company_id)->first();
                if ($package_active_check->BenchSalesIndia_balance == 0 && $package_active_check->BenchSalesInternational_balance == 0) {
                    DB::table('job_applications')->where('status', '=', 2)->where('company_id', @$company_id)->update(['status' => 4]);
                }

            } elseif ($event_name == 'BenchSalesInternational') {

                $startdate = $enddate = $status = $quantity = $used = $balance = '';

                $status = @$package_active_row->BenchSalesInternational_status;
                $quantity = @$package_active_row->BenchSalesInternational_quantity;
                $used = @$package_active_row->BenchSalesInternational_used;
                $balance = @$package_active_row->BenchSalesInternational_balance;

                $values = [
                    'package_invoice_id' => @$package_active_row->package_invoice_id,
                    'company_id' => $company_id,
                    'package_master_item' => @$f_id->id,
                    'user_id' => auth()->user()->id,
                    'event_name' => $event_table_name,
                    'event_inserted_id' => @$event_table_id,
                ];
                DB::table('package_transactions')->insert($values);

                $used_cnt = @$package_active_row->BenchSalesInternational_used + 1;
                $balance_cnt = @$package_active_row->BenchSalesInternational_quantity - $used_cnt;
                DB::table('package_active')->where('company_id', @$company_id)->limit(1)->update(['BenchSalesInternational_used' => @$used_cnt,  'BenchSalesInternational_balance' => @$balance_cnt]);

                $package_active_check = DB::table('package_active')->select('*')->where('company_id', $company_id)->first();
                if ($package_active_check->BenchSalesIndia_balance == 0 && $package_active_check->BenchSalesInternational_balance == 0) {
                    DB::table('job_applications')->where('status', '=', 2)->where('company_id', @$company_id)->update(['status' => 4]);
                }

            } elseif ($event_name == 'BenchClientAcccessIndia') {

                $startdate = $enddate = $status = $quantity = $used = $balance = '';

                $status = @$package_active_row->BenchClientAcccessIndia_status;
                $quantity = @$package_active_row->BenchClientAcccessIndia_quantity;
                $used = @$package_active_row->BenchClientAcccessIndia_used;
                $balance = @$package_active_row->BenchClientAcccessIndia_balance;

                $values = [
                    'package_invoice_id' => @$package_active_row->package_invoice_id,
                    'company_id' => $company_id,
                    'package_master_item' => @$f_id->id,
                    'user_id' => auth()->user()->id,
                    'event_name' => $event_table_name,
                    'event_inserted_id' => @$event_table_id,
                ];
                DB::table('package_transactions')->insert($values);

                $used_cnt = @$package_active_row->BenchClientAcccessIndia_used + 1;
                $balance_cnt = @$package_active_row->BenchClientAcccessIndia_quantity - $used_cnt;
                DB::table('package_active')->where('company_id', @$company_id)->limit(1)->update(['BenchClientAcccessIndia_used' => @$used_cnt,  'BenchClientAcccessIndia_balance' => @$balance_cnt]);

            } elseif ($event_name == 'BenchClientAccessInternational') {

                $startdate = $enddate = $status = $quantity = $used = $balance = '';

                $status = @$package_active_row->BenchClientAccessInternational_status;
                $quantity = @$package_active_row->BenchClientAccessInternational_quantity;
                $used = @$package_active_row->BenchClientAccessInternational_used;
                $balance = @$package_active_row->BenchClientAccessInternational_balance;

                $values = [
                    'package_invoice_id' => @$package_active_row->package_invoice_id,
                    'company_id' => $company_id,
                    'package_master_item' => @$f_id->id,
                    'user_id' => auth()->user()->id,
                    'event_name' => $event_table_name,
                    'event_inserted_id' => @$event_table_id,
                ];
                DB::table('package_transactions')->insert($values);

                $used_cnt = @$package_active_row->BenchClientAccessInternational_used + 1;
                $balance_cnt = @$package_active_row->BenchClientAccessInternational_quantity - $used_cnt;
                DB::table('package_active')->where('company_id', @$company_id)->limit(1)->update(['BenchClientAccessInternational_used' => @$used_cnt,  'BenchClientAccessInternational_balance' => @$balance_cnt]);

            } elseif ($event_name == 'emails') {
                $startdate = $enddate = $status = $quantity = $used = $balance = '';

                $startdate = @$package_active_row->startdate;
                $enddate = @$package_active_row->enddate;
                $status = @$package_active_row->emails_status;
                $quantity = @$package_active_row->emails_quantity;
                $used = @$package_active_row->emails_used;
                $balance = @$package_active_row->emails_balance;

                $values = [
                    'package_invoice_id' => @$package_active_row->package_invoice_id,
                    'company_id' => $company_id,
                    'package_master_item' => (int) $f_id->id,
                    'user_id' => auth()->user()->id,
                    'event_name' => $event_table_name,
                    'event_inserted_id' => @$event_table_id,
                ];
                // dd($values);
                DB::table('package_transactions')->insert($values);

                $used_cnt = @$package_active_row->emails_used + 1;
                $balance_cnt = @$package_active_row->emails_quantity - $used_cnt;
                // dd($balance_cnt,$company_id);
                DB::table('package_active')
                    ->where('company_id', @$company_id)
                    ->limit(1)
                    ->update(['emails_used' => @$used_cnt,  'emails_balance' => @$balance_cnt]);

            }

        }

    }

    // END FUNCTION
    public static function companyDetails($jobid)
    {
        try {

            $elasticService = app(\App\Services\ElasticService::class);
            $client = $elasticService->getClient();

            $company_id = auth()->user()->company_id;

            // ---------- JOB ----------
            $job = null;
            if ($client->exists(['index' => ELASTIC_JOBS, 'id' => $jobid])) {
                $job = $client->get(['index' => ELASTIC_JOBS, 'id' => $jobid]);
                $jobs_details = (object) $job['_source'];
            } else {
                return '';
            }

            // ---------- COMPANY ----------
            $company_details = null;
            if ($client->exists(['index' => ELASTIC_COMPANIES_INDEX, 'id' => $jobs_details->comId])) {
                $res = $client->get(['index' => ELASTIC_COMPANIES_INDEX, 'id' => $jobs_details->comId]);
                $company_details = (object) $res['_source'];
            }

            // ---------- CLIENT ----------
            $client_details = null;
            if ($client->exists(['index' => ELASTIC_CLIENT_LIST, 'id' => $jobs_details->client_id])) {
                $res = $client->get(['index' => ELASTIC_CLIENT_LIST, 'id' => $jobs_details->client_id]);
                $client_details = (object) $res['_source'];
            }

            // ---------- LOGIC ----------
            if ($jobs_details->comId == $company_id) {

                $logo_path = $client_details->logo_logical_path ?? '';

                if (($client_details->self_company ?? 0) == 1) {
                    $logo_path = $company_details->logo ?? $logo_path;
                }

            } else {

                $logo_path = $company_details->logo ?? '';
            }

            return $logo_path;

        } catch (\Throwable $e) {

            // NEVER crash blade
            return '';
        }
    }

    public static function openCompanyDetails($jobid, $company_id)
    {
        $logos = DB::table('companies')
            ->select('companies.*', 'clients.logo_logical_path', 'jobs.total_positions')
            ->join('jobs', 'companies.id', 'jobs.company_id')
            ->join('clients', 'clients.id', 'jobs.client_id')->where('jobs.id', $jobid)->first();

        $logos = DB::table('jobs')
            ->select(
                'jobs.id',
                'jobs.title',
                'jobs.work_type',
                'jobs.show_vendor',
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
                'jobs.min_salary_currency',
                'jobs.max_salary_currency',
                'clients.name as clients_name',
                'clients.logo_logical_path',
                'clients.self_company',
                'clients.company_id as client_company_id',
                'companies.company_name',
                'companies.logo',
                DB::raw('COUNT(job_applications.id) as allCandidate'),

                DB::raw('SUM(CASE WHEN job_applications.master_status_id = 1 THEN 1 ELSE 0 END) AS Sourcing'),
                DB::raw('SUM(CASE WHEN job_applications.master_status_id = 2 THEN 1 ELSE 0 END) AS Screening'),
                DB::raw('SUM(CASE WHEN job_applications.master_status_id = 3 THEN 1 ELSE 0 END) AS Interview'),       // MEANS COMPANY OWN UPLOAD APP
                DB::raw('SUM(CASE WHEN job_applications.master_status_id = 4 THEN 1 ELSE 0 END) AS Negotiation'),
                DB::raw('SUM(CASE WHEN job_applications.master_status_id = 5 THEN 1 ELSE 0 END) AS Offered'),
                DB::raw('SUM(CASE WHEN job_applications.master_status_id = 6 THEN 1 ELSE 0 END) AS Joined'),
                DB::raw('SUM(CASE WHEN job_applications.master_status_id = 7 THEN 1 ELSE 0 END) AS Rejected'),
                DB::raw('SUM(CASE WHEN job_applications.master_status_id = 8 THEN 1 ELSE 0 END) AS InReview'),

                DB::raw('SUM(CASE WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = '.$company_id.' THEN 1 ELSE 0 END) AS thirdparty_counter'),
                DB::raw('SUM(CASE WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = '.$company_id.' AND job_applications.master_status_id = 8 THEN 1 ELSE 0 END) AS thirdparty_InReview'),
                DB::raw('SUM(CASE WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = '.$company_id.' AND job_applications.master_status_id = 1 THEN 1 ELSE 0 END) AS thirdparty_Sourcing'),
                DB::raw('SUM(CASE WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = '.$company_id.' AND job_applications.master_status_id = 2 THEN 1 ELSE 0 END) AS thirdparty_Screening'),
                DB::raw('SUM(CASE WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = '.$company_id.' AND job_applications.master_status_id = 3 THEN 1 ELSE 0 END) AS thirdparty_Interview'),
                DB::raw('SUM(CASE WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = '.$company_id.' AND job_applications.master_status_id = 4 THEN 1 ELSE 0 END) AS thirdparty_Negotiation'),
                DB::raw('SUM(CASE WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = '.$company_id.' AND job_applications.master_status_id = 5 THEN 1 ELSE 0 END) AS thirdparty_Offered'),
                DB::raw('SUM(CASE WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = '.$company_id.' AND job_applications.master_status_id = 6 THEN 1 ELSE 0 END) AS thirdparty_Joined'),

                DB::raw('SUM(
				CASE 
						WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = '.$company_id.' AND job_applications.master_status_id = 7 THEN 1 
						WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = '.$company_id.' AND job_applications.master_status_id = 9 THEN 1
						ELSE 0 END
			 ) AS thirdparty_Rejected'
                ),
                // DB::raw("SUM(CASE WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = ".$company_id." AND job_applications.master_status_id = 9 THEN 1 ELSE 0 END) AS thirdparty_InReview_Rejected"),

                'recommendation_relation.id as recommendation_id',
                'recommendation_relation.from_company_id',
                'recommendation_relation.final_status',
                'recommendation_relation.to_company_id'
            )
            ->join('clients', 'clients.id', 'jobs.client_id')
            ->join('companies', 'companies.id', 'jobs.company_id')
            ->leftJoin('add_job_wise_condidates as job_applications', 'job_applications.job_id', '=', 'jobs.id')
            ->leftJoin('recommendation_relation', 'recommendation_relation.job_id', '=', 'jobs.id')
            ->where('jobs.id', '>', '0');
        $row = $logos->where('jobs.id', $jobid)->first();

        $recommendation_id = '';
        $recommendation_id = $row->recommendation_id;
        $recommendation_text = '&nbsp;<i class="bi bi-house" style="color:#00CC00"></i>';

        $logo_path = '';
        // $logo_path				=	url('img/client-default.png');
        if (! empty($row->logo_logical_path)) {
            $logo_path = @$row->logo_logical_path;
            if (@$row->to_company_id == $company_id) {
                $logo_path = @$row->logo;
            }

            if ($row->thirdparty_counter > 0 && $recommendation_id > 0 && @$row->to_company_id == $company_id) {
                $logo_path = @$row->logo;
            }
        }

        if ($recommendation_id > 0 && @$row->to_company_id == $company_id) {
            $recommendation_text = '&nbsp;<i style="color: #FF0000; font-weight:bold" class="fa fa-users" aria-hidden="true"></i>';
            $profile_detail_class = 'client-show-detail';
            $clients_name = ucwords(substr(@$row->company_name, 0, 15));

            if (! empty(@$row->logo) && strlen(@$row->logo) > 10) {
                $logo_path = @$row->logo;
            }
        }

        if (@$row->self_company == 1) {
            if (! empty(@$row->logo) && strlen(@$row->logo) > 10) {
                $logo_path = @$row->logo;
            }
        }

        return $logo_path;
    }

    public static function openDefaultTimeFormat($company_id)
    {

        $defaultTimeFormat = DB::table('companies')
            ->select('companies.time_format')
            ->where('companies.id', $company_id)
            ->first();

        // dd($defaultTimeFormat );

        $timeformat = 1;
        if ($defaultTimeFormat) {
            $timeformat = ($defaultTimeFormat->time_format);
        }

        return $timeformat;

    }

    public static function openCountryCodeFun($company_id)
    {
        $client = Client::select('clients.id', 'clients.tags')->where('clients.company_id', '=', $company_id)->where('clients.self_company', '=', 1)->first();
        $client_location = DB::table('client_location')
            ->join('countries', 'countries.id', '=', 'client_location.country_id')
            ->select('countries.country_code', 'countries.country_name')
            ->where('client_location.client_id', @$client->id)
            ->first();
        $country_code = 'in';
        if ($client_location) {
            $country_code = strtolower($client_location->country_code);
        }

        return $country_code;

    }

    public static function openDefaultCurrency($company_id)
    {

        $defaultCurrency = DB::table('companies')
            ->join('master_currency', 'master_currency.id', '=', 'companies.currency')
            ->select('master_currency.code')
            ->where('companies.id', $company_id)
            ->first();

        // dd($defaultCurrency );

        $currency_code = 'INR';
        if ($defaultCurrency) {
            $currency_code = ($defaultCurrency->code);
        }

        return $currency_code;

    }

    public static function oepnCompanyDetails($jobid, $company_id)
    {
        $logos = DB::table('companies')
            ->select('companies.*', 'clients.logo_logical_path', 'jobs.total_positions')
            ->join('jobs', 'companies.id', 'jobs.company_id')
            ->join('clients', 'clients.id', 'jobs.client_id')->where('jobs.id', $jobid)->first();

        $logos = DB::table('jobs')
            ->select(
                'jobs.id',
                'jobs.title',
                'jobs.work_type',
                'jobs.show_vendor',
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
                'jobs.min_salary_currency',
                'jobs.max_salary_currency',
                'clients.name as clients_name',
                'clients.logo_logical_path',
                'clients.self_company',
                'clients.company_id as client_company_id',
                'companies.company_name',
                'companies.logo',
                DB::raw('COUNT(job_applications.id) as allCandidate'),

                DB::raw('SUM(CASE WHEN job_applications.master_status_id = 1 THEN 1 ELSE 0 END) AS Sourcing'),
                DB::raw('SUM(CASE WHEN job_applications.master_status_id = 2 THEN 1 ELSE 0 END) AS Screening'),
                DB::raw('SUM(CASE WHEN job_applications.master_status_id = 3 THEN 1 ELSE 0 END) AS Interview'),       // MEANS COMPANY OWN UPLOAD APP
                DB::raw('SUM(CASE WHEN job_applications.master_status_id = 4 THEN 1 ELSE 0 END) AS Negotiation'),
                DB::raw('SUM(CASE WHEN job_applications.master_status_id = 5 THEN 1 ELSE 0 END) AS Offered'),
                DB::raw('SUM(CASE WHEN job_applications.master_status_id = 6 THEN 1 ELSE 0 END) AS Joined'),
                DB::raw('SUM(CASE WHEN job_applications.master_status_id = 7 THEN 1 ELSE 0 END) AS Rejected'),
                DB::raw('SUM(CASE WHEN job_applications.master_status_id = 8 THEN 1 ELSE 0 END) AS InReview'),

                DB::raw('SUM(CASE WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = '.$company_id.' THEN 1 ELSE 0 END) AS thirdparty_counter'),
                DB::raw('SUM(CASE WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = '.$company_id.' AND job_applications.master_status_id = 8 THEN 1 ELSE 0 END) AS thirdparty_InReview'),
                DB::raw('SUM(CASE WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = '.$company_id.' AND job_applications.master_status_id = 1 THEN 1 ELSE 0 END) AS thirdparty_Sourcing'),
                DB::raw('SUM(CASE WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = '.$company_id.' AND job_applications.master_status_id = 2 THEN 1 ELSE 0 END) AS thirdparty_Screening'),
                DB::raw('SUM(CASE WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = '.$company_id.' AND job_applications.master_status_id = 3 THEN 1 ELSE 0 END) AS thirdparty_Interview'),
                DB::raw('SUM(CASE WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = '.$company_id.' AND job_applications.master_status_id = 4 THEN 1 ELSE 0 END) AS thirdparty_Negotiation'),
                DB::raw('SUM(CASE WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = '.$company_id.' AND job_applications.master_status_id = 5 THEN 1 ELSE 0 END) AS thirdparty_Offered'),
                DB::raw('SUM(CASE WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = '.$company_id.' AND job_applications.master_status_id = 6 THEN 1 ELSE 0 END) AS thirdparty_Joined'),

                DB::raw('SUM(
				CASE 
						WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = '.$company_id.' AND job_applications.master_status_id = 7 THEN 1 
						WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = '.$company_id.' AND job_applications.master_status_id = 9 THEN 1
						ELSE 0 END
			 ) AS thirdparty_Rejected'
                ),
                // DB::raw("SUM(CASE WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = ".$company_id." AND job_applications.master_status_id = 9 THEN 1 ELSE 0 END) AS thirdparty_InReview_Rejected"),

                'recommendation_relation.id as recommendation_id',
                'recommendation_relation.from_company_id',
                'recommendation_relation.final_status',
                'recommendation_relation.to_company_id'
            )
            ->join('clients', 'clients.id', 'jobs.client_id')
            ->join('companies', 'companies.id', 'jobs.company_id')
            ->leftJoin('add_job_wise_condidates as job_applications', 'job_applications.job_id', '=', 'jobs.id')
            ->leftJoin('recommendation_relation', 'recommendation_relation.job_id', '=', 'jobs.id')
            ->where('jobs.id', '>', '0');
        $row = $logos->where('jobs.id', $jobid)->first();

        $recommendation_id = '';
        $recommendation_id = $row->recommendation_id;
        $recommendation_text = '&nbsp;<i class="bi bi-house" style="color:#00CC00"></i>';

        $logo_path = '';
        // $logo_path				=	url('img/client-default.png');
        if (! empty($row->logo_logical_path)) {
            $logo_path = @$row->logo_logical_path;
            if (@$row->to_company_id == $company_id) {
                $logo_path = @$row->logo;
            }

            if ($row->thirdparty_counter > 0 && $recommendation_id > 0 && @$row->to_company_id == $company_id) {
                $logo_path = @$row->logo;
            }
        }

        if ($recommendation_id > 0 && @$row->to_company_id == $company_id) {
            $recommendation_text = '&nbsp;<i style="color: #FF0000; font-weight:bold" class="fa fa-users" aria-hidden="true"></i>';
            $profile_detail_class = 'client-show-detail';
            $clients_name = ucwords(substr(@$row->company_name, 0, 15));

            if (! empty(@$row->logo) && strlen(@$row->logo) > 10) {
                $logo_path = @$row->logo;
            }
        }

        if (@$row->self_company == 1) {
            if (! empty(@$row->logo) && strlen(@$row->logo) > 10) {
                $logo_path = @$row->logo;
            }
        }

        return $logo_path;
    }

    public static function openCompanyDetailsByClient($jobid, $company_id)
    {
        $logos = DB::table('companies')
            ->select('companies.*', 'clients.logo_logical_path', 'jobs.total_positions')
            ->join('jobs', 'companies.id', 'jobs.company_id')
            ->join('clients', 'clients.id', 'jobs.client_id')->where('jobs.id', $jobid)->first();

        $logos = DB::table('jobs')
            ->select(
                'jobs.id',
                'jobs.title',
                'jobs.work_type',
                'jobs.show_vendor',
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
                'jobs.company_id',
                'jobs.min_salary_currency',
                'jobs.max_salary_currency',
                'clients.name as clients_name',
                'clients.logo_logical_path',
                'clients.self_company',
                'clients.company_id as client_company_id',
                'companies.company_name',
                'companies.logo',
                DB::raw('COUNT(job_applications.id) as allCandidate'),

                DB::raw('SUM(CASE WHEN job_applications.master_status_id = 1 THEN 1 ELSE 0 END) AS Sourcing'),
                DB::raw('SUM(CASE WHEN job_applications.master_status_id = 2 THEN 1 ELSE 0 END) AS Screening'),
                DB::raw('SUM(CASE WHEN job_applications.master_status_id = 3 THEN 1 ELSE 0 END) AS Interview'),       // MEANS COMPANY OWN UPLOAD APP
                DB::raw('SUM(CASE WHEN job_applications.master_status_id = 4 THEN 1 ELSE 0 END) AS Negotiation'),
                DB::raw('SUM(CASE WHEN job_applications.master_status_id = 5 THEN 1 ELSE 0 END) AS Offered'),
                DB::raw('SUM(CASE WHEN job_applications.master_status_id = 6 THEN 1 ELSE 0 END) AS Joined'),
                DB::raw('SUM(CASE WHEN job_applications.master_status_id = 7 THEN 1 ELSE 0 END) AS Rejected'),
                DB::raw('SUM(CASE WHEN job_applications.master_status_id = 8 THEN 1 ELSE 0 END) AS InReview'),

                DB::raw('SUM(CASE WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = '.$company_id.' THEN 1 ELSE 0 END) AS thirdparty_counter'),
                DB::raw('SUM(CASE WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = '.$company_id.' AND job_applications.master_status_id = 8 THEN 1 ELSE 0 END) AS thirdparty_InReview'),
                DB::raw('SUM(CASE WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = '.$company_id.' AND job_applications.master_status_id = 1 THEN 1 ELSE 0 END) AS thirdparty_Sourcing'),
                DB::raw('SUM(CASE WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = '.$company_id.' AND job_applications.master_status_id = 2 THEN 1 ELSE 0 END) AS thirdparty_Screening'),
                DB::raw('SUM(CASE WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = '.$company_id.' AND job_applications.master_status_id = 3 THEN 1 ELSE 0 END) AS thirdparty_Interview'),
                DB::raw('SUM(CASE WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = '.$company_id.' AND job_applications.master_status_id = 4 THEN 1 ELSE 0 END) AS thirdparty_Negotiation'),
                DB::raw('SUM(CASE WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = '.$company_id.' AND job_applications.master_status_id = 5 THEN 1 ELSE 0 END) AS thirdparty_Offered'),
                DB::raw('SUM(CASE WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = '.$company_id.' AND job_applications.master_status_id = 6 THEN 1 ELSE 0 END) AS thirdparty_Joined'),

                DB::raw('SUM(
				CASE 
						WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = '.$company_id.' AND job_applications.master_status_id = 7 THEN 1 
						WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = '.$company_id.' AND job_applications.master_status_id = 9 THEN 1
						ELSE 0 END
			 ) AS thirdparty_Rejected'
                ),
                // DB::raw("SUM(CASE WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = ".$company_id." AND job_applications.master_status_id = 9 THEN 1 ELSE 0 END) AS thirdparty_InReview_Rejected"),

                'recommendation_relation.id as recommendation_id',
                'recommendation_relation.from_company_id',
                'recommendation_relation.final_status',
                'recommendation_relation.to_company_id'
            )
            ->join('clients', 'clients.id', 'jobs.client_id')
            ->join('companies', 'companies.id', 'jobs.company_id')
            ->leftJoin('add_job_wise_condidates as job_applications', 'job_applications.job_id', '=', 'jobs.id')
            ->leftJoin('recommendation_relation', 'recommendation_relation.job_id', '=', 'jobs.id')
            ->where('jobs.id', '>', '0');
        $row = $logos->where('jobs.id', $jobid)->first();

        //  dd($row);
        $recommendation_id = '';
        $recommendation_id = $row->recommendation_id;
        $client_name = @$row->clients_name;
        if (@$row->company_id != $company_id) {
            $client_name = @$row->company_name;
            $client_name = ucwords(substr(@$client_name, 0, 30));
            if (@$row->company_id != $company_id) {
                $client_name = @$row->company_name;
                $client_name = ucwords(substr(@$client_name, 0, 30));
            }

        }

        // 			dd($client_name);

        return @$client_name;
    }

    public static function openJobcities($cityID, $company_id)
    {

        $citylistSelect = DB::table('client_city_jobs')
            ->join('city_list', 'city_list.city_id', '=', 'client_city_jobs.city_id')
            ->join('state_list', 'state_list.id', '=', 'city_list.state_id')
            ->join('countries', 'countries.id', '=', 'state_list.country_id')
            ->select('city_list.city as city_name', 'state_list.state as state_name', 'countries.country_name')
            ->where('city_list.city', '!=', '')
            ->where('client_city_jobs.job_id', $cityID)
            // ->where('client_city_jobs.company_id', $Company_id)
            ->get()->map(function ($city) {
                return $city->city_name;
            });
        // $jobCitySelectedTag  =
        $jobCitySelectedTag = $citylistSelect->toArray();

        return ($jobCitySelectedTag) ? implode(' , ', $jobCitySelectedTag) : '';

    }

    public static function openChecksubscription($company_id)
    {
        $package_active_row = DB::table('package_active')->select('*')->where('company_id', $company_id)->first();
        $end_date = $package_active_row->enddate;
        $current_date = date('Y-m-d');
        $end_date = Carbon::parse($end_date);
        $current_date = Carbon::parse($current_date);

        if ($end_date->gt($current_date) && $package_active_row->packages_status == 1) {
            // subscription end date is greater than
            return true;
        } elseif ($end_date->lt($current_date)) {
            // subscription end date is less than
            return true; // false;
        } else {
            // subscription end date and current date is equal
            if ($package_active_row->packages_status == 1) {
                return true;
            }

        }
    }

    public static function openFirstJobcities($id, $company_id)
    {

        $citylistSelect = DB::table('client_city_jobs')
            ->join('city_list', 'city_list.city_id', '=', 'client_city_jobs.city_id')
            ->join('state_list', 'state_list.id', '=', 'city_list.state_id')
            ->join('countries', 'countries.id', '=', 'state_list.country_id')
            ->select('city_list.city as city_name', 'state_list.state as state_name', 'countries.country_name')
            ->where('city_list.city', '!=', '')
            ->where('client_city_jobs.job_id', $id)
            // ->where('client_city_jobs.company_id', $Company_id)
            ->get();
        $total = $citylistSelect->count();
        if ($total == 0) {
            return [
                'display' => '',
                'tooltip' => '',
            ];
        }
        $first = $citylistSelect->first();
        $cityName = $first->city_name;
        $stateName = $first->state_name;

        $otherCities = $citylistSelect->slice(1)->pluck('city_name')->toArray();
        $tooltip = implode(', ', $otherCities);

        $display = $cityName;
        $countPlus = '';
        if ($total > 1) {
            $countPlus = ' +'.($total - 1).' Other City';
        }
        $display .= '`,`'.$stateName;

        return [
            'display' => $display,
            'tooltip' => $tooltip,
            'countPlus' => $countPlus,
        ];

    }

    public static function companyDetailsSupper($jobid)
    {
        $logos = DB::table('companies')
            ->select('companies.*', 'clients.logo_logical_path', 'jobs.total_positions')
            ->join('jobs', 'companies.id', 'jobs.company_id')
            ->join('clients', 'clients.id', 'jobs.client_id')->where('jobs.id', $jobid)->first();

        $company_id = $logos->id;

        $logos = DB::table('jobs')
            ->select(
                'jobs.id',
                'jobs.title',
                'jobs.work_type',
                'jobs.show_vendor',
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
                'jobs.min_salary_currency',
                'jobs.max_salary_currency',
                'clients.name as clients_name',
                'clients.logo_logical_path',
                'clients.self_company',
                'clients.company_id as client_company_id',
                'companies.company_name',
                'companies.logo',
                DB::raw('COUNT(job_applications.id) as allCandidate'),

                DB::raw('SUM(CASE WHEN job_applications.master_status_id = 1 THEN 1 ELSE 0 END) AS Sourcing'),
                DB::raw('SUM(CASE WHEN job_applications.master_status_id = 2 THEN 1 ELSE 0 END) AS Screening'),
                DB::raw('SUM(CASE WHEN job_applications.master_status_id = 3 THEN 1 ELSE 0 END) AS Interview'),       // MEANS COMPANY OWN UPLOAD APP
                DB::raw('SUM(CASE WHEN job_applications.master_status_id = 4 THEN 1 ELSE 0 END) AS Negotiation'),
                DB::raw('SUM(CASE WHEN job_applications.master_status_id = 5 THEN 1 ELSE 0 END) AS Offered'),
                DB::raw('SUM(CASE WHEN job_applications.master_status_id = 6 THEN 1 ELSE 0 END) AS Joined'),
                DB::raw('SUM(CASE WHEN job_applications.master_status_id = 7 THEN 1 ELSE 0 END) AS Rejected'),
                DB::raw('SUM(CASE WHEN job_applications.master_status_id = 8 THEN 1 ELSE 0 END) AS InReview'),

                DB::raw('SUM(CASE WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = '.$company_id.' THEN 1 ELSE 0 END) AS thirdparty_counter'),
                DB::raw('SUM(CASE WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = '.$company_id.' AND job_applications.master_status_id = 8 THEN 1 ELSE 0 END) AS thirdparty_InReview'),
                DB::raw('SUM(CASE WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = '.$company_id.' AND job_applications.master_status_id = 1 THEN 1 ELSE 0 END) AS thirdparty_Sourcing'),
                DB::raw('SUM(CASE WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = '.$company_id.' AND job_applications.master_status_id = 2 THEN 1 ELSE 0 END) AS thirdparty_Screening'),
                DB::raw('SUM(CASE WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = '.$company_id.' AND job_applications.master_status_id = 3 THEN 1 ELSE 0 END) AS thirdparty_Interview'),
                DB::raw('SUM(CASE WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = '.$company_id.' AND job_applications.master_status_id = 4 THEN 1 ELSE 0 END) AS thirdparty_Negotiation'),
                DB::raw('SUM(CASE WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = '.$company_id.' AND job_applications.master_status_id = 5 THEN 1 ELSE 0 END) AS thirdparty_Offered'),
                DB::raw('SUM(CASE WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = '.$company_id.' AND job_applications.master_status_id = 6 THEN 1 ELSE 0 END) AS thirdparty_Joined'),

                DB::raw('SUM(
				CASE 
						WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = '.$company_id.' AND job_applications.master_status_id = 7 THEN 1 
						WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = '.$company_id.' AND job_applications.master_status_id = 9 THEN 1
						ELSE 0 END
			 ) AS thirdparty_Rejected'
                ),
                // DB::raw("SUM(CASE WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = ".$company_id." AND job_applications.master_status_id = 9 THEN 1 ELSE 0 END) AS thirdparty_InReview_Rejected"),

                'recommendation_relation.id as recommendation_id',
                'recommendation_relation.from_company_id',
                'recommendation_relation.final_status',
                'recommendation_relation.to_company_id'
            )
            ->join('clients', 'clients.id', 'jobs.client_id')
            ->join('companies', 'companies.id', 'jobs.company_id')
            ->leftJoin('add_job_wise_condidates as job_applications', 'job_applications.job_id', '=', 'jobs.id')
            ->leftJoin('recommendation_relation', 'recommendation_relation.job_id', '=', 'jobs.id')
            ->where('jobs.id', '>', '0');
        $row = $logos->where('jobs.id', $jobid)->first();

        $recommendation_id = '';
        $recommendation_id = $row->recommendation_id;
        $recommendation_text = '&nbsp;<i class="bi bi-house" style="color:#00CC00"></i>';

        $logo_path = '';
        // $logo_path				=	url('img/client-default.png');

        if (@$row->self_company == 1) {
            if (! empty(@$row->logo) && strlen(@$row->logo) > 10) {
                $logo_path = @$row->logo;
            }
        }

        return $logo_path;
    }

    public static function companyDetailsByClient($jobid)
    {
        $logos = DB::table('companies')
            ->select('companies.*', 'clients.logo_logical_path', 'jobs.total_positions')
            ->join('jobs', 'companies.id', 'jobs.company_id')
            ->join('clients', 'clients.id', 'jobs.client_id')->where('jobs.id', $jobid)->first();

        $company_id = auth()->user()->company_id;

        $logos = DB::table('jobs')
            ->select(
                'jobs.id',
                'jobs.title',
                'jobs.work_type',
                'jobs.show_vendor',
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
                'jobs.company_id',
                'jobs.min_salary_currency',
                'jobs.max_salary_currency',
                'clients.name as clients_name',
                'clients.logo_logical_path',
                'clients.self_company',
                'clients.company_id as client_company_id',
                'companies.company_name',
                'companies.logo',
                DB::raw('COUNT(job_applications.id) as allCandidate'),

                DB::raw('SUM(CASE WHEN job_applications.master_status_id = 1 THEN 1 ELSE 0 END) AS Sourcing'),
                DB::raw('SUM(CASE WHEN job_applications.master_status_id = 2 THEN 1 ELSE 0 END) AS Screening'),
                DB::raw('SUM(CASE WHEN job_applications.master_status_id = 3 THEN 1 ELSE 0 END) AS Interview'),       // MEANS COMPANY OWN UPLOAD APP
                DB::raw('SUM(CASE WHEN job_applications.master_status_id = 4 THEN 1 ELSE 0 END) AS Negotiation'),
                DB::raw('SUM(CASE WHEN job_applications.master_status_id = 5 THEN 1 ELSE 0 END) AS Offered'),
                DB::raw('SUM(CASE WHEN job_applications.master_status_id = 6 THEN 1 ELSE 0 END) AS Joined'),
                DB::raw('SUM(CASE WHEN job_applications.master_status_id = 7 THEN 1 ELSE 0 END) AS Rejected'),
                DB::raw('SUM(CASE WHEN job_applications.master_status_id = 8 THEN 1 ELSE 0 END) AS InReview'),

                DB::raw('SUM(CASE WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = '.$company_id.' THEN 1 ELSE 0 END) AS thirdparty_counter'),
                DB::raw('SUM(CASE WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = '.$company_id.' AND job_applications.master_status_id = 8 THEN 1 ELSE 0 END) AS thirdparty_InReview'),
                DB::raw('SUM(CASE WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = '.$company_id.' AND job_applications.master_status_id = 1 THEN 1 ELSE 0 END) AS thirdparty_Sourcing'),
                DB::raw('SUM(CASE WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = '.$company_id.' AND job_applications.master_status_id = 2 THEN 1 ELSE 0 END) AS thirdparty_Screening'),
                DB::raw('SUM(CASE WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = '.$company_id.' AND job_applications.master_status_id = 3 THEN 1 ELSE 0 END) AS thirdparty_Interview'),
                DB::raw('SUM(CASE WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = '.$company_id.' AND job_applications.master_status_id = 4 THEN 1 ELSE 0 END) AS thirdparty_Negotiation'),
                DB::raw('SUM(CASE WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = '.$company_id.' AND job_applications.master_status_id = 5 THEN 1 ELSE 0 END) AS thirdparty_Offered'),
                DB::raw('SUM(CASE WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = '.$company_id.' AND job_applications.master_status_id = 6 THEN 1 ELSE 0 END) AS thirdparty_Joined'),

                DB::raw('SUM(
				CASE 
						WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = '.$company_id.' AND job_applications.master_status_id = 7 THEN 1 
						WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = '.$company_id.' AND job_applications.master_status_id = 9 THEN 1
						ELSE 0 END
			 ) AS thirdparty_Rejected'
                ),
                // DB::raw("SUM(CASE WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = ".$company_id." AND job_applications.master_status_id = 9 THEN 1 ELSE 0 END) AS thirdparty_InReview_Rejected"),

                'recommendation_relation.id as recommendation_id',
                'recommendation_relation.from_company_id',
                'recommendation_relation.final_status',
                'recommendation_relation.to_company_id'
            )
            ->join('clients', 'clients.id', 'jobs.client_id')
            ->join('companies', 'companies.id', 'jobs.company_id')
            ->leftJoin('add_job_wise_condidates as job_applications', 'job_applications.job_id', '=', 'jobs.id')
            ->leftJoin('recommendation_relation', 'recommendation_relation.job_id', '=', 'jobs.id')
            ->where('jobs.id', '>', '0');
        $row = $logos->where('jobs.id', $jobid)->first();

        //  dd($row);
        $recommendation_id = '';
        $recommendation_id = $row->recommendation_id;
        $client_name = @$row->clients_name;
        if (@$row->company_id != auth()->user()->company_id) {
            $client_name = @$row->company_name;
            $client_name = ucwords(substr(@$client_name, 0, 30));
            if (@$row->company_id != auth()->user()->company_id) {
                $client_name = @$row->company_name;
                $client_name = ucwords(substr(@$client_name, 0, 30));
            }

        }

        // 			dd($client_name);

        return @$client_name;
    }

    public static function companyOrClientDetails($jobid = '')
    {
        /**
         * Resilience & Tenancy Note: Protect against invalid or missing job IDs.
         * Returns an empty details object if the job record does not exist in DB.
         */
        $jobs = DB::table('jobs')->select('jobs.company_id', 'jobs.client_id')->where('jobs.id', $jobid)->first();

        if (! $jobs) {
            return (object) [
                'companyOrclientName' => '',
                'selfCompany' => '',
                'company' => '',
                'logo_logical_path' => '',
                'company_website' => '',
                'founded' => '',
                'email' => '',
                'acount_type' => '',
                'company_size' => '',
                'ind_name' => '',
            ];
        }

        $company_id = $jobs->company_id;

        $strAdded = DB::table('clients')
            ->select('clients.name as clients_name',
                'clients.logo_logical_path',
                'clients.self_company',
                'clients.website as client_website',
                'clients.founded as client_founded',
                'clients.email as client_email',
                'clients.company_type',
                'clients.industry',
                'clients.company_size_from as client_from_sizes',
                'clients.company_sizes as client_to_sizes',
                'clients.company_id as client_company_id',
                'companies.company_name',
                'companies.website',
                'companies.founded',
                'companies.company_size_from',
                'companies.company_size',
                'companies.company_email',
                'companies.acount_type',
                'companies.industries',
                'companies.logo', )
            ->join('companies', 'companies.id', '=', 'clients.company_id')
            ->where('clients.id', $jobs->client_id)
            ->first();

        $companyOrclientName = $company_website = $founded = $email = $acount_type = $company_size = $ind_name = '';
        $companyType = self::companyType();
        if ($strAdded) {

            if (@$jobs->company_id != auth()->user()->company_id) {
                $companyOrclientName = $strAdded->company_name;
                $company_website = $strAdded->website;
                $founded = $strAdded->founded;
                $email = $strAdded->company_email;
                $acount_type = $companyType[$strAdded->acount_type];
                $company_size = $strAdded->company_size_from.'-'.$strAdded->company_size;
                if ($strAdded->industries > 0) {
                    $td_industries = DB::table('td_industries')->select('ind_name')->where('ind_id', $strAdded->industries)->first();
                    $ind_name = @$td_industries->ind_name ?? '';
                }

            } else {
                if ($strAdded->self_company == 0) {
                    $companyOrclientName = $strAdded->clients_name;
                    $company_website = $strAdded->client_website;
                    $founded = $strAdded->client_founded;
                    $email = $strAdded->client_email;
                    $acount_type = $companyType[$strAdded->company_type];
                    $company_size = $strAdded->client_from_sizes.'-'.$strAdded->client_to_sizes;
                    if ($strAdded->industry > 0) {
                        $td_industries = DB::table('td_industries')->select('ind_name')->where('ind_id', $strAdded->industry)->first();
                        $ind_name = @$td_industries->ind_name ?? '';
                    }
                } else {
                    $companyOrclientName = $strAdded->company_name;
                    $company_website = $strAdded->website;
                    $founded = $strAdded->founded;
                    $email = $strAdded->company_email;
                    $acount_type = $companyType[$strAdded->acount_type];
                    $company_size = $strAdded->company_size_from.'-'.$strAdded->company_size;
                    if ($strAdded->industries > 0) {
                        $td_industries = DB::table('td_industries')->select('ind_name')->where('ind_id', $strAdded->industries)->first();
                        $ind_name = @$td_industries->ind_name ?? '';
                    }
                }

            }

            $data = [
                'companyOrclientName' => (@$companyOrclientName) ? $companyOrclientName : '',
                'selfCompany' => (@$strAdded->self_company) ? @$strAdded->self_company : '',
                'company' => ($strAdded->company_name) ? @$strAdded->company_name : '',
                'client' => (@$strAdded->clients_name) ? @$strAdded->clients_name : '',
                'company_website' => ($company_website) ? @$company_website : '',
                'founded' => ($founded) ? @$founded : '',
                'email' => ($email) ? @$email : '',
                'acount_type' => ($acount_type) ? @$acount_type : '',
                'company_size' => ($company_size) ? @$company_size : '',
                'ind_name' => ($ind_name) ? @$ind_name : '',
            ];

            return (object) $data;
        }

    }

    public static function Interview($typeSection, $company_id, $userId, $jobId, $conid = '')
    {
        // dd($typeSection,$company_id,$userId,$jobId,request()->segments(3));
        if ($typeSection == 'main') {
            // dd("ki");
            $interviewShedule = DB::table('interview_schedules')
                ->join('job_applications', 'job_applications.id', 'interview_schedules.job_application_id')
                ->join('add_job_wise_condidates', 'interview_schedules.interview_job_wise_con_id', 'add_job_wise_condidates.id')
               //  ->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id')
                ->join('jobs', function ($join) {
                    $join->on('jobs.id', 'add_job_wise_condidates.job_id');
                })
                ->leftjoin('users', 'users.id', 'job_applications.submitted_by')
                ->leftjoin('companies as uc', 'uc.id', '=', 'users.company_id')
                // ->leftjoin('jobs', 'jobs.id', 'job_applications.job_id')
                ->leftjoin('companies', 'companies.id', 'jobs.company_id')
                ->leftjoin('master_status', 'master_status.id', 'interview_schedules.stage')
                ->leftjoin('interview_mode', 'interview_mode.id', 'interview_schedules.interview_mode')
                ->leftjoin('interview_mode_lavel', 'interview_mode_lavel.id', 'interview_schedules.interview_mode_lavel')
                ->leftjoin('candidate_status', 'candidate_status.id', 'interview_schedules.substage')
                ->leftJoin('recommendation_relation', 'recommendation_relation.job_id', '=', 'jobs.id')                             // Append new code
                ->select(
                    'interview_schedules.id',
                    'job_applications.full_name',
                    'interview_schedules.status',
                    'interview_schedules.schedule_date',
                    'interview_schedules.available_status',
                    'interview_schedules.reschedule',
                    'interview_schedules.feedback',
                    'interview_schedules.interview_mode',
                    'interview_schedules.interviewers',
                    'interview_schedules.interview_type',
                    'interview_schedules.schedule_from_time',
                    'interview_schedules.company_id as real_company_id',
                    'users.name as source',
                    'users.name as posted_by',
                    'users.image as posted_by_image',

                    'interview_schedules.job_application_id',
                    'master_status.name as master_status_name',
                    'candidate_status.name as child_status_name',
                    'jobs.title as job_title',
                    'jobs.id as job_id',
                    'companies.company_name',
                    'job_applications.current_designation',
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
                    'uc.company_name as uc_company_name'
                );
            if (! empty($conid)) {
                $interviewShedule = $interviewShedule->where('jobs.id', '=', $jobId)->where('interview_schedules.job_application_id', '=', (int) $conid);

            } else {
                $interviewShedule = $interviewShedule->where('jobs.id', '=', $jobId);

                $interviewShedule = $interviewShedule->where('jobs.company_id', '=', $company_id);

                $interviewShedule = $interviewShedule->orWhere(function ($query) use ($company_id, $jobId) {
                    return
                        $query->where('recommendation_relation.to_company_id', '=', $company_id)
                            ->where('add_job_wise_condidates.thirdparty_company_id', '=', $company_id)
                            ->where('add_job_wise_condidates.job_id', '=', $jobId);

                });

                $interviewShedule = $interviewShedule->orWhere(function ($query) use ($company_id, $jobId) {
                    return
                        $query->where('recommendation_relation.to_company_id', '=', $company_id)
                            ->where('add_job_wise_condidates.thirdparty_company_id', '=', $company_id)
                            ->where('add_job_wise_condidates.job_id', '=', $jobId);

                });
            }

        } else {

            $interviewShedule = DB::table('interview_schedules')
                ->join('job_applications', 'job_applications.id', 'interview_schedules.job_application_id')
                ->join('add_job_wise_condidates', 'interview_schedules.interview_job_wise_con_id', 'add_job_wise_condidates.id')
               //  ->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id')
                ->join('jobs', function ($join) {
                    $join->on('jobs.id', 'add_job_wise_condidates.job_id');
                })
                ->join('users', 'users.id', 'job_applications.submitted_by')
                ->leftjoin('companies as uc', 'uc.id', '=', 'users.company_id')
                // ->join('jobs', 'jobs.id', 'job_applications.job_id')
                ->join('job_assign', 'job_assign.job_id', 'jobs.id')
                ->leftjoin('companies', 'companies.id', 'jobs.company_id')
                ->leftjoin('master_status', 'master_status.id', 'interview_schedules.stage')
                ->leftjoin('interview_mode', 'interview_mode.id', 'interview_schedules.interview_mode')
                ->leftjoin('interview_mode_lavel', 'interview_mode_lavel.id', 'interview_schedules.interview_mode_lavel')
                ->leftjoin('candidate_status', 'candidate_status.id', 'interview_schedules.substage')
                ->leftJoin('recommendation_relation', 'recommendation_relation.job_id', '=', 'jobs.id')                             // Append new code
                ->select(
                    'interview_schedules.id',
                    'job_applications.full_name',
                    'interview_schedules.status',
                    'interview_schedules.schedule_date',
                    'interview_schedules.available_status',
                    'interview_schedules.reschedule',
                    'interview_schedules.feedback',
                    'interview_schedules.interview_mode',
                    'interview_schedules.interviewers',
                    'interview_schedules.interview_type',
                    'interview_schedules.schedule_from_time',
                    'interview_schedules.company_id as real_company_id',
                    'users.name as source',
                    'users.name as posted_by',
                    'users.image as posted_by_image',
                    'interview_schedules.job_application_id',
                    'master_status.name as master_status_name',
                    'candidate_status.name as child_status_name',
                    'jobs.title as job_title',
                    'jobs.id as job_id',
                    'companies.company_name',
                    'job_applications.current_designation',
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
                    'uc.company_name as uc_company_name'
                );
            if (! empty($conid)) {
                $interviewShedule = $interviewShedule->where('jobs.id', '=', $jobId)
                    ->where('job_assign.user_id', $userId)
                    ->where('interview_schedules.job_application_id', '=', $conid);
            } else {

                $interviewShedule = $interviewShedule->where('jobs.id', '=', $jobId)->where('job_assign.user_id', $userId);

                $interviewShedule = $interviewShedule->orWhere(function ($query) use ($company_id, $userId, $jobId) {
                    return
                        $query->where('recommendation_relation.to_company_id', '=', $company_id)
                            ->where('job_assign.user_id', $userId)
                            ->where('add_job_wise_condidates.thirdparty_company_id', '=', $company_id)
                            ->where('add_job_wise_condidates.job_id', '=', $jobId);

                });

                $interviewShedule = $interviewShedule->orWhere(function ($query) use ($company_id, $userId, $jobId) {
                    return
                        $query->where('recommendation_relation.to_company_id', '=', $company_id)
                            ->where('job_assign.user_id', $userId)
                            ->where('add_job_wise_condidates.thirdparty_company_id', '=', $company_id)
                            ->where('add_job_wise_condidates.job_id', '=', $jobId);

                });
            }

        }

        return $interviewShedule->get();

    }

    public static function team_persion($jobId, $company_id)
    {
        return DB::table('users')
            ->join('job_assign', 'job_assign.user_id', 'users.id')
            ->leftJoin('users_profile', 'users_profile.user_id', '=', 'users.id')
            ->select('users.name', 'users.image', 'users.email', 'users.mobile', 'users_profile.designation')
            ->whereNotNull('job_assign.spok_user_id')
            ->where('job_assign.spok_user_id', '!=', '')
            ->where('job_assign.company_id', $company_id)
            ->where('job_assign.job_id', $jobId)
            ->take(1)
            ->get();

    }

    public static function condidate_timeline($jobId, $condId)
    {

        $updateTimeLine = DB::table('updatetimeline')->select(
            'updatetimeline.id',
            'updatetimeline.event_id',
            'updatetimeline.event_by_company_id',
            'updatetimeline.event_by_user_id',
            'updatetimeline.master_process_name',
            'updatetimeline.child_process_name',
            'updatetimeline.application_id',
            'updatetimeline.application_email',
            'updatetimeline.job_id',
            'updatetimeline.note',
            'updatetimeline.create_at',
            'updatetimeline.update_at',
            'updatetimeline.prev_process_name',
            'updatetimeline.note',
            'updatetimeline.prev_process_child_name',
            'updatetimeline.interview_schedule_date',
            'updatetimeline.interview_mode',
            'updatetimeline.interview_mention_link',
            'updatetimeline.interview_mode_child_level',
            'updatetimeline.interviewers',
            'updatetimeline.average_star_rating',
            'updatetimeline.interview_message',
            'updatetimeline.interview_schedules_id',
            'updatetimeline.offer_currency',
            'updatetimeline.offer_mode',
            'updatetimeline.offer_amount',
            'updatetimeline.joined_date',
            'application_event.event_name',
            'jobs.id as job_id',
            'jobs.title',
            'users.name',
            'users.image',
            'clients.name as client_name',
            'interview_schedules.available_status',
            'interview_schedules.reschedule',
            'interview_schedules.feedback',
            'interview_schedules.stage',
            'master_status.name as status_name',
            'master_status.css_style'
        )->join('application_event', 'application_event.id', '=', 'updatetimeline.event_id')
            ->join('job_applications', 'job_applications.id', '=', 'updatetimeline.application_id')
            ->leftjoin('interview_schedules', 'interview_schedules.id', 'updatetimeline.interview_schedules_id')
            ->leftjoin('master_status', 'master_status.id', 'interview_schedules.stage')
            ->join('users', 'users.id', '=', 'updatetimeline.event_by_user_id')
            ->join('jobs', 'jobs.id', '=', 'updatetimeline.job_id')
            ->join('clients', 'clients.id', '=', 'jobs.client_id')
            ->where('updatetimeline.application_id', '=', $condId)
            ->where('updatetimeline.job_id', '=', $jobId)
            ->orderBy('updatetimeline.id', 'DESC')
            ->get();

        return $updateTimeLine;

    }

    public static function mailheader()
    {
        return DB::table('global_settings')->select('global_settings.*')->first();
    }

    public static function CompantName($company_id = '')
    {
        // dd("dkd");
        if (empty($company_id)) {
            if (auth()->check()) {
                $company_id = auth()->user()->company_id;
            } else {
                $company_id = 1;
            }

        }

        return DB::table('companies')->select('companies.company_name')->where('id', '=', $company_id)->first();
    }

    public static function qualification($id = '')
    {

        return DB::table('qualification_master')->select('qualification_master.name')->where('id', '=', $id)->first();
    }

    public static function companiesType()
    {
        $company_id = auth()->user()->company_id;
        $allow = DB::table('companies')->select('companies.company_type')->where('id', $company_id)->first();

        if (@$allow->company_type == 3) {
            return false;
        } else {
            return true;
        }
    }

    public static function companyOrClientName($id = '')
    {
        $company_id = auth()->user()->company_id;

        $strAdded = DB::table('clients')
            ->select('companies.company_name', 'companies.company_size', 'clients.company_sizes', 'companies.description as company_description', 'clients.description as client_description', 'clients.name', 'clients.self_company', 'clients.website as client_website', 'companies.website as company_website', 'clients.company_id')
            ->join('companies', 'companies.id', '=', 'clients.company_id')
            ->where('clients.id', $id)
            ->first();
        $companyOrclientName = '';
        if ($strAdded) {

            if ($strAdded->company_id != $company_id) {
                $companyOrclientName = $strAdded->company_name;
            } else {
                if ($strAdded->self_company == 0) {
                    $companyOrclientName = $strAdded->name;
                } else {
                    $companyOrclientName = $strAdded->company_name;
                }

            }

            if ($strAdded->self_company == 1) {
                $companyOrclientName = $strAdded->company_name;
            } else {
                $companyOrclientName = $strAdded->name;
            }

            $data = [
                'companyOrclientName' => (@$companyOrclientName) ? $companyOrclientName : '',
                'selfCompany' => (@$strAdded->self_company) ? @$strAdded->self_company : '',
                'company' => ($strAdded->company_name) ? @$strAdded->company_name : '',
                'client' => ($strAdded->name) ? @$strAdded->name : '',
                'company_website' => ($strAdded->company_website) ? @$strAdded->company_website : '',
                'client_website' => ($strAdded->client_website) ? @$strAdded->client_website : '',
                'company_description' => ($strAdded->company_description) ? @$strAdded->company_description : '',
                'client_description' => ($strAdded->client_description) ? @$strAdded->client_description : '',
                'company_size' => ($strAdded->company_sizes) ? @$strAdded->company_sizes : $strAdded->company_size,
            ];

            return (object) $data;
        }

    }

    public static function baseCompany()
    {
        $smtp = EmailSetting::where('status', '=', 0)->where('id', 11)->orderByDesc('id')->first();
        $data = [
            'companyName' => $smtp->mail_from_name ?? 'JERO.WORKS',
            'companyEmail' => $smtp->email_id,
        ];

        return (object) $data;

    }

    public static function googleSheetId($companyId)
    {
        // $companyId = 2;//auth()->user()->company_id;
        $count = DB::table('google_sheets')->where('company_id', $companyId)->where('status', 1)->first();

        return ($count) ? $count : 0;

    }

    public static function mailPermission($id)
    {
        // $companyId = 2;//auth()->user()->company_id;
        // $count	=	DB::table('email_notifications')->where('id',$id)->where('status', 1)->count();
        $elasticService = app(ElasticService::class);
        $params = [
            'index' => ELASTIC_EMAIL_NOTIFICATIONS,
            'body' => [
                'size' => 10000,
                'query' => [
                    'bool' => [
                        'must' => [
                            ['term' => ['id' => (int) $id]],
                            ['term' => ['status' => 1]],
                        ],
                    ],
                ],
            ],
        ];

        $response = $elasticService->getClient()->search($params);
        $collection = collect($response['hits']['hits'] ?? [])
            ->map(fn ($hit) => (object) $hit['_source']);

        $count = $collection->count();

        return ($count) ? true : false;

    }

    public static function mailTypeSubject($id, $mailType = '')
    {
        // $companyId = 2;//auth()->user()->company_id;
        $elasticService = app(ElasticService::class);

        // $data	=	DB::table('(email_notifications)')->where('id',$id)->where('status', 1)->where('mailType', $mailType)->first();

        $params = [
            'index' => ELASTIC_EMAIL_NOTIFICATIONS,
            'body' => [
                'size' => 10000,
                'query' => [
                    'bool' => [
                        'must' => [
                            ['term' => ['id' => (int) $id]],
                            ['term' => ['mailType' => (int) $mailType]],
                        ],
                    ],
                ],
            ],
        ];

        $response = $elasticService->getClient()->search($params);
        $data = isset($response['hits']['hits'][0]) ? (object) $response['hits']['hits'][0]['_source'] : null;

        return $data;

    }

    public static function notification($company_id, $type)
    {

        // $notification = DB::table('master_notifications')->where('company_id',$company_id)->where('notifications_name',$type)->first();
        $elasticService = app(ElasticService::class);

        $params = [
            'index' => ELASTIC_MASTER_NOTIFICATIONS,
            'body' => [
                'size' => 10000,
                'query' => [
                    'bool' => [
                        'must' => [
                            ['term' => ['company_id' => (int) $company_id]], // OK
                            ['match' => ['notifications_name' => $type]],    // TEXT field
                        ],
                    ],
                ],
            ],
        ];

        $response = $elasticService->getClient()->search($params);
        $notification = isset($response['hits']['hits'][0]) ? (object) $response['hits']['hits'][0]['_source'] : null;

        return $notification;
    }

    // The single, shared source for a job's DEFAULT skills/location/
    // experience/salary — reads the Elasticsearch ELASTIC_JOBS document
    // only, deliberately never MySQL. Used by every AppTracker tab page
    // that pre-populates its filter drawer from the job's defaults
    // (similar_candidates(), internal_bench(), career_apply(),
    // public_bench()) AND by custome_count_all() (the header counter
    // shown on all six tabs). Before this, each of those read the job
    // separately — most via gdFilterData()/JobcitiesTags() (MySQL),
    // custome_count_all() via ES directly — so if a job's skills/
    // location/salary were edited and the ES reindex lagged even
    // briefly, different tabs would silently compute against different
    // versions of the same job and show disagreeing counts on a fresh
    // page load, with no filter having been touched. Every caller now
    // reading the identical ES document is what makes that permanent
    // rather than something that can quietly drift apart again the next
    // time one call site is edited and another isn't.
    //
    // city_list/state_list/countries lookups below stay MySQL — that's
    // static reference data (city names), not the job's own editable
    // state, so it carries none of the staleness risk this exists to
    // avoid.
    // $resolveLocationNames: skip when the caller only needs raw city_ids
    // (e.g. custome_count_all(), which builds an Elasticsearch terms filter
    // from them) — the city_list/state_list/countries join to produce
    // "City | State | Country" display strings is only useful to callers
    // populating a UI field (the filter drawer's default_loc). Avoids an
    // unnecessary MySQL join on every header-counter computation, which
    // runs on every AppTracker tab page load.
    public static function jobDefaultsFromEs($id, $resolveLocationNames = true)
    {
        $elasticService = app(ElasticService::class);
        $jobEsDoc = (object) $elasticService->getClient()->get([
            'index' => ELASTIC_JOBS,
            'id' => $id,
        ])['_source'];

        $skillArr = [];
        foreach ($jobEsDoc->job_metronics_skills ?? [] as $sval) {
            $sval = (object) $sval;
            $skillArr[] = $sval->skill_name;
        }

        $cityIds = [];
        foreach ($jobEsDoc->client_city_jobs ?? [] as $sval) {
            $sval = (object) $sval;
            $cityIds[] = $sval->city_id;
        }
        $locationTags = [];
        if ($resolveLocationNames && ! empty($cityIds)) {
            $locationTags = DB::table('client_city_jobs')
                ->join('city_list', 'city_list.city_id', '=', 'client_city_jobs.city_id')
                ->join('state_list', 'state_list.id', '=', 'city_list.state_id')
                ->join('countries', 'countries.id', '=', 'state_list.country_id')
                ->select('city_list.city as city_name', 'state_list.state as state_name', 'countries.country_name')
                ->where('city_list.city', '!=', '')
                ->whereIn('city_list.city_id', $cityIds)
                ->get()
                ->map(function ($city) {
                    return $city->city_name.' | '.$city->state_name.' | '.$city->country_name;
                })
                ->toArray();
        }

        return (object) [
            'jobEsDoc' => $jobEsDoc,
            'min_exp' => $jobEsDoc->min_exp,
            'max_exp' => $jobEsDoc->max_exp,
            'min_salary_currency' => $jobEsDoc->min_salary_currency,
            'min_salary' => $jobEsDoc->min_salary,
            'max_salary' => $jobEsDoc->max_salary,
            'min_sal_mode' => $jobEsDoc->min_sal_mode,
            'qualification' => $jobEsDoc->qualification ?? '',
            'default_skill' => implode(',', $skillArr),
            'default_loc' => $locationTags[0] ?? '',
            'default_locations' => $locationTags,
        ];
    }

    public static function gdFilterData($jobId)
    {
        $company_id = auth()->user()->company_id ?? 0;
        $job = Job::withoutGlobalScopes()->findOrFail($jobId);
        $jobid = $job->id;
        $title = $job->title;
        $min_exp = $job->min_exp;
        $max_exp = $job->max_exp;
        $min_salary = round($job->min_salary);
        $max_salary = round($job->max_salary);
        $min_salary_currency = $job->min_salary_currency;
        $max_salary_currency = $job->max_salary_currency;
        $min_sal_mode = $job->min_sal_mode;
        $qualification_master = DB::table('qualification_master')->select('name')->where('id', $job->qualification)->first();
        $qualification = ($qualification_master) ? $qualification_master->name : '';

        $loc_data = DB::table('client_location_jobs')
            ->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
            ->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')
            ->where('client_location_jobs.job_id', $jobid)
            ->pluck('city_list.city as city_name')
            ->toArray();

        $default_loc = implode(',', $loc_data);

        $skill_arr = [];
        $skill_data = DB::table('job_metronics_skills')->select('*')->where('job_id', $jobid)->where('company_id', '=', $job->company_id)->get()->toArray();
        foreach ($skill_data as $sval) {
            $skill_arr[] = $sval->skill_name;
        }
        $default_skill = implode(',', $skill_arr);

        $qualification_master = DB::table('qualification_master')->select('name')->where('id', $job->qualification)->first();
        // $this->skill_data = $skill_data;
        $js_array = 'whitelist: ["'.implode('", "', $skill_arr).'"]';

        $data = [
            'jobid' => $job->id,
            'title' => $job->title,
            'min_exp' => $job->min_exp,
            'max_exp' => $job->max_exp,
            'min_salary' => round($job->min_salary),
            'max_salary' => round($job->max_salary),
            'min_salary_currency' => $job->min_salary_currency,
            'max_salary_currency' => $job->max_salary_currency,
            'min_sal_mode' => $job->min_sal_mode,
            'qualification' => ($qualification_master) ? $qualification_master->name : '',
            'default_skill' => $default_skill,
            'default_loc' => $default_loc,
            'js_array' => '',
        ];

        return (object) $data;

    }

    public static function canGgdFilterData($CandId)
    {
        $company_id = auth()->user()->company_id;
        $candidateDetaiks = JobApplication::withoutGlobalScopes()->findOrFail($CandId);
        $qualification_master = DB::table('qualification_master')->select('name')->where('id', $candidateDetaiks->qualification)->first();

        $loc_data = DB::table('job_applications')
            ->leftJoin('city_list', 'city_list.city_id', '=', 'job_applications.city_id')
            ->where('job_applications.id', $CandId)
            ->pluck('city_list.city as city_name')
            ->toArray();

        $default_loc = implode(',', $loc_data);

        $default_skill = $candidateDetaiks->skills; // implode(",",$skill_arr);

        $default_skill = $default_skill.','.$candidateDetaiks->current_designation;

        $data = [
            'jobid' => $candidateDetaiks->id,
            'title' => $candidateDetaiks->current_designation,
            'min_exp' => 0,
            'max_exp' => $candidateDetaiks->total_exp,
            'min_salary' => round($candidateDetaiks->bill_rate_amount),
            'max_salary' => round($candidateDetaiks->bill_rate_amount),
            'min_salary_currency' => $candidateDetaiks->bill_rate_currency,
            'max_salary_currency' => $candidateDetaiks->bill_rate_currency,
            'min_sal_mode' => $candidateDetaiks->bill_rate_mode,
            'city_id' => $candidateDetaiks->city_id,
            'qualification' => $qualification_master->name,
            'default_skill' => $default_skill,
            'default_loc' => $default_loc,
            'js_array' => '',
        ];

        return (object) $data;

    }

    public static function currency()
    {

        $currencies = Cache::remember('currency_codesww', 86400, function () {
            return DB::table('currencies')
                ->orderBy('getKey', 'ASC')
                ->pluck('currency_code')
                ->toArray();
        });
        if ($currencies) {
            return $currencies;
        }
        $array = [
            0 => 'INR',
            1 => 'EUR',
            2 => 'JPY',
            3 => 'GBP',
            4 => 'USD',
            5 => 'AED',
        ];

        return $array;
    }

    public static function currencySymbole()
    {
        $array = [
            0 => '<i class="fa fa-inr" aria-hidden="true"></i>',
            1 => '<i class="fa fa-eur" aria-hidden="true"></i>',
            2 => '<i class="fa fa-jpy" aria-hidden="true"></i>',
            3 => '<i class="fa fa-gbp" aria-hidden="true"></i>',
            4 => '<i class="fa fa-usd" aria-hidden="true"></i>',
            5 => '<i class="fa fa-usd" aria-hidden="true"></i>',
        ];

        return $array;
    }

    public static function currencySymboleforexcell()
    {

        $currencies = Cache::remember('currency_codesww', 86400, function () {
            return DB::table('currencies')
                ->orderBy('getKey', 'ASC')
                ->pluck('currency_code')
                ->toArray();
        });
        if ($currencies) {
            return $currencies;
        }

        $array = [
            0 => 'INR',
            1 => 'EUR',
            2 => 'JPY',
            3 => 'GBP',
            4 => 'USD',
            5 => 'AED',
        ];

        return $array;
    }

    public static function thousandsCurrencyFormat_old($num)
    {

        if ($num > 1000) {

            $x = round($num);
            $x_number_format = number_format($x);
            $x_array = explode(',', $x_number_format);
            // dd($x_array,strlen($x_array[0]));
            if (count($x_array) == 3) {

                $x_parts = ['K', 'L', 'Cr', 'T'];
                $x_count_parts = count($x_array) - 1;
                $x_display = $x;
                $x_display = $x_array[0].((int) $x_array[1][0] !== 0 ? '.'.$x_array[1][0] : '');
                $x_display .= $x_parts[$x_count_parts - 1];

                $x_parts = ['K', 'm', 'b', 't'];
                $x_count_parts = count($x_array) - 1;
                $x_display = $x;
                $x_display = $x_array[0].((int) $x_array[1][0] !== 0 ? '.'.$x_array[1][0] : '');
                $x_display .= $x_parts[$x_count_parts - 1];
            }

            return $x_display;
        }

        return $num;
    }

    public static function convertToINR($amount, $currency)
    {

        $exchangeRates = self::currencyConverter()['inr_based_rates'] ?? [];

        $exchangeRates = ! empty($exchangeRates) ? $exchangeRates : [
            'INR' => 1,
            'EUR' => 88.50,
            'JPY' => 0.68,
            'GBP' => 103.70,
            'USD' => 83,
            'AED' => 20.38,
        ];

        if (array_key_exists($currency, $exchangeRates)) {
            return $amount * $exchangeRates[$currency];
        } else {
            return 0;
        }
    }

    public static function salaryDividedByMode($mode, $amount)
    {
        $HOURS_PER_MONTH = 160;
        $MONTHS_PER_YEAR = 12;

        $hourly = 0;
        $monthly = 0;
        $yearly = 0;

        if ($mode == 0) {
            // yearly input
            $yearly = $amount;
            $monthly = $yearly / $MONTHS_PER_YEAR;
            $hourly = $monthly / $HOURS_PER_MONTH;

        } elseif ($mode == 1) {
            // monthly input
            $monthly = $amount;
            $yearly = $monthly * $MONTHS_PER_YEAR;
            $hourly = $monthly / $HOURS_PER_MONTH;

        } elseif ($mode == 2) {
            // hourly input
            $hourly = $amount;
            $monthly = $hourly * $HOURS_PER_MONTH;
            $yearly = $monthly * $MONTHS_PER_YEAR;
        }

        $data = [
            'yearly' => number_format($yearly, 2, '.', ''),
            'monthly' => number_format($monthly, 2, '.', ''),
            'hourly' => number_format($hourly, 2, '.', ''),
        ];
        // dd($data);

        return (object) $data;
    }

    public static function conversionRates($currency, $amount, $mode)
    {

        // Define custom conversion rates from INR to other currencies
        $conversionRates = [
            'INR' => 1,        // Base currency
            'EUR' => 0.011,    // Example rate: 1 INR = 0.011 EUR
            'JPY' => 1.49,     // Example rate: 1 INR = 1.49 JPY
            'GBP' => 0.0095,   // Example rate: 1 INR = 0.0095 GBP
            'USD' => 0.012,    // Example rate: 1 INR = 0.012 USD
            'AED' => 0.044,     // Example rate: 1 INR = 0.044 AED
        ];

        $exchangeRates = self::currencyConverter()['inr_based_rates'] ?? [];

        $exchangeRates = ! empty($exchangeRates) ? $exchangeRates : [
            'INR' => 1,
            'EUR' => 88.50,
            'JPY' => 0.68,
            'GBP' => 103.70,
            'USD' => 83,
            'AED' => 20.38,
        ];
        // dd($conversionRates['INR']);
        //  $convertedAmount = $amount * $conversionRates[$currency];
        $convertedAmount = $amount / $exchangeRates[$currency];

        $HOURS_PER_MONTH = 160;
        $MONTHS_PER_YEAR = 12;

        $yearly = $convertedAmount;

        $monthly = $yearly / $MONTHS_PER_YEAR;

        $hourly = $monthly / $HOURS_PER_MONTH;

        $yearly = round($yearly, 2);
        $monthly = round($monthly, 2);
        $hourly = round($hourly, 2);

        $data = [
            '0' => $yearly,
            '1' => $monthly,
            '2' => $hourly,
        ];
        // dd($convertedAmount,$currency,$amount,$mode,$data);

        return $data;

        //  dd($amount ,$convertedAmount, $exchangeRates[$currency]);

        //  dd( $convertedAmount);

        $yearly = $monthly = $hourly = 0;
        // dd($mode,$amount);
        if (@$mode == 0) {
            $yearly = $convertedAmount;
            $monthly = $convertedAmount / 12;
            $hourly = $monthly / 30 / 8;

        } elseif (@$mode == 1) {
            $monthly = $convertedAmount;
            $yearly = $convertedAmount * 12;
            $hourly = $monthly / 30 / 8;
            // dd();

        } elseif (@$mode == 2) {
            $hourly = $convertedAmount;
            $monthly = $convertedAmount * 8 * 30;
            $yearly = $convertedAmount * 8 * 30 * 12;

        }

        $data = [
            '0' => $yearly,
            '1' => $monthly,
            '2' => $hourly,
        ];
        // dd($convertedAmount,$currency,$amount,$mode,$data);

        return $data;

        // dd($convertedAmount);
        // return $convertedAmount;

    }

    public static function formatViewSalary($amountINR, $currency, $mode)
    {
        // constants
        $HOURS_PER_MONTH = 160;
        $MONTHS_PER_YEAR = 12;

        // currency rates (INR base)
        $rates = self::currencyConverter()['inr_based_rates'] ?? [];
        $rates = ! empty($rates) ? $rates : [
            'INR' => 1,
            'EUR' => 88.50,
            'JPY' => 0.68,
            'GBP' => 103.70,
            'USD' => 83,
            'AED' => 20.38,
        ];

        // currency symbols
        $symbols = self::currencyConverter()['symbols'] ?? [];
        $symbols = ! empty($symbols) ? $symbols : [
            'INR' => '₹',
            'USD' => '$',
            'EUR' => '€',
            'JPY' => '¥',
            'GBP' => '£',
            'AED' => 'د.إ',
        ];

        $symbolsValue = self::currencyConverter()['symbolsValue'] ?? [];
        $symbolsValue = ! empty($symbolsValue) ? $symbolsValue : [
            'INR' => 0,
            'EUR' => 1,
            'JPY' => 2,
            'GBP' => 3,
            'USD' => 4,
            'AED' => 5,
        ];

        // ================= STEP 1: INR → SELECTED CURRENCY =================
        $rate = $rates[$currency] ?? 1;

        $amount = $amountINR / $rate;

        $yearly = $amount;
        $monthly = $yearly / $MONTHS_PER_YEAR;
        $hourly = $monthly / $HOURS_PER_MONTH;
        if ($mode == 0) {
            // yearly
            $value = $yearly;
        } elseif ($mode == 1) {
            // monthly
            $value = $monthly;
        } else {
            // hourly
            $value = $hourly;
        }

        return (object) [
            'amount' => number_format($value, 2, '.', ''),
            'symbol' => $symbols[$currency] ?? '₹',
            'currency' => $symbolsValue[$currency] ?? 0,
            'mode' => $mode,
        ];

        // ///

        // ================= STEP 2: MODE =================
        if ($mode == 0) {
            // yearly
            $value = $hourly * $HOURS_PER_MONTH * $MONTHS_PER_YEAR;
        } elseif ($mode == 1) {
            // monthly
            $value = $hourly * $HOURS_PER_MONTH;
        } else {
            // hourly
            $value = $hourly;
        }

        // ================= STEP 3: FORMAT =================
        return (object) [
            'amount' => number_format($value, 2, '.', ''),
            'symbol' => $symbols[$currency] ?? '₹',
            'currency' => $symbolsValue[$currency] ?? 0,
            'mode' => $mode,
        ];
    }

    public static function currencyConverter()
    {
        return cache()->remember('ref:currencies:all', 86400, function () {
            $currencies = DB::table('currencies')->get();

            $exchangeRates = [];
            $rates = [];
            $symbolsValue = [];
            $symbols = [];
            $getKey = [];

            foreach ($currencies as $row) {
                // INR based (your first array)
                $exchangeRates[$row->currency_code] = (float) $row->inr_based_rates;

                // Second rates (example: you can use another column or same)
                $rates[$row->currency_code] = (float) $row->usd_based_rates;
                $symbolsValue[$row->currency_code] = $row->getKey;
                $symbols[$row->currency_code] = $row->currency_symbol;
                $getKey[$row->getKey] = $row->currency_code;
            }

            return [
                'inr_based_rates' => $exchangeRates,
                'usd_based_rates' => $rates,
                'symbolsValue' => $symbolsValue,
                'symbols' => $symbols,
                'getKey' => $getKey,
            ];
        });

    }

    public static function candidateValidation($id)
    {
        $jobApplication = JobApplication::withoutGlobalScopes()->find($id);

        //   if($jobApplication->condidate_type=='bench_condidate'){
        // 	dd("bench candidate");
        //   }else{
        // 	dd("other candidate");
        //   }
        $elasticService = app(ElasticService::class);
        $exists = $elasticService->getClient()->exists([
            'index' => ELASTIC_CANDIDATES,
            'id' => $id,
        ]);
        $params = [
            'index' => ELASTIC_CANDIDATES,
            'id' => $id,
        ];

        $response = $elasticService->getClient()->get($params);
        $data_candidate = (object) $response['_source'];

        // dd($data_candidate );
        // $jobApplication = JobApplication::find($data_candidate->candidate_id);

        // dd($jobApplication);

        $data = [
            'status' => false,
            'message' => 'All field valid',
        ];

        if (! $exists) {
            $data = [
                'status' => true,
                'message' => 'Job Application not found',
            ];
        }
        if (empty($data_candidate->np_id) || is_null($data_candidate->np_id)) {
            $data = [
                'status' => true,
                'message' => 'Noitce period is required please recheck candidate form',
            ];
        }

        if (empty($data_candidate->city_id) || is_null($data_candidate->city_id) || $data_candidate->city_id == 0) {
            $data = [
                'status' => true,
                'message' => 'City is required please recheck candidate form',
            ];
        }

        if (empty($data_candidate->skills) || is_null($data_candidate->skills)) {
            $data = [
                'status' => true,
                'message' => 'Skill is required please recheck candidate form',
            ];
        }
        if ($data_candidate->pincode == 0) {

            if (empty($data_candidate->total_exp) || is_null($data_candidate->total_exp)) {
                $data = [
                    'status' => true,
                    'message' => 'Total experience is required please recheck candidate form',
                ];
            }

            if (empty($data_candidate->rel_exp) || is_null($data_candidate->rel_exp)) {
                $data = [
                    'status' => true,
                    'message' => 'Relevant experience is required please recheck candidate form',
                ];
            }
            if ($data_candidate->condidate_type == 'other') {
                if (empty($data_candidate->ectc) || is_null($data_candidate->ectc)) {
                    $data = [
                        'status' => true,
                        'message' => 'Current CTC is required please recheck candidate form',
                    ];
                }
            } else {
                if (empty($data_candidate->bill_rate_amount) || is_null($data_candidate->bill_rate_amount)) {
                    $data = [
                        'status' => true,
                        'message' => 'Current Bill rate is required please recheck candidate form',
                    ];
                }
            }

            if ($data_candidate->condidate_type == 'other') {
                if (empty($data_candidate->ectc) || is_null($data_candidate->ectc)) {
                    $data = [
                        'status' => true,
                        'message' => 'Expected CTC is required please recheck candidate form',
                    ];
                }
            } else {
                if (empty($data_candidate->pay_rate_amount) || is_null($data_candidate->pay_rate_amount)) {
                    $data = [
                        'status' => true,
                        'message' => 'Pay rate amount is required please recheck candidate form',
                    ];
                }
            }
        }

        if (empty($data_candidate->qualification) || is_null($data_candidate->qualification)) {
            $data = [
                'status' => true,
                'message' => 'Qualification is required please recheck candidate form',
            ];
        }

        // if (empty($jobApplication->current_company) || is_null($jobApplication->current_company)) {
        // 	$data = [
        // 		'status'=>true,
        // 		'message'=>'Current_company is required'
        // 	];
        // }
        if (empty($data_candidate->country_name) || is_null($data_candidate->country_name)) {
            $data = [
                'status' => true,
                'message' => 'Country is required please recheck candidate form',
            ];
        }

        // if (empty($jobApplication->state) || is_null($jobApplication->state)) {
        // 	$data = [
        // 		'status'=>true,
        // 		'message'=>'State is required please recheck candidate form'
        // 	];
        // }

        return (object) $data;
    }

    public static function BenchcandidateValidation($id)
    {
        $jobApplication = JobApplication::withoutGlobalScopes()->find($id);

        //   if($jobApplication->condidate_type=='bench_condidate'){
        // 	dd("bench candidate");
        //   }else{
        // 	dd("other candidate");
        //   }
        $elasticService = app(ElasticService::class);
        $exists = $elasticService->getClient()->exists([
            'index' => ELASTIC_CANDIDATES_BENCH,
            'id' => $id,
        ]);
        $params = [
            'index' => ELASTIC_CANDIDATES_BENCH,
            'id' => $id,
        ];

        $response = $elasticService->getClient()->get($params);
        $data_candidate = (object) $response['_source'];

        // dd($data_candidate);

        $data = [
            'status' => false,
            'message' => 'All field valid',
        ];

        if (! $exists) {
            $data = [
                'status' => true,
                'message' => 'Job Application not found',
            ];
        }
        if (empty($data_candidate->np_id) || is_null($data_candidate->np_id)) {
            $data = [
                'status' => true,
                'message' => 'Noitce period is required please recheck candidate form',
            ];
        }

        if (empty($data_candidate->city_id) || is_null($data_candidate->city_id) || $data_candidate->city_id == 0) {
            $data = [
                'status' => true,
                'message' => 'City is required please recheck candidate form',
            ];
        }

        if (empty($data_candidate->skills) || is_null($data_candidate->skills)) {
            $data = [
                'status' => true,
                'message' => 'Skill is required please recheck candidate form',
            ];
        }
        if (empty($data_candidate->resume) || is_null($data_candidate->resume)) {
            $data = [
                'status' => true,
                'message' => 'Please attach resume to move to BENCH!',
            ];
        }
        if ($jobApplication->pincode == 0) {

            if (empty($data_candidate->total_exp) || is_null($data_candidate->total_exp)) {
                $data = [
                    'status' => true,
                    'message' => 'Total experience is required please recheck candidate form',
                ];
            }

            if (empty($data_candidate->rel_exp) || is_null($data_candidate->rel_exp)) {
                $data = [
                    'status' => true,
                    'message' => 'Relevant experience is required please recheck candidate form',
                ];
            }
            if ($data_candidate->condidate_type == 'other') {
                if (empty($data_candidate->ctc_yearly) || is_null($data_candidate->ctc_yearly)) {
                    $data = [
                        'status' => true,
                        'message' => 'Current CTC is required please recheck candidate form',
                    ];
                }
            } else {
                if (empty($data_candidate->ectc_yearly) || is_null($data_candidate->ectc_yearly)) {
                    $data = [
                        'status' => true,
                        'message' => 'Current Bill rate is required please recheck candidate form',
                    ];
                }
            }

            if ($data_candidate->condidate_type == 'other') {
                if (empty($jobApplication->ectc_yearly) || is_null($jobApplication->ectc_yearly)) {
                    $data = [
                        'status' => true,
                        'message' => 'Expected CTC is required please recheck candidate form',
                    ];
                }
            } else {
                if (empty($data_candidate->ctc_yearly) || is_null($data_candidate->ctc_yearly)) {
                    $data = [
                        'status' => true,
                        'message' => 'Pay rate amount is required please recheck candidate form',
                    ];
                }
            }
        }

        if (empty($data_candidate->qualification) || is_null($data_candidate->qualification)) {
            $data = [
                'status' => true,
                'message' => 'Qualification is required please recheck candidate form',
            ];
        }

        // if (empty($jobApplication->current_company) || is_null($jobApplication->current_company)) {
        // 	$data = [
        // 		'status'=>true,
        // 		'message'=>'Current_company is required'
        // 	];
        // }
        if (empty($data_candidate->country_name) || is_null($data_candidate->country_name)) {
            $data = [
                'status' => true,
                'message' => 'Country is required please recheck candidate form',
            ];
        }

        // if (empty($jobApplication->state) || is_null($jobApplication->state)) {
        // 	$data = [
        // 		'status'=>true,
        // 		'message'=>'State is required please recheck candidate form'
        // 	];
        // }

        return (object) $data;
    }

    public static function stateId($id)
    {
        $stateId = DB::table('city_list')->where('city_id', $id)->first();

        return ($stateId) ? $stateId->state_id : '';
    }

    public static function allCities()
    {
        $cities = DB::table('city_list')
            ->join('state_list', 'state_list.id', '=', 'city_list.state_id')
            ->join('countries', 'countries.id', '=', 'state_list.country_id')
            ->select('city_list.city as city_name', 'state_list.state as state_name', 'countries.country_name')
            ->where('city_list.city_id', '=', '1')
            ->orderBy('city_list.city')
            ->limit(10)
            ->cursor()
            ->map(function ($city) {
                return $city->city_name.' | '.$city->state_name.' | '.$city->country_name;
            });

        return ($cities) ? $cities : '';

    }

    public static function allCitiesSelected($cityID)
    {

        $citylistSelect = DB::table('city_list')
            ->join('state_list', 'state_list.id', '=', 'city_list.state_id')
            ->join('countries', 'countries.id', '=', 'state_list.country_id')
            ->select('city_list.city as city_name', 'state_list.state as state_name', 'countries.country_name')
            ->where('city_list.city', '!=', '')
            ->whereIn('city_list.city_id', $cityID)
            ->cursor()->map(function ($city) {
                return $city->city_name.' | '.$city->state_name.' | '.$city->country_name;
            });
        // $jobCitySelectedTag  =
        $jobCitySelectedTag = $citylistSelect->toArray();

        return ($jobCitySelectedTag) ? implode(',', $jobCitySelectedTag) : '';

    }

    public static function allQualificationSelected($jobyID)
    {

        $qualificationlistSelect = DB::table('job_qualifications')
            ->select('job_qualifications.qualification_name')
            ->where('job_qualifications.job_id', $jobyID)
            ->get()->map(function ($qualification) {
                return $qualification->qualification_name;
            });
        // $jobqualificationSelectedTag  =
        $jobqualificationSelectedTag = $qualificationlistSelect->toArray();

        // dd($qualificationlistSelect);

        return ($jobqualificationSelectedTag) ? implode(',', $jobqualificationSelectedTag) : '';

    }

    public static function Jobcities($cityID)
    {

        $Company_id = auth()->user()->company_id;

        $citylistSelect = DB::table('client_city_jobs')
            ->join('city_list', 'city_list.city_id', '=', 'client_city_jobs.city_id')
            ->join('state_list', 'state_list.id', '=', 'city_list.state_id')
            ->join('countries', 'countries.id', '=', 'state_list.country_id')
            ->select('city_list.city as city_name', 'state_list.state as state_name', 'countries.country_name')
            ->where('city_list.city', '!=', '')
            ->where('client_city_jobs.job_id', $cityID)
            // ->where('client_city_jobs.company_id', $Company_id)
            ->get()->map(function ($city) {
                return $city->city_name;
            });
        // $jobCitySelectedTag  =
        $jobCitySelectedTag = $citylistSelect->toArray();

        return ($jobCitySelectedTag) ? implode(' , ', $jobCitySelectedTag) : '';

    }

    public static function firstJobcities($id)
    {
        $Company_id = auth()->user()->company_id;

        $citylistSelect = DB::table('client_city_jobs')
            ->join('city_list', 'city_list.city_id', '=', 'client_city_jobs.city_id')
            ->join('state_list', 'state_list.id', '=', 'city_list.state_id')
            ->join('countries', 'countries.id', '=', 'state_list.country_id')
            ->select('city_list.city as city_name', 'state_list.state as state_name', 'countries.country_name')
            ->where('city_list.city', '!=', '')
            ->where('client_city_jobs.job_id', $id)
            // ->where('client_city_jobs.company_id', $Company_id)
            ->get();

        $total = $citylistSelect->count();
        if ($total == 0) {
            return [
                'display' => '',
                'tooltip' => '',
                'countPlus' => '',
            ];
        }
        $first = $citylistSelect->first();
        $cityName = $first->city_name;
        $stateName = $first->state_name;

        $otherCities = $citylistSelect->slice(1)->pluck('city_name')->toArray();
        $tooltip = implode(', ', $otherCities);

        $display = $cityName;
        $countPlus = '';
        if ($total > 1) {
            $countPlus = ' +'.($total - 1).' Other City';
        }
        $display .= '`,`'.$stateName;

        return [
            'display' => $display,
            'tooltip' => $tooltip,
            'countPlus' => $countPlus,
        ];

    }

    public static function JobcitiesTags($cityID)
    {

        $citylistSelect = DB::table('client_city_jobs')
            ->join('city_list', 'city_list.city_id', '=', 'client_city_jobs.city_id')
            ->join('state_list', 'state_list.id', '=', 'city_list.state_id')
            ->join('countries', 'countries.id', '=', 'state_list.country_id')
            ->select('city_list.city as city_name', 'state_list.state as state_name', 'countries.country_name')
            ->where('city_list.city', '!=', '')
            ->where('client_city_jobs.job_id', $cityID)
            ->take(1)->get()->map(function ($city) {
                return $city->city_name.' | '.$city->state_name.' | '.$city->country_name;
            });
        // $jobCitySelectedTag  =
        $jobCitySelectedTag = $citylistSelect->toArray();

        return ($jobCitySelectedTag) ? implode(' , ', $jobCitySelectedTag) : '';

    }

    // Same query as JobcitiesTags() but WITHOUT the ->take(1) — that limit
    // meant a job configured with multiple locations (e.g. "Hyderabad + 1
    // Other City") only ever had its FIRST city auto-applied as a filter,
    // silently dropping the rest. JobcitiesTags() itself is left untouched
    // since it has ~13 other call sites across the codebase that expect a
    // single joined string; this returns every configured city as a plain
    // array of "City | State | Country" strings instead, for callers that
    // want to seed all of them (e.g. multiple Tagify tags), not just one.
    public static function JobcitiesTagsAll($jobID)
    {

        $citylistSelect = DB::table('client_city_jobs')
            ->join('city_list', 'city_list.city_id', '=', 'client_city_jobs.city_id')
            ->join('state_list', 'state_list.id', '=', 'city_list.state_id')
            ->join('countries', 'countries.id', '=', 'state_list.country_id')
            ->select('city_list.city as city_name', 'state_list.state as state_name', 'countries.country_name')
            ->where('city_list.city', '!=', '')
            ->where('client_city_jobs.job_id', $jobID)
            ->get()->map(function ($city) {
                return $city->city_name.' | '.$city->state_name.' | '.$city->country_name;
            });

        return $citylistSelect->toArray();

    }

    public static function companyMainAddress($companyId)
    {
        $clientDetails = DB::table('clients')->where('clients.company_id', '=', $companyId)->where('self_company', '=', 1)->first();
        $clientMainLocation = 101;
        if ($clientDetails) {
            $client_loc = DB::table('client_location')
                ->select('client_location.country_id')
                ->where('client_location.client_id', $clientDetails->id)
                ->first();
            if ($client_loc) {
                $clientMainLocation = $client_loc->country_id;
            }

        }

        return ($clientMainLocation) ? $clientMainLocation : 101;

    }

    public static function JobCountries($JobID)
    {

        $citylistSelect = DB::table('client_city_jobs')
            ->join('city_list', 'city_list.city_id', '=', 'client_city_jobs.city_id')
            ->join('state_list', 'state_list.id', '=', 'city_list.state_id')
            ->join('countries', 'countries.id', '=', 'state_list.country_id')
            ->select('city_list.city as city_name', 'state_list.state as state_name', 'countries.country_name', 'countries.id')
            ->where('city_list.city', '!=', '')
            ->where('client_city_jobs.job_id', $JobID)
            ->get()->map(function ($city) {
                return $city->id;
            });
        // $jobCitySelectedTag  =
        $jobCitySelectedTag = $citylistSelect->toArray();

        return ($jobCitySelectedTag) ? $jobCitySelectedTag : '';
    }

    public static function companyAsVendorAndClient($clientComId, $company_id)
    {
        $data = [
            'Company' => '',
            'vendor_name' => '',
            'vendor_phone' => '',
            'vendor_email' => '',
            'vendor_designation' => '',
            'client_company' => '',
            'client_name' => '',
            'client_phone' => '',
            'client_email' => '',
            'client_designation' => '',
        ];

        $strAddedVendor = DB::table('users')
            ->join('role_user', 'role_user.user_id', 'users.id')
            ->join('roles', 'roles.id', 'role_user.role_id')
            ->join('users_profile', 'users_profile.user_id', '=', 'users.id')
            ->join('job_applications', 'job_applications.submitted_by', '=', 'users.id')
            ->join('companies', 'companies.id', 'job_applications.company_id')
            ->select('users.id', 'users.name', 'users.email', 'users.mobile', 'roles.display_name', 'users_profile.designation',
                'companies.company_name'
            )
            ->where('companies.id', $company_id)
            ->first();

        $strAddedClient = DB::table('users')
            ->join('role_user', 'role_user.user_id', 'users.id')
            ->join('roles', 'roles.id', 'role_user.role_id')
            ->join('users_profile', 'users_profile.user_id', '=', 'users.id')
            ->join('companies', 'companies.id', 'users.company_id')
            ->select('users.id', 'users.name', 'users.email', 'users.mobile', 'roles.display_name', 'users_profile.designation', 'companies.company_name')
            ->where('companies.id', $clientComId)
            ->first();

        // dd($strAddedClient);

        if ($strAddedVendor) {

            $data = [
                'vendor_company' => (@$strAddedVendor->company_name) ? @$strAddedVendor->company_name : '',
                'vendor_name' => ($strAddedVendor->name) ? @$strAddedVendor->name : '',
                'vendor_phone' => ($strAddedVendor->mobile) ? @$strAddedVendor->mobile : '',
                'vendor_email' => ($strAddedVendor->email) ? @$strAddedVendor->email : '',
                'vendor_designation' => ($strAddedVendor->designation) ? @$strAddedVendor->designation : '',
                'client_company' => ($strAddedClient->company_name) ? @$strAddedClient->company_name : '',
                'client_name' => ($strAddedClient->name) ? @$strAddedClient->name : '',
                'client_phone' => ($strAddedClient->mobile) ? @$strAddedClient->mobile : '',
                'client_email' => ($strAddedClient->email) ? @$strAddedClient->email : '',
                'client_designation' => ($strAddedClient->designation) ? @$strAddedClient->designation : '',
            ];

            return (object) $data;
        }

    }

    public static function companyAsVendor($clientComId, $company_id)
    {
        $data = [
            'Company' => '',
            'vendor_name' => '',
            'vendor_phone' => '',
            'vendor_email' => '',
            'vendor_designation' => '',
            'client_company' => '',
            'client_name' => '',
            'client_phone' => '',
            'client_email' => '',
            'client_designation' => '',
        ];

        $strAddedVendor = DB::table('users')
            ->leftJoin('role_user', 'role_user.user_id', 'users.id')
            ->leftJoin('roles', 'roles.id', 'role_user.role_id')
            ->leftJoin('users_profile', 'users_profile.user_id', '=', 'users.id')
            ->leftJoin('companies', 'companies.id', 'users.company_id')
            ->select('users.id', 'users.name', 'users.email', 'users.mobile', 'roles.display_name', 'users_profile.designation',
                'companies.company_name'
            )
            ->where('companies.id', $company_id)
            ->first();

        $strAddedClient = DB::table('users')
            ->leftJoin('role_user', 'role_user.user_id', 'users.id')
            ->leftJoin('roles', 'roles.id', 'role_user.role_id')
            ->leftJoin('users_profile', 'users_profile.user_id', '=', 'users.id')
            ->join('companies', 'companies.id', 'users.company_id')
            ->select('users.id', 'users.name', 'users.email', 'users.mobile', 'roles.display_name', 'users_profile.designation',
                'companies.company_name')
            ->where('companies.id', $clientComId)
            ->first();

        // dd($strAddedClient);

        if ($strAddedVendor) {

            $data = [
                'vendor_company' => (@$strAddedVendor->company_name) ? @$strAddedVendor->company_name : '',
                'vendor_name' => ($strAddedVendor->name) ? @$strAddedVendor->name : '',
                'vendor_phone' => ($strAddedVendor->mobile) ? @$strAddedVendor->mobile : '',
                'vendor_email' => ($strAddedVendor->email) ? @$strAddedVendor->email : '',
                'vendor_designation' => ($strAddedVendor->designation) ? @$strAddedVendor->designation : '',
                'client_company' => ($strAddedClient->company_name) ? @$strAddedClient->company_name : '',
                'client_name' => ($strAddedClient->name) ? @$strAddedClient->name : '',
                'client_phone' => ($strAddedClient->mobile) ? @$strAddedClient->mobile : '',
                'client_email' => ($strAddedClient->email) ? @$strAddedClient->email : '',
                'client_designation' => ($strAddedClient->designation) ? @$strAddedClient->designation : '',
            ];

            return (object) $data;
        }

    }

    public static function companyClientAddress($companyId)
    {
        $clientMainLocation = '';
        $clientDetails = DB::table('clients')->where('clients.company_id', '=', $companyId)->first();
        if ($clientDetails) {
            $client_loc = DB::table('client_location')
                ->select('city_list.city')
                ->join('city_list', 'city_list.city_id', '=', 'client_location.city_id')
                ->where('client_location.client_id', $clientDetails->id)
                ->first();
            if ($client_loc) {
                $clientMainLocation = $client_loc->city;
            }

        }

        return ($clientMainLocation) ? $clientMainLocation : '';

    }

    public static function qualificationFilterCondition($qualificationId)
    {
        $education = [];
        $anyDegree = [];
        $postQualifications = [];
        $qualifications = [];
        if ($qualificationId == 59 || $qualificationId == 60) {
            $anyDegree = DB::table('qualification_master')->pluck('id')->toArray();
            $new_values = array_diff($anyDegree, $education);
            $education = array_merge($education, $new_values);
        } elseif ($qualificationId == 58) {
            // Any Post Qualifications
            $postQualifications = [24, 30, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 60];
            $new_values = array_diff($postQualifications, $education);
            $education = array_merge($education, $new_values);
        } elseif ($qualificationId == 57) {
            // Any Qualifications
            $qualifications = [3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 31, 60];
            $new_values = array_diff($qualifications, $education);
            $education = array_merge($education, $new_values);
        } else {
            array_push($education, $qualificationId);
        }

        return $education;
    }

    public static function coinsDeductions($companyid, $eventId, $userId, $from_company_id, $jobId = null, $teamId = null)
    {
        /** @var \App\Services\CoinService $coinService */
        $coinService = app(\App\Services\CoinService::class);

        return $coinService->deductCoinsAtomic(
            (int) $companyid,
            (int) $eventId,
            (int) $userId,
            (int) $from_company_id,
            $jobId,
            $teamId
        );
    }

    public static function coinsValidations($companyid, $eventId, $userId, $from_company_id, $jobId = null, $teamId = null)
    {
        /** @var \App\Services\CoinService $coinService */
        $coinService = app(\App\Services\CoinService::class);

        return $coinService->validateCoinsAtomic(
            (int) $companyid,
            (int) $eventId,
            (int) $userId,
            (int) $from_company_id,
            $jobId,
            $teamId
        );
        $coin_events = DB::table('coin_events')->where('id', $eventId)->first();
        // dd($companyid,$eventId,$userId);
        $company_coins = DB::table('company_coins')->where('company_id', $companyid)->where('event_type', $coin_events->event_type)->first();
        $end_date = $company_coins->end_date;
        $current_date = date('Y-m-d');
        $end_date_timestamp = strtotime($end_date);
        $current_date_timestamp = strtotime($current_date);

        // dd($company_coins);
        $free_update_date = Carbon::parse($company_coins->free_update_date);
        $today = Carbon::today();
        $diffInDays = abs($today->diffInDays($free_update_date));
        $currentMonth = $today->month;
        $currentYear = $today->year;
        $specificDay = $free_update_date->day;

        $company_coins_free = DB::table('coin_packages')
            ->where('coin_packages.custome_pck', 2)
            ->where('event_type', '=', $coin_events->event_type)
            ->select('coin_packages.*')
            ->get()
            ->map(function ($coinOrder) {
                $coinOrder->coin_order_events = DB::table('coin_events')
                    ->where('event_type', $coinOrder->event_type)
                    ->select('coin_events.*')
                    ->where('status', '=', 1)
                    ->get()
                    ->toArray();

                return (array) $coinOrder;
            })
            ->toArray();

        if ($diffInDays >= 30 && $company_coins->free_pack_status == 'active' && $company_coins->coins <= 0) {

            if (@$company_coins_free) {

                foreach ($company_coins_free as $package_sales) {

                    $newDate = date('Y-m-d');
                    $currentDate = Carbon::create($currentYear, $currentMonth, $specificDay);
                    $newDate = date('Y-m-d', strtotime($currentDate.' +'.$package_sales['days'].' days'));
                    // dd($currentDate,$newDate);

                    $coin_orders = [
                        'coin_package_id' => $package_sales['id'],
                        'price' => $package_sales['price'],
                        'coins' => $package_sales['coins'],
                        'days' => $package_sales['days'],
                        'event_type' => $package_sales['event_type'],
                        'company_id' => $companyid,
                        'discount' => $package_sales['discount'],
                        'status' => 1,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ];

                    $coin_order_id = DB::table('coin_orders')->insertGetId($coin_orders);
                    foreach ($package_sales['coin_order_events'] as $package_value) {
                        $coin_order_events = [
                            'company_id' => $companyid,
                            'coin_event_id' => $package_value->id,
                            'deductions' => $package_value->default_rate,
                            'deduction_type' => $package_value->deduction_type,
                            'coin_order_id' => $coin_order_id,
                            'required_coin' => $package_value->minimum_required,
                            'event_type' => $package_value->event_type,
                        ];
                        // dd($coin_order_events);
                        DB::table('coin_order_events')->insertGetId($coin_order_events);
                    }

                    $company_coins_check = DB::table('company_coins')->where('company_id', $companyid)->where('event_type', $coin_events->event_type)->first();

                    if ($company_coins_check) {

                        $total_coins_comp = $package_sales['coins'] - $company_coins_check->balance_coin;

                        if ($total_coins_comp > 0) {
                            // The value is positive.
                            $balance_coin = 0;
                            $total_coins = $total_coins_comp;
                        } elseif ($total_coins_comp < 0) {
                            // The value is negative.
                            $balance_coin = abs($total_coins_comp);
                            $total_coins = 0;
                        } else {
                            // The value is zero.
                            $balance_coin = 0;
                            $total_coins = 0;
                        }
                        $settlement_coins = $company_coins_check->balance_coin - $balance_coin;

                    } else {
                        $balance_coin = 0;
                        $settlement_coins = 0;
                        $total_coins = $package_sales['coins'];
                    }

                    $company_coins_value = [
                        'coin_order_id' => $coin_order_id,
                        'company_id' => $companyid,
                        'event_type' => $package_sales['event_type'],
                        'amount' => $package_sales['price'],
                        'discount' => $package_sales['discount'],
                        'coins' => $total_coins,
                        'balance_coin' => $balance_coin,
                        'actual_coins' => $package_sales['coins'],
                        'status' => 'valid',
                        'free_pack_status' => 'active',
                        'start_date' => $currentDate,
                        'end_date' => $newDate,
                        'free_update_date' => $currentDate,
                        'updated_at' => date('Y-m-d H:i:s'),

                    ];

                    DB::table('company_coins')->where('company_id', $companyid)->where('event_type', $package_sales['event_type'])->update($company_coins_value);
                    self::addUpdateCompanyCoins($companyid);

                    $coinEvents = DB::table('coin_orders')
                        ->join('coin_packages', 'coin_packages.id', '=', 'coin_orders.coin_package_id')
                        ->join('coin_package_types', 'coin_package_types.id', '=', 'coin_packages.coin_package_type_id')
                        ->where('coin_orders.id', $coin_order_id)
                        ->select('coin_package_types.title')->first();
                    $coinEventspackage = ($coinEvents) ? $coinEvents->title : '';

                    $coin_transection_logs = [
                        'coin_order_id' => $coin_order_id,
                        'from_company_id' => $companyid,
                        'company_id' => $companyid,
                        'event_by' => $userId,
                        'event' => 'Package Auto Activated',
                        'amount' => $package_sales['price'],
                        'discount' => $package_sales['discount'],
                        'coins' => $package_sales['coins'],
                        'transection_type' => 'credit',
                        'remain_coins' => $total_coins,
                        'actual_coins' => $package_sales['coins'],
                        'settlement_coins' => 0,
                        'event_type' => $package_sales['event_type'],
                        'start_date' => $currentDate,
                        'end_date' => $newDate,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ];

                    DB::table('coin_transection_logs')->insertGetId($coin_transection_logs);

                }
                $company_coins = DB::table('company_coins')->where('company_id', $companyid)->where('event_type', $coin_events->event_type)->first();
            }

            // || $current_date_timestamp > $end_date_timestamp
        } elseif ($company_coins->coins < 0) {

            $coin_orders = DB::table('coin_orders')->where('company_id', $companyid)->where('event_type', $coin_events->event_type)->where('status', 2)->first();
            if ($coin_orders) {

                $newDate = date('Y-m-d');
                $currentDate = Carbon::create($newDate);
                $newDate = date('Y-m-d', strtotime($currentDate.' +'.$coin_orders->days.' days'));

                $company_coins_check = DB::table('company_coins')->where('company_id', $companyid)->where('event_type', $coin_events->event_type)->first();

                if ($company_coins_check) {

                    $total_coins_comp = $coin_orders->coins - $company_coins_check->balance_coin;

                    if ($total_coins_comp > 0) {
                        // The value is positive.
                        $balance_coin = 0;
                        $total_coins = $total_coins_comp;
                    } elseif ($total_coins_comp < 0) {
                        // The value is negative.
                        $balance_coin = abs($total_coins_comp);
                        $total_coins = 0;
                    } else {
                        // The value is zero.
                        $balance_coin = 0;
                        $total_coins = 0;
                    }
                    $settlement_coins = $company_coins_check->balance_coin - $balance_coin;

                } else {
                    $balance_coin = 0;
                    $settlement_coins = 0;
                    $total_coins = $coin_orders->coins;
                }

                $company_coins_value = [
                    'coin_order_id' => $coin_orders->id,
                    'amount' => $coin_orders->price,
                    'discount' => $coin_orders->discount,
                    'coins' => $total_coins,
                    'balance_coin' => $balance_coin,
                    'status' => 'valid',
                    'actual_coins' => $total_coins,
                    'free_pack_status' => 'active',
                    'start_date' => $currentDate,
                    'end_date' => $newDate,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];

                $coin_transection_logs = [
                    'coin_order_id' => $coin_orders->id,
                    'from_company_id' => $companyid,
                    'company_id' => $companyid,
                    'event_by' => $userId,
                    'event' => 'Package Auto Activated',
                    'amount' => $coin_orders->price,
                    'discount' => $coin_orders->discount,
                    'coins' => $coin_orders->coins,
                    'transection_type' => 'credit',
                    'remain_coins' => $total_coins,
                    'actual_coins' => $coin_orders->coins,
                    'settlement_coins' => 0,
                    'event_type' => $coin_orders->event_type,
                    'start_date' => $currentDate,
                    'end_date' => $newDate,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];

                DB::table('coin_transection_logs')->insertGetId($coin_transection_logs);

                DB::table('company_coins')->where('company_id', $companyid)->where('event_type', $coin_orders->event_type)->update($company_coins_value);

                self::addUpdateCompanyCoins($companyid);

                DB::table('coin_orders')->where('id', $companyid)->update(['status' => 1]);

                $company_coins = DB::table('company_coins')->where('company_id', $companyid)->where('event_type', $coin_events->event_type)->first();
            }

        }

        $coin_order_events = DB::table('coin_order_events')->where('company_id', $companyid)->where('coin_event_id', $eventId)->where('event_type', $coin_events->event_type)->where('coin_order_id', $company_coins->coin_order_id)->first();

        $Company = Company::withoutGlobalScopes()->find($companyid);

        $balance_coin_limit = '';
        if ($company_coins->event_type == 'operational') {
            $balance_coin_limit = 20;
        } else {
            $balance_coin_limit = 1000;
        }

        if ($coin_events && $company_coins && $coin_order_events) {

            // $balance_coin_limit > $company_coins->balance_coin =  $company_coins->coins > 0
            //  && $current_date_timestamp <= $end_date_timestamp

            if ($company_coins->coins > 0) {

                // dd("dk");
                if ($company_coins->coins >= $coin_order_events->required_coin) {
                    $data = [
                        'status' => false,
                        'message' => 'Success',
                        'company_coins' => @$company_coins->coins,
                    ];

                    return (object) $data;

                } else {
                    $data = [
                        'status' => true,
                        'message' => '"'.$Company->company_name.'" does not have sufficient coins, minimum required coins '.$coin_order_events->required_coin.' ',
                        'company_coins' => @$company_coins->coins,
                    ];

                    return (object) $data;
                }

            } elseif ($company_coins->coins <= 0) {
                $data = [
                    'status' => true,
                    'message' => '"'.$Company->company_name.'" does not have sufficient coins',
                    'company_coins' => @$company_coins->coins,
                ];

                return (object) $data;

            } else {
                $data = [
                    'status' => true,
                    'message' => '"'.$Company->company_name.'" packages have expired',
                    'company_coins' => @$company_coins->coins,
                ];

                return (object) $data;
            }

        } else {

            $data = [
                'status' => true,
                'message' => '"'.$Company->company_name.'" Event not allow',
                'company_coins' => @$company_coins->coins,
            ];
        }

        return (object) $data;
    }

    public static function coinsValidationsAllcompany($companyid, $eventId, $coinsRequ)
    {

        $coin_events = DB::table('coin_events')->where('id', $eventId)->first();
        // dd($coin_events,$companyid,$eventId);
        $company_coins = DB::table('company_coins')->where('company_id', $companyid)->where('event_type', $coin_events->event_type)->first();
        $usercompany = Company::join('users', 'users.company_id', '=', 'companies.id')->where('users.root_user', '=', 1)->select('users.id')->first();
        if ($company_coins == null) {
            $data = [
                'status' => true,
                'message' => 'Client company does not have sufficient coins',
                'company_coins' => '',
            ];

            return (object) $data;
        }

        // dd($companyid,$company_coins,$eventId,$coin_events->event_type,$coin_events);
        $userId = ($usercompany) ? $usercompany->id : 0;
        $end_date = $company_coins->end_date;
        $current_date = date('Y-m-d');
        $end_date_timestamp = strtotime($end_date);
        $current_date_timestamp = strtotime($current_date);

        $free_update_date = Carbon::parse($company_coins->free_update_date);
        $today = Carbon::today();
        $diffInDays = abs($today->diffInDays($free_update_date));
        $currentMonth = $today->month;
        $currentYear = $today->year;
        $specificDay = $free_update_date->day;

        $company_coins_free = DB::table('coin_packages')
            ->where('coin_packages.custome_pck', 2)
            ->where('event_type', '=', $coin_events->event_type)
            ->select('coin_packages.*')
            ->get()
            ->map(function ($coinOrder) {
                $coinOrder->coin_order_events = DB::table('coin_events')
                    ->where('event_type', $coinOrder->event_type)
                    ->select('coin_events.*')
                    ->where('status', '=', 1)
                    ->get()
                    ->toArray();

                return (array) $coinOrder;
            })
            ->toArray();
        if ($diffInDays >= 30 && $company_coins->free_pack_status == 'active' && $company_coins->coins <= 0) {

            if (@$company_coins_free) {

                foreach ($company_coins_free as $package_sales) {

                    $newDate = date('Y-m-d');
                    $currentDate = Carbon::create($currentYear, $currentMonth, $specificDay);
                    $newDate = date('Y-m-d', strtotime($currentDate.' +'.$package_sales['days'].' days'));
                    // dd($currentDate,$newDate);

                    $coin_orders = [
                        'coin_package_id' => $package_sales['id'],
                        'price' => $package_sales['price'],
                        'coins' => $package_sales['coins'],
                        'days' => $package_sales['days'],
                        'event_type' => $package_sales['event_type'],
                        'company_id' => $companyid,
                        'discount' => $package_sales['discount'],
                        'status' => 1,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ];

                    $coin_order_id = DB::table('coin_orders')->insertGetId($coin_orders);
                    foreach ($package_sales['coin_order_events'] as $package_value) {
                        $coin_order_events = [
                            'company_id' => $companyid,
                            'coin_event_id' => $package_value->id,
                            'deductions' => $package_value->default_rate,
                            'deduction_type' => $package_value->deduction_type,
                            'coin_order_id' => $coin_order_id,
                            'required_coin' => $package_value->minimum_required,
                            'event_type' => $package_value->event_type,
                        ];
                        // dd($coin_order_events);
                        DB::table('coin_order_events')->insertGetId($coin_order_events);
                    }

                    $company_coins_check = DB::table('company_coins')->where('company_id', $companyid)->where('event_type', $coin_events->event_type)->first();

                    if ($company_coins_check) {

                        $total_coins_comp = $package_sales['coins'] - $company_coins_check->balance_coin;

                        if ($total_coins_comp > 0) {
                            // The value is positive.
                            $balance_coin = 0;
                            $total_coins = $total_coins_comp;
                        } elseif ($total_coins_comp < 0) {
                            // The value is negative.
                            $balance_coin = abs($total_coins_comp);
                            $total_coins = 0;
                        } else {
                            // The value is zero.
                            $balance_coin = 0;
                            $total_coins = 0;
                        }
                        $settlement_coins = $company_coins_check->balance_coin - $balance_coin;

                    } else {
                        $balance_coin = 0;
                        $settlement_coins = 0;
                        $total_coins = $package_sales['coins'];
                    }

                    $company_coins_value = [
                        'coin_order_id' => $coin_order_id,
                        'company_id' => $companyid,
                        'event_type' => $package_sales['event_type'],
                        'amount' => $package_sales['price'],
                        'discount' => $package_sales['discount'],
                        'coins' => $total_coins,
                        'balance_coin' => $balance_coin,
                        'actual_coins' => $package_sales['coins'],
                        'status' => 'valid',
                        'free_pack_status' => 'active',
                        'start_date' => $currentDate,
                        'end_date' => $newDate,
                        'free_update_date' => $currentDate,
                        'updated_at' => date('Y-m-d H:i:s'),

                    ];

                    DB::table('company_coins')->where('company_id', $companyid)->where('event_type', $package_sales['event_type'])->update($company_coins_value);

                    self::addUpdateCompanyCoins($companyid);

                    $coinEvents = DB::table('coin_orders')
                        ->join('coin_packages', 'coin_packages.id', '=', 'coin_orders.coin_package_id')
                        ->join('coin_package_types', 'coin_package_types.id', '=', 'coin_packages.coin_package_type_id')
                        ->where('coin_orders.id', $coin_order_id)
                        ->select('coin_package_types.title')->first();
                    $coinEventspackage = ($coinEvents) ? $coinEvents->title : '';

                    $coin_transection_logs = [
                        'coin_order_id' => $coin_order_id,
                        'from_company_id' => $companyid,
                        'company_id' => $companyid,
                        'event_by' => $userId,
                        'event' => 'Package Auto Activated',
                        'amount' => $package_sales['price'],
                        'discount' => $package_sales['discount'],
                        'coins' => $package_sales['coins'],
                        'transection_type' => 'credit',
                        'remain_coins' => $total_coins,
                        'actual_coins' => $package_sales['coins'],
                        'settlement_coins' => 0,
                        'event_type' => $package_sales['event_type'],
                        'start_date' => $currentDate,
                        'end_date' => $newDate,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ];

                    DB::table('coin_transection_logs')->insertGetId($coin_transection_logs);

                }
                $company_coins = DB::table('company_coins')->where('company_id', $companyid)->where('event_type', $coin_events->event_type)->first();
            }

            // || $current_date_timestamp > $end_date_timestamp
        } elseif ($company_coins->coins < 0) {

            $coin_orders = DB::table('coin_orders')->where('company_id', $companyid)->where('event_type', $coin_events->event_type)->where('status', 2)->first();
            if ($coin_orders) {
                $newDate = date('Y-m-d');
                $currentDate = Carbon::create($newDate);
                $newDate = date('Y-m-d', strtotime($currentDate.' +'.$coin_orders->days.' days'));

                $company_coins_check = DB::table('company_coins')->where('company_id', $companyid)->where('event_type', $coin_events->event_type)->first();

                if ($company_coins_check) {

                    $total_coins_comp = $coin_orders->coins - $company_coins_check->balance_coin;

                    if ($total_coins_comp > 0) {
                        // The value is positive.
                        $balance_coin = 0;
                        $total_coins = $total_coins_comp;
                    } elseif ($total_coins_comp < 0) {
                        // The value is negative.
                        $balance_coin = abs($total_coins_comp);
                        $total_coins = 0;
                    } else {
                        // The value is zero.
                        $balance_coin = 0;
                        $total_coins = 0;
                    }
                    $settlement_coins = $company_coins_check->balance_coin - $balance_coin;

                } else {
                    $balance_coin = 0;
                    $settlement_coins = 0;
                    $total_coins = $coin_orders->coins;
                }

                $company_coins_value = [
                    'coin_order_id' => $coin_orders->id,
                    'amount' => $coin_orders->price,
                    'discount' => $coin_orders->discount,
                    'coins' => $total_coins,
                    'balance_coin' => $balance_coin,
                    'status' => 'valid',
                    'actual_coins' => $total_coins,
                    'free_pack_status' => 'active',
                    'start_date' => $currentDate,
                    'end_date' => $newDate,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];

                $coin_transection_logs = [
                    'coin_order_id' => $coin_orders->id,
                    'from_company_id' => $companyid,
                    'company_id' => $companyid,
                    'event_by' => $userId,
                    'event' => 'Package Auto Activated',
                    'amount' => $coin_orders->price,
                    'discount' => $coin_orders->discount,
                    'coins' => $coin_orders->coins,
                    'transection_type' => 'credit',
                    'remain_coins' => $total_coins,
                    'actual_coins' => $coin_orders->coins,
                    'settlement_coins' => 0,
                    'event_type' => $coin_orders->event_type,
                    'start_date' => $currentDate,
                    'end_date' => $newDate,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];

                DB::table('coin_transection_logs')->insertGetId($coin_transection_logs);

                DB::table('company_coins')->where('company_id', $companyid)->where('event_type', $coin_orders->event_type)->update($company_coins_value);

                self::addUpdateCompanyCoins($companyid);

                DB::table('coin_orders')->where('id', $companyid)->update(['status' => 1]);

                $company_coins = DB::table('company_coins')->where('company_id', $companyid)->where('event_type', $coin_events->event_type)->first();
            }

        }

        $coin_order_events = DB::table('coin_order_events')->where('company_id', $companyid)->where('coin_event_id', $eventId)->where('event_type', $coin_events->event_type)->where('coin_order_id', $company_coins->coin_order_id)->first();

        // dd($coin_order_events);

        $Company = Company::withoutGlobalScopes()->find($companyid);
        if ($coin_events && $company_coins && $coin_order_events) {

            $coinsValid = (@$coin_order_events->deductions) ? $coin_order_events->deductions * $coinsRequ : 0;

            // dd($coin_order_events,$coinsValid); required_coin

            // && $current_date_timestamp <= $end_date_timestamp
            if ($company_coins->coins >= $coinsValid) {

                if ($company_coins->coins >= $coin_order_events->required_coin) {
                    $data = [
                        'status' => false,
                        'message' => 'Success',
                        'company_coins' => @$company_coins->coins,
                    ];

                    return (object) $data;
                } else {
                    $data = [
                        'status' => true,
                        'message' => '"'.$Company->company_name.'" does not have sufficient coins, minimum required '.$coin_order_events->event_type.' coins '.$coin_order_events->required_coin.' ',
                        'company_coins' => @$company_coins->coins,
                    ];

                    return (object) $data;
                }

            } elseif ($company_coins->coins < $coinsValid) {
                $data = [
                    'status' => true,
                    'message' => '"'.$Company->company_name.'" does not have sufficient '.$coin_order_events->event_type.' coins',
                    'company_coins' => @$company_coins->coins,
                ];

                return (object) $data;

            }

        } else {

            $data = [
                'status' => true,
                'message' => '"'.$Company->company_name.'" Event not allow',
                'company_coins' => @$company_coins->coins,
            ];
        }

        return (object) $data;
    }

    public static function value_encrypt($data)
    {
        return encrypt($data);
        // $customKey = bin2hex(random_bytes(32)); // 32 bytes key for AES-256-CBC
        $cipherMethod = 'AES-256-CBC';
        $key = config('services.custom_encryption_key');
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipherMethod));
        $encryptedData = openssl_encrypt($data, $cipherMethod, $key, 0, $iv);

        return base64_encode($iv.$encryptedData); // Combine IV and encrypted data
    }

    public static function value_decrypt($data)
    {
        // Use strict null/empty-string check — PHP's empty('0') is true which would
        // incorrectly swallow a legitimate zero appId before the numeric path runs.
        if ($data === null || $data === '') {
            return null;
        }
        // Plain numeric IDs (new-candidate links pass raw integers) need no decryption.
        if (is_numeric($data)) {
            return (int) $data;
        }
        // Laravel Crypt::decrypt() is the primary path.
        // URL transport mutates base64 '+' → ' '; the second attempt restores it.
        try {
            return decrypt($data);
        } catch (\Throwable $e) {
            try {
                return decrypt(str_replace(' ', '+', $data));
            } catch (\Throwable) {
                return null;
            }
        }
    }

    public static function benchAutoPause($companyid)
    {
        $company_coins = DB::table('company_coins')->where('company_id', $companyid)->where('event_type', 'operational')->first();

        $balance_coin_limit = 20;
        if ($company_coins) {
            $end_date = $company_coins->end_date;
            $current_date = date('Y-m-d');
            $end_date_timestamp = strtotime($end_date);
            $current_date_timestamp = strtotime($current_date);
            //  && $current_date_timestamp <= $end_date_timestamp
            if ($balance_coin_limit > $company_coins->balance_coin) {
                return true;
            } else {
                DB::table('job_applications')->where('company_id', '=', $companyid)->where('status', '=', 4)->update(['status' => 4]);

                return false;
            }

        }

    }

    public static function dataCnt($id)
    {

        $company_id = $id;

        $dataCnt = [];

        $dataCnt['jobsActive'] = 0;
        $dataCnt['jobsClose'] = 0;
        $dataCnt['jobsHold'] = 0;

        // All Jobs
        $jobsActive = DB::table('jobs')->select('jobs.id');
        $jobsActive = $jobsActive->leftJoin('recommendation_relation', function ($join) use ($company_id) {
            $join->on('recommendation_relation.job_id', '=', 'jobs.id');
            $join->on('recommendation_relation.to_company_id', '=', DB::raw("'".$company_id."'"));
        });

        $jobsActive = $jobsActive->where('jobs.company_id', '=', $company_id);
        $jobsActive = $jobsActive->where('jobs.status', '=', 'active');
        $jobsActive = $jobsActive->orWhere(function ($query) use ($company_id) {
            return
                $query->where('recommendation_relation.final_status', '=', 1)
                    ->where('recommendation_relation.to_company_id', '=', $company_id)
                    ->where('jobs.status', '=', 'active');
        });
        $jobsActive = $jobsActive->orWhere(function ($query) use ($company_id) {
            return
                $query->where('recommendation_relation.final_status', '=', 2)
                    ->where('recommendation_relation.to_company_id', '=', $company_id)
                    ->where('jobs.status', '=', 'active');
        });
        $jobsActive = $jobsActive->count();
        $dataCnt['jobsActive'] = $jobsActive;

        $jobsClose = DB::table('jobs')->select('jobs.id');
        $jobsClose = $jobsClose->leftJoin('recommendation_relation', function ($join) use ($company_id) {
            $join->on('recommendation_relation.job_id', '=', 'jobs.id');
            $join->on('recommendation_relation.to_company_id', '=', DB::raw("'".$company_id."'"));
        });

        $jobsClose = $jobsClose->where('jobs.company_id', '=', $company_id);
        $jobsClose = $jobsClose->where('jobs.status', '=', 'inactive');
        $jobsClose = $jobsClose->orWhere(function ($query) use ($company_id) {
            return
                $query->where('recommendation_relation.final_status', '=', 1)
                    ->where('recommendation_relation.to_company_id', '=', $company_id)
                    ->where('jobs.status', '=', 'inactive');
        });
        $jobsClose = $jobsClose->orWhere(function ($query) use ($company_id) {
            return
                $query->where('recommendation_relation.final_status', '=', 2)
                    ->where('recommendation_relation.to_company_id', '=', $company_id)
                    ->where('jobs.status', '=', 'inactive');
        });
        $jobsClose = $jobsClose->count();
        $dataCnt['jobsClose'] = $jobsClose;

        $jobsHold = DB::table('jobs')->select('jobs.id');
        $jobsHold = $jobsHold->leftJoin('recommendation_relation', function ($join) use ($company_id) {
            $join->on('recommendation_relation.job_id', '=', 'jobs.id');
            $join->on('recommendation_relation.to_company_id', '=', DB::raw("'".$company_id."'"));
        });

        $jobsHold = $jobsHold->where('jobs.company_id', '=', $company_id);
        $jobsHold = $jobsHold->where('jobs.status', '=', 'hold');
        $jobsHold = $jobsHold->orWhere(function ($query) use ($company_id) {
            return
                $query->where('recommendation_relation.final_status', '=', 1)
                    ->where('recommendation_relation.to_company_id', '=', $company_id)
                    ->where('jobs.status', '=', 'hold');
        });
        $jobsHold = $jobsHold->orWhere(function ($query) use ($company_id) {
            return
                $query->where('recommendation_relation.final_status', '=', 2)
                    ->where('recommendation_relation.to_company_id', '=', $company_id)
                    ->where('jobs.status', '=', 'hold');
        });
        $jobsHold = $jobsHold->count();
        $dataCnt['jobsHold'] = $jobsHold;

        // dd($dataCnt['allRequisition']);
        return $dataCnt;

    }

    public static function benchCandidateCnt($id)
    {

        $company_id = $id;

        $dataCnt = [];

        $dataCnt['bench_counter'] = 0;
        $dataCnt['pool_counter'] = 0;
        $dataCnt['place_counter'] = 0;

        // All Jobs
        $bench_counter = DB::table('job_applications')->select('job_applications.id');
        $bench_counter = $bench_counter->where('job_applications.company_id', '=', $company_id);
        $bench_counter = $bench_counter->where('job_applications.status', '=', 2);
        $bench_counter = $bench_counter->count();
        $dataCnt['bench_counter'] = $bench_counter;

        $pool_counter = DB::table('job_applications')->select('job_applications.id');
        $pool_counter = $pool_counter->where('job_applications.company_id', '=', $company_id);
        $pool_counter = $pool_counter->where('job_applications.status', '=', 1);
        $pool_counter = $pool_counter->count();
        $dataCnt['pool_counter'] = $pool_counter;

        $place_counter = DB::table('job_applications')->select('job_applications.id');
        $place_counter = $place_counter->where('job_applications.company_id', '=', $company_id);
        $place_counter = $place_counter->where('job_applications.status', '=', 3);
        $place_counter = $place_counter->count();
        $dataCnt['place_counter'] = $place_counter;

        // dd($dataCnt['allRequisition']);
        return $dataCnt;

    }

    public static function spartnerDataCnt($id)
    {

        $company_id = $id;
        $spartnerCounter = [];

        // All Clients
        $spartnerCounter['clientAll'] = 0;
        $clientAll = DB::table('companies')->select('companies.id');
        $clientAll = $clientAll->leftJoin('staffing_company_relation', 'staffing_company_relation.to_company_id', '=', 'companies.id');
        $clientAll = $clientAll->leftJoin('clients', 'clients.company_id', '=', 'staffing_company_relation.to_company_id');
        //    $clientAll = $clientAll->where('clients.self_company', '=', 1);
        $clientAll = $clientAll->where('staffing_company_relation.final_status', '=', 1);
        $clientAll = $clientAll->where('staffing_company_relation.from_company_id', '=', $company_id);
        $clientAll = $clientAll->groupBy('staffing_company_relation.to_company_id');
        $clientAll = $clientAll->get();
        $spartnerCounter['spartnerAll'] = count($clientAll);

        // SENT REQUEST INVITATION
        $spartnerCounter['sentRequest'] = 0;
        $sentRequest = DB::table('staffing_company_invitation')
            ->select('staffing_company_invitation.id')
            ->where('staffing_company_invitation.from_company_id', '=', $company_id)
            ->where('staffing_company_invitation.to_invitation_status', '!=', 1)
            ->count();
        $spartnerCounter['sentRequest'] = $sentRequest;

        // Direct Client
        $spartnerCounter['clientDirect'] = 0;
        $clientDirect = DB::table('staffing_company_relation')
            ->select('staffing_company_relation.id')
            ->where('staffing_company_relation.to_company_id', '=', $company_id)
            ->where('staffing_company_relation.final_status', '=', 1)
            ->count();
        $spartnerCounter['clientDirect'] = $clientDirect;

        // Custom Client
        $spartnerCounter['clientCustom'] = 0;
        $clientCustom = Client::select('clients.id')->where('clients.self_company', '!=', 1)->where('clients.company_id', '=', $company_id)->count();
        $spartnerCounter['clientCustom'] = $clientCustom;

        return $spartnerCounter;
    }

    public static function createPayment($amount, $currency)
    {
        $payment_settings = DB::table('payment_settings')->select('payment_settings.*')->first();

        // ........................................
        $key = $payment_settings->razorpay_key;
        $secret = $payment_settings->razorpay_secret;

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://api.razorpay.com/v1/orders');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);

        // The parameters for creating a Razorpay order
        $postData = [
            'amount' => $amount * 100, // ₹5000 in paisa (₹5000.00)
            'currency' => $currency, // USD 'INR'
            'receipt' => 'order_rcptid_11',
            'payment_capture' => 0, // Auto-capture payment (1 means capture automatically)
        ];

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));

        // Razorpay authentication
        curl_setopt($ch, CURLOPT_USERPWD, $key.':'.$secret);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

        // Execute the cURL request and get the response
        $result = curl_exec($ch);

        if (curl_errno($ch)) {
            // Handle cURL errors
            return 'Error:'.curl_error($ch);
        }

        curl_close($ch);

        $order = json_decode($result, true);

        return $order;

        // dd($order);

        // return view('payment-page', ['orderId' => $order['id'],'currency' => $order['currency'],'amount' => $order['amount']]);

    }

    public static function convertINRToUSD()
    {
        $ip = request()->ip(); // Get user's IP address
        if ($ip === '::1' || $ip === '127.0.0.1' || $ip === 'localhost') {
            return 'INR';
        }

        // ipinfo: HTTPS + 2s timeout + 7-day cache per IP. (Sprint 3 task 3.9)
        $countryCode = \Illuminate\Support\Facades\Cache::remember('ipinfo:country:'.$ip, 604800, function () use ($ip) {
            try {
                $guzzleHttpClient = new GuzzleHttpClient(['timeout' => 2]);
                $response = $guzzleHttpClient->get("https://ipinfo.io/{$ip}/json");
                $data = json_decode($response->getBody(), true);

                return $data['country'] ?? 'Unknown';
            } catch (\Throwable $e) {
                return 'Unknown';
            }
        });

        $currencies = ['US' => 'USD', 'IN' => 'INR'];

        return $currencies[$countryCode] ?? 'USD';
    }

    public static function convertDaysToMonths($days)
    {
        // Assuming 1 month is approximately 30 days
        $months = $days / 30;

        // Return the months as a floating number
        return round($months, 2); // Rounds to 2 decimal places
    }

    public static function paymentstatus($status, $typepay)
    {
        $paymentStatus = $status;
        $status_text = '';
        $status_color = '';
        if ($typepay == 'razorpay') {
            switch ($paymentStatus) {
                case 'created':
                    $status_text = 'Initiated';
                    $status_color = 'warning';
                    break;
                case 'authorized':
                    $status_text = 'Authorized';
                    $status_color = 'primary';
                    break;
                case 'captured':
                    $status_text = 'Success';
                    $status_color = 'success';
                    break;
                case 'failed':
                    $status_text = 'Failed';
                    $status_color = 'danger';
                    break;
                case 'refunded':
                    $status_text = 'Refunded';
                    $status_color = 'success';
                    break;
                case 'partially_refunded':
                    $status_text = 'Partially refunded';
                    $status_color = 'success';
                    break;
                default:
                    $status_text = 'Unknown status';
                    $status_color = 'info';
                    break;
            }
        }

        $data = [
            'status_text' => $status_text,
            'status_color' => $status_color,
        ];

        return (object) $data; // Rounds to 2 decimal places
    }

    public static function offerLetterdetails($offerId)
    {
        //  dd($company_id);

        $rowDataOffer = DB::table('offer_job_applications')
            ->join('job_applications', 'job_applications.id', '=', 'offer_job_applications.application_id')
            ->join('jobs', 'jobs.id', '=', 'offer_job_applications.job_id')
            ->select('offer_job_applications.*', 'job_applications.full_name', 'job_applications.email', 'job_applications.phone', 'jobs.company_id')
            ->where('offer_job_applications.id', $offerId)->first();

        $elasticService = app(ElasticService::class);
        $params = [
            'index' => ELASTIC_CANDIDATES,
            'id' => $rowDataOffer->joined,
        ];

        $response = $elasticService->getClient()->get($params);
        $data_show = (object) $response['_source'];

        // dd($data_show->send_accept_date_time);

        // dd($data_show);

        $jobID = $rowDataOffer->job_id;
        $company_id = $rowDataOffer->company_id;

        $clients = DB::table('clients')
            ->join('companies', 'companies.id', '=', 'clients.company_id')
            ->select('clients.id as clientId', 'companies.logo', 'companies.company_name')
            ->where('company_id', '=', $company_id)
            ->where('self_company', '=', 1)
            ->first();

        $companies = DB::table('users')
            ->select('users.name', 'users.email', 'users.company_id', 'users.id')
            ->where('company_id', '=', $company_id)
            ->where('root_user', '=', 1)
            ->first();

        $jobDetails = DB::table('jobs')->where('id', '=', $jobID)->first();
        $exits_offer = DB::table('offer_create_main_tds')->where('offer_id', '=', $offerId)->where('status', '=', 1)->first();

        // dd($clients);

        $locInfo = DB::table('client_location')
            ->leftJoin('countries', 'countries.id', '=', 'client_location.country_id')
            ->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')
            ->leftJoin('state_list', 'state_list.id', '=', 'client_location.state')
            ->select(
                'client_location.id',
                'client_location.city',
                'client_location.address',
                'client_location.postcode',
                'client_location.country_id',
                'city_list.city as city_name',
                'state_list.state as state_name',
                'countries.country_name'
            )
            ->where('client_id', $clients->clientId)
            ->get();

        // dd( count($locInfo ));

        // dd($locInfo);

        $locArr = '';
        if (count(@$locInfo) > 0) {
            foreach ($locInfo as $key => $lArr) {
                if ($key <= 0) {
                    $city_name = $lArr->city;
                    $selected = '';
                    if ($lArr->country_id == 101) {
                        $city_name = $lArr->city_name;
                    }
                    $locArr = ucwords($lArr->address).' '.ucwords($city_name).' , '.ucwords($lArr->state_name).' , '.ucwords($lArr->country_name);
                }
            }
        } else {
            $locArr = ' ';
            $address_show = true;
        }

        $data = [
            'address' => @$locArr,
            'comapny_name' => @$clients->company_name,
            'logo' => @$clients->logo,
            'username' => @$companies->name,
            'useremail' => @$companies->email,
            'usercompany_id' => @$companies->company_id,
            'user_id' => @$companies->id,
            'job_title' => (@$exits_offer) ? @$exits_offer->designation : @$jobDetails->title,
            'full_name' => $rowDataOffer->full_name,
            'email' => @$rowDataOffer->email,
            'job_id' => @$rowDataOffer->job_id,
            'company_id' => @$rowDataOffer->company_id,
            'application_id' => @$rowDataOffer->application_id,
            'mobile' => @$rowDataOffer->phone,
            'send_accept_date_time' => (@$data_show->send_accept_date_time) ? date('d-M-Y', strtotime(@$data_show->send_accept_date_time)) : '',
            'of_joined_date' => (@$data_show->of_joined_date) ? date('d-M-Y', strtotime(@$data_show->of_joined_date)) : '',
        ];

        return (object) $data; // Rounds to 2 decimal places
    }

    public static function permissioncheck($permissions_val, $id)
    {

        $users = DB::table('users')->select('role_user.role_id');
        $users = $users->join('role_user', 'role_user.user_id', 'users.id');
        $users = $users->where('users.id', '=', $id)->first();

        $role_id = $users->role_id;

        $Rawlist = DB::table('role_has_permissions')->select('role_has_permissions.permission_id')->where('role_has_permissions.role_id', $role_id)->get()->toArray();
        $array = array_column($Rawlist, 'permission_id');
        $permission_role = json_decode(json_encode($array), true);
        $result = true;

        if (in_array($permissions_val, $permission_role)) {
            $result = false;
            // dd($permissions_val,$permission_role);
        }

        // dd($result);
        return $result; // Rounds to 2 decimal places
    }

    /**
     * Compute match % from pre-fetched job_skills array — no DB hit.
     * Used by all_apptracker blade after prepareRowMaps() pre-fetches skills in one batch.
     */
    public static function skill_percentage_from_array(array $job_skills, array $skillArr): string
    {
        $lowercaseArray1 = array_map(fn ($i) => strtolower(trim($i)), $job_skills);
        $lowercaseArray2 = array_map(fn ($i) => strtolower(trim($i)), $skillArr);

        $matches = 0;
        foreach ($lowercaseArray1 as $required) {
            foreach ($lowercaseArray2 as $candidate) {
                if (self::skillMatchesPhrasePrefix($required, $candidate)) {
                    $matches++;
                    break;
                }
            }
        }

        $total = count($lowercaseArray1);
        $pct = $total > 0 ? min(($matches / $total) * 100, 100) : 0;

        return round($pct, 1).'%';
    }

    public static function skill_percentage($jobId, $skillArr)
    {

        $job_skills = DB::table('job_metronics_skills')->where('job_metronics_skills.job_id', $jobId)->pluck('skill_name')->toArray();

        $lowercaseArray1 = array_map(function ($item) {
            return strtolower(trim($item));
        }, $job_skills);

        $lowercaseArray2 = array_map(function ($item) {
            return strtolower(trim($item));
        }, $skillArr);

        // in_array() required an EXACT string match between the job's
        // skill keyword ("java") and the candidate's own skill entry
        // ("java developer") — never equal, so a candidate whose skill
        // list only had compound entries scored 0% even when they
        // clearly had the skill. Same phrase-prefix check
        // buildSkillMatchClause()/matchedSkillsList() use elsewhere.
        $matches = 0;
        foreach ($lowercaseArray1 as $required) {
            foreach ($lowercaseArray2 as $candidate) {
                if (self::skillMatchesPhrasePrefix($required, $candidate)) {
                    $matches++;
                    break;
                }
            }
        }

        $totalElements = count($lowercaseArray1);
        $matchPercentage = $totalElements > 0 ? ($matches / $totalElements) * 100 : 0;

        // Ensure the percentage does not exceed 100%
        $matchPercentage = min($matchPercentage, 100);

        $percentage = round($matchPercentage, 1).'%';

        return $percentage; // Rounds to 2 decimal places

    }

    public static function skill_match_percentage($jobId, $skillArr)
    {

        $job_skills = DB::table('job_metronics_skills')->where('job_metronics_skills.job_id', $jobId)->pluck('skill_name')->toArray();

        $lowercaseArray1 = array_map(function ($item) {
            return strtolower(trim($item));
        }, $job_skills);

        $lowercaseArray2 = array_map(function ($item) {
            return strtolower(trim($item));
        }, $skillArr);

        // Same phrase-prefix fix as skill_percentage() above — in_array()
        // required an exact string match, so a candidate skill of "java
        // developer" never counted against a required "java".
        $matches = 0;
        foreach ($lowercaseArray1 as $required) {
            foreach ($lowercaseArray2 as $candidate) {
                if (self::skillMatchesPhrasePrefix($required, $candidate)) {
                    $matches++;
                    break;
                }
            }
        }

        $totalElements = count($lowercaseArray1);
        $matchPercentage = $totalElements > 0 ? ($matches / $totalElements) * 100 : 0;

        // Ensure the percentage does not exceed 100%
        $matchPercentage = min($matchPercentage, 100);

        $percentage = round($matchPercentage, 1);

        return $percentage; // Rounds to 2 decimal places

    }

    public static function profilesCompletion()
    {
        $company_id = auth()->user()->company_id;

        //  industries company_name
        // client_location commericial_countries
        $profilesCompletion = DB::table('companies')
            ->select('company_name', 'industries', 'description', 'company_type')
            ->where('companies.id', $company_id)->first();

        $clientsprofiles = DB::table('clients')
            ->join('companies', 'companies.id', '=', 'clients.company_id')
            ->join('client_location', 'client_location.client_id', '=', 'clients.id')
            ->select('clients.tags', 'client_location.address', 'client_location.country_id')
            ->where('clients.company_id', @$company_id)
            ->where('self_company', 1)
            ->first();

        $data = [
            'clientsprofiles' => $clientsprofiles,
            'profilesCompletion' => $profilesCompletion,
        ];

        return (object) $data; // Rounds to 2 decimal places

    }

    public static function currency_exchange_rates($fromCurrency, $toCurrency, $amount)
    {

        $rate = DB::table('currency_exchange_rates')->where('from_currency', $fromCurrency)
            ->where('to_currency', $toCurrency)
            ->first();
        if (! $rate) {
            return back()->with('error', 'Exchange rate not available for these currencies.');
        }
        $convertedAmount = $amount * $rate->rate;

        return $convertedAmount;

    }

    public static function countryCodeFun()
    {
        $company_id = auth()->user()->company_id;
        $client = Client::select('clients.id', 'clients.tags')->where('clients.company_id', '=', $company_id)->where('clients.self_company', '=', 1)->first();
        $client_location = DB::table('client_location')
            ->join('countries', 'countries.id', '=', 'client_location.country_id')
            ->select('countries.country_code', 'countries.country_name')
            ->where('client_location.client_id', @$client->id)
            ->first();
        $country_code = 'in';
        if ($client_location) {
            $country_code = strtolower($client_location->country_code);
        }

        return $country_code;

    }

    public static function defaultCurrency()
    {
        $company_id = auth()->user()->company_id;

        $defaultCurrency = DB::table('companies')
            ->join('master_currency', 'master_currency.id', '=', 'companies.currency')
            ->select('master_currency.code')
            ->where('companies.id', $company_id)
            ->first();

        // dd($defaultCurrency );

        $currency_code = 'INR';
        if ($defaultCurrency) {
            $currency_code = ($defaultCurrency->code);
        }

        return $currency_code;

    }

    public static function defaultTimeZone()
    {
        $company_id = auth()->user()->company_id;

        $defaultTimeZone = DB::table('companies')
            ->select('companies.timezone')
            ->where('companies.id', $company_id)
            ->first();

        // dd($defaultTimeZone );

        $timezone = 'Asia/Kolkata';
        if ($defaultTimeZone) {
            $timezone = ($defaultTimeZone->timezone);
        }

        return $timezone;

    }

    public static function defaultTimeFormat($companyId = null)
    {
        $company_id = $companyId ?: (auth()->user()?->company_id ?? null);
        if (! $company_id) {
            return 1;
        }

        $defaultTimeFormat = DB::table('companies')
            ->select('companies.time_format')
            ->where('companies.id', $company_id)
            ->first();

        // dd($defaultTimeFormat );

        $timeformat = 1;
        if ($defaultTimeFormat) {
            $timeformat = ($defaultTimeFormat->time_format);
        }

        return $timeformat;

    }

    public static function separateNumberParts($value)
    {
        // Ensure value is treated as a string
        $value = (string) $value;

        // Case: Starts with "." → intPart = null, decimalPart = after "."
        if (strpos($value, '.') === 0) {
            return [null, substr($value, 1)];
        }
        // Case: Ends with "." → intPart = before ".", decimalPart = null
        elseif (strpos($value, '.') !== false && substr($value, -1) === '.') {
            return [rtrim($value, '.'), null];
        }
        // Case: Contains both int and decimal → Split normally
        elseif (strpos($value, '.') !== false) {
            return explode('.', $value) + [null, null];
        }
        // Case: Whole number → intPart = value, decimalPart = null
        else {
            return [$value, null];
        }
    }

    public static function statusCountAll($jobID)
    {

        $elasticService = app(ElasticService::class);
        $companyId = auth()->user()->company_id;

        $boolQuery = [
            'bool' => [
                'should' => [
                    // First condition:
                    [
                        'bool' => [
                            'must' => [
                                ['term' => ['jobId' => $jobID]],
                                ['term' => ['company_id' => $companyId]],
                            ],
                            'must_not' => [
                                ['range' => ['ClientComID' => ['gt' => 0]]],
                            ],
                        ],
                    ],

                    // Second condition:
                    [
                        'bool' => [
                            'must' => [
                                ['term' => ['jobId' => $jobID]],
                                ['term' => ['company_id' => $companyId]],
                                ['range' => ['ClientComID' => ['gt' => 0]]],
                            ],
                            'must_not' => [
                                ['terms' => ['job_master_status_id' => [8, 9]]],
                            ],
                        ],
                    ],
                ],
                'minimum_should_match' => 1,
                'filter' => [
                    // ['term' => ['job_id' => $jobID]]
                ],
            ],
        ];

        $params = [
            'index' => ELASTIC_CANDIDATES,
            'body' => [
                'query' => $boolQuery,
            ],
        ];

        // Count query
        $response = $elasticService->getClient()->count($params);
        $totalCount = $response['count'] ?? 0;

        return ($totalCount) ? $totalCount : 0;

    }

    public static function statusCountAllInRe($jobID)
    {

        $elasticService = app(ElasticService::class);

        $companyId = auth()->user()->company_id;

        // $boolQuery = [
        // 	'bool' => [
        // 		'should' => [
        // 			// First condition:
        // 			[
        // 				'bool' => [
        // 					'must' => [
        // 						['term' => ['jobId' => $jobID]],
        // 						['term' => ['company_id' => $companyId]],
        // 						['term' => ['job_master_status_id' => 8]]
        // 					],
        // 					'must_not' => [
        // 						['range' => ['ClientComID' => ['gt' => 0]]]
        // 					]
        // 				],
        // 			],

        // 			// Second condition:
        // 			[
        // 				'bool' => [
        // 					'must' => [
        // 						['term' => ['jobId' => $jobID]],
        // 						['term' => ['company_id' => $companyId]],
        // 						['range' => ['ClientComID' => ['gt' => 0]]],
        // 					],
        // 					'must_not' => [
        // 						['term' => ['job_master_status_id' => 8]]
        // 					]
        // 				],
        // 			],
        // 		],
        // 		'minimum_should_match' => 1,
        // 	]
        // ];
        $boolQuery = [
            'bool' => [
                'must' => [
                    ['term' => ['jobId' => $jobID]],
                    ['term' => ['company_id' => $companyId]],
                    ['term' => ['job_master_status_id' => 8]],
                ],
                'must_not' => [
                    ['range' => ['ClientComID' => ['gt' => 0]]],
                ],
            ],
        ];

        $params = [
            'index' => ELASTIC_CANDIDATES,
            'body' => [
                'query' => $boolQuery,
            ],
        ];

        // Count query
        $response = $elasticService->getClient()->count($params);
        $totalCount = $response['count'] ?? 0;

        return ($totalCount) ? $totalCount : 0;

    }

    public static function companyDetailsGet($company_id)
    {

        $elasticService = app(ElasticService::class);

        $params = [
            'index' => ELASTIC_COMPANIES_INDEX,
            'id' => $company_id,
        ];

        $response = $elasticService->getClient()->get($params);

        $company_details = isset($response['_source']) ? (object) $response['_source'] : null;

        return ($company_details) ? $company_details : null;

    }

    public static function statusCountThirdpartyAll($companyId, $jobID)
    {

        $elasticService = app(ElasticService::class);

        $boolQuery = [
            'bool' => [
                'should' => [
                    // First condition:
                    [
                        'bool' => [
                            'must' => [
                                ['term' => ['ClientComID' => $companyId]],
                            ],
                        ],
                    ],
                ],
                'minimum_should_match' => 1,
                'filter' => [
                    ['term' => ['jobId' => $jobID]],
                ],
            ],
        ];

        $params = [
            'index' => ELASTIC_CANDIDATES,
            'body' => [
                'query' => $boolQuery,
            ],
        ];

        // Count query
        $response = $elasticService->getClient()->count($params);
        $totalCount = $response['count'] ?? 0;

        return ($totalCount) ? $totalCount : 0;

    }

    public static function statusCountThirdparty($companyId, $jobID, $status)
    {

        $elasticService = app(ElasticService::class);
        if ($status == 7) {
            $status = [7, 9];   // array required
        } else {
            $status = [(int) $status];
        }
        $boolQuery = [
            'bool' => [
                'should' => [
                    // First condition:
                    [
                        'bool' => [
                            'must' => [
                                ['term' => ['ClientComID' => $companyId]],
                                ['terms' => ['job_master_status_id' => $status]],
                            ],
                        ],
                    ],
                ],
                'minimum_should_match' => 1,
                'filter' => [
                    ['term' => ['jobId' => $jobID]],
                ],
            ],
        ];

        $params = [
            'index' => ELASTIC_CANDIDATES,
            'body' => [
                'query' => $boolQuery,
            ],
        ];

        // Count query
        $response = $elasticService->getClient()->count($params);
        $totalCount = $response['count'] ?? 0;

        return ($totalCount) ? $totalCount : 0;

    }

    public static function statusCountThirdpartyInterview($companyId, $jobID)
    {

        $elasticService = app(ElasticService::class);
        // company_id
        $boolQuery = [
            'bool' => [
                'should' => [
                    // First condition:
                    [
                        'bool' => [
                            'must' => [
                                ['term' => ['ClientComID' => $companyId]],
                            ],
                        ],
                    ],
                ],
                'minimum_should_match' => 1,
                'filter' => [
                    ['term' => ['jobId' => $jobID]],
                ],
            ],
        ];

        $params = [
            'index' => ELASTIC_INERTVIEW_TRACKER,
            'body' => [
                'query' => $boolQuery,
            ],
        ];

        // Count query
        $response = $elasticService->getClient()->count($params);
        $totalCount = $response['count'] ?? 0;

        return ($totalCount) ? $totalCount : 0;

    }

    public static function statusCountInterview($companyId, $jobID)
    {

        $elasticService = app(ElasticService::class);

        $boolQuery = [
            'bool' => [
                'should' => [
                    // First condition:
                    [
                        'bool' => [
                            'must' => [
                                ['term' => ['company_id' => $companyId]],
                            ],
                        ],
                    ],
                ],
                'minimum_should_match' => 1,
                'filter' => [
                    ['term' => ['jobId' => $jobID]],
                ],
            ],
        ];

        $params = [
            'index' => ELASTIC_INERTVIEW_TRACKER,
            'body' => [
                'query' => $boolQuery,
            ],
        ];

        // Count query
        $response = $elasticService->getClient()->count($params);
        $totalCount = $response['count'] ?? 0;

        return ($totalCount) ? $totalCount : 0;

    }

    public static function statusCountAllThirdpartyInRe($companyId, $jobID)
    {

        $elasticService = app(ElasticService::class);

        $boolQuery = [
            'bool' => [
                'should' => [
                    // First condition:
                    [
                        'bool' => [
                            'must' => [
                                ['term' => ['ClientComID' => $companyId]],
                                ['term' => ['job_master_status_id' => 8]],
                            ],
                        ],
                    ],
                ],
                'minimum_should_match' => 1,
                'filter' => [
                    ['term' => ['jobId' => $jobID]],
                ],
            ],
        ];

        $params = [
            'index' => ELASTIC_CANDIDATES,
            'body' => [
                'query' => $boolQuery,
            ],
        ];

        // Count query
        $response = $elasticService->getClient()->count($params);
        $totalCount = $response['count'] ?? 0;

        return ($totalCount) ? $totalCount : 0;

    }

    public static function statusCount($companyId, $jobID, $status)
    {

        $elasticService = app(ElasticService::class);

        // $params = [
        // 		'index' => ELASTIC_CANDIDATES,
        // 		'id'    => 1434,
        // ];

        // $response = $elasticService->getClient()->get($params);
        // $team_details = (object) $response['_source'];

        // dd($team_details);

        $boolQuery = [
            'bool' => [
                'should' => [

                    [
                        'bool' => [
                            'must' => [
                                ['term' => ['jobId' => $jobID]],
                                ['term' => ['company_id' => $companyId]],
                                ['term' => ['job_master_status_id' => $status]],
                            ],
                        ],
                    ],

                    [
                        'bool' => [
                            'must' => [
                                ['term' => ['jobId' => $jobID]],
                                ['term' => ['company_id' => $companyId]],
                                ['term' => ['job_master_status_id' => $status]],
                                ['range' => ['ClientComID' => ['gt' => 0]]],
                            ],
                        ],
                    ],

                ],

                'minimum_should_match' => 1,

                'must_not' => [
                    ['terms' => ['job_master_status_id' => [8, 9]]],
                ],
            ],
        ];

        $params = [
            'index' => ELASTIC_CANDIDATES,
            'body' => [
                'query' => $boolQuery,
            ],
        ];

        // Count query
        $response = $elasticService->getClient()->count($params);
        $totalCount = $response['count'] ?? 0;

        return ($totalCount) ? $totalCount : 0;

    }

    public static function allStatusCount($companyId, $jobID)
    {

        // $elasticService = app(ElasticService::class);

        // $boolQuery = [
        // 	'bool' => [
        // 		'should' => [
        // 			// First condition:
        // 			[
        // 				'bool' => [
        // 					'must' => [
        // 						['term' => ['company_id' => $companyId]],
        // 						['term' => ['jobId' => $jobID]],
        // 						['term' => ['ClientComID' => 0]],
        // 					],
        // 				],
        // 			],

        // 			// Second condition:
        // 			[
        // 				'bool' => [
        // 					'must' => [
        // 						['term' => ['company_id' => $companyId]],
        // 						['term' => ['jobId' => $jobID]],
        // 						['range' => ['ClientComID' => ['gt' => 0]]],
        // 						['range' => ['job_id' => ['gt' => 0]]],
        // 						[
        // 							'bool' => [
        // 								'must_not' => [
        // 									['term' => ['job_master_status_id' => 8]],
        // 								],
        // 							],
        // 						],
        // 					],
        // 				],
        // 			],

        // 			// Third condition:
        // 			[
        // 				'bool' => [
        // 					'must' => [
        // 						['term' => ['ClientComID' => $companyId]],
        // 						['term' => ['jobId' => $jobID]],
        // 						['range' => ['job_id' => ['gt' => 0]]],
        // 					],
        // 				],
        // 			],
        // 		],
        // 		'minimum_should_match' => 1,
        // 		'filter' => [
        // 			// Example filter, add your own as needed
        // 			// ['term' => ['status' => 'active']],
        // 		],
        // 	]
        // ];

        // $params = [
        // 	'index' => ELASTIC_CANDIDATES,
        // 	'body' => [
        // 		'query' => $boolQuery
        // 	]
        // ];

        // $response = $elasticService->getClient()->count($params);
        // $totalCount = $response['count'] ?? 0;

        // return ($totalCount) ? $totalCount : 0;

        $count = DB::table('add_job_wise_condidates')->where('company_id', $companyId)->where('job_id', $jobID)->count();

        return ($count) ? $count : 0;

    }

    public static function allStatusCountThirdparty($companyId, $jobID)
    {

        $count = DB::table('add_job_wise_condidates')->where('thirdparty_company_id', $companyId)->where('job_id', $jobID)->count();

        return ($count) ? $count : 0;

    }

    public static function statusCountStaffingPartner($companyId, $jobID, $status)
    {

        $elasticService = app(ElasticService::class);
        $boolQuery = [
            'bool' => [
                'should' => [
                    // First condition:
                    [
                        'bool' => [
                            'must' => [
                                ['term' => ['jobId' => $jobID]],
                                ['term' => ['ClientComID' => $companyId]],
                                ['term' => ['job_master_status_id' => $status]],
                            ],
                        ],
                    ],
                ],
                'minimum_should_match' => 1,
                'filter' => [],
            ],
        ];

        $params = [
            'index' => ELASTIC_CANDIDATES,
            'body' => [
                'query' => $boolQuery,
            ],
        ];

        // Count query
        $response = $elasticService->getClient()->count($params);
        $totalCount = $response['count'] ?? 0;

        return ($totalCount) ? $totalCount : 0;

        $count = DB::table('add_job_wise_condidates')->where('thirdparty_company_id', '>', 0)->where('company_id', $companyId)->where('job_id', $jobID)->where('master_status_id', $status)->count();

        return ($count) ? $count : 0;

    }

    public static function allStatusCountStaffingPartner($companyId, $jobID)
    {

        $elasticService = app(ElasticService::class);
        $boolQuery = [
            'bool' => [
                'must' => [
                    ['term' => ['jobId' => $jobID]],
                    ['term' => ['ClientComID' => $companyId]],
                ],
                'must_not' => [
                    ['terms' => ['job_master_status_id' => [8, 9]]],
                ],
            ],
        ];

        $params = [
            'index' => ELASTIC_CANDIDATES,
            'body' => [
                'query' => $boolQuery,
            ],
        ];

        // Count query
        $response = $elasticService->getClient()->count($params);
        $totalCount = $response['count'] ?? 0;

        return ($totalCount) ? $totalCount : 0;

        $count = DB::table('add_job_wise_condidates')->where('thirdparty_company_id', '>', 0)->where('company_id', $companyId)->where('job_id', $jobID)->where('master_status_id', '!=', 8)->count();

        return ($count) ? $count : 0;

    }

    public static function teamActiveJobsCount($id)
    {
        $response = '';
        $elasticService = app(ElasticService::class);
        $must[] = [
            'bool' => [
                'should' => [
                    ['term' => ['status' => 'active']],
                ],
                'minimum_should_match' => 1,
            ],
        ];

        $must[] = [
            'nested' => [
                'path' => 'job_assign',
                'query' => [
                    'term' => ['job_assign.user_id' => $id],
                ],
            ],
        ];
        // FINAL ELASTIC PARAMS
        $params = [
            'index' => ELASTIC_JOBS,
            'body' => [
                'query' => [
                    'bool' => [
                        'must' => $must,
                        'filter' => [],
                    ],
                ],
            ],
        ];
        $response = $elasticService->getClient()->count($params);

        return $response['count'] ?? 0;

        // $boolQuery = [
        // 	'bool' => [
        // 		'should' => [
        // 			// First condition:
        // 			[
        // 				'bool' => [
        // 					'must' => [
        // 						['term' => ['company_id' => $companyId]],
        // 						['term' => ['jobId' => $jobID]],
        // 						['term' => ['ClientComID' => 0]],
        // 					],
        // 				],
        // 			],

        // 			// Second condition:
        // 			[
        // 				'bool' => [
        // 					'must' => [
        // 						['term' => ['company_id' => $companyId]],
        // 						['term' => ['jobId' => $jobID]],
        // 						['range' => ['ClientComID' => ['gt' => 0]]],
        // 						['range' => ['job_id' => ['gt' => 0]]],
        // 						[
        // 							'bool' => [
        // 								'must_not' => [
        // 									['term' => ['job_master_status_id' => 8]],
        // 								],
        // 							],
        // 						],
        // 					],
        // 				],
        // 			],

        // 			// Third condition:
        // 			[
        // 				'bool' => [
        // 					'must' => [
        // 						['term' => ['ClientComID' => $companyId]],
        // 						['term' => ['jobId' => $jobID]],
        // 						['range' => ['job_id' => ['gt' => 0]]],
        // 					],
        // 				],
        // 			],
        // 		],
        // 		'minimum_should_match' => 1,
        // 		'filter' => [
        // 			// Example filter, add your own as needed
        // 			// ['term' => ['status' => 'active']],
        // 		],
        // 	]
        // ];

        // $params = [
        // 	'index' => ELASTIC_CANDIDATES,
        // 	'body' => [
        // 		'query' => $boolQuery
        // 	]
        // ];

        // $response = $elasticService->getClient()->count($params);
        // $totalCount = $response['count'] ?? 0;

        // return ($totalCount) ? $totalCount : 0;
    }

    public static function JobApplicationsAddEditUpload($getId)
    {
        $response = '';
        $elasticService = app(ElasticService::class);

        // $add_job_wise_condidates = DB::table('add_job_wise_condidates')->select('id', 'job_id')->orderByDesc(column: 'created_at')->limit(50)->get();

        $add_job_wise_condidates = DB::table('add_job_wise_condidates')
            ->where('id', '=', $getId)
            ->select('add_job_wise_condidates.id', 'add_job_wise_condidates.job_id')
            ->get();

        foreach ($add_job_wise_condidates as $add_job_wise) {

            $id = $add_job_wise->id;
            $jobId = $add_job_wise->job_id;
            $applications = JobApplication::withoutGlobalScopes()->select(
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
                'job_applications.gender',
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
                'job_applications.company_id as other_company',
                'jobs.title',
                'jobs.client_id',
                'jobs.employment_type',
                'job_applications.outsourcing_type',
                'job_applications.pay_rate_currency',
                'job_applications.pay_rate_amount',
                'job_applications.pay_rate_mode',
                'job_applications.careersummary',
                'job_applications.nationality',
                'job_applications.wm',
                'job_applications.employmentType',
                'job_applications.oh',
                'job_applications.visastatu',
                'job_applications.referencename',
                'job_applications.recruiter_note',
                'job_applications.reason_for_job_change',
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
                })
                ->leftjoin('clients', 'clients.id', 'jobs.client_id')
                ->leftjoin('job_locations', 'job_locations.id', 'jobs.location_id')
                ->leftjoin('users', 'users.id', 'add_job_wise_condidates.submitted_by')
                ->leftjoin('documents', 'documents.documentable_id', 'job_applications.id')
                ->leftjoin('qualification_master', 'qualification_master.id', 'job_applications.qualification')
                ->leftJoin('countries', 'countries.id', '=', 'job_applications.country')
                ->leftJoin('state_list', 'state_list.id', '=', 'job_applications.state')
                ->leftJoin('city_list', 'city_list.city_id', '=', 'job_applications.city_id')
                ->where('add_job_wise_condidates.id', $id)
                ->orderByDesc('documents.id')->get();

            // dd($applications);

            $bulkData = [];

            foreach ($applications as $application) {
                $wiseId = $application->wiseId;

                $client_name_com = '';
                $client_name = @$application->client_name;
                if (@$application->client_comapny_id != auth()->user()->company_id) {
                    $client_name = @$application->cur_company_name;
                    $client_name = ucwords(substr(@$client_name, 0, 30));

                }

                $client_name_com = $client_name;
                //  dd($application->wiseId,$application->jobId);
                $params = [
                    'index' => ELASTIC_INERTVIEW_TRACKER,
                    'body' => [
                        'size' => 1,
                        'query' => [
                            'bool' => [
                                'must' => [
                                    ['term' => ['interview_job_wise_con_id' => @$application->wiseId]],
                                    ['term' => ['interview_job_id' => @$application->jobId]],
                                ],
                            ],
                        ],
                        'sort' => [
                            ['updated_at' => ['order' => 'desc']],
                        ],
                    ],
                ];
                $result = $elasticService->getClient()->search($params);
                $intRow = isset($result['hits']['hits'][0]) ? (object) $result['hits']['hits'][0]['_source'] : null;

                // $jobSkill = DB::table('job_metronics_skills')
                // ->leftJoin('candidate_job_skill_exp', function($join) use($application) {
                // 	$join->on('candidate_job_skill_exp.job_skill_id', '=', 'job_metronics_skills.id')
                // 		->where('candidate_job_skill_exp.candidate_id', '=', $application->id); // Add the candidate condition
                // })
                // ->select('job_metronics_skills.*','candidate_job_skill_exp.skill_value')
                // ->where('job_metronics_skills.job_id', '=', $application->jobId)
                // ->get();

                // // dd($jobSkill);

                // if (count($jobSkill)>0) {
                //     foreach ($jobSkill as $sval) {

                //          $skillValue = str_replace(['Years','Year',' '],'',$sval->skill_value);
                //                     // dd($skillValue);
                //          if(!is_null($skillValue) && !empty($skillValue) && $skillValue !='0.'){
                //             $values = array(
                //                 'job_application_id' => $application->id,
                //                 'skills' => $sval->skill_name
                //             );
                //             if(!DB::table('job_application_skills')->where('job_application_id','=',$application->id)->where('skills','LIKE', '%'.$sval->skill_name.'%')->exists()){
                //                 DB::table('job_application_skills')->insert($values);
                //             }
                //         }

                //     }
                // }

                $skills = DB::table('job_application_skills')
                    ->where('job_application_id', $application->id)
                    ->get();

                $job_application_skills = $skills->map(function ($s) {
                    return [
                        'id' => $s->id,
                        'job_application_id' => $s->job_application_id,   // ← FIXED (you had wrong value)
                        'skills' => strtolower($s->skills),
                    ];
                })->toArray();

                $application_skills = $skills
                    ->pluck('skills')
                    ->map(fn ($skill) => strtolower($skill))
                    ->implode(',');

                $params = [
                    'index' => ELASTIC_CANDIDATES,
                    'size' => 1,
                    'body' => [
                        'query' => [
                            'bool' => [
                                'filter' => [
                                    [
                                        'term' => [
                                            'jobId' => (int) $application->jobId,
                                        ],
                                    ],
                                    [
                                        'term' => [
                                            'candidate_id' => (int) $application->id,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ];

                $response = $elasticService->getClient()->search($params);

                $exists = ! empty($response['hits']['hits']);

                $duplicate = (int) ($application->duplicate ?? 0);

                // Intent: Evaluate candidate duplicate status in static method context without $this or undefined variables.
                // TENANCY NOTE: Scope company duplicate checks by application company_id.
                $companyId = $application->company_id ?: ($application->other_company ?: (auth()->check() ? auth()->user()->company_id : null));
                if ($companyId) {
                    $company = DB::table('companies')->where('id', $companyId)->first();
                    $appDuplicate = $company->app_duplicate ?? 0;
                    $appDuplicateDays = $company->app_duplicate_days ?? 0;

                    if ($appDuplicate == 1 || $appDuplicate == 2) {
                        $applicationEmail = $application->email ?? '';
                        $applicationPhone = $application->phone ?? '';
                        $targetJobId = $application->jobId ?? ($application->job_id ?? null);

                        if (! empty($applicationEmail) || ! empty($applicationPhone)) {
                            $returnCount = self::checkDuplicateCandidate(
                                $companyId,
                                $appDuplicate,
                                $targetJobId,
                                $applicationEmail,
                                $appDuplicateDays,
                                $applicationPhone
                            );

                            if ($returnCount > 0 || ($application->duplicate ?? 0) == 1) {
                                $duplicate = 0;
                            }
                        }
                    }
                }
                //    dd($duplicate);
                // if (($application->duplicate ?? 0) == 1) {
                //     $duplicate = 1;
                // }

                try {
                    $response = $elasticService->getClient()->update([
                        'index' => ELASTIC_CANDIDATES,
                        'id' => $wiseId,
                        'retry_on_conflict' => 5,
                        'body' => [
                            'doc' => [
                                'id' => $wiseId,
                                'candidate_id' => $application->id,
                                'thirdparty_company_id' => $application->thirdparty_company_id,
                                'full_name' => strtolower($application->full_name),
                                'email' => $application->email,
                                'phone' => $application->phone,
                                'gender' => $application->gender,
                                'np_id' => $application->np_id,
                                'immediate_join' => $application->np_id,
                                'serving_notice_period_mode' => $application->serving_notice_period_mode,
                                'country_name' => strtolower($application->country_name),
                                'state_name' => strtolower($application->state_name),
                                'city_name' => strtolower($application->city_name),
                                'city_id' => $application->city_id,
                                'skills' => $application_skills,
                                'photo' => $application->photo,
                                'job_id' => $application->job_id,
                                'jobId' => $application->jobId,
                                'careersummary' => $application->careersummary,
                                'nationality' => $application->nationality,
                                'wm' => $application->wm,
                                'employmentType' => $application->employmentType,
                                'oh' => $application->oh,
                                'visastatu' => $application->visastatu,
                                'referencename' => $application->referencename,
                                'recruiter_note' => $application->recruiter_note,
                                'reason_for_job_change' => $application->reason_for_job_change,
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
                                'duplicate' => (int) ($duplicate ?? $application->duplicate ?? 0),
                                'is_duplicate' => (bool) (($duplicate ?? $application->duplicate ?? 0) == 1),
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
                                'resume' => $application->resume,
                                'rating' => $application->rating,
                                'skype_id' => $application->skype_id,
                                'cover_letter' => $application->cover_letter,
                                'passport' => $application->passport,
                                'updated_at' => ($application->updated_at) ? Carbon::parse($application->updated_at)->toIso8601String() : null,
                                'created_at' => ($application->created_at) ? Carbon::parse($application->created_at)->toIso8601String() : null,
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
                            ],
                            'doc_as_upsert' => true,
                            'detect_noop' => false,
                        ],
                    ]);

                    self::addCompanyCandidate($application->company_id, $wiseId);

                    $resultStatus = $response['result'] ?? '';
                    $hasHits = ! empty($response['_id']);
                    $success = in_array($resultStatus, ['updated', 'created', 'noop']) || $hasHits;
                } catch (\Exception $e) {
                    Log::error('JobApplicationsAddEditUpload ES sync failed', [
                        'wise_id' => $wiseId,
                        'error' => $e->getMessage(),
                    ]);
                    $success = false;
                }
            }
        }

        return $success ?? true;
    }

    public static function elasticBenchCandidateAddEditIndex($id)
    {
        $elasticService = app(ElasticService::class);
        $response = '';

        $applications = JobApplication::withoutGlobalScopes()->select(
            'job_applications.id',
            'job_applications.thirdparty_company_id',
            'job_applications.outsourcing_type',
            'job_applications.full_name',
            'job_applications.company_id',
            'job_applications.gender',
            'job_applications.email',
            'job_applications.careersummary',
            'job_applications.nationality',
            'job_applications.wm',
            'job_applications.employmentType',
            'job_applications.oh',
            'job_applications.visastatu',
            'job_applications.referencename',
            'job_applications.recruiter_note',
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
            'job_applications.linkedin',
            'job_applications.github',
            'job_applications.behance',
            'job_applications.otherLink',
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
            ->where('job_applications.id', $id)
            ->get();
        //   dd($applications);
        foreach ($applications as $application) {

            $add_job_wise_condidates = DB::table('add_job_wise_condidates')
                ->where('job_application_id', $application->id)
                ->get()
                ->map(function ($s) {
                    return [
                        'id' => $s->id,
                        'job_application_id' => $s->job_application_id,
                        'job_id' => $s->job_id,
                        'status_id' => $s->status_id,
                        'master_status_id' => $s->master_status_id,
                        'child_status_id' => $s->child_status_id,
                    ];
                })
                ->toArray();

            $job_application_skills = DB::table('job_application_skills')
                ->where('job_application_id', $application->id)
                ->get()
            // ->map(fn($row) => (array) $row)
                ->map(function ($s) {
                    return [
                        'id' => $s->id,
                        'job_application_id' => $s->id,
                        'skills' => strtolower($s->skills),
                    ];
                })
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

            $response = $elasticService->getClient()->update([
                'index' => ELASTIC_CANDIDATES_BENCH,
                'id' => $application->id,
                'retry_on_conflict' => 5,
                'body' => [
                    'doc' => [
                        'id' => $application->id,
                        'thirdparty_company_id' => $application->thirdparty_company_id,
                        'full_name' => $application->full_name,
                        'company_id' => $application->company_id,
                        'comission_type' => $application->comission_type,
                        'outsourcing_type' => $application->outsourcing_type,
                        'email' => $application->email,
                        'gender' => $application->gender,
                        'pay_rate_currency' => $application->pay_rate_currency,
                        'pay_rate_amount' => $application->pay_rate_amount,
                        'pay_rate_mode' => $application->pay_rate_mode,
                        'phone' => $application->phone,
                        'np_id' => $application->np_id,
                        'immediate_join' => $application->np_id,
                        'serving_notice_period_mode' => $application->serving_notice_period_mode,
                        'state' => $application->state,
                        'city' => $application->city,
                        'careersummary' => $application->careersummary,
                        'nationality' => $application->nationality,
                        'wm' => $application->wm,
                        'employmentType' => $application->employmentType,
                        'oh' => $application->oh,
                        'visastatu' => $application->visastatu,
                        'referencename' => $application->referencename,
                        'recruiter_note' => $application->recruiter_note,
                        'submitted_by' => $application->submitted_by,
                        'country_name' => $application->country_name,
                        'country' => $application->country,
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
                        'linkedin' => $application->linkedin,
                        'github' => $application->github,
                        'behance' => $application->behance,
                        'otherLink' => $application->otherLink,
                    ],
                    'doc_as_upsert' => true,
                    'detect_noop' => false,
                ],
            ]);

            self::addCompanyBenchCandidate($application->company_id, $application->id);
        }

        // dd($response);
        return $response;

    }

    public static function elasticJobsAddEditUpdateIndex($id)
    {
        $elasticService = app(ElasticService::class);
        $response = '';
        $jobs = DB::table('jobs')
            ->select('jobs.*', 'clients.name as clients_name', 'clients.logo_logical_path', 'clients.self_company', 'clients.company_id as client_company_id', 'companies.company_name', 'companies.logo')
            ->leftJoin('clients', 'clients.id', 'jobs.client_id')
            ->join('companies', 'companies.id', 'jobs.company_id')
            ->where('jobs.id', $id)
            ->get();

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

            $client_contact_info_jobs = DB::table('client_contact_info_jobs')
                ->join('client_contact_info', 'client_contact_info.id', '=', 'client_contact_info_jobs.client_contact_info_id')
                ->select('client_contact_info.contact_person_name', 'client_contact_info.contact_person_email', 'client_contact_info.contact_person_number', 'client_contact_info.contact_person_designation', 'client_contact_info_jobs.*')
                ->where('client_contact_info_jobs.job_id', $row->id)
                ->get()
                ->map(function ($s) {
                    return [
                        'id' => (int) $s->id,
                        'company_id' => (int) $s->company_id,
                        'job_id' => (int) $s->job_id,
                        'client_contact_info_id' => (int) $s->client_contact_info_id,
                        'contact_person_name' => strtolower($s->contact_person_name),
                        'contact_person_email' => ($s->contact_person_email),
                        'contact_person_number' => ($s->contact_person_number),
                        'contact_person_designation' => ($s->contact_person_designation),
                    ];
                })
                ->toArray();

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

            $response = $elasticService->getClient()->update([
                'index' => ELASTIC_JOBS,
                'id' => $row->id,
                'retry_on_conflict' => 5,
                'body' => [
                    'doc' => [
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
                    ],
                    'doc_as_upsert' => true,
                    'detect_noop' => false,
                ],
            ]);

            self::addUpdateCompanyJob($row->company_id, $row->id);

        }

        return $response;

    }

    public static function interviewTrackerAddEditUpload($id)
    {
        $elasticService = app(ElasticService::class);
        $response = '';
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
                'add_job_wise_condidates.rec_candidate_assign_id',
                'add_job_wise_condidates.id as wiseID',
                'add_job_wise_condidates.thirdparty_company_id as ClientComID',
                'add_job_wise_condidates.job_id as jobId',
                'add_job_wise_condidates.submitted_by',
                'uc.company_name as uc_company_name',
                'updatetimeline.interview_mention_link'
            )->where('interview_schedules.id', '=', $id)
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

            $exists = $elasticService->getClient()->exists([
                'index' => ELASTIC_CANDIDATES,
                'id' => $row->wiseID,
            ]);
            if ($exists) {
                $params = [
                    'index' => ELASTIC_CANDIDATES,
                    'id' => $row->wiseID,
                ];

                $response = $elasticService->getClient()->get($params);
                $data = (object) $response['_source'];
                $row->full_name = $data->full_name;
                $row->email = $data->email;
                $row->phone = $data->phone;
                $row->current_designation = $data->current_designation;
                $row->condidate_type = $data->condidate_type;
            }

            $response = $elasticService->getClient()->update([
                'index' => ELASTIC_INERTVIEW_TRACKER,
                'id' => $row->id,
                'retry_on_conflict' => 5,
                'body' => [
                    'doc' => [
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
                        'rec_candidate_assign_id' => $row->rec_candidate_assign_id,
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
                        'has_feedback' => ! empty($row->feedback) ? 1 : 0,
                        'client_title_name' => $row->client_name ?: $row->company_name,
                        'recommendation_relation' => $recommendation_relation ?? [],
                        'job_assign' => $job_assign ?? [],
                    ],
                    'doc_as_upsert' => true,
                    'detect_noop' => false,
                ],
            ]);

        }

        return $response;
    }

    public static function clientStaffingPartner($id)
    {
        $elasticService = app(ElasticService::class);
        $response = '';
        $clients = DB::table('companies')
            ->select(
                'companies.id',
                'companies.company_name',
                'companies.industries',
                'companies.logo',
                'clients.company_type',
                'companies.industries_type',
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
                'clients.company_size_from',
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
            ->where('clients.id', '=', $id)
            ->groupBy('clients.id')
            ->get();
        // ->cursor();
        // dd($clients);

        foreach ($clients as $row) {

            // dd($row);

            // $staffing_company_relation =  DB::table('staffing_company_relation')
            // ->where('staffing_company_relation.from_company_id', $row->id)
            // ->get()
            // ->map(function ($s){
            // 	return [
            // 	'id' => (int) $s->id,
            // 	'from_company_id' => (int) $s->from_company_id,
            // 	'from_client_id' => (int) $s->from_client_id,
            // 	'from_user_id' => (int) $s->from_user_id,
            // 	'from_status' => (int) $s->from_status,
            // 	'from_created_at' => ($s->from_created_at) ? Carbon::parse($s->from_created_at)->toIso8601String() : null,
            // 	'to_company_id' => (int) $s->to_company_id,
            // 	'to_client_id' => (int) $s->to_client_id,
            // 	'to_user_id' => (int) $s->to_user_id,
            // 	'to_status' => (int) $s->to_status,
            // 	'to_created_at' =>  ($s->to_created_at) ? Carbon::parse($s->to_created_at)->toIso8601String() : null,
            // 	'final_status' => (int) $s->final_status,
            // 	'created_at' =>  ($s->created_at) ? Carbon::parse($s->created_at)->toIso8601String() : null,
            // 	'updated_at' =>  ($s->updated_at) ? Carbon::parse($s->updated_at)->toIso8601String() : null
            // 	];
            //  })
            // ->toArray();
            // $staffing_company =  DB::table('staffing_company_relation')
            // ->where('staffing_company_relation.to_company_id', $row->id)
            // ->get()
            // ->map(function ($s){
            // 	return [
            // 	'id' => (int) $s->id,
            // 	'from_company_id' => (int) $s->from_company_id,
            // 	'from_client_id' => (int) $s->from_client_id,
            // 	'from_user_id' => (int) $s->from_user_id,
            // 	'from_status' => (int) $s->from_status,
            // 	'from_created_at' => ($s->from_created_at) ? Carbon::parse($s->from_created_at)->toIso8601String() : null,
            // 	'to_company_id' => (int) $s->to_company_id,
            // 	'to_client_id' => (int) $s->to_client_id,
            // 	'to_user_id' => (int) $s->to_user_id,
            // 	'to_status' => (int) $s->to_status,
            // 	'to_created_at' =>  ($s->to_created_at) ? Carbon::parse($s->to_created_at)->toIso8601String() : null,
            // 	'final_status' => (int) $s->final_status,
            // 	'created_at' =>  ($s->created_at) ? Carbon::parse($s->created_at)->toIso8601String() : null,
            // 	'updated_at' =>  ($s->updated_at) ? Carbon::parse($s->updated_at)->toIso8601String() : null
            // 	];
            //  })
            // ->toArray();

            $staffing_company_relation = [];
            $staffing_company = [];

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

            $candidate_relation = [];

            $response = $elasticService->getClient()->update([
                'index' => ELASTIC_CLIENT_LIST,
                'id' => $row->client_id,
                'retry_on_conflict' => 5,
                'body' => [
                    'doc' => [
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
                        'company_size_from' => $row->company_size_from,
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
                    ],
                    'doc_as_upsert' => true,
                    'detect_noop' => false,
                ],
            ]);

        }

        return $response;

    }

    public static function appendClientNestedData($id)
    {
        $elasticService = app(ElasticService::class);
        $response = '';
        $staffing_company_relation = DB::table('staffing_company_relation')
            ->where('staffing_company_relation.id', $id)
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
        // direct clients
        foreach ($staffing_company_relation as $row) {
            $row = (object) $row;
            $response = $elasticService->getClient()->update([
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

        return $response;

    }

    public static function getRelationData($to_company_id, $from_company_id)
    {
        $elasticService = app(ElasticService::class);

        $response = $elasticService->getClient()->search([
            'index' => ELASTIC_CLIENT_AND_AGENCY_RELATION_INDEX,
            'body' => [
                'size' => 1,
                'query' => [
                    'bool' => [
                        'filter' => [
                            [
                                'bool' => [
                                    'must' => [
                                        ['term' => ['from_company_id' => (int) $from_company_id]],
                                        ['term' => ['to_company_id' => (int) $to_company_id]],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'sort' => [
                    ['created_at' => ['order' => 'desc']],
                ],
            ],
        ]);

        return (object) ($response['hits']['hits'][0]['_source'] ?? []);
    }

    public static function getrRcommendationRelationData($company_id, $targetJobId)
    {
        $targetJobId = (int) $targetJobId;
        if ($targetJobId <= 0) {
            return (object) [];
        }

        try {
            $elasticService = app(ElasticService::class);
            $response = $elasticService->getClient()->search([
                'index' => ELASTIC_JOB_REQUEST,
                'body' => [
                    'size' => 1,
                    'query' => [
                        'bool' => [
                            'must' => [
                                ['term' => ['to_company_id' => (int) $company_id]],
                                ['term' => ['job_id' => $targetJobId]],
                            ],
                        ],
                    ],
                    'sort' => [
                        ['created_at' => ['order' => 'desc']],
                    ],
                ],
            ]);

            return (object) ($response['hits']['hits'][0]['_source'] ?? []);
        } catch (\Throwable $e) {
            return (object) [];
        }
    }

    /**
     * Bulk variant of getrRcommendationRelationData() for list rendering —
     * ONE Elasticsearch search for a whole page of jobs instead of one per
     * row. Returns [job_id => latest relation object], using the same
     * "newest created_at wins" selection as the single-row helper; jobs
     * with no relation are absent from the map (callers default to
     * (object)[], matching the single-row helper's empty-object return).
     * Tenancy: results are scoped by $company_id (to_company_id term).
     */
    public static function getrRcommendationRelationDataBulk($company_id, array $jobIds)
    {
        if (empty($jobIds)) {
            return [];
        }
        $elasticService = app(ElasticService::class);
        $map = [];
        try {
            $response = $elasticService->getClient()->search([
                'index' => ELASTIC_JOB_REQUEST,
                'body' => [
                    // headroom for multiple relation rows per job — newest-first
                    // sort below means the first hit seen per job wins
                    'size' => count($jobIds) * 5,
                    'query' => [
                        'bool' => [
                            'must' => [
                                ['term' => ['to_company_id' => (int) $company_id]],
                                ['terms' => ['job_id' => array_map('intval', $jobIds)]],
                            ],
                        ],
                    ],
                    'sort' => [['created_at' => ['order' => 'desc']]],
                ],
            ]);
            foreach ($response['hits']['hits'] ?? [] as $hit) {
                $src = $hit['_source'] ?? [];
                $jid = $src['job_id'] ?? null;
                if ($jid !== null && ! isset($map[$jid])) {
                    $map[$jid] = (object) $src;
                }
            }
        } catch (\Throwable $e) {
            \Log::error('getrRcommendationRelationDataBulk failed: '.$e->getMessage());
        }

        return $map;
    }

    /**
     * Bulk variant of benchRequestCheck() — ONE Elasticsearch search for all
     * jobs on the page. Returns [job_id => request object]; an absent key
     * means no request exists (the single-row helper's null).
     */
    public static function benchRequestCheckBulk($candidate_id, array $jobIds)
    {
        if (empty($jobIds)) {
            return [];
        }
        $elasticService = app(ElasticService::class);
        $map = [];
        try {
            $response = $elasticService->getClient()->search([
                'index' => ELASTIC_CANDIDATES_REQUEST,
                'body' => [
                    'size' => count($jobIds) * 2,
                    'query' => [
                        'bool' => [
                            'filter' => [
                                ['term' => ['candidate_id' => $candidate_id]],
                                ['terms' => ['job_id' => array_map('intval', $jobIds)]],
                            ],
                        ],
                    ],
                ],
            ]);
            foreach ($response['hits']['hits'] ?? [] as $hit) {
                $src = $hit['_source'] ?? [];
                $jid = $src['job_id'] ?? null;
                if ($jid !== null && ! isset($map[$jid])) {
                    $map[$jid] = (object) $src;
                }
            }
        } catch (\Throwable $e) {
            \Log::error('benchRequestCheckBulk failed: '.$e->getMessage());
        }

        return $map;
    }

    public static function getAgencyRelationData($from_company_id, $to_company_id)
    {
        $elasticService = app(ElasticService::class);

        $response = $elasticService->getClient()->search([
            'index' => ELASTIC_CLIENT_REQUEST,
            'body' => [
                'size' => 1,
                'query' => [
                    'bool' => [
                        'must' => [
                            ['term' => ['from_company_id' => (int) $from_company_id]],
                            ['term' => ['to_company_id' => (int) $to_company_id]],
                        ],
                    ],
                ],
                'sort' => [
                    ['created_at' => ['order' => 'desc']],
                ],
            ],
        ]);

        return (object) ($response['hits']['hits'][0]['_source'] ?? []);

    }

    public static function userIndexingupload($id)
    {
        $response = '';
        $elasticService = app(ElasticService::class);
        $teams = DB::table('users')
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
            )->where('users.id', '=', $id)
            ->groupBy('users.id')
            ->get();
        // dd($teams);

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

            // $candidate_relation =  DB::table('updatetimeline')
            //     ->select('master_process_name','job_id','application_id','create_at','update_at')
            //     ->where('event_by_user_id', $row->id)
            // 	->whereIn('master_process_name', ['Sourcing', 'Joined'])
            //     ->get()
            //     ->map(function ($s){
            //         return [
            //         'master_process_name' => $s->master_process_name,
            //         'application_id' => (int) $s->application_id,
            //         'job_id' => (int) $s->job_id,
            //         'created_at' =>  ($s->create_at) ? Carbon::parse($s->create_at)->toIso8601String() : null,
            //         'updated_at' =>  ($s->update_at) ? Carbon::parse($s->update_at)->toIso8601String() : null
            //         ];
            //      })
            //     ->toArray();

            $candidate_relation = [];

            $response = $elasticService->getClient()->update([
                'index' => ELASTIC_USERS_LIST,
                'id' => $row->id,
                'retry_on_conflict' => 5,
                'body' => [
                    'doc' => [
                        'id' => @$row->id,
                        'company_id' => @$row->company_id,
                        'company_name' => strtolower(@$row->company_name),
                        'root_user' => @$row->root_user,
                        'name' => strtolower(@$row->name),
                        'email' => @$row->email ? strtolower($row->email) : null,
                        'calling_code' => @$row->calling_code,
                        'mobile' => @$row->mobile,
                        'whatsapp_number' => @$row->whatsapp_number,
                        'linkedin' => @$row->linkedin,
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
                    ],
                    'doc_as_upsert' => true,
                    'detect_noop' => false,
                ],
            ]);

        }

        return $response;
    }

    public static function benchPlacedIndexingupload($id)
    {
        $response = '';
        $elasticService = app(ElasticService::class);
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
        $placedApplications = $placedApplications->where('bench_place.id', '=', (int) $id);
        $placedApplications = $placedApplications->groupBy('bench_place.id')->get();

        foreach ($placedApplications as $row) {
            $country_name = '';

            $response = $elasticService->getClient()->update([
                'index' => ELASTIC_BENCH_PLACED,
                'id' => $row->id,
                'retry_on_conflict' => 5,
                'body' => [
                    'doc' => [
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
                    ],
                    'doc_as_upsert' => true,
                    'detect_noop' => false,
                ],
            ]);

        }

        return $response;
    }

    public static function candidateRequestIndexElastic($id)
    {
        $elasticService = app(ElasticService::class);
        $response = '';

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
            ->where('rec_candidate_assign.id', '=', $id)
            ->get();

        foreach ($rec_candidate_assign as $application) {
            $duplicate_candidate = DB::table('add_job_wise_condidates')
                ->where('job_application_id', $application->id)
                ->where('job_id', $application->source_jobid)
                ->count();

            $response = $elasticService->getClient()->update([
                'index' => ELASTIC_CANDIDATES_REQUEST,
                'id' => $application->rec_candidate_assign_id,
                'retry_on_conflict' => 5,
                'body' => [
                    'doc' => [
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
                    ],
                    'doc_as_upsert' => true,
                    'detect_noop' => false,
                ],
            ]);
        }

        return $response;

    }

    public static function jobRequestIndexElastic($id)
    {
        $elasticService = app(ElasticService::class);
        $response = '';

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
                'recommendation_relation.from_user_id',
                'recommendation_relation.from_status',
                'recommendation_relation.from_created_at',
                'recommendation_relation.to_company_id',
                'recommendation_relation.to_user_id',
                'recommendation_relation.job_id',
                'recommendation_relation.to_status',
                'recommendation_relation.to_created_at',
                'recommendation_relation.final_status',
                'recommendation_relation.accepterms',
                'recommendation_relation.conversation_id',
                'recommendation_relation.add_status',
                'recommendation_relation.created_at as created_at_rec',
                'recommendation_relation.updated_at as updated_at_rec',
                'to_com.logo as logo_to',
                'to_com.company_name as company_name_to'
            )
            ->join('clients', 'clients.id', 'jobs.client_id')
            ->join('companies', 'companies.id', 'clients.company_id')
            ->join('recommendation_relation', 'recommendation_relation.job_id', 'jobs.id')
            ->leftJoin('companies as to_com', 'to_com.id', '=', 'recommendation_relation.to_company_id')
            ->where('jobs.id', '>', '0');
        $categories = $categories->where('recommendation_relation.id', $id);
        $categories = $categories->orderBy('jobs.id', 'DESC')->get();

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

            $response = $elasticService->getClient()->update([
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
                        'accepterms' => $row->accepterms,
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

        return $response;

    }

    /**
     * Intent: Re-index a single client request invitation into Elasticsearch (ELASTIC_CLIENT_REQUEST).
     * Tenancy: Scoped by staffing_company_invitation ID and from_company_id/to_company_id.
     */
    public static function clientRequestIndexElastic($id)
    {
        $elasticService = app(ElasticService::class);
        $response = '';

        $clientRequestDataList = DB::table('staffing_company_invitation')
            ->select(
                'staffing_company_invitation.id as staffing_company_invitation_id',
                'staffing_company_invitation.id',
                'staffing_company_invitation.from_company_id',
                'staffing_company_invitation.from_client_id',
                'staffing_company_invitation.from_user_id',
                'staffing_company_invitation.from_status',
                'staffing_company_invitation.from_created_at',
                'staffing_company_invitation.to_company_id',
                'staffing_company_invitation.to_email',
                'staffing_company_invitation.to_status',
                'staffing_company_invitation.job_id',
                'staffing_company_invitation.to_invitation_status',
                'companies.id as company_id',
                'companies.company_name',
                'companies.logo as company_logo',
                'companies.industries',
                'clients.id as client_id',
                'clients.name as client_name',
                'clients.logo as client_logo',
                'clients.logo_logical_path as client_logo_logical_path',
                'clients.tags',
                'clients.industry as client_industry',
                'td_industries.ind_name as company_ind_name',
                'client_ind.ind_name as client_ind_name'
            )
            ->join('companies', 'companies.id', '=', 'staffing_company_invitation.from_company_id')
            ->leftJoin('clients', function ($join) {
                $join->on('clients.id', '=', DB::raw('COALESCE(NULLIF(staffing_company_invitation.from_client_id, 0), (SELECT c2.id FROM clients c2 WHERE c2.company_id = staffing_company_invitation.from_company_id AND c2.self_company = 1 LIMIT 1))'));
            })
            ->leftJoin('td_industries', 'td_industries.ind_id', '=', 'companies.industries')
            ->leftJoin('td_industries as client_ind', 'client_ind.ind_id', '=', 'clients.industry')
            ->where('staffing_company_invitation.id', '=', $id)
            ->get();

        foreach ($clientRequestDataList as $row) {
            $client_id = $row->client_id;
            $locInfo = null;
            if ($client_id) {
                $locInfo = DB::table('client_location')
                    ->select('client_location.id', 'client_location.city', 'client_location.address', 'client_location.postcode', 'client_location.country_id', 'city_list.city as city_name', 'countries.country_name')
                    ->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')
                    ->leftJoin('countries', 'countries.id', '=', 'client_location.country_id')
                    ->where('client_id', $client_id)
                    ->first();
            }
            $country_name = $locInfo->country_name ?? '';
            $city_name = $locInfo->city_name ?? '';

            $nameToUse = ! empty($row->client_name) ? $row->client_name : $row->company_name;
            $logoToUse = ! empty($row->client_logo_logical_path) ? url('public/'.$row->client_logo_logical_path) : (! empty($row->client_logo) ? $row->client_logo : $row->company_logo);
            $indNameToUse = ! empty($row->client_ind_name) ? $row->client_ind_name : $row->company_ind_name;

            $updateTime = now()->toIso8601String();
            $response = $elasticService->getClient()->update([
                'index' => ELASTIC_CLIENT_REQUEST,
                'id' => $row->staffing_company_invitation_id,
                'retry_on_conflict' => 5,
                'body' => [
                    'doc' => [
                        'id' => $row->staffing_company_invitation_id,
                        'company_id' => $row->company_id,
                        'company_name' => $nameToUse,
                        'logo' => $logoToUse,
                        'industries' => $row->industries,
                        'client_id' => $row->client_id,
                        'tags' => $row->tags,
                        'ind_name' => $indNameToUse,
                        'country_name' => $country_name,
                        'staffing_company_invitation_id' => $row->staffing_company_invitation_id,
                        'from_company_id' => $row->from_company_id,
                        'from_created_at' => ($row->from_created_at) ? Carbon::parse($row->from_created_at)->toIso8601String() : null,
                        'updated_at' => $updateTime,
                        'created_at' => ($row->from_created_at) ? Carbon::parse($row->from_created_at)->toIso8601String() : $updateTime,
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
                    ],
                    'doc_as_upsert' => true,
                    'detect_noop' => false,
                ],
            ]);
        }

        return $response;

    }

    public static function teamRankElastic($teamRankElastic = [])
    {
        $elasticService = app(ElasticService::class);
        $response = '';
        $company_id = $teamRankElastic['company_id'] ?? 0;
        $user_id = $teamRankElastic['user_id'] ?? 0;
        $candidate_id = $teamRankElastic['candidate_id'] ?? 0;
        $profileUpload = $teamRankElastic['profileUpload'] ?? 0;
        $interviewScheduled = $teamRankElastic['interviewScheduled'] ?? 0;
        $interviewNoShow = $teamRankElastic['interviewNoShow'] ?? 0;
        $offerReleased = $teamRankElastic['offerReleased'] ?? 0;
        $candidateJoined = $teamRankElastic['candidateJoined'] ?? 0;
        $jobsPosted = $teamRankElastic['jobsPosted'] ?? 0;
        $feedbackGivenOnCVs = $teamRankElastic['feedbackGivenOnCVs'] ?? 0;
        $postJobToVendorMarket = $teamRankElastic['postJobToVendorMarket'] ?? 0;
        $outsourceJobsToVendors = $teamRankElastic['outsourceJobsToVendors'] ?? 0;
        $listBenchCandidates = $teamRankElastic['listBenchCandidates'] ?? 0;
        $respondToBenchRequests = $teamRankElastic['respondToBenchRequests'] ?? 0;

        $interestedCandidates = $teamRankElastic['interestedCandidates'] ?? 0;
        $notInterestedCandidates = $teamRankElastic['notInterestedCandidates'] ?? 0;
        $teamCaptainInterviewScheduled = $teamRankElastic['teamCaptainInterviewScheduled'] ?? 0;
        $teamCaptainInterviewNoShow = $teamRankElastic['teamCaptainInterviewNoShow'] ?? 0;
        $teamCaptainOfferReleased = $teamRankElastic['teamCaptainOfferReleased'] ?? 0;
        $teamCaptainCandidateJoined = $teamRankElastic['teamCaptainCandidateJoined'] ?? 0;

        $sourcing = $teamRankElastic['sourcing'] ?? 0;
        $screening = $teamRankElastic['screening'] ?? 0;
        $evaluation = $teamRankElastic['evaluation'] ?? 0;
        $negotiation = $teamRankElastic['negotiation'] ?? 0;
        $backout = $teamRankElastic['backout'] ?? 0;
        $backoutTeam = $teamRankElastic['backoutTeam'] ?? 0;

        // Contract-hiring variants of the "joined"/backout events (full-time stays in the base keys).
        $candidateJoinedContract = $teamRankElastic['candidateJoinedContract'] ?? 0;
        $backoutContract = $teamRankElastic['backoutContract'] ?? 0;
        $teamCaptainCandidateJoinedContract = $teamRankElastic['teamCaptainCandidateJoinedContract'] ?? 0;
        $backoutTeamContract = $teamRankElastic['backoutTeamContract'] ?? 0;

        $updateTime = now()->toIso8601String();

        $mysqlId = DB::table('company_user_points_logs')->insertGetId([
            'company_id' => $company_id,
            'user_id' => $user_id,
            'candidate_id' => $candidate_id,
            'profileUpload' => $profileUpload,
            'interviewScheduled' => $interviewScheduled,
            'interviewNoShow' => $interviewNoShow,
            'offerReleased' => $offerReleased,
            'candidateJoined' => $candidateJoined,
            'jobsPosted' => $jobsPosted,
            'feedbackGivenOnCVs' => $feedbackGivenOnCVs,
            'postJobToVendorMarket' => $postJobToVendorMarket,
            'outsourceJobsToVendors' => $outsourceJobsToVendors,
            'listBenchCandidates' => $listBenchCandidates,
            'respondToBenchRequests' => $respondToBenchRequests,
            'interestedCandidates' => $interestedCandidates,
            'notInterestedCandidates' => $notInterestedCandidates,
            'teamCaptainInterviewScheduled' => $teamCaptainInterviewScheduled,
            'teamCaptainInterviewNoShow' => $teamCaptainInterviewNoShow,
            'teamCaptainOfferReleased' => $teamCaptainOfferReleased,
            'teamCaptainCandidateJoined' => $teamCaptainCandidateJoined,
            'candidateJoinedContract' => $candidateJoinedContract,
            'teamCaptainCandidateJoinedContract' => $teamCaptainCandidateJoinedContract,
            'sourcing' => $sourcing,
            'screening' => $screening,
            'evaluation' => $evaluation,
            'negotiation' => $negotiation,
            'backout' => $backout,
            'backoutTeam' => $backoutTeam,
            'backoutContract' => $backoutContract,
            'backoutTeamContract' => $backoutTeamContract,
            'total_points' => 0,
            'created_at' => isset($updateTime) ? date('Y-m-d H:i:s', strtotime($updateTime)) : null,
            'updated_at' => isset($updateTime) ? date('Y-m-d H:i:s', strtotime($updateTime)) : null,
        ]);
        // dd("sksk",$mysqlId);

        $response = $elasticService->getClient()->index([
            'index' => ELASTIC_TEAM_RANK,
            'id' => $mysqlId,
            'body' => [
                'doc' => [
                    'company_id' => $company_id,
                    'user_id' => $user_id,
                    'candidate_id' => $candidate_id,
                    'profileUpload' => $profileUpload,  // +1 point
                    'interviewScheduled' => $interviewScheduled, // +100 points
                    'interviewNoShow' => $interviewNoShow, // –5 points
                    'offerReleased' => $offerReleased, // +300 points
                    'candidateJoined' => $candidateJoined, // +500 points
                    'jobsPosted' => $jobsPosted, // +2 pts per job
                    'feedbackGivenOnCVs' => $feedbackGivenOnCVs, // +3 pts per feedback
                    'postJobToVendorMarket' => $postJobToVendorMarket, // +2 pts per vendor post
                    'outsourceJobsToVendors' => $outsourceJobsToVendors, // +5 pts per vendor assignment
                    'listBenchCandidates' => $listBenchCandidates, // +3 pts per candidate listed
                    'respondToBenchRequests' => $respondToBenchRequests, // +4 pts per response
                    'interestedCandidates' => $interestedCandidates, // +2 pts per response
                    'notInterestedCandidates' => $notInterestedCandidates, // -1 pts per response
                    'teamCaptainInterviewScheduled' => $teamCaptainInterviewScheduled, // +2 pts per response
                    'teamCaptainInterviewNoShow' => $teamCaptainInterviewNoShow, // -1 pts per response
                    'teamCaptainOfferReleased' => $teamCaptainOfferReleased, // +10 pts per response
                    'teamCaptainCandidateJoined' => $teamCaptainCandidateJoined, // +20 pts per response
                    'candidateJoinedContract' => $candidateJoinedContract, // joined on a contract job
                    'teamCaptainCandidateJoinedContract' => $teamCaptainCandidateJoinedContract, // team captain, contract job
                    'sourcing' => $sourcing, // +1 pts per response
                    'screening' => $screening, // +1 pts per response
                    'evaluation' => $evaluation, // +1 pts per response
                    'negotiation' => $negotiation, // +1 pts per response
                    'backout' => $backout, // join pts per response
                    'backoutTeam' => $backoutTeam, // join pts per response
                    'backoutContract' => $backoutContract, // backout on a contract job
                    'backoutTeamContract' => $backoutTeamContract, // team captain backout, contract job
                    'created_at' => $updateTime,
                    'updated_at' => $updateTime,
                ],
            ],
        ]);

    }

    public static function skillsAddElastic($skillsElastic = [])
    {
        $elasticService = app(ElasticService::class);

        if (count($skillsElastic) > 0) {
            foreach ($skillsElastic as $row) {

                $skill_name = strtolower(trim($row));

                $result = $elasticService->getClient()->search([
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
                    // $elasticService->getClient()->line("⚠️ Skipped duplicate: {$skill_name}");

                    continue;
                }

                $customId = uniqid('skill_', true); // e.g., skill_64f9a7b3a12c4
                $response = $elasticService->getClient()->index([
                    'index' => ELASTIC_SKILLS,
                    'id' => $customId,   // store as ES _id
                    'body' => [
                        'skill_name' => $skill_name,
                        'created_at' => now()->toIso8601String(),
                        'updated_at' => now()->toIso8601String(),
                    ],
                ]);
            }
        }

    }

    public static function jobStatusCountElastic($company_id, $jobStatus = '')
    {
        $elasticService = app(ElasticService::class);
        $statusArr = ['active', 'hold', 'inactive', '2'];
        $filter = [];
        if (! empty($jobStatus)) {
            $statusArr = [];
            $statusArr = [$jobStatus];
            $filter[] = ['terms' => ['status' => $statusArr]];
        }
        $must[] = [
            'bool' => [
                'should' => [
                    ['term' => ['comId' => $company_id]],
                    [
                        'nested' => [
                            'path' => 'recommendation_relation',
                            'query' => [
                                'term' => ['recommendation_relation.to_company_id' => $company_id],
                            ],
                        ],
                    ],
                ],
                'minimum_should_match' => 1,
            ],
        ];

        $params = [
            'index' => ELASTIC_JOBS,
            'request_cache' => true,
            'body' => [
                'track_total_hits' => true,
                'query' => [
                    'bool' => [
                        'must' => $must,
                        'filter' => $filter,
                    ],
                ],
            ],
        ];

        if (! $elasticService->indexExists(ELASTIC_JOBS)) {
            $total = 0;
            $data = [];
        } else {
            $response = $elasticService->getClient()->search($params);
            $total = $response['hits']['total']['value'] ?? 0;
            $data = collect($response['hits']['hits'])
                ->map(function ($hit) {
                    $job = (object) $hit['_source'];

                    $skills = collect($job->job_metronics_skills ?? [])
                        ->map(fn ($s) => [
                            'skill_id' => $s['skill_id'] ?? null,
                            'skill_name' => strtolower($s['skill_name'] ?? ''),
                        ])
                        ->toArray();

                    return [
                        'job_id' => $job->job_id ?? $job->id,
                        'job_title' => $job->job_title ?? '',
                        'employment_type' => $job->employment_type ?? '',
                        'status' => $job->status ?? '',
                        'skills' => $skills,
                    ];
                })
                ->toArray();
        }

        return $total;

    }

    public static function CompanyAddEditUpload($id)
    {
        return true;
        $response = '';
        $elasticService = app(ElasticService::class);
        $companies = DB::table('companies')
            ->select('companies.*', 'td_industries.ind_name as industry_name')
            ->leftJoin('td_industries', 'td_industries.ind_id', '=', 'companies.industries')
            ->where('id', '=', $id)
            ->get();

        foreach ($companies as $company) {
            $company_id = $company->id;

            $industry = DB::table('td_industries')
                ->where('ind_id', $company->industries)
                ->first();

            $company_coins = DB::table('company_coins')
                ->where('company_id', $company_id)
                ->get()
                ->map(function ($row) {
                    return (array) $row;
                })
                ->toArray();

            $commericial_countries = DB::table('commericial_countries')
                ->where('company_id', $company_id)
                ->get()
                ->map(function ($row) {
                    return (array) $row;
                })
                ->toArray();

            $team_members = DB::table('users')
                ->where('company_id', $company_id)
                ->where('root_user', '!=', 1)
                ->get()
                ->map(function ($row) {
                    return (array) $row;
                })
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
                        ->map(function ($s) {
                            [
                                'city_name' => $s->city_name,
                                'state_name' => $s->state_name,
                                'country_name' => $s->country_name,
                            ];
                        })
                        ->toArray();

                    return [
                        'locations_address' => $address,
                    ];
                })
                ->toArray();

            $job_metronics_skills = DB::table('job_metronics_skills')
                ->where('company_id', $company_id)
                ->get()
                ->map(function ($row) {
                    return (array) $row;
                })
                ->toArray();

            $boostkeywords_skills = DB::table('boostkeywords')
                ->where('company_id', $company_id)
                ->get()
                ->map(function ($row) {
                    return (array) $row;
                })
                ->toArray();

            $staffing_relations = DB::table('staffing_company_relation')
                ->where('from_company_id', $company_id)
                ->orWhere('to_company_id', $company_id)
                ->get()
                ->map(function ($row) {
                    return (array) $row;
                })
                ->toArray();

            $recommendation_relations = DB::table('recommendation_relation')
                ->where('from_company_id', $company_id)
                ->orWhere('to_company_id', $company_id)
                ->get()
                ->map(function ($row) {
                    return (array) $row;
                })
                ->toArray();

            $jobs = DB::table('jobs')
                ->leftJoin('recommendation_relation', 'recommendation_relation.job_id', '=', 'jobs.id')
                ->where('jobs.company_id', $company_id)
                ->orWhere('recommendation_relation.to_company_id', $company_id)
                ->get()
                ->map(function ($job) {
                    $skills = DB::table('job_metronics_skills')
                        ->where('job_id', $job->id)
                        ->get()
                        ->map(function ($s) {
                            return [
                                'skill_id' => $s->id,
                                'skill_name' => strtolower($s->skill_name),
                            ];
                        })
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
                ->select('job_applications.id', 'job_applications.full_name', 'job_applications.condidate_type')
                ->where('add_job_wise_condidates.company_id', $company_id)
                ->get()
                ->map(function ($c) {
                    $skills = DB::table('job_application_skills')
                        ->join('job_applications', 'job_applications.id', '=', 'job_application_skills.job_application_id')
                        ->select('job_application_skills.*')
                        ->where('job_application_skills.job_application_id', $c->id)
                        ->get()
                        ->map(function ($s) {
                            return [
                                'skill_id' => $s->id,
                                'skill_name' => strtolower($s->skills),
                            ];
                        })
                        ->toArray();

                    return [
                        'candidate_id' => $c->id,
                        'candidate_name' => $c->full_name,
                        'profile_type' => $c->condidate_type,
                        'skills' => $skills,
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
                        ->map(function ($d) {
                            return [
                                'exp' => $d->exp,
                                'rate' => $d->rate,
                                'commtype' => $d->commtype,
                            ];
                        })
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

            $response = $elasticService->getClient()->update([
                'index' => ELASTIC_COMPANIES_INDEX,
                'id' => $company_id,
                'retry_on_conflict' => 5,
                'body' => [
                    'doc' => [
                        'id' => $company_id,
                        'company_name' => $company->company_name,
                        'logo' => $company->logo,
                        'company_type' => $company->company_type,
                        'leadCount' => $company->leadCount,
                        'last_login' => $company->last_login ? Carbon::parse($company->last_login)->toIso8601String() : null,
                        'industry' => $industry ? [
                            'ind_id' => $industry->ind_id,
                            'industry_name' => $industry->ind_name,
                        ] : null,
                        'company_coins' => $company_coins,
                        'commericial_countries' => $commericial_countries,
                        'team_members' => $team_members,
                        'locations_address' => $clients,
                        'job_metronics_skills' => $job_metronics_skills,
                        'boostkeywords_skills' => $boostkeywords_skills,
                        'staffing_company_relation' => $staffing_relations,
                        'recommendation_relation' => $recommendation_relations,
                        'jobs' => $jobs,
                        'candidates' => $candidates,
                        'commericials' => $commericials,
                    ],
                    'doc_as_upsert' => true,
                    'detect_noop' => false,
                ],
            ]);

        }

    }

    public static function confidentialAmontShow($jobId)
    {
        $userId = auth()->user()->id;
        //    $role_id = auth()->user()->role->role_id;
        // $userAllow = DB::table('users')
        //     ->join('job_assign', 'job_assign.user_id', 'users.id')
        //     ->select('users.id')
        //     ->where('job_assign.job_id', $jobId)
        //     ->where('job_assign.user_id', $userId)
        //     ->count();
        $userAllow = DB::table('job_assign')
            ->where('job_id', $jobId)
            ->where('user_id', $userId)
            ->exists();

        $chkAdminRole = auth()->user()->hasRole('admin');

        return $userAllow || $chkAdminRole;
        // $chkAdminRole = DB::table('roles')->select('*')->where('id', $role_id)->where('name', 'admin')->count();

        // $allow = false;
        // if ($userAllow == 1 || $chkAdminRole == 1) {
        //     $allow = true;
        // }

        // return $allow;
    }

    public static function getTeamRankData($company_id, $loggedInUserId, $startDate, $endDate, $isInternal = true, $returnFull = false)
    {
        $elasticService = app(ElasticService::class);
        $userQuery = [
            'index' => ELASTIC_USERS_LIST,
            'body' => [
                'query' => [
                    'bool' => [
                        'filter' => array_values(array_filter([
                            $isInternal ? ['term' => ['company_id' => $company_id]] : null,
                            ['term' => ['status' => 'active']],
                        ])),
                    ],
                ],
                '_source' => ['id', 'name', 'email', 'designation'],
                'size' => 10000,
                'sort' => [
                    ['id' => ['order' => 'asc']],
                ],
            ],
        ];

        $userResponse = $elasticService->getClient()->search($userQuery);
        $users = collect($userResponse['hits']['hits'])->pluck('_source');

        // Get ranking stats
        $rankQuery = [
            'index' => ELASTIC_TEAM_RANK,
            'body' => [
                'query' => [
                    'bool' => [
                        'filter' => array_values(array_filter([
                            $isInternal ? ['term' => ['doc.company_id' => $company_id]] : null,
                            ['range' => [
                                'doc.created_at' => [
                                    'gte' => $startDate,
                                    'lte' => $endDate,
                                ],
                            ]],
                        ])),
                    ],
                ],
                'aggs' => [
                    'user_scores' => [
                        'terms' => [
                            'field' => 'doc.user_id',
                            'size' => 10000,
                        ],
                        'aggs' => [
                            'profileUpload_sum' => ['sum' => ['field' => 'doc.profileUpload']],
                            'interviewScheduled_sum' => ['sum' => ['field' => 'doc.interviewScheduled']],
                            'interviewNoShow_sum' => ['sum' => ['field' => 'doc.interviewNoShow']],
                            'offerReleased_sum' => ['sum' => ['field' => 'doc.offerReleased']],
                            'candidateJoined_sum' => ['sum' => ['field' => 'doc.candidateJoined']],
                            'interestedCandidates_sum' => ['sum' => ['field' => 'doc.interestedCandidates']],
                            'notInterestedCandidates_sum' => ['sum' => ['field' => 'doc.notInterestedCandidates']],
                            'teamCaptainInterviewScheduled_sum' => ['sum' => ['field' => 'doc.teamCaptainInterviewScheduled']],
                            'teamCaptainInterviewNoShow_sum' => ['sum' => ['field' => 'doc.teamCaptainInterviewNoShow']],
                            'teamCaptainOfferReleased_sum' => ['sum' => ['field' => 'doc.teamCaptainOfferReleased']],
                            'teamCaptainCandidateJoined_sum' => ['sum' => ['field' => 'doc.teamCaptainCandidateJoined']],
                            'attachments_sum' => ['sum' => ['field' => 'doc.attachmentCount']],
                            'sourcing_sum' => ['sum' => ['field' => 'doc.sourcing']],
                            'screening_sum' => ['sum' => ['field' => 'doc.screening']],
                            'evaluation_sum' => ['sum' => ['field' => 'doc.evaluation']],
                            'negotiation_sum' => ['sum' => ['field' => 'doc.negotiation']],
                            'backout_sum' => ['sum' => ['field' => 'doc.backout']],
                            'backoutTeam_sum' => ['sum' => ['field' => 'doc.backoutTeam']],
                            'candidateJoinedContract_sum' => ['sum' => ['field' => 'doc.candidateJoinedContract']],
                            'teamCaptainCandidateJoinedContract_sum' => ['sum' => ['field' => 'doc.teamCaptainCandidateJoinedContract']],
                            'backoutContract_sum' => ['sum' => ['field' => 'doc.backoutContract']],
                            'backoutTeamContract_sum' => ['sum' => ['field' => 'doc.backoutTeamContract']],
                        ],
                    ],
                ],
            ],
        ];

        $response = $elasticService->getClient()->search($rankQuery);
        $buckets = $response['aggregations']['user_scores']['buckets'];

        $scoreMap = collect($buckets)->mapWithKeys(function ($item) {
            $score = ($item['profileUpload_sum']['value'] ?? 0)
                + ($item['interviewScheduled_sum']['value'] ?? 0)
                + ($item['interviewNoShow_sum']['value'] ?? 0)
                + ($item['offerReleased_sum']['value'] ?? 0)
                + ($item['candidateJoined_sum']['value'] ?? 0)
                + ($item['teamCaptainInterviewScheduled_sum']['value'] ?? 0)
                + ($item['teamCaptainOfferReleased_sum']['value'] ?? 0)
                + ($item['teamCaptainCandidateJoined_sum']['value'] ?? 0)
                + ($item['sourcing_sum']['value'] ?? 0)
                + ($item['screening_sum']['value'] ?? 0)
                + ($item['evaluation_sum']['value'] ?? 0)
                + ($item['negotiation_sum']['value'] ?? 0)
                + ($item['backout_sum']['value'] ?? 0)
                + ($item['backoutTeam_sum']['value'] ?? 0)
                + ($item['candidateJoinedContract_sum']['value'] ?? 0)
                + ($item['teamCaptainCandidateJoinedContract_sum']['value'] ?? 0)
                + ($item['backoutContract_sum']['value'] ?? 0)
                + ($item['backoutTeamContract_sum']['value'] ?? 0);

            return [$item['key'] => [
                'user_id' => $item['key'],
                'profileUpload' => $item['profileUpload_sum']['value'] ?? 0,
                'interviewScheduled' => $item['interviewScheduled_sum']['value'] ?? 0,
                'interviewNoShow' => $item['interviewNoShow_sum']['value'] ?? 0,
                'offerReleased' => $item['offerReleased_sum']['value'] ?? 0,
                'candidateJoined' => $item['candidateJoined_sum']['value'] ?? 0,
                'teamCaptainInterviewScheduled' => $item['teamCaptainInterviewScheduled_sum']['value'] ?? 0,
                'teamCaptainOfferReleased' => $item['teamCaptainOfferReleased_sum']['value'] ?? 0,
                'teamCaptainCandidateJoined' => $item['teamCaptainCandidateJoined_sum']['value'] ?? 0,
                'sourcing' => ($item['sourcing_sum']['value'] ?? 0),
                'screening' => ($item['screening_sum']['value'] ?? 0),
                'evaluation' => ($item['evaluation_sum']['value'] ?? 0),
                'negotiation' => ($item['negotiation_sum']['value'] ?? 0),
                'backout' => ($item['backout_sum']['value'] ?? 0),
                'backoutTeam' => ($item['backoutTeam_sum']['value'] ?? 0),
                'candidateJoinedContract' => ($item['candidateJoinedContract_sum']['value'] ?? 0),
                'teamCaptainCandidateJoinedContract' => ($item['teamCaptainCandidateJoinedContract_sum']['value'] ?? 0),
                'backoutContract' => ($item['backoutContract_sum']['value'] ?? 0),
                'backoutTeamContract' => ($item['backoutTeamContract_sum']['value'] ?? 0),
                'score' => $score,
            ]];
        });

        $ranked = $users->map(function ($user) use ($scoreMap, $startDate) {
            $id = $user['id'];

            return [
                'id' => $id,
                'name' => $user['name'] ?? '',
                'email' => $user['email'] ?? '',
                'designation' => $user['designation'] ?? '',
                'profileUpload' => $scoreMap[$id]['profileUpload'] ?? 0,
                'interviewScheduled' => $scoreMap[$id]['interviewScheduled'] ?? 0,
                'interviewNoShow' => $scoreMap[$id]['interviewNoShow'] ?? 0,
                'offerReleased' => $scoreMap[$id]['offerReleased'] ?? 0,
                'candidateJoined' => $scoreMap[$id]['candidateJoined'] ?? 0,
                'teamCaptainInterviewScheduled' => $scoreMap[$id]['teamCaptainInterviewScheduled'] ?? 0,
                'teamCaptainOfferReleased' => $scoreMap[$id]['teamCaptainOfferReleased'] ?? 0,
                'teamCaptainCandidateJoined' => $scoreMap[$id]['teamCaptainCandidateJoined'] ?? 0,
                'rankMonth' => date('M Y', strtotime($startDate)),
                'sourcing' => $scoreMap[$id]['sourcing'] ?? 0,
                'screening' => $scoreMap[$id]['screening'] ?? 0,
                'evaluation' => $scoreMap[$id]['evaluation'] ?? 0,
                'negotiation' => $scoreMap[$id]['negotiation'] ?? 0,
                'backout' => $scoreMap[$id]['backout'] ?? 0,
                'backoutTeam' => $scoreMap[$id]['backoutTeam'] ?? 0,
                'candidateJoinedContract' => $scoreMap[$id]['candidateJoinedContract'] ?? 0,
                'teamCaptainCandidateJoinedContract' => $scoreMap[$id]['teamCaptainCandidateJoinedContract'] ?? 0,
                'backoutContract' => $scoreMap[$id]['backoutContract'] ?? 0,
                'backoutTeamContract' => $scoreMap[$id]['backoutTeamContract'] ?? 0,
                'score' => $scoreMap[$id]['score'] ?? 0,
            ];
        })->sortByDesc('score')->values();

        $ranked = $ranked->map(function ($user, $index) {
            $user['rank'] = $index + 1;

            return $user;
        });

        if ($returnFull) {
            $currentUser = $ranked->firstWhere('id', $loggedInUserId) ?? ['score' => 0, 'rank' => 'N/A'];
            $leaders = $ranked->take(4);
            if ($currentUser && ! $leaders->contains('id', $loggedInUserId)) {
                $leaders->push($currentUser);
            }

            return [
                'user' => $currentUser,
                'leaders' => $leaders,
                'total' => $ranked->count(),
            ];
        }

        return $ranked->firstWhere('id', $loggedInUserId) ?? ['score' => 0, 'rank' => 'N/A'];
    }

    public static function addCompanyCandidate($company_id, $candidate_id)
    {

        $elasticService = app(ElasticService::class);
        $candidates = DB::table('add_job_wise_condidates')
            ->join('job_applications', 'job_applications.id', '=', 'add_job_wise_condidates.job_application_id')
            ->select('job_applications.id', 'job_applications.full_name', 'job_applications.condidate_type', 'add_job_wise_condidates.master_status_id')
            ->where('add_job_wise_condidates.id', (int) $candidate_id)
            ->get()
            ->map(function ($c) {
                $skills = DB::table('job_application_skills')
                    ->join('job_applications', 'job_applications.id', '=', 'job_application_skills.job_application_id')
                    ->select('job_application_skills.*')
                    ->where('job_application_skills.job_application_id', $c->id)
                    ->get()
                    ->map(function ($s) {
                        return [
                            'skill_id' => $s->id,
                            'skill_name' => strtolower($s->skills),
                        ];
                    })
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

        $newCandidates = $candidates;

        $response = $elasticService->getClient()->update([
            'index' => ELASTIC_COMPANIES_INDEX,
            'id' => (int) $company_id,
            'retry_on_conflict' => 5,
            'body' => [
                'script' => [
                    'lang' => 'painless',
                    'source' => '
								ctx._source.updated_at   = params.updated_at;

								if (ctx._source.candidates == null) {
									ctx._source.candidates = [];
								}

								for (newC in params.newCandidates) {
									boolean found = false;

									for (int i = 0; i < ctx._source.candidates.size(); i++) {
										if (ctx._source.candidates[i].candidate_id == newC.candidate_id) {
											ctx._source.candidates[i] = newC;
											found = true;
											break;
										}
									}

									if (!found) {
										ctx._source.candidates.add(newC);
									}
								}
							',
                    'params' => [
                        'newCandidates' => $newCandidates,
                        'updated_at' => now()->toIso8601String(),
                    ],
                ],
                'upsert' => [
                    'candidates' => $newCandidates,
                    'updated_at' => now()->toIso8601String(),
                ],
            ],
        ]);

    }

    public static function addCompanyBenchCandidate($company_id, $candidate_id)
    {

        $elasticService = app(ElasticService::class);
        $bench_candidates = DB::table('job_applications')
            ->select('job_applications.id', 'job_applications.full_name', 'job_applications.condidate_type', 'job_applications.status')
            ->where('job_applications.id', $candidate_id)
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

        $newBenchCandidates = $bench_candidates;

        $response = $elasticService->getClient()->update([
            'index' => ELASTIC_COMPANIES_INDEX,
            'id' => (int) $company_id,
            'retry_on_conflict' => 5,
            'body' => [
                'script' => [
                    'lang' => 'painless',
                    'source' => '
								ctx._source.updated_at   = params.updated_at;

								if (ctx._source.bench_candidates == null) {
									ctx._source.bench_candidates = [];
								}

								for (newC in params.newBenchCandidates) {
									boolean found = false;

									for (int i = 0; i < ctx._source.bench_candidates.size(); i++) {
										if (ctx._source.bench_candidates[i].candidate_id == newC.candidate_id) {
											ctx._source.bench_candidates[i] = newC;
											found = true;
											break;
										}
									}

									if (!found) {
										ctx._source.bench_candidates.add(newC);
									}
								}
							',
                    'params' => [
                        'newBenchCandidates' => $newBenchCandidates,
                        'updated_at' => now()->toIso8601String(),
                    ],
                ],
                'upsert' => [
                    'bench_candidates' => $newBenchCandidates,
                    'updated_at' => now()->toIso8601String(),
                ],
            ],
        ]);

    }

    public static function addUpdateCompanyJob($company_id, $job_id)
    {
        $elasticService = app(ElasticService::class);

        $jobs = DB::table('jobs')
            ->where('jobs.id', '=', $job_id)
            ->get()
            ->map(function ($job) {
                $skills = DB::table('job_metronics_skills')
                    ->where('job_id', $job->id)
                    ->get()
                    ->map(function ($s) {
                        return [
                            'skill_id' => $s->id,
                            'skill_name' => strtolower($s->skill_name),
                        ];
                    })
                    ->toArray();

                return [
                    'job_id' => $job->id,
                    'job_title' => $job->title,
                    'employment_type' => $job->employment_type,
                    'status' => $job->status,
                    'skills' => $skills,
                ];
            })->toArray();
        $newJobs = $jobs;

        $response = $elasticService->getClient()->update([
            'index' => ELASTIC_COMPANIES_INDEX,
            'id' => (int) $company_id,
            'retry_on_conflict' => 5,
            'body' => [
                'script' => [
                    'lang' => 'painless',
                    'source' => '
								ctx._source.updated_at   = params.updated_at;

								if (ctx._source.jobs == null) {
									ctx._source.jobs = [];
								}

								for (newC in params.newJobs) {
									boolean found = false;

									for (int i = 0; i < ctx._source.jobs.size(); i++) {
										if (ctx._source.jobs[i].job_id == newC.job_id) {
											ctx._source.jobs[i] = newC;
											found = true;
											break;
										}
									}

									if (!found) {
										ctx._source.jobs.add(newC);
									}
								}
							',
                    'params' => [
                        'newJobs' => $newJobs,
                        'updated_at' => now()->toIso8601String(),
                    ],
                ],
                'upsert' => [
                    'jobs' => $newJobs,
                    'updated_at' => now()->toIso8601String(),
                ],
            ],
        ]);

    }

    public static function addUpdateCompanyCoins($company_id)
    {
        $elasticService = app(ElasticService::class);
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

        $newCompanyCoins = $company_coins;

        $response = $elasticService->getClient()->update([
            'index' => ELASTIC_COMPANIES_INDEX,
            'id' => (int) $company_id,
            'retry_on_conflict' => 5,
            'body' => [
                'script' => [
                    'lang' => 'painless',
                    'source' => '
								ctx._source.updated_at   = params.updated_at;

								if (ctx._source.company_coins == null) {
									ctx._source.company_coins = [];
								}

								for (newC in params.newCompanyCoins) {
									boolean found = false;

									for (int i = 0; i < ctx._source.company_coins.size(); i++) {
										if (ctx._source.company_coins[i].id == newC.id) {
											ctx._source.company_coins[i] = newC;
											found = true;
											break;
										}
									}

									if (!found) {
										ctx._source.company_coins.add(newC);
									}
								}
							',
                    'params' => [
                        'newCompanyCoins' => $newCompanyCoins,
                        'updated_at' => now()->toIso8601String(),
                    ],
                ],
                'upsert' => [
                    'company_coins' => $newCompanyCoins,
                    'updated_at' => now()->toIso8601String(),
                ],
            ],
        ]);

    }

    public static function agencyCount($company_id, $job_id, $status)
    {
        $elasticService = app(ElasticService::class);

        $rcommendationIds = $elasticService->getClient()->search([
            'index' => ELASTIC_JOB_REQUEST,
            'body' => [
                'size' => 10000,
                'query' => [
                    'bool' => [
                        'must' => [
                            ['term' => ['from_company_id' => $company_id]],
                            ['term' => ['job_id' => $job_id]],
                            ['term' => ['to_status' => $status]],
                        ],
                    ],
                ],
                '_source' => [
                    'job_id',
                    'company_name_to',
                    'logo_to',
                    'to_company_id',
                    'final_status',
                ],
            ],
        ]);

        // Full records with unique job_id
        return collect($rcommendationIds['hits']['hits'])
            ->pluck('_source')
            ->values()
            ->toArray();
        // Extract company IDs

    }

    public static function addUpdateCompanyStaffingRecommendationRelations($company_id)
    {
        $elasticService = app(ElasticService::class);
        $staffing_relations = DB::table('staffing_company_relation')
            ->where('final_status', 1)
            ->where(function ($q) use ($company_id) {
                $q->where('from_company_id', $company_id)
                    ->orWhere('to_company_id', $company_id);
            })
            ->get()
            ->map(fn ($row) => (array) $row)
            ->toArray();

        $recommendation_relations = DB::table('recommendation_relation')
            ->where('final_status', 1)
            ->where(function ($q) use ($company_id) {
                $q->where('from_company_id', $company_id)
                    ->orWhere('to_company_id', $company_id);
            })
            ->get()
            ->map(fn ($row) => (array) $row)
            ->toArray();

        $response = $elasticService->getClient()->update([
            'index' => ELASTIC_COMPANIES_INDEX,
            'id' => (int) $company_id,
            'retry_on_conflict' => 5,
            'body' => [
                'script' => [
                    'lang' => 'painless',
                    'source' => '
									ctx._source.updated_at = params.updated_at;

									/* ================= STAFFING RELATIONS ================= */
									if (ctx._source.staffing_company_relation == null) {
										ctx._source.staffing_company_relation = [];
									}

									Map staffMap = new HashMap();
									for (s in ctx._source.staffing_company_relation) {
										staffMap.put(s.id, s);
									}

									for (ns in params.staffing_company_relation) {
										staffMap.put(ns.id, ns);   // overwrite or insert
									}

									ctx._source.staffing_company_relation = new ArrayList(staffMap.values());

									/* ============== RECOMMENDATION RELATIONS =============== */
									if (ctx._source.recommendation_relation == null) {
										ctx._source.recommendation_relation = [];
									}

									Map recoMap = new HashMap();
									for (r in ctx._source.recommendation_relation) {
										recoMap.put(r.id, r);
									}

									for (nr in params.recommendation_relation) {
										recoMap.put(nr.id, nr);   // overwrite or insert
									}

									ctx._source.recommendation_relation = new ArrayList(recoMap.values());
								',
                    'params' => [
                        'staffing_company_relation' => $staffing_relations,
                        'recommendation_relation' => $recommendation_relations,
                        'updated_at' => now()->toIso8601String(),
                    ],
                ],
                'upsert' => [
                    'staffing_company_relation' => $staffing_relations,
                    'recommendation_relation' => $recommendation_relations,
                    'updated_at' => now()->toIso8601String(),
                ],
            ],
        ]);

    }

    public static function extractTextFromUrl($fileUrl)
    {
        $ext = strtolower(pathinfo(parse_url($fileUrl, PHP_URL_PATH), PATHINFO_EXTENSION));
        $tempPath = storage_path('app/temp_resume.'.$ext);

        try {
            // Download file
            $fileContent = @file_get_contents($fileUrl);
            if ($fileContent === false || strlen($fileContent) < 100) {
                return null; // Invalid file or too small to be a real document
            }

            file_put_contents($tempPath, $fileContent);
        } catch (\Exception $e) {
            return null;
        }

        try {
            if ($ext === 'pdf') {
                $parser = new Parser;
                $pdf = $parser->parseFile($tempPath);
                $text = trim($pdf->getText());

                return $text !== '' ? $text : null;
            }

            if (in_array($ext, ['docx', 'doc'])) {
                $phpWord = IOFactory::load($tempPath);
                $text = '';

                foreach ($phpWord->getSections() as $section) {
                    foreach ($section->getElements() as $element) {
                        if ($element instanceof Text) {
                            $text .= $element->getText()."\n";
                        } elseif ($element instanceof TextRun) {
                            foreach ($element->getElements() as $subElement) {
                                if ($subElement instanceof Text) {
                                    $text .= $subElement->getText();
                                }
                            }
                            $text .= "\n";
                        }
                    }
                }

                $text = trim($text);

                return $text !== '' ? $text : null;
            }
        } catch (\Exception $e) {
            return null; // Parsing failed
        }

        return null; // Unsupported format
    }

    public static function careerStore($getId)
    {
        $response = '';
        $elasticService = app(ElasticService::class);

        $id = $getId;
        $applications = JobApplication::select(
            'job_applications.id',
            'job_applications.thirdparty_company_id',
            'job_applications.full_name',
            'job_applications.email',
            'job_applications.phone',
            'job_applications.np_id',
            'job_applications.company_id',
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
            'job_applications.company_id as other_company',
            'job_applications.outsourcing_type',
            'job_applications.pay_rate_currency',
            'job_applications.pay_rate_amount',
            'job_applications.pay_rate_mode',
            'job_applications.deleted_at',
            'job_applications.updated_at',
            'job_applications.created_at',
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
        )
            ->join('master_status', function ($join) {
                $join->on('master_status.id', 'job_applications.master_status_id');
            })
            ->leftJoin('jobs', function ($join) {
                $join->on('jobs.id', 'job_applications.job_id');
            })
            ->leftJoin('recommendation_relation', function ($join) {
                $join->on('recommendation_relation.job_id', 'jobs.id');
            })
            // ->leftJoin('recommendation_relation', 'recommendation_relation.job_id', '=', 'jobs.id')
            ->leftJoin('companies', function ($join) {
                $join->on('companies.id', 'job_applications.company_id');
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
            // ->leftJoin('jobs', 'jobs.id', '=', 'job_applications.job_id')
            ->leftjoin('clients', 'clients.id', 'jobs.client_id')
            // ->leftjoin('notice_periods', 'notice_periods.np_id', 'job_applications.np_id')
            ->leftjoin('job_locations', 'job_locations.id', 'jobs.location_id')
            ->leftjoin('users', 'users.id', 'job_applications.submitted_by')
            ->leftjoin('documents', 'documents.documentable_id', 'job_applications.id')
            ->leftjoin('qualification_master', 'qualification_master.id', 'job_applications.qualification')
            // ->leftJoin('master_status', 'master_status.id', '=', 'job_applications.master_status_id')
            ->leftJoin('countries', 'countries.id', '=', 'job_applications.country')
            ->leftJoin('state_list', 'state_list.id', '=', 'job_applications.state')
            ->leftJoin('city_list', 'city_list.city_id', '=', 'job_applications.city_id')
            ->where('job_applications.id', $id)->get();

        // dd($applications);

        $bulkData = [];

        foreach ($applications as $application) {
            $wiseId = $application->id;

            $logos = DB::table('companies')
                ->select('companies.*', 'clients.logo_logical_path', 'jobs.total_positions', 'jobs.company_id as companyChech', 'clients.name as clients_name')
                ->join('jobs', 'companies.id', 'jobs.company_id')
                ->join('clients', 'clients.id', 'jobs.client_id')->where('jobs.client_id', $application->client_id)->first();
            // dd($logos);
            $client_name_com = '';
            $client_name = @$logos->clients_name;
            if (@$logos->companyChech != $application->company_id) {
                $client_name = @$logos->company_name;
                $client_name = ucwords(substr(@$client_name, 0, 30));
                if (@$logos->companyChech != $application->company_id) {
                    $client_name = @$logos->company_name;
                    $client_name = ucwords(substr(@$client_name, 0, 30));
                }

            }
            $client_name_com = $client_name;

            $intRow = DB::table('interview_schedules')
                ->select('*')
                ->where('job_application_id', $application->id)
                ->where('interview_job_id', $application->jobId)
                ->orderBy('interview_schedules.id', 'DESC')
                ->first();

            $jobSkill = DB::table('job_metronics_skills')
                ->leftJoin('candidate_job_skill_exp', function ($join) use ($application) {
                    $join->on('candidate_job_skill_exp.job_skill_id', '=', 'job_metronics_skills.id')
                        ->where('candidate_job_skill_exp.candidate_id', '=', $application->id); // Add the candidate condition
                })
                ->select('job_metronics_skills.*', 'candidate_job_skill_exp.skill_value')
                ->where('job_metronics_skills.job_id', '=', $application->jobId)
                ->get();

            // dd($jobSkill);

            if (count($jobSkill) > 0) {
                foreach ($jobSkill as $sval) {

                    $skillValue = str_replace(['Years', 'Year', ' '], '', $sval->skill_value);
                    // dd($skillValue);
                    if (! is_null($skillValue) && ! empty($skillValue) && $skillValue != '0.') {
                        $values = [
                            'job_application_id' => $application->id,
                            'skills' => $sval->skill_name,
                        ];
                        if (! DB::table('job_application_skills')->where('job_application_id', '=', $application->id)->where('skills', 'LIKE', '%'.$sval->skill_name.'%')->exists()) {
                            DB::table('job_application_skills')->insert($values);
                        }
                    }

                }
            }

            $job_application_skills = DB::table('job_application_skills')
                ->where('job_application_id', $application->id)
                ->get()
            // ->map(fn($row) => (array) $row)
                ->map(function ($s) {
                    return [
                        'id' => $s->id,
                        'job_application_id' => $s->id,
                        'skills' => strtolower($s->skills),
                    ];
                })
                ->toArray();
            $application_skills = DB::table('job_application_skills')
                ->where('job_application_id', $application->id)
                ->pluck('skills') // only take skills column
                ->map(fn ($skill) => strtolower($skill))
                ->implode(',');

            $response = $elasticService->getClient()->update([
                'index' => ELASTIC_CAREER_APPLY,
                'id' => $wiseId,
                'retry_on_conflict' => 5,
                'body' => [
                    'doc' => [
                        'id' => $wiseId,
                        'candidate_id' => $application->id,
                        'thirdparty_company_id' => $application->thirdparty_company_id,
                        'full_name' => strtolower($application->full_name),
                        'email' => $application->email,
                        'phone' => $application->phone,
                        'np_id' => $application->np_id,
                        'immediate_join' => $application->np_id,
                        'status_type' => 'DIRECT',
                        'serving_notice_period_mode' => $application->serving_notice_period_mode,
                        'country_name' => strtolower($application->country_name),
                        'state_name' => strtolower($application->state_name),
                        'city_name' => strtolower($application->city_name),
                        'city_id' => $application->city_id,
                        'skills' => $application_skills,
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
                        'resume' => $application->resume,
                        'rating' => $application->rating,
                        'skype_id' => $application->skype_id,
                        'cover_letter' => $application->cover_letter,
                        'passport' => $application->passport,
                        'updated_at' => ($application->updated_at) ? Carbon::parse($application->updated_at)->toIso8601String() : null,
                        'created_at' => ($application->created_at) ? Carbon::parse($application->created_at)->toIso8601String() : null,
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
                    ],
                    'doc_as_upsert' => true,
                    'detect_noop' => false,
                ],
            ]);

        }

        return $response;
    }

    public static function getUniqueCvName($fullName, $extension)
    {
        $prefix = '1';   // fixed prefix (5 chars)
        $maxTotal = 15;        // total max length

        // Clean name (remove spaces)
        $cleanName = preg_replace('/\s+/', '', $fullName);

        // default basePart with max length (for first check without counter)
        $basePart = substr($cleanName, 0, $maxTotal - strlen($prefix));
        $matchPrefix = substr($fullName, 0, 5);
        // Find last record only for this basePart + extension
        $lastRecord = DB::table('cv_check')
            ->orderByDesc('id')
            ->first();

        // counter logic
        $counter = $lastRecord ? $lastRecord->count_cv + 1 : 1;

        // Adjust max length depending on digits
        $digits = $counter > 1 ? strlen((string) $counter) : 0;
        $maxNameLen = $maxTotal - strlen($prefix) - $digits;
        if ($maxNameLen < 1) {
            $maxNameLen = 1;
        }

        $basePart = Str::random(15); // Str::random(10);

        // build cvName (first record has no number)
        $cvName = $basePart;

        // ✅ insert with correct cvName
        DB::table('cv_check')->insert([
            'name' => $fullName,
            'cv_name' => $cvName,
            'cv_extension' => $extension,
            'count_cv' => $counter,
        ]);

        return $cvName;
    }

    public static function getDomain($url)
    {

        if (! preg_match('~^(?:f|ht)tps?://~i', $url)) {
            $url = 'http://'.$url;
        }
        $host = parse_url($url, PHP_URL_HOST);

        return preg_replace('/^www\./', '', $host);
    }

    public static function getDomainIcon($url, $id, $status)
    {
        $elasticService = app(ElasticService::class);
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_NOBODY => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 5,
            CURLOPT_FOLLOWLOCATION => true,
        ]);
        curl_exec($ch);
        $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http == 200) {
            if ($status) {
                $response = $elasticService->getClient()->update([
                    'index' => ELASTIC_CLIENT_LIST,
                    'id' => $id,
                    'retry_on_conflict' => 5,
                    'body' => [
                        'doc' => [
                            'id' => $id,
                            'logo' => $url,
                        ],
                        'doc_as_upsert' => true,
                        'detect_noop' => false,
                    ],
                ]);
                DB::table('clients')->where('id', $id)->update(['logo' => $url]);

            } else {
                $response = $elasticService->getClient()->update([
                    'index' => ELASTIC_CLIENT_LIST,
                    'id' => $id,
                    'retry_on_conflict' => 5,
                    'body' => [
                        'doc' => [
                            'id' => $id,
                            'logo_logical_path' => $url,
                        ],
                        'doc_as_upsert' => true,
                        'detect_noop' => false,
                    ],
                ]);
                DB::table('clients')->where('id', $id)->update(['logo_logical_path' => $url]);

            }
        }

        return $http == 200;

    }

    public static function getDomainIconCompany($url, $id, $status)
    {
        $elasticService = app(ElasticService::class);
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_NOBODY => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 5,
            CURLOPT_FOLLOWLOCATION => true,
        ]);
        curl_exec($ch);
        $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $response = $elasticService->getClient()->update([
            'index' => ELASTIC_COMPANIES_INDEX,
            'id' => $id,
            'retry_on_conflict' => 5,
            'body' => [
                'doc' => [
                    'id' => $id,
                    'logo' => $url,
                ],
                'doc_as_upsert' => true,
                'detect_noop' => false,
            ],
        ]);
        DB::table('companies')->where('id', $id)->update(['logo' => $url]);

        $intRow = self::getClientIdCompany($id);

        if ($intRow) {
            $response = $elasticService->getClient()->update([
                'index' => ELASTIC_CLIENT_LIST,
                'id' => $intRow->client_id,
                'retry_on_conflict' => 5,
                'body' => [
                    'doc' => [
                        'id' => $id,
                        'logo' => $url,
                    ],
                    'doc_as_upsert' => true,
                    'detect_noop' => false,
                ],
            ]);
            DB::table('clients')->where('id', $intRow->client_id)->update(['logo' => $url]);

        }

        return $http == 200;

    }

    public static function getClientIdCompany($company_id)
    {
        $elasticService = app(ElasticService::class);

        $params = [
            'index' => ELASTIC_CLIENT_LIST,
            'body' => [
                'size' => 1,
                'query' => [
                    'bool' => [
                        'must' => [
                            ['term' => ['company_id' => $company_id]],
                            ['term' => ['self_company' => 1]],
                        ],
                    ],
                ],
                'sort' => [],
            ],
        ];
        $result = $elasticService->getClient()->search($params);
        $intRow = isset($result['hits']['hits'][0]) ? (object) $result['hits']['hits'][0]['_source'] : null;

        return $intRow ?? null;

    }

    // Expands a skill into a match_phrase_prefix query for itself, OR (if
    // it has known synonyms in config/skill_synonyms.php) a nested bool
    // "should" covering every variant — a candidate matching ANY variant
    // satisfies this one skill slot. match_phrase_prefix (not
    // match_phrase): a job requiring "Java" was not matching a candidate
    // whose stored skill value is the compound phrase "Java Developer" —
    // match_phrase requires an exact phrase match, and "java" alone isn't
    // the same phrase as "java developer". match_phrase_prefix matches
    // "java" as a completable prefix wherever it occurs, so "Java
    // Developer" / "Senior Java Developer" now correctly match. It's also
    // the efficient choice: Lucene can use the term dictionary's sorted
    // prefix range directly, unlike a *java*-style wildcard (full
    // field-value scan, forbidden by docs/UI_UX_STANDARDS.md for exactly
    // this performance reason).
    //
    // Shared between ServiceCompanyAppTrackerController::
    // similar_existing_data_ajax() (the actual results query) and
    // custome_count_all() below (the header tab counters shown on every
    // AppTracker page) specifically so the two can never drift out of
    // sync again — they did drift once already (skill matching, location
    // clustering, and salary-missing-data handling were all improved in
    // the results query without this counter being updated to match,
    // which is why the same job could show very different "Similar
    // Candidates" numbers depending on which tab you were viewing it
    // from).
    // Mirrors buildSkillMatchClause()'s match_phrase_prefix semantics in
    // PHP: a required skill counts as matched when its words appear as a
    // consecutive prefix match within the candidate's skill text — e.g.
    // required "java" matches a candidate skill entry of "Java Developer"
    // or "Sr. Java Fullstack Developer", not only an exact "Java". The
    // candidate-list endpoints used to compute matched_skills with a plain
    // array_intersect() (exact string equality), so a candidate ES itself
    // had already returned via this same phrase-prefix query would show up
    // in results with NONE of their skill tags highlighted as matched.
    public static function skillMatchesPhrasePrefix(string $requiredSkill, string $candidateSkill): bool
    {
        $requiredWords = preg_split('/[\s\/,\-]+/', strtolower(trim($requiredSkill)), -1, PREG_SPLIT_NO_EMPTY);
        $candidateWords = preg_split('/[\s\/,\-]+/', strtolower(trim($candidateSkill)), -1, PREG_SPLIT_NO_EMPTY);
        $wordCount = count($requiredWords);
        if ($wordCount === 0 || count($candidateWords) < $wordCount) {
            return false;
        }

        for ($i = 0; $i <= count($candidateWords) - $wordCount; $i++) {
            $isMatch = true;
            foreach ($requiredWords as $idx => $word) {
                $candidateWord = $candidateWords[$i + $idx];
                // Every word must match exactly except the LAST word of the
                // required skill, which only needs to be a prefix — same as
                // match_phrase_prefix treating the final query token as a
                // prefix search.
                $isLastWord = $idx === $wordCount - 1;
                if ($isLastWord ? ! str_starts_with($candidateWord, $word) : $candidateWord !== $word) {
                    $isMatch = false;
                    break;
                }
            }
            if ($isMatch) {
                return true;
            }
        }

        return false;
    }

    // Returns the subset of $candidateSkills that phrase-prefix-match any
    // entry in $requiredSkills — the "which of this candidate's skills
    // satisfy the job's requirements" computation every AppTracker tab's
    // results endpoint needs for its Match % badge and skill highlighting
    // (see skillMatchesPhrasePrefix() above). Preserves each matched
    // candidate skill's original wording/casing (callers ucfirst() it for
    // display), only $requiredSkills is expected pre-lowercased.
    public static function matchedSkillsList(array $candidateSkills, array $requiredSkills): array
    {
        $matched = [];
        foreach ($candidateSkills as $candidateSkill) {
            foreach ($requiredSkills as $requiredSkill) {
                if (self::skillMatchesPhrasePrefix($requiredSkill, $candidateSkill)) {
                    $matched[] = $candidateSkill;
                    break;
                }
            }
        }

        return array_values(array_unique($matched, SORT_STRING));
    }

    public static function buildSkillMatchClause(string $skill): array
    {
        $synonymMap = config('skill_synonyms', []);
        $variants = array_values(array_unique(array_merge([$skill], $synonymMap[$skill] ?? [])));

        if (count($variants) === 1) {
            return [
                'match_phrase_prefix' => [
                    'job_application_skills.skills' => $variants[0],
                ],
            ];
        }

        return [
            'bool' => [
                'should' => array_map(function ($variant) {
                    return [
                        'match_phrase_prefix' => [
                            'job_application_skills.skills' => $variant,
                        ],
                    ];
                }, $variants),
                'minimum_should_match' => 1,
            ],
        ];
    }

    // Given the exact city_ids the recruiter selected (or, for the header
    // counters, the job's own default location) and the raw city names
    // those selections resolved from, widens the location filter to
    // include every other city in the same metro cluster (see
    // config/location_clusters.php) — e.g. selecting/defaulting to Gurgaon
    // also pulls in Noida/Delhi/Faridabad/Ghaziabad/Greater Noida
    // candidates. Returns both sets so the caller can filter on 'expanded'
    // while still ranking 'exact' first via a priority sort. If no
    // selected city belongs to a configured cluster, 'expanded' just
    // equals 'exact' — unchanged original behaviour. See
    // buildSkillMatchClause() above for why this lives here shared, not on
    // the controller.
    public static function resolveLocationCluster(array $exactCityIds, array $cityNames): array
    {
        $clusters = config('location_clusters', []);
        if (empty($clusters) || empty($cityNames)) {
            return ['exact' => $exactCityIds, 'expanded' => $exactCityIds];
        }

        $matchedFragments = [];
        foreach ($cityNames as $cityName) {
            $lower = strtolower($cityName);
            foreach ($clusters as $cluster) {
                foreach ($cluster['city_fragments'] as $fragment) {
                    if (str_contains($lower, $fragment)) {
                        $matchedFragments = array_merge($matchedFragments, $cluster['city_fragments']);

                        continue 2;
                    }
                }
            }
        }

        if (empty($matchedFragments)) {
            return ['exact' => $exactCityIds, 'expanded' => $exactCityIds];
        }

        $matchedFragments = array_values(array_unique($matchedFragments));

        $clusterCityIds = DB::table('city_list')
            ->where('city', '!=', '')
            ->where(function ($q) use ($matchedFragments) {
                foreach ($matchedFragments as $fragment) {
                    $q->orWhere('city', 'LIKE', '%'.$fragment.'%');
                }
            })
            ->pluck('city_id')
            ->map(function ($id) {
                return (int) $id;
            })
            ->toArray();

        return [
            'exact' => $exactCityIds,
            'expanded' => array_values(array_unique(array_merge($exactCityIds, $clusterCityIds))),
        ];
    }

    public static function custome_count_all($id)
    {
        $company_id = auth()->user()->company_id;

        // BUG-05: stale-while-revalidate — serve cached counters immediately, refresh in background
        return Cache::flexible("custome_count_all:{$company_id}:{$id}", [60, 300], function () use ($id, $company_id) {
            return self::custome_count_all_compute($id, $company_id);
        });
    }

    private static function custome_count_all_compute(int $id, int $company_id): object
    {
        $elasticService = app(ElasticService::class);

        // Same shared ES-only job lookup every tab page's default-filter
        // population uses (see jobDefaultsFromEs() above) — jobEsDoc is the
        // raw ELASTIC_JOBS _source, kept under this same $jobs_details name
        // so the rest of this (large, multi-counter) function is unchanged.
        // false: this function only ever uses raw city_ids in ES filters
        // below, never the resolved "City | State | Country" display
        // strings, so skip that extra MySQL join.
        $jobs_details = self::jobDefaultsFromEs($id, false)->jobEsDoc;
        //  dd($jobs_details);
        $min_exp = ($jobs_details->min_exp >= 1) ? $jobs_details->min_exp - 1 : $jobs_details->min_exp;
        $max_exp = $jobs_details->max_exp + 1;
        $min_salary_currency = $jobs_details->min_salary_currency;
        $min_salary = $jobs_details->min_salary;
        $max_salary = $jobs_details->max_salary;
        $sal_mode = $jobs_details->min_sal_mode;
        $currency = self::currency();  // Currency code

        $currencyCheck = $currency[$min_salary_currency];

        // dd($currencyCheck,$min_salary_currency);
        $convertedAmount = $max_salary; // $job->max_salary;
        if ($min_salary_currency > 0) {
            $max_salary = self::convertToINR($max_salary, $currencyCheck);
            $min_salary = self::convertToINR($min_salary, $currencyCheck);
        }
        // $ectcdivided = self::salaryDividedByMode($sal_mode, $convertedAmount);
        // //  dd($ectcdivided);
        // if ($sal_mode == 0) {
        // 	$max_salary = $ectcdivided->yearly;
        // } elseif ($sal_mode == 1) {
        // 	$max_salary = $ectcdivided->monthly;

        // } elseif ($sal_mode == 2) {
        // 	$max_salary = $ectcdivided->hourly;

        // }

        // 		dd($jobs_details);

        $education = [$jobs_details->qualification];
        $educationfiletr = self::qualificationFilterCondition($jobs_details->qualification);
        $education = array_unique(array_merge($educationfiletr, $education));
        $skill_arr = [];
        $skill_data = $jobs_details->job_metronics_skills;
        foreach ($skill_data as $sval) {
            $sval = (object) $sval;
            $skill_arr[] = $sval->skill_name;
        }
        $requiredSkills = array_map('strtolower', $skill_arr);

        $location_arr = [];
        $location_data = $jobs_details->client_city_jobs;
        // dd($location_data);
        foreach ($location_data as $sval) {
            $sval = (object) $sval;
            $location_arr[] = $sval->city_id;
        }
        $location = $location_arr;

        // Same Delhi NCR-style metro cluster expansion
        // similar_existing_data_ajax() applies to the actual results (see
        // resolveLocationCluster() above) — without this, this counter used
        // only the job's exact default city while the results page (once a
        // clustered city like Gurgaon is involved) counted every city in
        // the cluster, which is the other half of why the two numbers
        // diverged. city_list has no name on the job's own city_id list, so
        // resolve it first purely to determine cluster membership.
        $exactLocationIds = array_map('intval', $location);
        $locationCityNames = ! empty($exactLocationIds)
            ? DB::table('city_list')->whereIn('city_id', $exactLocationIds)->pluck('city')->toArray()
            : [];
        $locationCluster = self::resolveLocationCluster($exactLocationIds, $locationCityNames);
        $location = $locationCluster['expanded'];

        $existingCandidatesQuery = DB::table('add_job_wise_condidates')
            ->where('job_id', '=', $id)->pluck('job_application_id')->toArray();

        // dd($education);

        // ####################################   START  POOL APPLICATION COUNTER

        $boolQueryPool = [
            'bool' => [
                'should' => [
                    // First condition
                    [
                        'bool' => [
                            'must' => array_merge([
                                ['term' => ['company_id' => $company_id]],
                                ['term' => ['jobId' => $id]],
                                ['term' => ['ClientComID' => 0]],
                            ]),
                        ],
                    ],
                    // Second condition
                    [
                        'bool' => [
                            'must' => array_merge([
                                ['term' => ['company_id' => $company_id]],
                                ['term' => ['jobId' => $id]],
                                ['range' => ['ClientComID' => ['gt' => 0]]],
                            ], [
                                ['bool' => [
                                    'must_not' => [
                                        ['terms' => ['job_master_status_id' => [8, 9]]],
                                    ],
                                ]],
                            ]),
                        ],
                    ],
                    // Third condition
                    [
                        'bool' => [
                            'must' => array_merge([
                                ['term' => ['ClientComID' => $company_id]],
                                ['term' => ['jobId' => $id]],
                                ['range' => ['job_id' => ['gt' => 0]]],
                            ]),
                        ],
                    ],
                ],
                'minimum_should_match' => 1,
                'filter' => [], // We'll add all filters here
            ],
        ];

        // dd($boolQuery);

        $total_pool = 0;

        // ####################################   END   POOL APPLICATION COUNTER (executed via msearch below)

        // ####################################   START  EXISTING APPLICATION COUNTER

        $boolQuery = [
            'bool' => [
                'should' => [
                    // First condition
                    [
                        'bool' => [
                            'must' => array_merge([
                                ['term' => ['company_id' => $company_id]],
                                ['term' => ['condidate_type' => 'other']],
                                ['term' => ['ClientComID' => 0]],
                            ]),
                        ],
                    ],
                    // Second condition
                    [
                        'bool' => [
                            'must' => array_merge([
                                ['term' => ['company_id' => $company_id]],
                                ['term' => ['condidate_type' => 'other']],
                                ['range' => ['ClientComID' => ['gt' => 0]]],
                                ['range' => ['job_id' => ['gt' => 0]]],
                            ], [
                                ['bool' => [
                                    'must_not' => [
                                        ['terms' => ['job_master_status_id' => [8, 9]]],
                                    ],
                                ]],
                            ]),
                        ],
                    ],
                    // Third condition
                    [
                        'bool' => [
                            'must' => array_merge([
                                ['term' => ['ClientComID' => $company_id]],
                                ['term' => ['condidate_type' => 'other']],
                                ['range' => ['job_id' => ['gt' => 0]]],
                            ]),
                        ],
                    ],
                ],
                'minimum_should_match' => 1,
                'filter' => [], // We'll add all filters here
                'must_not' => [
                    ['term' => ['job_master_status_id' => 5]],
                    ['term' => ['job_master_status_id' => 6]],
                ],
            ],
        ];
        // dd($requiredSkills);

        // Same match_phrase_prefix-based matching similar_existing_data_ajax()
        // uses for the actual results — see buildSkillMatchClause() above for
        // why (a job requiring "Java" needs to match a candidate whose skill
        // is stored as "Java Developer"). This counter has no concept of a
        // mandatory/starred skill (it only knows the job's own default skill
        // list), so every skill stays an OR condition — unchanged structure,
        // just the corrected match type.
        if (! empty($requiredSkills)) {
            if (! empty($requiredSkills)) {
                $should = [];

                foreach ($requiredSkills as $skill) {
                    $should[] = self::buildSkillMatchClause($skill);
                }

                $boolQuery['bool']['filter'][] = [
                    'bool' => [
                        'should' => $should,
                        'minimum_should_match' => 1,
                    ],
                ];
            }
        }

        $boolQuery['bool']['must_not'][] = [
            'terms' => [
                'job_master_status_id' => [5, 6],
            ],
        ];

        if (! empty($existingCandidatesQuery)) {
            $boolQuery['bool']['must_not'][] = [
                'terms' => [
                    'candidate_id' => $existingCandidatesQuery,
                ],
            ];
        }

        if (! empty($location)) {

            $boolQuery['bool']['filter'][] = [
                'terms' => [
                    'city_id' => array_map('intval', $location),
                ],
            ];
            $boolQuery['bool']['filter'][] = [
                'exists' => [
                    'field' => 'city_id',
                ],
            ];
            $boolQuery['bool']['filter'][] = [
                'range' => [
                    'city_id' => [
                        'gt' => 0,
                    ],
                ],
            ];

        }

        // if (!empty($min_exp) || !empty($max_exp)) {

        // 	$range = [];

        // 	// Minimum experience
        // 	if (!empty($min_exp)) {
        // 		$range['gte'] = (float) $min_exp;
        // 	}

        // 	// Maximum experience
        // 	if (!empty($max_exp)) {
        // 		$range['lte'] = (float) $max_exp;
        // 	}

        // 	// Add filter query
        // 	$boolQuery['bool']['filter'][] = [
        // 		'range' => [
        // 			'total_exp' => $range
        // 		]
        // 	];
        // }

        if ($min_exp !== null || $max_exp !== null) {

            $range = [];

            // Minimum experience
            if ($min_exp !== '' && $min_exp !== null) {
                $range['gte'] = (float) $min_exp;
            }

            // Maximum experience
            if ($max_exp !== '' && $max_exp !== null) {
                $range['lte'] = (float) $max_exp;
            }

            // Add filter query
            $boolQuery['bool']['filter'][] = [
                'range' => [
                    'total_exp' => $range,
                ],
            ];
        }

        $salaryField = match ($sal_mode) {
            0 => 'ectc_yearly',
            1 => 'ectc_monthly',
            2 => 'ectc_hourly',
            default => 'ectc_yearly'
        };

        // similar_candidates.blade.php's filter drawer hardcodes its Min
        // Salary field to 0 (not the job's own min_salary) — every other
        // tab's older-style filter modal (internal_bench/public_bench)
        // defaults that field to the job's real min_salary instead. This
        // counter has to match whichever floor the CORRESPONDING live page
        // actually applies on load, not the job's raw value uniformly, or
        // this counter and the Similar Candidates page disagree the moment
        // you land on either one — same 0 floor as the live page, only for
        // this block.
        $minSalaryForThisCounter = 0;

        if ((! empty($minSalaryForThisCounter) && $minSalaryForThisCounter != 0.00) || (! empty($max_salary) && $max_salary != 0.00)) {

            $salaryRange = [];

            if (! empty($minSalaryForThisCounter)) {
                $salaryRange['gte'] = $minSalaryForThisCounter;
            }

            if (! empty($max_salary)) {
                $salaryRange['lte'] = $max_salary;
            }

            // Same missing-salary-inclusive handling
            // similar_existing_data_ajax() uses for the actual results: a
            // plain range filter only matches documents that HAVE the
            // field, so a candidate missing salary data was silently
            // excluded from this counter (but NOT from the results page
            // after that fix), which was the third source of the two
            // numbers disagreeing.
            $boolQuery['bool']['filter'][] = [
                'bool' => [
                    'should' => [
                        ['range' => [$salaryField => $salaryRange]],
                        ['bool' => ['must_not' => [['exists' => ['field' => $salaryField]]]]],
                    ],
                    'minimum_should_match' => 1,
                ],
            ];
        }

        $total_existingApplications = 0;

        // ####################################   END   EXISTING APPLICATION COUNTER (executed via msearch below)

        // ####################################   START  Internal Bench APPLICATION COUNTER

        $boolQueryInternalBench = [
            'bool' => [
                'must' => [
                    // First condition
                    [
                        'bool' => [
                            'must' => array_merge([
                                ['term' => ['company_id' => $company_id]],
                                ['term' => ['condidate_type' => 'bench_condidate']],
                                ['term' => ['status' => 2]],
                            ]),
                        ],
                    ],
                ],
                'filter' => [], // We'll add all filters here
                'must_not' => [],
            ],
        ];

        if (! empty($requiredSkills)) {
            if (! empty($requiredSkills)) {
                $should = [];

                foreach ($requiredSkills as $skill) {
                    $should[] = [
                        'match_phrase' => [
                            'job_application_skills.skills' => $skill,
                        ],
                    ];
                }
                $boolQueryInternalBench['bool']['filter'][] = [
                    'bool' => [
                        'should' => $should,
                        'minimum_should_match' => 1,
                    ],
                ];
            }
        }

        if (! empty($location)) {

            $boolQueryInternalBench['bool']['filter'][] = [
                'terms' => [
                    'city_id' => array_map('intval', $location),
                ],
            ];
            $boolQueryInternalBench['bool']['filter'][] = [
                'exists' => [
                    'field' => 'city_id',
                ],
            ];
            $boolQueryInternalBench['bool']['filter'][] = [
                'range' => [
                    'city_id' => [
                        'gt' => 0,
                    ],
                ],
            ];

        }

        if ($min_exp !== null || $max_exp !== null) {

            $range = [];

            // Minimum experience
            if ($min_exp !== '' && $min_exp !== null) {
                $range['gte'] = (float) $min_exp;
            }

            // Maximum experience
            if ($max_exp !== '' && $max_exp !== null) {
                $range['lte'] = (float) $max_exp;
            }

            // Add filter query
            $boolQueryInternalBench['bool']['filter'][] = [
                'range' => [
                    'total_exp' => $range,
                ],
            ];
        }

        // if (!empty($min_exp) || !empty($max_exp)) {

        // 	$range = [];

        // 	// Minimum experience
        // 	if (!empty($min_exp)) {
        // 		$range['gte'] = (float) $min_exp;
        // 	}

        // 	// Maximum experience
        // 	if (!empty($max_exp)) {
        // 		$range['lte'] = (float) $max_exp;
        // 	}

        // 	// Add filter query
        // 	$boolQueryInternalBench['bool']['filter'][] = [
        // 		'range' => [
        // 			'total_exp' => $range
        // 		]
        // 	];
        // }

        $salaryField = match ($sal_mode) {
            0 => 'ectc_yearly',
            1 => 'ectc_monthly',
            2 => 'ectc_hourly',
            default => 'ectc_yearly'
        };

        // internal_bench.blade.php's filter drawer now defaults Min Salary
        // to 0 (not the job's own min_salary) — every AppTracker tab's Min
        // Salary field is 0 by default, same as similar_candidates.blade.php
        // (see custome_count_all()'s Existing counter block above), so this
        // counter has to use the same 0 floor as the live In-house Bench
        // page or the two disagree the moment you land on either one.
        $minSalaryForThisCounter = 0;

        if ((! empty($minSalaryForThisCounter) && $minSalaryForThisCounter != 0.00) || (! empty($max_salary) && $max_salary != 0.00)) {

            $salaryRange = [];

            if (! empty($minSalaryForThisCounter)) {
                $salaryRange['gte'] = $minSalaryForThisCounter;
            }

            if (! empty($max_salary)) {
                $salaryRange['lte'] = $max_salary;
            }

            $boolQueryInternalBench['bool']['filter'][] = [
                'range' => [
                    $salaryField => $salaryRange,
                ],
            ];
        }

        if (! empty($existingCandidatesQuery)) {
            $boolQueryInternalBench['bool']['must_not'][] = [
                'terms' => ['id' => $existingCandidatesQuery],
            ];
        }

        $total_InternalBenchApplications = 0;

        // ####################################   END Internal Bench APPLICATION COUNTER (executed via msearch below)

        // ####################################   START   CLIENT JOB APPLICATION COUNTER

        $boolQueryPoolClientJobApplication = [
            'bool' => [
                'should' => [
                    // First condition
                    [
                        'bool' => [
                            'must' => array_merge([
                                ['term' => ['ClientComID' => $company_id]],
                                ['term' => ['jobId' => $id]],
                            ]),
                        ],
                    ],
                ],
                'minimum_should_match' => 1,
                'filter' => [], // We'll add all filters here
            ],
        ];

        $total_pool_client_application = 0;

        // ####################################   END CLIENT JOB APPLICATION COUNTER (executed via msearch below)

        // ####################################   START   Market Bench APPLICATION COUNTER

        $boolQueryMarketBench = [
            'bool' => [
                'must' => [
                    // First condition
                    [
                        'bool' => [
                            'must' => array_merge([
                                ['term' => ['condidate_type' => 'bench_condidate']],
                                ['term' => ['status' => 2]],
                            ]),
                        ],
                    ],
                ],
                'filter' => [], // We'll add all filters here
                'must_not' => [
                    ['term' => ['company_id' => $company_id]],
                ],
            ],
        ];

        if (! empty($requiredSkills)) {
            if (! empty($requiredSkills)) {
                $should = [];

                foreach ($requiredSkills as $skill) {
                    $should[] = [
                        'match_phrase' => [
                            'job_application_skills.skills' => $skill,
                        ],
                    ];
                }
                $boolQueryMarketBench['bool']['filter'][] = [
                    'bool' => [
                        'should' => $should,
                        'minimum_should_match' => 1,
                    ],
                ];
            }
        }

        if (! empty($location)) {

            $boolQueryMarketBench['bool']['filter'][] = [
                'terms' => [
                    'city_id' => array_map('intval', $location),
                ],
            ];
            $boolQueryMarketBench['bool']['filter'][] = [
                'exists' => [
                    'field' => 'city_id',
                ],
            ];
            $boolQueryMarketBench['bool']['filter'][] = [
                'range' => [
                    'city_id' => [
                        'gt' => 0,
                    ],
                ],
            ];

        }

        // if (!empty($min_exp) || !empty($max_exp)) {

        // 	$range = [];

        // 	// Minimum experience
        // 	if (!empty($min_exp)) {
        // 		$range['gte'] = (float) $min_exp;
        // 	}

        // 	// Maximum experience
        // 	if (!empty($max_exp)) {
        // 		$range['lte'] = (float) $max_exp;
        // 	}

        // 	// Add filter query
        // 	$boolQueryMarketBench['bool']['filter'][] = [
        // 		'range' => [
        // 			'total_exp' => $range
        // 		]
        // 	];
        // }

        if ($min_exp !== null || $max_exp !== null) {

            $range = [];

            // Minimum experience
            if ($min_exp !== '' && $min_exp !== null) {
                $range['gte'] = (float) $min_exp;
            }

            // Maximum experience
            if ($max_exp !== '' && $max_exp !== null) {
                $range['lte'] = (float) $max_exp;
            }

            // Add filter query
            $boolQueryMarketBench['bool']['filter'][] = [
                'range' => [
                    'total_exp' => $range,
                ],
            ];
        }

        $salaryField = match ($sal_mode) {
            0 => 'ectc_yearly',
            1 => 'ectc_monthly',
            2 => 'ectc_hourly',
            default => 'ectc_yearly'
        };

        // public_bench.blade.php's filter drawer now defaults Min Salary to
        // 0 (not the job's own min_salary) — every AppTracker tab's Min
        // Salary field is 0 by default, same as similar_candidates.blade.php
        // (see custome_count_all()'s Existing counter block above), so this
        // counter has to use the same 0 floor as the live Agency Bench page
        // or the two disagree the moment you land on either one.
        $minSalaryForThisCounter = 0;

        if ((! empty($minSalaryForThisCounter) && $minSalaryForThisCounter != 0.00) || (! empty($max_salary) && $max_salary != 0.00)) {

            $salaryRange = [];

            if (! empty($minSalaryForThisCounter)) {
                $salaryRange['gte'] = $minSalaryForThisCounter;
            }

            if (! empty($max_salary)) {
                $salaryRange['lte'] = $max_salary;
            }

            $boolQueryMarketBench['bool']['filter'][] = [
                'range' => [
                    $salaryField => $salaryRange,
                ],
            ];
        }

        if (! empty($existingCandidatesQuery)) {
            $boolQueryMarketBench['bool']['must_not'][] = [
                'terms' => ['id' => $existingCandidatesQuery],
            ];
        }

        $total_MarketBenchApplications = 0;

        // ####################################   END Market Bench APPLICATION COUNTER (executed via msearch below)

        // ####################################  START  Bench Talent Request

        $boolQuerytBenchRequest = [
            'bool' => [
                'should' => [
                    [
                        'bool' => [
                            'must' => array_merge([
                                ['term' => ['source_company_id' => $company_id]],
                                ['term' => ['source_jobid' => $id]],
                            ]),
                        ],
                    ],
                ],
                'minimum_should_match' => 1,
                'must_not' => [
                    // Exclude already-added candidates from the tab counter too.
                    ['term' => ['ac_status' => 1]],
                ],
                'filter' => [],
            ],
        ];
        $total_RequestBenchApplications = 0;

        // ####################################  END  Bench Talent Request (executed via msearch below)

        // ####################################  START  CAREER COUNTER

        $boolQueryCareer = [
            'bool' => [
                'should' => [
                    // First condition
                    [
                        'bool' => [
                            'must' => array_merge([
                                ['term' => ['company_id' => $company_id]],
                                ['term' => ['job_id' => $id]],
                            ], [
                                ['bool' => [
                                    'must_not' => [
                                        ['term' => ['status' => 1]],
                                    ],
                                ]],
                            ]),
                        ],
                    ],
                ],
                'minimum_should_match' => 1,
                'filter' => [], // We'll add all filters here
                'must_not' => [],
            ],
        ];

        if (! empty($existingCandidatesQuery)) {
            $boolQueryCareer['bool']['must_not'][] = [
                'terms' => ['id' => $existingCandidatesQuery],
            ];
        }

        $total_career = 0;

        // ####################################  END  CAREER COUNTER (executed via msearch below)

        // Step-5 consolidation: send all 7 counter queries in one msearch round-trip.
        // Slot order: [0] pool, [1] existing, [2] internal_bench,
        //             [3] pool_client, [4] market_bench, [5] bench_request, [6] career
        $msearchBody = [
            ['index' => ELASTIC_CANDIDATES,         'request_cache' => true],
            ['size' => 0, 'query' => $boolQueryPool],

            ['index' => ELASTIC_CANDIDATES,         'request_cache' => true],
            ['size' => 0, 'query' => $boolQuery,
                'aggs' => ['uc' => ['cardinality' => ['field' => 'email']]]],

            ['index' => ELASTIC_CANDIDATES_BENCH,   'request_cache' => true],
            ['size' => 0, 'query' => $boolQueryInternalBench,
                'aggs' => ['uc' => ['cardinality' => ['field' => 'email']]]],

            ['index' => ELASTIC_CANDIDATES,         'request_cache' => true],
            ['size' => 0, 'query' => $boolQueryPoolClientJobApplication],

            ['index' => ELASTIC_CANDIDATES_BENCH,   'request_cache' => true],
            ['size' => 0, 'query' => $boolQueryMarketBench,
                'aggs' => ['uc' => ['cardinality' => ['field' => 'email']]]],

            ['index' => ELASTIC_CANDIDATES_REQUEST, 'request_cache' => true],
            ['size' => 0, 'query' => $boolQuerytBenchRequest],

            ['index' => ELASTIC_CAREER_APPLY,       'request_cache' => true],
            ['size' => 0, 'query' => $boolQueryCareer],
        ];

        try {
            $msResults = $elasticService->msearch($msearchBody)['responses'];

            $hits = fn (array $r): int => isset($r['error']) ? 0
                : (int) ($r['hits']['total']['value'] ?? 0);
            $card = fn (array $r): int => isset($r['error']) ? 0
                : (int) ($r['aggregations']['uc']['value'] ?? 0);

            $total_pool = $hits($msResults[0]);
            $total_existingApplications = $card($msResults[1]);
            $total_InternalBenchApplications = $card($msResults[2]);
            $total_pool_client_application = $hits($msResults[3]);
            $total_MarketBenchApplications = $card($msResults[4]);
            $total_RequestBenchApplications = $hits($msResults[5]);
            $total_career = $hits($msResults[6]);
        } catch (\Throwable $e) {
            \Log::warning('custome_count_all_compute msearch error', [
                'job_id' => $id,
                'error' => $e->getMessage(),
            ]);
            // Counters stay at 0 — the 60-300 s flexible cache will serve the last good value
        }

        $return = (object) [
            'existing_counter' => $total_existingApplications ? $total_existingApplications : 0,
            'internal_bench_counter' => $total_InternalBenchApplications ? $total_InternalBenchApplications : 0,
            'public_bench_counter' => $total_MarketBenchApplications ? $total_MarketBenchApplications : 0,
            'career_counter' => $total_career ? $total_career : 0,
            'pool_counter' => $total_pool ? $total_pool : 0,
            'pool_client_counter' => $total_pool_client_application ? $total_pool_client_application : 0,
            'recomnded_counter' => $total_RequestBenchApplications ? $total_RequestBenchApplications : 0,
            'jobclient' => 0,
        ];

        return $return;

    }

    public static function bench_status_subline($id)
    {

        $array = [
            1 => ['name' => 'No Project', 'classname' => 'text-Asourcing'],
            2 => ['name' => 'Open For Work', 'classname' => 'text-Ainterview'],
            3 => ['name' => 'Work Assigned', 'classname' => 'text-Ajoiner'],
            4 => ['name' => 'In Interview Pipelines', 'classname' => 'text-Aoffer'],
            5 => ['name' => 'Left the company', 'classname' => 'text-bench-left'],
        ];
        // If ID passed, return only that item
        if ($id !== null && isset($array[$id])) {
            return (object) $array[$id];
        }

        // Otherwise return full array
        return (object) $array;
    }

    public static function candidateDetailsJobWise($id)
    {

        $elasticService = app(ElasticService::class);
        $params = [
            'index' => ELASTIC_CANDIDATES,
            'id' => $id,
        ];

        $response = $elasticService->getClient()->get($params);
        $jobs_details = (object) $response['_source'];

        return $jobs_details;

    }

    public static function permissionRole($key = null)
    {

        $array = [
            'Clients' => ['name' => 'Clients'],
            'Jobs' => ['name' => 'Requisitions'],
            'Job Applications' => ['name' => 'Candidates'],
            'Schedule' => ['Interview Tracker'],
            'Staffing Partners' => ['Staffing Partners'],
            'Roles & Permissions' => ['Roles & Permissions'],
            'Team' => ['Team'],
            'Offer' => ['Offer Tracker'],
            'Joining' => ['Joining Tracker'],
            'Settings' => ['Manage Settings'],
            'Website' => ['Career Website'],
            'Subscription' => ['Subscription'],
            'Reports' => ['Reports'],
            'Bench' => ['Talent Marketplace'],
            'not_use' => ['not_use'],
            'not_use1' => ['not_use1'],
            'Salary Break' => ['Salary breakup'],
            'Salary' => ['name' => 'Salary view'],
        ];
        // If ID passed, return only that item
        if ($key !== null && isset($array[$key])) {
            $value = $array[$key]['name'] ?? $array[$key][0] ?? $key;

            // 🚨 force string return
            return is_string($value) ? $value : $key;
        }

        return $key;
    }

    /**
     * Fetch job details from Elasticsearch index (ELASTIC_JOBS) with MySQL fallback & memoization cache.
     * Tenancy / Resilience Note: Protects against missing ES documents, ES cluster outages, or invalid IDs.
     *
     * @param  int|string  $id
     * @return object
     */
    public static function jobDetails($id)
    {
        static $jobDetailsCache = [];

        $jId = (int) $id;
        if ($jId <= 0) {
            return (object) [
                'id' => 0,
                'title' => 'N/A',
                'comId' => 0,
                'client_name' => '',
                'logo' => '',
                'company_name' => '',
                'max_exp' => 0,
                'min_exp' => 0,
                'min_salary' => 0,
                'max_salary' => 0,
                'min_sal_mode' => 0,
                'max_sal_mode' => 0,
                'min_salary_currency' => 0,
                'max_salary_currency' => 0,
                'employment_type' => 0,
                'work_type' => 'no',
                'status' => 'inactive',
                'category_id' => 0,
                'client_city_jobs' => [],
                'client_contact_info_jobs' => [],
                'job_assign' => [],
                'job_metronics_skills' => [],
            ];
        }

        if (array_key_exists($jId, $jobDetailsCache)) {
            return $jobDetailsCache[$jId];
        }

        try {
            $elasticService = app(ElasticService::class);
            $params = [
                'index' => ELASTIC_JOBS,
                'id' => $jId,
            ];

            $response = $elasticService->getClient()->get($params);
            if (! empty($response['found']) && ! empty($response['_source'])) {
                $jobs_details = (object) $response['_source'];
                $jobDetailsCache[$jId] = $jobs_details;

                return $jobs_details;
            }
        } catch (\Throwable $e) {
            // Document not found in ES or ES unreachable — fall through to MySQL DB lookup
        }

        try {
            $dbJob = DB::table('jobs')
                ->leftJoin('companies', 'companies.id', '=', 'jobs.company_id')
                ->leftJoin('clients', 'clients.id', '=', 'jobs.client_id')
                ->select(
                    'jobs.*',
                    'jobs.id as id',
                    'jobs.company_id as comId',
                    'clients.name as client_name',
                    'companies.company_name',
                    'companies.logo'
                )
                ->where('jobs.id', $jId)
                ->first();

            if ($dbJob) {
                $dbJob->client_city_jobs = DB::table('client_city_jobs')
                    ->leftJoin('city_list', 'city_list.city_id', '=', 'client_city_jobs.city_id')
                    ->select('city_list.city as city_name')
                    ->where('client_city_jobs.job_id', $jId)
                    ->get()
                    ->toArray();

                $dbJob->client_contact_info_jobs = DB::table('client_contact_info_jobs')
                    ->where('job_id', $jId)
                    ->get()
                    ->toArray();

                $dbJob->job_assign = DB::table('job_assign')
                    ->leftJoin('users', 'users.id', '=', 'job_assign.user_id')
                    ->select('job_assign.*', 'users.name as user_name')
                    ->where('job_assign.job_id', $jId)
                    ->get()
                    ->toArray();

                $dbJob->job_metronics_skills = DB::table('job_metronics_skills')
                    ->select('skill_name')
                    ->where('job_id', $jId)
                    ->get()
                    ->toArray();

                $jobDetailsCache[$jId] = $dbJob;

                return $dbJob;
            }
        } catch (\Throwable $dbEx) {
            \Log::warning('jobDetails MySQL fallback exception for job_id '.$jId.': '.$dbEx->getMessage());
        }

        $defaultObj = (object) [
            'id' => $jId,
            'title' => 'Requisition #'.$jId,
            'comId' => 0,
            'client_name' => '',
            'logo' => '',
            'company_name' => '',
            'max_exp' => 0,
            'min_exp' => 0,
            'min_salary' => 0,
            'max_salary' => 0,
            'min_sal_mode' => 0,
            'max_sal_mode' => 0,
            'min_salary_currency' => 0,
            'max_salary_currency' => 0,
            'employment_type' => 0,
            'work_type' => 'no',
            'status' => 'inactive',
            'category_id' => 0,
            'client_city_jobs' => [],
            'client_contact_info_jobs' => [],
            'job_assign' => [],
            'job_metronics_skills' => [],
        ];

        $jobDetailsCache[$jId] = $defaultObj;

        return $defaultObj;
    }

    public static function whatsappConnectCheck($id)
    {

        $instance = self::getInstance($id);
        $baseUrl = self::baseUrl();
        if (! $instance) {
            return null;
        }

        /* Check live connection state first — must be 'open' to count as connected */
        try {
            $stateResponse = Http::withHeaders([
                'apikey' => config('services.evolution.global_key'),
            ])->get($baseUrl.'/instance/connectionState/'.$instance->instance_name);
            $stateData = $stateResponse->json();
            $state = $stateData['instance']['state'] ?? $stateData['state'] ?? null;
            if ($state !== 'open') {
                return null;
            }
        } catch (\Throwable $e) {
            return null;
        }

        $responseConnected = Http::withHeaders([
            'apikey' => config('services.evolution.global_key'),
        ])->get(
            $baseUrl.'/instance/fetchInstances'
        );
        $dataConnected = $responseConnected->json();
        $connectedNumber = collect($dataConnected)
            ->firstWhere('name', @$instance->instance_name)['ownerJid'] ?? null;

        if ($connectedNumber) {
            $connectedNumber = explode('@', $connectedNumber)[0];
        }

        return $connectedNumber;

    }

    public static function getInstance($id)
    {
        return DB::table('whatsapp_instances')
            ->where('user_id', $id)
            ->first();
    }

    public static function baseUrl()
    {
        return config('services.evolution.url');
    }

    public static function cacheClean($key)
    {
        Cache::lock('lock_'.$key, 2)->block(2, function () use ($key) {
            cache()->forever($key, (int) cache()->get($key, 1) + 1);
        });

        return true;
    }

    public static function getCompanyCurrencyBenchshowMode($id)
    {

        $defaultCurrency = DB::table('companies')
            ->join('master_currency', 'master_currency.id', '=', 'companies.currency')
            ->select('master_currency.code', 'companies.bench_show_mode')
            ->where('companies.id', $id)
            ->first();
        $currency_code = 'INR';
        if ($defaultCurrency) {
            $currency_code = ($defaultCurrency->code);
        }

        return (object) [
            'currency_code' => $currency_code ?? 'INR',
            'show_mode' => $defaultCurrency->bench_show_mode ?? 0,
        ];

    }

    public static function agencyCandidatesCount($ClientComID, $jobId)
    {
        $elasticService = app(ElasticService::class);

        $params = [
            'index' => ELASTIC_CANDIDATES,
            'body' => [
                'size' => 0,
                'query' => [
                    'bool' => [
                        'must' => [
                            ['term' => ['ClientComID' => $ClientComID]],
                            ['term' => ['jobId' => $jobId]],
                        ],
                        'must_not' => [
                            ['terms' => ['job_master_status_id' => [8, 9]]],
                        ],
                    ],
                ],
                'aggs' => [
                    'all_candidates' => [
                        'value_count' => [
                            'field' => 'id',
                        ],
                    ],
                    'joined' => [
                        'filter' => [
                            'term' => [
                                'job_master_status_id' => 6,
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $response = $elasticService->getClient()->search($params);
        $allCandidate = $response['aggregations']['all_candidates']['value'] ?? 0;
        $Joined = $response['aggregations']['joined']['doc_count'] ?? 0;

        $params = [
            'index' => ELASTIC_INERTVIEW_TRACKER,
            'body' => [
                'size' => 0,
                'query' => [
                    'bool' => [
                        'must' => [
                            ['term' => ['ClientComID' => $ClientComID]],
                            ['term' => ['jobId' => $jobId]],
                        ],
                        'must_not' => [
                            ['terms' => ['job_master_status_id' => [8, 9]]],
                        ],
                    ],
                ],
                'aggs' => [
                    'all_candidate_interview' => [
                        'value_count' => [
                            'field' => 'id',
                        ],
                    ],
                ],
            ],
        ];
        $responseIinterview = $elasticService->getClient()->search($params);
        $allCandidateInterview = $responseIinterview['aggregations']['all_candidate_interview']['value'] ?? 0;

        $recommendedDataListArr = (object) [
            'Upload' => $allCandidate,
            'Interview' => $allCandidateInterview,
            'Joined' => $Joined,
        ];

        return $recommendedDataListArr;

    }

    /**
     * Batch fetch self-company client details for multiple company IDs from Elasticsearch.
     * Replaces N+1 single ES queries per row in agency listing views.
     *
     * Intent: Batch load client details for multiple agency company IDs.
     * Tenancy: Filtered by company_id IN ($companyIds).
     *
     * @param  array  $companyIds  List of agency company IDs
     * @return array Map of company_id => (object)client_source
     */
    public static function batchGetClientIdCompany(array $companyIds)
    {
        if (empty($companyIds)) {
            return [];
        }

        $elasticService = app(ElasticService::class);
        $resultMap = [];

        try {
            $params = [
                'index' => ELASTIC_CLIENT_LIST,
                'body' => [
                    'size' => max(count($companyIds) * 2, 20),
                    'query' => [
                        'bool' => [
                            'must' => [
                                ['terms' => ['company_id' => array_values(array_map('intval', $companyIds))]],
                                ['term' => ['self_company' => 1]],
                            ],
                        ],
                    ],
                ],
            ];

            $result = $elasticService->getClient()->search($params);
            $hits = $result['hits']['hits'] ?? [];
            foreach ($hits as $hit) {
                $source = (object) ($hit['_source'] ?? []);
                if (isset($source->company_id)) {
                    $resultMap[$source->company_id] = $source;
                }
            }
        } catch (\Exception $e) {
            // Fallback gracefully on search exception
        }

        return $resultMap;
    }

    /**
     * Batch count candidate metrics (Upload, Interview, Joined) for multiple agencies on a job.
     * Single Elasticsearch search call replacing N+1 per-agency aggregations.
     *
     * Intent: Batch load performance metrics for multiple assigned agency company IDs.
     * Tenancy: Filtered by ClientComID IN ($clientComIds) and jobId.
     *
     * @param  array  $clientComIds  Array of agency company IDs
     * @param  int|string  $jobId  Job ID
     * @return array Map of agency_id => (object)['Upload' => count, 'Interview' => count, 'Joined' => count]
     */
    public static function batchAgencyCandidatesCount(array $clientComIds, $jobId)
    {
        if (empty($clientComIds)) {
            return [];
        }

        $elasticService = app(ElasticService::class);
        $resultMap = [];

        foreach ($clientComIds as $cid) {
            $resultMap[$cid] = (object) ['Upload' => 0, 'Interview' => 0, 'Joined' => 0];
        }

        try {
            // Query 1: Candidates count & Joined count grouped by ClientComID
            $paramsCandidates = [
                'index' => ELASTIC_CANDIDATES,
                'body' => [
                    'size' => 0,
                    'query' => [
                        'bool' => [
                            'must' => [
                                ['terms' => ['ClientComID' => array_values(array_map('intval', $clientComIds))]],
                                ['term' => ['jobId' => (int) $jobId]],
                            ],
                            'must_not' => [
                                ['terms' => ['job_master_status_id' => [8, 9]]],
                            ],
                        ],
                    ],
                    'aggs' => [
                        'agencies' => [
                            'terms' => [
                                'field' => 'ClientComID',
                                'size' => count($clientComIds),
                            ],
                            'aggs' => [
                                'joined' => [
                                    'filter' => [
                                        'term' => ['job_master_status_id' => 6],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ];

            $respCandidates = $elasticService->getClient()->search($paramsCandidates);
            $buckets = $respCandidates['aggregations']['agencies']['buckets'] ?? [];
            foreach ($buckets as $b) {
                $cid = $b['key'];
                if (isset($resultMap[$cid])) {
                    $resultMap[$cid]->Upload = $b['doc_count'] ?? 0;
                    $resultMap[$cid]->Joined = $b['joined']['doc_count'] ?? 0;
                }
            }

            // Query 2: Interview tracker count grouped by ClientComID
            $paramsInterviews = [
                'index' => ELASTIC_INERTVIEW_TRACKER,
                'body' => [
                    'size' => 0,
                    'query' => [
                        'bool' => [
                            'must' => [
                                ['terms' => ['ClientComID' => array_values(array_map('intval', $clientComIds))]],
                                ['term' => ['jobId' => (int) $jobId]],
                            ],
                            'must_not' => [
                                ['terms' => ['job_master_status_id' => [8, 9]]],
                            ],
                        ],
                    ],
                    'aggs' => [
                        'agencies' => [
                            'terms' => [
                                'field' => 'ClientComID',
                                'size' => count($clientComIds),
                            ],
                        ],
                    ],
                ],
            ];

            $respInterviews = $elasticService->getClient()->search($paramsInterviews);
            $bucketsInt = $respInterviews['aggregations']['agencies']['buckets'] ?? [];
            foreach ($bucketsInt as $b) {
                $cid = $b['key'];
                if (isset($resultMap[$cid])) {
                    $resultMap[$cid]->Interview = $b['doc_count'] ?? 0;
                }
            }
        } catch (\Exception $e) {
            // Fallback gracefully if index query fails
        }

        return $resultMap;
    }

    /**
     * Batch fetch agency relation data for multiple agency company IDs.
     *
     * Intent: Batch load agency invitation/relation status between from_company_id and to_company_ids.
     * Tenancy: Filtered by from_company_id and to_company_id IN ($toCompanyIds).
     *
     * @param  int|string  $fromCompanyId  Employer company ID
     * @param  array  $toCompanyIds  List of agency company IDs
     * @return array Map of to_company_id => (object)relation_source
     */
    public static function batchGetAgencyRelationData($fromCompanyId, array $toCompanyIds)
    {
        if (empty($toCompanyIds)) {
            return [];
        }

        $elasticService = app(ElasticService::class);
        $resultMap = [];

        try {
            $params = [
                'index' => ELASTIC_CLIENT_REQUEST,
                'body' => [
                    'size' => max(count($toCompanyIds) * 2, 20),
                    'query' => [
                        'bool' => [
                            'must' => [
                                ['term' => ['from_company_id' => (int) $fromCompanyId]],
                                ['terms' => ['to_company_id' => array_values(array_map('intval', $toCompanyIds))]],
                            ],
                        ],
                    ],
                    'sort' => [
                        ['created_at' => ['order' => 'desc']],
                    ],
                ],
            ];

            $response = $elasticService->getClient()->search($params);
            $hits = $response['hits']['hits'] ?? [];
            foreach ($hits as $hit) {
                $source = (object) ($hit['_source'] ?? []);
                if (isset($source->to_company_id) && ! isset($resultMap[$source->to_company_id])) {
                    $resultMap[$source->to_company_id] = $source;
                }
            }
        } catch (\Exception $e) {
            // Fallback gracefully on search exception
        }

        return $resultMap;
    }

    /**
     * Batch count active jobs for multiple agency company IDs from Elasticsearch.
     * Replaces N+1 per-agency queries in recommended agency views.
     *
     * Intent: Batch load active job count per agency company.
     * Tenancy: Filtered by company_id IN ($companyIds) and status='active'.
     *
     * @param  array  $companyIds  List of agency company IDs
     * @return array Map of company_id => (object)['all_jobs' => count]
     */
    public static function batchAgencyJobsCount(array $companyIds)
    {
        if (empty($companyIds)) {
            return [];
        }

        $elasticService = app(ElasticService::class);
        $resultMap = [];
        foreach ($companyIds as $cid) {
            $resultMap[$cid] = (object) ['all_jobs' => 0];
        }

        try {
            $params = [
                'index' => ELASTIC_JOBS,
                'body' => [
                    'size' => 0,
                    'query' => [
                        'bool' => [
                            'must' => [
                                ['terms' => ['company_id' => array_values(array_map('intval', $companyIds))]],
                                ['term' => ['status.keyword' => 'active']],
                            ],
                        ],
                    ],
                    'aggs' => [
                        'agencies' => [
                            'terms' => [
                                'field' => 'company_id',
                                'size' => count($companyIds),
                            ],
                        ],
                    ],
                ],
            ];

            $response = $elasticService->getClient()->search($params);
            $buckets = $response['aggregations']['agencies']['buckets'] ?? [];
            foreach ($buckets as $b) {
                $cid = $b['key'];
                if (isset($resultMap[$cid])) {
                    $resultMap[$cid]->all_jobs = $b['doc_count'] ?? 0;
                }
            }
        } catch (\Exception $e) {
            // Fallback gracefully on search error
        }

        return $resultMap;
    }

    /**
     * Batch count total & bench candidates for multiple agency company IDs from Elasticsearch.
     * Replaces N+1 per-agency candidate counts in recommended agency views.
     *
     * Intent: Batch load total and bench candidate counts per agency company.
     * Tenancy: Filtered by company_id IN ($companyIds) and valid statuses.
     *
     * @param  array  $companyIds  List of agency company IDs
     * @return array Map of company_id => (object)['all_candidates' => count, 'all_candidate_bench' => count]
     */
    public static function batchAgencyJobsCandidatesCount(array $companyIds)
    {
        if (empty($companyIds)) {
            return [];
        }

        $elasticService = app(ElasticService::class);
        $resultMap = [];
        foreach ($companyIds as $cid) {
            $resultMap[$cid] = (object) ['all_candidates' => 0, 'all_candidate_bench' => 0];
        }

        try {
            $params = [
                'index' => ELASTIC_CANDIDATES,
                'body' => [
                    'size' => 0,
                    'query' => [
                        'bool' => [
                            'must' => [
                                ['terms' => ['company_id' => array_values(array_map('intval', $companyIds))]],
                            ],
                            'must_not' => [
                                ['terms' => ['job_master_status_id' => [8, 9]]],
                            ],
                        ],
                    ],
                    'aggs' => [
                        'agencies' => [
                            'terms' => [
                                'field' => 'company_id',
                                'size' => count($companyIds),
                            ],
                            'aggs' => [
                                'bench' => [
                                    'filter' => [
                                        'term' => ['candidate_type.keyword' => 'bench_condidate'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ];

            $response = $elasticService->getClient()->search($params);
            $buckets = $response['aggregations']['agencies']['buckets'] ?? [];
            foreach ($buckets as $b) {
                $cid = $b['key'];
                if (isset($resultMap[$cid])) {
                    $resultMap[$cid]->all_candidates = $b['doc_count'] ?? 0;
                    $resultMap[$cid]->all_candidate_bench = $b['bench']['doc_count'] ?? 0;
                }
            }
        } catch (\Exception $e) {
            // Fallback gracefully on search error
        }

        return $resultMap;
    }

    /**
     * Batch fetch commercial minimum rate & type for multiple agency company IDs.
     * Replaces N+1 per-agency queries in recommended & registered agency views.
     *
     * Intent: Batch fetch minimum commercial rates for agencies in single query.
     * Tenancy: Filtered by company_id IN ($companyIds).
     *
     * @param  array  $companyIds  List of agency company IDs
     * @param  int  $commtype  Commercial type (1: Contract, 2: Full-time)
     * @return array Map of company_id => (object)['minimum_rate' => rate, 'minimum_rate_type' => object]
     */
    public static function batchGetCommercialsData(array $companyIds, $commtype)
    {
        if (empty($companyIds)) {
            return [];
        }

        $commTypeMap = DB::table('commericials')
            ->select('commericials.*')
            ->whereIn('commericials.company_id', $companyIds)
            ->where('commericials.commtype', '=', $commtype)
            ->get()
            ->keyBy('company_id');

        $minRates = DB::table('commericials')
            ->join('commericial_details', 'commericial_details.commericial_id', '=', 'commericials.id')
            ->select('commericials.company_id', DB::raw('MIN(commericial_details.rate) as min_rate'))
            ->whereIn('commericials.company_id', $companyIds)
            ->where('commericials.commtype', '=', $commtype)
            ->whereExists(function ($experienceBands) {
                $experienceBands->selectRaw('1')
                    ->from('commercial_experience_bands as experience_bands')
                    ->where('experience_bands.is_active', 1)
                    ->whereColumn('commericial_details.exp', '>=', 'experience_bands.min_experience')
                    ->whereColumn('commericial_details.exp', '<=', 'experience_bands.max_experience');
            })
            ->groupBy('commericials.company_id')
            ->pluck('min_rate', 'company_id');

        $resultMap = [];
        foreach ($companyIds as $cid) {
            $resultMap[$cid] = (object) [
                'minimum_rate' => $minRates[$cid] ?? null,
                'minimum_rate_type' => $commTypeMap[$cid] ?? null,
            ];
        }

        return $resultMap;
    }

    /**
     * Batch fetch match found status on staffing_company_relation for multiple agencies.
     * Replaces N+1 per-agency queries in agency marketplace views.
     *
     * Intent: Batch fetch active relation counts between employer and multiple agencies.
     * Tenancy: Filtered by from_company_id and to_company_id IN ($toCompanyIds).
     *
     * @param  int  $fromCompanyId  Employer company ID
     * @param  array  $toCompanyIds  List of agency company IDs
     * @return array Map of company_id => count
     */
    public static function batchGetMatchFoundMap($fromCompanyId, array $toCompanyIds)
    {
        if (empty($toCompanyIds)) {
            return [];
        }

        return DB::table('staffing_company_relation')
            ->select('to_company_id', DB::raw('count(*) as total'))
            ->where('from_company_id', $fromCompanyId)
            ->whereIn('to_company_id', $toCompanyIds)
            ->where('final_status', 1)
            ->groupBy('to_company_id')
            ->pluck('total', 'to_company_id')
            ->toArray();
    }

    /**
     * Invalidate all agency marketplace cache version keys for a given user ID.
     * Bumps version keys for all agency drawers (send invitation, recommended, registered, assigned).
     *
     * Intent: Clean agency marketplace cache on write actions.
     * Tenancy: Scoped to user_id.
     *
     * @param  int  $userId  User ID whose agency marketplace cache should be cleaned
     * @return bool
     */
    public static function invalidateSpartnerlistingCache($userId)
    {
        if (! $userId) {
            return false;
        }

        $keys = [
            'allsendinvitationtoagency_version_user_'.$userId,
            'allrecommendedagency_version_user_'.$userId,
            'allregisteredagency_version_user_'.$userId,
            'allassignedagency_version_user_'.$userId,
        ];

        foreach ($keys as $k) {
            self::cacheClean($k);
        }

        return true;
    }

    /**
     * Calculate pipeline status stage counters for bench explore candidates.
     * Tenancy Note: Filtered strictly by candidate_id and tenant other_company for 100% data consistency with listing.
     *
     * @param  int|string  $bench_id
     * @param  int|string|null  $company_id
     * @return object
     */
    public static function bench_explore_listing_counter($bench_id, $company_id = null)
    {
        if (empty($company_id) && auth()->check()) {
            $company_id = auth()->user()->company_id;
        }

        $elasticService = app(ElasticService::class);

        $must = [
            ['term' => ['candidate_id' => (int) $bench_id]],
            ['terms' => ['job_status' => ['active', 'hold', 'inactive', '2']]],
        ];
        if (! empty($company_id)) {
            $must[] = ['term' => ['other_company' => (int) $company_id]];
        }

        $boolQuery = [
            'bool' => [
                'must' => $must,
            ],
        ];

        $params = [
            'index' => ELASTIC_CANDIDATES,
            'body' => [
                'size' => 0,
                'query' => $boolQuery,
                'aggs' => [
                    'status_counts' => [
                        'filters' => [
                            'filters' => [
                                '1' => ['term' => ['job_master_status_id' => 1]],
                                '2' => ['term' => ['job_master_status_id' => 2]],
                                '3' => ['terms' => ['job_master_status_id' => [3, 8]]],
                                '4' => ['term' => ['job_master_status_id' => 4]],
                                '5' => ['term' => ['job_master_status_id' => 5]],
                                '6' => ['term' => ['job_master_status_id' => 6]],
                                '7' => ['terms' => ['job_master_status_id' => [7, 9]]],
                                '8' => ['terms' => ['job_master_status_id' => [3, 8]]],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        try {
            $response = $elasticService->getClient()->search($params);
            $total = $response['hits']['total']['value'] ?? 0;
            $buckets = $response['aggregations']['status_counts']['buckets'] ?? [];

            $applicationCounter = (object) [
                'allCandidate' => $total,
                'Sourcing' => $buckets['1']['doc_count'] ?? 0,
                'Screening' => $buckets['2']['doc_count'] ?? 0,
                'Evaluation' => $buckets['3']['doc_count'] ?? 0,
                'Negotiation' => $buckets['4']['doc_count'] ?? 0,
                'Offered' => $buckets['5']['doc_count'] ?? 0,
                'Joined' => $buckets['6']['doc_count'] ?? 0,
                'Rejected' => $buckets['7']['doc_count'] ?? 0,
                'Interview' => $buckets['8']['doc_count'] ?? 0,
            ];

            return (object) [
                'total' => $total,
                'applicationCounter' => $applicationCounter,
            ];
        } catch (\Throwable $e) {
            \Log::warning('bench_explore_listing_counter exception: '.$e->getMessage());

            return (object) [
                'total' => 0,
                'applicationCounter' => (object) [
                    'allCandidate' => 0,
                    'Sourcing' => 0,
                    'Screening' => 0,
                    'Evaluation' => 0,
                    'Negotiation' => 0,
                    'Offered' => 0,
                    'Joined' => 0,
                    'Rejected' => 0,
                    'Interview' => 0,
                ],
            ];
        }
    }

    public static function benchRequestCheck($candidate_id, $job_id)
    {

        $elasticService = app(ElasticService::class);

        $params = [
            'index' => ELASTIC_CANDIDATES_REQUEST,
            'body' => [
                'size' => 1,
                'query' => [
                    'bool' => [
                        'filter' => [
                            ['term' => ['candidate_id' => $candidate_id]],
                            ['term' => ['job_id' => $job_id]],
                        ],
                    ],
                ],
            ],
        ];

        $response = $elasticService->getClient()->search($params);

        $hit = $response['hits']['hits'][0] ?? null;

        $data = $hit ? (object) $hit['_source'] : null;

        return $data;

    }

    public static function InternalClientCandidatesCount($client_id)
    {
        $elasticService = app(ElasticService::class);

        $params = [
            'index' => ELASTIC_CANDIDATES,
            'body' => [
                'size' => 0,
                'query' => [
                    'bool' => [
                        'must' => [
                            ['term' => ['client_id' => $client_id]],
                        ],
                    ],
                ],
                'aggs' => [
                    'all_candidates' => [
                        'value_count' => [
                            'field' => 'id',
                        ],
                    ],
                    'joined' => [
                        'filter' => [
                            'term' => [
                                'job_master_status_id' => 6,
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $response = $elasticService->getClient()->search($params);
        $allCandidate = $response['aggregations']['all_candidates']['value'] ?? 0;
        $Joined = $response['aggregations']['joined']['doc_count'] ?? 0;

        $recommendedDataListArr = (object) [
            'all_candidates' => $allCandidate,
            'Joined' => $Joined,
        ];

        return $recommendedDataListArr;

    }

    public static function DirectClientCandidatesCount($from_company_id, $to_company_id)
    {
        $elasticService = app(ElasticService::class);

        $params = [
            'index' => ELASTIC_CANDIDATES,
            'body' => [
                'size' => 0,
                'query' => [
                    'bool' => [
                        'must' => [
                            ['term' => ['from_company_id' => $from_company_id]],
                            ['term' => ['to_company_id' => $to_company_id]],
                            ['term' => ['ClientComID' => $to_company_id]],
                            ['term' => ['final_status' => 1]],
                        ],
                        'must_not' => [
                            ['terms' => ['job_master_status_id' => [8, 9]]],
                        ],
                    ],
                ],
                'aggs' => [
                    'all_candidates' => [
                        'value_count' => [
                            'field' => 'id',
                        ],
                    ],
                    'joined' => [
                        'filter' => [
                            'term' => [
                                'job_master_status_id' => 6,
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $response = $elasticService->getClient()->search($params);
        $allCandidate = $response['aggregations']['all_candidates']['value'] ?? 0;
        $Joined = $response['aggregations']['joined']['doc_count'] ?? 0;

        $recommendedDataListArr = (object) [
            'all_candidates' => $allCandidate,
            'Joined' => $Joined,
        ];

        return $recommendedDataListArr;
    }

    public static function InternalClientJobsCount($company_id, $client_id)
    {

        $elasticService = app(ElasticService::class);

        $params = [
            'index' => ELASTIC_JOBS,
            'body' => [
                'size' => 0,
                'query' => [
                    'bool' => [
                        'must' => [
                            ['term' => ['comId' => (int) $company_id]],
                            ['term' => ['client_id' => (int) $client_id]],
                            ['term' => ['status' => 'active']],
                        ],
                    ],
                ],
                'aggs' => [
                    'all_jobs' => [
                        'value_count' => [
                            'field' => 'id',
                        ],
                    ],
                ],
            ],
        ];

        $response = $elasticService->getClient()->search($params);
        $all_jobs = $response['aggregations']['all_jobs']['value'] ?? 0;

        $recommendedDataListArr = (object) [
            'all_jobs' => $all_jobs,
        ];

        return $recommendedDataListArr;

    }

    public static function DirectClientJobsCount($from_company_id, $toCompanyId)
    {

        $elasticService = app(ElasticService::class);

        $must = [];
        $must[] = [
            'term' => [
                'status' => 'active',
            ],
        ];

        $must[] = [
            'nested' => [
                'path' => 'recommendation_relation',
                'query' => [
                    'bool' => [
                        'must' => [
                            [
                                'term' => [
                                    'recommendation_relation.to_company_id' => (int) $toCompanyId,
                                ],
                            ],
                            [
                                'term' => [
                                    'recommendation_relation.from_company_id' => (int) $from_company_id,
                                ],
                            ],

                            [
                                'term' => [
                                    'recommendation_relation.final_status' => (int) 1,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $params = [
            'index' => ELASTIC_JOBS,
            'body' => [
                'size' => 0,
                'query' => [
                    'bool' => [
                        'must' => $must,
                    ],
                ],
                'aggs' => [
                    'all_jobs' => [
                        'value_count' => [
                            'field' => 'id',
                        ],
                    ],
                ],
            ],
        ];

        $response = $elasticService->getClient()->search($params);
        $all_jobs = $response['aggregations']['all_jobs']['value'] ?? 0;

        $recommendedDataListArr = (object) [
            'all_jobs' => $all_jobs,
        ];

        return $recommendedDataListArr;

    }

    /**
     * Internal-client job counts broken down by status (active/inactive/hold).
     * Read-only ES aggregation for the Clients list "Jobs" column; mirrors the
     * Agencies design. Tenancy: scoped by comId (current company) + client_id.
     */
    public static function InternalClientJobsCountAllStatus($company_id, $client_id)
    {
        $cacheKey = "ats:kpi:internal_client_jobs:{$company_id}:{$client_id}";

        return cache()->remember($cacheKey, 900, function () use ($company_id, $client_id) {
            $elasticService = app(ElasticService::class);
            if (! $elasticService->indexExists(ELASTIC_JOBS)) {
                return (object) ['all_jobs' => 0, 'job_active' => 0, 'job_inactive' => 0, 'job_hold' => 0];
            }

            $params = [
                'index' => ELASTIC_JOBS,
                'request_cache' => true,
                'body' => [
                    'size' => 0,
                    'query' => [
                        'bool' => [
                            'must' => [
                                ['term' => ['comId' => (int) $company_id]],
                                ['term' => ['client_id' => (int) $client_id]],
                            ],
                        ],
                    ],
                    'aggs' => [
                        'all_jobs' => ['value_count' => ['field' => 'id']],
                        'job_active' => ['filter' => ['term' => ['status' => 'active']]],
                        'job_inactive' => ['filter' => ['term' => ['status' => 'inactive']]],
                        'job_hold' => ['filter' => ['term' => ['status' => 'hold']]],
                    ],
                ],
            ];

            $response = $elasticService->getClient()->search($params);

            return (object) [
                'all_jobs' => $response['aggregations']['all_jobs']['value'] ?? 0,
                'job_active' => $response['aggregations']['job_active']['doc_count'] ?? 0,
                'job_inactive' => $response['aggregations']['job_inactive']['doc_count'] ?? 0,
                'job_hold' => $response['aggregations']['job_hold']['doc_count'] ?? 0,
            ];
        });
    }

    /**
     * Internal-client candidate funnel for the Clients list "Performance" column
     * (sourced/evaluated/interviews/offers/joined). Read-only ES aggregations.
     * Tenancy: scoped by client_id (already company-bound). Interview count is a
     * best-effort term match on client_id in the interview tracker; degrades to 0.
     */
    public static function InternalClientCandidatesCountList($client_id)
    {
        $cacheKey = "ats:kpi:internal_client_cand:{$client_id}";

        return cache()->remember($cacheKey, 900, function () use ($client_id) {
            $elasticService = app(ElasticService::class);
            $allCandidates = 0;
            $joined = 0;
            $sourced = 0;
            $evaluated = 0;
            $offers = 0;
            $allInterview = 0;

            if ($elasticService->indexExists(ELASTIC_CANDIDATES)) {
                $params = [
                    'index' => ELASTIC_CANDIDATES,
                    'request_cache' => true,
                    'body' => [
                        'size' => 0,
                        'query' => [
                            'bool' => [
                                'must' => [
                                    ['term' => ['client_id' => $client_id]],
                                ],
                                'must_not' => [
                                    ['terms' => ['job_master_status_id' => [8, 9]]],
                                ],
                            ],
                        ],
                        'aggs' => [
                            'all_candidates' => ['value_count' => ['field' => 'id']],
                            'joined' => ['filter' => ['term' => ['job_master_status_id' => 6]]],
                            'sourced' => ['filter' => ['term' => ['job_master_status_id' => 1]]],
                            'evaluated' => ['filter' => ['term' => ['job_master_status_id' => 3]]],
                            'offers' => ['filter' => ['exists' => ['field' => 'of_created_date']]],
                        ],
                    ],
                ];
                $response = $elasticService->getClient()->search($params);
                $allCandidates = $response['aggregations']['all_candidates']['value'] ?? 0;
                $joined = $response['aggregations']['joined']['doc_count'] ?? 0;
                $sourced = $response['aggregations']['sourced']['doc_count'] ?? 0;
                $evaluated = $response['aggregations']['evaluated']['doc_count'] ?? 0;
                $offers = $response['aggregations']['offers']['doc_count'] ?? 0;
            }

            if ($elasticService->indexExists(ELASTIC_INERTVIEW_TRACKER)) {
                $paramsInterview = [
                    'index' => ELASTIC_INERTVIEW_TRACKER,
                    'request_cache' => true,
                    'body' => [
                        'size' => 0,
                        'query' => [
                            'bool' => [
                                'must' => [
                                    ['term' => ['client_id' => $client_id]],
                                ],
                            ],
                        ],
                        'aggs' => [
                            'all_interview' => ['value_count' => ['field' => 'id']],
                        ],
                    ],
                ];
                $responseInterview = $elasticService->getClient()->search($paramsInterview);
                $allInterview = $responseInterview['aggregations']['all_interview']['value'] ?? 0;
            }

            return (object) [
                'all_candidates' => $allCandidates,
                'Joined' => $joined,
                'sourced' => $sourced,
                'evaluated' => $evaluated,
                'offers' => $offers,
                'all_interview' => $allInterview,
            ];
        });
    }

    /**
     * Direct-client job counts broken down by status (active/inactive/hold) for the
     * Clients list "Jobs" column. Read-only ES aggregation over the recommendation
     * relation (from_company_id -> to_company_id, final_status = 1).
     */
    public static function DirectClientJobsCountAllStatus($from_company_id, $toCompanyId)
    {
        $cacheKey = "ats:kpi:direct_client_jobs:{$from_company_id}:{$toCompanyId}";

        return cache()->remember($cacheKey, 900, function () use ($from_company_id, $toCompanyId) {
            $elasticService = app(ElasticService::class);
            if (! $elasticService->indexExists(ELASTIC_JOBS)) {
                return (object) ['all_jobs' => 0, 'job_active' => 0, 'job_inactive' => 0, 'job_hold' => 0];
            }

            $must = [];
            $must[] = [
                'nested' => [
                    'path' => 'recommendation_relation',
                    'query' => [
                        'bool' => [
                            'must' => [
                                ['term' => ['recommendation_relation.to_company_id' => (int) $toCompanyId]],
                                ['term' => ['recommendation_relation.from_company_id' => (int) $from_company_id]],
                                ['term' => ['recommendation_relation.final_status' => (int) 1]],
                            ],
                        ],
                    ],
                ],
            ];

            $params = [
                'index' => ELASTIC_JOBS,
                'request_cache' => true,
                'body' => [
                    'size' => 0,
                    'query' => [
                        'bool' => [
                            'must' => $must,
                        ],
                    ],
                    'aggs' => [
                        'all_jobs' => ['value_count' => ['field' => 'id']],
                        'job_active' => ['filter' => ['term' => ['status' => 'active']]],
                        'job_inactive' => ['filter' => ['term' => ['status' => 'inactive']]],
                        'job_hold' => ['filter' => ['term' => ['status' => 'hold']]],
                    ],
                ],
            ];

            $response = $elasticService->getClient()->search($params);

            return (object) [
                'all_jobs' => $response['aggregations']['all_jobs']['value'] ?? 0,
                'job_active' => $response['aggregations']['job_active']['doc_count'] ?? 0,
                'job_inactive' => $response['aggregations']['job_inactive']['doc_count'] ?? 0,
                'job_hold' => $response['aggregations']['job_hold']['doc_count'] ?? 0,
            ];
        });
    }

    /**
     * Direct-client candidate funnel for the Clients list "Performance" column
     * (sourced/evaluated/interviews/offers/joined). Read-only ES aggregations,
     * scoped by from_company_id/to_company_id + ClientComID + final_status = 1.
     */
    public static function DirectClientCandidatesCountList($from_company_id, $to_company_id)
    {
        $cacheKey = "ats:kpi:direct_client_cand:{$from_company_id}:{$to_company_id}";

        return cache()->remember($cacheKey, 900, function () use ($from_company_id, $to_company_id) {
            $elasticService = app(ElasticService::class);
            $allCandidates = 0;
            $joined = 0;
            $sourced = 0;
            $evaluated = 0;
            $offers = 0;
            $allInterview = 0;

            if ($elasticService->indexExists(ELASTIC_CANDIDATES)) {
                $params = [
                    'index' => ELASTIC_CANDIDATES,
                    'request_cache' => true,
                    'body' => [
                        'size' => 0,
                        'query' => [
                            'bool' => [
                                'must' => [
                                    ['term' => ['from_company_id' => $from_company_id]],
                                    ['term' => ['to_company_id' => $to_company_id]],
                                    ['term' => ['ClientComID' => $to_company_id]],
                                    ['term' => ['final_status' => 1]],
                                ],
                                'must_not' => [
                                    ['terms' => ['job_master_status_id' => [8, 9]]],
                                ],
                            ],
                        ],
                        'aggs' => [
                            'all_candidates' => ['value_count' => ['field' => 'id']],
                            'joined' => ['filter' => ['term' => ['job_master_status_id' => 6]]],
                            'sourced' => ['filter' => ['term' => ['job_master_status_id' => 1]]],
                            'evaluated' => ['filter' => ['term' => ['job_master_status_id' => 3]]],
                            'offers' => ['filter' => ['exists' => ['field' => 'of_created_date']]],
                        ],
                    ],
                ];

                $response = $elasticService->getClient()->search($params);
                $allCandidates = $response['aggregations']['all_candidates']['value'] ?? 0;
                $joined = $response['aggregations']['joined']['doc_count'] ?? 0;
                $sourced = $response['aggregations']['sourced']['doc_count'] ?? 0;
                $evaluated = $response['aggregations']['evaluated']['doc_count'] ?? 0;
                $offers = $response['aggregations']['offers']['doc_count'] ?? 0;
            }

            if ($elasticService->indexExists(ELASTIC_INERTVIEW_TRACKER)) {
                $paramsInterview = [
                    'index' => ELASTIC_INERTVIEW_TRACKER,
                    'request_cache' => true,
                    'body' => [
                        'size' => 0,
                        'query' => [
                            'bool' => [
                                'must' => [
                                    ['term' => ['from_company_id' => $from_company_id]],
                                    ['term' => ['to_company_id' => $to_company_id]],
                                ],
                            ],
                        ],
                        'aggs' => [
                            'all_interview' => ['value_count' => ['field' => 'id']],
                        ],
                    ],
                ];

                $responseInterview = $elasticService->getClient()->search($paramsInterview);
                $allInterview = $responseInterview['aggregations']['all_interview']['value'] ?? 0;
            }

            return (object) [
                'all_candidates' => $allCandidates,
                'Joined' => $joined,
                'sourced' => $sourced,
                'evaluated' => $evaluated,
                'offers' => $offers,
                'all_interview' => $allInterview,
            ];
        });
    }

    public static function agencyJobsCount($company_id)
    {

        $elasticService = app(ElasticService::class);

        $must = [];
        $must[] = [
            'term' => [
                'status' => 'active',
            ],
        ];

        $must[] = [
            'bool' => [
                'should' => [
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
                                        [
                                            'term' => [
                                                'recommendation_relation.to_company_id' => $company_id,
                                            ],
                                        ],
                                        [
                                            'term' => [
                                                'recommendation_relation.final_status' => 1,
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'minimum_should_match' => 1,
            ],
        ];

        $params = [
            'index' => ELASTIC_JOBS,
            'body' => [
                'size' => 0,
                'query' => [
                    'bool' => [
                        'must' => $must,
                    ],
                ],
                'aggs' => [
                    'all_jobs' => [
                        'value_count' => [
                            'field' => 'id',
                        ],
                    ],
                ],
            ],
        ];

        $response = $elasticService->getClient()->search($params);
        $all_jobs = $response['aggregations']['all_jobs']['value'] ?? 0;

        $recommendedDataListArr = (object) [
            'all_jobs' => $all_jobs,
        ];

        return $recommendedDataListArr;

    }

    public static function agencyJobsCandidatesCount($company_id)
    {

        $elasticService = app(ElasticService::class);

        $must = [];

        $must = [
            'bool' => [
                'should' => [
                    // First condition
                    [
                        'bool' => [
                            'must' => array_merge([
                                ['term' => ['company_id' => $company_id]],
                                ['term' => ['ClientComID' => 0]],
                            ]),
                        ],
                    ],
                    // Second condition
                    [
                        'bool' => [
                            'must' => array_merge([
                                ['term' => ['company_id' => $company_id]],
                                ['range' => ['ClientComID' => ['gt' => 0]]],
                                ['range' => ['jobId' => ['gt' => 0]]],
                            ], [
                                ['bool' => [
                                    'must_not' => [
                                        ['terms' => ['job_master_status_id' => [8, 9]]],
                                    ],
                                ]],
                            ]),
                        ],
                    ],
                    // Third condition
                    [
                        'bool' => [
                            'must' => array_merge([
                                ['term' => ['ClientComID' => $company_id]],
                                ['range' => ['jobId' => ['gt' => 0]]],
                            ]),
                        ],
                    ],
                ],
                'minimum_should_match' => 1,
                'filter' => [], // We'll add all filters here
            ],
        ];

        $params = [
            'index' => ELASTIC_CANDIDATES,
            'body' => [
                'size' => 0,
                'query' => [
                    'bool' => [
                        'must' => $must,
                    ],
                ],
                'aggs' => [
                    'all_candidates' => [
                        'value_count' => [
                            'field' => 'id',
                        ],
                    ],
                    'joined' => [
                        'filter' => [
                            'term' => [
                                'job_master_status_id' => 6,
                            ],
                        ],
                    ],
                    'offer_candidates' => [
                        'filter' => [
                            'exists' => [
                                'field' => 'of_created_date',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $response = $elasticService->getClient()->search($params);
        $allCandidate = $response['aggregations']['all_candidates']['value'] ?? 0;
        $Joined = $response['aggregations']['joined']['doc_count'] ?? 0;
        $offer_candidates = $response['aggregations']['offer_candidates']['doc_count'] ?? 0;

        $params = [
            'index' => ELASTIC_CANDIDATES_BENCH,
            'body' => [
                'size' => 0,
                'query' => [
                    'bool' => [
                        'must' => [
                            ['term' => ['company_id' => $company_id]],
                            ['term' => ['status' => 2]],
                        ],
                    ],
                ],
                'aggs' => [
                    'all_candidate_bench' => [
                        'value_count' => [
                            'field' => 'id',
                        ],
                    ],
                ],
            ],
        ];

        $response = $elasticService->getClient()->search($params);
        $allCandidateBench = $response['aggregations']['all_candidate_bench']['value'] ?? 0;

        $recommendedDataListArr = (object) [
            'all_candidates' => $allCandidate,
            'Joined' => $Joined,
            'offer_candidates' => $offer_candidates,
            'all_candidate_bench' => $allCandidateBench,
        ];

        return $recommendedDataListArr;

    }

    public static function agencyJobsCandidatesCountJobWise($company_id, $jobId)
    {

        $elasticService = app(ElasticService::class);

        $must = [];

        $must = [
            'bool' => [
                'should' => [
                    // First condition
                    [
                        'bool' => [
                            'must' => array_merge([
                                ['term' => ['company_id' => $company_id]],
                                ['term' => ['jobId' => $jobId]],
                                ['term' => ['ClientComID' => 0]],
                            ]),
                        ],
                    ],
                    // Second condition
                    [
                        'bool' => [
                            'must' => array_merge([
                                ['term' => ['company_id' => $company_id]],
                                ['term' => ['jobId' => $jobId]],
                                ['range' => ['ClientComID' => ['gt' => 0]]],
                            ], [
                                ['bool' => [
                                    'must_not' => [
                                        ['terms' => ['job_master_status_id' => [8, 9]]],
                                    ],
                                ]],
                            ]),
                        ],
                    ],
                    // Third condition
                    [
                        'bool' => [
                            'must' => array_merge([
                                ['term' => ['ClientComID' => $company_id]],
                                ['term' => ['jobId' => $jobId]],
                            ]),
                        ],
                    ],
                ],
                'minimum_should_match' => 1,
                'filter' => [], // We'll add all filters here
            ],
        ];

        $params = [
            'index' => ELASTIC_CANDIDATES,
            'body' => [
                'size' => 0,
                'query' => [
                    'bool' => [
                        'must' => $must,
                    ],
                ],
                'aggs' => [
                    'all_candidates' => [
                        'value_count' => [
                            'field' => 'id',
                        ],
                    ],
                    'joined' => [
                        'filter' => [
                            'term' => [
                                'job_master_status_id' => 6,
                            ],
                        ],
                    ],
                    'offer_candidates' => [
                        'filter' => [
                            'exists' => [
                                'field' => 'of_created_date',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $response = $elasticService->getClient()->search($params);
        $allCandidate = $response['aggregations']['all_candidates']['value'] ?? 0;
        $Joined = $response['aggregations']['joined']['doc_count'] ?? 0;
        $offer_candidates = $response['aggregations']['offer_candidates']['doc_count'] ?? 0;

        $recommendedDataListArr = (object) [
            'all_candidates' => $allCandidate,
            'Joined' => $Joined,
            'offer_candidates' => $offer_candidates,
        ];

        return $recommendedDataListArr;

    }

    public static function agencyJobsInterviewCountJobWise($jobId)
    {

        $elasticService = app(ElasticService::class);

        $must = [];

        $must = [
            'bool' => [
                'must' => [
                    // First condition
                    [
                        'bool' => [
                            'must' => array_merge([
                                ['term' => ['jobId' => $jobId]],
                            ]),
                        ],
                    ],
                ],
                'filter' => [], // We'll add all filters here
            ],
        ];

        $params = [
            'index' => ELASTIC_INERTVIEW_TRACKER,
            'body' => [
                'size' => 0,
                'query' => [
                    'bool' => [
                        'must' => $must,
                    ],
                ],
                'aggs' => [
                    'all_interview' => [
                        'value_count' => [
                            'field' => 'id',
                        ],
                    ],
                ],
            ],
        ];

        $response = $elasticService->getClient()->search($params);
        $allInterview = $response['aggregations']['all_interview']['value'] ?? 0;

        $recommendedDataListArr = (object) [
            'all_interview' => $allInterview,
        ];

        return $recommendedDataListArr;

    }

    public static function formatAmount($amount)
    {

        // Remove commas and spaces
        $amount = str_replace([',', ' '], '', $amount);

        // Check numeric
        if (! is_numeric($amount)) {
            return 0;
        }

        $amount = (float) $amount;

        return ($amount >= 1000)
            ? round($amount / 1000, 1).'K'
            : $amount;

        // Remove commas and spaces
        $amount = str_replace([',', ' '], '', $amount);

        // Check numeric
        if (! is_numeric($amount)) {
            return 0;
        }

        $amount = (float) $amount;

        if ($amount >= 10000000) {
            return round($amount / 10000000, 1).'Cr';
        } elseif ($amount >= 100000) {
            return round($amount / 100000, 1).'L';
        } elseif ($amount >= 1000) {
            return round($amount / 1000, 1).'K';
        }

        return $amount;

    }
    // $instance 		= self :: getInstance($id);

    public static function benchCandidateCountSkillBase($id)
    {
        $elasticService = app(ElasticService::class);
        $company_id = auth()->user()->company_id;
        $gdFilterData = self::gdFilterData($id);
        $skillArr = explode(',', $gdFilterData->default_skill);

        $skillShould = [];

        foreach ($skillArr as $skill) {

            $skill = strtolower(trim($skill));

            if (! empty($skill)) {

                $skillShould[] = [
                    'term' => [
                        'job_application_skills.skills.keyword' => [
                            'value' => $skill,
                            'boost' => 10,
                        ],
                    ],
                ];
            }
        }

        $mustQuery = [

            [
                'term' => [
                    'company_id' => $company_id,
                ],
            ],

            [
                'term' => [
                    'condidate_type' => 'bench_condidate',
                ],
            ],

            [
                'term' => [
                    'status' => 2,
                ],
            ],
        ];

        $boolQuery = [
            'bool' => [

                'must' => $mustQuery,
            ],
        ];

        // only apply skill matching if skills exist
        if (! empty($skillShould)) {

            $boolQuery['bool']['should'] = $skillShould;

            // minimum one skill match required
            $boolQuery['bool']['minimum_should_match'] = 1;
        }

        $params = [

            'index' => ELASTIC_CANDIDATES_BENCH,

            'request_cache' => true,

            'track_total_hits' => true,

            'body' => [

                'size' => 0,

                'query' => $boolQuery,

                'sort' => [

                    [
                        '_score' => [
                            'order' => 'desc',
                        ],
                    ],

                    [
                        'created_at' => [
                            'order' => 'desc',
                        ],
                    ],
                ],
            ],
        ];

        $response = $elasticService->getClient()->search($params);

        // default 0
        $totalMatchedCandidates = 0;

        if (
            isset($response['hits']['total']['value']) &&
            $response['hits']['total']['value'] > 0
        ) {

            $totalMatchedCandidates =
                $response['hits']['total']['value'];
        }

        return $totalMatchedCandidates;

    }

    public static function agencyAndRelationshipDetails($candidate_id, $company_id)
    {
        $elasticService = app(ElasticService::class);
        $params = [
            'index' => ELASTIC_CANDIDATES,
            'size' => 1,
            'body' => [
                'query' => [
                    'bool' => [
                        'must' => [
                            ['term' => ['candidate_id' => $candidate_id]],
                            ['term' => ['company_id' => $company_id]],
                        ],
                    ],
                ],
            ],
        ];

        $result = $elasticService->getClient()->search($params);

        $exists = ! empty($result['hits']['hits']);
        $agencyDetails = [];
        $relationShipManager = [];
        if ($exists) {

        }

        return (object) [
            'exists' => $exists,
            'agencyDetails' => $agencyDetails,
            'relationShipManager' => $relationShipManager,
        ];

    }

    public static function agencyJobsCountAllstatus($toCompanyId, $from_company_id)
    {

        $elasticService = app(ElasticService::class);

        $must = [];

        $must[] = [
            'bool' => [
                'must' => [
                    [
                        'nested' => [
                            'path' => 'recommendation_relation',
                            'query' => [
                                'bool' => [
                                    'must' => [
                                        ['term' => ['recommendation_relation.to_company_id' => $toCompanyId]],
                                        ['term' => ['recommendation_relation.from_company_id' => $from_company_id]],
                                        ['term' => ['recommendation_relation.final_status' => 1]],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $params = [
            'index' => ELASTIC_JOBS,
            'body' => [
                'size' => 0,
                'query' => [
                    'bool' => [
                        'must' => $must,
                    ],
                ],
                'aggs' => [
                    'all_jobs' => [
                        'value_count' => [
                            'field' => 'id',
                        ],
                    ],
                    'job_active' => [
                        'filter' => [
                            'term' => [
                                'status' => 'active',
                            ],
                        ],
                    ],
                    'job_inactive' => [
                        'filter' => [
                            'term' => [
                                'status' => 'inactive',
                            ],
                        ],
                    ],
                    'job_hold' => [
                        'filter' => [
                            'term' => [
                                'status' => 'hold',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $response = $elasticService->getClient()->search($params);
        $all_jobs = $response['aggregations']['all_jobs']['value'] ?? 0;
        $job_active = $response['aggregations']['job_active']['doc_count'] ?? 0;
        $job_inactive = $response['aggregations']['job_inactive']['doc_count'] ?? 0;
        $job_hold = $response['aggregations']['job_hold']['doc_count'] ?? 0;

        $recommendedDataListArr = (object) [
            'all_jobs' => $all_jobs,
            'job_active' => $job_active,
            'job_inactive' => $job_inactive,
            'job_hold' => $job_hold,
        ];

        return $recommendedDataListArr;

    }

    public static function agencyCandidatesCountList($to_company_id, $from_company_id)
    {
        $elasticService = app(ElasticService::class);

        $params = [
            'index' => ELASTIC_CANDIDATES,
            'body' => [
                'size' => 0,
                'query' => [
                    'bool' => [
                        'must' => [
                            ['term' => ['from_company_id' => $from_company_id]],
                            ['term' => ['ClientComID' => $to_company_id]],
                            ['term' => ['final_status' => 1]],
                        ],
                        'must_not' => [
                            ['terms' => ['job_master_status_id' => [8, 9]]],
                        ],
                    ],
                ],
                'aggs' => [
                    'all_candidates' => [
                        'value_count' => [
                            'field' => 'id',
                        ],
                    ],
                    'joined' => [
                        'filter' => [
                            'term' => [
                                'job_master_status_id' => 6,
                            ],
                        ],
                    ],
                    'sourced' => [
                        'filter' => [
                            'term' => [
                                'job_master_status_id' => 1,
                            ],
                        ],
                    ],
                    'evaluated' => [
                        'filter' => [
                            'term' => [
                                'job_master_status_id' => 3,
                            ],
                        ],
                    ],
                    'offers' => [
                        'filter' => [
                            'exists' => [
                                'field' => 'of_created_date',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $response = $elasticService->getClient()->search($params);
        $allCandidate = $response['aggregations']['all_candidates']['value'] ?? 0;
        $Joined = $response['aggregations']['joined']['doc_count'] ?? 0;
        $sourced = $response['aggregations']['sourced']['doc_count'] ?? 0;
        $evaluated = $response['aggregations']['evaluated']['doc_count'] ?? 0;
        $offers = $response['aggregations']['offers']['doc_count'] ?? 0;

        $must = [
            'bool' => [
                'must' => [
                    // First condition
                    [
                        'bool' => [
                            'must' => array_merge([
                                ['term' => ['from_company_id' => $from_company_id]],
                                ['term' => ['ClientComID' => $to_company_id]],
                            ]),
                        ],
                    ],
                ],
                'filter' => [], // We'll add all filters here
            ],
        ];

        $params = [
            'index' => ELASTIC_INERTVIEW_TRACKER,
            'body' => [
                'size' => 0,
                'query' => [
                    'bool' => [
                        'must' => $must,
                    ],
                ],
                'aggs' => [
                    'all_interview' => [
                        'value_count' => [
                            'field' => 'id',
                        ],
                    ],
                ],
            ],
        ];

        $response = $elasticService->getClient()->search($params);
        $allInterview = $response['aggregations']['all_interview']['value'] ?? 0;

        $recommendedDataListArr = (object) [
            'all_candidates' => $allCandidate,
            'Joined' => $Joined,
            'offers' => $offers,
            'sourced' => $sourced,
            'evaluated' => $evaluated,
            'all_interview' => $allInterview,
        ];

        return $recommendedDataListArr;
    }
}
