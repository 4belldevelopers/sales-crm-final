<?php

namespace App\Http\Requests\SuperAdmin;

use App\Helper\Reply;
use App\Http\Requests\CoreRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * UpdateGlobalSettingRequest — Input Validation for Global Platform Settings.
 *
 * Intent: Strictly validates platform-wide branding, regional localization,
 * system security/maintenance flags, account retention limits, brand logo files,
 * and official seller/invoicing ("Sold-By") address details.
 * Tenancy: Global Super Admin scope (system-wide singleton).
 */
class UpdateGlobalSettingRequest extends CoreRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && (int) auth()->user()->is_superadmin === 1;
    }

    /**
     * Prepare inputs for validation (e.g. prefix https:// to website if omitted).
     */
    protected function prepareForValidation(): void
    {
        if ($this->filled('website')) {
            $website = trim($this->input('website'));
            if (! preg_match('~^(?:f|ht)tps?://~i', $website)) {
                $this->merge(['website' => 'https://'.$website]);
            }
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            // Platform Brand & Contact Details
            'company_name' => 'required|string|max:191',
            'company_email' => 'required|email:filter|max:191',
            'company_phone' => 'required|string|max:25',
            'website' => 'nullable|url|max:191',
            'address' => 'nullable|string|max:1000',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048',
            'remove_logo' => 'nullable',

            // Regional & Localization
            'currency_id' => 'required|integer|exists:currencies,id',
            'locale' => 'required|string|max:10',

            // System Security & Lifecycle Policies
            'google_recaptcha_key' => 'nullable|string|max:255',
            'system_update' => 'nullable',
            'delete_account_in' => 'nullable|integer|min:0|max:9999',
            'hoursDays' => 'required|in:hour,day',

            // Invoicing & Sold-By Address
            'country' => 'nullable|integer|exists:countries,id',
            'first_name' => 'nullable|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'building' => 'nullable|string|max:255',
            'street' => 'nullable|string|max:500',
            'state_id' => 'nullable|integer',
            'city' => 'nullable|integer',
            'postal_code' => 'nullable|string|max:20',
            'organization' => 'nullable|string|max:191',
            'gstin' => 'nullable|string|max:50',
            'pan_no' => 'nullable|string|max:50',
        ];
    }

    /**
     * Handle a failed validation attempt and throw a unified JSON exception.
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json(Reply::formErrors($validator), 422)
        );
    }

    /**
     * Custom friendly attribute names for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'company_name' => 'Company Name',
            'company_email' => 'Company Email',
            'company_phone' => 'Company Phone',
            'website' => 'Official Website',
            'address' => 'Company Address',
            'logo' => 'Platform Logo',
            'currency_id' => 'Base Currency',
            'locale' => 'Default Language',
            'google_recaptcha_key' => 'Google reCAPTCHA Key',
            'system_update' => 'Maintenance Mode Status',
            'delete_account_in' => 'Account Deletion Period',
            'hoursDays' => 'Account Deletion Time Unit',
            'country' => 'Billing Country',
            'first_name' => 'Signatory First Name',
            'last_name' => 'Signatory Last Name',
            'building' => 'Building / Society',
            'street' => 'Street Address',
            'state_id' => 'Billing State',
            'city' => 'Billing City',
            'postal_code' => 'Postal Code',
            'organization' => 'Registered Organization',
            'gstin' => 'GSTIN / TAXIN',
            'pan_no' => 'PAN No. / Tax ID',
        ];
    }

    /**
     * Custom validation error messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'company_name.required' => 'The platform company name is required.',
            'company_email.required' => 'The official platform email is required.',
            'company_email.email' => 'Please enter a valid official email address.',
            'company_phone.required' => 'The company telephone number is required.',
            'website.url' => 'Please enter a valid website URL (including http:// or https://).',
            'currency_id.required' => 'Please select an active base currency.',
            'currency_id.exists' => 'The selected currency is invalid.',
            'locale.required' => 'Please select the default system language.',
            'hoursDays.required' => 'Please select the account retention time unit (Hours or Days).',
            'hoursDays.in' => 'The account retention unit must be either "hour" or "day".',
            'delete_account_in.integer' => 'The account retention duration must be a valid number.',
            'logo.image' => 'The logo file must be an image.',
            'logo.mimes' => 'The logo must be an image of type: jpeg, png, jpg, webp, svg.',
            'logo.max' => 'The logo image may not be greater than 2 MB.',
        ];
    }
}
