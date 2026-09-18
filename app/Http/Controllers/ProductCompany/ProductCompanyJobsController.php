<?php

/***########################################################
Controller    	: 	AdminJobsController
Purpose			:
Author			:	Abu
Company			:	4Bell
Date			:	05-April-2021
########################################################***/

namespace App\Http\Controllers\ProductCompany;

use App\Exports\JobExport;
use App\Helper\GeneralMethod;
use App\Helper\Reply;
use App\Http\Requests\StoreJob;
use App\Http\Requests\UpdateJob;
use App\Models\ApplicationStatus;
use App\Models\Client;
use App\Models\Company;
use App\Models\Country;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\JobCategory;
use App\Models\JobLocation;
use App\Models\JobSkill;
use App\Models\Question;
use App\Models\Skill;
use App\Notifications\NewJobOpening;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Excel as ExcelExcel;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class ProductCompanyJobsController extends ProductCompanyBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'Requisitions';
        $this->pageIcon = 'icon-badge';
    }

    public function index()
    {

        // echo '<pre>'; print_r($this->userPermissions); exit;
        // $result 			= Job::select('id', 'title')->get();
        // $this->companies = Company::all();
        $company_id = auth()->user()->company_id;
        $this->totalJobs = Job::where('id', '>', '0')->where('jobs.company_id', '=', $company_id)->count();
        $this->activeJobs = Job::where('status', 'active')->count();
        $this->inactiveJobs = Job::where('status', 'inactive')->count();
        $chkAdminRole = DB::table('roles')->select('roles.id')->where('id', $this->user->role->role_id)->where('name', 'admin')->count();
        if ($chkAdminRole > 0) {
            $this->totalJobs = Job::where('id', '>', '0')->where('jobs.company_id', '=', $company_id)->count();
            $this->activeJobs = Job::where('status', 'active')->count();
            $this->inactiveJobs = Job::where('status', 'inactive')->count();
            $this->holdJobs = Job::where('status', 'hold')->count();
        } else {
            $this->totalJobs = Job::join('job_assign', 'job_assign.job_id', 'jobs.id')->where('jobs.company_id', '=', $company_id)->where('jobs.id', '>', '0')->where('job_assign.user_id', '=', $this->user->id)->count();
            $this->activeJobs = Job::join('job_assign', 'job_assign.job_id', 'jobs.id')->where('status', 'active')->where('job_assign.user_id', '=', $this->user->id)->count();
            $this->inactiveJobs = Job::join('job_assign', 'job_assign.job_id', 'jobs.id')->where('status', 'inactive')->where('job_assign.user_id', '=', $this->user->id)->count();
            $this->holdJobs = Job::join('job_assign', 'job_assign.job_id', 'jobs.id')->where('status', 'hold')->where('job_assign.user_id', '=', $this->user->id)->count();
        }

        return view('product_company.jobs.index', $this->data);
    }

    public function create1()
    {

        abort_if(! $this->user->cans('add_jobs'), 403);
        $activeJobsCount = Job::where('status', 'active')->count();
        if ($activeJobsCount > 0 && $this->activePackage->package->no_of_job_openings !== null && $this->activePackage->package->no_of_job_openings <= $activeJobsCount) {
            $this->message = __('modules.subscription.jobPurchase');

            return view('product_company.subscription.ask_purchase', $this->data);
        }
        $this->categories = JobCategory::all();
        $this->locations = JobLocation::all();
        $this->questions = Question::all();
        $this->companies = Company::all();

        return view('product_company.jobs.create', $this->data);

    }

    public function create()
    {

        $this->pageTitle = __('Add Requisition');

        abort_if(! $this->user->cans('add_jobs'), 403);

        // echo $this->company_type;
        // echo '<pre>'; print_r($this->user);
        // exit;

        $company_id = auth()->user()->company_id;
        $activeJobsCount = Job::where('status', 'active')->count();
        if ($activeJobsCount > 0 && $this->activePackage->package->no_of_job_openings !== null && $this->activePackage->package->no_of_job_openings <= $activeJobsCount) {
            $this->message = __('modules.subscription.jobPurchase');

            return view('product_company.subscription.ask_purchase', $this->data);
        }

        $this->categories = JobCategory::all();
        $this->questions = Question::all();
        $this->companies = Company::all();
        $this->locations = JobLocation::all();
        $this->client = Client::select('*')->where('company_id', $company_id)->orderBy('clients.id', 'ASC')->get();

        $this->skills = Skill::all();
        $this->consultancy = DB::table('users')->select('id', 'name')->where('type', 1)->where('company_id', $company_id)->where('status', 'active')->get();
        $this->team = DB::table('users')->select('id', 'name')->where('type', 0)->where('company_id', $company_id)->where('status', 'active')->get();

        $this->employmentType = GeneralMethod::employmentType();
        $this->priority = GeneralMethod::priority();
        $this->qualification = DB::table('qualification_master')
            ->select('*')
            ->orderBy('qualification_master.display_order', 'ASC')
            ->get();

        // echo '<pre>'; print_r($this->employmentType); exit;

        // echo $this->user->company_id;
        $this->other_consultancy = DB::table('invitation_consultancy')
            ->join('users', 'users.id', '=', 'invitation_consultancy.parent_id')
            ->join('companies', 'companies.id', '=', 'users.company_id')
            ->select('users.id', 'users.name', 'companies.company_name', 'companies.id as other_company_id')
            ->where('invitation_consultancy.company_id', '=', $this->user->company_id)
            ->orderBy('users.name', 'ASC')
            ->get();

        $this->jobLocation = [];
        $this->jobCity = DB::table('city_list')->get();
        $this->jobCitySelected = [];

        $this->countries = DB::table('countries')->get();

        $this->valSelected = [];

        $this->page_event = 'ADD';
        $this->currency = GeneralMethod::currency();
        $this->salaryMode = GeneralMethod::salaryMode();

        // echo '<pre>'; print_r($this->currencySymbole); exit;

        return view('product_company.jobs.create', $this->data);
    }

    public function store(Request $request)
    {

        abort_if(! $this->user->cans('add_jobs'), 403);

        $job = new Job;
        $job->slug = null;
        $job->company_id = $this->user->company_id;
        $job->status = 'active';

        $job->client_id = @$request->client_id;
        $job->title = @$request->title;
        $job->category_id = @$request->category_id;
        if (! empty(@$request->employment_type)) {
            $job->employment_type = @$request->employment_type;
        }
        if (! empty(@$request->total_positions)) {
            $job->total_positions = @$request->total_positions;
        }
        if (! empty(@$request->min_exp)) {
            $job->min_exp = @$request->min_exp;
        }
        if (! empty(@$request->max_exp)) {
            $job->max_exp = @$request->max_exp;
        }
        if (! empty(@$request->qualification)) {
            $job->qualification = @$request->qualification;
        }
        $job->start_date = date('Y-m-d');
        $job->end_date = @$request->end_date;
        $job->priority = 0;
        $job->location_id = 1;
        $job->meta_details = [
            'title' => @$request->meta_title ?: @$request->title,
            'description' => @$request->meta_description ?: strip_tags(Str::substr(html_entity_decode(@$request->job_description), 0, 150)),
        ];
        $job->min_salary_currency = @$request->min_salary_currency;
        $job->min_salary = @$request->minimum_salary;
        $job->min_sal_mode = @$request->min_sal_mode;

        $job->max_salary_currency = @$request->max_salary_currency;
        $job->max_salary = @$request->maximum_salary;
        $job->max_sal_mode = @$request->max_sal_mode;
        if (! empty(@$request->detail_description)) {
            $job->job_description = @$request->detail_description;
        }
        $job->carrier_website = @$request->carrier_website;
        $job->work_type = @$request->work_from_home;
        $job->show_vendor = @$request->show_client_name;
        $job->save();

        // Insert Job Location
        if (! is_null($request->kt_select2_3)) {
            foreach ($request->kt_select2_3 as $ld) {
                $insertArr = ['company_id' => $this->user->company_id, 'client_location_id' => $ld, 'job_id' => $job->id];
                DB::table('client_location_jobs')->insert($insertArr);
            }
        }
        if (! is_null($request->kt_select2_2)) {
            foreach ($request->kt_select2_2 as $ld) {
                $insertArrCity = ['company_id' => $this->user->company_id, 'city_id' => $ld, 'job_id' => $job->id];
                DB::table('client_city_jobs')->insert($insertArrCity);
            }
        }

        if (! is_null($request->kt_select2_4)) {
            foreach ($request->kt_select2_4 as $ld) {
                $insertArrCountry = ['company_id' => $this->user->company_id, 'country_id' => $ld, 'job_id' => $job->id];
                DB::table('job_countries')->insert($insertArrCountry);
            }
        }

        // Insert Skills
        if (! empty(@$request->tags)) {
            $tags = @$request->tags;
            $tags = json_decode($tags, true);
            $tagsArr = array_column($tags, 'value');
            foreach ($tagsArr as $key => $value) {
                $insertArr = ['company_id' => $this->user->company_id, 'job_id' => $job->id, 'skill_name' => $value];
                DB::table('job_metronics_skills')->insert($insertArr);

            }

        }

        // Job additional detail
        if (! is_null($request->custom_title)) {
            foreach ($request->custom_title as $adArr) {
                if (! empty($adArr)) {
                    $values = [
                        'company_id' => $this->user->company_id,
                        'job_id' => $job->id,
                        'detail_value' => $adArr,
                    ];
                    DB::table('job_additional_detail')->insert($values);
                }
            }
        }

        // Assign to Team
        if (! is_null($request->recruiter)) {
            $recruiterArr = '';
            $recruiterArr = explode(',', $request->recruiter);
            foreach ($recruiterArr as $recArr) {
                $recRow = DB::table('users')->select('users.id')->where('company_id', $this->user->company_id)->where('name', trim($recArr))->first();
                if (! empty(@$recRow->id) && @$recRow->id > 0) {
                    $values = ['company_id' => $this->user->company_id, 'user_id' => $recRow->id, 'job_id' => $job->id];
                    DB::table('job_assign')->insert($values);
                }
            }
        }

        // Insert Client Contact Person
        if (! is_null($request->hiring_namager)) {
            $hiringManagerArr = '';
            $recruiterArr = explode(',', $request->hiring_namager);
            foreach ($recruiterArr as $cp) {
                $recRow = DB::table('client_contact_info')->select('client_contact_info.id')->where('client_id', @$request->client_id)->where('contact_person_name', trim($cp))->first();
                if (! empty(@$recRow->id) && @$recRow->id > 0) {
                    $insertArr = ['company_id' => $this->user->company_id, 'client_contact_info_id' => $recRow->id, 'job_id' => $job->id];
                    DB::table('client_contact_info_jobs')->insert($insertArr);
                }
            }
        }

        // echo '<pre>'; print_r($_REQUEST);
        // exit;

        /*if (is_null($request->job_description)) {
            return Reply::error(__('messages.job_description'));
        }

        if (is_null($request->job_requirement))
        {
            return Reply::error(__('messages.job_requirement'));
        }

        $required_columns =
                            [
                                'gender' 	=> false,
                                'dob' 		=> false,
                                'country' 	=> false
                            ];

        foreach ($required_columns as $key => $value )
        {
            if ($request->has($key))
            {
                $required_columns[$key] = true;
            }
        }

        $section_visibility =
                                [
                                    'profile_image' 		=> 'no',
                                    'resume' 				=> 'no',
                                    'cover_letter' 			=> 'no',
                                    'terms_and_conditions' 	=> 'no'
                                ];

        foreach ($section_visibility as $key => $value )
        {
           if ($request->has($key))
           {
                $section_visibility[$key] = 'yes';
           }
        }*/

        // $job->job_description 		= $request->job_description;
        // $job->job_requirement 		= $request->job_requirement;
        // $job->total_positions 		= $request->total_positions;

        // $job->location_id 			= $request->location_id;

        // $job->required_columns 		= $required_columns;
        // $job->section_visibility 	= $section_visibility;

        /*if(!empty($request->priority))
        {
            $job->priority 				= $request->priority;
        }*/

        /*if(!empty($request->min_salary))
        {
            $job->min_salary 			= $request->min_salary;
        }
        if(!empty($request->max_salary))
        {
            $job->max_salary 			= $request->max_salary;
        }*/

        /*if (!is_null($request->skill_id))
        {
            JobSkill::where('job_id', $job->id)->delete();

            foreach ($request->skill_id as $skill)
            {
                $jobSkill 				= new JobSkill();
                $jobSkill->skill_id 	= $skill;
                $jobSkill->job_id 		= $job->id;
                $jobSkill->save();
            }
        }*/

        // Assign to consultancy
        /*if (!is_null($request->consultancy))
        {
            foreach ($request->consultancy as $constArr)
            {
                $values = array('company_id' => $this->user->company_id,	'user_id' => $constArr, 'job_id' => $job->id);
                DB::table('job_assign')->insert($values);
            }
        }*/

        // Save Question for job
        // $job->questions()->sync($request->question);
        // echo '<pre>'; print_r($_REQUEST); exit;
        return Reply::redirect(route('product_company.jobs.index'), __('menu.jobs').' '.__('messages.createdSuccessfully'));
    }

    public function store_2(StoreJob $request)
    {

        abort_if(! $this->user->can('add_jobs'), 403);
        // echo '<pre>'; print_r($_REQUEST);
        // exit;
        $job = new Job;
        $job->slug = null;
        $job->company_id = $this->user->company_id;
        $job->status = $request->status;
        $job->title = $request->title;
        $job->job_description = $request->job_description;
        $job->job_requirement = $request->job_requirement;
        $job->category_id = $request->category_id;
        $job->start_date = $request->start_date;
        $job->end_date = $request->end_date;
        // $job->location_id 		= $request->location_id;
        $job->location_id = 5;
        $job->client_id = $request->client_id;
        if (! empty($request->total_positions)) {
            $job->total_positions = $request->total_positions;
        }
        if (! empty($request->employment_type)) {
            $job->employment_type = $request->employment_type;
        }
        if (! empty($request->priority)) {
            $job->priority = $request->priority;
        }
        if (! empty($request->min_exp)) {
            $job->min_exp = $request->min_exp;
        }
        if (! empty($request->max_exp)) {
            $job->max_exp = $request->max_exp;
        }
        if (! empty($request->min_salary)) {
            $job->min_salary = $request->min_salary;
        }
        if (! empty($request->max_salary)) {
            $job->max_salary = $request->max_salary;
        }
        if (! empty($request->qualification)) {
            $job->qualification = $request->qualification;
        }

        if (! empty($request->detail_description)) {
            $job->detail_description = $request->detail_description;
        }
        $job->save();

        if (! is_null($request->skill_id)) {
            JobSkill::where('job_id', $job->id)->delete();
            foreach ($request->skill_id as $skill) {
                $jobSkill = new JobSkill;
                $jobSkill->skill_id = $skill;
                $jobSkill->job_id = $job->id;
                $jobSkill->save();
            }
        }
        // Insert Job Address
        if (! is_null($request->contact_person)) {
            foreach ($request->contact_person as $cp) {
                $insertArr = [
                    'company_id' => $this->user->company_id,
                    'client_contact_info_id' => $cp,
                    'job_id' => $job->id,
                ];
                DB::table('client_contact_info_jobs')->insert($insertArr);
            }
        }
        // Insert reporting User
        if (! is_null($request->location_id)) {
            foreach ($request->location_id as $ld) {
                $insertArr = [
                    'company_id' => $this->user->company_id,
                    'client_location_id' => $ld,
                    'job_id' => $job->id,
                ];
                DB::table('client_location_jobs')->insert($insertArr);
            }
        }
        // Save Question for job
        if (! is_null($request->question)) {
            JobQuestion::where('job_id', $job->id)->delete();
            foreach ($request->question as $question) {
                $jobQuestion = new JobQuestion;
                $jobQuestion->question_id = $question;
                $jobQuestion->job_id = $job->id;
                $jobQuestion->save();
            }
        }
        // Assign to consultancy
        if (! is_null($request->consultancy)) {
            foreach ($request->consultancy as $constArr) {
                $values = [
                    'company_id' => $this->user->company_id,
                    'user_id' => $constArr,
                    'job_id' => $job->id,
                ];
                DB::table('job_assign')->insert($values);
            }
        }
        // Assign to Team
        if (! is_null($request->team)) {
            foreach ($request->team as $teamArr) {
                $values = [
                    'company_id' => $this->user->company_id,
                    'user_id' => $teamArr,
                    'job_id' => $job->id,
                ];
                DB::table('job_assign')->insert($values);
            }
        }
        $id = $job->id;
        // $id 	='';
        if (! empty($id) && $_SERVER['HTTP_HOST'] != '127.0.0.1') {
            $company = DB::table('companies')->select('*')->where('id', $this->user->company_id)->first();
            $job = Job::findOrFail($id);
            $userArr = DB::table('users')
                ->join('job_assign', 'job_assign.user_id', '=', 'users.id')
                ->where('job_assign.job_id', '=', $id)
                ->get()->toArray();

            if (! empty($userArr)) {
                foreach ($userArr as $uData) {
                    $email = $name = $company_id = '';
                    $name = $uData->name;
                    $email = $uData->email;
                    $user = ['name' => @$uData->name, 'email' => @$uData->email, 'company' => $company->company_name, 'job_title' => @$job->title];
                    Mail::send('email.job-created', ['user' => $user], function ($message) use ($user) {
                        $message->from('no-reply@vibhanshujaiswal.com', 'ATS');
                        $message->to($user['email'], $user['name']);
                        $message->subject('New job has been posted!');
                    });
                }
            }

            $adminUserArr = DB::table('roles')
                ->select('users.*')
                ->join('role_user', 'role_user.role_id', '=', 'roles.id')
                ->join('users', 'users.id', '=', 'role_user.user_id')
                ->where('roles.company_id', '=', $this->user->company_id)
                ->where('roles.name', '=', 'admin')
                ->where('roles.display_name', '=', 'Administrator')
                ->where('users.type', '=', 0)
                ->get();
            if (! empty($adminUserArr)) {
                foreach ($adminUserArr as $dArr) {
                    $email = $name = $company_id = '';
                    $name = $uData->name;
                    $email = $uData->email;
                    $user = ['name' => @$dArr->name, 'email' => @$dArr->email, 'company' => $company->company_name, 'job_title' => @$job->title];
                    Mail::send('email.job-created', ['user' => $user], function ($message) use ($user) {
                        $message->from('no-reply@vibhanshujaiswal.com', 'ATS');
                        $message->to($user['email'], $user['name']);
                        $message->subject('New job has been posted!');
                    });
                }
            }
        }

        return Reply::redirect(route('product_company.jobs.index'), __('menu.jobs').' '.__('messages.createdSuccessfully'));
    }

    public function show($id)
    {
        $this->job = Job::find($id);

        return $this->job;
    }

    public function edit($id)
    {

        $this->pageTitle = __('Edit Requisition');

        abort_if(! $this->user->cans('edit_jobs'), 403);

        $this->job = Job::findOrFail($id);
        $this->categories = JobCategory::all();
        $this->locations = JobLocation::all();
        // $this->skills 			=	Skill::where('category_id', $this->job->category_id)->get();
        $this->skills = Skill::all();
        $this->jobQuestion = $this->job->questions->pluck('id')->toArray();
        $this->questions = Question::all();
        $company_id = $this->user->company_id;

        $this->consultancy = DB::table('users')->select('id', 'name')->where('type', 1)->where('company_id', $company_id)->where('status', 'active')->get();

        $this->team = DB::table('users')->select('id', 'name')->where('type', 0)->where('company_id', $company_id)->where('status', 'active')->get();

        $this->lArr = '';
        $this->uArr = '';
        if ($this->job->client_id > 0) {
            $this->lArr = DB::table('client_location')
                ->join('countries', 'countries.id', '=', 'client_location.country_id')
                ->join('clients', 'clients.id', '=', 'client_location.client_id')
                ->select('client_location.id', 'client_location.city', 'client_location.address', 'client_location.postcode', 'countries.country_name')
                ->where('client_location.client_id', $this->job->client_id)
                ->get();

            $this->uArr = DB::table('client_contact_info')
            // ->join('countries', 'countries.id', '=', 'client_location.country_id')
                ->join('clients', 'clients.id', '=', 'client_contact_info.client_id')
                ->select('client_contact_info.id', 'client_contact_info.contact_person_name')
                ->where('clients.company_id', $this->job->client_id)
                ->get();
        }
        $this->client = Client::all();
        $this->employmentType = GeneralMethod::employmentType();
        $this->priority = GeneralMethod::priority();
        $this->locations = JobLocation::all();
        $this->qualification = DB::table('qualification_master')
            ->select('*')
            ->orderBy('qualification_master.display_order', 'ASC')
            ->get();

        $this->jobAssign = DB::table('job_assign')
            ->select('user_id')
            ->where('company_id', $company_id)
            ->where('job_id', $id)
            ->get();
        $this->additionalDetailCount = DB::table('job_additional_detail')->select('*')->where('company_id', $company_id)->where('job_id', $id)->count();

        // echo '<pre>'; print_r($this->additionalDetail); exit;
        // return view('product_company.jobs.edit', $this->data);

        // CONTACT PERSON
        $this->contactPersonList = DB::table('client_contact_info_jobs')
            ->join('client_contact_info', 'client_contact_info.id', '=', 'client_contact_info_jobs.client_contact_info_id')
            ->select('client_contact_info.contact_person_name')
            ->where('client_contact_info_jobs.job_id', $id)
            ->get();

        // RECRUITER LIST
        $this->recruiterlist = DB::table('job_assign')
            ->join('users', 'users.id', '=', 'job_assign.user_id')
            ->select('users.name')
            ->where('job_assign.company_id', $company_id)
            ->where('job_assign.job_id', $id)
            ->where('users.type', '=', 0)
            ->get();

        $this->additionalDetail = DB::table('job_additional_detail')->select('*')->where('company_id', $company_id)->where('job_id', $id)->get()->toArray();
        $this->job_metronics_skills = DB::table('job_metronics_skills')->select('skill_name')->where('company_id', $company_id)->where('job_id', $id)->get();
        $this->page_event = 'EDIT';
        $this->currency = GeneralMethod::currency();
        $this->salaryMode = GeneralMethod::salaryMode();

        $this->jobLocation = DB::table('client_location_jobs')
            ->where('client_location_jobs.company_id', $company_id)
            ->where('client_location_jobs.job_id', $id)
            ->pluck('client_location_jobs.client_location_id')
            ->toArray();

        $this->jobCity = DB::table('city_list')->get();

        $this->jobCitySelected = DB::table('client_city_jobs')
            ->where('client_city_jobs.company_id', $company_id)
            ->where('client_city_jobs.job_id', $id)
            ->pluck('client_city_jobs.city_id')
            ->toArray();

        $this->countries = DB::table('countries')->get();

        $this->valSelected = DB::table('job_countries')
            ->where('job_countries.company_id', $company_id)
            ->where('job_countries.job_id', $id)
            ->pluck('job_countries.country_id')
            ->toArray();

        // echo '<pre>'; print_r($this->client_location_jobs); exit;

        return view('product_company.jobs.create', $this->data);

    }

    public function update(StoreJob $request, $id)
    {

        abort_if(! $this->user->cans('edit_jobs'), 403);

        $job = Job::findOrFail($id);
        $job->client_id = @$request->client_id;
        $job->title = @$request->title;
        $job->category_id = @$request->category_id;
        if (! empty(@$request->employment_type)) {
            $job->employment_type = @$request->employment_type;
        }
        if (! empty(@$request->total_positions)) {
            $job->total_positions = @$request->total_positions;
        }
        if (! empty(@$request->min_exp)) {
            $job->min_exp = @$request->min_exp;
        }
        if (! empty(@$request->max_exp)) {
            $job->max_exp = @$request->max_exp;
        }
        if (! empty(@$request->qualification)) {
            $job->qualification = @$request->qualification;
        }
        // $job->start_date 				= date('Y-m-d');
        $job->end_date = @$request->end_date;
        $job->priority = 0;
        $job->location_id = 1;
        $job->meta_details = [
            'title' => @$request->meta_title ?: @$request->title,
            'description' => @$request->meta_description ?: strip_tags(Str::substr(html_entity_decode(@$request->job_description), 0, 150)),
        ];
        $job->min_salary_currency = @$request->min_salary_currency;
        $job->min_salary = @$request->minimum_salary;
        $job->min_sal_mode = @$request->min_sal_mode;

        $job->max_salary_currency = @$request->max_salary_currency;
        $job->max_salary = @$request->maximum_salary;
        $job->max_sal_mode = @$request->max_sal_mode;
        $job->carrier_website = @$request->carrier_website;

        if (! empty(@$request->detail_description)) {
            $job->job_description = @$request->detail_description;
        }
        $job->work_type = @$request->work_from_home;
        $job->show_vendor = @$request->show_client_name;
        $job->save();

        // UPDATE JOB LOCATION
        DB::table('client_location_jobs')->where('job_id', $job->id)->delete();
        if (! is_null($request->kt_select2_3)) {
            foreach ($request->kt_select2_3 as $ld) {
                $insertArr = ['company_id' => $this->user->company_id, 'client_location_id' => $ld, 'job_id' => $job->id];
                DB::table('client_location_jobs')->insert($insertArr);
            }
        }
        DB::table('client_city_jobs')->where('job_id', $job->id)->delete();
        if (! is_null($request->kt_select2_2)) {
            foreach ($request->kt_select2_2 as $ld) {
                $insertArrCity = ['company_id' => $this->user->company_id, 'city_id' => $ld, 'job_id' => $job->id];
                DB::table('client_city_jobs')->insert($insertArrCity);
            }
        }

        DB::table('job_countries')->where('job_id', $job->id)->delete();
        if (! is_null($request->kt_select2_4)) {
            foreach ($request->kt_select2_4 as $ld) {
                $insertArrCountry = ['company_id' => $this->user->company_id, 'country_id' => $ld, 'job_id' => $job->id];
                DB::table('job_countries')->insert($insertArrCountry);
            }
        }

        // UPDATE HIRING MANAGER
        DB::table('client_contact_info_jobs')->where('job_id', $job->id)->delete();
        if (! is_null($request->hiring_namager)) {
            $hiringManagerArr = '';
            $recruiterArr = explode(',', $request->hiring_namager);
            foreach ($recruiterArr as $cp) {
                $recRow = DB::table('client_contact_info')->select('client_contact_info.id')->where('client_id', @$request->client_id)->where('contact_person_name', trim($cp))->first();
                if (! empty(@$recRow->id) && @$recRow->id > 0) {
                    $insertArr = ['company_id' => $this->user->company_id, 'client_contact_info_id' => $recRow->id, 'job_id' => $job->id];
                    DB::table('client_contact_info_jobs')->insert($insertArr);
                }
            }
        }
        // UPDATE RECRUITER
        DB::table('job_assign')->where('job_id', $job->id)->delete();
        if (! is_null($request->recruiter)) {
            $recruiterArr = '';
            $recruiterArr = explode(',', $request->recruiter);
            foreach ($recruiterArr as $recArr) {
                $recRow = DB::table('users')->select('users.id')->where('company_id', $this->user->company_id)->where('name', trim($recArr))->first();
                if (! empty(@$recRow->id) && @$recRow->id > 0) {
                    $values = ['company_id' => $this->user->company_id, 'user_id' => $recRow->id, 'job_id' => $job->id];
                    DB::table('job_assign')->insert($values);
                }
            }
        }
        // UPDATE  SKILLS
        DB::table('job_metronics_skills')->where('job_id', $job->id)->delete();
        JobSkill::where('job_id', $job->id)->delete();
        if (! empty(@$request->tags)) {
            $tags = @$request->tags;
            $tags = json_decode($tags, true);
            $tagsArr = array_column($tags, 'value');
            foreach ($tagsArr as $key => $value) {
                $insertArr = ['company_id' => $this->user->company_id, 'job_id' => $job->id, 'skill_name' => $value];
                DB::table('job_metronics_skills')->insert($insertArr);

            }
        }
        // Job ADDITIONAL DETAIL
        DB::table('job_additional_detail')->where('job_id', $job->id)->delete();
        if (! is_null($request->custom_title)) {
            foreach ($request->custom_title as $adArr) {
                if (! empty($adArr)) {
                    $values = [
                        'company_id' => $this->user->company_id,
                        'job_id' => $job->id,
                        'detail_value' => $adArr,
                    ];
                    DB::table('job_additional_detail')->insert($values);
                }
            }
        }

        // echo '<pre>'; print_r($_REQUEST); exit;
        /*$required_columns =
                            [
                                'gender' 	=> false,
                                'dob' 		=> false,
                                'country' 	=> false
                            ];
        foreach ($required_columns as $key => $value )
            {
                if ($request->has($key))
                {
                    $required_columns[$key] = true;
                }
            }*/
        /*$section_visibility =
                                [
                                    'profile_image' 		=> 'no',
                                    'resume' 				=> 'no',
                                    'cover_letter' 			=> 'no',
                                    'terms_and_conditions' 	=> 'no'
                                ];

        foreach ($section_visibility as $key => $value )
        {
            if ($request->has($key))
            {
              $section_visibility[$key] = 'yes';
            }
        }*/
        // $job->job_description 		= $request->job_description;
        // $job->job_requirement 		= $request->job_requirement;
        // $job->location_id 			= $request->location_id;
        // $job->start_date 				= $request->start_date;
        // $job->end_date 				= $request->end_date;
        // $job->status 					= $request->status;
        // $job->required_columns 		= $required_columns;
        // $job->section_visibility 		= $section_visibility;
        /*$job->meta_details 			=
                                        [
                                            'title' 		=> $request->meta_title ?: $job->title,
                                            'description' 	=> $request->meta_description ?: strip_tags(Str::substr(html_entity_decode($job->job_description), 0, 150))
                                        ];*/
        /*if(!empty($request->priority))
        {
            $job->priority 			= $request->priority;
        }*/
        /*if(!empty($request->min_salary))
        {
            $job->min_salary 		= $request->min_salary;
        }
        if(!empty($request->max_salary))
        {
            $job->max_salary 		= $request->max_salary;
        }*/

        /*if(!empty($request->detail_description))
        {
            $job->detail_description = $request->detail_description;
        }*/

        /*if (!is_null($request->skill_id))
        {
            JobSkill::where('job_id', $job->id)->delete();
            foreach ($request->skill_id as $skill)
            {
                $jobSkill = new JobSkill();
                $jobSkill->skill_id = $skill;
                $jobSkill->job_id = $job->id;
                $jobSkill->save();
            }
        }*/

        // Update Question for job
        /*$job->questions()->sync($request->question);
        DB::table('client_contact_info_jobs')->where('job_id', $job->id)->delete();
        // Insert Job Address
        if (!is_null($request->contact_person))
        {
            foreach ($request->contact_person as $cp)
            {
                $insertArr = array(
                'company_id' 				=> $this->user->company_id,
                'client_contact_info_id'	=> $cp,
                'job_id' 					=> $job->id
                );
                DB:: table('client_contact_info_jobs')->insert($insertArr);
            }
        }*/
        /*DB::table('client_location_jobs')->where('job_id', $job->id)->delete();
        // Insert reporting User
        if (!is_null($request->location_id))
        {
            foreach ($request->location_id as $ld)
            {
                 $insertArr = array(
                'company_id' 			=> $this->user->company_id,
                'client_location_id' 	=> $ld,
                'job_id' 				=> $job->id
                );
                DB:: table('client_location_jobs')->insert($insertArr);
            }
        }*/

        /*if($request->status == 'active')
        {
            DB::table('job_assign')->where('job_id', $job->id)->delete();
            if (!is_null($request->consultancy))
            {
                foreach ($request->consultancy as $constArr)
                {
                    $values = array(
                    'company_id' 		=> $this->user->company_id,
                    'user_id' 		 	=> $constArr,
                    'job_id' 		 	=> $job->id
                );
                DB::table('job_assign')->insert($values);
                }
            }
            if (!is_null($request->team))
            {
                foreach ($request->team as $teamArr)
                {
                    $values = array(
                    'company_id' 		=> $this->user->company_id,
                    'user_id' 		 	=> $teamArr,
                    'job_id' 		 	=> $job->id
                );
                DB::table('job_assign')->insert($values);
                }
            }
        }*/

        return Reply::redirect(route('product_company.jobs.index'), __('menu.jobs').' '.__('messages.updatedSuccessfully'));

    }

    public function destroy($id)
    {
        abort_if(! $this->user->cans('delete_jobs'), 403);
        Job::destroy($id);

        return Reply::success(__('messages.recordDeleted'));
    }

    public function data_old()
    {
        abort_if(! $this->user->cans('view_jobs'), 403);
        $categories = Job::where('id', '>', '0');
        if (\request('filter_company') != '') {
            $categories->where('company_id', \request('filter_company'));
        }
        if (\request('filter_status') != '') {
            $categories->where('status', \request('filter_status'));
        }
        $categories->get();

        return DataTables::of($categories)
            ->addColumn('action', function ($row) {
                $params = module_enabled('Subdomain') ? [$row->slug] : [$row->company->career_page_link, $row->slug];
                $action = '';
                if ($this->user->cans('edit_jobs')) {
                    $action .= '<a href="'.route('product_company.jobs.edit', [$row->id]).'" class="btn btn-primary btn-circle"
                data-toggle="tooltip" data-original-title="'.__('app.edit').'"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
                }
                $action .= ' <a href="javascript:;" class="btn btn-info btn-circle open-url"
            data-toggle="tooltip" data-row-open-url="'.route('jobs.jobDetail', $params).'" data-original-title="'.__('app.copyUrl').'"><i class="fa fa-copy" aria-hidden="true"></i></a>';

                if ($this->user->cans('delete_jobs')) {
                    $action .= ' <a href="javascript:;" class="btn btn-danger btn-circle sa-params"
                    data-toggle="tooltip" data-row-id="'.$row->id.'" data-original-title="'.__('app.delete').'"><i class="fa fa-times" aria-hidden="true"></i></a>';
                }
                if ($row->status == 'expired') {
                    $action .= ' <a href="javascript:;" class="btn btn-circle expire_modal" style="background: #FF8C00; color: white;"
                    data-toggle="tooltip" data-row-id="'.$row->id.'" data-original-title="'.__('app.refresh').'"><i class="fa fa-refresh" aria-hidden="true"></i></a>';
                }

                return $action;
            })
            ->editColumn('title', function ($row) {
                return ucfirst($row->title);
            })
            ->editColumn('location_id', function ($row) {
                return ucfirst($row->location->location).' ('.$row->location->country->country_code.')';

            })

            ->editColumn('start_date', function ($row) {
                return $row->start_date->format('d M, Y');
            })

            ->editColumn('end_date', function ($row) {
                return $row->end_date->format('d M, Y');
            })

            ->editColumn('status', function ($row) {
                if ($row->status == 'active') {
                    return '<label class="badge bg-success">'.__('app.active').'</label>';
                }

                if ($row->status == 'inactive') {
                    return '<label class="badge bg-danger">'.__('app.inactive').'</label>';
                }
                if ($row->status == 'expired') {
                    return '<label class="badge" style="background: #FF8C00;">'.__('app.expired').'</label>';
                }
            })
            ->rawColumns(['status', 'action'])
            ->addIndexColumn()
            ->make(true);
    }

    public function data(Request $request)
    {

        abort_if(! $this->user->cans('view_jobs'), 403);

        $company_id = auth()->user()->company_id;

        $chkAdminRole = DB::table('roles')->select('roles.id')->where('id', $this->user->role->role_id)->where('name', 'admin')->count();
        $job_status = GeneralMethod::job_status();
        $salaryMode = GeneralMethod::salaryMode();
        $currencySymbole = GeneralMethod::currencySymbole();

        if ($chkAdminRole > 0) {
            $requisitionQuery = Job::select(
                'jobs.id', 'jobs.title', 'jobs.work_type', 'jobs.total_positions', 'jobs.priority', 'jobs.status', 'jobs.min_exp', 'jobs.max_exp', 'jobs.created_at', 'jobs.start_date', 'jobs.end_date',
                'jobs.min_salary', 'jobs.max_salary', 'jobs.min_sal_mode', 'jobs.max_sal_mode',
                'jobs.min_salary_currency', 'jobs.max_salary_currency',
                'clients.name as clients_name', 'clients.logo_logical_path',
                DB::raw('COUNT(job_applications.id) as allCandidate'),
                DB::raw('SUM(CASE WHEN job_applications.master_status_id = 1 THEN 1 ELSE 0 END) AS Sourcing'),
                DB::raw('SUM(CASE WHEN job_applications.master_status_id = 2 THEN 1 ELSE 0 END) AS Screening'),
                DB::raw('SUM(CASE WHEN job_applications.master_status_id = 3 THEN 1 ELSE 0 END) AS Interview'),
                DB::raw('SUM(CASE WHEN job_applications.master_status_id = 4 THEN 1 ELSE 0 END) AS Negotiation'),
                DB::raw('SUM(CASE WHEN job_applications.master_status_id = 5 THEN 1 ELSE 0 END) AS Offered'),
                DB::raw('SUM(CASE WHEN job_applications.master_status_id = 6 THEN 1 ELSE 0 END) AS Joined'),
                DB::raw('SUM(CASE WHEN job_applications.master_status_id = 7 THEN 1 ELSE 0 END) AS Rejected'),
                // DB::raw("SUM(CASE WHEN job_applications.master_status_id = 8 THEN 1 ELSE 0 END) AS InReview")
                DB::raw('SUM(CASE WHEN job_applications.master_status_id = 8 AND job_applications.thirdparty_company_id = 0 THEN 1 ELSE 0 END) AS InReview')
            )
                ->join('clients', 'clients.id', 'jobs.client_id')
                ->leftJoin('add_job_wise_condidates as job_applications', 'job_applications.job_id', '=', 'jobs.id')
                ->where('jobs.id', '>', '0')
                ->take(1);

        } else {

            if (in_array('all_requisition', $this->userPermissions) && in_array('view_jobs', $this->userPermissions)) {
                $requisitionQuery = Job::select(
                    'jobs.id', 'jobs.title', 'jobs.work_type', 'jobs.total_positions', 'jobs.priority', 'jobs.status', 'jobs.min_exp', 'jobs.max_exp', 'jobs.created_at', 'jobs.start_date', 'jobs.end_date',
                    'jobs.min_salary', 'jobs.max_salary', 'jobs.min_sal_mode', 'jobs.max_sal_mode',
                    'jobs.min_salary_currency', 'jobs.max_salary_currency',
                    'clients.name as clients_name', 'clients.logo_logical_path',
                    DB::raw('COUNT(job_applications.id) as allCandidate'),
                    DB::raw('SUM(CASE WHEN job_applications.master_status_id = 1 THEN 1 ELSE 0 END) AS Sourcing'),
                    DB::raw('SUM(CASE WHEN job_applications.master_status_id = 2 THEN 1 ELSE 0 END) AS Screening'),
                    DB::raw('SUM(CASE WHEN job_applications.master_status_id = 3 THEN 1 ELSE 0 END) AS Interview'),
                    DB::raw('SUM(CASE WHEN job_applications.master_status_id = 4 THEN 1 ELSE 0 END) AS Negotiation'),
                    DB::raw('SUM(CASE WHEN job_applications.master_status_id = 5 THEN 1 ELSE 0 END) AS Offered'),
                    DB::raw('SUM(CASE WHEN job_applications.master_status_id = 6 THEN 1 ELSE 0 END) AS Joined'),
                    DB::raw('SUM(CASE WHEN job_applications.master_status_id = 7 THEN 1 ELSE 0 END) AS Rejected'),
                    // DB::raw("SUM(CASE WHEN job_applications.master_status_id = 8 THEN 1 ELSE 0 END) AS InReview")
                    DB::raw('SUM(CASE WHEN job_applications.master_status_id = 8 AND job_applications.thirdparty_company_id = 0 THEN 1 ELSE 0 END) AS InReview')
                )
                    ->join('clients', 'clients.id', 'jobs.client_id')
                    ->leftJoin('add_job_wise_condidates as job_applications', 'job_applications.job_id', '=', 'jobs.id')
                    ->where('jobs.id', '>', '0')
                    ->take(1);
            } else {
                $requisitionQuery = Job::select(
                    'jobs.id', 'jobs.title', 'jobs.work_type', 'jobs.total_positions', 'jobs.priority', 'jobs.status', 'jobs.min_exp', 'jobs.max_exp', 'jobs.created_at', 'jobs.start_date', 'jobs.end_date',
                    'jobs.min_salary', 'jobs.max_salary', 'jobs.min_sal_mode', 'jobs.max_sal_mode',
                    'jobs.min_salary_currency', 'jobs.max_salary_currency',
                    'clients.name as clients_name', 'clients.logo_logical_path',
                    DB::raw('count(job_applications.id) as allCandidate'),
                    DB::raw('SUM(CASE WHEN job_applications.master_status_id = 1 THEN 1 ELSE 0 END) AS Sourcing'),
                    DB::raw('SUM(CASE WHEN job_applications.master_status_id = 2 THEN 1 ELSE 0 END) AS Screening'),
                    DB::raw('SUM(CASE WHEN job_applications.master_status_id = 3 THEN 1 ELSE 0 END) AS Interview'),
                    DB::raw('SUM(CASE WHEN job_applications.master_status_id = 4 THEN 1 ELSE 0 END) AS Negotiation'),
                    DB::raw('SUM(CASE WHEN job_applications.master_status_id = 5 THEN 1 ELSE 0 END) AS Offered'),
                    DB::raw('SUM(CASE WHEN job_applications.master_status_id = 6 THEN 1 ELSE 0 END) AS Joined'),
                    DB::raw('SUM(CASE WHEN job_applications.master_status_id = 7 THEN 1 ELSE 0 END) AS Rejected'),
                    DB::raw('SUM(CASE WHEN job_applications.master_status_id = 8 AND job_applications.thirdparty_company_id = 0 THEN 1 ELSE 0 END) AS InReview')
                )
                    ->join('job_assign', 'job_assign.job_id', 'jobs.id')
                    ->join('clients', 'clients.id', 'jobs.client_id')
                    ->leftJoin('add_job_wise_condidates as job_applications', 'job_applications.job_id', '=', 'jobs.id')
                    ->where('jobs.id', '>', '0')
                    ->where('job_assign.user_id', '=', $this->user->id)
                    ->take(1);

                $requisitionQuery = $requisitionQuery->where('job_assign.user_id', $this->user->id);
            }

        }

        if (\request('filter_company') != '') {
            $requisitionQuery->where('company_id', \request('filter_company'));
        }
        if (\request('filter_status') != '') {
            $requisitionQuery->where('status', \request('filter_status'));
        }
        if (\request('search') != '') {
            $search = request('search');
            $requisitionQuery->where('title', 'LIKE', "%$search%");
        }
        if ($request->client_id != 'all' && $request->client_id != '') {
            $requisitionQuery->where('jobs.client_id', $request->client_id);
        }

        $requisitionQuery->where('jobs.company_id', '=', $company_id);
        $requisitionQuery->groupBy('jobs.id');
        $requisitionQuery->orderBy('jobs.id', 'DESC');

        return DataTables::of($requisitionQuery)

            ->editColumn('chkbox', function ($row) {
                return '<div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input" type="checkbox" value="1" />
                  </div>';
            })
            ->editColumn('Requisition', function ($row) {

                $bgTextDecore = $strVal = '';
                $str = trim(@$row->title);
                $str = $strVal = strtolower($str[0]);
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

                $icon = '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
                    <a href="javascript:void(0);" data-row-id="'.$row->id.'" class="show-detail">
                      <div class="symbol-label '.$bgTextDecore.' border-radius-5">'.ucwords($strVal).'</div>
                    </a>
                  </div>';

                if (! empty($row->logo_logical_path)) {
                    $icon = '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2  border-radius-5">
                                        <a href="javascript:void(0);">
                                            <div class="symbol-label border-radius-5">
                                            <img src="'.url('public/'.@$row->logo_logical_path).'" alt="Company Logo" class="border-radius-5 w-100">
                                            </div>
                                        </a>
                                    </div>';
                }

                return $icon.'<div class="d-flex flex-column">
                    <a href="javascript:void(0);" data-row-id="'.$row->id.'" class="text-dark fw-bolder text-hover-primary fs-6 show-detail"  data-toggle="tooltip1" title="'.ucwords(@$row->title).'" >'.ucwords(substr(@$row->title, 0, 15)).'</a> 
                    <a href="javascript:void(0);" data-row-id="'.$row->id.'" class="fw-bold text-muted d-block fs-7 text-hover-primary show-detail">'.ucwords(substr(@$row->clients_name, 0, 15)).'</a>
                  </div>
                  ';
            })
            ->editColumn('Exp', function ($row) {
                return '<div class="d-flex flex-column">
                    <span class="text-dark fw-bolder fs-6">'.@$row->max_exp.'+</span>
                    <span class="text-muted fw-bold text-muted d-block fs-7">'.@$row->min_exp.'+</span>
                  </div>';
            })
            ->editColumn('Salary', function ($row) use ($salaryMode, $currencySymbole) {

                $min_sal_mode = $max_sal_mode = $min_salary_currency = $max_salary_currency = '';
                $min_sal_mode = strtoupper(substr($salaryMode[$row->min_sal_mode], 0, 1));
                $max_sal_mode = strtoupper(substr($salaryMode[$row->max_sal_mode], 0, 1));

                $min_salary_currency = $currencySymbole[@$row->min_salary_currency];
                $max_salary_currency = $currencySymbole[@$row->max_salary_currency];

                $max_salary = '';
                $max_salary = GeneralMethod::thousandsCurrencyFormat($row->max_salary);
                $min_salary = '';
                $min_salary = GeneralMethod::thousandsCurrencyFormat($row->min_salary);

                return '<div class="d-flex flex-column">
                    <span class="text-dark fw-bolder fs-6">'.$min_salary_currency.$max_salary.'/'.$min_sal_mode.'</span>                    
                    <span class="fw-bold d-block fs-7">'.$min_salary_currency.$min_salary.'/'.$max_sal_mode.'</span>
                  </div>';
            })
            ->editColumn('Location', function ($row) {

                $jobid = $row->id;
                $client_loc = DB::table('client_location_jobs')
                    ->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
                    ->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')
                    ->select(
                        'client_location.address', 'client_location.city', 'client_location.postcode', 'client_location.country_id',
                        'city_list.city as city_name'
                    )
                    ->where('client_location_jobs.job_id', $jobid)
                    ->first();

                $country_id = $country_name = '';
                $country_id = @$client_loc->country_id;
                if (@$country_id > 0) {
                    $country_name = GeneralMethod::getCountryName($country_id);
                    foreach ($country_name as $cval) {
                        $country_name = ucwords($cval['sortname']);
                    }
                }

                $city = substr(@$client_loc->city, 0, 6);
                if (@$client_loc->country_id == 101) {
                    $city = substr(@$client_loc->city_name, 0, 6);
                }

                $jobLocation = DB::table('client_location_jobs')
                    ->where('client_location_jobs.job_id', $jobid)->count();

                $jobCitySelected = DB::table('client_city_jobs')
                    ->where('client_city_jobs.job_id', $jobid)->count();

                if ($row->work_type == 'yes') {
                    return ' <div class="d-flex flex-column">
						<span class="text-dark fw-bolder fs-6" data-toggle="tooltip4" title="'.ucwords(@$client_loc->address).'" >'.ucwords(substr(@$client_loc->address, 0, 10)).'</span>
						<div class="d-flex align-items-center">
						  <span class="fw-bold text-muted d-block fs-7">Remote</span>
						  </div>
						</div>
					  </div>';
                } elseif ($row->work_type == 'no') {

                    if ($jobCitySelected == 1 || $jobCitySelected == 0) {
                        return ' <div class="d-flex flex-column">
							<span class="text-dark fw-bolder fs-6" data-toggle="tooltip4" title="'.ucwords(@$client_loc->address).'" >'.ucwords(substr(@$client_loc->address, 0, 10)).'</span>
							<div class="d-flex align-items-center">
								  <span class="fw-bold text-muted d-block fs-7">'.ucwords(substr(@$city, 0, 10)).'</span>
									&nbsp;|&nbsp;
								  <span class="fw-bold text-muted d-block fs-7">'.ucwords(substr(@$country_name, 0, 10)).'</span>
								<div>
							  </div>
							</div>
						  </div>';
                    } else {
                        return ' <div class="d-flex flex-column">
							<span class="text-dark fw-bolder fs-6" data-toggle="tooltip4" title="'.ucwords(@$client_loc->address).'" >'.ucwords(substr(@$client_loc->address, 0, 10)).'</span>
							<div class="d-flex align-items-center">
								  <span class="fw-bold text-muted d-block fs-7">Multiple</span>
								<div>
						  </div>
						</div>
					  </div>';
                    }

                } elseif ($row->work_type == 'any') {

                    if ($jobCitySelected == 1 || $jobCitySelected == 0) {
                        return ' <div class="d-flex flex-column">
							<span class="text-dark fw-bolder fs-6" data-toggle="tooltip4" title="'.ucwords(@$client_loc->address).'" >'.ucwords(substr(@$client_loc->address, 0, 10)).'</span>
							<div class="d-flex align-items-center">
								  <span class="fw-bold text-muted d-block fs-7">'.ucwords(substr(@$city, 0, 10)).'</span>
									&nbsp;|&nbsp;
								  <span class="fw-bold text-muted d-block fs-7">'.ucwords(substr(@$country_name, 0, 10)).'</span>
								<div>
							  </div>
							</div>
						  </div>';
                    } else {
                        return ' <div class="d-flex flex-column">
							<span class="text-dark fw-bolder fs-6" data-toggle="tooltip4" title="'.ucwords(@$client_loc->address).'" >'.ucwords(substr(@$client_loc->address, 0, 10)).'</span>
						<div class="d-flex align-items-center">
						  <span class="fw-bold text-muted d-block fs-7">Remote</span>
						  &nbsp;|&nbsp;
								  <span class="fw-bold text-muted d-block fs-7">Multiple</span>
								<div>

						  </div>
						</div>
					  </div>';
                    }

                }

                //     return ' <div class="d-flex flex-column">
                //     <span class="text-dark fw-bolder fs-6" data-toggle="tooltip4" title="'.ucwords(@$client_loc->address).'" >'.ucwords(substr(@$client_loc->address, 0, 10)).'</span>
                //     <div class="d-flex align-items-center">
                //           <span class="fw-bold text-muted d-block fs-7">'.ucwords(substr(@$city, 0, 10)).'</span>
                //             &nbsp;|&nbsp;
                //           <span class="fw-bold text-muted d-block fs-7">'.ucwords(substr(@$country_name, 0, 10)).'</span>
                //         <div>
                //       </div>
                //     </div>
                //   </div>';
            })

            ->editColumn('Status', function ($row) use ($job_status) {

                $status = $active = $hold = $inactive = $class = '';
                $status = ucwords($row->status);
                $class = $job_status[$row->status];
                if ($status == 'Inactive') {
                    $status = 'Closed';
                }

                return '
					<div class="d-flex flex-column">
						<a href="javascript.void(0);" data-row-id="'.@$row->id.'" class="d-flex flex-column '.@$class.'  status_stage text-hover-success" data-toggle="modal" data-target="#status_stage">'.$status.' </a>
                      
                      <span class="fw-bold text-muted d-block fs-7">R'.@$row->id.'</span>
                    </div>';
            })
            ->editColumn('Date', function ($row) {

                $total_positions = '';
                $total_positions = number_format($row->total_positions);

                return '<div class="d-flex flex-column">
                        <span class="text-dark fw-bolder fs-6">'.$row->created_at->format('d M, Y').'</span>
                        <span class="text-muted fw-bold d-block fs-7">'.$total_positions.'</span>
                    </div>';
            })
            ->editColumn('HM', function ($row) use ($company_id) {

                $partnersCount = DB::table('job_assign')
                    ->join('users', 'users.id', '=', 'job_assign.user_id')
                    ->select('users.id', 'users.name', 'users.image')
                    ->where('job_assign.job_id', '=', $row->id)
                    ->where('job_assign.company_id', '=', $company_id)
                    ->where('users.company_id', '=', $company_id)
                    ->count();

                $partnersData = DB::table('job_assign')
                    ->join('users', 'users.id', '=', 'job_assign.user_id')
                    ->select('users.id', 'users.name', 'users.image')
                    ->where('job_assign.job_id', '=', $row->id)
                    ->where('job_assign.company_id', '=', $company_id)
                    ->where('users.company_id', '=', $company_id)
                    ->orderBy('users.name', 'ASC')
                    ->skip(0)->take(3)->get();

                $jobId = $row->id;

                $html = '';
                $html .= '<div class="d-flex flex-column">
                    							<div class="symbol-group symbol-hover">';
                $temp = '';

                foreach ($partnersData as $pval) {
                    $logo_path = url('public/avatar.png');
                    if (! empty(@$pval->image) && strlen(@$pval->image) > 10) {
                        $logo_path = url('public/user-uploads/team_profile_img/'.@$pval->image);
                    }
                    $html .= '<div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip3" title="'.@$pval->name.'">
                            						<img alt="Pic" src="'.@$logo_path.'">
                          						</div>';
                }

                if (in_array('add_team_on_requisition', $this->userPermissions)) {
                    $html .= '<div data-row-id="'.$row->id.'"   class="symbol symbol-30 symbol-circle symbol-light hm-listing" data-bs-placement="top" data-toggle="tooltip2" title="More users list..">
	<span class="symbol-label font-weight-bold">'.$partnersCount.'+</span>
  </div>';
                }

                $html .= '</div>
                    					</div>';

                return $html;

            })
            ->editColumn('Partners', function ($row) use ($company_id) {

                $displayCounter = 0;
                $jobId = $row->id;
                $partnersCount = DB::table('recommendation_relation')
                    ->join('companies', 'companies.id', '=', 'recommendation_relation.to_company_id')
                    ->join('clients', function ($join) use ($jobId) {
                        $join->on('clients.company_id', '=', 'companies.id');
                        $join->on('clients.self_company', '=', DB::raw("'1'"));
                        $join->on('recommendation_relation.job_id', '=', DB::raw("'".$jobId."'"));
                    })
                    ->select('clients.id', 'clients.name', 'clients.logo_logical_path')
                    ->where('recommendation_relation.job_id', '=', $row->id)
                    ->where('recommendation_relation.from_company_id', '=', $company_id)
                    ->where('recommendation_relation.final_status', '=', 1)
                    ->groupBy('companies.id')
                    ->count();
                $displayCounter = $partnersCount;

                $partnersData = DB::table('recommendation_relation')
                    ->join('companies', 'companies.id', '=', 'recommendation_relation.to_company_id')
                    ->join('clients', function ($join) use ($jobId) {
                        $join->on('clients.company_id', '=', 'companies.id');
                        $join->on('clients.self_company', '=', DB::raw("'1'"));
                        $join->on('recommendation_relation.job_id', '=', DB::raw("'".$jobId."'"));
                    })
                    ->select('clients.id', 'clients.name', 'clients.logo_logical_path', 'companies.logo')
                    ->where('recommendation_relation.job_id', '=', $row->id)
                    ->where('recommendation_relation.from_company_id', '=', $company_id)
                    ->where('recommendation_relation.final_status', '=', 1)
                    ->groupBy('companies.id')
                    ->skip(0)->take(3)->get();

                $html = '';
                $html .= '<div class="d-flex flex-column">
                    						<div class="symbol-group symbol-hover">';
                $temp = '';
                foreach ($partnersData as $pval) {

                    $logo_path = url('img/client-default.png');
                    if (! empty(@$pval->logo) && strlen(@$pval->logo) > 10) {
                        $logo_path = url('public/user-uploads/company-logo/'.@$pval->logo);
                    }

                    $html .= '<div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip3" title="'.@$pval->name.'">
                            						<img alt="Pic" src="'.@$logo_path.'">
                          						</div>';

                }
                $html .= '<div data-row-id="'.$row->id.'"   class="symbol symbol-30 symbol-circle symbol-light spartner-listing" data-bs-placement="top" data-toggle="tooltip2" title="More users list..">
														<span class="symbol-label font-weight-bold">'.$displayCounter.'+</span>
													  </div>';
                $html .= '</div>
                    					</div>';

                return $html;

            })

            ->editColumn('TOT', function ($row) {

                $job_id = $row->id;

                $applicationCounter = DB::table('add_job_wise_condidates as job_applications')
                    ->select('job_applications.id')
                    ->where('job_applications.job_id', '=', $row->id)
                    ->where('job_applications.thirdparty_company_id', '=', 0)
                    ->orWhere(function ($query) use ($job_id) {
                        return
                                $query->where('job_applications.thirdparty_company_id', '>', 0)
                                    ->where('job_applications.job_id', '=', $job_id);
                    })
                    ->count();

                // $applicationCounter 	= number_format(@$row->allCandidate);

                return '<div class="d-flex flex-column">
                        <a href="'.url('/product_company/apptracker/'.$row->id.'/all').'" class="text-dark fw-bolder fs-6 text-hover-primary">'.@$applicationCounter.'</a>
                        <a href="javascript:void(0);" class="text-muted fw-bold d-block fs-7 text-hover-primary">'.number_format(@$row->InReview).'</a>
                    </div>';

            })

            ->editColumn('SOU', function ($row) {
                $master_status_id = 1;
                $job_id = '';
                $cnt = 0;
                $job_id = $row->id;
                $cnt = number_format($row->Sourcing);
                if ($cnt == 0) {
                    return '<div class="d-flex flex-column"><a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">0</a></div>';
                } else {
                    return '<div class="d-flex flex-column"><a href="'.url('/product_company/apptracker/'.$row->id.'/'.$master_status_id).'" class="text-dark fw-bolder fs-6 text-hover-primary">'.@$cnt.'</a></div>';
                }
            })
            ->editColumn('SCR', function ($row) {
                $master_status_id = 2;
                $job_id = '';
                $cnt = 0;
                $job_id = $row->id;
                $cnt = number_format($row->Screening);
                if ($cnt == 0) {
                    $cnt = '<b>.</b>';

                    return '<div class="d-flex flex-column"><a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">0</a></div>';
                } else {
                    return '<div class="d-flex flex-column"><a href="'.url('/product_company/apptracker/'.$row->id.'/'.$master_status_id).'" class="text-dark fw-bolder fs-6 text-hover-primary">'.@$cnt.'</a></div>';
                }
            })
            ->editColumn('EVA', function ($row) {
                $master_status_id = 3;
                $job_id = '';
                $cnt = 0;
                $job_id = $row->id;
                $cnt = number_format($row->Interview);
                if ($cnt == 0) {
                    $cnt = '<b>.</b>';

                    return '<div class="d-flex flex-column"><a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">0</a></div>';
                } else {
                    return '<div class="d-flex flex-column"><a href="'.url('/product_company/apptracker/'.$row->id.'/'.$master_status_id).'" class="text-dark fw-bolder fs-6 text-hover-primary">'.@$cnt.'</a></div>';
                }
            })
            ->editColumn('NEO', function ($row) {

                $master_status_id = 4;
                $job_id = '';
                $cnt = 0;
                $job_id = $row->id;
                $cnt = number_format($row->Negotiation);
                if ($cnt == 0) {
                    $cnt = '<b>.</b>';

                    return '<div class="d-flex flex-column"><a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">0</a></div>';
                } else {
                    return '<div class="d-flex flex-column"><a href="'.url('/product_company/apptracker/'.$row->id.'/'.$master_status_id).'" class="text-dark fw-bolder fs-6 text-hover-primary">'.@$cnt.'</a></div>';
                }
            })
            ->editColumn('OFF', function ($row) {
                $master_status_id = 5;
                $job_id = '';
                $cnt = 0;
                $job_id = $row->id;
                $cnt = number_format($row->Offered);
                if ($cnt == 0) {
                    $cnt = '<b>.</b>';

                    return '<div class="d-flex flex-column"><a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">0</a></div>';
                } else {
                    return '<div class="d-flex flex-column"><a href="'.url('/product_company/apptracker/'.$row->id.'/'.$master_status_id).'" class="text-dark fw-bolder fs-6 text-hover-primary">'.@$cnt.'</a></div>';
                }
            })

            ->editColumn('JOI', function ($row) {
                $master_status_id = 6;
                $job_id = '';
                $cnt = 0;
                $job_id = $row->id;
                $cnt = number_format($row->Joined);
                if ($cnt == 0) {
                    $cnt = '<b>.</b>';

                    return '<div class="d-flex flex-column"><a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">0</a></div>';
                } else {
                    return '<div class="d-flex flex-column"><a href="'.url('/product_company/apptracker/'.$row->id.'/'.$master_status_id).'" class="text-dark fw-bolder fs-6 text-hover-primary">'.@$cnt.'</a></div>';
                }
            })
            ->editColumn('REJ', function ($row) {
                $master_status_id = 7;
                $job_id = '';
                $cnt = 0;
                $job_id = $row->id;
                $cnt = number_format($row->Rejected);
                if ($cnt == 0) {
                    $cnt = '<b>.</b>';

                    return '<div class="d-flex flex-column"><a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">0</a></div>';
                } else {
                    return '<div class="d-flex flex-column"><a href="'.url('/product_company/apptracker/'.$row->id.'/'.$master_status_id).'" class="text-dark fw-bolder fs-6 text-hover-primary">'.@$cnt.'</a></div>';
                }
            })

            ->rawColumns(['chkbox', 'Requisition', 'Exp', 'Salary', 'Location', 'Status', 'Date', 'HM', 'Partners', 'TOT', 'SOU', 'SCR', 'EVA', 'NEO', 'OFF', 'JOI', 'REJ'])

            ->addIndexColumn()
            ->make(true);
    }

    public function recommendedAssignList(Request $request)
    {

        $chkAdminRole = DB::table('roles')->select('roles.id')->where('id', $this->user->role->role_id)->where('name', 'admin')->count();

        $job_status = GeneralMethod::job_status();
        $salaryMode = GeneralMethod::salaryMode();
        $currencySymbole = GeneralMethod::currencySymbole();

        $company_id = auth()->user()->company_id;
        $employmentType = GeneralMethod::employmentType();

        $contHtml = '<div class="symbol-group symbol-hover" style="float:left">';
        $contactQuery = DB::table('users')
            ->join('job_assign', 'job_assign.user_id', 'users.id')
            ->select('users.name', 'users.image')
            ->where('users.company_id', $company_id)
            ->where('job_assign.company_id', $company_id)
            ->where('job_assign.job_id', $request->jobid)
            ->get();
        if (! empty($contactQuery)) {
            foreach ($contactQuery as $cont) {
                $logo_path = url('public/avatar.png');
                if (! empty(@$cont->image) && strlen(@$cont->image) > 10) {
                    $logo_path = url('public/user-uploads/team_profile_img/'.@$cont->image);
                }
                $contHtml .= '<div class="symbol symbol-30 symbol-circle" data-toggle="tooltip" title="'.$cont->name.'"> <img alt="One" src="'.$logo_path.'"></div>';
            }
        }
        $contHtml .= '</div>';

        /*$recommendedDataList 	= DB::table('companies')
        ->select(
                        'companies.id', 'companies.company_name', 'companies.logo', 'companies.industries',
                        'td_industries.ind_name',
                        'jobs.title', 'jobs.id as jobid',
                        DB::raw('count(DISTINCT job_applications.job_id) as SimilarReq'),
                        DB::raw('count(DISTINCT job_applications.id) as Profiles'),
                        'recommendation_relation.from_company_id', 'recommendation_relation.to_company_id',
                        'recommendation_relation.final_status', 'recommendation_relation.job_id as recommendation_job_id',

                        DB::raw("SUM(CASE WHEN job_applications.master_status_id = 3 THEN 1 ELSE 0 END) AS Interview"),
                        DB::raw("SUM(CASE WHEN job_applications.id > 0 THEN 1 ELSE 0 END) AS Upload"),
                        DB::raw("SUM(CASE WHEN job_applications.master_status_id = 6 THEN 1 ELSE 0 END) AS Joined")
        )
        ->join('jobs', 'jobs.company_id', 'companies.id')
        ->join('job_applications', 'job_applications.job_id', 'jobs.id')
        ->join('recommendation_relation', function ($join) {
        $join->on('recommendation_relation.to_company_id', '=', 'companies.id');
        })
        ->leftJoin('td_industries', 'td_industries.ind_id', '=', 'companies.industries');
        $recommendedDataList 	=	$recommendedDataList->where('companies.id', '!=' , $company_id);
        $recommendedDataList 	=	$recommendedDataList->where('recommendation_relation.final_status', 1);
        $recommendedDataList 	=	$recommendedDataList->where('recommendation_relation.job_id', '=' , $request->jobid);
        $recommendedDataList 	=	$recommendedDataList->groupBy('companies.id');
        */

        $jobId = $request->jobId;

        // where			( companies.id !=244 ) AND (recommendation_relation.final_status = 1)  AND (recommendation_relation.job_id = 182)
        // group by 		companies.id

        $recommendedDataList = DB::table('recommendation_relation');
        $recommendedDataList = $recommendedDataList->select(
            'companies.id', 'companies.company_name', 'companies.logo', 'companies.industries',
            'td_industries.ind_name',
            'jobs.title', 'jobs.id as jobid',
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

        $recommendedDataList = $recommendedDataList->where('companies.id', '!=', $company_id)->whereNotIn('companies.id', \App\Helper\GeneralMethod::superAdminCompanyIds());
        $recommendedDataList = $recommendedDataList->where('recommendation_relation.final_status', 1);
        $recommendedDataList = $recommendedDataList->where('recommendation_relation.job_id', '=', $request->jobid);
        $recommendedDataList = $recommendedDataList->groupBy('companies.id');

        return DataTables::of($recommendedDataList)

            ->editColumn('chkbox', function ($row) {
                return '<div class="form-check form-check-sm form-check-custom form-check-solid">
                      <input class="form-check-input form-check-input-staff" type="checkbox" value="1" />
                    </div>';
            })

            ->editColumn('Name', function ($row) {

                $logo_path = url('img/client-default.png');
                if (! empty(@$row->logo) && strlen(@$row->logo) > 10) {
                    $logo_path = url('public/user-uploads/company-logo/'.@$row->logo);
                }

                return '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2  border-radius-5" style="float:left"> 
								<a href="javascript:void(0);">
                      				<div class="symbol-label border-radius-5"> <img src="'.$logo_path.'" alt="Company Logo" class="border-radius-5 w-100"> </div>
                      			</a> 
							</div>
							<div class="d-flex flex-column"> 
								<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">'.substr(ucwords(@$row->company_name), 0, 15).'</a>   
								<a href="javascript:void(0);" class="fw-bold text-muted d-block fs-7 text-hover-primary">'.substr(ucwords(@$row->ind_name), 0, 15).'</a>
							</div>';
            })

            ->editColumn('ContactPerson', function ($row) use ($contHtml) {
                /*return '<div class="symbol-group symbol-hover" style="float:left">
                <div class="symbol symbol-30 symbol-circle" data-toggle="tooltip" title="One"> <img alt="One" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_1.jpg"> </div>
                <div class="symbol symbol-30 symbol-circle" data-toggle="tooltip" title="Two"> <img alt="Two" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_2.jpg"> </div>
                <div class="symbol symbol-30 symbol-circle"data-toggle="tooltip" title="Three"> <img alt="Three" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_3.jpg"> </div>
                </div>
                </div>';*/

                return $contHtml;
            })
            ->editColumn('UploadProfile', function ($row) {
                return '<div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">'.$row->Upload.'</span> </div>';
            })
            ->editColumn('Interview', function ($row) {
                return '<div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">'.$row->Interview.'</span> </div>';
            })
            ->editColumn('Joinings', function ($row) {
                return '<div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">'.$row->Joined.'</span> </div>';
            })
            ->editColumn('Action', function ($row) use ($company_id) {
                $jobid = $row->jobid;

                return '<div class="flex-column"> 
		<a href="javascript:void(0);" data-row-tabname="Assigned"  data-row-ename="remove"  data-row-jobid="'.$jobid.'" data-row-fromcompany="'.$company_id.'" data-row-tocompany="'.$row->id.'" class="btn btn-sm btn-light-danger w-130 remove_job">
			Remove
		</a> 
		</div>';
            })
            ->rawColumns(['chkbox', 'Name', 'ContactPerson', 'UploadProfile', 'Interview', 'Joinings', 'Action'])
            ->addIndexColumn()
            ->make(true);
    }

    public function staffingPartnersRegister(Request $request)
    {

        $company_id = auth()->user()->company_id;
        $chkAdminRole = DB::table('roles')->select('roles.id')->where('id', $this->user->role->role_id)->where('name', 'admin')->count();
        $accountCompanyType = GeneralMethod::accountCompanyType();
        $jobId = $request->jobid;

        $recommendedDataList = DB::table('companies')
            ->select(
                'companies.id', 'companies.company_name', 'companies.logo', 'companies.industries', 'companies.company_type',
                'staffing_company_relation.from_company_id', 'staffing_company_relation.to_company_id', 'staffing_company_relation.final_status'
            )
        // ->join('jobs', 'jobs.company_id', 'companies.id')
            ->join('staffing_company_relation', function ($join) use ($company_id) {
                $join->on('staffing_company_relation.to_company_id', '=', 'companies.id');
                $join->on('staffing_company_relation.from_company_id', '=', DB::raw("'".$company_id."'"));
                // $join->on("staffing_company_relation.from_company_id", 	"=", "companies.id");
                // $join->on("staffing_company_relation.final_status", 	"=",  DB::raw("'1'"));
            })
            ->leftJoin('td_industries', 'td_industries.ind_id', '=', 'companies.industries');
        $recommendedDataList = $recommendedDataList->where('companies.id', '!=', auth()->user()->company_id)->whereNotIn('companies.id', \App\Helper\GeneralMethod::superAdminCompanyIds());
        $recommendedDataList = $recommendedDataList->where('staffing_company_relation.final_status', '=', 1);

        $recommendedDataList = $recommendedDataList->groupBy('companies.id');

        return DataTables::of($recommendedDataList)

            ->editColumn('chkbox', function ($row) {
                return '<div class="form-check form-check-sm form-check-custom form-check-solid">
                      <input class="form-check-input form-check-input-reco" type="checkbox" value="1" />
                    </div>';
            })
            ->editColumn('Name', function ($row) {

                $logo_path = url('img/client-default.png');
                if (! empty(@$row->logo) && strlen(@$row->logo) > 10) {
                    $logo_path = url('public/user-uploads/company-logo/'.@$row->logo);
                }

                return '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2  border-radius-5" style="float:left"> 
								<a href="javascript:void(0);">
                      				<div class="symbol-label border-radius-5"> <img src="'.$logo_path.'" alt="Company Logo" class="border-radius-5 w-100"> </div>
                      			</a> 
							</div>
							<div class="d-flex flex-column"> 
								<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">'.substr(ucwords(@$row->company_name), 0, 15).'</a>   
								<a href="javascript:void(0);" class="fw-bold text-muted d-block fs-7 text-hover-primary">'.substr(ucwords(@$row->ind_name), 0, 15).'</a>
							</div>';
            })

            ->editColumn('CompanyType', function ($row) use ($accountCompanyType) {
                $company_type = '';
                if ($row->company_type > 0) {
                    $company_type = $accountCompanyType[$row->company_type];
                }

                return '<div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">'.$company_type.'</span> </div>';
            })

            ->editColumn('Status', function ($row) {
                return '<div class="d-flex flex-column"> <span class="text-success fw-bolder fs-6">Registered</span> </div>';
            })

            ->editColumn('Action', function ($row) use ($jobId) {

                $from_company = $company_id = auth()->user()->company_id;
                $id = '';
                $id = $row->to_company_id;

                $html = '';
                $recommendateCounter = DB::table('recommendation_relation')
                    ->select('recommendation_relation.id')
                    ->where('recommendation_relation.from_company_id', $from_company)
                    ->where('recommendation_relation.to_company_id', $id)
                    ->where('recommendation_relation.job_id', $jobId)
                                                // ->where('recommendation_relation.final_status', '=', 1)
                    ->count();

                if ($recommendateCounter == 1) {

                    $recommendateRow = DB::table('recommendation_relation')
                        ->select('recommendation_relation.id', 'recommendation_relation.from_company_id', 'recommendation_relation.from_status', 'recommendation_relation.to_status', 'recommendation_relation.final_status')
                        ->where('recommendation_relation.from_company_id', $from_company)
                        ->where('recommendation_relation.to_company_id', $id)
                        ->where('recommendation_relation.job_id', $jobId)
                    // ->where('recommendation_relation.final_status', '=', 1)
                        ->first();

                    if (($recommendateRow->final_status == 0) || ($recommendateRow->final_status == '')) {
                        $html = '<div class="d-flex flex-column"> <a href="javascript:void(0);" class="btn btn-sm btn-light-success w-130">Request Sent</a> </div>';
                    } elseif ($recommendateRow->final_status == -1) {
                        $html = '<a href="javascript:void(0);" data-row-id="'.$id.'" data-row-jobid="'.@$jobId.'" data-row-ename="ignore" class="btn btn-sm btn-light-success w-130 resend_job_recommanded_request">Assign Job</a>';
                    } elseif ($recommendateRow->final_status == 2) {
                        $html = '<a href="javascript:void(0);" data-row-id="'.$id.'" data-row-jobid="'.@$jobId.'" data-row-ename="remove" class="btn btn-sm btn-light-success w-130 resend_job_recommanded_request">Assign Job</a>';
                    } else {
                        $html = '<div class="d-flex flex-column"> 
<a href="javascript:void(0);" data-row-tabname="Registered" data-row-jobid="'.$jobId.'" data-row-fromcompany="'.$company_id.'" data-row-tocompany="'.$row->to_company_id.'" class="btn btn-sm btn-light-danger w-130 remove_job">
Remove
</a>
</div>';
                    }

                } else {
                    $html = '<div class="d-flex flex-column"> 
<a href="javascript:void(0);" data-row-jobid="'.$jobId.'" data-row-fromcompany="'.$from_company.'" data-row-tocompany="'.$id.'" class="btn btn-sm btn-secondary  w-130 send_only_job_invitation">
Assign Job
</a> 
</div>';

                }

                return $html;

            })

            ->editColumn('Action-old', function ($row) use ($jobId, $company_id) {

                $html = '';
                $recommendateCounter = DB::table('recommendation_relation')
                    ->select('recommendation_relation.id')
                    ->where('recommendation_relation.from_company_id', $company_id)
                    ->where('recommendation_relation.to_company_id', $row->to_company_id)
                    ->where('recommendation_relation.job_id', $jobId)
                    ->where('recommendation_relation.final_status', '=', 1)
                    ->count();

                if ($recommendateCounter == 1) {

                    $html = '<div class="d-flex flex-column"> 
	<a href="javascript:void(0);" data-row-tabname="Registered"  data-row-ename="remove" data-row-jobid="'.$jobId.'" data-row-fromcompany="'.$company_id.'" data-row-tocompany="'.$row->to_company_id.'" class="btn btn-sm btn-light-danger w-130 remove_job">
		Remove
	</a>
</div>';

                } else {
                    $recommendateCounter = DB::table('recommendation_relation')
                        ->select('recommendation_relation.id')
                        ->where('recommendation_relation.from_company_id', $company_id)
                        ->where('recommendation_relation.to_company_id', $row->to_company_id)
                        ->where('recommendation_relation.job_id', $jobId)
                        ->count();
                    if ($recommendateCounter == 0) {
                        $html = '<div class="d-flex flex-column"> 
								<a href="javascript:void(0);" data-row-jobid="'.$jobId.'" data-row-fromcompany="'.$company_id.'" data-row-tocompany="'.$row->to_company_id.'" class="btn btn-sm btn-secondary  w-130 send_only_job_invitation">
									Assign Job
								</a> 
							</div>';
                    } else {
                        $recommendateRow = DB::table('recommendation_relation')
                            ->select('recommendation_relation.id', 'recommendation_relation.final_status')
                            ->where('recommendation_relation.from_company_id', $company_id)
                            ->where('recommendation_relation.to_company_id', $row->to_company_id)
                            ->where('recommendation_relation.job_id', $jobId)
                            ->first();

                        if (($recommendateRow->final_status == 0) || ($recommendateRow->final_status == '')) {
                            $html = '<div class="d-flex flex-column"> <a href="javascript:void(0);" class="btn btn-sm btn-light-success w-130">Request Sent</a> </div>';
                        } elseif ($recommendateRow->final_status == -1) {
                            $html = '<div class="d-flex flex-column"> <a href="javascript:void(0);" class="btn btn-sm btn-light-success w-130">Ignore</a> </div>';
                        } elseif ($recommendateRow->final_status == 2) {
                            $html = '<a href="javascript:void(0);" data-row-id="'.$row->to_company_id.'" data-row-jobid="'.@$jobId.'" class="btn btn-sm btn-light-success w-130 resend_job_recommanded_request">Assign Job</a>';
                        }

                    }

                }

                return $html;
            })

            ->rawColumns(['chkbox', 'Name', 'CompanyType', 'Status', 'Action'])
            ->addIndexColumn()
            ->make(true);

    }

    public function data_n(Request $request)
    {

        // abort_if(!$this->user->cans('view_jobs'), 403);

        $chkAdminRole = DB::table('roles')->select('roles.id')->where('id', $this->user->role->role_id)->where('name', 'admin')->count();
        $job_status = GeneralMethod::job_status();
        $salaryMode = GeneralMethod::salaryMode();
        $currencySymbole = GeneralMethod::currencySymbole();

        if ($chkAdminRole > 0) {
            $categories = Job::select(
                'jobs.id', 'jobs.title', 'jobs.total_positions', 'jobs.priority', 'jobs.status', 'jobs.min_exp', 'jobs.max_exp', 'jobs.created_at', 'jobs.start_date', 'jobs.end_date',
                'jobs.min_salary', 'jobs.max_salary', 'jobs.min_sal_mode', 'jobs.max_sal_mode',

                'jobs.min_salary_currency', 'jobs.max_salary_currency',

                'clients.name as clients_name', 'clients.logo_logical_path',
                DB::raw('COUNT(job_applications.id) as allCandidate'),
                DB::raw('SUM(CASE WHEN job_applications.master_status_id = 1 THEN 1 ELSE 0 END) AS Sourcing'),
                DB::raw('SUM(CASE WHEN job_applications.master_status_id = 2 THEN 1 ELSE 0 END) AS Screening'),
                DB::raw('SUM(CASE WHEN job_applications.master_status_id = 3 THEN 1 ELSE 0 END) AS Interview'),
                DB::raw('SUM(CASE WHEN job_applications.master_status_id = 4 THEN 1 ELSE 0 END) AS Negotiation'),
                DB::raw('SUM(CASE WHEN job_applications.master_status_id = 5 THEN 1 ELSE 0 END) AS Offered'),
                DB::raw('SUM(CASE WHEN job_applications.master_status_id = 6 THEN 1 ELSE 0 END) AS Joined'),
                DB::raw('SUM(CASE WHEN job_applications.master_status_id = 7 THEN 1 ELSE 0 END) AS Rejected'),
                DB::raw('SUM(CASE WHEN job_applications.master_status_id = 8 THEN 1 ELSE 0 END) AS InReview')
            )
                ->join('clients', 'clients.id', 'jobs.client_id')
                ->leftJoin('job_applications', 'job_applications.job_id', '=', 'jobs.id')
                ->where('jobs.id', '>', '0')
                ->take(1);
        }
        /*else
        {
            $categories = Job::select(
                                        'jobs.id', 'jobs.title', 'jobs.total_positions', 'jobs.priority', 'jobs.status', 'jobs.min_exp', 'jobs.max_exp', 'jobs.created_at', 'jobs.start_date', 'jobs.end_date',
                                        'jobs.min_salary', 'jobs.max_salary', 'jobs.min_sal_mode', 'jobs.max_sal_mode',

                                        'jobs.min_salary_currency', 'jobs.max_salary_currency',

                                        'clients.name as clients_name', 'clients.logo_logical_path',
                                        DB::raw("count(job_applications.id) as allCandidate"),
                                        DB::raw("SUM(CASE WHEN job_applications.master_status_id = 1 THEN 1 ELSE 0 END) AS Sourcing"),
                                        DB::raw("SUM(CASE WHEN job_applications.master_status_id = 2 THEN 1 ELSE 0 END) AS Screening"),
                                        DB::raw("SUM(CASE WHEN job_applications.master_status_id = 3 THEN 1 ELSE 0 END) AS Interview"),
                                        DB::raw("SUM(CASE WHEN job_applications.master_status_id = 4 THEN 1 ELSE 0 END) AS Negotiation"),
                                        DB::raw("SUM(CASE WHEN job_applications.master_status_id = 5 THEN 1 ELSE 0 END) AS Offered"),
                                        DB::raw("SUM(CASE WHEN job_applications.master_status_id = 6 THEN 1 ELSE 0 END) AS Joined"),
                                        DB::raw("SUM(CASE WHEN job_applications.master_status_id = 7 THEN 1 ELSE 0 END) AS Rejected"),
                                        DB::raw("SUM(CASE WHEN job_applications.master_status_id = 8 THEN 1 ELSE 0 END) AS InReview")
                                    )
                                ->join('job_assign', 'job_assign.job_id', 'jobs.id')
                                ->join('clients', 'clients.id', 'jobs.client_id')
                                ->leftJoin('job_applications', 'job_applications.job_id', '=', 'jobs.id')
                                ->where('jobs.id', '>', '0')
                                ->where('job_assign.user_id', '=', $this->user->id)
                                ->take(1);
            $categories->where('job_assign.user_id', $this->user->id);
        }*/

        if (\request('filter_company') != '') {
            $categories->where('company_id', \request('filter_company'));
        }
        if (\request('filter_status') != '') {
            $categories->where('status', \request('filter_status'));
        }
        if (\request('search') != '') {
            $search = request('search');
            $categories->where('title', 'LIKE', "%$search%");
        }
        if ($request->client_id != 'all' && $request->client_id != '') {
            $categories->where('jobs.client_id', $request->client_id);
        }

        $categories->groupBy('jobs.id');
        $categories->orderBy('jobs.id', 'DESC');

        return DataTables::of($categories)

            ->editColumn('chkbox', function ($row) {
                return '<div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input" type="checkbox" value="1" />
                  </div>';
            })
            ->editColumn('Requisition', function ($row) {

                return '<div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input" type="checkbox" value="1" />
                  </div>';
            })
            ->editColumn('Exp', function ($row) {
                return '<div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input" type="checkbox" value="1" />
                  </div>';
            })
            ->editColumn('Salary', function ($row) {

                return '<div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input" type="checkbox" value="1" />
                  </div>';
            })
            ->editColumn('Location', function ($row) {

                return '<div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input" type="checkbox" value="1" />
                  </div>';
            })

            ->editColumn('Status', function ($row) {

                return '<div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input" type="checkbox" value="1" />
                  </div>';
            })

            ->editColumn('Date', function ($row) {

                return '<div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input" type="checkbox" value="1" />
                  </div>';
            })

            ->editColumn('HM', function ($row) {

                return '<div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input" type="checkbox" value="1" />
                  </div>';
            })

            ->editColumn('Partners', function ($row) {

                return '<div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input" type="checkbox" value="1" />
                  </div>';

            })

            ->editColumn('TOT', function ($row) {
                return '<div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input" type="checkbox" value="1" />
                  </div>';
            })
            ->editColumn('SOU', function ($row) {
                return '<div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input" type="checkbox" value="1" />
                  </div>';
            })
            ->editColumn('SCR', function ($row) {
                return '<div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input" type="checkbox" value="1" />
                  </div>';
            })
            ->editColumn('EVA', function ($row) {
                return '<div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input" type="checkbox" value="1" />
                  </div>';
            })

            ->editColumn('NEO', function ($row) {

                return '<div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input" type="checkbox" value="1" />
                  </div>';
            })

            ->editColumn('OFF', function ($row) {
                return '<div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input" type="checkbox" value="1" />
                  </div>';
            })
            ->editColumn('JOI', function ($row) {
                return '<div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input" type="checkbox" value="1" />
                  </div>';
            })
            ->editColumn('REJ', function ($row) {
                return '<div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input" type="checkbox" value="1" />
                  </div>';
            })

            ->rawColumns(['chkbox', 'Requisition', 'Exp', 'Salary', 'Location', 'Status', 'Date', 'HM', 'Partners', 'TOT', 'SOU', 'SCR', 'EVA', 'NEO', 'OFF', 'JOI', 'REJ'])

            ->addIndexColumn()
            ->make(true);
    }

    public function sendEmail(Request $request)
    {

        abort_if(! $this->user->cans('add_jobs'), 403);

        $this->boardColumns = ApplicationStatus::all();

        $this->locations = JobLocation::all();

        $this->jobs = Job::all();

        $this->skills = Skill::all();

        return view('product_company.jobs.send-email', $this->data);

    }

    public function applicationData(Request $request)
    {

        abort_if(! $this->user->cans('view_job_applications'), 403);

        return DataTables::of($this->filterJobApplications($request))

            ->editColumn('full_name', function ($row) {

                return '<a href="javascript:;" class="show-detail" data-widget="control-sidebar" data-slide="true" data-row-id="'.$row->id.'">'.ucwords($row->full_name).'</a>';

            })

            ->editColumn('title', function ($row) {

                return ucfirst($row->title);

            })

            ->editColumn('location', function ($row) {

                return ucwords($row->location);

            })

            ->editColumn('status', function ($row) {

                return ucwords($row->status);

            })

            ->addColumn('mail_status', function ($row) use ($request) {

                return $row->jobs()->where('job_id', $request->jobId)->count() == 0 ? '<label class="badge bg-danger">'.__('modules.newJobEmail.mailNotSent').'</label>' : '<label class="badge bg-success">'.__('modules.newJobEmail.mailSent').'</label>';

            })

            ->addColumn('checkbox', function ($row) {

                return '



                    <div class="checkbox form-check">



                        <input id="'.$row->id.'" type="checkbox" class="form-check-input" >



                        <label for="'.$row->id.'"></label>



                    </div>



                ';

            })

            ->rawColumns(['action', 'resume', 'full_name', 'checkbox', 'mail_status'])

            ->addIndexColumn()

            ->make(true);

    }

    public function sendEmails(Request $request)
    {

        if ($request->allSelected == 'false') {

            if (! $request->has('selectedIds')) {

                return Reply::error(__('messages.selectApplicantsForEmail'));

            }

            $jobApplications = JobApplication::whereIn('id', $request->selectedIds)->with('jobs');

        } else {

            $jobApplications = JobApplication::with('jobs');

        }

        // get jobApplication

        $job = Job::findOrFail($request->job_for_email);

        if ($request->excludeSent == 'true') {

            $jobApplicationsCopy = clone $jobApplications;

            $jobApplicationIds = $jobApplicationsCopy->whereHas('jobs', function ($q) use ($request) {

                $q->where('job_id', $request->job_for_email);

            })->get()->map(function ($jobApplication) {

                return $jobApplication->id;

            })->toArray();

            $jobApplications = $jobApplications->whereNotIn('job_applications.id', $jobApplicationIds);

        }

        $jobApplications = $jobApplications->get();

        $jobApplicationIds = $jobApplications->map(function ($jobApplication) {

            return $jobApplication->id;

        })->toArray();

        $job->applications()->syncWithoutDetaching($jobApplicationIds);

        $uniqueEmailJobs = $jobApplications->unique(function ($job) {

            return $job['email'];

        });

        Notification::send($uniqueEmailJobs, new NewJobOpening($job));

        return Reply::success(__('messages.emailsSentSuccessfully'));

    }

    public function filterJobApplications($request)
    {

        $jobApplications = JobApplication::select('job_applications.id', 'job_applications.full_name', 'job_applications.email', 'jobs.title', 'job_locations.location', 'application_status.status')

            ->with('jobs')

            ->join('jobs', 'jobs.id', 'job_applications.job_id')

            ->leftjoin('job_skills', 'jobs.id', 'job_skills.job_id')

            ->leftjoin('job_locations', 'job_locations.id', 'jobs.location_id')

            ->leftjoin('application_status', 'application_status.id', 'job_applications.status_id')->distinct();

        // Filter by status

        if ($request->status != 'all' && $request->status != '') {

            $jobApplications = $jobApplications->where('job_applications.status_id', $request->status);

        }

        // Filter By jobs

        if ($request->jobs != 'all' && $request->jobs != '') {

            $jobApplications = $jobApplications->where('job_applications.job_id', $request->jobs);

        }

        // Filter By skills

        if ($request->skill != 'all' && $request->skill != '') {

            $jobApplications = $jobApplications->whereIn('job_skills.skill_id', gettype($request->skill) == 'array' ? $request->skill : explode(',', $request->skill));

        }

        // Filter by location

        if ($request->location != 'all' && $request->location != '') {

            $jobApplications = $jobApplications->where('jobs.location_id', $request->location);

        }

        // Filter by StartDate

        if ($request->startDate != null && $request->startDate != '') {

            $jobApplications = $jobApplications->where(DB::raw('DATE(job_applications.`created_at`)'), '>=', "$request->startDate");

        }

        // Filter by EndDate

        if ($request->endDate != null && $request->endDate != '') {

            $jobApplications = $jobApplications->where(DB::raw('DATE(job_applications.`created_at`)'), '<=', "$request->endDate");

        }

        // Filter by MailStatus

        if ($request->mailStatus != null && $request->mailStatus != '') {

            if ($request->mailStatus !== 'all') {

                if ($request->mailStatus == 'sent') {

                    $jobApplications = $jobApplications->whereHas('jobs', function ($q) use ($request) {

                        $q->where('job_id', $request->jobId);

                    });

                } else {

                    $jobApplicationsCopy = clone $jobApplications;

                    $jobApplicationIds = $jobApplicationsCopy->whereHas('jobs', function ($q) use ($request) {

                        $q->where('job_id', $request->jobId);

                    })->get()->map(function ($jobApplication) {

                        return $jobApplication->id;

                    })->toArray();

                    $jobApplications = $jobApplications->whereNotIn('job_applications.id', $jobApplicationIds);

                }

            }

        }

        $jobApplications = $jobApplications->groupBy('job_applications.id');

        return $jobApplications;

    }

    public function refreshDate(UpdateJob $request)
    {

        $job = Job::find($request->id);

        $job->start_date = $request->start_date;

        $job->end_date = $request->end_date;

        $job->status = 'active';

        $job->save();

        return Reply::success(__('messages.updatedSuccessfully'));

    }

    public function getContactPerson($client_id)
    {
        $jobLocation = explode(',', request()->jobLocation);
        // dd($jobLocation);
        $ccArr = '';
        $locArr = '';
        $client_arr = DB::table('client_contact_info')->select('*')->where('client_id', $client_id)->get();
        foreach ($client_arr as $cArr) {
            $ccArr .= '<option  value="'.$cArr->id.'">'.ucwords($cArr->contact_person_name).'</option>';
        }

        $locInfo = DB::table('client_location')
            ->leftJoin('countries', 'countries.id', '=', 'client_location.country_id')
            ->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')
            ->select(
                'client_location.id', 'client_location.city', 'client_location.address', 'client_location.postcode', 'client_location.country_id',
                'city_list.city as city_name'
            )
            ->where('client_id', $client_id)
            ->get();

        foreach ($locInfo as $lArr) {

            $city_name = $lArr->city;
            if ($lArr->country_id == 101) {
                $city_name = $lArr->city_name;
            }
            if (in_array($lArr->id, $jobLocation)) {
                $selected = 'selected';
            }

            $locArr .= '<option   value="'.$lArr->id.'" '.$selected.'>'.ucwords($lArr->address).'&nbsp;-&nbsp;'.ucwords($city_name).'</option>';

        }

        $job_id = @$_REQUEST['job_id'];
        if ($_REQUEST['job_id'] > 0) {
            $client_location_jobs = DB::table('client_location_jobs')
                ->select('client_location_jobs.id', 'client_location_jobs.client_location_id')
                ->where('client_location_jobs.job_id', $job_id)
                ->get()
                ->toArray();
        }

        $city = DB::table('client_location')
            ->join('countries', 'countries.id', '=', 'client_location.country_id')
            ->select('client_location.id', 'client_location.city', 'client_location.address', 'client_location.postcode', 'countries.country_name')
            ->where('client_id', $client_id)
            ->groupBy('client_location.city')
            ->get();
        $clientcity = '';
        foreach ($city as $arr) {

            $sel = '';
            if ($job_id > 0) {
                foreach ($client_location_jobs as $locVal) {
                    if ($locVal->client_location_id == $arr->id) {
                        $sel = 'selected="selected"';
                    }
                }
            }

            $clientcity .= '<option   value="'.$arr->id.'"  '.@$sel.'>'.ucwords($arr->city).'</option>';
        }

        return Reply::dataOnly(['status' => 'success', 'ccArr' => $ccArr, 'locArr' => $locArr, 'clientcity' => $clientcity]);

    }

    public function lightbox($client_id)
    {

        $this->client = Client::find($client_id);

        return view('product_company.jobs.lightbox', $this->data)->render();

    }

    public function loc_lightbox($client_id)
    {

        $this->client = '';

        $this->countries = Country::all();

        if ($client_id > 0) {

            $this->client = Client::find($client_id);

        } else {

            $data = [];

            $data['id'] = 0;

            $data['name'] = 'Self';

            $this->client = (object) $data;

        }

        return view('product_company.jobs.loc_lightbox', $this->data)->render();

    }

    public function editJobStatus($id)
    {

        $this->id = $id;
        if ($this->id) {
            $this->job = DB::table('jobs')->select('*')->where('id', '=', $this->id)->first();
        }
        // return view('product_company.jobs.status', $this->data)->render();
        $view = view('product_company.jobs.status', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'view' => $view]);

    }

    public function newUpdateJobstatus(Request $request)
    {

        $company_id = '';
        $company_id = auth()->user()->company_id;

        $job_id = $_REQUEST['job_id'];
        $status = $_REQUEST['status'];

        $data = [];
        // $data['job_id']	=	$job_id ;
        $data['status'] = $status;

        DB::table('jobs')->where('id', @$job_id)->limit(1)->update($data);

        $this->activeJobCounter = DB::table('jobs')->select('id')->where('status', '=', 'active')->where('company_id', '=', $company_id)->count();
        $this->inactiveJobCounter = DB::table('jobs')->select('id')->where('status', '=', 'inactive')->where('company_id', '=', $company_id)->count();
        $this->holdJobCounter = DB::table('jobs')->select('id')->where('status', '=', 'hold')->where('company_id', '=', $company_id)->count();

        return Reply::dataOnly(['status' => 'success', 'activeJobs' => $this->activeJobCounter, 'inactiveJobs' => $this->inactiveJobCounter, 'holdJobs' => $this->holdJobCounter]);

    }

    public function saveClass(Request $request)
    {

        $skillId = $company_id = '';

        $jobid = $request->job_id;

        $skill_name = $request->class_name;

        $job = Job::findOrFail($jobid);

        $company_id = auth()->user()->company_id;

        $values = ['name' => $skill_name, 'company_id' => $company_id, 'category_id' => $job->category_id];

        DB::table('skills')->insert($values);

        $skillId = DB::getPdo()->lastInsertId();

        $values = ['company_id' => $company_id, 'skill_id' => $skillId, 'job_id' => $jobid];

        DB::table('job_skills')->insert($values);

        $skillId = DB::getPdo()->lastInsertId();

        return Reply::dataOnly(['status' => 'success']);

    }

    public function export($start, $length)
    {
        $filters = [
            'start' => $start,
            'length' => $length,
            'loginUserId' => $this->user->id,
            'loginUserRole' => $this->user->role->role_id,
            'company_id' => auth()->user()->company_id,
        ];

        $data = [
            'company' => $this->user->name,
        ];

        return Excel::download(new JobExport($filters, $data), 'JobExport.xlsx', ExcelExcel::XLSX);

    }

    public function hiring_manager($id)
    {

        $hiring_namager = '';
        $hiring_namager = $_REQUEST['hiring_namager'];
        $this->hiring_namagerArr = explode(',', $hiring_namager);

        $this->client_id = $id;
        $contPersonArr = [];

        $clientContactArr = DB::table('client_contact_info')
            ->select('client_contact_info.contact_person_designation')
            ->where('client_contact_info.client_id', $this->client_id)
            ->orderBy('client_contact_info.contact_person_designation', 'ASC')
            ->groupBy('client_contact_info.contact_person_designation')
            ->get();

        $cnt = 0;
        foreach ($clientContactArr as $cval) {
            // $contPersonArr[$cnt]			=	$cval->contact_person_designation;

            $personDetail = DB::table('client_contact_info')
                ->select('client_contact_info.id', 'client_contact_info.client_id', 'client_contact_info.contact_person_name', 'client_contact_info.contact_person_email', 'client_contact_info.contact_person_number', 'client_contact_info.contact_person_designation')
                ->where('client_contact_info.client_id', $this->client_id)
                ->where('client_contact_info.contact_person_designation', $cval->contact_person_designation)
                ->orderBy('client_contact_info.contact_person_name', 'ASC')
                ->get();

            $i = 0;
            foreach ($personDetail as $pDetail) {
                $contPersonArr[$cval->contact_person_designation][$i]['id'] = $pDetail->id;
                $contPersonArr[$cval->contact_person_designation][$i]['client_id'] = $pDetail->client_id;
                $contPersonArr[$cval->contact_person_designation][$i]['contact_person_name'] = $pDetail->contact_person_name;
                $contPersonArr[$cval->contact_person_designation][$i]['contact_person_email'] = $pDetail->contact_person_email;
                $contPersonArr[$cval->contact_person_designation][$i]['contact_person_number'] = $pDetail->contact_person_number;
                $contPersonArr[$cval->contact_person_designation][$i]['contact_person_designation'] = $pDetail->contact_person_designation;
                $i++;
            }

            $cnt++;

        }

        $this->contPersonArr = $contPersonArr;

        $this->assigned = DB::table('client_contact_info')
            ->select('client_contact_info.id', 'client_contact_info.client_id', 'client_contact_info.contact_person_name', 'client_contact_info.contact_person_email', 'client_contact_info.contact_person_number', 'client_contact_info.contact_person_designation')
            ->where('client_contact_info.client_id', $this->client_id)
                                            // ->where('client_contact_info.contact_person_designation',$cval->contact_person_designation)
            ->orderBy('client_contact_info.contact_person_name', 'ASC')
            ->get()->toArray();

        $view = view('product_company.jobs.hiring_manager', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'view' => $view]);

    }

    public function recruiter($id)
    {

        $recruiter = '';
        $recruiter = $_REQUEST['recruiter'];
        $this->recruiter = explode(',', $recruiter);

        $this->client_id = $id;
        $contPersonArr = [];

        $clientContactArr = DB::table('client_contact_info')
            ->select('client_contact_info.contact_person_designation')
            ->where('client_contact_info.client_id', $this->client_id)
            ->orderBy('client_contact_info.contact_person_designation', 'ASC')
            ->groupBy('client_contact_info.contact_person_designation')
            ->get();

        $cnt = 0;
        foreach ($clientContactArr as $cval) {
            // $contPersonArr[$cnt]			=	$cval->contact_person_designation;

            $personDetail = DB::table('client_contact_info')
                ->select('client_contact_info.id', 'client_contact_info.client_id', 'client_contact_info.contact_person_name', 'client_contact_info.contact_person_email', 'client_contact_info.contact_person_number', 'client_contact_info.contact_person_designation')
                ->where('client_contact_info.client_id', $this->client_id)
                ->where('client_contact_info.contact_person_designation', $cval->contact_person_designation)
                ->orderBy('client_contact_info.contact_person_name', 'ASC')
                ->get();

            $i = 0;
            foreach ($personDetail as $pDetail) {
                $contPersonArr[$cval->contact_person_designation][$i]['id'] = $pDetail->id;
                $contPersonArr[$cval->contact_person_designation][$i]['client_id'] = $pDetail->client_id;
                $contPersonArr[$cval->contact_person_designation][$i]['contact_person_name'] = $pDetail->contact_person_name;
                $contPersonArr[$cval->contact_person_designation][$i]['contact_person_email'] = $pDetail->contact_person_email;
                $contPersonArr[$cval->contact_person_designation][$i]['contact_person_number'] = $pDetail->contact_person_number;
                $contPersonArr[$cval->contact_person_designation][$i]['contact_person_designation'] = $pDetail->contact_person_designation;
                $i++;
            }

            $cnt++;

        }

        $this->contPersonArr = $contPersonArr;

        $this->assigned = DB::table('client_contact_info')
            ->select('client_contact_info.id', 'client_contact_info.client_id', 'client_contact_info.contact_person_name', 'client_contact_info.contact_person_email', 'client_contact_info.contact_person_number', 'client_contact_info.contact_person_designation')
            ->where('client_contact_info.client_id', $this->client_id)
                                            // ->where('client_contact_info.contact_person_designation',$cval->contact_person_designation)
            ->orderBy('client_contact_info.contact_person_name', 'ASC')
            ->get()->toArray();

        $company_id = auth()->user()->company_id;
        $this->team = DB::table('users')
            ->leftjoin('role_user', 'role_user.user_id', 'users.id')
            ->leftjoin('roles', 'roles.id', 'role_user.role_id')
            ->select('users.id', 'users.name', 'roles.display_name', 'users.working_status')
            ->where('type', 0)
            ->where('users.company_id', $company_id)
            ->where('status', 'active')
                                        // ->where('working_status', '=', 1)
            ->get();

        $view = view('product_company.jobs.recruiter', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'view' => $view]);

    }

    public function viewMore($id)
    {
        abort_if(! $this->user->cans('view_jobs'), 403);

        $this->salaryMode = GeneralMethod::salaryMode();
        $this->employmentType = GeneralMethod::employmentType();

        $company_id = auth()->user()->company_id;

        $this->job = Job::Select(
            'jobs.id', 'jobs.title', 'jobs.work_type', 'jobs.slug', 'jobs.company_id', 'jobs.job_description', 'jobs.job_requirement', 'jobs.total_positions', 'jobs.location_id', 'jobs.category_id',
            'jobs.start_date', 'jobs.end_date', 'jobs.status', 'jobs.required_columns', 'jobs.meta_details', 'jobs.section_visibility', 'jobs.created_at', 'jobs.updated_at',
            'jobs.priority', 'jobs.client_id', 'jobs.min_exp', 'jobs.max_exp', 'jobs.min_salary', 'jobs.max_salary', 'jobs.qualification', 'jobs.employment_type',
            'jobs.min_salary_currency', 'jobs.max_salary_currency', 'jobs.min_sal_mode', 'jobs.max_sal_mode', 'jobs.carrier_website',
            'clients.name as client_name', 'clients.description as client_description', 'clients.logo_logical_path',
            'job_categories.name as cat_name',
            'qualification_master.name as qualification_name'
        )
            ->join('clients', 'clients.id', '=', 'jobs.client_id')
            ->leftJoin('job_categories', 'job_categories.id', '=', 'jobs.category_id')
            ->leftJoin('qualification_master', 'qualification_master.id', '=', 'jobs.qualification')
            ->findOrFail($id);

        $this->skillArr = DB::table('job_metronics_skills')->select('*')->where('job_id', $this->job->id)->get();

        // Company Data
        $company_arr = DB::table('companies')->select('*')->where('id', $this->job->company_id)->first();
        $this->company_name = @$company_arr->company_name;
        $this->category_name = 'N/A';

        $this->jobLocation = '';

        if ($this->job->id > 0) {

            /*$jobid 		= $row->id;
            $client_loc 	= DB::table('client_location_jobs')
            ->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
            ->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')
            ->select(
            'client_location.address', 'client_location.city',  'client_location.postcode', 'client_location.country_id',
            'city_list.city as city_name'
            )
            ->where('client_location_jobs.job_id', $jobid)
            ->first();

            $country_id		=	$country_name = '';
            $country_id		=	@$client_loc->country_id;
            if(@$country_id > 0){
            $country_name	= GeneralMethod::getCountryName($country_id);
            foreach($country_name as $cval){
            $country_name 	=  ucwords($cval['name']);
            }
            }

            $city				=	@$client_loc->city;
            if(@$client_loc->country_id == 101){
            $city			    =	@$client_loc->city_name;
            }
            */

            $this->jobLocation = DB::table('client_location_jobs')
                ->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
                ->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')
                                    // ->join('countries', 'countries.id', '=', 'client_location.country_id')
                ->select('client_location.city', 'client_location.address', 'client_location.postcode', 'client_location.country_id', 'city_list.city as city_name')
                ->where('client_location_jobs.job_id', $this->job->id)
                ->orderBy('client_location_jobs.id', 'DESC')
                ->first();

            $this->jobLocationCount = DB::table('client_location_jobs')
                ->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
                ->join('countries', 'countries.id', '=', 'client_location.country_id')
                ->select('client_location.city', 'client_location.address', 'client_location.postcode', 'countries.country_name')
                ->where('client_location_jobs.job_id', $this->job->id)
                ->orderBy('client_location_jobs.id', 'DESC')
                ->count();

        }

        /*$this->assignTeam				=	DB:: table('job_assign')
                                        ->join('users', 'users.id', '=', 'job_assign.user_id')
                                        ->select('users.id', 'users.name', 'users.email',  'users.image')
                                        ->where('job_assign.job_id', $this->job->id)
                                        ->orderBy('users.name', 'DESC')
                                        ->get();*/

        $this->assignTeamCount = DB::table('job_assign')
            ->join('users', 'users.id', '=', 'job_assign.user_id')
            ->select('users.id', 'users.name', 'users.email')
            ->where('job_assign.job_id', $this->job->id)
            ->orderBy('users.name', 'DESC')
            ->count();

        // RECRUITER LIST
        $this->recruiterlist = DB::table('job_assign')
            ->join('users', 'users.id', '=', 'job_assign.user_id')
            ->join('role_user', 'role_user.user_id', 'users.id')
            ->join('roles', 'roles.id', 'role_user.role_id')
            ->leftJoin('job_applications', function ($join) {
                $join->on('job_applications.job_id', '=', 'job_assign.job_id')
                    ->on('job_applications.submitted_by', '=', 'users.id');
            })
            ->select(
                'users.id', 'users.name', 'users.email', 'users.image', 'users.mobile', 'roles.display_name',
                DB::raw('count(job_applications.id) as allCandidate'),
                DB::raw('SUM(CASE WHEN job_applications.master_status_id = 3 THEN 1 ELSE 0 END) AS Interview'),
                DB::raw('SUM(CASE WHEN job_applications.master_status_id = 6 THEN 1 ELSE 0 END) AS Joined')
            )
            ->where('job_assign.company_id', $company_id)
            ->where('job_assign.job_id', $this->job->id)
            ->where('users.company_id', $company_id)
            ->where('users.type', '=', 0)
            ->groupBy('users.id')
            ->orderBy('users.name', 'DESC')
            ->get();

        // STAFFING PARTNER
        $this->staffing_partner_listing = DB::table('recommendation_relation')
            ->join('companies', 'companies.id', '=', 'recommendation_relation.to_company_id')
            ->join('clients', function ($join) {
                $join->on('clients.company_id', '=', 'companies.id');
                $join->on('clients.self_company', '=', DB::raw("'1'"));
                // $join->on('recommendation_relation.job_id', '=', DB::raw("'".$jobId."'") );

            })
            ->join('job_applications', 'job_applications.thirdparty_company_id', '=', 'recommendation_relation.to_company_id')
            ->select(
                'companies.id', 'companies.company_name', 'companies.logo',

                'clients.name as clients_name', 'clients.logo_logical_path',

                DB::raw('count(job_applications.id) as allCandidate'),
                DB::raw('SUM(CASE WHEN job_applications.master_status_id = 3 THEN 1 ELSE 0 END) AS Interview'),
                DB::raw('SUM(CASE WHEN job_applications.master_status_id = 6 THEN 1 ELSE 0 END) AS Joined')
            )
            ->where('recommendation_relation.from_company_id', $company_id)
            ->where('recommendation_relation.job_id', $this->job->id)
            ->where('recommendation_relation.final_status', 1)
                                            // ->where('users.type', '=', 0)
            ->groupBy('companies.id')
            ->orderBy('companies.company_name', 'DESC')
            ->get();

        $this->jobCitySel = DB::table('client_city_jobs')
            ->join('city_list', 'city_list.city_id', '=', 'client_city_jobs.city_id')
            ->where('client_city_jobs.job_id', $this->job->id)
            ->pluck('city_list.city')
            ->toArray();

        if ($this->job->work_type == 'yes') {
            $this->jobCitySelected = 'Remote';
        } elseif ($this->job->work_type == 'no') {
            $this->jobCitySelected = implode(',', $this->jobCitySel);
        } elseif ($this->job->work_type == 'any') {
            $this->jobCitySelected = 'Remote/'.implode(',', $this->jobCitySel);

        }

        $view = view('product_company.jobs.show', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'view' => $view]);

    }

    public function teamdata()
    {

        abort_if(! $this->user->cans('view_team'), 403);

        $company_id = auth()->user()->company_id;

        $users = User::select('users.id', 'users.name', 'users.email', 'roles.display_name', 'users.created_at', 'users.status', 'users.image');
        $users = $users->join('role_user', 'role_user.user_id', 'users.id');
        $users = $users->join('roles', 'roles.id', 'role_user.role_id');
        // $users		=	$users->join('clients', 'clients.id', 'jobs.client_id')
        // $users		=	$users->join('master_status', 'master_status.id', 'job_applications.master_status_id')
        $users = $users->where('users.company_id', '=', $company_id);

        if (\request('search') != '') {
            $search = request('search');
            // $users		=	$users->where('users.name', 'LIKE', "%$search%");
        }

        return DataTables::of($users)
            ->editColumn('name', function ($row) {
                return '<img src='.$row->profile_image_url.' class="img-circle" width="25" /> '.ucwords($row->name);
            })
            ->editColumn('email', function ($row) {
                return $row->email;
            })
            ->addColumn('role_name', function ($row) {
                return $row->display_name;
            })
            ->addColumn('action', function ($row) {
                $action = '';

                if ($row->id == $this->user->id) {
                    return $action;
                }

                if ($this->user->cans('edit_team')) {
                    $action .= '
                    <a href="'.route('product_company.team.edit', [$row->id]).'" class="btn" data-toggle="tooltip" data-original-title="'.__('app.edit').'">
                      <i class="fa fa-pencil" aria-hidden="true"></i>
                  </a>';
                }

                if ($this->user->cans('delete_team')) {
                    $action .= '
                    <a href="javascript:;" class="btn sa-params" data-toggle="tooltip" data-row-id="'.$row->id.'" data-original-title="'.__('app.delete').'">
                        <i class="fa fa-times text-danger" aria-hidden="true"></i>
                    </a>';
                }

                return $action;
            })
            ->editColumn('chkbox', function ($row) {
                return '<div class="form-check form-check-sm form-check-custom form-check-solid"><input class="form-check-input form-check-input1" type="checkbox" value="1"></div>';
            })
            ->editColumn('Name', function ($row) {

                $bgTextDecore = $strVal = '';
                $str = trim(@$row->name);
                $str = $strVal = strtolower($str[0]);
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

                $icon = '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left" >
                   				 <a href="javascript:void(0);" data-row-id="'.$row->id.'" class="show-detail-team" id="kt_activities3_toggle" >
                      				<div class="symbol-label fs-3 '.@$bgTextDecore.'  border-radius-5">'.ucwords(@$strVal).'</div>
                    			</a>
                  		</div>';

                if (! empty($row->image)) {
                    $imagePath = '';
                    $imagePath = url('public/user-uploads/team_profile_img/'.@$row->image);
                    $icon = '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2  border-radius-5" style="float:left">
                                        <a href="javascript:void(0);">
                                            <div class="symbol-label border-radius-5">
                                            <img src="'.$imagePath.'" alt="User Image" class="border-radius-5 w-100">
                                            </div>
                                        </a>
                                    </div>';
                }

                $departmentArr = '------';

                return $icon.'<div class="d-flex flex-column">
                    <a href="javascript:void(0);" data-row-id="'.$row->id.'" class="text-dark fw-bolder text-hover-primary fs-6 show-detail-team"  data-toggle="tooltip1" data-html="true" title="'.ucwords(@$row->name).'">'.ucwords(substr(@$row->name, 0, 15)).'</a>
                    <a href="javascript:void(0);" data-row-id="'.$row->id.'" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary show-detail-team">'.@$departmentArr.'</a>
                  </div>
                  ';
            })
            ->editColumn('Role', function ($row) {
                return '<div class="d-flex flex-column">
                                       <span class="text-dark fw-bolder fs-6">'.ucwords($row->display_name).'</span>
                                    </div>';
            })
            ->editColumn('Status', function ($row) {

                $chk = '';
                $status = $row->status;
                if ($status == 'active') {
                    $chk = 'checked="checked"';
                }

                return ' <div class="form-check form-switch form-switch-sm form-check-custom form-check-solid">
                                       <input class="form-check-input chkStatus" type="checkbox" value="active" name="staus" '.@$chk.' onclick="handleClick(this, '.$row->id.');">
                                    </div>';
            })
            ->editColumn('AddedDate', function ($row) {

                $created_at = $row->created_at;
                // $currentDateTime = '08/04/2010 22:15:00';
                $strtotime = strtotime($created_at);
                $newDate = date('d-M-Y', $strtotime);
                $newDateTime = date('h:i A', $strtotime);

                return ' <div class="d-flex flex-column">
                                       <span class="text-dark fw-bolder fs-6">'.@$newDate.'</span>
                                       <span class="text-muted fw-bold d-block fs-7">'.@$newDateTime.'</span>
                                    </div>';
            })
            ->rawColumns(['chkbox', 'Name', 'Role', 'Status', 'AddedDate',  'role_name', 'action'])
            ->addIndexColumn()
            ->make(true);

    }

    public function spartnerlisting($id)
    {

        // abort_if(! $this->user->cans('view_staffing_partners'), 403);

        $this->salaryMode = GeneralMethod::salaryMode();
        $this->employmentType = GeneralMethod::employmentType();

        $this->job = Job::Select(
            'jobs.id', 'jobs.title', 'jobs.slug', 'jobs.company_id', 'jobs.job_description', 'jobs.job_requirement', 'jobs.total_positions', 'jobs.location_id', 'jobs.category_id', 'jobs.start_date', 'jobs.end_date', 'jobs.status', 'jobs.required_columns', 'jobs.meta_details', 'jobs.section_visibility', 'jobs.created_at', 'jobs.updated_at', 'jobs.priority', 'jobs.client_id', 'jobs.min_exp', 'jobs.max_exp', 'jobs.min_salary', 'jobs.max_salary', 'jobs.qualification', 'jobs.employment_type', 'jobs.min_salary_currency', 'jobs.max_salary_currency', 'jobs.min_sal_mode', 'jobs.max_sal_mode', 'jobs.carrier_website',
            'clients.name as client_name', 'clients.description as client_description', 'clients.logo_logical_path',
            'job_categories.name as cat_name',
            'qualification_master.name as qualification_name'
        )
            ->join('clients', 'clients.id', '=', 'jobs.client_id')
            ->leftJoin('job_categories', 'job_categories.id', '=', 'jobs.category_id')
            ->leftJoin('qualification_master', 'qualification_master.id', '=', 'jobs.qualification')
            ->findOrFail($id);

        $this->skillArr = DB::table('job_metronics_skills')->select('*')->where('job_id', $this->job->id)->get();

        // Company Data
        $company_arr = DB::table('companies')->select('*')->where('id', $this->job->company_id)->first();
        $this->company_name = @$company_arr->company_name;
        $this->category_name = 'N/A';

        $this->jobLocation = '';

        if ($this->job->id > 0) {

            $this->jobLocation = DB::table('client_location_jobs')
                ->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
                ->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')
                ->select('client_location.city', 'client_location.address', 'client_location.postcode', 'client_location.country_id', 'city_list.city as city_name')
                ->where('client_location_jobs.job_id', $this->job->id)
                ->orderBy('client_location_jobs.id', 'DESC')
                ->first();

            $this->jobLocationCount = DB::table('client_location_jobs')
                ->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
                ->join('countries', 'countries.id', '=', 'client_location.country_id')
                ->select('client_location.city', 'client_location.address', 'client_location.postcode', 'countries.country_name')
                ->where('client_location_jobs.job_id', $this->job->id)
                ->orderBy('client_location_jobs.id', 'DESC')
                ->count();

        }

        $this->assignTeam = DB::table('job_assign')
            ->join('users', 'users.id', '=', 'job_assign.user_id')
            ->select('users.id', 'users.name', 'users.email', 'users.image')
            ->where('job_assign.job_id', $this->job->id)
            ->orderBy('users.name', 'DESC')
            ->get();

        $this->assignTeamCount = DB::table('job_assign')
            ->join('users', 'users.id', '=', 'job_assign.user_id')
            ->select('users.id', 'users.name', 'users.email')
            ->where('job_assign.job_id', $this->job->id)
            ->orderBy('users.name', 'DESC')
            ->count();

        $company_id = auth()->user()->company_id;
        $this->spartnerArr = DB::table('users')
            ->leftjoin('job_assign', 'users.id', 'job_assign.user_id')
            ->select('users.id', 'users.name', 'users.email', 'users.image', 'job_assign.job_id as job_assign_id')
            ->where('users.company_id', $company_id)
            ->where('users.type', 1)
            ->orderBy('users.name', 'DESC')
            ->get();

        $this->job_id = $id;

        // JOB SKILLS
        $currentJobSkill = DB::table('job_metronics_skills')
            ->select('job_metronics_skills.skill_name')
            ->where('job_metronics_skills.job_id', $this->job->id)
            ->get();

        $jobId = $this->job->id;
        $current_company_id = auth()->user()->company_id;
        $current_job_employment_type = $this->job->employment_type;
        $this->boosterListing = DB::table('companies')
            ->select(
                'companies.id', 'companies.company_name', 'companies.logo', 'companies.industries',
                'jobs.title',
                DB::raw('count(DISTINCT job_applications.job_id) as SimilarReq'),
                DB::raw('count(DISTINCT job_applications.id) as Profiles'),
                'jobs.employment_type',
                'job_metronics_skills.skill_name',
                'td_industries.ind_name',
                'staffing_company_relation.from_company_id', 'staffing_company_relation.to_company_id', 'staffing_company_relation.final_status',
                'recommendation_relation.from_status as rel_from_status', 'recommendation_relation.to_status as rel_to_status', 'recommendation_relation.to_status as rel_final_status',
                'recommendation_relation.job_id as rel_job_id', 'recommendation_relation.from_company_id as rel_from_company_id',

                'package_active.newjobrequest_status', 'package_active.newjobrequest_balance',
                'package_active.newclientrequest_status', 'package_active.newclientrequest_balance'
            );

        if ($current_job_employment_type == 0) {				// 0 : Fulltime(In job)      New Job Request :: Fulltime Recruitment Leads  FULLTIME_RECRUITMENT_LEAD(In package_active)
            $this->boosterListing = $this->boosterListing->join('package_active', function ($join) {
                $join->on('package_active.company_id', '=', 'jobs.company_id')
                    ->on('package_active.newjobrequest_balance', '>', DB::raw('0'))
                    ->on('package_active.newjobrequest_status', '=', DB::raw('1'));
            });
        } elseif ($current_job_employment_type == 1) {			// 1	: Contract(In job)      New Client Request :: Contract Recruitment Lead   CONTRACT_RECRUITMENT_LEAD(In package_active)
            $this->boosterListing = $this->boosterListing->join('package_active', function ($join) {
                $join->on('package_active.company_id', '=', 'jobs.company_id')
                    ->on('package_active.newclientrequest_balance', '>', DB::raw('0'))
                    ->on('package_active.newclientrequest_status', '=', DB::raw('1'));
            });
        }

        $this->boosterListing = $this->boosterListing->leftJoin('jobs', function ($join) use ($current_company_id) {
            $join->on('jobs.company_id', '=', 'companies.id')
                ->on('jobs.company_id', '!=', DB::raw("'".$current_company_id."'"));
        });

        $this->boosterListing = $this->boosterListing->leftJoin('job_applications', 'job_applications.job_id', 'jobs.id');
        $this->boosterListing = $this->boosterListing->leftJoin('job_metronics_skills', 'job_metronics_skills.job_id', '=', 'jobs.id');
        $this->boosterListing = $this->boosterListing->leftJoin('staffing_company_relation', function ($join) use ($current_company_id) {
            $join->on('staffing_company_relation.to_company_id', '=', 'companies.id');
            $join->on('staffing_company_relation.from_company_id', '=', DB::raw("'".$current_company_id."'"));
        });
        $this->boosterListing = $this->boosterListing->leftJoin('recommendation_relation', function ($join) use ($jobId) {
            $join->on('recommendation_relation.to_company_id', '=', 'companies.id');
            $join->on('recommendation_relation.final_status', '!=', DB::raw("'-1'"));
            $join->on('recommendation_relation.job_id', '=', DB::raw("'".$jobId."'"));
        });
        $this->boosterListing = $this->boosterListing->leftJoin('td_industries', 'td_industries.ind_id', '=', 'companies.industries');

        $this->boosterListing = $this->boosterListing->whereIn('companies.company_type', [1, 2]);

        // ################################################################################# ------------- TITLE MATCH
        $findstring = explode(' ', $this->job->title);
        foreach ($findstring as $value) {
            $this->boosterListing = $this->boosterListing->orWhere('jobs.title', 'like', "%{$value}%")->whereIn('companies.company_type', [1, 2]);
        }
        if (! empty($currentJobSkill)) {
            foreach ($currentJobSkill as $jskill) {
                $this->boosterListing = $this->boosterListing->orWhere('jobs.title', 'like', "%{$jskill->skill_name}%")->whereIn('companies.company_type', [1, 2]);
                $this->boosterListing = $this->boosterListing->orWhere('job_metronics_skills.skill_name', 'like', "%{$jskill->skill_name}%")->whereIn('companies.company_type', [1, 2]);
            }
        }

        // ################################################################################# ------------- EXPERIENCE MATCH
        /*if($this->job->min_exp >=0 && $this->job->max_exp >=0){
            $this->boosterListing 	=	$this->boosterListing->orWhereBetween('jobs.min_exp', [$this->job->min_exp, $this->job->max_exp])->whereIn('companies.company_type', array(1,2) );
            $this->boosterListing 	=	$this->boosterListing->orWhereBetween('jobs.max_exp', [$this->job->min_exp, $this->job->max_exp])->whereIn('companies.company_type', array(1,2) );
        }*/

        $this->boosterListing = $this->boosterListing->where('companies.id', '>', 0)->whereIn('companies.company_type', [1, 2]);
        $this->boosterListing = $this->boosterListing->where(function ($query) use ($current_company_id) {
            return $query->where('companies.id', '!=', $current_company_id)->whereIn('companies.company_type', [1, 2]);
        });

        // START BOOSTER PACKAGE

        $this->boosterListing = $this->boosterListing->where(function ($query) {
            return $query->where('package_active.boostkeywords_status', '=', DB::raw('1'));
            // ->where('clients.self_company', '!=', 1);
        });

        $this->boosterListing = $this->boosterListing->where('companies.id', '!=', $current_company_id)->whereIn('companies.company_type', [1, 2]);
        $this->boosterListing = $this->boosterListing->groupBy('companies.id');
        $this->boosterListing = $this->boosterListing->orderBy(DB::raw('RAND()'));
        $this->boosterListing = $this->boosterListing->offset(0)->limit(2);
        // $this->boosterListing 	=	$this->boosterListing->get();

        // dd(\DB::getQueryLog());
        // echo '<pre>'; print_r($this->boosterListing->toSql()); exit;

        $view = view('product_company.jobs.spartnerlisting', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'view' => $view]);

    }

    public function spartnerAssign(Request $request)
    {
        $company_id = auth()->user()->company_id;
        $id = $request->id;
        $assign = $request->assign;
        $jobid = $request->jobid;

        if ($assign == 1) {
            DB::table('job_assign')->where('company_id', $company_id)->where('user_id', $id)->where('job_id', $jobid)->delete();
            // INSERT
            $insertArr = ['company_id' => $company_id, 'user_id' => $id, 'job_id' => $jobid];
            DB::table('job_assign')->insert($insertArr);

        } else {
            // DELETE
            DB::table('job_assign')->where('company_id', $company_id)->where('user_id', $id)->where('job_id', $jobid)->delete();
        }

    }

    public function all_jobs($id)
    {
        $this->pageTitle = __('Job Request');

        return view('product_company.jobs.all_jobs', $this->data);
    }

    public function recommended_request($id)
    {

        $from_company_id = auth()->user()->company_id;
        $from_user_id = $this->user->id;
        $to_company_id = $id;

        $jobid = $_REQUEST['jobid'];

        $fromCounter = DB::table('recommendation_relation')->select('*')
            ->where('from_company_id', $from_company_id)
            ->where('to_company_id', $to_company_id)
            ->where('job_id', $jobid)
            ->count();

        $toCounter = DB::table('recommendation_relation')->select('*')
            ->where('from_company_id', $to_company_id)
            ->where('to_company_id', $from_company_id)
            ->where('job_id', $jobid)
            ->count();

        if (($fromCounter == 0)) {
            // INSERT
            $insertArr = [
                'from_company_id' => $from_company_id,
                'from_user_id' => $from_user_id,
                'from_status' => 1,
                'from_created_at' => date('Y-m-d'),

                'to_company_id' => $to_company_id,
                'to_status' => 0,
                'final_status' => 0,
                'job_id' => $jobid,

            ];
            DB::table('recommendation_relation')->insert($insertArr);

        }

        return Reply::dataOnly(['status' => 'success']);

    }

    public function rsp_job_request_send($id)
    {

        $from_company_id = auth()->user()->company_id;
        $from_user_id = $this->user->id;
        $to_company_id = $id;

        $jobid = $_REQUEST['jobid'];

        $fromCounter = DB::table('recommendation_relation')->select('*')
            ->where('from_company_id', $from_company_id)
            ->where('to_company_id', $to_company_id)
            ->where('job_id', $jobid)
            ->count();

        $toCounter = DB::table('recommendation_relation')->select('*')
            ->where('from_company_id', $to_company_id)
            ->where('to_company_id', $from_company_id)
            ->where('job_id', $jobid)
            ->count();

        if (($fromCounter == 0)) {
            // INSERT
            $insertArr = [
                'from_company_id' => $from_company_id,
                'from_user_id' => $from_user_id,
                'from_status' => 1,
                'from_created_at' => date('Y-m-d'),

                'to_company_id' => $to_company_id,
                'to_status' => 0,
                'final_status' => 0,
                'job_id' => $jobid,

            ];
            DB::table('recommendation_relation')->insert($insertArr);
            $msg = 'Recommended request sent successfully !';

            return Reply::dataOnly(['status' => 'success', 'message' => $msg]);

        } else {
            $msg = 'Recommended request already sent !';

            return Reply::dataOnly(['status' => 'warning', 'message' => $msg]);
        }

    }

    public function send_job_recommanded_request($id)
    {

        $from_company_id = auth()->user()->company_id;
        $from_user_id = $this->user->id;
        $to_company_id = $id;
        $jobid = $_REQUEST['current_jobid'];

        $clientRow = DB::table('clients')->select('clients.id')->where('clients.company_id', '=', $from_company_id)->where('clients.self_company', '=', 1)->first();
        $jobRow = DB::table('jobs')->select('jobs.id', 'jobs.employment_type')->where('jobs.id', '=', $jobid)->first();

        // $to_company_id

        $boostKeyWork_Auth = DB::table('package_active')
            ->select('package_active.id')
            ->where('package_active.boostkeywords_status', '=', 1)
            ->where('package_active.boostkeywords_balance', '>', 0)
            ->where('package_active.company_id', '=', $to_company_id)
            ->count();

        if ($boostKeyWork_Auth > 0) {
            $packageStatus = '';
            $packageStatus = GeneralMethod::validateClientPackage('BOOSTKEYWORDS', $to_company_id);

            if (($packageStatus['STATUS'] == 1) && ($packageStatus['EXPIRE'] == 'false') && ($packageStatus['BALANCE'] > 0)) { 		// BOOST STAFFING PARTNER

                // DEDUCTION BOOST PROFILE
                if (@$jobid > 0) {
                    $source_from_company_id = $to_company_id;
                    $event_name = 'BOOSTKEYWORDS';
                    $event_table_name = 'jobs';
                    $event_table_id = @$jobid;
                    $packageStatus = GeneralMethod::clientPackageRedeem($event_name, $event_table_name, $event_table_id, $source_from_company_id);
                }

            } else {
                return Reply::dataOnly(['status' => 'warning', 'message' => "Boost keywords can't access because Staffing partne PACKAGE EXPIRED , please contact to Administrator."]);
            }

        }

        if ($jobRow->employment_type == 1) {		//	CONTRACT_RECRUITMENT_LEAD   employment_type = 1

            $packageStatus = GeneralMethod::validateClientPackage('CONTRACT_RECRUITMENT_LEAD', $to_company_id);
            if (($packageStatus['STATUS'] == 1) && ($packageStatus['EXPIRE'] == 'false') && ($packageStatus['BALANCE'] > 0)) {
                // DEDUCTION CONTRACT_RECRUITMENT_LEAD
                if (@$jobid > 0) {
                    $source_from_company_id = $to_company_id;
                    $event_name = 'CONTRACT_RECRUITMENT_LEAD';
                    $event_table_name = 'jobs';
                    $event_table_id = @$jobid;
                    $packageStatus = GeneralMethod::clientPackageRedeem($event_name, $event_table_name, $event_table_id, $source_from_company_id);
                }
            } else {
                return Reply::dataOnly(['status' => 'warning', 'message' => "Contract Recruitment Lead can't access because Staffing partne PACKAGE EXPIRED , please contact to Administrator."]);
            }

        } elseif ($jobRow->employment_type == 0) {	//	FULLTIME_RECRUITMENT_LEAD   employment_type = 0

            $packageStatus = GeneralMethod::validateClientPackage('FULLTIME_RECRUITMENT_LEAD', $to_company_id);
            if (($packageStatus['STATUS'] == 1) && ($packageStatus['EXPIRE'] == 'false') && ($packageStatus['BALANCE'] > 0)) {
                // DEDUCTION FULLTIME_RECRUITMENT_LEAD
                if (@$jobid > 0) {
                    $source_from_company_id = $to_company_id;
                    $event_name = 'FULLTIME_RECRUITMENT_LEAD';
                    $event_table_name = 'jobs';
                    $event_table_id = @$jobid;
                    $packageStatus = GeneralMethod::clientPackageRedeem($event_name, $event_table_name, $event_table_id, $source_from_company_id);
                }
            } else {
                return Reply::dataOnly(['status' => 'warning', 'message' => "Fulltime Recruitment Leads can't access because Staffing partne PACKAGE EXPIRED , please contact to Administrator."]);
            }
        }

        // ###################################################         START CLIENT INVITATION

        // CHECK CLIENT INVITATION SENT OR NOT
        $clientInvitationCounter = DB::table('staffing_company_invitation')->select('*')
            ->where('from_company_id', $from_company_id)
            ->where('to_company_id', $to_company_id)
            ->where('to_invitation_status', '!=', -1)
            ->count();
        if ($clientInvitationCounter == 0) {		// INVITATION SENT :- IN CASE NO INVITATION AVAIL
            // GET COMPANY DETAILS
            $company_row = DB::table('companies')->select('companies.company_email')->where('companies.id', '=', $to_company_id)->first();
            // INVITATION
            // staffing_company_invitation
            $insertArr = [
                'from_company_id' => $from_company_id,
                'from_client_id' => $clientRow->id,
                'from_user_id' => $from_user_id,
                'from_status' => 1,
                'from_created_at' => date('Y-m-d'),

                'to_company_id' => $to_company_id,
                'to_email' => $company_row->company_email,
                'to_status' => 0,
                'job_id' => $jobid,
                'to_invitation_status' => 0,
            ];

            DB::table('staffing_company_invitation')->insert($insertArr);
        }

        // ################################################             END CLIENT INVITATION

        // ###############################################				CHECK ACCOUNT REGISTER WITH US OR NOT

        $fromCounter = DB::table('staffing_company_relation')->select('*')
            ->where('from_company_id', $from_company_id)
            ->where('to_company_id', $to_company_id)
            ->where('final_status', '!=', -1)
            ->count();
        if ($fromCounter == 0) {
            // NOW REGISTER REQUEST SEND
            $insertArr = [
                'from_company_id' => $from_company_id,
                'from_client_id' => $clientRow->id,
                'from_user_id' => $from_user_id,
                'from_status' => 1,
                'from_created_at' => date('Y-m-d'),

                'to_company_id' => $to_company_id,
                'to_client_id' => 0,
                'to_user_id' => 0,
                'to_status' => 0,
                'to_created_at' => date('Y-m-d'),

                'final_status' => 0,
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),

            ];
            // DB:: table('staffing_company_relation')->insert($insertArr);
        }

        // ###############################################				END CHECK ACCOUNT REGISTER WITH US OR NOT

        // ###############################################				SEND JOB INVITATION

        $jobCounter = DB::table('recommendation_relation')->select('*')
            ->where('from_company_id', $from_company_id)
            ->where('to_company_id', $to_company_id)
            ->where('job_id', $jobid)
            ->where('final_status', '!=', -1)
            ->count();
        if ($jobCounter == 0) {
            // INSERT
            $insertArr = [
                'from_company_id' => $from_company_id,
                'from_user_id' => $from_user_id,
                'from_status' => 1,
                'from_created_at' => date('Y-m-d'),

                'to_company_id' => $to_company_id,
                'to_status' => 0,
                'final_status' => 0,
                'job_id' => $jobid,

            ];
            DB::table('recommendation_relation')->insert($insertArr);
        }

        // ###############################################				END  SEND JOB INVITATION

        return Reply::dataOnly(['status' => 'success']);

    }

    public function send_only_job_invitation(Request $request)
    {
        $jobid = $fromcompany = $tocompany = '';

        $from_company_id = auth()->user()->company_id;
        $from_user_id = $this->user->id;

        $jobid = $request->jobid;
        $fromcompany = $request->fromcompany;
        $tocompany = $request->tocompany;

        $boostKeyWork_Auth = DB::table('package_active')
            ->select('package_active.id')
            ->where('package_active.boostkeywords_status', '=', 1)
            ->where('package_active.boostkeywords_balance', '>', 0)
            ->where('package_active.company_id', '=', $tocompany)
            ->count();

        if ($boostKeyWork_Auth > 0) {
            $packageStatus = '';
            $packageStatus = GeneralMethod::validateClientPackage('BOOSTKEYWORDS', $tocompany);

            if (($packageStatus['STATUS'] == 1) && ($packageStatus['EXPIRE'] == 'false') && ($packageStatus['BALANCE'] > 0)) { 		// BOOST STAFFING PARTNER

                // DEDUCTION BOOST PROFILE
                if (@$jobid > 0) {
                    $source_from_company_id = $tocompany;
                    $event_name = 'BOOSTKEYWORDS';
                    $event_table_name = 'jobs';
                    $event_table_id = @$jobid;
                    $packageStatus = GeneralMethod::clientPackageRedeem($event_name, $event_table_name, $event_table_id, $source_from_company_id);
                }

            } else {
                return Reply::dataOnly(['status' => 'warning', 'message' => "Boost keywords can't access because Staffing partne PACKAGE EXPIRED , please contact to Administrator."]);
            }

        }

        $jobRow = DB::table('jobs')->select('jobs.id', 'jobs.employment_type')->where('jobs.id', '=', $jobid)->first();
        if ($jobRow->employment_type == 1) {		//	CONTRACT_RECRUITMENT_LEAD   employment_type = 1

            $packageStatus = GeneralMethod::validateClientPackage('CONTRACT_RECRUITMENT_LEAD', $tocompany);
            if (($packageStatus['STATUS'] == 1) && ($packageStatus['EXPIRE'] == 'false') && ($packageStatus['BALANCE'] > 0)) {
                // DEDUCTION CONTRACT_RECRUITMENT_LEAD
                if (@$jobid > 0) {
                    $source_from_company_id = $tocompany;
                    $event_name = 'CONTRACT_RECRUITMENT_LEAD';
                    $event_table_name = 'jobs';
                    $event_table_id = @$jobid;
                    $packageStatus = GeneralMethod::clientPackageRedeem($event_name, $event_table_name, $event_table_id, $source_from_company_id);
                }
            } else {
                return Reply::dataOnly(['status' => 'warning', 'message' => "Contract Recruitment Lead can't access because Staffing partne PACKAGE EXPIRED , please contact to Administrator."]);
            }

        } elseif ($jobRow->employment_type == 0) {	//	FULLTIME_RECRUITMENT_LEAD   employment_type = 0

            $packageStatus = GeneralMethod::validateClientPackage('FULLTIME_RECRUITMENT_LEAD', $tocompany);
            if (($packageStatus['STATUS'] == 1) && ($packageStatus['EXPIRE'] == 'false') && ($packageStatus['BALANCE'] > 0)) {
                // DEDUCTION FULLTIME_RECRUITMENT_LEAD
                if (@$jobid > 0) {
                    $source_from_company_id = $tocompany;
                    $event_name = 'FULLTIME_RECRUITMENT_LEAD';
                    $event_table_name = 'jobs';
                    $event_table_id = @$jobid;
                    $packageStatus = GeneralMethod::clientPackageRedeem($event_name, $event_table_name, $event_table_id, $source_from_company_id);
                }
            } else {
                return Reply::dataOnly(['status' => 'warning', 'message' => "Fulltime Recruitment Leads can't access because Staffing partne PACKAGE EXPIRED , please contact to Administrator."]);
            }
        }

        $fromCounter = DB::table('recommendation_relation')->select('*')
            ->where('from_company_id', $fromcompany)
            ->where('to_company_id', $tocompany)
            ->where('job_id', $jobid)
            ->count();

        if (($fromCounter == 0)) {
            // INSERT
            $insertArr = [
                'from_company_id' => $fromcompany,
                'from_user_id' => $from_user_id,
                'from_status' => 1,
                'from_created_at' => date('Y-m-d'),

                'to_company_id' => $tocompany,
                'to_status' => 0,
                'final_status' => 0,
                'job_id' => $jobid,

            ];
            DB::table('recommendation_relation')->insert($insertArr);
            $msg = 'Recommended request sent successfully !';

            return Reply::dataOnly(['status' => 'success', 'message' => $msg]);

        } else {
            $msg = 'Recommended request already sent !';

            return Reply::dataOnly(['status' => 'warning', 'message' => $msg]);
        }

    }

    public function hmlisting_08feb2022($id)
    {

        // abort_if(! $this->user->cans('view_staffing_partners'), 403);

        $this->salaryMode = GeneralMethod::salaryMode();
        $this->employmentType = GeneralMethod::employmentType();

        $this->job = Job::Select(
            'jobs.id', 'jobs.title', 'jobs.slug', 'jobs.company_id', 'jobs.job_description', 'jobs.job_requirement', 'jobs.total_positions', 'jobs.location_id', 'jobs.category_id', 'jobs.start_date', 'jobs.end_date', 'jobs.status', 'jobs.required_columns', 'jobs.meta_details', 'jobs.section_visibility', 'jobs.created_at', 'jobs.updated_at', 'jobs.priority', 'jobs.client_id', 'jobs.min_exp', 'jobs.max_exp', 'jobs.min_salary', 'jobs.max_salary', 'jobs.qualification', 'jobs.employment_type', 'jobs.min_salary_currency', 'jobs.max_salary_currency', 'jobs.min_sal_mode', 'jobs.max_sal_mode', 'jobs.carrier_website',
            'clients.name as client_name', 'clients.description as client_description', 'clients.logo_logical_path',
            'job_categories.name as cat_name',
            'qualification_master.name as qualification_name'
        )
            ->join('clients', 'clients.id', '=', 'jobs.client_id')
            ->leftJoin('job_categories', 'job_categories.id', '=', 'jobs.category_id')
            ->leftJoin('qualification_master', 'qualification_master.id', '=', 'jobs.qualification')
            ->findOrFail($id);

        $this->skillArr = DB::table('job_metronics_skills')->select('*')->where('job_id', $this->job->id)->get();

        // Company Data
        $company_arr = DB::table('companies')->select('*')->where('id', $this->job->company_id)->first();
        $this->company_name = @$company_arr->company_name;
        $this->category_name = 'N/A';

        $this->jobLocation = '';

        if ($this->job->id > 0) {

            $this->jobLocation = DB::table('client_location_jobs')
                ->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
                ->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')
                ->select('client_location.city', 'client_location.address', 'client_location.postcode', 'client_location.country_id', 'city_list.city as city_name')
                ->where('client_location_jobs.job_id', $this->job->id)
                ->orderBy('client_location_jobs.id', 'DESC')
                ->first();

            $this->jobLocationCount = DB::table('client_location_jobs')
                ->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
                ->join('countries', 'countries.id', '=', 'client_location.country_id')
                ->select('client_location.city', 'client_location.address', 'client_location.postcode', 'countries.country_name')
                ->where('client_location_jobs.job_id', $this->job->id)
                ->orderBy('client_location_jobs.id', 'DESC')
                ->count();

        }

        $this->assignTeam = DB::table('job_assign')
            ->join('users', 'users.id', '=', 'job_assign.user_id')
            ->select('users.id', 'users.name', 'users.email', 'users.image')
            ->where('job_assign.job_id', $this->job->id)
            ->orderBy('users.name', 'DESC')
            ->get();

        $this->assignTeamCount = DB::table('job_assign')
            ->join('users', 'users.id', '=', 'job_assign.user_id')
            ->select('users.id', 'users.name', 'users.email')
            ->where('job_assign.job_id', $this->job->id)
            ->orderBy('users.name', 'DESC')
            ->count();

        $company_id = auth()->user()->company_id;
        $this->spartnerArr = DB::table('users')
            ->leftjoin('job_assign', 'users.id', 'job_assign.user_id')
            ->select('users.id', 'users.name', 'users.email', 'users.image', 'job_assign.job_id as job_assign_id')
            ->where('users.company_id', $company_id)
            ->where('users.type', 1)
            ->orderBy('users.name', 'DESC')
            ->get();

        $this->job_id = $id;

        $view = view('product_company.jobs.hmlisting', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'view' => $view]);

    }

    public function hmlisting($id)
    {

        $this->currentJobId = $id;
        $this->recruiter = [];

        $company_id = auth()->user()->company_id;
        $this->team = DB::table('users')
            ->leftJoin('job_assign', function ($join) use ($id) {
                $join->on('job_assign.user_id', '=', 'users.id');
                $join->on('job_assign.job_id', '=', DB::raw("'".$id."'"));
            })
            ->leftjoin('role_user', 'role_user.user_id', 'users.id')
            ->leftjoin('roles', 'roles.id', 'role_user.role_id')
            ->select(
                'users.id', 'users.name', 'users.email', 'users.working_status',
                'job_assign.id as job_assign_id', 'job_assign.job_id',
                'roles.display_name'
            )
            ->where('users.company_id', $company_id)
            ->groupBy('users.id')
            ->get();
        /*$view = view('service_company.jobs.hmlisting', $this->data)->render();
        return Reply::dataOnly(['status' => 'success', 'view' => $view]);*/
        $view = view('product_company.jobs.hmlisting', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'view' => $view]);
    }

    public function hmlistingupdate(Request $request)
    {
        $company_id = auth()->user()->company_id;

        $assignStatus = $request->assignStatus;
        $currentJobId = $request->currentJobId;
        $currentUserId = $request->currentUserId;

        if ($assignStatus == 1) {
            DB::table('job_assign')->where('job_id', $currentJobId)->where('user_id', $currentUserId)->where('company_id', $company_id)->delete();

            return Reply::dataOnly(['status' => 'success', 'msg' => 'Remove Record successfully.']);
        } else {
            DB::table('job_assign')->where('job_id', $currentJobId)->where('user_id', $currentUserId)->where('company_id', $company_id)->delete();
            $values = [
                'company_id' => $company_id,
                'user_id' => $currentUserId,
                'job_id' => $currentJobId,
            ];
            DB::table('job_assign')->insert($values);

            return Reply::dataOnly(['status' => 'success', 'msg' => 'Added Record successfully.']);
        }

    }

    public function recommendedDataold(Request $request)
    {

        // abort_if(!$this->user->cans('view_jobs'), 403);

        $chkAdminRole = DB::table('roles')->select('roles.id')->where('id', $this->user->role->role_id)->where('name', 'admin')->count();
        $job_status = GeneralMethod::job_status();
        $salaryMode = GeneralMethod::salaryMode();
        $currencySymbole = GeneralMethod::currencySymbole();

        $company_id = auth()->user()->company_id;
        $employmentType = GeneralMethod::employmentType();

        $jobId = $request->jobid;
        // JOB DETAIL
        $currentRowJob = Job::select(
            'title', 'min_exp', 'max_exp', 'min_salary', 'max_salary', 'min_salary_currency', 'max_salary_currency', 'min_sal_mode', 'max_sal_mode',
            'employment_type'
        )
            ->where('id', $request->jobid)
            ->first();
        // JOB SKILLS
        $currentJobSkill = DB::table('job_metronics_skills')
            ->select('job_metronics_skills.skill_name')
            ->where('job_metronics_skills.job_id', $request->jobid)
            ->get();

        // JOB LOCATION QUERY
        $currentJobLocation = DB::table('client_location_jobs')
            ->select('client_location.id', 'client_location.city', 'client_location.city_id', 'client_location.country_id')
            ->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
            ->where('client_location_jobs.job_id', $request->jobid)
            ->get();

        $recommendedDataList = DB::table('companies')
            ->select(
                'companies.id', 'companies.company_name', 'companies.logo', 'companies.industries',
                'jobs.title',
                DB::raw('count(DISTINCT job_applications.job_id) as SimilarReq'),
                DB::raw('count(DISTINCT job_applications.id) as Profiles'),
                'jobs.employment_type',
                'job_metronics_skills.skill_name',
                'td_industries.ind_name',

                'staffing_company_relation.from_company_id', 'staffing_company_relation.to_company_id', 'staffing_company_relation.final_status',

                'recommendation_relation.from_status as rel_from_status', 'recommendation_relation.to_status as rel_to_status', 'recommendation_relation.to_status as rel_final_status',
                'recommendation_relation.job_id as rel_job_id', 'recommendation_relation.from_company_id as rel_from_company_id'

            )
        // ->join('jobs', 'jobs.company_id', 'companies.id')
            ->join('jobs', function ($join) use ($company_id) {
                $join->on('jobs.company_id', '=', 'companies.id')
                    ->on('jobs.company_id', '!=', DB::raw("'".$company_id."'"));
                // $join->orOn('recommendation_relation.to_company_id', '=', 'companies.id');
            })
            ->join('job_applications', 'job_applications.job_id', 'jobs.id')
            ->leftJoin('job_metronics_skills', 'job_metronics_skills.job_id', '=', 'jobs.id')
            ->leftJoin('client_location_jobs', 'client_location_jobs.job_id', '=', 'jobs.id')
            ->leftJoin('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')

        // ->leftJoin('staffing_company_relation', 'staffing_company_relation.to_company_id', '=', 'companies.id')
            ->leftJoin('staffing_company_relation', function ($join) use ($company_id) {
                $join->on('staffing_company_relation.to_company_id', '=', 'companies.id');
                $join->on('staffing_company_relation.from_company_id', '=', DB::raw("'".$company_id."'"));
                // $join->orOn('recommendation_relation.to_company_id', '=', 'companies.id');
            })
            ->leftJoin('recommendation_relation', function ($join) use ($jobId) {
                $join->on('recommendation_relation.to_company_id', '=', 'companies.id');
                $join->on('recommendation_relation.final_status', '!=', DB::raw("'-1'"));
                $join->on('recommendation_relation.job_id', '=', DB::raw("'".$jobId."'"));

            })
            ->leftJoin('td_industries', 'td_industries.ind_id', '=', 'companies.industries');

        // ->where('jobs.id', '>', '0')
        // ->take(1);
        //	company_type

        $recommendedDataList = $recommendedDataList->orWhere('jobs.title', 'like', "%{$value}%");
        // ################################################################################# ------------- TITLE MATCH
        $findstring = explode(' ', $currentRowJob->title);
        foreach ($findstring as $value) { // Title match
            $recommendedDataList = $recommendedDataList->orWhere('jobs.title', 'like', "%{$value}%");
        }
        if (! empty($currentJobSkill)) {
            foreach ($currentJobSkill as $jskill) {
                $recommendedDataList = $recommendedDataList->orWhere('jobs.title', 'like', "%{$jskill->skill_name}%");
                $recommendedDataList = $recommendedDataList->orWhere('job_metronics_skills.skill_name', 'like', "%{$jskill->skill_name}%");

            }
        }

        // ################################################################################# ------------- EXPERIENCE MATCH
        if ($currentRowJob->min_exp >= 0 && $currentRowJob->max_exp >= 0) {
            $recommendedDataList = $recommendedDataList->orWhereBetween('jobs.min_exp', [$currentRowJob->min_exp, $currentRowJob->max_exp]);
            $recommendedDataList = $recommendedDataList->orWhereBetween('jobs.max_exp', [$currentRowJob->min_exp, $currentRowJob->max_exp]);
        }

        // ################################################################################# ------------ JOB TYPE
        if ($currentRowJob->employment_type) {
            $recommendedDataList = $recommendedDataList->orWhere('jobs.employment_type', $currentRowJob->employment_type);
        }

        // ################################################################################# ------------  LOCATION
        if (! empty($currentJobLocation)) {
            foreach ($currentJobLocation as $cloc) {
                if ($cloc->country_id == 101) {
                    $recommendedDataList = $recommendedDataList->orWhere('client_location.city_id', $cloc->city_id);
                } else {
                    $recommendedDataList = $recommendedDataList->orWhere('client_location.city', 'like', "%{$cloc->city}%");
                }
            }
        }

        $recommendedDataList = $recommendedDataList->where('companies.id', '>', 0);
        $recommendedDataList = $recommendedDataList->where(function ($query) {
            return $query->where('companies.id', '!=', auth()->user()->company_id);
            // ->where('staffing_company_relation.to_company_id', '=', $company_id)
            // ->where('staffing_company_relation.final_status', '=', 1);
        });

        // $recommendedDataList 	=	$recommendedDataList->where('companies.id', '!=' , auth()->user()->company_id);
        $recommendedDataList = $recommendedDataList->where('companies.id', '!=', auth()->user()->company_id);
        $recommendedDataList = $recommendedDataList->groupBy('companies.id');

        // echo $recommendedDataList 	=	$recommendedDataList->tosql();

        return DataTables::of($recommendedDataList)

            ->editColumn('chkbox', function ($row) {
                return '<div class="form-check form-check-sm form-check-custom form-check-solid">
                      <input class="form-check-input form-check-input-reco" type="checkbox" value="1" />
                    </div>';
            })

            ->editColumn('Name', function ($row) {

                $logo_path = url('img/client-default.png');
                if (! empty(@$row->logo) && strlen(@$row->logo) > 10) {
                    $logo_path = url('public/user-uploads/company-logo/'.@$row->logo);
                }

                return '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2  border-radius-5" style="float:left"> 
								<a href="javascript:void(0);">
                      				<div class="symbol-label border-radius-5"> <img src="'.$logo_path.'" alt="Company Logo" class="border-radius-5 w-100"> </div>
                      			</a> 
							</div>
							<div class="d-flex flex-column"> 
								<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">'.substr(ucwords(@$row->company_name), 0, 15).'</a>   
								<a href="javascript:void(0);" class="fw-bold text-muted d-block fs-7 text-hover-primary">'.substr(ucwords(@$row->ind_name), 0, 15).'</a>
							</div>';
            })

            ->editColumn('Match', function ($row) use ($currentRowJob, $currentJobSkill, $currentJobLocation) {

                $percentage = 0;
                $companies_id = '';
                $companies_id = $row->id;

                $title = $skills = $experience = $salary = $location = $jobtype = 0;

                // ######################################### ------------- SALARY COUNTER
                $recommended_Salary = DB::table('jobs');
                $recommended_Salary = $recommended_Salary->where('jobs.min_salary_currency', $currentRowJob->min_salary_currency);
                $recommended_Salary = $recommended_Salary->where('jobs.max_salary_currency', $currentRowJob->max_salary_currency);
                $recommended_Salary = $recommended_Salary->where('jobs.min_sal_mode', $currentRowJob->min_sal_mode);
                $recommended_Salary = $recommended_Salary->where('jobs.max_sal_mode', $currentRowJob->max_sal_mode);
                $recommended_Salary = $recommended_Salary->whereBetween('jobs.min_salary', [$currentRowJob->min_exp, $currentRowJob->max_exp]);
                $recommended_Salary = $recommended_Salary->whereBetween('jobs.max_salary', [$currentRowJob->min_exp, $currentRowJob->max_exp]);
                $recommended_Salary = $recommended_Salary->where('jobs.company_id', $companies_id);
                $recommended_Salary = $recommended_Salary->count();
                if ($recommended_Salary > 0) {
                    $salary = 15;
                }

                // ######################################### ------------- TITLE COUNTER
                $recommended_Title = DB::table('jobs');
                $findstring = explode(' ', $currentRowJob->title);
                foreach ($findstring as $value) { // Title match
                    if ($value != '') {
                        $recommended_Title = $recommended_Title->orWhere('jobs.title', 'like', "%{$value}%");
                    }
                }
                $recommended_Title = $recommended_Title->where('jobs.company_id', $companies_id);
                $recommended_Title = $recommended_Title->count();
                if ($recommended_Title > 0) {
                    $title = 30;
                }

                // ######################################### ------------- JOBTYPE
                $recommended_Jobtype = DB::table('jobs');
                $recommended_Jobtype = $recommended_Jobtype->where('jobs.company_id', $companies_id);
                $recommended_Jobtype = $recommended_Jobtype->Where('jobs.employment_type', $currentRowJob->employment_type);
                $recommended_Jobtype = $recommended_Jobtype->count();
                if ($recommended_Jobtype > 0) {
                    $jobtype = 10;
                }

                // ######################################### ------------- EXPERIENCE MATCH
                $recommended_Experience = DB::table('jobs');
                $recommended_Experience = $recommended_Experience->where('jobs.company_id', $companies_id);
                if ($currentRowJob->min_exp >= 0 && $currentRowJob->max_exp >= 0) {
                    $recommended_Experience = $recommended_Experience->Where('jobs.min_exp', [$currentRowJob->min_exp, $currentRowJob->max_exp]);
                    $recommended_Experience = $recommended_Experience->Where('jobs.max_exp', [$currentRowJob->min_exp, $currentRowJob->max_exp]);
                }
                $recommended_Experience = $recommended_Experience->count();
                if ($recommended_Experience > 0) {
                    $experience = 15;
                }

                // SKILL COUNTER
                $recommended_Skill = DB::table('companies')
                    ->select('companies.id')
                    ->join('jobs', 'jobs.company_id', 'companies.id')
                    ->join('job_applications', 'job_applications.job_id', 'jobs.id')
                    ->leftJoin('job_metronics_skills', 'job_metronics_skills.job_id', '=', 'jobs.id')
                    ->leftJoin('client_location_jobs', 'client_location_jobs.job_id', '=', 'jobs.id')
                    ->leftJoin('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id');
                if (! empty($currentJobSkill)) {
                    foreach ($currentJobSkill as $jskill) {
                        $recommended_Skill = $recommended_Skill->orWhere('jobs.title', 'like', "%{$jskill->skill_name}%");
                        $recommended_Skill = $recommended_Skill->orWhere('job_metronics_skills.skill_name', 'like', "%{$jskill->skill_name}%");

                    }
                }
                $recommended_Skill = $recommended_Skill->where('jobs.company_id', $companies_id);
                $recommended_Skill = $recommended_Skill->count();
                if ($recommended_Skill > 0) {
                    $skills = 20;
                }

                $recommended_Skill_Name = DB::table('companies')
                    ->select('job_metronics_skills.skill_name')
                    ->join('jobs', 'jobs.company_id', 'companies.id')
                    ->join('job_applications', 'job_applications.job_id', 'jobs.id')
                    ->leftJoin('job_metronics_skills', 'job_metronics_skills.job_id', '=', 'jobs.id');

                if (! empty($currentJobSkill)) {
                    foreach ($currentJobSkill as $jskill) {
                        // $recommended_Skill_Name 	=	$recommended_Skill_Name->orWhere('jobs.title', 'like', "%{$jskill->skill_name}%");
                        $recommended_Skill_Name = $recommended_Skill_Name->orWhere('job_metronics_skills.skill_name', $jskill->skill_name);

                    }
                }
                $recommended_Skill_Name = $recommended_Skill_Name->where('jobs.company_id', $companies_id);
                $recommended_Skill_Name = $recommended_Skill_Name->get();
                $skillname = '';
                $scnt = 0;
                $tempArr = [];
                if (! empty($recommended_Skill_Name)) {
                    foreach ($recommended_Skill_Name as $sname) {
                        $tempArr[] = $sname->skill_name;
                        $scnt++;

                    }
                }
                if (! empty($tempArr)) {
                    $newArr = array_keys(array_flip($tempArr));
                    // $$tempArr	=	array_unique($tempArr);
                    $skillname = implode(',', $newArr);
                }
                // ################################################################################# ------------  LOCATION
                $recommended_Location = DB::table('companies')
                    ->select('client_location.id')
                    ->join('jobs', 'jobs.company_id', 'companies.id')
                    ->join('job_applications', 'job_applications.job_id', 'jobs.id')
                    ->leftJoin('job_metronics_skills', 'job_metronics_skills.job_id', '=', 'jobs.id')
                    ->leftJoin('client_location_jobs', 'client_location_jobs.job_id', '=', 'jobs.id')
                    ->leftJoin('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id');

                if (! empty($currentJobLocation)) {
                    foreach ($currentJobLocation as $cloc) {
                        if ($cloc->country_id == 101) {
                            $recommended_Location = $recommended_Location->orWhere('client_location.city_id', $cloc->city_id);
                        } else {
                            $recommended_Location = $recommended_Location->orWhere('client_location.city', 'like', "%{$cloc->city}%");
                        }
                    }
                }

                $recommended_Location = $recommended_Location->where('jobs.company_id', $companies_id);
                $recommended_Location = $recommended_Location->count();
                if ($recommended_Location > 0) {
                    $location = 10;
                }

                $percentage = $salary + $title + $jobtype + $experience + $skills + $location;

                $percentage_html = '';
                $percentage_html = '
<div class="progress-circle p'.$percentage.'">
  <span>'.$percentage.'%</span>
  <div class="left-half-clipper">
    <div class="first50-bar"></div>
    <div class="value-bar"></div>
  </div>
</div>	';

                /*return '<div class="d-flex flex-column w-100 me-2">
                  <div class="d-flex flex-stack"> <span class="text-muted me-2 fs-7 fw-bold">'.$percentage.'%</span> </div>
                  <div class="progress h-6px w-100">
                    <div class="progress-bar bg-primary" role="progressbar" style="width: '.$percentage.'%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                  </div>
                  <span class="text-muted fw-bold d-block fs-7">'.substr(@$skillname, 0, 20).'</span> </div>'; 		  */

                return '<div class="d-flex flex-column w-100 me-2"> 
		  '.$percentage_html.'
		  <span class="text-muted fw-bold d-block fs-7">'.substr(@$skillname, 0, 20).'</span> </div>';

            })

            ->editColumn('SimilarReq', function ($row) {
                return '<div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">'.$row->SimilarReq.' </span> </div>';
            })

            ->editColumn('Profiles', function ($row) {
                return '<div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">'.$row->Profiles.'</span> </div>';
            })

            ->editColumn('HiringType', function ($row) use ($employmentType) {
                $employment_type_name = '';
                $employment_type_name = $row->employment_type;
                $employment_type_name = $employmentType[$employment_type_name];

                return '<div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">'.$employment_type_name.'</span> </div>';
            })

            ->editColumn('Status', function ($row) use ($company_id) {

                // 'staffing_company_relation.from_company_id', 'staffing_company_relation.to_company_id',  'staffing_company_relation.final_status',

                $regStatus = 'UnRegistered';
                $regStatusCss = 'text';
                if (! empty($row->from_company_id)) {
                    if (($row->from_company_id == $company_id) && ($row->to_company_id == $row->id) && ($row->final_status == 1)) {
                        $regStatus = 'Registered';
                        $regStatusCss = 'text-success';
                    }
                }

                return '<div class="d-flex flex-column"> <span class=" '.@$regStatusCss.' fw-bolder fs-6">'.@$regStatus.'</div>';

            })

            ->editColumn('Action', function ($row) use ($jobId) {

                $company_id = auth()->user()->company_id;
                $id = '';
                $id = $row->id;

                /*'recommendation_relation.from_status as rel_from_status', 'recommendation_relation.to_status as rel_to_status', 'recommendation_relation.to_status as rel_final_status',
                'recommendation_relation.job_id as rel_job_id' */

                if ($id > 0) {

                    if (($company_id == $row->rel_from_company_id) && ($row->rel_final_status == 1) && ($row->rel_job_id == $jobId)) {
                        $html = '<div class="flex-column"> 
											<a href="javascript:void(0);" data-row-id="'.$id.'" class="btn btn-sm btn-light-success w-130 send_request2">
												Assign Job
											</a> 
										</div>';
                    } elseif (($company_id == $row->rel_from_company_id) && ($row->rel_job_id == $jobId) && ($row->rel_final_status != 1)) {
                        $html = '<div class="flex-column"> 
											<a href="javascript:void(0);" data-row-id="'.$id.'" class="btn btn-sm btn-light w-130 send_request1">
												Request sent
											</a>
              							</div>';
                    } else {
                        $html = '<div class="flex-column">
											<a href="javascript:void(0);" data-row-id="'.$id.'"  data-row-jobid="'.$jobId.'" class="btn btn-sm btn-light-primary w-130 send_job_recommanded_request">
												Request Hire
											</a> 
										</div>
										';
                    }

                    return '<div class="flex-column"> 
									'.$html.' 
								</div>';
                }
            })

            ->rawColumns(['chkbox', 'Name', 'Match', 'SimilarReq', 'Profiles', 'HiringType', 'Status', 'Action'])

            ->addIndexColumn()
            ->make(true);

    }

    public function recommendedData_06_sept_2022(Request $request)
    {

        // abort_if(!$this->user->cans('view_jobs'), 403);
        // employment_type
        // 0 	:	Fulltime
        // 1		:	Contract

        $employment_type = '';
        $chkAdminRole = DB::table('roles')->select('roles.id')->where('id', $this->user->role->role_id)->where('name', 'admin')->count();
        $job_status = GeneralMethod::job_status();
        $salaryMode = GeneralMethod::salaryMode();
        $currencySymbole = GeneralMethod::currencySymbole();

        $company_id = auth()->user()->company_id;
        $employmentType = GeneralMethod::employmentType();

        $jobId = $request->jobid;
        // JOB DETAIL
        $currentRowJob = Job::select(
            'title', 'min_exp', 'max_exp', 'min_salary', 'max_salary', 'min_salary_currency', 'max_salary_currency', 'min_sal_mode', 'max_sal_mode',
            'employment_type'
        )
            ->where('id', $request->jobid)
            ->first();
        $parent_employment_type = $currentRowJob->employment_type;

        // JOB SKILLS
        $currentJobSkill = DB::table('job_metronics_skills')
            ->select('job_metronics_skills.skill_name')
            ->where('job_metronics_skills.job_id', $request->jobid)
            ->get();

        // JOB LOCATION QUERY
        $currentJobLocation = DB::table('client_location_jobs')
            ->select('client_location.id', 'client_location.city', 'client_location.city_id', 'client_location.country_id')
            ->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
            ->where('client_location_jobs.job_id', $request->jobid)
            ->get();

        $recommendedDataList = DB::table('companies')
            ->select(
                'companies.id', 'companies.company_name', 'companies.logo', 'companies.industries',
                'jobs.title',
                DB::raw('count(DISTINCT job_applications.job_id) as SimilarReq'),
                DB::raw('count(DISTINCT job_applications.id) as Profiles'),
                'jobs.employment_type',
                'job_metronics_skills.skill_name',
                'td_industries.ind_name',

                'staffing_company_relation.from_company_id', 'staffing_company_relation.to_company_id', 'staffing_company_relation.final_status',

                'recommendation_relation.from_status as rel_from_status', 'recommendation_relation.to_status as rel_to_status', 'recommendation_relation.to_status as rel_final_status',
                'recommendation_relation.job_id as rel_job_id', 'recommendation_relation.from_company_id as rel_from_company_id',

                'package_active.newjobrequest_status', 'package_active.newjobrequest_balance',
                'package_active.newclientrequest_status', 'package_active.newclientrequest_balance',
                'package_active.boostkeywords_status'
            );

        $recommendedDataList = $recommendedDataList->join('jobs', function ($join) use ($company_id) {
            $join->on('jobs.company_id', '=', 'companies.id')
                ->on('jobs.company_id', '!=', DB::raw("'".$company_id."'"));
        });

        // ->join('package_active', 'package_active.company_id', '=', 'companies.id')
        // New Client Request 		:: Contract Recruitment Lead   CONTRACT_RECRUITMENT_LEAD
        // New Job Request	   		:: Fulltime Recruitment Leads  FULLTIME_RECRUITMENT_LEAD
        if ($parent_employment_type == 0) {				// 0 : Fulltime(In job)      New Job Request :: Fulltime Recruitment Leads  FULLTIME_RECRUITMENT_LEAD(In package_active)
            $recommendedDataList = $recommendedDataList->join('package_active', function ($join) {
                $join->on('package_active.company_id', '=', 'jobs.company_id')
                    ->on('package_active.newjobrequest_balance', '>', DB::raw('0'))
                    ->on('package_active.newjobrequest_status', '=', DB::raw('1'))
                    ->on('package_active.boostkeywords_status', '=', DB::raw('0'));

            });
        } elseif ($parent_employment_type == 1) {			// 1	: Contract(In job)      New Client Request :: Contract Recruitment Lead   CONTRACT_RECRUITMENT_LEAD(In package_active)
            $recommendedDataList = $recommendedDataList->join('package_active', function ($join) {
                $join->on('package_active.company_id', '=', 'jobs.company_id')
                    ->on('package_active.newclientrequest_balance', '>', DB::raw('0'))
                    ->on('package_active.newclientrequest_status', '=', DB::raw('1'))
                    ->on('package_active.boostkeywords_status', '=', DB::raw('0'));

            });
        }

        $recommendedDataList = $recommendedDataList->join('job_applications', 'job_applications.job_id', 'jobs.id');
        $recommendedDataList = $recommendedDataList->leftJoin('job_metronics_skills', 'job_metronics_skills.job_id', '=', 'jobs.id');
        $recommendedDataList = $recommendedDataList->leftJoin('client_location_jobs', 'client_location_jobs.job_id', '=', 'jobs.id');
        $recommendedDataList = $recommendedDataList->leftJoin('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id');

        $recommendedDataList = $recommendedDataList->leftJoin('staffing_company_relation', function ($join) use ($company_id) {
            $join->on('staffing_company_relation.to_company_id', '=', 'companies.id');
            $join->on('staffing_company_relation.from_company_id', '=', DB::raw("'".$company_id."'"));
        });

        $recommendedDataList = $recommendedDataList->leftJoin('recommendation_relation', function ($join) use ($jobId) {
            $join->on('recommendation_relation.to_company_id', '=', 'companies.id');
            $join->on('recommendation_relation.final_status', '!=', DB::raw("'-1'"));
            $join->on('recommendation_relation.job_id', '=', DB::raw("'".$jobId."'"));
        });

        /*$recommendedDataList 		= $recommendedDataList->leftJoin('recommendation_relation', function($jobId) use ($jobId){
                                                                $join->on('recommendation_relation.to_company_id', '=', 'companies.id');
                                                                $join->on('recommendation_relation.final_status', '=', DB::raw("'1'"));
                                                                $join->on('recommendation_relation.job_id', '=', DB::raw("'".$jobId."'") );
                                                        });*/

        $superAdminCompanyIds = \App\Helper\GeneralMethod::superAdminCompanyIds();
        $recommendedDataList = $recommendedDataList->whereIn('companies.company_type', [1, 2])
            ->whereNotIn('companies.id', $superAdminCompanyIds);

        $findstring = explode(' ', $currentRowJob->title);
        $recommendedDataList = $recommendedDataList->where(function ($matchQuery) use ($findstring, $currentJobSkill, $currentRowJob, $currentJobLocation) {
            foreach ($findstring as $value) {
                if (! empty($value)) {
                    $matchQuery->orWhere('jobs.title', 'like', "%{$value}%");
                }
            }
            if (! empty($currentJobSkill)) {
                foreach ($currentJobSkill as $jskill) {
                    if (! empty($jskill->skill_name)) {
                        $matchQuery->orWhere('jobs.title', 'like', "%{$jskill->skill_name}%");
                        $matchQuery->orWhere('job_metronics_skills.skill_name', 'like', "%{$jskill->skill_name}%");
                    }
                }
            }
            if ($currentRowJob->min_exp >= 0 && $currentRowJob->max_exp >= 0) {
                $matchQuery->orWhereBetween('jobs.min_exp', [$currentRowJob->min_exp, $currentRowJob->max_exp]);
                $matchQuery->orWhereBetween('jobs.max_exp', [$currentRowJob->min_exp, $currentRowJob->max_exp]);
            }
            if ($currentRowJob->employment_type) {
                $matchQuery->orWhere('jobs.employment_type', $currentRowJob->employment_type);
            }
            if (! empty($currentJobLocation)) {
                foreach ($currentJobLocation as $cloc) {
                    if ($cloc->country_id == 101) {
                        $matchQuery->orWhere('client_location.city_id', $cloc->city_id);
                    } else {
                        $matchQuery->orWhere('client_location.city', 'like', "%{$cloc->city}%");
                    }
                }
            }
        });

        $recommendedDataList = $recommendedDataList->where('companies.id', '!=', auth()->user()->company_id)
            ->whereNotIn('companies.id', $superAdminCompanyIds)
            ->whereIn('companies.company_type', [1, 2]);
        $recommendedDataList = $recommendedDataList->groupBy('companies.id');
        $recommendedDataList = $recommendedDataList->orderBy(DB::raw('RAND()'));

        $recommendedDataList = $recommendedDataList->toSql();

        /*echo '<pre>'; print_r($findstring);
        echo '<pre>'; print_r($currentJobSkill);
        echo '<pre>'; print_r($currentRowJob);
        echo '<pre>'; print_r($recommendedDataList); exit; */

        return DataTables::of($recommendedDataList)

            ->editColumn('chkbox', function ($row) {
                return '<div class="form-check form-check-sm form-check-custom form-check-solid">
                      <input class="form-check-input form-check-input-reco" type="checkbox" value="1" />
                    </div>';
            })
            ->editColumn('Name', function ($row) {

                $logo_path = url('img/client-default.png');
                if (! empty(@$row->logo) && strlen(@$row->logo) > 10) {
                    $logo_path = url('public/user-uploads/company-logo/'.@$row->logo);
                }

                return '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2  border-radius-5" style=" float:left;margin-left: 10px !important;"> 
								<a href="javascript:void(0);">
                      				<div class="symbol-label border-radius-5"> <img src="'.$logo_path.'" alt="Company Logo" class="border-radius-5 w-100"> </div>
                      			</a> 
							</div>
							<div class="d-flex flex-column" style=" float:left; width: 100px !important;"> 
								<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">'.substr(ucwords(@$row->company_name), 0, 15).'</a>   
								<a href="javascript:void(0);" class="fw-bold text-muted d-block fs-7 text-hover-primary">'.substr(ucwords(@$row->ind_name), 0, 15).'</a>
							</div>';
            })
            ->editColumn('Match', function ($row) use ($currentRowJob, $currentJobSkill, $currentJobLocation) {

                $percentage = 0;
                $companies_id = '';
                $companies_id = $row->id;

                $title = $skills = $experience = $salary = $location = $jobtype = 0;

                // ######################################### ------------- SALARY COUNTER
                $recommended_Salary = DB::table('jobs');
                $recommended_Salary = $recommended_Salary->where('jobs.min_salary_currency', $currentRowJob->min_salary_currency);
                $recommended_Salary = $recommended_Salary->where('jobs.max_salary_currency', $currentRowJob->max_salary_currency);
                $recommended_Salary = $recommended_Salary->where('jobs.min_sal_mode', $currentRowJob->min_sal_mode);
                $recommended_Salary = $recommended_Salary->where('jobs.max_sal_mode', $currentRowJob->max_sal_mode);
                $recommended_Salary = $recommended_Salary->whereBetween('jobs.min_salary', [$currentRowJob->min_exp, $currentRowJob->max_exp]);
                $recommended_Salary = $recommended_Salary->whereBetween('jobs.max_salary', [$currentRowJob->min_exp, $currentRowJob->max_exp]);
                $recommended_Salary = $recommended_Salary->where('jobs.company_id', $companies_id);
                $recommended_Salary = $recommended_Salary->count();
                if ($recommended_Salary > 0) {
                    $salary = 15;
                }

                // ######################################### ------------- TITLE COUNTER
                $recommended_Title = DB::table('jobs');
                $findstring = explode(' ', $currentRowJob->title);
                foreach ($findstring as $value) { // Title match
                    if ($value != '') {
                        $recommended_Title = $recommended_Title->orWhere('jobs.title', 'like', "%{$value}%");
                    }
                }
                $recommended_Title = $recommended_Title->where('jobs.company_id', $companies_id);
                $recommended_Title = $recommended_Title->count();
                if ($recommended_Title > 0) {
                    $title = 30;
                }

                // ######################################### ------------- JOBTYPE
                $recommended_Jobtype = DB::table('jobs');
                $recommended_Jobtype = $recommended_Jobtype->where('jobs.company_id', $companies_id);
                $recommended_Jobtype = $recommended_Jobtype->Where('jobs.employment_type', $currentRowJob->employment_type);
                $recommended_Jobtype = $recommended_Jobtype->count();
                if ($recommended_Jobtype > 0) {
                    $jobtype = 10;
                }

                // ######################################### ------------- EXPERIENCE MATCH
                $recommended_Experience = DB::table('jobs');
                $recommended_Experience = $recommended_Experience->where('jobs.company_id', $companies_id);
                if ($currentRowJob->min_exp >= 0 && $currentRowJob->max_exp >= 0) {
                    $recommended_Experience = $recommended_Experience->Where('jobs.min_exp', [$currentRowJob->min_exp, $currentRowJob->max_exp]);
                    $recommended_Experience = $recommended_Experience->Where('jobs.max_exp', [$currentRowJob->min_exp, $currentRowJob->max_exp]);
                }
                $recommended_Experience = $recommended_Experience->count();
                if ($recommended_Experience > 0) {
                    $experience = 15;
                }

                // SKILL COUNTER
                $recommended_Skill = DB::table('companies')
                    ->select('companies.id')
                    ->join('jobs', 'jobs.company_id', 'companies.id')
                    ->join('job_applications', 'job_applications.job_id', 'jobs.id')
                    ->leftJoin('job_metronics_skills', 'job_metronics_skills.job_id', '=', 'jobs.id')
                    ->leftJoin('client_location_jobs', 'client_location_jobs.job_id', '=', 'jobs.id')
                    ->leftJoin('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id');
                if (! empty($currentJobSkill)) {
                    foreach ($currentJobSkill as $jskill) {
                        $recommended_Skill = $recommended_Skill->orWhere('jobs.title', 'like', "%{$jskill->skill_name}%");
                        $recommended_Skill = $recommended_Skill->orWhere('job_metronics_skills.skill_name', 'like', "%{$jskill->skill_name}%");

                    }
                }
                $recommended_Skill = $recommended_Skill->where('jobs.company_id', $companies_id);
                $recommended_Skill = $recommended_Skill->count();
                if ($recommended_Skill > 0) {
                    $skills = 20;
                }

                $recommended_Skill_Name = DB::table('companies')
                    ->select('job_metronics_skills.skill_name')
                    ->join('jobs', 'jobs.company_id', 'companies.id')
                    ->join('job_applications', 'job_applications.job_id', 'jobs.id')
                    ->leftJoin('job_metronics_skills', 'job_metronics_skills.job_id', '=', 'jobs.id');

                if (! empty($currentJobSkill)) {
                    foreach ($currentJobSkill as $jskill) {
                        // $recommended_Skill_Name 	=	$recommended_Skill_Name->orWhere('jobs.title', 'like', "%{$jskill->skill_name}%");
                        $recommended_Skill_Name = $recommended_Skill_Name->orWhere('job_metronics_skills.skill_name', $jskill->skill_name);

                    }
                }
                $recommended_Skill_Name = $recommended_Skill_Name->where('jobs.company_id', $companies_id);
                $recommended_Skill_Name = $recommended_Skill_Name->get();
                $skillname = '';
                $scnt = 0;
                $tempArr = [];
                if (! empty($recommended_Skill_Name)) {
                    foreach ($recommended_Skill_Name as $sname) {
                        $tempArr[] = $sname->skill_name;
                        $scnt++;

                    }
                }
                if (! empty($tempArr)) {
                    $newArr = array_keys(array_flip($tempArr));
                    // $$tempArr	=	array_unique($tempArr);
                    $skillname = implode(',', $newArr);
                }
                // ################################################################################# ------------  LOCATION
                $recommended_Location = DB::table('companies')
                    ->select('client_location.id')
                    ->join('jobs', 'jobs.company_id', 'companies.id')
                    ->join('job_applications', 'job_applications.job_id', 'jobs.id')
                    ->leftJoin('job_metronics_skills', 'job_metronics_skills.job_id', '=', 'jobs.id')
                    ->leftJoin('client_location_jobs', 'client_location_jobs.job_id', '=', 'jobs.id')
                    ->leftJoin('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id');

                if (! empty($currentJobLocation)) {
                    foreach ($currentJobLocation as $cloc) {
                        if ($cloc->country_id == 101) {
                            $recommended_Location = $recommended_Location->orWhere('client_location.city_id', $cloc->city_id);
                        } else {
                            $recommended_Location = $recommended_Location->orWhere('client_location.city', 'like', "%{$cloc->city}%");
                        }
                    }
                }

                $recommended_Location = $recommended_Location->where('jobs.company_id', $companies_id);
                $recommended_Location = $recommended_Location->count();
                if ($recommended_Location > 0) {
                    $location = 10;
                }

                $percentage = $salary + $title + $jobtype + $experience + $skills + $location;

                $percentage_html = '';
                $percentage_html = '
<div class="progress-circle p'.$percentage.'">
  <span>'.$percentage.'%</span>
  <div class="left-half-clipper">
    <div class="first50-bar"></div>
    <div class="value-bar"></div>
  </div>
</div>	';

                /*return '<div class="d-flex flex-column w-100 me-2">
                  <div class="d-flex flex-stack"> <span class="text-muted me-2 fs-7 fw-bold">'.$percentage.'%</span> </div>
                  <div class="progress h-6px w-100">
                    <div class="progress-bar bg-primary" role="progressbar" style="width: '.$percentage.'%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                  </div>
                  <span class="text-muted fw-bold d-block fs-7">'.substr(@$skillname, 0, 20).'</span> </div>'; 		  */

                return '<div class="d-flex flex-column w-100 me-2" style="width: 150px !important; margin-left: 30px !important;"> 
		  '.$percentage_html.'
		  <span class="text-muted fw-bold d-block fs-7">'.substr(@$skillname, 0, 15).'</span> </div>';

            })
            ->editColumn('SimilarReq', function ($row) {
                return '<div class="d-flex flex-column" style="width: 90px !important; margin-left: 10px !important;"> <span class="text-dark fw-bolder fs-6">'.$row->SimilarReq.' </span> </div>';
            })
            ->editColumn('Profiles', function ($row) {
                return '<div class="d-flex flex-column" style="width: 60px !important; margin-left: 0px !important;"> <span class="text-dark fw-bolder fs-6">'.$row->Profiles.'</span> </div>';
            })

            ->editColumn('HiringType', function ($row) {

                $empType = '';
                if (($row->newjobrequest_status == 1 && $row->newjobrequest_balance > 0) && ($row->newclientrequest_status == 1 && $row->newclientrequest_balance > 0)) {
                    $empType = 'Full-time/Contract';
                } elseif ($row->newjobrequest_status == 1 && $row->newjobrequest_balance > 0) {
                    $empType = 'Full-time';
                } elseif ($row->newclientrequest_status == 1 && $row->newclientrequest_balance > 0) {
                    $empType = 'Contract';
                }

                return '<div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">'.$empType.'</span> </div>';
            })
            ->editColumn('Status', function ($row) use ($company_id) {

                // 'staffing_company_relation.from_company_id', 'staffing_company_relation.to_company_id',  'staffing_company_relation.final_status',

                $regStatus = 'UnRegistered';
                $regStatusCss = 'text';
                if (! empty($row->from_company_id)) {
                    if (($row->from_company_id == $company_id) && ($row->to_company_id == $row->id) && ($row->final_status == 1)) {
                        $regStatus = 'Registered';
                        $regStatusCss = 'text-success';
                    }
                }

                return '<div class="d-flex flex-column"> <span class=" '.@$regStatusCss.' fw-bolder fs-6">'.@$regStatus.'</div>';

            })
            ->editColumn('Action', function ($row) use ($jobId) {

                $from_company = $company_id = auth()->user()->company_id;
                $id = '';
                $id = $row->id;

                $html = '';
                $recommendateCounter = DB::table('recommendation_relation')
                    ->select('recommendation_relation.id')
                    ->where('recommendation_relation.from_company_id', $from_company)
                    ->where('recommendation_relation.to_company_id', $id)
                    ->where('recommendation_relation.job_id', $jobId)
                                                // ->where('recommendation_relation.final_status', '=', 1)
                    ->count();

                if ($recommendateCounter == 1) {

                    $recommendateRow = DB::table('recommendation_relation')
                        ->select('recommendation_relation.id', 'recommendation_relation.from_company_id', 'recommendation_relation.from_status', 'recommendation_relation.to_status', 'recommendation_relation.final_status')
                        ->where('recommendation_relation.from_company_id', $from_company)
                        ->where('recommendation_relation.to_company_id', $id)
                        ->where('recommendation_relation.job_id', $jobId)
                    // ->where('recommendation_relation.final_status', '=', 1)
                        ->first();

                    if (($recommendateRow->final_status == 0) || ($recommendateRow->final_status == '')) {
                        $html = '<div class="d-flex flex-column"> <a href="javascript:void(0);" class="btn btn-sm btn-light-success w-130">Request Sent</a> </div>';
                    } elseif ($recommendateRow->final_status == -1) {
                        $html = '<a href="javascript:void(0);" data-row-id="'.$id.'" data-row-jobid="'.@$jobId.'" data-row-ename="ignore" class="btn btn-sm btn-light-success w-130 resend_job_recommanded_request">Assign Job</a>';
                    } elseif ($recommendateRow->final_status == 2) {
                        $html = '<a href="javascript:void(0);" data-row-id="'.$id.'" data-row-jobid="'.@$jobId.'" data-row-ename="remove" class="btn btn-sm btn-light-success w-130 resend_job_recommanded_request">Assign Job</a>';
                    } else {
                        $html = '<div class="d-flex flex-column"> 
<a href="javascript:void(0);" data-row-tabname="Registered" data-row-jobid="'.$jobId.'" data-row-fromcompany="'.$company_id.'" data-row-tocompany="'.$row->to_company_id.'" class="btn btn-sm btn-light-danger w-130 remove_job">
Remove
</a>
</div>';
                    }

                } else {
                    if ($row->to_company_id == $id) {     // ###############    						   REGISTER NOT ASSIGN
                        $html = '<div class="d-flex flex-column"> 
<a href="javascript:void(0);" data-row-jobid="'.$jobId.'" data-row-fromcompany="'.$from_company.'" data-row-tocompany="'.$id.'" class="btn btn-sm btn-secondary  w-130 send_only_job_invitation">
Assign Job
</a> 
</div>';
                    } else { // ##########################################    							UNREGISTER
                        $html = '<div class="flex-column">
<a href="javascript:void(0);" data-row-id="'.$id.'"  data-row-jobid="'.$jobId.'" class="btn btn-sm btn-light-primary w-130 send_job_recommanded_request">
Request Hire
</a> 
</div>';
                    }

                }

                /*	if($id > 0)
                    {


                        if( ($company_id == $row->rel_from_company_id ) && ($row->rel_final_status ==1 )  && ($row->rel_job_id == $jobId)  )
                        {
                            $html	=	'<div class="flex-column">
                                            <a href="javascript:void(0);" data-row-id="'.$id.'" class="btn btn-sm btn-light-success w-130 send_request2">
                                                Assign Job
                                            </a>
                                        </div>';
                        }
                        else if( ($company_id == $row->rel_from_company_id ) && ($row->rel_job_id == $jobId) && ($row->rel_final_status !=1 )  )
                        {
                            $html	=	'<div class="flex-column">
                                            <a href="javascript:void(0);" data-row-id="'.$id.'" class="btn btn-sm btn-light w-130 send_request1">
                                                Request sent
                                            </a>
                                          </div>';
                        }
                        else
                        {
                            $html	=	'<div class="flex-column">
                                            <a href="javascript:void(0);" data-row-id="'.$id.'"  data-row-jobid="'.$jobId.'" class="btn btn-sm btn-light-primary w-130 send_job_recommanded_request">
                                                Request Hire
                                            </a>
                                        </div>
                                        ';
                        }

                        return '<div class="flex-column">
                                    '.$html.'
                                </div>';
                    } */

                return $html;

            })

            ->rawColumns(['chkbox', 'Name', 'Match', 'SimilarReq', 'Profiles', 'HiringType', 'Status', 'Action'])

            ->addIndexColumn()
            ->make(true);
    }

    public function recommendedData(Request $request)
    {

        // abort_if(!$this->user->cans('view_jobs'), 403);
        // employment_type
        // 0 	:	Fulltime
        // 1		:	Contract

        $employment_type = '';
        $chkAdminRole = DB::table('roles')->select('roles.id')->where('id', $this->user->role->role_id)->where('name', 'admin')->count();
        $job_status = GeneralMethod::job_status();
        $salaryMode = GeneralMethod::salaryMode();
        $currencySymbole = GeneralMethod::currencySymbole();

        $company_id = auth()->user()->company_id;
        $employmentType = GeneralMethod::employmentType();

        $jobId = $request->jobid;
        // JOB DETAIL
        $currentRowJob = Job::select(
            'title', 'min_exp', 'max_exp', 'min_salary', 'max_salary', 'min_salary_currency', 'max_salary_currency', 'min_sal_mode', 'max_sal_mode',
            'employment_type'
        )
            ->where('id', $request->jobid)
            ->first();
        $parent_employment_type = $currentRowJob->employment_type;

        // JOB SKILLS
        $currentJobSkill = DB::table('job_metronics_skills')
            ->select('job_metronics_skills.skill_name')
            ->where('job_metronics_skills.job_id', $request->jobid)
            ->get();

        // JOB LOCATION QUERY
        $currentJobLocation = DB::table('client_location_jobs')
            ->select('client_location.id', 'client_location.city', 'client_location.city_id', 'client_location.country_id')
            ->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
            ->where('client_location_jobs.job_id', $request->jobid)
            ->get();

        $recommendedDataList = DB::table('companies')
            ->select(
                'companies.id', 'companies.company_name', 'companies.logo', 'companies.industries',
                'jobs.title',
                DB::raw('count(DISTINCT job_applications.job_id) as SimilarReq'),
                DB::raw('count(DISTINCT job_applications.id) as Profiles'),
                'jobs.employment_type',
                'job_metronics_skills.skill_name',
                'td_industries.ind_name',

                'staffing_company_relation.from_company_id', 'staffing_company_relation.to_company_id', 'staffing_company_relation.final_status',

                'recommendation_relation.from_status as rel_from_status', 'recommendation_relation.to_status as rel_to_status', 'recommendation_relation.to_status as rel_final_status',
                'recommendation_relation.job_id as rel_job_id', 'recommendation_relation.from_company_id as rel_from_company_id',

                'package_active.newjobrequest_status', 'package_active.newjobrequest_balance',
                'package_active.newclientrequest_status', 'package_active.newclientrequest_balance',
                'package_active.boostkeywords_status'
            );

        $recommendedDataList = $recommendedDataList->join('jobs', function ($join) use ($company_id) {
            $join->on('jobs.company_id', '=', 'companies.id')
                ->on('jobs.company_id', '!=', DB::raw("'".$company_id."'"));
        });

        // ->join('package_active', 'package_active.company_id', '=', 'companies.id')
        // New Client Request 		:: Contract Recruitment Lead   CONTRACT_RECRUITMENT_LEAD
        // New Job Request	   		:: Fulltime Recruitment Leads  FULLTIME_RECRUITMENT_LEAD
        if ($parent_employment_type == 0) {				// 0 : Fulltime(In job)      New Job Request :: Fulltime Recruitment Leads  FULLTIME_RECRUITMENT_LEAD(In package_active)
            $recommendedDataList = $recommendedDataList->join('package_active', function ($join) {
                $join->on('package_active.company_id', '=', 'jobs.company_id')
                    ->on('package_active.newjobrequest_balance', '>', DB::raw('0'))
                    ->on('package_active.newjobrequest_status', '=', DB::raw('1'))
                    ->on('package_active.boostkeywords_status', '=', DB::raw('0'));

            });
        } elseif ($parent_employment_type == 1) {			// 1	: Contract(In job)      New Client Request :: Contract Recruitment Lead   CONTRACT_RECRUITMENT_LEAD(In package_active)
            $recommendedDataList = $recommendedDataList->join('package_active', function ($join) {
                $join->on('package_active.company_id', '=', 'jobs.company_id')
                    ->on('package_active.newclientrequest_balance', '>', DB::raw('0'))
                    ->on('package_active.newclientrequest_status', '=', DB::raw('1'))
                    ->on('package_active.boostkeywords_status', '=', DB::raw('0'));

            });
        }

        $recommendedDataList = $recommendedDataList->join('job_applications', 'job_applications.job_id', 'jobs.id');
        $recommendedDataList = $recommendedDataList->leftJoin('job_metronics_skills', 'job_metronics_skills.job_id', '=', 'jobs.id');
        $recommendedDataList = $recommendedDataList->leftJoin('client_location_jobs', 'client_location_jobs.job_id', '=', 'jobs.id');
        $recommendedDataList = $recommendedDataList->leftJoin('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id');

        $recommendedDataList = $recommendedDataList->leftJoin('staffing_company_relation', function ($join) use ($company_id) {
            $join->on('staffing_company_relation.to_company_id', '=', 'companies.id');
            $join->on('staffing_company_relation.from_company_id', '=', DB::raw("'".$company_id."'"));
        });
        $recommendedDataList = $recommendedDataList->leftJoin('recommendation_relation', function ($join) use ($jobId) {
            $join->on('recommendation_relation.to_company_id', '=', 'companies.id');
            $join->on('recommendation_relation.final_status', '=', DB::raw("'1'"));
            $join->on('recommendation_relation.job_id', '=', DB::raw("'".$jobId."'"));
        });

        $recommendedDataList = $recommendedDataList->leftJoin('td_industries', 'td_industries.ind_id', '=', 'companies.industries');
        $recommendedDataList = $recommendedDataList->whereIn('companies.company_type', [1, 2])->whereNull('recommendation_relation.final_status');

        // $recommendedDataList 		=	$recommendedDataList->whereNotIn('package_active.boostkeywords_status', [1] );

        // ################################################################################# ------------- TITLE MATCH
        $findstring = explode(' ', $currentRowJob->title);
        foreach ($findstring as $value) {
            $recommendedDataList = $recommendedDataList->orWhere('jobs.title', 'like', "%{$value}%")->whereIn('companies.company_type', [1, 2])->whereNull('recommendation_relation.final_status');
        }
        if (! empty($currentJobSkill)) {
            foreach ($currentJobSkill as $jskill) {
                $recommendedDataList = $recommendedDataList->orWhere('jobs.title', 'like', "%{$jskill->skill_name}%")->whereIn('companies.company_type', [1, 2])->whereNull('recommendation_relation.final_status');
                $recommendedDataList = $recommendedDataList->orWhere('job_metronics_skills.skill_name', 'like', "%{$jskill->skill_name}%")->whereIn('companies.company_type', [1, 2])->whereNull('recommendation_relation.final_status');
            }
        }

        // ################################################################################# ------------- EXPERIENCE MATCH
        if ($currentRowJob->min_exp >= 0 && $currentRowJob->max_exp >= 0) {
            $recommendedDataList = $recommendedDataList->orWhereBetween('jobs.min_exp', [$currentRowJob->min_exp, $currentRowJob->max_exp])->whereIn('companies.company_type', [1, 2])->whereNull('recommendation_relation.final_status');
            $recommendedDataList = $recommendedDataList->orWhereBetween('jobs.max_exp', [$currentRowJob->min_exp, $currentRowJob->max_exp])->whereIn('companies.company_type', [1, 2])->whereNull('recommendation_relation.final_status');
        }

        // ################################################################################# ------------ JOB TYPE
        if ($currentRowJob->employment_type) {
            $recommendedDataList = $recommendedDataList->orWhere('jobs.employment_type', $currentRowJob->employment_type)->whereIn('companies.company_type', [1, 2])->whereNull('recommendation_relation.final_status');
        }

        // ################################################################################# ------------  LOCATION
        if (! empty($currentJobLocation)) {
            foreach ($currentJobLocation as $cloc) {
                if ($cloc->country_id == 101) {
                    $recommendedDataList = $recommendedDataList->orWhere('client_location.city_id', $cloc->city_id)->whereIn('companies.company_type', [1, 2])->whereNull('recommendation_relation.final_status');
                } else {
                    $recommendedDataList = $recommendedDataList->orWhere('client_location.city', 'like', "%{$cloc->city}%")->whereIn('companies.company_type', [1, 2])->whereNull('recommendation_relation.final_status');
                }
            }
        }

        $recommendedDataList = $recommendedDataList->where('companies.id', '>', 0)->whereIn('companies.company_type', [1, 2])->whereNull('recommendation_relation.final_status');
        $recommendedDataList = $recommendedDataList->where(function ($query) {
            return $query->where('companies.id', '!=', auth()->user()->company_id)->whereIn('companies.company_type', [1, 2])->whereNull('recommendation_relation.final_status');
        });

        // ->on('package_active.boostkeywords_status', '!=',  DB::raw('1') )
        // $recommendedDataList 	=	$recommendedDataList->where('package_active.boostkeywords_status', '!=',  1);

        $recommendedDataList = $recommendedDataList->where('companies.id', '!=', auth()->user()->company_id)
            ->whereNotIn('companies.id', \App\Helper\GeneralMethod::superAdminCompanyIds())
            ->where('recommendation_relation.final_status', '!=', 1)
            ->whereIn('companies.company_type', [1, 2]);

        $recommendedDataList = $recommendedDataList->groupBy('companies.id');
        $recommendedDataList = $recommendedDataList->orderBy(DB::raw('RAND()'));

        // $recommendedDataList 	=	$recommendedDataList->take(1);  recommendation_relation.final_status

        /*echo '<pre>'; print_r($findstring);
        echo '<pre>'; print_r($currentJobSkill);
        echo '<pre>'; print_r($currentRowJob);
        echo '<pre>'; print_r($recommendedDataList->toSql()); exit; */

        return DataTables::of($recommendedDataList)

            ->editColumn('chkbox', function ($row) {
                return '<div class="form-check form-check-sm form-check-custom form-check-solid">
                      <input class="form-check-input form-check-input-reco" type="checkbox" value="1" />
                    </div>';
            })
            ->editColumn('Name', function ($row) {

                $logo_path = url('img/client-default.png');
                if (! empty(@$row->logo) && strlen(@$row->logo) > 10) {
                    $logo_path = url('public/user-uploads/company-logo/'.@$row->logo);
                }

                return '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2  border-radius-5" style=" float:left;margin-left: 10px !important;"> 
								<a href="javascript:void(0);">
                      				<div class="symbol-label border-radius-5"> <img src="'.$logo_path.'" alt="Company Logo" class="border-radius-5 w-100"> </div>
                      			</a> 
							</div>
							<div class="d-flex flex-column" style=" float:left; width: 100px !important;"> 
								<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">'.substr(ucwords(@$row->company_name), 0, 15).'</a>   
								<a href="javascript:void(0);" class="fw-bold text-muted d-block fs-7 text-hover-primary">'.substr(ucwords(@$row->ind_name), 0, 15).'</a>
							</div>';
            })
            ->editColumn('Match', function ($row) use ($currentRowJob, $currentJobSkill, $currentJobLocation) {

                $percentage = 0;
                $companies_id = '';
                $companies_id = $row->id;

                $title = $skills = $experience = $salary = $location = $jobtype = 0;

                // ######################################### ------------- SALARY COUNTER
                $recommended_Salary = DB::table('jobs');
                $recommended_Salary = $recommended_Salary->where('jobs.min_salary_currency', $currentRowJob->min_salary_currency);
                $recommended_Salary = $recommended_Salary->where('jobs.max_salary_currency', $currentRowJob->max_salary_currency);
                $recommended_Salary = $recommended_Salary->where('jobs.min_sal_mode', $currentRowJob->min_sal_mode);
                $recommended_Salary = $recommended_Salary->where('jobs.max_sal_mode', $currentRowJob->max_sal_mode);
                $recommended_Salary = $recommended_Salary->whereBetween('jobs.min_salary', [$currentRowJob->min_exp, $currentRowJob->max_exp]);
                $recommended_Salary = $recommended_Salary->whereBetween('jobs.max_salary', [$currentRowJob->min_exp, $currentRowJob->max_exp]);
                $recommended_Salary = $recommended_Salary->where('jobs.company_id', $companies_id);
                $recommended_Salary = $recommended_Salary->count();
                if ($recommended_Salary > 0) {
                    $salary = 15;
                }

                // ######################################### ------------- TITLE COUNTER
                $recommended_Title = DB::table('jobs');
                $findstring = explode(' ', $currentRowJob->title);
                foreach ($findstring as $value) { // Title match
                    if ($value != '') {
                        $recommended_Title = $recommended_Title->orWhere('jobs.title', 'like', "%{$value}%");
                    }
                }
                $recommended_Title = $recommended_Title->where('jobs.company_id', $companies_id);
                $recommended_Title = $recommended_Title->count();
                if ($recommended_Title > 0) {
                    $title = 30;
                }

                // ######################################### ------------- JOBTYPE
                $recommended_Jobtype = DB::table('jobs');
                $recommended_Jobtype = $recommended_Jobtype->where('jobs.company_id', $companies_id);
                $recommended_Jobtype = $recommended_Jobtype->Where('jobs.employment_type', $currentRowJob->employment_type);
                $recommended_Jobtype = $recommended_Jobtype->count();
                if ($recommended_Jobtype > 0) {
                    $jobtype = 10;
                }

                // ######################################### ------------- EXPERIENCE MATCH
                $recommended_Experience = DB::table('jobs');
                $recommended_Experience = $recommended_Experience->where('jobs.company_id', $companies_id);
                if ($currentRowJob->min_exp >= 0 && $currentRowJob->max_exp >= 0) {
                    $recommended_Experience = $recommended_Experience->Where('jobs.min_exp', [$currentRowJob->min_exp, $currentRowJob->max_exp]);
                    $recommended_Experience = $recommended_Experience->Where('jobs.max_exp', [$currentRowJob->min_exp, $currentRowJob->max_exp]);
                }
                $recommended_Experience = $recommended_Experience->count();
                if ($recommended_Experience > 0) {
                    $experience = 15;
                }

                // SKILL COUNTER
                $recommended_Skill = DB::table('companies')
                    ->select('companies.id')
                    ->join('jobs', 'jobs.company_id', 'companies.id')
                    ->join('job_applications', 'job_applications.job_id', 'jobs.id')
                    ->leftJoin('job_metronics_skills', 'job_metronics_skills.job_id', '=', 'jobs.id')
                    ->leftJoin('client_location_jobs', 'client_location_jobs.job_id', '=', 'jobs.id')
                    ->leftJoin('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id');
                if (! empty($currentJobSkill)) {
                    foreach ($currentJobSkill as $jskill) {
                        $recommended_Skill = $recommended_Skill->orWhere('jobs.title', 'like', "%{$jskill->skill_name}%");
                        $recommended_Skill = $recommended_Skill->orWhere('job_metronics_skills.skill_name', 'like', "%{$jskill->skill_name}%");

                    }
                }
                $recommended_Skill = $recommended_Skill->where('jobs.company_id', $companies_id);
                $recommended_Skill = $recommended_Skill->count();
                if ($recommended_Skill > 0) {
                    $skills = 20;
                }

                $recommended_Skill_Name = DB::table('companies')
                    ->select('job_metronics_skills.skill_name')
                    ->join('jobs', 'jobs.company_id', 'companies.id')
                    ->join('job_applications', 'job_applications.job_id', 'jobs.id')
                    ->leftJoin('job_metronics_skills', 'job_metronics_skills.job_id', '=', 'jobs.id');

                if (! empty($currentJobSkill)) {
                    foreach ($currentJobSkill as $jskill) {
                        // $recommended_Skill_Name 	=	$recommended_Skill_Name->orWhere('jobs.title', 'like', "%{$jskill->skill_name}%");
                        $recommended_Skill_Name = $recommended_Skill_Name->orWhere('job_metronics_skills.skill_name', $jskill->skill_name);

                    }
                }
                $recommended_Skill_Name = $recommended_Skill_Name->where('jobs.company_id', $companies_id);
                $recommended_Skill_Name = $recommended_Skill_Name->get();
                $skillname = '';
                $scnt = 0;
                $tempArr = [];
                if (! empty($recommended_Skill_Name)) {
                    foreach ($recommended_Skill_Name as $sname) {
                        $tempArr[] = $sname->skill_name;
                        $scnt++;

                    }
                }
                if (! empty($tempArr)) {
                    $newArr = array_keys(array_flip($tempArr));
                    // $$tempArr	=	array_unique($tempArr);
                    $skillname = implode(',', $newArr);
                }
                // ################################################################################# ------------  LOCATION
                $recommended_Location = DB::table('companies')
                    ->select('client_location.id')
                    ->join('jobs', 'jobs.company_id', 'companies.id')
                    ->join('job_applications', 'job_applications.job_id', 'jobs.id')
                    ->leftJoin('job_metronics_skills', 'job_metronics_skills.job_id', '=', 'jobs.id')
                    ->leftJoin('client_location_jobs', 'client_location_jobs.job_id', '=', 'jobs.id')
                    ->leftJoin('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id');

                if (! empty($currentJobLocation)) {
                    foreach ($currentJobLocation as $cloc) {
                        if ($cloc->country_id == 101) {
                            $recommended_Location = $recommended_Location->orWhere('client_location.city_id', $cloc->city_id);
                        } else {
                            $recommended_Location = $recommended_Location->orWhere('client_location.city', 'like', "%{$cloc->city}%");
                        }
                    }
                }

                $recommended_Location = $recommended_Location->where('jobs.company_id', $companies_id);
                $recommended_Location = $recommended_Location->count();
                if ($recommended_Location > 0) {
                    $location = 10;
                }

                $percentage = $salary + $title + $jobtype + $experience + $skills + $location;

                $percentage_html = '';

                $percentage_html = '
<div class="progress-circle p'.$percentage.'">
  <span>'.$percentage.'%</span>
  <div class="left-half-clipper">
    <div class="first50-bar"></div>
    <div class="value-bar"></div>
  </div>
</div>	';

                /*return '<div class="d-flex flex-column w-100 me-2">
                  <div class="d-flex flex-stack"> <span class="text-muted me-2 fs-7 fw-bold">'.$percentage.'%</span> </div>
                  <div class="progress h-6px w-100">
                    <div class="progress-bar bg-primary" role="progressbar" style="width: '.$percentage.'%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                  </div>
                  <span class="text-muted fw-bold d-block fs-7">'.substr(@$skillname, 0, 20).'</span> </div>'; 		  */

                return '<div class="d-flex flex-column w-100 me-2" style="width: 150px !important; margin-left: 30px !important;"> 
		  '.$percentage_html.'
		  <span class="text-muted fw-bold d-block fs-7">'.substr(@$skillname, 0, 15).'</span> </div>';

            })
            ->editColumn('SimilarReq', function ($row) {
                return '<div class="d-flex flex-column" style="width: 90px !important; margin-left: 10px !important;"> <span class="text-dark fw-bolder fs-6">'.$row->SimilarReq.' </span> </div>';
            })
            ->editColumn('Profiles', function ($row) {
                return '<div class="d-flex flex-column" style="width: 60px !important; margin-left: 0px !important;"> <span class="text-dark fw-bolder fs-6">'.$row->Profiles.'</span> </div>';
            })

            ->editColumn('HiringType', function ($row) {

                $empType = '';
                if (($row->newjobrequest_status == 1 && $row->newjobrequest_balance > 0) && ($row->newclientrequest_status == 1 && $row->newclientrequest_balance > 0)) {
                    $empType = 'Full-time/Contract';
                } elseif ($row->newjobrequest_status == 1 && $row->newjobrequest_balance > 0) {
                    $empType = 'Full-time';
                } elseif ($row->newclientrequest_status == 1 && $row->newclientrequest_balance > 0) {
                    $empType = 'Contract';
                }

                return '<div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">'.$empType.'</span> </div>';
            })
            ->editColumn('Status', function ($row) use ($company_id) {

                // 'staffing_company_relation.from_company_id', 'staffing_company_relation.to_company_id',  'staffing_company_relation.final_status',

                $regStatus = 'UnRegistered';
                $regStatusCss = 'text';
                if (! empty($row->from_company_id)) {
                    if (($row->from_company_id == $company_id) && ($row->to_company_id == $row->id) && ($row->final_status == 1)) {
                        $regStatus = 'Registered';
                        $regStatusCss = 'text-success';
                    }
                }

                return '<div class="d-flex flex-column"> <span class=" '.@$regStatusCss.' fw-bolder fs-6">'.@$regStatus.'</div>';

            })
            ->editColumn('Action', function ($row) use ($jobId) {

                $from_company = $company_id = auth()->user()->company_id;
                $id = '';
                $id = $row->id;

                $html = '';
                $recommendateCounter = DB::table('recommendation_relation')
                    ->select('recommendation_relation.id')
                    ->where('recommendation_relation.from_company_id', $from_company)
                    ->where('recommendation_relation.to_company_id', $id)
                    ->where('recommendation_relation.job_id', $jobId)
                                                // ->where('recommendation_relation.final_status', '=', 1)
                    ->count();

                if ($recommendateCounter == 1) {

                    $recommendateRow = DB::table('recommendation_relation')
                        ->select('recommendation_relation.id', 'recommendation_relation.from_company_id', 'recommendation_relation.from_status', 'recommendation_relation.to_status', 'recommendation_relation.final_status')
                        ->where('recommendation_relation.from_company_id', $from_company)
                        ->where('recommendation_relation.to_company_id', $id)
                        ->where('recommendation_relation.job_id', $jobId)
                    // ->where('recommendation_relation.final_status', '=', 1)
                        ->first();

                    if (($recommendateRow->final_status == 0) || ($recommendateRow->final_status == '')) {
                        $html = '<div class="d-flex flex-column"> <a href="javascript:void(0);" class="btn btn-sm btn-light-success w-130">Request Sent</a> </div>';
                    } elseif ($recommendateRow->final_status == -1) {
                        $html = '<a href="javascript:void(0);" data-row-id="'.$id.'" data-row-jobid="'.@$jobId.'" data-row-ename="ignore" class="btn btn-sm btn-light-success w-130 resend_job_recommanded_request">Assign Job</a>';
                    } elseif ($recommendateRow->final_status == 2) {
                        $html = '<a href="javascript:void(0);" data-row-id="'.$id.'" data-row-jobid="'.@$jobId.'" data-row-ename="remove" class="btn btn-sm btn-light-success w-130 resend_job_recommanded_request">Assign Job</a>';
                    } else {
                        $html = '<div class="d-flex flex-column"> 
<a href="javascript:void(0);" data-row-tabname="Registered" data-row-jobid="'.$jobId.'" data-row-fromcompany="'.$company_id.'" data-row-tocompany="'.$row->to_company_id.'" class="btn btn-sm btn-light-danger w-130 remove_job">
Remove
</a>
</div>';
                    }

                } else {
                    if ($row->to_company_id == $id) {     // ###############    						   REGISTER NOT ASSIGN
                        $html = '<div class="d-flex flex-column"> 
<a href="javascript:void(0);" id="sp_list_'.$id.'"  data-row-jobid="'.$jobId.'" data-row-fromcompany="'.$from_company.'" data-row-tocompany="'.$id.'" class="btn btn-sm btn-secondary  w-130 send_only_job_invitation">
Assign Job
</a> 
</div>';
                    } else { // ##########################################    							UNREGISTER
                        $html = '<div class="flex-column">
<a href="javascript:void(0);" id="sp_list_'.$id.'"  data-row-id="'.$id.'"  data-row-jobid="'.$jobId.'" class="btn btn-sm btn-light-primary w-130 send_job_recommanded_request">
Request Hire
</a> 
</div>';
                    }

                }

                /*	if($id > 0)
                    {


                        if( ($company_id == $row->rel_from_company_id ) && ($row->rel_final_status ==1 )  && ($row->rel_job_id == $jobId)  )
                        {
                            $html	=	'<div class="flex-column">
                                            <a href="javascript:void(0);" data-row-id="'.$id.'" class="btn btn-sm btn-light-success w-130 send_request2">
                                                Assign Job
                                            </a>
                                        </div>';
                        }
                        else if( ($company_id == $row->rel_from_company_id ) && ($row->rel_job_id == $jobId) && ($row->rel_final_status !=1 )  )
                        {
                            $html	=	'<div class="flex-column">
                                            <a href="javascript:void(0);" data-row-id="'.$id.'" class="btn btn-sm btn-light w-130 send_request1">
                                                Request sent
                                            </a>
                                          </div>';
                        }
                        else
                        {
                            $html	=	'<div class="flex-column">
                                            <a href="javascript:void(0);" data-row-id="'.$id.'"  data-row-jobid="'.$jobId.'" class="btn btn-sm btn-light-primary w-130 send_job_recommanded_request">
                                                Request Hire
                                            </a>
                                        </div>
                                        ';
                        }

                        return '<div class="flex-column">
                                    '.$html.'
                                </div>';
                    } */

                return $html;

            })

            ->rawColumns(['chkbox', 'Name', 'Match', 'SimilarReq', 'Profiles', 'HiringType', 'Status', 'Action'])

            ->addIndexColumn()
            ->make(true);
    }

    public function boostkeywords(Request $request)
    {

        //	Employment	Type
        // 0 	:	Fulltime
        // 1		:	Contract

        $employment_type = '';
        $chkAdminRole = DB::table('roles')->select('roles.id')->where('id', $this->user->role->role_id)->where('name', 'admin')->count();
        $job_status = GeneralMethod::job_status();
        $salaryMode = GeneralMethod::salaryMode();
        $currencySymbole = GeneralMethod::currencySymbole();

        $company_id = auth()->user()->company_id;
        $employmentType = GeneralMethod::employmentType();

        $jobId = $request->jobid;
        // JOB DETAIL
        $currentRowJob = Job::select(
            'title', 'min_salary', 'max_salary', 'min_salary_currency', 'max_salary_currency', 'min_sal_mode', 'max_sal_mode',
            'employment_type'
        )
            ->where('id', $request->jobid)
            ->first();
        $parent_employment_type = $currentRowJob->employment_type;

        // JOB SKILLS
        $currentJobSkill = DB::table('job_metronics_skills')
            ->select('job_metronics_skills.skill_name')
            ->where('job_metronics_skills.job_id', $request->jobid)
            ->get();

        $boostkeywordsDataList = DB::table('companies')
            ->select(
                'companies.id', 'companies.company_name', 'companies.logo', 'companies.industries',
                'jobs.title',
                DB::raw('count(DISTINCT job_applications.job_id) as SimilarReq'),
                DB::raw('count(DISTINCT job_applications.id) as Profiles'),
                'jobs.employment_type',
                'job_metronics_skills.skill_name',
                'td_industries.ind_name',

                'staffing_company_relation.from_company_id', 'staffing_company_relation.to_company_id', 'staffing_company_relation.final_status',

                'recommendation_relation.id as recommendation_relation_id',
                'recommendation_relation.from_status as rel_from_status', 'recommendation_relation.to_status as rel_to_status', 'recommendation_relation.to_status as rel_final_status',
                'recommendation_relation.job_id as rel_job_id', 'recommendation_relation.from_company_id as rel_from_company_id',
                'recommendation_relation.final_status as recommendation_final_status',

                'package_active.newjobrequest_status', 'package_active.newjobrequest_balance',
                'package_active.newclientrequest_status', 'package_active.newclientrequest_balance',
                'package_active.boostkeywords_status'
            );

        // ->join('package_active', 'package_active.company_id', '=', 'companies.id')
        // New Client Request 		:: Contract Recruitment Lead   CONTRACT_RECRUITMENT_LEAD
        // New Job Request	   		:: Fulltime Recruitment Leads  FULLTIME_RECRUITMENT_LEAD
        if ($parent_employment_type == 0) {				// 0 : Fulltime(In job)      New Job Request :: Fulltime Recruitment Leads  FULLTIME_RECRUITMENT_LEAD(In package_active)
            $boostkeywordsDataList = $boostkeywordsDataList->join('package_active', function ($join) {
                $join->on('package_active.company_id', '=', 'companies.id')
                    ->on('package_active.newjobrequest_balance', '>', DB::raw('0'))
                    ->on('package_active.newjobrequest_status', '=', DB::raw('1'))
                    ->on('package_active.boostkeywords_balance', '>', DB::raw('0'))
                    ->on('package_active.boostkeywords_status', '=', DB::raw('1'));
            });
        } elseif ($parent_employment_type == 1) {			// 1	: Contract(In job)      New Client Request :: Contract Recruitment Lead   CONTRACT_RECRUITMENT_LEAD(In package_active)
            $boostkeywordsDataList = $boostkeywordsDataList->join('package_active', function ($join) {
                $join->on('package_active.company_id', '=', 'companies.id')
                    ->on('package_active.newclientrequest_balance', '>', DB::raw('0'))
                    ->on('package_active.newclientrequest_status', '=', DB::raw('1'))
                    ->on('package_active.boostkeywords_balance', '>', DB::raw('0'))
                    ->on('package_active.boostkeywords_status', '=', DB::raw('1'));
            });
        }

        $boostkeywordsDataList = $boostkeywordsDataList->leftJoin('recommendation_relation', function ($join) use ($jobId) {
            $join->on('recommendation_relation.to_company_id', '=', 'companies.id');
            $join->on('recommendation_relation.final_status', '=', DB::raw("'1'"));
            $join->on('recommendation_relation.job_id', '=', DB::raw("'".$jobId."'"));
        });

        $boostkeywordsDataList = $boostkeywordsDataList->leftJoin('jobs', function ($join) use ($company_id) {
            $join->on('jobs.company_id', '=', 'companies.id')
                ->on('jobs.company_id', '!=', DB::raw("'".$company_id."'"));
        });

        $boostkeywordsDataList = $boostkeywordsDataList->leftJoin('job_applications', 'job_applications.job_id', 'jobs.id');
        $boostkeywordsDataList = $boostkeywordsDataList->leftJoin('job_metronics_skills', 'job_metronics_skills.job_id', '=', 'jobs.id');
        $boostkeywordsDataList = $boostkeywordsDataList->leftJoin('client_location_jobs', 'client_location_jobs.job_id', '=', 'jobs.id');
        $boostkeywordsDataList = $boostkeywordsDataList->leftJoin('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id');

        $boostkeywordsDataList = $boostkeywordsDataList->leftJoin('staffing_company_relation', function ($join) use ($company_id) {
            $join->on('staffing_company_relation.to_company_id', '=', 'companies.id');
            $join->on('staffing_company_relation.from_company_id', '=', DB::raw("'".$company_id."'"));
        });

        $boostkeywordsDataList = $boostkeywordsDataList->leftJoin('td_industries', 'td_industries.ind_id', '=', 'companies.industries');

        $findstring = explode(' ', $currentRowJob->title);
        if (! empty($findstring)) {
            $boostkeywordsDataList = $boostkeywordsDataList->orWhere(function ($subquery) use ($findstring, $company_id) {
                $subquery->whereIn('package_active.boostkeywords_text', $findstring);
                $subquery->whereIn('companies.company_type', [1, 2]);
                $subquery->where('companies.id', '!=', $company_id);
                $subquery->whereNull('recommendation_relation.final_status');
            });
        }

        if (! empty($currentJobSkill)) {
            foreach ($currentJobSkill as $jskill) {

                $temp_skill_name = '';
                $temp_skill_name = $jskill->skill_name;
                if (! empty($temp_skill_name)) {
                    $boostkeywordsDataList = $boostkeywordsDataList->orWhere(function ($subquery) use ($temp_skill_name, $company_id) {
                        $subquery->where('package_active.boostkeywords_text', 'like', "%{$temp_skill_name}%");
                        $subquery->whereIn('companies.company_type', [1, 2]);
                        $subquery->where('companies.id', '!=', $company_id);
                        $subquery->whereNull('recommendation_relation.final_status');
                    });
                }

            }
        }

        $boostkeywordsDataList = $boostkeywordsDataList->where('recommendation_relation.final_status', '!=', 1);
        $boostkeywordsDataList = $boostkeywordsDataList->groupBy('companies.id');
        $boostkeywordsDataList = $boostkeywordsDataList->orderBy(DB::raw('RAND()'));
        $boostkeywordsDataList = $boostkeywordsDataList->take(3);

        /*echo '<pre>'; print_r($findstring);
        echo '<pre>'; print_r($currentJobSkill);
        echo $boostkeywordsDataList->toSql();
        exit;*/

        return DataTables::of($boostkeywordsDataList)

            ->editColumn('chkbox', function ($row) {
                return '<div class="form-check form-check-sm form-check-custom form-check-solid">
                      <input class="form-check-input form-check-input-reco" type="checkbox" value="1" />
                    </div>';
            })
            ->editColumn('Name', function ($row) {

                $logo_path = url('img/client-default.png');
                if (! empty(@$row->logo) && strlen(@$row->logo) > 10) {
                    $logo_path = url('public/user-uploads/company-logo/'.@$row->logo);
                }

                return '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2  border-radius-5" style="float:left"> 
								<a href="javascript:void(0);">
                      				<div class="symbol-label border-radius-5"> <img src="'.$logo_path.'" alt="Company Logo" class="border-radius-5 w-100"> </div>
                      			</a> 
							</div>
							<div class="d-flex flex-column"> 
								<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">'.substr(ucwords(@$row->company_name), 0, 15).'</a>   
								<a href="javascript:void(0);" class="fw-bold text-muted d-block fs-7 text-hover-primary">'.substr(ucwords(@$row->ind_name), 0, 15).'</a>
							</div>';
            })
            ->editColumn('Match', function ($row) {

                return '
				<div class="d-flex flex-column w-100 me-2" style="width: 150px !important; margin-left: 30px !important;">
				<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 111.81 122.88" style="enable-background:new 0 0 111.81 122.88; height:30px; width:30px;" xml:space="preserve"><style type="text/css"><![CDATA[
	.st0{fill-rule:evenodd;clip-rule:evenodd;fill:#51B53C;}
	.st1{fill:#FFFFFF;}
	.st2{fill-rule:evenodd;clip-rule:evenodd;fill:#FFFFFF;}
]]></style><g>
<path class="st0" d="M55.71,0c20.85,13.21,39.68,19.47,55.85,17.99c2.82,57.1-18.26,90.82-55.63,104.89 C19.84,109.71-1.5,77.44,0.08,17.12C19.06,18.12,37.67,14.01,55.71,0L55.71,0L55.71,0z"/>
<path class="st2" d="M55.73,7.05c18.45,11.7,35.13,17.23,49.43,15.92c2.5,50.54-16.17,80.39-49.24,92.85 C23.98,104.16,5.09,75.6,6.49,22.21C23.29,23.09,39.77,19.46,55.73,7.05L55.73,7.05L55.73,7.05z"/>
<path class="st0" d="M56.24,19.54c14.22,9.01,27.06,13.27,38.08,12.27c1.92,38.94-12.45,61.93-37.94,71.53 c-0.16-0.06-0.32-0.12-0.48-0.18c-0.16,0.06-0.32,0.12-0.48,0.18c-25.48-9.6-39.86-32.59-37.94-71.53 c11.02,1.01,23.87-3.26,38.08-12.27l0,0l0.33,0.25L56.24,19.54L56.24,19.54L56.24,19.54z"/>
<path class="st1" d="M35.44,58.28l7.47-0.1l0.56,0.14c1.51,0.87,2.93,1.86,4.26,2.99c0.96,0.81,1.87,1.69,2.74,2.65 c2.68-4.31,5.54-8.28,8.56-11.92c3.31-3.99,5.38-6.18,9.06-9.49l0.73-0.28h8.16l-1.65,1.82c-5.05,5.61-8.21,9.99-12.35,15.97 c-4.15,6-7.85,12.18-11.15,18.54l-1.03,1.98l-0.94-2.02c-1.74-3.73-3.82-7.15-6.3-10.21c-2.48-3.06-5.37-5.78-8.74-8.09 L35.44,58.28L35.44,58.28L35.44,58.28L35.44,58.28z"/></g></svg>
 </div>
';

            })
            ->editColumn('SimilarReq', function ($row) {
                return '<div class="d-flex flex-column" > <span class="text-dark fw-bolder fs-6">'.$row->SimilarReq.' </span> </div>';
            })
            ->editColumn('Profiles', function ($row) {
                return '<div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">'.$row->Profiles.'</span> </div>';
            })

            ->editColumn('HiringType', function ($row) {

                $empType = '';
                if (($row->newjobrequest_status == 1 && $row->newjobrequest_balance > 0) && ($row->newclientrequest_status == 1 && $row->newclientrequest_balance > 0)) {
                    $empType = 'Full-time/Contract';
                } elseif ($row->newjobrequest_status == 1 && $row->newjobrequest_balance > 0) {
                    $empType = 'Full-time';
                } elseif ($row->newclientrequest_status == 1 && $row->newclientrequest_balance > 0) {
                    $empType = 'Contract';
                }

                return '<div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">'.$empType.'</span> </div>';
            })
            ->editColumn('Status', function ($row) use ($company_id) {

                // 'staffing_company_relation.from_company_id', 'staffing_company_relation.to_company_id',  'staffing_company_relation.final_status',

                $regStatus = 'UnRegistered';
                $regStatusCss = 'text';
                if (! empty($row->from_company_id)) {
                    if (($row->from_company_id == $company_id) && ($row->to_company_id == $row->id) && ($row->final_status == 1)) {
                        $regStatus = 'Registered';
                        $regStatusCss = 'text-success';
                    }
                }

                return '<div class="d-flex flex-column"> <span class=" '.@$regStatusCss.' fw-bolder fs-6">'.@$regStatus.'</div>';

            })
            ->editColumn('Action', function ($row) use ($jobId) {

                $source_company = auth()->user()->company_id;
                $staffing_company = $row->id;
                $current_job = $jobId;

                $recommendation_relation_cnt = 0;
                $recommendation_relation_cnt = DB::table('recommendation_relation')
                    ->select('recommendation_relation.id')
                    ->where('recommendation_relation.from_company_id', '=', $source_company)
                    ->where('recommendation_relation.to_company_id', '=', $staffing_company)
                    ->where('recommendation_relation.final_status', '=', 0)
                    ->count();

                // echo '<pre>'; print_r($recommendation_relation_cnt); exit;

                if ($recommendation_relation_cnt > 0) {

                    $html = '<div class="d-flex flex-column"> <a href="javascript:void(0);" class="btn btn-sm btn-light-success w-130">Request Sent</a> </div>';

                } else {

                    if ($row->to_company_id == $staffing_company) {     // ###############    			  REGISTER NOT ASSIGN
                        $html = '<div class="d-flex flex-column"> 
					<a id="sp_list_'.$staffing_company.'" href="javascript:void(0);" data-row-jobid="'.$jobId.'" data-row-fromcompany="'.$source_company.'" data-row-tocompany="'.$staffing_company.'" class="btn btn-sm btn-light-primary w-130  w-130 send_only_job_invitation">
						Request Hire
					</a> 
				</div>';
                    } else { // ##########################################    							UNREGISTER
                        $html = '<div class="flex-column">
					<a id="sp_list_'.$staffing_company.'" href="javascript:void(0);" data-row-id="'.$staffing_company.'"  data-row-jobid="'.$jobId.'" class="btn btn-sm btn-light-primary w-130 send_job_recommanded_request">
						Request Hire
					</a> 
				</div>';
                    }

                }

                return $html;

            })

            ->rawColumns(['chkbox', 'Name', 'Match', 'SimilarReq', 'Profiles', 'HiringType', 'Status', 'Action'])

            ->addIndexColumn()
            ->make(true);
    }

    public function boostkeywords_current(Request $request)
    {

        $this->recruiter = [];
        $company_id = auth()->user()->company_id;
        $this->company_id = auth()->user()->company_id;

        $jobId = $request->jobid;
        $this->jobId = $request->jobid;

        // JOB DETAIL
        $currentRowJob = Job::select('employment_type')->where('id', $request->jobid)->first();
        $parent_employment_type = $currentRowJob->employment_type;
        $this->currentRowJob = $currentRowJob;

        // JOB SKILLS
        $currentJobSkill = DB::table('job_metronics_skills')
            ->select('job_metronics_skills.skill_name')
            ->where('job_metronics_skills.job_id', $request->jobid)
            ->get();

        $this->boostkeywordsDataList = DB::table('companies');
        $this->boostkeywordsDataList = $this->boostkeywordsDataList->select(
            'companies.id', 'companies.company_name', 'companies.logo', 'companies.industries',
            'td_industries.ind_name',

            'staffing_company_relation.from_company_id', 'staffing_company_relation.to_company_id', 'staffing_company_relation.final_status',
            'recommendation_relation.from_status as rel_from_status', 'recommendation_relation.to_status as rel_to_status', 'recommendation_relation.to_status as rel_final_status',
            'recommendation_relation.job_id as rel_job_id', 'recommendation_relation.from_company_id as rel_from_company_id', 'recommendation_relation.final_status',

            'package_active.newjobrequest_status', 'package_active.newjobrequest_balance',
            'package_active.newclientrequest_status', 'package_active.newclientrequest_balance',
            'package_active.boostkeywords_status',

            'package_active.boostkeywords_text'
        );
        $this->boostkeywordsDataList = $this->boostkeywordsDataList->leftJoin('td_industries', 'td_industries.ind_id', '=', 'companies.industries');

        if ($parent_employment_type == 0) {				// 0 : Fulltime(In job)      New Job Request :: Fulltime Recruitment Leads  FULLTIME_RECRUITMENT_LEAD(In package_active)
            $this->boostkeywordsDataList = $this->boostkeywordsDataList->join('package_active', function ($join) {
                $join->on('package_active.company_id', '=', 'companies.id')
                    ->on('package_active.newjobrequest_balance', '>', DB::raw('0'))
                    ->on('package_active.newjobrequest_status', '=', DB::raw('1'))
                    ->on('package_active.boostkeywords_status', '=', DB::raw('1'))
                    ->on('package_active.boostkeywords_balance', '>', DB::raw('0'));
            });
        } elseif ($parent_employment_type == 1) {			// 1	: Contract(In job)      New Client Request :: Contract Recruitment Lead   CONTRACT_RECRUITMENT_LEAD(In package_active)
            $this->boostkeywordsDataList = $this->boostkeywordsDataList->join('package_active', function ($join) {
                $join->on('package_active.company_id', '=', 'companies.id')
                    ->on('package_active.newclientrequest_balance', '>', DB::raw('0'))
                    ->on('package_active.newclientrequest_status', '=', DB::raw('1'))
                    ->on('package_active.boostkeywords_status', '=', DB::raw('1'))
                    ->on('package_active.boostkeywords_balance', '>', DB::raw('0'));
            });
        }

        $this->boostkeywordsDataList = $this->boostkeywordsDataList->leftJoin('staffing_company_relation', function ($join) use ($company_id) {
            $join->on('staffing_company_relation.to_company_id', '=', 'companies.id');
            $join->on('staffing_company_relation.from_company_id', '=', DB::raw("'".$company_id."'"));
        });
        $this->boostkeywordsDataList = $this->boostkeywordsDataList->leftJoin('recommendation_relation', function ($join) use ($jobId) {
            $join->on('recommendation_relation.to_company_id', '=', 'companies.id')
                ->on('recommendation_relation.final_status', '!=', DB::raw("'1'"))
                ->on('recommendation_relation.job_id', '=', DB::raw("'".$jobId."'"));
        });

        $this->boostkeywordsDataList = $this->boostkeywordsDataList->leftJoin('jobs', function ($join) use ($company_id) {
            $join->on('jobs.company_id', '=', 'companies.id')
                ->on('jobs.company_id', '!=', DB::raw("'".$company_id."'"));
        });

        $this->boostkeywordsDataList = $this->boostkeywordsDataList->leftJoin('job_applications', 'job_applications.job_id', 'jobs.id');

        $this->boostkeywordsDataList = $this->boostkeywordsDataList->whereIn('companies.company_type', [1, 2]);
        // ################################################################################# ------------- TITLE MATCH
        $findstring = explode(' ', $currentRowJob->title);
        foreach ($findstring as $value) {
            $this->boostkeywordsDataList = $this->boostkeywordsDataList->orWhere('package_active.boostkeywords_text', 'like', "%{$value}%")->whereIn('companies.company_type', [1, 2]);
        }
        if (! empty($currentJobSkill)) {
            foreach ($currentJobSkill as $jskill) {
                $this->boostkeywordsDataList = $this->boostkeywordsDataList->orWhere('package_active.boostkeywords_text', 'like', "%{$jskill->skill_name}%")->whereIn('companies.company_type', [1, 2]);
                // $this->boostkeywordsDataList 	=	$this->boostkeywordsDataList->orWhere('job_metronics_skills.skill_name', 'like', "%{$jskill->skill_name}%")->whereIn('companies.company_type', array(1,2) );
            }
        }

        $this->boostkeywordsDataList = $this->boostkeywordsDataList->groupBy('companies.id');
        $this->boostkeywordsDataList = $this->boostkeywordsDataList->orderBy(DB::raw('RAND()'));
        $this->boostkeywordsDataList = $this->boostkeywordsDataList->take(3);
        $this->boostkeywordsDataList = $this->boostkeywordsDataList->get();

        $view = view('product_company.jobs.boostkeywords', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'view' => $view]);

    }

    public function boostkeywords_old(Request $request)
    {

        $id = $request->jobid;
        $this->currentJobId = $request->jobid;
        $this->recruiter = [];

        $company_id = auth()->user()->company_id;

        $this->company_id = auth()->user()->company_id;

        $this->trusted_logo_path = url('assets/images/trusted.png');

        $jobId = $request->jobid;

        $this->jobId = $request->jobid;
        // JOB DETAIL
        $currentRowJob = Job::select('employment_type')
            ->where('id', $request->jobid)
            ->first();
        $parent_employment_type = $currentRowJob->employment_type;
        $this->currentRowJob = $currentRowJob;

        // JOB SKILLS
        $currentJobSkill = DB::table('job_metronics_skills')
            ->select('job_metronics_skills.skill_name')
            ->where('job_metronics_skills.job_id', $request->jobid)
            ->get();

        // JOB LOCATION QUERY
        $currentJobLocation = DB::table('client_location_jobs')
            ->select('client_location.id', 'client_location.city', 'client_location.city_id', 'client_location.country_id')
            ->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
            ->where('client_location_jobs.job_id', $request->jobid)
            ->get();

        $this->boostkeywordsDataList = DB::table('companies');
        $this->boostkeywordsDataList = $this->boostkeywordsDataList->select(
            'companies.id', 'companies.company_name', 'companies.logo', 'companies.industries',
            'td_industries.ind_name',

            'staffing_company_relation.from_company_id', 'staffing_company_relation.to_company_id', 'staffing_company_relation.final_status',
            'recommendation_relation.from_status as rel_from_status', 'recommendation_relation.to_status as rel_to_status', 'recommendation_relation.to_status as rel_final_status',
            'recommendation_relation.job_id as rel_job_id', 'recommendation_relation.from_company_id as rel_from_company_id', 'recommendation_relation.final_status',

            'package_active.newjobrequest_status', 'package_active.newjobrequest_balance',
            'package_active.newclientrequest_status', 'package_active.newclientrequest_balance',
            'package_active.boostkeywords_status',

            'package_active.boostkeywords_text'

        );
        $this->boostkeywordsDataList = $this->boostkeywordsDataList->leftJoin('td_industries', 'td_industries.ind_id', '=', 'companies.industries');

        if ($parent_employment_type == 0) {				// 0 : Fulltime(In job)      New Job Request :: Fulltime Recruitment Leads  FULLTIME_RECRUITMENT_LEAD(In package_active)
            $this->boostkeywordsDataList = $this->boostkeywordsDataList->join('package_active', function ($join) {
                $join->on('package_active.company_id', '=', 'companies.id')
                    ->on('package_active.newjobrequest_balance', '>', DB::raw('0'))
                    ->on('package_active.newjobrequest_status', '=', DB::raw('1'))
                    ->on('package_active.boostkeywords_status', '=', DB::raw('1'))
                    ->on('package_active.boostkeywords_balance', '>', DB::raw('0'));
            });
        } elseif ($parent_employment_type == 1) {			// 1	: Contract(In job)      New Client Request :: Contract Recruitment Lead   CONTRACT_RECRUITMENT_LEAD(In package_active)
            $this->boostkeywordsDataList = $this->boostkeywordsDataList->join('package_active', function ($join) {
                $join->on('package_active.company_id', '=', 'companies.id')
                    ->on('package_active.newclientrequest_balance', '>', DB::raw('0'))
                    ->on('package_active.newclientrequest_status', '=', DB::raw('1'))
                    ->on('package_active.boostkeywords_status', '=', DB::raw('1'))
                    ->on('package_active.boostkeywords_balance', '>', DB::raw('0'));
            });
        }

        $this->boostkeywordsDataList = $this->boostkeywordsDataList->leftJoin('staffing_company_relation', function ($join) use ($company_id) {
            $join->on('staffing_company_relation.to_company_id', '=', 'companies.id');
            $join->on('staffing_company_relation.from_company_id', '=', DB::raw("'".$company_id."'"));
        });
        $this->boostkeywordsDataList = $this->boostkeywordsDataList->leftJoin('recommendation_relation', function ($join) use ($jobId) {
            $join->on('recommendation_relation.to_company_id', '=', 'companies.id')
                ->on('recommendation_relation.final_status', '!=', DB::raw("'1'"))
                ->on('recommendation_relation.job_id', '=', DB::raw("'".$jobId."'"));
        });

        $this->boostkeywordsDataList = $this->boostkeywordsDataList->leftJoin('jobs', function ($join) use ($company_id) {
            $join->on('jobs.company_id', '=', 'companies.id')
                ->on('jobs.company_id', '!=', DB::raw("'".$company_id."'"));
        });

        $this->boostkeywordsDataList = $this->boostkeywordsDataList->leftJoin('job_applications', 'job_applications.job_id', 'jobs.id');

        $this->boostkeywordsDataList = $this->boostkeywordsDataList->whereIn('companies.company_type', [1, 2]);
        // ################################################################################# ------------- TITLE MATCH
        $findstring = explode(' ', $currentRowJob->title);
        foreach ($findstring as $value) {
            $this->boostkeywordsDataList = $this->boostkeywordsDataList->orWhere('package_active.boostkeywords_text', 'like', "%{$value}%")->whereIn('companies.company_type', [1, 2]);
        }
        if (! empty($currentJobSkill)) {
            foreach ($currentJobSkill as $jskill) {
                $this->boostkeywordsDataList = $this->boostkeywordsDataList->orWhere('package_active.boostkeywords_text', 'like', "%{$jskill->skill_name}%")->whereIn('companies.company_type', [1, 2]);
                // $this->boostkeywordsDataList 	=	$this->boostkeywordsDataList->orWhere('job_metronics_skills.skill_name', 'like', "%{$jskill->skill_name}%")->whereIn('companies.company_type', array(1,2) );
            }
        }

        $this->boostkeywordsDataList = $this->boostkeywordsDataList->groupBy('companies.id');
        $this->boostkeywordsDataList = $this->boostkeywordsDataList->orderBy(DB::raw('RAND()'));
        $this->boostkeywordsDataList = $this->boostkeywordsDataList->take(3);
        $this->boostkeywordsDataList = $this->boostkeywordsDataList->get();

        $view = view('product_company.jobs.boostkeywords', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'view' => $view]);

    }

    public function hmlistingData(Request $request)
    {

        // abort_if(!$this->user->cans('view_jobs'), 403);

        $chkAdminRole = DB::table('roles')->select('roles.id')->where('id', $this->user->role->role_id)->where('name', 'admin')->count();
        $job_status = GeneralMethod::job_status();
        $salaryMode = GeneralMethod::salaryMode();
        $currencySymbole = GeneralMethod::currencySymbole();

        $company_id = auth()->user()->company_id;
        $employmentType = GeneralMethod::employmentType();

        $jobId = $request->jobid;

        // JOB DETAIL
        $currentRowJob = Job::select(
            'title', 'min_exp', 'max_exp', 'min_salary', 'max_salary', 'min_salary_currency', 'max_salary_currency', 'min_sal_mode', 'max_sal_mode',
            'employment_type'
        )
            ->where('id', $request->jobid)
            ->first();
        // JOB SKILLS
        $currentJobSkill = DB::table('job_metronics_skills')
            ->select('job_metronics_skills.skill_name')
            ->where('job_metronics_skills.job_id', $request->jobid)
            ->get();

        // JOB LOCATION QUERY
        $currentJobLocation = DB::table('client_location_jobs')
            ->select('client_location.id', 'client_location.city', 'client_location.city_id', 'client_location.country_id')
            ->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
            ->where('client_location_jobs.job_id', $request->jobid)
            ->get();

        $recommendedDataList = DB::table('users')
            ->select(

                'users.id', 'users.name',
                DB::raw('count(DISTINCT jobs.id) as SimilarReq'),
                DB::raw('count(DISTINCT job_applications.id) as Profiles')
            );
        $recommendedDataList = $recommendedDataList->leftJoin('job_assign', 'job_assign.user_id', 'users.id');
        $recommendedDataList = $recommendedDataList->leftJoin('jobs', 'jobs.id', 'job_assign.job_id');
        // $recommendedDataList 		=	$recommendedDataList->leftJoin('job_assign', 'job_assign.user_id', 'users.id');

        $recommendedDataList = $recommendedDataList->leftJoin('job_applications', function ($join) {
            // $join->on('job_applications.job_id', '=', 'jobs.id');
            $join->on('job_applications.submitted_by', '=', 'users.id');
        });
        $recommendedDataList = $recommendedDataList->leftJoin('job_metronics_skills', 'job_metronics_skills.job_id', '=', 'jobs.id');

        $recommendedDataList = $recommendedDataList->where('users.company_id', '=', auth()->user()->company_id);

        // ################################################################################# ------------- TITLE MATCH
        /*$findstring 					=	explode(' ', $currentRowJob->title);
        foreach ($findstring as $value) { // Title match
            $recommendedDataList 		=	$recommendedDataList->orWhere('jobs.title', 'like', "%{$value}%");
        }
        if(!empty($currentJobSkill)){
            foreach($currentJobSkill as $jskill){
                $recommendedDataList 	=	$recommendedDataList->orWhere('jobs.title', 'like', "%{$jskill->skill_name}%");
                $recommendedDataList 	=	$recommendedDataList->orWhere('job_metronics_skills.skill_name', 'like', "%{$jskill->skill_name}%");

            }
        }*/

        // $recommendedDataList 		=	$recommendedDataList->where('jobs.id', '!=', $request->jobid);
        $recommendedDataList = $recommendedDataList->groupBy('users.id');

        // ->join('jobs', 'jobs.company_id', 'companies.id')
        // ->leftJoin('job_assign', 'job_applications.job_id', 'jobs.id')
        // ->leftJoin('job_applications', 'job_applications.job_id', 'jobs.id')
        // ->leftJoin('job_metronics_skills', 'job_metronics_skills.job_id', '=', 'jobs.id')
        // ->leftJoin('client_location_jobs', 'client_location_jobs.job_id', '=', 'jobs.id')
        // ->leftJoin('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
        // ->leftJoin('td_industries', 'td_industries.ind_id', '=', 'companies.industries');

        // ################################################################################# ------------- TITLE MATCH
        /*$findstring 					=	explode(' ', $currentRowJob->title);
        foreach ($findstring as $value) { // Title match
            $recommendedDataList 		=	$recommendedDataList->orWhere('jobs.title', 'like', "%{$value}%");
        }
        if(!empty($currentJobSkill)){
            foreach($currentJobSkill as $jskill){
                $recommendedDataList 	=	$recommendedDataList->orWhere('jobs.title', 'like', "%{$jskill->skill_name}%");
                $recommendedDataList 	=	$recommendedDataList->orWhere('job_metronics_skills.skill_name', 'like', "%{$jskill->skill_name}%");

            }
        }*/

        // ################################################################################# ------------- EXPERIENCE MATCH
        /*if($currentRowJob->min_exp >=0 && $currentRowJob->max_exp >=0){
                $recommendedDataList 	=	$recommendedDataList->orWhereBetween('jobs.min_exp', [$currentRowJob->min_exp, $currentRowJob->max_exp]);
                $recommendedDataList 	=	$recommendedDataList->orWhereBetween('jobs.max_exp', [$currentRowJob->min_exp, $currentRowJob->max_exp]);
        }*/

        // ################################################################################# ------------ JOB TYPE
        /*if($currentRowJob->employment_type){
            $recommendedDataList 	=	$recommendedDataList->orWhere('jobs.employment_type', $currentRowJob->employment_type);
        }*/

        // ################################################################################# ------------  LOCATION
        /*if(!empty($currentJobLocation)){
            foreach($currentJobLocation as $cloc){
                    if($cloc->country_id == 101){
                        $recommendedDataList 	=	$recommendedDataList->orWhere('client_location.city_id', $cloc->city_id);
                    }else{
                        $recommendedDataList 	=	$recommendedDataList->orWhere('client_location.city', 'like', "%{$cloc->city}%");
                    }
            }
        }*/

        // $recommendedDataList 	=	$recommendedDataList->where('jobs.company_id', '=' , auth()->user()->company_id);
        // $recommendedDataList 	=	$recommendedDataList->where('job_applications.company_id', '=' , auth()->user()->company_id);
        // $recommendedDataList 	=	$recommendedDataList->groupBy('companies.id');
        // $recommendedDataList 	=	$recommendedDataList->toSql();

        // echo $recommendedDataList;
        // exit;

        return DataTables::of($recommendedDataList)

            ->editColumn('chkbox', function ($row) {
                return '<div class="form-check form-check-sm form-check-custom form-check-solid">
                      <input class="form-check-input form-check-input-reco" type="checkbox" value="1" />
                    </div>';
            })

            /*->editColumn('Name', function ($row) {

                     $logo_path				=	url('img/client-default.png');
                    if(!empty(@$row->logo) && strlen(@$row->logo)>10){
                        $logo_path			=  url('public/user-uploads/company-logo/'.@$row->logo);
                    }
                    return '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2  border-radius-5" style="float:left">
                                <a href="javascript:void(0);">
                                      <div class="symbol-label border-radius-5"> <img src="'.$logo_path.'" alt="Company Logo" class="border-radius-5 w-100"> </div>
                                  </a>
                            </div>
                            <div class="d-flex flex-column">
                                <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">'.substr(ucwords(@$row->name), 0, 15).'</a>
                                <a href="javascript:void(0);" class="fw-bold text-muted d-block fs-7 text-hover-primary">'.substr(ucwords(@$row->name), 0, 15).'</a>
                            </div>';
            }) */

            ->editColumn('Name', function ($row) {

                $bgTextDecore = $strVal = '';
                $str = trim(@$row->name);
                $str = $strVal = strtolower($str[0]);
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

                $profile_sidebar_css = '';

                return '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
<a href="javascript:void(0);"   data-row-id="'.@$row->id.'" class="'.$profile_sidebar_css.'">
<div class="symbol-label '.@$bgTextDecore.' border-radius-5">'.strtoupper(@$strVal).'</div>
</a>
</div>                  
<div class="d-flex flex-column">
<a href="javascript:void(0);"  data-row-id="'.@$row->id.'"  class="text-dark fw-bolder text-hover-primary fs-6 '.$profile_sidebar_css.' " data-toggle="tooltip" title="'.ucwords(@$row->name).'">'.ucwords(substr(@$row->name, 0, 10)).'</a>
<a href="javascript:void(0);"  data-row-id="'.@$row->id.'"  class="text-muted fw-bold d-block fs-7 text-hover-primary '.$profile_sidebar_css.' " data-toggle="tooltip" title="'.ucwords(@$row->name).'">'.ucwords(substr(@$row->name, 0, 10)).'</a>
</div>';

            })

            /*->editColumn('Match', function ($row) {
                                return '<div class="form-check form-check-sm form-check-custom form-check-solid">
                                  <input class="form-check-input form-check-input-reco" type="checkbox" value="1" />
                                </div>';
                        })*/
            ->editColumn('Match', function ($row) use ($currentRowJob, $currentJobSkill, $currentJobLocation, $company_id) {

                $percentage = 0;

                $user_id = $row->id;
                $title = $skills = $experience = $salary = $location = $jobtype = 0;

                // ######################################### ------------- SALARY COUNTER
                $recommended_Salary = DB::table('jobs');
                $recommended_Salary = $recommended_Salary->join('job_applications', function ($join) use ($user_id) {
                    $join->on('job_applications.job_id', '=', 'jobs.id');
                    $join->on('job_applications.submitted_by', '=', DB::raw("'".$user_id."'"));
                });
                $recommended_Salary = $recommended_Salary->join('job_assign', function ($join) use ($user_id) {
                    $join->on('job_assign.job_id', '=', 'jobs.id');
                    $join->on('job_assign.user_id', '=', DB::raw("'".$user_id."'"));
                });
                $recommended_Salary = $recommended_Salary->where('jobs.min_salary_currency', $currentRowJob->min_salary_currency);
                $recommended_Salary = $recommended_Salary->where('jobs.max_salary_currency', $currentRowJob->max_salary_currency);
                $recommended_Salary = $recommended_Salary->where('jobs.min_sal_mode', $currentRowJob->min_sal_mode);
                $recommended_Salary = $recommended_Salary->where('jobs.max_sal_mode', $currentRowJob->max_sal_mode);
                $recommended_Salary = $recommended_Salary->whereBetween('jobs.min_salary', [$currentRowJob->min_exp, $currentRowJob->max_exp]);
                $recommended_Salary = $recommended_Salary->whereBetween('jobs.max_salary', [$currentRowJob->min_exp, $currentRowJob->max_exp]);
                $recommended_Salary = $recommended_Salary->where('jobs.company_id', $company_id);

                $recommended_Salary = $recommended_Salary->count();
                if ($recommended_Salary > 0) {
                    $salary = 15;
                }

                // ######################################### ------------- TITLE COUNTER
                $recommended_Title = DB::table('jobs');
                $recommended_Title = $recommended_Title->join('job_assign', function ($join) use ($user_id) {
                    $join->on('job_assign.job_id', '=', 'jobs.id');
                    $join->on('job_assign.user_id', '=', DB::raw("'".$user_id."'"));
                });

                $findstring = explode(' ', $currentRowJob->title);
                foreach ($findstring as $value) { // Title match
                    if ($value != '') {
                        $recommended_Title = $recommended_Title->orWhere('jobs.title', 'like', "%{$value}%");
                    }
                }
                $recommended_Title = $recommended_Title->where('jobs.company_id', $company_id);
                $recommended_Title = $recommended_Title->count();
                if ($recommended_Title > 0) {
                    $title = 30;
                }

                // ######################################### ------------- JOBTYPE
                $recommended_Jobtype = DB::table('jobs');
                $recommended_Jobtype = $recommended_Jobtype->join('job_assign', function ($join) use ($user_id) {
                    $join->on('job_assign.job_id', '=', 'jobs.id');
                    $join->on('job_assign.user_id', '=', DB::raw("'".$user_id."'"));
                });
                $recommended_Jobtype = $recommended_Jobtype->where('jobs.company_id', $company_id);
                $recommended_Jobtype = $recommended_Jobtype->Where('jobs.employment_type', $currentRowJob->employment_type);
                $recommended_Jobtype = $recommended_Jobtype->count();
                if ($recommended_Jobtype > 0) {
                    $jobtype = 10;
                }

                // ######################################### ------------- EXPERIENCE MATCH
                $recommended_Experience = DB::table('jobs');
                $recommended_Experience = $recommended_Experience->join('job_assign', function ($join) use ($user_id) {
                    $join->on('job_assign.job_id', '=', 'jobs.id');
                    $join->on('job_assign.user_id', '=', DB::raw("'".$user_id."'"));
                });
                $recommended_Experience = $recommended_Experience->where('jobs.company_id', $company_id);
                if ($currentRowJob->min_exp >= 0 && $currentRowJob->max_exp >= 0) {
                    $recommended_Experience = $recommended_Experience->Where('jobs.min_exp', [$currentRowJob->min_exp, $currentRowJob->max_exp]);
                    $recommended_Experience = $recommended_Experience->Where('jobs.max_exp', [$currentRowJob->min_exp, $currentRowJob->max_exp]);
                }
                $recommended_Experience = $recommended_Experience->count();
                if ($recommended_Experience > 0) {
                    $experience = 15;
                }

                // SKILL COUNTER

                $recommended_Skill = DB::table('companies')
                    ->select('companies.id')
                    ->join('jobs', 'jobs.company_id', 'companies.id')
                    ->join('job_applications', 'job_applications.job_id', 'jobs.id')
                    ->join('job_assign', function ($join) use ($user_id) {
                        $join->on('job_assign.job_id', '=', 'jobs.id');
                        $join->on('job_assign.user_id', '=', DB::raw("'".$user_id."'"));
                    })
                    ->leftJoin('job_metronics_skills', 'job_metronics_skills.job_id', '=', 'jobs.id')
                    ->leftJoin('client_location_jobs', 'client_location_jobs.job_id', '=', 'jobs.id')
                    ->leftJoin('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id');
                if (! empty($currentJobSkill)) {
                    foreach ($currentJobSkill as $jskill) {
                        $recommended_Skill = $recommended_Skill->orWhere('jobs.title', 'like', "%{$jskill->skill_name}%");
                        $recommended_Skill = $recommended_Skill->orWhere('job_metronics_skills.skill_name', 'like', "%{$jskill->skill_name}%");

                    }
                }
                $recommended_Skill = $recommended_Skill->where('jobs.company_id', $company_id);
                $recommended_Skill = $recommended_Skill->count();
                if ($recommended_Skill > 0) {
                    $skills = 20;
                }

                $skillname = '';
                $scnt = 0;
                $recommended_Skill_Name = DB::table('companies')
                    ->select('job_metronics_skills.skill_name')
                    ->join('jobs', 'jobs.company_id', 'companies.id')
                    ->join('job_applications', 'job_applications.job_id', 'jobs.id')
                    ->join('job_assign', function ($join) use ($user_id) {
                        $join->on('job_assign.job_id', '=', 'jobs.id');
                        $join->on('job_assign.user_id', '=', DB::raw("'".$user_id."'"));
                    })
                    ->leftJoin('job_metronics_skills', 'job_metronics_skills.job_id', '=', 'jobs.id');
                if (! empty($currentJobSkill)) {
                    foreach ($currentJobSkill as $jskill) {
                        $recommended_Skill_Name = $recommended_Skill_Name->orWhere('job_metronics_skills.skill_name', $jskill->skill_name);

                    }
                }
                $recommended_Skill_Name = $recommended_Skill_Name->where('jobs.company_id', $company_id);
                $recommended_Skill_Name = $recommended_Skill_Name->get();

                $tempArr = [];
                if (! empty($recommended_Skill_Name)) {
                    foreach ($recommended_Skill_Name as $sname) {
                        $tempArr[] = $sname->skill_name;
                        $scnt++;

                    }
                }
                if (! empty($tempArr)) {
                    $newArr = array_keys(array_flip($tempArr));
                    // $$tempArr	=	array_unique($tempArr);
                    $skillname = implode(',', $newArr);
                }

                // ################################################################################# ------------  LOCATION
                $recommended_Location = DB::table('companies')
                    ->select('client_location.id')
                    ->join('jobs', 'jobs.company_id', 'companies.id')
                    ->join('job_assign', function ($join) use ($user_id) {
                        $join->on('job_assign.job_id', '=', 'jobs.id');
                        $join->on('job_assign.user_id', '=', DB::raw("'".$user_id."'"));
                    })
                    ->join('job_applications', 'job_applications.job_id', 'jobs.id')
                    ->leftJoin('job_metronics_skills', 'job_metronics_skills.job_id', '=', 'jobs.id')
                    ->leftJoin('client_location_jobs', 'client_location_jobs.job_id', '=', 'jobs.id')
                    ->leftJoin('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id');
                if (! empty($currentJobLocation)) {
                    foreach ($currentJobLocation as $cloc) {
                        if ($cloc->country_id == 101) {
                            $recommended_Location = $recommended_Location->orWhere('client_location.city_id', $cloc->city_id);
                        } else {
                            $recommended_Location = $recommended_Location->orWhere('client_location.city', 'like', "%{$cloc->city}%");
                        }
                    }
                }

                $recommended_Location = $recommended_Location->where('jobs.company_id', $company_id);
                $recommended_Location = $recommended_Location->count();
                if ($recommended_Location > 0) {
                    $location = 10;
                }

                $percentage = $salary + $title + $jobtype + $experience + $skills + $location;

                $percentage_html = '';
                $percentage_html = '
<div class="progress-circle p'.$percentage.'">
  <span>'.$percentage.'%</span>
  <div class="left-half-clipper">
    <div class="first50-bar"></div>
    <div class="value-bar"></div>
  </div>
</div>	';

                return '<div class="d-flex flex-column w-100 me-2"> 
		  '.$percentage_html.'
		  <span class="text-muted fw-bold d-block fs-7">'.substr(@$skillname, 0, 20).'</span> </div>';

            })

            /*->editColumn('SimilarReq', function ($row) {
                    return '<div class="form-check form-check-sm form-check-custom form-check-solid">
                      <input class="form-check-input form-check-input-reco" type="checkbox" value="1" />
                    </div>';
            }) */
            ->editColumn('SimilarReq', function ($row) {
                return '<div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">'.$row->SimilarReq.' </span> </div>';
            })
            /*->editColumn('Profiles', function ($row) {
                    return '<div class="form-check form-check-sm form-check-custom form-check-solid">
                      <input class="form-check-input form-check-input-reco" type="checkbox" value="1" />
                    </div>';
            }) */

            ->editColumn('Profiles', function ($row) {
                return '<div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">'.$row->Profiles.'</span> </div>';
            })

            ->editColumn('Action', function ($row) use ($jobId) {

                $html = '';
                $userId = $row->id;
                $assignCounter = DB::table('job_assign')->select('job_assign.id')->where('job_assign.user_id', '=', $userId)->where('job_assign.job_id', '=', $jobId)->count();

                if ($assignCounter > 0) {
                    $html = '<div class="flex-column">
											<a href="javascript:void(0);" data-row-userid="'.$row->id.'"  data-row-jobid="'.$jobId.'" class="btn btn-sm btn-light-primary w-130">
												Added
											</a> 
										</div>
										';
                } else {
                    $html = '<div class="flex-column"> 
											<a href="javascript:void(0);" data-row-userid="'.$row->id.'" data-row-jobid="'.$jobId.'" class="btn btn-sm btn-light-success w-130 team_job_assign_request">
												Add 
											</a> 
										</div>';
                }

                return $html;
            })

            ->rawColumns(['chkbox', 'Name', 'Match', 'SimilarReq', 'Profiles', 'Action'])

            ->addIndexColumn()
            ->make(true);

    }

    public function teamJobAssignRequest(Request $request)
    {

        $company_id = $tocompany_id = $jobid = '';
        $company_id = auth()->user()->company_id;

        $userId = $request->id;   		//	$request->id;
        $jobid = $request->jobid;		//	$request->jobid;

        $cnt = '';
        $cnt = DB::table('job_assign')->select('id')->where('company_id', '=', $company_id)->where('user_id', '=', $userId)->where('job_id', '=', $jobid)->count();

        if ($cnt == 0) {
            $values = ['company_id' => $company_id, 'user_id' => $userId, 'job_id' => $jobid];
            DB::table('job_assign')->insert($values);

            return Reply::dataOnly(['status' => 'success']);
        }

    }

    public function remove_job(Request $request)
    {

        $jobid = $fromcompany = $tocompany = '';

        $from_company_id = auth()->user()->company_id;
        $from_user_id = $this->user->id;

        $jobid = $request->jobid;
        $fromcompany = $request->fromcompany;
        $tocompany = $request->tocompany;

        $fromCounter = DB::table('recommendation_relation')->select('*')
            ->where('from_company_id', $fromcompany)
            ->where('to_company_id', $tocompany)
            ->where('job_id', $jobid)
            ->count();

        if (($fromCounter == 0)) {
            // INSERT
            $insertArr = [
                'from_company_id' => $fromcompany,
                'from_user_id' => $from_user_id,
                'from_status' => 1,
                'from_created_at' => date('Y-m-d'),

                'to_company_id' => $tocompany,
                'to_status' => 0,
                'final_status' => 0,
                'job_id' => $jobid,

            ];
            DB::table('recommendation_relation')->insert($insertArr);
            $msg = 'Recommended request sent successfully !';

            return Reply::dataOnly(['status' => 'success', 'message' => $msg]);

        } else {
            $msg = 'Recommended request already sent !';

            return Reply::dataOnly(['status' => 'warning', 'message' => $msg]);
        }

    }

    public function assignRejected(Request $request)
    {

        $company_id = $tocompany_id = $jobid = '';
        $company_id = auth()->user()->company_id;

        $tocompany_id = $request->id;   // $request->id;
        $jobid = $request->jobid;	// $request->jobid;

        echo '<pre>';
        print_r($request->jobid);
        exit;

        if (! empty($company_id) && ! empty($tocompany_id) && ! empty($jobid)) {

            $data = [];
            $data['final_status'] = -1;

            DB::table('recommendation_relation')
                ->where('from_company_id', @$company_id)
                ->where('to_company_id', @$tocompany_id)
                ->where('job_id', @$jobid)
                ->where('final_status', 1)
                ->limit(1)
                ->update($data);
        }

        return Reply::dataOnly(['status' => 'success']);

    }

    public function removeJob(Request $request)
    {

        $company_id = $jobid = $fromcompany = $tocompany = '';

        $company_id = auth()->user()->company_id;
        $jobid = $request->jobid;
        $fromcompany = $request->fromcompany;
        $tocompany = $request->tocompany;

        if (! empty($jobid) && ! empty($company_id) && ! empty($tocompany)) {

            $data = [];
            $data['final_status'] = 2;
            $data['from_status'] = 2;

            DB::table('recommendation_relation')
                ->where('from_company_id', @$fromcompany)
                ->where('to_company_id', @$tocompany)
                ->where('job_id', @$jobid)
                ->where('final_status', 1)
                ->limit(1)
                ->update($data);
        }

        return Reply::dataOnly(['status' => 'success']);

    }

    public function resend_job_recommanded_request($id)
    {

        $from_company_id = auth()->user()->company_id;
        $from_user_id = $this->user->id;

        $to_company_id = $_REQUEST['to_company_id'];
        $current_jobid = $_REQUEST['current_jobid'];
        $ename = $_REQUEST['ename'];

        $recommendation_relation_row = DB::table('recommendation_relation')
            ->select('clients.id')
            ->where('recommendation_relation.from_company_id', '=', $from_company_id)
            ->where('recommendation_relation.to_company_id', '=', $to_company_id)
            ->where('recommendation_relation.job_id', '=', $current_jobid)
                                            // ->where('recommendation_relation.from_status', '=', 2)
                                            // ->where('recommendation_relation.final_status', '=', 2)
            ->count();

        if ($recommendation_relation_row > 0) {

            $recommendation_data = DB::table('recommendation_relation')
                ->select(
                    'recommendation_relation.id', 'recommendation_relation.from_company_id', 'recommendation_relation.from_user_id', 'recommendation_relation.from_status', 'recommendation_relation.to_company_id',
                    'recommendation_relation.job_id', 'recommendation_relation.to_status', 'recommendation_relation.final_status'

                )
                ->where('recommendation_relation.from_company_id', '=', $from_company_id)
                ->where('recommendation_relation.to_company_id', '=', $to_company_id)
                ->where('recommendation_relation.job_id', '=', $current_jobid)
                ->first();

            if (($recommendation_data->from_status == 2) && ($recommendation_data->final_status == 2)) {
                $updateArr['from_status'] = 1;
                $updateArr['to_status'] = 0;
                $updateArr['final_status'] = 0;

                DB::table('recommendation_relation')
                    ->where('recommendation_relation.from_company_id', '=', $from_company_id)
                    ->where('recommendation_relation.to_company_id', '=', $to_company_id)
                    ->where('recommendation_relation.job_id', '=', $current_jobid)
                    ->where('recommendation_relation.from_status', '=', 2)
                    ->where('recommendation_relation.final_status', '=', 2)
                    ->limit(1)
                    ->update($updateArr);

                return Reply::dataOnly(['status' => 'success']);
            }

            if (($recommendation_data->to_status == -1) && ($recommendation_data->final_status == -1)) {
                $updateArr['from_status'] = 1;
                $updateArr['to_status'] = 0;
                $updateArr['final_status'] = 0;

                DB::table('recommendation_relation')
                    ->where('recommendation_relation.from_company_id', '=', $from_company_id)
                    ->where('recommendation_relation.to_company_id', '=', $to_company_id)
                    ->where('recommendation_relation.job_id', '=', $current_jobid)
                    ->where('recommendation_relation.to_status', '=', -1)
                    ->where('recommendation_relation.final_status', '=', -1)
                    ->limit(1)
                    ->update($updateArr);

                return Reply::dataOnly(['status' => 'success']);
            }

        } else {
            return Reply::dataOnly(['status' => 'warning']);
        }

    }

    public function add_to_list(Request $request)
    {

        $jobId = $request->jobId;
        $recomndedId = $request->recomndedId;
        $condidateId = $request->condidateId;
        $jobs = JobApplication::findOrFail($condidateId);
        // $query = DB::table('job_applications')->where('id',$condidateId)->update(['job_id'=>$jobId]);
        $company_id = auth()->user()->company_id;
        $user_id = auth()->user()->id;

        $insertArr = ['job_id' => $jobId, 'job_application_id' => $condidateId, 'rec_candidate_assign_id' => $recomndedId, 'status_id' => 2, 'master_status_id' => 1, 'child_status_id' => 0, 'company_id' => $company_id, 'submitted_by' => $user_id];

        $query_update = DB::table('add_job_wise_condidates')->insert($insertArr);

        if ($query_update) {

            $res = [
                'status' => true,
            ];
        } else {
            $res = [
                'status' => false,
            ];

        }

        return $res;

    }
}
