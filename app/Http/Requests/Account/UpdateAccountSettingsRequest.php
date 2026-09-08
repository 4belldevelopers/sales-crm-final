<?php

namespace App\Http\Requests\Account;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form Request: UpdateAccountSettingsRequest
 *
 * Intent: Validates inputs for company account settings modifications and ensures
 * multi-tenancy authorization safety.
 *
 * Tenancy: Scoped strictly by auth()->user()->company_id.
 *
 * Author: Antigravity AI
 * Date: August 12, 2026
 */
class UpdateAccountSettingsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->company_id > 0;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'company_name' => 'sometimes|required|string|max:191',
            'company_email' => 'sometimes|required|email|max:191',
            'company_phone' => 'sometimes|nullable|string|max:191',
            'website' => 'sometimes|nullable|string|max:191',
            'timezone' => 'sometimes|nullable|string|max:191',
            'currency' => 'sometimes|nullable|integer',
            'language' => 'sometimes|nullable|integer',
            'app_duplicate' => 'sometimes|nullable|integer|in:0,1,2',
            'app_duplicate_days' => 'sometimes|nullable|integer|min:0',
        ];
    }
}
