<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class ForwardAttachment
 *
 * Intent: Tracks ephemeral client-compiled candidate resume PDFs uploaded directly to Cloudflare R2.
 * Tenancy: Enforces company_id scoping via scopeForCompany.
 */
class ForwardAttachment extends Model
{
    protected $table = 'forward_attachments';

    protected $fillable = [
        'uuid',
        'forward_request_id',
        'company_id',
        'candidate_id',
        'r2_object_key',
        'filename',
        'file_size_bytes',
        'mime_type',
        'expected_sha256',
        'expected_size',
        'status',
        'verified_at',
        'failed_at',
        'expires_at',
        'deleted_at',
    ];

    protected $casts = [
        'file_size_bytes' => 'integer',
        'expected_size' => 'integer',
        'verified_at' => 'datetime',
        'failed_at' => 'datetime',
        'expires_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Tenant scoping: Always filter by company_id (CLAUDE.md rule 3)
     */
    public function scopeForCompany($query, int|string $companyId)
    {
        return $query->where('company_id', (int) $companyId);
    }

    public function scopeByUuid($query, string $uuid)
    {
        return $query->where('uuid', $uuid);
    }

    public function forwardRequest(): BelongsTo
    {
        return $this->belongsTo(ForwardRequest::class, 'forward_request_id');
    }
}
