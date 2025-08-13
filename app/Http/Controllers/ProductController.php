<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class ProductController extends Controller
{
    /**
     * Display the specified product.
     *
     * @param  string  $slug
     * @return \Inertia\Response
     */
    public function show($slug)
    {
        try {
            $product = Product::with(['category', 'brand', 'media', 'details'])
                ->where('slug', $slug)
                ->firstOrFail();

            // Check if product is in user's wishlist
            $product->is_in_wishlist = false;
            if (Auth::check()) {
                $product->is_in_wishlist = Auth::user()->wishlist()
                    ->where('product_id', $product->id)
                    ->exists();
            }

            // Format price for display
            $product->formatted_price = number_format($product->price / 100, 2);
            if ($product->sale_price) {
                $product->formatted_sale_price = number_format($product->sale_price / 100, 2);
            }

            // Get related products (same category, excluding current product)
            $relatedProductsQuery = Product::with(['media'])
                ->where('id', '!=', $product->id);
                
            if ($product->category_id) {
                $relatedProductsQuery->where('category_id', $product->category_id);
            }
                
            $relatedProducts = $relatedProductsQuery->inRandomOrder()
                ->limit(4)
                ->get()
                ->map(function ($item) {
                    $item->formatted_price = number_format($item->price / 100, 2);
                    if ($item->sale_price) {
                        $item->formatted_sale_price = number_format($item->sale_price / 100, 2);
                    }
                    return $item;
                });

            return Inertia::render('Products/Show', [
                'product' => $product,
                'relatedProducts' => $relatedProducts
            ]);

        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('ProductController@show error: ' . $e->getMessage());
            
            // Return a 404 response for any errors
            return response()->view('errors.404', [], 404);
        }
    }

    // Other methods can be kept as is or removed if not needed
    public function index() {}
    public function store(Request $request) {}
    public function update(Request $request, string $id) {}
    public function destroy(string $id) {}
}
