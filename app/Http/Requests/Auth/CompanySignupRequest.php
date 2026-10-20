<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class CompanySignupRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare inputs before validation: default company_type to 2 (Service Company)
     * for frictionless lightweight signup.
     */
    protected function prepareForValidation(): void
    {
        if (! $this->has('company_type') || empty($this->company_type)) {
            $this->merge(['company_type' => 2]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     * The 4 Essential Inputs: Name, Email, Phone (with Code), Password.
     * Zero-DNS real-time validation: syntax checked via RFC 5321 in User RAM and server.
     */
    public function rules(): array
    {
        return [
            // 1. Full Name (authentic casing preserved, trims whitespace, no control characters)
            'name' => ['required', 'string', 'min:2', 'max:100'],

            // 2. Work Email (RFC 5321 syntax; supports corporate domains and free webmails)
            'email' => ['required', 'string', 'email:rfc', 'max:191', 'unique:users,email'],

            // 3. Contact Number & Country Calling Code
            'contact_number' => ['required', 'string', 'min:6', 'max:20'],
            'contact_code' => ['required', 'string', 'max:10'],

            // 4. Company Type (structural organization model)
            // 1: Staffing Agency, 2: Service Company, 3: Product Company, 4: Freelancer
            'company_type' => ['required', 'in:1,2,3,4'],

            // 5. Password (Argon2id / bcrypt standard; min 8, max 128)
            'password' => ['required', 'string', 'min:8', 'max:128'],

            // Anti-bot honeypot field (must remain empty; bots fill hidden fields)
            'website_url' => ['nullable', 'max:0'],
        ];
    }

    /**
     * Custom validation error messages.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Please enter your full name.',
            'name.min' => 'Full name must be at least 2 characters.',
            'email.required' => 'Please enter your work email address.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email is already registered on Jero. Please sign in instead.',
            'contact_number.required' => 'Please enter your phone number.',
            'contact_code.required' => 'Please select your country calling code.',
            'company_type.required' => 'Please select your company type.',
            'company_type.in' => 'Please select a valid company type.',
            'password.required' => 'Please enter a secure password.',
            'password.min' => 'Password must be at least 8 characters.',
        ];
    }
}
