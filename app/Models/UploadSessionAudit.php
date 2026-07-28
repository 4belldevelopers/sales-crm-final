<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// Intent: Immutable audit log for every state transition in the upload
// lifecycle. Append-only — never updated or deleted after insert.

class UploadSessionAudit extends Model
{
    protected $table = 'upload_session_audits';

    protected $fillable = [
        'resume_upload_id',
        'company_id',
        'user_id',
        'action',
        'payload',
        'request_id',
        'pod_id',
        'error_code',
        'message_safe',
        'ip_hash',
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    public function upload(): BelongsTo
    {
        return $this->belongsTo(ResumeUpload::class, 'resume_upload_id');
    }

    /** Convenience factory — call from controllers/jobs after every state transition */
    public static function record(
        int    $resumeUploadId,
        string $action,
        array  $payload = [],
        ?int   $companyId = null,
        ?int   $userId = null,
    ): self {
        return self::create([
            'resume_upload_id' => $resumeUploadId,
            'action'           => $action,
            'payload'          => $payload,
            'company_id'       => $companyId,
            'user_id'          => $userId,
            'pod_id'           => gethostname(),
            'request_id'       => $payload['request_id'] ?? null,
            'error_code'       => $payload['error_code'] ?? null,
            'message_safe'     => $payload['message_safe'] ?? null,
            'ip_hash'          => $payload['ip_hash'] ?? null,
        ]);
    }
}
