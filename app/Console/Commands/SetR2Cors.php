<?php

/**
 * One-time setup: configure Cloudflare R2 bucket CORS to allow direct PUT
 * uploads from the browser. Must be run once after deployment.
 *
 * Usage:  php artisan r2:set-cors
 */

namespace App\Console\Commands;

use Aws\S3\S3Client;
use Illuminate\Console\Command;

class SetR2Cors extends Command
{
    protected $signature   = 'r2:set-cors';
    protected $description = 'Set Cloudflare R2 bucket CORS rules to allow direct browser uploads';

    public function handle(): int
    {
        $disk = config('filesystems.disks.r2');

        $client = new S3Client([
            'version'                 => 'latest',
            'region'                  => 'auto',
            'endpoint'                => $disk['endpoint'],
            'use_path_style_endpoint' => true,
            'credentials'             => [
                'key'    => $disk['key'],
                'secret' => $disk['secret'],
            ],
        ]);

        $appUrl = rtrim(config('app.url'), '/');

        try {
            $client->putBucketCors([
                'Bucket' => $disk['bucket'],
                'CORSConfiguration' => [
                    'CORSRules' => [
                        [
                            'AllowedHeaders' => ['Content-Type', 'Content-Length', 'x-amz-*'],
                            'AllowedMethods' => ['PUT', 'GET'],
                            'AllowedOrigins' => [$appUrl, 'http://localhost', 'http://localhost:*'],
                            'ExposeHeaders'  => ['ETag'],
                            'MaxAgeSeconds'  => 3600,
                        ],
                    ],
                ],
            ]);

            $this->info("R2 CORS configured successfully for origin: {$appUrl}");
            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Failed to set CORS: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
