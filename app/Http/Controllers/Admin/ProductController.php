<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with(['category', 'brand', 'media'])
            ->withCount('details')
            ->latest()
            ->filter(request(['search', 'status', 'category_id', 'brand_id']))
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/Products/Index', [
            'products' => $products,
            'filters' => request()->all('search', 'status', 'category_id', 'brand_id'),
            'categories' => Category::select('id', 'name')->active()->get(),
            'brands' => Brand::select('id', 'name')->active()->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/Products/Create', [
            'categories' => Category::active()->get(['id', 'name']),
            'brands' => Brand::active()->get(['id', 'name']),
            'conditions' => [
                ['value' => 'new', 'label' => 'New'],
                ['value' => 'used', 'label' => 'Used'],
                ['value' => 'refurbished', 'label' => 'Refurbished'],
            ],
            'stockStatuses' => [
                ['value' => 'in_stock', 'label' => 'In Stock'],
                ['value' => 'out_of_stock', 'label' => 'Out of Stock'],
                ['value' => 'on_backorder', 'label' => 'On Backorder'],
            ],
            'attributeTypes' => [
                ['value' => 'text', 'label' => 'Text'],
                ['value' => 'number', 'label' => 'Number'],
                ['value' => 'boolean', 'label' => 'Yes/No'],
                ['value' => 'date', 'label' => 'Date'],
                ['value' => 'url', 'label' => 'URL'],
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            // Basic Information
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:products,slug',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:price',
            'sku' => 'nullable|string|max:100|unique:products,sku',
            
            // Inventory
            'stock_quantity' => 'required_if:manage_stock,true|integer|min:0|nullable',
            'manage_stock' => 'boolean',
            'stock_status' => 'required|in:in_stock,out_of_stock,on_backorder',
            'condition' => 'required|in:new,used,refurbished',
            
            // Categorization
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            
            // Status & Visibility
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'order' => 'nullable|integer|min:0',
            
            // SEO
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            
            // Images
            'images' => 'nullable|array',
            'images.*' => 'image|max:5120', // 5MB max per image
            
            // Dynamic attributes
            'attributes' => 'nullable|array',
            'attributes.*.attribute_name' => 'required|string|max:100',
            'attributes.*.attribute_value' => 'required|string',
            'attributes.*.attribute_type' => 'required|in:text,number,boolean,date,url',
            'attributes.*.order' => 'nullable|integer|min:0',
        ]);
        
        try {
            DB::beginTransaction();
            
            // Create the product
            $product = Product::create([
                'name' => $validated['name'],
                'slug' => $validated['slug'],
                'description' => $validated['description'] ?? null,
                'short_description' => $validated['short_description'] ?? null,
                'price' => $validated['price'] * 100, // Store in cents
                'sale_price' => isset($validated['sale_price']) ? $validated['sale_price'] * 100 : null,
                'sku' => $validated['sku'] ?? null,
                'stock_quantity' => $validated['stock_quantity'] ?? 0,
                'manage_stock' => $validated['manage_stock'] ?? false,
                'stock_status' => $validated['stock_status'],
                'condition' => $validated['condition'],
                'category_id' => $validated['category_id'],
                'brand_id' => $validated['brand_id'] ?? null,
                'is_active' => $validated['is_active'] ?? true,
                'is_featured' => $validated['is_featured'] ?? false,
                'order' => $validated['order'] ?? 0,
                'meta_title' => $validated['meta_title'] ?? null,
                'meta_description' => $validated['meta_description'] ?? null,
            ]);
            
            // Handle image uploads
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $product->addMedia($image)
                        ->toMediaCollection('images');
                }
            }
            
            // Save dynamic attributes
            if (!empty($validated['attributes'])) {
                $attributes = collect($validated['attributes'])->map(function ($attr) {
                    return [
                        'attribute_name' => $attr['attribute_name'],
                        'attribute_value' => $attr['attribute_value'],
                        'attribute_type' => $attr['attribute_type'],
                        'order' => $attr['order'] ?? 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                })->toArray();
                
                $product->details()->createMany($attributes);
            }
            
            DB::commit();
            
            return redirect()->route('admin.products.index')
                ->with('success', 'Product created successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Delete any uploaded images if there was an error
            if (isset($product) && $product->exists) {
                $product->media()->delete();
                $product->forceDelete();
            }
            
            return back()->with('error', 'Error creating product: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $product->load([
            'category:id,name',
            'brand:id,name',
            'details',
            'media'
        ]);

        return Inertia::render('Admin/Products/Show', [
            'product' => $product,
            'images' => $product->getMedia('images')->map(function ($media) {
                return [
                    'id' => $media->id,
                    'url' => $media->getUrl(),
                    'thumb_url' => $media->getUrl('thumb'),
                    'preview_url' => $media->getUrl('preview'),
                    'name' => $media->name,
                    'file_name' => $media->file_name,
                    'size' => $media->size,
                    'mime_type' => $media->mime_type,
                    'created_at' => $media->created_at,
                ];
            }),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $product->load('details');
        
        return Inertia::render('Admin/Products/Edit', [
            'product' => array_merge($product->toArray(), [
                'price' => $product->price / 100, // Convert from cents
                'sale_price' => $product->sale_price ? $product->sale_price / 100 : null,
            ]),
            'categories' => Category::active()->get(['id', 'name']),
            'brands' => Brand::active()->get(['id', 'name']),
            'media' => $product->getMedia('images')->map(function ($media) {
                return [
                    'id' => $media->id,
                    'url' => $media->getUrl(),
                    'thumb_url' => $media->getUrl('thumb'),
                    'preview_url' => $media->getUrl('preview'),
                    'name' => $media->name,
                    'file_name' => $media->file_name,
                    'size' => $media->size,
                    'mime_type' => $media->mime_type,
                    'created_at' => $media->created_at,
                ];
            }),
            'conditions' => [
                ['value' => 'new', 'label' => 'New'],
                ['value' => 'used', 'label' => 'Used'],
                ['value' => 'refurbished', 'label' => 'Refurbished'],
            ],
            'stockStatuses' => [
                ['value' => 'in_stock', 'label' => 'In Stock'],
                ['value' => 'out_of_stock', 'label' => 'Out of Stock'],
                ['value' => 'on_backorder', 'label' => 'On Backorder'],
            ],
            'attributeTypes' => [
                ['value' => 'text', 'label' => 'Text'],
                ['value' => 'number', 'label' => 'Number'],
                ['value' => 'boolean', 'label' => 'Yes/No'],
                ['value' => 'date', 'label' => 'Date'],
                ['value' => 'url', 'label' => 'URL'],
            ],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            // Basic Information
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:products,slug,' . $product->id,
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:price',
            'sku' => 'nullable|string|max:100|unique:products,sku,' . $product->id,
            
            // Inventory
            'stock_quantity' => 'required_if:manage_stock,true|integer|min:0|nullable',
            'manage_stock' => 'boolean',
            'stock_status' => 'required|in:in_stock,out_of_stock,on_backorder',
            'condition' => 'required|in:new,used,refurbished',
            
            // Categorization
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            
            // Status & Visibility
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'order' => 'nullable|integer|min:0',
            
            // SEO
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            
            // Images
            'images' => 'nullable|array',
            'images.*' => 'image|max:5120', // 5MB max per image
            'deleted_media_ids' => 'nullable|array',
            'deleted_media_ids.*' => 'exists:media,id',
            
            // Dynamic attributes
            'attributes' => 'nullable|array',
            'attributes.*.id' => 'nullable|exists:product_details,id',
            'attributes.*.attribute_name' => 'required|string|max:100',
            'attributes.*.attribute_value' => 'required|string',
            'attributes.*.attribute_type' => 'required|in:text,number,boolean,date,url',
            'attributes.*.order' => 'nullable|integer|min:0',
            'deleted_attributes' => 'nullable|array',
            'deleted_attributes.*' => 'exists:product_details,id',
        ]);
        
        try {
            DB::beginTransaction();
            
            // Update the product
            $product->update([
                'name' => $validated['name'],
                'slug' => $validated['slug'],
                'description' => $validated['description'] ?? null,
                'short_description' => $validated['short_description'] ?? null,
                'price' => $validated['price'] * 100, // Store in cents
                'sale_price' => isset($validated['sale_price']) ? $validated['sale_price'] * 100 : null,
                'sku' => $validated['sku'] ?? null,
                'stock_quantity' => $validated['stock_quantity'] ?? 0,
                'manage_stock' => $validated['manage_stock'] ?? false,
                'stock_status' => $validated['stock_status'],
                'condition' => $validated['condition'],
                'category_id' => $validated['category_id'],
                'brand_id' => $validated['brand_id'] ?? null,
                'is_active' => $validated['is_active'] ?? true,
                'is_featured' => $validated['is_featured'] ?? false,
                'order' => $validated['order'] ?? 0,
                'meta_title' => $validated['meta_title'] ?? null,
                'meta_description' => $validated['meta_description'] ?? null,
            ]);
            
            // Handle image uploads
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $product->addMedia($image)
                        ->toMediaCollection('images');
                }
            }
            
            // Handle deleted images
            if (!empty($validated['deleted_media_ids'])) {
                $product->media()->whereIn('id', $validated['deleted_media_ids'])->delete();
            }
            
            // Handle dynamic attributes - delete removed ones first
            if (!empty($validated['deleted_attributes'])) {
                $product->details()->whereIn('id', $validated['deleted_attributes'])->delete();
            }
            
            // Update or create attributes
            if (!empty($validated['attributes'])) {
                foreach ($validated['attributes'] as $attribute) {
                    if (isset($attribute['id'])) {
                        // Update existing attribute
                        $product->details()->where('id', $attribute['id'])->update([
                            'attribute_name' => $attribute['attribute_name'],
                            'attribute_value' => $attribute['attribute_value'],
                            'attribute_type' => $attribute['attribute_type'],
                            'order' => $attribute['order'] ?? 0,
                        ]);
                    } else {
                        // Create new attribute
                        $product->details()->create([
                            'attribute_name' => $attribute['attribute_name'],
                            'attribute_value' => $attribute['attribute_value'],
                            'attribute_type' => $attribute['attribute_type'],
                            'order' => $attribute['order'] ?? 0,
                        ]);
                    }
                }
            }
            
            DB::commit();
            
            return redirect()->route('admin.products.show', $product)
                ->with('success', 'Product updated successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->with('error', 'Error updating product: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        try {
            DB::beginTransaction();
            
            // Delete associated media
            $product->media()->delete();
            
            // Delete associated details
            $product->details()->delete();
            
            // Delete the product
            $product->delete();
            
            DB::commit();
            
            return redirect()->route('admin.products.index')
                ->with('success', 'Product deleted successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->with('error', 'Error deleting product: ' . $e->getMessage());
        }
    }
}