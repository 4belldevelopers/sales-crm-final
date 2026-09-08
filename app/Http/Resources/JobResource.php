<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * JobResource — API & AJAX JSON Transformer for Job Requisitions.
 *
 * Intent: Provides lightweight, standardized JSON serialization for Jobs API endpoints,
 * reducing network payload size by eliminating unneeded raw model properties.
 *
 * Tenancy Note: Tenant ID (company_id) is included for verification.
 *
 * Author: Antigravity AI
 * Date: August 14, 2026
 */
class JobResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'company_id' => $this->company_id,
            'client_id' => $this->client_id,
            'title' => $this->title,
            'slug' => $this->slug,
            'status' => $this->status,
            'total_positions' => $this->total_positions,
            'employment_type' => $this->employment_type,
            'min_exp' => $this->min_exp,
            'max_exp' => $this->max_exp,
            'min_salary' => $this->min_salary,
            'max_salary' => $this->max_salary,
            'min_salary_currency' => $this->min_salary_currency,
            'max_salary_currency' => $this->max_salary_currency,
            'work_type' => $this->work_type,
            'work_shifts' => $this->work_shifts,
            'start_date' => $this->start_date ? date('Y-m-d', strtotime($this->start_date)) : null,
            'end_date' => $this->end_date ? date('Y-m-d', strtotime($this->end_date)) : null,
            'created_at' => $this->created_at ? $this->created_at->toIso8601String() : null,
            'updated_at' => $this->updated_at ? $this->updated_at->toIso8601String() : null,
        ];
    }
}
