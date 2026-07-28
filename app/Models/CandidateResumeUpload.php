<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CandidateResumeUpload extends Model
{
    protected $fillable = [
        'candidate_id', 'company_id', 'object_key', 'original_filename',
        'mime_type', 'file_size', 'uploaded_by', 'status', 'error_log',
        'parsed_data', 'resume_text',
    ];

    protected $casts = [
        'parsed_data' => 'array',
    ];
}
