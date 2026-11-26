<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * ProductSubcategory — Master & Tenant-Scoped Sales CRM Product Subcategories.
 *
 * Intent: Child classification under ProductCategory for granular Sales CRM catalog filtering.
 */
class ProductSubcategory extends Model
{
    protected $table = 'product_subcategories';

    protected $guarded = ['id'];

    /**
     * Parent category relationship.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    /**
     * Scope subcategories visible to the given tenant company.
     */
    public function scopeForCompany($query, int $companyId)
    {
        return $query->where('status', 1)
            ->where(function ($q) use ($companyId) {
                $q->where('company_id', $companyId)
                    ->orWhereNull('company_id');
            });
    }
}
