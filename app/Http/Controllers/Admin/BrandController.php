<?php

namespace App\Http\Controllers\Admin;

 use App\Http\Controllers\Controller;
 use App\Http\Requests\Admin\StoreBrandRequest;
 use App\Http\Requests\Admin\UpdateBrandRequest;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $brands = Brand::latest()
            ->withCount('products')
            ->paginate(10);

        return Inertia::render('Admin/Brands/Index', [
            'brands' => $brands,
            'filters' => request()->all('search'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/Brands/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBrandRequest $request)
    {
        $validated = $request->validated();

        // Generate slug from name if not provided
        $validated['slug'] = Str::slug($validated['name']);

        // Handle file upload
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('brands', 'public');
            $validated['logo'] = $path;
        }

        Brand::create($validated);

        return redirect()->route('admin.brands.index')
            ->with('success', 'Brand created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Brand $brand)
    {
        $brand->loadCount('products');
        
        return Inertia::render('Admin/Brands/Show', [
            'brand' => $brand,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Brand $brand)
    {
        return Inertia::render('Admin/Brands/Edit', [
            'brand' => $brand,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBrandRequest $request, Brand $brand)
    {
        $validated = $request->validated();

        // Handle file upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($brand->logo) {
                Storage::disk('public')->delete($brand->logo);
            }
            
            $path = $request->file('logo')->store('brands', 'public');
            $validated['logo'] = $path;
        }

        $brand->update($validated);

        return redirect()->route('admin.brands.index')
            ->with('success', 'Brand updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Brand $brand)
    {
        // Prevent deletion if brand has products
        if ($brand->products()->exists()) {
            return back()->with('error', 'Cannot delete brand with associated products.');
        }

        // Delete logo if exists
        if ($brand->logo) {
            Storage::disk('public')->delete($brand->logo);
        }

        $brand->delete();

        return redirect()->route('admin.brands.index')
            ->with('success', 'Brand deleted successfully.');
    }
}