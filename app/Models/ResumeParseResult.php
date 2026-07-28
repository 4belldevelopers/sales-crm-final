<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// Intent: Stores the structured payload produced by ParseResumeJob.
// One row per upload session (INV-5: parser_attempts capped at 1).

class ResumeParseResult extends Model
{
    protected $table = 'resume_parse_results';

    protected $fillable = [
        'resume_upload_id',
        'company_id',
        'payload',
        'extractor_version',
        'extracted_at',
    ];

    protected $casts = [
        'payload'      => 'array',
        'extracted_at' => 'datetime',
    ];

    public function upload(): BelongsTo
    {
        return $this->belongsTo(ResumeUpload::class, 'resume_upload_id');
    }
}
