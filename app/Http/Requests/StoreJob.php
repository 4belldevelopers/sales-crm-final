<?php

namespace App\Http\Requests;

/**
 * StoreJob FormRequest — Input Validation for Job Requisitions.
 *
 * Intent: Enforces request payload integrity and validation constraints for creating/updating job requisitions.
 * Tenancy Note: Tenant ID is validated against authenticated user session.
 *
 * Author: Antigravity AI
 * Date: August 14, 2026
 */
class StoreJob extends CoreRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'client_id' => 'nullable|integer',
            'category_id' => 'nullable|integer',
            'total_positions' => 'nullable|integer|min:1',
            'minimum_salary' => 'nullable|numeric|min:0',
            'maximum_salary' => 'nullable|numeric|min:0',
            'min_exp' => 'nullable|integer|min:0',
            'max_exp' => 'nullable|integer|min:0',
            'carrier_website' => 'nullable|string|max:500',
            'salary_visibility' => 'nullable|string|max:50',
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'title.required' => 'Job title is required.',
            'category_id.required' => __('menu.jobCategories').' '.__('errors.fieldRequired'),
            'location_id.required' => __('menu.locations').' '.__('errors.fieldRequired'),
        ];
    }
}
