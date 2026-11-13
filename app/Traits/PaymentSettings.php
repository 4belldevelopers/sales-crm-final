<?php

namespace App\Traits;

use App\Services\SuperAdmin\SuperAdminPaymentSettingReadService;
use Illuminate\Support\Facades\Config;

/**
 * Trait PaymentSettings
 *
 * Intent: Configures Stripe cashier runtime credentials from authoritative
 * payment settings cache / Elasticsearch (0% MySQL reads).
 */
trait PaymentSettings
{
    public function setStripConfigs()
    {
        $settings = app(SuperAdminPaymentSettingReadService::class)->getPaymentSettings();
        $key = ($settings->api_key) ? $settings->api_key : config('services.stripe.key');
        $apiSecret = ($settings->api_secret) ? $settings->api_secret : config('services.stripe.secret');
        $webhookKey = ($settings->webhook_key) ? $settings->webhook_key : config('services.stripe.webhook_secret');

        Config::set('cashier.key', $key);
        Config::set('cashier.secret', $apiSecret);
        Config::set('cashier.webhook.secret', $webhookKey);
    }
}
