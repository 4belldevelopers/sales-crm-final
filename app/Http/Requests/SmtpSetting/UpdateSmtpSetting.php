<?php

namespace App\Http\Requests\SmtpSetting;

use Froiden\LaravelInstaller\Request\CoreRequest;
use Illuminate\Validation\Validator;

/**
 * Class UpdateSmtpSetting
 *
 * Form request handling validation for JERO Super Admin SMTP gateway configuration.
 * Strictly enforces gateway authorization (IDs 8, 9, 10, 11, 12), validates socket parameters,
 * supports password omission (preserving existing credentials), and protects operational counters.
 */
class UpdateSmtpSetting extends CoreRequest
{
    /**
     * Allowed gateway IDs in JERO multi-gateway architecture.
     *
     * @var array<int>
     */
    public const ALLOWED_GATEWAY_IDS = [8, 9, 10, 11, 12];

    /**
     * Determine if the user is authorized to make this request.
     * Route is protected by 'auth' and 'super-admin' middlewares.
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
        $hostInput = $this->input('mail_host');

        // Fallback for single-gateway payload
        if (! is_array($hostInput)) {
            return [
                'mail_driver' => 'required|string|in:smtp,mail,sendmail',
                'mail_host' => 'required|string|max:191',
                'mail_port' => 'required|numeric|between:1,65535',
                'mail_username' => 'required|string|max:191',
                'mail_password' => 'nullable|string|max:191',
                'mail_encryption' => 'nullable|in:ssl,tls,none',
                'from_mail' => 'nullable|email|max:191',
                'mail_from_email' => 'nullable|email|max:191',
                'mail_from_name' => 'nullable|string|max:191',
                'status' => 'nullable|in:0,1',
            ];
        }

        // Multi-gateway batch submission rules
        return [
            'mail_driver' => 'required|string',
            'mail_host' => 'required|array|min:1',
            'mail_host.*' => 'required|string|max:191',
            'mail_port' => 'required|array|min:1',
            'mail_port.*' => 'required|numeric|between:1,65535',
            'mail_username' => 'required|array|min:1',
            'mail_username.*' => 'required|string|max:191',
            'mail_password' => 'nullable|array',
            'mail_password.*' => 'nullable|string|max:191',
            'mail_encryption' => 'nullable|array',
            'mail_encryption.*' => 'nullable|in:ssl,tls,none',
            'from_mail' => 'required|array|min:1',
            'from_mail.*' => 'required|email|max:191',
            'mail_from_name' => 'nullable|array',
            'mail_from_name.*' => 'nullable|string|max:191',
            'status' => 'required|array|min:1',
            'status.*' => 'required|in:0,1',
        ];
    }

    /**
     * Configure custom validation callbacks to prevent gateway ID tampering.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $hostInput = $this->input('mail_host');
            if (is_array($hostInput)) {
                foreach (array_keys($hostInput) as $key) {
                    if (! in_array((int) $key, self::ALLOWED_GATEWAY_IDS, true)) {
                        $validator->errors()->add(
                            "mail_host.{$key}",
                            "Unauthorized Gateway ID ({$key}) detected. Only Gateways 8, 9, 10, 11, and 12 may be configured."
                        );
                    }
                }
            }
        });
    }

    /**
     * Get custom friendly attribute names for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        $attributes = [
            'mail_driver' => 'Mail Driver',
        ];

        $gatewayTitles = [
            8 => 'Gateway #08 (Mass Mail)',
            9 => 'Gateway #09 (Candidate & Resume)',
            10 => 'Gateway #10 (Interview Reminders)',
            11 => 'Gateway #11 (Notifications)',
            12 => 'Gateway #12 (Accounts & Offers)',
        ];

        $hostInput = $this->input('mail_host');

        if (is_array($hostInput)) {
            foreach (array_keys($hostInput) as $key) {
                $prefix = $gatewayTitles[$key] ?? ('Gateway #'.$key);
                $attributes["mail_host.{$key}"] = "{$prefix} SMTP Host";
                $attributes["mail_port.{$key}"] = "{$prefix} Port";
                $attributes["mail_username.{$key}"] = "{$prefix} Username";
                $attributes["mail_password.{$key}"] = "{$prefix} Password";
                $attributes["mail_encryption.{$key}"] = "{$prefix} Encryption";
                $attributes["from_mail.{$key}"] = "{$prefix} From Email";
                $attributes["mail_from_name.{$key}"] = "{$prefix} From Name";
                $attributes["status.{$key}"] = "{$prefix} Status";
            }
        }

        return $attributes;
    }

    /**
     * Custom validation error messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'mail_port.*.between' => 'The port must be a valid port number between 1 and 65535 (commonly 465 or 587).',
            'from_mail.*.email' => 'Please provide a valid sender email address (e.g. noreply@example.com).',
            'mail_host.*.required' => 'The SMTP host is required for this gateway.',
            'mail_username.*.required' => 'The SMTP username is required for this gateway.',
        ];
    }
}
