<?php

namespace App\Http\Requests\SuperAdmin;

use App\Helper\Reply;
use App\Http\Requests\CoreRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * ToggleNotificationStatusRequest — Input Validation for Notification Status Toggles.
 *
 * Intent: Strictly validates single-notification status updates (active/inactive),
 * enforces Super Admin authorization, and maps legacy chkStatus aliases.
 * Tenancy: Global Super Admin scope (tenant_id = 0).
 */
class ToggleNotificationStatusRequest extends CoreRequest
{
    /**
     * Prepare inputs for validation (normalizing chkStatus to status).
     */
    protected function prepareForValidation(): void
    {
        if (! $this->has('status') && $this->has('chkStatus')) {
            $this->merge([
                'status' => $this->input('chkStatus'),
            ]);
        }
    }

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
            'id' => 'required|integer|min:1',
            'status' => 'required_without:chkStatus|in:0,1',
            'chkStatus' => 'required_without:status|in:0,1',
        ];
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
            'id' => 'Notification ID',
            'status' => 'Status',
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
            'id.required' => 'The notification ID is required.',
            'id.integer' => 'The notification ID must be an integer.',
            'status.required' => 'The status value is required.',
            'status.in' => 'The status must be either 0 (inactive) or 1 (active).',
        ];
    }
}
