<?php

namespace App\Http\Requests;

/**
 * Form Request: Store Team Member Invitation
 *
 * Intent: Validates incoming request parameters when inviting a new team member.
 *
 * Author: Antigravity AI
 * Date: August 14, 2026
 */
class StoreTeam extends CoreRequest
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
            'name' => 'required|string|max:191',
            'email' => 'required|email:rfc,dns|disposable_email',
            'role_id' => 'required|integer',
            'logo' => 'nullable|image|max:2048',
        ];
    }

    /**
     * Custom validation messages.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => 'Full name is required',
            'email.required' => 'Email is required',
            'email.email' => 'Please enter a valid email address',
            'email.disposable_email' => 'Temporary/disposable emails are not allowed',
            'role_id.required' => 'Please select a role',
            'logo.max' => 'Logo file size must not exceed 2MB',
        ];
    }
}
