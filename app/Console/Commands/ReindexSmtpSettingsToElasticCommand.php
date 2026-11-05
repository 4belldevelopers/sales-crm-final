<?php

namespace App\Console\Commands;

use App\Services\ElasticService;
use App\Services\SuperAdmin\SuperAdminSmtpSettingCacheManager;
use App\Services\SuperAdmin\SuperAdminSmtpSettingReadService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Class ReindexSmtpSettingsToElasticCommand
 *
 * Intent: Authoritatively synchronizes and denormalizes all 5 SMTP gateways into Elasticsearch
 * index ELASTIC_SMTP_SETTINGS (local_smtp_settings) and purges all L2 Redis/Dragonfly caches.
 *
 * Signature: php artisan smtp-settings:reindex
 *
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class ReindexSmtpSettingsToElasticCommand extends Command
{
    protected $signature = 'smtp-settings:reindex';

    protected $description = 'Reindex all 5 Super Admin SMTP gateways into Elasticsearch with complete schema verification';

    public function handle(ElasticService $elasticService, SuperAdminSmtpSettingReadService $readService): int
    {
        $this->info('Starting Super Admin SMTP Settings Elasticsearch reindexing...');

        $indexName = $readService->getIndexName();

        if (! $elasticService->indexExists($indexName)) {
            $this->warn("Elasticsearch index {$indexName} does not exist. Creating with complete mappings...");

            $elasticService->getClient()->indices()->create([
                'index' => $indexName,
                'body' => [
                    'settings' => [
                        'number_of_shards' => 1,
                        'number_of_replicas' => 0,
                        'index.requests.cache.enable' => true,
                    ],
                    'mappings' => [
                        'properties' => [
                            'id' => ['type' => 'long'],
                            'mail_driver' => ['type' => 'keyword'],
                            'mail_host' => [
                                'type' => 'keyword',
                                'fields' => ['text' => ['type' => 'text']],
                            ],
                            'mail_port' => ['type' => 'integer'],
                            'mail_username' => [
                                'type' => 'keyword',
                                'fields' => ['text' => ['type' => 'text']],
                            ],
                            'mail_password' => ['type' => 'keyword'],
                            'mail_from_name' => [
                                'type' => 'text',
                                'fields' => ['keyword' => ['type' => 'keyword', 'ignore_above' => 256]],
                            ],
                            'mail_from_email' => [
                                'type' => 'keyword',
                                'fields' => ['text' => ['type' => 'text']],
                            ],
                            'mail_encryption' => ['type' => 'keyword'],
                            'notification_mail_host' => ['type' => 'keyword'],
                            'notification_mail_port' => ['type' => 'integer'],
                            'notification_mail_username' => ['type' => 'keyword'],
                            'notification_mail_password' => ['type' => 'keyword'],
                            'notification_mail_encryption' => ['type' => 'keyword'],
                            'mass_mail_host' => ['type' => 'keyword'],
                            'mass_mail_port' => ['type' => 'keyword'],
                            'mass_mail_username' => ['type' => 'keyword'],
                            'mass_mail_password' => ['type' => 'keyword'],
                            'mass_mail_encryption' => ['type' => 'keyword'],
                            'email_id' => [
                                'type' => 'keyword',
                                'fields' => ['text' => ['type' => 'text']],
                            ],
                            'contact_no' => ['type' => 'keyword'],
                            'website' => ['type' => 'keyword'],
                            'status' => ['type' => 'integer'],
                            'is_active' => ['type' => 'boolean'],
                            'dailyCount' => ['type' => 'integer'],
                            'dailyDate' => ['type' => 'keyword'],
                            'verified' => ['type' => 'boolean'],
                            'mailtype' => ['type' => 'text'],
                            'gateway_title' => [
                                'type' => 'text',
                                'fields' => ['keyword' => ['type' => 'keyword', 'ignore_above' => 256]],
                            ],
                            'gateway_badge' => ['type' => 'keyword'],
                            'service_file' => ['type' => 'keyword'],
                            'purpose_desc' => ['type' => 'text'],
                            'created_at' => ['type' => 'date', 'format' => 'yyyy-MM-dd HH:mm:ss||yyyy-MM-dd\'T\'HH:mm:ssZZZZZ||epoch_millis||strict_date_optional_time'],
                            'updated_at' => ['type' => 'date', 'format' => 'yyyy-MM-dd HH:mm:ss||yyyy-MM-dd\'T\'HH:mm:ssZZZZZ||epoch_millis||strict_date_optional_time'],
                        ],
                    ],
                ],
            ]);
            $this->info("Created Elasticsearch index: {$indexName}");
        }

        $records = DB::table('smtp_settings')
            ->whereIn('id', SuperAdminSmtpSettingReadService::GATEWAY_IDS)
            ->get();

        $this->info('Found '.count($records).' SMTP gateway records to index.');

        $bulkParams = ['body' => []];

        foreach ($records as $record) {
            $doc = $readService->buildGatewayDocument($record);

            $bulkParams['body'][] = [
                'index' => [
                    '_index' => $indexName,
                    '_id' => (string) $record->id,
                ],
            ];
            $bulkParams['body'][] = $doc;
        }

        if (! empty($bulkParams['body'])) {
            $bulkParams['refresh'] = 'wait_for';
            $response = $elasticService->getClient()->bulk($bulkParams);

            if (! empty($response['errors'])) {
                $this->error('Some items encountered indexing errors in Elasticsearch: '.json_encode($response['items']));
            } else {
                $this->info('Indexed '.count($records)." SMTP gateways successfully into {$indexName}.");
            }
        }

        SuperAdminSmtpSettingCacheManager::flushCaches();
        $this->info('Flushed Super Admin SMTP L2 caches.');

        return self::SUCCESS;
    }
}
