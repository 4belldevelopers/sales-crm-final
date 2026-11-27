<?php

namespace App\Http\Controllers\ServiceCompany;

use App\Helper\GeneralMethod;
use App\Http\Requests\StoreCrmOfferingRequest;
use App\Models\Client;
use App\Models\CrmOffering;
use App\Models\CrmOfferingLocation;
use App\Models\CrmOfferingQuestion;
use App\Models\CrmOfferingTag;
use App\Models\CrmOfferingTeam;
use App\Models\Job;
use App\Services\CurrencyService;
use App\Services\IndustryTaxonomyService;
use App\Services\JobService;
use App\Services\RelationshipManagerService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * ServiceCompanyCrmOfferingController â€” JeroCRM Add Product / Services Wizard.
 *
 * Intent: Handles 4-step CRM Offering creation, editing, SKU uniqueness checks,
 *         idempotency, and active catalog feeds for Deals/Quotations/Orders/Invoices.
 *         Completely isolated from recruitment `jobs` and `ServiceCompanyJobsController`.
 * Tenancy Note: All queries and writes are strictly scoped by authenticated user's company_id.
 */
class ServiceCompanyCrmOfferingController extends ServiceCompanyBaseController
{
    public function index(Request $request)
    {
        $companyId = (int) auth()->user()->company_id;
        $offerings = CrmOffering::with(['tags', 'locations'])
            ->where('company_id', $companyId)
            ->orderByDesc('id')
            ->paginate(20);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['status' => 'success', 'data' => $offerings]);
        }

        return $this->create();
    }

    /**
     * Render the 4-Step JeroCRM Add Product / Services Wizard.
     */
    public function create()
    {
        $this->pageTitle = __('Add Product / Service');
        $companyId = (int) (auth()->user()->company_id ?? 0);

        $this->currency = GeneralMethod::currency();
        $this->currency_code = $companyId > 0
            ? CurrencyService::getTenantCurrency($companyId)
            : GeneralMethod::defaultCurrency();
        $this->tenantTimezone = GeneralMethod::defaultTimeZone($companyId ?: null) ?: config('app.timezone', 'UTC');
        $this->tenantTimezoneAbbr = Carbon::now($this->tenantTimezone)->format('T');
        $this->clients = Client::where('company_id', $companyId)->orderBy('name')->get(['id', 'name', 'self_company']);
        $clientIds = $this->clients->pluck('id')->all();
        $this->clientAddresses = ! empty($clientIds)
            ? DB::table('client_location')
                ->whereIn('client_id', $clientIds)
                ->orderBy('id')
                ->get(['id', 'client_id', 'city', 'address', 'postcode'])
            : collect();
        $this->industries = IndustryTaxonomyService::getGlobalManifest();
        $this->teamRoster = app(RelationshipManagerService::class)->getTeamRoster($companyId);
        $this->clientContacts = ! empty($clientIds)
            ? DB::table('client_contact_info')
                ->whereIn('client_id', $clientIds)
                ->orderBy('contact_person_name')
                ->get(['id', 'client_id', 'contact_person_name as name', 'contact_person_designation as designation', 'contact_person_email as email', 'contact_person_number as phone'])
            : collect();
        $this->assignedOwners = collect();
        $this->assignedTeam = collect();
        $this->offering = null;
        $this->page_event = 'ADD';

        return view('service_company.crm.products_services.create', $this->data);
    }

    /**
     * Store a new CRM Offering with UUID idempotency and transactional child sync.
     */
    public function store(StoreCrmOfferingRequest $request): JsonResponse
    {
        $user = auth()->user();
        $companyId = (int) $user->company_id;
        $operationId = trim((string) $request->input('operation_id', ''));

        if ($operationId !== '') {
            $existing = CrmOffering::where('company_id', $companyId)
                ->where('operation_id', $operationId)
                ->first();
            if ($existing) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Offering already saved.',
                    'offering' => $existing->load(['locations', 'tags', 'questions', 'team']),
                ], 201);
            }
        }

        $offering = DB::transaction(function () use ($request, $companyId, $user, $operationId) {
            $payload = $this->buildPayload($request, $companyId, (int) $user->id, true);
            $payload['operation_id'] = $operationId !== '' ? $operationId : Str::uuid()->toString();

            $offering = CrmOffering::create($payload);
            $this->syncChildren($request, $offering, $companyId);

            return $offering;
        });

        // Also persist into `jobs` + Elasticsearch so the saved service appears in the Services Tracker list
        $createdJobId = null;
        try {
            $jobPayload = $this->mapOfferingToJobPayload($request, $offering, $companyId, $user);
            $job = app(JobService::class)->createJob($jobPayload, $user);
            $createdJobId = $job->id ?? null;
            if ($createdJobId) {
                $offering->update(['job_id' => $createdJobId]);
            }
        } catch (\Throwable $e) {
            \Log::warning('CRM Offering -> JobService sync skipped: '.$e->getMessage());
        }

        return response()->json([
            'status' => 'success',
            'message' => $offering->status === 'draft'
                ? 'Service saved as Draft successfully.'
                : 'Service published successfully.',
            'job_id' => $createdJobId,
            'redirect_url' => route('service_company.jobs.index'),
            'offering' => $offering->load(['locations', 'tags', 'questions', 'team']),
        ], 201);
    }

    /**
     * Render the 4-Step Wizard in Edit mode.
     */
    public function edit($id)
    {
        $companyId = (int) auth()->user()->company_id;
        $offering = CrmOffering::with(['locations', 'tags', 'questions', 'team'])
            ->where('company_id', $companyId)
            ->findOrFail($id);

        $this->pageTitle = __('Edit Product / Service');
        $this->currency = GeneralMethod::currency();
        $this->currency_code = CurrencyService::getTenantCurrency($companyId);
        $this->tenantTimezone = GeneralMethod::defaultTimeZone($companyId) ?: config('app.timezone', 'UTC');
        $this->tenantTimezoneAbbr = Carbon::now($this->tenantTimezone)->format('T');
        $this->clients = Client::where('company_id', $companyId)->orderBy('name')->get(['id', 'name', 'self_company']);
        $clientIds = $this->clients->pluck('id')->all();
        $this->clientAddresses = ! empty($clientIds)
            ? DB::table('client_location')
                ->whereIn('client_id', $clientIds)
                ->orderBy('id')
                ->get(['id', 'client_id', 'city', 'address', 'postcode'])
            : collect();
        $this->industries = IndustryTaxonomyService::getGlobalManifest();
        $this->teamRoster = app(RelationshipManagerService::class)->getTeamRoster($companyId);
        $this->clientContacts = ! empty($clientIds)
            ? DB::table('client_contact_info')
                ->whereIn('client_id', $clientIds)
                ->orderBy('contact_person_name')
                ->get(['id', 'client_id', 'contact_person_name as name', 'contact_person_designation as designation', 'contact_person_email as email', 'contact_person_number as phone'])
            : collect();
        $this->assignedOwners = collect();
        $this->assignedTeam = collect();
        $this->offering = $offering;
        $this->editJobId = 0;
        $this->page_event = 'EDIT';

        return view('service_company.crm.products_services.create', $this->data);
    }

    /**
     * Render the Service Edit Form for an existing record in `jobs` (from Services Tracker).
     */
    public function editJob($id)
    {
        $companyId = (int) auth()->user()->company_id;
        $decodedId = is_numeric($id) ? (int) $id : (int) GeneralMethod::value_decrypt($id);
        $job = Job::where('company_id', $companyId)->withoutGlobalScopes()->findOrFail($decodedId);

        $linked = CrmOffering::with(['locations', 'tags', 'questions', 'team'])
            ->where('company_id', $companyId)
            ->where('job_id', $job->id)
            ->first();

        if ($linked) {
            $offering = $linked;
        } else {
            $categoryName = DB::table('job_categories')->where('id', $job->category_id)->value('name');
            $jobSkills = DB::table('job_metronics_skills')
                ->where('job_id', $job->id)
                ->pluck('skill_name')
                ->filter()
                ->values()
                ->map(fn ($s) => (object) ['tag_name' => $s]);

            $jobLocs = DB::table('client_city_jobs')
                ->leftJoin('city_list', 'city_list.city_id', '=', 'client_city_jobs.city_id')
                ->leftJoin('state_list', 'state_list.id', '=', 'city_list.state_id')
                ->leftJoin('countries', 'countries.id', '=', 'state_list.country_id')
                ->where('client_city_jobs.job_id', $job->id)
                ->get(['city_list.city as city_name', 'state_list.state as state_name', 'countries.country_name'])
                ->filter(fn ($r) => ! empty($r->city_name))
                ->values()
                ->map(function ($r) {
                    $suffix = implode(', ', array_filter([$r->state_name ?? '', $r->country_name ?? '']));
                    $label = $suffix !== '' ? "{$r->city_name} ({$suffix})" : $r->city_name;

                    return (object) ['location_name' => $label];
                });

            $jobQs = DB::table('job_questions')
                ->join('questions', 'questions.id', '=', 'job_questions.question_id')
                ->where('job_questions.job_id', $job->id)
                ->get(['questions.question', 'questions.required'])
                ->filter(fn ($q) => ! empty($q->question))
                ->values()
                ->map(fn ($q) => (object) [
                    'question' => $q->question ?? '',
                    'answer_type' => 'short',
                    'is_required' => ($q->required ?? 'no') === 'yes',
                ]);

            if ($jobQs->isEmpty()) {
                $jobQs = DB::table('job_additional_detail')
                    ->where('job_id', $job->id)
                    ->pluck('detail_value')
                    ->filter()
                    ->values()
                    ->map(fn ($txt) => (object) [
                        'question' => $txt,
                        'answer_type' => 'short',
                        'is_required' => true,
                    ]);
            }

            $addressId = DB::table('client_location_jobs')->where('job_id', $job->id)->value('client_location_id');
            $currencyMap = GeneralMethod::currency();
            $rawCurr = $job->min_salary_currency;
            $resolvedCurrency = is_numeric($rawCurr)
                ? ($currencyMap[(int) $rawCurr] ?? 'INR')
                : (strtoupper((string) ($rawCurr ?: 'INR')));

            $freqReverse = ['2' => 'Hourly', '1' => 'Monthly', '0' => 'Annual'];
            $wfhReverse = ['yes' => 'Remote', 'other' => 'Hybrid', 'no' => 'On-site'];

            $validityDays = 'ongoing';
            if (! empty($job->start_date) && ! empty($job->end_date)) {
                $diffDays = (int) round(Carbon::parse($job->start_date)->diffInDays(Carbon::parse($job->end_date)));
                if (abs($diffDays - 30) <= 2) {
                    $validityDays = '30';
                } elseif (abs($diffDays - 60) <= 2) {
                    $validityDays = '60';
                } elseif (abs($diffDays - 90) <= 2) {
                    $validityDays = '90';
                }
            }

            $offering = (object) [
                'id' => 0,
                'operation_id' => null,
                'offering_class' => $job->offering_class ?? 'service',
                'offering_type' => $job->offering_type ?? 'Professional Service',
                'owner_client_id' => $job->client_id,
                'address_id' => $addressId ?: null,
                'name' => $job->title,
                'product_url' => $job->product_url ?? '',
                'crm_category' => $categoryName ?: '',
                'available_capacity' => $job->total_positions ?: 1,
                'is_unlimited_capacity' => (bool) ($job->is_unlimited_capacity ?? false),
                'status' => in_array($job->status, ['draft', 'active', 'inactive'], true) ? $job->status : 'active',
                'pricing_visibility' => ($job->salary_visibility ?? '') === 'confidential' ? 'confidential' : 'public_catalog',
                'min_price' => $job->min_salary ?? 0,
                'max_price' => $job->max_salary ?? null,
                'currency_code' => $resolvedCurrency,
                'pricing_model' => $job->pricing_model ?? 'Fixed Price',
                'pricing_frequency' => $freqReverse[(string) ($job->min_sal_mode ?? '0')] ?? 'One-Time',
                'delivery_model' => $wfhReverse[(string) ($job->work_type ?? 'yes')] ?? 'Remote',
                'service_availability' => $job->service_availability ?? 'Business Hours',
                'unit' => $job->unit ?? 'project',
                'sku' => $job->sku ?: ('SRV-'.$job->id),
                'warranty' => $job->warranty ?? '',
                'service_duration' => $job->service_duration ?? '',
                'implementation_timeline' => $job->implementation_timeline ?? '',
                'sla_details' => $job->sla_details ?? '',
                'brochure_path' => $job->brochure_path ?? null,
                'validity_days' => $job->validity_days ?? $validityDays,
                'description' => $job->job_description ?? '',
                'locations' => $jobLocs,
                'tags' => $jobSkills,
                'questions' => $jobQs,
                'team' => collect(),
            ];
        }

        $this->pageTitle = __('Edit Service');
        $this->currency = GeneralMethod::currency();
        $this->currency_code = CurrencyService::getTenantCurrency($companyId);
        $this->tenantTimezone = GeneralMethod::defaultTimeZone($companyId) ?: config('app.timezone', 'UTC');
        $this->tenantTimezoneAbbr = Carbon::now($this->tenantTimezone)->format('T');
        $this->clients = Client::where('company_id', $companyId)->orderBy('name')->get(['id', 'name', 'self_company']);
        $clientIds = $this->clients->pluck('id')->all();
        $this->clientAddresses = ! empty($clientIds)
            ? DB::table('client_location')
                ->whereIn('client_id', $clientIds)
                ->orderBy('id')
                ->get(['id', 'client_id', 'city', 'address', 'postcode'])
            : collect();
        $this->industries = IndustryTaxonomyService::getGlobalManifest();
        $this->teamRoster = app(RelationshipManagerService::class)->getTeamRoster($companyId);
        $this->clientContacts = ! empty($clientIds)
            ? DB::table('client_contact_info')
                ->whereIn('client_id', $clientIds)
                ->orderBy('contact_person_name')
                ->get(['id', 'client_id', 'contact_person_name as name', 'contact_person_designation as designation', 'contact_person_email as email', 'contact_person_number as phone'])
            : collect();
        $this->assignedOwners = DB::table('client_contact_info_jobs')
            ->join('client_contact_info', 'client_contact_info.id', '=', 'client_contact_info_jobs.client_contact_info_id')
            ->where('client_contact_info_jobs.job_id', $job->id)
            ->get([
                'client_contact_info.id',
                'client_contact_info.client_id',
                'client_contact_info.contact_person_name as name',
                'client_contact_info.contact_person_designation as designation',
            ]);
        $this->assignedTeam = DB::table('job_assign')
            ->join('users', 'users.id', '=', 'job_assign.user_id')
            ->where('job_assign.job_id', $job->id)
            ->where('job_assign.company_id', $companyId)
            ->get([
                'users.id as user_id',
                'users.name',
                'job_assign.spok_user_id',
            ])
            ->map(fn ($r) => (object) [
                'id' => $r->user_id,
                'name' => $r->name,
                'is_lead' => ((int) $r->spok_user_id === (int) $r->user_id),
            ]);
        $this->offering = $offering;
        $this->editJobId = $job->id;
        $this->page_event = 'EDIT';

        return view('service_company.crm.products_services.create', $this->data);
    }

    /**
     * Update an existing record in `jobs` + Elasticsearch + linked `crm_offerings`.
     */
    public function updateJob(StoreCrmOfferingRequest $request, $id): JsonResponse
    {
        $user = auth()->user();
        $companyId = (int) $user->company_id;
        $job = Job::where('company_id', $companyId)->withoutGlobalScopes()->findOrFail((int) $id);

        $linked = CrmOffering::where('company_id', $companyId)
            ->where('job_id', $job->id)
            ->first();

        DB::transaction(function () use ($request, $companyId, $user, $job, $linked) {
            if ($linked) {
                $payload = $this->buildPayload($request, $companyId, (int) $user->id, false, $linked);
                $linked->update($payload);
                $this->syncChildren($request, $linked, $companyId);
            } else {
                $payload = $this->buildPayload($request, $companyId, (int) $user->id, true, null);
                $payload['created_by'] = (int) $user->id;
                $payload['job_id'] = $job->id;
                $payload['operation_id'] = $request->input('operation_id') ?: Str::uuid()->toString();
                $created = CrmOffering::create($payload);
                $this->syncChildren($request, $created, $companyId);
            }
        });

        $jobPayload = $this->mapRequestToJobPayload($request, $companyId, $user, null);
        app(JobService::class)->updateJob($job, $jobPayload, $user);

        return response()->json([
            'status' => 'success',
            'message' => 'Service updated successfully.',
            'job_id' => $job->id,
            'redirect_url' => route('service_company.jobs.index'),
        ]);
    }

    /**
     * Update an existing CRM Offering.
     */
    public function update(StoreCrmOfferingRequest $request, $id): JsonResponse
    {
        $user = auth()->user();
        $companyId = (int) $user->company_id;
        $offering = CrmOffering::where('company_id', $companyId)->findOrFail($id);

        DB::transaction(function () use ($request, $offering, $companyId, $user) {
            $payload = $this->buildPayload($request, $companyId, (int) $user->id, false, $offering);
            $offering->update($payload);
            $this->syncChildren($request, $offering, $companyId);
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Product / Service updated successfully.',
            'offering' => $offering->fresh(['locations', 'tags', 'questions', 'team']),
        ]);
    }

    public function checkSku(Request $request): JsonResponse
    {
        $companyId = (int) auth()->user()->company_id;
        $sku = strtoupper(trim((string) $request->input('sku', '')));
        $excludeId = (int) $request->input('exclude_id', 0);

        if ($sku === '') {
            return response()->json(['available' => true]);
        }

        $exists = CrmOffering::where('company_id', $companyId)
            ->where('sku', $sku)
            ->when($excludeId > 0, fn ($q) => $q->where('id', '!=', $excludeId))
            ->exists();

        return response()->json([
            'available' => ! $exists,
            'message' => $exists ? 'This Product Code / SKU already exists in your company catalog.' : 'SKU is available.',
        ]);
    }

    public function checkReady($id): JsonResponse
    {
        $companyId = (int) auth()->user()->company_id;
        $exists = CrmOffering::where('company_id', $companyId)->where('id', (int) $id)->exists();

        return response()->json(['ready' => $exists]);
    }

    /**
     * AJAX: Return warehouse/origin addresses for a selected organization/client.
     */
    public function clientAddresses($clientId): JsonResponse
    {
        $companyId = (int) auth()->user()->company_id;
        $ownsClient = Client::where('company_id', $companyId)->where('id', (int) $clientId)->exists();
        if (! $ownsClient) {
            return response()->json(['status' => 'success', 'data' => []]);
        }

        $addresses = DB::table('client_location')
            ->where('client_id', (int) $clientId)
            ->orderBy('id')
            ->get(['id', 'client_id', 'address', 'city', 'postcode']);

        return response()->json(['status' => 'success', 'data' => $addresses]);
    }

    /**
     * Active catalog feed for Deals, Quotations, Sales Orders, and Invoices.
     */
    public function activeCatalog(Request $request): JsonResponse
    {
        $companyId = (int) auth()->user()->company_id;
        $items = CrmOffering::where('company_id', $companyId)
            ->where('status', 'active')
            ->orderBy('name')
            ->limit(50)
            ->get([
                'id', 'offering_class', 'offering_type', 'name', 'sku',
                'pricing_visibility', 'min_price', 'max_price', 'currency_code',
                'pricing_model', 'pricing_frequency', 'is_negotiable', 'unit',
            ]);

        return response()->json(['status' => 'success', 'data' => $items]);
    }

    private function buildPayload(
        StoreCrmOfferingRequest $request,
        int $companyId,
        int $userId,
        bool $isCreate,
        ?CrmOffering $existing = null
    ): array {
        $isConfidential = $request->input('pricing_visibility') === 'confidential';
        $pricingModel = (string) $request->input('pricing_model', 'Fixed Price');
        $pricingFreq = $pricingModel === 'Fixed Price'
            ? 'One-Time'
            : (string) ($request->input('pricing_frequency') ?: 'One-Time');

        $isUnlimited = $request->boolean('is_unlimited_capacity', false);
        $tz = GeneralMethod::defaultTimeZone($companyId) ?: config('app.timezone', 'UTC');

        $validityDays = (string) $request->input('validity_days', 'ongoing');
        $validUntil = $request->input('valid_until') ?: null;
        if (in_array($validityDays, ['30', '60', '90'], true)) {
            $validUntil = Carbon::now($tz)->addDays((int) $validityDays)->toDateString();
        } elseif ($validityDays === 'ongoing') {
            $validUntil = null;
        }

        $brochurePath = $existing?->brochure_path;
        if ($request->hasFile('brochure')) {
            $uploadDir = public_path('user-uploads/crm-offerings/'.$companyId);
            if (! File::isDirectory($uploadDir)) {
                File::makeDirectory($uploadDir, 0755, true);
            }
            $file = $request->file('brochure');
            $filename = Str::uuid()->toString().'.'.$file->getClientOriginalExtension();
            $file->move($uploadDir, $filename);
            $brochurePath = 'user-uploads/crm-offerings/'.$companyId.'/'.$filename;
        }

        $segments = $request->input('target_segments');
        if (is_string($segments)) {
            $segments = json_decode($segments, true) ?: [];
        }
        $eligibility = $request->input('eligibility_criteria');
        if (is_string($eligibility)) {
            $eligibility = json_decode($eligibility, true) ?: [];
        }

        $sku = trim((string) $request->input('sku', ''));

        $data = [
            'company_id' => $companyId,
            'updated_by' => $userId,
            'offering_class' => (string) $request->input('offering_class', 'product'),
            'offering_type' => (string) $request->input('offering_type', 'Physical Product'),
            'owner_client_id' => $request->filled('owner_client_id') ? (int) $request->input('owner_client_id') : null,
            'address_id' => $request->filled('address_id') ? (int) $request->input('address_id') : null,
            'name' => trim((string) $request->input('name')),
            'product_url' => $request->input('product_url'),
            'crm_category' => $request->input('crm_category'),
            'industry_id' => $request->filled('industry_id') ? (int) $request->input('industry_id') : null,

            'target_segments' => is_array($segments) ? $segments : [],
            'available_capacity' => $isUnlimited ? null : ($request->filled('available_capacity') ? max(1, (int) $request->input('available_capacity')) : null),
            'is_unlimited_capacity' => $isUnlimited,
            'status' => (string) $request->input('status', 'active'),

            'pricing_visibility' => (string) $request->input('pricing_visibility', 'public_catalog'),
            'min_price' => $isConfidential ? 0.00 : max(0.0, (float) ($request->input('min_price') ?? 0)),
            'max_price' => $isConfidential ? null : ($request->filled('max_price') ? max(0.0, (float) $request->input('max_price')) : null),
            'currency_code' => strtoupper((string) $request->input('currency_code', 'INR')),
            'pricing_model' => $pricingModel,
            'pricing_frequency' => $pricingFreq,
            'is_negotiable' => $request->boolean('is_negotiable', false),

            'delivery_model' => (string) $request->input('delivery_model', 'Remote'),
            'service_availability' => (string) $request->input('service_availability', 'Business Hours'),
            'coverage_scope' => $request->input('coverage_scope'),
            'validity_days' => $validityDays,
            'valid_until' => $validUntil,

            'sku' => $sku !== '' ? strtoupper($sku) : null,
            'unit' => $request->input('unit'),
            'warranty' => $request->input('warranty'),
            'brochure_path' => $brochurePath,
            'service_duration' => $request->input('service_duration'),
            'implementation_timeline' => $request->input('implementation_timeline'),
            'sla_details' => $request->input('sla_details'),

            'eligibility_criteria' => is_array($eligibility) ? $eligibility : [],
            'description' => $request->input('description'),
            'features_scope' => $request->input('features_scope'),
            'specifications_eligibility' => $request->input('specifications_eligibility'),
            'customer_benefits' => $request->input('customer_benefits'),
            'additional_info' => $request->input('additional_info'),
            'scope_of_work' => $request->input('scope_of_work'),
            'deliverables' => $request->input('deliverables'),

            'visibility_internal_crm' => $request->boolean('visibility_internal_crm', true),
            'visibility_client_portal' => $request->boolean('visibility_client_portal', true),
            'visibility_public_catalog' => $request->boolean('visibility_public_catalog', false),
        ];

        if ($isCreate) {
            $data['created_by'] = $userId;
        }

        return $data;
    }

    private function syncChildren(Request $request, CrmOffering $offering, int $companyId): void
    {
        $locations = $request->input('locations', []);
        if (is_string($locations)) {
            $locations = json_decode($locations, true) ?: [];
        }
        CrmOfferingLocation::where('offering_id', $offering->id)->where('company_id', $companyId)->delete();
        foreach ((array) $locations as $loc) {
            $locName = trim((string) $loc);
            if ($locName !== '') {
                CrmOfferingLocation::create([
                    'company_id' => $companyId,
                    'offering_id' => $offering->id,
                    'location_name' => $locName,
                ]);
            }
        }

        $tags = $request->input('feature_tags', []);
        if (is_string($tags)) {
            $tags = json_decode($tags, true) ?: [];
        }
        CrmOfferingTag::where('offering_id', $offering->id)->where('company_id', $companyId)->delete();
        foreach ((array) $tags as $tag) {
            $tName = trim((string) $tag);
            if ($tName !== '') {
                CrmOfferingTag::create([
                    'company_id' => $companyId,
                    'offering_id' => $offering->id,
                    'tag_name' => $tName,
                ]);
            }
        }

        $questions = $request->input('qualification_questions', []);
        if (is_string($questions)) {
            $questions = json_decode($questions, true) ?: [];
        }
        CrmOfferingQuestion::where('offering_id', $offering->id)->where('company_id', $companyId)->delete();
        foreach ((array) $questions as $idx => $q) {
            $qText = trim((string) ($q['question'] ?? ''));
            if ($qText !== '') {
                CrmOfferingQuestion::create([
                    'company_id' => $companyId,
                    'offering_id' => $offering->id,
                    'question' => $qText,
                    'answer_type' => (string) ($q['answer_type'] ?? 'short'),
                    'is_required' => (bool) ($q['is_required'] ?? false),
                    'options' => $q['options'] ?? [],
                    'sort_order' => (int) $idx,
                ]);
            }
        }

        $team = $request->input('team_members', []);
        if (is_string($team)) {
            $team = json_decode($team, true) ?: [];
        }
        CrmOfferingTeam::where('offering_id', $offering->id)->where('company_id', $companyId)->delete();
        foreach ((array) $team as $member) {
            $mName = trim((string) ($member['name'] ?? ''));
            if ($mName !== '') {
                CrmOfferingTeam::create([
                    'company_id' => $companyId,
                    'offering_id' => $offering->id,
                    'user_id' => ! empty($member['user_id']) ? (int) $member['user_id'] : null,
                    'member_name' => $mName,
                    'role_type' => ($member['role_type'] ?? 'collaborator') === 'owner' ? 'owner' : 'collaborator',
                    'is_lead' => (bool) ($member['is_lead'] ?? false),
                ]);
            }
        }
    }

    /**
     * Map a CRM Service/Offering into JobService::createJob() payload so it appears in the Services Tracker list.
     */
    private function mapOfferingToJobPayload(Request $request, CrmOffering $offering, int $companyId, $user): array
    {
        $clientId = $offering->owner_client_id;
        if (! $clientId) {
            $clientId = (int) Client::where('company_id', $companyId)->where('self_company', 1)->value('id');
        }

        $tagsRaw = $request->input('feature_tags', []);
        if (is_string($tagsRaw)) {
            $tagsRaw = json_decode($tagsRaw, true) ?: [];
        }
        $tagifyTags = array_values(array_map(fn ($t) => ['value' => (string) $t], (array) $tagsRaw));

        $locsRaw = $request->input('locations', []);
        if (is_string($locsRaw)) {
            $locsRaw = json_decode($locsRaw, true) ?: [];
        }

        $wfhMap = ['Remote' => 'yes', 'Hybrid' => 'other', 'On-site' => 'no'];
        $freqMap = ['Hourly' => '2', 'Monthly' => '1', 'Annual' => '0'];
        $salMode = $freqMap[$offering->pricing_frequency] ?? '0';

        $questionsRaw = $request->input('qualification_questions', []);
        if (is_string($questionsRaw)) {
            $questionsRaw = json_decode($questionsRaw, true) ?: [];
        }
        $customTitles = array_values(array_filter(array_map(fn ($q) => $q['question'] ?? null, (array) $questionsRaw)));

        return [
            'client_id' => $clientId ?: null,
            'address_id' => $offering->address_id,
            'title' => $offering->name,
            'category_id_display' => $offering->crm_category ?: 'Services',
            'total_positions' => $offering->available_capacity ?: 1,
            'minimum_salary' => $offering->min_price ?? 0,
            'maximum_salary' => $offering->max_price ?? $offering->min_price ?? 0,
            'min_salary_currency' => $offering->currency_code ?: 'INR',
            'max_salary_currency' => $offering->currency_code ?: 'INR',
            'salary_currency' => $offering->currency_code ?: 'INR',
            'min_sal_mode' => $salMode,
            'max_sal_mode' => $salMode,
            'salary_visibility' => $offering->pricing_visibility === 'confidential' ? 'confidential' : 'visible_everywhere',
            'work_from_home' => $wfhMap[$offering->delivery_model] ?? 'yes',
            'employment_type' => '0',
            'kt_docs_tinymce_hidden' => $offering->description ?: '',
            'tags' => json_encode($tagifyTags),
            'kt_tagify_work_location' => implode(',', (array) $locsRaw),
            'end_date' => in_array($offering->validity_days, ['30', '60', '90'], true) ? $offering->validity_days : '60',
            'custom_title' => $customTitles,
            'hiring_namager' => $request->input('hiring_namager', ''),
            'hiring_manager_id' => $request->input('hiring_manager_id', ''),
            'recruiter' => $request->input('recruiter') ?: ($user->name ?? '1'),
            'recruiter_id' => $request->input('recruiter_id') ?: (string) $user->id,
            'operation_id' => $offering->operation_id,
            'offering_class' => $offering->offering_class,
            'offering_type' => $offering->offering_type,
            'product_url' => $offering->product_url,
            'sku' => $offering->sku,
            'unit' => $offering->unit,
            'is_unlimited_capacity' => (bool) $offering->is_unlimited_capacity,
            'pricing_model' => $offering->pricing_model,
            'service_availability' => $offering->service_availability,
            'service_duration' => $offering->service_duration,
            'implementation_timeline' => $offering->implementation_timeline,
            'sla_details' => $offering->sla_details,
            'warranty' => $offering->warranty,
            'brochure_path' => $offering->brochure_path,
            'validity_days' => $offering->validity_days,
        ];
    }

    private function mapRequestToJobPayload(Request $request, int $companyId, $user, ?string $operationId): array
    {
        $clientId = $request->filled('owner_client_id') ? (int) $request->input('owner_client_id') : null;
        if (! $clientId) {
            $clientId = (int) Client::where('company_id', $companyId)->where('self_company', 1)->value('id');
        }
        $tagsRaw = $request->input('feature_tags', []);
        if (is_string($tagsRaw)) {
            $tagsRaw = json_decode($tagsRaw, true) ?: [];
        }
        $locsRaw = $request->input('locations', []);
        if (is_string($locsRaw)) {
            $locsRaw = json_decode($locsRaw, true) ?: [];
        }
        $questionsRaw = $request->input('qualification_questions', []);
        if (is_string($questionsRaw)) {
            $questionsRaw = json_decode($questionsRaw, true) ?: [];
        }
        $customTitles = array_values(array_filter(array_map(fn ($q) => $q['question'] ?? null, (array) $questionsRaw)));
        $wfhMap = ['Remote' => 'yes', 'Hybrid' => 'other', 'On-site' => 'no'];
        $freqMap = ['Hourly' => '2', 'Monthly' => '1', 'Annual' => '0'];
        $salMode = $freqMap[$request->input('pricing_frequency', 'One-Time')] ?? '0';

        return [
            'client_id' => $clientId ?: null,
            'address_id' => $request->filled('address_id') ? (int) $request->input('address_id') : null,
            'title' => trim((string) $request->input('name')),
            'category_id_display' => $request->input('crm_category') ?: 'Services',
            'total_positions' => $request->boolean('is_unlimited_capacity') ? 1 : max(1, (int) ($request->input('available_capacity') ?? 1)),
            'minimum_salary' => (float) ($request->input('min_price') ?? 0),
            'maximum_salary' => (float) ($request->input('max_price') ?? $request->input('min_price') ?? 0),
            'min_salary_currency' => strtoupper((string) $request->input('currency_code', 'INR')),
            'max_salary_currency' => strtoupper((string) $request->input('currency_code', 'INR')),
            'salary_currency' => strtoupper((string) $request->input('currency_code', 'INR')),
            'min_sal_mode' => $salMode,
            'max_sal_mode' => $salMode,
            'salary_visibility' => $request->input('pricing_visibility') === 'confidential' ? 'confidential' : 'visible_everywhere',
            'work_from_home' => $wfhMap[$request->input('delivery_model', 'Remote')] ?? 'yes',
            'employment_type' => '0',
            'kt_docs_tinymce_hidden' => $request->input('description', ''),
            'tags' => json_encode(array_values(array_map(fn ($t) => ['value' => (string) $t], (array) $tagsRaw))),
            'kt_tagify_work_location' => implode(',', (array) $locsRaw),
            'end_date' => in_array($request->input('validity_days'), ['30', '60', '90'], true) ? $request->input('validity_days') : '60',
            'custom_title' => $customTitles,
            'hiring_namager' => $request->input('hiring_namager', ''),
            'hiring_manager_id' => $request->input('hiring_manager_id', ''),
            'recruiter' => $request->input('recruiter') ?: ($user->name ?? '1'),
            'recruiter_id' => $request->input('recruiter_id') ?: (string) $user->id,
            'operation_id' => $operationId,
            'offering_class' => (string) $request->input('offering_class', 'service'),
            'offering_type' => $request->input('offering_type'),
            'product_url' => $request->input('product_url'),
            'sku' => strtoupper(trim((string) $request->input('sku', ''))),
            'unit' => $request->input('unit'),
            'is_unlimited_capacity' => $request->boolean('is_unlimited_capacity', false),
            'pricing_model' => $request->input('pricing_model'),
            'service_availability' => $request->input('service_availability'),
            'service_duration' => $request->input('service_duration'),
            'implementation_timeline' => $request->input('implementation_timeline'),
            'sla_details' => $request->input('sla_details'),
            'warranty' => $request->input('warranty'),
            'validity_days' => $request->input('validity_days'),
        ];
    }
}
