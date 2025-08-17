<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class ProductController extends Controller
{
    /**
     * @var ProductService
     */
    protected $productService;

    /**
     * Create a new controller instance.
     *
     * @param ProductService $productService
     * @return void
     */
    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * Display a listing of products with optional filters.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        $products = $this->productService->getFilteredProducts($request->all());

        return Inertia::render('Products/Index', [
            'products' => $products,
            'filters' => $request->all(['search', 'category', 'brand', 'sort', 'order'])
        ]);
    }

    /**
     * Display the specified product.
     *
     * @param  string  $slug
     * @return \Inertia\Response|\Illuminate\Http\Response
     */
    public function show($slug)
    {
        try {
            $data = $this->productService->getProductBySlug($slug);
            
            return Inertia::render('Products/Show', [
                'product' => $data['product'],
                'relatedProducts' => $data['relatedProducts']
            ]);
        } catch (\Exception $e) {
            Log::error('ProductController@show error: ' . $e->getMessage());
            return response()->view('errors.404', [], 404);
        }
    }

    /**
     * Get featured products
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function featured()
    {
        try {
            $products = $this->productService->getFeaturedProducts(8);
            return response()->json($products);
        } catch (\Exception $e) {
            Log::error('ProductController@featured error: ' . $e->getMessage());
            return response()->json(['error' => 'Unable to load featured products'], 500);
        }
    }
}
