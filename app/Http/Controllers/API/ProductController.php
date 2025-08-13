<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductFilterRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    /**
     * Display a listing of the products with filtering and sorting.
     *
     * @param ProductFilterRequest $request
     * @return JsonResponse
     */
    public function index(ProductFilterRequest $request): JsonResponse
    {
        // Cache key based on request parameters
        $cacheKey = 'products_' . md5(json_encode($request->all()));
        
        // Cache for 1 hour (60 minutes)
        $products = Cache::remember($cacheKey, 3600, function () use ($request) {
            return Product::query()
                ->with(['category', 'brand', 'media'])
                ->active()
                ->filter($request->validated())
                ->sort($request->input('sort_by'))
                ->paginate(
                    $request->input('per_page', 12),
                    ['*'],
                    'page',
                    $request->input('page', 1)
                );
        });

        return response()->json([
            'success' => true,
            'data' => ProductResource::collection($products),
            'meta' => [
                'total' => $products->total(),
                'per_page' => $products->perPage(),
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'filters' => [
                    'categories' => Category::active()->get(['id', 'name']),
                    'brands' => Brand::active()->get(['id', 'name']),
                    'conditions' => [
                        ['value' => 'new', 'label' => 'New'],
                        ['value' => 'used', 'label' => 'Used'],
                        ['value' => 'refurbished', 'label' => 'Refurbished'],
                    ],
                    'sort_options' => [
                        ['value' => 'newest', 'label' => 'Newest'],
                        ['value' => 'oldest', 'label' => 'Oldest'],
                        ['value' => 'price_asc', 'label' => 'Price: Low to High'],
                        ['value' => 'price_desc', 'label' => 'Price: High to Low'],
                    ]
                ]
            ]
        ]);
    }

    /**
     * Display the specified product.
     *
     * @param string $slug
     * @return JsonResponse
     */
    public function show(string $slug): JsonResponse
    {
        $product = Product::with(['category', 'brand', 'details', 'media'])
            ->where('slug', $slug)
            ->active()
            ->firstOrFail();

        // Get related products (same category, excluding current product)
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->active()
            ->inRandomOrder()
            ->limit(4)
            ->get();

        return response()->json([
            'success' => true,
            'data' => new ProductResource($product),
            'related_products' => ProductResource::collection($relatedProducts)
        ]);
    }
}
