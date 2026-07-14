<?php

namespace App\Http\Controllers\ServiceCompany;

use App\Models\Helper\Reply;
use App\Models\JobApplication;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use App\Helper\GeneralMethod;
use App\OfferJobApplication;
use Carbon\Carbon;

class ServiceCompanyApplicationOfferController extends ServiceCompanyBaseController
{

	public function __construct()
	{
		parent::__construct();
		$this->pageTitle = __('Offer Tracker');
		$this->pageIcon = 'icon-user';
	}

	public function index(Request $request)
	{
		abort_if(Gate::denies('view_job_applications'), 403);
		$company_id = auth()->user()->company_id;

		$lock_unlock = GeneralMethod::checksubscription();
		if($lock_unlock==false){
			return redirect()->route('service_company.dashboard')->with('message_paid', 'This is a paid featute');;
		}


		$this->appCounter = DB::table('job_applications');
		$this->appCounter = $this->appCounter->select('job_applications.id');
		$this->appCounter = $this->appCounter->join('jobs', 'jobs.id', 'job_applications.job_id');
		$this->appCounter = $this->appCounter->join('clients', 'clients.id', 'jobs.client_id');
		$this->appCounter = $this->appCounter->join('users', 'users.id', 'job_applications.submitted_by');
		$this->appCounter = $this->appCounter->join('master_status', 'master_status.id', 'job_applications.master_status_id');
		$this->appCounter = $this->appCounter->leftJoin('recommendation_relation', 'recommendation_relation.job_id', '=', 'jobs.id');
		$this->appCounter = $this->appCounter->where('job_applications.master_status_id', '=', 5);
		$this->appCounter = $this->appCounter->where('jobs.company_id', '=', $company_id)->where('job_applications.master_status_id', '=', 5);
		$this->appCounter = $this->appCounter->orWhere(function ($query) use ($company_id) {
			return
				$query->where('recommendation_relation.final_status', '=', 1)
					->where('recommendation_relation.to_company_id', '=', $company_id)
					->where('job_applications.master_status_id', '=', 5)
					->where('job_applications.thirdparty_company_id', '=', $company_id);
		});
		$this->appCounter = $this->appCounter->count();
		$this->countries  = DB::table('countries')->get();

		// KPI stats & pipeline for the redesigned index page.
		//
		// WHY this reads offer_create_main_tds and not offer_job_applications.offer_acceptance:
		// the offer lifecycle's source of truth is offer_create_main_tds.candidate_status
		// (0-created, 1-sent/released, 2-accepted, 3-rejected, 4-expired). The "sent" and
		// "expired" transitions are only ever written there (+ Elasticsearch); the legacy
		// offer_job_applications.offer_acceptance column only receives accept/reject, so
		// aggregating it made Sent/Expired permanently read 0. We read the active offer doc
		// (status IN (1,3); 2 = superseded version) and mirror the exact buckets the
		// Elasticsearch tab filters use, so the tab badges match the pipeline/KPI counts.
		// Tenancy: scoped by offer_job_applications.company_id — raw DB::table() is NOT
		// auto-scoped, so the company_id filter is applied explicitly (project Rule 3).
		$offerStats = DB::table('offer_job_applications as o')
			->leftJoin('offer_create_main_tds as t', function ($j) {
				$j->on('t.offer_id', '=', 'o.id')->whereIn('t.status', [1, 3]);
			})
			->whereNull('o.deleted_at')
			->where('o.company_id', $company_id)
			->selectRaw("
				COUNT(DISTINCT o.id) as total,
				SUM(CASE WHEN COALESCE(t.candidate_status, 0) = 0 THEN 1 ELSE 0 END) as pending,
				SUM(CASE WHEN t.candidate_status = 1 THEN 1 ELSE 0 END) as sent,
				SUM(CASE WHEN t.candidate_status = 2 THEN 1 ELSE 0 END) as accepted,
				SUM(CASE WHEN t.candidate_status = 3 THEN 1 ELSE 0 END) as rejected,
				SUM(CASE WHEN t.candidate_status = 4 THEN 1 ELSE 0 END) as expired,
				SUM(CASE WHEN o.joined = 1 THEN 1 ELSE 0 END) as joined_count,
				SUM(CASE WHEN o.joined_date >= NOW() AND o.joined_date <= DATE_ADD(NOW(), INTERVAL 7 DAY) THEN 1 ELSE 0 END) as joining_soon
			")
			->first();

		$t  = (int)($offerStats->total ?? 0);
		$jn = (int)($offerStats->joined_count ?? 0);
		$sn = (int)($offerStats->sent ?? 0);
		$ac = (int)($offerStats->accepted ?? 0);
		$rj = (int)($offerStats->rejected ?? 0);
		$pct = fn($n) => $t > 0 ? (int)round($n / $t * 100) : 0;

		$this->offerStats = [
			'total'        => $t,
			'pending'      => (int)($offerStats->pending ?? 0),
			'sent'         => $sn,
			'accepted'     => $ac,
			'expired'      => (int)($offerStats->expired ?? 0),
			'joining_soon' => (int)($offerStats->joining_soon ?? 0),
		];
		$this->offerPipeline = [
			['key'=>'total',   'label'=>'Created',            'count'=>$t,             'pct'=>100,       'color'=>'#6366f1','icon_bg'=>'#e0e7ff','icon_clr'=>'#6366f1','icon'=>'M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2Z'],
			['key'=>'sent',    'label'=>'Released',           'count'=>$sn,            'pct'=>$pct($sn), 'color'=>'#3b82f6','icon_bg'=>'#dbeafe','icon_clr'=>'#3b82f6','icon'=>'M6 12L3.269 3.126A59.768 59.768 0 0 1 21.485 12 59.77 59.77 0 0 1 3.27 20.876L5.999 12zm0 0h7.5'],
			['key'=>'accepted','label'=>'Accepted',           'count'=>$ac,            'pct'=>$pct($ac), 'color'=>'#22c55e','icon_bg'=>'#dcfce7','icon_clr'=>'#22c55e','icon'=>'M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z'],
			['key'=>'rejected','label'=>'Rejected',           'count'=>$rj,            'pct'=>$pct($rj), 'color'=>'#ef4444','icon_bg'=>'#fee2e2','icon_clr'=>'#ef4444','icon'=>'m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z'],
			['key'=>'joined',  'label'=>'Joined',             'count'=>$jn,            'pct'=>$pct($jn), 'color'=>'#10b981','icon_bg'=>'#d1fae5','icon_clr'=>'#10b981','icon'=>'M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0zM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632z'],
			['key'=>'ratio',   'label'=>'Offer to Join Ratio','count'=>$pct($jn).'%', 'pct'=>$pct($jn), 'color'=>'#8b5cf6','icon_bg'=>'#ede9fe','icon_clr'=>'#8b5cf6','icon'=>'M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125z','sub'=>$jn.'/'.$t],
		];

		return view('service_company.applicationoffer.index', $this->data);
	}

	/**
	 * Offer Tracker dashboard (KPI cards, pipeline funnel, top recruiters).
	 * Built to run with minimal load: a couple of single-pass aggregate queries.
	 */
	public function tracker(Request $request)
	{
		abort_if(Gate::denies('view_job_applications'), 403);
		$company_id = auth()->user()->company_id;

		$lock_unlock = GeneralMethod::checksubscription();
		if ($lock_unlock == false) {
			return redirect()->route('service_company.dashboard')->with('message_paid', 'This is a paid featute');
		}

		// ---- Resolve date range (defaults to the full span of this company's offers) ----
		$earliest = DB::table('offer_job_applications')
			->whereNull('deleted_at')->where('company_id', $company_id)->min('created_at');

		$end   = $request->filled('end')   ? Carbon::parse($request->input('end'))->endOfDay()     : Carbon::now()->endOfDay();
		$start = $request->filled('start') ? Carbon::parse($request->input('start'))->startOfDay()
				: ($earliest ? Carbon::parse($earliest)->startOfDay() : Carbon::now()->startOfMonth());

		// previous equal-length window, for the "vs last period" deltas
		$spanDays  = max(1, $start->diffInDays($end) + 1);
		$prevEnd   = (clone $start)->subDay()->endOfDay();
		$prevStart = (clone $prevEnd)->subDays($spanDays - 1)->startOfDay();

		// ---- Single-pass KPI + funnel aggregate (one row) ----
		// Same source of truth as the Offer Tracker index(): the offer lifecycle lives in
		// offer_create_main_tds.candidate_status (0-created, 1-released, 2-accepted, 3-rejected,
		// 4-expired). The legacy offer_job_applications flag columns (offer_release,
		// offer_acceptance) are never populated for the release/accept steps, which made the
		// "Offer Released" and "Offer Accepted" funnel stages read 0. We read the active offer
		// doc (t.status IN (1,3); 2 = superseded). Tenancy: scoped by o.company_id (Rule 3).
		$aggSelect = "
			COUNT(DISTINCT o.id) as total,
			SUM(CASE WHEN COALESCE(t.candidate_status, 0) = 0 THEN 1 ELSE 0 END) as pending,
			SUM(CASE WHEN t.candidate_status = 1 THEN 1 ELSE 0 END) as released,
			SUM(CASE WHEN t.candidate_status = 2 THEN 1 ELSE 0 END) as accepted,
			SUM(CASE WHEN t.candidate_status = 3 THEN 1 ELSE 0 END) as declined,
			SUM(CASE WHEN o.joined = 1 THEN 1 ELSE 0 END) as joined
		";

		$baseQuery = function ($from, $to) use ($company_id, $aggSelect) {
			return DB::table('offer_job_applications as o')
				->leftJoin('offer_create_main_tds as t', function ($j) {
					$j->on('t.offer_id', '=', 'o.id')->whereIn('t.status', [1, 3]);
				})
				->whereNull('o.deleted_at')
				->where('o.company_id', $company_id)
				->whereBetween('o.created_at', [$from, $to])
				->selectRaw($aggSelect)
				->first();
		};

		$cur  = $baseQuery($start, $end);
		$prev = $baseQuery($prevStart, $prevEnd);

		$pct = function ($v) { return (int) round($v); };
		$delta = function ($now, $was) {
			if ($was <= 0) { return $now > 0 ? 100 : 0; }
			return (int) round((($now - $was) / $was) * 100);
		};

		// Conversion % = joined / total offers in the window
		$conversion     = $cur->total > 0 ? round(($cur->joined / $cur->total) * 100) : 0;
		$prevConversion = $prev->total > 0 ? round(($prev->joined / $prev->total) * 100) : 0;

		$stats = [
			'total'      => (int) $cur->total,
			'pending'    => (int) $cur->pending,
			'accepted'   => (int) $cur->accepted,
			'declined'   => (int) $cur->declined,
			'joined'     => (int) $cur->joined,
			'conversion' => $pct($conversion),
		];

		$deltas = [
			'total'      => $delta($cur->total, $prev->total),
			'pending'    => $delta($cur->pending, $prev->pending),
			'accepted'   => $delta($cur->accepted, $prev->accepted),
			'declined'   => $delta($cur->declined, $prev->declined),
			'joined'     => $delta($cur->joined, $prev->joined),
			'conversion' => $pct($conversion - $prevConversion),
		];

		// ---- Pipeline funnel stages ----
		$interviewCleared = (int) $cur->total; // candidates that reached the offer stage
		$funnel = [
			['key' => 'interview', 'label' => 'Interview Cleared', 'count' => $interviewCleared],
			['key' => 'drafted',   'label' => 'Offer Drafted',     'count' => (int) $cur->total],
			['key' => 'released',  'label' => 'Offer Released',     'count' => (int) $cur->released],
			['key' => 'accepted',  'label' => 'Offer Accepted',     'count' => (int) $cur->accepted],
			['key' => 'joined',    'label' => 'Joined',             'count' => (int) $cur->joined],
		];
		$base = max(1, $interviewCleared);
		foreach ($funnel as &$stage) {
			$stage['rate'] = round(($stage['count'] / $base) * 100, 1);
		}
		unset($stage);
		$pipelineConversion = $cur->accepted > 0 ? round(($cur->joined / $cur->accepted) * 100, 1) : 0;

		// ---- Top recruiters (one grouped query) ----
		$topRecruiters = DB::table('offer_job_applications as o')
			->leftJoin('users as u', 'u.id', '=', 'o.submitted_by')
			->leftJoin('offer_create_main_tds as t', function ($j) {
				$j->on('t.offer_id', '=', 'o.id')->whereIn('t.status', [1, 3]);
			})
			->whereNull('o.deleted_at')
			->where('o.company_id', $company_id)
			->whereBetween('o.created_at', [$start, $end])
			->where('o.submitted_by', '>', 0)
			->groupBy('o.submitted_by', 'u.name')
			->selectRaw("
				o.submitted_by as id,
				COALESCE(u.name, 'Unassigned') as name,
				COUNT(DISTINCT o.id) as offers,
				SUM(CASE WHEN t.candidate_status = 2 OR o.joined = 1 THEN 1 ELSE 0 END) as wins
			")
			->orderByDesc('offers')
			->limit(5)
			->get()
			->map(function ($r) {
				$r->rate = $r->offers > 0 ? (int) round(($r->wins / $r->offers) * 100) : 0;
				return $r;
			});

		$this->data['stats']              = $stats;
		$this->data['deltas']             = $deltas;
		$this->data['funnel']             = $funnel;
		$this->data['pipelineConversion'] = $pipelineConversion;
		$this->data['topRecruiters']      = $topRecruiters;
		$this->data['rangeStart']         = $start;
		$this->data['rangeEnd']           = $end;

		return view('service_company.applicationoffer.tracker', $this->data);
	}

	public function updateSalary(Request $request)
	{
		abort_if(Gate::denies('edit_offer'), 403);

		$block = '';
		$company_id = auth()->user()->company_id;

		$option_case = @$request->option_case;
		$option_currency_id = @$request->option_currency_id;
		$option_currency_val = @$request->option_currency_val;
		$offerId = @$request->offerId;

		$jobApplication = JobApplication::find($option_currency_id);
		$OfferJobApplication = OfferJobApplication::find($offerId);
		// dd($OfferJobApplication);

		$date = date('Y-m-d H:i:s');
		$values = array(
			'event_id' => 4,
			'event_by_company_id' => auth()->user()->company_id,
			'event_by_user_id' => auth()->user()->id,

			'application_id' => @$jobApplication->id ? @$jobApplication->id : NULL,
			'application_email' => @$jobApplication->email ? @$jobApplication->email : NULL,
			'job_id' => @$OfferJobApplication->job_id ? @$OfferJobApplication->job_id : NULL,
			'note' => '',

			'create_at' => @$date ? @$date : NULL,
			'update_at' => @$date ? @$date : NULL,
		);


		if (@$jobApplication->master_status_id > 0) {
			$masterStatus = DB::table('master_status')->select('master_status.name')->where('id', $jobApplication->master_status_id)->first();
			$values['master_process_name'] = @$masterStatus->name;
		}
		if (@$jobApplication->child_status_id > 0) {
			$masterStatus = DB::table('candidate_status')->select('candidate_status.name')->where('candidate_status.id', $jobApplication->child_status_id)->first();
			$values['child_process_name'] = @$masterStatus->name;
		}
			//DB::table('timeline')->insert($values);

		if ($option_case == 'OFFER_CURRENCY') {
			DB::table('job_applications')->where('company_id', $company_id)->where('id', $option_currency_id)->limit(1)->update(array('offer_currency' => $option_currency_val));

			DB::table('offer_job_applications')->where('company_id', $company_id)->where('id', $offerId)->limit(1)->update(array('offer_currency' => $option_currency_val));

			$values['offer_currency'] = @$option_currency_val;
		}
		if ($option_case == 'OFFER_SALARY_MODE') {
			DB::table('job_applications')->where('company_id', $company_id)->where('id', $option_currency_id)->limit(1)->update(array('offer_mode' => $option_currency_val));

			DB::table('offer_job_applications')->where('company_id', $company_id)->where('id', $offerId)->limit(1)->update(array('offer_mode' => $option_currency_val));

			$values['offer_mode'] = @$option_currency_val;
		}
		if ($option_case == 'OFFER_AMOUNT') {
			DB::table('job_applications')->where('company_id', $company_id)->where('id', $option_currency_id)->limit(1)->update(array('offer_amount' => $option_currency_val));

			DB::table('offer_job_applications')->where('company_id', $company_id)->where('id', $offerId)->limit(1)->update(array('offer_amount' => $option_currency_val));

			$values['offer_amount'] = @$option_currency_val;
		}
		if ($option_case == 'OFFER_JOINING_DATE') {
			$joined_date = '';
			$joined_date = date("y-m-d H:i:s", strtotime($option_currency_val));
			DB::table('job_applications')->where('company_id', $company_id)->where('id', $option_currency_id)->limit(1)->update(array('joined_date' => $joined_date));

			DB::table('offer_job_applications')->where('company_id', $company_id)->where('id', $offerId)->limit(1)->update(array('joined_date' => $joined_date));

			$values['joined_date'] = @$joined_date;
		}
		$Offertimelime = OfferJobApplication::find($offerId);

		$Offertimelime->offer_currency=  @$request->currency ? @$request->currency : 'NULL'; 
		$Offertimelime->offer_mode=  @$request->salarymode ? @$request->salarymode : 'NULL';
		$Offertimelime->offer_amount=  @$request->offer_amount ? @$request->offer_amount : 'NULL' ;
		$Offertimelime->joined_date=  @$request->joining_date ?  date("Y-m-d H:i:s", strtotime(@$request->joining_date)) : 'NULL'; 
		$Offertimelime->save();
		$values['offer_currency'] = @$request->currency ? @$request->currency : 'NULL';
		$values['offer_mode'] = @$request->salarymode ? @$request->salarymode : 'NULL';
		$values['offer_amount'] = @$request->offer_amount ? @$request->offer_amount : 'NULL' ;
		$values['joined_date'] = @$request->joining_date ?  date("Y-m-d H:i:s", strtotime(@$request->joining_date)) : 'NULL';
		// dd($values);

		GeneralMethod::UpdateTimeline($values);

		$message = '<div class="alert alert-success" id="success-alert"><button type="button" class="close bg-transparent border-0 fw-bold" data-dismiss="alert">x</button><strong>Success! </strong> New Process have added successfully.</div>';
		return Reply::dataOnly(['status' => 'success', 'message' => $message]);


	}


}
