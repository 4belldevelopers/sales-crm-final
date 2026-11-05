<?php

namespace App\Console\Commands;

use App\Services\ElasticService;
use App\Services\SuperAdmin\TransactionHistoryCacheManager;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Class ReindexTransactionsToElasticCommand
 *
 * Intent: Authoritatively synchronizes and denormalizes all MySQL cart transactions,
 * buyer corporate billing records, and tax invoice metadata into Elasticsearch index
 * ELASTIC_CART_TRANSACTION_HISTORY (local_cart_transaction_history), ensuring 100% strict CQRS
 * reads and purging all L2 Redis/Dragonfly caches.
 *
 * Signature: php artisan transactions:reindex {--force : Delete and recreate index with fresh schema}
 *
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class ReindexTransactionsToElasticCommand extends Command
{
    protected $signature = 'transactions:reindex {--force : Force recreate index mapping}';

    protected $description = 'Reindex all cart transactions and billing records into Elasticsearch with complete denormalization';

    public function handle(ElasticService $elasticService): int
    {
        $this->info('Starting Transaction History Elasticsearch reindexing...');

        $index = config('constants.ELASTIC_CART_TRANSACTION_HISTORY', 'local_cart_transaction_history');

        $force = $this->option('force');

        if ($force && $elasticService->indexExists($index)) {
            $this->warn("Index {$index} exists. Force flag active: deleting old index...");
            $elasticService->getClient()->indices()->delete(['index' => $index]);
        }

        if (! $elasticService->indexExists($index)) {
            $this->info("Creating index {$index} with complete enterprise mappings...");

            $elasticService->getClient()->indices()->create([
                'index' => $index,
                'body' => [
                    'settings' => [
                        'number_of_shards' => 1,
                        'number_of_replicas' => 0,
                        'index.requests.cache.enable' => true,
                    ],
                    'mappings' => [
                        'properties' => [
                            'id' => ['type' => 'long'],
                            'company_id' => ['type' => 'long'],
                            'user_id' => ['type' => 'long'],
                            'cart_id' => ['type' => 'long'],
                            'pck_top_id' => ['type' => 'long'],
                            'subscription_type' => ['type' => 'keyword'],
                            'event_type' => ['type' => 'keyword'],
                            'product_name' => [
                                'type' => 'text',
                                'fields' => [
                                    'keyword' => ['type' => 'keyword', 'ignore_above' => 256],
                                ],
                            ],
                            'duration' => ['type' => 'text'],
                            'credit' => ['type' => 'double'],
                            'currency' => [
                                'type' => 'text',
                                'fields' => [
                                    'keyword' => ['type' => 'keyword', 'ignore_above' => 64],
                                ],
                            ],
                            'price' => ['type' => 'double'],
                            'gst' => ['type' => 'double'],
                            'gst_amount' => ['type' => 'double'],
                            'total_price' => ['type' => 'double'],
                            'status' => ['type' => 'keyword'],
                            'payment_orderId' => [
                                'type' => 'text',
                                'fields' => [
                                    'keyword' => ['type' => 'keyword', 'ignore_above' => 256],
                                ],
                            ],
                            'method' => ['type' => 'keyword'],
                            'international' => ['type' => 'integer'],
                            'bank' => [
                                'type' => 'text',
                                'fields' => [
                                    'keyword' => ['type' => 'keyword', 'ignore_above' => 256],
                                ],
                            ],
                            'payment_Id' => [
                                'type' => 'text',
                                'fields' => [
                                    'keyword' => ['type' => 'keyword', 'ignore_above' => 256],
                                ],
                            ],
                            'payment_gateway' => ['type' => 'keyword'],
                            'payment_mode' => [
                                'type' => 'text',
                                'fields' => [
                                    'keyword' => ['type' => 'keyword', 'ignore_above' => 128],
                                ],
                            ],
                            'chequenumber' => [
                                'type' => 'text',
                                'fields' => [
                                    'keyword' => ['type' => 'keyword', 'ignore_above' => 128],
                                ],
                            ],
                            'chequedate' => ['type' => 'date'],
                            'bankname' => [
                                'type' => 'text',
                                'fields' => [
                                    'keyword' => ['type' => 'keyword', 'ignore_above' => 256],
                                ],
                            ],
                            'transactionid' => [
                                'type' => 'text',
                                'fields' => [
                                    'keyword' => ['type' => 'keyword', 'ignore_above' => 256],
                                ],
                            ],
                            'bankreference' => [
                                'type' => 'text',
                                'fields' => [
                                    'keyword' => ['type' => 'keyword', 'ignore_above' => 256],
                                ],
                            ],
                            'payment_link_id' => [
                                'type' => 'text',
                                'fields' => [
                                    'keyword' => ['type' => 'keyword', 'ignore_above' => 256],
                                ],
                            ],
                            'invoiceId' => [
                                'type' => 'text',
                                'fields' => [
                                    'keyword' => ['type' => 'keyword', 'ignore_above' => 256],
                                ],
                            ],
                            'invoice_date' => ['type' => 'date'],
                            'invoice_approval' => ['type' => 'keyword'],
                            'approval_invoice_no' => [
                                'type' => 'text',
                                'fields' => [
                                    'keyword' => ['type' => 'keyword', 'ignore_above' => 256],
                                ],
                            ],
                            'approval_invoice_date' => ['type' => 'date'],
                            'company_name' => [
                                'type' => 'text',
                                'fields' => [
                                    'keyword' => ['type' => 'keyword', 'ignore_above' => 256],
                                ],
                            ],
                            'company_logo' => ['type' => 'keyword', 'index' => false],
                            'company_email' => ['type' => 'keyword'],
                            'company_phone' => ['type' => 'keyword'],
                            'organization' => [
                                'type' => 'text',
                                'fields' => [
                                    'keyword' => ['type' => 'keyword', 'ignore_above' => 256],
                                ],
                            ],
                            'billing_first_name' => ['type' => 'text'],
                            'billing_last_name' => ['type' => 'text'],
                            'billing_phone' => ['type' => 'keyword'],
                            'billing_building' => ['type' => 'text'],
                            'billing_street' => ['type' => 'text'],
                            'billing_postal_code' => ['type' => 'keyword'],
                            'gstin' => [
                                'type' => 'text',
                                'fields' => [
                                    'keyword' => ['type' => 'keyword', 'ignore_above' => 64],
                                ],
                            ],
                            'pan_no' => [
                                'type' => 'text',
                                'fields' => [
                                    'keyword' => ['type' => 'keyword', 'ignore_above' => 64],
                                ],
                            ],
                            'sold_organization' => [
                                'type' => 'text',
                                'fields' => [
                                    'keyword' => ['type' => 'keyword', 'ignore_above' => 256],
                                ],
                            ],
                            'sold_gstin' => [
                                'type' => 'text',
                                'fields' => [
                                    'keyword' => ['type' => 'keyword', 'ignore_above' => 64],
                                ],
                            ],
                            'sold_pan_no' => [
                                'type' => 'text',
                                'fields' => [
                                    'keyword' => ['type' => 'keyword', 'ignore_above' => 64],
                                ],
                            ],
                            'sold_phone' => ['type' => 'keyword'],
                            'sold_street' => ['type' => 'text'],
                            'sold_building' => ['type' => 'text'],
                            'sold_postal_code' => ['type' => 'keyword'],
                            'country_name' => [
                                'type' => 'text',
                                'fields' => [
                                    'keyword' => ['type' => 'keyword', 'ignore_above' => 128],
                                ],
                            ],
                            'state' => [
                                'type' => 'text',
                                'fields' => [
                                    'keyword' => ['type' => 'keyword', 'ignore_above' => 128],
                                ],
                            ],
                            'city' => [
                                'type' => 'text',
                                'fields' => [
                                    'keyword' => ['type' => 'keyword', 'ignore_above' => 128],
                                ],
                            ],
                            'created_at' => ['type' => 'date'],
                            'updated_at' => ['type' => 'date'],
                        ],
                    ],
                ],
            ]);
        }

        $query = DB::table('cart_transaction_history')
            ->leftJoin('companies', 'companies.id', '=', 'cart_transaction_history.company_id')
            ->leftJoin('billing_address_by_orders', 'billing_address_by_orders.cart_transaction_id', '=', 'cart_transaction_history.id')
            ->leftJoin('countries', 'countries.id', '=', 'billing_address_by_orders.country_id')
            ->leftJoin('state_list', 'state_list.id', '=', 'billing_address_by_orders.state_id')
            ->leftJoin('city_list', 'city_list.city_id', '=', 'billing_address_by_orders.city')
            ->select([
                'cart_transaction_history.*',
                'companies.company_name',
                'companies.logo as company_logo',
                'companies.company_email',
                'companies.company_phone',
                'billing_address_by_orders.organization',
                'billing_address_by_orders.first_name as billing_first_name',
                'billing_address_by_orders.last_name as billing_last_name',
                'billing_address_by_orders.phone as billing_phone',
                'billing_address_by_orders.building as billing_building',
                'billing_address_by_orders.street as billing_street',
                'billing_address_by_orders.postal_code as billing_postal_code',
                'billing_address_by_orders.gstin',
                'billing_address_by_orders.pan_no',
                'billing_address_by_orders.sold_organization',
                'billing_address_by_orders.sold_gstin',
                'billing_address_by_orders.sold_pan_no',
                'billing_address_by_orders.sold_phone',
                'billing_address_by_orders.sold_street',
                'billing_address_by_orders.sold_building',
                'billing_address_by_orders.sold_postal_code',
                'countries.country_name',
                'state_list.state',
                'city_list.city',
            ]);

        $totalCount = $query->count();
        $this->info("Found {$totalCount} transaction(s) to index.");

        if ($totalCount === 0) {
            $this->info('Zero transactions to index. Reindex complete.');
            TransactionHistoryCacheManager::flushCaches();

            return self::SUCCESS;
        }

        $synced = 0;
        $batchSize = 250;

        $query->orderBy('cart_transaction_history.id', 'asc')
            ->chunk($batchSize, function ($transactions) use ($elasticService, $index, &$synced) {
                $params = ['body' => []];

                foreach ($transactions as $tx) {
                    $doc = [
                        'id' => (int) $tx->id,
                        'company_id' => $tx->company_id ? (int) $tx->company_id : null,
                        'user_id' => $tx->user_id ? (int) $tx->user_id : null,
                        'cart_id' => $tx->cart_id ? (int) $tx->cart_id : null,
                        'pck_top_id' => $tx->pck_top_id ? (int) $tx->pck_top_id : null,
                        'subscription_type' => (string) ($tx->subscription_type ?? 'yes'),
                        'event_type' => (string) ($tx->event_type ?? 'operational'),
                        'product_name' => (string) ($tx->product_name ?? ''),
                        'duration' => (string) ($tx->duration ?? ''),
                        'credit' => (float) ($tx->credit ?? 0),
                        'currency' => (string) ($tx->currency ?? 'INR'),
                        'price' => (float) ($tx->price ?? 0),
                        'gst' => (float) ($tx->gst ?? 0),
                        'gst_amount' => (float) ($tx->gst_amount ?? 0),
                        'total_price' => (float) ($tx->total_price ?? 0),
                        'status' => (string) ($tx->status ?? 'captured'),
                        'payment_orderId' => (string) ($tx->payment_orderId ?? ''),
                        'method' => (string) ($tx->method ?? ''),
                        'international' => (int) ($tx->international ?? 0),
                        'bank' => (string) ($tx->bank ?? ''),
                        'payment_Id' => (string) ($tx->payment_Id ?? ''),
                        'payment_gateway' => (string) ($tx->payment_gateway ?? ''),
                        'payment_mode' => (string) ($tx->payment_mode ?? 'Online'),
                        'chequenumber' => (string) ($tx->chequenumber ?? ''),
                        'chequedate' => $tx->chequedate ? Carbon::parse($tx->chequedate)->format('Y-m-d') : null,
                        'bankname' => (string) ($tx->bankname ?? ''),
                        'transactionid' => (string) ($tx->transactionid ?? ''),
                        'bankreference' => (string) ($tx->bankreference ?? ''),
                        'payment_link_id' => (string) ($tx->payment_link_id ?? ''),
                        'invoiceId' => (string) ($tx->invoiceId ?? ''),
                        'invoice_date' => $tx->invoice_date ? Carbon::parse($tx->invoice_date)->toIso8601String() : null,
                        'invoice_approval' => (string) ($tx->invoice_approval ?? 'No'),
                        'approval_invoice_no' => (string) ($tx->approval_invoice_no ?? ''),
                        'approval_invoice_date' => $tx->approval_invoice_date ? Carbon::parse($tx->approval_invoice_date)->toIso8601String() : null,
                        'company_name' => (string) ($tx->company_name ?? ''),
                        'company_logo' => (string) ($tx->company_logo ?? ''),
                        'company_email' => (string) ($tx->company_email ?? ''),
                        'company_phone' => (string) ($tx->company_phone ?? ''),
                        'organization' => (string) ($tx->organization ?? ($tx->company_name ?? '')),
                        'billing_first_name' => (string) ($tx->billing_first_name ?? ''),
                        'billing_last_name' => (string) ($tx->billing_last_name ?? ''),
                        'billing_phone' => (string) ($tx->billing_phone ?? ''),
                        'billing_building' => (string) ($tx->billing_building ?? ''),
                        'billing_street' => (string) ($tx->billing_street ?? ''),
                        'billing_postal_code' => (string) ($tx->billing_postal_code ?? ''),
                        'gstin' => (string) ($tx->gstin ?? ''),
                        'pan_no' => (string) ($tx->pan_no ?? ''),
                        'sold_organization' => (string) ($tx->sold_organization ?? 'JERO'),
                        'sold_gstin' => (string) ($tx->sold_gstin ?? ''),
                        'sold_pan_no' => (string) ($tx->sold_pan_no ?? ''),
                        'sold_phone' => (string) ($tx->sold_phone ?? ''),
                        'sold_street' => (string) ($tx->sold_street ?? ''),
                        'sold_building' => (string) ($tx->sold_building ?? ''),
                        'sold_postal_code' => (string) ($tx->sold_postal_code ?? ''),
                        'country_name' => (string) ($tx->country_name ?? ''),
                        'state' => (string) ($tx->state ?? ''),
                        'city' => (string) ($tx->city ?? ''),
                        'created_at' => $tx->created_at ? Carbon::parse($tx->created_at)->toIso8601String() : null,
                        'updated_at' => $tx->updated_at ? Carbon::parse($tx->updated_at)->toIso8601String() : null,
                    ];

                    $params['body'][] = [
                        'index' => [
                            '_index' => $index,
                            '_id' => (string) $tx->id,
                        ],
                    ];
                    $params['body'][] = $doc;

                    $synced++;
                }

                if (! empty($params['body'])) {
                    $elasticService->getClient()->bulk($params);
                }
            });

        // Flush Redis/Dragonfly L2 caches
        TransactionHistoryCacheManager::flushCaches();

        $this->info("Successfully indexed {$synced} transaction(s) into {$index} with complete cache eviction.");

        return self::SUCCESS;
    }
}
