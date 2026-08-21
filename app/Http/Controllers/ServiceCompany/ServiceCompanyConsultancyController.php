<?php

/*** ########################################################
Controller    	: 	AdminConsultancyController
Purmose			:
Author			:	Abu
Company			:	4Bell
Date			:	05-April-2021
########################################################***/

namespace App\Http\Controllers\ServiceCompany;

use App\Helper\GeneralMethod;
use App\Helper\Reply;
use App\Jobs\QueueNotificationMailService;
use App\Models\Client;
use App\Models\Company;
use App\Models\Country;
use App\Models\JobCategory;
use App\Models\User;
use App\Services\AgencyListingService;
use App\Services\ElasticService;
use App\Services\MailService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class ServiceCompanyConsultancyController extends ServiceCompanyBaseController
{
    protected $mailService;

    protected $elasticService;

    protected $agencyListingService;

    public function __construct(MailService $mailService, ElasticService $elasticService, AgencyListingService $agencyListingService)
    {
        parent::__construct();
        $this->pageTitle = __('Staffing Partners');
        $this->pageIcon = 'icon-grid';
        $this->mailService = $mailService;
        $this->elasticService = $elasticService;
        $this->agencyListingService = $agencyListingService;
        view()->share('calling_codes', $this->getCallingCodes());
    }

    public function index(Request $request)
    {
        $company_id = auth()->user()->company_id;
        $this->spartnerDataCnt = $this->agencyListingService->getSummaryCounters($company_id);
        $this->countries = DB::table('countries')->get();

        return view('service_company.consultancy.index', $this->data);
    }

    public function data_ajax(Request $request)
    {
        $company_id = '';
        $company_id = auth()->user()->company_id;
        $searchTerms = request('cus_search');
        $custom_search = $searchTerms;
        $searchQuery = $request->input('q');
        $page = max((int) $request->input('page', 1), 1);
        $size = (int) ($request->input('per_page') ?? $request->input('size') ?? $request->input('limit') ?? 10);
        $from = ($page - 1) * $size;
        $sort = [];
        $customSearch = trim($custom_search);
        $shouldSearch = [];
        $sort = [];
        $must = [];
        $valCheck = request('sortingfilter');

        $cachePayload = [
            'page' => $page,
            'size' => $size,
            'q' => $searchQuery,
            'country' => request('country'),
            'company' => $company_id,
            'custom_search' => $custom_search,
            'user_id' => $this->user->id,
            'valCheck' => $valCheck,
        ];

        $versionKey = 'allconsultancy_version_user_'.$this->user->id;
        $version = cache()->get($versionKey, 1);
        $cacheKeySet = 'ats:allconsultancy:'.$this->user->id.':'.$version.':'.md5(json_encode($cachePayload));
        /* ----------------------------
        ⚡ Check DragonflyDB first
        ----------------------------- */
        if (cache()->has($cacheKeySet)) {
            return response()->json(cache()->get($cacheKeySet));
        }        try {
            $relationIds = $this->elasticService->getClient()->search([
                'index' => ELASTIC_CLIENT_AND_AGENCY_RELATION_INDEX,
                'body' => [
                    'size' => 10000,
                    'query' => [
                        'bool' => [
                            'must' => [
                                ['term' => ['from_company_id' => $company_id]],
                                ['term' => ['final_status' => 1]],
                            ],
                        ],
                    ],
                    '_source' => ['to_company_id'],
                ],
            ]);

            // Extract connected agency company IDs
            $companyIds = collect($relationIds['hits']['hits'] ?? [])
                ->pluck('_source.to_company_id')
                ->unique()
                ->values()
                ->toArray();
        } catch (\Exception $e) {
            $companyIds = [];
        }

        // Early return if tenant has no connected agencies
        if (empty($companyIds)) {
            $responseData = [
                'html' => view('service_company.ajax_pages.staffing-partner-ajax', [
                    'data' => [],
                    'userPermissions' => $this->userPermissions,
                    'company_id' => $company_id,
                    'batchAgencyStats' => [],
                    'batchLocations' => [],
                    'batchIndustries' => [],
                    'batchRelations' => [],
                ])->render(),
                'total' => 0,
                'pagination' => view('service_company.ajax_pages.pagination', [
                    'pagination' => ['total' => 0, 'current_page' => $page, 'last_page' => 0, 'listType' => 'allcandidate'],
                ])->render(),
            ];

            return response()->json($responseData);
        }

        if (! empty($valCheck)) {
            if ($valCheck == 1) {
                $sort[] = ['created_at' => ['order' => 'desc']];
            } elseif ($valCheck == 2) {
                $sort[] = ['created_at' => ['order' => 'asc']];
            } else {
                $sort[] = ['id' => ['order' => 'desc']];
            }
        } else {
            $sort[] = ['id' => ['order' => 'desc']];
        }

        // Cleaned up ES filter conditions
        $must[] = ['term' => ['self_company' => 1]];
        $must[] = ['terms' => ['company_id' => $companyIds]];

        if (! empty($customSearch)) {
            $customSearchLower = strtolower($customSearch);
            $shouldSearch = [
                ['match_phrase_prefix' => ['name' => $customSearch]],
                ['match_phrase_prefix' => ['td_industries.ind_name' => $customSearch]],
                ['match_phrase_prefix' => ['client_contact_info.contact_person_name' => $customSearch]],
                [
                    'nested' => [
                        'path' => 'client_location',
                        'query' => [
                            'bool' => [
                                'should' => [
                                    ['wildcard' => ['client_location.city_name' => $customSearchLower.'*']],
                                    ['wildcard' => ['client_location.state_name' => $customSearchLower.'*']],
                                    ['wildcard' => ['client_location.country_name' => $customSearchLower.'*']],
                                ],
                            ],
                        ],
                    ],
                ],
            ];

            $must[] = [
                'bool' => [
                    'should' => $shouldSearch,
                    'minimum_should_match' => 1,
                ],
            ];
        }

        $params = [
            'index' => ELASTIC_CLIENT_LIST,
            'body' => [
                'from' => $from,
                'size' => $size,
                'sort' => $sort,
                'query' => [
                    'bool' => [
                        'must' => $must,
                    ],
                ],
            ],
        ];

        if (! $this->elasticService->indexExists(ELASTIC_CLIENT_LIST)) {
            $total = 0;
            $data = [];
            $batchAgencyStats = [];
            $batchLocations = [];
            $batchIndustries = [];
            $batchRelations = [];
        } else {
            try {
                $response = $this->elasticService->getClient()->search($params);
                $total = $response['hits']['total']['value'] ?? 0;
                $data = array_map(function ($hit) {
                    return $hit['_source'];
                }, $response['hits']['hits'] ?? []);

                $agencyCompanyIds = array_filter(array_column($data, 'company_id'));
                $clientIds = array_filter(array_column($data, 'client_id'));
                $industryIds = array_filter(array_column($data, 'industries'));

                $batchAgencyStats = $this->agencyListingService->getBatchAgencyStats($agencyCompanyIds, $company_id);
                $batchLocations = $this->agencyListingService->getBatchLocations($clientIds);
                $batchIndustries = $this->agencyListingService->getBatchIndustries($industryIds);
                $batchRelations = $this->agencyListingService->getBatchRelationData($agencyCompanyIds, $company_id);
            } catch (\Exception $e) {
                $total = 0;
                $data = [];
                $batchAgencyStats = [];
                $batchLocations = [];
                $batchIndustries = [];
                $batchRelations = [];
            }
        }

        $pagination = [
            'total' => $total,
            'current_page' => $page,
            'per_page' => $size,
            'last_page' => ceil($total / $size),
            'listType' => 'allcandidate',
        ];

        $userPermissions = $this->userPermissions;
        $responseData = [
            'html' => view('service_company.ajax_pages.staffing-partner-ajax', compact('data', 'userPermissions', 'company_id', 'batchAgencyStats', 'batchLocations', 'batchIndustries', 'batchRelations'))->render(),
            'total' => $total,
            'pagination' => view('service_company.ajax_pages.pagination', compact('pagination'))->render(),
        ];

        cache()->put($cacheKeySet, $responseData, now()->addMinutes(ALL_CONSULTANCY_TIME));

        return response()->json($responseData);
    }

    public function staffing_invitation(Request $request, $id)
    {

        $staffing_partners_email = $userId = $message = '';

        $company_id = auth()->user()->company_id;
        $staffing_partners_email = $_REQUEST['staffing_partners_email'];
        $userId = $this->user->id;
        $validator = Validator::make($request->all(), [
            'staffing_partners_email' => [
                'required',
                'email:rfc,dns',
                'disposable_email',
            ],
        ], [
            'staffing_partners_email.required' => 'Email is required',
            'staffing_partners_email.email' => 'Please enter a valid email',
            'staffing_partners_email.disposable_email' => 'Temporary emails are not allowed',
        ]);

        if ($validator->fails()) {
            return Reply::dataOnly(['status' => 'warning', 'message' => $validator->errors()->first()]);
        }

        $clientRow = DB::table('clients')->select('*')->where('company_id', $company_id)->where('self_company', 1)->first();

        $exists = DB::table('users')->select('*')->where('root_user', '=', 0)->where('email', $staffing_partners_email)->first();
        if ($exists) {
            $message = 'Use the company’s main email ID to add them as a Staffing Partner, not an employee’s email ID.';

            return Reply::dataOnly(['status' => 'warning', 'message' => $message]);
        }

        if (empty($staffing_partners_email) || $staffing_partners_email == '') {
            $message = 'Email can not be empty, please enter email';

            return Reply::dataOnly(['status' => 'warning', 'message' => $message]);
        }

        // DELETE ALL OLD INVITATION

        DB::table('staffing_company_invitation')
            ->where('staffing_company_invitation.from_company_id', $company_id)
            ->where('staffing_company_invitation.to_email', $staffing_partners_email)
            ->where('staffing_company_invitation.to_invitation_status', '!=', 1)
            ->delete();

        // CASE 1. NOT REGISTER 						=> SEND REGISTRATION REQUEST
        // CASE 2. DO NOT SEND INTERNAL TEAM MEMBER
        // CASE 3. ALREADY REGISTER 					=> SEND STAFFING PARTNER REQUEST
        // CASE 4.

        $userRowCount = User::where('email', $staffing_partners_email)->count();
        $userRow = User::where('email', $staffing_partners_email)->first();

        if ($userRowCount > 0) {

            if ($userRow->company_id == $company_id) {											// CASE 2. DO NOT SEND INTERNAL TEAM MEMBER
                $message = 'Can not send internal team member';

                return Reply::dataOnly(['status' => 'warning', 'message' => $message]);
            } else {
                // CHECK ALREADY LINKED
                $staffingRowCount = DB::table('staffing_company_relation')
                    ->where('from_company_id', $company_id)
                    ->where('to_company_id', $userRow->company_id)
                    ->where('final_status', 1)
                    ->count();
                if ($staffingRowCount > 0) {														// CASE 3. ALREADY REGISTER 	=> SEND STAFFING PARTNER REQUEST

                    $message = 'Agency already added';

                    return Reply::dataOnly(['status' => 'warning', 'message' => $message]);

                } else {

                    $from_company_id = $company_id;
                    $to_company_id = $userRow->company_id;

                    // CHECK CLIENT INVITATION SENT OR NOT
                    $clientInvitationCounter = DB::table('staffing_company_invitation')->select('*')
                        ->where('from_company_id', $from_company_id)
                        ->where('to_company_id', $to_company_id)
                        ->where('to_invitation_status', '!=', -1)
                        ->count();

                    if ($clientInvitationCounter == 0) {		// INVITATION SENT :- IN CASE NO INVITATION AVAIL

                        // INVITATION
                        $staffing_company_invitation = [
                            'from_client_id' => $clientRow->id,
                            'from_company_id' => $company_id,
                            'from_user_id' => $userId,
                            'from_status' => 1,
                            'from_created_at' => date('Y-m-d'),
                            'to_company_id' => $userRow->company_id,
                            'to_email' => $staffing_partners_email,
                            'to_status' => 0,
                            // 'job_id'			=>
                        ];
                        DB::table('staffing_company_invitation')->insert($staffing_company_invitation);
                        $invitationId = DB::getPdo()->lastInsertId();
                        $this->agencyListingService->clearCompanyStatsCache($company_id);
                        $this->agencyListingService->clearUserConsultancyCache($userId);
                        $Companies = Company::find(auth()->user()->company_id);

                        GeneralMethod::clientRequestIndexElastic($invitationId);

                        $encode = base64_encode($invitationId);
                        $reqdate = date('d M Y');
                        $Url = url('/invitation-company-register/'.$encode);
                        $to = $staffing_partners_email;
                        $cc = 'abu@4bell.com';
                        $subject = "Invitation to Become a Staffing Partner | $Companies->company_name | $reqdate ";
                        $message = '<html><head><title>HTML email</title></head><body><p>Hi</p><table><tr><th><a href="'.$Url.'" target="_blank">Click here </a></th></tr><tr><th>Or Copy link</th></tr><tr><th>'.$Url.'</th></tr></table></body></html>';

                        $attachments = [];
                        $cc = [];
                        $basecompany = GeneralMethod::baseCompany();
                        $senderName = ($basecompany->companyName) ? $basecompany->companyName : '';
                        $fromAddress = ($basecompany->companyEmail) ? $basecompany->companyEmail : '';
                        $checkMailPermision = GeneralMethod::mailPermission(40);
                        if ($checkMailPermision) {
                            $mailTypeSubject = GeneralMethod::mailTypeSubject(40, 1);
                            $subject = str_replace(['[COMPANY_NAME]', '[REQUEST_DATE]'], [$Companies->company_name, $reqdate], $mailTypeSubject->subject);
                            $template_description = $mailTypeSubject->body_description;

                            $AcceptInvite = '<a href="'.$Url.'" rel="noopener"
																					style="text-decoration:none;display:inline-block;text-align:center;padding:0.75575rem 1.3rem;font-size:0.925rem;line-height:1.5;border-radius:0.35rem;color:#ffffff;background-color:#009EF7;border:0px;margin-right:0.75rem!important;font-weight:600!important;outline:none!important;vertical-align:middle">Accept
																					Invite
																				</a>';
                            $urlLink = '<a href="'.$Url.'" rel="noopener" style="text-decoration:none;color: #009EF7">'.$Url.'</a>';

                            $template_description = str_replace(['[COMPANY_NAME]', '[ACCEPT_INVITE]', '[URL_LINK]'], [$Companies->company_name, $AcceptInvite, $urlLink], $template_description);

                            $message = $template_description;
                            $data = [
                                'message' => $message,
                            ];

                            QueueNotificationMailService::dispatch(
                                $staffing_partners_email,
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

                    }
                    $message = 'An invitation has been sent to'.$staffing_partners_email.'<br>
						Please ask the agency to check their email and accept the invitation.<br>
						Once accepted, they can proceed with either of the following:<br>
						<ul>
						<li> Register on Jero.works, or </li>
						<li>If already registered, accept the request from the <b>Client Request tab</b> </li>
						</ul><br>
						After completing these steps, they will be able to start collaborating with your organization.';
                    $spartnerDataCnt = $this->agencyListingService->getSummaryCounters($company_id);

                    return Reply::dataOnly(['status' => 'success', 'message' => $message, 'spartnerDataCnt' => $spartnerDataCnt]);
                }
            }

        } else { //  CASE 1. NOT REGISTER => SEND REGISTRATION REQUEST

            $staffing_company_invitation = [
                'from_client_id' => $clientRow->id,
                'from_company_id' => $company_id,
                'from_user_id' => $userId,
                'from_status' => 1,
                'from_created_at' => date('Y-m-d'),
                // 'to_company_id'		=>,
                'to_email' => $staffing_partners_email,
                'to_status' => 0,
                // 'job_id'			=>
            ];
            $Companies = Company::find(auth()->user()->company_id);

            DB::table('staffing_company_invitation')->insert($staffing_company_invitation);
            $invitationId = DB::getPdo()->lastInsertId();
            $this->agencyListingService->clearCompanyStatsCache($company_id);
            $this->agencyListingService->clearUserConsultancyCache($userId);

            GeneralMethod::clientRequestIndexElastic($invitationId);

            $encode = base64_encode($invitationId);
            $reqdate = date('d M Y');
            $Url = url('/invitation-company-register/'.$encode);
            $to = $staffing_partners_email;
            $cc = 'abu@4bell.com';
            $subject = "Invitation to Become a Staffing Partner | $Companies->company_name | $reqdate ";
            $message = '<html><head><title>HTML email</title></head><body><p>Hi</p><table><tr><th><a href="'.$Url.'" target="_blank">Click here </a></th></tr><tr><th>Or Copy link</th></tr><tr><th>'.$Url.'</th></tr></table></body></html>';
            $from = 'vibhanshu@4bell.com';

            // dd($Companies);
            $data = [
                'to' => $staffing_partners_email,
                'url' => $Url,
                'company_name' => $Companies->company_name,
            ];
            $cc_team = [$staffing_partners_email];
            $attachments = [];
            $cc = [];
            $basecompany = GeneralMethod::baseCompany();
            $senderName = ($basecompany->companyName) ? $basecompany->companyName : '';
            $fromAddress = ($basecompany->companyEmail) ? $basecompany->companyEmail : '';
            $checkMailPermision = GeneralMethod::mailPermission(40);
            if ($checkMailPermision) {
                $mailTypeSubject = GeneralMethod::mailTypeSubject(40, 1);
                $subject = str_replace(['[COMPANY_NAME]', '[REQUEST_DATE]'], [$Companies->company_name, $reqdate], $mailTypeSubject->subject);
                $template_description = $mailTypeSubject->body_description;

                $AcceptInvite = '<a href="'.$Url.'" rel="noopener"
															style="text-decoration:none;display:inline-block;text-align:center;padding:0.75575rem 1.3rem;font-size:0.925rem;line-height:1.5;border-radius:0.35rem;color:#ffffff;background-color:#009EF7;border:0px;margin-right:0.75rem!important;font-weight:600!important;outline:none!important;vertical-align:middle">Accept
															Invite
														</a>';
                $urlLink = '<a href="'.$Url.'" rel="noopener" style="text-decoration:none;color: #009EF7">'.$Url.'</a>';

                $template_description = str_replace(['[COMPANY_NAME]', '[ACCEPT_INVITE]', '[URL_LINK]'], [$Companies->company_name, $AcceptInvite, $urlLink], $template_description);

                $message = $template_description;
                $data = [
                    'message' => $message,
                ];

                QueueNotificationMailService::dispatch(
                    $staffing_partners_email,
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

            // $result = $this->mailService->sendEmail($staffing_partners_email, $subject,'email.staffing-partners.accept-invite',(object) $data, $attachments, $fromAddress, $senderName,$cc);

            // Mail::to($staffing_partners_email)->send(new AcceptInviteMail((object) $data));

            // $sendEmail			=	GeneralMethod::sentEmailUsingSMTP($to, $cc, $subject, $message, $from);

            $message = 'Invitation request has been sent on email !';

            $spartnerDataCnt = $this->agencyListingService->getSummaryCounters($company_id);

            return Reply::dataOnly(['status' => 'success', 'message' => $message, 'spartnerDataCnt' => $spartnerDataCnt]);

        }

    }

    public function requestPartners($id)
    {
        $this->pageTitle = __('Request Partners');
        $company_id = auth()->user()->company_id;
        $this->spartnerDataCnt = $this->agencyListingService->getSummaryCounters($company_id);

        return view('service_company.consultancy.requestPartners', $this->data);
    }

    public function uncleared_request_data()
    {
        $company_id = '';
        $company_id = auth()->user()->company_id;
        $unclearedDataList = DB::table('staffing_company_invitation')
            ->select('staffing_company_invitation.to_email', 'staffing_company_invitation.to_company_id', 'staffing_company_invitation.to_company_id', 'staffing_company_invitation.to_invitation_status', 'companies.company_name', 'companies.industries', 'td_industries.ind_name')
            ->leftJoin('companies', 'companies.id', '=', 'staffing_company_invitation.to_company_id')
            ->leftJoin('td_industries', 'td_industries.ind_id', '=', 'companies.industries');

        $unclearedDataList = $unclearedDataList->where('staffing_company_invitation.from_company_id', '=', $company_id);
        $unclearedDataList = $unclearedDataList->where('staffing_company_invitation.to_invitation_status', '!=', 1);

        $dataList = $unclearedDataList->get();
        $toCompanyIds = array_filter(array_unique($dataList->pluck('to_company_id')->toArray()));

        $companyTagsGrouped = [];
        $clientsGrouped = [];

        if (!empty($toCompanyIds)) {
            $companyTagsGrouped = DB::table('company_tags')
                ->whereIn('company_id', $toCompanyIds)
                ->get()
                ->groupBy('company_id');

            $clientsGrouped = DB::table('clients')
                ->select('clients.id', 'clients.company_id', 'clients.tags')
                ->whereIn('clients.company_id', $toCompanyIds)
                ->where('clients.self_company', 1)
                ->get()
                ->keyBy('company_id');
        }

        return DataTables::of($dataList)
            ->editColumn('chkbox', function ($row) use ($companyTagsGrouped, $clientsGrouped) {

                $cname = $btn = $to_company_id = $to_invitation_status = $tags_html = '';

                $cname = $row->company_name;
                if ($cname == '') {
                    $cname = $row->to_email;
                }
                $cname = ucwords($cname);

                $bgTextDecore = $strVal = '';
                $str = trim(@$cname);
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

                if ($row->to_invitation_status == -1) {
                    $btn = '<a href="javascript:void(0);" type="button" class="btn btn-sm btn-light fw-bolder me-3 fs-6" style="color:#FF0000">Ignore Request</a>';
                } else {
                    if ($row->to_company_id > 0) {
                        $btn = '<a href="javascript:void(0);" type="button" class="btn btn-sm btn-light fw-bolder me-3 fs-6">Pending Request</a>';
                    } else {
                        $btn = '<a href="javascript:void(0);" type="button" class="btn btn-sm btn-light fw-bolder me-3 fs-6">Waiting For Registration</a>';
                    }
                }

                $industries = $ind_name = '&nbsp;&nbsp;';
                $industries = $row->industries;
                if ($industries > 0) {
                    $ind_name = $row->ind_name;
                }

                $to_company_id = $tags_html = '';
                if ($row->to_company_id > 0) {
                    if (isset($companyTagsGrouped[$row->to_company_id])) {
                        foreach ($companyTagsGrouped[$row->to_company_id] as $val) {
                            $tags_html .= '<span class="badge badge-light my-1">'.$val->tag_name.'</span> ';
                        }
                    }

                    if (isset($clientsGrouped[$row->to_company_id])) {
                        $tags = $clientsGrouped[$row->to_company_id]->tags;
                        if (!empty($tags)) {
                            $tagsArr = explode(',', $tags);
                            foreach ($tagsArr as $val) {
                                $tags_html .= '<span class="badge badge-light my-1 me-2">'.$val.'</span>';
                            }
                        }
                    }
                }

                return '<div class="card mb-5 mb-xl-5 mx-2">
  <div class="card-body px-5">
	<div class="d-flex flex-wrap flex-sm-nowrap mb-3">
	  <div class="me-7 mb-4">
		<div class="symbol symbol-100px symbol-lg-140px symbol-fixed position-relative"> <a href="javascript:void(0);">
		  <div class="symbol-label bg-light-primary text-primary border-radius-5 fs-40 '.$bgTextDecore.'">'.ucwords($strVal).'</div>
		  </a>
		  <div class="position-absolute translate-middle bottom-0 start-100 mb-6 bg-success rounded-circle border border-4 border-white h-20px w-20px"></div>
		</div>
	  </div>
	  
	  <div class="flex-grow-1">
		<div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
		  <div class="d-flex flex-column">
			<div class="d-flex align-items-center mb-2"> 
				<span class="text-gray-900 fs-2 fw-bolder me-2"> '.$cname.' </span> 
<a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top" title="Verified"> <span class="svg-icon svg-icon-1 svg-icon-success">
<svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24">
<path d="M10.0813 3.7242C10.8849 2.16438 13.1151 2.16438 13.9187 3.7242V3.7242C14.4016 4.66147 15.4909 5.1127 16.4951 4.79139V4.79139C18.1663 4.25668 19.7433 5.83365 19.2086 7.50485V7.50485C18.8873 8.50905 19.3385 9.59842 20.2758 10.0813V10.0813C21.8356 10.8849 21.8356 13.1151 20.2758 13.9187V13.9187C19.3385 14.4016 18.8873 15.491 19.2086 16.4951V16.4951C19.7433 18.1663 18.1663 19.7433 16.4951 19.2086V19.2086C15.491 18.8873 14.4016 19.3385 13.9187 20.2758V20.2758C13.1151 21.8356 10.8849 21.8356 10.0813 20.2758V20.2758C9.59842 19.3385 8.50905 18.8873 7.50485 19.2086V19.2086C5.83365 19.7433 4.25668 18.1663 4.79139 16.4951V16.4951C5.1127 15.491 4.66147 14.4016 3.7242 13.9187V13.9187C2.16438 13.1151 2.16438 10.8849 3.7242 10.0813V10.0813C4.66147 9.59842 5.1127 8.50905 4.79139 7.50485V7.50485C4.25668 5.83365 5.83365 4.25668 7.50485 4.79139V4.79139C8.50905 5.1127 9.59842 4.66147 10.0813 3.7242V3.7242Z" fill="#00A3FF"></path>
<path class="permanent" d="M14.8563 9.1903C15.0606 8.94984 15.3771 8.9385 15.6175 9.14289C15.858 9.34728 15.8229 9.66433 15.6185 9.9048L11.863 14.6558C11.6554 14.9001 11.2876 14.9258 11.048 14.7128L8.47656 12.4271C8.24068 12.2174 8.21944 11.8563 8.42911 11.6204C8.63877 11.3845 8.99996 11.3633 9.23583 11.5729L11.3706 13.4705L14.8563 9.1903Z" fill="white"></path>
</svg>
</span> 
</a> 
			  </div>
			  
			<div class="d-flex flex-wrap fw-bold fs-6 pe-2"> 
			
			<a href="javascript:void(0);" class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2"> <span class="svg-icon svg-icon-4 me-1">
			  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
				<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
				  <rect x="0" y="0" width="24" height="24"/>
				  <path d="M13.5,21 L13.5,18 C13.5,17.4477153 13.0522847,17 12.5,17 L11.5,17 C10.9477153,17 10.5,17.4477153 10.5,18 L10.5,21 L5,21 L5,4 C5,2.8954305 5.8954305,2 7,2 L17,2 C18.1045695,2 19,2.8954305 19,4 L19,21 L13.5,21 Z M9,4 C8.44771525,4 8,4.44771525 8,5 L8,6 C8,6.55228475 8.44771525,7 9,7 L10,7 C10.5522847,7 11,6.55228475 11,6 L11,5 C11,4.44771525 10.5522847,4 10,4 L9,4 Z M14,4 C13.4477153,4 13,4.44771525 13,5 L13,6 C13,6.55228475 13.4477153,7 14,7 L15,7 C15.5522847,7 16,6.55228475 16,6 L16,5 C16,4.44771525 15.5522847,4 15,4 L14,4 Z M9,8 C8.44771525,8 8,8.44771525 8,9 L8,10 C8,10.5522847 8.44771525,11 9,11 L10,11 C10.5522847,11 11,10.5522847 11,10 L11,9 C11,8.44771525 10.5522847,8 10,8 L9,8 Z M9,12 C8.44771525,12 8,12.4477153 8,13 L8,14 C8,14.5522847 8.44771525,15 9,15 L10,15 C10.5522847,15 11,14.5522847 11,14 L11,13 C11,12.4477153 10.5522847,12 10,12 L9,12 Z M14,12 C13.4477153,12 13,12.4477153 13,13 L13,14 C13,14.5522847 13.4477153,15 14,15 L15,15 C15.5522847,15 16,14.5522847 16,14 L16,13 C16,12.4477153 15.5522847,12 15,12 L14,12 Z" fill="#000000"/>
				  <rect fill="#FFFFFF" x="13" y="8" width="3" height="3" rx="1"/>
				  <path d="M4,21 L20,21 C20.5522847,21 21,21.4477153 21,22 L21,22.4 C21,22.7313708 20.7313708,23 20.4,23 L3.6,23 C3.26862915,23 3,22.7313708 3,22.4 L3,22 C3,21.4477153 3.44771525,21 4,21 Z" fill="#000000" opacity="0.3"/>
				</g>
			  </svg>
			  </span> '.$ind_name.' </a> 
			    
			  <a href="javascript:void(0);" class="d-flex align-items-center text-gray-400 text-hover-primary mb-2"> <span class="svg-icon svg-icon-4 me-1">
			  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
				<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
				  <polygon points="0 0 24 0 24 24 0 24"/>
				  <path d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
				  <path d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z" fill="#000000" fill-rule="nonzero"/>
				</g>
			  </svg>
			  </span> Badge </a> </div>
		  </div>
		  <div class="d-flex mt-4">'.$btn.'</div>
		</div>
		
		<div class="d-flex flex-wrap flex-stack">
		  <div class="d-flex flex-column flex-grow-1 pe-8">
			<div class="row w-75"> 
				<span class=""> 
					'.$tags_html.'
				</span> 
			</div>
		  </div>
		</div>
		
	  </div>
	</div>
	<div class="separator separator-solid my-7 d-none"></div>
  </div>
</div>';

            })
            ->rawColumns(['chkbox'])
            ->addIndexColumn()
            ->make(true);

    }

    public function suggestedPartners($id)
    {
        $company_id = auth()->user()->company_id;
        $this->spartnerDataCnt = $this->agencyListingService->getSummaryCounters($company_id);
        $this->pageTitle = __('Suggested Partners');

        return view('service_company.consultancy.suggestedPartners', $this->data);
    }

    public function dviewMore($id)
    {
        $company_id = auth()->user()->company_id;

        // Step A: Tenant Ownership Guard — resolve client -> company ID
        $partnerCompanyId = DB::table('clients')
            ->where('id', $id)
            ->value('company_id');

        if (!$partnerCompanyId) {
            abort(404, 'Partner not found.');
        }

        // Step B: verify the auth'd tenant has an accepted relation with this company
        $isAuthorizedPartner = DB::table('staffing_company_relation')
            ->where(function ($query) use ($company_id, $partnerCompanyId) {
                $query->where('from_company_id', $company_id)->where('to_company_id', $partnerCompanyId);
            })->orWhere(function ($query) use ($company_id, $partnerCompanyId) {
                $query->where('from_company_id', $partnerCompanyId)->where('to_company_id', $company_id);
            })
            ->where('final_status', 1)
            ->exists();

        abort_unless($isAuthorizedPartner, 404, 'Partner not found or unauthorized.');

        $previousUrl = url()->previous();
        $this->all_tab_show = false;
        if (! str_contains($previousUrl, 'find-agency')) {
            $this->all_tab_show = true;
        }

        $this->company_id = $company_id;
        $cachePayload = [
            'id' => $id,
            'company' => $company_id,
            'user_id' => $this->user->id,
        ];

        $versionKey = 'consultancyviewmore_version_user_'.$this->user->id;
        $version = cache()->get($versionKey, 1);
        $cacheKeySet = 'consultancyviewmore:'.$this->user->id.':'.$version.':'.md5(json_encode($cachePayload));
        /* ----------------------------
        ⚡ Check DragonflyDB / Redis first (Quick Win Step 4)
        ----------------------------- */
        if (cache()->has($cacheKeySet)) {
            return response()->json(cache()->get($cacheKeySet));
        }

        $this->countries = Country::all();
        $this->categories = JobCategory::all();
        $this->companyType = GeneralMethod::industries_type_arr(); // GeneralMethod::accountCompanyType();
        $this->department = GeneralMethod::department();
        $this->industries = GeneralMethod::industries();
        $this->countryArr = GeneralMethod::getAllCountryList();
        $this->statelist = GeneralMethod::statelist(101);

        $this->directClientDetail = DB::table('clients')
            ->select(
                'companies.id as company_id', 'companies.description as companies_description',
                'clients.id as client_id', 'clients.name', 'clients.logo_logical_path', 'clients.industry', 'clients.tags', 'clients.website', 'clients.description',
                'companies.id as companies_id', 'companies.logo', 'companies.company_type', 'companies.company_size', 'companies.founded',
                'staffing_company_relation.created_at as to_created_at', 'staffing_company_relation.from_company_id', 'staffing_company_relation.to_company_id', 'companies.industries_type', 'companies.created_at', 'companies.website', 'companies.industries'
            )
            ->join('companies', 'companies.id', '=', 'clients.company_id')
            ->leftJoin('staffing_company_relation', 'staffing_company_relation.to_company_id', '=', 'companies.id')
            ->where('clients.id', '=', $id)
            ->first();

        $this->client_file = DB::table('client_file')->select('*')->where('client_id', $id)->get();
        $this->client_location = DB::table('client_location')
            ->select('client_location.city', 'client_location.city_id', 'client_location.address', 'client_location.postcode', 'client_location.country_id')
            ->where('client_location.client_id', $id)
            ->first();

        $this->city_name = '';

        if (! empty($this->client_location->country_id) && ($this->client_location->country_id == 101)) {
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

        // Quick Win Step 5b: Queries 1 & 2 deleted; Query 3 below provides exact final value
        $this->active_requisition = 0;

        $requisitionRow = '';
        $requisitionRow = DB::table('recommendation_relation')
            ->join('jobs', 'jobs.id', '=', 'recommendation_relation.job_id')
            ->select(
                DB::raw("SUM(CASE WHEN jobs.status = 'active'  THEN 1 ELSE 0 END) AS active"),
                DB::raw("SUM(CASE WHEN jobs.status = 'hold'  THEN 1 ELSE 0 END) AS hold"),
                DB::raw("SUM(CASE WHEN jobs.status = 'inactive'  THEN 1 ELSE 0 END) AS inactive")
            )
            ->where('recommendation_relation.from_company_id', '=', $this->directClientDetail->from_company_id)
            ->where('recommendation_relation.to_company_id', '=', $this->directClientDetail->to_company_id)
            ->where('recommendation_relation.final_status', '=', 1)
            ->first();

        if ($requisitionRow->active > 0) {
            $this->active_requisition = $requisitionRow->active;
        }

        $this->client_contact_info = DB::table('roles')
            ->select('users.*')
            ->join('role_user', 'role_user.role_id', '=', 'roles.id')
            ->join('users', 'users.id', '=', 'role_user.user_id')
            ->where('roles.company_id', '=', $this->directClientDetail->company_id)
            ->where('roles.name', '=', 'admin')
            ->where('roles.display_name', '=', 'Administrator')
            ->get();

        if ($id) {
            $params = [
                'index' => ELASTIC_CLIENT_LIST,
                'id' => $id,
            ];

            $response = $this->elasticService->getClient()->get($params);
            $this->directClientDetail = (object) $response['_source'];

            // dd($this->directClientDetail);
        }

        // $startOfMonth = Carbon::now()->startOfMonth();
        $startOfMonth = Carbon::now()->startOfMonth()->subMonths(3);
        $endOfMonth = Carbon::now()->endOfMonth();

        $startOfMonthcomman = Carbon::now()->startOfMonth()->subMonths(6);
        $endOfMonthcomman = Carbon::now()->endOfMonth();

        // dd($startOfMonth,$endOfMonth); $company_id

        $to_company_id = $this->directClientDetail->company_id;

        $must = [];
        $should = [];
        $filter = [];
        $sort = [];

        $boolQuery = [
            'bool' => [
                'must' => [
                    // First required condition
                    [
                        'bool' => [
                            'must' => [
                                ['term' => ['from_company_id' => $company_id]],
                                ['term' => ['to_company_id' => $to_company_id]],
                                ['term' => ['final_status' => 1]],
                            ],
                            'must_not' => [
                                ['terms' => ['job_master_status_id' => [8, 9]]],
                            ],
                        ],
                    ],
                    // Second required condition
                    [
                        'range' => [
                            'updated_at' => [
                                'gte' => $startOfMonth,
                                'lte' => $endOfMonth,
                            ],
                        ],
                    ],

                ],
            ],
        ];

        // $boolQuery = [
        // 		'bool' => [
        // 			'should' => [
        // 				// First condition
        // 				[
        // 					'bool' => [
        // 						'must' => array_merge([
        // 							['term' => ['to_company_id' => $to_company_id]],
        // 				            ['term' => ['from_company_id' => $company_id]],
        // 						])
        // 					]
        // 				],
        // 				// Second condition
        // 				[
        // 					'bool' => [
        // 						'must' => array_merge([
        // 							['term' => ['to_company_id' => $to_company_id]],
        // 				            ['term' => ['from_company_id' => $company_id]],
        // 							['range' => ['jobId' => ['gt' => 0]]],
        // 						], [
        // 							['bool' => [
        // 								'must_not' => [
        // 									['term' => ['job_master_status_id' => 8]]
        // 								]
        // 							]]
        // 						])
        // 					]
        // 				],
        // 			],
        // 			'minimum_should_match' => 1,
        // 			'filter' => [
        // 				[
        // 					'bool' => [
        // 						'should' => [
        // 							[
        // 								'range' => [
        // 									'updated_at' => [
        // 										'gte' => $startOfMonth,
        // 										'lte' => $endOfMonth,
        // 									],
        // 								],
        // 							],
        // 						],
        // 						'minimum_should_match' => 1,
        // 					],
        // 				],
        // 			],
        // 		]
        // ];
        $params = [
            'index' => ELASTIC_CANDIDATES,
            'request_cache' => true,
            'body' => [
                'track_total_hits' => true,
                'size' => 0,
                'query' => $boolQuery,
                'aggs' => [
                    'status_counts' => [
                        'terms' => [
                            'field' => 'job_master_status_id', // Use `.keyword` for exact match
                            'size' => 10, // Increase if you have more unique statuses
                        ],
                    ],
                ],
            ],
        ];

        $response = $this->elasticService->getClient()->search($params);
        $bucketData = $response['aggregations']['status_counts']['buckets'] ?? [];
        $this->result = collect($bucketData)->mapWithKeys(function ($item) {
            return [$item['key'] => $item['doc_count']];
        });

        $this->joined_count = $this->result->get('6', 0);
        $this->sourcing_count = $this->result->get('1', 0);
        $this->screening_count = $this->result->get('2', 0);
        $this->evaluation_count = $this->result->get('3', 0);
        $this->negotiation_count = $this->result->get('4', 0);
        $this->offer_count = $this->result->get('5', 0);
        $this->Rejected_count = $this->result->get('7', 0);
        $this->inreview_count = $this->result->get('8', 0);

        $this->total_candidates = $this->inreview_count + $this->sourcing_count + $this->screening_count + $this->evaluation_count + $this->negotiation_count + $this->offer_count + $this->joined_count + $this->Rejected_count;

        $this->startOfMonth = $startOfMonth;
        $this->endOfMonth = $endOfMonth;

        // dd($this->total_candidates,$this->inreview_count,$this->sourcing_count,$this->screening_count,$this->evaluation_count,$this->negotiation_count,$this->offer_count,$this->joined_count,$this->Rejected_count);

        /** Start Jobs Count Graph */
        $must = [];
        $filter = [];
        $must[] = [
            'bool' => [
                'should' => [
                    /*
                    OPTION 1
                    */
                    [
                        'nested' => [
                            'path' => 'recommendation_relation',
                            'query' => [
                                'bool' => [
                                    'must' => [
                                        [
                                            'term' => [
                                                'recommendation_relation.from_company_id' => $company_id,
                                            ],
                                        ],
                                        [
                                            'term' => [
                                                'recommendation_relation.to_company_id' => $to_company_id,
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

        // $must[] = [
        // 		'bool' => [
        // 			'should' => [
        // 				['term' => ['client_id' => $id]],
        // 			],
        // 			'minimum_should_match' => 1,
        // 		],
        // ];

        $params = [
            'index' => ELASTIC_JOBS,
            'request_cache' => true,
            'body' => [
                'track_total_hits' => true,
                'size' => 0,
                'query' => [
                    'bool' => [
                        'must' => $must,
                        'filter' => $filter,
                    ],
                ],
                'aggs' => [
                    'status_counts' => [
                        'terms' => [
                            'field' => 'status', // Use `.keyword` for exact match
                            'size' => 10, // Increase if you have more unique statuses
                        ],
                    ],
                ],
            ],
        ];

        $response = $this->elasticService->getClient()->search($params);
        $bucketData = $response['aggregations']['status_counts']['buckets'] ?? [];
        $this->counts = collect($bucketData)->mapWithKeys(function ($item) {
            return [$item['key'] => $item['doc_count']];
        });

        /** End Jobs Count Graph */

        /** Start Candidate last 7 days */
        $endDate = Carbon::today()->endOfDay();
        $startDate = Carbon::today()->subDays(6)->startOfDay();
        // dd($startDate,$endDate);
        $paramsCandidates = [
            'index' => ELASTIC_CANDIDATES,
            'request_cache' => true,
            'body' => [
                'track_total_hits' => true,
                'size' => 0,
                'query' => [
                    'bool' => [
                        'must' => [
                            // First required condition
                            [
                                'bool' => [
                                    'must' => [
                                        ['term' => ['from_company_id' => $company_id]],
                                        ['term' => ['to_company_id' => $to_company_id]],
                                        ['term' => ['final_status' => 1]],
                                    ],
                                    'must_not' => [
                                        ['terms' => ['job_master_status_id' => [8, 9]]],
                                    ],
                                ],
                            ],
                            // Second required condition
                            [
                                'range' => [
                                    'updated_at' => [
                                        'gte' => $startDate->toDateString(),
                                        'lte' => $endDate->toDateString(),
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'aggs' => [
                    'uploads_over_time' => [
                        'date_histogram' => [
                            'field' => 'created_at',
                            'calendar_interval' => 'day',
                            'format' => 'yyyy-MM-dd',
                            'min_doc_count' => 0,
                            'extended_bounds' => [
                                'min' => $startDate->toDateString(),
                                'max' => $endDate->toDateString(),
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $responseCandidates = $this->elasticService->getClient()->search($paramsCandidates);
        $candidateBuckets = $responseCandidates['aggregations']['uploads_over_time']['buckets'] ?? [];

        $labels = [];
        $candidateValues = [];

        for ($i = 0; $i < 7; $i++) {
            $date = $startDate->copy()->addDays($i);
            $formatted = $date->format('M d'); // e.g., 'Jul 16'
            $labels[] = $formatted;
            // Find matching doc_count from candidates
            $candidateBucket = collect($candidateBuckets)->firstWhere('key_as_string', $date->format('Y-m-d'));

            $candidateValues[] = $candidateBucket['doc_count'] ?? 0;
        }
        // dd($labels,$candidateValues);
        $this->labels = $labels;
        $this->candidateValues = $candidateValues;
        $this->candidateValuesTotal = array_sum($candidateValues);

        /** End Candidate last 7 days */

        /** Start Offer Count Graph */
        $boolQuery = [
            'bool' => [
                'must' => [
                    [
                        'term' => ['from_company_id' => $company_id],
                    ],
                    [
                        'term' => ['to_company_id' => $to_company_id],
                    ],
                    [
                        'term' => ['final_status' => 1],
                    ],
                    [
                        'range' => [
                            'of_created_date' => [
                                'gte' => $startOfMonthcomman->toDateString(),
                                'lte' => $endOfMonthcomman->toDateString(),
                            ],
                        ],
                    ],
                ],
            ],
        ];

        // $boolQuery = [
        // 	'bool' => [
        // 		'should' => [
        // 			// First condition
        // 			[
        // 				'bool' => [
        // 					'must' => array_merge([
        // 						['term' => ['to_company_id' => $to_company_id]],
        // 						['term' => ['from_company_id' => $company_id]],
        // 						['term' => ['job_master_status_id' => 6]],
        // 					])
        // 				]
        // 			],
        // 		],
        // 		'minimum_should_match' => 1,
        // 		'filter' => [
        // 			[
        // 				'bool' => [
        // 					'should' => [
        // 						[
        // 							'range' => [
        // 								'of_created_date' => [
        // 									'gte' => $startOfMonthcomman->toDateString(),
        // 									'lte' => $endOfMonthcomman->toDateString(),
        // 								],
        // 							],
        // 						],
        // 					],
        // 					'minimum_should_match' => 1,
        // 				],
        // 			],
        // 		],
        // 	]
        // ];

        $params = [
            'index' => ELASTIC_CANDIDATES,
            'request_cache' => true,
            'body' => [
                'track_total_hits' => true,
                'size' => 0,
                'query' => $boolQuery,
                'aggs' => [
                    'of_offer_count' => [
                        'date_histogram' => [
                            'field' => 'of_created_date',
                            'calendar_interval' => 'month',
                            'format' => 'yyyy-MM',
                            'min_doc_count' => 0,
                            'extended_bounds' => [
                                'min' => $startOfMonthcomman->format('Y-m'),
                                'max' => $endOfMonthcomman->format('Y-m'),
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $response = $this->elasticService->getClient()->search($params);
        $candidateBucketsoffer = $response['aggregations']['of_offer_count']['buckets'] ?? [];

        $candidateValuesCommoffer = [];
        $labelsCommoffer = [];
        $startDateComm = Carbon::now();
        for ($i = 5; $i >= 0; $i--) {
            $startDate = $startDateComm->copy()->startOfMonth()->subMonths($i);
            $endDate = $startDate->copy()->endOfMonth();
            // dd($startDate,$endDate);
            $formattedComm = $startDate->format('M Y'); // e.g., 'Jul 16'
            $labelsCommoffer[] = $formattedComm;
            $candidateBucketoffer = collect($candidateBucketsoffer)->firstWhere('key_as_string', $startDate->format('Y-m'));
            $candidateValuesCommoffer[] = $candidateBucketoffer['doc_count'] ?? 0;

        }
        $this->labelsCommoffer = $labelsCommoffer;
        $this->candidateValuesCommoffer = $candidateValuesCommoffer;
        //  dd($this->candidateValuesCommoffer,$this->labelsCommoffer);
        $this->candidateValuesTotaloffer = array_sum($candidateValuesCommoffer);

        /** End Offer count Graph */

        /** Start Candidate list joined*/
        $startOfMonthcomman = Carbon::now()->startOfMonth()->subMonths(6);
        $endOfMonthcomman = Carbon::now()->endOfMonth();

        $must = [];
        $should = [];
        $filter = [];
        $sort = [];

        $boolQuery = [
            'bool' => [
                'must' => [
                    [
                        'term' => ['from_company_id' => $company_id],
                    ],
                    [
                        'term' => ['to_company_id' => $to_company_id],
                    ],
                    [
                        'term' => ['final_status' => 1],
                    ],
                    [
                        'range' => [
                            'of_joined_date' => [
                                'gte' => $startOfMonthcomman->toDateString(),
                                'lte' => $endOfMonthcomman->toDateString(),
                            ],
                        ],
                    ],
                ],
            ],
        ];

        // $boolQuery = [
        // 	'bool' => [
        // 		'should' => [
        // 			// First condition
        // 			[
        // 				'bool' => [
        // 					'must' => array_merge([
        // 						['term' => ['to_company_id' => $to_company_id]],
        // 						['term' => ['from_company_id' => $company_id]],
        // 						['term' => ['job_master_status_id' => 6]],
        // 					])
        // 				]
        // 			],
        // 		],
        // 		'minimum_should_match' => 1,
        // 		'filter' => [
        // 			[
        // 				'bool' => [
        // 					'should' => [
        // 						[
        // 							'range' => [
        // 								'of_joined_date' => [
        // 									'gte' => $startOfMonthcomman->toDateString(),
        // 									'lte' => $endOfMonthcomman->toDateString(),
        // 								],
        // 							],
        // 						],
        // 					],
        // 					'minimum_should_match' => 1,
        // 				],
        // 			],
        // 		],
        // 	]
        // ];

        $sort[] = ['of_joined_date' => ['order' => 'desc']];
        $params = [
            'index' => ELASTIC_CANDIDATES,
            'request_cache' => true,
            'body' => [
                'track_total_hits' => true,
                'size' => 6,
                'sort' => $sort,
                'query' => $boolQuery,
                'aggs' => [
                    'of_joined_count' => [
                        'date_histogram' => [
                            'field' => 'of_joined_date',
                            'calendar_interval' => 'month',
                            'format' => 'yyyy-MM',
                            'min_doc_count' => 0,
                            'extended_bounds' => [
                                'min' => $startOfMonthcomman->format('Y-m'),
                                'max' => $endOfMonthcomman->format('Y-m'),
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $response = $this->elasticService->getClient()->search($params);
        $this->total_candidates_join = $response['hits']['total']['value'] ?? 0;
        $data = array_map(function ($hit) {
            return $hit['_source'];
        }, $response['hits']['hits']);
        $this->data_list = $data;
        $candidateBucketsjoin = $response['aggregations']['of_joined_count']['buckets'] ?? [];

        $candidateValuesCommjoin = [];
        $labelsCommjoin = [];
        $startDateComm = Carbon::now();
        for ($i = 5; $i >= 0; $i--) {
            $startDate = $startDateComm->copy()->startOfMonth()->subMonths($i);
            $endDate = $startDate->copy()->endOfMonth();
            // dd($startDate,$endDate);
            $formattedComm = $startDate->format('M Y'); // e.g., 'Jul 16'
            $labelsCommjoin[] = $formattedComm;
            $candidateBucketjoin = collect($candidateBucketsjoin)->firstWhere('key_as_string', $startDate->format('Y-m'));
            $candidateValuesCommjoin[] = $candidateBucketjoin['doc_count'] ?? 0;

        }
        $this->labelsCommjoin = $labelsCommjoin;
        $this->candidateValuesCommjoin = $candidateValuesCommjoin;
        //  dd($this->candidateValuesCommjoin,$this->labelsCommjoin);
        $this->candidateValuesTotaljoin = array_sum($candidateValuesCommjoin);

        /** End Candidate list joined*/

        /** Start Interview Section */
        $page = 1;
        $size = 5; // Default 20 results per page
        $from = ($page - 1) * $size;
        $must = [];
        $should = [];
        $filter = [];
        $nowMinus1Hour = now()->subHour()->toIso8601String();

        $must[] = [
            'bool' => [
                'must' => [
                    [
                        'term' => ['from_company_id' => $company_id],
                    ],
                    [
                        'term' => ['to_company_id' => $to_company_id],
                    ],
                ],
            ],
        ];

        // $must[] = [
        // 	'bool' => [
        // 		'should' => [
        // 			['term' => ['client_id' => $id]],
        // 		],
        // 		'minimum_should_match' => 1,
        // 	]
        // ];

        $upcomingParams = [
            'index' => ELASTIC_INERTVIEW_TRACKER,
            'request_cache' => true,
            'body' => [
                'from' => $from,
                'size' => $size,
                'sort' => [
                    ['schedule_date' => ['order' => 'asc']],
                ],
                'query' => [
                    'bool' => [
                        'must' => $must,
                        'filter' => [
                            [
                                'range' => [
                                    'schedule_date' => [
                                        'gte' => $nowMinus1Hour,
                                        'format' => 'strict_date_optional_time',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $upcomingData = [];
        $upcomingTotal = 0;

        if ($this->elasticService->indexExists(ELASTIC_INERTVIEW_TRACKER)) {
            $response = $this->elasticService->getClient()->search($upcomingParams);
            $upcomingTotal = $response['hits']['total']['value'] ?? 0;
            $upcomingData = array_map(fn ($hit) => $hit['_source'], $response['hits']['hits']);
        }
        $this->upcomingData = $upcomingData;

        // ////////////////////////////////// last 7 days
        $endDate = Carbon::today()->endOfDay();
        $startDate = Carbon::today()->subDays(6)->startOfDay();
        $recentParams = [
            'index' => ELASTIC_INERTVIEW_TRACKER,
            'request_cache' => true,
            'body' => [
                'from' => $from,
                'size' => $size,
                'sort' => [
                    ['schedule_date' => ['order' => 'desc']],
                ],
                'query' => [
                    'bool' => [
                        'must' => $must,
                        'filter' => [
                            [
                                'range' => [
                                    'schedule_date' => [
                                        'gte' => $startDate->toDateString(),
                                        'lte' => $endDate->toDateString(),
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'aggs' => [
                    'uploads_over_time' => [
                        'date_histogram' => [
                            'field' => 'schedule_date',
                            'calendar_interval' => 'day',
                            'format' => 'yyyy-MM-dd',
                            'min_doc_count' => 0,
                            'extended_bounds' => [
                                'min' => $startDate->toDateString(),
                                'max' => $endDate->toDateString(),
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $recentData = [];
        $recentTotal = 0;

        if ($this->elasticService->indexExists(ELASTIC_INERTVIEW_TRACKER)) {
            $response = $this->elasticService->getClient()->search($recentParams);
            $interviewBuckets = $response['aggregations']['uploads_over_time']['buckets'] ?? [];
            $recentTotal = $response['hits']['total']['value'] ?? 0;
            $recentData = array_map(fn ($hit) => $hit['_source'], $response['hits']['hits']);
        }
        $this->recentData = $recentData;

        $interviewlabels = [];
        $interviewValues = [];

        for ($i = 0; $i < 7; $i++) {
            // dd($date->format('Y-m-d'));
            $date = $startDate->copy()->addDays($i);
            $formatted = $date->format('M d'); // e.g., 'Jul 16'
            $interviewlabels[] = $formatted;
            // Find matching doc_count from candidates
            $interviewBucket = collect($interviewBuckets)->firstWhere('key_as_string', $date->format('Y-m-d'));

            $interviewValues[] = $interviewBucket['doc_count'] ?? 0;
        }
        $this->interviewlabels = $interviewlabels;
        $this->interviewValues = $interviewValues;
        // dd($this->interviewlabels);
        $this->interviewValuesTotal = array_sum($interviewValues);

        // ////////////////////////////////// last 6 months
        $startDateComm = Carbon::now();
        $startDate = $startDateComm->copy()->startOfMonth()->subMonths(5);
        $endDate = $startDateComm;
        $recentParamsMonth = [
            'index' => ELASTIC_INERTVIEW_TRACKER,
            'request_cache' => true,
            'body' => [
                'from' => $from,
                'size' => $size,
                'sort' => [
                    ['schedule_date' => ['order' => 'desc']],
                ],
                'query' => [
                    'bool' => [
                        'must' => $must,
                        'filter' => [
                            [
                                'range' => [
                                    'schedule_date' => [
                                        'gte' => $startDate->toDateString(),
                                        'lte' => $endDate->toDateString(),
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'aggs' => [
                    'uploads_over_time' => [
                        'date_histogram' => [
                            'field' => 'schedule_date',
                            'calendar_interval' => 'month',
                            'format' => 'yyyy-MM-dd',
                            'min_doc_count' => 0,
                            'extended_bounds' => [
                                'min' => $startDate->toDateString(),
                                'max' => $endDate->toDateString(),
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $recentDataMonth = [];
        $recentTotalMonth = 0;

        if ($this->elasticService->indexExists(ELASTIC_INERTVIEW_TRACKER)) {
            $responseMonth = $this->elasticService->getClient()->search($recentParamsMonth);
            $interviewBucketsMonth = $responseMonth['aggregations']['uploads_over_time']['buckets'] ?? [];
            $recentTotalMonth = $responseMonth['hits']['total']['value'] ?? 0;
            $recentDataMonth = array_map(fn ($hit) => $hit['_source'], $responseMonth['hits']['hits']);
        }
        $this->recentDataMonth = $recentDataMonth;

        // dd($this->recentDataMonth);

        $interviewlabelsMonth = [];
        $interviewValuesMonth = [];

        for ($i = 5; $i >= 0; $i--) {
            $startDate = $startDateComm->copy()->startOfMonth()->subMonths($i);
            // dd($startDate->format('Y-m'),$this->recentDataMonth);
            $formatted = $startDate->format('M Y'); // e.g., 'Jul 16'
            $interviewlabelsMonth[] = $formatted;
            $interviewBucket = collect($interviewBucketsMonth)->firstWhere('key_as_string', $startDate->format('Y-m-01'));
            // collect($interviewBucketsMonth)->firstWhere('key_as_string', $startDate->format('Y-m'));

            $interviewValuesMonth[] = $interviewBucket['doc_count'] ?? 0;
        }
        $this->interviewlabelsMonth = $interviewlabelsMonth;
        $this->interviewValuesMonth = $interviewValuesMonth;
        $this->interviewValuesTotalMonth = array_sum($interviewValuesMonth);

        /** End Interview Section */
        $view = view('service_company.consultancy.dviewMore', $this->data)->render();
        $responseData = Reply::dataOnly(['status' => 'success', 'view' => $view]);

        cache()->put($cacheKeySet, $responseData, now()->addMinutes(CONSULTANCY_VIEWMORE_TIME));

        return $responseData;

    }

    public function staffingContactList($id)
    {

        $parentCompanyType = GeneralMethod::parentCompanyType();
        $accountCompanyType = GeneralMethod::accountCompanyType();

        $company_id = auth()->user()->company_id;
        $department = GeneralMethod::department();

        $directContactList = DB::table('companies')
            ->select(
                'companies.id', 'companies.company_name', 'companies.logo', 'companies.industries', 'companies.company_type',
                'users.name', 'users.email', 'users.mobile', 'users.calling_code', 'users.image', 'users.id as userId', 'users.status', 'roles.display_name', 'users_profile.designation');
        $directContactList = $directContactList->join('roles', 'roles.company_id', '=', 'companies.id');
        $directContactList = $directContactList->join('role_user', 'role_user.role_id', '=', 'roles.id');
        $directContactList = $directContactList->join('users', 'users.id', '=', 'role_user.user_id');
        $directContactList = $directContactList->leftJoin('users_profile', 'users_profile.user_id', '=', 'users.id');
        $directContactList = $directContactList->where('companies.id', '=', $id);
        $directContactList = $directContactList->where(function ($query) {
            return
            $query->where('roles.name', '=', 'admin')
                ->orWhere('roles.name', '=', 'sub administrator');

        });
        // $directContactList 			=	$directContactList->where('roles.name', '=', 'admin');

        return DataTables::of($directContactList)
            ->editColumn('chkbox', function ($row) {
                return '<div class="form-check form-check-sm form-check-custom form-check-solid">
									<input class="form-check-input form-check-input2" type="checkbox" value="1" />
								  </div>';
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

                $icon = '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
								<a href="javascript:void(0);"  class="show-detail">
								  <div class="symbol-label '.$bgTextDecore.' border-radius-5">'.ucwords($strVal).'</div>
								</a>
							  </div>';

                return ' <div class="d-flex align-items-center border-0">
										  '.$icon.'                   
										  <div class="d-flex flex-column"> 
												<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">'.ucwords($row->name).'</a> 
												<a href="javascript:void(0);" class="text-muted fw-bold d-block fs-7 text-hover-primary">'.ucwords($row->designation).'</a> 
										  </div>
								  </div>';
            })
            ->editColumn('Role', function ($row) {
                return '<div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">'.ucwords($row->designation).'</span> </div>';
            })
            ->editColumn('Contacts', function ($row) {
                $email = 'XXXXXXXXXXX';
                $mobile = 'XXXXXXXXXXX';

                if ($row->display_name === 'Administrator' || $row->display_name === 'Sub Administrator') {
                    $email = $row->email;
                    $mobile = $row->mobile;
                }

                return '<div class="d-flex flex-column"> 
									<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">'.$email.'</a> 
									<div style="position: relative;display: flex;">
								    <div class="iti__flag-container" data-code="'.$row->calling_code.'" data-row="'.$row->userId.'" id="phoneDisplay_'.$row->userId.'" style="display: flex;position: relative;"> <!-- Flag and dial code will be appended here --></div>
										<div style="margin-left: 6px;margin-top: 2px;">
											<a href="javascript:void(0);" class="text-muted fw-bold d-block fs-7 text-hover-primary">'.@$mobile.'</a>
										</div>
								  </div>
								  
								</div>';
            })
            ->editColumn('Status', function ($row) {
                $classname = '';
                if ($row->status == 'active') {
                    $classname = 'bg-light-success text-success';
                } else {
                    $classname = 'bg-light-danger text-danger';
                }

                return '<span class=" '.$classname.' fw-bolder fs-6 px-1">'.ucwords($row->status).'</span>';
            })
            ->rawColumns(['chkbox', 'Name', 'Role', 'Contacts', 'Status'])
            ->addIndexColumn()->make(true);

    }

    public function allreq_ajax(Request $request)
    {

        $toCompanyId = request('id');
        $company_id = auth()->user()->company_id;
        $chkAdminRole = $this->user->hasRole('admin');
        // $chkAdminRole 		=	DB::table('roles')->select('roles.id')->where('id', $this->user->role->role_id)->where('name', 'admin')->count();
        $job_status = GeneralMethod::job_status();
        $salaryMode = GeneralMethod::salaryMode();
        $currencySymbole = GeneralMethod::currencySymbole();

        $searchQuery = $request->input('q');
        $page = max((int) $request->input('page', 1), 1);
        $size = (int) $request->input('size', 10); // Default 20 results per page
        $from = ($page - 1) * $size;

        $must[] = [
            'bool' => [
                'should' => [
                    [
                        'nested' => [
                            'path' => 'recommendation_relation',
                            'query' => [
                                'bool' => [
                                    'must' => [
                                        ['term' => ['recommendation_relation.to_company_id' => $toCompanyId]],
                                        ['term' => ['recommendation_relation.from_company_id' => $company_id]],
                                        ['term' => ['recommendation_relation.final_status' => 1]],
                                    ],
                                ],
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
            'body' => [
                'from' => $from,
                'size' => $size,
                'sort' => [],
                'query' => [
                    'bool' => [
                        'must' => $must,
                        'filter' => [],
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
        $userPermissions = $this->userPermissions;

        return response()->json([
            'html' => view('service_company.ajax_pages.staffing-partner-jobs-ajax', compact('data', 'userPermissions', 'company_id', 'toCompanyId'))->render(),
            'total' => $total,
            'pagination' => view('service_company.ajax_pages.pagination', compact('pagination'))->render(),
        ]);

    }
}
