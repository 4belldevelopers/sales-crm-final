<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * ProductCategory — Master & Tenant-Scoped Sales CRM Product Categories.
 *
 * Intent: Provides global default categories (company_id IS NULL) and tenant-custom categories.
 */
class ProductCategory extends Model
{
    protected $table = 'product_categories';

    protected $guarded = ['id'];

    /**
     * Subcategories belonging to this category.
     */
    public function subcategories(): HasMany
    {
        return $this->hasMany(ProductSubcategory::class, 'category_id');
    }

    /**
     * Scope categories visible to the given tenant company.
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
