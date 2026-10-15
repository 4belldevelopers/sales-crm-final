<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Helper\Files;
use App\Helper\GeneralMethod;
use App\Helper\Reply;
use App\Http\Requests\Company\StoreCompany;
use App\Http\Requests\Company\UpdateCompany;
use App\Http\Requests\Company\UpdatePakage;
use App\MeetingComment;
use App\Models\AddJobWiseCondidate;
use App\Models\ApplicationStatus;
use App\Models\Client;
use App\Models\Comment;
use App\Models\Company;
use App\Models\CompanyPackage;
use App\Models\Country;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\JobApplicationAnswer;
use App\Models\JobCategory;
use App\Models\MeetingSchedule;
use App\Models\Package;
use App\Models\Role;
use App\Models\Ticket;
use App\Models\TicketStatus;
use App\Models\User;
use App\Enums\BenchSubmissionState;
use App\Enums\ClaimCapacity;
use App\Enums\MarketModState;
use App\Enums\MarketPubState;
use App\Services\AgencyCommercialTermsService;
use App\Services\ElasticService;
use App\Services\GoogleSheetServiceCandidate;
use App\Services\GoogleSheetsServiceGet;
use App\Services\MailService;
use App\Services\MarketplaceModerationService;
use Carbon\Carbon;
use Crypt;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class SuperAdminCompanyController extends SuperAdminBaseController
{
    protected $GoogleSheetServiceCandidate;

    protected $GoogleSheetsServiceGet;

    protected $mailService;

    protected $elasticService;

    public function __construct(GoogleSheetServiceCandidate $GoogleSheetServiceCandidate, GoogleSheetsServiceGet $GoogleSheetsServiceGet, MailService $mailService, ElasticService $elasticService)
    {
        parent::__construct();
        $this->pageTitle = 'menu.companies';
        $this->pageIcon = 'icon-film';
        $this->GoogleSheetServiceCandidate = $GoogleSheetServiceCandidate;
        $this->GoogleSheetsServiceGet = $GoogleSheetsServiceGet;
        $this->mailService = $mailService;
        $this->elasticService = $elasticService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function teamEdit($id)
    {

        $this->statelist = [];
        $this->team = User::join('companies', 'companies.id', '=', 'users.company_id')
            ->leftJoin('users_profile', 'users_profile.user_id', '=', 'users.id')
            ->select(
                'users.id', 'users.company_id', 'users.name', 'users.email', 'users.mobile', 'users.created_at', 'users.image', 'users.status', 'users.image',
                'companies.company_name', 'companies.website',
                'users_profile.country', 'users_profile.state', 'users_profile.city', 'users_profile.city_id', 'users_profile.designation', 'users_profile.skills'
            )
            ->where('users.id', '=', $id)
            ->first();

        $this->categories = JobCategory::all();
        $this->companyType = GeneralMethod::companyType();
        $this->department = GeneralMethod::department();
        $this->countryArr = GeneralMethod::getAllCountryList();
        if ($this->team->country > 0) {
            $this->statelist = GeneralMethod::statelist($this->team->country);
        }

        $this->cityListArr = '';
        if ($this->team->country == 101) {
            $statelist = DB::table('state_list')
                ->select('state_list.id', 'state_list.state as name', DB::raw('101 as country_id'))
                ->orderBy('state_list.state')
                ->get()->toArray();
            $this->statelist = json_decode(json_encode($statelist), true);

            $citylist = DB::table('city_list')
                ->select('city_list.city_id as id', 'city_list.city as name')
                ->where('city_list.state_id', $this->team->state)
                ->orderBy('city_list.city')
                ->get()->toArray();
            $this->cityListArr = json_decode(json_encode($citylist), true);

        }

        $this->cities = GeneralMethod::allCities();

        return view('super-admin.team.edit', $this->data);
    }

    public function index()
    {
        $must = [];
        $params = [
            'index' => ELASTIC_COMPANIES_INDEX,
            'body' => [
                'size' => 10000, // or a custom number
                'query' => [
                    'function_score' => [
                        'query' => [
                            'bool' => [
                                'must' => $must,
                            ],
                        ],
                        'score_mode' => 'sum',
                        'boost_mode' => 'sum',
                    ],
                ],
            ],
        ];

        if (! $this->elasticService->indexExists(ELASTIC_COMPANIES_INDEX)) {
            $total = 0;
            $data = [];
            $this->totalCompanies = 0;
            $this->activeCompanies = 0;
            $this->inactiveCompanies = 0;
        } else {
            $response = $this->elasticService->getClient()->search($params);
            $total = $response['hits']['total']['value'] ?? 0;
            $data = array_map(function ($hit) {
                return $hit['_source'];
            }, $response['hits']['hits']);
            $collection = collect($data);
            $this->totalCompanies = $collection->count();
            $this->activeCompanies = $collection->where('status', 'active')->count();
            $this->inactiveCompanies = $collection->where('status', 'inactive')->count();

        }

        // dd($this->data);
        return view('super-admin.company.index', $this->data);
    }

    public function superAdminAccess()
    {

        $this->totalCompanies = Company::withoutGlobalScope('company')->count();
        $this->activeCompanies = Company::withoutGlobalScope('company')->where('status', 'active')->count();
        $this->inactiveCompanies = Company::where('status', 'inactive')->count();

        return view('super-admin.company.super-admin-access', $this->data);
    }

    public function accessData()
    {
        // $categories = Company::with('package')->withoutGlobalScope('company')->get();
        // dd($categories->count());

        $company_id = auth()->user()->company_id;
        $department = GeneralMethod::department();
        $accountCompanyType = GeneralMethod::accountCompanyType();

        $clientAllDataList = DB::table('companies')
            ->select(
                'companies.id',
                'companies.company_name',
                'companies.created_at',
                'companies.industries',
                'companies.logo',
                'companies.company_type',
                'companies.package_type',
                'users.email',
                'users.id as userId',
                'users.status',
                // 'clients.id as client_id',
                'companies.licence_expire_on',
                // 'clients.tags',
                // 'clients.created_at',
                'td_industries.ind_name',
                // DB::raw("CASE WHEN staffing_company_relation.id >0 THEN staffing_company_relation.id ELSE 0 END AS staffing_company_relation_id"),
                // 'staffing_company_relation.from_company_id',
                'companies.created_at as to_created_at',
                'companies.created_at as from_created_at',
                // 'clients.name',
                // 'clients.logo_logical_path',
                // 'clients.industry',
                'packages.name as pck_name',
                'packages.is_trial',
                // 'clients.company_id as clients_company_id'
            )
            // ->leftJoin('staffing_company_relation', 'staffing_company_relation.from_company_id', '=', 'companies.id')
            ->join('users', 'users.company_id', '=', 'companies.id')
            ->join('packages', 'packages.id', '=', 'companies.package_id')
            ->leftJoin('td_industries', 'td_industries.ind_id', '=', 'companies.industries')
            ->where('users.root_user', '=', 1);
        // $clientAllDataList = $clientAllDataList->groupBy('companies.id');
        $clientAllDataList = $clientAllDataList->orderBy('companies.id', 'DESC');

        // dd($clientAllDataList->get());

        return DataTables::of($clientAllDataList)

            ->editColumn('chkbox', function ($row) {
                return '<div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input" type="checkbox" value="1">
                  </div>';
            })
            ->editColumn('Name', function ($row) {

                $bgTextDecore = $strVal = $image = '';
                $str = trim(@$row->company_name);
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

                $client_type_css = '';

                $departmentArr = '------';
                // if ($row->industries >= 0 && $row->industries !='') {
                //     $departmentArr = $department[($row->industries) ? @$row->industries : 1];
                // }

                $logo_path = url('public/avatar.png');
                if (! empty(@$row->logo) && strlen(@$row->logo) > 10) {
                    $logo_path = url('public/user-uploads/company-logo/'.@$row->logo);

                    $image = '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5 " style="float:left" >
														<img src="'.str_replace('index.php', '', @$logo_path).'" height="100px" width="100px" data-row-id="'.$row->id.'" class="'.$client_type_css.' border-radius-5   tttt"  />
													</div>';
                } else {
                    $image = '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left" >
														<a href="javascript:void(0);" data-row-id="'.$row->id.'" class="'.$client_type_css.' border-radius-5  ssss" id="kt_activities3_toggle" >
															<div class="symbol-label fs-3 '.@$bgTextDecore.'  border-radius-5">'.ucwords(@$strVal).'</div>
														</a>
													</div>';
                }

                return $image.'		              
                  <div class="d-flex flex-column">
                    <a href="javascript:void(0);" data-row-id="'.$row->id.'" class="text-dark fw-bolder text-hover-primary fs-6 '.$client_type_css.'"  data-toggle="tooltip1" data-html="true" title="'.ucwords(@$row->company_name).'">'.ucwords(substr(@$row->company_name, 0, 20)).'</a>
                    <a href="javascript:void(0);" data-row-id="'.$row->id.'" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary '.$client_type_css.'">'.@$row->ind_name.'</a>
                  </div>';
            })
            ->editColumn('Username', function ($row) {

                $locationHtml = '';

                return '<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">'.$row->email.'</a>
                  	<div class="d-flex align-items-center">
                  		<span class="fw-bold d-block fs-7">'.$locationHtml.'</span>
                  	<div>';
            })

            ->editColumn('Code', function ($row) {

                return '<div class="d-flex flex-column">
                      <span class="fw-bolder code_generator text-success fs-6" id="row_'.$row->userId.'" data-id="'.$row->userId.'" style="cursor: pointer;">Code <i class="fa fa-refresh"></i></span>
                    </div>
                </div></div>';
            })
            ->editColumn('Status', function ($row) {
                return '<div class="d-flex flex-column">
                <span class="fw-bolder text-'.($row->status == 'active' ? 'success' : 'danger').' fs-6">'
                .($row->status == 'active' ? 'Active' : 'Inactive').
                '</span>
            </div>';
            })

            ->rawColumns(['chkbox', 'Name', 'Username', 'Code', 'Status'])
            ->addIndexColumn()->make(true);

    }

    public function updateAccessCode(Request $request)
    {

        $row = DB::table('users')->where('id', '=', $request->id)->where('root_user', '=', 1)->first();

        $code = $row->company_id.rand(1111, 9999);

        // dd($row,$code);

        $result = DB::table('users')->where('id', '=', $request->id)->update(['super_admin_login_code' => $code]);

        if ($result) {
            return Reply::dataOnly(['status' => 'success', 'access_code' => $code]);

        }

        return Reply::dataOnly(['status' => 'faild', 'access_code' => $code]);

    }

    public function subscription()
    {

        $this->totalCompanies = Company::withoutGlobalScope('company')->count();
        $this->activeCompanies = Company::withoutGlobalScope('company')->where('status', 'active')->count();
        $this->inactiveCompanies = Company::where('status', 'inactive')->count();

        $must = [];

        $coinEventType = 'operational';
        $coinPath = 'company_coins';
        $coinAlias = 'company_coins';
        $filters = [];

        $params = [
            'index' => ELASTIC_COMPANIES_INDEX,
            'body' => [
                'size' => 0, // Only aggregation counts
                'query' => [
                    'function_score' => [
                        'query' => [
                            'bool' => [
                                'must' => [],
                                'filter' => [],
                            ],
                        ],
                        'score_mode' => 'sum',
                        'boost_mode' => 'sum',
                    ],
                ],
                'aggs' => [
                    'all_count' => [
                        'nested' => ['path' => $coinPath],
                        'aggs' => [
                            'filtered' => [
                                'filter' => [
                                    'term' => ["$coinAlias.event_type" => $coinEventType],
                                ],
                            ],
                        ],
                    ],
                    'free_count' => [
                        'nested' => ['path' => $coinPath],
                        'aggs' => [
                            'filtered' => [
                                'filter' => [
                                    'bool' => [
                                        'must' => [
                                            ['term' => ["$coinAlias.event_type" => $coinEventType]],
                                            ['term' => ["$coinAlias.package_type" => 2]],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'paid_count' => [
                        'nested' => ['path' => $coinPath],
                        'aggs' => [
                            'filtered' => [
                                'filter' => [
                                    'bool' => [
                                        'must' => [
                                            ['term' => ["$coinAlias.event_type" => $coinEventType]],
                                        ],
                                        'must_not' => [
                                            ['term' => ["$coinAlias.package_type" => 2]],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'expiry_in_30_days_count' => [
                        'nested' => ['path' => $coinPath],
                        'aggs' => [
                            'filtered' => [
                                'filter' => [
                                    'bool' => [
                                        'must' => [
                                            ['term' => ["$coinAlias.event_type" => $coinEventType]],
                                            ['range' => [
                                                "$coinAlias.end_date" => [
                                                    'gte' => 'now',
                                                    'lte' => 'now+30d',
                                                ],
                                            ]],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'less_coins_count' => [
                        'nested' => ['path' => $coinPath],
                        'aggs' => [
                            'filtered' => [
                                'filter' => [
                                    'bool' => [
                                        'must' => [
                                            ['term' => ["$coinAlias.event_type" => $coinEventType]],
                                            ['range' => [
                                                "$coinAlias.coins" => [
                                                    'lte' => 100,
                                                ],
                                            ]],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'last_active_7_days_count' => [
                        'filter' => [
                            'range' => [
                                'last_login' => [
                                    'lt' => 'now-7d/d',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        // Execute the query
        if (! $this->elasticService->indexExists(ELASTIC_COMPANIES_INDEX)) {
            $this->counts = (object) [
                'all_count' => 0,
                'free_count' => 0,
                'paid_count' => 0,
                'expiry_in_30_days_count' => 0,
                'less_coins_count' => 0,
                'last_active_7_days_count' => 0,
            ];
        } else {
            $response = $this->elasticService->getClient()->search($params);
            $aggs = $response['aggregations'];

            $this->counts = (object) [
                'all_count' => $aggs['all_count']['filtered']['doc_count'] ?? 0,
                'free_count' => $aggs['free_count']['filtered']['doc_count'] ?? 0,
                'paid_count' => $aggs['paid_count']['filtered']['doc_count'] ?? 0,
                'expiry_in_30_days_count' => $aggs['expiry_in_30_days_count']['filtered']['doc_count'] ?? 0,
                'less_coins_count' => $aggs['less_coins_count']['filtered']['doc_count'] ?? 0,
                'last_active_7_days_count' => $aggs['last_active_7_days_count']['doc_count'] ?? 0,
            ];
        }

        return view('super-admin.subscription.index', $this->data);
    }

    public function countaccountviewmore($id)
    {

        // dd($id);

        if ($id == 1) {
            $accounttype = 'operational';
            $this->counts = DB::table('companies')
                ->leftJoin('company_coins as sales_coins', function ($join) {
                    $join->on('sales_coins.company_id', '=', 'companies.id')
                        ->where('sales_coins.event_type', '=', 'sales');
                })
                ->leftJoin('company_coins as oper_coins', function ($join) {
                    $join->on('oper_coins.company_id', '=', 'companies.id')
                        ->where('oper_coins.event_type', '=', 'operational');
                })
                ->leftJoin('coin_orders as sales_orders', 'sales_orders.id', '=', 'sales_coins.coin_order_id') // For sales_coins
                ->leftJoin('coin_packages as sales_packages', 'sales_packages.id', '=', 'sales_orders.coin_package_id')
                ->leftJoin('coin_package_types as sales_package_types', 'sales_package_types.id', '=', 'sales_packages.coin_package_type_id')

            // Optional: Similar joins for oper_coins if necessary
                ->leftJoin('coin_orders as oper_orders', 'oper_orders.id', '=', 'oper_coins.coin_order_id') // For oper_coins
                ->leftJoin('coin_packages as oper_packages', 'oper_packages.id', '=', 'oper_orders.coin_package_id')
                ->leftJoin('coin_package_types as oper_package_types', 'oper_package_types.id', '=', 'oper_packages.coin_package_type_id')
                ->leftJoin('td_industries', 'td_industries.ind_id', '=', 'companies.industries')
                ->selectRaw('
                COUNT(CASE WHEN oper_packages.event_type = ? THEN companies.id ELSE NULL END) as all_count,
                SUM(CASE WHEN oper_packages.custome_pck = 2 AND oper_packages.event_type = ? THEN 1 ELSE 0 END) as free_count,
                SUM(CASE WHEN oper_packages.custome_pck != 2 AND oper_packages.event_type = ? THEN 1 ELSE 0 END) as paid_count,
                SUM(CASE WHEN oper_packages.event_type = ? AND oper_coins.end_date >= CURDATE() AND oper_coins.end_date <= DATE_ADD(CURDATE(), INTERVAL 30 DAY) THEN 1 ELSE 0 END) as expiry_in_30_days_count,
                SUM(CASE WHEN oper_packages.event_type = ? AND oper_coins.coins <= 100 THEN 1 ELSE 0 END) as less_coins_count,
                SUM(CASE WHEN oper_packages.event_type = ? AND companies.last_login < DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 ELSE 0 END) as last_active_7_days_count
            ', [$accounttype, $accounttype, $accounttype, $accounttype, $accounttype, $accounttype]) // Binding parameters
                ->first(); // Use `first()` to retrieve a single row with all the aggregated counts.
        } else {
            $accounttype = 'sales';
            $this->counts = DB::table('companies')
                ->leftJoin('company_coins as sales_coins', function ($join) {
                    $join->on('sales_coins.company_id', '=', 'companies.id')
                        ->where('sales_coins.event_type', '=', 'sales');
                })
                ->leftJoin('company_coins as oper_coins', function ($join) {
                    $join->on('oper_coins.company_id', '=', 'companies.id')
                        ->where('oper_coins.event_type', '=', 'operational');
                })
                ->leftJoin('coin_orders as sales_orders', 'sales_orders.id', '=', 'sales_coins.coin_order_id') // For sales_coins
                ->leftJoin('coin_packages as sales_packages', 'sales_packages.id', '=', 'sales_orders.coin_package_id')
                ->leftJoin('coin_package_types as sales_package_types', 'sales_package_types.id', '=', 'sales_packages.coin_package_type_id')

            // Optional: Similar joins for oper_coins if necessary
                ->leftJoin('coin_orders as oper_orders', 'oper_orders.id', '=', 'oper_coins.coin_order_id') // For oper_coins
                ->leftJoin('coin_packages as oper_packages', 'oper_packages.id', '=', 'oper_orders.coin_package_id')
                ->leftJoin('coin_package_types as oper_package_types', 'oper_package_types.id', '=', 'oper_packages.coin_package_type_id')
                ->leftJoin('td_industries', 'td_industries.ind_id', '=', 'companies.industries')
                ->selectRaw('
                COUNT(CASE WHEN sales_coins.event_type = ? THEN companies.id ELSE NULL END) as all_count,
                SUM(CASE WHEN sales_packages.custome_pck = 2 AND sales_coins.event_type = ? THEN 1 ELSE 0 END) as free_count,
                SUM(CASE WHEN sales_packages.custome_pck != 2 AND sales_coins.event_type = ? THEN 1 ELSE 0 END) as paid_count,
                SUM(CASE WHEN sales_coins.event_type = ? AND sales_coins.end_date >= CURDATE() AND sales_coins.end_date <= DATE_ADD(CURDATE(), INTERVAL 30 DAY) THEN 1 ELSE 0 END) as expiry_in_30_days_count,
                SUM(CASE WHEN sales_coins.event_type = ? AND sales_coins.coins <= 100 THEN 1 ELSE 0 END) as less_coins_count,
                SUM(CASE WHEN sales_coins.event_type = ? AND companies.last_login < DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 ELSE 0 END) as last_active_7_days_count
            ', [$accounttype, $accounttype, $accounttype, $accounttype, $accounttype, $accounttype]) // Binding parameters
                ->first(); // Use `first()` to retrieve a single row with all the aggregated counts.
        }

        // dd( $this->counts);
        return response()->json(['counts' => $this->counts]);
    }

    public function allSubscriptionDataAjax(Request $request)
    {

        // dd($request->mstatus_id);
        // $categories = Company::with('package')->withoutGlobalScope('company')->get();
        // dd($categories->count());

        $company_id = auth()->user()->company_id;
        $department = GeneralMethod::department();
        $accountCompanyType = GeneralMethod::accountCompanyType();
        $sevenDaysAgo = Carbon::now()->subDays(7);

        $searchQuery = $request->input('q');
        $page = max((int) $request->input('page', 1), 1);
        $size = (int) $request->input('size', 10); // Default 20 results per page
        $from = ($page - 1) * $size;

        $must = [];
        $custom_search = '';
        $coinEventType = $request->pck_id == 1 ? 'operational' : 'sales';
        $coinPath = 'company_coins';
        $coinAlias = 'company_coins';
        $filters = [];

        // Create base nested query
        $coinFilter = [
            'nested' => [
                'path' => $coinPath,
                'query' => [
                    'bool' => [
                        'must' => [
                            ['term' => ["$coinAlias.event_type" => $coinEventType]],
                        ],
                    ],
                ],
            ],
        ];

        // Apply condition based on mstatus_id
        switch ($request->mstatus_id) {
            case 3: // end_date in next 30 days
                $coinFilter['nested']['query']['bool']['must'][] = [
                    'range' => [
                        "$coinAlias.end_date" => [
                            'gte' => 'now',
                            'lte' => 'now+30d',
                        ],
                    ],
                ];
                break;

            case 4: // coins <= 100
                $coinFilter['nested']['query']['bool']['must'][] = [
                    'range' => [
                        "$coinAlias.coins" => ['lte' => 100],
                    ],
                ];
                break;

            case 1: // custome_pck = 2 (only if indexed)
                $coinFilter['nested']['query']['bool']['must'][] = [
                    'term' => ["$coinAlias.package_type" => 2],
                ];
                break;

            case 2: // custome_pck != 2
                $coinFilter['nested']['query']['bool']['must_not'][] = [
                    'term' => ["$coinAlias.package_type" => 2],
                ];
                break;

            case 5: // last_login older than 7 days
                $filters[] = [
                    'range' => [
                        'last_login' => ['lt' => 'now-7d/d'],
                    ],
                ];
                break;
        }

        // Always add company_coins event_type filter
        $filters[] = $coinFilter;

        if (! empty($custom_search)) {
            $must[] = [
                'bool' => [
                    'should' => [
                        // Fuzzy Match
                        [
                            'match' => [
                                'company_name' => [
                                    'query' => $custom_search,
                                    'fuzziness' => 'AUTO',
                                    'operator' => 'and',
                                ],
                            ],
                        ],
                        // Prefix Match
                        [
                            'match_phrase_prefix' => [
                                'company_name' => $custom_search,
                            ],
                        ],
                    ],
                    'minimum_should_match' => 1,
                ],
            ];
        }

        $params = [
            'index' => ELASTIC_COMPANIES_INDEX,
            'body' => [
                'from' => $from,
                'size' => $size,
                'sort' => [
                    ['_score' => ['order' => 'desc']],
                    ['leadCount' => ['order' => 'asc']],
                    ['id' => ['order' => 'desc']],
                ],
                'query' => [
                    'function_score' => [
                        'query' => [
                            'bool' => [
                                'must' => $must,
                                'filter' => $filters, // ✅ inject dynamic filters here
                            ],
                        ],
                        'score_mode' => 'sum',
                        'boost_mode' => 'sum',
                    ],
                ],
            ],
        ];

        if (! $this->elasticService->indexExists(ELASTIC_COMPANIES_INDEX)) {
            $total = 0;
            $data = [];
        } else {
            $response = $this->elasticService->getClient()->search($params);
            $total = $response['hits']['total']['value'] ?? 0;
            $data = array_map(function ($hit) {
                return $hit['_source'];
            }, $response['hits']['hits']);
        }

        $pagination = [
            'total' => $total,
            'current_page' => $page,
            'last_page' => ceil($total / $size),
            'listType' => 'allcandidate',
        ];

        $userPermissions = [];

        return response()->json([
            'html' => view('super-admin.ajax_pages.company_subscription_ajax', compact('data', 'userPermissions', 'coinEventType'))->render(),
            'total' => $total,
            'pagination' => view('super-admin.ajax_pages.pagination', compact('pagination'))->render(),
        ]);

    }

    public function subscriptiondata(Request $request)
    {

        // dd($request->mstatus_id);
        // $categories = Company::with('package')->withoutGlobalScope('company')->get();
        // dd($categories->count());

        $company_id = auth()->user()->company_id;
        $department = GeneralMethod::department();
        $accountCompanyType = GeneralMethod::accountCompanyType();
        $sevenDaysAgo = Carbon::now()->subDays(7);
        // dd($sevenDaysAgo);
        $clientAllDataList = DB::table('companies')
            ->select(
                'companies.id',
                'companies.company_name',
                'companies.last_login',
                'companies.created_at',
                'companies.industries',
                'companies.logo',
                'companies.company_type',
                'companies.package_type',
                'companies.licence_expire_on',
                'td_industries.ind_name',
                'companies.created_at as to_created_at',
                'companies.created_at as from_created_at',
                'oper_coins.coins as ope_coins',
                'sales_coins.coins as sal_coins',
                'oper_coins.actual_coins as ope_actual_coins',
                'sales_coins.actual_coins as sal_actual_coins',
                'oper_coins.end_date as oper_end_date',
                'sales_coins.end_date as sales_end_date',
                'sales_package_types.title as sales_package_name',
                'oper_package_types.title as oper_package_name'
            )
            ->leftJoin('company_coins as sales_coins', function ($join) {
                $join->on('sales_coins.company_id', '=', 'companies.id')
                    ->where('sales_coins.event_type', '=', 'sales');
            })
            ->leftJoin('company_coins as oper_coins', function ($join) {
                $join->on('oper_coins.company_id', '=', 'companies.id')
                    ->where('oper_coins.event_type', '=', 'operational');
            })
            ->leftJoin('coin_orders as sales_orders', 'sales_orders.id', '=', 'sales_coins.coin_order_id') // For sales_coins
            ->leftJoin('coin_packages as sales_packages', 'sales_packages.id', '=', 'sales_orders.coin_package_id')
            ->leftJoin('coin_package_types as sales_package_types', 'sales_package_types.id', '=', 'sales_packages.coin_package_type_id')

            // Optional: Similar joins for oper_coins if necessary
            ->leftJoin('coin_orders as oper_orders', 'oper_orders.id', '=', 'oper_coins.coin_order_id') // For oper_coins
            ->leftJoin('coin_packages as oper_packages', 'oper_packages.id', '=', 'oper_orders.coin_package_id')
            ->leftJoin('coin_package_types as oper_package_types', 'oper_package_types.id', '=', 'oper_packages.coin_package_type_id')

            ->leftJoin('td_industries', 'td_industries.ind_id', '=', 'companies.industries');

        if ($request->pck_id == 1) {
            if ($request->mstatus_id == 3) {
                $clientAllDataList = $clientAllDataList->where(function ($qry) {
                    $qry->where('oper_coins.end_date', '>=', DB::raw('CURDATE()'))
                        ->where('oper_coins.end_date', '<=', DB::raw('DATE_ADD(CURDATE(), INTERVAL 30 DAY)'))
                        ->where('oper_coins.event_type', '=', 'operational');
                });
            } elseif ($request->mstatus_id == 4) {
                $clientAllDataList = $clientAllDataList->where(function ($qry) {
                    $qry->where('oper_coins.coins', '<=', 100)
                        ->where('oper_coins.event_type', '=', 'operational');
                });
            } elseif ($request->mstatus_id == 1) {
                $clientAllDataList = $clientAllDataList->where(function ($qry) {
                    $qry->where('oper_packages.custome_pck', '=', 2)
                        ->where('oper_coins.event_type', '=', 'operational');
                });
            } elseif ($request->mstatus_id == 2) {
                $clientAllDataList = $clientAllDataList->where(function ($qry) {
                    $qry->where('oper_packages.custome_pck', '!=', 2)
                        ->where('oper_packages.event_type', '=', 'operational')
                        ->where('oper_coins.event_type', '=', 'operational');
                });
            } elseif ($request->mstatus_id == 5) {
                $clientAllDataList = $clientAllDataList->where(function ($qry) use ($sevenDaysAgo) {
                    $qry->where('companies.last_login', '<', $sevenDaysAgo)
                        ->where('oper_coins.event_type', '=', 'operational');
                });
            } else {
                $clientAllDataList = $clientAllDataList->where(function ($qry) {
                    $qry->where('oper_coins.event_type', '=', 'operational');
                });
            }
        } else {
            if ($request->mstatus_id == 3) {
                $clientAllDataList = $clientAllDataList->where(function ($qry) {
                    $qry->where('sales_coins.end_date', '>=', DB::raw('CURDATE()'))
                        ->where('sales_coins.end_date', '<=', DB::raw('DATE_ADD(CURDATE(), INTERVAL 30 DAY)'))
                        ->where('sales_coins.event_type', '=', 'sales');
                });
            } elseif ($request->mstatus_id == 4) {
                $clientAllDataList = $clientAllDataList->where(function ($qry) {
                    $qry->where('sales_coins.coins', '<=', 100)
                        ->where('sales_coins.event_type', '=', 'sales');
                });
            } elseif ($request->mstatus_id == 1) {
                $clientAllDataList = $clientAllDataList->where(function ($qry) {
                    $qry->where('sales_packages.custome_pck', '=', 2)
                        ->where('sales_coins.event_type', '=', 'sales');
                });
            } elseif ($request->mstatus_id == 2) {
                $clientAllDataList = $clientAllDataList->where(function ($qry) {
                    $qry->where('sales_packages.custome_pck', '!=', 2)
                        ->where('sales_coins.event_type', '=', 'sales');
                });
            } elseif ($request->mstatus_id == 5) {
                $clientAllDataList = $clientAllDataList->where(function ($qry) use ($sevenDaysAgo) {
                    $qry->where('companies.last_login', '<', $sevenDaysAgo)
                        ->where('sales_coins.event_type', '=', 'sales');
                });
            } else {
                $clientAllDataList = $clientAllDataList->where(function ($qry) {
                    $qry->where('sales_coins.event_type', '=', 'sales');
                });
            }
        }

        if ($request->pck_id == 1) {

            $clientAllDataList = $clientAllDataList->groupBy('oper_coins.company_id');
        } else {
            $clientAllDataList = $clientAllDataList->groupBy('sales_coins.company_id');
        }

        $clientAllDataList = $clientAllDataList->orderBy('companies.id', 'DESC');

        // dd($clientAllDataList->get());

        return DataTables::of($clientAllDataList)

            ->editColumn('chkbox', function ($row) {
                return '<div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input" type="checkbox" value="1">
                  </div>';
            })
            ->editColumn('Name', function ($row) {

                $bgTextDecore = $strVal = $image = '';
                $str = trim(@$row->company_name);
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

                $client_type_css = 'show-detail';

                $departmentArr = '------';
                // if ($row->industries >= 0 && $row->industries !='') {
                //     $departmentArr = $department[($row->industries) ? @$row->industries : 1];
                // }

                $company_type = '';
                if ($row->company_type == 1) {
                    $company_type = 'Staffing Company';
                } elseif ($row->company_type == 2) {
                    $company_type = 'Service Company';
                } elseif ($row->company_type == 3) {
                    $company_type = 'Product Company';
                }

                $logo_path = url('public/avatar.png');
                if (! empty(@$row->logo) && strlen(@$row->logo) > 10) {
                    $logo_path = url('public/user-uploads/company-logo/'.@$row->logo);

                    $image = '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5 " style="float:left" >
														<img src="'.str_replace('index.php', '', @$logo_path).'" height="100px" width="100px" data-row-id="'.$row->id.'" class="'.$client_type_css.' border-radius-5   tttt"  />
													</div>';
                } else {
                    $image = '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left" >
														<a href="javascript:void(0);" data-row-id="'.$row->id.'" class="'.$client_type_css.' border-radius-5  ssss" id="kt_activities3_toggle" >
															<div class="symbol-label fs-3 '.@$bgTextDecore.'  border-radius-5">'.ucwords(@$strVal).'</div>
														</a>
													</div>';
                }

                return $image.'		              
                  <div class="d-flex flex-column">
                    <a href="javascript:void(0);" data-row-id="'.$row->id.'" class="text-dark fw-bolder text-hover-primary fs-6 '.$client_type_css.'"  data-toggle="tooltip1" data-html="true" title="'.ucwords(@$row->company_name).'">'.ucwords(substr(@$row->company_name, 0, 20)).'</a>
                    <a href="javascript:void(0);" data-row-id="'.$row->id.'" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary '.$client_type_css.'">'.@$company_type.'</a>
                  </div>';
            })
            ->editColumn('Location', function ($row) {

                $company_id = $row->id;

                $locationHtml = '';
                $locInfo = DB::table('client_location')
                    ->select('client_location.id', 'client_location.city', 'client_location.address', 'client_location.postcode', 'client_location.country_id', 'city_list.city as city_name')
                    ->join('clients', 'clients.id', '=', 'client_location.client_id')
                    ->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')
                    ->where('clients.company_id', $company_id)
                    ->first();
                $locationHtml = '';

                $country_name = '';
                if (@$locInfo->country_id > 0) {
                    $country_name = GeneralMethod::getCountryName($locInfo->country_id);
                    foreach ($country_name as $cval) {
                        $country_name = ucwords($cval['name']);
                    }

                }

                $city = @$locInfo->city;
                if (@$locInfo->country_id == 101) {
                    $city = @$locInfo->city_name;
                }
                // $locationHtml = ucwords(substr(@$city, 0, 10)) . '&nbsp;' . @$country_name;
                $locationHtml = @$country_name;
                $address = '';
                $address = @$locInfo->address;

                return '<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">'.$city.'</a>
                  	<div class="d-flex align-items-center">
                  		<span class="fw-bold d-block fs-7">'.$locationHtml.'</span>
                  	<div>';
            })
            ->editColumn('Connected', function ($row) {
                $daysAgo = '';

                $created_at = $row->created_at;
                $newDate = date('d M, Y', strtotime($created_at));
                $daysAgo = GeneralMethod::timeAgo($row->from_created_at);

                return '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">'.@$newDate.'</a>
                    <a href="javascript:void(0);" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary">'.@$daysAgo.'</a>
                  </div>';
            })
            ->editColumn('OperationalPack', function ($row) {

                $oper_end_date = $row->oper_end_date;
                $oper_end_date = date('d M, Y', strtotime($oper_end_date));

                return '<div class="d-flex flex-column">
                <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">'.@$row->oper_package_name.'</a>
                <a href="javascript:void(0);" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary">'.@$oper_end_date.'</a>
              </div>';
            })
            ->editColumn('OpCoins', function ($row) {
                $ope_coins = @$row->ope_coins;
                $ope_actual_coins = @$row->ope_actual_coins;

                return '<div class="d-flex flex-column">
							
						<span class="text-muted fw-bold text-muted d-block fs-7">'.$ope_coins.'</span>
					</div>';

            })
            ->editColumn('SalesPack', function ($row) {

                $sales_end_date = $row->sales_end_date;
                $sales_end_date = date('d M, Y', strtotime($sales_end_date));

                return '<div class="d-flex flex-column">
                <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">'.@$row->sales_package_name.'</a>
                <a href="javascript:void(0);" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary">'.@$sales_end_date.' </a>
              </div>';

            })
            ->editColumn('SalesCoins', function ($row) {

                $sal_coins = @$row->sal_coins;
                $sal_actual_coins = @$row->sal_actual_coins;

                return '<div class="d-flex flex-column">
							
						<span class="text-muted fw-bold text-muted d-block fs-7">'.$sal_coins.'</span>
					</div>';

            })
            ->editColumn('LastActive', function ($row) {

                $last_login = $row->last_login;
                $last_login = date('d M, Y', strtotime($last_login));
                $daysAgo = GeneralMethod::timeAgo($row->last_login);

                return '<div class="d-flex flex-column">
                            <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">'.$last_login.'</a>
                            <span class="text-muted fw-bold text-muted d-block fs-7">'.ucwords(substr(@$row->ind_name, 0, 10)).'</span>
                    </div>';

            })

            ->rawColumns(['chkbox', 'Name', 'Location', 'OpCoins', 'Connected', 'SalesCoins', 'OperationalPack', 'LastActive', 'SalesPack'])
            ->addIndexColumn()->make(true);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('super-admin.company.create', $this->data);
    }

    /**
     * @return array
     */
    public function store(StoreCompany $request)
    {
        $setting = new Company;
        $setting->company_name = $request->input('company_name');
        $setting->career_page_link = str_slug($request->input('company_name'), '-');
        $setting->company_email = $request->input('company_email');
        $setting->company_phone = $request->input('company_phone');
        $setting->website = $request->input('website');
        $setting->address = $request->input('address');
        $setting->show_in_frontend = $request->input('show_in_frontend');
        $setting->job_opening_text = 'Welcome!';
        $setting->job_opening_title = 'We want people to thrive. We believe you do your best work when you feel your best.';
        $setting->featured_start_date = Carbon::parse($request->start_date)->format('Y-m-d');
        $setting->featured_end_date = Carbon::parse($request->end_date)->format('Y-m-d');
        $setting->featured = ($request->featured == 'yes') ? 1 : 0;
        $setting->timezone = 'Asia/Kolkata';
        if ($request->hasFile('logo')) {
            $setting->logo = Files::upload($request->logo, 'company-logo');
        }
        if (module_enabled('Subdomain')) {
            $setting->sub_domain = $request->sub_domain;
        }
        $setting->save();

        $user = new User;
        $user->company_id = $setting->id;
        $user->name = $request->full_name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->save();

        // assign admin role to default user
        $role = Role::where('company_id', $setting->id)->first();
        $user->roles()->attach($role->id);

        return Reply::redirect(route('superadmin.company.index'), __('menu.companies').' '.__('messages.createdSuccessfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $this->company = Company::withoutGlobalScope('company')->findOrFail($id);
        $this->companyPackage = CompanyPackage::activePackage($id);

        $company_id = $id;
        $this->account = DB::table('companies')->select('*')->where('id', $company_id)->first();
        $this->package_active = DB::table('package_active')->select('*')->where('company_id', $company_id)->first();

        $this->client = DB::table('clients')->select('clients.id', 'clients.tags')->where('clients.company_id', '=', $company_id)->where('clients.self_company', '=', 1)->first();

        // echo '<pre>'; print_r($this->company); exit;

        $this->client_location_count = DB::table('client_location')
            // ->join('countries', 'countries.id', '=', 'client_location.country_id')
            ->select('client_location.city', 'client_location.city_id', 'client_location.address', 'client_location.postcode', 'client_location.country_id')
            ->where('client_location.client_id', $this->client->id)
            ->orderBy('client_location.id', 'ASC')
            ->count();
        $this->client_location = DB::table('client_location')
            // ->join('countries', 'countries.id', '=', 'client_location.country_id')
            ->select('client_location.city', 'client_location.city_id', 'client_location.address', 'client_location.postcode', 'client_location.country_id')
            ->where('client_location.client_id', $this->client->id)
            ->orderBy('client_location.id', 'ASC')
            ->first();
        $this->company_city_name = '';
        $this->company_country_name = '';
        if ($this->client_location_count > 0) {
            if ($this->client_location->country_id == 101) {
                $this->city_obj = DB::table('city_list')->select('city_list.city')->where('city_list.city_id', $this->client_location->city_id)->first();
                $this->company_city_name = ucwords($this->city_obj->city);
            } else {
                $city = $this->client_location->city;
                $this->company_city_name = ucwords($city);
            }

            if ($this->client_location->country_id > 0) {
                $country_name = GeneralMethod::getCountryName($this->client_location->country_id);
                foreach ($country_name as $cval) {
                    $this->company_country_name = ucwords($cval['name']);
                }
            }
        }

        return view('super-admin.company.show', $this->data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->company = Company::withoutGlobalScope('company')->findOrFail($id);

        return view('super-admin.company.edit', $this->data);
    }

    /**
     * @return array
     */
    public function update(UpdateCompany $request, $id)
    {
        $setting = Company::withoutGlobalScope('company')->findOrFail($id);

        $setting->company_name = $request->input('company_name');
        $setting->company_email = $request->input('company_email');
        $setting->company_phone = $request->input('company_phone');
        $setting->website = $request->input('website');
        $setting->address = $request->input('address');
        $setting->show_in_frontend = $request->input('show_in_frontend');
        $setting->featured_start_date = Carbon::parse($request->start_date)->format('Y-m-d');
        $setting->featured_end_date = Carbon::parse($request->end_date)->format('Y-m-d');
        $setting->featured = ($request->featured == 'yes') ? 1 : 0;

        if (module_enabled('Subdomain')) {
            $setting->sub_domain = $request->sub_domain;
        }
        // update company's jobs status
        if ($setting->status != $request->input('status')) {
            Job::where('company_id', $id)->update(['status' => $request->status]);
        }

        $setting->status = $request->input('status');

        if ($request->hasFile('logo')) {
            $setting->logo = Files::upload($request->logo, 'company-logo');
        }

        $setting->save();

        return Reply::redirect(route('superadmin.company.index'), __('menu.companies').' '.__('messages.updatedSuccessfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $company = Company::withoutGlobalScope('company')->where('id', $id)->first();
        Files::deleteFile($company->logo, 'company-logo');
        $company->delete();

        return Reply::success(__('messages.recordDeleted'));
    }

    public function data_old()
    {
        $categories = Company::with('package')->withoutGlobalScope('company')->get();

        return DataTables::of($categories)
            ->addColumn('action', function ($row) {
                $action = '';

                $action .= '<a href="'.route('superadmin.company.show', [$row->id]).'" class="btn btn-dark btn-circle"
                    data-toggle="tooltip" data-original-title="'.__('app.view').'"><i class="fa fa-search" aria-hidden="true"></i></a>';

                $action .= ' <a href="'.route('superadmin.company.edit', [$row->id]).'" class="btn btn-primary btn-circle"
                    data-toggle="tooltip" data-original-title="'.__('app.edit').'"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
                if (module_enabled('Subdomain')) {
                    $action .= ' <a href="javascript:;" class="btn btn-success btn-circle domain-params"
                      data-toggle="tooltip" data-company-id="'.$row->id.'" data-company-url="'.request()->getScheme().'//'.$row->sub_domain.'" data-original-title="Domain Notify to company admins"><i class="fa fa-bell" aria-hidden="true"></i></a>';
                }
                $action .= ' <a href="javascript:;" class="btn btn-danger btn-circle sa-params"
                    data-toggle="tooltip" data-row-id="'.$row->id.'" data-original-title="'.__('app.delete').'"><i class="fa fa-times" aria-hidden="true"></i></a>';

                return $action;
            })
            ->editColumn('status', function ($row) {
                if ($row->status == 'active') {
                    $status = '<label class="badge bg-success">'.__('app.active').'</label>';

                    return $status;
                }
                if ($row->status == 'inactive') {
                    return '<label class="badge bg-danger">'.__('app.inactive').'</label>';
                }
            })

            ->editColumn('logo', function ($row) {
                return '<img src="'.$row->logo_url.'" class="img-responsive" height="25" />';
            })
            ->editColumn('company_name', function ($row) {
                return '<a href="'.route('superadmin.company.show', [$row->id]).'">'.ucfirst($row->company_name).'</a>';
            })
            ->editColumn('package_id', function ($row) {
                $package = ucfirst($row->package->name);

                if (! $row->package->is_trial) {
                    $package .= '<br><small>['.__('app.'.$row->package_type).']</small>';
                }

                if (! is_null($row->licence_expire_on) && $row->licence_expire_on->isPast()) {
                    $package .= '<br> <small class="text-danger">'.__('modules.subscription.packageExpired').'</small>';
                }

                return $package;
            })
            ->editColumn('sub_domain', function ($row) {
                return '<a href="http://'.$row->sub_domain.'" target="_blank">'.$row->sub_domain.'</a>';
            })
            ->addIndexColumn()
            ->rawColumns(['logo', 'action', 'status', 'package_id', 'company_name', 'sub_domain'])
            ->make(true);
    }

    public function dataold()
    {

        $categories = Company::with('package')->withoutGlobalScope('company')->get();

        return DataTables::of($categories)

            ->editColumn('chkbox', function ($row) {
                return '<div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input chk_list_item " name="chk_list_item[]" type="checkbox" value="'.$row->id.'" />
                  </div>';
            })

            ->editColumn('Companies', function ($row) {

                $bgTextDecore = $strVal = '';
                $str = trim(@$row->company_name);
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
                    
                      <div class="symbol-label '.$bgTextDecore.' border-radius-5">'.ucwords($strVal).'</div>
                    
                  </div>';

                /*if(!empty($row->logo_url)){
                                     $icon	= '<div class="symbol symbol-45px me-5"><img src="'.$row->logo_url.'" alt="Company Logo" class="border-radius-5 w-100"></div>';
                             }*/

                return ' <a href="'.route('superadmin.company.show', [$row->id]).'"> <div class="d-flex align-items-center">
                                                '.$icon.'
                                                <div class="d-flex justify-content-start flex-column">
                                                    <span class="text-dark fw-bolder text-hover-primary fs-6">'.ucfirst($row->company_name).'</span>
                                                    <span class="text-muted fw-bold text-muted d-block fs-7">'.$row->company_email.'</span>
                                                </div>
                                            </div>
											</a>';
            })
            ->editColumn('package_id', function ($row) {
                $package = ucfirst($row->package->name);

                if (! $row->package->is_trial) {
                    $package .= '<br><small>['.__('app.'.$row->package_type).']</small>';
                }

                if (! is_null($row->licence_expire_on) && $row->licence_expire_on->isPast()) {
                    $package .= '<br> <small class="text-danger">'.__('modules.subscription.packageExpired').'</small>';
                }

                return $package;
            })
            ->editColumn('status', function ($row) {
                if ($row->status == 'active') {
                    $status = '<label class="badge text-success text-dark-800 fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>';

                    return $status;
                }
                if ($row->status == 'inactive') {
                    return '<label class="badge text-danger text-dark-800 fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>';
                }
            })
            ->addColumn('action', function ($row) {
                $action = '';

                $action .= '<a href="'.route('superadmin.company.show', [$row->id]).'" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1"
                    data-toggle="tooltip" data-original-title="'.__('app.view').'"><span class="svg-icon svg-icon-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path d="M17.5 11H6.5C4 11 2 9 2 6.5C2 4 4 2 6.5 2H17.5C20 2 22 4 22 6.5C22 9 20 11 17.5 11ZM15 6.5C15 7.9 16.1 9 17.5 9C18.9 9 20 7.9 20 6.5C20 5.1 18.9 4 17.5 4C16.1 4 15 5.1 15 6.5Z" fill="currentColor"></path>
                    <path opacity="0.3" d="M17.5 22H6.5C4 22 2 20 2 17.5C2 15 4 13 6.5 13H17.5C20 13 22 15 22 17.5C22 20 20 22 17.5 22ZM4 17.5C4 18.9 5.1 20 6.5 20C7.9 20 9 18.9 9 17.5C9 16.1 7.9 15 6.5 15C5.1 15 4 16.1 4 17.5Z" fill="currentColor"></path>
                    </svg>
                    </span></a>';

                $action .= ' <a href="'.route('superadmin.company.edit', [$row->id]).'" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1"
                    data-toggle="tooltip" data-original-title="'.__('app.edit').'">
					<span class="svg-icon svg-icon-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path opacity="0.3" d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z" fill="currentColor"></path>
                    <path d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z" fill="currentColor"></path>
                    </svg>
                    </span>
					</a>';

                $action .= ' <a href="javascript:;" class="btn1 btn-danger1 btn-circle1 sa-params btn btn-icon btn-bg-light btn-active-color-primary btn-sm"
                    data-toggle="tooltip" data-row-id="'.$row->id.'" data-original-title="'.__('app.delete').'">
					<span class="svg-icon svg-icon-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path d="M5 9C5 8.44772 5.44772 8 6 8H18C18.5523 8 19 8.44772 19 9V18C19 19.6569 17.6569 21 16 21H8C6.34315 21 5 19.6569 5 18V9Z" fill="currentColor"></path>
                    <path opacity="0.5" d="M5 5C5 4.44772 5.44772 4 6 4H18C18.5523 4 19 4.44772 19 5V5C19 5.55228 18.5523 6 18 6H6C5.44772 6 5 5.55228 5 5V5Z" fill="currentColor"></path>
                    <path opacity="0.5" d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V4H9V4Z" fill="currentColor"></path>
                    </svg>
                    </span> 
					</a>';

                return $action;
            })
            ->editColumn('logo', function ($row) {
                return '<img src="'.$row->logo_url.'" class="img-responsive" height="25" />';
            })
            ->editColumn('company_name', function ($row) {
                return '<a href="'.route('superadmin.company.show', [$row->id]).'">'.ucfirst($row->company_name).'</a>';
            })

            ->addIndexColumn()
            ->rawColumns(['chkbox', 'Companies', 'logo', 'company_name', 'package_id', 'status', 'action'])
            ->make(true);
    }

    public function data_ajax(Request $request)
    {

        // $categories = Company::with('package')->withoutGlobalScope('company')->get();
        // dd($categories->count());

        $company_id = auth()->user()->company_id;
        $department = GeneralMethod::department();
        $accountCompanyType = GeneralMethod::accountCompanyType();
        $status = request('sortingfilter');
        $searchTerms = request('search');
        $custom_search = $searchTerms; // json_decode($searchTerms, true);

        $clientAllDataList = DB::table('companies')
            ->select(
                'companies.id',
                'companies.company_name',
                'companies.created_at',
                'companies.industries',
                'companies.logo',
                'companies.company_type',
                'companies.package_type',
                // 'clients.id as client_id',
                'companies.licence_expire_on',
                // 'clients.tags',
                // 'clients.created_at',
                'td_industries.ind_name',
                // DB::raw("CASE WHEN staffing_company_relation.id >0 THEN staffing_company_relation.id ELSE 0 END AS staffing_company_relation_id"),
                // 'staffing_company_relation.from_company_id',
                'companies.created_at as to_created_at',
                'companies.created_at as from_created_at',
                // 'clients.name',
                // 'clients.logo_logical_path',
                // 'clients.industry',
                'packages.name as pck_name',
                'packages.is_trial',
                // 'clients.company_id as clients_company_id'
            )
            // ->leftJoin('staffing_company_relation', 'staffing_company_relation.from_company_id', '=', 'companies.id')
            // ->join('clients', 'clients.company_id', '=', 'companies.id')
            ->leftJoin('packages', 'packages.id', '=', 'companies.package_id')
            ->leftJoin('td_industries', 'td_industries.ind_id', '=', 'companies.industries');

        if (\request('search') != '') {
            $clientAllDataList->where(function ($q) use ($custom_search) {
                $q->where('companies.company_name', 'LIKE', "%{$custom_search}%");
            });
        }
        // $clientAllDataList = $clientAllDataList->groupBy('companies.id');
        $clientAllDataList = $clientAllDataList->orderBy('companies.id', 'DESC');

        $perPage = 10;
        $page = $request->get('page', 1);

        $data = $clientAllDataList->paginate($perPage);
        $total = $data->total();

        $searchQuery = $request->input('q');
        $page = max((int) $request->input('page', 1), 1);
        $size = (int) $request->input('size', 10); // Default 20 results per page
        $from = ($page - 1) * $size;

        $must = [];

        if (! empty($custom_search)) {
            $must[] = [
                'bool' => [
                    'should' => [
                        // Fuzzy Match
                        [
                            'match' => [
                                'company_name' => [
                                    'query' => $custom_search,
                                    'fuzziness' => 'AUTO',
                                    'operator' => 'and',
                                ],
                            ],
                        ],
                        // Prefix Match
                        [
                            'match_phrase_prefix' => [
                                'company_name' => $custom_search,
                            ],
                        ],
                    ],
                    'minimum_should_match' => 1,
                ],
            ];
        }

        // ✅ Status Filter (ONLY if provided)
        if ($status !== null && $status !== '') {
            $must[] = [
                'term' => [
                    'status' => $status,   // keyword value (string)
                ],
            ];
        }

        $params = [
            'index' => ELASTIC_COMPANIES_INDEX,
            'body' => [
                'from' => $from,
                'size' => $size,
                'sort' => [
                    ['last_login' => ['order' => 'desc', 'missing' => '_last']],
                    ['_score' => ['order' => 'desc']],
                    ['leadCount' => ['order' => 'asc']],
                    ['id' => ['order' => 'desc']],
                ],
                'query' => [
                    'function_score' => [
                        'query' => [
                            'bool' => [
                                'must' => $must,
                            ],
                        ],
                        'score_mode' => 'sum',
                        'boost_mode' => 'sum',
                    ],
                ],
            ],
        ];

        if (! $this->elasticService->indexExists(ELASTIC_COMPANIES_INDEX)) {
            $total = 0;
            $data = [];
        } else {
            $response = $this->elasticService->getClient()->search($params);
            $total = $response['hits']['total']['value'] ?? 0;
            $data = array_map(function ($hit) {
                return $hit['_source'];
            }, $response['hits']['hits']);
        }

        $pagination = [
            'total' => $total,
            'current_page' => $page,
            'last_page' => ceil($total / $size),
            'listType' => 'allcandidate',
        ];

        $userPermissions = [];

        return response()->json([
            'html' => view('super-admin.ajax_pages.company_list_ajax', compact('data', 'userPermissions', 'accountCompanyType'))->render(),
            'total' => $total,
            'pagination' => view('super-admin.ajax_pages.pagination', compact('pagination'))->render(),
        ]);

        return DataTables::of($clientAllDataList)

            ->editColumn('chkbox', function ($row) {})
            ->editColumn('Name', function ($row) {})
            ->editColumn('Location', function ($row) {})
            ->editColumn('Joining', function ($row) {})
            ->editColumn('Requisitions', function ($row) {})
            ->editColumn('Connected', function ($row) {})
            ->editColumn('Client', function ($row) use ($accountCompanyType) {

                $client_type = '';
                $company_name = '------------';

                // if ($company_id != $row->id) {
                //     $client_type = 'Direct Client';
                // }

                $company_type = @$row->company_type;
                if ($company_type >= 1) {
                    $company_name = $accountCompanyType[$company_type];
                }

                return '<div class="d-flex flex-column">
							<a href="javascript:void(0);" class="text-dark fw-bolder fs-6">'.$client_type.'</a>
						<span class="text-muted fw-bold text-muted d-block fs-7">'.str_replace('Company', '', $company_name).'</span>
					</div>';

            })
            ->editColumn('OperationalPack', function ($row) {})
            ->editColumn('SalesPack', function ($row) {})
            ->editColumn('SpartnerAll', function ($row) {})
            ->editColumn('Bench', function ($row) {})
            ->editColumn('JobReq', function ($row) {
                return '<div class="d-flex flex-column">
                <span class="fw-bolder text-success fs-6">FT:500/1000</span><span class="fw-bolder text-success fs-6">CT:500/1000</span>
              </div>
              <div class="d-flex flex-column">
              <span class="fw-bolder text-danger fs-6">IFT:500/1000</span><span class="fw-bolder text-danger fs-6">ICT:500/1000</span>
               </div>';
            })
            ->editColumn('BenchReq', function ($row) {
                return '<div class="d-flex flex-column">
                <span class="fw-bolder text-success fs-6">IND:500/1000</span>
              </div>
              <div class="d-flex flex-column">
              <span class="fw-bolder text-danger fs-6">ITL:500/1000</span>
            </div>
          </div></div>';
            })

            ->rawColumns(['chkbox', 'Name', 'Location', 'Joining', 'Requisitions', 'Connected', 'Client', 'OperationalPack', 'SalesPack', 'SpartnerAll', 'Bench', 'JobReq', 'BenchReq'])
            ->addIndexColumn()->make(true);

    }

    public function data()
    {

        // $categories = Company::with('package')->withoutGlobalScope('company')->get();
        // dd($categories->count());

        $company_id = auth()->user()->company_id;
        $department = GeneralMethod::department();
        $accountCompanyType = GeneralMethod::accountCompanyType();

        $clientAllDataList = DB::table('companies')
            ->select(
                'companies.id',
                'companies.company_name',
                'companies.created_at',
                'companies.industries',
                'companies.logo',
                'companies.company_type',
                'companies.package_type',
                // 'clients.id as client_id',
                'companies.licence_expire_on',
                // 'clients.tags',
                // 'clients.created_at',
                'td_industries.ind_name',
                // DB::raw("CASE WHEN staffing_company_relation.id >0 THEN staffing_company_relation.id ELSE 0 END AS staffing_company_relation_id"),
                // 'staffing_company_relation.from_company_id',
                'companies.created_at as to_created_at',
                'companies.created_at as from_created_at',
                // 'clients.name',
                // 'clients.logo_logical_path',
                // 'clients.industry',
                'packages.name as pck_name',
                'packages.is_trial',
                // 'clients.company_id as clients_company_id'
            )
            // ->leftJoin('staffing_company_relation', 'staffing_company_relation.from_company_id', '=', 'companies.id')
            // ->join('clients', 'clients.company_id', '=', 'companies.id')
            ->leftJoin('packages', 'packages.id', '=', 'companies.package_id')
            ->leftJoin('td_industries', 'td_industries.ind_id', '=', 'companies.industries');
        // $clientAllDataList = $clientAllDataList->groupBy('companies.id');
        $clientAllDataList = $clientAllDataList->orderBy('companies.id', 'DESC');

        // dd($clientAllDataList->get());

        return DataTables::of($clientAllDataList)

            ->editColumn('chkbox', function ($row) {
                return '<div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input" type="checkbox" value="1">
                  </div>';
            })
            ->editColumn('Name', function ($row) {

                $bgTextDecore = $strVal = $image = '';
                $str = trim(@$row->company_name);
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

                $client_type_css = 'show-detail';

                $departmentArr = '------';
                // if ($row->industries >= 0 && $row->industries !='') {
                //     $departmentArr = $department[($row->industries) ? @$row->industries : 1];
                // }

                $logo_path = url('public/avatar.png');
                if (! empty(@$row->logo) && strlen(@$row->logo) > 10) {
                    $logo_path = url('public/user-uploads/company-logo/'.@$row->logo);

                    $image = '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5 " style="float:left" >
														<img src="'.str_replace('index.php', '', @$logo_path).'" height="100px" width="100px" data-row-id="'.$row->id.'" class="'.$client_type_css.' border-radius-5   tttt"  />
													</div>';
                } else {
                    $image = '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left" >
														<a href="javascript:void(0);" data-row-id="'.$row->id.'" class="'.$client_type_css.' border-radius-5  ssss" id="kt_activities3_toggle" >
															<div class="symbol-label fs-3 '.@$bgTextDecore.'  border-radius-5">'.ucwords(@$strVal).'</div>
														</a>
													</div>';
                }

                return $image.'		              
                  <div class="d-flex flex-column">
                    <a href="javascript:void(0);" data-row-id="'.$row->id.'" class="text-dark fw-bolder text-hover-primary fs-6 '.$client_type_css.'"  data-toggle="tooltip1" data-html="true" title="'.ucwords(@$row->company_name).'">'.ucwords(substr(@$row->company_name, 0, 20)).'</a>
                    <a href="javascript:void(0);" data-row-id="'.$row->id.'" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary '.$client_type_css.'">'.@$row->ind_name.'</a>
                  </div>';
            })
            ->editColumn('Location', function ($row) {

                $company_id = $row->id;

                $locationHtml = '';
                $locInfo = DB::table('client_location')
                    ->select('client_location.id', 'client_location.city', 'client_location.address', 'client_location.postcode', 'client_location.country_id', 'city_list.city as city_name')
                    ->join('clients', 'clients.id', '=', 'client_location.client_id')
                    ->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')
                    ->where('clients.company_id', $company_id)
                    ->first();
                $locationHtml = '';

                $country_name = '';
                if (@$locInfo->country_id > 0) {
                    $country_name = GeneralMethod::getCountryName($locInfo->country_id);
                    foreach ($country_name as $cval) {
                        $country_name = ucwords($cval['name']);
                    }

                }

                $city = @$locInfo->city;
                if (@$locInfo->country_id == 101) {
                    $city = @$locInfo->city_name;
                }
                // $locationHtml = ucwords(substr(@$city, 0, 10)) . '&nbsp;' . @$country_name;
                $locationHtml = @$country_name;
                $address = '';
                $address = @$locInfo->address;

                return '<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">'.$city.'</a>
                  	<div class="d-flex align-items-center">
                  		<span class="fw-bold d-block fs-7">'.$locationHtml.'</span>
                  	<div>';
            })
            ->editColumn('Joining', function ($row) use ($company_id) {
                $company_id = $row->id;
                $requisitionRow = DB::table('job_applications')
                    ->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id')
                    ->join('master_status', function ($join) {
                        $join->on('master_status.id', 'add_job_wise_condidates.master_status_id');
                    })
                    ->leftjoin('offer_job_applications', function ($join) {
                        $join->on('offer_job_applications.joined', 'add_job_wise_condidates.id');
                    })
                    ->join('jobs', function ($join) {
                        $join->on('jobs.id', 'add_job_wise_condidates.job_id');
                    })
                    ->select(
                        DB::raw('COUNT(add_job_wise_condidates.job_id) AS job_id_count'),
                        DB::raw('COUNT(DISTINCT add_job_wise_condidates.job_id) AS unique_job_id_count'),
                        DB::raw('COUNT(add_job_wise_condidates.job_application_id) AS job_application_id_count'),
                        DB::raw('COUNT(CASE WHEN add_job_wise_condidates.master_status_id != 8 THEN add_job_wise_condidates.job_application_id ELSE NULL END) AS all_application'),
                        DB::raw('COUNT(CASE WHEN add_job_wise_condidates.master_status_id = 6 THEN add_job_wise_condidates.job_application_id ELSE NULL END) AS join_application')
                    )
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
                    })
                    ->first();

                return '<div class="d-flex flex-column">
                <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-7">'.$requisitionRow->job_id_count.'</a>                    
                <span class="fw-bold d-block fs-7">'.$requisitionRow->join_application.'</span>
            </div>';

            })
            ->editColumn('Requisitions', function ($row) {

                $client_id = $clientCompanyId = '';
                $active_counter = $inactive_counter = $hold_counter = 0;
                $currentCompanyId = $row->id;
                $clientCompanyId = $row->id;

                if ($currentCompanyId == $row->id) {
                    $jobCounter = DB::table('jobs')
                        ->leftJoin('job_assign', function ($join) use ($clientCompanyId) {
                            $join->on('job_assign.job_id', '=', 'jobs.id');
                            $join->on('job_assign.company_id', '=', DB::raw("'".$clientCompanyId."'"));
                        })
                        ->leftJoin('recommendation_relation', 'recommendation_relation.job_id', '=', 'jobs.id')
                        ->select(
                            DB::raw("SUM(CASE WHEN jobs.status = 'active' THEN 1 ELSE 0 END) AS active"),
                            DB::raw("SUM(CASE WHEN jobs.status = 'inactive' AND jobs.company_id = {$clientCompanyId} THEN 1 ELSE 0 END) AS inactive"),
                            DB::raw("SUM(CASE WHEN jobs.status = 'hold' THEN 1 ELSE 0 END) AS hold")
                        )
                        ->where('jobs.company_id', '=', $clientCompanyId)
                        ->orWhere('recommendation_relation.to_company_id', '=', $currentCompanyId)
                        ->where('recommendation_relation.final_status', '=', 1)
                        ->first();
                } else {
                    $jobCounter = DB::table('jobs')
                        ->leftJoin('job_assign', function ($join) use ($clientCompanyId) {
                            $join->on('job_assign.job_id', '=', 'jobs.id');
                            $join->on('job_assign.company_id', '=', DB::raw("'".$clientCompanyId."'"));
                        })
                        ->leftJoin('recommendation_relation', 'recommendation_relation.job_id', '=', 'jobs.id')
                        ->select(
                            DB::raw("SUM(CASE WHEN jobs.status = 'active' THEN 1 ELSE 0 END) AS active"),
                            DB::raw("SUM(CASE WHEN jobs.status = 'inactive' AND jobs.company_id = {$clientCompanyId} THEN 1 ELSE 0 END) AS inactive"),
                            DB::raw("SUM(CASE WHEN jobs.status = 'hold' THEN 1 ELSE 0 END) AS hold")
                        )
                        ->where('jobs.company_id', '=', $clientCompanyId)
                        // ->where('recommendation_relation.from_company_id', '=', $clientCompanyId)
                        ->orWhere('recommendation_relation.to_company_id', '=', $currentCompanyId)
                        ->where('recommendation_relation.final_status', '=', 1)
                        ->first();
                }
                $jobCounter = GeneralMethod::dataCnt($clientCompanyId);

                $active_counter = $jobCounter['jobsActive'];
                $inactive_counter = $jobCounter['jobsClose'];
                $hold_counter = $jobCounter['jobsHold'];

                return '<div class="d-flex flex-column">
                      <span class="fw-bolder text-success fs-6">'.$active_counter.' Active</span>
                      <div class="d-flex align-items-center">
                        <span class="fw-bold d-block fs-7 text-warning">'.$hold_counter.' Hold</span>
                          &nbsp;|&nbsp;
                        <span class="fw-bold d-block fs-7 text-danger">'.$inactive_counter.' Closed</span>
                      <div>
                    </div>
                </div></div>';
            })
            ->editColumn('Connected', function ($row) {
                $daysAgo = '';

                $created_at = $row->created_at;
                $newDate = date('d M, Y', strtotime($created_at));
                $daysAgo = GeneralMethod::timeAgo($row->from_created_at);

                return '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">'.@$newDate.'</a>
                    <a href="javascript:void(0);" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary">'.@$daysAgo.'</a>
                  </div>';
            })
            ->editColumn('Client', function ($row) use ($accountCompanyType) {

                $client_type = '';
                $company_name = '------------';

                // if ($company_id != $row->id) {
                //     $client_type = 'Direct Client';
                // }

                $company_type = @$row->company_type;
                if ($company_type >= 1) {
                    $company_name = $accountCompanyType[$company_type];
                }

                return '<div class="d-flex flex-column">
							<a href="javascript:void(0);" class="text-dark fw-bolder fs-6">'.$client_type.'</a>
						<span class="text-muted fw-bold text-muted d-block fs-7">'.str_replace('Company', '', $company_name).'</span>
					</div>';

            })
            ->editColumn('OperationalPack', function ($row) {

                $company_coins = DB::table('company_coins')
                    ->join('coin_orders', 'coin_orders.id', '=', 'company_coins.coin_order_id')
                    ->join('coin_packages', 'coin_packages.id', '=', 'coin_orders.coin_package_id')
                    ->join('coin_package_types', 'coin_package_types.id', '=', 'coin_packages.coin_package_type_id')
                    ->where('company_coins.company_id', $row->id)
                    ->where('company_coins.event_type', 'operational')
                    ->where('coin_orders.status', 1)
                    ->select('company_coins.*', 'coin_package_types.title', 'coin_packages.custome_pck', 'coin_orders.id as coin_order_id', 'coin_orders.coins as order_coins')
                    ->first();

                // $contactHtml = '';
                // $contactHtml .= '<div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip2" data-html="true" title="c" >
                // 							<img alt="Pic" src="c">
                // 						</div>';
                // $contactHtml .= '</div>
                // 						</div>';

                return '<div class="d-flex flex-column">
                <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">'.@$company_coins->title.'</a>
                <a href="javascript:void(0);" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary">'.@$company_coins->coins.' Remaining Coins</a>
              </div>';
            })
            ->editColumn('SalesPack', function ($row) {

                $company_coins_sales = DB::table('company_coins')
                    ->join('coin_orders', 'coin_orders.id', '=', 'company_coins.coin_order_id')
                    ->join('coin_packages', 'coin_packages.id', '=', 'coin_orders.coin_package_id')
                    ->join('coin_package_types', 'coin_package_types.id', '=', 'coin_packages.coin_package_type_id')
                    ->where('company_coins.company_id', $row->id)
                    ->where('company_coins.event_type', 'sales')
                    ->where('coin_orders.status', 1)
                    ->select('company_coins.*', 'coin_package_types.title', 'coin_packages.custome_pck', 'coin_orders.id as coin_order_id', 'coin_orders.coins as order_coins')
                    ->first();

                // $company_coins_sales->start_date ? date("d-M-Y", strtotime(@$company_coins_sales->start_date)) : '';
                // $company_coins_sales->end_date ? date("d-M-Y", strtotime(@$company_coins_sales->end_date )): '';
                // $company_coins_sales->order_coins ;
                // $company_coins_sales->coins;

                // $package = str_replace("Package", "", ucfirst($row->pck_name));

                // if (!$row->is_trial) {
                //     $package .= '<br><small>[' . __('app.' . $row->package_type) . ']</small>';
                // }

                // if (!is_null($row->licence_expire_on) && $row->licence_expire_on->isPast()) {
                //     $package .= '<br> <small class="text-danger">' . __("modules.subscription.packageExpired") . '</small>';
                // }
                return '<div class="d-flex flex-column">
                <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">'.@$company_coins_sales->title.'</a>
                <a href="javascript:void(0);" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary">'.@$company_coins_sales->coins.' Remaining Coins</a>
              </div>';

            })
            ->editColumn('SpartnerAll', function ($row) {
                $spartnerAll = GeneralMethod::spartnerDataCnt($row->id);

                return '<span class="fw-bold d-block fs-7">'.$spartnerAll['spartnerAll'].'</span>
                </div>';
            })
            ->editColumn('Bench', function ($row) {

                $candidateCounter = GeneralMethod::benchCandidateCnt($row->id);

                $bench_counter = $candidateCounter['bench_counter'];
                $pool_counter = $candidateCounter['pool_counter'];
                $place_counter = $candidateCounter['place_counter'];

                return '<div class="d-flex flex-column">
                <span class="fw-bolder text-success fs-6">'.$bench_counter.' Bench</span>
                <div class="d-flex align-items-center">
                  <span class="fw-bold d-block fs-7 text-warning">'.$pool_counter.' Pool</span>
                    &nbsp;|&nbsp;
                  <span class="fw-bold d-block fs-7 text-danger">'.$place_counter.' Place</span>
                <div>
              </div>
          </div></div>';
            })
            ->editColumn('JobReq', function ($row) {
                return '<div class="d-flex flex-column">
                <span class="fw-bolder text-success fs-6">FT:500/1000</span><span class="fw-bolder text-success fs-6">CT:500/1000</span>
              </div>
              <div class="d-flex flex-column">
              <span class="fw-bolder text-danger fs-6">IFT:500/1000</span><span class="fw-bolder text-danger fs-6">ICT:500/1000</span>
               </div>';
            })
            ->editColumn('BenchReq', function ($row) {
                return '<div class="d-flex flex-column">
                <span class="fw-bolder text-success fs-6">IND:500/1000</span>
              </div>
              <div class="d-flex flex-column">
              <span class="fw-bolder text-danger fs-6">ITL:500/1000</span>
            </div>
          </div></div>';
            })

            ->rawColumns(['chkbox', 'Name', 'Location', 'Joining', 'Requisitions', 'Connected', 'Client', 'OperationalPack', 'SalesPack', 'SpartnerAll', 'Bench', 'JobReq', 'BenchReq'])
            ->addIndexColumn()->make(true);

    }

    public function jdlist($status)
    {
        // dd($status);
        $this->totalCompanies = Company::withoutGlobalScope('company')->count();
        $this->activeCompanies = Company::withoutGlobalScope('company')->where('status', 'active')->count();
        $this->inactiveCompanies = Company::where('status', 'inactive')->count();
        $this->jd_status = $status;

        return view('super-admin.jd.index', $this->data);
    }

    public function jdlistdata(Request $request)
    {

        // $categories = Company::with('package')->withoutGlobalScope('company')->get();
        // dd($categories->count());
        // jd_status
        $jd_status = '';
        if (@$request->jd_status === 'pending') {
            $jd_status = 0;
        } elseif (@$request->jd_status === 'approved') {
            $jd_status = 1;
        } elseif (@$request->jd_status === 'decline') {
            $jd_status = 2;
        }

        // dd($request->jd_status);

        $company_id = auth()->user()->company_id;
        $department = GeneralMethod::department();
        $accountCompanyType = GeneralMethod::accountCompanyType();

        $clientAllDataList = DB::table('jd_details')
            ->select('jd_details.*')
            ->where('status', '=', $jd_status);
        // $clientAllDataList = $clientAllDataList->groupBy('companies.id');
        $clientAllDataList = $clientAllDataList->orderBy('jd_details.id', 'DESC');

        // dd($clientAllDataList->get());

        return DataTables::of($clientAllDataList)

            ->editColumn('chkbox', function ($row) {
                return '<div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input" type="checkbox" value="1">
                  </div>';
            })
            ->editColumn('title', function ($row) {

                $bgTextDecore = $strVal = $image = '';
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

                $client_type_css = '';
                // if ($row->industries >= 0 && $row->industries !='') {
                //     $departmentArr = $department[($row->industries) ? @$row->industries : 1];
                // }

                $image = '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left" >
														<a href="javascript:void(0);" data-row-id="'.$row->id.'" class="'.$client_type_css.' border-radius-5  ssss" id="kt_activities3_toggle" >
															<div class="symbol-label fs-3 '.@$bgTextDecore.'  border-radius-5">'.ucwords(@$strVal).'</div>
														</a>
													</div>';

                return $image.'		              
                  <div class="d-flex flex-column">
                    <a href="javascript:void(0);" data-row-id="'.$row->id.'" class="text-dark fw-bolder text-hover-primary fs-6 '.$client_type_css.'"  data-toggle="tooltip1" data-html="true" title="'.ucwords(@$row->title).'">'.ucwords(substr(@$row->title, 0, 100)).'</a>
                  </div>';
            })
            ->editColumn('experience', function ($row) {

                return '<div class="d-flex flex-column">
                <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-7">'.$row->minimum_exp.'+</a>                    
                <span class="fw-bold d-block fs-7">'.$row->maxmum_exp.'+</span>
              </div>';
            })
            ->editColumn('skills', function ($row) {

                return '<div class="d-flex flex-column">
                <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-7">'.$row->skills.'</a>                    
            </div>';

            })
            ->editColumn('jd', function ($row) {

                return '<div class="d-flex flex-column">
                      <span class="fw-bolder text-success fs-6">'.$row->jd.' Active</span>
                    </div>
                </div></div>';
            })
            ->addColumn('action', function ($row) {
                $action = '';
                $action = ' <a href="'.route('superadmin.company.createjd', [encrypt(1), encrypt($row->id)]).'" class="btn btn-primary btn-circle"
                    data-toggle="tooltip" data-original-title="'.__('app.edit').'"><i class="fa fa-edit" aria-hidden="true"></i></a>';

                return $action;
            })
            ->rawColumns(['chkbox', 'title', 'experience', 'skills', 'jd', 'action'])
            ->addIndexColumn()->make(true);

    }

    public function createjd($entryType, $id)
    {
        // dd($id);/

        $this->entryType = decrypt($entryType);
        // dd($this->entryType,decrypt($id));
        $this->tokenID = decrypt($id);
        $this->jd_details = DB::table('jd_details')
            ->where('id', '=', $this->tokenID)
            ->select('jd_details.*')
            ->first();
        // dd($this->jd_details);

        return view('super-admin.jd.create', $this->data);
    }

    public function jdgoogleSheetInsertdataInDatabase(Request $request)
    {

        $entryType = ($request->entryType);
        if ($entryType == 2) {
            $request->validate([
                'googlesheetid' => 'required',
            ]);
        } else {
            if ($request->tokenID > 0) {
                $request->validate([
                    'title' => [
                        'required',
                        Rule::unique('jd_details', 'title')->ignore($request->tokenID),
                    ],
                    'skills' => 'required',
                ]);
            } else {
                $request->validate([
                    'title' => [
                        'required',
                        Rule::unique('jd_details', 'title'),
                    ],
                    'skills' => 'required',
                ]);
            }

        }

        $done = 0;
        $pendind = 0;
        $duplicate = 0;
        $timeStamp = time();
        $company_id = '';
        if ($entryType == 2) {

            $range = 'Sheet1!A1:P1'; // Specify the range of data you want to fetch
            $values = $this->GoogleSheetsServiceGet->getSpreadsheetValues($range, $company_id, $request->googlesheetid);
            if (count($values) == 0) {
                return response()->json([
                    'message' => 'The spreadsheet ID is invalid or not accessible',
                    'status' => 'error',
                ]);
            }
            //  dd($values["Sheet1"]);

            //  dd(count($values));

            foreach ($values as $val) {
                for ($i = 1; $i < count($val); $i++) {
                    //  dd($val);
                    if (isset($val[$i][0]) && ! empty($val[$i][0]) &&
                        isset($val[$i][4]) && ! empty($val[$i][4])) {

                        $values = [
                            'title' => $val[$i][0],
                            'jd' => $val[$i][1],
                            'minimum_exp' => $val[$i][2],
                            'maxmum_exp' => $val[$i][3],
                            'skills' => $val[$i][4],
                        ];
                        // Check for duplicate title before inserting
                        $existingEntry = DB::table('jd_details')
                            ->where('title', $val[$i][0])
                            ->exists();

                        if (! $existingEntry) {
                            DB::table('jd_details')->insert($values);
                            $done++;
                        } else {
                            $duplicate++; // Duplicate found, increment pending counter
                        }

                    } else {
                        $pendind++;
                    }

                }

            }
        } else {
            $skills = '';
            if (! empty(@$request->skills)) {
                $tags = @$request->skills;
                $tags = json_decode($tags, true);
                $tagsArr = array_column($tags, 'value');
                $skills = implode(',', $tagsArr);
            }

            // dd($tagsArr,$request->skills);
            $values = [
                'title' => $request->title,
                'jd' => $request->jd_details,
                'minimum_exp' => $request->minimum_experience,
                'maxmum_exp' => $request->maximum_experience,
                'skills' => $skills,
                'status' => $request->jd_status,
            ];
            // dd( $values );
            if ($request->tokenID > 0) {
                DB::table('jd_details')->where('id', '=', $request->tokenID)->update($values);
            } else {
                DB::table('jd_details')->insert($values);
            }

            $done = 1;

        }

        return response()->json([
            'message' => '',
            'status' => 'success',
            'done' => $done,
            'duplicate' => $duplicate,
            'pendind' => $pendind,
        ]);

    }

    // jobApplication insert google sheet
    public function googleSheetCandidateData(Request $request)
    {

        // dd($request->company_id());

        $packageStatus = GeneralMethod::validatePackageRet('CVACCESS');

        $request->validate([
            'googlesheetid' => 'required',
            'job_id' => 'required',
            'teams' => 'required',
            'current_ctc_cur' => 'required',
            'current_ctc_mode' => 'required',
        ]);

        $range = 'Sheet1!A1:W1'; // Specify the range of data you want to fetch
        $values = $this->GoogleSheetServiceCandidate->getSpreadsheetValues($range, $request->company_id, $request->googlesheetid);
        // unset($values["Sheet1"][0]);
        if (count($values) == 0) {
            return response()->json([
                'message' => 'The spreadsheet ID is invalid or not accessible',
                'status' => 'error',
            ]);
        }

        // dd($values);
        $done = 0;
        $pendind = 0;
        foreach ($values as $val) {
            for ($i = 1; $i < count($val); $i++) {

                // if (isset($val[$i][0]) && !empty($val[$i][0]) &&
                // isset($val[$i][1]) && !empty($val[$i][1]) &&
                // isset($val[$i][2]) && !empty($val[$i][2]) &&
                // isset($val[$i][3]) && !empty($val[$i][3]) &&
                // isset($val[$i][5]) && !empty($val[$i][5]) &&
                // isset($val[$i][6]) && !empty($val[$i][6]) &&
                // isset($val[$i][18]) && !empty($val[$i][18]) &&
                // isset($val[$i][8]) && !empty($val[$i][8]) &&
                // isset($val[$i][9]) && !empty($val[$i][9]) &&
                // isset($val[$i][10]) && !empty($val[$i][10]) &&
                // isset($val[$i][12]) && !empty($val[$i][12]) &&
                // isset($val[$i][13]) && !empty($val[$i][13]) &&
                // isset($val[$i][14]) && !empty($val[$i][14]) &&
                // isset($val[$i][11]) && !empty($val[$i][11]) &&
                // isset($val[$i][19]) && !empty($val[$i][19]) &&
                // isset($val[$i][20]) && !empty($val[$i][20])) {
                // dd($values);

                $stateId = DB::table('state_list')->where('state', 'like', '%'.$val[$i][17].'%')->first();
                $city_list = DB::table('city_list')->where('city', 'like', '%'.$val[$i][18].'%')->first();
                $countries = DB::table('countries')->where('country_name', 'like', '%'.$val[$i][16].'%')->first();

                $qualification_master = DB::table('qualification_master')->where('name', 'like', '%'.$val[$i][8].'%')->first();

                $company_id = $request->company_id;
                $submitted_by = $request->teams;
                $applicationStatus = ApplicationStatus::first();
                $jobApplication = new JobApplication;
                $jobApplication->full_name = $val[$i][0];
                $jobApplication->job_id = $request->job_id;
                $jobApplication->status_id = 8;
                $jobApplication->email = $val[$i][6];
                $jobApplication->phone = $val[$i][3];
                $jobApplication->column_priority = 0;
                $jobApplication->total_exp = $val[$i][9];
                $jobApplication->rel_exp = $val[$i][10];

                $jobApplication->ctc = $val[$i][12];
                $jobApplication->current_ctc_cur = $request->current_ctc_cur;
                $jobApplication->current_ctc_mode = $request->current_ctc_mode;
                $jobApplication->ectc = $val[$i][13];
                $jobApplication->current_ectc_cur = $request->current_ctc_cur;
                $jobApplication->current_ectc_mode = $request->current_ctc_mode;

                $currency = GeneralMethod::currency();  // Currency code

                $currency = $currency[$request->current_ctc_cur];
                $econvertedAmount = $val[$i][13];
                // dd($val[$i][0],$econvertedAmount, $currency);
                if ($request->current_ctc_cur > 0) {
                    $econvertedAmount = GeneralMethod::convertToINR($econvertedAmount, $currency);
                }

                $ectcdivided = GeneralMethod::salaryDividedByMode($request->current_ctc_mode, $econvertedAmount);
                $jobApplication->ectc_yearly = @$ectcdivided->yearly;
                $jobApplication->ectc_monthly = @$ectcdivided->monthly;
                $jobApplication->ectc_hourly = @$ectcdivided->hourly;

                $convertedAmount = $val[$i][12];
                if ($request->current_ctc_cur > 0) {
                    $econvertedAmount = GeneralMethod::convertToINR($convertedAmount, $currency);
                }

                $ctcdivided = GeneralMethod::salaryDividedByMode($request->current_ctc_mode, $convertedAmount);
                $jobApplication->ctc_yearly = @$ctcdivided->yearly;
                $jobApplication->ctc_monthly = @$ctcdivided->monthly;
                $jobApplication->ctc_hourly = @$ctcdivided->hourly;

                $jobApplication->percentage_hike = $val[$i][14];
                $jobApplication->comment = $val[$i][15];
                $jobApplication->pincode = $val[$i][19];
                $jobApplication->current_designation = $val[$i][1];
                $jobApplication->current_company = $val[$i][2];

                $jobApplication->skills = $val[$i][20];

                $jobApplication->master_status_id = 8;
                $jobApplication->secondrary_email = $val[$i][7];
                $jobApplication->secondary_mobile = $val[$i][4];

                $jobApplication->submitted_by = $submitted_by;

                if (is_string($val[$i][11])) {
                    $jobApplication->serving_notice_period_mode = 0;
                } elseif (is_int($val[$i][11])) {
                    $jobApplication->serving_notice_period_mode = 2;
                } else {
                    $jobApplication->serving_notice_period_mode = 1;
                }
                $jobApplication->np_id = $val[$i][11];

                $jobApplication->qualification = ($qualification_master) ? $qualification_master->id : 0;
                $jobApplication->dob = date('Y-m-d', strtotime($val[$i][5]));
                $jobApplication->country = ($countries) ? $countries->id : 0;
                $jobApplication->state = ($stateId) ? $stateId->id : 0;
                $jobApplication->city_id = ($city_list) ? $city_list->city_id : 0;
                $jobApplication->city = '';
                $jobApplication->company_id = $company_id;
                $chkApplication = DB::table('job_applications')->select('job_applications.id')->where('company_id', $company_id)->where('email', $val[$i][6])->orWhere('phone', $val[$i][3])->count();
                if ($chkApplication > 0) {
                    $jobApplication->duplicate = 1;
                }
                // dd($countries->id,$qualification_master->id,$jobApplication);

                $jobApplication->save();

                $skillArr = explode(',', @$jobApplication->skills);
                $percentage = GeneralMethod::skill_match_percentage($request->job_id, $skillArr);

                $insertArr = ['job_id' => $request->job_id, 'job_application_id' => $jobApplication->id, 'status_id' => $jobApplication->status_id, 'master_status_id' => $jobApplication->master_status_id, 'child_status_id' => $jobApplication->child_status_id, 'company_id' => $company_id, 'submitted_by' => $submitted_by, 'skill_match' => $percentage];
                $add_job_wise_condidates = DB::table('add_job_wise_condidates')->insert($insertArr);
                $lastInsertedId = DB::getPdo()->lastInsertId();

                if (! empty($val[$i][20])) {
                    $skillArr = explode(',', $val[$i][20]);
                    //  dd($skillArr);
                    foreach ($skillArr as $sval) {
                        $values = [
                            'job_application_id' => $jobApplication->id,
                            'skills' => $sval,
                        ];
                        if (! DB::table('job_application_skills')->where('job_application_id', '=', $jobApplication->id)->where('skills', 'LIKE', '%'.$sval.'%')->exists()) {
                            DB::table('job_application_skills')->insert($values);
                        }
                    }
                }
                if (! DB::table('job_application_skills')->where('job_application_id', '=', $jobApplication->id)->where('skills', 'LIKE', '%'.$val[$i][1].'%')->exists()) {
                    DB::table('job_application_skills')->insert(['job_application_id' => $jobApplication->id, 'skills' => $val[$i][1]]);
                }

                $timelineArr = [
                    'event_id' => 1,
                    'event_by_company_id' => $company_id,
                    'event_by_user_id' => $submitted_by,
                    'master_process_name' => 'InReview',
                    // 'child_process_name'	=>

                    'application_id' => $jobApplication->id,
                    'application_email' => $val[$i][6],
                    'job_id' => @$request->job_id,
                    'note' => 'Successfully Added',
                ];
                GeneralMethod::UpdateTimeline($timelineArr);
                $done++;

                // }else{
                //     $pendind++;

                // }

            }

        }

        return response()->json([
            'message' => '',
            'status' => 'success',
            'done' => $done,
            'pendind' => $pendind,
        ]);

    }

    // Client insert google sheet
    public function googleSheetInsertdataInDatabase(Request $request)
    {

        $request->validate([
            'googlesheetid' => 'required',
            'teams' => 'required',
        ]);

        $done = 0;
        $pendind = 0;
        $company_id = '';
        $timeStamp = time();
        $names = $request->name;
        $company_id = $request->company_id;

        $range = 'Sheet1!A1:P1'; // Specify the range of data you want to fetch
        $values = $this->GoogleSheetsServiceGet->getSpreadsheetValues($range, $company_id, $request->googlesheetid);
        if (count($values) == 0) {
            return response()->json([
                'message' => 'The spreadsheet ID is invalid or not accessible',
                'status' => 'error',
            ]);
        }
        //  unset($values["Sheet1"][0]);

        //  dd(count($values));

        foreach ($values as $val) {
            for ($i = 1; $i < count($val); $i++) {

                if (isset($val[$i][0]) && ! empty($val[$i][0]) &&
                    isset($val[$i][1]) && ! empty($val[$i][1]) &&
                    isset($val[$i][2]) && ! empty($val[$i][2]) &&
                    isset($val[$i][3]) && ! empty($val[$i][3]) &&
                    isset($val[$i][4]) && ! empty($val[$i][4]) &&
                    isset($val[$i][5]) && ! empty($val[$i][5]) &&
                    isset($val[$i][6]) && ! empty($val[$i][6]) &&
                    isset($val[$i][7]) && ! empty($val[$i][7]) &&
                    isset($val[$i][8]) && ! empty($val[$i][8]) &&
                    isset($val[$i][9]) && ! empty($val[$i][9]) &&
                    isset($val[$i][10]) && ! empty($val[$i][10]) &&
                    isset($val[$i][11]) && ! empty($val[$i][11]) &&
                    isset($val[$i][12]) && ! empty($val[$i][12]) &&
                    isset($val[$i][13]) && ! empty($val[$i][13]) &&
                    isset($val[$i][14]) && ! empty($val[$i][14]) &&
                    isset($val[$i][16]) && ! empty($val[$i][16])) {

                    $stateId = DB::table('state_list')->where('state', 'like', '%'.$val[$i][10].'%')->first();
                    $city_list = DB::table('city_list')->where('city', 'like', '%'.$val[$i][11].'%')->first();
                    $countries = DB::table('countries')->where('country_name', 'like', '%'.$val[$i][9].'%')->first();

                    $td_industries = DB::table('td_industries')->where('ind_name', 'like', '%'.$val[$i][2].'%')->first();

                    $client = new Client;
                    $client->company_id = @$company_id;
                    $client->name = $val[$i][0];
                    $client->website = $val[$i][1];
                    $client->industry = ($td_industries) ? $td_industries->ind_id : 0; // $val[$i][2];
                    $client->company_sizes = $val[$i][3];
                    $client->company_type = 0; // $val[$i][4];
                    $client->founded = $val[$i][5];
                    $client->description = $val[$i][6];
                    $client->tags = $val[$i][7];
                    $client->save();
                    $client_id = DB::getPdo()->lastInsertId();

                    $values = [
                        'client_id' => $client_id,
                        'country_id' => ($countries) ? $countries->id : 0,
                        'city' => '',
                        'city_id' => ($city_list) ? $city_list->city_id : 0,
                        'address' => $val[$i][8],
                        'postcode' => $val[$i][12],
                        'state' => ($stateId) ? $stateId->id : 0,
                    ];
                    DB::table('client_location')->insert($values);

                    $values = [
                        'client_id' => $client_id,
                        'contact_person_name' => $val[$i][13],
                        'contact_person_email' => $val[$i][14],
                        'contact_person_number' => $val[$i][15],
                        'contact_person_designation' => $val[$i][16],
                    ];
                    DB::table('client_contact_info')->insert($values);
                    $done++;

                } else {
                    $pendind++;
                }

            }

        }

        return response()->json([
            'message' => '',
            'status' => 'success',
            'done' => $done,
            'pendind' => $pendind,
        ]);

    }

    public function SingleNotificationUpdateS(Request $request)
    {

        $values = [
            'status' => $request->chkStatus,
        ];
        // dd($values);
        DB::table('email_notifications')->where('id', '=', $request->id)->update($values);

        return response()->json([
            'message' => '',
            'status' => 'success',
        ]);

    }

    public function googleSheetsServicecheckCandidate($spreadsheetId)
    {

        $isAccessible = $this->GoogleSheetServiceCandidate->checkSpreadsheetAccess($spreadsheetId);

        if ($isAccessible) {
            return false;
        } else {
            return true;
        }
    }

    public function googleSheetsServicecheckClient($spreadsheetId)
    {

        $isAccessible = $this->GoogleSheetsServiceGet->checkSpreadsheetAccess($spreadsheetId);

        if ($isAccessible) {
            return false;
        } else {
            return true;
        }
    }

    public function viewMore($id)
    {

        $company_id = $id;

        // abort_if(! $this->user->cans('view_clients'), 403);
        // dd("dkfdj");
        $this->countries = Country::all();
        $this->categories = JobCategory::all();
        $this->companyType = GeneralMethod::companyType();
        $this->department = GeneralMethod::department();
        $this->countryArr = GeneralMethod::getAllCountryList();
        $this->statelist = GeneralMethod::statelist(101);

        $this->client = Company::withoutGlobalScopes()->find($id);
        // dd(auth()->user()->getRoleNames());
        // $this->client = DB::table('companies')->where('id','=',$id)->first();

        // dd($this->client,$id);
        //    $dsts = ($this->client->clients()->where('self_company', 1)->get());
        //    dd($dsts[0]->tags);

        // dd($this->client,$id);
        $this->client_file = DB::table('client_file')->select('*')->where('client_id', $id)->get();
        // $this->countries 		= Country::all();

        $this->client_location = DB::table('client_location')
            // ->join('countries', 'countries.id', '=', 'client_location.country_id')
            ->join('clients', 'clients.id', '=', 'client_location.client_id')
            ->select('client_location.city', 'client_location.city_id', 'client_location.address', 'client_location.postcode', 'client_location.country_id')
            ->where('clients.company_id', $id)
            ->orderBy('client_location.id', 'DESC')
            ->first();

        $this->city_name = '';
        if (@$this->client_location->country_id == 101) {
            $this->city_obj = DB::table('city_list')->select('city_list.city')->where('city_list.city_id', $this->client_location->city_id)->first();
            $this->city_name = ucwords($this->city_obj->city);
        } else {
            $city = @$this->client_location->city;
            $this->city_name = ucwords($city);
        }

        $this->country_name = '';
        if (@$this->client_location->country_id > 0) {
            $country_name = GeneralMethod::getCountryName($this->client_location->country_id);
            foreach ($country_name as $cval) {
                $this->country_name = ucwords($cval['name']);
            }
        }
        $this->MeetingSchedule = MeetingSchedule::join('companies as from_com', 'from_com.id', '=', 'meeting_schedules.from_company_id')->
        join('companies as to_com', 'to_com.id', '=', 'meeting_schedules.to_company_id')->
        join('interview_mode', 'interview_mode.id', '=', 'meeting_schedules.interview_mode')->
        join('users', 'users.id', '=', 'meeting_schedules.created_by')->
        leftJoin('interview_mode_lavel', 'interview_mode_lavel.id', '=', 'meeting_schedules.interview_mode_level')->
        select('meeting_schedules.*', 'interview_mode.name as mode_name', 'interview_mode_lavel.name as sub_mode_name', 'from_com.company_name as from_com_name', 'to_com.company_name as to_com_name', 'users.image', 'users.name')->
        get();
        // dd( $this->MeetingSchedule);

        $this->client_contact_info = DB::table('client_contact_info')->select('*')->where('client_id', $id)->take(1)->get();
        $this->active_requisition = DB::table('jobs')->select('jobs.id')->where('jobs.company_id', $id)->where('jobs.status', 'active')->count();

        $this->company = Company::withoutGlobalScope('company')->findOrFail($id);
        $this->companyPackage = CompanyPackage::activePackage($id);

        $company_id = $id;
        $this->account = DB::table('companies')->select('*')->where('id', $company_id)->first();

        $this->users = DB::table('users')->where('company_id', '=', $id)->first();

        $this->company_coins = DB::table('company_coins')
            ->join('coin_orders', 'coin_orders.id', '=', 'company_coins.coin_order_id')
            ->join('coin_packages', 'coin_packages.id', '=', 'coin_orders.coin_package_id')
            ->join('coin_package_types', 'coin_package_types.id', '=', 'coin_packages.coin_package_type_id')
            ->where('company_coins.company_id', $company_id)
            ->where('coin_orders.status', 1)
            ->select('company_coins.*', 'coin_package_types.title', 'coin_packages.custome_pck')
            ->get()
            ->map(function ($coinOrder) {
                $coinOrder->coin_order_events = DB::table('coin_order_events')
                    ->join('coin_events', 'coin_events.id', '=', 'coin_order_events.coin_event_id')
                    ->where('coin_order_id', $coinOrder->id)
                    ->select('coin_order_events.*', 'coin_events.title')
                    ->get()
                    ->toArray();

                return (array) $coinOrder;
            })
            ->toArray();

        $this->company_coins = DB::table('company_coins')
            ->join('coin_orders', 'coin_orders.id', '=', 'company_coins.coin_order_id')
            ->join('coin_packages', 'coin_packages.id', '=', 'coin_orders.coin_package_id')
            ->join('coin_package_types', 'coin_package_types.id', '=', 'coin_packages.coin_package_type_id')
            ->where('company_coins.company_id', $company_id)
            ->where('company_coins.event_type', 'operational')
            ->where('coin_orders.status', 1)
            ->select('company_coins.*', 'coin_package_types.title', 'coin_packages.custome_pck', 'coin_orders.id as coin_order_id', 'coin_orders.coins as order_coins')
            ->get()
            ->map(function ($coinOrder) {
                $coinOrder->coin_order_events = DB::table('coin_order_events')
                    ->join('coin_events', 'coin_events.id', '=', 'coin_order_events.coin_event_id')
                    ->where('coin_order_id', $coinOrder->coin_order_id)
                    ->select('coin_order_events.*', 'coin_events.title')
                    ->get()
                    ->toArray();

                return (array) $coinOrder;
            })->toArray();

        $this->company_coins_sales = DB::table('company_coins')
            ->join('coin_orders', 'coin_orders.id', '=', 'company_coins.coin_order_id')
            ->join('coin_packages', 'coin_packages.id', '=', 'coin_orders.coin_package_id')
            ->join('coin_package_types', 'coin_package_types.id', '=', 'coin_packages.coin_package_type_id')
            ->where('company_coins.company_id', $company_id)
            ->where('company_coins.event_type', 'sales')
            ->where('coin_orders.status', 1)
            ->select('company_coins.*', 'coin_package_types.title', 'coin_packages.custome_pck', 'coin_orders.id as coin_order_id', 'coin_orders.coins as order_coins')
            ->get()
            ->map(function ($coinOrder) {
                $coinOrder->coin_order_events = DB::table('coin_order_events')
                    ->join('coin_events', 'coin_events.id', '=', 'coin_order_events.coin_event_id')
                    ->where('coin_order_id', $coinOrder->coin_order_id)
                    ->select('coin_order_events.*', 'coin_events.title')
                    ->get()
                    ->toArray();

                return (array) $coinOrder;
            })->toArray();

        // dd($company_coins);

        $this->package_active_all = DB::table('package_active')->join('package_invoice', 'package_invoice.id', '=', 'package_active.package_invoice_id')->join('package_order_items', 'package_order_items.invoice_id', '=', 'package_invoice.id')->join('package_features', 'package_features.id', '=', 'package_order_items.feature_id')->select('package_active.*', 'package_features.title', 'package_features.field_name', 'package_features.id as gg_is')->where('package_active.company_id', $company_id)->get();

        $this->package_active_details = DB::table('package_active')->join('package_invoice', 'package_invoice.id', '=', 'package_active.package_invoice_id')->join('packages', 'packages.id', '=', 'package_invoice.package_id')->select('package_active.*', 'packages.name')->where('package_active.company_id', $company_id)->first();

        // dd($this->package_active_all);

        $this->client_com = DB::table('clients')->select('clients.id', 'clients.tags')->where('clients.company_id', '=', $company_id)->where('clients.self_company', '=', 1)->first();

        // dd( $this->client);

        $this->client_location_count = DB::table('client_location')
            // ->join('countries', 'countries.id', '=', 'client_location.country_id')
            ->select('client_location.city', 'client_location.city_id', 'client_location.address', 'client_location.postcode', 'client_location.country_id')
            ->where('client_location.client_id', @$this->client_com->id)
            ->orderBy('client_location.id', 'ASC')
            ->count();
        $this->client_location = DB::table('client_location')
            // ->join('countries', 'countries.id', '=', 'client_location.country_id')
            ->select('client_location.city', 'client_location.city_id', 'client_location.address', 'client_location.postcode', 'client_location.country_id')
            ->where('client_location.client_id', @$this->client_com->id)
            ->orderBy('client_location.id', 'ASC')
            ->first();
        $this->company_city_name = '';
        $this->company_country_name = '';
        if ($this->client_location_count > 0) {
            if ($this->client_location->country_id == 101) {
                $this->city_obj = DB::table('city_list')->select('city_list.city')->where('city_list.city_id', $this->client_location->city_id)->first();
                $this->company_city_name = ucwords($this->city_obj->city);
            } else {
                $city = $this->client_location->city;
                $this->company_city_name = ucwords($city);
            }

            if ($this->client_location->country_id > 0) {
                $country_name = GeneralMethod::getCountryName($this->client_location->country_id);
                foreach ($country_name as $cval) {
                    $this->company_country_name = ucwords($cval['name']);
                }
            }
        }
        $company_type = '';
        $this->myPackageHistory = DB::table('package_invoice')->select('package_invoice.*', 'packages.name')->join('packages', 'packages.id', '=', 'package_invoice.package_id')->where('package_invoice.company_id', '=', $company_id)->orderBy('package_invoice.id', 'DESC')->get();
        $companyRow = DB::table('companies')->select('*')->where('id', $company_id)->first();
        $company_type = $companyRow->company_type;

        $this->top_up_coin_packages = DB::table('top_up_coin_packages')->where('status', '=', 1)->get();

        $this->link_payments = DB::table('link_payments')->select('*')->where('status', '=', 0)->where('subscription_type', '=', 'no')->where('company_id', $company_id)->orderByDesc('id')->first();

        $this->coin_transection_logs_sales = DB::table('coin_transection_logs')
            ->join('users', 'users.id', '=', 'coin_transection_logs.event_by')
            ->leftJoin('companies', 'companies.id', '=', 'users.company_id')
            ->select('coin_transection_logs.*', 'users.name', 'companies.company_name')
            ->orderByDesc('coin_transection_logs.id')
            ->where('coin_transection_logs.event_type', '=', 'sales')
            ->where('coin_transection_logs.coins', '>', 0)
            ->where(function ($query) use ($company_id) {
                $query->where('coin_transection_logs.company_id', $company_id);
                //   ->orWhere('coin_transection_logs.from_company_id', $company_id);
            })->orderByDesc('id')->take(10)->get();

        // dd($this->coin_transection_logs_sales);

        $this->coin_transection_logs = DB::table('coin_transection_logs')
            ->join('users', 'users.id', '=', 'coin_transection_logs.event_by')
            ->leftJoin('companies', 'companies.id', '=', 'users.company_id')
            ->select('coin_transection_logs.*', 'users.name', 'companies.company_name')
            ->orderByDesc('coin_transection_logs.id')
            ->where('coin_transection_logs.event_type', '=', 'operational')
            ->where('coin_transection_logs.coins', '>', 0)
            ->where(function ($query) use ($company_id) {
                $query->where('coin_transection_logs.company_id', $company_id);
                //   ->orWhere('coin_transection_logs.from_company_id', $company_id);
            })->orderByDesc('id')->take(10)->get();

        $this->cart_transaction_history = DB::table('cart_transaction_history')->select('cart_transaction_history.*')->where('cart_transaction_history.company_id', '=', $company_id)->orderByDesc('id')->take(10)->get();

        $this->clientlist = Client::select('*')->where('company_id', $company_id)->orderBy('clients.name', 'ASC')->get();
        //   dd($company_id);

        $clients = DB::table('clients')->leftJoin('companies', 'companies.id', '=', 'clients.company_id')
            ->select('clients.id', 'companies.company_name', 'users.name', 'users.id as userId', 'users.email')
            ->leftJoin('users', 'users.company_id', '=', 'companies.id')
            ->where('clients.company_id', '=', $company_id)
            ->where('clients.self_company', '=', 1)
            ->first();
        // dd($clients);

        $locInfo = DB::table('client_location')
            ->leftJoin('countries', 'countries.id', '=', 'client_location.country_id')
            ->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')
            ->leftJoin('state_list', 'state_list.id', '=', 'client_location.state')
            ->select(
                'client_location.id',
                'client_location.city',
                'client_location.city_id',
                'client_location.state as state_id',
                'client_location.address',
                'client_location.postcode',
                'client_location.country_id',
                'city_list.city as city_name',
                'state_list.state as state_name',
                'countries.country_name'
            )
            ->where('client_id', $clients->id)
            ->first();

        // dd( $locInfo);
        $this->countrie_id = ($locInfo) ? $locInfo->country_id : '';
        $this->city_id = ($locInfo) ? $locInfo->city_id : '';
        $this->state_id = ($locInfo) ? $locInfo->state_id : '';

        $this->phone = auth()->user()->mobile;
        $this->building = '';
        $this->postal_code = '';
        $this->gstin = '';
        $this->pan_no = '';

        $this->company_id = $company_id;
        // dd($this->countrie_id);

        $this->company_name = @$clients->company_name;
        $this->user_name = @$clients->name;
        $nameParts = explode(' ', $this->user_name);
        $this->firstName = $nameParts[0];
        $this->lastName = isset($nameParts[1]) ? $nameParts[1] : '';

        $this->locArr = '';
        if ($locInfo) {
            $city_name = @$locInfo->city;

            $this->locArr = ucwords(@$locInfo->address).' '.ucwords($city_name).' , '.ucwords(@$locInfo->state_name).' , '.ucwords(@$locInfo->country_name);
        }

        $this->countries = DB::table('countries')
            ->select('countries.*')
            ->get();

        $billing_address_companies = DB::table('billing_address_companies')->where('company_id', '=', $company_id)->first();

        // dd($billing_address_companies);

        if ($billing_address_companies) {
            $this->countrie_id = ($billing_address_companies) ? $billing_address_companies->country_id : '';
            $this->city_id = ($billing_address_companies) ? $billing_address_companies->city : '';
            $this->state_id = ($billing_address_companies) ? $billing_address_companies->state_id : '';
            $this->locArr = ($billing_address_companies) ? $billing_address_companies->street : '';
            $this->phone = ($billing_address_companies) ? $billing_address_companies->phone : '';
            $this->building = ($billing_address_companies) ? $billing_address_companies->building : '';
            $this->firstName = ($billing_address_companies) ? $billing_address_companies->first_name : '';
            $this->lastName = ($billing_address_companies) ? $billing_address_companies->last_name : '';
            $this->postal_code = ($billing_address_companies) ? $billing_address_companies->postal_code : '';
            $this->gstin = ($billing_address_companies) ? $billing_address_companies->gstin : '';
            $this->pan_no = ($billing_address_companies) ? $billing_address_companies->pan_no : '';
            $this->user_name = ($billing_address_companies) ? $billing_address_companies->first_name.' '.$billing_address_companies->last_name : '';

        }
        $this->boostkeywords = '';
        $boostkeywords = DB::table('boostkeywords')->where('company_id', $company_id)->pluck('skill_name')->toArray();
        if (count($boostkeywords) > 0) {
            $this->boostkeywords = implode(',', $boostkeywords);
        }

        // dd($this->data);
        $view = view('super-admin.company.viewmore', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'view' => $view]);
    }

    public function billinDetails(Request $request)
    {

        $company_id = $request->id;

        $clients = DB::table('clients')->leftJoin('companies', 'companies.id', '=', 'clients.company_id')
            ->select('clients.id', 'companies.company_name', 'users.name')
            ->leftJoin('users', 'users.company_id', '=', 'companies.id')
            ->where('clients.company_id', '=', $company_id)
            ->where('clients.self_company', '=', 1)
            ->first();

        $locInfo = DB::table('client_location')
            ->leftJoin('countries', 'countries.id', '=', 'client_location.country_id')
            ->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')
            ->leftJoin('state_list', 'state_list.id', '=', 'client_location.state')
            ->select(
                'client_location.id',
                'client_location.city',
                'client_location.city_id',
                'client_location.state as state_id',
                'client_location.address',
                'client_location.postcode',
                'client_location.country_id',
                'city_list.city as city_name',
                'state_list.state as state_name',
                'countries.country_name'
            )
            ->where('client_id', $clients->id)
            ->first();

        // dd( $locInfo);
        $this->countrie_id = ($locInfo) ? $locInfo->country_id : '';
        $this->city_id = ($locInfo) ? $locInfo->city_id : '';
        $this->state_id = ($locInfo) ? $locInfo->state_id : '';

        $this->phone = auth()->user()->mobile;
        $this->building = '';
        $this->postal_code = '';
        $this->gstin = '';
        $this->pan_no = '';

        $this->company_id = $company_id;
        // dd($this->countrie_id);

        $this->company_name = @$clients->company_name;
        $this->user_name = @$clients->name;
        $nameParts = explode(' ', $this->user_name);
        $this->firstName = $nameParts[0];
        $this->lastName = isset($nameParts[1]) ? $nameParts[1] : '';

        $this->locArr = '';
        if ($locInfo) {
            $city_name = @$locInfo->city;

            $this->locArr = ucwords(@$locInfo->address).' '.ucwords($city_name).' , '.ucwords(@$locInfo->state_name).' , '.ucwords(@$locInfo->country_name);
        }

        $this->countries = DB::table('countries')
            ->select('countries.*')
            ->get();

        $billing_address_companies = DB::table('billing_address_companies')->where('company_id', '=', $company_id)->first();

        // dd( $billing_address_companies);

        if ($billing_address_companies) {
            $this->countrie_id = ($billing_address_companies) ? $billing_address_companies->country_id : '';
            $this->city_id = ($billing_address_companies) ? $billing_address_companies->city : '';
            $this->state_id = ($billing_address_companies) ? $billing_address_companies->state_id : '';
            $this->locArr = ($billing_address_companies) ? $billing_address_companies->street : '';
            $this->phone = ($billing_address_companies) ? $billing_address_companies->phone : '';
            $this->building = ($billing_address_companies) ? $billing_address_companies->building : '';
            $this->firstName = ($billing_address_companies) ? $billing_address_companies->first_name : '';
            $this->lastName = ($billing_address_companies) ? $billing_address_companies->last_name : '';
            $this->postal_code = ($billing_address_companies) ? $billing_address_companies->postal_code : '';
            $this->gstin = ($billing_address_companies) ? $billing_address_companies->gstin : '';
            $this->pan_no = ($billing_address_companies) ? $billing_address_companies->pan_no : '';
            $this->user_name = ($billing_address_companies) ? $billing_address_companies->first_name.' '.$billing_address_companies->last_name : '';

        }

        $view = view('super-admin.company.billingDetail', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'view' => $view, 'countrie_id' => $this->countrie_id, 'state_id' => $this->state_id, 'city_id' => $this->city_id]);

    }

    public function hiring_manager($id)
    {

        $this->client_id = $id;

        $this->hiring_manager = DB::table('client_contact_info')
            ->select('client_contact_info.id', 'client_contact_info.client_id', 'client_contact_info.contact_person_name', 'client_contact_info.contact_person_email', 'client_contact_info.contact_person_number', 'client_contact_info.contact_person_designation')
            ->where('client_contact_info.client_id', $this->client_id)
            ->orderBy('client_contact_info.contact_person_name', 'ASC')
            ->get()->toArray();

        return Reply::dataOnly(['status' => 'success', 'hiring_manager' => $this->hiring_manager]);

    }

    // Job insert google sheet
    public function googleSheetCandidateDataJob(Request $request)
    {

        // dd($request->all());

        $packageStatus = GeneralMethod::validatePackageRet('CVACCESS');

        $request->validate([
            'googlesheetid' => 'required',
            'client_id' => 'required',
            'hiring_namager' => 'required',
            'recruiter' => 'required',
        ]);

        $range = 'Sheet1!A1:Q1'; // Specify the range of data you want to fetch
        $values = $this->GoogleSheetServiceCandidate->getSpreadsheetValues($range, $request->company_id, $request->googlesheetid);
        // unset($values["Sheet1"][0]);
        if (count($values) == 0) {
            return response()->json([
                'message' => 'The spreadsheet ID is invalid or not accessible',
                'status' => 'error',
            ]);
        }

        // dd($values);
        $done = 0;
        $pendind = 0;
        foreach ($values as $val) {
            for ($i = 1; $i < count($val); $i++) {

                // dd($val[$i][0]);

                if (isset($val[$i][0]) && ! empty($val[$i][0]) &&
                isset($val[$i][1]) && ! empty($val[$i][1]) &&
                isset($val[$i][2]) && ! empty($val[$i][2]) &&
                isset($val[$i][3]) && ! empty($val[$i][3]) &&
                isset($val[$i][4]) && ! empty($val[$i][4]) &&
                isset($val[$i][5]) && ! empty($val[$i][5]) &&
                isset($val[$i][6]) && ! empty($val[$i][6]) &&
                isset($val[$i][7]) && ! empty($val[$i][7]) &&
                isset($val[$i][8]) && ! empty($val[$i][8]) &&
                isset($val[$i][9]) && ! empty($val[$i][9]) &&
                isset($val[$i][10]) && ! empty($val[$i][10]) &&
                isset($val[$i][11]) && ! empty($val[$i][11]) &&
                isset($val[$i][12]) && ! empty($val[$i][12]) &&
                isset($val[$i][14]) && ! empty($val[$i][14]) &&
                isset($val[$i][18]) && ! empty($val[$i][18]) &&
                isset($val[$i][13]) && ! empty($val[$i][13])) {
                    // dd($values);

                    // $stateId = DB::table('state_list')->where('state', 'like', '%'.$val[$i][17].'%')->first();
                    // $city_list = DB::table('city_list')->where('city', 'like', '%'.$val[$i][18].'%')->first();
                    // $countries = DB::table('countries')->where('country_name', 'like', '%'.$val[$i][16].'%')->first();

                    $qualification_master = DB::table('qualification_master')->where('name', 'like', '%'.$val[$i][12].'%')->first();
                    $company_id = $request->company_id;
                    $clients = DB::table('clients')->where('company_id', $company_id)->where('name', 'like', '%'.$val[$i][18].'%')->first();

                    $job = new Job;
                    $job->slug = null;
                    $job->company_id = $company_id;
                    $job->status = 'active';
                    $clientsId = @$clients->id;
                    $job->client_id = @$clientsId; // @$request->client_id;
                    $job->title = $val[$i][0];
                    $categories = JobCategory::where('company_id', $company_id)->where('name', 'LIKE', '%'.$val[$i][2].'%')->first();
                    // dd($categories->id);
                    $job->category_id = ($categories) ? $categories->id : 0;

                    // $request->employment_type 		=	$request->employment_type ?? $job->employment_type;
                    $employment_type = ['Full-time' => 0, 'Contract' => 1];
                    // dd($employment_type,$val[$i][3]);
                    $type = $employment_type[$val[$i][3]];
                    // dd($type);
                    if (! empty($val[$i][3])) {
                        $job->employment_type = $type;
                    }
                    if (! empty($val[$i][11])) {
                        $job->total_positions = $val[$i][11];
                    }
                    if (! empty($val[$i][5])) {
                        $job->min_exp = $val[$i][5];
                    }
                    if (! empty($val[$i][6])) {
                        $job->max_exp = $val[$i][6];
                    }
                    $days = 60;
                    $start_date = date('Y-m-d');
                    $end_date = date('Y-m-d', strtotime($start_date.' + '.@$days.' days'));
                    $job->start_date = $start_date;

                    $job->end_date = $end_date;
                    $job->priority = 0;
                    $job->location_id = 1;
                    $job->meta_details = [
                        'title' => $val[$i][0],
                        'description' => $val[$i][13] ?: strip_tags(Str::substr(html_entity_decode($val[$i][13]), 0, 150)),
                    ];

                    $salaryMode = GeneralMethod::salaryMode();
                    $currencySymboleforexcell = GeneralMethod::currencySymboleforexcell();
                    $flippedSalaryMode = array_flip($salaryMode);
                    $flippedCurrencySymboleforexcell = array_flip($currencySymboleforexcell);
                    //    dd($flippedSalaryMode,$flippedCurrencySymboleforexcell);
                    // @$request->min_salary_currency = 1;
                    // dd(@$request->min_salary_currency);
                    @$request->minimum_salary = $val[$i][9];
                    @$request->maximum_salary = $val[$i][10];
                    @$request->min_salary_currency = $flippedCurrencySymboleforexcell[$val[$i][7]];
                    @$request->max_salary_currency = $flippedCurrencySymboleforexcell[$val[$i][7]];
                    @$request->max_sal_mode = $flippedSalaryMode[$val[$i][8]];
                    @$request->min_sal_mode = $flippedSalaryMode[$val[$i][8]];
                    $job->min_salary_currency = @$request->min_salary_currency;
                    $job->min_salary = @$request->minimum_salary;
                    $job->min_sal_mode = @$request->min_sal_mode;

                    $job->max_salary_currency = @$request->max_salary_currency;
                    $job->max_salary = @$request->maximum_salary;
                    $job->max_sal_mode = @$request->max_sal_mode;
                    if (! empty($val[$i][13])) {
                        $job->job_description = $val[$i][13];
                    }

                    // dd($job);
                    $job->work_type = $val[$i][4];
                    $job->show_vendor = 'no';

                    $currency = GeneralMethod::currency();  // Currency code

                    $ecurrency = $currency[@$request->max_salary_currency];
                    $econvertedAmount = @$request->maximum_salary;
                    if (@$request->max_salary_currency > 0) {
                        $econvertedAmount = GeneralMethod::convertToINR($econvertedAmount, $ecurrency);
                    }

                    $ectcdivided = GeneralMethod::salaryDividedByMode(@$request->max_sal_mode, $econvertedAmount);
                    $job->max_yearly = @$ectcdivided->yearly;
                    $job->max_monthly = @$ectcdivided->monthly;
                    $job->max_hourly = @$ectcdivided->hourly;

                    $currency = $currency[@$request->min_salary_currency];
                    $convertedAmount = @$request->minimum_salary;
                    if (@$request->min_salary_currency > 0) {
                        $convertedAmount = GeneralMethod::convertToINR($convertedAmount, $currency);
                    }

                    $ctcdivided = GeneralMethod::salaryDividedByMode(@$request->min_sal_mode, $convertedAmount);
                    $job->min_yearly = @$ctcdivided->yearly;
                    $job->min_monthly = @$ctcdivided->monthly;
                    $job->min_hourly = @$ctcdivided->hourly;
                    $job->qualification = (@$qualification_master) ? $qualification_master->id : 1;
                    $job->auto_date_job = Carbon::today();

                    // $alldatalist = $alldatalist->where('title', 'LIKE', "%$search%");
                    //  dd($val[$i][1]);

                    $job->save();

                    if (! empty(@$val[$i][1])) {
                        $locationCity = explode(',', $val[$i][1]);

                        foreach ($locationCity as $key => $value) {
                            $city_row = DB::table('city_list')
                                ->join('state_list', 'state_list.id', '=', 'city_list.state_id')
                                ->join('countries', 'countries.id', '=', 'state_list.country_id')
                                ->select('city_list.city_id', 'countries.id as countryId', 'state_list.id as stateId')
                                ->where('city_list.city', 'LIKE', "%{$value}%")
                                ->first();
                            if ($city_row) {
                                $insertArrCity = ['company_id' => $company_id, 'city_id' => ($city_row) ? $city_row->city_id : '', 'country_id' => ($city_row) ? $city_row->countryId : '', 'job_id' => $job->id];
                                DB::table('client_city_jobs')->insert($insertArrCity);
                            }
                        }

                    }

                    if (! empty(@$val[$i][11])) {
                        $qualification = explode(',', $val[$i][12]);
                        foreach ($qualification as $key => $value) {
                            $qualification_row = DB::table('qualification_master')
                                ->select('qualification_master.id', 'qualification_master.name')
                                ->where('qualification_master.name', 'LIKE', "%{$value}%")
                                ->first();
                            if ($qualification_row) {
                                $insertArrQualification = ['job_id' => $job->id, 'qualification_id' => (@$qualification_row) ? $qualification_row->id : '', 'qualification_name' => (@$qualification_row) ? $qualification_row->name : ''];
                                DB::table('job_qualifications')->insert($insertArrQualification);
                            }
                        }

                    }

                    if (! empty(@$val[$i][14])) {
                        $skills = explode(',', $val[$i][14]);
                        foreach ($skills as $key => $value) {
                            $insertArr = ['company_id' => $company_id, 'job_id' => $job->id, 'skill_name' => $value];
                            DB::table('job_metronics_skills')->insert($insertArr);
                        }
                    }

                    $existingEntry = DB::table('jd_details')
                        ->where('title', @$val[$i][0])
                        ->exists();

                    if (! $existingEntry) {
                        $values = [
                            'title' => @$val[$i][0],
                            'jd' => @$val[$i][13],
                            'minimum_exp' => @$val[$i][5],
                            'maxmum_exp' => @$val[$i][6],
                            'status' => 0,
                            'skills' => (count($skills) > 0) ? implode(',', $skills) : '',
                        ];
                        DB::table('jd_details')->insert($values);
                    }

                    if (! empty(@$val[$i][15])) {
                        $values = [
                            'company_id' => $company_id,
                            'job_id' => $job->id,
                            'detail_value' => $val[$i][15],
                        ];
                        DB::table('job_additional_detail')->insert($values);
                    }
                    if (! empty(@$val[$i][16])) {
                        $values = [
                            'company_id' => $company_id,
                            'job_id' => $job->id,
                            'detail_value' => $val[$i][16],
                        ];
                        DB::table('job_additional_detail')->insert($values);
                    }
                    if (! empty(@$val[$i][17])) {
                        $values = [
                            'company_id' => $company_id,
                            'job_id' => $job->id,
                            'detail_value' => $val[$i][17],
                        ];
                        DB::table('job_additional_detail')->insert($values);
                    }

                    if (! is_null($request->hiring_namager)) {
                        $hiringManagerArr = '';
                        $recruiterArr = explode(',', $request->hiring_namager);
                        DB::table('client_contact_info_jobs')->where('job_id', $job->id)->delete();
                        foreach ($recruiterArr as $cp) {
                            $recRow = DB::table('client_contact_info')->select('client_contact_info.id')->where('client_id', @$clientsId)->where('contact_person_name', trim($cp))->first();
                            if (! empty(@$recRow->id) && @$recRow->id > 0) {
                                $insertArr = ['company_id' => $company_id, 'client_contact_info_id' => $recRow->id, 'job_id' => $job->id];
                                DB::table('client_contact_info_jobs')->insert($insertArr);
                            } else {
                                $recRow = DB::table('client_contact_info')->select('client_contact_info.id')->where('client_id', @$clientsId)->first();
                                if (! empty(@$recRow->id) && @$recRow->id > 0) {
                                    $insertArr = ['company_id' => $company_id, 'client_contact_info_id' => $recRow->id, 'job_id' => $job->id];
                                    DB::table('client_contact_info_jobs')->insert($insertArr);
                                }
                            }
                        }
                    }
                    // UPDATE RECRUITER

                    if (! is_null($request->recruiter)) {
                        DB::table('job_assign')->where('job_id', $job->id)->delete();
                        $recruiterArr = '';
                        $recruiterArr = explode(',', $request->recruiter);
                        foreach ($recruiterArr as $recArr) {
                            $recRow = DB::table('users')->select('users.id')->where('company_id', $company_id)->where('name', trim($recArr))->first();
                            if (! empty(@$recRow->id) && @$recRow->id > 0) {
                                $values = ['company_id' => $company_id, 'user_id' => $recRow->id, 'job_id' => $job->id];
                                DB::table('job_assign')->insert($values);
                            }
                        }
                    }

                    // GeneralMethod::UpdateTimeline($timelineArr);
                    $done++;

                } else {
                    $pendind++;

                }

            }

        }

        return response()->json([
            'message' => '',
            'status' => 'success',
            'done' => $done,
            'pendind' => $pendind,
        ]);

    }

    public function receiptdownload(Request $request)
    {

        $id = decrypt($request->id);
        // dd($id);
        $carts = DB::table('carts')->where('id', '=', $id)->first();
        if ($id > 0) {
            $company_id = $carts->company_id;
            // dd($company_id);
            $clients = DB::table('clients')
                ->leftJoin('companies', 'companies.id', '=', 'clients.company_id')
                ->leftJoin('users', 'users.company_id', '=', 'companies.id')
                ->select('clients.id', 'companies.company_name', 'users.name')
                ->where('clients.company_id', '=', $company_id)
                ->where('clients.self_company', '=', 1)
                ->first();

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
                ->where('client_id', $clients->id)
                ->first();
            $company_name = @$clients->company_name;
            $user_name = @$clients->name;
            $locArrLocation = '';
            if ($locInfo) {
                $city_name = @$locInfo->city;

                $locArrLocation = ucwords(@$locInfo->address).' '.ucwords($city_name).' , '.ucwords(@$locInfo->state_name).' , '.ucwords(@$locInfo->country_name);
            }

            //    dd($locArrLocation);

            $reciepts = DB::table('cart_transaction_history')
                ->select('cart_transaction_history.*',
                    'billing_address_by_orders.organization',
                    'billing_address_by_orders.postal_code',
                    'billing_address_by_orders.gstin',
                    'billing_address_by_orders.street',
                    'billing_address_by_orders.building',
                    'billing_address_by_orders.phone',
                    'billing_address_by_orders.last_name',
                    'billing_address_by_orders.first_name',
                    'billing_address_by_orders.pan_no',
                    'billing_address_by_orders.country_id',
                    'billing_address_by_orders.state_id',
                    'billing_address_by_orders.city',
                    'countries.country_name',
                    'state_list.state',
                    'city_list.city',
                    'billing_address_by_orders.sold_organization',
                    'billing_address_by_orders.sold_postal_code',
                    'billing_address_by_orders.sold_gstin',
                    'billing_address_by_orders.sold_street',
                    'billing_address_by_orders.sold_building',
                    'billing_address_by_orders.sold_phone',
                    'billing_address_by_orders.sold_last_name',
                    'billing_address_by_orders.sold_first_name',
                    'billing_address_by_orders.sold_pan_no',
                    'billing_address_by_orders.sold_country_id',
                    'billing_address_by_orders.sold_state_id',
                    'billing_address_by_orders.sold_city',
                    'sold_countries.country_name as sold_country_name',
                    'sold_state_list.state as sold_state',
                    'sold_city_list.city as sold_city',
                )
                ->join('carts', 'carts.id', '=', 'cart_transaction_history.cart_id')
                ->leftJoin('billing_address_by_orders', 'billing_address_by_orders.cart_transaction_id', '=', 'cart_transaction_history.id')
                ->leftJoin('countries', 'countries.id', '=', 'billing_address_by_orders.country_id')
                ->leftJoin('state_list', 'state_list.id', '=', 'billing_address_by_orders.state_id')
                ->leftJoin('city_list', 'city_list.city_id', '=', 'billing_address_by_orders.city')
                ->leftJoin('countries as sold_countries', 'sold_countries.id', '=', 'billing_address_by_orders.sold_country_id')
                ->leftJoin('state_list as sold_state_list', 'sold_state_list.id', '=', 'billing_address_by_orders.sold_state_id')
                ->leftJoin('city_list as sold_city_list', 'sold_city_list.city_id', '=', 'billing_address_by_orders.sold_city')
                ->where('cart_transaction_history.cart_id', '=', $id)->first();

            $sold_by_fixed = DB::table('sold_by_address')->select('sold_by_address.*',
                'countries.country_name',
                'state_list.state',
                'city_list.city',
            )
                ->leftJoin('countries', 'countries.id', '=', 'sold_by_address.country_id')
                ->leftJoin('state_list', 'state_list.id', '=', 'sold_by_address.state_id')
                ->leftJoin('city_list', 'city_list.city_id', '=', 'sold_by_address.city')->first();

            // dd($sold_by_fixed );

            // dd( $reciepts);

            $paymentStatus = $reciepts->status;
            $status_text = '';
            $status_color = '';

            switch ($paymentStatus) {
                case 'created':
                    $status_text = 'Initiated';
                    $status_color = 'color: rgba(224, 11, 11, 0.07)';
                    break;
                case 'authorized':
                    $status_text = 'Authorized';
                    $status_color = 'color: rgba(224, 11, 11, 0.07)';
                    break;
                case 'captured':
                    $status_text = 'PAID';
                    $status_color = 'color: rgba(11, 224, 64, 0.07)';
                    break;
                case 'failed':
                    $status_text = 'FAILED';
                    $status_color = 'color: rgba(224, 11, 11, 0.07)';
                    break;
                case 'refunded':
                    $status_text = 'Refunded';
                    $status_color = 'color: rgba(224, 11, 11, 0.07)';
                    break;
                case 'partially_refunded':
                    $status_text = 'Partially refunded';
                    $status_color = 'color: rgba(224, 11, 11, 0.07)';
                    break;
                default:
                    $status_text = 'Unknown status';
                    $status_color = 'color: rgba(224, 11, 11, 0.07)';
                    break;
            }

            //  dd($reciepts,$id);
            // Generate the PDF
            //  return view('receipt',compact('reciepts','locArrLocation','company_name','user_name','status_text','status_color','sold_by_fixed'));
            $pdf = app('dompdf.wrapper');
            $pdf->loadView('receipt', compact('reciepts', 'locArrLocation', 'company_name', 'user_name', 'status_text', 'status_color', 'sold_by_fixed'));
            //    $pdf = Pdf::loadView('invoice');
            // dd($pdf->output());

            $recieptno = rand(1111, 9999);
            // Generate a filename
            // $fileName = 'receipt_'.$recieptno.'.pdf';
            $fileName = (@$reciepts->invoiceId) ? $reciepts->invoiceId.'.pdf' : $recieptno.'.pdf';
            if ($reciepts) {
                return response($pdf->output(), 200)
                    ->header('Content-Type', 'application/pdf')
                    ->header('Content-Disposition', 'attachment; filename="'.$fileName.'"');
            } else {
                return back()->withErrors(['error_get' => 'Receipt not found by ID']);
            }

            // dd($fileName);

            // Save the PDF to storage (optional, if you want to save it)
            // $pdf->save(storage_path('app/public/invoices/' . $fileName));

            // Return the file as a download

        } else {
            return back()->withErrors(['error_get' => 'Receipt not found by ID']);
        }

    }

    public function priceCurrencyWise(Request $request)
    {

        $currency = $request->currency_get;
        $event_type = $request->topUptype;

        // Fetch top-up coin packages from the database based on the event type and status
        $top_up_coin_packages = DB::table('top_up_coin_packages')
            ->where('event_type', '=', $event_type)
            ->where('status', '=', 1)
            ->get();

        $html = '';

        // dd($top_up_coin_packages);

        if ($top_up_coin_packages) {
            foreach ($top_up_coin_packages as $key => $val) {
                $html .= '<div class="col-lg-2" style="margin-bottom: 6px;width:25%!important">';
                $html .= '<input type="radio" class="btn-check app_int_amount" name="app_int_amount" value="'.($val->id).'" id="app_int_amount'.$val->id.'">';
                $html .= '<label for="app_int_amount'.$val->id.'" class="btn btn-light-primary w-100" data-kt-modal-bidding="option">';

                if ($val->custome_chk == 0) {
                    $html .= '<div class="fw-semibold" style="position: relative">';
                    $html .= '<div class="fs-6 fw-bold" style="position: absolute; top: -24px;">
                                    <span class="fs-4">'.$val->coins.' Credit</span>
                                </div>';
                    $html .= '<div style="position: relative; margin-top: 22px;">
                                    <div class="fs-6 fw-bold" style="float: left;">
                                        <span class="mb-2"><i class="fa fa-'.($currency == 'INR' ? 'inr' : 'usd').'" aria-hidden="true"></i></span>'.
                                    ($currency == 'INR' ? $val->price : $val->price_usd).'
                                    </div>';
                    $html .= '<div class="fs-6 fw-bold" style="float: right;">
                                        <span class="mb-2"><i class="fa fa-'.($currency == 'INR' ? 'inr' : 'usd').'" aria-hidden="true"></i></span>'.
                                    ($currency == 'INR' ? $val->per_coin_rate : $val->per_coin_rate_usd).'/Coins
                                    </div>
                                </div>';
                    $html .= '</div>';
                } else {
                    $html .= '<div style="position: relative; margin-top: 22px;">
                                    <div class="fs-6 fw-bold" style="position: absolute; top: -24px;">
                                        <span class="fs-4">Custom</span>
                                    </div>';
                    $html .= '<div class="fs-6 fw-bold" style="float: left;">
                                        <span class="mb-2"></span>Contact
                                </div>';
                    $html .= '<div class="fs-6 fw-bold" style="float: right;">
                                        <span class="mb-2"></span>'.
                                    ($currency == 'INR' ? $val->per_coin_rate : $val->per_coin_rate_usd).' Coins
                                </div>';
                    $html .= '</div>';
                }

                $html .= '</label>';
                $html .= '</div>';
            }
        }

        // Return the generated HTML
        echo $html;

    }

    public function contactdata_old(Request $request)
    {

        $clientId = '';
        $clientId = $_REQUEST['clientId'];

        // dd($clientId);

        $available_status = '';
        $available_status = GeneralMethod::available_status();
        $feedback_status = '';
        $feedback_status = GeneralMethod::feedback_status();
        $interview_mode_arr = '';
        $interview_mode_arr = GeneralMethod::interview_mode();

        $shedule = DB::table('client_contact_info')
            ->join('clients', 'clients.id', '=', 'client_contact_info.client_id')
            ->select('client_contact_info.id', 'client_contact_info.contact_person_name', 'client_contact_info.contact_person_email', 'client_contact_info.contact_person_number', 'client_contact_info.contact_person_designation')
            ->where('clients.company_id', $request->clientId)
            ->get();

        return DataTables::of($shedule)
            ->editColumn('chkbox', function ($row) {
                return '<div class="form-check form-check-sm form-check-custom form-check-solid">
                              <input class="form-check-input form-check-input2" type="checkbox" value="1" />
                            </div>';
            })
            ->editColumn('Name', function ($row) {
                $bgTextDecore = $strVal = '';
                $str = trim(@$row->contact_person_name);
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
                    $bgTextDecore = 'fs-3 bg-light-danger text-danger';
                } else {
                    $bgTextDecore = 'fs-3 bg-light-secondary';
                }

                return '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5"  style="float:left"  >
                              <a href="javascript:void(0);">
                                <div class="symbol-label fs-3 '.$bgTextDecore.' border-radius-5">'.ucwords($strVal).'</div>
                              </a>
                            </div>
                            <div class="d-flex flex-column">
                              <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">'.ucwords(@$row->contact_person_name).'</a> 
                            </div>';

            })
            ->editColumn('Role', function ($row) {
                return '<div class="d-flex flex-column">
                              <span class="text-dark fw-bolder fs-6">'.@$row->contact_person_designation.'</span> 
                            </div>';
            })
            ->editColumn('Contacts', function ($row) {
                return '<div class="d-flex flex-column">
                              <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">'.@$row->contact_person_number.'</a>
                              <a href="javascript:void(0);" class="text-muted fw-bold d-block fs-7 text-hover-primary">'.@$row->contact_person_email.'</a>
                            </div>';
            })
            ->editColumn('Status', function ($row) {
                return '<span class="bg-light-success text-success fw-bolder fs-6">Active</span>';
            })
            ->rawColumns(['chkbox', 'Name', 'Role', 'Contacts', 'Status'])
            ->make(true);

    }

    // 04/10/2024
    public function contactdata(Request $request)
    {

        $company_id = $_REQUEST['clientId'];

        $searchTerms = request('cus_search');
        $custom_search = $searchTerms; // json_decode($searchTerms, true);

        $searchQuery = $request->input('q');
        $page = max((int) $request->input('page', 1), 1);
        $size = (int) $request->input('size', 10); // Default 20 results per page
        $from = ($page - 1) * $size;

        $sortField = 'id'; // Default sort field
        $sortOrder = 'desc'; // Default sort order

        if (! empty($custom_search)) {

            $must[] = [
                'bool' => [
                    'must' => [
                        [
                            'bool' => [
                                'should' => [
                                    ['match_phrase_prefix' => ['name' => '*'.strtolower($custom_search).'*']],
                                    ['wildcard' => ['email' => '*'.strtolower($custom_search).'*']],
                                    ['match_phrase_prefix' => ['designation' => '*'.strtolower($custom_search).'*']],
                                    ['match_phrase_prefix' => ['display_name' => '*'.strtolower($custom_search).'*']],
                                ],
                                'minimum_should_match' => 1,
                            ],
                        ],
                    ],
                ],
            ];
        }

        // 4. COMPANY ACCESS CONDITIONS
        $must[] = [
            'bool' => [
                'should' => [
                    ['term' => ['company_id' => $company_id]],
                ],
                'minimum_should_match' => 1,
            ],
        ];

        if (request('sortingfilter') != '') {
            $valCheck = request('sortingfilter');
            if ($valCheck == 1) {
                $sortField = 'created_at';
                $sortOrder = 'desc';
            } elseif ($valCheck == 2) {
                $sortField = 'created_at';
                $sortOrder = 'asc';
            } else {
                $sortField = 'id';
                $sortOrder = 'desc';
            }
        }

        // FINAL ELASTIC PARAMS
        $params = [
            'index' => ELASTIC_USERS_LIST,
            'request_cache' => true,
            'body' => [
                'from' => $from,
                'size' => $size,
                'track_total_hits' => true,
                'sort' => [
                    [
                        $sortField => [
                            'order' => $sortOrder,
                        ],
                    ],
                ],
                'query' => [
                    'bool' => [
                        'must' => $must,
                        'filter' => [],
                    ],
                ],
            ],
        ];

        if (! $this->elasticService->indexExists(ELASTIC_USERS_LIST)) {
            $total = 0;
            $data = [];
        } else {
            $response = $this->elasticService->getClient()->search($params);
            $total = $response['hits']['total']['value'] ?? 0;
            $data = array_map(function ($hit) {
                return $hit['_source'];
            }, $response['hits']['hits']);
        }

        $pagination = [
            'total' => $total,
            'current_page' => $page,
            'last_page' => ceil($total / $size),
            'listType' => 'allcandidate',
        ];

        // dd($pagination);

        $userPermissions = [];

        return response()->json([
            'html' => view('super-admin.ajax_pages.company-team-list-ajax', compact('data', 'userPermissions', 'company_id'))->render(),
            'total' => $total,
            'pagination' => view('super-admin.ajax_pages.pagination', compact('pagination'))->render(),
        ]);

        // dd($request->clientId,$company_id);

        $users = User::select('users.id', 'users.name', 'users.email', 'users.mobile', 'users.root_user', 'roles.display_name', 'users.created_at', 'users.status', 'users.image', 'users.working_status', 'users.root_user',
            'roles.name as roles_name', 'users_profile.designation', 'users.password');
        $users = $users->join('role_user', 'role_user.user_id', 'users.id');
        $users = $users->leftJoin('users_profile', 'users_profile.user_id', '=', 'users.id');
        $users = $users->join('roles', 'roles.id', 'role_user.role_id');
        $users = $users->where('users.company_id', '=', $company_id);
        $searchTerms = request('cus_search');
        $custom_search = json_decode($searchTerms, true);

        if (\request('cus_search') != '') {

            $users = $users->where(function ($query) use ($custom_search) {
                $query->whereIn('users.name', $custom_search);
                $query->orWhereIn('users.email', $custom_search);
                $query->orWhereIn('users_profile.designation', $custom_search);
                $query->orWhereIn('roles.display_name', $custom_search);
            });
        }

        if (\request('sortingfilter') != '') {
            $valCheck = request('sortingfilter');
            if ($valCheck == 1) {
                $users = $users->orderBy('users.created_at', 'DESC');
            } elseif ($valCheck == 2) {
                $users = $users->orderBy('users.created_at', 'ASC');
            }
        } else {
            // $users = $users->orderBy('jobs.id', 'DESC');
        }

        // dd($users->get());
        return DataTables::of($users)
            ->editColumn('chkbox', function ($row) {
                if ($row->root_user == 1) {
                    return '<div class="form-check form-check-sm form-check-solid"><input class="form-check-input form-check-input1"  type="checkbox" disabled></div>';
                } else {
                    return '<div class="form-check form-check-sm form-check-custom form-check-solid"><input class="form-check-input form-check-input1 chk_list_item" name="chk_list_item[]" type="checkbox" value="'.$row->id.'"></div>';
                }

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
                $icon = '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5 show-detail-team" data-row-id="'.$row->id.'" style="float:left" >
													 <a href="javascript:void(0);" data-row-id="'.$row->id.'" class="show-detail-team" id="kt_activities3_toggle" >
														<div class="symbol-label fs-3 '.@$bgTextDecore.'  border-radius-5">'.ucwords(@$strVal).'</div>
													</a>
											</div>';
                if (! empty($row->image)) {
                    $imagePath = '';
                    $imagePath = @$row->image;
                    $icon = '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2  border-radius-5 show-detail-team" data-row-id="'.$row->id.'" style="float:left">
														<a href="javascript:void(0);">
															<div class="symbol-label border-radius-5">
															<img src="'.$imagePath.'" alt="User Image" class="border-radius-5 w-100">
															</div>
														</a>
													</div>';
                }
                $departmentArr = ucwords(@$row->designation);

                return $icon.'<div class="d-flex flex-column show-detail-team" data-row-id="'.$row->id.'">
										<a href="javascript:void(0);" data-row-id="'.$row->id.'" class="text-dark fw-bolder text-hover-primary fs-6 show-detail-team"  data-toggle="tooltip1" data-html="true" title="'.ucwords(@$row->name).'">'.ucwords(substr(@$row->name, 0, 15)).'</a>
										<a href="javascript:void(0);" data-row-id="'.$row->id.'" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary show-detail-team">'.@$departmentArr.'</a>
									  </div>
									  ';
            })
            ->editColumn('designation', function ($row) {
                $activeJobs = DB::table('job_assign')->join('jobs', 'jobs.id', '=', 'job_assign.job_id')->where('jobs.status', '=', 'active')->where('job_assign.user_id', $row->id)->count();

                $departmentArr = '';

                return '<div class="d-flex flex-column">
										<span class="fw-bolder text-success text-muted fw-bold d-block fs-7 fs-6">'.$activeJobs.' Active</span>
									  </div>
									  ';
            })
            ->editColumn('Role', function ($row) {
                $disabled = $row->root_user == 1 ? '' : 'update-user-role';
                $update = $row->root_user == 1 ? '' : '';

                return '<div class="d-flex flex-column '.$disabled.'" data-row-id="'.$row->id.'">
										<span class="text-dark fw-bolder fs-6">'.ucwords($row->display_name).'
										 
											'.$update.'
										</span>
									</div>';
            })
            ->editColumn('Status', function ($row) {
                return '<span class="bg-light-success text-'.($row->status == 'active' ? 'success' : 'danger').' fw-bolder fs-6">'.($row->status == 'active' ? 'Active' : 'Inactive').'</span>';
            })
            ->editColumn('WorkingStatus', function ($row) {

                $chk = $disabled = '';
                $working_status = $row->working_status;
                if ($working_status == 1) {
                    $chk = 'checked="checked"';
                }
                $disabled = $row->root_user == 1 ? 'disabled="disabled"' : '';
                if (! empty($row->password)) {
                    return '<div class="form-check form-switch form-switch-sm form-check-custom form-check-solid">
												<input id="working_status_'.$row->id.'" class="form-check-input chkStatus" type="checkbox" value="1" name="working_status" '.@$chk.' onclick="WorkingStatus(this, '.$row->id.');"   '.@$disabled.'  ></div>';
                } else {
                    return '<div class="form-check form-switch form-switch-sm form-check-custom form-check-solid" style="position: relative;">
												<input id="working_status_'.$row->id.'" class="form-check-input chkStatus" type="checkbox" value="1" name="working_status" '.@$chk.'  '.@$disabled.'  >
												<div style="position: absolute; top: 0; width: 100%; height: 100%;"> </div>
												</div>
												';
                }

            })
            ->editColumn('Contacts', function ($row) {

                return '<div class="d-flex flex-column">
                            <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">'.@$row->mobile.'</a>
                            <a href="javascript:void(0);" class="text-muted fw-bold d-block fs-7 text-hover-primary">'.@$row->email.'</a>
                          </div>';
            })
            ->rawColumns(['chkbox', 'Name', 'Role', 'Status', 'WorkingStatus', 'Contacts',  'role_name', 'action', 'designation'])
            ->addIndexColumn()
            ->make(true);

    }

    public function meetingScheduleList(Request $request)
    {

        $perPage = (int) $request->input('per_page', $request->input('size', $request->input('limit', 50)));
        $perPage = min(max($perPage, 1), 100);
        $this->totalMeetingSchedules = MeetingSchedule::all()->count();
        $this->MeetingSchedule = MeetingSchedule::with('comments', 'toCompany', 'fromCompany', 'users_main')->orderBy('id', 'desc')->paginate($perPage)->appends($request->query());

        return view('super-admin.company.meeting-list', $this->data);

    }

    public function meetingComment(Request $request, $id)
    {
        $html = '';
        $interview_feedback = DB::table('meeting_comments')
            ->select('meeting_comments.*', 'users.name', 'users.image')
            ->join('users', 'users.id', '=', 'meeting_comments.comment_by')
            ->where('meeting_comments.meeting_schedule_id', @$id)
            ->orderByDesc('meeting_comments.id')
            ->get();
        if (! empty($interview_feedback)) {
            foreach ($interview_feedback as $intfeed) {

                $newDate = date('d-m-Y', strtotime($intfeed->comment_date));
                $newTime = date('H:i A', strtotime($intfeed->comment_date));

                $imagePath = '';
                $imagePath = url('public/user-uploads/team_profile_img/'.@$intfeed->image);

                $feedback_star_imagePath = $feedback_star_text = '';

                $feedback_star_imagePath = url('public/img/star/5-blank.png');
                $feedback_star_text = 'Blank';

                $html .= '<div class="overflow-auto pb-5">
                    <div class="d-flex align-items-center border border-dashed border-gray-300 rounded min-w-700px p-5">
                    <div class="mb-1"> 
                    <a href="#" class="fs-5 fw-bold text-gray-800 text-hover-primary mb-2">
                    <img src="'.str_replace('index.php', '', @$feedback_star_imagePath).'" alt="'.@$feedback_star_text.'"  />&nbsp;Meeting Feedback:
                    </a>
                    <p> '.$intfeed->comments.' </p>
                    <div class="d-flex align-items-center mt-1 fs-6">
                    <div class="text-muted me-2 fs-7">Added at '.@$newTime.' | '.@$newDate.' by</div>
                    <div class="symbol symbol-circle symbol-25px" data-toggle="tooltip" data-boundary="window" data-placement="top" title="'.@$intfeed->name.'"> 
                    <img src="'.str_replace('index.php', '', @$imagePath).'" alt="'.@$intfeed->name.'"> 
                    </div>   
                    </div>
                    </div>
                    </div>
                    </div>';
            }
        }

        return response()->json(['html' => $html]);

    }

    public function meetingcommentnew(Request $request)
    {
        $MeetingComment = new MeetingComment;
        $MeetingComment->meeting_schedule_id = $request->meet_id;
        $MeetingComment->comment_by = auth()->user()->id;
        $MeetingComment->comments = $request->comments;

        $MeetingComment->save();

        return response()->json(['status' => true]);
    }

    public function all_data(Request $request)
    {
        // abort_if(!$this->user->cans('view_jobs'), 403);
        //  dd("hi");
        $company_id = request('client_id');
        $limit = '';
        $job_status = GeneralMethod::job_status();
        $salaryMode = GeneralMethod::salaryMode();
        $currencySymbole = GeneralMethod::currencySymbole();
        $this->user->id = 3;

        $searchQuery = $request->input('q');
        $page = max((int) $request->input('page', 1), 1);
        $size = (int) $request->input('size', 10); // Default 20 results per page
        $from = ($page - 1) * $size;
        $custom_status = request('custom_status');
        $company_id = request('clientId');
        $custom_hiringtype = request('custom_hiringtype');
        $custom_search = trim(request('search'));

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

        // 1. STATUS FILTERING
        // if (!session()->has('UrlMailJobId')) {
        if ($custom_status !== '' && $custom_status !== 'all') {
            if (in_array($custom_status, ['active', 'hold', 'inactive'])) {
                $statusArr = [$custom_status];
            } elseif ($custom_status === 'removed') {
                $statusArr = [];
                $must[] = [
                    'nested' => [
                        'path' => 'recommendation_relation',
                        'query' => [
                            'term' => ['recommendation_relation.final_status' => 2],
                        ],
                    ],
                ];

            }
        }

        $filter[] = ['terms' => ['status' => $statusArr]];
        // }

        // 2. HIRING TYPE
        if ($custom_hiringtype !== '' && $custom_hiringtype !== 'All') {
            if ($custom_hiringtype == 7) {
                $filter[] = ['term' => ['employment_type' => 0]];
            } elseif ($custom_hiringtype == 8) {
                $filter[] = ['term' => ['employment_type' => 1]];
            }
        }

        // 3. SEARCH CONDITIONS (similar to LIKE, IN, etc.)
        if (! empty($custom_search)) {

            $must[] = [
                'bool' => [
                    'must' => [
                        [
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
                        ],
                        [
                            'bool' => [
                                'should' => [
                                    ['match_phrase_prefix' => ['title' => '*'.strtolower($custom_search).'*']],
                                    ['term' => ['id' => (int) $custom_search]],
                                    ['wildcard' => ['client_name' => '*'.strtolower($custom_search).'*']],
                                    ['wildcard' => ['company_name' => '*'.strtolower($custom_search).'*']],
                                    [
                                        'nested' => [
                                            'path' => 'job_assign',
                                            'query' => [
                                                'wildcard' => ['job_assign.user_name' => '*'.strtolower($custom_search).'*'],
                                            ],
                                        ],
                                    ],
                                    [
                                        'nested' => [
                                            'path' => 'client_contact_info_jobs',
                                            'query' => [
                                                'wildcard' => ['client_contact_info_jobs.contact_person_name' => '*'.strtolower($custom_search).'*'],
                                            ],
                                        ],
                                    ],
                                    [
                                        'nested' => [
                                            'path' => 'client_city_jobs',
                                            'query' => [
                                                'wildcard' => ['client_city_jobs.city_name' => '*'.strtolower($custom_search).'*'],
                                            ],
                                        ],
                                    ],
                                    [
                                        'nested' => [
                                            'path' => 'client_city_jobs',
                                            'query' => [
                                                'wildcard' => ['client_city_jobs.country_name' => '*'.strtolower($custom_search).'*'],
                                            ],
                                        ],
                                    ],
                                ],
                                'minimum_should_match' => 1,
                            ],
                        ],
                    ],
                ],
            ];
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

        // FINAL ELASTIC PARAMS
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

        $pagination = [
            'total' => $total,
            'current_page' => $page,
            'last_page' => ceil($total / $size),
            'listType' => 'allcandidate',
        ];

        $userPermissions = [];

        return response()->json([
            'html' => view('super-admin.ajax_pages.company_jobs_ajax', compact('data', 'userPermissions', 'limit', 'salaryMode', 'currencySymbole', 'job_status'))->render(),
            'total' => $total,
            'pagination' => view('super-admin.ajax_pages.pagination', compact('pagination'))->render(),
        ]);

    }

    public function edit_company($id)
    {

        $this->categories = JobCategory::all();
        $this->companyType = GeneralMethod::companyType();
        $company_id = Crypt::decryptString($id);
        // dd($company_id);
        // if(!Company::find($company_id)->exists()){
        //     dd("jfjf");
        // }
        $this->account = DB::table('companies')->select('*')->where('id', $company_id)->first();
        $this->company_tags = DB::table('company_tags')->select('*')->where('company_id', $company_id)->get();

        $this->industries_type_arr = GeneralMethod::industries_type_arr();

        $this->client = Client::select('clients.id', 'clients.tags')->where('clients.company_id', '=', $company_id)->where('clients.self_company', '=', 1)->first();
        $this->client_location = DB::table('client_location')
            ->join('countries', 'countries.id', '=', 'client_location.country_id')
            ->select('client_location.city', 'client_location.city_id', 'client_location.address', 'client_location.postcode', 'countries.country_name', 'client_location.country_id', 'client_location.state')
            ->where('client_location.client_id', $this->client->id)
            ->get();

        $this->industries = DB::table('td_industries')
            ->select('td_industries.ind_id', 'td_industries.ind_name')
            ->orderBy('td_industries.ind_name', 'ASC')
            ->get();

        $this->countryArr = GeneralMethod::getAllCountryList();
        $this->statelist = GeneralMethod::statelist(101);
        $this->cities = GeneralMethod::allCities();
        $statelist = DB::table('state_list')
            ->select('state_list.id', 'state_list.state as name', DB::raw('101 as country_id'))
            ->orderBy('state_list.state')
            ->get()->toArray();
        $this->indianStatelist = json_decode(json_encode($statelist), true);

        return view('super-admin.company.company_edit', $this->data);

    }

    public function update_company(Request $request, $id)
    {
        // dd($id,$request);
        $company_logo_path = '';
        $company_id = $id;
        $company = Company::withoutGlobalScopes()->findOrFail($company_id);

        // dd($request->all());
        if (! empty(@$request->company_name)) {
            $company->company_name = @$request->company_name;
        }
        if (! empty(@$request->website)) {
            $company->website = @$request->website;
        }
        if (! empty(@$request->industries)) {
            $company->industries = @$request->industries;
        }
        if (! empty(@$request->company_size)) {
            $company->company_size = @$request->company_size;
        }
        if (! empty(@$request->industries_type)) {
            $company->industries_type = @$request->industries_type;
        }
        if (! empty(@$request->founded)) {
            $company->founded = @$request->founded;
        }
        if (! empty(@$request->kt_docs_tinymce_hidden)) {
            $company->description = htmlentities(@$request->description, ENT_QUOTES, 'UTF-8');
        }

        if ($request->hasFile('logo')) {
            $company_logo_path = Files::upload($request->logo, 'company-logo');
            $company->logo = $company_logo_path;
        }
        if (! empty(@$request->language)) {
            $company->language = @$request->language;
        }
        if (! empty(@$request->timezone)) {
            $company->timezone = @$request->timezone;
        }
        if (! empty(@$request->currency)) {
            $company->currency = @$request->currency;
        }
        if (! empty(@$request->time_format)) {
            $company->time_format = @$request->time_format;
        }
        if (! empty(@$request->date_format)) {
            $company->date_format = @$request->date_format;
        }

        if (! empty(@$request->date_format)) {
            if (@$request->app_duplicate == 1) {
                $company->app_duplicate = @$request->app_duplicate;
                $company->app_duplicate_days = @$request->app_duplicate_requisition_wise;
            } elseif (@$request->app_duplicate == 2) {
                $company->app_duplicate = @$request->app_duplicate;
                $company->app_duplicate_days = @$request->app_duplicate_overroll;
            } else {
                $company->app_duplicate = @$request->app_duplicate;
                $company->app_duplicate_days = '';
            }
        }
        if (! empty(@$request->accountVerify)) {
            $company->accountVerify = 1;
        } else {
            $company->accountVerify = 0;
        }

        $company->save();

        $response = $this->elasticService->getClient()->update([
            'index' => ELASTIC_COMPANIES_INDEX,
            'id' => $company->id,
            'retry_on_conflict' => 5,
            'body' => [
                'doc' => [
                    'id' => $company->id,
                    'accountVerify' => $company->accountVerify,
                    'website' => $company->website,
                    'industries' => $company->industries,
                    'company_size' => $company->company_size,
                    'industries_type' => $company->industries_type,
                    'founded' => $company->founded,
                    'date_format' => $company->date_format,
                    'description' => $company->description,
                    'logo' => $company->logo,
                    'curr_date' => now()->toIso8601String(),
                ],
                'doc_as_upsert' => true,
                'detect_noop' => false,
            ],
        ]);

        // Insert Skills
        $strdata = $tagsArr = '';
        // Insert Skills
        if (! empty(@$request->tags)) {
            $tags = @$request->tags;
            $tags = json_decode($tags, true);
            $tagsArr = array_column($tags, 'value');
            $strdata = implode(',', $tagsArr);

            DB::table('clients')
                ->where('company_id', @$company_id)
                ->where('self_company', 1)
                ->limit(1)
                ->update(['tags' => @$strdata]);
        }

        if ($request->pagename != 'settings') {
            // THIRD TAB-3
            $client_id = '';
            $client_obj = DB::table('clients')->select('id')->where('company_id', $company_id)->where('self_company', 1)->first();
            $client_id = @$client_obj->id;
            $addressId = @$request->addressId;
            DB::table('client_location')->where('client_id', $client_id)->delete();
            for ($i = 0; $i <= $addressId; $i++) {

                $address = @$request->address[$i];
                $country = ''; // @$request->country[$i];
                $state = ''; // @$request->state[$i];
                $city = @$request->kt_tagify_work_location[$i];
                $postcode = '0'; // @$request->postcode[$i];

                $stateId = '';
                $cityID = '';
                $countryID = '';
                $city = '';
                $work_city = @$request->kt_tagify_work_location[$i];
                $work_city_all = json_decode($work_city, true);
                if (@$request->kt_tagify_work_location[$i] != '') {
                    foreach ($work_city_all as $cityVal) {
                        $parts = explode('|', $cityVal['value']);
                        // Trim to remove any leading or trailing whitespace
                        $cityName = trim($parts[0]);
                        $countrName = trim($parts[2]);
                        $city_row = DB::table('city_list')
                            ->join('state_list', 'state_list.id', '=', 'city_list.state_id')
                            ->join('countries', 'countries.id', '=', 'state_list.country_id')
                            ->select('city_list.city_id', 'countries.id as country_id')
                            ->where('city_list.city', '!=', '')
                            ->where('city_list.city', '=', $cityName)
                            ->where('countries.country_name', '=', $countrName)
                            ->first();

                        // dd($city_row);

                        if ($city_row) {
                            $cityID = (@$city_row) ? $city_row->city_id : '';
                            $countryID = (@$city_row) ? $city_row->country_id : '';
                            $stateId = GeneralMethod::stateId($cityID);

                            $values = [
                                'client_id' => $client_id,
                                'country_id' => $countryID,
                                'city' => $city,
                                'city_id' => $cityID,
                                'address' => $address,
                                'postcode' => $postcode,
                                'state' => $stateId,
                            ];

                            DB::table('client_location')->insert($values);

                        }

                    }
                }

            }

        }

        return Reply::dataOnly(['status' => 'success']);
    }

    public function meetingSchedule(Request $request, $id)
    {

        $salaryMode = GeneralMethod::salaryMode();
        $currencySymbole = GeneralMethod::currencySymbole();

        $this->users = User::with('company')->company()->where('users.type', '<>', 1)->get();

        $this->scheduleDate = $request->date;
        // $this->currentApplicant = JobApplication::findOrFail($id);
        $this->interview_node = GeneralMethod::interview_mode();
        $this->company_id = $this->interview_id = $this->feedback_id = $this->interview_detail = '';
        $this->company_id = $id;
        // dd($this->company_id);
        // dd($this->company_id);
        $this->interview_detail = [];

        $this->company = Company::withoutGlobalScopes()->find($id);

        $this->company_location = DB::table('client_location')
        // ->join('countries', 'countries.id', '=', 'client_location.country_id')
            ->join('clients', 'clients.id', '=', 'client_location.client_id')
            ->select('client_location.city', 'client_location.city_id', 'client_location.address', 'client_location.postcode', 'client_location.country_id')
            ->where('clients.company_id', $id)
            ->orderBy('client_location.id', 'DESC')
            ->first();

        $this->interview_mode = DB::table('interview_mode')->select('*')->orderBy('interview_mode.id', 'ASC')->get();
        $this->reschedule = @$_REQUEST['reschedule'];
        $view = view('super-admin.company.meeting-create', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'view' => $view]);

    }

    public function add_team_member_form($id)
    {
        $company_id = auth()->user()->company_id;
        $this->company = DB::table('companies')->select('*')->where('id', $company_id)->first();
        $this->roles = DB::table('roles')->select('*')->where('company_id', $company_id)->orderBy('roles.display_name', 'ASC')->get();
        $view = view('super-admin.account.add_team_member_form', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'view' => $view]);
    }

    public function getmeetingchild(Request $request)
    {
        $id = $company_id = '';
        $id = @$request->id;
        $company_id = @$request->company_id;
        // dd($company_id,$id);
        $Data = DB::table('interview_mode_lavel')->select('*')->where('interview_mode_id', $id)->get();

        $companyData = Company::withoutGlobalScopes()->find($company_id);

        // dd($companyData);

        if (! empty($Data)) {
            if ($id == 1) {
                $html = '<select class="form-control" name="interview_mode_lavel" id="interview_mode_lavels" data-style="" onchange="getVideoMeetingUrl(this.value, '.$id.');">
													<option value="">Select Mode</option>';
                foreach ($Data as $cval) {

                    $html .= '<option value= "'.$cval->id.'">'.ucfirst($cval->name).'</option>';

                    $flag = 1;
                }
                $html .= '</select>';
            } elseif ($id == 2) {
                $html = '<select class="form-control" name="interview_mode_lavel" id="interview_mode_lavels" data-style="" onchange="getVideoMeetingUrl(this.value, '.$id.','.$company_id.');">
													<option value="">Select Mode</option>';
                foreach ($Data as $cval) {

                    if ($cval->id == 2) {  // Google Meeting
                        if ($companyData->google_status == 1) {
                            $html .= '<option value= "'.$cval->id.'">'.ucfirst($cval->name).'</option>';
                        }
                    } elseif ($cval->id == 3) {  // Zoom Meeting
                        if ($companyData->zoom_status == 1) {
                            $html .= '<option value= "'.$cval->id.'">'.ucfirst($cval->name).'</option>';
                        }
                    } elseif ($cval->id == 5) {  // Microsoft Meeting
                        if ($companyData->microsoft_status == 1) {
                            $html .= '<option value= "'.$cval->id.'">'.ucfirst($cval->name).'</option>';
                        }
                    } else {
                        $html .= '<option value= "'.$cval->id.'">'.ucfirst($cval->name).'</option>';
                    }

                    $flag = 1;
                }
                $html .= '</select>';
            } elseif ($id == 3) {

                $Data = DB::table('client_location')
                    ->select('client_location.id', 'client_location.address', 'client_location.city', 'client_location.postcode', 'city_list.city as cityname', 'state_list.state', 'countries.country_name')
                    ->join('client_location_jobs', 'client_location_jobs.client_location_id', '=', 'client_location.id')
                    ->join('clients', 'clients.id', '=', 'client_location.client_id')
                    ->leftJoin('countries', 'countries.id', '=', 'client_location.country_id')
                    ->leftJoin('state_list', 'state_list.id', '=', 'client_location.state')
                    ->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')
                    ->where('clients.company_id', $company_id)
                    ->orderBy('client_location.id', 'ASC')
                    ->get();

                $html = '<select class="form-control" name="interview_mode_lavel" id="interview_mode_lavels" data-style="" onchange="getVideoMeetingUrl(this.value, '.$id.');">
													<option value="">Select Address</option>';
                foreach ($Data as $cval) {
                    $cityName = @$cval->city;
                    if (empty(@$cval->city)) {
                        $cityName = @$cval->cityname;
                    }
                    $country_name = '';
                    if (! empty(@$cval->country_name)) {
                        $country_name = '- '.@$cval->country_name;
                    }
                    $stateName = '';
                    if (! empty(@$cval->state)) {
                        $stateName = ', '.@$cval->state;
                    }
                    $html .= '<option value= "'.@$cval->id.'">'.ucfirst(@$cval->address).'  '.ucfirst(@$cityName).'  - '.@$cval->postcode.' '.@$stateName.' '.@$country_name.'</option>';
                    $flag = 1;
                }
                $html .= '<<option value="0">Other address</option>';
                $html .= '</select>';
            }
        } else {
            $html = '&nbsp;-&nbsp;';
        }

        return Reply::dataOnly(['status' => 'success', 'data' => $html]);
    }

    public function recruiter(Request $request, $id)
    {

        $panellist = '';
        $panellist = $_REQUEST['panellist'];

        $this->panellistArr = explode(',', $panellist);

        // echo '<pre>'; print_r($panellist);
        //  dd($id);
        $this->allow = false;
        $company_id = $_REQUEST['company_id']; // auth()->user()->company_id;
        if (empty($company_id)) {
            $company_id = auth()->user()->company_id;
            $this->allow = true;
        }

        $this->company = DB::table('companies')->select('*')->where('id', $company_id)->first();

        // $company_id = auth()->user()->company_id;

        $this->team = DB::table('users')
            ->leftjoin('role_user', 'role_user.user_id', 'users.id')
            ->leftjoin('users_profile', 'users_profile.user_id', 'users.id')
            ->leftjoin('roles', 'roles.id', 'role_user.role_id')
            ->select('users.id', 'users.name', 'users.email', 'users_profile.designation as display_name', 'users.working_status')
            ->where('type', 0)
            ->where('users.company_id', $company_id)
            ->where('status', 'active')
            ->get();

        // dd($this->team);

        $view = view('super-admin.company.recruiter', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'view' => $view]);

    }

    public function storeSchedule(Request $request)
    {

        $application_id = $interview_type = $interviewDate = $master_status_id = $child_status_id = $interview_mode = $interview_mode_lavel = $interviewers = $panellist = '';
        $application_id = @$request->application_id;
        $interview_type = @$request->interview_type;
        $interviewDate = @$request->interviewDate;
        $interview_mode = @$request->interview_mode;
        $interview_mode_lavel = @$request->interview_mode_lavel;
        $master_status_id = @$request->master_status_id;
        $url_address = @$request->url;
        $interviewers = @$request->interviewers;
        $panellist = @$request->panellist;
        $ourpanellist = @$request->ourpanellist;
        $to_company_id = @$request->meeting_company_id;
        $from_company_id = auth()->user()->company_id;

        // INTERVIEW SCHEDULE
        $meetingSchedule = new MeetingSchedule;
        if (! empty($request->interview_type)) {
            $meetingSchedule->interview_type = $interview_type;
        }
        $meetingSchedule->schedule_date = $interviewDate;
        $meetingSchedule->from_company_id = $from_company_id;
        $meetingSchedule->to_company_id = $to_company_id;
        if (! empty($interview_mode)) {
            $meetingSchedule->interview_mode = $interview_mode;
        }
        if (! empty($interview_mode_lavel)) {
            $meetingSchedule->interview_mode_level = $interview_mode_lavel;
        }

        if (! empty($panellist)) {
            $meetingSchedule->company_team = $panellist;
        }
        if (! empty($ourpanellist)) {
            $meetingSchedule->our_team = $ourpanellist;
        }

        if (! empty($url_address)) {
            $meetingSchedule->url_address = $url_address;
        }

        $meetingSchedule->created_by = $this->user->id;
        $meetingSchedule->updated_by = $this->user->id;

        $meetingSchedule->created_at = date('Y-m-d H:i:s');
        $meetingSchedule->updated_at = date('Y-m-d H:i:s');

        $meetingSchedule->save();

        return Reply::dataOnly(['status' => 'success', 'message' => 'Meeting Reschedule successfully !']);

    }

    public function addresslist($cid)
    {

        $this->countryArr = GeneralMethod::getAllCountryList();
        // $this->statelist 		=	GeneralMethod::statelist(101);
        $this->cid = $cid;
        $this->cities = GeneralMethod::allCities();

        $this->default_loc = GeneralMethod::allCitiesSelected([]);

        $view = view('service_company.client.addresslist', $this->data)->render();

        // // dd("djdh");
        // $this->countryArr 		=	GeneralMethod::getAllCountryList();
        // //$this->statelist 		=	GeneralMethod::statelist(101);
        // $this->cid				=	$cid;
        // $view = view('super-admin.company.addresslist', $this->data)->render();
        return Reply::dataOnly(['status' => 'success', 'data' => $view]);
    }

    public function statelist($cid)
    {
        $statelist = GeneralMethod::statelist($cid);

        if ($cid == 101) {

            $Rawstatelist = DB::table('state_list')
                ->select('state_list.id', 'state_list.state as name', DB::raw('101 as country_id'))
                ->orderBy('state_list.state')
                ->get()->toArray();
            $statelist = json_decode(json_encode($Rawstatelist), true);
        }

        $custom = '<option>Select State</option>';
        foreach ($statelist as $sval) {
            $custom .= '<option value="'.$sval['id'].'">'.$sval['name'].'</option>';
        }

        return Reply::dataOnly(['status' => 'success', 'data' => $custom]);
        // echo '<pre>'; print_r($statelist);
    }

    public function citylist($state_id)
    {

        $Rawcitylist = DB::table('city_list')
            ->select('city_list.city_id as id', 'city_list.city as name')
            ->where('city_list.state_id', $state_id)
            ->orderBy('city_list.city')
            ->get()->toArray();
        $citylist = json_decode(json_encode($Rawcitylist), true);

        $custom = '<option>Select City</option>';
        foreach ($citylist as $sval) {
            $custom .= '<option value="'.$sval['id'].'">'.$sval['name'].'</option>';
        }

        return Reply::dataOnly(['status' => 'success', 'data' => $custom]);
    }

    public function changePackage($companyId)
    {
        $this->packages = Package::where('is_trial', 0)->active()->get();
        $this->company = Company::withoutGlobalScope('company')->findOrFail($companyId);

        return view('super-admin.company.change_package_modal', $this->data);
    }

    public function updateCompanyPackage(UpdatePakage $request, $companyId)
    {
        CompanyPackage::where('company_id', $companyId)
            ->update(['status' => 'inactive', 'end_date' => Carbon::now()->format('Y-m-d')]);

        $company = Company::withoutGlobalScope('company')->findOrFail($companyId);
        $company->package_id = $request->package;

        $companyPackages = new CompanyPackage;
        $companyPackages->package_id = $request->package;
        $companyPackages->company_id = $companyId;
        $companyPackages->start_date = Carbon::now()->format('Y-m-d');
        if ($request->package_type) {
            $companyPackages->package_type = 'annual';
            $companyPackages->end_date = Carbon::now()->addYear()->format('Y-m-d');

            $company->package_type = 'annual';
            $company->licence_expire_on = Carbon::now()->addYear()->format('Y-m-d');
        } else {
            $companyPackages->package_type = 'monthly';
            $companyPackages->end_date = Carbon::now()->addMonth()->format('Y-m-d');

            $company->package_type = 'monthly';
            $company->licence_expire_on = Carbon::now()->addMonth()->format('Y-m-d');
        }
        $companyPackages->status = 'active';
        $companyPackages->save();

        $company->save();

        return Reply::success(__('messages.updatedSuccessfully'));

    }

    public function loginAsCompany($companyId)
    {
        $admin = User::frontAllAdmins($companyId)->first();

        Auth::logout();

        Auth::loginUsingId($admin->id);

        return Reply::success(__('messages.successfullyLoginAsCompany'));
    }

    public function myContactPerson()
    {

        $categories = Company::with('package')->withoutGlobalScope('company')->get();
        $companyContact = DB::table('users')->select('users.id', 'users.name', 'users.email', 'users.mobile', 'users_profile.designation')->join('users_profile', 'users_profile.user_id', 'users.id');

        if (\request('company_id') != '') {
            $company_id = request('company_id');
            $companyContact->where('users.company_id', '=', $company_id);
        }

        $companyContact->groupBy('users.id');
        $companyContact->orderBy('users.id', 'DESC');

        return DataTables::of($companyContact)

            ->editColumn('Name', function ($row) {
                return '<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$row->name.'</a>';
            })
            ->editColumn('Position', function ($row) {
                return @$row->designation;
            })
            ->editColumn('Email', function ($row) {
                return @$row->email;
            })
            ->editColumn('Phone', function ($row) {
                return @$row->mobile;
            })

            ->addIndexColumn()
            ->rawColumns(['Name', 'Position', 'Email', 'Phone'])
            ->make(true);
    }

    public function emailThemes()
    {
        $params = [
            'index' => ELASTIC_EMAIL_TEMPLATE,
            'body' => [
                'size' => 10000,
                'query' => [
                    'bool' => [
                        'must_not' => [
                            [
                                'term' => [
                                    'temptype' => 9,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $response = $this->elasticService->getClient()->search($params);

        $this->th_email_template = collect($response['hits']['hits'] ?? [])
            ->map(function ($hit) {
                return (object) $hit['_source'];
            });

        // $this->th_email_template =  isset($response['hits']['hits'][0]) ? (object) $response['hits']['hits'][0]['_source'] : null;

        return view('super-admin.emailthemes.index', $this->data);
    }

    public function emailThemesupdate(Request $request, $Id)
    {
        // dd($Id);

        $params = [
            'index' => ELASTIC_EMAIL_TEMPLATE,
            'body' => [
                'size' => 10000,
                'query' => [
                    'bool' => [
                        'must' => [
                            ['term' => ['e_temp_id' => (int) $Id]],
                        ],
                    ],
                ],
            ],
        ];

        $response = $this->elasticService->getClient()->search($params);
        $this->th_email_template = isset($response['hits']['hits'][0]) ? (object) $response['hits']['hits'][0]['_source'] : null;

        return view('super-admin.emailthemes.add-edit', $this->data);
    }

    public function addEditemailThemesupdate(Request $request)
    {
        // dd($request->tokenId);
        if ($request->tokenId == 0) {
            //  dd($request->all());
            $values = [
                'template_name' => $request->template,
                'display_name' => $request->template,
                'template_subject' => $request->subject,
                'template_description' => $request->editor1,
                'status' => $request->status_is,
                'temptype' => $request->temptype_is,
                'curr_date' => date('Y-m-d'),
            ];
            $e_temp_id = DB::table('th_email_template')->insertGetId($values);

            $response = $this->elasticService->getClient()->update([
                'index' => ELASTIC_EMAIL_TEMPLATE,
                'id' => $e_temp_id,
                'retry_on_conflict' => 5,
                'body' => [
                    'doc' => [
                        'id' => $e_temp_id,
                        'e_temp_id' => $e_temp_id,
                        'template_name' => $request->template,
                        'display_name' => $request->template,
                        'template_subject' => $request->subject,
                        'template_description' => $request->editor1,
                        'status' => $request->status_is,
                        'tempForInterview' => 1,
                        'temptype' => $request->temptype_is,
                        'curr_date' => now()->toIso8601String(),
                    ],
                    'doc_as_upsert' => true,
                    'detect_noop' => false,
                ],
            ]);

        } else {
            $values = [
                'template_name' => $request->template,
                'display_name' => $request->template,
                'template_subject' => $request->subject,
                'template_description' => $request->editor1,
                'status' => $request->status_is,
                'temptype' => $request->temptype_is,
            ];
            DB::table('th_email_template')->where('e_temp_id', '=', $request->tokenId)->update($values);
            $response = $this->elasticService->getClient()->update([
                'index' => ELASTIC_EMAIL_TEMPLATE,
                'id' => $request->tokenId,
                'retry_on_conflict' => 5,
                'body' => [
                    'doc' => [
                        'id' => $request->tokenId,
                        'e_temp_id' => $request->tokenId,
                        'template_name' => $request->template,
                        'display_name' => $request->template,
                        'template_subject' => $request->subject,
                        'template_description' => $request->editor1,
                        'status' => $request->status_is,
                        'temptype' => $request->temptype_is,
                    ],
                    'doc_as_upsert' => true,
                    'detect_noop' => false,
                ],
            ]);

        }

        return Reply::dataOnly(['status' => 'success']);

    }

    public function featuresList()
    {
        $this->th_features = DB::table('features')->select('features.*')->orderBy('features.id', 'ASC')->get();

        return view('super-admin.features.index', $this->data);
    }

    public function whatsappThemes()
    {

        $params = [
            'index' => ELASTIC_EMAIL_TEMPLATE,
            'body' => [
                'size' => 10000,
                'query' => [
                    'bool' => [
                        'filter' => [
                            ['term' => ['temptype' => 9]],
                        ],
                    ],
                ],
                'sort' => [
                    ['orderByShow' => ['order' => 'asc']], // ✅ order by id
                ],
            ],
        ];

        $response = $this->elasticService->getClient()->search($params);

        $this->th_email_template = collect($response['hits']['hits'] ?? [])
            ->map(function ($hit) {
                return (object) $hit['_source'];
            });

        // $this->th_email_template =  isset($response['hits']['hits'][0]) ? (object) $response['hits']['hits'][0]['_source'] : null;

        return view('super-admin.comman.index_whatsapp', $this->data);
    }

    public function whatsappThemesupdate(Request $request, $Id)
    {
        // dd($Id);

        $params = [
            'index' => ELASTIC_EMAIL_TEMPLATE,
            'body' => [
                'size' => 10000,
                'query' => [
                    'bool' => [
                        'must' => [
                            ['term' => ['e_temp_id' => (int) $Id]],
                        ],
                    ],
                ],
            ],
        ];

        $response = $this->elasticService->getClient()->search($params);
        $this->th_email_template = isset($response['hits']['hits'][0]) ? (object) $response['hits']['hits'][0]['_source'] : null;

        return view('super-admin.comman.add-edit-whatsapp', $this->data);
    }

    public function addEditwhatsappThemesupdate(Request $request)
    {
        // dd($request->tokenId);
        if ($request->tokenId == 0) {
            //  dd($request->all());
            $values = [
                'template_name' => $request->template,
                'display_name' => $request->template,
                'template_subject' => $request->subject,
                'template_description' => $request->editor1,
                'status' => $request->status_is,
                'orderByShow' => $request->orderByShow,
                'temptype' => $request->temptype_is,
                'curr_date' => date('Y-m-d'),
            ];
            $e_temp_id = DB::table('th_email_template')->insertGetId($values);

            $response = $this->elasticService->getClient()->update([
                'index' => ELASTIC_EMAIL_TEMPLATE,
                'id' => $e_temp_id,
                'retry_on_conflict' => 5,
                'body' => [
                    'doc' => [
                        'id' => $e_temp_id,
                        'e_temp_id' => $e_temp_id,
                        'template_name' => $request->template,
                        'display_name' => $request->template,
                        'template_subject' => $request->subject,
                        'template_description' => $request->editor1,
                        'status' => $request->status_is,
                        'orderByShow' => $request->orderByShow,
                        'tempForInterview' => 1,
                        'temptype' => $request->temptype_is,
                        'curr_date' => now()->toIso8601String(),
                    ],
                    'doc_as_upsert' => true,
                    'detect_noop' => false,
                ],
            ]);

        } else {
            $values = [
                'template_name' => $request->template,
                'display_name' => $request->template,
                'template_subject' => $request->subject,
                'template_description' => $request->editor1,
                'status' => $request->status_is,
                'orderByShow' => $request->orderByShow,
                'temptype' => $request->temptype_is,
            ];
            DB::table('th_email_template')->where('e_temp_id', '=', $request->tokenId)->update($values);
            $response = $this->elasticService->getClient()->update([
                'index' => ELASTIC_EMAIL_TEMPLATE,
                'id' => $request->tokenId,
                'retry_on_conflict' => 5,
                'body' => [
                    'doc' => [
                        'id' => $request->tokenId,
                        'e_temp_id' => $request->tokenId,
                        'template_name' => $request->template,
                        'display_name' => $request->template,
                        'template_subject' => $request->subject,
                        'template_description' => $request->editor1,
                        'status' => $request->status_is,
                        'orderByShow' => $request->orderByShow,
                        'temptype' => $request->temptype_is,
                    ],
                    'doc_as_upsert' => true,
                    'detect_noop' => false,
                ],
            ]);

        }

        return Reply::dataOnly(['status' => 'success']);

    }

    public function featuresListupdate(Request $request, $Id)
    {
        // dd($Id);
        $this->th_features = DB::table('features')->select('features.*')->orderBy('features.id', 'ASC')->where('id', '=', $Id)->first();
        // dd($this->th_email_template);

        return view('super-admin.features.add-edit', $this->data);
    }

    public function addEditfeaturesListupdate(Request $request)
    {
        // dd($request->tokenId);
        if ($request->tokenId == 0) {
            //  dd($request->all());
            $values = [
                'title' => $request->title,
                'days' => $request->days,
                'status' => $request->status_is,
            ];
            DB::table('features')->insert($values);
        } else {
            $values = [
                'title' => $request->title,
                'days' => $request->days,
                'status' => $request->status_is,
            ];
            DB::table('features')->where('id', '=', $request->tokenId)->update($values);
        }

        return Reply::dataOnly(['status' => 'success']);

    }

    public function notification()
    {
        $params = [
            'index' => ELASTIC_EMAIL_NOTIFICATIONS,
            'body' => [
                'size' => 10000,
            ],
        ];

        $response = $this->elasticService->getClient()->search($params);

        $this->th_email_template = collect($response['hits']['hits'] ?? [])
            ->map(function ($hit) {
                return (object) $hit['_source'];
            });

        return view('super-admin.notification.index', $this->data);
    }

    public function notificationupdate(Request $request, $Id)
    {
        // dd($Id);
        $params = [
            'index' => ELASTIC_EMAIL_NOTIFICATIONS,
            'body' => [
                'size' => 10000,
                'query' => [
                    'bool' => [
                        'must' => [
                            ['term' => ['id' => (int) $Id]],
                        ],
                    ],
                ],
            ],
        ];

        $response = $this->elasticService->getClient()->search($params);
        $this->th_email_template = isset($response['hits']['hits'][0]) ? (object) $response['hits']['hits'][0]['_source'] : null;

        // $this->clients = DB::table('clients')->select('clients.*')->orderBy('clients.id', 'ASC')->get();

        $this->mail_permissions = DB::table('mail_permissions')->where('notification_id', '=', $Id)->pluck('role_name')->toArray();

        $this->roles = DB::table('roles')->select('roles.*')->groupBy('display_name')->get();

        // dd($this->th_email_template);	 display_name

        return view('super-admin.notification.add-edit', $this->data);
    }

    public function addEditnotificationupdate(Request $request)
    {
        // dd($request->role_ck[0],$request->tokenId);
        if ($request->tokenId == 0) {
            //  dd($request->all());
            $values = [
                'purpose' => $request->purpose,
                'subject' => $request->subject,
                'status' => $request->status,
                'body_description' => $request->editor1,
                'from_email_id' => $request->from_email_id,
                'mailType' => $request->mailType_is,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            $id = DB::table('email_notifications')->insertGetId($values);

            $response = $this->elasticService->getClient()->update([
                'index' => ELASTIC_EMAIL_NOTIFICATIONS,
                'id' => $id,
                'retry_on_conflict' => 5,
                'body' => [
                    'doc' => [
                        'id' => $id,
                        'purpose' => $request->purpose,
                        'subject' => $request->subject,
                        'from_email_id' => $request->from_email_id,
                        'body_description' => $request->editor1,
                        'status' => $request->status,
                        'mailType' => $request->mailType_is,
                        'created_at' => now()->toIso8601String(),
                        'updated_at' => now()->toIso8601String(),
                    ],
                    'doc_as_upsert' => true,
                    'detect_noop' => false,
                ],
            ]);

            // if(!empty($request->role_c)){
            if (count($request->role_ck) > 0) {
                for ($i = 0; $i < count($request->role_ck); $i++) {
                    $rowId = DB::table('mail_permissions')->insertGetId(['role_name' => $request->role_ck[$i], 'notification_id' => $id]);

                    $response = $this->elasticService->getClient()->update([
                        'index' => ELASTIC_EMAIL_PERMISSIONS,
                        'id' => $rowId,
                        'retry_on_conflict' => 5,
                        'body' => [
                            'doc' => [
                                'id' => $rowId,
                                'role_name' => $request->role_ck[$i],
                                'notification_id' => $id,
                            ],
                            'doc_as_upsert' => true,
                            'detect_noop' => false,
                        ],
                    ]);

                }
            }
            // }

        } else {
            $values = [
                'purpose' => $request->purpose,
                'subject' => $request->subject,
                'status' => $request->status,
                'body_description' => $request->editor1,
                'mailType' => $request->mailType_is,
                'from_email_id' => $request->from_email_id,
            ];
            // Intent: avoid SQL errors on live databases that have not yet received newer default-term columns.
            foreach (array_keys($values) as $settingsColumn) {
                if (! Schema::hasColumn('global_settings', $settingsColumn)) {
                    return Reply::dataOnly([
                        'status' => 'warning',
                        'message' => 'This default term cannot be saved until the database column is added.',
                    ]);
                }
            }
            // dd($values);
            DB::table('email_notifications')->where('id', '=', $request->tokenId)->update($values);

            $response = $this->elasticService->getClient()->update([
                'index' => ELASTIC_EMAIL_NOTIFICATIONS,
                'id' => $request->tokenId,
                'retry_on_conflict' => 5,
                'body' => [
                    'doc' => [
                        'id' => $request->tokenId,
                        'purpose' => $request->purpose,
                        'subject' => $request->subject,
                        'from_email_id' => $request->from_email_id,
                        'body_description' => $request->editor1,
                        'status' => $request->status,
                        'mailType' => $request->mailType_is,
                        'updated_at' => now()->toIso8601String(),
                    ],
                    'doc_as_upsert' => true,
                    'detect_noop' => false,
                ],
            ]);

            DB::table('mail_permissions')->where('notification_id', '=', $request->tokenId)->delete();
            $this->elasticService->getClient()->deleteByQuery([
                'index' => ELASTIC_EMAIL_PERMISSIONS,
                'conflicts' => 'proceed',   // ⭐ THIS LINE FIXES YOUR ERROR
                'body' => [
                    'query' => [
                        'bool' => [
                            'must' => [
                                ['term' => ['notification_id' => $request->tokenId]],
                            ],
                        ],
                    ],
                ],
            ]);

            if (count($request->role_ck) > 0) {
                for ($i = 0; $i < count($request->role_ck); $i++) {
                    $rowId = DB::table('mail_permissions')->insertGetId(['role_name' => $request->role_ck[$i], 'notification_id' => $request->tokenId]);

                    $response = $this->elasticService->getClient()->update([
                        'index' => ELASTIC_EMAIL_PERMISSIONS,
                        'id' => $rowId,
                        'retry_on_conflict' => 5,
                        'body' => [
                            'doc' => [
                                'id' => $rowId,
                                'role_name' => $request->role_ck[$i],
                                'notification_id' => $request->tokenId,
                            ],
                            'doc_as_upsert' => true,
                            'detect_noop' => false,
                        ],
                    ]);
                }
            }
        }

        return Reply::dataOnly(['status' => 'success']);

    }

    public function defaultTerms(Request $request, $ecrypt_id)
    {
        $id = decrypt($ecrypt_id);
        $this->title = '';
        $this->used = '';
        $this->encrypt_id = $ecrypt_id;
        if ($id == 1) {
            $this->th_email_template = DB::table('global_settings')->select('global_settings.terms')->first();
            $this->title = 'Vendor Terms';
            $this->used = 'For Vendors';
        } elseif ($id == 2) {
            $this->th_email_template = DB::table('global_settings')->select('global_settings.offer_terms as terms')->first();
            $this->title = 'Fulltime Offer Letter';
            $this->used = 'For Candidates';
        } elseif ($id == 3) {
            $this->th_email_template = DB::table('global_settings')->select('global_settings.privacy_policy as terms')->first();
            $this->title = 'Privacy policy';
            $this->used = 'For Website';
        } elseif ($id == 4) {
            $this->th_email_template = DB::table('global_settings')->select('global_settings.terms_and_conditions as terms')->first();
            $this->title = 'Terms and Conditions';
            $this->used = 'For Website';
        } elseif ($id == 5) {
            $this->th_email_template = DB::table('global_settings')->select('global_settings.payment_policy as terms')->first();
            $this->title = 'Payment policy';
            $this->used = 'For Website';
        } elseif ($id == 6) {
            $this->th_email_template = DB::table('global_settings')->select('global_settings.fraud_alery as terms')->first();
            $this->title = 'Fraud Alery';
            $this->used = 'For Website';
        } elseif ($id == 7) {
            $this->th_email_template = DB::table('global_settings')->select('global_settings.trust_and_safety as terms')->first();
            $this->title = 'Trust and Safety';
            $this->used = 'For Website';
        } elseif ($id == 8) {
            $this->th_email_template = DB::table('global_settings')->select('global_settings.report_issue as terms')->first();
            $this->title = 'Report Issue';
            $this->used = 'For Website';
        } elseif ($id == 9) {
            $this->th_email_template = DB::table('global_settings')->select('global_settings.grievances as terms')->first();
            $this->title = 'Grievances';
            $this->used = 'For Website';
        } elseif ($id == 10) {
            // Intent: support older live DBs that do not yet have the extra offer-template columns.
            $offerTermsColumn = Schema::hasColumn('global_settings', 'agency_offer_terms') ? 'agency_offer_terms' : 'offer_terms';
            $this->th_email_template = DB::table('global_settings')->select(DB::raw("COALESCE(NULLIF(global_settings.{$offerTermsColumn}, ''), global_settings.offer_terms) as terms"))->first();
            $this->title = 'Agency Offer terms';
            $this->used = 'For Candidates';
        } elseif ($id == 11) {
            // Intent: expose the internship offer-letter master text with Candidate Offer terms as the editable starter copy.
            $offerTermsColumn = Schema::hasColumn('global_settings', 'internship_offer_terms') ? 'internship_offer_terms' : 'offer_terms';
            $this->th_email_template = DB::table('global_settings')->select(DB::raw("COALESCE(NULLIF(global_settings.{$offerTermsColumn}, ''), global_settings.offer_terms) as terms"))->first();
            $this->title = 'Internship Offer Letter';
            $this->used = 'For Candidates';
        } elseif ($id == 12) {
            // Intent: expose the contract offer-letter master text with Candidate Offer terms as the editable starter copy.
            $offerTermsColumn = Schema::hasColumn('global_settings', 'contract_offer_terms') ? 'contract_offer_terms' : 'offer_terms';
            $this->th_email_template = DB::table('global_settings')->select(DB::raw("COALESCE(NULLIF(global_settings.{$offerTermsColumn}, ''), global_settings.offer_terms) as terms"))->first();
            $this->title = 'Contract Offer Letter';
            $this->used = 'For Candidates';
        } elseif ($id == 13) {
            // Intent: let Super Admin maintain agency commercial defaults separately from client Services/Commercials.
            $this->agencyCommercialDefaults = app(AgencyCommercialTermsService::class)->getGlobalDefault();
            $this->th_email_template = (object) ['terms' => ''];
            $this->title = 'Agency Commercial Defaults';
            $this->used = 'For Agencies';
        }

        return view('super-admin.default-terms', $this->data);
        // default-terms-list.blade.php
    }

    public function defaultTermsList()
    {
        $this->th_email_template = DB::table('global_settings')->select('global_settings.*')->first();

        return view('super-admin.default-terms-list', $this->data);
        // default-terms-list.blade.php
    }

    public function addEditTermsupdate(Request $request)
    {
        // dd($request->role_ck[0],$request->tokenId);

        $id = decrypt($request->encrypt_id);
        if ($id == 1) {
            $values = [
                'terms' => $request->editor1,
            ];
        } elseif ($id == 2) {
            $values = [
                'offer_terms' => $request->editor1,
            ];
        } elseif ($id == 3) {
            $values = [
                'privacy_policy' => $request->editor1,
            ];
        } elseif ($id == 4) {
            $values = [
                'terms_and_conditions' => $request->editor1,
            ];
        } elseif ($id == 5) {
            $values = [
                'payment_policy' => $request->editor1,
            ];
        } elseif ($id == 6) {
            $values = [
                'fraud_alery' => $request->editor1,
            ];
        } elseif ($id == 7) {
            $values = [
                'trust_and_safety' => $request->editor1,
            ];
        } elseif ($id == 8) {
            $values = [
                'report_issue' => $request->editor1,
            ];
        } elseif ($id == 9) {
            $values = [
                'grievances' => $request->editor1,
            ];
        } elseif ($id == 10) {
            $values = [
                'agency_offer_terms' => $request->editor1,
            ];
        } elseif ($id == 11) {
            // Intent: persist the internship default offer template into the existing global settings singleton.
            $values = [
                'internship_offer_terms' => $request->editor1,
            ];
        } elseif ($id == 12) {
            // Intent: persist the contract default offer template into the existing global settings singleton.
            $values = [
                'contract_offer_terms' => $request->editor1,
            ];
        } elseif ($id == 13) {
            // Intent: persist global agency commercial defaults in the agency-only terms table using company_id 0.
            $agencyExperienceRows = function (array $labels, array $rates): array {
                $rows = [];
                foreach ($labels as $index => $label) {
                    $label = trim((string) $label);
                    if ($label === '') {
                        continue;
                    }
                    $rows[] = [
                        'label' => $label,
                        'rate' => $rates[$index] ?? '',
                    ];
                }

                return $rows;
            };
            $payload = [
                'company_id' => 0,
                'status' => 'active',
                'version' => '1.0',
                'fulltime_fee_model' => $request->input('fulltime_fee_model', 'percentage'),
                'fulltime_success_fee_percentage' => (float) $request->input('fulltime_success_fee_percentage', 8.33),
                'fulltime_payment_terms_days' => (int) $request->input('fulltime_payment_terms_days', 30),
                'fulltime_replacement_period_days' => (int) $request->input('fulltime_replacement_period_days', 90),
                'contract_billing_model' => $request->input('contract_billing_model', 'monthly'),
                'contract_rate_amount' => (float) $request->input('contract_rate_amount', 80000),
                'contract_rate_currency' => (int) $request->input('contract_rate_currency', 4),
                'contract_payment_terms_days' => (int) $request->input('contract_payment_terms_days', 30),
                'contract_min_duration_months' => (int) $request->input('contract_min_duration_months', 3),
                'contract_replacement_policy' => $request->input('contract_replacement_policy', 'As per Contract Terms'),
                'applies_to' => 'All Agencies',
                'acceptance_required' => 1,
                'acceptance_trigger' => 'Before Candidate Submission',
                'submission_before_acceptance' => 0,
                'effective_from' => 'Agency Acceptance Date',
                'agreement_duration_months' => 12,
                'auto_renewal' => 1,
                'agency_can_reject' => 1,
                'updated_by' => auth()->id(),
                'updated_by_name' => auth()->user()->name ?? null,
                'settings_json' => json_encode([
                    'fulltime_fixed_fee_currency' => (int) $request->input('fulltime_fixed_fee_currency', 4),
                    'fulltime_fixed_fee_amount' => $request->filled('fulltime_fixed_fee_amount') ? (float) $request->input('fulltime_fixed_fee_amount') : null,
                    'fulltime_fixed_fee_unit' => $request->input('fulltime_fixed_fee_unit', 'Per Hire'),
                    'fulltime_experience_pricing' => $agencyExperienceRows((array) $request->input('fulltime_experience_label', []), (array) $request->input('fulltime_experience_rate', [])),
                    'fulltime_basis' => $request->input('fulltime_basis', 'Annual Fixed CTC'),
                    'contract_rate_display' => $request->input('contract_rate_display', 'Depend upon job requirements'),
                    'contract_experience_pricing' => $agencyExperienceRows((array) $request->input('contract_experience_label', []), (array) $request->input('contract_experience_rate', [])),
                    'contract_billing_cycle' => $request->input('contract_billing_cycle', 'Monthly in Advance'),
                ]),
            ];
            app(AgencyCommercialTermsService::class)->upsertGlobalDefault($payload);

            return Reply::dataOnly(['status' => 'success']);
        }

        // dd($values);
        // DB::table('email_notifications')->where('id','=',$request->tokenId)->update($values);
        DB::table('global_settings')
            ->where('id', DB::table('global_settings')->min('id'))
            ->update($values);

        return Reply::dataOnly(['status' => 'success']);

    }

    public function bernchNewCandidates(Request $request)
    {
        // dd($request->status);

        $valCheck = $request->input('sortingfilter');
        $country = '';
        $searchQuery = $request->input('q');
        $page = max((int) $request->input('page', 1), 1);
        $size = (int) $request->input('size', 10); // Default 20 results per page
        $from = ($page - 1) * $size;
        $requiredSkills = [];
        $sort = [];
        $status = 2;
        $this->reject = true;
        $this->showAction = true;
        $statusFilter = [];

        // 1. Sort: ads_switch 'Yes' on top
        $sort[] = [
            '_script' => [
                'type' => 'number',
                'script' => [
                    'lang' => 'painless',
                    'source' => "
						if (!doc.containsKey('ads_switch') || doc['ads_switch'].size() == 0) {
							return 1;
						}
						return doc['ads_switch'].value.toLowerCase() == 'yes' ? 0 : 1;
					",
                ],
                'order' => 'asc',
            ],
        ];

        // 2. Sort: skill match count (highest first)
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

        // 3. Sort: based on selected filter value
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
                $sort[] = [
                    '_script' => [
                        'type' => 'number',
                        'script' => [
                            'lang' => 'painless',
                            'source' => "
								if (!doc.containsKey('np_id.keyword') || doc['np_id.keyword'].size() == 0 || doc['np_id.keyword'].value == 'Immediate') {
									return 999999;
								}
								try {
									String[] parts = doc['np_id.keyword'].value.splitOnToken(',');
									if (parts.length > 0) {
										return Double.parseDouble(parts[0].trim());
									}
								} catch (Exception e) {}
								return 999999;
							",
                        ],
                        'order' => 'asc',
                    ],
                ];
                break;
            case 6:
                $sort[] = [
                    '_script' => [
                        'type' => 'number',
                        'script' => [
                            'lang' => 'painless',
                            'source' => "
								if (!doc.containsKey('np_id.keyword') || doc['np_id.keyword'].size() == 0 || doc['np_id.keyword'].value == 'Immediate') {
									return -1;
								}
								try {
									String[] parts = doc['np_id.keyword'].value.splitOnToken(',');
									if (parts.length > 0) {
										return Double.parseDouble(parts[0].trim());
									}
								} catch (Exception e) {}
								return -1;
							",
                        ],
                        'order' => 'desc',
                    ],
                ];
                break;
            case 7:
                $sort[] = ['total_exp' => ['order' => 'desc', 'unmapped_type' => 'float']];
                break;
            case 8:
                $sort[] = ['total_exp' => ['order' => 'asc', 'unmapped_type' => 'float']];
                break;
            case 9:
                $sort[] = [
                    '_script' => [
                        'type' => 'number',
                        'script' => [
                            'lang' => 'painless',
                            'source' => "params['_source']['immediate_join'] == 'Immediate' ? 0 : 1",
                        ],
                        'order' => 'asc',
                    ],
                ];
                break;
            default:
                $sort[] = ['id' => ['order' => 'desc', 'unmapped_type' => 'long']];
                break;
        }

        if ($request->status === 'pending') {
            $status = 1;
            $this->status = 'All Bench Pending';
            $statusFilter[] = ['term' => ['superadminstatus' => 0]];
        } elseif ($request->status === 'approve') {
            $status = 2;
            $this->status = 'All Market Bench';
            $statusFilter[] = ['term' => ['superadminstatus' => 1]];
        } elseif ($request->status === 'reject') {
            $this->status = 'All Reject Bench';
            $this->reject = false;
            $statusFilter[] = ['term' => ['superadminstatus' => 2]];
        } elseif ($request->status === 'place') {
            $status = 3;
            $this->showAction = false;
            $this->status = 'All Place Bench';
        } elseif ($request->status === 'pool') {
            $status = 1;
            $this->showAction = false;
            $this->status = 'All Bench Pool';
        }

        $statusFilter[] = ['term' => ['status' => $status]];

        $boolQuery = [
            'bool' => [
                'filter' => $statusFilter,
            ],
        ];

        $params = [
            'index' => ELASTIC_CANDIDATES_BENCH,
            'request_cache' => true,
            'body' => [
                'from' => $from,
                'size' => $size,
                'sort' => $sort,
                'query' => $boolQuery,
            ],
        ];

        if (! $this->elasticService->indexExists(ELASTIC_CANDIDATES_BENCH)) {
            $this->total = 0;
            $this->newCandidates = [];
        } else {
            $response = $this->elasticService->getClient()->search($params);
            $this->total = $response['hits']['total']['value'] ?? 0;
            $this->newCandidates = array_map(function ($hit) {
                return $hit['_source'];
            }, $response['hits']['hits']);
        }
        $this->statusType = $request->status;
        $pagination = [
            'total' => $this->total,
            'current_page' => $page,
            'last_page' => ceil($this->total / $size),
            'listType' => 'allcandidate',
        ];

        return view('super-admin.bench-lists.index', $this->data);

        //    $this->newCandidates =  JobApplication::paginate(10);

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
                'companies.company_name',
                'job_applications.superadminstatus',
                'qualification_master.name as qualification_name',
                'bench_master_status.name as master_status_name',
                'bench_master_status.css_style'

                // DB::raw("COUNT(add_job_wise_condidates.id) as allCandidate"),
                // DB::raw("SUM(CASE WHEN add_job_wise_condidates.master_status_id = 1 THEN 1 ELSE 0 END) AS Sourcing"),
                // DB::raw("SUM(CASE WHEN add_job_wise_condidates.master_status_id = 2 THEN 1 ELSE 0 END) AS Screening"),
                // DB::raw("SUM(CASE WHEN add_job_wise_condidates.master_status_id = 3 THEN 1 ELSE 0 END) AS Interview"),
                // DB::raw("SUM(CASE WHEN add_job_wise_condidates.master_status_id = 4 THEN 1 ELSE 0 END) AS Negotiation"),
                // DB::raw("SUM(CASE WHEN add_job_wise_condidates.master_status_id = 5 THEN 1 ELSE 0 END) AS Offered"),
                // DB::raw("SUM(CASE WHEN add_job_wise_condidates.master_status_id = 6 THEN 1 ELSE 0 END) AS Joined"),
                // DB::raw("SUM(CASE WHEN add_job_wise_condidates.master_status_id = 7 THEN 1 ELSE 0 END) AS Rejected"),
                // DB::raw("SUM(CASE WHEN add_job_wise_condidates.master_status_id = 8 THEN 1 ELSE 0 END) AS InReview")

            );
        // $benchApplications = $benchApplications->leftjoin('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id');
        $benchApplications = $benchApplications->leftjoin('bench_master_status', 'bench_master_status.id', 'job_applications.status');
        $benchApplications = $benchApplications->leftjoin('companies', 'companies.id', 'job_applications.company_id');
        $benchApplications = $benchApplications->leftjoin('qualification_master', 'qualification_master.id', 'job_applications.qualification');
        $status = 2;
        $this->reject = true;
        $this->showAction = true;
        if ($request->status == 'pending') {
            $status = 1;
            $benchApplications = $benchApplications->where('job_applications.superadminstatus', '=', 0);
            $this->status = 'All Bench Pending';
        } elseif ($request->status == 'approve') {
            $status = 2;
            $benchApplications = $benchApplications->where('job_applications.superadminstatus', '=', 1);
            $this->status = 'All Market Bench';
        } elseif ($request->status == 'reject') {
            $benchApplications = $benchApplications->where('job_applications.superadminstatus', '=', 2);
            $this->status = 'All Reject Bench';
            $this->reject = false;
        } elseif ($request->status == 'place') {
            $status = 3;
            $this->showAction = false;
            $this->status = 'All Place Bench';
        } elseif ($request->status == 'pool') {
            $status = 1;
            $this->showAction = false;
            $this->status = 'All Bench Pool';
        }
        $benchApplications = $benchApplications->where('job_applications.status', '=', $status);
        // dd($benchApplications->get());
        $this->total = count($benchApplications->get());
        $this->newCandidates = $benchApplications->paginate(10);
        //    dd($this->newCandidates);

        return view('super-admin.bench-lists.index', $this->data);

    }

    public function ajaxbenchNewCandidates(Request $request)
    {

        $valCheck = $request->input('sortingfilter');
        $country = '';
        $searchQuery = $request->input('q');
        $page = max((int) $request->input('page', 1), 1);
        $size = (int) $request->input('size', 10); // Default 20 results per page
        $from = ($page - 1) * $size;
        $requiredSkills = [];
        $sort = [];
        $status = 2;
        $this->reject = true;
        $this->showAction = true;
        $statusFilter = [];

        // 1. Sort: ads_switch 'Yes' on top
        $sort[] = [
            '_script' => [
                'type' => 'number',
                'script' => [
                    'lang' => 'painless',
                    'source' => "
						if (!doc.containsKey('ads_switch') || doc['ads_switch'].size() == 0) {
							return 1;
						}
						return doc['ads_switch'].value.toLowerCase() == 'yes' ? 0 : 1;
					",
                ],
                'order' => 'asc',
            ],
        ];

        // 2. Sort: skill match count (highest first)
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

        // 3. Sort: based on selected filter value
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
                $sort[] = [
                    '_script' => [
                        'type' => 'number',
                        'script' => [
                            'lang' => 'painless',
                            'source' => "
								if (!doc.containsKey('np_id.keyword') || doc['np_id.keyword'].size() == 0 || doc['np_id.keyword'].value == 'Immediate') {
									return 999999;
								}
								try {
									String[] parts = doc['np_id.keyword'].value.splitOnToken(',');
									if (parts.length > 0) {
										return Double.parseDouble(parts[0].trim());
									}
								} catch (Exception e) {}
								return 999999;
							",
                        ],
                        'order' => 'asc',
                    ],
                ];
                break;
            case 6:
                $sort[] = [
                    '_script' => [
                        'type' => 'number',
                        'script' => [
                            'lang' => 'painless',
                            'source' => "
								if (!doc.containsKey('np_id.keyword') || doc['np_id.keyword'].size() == 0 || doc['np_id.keyword'].value == 'Immediate') {
									return -1;
								}
								try {
									String[] parts = doc['np_id.keyword'].value.splitOnToken(',');
									if (parts.length > 0) {
										return Double.parseDouble(parts[0].trim());
									}
								} catch (Exception e) {}
								return -1;
							",
                        ],
                        'order' => 'desc',
                    ],
                ];
                break;
            case 7:
                $sort[] = ['total_exp' => ['order' => 'desc', 'unmapped_type' => 'float']];
                break;
            case 8:
                $sort[] = ['total_exp' => ['order' => 'asc', 'unmapped_type' => 'float']];
                break;
            case 9:
                $sort[] = [
                    '_script' => [
                        'type' => 'number',
                        'script' => [
                            'lang' => 'painless',
                            'source' => "params['_source']['immediate_join'] == 'Immediate' ? 0 : 1",
                        ],
                        'order' => 'asc',
                    ],
                ];
                break;
            default:
                $sort[] = ['id' => ['order' => 'desc', 'unmapped_type' => 'long']];
                break;
        }

        if ($request->status === 'pending') {
            $status = 1;
            $this->status = 'All Bench Pending';
            $statusFilter[] = ['term' => ['superadminstatus' => 0]];
        } elseif ($request->status === 'approve') {
            $status = 2;
            $this->status = 'All Market Bench';
            $statusFilter[] = ['term' => ['superadminstatus' => 1]];
        } elseif ($request->status === 'reject') {
            $this->status = 'All Reject Bench';
            $this->reject = false;
            $statusFilter[] = ['term' => ['superadminstatus' => 2]];
        } elseif ($request->status === 'place') {
            $status = 3;
            $this->showAction = false;
            $this->status = 'All Place Bench';
        } elseif ($request->status === 'pool') {
            $status = 1;
            $this->showAction = false;
            $this->status = 'All Bench Pool';
        }

        $statusFilter[] = ['term' => ['status' => $status]];

        $boolQuery = [
            'bool' => [
                'filter' => $statusFilter,
            ],
        ];

        $params = [
            'index' => ELASTIC_CANDIDATES_BENCH,
            'request_cache' => true,
            'body' => [
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

        $pagination = [
            'total' => $total,
            'current_page' => $page,
            'last_page' => ceil($total / $size),
            'listType' => 'allcandidate',
        ];

        $userPermissions = [];

        return response()->json([
            'html' => view('super-admin.ajax_pages.bench_candidates_ajax', compact('data', 'userPermissions'))->render(),
            'total' => $total,
            'pagination' => view('super-admin.ajax_pages.pagination', compact('pagination'))->render(),
        ]);

    }

    public function candidateApproval(Request $request)
    {

        // dd($request->candidate_id);

        if ($request->datatype == 'approve') {
            if (count($request->candidate_id) > 0) {
                DB::table('job_applications')->whereIn('id', $request->candidate_id)->update(['superadminstatus' => 1]);

                $basecompany = GeneralMethod::baseCompany();
                $senderName = ($basecompany->companyName) ? $basecompany->companyName : '';
                $fromAddress = ($basecompany->companyEmail) ? $basecompany->companyEmail : '';
                $attachments = [];
                $benchstatus = 'Live on Platform';
                $cc = [];
                for ($i = 0; $i < count($request->candidate_id); $i++) {
                    $rowdata = DB::table('job_applications')->join('companies', 'companies.id', '=', 'job_applications.company_id')->select('job_applications.*', 'companies.company_name')->where('job_applications.id', $request->candidate_id[$i])->first();
                    $checkMailPermision = GeneralMethod::mailPermission(27);
                    $mailTypeSubject = GeneralMethod::mailTypeSubject(27, 2);
                    if ($checkMailPermision) {
                        $subject = str_replace(['[{BENCH_CANDIDATE_NAME}]', '[{STATUS}]'], [$rowdata->full_name, $benchstatus], $mailTypeSubject->subject);
                        $template_description = $mailTypeSubject->body_description;
                        $template_description = str_replace(['[{VENDOR_COMPANY_NAME}]', '[{BENCH_CANDIDATE_NAME}]', '[{BENCH_ID}]'], [$rowdata->company_name, $rowdata->full_name, $rowdata->id], $template_description);

                        $message = $template_description;

                        $notificationemail = GeneralMethod::notification($rowdata->company_id, 'Sales'); // Recuitment Sales
                        $defailsEmail = DB::table('companies')->where('id', $rowdata->company_id)->first()->company_email;
                        $to_Mail_Id = $notificationemail->email ?? $defailsEmail ?? '';
                        $datamail = [
                            'message' => $message,
                        ];
                        $result = $this->mailService->sendEmail($to_Mail_Id, $subject, 'email.common.index', (object) $datamail, $attachments, $fromAddress, $senderName, $cc);
                        // dd($to_Mail_Id,$result);

                    }

                }

            }

            $message = 'Selected Applicant Approved.';
        } else {
            // DB::table('job_applications')->whereIn('id',$request->candidate_id)->update(['superadminstatus'=>2]);
            $message = 'Selected Applicant Rejected.';
        }

        $data = ['status' => 'success', 'message' => $message];

        return response()->json($data);

    }

    public function autoApproval(Request $request)
    {

        // dd($request->candidate_id);
        $bench_status = DB::table('global_settings')->select('bench_status')->where('id', '=', $request->id)->first();
        $superadminstatus = 1;
        if ($bench_status) {
            if ($bench_status->bench_status) {
                $superadminstatus = 0;
            }
        }
        DB::table('global_settings')->where('id', $request->id)->update(['bench_status' => $superadminstatus]);

        $message = 'Changed setting auto approval.';

        $data = ['status' => 'success', 'message' => $message];

        return response()->json($data);

    }

    public function reject_mail(Request $request)
    {

        // echo $request->id; exit;
        $arr = '';
        // dd($request->arr);
        $this->arr = $request->arr;
        $this->jobIdArr = $request->jobIdArr;
        $this->applicationDataCount = DB::table('job_applications')->select('*')->whereIn('id', $this->arr)->count();
        // $this->applicationData			=	DB::table('job_applications')->select('job_applications.id')->whereIn('id', $arr)->get();

        $this->applicationData = DB::table('job_applications')
            ->select(
                'job_applications.id',
                'job_applications.full_name',
                'job_applications.current_designation',
                'job_applications.phone',
                'job_applications.email',
                'job_applications.total_exp',
                'job_applications.rel_exp',
                'job_applications.ctc',
                'job_applications.ectc',
                'job_applications.current_ctc_cur',
                'job_applications.current_ectc_cur',
                'job_applications.current_ctc_mode',
                'job_applications.current_ectc_cur',
                'job_applications.np_id',
                'job_applications.serving_notice_period_mode',
                'job_applications.current_company',
                'job_applications.skills',
                'qualification_master.name as qualification_name',
                'jobs.title as job_title',
                'clients.name as client_name'
            )
            // ->leftjoin('jobs', 'jobs.id', 'job_applications.job_id')
            ->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id')
            ->join('jobs', function ($join) {
                $join->on('jobs.id', 'add_job_wise_condidates.job_id');
            })
            ->leftjoin('clients', 'clients.id', 'jobs.client_id')
            ->leftjoin('qualification_master', 'qualification_master.id', 'job_applications.qualification')
            ->whereIn('job_applications.id', $this->arr)->groupBy('job_applications.id')->get();

        $this->templateData = DB::table('th_email_template')->select('*')->where('th_email_template.status', 1)->where('th_email_template.temptype', 5)->get();

        $this->userdata = $this->user;

        $this->to_email_from_count = $this->cc_email_count = 0;
        $this->to_email_from_data = [];
        $this->cc_email_data = [];
        $commonFromArr = [];
        $commonCcArr = [];

        if ($request->id > 1) {

            $client_row = DB::table('jobs')->select('jobs.client_id')->where('jobs.id', $request->id)->first();
            // CLIENT CONTACT INFO
            $ccInfoCount = 0;

            $this->to_email_from_count = $ccInfoCount;
            $this->to_email_from_data = $commonFromArr;

            // HR ASSIGN JOB
            $assignData = [];

            $i = 0;
            foreach ($this->applicationData as $aval) {
                $commonCcArr[$i] = $aval->email;
                $i++;
            }

            $commonCcArr = DB::table('bench_assign')->join('users', 'users.id', '=', 'bench_assign.user_id')->whereIn('bench_assign.bench_applications_id', $this->arr)->pluck('users.email')->toArray();

            // dd($hmData);

            $assignDataclient = [];

            $assignData = array_merge($assignDataclient, $assignData);

            $this->cc_email_count = count($commonCcArr);
            $this->cc_email_data = $commonCcArr;
            $this->cc_email_assignData = (object) $assignData;
        }

        $company_id = auth()->user()->company_id;
        $company_name = DB::table('companies')->select('companies.company_name')->where('id', '=', $company_id)->first();
        $this->company_name = (@$company_name->company_name) ? @$company_name->company_name : '';

        $view = view('super-admin.bench-lists.reject_mail', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'view' => $view]);

    }

    public function email_template(Request $request)
    {
        $salaryMode = GeneralMethod::salaryMode();
        $currencySymbole = GeneralMethod::currencySymbole();
        $selectedApplicantListing = $applicationDataCount = $applicationData = '';
        $selectedApplicantListing = $request->selectedApplicantListing;
        // dd($selectedApplicantListing);
        $applicationDataCount = DB::table('job_applications')->select('*')->whereIn('id', $selectedApplicantListing)->count();
        $applicationData = DB::table('job_applications')
            ->select(
                'job_applications.id',
                'job_applications.full_name',
                'job_applications.current_designation',
                'job_applications.phone',
                'job_applications.email',
                'job_applications.total_exp',
                'job_applications.rel_exp',
                'job_applications.ctc',
                'job_applications.ectc',
                'job_applications.city_id',
                'job_applications.city',
                'job_applications.country',
                'job_applications.state',
                'job_applications.current_ctc_cur',
                'job_applications.current_ectc_cur',
                'job_applications.current_ctc_mode',
                'job_applications.current_ectc_cur',
                'job_applications.np_id',
                'job_applications.serving_notice_period_mode',
                'job_applications.current_company',
                'job_applications.skills',
                'qualification_master.name as qualification_name',
                'jobs.title as job_title',
                'clients.name as client_name'
            )
            // ->join('jobs', 'jobs.id', 'job_applications.job_id')
            ->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id')
            ->join('jobs', function ($join) {
                $join->on('jobs.id', 'add_job_wise_condidates.job_id');
            })
            ->leftjoin('clients', 'clients.id', 'jobs.client_id')
            ->leftjoin('qualification_master', 'qualification_master.id', 'job_applications.qualification')
            ->whereIn('job_applications.id', $selectedApplicantListing)->groupBy('job_applications.id')->get();

        // 			dd($applicationData[0]->full_name);

        $template = '';
        // dd($request->template);
        $template = (int) $request->template;
        $templateData = DB::table('th_email_template')->select('*')->where('th_email_template.status', 1)->where('th_email_template.e_temp_id', '=', $template)->first();

        $this->job = Job::select(
            'jobs.id',
            'jobs.title',
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
            'clients.logo_logical_path'
        )
            ->join('clients', 'clients.id', 'jobs.client_id')
            ->leftJoin('job_applications', 'job_applications.job_id', '=', 'jobs.id')
            ->where('jobs.id', '>', '0')
            ->groupBy('jobs.id')
            ->orderBy('jobs.id', 'DESC')
            ->get();

        $tracker_html = '';

        if ($applicationDataCount > 0) {
            $cnt = 1;
            foreach ($applicationData as $appData) {

                $bgTextDecore = $strVal = '';
                $str = trim(@$appData->full_name);
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

                $current_ctc_cur = $current_ectc_cur = $ctc = $ectc = '';
                $current_ctc_cur = $currencySymbole[@$appData->current_ctc_cur];
                $current_ectc_cur = $currencySymbole[@$appData->current_ectc_cur];
                $ctc = GeneralMethod::thousandsCurrencyFormat($appData->ctc);
                $ectc = GeneralMethod::thousandsCurrencyFormat($appData->ectc);

                $np = $appData->np_id;
                if (empty($np)) {
                    $np = 'N/A';
                }
                if ($appData->serving_notice_period_mode == 0) {
                    if (! empty($appData->np_id)) {
                        $np = $appData->np_id.'  Days';
                    }
                } else {
                    $np = $appData->np_id;
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
                    if (@$appData->country != 101) {
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
                    $row_city = DB::table('city_list')->select('*')->where('city_id', @$appData->city_id)->first();
                    $row_city_name = @$row_city->city;
                } else {
                    $row_city_name = @$appData->city;
                }

                $cnt = $cnt + 1;
            }
        }

        // 		$to = $request->to_email;
        // 		if(is_array($to)){
        // 		  $to = json_decode($to, true);
        // 		  $to = array_column($to, 'value');
        // 		  $to = implode(',', $to);
        // 		}else{

        // 		}

        // $assignData = DB::table('job_assign')->join('users', 'users.id', '=', 'job_assign.user_id')->select('users.email','users.name')->where('users.email', $to)->first();

        $EMAIL_TRACKER_HR = ''; // @$assignData->name;

        $template_description = $templateData->template_description;
        // dd($template_description);
        $template_description = str_replace(['[{EMAIL_TRACKER}]', '[{EMAIL_TRACKER_HR}]'], [$tracker_html, $EMAIL_TRACKER_HR], $template_description);
        $subject = '';
        $whatlink = '';
        //  dd((int)$request->mailtype);
        if ((int) $request->mailtype == 1) {
            $companyDetails = Company::join('users_profile', 'users_profile.company_id', 'companies.id')->select('users_profile.designation', 'companies.company_name')
                ->where('users_profile.user_id', auth()->user()->id)->where('users_profile.company_id', auth()->user()->company_id)->first();
            // dd($companyDetails->designation);
            $subject = str_replace(['[{COMPANY_NAME}]'], 'aaaaaaa', $templateData->template_subject);

            //	dd($template_description);
            $template_description = str_replace(['[{COMPANY_NAME}]', '[{HR_PHONE}]', '[{HR_NAME}]', '[{HR_EMAIL}]', '[{POSITION}]'], [$companyDetails->company_name, auth()->user()->mobile, auth()->user()->name, auth()->user()->email, $companyDetails->designation], $template_description);
        }
        if ((int) $request->mailtype == 2) {
            // dd($company_name->company_name, auth()->user()->phone, auth()->user()->name, auth()->user()->email);
            $subject = str_replace(['[{COMPANY_NAME}]'], 'aaaaaaa', $templateData->template_subject);
            $whatlink = '<a href="https://wa.me/'.auth()->user()->mobile.'" rel="noreferrer noopener" target="_blank" title="https://wa.me/'.auth()->user()->mobile.'">Connect To Wahtsapp</a>';
            $template_description = str_replace(['[{COMPANY_NAME}]', '[{HR_PHONE}]', '[{HR_NAME}]', '[{HR_EMAIL}]', '[{WHAT_LINK}]'], ['aaaaaa', auth()->user()->mobile, auth()->user()->name, auth()->user()->email, $whatlink], $template_description);
        }
        if ((int) $request->mailtype == 3) {

            $subject = str_replace(['[{COMPANY_NAME}]'], 'aaaaaaa', $templateData->template_subject);
        }
        if ((int) $request->mailtype == 4) {

            $country_id = $country_name = '';
            $jobid = $request->jobID;
            $client_loc = DB::table('client_location_jobs')
                ->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
                ->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')
                ->select('client_location.address', 'client_location.city', 'client_location.postcode', 'client_location.country_id', 'city_list.city as city_name')
                ->where('client_location_jobs.job_id', $jobid)
                ->first();

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
            $jobs = Job::withoutGlobalScopes()->find($jobid);

            $companyOrClientName = GeneralMethod::companyOrClientName($jobs->client_id);

            $location = $city.' | '.$country_name;

            // dd($templateData->template_subject);
            if ($templateData->tempForInterview == 1) {
                $subject = str_replace(['[{COMPANY_NAME}]', '[{JOB_TITLE}]', '[{JOB_LOCATION}]'], [$companyOrClientName->company, $jobs->title, $location], $templateData->template_subject);
            } elseif ($templateData->tempForInterview == 2) {
                $subject = str_replace(['[{CLIENT_NAME}]', '[{JOB_TITLE}]', '[{JOB_LOCATION}]'], [$companyOrClientName->client, $jobs->title, $location], $templateData->template_subject);
            } elseif ($templateData->tempForInterview == 3) {
                $subject = str_replace(['[{CLIENT_NAME}]', '[{COMPANY_NAME}]', '[{JOB_LOCATION}]'], [$companyOrClientName->client, $companyOrClientName->company, $location], $templateData->template_subject);
            } elseif ($templateData->tempForInterview == 4) {
                //  Documents Required | [{COMPANY_NAME}]
                $subject = str_replace(['[{COMPANY_NAME}]'], [$companyOrClientName->company], $templateData->template_subject);
            }

            $template_description = str_replace(['[{COMPANY_NAME}]', '[{HR_PHONE}]', '[{HR_NAME}]', '[{HR_EMAIL}]', '[{WHAT_LINK}]', '[{CLIENT_NAME}]', '[{JOB_TITLE}]', '[{JOB_LOCATION}]', '[{CLIENT_WEBSITE}]', '[{JOB_DESCRIPTION}]', '[{COMPANY_WEBSITE}]', '[{COMPANY_DESCRIPTION}]', '[{CLIENT_DESCRIPTION}]', '[{NO_OF_EMP}]', '[{CANDIDATE_NAME}]'], [$companyOrClientName->company, auth()->user()->mobile, auth()->user()->name, auth()->user()->email, $whatlink, $companyOrClientName->client, $jobs->title, $location, $companyOrClientName->client_website, $jobs->job_description, $companyOrClientName->company_website, $companyOrClientName->company_description, $companyOrClientName->client_description, $companyOrClientName->company_size, $applicationData[0]->full_name], $template_description);

        }

        // $view = view('service_company.cvtracker.fwd_resume', $this->data)->render();
        return Reply::dataOnly(['status' => 'success', 'template_description' => $template_description, 'subject' => $subject]);

    }

    public function reject_mail_send(Request $request)
    {

        // dd($request->all());
        $salaryMode = GeneralMethod::salaryMode();
        $currencySymbole = GeneralMethod::currencySymbole();
        $selectedApplicantListing = $applicationDataCount = $applicationData = '';
        $selectedApplicantListing = $request->selectedApplicantListing;

        $template = $from = $to = $cc = $subject = $attached_cv = $message = $arr = '';
        $this->status = false;

        $template = $request->template;
        $from = $request->from_email;
        $to = $request->to_email;
        $cc = $request->cc_email;

        $subject = $request->subject;
        $attached_cv = $request->attached_cv;
        $message = $request->editor1;
        $arr = $request->arr;

        $from = json_decode($from, true);
        $from = array_column($from, 'value');
        $from = implode(',', $from);

        $to = json_decode($to, true);
        $to = array_column($to, 'value');
        $to = implode(',', $to);

        if (! empty($cc)) {
            // $cc = json_decode($cc, true);
            // $cc = array_column($cc, 'value');
            $cc = $request->cc_email; // implode(',', $cc);
        }
        // dd($cc);
        $attachments = [];

        // dd( $request->arr);

        $mailto = $request->arr; // explode(",",$to);

        // dd($mailto);

        $company_id = auth()->user()->company_id;
        $company_name = DB::table('companies')->select('companies.company_name')->where('id', '=', $company_id)->first();

        // dd("dd",$mailto);
        $senderName = auth()->user()->name;

        if (count($mailto) > 0) {
            for ($k = 0; $k < count($mailto); $k++) {
                $cnt = 1;
                $condi = JobApplication::leftjoin('qualification_master', 'qualification_master.id', 'job_applications.qualification')->select('job_applications.*', 'qualification_master.name as qualification_name')->where('job_applications.id', '=', $mailto[$k])->first();

                $tracker_html = '';
                $tracker_html = '<table class="table align-middle table-row-dashed fs-6 gy-5 dataTable no-footer dtr-inline" id="myTable" role="grid" aria-describedby="myTable_info" style="width: 1272px;">
                        <thead>
				<tr style="mso-yfti-irow:0;mso-yfti-firstrow:yes;height:15.75pt"> 

				<td valign="bottom" style="border:solid windowtext 1.0pt;border-left:none; background:#BDD7EE;padding:1.5pt 2.25pt 1.5pt 2.25pt;height:15.75pt; overflow:hidden"> <p class="MsoNormal"><b><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">Name of the candidate<o:p></o:p></span></b></p></td>
                <td valign="bottom" style="border:solid windowtext 1.0pt;border-left:none; background:#BDD7EE;padding:1.5pt 2.25pt 1.5pt 2.25pt;height:15.75pt; overflow:hidden"> <p class="MsoNormal"><b><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">Current Position<o:p></o:p></span></b></p></td>
                <td valign="bottom" style="border:solid windowtext 1.0pt;border-left:none; background:#BDD7EE;padding:1.5pt 2.25pt 1.5pt 2.25pt;height:15.75pt; overflow:hidden"> <p class="MsoNormal"><b><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">Total Exp<o:p></o:p></span></b></p></td>
                <td valign="bottom" style="border:solid windowtext 1.0pt;border-left:none; background:#BDD7EE;padding:1.5pt 2.25pt 1.5pt 2.25pt;height:15.75pt; overflow:hidden"> <p class="MsoNormal"><b><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">Rel Exp<o:p></o:p></span></b></p></td>
                <td valign="bottom" style="border:solid windowtext 1.0pt;border-left:none; background:#BDD7EE;padding:1.5pt 2.25pt 1.5pt 2.25pt;height:15.75pt; overflow:hidden"> <p class="MsoNormal"><b><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">Salary<o:p></o:p></span></b></p></td>
                <td valign="bottom" style="border:solid windowtext 1.0pt;border-left:none; background:#BDD7EE;padding:1.5pt 2.25pt 1.5pt 2.25pt;height:15.75pt; overflow:hidden"> <p class="MsoNormal"><b><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">Availability<o:p></o:p></span></b></p></td>
                <td valign="bottom" style="border:solid windowtext 1.0pt;border-left:none; background:#BDD7EE;padding:1.5pt 2.25pt 1.5pt 2.25pt;height:15.75pt; overflow:hidden"> <p class="MsoNormal"><b><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">Qualification<o:p></o:p></span></b></p></td>
                <td valign="bottom" style="border:solid windowtext 1.0pt;border-left:none; background:#BDD7EE;padding:1.5pt 2.25pt 1.5pt 2.25pt;height:15.75pt; overflow:hidden"> <p class="MsoNormal"><b><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">Location<o:p></o:p></span></b></p></td>
                 <td valign="bottom" style="border:solid windowtext 1.0pt;background:#BDD7EE; padding:1.5pt 2.25pt 1.5pt 2.25pt;height:15.75pt"> <p class="MsoNormal"><b><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">Upload Date</span></b><b><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif;mso-fareast-language: EN-IN"><o:p></o:p></span></b></p></td>
                <td valign="bottom" style="border:solid windowtext 1.0pt;border-left:none; background:#BDD7EE;padding:1.5pt 2.25pt 1.5pt 2.25pt;height:15.75pt; overflow:hidden"> <p class="MsoNormal"><b><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">Status<o:p></o:p></span></b></p></td>
               
				
				
				
				</tr>			   
				</thead>';

                if ($condi) {
                    $appData = $condi;

                    $bgTextDecore = $strVal = '';
                    $str = trim(@$appData->full_name);
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

                    $current_ctc_cur = $current_ectc_cur = $ctc = $ectc = '';
                    $current_ctc_cur = $currencySymbole[@$appData->pay_rate_currency];
                    $current_ectc_cur = $currencySymbole[@$appData->bill_rate_currency];
                    $ctc = GeneralMethod::thousandsCurrencyFormat($appData->pay_rate_amount);
                    $ectc = GeneralMethod::thousandsCurrencyFormat($appData->bill_rate_amount);

                    $np = $appData->np_id;
                    if (empty($np)) {
                        $np = 'N/A';
                    }
                    if ($appData->serving_notice_period_mode == 0) {
                        if (! empty($appData->np_id)) {
                            $np = $appData->np_id.'  Days';
                        }
                    } else {
                        $np = $appData->np_id;
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
                        if (@$appData->country != 101) {
                            $row_state_name = @$appData->state_name;
                        } else {
                            $state = @$appData->state;
                            // dd($state);
                            if (is_numeric(@$appData->country) && is_numeric($state)) {
                                if ($state > 0) {
                                    $state_name_Arr = GeneralMethod::getStateName(@$appData->country, $state);
                                    // dd($state_name_Arr);
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
                        $row_city = DB::table('city_list')->select('*')->where('city_id', @$appData->city_id)->first();
                        // dd(@$appData->city_id,$row_city);
                        $row_city_name = @$row_city->city;
                    } else {
                        $row_city_name = @$appData->city;
                    }

                    $tracker_html .= '
				<tr style="mso-yfti-irow:1;mso-yfti-lastrow:yes;height:15.75pt"> 
				
				<td valign="bottom" style="border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt; border-right:solid windowtext 1.0pt;background:white;padding:1.5pt 2.25pt 1.5pt 2.25pt; height:15.75pt;overflow:hidden"> <p class="MsoNormal"><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">'.ucwords(@$appData->full_name).'<o:p></o:p></span></p></td>
                <td valign="bottom" style="border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt; border-right:solid windowtext 1.0pt;background:white;padding:1.5pt 2.25pt 1.5pt 2.25pt; height:15.75pt;overflow:hidden"> <p class="MsoNormal"><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">'.ucwords(@$appData->current_designation).'<o:p></o:p></span></p></td>
                <td valign="bottom" style="border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt; border-right:solid windowtext 1.0pt;background:white;padding:1.5pt 2.25pt 1.5pt 2.25pt; height:15.75pt;overflow:hidden"> <p class="MsoNormal"><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">'.@$appData->total_exp.'+<o:p></o:p></span></p></td>
				<td valign="bottom" style="border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt; border-right:solid windowtext 1.0pt;background:white;padding:1.5pt 2.25pt 1.5pt 2.25pt; height:15.75pt;overflow:hidden"> <p class="MsoNormal"><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">'.@$appData->rel_exp.'+<o:p></o:p></span></p></td>
				
				<td valign="bottom" style="border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt; border-right:solid windowtext 1.0pt;background:white;padding:1.5pt 2.25pt 1.5pt 2.25pt; height:15.75pt;overflow:hidden"> <p class="MsoNormal"><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">'.$current_ectc_cur.@$ectc.'<o:p></o:p></span></p></td>
				<td valign="bottom" style="border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt; border-right:solid windowtext 1.0pt;background:white;padding:1.5pt 2.25pt 1.5pt 2.25pt; height:15.75pt;overflow:hidden"> <p class="MsoNormal"><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">'.$np.'<o:p></o:p></span></p></td>
				<td valign="bottom" style="border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt; border-right:solid windowtext 1.0pt;background:white;padding:1.5pt 2.25pt 1.5pt 2.25pt; height:15.75pt;overflow:hidden"> <p class="MsoNormal"><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">'.ucwords(substr(@$appData->qualification_name, 0, 10)).'<o:p></o:p></span></p></td>
				<td valign="bottom" style="border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt; border-right:solid windowtext 1.0pt;background:white;padding:1.5pt 2.25pt 1.5pt 2.25pt; height:15.75pt;overflow:hidden"> <p class="MsoNormal"><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif"><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="'.ucwords($row_city_name).'">'.ucwords(substr($row_city_name, 0, 10)).'</a>
				<span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="'.ucwords($row_state_name).' | '.strtoupper($row_country_name).'">'.ucfirst(substr($row_state_name, 0, 5)).' | '.strtoupper(substr($row_country_name, 0, 2)).'</span><o:p></o:p></span></p></td>
                <td valign="bottom" style="border:solid windowtext 1.0pt;border-top:none; background:white;padding:1.5pt 2.25pt 1.5pt 2.25pt;height:15.75pt;overflow: hidden"> <p class="MsoNormal"><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">'.date('d-M-Y').'<o:p></o:p></span></p></td>
                <td valign="bottom" style="border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt; border-right:solid windowtext 1.0pt;background:white;padding:1.5pt 2.25pt 1.5pt 2.25pt; height:15.75pt;overflow:hidden"> <p class="MsoNormal"><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">Reject<o:p></o:p></span></p></td>
                
				</tr>';

                    $cnt = $cnt + 1;

                }

                $name = ($condi) ? $condi->full_name : '';
                // $template_description = $message;
                $template_description = str_replace(['[{CANDIDATE_TRACKER}]'], [$tracker_html], $message);

                // dd($message);

                $data = ['message' => $template_description, 'subject' => $subject, 'company_name' => '', 'consultant_name' => '4Bell Technology', 'position' => 'PHP', 'condidate_name' => $name];

                $basecompany = GeneralMethod::baseCompany();
                $senderName = ($basecompany->companyName) ? $basecompany->companyName : '';
                $fromAddress = ($basecompany->companyEmail) ? $basecompany->companyEmail : '';

                $commonCcArr = DB::table('bench_assign')->join('users', 'users.id', '=', 'bench_assign.user_id')->where('bench_assign.bench_applications_id', $mailto[$k])->pluck('users.email')->toArray();
                // dd($commonCcArr);

                $cc = [];
                $checkMailPermision = GeneralMethod::mailPermission(16);
                if ($checkMailPermision) {
                    foreach ($commonCcArr as $tomail) {
                        $result = $this->mailService->sendEmail($tomail, $subject, 'email.notification', $data, $attachments, $fromAddress, $senderName, $cc);
                    }

                    // dd($result);
                }

                DB::table('job_applications')->where('id', $mailto[$k])->update(['superadminstatus' => 2]);

            }
        }

        $view = view('service_company.cvtracker.fwd_resume_message', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'view' => $view]);

    }

    public function bernchDetailsCandidates()
    {
        // dd("jfif");
        $id = 22;

        // dd($id);
        $company_id = $this->job_additional_detail = $this->bench_assign = '';
        $company_id = auth()->user()->company_id;

        $this->currency = GeneralMethod::currency();
        $this->salaryMode = GeneralMethod::salaryMode();
        $this->benchApplication = JobApplication::select(
            'job_applications.id',
            'job_applications.company_id',
            'job_applications.submitted_by',
            'job_applications.submitted_by as editAllow',
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
            'job_applications.offer_amount',
            'job_applications.joined_date',
            'job_applications.created_at',
            'job_applications.updated_at',
            'job_applications.employee_number',
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
            ->leftJoin('city_list', 'city_list.city_id', '=', 'job_applications.city');
        $this->benchApplication = $this->benchApplication->where('job_applications.id', $id);
        $this->benchApplication = $this->benchApplication->first();

        // dd($this->benchApplication); resources\views\super-admin\bench-lists\showdetail.blade.php

        $this->job_additional_detail = DB::table('bench_additional_detail')->select('*')->where('bench_additional_detail.bench_id', '=', @$this->benchApplication->id)->orderBy('bench_additional_detail.id', 'ASC')->get();

        $this->bench_assign = DB::table('bench_assign')->select('users.id', 'users.name')->leftjoin('users', 'users.id', 'bench_assign.user_id')->where('bench_assign.bench_applications_id', '=', @$this->benchApplication->id)->orderBy('bench_assign.id', 'ASC')->get();

        $view = view('super-admin.bench-lists.showdetail', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'view' => $view]);
    }

    public function coinFeatures()
    {
        $coinEvents = DB::table('coin_events')
            ->select('*');
        $this->coinEvents = $coinEvents->paginate(20);

        return view('super-admin.coin-settings.coin-events', $this->data);
    }

    public function coinPackages()
    {
        $coinEvents = DB::table('coin_packages')
            ->join('coin_package_types', 'coin_package_types.id', '=', 'coin_packages.coin_package_type_id')
            ->select('coin_packages.*', 'coin_package_types.title', 'coin_package_types.days');
        $this->coinEvents = $coinEvents->paginate(20);

        // dd($coinEvents->get());
        return view('super-admin.coin-settings.coin-packages', $this->data);
    }

    public function addEditcoinPackages(Request $request)
    {
        $decryptedValue = ($request->id);
        $this->update = false;
        if ($decryptedValue != null) {

            $id = decrypt($request->id);
            // dd($id);
            $coin_packages = DB::table('coin_packages')->where('id', $id)->first();
            $this->update = true;
            $this->coin_packages = $coin_packages;
            // dd($coin_packages);
        }

        $coin_package_types = DB::table('coin_package_types')
            ->select('*');
        // dd("hgf");
        $this->coinPackageType = $coin_package_types->get();

        return view('super-admin.coin-settings.add-edit-coin-package', $this->data);
    }

    public function addEditcoinFeatutes(Request $request)
    {
        $decryptedValue = ($request->id);
        $this->update = false;
        if ($decryptedValue != null) {
            $id = decrypt($request->id);
            $coin_events = DB::table('coin_events')->where('id', $id)->first();
            $this->update = true;
            $this->coin_packages = $coin_events;
            // dd($coin_packages);
        }

        $coin_package_types = DB::table('coin_package_types')
            ->select('*');
        $this->coinPackageType = $coin_package_types->get();

        return view('super-admin.coin-settings.add-edit-coin-events', $this->data);
    }

    public function coinPackageStore(Request $req)
    {
        // dd((int)$req->token_id);
        $message = '';
        $status = 'error';

        $validator = Validator::make($req->all(), [
            'event' => 'required',
            'coin_package' => [
                'required',
                Rule::unique('coin_package_types', 'title')
                    ->where('event_type', $req->event) // assuming 'event' is the name of the event type field in the request
                    ->ignore((int) $req->coin_package_type_id), // Ignore the current ID for updates
            ],
            'durations' => 'required',
            'total_price' => 'required',
            'total_coins' => 'required',
        ]);

        if ($validator->fails()) {
            if ($req->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'errors' => $validator->errors(),
                ], 422); // 422 Unprocessable Entity
            } else {
                return redirect()->back()->withErrors($validator)->withInput();
            }
        }

        if ($req->token_id == '' || $req->token_id == 0) {

            if (! DB::table('coin_package_types')->where('title', '=', $req->coin_package)->where('event_type', '=', $req->event)->exists()) {
                DB::table('coin_package_types')->insert(['title' => $req->coin_package, 'event_type' => $req->event, 'days' => $req->durations]);
                $lastInsertedId = DB::getPdo()->lastInsertId();
            } else {
                $coin_package_types = DB::table('coin_package_types')->where('title', '=', $req->coin_package)->select('title', 'id')->first();
                $lastInsertedId = $coin_package_types->id;
            }
            $coin_package_id = (int) $lastInsertedId;
            $value = [
                'coin_package_type_id' => $lastInsertedId,
                'price' => $req->total_price,
                'price_usd' => $req->total_price_usd,
                'coins' => $req->total_coins,
                'discount' => ($req->discount) ? $req->discount : 0,
                'days' => ($req->durations) ? $req->durations : 0,
                'event_type' => $req->event,
                'total_price_monthly' => $req->total_price_monthly,
                'total_price_days' => $req->total_price_days,
                'total_price_rate' => $req->total_price_rate,
                'total_price_usd_monthly' => $req->total_price_usd_monthly,
                'total_price_usd_days' => $req->total_price_usd_days,
                'total_price_usd_rate' => $req->total_price_usd_rate,
                'visibility_show' => $req->visibility_show,
                'custome_pck' => $req->package_type,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'status' => ($req->status) ? 1 : 0,
            ];
            $id = DB::table('coin_packages')->insertGetId($value);
            $coin_package_id = (int) $id;
            $message = 'Coin Package Created Successfully';
            $status = 'success';
        } else {

            DB::table('coin_package_types')->where('id', '=', (int) $req->coin_package_type_id)->update(['title' => $req->coin_package, 'event_type' => $req->event, 'days' => $req->durations]);

            $value = [
                'coin_package_type_id' => (int) $req->coin_package_type_id,
                'price' => $req->total_price,
                'price_usd' => $req->total_price_usd,
                'coins' => $req->total_coins,
                'discount' => ($req->discount) ? $req->discount : 0,
                'days' => ($req->durations) ? $req->durations : 0,
                'event_type' => $req->event,
                'total_price_monthly' => $req->total_price_monthly,
                'total_price_days' => $req->total_price_days,
                'total_price_rate' => $req->total_price_rate,
                'total_price_usd_monthly' => $req->total_price_usd_monthly,
                'total_price_usd_days' => $req->total_price_usd_days,
                'total_price_usd_rate' => $req->total_price_usd_rate,
                'visibility_show' => $req->visibility_show,
                'custome_pck' => $req->package_type,
                'updated_at' => date('Y-m-d H:i:s'),
                'status' => ($req->status) ? 1 : 0,
            ];
            $id = ($req->token_id);
            DB::table('coin_packages')->where('id', $id)->update($value);
            $coin_package_id = (int) $id;
            $message = 'Coin Package updated Successfully';
            $status = 'success';
        }

        DB::table('coin_package_points')->where('coin_package_points.coin_package_id', $coin_package_id)->delete();
        // dd($req->points);
        if (count($req->points) > 0) {
            for ($i = 0; $i < count($req->points); $i++) {
                DB::table('coin_package_points')->insert(['title' => $req->points[$i], 'status' => $req->status_point[$i], 'coin_package_id' => $coin_package_id]);
            }

        }

        $data = ['status' => $status, 'message' => $message, 'url' => route('superadmin.company.coinPackages')];

        return response()->json($data);
    }

    public function coinEventsStore(Request $req)
    {
        // dd($req->token_id());
        $message = '';
        $status = 'error';

        $validator = Validator::make($req->all(), [
            'event_type' => 'required',
            'title' => 'required',
            'default_rate' => 'required',
            'minimum_required' => 'required',
            'deduction_type' => 'required',
        ]);

        if ($validator->fails()) {
            if ($req->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'errors' => $validator->errors(),
                ], 422); // 422 Unprocessable Entity description
            } else {
                return redirect()->back()->withErrors($validator)->withInput();
            }
        }
        //    dd(($req->status) ? 1 : 0);
        if ($req->token_id == '' || $req->token_id == 0) {

            $value = [
                'title' => $req->title,
                'default_rate' => $req->default_rate,
                'minimum_required' => $req->minimum_required,
                'description' => ($req->description) ? $req->description : '',
                'deduction_type' => ($req->deduction_type) ? $req->deduction_type : 'onetime',
                'event_type' => $req->event_type,
                'status' => ($req->status) ? 1 : 0,
            ];
            DB::table('coin_events')->insert($value);
            $message = 'Event Created Successfully';
            $status = 'success';
        } else {
            $value = [
                'title' => $req->title,
                'default_rate' => $req->default_rate,
                'minimum_required' => $req->minimum_required,
                'description' => ($req->description) ? $req->description : '',
                'deduction_type' => ($req->deduction_type) ? $req->deduction_type : 'onetime',
                'event_type' => $req->event_type,
                'status' => ($req->status) ? 1 : 0,
            ];
            $id = ($req->token_id);
            DB::table('coin_events')->where('id', $id)->update($value);
            $message = 'Event updated Successfully';
            $status = 'success';
        }

        $data = ['status' => $status, 'message' => $message, 'url' => route('superadmin.company.coinFeatures')];

        return response()->json($data);
    }

    public function coinEventStatus(Request $request)
    {

        // dd($request->candidate_id);
        $coin_events = DB::table('coin_events')->select('coin_events.*')->where('id', '=', $request->id)->first();
        $status = 1;
        if ($coin_events) {
            if ($coin_events->status) {
                $status = 0;
            }
        }
        DB::table('coin_events')->where('id', $request->id)->update(['status' => $status]);

        $message = 'Changed setting Successfully.';

        $data = ['status' => 'success', 'message' => $message];

        return response()->json($data);

    }

    public function topUpCoinPackages()
    {
        $coinEvents = DB::table('top_up_coin_packages')
            ->select('top_up_coin_packages.*');
        $this->coinEvents = $coinEvents->paginate(20);

        // dd($coinEvents->get());
        return view('super-admin.coin-settings.topup-coin-package', $this->data);
    }

    public function addEditTopUpcoinPackages(Request $request)
    {
        $decryptedValue = ($request->id);
        $this->update = false;
        if ($decryptedValue != null) {
            $id = decrypt($request->id);
            $coin_packages = DB::table('top_up_coin_packages')->where('id', $id)->first();
            $this->update = true;
            $this->coin_packages = $coin_packages;
            // dd($coin_packages);
        }

        $coin_package_types = DB::table('coin_package_types')
            ->select('*');
        $this->coinPackageType = $coin_package_types->get();

        return view('super-admin.coin-settings.add-edit-top-up-coin-package', $this->data);
    }

    public function topUpCoinPackageStore(Request $req)
    {
        // dd($req->token_id());
        $message = '';
        $status = 'error';

        $validator = Validator::make($req->all(), [
            'total_price' => 'required',
            'total_coins' => 'required',
        ]);

        if ($validator->fails()) {
            if ($req->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'errors' => $validator->errors(),
                ], 422); // 422 Unprocessable Entity
            } else {
                return redirect()->back()->withErrors($validator)->withInput();
            }
        }

        if ($req->token_id == '' || $req->token_id == 0) {

            $value = [
                'price' => $req->total_price,
                'coins' => $req->total_coins,
                'discount' => ($req->discount) ? $req->discount : 0,
                'per_coin_rate' => ($req->per_coin_rate) ? $req->per_coin_rate : null,
                'per_coin_rate_usd' => ($req->per_coin_rate_usd) ? $req->per_coin_rate_usd : null,
                'price_usd' => ($req->total_price_usd) ? $req->total_price_usd : null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'status' => ($req->status) ? 1 : 0,
            ];
            DB::table('top_up_coin_packages')->insert($value);
            $message = 'Top Up Coin Created Successfully';
            $status = 'success';
        } else {
            $value = [
                'price' => $req->total_price,
                'coins' => $req->total_coins,
                'discount' => ($req->discount) ? $req->discount : 0,
                'per_coin_rate' => ($req->per_coin_rate) ? $req->per_coin_rate : null,
                'per_coin_rate_usd' => ($req->per_coin_rate_usd) ? $req->per_coin_rate_usd : null,
                'price_usd' => ($req->total_price_usd) ? $req->total_price_usd : null,
                'updated_at' => date('Y-m-d H:i:s'),
                'status' => ($req->status) ? 1 : 0,
            ];
            $id = ($req->token_id);
            DB::table('top_up_coin_packages')->where('id', $id)->update($value);
            $message = 'Top Up Coin updated Successfully';
            $status = 'success';
        }

        $data = ['status' => $status, 'message' => $message, 'url' => route('superadmin.company.topUpCoinPackages')];

        return response()->json($data);
    }

    public function createOrderTopup(Request $request) {}

    public function topUpCoinsAdded(Request $request)
    {

        //  $id =  decrypt($request->id);
        //  dd("kj");
        $request->validate([
            'topUptype' => 'required',
            'price' => 'required',
            'app_int_amount' => 'required',
            'coins' => 'required',
            'currency' => 'required',
            'paymentmethodselector' => 'required',
            'paymentmodeselector' => 'required',
        ]);

        if ($request->paymentmodeselector == 'Cheque') {
            $request->validate([
                'chequenumber' => 'required',
                'chequedate' => 'required',
                'bankname' => 'required',
            ]);
        } elseif ($request->paymentmodeselector == 'Bank Transfer') {
            $request->validate([
                'transactionid' => 'required',
                'bankreference' => 'required',
            ]);
        }

        $topUptype = $request->topUptype;
        $company_id = $request->company_id;
        $app_int_amount = $request->app_int_amount;
        $price = $request->price;
        $coins = $request->coins;
        $gst = $request->gst;
        $gst_amount = $request->gst_amount;
        $total_price = $request->total_price;
        $chequenumber = $request->chequenumber;
        $chequedate = $request->chequedate;
        $bankname = $request->bankname;
        $transactionid = $request->transactionid;
        $bankreference = $request->bankreference;

        $status = '';
        $Paymentstatus = 'captured';
        $payment_orderId = '';

        $paymentLink = '';
        $top_up_logs_status = 1;

        $paymentLinkPaymentstatus = $request->paymentLinkPaymentstatus;
        $paymentLinkpayment_orderId = $request->paymentLinkpayment_orderId;
        $paymentLinkshort_url = $request->paymentLinkshort_url;

        $row = DB::table('top_up_coin_packages')->where('id', '=', $app_int_amount)->first();
        $custome_chk = ($row) ? @$row->custome_chk : 0;
        if ($row->custome_chk != 1) {
            $price = $row->price;
            $coins = $row->coins;
            $discount = $row->discount;

            if ($row->price != $price) {
                return response()->json([
                    'message' => 'Price not sufficient',
                    'status' => 'error',
                ]);
            }
            if ($row->coins != $coins) {
                return response()->json([
                    'message' => 'Coins not sufficient',
                    'status' => 'error',
                ]);

            }

        }
        //   dd($company_id);
        $company_coins = DB::table('company_coins')->where('event_type', '=', $topUptype)->where('company_id', '=', $company_id)->whereDate('end_date', '>=', now())->first();
        $update_coins = $coins;
        if ($company_coins) {
            $update_coins = $company_coins->coins + $coins;
        }

        $top_up_coins_cart = DB::table('top_up_coin_packages')
            ->where('top_up_coin_packages.id', $app_int_amount)
            ->select('top_up_coin_packages.*')
            ->first();

        // dd($update_coins);
        $currency = $request->currency;
        $paymentmodeselector = $request->paymentmodeselector;
        $paymentmethodselector = $request->paymentmethodselector;
        if ($paymentmodeselector == 'Payment Link') {
            if ($paymentmethodselector == 'razorpay') {
                //    dd($total_price,$price);

                $payment_settings = DB::table('payment_settings')->select('payment_settings.*')->first();
                $apiKey = $payment_settings->razorpay_key;
                $apiSecret = $payment_settings->razorpay_secret;

                $amount = $total_price * 100; // Amount in paise
                $receipt = uniqid();

                $orderData = [
                    'amount' => $amount,
                    'currency' => $currency,
                    'receipt' => $receipt,
                    'payment_capture' => 1,
                ];

                $paymentLink = $paymentLinkshort_url;
                $Paymentstatus = $paymentLinkPaymentstatus;
                $payment_orderId = $paymentLinkpayment_orderId;

                $company_details = DB::table('companies')->where('id', '=', $company_id)->first();
                $product_name = 'Top-up Coins';
                $event_type = @$top_up_coins_cart->event_type;
                $paymentLinkData = [
                    'amount' => $amount,
                    'currency' => $currency,
                    'description' => $product_name.' - '.$event_type,
                    'callback_url' => url('link-payment-callback/'),
                    'callback_method' => 'get',
                    'customer' => [
                        'name' => $company_details->company_name,
                        'contact' => $company_details->company_phone,
                        'email' => $company_details->company_email,
                    ],
                    'notify' => [
                        'sms' => true,
                        'email' => true,
                    ],
                ];

                // Create payment link using cURL

                // $paymentLinkResponse = $this->createRazorpayPaymentLink($apiKey, $apiSecret, $paymentLinkData);

                if (! empty($paymentLink)) {

                    $basecompany = GeneralMethod::baseCompany();
                    $senderName = ($basecompany->companyName) ? $basecompany->companyName : '';
                    $fromAddress = ($basecompany->companyEmail) ? $basecompany->companyEmail : '';
                    $attachments = [];
                    $cc = [];

                    $subject = 'Your Payment Link';

                    $message = '
                                <html>
                                <head>
                                    <title>Your Payment Link</title>
                                </head>
                                <body>
                                    <h1>Hello,</h1>
                                    <p>We have generated a payment link for you. Please use the following link to complete your payment of '.($currency == 'INR' ? '₹' : '$').''.number_format($amount / 100, 2).":</p>
                                    <p><a href='$paymentLink'>$paymentLink</a></p>
                                    <p>Thank you!</p>
                                </body>
                                </html>
                                ";

                    $to_Mail_Id = $company_details->company_email;

                    $datamail = [
                        'message' => $message,
                    ];
                    $result = $this->mailService->sendEmail($to_Mail_Id, $subject, 'email.common.index', (object) $datamail, $attachments, $fromAddress, $senderName, $cc);
                    // dd($to_Mail_Id,$result);

                } else {
                    // return response()->json([
                    //     'message' => 'Failed to create payment link',
                    //     'status' => 'error'
                    // ]);
                }

                $status = 'razorpay';
                $top_up_logs_status = 0;
                // dd( $paymentLinkResponse);

                // $createPayment = GeneralMethod::createPayment($total_price, $currency);

                // $Paymentstatus = $paymentLinkResponse['status'];
                // $payment_orderId = $paymentLinkResponse['id'];

            } else {
                $createPayment = GeneralMethod::createPayment($total_price, $currency);
                $status = 'phonepe';
                $top_up_logs_status = 0;
            }
        } else {

            $status = 'offline';

        }

        // dd( $top_up_coins_cart);

        $subscription_type = 'no';
        $product_name = 'Top-up Coins';
        $event_type = @$top_up_coins_cart->event_type;
        $duration = 'Existing Package';
        $credit = @$coins;

        // Create the cart value only if one of the cart types is found
        if ($top_up_coins_cart) {
            $cartValue = [
                'company_id' => $company_id,
                'subscription_type' => $subscription_type,
                'product_name' => $product_name.' - '.$event_type,
                'pck_top_id' => $app_int_amount,
                'event_type' => $event_type,
                'duration' => $duration,
                'credit' => $credit,
                'currency' => $currency,
                'price' => $price,
                'gst' => $gst,
                'status' => 2,
                'gst_amount' => $gst_amount,
                'total_price' => $total_price,
                'user_id' => auth()->user()->id,
            ];

            // dd($cartValue);
            // Insert the cart record
            DB::table('carts')->insert($cartValue);
            $cartsInsertedId = DB::getPdo()->lastInsertId();

            // dd("kfd");
        }

        $row = DB::table('carts')->where('id', '=', $cartsInsertedId)->where('status', '=', 2)->first();

        $maxId = DB::table('cart_transaction_history')->max('id');
        $pre_fixed = DB::table('pre_fixed_settings')->where('type', 'invoice')->first();
        $number = ($maxId > 0) ? $maxId + 1 : 1;
        $padded_number = str_pad($number, 5, '0', STR_PAD_LEFT);
        $invoices_no = $pre_fixed->pre_fixed.$padded_number;
        $invoices_date = date('Y-m-d H:i:s');

        DB::table('cart_transaction_history')->insert([
            'company_id' => $row->company_id,
            'subscription_type' => $row->subscription_type,
            'pck_top_id' => $row->pck_top_id,
            'event_type' => $row->event_type,
            'product_name' => $row->product_name,
            'duration' => $row->duration,
            'credit' => $row->credit,
            'currency' => $row->currency,
            'price' => $row->price,
            'gst' => $row->gst,
            'gst_amount' => $row->gst_amount,
            'total_price' => $row->total_price,
            'user_id' => $row->user_id,
            'status' => $Paymentstatus,
            'payment_link_id' => $payment_orderId,
            'chequenumber' => (@$chequenumber) ? $chequenumber : '',
            'chequedate' => (@$chequedate) ? date('Y-m-d', strtotime($chequedate)) : '',
            'bankname' => (@$bankname) ? $bankname : '',
            'transactionid' => (@$transactionid) ? $transactionid : '',
            'bankreference' => (@$bankreference) ? $bankreference : '',
            'payment_gateway' => $paymentmethodselector,
            'invoiceId' => $invoices_no,
            'invoice_date' => $invoices_date,
            'payment_mode' => $paymentmodeselector,
            'cart_id' => $cartsInsertedId,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        $cartsInsertedIdHistoryID = DB::getPdo()->lastInsertId();

        DB::table('link_payments')->insert([
            'status' => '0',
            'company_id' => $row->company_id,
            'coins' => $row->credit,
            'amount' => $total_price,
            'update_coins' => $update_coins,
            'event_type' => $row->event_type,
            'subscription_type' => $row->subscription_type,
            'cart_id' => $cartsInsertedId,
            'currency' => $currency,
            'payment_link' => $paymentLink,
            'payment_link_id' => $payment_orderId,
        ]);

        if ($company_coins) {

            $value = [
                'company_id' => $company_id,
                'price' => $price,
                'coins' => $coins,
                'custome_chk' => @$custome_chk,
                'event_type' => $topUptype,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'payment_link_id' => $payment_orderId,
                'cart_id' => $cartsInsertedId,
                'status' => $top_up_logs_status,
            ];

            DB::table('top_up_logs')->insert($value);

            if ($paymentmodeselector == 'Payment Link') {
                $coins_update = 0;
                $actual_coins_update = 0;
                $start_date = $company_coins->start_date;
                $end_date = $company_coins->end_date;
            } else {
                $actual_coins = DB::table('cart_transaction_history')->where('id', '=', $cartsInsertedIdHistoryID)->first();
                // $actual_coins = $update_coins-$company_coins->coins;

                $actual_coins_update = $actual_coins->credit;
                $coins_update = $update_coins;
                $start_date = $company_coins->start_date;
                $end_date = $company_coins->end_date;

                DB::table('company_coins')->where('event_type', '=', $topUptype)->where('company_id', '=', $company_id)->update(['coins' => $coins_update, 'actual_coins' => $actual_coins_update, 'updated_at' => date('Y-m-d H:i:s')]);

            }

            $coin_transection_logs = [
                'company_id' => $company_id,
                'event_by' => 1,
                'event' => $row->product_name,
                'currency' => $currency,
                'amount' => $price,
                'coins' => $coins_update,
                'remain_coins' => 0,
                'actual_coins' => $actual_coins_update,
                'transection_type' => 'credit',
                'settlement_coins' => 0,
                'event_type' => $topUptype,
                'cart_id' => $cartsInsertedId,
                'payment_link_id' => $payment_orderId,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'payment_orderId' => $payment_orderId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            DB::table('coin_transection_logs')->insertGetId($coin_transection_logs);

            if (! DB::table('billing_address_companies')->where('company_id', '=', $company_id)->exists()) {

                $clients = DB::table('clients')->leftJoin('companies', 'companies.id', '=', 'clients.company_id')
                    ->select('clients.id', 'companies.company_name', 'users.name')
                    ->leftJoin('users', 'users.company_id', '=', 'companies.id')
                    ->where('clients.company_id', '=', $company_id)
                    ->where('clients.self_company', '=', 1)
                    ->first();

                $locInfo = DB::table('client_location')
                    ->leftJoin('countries', 'countries.id', '=', 'client_location.country_id')
                    ->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')
                    ->leftJoin('state_list', 'state_list.id', '=', 'client_location.state')
                    ->select(
                        'client_location.id',
                        'client_location.city',
                        'client_location.city_id',
                        'client_location.state as state_id',
                        'client_location.address',
                        'client_location.postcode',
                        'client_location.country_id',
                        'city_list.city as city_name',
                        'state_list.state as state_name',
                        'countries.country_name'
                    )
                    ->where('countries.id', $company_id)
                    ->first();

                $phone = auth()->user()->mobile;

                $company_name = @$clients->company_name;
                $user_name = @$clients->name;
                $nameParts = explode(' ', $user_name);
                $firstName = $nameParts[0];
                $lastName = isset($nameParts[1]) ? $nameParts[1] : '';

                $locArr = '';
                if ($locInfo) {
                    $city_name = @$locInfo->city;

                    $locArr = ucwords(@$locInfo->address).' '.ucwords($city_name).' , '.ucwords(@$locInfo->state_name).' , '.ucwords(@$locInfo->country_name);
                }

                DB::table('billing_address_companies')->insert([
                    'company_id' => $company_id,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'phone' => $phone,
                    'street' => $locArr,
                    'organization' => $company_name,
                ]);
            }

            $billing_address_companies = DB::table('billing_address_companies')->where('company_id', '=', $company_id)->first();
            $sold_by_address = DB::table('sold_by_address')->first();
            if ($billing_address_companies && $cartsInsertedIdHistoryID) {

                DB::table('billing_address_by_orders')->insert([
                    'cart_transaction_id' => $cartsInsertedIdHistoryID,
                    'company_id' => $company_id,
                    'country_id' => ($billing_address_companies) ? $billing_address_companies->country_id : '',
                    'first_name' => ($billing_address_companies) ? $billing_address_companies->first_name : '',
                    'last_name' => ($billing_address_companies) ? $billing_address_companies->last_name : '',
                    'phone' => ($billing_address_companies) ? $billing_address_companies->phone : '',
                    'building' => ($billing_address_companies) ? $billing_address_companies->building : '',
                    'street' => ($billing_address_companies) ? $billing_address_companies->street : '',
                    'state_id' => ($billing_address_companies) ? $billing_address_companies->state_id : '',
                    'city' => ($billing_address_companies) ? $billing_address_companies->city : '',
                    'postal_code' => ($billing_address_companies) ? $billing_address_companies->postal_code : '',
                    'organization' => ($billing_address_companies) ? $billing_address_companies->organization : '',
                    'pan_no' => ($billing_address_companies) ? $billing_address_companies->pan_no : '',
                    'gstin' => ($billing_address_companies) ? $billing_address_companies->gstin : '',
                    'sold_country_id' => ($sold_by_address) ? $sold_by_address->country_id : '',
                    'sold_first_name' => ($sold_by_address) ? $sold_by_address->first_name : '',
                    'sold_last_name' => ($sold_by_address) ? $sold_by_address->last_name : '',
                    'sold_phone' => ($sold_by_address) ? $sold_by_address->phone : '',
                    'sold_building' => ($sold_by_address) ? $sold_by_address->building : '',
                    'sold_street' => ($sold_by_address) ? $sold_by_address->street : '',
                    'sold_state_id' => ($sold_by_address) ? $sold_by_address->state_id : '',
                    'sold_city' => ($sold_by_address) ? $sold_by_address->city : '',
                    'sold_postal_code' => ($sold_by_address) ? $sold_by_address->postal_code : '',
                    'sold_organization' => ($sold_by_address) ? $sold_by_address->organization : '',
                    'sold_gstin' => ($sold_by_address) ? $sold_by_address->gstin : '',
                    'sold_pan_no' => ($sold_by_address) ? $sold_by_address->pan_no : '',
                ]);

            }

            return response()->json([
                'message' => '',
                'status' => $status,
                'paymentLink' => $paymentLink,
            ]);
        }

    }

    public function transactionhistory()
    {
        $this->cart_transaction_history = DB::table('cart_transaction_history')
            ->join('companies', 'companies.id', 'cart_transaction_history.company_id')
            ->leftJoin('billing_address_by_orders', 'billing_address_by_orders.cart_transaction_id', 'cart_transaction_history.id')
            ->leftJoin('countries', 'countries.id', '=', 'billing_address_by_orders.country_id')
            ->leftJoin('state_list', 'state_list.id', '=', 'billing_address_by_orders.state_id')
            ->leftJoin('city_list', 'city_list.city_id', '=', 'billing_address_by_orders.city')
            ->select('cart_transaction_history.*', 'companies.company_name', 'billing_address_by_orders.gstin', 'countries.country_name', 'state_list.state', 'city_list.city')
            ->orderByDesc('id')->paginate(50);

        // dd($this->cart_transaction_history);
        return view('super-admin.subscription.transactionhistory', $this->data);
    }

    public function invoiceupdate(Request $request)
    {

        // dd($request->all());
        $id = decrypt($request->dataValue);
        $message = '';
        if ($request->gst_on == 1) {
            DB::table('cart_transaction_history')->where('id', '=', $id)->update([
                'invoice_approval' => $request->invoice_no,
            ]);
            $message = 'Invoice file successfully';
        } else {
            DB::table('cart_transaction_history')->where('id', '=', $id)->update([
                'approval_invoice_no' => $request->invoice_no,
                'approval_invoice_date' => date('Y-m-d H:i:s'),
            ]);
            $message = 'Invoice updated successfully';
        }
        $data = ['status' => 'success', 'message' => $message];

        return response()->json($data);

        // dd($id);

    }

    private function createRazorpayPaymentLink($apiKey, $apiSecret, $paymentLinkData)
    {
        $url = 'https://api.razorpay.com/v1/payment_links';

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $apiKey.':'.$apiSecret);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($paymentLinkData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);

        $response = curl_exec($ch);
        $paymentLinkResponse = json_decode($response, true);

        curl_close($ch);

        return $paymentLinkResponse;
    }

    public function topuptypegetprice(Request $request)
    {

        $row = DB::table('top_up_coin_packages')->where('id', '=', $request->id)->first();

        // dd($row );

        $pack_status = $row->custome_chk;
        $gst_on_get = $request->gst_on_get;
        // dd($request->all());
        $currency = $request->currency_get;
        $price = ($request->currency_get == 'INR') ? $row->price : $row->price_usd;

        if ($currency == 'INR' && $gst_on_get == 'Yes') {
            $gst = 18;
            $gst_amount = ($price * $gst) / 100;
            $total_price = $price + $gst_amount;
        } else {
            $gst = 0; // GST might not apply for USD or set appropriate tax value
            $gst_amount = ($price * $gst) / 100; // Modify this if other tax applies
            $total_price = $price + $gst_amount; // Total price in USD
        }

        $currency = '<i class="fa fa-'.($currency == 'INR' ? 'inr' : 'usd').'" aria-hidden="true"></i>';
        $coins = $row->coins;
        $discount = $row->discount;

        return Reply::dataOnly(['status' => 'success', 'price' => $price, 'coins' => $coins, 'discount' => $discount, 'custome_status' => $pack_status, 'gst' => $gst, 'gst_amount' => $gst_amount, 'total_price' => $total_price, 'currency' => $currency]);

    }

    public function zoomSetting(Request $request)
    {
        // dd($request->status);

        //    $this->newCandidates =  JobApplication::paginate(10);

        $zoom_setting = DB::table('zoomsettings')
            ->select('zoomsettings.*');

        $this->zoom_setting = $zoom_setting->first();
        if ($this->zoom_setting) {
            $this->isEmptycheck = false;
        } else {
            $this->isEmptycheck = true;
        }
        //    dd($this->zoom_setting);

        return view('super-admin.meeting.zoom-setting', $this->data);

    }

    public function settingZoomStore(Request $req)
    {
        // dd($req->token_id());
        $message = '';
        $status = 'error';

        $validator = Validator::make($req->all(), [
            'title' => 'required',
            'redirect_uri' => 'required',
            'client_id' => 'required',
            'client_secret' => 'required',
        ]);

        if ($validator->fails()) {
            if ($req->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'errors' => $validator->errors(),
                ], 422); // 422 Unprocessable Entity description
            } else {
                return redirect()->back()->withErrors($validator)->withInput();
            }
        }

        if ($req->token_id == '' || $req->token_id == 0) {

            $value = [
                'title' => $req->title,
                'redirect_uri' => $req->redirect_uri,
                'client_id' => $req->client_id,
                'client_secret' => ($req->client_secret) ? $req->client_secret : '',
            ];
            DB::table('zoomsettings')->insert($value);
            $message = 'Zoom Meeting Created Successfully';
            $status = 'success';
        } else {
            $value = [
                'title' => $req->title,
                'redirect_uri' => $req->redirect_uri,
                'client_id' => $req->client_id,
                'client_secret' => ($req->client_secret) ? $req->client_secret : '',
            ];
            $id = ($req->token_id);
            DB::table('zoomsettings')->where('id', $id)->update($value);
            $message = 'Zoom Meeting updated Successfully';
            $status = 'success';
        }

        $data = ['status' => $status, 'message' => $message, 'url' => ''];

        return response()->json($data);
    }

    public function googleMeetingSetting(Request $request)
    {

        $meeting_link = ($request->meeting_link);
        //    $this->newCandidates =  JobApplication::paginate(10); lisisd
        $google_meeting_setting = DB::table('google_meeting_lists')
            ->select('google_meeting_lists.*')
            ->when($meeting_link !== '', function ($query) use ($meeting_link) {
                $query->where('meeting_link', 'LIKE', '%'.$meeting_link.'%');
            })
            ->orderByDesc('id');

        $this->google_meeting_setting = $google_meeting_setting->paginate(10);

        if ($this->google_meeting_setting) {
            $this->isEmptycheck = false;
        } else {
            $this->isEmptycheck = true;
        }
        //    dd($this->zoom_setting);

        $this->meeting_link = $meeting_link;

        return view('super-admin.meeting.google-meeting-setting', $this->data);

    }

    public function googleMeetStatusUpdate(Request $request)
    {

        $values = [
            'status' => $request->chkStatus,
        ];
        // dd($values);
        DB::table('google_meeting_lists')->where('id', '=', $request->id)->update($values);

        return response()->json([
            'message' => '',
            'status' => 'success',
        ]);

    }

    public function teamMeetingSetting(Request $request)
    {
        // dd($request->status);

        $meeting_link = ($request->meeting_link);
        //    $this->newCandidates =  JobApplication::paginate(10);

        $team_meeting_setting = DB::table('team_meeting_lists')
            ->select('team_meeting_lists.*')
            ->when($meeting_link !== '', function ($query) use ($meeting_link) {
                $query->where('meeting_link', 'LIKE', '%'.$meeting_link.'%');
            })
            ->orderByDesc('id');

        $this->team_meeting_setting = $team_meeting_setting->paginate(10);

        if ($this->team_meeting_setting) {
            $this->isEmptycheck = false;
        } else {
            $this->isEmptycheck = true;
        }
        //    dd($this->zoom_setting);
        $this->meeting_link = $meeting_link;

        return view('super-admin.meeting.team-meeting-setting', $this->data);

    }

    public function teamMeetStatusUpdate(Request $request)
    {

        $values = [
            'status' => $request->chkStatus,
        ];
        // dd($values);
        DB::table('team_meeting_lists')->where('id', '=', $request->id)->update($values);

        return response()->json([
            'message' => '',
            'status' => 'success',
        ]);

    }

    public function meetingListDelete(Request $request)
    {
        try {
            $ids = $request->input('arr', []);
            $table = $request->input('table');

            // ✅ Validate input
            if (empty($ids) || ! is_array($ids)) {
                return response()->json([
                    'status' => false,
                    'type' => 'warning',
                    'message' => 'Please select at least one record.',
                ]);
            }

            // ✅ Whitelist tables (IMPORTANT for security)
            $allowedTables = ['team_meeting_lists', 'google_meeting_lists'];

            if (! in_array($table, $allowedTables)) {
                return response()->json([
                    'status' => false,
                    'type' => 'error',
                    'message' => 'Invalid request.',
                ]);
            }

            // ✅ Delete query
            $deleted = DB::table($table)
                ->whereIn('id', $ids)
                ->delete();

            return response()->json([
                'status' => true,
                'type' => 'success',
                'message' => $deleted.' record(s) deleted successfully.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'type' => 'error',
                'message' => 'Something went wrong!',
                'error' => $e->getMessage(), // optional (remove in production)
            ]);
        }
    }

    public function addMeetingLists(Request $request)
    {
        try {

            $table = $request->input('tablename');
            $url = '';
            // ✅ सुरक्षा (IMPORTANT)
            $allowedTables = ['google_meeting_lists', 'team_meeting_lists'];
            if ($table === 'google_meeting_lists') {
                $url = url('super-admin/google-meeting-setting');
            } elseif ($table === 'team_meeting_lists') {
                $url = url('super-admin/team-meeting-setting');
            }

            if (! in_array($table, $allowedTables)) {
                return response()->json([
                    'status' => false,
                    'type' => 'error',
                    'message' => 'Invalid table name',
                ]);
            }

            $emails = $request->input('holder_email', []);
            $links = $request->input('meeting_link', []);

            if (empty($emails) || empty($links)) {
                return response()->json([
                    'status' => false,
                    'type' => 'warning',
                    'message' => 'No data found to insert',
                ]);
            }

            $insertData = [];

            // ✅ Validate arrays
            if (empty($emails) || empty($links)) {
                return response()->json([
                    'status' => false,
                    'type' => 'warning',
                    'message' => 'No records selected.',
                ]);
            }

            foreach ($emails as $key => $email) {

                // Skip empty rows
                if (empty($email) || empty($links[$key])) {
                    continue;
                }

                $insertData[] = [
                    'account_name' => $email,
                    'meeting_link' => $links[$key],
                    'status' => 1,
                ];
            }
            if (empty($insertData)) {
                return response()->json([
                    'status' => false,
                    'type' => 'warning',
                    'message' => 'All rows are empty',
                ]);
            }
            // ✅ Bulk Insert (FAST)
            DB::table($table)->insertOrIgnore($insertData);

            return response()->json([
                'status' => true,
                'type' => 'success',
                'url' => $url,
                'message' => count($insertData).' record(s) inserted successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'type' => 'error',
                'message' => 'Something went wrong',
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function addMeetingList(Request $request, $tablename)
    {

        // $company_id

        $this->tablename = $tablename;
        $this->offer_id = 0;
        $this->section_other = '';
        $this->section_hourly = 'd-none';

        return view('super-admin.meeting.add-meeting', $this->data);

    }

    public function skillskElastic(Request $request)
    {

        $searchQuery = $request->input('q');
        $page = max((int) $request->input('page', 1), 1);
        $size = (int) $request->input('size', 20); // Default 20 results per page
        $from = ($page - 1) * $size;
        $sort = [
            ['created_at' => ['order' => 'desc']],
        ];
        $params = [
            'index' => ELASTIC_SKILLS,
            'request_cache' => true,
            'body' => [
                'from' => $from,
                'size' => $size,
                'track_total_hits' => true,
                'sort' => $sort,
            ],
        ];
        $response = $this->elasticService->getClient()->search($params);

        $dataskill = collect($response['hits']['hits'])->map(function ($hit) {
            return (object) array_merge(
                ['id' => $hit['_id']],   // include the ES document ID
                $hit['_source']          // merge with your fields
            );
        });

        //  $total_cus = (int) $dataskill->count();
        // dd($dataskill);

        $total = $response['hits']['total']['value'] ?? 0;

        //    dd($total_cus,$total);

        $paginatedSkills = new LengthAwarePaginator(
            $dataskill,         // items for this page
            $total,             // total items (from Elasticsearch)
            $size,              // per page
            $page,              // current page
            ['path' => request()->url(), 'query' => request()->query()] // keep query params
        );
        // dd($paginatedSkills);

        return view('super-admin.comman.skill-list', $this->data, [
            'dataskill' => $paginatedSkills,
            'total' => $total,
        ]);

    }

    public function skillDelete(Request $request)
    {

        // all($request->all());

        $docId = $this->getElasticDocId($request->id);
        if (! $docId) {
            return $this->error('❌ Not found in ES');
        }

        // dd($docId);

        $this->elasticService->getClient()->delete([
            'index' => ELASTIC_SKILLS,
            'id' => $docId,
        ]);

        return response()->json([
            'message' => '',
            'status' => 'success',
        ]);

    }

    public function skillUpdate(Request $request, $Id, $name)
    {

        $this->skill_name = $name;
        $this->skill_id = $Id;

        $this->clients = DB::table('clients')->select('clients.*')->orderBy('clients.id', 'ASC')->get();

        $this->mail_permissions = DB::table('mail_permissions')->where('notification_id', '=', $Id)->pluck('role_name')->toArray();

        $this->roles = DB::table('roles')->select('roles.*')->groupBy('display_name')->get();

        // dd($this->th_email_template);	 display_name

        return view('super-admin.comman.add-edit-skill', $this->data);
    }

    public function addEditSkillUpdate(Request $request)
    {

        if ($request->tokenId === '0') {
            //   dd($request->all());
            if (! empty(@$request->skill_name)) {
                $tags = @$request->skill_name;
                $tags = json_decode($tags, true);
                $skillsAddElastic = array_column($tags, 'value');
                if (count($skillsAddElastic) > 0) {
                    GeneralMethod::skillsAddElastic($skillsAddElastic);
                }
            }

        } else {

            $docId = $this->getElasticDocId($request->tokenId);
            if (! $docId) {
                return Reply::dataOnly(['status' => 'error']);
            }

            if (! empty(@$request->skill_name)) {
                $tags = @$request->skill_name;
                // dd($tags); now()->toIso8601String()
                $tags = json_decode($tags, true);
                $skillsAddElastic = array_column($tags, 'value');
                if (count($skillsAddElastic) > 0) {
                    foreach ($skillsAddElastic as $row) {
                        $skill_name = strtolower(trim($row));

                        $this->elasticService->getClient()->update([
                            'index' => ELASTIC_SKILLS,
                            'id' => $docId,
                            'body' => [
                                'doc' => [
                                    'skill_name' => $skill_name,
                                    'updated_at' => now()->toIso8601String(),
                                ],
                            ],
                        ]);

                    }
                }
            }

        }

        return Reply::dataOnly(['status' => 'success']);

    }

    private function getElasticDocId($id)
    {
        //   dd($id);
        $result = $this->elasticService->getClient()->search([
            'index' => ELASTIC_SKILLS,
            'body' => [
                'query' => [
                    'term' => ['_id' => $id], // exact match
                ],
            ],
        ]);

        return $result['hits']['total']['value'] > 0
            ? $result['hits']['hits'][0]['_id']
            : null;
    }

    public function job_request($id)
    {
        $this->pageTitle = __('Job Request');
        $this->dataCnt = 0;

        return view('super-admin.jobs.job_request', $this->data);
    }

    public function all_recommandedrequest_data_ajax(Request $request)
    {
        $company_id = auth()->user()->company_id;

        $job_status = GeneralMethod::job_status();
        $salaryMode = GeneralMethod::salaryMode();
        $currencySymbole = GeneralMethod::currencySymbole();
        $feedback_status = GeneralMethod::conversation_status();

        $searchQuery = $request->input('q');
        $page = max((int) $request->input('page', 1), 1);
        $size = (int) $request->input('size', 10); // Default 20 results per page
        $from = ($page - 1) * $size;
        $valCheck = $request->input('sortingfilter');
        $mstatus_id = $request->input('mstatus_id');
        $sort = [];
        $country = '';
        $filters = [];
        $sort[] = ['final_status' => ['order' => 'asc', 'unmapped_type' => 'long']];
        $boolQuery = [
            'bool' => [
                'should' => [
                    [
                        'bool' => [
                            'must' => array_merge([
                                ['terms' => ['final_status' => [0]]],
                            ]),
                        ],
                    ],
                ],
                'minimum_should_match' => 1,
                'filter' => $filters, // We'll add all filters here
                'must_not' => [],
            ],
        ];
        $params = [
            'index' => ELASTIC_JOB_REQUEST,
            'request_cache' => true,
            'body' => [
                'from' => $from,
                'size' => $size,
                'sort' => $sort,
                'query' => $boolQuery,
            ],
        ];

        if (! $this->elasticService->indexExists(ELASTIC_JOB_REQUEST)) {
            $total = 0;
            $data = [];
        } else {
            $response = $this->elasticService->getClient()->search($params);
            $total = $response['hits']['total']['value'] ?? 0;
            $data = array_map(function ($hit) {
                return $hit['_source'];
            }, $response['hits']['hits']);
        }

        $pagination = [
            'total' => $total,
            'current_page' => $page,
            'last_page' => ceil($total / $size),
            'listType' => 'allcandidate',
        ];
        $userPermissions = [];

        return response()->json([
            'html' => view('super-admin.ajax_pages.recommandedrequestpending_data_ajax', compact('data', 'userPermissions', 'salaryMode', 'currencySymbole', 'job_status', 'feedback_status'))->render(),
            'total' => $total,
            'pagination' => view('super-admin.ajax_pages.pagination', compact('pagination'))->render(),
        ]);

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
                'jobs.company_id as from_company_id',
                'jobs.min_salary_currency',
                'jobs.max_salary_currency',
                'recommendation_relation.to_company_id',
                'clients.name as clients_name',
                'clients.logo_logical_path',
                'recommendation_relation.id as recommendation_relation_id',
                'recommendation_relation.final_status',
                'recommendation_relation.from_created_at',
                'companies.logo',
                'conversations.status as conversations_status',
                'conversations.id as conversations_id',
                'companies.company_name',
                'to_com.company_name as company_name_to'
            )
            ->join('clients', 'clients.id', 'jobs.client_id')
            ->join('companies', 'companies.id', 'clients.company_id')
            ->join('recommendation_relation', 'recommendation_relation.job_id', 'jobs.id')
            ->leftJoin('companies as to_com', 'to_com.id', '=', 'recommendation_relation.to_company_id')
            ->leftJoin('conversations', 'conversations.id', '=', 'recommendation_relation.conversation_id')
            ->where('jobs.id', '>', '0');

        if (\request('search') != '') {
            $search = request('search');
            $categories->where('jobs.title', 'LIKE', "%$search%");
        }

        $categories->whereIn('recommendation_relation.final_status', [0]);

        $categories->orderBy('jobs.id', 'DESC');

        $perPage = 10;
        $page = $request->get('page', 1);

        $total = count($categories->get());

        $data = $categories->skip(($page - 1) * $perPage)->take($perPage)->get();

        $pagination = [
            'total' => $total,
            'current_page' => $page,
            'last_page' => ceil($total / $perPage),
            'listType' => 'allcandidate',
        ];

        $userPermissions = [];

        return response()->json([
            'html' => view('super-admin.ajax_pages.recommandedrequestpending_data_ajax', compact('data', 'userPermissions', 'feedback_status', 'salaryMode', 'currencySymbole', 'job_status', 'company_id'))->render(),
            'total' => $total,
            'pagination' => view('super-admin.ajax_pages.pagination', compact('pagination'))->render(),
        ]);

    }

    public function recommandedrequest(Request $request)
    {
        $company_id = auth()->user()->company_id;

        $job_status = GeneralMethod::job_status();
        $salaryMode = GeneralMethod::salaryMode();
        $currencySymbole = GeneralMethod::currencySymbole();
        $feedback_status = GeneralMethod::conversation_status();
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
                'jobs.company_id as from_company_id',
                'jobs.min_salary_currency',
                'jobs.max_salary_currency',
                'recommendation_relation.to_company_id',
                'clients.name as clients_name',
                'clients.logo_logical_path',
                'recommendation_relation.id as recommendation_relation_id',
                'recommendation_relation.final_status',
                'recommendation_relation.from_created_at',
                'companies.logo',
                'conversations.status as conversations_status',
                'conversations.id as conversations_id',
                'companies.company_name',
                'to_com.company_name as company_name_to'
            )
            ->join('clients', 'clients.id', 'jobs.client_id')
            ->join('companies', 'companies.id', 'clients.company_id')
            ->join('recommendation_relation', 'recommendation_relation.job_id', 'jobs.id')
            ->leftJoin('companies as to_com', 'to_com.id', '=', 'recommendation_relation.to_company_id')
            ->leftJoin('conversations', 'conversations.id', '=', 'recommendation_relation.conversation_id')
            ->where('jobs.id', '>', '0');

        if (\request('search') != '') {
            $search = request('search');
            $categories->where('jobs.title', 'LIKE', "%$search%");
        }
        // $categories->where('recommendation_relation.to_company_id', $company_id);
        $categories->whereIn('recommendation_relation.final_status', [0]);
        // $categories->groupBy('jobs.id');
        $categories->orderBy('jobs.id', 'DESC');

        return DataTables::of($categories)

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

                $icon = '<div data-row-id="'.$row->id.'" class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5 client-show-detail" style="float:left">
                    <a href="javascript:void(0);" data-row-id="'.$row->id.'" class="show-detail">
                      <div class="symbol-label '.$bgTextDecore.' border-radius-5">'.ucwords($strVal).'</div>
                    </a>
                  </div>';

                $logo_path = url('img/client-default.png');
                if (! empty(@$row->logo) && strlen(@$row->logo) > 10) {
                    $logo_path = @$row->logo;
                }

                if (! empty($logo_path)) {
                    $icon = '<div data-row-id="'.$row->id.'" class="symbol symbol-circle symbol-40px overflow-hidden me-2  border-radius-5 client-show-detail">
                                        <a href="javascript:void(0);">
                                            <div class="symbol-label border-radius-5">
                                            <img src="'.$logo_path.'" alt="Company Logo" class="border-radius-5 w-100">
                                            </div>
                                        </a>
                                    </div>';
                }

                $skilltag = '';
                $skilltagclass = '';
                if (empty($limit)) {
                    $skillArr = DB::table('job_metronics_skills')->select('*')->where('job_id', $row->id)->where('company_id', '=', $row->from_company_id)->get();
                    if (! empty($skillArr)) {
                        foreach ($skillArr as $sval) {
                            $skilltag .= '<span class="badge badge-light my-1">'.ucfirst($sval->skill_name).'</span>  ';
                        }
                    }
                } else {
                    $skilltagclass = 'd-none';
                }

                return ' <div class=" d-flex align-items-center border-0" >
							
							'.$icon.'
						   
                           <div class="d-flex flex-column"> 
						   		<a href="javascript:void(0);" data-row-id="'.$row->id.'" class="text-dark fw-bolder text-hover-primary fs-6 client-show-detail " data-toggle="tooltip1" title="" data-bs-original-title="2">
									'.ucwords(substr(@$row->title, 0, 15)).'
								</a> 
								<a href="javascript:void(0);" data-row-id="'.$row->id.'" class="fw-bold text-muted d-block fs-7 text-hover-primary client-show-detail ">
									'.ucwords(substr(@$row->company_name, 0, 15)).'
								</a> 
							</div> 
						</div> 
						<div class="'.$skilltagclass.'" style="position: absolute;bottom: 5px;left: 11px;white-space: nowrap;">
                                    <span class="badge badge-light-success my-1">Skills:</span>
                                 
                                  '.$skilltag.'';
            })
            ->editColumn('Exp', function ($row) {
                return '<div class="d-flex flex-column"> 
								<span class="text-dark fw-bolder fs-6">'.@$row->max_exp.'+</span> 
								<span class="text-muted fw-bold text-muted d-block fs-7">'.@$row->min_exp.'+</span> 
							</div>';
            })
            ->editColumn('Salary', function ($row) use ($salaryMode, $currencySymbole) {

                $min_sal_mode = $max_sal_mode = $min_salary_currency = $max_salary_currency = '';
                $min_sal_mode = ($salaryMode[$row->min_sal_mode]);
                $max_sal_mode = ($salaryMode[$row->max_sal_mode]);

                $min_salary_currency = $currencySymbole[@$row->min_salary_currency];
                $max_salary_currency = $currencySymbole[@$row->max_salary_currency];

                $max_salary = '';
                $max_salary = GeneralMethod::thousandsCurrencyFormat($row->max_salary);
                $min_salary = '';
                $min_salary = GeneralMethod::thousandsCurrencyFormat($row->min_salary);

                return '<div class="d-flex flex-column">
                    <span class="text-dark fw-bolder fs-6">'.$min_salary_currency.$max_salary.'/'.$min_sal_mode.'</span>                    
                    <span class="fw-bold text-muted d-block fs-7">'.$min_salary_currency.$min_salary.'/'.$max_sal_mode.'</span>
                  </div>';
            })
            ->editColumn('Location', function ($row) {

                // dd($row);

                $country_id = $country_name = '';
                $jobid = $row->id;
                $client_loc = DB::table('client_location_jobs')
                    ->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
                    ->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')
                    ->select('client_location.address', 'client_location.city', 'client_location.postcode', 'client_location.country_id', 'city_list.city as city_name')
                    ->where('client_location_jobs.job_id', $jobid)
                    ->first();

                $country_id = @$client_loc->country_id;
                if (@$country_id > 0) {
                    $country_name = GeneralMethod::getCountryName($country_id);
                    foreach ($country_name as $cval) {
                        $country_name = ucwords($cval['sortname']);
                    }
                }

                $city = substr(@$client_loc->city, 0, 6);
                // if (@$client_loc->country_id == 101) {
                $city = substr(@$client_loc->city_name, 0, 6);
                // }

                $jobLocation = DB::table('client_location_jobs')
                    ->where('client_location_jobs.job_id', $jobid)->count();

                $jobCitySelected = DB::table('client_city_jobs')
                    ->where('client_city_jobs.job_id', $jobid)->count();

                $employment_type = 'Full-time';
                if ($row->employment_type == 1) {
                    $employment_type = 'Contract';
                }

                if ($row->work_type == 'yes') {
                    return ' <div class="d-flex flex-column">
						<span class="text-dark fw-bolder fs-6">Remote</span>
						<div class="d-flex align-items-center">
						  <span class="fw-bold text-muted d-block fs-7" data-toggle="tooltip4" title="'.ucwords(@$employment_type).'" >'.ucwords(substr(@$employment_type, 0, 10)).'</span>
						  </div>
						</div>
					  </div>';
                } elseif ($row->work_type == 'no') {

                    if ($jobCitySelected == 1 || $jobCitySelected == 0) {
                        $client_loc = DB::table('client_city_jobs')
                            ->leftJoin('city_list', 'city_list.city_id', '=', 'client_city_jobs.city_id')
                            ->leftJoin('state_list', 'state_list.id', '=', 'city_list.state_id')
                            ->leftJoin('countries', 'countries.id', '=', 'state_list.country_id')
                            ->select('city_list.city as city_name', 'countries.country_name')
                            ->where('client_city_jobs.job_id', $jobid)
                            ->first();
                        $city = substr(@$client_loc->city_name, 0, 21);
                        $country_name = substr(@$client_loc->country_name, 0, 6);

                        return ' <div class="d-flex flex-column">
							<div class="d-flex align-items-center">
								  <span class="text-dark fw-bolder fs-6">'.ucwords(substr(@$city, 0, 10)).'</span>
								</div>
								
							  <span class="fw-bold text-muted d-block fs-7" data-toggle="tooltip4" title="'.ucwords(@$employment_type).'" >'.ucwords(substr(@$employment_type, 0, 10)).'</span>
							  </div>
							</div>
						  </div>';
                    } else {
                        return ' <div class="d-flex flex-column">
							<div class="d-flex align-items-center">
								  <span class="text-dark fw-bolder fs-6">Multiple</span>
								</div>
							<span class="fw-bold text-muted d-block fs-7" data-toggle="tooltip4" title="'.ucwords(@$employment_type).'" >'.ucwords(substr(@$employment_type, 0, 10)).'</span>
						  </div>
						</div>
					  </div>';
                    }

                } elseif ($row->work_type == 'any') {

                    if ($jobCitySelected == 1 || $jobCitySelected == 0) {
                        $client_loc = DB::table('client_city_jobs')
                            ->leftJoin('city_list', 'city_list.city_id', '=', 'client_city_jobs.city_id')
                            ->leftJoin('state_list', 'state_list.id', '=', 'city_list.state_id')
                            ->leftJoin('countries', 'countries.id', '=', 'state_list.country_id')
                            ->select('city_list.city as city_name', 'countries.country_name')
                            ->where('client_city_jobs.job_id', $jobid)
                            ->first();
                        $city = substr(@$client_loc->city_name, 0, 21);
                        $country_name = substr(@$client_loc->country_name, 0, 6);

                        return ' <div class="d-flex flex-column">
							<div class="d-flex align-items-center">
								  <span class="text-dark fw-bolder fs-6">'.ucwords(substr(@$city, 0, 10)).'</span>
								</div>
								
							<span class="fw-bold text-muted d-block fs-7" data-toggle="tooltip4" title="'.ucwords(@$employment_type).'" >'.ucwords(substr(@$employment_type, 0, 10)).'</span>
							  </div>
							</div>
						  </div>';
                    } else {
                        return ' <div class="d-flex flex-column">
						<div class="d-flex align-items-center">
						  <span class="text-dark fw-bolder fs-6">Remote</span>
						  &nbsp;|&nbsp;
								  <span class="text-dark fw-bolder fs-6">Multiple</span>
								</div>
							<span class="fw-bold text-muted d-block fs-7" data-toggle="tooltip4" title="'.ucwords(@$employment_type).'" >'.ucwords(substr(@$employment_type, 0, 10)).'</span>

						  </div>
						</div>
					  </div>';
                    }

                }

            })

            ->editColumn('Status', function ($row) use ($job_status) {

                $status = $active = $hold = $inactive = $class = $recommendation_id = '';
                $status = ucwords($row->status);
                $class = $job_status[$row->status];
                $recommendation_id = $row->recommendation_relation_id;
                if ($status == 'Inactive') {
                    $status = 'Closed';
                }
                // $final_status = "";
                if ($row->final_status == 2 || $row->final_status == 3) {
                    $status = 'Removed';
                    $class = 'closed-bg';
                }

                $ulink = '<a href="javascript.void(0);" data-row-id="'.@$row->id.'" class="d-flex flex-column '.@$class.' status_stage text-hover-success" data-toggle="modal" data-target="#status_stage">'.$status.'</a>';

                $recommendation_text = 'Custom';
                if ($recommendation_id > 0 && @$row->to_company_id == auth()->user()->company_id) {
                    $recommendation_text = 'Client';
                    $ulink = '<a href="javascript:void(0);" class="fs-6 '.@$class.' text-hover-success">'.$status.'</a>';
                }

                return '<div class="d-flex flex-column">
                       '.$ulink.'
                      <span class="fw-bold text-muted d-block fs-7">R'.@$row->id.' -'.$recommendation_text.'</span>
                    </div>';
            })
            ->editColumn('Date', function ($row) {

                $total_positions = '';
                $total_positions = number_format($row->total_positions);

                $created_at = '';
                $created_at = $row->created_at;
                $newDate = GeneralMethod::ymdTodmyNotTime($created_at); // date("d M, Y", strtotime($created_at));

                return '<div class="d-flex flex-column">
                        <span class="text-dark fw-bolder fs-6">'.$newDate.'</span>
                        <span class="text-muted fw-bold d-block fs-7">'.$total_positions.' Opening</span>
                    </div>';
            })
            ->editColumn('HM', function ($row) use ($feedback_status) {
                $feedback = ucwords(substr(@$feedback_status[@$row->conversations_status]['name'], 0, 20));
                if (empty($feedback)) {
                    $feedback = '<button type="button" class="btn btn-secondary">Add Note</button>';
                }

                return '<div class="d-flex flex-column show-feedback"  data-row-job_id="'.$row->id.'" data-row-conversations_id="'.$row->conversations_id.'" data-row-recommendation_relation_id="'.$row->recommendation_relation_id.'">
				<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6 ">'.@$feedback.'</a>
				<span class="text-muted fw-bold text-muted d-block fs-7"></span>
				</div>';
            })
            ->editColumn('Terms', function ($row) {
                $html = '<div data-row-id="'.$row->id.'" data-row-recommendation_relation_id="'.$row->recommendation_relation_id.'" class="client-terms-listing"><i class="fa-solid fa-eye" style="font-size: 20px;"></i></div>';

                return $html;
            })
            ->editColumn('company_name_to', function ($row) {
                $newDate = GeneralMethod::ymdTodmyNotTime($row->from_created_at);
                $html = '<div data-row-id="'.$row->recommendation_relation_id.'" data-row-recommendation_relation_id="'.$row->company_name_to.'" class="client-terms-listing"><a href="javascript:void(0);">
                <span class="text-dark fw-bolder fs-6">'.$row->company_name_to.'</span>
                <span class="text-muted fw-bold d-block fs-7">Req Date, '.$newDate.'</span></a>
                </div>';

                return $html;
            })
            ->editColumn('Action', function ($row) {
                return '<div class="d-flex my-4"> 
				<a href="javascript:void(0);" data-row-status="ignore" data-row-id="'.$row->id.'"  data-row-recommendation_relation_id="'.$row->recommendation_relation_id.'" class="btn btn-sm btn-light me-2 request_approval">
					Ignore
				</a> 
				<a href="javascript:void(0);" data-row-status="accept" data-row-id="'.$row->id.'" data-row-recommendation_relation_id="'.$row->recommendation_relation_id.'" class="btn btn-sm btn-primary request_approval">
					Accept
				</a> 
				</div>';
            })
            ->rawColumns(['chkbox', 'Requisition', 'Exp', 'Salary', 'Location', 'Status', 'Date', 'HM', 'Terms', 'Action', 'company_name_to'])
            ->addIndexColumn()
            ->make(true);
    }

    public function jobLists($id)
    {
        $this->pageTitle = __('All Jobs');
        $this->dataCnt = 0;

        return view('super-admin.jobs.all-jobs-data', $this->data);
    }

    public function all_jobs_data_ajax(Request $request)
    {

        $company_id = 2;
        $limit = '';
        $userId = $this->user->id;

        $job_status = GeneralMethod::job_status();
        $salaryMode = GeneralMethod::salaryMode();
        $currencySymbole = GeneralMethod::currencySymbole();

        $searchQuery = $request->input('q');
        $page = max((int) $request->input('page', 1), 1);
        $size = (int) $request->input('size', 10); // Default 20 results per page
        $from = ($page - 1) * $size;
        $custom_status = request('custom_status');
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

        // 1. STATUS FILTERING
        // if (!session()->has('UrlMailJobId')) {
        if ($custom_status !== '' && $custom_status !== 'all') {
            if (in_array($custom_status, ['active', 'hold', 'inactive'])) {
                $statusArr = [$custom_status];
            } elseif ($custom_status === 'removed') {
                $statusArr = [];
                $must[] = [
                    'nested' => [
                        'path' => 'recommendation_relation',
                        'query' => [
                            'term' => ['recommendation_relation.final_status' => 2],
                        ],
                    ],
                ];

            }
        }

        $filter[] = ['terms' => ['status' => $statusArr]];
        // }

        // 2. HIRING TYPE
        if ($custom_hiringtype !== '' && $custom_hiringtype !== 'All') {
            if ($custom_hiringtype == 7) {
                $filter[] = ['term' => ['employment_type' => 0]];
            } elseif ($custom_hiringtype == 8) {
                $filter[] = ['term' => ['employment_type' => 1]];
            }
        }

        // 3. SEARCH CONDITIONS (similar to LIKE, IN, etc.)
        if (! empty($custom_search)) {

            $must[] = [
                'bool' => [
                    'must' => [
                        [
                            'bool' => [
                                'should' => [
                                    ['match_phrase_prefix' => ['title' => '*'.strtolower($custom_search).'*']],
                                    ['term' => ['id' => (int) $custom_search]],
                                    ['wildcard' => ['client_name' => '*'.strtolower($custom_search).'*']],
                                    ['wildcard' => ['company_name' => '*'.strtolower($custom_search).'*']],
                                    [
                                        'nested' => [
                                            'path' => 'job_assign',
                                            'query' => [
                                                'wildcard' => ['job_assign.user_name' => '*'.strtolower($custom_search).'*'],
                                            ],
                                        ],
                                    ],
                                    [
                                        'nested' => [
                                            'path' => 'client_contact_info_jobs',
                                            'query' => [
                                                'wildcard' => ['client_contact_info_jobs.contact_person_name' => '*'.strtolower($custom_search).'*'],
                                            ],
                                        ],
                                    ],
                                    [
                                        'nested' => [
                                            'path' => 'client_city_jobs',
                                            'query' => [
                                                'wildcard' => ['client_city_jobs.city_name' => '*'.strtolower($custom_search).'*'],
                                            ],
                                        ],
                                    ],
                                    [
                                        'nested' => [
                                            'path' => 'client_city_jobs',
                                            'query' => [
                                                'wildcard' => ['client_city_jobs.country_name' => '*'.strtolower($custom_search).'*'],
                                            ],
                                        ],
                                    ],
                                ],
                                'minimum_should_match' => 1,
                            ],
                        ],
                    ],
                ],
            ];
        }

        // FINAL ELASTIC PARAMS
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

        $pagination = [
            'total' => $total,
            'current_page' => $page,
            'last_page' => ceil($total / $size),
            'listType' => 'allcandidate',
        ];

        $userPermissions = [];

        return response()->json([
            'html' => view('super-admin.ajax_pages.all_jobs_ajax', compact('data', 'userPermissions', 'limit', 'salaryMode', 'currencySymbole', 'job_status'))->render(),
            'total' => $total,
            'pagination' => view('super-admin.ajax_pages.pagination', compact('pagination'))->render(),
        ]);

    }

    public function canndidateList()
    {
        $this->pageTitle = __('All Candidate');
        $this->dataCnt = 0;

        return view('super-admin.candidates.index', $this->data);
    }

    public function canndidateListAjax(Request $request)
    {
        // dd($request->all());

        $currency = GeneralMethod::currency();
        $salaryMode = GeneralMethod::salaryMode();
        $currencySymbole = GeneralMethod::currencySymbole();

        $job_status_arr = GeneralMethod::job_status();

        $searchQuery = $request->input('q');
        $page = max((int) $request->input('page', 1), 1);
        $size = (int) $request->input('size', 10); // Default 20 results per page
        $from = ($page - 1) * $size;
        $valCheck = $request->input('sortingfilter');

        $searchTerms = request('cus_search');
        $custom_search = $searchTerms; // json_decode($searchTerms, true);
        $conditions = [];
        if (! empty($custom_search)) {
            $conditions[] = [
                'bool' => [
                    'should' => [
                        [
                            'match_phrase_prefix' => ['full_name' => '*'.strtolower($custom_search).'*'],
                        ],
                        [
                            'wildcard' => ['email' => $custom_search],
                        ],
                        [
                            'wildcard' => ['phone' => $custom_search],
                        ],
                        [
                            'match_phrase_prefix' => ['title' => '*'.strtolower($custom_search).'*'],
                        ],
                        [
                            'wildcard' => ['country_name' => '*'.strtolower($custom_search).'*'],
                        ],
                        [
                            'match_phrase_prefix' => ['qualification_name' => '*'.strtolower($custom_search).'*'],
                        ],
                        [
                            'wildcard' => ['state_name' => '*'.strtolower($custom_search).'*'],
                        ],
                        [
                            'wildcard' => ['city_name' => '*'.strtolower($custom_search).'*'],
                        ],
                        [
                            'wildcard' => ['posted_by' => '*'.strtolower($custom_search).'*'],
                        ],
                        [
                            'wildcard' => ['company_name' => '*'.strtolower($custom_search).'*'],
                        ],
                    ],
                    'minimum_should_match' => 1,
                ],
            ];
        }

        $country = request('country');
        $limit = '';
        $sort = [];
        switch ($valCheck) {
            case 1:
                $sort[] = ['created_at' => ['order' => 'desc']];
                break;
            case 2:
                $sort[] = ['created_at' => ['order' => 'asc']];
                break;
            case 3:
                $sort[] = ['ctc' => ['order' => 'asc', 'unmapped_type' => 'float']];
                break;
            case 4:
                $sort[] = ['ctc' => ['order' => 'desc', 'unmapped_type' => 'float']];
                break;
            case 5: // Notice period ASC (ignore Immediate)
                $sort[] = [
                    '_script' => [
                        'type' => 'number',
                        'script' => [
                            'lang' => 'painless',
                            'source' => "
								if (!doc.containsKey('np_id.keyword') || doc['np_id.keyword'].size() == 0 || doc['np_id.keyword'].value == 'Immediate') {
									return 999999; // Put missing/Immediate at the end
								}
								try {
									// Extract first number from comma-separated values
									String[] parts = doc['np_id.keyword'].value.splitOnToken(',');
									if (parts.length > 0) {
										return Double.parseDouble(parts[0].trim());
									}
								} catch (Exception e) {
									// If parsing fails
								}
								return 999999; // Invalid values at the end
							",
                        ],
                        'order' => 'asc',
                    ],
                ];
                break;
            case 6: // Notice period DESC (ignore Immediate)
                $sort[] = [
                    '_script' => [
                        'type' => 'number',
                        'script' => [
                            'lang' => 'painless',
                            'source' => "
								if (!doc.containsKey('np_id.keyword') || doc['np_id.keyword'].size() == 0 || doc['np_id.keyword'].value == 'Immediate') {
									return -1; // Put missing/Immediate at the end
								}
								try {
									// Extract first number from comma-separated values
									String[] parts = doc['np_id.keyword'].value.splitOnToken(',');
									if (parts.length > 0) {
										return Double.parseDouble(parts[0].trim());
									}
								} catch (Exception e) {
									// If parsing fails
								}
								return -1; // Invalid values at the end
							",
                        ],
                        'order' => 'desc',
                    ],
                ];
                break;
            case 7:
                $sort[] = ['total_exp' => ['order' => 'desc', 'unmapped_type' => 'float']];
                break;
            case 8:
                $sort[] = ['total_exp' => ['order' => 'asc', 'unmapped_type' => 'float']];
                break;
            case 9: // Immediate joiners first (original functionality)
                $sort[] = [
                    '_script' => [
                        'type' => 'number',
                        'script' => [
                            'lang' => 'painless',
                            'source' => "params['_source']['immediate_join'] == 'Immediate' ? 0 : 1",
                        ],
                        'order' => 'asc',
                    ],
                ];
                break;
            case 10:
                $sort[] = ['send_accept_date_time' => ['order' => 'desc']];
                break;
            case 11:
                $sort[] = ['send_accept_date_time' => ['order' => 'asc']];
                break;
            case 12:
                $sort[] = ['of_joined_date' => ['order' => 'desc']];
                break;
            case 13:
                $sort[] = ['of_joined_date' => ['order' => 'asc']];
                break;
            case 14:
                $sort[] = ['of_offer_amount' => ['order' => 'asc', 'unmapped_type' => 'float']];
                break;
            case 15:
                $sort[] = ['of_offer_amount' => ['order' => 'desc', 'unmapped_type' => 'float']];
                break;
            default:
                $sort[] = ['wiseId' => ['order' => 'desc', 'unmapped_type' => 'long']];
                break;
        }

        $mustSubmittedBy = [];

        $params = [
            'index' => ELASTIC_CANDIDATES,
            'request_cache' => true,
            'body' => [
                'from' => $from,
                'size' => $size,
                'sort' => $sort,
                'track_total_hits' => true,
                'query' => [
                    'bool' => [
                        'must' => $conditions,
                    ],
                ],
            ],
        ];
        if (! $this->elasticService->indexExists(ELASTIC_CANDIDATES)) {
            $total = 0;
            $data = [];
        } else {
            $response = $this->elasticService->getClient()->search($params);
            $total = $response['hits']['total']['value'] ?? 0;
            $data = array_map(function ($hit) {
                return $hit['_source'];
            }, $response['hits']['hits']);
            $lastSort = end($response['hits']['hits'])['sort'] ?? null;
        }

        $pagination = [
            'total' => $total,
            'current_page' => $page,
            'last_page' => ceil($total / $size),
            'listType' => 'allcandidate',
        ];

        $userPermissions = [];

        return response()->json([
            'html' => view('super-admin.ajax_pages.all_candidates_ajax', compact('data', 'userPermissions', 'limit', 'salaryMode', 'currencySymbole', 'job_status_arr', 'currency'))->render(),
            'total' => $total,
            'pagination' => view('super-admin.ajax_pages.pagination', compact('pagination'))->render(),
        ]);

    }

    public function candidatedata(Request $request)
    {
        // dd($request->all());

        $currency = GeneralMethod::currency();
        $salaryMode = GeneralMethod::salaryMode();
        $currencySymbole = GeneralMethod::currencySymbole();

        $job_status_arr = GeneralMethod::job_status();

        // 'application_status.status',
        $jobApplications = DB::table('job_applications')
            ->select(
                'job_applications.*',
                'jobs.title',
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
                'recommendation_relation.final_status',
                'recommendation_relation.id as recommendation_id',
                'recommendation_relation.from_company_id',
                'recommendation_relation.to_company_id',
                'companies.company_name',
                'app_com.company_name as apl_company_name',
                'cur_com.company_name as cur_company_name',
                'jobs.status as job_status',
                'add_job_wise_condidates.child_status_id as childStatus',
                'add_job_wise_condidates.id as wiseId',
                'add_job_wise_condidates.company_id as wiseIdcompany_id',
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
                'add_job_wise_condidates.job_id as jobId'
            )
            ->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id')
            ->join('master_status', function ($join) {
                $join->on('master_status.id', 'add_job_wise_condidates.master_status_id');
            })
            ->leftjoin('offer_job_applications', function ($join) {
                $join->on('offer_job_applications.joined', 'add_job_wise_condidates.id');
            })
            ->join('jobs', function ($join) {
                $join->on('jobs.id', 'add_job_wise_condidates.job_id');
            })
            ->leftjoin('companies as app_com', function ($join) {
                $join->on('app_com.id', 'job_applications.company_id');
            })
            // ->join('jobs', 'jobs.id', 'job_applications.job_id')
            ->leftjoin('job_skills', 'jobs.id', 'job_skills.job_id')
            ->leftjoin('companies as cur_com', function ($join) {
                $join->on('cur_com.id', 'jobs.company_id');
            })
            // ->leftjoin('master_status', 'master_status.id', 'job_applications.master_status_id')
            ->leftjoin('job_locations', 'job_locations.id', 'jobs.location_id')
            ->leftjoin('clients', 'clients.id', 'jobs.client_id')
            ->leftjoin('users', 'users.id', 'add_job_wise_condidates.submitted_by')
            ->leftjoin('companies', 'companies.id', 'users.company_id')
            ->leftjoin('qualification_master', 'qualification_master.id', 'job_applications.qualification')
            ->leftjoin('application_status', 'application_status.id', 'job_applications.status_id')
            ->leftJoin('recommendation_relation', 'recommendation_relation.job_id', '=', 'jobs.id')
            ->leftjoin('state_list', 'state_list.id', 'job_applications.state');
        //    dd(request('status_cus'));
        if (\request('status_cus') != '') {
            // dd(request('status_cus'),"1",request('profile'));
            $status_cus = request('status_cus');
            //    dd($status_cus);
            $jobApplications = $jobApplications->where('add_job_wise_condidates.master_status_id', $status_cus);

        } else {
            // dd(request('status_cus'),"2",request('profile'),$chkAdminRole);

            $searchTerms = request('cus_search');
            $custom_search = $searchTerms; // json_decode($searchTerms, true);

            if (\request('cus_search') != '') {

                $jobApplications = $jobApplications->where(function ($query) use ($custom_search) {
                    $query->where('job_applications.full_name', 'like', "%{$custom_search}%");
                    $query->orWhere('job_applications.email', 'like', "%{$custom_search}%");
                    $query->orWhere('job_applications.phone', 'like', "%{$custom_search}%");
                    $query->orWhere('jobs.title', 'like', "%{$custom_search}%");
                });
            }
            if (\request('country') != '') {

                $country = request('country');
                $jobApplications = $jobApplications->where(function ($query) use ($country) {
                    $query->where('job_applications.country', '=', $country);
                });

            }

            // $jobApplications = $jobApplications->orWhere(function ($query) {
            // 	return
            // 		$query->where('add_job_wise_condidates.thirdparty_company_id', '>', 0)
            // 			->where('recommendation_relation.job_id', '>', 0)
            // 			->where('add_job_wise_condidates.master_status_id', '!=', 8);
            // });

            // $jobApplications = $jobApplications->orWhere(function ($query) {
            // 	return
            // 		$query->where('recommendation_relation.job_id', '>', 0);
            // });

        }

        if (\request('limitget') != '') {
            $limit = (int) request('limitget');
            $jobApplications = $jobApplications->limit($limit);
        }
        $jobApplications = $jobApplications->groupBy('add_job_wise_condidates.id');

        if (\request('sortingfilter') != '') {
            $valCheck = request('sortingfilter');
            if ($valCheck == 1) {
                $jobApplications = $jobApplications->orderBy('add_job_wise_condidates.created_at', 'DESC');
            } elseif ($valCheck == 2) {
                $jobApplications = $jobApplications->orderBy('add_job_wise_condidates.created_at', 'ASC');
            } elseif ($valCheck == 3) {
                $jobApplications = $jobApplications->orderBy('add_job_wise_condidates.ctc', 'ASC');
            } elseif ($valCheck == 4) {
                $jobApplications = $jobApplications->orderBy('add_job_wise_condidates.ctc', 'DESC');
            } elseif ($valCheck == 5) {
                $jobApplications = $jobApplications->orderBy('job_applications.np_id', 'ASC');
            } elseif ($valCheck == 6) {
                $jobApplications = $jobApplications->orderBy('job_applications.np_id', 'DESC');
            } elseif ($valCheck == 9) {
                $jobApplications = $jobApplications->orderByRaw("FIELD(job_applications.np_id, 'Immediate') DESC")
                    ->orderBy('job_applications.np_id', 'DESC')
                    ->get();
            } elseif ($valCheck == 7) {
                $jobApplications = $jobApplications->orderBy('job_applications.total_exp', 'DESC');
            } elseif ($valCheck == 8) {
                $jobApplications = $jobApplications->orderBy('job_applications.total_exp', 'ASC');
            } elseif ($valCheck == 10) {
                $jobApplications = $jobApplications->orderBy('offer_job_applications.joined_date', 'DESC');
            } elseif ($valCheck == 11) {
                $jobApplications = $jobApplications->orderBy('offer_job_applications.joined_date', 'ASC');
            } elseif ($valCheck == 12) {
                $jobApplications = $jobApplications->orderBy('offer_job_applications.joined_date', 'DESC');
            } elseif ($valCheck == 13) {
                $jobApplications = $jobApplications->orderBy('offer_job_applications.joined_date', 'ASC');
            } elseif ($valCheck == 14) {
                $jobApplications = $jobApplications->orderBy('offer_job_applications.offer_amount', 'ASC');
            } elseif ($valCheck == 15) {
                $jobApplications = $jobApplications->orderBy('offer_job_applications.offer_amount', 'DESC');
            } else {
                $jobApplications = $jobApplications->orderBy('add_job_wise_condidates.id', 'DESC');
            }

        } else {
            $jobApplications = $jobApplications->orderBy('add_job_wise_condidates.id', 'DESC');
        }

        // dd($jobApplications->get());
        if (\request('limitget') != '') {
            $limit = (int) request('limitget');
        } else {
            $limit = '';
        }

        return DataTables::of($jobApplications)

            ->editColumn('chkbox', function ($row) {

                return '<div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input chk_list_item " name="chk_list_item[]" type="checkbox"  data-jobId="'.$row->jobId.'" value="'.$row->id.'" />
                  </div>';
            })
            ->editColumn('Name', function ($row) use ($limit) {

                $bgTextDecore = $strVal = $tredmark = '';
                $str = trim(@$row->full_name);
                if (! empty($str)) {
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

                    $this->company = Company::withoutGlobalScope('company')->findOrFail($row->client_comapny_id);
                    // dd($this->company);
                    $flag = 0;
                    $duplicate = $row->duplicate ?? 0;
                    $flag = 0;
                    if ($duplicate == 1) {
                        $flag = $duplicate;
                    }

                    $company_id = auth()->user()->company_id;
                    $profile_sidebar_css = 'show-application-detail';
                    if ($row->thirdparty_company_id > 0 && $row->thirdparty_company_id == $company_id) {
                        $profile_sidebar_css = 'show-application-detail';
                        // client-show-application-detail
                    }

                    if (@$row->condidate_type == 'bench_condidate') {
                        $tredmark = '[Bench]';
                    }

                    $photo = '';
                    if (! empty(@$row->photo)) {
                        //   $photo			=	 url('/public/user-uploads/candidate-photos/'.$row->photo);
                        $photo = @$row->photo;
                    }

                    if (! empty($photo)) {
                        $nameImg = '<div class="symbol symbol-100px symbol-lg-140px symbol-fixed position-relative"> <img src="'.@$photo.'" alt="image" style="width: 40px; height: 40px;">
              <div class="position-absolute translate-middle bottom-0 start-100 mb-6 bg-success rounded-circle border border-4 border-white h-20px w-20px"></div>
            </div>';
                    } else {
                        if ($flag == 1) {
                            $nameImg = '<span class="fs-3">'.strtoupper(@$strVal).'</span>';
                        } else {
                            $nameImg = '<div class="symbol-label '.@$bgTextDecore.' border-radius-5">'.strtoupper(@$strVal).'</div>';
                        }

                    }

                    $skilltag = '';
                    $skilltagclass = '';
                    $skillArr = explode(',', $row->skills);
                    if (empty($limit)) {
                        if (count($skillArr) > 0) {
                            foreach ($skillArr as $key => $sval) {
                                $skilltag .= '<span class="badge badge-light my-1 skill">'.ucfirst($sval).'</span>  ';
                            }
                        }
                    } else {
                        $skilltagclass = 'd-none';
                    }

                    if ($flag == 1) {
                        return '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
																				<a href="javascript:void(0);"  data-row-id="'.@$row->wiseId.'" class=" '.$profile_sidebar_css.' btn btn-icon btn-light-danger pulse pulse-danger mr-5" data-toggle="tooltip" title="Duplicate">
																				'.$nameImg.'
																				<span class="pulse-ring"></span>
																				</a>
																				</div>                  
																				<div class="d-flex flex-column">
																				<a href="javascript:void(0);"  data-row-id="'.@$row->wiseId.'"  class="text-danger fw-bolder text-hover-primary fs-6  '.$profile_sidebar_css.' " data-toggle="tooltip" title="'.ucwords(@$row->full_name).'">'.ucwords(substr(@$row->full_name, 0, 30)).$covid.'<sup style="color: #ef7c62 !important;
																				font-weight: bold;">'.$tredmark.'</sup></a>
																				<a href="javascript:void(0);"  data-row-id="'.@$row->wiseId.'"  class="text-danger fw-bold d-block fs-7 text-hover-primary '.$profile_sidebar_css.' " data-toggle="tooltip" title="'.ucwords(@$row->current_designation).'">'.ucwords(substr(@$row->current_designation, 0, 30)).'</a>
																				</div>
																				<div class="'.$skilltagclass.'" style="position: absolute; bottom: 0; white-space: nowrap; width: 94%; overflow: hidden;">
                                    <span class="badge badge-light-success my-1">Skills:</span>
                                 
                                  '.$skilltag.'
                                         
                                   
                                    </div> 	

																				';
                    } else {
                        return '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
																				<a href="javascript:void(0);"   data-row-id="'.@$row->wiseId.'" class="'.$profile_sidebar_css.'">
																				'.$nameImg.'
																				</a>
																				</div>                  
																				<div class="d-flex flex-column">
																				<a href="javascript:void(0);"  data-row-id="'.@$row->wiseId.'"  class="text-dark fw-bolder text-hover-primary fs-6 '.$profile_sidebar_css.' " data-toggle="tooltip" title="'.ucwords(@$row->full_name).'">'.ucwords(substr(@$row->full_name, 0, 30)).$covid.'<sup style="color: #ef7c62 !important;
																				font-weight: bold;">'.$tredmark.'</sup></a>
																				<a href="javascript:void(0);"  data-row-id="'.@$row->wiseId.'"  class="text-muted fw-bold d-block fs-7 text-hover-primary '.$profile_sidebar_css.' " data-toggle="tooltip" title="'.ucwords(@$row->current_designation).'">'.ucwords(substr(@$row->current_designation, 0, 30)).'</a>
																				</div>
																				<div class="'.$skilltagclass.'" style="position: absolute; bottom: 0; white-space: nowrap; width: 94%; overflow: hidden;">
                                    <span class="badge badge-light-success my-1">Skills:</span>
                                 
                                  '.$skilltag.'
                                         
                                   
                                    </div> 	
																				';
                    }

                }

            })
            ->editColumn('Contacts', function ($row) {
                return 1;

                $duplicate = $row->duplicate ?? 0;
                $flag = 0;
                if ($duplicate == 1) {
                    $flag = $duplicate;
                }

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
                        $bench_cnt_html = '<div data-row-id="'.$bench_row->bench_applications_id.'" class="symbol symbol-30 symbol-circle symbol-light bench_assign_listing" data-bs-placement="top" data-toggle="tooltip2" title="More users list.."> 
                        <span class="symbol-label font-weight-bold">'.$bench_cnt.'+</span> 
                    </div>';
                    }

                }

                if ($row->condidate_type == 'bench_condidate' && $row->company_id != auth()->user()->company_id) {
                    $row->phone = 'xxx-xxx-xxxx';
                    $row->email = 'xxx-xxx@email.com';
                }

                if (@$flag == 1) {
                    return '<div class="d-flex flex-column">
				<a href="javascript:void(0);" class="text-danger fw-bolder text-hover-primary fs-6">'.@$row->phone.'</a>
				<a href="javascript:void(0);" class="text-danger fw-bold d-block fs-7 text-hover-primary" data-toggle="tooltip" title="'.@$row->email.'">'.substr(@$row->email, 0, 10).$bench_cnt_html.'</a>
			</div>';
                } else {
                    return '<div class="d-flex flex-column">
			<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">'.@$row->phone.'</a>
			<a href="javascript:void(0);" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary" data-toggle="tooltip" title="'.@$row->email.'">'.substr(@$row->email, 0, 10).$bench_cnt_html.'</a>
			</div>';
                }

            })
            ->editColumn('Exp', function ($row) {

                return '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">'.@$row->total_exp.'+</a>
                    <span class="text-muted fw-bold d-block fs-7">'.@$row->rel_exp.'+</span>
                  </div>';
            })
            ->editColumn('Salary', function ($row) use ($salaryMode, $currencySymbole) {

                $current_ctc_cur = '';
                $current_ctc_cur = $currencySymbole[@$row->current_ctc_cur];

                $current_ectc_cur = '';
                $current_ectc_cur = $currencySymbole[@$row->current_ectc_cur];

                if ($row->condidate_type == 'bench_condidate') {
                    $current_ectc_cur = $currencySymbole[@$row->bill_rate_currency];
                }

                $ctc = '';
                $ctc = GeneralMethod::thousandsCurrencyFormat($row->ctc);
                $ectc = '';
                $ectc = GeneralMethod::thousandsCurrencyFormat($row->ectc);
                if ($row->condidate_type == 'bench_condidate') {
                    $ectc = GeneralMethod::thousandsCurrencyFormat($row->bill_rate_amount);
                }

                $current_ctc_mode = '';
                $current_ctc_mode = substr($salaryMode[@$row->current_ctc_mode], 0, 1);
                $current_ctc_mode = substr($salaryMode[0], 0, 1);

                $current_ectc_mode = '';
                $current_ectc_mode = substr($salaryMode[@$row->current_ectc_mode], 0, 1);
                $current_ectc_mode = substr($salaryMode[0], 0, 1);
                if ($row->condidate_type == 'bench_condidate') {
                    $current_ectc_mode = substr($salaryMode[@$row->bill_rate_mode], 0, 1);
                }
                $dnone = '';
                if ($row->condidate_type == 'bench_condidate') {
                    $dnone = 'd-none';
                }

                return '<div class="d-flex flex-column">
				<a href="javascript:void(0);" class="text-dark fw-bolder fs-6">'.$current_ectc_cur.@$ectc.'/'.$current_ectc_mode.'</a>
                    <span class="text-muted fw-bold d-block fs-7 '.$dnone.'">'.@$current_ctc_cur.@$ctc.'/'.$current_ctc_mode.'</span>
                  </div>';
            })
            ->editColumn('NoticePeriod', function ($row) {

                $np = $row->np_id;
                if (empty(@$np)) {
                    $np = 'N/A';
                }
                if (@$row->serving_notice_period_mode == 0) {
                    if (! empty(@$row->np_id)) {
                        $np = $row->np_id.'  Days';
                    }
                } elseif (@$row->serving_notice_period_mode == 1) {

                    $np = $row->np_id;
                    if ($np != 'Immediate') {
                        $np = GeneralMethod::ymdTodmyNotTime(@$row->np_id); // date("d-M-Y",strtotime(@$row->np_id));
                    }

                } else {
                    $np = $row->np_id;
                }
                $row->current_company = @$row->apl_company_name;
                if ($row->condidate_type === 'other') {
                    // $row->current_company = $row->cur_company_name; apl_company_name
                    $row->current_company = (@$row->current_company) ? @$row->current_company : @$row->current_company;
                }

                // $row->current_company = ($row->cur_company_name) ? $row->cur_company_name : @$row->apl_company_name;
                // $row->current_company = (@$row->current_company) ? @$row->current_company : @$row->current_company;
                return '<div class="d-flex flex-column">
						<a href="javascript:void(0);" class="text-dark fw-bolder fs-6">'.@$np.'</a>
						<span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="'.ucwords(@$row->current_company).'">'.ucwords(substr(@$row->current_company, 0, 20)).'</span>
					  </div>';
            })
            ->editColumn('Qualifaction', function ($row) {

                return ' <div class="d-flex flex-column">
						<a href="javascript:void(0);" class="text-dark fw-bolder fs-6" data-toggle="tooltip" title="'.@$row->qualification_name.'">'.ucwords(substr(@$row->qualification_name, 0, 10)).'</a>
						<span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="'.strtoupper(@$row->skills).'">'.strtoupper(substr(@$row->skills, 0, 15)).'</span> 
					  </div>';
            })
            ->editColumn('Location', function ($row) {

                $country = '';
                $row_country_name = '';

                if (empty($row->country)) {
                    $row_country_name = '----';
                } else {
                    if (! empty(@$row->country)) {
                        if (is_numeric(@$row->country)) {
                            $country_name_Arr = GeneralMethod::getCountryName(@$row->country);
                            foreach ($country_name_Arr as $cval) {
                                $row_country_name = ucwords($cval['name']);
                            }
                        }
                    }
                }

                $state = '';
                $row_state_name = '';
                if (empty($row->state)) {
                    $row_state_name = '----';
                } else {
                    if (@$row->country == 101) {
                        $row_state_name = @$row->state_name;
                    } else {
                        $state = @$row->state;
                        if (is_numeric(@$row->country) && is_numeric($state)) {
                            if ($state > 0) {
                                $state_name_Arr = GeneralMethod::getStateName(@$row->country, $state);
                                foreach ($state_name_Arr as $sval) {
                                    $row_state_name = ucwords($sval['name']);
                                }
                            }
                        }

                    }

                }

                $city = '';
                $row_city_name = '';

                if (@$row->country == 101) {
                    $row_city = DB::table('city_list')->select('*')->where('city_id', @$row->city_id)->first();
                    $row_city_name = @$row_city->city;
                } else {
                    $row_city_name = @$row->city;
                }

                return '<div class="d-flex flex-column">
                    	<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="'.ucwords($row_city_name).'">'.ucwords(substr($row_city_name, 0, 10)).'</a>
                    	<span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="'.ucwords($row_state_name).' | '.strtoupper($row_country_name).'">'.ucfirst(substr($row_state_name, 0, 10)).' | '.strtoupper(substr($row_country_name, 0, 2)).'</span>
                    </div>';

            })
            ->editColumn('Source', function ($row) {

                $bgTextDecore = GeneralMethod::icoinText(@$row->posted_by);
                $posted_by_image = '';
                if (! empty($row->posted_by_image)) {
                    // 	$posted_by_image = url('public/user-uploads/team_profile_img/' . @$row->posted_by_image);
                    $posted_by_image = @$row->posted_by_image;
                }

                // if(!empty($posted_by_image)){
                // return '<div class="d-flex flex-column">
                //     <div class="symbol-group symbol-hover">
                //       <div class="symbol symbol-30 symbol-circle" data-toggle="tooltip" title="' . @$row->posted_by . '">
                //         <img alt="Pic" src="' . @$posted_by_image . '">
                //       </div>
                //   </div>
                //   <span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="" data-bs-original-title="' . @$row->company_name . '">' . substr(@$row->company_name, 0, 10) . '</span>';
                // }else{
                return '<div class="d-flex flex-column">
							<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="" data-bs-original-title="'.@$row->posted_by.'">'.substr($row->posted_by, 0, 10).'</a>
						  </div>
						  <span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="" data-bs-original-title="'.@$row->company_name.'">'.substr(@$row->company_name, 0, 10).'</span>';
                // }

            })
            ->editColumn('UploadDate', function ($row) {

                $created_at = '';
                $created_at = $newDate = GeneralMethod::ymdTodmyNotTime($row->created_at); // date("d M , Y", strtotime($row->created_at));

                return '<div class="d-flex flex-column">
                    			<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">'.@$created_at.'</a> 
                  			</div>';
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
                $this->company = Company::withoutGlobalScope('company')->findOrFail($row->client_comapny_id);
                $final_status = '';
                if ($row->final_status == 2 && @$row->to_company_id == $row->client_comapny_id) {
                    // $status = 'REMOVED';
                    // $class = "closed-bg";
                }

                $ulink = '<a href="javascript:void(0);" class="d-flex flex-column '.@$class.'  text-hover-success" >'.$status.'</a>';

                $recommendation_text = 'Client';
                if (($recommendation_id > 0 && @$row->client_comapny_id == $row->from_company_id)) {
                    $recommendation_text = 'Custom';
                    $ulink = '<a href="javascript:void(0);" class="fs-6 '.@$class.' text-hover-success">'.$status.'</a>';
                }

                return '<div class="d-flex flex-column">
								   '.$ulink.'
								  <span class="fw-bold text-muted d-block fs-7">R'.@$row->jobId.' -'.$recommendation_text.'</span>
								</div>';
            })
            ->editColumn('OfferAmount', function ($row) use ($currency, $salaryMode) {

                $disabled = '&nbsp';
                $custom_style = '';
                $company_id = $row->wiseIdcompany_id;
                if ($row->thirdparty_company_id == $company_id) {
                    $disabled = '&nbsp; disabled';
                    $custom_style = '';
                }
                if ($row->to_company_id == $company_id) {
                    $disabled = '&nbsp; disabled';
                    $custom_style = '';
                }

                $sel = '';
                $option_currency = '';
                if (! empty($currency)) {
                    foreach ($currency as $key => $cval) {
                        $sel = '';
                        if ($row->of_offer_currency == $key) {
                            $sel = 'selected="selected"';
                            $option_currency = $cval;
                        }
                        // $option_currency .= '<option value="' . $key . '" ' . $sel . '>' . $cval . '</option>';
                    }
                }

                $option_salarymode = '';
                if (! empty($salaryMode)) {
                    foreach ($salaryMode as $key => $sval) {
                        $sel = '';
                        if ($row->of_offer_mode == $key) {
                            $sel = 'selected="selected"';
                            $option_salarymode = $sval;
                        }
                        // $option_salarymode .= '<option value="' . $key . '" ' . $sel . '>' . $sval . '</option>';
                    }
                }

                $offer_id = $row->offer_id;
                $create_offer = '';
                $create_offer_icon = '';
                $text_show = 'Offer Pending';
                if (@$row->client_comapny_id == auth()->user()->company_id) {
                    $create_offer = 'create_offer';
                    $create_offer_icon = ''; // '<i class="fa fa-pencil" style="position: absolute; right: 17px; top: 2px;"></i>';
                    $text_show = ucwords(substr('Create Offer', 0, 30));
                }
                if (! DB::table('offer_create_main_tds')->where('offer_id', '=', $offer_id)->where('status', '=', 1)->orderByDesc('id')->exists()) {

                    $raw_html = '<div class="d-flex flex-column '.$create_offer.'" data-row-offerid="'.@$offer_id.'" style="position: relative;">
								<a href="javascript.void(0);" class="d-flex flex-column " data-toggle="modal" data-target="#">
									<span class="badge fw-bolder fs-6 '.@$row->css_style.' px-2 py-1">'.$text_show.'</span>
									<span class="text-muted fw-bold d-block fs-7"></span>
									'.$create_offer_icon.'
								</a>
							</div>';
                } else {

                    $offer_create_main_tds = DB::table('offer_create_main_tds')->where('offer_id', '=', $offer_id)->where('status', '=', 1)->first();
                    $employment_type = 'Full-time';
                    if ($row->employment_type == 1) {
                        $employment_type = 'Contract';
                    }

                    $raw_html = '

				<div class="d-flex flex-column '.$create_offer.'" data-row-offerid="'.@$offer_id.'" style="position: relative;">
					<a href="javascript:void(0);" class="text-dark fw-bolder fs-6">'.$option_currency.' '.$offer_create_main_tds->yearly_final_gross.'/'.substr($option_salarymode, 0, 1).'</a>
						<span class="text-muted fw-bold d-block fs-7 ">'.$employment_type.'</span>
						'.$create_offer_icon.'
                </div>';
                }

                return @$raw_html;

            })
            ->editColumn('JoiningDate', function ($row) {

                $disabled = '&nbsp';
                $custom_style = '';
                $company_id = $row->wiseIdcompany_id;
                if ($row->thirdparty_company_id == $company_id) {
                    $disabled = '&nbsp; disabled';
                    $custom_style = '';
                }
                if ($row->to_company_id == $company_id) {
                    $disabled = '&nbsp; disabled';
                    $custom_style = '';
                }

                $joined_date = $reset_date = '';
                $joined_date = @$row->of_joined_date;

                $oneNineSevenZero = date('d-m-Y', strtotime($joined_date));
                if (! empty($joined_date) && ($oneNineSevenZero != '01-01-1970')) {
                    $reset_date = date('Y-m-d', strtotime($joined_date));
                }
                $offer_id = $row->offer_id;
                $offer_create_main_tds = DB::table('offer_create_main_tds')->where('offer_id', '=', $offer_id)->where('status', '=', 1)->first();

                $joined_date = $joined_time = $employment_type = $date = '-----';
                $date = @$offer_create_main_tds->joined_date;
                if (! empty($date)) {
                    $joined_date = date('d-M-y', strtotime($date));
                    $joined_time = date('H:i a', strtotime($date));
                    if (@$row->employment_type == 1) {
                        $employment_type = 'Contract';
                    } else {
                        $employment_type = 'Full-time';
                    }

                }

                return '<div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">'.$joined_date.'</span> <span class="text-muted fw-bold d-block fs-7">'.$joined_time.'</span> </div>
					';

            })
            ->editColumn('OfferStatus', function ($row) {

                $company_id = $row->wiseIdcompany_id;
                $offer_id = $row->offer_id;
                $offer_create_main_tds = DB::table('offer_create_main_tds')->where('offer_id', '=', $offer_id)->where('status', '=', 1)->first();

                $offer_status = $management_status = '----';
                $date = @$offer_create_main_tds->joined_date;
                $stage_default_css = 'stage_sub_stage';
                if ($offer_create_main_tds) {

                    if ($offer_create_main_tds->candidate_status == 1) {
                        $offer_status = 'Offer Released';
                    } elseif ($offer_create_main_tds->candidate_status == 2) {
                        $offer_status = 'Offer Accepted';
                    } elseif ($offer_create_main_tds->candidate_status == 3) {
                        $offer_status = 'Offer Rejected';
                    } elseif ($offer_create_main_tds->candidate_status == 4) {
                        $offer_status = 'Offer Expired';
                    } elseif ($offer_create_main_tds->candidate_status == 0) {
                        $offer_status = 'Offer Created';
                    }

                    if ($offer_create_main_tds->management_status == 1) {
                        $management_status = 'Approved';
                    } elseif ($offer_create_main_tds->management_status == 2) {
                        $management_status = 'Not Approved';
                    } elseif ($offer_create_main_tds->management_status == 3) {
                        $management_status = 'In Process';
                    }

                } else {
                    $offer_status = 'Pending';
                    $management_status = '----';
                }

                return '<div class="d-flex flex-column">
				              <span class="text-dark fw-bolder fs-6">'.$offer_status.'</span>
								
								</span> <span class="text-muted fw-bold d-block fs-7">'.$management_status.'</span>
							</div>';

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
                $employment_type = '-----';
                if (@$row->employment_type == 1) {
                    $employment_type = 'Contract';
                } else {
                    $employment_type = 'Full-time';
                }
                $offer_currency = $currencySymbole[($row->of_offer_currency) ? @$row->of_offer_currency : 0];

                $offer_amount = GeneralMethod::thousandsCurrencyFormat(($row->of_offer_amount) ? @$row->of_offer_amount : 0);

                $offer_mode = $salaryMode[($row->of_offer_mode) ? @$row->of_offer_mode : 0];

                return '<div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">'.@$offer_currency.' '.@$offer_amount.'/'.substr(@$offer_mode, 0, 1).'</span> 
				<span class="text-muted fw-bold d-block fs-7">'.$employment_type.'</span>
				</div>';

            })
            ->editColumn('Joining', function ($row) {

                $joined_date = $joined_time = $date = '';
                $date = @$row->of_joined_date;
                if (! empty($date)) {
                    $joined_date = date('d-M-y', strtotime($date));
                    $joined_time = date('H:i a', strtotime($date));
                }

                return '<div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">'.$joined_date.'</span> <span class="text-muted fw-bold d-block fs-7">'.$joined_time.'</span> </div>
					';
            })
            ->editColumn('Requisitions', function ($row) {

                $company_id = $row->wiseIdcompany_id;
                $removed_status = $icon = '';
                if ($row->final_status == 2) {
                    // $removed_status = '<div class=" fw-bolder fs-6 closed-bg px-2 py-1">REMOVED</div>';
                }
                // $icon				=	'&nbsp;<i class="bi bi-house" style="color:#00CC00"></i>';
                if ($row->thirdparty_company_id == $company_id) {
                    // $icon			=	'&nbsp;<i style="color: #FF0000; font-weight:bold" class="fa fa-users" aria-hidden="true"></i>';
                }
                $job_title = '';
                $job_title = $row->title;
                $job_title = ucwords(substr($job_title, 0, 15));
                $client_name = '';
                $client_title_name = '';
                $client_name_com = GeneralMethod::companyDetailsByClient($row->jobId);
                if (! empty($client_name_com)) {
                    $client_title_name = $client_name_com;
                    $client_name = ucwords(substr($client_title_name, 0, 15));
                } else {
                    $client_title_name = $row->client_name;
                    $client_name = ucwords(substr($client_title_name, 0, 15));
                }

                return '<div class="d-flex flex-column">
                    	<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="" data-bs-original-title="'.$row->title.'">'.$job_title.'</a>
                    	<span class="text-muted fw-bold text-muted d-block fs-7" data-toggle="tooltip" title="" data-bs-original-title="'.@$client_title_name.'">'.@$client_name.$icon.$removed_status.'</span>
                  	</div>';

            })
            ->editColumn('Stage', function ($row) {
                $company_id = $row->wiseIdcompany_id;
                $stage_default_css = 'stage_sub_stage';

                if (($row->thirdparty_company_id > 0 && $row->thirdparty_company_id == $company_id)) {
                    $stage_default_css = 'client_stage_sub_stage';
                }
                if (($row->thirdparty_company_id > 0 && $row->thirdparty_company_id == $company_id) && $row->master_status_id != 8) {
                    $stage_default_css = '';
                }
                $name = '';
                if ($row->master_status_id == 8) {
                    $name = '';
                }
                if (! empty(@$row->childStatus) && @$row->childStatus > 0) {
                    $childStatus = '';
                    $childStatus = DB::table('candidate_status')->select('*')->where('id', $row->childStatus)->first();
                    if (! empty($childStatus)) {
                        $name = @$childStatus->name;
                    }
                }

                return '<div class="d-flex flex-column">
								<a href="javascript.void(0);" data-row-id="'.@$row->wiseId.'" class="d-flex flex-column '.$stage_default_css.'" data-toggle="modal" data-target="#'.$stage_default_css.'">
									<span class="badge fw-bolder fs-6 '.@$row->css_style.' px-2 py-1">'.ucwords(substr($row->name, 0, 10)).'</span>
									<span class="text-muted fw-bold d-block fs-7">'.@$name.'</span>
								</a>
							</div>';
            })

            ->rawColumns(['chkbox', 'Name', 'Contacts', 'Exp', 'Salary', 'NoticePeriod', 'Qualifaction', 'Location', 'Source', 'UploadDate', 'Requisitions', 'Stage', 'OfferAmount', 'JoiningDate', 'Status', 'Offer', 'Joining', 'OfferStatus'])
            ->addIndexColumn()
            ->make(true);

    }

    public function candidatedatashow($id)
    {

        // dd($id);

        $company_id = '';
        $company_id = auth()->user()->company_id;
        // dd(auth()->user());

        $this->currency = GeneralMethod::currency();
        $this->salaryMode = GeneralMethod::salaryMode();
        $AddJobWiseCondidate = AddJobWiseCondidate::withoutGlobalScopes()->find($id);
        $jobId = @$AddJobWiseCondidate->job_id;
        // dd($id,$jobId,$AddJobWiseCondidate);
        $this->intModeArr = GeneralMethod::interview_mode();
        $this->application = JobApplication::select(
            'job_applications.id',
            'add_job_wise_condidates.submitted_by',
            'job_applications.submitted_by as editAllow',
            'add_job_wise_condidates.company_id',
            'job_applications.np_id',
            'job_applications.serving_notice_period_mode',
            'job_applications.full_name',
            'job_applications.email',
            'job_applications.phone',
            'job_applications.secondary_mobile',
            'job_applications.photo',
            'job_applications.resume',
            'job_applications.rating',
            'job_applications.skype_id',
            'job_applications.cover_letter',
            'add_job_wise_condidates.job_id',
            'job_applications.status_id',
            'job_applications.is_viewed',
            'job_applications.column_priority',
            'job_applications.created_at',
            'job_applications.updated_at',
            'job_applications.deleted_at',
            'job_applications.total_exp',
            'job_applications.rel_exp',
            'job_applications.passport',
            'job_applications.current_location',
            'job_applications.current_designation',
            'job_applications.current_company',
            'job_applications.process_id',
            'job_applications.qualification',
            'add_job_wise_condidates.master_status_id',
            'add_job_wise_condidates.child_status_id',
            'job_applications.secondrary_email',
            'job_applications.secondary_mobile',
            'job_applications.skills',
            'job_applications.city',
            'job_applications.country',
            'job_applications.pincode',
            'job_applications.state',

            'job_applications.current_ctc_cur',
            'job_applications.ctc',
            'job_applications.current_ctc_mode',
            'job_applications.current_ectc_cur',
            'job_applications.bill_rate_currency',
            'job_applications.bill_rate_amount',
            'job_applications.bill_rate_mode',
            'job_applications.pay_rate_currency',
            'job_applications.pay_rate_amount',
            'job_applications.pay_rate_mode',
            'job_applications.ectc',
            'job_applications.current_ectc_mode',
            'job_applications.percentage_hike',
            'job_applications.comment',
            'job_applications.dob',
            'job_applications.created_at',
            'job_applications.company_id as other_company',
            'job_applications.condidate_type',
            'clients.name as client_name',
            'jobs.title',
            'users.name as posted_by',
            'users.image as posted_by_image',
            // 'notice_periods.np_title',
            'master_status.name as master_status_name',
            'offer_job_applications.proposed_ctc',
            'offer_job_applications.any_offer',
            'offer_job_applications.document',
            'offer_job_applications.sub_for_approval',
            'offer_job_applications.offer_approved',
            'offer_job_applications.offer_release',
            'offer_job_applications.offer_acceptance',
            'offer_job_applications.joined',
            'offer_job_applications.joined_date',
            'offer_job_applications.offer_amount',
            'qualification_master.name as qualification_name',
            'companies.company_name',
            'owncom.company_name as own_company_name',
            'state_list.state as state_name',
            'city_list.city as city_name',
            'recommendation_relation.job_id as rjobid',
            'recommendation_relation.from_company_id',
            'add_job_wise_condidates.id as wiseId',
            'documents.hashname'
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
                $join->on('owncom.id', 'job_applications.company_id');
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
            // ->leftJoin('master_status', 'master_status.id', '=', 'job_applications.master_status_id')
            ->leftJoin('state_list', 'state_list.id', '=', 'job_applications.state')
            ->leftJoin('city_list', 'city_list.city_id', '=', 'job_applications.city_id');
        if ($AddJobWiseCondidate) {
            $this->application = $this->application->where('add_job_wise_condidates.id', $id);
        } else {
            $this->application = $this->application->where('job_applications.id', $id);
        }

        // $this->application = $this->application->where('add_job_wise_condidates.company_id', $company_id);
        $this->application = $this->application->first();

        //  dd($this->application);

        $this->client_contuser_arr = DB::table('client_contact_info_jobs')->join('client_contact_info', 'client_contact_info.id', '=', 'client_contact_info_jobs.client_contact_info_id')->join('clients', 'clients.id', '=', 'client_contact_info.client_id')->select('client_contact_info.contact_person_name', 'client_contact_info.contact_person_email', 'client_contact_info.contact_person_number', 'client_contact_info.contact_person_designation', 'clients.name')->where('client_contact_info_jobs.job_id', @$jobId)->get();

        $this->job_additional_detail = '';
        $this->job_additional_detail = DB::table('job_additional_detail')
            ->select('*')
            // ->where('job_additional_detail.company_id', '=', $company_id)
            ->where('job_additional_detail.job_id', '=', @$jobId)
            ->orderBy('job_additional_detail.id', 'ASC')
            ->get();

        $this->application_additional_detail = DB::table('application_additional_detail')
            ->select('*')
            ->where('application_additional_detail.application_id', '=', @$this->application->id)
            ->where('application_additional_detail.job_id', '=', @$jobId)
            ->orderBy('application_additional_detail.id', 'ASC')
            ->get();

        $this->bench_additional_detail = DB::table('bench_additional_detail')
            ->select('*')
            ->where('bench_additional_detail.bench_id', '=', $this->application->id)
            ->orderBy('bench_additional_detail.id', 'ASC')
            ->get();

        // echo '<pre>'; print_r($this->application); exit;
        if (! empty($this->application->qualification) && $this->application->qualification > 0) {
            $this->qualification = DB::table('qualification_master')->select('name')->where('id', $this->application->qualification)->first();
        }
        $this->answers = JobApplicationAnswer::with(['question'])->where('job_id', $jobId)->where('job_application_id', $this->application->id)->get();

        $this->notesArr = DB::table('applicant_notes')
            ->select(
                'applicant_notes.id',
                'applicant_notes.note_text',
                'applicant_notes.created_at',
                'applicant_notes.updated_at',
                'users.name',
                'users.image as posted_by_image'
            )
            ->join('job_applications', 'job_applications.id', 'applicant_notes.job_application_id')
            ->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id')
            // ->where('company_id', user()->company_id)
            ->join('users', 'users.id', '=', 'applicant_notes.user_id')
            ->where('add_job_wise_condidates.id', $id)
            ->where('applicant_notes.job_application_id', $this->application->id)
            ->where('applicant_notes.user_id', auth()->user()->id)
            ->orderBy('id', 'ASC')
            ->get();

        $this->schedule = DB::table('interview_schedules')->select('interview_schedules.id', 'interview_schedules.company_id', 'interview_schedules.job_application_id', 'interview_schedules.schedule_date', 'interview_schedules.status', 'interview_schedules.user_accept_status', 'interview_schedules.created_at', 'interview_schedules.updated_at', 'interview_schedules.available_status', 'interview_schedules.reschedule', 'interview_schedules.feedback')
            ->join('job_applications', 'job_applications.id', 'interview_schedules.job_application_id')
            ->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id')
            // ->where('interview_schedules.company_id', user()->company_id)
            // ->join('users', 'users.id', '=', 'applicant_notes.user_id')
            ->where('add_job_wise_condidates.id', $id)
            ->where('interview_schedules.job_application_id', $this->application->id)
            ->orderBy('id', 'DESC')
            ->get();

        // dd($this->schedule);

        $application_email = $this->application->email;
        $this->application_email = $application_email;
        $application_id = $this->application->id;
        $this->application_id = $application_id;
        $job_id = $jobId;
        $this->job_id = $job_id;

        $this->created_at = GeneralMethod::ymdTodmy($this->application->created_at);

        // UPDATE TIMELINE
        $this->updateTimeLine = DB::table('updatetimeline');
        $this->updateTimeLine = $this->updateTimeLine->select(
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
            'updatetimeline.candidate_status',
            'updatetimeline.management_status',
            'updatetimeline.accept_date_time',
            'updatetimeline.approved_date_time',
            'updatetimeline.email_id_hr',
            'updatetimeline.name_hr',
            'updatetimeline.yearly_gross',
            'updatetimeline.yearly_final_gross',
            'updatetimeline.mtypeSection',
            'updatetimeline.designation_offer',
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
        $this->updateTimeLine = $this->updateTimeLine->leftjoin('application_event', 'application_event.id', '=', 'updatetimeline.event_id');
        $this->updateTimeLine = $this->updateTimeLine->join('job_applications', 'job_applications.id', '=', 'updatetimeline.application_id');
        $this->updateTimeLine = $this->updateTimeLine->leftjoin('interview_schedules', 'interview_schedules.id', 'updatetimeline.interview_schedules_id');
        $this->updateTimeLine = $this->updateTimeLine->leftjoin('master_status', 'master_status.id', 'interview_schedules.stage');
        $this->updateTimeLine = $this->updateTimeLine->join('users', 'users.id', '=', 'updatetimeline.event_by_user_id');
        $this->updateTimeLine = $this->updateTimeLine->leftjoin('companies', 'companies.id', 'users.company_id');
        $this->updateTimeLine = $this->updateTimeLine->join('jobs', 'jobs.id', '=', 'updatetimeline.job_id');
        $this->updateTimeLine = $this->updateTimeLine->join('clients', 'clients.id', '=', 'jobs.client_id');

        $this->updateTimeLine = $this->updateTimeLine->where('updatetimeline.application_id', '=', $application_id);
        // $this->updateTimeLine = $this->updateTimeLine->where('updatetimeline.event_by_company_id', '=', auth()->user()->company_id);
        $this->updateTimeLine = $this->updateTimeLine->where('updatetimeline.job_id', '=', @$jobId);
        // $this->updateTimeLine = $this->updateTimeLine->orWhere(function ($query) use ($company_id, $id) {
        // 	return
        // 		$query->where('job_applications.thirdparty_company_id', '=', $company_id)
        // 			->where('job_applications.id', '=', $id);
        // 	//->where('staffing_company_relation.final_status', '=', 1);
        // });
        $this->updateTimeLine = $this->updateTimeLine->orderBy('updatetimeline.id', 'DESC');
        $this->updateTimeLine = $this->updateTimeLine->get();

        // dd(	$this->updateTimeLine);

        $sel_app_id = $id;
        $sel_job_id = $job_id;
        $this->benchStatusCnt = DB::table('rec_candidate_assign')->where('source_application_id', '=', $sel_app_id)->where('source_jobid', '=', $sel_job_id)->where('source_from_type', '!=', 0)->count();
        if ($this->benchStatusCnt > 0) {
            $benchAssignRow = DB::table('rec_candidate_assign')->select('rec_candidate_assign.*')->where('source_application_id', '=', $sel_app_id)->first();

            $source_from_company_id = @$benchAssignRow->source_from_company_id;
            $source_from_user_id = @$benchAssignRow->source_from_user_id;
            $source_from_id = @$benchAssignRow->source_from_id;
            $source_from_type = @$benchAssignRow->source_from_type;
            $source_company_id = @$benchAssignRow->source_company_id;

            $source_user_id = @$benchAssignRow->source_user_id;
            $source_application_id = @$benchAssignRow->source_application_id;
            $source_jobid = @$benchAssignRow->source_jobid;

            // dd($this->userPermissions);

            if ($company_id != $this->application->other_company && $source_from_type == 2) {
                $this->application->phone = '2xxx-xxx-xxxx';
                $this->application->secondary_mobile = '2xxx-xxx-xxxx';
                $this->application->email = '2xxx-xxx-xxxx';
                $this->application->secondrary_email = '2xxx-xxx-xxxx';
            }

            // if ($source_from_type == 2) {

            // }

            $this->bench_assign_row = DB::table('bench_assign')->select('users.id', 'users.name', 'users.email', 'users.mobile', 'users_profile.designation')
                ->join('users', 'users.id', '=', 'bench_assign.user_id')
                ->leftjoin('users_profile', 'users_profile.user_id', 'users.id')
                ->where('bench_assign.bench_applications_id', '=', $benchAssignRow->source_from_id)->get();
            $this->bench_additional_detail = DB::table('bench_additional_detail')
                ->select('bench_additional_detail.id', 'bench_additional_detail.custom_keys', 'bench_additional_detail.custom_keys_value')
                ->where('bench_additional_detail.bench_id', '=', $benchAssignRow->source_from_id)->get();

        }

        $this->userPermissions = [];
        $this->child_status_name = 'N/A';
        if ($this->application->child_status_id > 0) {
            $childStatus = DB::table('candidate_status')->select('*')->where('id', $this->application->child_status_id)->first();
            if (! empty($childStatus)) {
                $this->child_status_name = @$childStatus->name;
            }
        }
        $this->company_id = $this->user->company_id;
        $this->additional_skills_arr = DB::table('skills')->join('candidate_skills_value', 'skills.id', '=', 'candidate_skills_value.skill_id')
            // ->join('clients', 'clients.id', '=', 'client_contact_info.client_id')
            ->select('skills.name', 'candidate_skills_value.skill_value')->where('candidate_skills_value.job_id', $jobId)->where('candidate_skills_value.candidate_id', $this->application->id)->where('skills.company_id', $this->user->company_id)->get();
        $this->application_add_detail_cnt = DB::table('application_additional_detail')->select('*')->where('application_id', $this->application->id)->where('job_id', $jobId)->orderBy('application_additional_detail.id', 'ASC')->count();
        $this->application_add_detail = DB::table('application_additional_detail')->select('*')->where('application_id', $this->application->id)->where('job_id', $jobId)->orderBy('application_additional_detail.id', 'ASC')->get();
        $this->offer_job_applications_count = DB::table('offer_job_applications')->select('offer_job_applications.id')->join('jobs', 'jobs.id', '=', 'offer_job_applications.job_id')->join('clients', 'clients.id', '=', 'jobs.client_id')
            // ->leftjoin('countries', 'countries.id', 'job_applications.master_status_id')
            ->where('offer_job_applications.email', '=', $this->application->email)->where('offer_job_applications.company_id', '=', auth()->user()->company_id)->orderBy('offer_job_applications.id', 'DESC')->count();
        $this->offer_job_applications = DB::table('offer_job_applications')->select('offer_job_applications.id', 'offer_job_applications.application_id', 'offer_job_applications.submitted_by', 'offer_job_applications.company_id', 'offer_job_applications.np_id', 'offer_job_applications.full_name', 'offer_job_applications.email', 'offer_job_applications.phone', 'offer_job_applications.gender', 'offer_job_applications.dob', 'offer_job_applications.country', 'offer_job_applications.state', 'offer_job_applications.city', 'offer_job_applications.secondrary_email', 'offer_job_applications.secondary_mobile', 'offer_job_applications.skills', 'offer_job_applications.photo', 'offer_job_applications.resume', 'offer_job_applications.rating', 'offer_job_applications.skype_id', 'offer_job_applications.cover_letter', 'offer_job_applications.job_id', 'offer_job_applications.status_id', 'offer_job_applications.is_viewed', 'offer_job_applications.column_priority', 'offer_job_applications.created_at', 'offer_job_applications.updated_at', 'offer_job_applications.total_exp', 'offer_job_applications.rel_exp', 'offer_job_applications.passport', 'offer_job_applications.ctc', 'offer_job_applications.ectc', 'offer_job_applications.current_location', 'offer_job_applications.current_designation', 'offer_job_applications.current_company', 'offer_job_applications.process_id', 'offer_job_applications.qualification', 'offer_job_applications.master_status_id', 'offer_job_applications.child_status_id', 'offer_job_applications.serving_notice_period_mode', 'offer_job_applications.proposed_ctc', 'offer_job_applications.any_offer', 'offer_job_applications.document', 'offer_job_applications.sub_for_approval', 'offer_job_applications.offer_approved', 'offer_job_applications.offer_release', 'offer_job_applications.offer_acceptance', 'offer_job_applications.joined', 'offer_job_applications.joined_date', 'offer_job_applications.duplicate', 'jobs.title', 'clients.name as client_name')->join('jobs', 'jobs.id', '=', 'offer_job_applications.job_id')->join('clients', 'clients.id', '=', 'jobs.client_id')
            // ->leftjoin('countries', 'countries.id', 'job_applications.master_status_id')
            ->where('offer_job_applications.email', '=', $this->application->email)->where('offer_job_applications.company_id', '=', auth()->user()->company_id)
            // ->where('job_id', $this->application->job_id)
            ->orderBy('offer_job_applications.id', 'DESC')->get();
        // echo '<pre>'; print_r($this->offer_job_applications); exit;

        $resume = '';
        $resumeData = DB::table('documents')->select('*')->where('documentable_id', $this->application->id)->where('name', '=', 'Resume')->first();
        $this->resume = @$resumeData->hashname;

        $this->bench_assign_count = DB::table('bench_assign')->select('users.id', 'users.name', 'users.email', 'users.mobile', 'users_profile.designation', 'users.working_status')->join('users', 'users.id', '=', 'bench_assign.user_id')->leftJoin('users_profile', 'users_profile.user_id', '=', 'users.id')->where('bench_assign.bench_applications_id', '=', @$this->application->id)->count();

        $this->bench_assign_row = DB::table('bench_assign')->select('users.id', 'users.name', 'users.email', 'users.mobile', 'users_profile.designation')->join('users', 'users.id', '=', 'bench_assign.user_id')
            ->leftjoin('users_profile', 'users_profile.user_id', 'users.id')->where('bench_assign.bench_applications_id', '=', @$this->application->id)->get();

        $this->jobWiseCanId = $id;

        // $view = view('service_company.job-applications.show', $this->data)->render();
        // dd("jd");
        $view = view('super-admin.candidates.showdetail', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'view' => $view]);

    }

    public function job_request_accept($id)
    {
        $this->pageTitle = __('Job Request');
        $this->dataCnt = 0;

        return view('super-admin.jobs.job_request_accept', $this->data);
    }

    public function recommandedrequest_accept_ajax(Request $request)
    {
        $company_id = auth()->user()->company_id;

        $job_status = GeneralMethod::job_status();
        $salaryMode = GeneralMethod::salaryMode();
        $currencySymbole = GeneralMethod::currencySymbole();
        $feedback_status = GeneralMethod::conversation_status();

        $searchQuery = $request->input('q');
        $page = max((int) $request->input('page', 1), 1);
        $size = (int) $request->input('size', 10); // Default 20 results per page
        $from = ($page - 1) * $size;
        $valCheck = $request->input('sortingfilter');
        $mstatus_id = $request->input('mstatus_id');
        $sort = [];
        $country = '';
        $filters = [];
        $sort[] = ['final_status' => ['order' => 'asc', 'unmapped_type' => 'long']];
        $boolQuery = [
            'bool' => [
                'should' => [
                    [
                        'bool' => [
                            'must' => array_merge([
                                ['terms' => ['final_status' => [1, 2, 3]]],
                            ]),
                        ],
                    ],
                ],
                'minimum_should_match' => 1,
                'filter' => $filters, // We'll add all filters here
                'must_not' => [],
            ],
        ];
        $params = [
            'index' => ELASTIC_JOB_REQUEST,
            'request_cache' => true,
            'body' => [
                'from' => $from,
                'size' => $size,
                'sort' => $sort,
                'query' => $boolQuery,
            ],
        ];

        if (! $this->elasticService->indexExists(ELASTIC_JOB_REQUEST)) {
            $total = 0;
            $data = [];
        } else {
            $response = $this->elasticService->getClient()->search($params);
            $total = $response['hits']['total']['value'] ?? 0;
            $data = array_map(function ($hit) {
                return $hit['_source'];
            }, $response['hits']['hits']);
        }

        $pagination = [
            'total' => $total,
            'current_page' => $page,
            'last_page' => ceil($total / $size),
            'listType' => 'allcandidate',
        ];
        $userPermissions = [];

        return response()->json([
            'html' => view('super-admin.ajax_pages.recommandedrequestaccept_data_ajax', compact('data', 'userPermissions', 'salaryMode', 'currencySymbole', 'job_status', 'feedback_status'))->render(),
            'total' => $total,
            'pagination' => view('super-admin.ajax_pages.pagination', compact('pagination'))->render(),
        ]);

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
                'jobs.company_id as companyidcount',
                'jobs.end_date',
                'jobs.min_salary',
                'jobs.max_salary',
                'jobs.min_sal_mode',
                'jobs.max_sal_mode',
                'jobs.company_id as from_company_id',
                'jobs.min_salary_currency',
                'jobs.max_salary_currency',
                'recommendation_relation.to_company_id',
                'clients.name as clients_name',
                'clients.logo_logical_path',
                DB::raw('COUNT(job_applications.id) as allCandidate'),
                DB::raw('SUM(CASE WHEN job_applications.master_status_id = 1 THEN 1 ELSE 0 END) AS Sourcing'),
                DB::raw('SUM(CASE WHEN job_applications.master_status_id = 2 THEN 1 ELSE 0 END) AS Screening'),
                DB::raw('SUM(CASE WHEN job_applications.master_status_id = 3 THEN 1 ELSE 0 END) AS Interview'),
                DB::raw('SUM(CASE WHEN job_applications.master_status_id = 4 THEN 1 ELSE 0 END) AS Negotiation'),
                DB::raw('SUM(CASE WHEN job_applications.master_status_id = 5 THEN 1 ELSE 0 END) AS Offered'),
                DB::raw('SUM(CASE WHEN job_applications.master_status_id = 6 THEN 1 ELSE 0 END) AS Joined'),
                DB::raw('SUM(CASE WHEN job_applications.master_status_id = 7 THEN 1 ELSE 0 END) AS Rejected'),
                DB::raw('SUM(CASE WHEN job_applications.master_status_id = 8 THEN 1 ELSE 0 END) AS InReview'),
                'recommendation_relation.id as recommendation_relation_id',
                'recommendation_relation.id as recommendation_id',
                'recommendation_relation.final_status',
                'recommendation_relation.from_created_at',
                'companies.logo',
                'conversations.status as conversations_status',
                'conversations.id as conversations_id',
                'companies.company_name',
                'to_com.company_name as company_name_to'
            )
            ->join('clients', 'clients.id', 'jobs.client_id')
            ->join('companies', 'companies.id', 'clients.company_id')
            ->join('recommendation_relation', 'recommendation_relation.job_id', 'jobs.id')
            ->leftJoin('companies as to_com', 'to_com.id', '=', 'recommendation_relation.to_company_id')
            ->leftJoin('add_job_wise_condidates as job_applications', 'job_applications.job_id', '=', 'jobs.id')
            ->leftJoin('conversations', 'conversations.id', '=', 'recommendation_relation.conversation_id')
            ->where('jobs.id', '>', '0');

        if (\request('search') != '') {
            $search = request('search');
            $categories->where('jobs.title', 'LIKE', "%$search%");
        }
        // $categories->where('recommendation_relation.to_company_id', $company_id);
        $categories->whereIn('recommendation_relation.final_status', [1, 2, 3]);
        $categories->groupBy('jobs.id');
        $categories->orderBy('jobs.id', 'DESC');

        $perPage = 10;
        $page = $request->get('page', 1);
        // dd($page);
        $total = count($categories->get());
        // dd($total);
        $data = $categories->skip(($page - 1) * $perPage)->take($perPage)->get();

        $pagination = [
            'total' => $total,
            'current_page' => $page,
            'last_page' => ceil($total / $perPage),
            'listType' => 'allcandidate',
        ];
        // dd($pagination);
        $userPermissions = [];

        return response()->json([
            'html' => view('super-admin.ajax_pages.recommandedrequestaccept_data_ajax', compact('data', 'userPermissions', 'salaryMode', 'currencySymbole', 'job_status', 'feedback_status'))->render(),
            'total' => $total,
            'pagination' => view('super-admin.ajax_pages.pagination', compact('pagination'))->render(),
        ]);

        return DataTables::of($categories)

            ->editColumn('chkbox', function ($row) {})
            ->editColumn('Requisition', function ($row) {})
            ->editColumn('Exp', function ($row) {})
            ->editColumn('Salary', function ($row) {})
            ->editColumn('Location', function ($row) {

                // dd($row);

            })
            ->editColumn('Status', function ($row) {})
            ->editColumn('Date', function ($row) {})
            ->editColumn('HM', function ($row) {})
            ->editColumn('Terms', function ($row) {
                $html = '<div data-row-id="'.$row->id.'" data-row-recommendation_relation_id="'.$row->recommendation_relation_id.'" class="client-terms-listing"><i class="fa-solid fa-eye" style="font-size: 20px;"></i></div>';

                return $html;
            })
            ->editColumn('company_name_to', function ($row) {})
            ->editColumn('TOT', function ($row) {})
            ->editColumn('SOU', function ($row) {})
            ->editColumn('SCR', function ($row) {})
            ->editColumn('EVA', function ($row) {})
            ->editColumn('NEO', function ($row) {})
            ->editColumn('OFF', function ($row) {})
            ->editColumn('JOI', function ($row) {})
            ->editColumn('REJ', function ($row) {})
            ->editColumn('Action', function ($row) {
                return '<div class="d-flex my-4"> 
				<a href="javascript:void(0);" data-row-status="ignore" data-row-id="'.$row->id.'"  data-row-recommendation_relation_id="'.$row->recommendation_relation_id.'" class="btn btn-sm btn-light me-2 request_approval">
					Ignore
				</a> 
				<a href="javascript:void(0);" data-row-status="accept" data-row-id="'.$row->id.'" data-row-recommendation_relation_id="'.$row->recommendation_relation_id.'" class="btn btn-sm btn-primary request_approval">
					Accept
				</a> 
				</div>';
            })

            ->rawColumns(['chkbox', 'Requisition', 'Exp', 'Salary', 'Location', 'Status', 'Date', 'HM', 'Terms', 'Action', 'company_name_to', 'TOT', 'SOU', 'SCR', 'EVA', 'NEO', 'OFF', 'JOI', 'REJ'])
            ->addIndexColumn()
            ->make(true);
    }

    public function recommandedrequest_accept(Request $request)
    {
        $company_id = auth()->user()->company_id;

        $job_status = GeneralMethod::job_status();
        $salaryMode = GeneralMethod::salaryMode();
        $currencySymbole = GeneralMethod::currencySymbole();
        $feedback_status = GeneralMethod::conversation_status();
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
                'jobs.company_id as companyidcount',
                'jobs.end_date',
                'jobs.min_salary',
                'jobs.max_salary',
                'jobs.min_sal_mode',
                'jobs.max_sal_mode',
                'jobs.company_id as from_company_id',
                'jobs.min_salary_currency',
                'jobs.max_salary_currency',
                'recommendation_relation.to_company_id',
                'clients.name as clients_name',
                'clients.logo_logical_path',
                DB::raw('COUNT(job_applications.id) as allCandidate'),
                DB::raw('SUM(CASE WHEN job_applications.master_status_id = 1 THEN 1 ELSE 0 END) AS Sourcing'),
                DB::raw('SUM(CASE WHEN job_applications.master_status_id = 2 THEN 1 ELSE 0 END) AS Screening'),
                DB::raw('SUM(CASE WHEN job_applications.master_status_id = 3 THEN 1 ELSE 0 END) AS Interview'),
                DB::raw('SUM(CASE WHEN job_applications.master_status_id = 4 THEN 1 ELSE 0 END) AS Negotiation'),
                DB::raw('SUM(CASE WHEN job_applications.master_status_id = 5 THEN 1 ELSE 0 END) AS Offered'),
                DB::raw('SUM(CASE WHEN job_applications.master_status_id = 6 THEN 1 ELSE 0 END) AS Joined'),
                DB::raw('SUM(CASE WHEN job_applications.master_status_id = 7 THEN 1 ELSE 0 END) AS Rejected'),
                DB::raw('SUM(CASE WHEN job_applications.master_status_id = 8 THEN 1 ELSE 0 END) AS InReview'),
                'recommendation_relation.id as recommendation_relation_id',
                'recommendation_relation.id as recommendation_id',
                'recommendation_relation.final_status',
                'recommendation_relation.from_created_at',
                'companies.logo',
                'conversations.status as conversations_status',
                'conversations.id as conversations_id',
                'companies.company_name',
                'to_com.company_name as company_name_to'
            )
            ->join('clients', 'clients.id', 'jobs.client_id')
            ->join('companies', 'companies.id', 'clients.company_id')
            ->join('recommendation_relation', 'recommendation_relation.job_id', 'jobs.id')
            ->leftJoin('companies as to_com', 'to_com.id', '=', 'recommendation_relation.to_company_id')
            ->leftJoin('add_job_wise_condidates as job_applications', 'job_applications.job_id', '=', 'jobs.id')
            ->leftJoin('conversations', 'conversations.id', '=', 'recommendation_relation.conversation_id')
            ->where('jobs.id', '>', '0');

        if (\request('search') != '') {
            $search = request('search');
            $categories->where('jobs.title', 'LIKE', "%$search%");
        }
        // $categories->where('recommendation_relation.to_company_id', $company_id);
        $categories->whereIn('recommendation_relation.final_status', [1, 2, 3]);
        $categories->groupBy('jobs.id');
        $categories->orderBy('jobs.id', 'DESC');

        // dd($categories->get());

        return DataTables::of($categories)

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

                $icon = '<div data-row-id="'.$row->id.'" class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5 client-show-detail" style="float:left">
                    <a href="javascript:void(0);" data-row-id="'.$row->id.'" class="show-detail">
                      <div class="symbol-label '.$bgTextDecore.' border-radius-5">'.ucwords($strVal).'</div>
                    </a>
                  </div>';

                $logo_path = url('img/client-default.png');
                if (! empty(@$row->logo) && strlen(@$row->logo) > 10) {
                    $logo_path = @$row->logo;
                }

                if (! empty($logo_path)) {
                    $icon = '<div data-row-id="'.$row->id.'" class="symbol symbol-circle symbol-40px overflow-hidden me-2  border-radius-5 client-show-detail">
                                        <a href="javascript:void(0);">
                                            <div class="symbol-label border-radius-5">
                                            <img src="'.$logo_path.'" alt="Company Logo" class="border-radius-5 w-100">
                                            </div>
                                        </a>
                                    </div>';
                }

                $skilltag = '';
                $skilltagclass = '';
                if (empty($limit)) {
                    $skillArr = DB::table('job_metronics_skills')->select('*')->where('job_id', $row->id)->where('company_id', '=', $row->from_company_id)->get();
                    if (! empty($skillArr)) {
                        foreach ($skillArr as $sval) {
                            $skilltag .= '<span class="badge badge-light my-1">'.ucfirst($sval->skill_name).'</span>  ';
                        }
                    }
                } else {
                    $skilltagclass = 'd-none';
                }

                return ' <div class=" d-flex align-items-center border-0" >
							
							'.$icon.'
						   
                           <div class="d-flex flex-column"> 
						   		<a href="javascript:void(0);" data-row-id="'.$row->id.'" class="text-dark fw-bolder text-hover-primary fs-6 client-show-detail " data-toggle="tooltip1" title="" data-bs-original-title="2">
									'.ucwords(substr(@$row->title, 0, 15)).'
								</a> 
								<a href="javascript:void(0);" data-row-id="'.$row->id.'" class="fw-bold text-muted d-block fs-7 text-hover-primary client-show-detail ">
									'.ucwords(substr(@$row->company_name, 0, 15)).'
								</a> 
							</div> 
						</div> 
						<div class="'.$skilltagclass.'" style="position: absolute;bottom: 5px;left: 11px;white-space: nowrap;">
                                    <span class="badge badge-light-success my-1">Skills:</span>
                                 
                                  '.$skilltag.'';
            })
            ->editColumn('Exp', function ($row) {
                return '<div class="d-flex flex-column"> 
								<span class="text-dark fw-bolder fs-6">'.@$row->max_exp.'+</span> 
								<span class="text-muted fw-bold text-muted d-block fs-7">'.@$row->min_exp.'+</span> 
							</div>';
            })
            ->editColumn('Salary', function ($row) use ($salaryMode, $currencySymbole) {

                $min_sal_mode = $max_sal_mode = $min_salary_currency = $max_salary_currency = '';
                $min_sal_mode = ($salaryMode[$row->min_sal_mode]);
                $max_sal_mode = ($salaryMode[$row->max_sal_mode]);

                $min_salary_currency = $currencySymbole[@$row->min_salary_currency];
                $max_salary_currency = $currencySymbole[@$row->max_salary_currency];

                $max_salary = '';
                $max_salary = GeneralMethod::thousandsCurrencyFormat($row->max_salary);
                $min_salary = '';
                $min_salary = GeneralMethod::thousandsCurrencyFormat($row->min_salary);

                return '<div class="d-flex flex-column">
                    <span class="text-dark fw-bolder fs-6">'.$min_salary_currency.$max_salary.'/'.$min_sal_mode.'</span>                    
                    <span class="fw-bold text-muted d-block fs-7">'.$min_salary_currency.$min_salary.'/'.$max_sal_mode.'</span>
                  </div>';
            })
            ->editColumn('Location', function ($row) {

                // dd($row);

                $country_id = $country_name = '';
                $jobid = $row->id;
                $client_loc = DB::table('client_location_jobs')
                    ->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
                    ->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')
                    ->select('client_location.address', 'client_location.city', 'client_location.postcode', 'client_location.country_id', 'city_list.city as city_name')
                    ->where('client_location_jobs.job_id', $jobid)
                    ->first();

                $country_id = @$client_loc->country_id;
                if (@$country_id > 0) {
                    $country_name = GeneralMethod::getCountryName($country_id);
                    foreach ($country_name as $cval) {
                        $country_name = ucwords($cval['sortname']);
                    }
                }

                $city = substr(@$client_loc->city, 0, 6);
                // if (@$client_loc->country_id == 101) {
                $city = substr(@$client_loc->city_name, 0, 6);
                // }

                $jobLocation = DB::table('client_location_jobs')
                    ->where('client_location_jobs.job_id', $jobid)->count();

                $jobCitySelected = DB::table('client_city_jobs')
                    ->where('client_city_jobs.job_id', $jobid)->count();

                $employment_type = 'Full-time';
                if ($row->employment_type == 1) {
                    $employment_type = 'Contract';
                }

                if ($row->work_type == 'yes') {
                    return ' <div class="d-flex flex-column">
						<span class="text-dark fw-bolder fs-6">Remote</span>
						<div class="d-flex align-items-center">
						  <span class="fw-bold text-muted d-block fs-7" data-toggle="tooltip4" title="'.ucwords(@$employment_type).'" >'.ucwords(substr(@$employment_type, 0, 10)).'</span>
						  </div>
						</div>
					  </div>';
                } elseif ($row->work_type == 'no') {

                    if ($jobCitySelected == 1 || $jobCitySelected == 0) {
                        $client_loc = DB::table('client_city_jobs')
                            ->leftJoin('city_list', 'city_list.city_id', '=', 'client_city_jobs.city_id')
                            ->leftJoin('state_list', 'state_list.id', '=', 'city_list.state_id')
                            ->leftJoin('countries', 'countries.id', '=', 'state_list.country_id')
                            ->select('city_list.city as city_name', 'countries.country_name')
                            ->where('client_city_jobs.job_id', $jobid)
                            ->first();
                        $city = substr(@$client_loc->city_name, 0, 21);
                        $country_name = substr(@$client_loc->country_name, 0, 6);

                        return ' <div class="d-flex flex-column">
							<div class="d-flex align-items-center">
								  <span class="text-dark fw-bolder fs-6">'.ucwords(substr(@$city, 0, 10)).'</span>
								</div>
								
							  <span class="fw-bold text-muted d-block fs-7" data-toggle="tooltip4" title="'.ucwords(@$employment_type).'" >'.ucwords(substr(@$employment_type, 0, 10)).'</span>
							  </div>
							</div>
						  </div>';
                    } else {
                        return ' <div class="d-flex flex-column">
							<div class="d-flex align-items-center">
								  <span class="text-dark fw-bolder fs-6">Multiple</span>
								</div>
							<span class="fw-bold text-muted d-block fs-7" data-toggle="tooltip4" title="'.ucwords(@$employment_type).'" >'.ucwords(substr(@$employment_type, 0, 10)).'</span>
						  </div>
						</div>
					  </div>';
                    }

                } elseif ($row->work_type == 'any') {

                    if ($jobCitySelected == 1 || $jobCitySelected == 0) {
                        $client_loc = DB::table('client_city_jobs')
                            ->leftJoin('city_list', 'city_list.city_id', '=', 'client_city_jobs.city_id')
                            ->leftJoin('state_list', 'state_list.id', '=', 'city_list.state_id')
                            ->leftJoin('countries', 'countries.id', '=', 'state_list.country_id')
                            ->select('city_list.city as city_name', 'countries.country_name')
                            ->where('client_city_jobs.job_id', $jobid)
                            ->first();
                        $city = substr(@$client_loc->city_name, 0, 21);
                        $country_name = substr(@$client_loc->country_name, 0, 6);

                        return ' <div class="d-flex flex-column">
							<div class="d-flex align-items-center">
								  <span class="text-dark fw-bolder fs-6">'.ucwords(substr(@$city, 0, 10)).'</span>
								</div>
								
							<span class="fw-bold text-muted d-block fs-7" data-toggle="tooltip4" title="'.ucwords(@$employment_type).'" >'.ucwords(substr(@$employment_type, 0, 10)).'</span>
							  </div>
							</div>
						  </div>';
                    } else {
                        return ' <div class="d-flex flex-column">
						<div class="d-flex align-items-center">
						  <span class="text-dark fw-bolder fs-6">Remote</span>
						  &nbsp;|&nbsp;
								  <span class="text-dark fw-bolder fs-6">Multiple</span>
								</div>
							<span class="fw-bold text-muted d-block fs-7" data-toggle="tooltip4" title="'.ucwords(@$employment_type).'" >'.ucwords(substr(@$employment_type, 0, 10)).'</span>

						  </div>
						</div>
					  </div>';
                    }

                }

            })

            ->editColumn('Status', function ($row) use ($job_status) {

                $status = $active = $hold = $inactive = $class = $recommendation_id = '';
                $status = ucwords($row->status);
                $class = $job_status[$row->status];
                $recommendation_id = $row->recommendation_relation_id;
                if ($status == 'Inactive') {
                    $status = 'Closed';
                }
                $final_status = '';
                if ($row->final_status == 2 || $row->final_status == 3) {
                    $status = 'Removed';
                    $class = 'closed-bg';
                }

                $ulink = '<a href="javascript.void(0);" data-row-id="'.@$row->id.'" class="d-flex flex-column '.@$class.' status_stage text-hover-success" data-toggle="modal" data-target="#status_stage">'.$status.'</a>';

                $recommendation_text = 'Custom';
                if ($recommendation_id > 0 && @$row->to_company_id == auth()->user()->company_id) {
                    $recommendation_text = 'Client';
                    $ulink = '<a href="javascript:void(0);" class="fs-6 '.@$class.' text-hover-success">'.$status.'</a>';
                }

                return '<div class="d-flex flex-column">
                       '.$ulink.'
                      <span class="fw-bold text-muted d-block fs-7">R'.@$row->id.' -'.$recommendation_text.'</span>
                    </div>';
            })
            ->editColumn('Date', function ($row) {

                $total_positions = '';
                $total_positions = number_format($row->total_positions);

                $created_at = '';
                $created_at = $row->created_at;
                $newDate = GeneralMethod::ymdTodmyNotTime($created_at); // date("d M, Y", strtotime($created_at));

                return '<div class="d-flex flex-column">
                        <span class="text-dark fw-bolder fs-6">'.$newDate.'</span>
                        <span class="text-muted fw-bold d-block fs-7">'.$total_positions.' Opening</span>
                    </div>';
            })
            ->editColumn('HM', function ($row) use ($feedback_status) {
                $feedback = ucwords(substr(@$feedback_status[@$row->conversations_status]['name'], 0, 20));
                if (empty($feedback)) {
                    $feedback = '<button type="button" class="btn btn-secondary">Add Note</button>';
                }

                return '<div class="d-flex flex-column show-feedback"  data-row-job_id="'.$row->id.'" data-row-conversations_id="'.$row->conversations_id.'" data-row-recommendation_relation_id="'.$row->recommendation_relation_id.'">
				<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6 ">'.@$feedback.'</a>
				<span class="text-muted fw-bold text-muted d-block fs-7"></span>
				</div>';

            })
            ->editColumn('Terms', function ($row) {
                $html = '<div data-row-id="'.$row->id.'" data-row-recommendation_relation_id="'.$row->recommendation_relation_id.'" class="client-terms-listing"><i class="fa-solid fa-eye" style="font-size: 20px;"></i></div>';

                return $html;
            })
            ->editColumn('company_name_to', function ($row) {
                $newDate = GeneralMethod::ymdTodmyNotTime($row->from_created_at);
                $html = '<div data-row-id="'.$row->recommendation_id.'" data-row-recommendation_relation_id="'.$row->company_name_to.'" class="client-terms-listing"><a href="javascript:void(0);">
                <span class="text-dark fw-bolder fs-6">'.$row->company_name_to.'</span>
                <span class="text-muted fw-bold d-block fs-7">Req Date, '.$newDate.'</span></a>
                </div>';

                return $html;
            })
            ->editColumn('TOT', function ($row) {

                $url_path = $total_application = $inreview_counter = '';
                $url_path = 'javascript:;'; // url('/service_company/apptracker/' . $row->id . '/all');

                $job_id = $row->id;

                $url_path = 'javascript:;'; // url('/service_company/apptracker/clientapp/' . $row->id . '/all');

                $applicationCounter = DB::table('add_job_wise_condidates as job_applications')
                    ->select('job_applications.id')
                    ->where('job_applications.job_id', '=', $row->id)
                    ->where('job_applications.thirdparty_company_id', '=', $row->to_company_id)
                    ->count();
                $inreview_counter = DB::table('job_applications')
                    ->select('job_applications.id')
                    ->where('job_applications.job_id', '=', $row->id)
                    ->where('job_applications.thirdparty_company_id', '=', $row->to_company_id)
                    ->where('job_applications.master_status_id', '=', 8)
                    ->count();

                $applicationCounter = number_format($applicationCounter);
                $inreview_counter = number_format($inreview_counter);

                return '<div class="d-flex flex-column">
                        <a href="'.$url_path.'" class="text-dark fw-bolder fs-6 text-hover-primary">'.@$applicationCounter.'</a>
                        <a href="'.$url_path.'" class="text-muted fw-bold d-block fs-7 text-hover-primary">'.$inreview_counter.'</a>
                    </div>';

            })
            ->editColumn('SOU', function ($row) {
                $job_id = '';
                $master_status_id = 1;
                $cnt = 0;
                $job_id = $row->id;
                $cnt = $row->Sourcing;
                $url_path = $total_application = $inreview_counter = '';
                $url_path = 'javascript:;'; // url('/service_company/apptracker/' . $job_id . '/' . $master_status_id);

                $cnt = DB::table('add_job_wise_condidates as job_applications')
                    ->select('job_applications.id')
                    ->where('job_applications.job_id', '=', $row->id)
                    ->where('job_applications.thirdparty_company_id', '=', $row->to_company_id)
                    ->where('job_applications.master_status_id', '=', 1)
                    ->count();
                if ($cnt == 0) {
                    return '<div class="d-flex flex-column"><a href="'.$url_path.'" class="text-dark fw-bolder fs-6 text-hover-primary">0</a></div>';
                } else {
                    return '<div class="d-flex flex-column"><a href="'.$url_path.'" class="text-dark fw-bolder fs-6 text-hover-primary">'.@$cnt.'</a></div>';
                }
            })
            ->editColumn('SCR', function ($row) {
                $master_status_id = 2;
                $job_id = '';
                $job_id = $row->id;
                $cnt = 0;
                $cnt = $row->Screening;
                $url_path = '';
                $url_path = 'javascript:;'; //  url('/service_company/apptracker/' . $job_id . '/' . $master_status_id);

                $cnt = DB::table('add_job_wise_condidates as job_applications')
                    ->select('job_applications.id')
                    ->where('job_applications.job_id', '=', $row->id)
                    ->where('job_applications.thirdparty_company_id', '=', $row->to_company_id)
                    ->where('job_applications.master_status_id', '=', 2)
                    ->count();
                if ($cnt == 0) {
                    $cnt = '<b>.</b>';

                    return '<div class="d-flex flex-column"><a href="'.$url_path.'" class="text-dark fw-bolder fs-6 text-hover-primary">0</a></div>';
                } else {
                    return '<div class="d-flex flex-column"><a href="'.$url_path.'" class="text-dark fw-bolder fs-6 text-hover-primary">'.@$cnt.'</a></div>';
                }
            })
            ->editColumn('EVA', function ($row) {
                $master_status_id = 3;
                $job_id = '';
                $job_id = $row->id;
                $cnt = 0;
                $cnt = $row->Interview;
                $url_path = '';
                $url_path = 'javascript:;'; // url('/service_company/apptracker/' . $job_id . '/' . $master_status_id);

                $cnt = DB::table('add_job_wise_condidates as job_applications')
                    ->select('job_applications.id')
                    ->where('job_applications.job_id', '=', $row->id)
                    ->where('job_applications.thirdparty_company_id', '=', $row->to_company_id)
                    ->where('job_applications.master_status_id', '=', 3)
                    ->count();
                if ($cnt == 0) {
                    $cnt = '<b>.</b>';

                    return '<div class="d-flex flex-column"><a href="'.$url_path.'" class="text-dark fw-bolder fs-6 text-hover-primary">0</a></div>';
                } else {
                    return '<div class="d-flex flex-column"><a href="'.$url_path.'" class="text-dark fw-bolder fs-6 text-hover-primary">'.@$cnt.'</a></div>';
                }
            })
            ->editColumn('NEO', function ($row) {
                $master_status_id = 4;
                $job_id = '';
                $job_id = $row->id;
                $cnt = 0;
                $cnt = $row->Negotiation;
                $url_path = '';
                $url_path = 'javascript:;'; // url('/service_company/apptracker/' . $job_id . '/' . $master_status_id);

                $cnt = DB::table('add_job_wise_condidates as job_applications')
                    ->select('job_applications.id')
                    ->where('job_applications.job_id', '=', $row->id)
                    ->where('job_applications.thirdparty_company_id', '=', $row->to_company_id)
                    ->where('job_applications.master_status_id', '=', 4)
                    ->count();
                if ($cnt == 0) {
                    $cnt = '<b>.</b>';

                    return '<div class="d-flex flex-column"><a href="'.$url_path.'" class="text-dark fw-bolder fs-6 text-hover-primary">0</a></div>';
                } else {
                    return '<div class="d-flex flex-column"><a href="'.$url_path.'" class="text-dark fw-bolder fs-6 text-hover-primary">'.@$cnt.'</a></div>';
                }
            })
            ->editColumn('OFF', function ($row) {
                $master_status_id = 5;
                $cnt = 0;
                $job_id = '';
                $job_id = $row->id;
                $cnt = $row->Offered;
                $url_path = ''; //
                $url_path = 'javascript:;'; // url('/service_company/apptracker/' . $job_id . '/' . $master_status_id);

                $cnt = DB::table('add_job_wise_condidates as job_applications')
                    ->select('job_applications.id')
                    ->where('job_applications.job_id', '=', $row->id)
                    ->where('job_applications.thirdparty_company_id', '=', $row->to_company_id)
                    ->where('job_applications.master_status_id', '=', 5)
                    ->count();
                if ($cnt == 0) {
                    $cnt = '<b>.</b>';

                    return '<div class="d-flex flex-column"><a href="'.$url_path.'" class="text-dark fw-bolder fs-6 text-hover-primary">0</a></div>';
                } else {
                    return '<div class="d-flex flex-column"><a href="'.$url_path.'" class="text-dark fw-bolder fs-6 text-hover-primary">'.@$cnt.'</a></div>';
                }
            })
            ->editColumn('JOI', function ($row) {

                $master_status_id = 6;
                $job_id = '';
                $job_id = $row->id;
                $cnt = 0;
                $cnt = $row->Joined;
                $url_path = ''; //
                $url_path = 'javascript:;'; // url('/service_company/apptracker/' . $row->id . '/' . $master_status_id);

                $cnt = DB::table('add_job_wise_condidates as job_applications')
                    ->select('job_applications.id')
                    ->where('job_applications.job_id', '=', $row->id)
                    ->where('job_applications.thirdparty_company_id', '=', $row->to_company_id)
                    ->where('job_applications.master_status_id', '=', 6)
                    ->count();
                if ($cnt == 0) {
                    $cnt = '<b>.</b>';

                    return '<div class="d-flex flex-column"><a href="'.$url_path.'" class="text-dark fw-bolder fs-6 text-hover-primary">0</a></div>';
                } else {
                    return '<div class="d-flex flex-column"><a href="'.$url_path.'" class="text-dark fw-bolder fs-6 text-hover-primary">'.@$cnt.'</a></div>';
                }
            })
            ->editColumn('REJ', function ($row) {
                $master_status_id = 7;
                $job_id = '';
                $job_id = $row->id;
                $cnt = 0;
                $cnt = $row->Rejected;
                $url_path = '';
                $url_path = 'javascript:;'; // url('/service_company/apptracker/' . $row->id . '/' . $master_status_id);

                $cnt = DB::table('add_job_wise_condidates as job_applications')
                    ->select('job_applications.id')
                    ->where('job_applications.job_id', '=', $row->id)
                    ->where('job_applications.thirdparty_company_id', '=', $row->to_company_id)
                    ->where('job_applications.master_status_id', '=', 7)
                    ->count();
                if ($cnt == 0) {
                    $cnt = '<b>.</b>';

                    return '<div class="d-flex flex-column"><a href="'.$url_path.'" class="text-dark fw-bolder fs-6 text-hover-primary">0</a></div>';
                } else {
                    return '<div class="d-flex flex-column"><a href="'.$url_path.'" class="text-dark fw-bolder fs-6 text-hover-primary">'.@$cnt.'</a></div>';
                }
            })
            ->editColumn('Action', function ($row) {
                return '<div class="d-flex my-4"> 
				<a href="javascript:void(0);" data-row-status="ignore" data-row-id="'.$row->id.'"  data-row-recommendation_relation_id="'.$row->recommendation_relation_id.'" class="btn btn-sm btn-light me-2 request_approval">
					Ignore
				</a> 
				<a href="javascript:void(0);" data-row-status="accept" data-row-id="'.$row->id.'" data-row-recommendation_relation_id="'.$row->recommendation_relation_id.'" class="btn btn-sm btn-primary request_approval">
					Accept
				</a> 
				</div>';
            })

            ->rawColumns(['chkbox', 'Requisition', 'Exp', 'Salary', 'Location', 'Status', 'Date', 'HM', 'Terms', 'Action', 'company_name_to', 'TOT', 'SOU', 'SCR', 'EVA', 'NEO', 'OFF', 'JOI', 'REJ'])
            ->addIndexColumn()
            ->make(true);
    }

    public function hmlisting($id)
    {
        $recommendation = DB::table('recommendation_relation')->where('id', '=', $id)->first();

        $this->currentJobId = $recommendation->job_id;
        $id = $recommendation->job_id;
        // dd($id,$recommendation->job_id);
        $this->recruiter = [];
        $company_id = $recommendation->to_company_id; // auth()->user()->company_id;
        $this->team = DB::table('users')
            ->leftJoin('job_assign', function ($join) use ($id) {
                $join->on('job_assign.user_id', '=', 'users.id');
                $join->on('job_assign.job_id', '=', DB::raw("'".$id."'"));
            })
            ->leftjoin('role_user', 'role_user.user_id', 'users.id')
            ->leftJoin('users_profile', 'users_profile.user_id', '=', 'users.id')
            ->leftjoin('roles', 'roles.id', 'role_user.role_id')
            ->select(
                'users.id',
                'users.name',
                'users.email',
                'users.mobile',
                'users.working_status',
                'job_assign.id as job_assign_id',
                'job_assign.job_id',
                'roles.display_name',
                'users_profile.designation'
            )
            ->where('users.company_id', $company_id)
            ->where('users.status', '=', 'active')
            ->groupBy('users.id')
            ->orderBy('job_assign.id', 'DESC')
            ->get();
        $view = view('super-admin.jobs.hmlisting', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'view' => $view]);
    }

    public function showfeedback(Request $request, $id)
    {
        //  dd($request->all());
        $this->interviewSchedule = $available_status = $feedback_status = '';
        //  dd($id);

        $this->conversations = DB::table('conversations')
            ->select('conversations.*')
            ->where('conversations.id', @$request->conversations_id)
            ->orderBy('conversations.id', 'DESC')
            ->first();

        $this->conversations_log = DB::table('conversations')
            ->join('conversation_log', 'conversation_log.conversation_id', '=', 'conversations.id')
            ->select('conversation_log.*')
            ->where('conversations.id', @$request->conversations_id)
            ->orderBy('conversation_log.id', 'DESC')
            ->get();

        $this->available_status = GeneralMethod::available_status();
        $this->feedback_status = GeneralMethod::conversation_status();

        $this->conversations_id = $request->conversations_id;
        $this->recommendation_relation_id = $request->recommendation_relation_id;
        $this->job_id = $request->id;

        // dd( $this->data);

        $view = view('super-admin.jobs.showfeedback', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'view' => $view]);
    }

    public function updateInterview(Request $request)
    {

        $interview_id = $app_int_status = $app_int_feedback_status = $feedback_comment = $application_id = $rating = '';

        $interview_id = @$request->id;

        $app_int_feedback_status = @$request->app_int_feedback_status;
        $feedback_comment = @$request->feedback_comment;
        $rating = @$request->rating;
        $conversations_id = $request->conversations_id;
        // dd($conversations_id);
        $recommendation_relation_id = $request->recommendation_relation_id;
        $job_id = @$request->job_id;

        $recommendation_relation = DB::table('recommendation_relation')->select('recommendation_relation.*')->where('id', '=', $recommendation_relation_id)->first();

        // 		dd($request->all());

        $values = [
            'status' => @$app_int_feedback_status,
            'comment' => @$feedback_comment,
            'job_id' => @$job_id,
            'from_company_id' => @$recommendation_relation->from_company_id,
            'to_company_id' => @$recommendation_relation->to_company_id,
            'rating' => @$rating,
            'created_by' => auth()->user()->id,
            'created_at' => date('Y-m-d:H:i:s'),
            'updated_at' => date('Y-m-d:H:i:s'),
        ];
        if ($request->conversations_id == '') {
            $conversationsId = DB::table('conversations')->insertGetId($values);
        } else {
            unset($values['created_at']);
            $conversationsId = DB::table('conversations')->where('id', '=', $conversations_id)->update($values);
            $conversationsId = $conversations_id;
        }
        DB::table('recommendation_relation')->where('id', '=', $recommendation_relation_id)->update(['conversation_id' => @$conversationsId]);

        $values = [
            'conversation_id' => @$conversationsId,
            'status' => @$app_int_feedback_status,
            'comment' => @$feedback_comment,
            'job_id' => @$job_id,
            'from_company_id' => @$recommendation_relation->from_company_id,
            'to_company_id' => @$recommendation_relation->to_company_id,
            'rating' => @$rating,
            'created_by' => auth()->user()->id,
            'created_at' => date('Y-m-d:H:i:s'),
            'updated_at' => date('Y-m-d:H:i:s'),
        ];
        $log_id = DB::table('conversation_log')->insertGetId($values);

        $updateTime = now()->toIso8601String();
        $updateParams = [
            'index' => ELASTIC_JOB_REQUEST,
            'body' => [
                'script' => [
                    'source' => '
                            ctx._source.conversation_id = params.conversation_id;
                            ctx._source.conversations_status = params.conversations_status;
                        ',
                    'lang' => 'painless',
                    'params' => [
                        'conversation_id' => $conversationsId,
                        'conversations_status' => @$app_int_feedback_status,
                    ],
                ],
                'query' => [
                    'bool' => [
                        'must' => [
                            ['term' => ['id' => $recommendation_relation_id]],
                        ],
                    ],
                ],
            ],
            'refresh' => false, // Eventual consistency for 10K concurrent scale
        ];
        // Elastic Execute the bulk update
        if ($this->elasticService->indexExists(ELASTIC_JOB_REQUEST)) {
            $response = $this->elasticService->getClient()->updateByQuery($updateParams);

        }

        return Reply::dataOnly(['status' => 'success', 'logsValue' => $values, 'log_id' => $log_id, 'date_set' => date('d M Y h:i: A'), 'msg' => 'feedback update successfully.']);

    }

    public function notedestroy(Request $request)
    {
        $conve_log = DB::table('conversation_log')
            ->select('conversation_log.conversation_id')
            ->where('conversation_log.id', @$request->noteId)
            ->orderBy('conversation_log.id', 'DESC')
            ->first();
        // dd($conve_log,@$request->noteId);

        $result = DB::table('conversation_log')->where('conversation_log.id', $request->noteId)->delete();
        // dd($result);
        if ($result) {
            //    dd(@$conve_log->conversation_id);
            $conversations = DB::table('conversation_log')
                ->select('conversation_log.*')
                ->where('conversation_log.conversation_id', (int) @$conve_log->conversation_id)
                ->orderBy('conversation_log.id', 'DESC')
                ->first();
            // dd($conversations->conversation_id);
            if ($conversations) {
                $conversationsId = DB::table('conversations')->where('id', '=', $conversations->conversation_id)->update(['status' => (@$conversations->status) ? $conversations->status : 0]);
            }

            return Reply::dataOnly(['status' => 'success']);

        }

    }

    public function checkActive(Request $request)
    {

        $row = DB::table('users')->where('company_id', '=', $request->id)->where('root_user', '=', 1)->first();
        if ($row->status === 'active') {
            $pack_status = 'inactive';
        } else {
            $pack_status = 'active';
        }

        $result = DB::table('users')->where('company_id', '=', $request->id)->where('working_status', '=', 1)->update(['status' => $pack_status]);

        // Phase 3.4: bump company epoch when blocking, so all active sessions are invalidated.
        if ($pack_status === 'inactive') {
            Cache::put('company:epoch:'.(int) $request->id, now()->timestamp, 7 * 24 * 3600);
        }

        $updateTime = now()->toIso8601String();
        $updateParams = [
            'index' => ELASTIC_USERS_LIST,
            'body' => [
                'script' => [
                    'source' => '
                            ctx._source.status = params.status;
                            ctx._source.working_status = params.working_status;
                            ctx._source.updated_at = params.updated_at;
                        ',
                    'lang' => 'painless',
                    'params' => [
                        'status' => $pack_status,
                        'updated_at' => $updateTime,
                    ],
                ],
                'query' => [
                    'bool' => [
                        'must' => [
                            ['term' => ['company_id' => $row->company_id]],
                            ['term' => ['working_status' => 1]],
                        ],
                    ],
                ],
            ],

        ];
        // Elastic Execute the bulk update
        if ($this->elasticService->indexExists(ELASTIC_USERS_LIST)) {
            $response = $this->elasticService->getClient()->updateByQuery($updateParams);
        }

        $updateParams = [
            'index' => ELASTIC_COMPANIES_INDEX,
            'body' => [
                'script' => [
                    'source' => '
                            ctx._source.status = params.status;
                            ctx._source.updated_at = params.updated_at;
                        ',
                    'lang' => 'painless',
                    'params' => [
                        'status' => $pack_status,
                        'updated_at' => $updateTime,
                    ],
                ],
                'query' => [
                    'bool' => [
                        'must' => [
                            ['term' => ['id' => $row->company_id]],
                        ],
                    ],
                ],
            ],

        ];
        // Elastic Execute the bulk update
        if ($this->elasticService->indexExists(ELASTIC_COMPANIES_INDEX)) {
            $response = $this->elasticService->getClient()->updateByQuery($updateParams);
        }

        if ($result) {
            return Reply::dataOnly(['status' => 'success', 'pack_status' => $pack_status]);

        }

        return Reply::dataOnly(['status' => 'faild', 'pack_status' => $pack_status]);

    }

    public function checkStatusOperational(Request $request)
    {
        $row = DB::table('company_coins')->where('company_id', '=', $request->id)->where('event_type', '=', 'operational')->first();
        if ($row->free_pack_status === 'active') {
            $pack_status = 'block';
        } else {
            $pack_status = 'active';
        }

        $result = DB::table('company_coins')->where('company_id', '=', $request->id)->where('event_type', '=', 'operational')->update(['free_pack_status' => $pack_status]);

        if ($result) {
            return Reply::dataOnly(['status' => 'success', 'pack_status' => $pack_status]);

        }

        return Reply::dataOnly(['status' => 'faild', 'pack_status' => $pack_status]);

    }

    public function checkStatusSales(Request $request)
    {

        $row = DB::table('company_coins')->where('company_id', '=', $request->id)->where('event_type', '=', 'sales')->first();
        // dd($row->free_pack_status);
        if ($row->free_pack_status === 'active') {
            $pack_status = 'block';
        } else {
            $pack_status = 'active';
        }

        // dd($pack_status,$request->id);
        $result = DB::table('company_coins')->where('company_id', '=', (int) $request->id)->where('event_type', '=', 'sales')->update(['free_pack_status' => $pack_status]);

        if ($result) {
            return Reply::dataOnly(['status' => 'success', 'pack_status' => $pack_status]);

        }

        return Reply::dataOnly(['status' => 'faild', 'pack_status' => $pack_status]);
    }

    public function jobPublicMarket(Request $request)
    {
        $statusTab = $request->status ?? 'pending';

        // High-level KPI deck counts (covers multi-dimensional + legacy statuses)
        $stats = [
            'total' => DB::table('jobs')->where(function ($q) {
                $q->where('jobs.public_market_status', '>', 0)
                    ->orWhereIn('jobs.market_pub_state', ['published', 'pending_review'])
                    ->orWhere('jobs.bench_submission', 'enabled');
            })->count(),
            'pending' => DB::table('jobs')->where(function ($q) {
                $q->where('jobs.market_mod_state', 'pending')
                    ->orWhere('jobs.public_market_status', 1);
            })->where('jobs.status', 'active')->count(),
            'approved' => DB::table('jobs')->where(function ($q) {
                $q->whereIn('jobs.market_mod_state', ['approved', 'auto_approved'])
                    ->orWhere('jobs.public_market_status', 2);
            })->where('jobs.status', 'active')->count(),
            'rejected' => DB::table('jobs')->where(function ($q) {
                $q->where('jobs.market_mod_state', 'rejected')
                    ->orWhere('jobs.public_market_status', 3);
            })->count(),
            'hold' => DB::table('jobs')->where(function ($q) {
                $q->where('jobs.claim_capacity', 'limit_reached')
                    ->orWhere('jobs.public_market_status', 4);
            })->count(),
            'bench_enabled' => DB::table('jobs')->where('jobs.bench_submission', 'enabled')->where('jobs.status', 'active')->count(),
            'agency_requests' => DB::table('recommendation_relation')->count(),
            'auto_approved' => (bool) DB::table('global_settings')->value('public_market_job_status'),
        ];

        // Base query - Left join clients so jobs without explicit client_id are NEVER dropped!
        $query = DB::table('jobs')
            ->join('companies', 'companies.id', '=', 'jobs.company_id')
            ->leftJoin('clients', 'clients.id', '=', 'jobs.client_id')
            ->leftJoin('recommendation_relation', 'recommendation_relation.job_id', '=', 'jobs.id')
            ->select([
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
                'jobs.market_pub_state',
                'jobs.market_mod_state',
                'jobs.claim_capacity',
                'jobs.bench_submission',
                'jobs.commercial_state',
                'jobs.public_market_status',
                'jobs.job_description',
                'jobs.job_requirement',
                'jobs.company_id as from_company_id',
                'companies.company_name',
                'companies.logo as company_logo',
                'clients.name as clients_name',
                'clients.logo_logical_path',
                DB::raw('COUNT(DISTINCT recommendation_relation.id) as agency_claims_count'),
            ])
            ->where('jobs.id', '>', 0);

        $this->reject = true;
        $this->showAction = true;

        if ($statusTab === 'pending') {
            $this->status = 'Pending Review Jobs';
            $query->where('jobs.status', 'active')
                ->where(function ($q) {
                    $q->where('jobs.market_mod_state', 'pending')
                        ->orWhere('jobs.public_market_status', 1);
                });
        } elseif ($statusTab === 'approve' || $statusTab === 'approved') {
            $this->status = 'Live Approved Market Jobs';
            $query->where('jobs.status', 'active')
                ->where(function ($q) {
                    $q->whereIn('jobs.market_mod_state', ['approved', 'auto_approved'])
                        ->orWhere('jobs.public_market_status', 2);
                });
        } elseif ($statusTab === 'reject' || $statusTab === 'rejected') {
            $this->status = 'Rejected Market Jobs';
            $this->reject = false;
            $query->where(function ($q) {
                $q->where('jobs.market_mod_state', 'rejected')
                    ->orWhere('jobs.public_market_status', 3);
            });
        } elseif ($statusTab === 'hold' || $statusTab === 'limit') {
            $this->status = 'Vendor Capacity Limit Jobs';
            $this->showAction = false;
            $query->where(function ($q) {
                $q->where('jobs.claim_capacity', 'limit_reached')
                    ->orWhere('jobs.public_market_status', 4);
            });
        } elseif ($statusTab === 'bench') {
            $this->status = 'Agency Bench Sharing Jobs';
            $query->where('jobs.status', 'active')
                ->where('jobs.bench_submission', 'enabled');
        } elseif ($statusTab === 'all') {
            $this->status = 'All Marketplace Jobs';
            $query->where(function ($q) {
                $q->where('jobs.public_market_status', '>', 0)
                    ->orWhereIn('jobs.market_pub_state', ['published', 'pending_review'])
                    ->orWhere('jobs.bench_submission', 'enabled');
            });
        } else {
            $this->status = 'Marketplace Jobs';
            $query->where('jobs.status', 'active')
                ->where(function ($q) {
                    $q->where('jobs.market_mod_state', 'pending')
                        ->orWhere('jobs.public_market_status', 1);
                });
        }

        // Search filter
        if ($request->filled('search')) {
            $term = trim($request->search);
            $query->where(function ($q) use ($term) {
                $q->where('jobs.title', 'like', "%{$term}%")
                    ->orWhere('jobs.id', $term)
                    ->orWhere('companies.company_name', 'like', "%{$term}%")
                    ->orWhere('clients.name', 'like', "%{$term}%");
            });
        }

        $query->groupBy('jobs.id');
        $query->orderBy('jobs.id', 'DESC');

        $paginatedJobs = $query->paginate(15)->appends($request->all());

        // Run automated risk scoring evaluation on displayed page rows
        $moderationService = app(MarketplaceModerationService::class);
        foreach ($paginatedJobs as $row) {
            $row->risk_eval = $moderationService->evaluateRisk([
                'title' => $row->title,
                'job_description' => $row->job_description ?? '',
                'job_requirement' => $row->job_requirement ?? '',
                'min_salary' => $row->min_salary ?? 0,
                'max_salary' => $row->max_salary ?? 0,
                'company_id' => $row->from_company_id,
            ]);
        }

        $this->newJobs = $paginatedJobs;
        $this->stats = $stats;
        $this->currentTab = $statusTab;
        $this->globalSetting = DB::table('global_settings')->select('id', 'public_market_job_status')->first();

        return view('super-admin.jobs.index', $this->data);
    }

    public function marketshowJobDetails(Request $request)
    {
        $jobId = $request->id ?? $request->job_id;
        if (! $jobId) {
            return response()->json(['status' => 'error', 'message' => 'Job ID is required.'], 400);
        }

        $job = DB::table('jobs')
            ->join('companies', 'companies.id', '=', 'jobs.company_id')
            ->leftJoin('clients', 'clients.id', '=', 'jobs.client_id')
            ->leftJoin('users', function ($join) {
                $join->on('users.company_id', '=', 'companies.id')
                    ->where('users.root_user', '=', 1);
            })
            ->where('jobs.id', $jobId)
            ->select([
                'jobs.*',
                'companies.company_name',
                'companies.logo as company_logo',
                'companies.company_email',
                'companies.company_phone',
                'clients.name as clients_name',
                'clients.logo_logical_path',
                'users.name as recruiter_name',
                'users.email as recruiter_email',
            ])
            ->first();

        if (! $job) {
            return response()->json(['status' => 'error', 'message' => 'Job record not found.'], 404);
        }

        // Skills
        $skills = DB::table('job_metronics_skills')
            ->leftJoin('skills', 'skills.id', '=', 'job_metronics_skills.skill_id')
            ->where('job_metronics_skills.job_id', $jobId)
            ->pluck('skills.name')
            ->filter()
            ->values()
            ->all();

        // Locations
        $locations = DB::table('client_city_jobs')
            ->leftJoin('city_list', 'city_list.city_id', '=', 'client_city_jobs.city_id')
            ->leftJoin('state_list', 'state_list.id', '=', 'city_list.state_id')
            ->leftJoin('countries', 'countries.id', '=', 'state_list.country_id')
            ->where('client_city_jobs.job_id', $jobId)
            ->select('city_list.city as city_name', 'state_list.state_name', 'countries.country_name')
            ->get();

        // Moderation risk evaluation
        $moderationService = app(MarketplaceModerationService::class);
        $riskEval = $moderationService->evaluateRisk([
            'title' => $job->title,
            'job_description' => $job->job_description ?? '',
            'job_requirement' => $job->job_requirement ?? '',
            'min_salary' => $job->min_salary ?? 0,
            'max_salary' => $job->max_salary ?? 0,
            'company_id' => $job->company_id,
        ]);

        // Audit logs
        $auditLogs = DB::table('job_marketplace_moderation_logs')
            ->leftJoin('users', 'users.id', '=', 'job_marketplace_moderation_logs.actor_id')
            ->where('job_marketplace_moderation_logs.job_id', $jobId)
            ->select([
                'job_marketplace_moderation_logs.*',
                'users.name as actor_name',
            ])
            ->orderBy('job_marketplace_moderation_logs.created_at', 'desc')
            ->get();

        // Agency Claims
        $agencyClaims = DB::table('recommendation_relation')
            ->join('companies as agency', 'agency.id', '=', 'recommendation_relation.to_company_id')
            ->where('recommendation_relation.job_id', $jobId)
            ->select([
                'recommendation_relation.*',
                'agency.company_name as agency_name',
                'agency.logo as agency_logo',
            ])
            ->get();

        $this->job = $job;
        $this->skills = $skills;
        $this->locations = $locations;
        $this->riskEval = $riskEval;
        $this->auditLogs = $auditLogs;
        $this->agencyClaims = $agencyClaims;

        $view = view('super-admin.jobs.showdetail', $this->data)->render();

        return response()->json(['status' => 'success', 'view' => $view]);
    }

    public function marketautoApproval(Request $request)
    {
        $public_market_job_status = DB::table('global_settings')->select('public_market_job_status')->where('id', '=', $request->id)->first();
        $superadminstatus = 1;
        if ($public_market_job_status && $public_market_job_status->public_market_job_status) {
            $superadminstatus = 0;
        }
        DB::table('global_settings')->where('id', $request->id)->update(['public_market_job_status' => $superadminstatus]);

        $statusText = $superadminstatus === 1 ? 'enabled' : 'disabled';
        $message = "Marketplace auto-approval has been {$statusText}.";

        return response()->json(['status' => 'success', 'message' => $message, 'auto_status' => $superadminstatus]);
    }

    public function jobApproval(Request $request)
    {
        $datatype = $request->datatype ?? 'approve';
        $jobIds = is_array($request->job_id) ? $request->job_id : [$request->job_id];
        $jobIds = array_filter(array_map('intval', $jobIds));

        if (empty($jobIds)) {
            return response()->json(['status' => 'error', 'message' => 'No jobs selected for action.'], 400);
        }

        if ($datatype === 'approve' || $datatype === 'approve_all' || $datatype === 'approve_bench') {
            $isBenchOnly = ($datatype === 'approve_bench');

            $updatePayload = [
                'public_market_status' => 2,
                'market_pub_state' => MarketPubState::PUBLISHED->value,
                'market_mod_state' => MarketModState::APPROVED->value,
                'claim_capacity' => $isBenchOnly ? ClaimCapacity::LIMIT_REACHED->value : ClaimCapacity::OPEN->value,
                'bench_submission' => BenchSubmissionState::ENABLED->value,
            ];

            DB::table('jobs')->whereIn('id', $jobIds)->update($updatePayload);

            $basecompany = GeneralMethod::baseCompany();
            $senderName = $basecompany->companyName ?? '';
            $fromAddress = $basecompany->companyEmail ?? '';

            foreach ($jobIds as $jobId) {
                $rowdata = DB::table('jobs')
                    ->join('companies', 'companies.id', '=', 'jobs.company_id')
                    ->leftJoin('users', function ($join) {
                        $join->on('users.company_id', '=', 'companies.id')
                            ->where('users.root_user', '=', 1);
                    })
                    ->select('jobs.*', 'companies.company_name', 'users.name as recipient_name', 'users.email as recipient_email')
                    ->where('jobs.id', $jobId)
                    ->first();

                if (! $rowdata) {
                    continue;
                }

                // Insert into immutable moderation audit logs
                DB::table('job_marketplace_moderation_logs')->insert([
                    'job_id' => $jobId,
                    'company_id' => $rowdata->company_id,
                    'actor_id' => Auth::id() ?? 1,
                    'actor_type' => 'superadmin',
                    'action' => 'manual_approve',
                    'from_mod_state' => 'pending',
                    'to_mod_state' => 'approved',
                    'reason_code' => $isBenchOnly ? 'APPROVED_BENCH_ONLY' : 'APPROVED_OUTSOURCE_AND_BENCH',
                    'rejection_notes' => $isBenchOnly ? 'Approved by Super Admin for Bench Talent Sharing only.' : 'Approved by Super Admin for full Outsource claims and Bench Talent proposals.',
                    'risk_score' => 0.00,
                    'created_at' => now(),
                ]);

                // Multi-Tenant Atomic Cache Invalidation
                Cache::forget("company_jobs_{$rowdata->company_id}");
                Cache::forget("job_details_{$jobId}");
                Cache::forget("marketplace_public_jobs");

                // Legacy mail permission & notification
                $checkMailPermision = GeneralMethod::mailPermission(28);
                $mailTypeSubject = GeneralMethod::mailTypeSubject(28, 1);
                if ($checkMailPermision && $mailTypeSubject) {
                    $subject = str_replace(['[{JOB_TITLE}]', '[{STATUS}]'], ['', ''], $mailTypeSubject->subject);
                    $template_description = $mailTypeSubject->body_description;
                    $template_description = str_replace(
                        ['[{RECIPIENT_NAME}]', '[{JOB_TITLE}]', '[{JOB_ID}]'],
                        [$rowdata->recipient_name ?? 'Recruiter', $rowdata->title, $rowdata->id],
                        $template_description
                    );

                    $notificationemail = GeneralMethod::notification($rowdata->company_id, 'Sales');
                    $defaultEmail = DB::table('companies')->where('id', $rowdata->company_id)->value('company_email');
                    $to_Mail_Id = $notificationemail->email ?? $defaultEmail ?? ($rowdata->recipient_email ?? '');

                    if (! empty($to_Mail_Id)) {
                        $datamail = ['message' => $template_description];
                        $this->mailService->sendEmail($to_Mail_Id, $subject, 'email.common.index', (object) $datamail, [], $fromAddress, $senderName, []);
                    }
                }

                DB::table('market_job_added_count')->where('job_id', '=', $jobId)->where('status', '=', 0)->update(['status' => 1]);
            }

            $message = $isBenchOnly
                ? 'Selected job(s) approved for Bench Talent Sharing only.'
                : 'Selected job(s) approved and published on Agency Marketplace.';
        } else {
            // Reject directly
            $rejectReason = $request->reason ?? $request->notes ?? 'Rejected by Super Admin moderation.';
            DB::table('jobs')->whereIn('id', $jobIds)->update([
                'public_market_status' => 3,
                'market_mod_state' => MarketModState::REJECTED->value,
                'market_pub_state' => MarketPubState::PRIVATE->value,
            ]);

            foreach ($jobIds as $jobId) {
                $compJob = DB::table('jobs')->where('id', $jobId)->first();
                if ($compJob) {
                    DB::table('job_marketplace_moderation_logs')->insert([
                        'job_id' => $jobId,
                        'company_id' => $compJob->company_id,
                        'actor_id' => Auth::id() ?? 1,
                        'actor_type' => 'superadmin',
                        'action' => 'reject',
                        'from_mod_state' => $compJob->market_mod_state ?? 'pending',
                        'to_mod_state' => 'rejected',
                        'reason_code' => $request->reason_code ?? 'MANUAL_REJECTION',
                        'rejection_notes' => $rejectReason,
                        'risk_score' => 0.00,
                        'created_at' => now(),
                    ]);

                    Cache::forget("company_jobs_{$compJob->company_id}");
                    Cache::forget("job_details_{$jobId}");
                    Cache::forget("marketplace_public_jobs");
                }
            }

            $message = 'Selected job(s) rejected from marketplace.';
        }

        return response()->json(['status' => 'success', 'message' => $message]);
    }

    public function job_reject_mail(Request $request)
    {

        // echo $request->id; exit;
        $arr = '';
        // dd($request->arr);
        $this->arr = $request->arr;
        $this->jobIdArr = $request->jobIdArr;
        $this->jobDataCount = DB::table('jobs')->select('*')->whereIn('id', $this->arr)->count();
        // $this->jobData			=	DB::table('jobs')->select('job_jobs.id')->whereIn('id', $arr)->get();

        $this->jobData = DB::table('jobs')
            ->select(
                'jobs.*',
                'companies.company_name',
                'users.name as recipient_name',
                'users.email as recipient_email',
                'jobs.title as job_title'
            )
            ->join('companies', 'companies.id', '=', 'jobs.company_id')
            ->join('users', 'users.company_id', '=', 'companies.id')
            ->whereIn('jobs.id', $this->arr)->groupBy('jobs.id')->get();

        // dd($this->jobData);

        $this->templateData = DB::table('th_email_template')->select('*')->where('th_email_template.status', 1)->where('th_email_template.temptype', 6)->get();

        $this->userdata = $this->user;

        $this->to_email_from_count = $this->cc_email_count = 0;
        $this->to_email_from_data = [];
        $this->cc_email_data = [];
        $commonFromArr = [];
        $commonCcArr = [];
        // dd("j");

        if ($request->id > 1) {

            $client_row = DB::table('jobs')->select('jobs.client_id')->where('jobs.id', $request->id)->first();
            // CLIENT CONTACT INFO
            $ccInfoCount = 0;

            $this->to_email_from_count = $ccInfoCount;
            $this->to_email_from_data = $commonFromArr;

            // HR ASSIGN JOB
            $assignData = [];

            $i = 0;
            foreach ($this->jobData as $aval) {
                // dd($aval,$aval->recipient_email);
                $commonCcArr[$i] = @$aval->recipient_email;
                $i++;
            }

            // $commonCcArr = DB::table('bench_assign')->join('users', 'users.id', '=', 'bench_assign.user_id')->whereIn('bench_assign.bench_applications_id', $this->arr)->pluck('users.email')->toArray();

            // dd($commonCcArr);

            $assignDataclient = [];

            $assignData = array_merge($assignDataclient, $assignData);

            $this->cc_email_count = count($commonCcArr);
            $this->cc_email_data = $commonCcArr;
            $this->cc_email_assignData = (object) $assignData;
        }
        // dd($this->cc_email_count);

        $company_id = auth()->user()->company_id;
        $company_name = DB::table('companies')->select('companies.company_name')->where('id', '=', $company_id)->first();
        $this->company_name = (@$company_name->company_name) ? @$company_name->company_name : '';

        $view = view('super-admin.jobs.job_reject_mail', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'view' => $view]);

    }

    public function reject_mail_send_job(Request $request)
    {
        //   dd($request->all());
        if (count($request->job_id) > 0) {
            DB::table('jobs')->whereIn('id', $request->job_id)->update(['public_market_status' => 3]);

            $basecompany = GeneralMethod::baseCompany();
            $senderName = ($basecompany->companyName) ? $basecompany->companyName : '';
            $fromAddress = ($basecompany->companyEmail) ? $basecompany->companyEmail : '';
            $attachments = [];
            $benchstatus = 'Live on Platform';
            $cc = [];
            for ($i = 0; $i < count($request->job_id); $i++) {
                $rowdata = DB::table('jobs')
                    ->join('companies', 'companies.id', '=', 'jobs.company_id')
                    ->join('users', 'users.company_id', '=', 'companies.id')
                    ->where('users.root_user', '=', 1)
                    ->select('jobs.*', 'companies.company_name', 'users.name as recipient_name', 'users.email as recipient_email ')->where('jobs.id', $request->job_id[$i])->first();
                // dd($rowdata);
                $checkMailPermision = GeneralMethod::mailPermission(29);
                $mailTypeSubject = GeneralMethod::mailTypeSubject(29, 1);
                if ($checkMailPermision) {
                    $subject = str_replace(['[{JOB_TITLE}]', '[{STATUS}]'], ['', ''], $mailTypeSubject->subject);
                    $template_description = $mailTypeSubject->body_description;
                    $template_description = str_replace(['[{RECIPIENT_NAME}]', '[{JOB_TITLE}]', '[{JOB_ID}]', '[{COMMENTS}]'], [$rowdata->recipient_name, $rowdata->title, $rowdata->id, @$request->editor1], $template_description);

                    $message = $template_description;

                    $notificationemail = GeneralMethod::notification($rowdata->company_id, 'Sales'); // Recuitment Sales
                    $defailsEmail = DB::table('companies')->where('id', $rowdata->company_id)->first()->company_email;
                    $to_Mail_Id = $notificationemail->email ?? $defailsEmail ?? '';
                    $datamail = [
                        'message' => $message,
                    ];
                    $result = $this->mailService->sendEmail($to_Mail_Id, $subject, 'email.common.index', (object) $datamail, $attachments, $fromAddress, $senderName, $cc);
                    // dd($to_Mail_Id,$result);

                }

            }

        }

        $message = 'Selected Jobs Approved.';

        // dd("jsj");
        $data = ['status' => 'success', 'message' => $message];

        return response()->json($data);

    }

    public function offerAmountChange()
    {

        // $company_id      			=	auth()->user()->company_id;

        $offer_points = DB::table('offer_points')->select('offer_points.*')->where('offer_points.section_divided', '=', 0)->where('offer_points.section_type', '=', 0)->orderByDesc('sorting')->get();

        $offer_point_other = DB::table('offer_points')->select('offer_points.*')->where('offer_points.section_divided', '=', 0)->where('offer_points.section_type', '=', 1)->orderByDesc('sorting')->get();

        $offer_point_hourly = DB::table('offer_points')->select('offer_points.*')->where('offer_points.section_divided', '=', 1)->where('offer_points.section_type', '=', 0)->orderByDesc('sorting')->get();

        $offer_point_other_hourly = DB::table('offer_points')->select('offer_points.*')->where('offer_points.section_divided', '=', 1)->where('offer_points.section_type', '=', 1)->orderByDesc('sorting')->get();

        // dd( $offer_point_hourly, $offer_point_other_hourly);

        $this->offer_points = $offer_points;
        $this->offer_point_other = $offer_point_other;

        $this->offer_point_hourly = $offer_point_hourly;
        $this->offer_point_other_hourly = $offer_point_other_hourly;

        $this->offer_id = 0;
        $this->section_other = '';
        $this->section_hourly = 'd-none';

        return view('super-admin.account.offerAmountChange', $this->data);

    }

    public function companysavecreateoffer(Request $request)
    {
        $company_id = auth()->user()->company_id;
        // dd($request->all());
        if ($request->current_ctc_mode == 2 || $request->current_ctc_mode == 1 || $request->current_ctc_mode == 3) {
            $validator = Validator::make($request->all(), [
                'offer_point_hourly.*' => 'sometimes|required',
                'option_percentage_hourly.*' => 'sometimes|required',
                'bonus_point_hourly.*' => 'sometimes|required',
                'bonus_option_percentage_hourly.*' => 'sometimes|required',
            ], [
                'offer_point_hourly.*.required' => 'Offer point is required.',
                'option_percentage_hourly.*.required' => 'Option percentage is required.',

                'bonus_point_hourly.*.required' => 'Bonus point is required.',
                'bonus_option_percentage_hourly.*.required' => 'Bonus option percentage is required.',
            ]);
        } else {

            $validator = Validator::make($request->all(), [
                'offer_point.*' => 'sometimes|required',
                'option_percentage.*' => 'sometimes|required',
                'bonus_points.*' => 'sometimes|required',
                'bonus_option_percentage.*' => 'sometimes|required',
            ], [
                'offer_point.*.required' => 'Offer point is required.',
                'option_percentage.*.required' => 'Option percentage is required.',

                'bonus_points.*.required' => 'Other point is required.',
                'bonus_option_percentage.*.required' => 'Other option percentage is required.',
            ]);
        }

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        if ($request->current_ctc_mode == 2 || $request->current_ctc_mode == 1 || $request->current_ctc_mode == 3) {
            $totalSum = array_sum($request->option_percentage_hourly);
        } else {
            $totalSum = array_sum($request->option_percentage);
        }

        if ($totalSum != 100) {
            return response()->json(['status' => 'errors', 'message' => 'Please check Percentage and complete 100%']);
        }
        // dd($totalSum,$request->current_ctc_mode,$request->option_percentage);

        $offer_id = 0;

        try {
            // Start the transaction
            DB::beginTransaction();

            if ($request->current_ctc_mode == 2 || $request->current_ctc_mode == 1 || $request->current_ctc_mode == 3) {

                // Prepare the main offer data
                DB::table('offer_points')->where('section_divided', 1)->delete();

                // Check if offer points exist and insert them
                if (! empty($request->offer_point_hourly)) {
                    foreach ($request->offer_point_hourly as $bp => $point) {
                        $basic_offer_points = [
                            'points' => $point,
                            'percentage' => $request->option_percentage_hourly[$bp],
                            'condition_check' => $request->option_salarymode_hourly[$bp],
                            'section_type' => 0,
                            'section_divided' => 1,
                        ];
                        DB::table('offer_points')->insert($basic_offer_points);
                    }
                }
                if (! empty($request->bonus_point_hourly)) {
                    foreach ($request->bonus_point_hourly as $bp => $point) {
                        $bonus_offer_points = [
                            'points' => $point,
                            'percentage' => $request->bonus_option_percentage_hourly[$bp],
                            'condition_check' => $request->bonus_option_salarymode_hourly[$bp],
                            'section_type' => 1,
                            'section_divided' => 1,
                        ];
                        DB::table('offer_points')->insert($bonus_offer_points);
                    }
                }

            } else {
                // dd("dsl");
                // Prepare the main offer data
                DB::table('offer_points')->where('section_divided', 0)->delete();
                // Check if offer points exist and insert them
                if (! empty($request->offer_point)) {
                    foreach ($request->offer_point as $bp => $point) {
                        $basic_offer_points = [
                            'points' => $point,
                            'percentage' => $request->option_percentage[$bp],
                            'condition_check' => $request->option_salarymode[$bp],
                            'view_type' => $request->basic_view[$bp],
                            'section_type' => 0,
                            'section_divided' => 0,
                        ];
                        DB::table('offer_points')->insert($basic_offer_points);
                    }
                }

                if (! empty($request->bonus_points)) {
                    foreach ($request->bonus_points as $bp => $point) {
                        $bonus_offer_points = [
                            'points' => $point,
                            'percentage' => $request->bonus_option_percentage[$bp],
                            'condition_check' => $request->bonus_option_salarymode[$bp],
                            'view_type' => $request->bonus_view[$bp],
                            'section_type' => 1,
                            'section_divided' => 0,
                        ];
                        DB::table('offer_points')->insert($bonus_offer_points);
                    }
                }

            }

            // Commit the transaction if all operations are successful
            DB::commit();

            // Return a success response
            return response()->json(['status' => 'success', 'message' => 'Offer Created successfully.']);

        } catch (\Exception $e) {
            // Rollback the transaction if any operation fails
            DB::rollBack();

            // Log the exception message (optional)
            \Log::error('Transaction failed: '.$e->getMessage());
            // dd($e->getMessage());

            // Return an error response
            return response()->json(['status' => 'failed', 'message' => 'Failed to save data. Please try again.'], 500);
        }

    }

    public function ticketList(Request $request)
    {
        $search = $request->input('search');
        $this->pageTitle = __('Applied Candidates');
        $this->pageIcon = 'icon-user';
        $this->tickets = Ticket::with(['ticketstatus', 'company' => function ($query) {
            $query->select('id', 'company_name');
        },
        ])
            ->when($search, function ($query, $search) {
                return $query->where('title', 'LIKE', "%{$search}%")
                    ->orWhere('ticket_no', 'LIKE', "%{$search}%");
            })
            ->orderBy('status_id', 'ASC') // Order by status_id
            ->paginate(21);

        // dd($this->tickets);

        return view('super-admin.supports.index', $this->data);
    }

    public function ticketCreate()
    {

        $number = Ticket::latest('id')->value('id');
        $this->number_token = ($number > 0) ? $number + 1 : 1;
        $this->formattedNumber = str_pad($this->number_token, 5, '0', STR_PAD_LEFT);

        $this->pageTitle = __('Applied Candidates');
        $this->pageIcon = 'icon-user';

        return view('super-admin.supports.create', $this->data);
    }

    public function storeticketCreate(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'ticketId' => 'required|string',
            'ticketIdCheck' => 'required|string',
        ]);

        // $number_token = decrypt($validated['ticketId']);

        $number = Ticket::latest('id')->value('id');
        $this->number_token = ($number > 0) ? $number + 1 : 1;
        $formattedNumber = str_pad($this->number_token, 5, '0', STR_PAD_LEFT);

        // dd($number);
        // $formattedNumber = sprintf("%05d", $number_token);
        // $formattedNumber = str_pad($number_token, 5, '0', STR_PAD_LEFT);

        if ($formattedNumber === $validated['ticketIdCheck']) {
            $ticket = Ticket::create([
                'user_id' => auth()->user()->id,
                'title' => $validated['title'],
                'description' => $validated['description'],
                'ticket_no' => $formattedNumber,
                'company_id' => auth()->user()->company_id,
                'status_id' => 1, // Default status as 'Pending' or 'Open'
            ]);

            return redirect()->route('superadmin.tickets.ticketList')->with('success', 'Ticket created successfully.');
        }
        // $number
        // dd($formattedNumber);

        return redirect()->back()
            ->withInput() // retains the input data
            ->with('error', 'Ticket creation failed. Please try again.');

    }

    public function ticketshow($ticket)
    {

        $id = decrypt($ticket);
        $this->ticket = Ticket::where('id', $id)
            ->with(['comments.user', 'ticketstatus', 'user'])
            ->firstOrFail();

        // dd($ticket);

        // dd($ticket->all());
        $this->pageTitle = __('Applied Candidates');
        $this->pageIcon = 'icon-user';

        $this->ticket_status = TicketStatus::all();

        // $this->ticket = $ticket->load(['comments.user', 'status', 'user']);

        // dd($ticket);
        return view('super-admin.supports.show', $this->data);
    }

    public function updateStatus(Request $request, Ticket $ticket)
    {
        $ticket->update(['status_id' => $request->status_id]);

        return redirect()->route('superadmin.tickets.ticketList')->with('success', 'Ticket status updated.');
    }

    public function storetComment(Request $request)
    {
        // dd($request->all());
        $validated = $request->validate([
            'ticket_id' => 'required|exists:tickets,id',
            'content' => 'required|string|max:1000',
        ]);

        $id = $validated['ticket_id'];

        Comment::create([
            'ticket_id' => $validated['ticket_id'],
            'user_id' => auth()->user()->id,
            'content' => $validated['content'],
        ]);

        Ticket::where('id', $id)->update(['status_id' => $request->status_is]);

        return redirect()->route('superadmin.tickets.show', encrypt($validated['ticket_id']))->with('success', 'Comment added successfully.');
    }

    public function boostkeywordadd(Request $request)
    {
        $boostkeywords = DB::table('boostkeywords')->where('company_id',$request->keyword_compant_id)->delete();
        $tags = request('tags');
        $tags_key = json_decode($tags, true);
        if (\request('tags') != '') {
            //  dd($tags_key);
            foreach ($tags_key as $tagSkill) {
                $keyWord = $tagSkill['value'];
                $insertArr = ['company_id' => $request->keyword_compant_id, 'skill_name' => $keyWord, 'status' => 1];
                DB::table('boostkeywords')->insert($insertArr);
            }

        }

        return response()->json([
            'message' => '',
            'status' => '',
            'paymentLink' => '',
        ]);

    }
}
