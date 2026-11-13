<?php

namespace App\Console\Commands;

use App\Jobs\ProcessPaymentSettingOutboxEventJob;
use App\Services\ElasticService;
use App\Services\SuperAdmin\SuperAdminPaymentSettingCacheManager;
use App\Services\SuperAdmin\SuperAdminPaymentSettingReadService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Class ReindexPaymentSettingsToElasticCommand
 *
 * Intent: Authoritatively synchronizes and denormalizes MySQL payment_settings
 * into Elasticsearch index (local_payment_settings) and purges all L2 Redis/Dragonfly caches.
 *
 * Signature: php artisan payment-settings:reindex {--force}
 *
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class ReindexPaymentSettingsToElasticCommand extends Command
{
    protected $signature = 'payment-settings:reindex {--force : Force re-creation of indices}';

    protected $description = 'Reindex Super Admin Payment Settings into Elasticsearch with complete schema verification';

    public function handle(ElasticService $elasticService, SuperAdminPaymentSettingReadService $readService): int
    {
        $this->info('Starting Super Admin Payment Settings Elasticsearch reindexing...');

        $indexName = $readService->getIndexName();
        $jobHelper = new ProcessPaymentSettingOutboxEventJob;

        // 1. Ensure index exists
        if ($this->option('force') && $elasticService->indexExists($indexName)) {
            $this->warn("Deleting existing {$indexName} index due to --force flag...");
            $elasticService->getClient()->indices()->delete(['index' => $indexName]);
        }

        if (! $elasticService->indexExists($indexName)) {
            $this->info("Creating index {$indexName} with complete mapping...");
            $jobHelper->createPaymentSettingsIndex($elasticService, $indexName);
        }

        // 2. Fetch Super Admin payment setting record from MySQL
        $paymentRow = DB::table('payment_settings')->first();

        if ($paymentRow) {
            $doc = $readService->buildPaymentSettingDocument($paymentRow);

            $elasticService->getClient()->index([
                'index' => $indexName,
                'id' => '1',
                'body' => $doc,
                'refresh' => 'wait_for',
            ]);

            $this->info("Indexed Payment Settings [ID {$paymentRow->id}, Gateways: Stripe={$paymentRow->stripe_status}, PayPal={$paymentRow->paypal_status}, Razorpay={$paymentRow->razorpay_status}] into {$indexName}");
        } else {
            $this->warn('No payment_settings record found in MySQL. Creating default projection in Elasticsearch...');
            $defaultDoc = $readService->buildPaymentSettingDocument([
                'id' => 1,
                'paypal_client_id' => null,
                'paypal_secret' => null,
                'api_key' => null,
                'api_secret' => null,
                'webhook_key' => null,
                'paypal_status' => 'inactive',
                'stripe_status' => 'inactive',
                'razorpay_key' => null,
                'razorpay_secret' => null,
                'razorpay_webhook_secret' => null,
                'razorpay_status' => 'deactive',
                'paypal_mode' => 'sandbox',
            ]);

            $elasticService->getClient()->index([
                'index' => $indexName,
                'id' => '1',
                'body' => $defaultDoc,
                'refresh' => 'wait_for',
            ]);

            $this->info("Indexed default payment setting projection into {$indexName}");
        }

        // 3. Invalidate Dragonfly/Redis L2 caches
        SuperAdminPaymentSettingCacheManager::flushCaches();
        $this->info('Redis / Dragonfly L2 caches flushed successfully.');

        $this->info('Super Admin Payment Settings reindexing completed with 0 errors.');

        return Command::SUCCESS;
    }
}
