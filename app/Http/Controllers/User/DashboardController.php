<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the user's dashboard.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get wishlist items with product details
        $wishlistItems = $user->wishlist()
            ->with(['product.category', 'product.brand', 'product.media'])
            ->latest()
            ->take(6)
            ->get();
            
        // Get recently viewed products (implementation depends on how you track views)
        $recentlyViewed = $this->getRecentlyViewedProducts($user);
        
        return Inertia::render('User/Dashboard', [
            'wishlistItems' => $wishlistItems,
            'recentlyViewed' => $recentlyViewed,
            'stats' => [
                'wishlist_count' => $user->wishlist()->count(),
                'recently_viewed_count' => $recentlyViewed->count(),
            ]
        ]);
    }
    
    /**
     * Get recently viewed products for the user.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function getRecentlyViewedProducts($user)
    {
        // This is a basic implementation. You might want to use a dedicated table for tracking views.
        // For now, we'll return recently added products as a placeholder.
        return Product::with(['category', 'brand', 'media'])
            ->active()
            ->latest()
            ->take(6)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'price' => $product->price,
                    'sale_price' => $product->sale_price,
                    'category' => $product->category ? $product->category->only(['id', 'name', 'slug']) : null,
                    'brand' => $product->brand ? $product->brand->only(['id', 'name', 'slug']) : null,
                    'image' => $product->getFirstMediaUrl('products', 'thumb'),
                    'is_in_wishlist' => auth()->check() ? 
                        auth()->user()->wishlist()->where('product_id', $product->id)->exists() : false,
                ];
            });
    }
}
