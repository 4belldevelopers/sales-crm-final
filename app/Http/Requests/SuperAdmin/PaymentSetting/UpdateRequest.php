<?php

namespace App\Http\Requests\SuperAdmin\PaymentSetting;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
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
            'paypal_status' => 'nullable|string|in:active,inactive,on,off,1,0',
            'paypal_client_id' => 'required_if:paypal_status,on,active,1|nullable|string|max:191',
            'paypal_secret' => 'required_if:paypal_status,on,active,1|nullable|string|max:191',
            'paypal_mode' => 'required_if:paypal_status,on,active,1|nullable|in:sandbox,live',

            'stripe_status' => 'nullable|string|in:active,inactive,on,off,1,0',
            'api_key' => 'required_if:stripe_status,on,active,1|nullable|string|max:191',
            'api_secret' => 'required_if:stripe_status,on,active,1|nullable|string|max:191',
            'webhook_key' => 'required_if:stripe_status,on,active,1|nullable|string|max:191',

            'razorpay_status' => 'nullable|string|in:active,deactive,inactive,on,off,1,0',
            'razorpay_key' => 'required_if:razorpay_status,on,active,1|nullable|string|max:191',
            'razorpay_secret' => 'required_if:razorpay_status,on,active,1|nullable|string|max:191',
            'razorpay_webhook_secret' => 'required_if:razorpay_status,on,active,1|nullable|string|max:191',
        ];
    }

    /**
     * Custom attributes for human-readable error messages.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'paypal_client_id' => 'PayPal Client ID',
            'paypal_secret' => 'PayPal Secret Key',
            'paypal_mode' => 'PayPal Environment Mode',
            'api_key' => 'Stripe Publishable Key',
            'api_secret' => 'Stripe Secret Key',
            'webhook_key' => 'Stripe Webhook Secret',
            'razorpay_key' => 'Razorpay Key ID',
            'razorpay_secret' => 'Razorpay Secret Key',
            'razorpay_webhook_secret' => 'Razorpay Webhook Secret',
        ];
    }
}
