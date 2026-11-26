<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * StoreProductRequest — Backend Validation for Sales CRM Products.
 *
 * Intent: Independently validates product creation and updates, enforces
 *         company-scoped SKU uniqueness, non-negative stock/prices, and
 *         discount/date boundaries.
 */
class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $companyId = (int) (auth()->user()->company_id ?? 0);
        $productId = $this->route('product') ?? $this->route('id');
        $status = (string) $this->input('status', 'draft');
        $isDraft = $status === 'draft';

        return [
            'name' => ['required', 'string', 'max:255'],
            'sku' => [
                'required',
                'string',
                'max:100',
                Rule::unique('products', 'sku')
                    ->where(fn ($q) => $q->where('company_id', $companyId)->whereNull('deleted_at'))
                    ->ignore($productId),
            ],
            'category_id' => [$isDraft ? 'nullable' : 'required', 'integer', 'exists:product_categories,id'],
            'subcategory_id' => ['nullable', 'integer', 'exists:product_subcategories,id'],
            'product_type' => ['required', Rule::in(['physical', 'digital', 'service'])],
            'brand' => ['nullable', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],

            'currency' => ['required', 'string', 'max:10'],
            'purchase_price' => ['nullable', 'numeric', 'min:0'],
            'selling_price' => [$isDraft ? 'nullable' : 'required', 'numeric', 'min:0'],
            'mrp' => ['nullable', 'numeric', 'min:0'],
            'discount_type' => ['nullable', Rule::in(['percentage', 'fixed'])],
            'discount_value' => ['nullable', 'numeric', 'min:0'],
            'tax_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'hsn_sac_code' => ['nullable', 'string', 'max:30'],

            'track_inventory' => ['nullable', 'boolean'],
            'opening_stock' => ['nullable', 'integer', 'min:0'],
            'available_quantity' => ['nullable', 'integer', 'min:0'],
            'min_stock_alert' => ['nullable', 'integer', 'min:0'],
            'unit' => ['nullable', 'string', 'max:50'],
            'warehouse_location' => ['nullable', 'string', 'max:150'],
            'stock_status' => ['nullable', Rule::in(['in_stock', 'low_stock', 'out_of_stock', 'not_applicable'])],

            'model_number' => ['nullable', 'string', 'max:100'],
            'barcode' => ['nullable', 'string', 'max:100'],
            'warranty_period' => ['nullable', 'string', 'max:100'],
            'weight' => ['nullable', 'string', 'max:60'],
            'dimensions' => ['nullable', 'string', 'max:120'],
            'features' => ['nullable', 'string'],
            'specifications' => ['nullable'],
            'tags' => ['nullable'],

            'status' => ['required', Rule::in(['draft', 'active', 'inactive'])],
            'visibility' => ['nullable', Rule::in(['public', 'internal', 'hidden'])],
            'is_featured' => ['nullable', 'boolean'],
            'availability' => ['nullable', Rule::in(['available', 'pre_order', 'discontinued'])],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $sellingPrice = (float) ($this->input('selling_price') ?? 0);
            $discountType = (string) ($this->input('discount_type') ?? 'percentage');
            $discountValue = (float) ($this->input('discount_value') ?? 0);

            if ($discountType === 'percentage' && $discountValue > 100) {
                $validator->errors()->add('discount_value', 'Percentage discount cannot exceed 100%.');
            }
            if ($discountType === 'fixed' && $sellingPrice > 0 && $discountValue > $sellingPrice) {
                $validator->errors()->add('discount_value', 'Fixed discount cannot exceed the Selling Price.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'sku.unique' => 'This SKU already exists in your company catalog. Please use a unique SKU.',
            'end_date.after_or_equal' => 'End Date must be on or after the Start Date.',
        ];
    }
}
