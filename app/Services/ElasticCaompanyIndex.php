<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ElasticCaompanyIndex
{
    protected $elasticService;

    public function __construct(ElasticService $elasticService)
    {
        $this->elasticService = $elasticService;
    }

    public function companyIndexElastic($comid)
    {

        $companies = DB::table('companies')
            ->select('companies.*', 'td_industries.ind_name as industry_name')
            ->leftJoin('td_industries', 'td_industries.ind_id', '=', 'companies.industries')
            ->where('id', '=', $comid)
            ->get();

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
                            return [
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

            // $candidates = DB::table('add_job_wise_condidates')
            // 	->join('job_applications', 'job_applications.id', '=', 'add_job_wise_condidates.job_application_id')
            // 	->select('job_applications.id','job_applications.full_name','job_applications.condidate_type','add_job_wise_condidates.master_status_id')
            // 	->where('add_job_wise_condidates.company_id', $company_id)
            // 	->get()
            // 	->map(function ($c) {
            // 		$skills = DB::table('job_application_skills')
            // 			->join('job_applications', 'job_applications.id', '=', 'job_application_skills.job_application_id')
            // 			->select('job_application_skills.*')
            // 			->where('job_application_skills.job_application_id', $c->id)
            // 			->get()
            // 			->map(function ($s) {
            // 				return [
            // 				'skill_id' => $s->id,
            // 				'skill_name' => strtolower($s->skills)
            // 				];
            // 			})
            // 			->toArray();

            // 		return [
            // 			'candidate_id' => $c->id,
            // 			'candidate_name' => $c->full_name,
            // 			'profile_type' => $c->condidate_type,
            // 			'skills' => $skills,
            //          'master_status_id' => $c->master_status_id
            // 		];
            // 	})
            // 	->toArray();

            // $bench_candidates = DB::table('job_applications')
            // ->select('job_applications.id','job_applications.full_name','job_applications.condidate_type','job_applications.status')
            // ->where('job_applications.company_id', $company_id)
            // ->where('job_applications.condidate_type','!=', 'other')
            // ->get()
            // ->map(function ($c) {
            // 	$skills = DB::table('job_application_skills')
            // 		->join('job_applications', 'job_applications.id', '=', 'job_application_skills.job_application_id')
            // 		->select('job_application_skills.*')
            // 		->where('job_application_skills.job_application_id', $c->id)
            // 		->get()
            // 		->map(fn($s) => [
            // 			'skill_id' => $s->id,
            // 			'skill_name' => strtolower($s->skills)
            // 		])
            // 		->toArray();

            // 	return [
            // 		'candidate_id' => $c->id,
            // 		'candidate_name' => $c->full_name,
            // 		'profile_type' => $c->condidate_type,
            // 		'skills' => $skills,
            // 		'status' => $c->status
            // 	];
            // })
            // ->toArray();

            $candidates = [];
            $bench_candidates = [];

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

            $response = $this->elasticService->getClient()->update([
                'index' => ELASTIC_COMPANIES_INDEX,
                'id' => $company_id,
                'body' => [
                    'doc' => [
                        'id' => $company_id,
                        'company_name' => $company->company_name,
                        'description' => $company->description,
                        'logo' => $company->logo,
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
                ],
            ]);

            return true;

        }

    }
}
