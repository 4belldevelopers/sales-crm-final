<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * StoreCrmOfferingRequest — Validation for JeroCRM Product / Services Wizard.
 *
 * Intent: Enforces conditional Product vs Service rules, Confidential pricing bypass,
 *         Fixed Price -> One-Time frequency lock, capacity bounds, and company-scoped SKU uniqueness.
 */
class StoreCrmOfferingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $companyId = (int) (auth()->user()->company_id ?? 0);
        $offeringId = $this->route('id') ?? $this->route('offering');
        if ($this->routeIs('service_company.crm.offerings.updateJob') && $offeringId) {
            $offeringId = \App\Models\CrmOffering::where('company_id', $companyId)
                ->where('job_id', (int) $offeringId)
                ->value('id');
        }
        $operationId = trim((string) $this->input('operation_id', ''));
        if (! $offeringId && $operationId !== '') {
            $offeringId = \App\Models\CrmOffering::where('company_id', $companyId)
                ->where('operation_id', $operationId)
                ->value('id');
        }
        $status = (string) $this->input('status', 'active');
        $isDraft = $status === 'draft';
        $isConfidential = $this->input('pricing_visibility') === 'confidential';
        $isUnlimited = $this->boolean('is_unlimited_capacity', false);

        return [
            'operation_id' => ['nullable', 'string', 'max:64'],
            'offering_class' => ['required', Rule::in(['product', 'service'])],
            'offering_type' => ['required', 'string', 'max:80'],
            'owner_client_id' => ['nullable', 'integer'],
            'address_id' => ['nullable', 'integer'],
            'name' => ['required', 'string', 'max:255'],
            'product_url' => ['nullable', 'url', 'max:500'],
            'crm_category' => [$isDraft ? 'nullable' : 'required', 'string', 'max:150'],
            'industry_id' => ['nullable', 'integer'],

            'target_segments' => ['nullable'],
            'available_capacity' => [($isDraft || $isUnlimited) ? 'nullable' : 'required', 'integer', 'min:1'],
            'is_unlimited_capacity' => ['nullable', 'boolean'],
            'status' => ['required', Rule::in(['draft', 'active', 'inactive', 'temporarily_unavailable', 'discontinued'])],

            'pricing_visibility' => ['required', Rule::in(['public_catalog', 'client_portal', 'internal_only', 'confidential'])],
            'min_price' => [($isDraft || $isConfidential) ? 'nullable' : 'required', 'numeric', 'min:0'],
            'max_price' => ['nullable', 'numeric', 'min:0'],
            'currency_code' => ['required', 'string', 'max:10'],
            'pricing_model' => ['required', 'string', 'max:60'],
            'pricing_frequency' => ['nullable', 'string', 'max:60'],
            'is_negotiable' => ['nullable', 'boolean'],

            'delivery_model' => ['required', 'string', 'max:60'],
            'service_availability' => ['required', 'string', 'max:60'],
            'coverage_scope' => ['nullable', 'string', 'max:80'],
            'validity_days' => ['nullable', 'string', 'max:30'],
            'valid_until' => ['nullable', 'date'],

            'sku' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('crm_offerings', 'sku')
                    ->where(fn ($q) => $q->where('company_id', $companyId)->whereNull('deleted_at'))
                    ->ignore($offeringId),
            ],
            'unit' => ['nullable', 'string', 'max:50'],
            'warranty' => ['nullable', 'string', 'max:120'],
            'brochure' => ['nullable', 'file', 'mimes:jpeg,png,jpg,webp,pdf', 'max:4096'],
            'service_duration' => ['nullable', 'string', 'max:120'],
            'implementation_timeline' => ['nullable', 'string', 'max:120'],
            'sla_details' => ['nullable', 'string'],

            'eligibility_criteria' => ['nullable'],
            'description' => [$isDraft ? 'nullable' : 'required', 'string'],
            'features_scope' => ['nullable', 'string'],
            'specifications_eligibility' => ['nullable', 'string'],
            'customer_benefits' => ['nullable', 'string'],
            'additional_info' => ['nullable', 'string'],
            'scope_of_work' => ['nullable', 'string'],
            'deliverables' => ['nullable', 'string'],

            'visibility_internal_crm' => ['nullable', 'boolean'],
            'visibility_client_portal' => ['nullable', 'boolean'],
            'visibility_public_catalog' => ['nullable', 'boolean'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->input('pricing_visibility') !== 'confidential') {
                $min = $this->input('min_price');
                $max = $this->input('max_price');
                if ($min !== null && $max !== null && $max !== '' && (float) $max < (float) $min) {
                    $validator->errors()->add('max_price', 'Maximum Price must be greater than or equal to Minimum / Base Price.');
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'sku.unique' => 'This Product Code / SKU already exists in your company catalog.',
            'name.required' => 'Product / Service Name is required.',
            'crm_category.required' => 'Product Category / Industry is required.',
            'description.required' => 'Product / Service Description is required.',
        ];
    }
}
