<?php

namespace App\Services;

use Aws\S3\S3Client;
use Illuminate\Support\Str;

/**
 * Generates short-lived pre-signed PUT URLs so the browser can upload directly
 * to Cloudflare R2 without passing bytes through PHP.
 */
class R2PresignService
{
    private S3Client $client;
    private string   $bucket;

    public function __construct()
    {
        $disk = config('filesystems.disks.r2');

        $this->bucket = $disk['bucket'];
        $this->client = new S3Client([
            'version'                 => 'latest',
            'region'                  => 'auto',
            'endpoint'                => $disk['endpoint'],
            'use_path_style_endpoint' => true,
            'credentials'             => [
                'key'    => $disk['key'],
                'secret' => $disk['secret'],
            ],
            'signature_version' => 'v4',
        ]);
    }

    /**
     * @param  string $filename      Original filename from the browser
     * @param  string $contentType   MIME type (e.g. application/pdf)
     * @param  int    $expirySeconds How long the URL stays valid (default 10 min)
     * @return array{upload_url: string, object_key: string, public_url: string, expires_in: int}
     */
    public function generateUploadUrl(string $filename, string $contentType, int $expirySeconds = 600): array
    {
        $ext       = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $objectKey = 'temp/' . Str::uuid() . '.' . $ext;

        $cmd = $this->client->getCommand('PutObject', [
            'Bucket'      => $this->bucket,
            'Key'         => $objectKey,
            'ContentType' => $contentType,
        ]);

        $presigned  = $this->client->createPresignedRequest($cmd, '+' . $expirySeconds . ' seconds');
        $uploadUrl  = (string) $presigned->getUri();
        $publicUrl  = 'https://file.jero.works/' . $objectKey;

        return [
            'upload_url' => $uploadUrl,
            'object_key' => $objectKey,
            'public_url' => $publicUrl,
            'expires_in' => $expirySeconds,
        ];
    }
}
