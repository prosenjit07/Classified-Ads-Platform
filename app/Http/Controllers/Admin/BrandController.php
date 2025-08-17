<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBrandRequest;
use App\Http\Requests\Admin\UpdateBrandRequest;
use App\Models\Brand;
use App\Services\BrandService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BrandController extends Controller
{
    /**
     * @var BrandService
     */
    protected $brandService;

    /**
     * Create a new controller instance.
     *
     * @param BrandService $brandService
     * @return void
     */
    public function __construct(BrandService $brandService)
    {
        $this->brandService = $brandService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        $brands = $this->brandService->getBrands($request->all());

        return Inertia::render('Admin/Brands/Index', [
            'brands' => $brands,
            'filters' => $request->all('search'),
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
     *
     * @param StoreBrandRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreBrandRequest $request)
    {
        try {
            $this->brandService->createBrand(
                $request->validated(),
                $request->file('logo')
            );

            return redirect()->route('admin.brands.index')
                ->with('success', 'Brand created successfully.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error creating brand: ' . $e->getMessage());
        }
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
     *
     * @param UpdateBrandRequest $request
     * @param Brand $brand
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateBrandRequest $request, Brand $brand)
    {
        try {
            $this->brandService->updateBrand(
                $brand,
                $request->validated(),
                $request->file('logo'),
                (bool) $request->input('delete_logo')
            );

            return redirect()->route('admin.brands.index')
                ->with('success', 'Brand updated successfully.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error updating brand: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Brand $brand
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Brand $brand)
    {
        try {
            $this->brandService->deleteBrand($brand);
            
            return redirect()->route('admin.brands.index')
                ->with('success', 'Brand deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}