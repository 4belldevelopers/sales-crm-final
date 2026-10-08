<?php

namespace App\Http\Controllers\ServiceCompany;

use App\Helper\GeneralMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Yajra\DataTables\Facades\DataTables;

class ServiceCompanyJobOnboardController extends ServiceCompanyBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = __('Joining Tracker');
        $this->pageIcon = 'icon-user';
    }

    public function index(Request $request)
    {
        abort_if(Gate::denies('view_job_applications'), 403);
        $company_id = auth()->user()->company_id;

        $lock_unlock = GeneralMethod::checksubscription();
        if ($lock_unlock == false) {
            return redirect()->route('service_company.dashboard')->with('message_paid', 'This is a paid featute');
        }

        $this->appCounter = \Illuminate\Support\Facades\Cache::get('joining_tracker_total_count_'.$company_id, 0);
        $this->countries = \Illuminate\Support\Facades\Cache::remember('static_countries_list', 86400, function () {
            return DB::table('countries')->select('id', 'country_name')->orderBy('country_name', 'asc')->get();
        });

        // Initial KPI seed for the redesigned Joining Tracker hero cards.
        // WHY only "total" is seeded: the live, authoritative numbers for every
        // card + tab come from ONE Elasticsearch aggregation returned by
        // cvtracker.allcandidates (status_cus=6) on the first table fetch, so the JS overrides
        // these the moment data lands. Seeding from Redis cache (or fallback 0) eliminates 100% of
        // avoidable MySQL read load on page loads while maintaining zero flash of 0.
        $this->joiningStats = [
            'total' => (int) $this->appCounter,
            'today' => 0,
            'joined' => 0,
            'backed_out' => 0,
            'onboarded' => 0,
            'this_week' => 0,
            'this_month' => 0,
            'delayed' => 0,
            'no_show' => 0,
            'pending_docs' => 0,
        ];

        return view('service_company.job-onboard.index', $this->data);
    }

    // ----------data function in un-use code
    public function data(Request $request)
    {
        abort_if(Gate::denies('view_joining'), 403);

        $company_id = auth()->user()->company_id;
        $currency = GeneralMethod::currency();
        $salaryMode = GeneralMethod::salaryMode();
        $currencySymbole = GeneralMethod::currencySymbole();

        $job_status_arr = GeneralMethod::job_status();

        $chkAdminRole = $this->user->hasRole('admin');
        if (($chkAdminRole > 0) && (in_array('all_requisition', $this->userPermissions) && in_array('view_jobs', $this->userPermissions))) {

            $joiningData = DB::table('job_applications');
            $joiningData = $joiningData->select(
                'job_applications.id',
                'add_job_wise_condidates.job_id',
                'job_applications.full_name',
                'job_applications.current_designation',
                'job_applications.proposed_ctc',
                'job_applications.any_offer',
                'job_applications.document',
                'job_applications.sub_for_approval',
                'offer_job_applications.offer_approved',
                'offer_job_applications.offer_release',
                'offer_job_applications.offer_acceptance',
                'offer_job_applications.joined',
                'join_applications.joined_date',
                'join_applications.offer_currency',
                'join_applications.offer_mode',
                'job_applications.phone',
                'job_applications.email',
                'join_applications.offer_amount',
                'join_applications.master_status_id',
                'join_applications.child_status_id',
                'add_job_wise_condidates.thirdparty_company_id',
                'jobs.title',
                'clients.name as client_name',
                'users.name as posted_by',
                'users.image as posted_by_image',
                'master_status.name',
                'master_status.css_style',
                'jobs.status as job_status',
                'recommendation_relation.id as recommendation_id',
                'recommendation_relation.from_company_id',
                'recommendation_relation.final_status',
                'recommendation_relation.to_company_id',
                'uc.company_name as uc_company_name',
                'add_job_wise_condidates.id as wiseId'
            )
                ->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id')
                ->join('jobs', function ($join) {
                    $join->on('jobs.id', 'add_job_wise_condidates.job_id');
                })
                ->leftJoin('companies as uc', function ($join) {
                    $join->on('uc.id', 'add_job_wise_condidates.company_id');
                });
            // $joiningData = $joiningData->join('jobs', 'jobs.id', 'job_applications.job_id');
            $joiningData = $joiningData->join('clients', 'clients.id', 'jobs.client_id');
            $joiningData = $joiningData->join('users', 'users.id', 'job_applications.submitted_by');
            // $joiningData = $joiningData->leftjoin('companies as uc', 'uc.id', '=', 'users.company_id');
            // $joiningData = $joiningData->join('master_status', 'master_status.id', 'job_applications.master_status_id');
            $joiningData = $joiningData->leftJoin('recommendation_relation', 'recommendation_relation.job_id', '=', 'jobs.id');
            $joiningData = $joiningData->join('offer_job_applications', 'offer_job_applications.application_id', 'job_applications.id');
            $joiningData = $joiningData->join('join_applications', 'join_applications.jobApplication', 'job_applications.id')
                ->join('master_status', function ($join) {
                    $join->on('master_status.id', 'join_applications.master_status_id');
                });
            // $joiningData = $joiningData->where('offer_job_applications.company_id', '=', $company_id);

            if (\request('search') != '') {

                $search = request('search');

                $joiningData = $joiningData->where(function ($query) use ($company_id, $search) {
                    return
                        $query->where('jobs.company_id', '=', $company_id)
                            ->where('join_applications.master_status_id', '=', 6)
                            ->where('offer_job_applications.company_id', '=', $company_id)
                            ->where('job_applications.full_name', 'LIKE', "%$search%");
                });

                $joiningData = $joiningData->orWhere(function ($query) use ($company_id, $search) {
                    return
                        $query->where('recommendation_relation.to_company_id', '=', $company_id)
                            ->where('add_job_wise_condidates.thirdparty_company_id', '=', $company_id)
                            ->where('join_applications.master_status_id', '=', 6)
                            ->where('job_applications.full_name', 'LIKE', "%$search%");
                });

            } else {

                $joiningData = $joiningData->where(function ($query) use ($company_id) {
                    return
                        $query->where('jobs.company_id', '=', $company_id)
                            ->where('join_applications.master_status_id', '=', 6);
                });

                $joiningData = $joiningData->orWhere(function ($query) use ($company_id) {
                    return
                        $query->where('recommendation_relation.final_status', '=', 1)
                            ->where('recommendation_relation.to_company_id', '=', $company_id)
                            ->where('join_applications.master_status_id', '=', 6)
                            ->where('add_job_wise_condidates.thirdparty_company_id', '=', $company_id);
                });

            }

        } else {

            $joiningData = DB::table('job_applications');
            $joiningData = $joiningData->select(
                'job_applications.id',
                'add_job_wise_condidates.job_id',
                'job_applications.full_name',
                'job_applications.current_designation',
                'job_applications.proposed_ctc',
                'job_applications.any_offer',
                'job_applications.document',
                'offer_job_applications.sub_for_approval',
                'offer_job_applications.offer_approved',
                'offer_job_applications.offer_release',
                'offer_job_applications.offer_acceptance',
                'offer_job_applications.joined',
                'join_applications.joined_date',
                'join_applications.offer_currency',
                'join_applications.offer_mode',
                'job_applications.phone',
                'job_applications.email',
                'join_applications.offer_amount',
                'join_applications.master_status_id',
                'add_job_wise_condidates.child_status_id',
                'add_job_wise_condidates.thirdparty_company_id',
                'jobs.title',
                'clients.name as client_name',
                'users.name as posted_by',
                'users.image as posted_by_image',
                'master_status.name',
                'master_status.css_style',
                'jobs.status as job_status',
                'recommendation_relation.id as recommendation_id',
                'recommendation_relation.from_company_id',
                'recommendation_relation.final_status',
                'recommendation_relation.to_company_id',
                'uc.company_name as uc_company_name',
                'add_job_wise_condidates.id as wiseId'
            )
                ->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id')
                ->join('master_status', function ($join) {
                    $join->on('master_status.id', 'add_job_wise_condidates.master_status_id');
                })
                ->join('jobs', function ($join) {
                    $join->on('jobs.id', 'add_job_wise_condidates.job_id');
                })
                ->leftJoin('companies as uc', function ($join) {
                    $join->on('uc.id', 'add_job_wise_condidates.company_id');
                });
            // $joiningData = $joiningData->join('jobs', 'jobs.id', 'job_applications.job_id');
            $joiningData = $joiningData->join('job_assign', 'job_assign.job_id', 'jobs.id');
            $joiningData = $joiningData->join('clients', 'clients.id', 'jobs.client_id');
            $joiningData = $joiningData->join('users', 'users.id', 'job_applications.submitted_by');
            // $joiningData = $joiningData->leftjoin('companies as uc', 'uc.id', '=', 'users.company_id');
            // $joiningData = $joiningData->join('master_status', 'master_status.id', 'job_applications.master_status_id');
            $joiningData = $joiningData->leftJoin('recommendation_relation', 'recommendation_relation.job_id', '=', 'jobs.id');
            $joiningData = $joiningData->join('offer_job_applications', 'offer_job_applications.joined', 'add_job_wise_condidates.id');
            $joiningData = $joiningData->join('join_applications', 'join_applications.jobApplication', 'job_applications.id')
                ->join('master_status', function ($join) {
                    $join->on('master_status.id', 'join_applications.master_status_id');
                });
            // $joiningData = $joiningData->where('offer_job_applications.company_id', '=', $company_id);

            if (\request('search') != '') {

                $search = request('search');

                $joiningData = $joiningData->where(function ($query) use ($company_id, $search) {
                    return
                        $query->where('jobs.company_id', '=', $company_id)
                            ->where('join_applications.master_status_id', '=', 6)
                            ->where('offer_job_applications.company_id', '=', $company_id)
                            ->where('job_assign.user_id', $this->user->id)
                            ->where('job_applications.full_name', 'LIKE', "%$search%");
                });

                $joiningData = $joiningData->orWhere(function ($query) use ($company_id, $search) {
                    return
                        $query->where('recommendation_relation.to_company_id', '=', $company_id)
                            ->where('add_job_wise_condidates.thirdparty_company_id', '=', $company_id)
                            ->where('job_assign.user_id', $this->user->id)
                            ->where('join_applications.master_status_id', '=', 6)
                            ->where('job_applications.full_name', 'LIKE', "%$search%");
                });

            } else {

                $joiningData = $joiningData->where(function ($query) use ($company_id) {
                    return
                        $query->where('jobs.company_id', '=', $company_id)
                            ->where('job_assign.user_id', $this->user->id)
                            ->where('join_applications.master_status_id', '=', 6);
                });

                $joiningData = $joiningData->orWhere(function ($query) use ($company_id) {
                    return
                        $query->where('recommendation_relation.final_status', '=', 1)
                            ->where('recommendation_relation.to_company_id', '=', $company_id)
                            ->where('job_assign.user_id', $this->user->id)
                            ->where('join_applications.master_status_id', '=', 6)
                            ->where('add_job_wise_condidates.thirdparty_company_id', '=', $company_id);
                });

            }

        }

        $joiningData = $joiningData->orderBy('add_job_wise_condidates.id', 'DESC');

        return DataTables::of($joiningData)

            ->editColumn('chkbox', function ($row) {
                return '
<div class="form-check form-check-sm form-check-custom form-check-solid">
  <input class="form-check-input" type="checkbox" value="1">
</div>
';
            })
            ->editColumn('Name', function ($row) {

                $bgTextDecore = $strVal = '';
                $str = trim(@$row->full_name);
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

                $flag = 0;
                $duplicate = 0;
                // $duplicate				=	$row->duplicate;
                $company_id = $this->user->roles[0]->company_id;
                $app_duplicate = $this->user->company->app_duplicate;
                $app_duplicate_days = $this->user->company->app_duplicate_days;

                $flag = 0;

                $profile_sidebar_css = 'show-application-detail';
                if ($row->thirdparty_company_id > 0) {
                    $profile_sidebar_css = 'client-show-application-detail';
                }

                return '
<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left"> <a href="javascript:void(0);"   data-row-id="'.@$row->wiseId.'" class="'.$profile_sidebar_css.'">
  <div class="symbol-label '.@$bgTextDecore.' border-radius-5">'.strtoupper(@$strVal).'</div>
  </a> </div>
<div class="d-flex flex-column"> <a href="javascript:void(0);"  data-row-id="'.@$row->wiseId.'"  class="text-dark fw-bolder text-hover-primary fs-6 '.$profile_sidebar_css.' " data-toggle="tooltip" title="'.ucwords(@$row->full_name).'">'.ucwords(substr(@$row->full_name, 0, 10)).'</a> <a href="javascript:void(0);"  data-row-id="'.@$row->wiseId.'"  class="text-muted fw-bold d-block fs-7 text-hover-primary '.$profile_sidebar_css.' " data-toggle="tooltip" title="'.ucwords(@$row->current_designation).'">'.ucwords(substr(@$row->current_designation, 0, 10)).'</a> </div>
';

            })

            ->editColumn('Contact', function ($row) {

                $bench_count = DB::table('rec_candidate_assign')
                    ->select('rec_candidate_assign.id')
                    ->join('bench_applications', 'bench_applications.id', '=', 'rec_candidate_assign.source_from_id')
                    ->where('rec_candidate_assign.source_application_id', '=', $row->id)
                    ->where('rec_candidate_assign.source_company_id', '=', auth()->user()->company_id)
                    ->where('rec_candidate_assign.source_from_type', '=', 2)
                    ->count();

                $bench_cnt_html = '';
                if ($bench_count > 0) {

                    $bench_row = DB::table('rec_candidate_assign')
                        ->select('rec_candidate_assign.id', 'bench_applications.id as bench_applications_id')
                        ->join('bench_applications', 'bench_applications.id', '=', 'rec_candidate_assign.source_from_id')
                        ->where('rec_candidate_assign.source_application_id', '=', $row->id)
                        ->where('rec_candidate_assign.source_company_id', '=', auth()->user()->company_id)
                        ->where('rec_candidate_assign.source_from_type', '=', 2)
                        ->first();

                    $bench_cnt = DB::table('bench_assign')->select('users.id')
                        ->join('users', 'users.id', '=', 'bench_assign.user_id')->where('bench_assign.bench_applications_id', '=', $bench_row->bench_applications_id)->count();

                    $bench_assign_row = DB::table('bench_assign')->select('users.id', 'users.name', 'users.email', 'users.mobile')
                        ->join('users', 'users.id', '=', 'bench_assign.user_id')
                        ->where('bench_assign.bench_applications_id', '=', $bench_row->bench_applications_id)->first();

                    $row->phone = @$bench_assign_row->mobile;
                    if (empty($row->phone)) {
                        $row->phone = 'xxx-xxx-xxxx';
                    }
                    $row->email = @$bench_assign_row->email;
                    if (empty($row->email)) {
                        $row->email = 'anonymous@email.com';
                    }

                    if ($bench_cnt > 1) {
                        $bench_cnt_html = '
<div data-row-id="'.$bench_row->bench_applications_id.'" class="symbol symbol-30 symbol-circle symbol-light bench_assign_listing" data-bs-placement="top" data-toggle="tooltip2" title="More users list.."> <span class="symbol-label font-weight-bold">'.$bench_cnt.'+</span> </div>
';
                    }

                }

                return '
<div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="'.$row->phone.'">'.@$row->phone.'</a> <a href="javascript:void(0);" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary" data-toggle="tooltip" title="'.$row->email.'">'.@$row->email.$bench_cnt_html.'</a> </div>
';

            })

            ->editColumn('Requisitions', function ($row) use ($company_id) {
                $icon = '&nbsp;';
                if ($row->thirdparty_company_id == $company_id) {
                    $icon = '&nbsp;';
                }

                return '
<div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">'.ucwords(substr($row->title, 0, 10)).'</a> <span class="text-muted fw-bold text-muted d-block fs-7">'.ucwords(substr($row->client_name, 0, 10)).$icon.'</span> </div>
';
            })
            ->editColumn('Source', function ($row) {

                $posted_by_image = '';
                $posted_by_image = url('public/avatar.png');
                if (! empty($row->posted_by_image)) {
                    $posted_by_image = url('public/user-uploads/team_profile_img/'.@$row->posted_by_image);
                }

                return '
<div class="d-flex flex-column">
<div class="symbol-group symbol-hover">
  <div class="symbol symbol-30 symbol-circle" data-toggle="tooltip" title="'.@$row->posted_by.'"> <img alt="image" src="'.@$posted_by_image.'"> </div>
</div>
<span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="" data-bs-original-title="'.@$row->uc_company_name.'">'.substr(@$row->uc_company_name, 0, 10).'</span> ';
            })

            ->editColumn('Offer', function ($row) use ($salaryMode, $currencySymbole) {

                $offer_currency = $offer_mode = $offer_amount = '';

                // if (!empty($row->offer_currency)) {
                // 	$offer_currency = $currency[$row->offer_currency];
                // }

                // $offer_mode = '';
                // if (!empty($row->offer_mode)) {
                // 	$offer_mode = $salaryMode[$row->offer_mode];
                // }

                $offer_currency = $currencySymbole[@$row->offer_currency];

                $offer_amount = GeneralMethod::thousandsCurrencyFormat($row->offer_amount);

                $offer_mode = $salaryMode[$row->offer_mode];

                return '<div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">'.$offer_currency.' '.@$offer_amount.'/'.$offer_mode.'</span> </div>';

            })
            ->editColumn('Joining', function ($row) {

                $joined_date = $joined_time = $date = '';
                $date = @$row->joined_date;
                if (! empty($date)) {
                    $joined_date = date('d-M-y', strtotime($date));
                    $joined_time = date('H:i a', strtotime($date));
                }

                return '
<div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">'.$joined_date.'</span> <span class="text-muted fw-bold d-block fs-7">'.$joined_time.'</span> </div>
';
            })
            ->editColumn('Stage', function ($row) {
                $name = '--------';
                if ($row->master_status_id == 8) {
                    $name = '';
                }
                if (! empty(@$row->child_status_id) && @$row->child_status_id > 0) {
                    $childStatus = '';
                    $childStatus = DB::table('candidate_status')->select('*')->where('id', $row->child_status_id)->first();
                    if (! empty($childStatus)) {
                        $name = @$childStatus->name;
                    }
                }

                return '
<div class="d-flex flex-column"> <a href="javascript:void(0);" data-row-id="'.@$row->id.'" class="d-flex flex-column" data-toggle="modal" "> <span class="badge fw-bolder fs-6 '.@$row->css_style.' px-2 py-1">'.ucwords(substr($row->name, 0, 10)).'</span> <span class="text-muted fw-bold text-muted d-block fs-7">'.@$name.'</span> </a> </div>
';
            })
            ->editColumn('Status', function ($row) use ($job_status_arr) {

                $status = $active = $hold = $inactive = $class = $recommendation_id = '';

                $job_status = $row->job_status;

                $status = ucwords($job_status);
                $class = '';
                // $class			=	$job_status_arr['active'];
                $class = $job_status_arr[$job_status];
                $recommendation_id = $row->recommendation_id;

                if ($status == 'Inactive') {
                    $status = 'Closed';
                }

                $final_status = '';
                if ($row->final_status == 2 && @$row->to_company_id == auth()->user()->company_id) {
                    $status = 'REMOVED';
                    $class = 'closed-bg';
                }

                $ulink = '<a href="javascript.void(0);" class="d-flex flex-column '.@$class.'  text-hover-success" >'.$status.'</a>';

                $recommendation_text = 'Custom';
                if ($recommendation_id > 0 && @$row->to_company_id == auth()->user()->company_id) {
                    $recommendation_text = 'Client';
                    $ulink = '<a href="javascript:void(0);" class="fs-6 '.@$class.' text-hover-success">'.$status.'</a>';
                }

                return '
<div class="d-flex flex-column"> '.$ulink.' <span class="fw-bold text-muted d-block fs-7">R'.@$row->job_id.' -'.$recommendation_text.'</span> </div>
';
            })

            ->rawColumns(['chkbox', 'Name', 'Contact', 'Requisitions', 'Status', 'Source', 'Offer', 'Joining', 'Stage'])

            ->addIndexColumn()
            ->make(true);
    }
}
