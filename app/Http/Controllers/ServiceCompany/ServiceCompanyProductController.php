<?php

namespace App\Http\Controllers\ServiceCompany;

use App\Helper\GeneralMethod;
use App\Http\Requests\StoreProductRequest;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductSubcategory;
use App\Services\CurrencyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * ServiceCompanyProductController — Sales CRM Product Management.
 *
 * Intent: Handles multi-tenant product catalog CRUD, SKU uniqueness checks,
 *         category/subcategory loading, pricing/GST/margin calculations,
 *         image uploads, and active catalog feeds for Deals/Quotations/Orders/Invoices.
 * Tenancy Note: Every query is strictly scoped to the authenticated user's company_id.
 */
class ServiceCompanyProductController extends ServiceCompanyBaseController
{
    /**
     * List company products (JSON or catalog view).
     */
    public function index(Request $request)
    {
        $companyId = (int) auth()->user()->company_id;
        $products = Product::with(['category:id,name', 'subcategory:id,name'])
            ->where('company_id', $companyId)
            ->orderByDesc('id')
            ->paginate(20);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['status' => 'success', 'data' => $products]);
        }

        $this->pageTitle = __('Products');
        $this->products = $products;

        return view('service_company.products.create', array_merge($this->data, [
            'categories' => ProductCategory::forCompany($companyId)->orderBy('name')->get(),
            'currency' => GeneralMethod::currency(),
            'currency_code' => CurrencyService::getTenantCurrency($companyId),
        ]));
    }

    /**
     * Show the Sales CRM Add Product form.
     */
    public function create()
    {
        $this->pageTitle = __('Add Product');
        $companyId = (int) (auth()->user()->company_id ?? 0);

        $this->currency = GeneralMethod::currency();
        $this->currency_code = $companyId > 0
            ? CurrencyService::getTenantCurrency($companyId)
            : GeneralMethod::defaultCurrency();
        $this->categories = ProductCategory::forCompany($companyId)->orderBy('name')->get();
        $this->subcategories = collect();
        $this->product = null;
        $this->page_event = 'ADD';

        return view('service_company.products.create', $this->data);
    }

    /**
     * Store a newly created Sales CRM product in MySQL within a transaction.
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        $user = auth()->user();
        $companyId = (int) $user->company_id;

        $product = DB::transaction(function () use ($request, $companyId, $user) {
            $payload = $this->buildProductPayload($request, $companyId, (int) $user->id, true);

            return Product::create($payload);
        });

        return response()->json([
            'status' => 'success',
            'message' => $product->status === 'draft'
                ? 'Product saved as Draft successfully.'
                : 'Product created successfully.',
            'product' => $product->load(['category:id,name', 'subcategory:id,name']),
        ], 201);
    }

    /**
     * Show the Sales CRM Edit Product form.
     */
    public function edit($id)
    {
        $companyId = (int) auth()->user()->company_id;
        $product = Product::where('company_id', $companyId)->findOrFail($id);

        $this->pageTitle = __('Edit Product');
        $this->currency = GeneralMethod::currency();
        $this->currency_code = CurrencyService::getTenantCurrency($companyId);
        $this->categories = ProductCategory::forCompany($companyId)->orderBy('name')->get();
        $this->subcategories = $product->category_id
            ? ProductSubcategory::forCompany($companyId)->where('category_id', $product->category_id)->orderBy('name')->get()
            : collect();
        $this->product = $product;
        $this->page_event = 'EDIT';

        return view('service_company.products.create', $this->data);
    }

    /**
     * Update an existing Sales CRM product within a transaction.
     */
    public function update(StoreProductRequest $request, $id): JsonResponse
    {
        $user = auth()->user();
        $companyId = (int) $user->company_id;
        $product = Product::where('company_id', $companyId)->findOrFail($id);

        DB::transaction(function () use ($request, $product, $companyId, $user) {
            $payload = $this->buildProductPayload($request, $companyId, (int) $user->id, false, $product);
            $product->update($payload);
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Product updated successfully.',
            'product' => $product->fresh(['category:id,name', 'subcategory:id,name']),
        ]);
    }

    /**
     * Soft-delete a company product.
     */
    public function destroy($id): JsonResponse
    {
        $companyId = (int) auth()->user()->company_id;
        $product = Product::where('company_id', $companyId)->findOrFail($id);
        $product->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Product deleted successfully.',
        ]);
    }

    /**
     * AJAX: Return subcategories for a given category (tenant-scoped).
     */
    public function subcategories($categoryId): JsonResponse
    {
        $companyId = (int) auth()->user()->company_id;
        $items = ProductSubcategory::forCompany($companyId)
            ->where('category_id', (int) $categoryId)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json(['status' => 'success', 'data' => $items]);
    }

    /**
     * AJAX: Check if SKU is already taken within the authenticated company.
     */
    public function checkSku(Request $request): JsonResponse
    {
        $companyId = (int) auth()->user()->company_id;
        $sku = strtoupper(trim((string) $request->input('sku', '')));
        $excludeId = (int) $request->input('exclude_id', 0);

        if ($sku === '') {
            return response()->json(['available' => false, 'message' => 'SKU is required.']);
        }

        $exists = Product::where('company_id', $companyId)
            ->where('sku', $sku)
            ->when($excludeId > 0, fn ($q) => $q->where('id', '!=', $excludeId))
            ->exists();

        return response()->json([
            'available' => ! $exists,
            'message' => $exists ? 'This SKU already exists in your company catalog.' : 'SKU is available.',
        ]);
    }

    /**
     * JSON feed of active, non-discontinued products for Deals, Quotations, Sales Orders, and Invoices.
     */
    public function activeCatalog(Request $request): JsonResponse
    {
        $companyId = (int) auth()->user()->company_id;
        $search = trim((string) $request->input('q', ''));

        $products = Product::where('company_id', $companyId)
            ->availableForSale()
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', '%'.$search.'%')
                        ->orWhere('sku', 'like', '%'.$search.'%');
                });
            })
            ->orderBy('name')
            ->limit(50)
            ->get([
                'id', 'name', 'sku', 'product_type', 'currency',
                'selling_price', 'discount_type', 'discount_value',
                'tax_percentage', 'final_selling_price', 'hsn_sac_code',
                'unit', 'available_quantity', 'stock_status',
            ]);

        return response()->json(['status' => 'success', 'data' => $products]);
    }

    /**
     * Assemble normalized, server-calculated product attributes from the validated request.
     */
    private function buildProductPayload(
        StoreProductRequest $request,
        int $companyId,
        int $userId,
        bool $isCreate,
        ?Product $existing = null
    ): array {
        $purchasePrice = (float) ($request->input('purchase_price') ?? 0);
        $sellingPrice = (float) ($request->input('selling_price') ?? 0);
        $discountType = (string) ($request->input('discount_type') ?? 'percentage');
        $discountValue = (float) ($request->input('discount_value') ?? 0);
        $taxPercentage = (float) ($request->input('tax_percentage') ?? 0);

        $pricing = Product::calculatePricing(
            $purchasePrice,
            $sellingPrice,
            $discountType,
            $discountValue,
            $taxPercentage
        );

        $productType = (string) $request->input('product_type', 'physical');
        $trackInventory = $productType === 'physical'
            ? true
            : (bool) $request->boolean('track_inventory', false);

        $openingStock = $trackInventory ? max(0, (int) ($request->input('opening_stock') ?? 0)) : 0;
        $availableQty = $trackInventory ? max(0, (int) ($request->input('available_quantity') ?? $openingStock)) : 0;
        $minStockAlert = $trackInventory ? max(0, (int) ($request->input('min_stock_alert') ?? 0)) : 0;

        $stockStatus = (string) ($request->input('stock_status') ?? 'in_stock');
        if (! $trackInventory) {
            $stockStatus = 'not_applicable';
        } elseif ($availableQty === 0) {
            $stockStatus = 'out_of_stock';
        } elseif ($minStockAlert > 0 && $availableQty <= $minStockAlert) {
            $stockStatus = 'low_stock';
        }

        // Handle optional product image upload
        $imagePath = $existing?->image_path;
        if ($request->hasFile('image')) {
            $uploadDir = public_path('user-uploads/products/'.$companyId);
            if (! File::isDirectory($uploadDir)) {
                File::makeDirectory($uploadDir, 0755, true);
            }
            $file = $request->file('image');
            $filename = Str::uuid()->toString().'.'.$file->getClientOriginalExtension();
            $file->move($uploadDir, $filename);
            $imagePath = 'user-uploads/products/'.$companyId.'/'.$filename;
        }

        $specs = $request->input('specifications');
        if (is_string($specs)) {
            $specs = json_decode($specs, true) ?: [];
        }
        $tags = $request->input('tags');
        if (is_string($tags)) {
            $tags = json_decode($tags, true) ?: [];
        }

        $data = [
            'company_id' => $companyId,
            'updated_by' => $userId,
            'name' => trim((string) $request->input('name')),
            'sku' => strtoupper(trim((string) $request->input('sku'))),
            'category_id' => $request->filled('category_id') ? (int) $request->input('category_id') : null,
            'subcategory_id' => $request->filled('subcategory_id') ? (int) $request->input('subcategory_id') : null,
            'product_type' => $productType,
            'brand' => $request->input('brand'),
            'description' => $request->input('description'),
            'image_path' => $imagePath,

            'currency' => strtoupper((string) $request->input('currency', 'INR')),
            'purchase_price' => $purchasePrice,
            'selling_price' => $sellingPrice,
            'mrp' => $request->filled('mrp') ? (float) $request->input('mrp') : null,
            'discount_type' => $discountType,
            'discount_value' => $discountValue,
            'tax_percentage' => $taxPercentage,
            'hsn_sac_code' => $request->input('hsn_sac_code'),
            'final_selling_price' => $pricing['final_selling_price'],
            'profit_margin' => $pricing['profit_margin'],

            'track_inventory' => $trackInventory,
            'opening_stock' => $openingStock,
            'available_quantity' => $availableQty,
            'min_stock_alert' => $minStockAlert,
            'unit' => (string) ($request->input('unit') ?? 'piece'),
            'warehouse_location' => $productType === 'physical' ? $request->input('warehouse_location') : null,
            'stock_status' => $stockStatus,

            'model_number' => $request->input('model_number'),
            'barcode' => $request->input('barcode'),
            'warranty_period' => $request->input('warranty_period'),
            'weight' => $productType === 'physical' ? $request->input('weight') : null,
            'dimensions' => $productType === 'physical' ? $request->input('dimensions') : null,
            'features' => $request->input('features'),
            'specifications' => is_array($specs) ? $specs : [],
            'tags' => is_array($tags) ? $tags : [],

            'status' => (string) $request->input('status', 'draft'),
            'visibility' => (string) $request->input('visibility', 'public'),
            'is_featured' => $request->boolean('is_featured', false),
            'availability' => (string) $request->input('availability', 'available'),
            'start_date' => $request->input('start_date') ?: null,
            'end_date' => $request->input('end_date') ?: null,
        ];

        if ($isCreate) {
            $data['created_by'] = $userId;
        }

        return $data;
    }
}
