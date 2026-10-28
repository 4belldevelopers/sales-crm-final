<?php

namespace App\Http\Requests\SuperAdmin;

use App\Helper\Reply;
use App\Http\Requests\CoreRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;

/**
 * UpdateEmailNotificationRequest — Input Validation for Email Notification Templates.
 *
 * Intent: Strictly validates email notification template mutations (create/update),
 * enforces Super Admin authorization, bounds template body size to 256 KB,
 * and validates role identifiers against the authoritative roles table.
 * Tenancy: Global Super Admin scope (tenant_id = 0).
 */
class UpdateEmailNotificationRequest extends CoreRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && (int) auth()->user()->is_superadmin === 1;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'tokenId' => 'nullable|integer|min:0',
            'purpose' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'from_email_id' => 'required|email:filter|max:191',
            'mailType_is' => 'required|integer|between:1,10',
            'status' => 'required|in:0,1',
            'role_ck' => 'nullable|array',
            'role_ck.*' => 'string|max:100',
            'editor1' => 'required|string|max:262144', // 256 KB maximum body
        ];
    }

    /**
     * Configure custom validation callbacks to ensure role names are authentic.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $data = $validator->getData();
            $submittedRoles = $data['role_ck'] ?? $this->input('role_ck');

            if (! empty($submittedRoles) && is_array($submittedRoles)) {
                $validRoles = DB::table('roles')
                    ->distinct()
                    ->pluck('display_name')
                    ->toArray();

                foreach ($submittedRoles as $role) {
                    if (! in_array($role, $validRoles, true)) {
                        $validator->errors()->add(
                            'role_ck',
                            "Invalid role '{$role}' specified. Only authorized platform roles are permitted."
                        );
                    }
                }
            }
        });
    }

    /**
     * Handle a failed validation attempt for AJAX and JSON requests.
     */
    protected function failedValidation(Validator $validator): void
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
            'tokenId' => 'Notification ID',
            'purpose' => 'Template Purpose',
            'subject' => 'Email Subject',
            'from_email_id' => 'Sender Email Address',
            'mailType_is' => 'Notification Category',
            'status' => 'Status',
            'role_ck' => 'Recipient Roles',
            'editor1' => 'Template Body',
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
            'purpose.required' => 'The notification purpose is required.',
            'subject.required' => 'The email subject line is required.',
            'from_email_id.required' => 'The sender email address is required.',
            'from_email_id.email' => 'Please provide a valid sender email address (e.g. noreply@jero.co.in).',
            'mailType_is.required' => 'Please select a valid notification category.',
            'mailType_is.between' => 'The selected notification category is invalid.',
            'editor1.required' => 'The template body content cannot be empty.',
            'editor1.max' => 'The template body exceeds the maximum permitted size of 256 KB.',
        ];
    }
}
