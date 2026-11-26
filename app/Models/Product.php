<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Product — Sales CRM Product Catalog Entity.
 *
 * Intent: Stores physical, digital, and service products for Deals, Quotations,
 *         Sales Orders, and Invoices with strict multi-tenant company_id isolation.
 * Tenancy Note: Global scope enforces company_id on all authenticated non-superadmin queries.
 */
class Product extends Model
{
    use SoftDeletes;

    protected $table = 'products';

    protected $guarded = ['id'];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'mrp' => 'decimal:2',
        'discount_value' => 'decimal:2',
        'tax_percentage' => 'decimal:2',
        'final_selling_price' => 'decimal:2',
        'profit_margin' => 'decimal:2',
        'track_inventory' => 'boolean',
        'opening_stock' => 'integer',
        'available_quantity' => 'integer',
        'min_stock_alert' => 'integer',
        'is_featured' => 'boolean',
        'specifications' => 'array',
        'tags' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Enforce strict tenant company_id isolation on all queries.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::addGlobalScope('company', function (Builder $builder) {
            if (auth()->check() && ! auth()->user()->is_superadmin) {
                $builder->where('products.company_id', auth()->user()->company_id);
            }
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(ProductSubcategory::class, 'subcategory_id');
    }

    /**
     * Only active products are eligible for new Deals, Quotations, Sales Orders, and Invoices.
     */
    public function scopeAvailableForSale(Builder $query): Builder
    {
        return $query->where('status', 'active')
            ->where('availability', '!=', 'discontinued');
    }

    /**
     * Authoritative server-side pricing, discount, GST, and profit margin calculation.
     *
     * @return array{discounted_base: float, tax_amount: float, final_selling_price: float, profit_margin: float}
     */
    public static function calculatePricing(
        float $purchasePrice,
        float $sellingPrice,
        string $discountType,
        float $discountValue,
        float $taxPercentage
    ): array {
        $purchase = max(0.0, $purchasePrice);
        $selling = max(0.0, $sellingPrice);
        $dVal = max(0.0, $discountValue);
        $taxPct = max(0.0, min(100.0, $taxPercentage));

        $discountAmount = $discountType === 'percentage'
            ? ($selling * min($dVal, 100.0) / 100.0)
            : min($dVal, $selling);

        $discountedBase = max(0.0, $selling - $discountAmount);
        $taxAmount = $discountedBase * ($taxPct / 100.0);
        $finalSellingPrice = round($discountedBase + $taxAmount, 2);
        $profitMargin = round($discountedBase - $purchase, 2);

        return [
            'discounted_base' => round($discountedBase, 2),
            'tax_amount' => round($taxAmount, 2),
            'final_selling_price' => $finalSellingPrice,
            'profit_margin' => $profitMargin,
        ];
    }
}
