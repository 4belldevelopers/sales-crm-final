<?php

namespace App\Http\Requests\SuperAdmin;

use App\Helper\Reply;
use App\Http\Requests\CoreRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * UpdateSuperAdminProfileRequest — Form Request Validation for Super Admin Profile.
 *
 * Intent: Strictly validates root administrative identity, contact channels,
 * password rotation with confirmation and current password verification,
 * avatar image uploads, and external social/communication links.
 * Tenancy: Master Super Admin scope (auth()->user()->is_superadmin === 1).
 */
class UpdateSuperAdminProfileRequest extends CoreRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && (int) auth()->user()->is_superadmin === 1;
    }

    /**
     * Prepare inputs for validation (e.g. prefix https:// to linkedin URL if omitted).
     */
    protected function prepareForValidation(): void
    {
        $mergeData = [];

        if ($this->filled('email')) {
            $mergeData['email'] = strtolower(trim((string) $this->input('email')));
        }

        if ($this->filled('name')) {
            $mergeData['name'] = trim((string) $this->input('name'));
        }

        if ($this->filled('mobile')) {
            $mergeData['mobile'] = trim((string) $this->input('mobile'));
        }

        if ($this->filled('calling_code')) {
            $mergeData['calling_code'] = trim((string) $this->input('calling_code'));
        }

        if ($this->filled('whatsapp_number')) {
            $mergeData['whatsapp_number'] = trim((string) $this->input('whatsapp_number'));
        }

        if ($this->filled('linkedin')) {
            $linkedin = trim((string) $this->input('linkedin'));
            if (! preg_match('~^(?:f|ht)tps?://~i', $linkedin)) {
                $linkedin = 'https://' . $linkedin;
            }
            $mergeData['linkedin'] = $linkedin;
        }

        if (! empty($mergeData)) {
            $this->merge($mergeData);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $userId = auth()->id();

        return [
            // Core Identity
            'name' => 'required|string|max:191',
            'email' => 'required|email:filter|max:191|unique:users,email,' . $userId,

            // Password Credentials & Security
            'current_password' => 'nullable|string',
            'password' => 'nullable|string|min:8|max:50|confirmed',
            'password_confirmation' => 'nullable|required_with:password',

            // Direct Phone & Calling Code
            'calling_code' => 'nullable|string|max:10',
            'mobile' => 'nullable|string|max:25',

            // Communications & Social Links
            'whatsapp_number' => 'nullable|string|max:25',
            'linkedin' => 'nullable|url|max:255',

            // Avatar Photo
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'remove_image' => 'nullable',
        ];
    }

    /**
     * Custom friendly attribute names for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'Full Name',
            'email' => 'Email Address',
            'current_password' => 'Current Password',
            'password' => 'New Password',
            'password_confirmation' => 'Confirm Password',
            'calling_code' => 'Country Dial Code',
            'mobile' => 'Mobile Phone Number',
            'whatsapp_number' => 'WhatsApp Number',
            'linkedin' => 'LinkedIn Profile URL',
            'image' => 'Profile Picture',
        ];
    }

    /**
     * Custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Please enter your full legal name.',
            'email.required' => 'Your administrative email address is required.',
            'email.email' => 'Please provide a valid, RFC-compliant email address.',
            'email.unique' => 'The specified email address is already in use by another account.',
            'password.min' => 'The new password must be at least 8 characters in length for root security.',
            'password.confirmed' => 'The password confirmation does not match the new password.',
            'password_confirmation.required_with' => 'Please confirm your new password in the confirmation field.',
            'image.max' => 'The profile image may not be larger than 2MB (2048 KB).',
            'image.mimes' => 'The profile image must be a file of type: jpeg, png, jpg, or webp.',
            'linkedin.url' => 'Please provide a valid LinkedIn URL (e.g. https://linkedin.com/in/username).',
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
}
