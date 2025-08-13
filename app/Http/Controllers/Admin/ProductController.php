<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
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
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        $validated = $request->validated();
        
        // Normalize fields to match DB schema
        if (isset($validated['quantity'])) {
            $validated['stock_quantity'] = (int) $validated['quantity'];
            unset($validated['quantity']);
        }
        if (empty($validated['slug']) && !empty($validated['name'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }
        
        // Handle the file uploads first
        $images = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $images[] = $image->store('products', 'public');
            }
        }
        
        DB::beginTransaction();
        
        try {
            // Create the product with the validated data
            $product = Product::create($validated);
            
            // Handle image uploads if any
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $product->addMedia($image)
                        ->toMediaCollection('images');
                }
            }
            
            // Save dynamic attributes if provided (simple key/value)
            if ($request->has('attributes')) {
                $attributes = $request->input('attributes');
                if (is_string($attributes)) {
                    $attributes = json_decode($attributes, true);
                }
                if (is_array($attributes)) {
                    foreach ($attributes as $name => $value) {
                        $product->details()->create([
                            'attribute_name' => (string) $name,
                            'attribute_value' => is_array($value) ? json_encode($value) : (string) $value,
                            'attribute_type' => 'text',
                            'order' => 0,
                        ]);
                    }
                }
            }
            
            DB::commit();
            
            return redirect()->route('admin.products.index')
                ->with('success', 'Product created successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Delete any uploaded images if there was an error
            if (!empty($images)) {
                foreach ($images as $image) {
                    Storage::disk('public')->delete($image);
                }
            }
            
            return back()->with('error', 'Failed to create product: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($idOrSlug)
    {
        $product = $this->resolveProduct($idOrSlug);
        
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
    public function edit($idOrSlug)
    {
        $product = $this->resolveProduct($idOrSlug);
        $product->load(['details', 'media']);
        
        return Inertia::render('Admin/Products/Edit', [
            'product' => array_merge($product->toArray(), [
                'price' => $product->price,
                'sale_price' => $product->sale_price,
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
    public function update(UpdateProductRequest $request, $idOrSlug)
    {
        $product = $this->resolveProduct($idOrSlug);
        $validated = $request->validated();
        
        // Normalize fields to match DB schema
        if (isset($validated['quantity'])) {
            $validated['stock_quantity'] = (int) $validated['quantity'];
            unset($validated['quantity']);
        }
        if (empty($validated['slug']) && !empty($validated['name'])) {
            $validated['slug'] = $product->slug;
        }
        
        // Handle the file uploads
        $images = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $images[] = $image->store('products', 'public');
            }
        }
        
        DB::beginTransaction();
        
        try {
            // Update the product
            $product->update($validated);
            
            // Handle image uploads if any
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $product->addMedia($image)
                        ->toMediaCollection('images');
                }
            }
            
            // Save dynamic attributes if provided
            if ($request->has('attributes')) {
                $attributes = $request->input('attributes');
                if (is_array($attributes)) {
                    foreach ($attributes as $attribute) {
                        if (isset($attribute['id'])) {
                            // Update existing attribute
                            $product->details()->where('id', $attribute['id'])->update([
                                'attribute_name' => $attribute['attribute_name'] ?? '',
                                'attribute_value' => $attribute['attribute_value'] ?? '',
                                'attribute_type' => $attribute['attribute_type'] ?? 'text',
                                'order' => $attribute['order'] ?? 0,
                            ]);
                        } else {
                            // Create new attribute
                            $product->details()->create([
                                'attribute_name' => $attribute['attribute_name'] ?? '',
                                'attribute_value' => $attribute['attribute_value'] ?? '',
                                'attribute_type' => $attribute['attribute_type'] ?? 'text',
                                'order' => $attribute['order'] ?? 0,
                            ]);
                        }
                    }
                }
            }
            
            // Dynamic attributes are handled above from request->input('attributes')
            
            DB::commit();
            
            return redirect()->route('admin.products.show', $product)
                ->with('success', 'Product updated successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Delete any uploaded images if there was an error
            if (!empty($images)) {
                foreach ($images as $image) {
                    Storage::disk('public')->delete($image);
                }
            }
            
            return back()->with('error', 'Error updating product: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($idOrSlug)
    {
        $product = $this->resolveProduct($idOrSlug);
        
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
    
    /**
     * Resolve the product using either ID or slug.
     */
    protected function resolveProduct($idOrSlug)
    {
        if (is_numeric($idOrSlug)) {
            return Product::findOrFail($idOrSlug);
        }
        return Product::where('slug', $idOrSlug)->firstOrFail();
    }

    /**
     * Delete a single media item for a product
     */
    public function destroyMedia($idOrSlug, $mediaId)
    {
        $product = $this->resolveProduct($idOrSlug);
        $media = $product->media()->where('id', $mediaId)->first();
        if (!$media) {
            return back()->with('error', 'Media not found.');
        }
        $media->delete();
        return back()->with('success', 'Image deleted successfully.');
    }
}