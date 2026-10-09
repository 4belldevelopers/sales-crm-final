<?php

namespace App\Http\Controllers\ServiceCompany;

use App\Helper\Files;
use App\Helper\GeneralMethod;
use App\Helper\Reply;
use App\Http\Requests\InterviewSchedule\StoreRequest;
use App\Http\Requests\UpdateJobApplication;
use App\Models\ApplicationStatus;
use App\Models\Client;
use App\Models\InterviewSchedule;
use App\Models\InterviewScheduleEmployee;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\JobApplicationAnswer;
use App\Models\JobLocation;
use App\Models\JobQuestion;
use App\Models\NoticePeriod;
use App\Models\Skill;
use App\Models\User;
use App\Notifications\CandidateScheduleInterview;
use App\Notifications\ScheduleInterview;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;
use Maatwebsite\Excel\Facades\Excel;
use Mail;
use Yajra\DataTables\Facades\DataTables;

class ServiceCompanyJobTrackerController extends ServiceCompanyBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = __('CV / Resume Received');
        $this->pageIcon = 'icon-user';

    }

    public function index($id)
    {
        abort_if(Gate::denies('view_job_applications'), 403);

        if ($id > 0) {
            $this->boardColumns = ApplicationStatus::all();
            $this->locations = JobLocation::all();
            // $this->jobs 			= Job::all();
            $this->skills = Skill::all();
            $this->clients = Client::all();
            $this->users = User::company()->get();
            $this->noticeperiod = NoticePeriod::all();

            $this->job = Job::withoutGlobalScopes()->findOrFail($id);

            $this->skillData = DB::table('job_skills')
                ->join('skills', 'job_skills.skill_id', '=', 'skills.id')
                ->select('skills.id', 'skills.name')
                ->where('job_skills.job_id', '=', $id)
                ->orderBy('skills.name', 'ASC')
                ->get();

            if ($this->job->client_id > 0) {
                $this->jobclient = Client::withoutGlobalScopes()->findOrFail($this->job->client_id);

                $contactHtml = '';
                $this->client_loc_arr = DB::table('client_location_jobs')
                    ->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
                    ->join('countries', 'countries.id', '=', 'client_location.country_id')
                    ->select('client_location.address', 'client_location.city', 'client_location.postcode', 'countries.country_name')
                                            // ->where('jobs.status', 'active')
                    ->where('client_location_jobs.job_id', $id)
                    ->get();

            }

        } else {
            $this->boardColumns = ApplicationStatus::all();
            $this->locations = JobLocation::all();
            $this->jobs = Job::all();
            $this->skills = Skill::all();
            $this->clients = Client::all();
            $this->users = User::company()->get();
            $this->noticeperiod = NoticePeriod::all();
            // echo '<pre>'; print_r($this->users ); exit;

        }

        return view('service_company.jobtracker.index', $this->data);
    }

    public function addclass(Request $request)
    {
        abort_if(! $this->user->can('add_schedule'), 403);

        $id = $request->id;
        $this->job = Job::withoutGlobalScopes()->findOrFail($id);

        return view('service_company.jobtracker.addclass', $this->data)->render();
    }

    public function applicaationByJobStatus($jobid, $status)
    {
        abort_if(Gate::denies('view_job_applications'), 403);

        if ($jobid > 0 && $status > 0) {

            $this->applicationStatus = $status;

            $this->boardColumns = ApplicationStatus::all();
            $this->locations = JobLocation::all();
            // $this->jobs 				= Job::all();
            $this->skills = Skill::all();
            $this->clients = Client::all();
            $this->users = User::company()->get();
            $this->noticeperiod = NoticePeriod::all();

            $this->job = Job::withoutGlobalScopes()->findOrFail($jobid);

            $this->skillData = DB::table('job_skills')
                ->join('skills', 'job_skills.skill_id', '=', 'skills.id')
                ->select('skills.id', 'skills.name')
                ->where('job_skills.job_id', '=', $jobid)
                ->orderBy('skills.name', 'ASC')
                ->get();

        } else {
            $this->boardColumns = ApplicationStatus::all();
            $this->locations = JobLocation::all();
            $this->jobs = Job::all();
            $this->skills = Skill::all();
            $this->clients = Client::all();
            $this->users = User::company()->get();
            $this->noticeperiod = NoticePeriod::all();
            // echo '<pre>'; print_r($this->users ); exit;

        }

        // return view('service_company.jobtracker.index', $this->data);
        return view('service_company.jobtracker.applicaationByJobStatus', $this->data);

    }

    public function index2(Request $request)
    {
        abort_if(Gate::denies('view_job_applications'), 403);
        $date = Carbon::now();
        $startDate = $date->subDays(30)->format('Y-m-d');
        $endDate = Carbon::now()->format('Y-m-d');
        $this->locations = JobLocation::all();
        $this->jobs = Job::all();

        $this->boardColumns = ApplicationStatus::with(['applications' => function ($q) use ($startDate, $endDate, $request) {

            if ($request->startDate !== null && $request->startDate != 'null' && $request->startDate != '') {
                $q = $q->where(DB::raw('DATE(job_applications.`created_at`)'), '>=', $request->startDate);
            } else {
                $q = $q->where(DB::raw('DATE(job_applications.`created_at`)'), '>=', $startDate);
            }

            if ($request->endDate !== null && $request->endDate != 'null' && $request->endDate != '') {
                $q = $q->where(DB::raw('DATE(job_applications.`created_at`)'), '<=', $request->endDate);
            } else {
                $q = $q->where(DB::raw('DATE(job_applications.`created_at`)'), '<=', $endDate);
            }

            // Filter By jobs
            if ($request->jobs != 'all' && $request->jobs != '') {
                $q = $q->where('job_applications.job_id', $request->jobs);
            }

        }, 'applications.schedule'])->get();

        $boardStracture = [];
        foreach ($this->boardColumns as $key => $column) {
            $boardStracture[$column->id] = [];
            foreach ($column->applications as $application) {
                $boardStracture[$column->id][] = $application->id;
            }
        }
        $this->boardStracture = json_encode($boardStracture);
        $this->currentDate = Carbon::now()->timestamp;

        $this->startDate = $startDate;
        $this->endDate = $endDate;

        if ($request->ajax()) {
            $view = view('service_company.cvtracker.board-data', $this->data)->render();

            return Reply::dataOnly(['view' => $view]);
        }

        return view('service_company.cvtracker.board', $this->data);
    }

    public function create()
    {
        abort_if(Gate::denies('add_job_applications'), 403);

        $candidateCount = JobApplication::count();
        if ($candidateCount > 0 && $this->activePackage->package->no_of_candidate_access !== null && $this->activePackage->package->no_of_candidate_access <= $candidateCount) {
            $this->message = __('modules.subscription.candidatePurchase');

            return view('service_company.subscription.ask_purchase', $this->data);
        }
        $this->jobs = Job::activeJobs();

        return view('service_company.cvtracker.create', $this->data);
    }

    public function jobQuestion($jobID)
    {
        $this->jobQuestion = JobQuestion::with(['question'])->where('job_id', $jobID)->get();
        $view = view('service_company.cvtracker.job-question', $this->data)->render();
        $count = count($this->jobQuestion);

        return Reply::dataOnly(['status' => 'success', 'view' => $view, 'count' => $count]);
    }

    public function edit($id)
    {
        abort_if(Gate::denies('edit_job_applications'), 403);

        $this->statuses = ApplicationStatus::all();
        $this->application = JobApplication::withoutGlobalScopes()->find($id);
        $this->jobQuestion = JobQuestion::with(['question'])
            ->where('job_id', $this->application->job_id)->get();

        return view('service_company.cvtracker.edit', $this->data);
    }

    public function data(Request $request)
    {

        abort_if(Gate::denies('view_job_applications'), 403);

        $chkAdminRole = DB::table('roles')
            ->select('*')
            ->where('id', $this->user->role->role_id)
            ->where('name', 'admin')
            ->count();

        $jobApplications = JobApplication::select('job_applications.*', 'jobs.title', 'job_locations.location', 'application_status.status as app_status',
            'clients.name as client_name',
            'users.name as posted_by',
            'notice_periods.np_title', 'qualification_master.name as qualification_name', 'master_status.name')
            ->join('jobs', 'jobs.id', 'job_applications.job_id')

            ->leftjoin('master_status', 'master_status.id', 'job_applications.master_status_id')

            ->leftjoin('job_skills', 'jobs.id', 'job_skills.job_id')
            ->leftjoin('job_locations', 'job_locations.id', 'jobs.location_id')

            ->leftjoin('clients', 'clients.id', 'jobs.client_id')

            ->leftjoin('users', 'users.id', 'job_applications.submitted_by')
            ->leftjoin('notice_periods', 'notice_periods.np_id', 'job_applications.np_id')

            ->leftjoin('qualification_master', 'qualification_master.id', 'job_applications.qualification')

            ->leftjoin('application_status', 'application_status.id', 'job_applications.status_id');

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
            $jobApplications = $jobApplications->whereIn('job_skills.skill_id', explode(',', $request->skill));
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

        // CUSTOM FILTER

        // FULLNAME
        if ($request->applicant_search != 'all' && $request->applicant_search != '') {
            $jobApplications = $jobApplications->where('job_applications.full_name', 'like', '%'.$request->applicant_search.'%');
        }
        // Client
        if ($request->clients != 'all' && $request->clients != '') {
            $jobApplications = $jobApplications->where('jobs.client_id', $request->clients);
        }
        // Email
        if ($request->email_search != 'all' && $request->email_search != '') {
            $jobApplications = $jobApplications->where('job_applications.email', 'like', '%'.$request->email_search.'%');
        }
        // SUBMITTED
        if ($request->source != 'all' && $request->source != '') {
            $jobApplications = $jobApplications->where('job_applications.submitted_by', $request->source);
        }
        // PHONE
        if ($request->phone_search != '') {

            $jobApplications = $jobApplications->where('job_applications.phone', 'like', '%'.$request->phone_search.'%');
        }
        // NOTICE PERIOD
        if ($request->search_passport != 'all' && $request->search_passport != '') {
            $jobApplications = $jobApplications->where('job_applications.passport', $request->search_passport);
        }
        // NOTICE PERIOD
        if ($request->search_np_id != '' && $request->search_np_id > 0) {
            $jobApplications = $jobApplications->where('job_applications.np_id', $request->search_np_id);
        }
        // CURRENT DESIGNATION
        if ($request->sea_cur_designation != '') {
            $jobApplications = $jobApplications->where('job_applications.current_designation', 'like', '%'.$request->sea_cur_designation.'%');
        }
        // CURRENT COMPANY
        if ($request->sea_cur_company != '') {
            $jobApplications = $jobApplications->where('job_applications.current_company', 'like', '%'.$request->sea_cur_company.'%');
        }
        // CURRENT LOCATION
        if ($request->sea_cur_location != '') {
            $jobApplications = $jobApplications->where('job_applications.current_location', 'like', '%'.$request->sea_cur_location.'%');
        }
        if ($request->jobid != '') {
            $jobApplications = $jobApplications->where('job_applications.job_id', $request->jobid);
        }

        $jobApplications = $jobApplications->groupBy('job_applications.id');
        $dataTable = DataTables::of($jobApplications)

            ->editColumn('title', function ($row) {
                return ucfirst($row->title);
            })
            ->addColumn('action', function ($row) use ($chkAdminRole) {

                $action = '';
                if ($chkAdminRole > 0) {
                    if ($this->user->cans('edit_job_applications')) {
                        $action .= '<a href="'.route('service_company.job-applications.edit', [$row->id]).'" class="btn"
						data-toggle="tooltip" data-original-title="'.__('app.edit').'"><i class="fa fa-edit text-primary" aria-hidden="true"></i></a>';
                    }

                    if ($this->user->cans('delete_job_applications')) {
                        $action .= ' <a href="javascript:;" class="btn sa-params"
						data-toggle="tooltip" data-row-id="'.$row->id.'" data-original-title="'.__('app.delete').'"><i class="fa fa-times text-danger" aria-hidden="true"></i></a>';
                    }
                } else {
                    if ($row->submitted_by == $this->user->id) {
                        if ($this->user->cans('edit_job_applications')) {
                            $action .= '<a href="'.route('service_company.cvtracker.edit', [$row->id]).'" class="btn"
							data-toggle="tooltip" data-original-title="'.__('app.edit').'"><i class="fa fa-edit text-primary" aria-hidden="true"></i></a>';
                        }

                        if ($this->user->cans('delete_job_applications')) {
                            $action .= ' <a href="javascript:;" class="btn btn-danger btn-circle sa-params"
							data-toggle="tooltip" data-row-id="'.$row->id.'" data-original-title="'.__('app.delete').'"><i class="fa fa-times text-danger" aria-hidden="true"></i></a>';
                        }
                    } else {
                        $action = 'N/A';
                    }

                }

                return $action;
            })
            ->editColumn('created_at', function ($row) {

                $created_at = '';
                $created_at = GeneralMethod::ymdTodmy($row->created_at);

                return '<div class="text-center"><a href="#" data-toggle="tooltip" title="'.$created_at.'"><i class="fa fa-calendar fa-sm text-custom"></i></a></div>';
            })
            ->editColumn('full_name', function ($row) use ($chkAdminRole) {
                $html = '';
                $html = '<input class="userinfo" type="text" name="" id="" value="'.ucwords($row->full_name).'" data-jobid="'.$row->job_id.'" data-candidate="'.$row->id.'" data-schematype="noskill" data-schemaname="full_name"  />';

                if ($chkAdminRole > 0) {
                    $html = '<input class="userinfo" type="text" name="" id="" value="'.ucwords($row->full_name).'" data-jobid="'.$row->job_id.'" data-candidate="'.$row->id.'" data-schematype="noskill" data-schemaname="full_name"  />';
                } else {
                    if ($row->submitted_by == $this->user->id) {
                        $html = '<input class="userinfo" type="text" name="" id="" value="'.ucwords($row->full_name).'" data-jobid="'.$row->job_id.'" data-candidate="'.$row->id.'" data-schematype="noskill" data-schemaname="full_name"  />';
                    } else {
                        $html = ucwords($row->full_name);
                    }
                }

                return $html;
            })
            ->editColumn('client_name', function ($row) {
                return ucfirst(substr($row->client_name, 0, 10));
            })
            ->editColumn('posted_by', function ($row) {
                $source = $row->posted_by;
                if ($row->submitted_by == 0) {
                    $source = 'Career Website';
                }

                return ucfirst(substr($source, 0, 10));
            })
            ->editColumn('email', function ($row) use ($chkAdminRole) {
                $html = '';
                if ($chkAdminRole > 0) {
                    $html = '<input class="userinfo" type="text" name="" id="" value="'.$row->email.'" data-jobid="'.$row->job_id.'" data-candidate="'.$row->id.'" data-schematype="noskill" data-schemaname="email" />';
                } else {
                    if ($row->submitted_by == $this->user->id) {
                        $html = '<input class="userinfo" type="text" name="" id="" value="'.$row->email.'" data-jobid="'.$row->job_id.'" data-candidate="'.$row->id.'" data-schematype="noskill" data-schemaname="email" />';
                    } else {
                        $html = $row->email;
                    }
                }

                return $html;
            })
            ->editColumn('phone', function ($row) use ($chkAdminRole) {
                $html = '';
                if ($chkAdminRole > 0) {
                    $html = '<input class="userinfo" type="text" name="" id="" value="'.$row->phone.'" data-jobid="'.$row->job_id.'" data-candidate="'.$row->id.'" data-schematype="noskill" data-schemaname="phone" />';
                } else {
                    if ($row->submitted_by == $this->user->id) {
                        $html = '<input class="userinfo" type="text" name="" id="" value="'.$row->phone.'" data-jobid="'.$row->job_id.'" data-candidate="'.$row->id.'" data-schematype="noskill" data-schemaname="phone" />';
                    } else {
                        $html = $row->phone;
                    }
                }

                return $html;
            })
            ->editColumn('ctc', function ($row) use ($chkAdminRole) {
                $html = '';
                if ($chkAdminRole > 0) {
                    $html = '<input class="userinfo" type="text" name="" id="" value="'.$row->ctc.'" data-jobid="'.$row->job_id.'" data-candidate="'.$row->id.'" data-schematype="noskill" data-schemaname="ctc" />';
                } else {
                    if ($row->submitted_by == $this->user->id) {
                        $html = '<input class="userinfo" type="text" name="" id="" value="'.$row->ctc.'" data-jobid="'.$row->job_id.'" data-candidate="'.$row->id.'" data-schematype="noskill" data-schemaname="ctc" />';
                    } else {
                        $html = $row->ctc;
                    }
                }

                return $html;
            })
            ->editColumn('ectc', function ($row) use ($chkAdminRole) {
                $html = '';
                if ($chkAdminRole > 0) {
                    $html = '<input class="userinfo" type="text" name="" id="" value="'.$row->ectc.'" data-jobid="'.$row->job_id.'" data-candidate="'.$row->id.'" data-schematype="noskill" data-schemaname="ectc" />';
                } else {
                    if ($row->submitted_by == $this->user->id) {
                        $html = '<input class="userinfo" type="text" name="" id="" value="'.$row->ectc.'" data-jobid="'.$row->job_id.'" data-candidate="'.$row->id.'" data-schematype="noskill" data-schemaname="ectc" />';
                    } else {
                        $html = $row->ctc;
                    }
                }

                return $html;
            })
            ->editColumn('total_exp', function ($row) use ($chkAdminRole) {
                $html = '';
                if ($chkAdminRole > 0) {
                    $html = '<input class="userinfo" type="text" name="" id="" value="'.$row->total_exp.'" data-jobid="'.$row->job_id.'" data-candidate="'.$row->id.'" data-schematype="noskill" data-schemaname="total_exp" />';
                } else {
                    if ($row->submitted_by == $this->user->id) {
                        $html = '<input class="userinfo" type="text" name="" id="" value="'.$row->total_exp.'" data-jobid="'.$row->job_id.'" data-candidate="'.$row->id.'" data-schematype="noskill" data-schemaname="total_exp" />';
                    } else {
                        $html = $row->total_exp;
                    }
                }

                return $html;
            })
            ->editColumn('rel_exp', function ($row) use ($chkAdminRole) {
                $html = '';
                if ($chkAdminRole > 0) {
                    $html = '<input class="userinfo" type="text" name="" id="" value="'.$row->rel_exp.'" data-jobid="'.$row->job_id.'" data-candidate="'.$row->id.'" data-schematype="noskill" data-schemaname="rel_exp" />';
                } else {
                    if ($row->submitted_by == $this->user->id) {
                        $html = '<input class="userinfo" type="text" name="" id="" value="'.$row->rel_exp.'" data-jobid="'.$row->job_id.'" data-candidate="'.$row->id.'" data-schematype="noskill" data-schemaname="rel_exp" />';
                    } else {
                        $html = $row->rel_exp;
                    }
                }

                return $html;
            })
            ->editColumn('passport', function ($row) use ($chkAdminRole) {
                $sel_no = $sel_yes = '';
                if ($row->passport == 'Y') {
                    $sel_yes = 'selected="selected"';
                } else {
                    $sel_no = 'selected="selected"';
                }
                $schematype = 'passport';

                $html = '';
                if ($chkAdminRole > 0) {
                    $html = '<select  name="passport" onchange="updateCandidatePassport(this.value, '.$row->id.', '.$row->job_id.');" >
								<option value="Y" '.$sel_yes.'>Yes</option>
								<option value="N" '.$sel_no.'>No</option>
							</select>';
                } else {
                    if ($row->submitted_by == $this->user->id) {
                        $html = '<select  name="passport" onchange="updateCandidatePassport(this.value, '.$row->id.', '.$row->job_id.');" >
									<option value="Y" '.$sel_yes.'>Yes</option>
									<option value="N" '.$sel_no.'>No</option>
								</select>';
                    } else {
                        $schemeValue = '';
                        if ($row->passport == 'Y') {
                            $schemeValue = 'Yes';
                        } else {
                            $schemeValue = 'No';
                        }
                        $html = $schemeValue;
                    }
                }

                return $html;
            })
            ->editColumn('current_designation', function ($row) use ($chkAdminRole) {
                $html = '';
                if ($chkAdminRole > 0) {
                    $html = '<input class="userinfo" type="text" name="" id="" value="'.$row->current_designation.'" data-jobid="'.$row->job_id.'" data-candidate="'.$row->id.'" data-schematype="noskill" data-schemaname="current_designation" />';
                } else {
                    if ($row->submitted_by == $this->user->id) {
                        $html = '<input class="userinfo" type="text" name="" id="" value="'.$row->current_designation.'" data-jobid="'.$row->job_id.'" data-candidate="'.$row->id.'" data-schematype="noskill" data-schemaname="current_designation" />';
                    } else {
                        $html = @$row->current_designation;
                    }
                }

                return $html;
            })
            ->editColumn('current_company', function ($row) use ($chkAdminRole) {
                $html = '';
                if ($chkAdminRole > 0) {
                    $html = '<input class="userinfo" type="text" name="" id="" value="'.$row->current_company.'" data-jobid="'.$row->job_id.'" data-candidate="'.$row->id.'" data-schematype="noskill" data-schemaname="current_company" />';
                } else {
                    if ($row->submitted_by == $this->user->id) {
                        $html = '<input class="userinfo" type="text" name="" id="" value="'.$row->current_company.'" data-jobid="'.$row->job_id.'" data-candidate="'.$row->id.'" data-schematype="noskill" data-schemaname="current_company" />';
                    } else {
                        $html = @$row->current_company;
                    }
                }

                return $html;
            })
            ->editColumn('current_location', function ($row) use ($chkAdminRole) {
                $html = '';
                if ($chkAdminRole > 0) {
                    $html = '<input class="userinfo" type="text" name="" id="" value="'.$row->current_location.'" data-jobid="'.$row->job_id.'" data-candidate="'.$row->id.'" data-schematype="noskill" data-schemaname="current_location" />';
                } else {
                    if ($row->submitted_by == $this->user->id) {
                        $html = '<input class="userinfo" type="text" name="" id="" value="'.$row->current_location.'" data-jobid="'.$row->job_id.'" data-candidate="'.$row->id.'" data-schematype="noskill" data-schemaname="current_location" />';
                    } else {
                        $html = @$row->current_location;
                    }
                }

                return $html;
            })
            ->editColumn('np_title', function ($row) use ($chkAdminRole) {
                $noticeValue = '';
                $noticeperiod = NoticePeriod::all();
                $ndDll = '<select name="np_id"   class="form-control" style="float:left" onchange="updateCandidateNoticePeriod(this.value, '.$row->id.', '.$row->job_id.');">
							<option value="">Select Notice Period</option>';
                foreach ($noticeperiod as $npData) {
                    $sel = '';
                    if ($npData->np_id == $row->np_id) {
                        $sel = 'selected="selected';
                        $noticeValue = ucwords($npData->np_title);
                    }
                    $ndDll .= '<option '.@$sel.'  value="'.@$npData->np_id.'" >'.ucwords($npData->np_title).'</option>';
                }
                $ndDll .= '</select>';

                $html = '';
                if ($chkAdminRole > 0) {
                    $html = $ndDll;
                } else {
                    if ($row->submitted_by == $this->user->id) {
                        $html = $ndDll;
                    } else {
                        $html = $noticeValue;
                    }
                }

                return $html;
            })
            ->editColumn('status', function ($row) {
                $name = '';
                if (! empty($row->name)) {
                    $name = @$row->name;
                }
                $action = $name.'&nbsp;';
                $action .= '<a href="Javascript:void(0);" onclick="updateCandidateStatus('.$row->id.');">Update</a>';

                return $action;
            })
            ->editColumn('interview_status_level', function ($row) {
                $name = '';
                if (! empty($row->child_status_id) && $row->child_status_id > 0) {
                    $childStatus = '';
                    $childStatus = DB::table('candidate_status')->select('*')->where('id', $row->child_status_id)->first();
                    if (! empty($childStatus)) {
                        $name = @$childStatus->name;
                    }
                }
                $action = $name;

                return $action;

            })
            ->editColumn('qualification_name', function ($row) use ($chkAdminRole) {
                $educationHtml = '';
                $educationArr = DB::table('qualification_master')->select('*')->orderBy('qualification_master.display_order', 'ASC')->get();
                $educationHtml = '<select name="status_id" class="form-control"  onchange="updateApplicationEducation(this.value, '.$row->id.', '.$row->job_id.');" >';
                $educationHtml .= '<option  value= "" >Select Education</option>';
                $educationValue = '';

                foreach ($educationArr as $data_value) {
                    $sel = '';
                    if ($row->qualification == $data_value->id) {
                        $sel = 'selected="selected"';
                        $educationValue = ucwords($data_value->name);
                    }
                    $educationHtml .= '<option '.$sel.' value= "'.$data_value->id.'" >'.ucwords($data_value->name).'</option>';
                }
                $educationHtml .= '</select><span class= "statusLoader_'.$row->id.'">';

                $html = '';
                if ($chkAdminRole > 0) {
                    $html = $educationHtml;
                } else {
                    if ($row->submitted_by == $this->user->id) {
                        $html = $educationHtml;
                    } else {
                        $html = $educationValue;
                    }
                }

                return $html;

            })
            ->editColumn('resume', function ($row) {
                $html = '';
                $resumeUrl = ! empty($row->resume) ? $row->resume : null;
                if (! $resumeUrl) {
                    $docHash = DB::table('documents')
                        ->where('documentable_id', $row->id)
                        ->where('name', 'Resume')
                        ->value('hashname');
                    if ($docHash) {
                        $resumeUrl = $docHash;
                    }
                }
                if (! empty($resumeUrl)) {
                    $downloadUrl = route('download.resume', [
                        'id' => $row->id,
                        'path' => rtrim(strtr(base64_encode($resumeUrl), '+/', '-_'), '='),
                    ]);
                    $html = '<p class="text-muted resume-button" id="resume-'.$row->id.'">
								<a href="'.$downloadUrl.'" target="_blank" class="btn" title="Download Resume">
									<i class="fa fa-file text-warning" aria-hidden="true"></i>
								</a>
							</p>';
                } else {
                    $html = 'N/A';
                }

                return $html;
            });

        $rawColumn = [];
        $rawColumn[] = 'action';
        $rawColumn[] = 'created_at';
        $rawColumn[] = 'resume';
        $rawColumn[] = 'full_name';
        $rawColumn[] = 'status';

        $rawColumn[] = 'client_name';
        $rawColumn[] = 'email';
        $rawColumn[] = 'phone';
        $rawColumn[] = 'ctc';
        $rawColumn[] = 'ectc';

        $rawColumn[] = 'total_exp';
        $rawColumn[] = 'rel_exp';
        $rawColumn[] = 'passport';
        $rawColumn[] = 'current_designation';
        $rawColumn[] = 'current_company';

        $rawColumn[] = 'current_location';
        $rawColumn[] = 'np_title';
        $rawColumn[] = 'qualification_name';
        $rawColumn[] = 'interview_status_level';

        $nskillData = $nsdata = '';
        $nskillData = DB::table('job_skills')
            ->join('skills', 'job_skills.skill_id', '=', 'skills.id')
            ->select('skills.id', 'skills.name', 'job_skills.job_id')
            ->where('job_skills.job_id', '=', $request->jobid)
            ->orderBy('skills.name', 'ASC')
            ->get();

        // $chkAdminRole
        foreach ($nskillData as $nsdata) {

            $dataTable = $dataTable->editColumn('skill_'.$nsdata->id, function ($row) use ($nsdata, $chkAdminRole) {

                $skillRow = DB::table('candidate_skills_value')->select('*')
                    ->where('job_id', $nsdata->job_id)
                    ->where('candidate_id', $row->id)
                    ->where('skill_id', $nsdata->id)
                    ->first();

                $html = '';
                if ($chkAdminRole > 0) {
                    $html = '<input class="userinfo" type="text" name="skill_'.$nsdata->id.'"  value="'.@$skillRow->skill_value.'" data-jobid="'.$nsdata->job_id.'" data-candidate="'.$row->id.'" data-schematype="skill" data-schemaname="'.$nsdata->id.'" />';
                } else {
                    if ($row->submitted_by == $this->user->id) {
                        $html = '<input class="userinfo" type="text" name="skill_'.$nsdata->id.'"  value="'.@$skillRow->skill_value.'" data-jobid="'.$nsdata->job_id.'" data-candidate="'.$row->id.'" data-schematype="skill" data-schemaname="'.$nsdata->id.'" />';
                    } else {
                        $html = ucwords(@$skillRow->skill_value);
                    }

                }

                return $html;

            });

            $rawColumn[] = 'skill_'.$nsdata->id;
        }

        $dataTable = $dataTable->rawColumns($rawColumn);
        $dataTable = $dataTable->addIndexColumn()
            ->make(true);

        return $dataTable;
    }

    public function createSchedule(Request $request, $id)
    {
        abort_if(Gate::denies('add_schedule'), 403);
        $this->candidates = JobApplication::all();
        $this->users = User::company()->get();
        $this->scheduleDate = $request->date;
        $this->currentApplicant = JobApplication::withoutGlobalScopes()->findOrFail($id);

        return view('service_company.cvtracker.interview-create', $this->data)->render();
    }

    public function storeSchedule(StoreRequest $request)
    {
        abort_if(Gate::denies('add_schedule'), 403);

        $dateTime = $request->scheduleDate.' '.$request->scheduleTime;
        $dateTime = Carbon::createFromFormat('Y-m-d H:i', $dateTime);

        // store Schedule
        $interviewSchedule = new InterviewSchedule;
        $interviewSchedule->job_application_id = $request->candidate;
        $interviewSchedule->schedule_date = $dateTime;
        $interviewSchedule->save();

        // Update Schedule Status
        $interviweColumn = ApplicationStatus::where('status', 'interview')->first();
        $jobApplication = JobApplication::withoutGlobalScopes()->find($request->candidate);
        $jobApplication->status_id = $interviweColumn->id;
        $jobApplication->save();

        if (! empty($request->employee)) {
            InterviewScheduleEmployee::where('interview_schedule_id', $interviewSchedule->id)->delete();
            foreach ($request->employee as $employee) {
                $scheduleEmployee = new InterviewScheduleEmployee;
                $scheduleEmployee->user_id = $employee;
                $scheduleEmployee->interview_schedule_id = $interviewSchedule->id;
                $scheduleEmployee->save();

                $user = User::withoutGlobalScopes()->find($employee);
                // Mail to employee for inform interview schedule
                Notification::send($user, new ScheduleInterview($jobApplication));
            }
        }

        // mail to candidate for inform interview schedule
        Notification::send($jobApplication, new CandidateScheduleInterview($jobApplication, $interviewSchedule));

        return Reply::redirect(route('service_company.interview-schedule.index'), __('menu.interviewSchedule').' '.__('messages.createdSuccessfully'));
    }

    public function update(UpdateJobApplication $request, $id)
    {
        abort_if(Gate::denies('edit_job_applications'), 403);

        $jobApplication = JobApplication::withoutGlobalScopes()->find($id);
        $jobApplication->full_name = $request->full_name;
        $jobApplication->status_id = $request->status_id;
        $jobApplication->email = $request->email;
        $jobApplication->phone = $request->phone;
        $jobApplication->cover_letter = $request->cover_letter;

        if ($request->hasFile('resume')) {

            $jobApplication->resume = Files::upload($request->resume, 'resumes', null, null, false);
        }

        if ($request->hasFile('photo')) {
            $jobApplication->photo = Files::upload($request->photo, 'candidate-photos');
        }

        $jobApplication->save();

        // Job Application Answer save
        if (isset($request->answer) && count($request->answer) > 0) {
            JobApplicationAnswer::where('job_application_id', $jobApplication->id)->delete();
            foreach ($request->answer as $key => $value) {
                $answer = new JobApplicationAnswer;
                $answer->job_application_id = $jobApplication->id;
                $answer->job_id = $jobApplication->job_id;
                $answer->question_id = $key;
                $answer->answer = $value;
                $answer->save();
            }
        }

        return Reply::redirect(route('service_company.cvtracker.index'), __('menu.jobApplications').' '.__('messages.updatedSuccessfully'));
    }

    public function destroy($id)
    {
        abort_if(Gate::denies('delete_job_applications'), 403);

        JobApplication::destroy($id);

        return Reply::success(__('messages.recordDeleted'));
    }

    public function show($id)
    {
        $this->application = JobApplication::with(['schedule', 'status', 'schedule.employee', 'schedule.comments.user'])->withoutGlobalScopes()->find($id);

        $this->answers = JobApplicationAnswer::with(['question'])
            ->where('job_id', $this->application->job_id)
            ->where('job_application_id', $this->application->id)
            ->get();

        $view = view('service_company.cvtracker.show', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'view' => $view]);
    }

    public function updateIndex(Request $request)
    {
        $taskIds = $request->applicationIds;
        $boardColumnIds = $request->boardColumnIds;
        $priorities = $request->prioritys;

        if (! is_null($taskIds)) {
            foreach ($taskIds as $key => $taskId) {
                if (! is_null($taskId)) {

                    $task = JobApplication::withoutGlobalScopes()->find($taskId);
                    $task->column_priority = $priorities[$key];
                    $task->status_id = $boardColumnIds[$key];

                    $task->save();
                }
            }
        }

        return Reply::dataOnly(['status' => 'success']);
    }

    public function table()
    {
        abort_if(Gate::denies('view_job_applications'), 403);
        $this->boardColumns = ApplicationStatus::all();
        $this->locations = JobLocation::all();
        $this->jobs = Job::all();
        $this->skills = Skill::all();

        return view('service_company.cvtracker.index', $this->data);
    }

    public function ratingSave(Request $request, $id)
    {
        abort_if(Gate::denies('edit_job_applications'), 403);

        $application = JobApplication::withoutGlobalScopes()->findOrFail($id);
        $application->rating = $request->rating;
        $application->save();

        return Reply::success(__('messages.updatedSuccessfully'));
    }

    public function viewDetails(Request $request)
    {
        $candidateCount = JobApplication::count();
        if ($candidateCount > 0 && ! is_null($this->activePackage->package->no_of_candidate_access) && $this->activePackage->package->no_of_candidate_access <= $candidateCount) {
            $this->message = __('modules.subscription.candidatePurchase');

            return Reply::error(__('modules.subscription.candidatePurchase'));
        }

        $application = JobApplication::withoutGlobalScopes()->findOrFail($request->id);
        $application->is_viewed = 1;
        $application->save();

        return Reply::dataOnly([
            'status' => 'success',
            'email' => $application->email,
            'phone' => $application->phone,
            'resume' => route('service_company.cvtracker.download', $application->id),
        ]);
    }

    public function download($id)
    {
        $jobApplication = JobApplication::withoutGlobalScopes()->findOrFail($id);
        $resume = $jobApplication->resume;
        if (empty($resume)) {
            $resume = DB::table('documents')->where('documentable_id', $id)->where('name', 'Resume')->value('hashname');
        }
        if (! empty($resume) && str_starts_with($resume, 'http')) {
            return redirect()->route('download.resume', ['id' => $id, 'path' => rtrim(strtr(base64_encode($resume), '+/', '-_'), '=')]);
        }
        if (! file_exists('user-uploads/resumes/'.$jobApplication->resume)) {
            abort(404);
        }

        return response()->download('user-uploads/resumes/'.$jobApplication->resume);
    }

    public function get_client_job1(Request $request)
    {
        $clientId = '';
        $clientId = $request->clientId;

        $html = '<select class="select2" name="jobs" id="jobs" data-style="form-control">';
        $jobArr = DB::table('jobs')->select('id', 'title')->where('client_id', $clientId)->where('status', 'active')->get();
        foreach ($jobArr as $jobvalue) {
            $html .= '<option value= "'.$jobvalue->id.'">'.ucfirst($jobvalue->title).'</option>';
        }
        $html .= '</select>';
        echo $html;
        exit;
        // return view('service_company.cvtracker.get_client_job', $this->data);
    }

    public function get_client_job(Request $request)
    {

        $clientId = $html = '';

        $clientId = $request->clientId;

        if ($clientId == 'all' || $clientId == '') {
            $jobArr = DB::table('jobs')->select('id', 'title')->where('status', 'active')->get();
        } else {
            $jobArr = DB::table('jobs')->select('id', 'title')->where('client_id', $clientId)->where('status', 'active')->get();
        }
        $html = '<option value="all">All Jobs</option>';
        foreach ($jobArr as $jobvalue) {
            $html .= '<option value= "'.$jobvalue->id.'">'.ucfirst($jobvalue->title).'</option>';
        }

        return Reply::dataOnly(['status' => 'success', 'data' => $html]);
    }

    public function store(Request $request)
    {
        $skillId = $company_id = '';
        $jobid = $request->jobid;
        $skill_name = $request->skill_name;
        $job = Job::withoutGlobalScopes()->findOrFail($jobid);

        $company_id = auth()->user()->company_id;

        $values = ['name' => $skill_name, 'company_id' => $company_id, 'category_id' => $job->category_id];
        DB::table('skills')->insert($values);
        $skillId = DB::getPdo()->lastInsertId();

        $values = ['company_id' => $company_id, 'skill_id' => $skillId, 'job_id' => $jobid];
        DB::table('job_skills')->insert($values);
        $skillId = DB::getPdo()->lastInsertId();

        echo 1;
        exit;
        // return redirect('service_company/jobtracker/'.$jobid)->with('status', 'Profile updated!');
        // return Reply::redirect(route('service_company.jobtracker.'.$jobid ), __('menu.jobApplications') . ' ' . __('messages.createdSuccessfully'));

    }

    public function updateinformation(Request $request)
    {

        $jobid = $candidate = $schematype = '';

        $jobid = $request->jobid;
        $candidate = $request->candidate;
        $schematype = $request->schematype;
        $schemaname = $request->schemaname;
        $schemavalue = $request->schemavalue;

        $timelineApp = $application = JobApplication::withoutGlobalScopes()->findOrFail($candidate);

        if ($schematype == 'noskill') {

            $schemaOldValue = '';

            if ($schemaname == 'full_name') {
                $schemaOldValue = $application->full_name;
                $application->full_name = $request->schemavalue;
            }
            if ($schemaname == 'email') {
                $schemaOldValue = $application->email;
                $application->email = $request->schemavalue;
            }
            if ($schemaname == 'phone') {
                $schemaOldValue = $application->phone;
                $application->phone = $request->schemavalue;
            }
            if ($schemaname == 'ctc') {
                $schemaOldValue = $application->ctc;
                $application->ctc = $request->schemavalue;
            }
            if ($schemaname == 'ectc') {
                $schemaOldValue = $application->ectc;
                $application->ectc = $request->schemavalue;
            }
            if ($schemaname == 'total_exp') {
                $schemaOldValue = $application->total_exp;
                $application->total_exp = $request->schemavalue;
            }
            if ($schemaname == 'rel_exp') {
                $schemaOldValue = $application->rel_exp;
                $application->rel_exp = $request->schemavalue;
            }
            if ($schemaname == 'current_designation') {
                $schemaOldValue = $application->current_designation;
                $application->current_designation = $request->schemavalue;
            }
            if ($schemaname == 'current_company') {
                $schemaOldValue = $application->current_company;
                $application->current_company = $request->schemavalue;
            }
            if ($schemaname == 'current_location') {
                $schemaOldValue = $application->current_location;
                $application->current_location = $request->schemavalue;
            }
            if ($schemaname == 'passport') {
                $schemaOldValue = $application->passport;
                $application->passport = $request->schemavalue;
            }
            if ($schemaname == 'np_id') {
                $schemaOldValue = $application->schemaOldValue;
                $application->np_id = $request->schemavalue;
            }
            if ($schemaname == 'status_id') {
                $application->status_id = $request->schemavalue;

                // Sender Detail
                $submitted_by = DB::table('users')->select('email')->where('id', $application->submitted_by)->first();
                $candidateSenderEmail = $submitted_by->email;

                // Candidate Detail
                $candidateName = $application->full_name;
                $candidateEmail = $application->email;

                // Status name
                $statusArr = DB::table('application_status')->select('status')->where('id', $application->status_id)->where('company_id', $this->user->company_id)->first();
                $status_name = $statusArr->status;

                $user = [
                    'name' => $candidateName,
                    'email' => $candidateEmail,

                    'cc_email' => $candidateSenderEmail,
                    'status_name' => $status_name,
                ];

                Mail::send('email.candidate_status_update', ['user' => $user], function ($message) use ($user) {
                    $message->from('no-reply@vibhanshujaiswal.com', 'ATS');
                    $message->to($user['email'], $user['name']);
                    $message->bcc($user['cc_email']);
                    $message->subject('Your Job Application update Successfully.');
                });

            }

            if ($schemaname == 'education') {
                $schemaOldValue = $application->qualification;
                $application->qualification = $request->schemavalue;
            }

            $liveUserId = auth()->user()->id;
            $applicationId = @$timelineApp->id;
            $application_jobId = @$timelineApp->job_id;
            $application_master_status_id = $application->master_status_id;
            $status_name = '';

            if ($schemaname == 'education') {
                $education = DB::table('qualification_master')->select('*')->where('id', '=', @$request->schemavalue)->first();
                $note = "Update $schemaname <strong>$education->name</strong>";
            } elseif ($schemaname == 'np_id') {
                $noticePeriod = DB::table('notice_periods')->select('*')->where('np_id', '=', @$request->schemavalue)->first();
                $note = "Update notice period <strong>$noticePeriod->np_title</strong>";
            } elseif ($schemaname == 'passport') {
                $password = '';
                if (@$request->schemavalue == 'N') {
                    $password = 'NO';
                }
                if (@$request->schemavalue == 'Y') {
                    $password = 'YES';
                }
                $note = "Update passport [$password]";
            } else {
                $note = "Update $schemaname  Old = <strong>$schemaOldValue</strong>  New = <strong>$request->schemavalue</strong>";
            }

            GeneralMethod::manageTimeline($liveUserId, $applicationId, $application_jobId, $application_master_status_id, $status_name, $note);

            $application->save();
        }

        if ($schematype == 'skill') {

            $skillcounter = DB::table('candidate_skills_value')->select('*')
                ->where('job_id', @$request->jobid)
                ->where('candidate_id', @$request->candidate)
                ->where('skill_id', @$request->schemaname)
                ->count();

            if ($skillcounter == 0 || $skillcounter == '') {
                $values = [
                    'job_id' => @$request->jobid,
                    'skill_id' => @$request->schemaname,
                    'candidate_id' => @$request->candidate,
                    'skill_value' => @$request->schemavalue,
                ];
                DB::table('candidate_skills_value')->insert($values);
            } else {

                DB::table('candidate_skills_value')
                    ->where('job_id', @$request->jobid)
                    ->where('candidate_id', @$request->candidate)
                    ->where('skill_id', @$request->schemaname)
                    ->limit(1)
                    ->update(['skill_value' => @$request->schemavalue]);
            }

            $liveUserId = auth()->user()->id;
            $applicationId = @$timelineApp->id;
            $application_jobId = @$timelineApp->job_id;
            $application_master_status_id = $application->master_status_id;
            $status_name = '';
            $note = "Update Skills Value <strong> $request->schemavalue </strong>";
            GeneralMethod::manageTimeline($liveUserId, $applicationId, $application_jobId, $application_master_status_id, $status_name, $note);

        }
        // echo '<pre>'; print_r($_REQUEST); exit;

    }

    public function export1($status, $location, $startDate, $endDate, $jobs)
    {

        // Fetching All Job Applications
        $jobApplications = JobApplication::select(
            'job_applications.id',
            'jobs.title',
            'job_applications.full_name',
            'job_applications.email',
            'job_applications.phone',
            'job_applications.cover_letter',
            'application_status.status',
            'job_applications.created_at'
        )
            ->leftJoin('jobs', 'jobs.id', '=', 'job_applications.job_id')
            ->leftJoin('application_status', 'application_status.id', '=', 'job_applications.status_id');
        if ($status != 'all' && $status != '') {
            $jobApplications = $jobApplications->where('job_applications.status_id', $status);
        }

        // Filter  By Location
        if ($location != 'all' && $location != '') {
            $jobApplications = $jobApplications->where('jobs.location_id', $location);
        }

        // Filter  By Job
        if ($jobs != 'all' && $jobs != '') {
            $jobApplications = $jobApplications->where('job_applications.job_id', $jobs);
        }  // Filter  By Job
        if ($jobs != 'all' && $jobs != '') {
            $jobApplications = $jobApplications->where('job_applications.job_id', $jobs);
        }

        // Filter  By StartDate of job
        if ($startDate != null && $startDate != '' && $startDate != 0) {
            $jobApplications = $jobApplications->where(DB::raw('DATE(jobs.`start_date`)'), '>=', "$startDate");
        }

        // Filter  By EndDate of job
        if ($endDate != null && $endDate != '' && $endDate != 0) {
            $jobApplications = $jobApplications->where(DB::raw('DATE(jobs.`end_date`)'), '<=', "$endDate");
        }

        $jobApplications = $jobApplications->get();

        // Initialize the array which will be passed into the Excel
        // generator.
        $exportArray = [];

        // Define the Excel spreadsheet headers
        $exportArray[] = ['ID', 'Job Title', 'Name', 'Email', 'Mobile', 'Cover Letter', 'Status', 'Created at'];

        // Convert each member of the returned collection into an array,
        // and append it to the payments array.
        foreach ($jobApplications as $row) {
            $exportArray[] = $row->toArray();
        }

        echo '<pre>';
        print_r($exportArray);
        exit;
        // Generate and return the spreadsheet
        Excel::create('job-applications', function ($excel) use ($exportArray) {

            // Set the spreadsheet title, creator, and description
            $excel->setTitle('Job Applications');
            $excel->setCreator('Recruit')->setCompany($this->companyName);
            $excel->setDescription('job-applications file');

            // Build the spreadsheet, passing in the payments array
            $excel->sheet('sheet1', function ($sheet) use ($exportArray) {
                $sheet->fromArray($exportArray, null, 'A1', false, false);

                $sheet->row(1, function ($row) {

                    // call row manipulation methods
                    $row->setFont([
                        'bold' => true,
                    ]);
                });
            });
        })->download('xlsx');
    }

    public function export($jobs, $start, $length)
    {

        // echo $jobs; exit;
        $jobApplications = JobApplication::select(
            'job_applications.id',

            'job_applications.created_at',
            'job_applications.full_name',
            'clients.name',
            'jobs.title',
            'users.name as submitted_by',

            'job_applications.email',
            'job_applications.phone',
            'job_applications.ctc',
            'job_applications.ectc',
            'job_applications.total_exp',

            'job_applications.rel_exp',
            'job_applications.passport',
            'notice_periods.np_title',
            'job_applications.current_designation',
            'job_applications.current_company',

            'job_applications.current_location',
            'application_status.status'

        );
        $jobApplications = $jobApplications->join('jobs', 'jobs.id', '=', 'job_applications.job_id');
        $jobApplications = $jobApplications->join('clients', 'clients.id', '=', 'jobs.client_id');
        $jobApplications = $jobApplications->join('users', 'users.id', '=', 'job_applications.submitted_by');
        $jobApplications = $jobApplications->join('notice_periods', 'notice_periods.np_id', 'job_applications.np_id');
        $jobApplications = $jobApplications->leftJoin('application_status', 'application_status.id', '=', 'job_applications.status_id');
        $jobApplications = $jobApplications->where('job_applications.job_id', '=', $jobs);
        // $jobApplications  = $jobApplications->get();

        $jobApplications = $jobApplications->skip($start)->take($length)->get();

        // echo '<pre>'; print_r($jobApplications); exit;

        $jobSkill = DB::table('job_skills')
            ->join('skills', 'skills.id', 'job_skills.skill_id')
            ->select('job_skills.company_id', 'job_skills.skill_id', 'job_skills.job_id', 'skills.name')
            ->where('job_skills.job_id', '=', $jobs)
            ->get()->toArray();

        $exportArray = [];

        $exportArray[0]['SNo'] = 'SNo';
        $exportArray[0]['Date'] = 'Date';
        $exportArray[0]['Applicant'] = 'Applicant';
        $exportArray[0]['Client'] = 'Client';
        $exportArray[0]['Jobs'] = 'Jobs';
        $exportArray[0]['Source'] = 'Source';

        // echo '<pre>'; print_r( $jobSkill); exit;
        $inc = 0;
        foreach ($jobSkill as $skillValue) {
            $exportArray[0][$inc] = 'Skill# '.$skillValue->name;
            $inc = $inc + 1;
        }

        $exportArray[0]['Email'] = 'Email';
        $exportArray[0]['Phone'] = 'Phone';
        $exportArray[0]['CTC'] = 'CTC';
        $exportArray[0]['ECTC'] = 'ECTC';
        $exportArray[0]['Experience'] = 'Experience';

        $exportArray[0]['Relevant Experience'] = 'Relevant Experience';
        $exportArray[0]['Passport'] = 'Passport';
        $exportArray[0]['Notice Period'] = 'Notice Period';
        $exportArray[0]['Current Designation'] = 'Current Designation';
        $exportArray[0]['Current Company'] = 'Current Company';
        $exportArray[0]['Current Location'] = 'Current Location';
        $exportArray[0]['Status'] = 'Status';

        $loopCounter = 1;
        foreach ($jobApplications as $row) {

            $exportArray[$loopCounter]['SNo'] = $loopCounter;
            $exportArray[$loopCounter]['Date'] = $row->created_at;
            $exportArray[$loopCounter]['Applicant'] = $row->full_name;
            $exportArray[$loopCounter]['Client'] = $row->name;
            $exportArray[$loopCounter]['Jobs'] = $row->title;
            $exportArray[$loopCounter]['Source'] = $row->submitted_by;

            $inc = 0;
            foreach ($jobSkill as $skillValue) {
                $skillvalue = DB::table('candidate_skills_value')
                    ->select('candidate_skills_value.skill_value')
                    ->where('candidate_skills_value.job_id', '=', $jobs)
                    ->where('candidate_skills_value.candidate_id', '=', $row->id)
                    ->where('candidate_skills_value.skill_id', '=', $skillValue->skill_id)->first();
                if (! empty(@$skillvalue->skill_value)) {
                    $exportArray[$loopCounter][$inc] = @$skillvalue->skill_value;

                } else {
                    $exportArray[$loopCounter][$inc] = 'N/A';
                }
                $inc = $inc + 1;
            }

            $exportArray[$loopCounter]['Email'] = $row->email;
            $exportArray[$loopCounter]['Phone'] = $row->phone;
            $exportArray[$loopCounter]['CTC'] = $row->ctc;
            $exportArray[$loopCounter]['ECTC'] = $row->ectc;
            $exportArray[$loopCounter]['Experience'] = $row->total_exp;

            $exportArray[$loopCounter]['Relevant Experience'] = $row->rel_exp;
            $exportArray[$loopCounter]['Passport'] = $row->passport;
            $exportArray[$loopCounter]['Notice Period'] = $row->np_title;
            $exportArray[$loopCounter]['Current Designation'] = $row->current_designation;
            $exportArray[$loopCounter]['Current Company'] = $row->current_company;
            $exportArray[$loopCounter]['Current Location'] = $row->current_location;
            $exportArray[$loopCounter]['Status'] = $row->status;
            $loopCounter = $loopCounter + 1;
        }

        $date = date('d-m-Y');
        // Generate and return the spreadsheet
        Excel::create('JobTracker-'.$date, function ($excel) use ($exportArray) {

            $excel->setTitle('Job Applications');
            $excel->setCreator('Recruit')->setCompany($this->companyName);
            $excel->setDescription('Job_tracker_file');

            $excel->sheet('sheet1', function ($sheet) use ($exportArray) {
                $sheet->fromArray($exportArray, null, 'A1', false, false);

                $sheet->row(1, function ($row) {

                    $row->setFont([
                        'bold' => true,
                    ]);
                });
            });
        })->download('xlsx');
    }

    public function loadVisibilityControl($id)
    {
        $job_id = $id;
        if ($job_id > 0) {
            $this->jobSkill = DB::table('job_skills')
                ->join('skills', 'skills.id', 'job_skills.skill_id')
                ->select('job_skills.company_id', 'job_skills.skill_id', 'job_skills.job_id', 'skills.name')
                ->where('job_skills.job_id', '=', $job_id)
                ->get()->toArray();
        }

        return view('service_company.jobtracker.load_visibility_control', $this->data)->render();
    }
}
