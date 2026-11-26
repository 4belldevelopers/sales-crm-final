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
use App\Services\CurrencyService;
use App\Services\IndustryTaxonomyService;
use App\Services\RelationshipManagerService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * ServiceCompanyCrmOfferingController — JeroCRM Add Product / Services Wizard.
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
        $this->clientAddresses = DB::table('client_location')
            ->where('company_id', $companyId)
            ->orderBy('id')
            ->get(['id', 'client_id', 'city', 'address', 'postcode']);
        $this->industries = IndustryTaxonomyService::getGlobalManifest();
        $this->teamRoster = app(RelationshipManagerService::class)->getTeamRoster($companyId);
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

        return response()->json([
            'status' => 'success',
            'message' => $offering->status === 'draft'
                ? 'Offering saved as Draft successfully.'
                : 'Product / Service published successfully.',
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
        $this->clientAddresses = DB::table('client_location')
            ->where('company_id', $companyId)
            ->orderBy('id')
            ->get(['id', 'client_id', 'city', 'address', 'postcode']);
        $this->industries = IndustryTaxonomyService::getGlobalManifest();
        $this->teamRoster = app(RelationshipManagerService::class)->getTeamRoster($companyId);
        $this->offering = $offering;
        $this->page_event = 'EDIT';

        return view('service_company.crm.products_services.create', $this->data);
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
}
